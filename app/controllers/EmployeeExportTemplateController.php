<?php

class EmployeeExportTemplateController extends \BaseController 
{
    protected $config = [
        'status' => null,
        'with_dependents' => false,
        'number_of_dependents' => 10
    ];

    protected $columns = [
        "fullname" => 'Full Name',
        "date-of-birth-ddmmyyyy" => 'Date of Birth (DD/MM/YYYY)',
        "work-email" => 'Work Email',
        "country-code" => 'Country Code',
        "mobile-number" => 'Mobile Number',
        "nric" => 'NRIC',
        "passport-number" => 'Passport Number',
        "medical-allocation" => 'Medical Allocation',
        "wellness-allocation" => 'Wellness Allocation',
        "employee-id" => 'Employee ID',
        "bank-name" => 'Bank Name',
        "bank-account-number" => 'Bank Account Number',
        "cap-per-visit" => 'Cap Per Visit',
        "start-date-ddmmyyyy" => 'Start Date (DD/MM/YYYY)'
    ];

    protected $dependentLists = [
        'Dependents',
        'Spouse',
        'Child',
        'Parent',
        'Sibling',
        'Family'
    ];

    protected $banks = [
        'DBS/POSB BANK',
        'OCBC',
        'UOB',
        'CITI BANK',
        'MAYBANK',
        'SCB - Standard Chartered Bank',
        'HSBC',
        'DOC - Bank Of China',
        'RHB BANK',
        'CIMB BANK Berhad',
        'FEB - Far Eastern Bank',
        'SBI BANK - State Bank of India'
    ];

    protected $relationships = [
        'Dependents',
        'Spouse',
        'Child',
        'Parent',
        'Sibling',
        'Family'
    ];

    protected $cells = [];

    public function generate()
    {
        $this->config['with_dependents'] = Input::has('with_dependents') ? true : false;
        $this->config['number_of_dependents'] = Input::get('number_of_dependents') ?? $this->config['number_of_dependents'];

        // $customer_id = PlanHelper::getCusomerIdToken();

        if (!Input::has('customer_id')  && isNullOrEmptyString(Input::get('customer_id'))) {
            return ['status' => false, 'message' => 'customer_id key is required'];
        }

        $status = CustomerHelper::getAccountSpendingStatus(Input::get('customer_id'));

        $this->config['status'] = $status;

        $excelDetails = $this->getExcelDetails();

        /**
         * @todo remove this in the future
         */
        error_reporting(E_ALL ^ E_WARNING );

        return Excel::create($excelDetails['title'], function($excel) use($excelDetails) {
            /**
             * First sheet for instructions
             */
			$excel->sheet(
                $excelDetails['instruction']['title'],
                function($sheet) use($excelDetails) {
                    $sheet->fromArray( $excelDetails['instruction']['rows'] );

                    $sheet->cell('B1', function($cell) {
                        $cell->setValue('Explanation (Fields that have (*) are mandatory to fill in)');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('A2', function($cell) {
                        $cell->setFontWeight('bold');
                    });

                    if ($this->config['status']['currency_type'] !== 'myr') {
                        $sheet->cell('A10', function($cell) {
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cell('A14', function($cell) {
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cell('A17', function($cell) {
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cell('A48', function($cell) {
                            $cell->setValue('Kindly proceed to Member Information tab to fill in member details >>');
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                        });
                        $sheet->mergeCells('A48:B48');
                    } else {
                        $sheet->cell('A12', function($cell) {
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cell('A16', function($cell) {
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cell('A19', function($cell) {
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cell('A50', function($cell) {
                            $cell->setValue('Kindly proceed to Member Information tab to fill in member details >>');
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                        });
                        $sheet->mergeCells('A50:B50');
                    }

                    if (!$this->config['with_dependents']) {
                        $sheet->cell('A17', function($cell) {
                            $cell->setValue('Kindly proceed to Member Information tab to fill in member details >>');
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                        });
                        $sheet->mergeCells('A17:B17');
                    }
                }
            );

            /**
             * Second sheet for member informations
             */
            $excel->sheet(
                $excelDetails['members']['title'],
                function($sheet) use($excelDetails) {
                    $sheet->fromArray( $excelDetails['members']['columns'] );

                    $columns = $sheet->getCellCollection();
                    $characters = [];

                    /**
                     * Set dropdowns for 100 rows
                     */
                    foreach ($columns as $key => $cell) {
                        if (strlen($cell) === 2) {
                            $characters[] ="{$cell[0]}2:{$cell[0]}100";
                        }

                        if (strlen($cell) === 3) {
                            $characters[] ="{$cell[1]}2:{$cell[0]}100";
                        }

                        if ($sheet->getCell($cell)->getValue() == "Bank Name") {
                            $bankValidation = $sheet->getCell($cell)->getDataValidation();
                            $bankValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                            $bankValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                            $bankValidation->setAllowBlank(false);
                            $bankValidation->setShowInputMessage(true);
                            $bankValidation->setShowErrorMessage(true);
                            $bankValidation->setShowDropDown(true);
                            $bankValidation->setErrorTitle('Input error');
                            $bankValidation->setError('Value is not in list.');
                            $bankValidation->setPromptTitle('Pick from list');
                            $bankValidation->setPrompt('Please pick a value from the drop-down list.');
                            $bankValidation->setFormula1(sprintf('"%s"', implode(',', $this->banks)));

                            for ($x = 2; $x <= 100; $x++) {
                                if (strlen($cell) === 2) {
                                    $sheet->getCell("{$cell[0]}{$x}")->setDataValidation(clone $bankValidation);
                                }

                                if (strlen($cell) === 3) {
                                    $sheet->getCell("{$cell[0]}{$cell[1]}{$x}")->setDataValidation(clone $bankValidation);
                                }
                            }
                        }


                        for ($i = 1; $i <= $this->config['number_of_dependents']; $i++) {
                            if (
                                $sheet->getCell($cell)->getValue() == "Dependent {$i} Relationship"
                            ) {
                                $relationshipValidation = $sheet->getCell($cell)->getDataValidation();
                                $relationshipValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
                                $relationshipValidation->setAllowBlank(false);
                                $relationshipValidation->setShowInputMessage(true);
                                $relationshipValidation->setShowErrorMessage(true);
                                $relationshipValidation->setShowDropDown(true);
                                $relationshipValidation->setErrorTitle('Input error');
                                $relationshipValidation->setError('Value is not in list.');
                                $relationshipValidation->setPromptTitle('Pick from list');
                                $relationshipValidation->setPrompt('Please pick a value from the drop-down list.');
                                $relationshipValidation->setFormula1(sprintf('"%s"', implode(',', $this->relationships)));


                                for ($x = 2; $x <= 100; $x++) {
                                    if (strlen($cell) === 2) {
                                        $sheet->getCell("{$cell[0]}{$x}")->setDataValidation(clone $relationshipValidation);
                                    }

                                    if (strlen($cell) === 3) {
                                        $sheet->getCell("{$cell[0]}{$cell[1]}{$x}")->setDataValidation(clone $relationshipValidation);
                                    }
                                }
                            }
                        }
                    }

                    /**
                     * Format all columns to string
                     */
                    foreach ($characters as $character) {
                        $sheet->setColumnFormat(array(
                            $character => '@'
                        ));
                    }
                }
            );
		})->export('xls');
    }

    private function getExcelDetails(): array
    {
        return [
            'title' => $this->config['with_dependents'] ? 'Employeed-and-Dependents' : 'Employees',
            'instruction' => [
                'title' => 'Instruction',
                'rows' => $this->getInstructionRows()
            ],
            'members' => [
                'title' => 'Member Information',
                'columns' => $this->extractColumns()
            ]
        ];
    }

    protected function extractColumns(): array
    {
        $status = $this->config['status'];

        $columns = collect($this->columns);

        /**
         * Remove NRIC and Password columns
         */
        if ($status['currency_type'] !== 'myr') {
           $columns = $columns->except(['nric','passport-number']);
        }

        // Medical reimbursement and wellness reimbursement enabled
        if (!$status['medical_reimbursement'] && !$status['wellness_reimbursement']) {
            $columns = $columns->except(['bank-name','bank-account-number']);
        }

        // Medical enabled
        if (!$status['medical_enabled']) {
            $columns = $columns->except(['medical-allocation']);
        }

        // Wellness enabled
        if (!$status['wellness_enabled']) {
            $columns = $columns->except(['wellness-allocation']);
        }

        if (
            $status['medical_enabled'] &&
            $status['wellness_enabled'] &&
            $status['medical_method'] === 'pre_paid' &&
            $status['wellness_method'] === 'pre_paid' &&
            !$status['paid_status']
        ) {
            $columns = $columns->except(['medical-allocation','wellness-allocation']);
        }

        if (
            $status['medical_enabled'] &&
            $status['wellness_enabled'] &&
            $status['medical_method'] === 'pre_paid' &&
            $status['wellness_method'] === 'post_paid' &&
            !$status['paid_status']
        )  {
            $columns = $columns->except(['medical-allocation']);
        }

        if (
            $status['medical_enabled'] &&
            $status['wellness_enabled'] &&
            $status['medical_method'] === 'post_paid' &&
            $status['wellness_method'] === 'pre_paid' &&
            !$status['paid_status']
        )  {
            $columns = $columns->except(['medical-allocation']);
        }

        if (
            $status['account_type'] === 'enterprise_plan' &&
            ($status['medical_enabled'] &&  $status['wellness_enabled']) &&
            ($status['wellness_method'] === 'pre_paid' && !$status['paid_status'])
        )  {
            $columns = $columns->except(['medical-allocation','wellness-allocation']);
        }

        if (
            $status['account_type'] === 'enterprise_plan' &&
            ($status['medical_enabled'] &&  $status['wellness_enabled']) &&
            ($status['wellness_method'] === 'pre_paid' && $status['paid_status'])
        )  {
            $columns = $columns->put(
                'wellness-allocation',
                collect($this->columns)->get('wellness-allocation')
            )->except(['medical-allocation']);
        }

        if (
            $status['account_type'] === 'enterprise_plan' &&
            ($status['medical_enabled'] &&  $status['wellness_enabled']) &&
            ($status['wellness_method'] === 'post_paid' && $status['paid_status'])
        )  {
            $columns = $columns->put(
                'wellness-allocation',
                collect($this->columns)->get('wellness-allocation')
            )->except(['medical-allocation']);
        }

        if (
            $status['account_type'] === 'enterprise_plan' &&
            ($status['medical_enabled'] &&  !$status['wellness_enabled'])
        )  {
            $columns = $columns->except(['medical-allocation','wellness-allocation']);
        }

        if (!$status['paid_status']) {
            $columns = $columns->except(['medical-allocation','wellness-allocation']);
        }

        $columns = array_values($columns->toArray());
        $dependents = $this->generateDependentsColumns();

        return $this->config['with_dependents'] ?
            array_merge($columns, array_flatten($dependents['dependents'])) :
            $columns;
    }

    protected function getInstructionRows(): array
    {
        $dependents = $this->generateDependentsColumns();

        $default = collect([
            'row1' => [
                'Employee Details',
                ''
            ],
            'row2' => [
                'Full Name*',
                "Full name of employee as per employee NRIC"
            ],
            'row3' =>[
                'Date of Birth (DD/MM/YYYY)*',
                "Date of birth of employee in exactly (DD/MM/YYYY) format"
            ],
            'row4' => [
                'Work Email',
                "Email address of employee"
            ],
            'row5' => [
                'Mobile Country Code*',
                'Only the country code e.g. 60 (only required if mobile number is registered.)'
            ],
            'row6' => [
                'Mobile Number*',
                "Please key in employee's 9-10 digit mobile number with no prefix '0' and no country code (either Mobile Number, NRIC or Passport Number is mandatory)"
            ],
            'row7' => [
                'NRIC',
                "Employee's 12 digit identity number issued by the government without the dash (either Mobile Number, NRIC or Passport Number is mandatory.)"
            ],
            'row8' => [
                'Passport number',
                "Employee's passport number including the letter (either Mobile Number, NRIC or Passport Number is mandatory.)"
            ],
            'row9' => [
                'Start Date (DD/MM/YYYY)*',
                'Date on which employee benefits starts  in exactly (DD/MM/YYYY) format, e.g. date when employees can start using Mednefits app'
            ],
            'row10' => [
                'Employee ID',
                'Employee unique identification code set by your company, please leave it blank if there is no Employee ID'
            ],
            'row11' => [
                'Allocation Budget (if applicable)',
                ''
            ],
            'row12' => [
                'Medical Entitlement*',
                'Amount that employee is allowed to utilise in the Medical e-wallet'
            ],
            'row13' => [
                'Wellness Entitlement*',
                'Amount that employee is allowed to utilise in the Wellness e-wallet'
            ],
            'row14' => [
                'Cap Per Visit',
                'Maximum amount covered by company per visit e.g. if bill is $50 and cap per visit is $30, employee pays $20 on their own, please key in "0" or leave it blank if there is no cap per visit'
            ],
            'row15' => [
                'Reimbursement (if applicable)',
                ''
            ],
            'row16' => [
                'Bank Name*',
                "Name of Bank where employee's bank account is managed (e.g. POSB, Maybank)"
            ],
            'row17' => [
                'Bank Account Number*',
                'Full bank account number of employee without the dash "-"'
            ],
            'row18' => [
                'Dependent Details (if applicable)',
                ''
            ],
        ]);

        if ($this->config['status']['currency_type'] !== 'myr') {
            $default = $default->except(['row7','row8']);
        }

        return array_merge(
            $default->values()->toArray(),
            $this->config['with_dependents'] ? $dependents['instructions'] : []
        );
    }

    /**
     * Generate dependents columns
     * based on the number dependents set
     *
     */
    protected function generateDependentsColumns(): array
    {
        $dependents = [];
        $instructions = [];

        $columns = [];

        for ($x = 1; $x <= $this->config['number_of_dependents']; $x++) {
            $dependents[] = [
                "Dependent {$x} Full Name",
                "Dependent {$x} Date of Birth",
                "Dependent {$x} Relationship"
            ];
            $instructions[] = [
                "Dependent {$x} Full Name",
                "Full name of dependent {$x}"
            ];

            $instructions[] = [
                "Dependent {$x} Date of Birth",
                "Date of birth of dependent {$x} in exactly (DD/MM/YYYY) format"
            ];

            $instructions[] = [
                "Dependent {$x} Relationship",
                "Relationship of dependent {$x} with employee e.g. Dependents/Spouse/Child/Parent/Sibling/Family"
            ];
        }

        $columns['dependents'] = $dependents;
        $columns['instructions'] = $instructions;

        return $columns;
    }
}