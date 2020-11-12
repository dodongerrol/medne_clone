<?php
class SpendingInvoiceLibrary
{
	public static function validateStartDate($date)
	{
		return (bool)strtotime($date);
	}

	public static function my_array_unique($array, $keep_key_assoc = false){
		$duplicate_keys = array();
		$tmp = array();       

		foreach ($array as $key => $val){
	            // convert objects to arrays, in_array() does not support objects
			if (is_object($val))
				$val = (array)$val;

			if (!in_array($val, $tmp))
				$tmp[] = $val;
			else
				$duplicate_keys[] = $key;
		}

		foreach ($duplicate_keys as $key)
			unset($array[$key]);

		return $keep_key_assoc ? $array : array_values($array);
	}

	public static function getEndDate($end)
	{
		$temp_end = date('Y-m-t H:i:s', strtotime('+23 hours', strtotime($end)));
		$temp_minutes_end = date('Y-m-d H:i:s', strtotime('+59 minutes', strtotime($temp_end)));
		$final_end = date('Y-m-d H:i:s', strtotime('+59 seconds', strtotime($temp_minutes_end)));

		return $final_end;
	}

	public static function checkCompanyTransactions($customer_id, $start, $end, $plan_method)
	{
		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
		$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();
		$spending = CustomerHelper::getAccountSpendingBasicPlanStatus($customer_id);
		// $lite_plan = StringHelper::liteCompanyPlanStatus($customer_id);
		$transactions = 0;

		if($spending['medical_method'] == "pre_paid" && $plan_method == "post_paid") {
			foreach ($corporate_members as $key => $member) {
				$ids = StringHelper::getSubAccountsID($member->user_id);
				$transactions_temp = DB::table('transaction_history')
						->whereIn('UserID', $ids)
						->where('lite_plan_enabled', 1)
						->where('credit_cost', 0)
						->where('deleted', 0)
						->where('paid', 1)
						->where('created_at', '>=', $start)
						->where('created_at', '<=', $end)
						->get();
				
				if(sizeof($transactions_temp) > 0)	{
					foreach($transactions_temp as $key => $trans)	{
						if($trans->spending_type == 'medical') {
							$table_wallet_history = 'wallet_history';
						} else {
							$table_wallet_history = 'wellness_wallet_history';
						}
						$logs_lite_plan = DB::table($table_wallet_history)
						->where('logs', 'deducted_from_mobile_payment')
						->where('lite_plan_enabled', 1)
						->where('id', $trans->transaction_id)
						->first();

						if(!$logs_lite_plan)	{
							$transactions++;
						}
					}
				}
			}
		} else {
			foreach ($corporate_members as $key => $member) {
				$ids = StringHelper::getSubAccountsID($member->user_id);
				$temp_trans_lite_plan = DB::table('transaction_history')
						->whereIn('UserID', $ids)
						->where('lite_plan_enabled', 1)
						->where('deleted', 0)
						->where('paid', 1)
						->where('created_at', '>=', $start)
						->where('created_at', '<=', $end)
						->count();
	
				$temp_trans = DB::table('transaction_history')
							->whereIn('UserID', $ids)
							->where('credit_cost', '>', 0)
							->where('deleted', 0)
							->where('paid', 1)
							->where('created_at', '>=', $start)
							->where('created_at', '<=', $end)
							->count();
				$transactions += $temp_trans_lite_plan + $temp_trans;
			}
		}

		if($transactions == 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public static function createStatement($customer_id, $start, $end, $plan_method)
	{
		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();
		$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();
		$spending = CustomerHelper::getAccountSpendingStatus($customer_id);
		$lite_plan = false;
		$lite_plan = StringHelper::liteCompanyPlanStatus($customer_id);
		$payment_method = $spending['medical_payment_method_panel'];

		$business_contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $customer_id)->first();
		$billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $customer_id)->first();
		$currency_data = DB::table('currency_options')->where('currency_type', $customer->currency_type)->first();
		$total_e_claim_amount = 0;
		$total_in_network_amount = 0;
		$transactions = [];

		// if($spending['medical_method'] == "pre_paid" && $plan_method == "post_paid") {
			// foreach ($corporate_members as $key => $member) {
			// 	$ids = StringHelper::getSubAccountsID($member->user_id);
	
			// 	$in_network = DB::table('transaction_history')
			// 	->whereIn('UserID', $ids)
			// 	->where('lite_plan_enabled', 1)
			// 	->where('credit_cost', 0)
			// 	->where('deleted', 0)
			// 	->where('paid', 1)
			// 	->where('created_at', '>=', $start)
			// 	->where('created_at', '<=', $end)
			// 	->orderBy('created_at', 'desc')
			// 	->get();
		
			// 	// $e_claim = DB::table('e_claim')
			// 	// ->where('status', 1)
			// 	// ->whereIn('user_id', $ids)
			// 	// ->where('date', '>=', $start)
			// 	// ->where('date', '<=', $end)
			// 	// ->orderBy('created_at', 'desc')
			// 	// ->get();
	
			// 	// foreach($e_claim as $key => $res) {
			// 	// 	$total_e_claim_amount += $res->amount;
			// 	// }
	
			// 	foreach ($in_network as $key => $trans) {
			// 		if($trans->spending_type == 'medical') {
			// 			$table_wallet_history = 'wallet_history';
			// 		} else {
			// 			$table_wallet_history = 'wellness_wallet_history';
			// 		}
			// 		$logs_lite_plan = DB::table($table_wallet_history)
			// 			->where('logs', 'deducted_from_mobile_payment')
			// 			->where('lite_plan_enabled', 1)
			// 			->where('id', $trans->transaction_id)
			// 			->first();

			// 		if(!$logs_lite_plan)	{
			// 			$total_in_network_amount += $trans->credit_cost;
			// 			array_push($transactions, $trans->transaction_id);
			// 		}
			// 	}
			// }
		// } else {
			foreach ($corporate_members as $key => $member) {
				$ids = StringHelper::getSubAccountsID($member->user_id);
	
				$temp_trans_lite_plan = DB::table('transaction_history')
				->whereIn('UserID', $ids)
				->where('lite_plan_enabled', 1)
				->where('deleted', 0)
				->where('paid', 1)
				->where('created_at', '>=', $start)
				->where('created_at', '<=', $end)
				->orderBy('created_at', 'desc')
				->get();
	
				$temp_trans = DB::table('transaction_history')
				->whereIn('UserID', $ids)
				->where('lite_plan_enabled', 0)
				->where('credit_cost', '>', 0)
				->where('deleted', 0)
				->where('paid', 1)
				->where('created_at', '>=', $start)
				->where('created_at', '<=', $end)
				->orderBy('created_at', 'desc')
				->get();
				$transactions_temp = array_merge($temp_trans_lite_plan, $temp_trans);
				$in_network = self::my_array_unique($transactions_temp);
	
				// $e_claim = DB::table('e_claim')
				// ->where('status', 1)
				// ->whereIn('user_id', $ids)
				// ->where('date', '>=', $start)
				// ->where('date', '<=', $end)
				// ->orderBy('created_at', 'desc')
				// ->get();
	
				// foreach($e_claim as $key => $res) {
				// 	$total_e_claim_amount += $res->amount;
				// }
	
				foreach ($in_network as $key => $trans) {
					$total_in_network_amount += $trans->credit_cost;
					array_push($transactions, $trans->transaction_id);
				}
			}
		// }
		
		$company_details = DB::table('customer_business_information')->where('customer_buy_start_id', $customer_id)->first();
		$number = InvoiceLibrary::getInvoiceNuber('company_credits_statement', 3);
		$spending_invoice_day = $customer->spending_default_invoice_day;
		$day = date('t', strtotime('+1 month', strtotime($start)));
	  
		if($currency_data) {
			$currency = $currency_data->currency_value;
		} else {
			$currency = 3.00;
		}
		
		if((int)$spending_invoice_day == 31) {
			if($customer->invoice_step == "before") {
				if((int)$spending_invoice_day > (int)$day) {
					$statement_date = date('Y-m-'.$day, strtotime('-1 month', strtotime($start)));
				} else {
					$statement_date = date('Y-m-'.$spending_invoice_day, strtotime('-1 month', strtotime($start)));
				}
			} else {
				if((int)$spending_invoice_day > (int)$day) {
					$statement_date = date('Y-m-'.$day, strtotime('+1 month', strtotime($start)));
				} else {
					$statement_date = date('Y-m-'.$spending_invoice_day, strtotime('+1 month', strtotime($start)));
				}
			}
		} else {
			$statement_date = date('Y-m-'.$spending_invoice_day, strtotime('+1 month', strtotime($start)));
		}

		$statement_due = date('Y-m-d', strtotime('+15 days', strtotime($statement_date)));
		$statement_data = array(
			'statement_customer_id'     => $customer_id,
			'statement_number'          => $number,
			'statement_date'            => date('Y-m-d', strtotime($statement_date)),
			'statement_due'             => date('Y-m-d', strtotime($statement_due)),
			'statement_start_date'      => $start,
			'statement_end_date'        => $end,
			'statement_company_name' 		=> $company_details->company_name,
    		'statement_company_address' => $company_details->company_address,
			'statement_contact_name'    => $billing_contact->first_name.' '.$billing_contact->last_name,
			'statement_contact_number'  => $billing_contact->phone,
			'statement_contact_email'   => $billing_contact->billing_email,
			'statement_in_network_amount'   => $total_in_network_amount,
			'statement_e_claim_amount'  => $total_e_claim_amount,
			'currency_type'				=> $customer->currency_type,
			'currency_value'			=> $currency,
			'payment_method'			=> $payment_method
		);

		if($lite_plan) {
			$statement_data['lite_plan'] = 1;
		}

		// $statement_data = $spending['medical_payment_method_panel'];
		if($spending['medical_method'] == "pre_paid" && $plan_method == "pre_paid" || $spending['account_type'] == "enterprise_plan") {
			if($spending['account_type'] != "enterprise_plan") {
				$statement_data['plan_method'] = 'pre_paid';
			}
			
			$statement_data['paid_date'] = date('Y-m-d', strtotime($statement_date));
			$statement_data['paid_amount'] = 0;
			$statement_data['statement_status'] = 1;
		} else {
			$statement_data['plan_method'] = 'post_paid';
		}

	   // create statement
		$statement_class = new CompanyCreditsStatement( );
		$statement_result = $statement_class->createCompanyCreditsStatement($statement_data);
		$statement_id = $statement_result->id;

		foreach ($transactions as $key => $trans) {
			$check_transaction = \SpendingInvoiceTransactions::where('transaction_id',  $trans)->first();

			if(!$check_transaction) {
        		// insert to spending invoice transaction
				\SpendingInvoiceTransactions::create(['invoice_id' => $statement_id, 'transaction_id' => $trans]);
			}
		}

		return $statement_result;
	}

	public static function getTotalCreditsInNetworkTransactions($invoice_id, $customer_id, $fields)
	{
		$total_credits = 0;
		$transaction_details = [];
		$total_consultation = 0;
		$in_network_transactions = 0;
		$lite_plan = false;
		$total_gp_medicine = 0;
		$total_gp_consultation = 0;
		$total_dental = 0;
		$total_tcm = 0;
		$total_transactions = 0;
		$total_post_paid_spent = 0;
		$total_pre_paid_spent = 0;
		$with_post_paid = false;

		$statement = DB::table('company_credits_statement')
						->where('statement_id', $invoice_id)
						->first();
		// check new transactions added
		$check_invoice_transactions = self::checkSpendingInvoiceNewTransactions($customer_id, $statement->statement_start_date, $statement->statement_end_date, $statement->statement_id, 'post_paid');
		$transaction_invoices = SpendingInvoiceTransactions::where('invoice_id', $invoice_id)->get();
		// return $invoice_id;
		foreach ($transaction_invoices as $key => $transaction) {
			$mednefits_fee = 0;
			$mednefits_credits = 0;
			$trans = Transaction::where('transaction_id', $transaction->transaction_id)
						->where('deleted', 0)->where('paid', 1)
						->first();
						
			if($trans) {
				$consultation_cash = false;
				$consultation_credits = false;
				$service_cash = false;
				$service_credits = false;

				if((int)$trans['deleted'] == 0) {
					if($trans->default_currency == $trans->currency_type && $trans->default_currency == "myr" || $trans->default_currency == "myr" && $trans->currency_type == "sgd") {
						$in_network_transactions += (float)$trans['credit_cost'] * $trans->currency_amount;
					} else {
						$in_network_transactions += (float)$trans['credit_cost'];
					}
	
					if($trans['spending_type'] == 'medical') {
						$table_wallet_history = 'wallet_history';
					} else {
						$table_wallet_history = 'wellness_wallet_history';
					}

					$total_transactions++;


					$history = DB::table($table_wallet_history)
						->where('logs', 'deducted_from_mobile_payment')
						->where('id', $trans['transaction_id'])
						->first();

					if($history && $history->spending_method == "pre_paid") {
						$total_pre_paid_spent += $trans->default_currency == "myr" ? $history->credit * $trans->currency_amount : $history->credit;
					} else {
						$with_post_paid = true;
						$total_post_paid_spent += $trans->default_currency == "myr" ? (float)$trans['credit_cost'] * $trans->currency_amount : (float)$trans['credit_cost'];
					}

					if((int)$trans['lite_plan_enabled'] == 1) {
						$lite_plan = true;
						$logs_lite_plan = DB::table($table_wallet_history)
						->where('logs', 'deducted_from_mobile_payment')
						->where('lite_plan_enabled', 1)
						->where('id', $trans['transaction_id'])
						->first();

						if($logs_lite_plan && (int)$trans['credit_cost'] >= 0 && (int)$trans['lite_plan_use_credits'] == 0) {
							$consultation_credits = true;
							$service_credits = true;
							
							if($trans->default_currency == "myr") {
								$total_consultation += floatval($logs_lite_plan->credit);
								$mednefits_credits = floatval($logs_lite_plan->credit) * $trans->currency_amount;
								$total_gp_consultation += floatval($logs_lite_plan->credit) * $trans->currency_amount;
								if($logs_lite_plan && $logs_lite_plan->spending_method == "pre_paid") {
									$total_pre_paid_spent += $logs_lite_plan->credit * $trans->currency_amount;
								} else {
									$total_post_paid_spent += $logs_lite_plan->credit * $trans->currency_amount;
								}
							} else {
								$total_consultation += floatval($logs_lite_plan->credit);
								$mednefits_credits = floatval($logs_lite_plan->credit);
								$total_gp_consultation += floatval($logs_lite_plan->credit);
								if($logs_lite_plan && $logs_lite_plan->spending_method == "pre_paid") {
									$total_pre_paid_spent += $logs_lite_plan->credit;
								} else {
									$total_post_paid_spent += $logs_lite_plan->credit;
								}
							}
							
						} else if($logs_lite_plan && $trans['procedure_cost'] >= 0 && (int)$trans['lite_plan_use_credits'] == 1){
							$consultation_credits = true;
							$service_credits = true;
							if($trans->default_currency == "myr") {
								$total_consultation += floatval($logs_lite_plan->credit) * $trans->currency_amount;
								$mednefits_credits = floatval($logs_lite_plan->credit) * $trans->currency_amount;
								$total_gp_consultation += floatval($logs_lite_plan->credit);
								if($logs_lite_plan && $logs_lite_plan->spending_method == "pre_paid") {
									$total_pre_paid_spent += $logs_lite_plan->credit * $trans->currency_amount;
								} else {
									$total_post_paid_spent += $logs_lite_plan->credit * $trans->currency_amount;
								}
							} else {
								$total_consultation += floatval($logs_lite_plan->credit);
								$mednefits_credits = floatval($logs_lite_plan->credit);
								$total_gp_consultation += floatval($logs_lite_plan->credit);
								if($logs_lite_plan && $logs_lite_plan->spending_method == "pre_paid") {
									$total_pre_paid_spent += $logs_lite_plan->credit;
								} else {
									$total_post_paid_spent += $logs_lite_plan->credit;
								}
							}
						} else if($trans['procedure_cost'] >= 0 && $trans['lite_plan_use_credits'] === 0 || $trans['procedure_cost'] >= 0 && $trans['lite_plan_use_credits'] === "0"){
							if($trans->default_currency == "myr") {
								$total_consultation += floatval($trans['consultation_fees']) * $trans->currency_amount;
								$total_gp_consultation + floatval($trans['consultation_fees']) * $trans->currency_amount;
							} else {
								$total_consultation += floatval($trans['consultation_fees']);
								$total_gp_consultation += floatval($trans['consultation_fees']);
							}
						}
					}

					if($fields == true) {
						if($trans['credit_cost'] > 0) {
							if($trans->default_currency == $trans->currency_type && $trans->default_currency == "myr" || $trans->default_currency == "myr" && $trans->currency_type == "sgd") {
								$mednefits_credits += (float)$trans['credit_cost'] * $trans->currency_amount;
							} else {
								$mednefits_credits += (float)$trans['credit_cost'];
							}
							$cash = 0;
						} else {
							$mednefits_credits += 0;
							if($trans->default_currency == $trans->currency_type && $trans->default_currency == "myr") {
								$cash = (float)$trans['procedure_cost'] * $trans->currency_amount;
							} else {
								$cash = (float)$trans['procedure_cost'];
							}
						}

						$receipt_images = DB::table('user_image_receipt')->where('transaction_id', $trans['transaction_id'])->get();
						$clinic = DB::table('clinic')->where('ClinicID', $trans['ClinicID'])->first();
						$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
						$customer = DB::table('user')->where('UserID', $trans['UserID'])->first();
						$procedure_temp = "";
						// get services
						if($trans['multiple_service_selection'] == 1 || $trans['multiple_service_selection'] == "1")
						{
							// get multiple service
							$service_lists = DB::table('transaction_services')
												->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
												->where('transaction_services.transaction_id', $trans['transaction_id'])
												->get();

							foreach ($service_lists as $key => $service) {
								if(sizeof($service_lists) - 2 == $key) {
									$procedure_temp .= ucwords($service->Name).' and ';
								} else {
									$procedure_temp .= ucwords($service->Name).',';
								}
								$procedure = rtrim($procedure_temp, ',');
							}
							$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
						} else {
							$service_lists = DB::table('clinic_procedure')
												->where('ProcedureID', $trans['ProcedureID'])
												->first();
							if($service_lists) {
								$procedure = ucwords($service_lists->Name);
								$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
							} else {
								// $procedure = "";
								$clinic_name = ucwords($clinic_type->Name);
							}
						}

						// check if there is a receipt image
						$receipt = DB::table('user_image_receipt')->where('transaction_id', $trans['transaction_id'])->count();

						if($receipt > 0) {
							$receipt_status = TRUE;
						} else {
							$receipt_status = FALSE;
						}

						$total_amount = (float)$trans['credit_cost'];
						$procedure_cost = (float)$trans['procedure_cost'];
						$treatment = (float)$trans->credit_cost;

						if((int)$trans['health_provider_done'] == 1) {
							$receipt_status = TRUE;
							$health_provider_status = TRUE;
							$payment_type = "Cash";
							$transaction_type = "cash";
							if((int)$trans['lite_plan_enabled'] == 1) {
							$treatment = 0;
							$logs_lite_plan = DB::table($table_wallet_history)
								->where('logs', 'deducted_from_mobile_payment')
								->where('lite_plan_enabled', 1)
								->where('id', $trans['transaction_id'])
								->first();

								if($logs_lite_plan) {
									// if($trans->default_currency == $trans->currency_type && $trans->default_currency == "myr") {
									// 	$total_amount = ($trans['credit_cost'] * $trans->currency_amount) + $logs_lite_plan->credit;
									// } else {
										$total_amount = (float)$trans['credit_cost'] + $logs_lite_plan->credit;
									// }
								} else {
									// if($trans->default_currency == $trans->currency_type && $trans->default_currency == "myr") {
									// 	$total_amount = $trans['consultation_fees'] * $trans->currency_amount;
									// } else {
										$total_amount = $trans['consultation_fees'];
									// }
								}
								$procedure_cost = (float)$trans['credit_cost'];
							}
						} else {
							$payment_type = "Mednefits Credits";
							$transaction_type = "credits";
							$health_provider_status = FALSE;
							$procedure_cost = $trans->credit_cost;
							if((int)$trans['lite_plan_enabled'] == 1) {
								$logs_lite_plan = DB::table($table_wallet_history)
									->where('logs', 'deducted_from_mobile_payment')
									->where('lite_plan_enabled', 1)
									->where('id', $trans['transaction_id'])
									->first();

								if($logs_lite_plan) {
									// if($trans->default_currency == $trans->currency_type && $trans->default_currency == "myr") {
									// 	$total_amount = ($trans['credit_cost'] * $trans->currency_amount) + $logs_lite_plan->credit;
									// 	$treatment = $trans->credit_cost * $trans->currency_amount;
									// } else {
										$total_amount = (float)$trans['credit_cost'] + $logs_lite_plan->credit;
										$treatment = (float)$trans->credit_cost;
									// }
								}
							}
						}

						// get clinic type
						$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
						$type = "";
						$clinic_type_name = "";
						if($clinic_type->head == 1 || $clinic_type->head == "1") {
							if($clinic_type->Name == "GP") {
								$type = "general_practitioner";
								$clinic_type_name = "GP";
								// $total_gp_medicine += $treatment;
								$total_gp_medicine = ($trans['consultation_fees'] + $treatment);
							} else if($clinic_type->Name == "Dental") {
								$type = "dental_care";
								$clinic_type_name = "Dental";
								$total_dental += $treatment;
							} else if($clinic_type->Name == "TCM") {
								$type = "tcm";
								$clinic_type_name = "TCM";
								$total_tcm += $treatment;
							} else if($clinic_type->Name == "Screening") {
								$type = "health_screening";
								$clinic_type_name = "Screening";
							} else if($clinic_type->Name == "Wellness") {
								$type = "wellness";
								$clinic_type_name = "Wellness";
							} else if($clinic_type->Name == "Specialist") {
								$type = "health_specialist";
								$clinic_type_name = "Specialist";
							}
						} else {
							$find_head = DB::table('clinic_types')
										->where('ClinicTypeID', $clinic_type->sub_id)
										->first();
							if($find_head->Name == "GP") {
								$type = "general_practitioner";
								$clinic_type_name = "GP";
								$total_gp_medicine += $treatment;
							} else if($find_head->Name == "Dental") {
								$type = "dental_care";
								$clinic_type_name = "Dental";
								$total_dental += $treatment;
							} else if($find_head->Name == "TCM") {
								$type = "tcm";
								$clinic_type_name = "TCM";
								$total_tcm += $treatment;
							} else if($find_head->Name == "Screening") {
								$type = "health_screening";
								$clinic_type_name = "Screening";
							} else if($find_head->Name == "Wellness") {
								$type = "wellness";
								$clinic_type_name = "Wellness";
							} else if($find_head->Name == "Specialist") {
								$type = "health_specialist";
								$clinic_type_name = "Specialist";
							}
						}

						// check user if it is spouse or dependent
						if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
							$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
							$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
							$sub_account = ucwords($temp_account->Name);
							$sub_account_type = $temp_sub->user_type;
							$owner_id = $temp_sub->owner_id;
							$employee = ucwords($temp_account->Name);
							$dependent = ucwords($customer->Name);
						} else {
							$sub_account = FALSE;
							$sub_account_type = FALSE;
							$owner_id = $customer->UserID;
							$employee = ucwords($customer->Name);
							$dependent = null;
						}

						if($trans->default_currency == "myr" && $trans->currency_type == "sgd" || $trans->default_currency == "myr" && $trans->currency_type == "myr") {
							// $trans['consultation_fees'] = $trans['consultation_fees'] * $trans->currency_amount;
							$procedure_cost = $procedure_cost * $trans->currency_amount;
							$consultation_credits = $consultation_credits * $trans->currency_amount;
							// $consultation = $consultation * $trans->currency_amount;
							$trans->cap_per_visit = $trans->cap_per_visit * $trans->currency_amount;
							$trans->cash_cost = (float)$trans->cash_cost * $trans->currency_amount;
							$trans->credit_cost = (float)$trans->credit_cost * $trans->currency_amount;
							$total_amount = $total_amount * $trans->currency_amount;
							$cash = $cash * $trans->currency_amount;
							$mednefits_credits = $mednefits_credits * $trans->currency_amount;
							$treatment = $treatment * $trans->currency_amount;
							$trans->default_currency = "myr";
							$trans['consultation_fees'] = $trans['consultation_fees'] * $trans->currency_amount;
						}

						// check for
						// if( strpos( strtolower($procedure), 'medicine' ) !== false ) $total_gp_medicine += $treatment;

						$transaction_id = str_pad($trans['transaction_id'], 6, "0", STR_PAD_LEFT);
						$format = array(
							'clinic_name'       => $clinic->Name,
							'amount'            => number_format($trans['consultation_fees'] + $treatment , 2),
							'total_amount'            => number_format($trans['consultation_fees'] + $treatment , 2),
							'amount_number'            => $trans['consultation_fees'] + $treatment,
							'procedure_cost'	=> number_format((float)$procedure_cost, 2),
							'clinic_type_and_service' => $clinic_name,
							'clinic_type_name'	=> $clinic_type_name,
							'date_of_transaction' => date('d F Y, h:ia', strtotime($trans['date_of_transaction'])),
							'member'            => ucwords($customer->Name),
							'employee'					=> $employee,
							'dependent'					=> $dependent,
							'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
							'receipt_status'    => $receipt_status,
							'health_provider_status' => $health_provider_status,
							'user_id'           => $trans['UserID'],
							'type'              => 'In-Network',
							'month'             => date('M', strtotime($trans['date_of_transaction'])),
							'day'               => date('d', strtotime($trans['date_of_transaction'])),
							'time'              => date('h:ia', strtotime($trans['date_of_transaction'])),
							'clinic_type'       => $type,
							'owner_account'     => $sub_account,
							'owner_id'          => $owner_id,
							'sub_account_user_type' => $sub_account_type,
							'co_paid'           => $trans['co_paid_amount'],
							'payment_type'      => $payment_type,
							'nric'							=> $customer->NRIC,
							'mednefits_credits'			=> number_format($mednefits_credits, 2),
							'cash'									=> number_format($cash, 2),
							'consultation_credits' => number_format($consultation_credits, 2),
							'consultation'		=> (int)$trans['lite_plan_enabled'] == 1 ? number_format($trans['consultation_fees'], 2) : "0.00",
							'service_credits'   => $service_credits,
							'transaction_type'  => $transaction_type,
							'treatment'			=> number_format($treatment, 2),
							'treatment_number'			=> $treatment,
							'currency_type'	=> strtoupper($trans->default_currency)
						);

						array_push($transaction_details, $format);
					}
				}
			}
		}

		$total_gp_medicine = 0;
		if($fields == true) {
			// return $transaction_details;
			usort($transaction_details, function($a, $b) {
				return strtotime($b['date_of_transaction']) - strtotime($a['date_of_transaction']);
			});
		}

		foreach($transaction_details as $gp) {
			if($gp['clinic_type_name'] == "GP") {
				$total_gp_medicine += $gp['treatment_number'];
			}
		}
		// $total_gp_medicine = $transaction_details->options()->where('clinic_type_name', 'GP')->sum('treatment_number');
		// $total_dental = collect($transaction_details)->where('clinic_type_name', 'Dental')->sum('amount_number'); 
		// $total_tcm = collect($transaction_details)->where('clinic_type_name', 'TCM')->sum('amount_number'); 

		if($statement && $statement->payment_method == "bank_transfer" || $statement && $statement->payment_method == "giro") {
			$total_post_paid_spent += $total_consultation;
		} else {
			$total_pre_paid_spent += $total_consultation;
		}
		
		return array(
			'credits' 				=> $in_network_transactions,
			'total_consultation'	=> $total_consultation, 
			'total_gp_medicine'		=> $total_gp_medicine,
			'total_gp_consultation'	=> $total_gp_consultation,
			'total_dental'			=> $total_dental,
			'total_tcm'				=> $total_tcm,
			'transactions' 			=> $transaction_details,
			'lite_plan'				=> $lite_plan,
			'total_transactions'	=> $total_transactions,
			'total_post_paid_spent' => $total_post_paid_spent,
			'total_pre_paid_spent'	=> $total_pre_paid_spent,
			'with_post_paid'		=> $with_post_paid,
			'total_amount' 			=> sum([
				$in_network_transactions,
				$total_consultation
			])
		);
	}

	public static function getEclaims($customer_id, $start, $end)
	{
		$account = DB::table('customer_link_customer_buy')
		->where('customer_buy_start_id', $customer_id)
		->first();

		$corporate_members = DB::table('corporate_members')
		->where('corporate_id', $account->corporate_id)
		->get();
		$total_e_claim_spent = 0;
		$e_claim = [];
		foreach ($corporate_members as $key => $member) {
			$ids = StringHelper::getSubAccountsID($member->user_id);
	            // get e claim
			$e_claim_result = DB::table('e_claim')
			->whereIn('user_id', $ids)
			->where('created_at', '>=', $start)
			->where('created_at', '<=', $end)
			->where('status', 1)
			->orderBy('created_at', 'desc')
			->get();

	           	 // e-claim transactions
			foreach($e_claim_result as $key => $res) {
				$status_text = 'Approved';
				if($res->spending_type == 'medical') {
					$table_wallet_history = 'wallet_history';
				} else {
					$table_wallet_history = 'wellness_wallet_history';
				}
				$history = DB::table($table_wallet_history)
								->where('logs', 'deducted_from_e_claim')
								->where('where_spend', 'e_claim_transaction')
								->where('id', $res->e_claim_id)
								->first();

				if($history) {
					$total_e_claim_spent += $history->credit;
					$res->amount = $history->credit;
				} else {
					$total_e_claim_spent += $res->amount;
					$res->amount = $history->credit;
				}

				$member = DB::table('user')->where('UserID', $res->user_id)->first();

	      		// check user if it is spouse or dependent
				if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
					$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
					$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
					$sub_account = ucwords($temp_account->Name);
					$sub_account_type = $temp_sub->user_type;
					$owner_id = $temp_sub->owner_id;
					$dependent_relationship = $temp_sub->relationship ? ucwords($temp_sub->relationship) : 'Dependent';
					$relationship = FALSE;
					$bank_account_number = $temp_account->bank_account;
					$bank_name = $temp_account->bank_name;
					$bank_code = $temp_account->bank_code;
					$bank_brh = $temp_account->bank_brh;
				} else {
					$sub_account = FALSE;
					$sub_account_type = FALSE;
					$owner_id = $member->UserID;
					$dependent_relationship = FALSE;
					$bank_account_number = $member->bank_account;
					$bank_name = $member->bank_name;
					$bank_code = $member->bank_code;
					$bank_brh = $member->bank_brh;
				}

				$docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();

				if(sizeof($docs) > 0) {
					$e_claim_receipt_status = TRUE;
					$doc_files = [];
					foreach ($docs as $key => $doc) {
						if($doc->file_type == "pdf" || $doc->file_type == "xls") {
												// if(StringHelper::Deployment()==1){
												// 	$fil = 'https://s3-ap-southeast-1.amazonaws.com/mednefits/receipts/'.$doc->doc_file;
												// } else {
												// 	$fil = url('').'/receipts/'.$doc->doc_file;
												// }
							$fil = EclaimHelper::createPreSignedUrl($doc->doc_file);
							$image_link = null;
						} else if($doc->file_type == "image") {
							$fil = $doc->doc_file;
							$image_link = FileHelper::formatImageAutoQualityCustomer($fil, 40);
						}

						$temp_doc = array(
							'e_claim_doc_id'    => $doc->e_claim_doc_id,
							'e_claim_id'            => $doc->e_claim_id,
							'file'                      => $fil,
							'file_type'             => $doc->file_type,
							'image_link'	 	=> $image_link
						);

						array_push($doc_files, $temp_doc);
					}
				} else {
					$e_claim_receipt_status = FALSE;
					$doc_files = FALSE;
				}

				$id = str_pad($res->e_claim_id, 6, "0", STR_PAD_LEFT);

				if($res->currency_type == "myr" && $res->default_currency == "myr") {
					$res->default_currency = "MYR";
				} else if($res->default_currency == "myr"){
					$res->default_currency = "MYR";
					// $res->amount = $res->amount;
				} else {
					$res->default_currency = "SGD";
				}

				$temp = array(
					'status'            => $res->status,
					'status_text'       => $status_text,
					'claim_date'        => date('d F Y', strtotime($res->created_at)),
					'approved_date'        => date('d F Y', strtotime($res->approved_date)),
					'time'              => $res->time,
					'service'           => $res->service,
					'merchant'          => $res->merchant,
					'amount'            => number_format($res->amount, 2),
					'member'            => ucwords($member->Name),
					'employee_dependent_name' => $sub_account ? $sub_account : null,
					'claim_member_type'       => $dependent_relationship ? 'DEPENDENT' : 'EMPLOYEE',
					'type'              => 'E-Claim',
					'transaction_id'    => 'MNF'.$id,
					'visit_date'        => date('d F Y', strtotime($res->date)).', '.$res->time,
					'owner_id'          => $owner_id,
					'sub_account_type'  => $sub_account_type,
					'sub_account'       => $sub_account,
					'month'             => date('M', strtotime($res->approved_date)),
					'day'               => date('d', strtotime($res->approved_date)),
					'approved_time'              => date('h:ia', strtotime($res->approved_date)),
					'spending_type'     => $res->spending_type,
					'dependent_relationship'	=> $dependent_relationship,
					'bank_account_number' => $bank_account_number,
					'files'             => $doc_files,
					'receipt_status'    => $e_claim_receipt_status,
					'bank_name'					=> $bank_name,
					'bank_code'					=> $bank_code,
					'bank_brh'					=> $bank_brh,
					'nric'							=> $member->NRIC,
					'currency_type'			=> $res->default_currency
				);

				array_push($e_claim, $temp);
			}
		}

		if(sizeof($e_claim) > 0) {
		         // sort in-network transaction
			usort($e_claim, function($a, $b) {
				return strtotime($b['claim_date']) - strtotime($a['claim_date']);
			});
		}


		return array(
			'total_e_claim_spent'       => $total_e_claim_spent,
			'e_claim_transactions'      => $e_claim,
		);
	}

	public static function getInvoiceSpending($invoice_id, $fields)
	{
		$data = CompanyCreditsStatement::where('statement_id', $invoice_id)
		->first();
		$lite_plan = false;
		$results = self::getTotalCreditsInNetworkTransactions($data->statement_id, $data->statement_customer_id, $fields);
		$consultation_amount_due = 0;
		$company_details = DB::table('customer_business_information')->where('customer_buy_start_id', $data->statement_customer_id)->first();
		if((int)$data->lite_plan == 1) {
			$lite_plan = true;
		} else if($results['lite_plan'] == true) {
			$lite_plan = true;
		}

		if($lite_plan == true) {
			$consultation_amount_due = DB::table('transaction_history')
			->join('statement_in_network_transactions', 'statement_in_network_transactions.transaction_id', '=', 'transaction_history.transaction_id')
			->where('statement_id', $data->statement_id)
			->where('statement_in_network_transactions.status', 0)
			->where('transaction_history.deleted', 0)
			->where('transaction_history.paid', 1)
			->where('transaction_history.lite_plan_enabled', 1)
			->sum('transaction_history.consultation_fees');
		}

		if((int)$data->statement_status == 1) {
			$amount_due = $data->paid_amount - $results['credits'] + $results['total_consultation'];
		} else {
			$amount_due = $results['credits'] + $results['total_consultation'];
		}

		return array(
			'company' => ucwords($data->statement_company_name),
			'company_address' => ucwords($data->statement_company_address),
			'postal'		=> $company_details->postal_code,
			'building_name'		=> $company_details->building_name,
			'unit_number'		=> $company_details->unit_number,
			'statement_contact_email' => $data->statement_contact_email,
			'statement_contact_name' => ucwords($data->statement_contact_name),
			'statement_contact_number' => $data->statement_contact_number,
			'customer_id' => $data->statement_customer_id,
			'statement_date' => date('j M Y', strtotime($data->statement_date)),
			'statement_due' => date('j M Y', strtotime($data->statement_due)),
			'statement_start_date' => date('d F', strtotime($data->statement_start_date)),
			'statement_end_date'	=> date('d F', strtotime($data->statement_end_date)),
			'start_date' => date('j M', strtotime($data->statement_start_date)),
			'end_date'	=> date('j M Y', strtotime($data->statement_end_date)),
			'period'			=> date('d-m-Y', strtotime($data->statement_start_date)).' - '.date('d-m-Y', strtotime($data->statement_end_date)),
			'statement_id'	=> $data->statement_id,
			'statement_number' => $data->statement_number,
			'statement_status'	=> $data->statement_status,
			'statement_total_amount' => DecimalHelper::formatDecimal($results['credits'] + $results['total_consultation']),
			'total_amount' => DecimalHelper::formatDecimal($results['credits'] + $results['total_consultation']),
			'total_in_network_amount'		=> $results['credits'],
			'statement_amount_due' => DecimalHelper::formatDecimal($amount_due),
			'consultation_amount_due'	=> $consultation_amount_due,
			'in_network'				=> $results['transactions'],
			'paid_date'				=> $data->paid_date ? date('j M Y', strtotime($data->paid_date)) : NULL,
			'payment_remarks' => $data->payment_remarks,
			'payment_amount' => DecimalHelper::formatDecimal($data->paid_amount),
			'lite_plan'	=> $lite_plan,
			'total_consultation'	=> DecimalHelper::formatDecimal($results['total_consultation']),
			'total_gp_medicine'		=> DecimalHelper::formatDecimal($results['total_gp_medicine']),
			'total_gp_consultation'	=> DecimalHelper::formatDecimal($results['total_gp_consultation']),
			'total_dental'			=> DecimalHelper::formatDecimal($results['total_dental']),
			'total_tcm'				=> DecimalHelper::formatDecimal($results['total_tcm']),
			'total_transactions'	=> $results['total_transactions'],
			'currency_type'	=> $data->currency_type
		);
	}

	public static function checkSpendingInvoiceNewTransactions($customer_id, $start, $end, $invoice_id, $plan_method)
	{
		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
		$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();
		// $spending = CustomerHelper::getAccountSpendingBasicPlanStatus($customer_id);
		// $transactions_data = [];
		// $array_of_users = [];
		$lite_plan = false;
		// $lite_plan = StringHelper::liteCompanyPlanStatus($customer_id);
		$transactions = [];

		// if($spending['medical_method'] == "pre_paid" && $plan_method == "post_paid")	{
		// 	foreach ($corporate_members as $key => $member) {
		// 		$ids = StringHelper::getSubAccountsID($member->user_id);
		// 		$in_network = DB::table('transaction_history')
		// 		->whereIn('UserID', $ids)
		// 		->where('lite_plan_enabled', 1)
		// 		->where('credit_cost', 0)
		// 		->where('deleted', 0)
		// 		->where('paid', 1)
		// 		->where('created_at', '>=', $start)
		// 		->where('created_at', '<=', $end)
		// 		->orderBy('created_at', 'desc')
		// 		->get();
	
		// 		foreach ($in_network as $key => $trans) {
		// 			if($trans->spending_type == 'medical') {
		// 				$table_wallet_history = 'wallet_history';
		// 			} else {
		// 				$table_wallet_history = 'wellness_wallet_history';
		// 			}
		// 			$logs_lite_plan = DB::table($table_wallet_history)
		// 				->where('logs', 'deducted_from_mobile_payment')
		// 				->where('lite_plan_enabled', 1)
		// 				->where('id', $trans->transaction_id)
		// 				->first();

		// 			if(!$logs_lite_plan)	{
		// 				array_push($transactions, $trans->transaction_id);
		// 			}
		// 		}
		// 	}
		// } else {
			foreach ($corporate_members as $key => $member) {
				$ids = StringHelper::getSubAccountsID($member->user_id);
				$temp_trans_lite_plan = DB::table('transaction_history')
				->whereIn('UserID', $ids)
				->where('lite_plan_enabled', 1)
				->where('deleted', 0)
				->where('paid', 1)
				->where('created_at', '>=', $start)
				->where('created_at', '<=', $end)
				->orderBy('created_at', 'desc')
				->get();
	
				$temp_trans = DB::table('transaction_history')
				->whereIn('UserID', $ids)
				->where('credit_cost', '>', 0)
				->where('deleted', 0)
				->where('paid', 1)
				->where('created_at', '>=', $start)
				->where('created_at', '<=', $end)
				->orderBy('created_at', 'desc')
				->get();
				$transactions_temp = array_merge($temp_trans_lite_plan, $temp_trans);
				$in_network = self::my_array_unique($transactions_temp);
	
				foreach ($in_network as $key => $trans) {
					array_push($transactions, $trans->transaction_id);
				}
			}
		// }
		
		foreach ($transactions as $key => $trans) {
			$check_transaction = \SpendingInvoiceTransactions::where('transaction_id',  $trans)->first();

			if(!$check_transaction) {
        		// insert to spending invoice transaction
				\SpendingInvoiceTransactions::create(['invoice_id' => $invoice_id, 'transaction_id' => $trans]);
			}
		}
	}

	public static function checkTotalCreditsInNetworkTransactions($customer_id, $start, $end)
	{
			// get all hr employees, spouse and dependents
		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();

		$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();
		$lite_plan = StringHelper::liteCompanyPlanStatus($customer_id);
		$total_transaction_spent = 0;
		$total_consultation = 0;

		foreach ($corporate_members as $key => $member) {
			$ids = StringHelper::getSubAccountsID($member->user_id);

	            // if($lite_plan) {
			$temp_trans_lite_plan = DB::table('transaction_history')
			->whereIn('UserID', $ids)
	                                // ->where('in_network', 1)
			->where('lite_plan_enabled', 1)
			->where('deleted', 0)
			->where('paid', 1)
			->where('created_at', '>=', $start)
			->where('created_at', '<=', $end)
			->orderBy('created_at', 'desc')
			->get();

			$temp_trans = DB::table('transaction_history')
			->whereIn('UserID', $ids)
	                                // ->where('in_network', 1)
			->where('credit_cost', '>', 0)
			->where('deleted', 0)
			->where('paid', 1)
			->where('created_at', '>=', $start)
			->where('created_at', '<=', $end)
			->orderBy('created_at', 'desc')
			->get();
			$transactions_temp = array_merge($temp_trans_lite_plan, $temp_trans);
			$transactions = self::my_array_unique($transactions_temp);
	            // } else {
	            //     // get in-network transactions
	            //     $transactions = DB::table('transaction_history')
	            //                     ->whereIn('UserID', $ids)
	            //                     // ->where('in_network', 1)
	            //                     ->where('health_provider_done', 0)
	            //                     ->where('deleted', 0)
	            //                     ->where('created_at', '>=', $start)
	            //                     ->where('created_at', '<=', $end)
	            //                     ->orderBy('created_at', 'desc')
	            //                     ->get();

	            // }

	            // return $transactions;

	            // in-network transactions
			foreach ($transactions as $key => $trans) {
				if($trans) {
					$total_transaction_spent += $trans->credit_cost;
					if($trans->lite_plan_enabled == 1) {

						if($trans->spending_type == 'medical') {
							$table_wallet_history = 'wallet_history';
						} else {
							$table_wallet_history = 'wellness_wallet_history';
						}

						if($trans->lite_plan_enabled == 1) {
							$logs_lite_plan = DB::table($table_wallet_history)
							->where('logs', 'deducted_from_mobile_payment')
							->where('lite_plan_enabled', 1)
							->where('id', $trans->transaction_id)
							->first();

							if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits == 0) {
								$total_consultation += floatval($logs_lite_plan->credit);
								$consultation_credits = true;
								$service_credits = true;
							} else if($logs_lite_plan && $trans->procedure_cost >= 0 && (int)$trans->lite_plan_use_credits == 1){
								$total_consultation += floatval($logs_lite_plan->credit);
								$consultation_credits = true;
								$service_credits = true;
							} else if($trans->procedure_cost >= 0 && (int)$trans->lite_plan_use_credits == 0){
								$total_consultation += floatval($trans->consultation_fees);
							}
						}
					}
				}
			}
		}

		return array(
			'total_credits'   => $total_transaction_spent,
			'total_consultation'        => $total_consultation
		);
	}

	public static function getNonPanelTransactionDetails($invoice_id, $customer_id, $fields)
	{
		$transactions = DB::table('statement_e_claim_transactions')->where('statement_id', $invoice_id)->get();
		$total_medical = 0;
		$total_wellness = 0;
		$transaction_data = array();
		$total_credits = 0;
		$total_post_paid_spent = 0;
		$with_post_paid = false;
		$total_pre_paid_spent = 0;

		foreach($transactions as $key => $transaction) {
			$e_claim = DB::table('e_claim')
						->where('e_claim_id', $transaction->e_claim_id)
						->where('status', 1)
						->first();

			if($e_claim) {
				$spending_method = "post_paid";
				if($e_claim->spending_type == "medical") {
					$table_wallet_history = 'wallet_history';
				} else {
					$table_wallet_history = 'wellness_wallet_history';
				}
	
				$logs = DB::table($table_wallet_history)
						->where('where_spend', 'e_claim_transaction')
						->where('id',  $e_claim->e_claim_id)
						->first();
				// if($logs && $logs->spending_method == "post_paid") {
					if($logs) {
						$spending_method = $logs->spending_method;
						$credits = $logs->credit;
						$e_claim->amount = $logs->credit;
						if($logs->spending_method == "post_paid") {
							$total_post_paid_spent += $credits;
							$with_post_paid = true;
						} else {
							$total_pre_paid_spent += $credits;
						}
					} else {
						$credits = $e_claim->amount;
						$e_claim->amount = $logs->credit;
					}
	
					if($e_claim->spending_type == "medical") {
						$total_medical += $credits;
					} else {
						$total_wellness += $credits;
					}
	
					$member = DB::table('user')->where('UserID', $e_claim->user_id)->first();
	
					  // check user if it is spouse or dependent
					$claim_member_type = "Employee";
					$employee_name = $member->Name;
					if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
						$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
						$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
						$sub_account = ucwords($temp_account->Name);
						$sub_account_type = $temp_sub->user_type;
						$owner_id = $temp_sub->owner_id;
						$dependent_relationship = $temp_sub->relationship ? ucwords($temp_sub->relationship) : 'Dependent';
						$relationship = FALSE;
						$bank_account_number = $temp_account->bank_account;
						$bank_name = $temp_account->bank_name;
						$bank_code = $temp_account->bank_code;
						$bank_brh = $temp_account->bank_brh;
						$claim_member_type = "Dependent";
					} else {
						$sub_account = FALSE;
						$sub_account_type = FALSE;
						$owner_id = $member->UserID;
						$dependent_relationship = FALSE;
						$bank_account_number = $member->bank_account;
						$bank_name = $member->bank_name;
						$bank_code = $member->bank_code;
						$bank_brh = $member->bank_brh;
					}
	
					$id = str_pad($e_claim->e_claim_id, 6, "0", STR_PAD_LEFT);
	
					if($e_claim->currency_type == "myr" && $e_claim->default_currency == "myr") {
						$e_claim->default_currency = "MYR";
					} else if($e_claim->default_currency == "myr"){
						$e_claim->default_currency = "MYR";
					} else {
						$e_claim->default_currency = "SGD";
					}
	
					$temp_docs = DB::table('e_claim_docs')
						  ->where('e_claim_id', $e_claim->e_claim_id)
						  ->get();
						  
					foreach ($temp_docs as $key => $doc) {
						if($doc->file_type == "pdf" || $doc->file_type == "xls" || $doc->file_type == "xlsx") {
							$doc->file = 'https://s3-ap-southeast-1.amazonaws.com/mednefits/receipts/'.$doc->doc_file;
						} else if($doc->file_type == "image") {
							$doc->file = $doc->doc_file;
						}
					}
	
					$temp = array(
						'status'            => $e_claim->status,
						'status_text'       => 'Approved',
						'claim_date'        => date('d F Y h:ia', strtotime($e_claim->created_at)),
						'approved_date'        => date('d F Y', strtotime($e_claim->approved_date)),
						'time'              => $e_claim->time,
						'service'           => $e_claim->service,
						'merchant'          => $e_claim->merchant,
						'claim_amount'		=> $e_claim->claim_amount,
						'amount'            => number_format($e_claim->amount, 2),
						'member'            => ucwords($member->Name),
						'employee_dependent_name' => $sub_account ? $sub_account : null,
						'claim_member_type'       => $dependent_relationship ? 'DEPENDENT' : 'EMPLOYEE',
						'type'              => 'E-Claim',
						'transaction_id'    => 'MNF'.$id,
						'visit_date'        => date('d F Y', strtotime($e_claim->date)).', '.$e_claim->time,
						'owner_id'          => $owner_id,
						'sub_account_type'  => $sub_account_type,
						'sub_account'       => $sub_account,
						'month'             => date('M', strtotime($e_claim->approved_date)),
						'day'               => date('d', strtotime($e_claim->approved_date)),
						'approved_time'              => date('h:ia', strtotime($e_claim->approved_date)),
						'spending_type'     => $e_claim->spending_type,
						'dependent_relationship'	=> $dependent_relationship,
						'bank_account_number' => $bank_account_number,
						'bank_name'			=> $bank_name,
						'bank_code'			=> $bank_code,
						'bank_brh'			=> $bank_brh,
						'nric'				=> $member->NRIC,
						'currency_type'		=> $e_claim->default_currency,
						'email_address'		=> $member->Email,
						'employee_name'		=> $employee_name,
						'remarks'			=> $e_claim->rejected_reason,
						'files'				=> $temp_docs,
						'spending_method'	=> $spending_method
					);
	
					array_push($transaction_data, $temp);
				// }
			}
		}

		$total_credits = $total_medical + $total_wellness;
		return ['total_pre_paid_spent' => $total_pre_paid_spent, 'with_post_paid' => $with_post_paid, 'total_post_paid_spent' => $total_post_paid_spent, 'total_medical' => $total_medical, 'total_wellness' => $total_wellness, 'total_credits' => $total_credits, 'transactions' => $transaction_data, 'total_consultation' => 0, 'credits' => $total_credits, 'lite_plan' => false];
	}
}
?>