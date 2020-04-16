<?php

use Illuminate\Support\Facades\Input;
class testcontroller extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('test');

	}

	public function getMedisysDoctorLists( )
	{	
		return MediSys_Library::getMedisysDoctorList( );
	}
	
	public function getMedisysDoctorBlocks( $doctor_id )
	{
		return MediSys_Library::getMedisysDoctorBlocks( $doctor_id );
	}

	public function createAppointmentMedisys( $doctor_id )
	{
		return MediSys_Library::createAppointmentMedisys( $doctor_id );
	}

	public function getMedisysAppointment( $calendar_id )
	{
		return MediSys_Library::getMedisysAppointment( $calendar_id );
	}

	public function eCardtest( )
	{
		$users = DB::table('user')->where('UserType', 5)->where('Active', 1)->paginate(1000);
		$e_card = new UserPackage();
		$results = array();
		// $results[]['data'] = $users;
		foreach ($users as $key => $user) {
			try {
				$result = $e_card->newEcardDetails($user->UserID);

				if($result) {
					$results[] = array('success' => true, 'user_id' => $user->UserID);
				}
			} catch(Exception $e) {
				return array('success' => false, 'user_id' => $user->UserID, 'error' => $e->getMessage());
			}
		}

		return $results;
	}

	public function downloadPlanInvoice( )
	{
		$bcontroller = new BenefitsDashboardController();

		$actives = DB::table('customer_active_plan')->paginate(100);
		$results = [];

		foreach ($actives as $key => $active) {
			$results[] = $bcontroller->benefitsReceipt($active->customer_active_plan_id);
		}

		return $results;
	}

	public function testSubmitClaimData( )
	{
		ini_set('memory_limit', '2028M');
		ini_set('max_execution_time', 600); 
		$users = DB::table('user')->where('Active', 1)->paginate(100);
		$results = [];
		foreach ($users as $key => $user) {
			$temp = array(
				'id'	=> $user->UserID,
				'procedure_ids'	=> [2008],
				'transaction_date'	=> date('Y-m-d H:i:s'),
				'claim_date'	=> date('Y-m-d H:i:s'),
				'currency_amount'	=> 3.0044,
				'back_date'		=> 1,
				'amount'		=> 10
			);

			try {
				$results[] = self::testSubmitClaim($temp);
			} catch(Exception $e) {
				return array('status' => false, 'error' => $e->getMessage());
			}
		}

		return $results;
	}

	public function testSubmitClaim($data)
	{
		$input = $data;
		$clinic_id = 4813;
		$clinic_data = DB::table('clinic')->where('ClinicID', $clinic_id)->first();
    	$transaction = new Transaction();
    	$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic_data->Clinic_Type)->first();
    	$lite_plan_status = false;
        $lite_plan_status = StringHelper::litePlanStatus($input['id']);
    	
        if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
            $lite_plan_enabled = 1;
        } else {
            $lite_plan_enabled = 0;
        }

		if($input['back_date'] == 1 || $input['back_date'] == "1") {
			// check if is a valid date
	    	if(!strtotime($input['transaction_date'])) {
	    		return array('status' => FALSE, 'message' => 'Date/Time of Visit is not a valid date.');
	    	}

			// check if multiple services

			if(sizeof($input['procedure_ids']) == 1) {
				$multiple = 0;
			} else if(sizeof($input['procedure_ids']) > 1) {
				$multiple = 1;
			}

			if($multiple == 0) {

				if($clinic_data->co_paid_status == 1 || $clinic_data->co_paid_status == "1") {
					$co_paid_amount = $clinic_data->gst_amount;
					$co_paid_status = $clinic_data->co_paid_status;
				} else {
					$co_paid_amount = $clinic_data->co_paid_amount;
					$co_paid_status = $clinic_data->co_paid_status;
				}

				$temp = array(
					'UserID'							=> $input['id'],
					'ProcedureID'					=> $input['procedure_ids'][0],
					'date_of_transaction'	=> date('Y-m-d H:i:s', strtotime($input['transaction_date'])),
					'claim_date'			=> date('Y-m-d H:i:s'),
					'ClinicID'						=> $clinic_id,
					'procedure_cost'			=> number_format($input['amount'], 2),
					'AppointmenID'				=> 0,
					'revenue'							=> 0,
					'debit'								=> 0,
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
					'currency_amount'			=> $input['currency_amount'],
					'lite_plan_enabled'     => $lite_plan_enabled
				);

				try {
					$result = $transaction->createTransaction($temp);
					if($lite_plan_enabled == 1) {
						$transaction_data = DB::table('transaction_history')->where('transaction_id', $result->id)->first();

						$wallet_data = DB::table('e_wallet')->where('UserID', $input['id'])->first();
						
						if($transaction_data->spending_type == "medical") {
							$balance = $wallet_data->balance;
						} else {
							$balance = $wallet_data->wellness_balance;
						}
						// check user credits and deduct

						if($balance > $input['amount']) {
							$wallet = new Wallet( );
							// deduct wallet
							$lite_plan_credits_log = array(
                                'wallet_id'     => $wallet_data->wallet_id,
                                'credit'        => $transaction_data->co_paid_amount,
                                'logs'          => 'deducted_from_mobile_payment',
                                'running_balance' => $balance - $transaction_data->co_paid_amount,
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
									$wallet->deductCredits($transaction_data->UserID, $transaction_data->co_paid_amount);
								} else {
									$wallet->deductWellnessCredits($transaction_data->UserID, $transaction_data->co_paid_amount);
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
				$transaction_services = new TransctionServices( );

				if($clinic_data->co_paid_status == 1 || $clinic_data->co_paid_status == "1") {
					$co_paid_amount = $clinic_data->gst_amount;
					$co_paid_status = $clinic_data->co_paid_status;
				} else {
					$co_paid_amount = $clinic_data->co_paid_amount;
					$co_paid_status = $clinic_data->co_paid_status;
				}

				$temp = array(
					'UserID'							=> $input['id'],
					'ProcedureID'					=> 0,
					'date_of_transaction'	=> date('Y-m-d H:i:s', strtotime($input['transaction_date'])),
					'claim_date'			=> date('Y-m-d H:i:s'),
					'ClinicID'						=> $clinic_id,
					'procedure_cost'			=> number_format($input['amount'], 2),
					'AppointmenID'				=> 0,
					'revenue'							=> 0,
					'debit'								=> 0,
					'medi_percent'				=> $clinic_data->medicloud_transaction_fees,
					'clinic_discount'			=> $clinic_data->discount,
					'wallet_use'					=> 0,
					'wallet_id'						=> 0,
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
					'currency_amount'			=> $input['currency_amount'],
					'lite_plan_enabled'     => $lite_plan_enabled
				);
				$result = $transaction->createTransaction($temp);

				try {
					if($lite_plan_enabled == 1) {
						$transaction_data = DB::table('transaction_history')->where('transaction_id', $result->id)->first();

						$wallet_data = DB::table('e_wallet')->where('UserID', $input['id'])->first();
						
						if($transaction_data->spending_type == "medical") {
							$balance = $wallet_data->balance;
						} else {
							$balance = $wallet_data->wellness_balance;
						}
						// check user credits and deduct

						if($balance > $input['amount']) {
							$wallet = new Wallet( );
							// deduct wallet
							$lite_plan_credits_log = array(
                                'wallet_id'     => $wallet_data->wallet_id,
                                'credit'        => $transaction_data->co_paid_amount,
                                'logs'          => 'deducted_from_mobile_payment',
                                'running_balance' => $balance - $transaction_data->co_paid_amount,
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
									$wallet->deductCredits($transaction_data->UserID, $transaction_data->co_paid_amount);
								} else {
									$wallet->deductWellnessCredits($transaction_data->UserID, $transaction_data->co_paid_amount);
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

					$transaction_services->createTransctionServices($input['procedure_ids'], $result->id);
				} catch(Exception $e) {
					$email = [];
					$email['end_point'] = url('clinic/save/claim/transaction', $parameter = array(), $secure = null);
					$email['logs'] = 'Save Claim Transaction - '.$e->getMessage();
					$email['emailSubject'] = 'Error log.';
					EmailHelper::sendErrorLogs($email);
					return array('status' => FALSE, 'message' => 'Failed to save transaction.');
				}
			}
		} else if($input['health_provider'] == 1) {
			$temp = array(
				'procedure_cost' 	=> number_format($input['amount'], 2),
				'claim_date'		=> date('Y-m-d H:i:s'),
				'co_paid_status'	=> $clinic_data->co_paid_status,
				'medi_percent'		=> $clinic_data->medicloud_transaction_fees,
				'clinic_discount'	=> $clinic_data->discount,
				'paid'				=> 1,
				'currency_type'		=> $clinic_data->currency_type,
				'currency_amount'	=> $input['currency_amount']
			);

			try {
				$result =  $transaction->updateTransaction($input['transaction_id'], $temp);
				if($lite_plan_enabled == 1) {
					$transaction_data = DB::table('transaction_history')->where('transaction_id', $input['transaction_id'])->first();

					$wallet_data = DB::table('e_wallet')->where('UserID', $input['id'])->first();
					
					if($transaction_data->spending_type == "medical") {
						$balance = $wallet_data->balance;
					} else {
						$balance = $wallet_data->wellness_balance;
					}
					// check user credits and deduct

					if($balance > $input['amount']) {
						$wallet = new Wallet( );
						// deduct wallet
						$lite_plan_credits_log = array(
                            'wallet_id'     => $wallet_data->wallet_id,
                            'credit'        => $transaction_data->co_paid_amount,
                            'logs'          => 'deducted_from_mobile_payment',
                            'running_balance' => $balance - $transaction_data->co_paid_amount,
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
								$wallet->deductCredits($transaction_data->UserID, $transaction_data->co_paid_amount);
							} else {
								$wallet->deductWellnessCredits($transaction_data->UserID, $transaction_data->co_paid_amount);
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
							$email['logs'] = 'Save Claim Transaction With Credits GST - '.$e->getMessage();
							$email['emailSubject'] = 'Error log.';
							EmailHelper::sendErrorLogs($email);
							return array('status' => FALSE, 'message' => 'Failed to save transaction.');
						}
					}
				}
			} catch(Exception $e) {
				$email = [];
				$email['end_point'] = url('clinic/save/claim/transaction', $parameter = array(), $secure = null);
				$email['logs'] = 'Save Claim Transaction - '.$e->getMessage();
				$email['emailSubject'] = 'Error log.';
				EmailHelper::sendErrorLogs($email);
				return array('status' => FALSE, 'message' => 'Failed to save transaction.');
			}

		}
		self::insertOrCheckInInvoice($clinic_id, date('Y-m-d'));
		return array('status' => TRUE, 'result' => $result);
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

	public function testSMSsend( )
	{
		$input = Input::all();
		return SmsHelper::checkPhone($input['phone']);
	}

	public function testSendSmsEnroll( )
	{
		$input = Input::all();

		$data = [];
		$data['name'] = 'allan cheam alzula';
		$data['company'] = 'mednefits';
		$data['plan_start'] = date('F d, Y');
		$data['email'] = 'allan.alzula.work@gmail.com';
		$data['nric'] = 'fdddsad2432';
		$data['password'] = '123';
		$data['phone'] = '+639064317892';
		$data['sms_type'] = 'LA';
		$data['message'] = SmsHelper::formatWelcomeEmployeeMessage($data);
		// return $data['message'];
		return SmsHelper::sendSms($data);
	}

	public function testNRIC( )
	{
		$input = Input::all();

		$result = PlanHelper::validIdentification($input['nric']);

		return array('result' => $result);
	}

	public function testGetuserPlanCoverage( )
	{
		$input = Input::all();
		return PlanHelper::getEmployeeCoverageStatus($input['user_id']);
	}

	public function getUserEmployee( )
	{
		$employees = DB::table('user')->where('UserType', 5)->whereIn('access_type', [0, 1])->get();
		$user = new User();

		foreach ($employees as $key => $employee) {
			try {
				$updateArray['userid'] = $employee->UserID;
				$updateArray['Password'] = StringHelper::encode('123');
				$updateArray['ResetLink'] = null;
				$updateArray['updated_at'] = time();
				$updatedUser = $user->updateUser($updateArray);
		        if($updatedUser){
		            // delete token
		            $get_session = DB::table('oauth_sessions')->where('owner_id', $employee->UserID)->orderBy('created_at', 'desc')->first();

		            if($get_session) {
		              DB::table('oauth_access_tokens')->where('session_id', $get_session->id)->delete();
		            }
				}
			} catch(Exception $e) {
				return $e->getMessage();
			}
		}
	}

	public function uploadImage( )
	{
	 if(Input::file('file')){
          $uploadFile = Image_Library::CloudinaryUploadFileWithResizer(Input::file('file'), 150, 150);
          if($uploadFile){
              return $uploadFile;
          }
      } 
	}

	public function updateClinicDefaultImage( )
	{
		return DB::table('clinic')
					->where('image', 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1439208475/medilogo_cn6d0x.png')
					->update(['image' => 'https://res.cloudinary.com/dzh9uhsqr/image/upload/v1556768437/rhknwowt6mjrmslv0a8i.png']);
	}

	public function ImageAutoQuality( )
	{
		$image = "https://res.cloudinary.com/www-medicloud-sg/image/upload/v1439208475/medilogo_cn6d0x.png";
		return FileHelper::formatImageAutoQuality($image);
		// $images = DB::table('e_claim_docs')
		// 			->where('file_type', 'image')
		// 			->get();

		return $images;
	}

	public function getCurrencyLists( )
	{
		return EclaimHelper::getCurrencies();
	}

	public function testEclaimSendEmail( )
	{
		$input = Input::all();
		return EclaimHelper::sendEclaimEmail($input['user_id'], $input['e_claim_id']);
	}

	public function testEclaimUploadQueue( )
	{
		$input = Input::all();
		$data = [];
		$file = Input::file('file');
		$file_name = time().' - '.$file->getClientOriginalName();
		// $receipt_file = $file_name;
        // $file->move(public_path().'/temp_uploads/', $file_name);
		// $data['file'] = public_path().'/temp_uploads/'.$file_name;
		// $data['e_claim_id'] = $input['e_claim_id'];
		return FileHelper::compress_image($file, public_path().'/temp_uploads', 50);
		// return EclaimFileUploadQueue::fire(null, $data);
		// return Queue::connection('redis_high')->push('\EclaimFileUploadQueue', $data);
	}

	public function testFormatDate( )
	{
		$input = Input::all();

		$result = PlanHelper::validateDate($input['date'], 'd-m-Y');
		return array('result' => $result, 'date' => date('Y-m-d', strtotime($input['date'])));
	}

	public function testGetMedicalBalanceByDate( )
	{
		$input = Input::all();

		$user_id = $input['user_id'];
		$date = $input['date'];
		$spending_type = $input['spending_type'];

		if($input['type'] == "member") {
			return EclaimHelper::getSpendingBalance($user_id, $date, $spending_type);
		} else {
			$result = CustomerHelper::getCustomerCreditReset($user_id, $input['filter'], $spending_type);
			return ['result' => $result];
		}
	}


	public function getMemberResetDateTest( )
	{
		$input = Input::all();

		return PlanHelper::getMemberCreditReset($input['id'], $input['spending_type']);
	}
	public function testMemberResetDates( )
	{
		$input = Input::all();
		$user_id = $input['user_id'];
		$filter = $input['filter'];
		$spending_type = $input['spending_type'];
		$user_spending_dates = MemberHelper::getMemberCreditReset($user_id, $filter, $spending_type);
		return $user_spending_dates;
	}

	public function testCustomerResetDates( )
	{
		$input = Input::all();
		$user_id = $input['user_id'];
		$filter = $input['filter'];
		$spending_type = $input['spending_type'];
		$user_spending_dates = CustomerHelper::getCustomerCreditReset($user_id, $filter, $spending_type);
		return $user_spending_dates;
	}

	public function testBalance( )
	{
		$input = Input::all();
		return PlanHelper::reCalculateEmployeeBalance($input['member_id']);
	}

	public function testReturnBalance( )
	{
		$input = Input::all();
		return PlanHelper::returnMemberMedicalBalance($input['member_id']);
	}
}