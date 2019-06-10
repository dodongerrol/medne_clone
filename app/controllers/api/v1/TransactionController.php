<?php
use Illuminate\Support\Facades\Input;

class Api_V1_TransactionController extends \BaseController
{
	public function payCredits( )
	{
		$AccessToken = new Api_V1_AccessTokenController();
		$returnObject = new stdClass();
		$authSession = new OauthSessions();
		$getRequestHeader = StringHelper::requestHeader();
		$input = Input::all();

		if(!empty($getRequestHeader['Authorization'])){
			$getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);

			if($getAccessToken){
				$findUserID = $authSession->findUserID($getAccessToken->session_id);

				if($findUserID){
					$email = [];
					// check if there is services ids
					if(!isset($input['services'])) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Please choose a service.';
						return Response::json($returnObject);
					} else if(sizeof($input['services']) == 0) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Please choose a service.';
						return Response::json($returnObject);
					}
					// check if clinic_id is present
					if(!isset($input['clinic_id'])) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Please choose a clinic.';
						return Response::json($returnObject);
					}
					// check if input amount is present
					if(!isset($input['input_amount'])) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Please enter an input amount.';
						return Response::json($returnObject);
					}

					$lite_plan_status = false;
					$clinic_peak_status = false;
					$currency = 3.00;
					$service_id = $input['services'][0];
					// check user type
					$type = StringHelper::checkUserType($findUserID);
					$lite_plan_status = StringHelper::newLitePlanStatus($findUserID);

					$user = DB::table('user')->where('UserID', $findUserID)->first();
					if((int)$type['user_type'] == 5 && (int)$type['access_type'] == 0 || (int)$type['user_type'] == 5 && (int)$type['access_type'] == 1)
					{
					   $user_id = $findUserID;
					   $customer_id = $findUserID;
					   $email_address = $user->Email;
					   $dependent_user = false;
					} else {
					                // find owner
					   $owner = DB::table('employee_family_coverage_sub_accounts')
					   ->where('user_id', $findUserID)
					   ->first();
					   $user_id = $owner->owner_id;
					   $user_email = DB::table('user')->where('UserID', $user_id)->first();
					   $email_address = $user_email->Email;
					   $customer_id = $findUserID;
					   $dependent_user = true;
					}

					// check block access
          $block = PlanHelper::checkCompanyBlockAccess($user_id, $input['clinic_id']);
					if($block) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Clinic not accessible to your Company. Please contact Your company for more information.';
						return Response::json($returnObject);
					}

					// get clinic info and type
					$clinic = DB::table('clinic')->where('ClinicID', $input['clinic_id'])->first();
					$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
					$consultation_fees = 0;

					// recalculate employee balance
         	PlanHelper::reCalculateEmployeeBalance($user_id);
					// check user credits and amount key in
					$spending_type = "medical";
					$wallet_user = DB::table('e_wallet')->where('UserID', $user_id)->first();

					if($clinic_type->spending_type == "medical") {
						$user_credits = TransactionHelper::floatvalue($wallet_user->balance);
						$spending_type = "medical";
					} else {
						$user_credits = TransactionHelper::floatvalue($wallet_user->wellness_balance);
						$spending_type = "wellness";
					}

					if($user_credits == 0) {
						$returnObject->status = FALSE;
            $returnObject->message = 'You have insufficient '.$spending_type.' credits in your account';
            $returnObject->sub_mesage = 'You may choose to pay directly to health provider.';
            return Response::json($returnObject);
					}

					$clinic_co_payment = TransactionHelper::getCoPayment($clinic, date('Y-m-d H:i:s'), $user_id);

					if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
						$consultation_fees = $clinic_co_payment['consultation_fees'] == 0 ? $clinic_data->consultation_fees : $clinic_co_payment['consultation_fees'];
					} else {
						$consultation_fees = 0;
					}

					if($clinic->currency_type == "myr") {
						$input_amount = TransactionHelper::floatvalue($input['input_amount']) + TransactionHelper::floatvalue($consultation_fees * 3);
					} else {
						$input_amount = TransactionHelper::floatvalue($input['input_amount']) + TransactionHelper::floatvalue($consultation_fees);
					}

					// check for lite plan
					if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
						if($consultation_fees > $user_credits) {
							$returnObject->status = FALSE;
	            $returnObject->message = 'You have insufficient '.$spending_type.' credits in your account for consultation fee credit deduction.';
	            $returnObject->sub_mesage = 'You may choose to pay directly to health provider.';
	            return Response::json($returnObject);
						}
					}

					if($clinic->currency_type == "myr") {
					  $total_amount = $input_amount / 3;
					} else {
					  $total_amount = $input_amount;
					}

					// return $total_amount;
					// get details for clinic co paid
					$clinic_co_payment = TransactionHelper::getCoPayment($clinic, date('Y-m-d H:i:s'), $user_id);
					$peak_amount = $clinic_co_payment['peak_amount'];
					$co_paid_amount = $clinic_co_payment['co_paid_amount'];
					$co_paid_status = $clinic_co_payment['co_paid_status'];

					// check if user has a plan tier
					$plan_tier = PlanHelper::getEmployeePlanTier($customer_id, $user_id);
					$cap_amount = 0;
					if($plan_tier) {
						if($wallet_user->cap_per_visit_medical > 0) {
						  $cap_amount = $wallet_user->cap_per_visit_medical;
						} else {
						  $cap_amount = $plan_tier->gp_cap_per_visit;
						}
					} else {
					  if($wallet_user->cap_per_visit_medical > 0) {
					    $cap_amount = $wallet_user->cap_per_visit_medical;
					  }
					}

					$credits = 0;
					$cash = 0;
					$half_payment = false;

					if($cap_amount > 0) {
						if($total_amount > $cap_amount) {
							$credits = $cap_amount;
							$cash = $total_amount - $cap_amount;
							$half_payment = true;
							$payment_credits = $credits;
						} else {
							$credits = $total_amount;
							$payment_credits = $total_amount;
						}
					} else {
						$credits = $total_amount;
						$payment_credits = $total_amount;
					}

					if($credits > $user_credits) {
						$credits_temp = $user_credits;
						$cash = $credits - $user_credits;
						$credits = $credits_temp;
						$half_payment = true;
					}
					// return $total_credits;
					$transaction = new Transaction();
  				$wallet = new Wallet( );

  				$multiple = false;
					if(sizeof($input['services']) > 1) {
						$services = 0;
						$multiple_service_selection = 1;
						$multiple = true;
					} else {
						$services = $input['services'][0];
						$multiple_service_selection = 0;
						$multiple = false;
					}

					if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
						$lite_plan_enabled = 1;
						$total_procedure_cost = $total_amount;
						$total_credits_cost = $credits - $consultation_fees;
					} else {
						$lite_plan_enabled = 0;
						$total_procedure_cost = $total_amount - $consultation_fees;
						$total_credits_cost = $credits;
						$consultation_fees = 0;
					}

					$data = array(
				   'UserID'                => $customer_id,
				   'ProcedureID'           => $services,
				   'date_of_transaction'   => date('Y-m-d H:i:s'),
				   'claim_date'            => date('Y-m-d H:i:s'),
				   'ClinicID'              => $input['clinic_id'],
				   'procedure_cost'        => $total_procedure_cost,
				   'AppointmenID'          => 0,
				   'revenue'               => 0,
				   'debit'                 => 0,
				   'clinic_discount'       => $clinic->discount,
				   'medi_percent'          => $clinic->medicloud_transaction_fees,
				   'currency_type'         => $clinic->currency_type,
				   'wallet_use'            => 1,
				   'current_wallet_amount' => $wallet_user->balance,
				   'credit_cost'           => $total_credits_cost,
				   'paid'                  => 1,
				   'co_paid_status'        => $co_paid_status,
				   'co_paid_amount'        => $co_paid_amount,
				   'DoctorID'              => 0,
				   'backdate_claim'        => 1,
				   'in_network'            => 1,
				   'mobile'                => 1,
				   'multiple_service_selection' => $multiple_service_selection,
				   'currency_type'         => $clinic->currency_type,
				   'lite_plan_enabled'     => $lite_plan_enabled,
				   'cash_cost'            => $cash,
				   'half_credits'          => $half_payment == true ? 1 : 0,
				   'consultation_fees'      => $consultation_fees,
				   'cap_per_visit'        => $cap_amount
					);

					if($clinic_peak_status) {
						$data['peak_hour_status'] = 1;
						if($clinic->co_paid_status == 1 || $clinic->co_paid_status == "1") {
							$gst_peak = $peak_amount * $clinic->gst_percent;
							$data['peak_hour_amount'] = $peak_amount + $gst_peak;
						} else {
							$data['peak_hour_amount'] = $peak_amount;
						}
					}

					if($currency) {
					 $data['currency_amount'] = $currency;
					}

					try {
						$result = $transaction->createTransaction($data);
						$transaction_id = $result->id;

						if($result) {
							$procedure = "";
							$procedure_temp = "";

							// insert transation services
							$ts = new TransctionServices( );
							$save_ts = $ts->createTransctionServices($input['services'], $transaction_id);

							if($multiple == true) {
								foreach ($input['services'] as $key => $value) {
								  $procedure_data = DB::table('clinic_procedure')->where('ProcedureID', $value)->first();
								  $procedure_temp .= ucwords($procedure_data->Name).',';
								}
								$procedure = rtrim($procedure_temp, ',');
							} else {
								$procedure_data = DB::table('clinic_procedure')->where('ProcedureID', $service_id)->first();
								$procedure = ucwords($procedure_data->Name);
							}

							// deduct medical/wellness credit
							$history = new WalletHistory( );

							if($spending_type == "medical") {
								$credits_logs = array(
									'wallet_id'     => $wallet_user->wallet_id,
									'credit'        => $total_credits_cost,
									'logs'          => 'deducted_from_mobile_payment',
									'running_balance' => $wallet_user->balance - $total_credits_cost,
									'where_spend'   => 'in_network_transaction',
									'id'            => $transaction_id
								);

								// insert for lite plan
								if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
									$lite_plan_credits_log = array(
									 'wallet_id'     => $wallet_user->wallet_id,
									 'credit'        => $consultation_fees,
									 'logs'          => 'deducted_from_mobile_payment',
									 'running_balance' => $wallet_user->balance - $total_credits_cost - $consultation_fees,
									 'where_spend'   => 'in_network_transaction',
									 'id'            => $transaction_id,
									 'lite_plan_enabled' => 1,
									);
								}
							} else {
								$credits_logs = array(
									'wallet_id'     => $wallet_user->wallet_id,
									'credit'        => $total_credits_cost,
									'logs'          => 'deducted_from_mobile_payment',
									'running_balance' => $wallet_user->wellness_balance - $total_credits_cost - $consultation_fees,
									'where_spend'   => 'in_network_transaction',
									'id'            => $transaction_id
								);

								if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
									$lite_plan_credits_log = array(
									'wallet_id'     => $wallet_user->wallet_id,
									'credit'        => $consultation_fees,
									'logs'          => 'deducted_from_mobile_payment',
									'running_balance' => $wallet_user->balance - $total_credits_cost - $consultation_fees,
									'where_spend'   => 'in_network_transaction',
									'id'            => $transaction_id,
									'lite_plan_enabled' => 1,
									);
								}
							}

							try {
								if($spending_type == "medical") {
									$deduct_history = \WalletHistory::create($credits_logs);
									$wallet_history_id = $deduct_history->id;

									if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
										\WalletHistory::create($lite_plan_credits_log);
									}
								} else {
									$deduct_history = \WellnessWalletHistory::create($credits_logs);
									$wallet_history_id = $deduct_history->id;

									if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
										\WellnessWalletHistory::create($lite_plan_credits_log);
									}
								}

								if($deduct_history) {
									try {
										if($spending_type == "medical") {
											$wallet->deductCredits($user_id, $payment_credits);

											if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
												$wallet->deductCredits($user_id, $consultation_fees);
											}
										} else {
											$wallet->deductWellnessCredits($user_id, $payment_credits);

											if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
												$wallet->deductWellnessCredits($user_id, $consultation_fees);
											}
										}

										$trans_id = str_pad($transaction_id, 6, "0", STR_PAD_LEFT);
										$SGD = null;

										if($clinic->currency_type == "myr") {
											$currency_symbol = "RM ";
											$email_currency_symbol = "RM";
											$total_amount = $total_amount * 3;
										} else {
											$email_currency_symbol = "S$";
											$currency_symbol = '$SGD ';
										}

										$transaction_results = array(
											'clinic_name'       => ucwords($clinic->Name),
											'bill_amount'				=> number_format($input['input_amount'], 2),
											'consultation_fees'	=> $clinic->currency_type == "myr" ? number_format($consultation_fees * 3, 2) : number_format($consultation_fees, 2),
											'total_amount'     => number_format($total_amount, 2),
											'paid_by_credits'            => $clinic->currency_type == "myr" ? number_format($credits * 3, 2) : number_format($credits, 2),
											'paid_by_cash'              => $clinic->currency_type == "myr" ? number_format($cash * 3, 2) : number_format($cash, 2),
											'transaction_time'  => date('Y-m-d h:i', strtotime($result->created_at)),
											'transation_id'     => strtoupper(substr($clinic->Name, 0, 3)).$trans_id,
											'services'          => $procedure,
											'currency_symbol'   => $email_currency_symbol,
											'dependent_user'    => $dependent_user,
											'half_credits_payment' => $half_payment
										);

										$clinic_type_properties = TransactionHelper::getClinicImageType($clinic_type);
										$type = $clinic_type_properties['type'];
										$image = $clinic_type_properties['image'];

										// check if check_in_id exist
										if(!empty($input['check_in_id']) && $input['check_in_id'] != null) {
											// check check_in_id data
											$check_in = DB::table('user_check_in_clinic')
											->where('check_in_id', $input['check_in_id'])
											->first();
											if($check_in) {
												// update check in date
												DB::table('user_check_in_clinic')
												->where('check_in_id', $input['check_in_id'])
												->update(['check_out_time' => date('Y-m-d H:i:s'), 'id' => $transaction_id, 'status' => 1]);
												PusherHelper::sendClinicCheckInRemoveNotification($input['check_in_id'], $check_in->clinic_id);
											}
										}

										// send email
										$email['member'] = ucwords($user->Name);
										$email['credits'] = $clinic->currency_type == "myr" ? number_format($credits * 3, 2) : number_format($credits, 2);
										$email['transaction_id'] = strtoupper(substr($clinic->Name, 0, 3)).$trans_id;
										$email['trans_id'] = $transaction_id;
										$email['transaction_date'] = date('d F Y, h:ia');
										$email['health_provider_name'] = ucwords($clinic->Name);
										$email['health_provider_address'] = $clinic->Address;
										$email['health_provider_city'] = $clinic->City;
										$email['health_provider_country'] = $clinic->Country;
										$email['health_provider_phone'] = $clinic->Phone;
										$email['service'] = ucwords($clinic_type->Name).' - '.$procedure;
										$email['emailSubject'] = 'Member - Successful Transaction';
										$email['emailTo'] = $email_address ? $email_address : 'info@medicloud.sg';
										// $email['emailTo'] = 'allan.alzula.work@gmail.com';
										$email['emailName'] = ucwords($user->Name);
										$email['url'] = 'http://staging.medicloud.sg';
										$email['clinic_type_image'] = $image;
										$email['transaction_type'] = 'Mednefits Credits';
										$email['emailPage'] = 'email-templates.member-successful-transaction-v2';
										$email['dl_url'] = url();
										$email['lite_plan_enabled'] = $clinic_type->lite_plan_enabled;
										$email['lite_plan_status'] = $lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1 ? TRUE : FAlSE;
										$email['total_amount'] = number_format($total_amount, 2);
										$email['consultation'] = $clinic->currency_type == "myr" ? number_format($consultation_fees * 3, 2) : number_format($consultation_fees, 2);
										$email['currency_symbol'] = $email_currency_symbol;
										$email['pdf_file'] = 'pdf-download.member-successful-transac-v2';

										try {
											EmailHelper::sendPaymentAttachment($email);
											  // send to clinic
											$clinic_email = DB::table('user')->where('UserType', 3)->where('Ref_ID', $input['clinic_id'])->first();

											if($clinic_email) {
											 $email['emailSubject'] = 'Health Partner - Successful Transaction By Mednefits Credits';
											 $email['nric'] = $user->NRIC;
											 $email['emailTo'] = $clinic_email->Email;
											 // $email['emailTo'] = 'allan.alzula.work@gmail.com';
											 $email['emailPage'] = 'email-templates.health-partner-successful-transaction-v2';
											 $api = "https://admin.medicloud.sg/send_clinic_transaction_email";
											 $email['pdf_file'] = 'pdf-download.health-partner-successful-transac-v2';
											 EmailHelper::sendPaymentAttachment($email);
											}
											$returnObject->status = TRUE;
											$returnObject->message = 'Payment Successfull';
											$returnObject->data = $transaction_results;
											return Response::json($returnObject);
										} catch(Exception $e) {
											$email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
											$email['logs'] = 'Mobile Payment Credits Send Email Attachments - '.$e;
											$email['emailSubject'] = 'Error log.';
											EmailHelper::sendErrorLogs($email);
											$returnObject->status = TRUE;
											$returnObject->message = 'Payment Successfull';
											$returnObject->data = $transaction_results;
											return Response::json($returnObject);
										}


									} catch(Exception $e) {
										$email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
										$email['logs'] = 'Mobile Payment Credits - '.$e;
										$email['emailSubject'] = 'Error log. - Transaction ID: '.$transaction_id.' Wallet History ID: '.$wallet_history_id;

										// delete transaction history log
										$transaction->deleteFailedTransactionHistory($transaction_id);
										// delete failed wallet history
										if($spending_type == "medical") {
											$history->deleteFailedWalletHistory($wallet_history_id);
											 // credits back
											$wallet->addCredits($user_id, $credits);
										} else {
											\WellnessWalletHistory::where('wellness_wallet_history_id', $wallet_history_id)->delete();
											$wallet->addWellnessCredits($user_id, $credits);
										}
										$returnObject->status = FALSE;
										$returnObject->message = 'Payment unsuccessfull. Please try again later';
										EmailHelper::sendErrorLogs($email);
									}
								} else {
									$email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
									$email['logs'] = 'Mobile Payment Credits - '.$e;
									$email['emailSubject'] = 'Error log. - Transaction ID: '.$transaction_id.' Wallet History ID: '.$wallet_history_id;

									// delete transaction history log
									$transaction->deleteFailedTransactionHistory($transaction_id);
									// delete failed wallet history
									if($spending_type == "medical") {
										$history->deleteFailedWalletHistory($wallet_history_id);
										 // credits back
										$wallet->addCredits($user_id, $credits);
									} else {
										\WellnessWalletHistory::where('wellness_wallet_history_id', $wallet_history_id)->delete();
										$wallet->addWellnessCredits($user_id, $credits);
									}
									$returnObject->status = FALSE;
									$returnObject->message = 'Payment unsuccessfull. Please try again later';
									EmailHelper::sendErrorLogs($email);
									return Response::json($returnObject);
								}
							} catch(Exception $e) {
								$email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
								$email['logs'] = 'Mobile Payment Credits - '.$e;
								$email['emailSubject'] = 'Error log. - Transaction ID: '.$transaction_id.' Wallet History ID: '.$wallet_history_id;

								// delete transaction history log
								$transaction->deleteFailedTransactionHistory($transaction_id);
								// delete failed wallet history
								if($spending_type == "medical") {
									$history->deleteFailedWalletHistory($wallet_history_id);
									 // credits back
									$wallet->addCredits($user_id, $credits);
								} else {
									\WellnessWalletHistory::where('wellness_wallet_history_id', $wallet_history_id)->delete();
									$wallet->addWellnessCredits($user_id, $credits);
								}
								$returnObject->status = FALSE;
								$returnObject->message = 'Payment unsuccessfull. Please try again later';
								EmailHelper::sendErrorLogs($email);
								return Response::json($returnObject);
							}

						} else {

						}
					} catch(Exception $e) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Cannot process payment credits. Please try again.';
						// send email logs
						$email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
						$email['logs'] = 'Mobile Payment Credits - '.$e;
						$email['emailSubject'] = 'Error log.';
						EmailHelper::sendErrorLogs($email);
						return Response::json($returnObject);
					}
				} else {
					$returnObject->status = FALSE;
					$returnObject->message = StringHelper::errorMessage("Token");
					return Response::json($returnObject);
				}
			} else {
				$returnObject->status = FALSE;
	      $returnObject->message = StringHelper::errorMessage("Token");
	      return Response::json($returnObject);
			}
		} else {
			$returnObject->status = FALSE;
      $returnObject->message = StringHelper::errorMessage("Token");
      return Response::json($returnObject);
		}
	}

	public function getNetworkTransactions( )
	{
   $AccessToken = new Api_V1_AccessTokenController();
   $returnObject = new stdClass();
   $authSession = new OauthSessions();
   $getRequestHeader = StringHelper::requestHeader();
   $input = Input::all();
   if(!empty($getRequestHeader['Authorization'])){
      $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
      if($getAccessToken){
         $findUserID = $authSession->findUserID($getAccessToken->session_id);

         if($findUserID){
            $returnObject->status = TRUE;
            $returnObject->message = 'Success.';
            $user = DB::table('user')->where('UserID', $findUserID)->first();
            $lite_plan_status = false;
            // $lite_plan_status = StringHelper::litePlanStatus($findUserID);

                    // $type = StringHelper::checkUserType($findUserID);
            $transaction_details = [];
            $ids = StringHelper::getSubAccountsID($findUserID);
            $transactions = DB::table('transaction_history')->whereIn('UserID', $ids)->orderBy('created_at', 'desc')->get();
              foreach ($transactions as $key => $trans) {
               if($trans) {
                  $receipt_images = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->get();
                  $clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
                  $clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
                  $customer = DB::table('user')->where('UserID', $trans->UserID)->first();
                  $procedure_temp = "";
                            // get services
                  if($trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1")
                  {
                                // get multiple service
                     $service_lists = DB::table('transaction_services')
                     ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
                     ->where('transaction_services.transaction_id', $trans->transaction_id)
                     ->get();

                     foreach ($service_lists as $key => $service) {
                        $procedure_temp .= ucwords($service->Name).',';
                        $procedure = rtrim($procedure_temp, ',');
                    }
                    $clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
                } else {
                   $service_lists = DB::table('clinic_procedure')
                   ->where('ProcedureID', $trans->ProcedureID)
                   ->first();
                   if($service_lists) {
                      $procedure = ucwords($service_lists->Name);
                      $clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
                  } else {
                      $clinic_name = ucwords($clinic_type->Name);
                  }
              }

                            // check if there is a receipt image
              $receipt = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->count();

              if($receipt > 0) {
                 $receipt_status = TRUE;
             } else {
                 $receipt_status = FALSE;
             }


             $clinic_sub_name = strtoupper(substr($clinic->Name, 0, 3));
             $transaction_id = $clinic_sub_name.str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

             $total_amount = 0;

             if(strripos($trans->procedure_cost, '$') !== false) {
                 $temp_cost = explode('$', $trans->procedure_cost);
                 $cost = $temp_cost[1];
             } else {
                 $cost = floatval($trans->procedure_cost);
             }

             $total_amount = $cost;

             if((int)$trans->health_provider_done == 1) {
                 $receipt_status = TRUE;
                 $health_provider_status = TRUE;
                 // $receipt_status = TRUE;
                 if((int)$trans->lite_plan_enabled == 1) {
                    $total_amount = $cost + $trans->consultation_fees;
                } else {
                    $total_amount = $cost;
                }
                $type = "cash";
            } else {
               $health_provider_status = FALSE;
               if((int)$trans->lite_plan_enabled == 1) {
                  $total_amount = $trans->credit_cost + $trans->consultation_fees + $trans->cash_cost;
              } else {
                  $total_amount = $cost;
              }
              $type = "credits";
          }

          $currency_symbol = null;
          $converted_amount = null;

          if($trans->currency_type == "sgd") {
            $currency_symbol = "S$";
            $converted_amount = $total_amount;
          } else if($trans->currency_type == "myr") {
            $currency_symbol = "RM";
            $converted_amount = $total_amount * $trans->currency_amount;
          }

          $format = array(
           'clinic_name'       => $clinic->Name,
           'amount'            => $trans->currency_type == "myr" ? number_format($total_amount * 3, 2) : number_format($total_amount, 2),
           'converted_amount'  => number_format($converted_amount, 2),
           'currency_symbol'   => $currency_symbol,
           'clinic_type_and_service' => $clinic_name,
           'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->created_at)),
           'customer'          => ucwords($customer->Name),
           'transaction_id'    => (string)$transaction_id,
           'receipt_status'    => $receipt_status,
           'health_provider_status' => $health_provider_status,
           'user_id'           => (string)$trans->UserID,
           'type'              => $type,
           'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE
	       );

	          array_push($transaction_details, $format);
	      }
	  }
		  $returnObject->data = $transaction_details;
		  return Response::json($returnObject);
		} else {
		  $returnObject->status = FALSE;
		  $returnObject->message = StringHelper::errorMessage("Token");
		  return Response::json($returnObject);
		}
		} else {
		   $returnObject->status = FALSE;
		   $returnObject->message = StringHelper::errorMessage("Token");
		   return Response::json($returnObject);
		}
		} else {
		  $returnObject->status = FALSE;
		  $returnObject->message = StringHelper::errorMessage("Token");
		  return Response::json($returnObject);
		}
	}

	public function getInNetworkDetails($id)
	{
		$AccessToken = new Api_V1_AccessTokenController();
		$returnObject = new stdClass();
		$authSession = new OauthSessions();
		$getRequestHeader = StringHelper::requestHeader();
		$input = Input::all();

		if(!empty($getRequestHeader['Authorization'])){
			$getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);

			if($getAccessToken){
				$findUserID = $authSession->findUserID($getAccessToken->session_id);
        $transaction_id = (int)preg_replace('/[^0-9]/', '', $id);

        if($findUserID){
					$returnObject->status = TRUE;
					$returnObject->message = 'Success.';
					// $user = DB::table('user')->where('UserID', $findUserID)->first();
					$user_id = StringHelper::getUserId($findUserID);
					$lite_plan_status = false;
					        // $lite_plan_status = StringHelper::litePlanStatus($findUserID);
					$total_amount = 0;
					$service_credits = false;
					$consultation_credits = false;
					$consultation = 0;
					$wallet_status = false;

					$transaction_details = [];
					$transaction = DB::table('transaction_history')->where('transaction_id', $transaction_id)->first();
					$company_wallet_status = PlanHelper::getCompanyAccountType($user_id);

					if($company_wallet_status) {
					   if($company_wallet_status == "Health Wallet") {
					      $wallet_status = true;
					  }
					}

					if($transaction) {
						$receipt_images = DB::table('user_image_receipt')->where('transaction_id', $transaction->transaction_id)->get();
						$clinic = DB::table('clinic')->where('ClinicID', $transaction->ClinicID)->first();
						$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
						$customer = DB::table('user')->where('UserID', $transaction->UserID)->first();
						$procedure_temp = "";

						if((int)$transaction->lite_plan_enabled == 1) {
						  if($transaction->spending_type == 'medical') {
						     $table_wallet_history = 'wallet_history';
						   } else {
						       $table_wallet_history = 'wellness_wallet_history';
						   }

						 $logs_lite_plan = DB::table($table_wallet_history)
						 ->where('logs', 'deducted_from_mobile_payment')
						 ->where('lite_plan_enabled', 1)
						 ->where('id', $transaction->transaction_id)
						 ->first();

						 if($logs_lite_plan && $transaction->credit_cost > 0 && (int)$transaction->lite_plan_use_credits == 0) {
						   $consultation_credits = true;
						    // $service_credits = true;
						    $consultation = $logs_lite_plan->credit;
						 } else if($logs_lite_plan && $transaction->procedure_cost >= 0 && (int)$transaction->lite_plan_use_credits == 1) {
						   $consultation_credits = true;
						    // $service_credits = true;
						    $consultation = $logs_lite_plan->credit;
						 } else if($transaction->procedure_cost >= 0 && (int)$transaction->lite_plan_use_credits == 0) {
						  // $total_consultation += floatval($trans->co_paid_amount);
						    $consultation = floatval($transaction->consultation_fees);
						 } else {
						  $consultation = floatval($transaction->consultation_fees);
						 }
						}

						$doc_files = [];
						foreach ($receipt_images as $key => $doc) {
							if($doc->type == "pdf" || $doc->type == "xls") {
								if(StringHelper::Deployment()==1){
									$fil = 'https://s3-ap-southeast-1.amazonaws.com/mednefits/receipts/'.$doc->file;
								} else {
									$fil = url('').'/receipts/'.$doc->file;
								}
							} else if($doc->type == "image") {
								$fil = FileHelper::formatImageAutoQuality($doc->file);
							}

							$temp_doc = array(
							  'transaction_doc_id'    => $doc->image_receipt_id,
							  'transaction_id'            => $doc->transaction_id,
							  'file'                      => $fil,
							  'file_type'             => $doc->type
							);

							array_push($doc_files, $temp_doc);
						}

						// get services
						if($transaction->multiple_service_selection == 1 || $transaction->multiple_service_selection == "1")
						{
							// get multiple service
							$service_lists = DB::table('transaction_services')
							->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
							->where('transaction_services.transaction_id', $transaction->transaction_id)
							->get();

							foreach ($service_lists as $key => $service) {
								$procedure_temp .= ucwords($service->Name).',';
								$procedure = rtrim($procedure_temp, ',');
							}
								$service = $procedure;
						} else {
							$service_lists = DB::table('clinic_procedure')
							->where('ProcedureID', $transaction->ProcedureID)
							->first();
							if($service_lists) {
								$procedure = ucwords($service_lists->Name);
								$service = $procedure;
							} else {
								$service = ucwords($clinic_type->Name);
							}
						}

						$trans_image = TransactionHelper::getClinicImageType($clinic_type);
						$type = $trans_image['type'];
						$image = $trans_image['image'];

						$half_credits = false;
						$total_amount = $transaction->procedure_cost;
						$bill_amount = 0;

						$procedure_cost = number_format($transaction->procedure_cost, 2);
						if((int)$transaction->health_provider_done == 1) {
							$payment_type = 'Cash';
							if((int)$transaction->lite_plan_enabled == 1 && $wallet_status == true) {
								if((int)$transaction->half_credits == 1) {
									$total_amount = $transaction->credit_cost + $transaction->consultation_fees;
								} else {
									$total_amount = $transaction->procedure_cost + $transaction->consultation_fees;
								}
							}
						} else {
							if($transaction->credit_cost > 0 && $transaction->cash_cost > 0) {
								$payment_type = 'Mednefits Credits + Cash';
								$half_credits = true;
							} else {
								$payment_type = 'Mednefits Credits';
							}
							$service_credits = true;
							if((int)$transaction->lite_plan_enabled == 1 && $wallet_status == true) {
								if((int)$transaction->half_credits == 1) {
									$total_amount = $transaction->credit_cost + $transaction->cash_cost + $transaction->consultation_fees;
								} else {
									$total_amount = $transaction->credit_cost + $transaction->consultation_fees;
								}
							} else {
								$total_amount = $transaction->procedure_cost;
							}
						}

						$lite_plan_status = (int)$transaction->lite_plan_enabled == 1 ? TRUE : FALSE;

						if((int)$transaction->lite_plan_enabled == 1 && $wallet_status == false) {
							$service_credits = false;
							$consultation_credits = false;
							$lite_plan_status = false;
						}

						$consultation_fee = 0;

						if($transaction->currency_type == "sgd") {
							if((int)$transaction->lite_plan_enabled == 1) {
								$consultation_fee = number_format($consultation, 2);
							}
						} else if($transaction->currency_type == "myr") {
							if((int)$transaction->lite_plan_enabled == 1) {
								$consultation_fee = number_format($consultation * $transaction->currency_amount, 2);
							}
						}

						$lite_plan_status = (int)$transaction->lite_plan_enabled == 1 ? TRUE : FALSE;

						if((int)$transaction->lite_plan_enabled == 1 && $wallet_status == false) {
							$service_credits = false;
							$consultation_credits = false;
							$lite_plan_status = false;
						}

						$transaction_details = array(
							'clinic_name'       => $clinic->Name,
							'clinic_image'      => $clinic->image ? $clinic->image : 'https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514443281/rjfremupirvnuvynz4bv.jpg',
							'clinic_type'       => $type,
							'clinic_type_image' => $image,
							'total_amount'       => $transaction->currency_type == "myr" ? number_format($total_amount * 3, 2) : number_format($total_amount, 2),
							"currency_symbol" => $transaction->currency_type == "myr" ? "RM" : "S$",
							'transaction_id'    => (string)$id,
							'date_of_transaction' => date('d-m-Y, h:ia', strtotime($transaction->date_of_transaction)),
							'customer'            => ucwords($customer->Name),
							'payment_type'		=> $payment_type,
							'bill_amount'				=> $transaction->currency_type == "myr" ? number_format($transaction->credit_cost * 3, 2) : number_format($transaction->credit_cost, 2),
							'consultation_fee'	=> $consultation_fee,
							'paid_by_cash'      => $transaction->currency_type == "myr" ? number_format($transaction->cash_cost * $transaction->currency_amount, 2) : number_format($transaction->cash_cost, 2),
							'paid_by_credits'      => $transaction->currency_type == "myr" ? number_format($transaction->credit_cost * $transaction->currency_amount, 2) : number_format($transaction->credit_cost, 2),
							'files'             => $doc_files,
							'lite_plan'         => $lite_plan_status,
							'lite_plan_enabled' => $transaction->lite_plan_enabled,
							'cap_transaction'   => $half_credits,
    					'cap_per_visit'     => $transaction->currency_type == "myr" ? number_format($transaction->cap_per_visit * $transaction->currency_amount, 2) : number_format($transaction->cap_per_visit, 2),
							'services' => $service
						);

						$returnObject->data = $transaction_details;
						return Response::json($returnObject);
					} else {
						$returnObject->status = FALSE;
						$returnObject->message = 'Transaction does not exist.';
						return Response::json($returnObject);
					}
        } else {
					$returnObject->status = FALSE;
					$returnObject->message = StringHelper::errorMessage("Token");
					return Response::json($returnObject);
        }
			} else {
				$returnObject->status = FALSE;
				$returnObject->message = StringHelper::errorMessage("Token");
				return Response::json($returnObject);
			}
		} else {
			$returnObject->status = FALSE;
			$returnObject->message = StringHelper::errorMessage("Token");
			return Response::json($returnObject);
		}
	}

	public function uploadInNetworkReceiptBulk( )
	{
		$AccessToken = new Api_V1_AccessTokenController();
		$returnObject = new stdClass();
		$authSession = new OauthSessions();
		$getRequestHeader = StringHelper::requestHeader();
		$input = Input::all();


		if(!empty($getRequestHeader['Authorization'])){
			$getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);

			if($getAccessToken){
				$findUserID = $authSession->findUserID($getAccessToken->session_id);

				if($findUserID){
					$returnObject->status = TRUE;
					$returnObject->message = 'Success.';

					if(empty($input['transaction_id']) || $input['transaction_id'] == null) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Transaction ID is required.';
						return Response::json($returnObject);
					}

					$transaction_id = (int)preg_replace('/[^0-9]/', '', $input['transaction_id']);

					$check = DB::table('transaction_history')->where('transaction_id', $transaction_id)->count();

					if($check == 0) {
					 $returnObject->status = FALSE;
					 $returnObject->message = 'Transaction data does not exist.';
					 return Response::json($returnObject);
					}

					if(empty(Input::file('files')) || sizeof(Input::file('files')) == 0) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Please select a file.';
						return Response::json($returnObject);
					}

					$rules = array(
            'file' => 'required | mimes:jpeg,jpg,png,pdf,xls,xlsx',
          );

          $trans_docs = new UserImageReceipt( );
          $results = [];
					foreach (Input::file('files') as $key => $file) {
						if(!$file) {
              $returnObject->status = FALSE;
              $returnObject->message = 'Please input a file.';
              return Response::json($returnObject);
            }

						// check if file is image
						$validator = Validator::make(
						  array('file' => $file),
						  $rules
						);

						if($validator->passes()) {
              $file_size = $file->getSize();
              // check file size if exceeds 10 mb
              if($file_size > 10000000) {
                $returnObject->status = FALSE;
                $returnObject->message = $file->getClientOriginalName().' file is too large. File must be 10mb size of image.';
                return Response::json($returnObject);
              }
              
              // if (false !== mb_strpos($file->getMimeType(), "video")) {
              //   $returnObject->status = FALSE;
              //   $returnObject->message = $file->getClientOriginalName().' file is not valid. Only accepts Image.';
              //   return Response::json($returnObject);
              // }
            } else {
              $returnObject->status = FALSE;
              $returnObject->message = $file->getClientOriginalName().' file is not valid. Only accepts Image.';
              return Response::json($returnObject);
            }
          }

          foreach (Input::file('files') as $key => $file) {
            // save receipt data
						$file_name = time().' - '.$file->getClientOriginalName();
						$aws_upload = false;
						if($file->getClientOriginalExtension() == "pdf") {
						    $receipt = array(
					       'user_id'           => $findUserID,
					       'transaction_id'    => $transaction_id,
					       'file'  => $file_name,
					       'type'  => "pdf"
						   );
						    $file->move(public_path().'/receipts/', $file_name);
						    $aws_upload = true;
						} else if($file->getClientOriginalExtension() == "xls" || $file->getClientOriginalExtension() == "xlsx") {
						    $receipt = array(
					       'user_id'           => $findUserID,
					       'transaction_id'    => $transaction_id,
					       'file'  => $file_name,
					       'type'  => "excel"
						   );
						    $file->move(public_path().'/receipts/', $file_name);
						    $aws_upload = true;
						} else {
						    $image = \Cloudinary\Uploader::upload($file->getPathName());
						    $receipt = array(
					       'user_id'           => $findUserID,
					       'file'      => $image['secure_url'],
					       'type'      => "image",
					       'transaction_id'    => $transaction_id,
						   );
						}

						$result = $trans_docs->saveReceipt($receipt);

						if($result) {
              // if(StringHelper::Deployment()==1){
                 if($aws_upload == true) {
                  //   aws
                  $s3 = AWS::get('s3');
                  $s3->putObject(array(
                   'Bucket'     => 'mednefits',
                   'Key'        => 'receipts/'.$file_name,
                   'SourceFile' => public_path().'/receipts/'.$file_name,
                 ));
                }
              // }
              array_push($results, $result);
            } else {
            	$returnObject->status = FALSE;
              $returnObject->message = 'Failed to save transaction receipt.';
              return Response::json($returnObject);
            }
					}

					$returnObject->status = FALSE;
          $returnObject->message = 'Success.';
          $returnObject->data = $results;
          return Response::json($returnObject);
				} else {
					$returnObject->status = FALSE;
					$returnObject->message = StringHelper::errorMessage("Token");
					return Response::json($returnObject);
				}
			} else {
				$returnObject->status = FALSE;
				$returnObject->message = StringHelper::errorMessage("Token");
				return Response::json($returnObject);
			}
		} else {
			$returnObject->status = FALSE;
			$returnObject->message = StringHelper::errorMessage("Token");
			return Response::json($returnObject);
		}
	}
}
?>