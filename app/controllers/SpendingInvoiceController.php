<?php

use Illuminate\Support\Facades\Input;
class SpendingInvoiceController extends \BaseController {

	public function checkSession( )
    {
        $result = StringHelper::getJwtHrSession();
        if(!$result) {
            return array(
                'status'    => FALSE,
                'message'   => 'Need to authenticate user.'
            );
        }
        return $result;
    }

    public function checkToken($token)
	{
		$result = StringHelper::getJwtHrToken($token);
		if(!$result) {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Need to authenticate user.'
			);
		}
		return $result;
	}

	public function createHrStatement( )
	{
		$input = Input::all();
		$result = self::checkSession();

        // check if company exist
        $check = DB::table('customer_buy_start')->where('customer_buy_start_id', $result->customer_buy_start_id)->count();

        if($check == 0) {
            return array('status' => FALSE, 'message' => 'HR account does not exist.');
        }
        
		$lite_plan = false;
		$start = date('Y-m-01', strtotime($input['start']));
        $end = SpendingInvoiceLibrary::getEndDate($input['end']);

        $e_claim = [];
        $transaction_details = [];
        $statement_in_network_amount = 0;
        $statement_e_claim_amount = 0;


        $plan = DB::table('customer_plan')->where('customer_buy_start_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
        $check_company_transactions = SpendingInvoiceLibrary::checkCompanyTransactions($result->customer_buy_start_id, $start, $end, "post_paid");

        if($plan->account_type == "enterprise_plan")  {
            return array('status' => FALSE, 'message' => 'Enterprise account does not require spending transaction invoice.');
        }

        $check_company_transactions = SpendingInvoiceLibrary::checkCompanyTransactions($result->customer_buy_start_id, $start, $end, "post_paid");
        if(!$check_company_transactions) {
            return array('status' => FALSE, 'message' => 'No Transactions for this Month.');
        }

        $statement_check = DB::table('company_credits_statement')
                            ->where('statement_customer_id', $result->customer_buy_start_id)
                            ->where('statement_start_date', $start)
                            ->count();
        if($statement_check == 0) {
            $statement = SpendingInvoiceLibrary::createStatement($result->customer_buy_start_id, $start, $end, "post_paid");
            if($statement) {
                $statement_id = $statement->id;
            } else {
                return array('status' => FALSE, 'message' => 'Failed to create statement record.');
            }
        } else {
            $statement = DB::table('company_credits_statement')
                            ->where('statement_customer_id', $result->customer_buy_start_id)
                            ->where('statement_start_date', $start)
                            ->where('plan_method', 'post_paid')
                            ->first();
            if($statement) {
                // get transaction if there is another transaction
                SpendingInvoiceLibrary::checkSpendingInvoiceNewTransactions($result->customer_buy_start_id, $start, $end, $statement->statement_id, "post_paid");
                $statement_id = $statement->statement_id;
            } else {
                $statement = SpendingInvoiceLibrary::createStatement($result->customer_buy_start_id, $start, $end, "post_paid");
                if($statement) {
                    $statement_id = $statement->id;
                } else {
                    return array('status' => FALSE, 'message' => 'Failed to create statement record.');
                }
            }
        }

        $statement = SpendingInvoiceLibrary::getInvoiceSpending($statement_id, true);
        $e_claims = SpendingInvoiceLibrary::getEclaims($result->customer_buy_start_id, $start, $end);
       	$statement['statement_e_claim_amount'] = $e_claims['total_e_claim_spent'];
       	$statement['statement_in_network_amount'] = $statement['total_in_network_amount'];
        $today = date("Y-m-d");
        $show_status = false;

        if($today >= date('Y-m-d', strtotime($statement['statement_date']))) {
            $show_status = true;
        } else {
            $statement["statement_total_amount"] = "0.00";
        }


        // return $statement;
        $sub_total = floatval($statement['total_in_network_amount']) + floatval($statement['total_consultation']);
        $statement['statement_in_network_amount'] = number_format($statement['statement_in_network_amount'], 2);
        $temp = array(
            'statement'     			=> $statement,
            'in_network_transactions'    => $statement['in_network'],
            'e_claim_transactions'       => $e_claims['e_claim_transactions'],
            'total_transaction_spent'   => number_format($statement['total_in_network_amount'], 2),
            'total_e_claim_spent'       => round($e_claims['total_e_claim_spent'], 2),
            'total_consultation'        => number_format($statement['total_consultation'], 2),
            'consultation'              => 0,
            'lite_plan'                 => $statement['lite_plan'],
            'sub_total'                 => number_format($sub_total, 2),
            'show_status'               => $show_status
        );
        return array('status' => TRUE, 'data' => $temp);
	}

	public function downloadSpendingInvoice( )
	{
		$input = Input::all();
      	$result = self::checkToken($input['token']);

		if(!$result) {
			return array('status' => FALSE, 'message' => 'Invalid Token.');
		}

		$statement = SpendingInvoiceLibrary::getInvoiceSpending($input['id'], false);
        $statement['total_due'] = $statement['statement_amount_due'];
        $company = DB::table('customer_business_information')
        			->where('customer_buy_start_id', $result->customer_buy_start_id)
        			->first();
       	$statement['statement_in_network_amount'] = $statement['total_in_network_amount'];
        $statement['sub_total'] = number_format(floatval($statement['total_in_network_amount']) + floatval($statement['total_consultation']), 2);
        $statement['statement_in_network_amount'] = number_format($statement['statement_in_network_amount'], 2);
        // return View::make('invoice.hr-statement-invoice', $statement);
		$pdf = PDF::loadView('invoice.hr-statement-invoice', $statement);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');

    	return $pdf->stream($statement['company'].' - '.$statement['statement_number'].'.pdf');
	}

	public function downloadCSV($data)
	{
		$lite_plan = $data['lite_plan'];
		// $lite_plan;
		$container = array();
		foreach ($data['in_network'] as $key => $trans) {
			$temp = array(
				'TRANSACTION #'	=> $trans['transaction_id'],
                'EMPLOYEE'  => $trans['employee'],
				'DEPENDENT' 	=> $trans['dependent'],
				'DATE'		=> $trans['date_of_transaction'],
				'ITEMS/SERVICE' => $trans['service'],
				'PROVIDER'	=> $trans['clinic_name'],
				'TOTAL AMOUNT'	=> $trans['total_amount']
			);

			if($lite_plan) {
				$temp['MEDICINE & TREATMENT'] = $trans['treatment'];
				$temp['CONSULTATION'] = $trans['consultation'];
			}

			$temp['PAYMENT TYPE'] = $trans['payment_type'];

			$container[] = $temp;
		}

		$excel = \Excel::create('In-Network Transactions', function($excel) use($container) {

        $excel->sheet('In-Network', function($sheet) use($container) {
            $sheet->fromArray( $container );
        });

        })->export('csv');
	}

	public function downloadSpendingInNetwork( )
	{
		$input = Input::all();
		$result = self::checkToken($input['token']);
	    if(!$result) {
	    	return array('status' => FALSE, 'message' => 'Invalid Token.');
	    }

	    $statement = SpendingInvoiceLibrary::getInvoiceSpending($input['id'], true);
		$statement['total_due'] = $statement['statement_amount_due'];
        // return $statement;
        $company = DB::table('customer_business_information')
        			->where('customer_buy_start_id', $result->customer_buy_start_id)
        			->first();
       	$statement['statement_in_network_amount'] = $statement['total_in_network_amount'];
        $statement['sub_total'] = floatval($statement['total_in_network_amount']) + floatval($statement['total_consultation']);

        if($input['type'] == "csv") {
			return self::downloadCSV($statement);
		} else {
			// return View::make('pdf-download.company-transaction-list-invoice', $statement);
		    $pdf = PDF::loadView('pdf-download.company-transaction-list-invoice', $statement);
				$pdf->getDomPDF()->get_option('enable_html5_parser');
		    $pdf->setPaper('A4', 'landscape');

		    return $pdf->stream();
		}
	}

	public function generateMonthlyCompanyInvoice( )
	{
		set_time_limit(1000);
		$companies = DB::table('corporate')
                    ->join('customer_link_customer_buy', 'customer_link_customer_buy.corporate_id', '=', 'corporate.corporate_id')
                    // ->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_link_customer_buy.customer_buy_start_id')
                    // ->where('customer_link_customer_buy.customer_buy_start_id', 126)
                    ->get();
        // return $companies;
        $start = date('Y-m-01', strtotime('-1 month'));
        $temp_end = date('Y-m-t', strtotime('-1 month'));
        $end = SpendingInvoiceLibrary::getEndDate($temp_end);

        $total_success_generate = 0;
        $total_fail_generate = 0;
        $result = [];
        $total_credits_generate = 0;
        $total_consultation_generate = 0;
        $format = [];

        foreach ($companies as $key => $company) {
        	$lite_plan = false;
            // check if company exist
            $check = DB::table('customer_buy_start')->where('customer_buy_start_id', $company->customer_buy_start_id)->first();

             if($check) {

             	$credit_check = SpendingInvoiceLibrary::checkTotalCreditsInNetworkTransactions($company->customer_buy_start_id, $start, $end);
                // return $credit_check;
             	if($credit_check['total_credits'] > 0 || $credit_check['total_consultation'] > 0) {

             		$total_credits_generate += $credit_check['total_credits'];
                    $total_consultation_generate += $credit_check['total_consultation'];

			        $statement_check = DB::table('company_credits_statement')
			                            ->where('statement_customer_id', $company->customer_buy_start_id)
			                            ->where('statement_start_date', $start)
			                            ->count();

			        if($statement_check == 0) {
			        	$total_success_generate++;
                        $plan = DB::table('customer_plan')->where('customer_buy_start_id', $company->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
			            $statement = SpendingInvoiceLibrary::createStatement($company->customer_buy_start_id, $start, $end, $plan);
			            if($statement) {
			                $statement_id = $statement->id;
			            } else {
			                $statement_id = false;
			            }
			        } else {
			            $statement = DB::table('company_credits_statement')
			                            ->where('statement_customer_id', $company->customer_buy_start_id)
			                            ->where('statement_start_date', $start)
			                            ->first();
			            // get transaction if there is another transaction
			            $check_invoice_transactions = SpendingInvoiceLibrary::checkSpendingInvoiceNewTransactions($company->customer_buy_start_id, $start, $end, $statement->statement_id);
			            $statement_id = $statement->statement_id;
			        }

			        if($statement_id) {
				        $statement = SpendingInvoiceLibrary::getInvoiceSpending($statement_id, true);
                        if($statement && $statement['statement_contact_email']) {
                            $company_details = DB::table('corporate')
                                    ->join('customer_link_customer_buy', 'customer_link_customer_buy.corporate_id', '=', 'corporate.corporate_id')
                                    ->where('customer_link_customer_buy.customer_buy_start_id', $company->customer_buy_start_id)
                                    ->first();

    				        $company = DB::table('customer_business_information')
    				        			->where('customer_buy_start_id', $company->customer_buy_start_id)
    				        			->first();

    				        // send to company
                            $new_statement = array(
                                "statement_contact_email"   => $statement['statement_contact_email'],
                                "statement_contact_name"    => ucwords($statement['statement_contact_name']),
                                "statement_contact_number"  => $statement['statement_contact_number'],
                                "statement_customer_id"     => $statement['customer_id'],
                                "statement_date"            => date('d F Y', strtotime($statement['statement_date'])),
                                'statement_due'             => date('d F Y', strtotime($statement['statement_due'])),
                                "statement_end_date"        => date('F d Y', strtotime($statement['statement_end_date'])),
                                "statement_id"              => $statement['statement_id'],
                                "statement_number"              => $statement['statement_number'],
                                "statement_start_date"          => date('F d', strtotime($statement['statement_start_date'])),
                                "statement_status"              => $statement['statement_status'],
                                "statement_in_network_amount"   => number_format($credit_check['total_credits'], 2),
                                "statement_total_amount"        => number_format($credit_check['total_credits'] , 2),
                                'total_consultation'            => number_format($credit_check['total_consultation'] , 2),
                                'sub_total'                     => number_format($credit_check['total_credits'] + $credit_check['total_consultation'] , 2),
                                'total_due'                     => number_format($credit_check['total_credits'] + $credit_check['total_consultation'] , 2),
                                'company'                       => ucwords($company_details->company_name),
                                'company_address'           	=> ucwords($company->company_address),
                                'emailSubject'                  => 'Company Monthly Spending Invoice',
                                'emailTo'                       => $statement['statement_contact_email'],
                                // 'emailTo'                       => 'allan.alzula.work@gmail.com',
                                'emailPage'                     => 'email-templates.company-monthly-invoice',
                                'emailName'                      => ucwords($company_details->company_name),
                                'lite_plan'                     => $statement['lite_plan'],
                                'payment_remarks'               => $statement['payment_remarks'],
                                'in_network'                    => $statement['in_network']
                            );

                             if((int)$check->spending_notification == 1) {
                                $ccs = [];
                                $ccs[] = 'info@medicloud.sg';
                                // send to email with attachment
                                $business_contact = DB::table('customer_business_contact')
                                    ->where('customer_buy_start_id', $statement['customer_id'])
                                    ->first();
                                if($business_contact && $business_contact->work_email && (int)$business_contact->send_email_billing == 1) {
                                    $ccs[] = $business_contact->work_email ? $business_contact->work_email : 'developer.mednefits@gmail.com';
                                }

                                // get company contacts
                                $company_contacts = DB::table('company_contacts')
                                                        ->where('customer_id', $statement['customer_id'])
                                                        ->where('active', 1)
                                                        ->where('send_email_billing', 1)
                                                        ->get();

                                foreach ($company_contacts as $key => $contact) {
                                    if($contact && $contact->email && (int)$contact->send_email_billing == 1) {
                                        $ccs[] = $contact->email ? $contact->email : 'developer.mednefits@gmail.com';
                                    }
                                }

                                $new_statement['ccs'] = $ccs;
                                EmailHelper::sendNewEmailCompanyInvoiceWithAttachment($new_statement);
                            }
                            try {
                                $admin_logs = array(
                                    'admin_id'  => null,
                                    'type'      => 'spending_account_generate_invoice_system_generate',
                                    'data'      => SystemLogLibrary::serializeData($new_statement)
                                );
                                SystemLogLibrary::createAdminLog($admin_logs);
                            } catch(Exception $e) {

                            }
    				        array_push($format, $statement);
                        }
			        }
             	} else {
             		$total_fail_generate = 0;
             	}
            }
        }

        $currenttime = StringHelper::CurrentTime();
        $emailDdata['emailName']= 'Mednefits Company Automatic SPending Invoice Generate';
        $emailDdata['emailPage']= 'email-templates.invoice-generate';
        $emailDdata['emailTo']= 'developer.mednefits@gmail.com';
        $emailDdata['emailSubject'] = "Company Benefits Spending Invoice";
        $emailDdata['actionDate'] = date('d-m-Y');
        $emailDdata['actionTime'] = $currenttime;
        $emailDdata['total_success'] = $total_success_generate;
        $emailDdata['total_fail'] = $total_fail_generate;
        $emailDdata['totalRecords'] = count($companies);
        $emailDdata['results'] = count($companies);
        $emailDdata['total_credits'] = number_format($total_credits_generate, 2);
        EmailHelper::sendEmail($emailDdata);

        return array('total_success_generate' => $total_success_generate, 'total_fail_generate' => $total_fail_generate, 'data' => $format);
	}

    public function getHrBenfitSpendingInvoice( )
    {
        $result = self::checkSession();
        $customer_id = $result->customer_buy_start_id;
        $paginate = [];
        $credits_statements_data = DB::table('company_credits_statement')
                                ->where('statement_customer_id', $customer_id)
                                ->get();

        $credits_statements = DB::table('company_credits_statement')
                                ->where('statement_customer_id', $customer_id)
                                ->paginate(10);
        $paginate['current_page'] = $credits_statements->getCurrentPage();
        $paginate['from'] = $credits_statements->getFrom();
        $paginate['last_page'] = $credits_statements->getLastPage();
        $paginate['per_page'] = $credits_statements->getPerPage();
        $format = [];
        $minus = 0;

        foreach ($credits_statements_data as $key => $data) {
            if(date('Y-m-d') <= date('Y-m-d', strtotime($data->statement_date))) {
                $minus++;
            }
        }

        foreach ($credits_statements as $key => $data) {
            if(date('Y-m-d') >= date('Y-m-d', strtotime($data->statement_date))) {
                $statement = SpendingInvoiceLibrary::getInvoiceSpending($data->statement_id, true);
                $statement['total_due'] = $statement['statement_amount_due'];
                
                if($statement['statement_total_amount'] > 0) {
                    $temp = array(
                        'transaction'       => 'Invoice - '.$data->statement_number,
                        'date_issue'        => date('d/m/Y', strtotime($data->statement_date)),
                        'type'              => 'Invoice',
                        'amount'            => $statement['statement_total_amount'],
                        'status'            => (int)$data->statement_status,
                        'statement_id'      => $data->statement_id,
                        'currency_type'     => $statement['currency_type']
                    );
    
                    array_push($format, $temp);
                } else {
                    $minus++;
                }
            }
        }

        $paginate['to'] = sizeof($format);
        $paginate['total'] = $credits_statements->getTotal() - $minus;
        $paginate['data'] = $format;

        return $paginate;
    }

    public function downloadStatementEclaim( )
    {
        $input = Input::all();
        $result = self::checkToken($input['token']);

        $statement_id = $input['id'];
        $format = [];
        $statement = DB::table('company_credits_statement')
                      ->where('statement_id', $statement_id)
                      ->first();
        $start = date('Y-m-01', strtotime($statement->statement_start_date));
        $end = SpendingInvoiceLibrary::getEndDate($statement->statement_end_date);
        $e_claims = SpendingInvoiceLibrary::getEclaims($result->customer_buy_start_id, $start, $end);
        $format['statement'] = date('d F', strtotime($statement->statement_start_date)).' - '.date('d F Y', strtotime($statement->statement_end_date));
        $format['transaction_details'] = $e_claims['e_claim_transactions'];

        // return View::make('pdf-download.hr-statement-full-eclaim', $format);
        $pdf = PDF::loadView('pdf-download.hr-statement-full-eclaim', $format);
            $pdf->getDomPDF()->get_option('enable_html5_parser');
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream();
    }

}
