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

        if($plan->account_type == "enterprise_plan")  {
            return array('status' => FALSE, 'message' => 'Enterprise account does not require spending transaction invoice.');
        }

        $check_company_transactions = SpendingInvoiceLibrary::checkCompanyTransactions($result->customer_buy_start_id, $start, $end, 'post_paid');
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
            'lite_plan'                 => $statement['lite_plan'],
            'sub_total'                 => number_format($sub_total, 2),
            'show_status'               => $show_status
        );
        return array('status' => TRUE, 'data' => $temp);
	}

	public function downloadSpendingInvoiceOld( )
	{
		$input = Input::all();

		if(empty($input['token']) || $input['token'] == null) {
			return ['status' => false, 'message' => 'token is required'];
		}
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
		// return View::make('pdf-download.globalTemplates.plan_invoice', $statement);
		$pdf = PDF::loadView('pdf-download.globalTemplates.plan_invoice', $statement);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');

    	return $pdf->stream($statement['company'].' - '.$statement['statement_number'].'.pdf');
	}

	public function downloadSpendingInvoice( )
	{
		$input = Input::all();
		
		if(empty($input['token']) || $input['token'] == null) {
			return ['status' => false, 'message' => 'token is required'];
		}

      	$result = self::checkToken($input['token']);

		if(!$result) {
			return array('status' => FALSE, 'message' => 'Invalid Token.');
		}

		$statement_id = $input['id'];
		$data = CompanyCreditsStatement::where('statement_id', $statement_id)
		->first();
		$lite_plan = false;
		$results = \SpendingInvoiceLibrary::getTotalCreditsInNetworkTransactions($data->statement_id, $data->statement_customer_id, true);

		if($results['credits'] > 0 || $results['total_consultation'] > 0 || $results['total_post_paid_spent'] > 0 || $results['total_post_paid_spent'] > 0) {
			$consultation_amount_due = 0;
			$company_details = DB::table('customer_business_information')->where('customer_buy_start_id', $data->statement_customer_id)->first();
			if((int)$data->lite_plan == 1) {
				$lite_plan = true;
			}
			$billingContact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $data->statement_customer_id)->first();
			$total = round($results['total_post_paid_spent'], 2);
			$amount_due = (float)$total - (float)$data->paid_amount;

			$temp = array(
				'company' => ucwords($data->statement_company_name),
				'company_address' => ucwords($data->statement_company_address),
				'postal'		=> $data->postal ? $data->postal : $company_details->postal_code,
				'building_name'		=> $company_details->building_name,
				'unit_number'		=> $company_details->unit_number,
				'contact_email' => $data->statement_contact_email,
				'contact_name' => ucwords($data->statement_contact_name),
				'contact_contact_number' => $data->statement_contact_number,
				'customer_id' => $data->statement_customer_id,
				'statement_date' => date('j M Y', strtotime($data->statement_date)),
				'statement_due' => date('j M Y', strtotime($data->statement_due)),
				'statement_start_date' => $data->statement_start_date,
				'statement_end_date'	=> $data->statement_end_date,
				'start_date' => date('j M', strtotime($data->statement_start_date)),
				'end_date'	=> date('j M Y', strtotime($data->statement_end_date)),
				'period'			=> date('d F', strtotime($data->statement_start_date)).' - '.date('d F Y', strtotime($data->statement_end_date)),
				'period_start'		=> date('j M', strtotime($data->statement_start_date)),
				'period_end'		=> date('j M Y', strtotime($data->statement_end_date)),
				'statement_id'	=> $data->statement_id,
				'statement_number' => $data->statement_number,
				'statement_status'	=> $amount_due > 0 ? false : true,
				'statement_total_amount' => number_format($results['credits'] + $results['total_consultation'], 2),
				'total_in_network_amount'		=> number_format($results['credits'], 2),
				'statement_amount_due' => number_format($amount_due, 2),
				'consultation_amount_due'	=> number_format($consultation_amount_due, 2),
				'in_network'				=> $results['transactions'],
				'paid_date'				=> $data->paid_date ? date('j M Y', strtotime($data->paid_date)) : NULL,
				'payment_remarks' => $data->payment_remarks,
				'payment_amount' => number_format($data->paid_amount, 2),
				'lite_plan'	=> $lite_plan,
				'total_consultation'	=> number_format($results['total_consultation'], 2),
				'total_gp_medicine'		=> number_format($results['total_gp_medicine'], 2),
				'total_gp_consultation'	=> number_format($results['total_gp_consultation'], 2),
				'total_dental'			=> number_format($results['total_dental'], 2),
				'total_tcm'				=> number_format($results['total_tcm'], 2),
				'total_transactions'	=> number_format($results['total_transactions'], 2),
				'total_spent'			=> number_format($results['total_post_paid_spent'] + $results['total_pre_paid_spent'], 2),
				'currency_type'	=> strtoupper($data->currency_type),
				'total_pre_paid_spent'	=> number_format($results['total_pre_paid_spent'], 2),
				'total_post_paid_spent'	=> number_format($results['total_post_paid_spent'], 2)
			);

    		return View::make('pdf-download.globalTemplates.panel-invoice', $temp);
			$pdf = \PDF::loadView('pdf-download.globalTemplates.panel-invoice', $temp);
			$pdf->getDomPDF()->get_option('enable_html5_parser');
			$pdf->setPaper('A4', 'portrait');
			return $pdf->stream();
		}
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

		if(empty($input['token']) || $input['token'] == null) {
			return ['status' => false, 'message' => 'token is required'];
		}

		$result = self::checkToken($input['token']);
	    if(!$result) {
	    	return array('status' => FALSE, 'message' => 'Invalid Token.');
	    }

	    $statement = SpendingInvoiceLibrary::getInvoiceSpending($input['id'], true);
		$statement['total_due'] = $statement['statement_amount_due'];
        $company = DB::table('customer_business_information')
        			->where('customer_buy_start_id', $result->customer_buy_start_id)
        			->first();
       	$statement['statement_in_network_amount'] = $statement['total_in_network_amount'];
        $statement['sub_total'] = floatval($statement['total_in_network_amount']) + floatval($statement['total_consultation']);

        if(!empty($input['type']) && $input['type'] == "csv") {
			return self::downloadCSV($statement);
		} else {
			// return View::make('pdf-download.globalTemplates.transaction-history-statement', $statement);
		    $pdf = PDF::loadView('pdf-download.globalTemplates.transaction-history-statement', $statement);
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
            
                $temp = array(
                    'transaction'       => 'Invoice - '.$data->statement_number,
                    'date_issue'        => date('d/m/Y', strtotime($data->statement_date)),
                    'type'              => 'Invoice',
                    'amount'            => 'S$'.$statement['statement_total_amount'],
                    'status'            => (int)$data->statement_status,
                    'statement_id'      => $data->statement_id,
                    'currency_type'     => $statement['currency_type']
                );

                array_push($format, $temp);
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
    
    public function getCompanyInvoiceHistory( )
	{
		$input = Input::all();

		if(!empty($input['token']) && $input['token'] != null) {
			$session = self::checkToken($input['token']);
		} else {
			$session = self::checkSession();
		}

		// if(!empty($session['status']) && $session['status'] == false) {
		// 	return $session;
		// }

		$customer_id = $session->customer_buy_start_id;
		
		if(empty($input['type']) || $input['type'] == null) {
			return array('status' => false, 'message' => 'type is required.');
		}

		if(!in_array($input['type'], ['spending', 'plan', 'deposit', 'plan_withdrawal', 'spending_purchase'])) {
			return ['status' => false, 'message' => 'type should only be spending, spending_purchase, plan, deposit and plan_withdrawal'];
		}

		$limit = !empty($input['limit']) ? $input['limit'] : 10;
		$download = !empty($input['download']) && $input['download'] === "true" || !empty($input['download']) && $input['download'] === true ? true : false;
		$today = date('Y-m-d');
		$type = '';
		if($input['type'] == 'spending') {
			$pagination = [];
			$all_data = DB::table('company_credits_statement')->where('statement_customer_id', $customer_id)->where('statement_date', '<=', $today)->get();
			$credits_statements = DB::table('company_credits_statement')->where('statement_customer_id', $customer_id)->where('statement_date', '<=', $today)->orderBy('statement_date', 'desc')->paginate($limit);
			// get spending settings status
			$spending = \CustomerHelper::getAccountSpendingStatus($customer_id);
			$spendingPurchasePayment = true;
			if($spending['spending_purchase']) {
				if((int)$spending['spending_purchase']->payment_status == 0) {
					$spendingPurchasePayment = false;
				}
			}
			
			$pagination['current_page'] = $credits_statements->getCurrentPage();
			$pagination['last_page'] = $credits_statements->getLastPage();
			$pagination['total'] = $credits_statements->getTotal();
			$pagination['per_page'] = $credits_statements->getPerPage();
			$pagination['count'] = $credits_statements->count();

			$format = [];

			$total_due = 0;


			foreach ($all_data as $key => $data) {
				$lite_plan = false;
				if($data->type == "panel") {
					$results = \SpendingInvoiceLibrary::getTotalCreditsInNetworkTransactions($data->statement_id, $data->statement_customer_id, true);
				} else {
					$results = \SpendingInvoiceLibrary::getNonPanelTransactionDetails($data->statement_id, $data->statement_customer_id, true);
				}
				if($results['credits'] > 0 || $results['total_consultation'] > 0 || $results['total_post_paid_spent'] > 0 || $results['total_pre_paid_spent'] > 0) {
					$consultation_amount_due = 0;
					// $company_details = DB::table('customer_business_information')->where('customer_buy_start_id', $data->statement_customer_id)->first();
					if((int)$data->lite_plan == 1 || $results['lite_plan'] == true) {
						$lite_plan = true;
					}

					$total = !$spendingPurchasePayment && $data->plan_method == "pre_paid" ? round($results['total_pre_paid_spent'], 2) : round($results['total_post_paid_spent'], 2);
					$amount_due = (float)$total - (float)$data->paid_amount;

					// if($results['with_post_paid'] == true) {
						$amount_due = $amount_due < 0 ? 0 : $amount_due;
					// } else {
					// 	$amount_due = 0;
					// }

					$total_due += $amount_due;
				}
			}	

			foreach ($credits_statements as $key => $data) {
				$lite_plan = false;
				if($data->type == "panel") {
					$results = \SpendingInvoiceLibrary::getTotalCreditsInNetworkTransactions($data->statement_id, $data->statement_customer_id, false);
				} else {
					$results = \SpendingInvoiceLibrary::getNonPanelTransactionDetails($data->statement_id, $data->statement_customer_id, true);
				}
				
				// if($results['credits'] > 0 || $results['total_consultation'] > 0) {
					$consultation_amount_due = 0;
					// $company_details = DB::table('customer_business_information')->where('customer_buy_start_id', $data->statement_customer_id)->first();
					if((int)$data->lite_plan == 1 || $results['lite_plan'] == true) {
						$lite_plan = true;
					}

					// if($lite_plan == true && $data->type == "panel") {
					// 	$consultation_amount_due_temp = DB::table('transaction_history')
					// 	->join('spending_invoice_transactions', 'spending_invoice_transactions.transaction_id', '=', 'transaction_history.transaction_id')
					// 	->where('spending_invoice_transactions.invoice_id', $data->statement_id)
					// 	->where('transaction_history.deleted', 0)
					// 	->where('transaction_history.paid', 1)
					// 	->where('transaction_history.lite_plan_enabled', 1)
					// 	->sum('transaction_history.co_paid_amount');
					// 	$consultation_amount_due = $results['total_consultation'] - $consultation_amount_due_temp;
					// }

					// if((int)$data->statement_status == 1) {
						$total = !$spendingPurchasePayment && $data->plan_method == "pre_paid" ? round($results['total_pre_paid_spent'], 2) : round($results['total_post_paid_spent'], 2);
						$amount_due = (float)$total - (float)$data->paid_amount;
					// } else {
					// 	$amount_due = (float)$results['credits'] + (float)$results['total_consultation'];
					// 	$total = $amount_due;
					// }

					$amount_due = $amount_due < 0 ? 0 : $amount_due;

					if($amount_due <= 0) {
						$data->statement_status = 1;
					} else {
						$data->statement_status = 0;
						$data->paid_date = null;
					}

					$data->paid_amount = $results['total_pre_paid_spent'] + $data->paid_amount;

					$temp = array(
						'id'					=> $data->statement_id,
						'invoice_date'		 	=> date('j M Y', strtotime($data->statement_date)),
						'payment_due' 			=> date('j M Y', strtotime($data->statement_due)),
						'number' 				=> $data->statement_number,
						'status'				=> $data->statement_status,
						'amount_due' 			=> \DecimalHelper::formatDecimal($amount_due),
						'in_network'			=> $results['transactions'],//payment_method in this key
						'paid_date'				=> $data->paid_date ? date('j M Y', strtotime($data->paid_date)) : NULL,
						'payment_amount' 		=> \DecimalHelper::formatDecimal($data->paid_amount),
						'currency_type' 		=> $data->currency_type,
						'company_name' 			=> $data->statement_company_name,
						'type'					=> $data->type,
						'with_post_paid'		=> $results['with_post_paid'],
						'payment_method'		=> !$spendingPurchasePayment && $data->plan_method == "pre_paid" ? 'bank_transfer' : $data->payment_method,
						'payment_remarks'		=> $data->payment_remarks,
						'total_pre_paid_spent'	=> $results['total_pre_paid_spent'],
						'total_post_paid_spent'	=> $results['total_post_paid_spent'],
						'category_type'			=> $input['type']
					);

					array_push($format, $temp);
				// }
			}

			if($download == true) {
				$date = date('d-m-Y h:i:s');
				$title = "Company History Invoice type - Spending-".$date;

				//need to understand the fix format.
				$filterSheet = array_map(function($tmp) { 
					unset($tmp['in_network']); 
					return $tmp; 
				}, $format);

				$container = array();

				foreach($filterSheet as $key => $data) {
					$payment_method = null;
					if($data['payment_method'] == "mednefits_credits") {
						$payment_method = 'Mednefits Credits';
					}

					if($data['payment_method'] == "bank_transfer") {
						$payment_method = 'Bank Transfer';
					}

					if($data['payment_method'] == "giro") {
						$payment_method = 'GIRO';
					}

					$container[] = array(
						'Status'			=> (int)$data['status'] == 1 ? 'Paid' : 'Pending',
						'Invoice Date'		=> $data['invoice_date'],
						'Number'			=> $data['number'],
						'Amount Due'		=> $data['amount_due'],
						'Payment Due'		=> $data['payment_due'],
						'Amount Paid'		=> $data['payment_amount'],
						'Payment Date'		=> $data['paid_date'],
						'Payment Method'	=> $payment_method,
						'Panel/Non-Panel'	=> ucfirst($data['type']),
						'Remarks'			=> $data['payment_remarks']
					);
				}

				$excel = Excel::create($title, function($excel) use($container) {

						$excel->sheet('Sheetname', function($sheet) use($container) {
							$sheet->fromArray( $container );
						});

				})->export('csv');
				return array('status' => TRUE, 'message' => 'Successfully Downloaded!');
			}

			$pagination['data'] = $format;
			$pagination['total_due'] = number_format($total_due, 2);
			return $pagination;

		} elseif ($input['type'] == 'plan') {
			$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();
			$pagination = [];
			$format = [];
			$total_due = 0;

			$invoiceHistory = new InvoiceHistoryService([
				'type' => 'plan',
				'customer_id' => $customer_id,
				'per_page' => $limit
			]);
			
			$pagination = $invoiceHistory->getInvoiceHistory();
	
			if ($plan && $plan->account_type != "lite_plan") {
				$pagination['data'] = $pagination['data'] ?? [];
			} else {
				$pagination['data'] = [];
			}

			if($plan && $plan->account_type != "lite_plan") {
				// $all_plan_data = DB::table('customer_active_plan')
				// 			->join('corporate_invoice', 'corporate_invoice.customer_active_plan_id', '=', 'customer_active_plan.customer_active_plan_id')
				// 			->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_active_plan.customer_start_buy_id')
				// 			->join('customer_link_customer_buy', 'customer_link_customer_buy.customer_buy_start_id', '=', 'customer_buy_start.customer_buy_start_id')
				// 			->join('corporate', 'corporate.corporate_id', '=', 'customer_link_customer_buy.corporate_id')
				// 			->where('customer_buy_start.customer_buy_start_id', $customer_id)
				// 			->get();		

				// $active_plans = DB::table('customer_active_plan')
				// 							->join('corporate_invoice', 'corporate_invoice.customer_active_plan_id', '=', 'customer_active_plan.customer_active_plan_id')
				// 							->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_active_plan.customer_start_buy_id')
				// 							->join('customer_link_customer_buy', 'customer_link_customer_buy.customer_buy_start_id', '=', 'customer_buy_start.customer_buy_start_id')
				// 							->join('corporate', 'corporate.corporate_id', '=', 'customer_link_customer_buy.corporate_id')
				// 							->where('customer_buy_start.customer_buy_start_id', $customer_id)
				// 							->orderBy('corporate_invoice.invoice_date', 'desc')
				// 							->paginate($limit);

				// $pagination['current_page'] = $active_plans->getCurrentPage();
				// $pagination['last_page'] = $active_plans->getLastPage();
				// $pagination['total'] = $active_plans->getTotal();
				// $pagination['per_page'] = $active_plans->getPerPage();
				// $pagination['count'] = $active_plans->count();
				
				// foreach ($all_plan_data as $key => $data) {
				// 	$result = \PlanHelper::getCompanyInvoice($data->corporate_invoice_id);
				// 	$total_due += $result['amount_due'];
				// }
				
				// foreach($active_plans as $key => $active) {
				// 	$result = \PlanHelper::getCompanyInvoice($active->corporate_invoice_id);
				// 	$result['invoice_id'] = $active->corporate_invoice_id;
				// 	$result['corporate_invoice_id'] = $active->corporate_invoice_id;
				// 	$result['customer_id'] = $active->customer_buy_start_id;

				// 	$temp = array(
				// 		'id' => $result['invoice_id'],
				// 		'invoice_date' => date('j M Y', strtotime($result['invoice_date'])),
				// 		'payment_due' => date('j M Y', strtotime($result['invoice_due'])),
				// 		'number' => $result['invoice_number'],
				// 		'status'	=> $result['paid'] ? 1 : 0,
				// 		'amount_due' => $result['amount_due'],
				// 		'paid_date'	=> $result['paid'] ? date('j M Y', strtotime($result['payment_date'])) : NULL,
				// 		'payment_amount' => $result['total'],
				// 		'type'			=> null,
				// 		'currency_type' => $result['currency_type'], 
				// 		'payment_remarks' => $result['payment_remarks'],
				// 		'payment_method' => null,
				// 		'company_name' => $result['company'],
				// 		'category_type'			=> $input['type']
				// 	);

				// 	array_push($format, $temp);
				// }

				if($download) {
					$date = date('d-m-Y h:i:s');
					$title = "Company History Invoice type - Plan-".$date;

					$container = array();

					foreach($format as $key => $data) {
						$payment_method = null;
						if($data['payment_method'] == "mednefits_credits") {
							$payment_method = 'Mednefits Credits';
						}

						if($data['payment_method'] == "bank_transfer") {
							$payment_method = 'Bank Transfer';
						}

						if($data['payment_method'] == "giro") {
							$payment_method = 'GIRO';
						}

						$container[] = array(
							'Status'			=> (int)$data['status'] == 1 ? 'Paid' : 'Pending',
							'Invoice Date'		=> $data['invoice_date'],
							'Number'			=> $data['number'],
							'Amount Due'		=> $data['amount_due'],
							'Payment Due'		=> $data['payment_due'],
							'Amount Paid'		=> $data['payment_amount'],
							'Payment Date'		=> $data['paid_date'],
							'Payment Method'	=> $payment_method,
							'Panel/Non-Panel'	=> ucfirst($data['type']),
							'Remarks'			=> $data['payment_remarks']
						);
					}

					$excel = Excel::create($title, function($excel) use($container) {

							$excel->sheet('Sheetname', function($sheet) use($container) {
								$sheet->fromArray( $container );
							});

					})->export('csv');
					return array('status' => TRUE, 'message' => 'Successfully Downloaded!');
				}
			}
			
			// $pagination['data'] = $format;
			$pagination['total_due'] = number_format($total_due, 2);
			return $pagination;
			
		} elseif ($input['type'] == 'deposit') {
			$all_deposit_data = DB::table('spending_deposit_credits')
									->join('customer_active_plan', 'customer_active_plan.customer_active_plan_id', "=", 'spending_deposit_credits.customer_active_plan_id')
									->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', "=", 'customer_active_plan.customer_start_buy_id')
									->where('customer_buy_start.customer_buy_start_id', $customer_id)
									->get();

			$deposits = DB::table('spending_deposit_credits')
							->join('customer_active_plan', 'customer_active_plan.customer_active_plan_id', "=", 'spending_deposit_credits.customer_active_plan_id')
							->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', "=", 'customer_active_plan.customer_start_buy_id')
							->where('customer_buy_start.customer_buy_start_id', $customer_id)
							->orderBy('spending_deposit_credits.invoice_date', 'desc')
	                       ->paginate($limit);
	
			
			$pagination = [];
			$pagination['current_page'] = $deposits->getCurrentPage();
			$pagination['last_page'] = $deposits->getLastPage();
			$pagination['total'] = $deposits->getTotal();
			$pagination['per_page'] = $deposits->getPerPage();
			$pagination['count'] = $deposits->count();
			$format = [];
			$total_due = 0;

			foreach ($all_deposit_data as $key => $data) {
				$result = \PlanHelper::getSpendingDeposit($data->deposit_id);
				$total_due += $result['amount_due'];
			}

			foreach ($deposits as $key => $deposit) {
				$result = \PlanHelper::getSpendingDeposit($deposit->deposit_id);
				$result['invoice_id'] = $deposit->deposit_id;
				$result['deposit_id'] = $deposit->deposit_id;
				$result['customer_id'] = $deposit->customer_buy_start_id;
				// array_push($format, $result);

				$temp = array(
					'id' => $customer_id,
					'invoice_date' => date('j M Y', strtotime($result['invoice_date'])),
					'payment_due' => date('j M Y', strtotime($result['invoice_due'])),
					'number' => $result['invoice_number'],
					'status'	=> $result['paid'] ? 1 : 0 ,
					'amount_due' => $result['amount_due'],
					'paid_date'	=> $result['paid'] ? date('j M Y', strtotime($result['payment_date'])) : NULL,
					'payment_amount' => $result['total'],
					'currency_type' => $result['currency_type'],
					'type'			=> $input['type'],
                    'payment_remarks' => $data->payment_remarks,
                    'payment_method' => null,
					'company_name' => $result['company'],
					'category_type'			=> $input['type']
				);

				array_push($format, $temp);
			}

			if($download) {

				$date = date('d-m-Y h:i:s');
				$title = "Company History Invoice type - Deposit-".$date;
				$container = array();
				foreach($format as $key => $data) {
					$payment_method = 'Bank Transfer';
					if($data['payment_method'] == "mednefits_credits") {
						$payment_method = 'Mednefits Credits';
					}

					if($data['payment_method'] == "bank_transfer") {
						$payment_method = 'Bank Transfer';
					}

					if($data['payment_method'] == "giro") {
						$payment_method = 'GIRO';
					}

					$container[] = array(
						'Status'			=> (int)$data['status'] == 1 ? 'Paid' : 'Pending',
						'Invoice Date'		=> $data['invoice_date'],
						'Number'			=> $data['number'],
						'Amount Due'		=> $data['amount_due'],
						'Payment Due'		=> $data['payment_due'],
						'Amount Paid'		=> $data['payment_amount'],
						'Payment Date'		=> $data['paid_date'],
						'Payment Method'	=> $payment_method,
						'Panel/Non-Panel'	=> null,
						'Remarks'			=> $data['payment_remarks']
					);
				}

				$excel = Excel::create($title, function($excel) use($container) {

						$excel->sheet('Sheetname', function($sheet) use($container) {
							$sheet->fromArray( $container );
						});

				})->export('csv');
				return array('status' => TRUE, 'message' => 'Successfully Downloaded!');
			}
	
			$pagination['data'] = $format;
			$pagination['total_due'] = number_format($total_due, 2);
			return $pagination;
			
		} elseif ($input['type'] == 'plan_withdrawal') {

			 $all_withdraw_data = DB::table('payment_refund')
									->join('customer_active_plan', 'customer_active_plan.customer_active_plan_id',"=",'payment_refund.customer_active_plan_id')
									->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id',"=", 'customer_active_plan.customer_start_buy_id')
									->whereIn('customer_active_plan.account_type',['stand_alone_plan', "=",'lite_plan'])
									->where('customer_buy_start.customer_buy_start_id', $customer_id)
									->get();

			 $refunds = DB::table('payment_refund')
							->join('customer_active_plan', 'customer_active_plan.customer_active_plan_id',"=",'payment_refund.customer_active_plan_id')
							->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id',"=", 'customer_active_plan.customer_start_buy_id')
							->whereIn('customer_active_plan.account_type',['stand_alone_plan', "=",'lite_plan'])
							->where('customer_buy_start.customer_buy_start_id', $customer_id)
							->orderBy('payment_refund.created_at', 'desc')
							->paginate($limit);
			
			$pagination = [];
			$pagination['current_page'] = $refunds->getCurrentPage();
			$pagination['last_page'] = $refunds->getLastPage();
			$pagination['total'] = $refunds->getTotal();
			$pagination['per_page'] = $refunds->getPerPage();
			$pagination['count'] = $refunds->count();
			$format = [];

			$total_due = 0;

			foreach ($all_withdraw_data as $key => $data) {
				$result = \PlanHelper::getRefundLists($data->payment_refund_id);
				$total_due += $result['amount_due'];
			}

			foreach ($refunds as $key => $refund) {
				$result = \PlanHelper::getRefundLists($refund->payment_refund_id);
				$result['invoice_id'] = $refund->payment_refund_id;
				$result['payment_refund_id'] = $refund->payment_refund_id;
				$result['customer_buy_start_id'] = $refund->customer_buy_start_id;
				$result['customer_id'] = $refund->customer_buy_start_id;
				// array_push($format, $result);

				$temp = array(
					'id' => $customer_id,
					'invoice_date' => date('j M Y', strtotime($result['cancellation_date'])),
					'payment_due' => NULL,
					'number' => $result['cancellation_number'],
					'status'	=> $result['paid'] ? 1 : 0 ,
					'amount_due' => $result['amount_due'],
					'paid_date'	=> $result['date_refund'],
					'payment_amount' => $result['total_refund'],
					'currency_type' => $result['currency_type'],
                    'payment_remarks' => $result['payment_remarks'],
                    'payment_method' => 'bank_transfer',
					'company_name' => $result['billing_info']['company'],
					'category_type'			=> $input['type']
				);

				array_push($format, $temp);
			}

			if($download) {

				$date = date('d-m-Y h:i:s');
				$title = "Company History Invoice type - plan withdrawal-".$date;

				$container = array();
				foreach($format as $key => $data) {
					$payment_method = 'Bank Transfer';
					if($data['payment_method'] == "mednefits_credits") {
						$payment_method = 'Mednefits Credits';
					}

					if($data['payment_method'] == "bank_transfer") {
						$payment_method = 'Bank Transfer';
					}

					if($data['payment_method'] == "giro") {
						$payment_method = 'GIRO';
					}

					$container[] = array(
						'Status'			=> (int)$data['status'] == 1 ? 'Paid' : 'Pending',
						'Invoice Date'		=> $data['invoice_date'],
						'Number'			=> $data['number'],
						'Amount Due'		=> $data['amount_due'],
						'Payment Due'		=> $data['payment_due'],
						'Amount Paid'		=> $data['payment_amount'],
						'Payment Date'		=> $data['paid_date'],
						'Payment Method'	=> $payment_method,
						'Panel/Non-Panel'	=> null,
						'Remarks'			=> $data['payment_remarks']
					);
				}
				$excel = Excel::create($title, function($excel) use($container) {

						$excel->sheet('Sheetname', function($sheet) use($container) {
							$sheet->fromArray( $container );
						});

				})->export('csv');
				return array('status' => TRUE, 'message' => 'Successfully Downloaded!');
			}
			
			$pagination['data'] = $format;
			$pagination['total_due'] = number_format($total_due, 2);
			return $pagination;
		}  else if($input['type'] == "spending_purchase") {
			$pagination = [];
			$format = [];
			$total_due = 0;
			$allInvoices = DB::table('spending_purchase_invoice')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->get();
			$invoices = DB::table('spending_purchase_invoice')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->paginate($limit);

			foreach($allInvoices as $key => $spendingPurchase) {
				$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $spendingPurchase->customer_active_plan_id)->first();
				$customer_wallet = DB::table('customer_credits')->where('customer_id', $spendingPurchase->customer_id)->first();
				
				$data = array();
				// medical spending account
				$data['medical_credits_purchase'] = $spendingPurchase->medical_purchase_credits;
				// wellness spending account
				$data['wellness_credits_purchase'] = $spendingPurchase->wellness_purchase_credits;
				$totalCredits = $data['medical_credits_purchase'] + $data['wellness_credits_purchase'];
				$totalBalance = $totalCredits - $spendingPurchase->payment_amount;
				$total_due += $totalBalance;
			}

			$pagination['current_page'] = $invoices->getCurrentPage();
			$pagination['last_page'] = $invoices->getLastPage();
			$pagination['total'] = $invoices->getTotal();
			$pagination['per_page'] = $invoices->getPerPage();
			$pagination['count'] = $invoices->count();

			foreach($invoices as $key => $spendingPurchase) {
				$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $spendingPurchase->customer_active_plan_id)->first();
				$customer_wallet = DB::table('customer_credits')->where('customer_id', $spendingPurchase->customer_id)->first();
				
				$data = array();
				$data['spending_purchase_invoice_id'] = $spendingPurchase->spending_purchase_invoice_id;
				$data['company_id'] = $spendingPurchase->customer_id;
				$data['payment_status'] = $spendingPurchase->payment_status == 1 ? 'PAID' : 'PENDING';
				$data['paid'] = $spendingPurchase->payment_status == 1 ? true : false;
				$data['invoice_date'] = date('d F Y', strtotime($spendingPurchase->invoice_date));
				$data['invoice_number'] = $spendingPurchase->invoice_number;
				$total = (float)$spendingPurchase->medical_purchase_credits + (float)$spendingPurchase->wellness_purchase_credits;
				$data['total']  = $total;
				$data['amount_due'] = $total - (float)$spendingPurchase->payment_amount;
				$data['invoice_due'] = date('d F Y', strtotime($spendingPurchase->invoice_due));
				$data['payment_date'] = $spendingPurchase->payment_date ? date('d F Y', strtotime($spendingPurchase->payment_date)) : null;
				$data['remarks']    = $spendingPurchase->remarks;
				$data['company_name']   = $spendingPurchase->company_name;
				$data['company_address']   = $spendingPurchase->company_address;
				$data['postal']   = $spendingPurchase->postal;
				$data['contact_name']   = $spendingPurchase->contact_name;
				$data['contact_number']   = $spendingPurchase->contact_number;
				$data['contact_email']   = $spendingPurchase->contact_email;
				$data['plan_start']   = date('d F Y', strtotime($spendingPurchase->plan_start));
				$data['plan_end']   = date('d F Y', strtotime($spendingPurchase->plan_end));
				$data['duration']   = $spendingPurchase->duration;
				$data['account_type'] = \PlanHelper::getAccountType($active_plan->account_type);
				$data['plan_type'] = 'Pre-paid Credits Plan Mednefits Care (Corporate)';
				$data['currency_type']   = strtoupper($customer_wallet->currency_type);
				// medical spending account
				$data['medical_spending_account'] = (float)$spendingPurchase->medical_purchase_credits > 0 ? true : false;
				$data['medical_credits_purchase'] = $spendingPurchase->medical_purchase_credits;
				$data['medical_credit_bonus'] = $spendingPurchase->medical_credit_bonus;
				$data['medical_total_credits']  = $spendingPurchase->medical_purchase_credits + $spendingPurchase->medical_credit_bonus;
	
				// wellness spending account
				$data['wellness_spending_account'] = (float)$spendingPurchase->wellness_purchase_credits > 0 ? true : false;
				$data['wellness_credits_purchase'] = $spendingPurchase->wellness_purchase_credits;
				$data['wellness_credit_bonus'] = $spendingPurchase->wellness_credit_bonus;
				$data['wellness_total_credits']  = $spendingPurchase->wellness_purchase_credits + $spendingPurchase->wellness_credit_bonus;
				
				$totalCredits = round($data['medical_credits_purchase'] + $data['wellness_credits_purchase'], 2);
				$totalBalance = $totalCredits <= $spendingPurchase->payment_amount ? 0 : round($totalCredits - $spendingPurchase->payment_amount, 2);
				
				$temp = array(
					'id'		=> $data['spending_purchase_invoice_id'],
					'invoice_date' => date('j M Y', strtotime($data['invoice_date'])),
					'payment_due' => date('j M Y', strtotime($data['invoice_due'])),
					'number' => $data['invoice_number'],
					'status'	=> $totalBalance <= 0 ? 1 : 0 ,
					'amount_due' => number_format($totalBalance, 2),
					'paid_date'	=> $data['payment_date'],
					'payment_amount' => number_format($totalCredits, 2),
					'currency_type' => $data['currency_type'],
					'company_name' => $data['company_name'],
					'payment_method'	=> 'bank_transfer',
					'payment_remarks'	=> $data['remarks'],
					'category_type'			=> $input['type']
				);

				array_push($format, $temp);
			}

			if($download) {
				$date = date('d-m-Y h:i:s');
				$title = "Company History Invoice type - Spending Purchase-".$date;
				$container = array();

				foreach($format as $key => $data) {
					$payment_method = null;
					if($data['payment_method'] == "mednefits_credits") {
						$payment_method = 'Mednefits Credits';
					}

					if($data['payment_method'] == "bank_transfer") {
						$payment_method = 'Bank Transfer';
					}

					if($data['payment_method'] == "giro") {
						$payment_method = 'GIRO';
					}

					$container[] = array(
						'Status'			=> (int)$data['status'] == 1 ? 'Paid' : 'Pending',
						'Invoice Date'		=> $data['invoice_date'],
						'Number'			=> $data['number'],
						'Amount Due'		=> $data['amount_due'],
						'Payment Due'		=> $data['payment_due'],
						'Amount Paid'		=> $data['payment_amount'],
						'Payment Date'		=> $data['paid_date'],
						'Payment Method'	=> $payment_method,
						'Panel/Non-Panel'	=> null,
						'Remarks'			=> $data['payment_remarks']
					);
				}

				$excel = Excel::create($title, function($excel) use($container) {

						$excel->sheet('Sheetname', function($sheet) use($container) {
							$sheet->fromArray( $container );
						});

				})->export('csv');
				return array('status' => TRUE, 'message' => 'Successfully Downloaded!');
			}

			$pagination['data'] = $format;
			$pagination['total_due'] = number_format($total_due, 2);
			return $pagination;
		}

		return array('status' => FALSE, 'message' => 'please check the request!');
		
	}
}
