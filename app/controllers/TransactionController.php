<?php
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
class TransactionController extends BaseController {

	public function calculateTransaction( )
	{
		$input = Input::all();
		$transaction = new Transaction();
		return $transaction->calculateTransaction($input['id'], $input['amount'], $input['credit_use_status']);
	}

	public function newCalculateTransaction( )
	{
		$input = Input::all();
		$transaction = new Transaction();
		return $transaction->newCalculateTransaction($input['id'], $input['amount']);
	}

	public function CalculateCoPaidTransaction( )
	{
		$input = Input::all();
		$transaction = new Transaction();
		return $transaction->CalculateCoPaidTransaction($input['id'], $input['amount']);
	}

	public function checkAppointmentTransaction( )
	{
		$transaction = new Transaction( );
		$input = Input::all();
		$result = $transaction->getTransaction($input['appointment_id']);
		if(sizeof($result) == 0) {
			$calendar = new CalendarController();
			$result = $calendar->concludedAppointment( );
			if($result) {
				return 0;
			}
		} else {
			$wallet = new Wallet( );
			$wallet_result = $wallet->getWalletAmount($result[0]->wallet_id);
			if( (int)$wallet_result->balance >= (int)$result[0]->procedure_cost) {
				$status_show = false;
        		// $deducted_temp = (int)$wallet_result->balance - (int)$result[0]->procedure_cost;
                // $deducted = (int)$
			} else {
				$status_show = true;
				$deducted = 0;
			}

			return array(
				'transaction'		=> $result[0],
				'status'			=> $status_show,
				'deducted'			=> "-$".$result[0]->procedure_cost
			);
		}
	}

	public function finishTransaction( )
	{
		$input = Input::all();
		$transaction = new Transaction( );
		$clinic = new Clinic( );
		$data = array(
			'paid'			=> 1,
			'updated_at'    => Carbon::now()
		);
		$result = $transaction->finishTransaction($data, $input['transaction_id']);
		// $transaction_result = $transaction->getTransactionDetails($input['transaction_id']);
		// $clinic_email = $clinic->FindClinicProfile($transaction_result->ClinicID);

		if($result)
		{
			$wallet = new Wallet();
			$wallet_history = new WalletHistory( );
			// $corporate = new Corporate();

			$wallet_result = $wallet->getWalletAmount($input['wallet_id']);
			$amount = (int)$wallet_result->balance - (int)$input['credit'];

			if($amount <= 0) {
				$amount = 0;
			}
			$wallet_res = $wallet->updateWallet($wallet_result->UserID, $amount);
			$data_new = array(
				'wallet_id'         => $input['wallet_id'],
				'credit'            => $amount,
				'logs'              => 'transaction',
				'created_at'        => Carbon::now(),
				'updated_at'        => Carbon::now()
			);
			$wallet_history->createWalletHistory($data_new);
            // $corporate->deductCredits($wallet_result->UserID, $amount);
			$calendar = new CalendarController();
			return $calendar->concludedAppointmentFromTransaction( );
		} else {
			$calendar = new CalendarController();
			return $calendar->concludedAppointment( );
		}
	}

	public function fixedGP( )
	{
		$transaction = new Clinic( );
		// $transaction = new Transaction( );
		$results =  \DB::table('clinic')
		->join('clinic_types', 'clinic_types.ClinicTypeID', '=', 'clinic.Clinic_Type')
				// ->join('transaction_history', 'transaction_history.ClinicID', '=', 'clinic.ClinicID')
		->where('clinic.Active', 1)
		->get();
		$status = array();

		foreach ($results as $key => $value) {
			if($value->discount_type == "fixed") {
				$temp = array(
					'status'	=> $transaction->updateGP($value->ClinicID)
				);
			} else {
				$temp = array(
					'status'	=> $transaction->updatePercent($value->ClinicID)
				);
			}
			array_push($status, $temp);
		}
		return $status;
	}

	// public function concludeClaim( )
	// {
	// 	$input = Input::all();
	// 	$clinic = new Clinic;
	// 	$transaction = new Transaction();
	// 	$wallet = new Wallet();
	// 	$wallet_history = new WalletHistory( );
	// 	$temp = [];
	// 	foreach ($input as $key => $value) {

	// 		if(isset($input['amount'])) {
	// 			$result = \DB::table('clinic')
	// 				->join('clinic_types', 'clinic_types.ClinicTypeID', '=', 'clinic.Clinic_Type')
	// 				->where('clinic.Active', 1)
	// 				->where('clinic.ClinicID', $value['clinic_id'])
	// 				->first();
	// 			if($result->co_paid == 0) {
	// 				$update_result = $transaction->newCalculateTransaction($value['transaction_id'], $value['amount']);
	// 				if($update_result['status'] == true) {
	// 					$data = array(
	// 						'paid'					=> 1,
	// 						'updated_at'    => Carbon::now()
	// 					);
	// 					$transaction->finishTransaction($data, $value['transaction_id']);

	// 					$wallet_result = $wallet->getWalletAmount($update_result['wallet_id']);
	// 					$amount = floatval($wallet_result->balance) - floatval($update_result['credit']);

	// 					if($amount <= 0) {
	// 						$amount = 0;
	// 					}
	// 					$wallet_res = $wallet->updateWallet($wallet_result->UserID, $amount);
	// 					$data_new = array(
	//               'wallet_id'         => $update_result['wallet_id'],
	//               'credit'            => $amount,
	//               'logs'              => 'transaction',
	//               'created_at'        => Carbon::now(),
	//               'updated_at'        => Carbon::now()
	//           );
	//           $wallet_history->createWalletHistory($data_new);
	//         	$calendar = new CalendarController();
	//         	$calendar->concludedAppointmentFromClaimTransaction($update_result, $value['appointment_id']);
	// 				}
	// 			}
	// 		}

	// 	}
	// 	return 1;

	// }

	public function saveBulkTransaction( )
	{
		$getSessionData = StringHelper::getMainSession(3);
		$clinic_id = $getSessionData->Ref_ID;
		$clinic_data = DB::table('clinic')->where('ClinicID', $clinic_id)->first();
		if($getSessionData != FALSE){
			$input = Input::all();
			$transaction = new Transaction();

			foreach($input as $key => $value) {
			// return date('Y-m-d', strtotime($value['display_book_date']));
				if($input['back_date'] == 1) {
					$temp = array(
						'UserID'							=> $value['id'],
						'ProcedureID'					=> $value['procedure'],
						'date_of_transaction'	=>  date('Y-m-d H:i:s', strtotime($value['display_book_date'])),
						'ClinicID'						=> $clinic_id,
						'procedure_cost'			=> $value['amount'],
						'AppointmenID'				=> 0,
						'revenue'							=> 0,
						'debit'								=> 0,
						'medi_percent'				=> $clinic_data->medicloud_transaction_fees,
						'clinic_discount'			=> $clinic_data->discount,
						'wallet_use'					=> 0,
						'current_wallet_amount' => 0,
						'credit_cost'					=> 0,
						'paid'								=> 1,
						'co_paid_status'			=> $clinic_data->co_paid_status,
						'DoctorID'						=> 0,
						'backdate_claim'			=> 1,
						'in_network'					=> 1,
						'health_provider_done' => 1
					);
					$transaction->createTransaction($temp);
				} else if($input['health_provider'] == 1) {
					$temp = array(
						'procedure_cost' => $input['amount']
					);

					$transaction->updateTransaction($input['transaction_id'], $temp);
				}
			}

			return self::insertOrCheckInInvoice($clinic_id, date('Y-m-d', strtotime($value['book_date'])));
			// insert or check in invoice
			return array(
				'status'	=> TRUE,
				'message'	=> 'Success.'
			);
		}else{
			return Redirect::to('provider-portal-login');
		}
	}

	public function submitClaim( )
	{
		$input = Input::all();

		$getSessionData = StringHelper::getMainSession(3);
		$clinic_id = $getSessionData->Ref_ID;
		$clinic_data = DB::table('clinic')->where('ClinicID', $clinic_id)->first();

		if($getSessionData != FALSE){
			$user_id = $input['id'];
			$owner_id = StringHelper::getUserId($user_id);
			$transaction = new Transaction();
			$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic_data->Clinic_Type)->first();
			$lite_plan_status = false;
			$clinic_peak_status = false;
			$consultation_fees = 0;
			$lite_plan_status = StringHelper::newLitePlanStatus($input['id']);

			if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
				$lite_plan_enabled = 1;
			} else {
				$lite_plan_enabled = 0;
			}

			if((int)$input['back_date'] == 1) {

				$user_plan_history = DB::table('user_plan_history')->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
				$customer_active_plan = DB::table('customer_active_plan')
				->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
				->first();

				if($customer_active_plan->account_type == "enterprise_plan" && (int)$clinic_type->visit_deduction == 1)	{
					$limit = $user_plan_history->total_visit_limit - $user_plan_history->total_visit_created;
		
					if($limit <= 0) {
						return array('status' => FALSE, 'message' => 'Maximum of 14 visit already reach');
					}
				}

				// check if is a valid date
				if(!strtotime($input['transaction_date'])) {
					return array('status' => FALSE, 'message' => 'Date/Time of Visit is not a valid date.');
				}

				if(sizeof($input['procedure_ids']) == 0) {
					return array('status' => FALSE, 'message' => 'Please select a service.');
				}

				if(empty($input['id']) || $input['id'] == null) {
					return array('status' => FALSE, 'message' => 'Please indicate the Customer.');
				}

				if(!is_numeric($input['amount'])) {
					return array('status' => FALSE, 'message' => 'Amount should be a number.');
				}

				// check if multiple services

				if(sizeof($input['procedure_ids']) == 1) {
					$multiple = 0;
				} else if(sizeof($input['procedure_ids']) > 1) {
					$multiple = 1;
				}

				$wallet_data = DB::table('e_wallet')->where('UserID', $owner_id)->first();
				$currency_data = DB::table('currency_options')->where('currency_type', $wallet_data->currency_type)->first();

				if($currency_data) {
					$currency = $currency_data->currency_value;
				} else {
					$currency = 3.00;
				}

				$peak_amount = 0;
				$clinic_co_payment = TransactionHelper::getCoPayment($clinic_data, $input['transaction_date'], $owner_id);
				$peak_amount = $clinic_co_payment['peak_amount'];
				$co_paid_amount = $clinic_co_payment['co_paid_amount'];
				$co_paid_status = $clinic_co_payment['co_paid_status'];
				$clinic_peak_status = $clinic_co_payment['clinic_peak_status'];
				$consultation_fees = $clinic_co_payment['consultation_fees'] == 0 ? $clinic_data->consultation_fees : $clinic_co_payment['consultation_fees'];

				$temp = array(
					'UserID'				=> $input['id'],
					'ProcedureID'			=> $input['procedure_ids'][0],
					'date_of_transaction'	=> date('Y-m-d H:i:s', strtotime($input['transaction_date'])),
					'claim_date'			=> date('Y-m-d H:i:s'),
					'ClinicID'				=> $clinic_id,
					'procedure_cost'		=> $input['amount'],
					'cash_cost'		=> $input['amount'],
					'AppointmenID'			=> 0,
					'revenue'				=> 0,
					'debit'					=> 0,
					'medi_percent'				=> $clinic_data->medicloud_transaction_fees,
					'clinic_discount'			=> $clinic_data->discount,
					'wallet_use'					=> 0,
					'current_wallet_amount' => 0,
					'credit_cost'					=> 0,
					'paid'								=> 1,
					'co_paid_status'			=> $co_paid_status,
					'co_paid_amount'			=> $co_paid_amount,
					'DoctorID'						=> 0,
					'backdate_claim'			=> 1,
					'in_network'					=> 1,
					'health_provider_done' => 1,
					'multiple_service_selection' => $multiple,
					'currency_type'			=> $clinic_data->currency_type,
					'currency_amount'			=> $currency,
					'lite_plan_enabled'     => $lite_plan_enabled,
					'consultation_fees'		=> $consultation_fees,
					'default_currency'		=> $wallet_data->currency_type
				);

				if($wallet_data->currency_type == "myr") {
					$consultation_fees = $consultation_fees * $currency;
					$temp['procedure_cost'] = $temp['procedure_cost'] / $currency;
				}

				if($clinic_peak_status) {
					$temp['peak_hour_status'] = 1;
					if($clinic_data->co_paid_status == 1 || $clinic_data->co_paid_status == "1") {
						$gst_peak = $peak_amount * $clinic_data->gst_percent;
						$temp['peak_hour_amount'] = $peak_amount + $gst_peak;
					} else {
						$temp['peak_hour_amount'] = $peak_amount;
					} 
				}

				if($customer_active_plan->account_type == "enterprise_plan" && (int)$clinic_type->visit_deduction == 1)	{
					$temp['enterprise_visit_deduction'] = 1;
				}

				try {
					$result = $transaction->createTransaction($temp);

					if($result) {
						if($lite_plan_enabled == 1) {
							$transaction_data = DB::table('transaction_history')->where('transaction_id', $result->id)->first();
							// $user_id = PlanHelper::getDependentOwnerID($input['id']);
							
							if($transaction_data->spending_type == "medical") {
								$balance = $wallet_data->balance;
							} else {
								$balance = $wallet_data->wellness_balance;
							}
							// check user credits and deduct
	
							if($balance >= $consultation_fees) {
								$wallet = new Wallet( );
								// deduct wallet
								$lite_plan_credits_log = array(
									'wallet_id'     => $wallet_data->wallet_id,
									'credit'        => $consultation_fees,
									'logs'          => 'deducted_from_mobile_payment',
									'running_balance' => $balance - $consultation_fees,
									'where_spend'   => 'in_network_transaction',
									'id'            => $result->id,
									'lite_plan_enabled' => 1,
								);
	
								try {
									// create logs
									if($transaction_data->spending_type == "medical") {
										$deduct_history = \WalletHistory::create($lite_plan_credits_log);
										$wallet_history_id = $deduct_history->id;
									} else {
										$deduct_history = \WellnessWalletHistory::create($lite_plan_credits_log);
										$wallet_history_id = $deduct_history->id;
									}
	
									if($transaction_data->spending_type == "medical") {
										$wallet->deductCredits($owner_id, $transaction_data->co_paid_amount);
									} else {
										$wallet->deductWellnessCredits($owner_id, $transaction_data->co_paid_amount);
									}
	
									// update transaction
									$update_trans = array(
										'lite_plan_use_credits' => 1
									);
	
									$transaction->updateTransaction($result->id, $update_trans);
	
								} catch(Exception $e) {
	
									if($transaction_data->spending_type == "medical") {
										$history = new WalletHistory( );
										$history->deleteFailedWalletHistory($wallet_history_id);
									} else {
										\WellnessWalletHistory::where('wellness_wallet_history_id', $wallet_history_id)->delete();
									}
	
									$email = [];
									$email['end_point'] = url('clinic/save/claim/transaction', $parameter = array(), $secure = null);
									$email['logs'] = 'Save Claim Transaction With Credits GST - '.$e->getMessage();
									$email['emailSubject'] = 'Error log.';
									EmailHelper::sendErrorLogs($email);
									return array('status' => FALSE, 'message' => 'Failed to save transaction.');
								}
							}
						}
	
						// deduct visit for enterprise plan user
						if($customer_active_plan->account_type == "enterprise_plan" && (int)$clinic_type->visit_deduction == 1)	{
							MemberHelper::deductPlanHistoryVisit($user_id);
						}
					}
					
					if($multiple == 1) {
						$transaction_services = new TransctionServices( );
						$transaction_services->createTransctionServices($input['procedure_ids'], $result->id);
					}
					
				} catch(Exception $e) {
					// send email logs
					$email = [];
					$email['end_point'] = url('clinic/save/claim/transaction', $parameter = array(), $secure = null);
					$email['logs'] = 'Save Claim Transaction - '.$e->getMessage();
					$email['emailSubject'] = 'Error log.';
					EmailHelper::sendErrorLogs($email);
					return array('status' => FALSE, 'message' => 'Failed to save transaction.');
				}
			} else {
			  // } else if((int)$input['health_provider'] == 1) {
				$clinic_peak_status = false;
				$transaction_data = DB::table('transaction_history')->where('transaction_id', $input['transaction_id'])->first();
				if(!is_numeric($input['amount'])) {
					return array('status' => FALSE, 'message' => 'Amount should be a number.');
				}

				$peak_amount = 0;
				$clinic_co_payment = TransactionHelper::getCoPayment($clinic_data, $transaction_data->date_of_transaction, $owner_id);
				$peak_amount = $clinic_co_payment['peak_amount'];
				$co_paid_amount = $clinic_co_payment['co_paid_amount'];
				$co_paid_status = $clinic_co_payment['co_paid_status'];
				$clinic_peak_status = $clinic_co_payment['clinic_peak_status'];
				$consultation_fees = $clinic_co_payment['consultation_fees'] == 0 ? $clinic_data->consultation_fees : $clinic_co_payment['consultation_fees'];

				$temp = array(
					'procedure_cost' 	=> $input['amount'],
					'claim_date'		=> date('Y-m-d H:i:s'),
					'co_paid_status'	=> $co_paid_status,
					'co_paid_amount'	=> $co_paid_amount,
					'medi_percent'		=> $clinic_data->medicloud_transaction_fees,
					'clinic_discount'	=> $clinic_data->discount,
					'paid'				=> 1,
					'currency_type'		=> $clinic_data->currency_type,
					'currency_amount'	=> $input['currency_amount'] ? $input['currency_amount'] : 3,
					'consultation_fees'	=> $consultation_fees
				);

				if($clinic_peak_status) {
					$temp['peak_hour_status'] = 1;
					if($clinic_data->co_paid_status == 1 || $clinic_data->co_paid_status == "1") {
						$gst_peak = $peak_amount * $clinic_data->gst_percent;
						$temp['peak_hour_amount'] = $peak_amount + $gst_peak;
					} else {
						$temp['peak_hour_amount'] = $peak_amount;
					} 
				}

				try {
					$result =  $transaction->updateTransaction($input['transaction_id'], $temp);
					if($lite_plan_enabled == 1) {
						$wallet_data = DB::table('e_wallet')->where('UserID', $owner_id)->first();
						
						if($transaction_data->spending_type == "medical") {
							$balance = $wallet_data->balance;
						} else {
							$balance = $wallet_data->wellness_balance;
						}
						// check user credits and deduct

						if($balance >= $consultation_fees) {
							$wallet = new Wallet( );
							// deduct wallet
							$lite_plan_credits_log = array(
								'wallet_id'     => $wallet_data->wallet_id,
								'credit'        => $consultation_fees,
								'logs'          => 'deducted_from_mobile_payment',
								'running_balance' => $balance - $consultation_fees,
								'where_spend'   => 'in_network_transaction',
								'id'            => $input['transaction_id'],
								'lite_plan_enabled' => 1,
							);

							try {
								// create logs
								if($transaction_data->spending_type == "medical") {
									$deduct_history = \WalletHistory::create($lite_plan_credits_log);
									$wallet_history_id = $deduct_history->id;
								} else {
									$deduct_history = \WellnessWalletHistory::create($lite_plan_credits_log);
									$wallet_history_id = $deduct_history->id;
								}

								if($transaction_data->spending_type == "medical") {
									$wallet->deductCredits($owner_id, $transaction_data->co_paid_amount);
								} else {
									$wallet->deductWellnessCredits($owner_id, $transaction_data->co_paid_amount);
								}

								// update transaction
								$update_trans = array(
									'lite_plan_use_credits' => 1
								);

								$transaction->updateTransaction($input['transaction_id'], $update_trans);

							} catch(Exception $e) {

								if($transaction_data->spending_type == "medical") {
									$history = new WalletHistory( );
									$history->deleteFailedWalletHistory($wallet_history_id);
								} else {
									\WellnessWalletHistory::where('wellness_wallet_history_id', $wallet_history_id)->delete();
								}

								$email = [];
								$email['end_point'] = url('clinic/save/claim/transaction', $parameter = array(), $secure = null);
								$email['logs'] = 'Save Claim Transaction With Credits GST - '.$e;
								$email['emailSubject'] = 'Error log.';
								EmailHelper::sendErrorLogs($email);
								return array('status' => FALSE, 'message' => 'Failed to save transaction.');
							}
						}
					}
				} catch(Exception $e) {
					$email = [];
					$email['end_point'] = url('clinic/save/claim/transaction', $parameter = array(), $secure = null);
					$email['logs'] = 'Save Claim Transaction - '.$e;
					$email['emailSubject'] = 'Error log.';
					EmailHelper::sendErrorLogs($email);
					return array('status' => FALSE, 'message' => 'Failed to save transaction.');
				}
			}
			self::insertOrCheckInInvoice($clinic_id, date('Y-m-d'));
			return array('status' => TRUE, 'result' => $result);
		} else {
			return Redirect::to('provider-portal-login');
		}


	}
	public function insertOrCheckInInvoice($id, $date)
	{

		$input = array(
			'start_date'	=> date('Y-m-01', strtotime($date)),
			'end_date'		=> date('Y-m-t', strtotime($date)),
			'clinic_id'		=> $id
		);

		// return $input;
		$invoice = new InvoiceRecord();
		$transaction = new Transaction();
		$payment_record = new PaymentRecord();
		$invoice_record_detail = new InvoiceRecordDetails();

		$check_invoice = $invoice->checkInvoice($input);
			// return $check_invoice;
		if($check_invoice) {
			$invoice_id = $check_invoice['invoice_id'];
			$invoice_data =  $check_invoice;
			$transaction_list = $transaction->getDateTransaction($check_invoice);
			$result_inserted_invoice = $invoice->insertOrUpdate($transaction_list, $check_invoice['invoice_id'], $check_invoice['clinic_id']);
			$check_payment_record = $payment_record->insertOrGet($check_invoice['invoice_id'], $input['clinic_id']);
		} else {
			$result_create = $invoice->createInvoice($input);
			$invoice_data = $result_create;
			$invoice_id = $result_create->id;
			$transaction_list = $transaction->getDateTransaction($result_create);
			$result_inserted_invoice = $invoice->insertOrUpdate($transaction_list, $result_create->id, $input['clinic_id']);
			$check_payment_record = $payment_record->insertOrGet($result_create->id, $input['clinic_id']);
		}

		return $invoice->checkInvoice($input);
	}

	public function getTransactionBackDate( )
	{
		$getSessionData = StringHelper::getMainSession(3);
		$final_data = [];
		// $getSessionData->Ref_ID;
		if($getSessionData != FALSE){
			$data = DB::table('transaction_history')
			->join('user', 'user.UserID', '=', 'transaction_history.UserID')
			->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_history.ProcedureID')
			->where('transaction_history.ClinicID', $getSessionData->Ref_ID)
			->where('transaction_history.backdate_claim', 1)
			->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'clinic_procedure.ProcedureID', 'clinic_procedure.Name as procedure_name', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status')
			->get();

			foreach ($data as $key => $value) {

				if($value->co_paid_status == 0) {
					if(strrpos($value->clinic_discount, '%')) {
						$percentage = chop($value->clinic_discount, '%');
						$discount = $percentage / 100 + $value->medi_percent / 100;
						$sub = $value->procedure_cost * $discount;
						$final = $value->procedure_cost - $sub;
					} else {
              // return 'use whole number';
						$discount_clinic = str_replace('$', '', $value->clinic_discount);
						$discount = $discount_clinic;
						$final = $value->procedure_cost - $discount;
					}

				} else {
					$final = $value->procedure_cost;
				}

				$temp = array(
					'ClinicID'							=> $value->ClinicID,
					'NRIC'									=> $value->NRIC,
					'ProcedureID'						=> $value->ProcedureID,
					'UserID'								=> $value->UserID,
					'date_of_transaction'		=> $value->date_of_transaction,
					'paid'									=> $value->paid,
					'procedure_cost'				=> $value->procedure_cost,
					'procedure_name'				=> $value->procedure_name,
					'transaction_id'				=> $value->transaction_id,
					'user_name'							=> $value->user_name,
					'final_bill'						=> $final,
					'discount'							=> $value->clinic_discount
				);

				array_push($final_data, $temp);
			}

			return $final_data;
		}else{
			return Redirect::to('provider-portal-login');
		}
	}


	public function checkTransaction($id)
	{
		$check_transaction_invoice = DB::table('invoice_record_details')->where('transaction_id', $id)->first();

		if($check_transaction_invoice) {
			$check_payment_record = DB::table('payment_record')->where('invoice_id', $check_transaction_invoice->invoice_id)->first();

			if($check_payment_record) {
				if($check_payment_record->status == 1 || $check_payment_record->status == "1") {
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	public function removeTransaction( )
	{	
		$getSessionData = StringHelper::getMainSession(3);

		if(!$getSessionData || $getSessionData == FALSE){
			return Redirect::to('provider-portal-login');
		}

		$input = Input::all();

		if(empty($input) || $input == null) {
			return array('status' => false, 'message' => 'Transaction id is required.');
		}

		$new_id = $input['id'];
		$transaction = DB::table('transaction_history')->where('transaction_id', $new_id)->first();
		$email = [];
		$lite_plan_status = false;

		if($transaction) {
			$check_transaction = self::checkTransaction($new_id);
			if($check_transaction) {
				return array('status' => FALSE, 'message' => 'Transaction cannot be updated since transaction was already paid from the monthly invoice.');
			}

			if($transaction->credit_cost > 0) {
				$user_id = PlanHelper::getDependentOwnerID($transaction->UserID);
				$wallet = DB::table('e_wallet')->where('UserID', $user_id)->orderBy('created_at', 'desc')->first();
				$lite_plan_status = StringHelper::newLitePlanStatus($transaction->UserID);

				$wallet_class = new Wallet( );

				try {
					if($transaction->spending_type == "medical") {
						$spending_type = "medical";
					} else {
						$spending_type = "wellness";
					}

					$history_class = new WalletHistory( );

					if($transaction->currency_type == $transaction->default_currency && $transaction->default_currency == "myr") {
						$transaction->credit_cost = $transaction->credit_cost * $transaction->currency_amount;
						$wallet_history = array(
							'wallet_id'			=> $wallet->wallet_id,
							'credit'			=> $transaction->credit_cost,
							'running_balance'	=> $wallet->balance + $transaction->credit_cost,
							'logs'				=> "credits_back_from_in_network",
							'where_spend'		=> "credits_back_from_in_network",
							"id"				=> $transaction->transaction_id,
							"currency_type" => $transaction->currency_type,
							"currency_value" => $transaction->currency_amount,
							"created_at"	=> $transaction->created_at
						);
					} else {
						$wallet_history = array(
							'wallet_id'			=> $wallet->wallet_id,
							'credit'			=> $transaction->credit_cost,
							'running_balance'	=> $wallet->balance + $transaction->credit_cost,
							'logs'				=> "credits_back_from_in_network",
							'where_spend'		=> "credits_back_from_in_network",
							"id"				=> $transaction->transaction_id,
							"currency_type" => $transaction->currency_type,
							"currency_value" => $transaction->currency_amount,
							"created_at"	=> $transaction->created_at
						);
					}

					if($transaction->lite_plan_enabled == 1) {
						if($transaction->currency_type == $transaction->default_currency && $transaction->default_currency == "myr") {
							$transaction->credit_cost = $transaction->credit_cost * $transaction->currency_amount;
							$transaction->consultation_fees = $transaction->consultation_fees * $transaction->currency_amount;
							$wallet_history_lite_plan = array(
								'wallet_id'			=> $wallet->wallet_id,
								'credit'			=> $transaction->consultation_fees,
								'running_balance'	=> $wallet->balance + $transaction->credit_cost + $transaction->consultation_fees,
								'logs'				=> "credits_back_from_in_network",
								'where_spend'		=> "credits_back_from_in_network",
								"id"				=> $transaction->transaction_id,
								'lite_plan_enabled' => 1,
								"currency_type" => $transaction->currency_type,
								"currency_value" => $transaction->currency_amount,
								"created_at"	=> $transaction->created_at
							);
						} else {
							$wallet_history_lite_plan = array(
								'wallet_id'			=> $wallet->wallet_id,
								'credit'			=> $transaction->consultation_fees,
								'running_balance'	=> $wallet->balance + $transaction->credit_cost + $transaction->consultation_fees,
								'logs'				=> "credits_back_from_in_network",
								'where_spend'		=> "credits_back_from_in_network",
								"id"				=> $transaction->transaction_id,
								'lite_plan_enabled' => 1,
								"currency_type" => $transaction->currency_type,
								"currency_value" => $transaction->currency_amount,
								"created_at"	=> $transaction->created_at
							);
						}
					}


					if($spending_type == "medical") {
						$result_back = $history_class->createWalletHistory($wallet_history);
						if($lite_plan_status && $transaction->lite_plan_enabled == 1) {
							$history_class->createWalletHistory($wallet_history_lite_plan);
						}
					} else {
						$result_back = \WellnessWalletHistory::create($wallet_history);
						if($lite_plan_status && $transaction->lite_plan_enabled == 1) {
							\WellnessWalletHistory::create($wallet_history_lite_plan);
						}
					}

					if($result_back) {
						try {

							if($spending_type == "medical") {
								if($transaction->currency_type == $transaction->default_currency && $transaction->default_currency == "myr") {
									$transaction->credit_cost = $transaction->credit_cost * $transaction->currency_amount;
									$transaction->consultation_fees = $transaction->consultation_fees * $transaction->currency_amount;
									$result = $wallet_class->addCredits($user_id, $transaction->credit_cost);
									if($lite_plan_status && $transaction->lite_plan_enabled == 1) {
										$wallet_class->addCredits($user_id, $transaction->consultation_fees);
									}
								} else {
									$result = $wallet_class->addCredits($user_id, $transaction->credit_cost);
									if($lite_plan_status && $transaction->lite_plan_enabled == 1) {
										$wallet_class->addCredits($user_id, $transaction->consultation_fees);
									}
								}
							} else {
								if($transaction->currency_type == $transaction->default_currency && $transaction->default_currency == "myr") {
									$transaction->credit_cost = $transaction->credit_cost * $transaction->currency_amount;
									$transaction->consultation_fees = $transaction->consultation_fees * $transaction->currency_amount;
									$result = $wallet_class->addWellnessCredits($user_id, $transaction->credit_cost);
									if($lite_plan_status && $transaction->lite_plan_enabled == 1) {
										$wallet_class->addWellnessCredits($user_id, $transaction->consultation_fees);
									}
								} else {
									$result = $wallet_class->addWellnessCredits($user_id, $transaction->credit_cost);
									if($lite_plan_status && $transaction->lite_plan_enabled == 1) {
										$wallet_class->addWellnessCredits($user_id, $transaction->consultation_fees);
									}
								}
							}

							$delete_data = array(
								'refunded'		=> $transaction->health_provider_done == 0 ? 1 : 0,
								'deleted'		=> 1,
								'deleted_at'	=> date('Y-m-d H:i:s')
							);

							\Transaction::where('transaction_id', $new_id)->update($delete_data);
							$user = DB::table('user')->where('UserID', $user_id)->first();

							// get transaction details
							$transaction = \TransactionHelper::getTransactionDetails($new_id);

							if($transaction['visit_deduction'] == true) {
								$user_type = PlanHelper::getUserAccountType($transaction['user_id']);

								if($user_type == "employee") {
									$user_plan_history = DB::table('user_plan_history')->where('user_id', $transaction['user_id'])->orderBy('created_at', 'desc')->first();
									$customer_active_plan = DB::table('customer_active_plan')
									->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
									->first();
								} else {
									$user_plan_history = DB::table('dependent_plan_history')->where('user_id', $transaction['user_id'])->orderBy('created_at', 'desc')->first();
									$customer_active_plan = DB::table('dependent_plans')
									->where('dependent_plan_id', $user_plan_history->dependent_plan_id)
									->first();
								}
								
								if($customer_active_plan->account_type == "enterprise_plan")  {
									MemberHelper::returnPlanHistoryVisit($transaction['user_id']);
								}
							}
							// send email
							try {
								if($user->Email) {
									$email['member'] = ucwords($user->Name);
									$email['credits'] = $transaction['total_amount'];
									$email['total_amount'] = $transaction['total_amount'];
									$email['bill_amount'] = $transaction['bill_amount'];
									$email['transaction_id'] = $transaction['transaction_id'];
									$email['transaction_date'] = $transaction['transaction_date'];
									$email['health_provider_name'] = $transaction['health_provider_name'];
									$email['health_provider_address'] = $transaction['health_provider_address'];
									$email['health_provider_city'] = $transaction['health_provider_city'];
									$email['health_provider_country'] = $transaction['health_provider_country'];
									$email['health_provider_phone'] = $transaction['health_provider_phone'];
									$email['health_provider_postal'] = $transaction['health_provider_postal'];
									$email['service'] = $transaction['service'];
									$email['emailSubject'] = 'Member - Refunded Transaction';
									$email['emailTo'] = $user->Email;
									$email['emailName'] = ucwords($user->Name);
									$email['clinic_type_image'] = $transaction['clinic_type'];
									$email['emailPage'] = 'email-templates.email-member-refunded-transaction';
									$email['lite_plan_status'] = $transaction['lite_plan'];
									$email['consultation'] = $transaction['consultation'];
									$email['total_credits'] = $transaction['credits'];
									$email['paid_by_credits'] = $transaction['paid_by_credits'];
									$email['paid_by_cash'] = $transaction['paid_by_cash'];
									$email['cap_per_visit'] = $transaction['cap_per_visit'];
									$email['cap_per_visit_status'] = $transaction['cap_per_visit_status'];
									$email['lite_plan_enabled'] = $transaction['lite_plan'];
									$email['currency_symbol'] = $transaction['currency_symbol'];
									EmailHelper::sendEmailRefundWithAttachment($email);
								}
							} catch(Exception $e) {
								$email['end_point'] = url('clinic/remove/transaction', $parameter = array(), $secure = null);
								$email['logs'] = 'Refund Transaction from Clinic - '.$e;
								$email['emailSubject'] = 'Error log.';
								EmailHelper::sendErrorLogs($email);
								return array(
									'status'	=> TRUE,
									'message'	=> 'Success.'
								);
							}
							
						} catch(Exception $e) {
							$email['end_point'] = url('clinic/remove/transaction', $parameter = array(), $secure = null);
							$email['logs'] = 'Refund Transaction from Clinic - '.$e;
							$email['emailSubject'] = 'Error log.';
							EmailHelper::sendErrorLogs($email);
							return array(
								'status'	=> FALSE,
								'message'	=> 'Failed to refund customer.'
							);
						}

						return array(
							'status'	=> TRUE,
							'message'	=> 'Success.'
						);
					}
				} catch(Exception $e) {
					// send email logs
					$email['end_point'] = url('clinic/remove/transaction', $parameter = array(), $secure = null);
					$email['logs'] = 'Refund Transaction from Clinic - '.$e->getMessage();
					$email['emailSubject'] = 'Error log.';
					EmailHelper::sendErrorLogs($email);
					return array(
						'status'	=> FALSE,
						'message'	=> 'Failed to refund customer.'
					);
				}
			} else {
				$delete_data = array(
					'refunded'		=> 0,
					'deleted'		=> 1,
					'deleted_at'	=> date('Y-m-d H:i:s')
				);

				try {
					$result_transaction = DB::table('transaction_history')->where('transaction_id', $new_id)->update($delete_data);
					if($result_transaction) {
						try {
							// check if its in lite plan
							if($transaction->lite_plan_enabled == 1 || $transaction->lite_plan_use_credits == 1) {
								// check if transaction exist in wallet logs
								if($transaction->spending_type == 'medical') {
									$table_wallet_history = 'wallet_history';
								} else {
									$table_wallet_history = 'wellness_wallet_history';
								}

								$logs_lite_plan = DB::table('e_wallet')
								->join($table_wallet_history, $table_wallet_history.'.wallet_id', '=', 'e_wallet.wallet_id')
								->where($table_wallet_history.'.logs', 'deducted_from_mobile_payment')
								->where($table_wallet_history.'.lite_plan_enabled', 1)
								->where('e_wallet.UserID', $transaction->UserID)
								->where($table_wallet_history.'.id', $transaction->transaction_id)
								->first();

								$wallet = DB::table('e_wallet')->where('UserID', $transaction->UserID)->orderBy('created_at', 'desc')->first();

								if($logs_lite_plan) {
			                    	// return the credits
									$wallet_history_lite_plan = array(
										'wallet_id'			=> $wallet->wallet_id,
										'credit'			=> $transaction->co_paid_amount,
										'running_balance'	=> $wallet->balance + $transaction->co_paid_amount,
										'logs'				=> "credits_back_from_in_network",
										'where_spend'		=> "credits_back_from_in_network",
										"id"				=> $transaction->transaction_id,
										'lite_plan_enabled' => 1,
									);

									if($transaction->spending_type == "medical") {
										$history_class = new WalletHistory( );
										$history_class->createWalletHistory($wallet_history_lite_plan);
									} else {
										\WellnessWalletHistory::create($wallet_history_lite_plan);
									}

									$wallet_class = new Wallet( );

									if($transaction->spending_type == "medical") {
										$wallet_class->addCredits($transaction->UserID, $transaction->co_paid_amount);
									} else {
										$wallet_class->addWellnessCredits($transaction->UserID, $transaction->co_paid_amount);
									}

									$delete_data = array(
										'refunded'		=> 1
									);
									DB::table('transaction_history')->where('transaction_id', $new_id)->update($delete_data);
								}
							}
						} catch(Exception $e) {
							$email['end_point'] = url('clinic/remove/transaction', $parameter = array(), $secure = null);
							$email['logs'] = 'Refund Transaction from Clinic with Credits From Lite Plan - '.$e->getMessage();
							$email['emailSubject'] = 'Error log.';
							EmailHelper::sendErrorLogs($email);
						}

					}

					return array(
						'status'	=> TRUE,
						'message'	=> 'Success.'
					);
				} catch(Exception $e) {
					$email['end_point'] = url('clinic/remove/transaction', $parameter = array(), $secure = null);
					$email['logs'] = 'Refund Transaction from Clinic - '.$e->getMessage();
					$email['emailSubject'] = 'Error log.';
					EmailHelper::sendErrorLogs($email);
					return array(
						'status'	=> FALSE,
						'message'	=> 'Failed to remove transaction.'
					);
				}
			}

		} else {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Transaction not found. Pleas check or reload the page.'
			);
		}
	}

	public function removeTransactionBackDate($id)
	{
		$getSessionData = StringHelper::getMainSession(3);
		// $getSessionData->Ref_ID;
		if($getSessionData != FALSE){

			// check if transaction has credits spent
			$new_id = $id;
			$transaction = DB::table('transaction_history')->where('transaction_id', $new_id)->first();
			$email = [];
			$lite_plan_status = false;

			if($transaction) {
				$check_transaction = self::checkTransaction($new_id);
				if($check_transaction) {
					return array('status' => FALSE, 'message' => 'Transaction cannot be updated since transaction was already paid from the monthly invoice.');
				}
				
				if($transaction->credit_cost > 0) {
					$wallet = DB::table('e_wallet')->where('UserID', $transaction->UserID)->orderBy('created_at', 'desc')->first();
					$lite_plan_status = StringHelper::litePlanStatus($transaction->UserID);

					$wallet_class = new Wallet( );

					try {

						if($transaction->spending_type == "medical") {
							$spending_type = "medical";
						} else {
							$spending_type = "wellness";
						}

						$history_class = new WalletHistory( );

						$wallet_history = array(
							'wallet_id'			=> $wallet->wallet_id,
							'credit'			=> $transaction->credit_cost,
							'running_balance'	=> $wallet->balance + $transaction->credit_cost,
							'logs'				=> "credits_back_from_in_network",
							'where_spend'		=> "credits_back_from_in_network",
							"id"				=> $transaction->transaction_id
						);

						if($lite_plan_status && $transaction->lite_plan_enabled == 1) {
							$wallet_history_lite_plan = array(
								'wallet_id'			=> $wallet->wallet_id,
								'credit'			=> $transaction->co_paid_amount,
								'running_balance'	=> $wallet->balance + $transaction->credit_cost + $transaction->co_paid_amount,
								'logs'				=> "credits_back_from_in_network",
								'where_spend'		=> "credits_back_from_in_network",
								"id"				=> $transaction->transaction_id,
								'lite_plan_enabled' => 1,
							);
						}


						if($spending_type == "medical") {
							$result_back = $history_class->createWalletHistory($wallet_history);
							if($lite_plan_status && $transaction->lite_plan_enabled == 1) {
								$history_class->createWalletHistory($wallet_history_lite_plan);
							}
						} else {
							$result_back = \WellnessWalletHistory::create($wallet_history);
							if($lite_plan_status && $transaction->lite_plan_enabled == 1) {
								\WellnessWalletHistory::create($wallet_history_lite_plan);
							}
						}


						if($result_back) {

							try {

								if($spending_type == "medical") {
									$result = $wallet_class->addCredits($transaction->UserID, $transaction->credit_cost);
									if($lite_plan_status && $transaction->lite_plan_enabled == 1) {
										$wallet_class->addCredits($transaction->UserID, $transaction->co_paid_amount);
									}
								} else {
									$result = $wallet_class->addWellnessCredits($transaction->UserID, $transaction->credit_cost);
									if($lite_plan_status && $transaction->lite_plan_enabled == 1) {
										$wallet_class->addWellnessCredits($transaction->UserID, $transaction->co_paid_amount);
									}
								}

								$delete_data = array(
									'refunded'		=> $transaction->health_provider_done == 0 ? 1 : 0,
									'deleted'		=> 1,
									'deleted_at'	=> date('Y-m-d H:i:s')
								);

								\Transaction::where('transaction_id', $new_id)->update($delete_data);

								// send email
								$user = DB::table('user')->where('UserID', $transaction->UserID)->first();
								$clinic = DB::table('clinic')->where('ClinicID', $transaction->ClinicID)->first();
								$transaction_id = str_pad($transaction->transaction_id, 6, "0", STR_PAD_LEFT);
								$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
								$procedure_temp = "";

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

								$type = "";
								$image = "";
								if($clinic_type->head == 1 || $clinic_type->head == "1") {
									if($clinic_type->Name == "General Practitioner") {
										$type = "General Practitioner";
										$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
									} else if($clinic_type->Name == "Dental Care") {
										$type = "Dental Care";
										$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
									} else if($clinic_type->Name == "Traditional Chinese Medicine") {
										$type = "Traditional Chinese Medicine";
										$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
									} else if($clinic_type->Name == "Health Screening") {
										$type = "Health Screening";
										$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
									} else if($clinic_type->Name == "Wellness") {
										$type = "Wellness";
										$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
									} else if($clinic_type->Name == "Health Specialist") {
										$type = "Health Specialist";
										$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
									}
								} else {
									$find_head = DB::table('clinic_types')
									->where('ClinicTypeID', $clinic_type->sub_id)
									->first();
									if($find_head->Name == "General Practitioner") {
										$type = "General Practitioner";
										$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
									} else if($find_head->Name == "Dental Care") {
										$type = "Dental Care";
										$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
									} else if($find_head->Name == "Traditional Chinese Medicine") {
										$type = "Traditional Chinese Medicine";
										$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
									} else if($find_head->Name == "Health Screening") {
										$type = "Health Screening";
										$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
									} else if($find_head->Name == "Wellness") {
										$type = "Wellness";
										$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
									} else if($find_head->Name == "Health Specialist") {
										$type = "Health Specialist";
										$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
									}
								}

								$total_credits = $transaction->credit_cost;

								if($lite_plan_status && $transaction->lite_plan_enabled == 1) {
									$total_credits = $transaction->credit_cost + $transaction->co_paid_amount;
								}

								$email['member'] = ucwords($user->Name);
								$email['credits'] = number_format($transaction->credit_cost, 2);
								$email['transaction_id'] = strtoupper(substr($clinic->Name, 0, 3)).$transaction_id;
								$email['transaction_date'] = date('d F Y, h:ia', strtotime($transaction->date_of_transaction));
								$email['health_provider_name'] = ucwords($clinic->Name);
								$email['health_provider_address'] = $clinic->Address;
								$email['health_provider_city'] = $clinic->City;
								$email['health_provider_country'] = $clinic->Country;
								$email['health_provider_phone'] = $clinic->Phone;
								$email['service'] = ucwords($clinic_type->Name).' - '.$service;
								$email['emailSubject'] = 'Member - Refunded Transaction';
								$email['emailTo'] = $user->Email;
								$email['emailName'] = ucwords($user->Name);
								$email['clinic_type_image'] = $image;
								$email['emailPage'] = 'email-templates.member-refunded-transaction';
								$email['lite_plan_status'] = $lite_plan_status;
								$email['consultation'] = number_format($transaction->co_paid_amount, 2);
								$email['total_credits'] = number_format($total_credits, 2);
								$email['lite_plan_enabled'] = $transaction->lite_plan_enabled;
								EmailHelper::sendEmailRefundWithAttachment($email);

								// $email['emailTo'] = 'info@medicloud.sg';
								// EmailHelper::sendEmailRefundWithAttachment($email);
							} catch(Exception $e) {
								$email['end_point'] = url('clinic/remove/backdate_claim_transactions', $parameter = array(), $secure = null);
								$email['logs'] = 'Refund Transaction from Clinic - '.$e->getMessage();
								$email['emailSubject'] = 'Error log.';
								EmailHelper::sendErrorLogs($email);
								return array(
									'status'	=> FALSE,
									'message'	=> 'Failed to refund customer.'
								);
							}

							return array(
								'status'	=> TRUE,
								'message'	=> 'Success.'
							);
						}
					} catch(Exception $e) {
					// send email logs
						$email['end_point'] = url('clinic/remove/backdate_claim_transactions', $parameter = array(), $secure = null);
						$email['logs'] = 'Refund Transaction from Clinic - '.$e->getMessage();
						$email['emailSubject'] = 'Error log.';
						EmailHelper::sendErrorLogs($email);
						return array(
							'status'	=> FALSE,
							'message'	=> 'Failed to refund customer.'
						);
					}
				} else {
					return array(
						'status'	=> FALSE,
						'message'	=> 'Transaction not found. Pleas check or reload the page.'
					);
				}
			}

			return array(
				'status'	=> TRUE,
				'message'	=> 'Success.'
			);


		}else{
			return Redirect::to('provider-portal-login');
		}
	}


	public function getMobileTransactionDetailsView( )
	{
		$hostName = $_SERVER['HTTP_HOST'];
		$protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$data['server'] = $protocol.$hostName;
		$data['title'] = 'Mobile Transaction Payment Page';
		$now = new \DateTime();
		$data['date'] = $now;
		$getSessionData = StringHelper::getMainSession(3);
		return View::make('settings.payments.preview', $data);
	}


	public function getMobileAllTransactionDetails( )
	{
		$getSessionData = StringHelper::getMainSession(3);
    // return var_dump($getSessionData);
		$transactions = DB::table('transaction_history')->where('ClinicID', $getSessionData->Ref_ID)->where('mobile', 1)->where('in_network', 1)->orderBy('created_at', 'desc')->get();
		$last_transaction = DB::table('transaction_history')->where('transaction_id', $transactions[0]->transaction_id)->first();
    // return var_dump($last_transaction);

		$transaction_details = [];
    // $customer_transaction_details = [];
		if(sizeof($transactions)) {
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
	                  // $procedure = "";
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

					if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
						$receipt_status = TRUE;
						$health_provider_status = TRUE;
					} else {
						$health_provider_status = FALSE;
					}

					$format = array(
						'clinic_name'       => $clinic->Name,
						'amount'            => number_format($trans->procedure_cost, 2),
						'clinic_type_and_service' => $clinic_name,
						'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->created_at)),
						'customer'          => ucwords($customer->Name),
						'transaction_id'    => $trans->transaction_id,
						'receipt_status'    => $receipt_status,
						'health_provider_status' => $health_provider_status,
						'user_id'           => $trans->UserID,
						'user_image'				=> $customer->Image
					);

					array_push($transaction_details, $format);
				}
			}
		}

		$data['result']['transaction_result'] = $transaction_details;

    // $customer_trans = DB::table('transaction_history')->where('transaction_id', $last_transaction->transaction_id)->first();
		if($last_transaction) {
			$receipt_images = DB::table('user_image_receipt')->where('transaction_id', $last_transaction->transaction_id)->get();
			$clinic = DB::table('clinic')->where('ClinicID', $last_transaction->ClinicID)->first();
			$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
			$customer = DB::table('user')->where('UserID', $last_transaction->UserID)->first();
			$procedure_temp = "";
        // get services
			if($last_transaction->multiple_service_selection == 1 || $last_transaction->multiple_service_selection == "1")
			{
            // get multiple service
				$service_lists = DB::table('transaction_services')
				->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
				->where('transaction_services.transaction_id', $last_transaction->transaction_id)
				->get();

				foreach ($service_lists as $key => $service) {
					$procedure_temp .= ucwords($service->Name).',';
					$procedure = rtrim($procedure_temp, ',');
				}
				$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
			} else {
				$service_lists = DB::table('clinic_procedure')
				->where('ProcedureID', $last_transaction->ProcedureID)
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
			$receipt = DB::table('user_image_receipt')->where('transaction_id', $last_transaction->transaction_id)->count();

			if($receipt > 0) {
				$receipt_status = TRUE;
			} else {
				$receipt_status = FALSE;
			}

			if($last_transaction->health_provider_done == 1 || $last_transaction->health_provider_done == "1") {
				$receipt_status = TRUE;
				$health_provider_status = TRUE;
			} else {
				$health_provider_status = FALSE;
			}

			$customer_transaction_details = array(
				'clinic_name'       => $clinic->Name,
				'amount'            => number_format($last_transaction->procedure_cost, 2),
				'clinic_type_and_service' => $clinic_name,
				'date_of_transaction' => date('d F Y, h:ia', strtotime($last_transaction->created_at)),
				'customer'          => ucwords($customer->Name),
				'transaction_id'    => $last_transaction->transaction_id,
				'receipt_status'    => $receipt_status,
				'health_provider_status' => $health_provider_status,
				'user_id'           => $last_transaction->UserID,
				'user_image'				=> $customer->Image
			);

			$data['result']['customer_result'] = $customer_transaction_details;
		}
    // get spific transaction
		return $data['result'];
	}

	public function getMobileTransactionDetails($id)
	{
		$getSessionData = StringHelper::getMainSession(3);


		$transaction_details = [];

		$customer_trans = DB::table('transaction_history')->where('transaction_id', $id)->where('ClinicID', $getSessionData->Ref_ID)->where('mobile', 1)->where('in_network', 1)->orderBy('created_at', 'desc')->first();
		if($customer_trans) {
			$receipt_images = DB::table('user_image_receipt')->where('transaction_id', $id)->get();
			$clinic = DB::table('clinic')->where('ClinicID', $customer_trans->ClinicID)->first();
			$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
			$customer = DB::table('user')->where('UserID', $customer_trans->UserID)->first();
			$procedure_temp = "";
        // get services
			if($customer_trans->multiple_service_selection == 1 || $customer_trans->multiple_service_selection == "1")
			{
            // get multiple service
				$service_lists = DB::table('transaction_services')
				->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
				->where('transaction_services.transaction_id', $id)
				->get();

				foreach ($service_lists as $key => $service) {
					$procedure_temp .= ucwords($service->Name).',';
					$procedure = rtrim($procedure_temp, ',');
				}
				$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
			} else {
				$service_lists = DB::table('clinic_procedure')
				->where('ProcedureID', $customer_trans->ProcedureID)
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
			$receipt = DB::table('user_image_receipt')->where('transaction_id', $id)->count();

			if($receipt > 0) {
				$receipt_status = TRUE;
			} else {
				$receipt_status = FALSE;
			}

			if($customer_trans->health_provider_done == 1 || $customer_trans->health_provider_done == "1") {
				$receipt_status = TRUE;
				$health_provider_status = TRUE;
			} else {
				$health_provider_status = FALSE;
			}

			$customer_transaction_details = array(
				'clinic_name'       => $clinic->Name,
				'amount'            => number_format($customer_trans->procedure_cost, 2),
				'clinic_type_and_service' => $clinic_name,
				'date_of_transaction' => date('d F Y, h:ia', strtotime($customer_trans->created_at)),
				'customer'          => ucwords($customer->Name),
				'transaction_id'    => $id,
				'receipt_status'    => $receipt_status,
				'health_provider_status' => $health_provider_status,
				'user_id'           => $customer_trans->UserID,
				'user_image'				=> $customer->Image
			);

			$data['result']['customer_result'] = $customer_transaction_details;
		}
    // get spific transaction
		return $data['result'];
	}

	public function updateTransactionDetails( )
	{
		$input = Input::all();

		// check transaction if already paid;
		$new_id = (int)preg_replace('/[^0-9]/', '', $input['transaction_id']);
		$transaction = DB::table('transaction_history')->where('transaction_id', $new_id)->first();
		$email = [];

		if($transaction) {
			$check_transaction = self::checkTransaction($new_id);
			if($check_transaction) {
				return array('status' => false, 'message' => 'Transaction cannot be updated since transaction was already paid from the monthly invoice.');
			}

			// update transaction
			$transaction_data = array(
				'date_of_transaction' => date('Y-m-d H:i:s', strtotime($input['date_of_transaction'])),
				'claim_date'	 	  => date('Y-m-d H:i:s', strtotime($input['claim_date']))
			);

			$result = \Transaction::where('transaction_id', $new_id)->update($transaction_data);

			if($result) {
				// check claim date and invoice date
				\InvoiceRecordDetails::where('transaction_id', $new_id)->delete();
				$transaction = DB::table('transaction_history')->where('transaction_id', $new_id)->first();
				self::insertOrCheckInInvoice($transaction->ClinicID, date('Y-m-d', strtotime($transaction->claim_date)));
			}

			return array('status' => true, 'message' => 'Transaction updated.');

		} else {
			return array('status' => false, 'message' => 'Transaction ID not found.');
		}

	}

	public function getAllTransactions( )
	{
		$getSessionData = StringHelper::getMainSession(3);
		$clinic_id = $getSessionData->Ref_ID;
		$clinic = DB::table('clinic')->where('ClinicID', $clinic_id)->first();
		$format = [];

		// $start = date('Y-m-01', strtotime('-1 month'));
		$start = date('Y-m-01');
		$temp_end = date('Y-m-t');
		$end =  date('Y-m-d H:i:s', strtotime('+24 hours', strtotime($temp_end)));

		// return $start.$end;

		$transactions = DB::table('transaction_history')
		->join('user', 'user.UserID', '=', 'transaction_history.UserID')
		->where('transaction_history.ClinicID', $getSessionData->Ref_ID)
		->where(function($query) use ($clinic_id, $start, $end){
			$query->where('transaction_history.ClinicID', $clinic_id)
			->where('transaction_history.paid', 1)
			->where('transaction_history.procedure_cost', ">=", 0)
			->where('transaction_history.claim_date', '>=', $start)
			->where('transaction_history.claim_date', '<=', $end);
		})
		->orWhere(function($query) use ($clinic_id, $start, $end){
			$query->where('transaction_history.ClinicID', $clinic_id)
			->where('transaction_history.paid', 1)
			->where('transaction_history.procedure_cost', ">=", 0)
			->where('transaction_history.created_at', '>=', $start)
			->where('transaction_history.created_at', '<=', $end);
		})
		->orderBy('transaction_history.created_at', 'desc')
		->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost', 'transaction_history.credit_divisor', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.health_provider_done', 'transaction_history.credit_divisor', 'transaction_history.gst_percent_value', 'transaction_history.claim_date', 'transaction_history.created_at', 'transaction_history.currency_type', 'transaction_history.currency_amount', 'transaction_history.peak_hour_status', 'transaction_history.peak_hour_amount', 'transaction_history.lite_plan_use_credits', 'transaction_history.lite_plan_enabled', 'transaction_history.spending_type', 'transaction_history.consultation_fees', 'transaction_history.half_credits', 'transaction_history.cash_cost', 'transaction_history.default_currency')
		->get();
		foreach ($transactions as $key => $trans) {

			// if($trans->procedure_cost > 0) {
			$procedure_temp = "";
			$text = "";
			$transaction_status = '';
			$mednefits_credits = 0;
			$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();

			if($trans->spending_type == 'medical') {
				$table_wallet_history = 'wallet_history';
			} else {
				$table_wallet_history = 'wellness_wallet_history';
			}

			if($trans->co_paid_status == 0 || $trans->co_paid_status == "0") {
				
				if(strrpos($trans->clinic_discount, '%')) {
					$percentage = chop($trans->clinic_discount, '%');
					if($trans->credit_cost > 0) {
						$amount = $trans->credit_cost;
					} else {
						$amount = $trans->procedure_cost;
					}

					$total_percentage = $percentage + $trans->medi_percent;

					$formatted_percentage = $total_percentage / 100;
					$temp_fee = $amount / ( 1 - $formatted_percentage );
		              // if non gst
					$mednefits_pecent = $trans->medi_percent / 100;
					$fee = $temp_fee * $mednefits_pecent;
				} else {
					if((int)$trans->peak_hour_status == 1) {
						$fee = number_format((float)$trans->peak_hour_amount, 2);
					} else {
						$fee = number_format((float)$trans->co_paid_amount, 2);
					}
				}
				$text = "non co paid";
			} else if($trans->co_paid_status == 1 || $trans->co_paid_status == "1"){
					// $fee = $trans->co_paid_amount;
					// $text = "co paid";
				if(strrpos($trans->clinic_discount, '%')) {
					$percentage = chop($trans->clinic_discount, '%');
					if($trans->credit_cost > 0) {
						$amount = $trans->credit_cost;
					} else {
						$amount = $trans->procedure_cost;
					}

					$total_percentage = $percentage + $trans->medi_percent;

					$formatted_percentage = $total_percentage / 100;
					$temp_fee = $amount / ( 1 - $formatted_percentage );
		              // if non gst
					$mednefits_pecent = $trans->medi_percent / 100;
					$temp_mednefits_fee = $temp_fee * $mednefits_pecent;
					$fee = $temp_mednefits_fee * $trans->gst_percent_value;
				} else {
					if((int)$trans->peak_hour_status == 1) {
						$fee = number_format((float)$trans->peak_hour_amount, 2);
					} else {
						$fee = number_format((float)$trans->co_paid_amount, 2);
					}
				}
			}
			if($trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1")
			{
	          // get multiple service
				$service_lists = DB::table('transaction_services')
				->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
				->where('transaction_services.transaction_id', $trans->transaction_id)
				->get();

				foreach ($service_lists as $key => $service) {
					if(sizeof($service_lists) - 2 == $key) {
						$procedure_temp .= ucwords($service->Name).' and ';
					} else {
						$procedure_temp .= ucwords($service->Name).',';
					}
					$procedure = rtrim($procedure_temp, ',');
					$procedure_id = 0;
				}
			} else {
				$service_lists = DB::table('clinic_procedure')
				->where('ProcedureID', $trans->ProcedureID)
				->first();
				if($service_lists) {
					$procedure = ucwords($service_lists->Name);
					$procedure_id = $trans->ProcedureID;
				} else {
					$procedure_id = $trans->ProcedureID;
					$procedure = '';
				}
			}

			if($trans->credit_cost > 0) {
				$mednefits_credits += $trans->credit_cost;
				$cash = "0.00";
			} else {
				$mednefits_credits += 0;
				$cash = $trans->procedure_cost;
			}

			if($trans->deleted == 1 && $trans->refunded == 1 || $trans->deleted == "1" && $trans->refunded == "1") {
				$transaction_status = 'REFUNDED';
			} else if($trans->deleted == 1 && $trans->health_provider_done == 1 || $trans->deleted == 1 && $trans->health_provider_done == "1"){
				$transaction_status = 'REMOVED';
			}

			$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);
			if(strripos($trans->procedure_cost, '$') !== false) {
				$temp_cost = explode('$', $trans->procedure_cost);
				$cost = number_format($temp_cost[1]);
			} else {
				$cost = number_format($trans->procedure_cost, 2);
			}

			if((int)$trans->lite_plan_enabled == 1 && $mednefits_credits > 0 || (int)$trans->lite_plan_use_credits == 1 && $mednefits_credits > 0 || $mednefits_credits > 0) {
				$deleted_option = "refund";
			} else {
				$deleted_option = "remove";
			}

			if((int)$trans->lite_plan_enabled == 1 && (int)$trans->deleted == 1 && $mednefits_credits > 0 || (int)$trans->lite_plan_use_credits == 1 && (int)$trans->deleted == 1 && $mednefits_credits > 0 || $mednefits_credits > 0 && (int)$trans->deleted == 1) {
				$option = "refunded";
			} else {
				$option = "removed";
			}

			if((int)$trans->half_credits == 1 || $trans->cash_cost > 0) {
				$cash = $trans->cash_cost;
				// if((int)$trans->lite_plan_enabled == 1 && (int)$trans->lite_plan_use_credits == 0) {
				// 	$mednefits_credits = $trans->credit_cost + $trans->consultation_fees;
				// } else {
				$mednefits_credits = $trans->credit_cost;
				// }
			}

			$registration_date = null;
			// get check in time data
			$check_in_data = DB::table('user_check_in_clinic')
			->where('user_id', $trans->UserID)
			->where('clinic_id', $trans->ClinicID)
			->where('id', $trans->transaction_id)
			->first();

			if($check_in_data) {
				$registration_date = date('d F Y, h:i a', strtotime($check_in_data->check_in_time));
			} else {
				$registration_date = date('d F Y, h:i a', strtotime($trans->date_of_transaction));
			}

			if($trans->default_currency == $trans->currency_type && $trans->default_currency == "myr") {
				$cost = $cost * $trans->currency_amount;
				$fee = $fee * $trans->currency_amount;
				$mednefits_credits = $mednefits_credits * $trans->currency_amount;
				$cash = $cash * $trans->currency_amount;
				$trans->currency_type = "myr";
			} else {
				$trans->currency_type = "sgd";
			}

			$temp = array(
				'ClinicID'							=> $trans->ClinicID,
				'NRIC'									=> $trans->NRIC,
				'ProcedureID'						=> $procedure_id,
				'UserID'								=> $trans->UserID,
				'date_of_transaction'		=> $registration_date,
				'claim_date'				=> $trans->claim_date ? date('d F Y, h:i a', strtotime($trans->claim_date)) : date('d F Y, h:i a', strtotime($trans->created_at)),
				'paid'									=> $trans->paid,
				'procedure_cost'				=> $cost,
				'procedure_name'				=> $procedure,
				'trans_id'						=> $trans->transaction_id,
				'transaction_id'				=> strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
				'user_name'							=> ucwords($trans->user_name),
				'mednefits_fee'					=> number_format($fee, 2),
				'discount'							=> $trans->clinic_discount,
				'multiple_service_selection' => $trans->multiple_service_selection,
				'mednefits_credits'			=> number_format($mednefits_credits, 2),
				'cash'									=> number_format($cash, 2),
				'text' 									=> $text,
				'deleted'								=> (int)$trans->deleted == 1 ? TRUE : FALSE,
				'refunded'							=> (int)$trans->refunded == 1 ? TRUE : FALSE,
				'health_provider'				=> (int)$trans->health_provider_done == 1 || $trans->credit_cost == 0 || $trans->credit_cost == NULL ? TRUE : FALSE,
				'transaction_status'		=> $transaction_status,
				'currency_type'			=> $trans->currency_type,
				'currency_amount'		=> $trans->currency_amount,
				'lite_plan_enabled'		=> (int)$trans->lite_plan_enabled == 1 ? true : false,
				'lite_plan_use_credits' => (int)$trans->lite_plan_use_credits == 1 ? true : false,
				'data_status'				=> $option,
				'deleted_option'		=> $deleted_option
			);

			array_push($format, $temp);

			// }
		}

		return $format;
	}

	public function searchByNricTransactions( )
	{
		$input = Input::all();
		$getSessionData = StringHelper::getMainSession(3);
		$clinic_id = $getSessionData->Ref_ID;
		$clinic = DB::table('clinic')->where('ClinicID', $clinic_id)->first();
		$format = [];

		$start = date('Y-m-01', strtotime('-1 month'));
		$end = date('Y-m-t');

		if(isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != null && $input['end_date'] != null) {
			$start = date('Y-m-d', strtotime($input['start_date']));
			$end = date('Y-m-d H:i:s', strtotime('+24 hours', strtotime($input['end_date'])));
			// return $start.' - '.$end;
			if(isset($input['nric']) && $input['nric'] != null && $input['nric'] != "") {
				$nric = $input['nric'];
				$transactions = DB::table('transaction_history')
				->join('user', 'user.UserID', '=', 'transaction_history.UserID')
				->where(function($query) use ($clinic_id, $nric, $start, $end){
					$query->where('transaction_history.ClinicID', $clinic_id)
					->where('user.PhoneNo', 'like', '%'.(int)$nric.'%')
					->where('transaction_history.paid', 1)
					->where('transaction_history.procedure_cost', ">=", 0)
					->where('transaction_history.created_at', '>=', $start)
					->where('transaction_history.created_at', '<=', $end);
				})
				->orWhere(function($query) use ($clinic_id, $nric, $start, $end){
					$query->where('transaction_history.ClinicID', $clinic_id)
					->where('user.PhoneNo', 'like', '%'.(int)$nric.'%')
					->where('transaction_history.paid', 1)
					->where('transaction_history.procedure_cost', ">=", 0)
					->where('transaction_history.claim_date', '>=', $start)
					->where('transaction_history.claim_date', '<=', $end);
				})
				->orderBy('transaction_history.created_at', 'desc')
				->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost', 'transaction_history.credit_divisor', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.health_provider_done', 'transaction_history.gst_percent_value', 'transaction_history.claim_date', 'transaction_history.created_at','transaction_history.currency_type', 'transaction_history.currency_amount', 'transaction_history.peak_hour_status', 'transaction_history.peak_hour_amount', 'transaction_history.lite_plan_enabled', 'transaction_history.lite_plan_use_credits', 'transaction_history.spending_type', 'transaction_history.cash_cost', 'transaction_history.half_credits', 'transaction_history.cap_per_visit', 'transaction_history.default_currency')
				->get();
			} else {
				$transactions = DB::table('transaction_history')
				->join('user', 'user.UserID', '=', 'transaction_history.UserID')
				->where(function($query) use ($clinic_id, $start, $end){
					$query->where('transaction_history.ClinicID', $clinic_id)
					->where('transaction_history.procedure_cost', ">=", 0)
					->where('transaction_history.paid', 1)
					->where('transaction_history.claim_date', '>=', $start)
					->where('transaction_history.claim_date', '<=', $end);
					
				})
				->orWhere(function($query) use ($clinic_id, $start, $end){
					$query->where('transaction_history.ClinicID', $clinic_id)
					->where('transaction_history.procedure_cost', ">=", 0)
					->where('transaction_history.paid', 1)
					->where('transaction_history.created_at', '>=', $start)
					->where('transaction_history.created_at', '<=', $end);
				})
				->orderBy('transaction_history.created_at', 'desc')
				->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost', 'transaction_history.credit_divisor', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.health_provider_done', 'transaction_history.gst_percent_value', 'transaction_history.claim_date', 'transaction_history.created_at', 'transaction_history.currency_type', 'transaction_history.currency_amount', 'transaction_history.peak_hour_status', 'transaction_history.peak_hour_amount', 'transaction_history.lite_plan_enabled', 'transaction_history.lite_plan_use_credits', 'transaction_history.spending_type', 'transaction_history.cash_cost', 'transaction_history.half_credits', 'transaction_history.cap_per_visit', 'transaction_history.default_currency')
				->get();
			}
			
		} else {
			$transactions = DB::table('transaction_history')
			->join('user', 'user.UserID', '=', 'transaction_history.UserID')
			->where('transaction_history.ClinicID', $clinic_id)
			->where('user.PhoneNo', 'like', '%'.(int)$input['nric'].'%')
			->where('transaction_history.paid', 0)
			->where('transaction_history.procedure_cost', ">=", 0)
				// ->where('transaction_history.date_of_transaction', '>=', $start)
				// ->where('transaction_history.date_of_transaction', '<=', $end)
			->orderBy('transaction_history.created_at', 'desc')
			->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost', 'transaction_history.credit_divisor', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.health_provider_done', 'transaction_history.gst_percent_value', 'transaction_history.claim_date', 'transaction_history.created_at', 'transaction_history.currency_type', 'transaction_history.currency_amount', 'transaction_history.peak_hour_status', 'transaction_history.peak_hour_amount', 'transaction_history.cash_cost', 'transaction_history.half_credits', 'transaction_history.cap_per_visit', 'transaction_history.default_currency')
			->get();
		}

		

		foreach ($transactions as $key => $trans) {
			$procedure_temp = "";
			$text = "";
			$mednefits_credits = 0;
			$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
			
			if($trans->spending_type == 'medical') {
				$table_wallet_history = 'wallet_history';
			} else {
				$table_wallet_history = 'wellness_wallet_history';
			}

	        // if((int)$trans->lite_plan_enabled == 1) {
	        //     $logs_lite_plan = DB::table($table_wallet_history)
	        //     ->where('logs', 'deducted_from_mobile_payment')
	        //     ->where('lite_plan_enabled', 1)
	        //     ->where('id', $trans->transaction_id)
	        //     ->first();

	        //     if($logs_lite_plan && floatval($trans->credit_cost) > 0 && (int)$trans->lite_plan_use_credits == 0) {
	        //         $mednefits_credits += floatval($trans->co_paid_amount);
	        //     } else if($logs_lite_plan && floatval($trans->procedure_cost) >= 0 && (int)$trans->lite_plan_use_credits == 1){
	        //         $mednefits_credits += floatval($trans->co_paid_amount);
	        //     }
	        // }

			if((int)$trans->co_paid_status == 0) {
				if(strrpos($trans->clinic_discount, '%')) {
					$percentage = chop($trans->clinic_discount, '%');
					if($trans->credit_cost > 0) {
						$amount = $trans->credit_cost;
					} else {
						$amount = $trans->procedure_cost;
					}

					$total_percentage = $percentage + $trans->medi_percent;

					$formatted_percentage = $total_percentage / 100;
					$temp_fee = $amount / ( 1 - $formatted_percentage );
	              // if non gst
					$mednefits_pecent = $trans->medi_percent / 100;
					$fee = $temp_fee * $mednefits_pecent;
				} else {
					if((int)$trans->peak_hour_status == 1) {
						$fee = number_format((float)$trans->peak_hour_amount, 2);
					} else {
						$fee = number_format((float)$trans->co_paid_amount, 2);
					}
				}
				$text = "non co paid";
			} else if((int)$trans->co_paid_status == 1){
				// $fee = $trans->co_paid_amount;
				// $text = "co paid";
				if(strrpos($trans->clinic_discount, '%')) {
					$percentage = chop($trans->clinic_discount, '%');
					if($trans->credit_cost > 0) {
						$amount = $trans->credit_cost;
					} else {
						$amount = $trans->procedure_cost;
					}

					$total_percentage = $percentage + $trans->medi_percent;

					$formatted_percentage = $total_percentage / 100;
					$temp_fee = $amount / ( 1 - $formatted_percentage );
	              // if non gst
					$mednefits_pecent = $trans->medi_percent / 100;
					$temp_mednefits_fee = $temp_fee * $mednefits_pecent;
					$fee = $temp_mednefits_fee * $trans->gst_percent_value;
				} else {
					if((int)$trans->peak_hour_status == 1) {
						$fee = number_format((float)$trans->peak_hour_amount, 2);
					} else {
						$fee = number_format((float)$trans->co_paid_amount, 2);
					}
				}
			}

			if((int)$trans->multiple_service_selection == 1)
			{
          // get multiple service
				$service_lists = DB::table('transaction_services')
				->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
				->where('transaction_services.transaction_id', $trans->transaction_id)
				->get();

				foreach ($service_lists as $key => $service) {
					if(sizeof($service_lists) - 2 == $key) {
						$procedure_temp .= ucwords($service->Name).' and ';
					} else {
						$procedure_temp .= ucwords($service->Name).',';
					}
					$procedure = rtrim($procedure_temp, ',');
					$procedure_id = 0;
				}
			} else {
				$service_lists = DB::table('clinic_procedure')
				->where('ProcedureID', $trans->ProcedureID)
				->first();
				if($service_lists) {
					$procedure = ucwords($service_lists->Name);
					$procedure_id = $trans->ProcedureID;
				}
			}

			if($trans->credit_cost > 0) {
				$mednefits_credits += $trans->credit_cost;
				$cash = 0.00;
				$cost = floatval($trans->procedure_cost);
			} else {
				$mednefits_credits += 0;
				if(strripos($trans->procedure_cost, '$') !== false) {
					$temp_cost = explode('$', $trans->procedure_cost);
            // $cost = number_format($temp_cost[1]);
					$cost = $temp_cost[1];
				} else {
            // $cost = number_format($trans->procedure_cost, 2);
					$cost = floatval($trans->procedure_cost);
				}
				$cash = number_format($cost, 2);
			}

			if((int)$trans->half_credits == 1 || $trans->cash_cost > 0) {
				$cash = $trans->cash_cost;
			}

			if((int)$trans->lite_plan_enabled == 1 && $mednefits_credits > 0 || (int)$trans->lite_plan_use_credits == 1 && $mednefits_credits > 0 || $mednefits_credits > 0) {
				$deleted_option = "refund";
			} else {
				$deleted_option = "remove";
			}

			if((int)$trans->lite_plan_enabled == 1 && (int)$trans->deleted == 1 && $mednefits_credits > 0 || (int)$trans->lite_plan_use_credits == 1 && (int)$trans->deleted == 1 && $mednefits_credits > 0|| $mednefits_credits > 0 && (int)$trans->deleted == 1) {
				$option = "refunded";
			} else {
				$option = "removed";
			}

			if($trans->default_currency == $trans->currency_type && $trans->default_currency == "myr") {
				$cost = $cost * $trans->currency_amount;
				$fee = $fee * $trans->currency_amount;
				$mednefits_credits = $mednefits_credits * $trans->currency_amount;
				$cash = $cash * $trans->currency_amount;
			}

			$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);
			$temp = array(
				'ClinicID'							=> $trans->ClinicID,
				'NRIC'									=> $trans->NRIC,
				'ProcedureID'						=> $procedure_id,
				'UserID'								=> $trans->UserID,
				'date_of_transaction'		=> date('d F Y, h:i a', strtotime($trans->date_of_transaction)),
				'claim_date'				=> $trans->claim_date ? date('d F Y, h:i a', strtotime($trans->claim_date)) : date('d F Y, h:i a', strtotime($trans->created_at)),
				'paid'									=> $trans->paid,
				'procedure_cost'				=> number_format($cash, 2),
				'procedure_name'				=> $procedure,
				'trans_id'						=> $trans->transaction_id,
				'transaction_id'				=> strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
				'user_name'							=> ucwords($trans->user_name),
				'mednefits_fee'					=> number_format($fee, 2),
				'discount'							=> $trans->clinic_discount,
				'multiple_service_selection' => $trans->multiple_service_selection,
				'mednefits_credits'			=> number_format($mednefits_credits, 2),
				'cash'									=> number_format($cash, 2),
				'text' 									=> $text,
				'deleted'								=> (int)$trans->deleted == 1 ? TRUE : FALSE,
				'refunded'							=> (int)$trans->refunded == 1 ? TRUE : FALSE,
				'health_provider'				=> (int)$trans->health_provider_done == 1 || $trans->credit_cost  == 0 || $trans->credit_cost == NULL ? TRUE : FALSE,
				'currency_type'			=> $trans->currency_type,
				'currency_amount'		=> $trans->currency_amount,
				'data_status'				=> $option,
				'deleted_option'		=> $deleted_option
			);

			array_push($format, $temp);
		}

		return ['status' => true, 'data' => $format];
	}

	public function getHealthProviderTransactions( )
	{
		$input = Input::all();
		$getSessionData = StringHelper::getMainSession(3);
		$clinic_id = $getSessionData->Ref_ID;
		$format = [];
		$procedure_ids_temp = [];
		$transactions = DB::table('transaction_history')
		->where('ClinicID', $clinic_id)
									// ->where('procedure_cost', '>', 0)
		->where('paid', 0)
		->where('health_provider_done', 1)
		->get();

		foreach ($transactions as $key => $trans) {
			// check if have many procedures
			if($trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1") {
				$multiple = true;
				$procedures = DB::table('transaction_services')->where('transaction_id', $trans->transaction_id)->get();
				foreach ($procedures as $key => $prod) {
					array_push($procedure_ids_temp, $prod->service_id);
				}
				$procedure_ids = array_unique($procedure_ids_temp);
			} else {
				$multiple = false;
				$procedure_ids[] = $trans->ProcedureID;
			}

			$user = DB::table('user')->where('UserID', $trans->UserID)->first();
			$temp = array(
				'user_id'	=> $trans->UserID,
				'transaction_id' => $trans->transaction_id,
				'date'		=> date('d F Y, h:ia', strtotime($trans->date_of_transaction)),
				'name'		=> ucwords($user->Name),
				'nric'		=> $user->NRIC,
				'amount'		=> number_format($trans->procedure_cost, 2),
				'procedure_id' => $trans->ProcedureID,
				'procedure_ids' => $procedure_ids,
				'multiple_procedures' => $multiple,
				'user_type' => $user->UserType,
				'access_type' => $user->access_type
			);

			array_push($format, $temp);
			$procedure_ids = [];
		}

		return $format;
	}

	public function getSpecificTransactionDetails( )
	{
		$input = Input::all();

		if(empty($input['transaction_id']) || $input['transaction_id'] == null) {
			return array('status' => false, 'message' => 'Transaction ID is required.');
		}

		// check transaction id existence

		$getSessionData = StringHelper::getMainSession(3);
		
		$trans_check = DB::table('transaction_history')
		->where('transaction_id', $input['transaction_id'])
		->where('ClinicID', $getSessionData->Ref_ID)
		->first();
		if(!$trans_check) {
			return array('status' => false, 'message' => 'Transaction does not exist.');
		}

		$transaction = DB::table('transaction_history')
		->join('user', 'user.UserID', '=', 'transaction_history.UserID')
		->where('transaction_history.ClinicID', $getSessionData->Ref_ID)
		->where('transaction_history.transaction_id', $input['transaction_id'])
		->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost','transaction_history.credit_divisor', 'transaction_history.currency_type', 'transaction_history.currency_amount')
		->first();


		$procedure_temp = "";
		$procedure_ids = [];
		if($transaction->co_paid_status == 0) {
			if(strrpos($transaction->clinic_discount, '%')) {
		      // $percentage = chop($transaction->clinic_discount, '%');
		      // $discount = $percentage / 100 + $transaction->medi_percent / 100;
		      // $sub = $transaction->procedure_cost * $discount;
		      // $fee = number_format($transaction->procedure_cost - $sub, 2);
				$clinic = DB::table('clinic')->where('ClinicID', $transaction->ClinicID)->first();
				$clinicType = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();

							// check if procedure cost or credit cost has value
				if($transaction->credit_cost != 0) {
					$fee = number_format($transaction->credit_cost / $clinicType->divisor_value, 2);
				} else {
					$percentage = chop($transaction->clinic_discount, '%');
					$discount = $percentage / 100;
					$sub = $transaction->procedure_cost * $discount;
					$fee = number_format($sub, 2);
				}
			} else {
		      // return 'use whole number';
		      // $discount_clinic = str_replace('$', '', $transaction->clinic_discount);
		      // $discount = $discount_clinic;
		      // $final = $transaction->procedure_cost - $discount;
				$fee = number_format($transaction->co_paid_amount, 2);
			}
		} else if($transaction->co_paid_status == 1){
			// $fee = $transaction->procedure_cost;
			$fee = $transaction->co_paid_amount;
		}

		if($transaction->multiple_service_selection == 1 || $transaction->multiple_service_selection == "1")
		{
        // get multiple service
			$service_lists = DB::table('transaction_services')
			->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
			->where('transaction_services.transaction_id', $transaction->transaction_id)
			->get();

			foreach ($service_lists as $key => $service) {
				array_push($procedure_ids, $service->service_id);
				if(sizeof($service_lists) - 2 == $key) {
					$procedure_temp .= ucwords($service->Name).' and ';
				} else {
					$procedure_temp .= ucwords($service->Name).',';
				}
				$procedure = rtrim($procedure_temp, ',');
				$procedure_id = 0;
			}
		} else {
			$service_lists = DB::table('clinic_procedure')
			->where('ProcedureID', $transaction->ProcedureID)
			->first();
			if($service_lists) {
				array_push($procedure_ids, $transaction->ProcedureID);
				$procedure = ucwords($service_lists->Name);
				$procedure_id = $transaction->ProcedureID;
			}
		}

		if($transaction->credit_cost > 0) {
			$mednefits_credits = number_format($transaction->credit_cost, 2);
			$cash = 0.00;
		} else {
			$mednefits_credits = 00;
			$cash = number_format($transaction->procedure_cost);
		}

		$format = array(
			'ClinicID'							=> $transaction->ClinicID,
			'NRIC'									=> $transaction->NRIC,
			'ProcedureID'						=> $procedure_id,
			'UserID'								=> $transaction->UserID,
			'date_of_transaction'		=> date('d F Y, h:i a', strtotime($transaction->date_of_transaction)),
			'paid'									=> $transaction->paid,
			'procedure_cost'				=> number_format($transaction->procedure_cost, 2),
			'procedure_name'				=> $procedure,
			'transaction_id'				=> $transaction->transaction_id,
			'user_name'							=> ucwords($transaction->user_name),
			'mednefits_fee'						=> $fee,
			'discount'							=> $transaction->clinic_discount,
			'multiple_procedures' => $transaction->multiple_service_selection,
			'health_provider'				=> $transaction->health_provider_done,
			'mednefits_credits'			=> $mednefits_credits,
			'cash'									=> $cash,
			'procedure_ids'					=> $procedure_ids
		);

		return $format;
	}



	public function deleteTransaction( )
	{
		$input = Input::all();

		$transaction = new Transaction( );
		$transaction->deleteTransaction($input['transaction_id']);

		return array('status' => TRUE, 'message' => 'Transaction deleted.');
	}

	public function downloadTransactions( )
	{
		$input = Input::all();
		$start = date('Y-m-d', strtotime($input['start']));
		$end = date('Y-m-d H:i:s', strtotime('+23 hours', strtotime($input['end'])));

		$getSessionData = StringHelper::getMainSession(3);
		$clinic_id = $getSessionData->Ref_ID;



		$format = [];
		$mednefits_total_fee = 0;
		$transaction_size = 0;

    // clinic details
		$clinic = DB::table('clinic')->where('ClinicID', $clinic_id)->first();
		$email = DB::table('user')->where('UserType', 3)->where('Ref_ID', $clinic_id)->first();
		$details = array(
			'clinic_name'	=> ucwords($clinic->Name),
			'address'			=> ucwords($clinic->Address),
			'city'				=> ucwords($clinic->City),
			'state'				=> ucwords($clinic->State),
			'country'			=> ucwords($clinic->Country),
			'postal'			=> $clinic->Postal,
			'phone'				=> $clinic->Phone,
			'email'				=> $email->Email
		);

		if(isset($input['nric'])) {
			$search = $input['nric'];
			$transactions = DB::table('transaction_history')
			->join('user', 'user.UserID', '=', 'transaction_history.UserID')
			->where(function($query) use ($search, $clinic_id, $start, $end){
				$query->where('user.Name', 'like', '%'.$search.'%')
				->where('transaction_history.ClinicID', $clinic_id)
				->where('transaction_history.deleted', 0)
				->where('transaction_history.date_of_transaction', '>=', $start)
				->where('transaction_history.date_of_transaction', '<=', $end);
			})
			->orWhere(function($query) use ($search, $clinic_id, $start, $end){
				$query->where('user.NRIC', 'like', '%'.$search.'%')
				->where('transaction_history.ClinicID', $clinic_id)
				->where('transaction_history.deleted', 0)
				->where('transaction_history.date_of_transaction', '>=', $start)
				->where('transaction_history.date_of_transaction', '<=', $end);
			})
			->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost', 'transaction_history.credit_divisor', 'transaction_history.paid', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.health_provider_done', 'transaction_history.gst_percent_value', 'transaction_history.lite_plan_use_credits', 'transaction_history.lite_plan_enabled', 'transaction_history.spending_type', 'transaction_history.currency_type', 'transaction_history.currency_amount', 'transaction_history.default_currency')
			->orderBy('transaction_history.created_at', 'desc')
			->get();
		} else {
			$transactions = DB::table('transaction_history')
			->join('user', 'user.UserID', '=', 'transaction_history.UserID')
			->where('transaction_history.ClinicID', $clinic_id)
							// ->where('transaction_history.deleted', 0)
							// ->where('transaction_history.procedure_cost', ">", 0)
			->where('transaction_history.paid', 1)
			->where('transaction_history.date_of_transaction', '>=', $start)
			->where('transaction_history.date_of_transaction', '<=', $end)
			->select('transaction_history.ClinicID', 'transaction_history.currency_type', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost', 'transaction_history.credit_divisor', 'transaction_history.paid', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.health_provider_done', 'transaction_history.gst_percent_value', 'transaction_history.gst_percent_value', 'transaction_history.lite_plan_use_credits', 'transaction_history.lite_plan_enabled', 'transaction_history.spending_type', 'transaction_history.currency_type', 'transaction_history.currency_amount', 'transaction_history.default_currency')
			->orderBy('transaction_history.created_at', 'desc')
			->get();
		}


		if(sizeof($transactions) > 0) {
			foreach ($transactions as $key => $trans) {
				$procedure_temp = "";
				$procedure = "";
				$procedure_ids = [];
				$transaction_status = '';
				$mednefits_credits = 0;

				if((int)$trans->paid == 1 && (int)$trans->deleted == 0) {
					$mednefits_total_fee += $trans->credit_cost;
					$transaction_size++;
				}

				if($trans->spending_type == 'medical') {
					$table_wallet_history = 'wallet_history';
				} else {
					$table_wallet_history = 'wellness_wallet_history';
				}

				if($trans->co_paid_status == 0) {
					if(strrpos($trans->clinic_discount, '%')) {
						$percentage = chop($trans->clinic_discount, '%');
						if($trans->credit_cost > 0) {
							$amount = $trans->credit_cost;
						} else {
							$amount = $trans->procedure_cost;
						}

						$total_percentage = $percentage + $trans->medi_percent;

						$formatted_percentage = $total_percentage / 100;
						$temp_fee = $amount / ( 1 - $formatted_percentage );
						// if non gst
						$mednefits_pecent = $trans->medi_percent / 100;
						$fee = $temp_fee * $mednefits_pecent;
					} else {
						$fee = (float)$trans->co_paid_amount;
					}
				} else {
					if(strrpos($trans->clinic_discount, '%')) {
						$percentage = chop($trans->clinic_discount, '%');
						if($trans->credit_cost > 0) {
							$amount = $trans->credit_cost;
						} else {
							$amount = $trans->procedure_cost;
						}

						$total_percentage = $percentage + $trans->medi_percent;

						$formatted_percentage = $total_percentage / 100;
						$temp_fee = $amount / ( 1 - $formatted_percentage );
						// if non gst
						$mednefits_pecent = $trans->medi_percent / 100;
						$temp_mednefits_fee = $temp_fee * $mednefits_pecent;
						$fee = $temp_mednefits_fee * $trans->gst_percent_value;
					} else {
						$fee = (float)$trans->co_paid_amount;
					}
				}

				if((int)$trans->multiple_service_selection == 1)
				{
		        // get multiple service
					$service_lists = DB::table('transaction_services')
					->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
					->where('transaction_services.transaction_id', $trans->transaction_id)
					->get();

					foreach ($service_lists as $key => $service) {
						array_push($procedure_ids, $service->service_id);
						if(sizeof($service_lists) - 2 == $key) {
							$procedure_temp .= ucwords($service->Name).' and ';
						} else {
							$procedure_temp .= ucwords($service->Name).' ';
						}
						$procedure = rtrim($procedure_temp, ',');
						$procedure_id = 0;
					}
				} else {
					$service_lists = DB::table('clinic_procedure')
					->where('ProcedureID', $trans->ProcedureID)
					->first();
					if($service_lists) {
						array_push($procedure_ids, $trans->ProcedureID);
						$procedure = ucwords($service_lists->Name);
						$procedure_id = $trans->ProcedureID;
					} else {
						$procedure_id = $trans->ProcedureID;
					}
				}

				if($trans->credit_cost > 0) {
					$mednefits_credits += (float)$trans->credit_cost;
					$cash = 0.00;
				} else {
					$mednefits_credits += 0;
					$cash = (float)$trans->procedure_cost;
				}

// 
				// if($trans->paid == 1 && $trans->deleted == 0 || $trans->paid == "1" && $trans->deleted == "0") {
				// 	$mednefits_total_fee += $fee;
				// }

				// if($trans->deleted == 1 && $trans->refunded == 1 || $trans->deleted == "1" && $trans->refunded == "1") {
				// 	$transaction_status = 'REFUNDED';
				// } else if($trans->deleted == 1 && $trans->health_provider_done == 1 || $trans->deleted == 1 && $trans->health_provider_done == "1"){
				// 	$transaction_status = 'REMOVED';
				// }
				if((int)$trans->lite_plan_enabled == 1 && (int)$trans->deleted == 1 || (int)$trans->lite_plan_use_credits == 1 && (int)$trans->deleted == 1 || $mednefits_credits > 0 && (int)$trans->deleted == 1) {
					$transaction_status = 'REFUNDED';
				} else {
					$transaction_status = 'REMOVED';
				}

				if($trans->default_currency == $trans->currency_type && $trans->default_currency == "myr") {
					$trans->procedure_cost = $trans->procedure_cost * $trans->currency_amount;
					$fee = $fee * $trans->currency_amount;
					$mednefits_credits = $mednefits_credits * $trans->currency_amount;
					$cash = $cash * $trans->currency_amount;
					$trans->currency_type = "MYR";
				} else {
					$trans->currency_type = "SGD";
				}

				$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);
				$temp = array(
					'ClinicID'								=> $trans->ClinicID,
					'NRIC'									=> $trans->NRIC,
					'ProcedureID'							=> $procedure_id,
					'UserID'								=> $trans->UserID,
					'date_of_transaction'					=> date('d F Y, h:i a', strtotime($trans->date_of_transaction)),
					'paid'									=> $trans->paid,
					'procedure_cost'						=> number_format($trans->procedure_cost, 2),
					'procedure_name'						=> $procedure,
					'transaction_id'						=> strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
					'user_name'								=> ucwords($trans->user_name),
					'mednefits_fee'							=> $trans->currency_type.toUpperCase() + ' ' + number_format($fee, 2),
					'discount'								=> $trans->clinic_discount,
					'multiple_procedures' 					=> $trans->multiple_service_selection,
					'health_provider'						=> $trans->health_provider_done,
					'mednefits_credits'						=> $trans->currency_type.toUpperCase() + ' ' + number_format($mednefits_credits, 2),
					'cash'									=> $trans->currency_type.toUpperCase() + ' ' + number_format($cash, 2),
					'procedure_ids'							=> $procedure_ids,
					'deleted'								=> $trans->deleted == 1 || $trans->deleted == "1" ? TRUE : FALSE,
					'refunded'								=> $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE,
					'health_provider'						=> $trans->health_provider_done == 1 || $trans->health_provider_done == "1" || $trans->credit_cost  == 0 || $trans->credit_cost == NULL ? TRUE : FALSE,
					'transaction_status'					=> $transaction_status,
					'currency_type'							=> strtoupper($trans->currency_type),
					'currency_amount'						=> $trans->currency_amount
				);
				array_push($format, $temp);
			}
		}

		$period = date('d F', strtotime($start)).' - '.date('d F Y', strtotime($end));
		$data = array(
			'transactions' 				=> $format,
			'total_transactions'	=> $transaction_size,
			'mednefits_wallet'		=> number_format($mednefits_total_fee, 2),
			'clinic_details'			=> $details,
			'period'							=> $period,
			"currency_type"				=> "SGD"
		);

    // return View::make('pdf-download.transaction-history', $data);
		$pdf = PDF::loadView('pdf-download.transaction-history', $data);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'landscape');
		return $pdf->download(ucwords($clinic->Name).' - ( '.$period.' ) - '.time().'.pdf');
	}

	public function searchTransaction( )
	{
		$getSessionData = StringHelper::getMainSession(3);
		$input = Input::all();
		$start = date('Y-m-d', strtotime($input['start']));
		$end = date('Y-m-d H:i:s', strtotime('+24 hours', strtotime($input['end'])));

	    // return $end;
		$clinic_id = $getSessionData->Ref_ID;

		$format = [];
		$mednefits_total_fee = 0;
		$transaction_size = 0;

	    // clinic details
		$clinic = DB::table('clinic')->where('ClinicID', $clinic_id)->first();
		$email = DB::table('user')->where('UserType', 3)->where('Ref_ID', $clinic_id)->first();
		$details = array(
			'clinic_id'		=> $clinic_id,
			'clinic_name'	=> ucwords($clinic->Name),
			'address'			=> ucwords($clinic->Address),
			'city'				=> ucwords($clinic->City),
			'state'				=> ucwords($clinic->State),
			'country'			=> ucwords($clinic->Country),
			'postal'			=> $clinic->Postal,
			'phone'				=> $clinic->Phone,
			'email'				=> $email->Email
		);

		$transactions = DB::table('transaction_history')
		->join('user', 'user.UserID', '=', 'transaction_history.UserID')
		->where(function($query) use ($clinic_id, $start, $end){
			$query->where('transaction_history.ClinicID', $clinic_id)
			->where('transaction_history.paid', 1)
			->where('transaction_history.procedure_cost', ">=", 0)
			->where('transaction_history.claim_date', '>=', $start)
			->where('transaction_history.claim_date', '<=', $end);
		})
		->orWhere(function($query) use ($clinic_id, $start, $end){
			$query->where('transaction_history.ClinicID', $clinic_id)
			->where('transaction_history.paid', 1)
			->where('transaction_history.procedure_cost', ">=", 0)
			->where('transaction_history.created_at', '>=', $start)
			->where('transaction_history.created_at', '<=', $end);
		})
		->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost', 'transaction_history.credit_divisor', 'transaction_history.paid', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.health_provider_done', 'transaction_history.gst_percent_value', 'transaction_history.currency_amount', 'transaction_history.currency_type', 'transaction_history.peak_hour_status', 'transaction_history.peak_hour_amount', 'transaction_history.lite_plan_use_credits', 'transaction_history.lite_plan_enabled', 'transaction_history.spending_type', 'transaction_history.cash_cost', 'transaction_history.half_credits', 'transaction_history.cap_per_visit', 'transaction_history.default_currency')
		->orderBy('transaction_history.created_at', 'desc')
		->get();

		if(sizeof($transactions) > 0) {
			foreach ($transactions as $key => $trans) {
				$procedure_temp = "";
				$procedure = "";
				$procedure_ids = [];
				$transaction_status = '';
				$mednefits_credits = 0;
				if($trans->spending_type == 'medical') {
					$table_wallet_history = 'wallet_history';
				} else {
					$table_wallet_history = 'wellness_wallet_history';
				}

				if($trans->co_paid_status == 0) {
					if(strrpos($trans->clinic_discount, '%')) {
						$percentage = chop($trans->clinic_discount, '%');
						if($trans->credit_cost > 0) {
							$amount = $trans->credit_cost;
						} else {
							$amount = $trans->procedure_cost;
						}

						$total_percentage = $percentage + $trans->medi_percent;

						$formatted_percentage = $total_percentage / 100;
						$temp_fee = $amount / ( 1 - $formatted_percentage );
						// if non gst
						$mednefits_pecent = $trans->medi_percent / 100;
						$fee = $temp_fee * $mednefits_pecent;
					} else {
						if((int)$trans->peak_hour_status == 1) {
							$fee = $trans->peak_hour_amount;
						} else {
							$fee = (float)$trans->co_paid_amount;
						}
					}
				} else {
					if(strrpos($trans->clinic_discount, '%')) {
						$percentage = chop($trans->clinic_discount, '%');
						if($trans->credit_cost > 0) {
							$amount = $trans->credit_cost;
						} else {
							$amount = $trans->procedure_cost;
						}

						$total_percentage = $percentage + $trans->medi_percent;

						$formatted_percentage = $total_percentage / 100;
						$temp_fee = $amount / ( 1 - $formatted_percentage );
						// if non gst
						$mednefits_pecent = $trans->medi_percent / 100;
						$temp_mednefits_fee = $temp_fee * $mednefits_pecent;
						$fee = $temp_mednefits_fee * $trans->gst_percent_value;
					} else {
						if((int)$trans->peak_hour_status == 1) {
							$fee = $trans->peak_hour_amount;
						} else {
							$fee = (float)$trans->co_paid_amount;
						}
					}
				}
				$procedure_id = 0;
				if((int)$trans->multiple_service_selection == 1) {
			        // get multiple service
					$service_lists = DB::table('transaction_services')
					->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
					->where('transaction_services.transaction_id', $trans->transaction_id)
					->get();

					foreach ($service_lists as $key => $service) {
						array_push($procedure_ids, $service->service_id);
						if(sizeof($service_lists) - 2 == $key) {
							$procedure_temp .= ucwords($service->Name).' and ';
						} else {
							$procedure_temp .= ucwords($service->Name).' ';
						}
						$procedure = rtrim($procedure_temp, ',');
					}
				} else {
					$service_lists = DB::table('clinic_procedure')
					->where('ProcedureID', $trans->ProcedureID)
					->first();
					if($service_lists) {
						array_push($procedure_ids, $trans->ProcedureID);
						$procedure = ucwords($service_lists->Name);
						$procedure_id = $trans->ProcedureID;
					} else {
						$procedure_id = $trans->ProcedureID;
					}
				}

				if($trans->credit_cost > 0) {
					$mednefits_credits += $trans->credit_cost;
					$cash = 0;
				} else {
					$mednefits_credits += 0;
					$cash = $trans->procedure_cost;
				}


				if((int)$trans->half_credits == 1) {
					$cash = $trans->cash_cost;
				}

				if((int)$trans->paid == 1 && $trans->deleted == 0) {
					$mednefits_total_fee += $fee;
				}

				// if($trans->deleted == 1 && $trans->refunded == 1 || $trans->deleted == "1" && $trans->refunded == "1") {
				// 	$transaction_status = 'REFUNDED';
				// } else if($trans->deleted == 1 && $trans->health_provider_done == 1 || $trans->deleted == 1 && $trans->health_provider_done == "1"){
				// 	$transaction_status = 'REMOVED';
				// }
				
				if((int)$trans->lite_plan_enabled == 1 && (int)$trans->deleted == 1 && $mednefits_credits > 0 || (int)$trans->lite_plan_use_credits == 1 && (int)$trans->deleted == 1 && $mednefits_credits > 0 || $mednefits_credits > 0 && (int)$trans->deleted == 1) {
					$transaction_status = 'REFUNDED';
				} else {
					$transaction_status = 'REMOVED';
				}

				if($trans->default_currency == $trans->currency_type && $trans->default_currency == "myr") {
					$trans->procedure_cost = $trans->procedure_cost * $trans->currency_amount;
					$fee = $fee * $trans->currency_amount;
					$mednefits_credits = $mednefits_credits * $trans->currency_amount;
					$cash = $cash * $trans->currency_amount;
					$trans->currency_type = "myr";
				} else {
					$trans->currency_type = "sgd";
				}

				if((int)$trans->paid == 1 && (int)$trans->deleted == 0) {
					$mednefits_total_fee += $mednefits_credits;
					$transaction_size++;
				}

				$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);
				$temp = array(
					'ClinicID'							=> $trans->ClinicID,
					'NRIC'									=> $trans->NRIC,
					'ProcedureID'						=> $procedure_id,
					'UserID'								=> $trans->UserID,
					'date_of_transaction'		=> date('d F Y, h:i a', strtotime($trans->date_of_transaction)),
					'paid'									=> $trans->paid,
					'procedure_cost'				=> $trans->procedure_cost,
					'procedure_name'				=> $procedure,
					'transaction_id'				=> strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
					'user_name'							=> ucwords($trans->user_name),
					'mednefits_fee'					=> number_format($fee, 2),
					'discount'							=> $trans->clinic_discount,
					'multiple_procedures' 	=> $trans->multiple_service_selection,
					'health_provider'				=> $trans->health_provider_done,
					'mednefits_credits'			=> number_format($mednefits_credits, 2),
					'cash'									=> number_format($cash, 2),
					'procedure_ids'					=> $procedure_ids,
					'deleted'								=> (int)$trans->deleted == 1 ? TRUE : FALSE,
					'refunded'							=> (int)$trans->refunded == 1 ? TRUE : FALSE,
					'health_provider'				=> $trans->health_provider_done == 1 || $trans->health_provider_done == "1" || $trans->credit_cost  == 0 || $trans->credit_cost == NULL ? TRUE : FALSE,
					'transaction_status'		=> (int)$trans->deleted == 1 ? $transaction_status : null,
					'currency_type'				=> $trans->currency_type,
					'currency_amount'			=> $trans->currency_amount
				);
				array_push($format, $temp);
			}
		}

		$data = array(
			'currency_type'				=> $clinic->currency_type == "myr" ? "MYR" : "SGD",
			'transactions' 				=> $format,
			'total_transactions'	=> $transaction_size,
			'mednefits_wallet'		=> number_format($mednefits_total_fee, 2),
			'clinic_details'			=> $details
		);

		return array('status' => TRUE, 'data' => $data);
	}

	public function searchSpecificTransaction( )
	{
		$getSessionData = StringHelper::getMainSession(3);
		$input = Input::all();
		$start = date('Y-m-01', strtotime($input['start']));
		$end = date('Y-m-t', strtotime($input['end']));
		$search = $input['search'];
		$clinic_id = $getSessionData->Ref_ID;

		$format = [];
		$mednefits_total_fee = 0;
		$transaction_size = 0;

		$clinic = DB::table('clinic')->where('ClinicID', $clinic_id)->first();

		$transactions = DB::table('transaction_history')
		->join('user', 'user.UserID', '=', 'transaction_history.UserID')
		->where(function($query) use ($search, $clinic_id, $start, $end){
			$query->where('user.Name', 'like', '%'.$search.'%')
			->where('UserType', 5)
			->where('transaction_history.ClinicID', $clinic_id)
			->where('transaction_history.deleted', 0)
			->where('transaction_history.date_of_transaction', '>=', $start)
			->where('transaction_history.date_of_transaction', '<=', $end);
		})
		->orWhere(function($query) use ($search, $clinic_id, $start, $end){
			$query->where('user.PhoneNo', 'like', '%'.(int)$search.'%')
			->where('UserType', 5)
			->where('transaction_history.ClinicID', $clinic_id)
			->where('transaction_history.deleted', 0)
			->where('transaction_history.date_of_transaction', '>=', $start)
			->where('transaction_history.date_of_transaction', '<=', $end);
		})
		->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost', 'transaction_history.credit_divisor', 'transaction_history.paid', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.health_provider_done', 'transaction_history.gst_percent_value', 'transaction_history.currency_amount', 'transaction_history.currency_type', 'transaction_history.peak_hour_status', 'transaction_history.peak_hour_amount', 'transaction_history.lite_plan_use_credits', 'transaction_history.lite_plan_enabled', 'transaction_history.spending_type')
		->orderBy('transaction_history.created_at', 'desc')
		->get();

		if(sizeof($transactions) > 0) {
			foreach ($transactions as $key => $trans) {
				$procedure_temp = "";
				$procedure = "";
				$procedure_ids = [];
				$transaction_status = '';
				$mednefits_credits = 0;
				if($trans->spending_type == 'medical') {
					$table_wallet_history = 'wallet_history';
				} else {
					$table_wallet_history = 'wellness_wallet_history';
				}

		        // if((int)$trans->lite_plan_enabled == 1) {
		        //     $logs_lite_plan = DB::table($table_wallet_history)
		        //     ->where('logs', 'deducted_from_mobile_payment')
		        //     ->where('lite_plan_enabled', 1)
		        //     ->where('id', $trans->transaction_id)
		        //     ->first();

		        //     if($logs_lite_plan && floatval($trans->credit_cost) > 0 && (int)$trans->lite_plan_use_credits == 0) {
		        //         $mednefits_credits += floatval($trans->co_paid_amount);
		        //     } else if($logs_lite_plan && floatval($trans->procedure_cost) >= 0 && (int)$trans->lite_plan_use_credits == 1){
		        //         $mednefits_credits += floatval($trans->co_paid_amount);
		        //     }
		        // }


				if((int)$trans->paid == 1 && (int)$trans->deleted == 0) {
					$mednefits_total_fee += $trans->credit_cost;
					$transaction_size++;
				}


				if($trans->co_paid_status == 0) {
					if(strrpos($trans->clinic_discount, '%')) {
						$percentage = chop($trans->clinic_discount, '%');
						if($trans->credit_cost > 0) {
							$amount = $trans->credit_cost;
						} else {
							$amount = $trans->procedure_cost;
						}

						$total_percentage = $percentage + $trans->medi_percent;

						$formatted_percentage = $total_percentage / 100;
						$temp_fee = $amount / ( 1 - $formatted_percentage );
						// if non gst
						$mednefits_pecent = $trans->medi_percent / 100;
						$fee = $temp_fee * $mednefits_pecent;
					} else {
						if((int)$trans->peak_hour_status == 1) {
							$fee = number_format($trans->peak_hour_amount, 2);
						} else {
							$fee = number_format((float)$trans->co_paid_amount, 2);
						}
					}
				} else {
					if(strrpos($trans->clinic_discount, '%')) {
						$percentage = chop($trans->clinic_discount, '%');
						if($trans->credit_cost > 0) {
							$amount = $trans->credit_cost;
						} else {
							$amount = $trans->procedure_cost;
						}

						$total_percentage = $percentage + $trans->medi_percent;

						$formatted_percentage = $total_percentage / 100;
						$temp_fee = $amount / ( 1 - $formatted_percentage );
						// if non gst
						$mednefits_pecent = $trans->medi_percent / 100;
						$temp_mednefits_fee = $temp_fee * $mednefits_pecent;
						$fee = $temp_mednefits_fee * $trans->gst_percent_value;
					} else {
						if((int)$trans->peak_hour_status == 1) {
							$fee = number_format($trans->peak_hour_amount, 2);
						} else {
							$fee = number_format((float)$trans->co_paid_amount, 2);
						}
					}
				}
				$procedure_id = 0;
				if($trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1") {
			        // get multiple service
					$service_lists = DB::table('transaction_services')
					->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
					->where('transaction_services.transaction_id', $trans->transaction_id)
					->get();

					foreach ($service_lists as $key => $service) {
						array_push($procedure_ids, $service->service_id);
						if(sizeof($service_lists) - 2 == $key) {
							$procedure_temp .= ucwords($service->Name).' and ';
						} else {
							$procedure_temp .= ucwords($service->Name).' ';
						}
						$procedure = rtrim($procedure_temp, ',');
					}
				} else {
					$service_lists = DB::table('clinic_procedure')
					->where('ProcedureID', $trans->ProcedureID)
					->first();
					if($service_lists) {
						array_push($procedure_ids, $trans->ProcedureID);
						$procedure = ucwords($service_lists->Name);
						$procedure_id = $trans->ProcedureID;
					} else {
						$procedure_id = $trans->ProcedureID;
					}
				}

				if($trans->credit_cost > 0) {
					$mednefits_credits += $trans->credit_cost;
					$cash = "0.00";
				} else {
					$mednefits_credits += 0;
					$cash = number_format($trans->procedure_cost, 2);
				}


				// if($trans->paid == 1 && $trans->deleted == 0 || $trans->paid == "1" && $trans->deleted == "0") {
				// 	$mednefits_total_fee += $fee;
				// }

				// if($trans->deleted == 1 && $trans->refunded == 1 || $trans->deleted == "1" && $trans->refunded == "1") {
				// 	$transaction_status = 'REFUNDED';
				// } else if($trans->deleted == 1 && $trans->health_provider_done == 1 || $trans->deleted == 1 && $trans->health_provider_done == "1"){
				// 	$transaction_status = 'REMOVED';
				// }
				
				if((int)$trans->lite_plan_enabled == 1 && (int)$trans->deleted == 1 && $mednefits_credits > 0 || (int)$trans->lite_plan_use_credits == 1 && (int)$trans->deleted == 1 && $mednefits_credits > 0 || $mednefits_credits > 0 && (int)$trans->deleted == 1) {
					$transaction_status = 'REFUNDED';
				} else {
					$transaction_status = 'REMOVED';
				}

				$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);
				$temp = array(
					'ClinicID'							=> $trans->ClinicID,
					'NRIC'									=> $trans->NRIC,
					'ProcedureID'						=> $procedure_id,
					'UserID'								=> $trans->UserID,
					'date_of_transaction'		=> date('d F Y, h:i a', strtotime($trans->date_of_transaction)),
					'paid'									=> $trans->paid,
					'procedure_cost'				=> number_format($trans->procedure_cost, 2),
					'procedure_name'				=> $procedure,
					'transaction_id'				=> strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
					'user_name'							=> ucwords($trans->user_name),
					'mednefits_fee'					=> number_format($fee, 2),
					'discount'							=> $trans->clinic_discount,
					'multiple_procedures' 	=> $trans->multiple_service_selection,
					'health_provider'				=> $trans->health_provider_done,
					'mednefits_credits'			=> number_format($mednefits_credits, 2),
					'cash'									=> $cash,
					'procedure_ids'					=> $procedure_ids,
					'deleted'								=> (int)$trans->deleted == 1 ? TRUE : FALSE,
					'refunded'							=> (int)$trans->refunded == 1 ? TRUE : FALSE,
					'health_provider'				=> (int)$trans->health_provider_done == 1 || $trans->credit_cost  == 0 || $trans->credit_cost == NULL ? TRUE : FALSE,
					'transaction_status'		=> (int)$trans->deleted == 1 ? $transaction_status : null,
					'currency_type'				=> $trans->currency_type,
					'currency_amount'			=> $trans->currency_amount
				);
				array_push($format, $temp);
			}
		}


		$data = array(
			'transactions' 				=> $format,
			'total_transactions'	=> sizeof($transactions),
			'mednefits_wallet'		=> number_format($mednefits_total_fee, 2)
		);

		return array('status' => TRUE, 'data' => $data);
	}

	public function insertDeletedToTransaction( )
	{
		$deleted = DB::table('deleted_transaction')->get();

		$success = [];
		$transaction = new Transaction( );
		foreach ($deleted as $key => $trans) {
			$check = DB::table('transaction_history')->where('transaction_id', $trans->transaction_id)->count();

			if($check == 0) {
				try {
					$temp = array(
						'transaction_id'			=> $trans->transaction_id,
						'UserID'							=> $trans->UserID,
						'ProcedureID'					=> $trans->ProcedureID,
						'date_of_transaction'	=> $trans->date_of_transaction,
						'ClinicID'						=> $trans->ClinicID,
						'procedure_cost'			=> $trans->procedure_cost,
						'AppointmenID'				=> $trans->AppointmenID,
						'revenue'							=> 0,
						'debit'								=> 0,
						'medi_percent'				=> $trans->medi_percent,
						'clinic_discount'			=> $trans->clinic_discount,
						'wallet_use'					=> $trans->wallet_use,
						'wallet_id'						=> $trans->wallet_id,
						'current_wallet_amount' => $trans->current_wallet_amount,
						'credit_cost'					=> $trans->credit_cost,
						'paid'								=> $trans->paid,
						'co_paid_status'			=> $trans->co_paid_status,
						'co_paid_amount'			=> $trans->co_paid_amount,
						'DoctorID'						=> $trans->DoctorID,
						'backdate_claim'			=> $trans->backdate_claim,
						'in_network'					=> $trans->in_network,
						'health_provider_done' => $trans->health_provider_done,
						'multiple_service_selection' => $trans->multiple_service_selection,
						'created_at'					=> $trans->created_at,
						'updated_at'					=> $trans->updated_at,
						'deleted'							=> 1,
						'refunded'						=> $trans->credit_cost > 0 ? 1 : 0
					);
					$success[]['status'] = $transaction->createTransaction($temp);
				} catch(Exception $e) {
					$success['status'] = $e->getMessage();
				}
			} else {
				$success[]['status'] = 'exist';
			}
		}
		return $success;
	}

	public function testCalculate( )
	{
		$input = Input::all();

		$trans = DB::table('transaction_history')->where('transaction_id', $input['id'])->first();

		// return array('result' => $transaction);
		if($trans->co_paid_status == 0) {
			if(strrpos($trans->clinic_discount, '%')) {
				$percentage = chop($trans->clinic_discount, '%');
				if($trans->credit_cost > 0) {
					$amount = $trans->credit_cost;
				} else {
					$amount = $trans->procedure_cost;
				}

				$total_percentage = $percentage + $trans->medi_percent;

				$formatted_percentage = $total_percentage / 100;
				$temp_fee = $amount / ( 1 - $formatted_percentage );
				// if non gst
				$mednefits_pecent = $trans->medi_percent / 100;
				$fee = $temp_fee * $mednefits_pecent;
			} else {
				$fee = number_format((float)$trans->co_paid_amount, 2);
			}
		} else {
			if(strrpos($trans->clinic_discount, '%')) {
				$percentage = chop($trans->clinic_discount, '%');
				if($trans->credit_cost > 0) {
					$amount = $trans->credit_cost;
				} else {
					$amount = $trans->procedure_cost;
				}

				$total_percentage = $percentage + $trans->medi_percent;

				$formatted_percentage = $total_percentage / 100;
				$temp_fee = $amount / ( 1 - $formatted_percentage );
				// if non gst
				$mednefits_pecent = $trans->medi_percent / 100;
				$temp_mednefits_fee = $temp_fee * $mednefits_pecent;
				$fee = $temp_mednefits_fee * $trans->gst_percent_value;
			} else {
				$fee = number_format((float)$trans->co_paid_amount, 2);
			}
		}

		return $fee;
	}

	public function updateClinicDiscount( )
	{
		$clinics = DB::table('clinic')->where('Clinic_Type', 2)->get();

		$clinic_discount = '20%';
		$mednefits_discount = 10;
		$clinic_class = new Clinic();
		$result = [];
		foreach ($clinics as $key => $clinic) {
			$result[]['status'] = $clinic_class->updateClinic($clinic->ClinicID, ['discount' => $clinic_discount, 'medicloud_transaction_fees' => $mednefits_discount]);
		}

		return $result;
	}

	public function updateClinicDentalTransactions( )
	{
		$clinics = DB::table('clinic')->where('Clinic_Type', 2)->get();
		$clinic_discount = '20%';
		$mednefits_discount = 10;

		$result = [];
		$transaction = new Transaction();
		foreach ($clinics as $key => $clinic) {
			$result[]['status'] = $transaction->updateTransaction($clinic->ClinicID, ['clinic_discount' => $clinic_discount, 'medi_percent' => $mednefits_discount]);
		}

		return $result;
	}

	public function testRefund($id)
	{
		$transaction = DB::table('transaction_history')->where('transaction_id', $id)->first();

		$user = DB::table('user')->where('UserID', $transaction->UserID)->first();
		$clinic = DB::table('clinic')->where('ClinicID', $transaction->ClinicID)->first();
		$transaction_id = str_pad($transaction->transaction_id, 6, "0", STR_PAD_LEFT);
		$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
		$procedure_temp = "";

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

		$type = "";
		$image = "";
		if($clinic_type->head == 1 || $clinic_type->head == "1") {
			if($clinic_type->Name == "General Practitioner") {
				$type = "General Practitioner";
				$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
			} else if($clinic_type->Name == "Dental Care") {
				$type = "Dental Care";
				$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
			} else if($clinic_type->Name == "Traditional Chinese Medicine") {
				$type = "Traditional Chinese Medicine";
				$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
			} else if($clinic_type->Name == "Health Screening") {
				$type = "Health Screening";
				$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
			} else if($clinic_type->Name == "Wellness") {
				$type = "Wellness";
				$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
			} else if($clinic_type->Name == "Health Specialist") {
				$type = "Health Specialist";
				$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
			}
		} else {
			$find_head = DB::table('clinic_types')
			->where('ClinicTypeID', $clinic_type->sub_id)
			->first();
			if($find_head->Name == "General Practitioner") {
				$type = "General Practitioner";
				$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
			} else if($find_head->Name == "Dental Care") {
				$type = "Dental Care";
				$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
			} else if($find_head->Name == "Traditional Chinese Medicine") {
				$type = "Traditional Chinese Medicine";
				$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
			} else if($find_head->Name == "Health Screening") {
				$type = "Health Screening";
				$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
			} else if($find_head->Name == "Wellness") {
				$type = "Wellness";
				$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
			} else if($find_head->Name == "Health Specialist") {
				$type = "Health Specialist";
				$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
			}
		}

		$email['member'] = ucwords($user->Name);
		$email['credits'] = number_format($transaction->credit_cost, 2);
		$email['transaction_id'] = strtoupper(substr($clinic->Name, 0, 3)).$transaction_id;
		$email['transaction_date'] = date('d F Y, h:ia', strtotime($transaction->date_of_transaction));
		$email['health_provider_name'] = ucwords($clinic->Name);
		$email['health_provider_address'] = $clinic->Address;
		$email['health_provider_city'] = $clinic->City;
		$email['health_provider_country'] = $clinic->Country;
		$email['health_provider_phone'] = $clinic->Phone;
		$email['service'] = ucwords($clinic_type->Name).' - '.$service;
		$email['emailSubject'] = 'Member - Refunded Transaction';
		$email['emailTo'] = $user->Email;
		$email['emailName'] = ucwords($user->Name);
		$email['url'] = 'http://staging.medicloud.sg';
		$email['clinic_type_image'] = $image;
		$email['emailPage'] = 'email-templates.member-refunded-transaction';

			// $pdf = PDF::loadView('pdf-download.member-refunded-transac', $email);
			// $pdf->getDomPDF()->get_option('enable_html5_parser');
	  //   $pdf->setPaper('A4', 'landscape');

	    // return $pdf->stream();
		return View::make('pdf-download.health-partner-successful-transac', $email);
			// EmailHelper::sendEmail($email);

			// $email['emailTo'] = 'info@medicloud.sg';
			// EmailHelper::sendEmail($email);
	}

	public function testCompany($id)
	{

		$e_claim = [];
		$transaction_details = [];
		$statement_in_network_amount = 0;
		$statement_e_claim_amount = 0;

	    // check if there is no statement
		$statement = DB::table('company_credits_statement')
		->where('statement_id', $id)
		->first();
	    // get transaction if there is another transaction

		$statement_id = $statement->statement_id;
		$statement = DB::table('company_credits_statement')
		->where('statement_id', $statement_id)
		->first();

		$in_network_transaction_array = [];
		$e_claim_transaction_array = [];
	    // get in-network and e-claim transactions from statement
		$in_network_transaction_temp = DB::table('statement_in_network_transactions')
		->where('statement_id', $statement_id)
		->get();

		$e_claim_transaction_temp = DB::table('statement_e_claim_transactions')
		->where('statement_id', $statement_id)
		->get();

		foreach ($in_network_transaction_temp as $key => $in_network_temp) {
			array_push($in_network_transaction_array, $in_network_temp->transaction_id);
		}

		foreach ($e_claim_transaction_temp as $key => $e_claim_temp) {
			array_push($e_claim_transaction_array, $e_claim_temp->e_claim_id);
		}


		if(sizeof($in_network_transaction_array) > 0) {
			$transactions = DB::table('transaction_history')
			->where('deleted', 0)
			->whereIn('transaction_id', $in_network_transaction_array)
			->get();

	      // in-network transactions
			foreach ($transactions as $key => $trans) {
				if($trans) {
					if($trans->deleted == 0 || $trans->deleted == "0") {
						$statement_in_network_amount += $trans->credit_cost;
					}

				}
			}

		}

		if(sizeof($e_claim_transaction_array) > 0) {
			$e_claim_result = DB::table('e_claim')
			->whereIn('e_claim_id', $e_claim_transaction_array)
			->get();
			foreach($e_claim_result as $key => $res) {
				if($res) {
					if($res->status == 1) {
						$statement_e_claim_amount += $res->amount;
					}

				}
			}
		}

		$company = DB::table('corporate')
		->join('customer_link_customer_buy', 'customer_link_customer_buy.corporate_id', '=', 'corporate.corporate_id')
		->where('customer_link_customer_buy.customer_buy_start_id', $statement->statement_customer_id)
		->first();


		$total_amount = $statement_in_network_amount;
		$statement_e_claim_amount = 0.00;

		$new_statement = array(
			"created_at"                => $statement->created_at,
			"statement_contact_email"   => $statement->statement_contact_email,
			"statement_contact_name"    => ucwords($statement->statement_contact_name),
			"statement_contact_number"  => $statement->statement_contact_number,
			"statement_customer_id"     => $statement->statement_customer_id,
			"statement_date"            => date('d F Y', strtotime($statement->statement_date)),
			'statement_due'             => date('d F Y', strtotime($statement->statement_due)),
			"statement_e_claim_amount"  => number_format($statement_e_claim_amount, 2),
			"statement_end_date"        => date('F d Y', strtotime($statement->statement_end_date)),
			"statement_id"              => $statement->statement_id,
			"statement_in_network_amount"   => number_format($statement_in_network_amount, 2),
			"statement_number"              => $statement->statement_number,
			"statement_reimburse_e_claim"   => $statement->statement_reimburse_e_claim,
			"statement_start_date"          => date('F d', strtotime($statement->statement_start_date)),
			"statement_status"              => $statement->statement_status,
			"statement_total_amount"        => number_format($total_amount, 2),
			"updated_at"                    => $statement->updated_at,
			'company'										=> ucwords($company->company_name),
			'emailSubject'							=> 'Company Monthly Invoice',
			'emailTo'										=> $statement->statement_contact_email,
			'emailPage'				=> 'email-templates.company-monthly-invoice',
			'emailName'									=> ucwords($company->company_name)
		);


		return EmailHelper::sendEmailCompanyInvoiceWithAttachment($new_statement);
    // return $new_statement;
		// return View::make('email-templates.company-monthly-invoice', $new_statement);
	}

	public function checkDuplicateTransaction( )
	{
		$input = Input::all();
		// $clinic_id = $input['clinic_id'];
		$getSessionData = StringHelper::getMainSession(3);

		if(!$getSessionData) {
			return array('status' => TRUE, 'error' => 1, 'message' => 'No token');
		}
		$clinic_id = $getSessionData->Ref_ID;

		$date = date('Y-m-d', strtotime($input['date_transaction']));
		$format = [];

		$check  = DB::table("transaction_history")
		->join("user", "user.UserID", "=", "transaction_history.UserID")
		->where("transaction_history.clinicID", $clinic_id)
		->where("transaction_history.UserID", $input['user_id'])
		->where("transaction_history.date_of_transaction", "like", "%".$date."%")
		->where("transaction_history.deleted", 0)
		->select('transaction_history.ClinicID', 'user.Name as user_name', 'user.UserID', 'transaction_history.date_of_transaction', 'transaction_history.procedure_cost', 'transaction_history.paid', 'user.NRIC', 'transaction_history.transaction_id', 'transaction_history.medi_percent', 'transaction_history.clinic_discount', 'transaction_history.co_paid_status', 'transaction_history.multiple_service_selection', 'transaction_history.transaction_id', 'transaction_history.ProcedureID', 'transaction_history.co_paid_amount', 'transaction_history.in_network', 'transaction_history.mobile', 'transaction_history.health_provider_done', 'transaction_history.credit_cost', 'transaction_history.credit_divisor', 'transaction_history.paid', 'transaction_history.deleted', 'transaction_history.refunded', 'transaction_history.health_provider_done', 'transaction_history.gst_percent_value', 'transaction_history.currency_type', 'transaction_history.currency_amount')
		->orderBy('transaction_history.created_at', 'desc')
		->get();

		if(sizeof($check) == 0) {
			return array('status' => FALSE, 'error' => 0, );
		}

		foreach ($check as $key => $trans) {
			$procedure_temp = "";
			$procedure = "";
			$procedure_ids = [];
			$transaction_status = '';
			$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();


			if($trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1")
			{
	        // get multiple service
				$service_lists = DB::table('transaction_services')
				->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
				->where('transaction_services.transaction_id', $trans->transaction_id)
				->get();

				foreach ($service_lists as $key => $service) {
					array_push($procedure_ids, $service->service_id);
					if(sizeof($service_lists) - 2 == $key) {
						$procedure_temp .= ucwords($service->Name).' and ';
					} else {
						$procedure_temp .= ucwords($service->Name).' ';
					}
					$procedure = rtrim($procedure_temp, ',');
					$procedure_id = 0;
				}
			} else {
				$service_lists = DB::table('clinic_procedure')
				->where('ProcedureID', $trans->ProcedureID)
				->first();
				if($service_lists) {
					array_push($procedure_ids, $trans->ProcedureID);
					$procedure = ucwords($service_lists->Name);
					$procedure_id = $trans->ProcedureID;
				} else {
					$procedure_id = $trans->ProcedureID;
				}
			}

			if($trans->credit_cost > 0) {
				$transaction_type = "Credits";
			} else {
				$transaction_type = "Cash";
			}



			if($trans->deleted == 1 && $trans->refunded == 1 || $trans->deleted == "1" && $trans->refunded == "1") {
				$transaction_status = 'REFUNDED';
			} else if($trans->deleted == 1 && $trans->health_provider_done == 1 || $trans->deleted == 1 && $trans->health_provider_done == "1"){
				$transaction_status = 'REMOVED';
			}

			$amount = 0;
			$currency_symbol = "S$";
			if($trans->currency_type == "myr") {
				$amount = $trans->procedure_cost * $trans->currency_amount;
				$currency_symbol = "RM";
			} else {
				$amount = $trans->procedure_cost;
			}

			$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);
			$temp = array(
				'ClinicID'					=> $trans->ClinicID,
				'NRIC'						=> $trans->NRIC,
				'ProcedureID'				=> $procedure_id,
				'UserID'					=> $trans->UserID,
				'date_of_transaction'		=> date('d F Y, h:i a', strtotime($trans->date_of_transaction)),
				'paid'						=> $trans->paid,
				'procedure_cost'			=> number_format($amount, 2),
				'service'					=> $procedure,
				'transaction_id'			=> strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
				'user_name'					=> ucwords($trans->user_name),
				'transaction_type'			=> $transaction_type,
				'transaction_status'		=> $transaction_status,
				'currency_symbol'			=> $currency_symbol
			);
			array_push($format, $temp);
		}

		$user_id = StringHelper::getUserId($input['user_id']);
		$user = DB::table("user")->where("UserID", $user_id)->first();
		$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
		$new_transaction = array(
			'name'		=> ucwords($user->Name),
			'date'		=> date('d F Y', strtotime($trans->date_of_transaction)),
			'amount'	=> number_format($input['amount'] ? $input['amount'] : 0, 2),
			'type'		=> 'Cash',
			'currency_type' => strtoupper($wallet->currency_type)
		);

		return array('status' => TRUE, 'error' => 0, 'duplicates' => $format, 'new_transaction' => $new_transaction);
	}
}
