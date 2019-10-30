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

		public static function checkCompanyTransactions($customer_id, $start, $end)
		{
			$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();

	        $corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();

	        $transactions_data = [];
	        $array_of_users = [];
	        $lite_plan = false;
	        $lite_plan = StringHelper::liteCompanyPlanStatus($customer_id);
	        $transactions = 0;
	        foreach ($corporate_members as $key => $member) {
	           $ids = StringHelper::getSubAccountsID($member->user_id);
	            // get e claim
	            if($lite_plan) {
	               $temp_trans_lite_plan = DB::table('transaction_history')
                                ->whereIn('UserID', $ids)
                                ->where('lite_plan_enabled', 1)
                                ->where('deleted', 0)
                                ->where('paid', 1)
                                ->where('date_of_transaction', '>=', $start)
                                ->where('date_of_transaction', '<=', $end)
                                ->orderBy('created_at', 'desc')
                                ->count();

	                $temp_trans = DB::table('transaction_history')
	                            ->whereIn('UserID', $ids)
	                            ->where('health_provider_done', 0)
	                            ->where('deleted', 0)
	                            ->where('paid', 1)
	                            ->where('date_of_transaction', '>=', $start)
	                            ->where('date_of_transaction', '<=', $end)
	                            ->orderBy('created_at', 'desc')
	                            ->count();
	                $transactions += $temp_trans_lite_plan + $temp_trans;
	            } else {
	                // get in-network transactions
	                $in_network_temp = DB::table('transaction_history')
		                        ->whereIn('UserID', $ids)
		                        ->where('health_provider_done', 0)
                            	->where('deleted', 0)
                            	->where('paid', 1)
		                        ->where('date_of_transaction', '>=', $start)
		                        ->where('date_of_transaction', '<=', $end)
		                        ->count();
	                $transactions += $in_network_temp;
	            }
	        }

	        if($transactions == 0) {
                return FALSE;
            } else {
                return TRUE;
            }
		}

		public static function createStatement($customer_id, $start, $end)
		{
			$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
			$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();
	        $corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();

	        $lite_plan = false;
	        $lite_plan = StringHelper::liteCompanyPlanStatus($customer_id);

	        $business_contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $customer_id)->first();
	        $billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $customer_id)->first();

	        $total_e_claim_amount = 0;
	        $total_in_network_amount = 0;
	        $transactions = [];

	        foreach ($corporate_members as $key => $member) {
	            $ids = StringHelper::getSubAccountsID($member->user_id);

	            // if($lite_plan) {
	                $temp_trans_lite_plan = DB::table('transaction_history')
	                                ->whereIn('UserID', $ids)
	                                // ->where('mobile', 1)
	                                // ->where('in_network', 1)
	                                ->where('lite_plan_enabled', 1)
	                                // ->where('health_provider_done', 0)
	                                ->where('deleted', 0)
	                                ->where('paid', 1)
	                                ->where('created_at', '>=', $start)
	                                ->where('created_at', '<=', $end)
	                                ->orderBy('created_at', 'desc')
	                                ->get();

	                $temp_trans = DB::table('transaction_history')
	                                ->whereIn('UserID', $ids)
	                                // ->where('mobile', 1)
	                                // ->where('in_network', 1)
	                                // ->where('health_provider_done', 0)
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
	            // } else {
	            //     $in_network = DB::table('transaction_history')
	            //                     ->whereIn('UserID', $ids)
	            //                     ->where('mobile', 1)
	            //                     ->where('in_network', 1)
	            //                     ->where('health_provider_done', 0)
	            //                     ->where('deleted', 0)
	            //                     ->where('paid', 1)
	            //                     ->where('date_of_transaction', '>=', $start)
	            //                     ->where('date_of_transaction', '<=', $end)
	            //                     ->orderBy('created_at', 'desc')
	            //                     ->get();
	                
	            // }

	            $e_claim = DB::table('e_claim')
	                            ->where('status', 1)
	                            ->whereIn('user_id', $ids)
	                            ->where('date', '>=', $start)
	                            ->where('date', '<=', $end)
	                            ->orderBy('created_at', 'desc')
	                            ->get();

	            foreach($e_claim as $key => $res) {
	                $total_e_claim_amount += $res->amount;
	            }

	            foreach ($in_network as $key => $trans) {
	                $total_in_network_amount += $trans->credit_cost;
	                array_push($transactions, $trans->transaction_id);
	            }
	        }

	        $company_details = DB::table('customer_business_information')->where('customer_buy_start_id', $customer_id)->first();

	        $statement = DB::table('company_credits_statement')->count();

	        $number = str_pad($statement + 1, 8, "0", STR_PAD_LEFT);

	        $spending_invoice_day = $customer->spending_default_invoice_day;
	        $day = date('t', strtotime('+1 month', strtotime($start)));
	        // return $day;
	        if((int)$spending_invoice_day == 31) {
	        	if((int)$spending_invoice_day > (int)$day) {
	        		$statement_date = date('Y-m-'.$day, strtotime('+1 month', strtotime($start)));
	        	} else {
	        		$statement_date = date('Y-m-'.$spending_invoice_day, strtotime('+1 month', strtotime($start)));
	        	}
	        } else {
		        $statement_date = date('Y-m-'.$spending_invoice_day, strtotime('+1 month', strtotime($start)));
	        }

		    $statement_due = date('Y-m-d', strtotime('+15 days', strtotime($statement_date)));
	        $statement_data = array(
	            'statement_customer_id'     => $customer_id,
	            'statement_number'          => 'MC'.$number,
	            'statement_date'            => date('Y-m-d', strtotime($statement_date)),
	            'statement_due'             => date('Y-m-d', strtotime($statement_due)),
	            'statement_start_date'      => $start,
	            'statement_end_date'        => $end,
	            'statement_contact_name'    => $billing_contact->first_name.' '.$billing_contact->last_name,
	            'statement_contact_number'  => $billing_contact->phone,
	            'statement_contact_email'   => $billing_contact->billing_email,
	            'statement_in_network_amount'   => $total_in_network_amount,
	            'statement_e_claim_amount'       => $total_e_claim_amount
	        );

	        if($lite_plan) {
	            $statement_data['lite_plan'] = 1;
	        }

	        // create statement
	        $statement_class = new CompanyCreditsStatement( );
	        $statement_result = $statement_class->createCompanyCreditsStatement($statement_data);
	        $statement_id = $statement_result->id;
	        // return $statement_id
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
			$consultation_status = false;
			$lite_plan = false;
			$transaction_invoices = SpendingInvoiceTransactions::where('invoice_id', $invoice_id)->get();

			foreach ($transaction_invoices as $key => $transaction) {
				$mednefits_fee = 0;
				$consultation = 0;
				$trans = Transaction::where('transaction_id', $transaction->transaction_id)
							->where('deleted', 0)
							->where('paid', 1)
							->first();
							
				if($trans) {
		        	$consultation_cash = false;
	                $consultation_credits = false;
	                $service_cash = false;
	                $service_credits = false;

					if((int)$trans['deleted'] == 0) {
						$in_network_transactions += $trans['credit_cost'];

						if($trans['spending_type'] == 'medical') {
	                        $table_wallet_history = 'wallet_history';
	                    } else {
	                        $table_wallet_history = 'wellness_wallet_history';
	                    }

	                    if((int)$trans['lite_plan_enabled'] == 1) {
	                    	$lite_plan = true;
	                    	$consultation_status = true;
	                        $logs_lite_plan = DB::table($table_wallet_history)
	                        ->where('logs', 'deducted_from_mobile_payment')
	                        ->where('lite_plan_enabled', 1)
	                        ->where('id', $trans['transaction_id'])
	                        ->first();

	                        if($logs_lite_plan && floatval($trans['credit_cost']) > 0 && (int)$trans['lite_plan_use_credits'] == 0 || $logs_lite_plan && floatval($trans['credit_cost']) > 0 && (int)$trans['lite_plan_enabled'] == 1) {
	                            $total_consultation += floatval($logs_lite_plan->credit);
	                            $consultation = number_format($logs_lite_plan->credit, 2);
	                            $consultation_credits = true;
	                            $service_credits = true;
	                        } else if($logs_lite_plan && floatval($trans['procedure_cost']) >= 0 && (int)$trans['lite_plan_use_credits'] == 1){
	                            $total_consultation += floatval($logs_lite_plan->credit);
	                            $consultation = number_format($logs_lite_plan->credit, 2);
	                            $consultation_credits = true;
	                            $service_credits = true;
	                        } else if(floatval($trans['procedure_cost']) >= 0 && (int)$trans['lite_plan_use_credits'] == 0){
	                            $total_consultation += floatval($trans['consultation_fees']);
	                            $consultation = number_format($trans['consultation_fees'], 2);
	                        }
	                    }


						if($fields == true) {
							if($trans['credit_cost'] > 0) {
								$mednefits_credits = number_format((float)$trans['credit_cost'], 2);
								$cash = number_format(0, 2);
							} else {
								$mednefits_credits = number_format(0, 2);
								$cash = number_format((float)$trans['procedure_cost']);
							}

							$receipt_images = DB::table('user_image_receipt')->where('transaction_id', $trans['transaction_id'])->get();
							$clinic = DB::table('clinic')->where('ClinicID', $trans['ClinicID'])->first();
							$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
							$customer = DB::table('user')->where('UserID', $trans['UserID'])->first();
							$procedure_temp = "";
							$services = "";
							$procedure = "";
							// get services
							if((int)$trans['multiple_service_selection'] == 1)
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
							$receipt = DB::table('user_image_receipt')->where('transaction_id', $trans['transaction_id'])->get();

							if(sizeof($receipt) > 0) {
							  $receipt_status = TRUE;
							  foreach ($receipt as $key => $doc) {
							  	if($doc->type == "image") {
							  		$doc->file = FileHelper::formatImageAutoQualityCustomer($doc->file, 40);
							  	}
							  }
							  $receipt_files = $receipt;
							} else {
							  $receipt_status = FALSE;
							  $receipt_files = FALSE;
							}

							$half_credits = false;
							$total_amount = number_format($trans['credit_cost'], 2);
							$procedure_cost = number_format($trans['procedure_cost'], 2);
							$treatment = number_format($trans->credit_cost, 2);
							// $consultation = 0;
							if((int)$trans['health_provider_done'] == 1) {
							  $receipt_status = TRUE;
							  $health_provider_status = TRUE;
							  $payment_type = "Cash";
							  $transaction_type = "cash";
							  	if((int)$trans['lite_plan_enabled'] == 1) {
		                        	$total_amount = number_format($trans['consultation_fees'], 2);
		                        	$procedure_cost = "0.00";
		                        	$treatment = 0;
                      				// $consultation = number_format($trans['co_paid_amount'], 2);
		                    	}
							} else {
							  // $payment_type = "Mednefits Credits";
							  $transaction_type = "credits";
							  $health_provider_status = FALSE;
							  $procedure_cost = number_format($trans->credit_cost, 2);
								if($trans->credit_cost > 0 && $trans->cash_cost > 0) {
								  $payment_type = 'Mednefits Credits + Cash';
								  $half_credits = true;
								} else {
								  $payment_type = 'Mednefits Credits';
								}

							  if((int)$trans['lite_plan_enabled'] == 1) {
		                        	$total_amount = number_format($trans['credit_cost'] + $trans['consultation_fees'], 2);
		                        	$treatment = number_format($trans->credit_cost, 2);
                       				// $consultation = number_format($trans->co_paid_amount, 2);
		                    	}
							}

							// get clinic type
							$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
							$type = "";
							$clinic_type_name = "";
							$type = "";
			                $clinic_type_name = "";
			                $image = "";
			                if($clinic_type->head == 1 || $clinic_type->head == "1") {
			                    if($clinic_type->Name == "General Practitioner") {
			                        $type = "general_practitioner";
			                        $clinic_type_name = "General Practitioner";
			                        $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
			                    } else if($clinic_type->Name == "Dental Care") {
			                        $type = "dental_care";
			                        $clinic_type_name = "Dental Care";
			                         $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
			                    } else if($clinic_type->Name == "Traditional Chinese Medicine") {
			                        $type = "tcm";
			                        $clinic_type_name = "Traditional Chinese Medicine";
			                        $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
			                    } else if($clinic_type->Name == "Health Screening") {
			                        $type = "health_screening";
			                        $clinic_type_name = "Health Screening";
			                        $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
			                    } else if($clinic_type->Name == "Wellness") {
			                        $type = "wellness";
			                        $clinic_type_name = "Wellness";
			                        $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
			                    } else if($clinic_type->Name == "Health Specialist") {
			                        $type = "health_specialist";
			                        $clinic_type_name = "Health Specialist";
			                        $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
			                    }
			                } else {
			                    $find_head = DB::table('clinic_types')
			                                ->where('ClinicTypeID', $clinic_type->sub_id)
			                                ->first();
			                    if($find_head->Name == "General Practitioner") {
			                        $type = "general_practitioner";
			                        $clinic_type_name = "General Practitioner";
			                        $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
			                    } else if($find_head->Name == "Dental Care") {
			                        $type = "dental_care";
			                        $clinic_type_name = "Dental Care";
			                        $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
			                    } else if($find_head->Name == "Traditional Chinese Medicine") {
			                        $type = "tcm";
			                        $clinic_type_name = "Traditional Chinese Medicine";
			                        $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
			                    } else if($find_head->Name == "Health Screening") {
			                        $type = "health_screening";
			                        $clinic_type_name = "Health Screening";
			                        $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
			                    } else if($find_head->Name == "Wellness") {
			                        $type = "wellness";
			                        $clinic_type_name = "Wellness";
			                        $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
			                    } else if($find_head->Name == "Health Specialist") {
			                        $type = "health_specialist";
			                        $clinic_type_name = "Health Specialist";
			                        $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
			                    }
			                }

							// check user if it is spouse or dependent
							if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
							  $temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
							  $temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
							  $sub_account = ucwords($temp_account->Name);
							  $sub_account_type = $temp_sub->user_type;
							  $owner_id = $temp_sub->owner_id;
							  $dependent_relationship = $temp_sub->relationship ? ucwords($temp_sub->relationship) : 'Dependent';
							} else {
							  $sub_account = FALSE;
							  $sub_account_type = FALSE;
							  $owner_id = $customer->UserID;
							  $dependent_relationship = FALSE;
							}


							$transaction_id = str_pad($trans['transaction_id'], 6, "0", STR_PAD_LEFT);
							$format = array(
								'clinic_name'       => $clinic->Name,
								'clinic_image'      => $clinic->image,
								'total_amount'      => $total_amount,
								'procedure_cost'	=> $procedure_cost,
								'clinic_type_and_service' => $clinic_name,
								'service'			=> $procedure,
								'date_of_transaction' => date('d F Y, h:ia', strtotime($trans['date_of_transaction'])),
								'member'            => ucwords($customer->Name),
								'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
								'receipt_status'    => $receipt_status,
								'receipt_files'      => $receipt_files,
								'health_provider_status' => $health_provider_status,
								'user_id'           => $trans['UserID'],
								'type'              => 'In-Network',
								'month'             => date('M', strtotime($trans['date_of_transaction'])),
								'day'               => date('d', strtotime($trans['date_of_transaction'])),
								'time'              => date('h:ia', strtotime($trans['date_of_transaction'])),
								'clinic_type'       => $type,
			                    'clinic_type_name'  => $clinic_type_name,
			                    'clinic_type_image' => $image,
								'owner_account'     => $sub_account,
								'owner_id'          => $owner_id,
								'sub_account_user_type' => $sub_account_type,
								'co_paid'           => $trans['co_paid_amount'],
								'payment_type'      => $payment_type,
								'nric'							=> $customer->NRIC,
								'mednefits_credits'			=> $mednefits_credits,
								'cash'									=> $cash,
								'consultation_credits' => $consultation_credits,
								'consultation'		=> number_format($consultation, 2),
								'service_credits'   => $service_credits,
								'transaction_type'  => $transaction_type,
								'treatment'			=> $treatment,
								'amount'			=> $treatment,
								'spending_type'		=> $trans->spending_type,
								'dependent_relationship'	=> $dependent_relationship,
								'lite_plan'			=> (int)$trans['lite_plan_enabled'] == 1 ? true : false,
								'cap_transaction'   => $half_credits,
							    'cap_per_visit'     => number_format($trans->cap_per_visit, 2),
							    'paid_by_cash'      => number_format($trans->cash_cost, 2),
							    'paid_by_credits'   => number_format($trans->credit_cost, 2),
							    "currency_symbol" 	=> $trans->currency_type == "myr" ? "RM" : "S$"
							);

							array_push($transaction_details, $format);

						}
					}

		        }
			}

			if($fields == true) {
				// return $transaction_details;
				usort($transaction_details, function($a, $b) {
					return strtotime($b['date_of_transaction']) - strtotime($a['date_of_transaction']);
				});
			}
			return array(
				'credits' => $in_network_transactions,
				'consultation_status'	=> $consultation_status,
				'total_consultation'	=> $total_consultation, 
				'transactions' => $transaction_details,
				'lite_plan'		=> $lite_plan
			);
		}

		public static function getEclaims($customer_id, $start, $end)
		{
			$account = DB::table('customer_link_customer_buy')
						->where('customer_buy_start_id', $customer_id)
						->first();

	        $corporate_members = DB::table('corporate_members')
	        					->where('corporate_id', $account->corporate_id
	        				)->get();
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
	                $total_e_claim_spent += $res->amount;

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

	                $temp = array(
	                    'status'            => $res->status,
	                    'status_text'       => $status_text,
	                    'claim_date'        => date('d F Y', strtotime($res->created_at)),
	                    'approved_date'        => date('d F Y', strtotime($res->approved_date)),
	                    'time'              => $res->time,
	                    'service'           => $res->service,
	                    'merchant'          => $res->merchant,
	                    'amount'            => $res->amount,
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
											'nric'							=> $member->NRIC
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
    		} else if($results['consultation_status'] == true) {
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
    			'company' => ucwords($company_details->company_name),
    			'company_address' => ucwords($company_details->company_address),
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
    			'period'			=> date('d F', strtotime($data->statement_start_date)).' - '.date('d F Y', strtotime($data->statement_end_date)),
    			'statement_id'	=> $data->statement_id,
    			'statement_number' => $data->statement_number,
    			'statement_status'	=> $data->statement_status,
    			'statement_total_amount' => number_format($results['credits'] + $results['total_consultation'], 2),
    			'total_in_network_amount'		=> $results['credits'],
    			'statement_amount_due' => number_format($amount_due, 2),
    			'consultation_amount_due'	=> $consultation_amount_due,
    			'in_network'				=> $results['transactions'],

    			'paid_date'				=> $data->paid_date ? date('j M Y', strtotime($data->paid_date)) : NULL,
    			'payment_remarks' => $data->payment_remarks,
    			'payment_amount' => number_format($data->paid_amount, 2),
    			'lite_plan'	=> $lite_plan,
    			'total_consultation'	=> $results['total_consultation']
    		);
		}

		public static function checkSpendingInvoiceNewTransactions($customer_id, $start, $end, $invoice_id)
		{
			$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();

	        $corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();

	        $transactions_data = [];
	        $array_of_users = [];
	        $lite_plan = false;
	        $lite_plan = StringHelper::liteCompanyPlanStatus($customer_id);

	        $transactions = [];

	        foreach ($corporate_members as $key => $member) {
	            $ids = StringHelper::getSubAccountsID($member->user_id);

	            // if($lite_plan) {
	                // $temp_trans_lite_plan = DB::table('transaction_history')
	                //                 ->whereIn('UserID', $ids)
	                //                 // ->where('mobile', 1)
	                //                 // ->where('in_network', 1)
	                //                 ->where('lite_plan_enabled', 1)
	                //                 // ->where('health_provider_done', 0)
	                //                 ->where('deleted', 0)
	                //                 ->where('paid', 1)
	                //                 ->where('created_at', '>=', $start)
	                //                 ->where('created_at', '<=', $end)
	                //                 ->orderBy('created_at', 'desc')
	                //                 ->get();

	                // $temp_trans = DB::table('transaction_history')
	                //                 ->whereIn('UserID', $ids)
	                //                 // ->where('mobile', 1)
	                //                 // ->where('in_network', 1)
	                //                 // ->where('health_provider_done', 0)
	                //                 ->where('credit_cost', '>', 0)
	                //                 ->where('lite_plan_enabled', 0)
	                //                 ->where('deleted', 0)
	                //                 ->where('paid', 1)
	                //                 ->where('created_at', '>=', $start)
	                //                 ->where('created_at', '<=', $end)
	                //                 ->orderBy('created_at', 'desc')
	                //                 ->get();
	                // $in_network = array_merge($temp_trans, $temp_trans_lite_plan);
	            // } else {
	            //     $in_network = DB::table('transaction_history')
	            //                     ->whereIn('UserID', $ids)
	            //                     ->where('mobile', 1)
	            //                     ->where('in_network', 1)
	            //                     ->where('health_provider_done', 0)
	            //                     ->where('deleted', 0)
	            //                     ->where('paid', 1)
	            //                     ->where('date_of_transaction', '>=', $start)
	            //                     ->where('date_of_transaction', '<=', $end)
	            //                     ->orderBy('created_at', 'desc')
	            //                     ->get();
	                
	            // }

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
	            $in_network = self::my_array_unique($transactions_temp);

	            foreach ($in_network as $key => $trans) {
	                array_push($transactions, $trans->transaction_id);
	            }
	        }

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
	}
?>