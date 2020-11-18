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
						$returnObject->head_message = 'Panel Submission Error';
						$returnObject->message = 'Please choose a service.';
						return Response::json($returnObject);
					} else if(sizeof($input['services']) == 0) {
						$returnObject->status = FALSE;
						$returnObject->head_message = 'Panel Submission Error';
						$returnObject->message = 'Please choose a service.';
						return Response::json($returnObject);
					}
					// check if clinic_id is present
					if(!isset($input['clinic_id'])) {
						$returnObject->status = FALSE;
						$returnObject->head_message = 'Panel Submission Error';
						$returnObject->message = 'Please choose a clinic.';
						return Response::json($returnObject);
					}
					// check if input amount is present
					if(is_numeric($input['input_amount']) == false) {
						$returnObject->status = FALSE;
						$returnObject->head_message = 'Panel Submission Error';
						$returnObject->message = 'Amount should be a number.';
						return Response::json($returnObject);
					}

					if($input['input_amount'] < 0) {
						$returnObject->status = FALSE;
						$returnObject->head_message = 'Panel Submission Error';
						$returnObject->message = 'Amount should not be below 0.';
						return Response::json($returnObject);
					}

					$lite_plan_status = false;
					$clinic_peak_status = false;
					$service_id = $input['services'][0];
					if(is_array($service_id)) {
						// $returnObject->status = FALSE;
						// $returnObject->head_message = 'Panel Submission Error';
						// $returnObject->message = 'Please choose a service.';
						// return Response::json($returnObject);
						// if()
						if(isset($service_id['procedureid'])) {
							$service_id = $service_id['procedureid'];
							$input['services'] = [$service_id];
						}					
					}
					
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

					// get clinic info and type
					$clinic = DB::table('clinic')->where('ClinicID', $input['clinic_id'])->first();
					$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
					$consultation_fees = 0;

					if(!$dependent_user) {
						$user_plan_history = DB::table('user_plan_history')->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
						$customer_active_plan = DB::table('customer_active_plan')
						->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
						->first();
					} else {
						$user_plan_history = DB::table('dependent_plan_history')->where('user_id', $customer_id)->orderBy('created_at', 'desc')->first();
						$customer_active_plan = DB::table('dependent_plans')
													->where('dependent_plan_id', $user_plan_history->dependent_plan_id)
													->first();
					}

					if($customer_active_plan->account_type == "enterprise_plan" && (int)$clinic_type->visit_deduction == 1)	{
						$limit = $user_plan_history->total_visit_limit - $user_plan_history->total_visit_created;
			
						if($limit <= 0) {
							$returnObject->status = FALSE;
							$returnObject->message = 'Maximum of 14 visits already reached.';
							return Response::json($returnObject);
						}

						$owner_id = StringHelper::getUserId($findUserID);
						$wallet_checker = DB::table('e_wallet')->where('UserID', $owner_id)->first();

						if($wallet_checker->currency_type === 'myr') {
							if($clinic->currency_type === 'sgd') {
								$returnObject->status = FALSE;
								$returnObject->message = 'Member is prohibited to access this clinic from Singpapore';
								return Response::json($returnObject);
							}
						}
					}

					// check if enable to access feature
					$transaction_access = MemberHelper::checkMemberAccessTransactionStatus($user_id);

					if($transaction_access)	{
						$returnObject->status = FALSE;
						$returnObject->head_message = 'Panel Submission Error';
						$returnObject->message = 'Panel function is disabled for your company.';
						return Response::json($returnObject);
					}

					// check block access
					$block = PlanHelper::checkCompanyBlockAccess($user_id, $input['clinic_id']);
					if($block) {
						$returnObject->status = FALSE;
						$returnObject->head_message = 'Panel Submission Error';
						$returnObject->message = 'Clinic not accessible to your Company. Please contact Your company for more information.';
						return Response::json($returnObject);
					}

					if($customer_active_plan && $customer_active_plan->account_type == "enterprise_plan") {
						$spending_type = $clinic_type->spending_type;
						$user_credits = 100000000000;
						$wallet_user = DB::table('e_wallet')->where('UserID', $user_id)->first();
					} else {
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
							$returnObject->head_message = 'Insufficient Credits';
							$returnObject->message = "Sorry, it seems you don't have enough credits to complete the transaction.";
							$returnObject->sub_mesage = 'Not to worry - you can still pay the health provider directly via Cash/Nets/Credit Card.';
							return Response::json($returnObject);
						}
					}

					$currency_data = DB::table('currency_options')->where('currency_type', $wallet_user->currency_type)->first();
					$user_curreny_type = $wallet_user->currency_type;
					if($currency_data) {
						$currency = $currency_data->currency_value;
						$currency_data_type = $currency_data->currency_type;
					} else {
						$currency = 3.00;
						$currency_data_type = "sgd";
					}


					$clinic_co_payment = TransactionHelper::getCoPayment($clinic, date('Y-m-d H:i:s'), $user_id);
					if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
						$consultation_fees = $clinic_co_payment['consultation_fees'] == 0 ? $clinic->consultation_fees : $clinic_co_payment['consultation_fees'];
					} else {
						$consultation_fees = 0;
					}

					if($clinic->currency_type == "myr") {
						$input_amount = TransactionHelper::floatvalue($input['input_amount']) + TransactionHelper::floatvalue($consultation_fees * $currency);
					} else {
						$input_amount = TransactionHelper::floatvalue($input['input_amount']) + TransactionHelper::floatvalue($consultation_fees);
					}

					if($clinic->currency_type == "myr") {
						$total_amount = $input_amount / 3;
					} else {
						$total_amount = $input_amount;
					}

					// get details for clinic co paid
					$clinic_co_payment = TransactionHelper::getCoPayment($clinic, date('Y-m-d H:i:s'), $user_id);
					$peak_amount = $clinic_co_payment['peak_amount'];
					$co_paid_amount = $clinic_co_payment['co_paid_amount'];
					$co_paid_status = $clinic_co_payment['co_paid_status'];
					$clinic_peak_status = $clinic_co_payment['clinic_peak_status'];
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

					$currency_type = $clinic->currency_type;
					$balance = $wallet_user->balance;
					if($wallet_user->currency_type == "myr") {
						$cap_amount = $cap_amount / $currency;
						$user_credits = $user_credits / $currency;
						$balance = $wallet_user->balance / $currency;
					} else {
						// $cap_amount = $cap_amount / $currency;
						// $user_credits = $user_credits / $currency;
						// $balance = $wallet_user->balance / $currency;
					}

					$credits = 0;
					$cash = 0;
					$half_payment = false;
					$user_credits = round($user_credits, 2);
					$consultation_fees = round($consultation_fees, 2);

					if($cap_amount > 0) {
						if($cap_amount > $user_credits) {
							if($total_amount > $user_credits) {
								$credits = $user_credits;
								$cash = $total_amount - $user_credits;
								$half_payment = true;
							} else {
								$credits = $total_amount;
								$cash = 0;
							}
						} else if($cap_amount == $total_amount){
							$credits = $total_amount;
							$cash = 0;
						} else {
							if($total_amount > $cap_amount) {
								$credits = $cap_amount;
								$cash = $total_amount - $cap_amount;
								$half_payment = true;
							} else {
								$credits = $total_amount;
								$cash = 0;
							}
						}
					} else {
						if($total_amount > $user_credits) {
							$credits = $user_credits;
							$cash = $total_amount - $user_credits;
							$half_payment = true;
						} else {
							$credits = $total_amount;
							$cash = 0;
						}
					}

					$transaction = new Transaction();
					$wallet = new Wallet( );

					$multiple = false;
					if(sizeof($input['services']) > 1) {
						$services = 0;
						$multiple_service_selection = 1;
						$multiple = true;
					} else {
						$multiple_service_selection = 0;
						$multiple = false;
						$services = $service_id;
					}
					
					if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
						$lite_plan_enabled = 1;
						$total_procedure_cost = $total_amount;
						$total_credits_cost = $credits;

						if( $total_credits_cost > $consultation_fees ){
							$total_credits_cost -= $consultation_fees;
						}else if( $consultation_fees > $total_credits_cost || $consultation_fees == $total_credits_cost && (float)$input['input_amount'] == 0){
							// $cash -= ( $consultation_fees - $total_credits_cost );
							$consultation_fees = $total_credits_cost;
							$total_credits_cost = 0;
						}
					} else {
						$lite_plan_enabled = 0;
						$total_procedure_cost = $total_amount - $consultation_fees;
						$total_credits_cost = $credits;
						// $consultation_fees = 0;
					}

					$date_of_transaction = null;
					$payment_credits = $total_credits_cost;

					if(isset($input['check_out_time']) && $input['check_out_time'] != null) {
						$date_of_transaction = date('Y-m-d H:i:s', strtotime($input['check_out_time']));
					} else {
						$date_of_transaction = date('Y-m-d H:i:s');
					}
					
					$data = array(
						'UserID'                => $customer_id,
						'ProcedureID'           => $services,
						'date_of_transaction'   => $date_of_transaction,
						'claim_date'            => $date_of_transaction,
						'ClinicID'              => $input['clinic_id'],
						'procedure_cost'        => $total_procedure_cost,
						'AppointmenID'          => 0,
						'revenue'               => 0,
						'debit'                 => 0,
						'clinic_discount'       => $clinic->discount,
						'medi_percent'          => $clinic->medicloud_transaction_fees,
						'currency_type'         => $currency_type,
						'wallet_use'            => 1,
						'current_wallet_amount' => $balance,
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
						'cap_per_visit'        => $cap_amount,
						'created_at'						 => $date_of_transaction,
						'updated_at'						 => $date_of_transaction,
						'default_currency'			=> $user_curreny_type
					);
					
					if($clinic_peak_status) {
						$data['peak_hour_status'] = 1;
						if((int)$clinic->co_paid_status == 1) {
							$gst_peak = $peak_amount * $clinic->gst_percent;
							$data['peak_hour_amount'] = $peak_amount + $gst_peak;
						} else {
							$data['peak_hour_amount'] = $peak_amount;
						}
					}

					if($currency) {
						$data['currency_amount'] = $currency;
					}

					if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1 && $user_credits < $consultation_fees) {
						$data['consultation_fees'] = $consultation_fees - $user_credits;
					}

					if($customer_active_plan->account_type == "enterprise_plan" && (int)$clinic_type->visit_deduction == 1)	{
						$data['enterprise_visit_deduction'] = 1;
					}
					
					try {
						$result = $transaction->createTransaction($data);
						$transaction_id = $result->id;

						if($result) {
							$procedure = "";
							$procedure_temp = "";

							// insert transation services
							$ts = new TransctionServices( );
							// if($input['services'] == null) {
							// 	$input['services'] = 55;
							// 	$save_ts = $ts->createTransctionServices($input['services'], $transaction_id);
							// 	$procedure_data = DB::table('clinic_procedure')->where('ProcedureID', 55)->first();
							// 	$procedure = ucwords($procedure_data->Name);
							// } else {
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
							// }
							

							// deduct medical/wellness credit
							$history = new WalletHistory( );

							if($spending_type == "medical") {
								if($user_curreny_type == "myr") {
									$total_credits_cost = $total_credits_cost * $currency;
									$credits_logs = array(
										'wallet_id'     => $wallet_user->wallet_id,
										'credit'        => $total_credits_cost,
										'logs'          => 'deducted_from_mobile_payment',
										'running_balance' => $wallet_user->balance - $total_credits_cost,
										'where_spend'   => 'in_network_transaction',
										'id'            => $transaction_id,
										'currency_type' => $user_curreny_type,
										'currency_value'	=> $currency
									);
								} else {
									$credits_logs = array(
										'wallet_id'     => $wallet_user->wallet_id,
										'credit'        => $total_credits_cost,
										'logs'          => 'deducted_from_mobile_payment',
										'running_balance' => $wallet_user->balance - $total_credits_cost,
										'where_spend'   => 'in_network_transaction',
										'id'            => $transaction_id,
										'currency_type' => $user_curreny_type,
										'currency_value'	=> $currency
									);
								}

								if($customer_active_plan && $customer_active_plan->account_type == "enterprise_plan") {
									$credits_logs['running_balance'] = 0;
									$credits_logs['unlimited'] = 1;
								}

								// insert for lite plan
								if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1 && $user_credits > $consultation_fees) {
									if($user_curreny_type == "myr") {
										$total_credits_cost = $total_credits_cost * $currency;
										$consultation_fees = $consultation_fees * $currency;
										$lite_plan_credits_log = array(
											'wallet_id'     => $wallet_user->wallet_id,
											'credit'        => $consultation_fees,
											'logs'          => 'deducted_from_mobile_payment',
											'running_balance' => $wallet_user->balance - $total_credits_cost - $consultation_fees,
											'where_spend'   => 'in_network_transaction',
											'id'            => $transaction_id,
											'lite_plan_enabled' => 1,
											'currency_type' => $user_curreny_type,
											'currency_value'	=> $currency
										);
									} else {
										$lite_plan_credits_log = array(
											'wallet_id'     => $wallet_user->wallet_id,
											'credit'        => $consultation_fees,
											'logs'          => 'deducted_from_mobile_payment',
											'running_balance' => $wallet_user->balance - $total_credits_cost - $consultation_fees,
											'where_spend'   => 'in_network_transaction',
											'id'            => $transaction_id,
											'lite_plan_enabled' => 1,
											'currency_type' => $user_curreny_type,
											'currency_value'	=> $currency
										);
									}
								}
							} else {
								if($user_curreny_type == "myr") {
									$total_credits_cost = $total_credits_cost * $currency;
									$consultation_fees = $consultation_fees * $currency;
									$credits_logs = array(
										'wallet_id'     => $wallet_user->wallet_id,
										'credit'        => $total_credits_cost,
										'logs'          => 'deducted_from_mobile_payment',
										'running_balance' => $wallet_user->wellness_balance - $total_credits_cost - $consultation_fees,
										'where_spend'   => 'in_network_transaction',
										'id'            => $transaction_id,
										'currency_type' => $user_curreny_type,
										'currency_value'	=> $currency
									);
								} else {
									$credits_logs = array(
										'wallet_id'     => $wallet_user->wallet_id,
										'credit'        => $total_credits_cost,
										'logs'          => 'deducted_from_mobile_payment',
										'running_balance' => $wallet_user->wellness_balance - $total_credits_cost - $consultation_fees,
										'where_spend'   => 'in_network_transaction',
										'id'            => $transaction_id,
										'currency_type' => $user_curreny_type,
										'currency_value'	=> $currency
									);
								}

								if($customer_active_plan && $customer_active_plan->account_type == "enterprise_plan") {
									$credits_logs['running_balance'] = 0;
									$credits_logs['unlimited'] = 1;
								}

								if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1 && $user_credits > $consultation_fees) {
									if($user_curreny_type == "myr") {
										$total_credits_cost = $total_credits_cost * $currency;
										$consultation_fees = $consultation_fees * $currency;
										$lite_plan_credits_log = array(
											'wallet_id'     => $wallet_user->wallet_id,
											'credit'        => $consultation_fees,
											'logs'          => 'deducted_from_mobile_payment',
											'running_balance' => $wallet_user->balance - $total_credits_cost - $consultation_fees,
											'where_spend'   => 'in_network_transaction',
											'id'            => $transaction_id,
											'lite_plan_enabled' => 1,
											'currency_type' => $user_curreny_type,
											'currency_value'	=> $currency
										);
									} else {
										$lite_plan_credits_log = array(
											'wallet_id'     => $wallet_user->wallet_id,
											'credit'        => $consultation_fees,
											'logs'          => 'deducted_from_mobile_payment',
											'running_balance' => $wallet_user->balance - $total_credits_cost - $consultation_fees,
											'where_spend'   => 'in_network_transaction',
											'id'            => $transaction_id,
											'lite_plan_enabled' => 1,
											'currency_type' => $user_curreny_type,
											'currency_value'	=> $currency
										);
									}
								}
							}

							try {
								if($spending_type == "medical") {
									$deduct_history = \WalletHistory::create($credits_logs);
									$wallet_history_id = $deduct_history->id;

									if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1 && $user_credits > $consultation_fees) {
										\WalletHistory::create($lite_plan_credits_log);
									}
								} else {
									$deduct_history = \WellnessWalletHistory::create($credits_logs);
									$wallet_history_id = $deduct_history->id;

									if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1 && $user_credits > $consultation_fees) {
										\WellnessWalletHistory::create($lite_plan_credits_log);
									}
								}

								if($deduct_history) {
									try {
										if($spending_type == "medical") {
											if($user_curreny_type == "myr") {
												$wallet->deductCredits($user_id, $payment_credits * $currency);
												if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
													$wallet->deductCredits($user_id, $consultation_fees * $currency);
												}
											} else {
												$wallet->deductCredits($user_id, $payment_credits);
												if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
													$wallet->deductCredits($user_id, $consultation_fees);
												}
											}
										} else {
											if($user_curreny_type == "myr") {
												$wallet->deductWellnessCredits($user_id, $payment_credits * $currency);
												if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
													$wallet->deductWellnessCredits($user_id, $consultation_fees * $currency);
												}
											} else {
												$wallet->deductWellnessCredits($user_id, $payment_credits);
												if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
													$wallet->deductWellnessCredits($user_id, $consultation_fees);
												}
											}
										}

										$trans_id = str_pad($transaction_id, 6, "0", STR_PAD_LEFT);
										$SGD = null;

										if($clinic->currency_type == "myr") {
											$currency_symbol = "MYR ";
											$email_currency_symbol = "MYR";
											$total_amount = $total_amount * $currency;
										} else {
											$email_currency_symbol = "SGD";
											$currency_symbol = 'SGD ';
										}

										$transaction_results = array(
											'clinic_name'       => ucwords($clinic->Name),
											'bill_amount'				=> TransactionHelper::floatvalue($input['input_amount']),
											'consultation_fees'	=> $clinic->currency_type == "myr" ? $data['consultation_fees'] * $currency : $data['consultation_fees'],
											'total_amount'     => $total_amount,
											'paid_by_credits'  => $clinic->currency_type == "myr" ? $credits * $currency : $credits,
											'paid_by_cash'     => $clinic->currency_type == "myr" ? $cash * $currency : $cash,
											'transaction_time'  => date('m-d-Y h:i a', strtotime($date_of_transaction)),
											'transation_id'     => strtoupper(substr($clinic->Name, 0, 3)).$trans_id,
											'services'          => $procedure,
											'currency_symbol'   => $email_currency_symbol,
											'dependent_user'    => $dependent_user,
											'half_credits_payment' => $half_payment,
											'user_id'						=> $customer_id,
											'convert_option'		=> $result->currency_type != $result->default_currency ? true : false,
											'currency'					=> $currency,
											'cap_per_visit'		=> $clinic->currency_type == "myr" ? $result->cap_per_visit * $currency : $result->cap_per_visit
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
												->update(['check_out_time' => $date_of_transaction, 'id' => $transaction_id, 'status' => 1]);
												PusherHelper::sendClinicCheckInRemoveNotification($input['check_in_id'], $check_in->clinic_id);
											}
										}
										
										// deduct visit for enterprise plan user
										if($customer_active_plan->account_type == "enterprise_plan" && (int)$clinic_type->visit_deduction == 1)	{
											MemberHelper::deductPlanHistoryVisit($findUserID);
										}
										try {
											$customer_id = PlanHelper::getCustomerId($user_id);
											$spending = CustomerHelper::getAccountSpendingStatus($customer_id);
											
											// if($spending['medical_method'] == "post_paid") {
												$plan_method = $spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" ? "pre_paid" : "post_paid";
												TransactionHelper::insertTransactionToCompanyInvoice($transaction_id, $user_id, $plan_method);
											// }
										} catch(Exception $e) {
											$email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
											$email['logs'] = 'Mobile Payment Credits Save Transaction Invoice - '.$e;
											$email['emailSubject'] = 'Error log.';
											EmailHelper::sendErrorLogs($email);
										}

										// send email
										$email['member'] = ucwords($user->Name);
										$email['credits'] = number_format($transaction_results['total_amount'], 2);
										$email['bill_amount'] = number_format($transaction_results['bill_amount'], 2);
										$email['transaction_id'] = strtoupper(substr($clinic->Name, 0, 3)).$trans_id;
										$email['trans_id'] = $transaction_id;
										$email['transaction_date'] = date('d F Y, h:ia', strtotime($date_of_transaction));
										$email['health_provider_name'] = ucwords($clinic->Name);
										$email['health_provider_address'] = $clinic->Address;
										$email['health_provider_city'] = $clinic->City;
										$email['health_provider_country'] = $clinic->Country;
										$email['health_provider_phone'] = $clinic->Phone;
										$email['health_provider_postal'] = $clinic->Postal;
										$email['service'] = $procedure;
										$email['emailSubject'] = 'Your Mednefits E-Receipt - '.$email['transaction_id'];
										$email['emailTo'] = $email_address ? $email_address : 'info@medicloud.sg';
										// $email['emailTo'] = 'allan.alzula.work@gmail.com'; 
										$email['emailName'] = ucwords($user->Name);
										$email['url'] = 'http://staging.medicloud.sg';
										$email['clinic_type_image'] = $image;
										$email['transaction_type'] = 'Mednefits Credits';
										$email['emailPage'] = 'email-templates.email-member-successful-transaction';
										$email['dl_url'] = url();
										$email['lite_plan_enabled'] = $clinic_type->lite_plan_enabled;
										$email['lite_plan_status'] = $lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1 ? TRUE : FAlSE;
										$email['total_amount'] = number_format($total_amount, 2);
										$email['paid_by_credits'] = number_format($transaction_results['paid_by_credits'], 2);
										$email['paid_by_cash'] = number_format($transaction_results['paid_by_cash'], 2);
										$email['cap_per_visit'] = $result->cap_per_visit > 0 ? number_format($transaction_results['cap_per_visit'], 2) : 'Not Applicable';
										$email['cap_per_visit_status'] = $result->cap_per_visit > 0 ? true : false;
										$email['consultation'] = $clinic->currency_type == "myr" ? number_format($consultation_fees * $currency, 2) : number_format($consultation_fees, 2);
										$email['currency_symbol'] = $email_currency_symbol;
										$email['pdf_file'] = 'pdf-download.pdf-member-successful-transaction';

										try {
											EmailHelper::sendPaymentAttachment($email);
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
										// $transaction->deleteFailedTransactionHistory($transaction_id);
										// // delete failed wallet history
										// if($spending_type == "medical") {
										// 	$history->deleteFailedWalletHistory($wallet_history_id);
										// 	 // credits back
										// 	$wallet->addCredits($user_id, $credits);
										// } else {
										// 	\WellnessWalletHistory::where('wellness_wallet_history_id', $wallet_history_id)->delete();
										// 	$wallet->addWellnessCredits($user_id, $credits);
										// }
										$returnObject->status = FALSE;
										$returnObject->head_message = 'Panel Submission Error';
										$returnObject->message = 'Payment unsuccessfull. Please try again later';
										EmailHelper::sendErrorLogs($email);
										return Response::json($returnObject);
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
								$returnObject->head_message = 'Panel Submission Error';
								$returnObject->message = 'Payment unsuccessfull. Please try again later';
								EmailHelper::sendErrorLogs($email);
								return Response::json($returnObject);
							}

						} else {
							$returnObject->status = FALSE;
							$returnObject->head_message = 'Panel Submission Error';
							$returnObject->message = 'Cannot process payment credits. Please try again.';
							// send email logs
							$email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
							$email['logs'] = 'Mobile Payment Credits - '.$e;
							$email['emailSubject'] = 'Error log.';
							EmailHelper::sendErrorLogs($email);
							return Response::json($returnObject);
						}
					} catch(Exception $e) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Cannot process payment credits. Please try again.';
						// send email logs
						$email['end_point'] = url('v2/clinic/send_payment - '.$customer_id, $parameter = array(), $secure = null);
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

	public function notifyClinicDirectPayment( )
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
                // return $findUserID;
				if($findUserID){
					$input_amount = 0;
					if(!isset($input['services'])) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Please choose a service.';
						return Response::json($returnObject);
					} 
					if(sizeof($input['services']) == 0) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Please choose a service.';
						return Response::json($returnObject);
					}

					if(is_array($input['services']) == false) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Parameter error. service should be an array';
						return Response::json($returnObject);
					}

					if(!isset($input['clinic_id'])) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Please choose a clinic.';
						return Response::json($returnObject);
					}

					if(isset($input['input_amount'])) {
						$input_amount = TransactionHelper::floatvalue($input['input_amount']);
					}
					$service_id = $input['services'][0];
					if(is_array($service_id)) {
						if(isset($service_id['procedureid'])) {
							$service_id = $service_id['procedureid'];
							$input['services'] = [$service_id];
						}					
					}
					$user_id = StringHelper::getUserId($findUserID);
					// check block access
					$block = PlanHelper::checkCompanyBlockAccess($user_id, $input['clinic_id']);

					if($block) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Clinic not accessible to your Company. Please contact Your company for more information.';
						return Response::json($returnObject);
					}

					// check if enable to access feature
					$transaction_access = MemberHelper::checkMemberAccessTransactionStatusPanel($user_id);

					if($transaction_access)	{
						$returnObject->status = FALSE;
						$returnObject->message = 'Panel function is disabled for your company.';
						return Response::json($returnObject);
					}

					$returnObject->status = TRUE;
					$returnObject->message = 'Success.';
                    // check user type 
					$type = StringHelper::checkUserType($findUserID);
					$lite_plan_status = false;
					$lite_plan_status = StringHelper::newLitePlanStatus($findUserID);
					$currency = 3.00;

					$user = DB::table('user')->where('UserID', $findUserID)->first();
					if($type['user_type'] == 5 && $type['access_type'] == 0 || $type['user_type'] == "5" && $type['access_type'] == "0" || $type['user_type'] == 5 && $type['access_type'] == 1 || $type['user_type'] == "5" && $type['access_type'] == "1")
					{
						$user_id = $findUserID;
						$customer_id = $findUserID;
						$dependent_user = false;
					} else {
                        // find owner
						$owner = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $findUserID)->first();
						$user_id = $owner->owner_id;
						$customer_id = $findUserID;
						$dependent_user = true;
					}

					$customerID = PlanHelper::getCustomerId($user_id);
					$spending = CustomerHelper::getAccountSpendingBasicPlanStatus($customerID);
					$transaction = new Transaction();
					$wallet = new Wallet( );
					$clinic_data = DB::table('clinic')->where('ClinicID', $input['clinic_id'])->first();
					$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic_data->Clinic_Type)->first();

					if(!$dependent_user) {
						$user_plan_history = DB::table('user_plan_history')->where('user_id', $customer_id)->orderBy('created_at', 'desc')->first();
						$customer_active_plan = DB::table('customer_active_plan')
						->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
						->first();
					} else {
						$user_plan_history = DB::table('dependent_plan_history')->where('user_id', $customer_id)->orderBy('created_at', 'desc')->first();
						$customer_active_plan = DB::table('dependent_plans')
											->where('dependent_plan_id', $user_plan_history->dependent_plan_id)
											->first();
					}


					if($customer_active_plan->account_type == "enterprise_plan" && (int)$clinic_type->visit_deduction == 1)	{
						$limit = $user_plan_history->total_visit_limit - $user_plan_history->total_visit_created;
			
						if($limit <= 0) {
							$returnObject->status = FALSE;
							$returnObject->message = 'Maximum of 14 visits already reached.';
							return Response::json($returnObject);
						}
					}

                    // check if multiple services selected
					$multiple = false;
					if(sizeof($input['services']) == 1) {
						$services = $input['services'][0];
						$multiple_service_selection = 0;
						$multiple = false;
					} else {
						$services = 0;
						$multiple_service_selection = 1;
						$multiple = true;
					}

					$wallet_data = $wallet->getUserWallet($user_id);
					$date_of_transaction = null;
					$user_curreny_type = $wallet_data->currency_type;

					if(!empty($input['check_out_time']) && $input['check_out_time'] != null) {
						$date_of_transaction = date('Y-m-d H:i:s', strtotime($input['check_out_time']));
					} else {
						$date_of_transaction = date('Y-m-d H:i:s');
					}

					$clinic_co_payment = TransactionHelper::getCoPayment($clinic_data, $date_of_transaction, $user_id);
					$peak_amount = $clinic_co_payment['peak_amount'];
					$co_paid_amount = $clinic_co_payment['co_paid_amount'];
					$co_paid_status = $clinic_co_payment['co_paid_status'];
					$clinic_peak_status = $clinic_co_payment['clinic_peak_status'];

					if($lite_plan_status && $clinic_type->lite_plan_enabled == 1) {
						$lite_plan_enabled = 1;
						$consultation_fees = $clinic_co_payment['consultation_fees'] == 0 ? $clinic_data->consultation_fees : $clinic_co_payment['consultation_fees'];
					} else {
						$lite_plan_enabled = 0;
						$consultation_fees = 0;
					}

					if($clinic_data->currency_type == "myr") {
						$total_amount = $input_amount / 3;
					} else {
						$total_amount = $input_amount;
					}

					$data = array(
						'UserID'                => $customer_id,
						'ProcedureID'           => $services,
						'date_of_transaction'   => $date_of_transaction,
						'ClinicID'              => $input['clinic_id'],
						'procedure_cost'        => $total_amount,
						'AppointmenID'          => 0,
						'revenue'               => 0,
						'debit'                 => 0,
						'medi_percent'          => $clinic_data->medicloud_transaction_fees,
						'clinic_discount'       => $clinic_data->discount,
						'wallet_use'            => 0,
						'current_wallet_amount' => $wallet_data->balance,
						'credit_cost'           => 0,
						'paid'                  => 1,
						'co_paid_status'        => $co_paid_status,
						'co_paid_amount'        => $co_paid_amount,
						'DoctorID'              => 0,
						'backdate_claim'        => 1,
						'in_network'            => 1,
						'mobile'                => 1,
						'health_provider_done'  => 1,
						'multiple_service_selection' => $multiple_service_selection,
						'spending_type'         => $clinic_type->spending_type,
						'lite_plan_enabled'     => $lite_plan_enabled,
						'currency_type'			=> $clinic_data->currency_type,
						'consultation_fees'		=> $consultation_fees,
						'created_at'			=> $date_of_transaction,
						'updated_at'			=> $date_of_transaction,
						'default_currency'		=> $user_curreny_type
					);

					if($clinic_peak_status) {
						$data['peak_hour_status'] = 1;
						if((int)$clinic_data->co_paid_status == 1) {
							$gst_peak = $peak_amount * $clinic_data->gst_percent;
							$data['peak_hour_amount'] = $peak_amount + $gst_peak;
						} else {
							$data['peak_hour_amount'] = $peak_amount;
						}
					}

					if($currency) {
						$data['currency_amount'] = $currency;
					}

					if($customer_active_plan->account_type == "enterprise_plan" && (int)$clinic_type->visit_deduction == 1)	{
						$data['enterprise_visit_deduction'] = 1;
					}
					
					try {
						$result = $transaction->createTransaction($data);
						$transaction_id = $result->id;

						if($result) {
                            // insert transation services
							$ts = new TransctionServices( );
							$save_ts = $ts->createTransctionServices($input['services'], $transaction_id);
		
							// deduct visit for enterprise plan user
							if($customer_active_plan->account_type == "enterprise_plan" && (int)$clinic_type->visit_deduction == 1)	{
								MemberHelper::deductPlanHistoryVisit($findUserID);
							}

							if($lite_plan_enabled == 1) {
								$wallet_data = DB::table('e_wallet')->where('UserID', $user_id)->first();

								if($data['spending_type'] == "medical") {
									$balance = $wallet_data->balance;
								} else {
									$balance = $wallet_data->wellness_balance;
								}
								
								// check user credits and deduct
								//  || $spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" && $balance < $consultation_fee
								if($balance >= $consultation_fees) {
									// deduct wallet
									$lite_plan_credits_log = array(
										'wallet_id'     => $wallet_data->wallet_id,
										'credit'        => $consultation_fees,
										'logs'          => 'deducted_from_mobile_payment',
										'running_balance' => $balance - $consultation_fees,
										'where_spend'   => 'in_network_transaction',
										'id'            => $transaction_id,
										'lite_plan_enabled' => 1,
										'currency_type' => $user_curreny_type,
									);

									try {
										// create logs
										if($data['spending_type'] == "medical") {
											$deduct_history = \WalletHistory::create($lite_plan_credits_log);
											$wallet_history_id = $deduct_history->id;
										} else {
											$deduct_history = \WellnessWalletHistory::create($lite_plan_credits_log);
											$wallet_history_id = $deduct_history->id;
										}

										if($data['spending_type'] == "medical") {
											$wallet->deductCredits($user_id, $data['co_paid_amount']);
										} else {
											$wallet->deductWellnessCredits($user_id, $data['co_paid_amount']);
										}

										// update transaction
										$update_trans = array(
											'lite_plan_use_credits' => 1
										);

										$transaction->updateTransaction($transaction_id, $update_trans);
										// insert transaction
										// if($spending['medical_method'] == "post_paid") {
											// $plan_method = $spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" ? "pre_paid" : "post_paid";
											$plan_method = $spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" ? "pre_paid" : "post_paid";
											TransactionHelper::insertTransactionToCompanyInvoice($transaction_id, $user_id, $plan_method);
										// }
									} catch(Exception $e) {

										if($data['spending_type'] == "medical") {
											$history = new WalletHistory( );
											$history->deleteFailedWalletHistory($wallet_history_id);
										} else {
											\WellnessWalletHistory::where('wellness_wallet_history_id', $wallet_history_id)->delete();
										}

										$email = [];
										$email['end_point'] = url('v2/clinic/payment_direct', $parameter = array(), $secure = null);
										$email['logs'] = 'Save Claim Transaction With Credits GST - '.$e;
										$email['emailSubject'] = 'Error log.';
										EmailHelper::sendErrorLogs($email);
										return array('status' => FALSE, 'message' => 'Failed to save transaction.');
									}
								} else {
									// insert to spending invoice
									// $plan_method = $spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" ? "post_paid" : "pre_paid";
									$plan_method = "post_paid";
									TransactionHelper::insertTransactionToCompanyInvoice($transaction_id, $user_id, $plan_method);
								}
							}

                            // send notification to browser
							// Notification::sendNotification('Customer Payment - Mednefits', 'Customer '.ucwords($user->Name).' will pay directly to your clinic.', url('app/setting/claim-report', $parameter = array(), $secure = null), $input['clinic_id'], $user->Image);

                            // send realtime update to claim clinic admin
                  			// PusherHelper::sendClinicClaimNotification($transaction_id, $input['clinic_id']);

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

							$returnObject->status = TRUE;
							$returnObject->message = 'Transaction Done.';
						}
					} catch(Exception $e) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Cannot process payment direct to health provider. Please try again.';
			                        // send email logs
						$email = [];
						$email['end_point'] = url('v1/clinic/payment_direct', $parameter = array(), $secure = null);
						$email['logs'] = 'Mobile Payment Direct - '.$e;
						$email['emailSubject'] = 'Error log.';
						EmailHelper::sendErrorLogs($email);
					}

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
					$user_id = StringHelper::getUserId($findUserID);
					$spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';
					$filter = isset($input['filter']) ? $input['filter'] : 'current_term';
					$dates = MemberHelper::getMemberDateTerms($user_id, $filter);
					$user_type = PlanHelper::getUserAccountType($findUserID);
					$lite_plan_status = false;

					$transaction_details = [];
					$ids = StringHelper::getSubAccountsID($findUserID);
					$paginate = [];
					if($dates) {
						if(isset($input['paginate']) && !empty($input['paginate']) && $input['paginate'] == true) {
							$per_page = !empty($input['per_page']) ? $input['per_page'] : 5;
							
							if($user_type == "employee") {
								$transactions = DB::table('transaction_history')
														->whereIn('UserID', $ids)
														->where('created_at', '>=', $dates['start'])
	                  									->where('created_at', '<=', $dates['end'])
														->orderBy('created_at', 'desc')
														->paginate($per_page);
							} else {
								$transactions = DB::table('transaction_history')
														->where('UserID', $findUserID)
														->where('created_at', '>=', $dates['start'])
	                  									->where('created_at', '<=', $dates['end'])
														->orderBy('created_at', 'desc')
														->paginate($per_page);
							}
						} else {
							if($user_type == "employee") {
								$transactions = DB::table('transaction_history')
														->whereIn('UserID', $ids)
														->where('created_at', '>=', $dates['start'])
	                  									->where('created_at', '<=', $dates['end'])
														->orderBy('created_at', 'desc')
														->get();
							} else {
								$transactions = DB::table('transaction_history')
														->where('UserID', $findUserID)
														->where('created_at', '>=', $dates['start'])
	                  									->where('created_at', '<=', $dates['end'])
														->orderBy('created_at', 'desc')
														->get();
							}
							
						}
					} else {
						$transactions = [];
					}

					foreach ($transactions as $key => $trans) {
						if($trans) {
							$receipt_images = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->get();
							$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
							$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
							$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
							$procedure_temp = "";
							$procedure = "";
                            // get services
							if((int)$trans->multiple_service_selection == 1)
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
								if((int)$trans->lite_plan_enabled == 1) {
									$total_amount = $cost + $trans->consultation_fees;
								} else {
									$total_amount = $cost;
								}
								$type = "cash";
							} else {
								$health_provider_status = FALSE;
								if((int)$trans->lite_plan_enabled == 1) {
									if((int)$trans->half_credits == 1) {
										$total_amount = $trans->credit_cost + $trans->consultation_fees + $trans->cash_cost;
                 						 // $total_amount = $trans->credit_cost + $trans->cash_cost;
									} else {
										$total_amount = $trans->credit_cost + $trans->consultation_fees + $trans->cash_cost;
									}
								} else {
									$total_amount = $cost;
								}
								$type = "credits";
							}

							$currency_symbol = null;
							$converted_amount = null;

							if($trans->default_currency == "sgd") {
								$currency_symbol = "SGD";
								$converted_amount = $total_amount;
							} else if($trans->default_currency == "myr" && $trans->currency_type == "myr") {
								$currency_symbol = "MYR";
								$converted_amount = $total_amount * $trans->currency_amount;
								$total_amount = $converted_amount;
							} else if ($trans->default_currency == "myr" && $trans->currency_type == "sgd") {
								$currency_symbol = "MYR";
								$converted_amount = $total_amount * $trans->currency_amount;;
								$total_amount = $converted_amount;
							}

							$format = array(
								'clinic_name'       => $clinic->Name,
								'amount'            => number_format($total_amount, 2),
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
								'refunded'          => (int)$trans->refunded == 1 ? TRUE : FALSE
							);

							array_push($transaction_details, $format);
						}
					}

					if(isset($input['paginate']) && !empty($input['paginate']) && $input['paginate'] == true) {
						$paginate['total'] = $transactions->getTotal();
						$paginate['per_page'] = $transactions->getPerPage();
						$paginate['current_page'] = $transactions->getCurrentPage();
						$paginate['last_page'] = $transactions->getLastPage();
						$paginate['from'] = $transactions->getFrom();
						$paginate['to'] = $transactions->getTo();
						$paginate['data'] = $transaction_details;
						$returnObject->data = $paginate;
					} else {
						$returnObject->data = $transaction_details;
					}

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
					$user_id = StringHelper::getUserId($findUserID);
					$lite_plan_status = false;
					$total_amount = 0;
					$service_credits = false;
					$consultation_credits = false;
					$consultation = 0;
					$wallet_status = false;
					$procedure = "";

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
						    if($transaction->default_currency == "myr") {
						    	$consultation = $logs_lite_plan->credit / $transaction->currency_amount;
						    } else {
									$consultation = $logs_lite_plan->credit;
						    }
							} else if($logs_lite_plan && $transaction->procedure_cost >= 0 && (int)$transaction->lite_plan_use_credits == 1) {
								$consultation_credits = true;
						    // $service_credits = true;
								if($transaction->default_currency == "myr") {
						    	$consultation = $logs_lite_plan->credit / $transaction->currency_amount;
						    } else {
									$consultation = $logs_lite_plan->credit;
						    }
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
						if((int)$transaction->multiple_service_selection == 1)
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
						$cash_cost = 0;

						$procedure_cost = number_format($transaction->procedure_cost, 2);
						if((int)$transaction->health_provider_done == 1) {
							$payment_type = 'Cash';
							if((int)$transaction->lite_plan_enabled == 1 && $wallet_status == true) {
								if((int)$transaction->half_credits == 1) {
									$total_amount = $transaction->credit_cost + $transaction->consultation_fees;
									$cash_cost = $transation->cash_cost;
								} else {
									$total_amount = $transaction->procedure_cost + $transaction->consultation_fees + $transaction->cash_cost;
									$cash_cost = $transaction->procedure_cost;
								}
							} else {
								if((int)$transaction->half_credits == 1) {
									$cash_cost = $transaction->cash_cost;
								} else {
									$cash_cost = $transaction->procedure_cost;
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
							if((int)$transaction->lite_plan_enabled == 1) {
								if((int)$transaction->half_credits == 1) {
									$total_amount = $transaction->credit_cost + $transaction->cash_cost + $transaction->consultation_fees;
									// $total_amount = $transaction->credit_cost + $transaction->cash_cost;
									$cash_cost = $transaction->cash_cost;
								} else {
									$total_amount = $transaction->credit_cost + $transaction->consultation_fees;
									if($transaction->credit_cost > 0) {
										$cash_cost = 0;
									} else {
										$cash_cost = $transaction->procedure_cost - $transaction->consultation_fees;
									}
								}
							} else {
								$total_amount = $transaction->procedure_cost;
								if((int)$transaction->half_credits == 1) {
									$cash_cost = $transaction->cash_cost;
								} else {
									if($transaction->credit_cost > 0) {
										$cash_cost = 0;
									} else {
										$cash_cost = $transaction->procedure_cost;
									}
								}
							}
						}

						if((int)$transaction->half_credits == 1) {
							if((int)$transaction->lite_plan_enabled == 1) {
								if((int)$transaction->health_provider_done == 1) {
									$bill_amount = $transaction->procedure_cost;
								} else {
									$bill_amount = $transaction->procedure_cost - $transaction->consultation_fees;
								}
							} else {
								$bill_amount = 	$transaction->procedure_cost;
							}
						} else {
							if((int)$transaction->lite_plan_enabled == 1) {
								if((int)$transaction->health_provider_done == 1) {
									if((int)$transaction->lite_plan_use_credits == 1) {
										$bill_amount = 	$transaction->procedure_cost;
									} else {
										$bill_amount = 	$transaction->procedure_cost;
									}
								} else {
									if((int)$transaction->lite_plan_use_credits == 1) {
										$bill_amount = 	$transaction->procedure_cost;
									} else {
										$bill_amount = 	$transaction->credit_cost + $transaction->cash_cost;
									}
								}
							} else {
								if((int)$transaction->health_provider_done == 1) {
									$bill_amount = 	$transaction->procedure_cost;
								} else {
									$bill_amount = 	$transaction->procedure_cost;
								}
							}
						}

						$paid_by_credits = $transaction->credit_cost;
						// if((int)$transaction->lite_plan_enabled == 1) {
						// 	if($consultation_credits == true) {
						// 		// if((int)$transaction->half_credits == 1) {
						// 		// 	$paid_by_credits += $consultation;
						// 		// }
						// 	}
						// }

						if($transaction->cap_per_visit == $transaction->credit_cost + $consultation && (int)$transaction->half_credits == 1 && $consultation_credits == true) {
							$paid_by_credits = $transaction->credit_cost + $consultation;
						} else {
							if($consultation_credits == true) {
								// if((int)$transaction->half_credits == 1) {
									$paid_by_credits += $consultation;
								// }
							}
						}

						$lite_plan_status = (int)$transaction->lite_plan_enabled == 1 ? TRUE : FALSE;
						
						if((int)$transaction->lite_plan_enabled == 1 && $wallet_status == false) {
							$service_credits = false;
							$consultation_credits = false;
							$lite_plan_status = false;
						}

						$consultation_fee = 0;
						$lite_plan_status = (int)$transaction->lite_plan_enabled == 1 ? TRUE : FALSE;

						if((int)$transaction->lite_plan_enabled == 1 && $wallet_status == false) {
							$service_credits = false;
							$consultation_credits = false;
							$lite_plan_status = false;
						}

						if($transaction->cap_per_visit > 0) {
							$half_credits = true;
						}

						if($transaction->credit_cost == 0 && $transaction->consultation_fees > 0 && $transaction->lite_plan_enabled == 1) {
							$paid_by_credits = $transaction->consultation_fees;
						}

						if($transaction->default_currency == "myr" && $transaction->currency_type == "myr") {
							$currency_symbol = "MYR";
							$temp_total_amount = $total_amount;
							$temp_bill_amount = $bill_amount;
							$temp_cash_cost = $cash_cost;
							$temp_paid_by_credits = $paid_by_credits;
							$temp_cap_per_visit = $transaction->cap_per_visit;
							$total_amount = $total_amount * $transaction->currency_amount;
							$bill_amount = $bill_amount * $transaction->currency_amount;
							$cash_cost = $cash_cost * $transaction->currency_amount;
							$paid_by_credits = $paid_by_credits * $transaction->currency_amount;
							$transaction->cap_per_visit = $transaction->cap_per_visit * $transaction->currency_amount;
							if((int)$transaction->lite_plan_enabled == 1) {
								$consultation_fee = $consultation;
							}

							$temp_consultation_fee = $consultation_fee;
							$consultation_fee = $consultation_fee * $transaction->currency_amount;

							$total_amount_converted = $temp_total_amount * $transaction->currency_amount;
							$bill_amount_converted = $temp_bill_amount * $transaction->currency_amount;;
							$consultation_fee_converted = $temp_consultation_fee * $transaction->currency_amount;
							$paid_by_cash_converted = $temp_cash_cost * $transaction->currency_amount;
							$paid_by_credits_converted = $temp_paid_by_credits * $transaction->currency_amount;
							$cap_per_visit_converted = $temp_cap_per_visit * $transaction->currency_amount;
						} else if($transaction->default_currency == "myr" && $transaction->currency_type == "sgd") {
							$currency_symbol = "MYR";
							if((int)$transaction->lite_plan_enabled == 1) {
								$consultation_fee = $consultation;
							}
							$temp_consultation_fee = $consultation_fee;
							// $consultation_fee = $consultation_fee * $transaction->currency_amount;
							$total_amount_converted = $total_amount * $transaction->currency_amount;
							$bill_amount_converted = $bill_amount * $transaction->currency_amount;;
							$consultation_fee_converted = $temp_consultation_fee * $transaction->currency_amount;
							$paid_by_cash_converted = $cash_cost * $transaction->currency_amount;
							$paid_by_credits_converted = $paid_by_credits * $transaction->currency_amount;
							$cap_per_visit_converted = $transaction->cap_per_visit * $transaction->currency_amount;

						} else {
							$currency_symbol = "SGD";
							if((int)$transaction->lite_plan_enabled == 1) {
								$consultation_fee = $consultation;
							}
							$temp_consultation_fee = $consultation_fee;
							$temp_total_amount = $total_amount;
							$temp_bill_amount = $bill_amount;
							$temp_cash_cost = $cash_cost;
							$temp_paid_by_credits = $paid_by_credits;
							$temp_cap_per_visit = $transaction->cap_per_visit;
							$total_amount_converted = $total_amount;
							$bill_amount_converted = $bill_amount;
							$consultation_fee_converted = $consultation_fee;
							$paid_by_cash_converted = $cash_cost;
							$paid_by_credits_converted = $paid_by_credits;
							$cap_per_visit_converted = $transaction->cap_per_visit;


							$total_amount_converted = $temp_total_amount * $transaction->currency_amount;
							$bill_amount_converted = $temp_bill_amount * $transaction->currency_amount;;
							$consultation_fee_converted = $temp_consultation_fee * $transaction->currency_amount;
							$paid_by_cash_converted = $temp_cash_cost * $transaction->currency_amount;
							$paid_by_credits_converted = $temp_paid_by_credits * $transaction->currency_amount;
							$cap_per_visit_converted = $temp_cap_per_visit * $transaction->currency_amount;
						}

						if($transaction->default_currency == "myr" && $transaction->currency_type == "myr" || $transaction->default_currency == "myr" && $transaction->currency_type == "sgd") {
							$default_currency = "myr";
						} else {
							$default_currency = "sgd";
						}

						$transaction_details = array(
							'clinic_name'       => $clinic->Name,
							'clinic_image'      => $clinic->image ? $clinic->image : 'https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514443281/rjfremupirvnuvynz4bv.jpg',
							'clinic_type'       => $type,
							'clinic_type_image' => $image,
							'total_amount'       => number_format($total_amount, 2),
							'total_amount_converted'       => number_format($total_amount_converted, 2),
							"currency_symbol" => $currency_symbol,
							'transaction_id'    => (string)$id,
							'date_of_transaction' => date('d-m-Y, h:ia', strtotime($transaction->date_of_transaction)),
							'customer'            => ucwords($customer->Name),
							'payment_type'		=> $payment_type,
							'bill_amount'				=> number_format($bill_amount, 2),
							'bill_amount_converted'				=> number_format($bill_amount_converted, 2),
							'consultation_fee'	=> number_format($consultation_fee, 2),
							'consultation_fee_converted'	=> number_format($consultation_fee_converted, 2),
							'paid_by_cash'      => number_format($cash_cost, 2),
							'paid_by_cash_converted'      => number_format($paid_by_cash_converted, 2),
							'paid_by_credits'      => number_format($paid_by_credits, 2),
							'paid_by_credits_converted'      => number_format($paid_by_credits_converted, 2),
							'files'             => $doc_files,
							'lite_plan'         => $lite_plan_status,
							'lite_plan_enabled' => $transaction->lite_plan_enabled,
							'cap_transaction'   => $half_credits,
							'cap_per_visit'     => number_format($transaction->cap_per_visit, 2),
							'cap_per_visit_converted'     => number_format($cap_per_visit_converted, 2),
							'services' => $service,
							'convert_option'		=> $transaction->default_currency != $transaction->currency_type ? true : false,
							'currency_amount'		=> $transaction->currency_amount,
							'currency_symbols'	=> ["SGD", "MYR"],
							'default_currency'	=> $default_currency
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

					$check = DB::table('transaction_history')->where('transaction_id', $transaction_id)->first();

					if(!$check) {
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
							if($file_size > 20000000) {
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

					try {
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
								$result = $trans_docs->saveReceipt($receipt);
							} else if($file->getClientOriginalExtension() == "xls" || $file->getClientOriginalExtension() == "xlsx") {
								$receipt = array(
									'user_id'           => $findUserID,
									'transaction_id'    => $transaction_id,
									'file'  => $file_name,
									'type'  => "excel"
								);
								$file->move(public_path().'/receipts/', $file_name);
								$aws_upload = true;
								$result = $trans_docs->saveReceipt($receipt);
							} else {
								// $random = StringHelper::get_random_password(6);
								// $file_name = $random.'-'.$random;
								// $file->move(public_path().'/temp_uploads/', $file_name);
								// $result_doc = Queue::connection('redis_high')->push('\InNetworkFileUploadQueue', array('file' => public_path().'/temp_uploads/'.$file_name, 'transaction_id' => $transaction_id, 'user_id' => $check->UserID));
								//   // $file_address = url('temp_uploads', $parameter = array(), $secure = null).'/'.$file_name;
								// 	$file_address = url('temp_uploads', $parameter = array(), $secure = null).$file_name;
	       //          $result = array(
	       //            'file'      => $file_address,
						  //      	'type'      => "image",
						  //      	'transaction_id'    => $transaction_id,
						  //      	'user_id'		=> $check->UserID,
						  //      	'id'				=> rand(),
						  //      	'created_at'	=> date('Y-m-d H:i:s'),
						  //      	'updated_at' => date('Y-m-d H:i:s')
	       //          );
								$image = \Cloudinary\Uploader::upload($file->getPathName());
								$receipt = array(
									'user_id'           => $findUserID,
									'file'      => $image['secure_url'],
									'type'      => "image",
									'transaction_id'    => $transaction_id,
								);
								$result = $trans_docs->saveReceipt($receipt);
							}

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
					} catch(Exception $e) {
						$email['end_point'] = url('v2/user/upload_in_network_receipt_bulk', $parameter = array(), $secure = null);
						$email['logs'] = 'In-Network Upload Receipt - '.$e;
						$email['emailSubject'] = 'Error log. - In-Network Upload Receipt';
						EmailHelper::sendErrorLogs($email);
					}

					$returnObject->status = TRUE;
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

	public function getCheckInData( )
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

					if(empty($input['check_in_id']) || $input['check_in_id'] == null) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Check-In ID is required.';
						return Response::json($returnObject);
					}

					$check_in = DB::table('user_check_in_clinic')
					->where('check_in_id', $input['check_in_id'])
					->where('status', 0)
					->first();

					if(!$check_in) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Check In Registration removed by Health Provider. Please make another Check-In Registration.';
						$returnObject->check_in_status_removed = true;
						return Response::json($returnObject);
					}

					// check if still valid
					$check_in_expiry_time = strtotime('+120 minutes', strtotime($check_in->check_in_time));
					if(!empty($input['check_out_time']) && $input['check_out_time'] != null) {
						$today = strtotime(date('Y-m-d H:i:s', strtotime($input['check_out_time'])));
					} else {
						$today = strtotime(date('Y-m-d H:i:s'));
					}
					// return $check_in_expiry_time;
					// return date('d M, h:i a', $check_in_expiry_time);
					if($today > $check_in_expiry_time) {
						$returnObject->status = FALSE;
						$returnObject->message = 'Check In Registration is expired. Please make another Check-In Registration.';
						$returnObject->check_in_status_removed = true;
						return Response::json($returnObject);
					}

					$user = DB::table('user')->where('UserID', $check_in->user_id)->first();
					$clinic = DB::table('clinic')->where('ClinicID', $check_in->clinic_id)->first();
					$data['clinic_id'] = $clinic->ClinicID;
					$data['clinic_name'] = $clinic->Name;
					$data['image_url'] = $clinic->image;
					$data['check_in_time'] = date('d M, h:i a', strtotime($check_in->check_in_time));
					$data['cap_per_visit_amount'] = $check_in->cap_per_visit;
					$data['nric'] = $user->NRIC;
					$data['member'] = ucwords($user->Name);
					if($check_in->currency_symbol == "myr") {
						$cap_currency_symbol = "RM";
					} else {
						$cap_currency_symbol = "S$";
					}
					$data['cap_currency_symbol'] = $cap_currency_symbol;
					$returnObject->status = TRUE;
					$returnObject->message = 'Success.';
					$returnObject->data = $data;
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