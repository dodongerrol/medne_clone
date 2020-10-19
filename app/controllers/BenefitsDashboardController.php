<?php
use Illuminate\Support\Facades\Input;

class BenefitsDashboardController extends \BaseController {

	public function getDownloadToken( )
	{
		$hr = self::checkSession();
		// $input = Input::all();
		$hr = DB::table('customer_hr_dashboard')->where('hr_dashboard_id', $hr->hr_dashboard_id)->first();

		if($hr) {
			$api = null;

			if(url() == "https://medicloud.sg" || url() == "https://hrapi.medicloud.sg") {
				$api = "https://api.medicloud.sg/hr/login_token";
				$download_link = "https://api.medicloud.sg/hr";
				$live = true;
				$data = array(
					'email'		=> $hr->email,
					'password'	=> $hr->password
				);

				$result = httpLibrary::postHttp($api, $data, null);
				if($result['status']) {
					$result['download_link'] = $download_link;
					$result['live'] = $live;
					return $result;
				} else {
					return array('status' => false, 'message' => 'Failed to get download token.');
				}
			}

			return array('status' => false, 'message' => 'Only Live Server can download a token.');
		} else {
			return array('status' => false, 'message' => 'HR Account not found.');
		}
	}
	public function hrLogin( )
	{
		$input = Input::all();

		// return $input;
		$result = DB::table('customer_hr_dashboard')
		->where('email', $input['email'])
		->where('password', md5($input['password']))
		->where('active', 1)
		->first();

		if($result) {
			// Session::put('hr-session', $result);
			// create token
			$jwt = new JWT();
			$secret = Config::get('config.secret_key');

			if(isset($input['signed_in']) && $input['signed_in'] == true) {
				$result->signed_in = TRUE;
			} else {
				$result->signed_in = FALSE;
				$result->expire_in = strtotime('+15 days', time());
			}
			
			$token = $jwt->encode($result, $secret);

			$hr = new HRDashboard();
			$hr->updateCorporateHrDashboard($result->hr_dashboard_id, array('login_ip' => $_SERVER['REMOTE_ADDR']));
			// Session::put('customer-session-id', $result->customer_buy_start_id);
			$customer_credits = new CustomerCredits( );
			$check = $customer_credits->checkCustomerCredits($result->customer_buy_start_id);
			if($check == 0) {
				$data = array(
					'customer_id'	=> $result->customer_buy_start_id,
					'active'			=> 1
				);
				$customer_credits->createCustomerCredits($data);
			}

			$admin_logs = array(
				'admin_id'  => $result->hr_dashboard_id,
				'admin_type' => 'hr',
				'type'      => 'admin_hr_login_portal',
				'data'      => SystemLogLibrary::serializeData($input)
			);
			SystemLogLibrary::createAdminLog($admin_logs);

			return array(
				'status'	=> TRUE,
				'message'	=> 'Success.',
				'token' => $token
			);
		} else {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Invalid credentials.'
			);
		}
	}

	public function logOutHr( )
	{
		Session::forget('hr-session');
		return Redirect::to('company-benefits-dashboard-login');
	}

	public function updateShowDone( )
	{
		$result = self::checkSession();
		return \CorporateBuyStart::where('customer_buy_start_id', $result->customer_buy_start_id)->update(['show_done' => 1]);
	}

	public function getPlanStatus( )
	{
		$hr = self::checkSession();
		$result = PlanHelper::getPlanExpiration($hr->customer_buy_start_id);
		$result['status'] = true;
		return $result;
	}

	public function hrStatus( )
	{
		$hr = self::checkSession();
		$settings = DB::table('customer_buy_start')->where('customer_buy_start_id', $hr->customer_buy_start_id)->first();

		if((int)$settings->qr_payment == 1 && (int)$settings->wallet == 1) {
			$accessibility = 1;
		} else {
			$accessibility = 0;
		}

		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $hr->customer_buy_start_id)->first();

		$session = array(
			'hr_dashboard_id'				=> $hr->hr_dashboard_id,
			'customer_buy_start_id'			=> $hr->customer_buy_start_id,
			'qr_payment'					=> $settings->qr_payment,
			'wallet'						=> $settings->wallet,
			'accessibility'					=> $accessibility,
			'expire_in'						=> $hr->expire_in,
			'signed_in'						=> $hr->signed_in,
			'account_type'					=> $plan->account_type
		);
		return $session;
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

	public function checkSession( )
	{
		$result = StringHelper::getJwtHrSession();
		if(!$result) {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Need to authenticate user.'
			);
		}
		return $result;
	}

	public function calculatePrices( )
	{
		$result = Session::get('temp_enrollment_users');
		$new_active_plan = Session::get('new_active_plan');

		if($result && $new_active_plan) {
			return array(
				'status'	=> TRUE,
				'employees' => sizeof($result),
				'price'		=> sizeof($result) * 99,
				'plan'		=> $new_active_plan
			);
		} else {
			return array(
				'status'	=> FALSE,
			);
		}
	}
	
	public function getCompanyDetails( ) 
	{
		$result = self::checkSession();

		$company = DB::table("customer_business_information")->where("customer_buy_start_id", $result->customer_buy_start_id)->first();

		return array('status' => TRUE, 'data' => ucwords($company->company_name));
	}

	public function getCompanyPlanAccountType( ) 
	{
		$result = self::checkSession();
		$plan = DB::table("customer_plan")
		->where("customer_buy_start_id", $result->customer_buy_start_id)
		->orderBy('created_at', 'desc')
		->first();

		return array('status' => TRUE, 'account_type' => $plan->account_type);
	}

	public function checkPlan( )
	{

		$result = self::checkSession();
		$plans = [];

		$plan = DB::table('customer_plan')
		->where('customer_buy_start_id', $result->customer_buy_start_id)
		->orderBy('created_at', 'desc')
		->first();

		$active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
		$account = DB::table('customer_buy_start')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

		if($active_plan->paid == "true") {
			$paid = TRUE;
		} else {
			$paid = FALSE;
		}

		$count_employees = DB::table('corporate_members')
		->join('customer_link_customer_buy', 'customer_link_customer_buy.corporate_id', '=', 'corporate_members.corporate_id')
		->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_link_customer_buy.customer_buy_start_id')
		->where('customer_link_customer_buy.customer_buy_start_id', $result->customer_buy_start_id)
		->count();

		if($paid == TRUE && $count_employees > 0 && $account->show_done == 1 || $paid == TRUE && $count_employees > 0 && $account->show_done == "1") {
			$checks = TRUE;
		} else {
			$checks = FALSE;
		}

		// check for dependents
		$dependents = DB::table('dependent_plans')
		->where('customer_plan_id', $plan->customer_plan_id)
		->count();

		if($count_employees > 0) {
			$paid = true;
		}

		if($paid == true && $count_employees > 0) {
			$checks = true;
		}

		return array('status' => TRUE , 
			'data' => array('paid' => $paid, 
				'employee_count' => $count_employees, 
				'cheque' => TRUE, 
				'agree_status' => $account->agree_status, 
				'checks' => $checks, 
				'plan' => $active_plan,
				'dependent_status'	=> $dependents > 0 ? true : false
			)
		);
	}

	public function updateAgreeStatus( )
	{
		$input = Input::all();
		
		if(empty($input['hr_id']) || $input['hr_id'] == null) {
			return ['status' => false, 'message' => 'hr_id is required'];
		}

		$result = DB::table('customer_hr_dashboard')->where('hr_dashboard_id', $input['hr_id'])->first();

		if(!$result) {
			return ['status' => false, 'message' => 'hr id does not exist'];
		}
		// $result = self::checkSession();
		// return json_encode($result);
		$customer_start = new CorporateBuyStart();
		return $customer_start->updateAgreeStatus($result->customer_buy_start_id);
	}

	public function employeeEnrollmentProgress( )
	{
		$result = self::checkSession();
		$total_employees = 0;

		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->first();

		$active_plans = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->get();
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
		$added_purchase = 0;

		$plan_status = DB::table('customer_plan_status')->where('customer_plan_id', $plan->customer_plan_id)->first();
		$in_progress = $plan_status->employees_input - $plan_status->enrolled_employees;
		$end_plan = date('d/m/Y', strtotime('-5 days', strtotime($plan->plan_start)));

		$added_purchase_status = Session::get('added_purchase_status');
		if($added_purchase_status) {
			$added_purchase_status = TRUE;
		} else {
			$added_purchase_status = FALSE;
		}

		if($in_progress <= 0) {
			$in_progress = 0;
		}

		if($plan->account_type == "lite_plan" || $plan->account_type == "out_of_pocket") {
			$in_progress = 99999;
		}

		return array(
			'status'	=> TRUE,
			'data'		=> array(
				'in_progress' => $in_progress,
				'completed' => $plan_status->enrolled_employees,
				'active_plans' => $active_plans,
				'total_employees' => $plan_status->employees_input,
				'plan_end_date'	=> $end_plan,
				'added_purchase_status' => $added_purchase_status,
				'account_type'	=> $plan->account_type
			)
		);
	}

	public function uploadExcel( )
	{

		$input = Input::all();
		$result = self::checkSession();
		$total_employees = 0;

		$planned = DB::table('customer_plan')->where('customer_buy_start_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
		$plan_status = DB::table('customer_plan_status')->where('customer_plan_id', $planned->customer_plan_id)->orderBy('created_at', 'desc')->first();
		$total = $plan_status->employees_input - $plan_status->enrolled_employees;

		$rules = array(
			'file' => 'required|mimes:xlsx,xls|max:200000'
		);

		if(Input::hasFile('file'))
		{
			$validator = \Validator::make( Input::all() , $rules);
			if($validator->passes()){
				$file = Input::file('file');
				$temp_file = time().$file->getClientOriginalName();
				$file->move('excel_upload', $temp_file);
				$data_array = Excel::load(public_path()."/excel_upload/".$temp_file)->get();
				$temp_users = [];
				$headerRow = $data_array->first()->keys();

				$temp_enroll = new TempEnrollment();

				$fname = false;
				$lname = false;
				$nric = false;
				$dob = false;
				$email = false;
				$mobile = false;
				$job = false;
				$credits = false;
				$credit = 0;
				$medical_credits = false;
				$wellness_credits = false;
				$start_date = false;
		        // return $headerRow;

				foreach ($headerRow as $key => $row) {
					if($row == "first_name") {
						$fname = true;
					} else if($row == "last_name") {
						$lname = true;
					} else if($row == "nricfin") {
						$nric = true;
					} else if($row == "date_of_birth") {
						$dob = true;
					} else if($row == "work_email") {
						$email = true;
					} elseif ($row == "mobile") {
						$mobile = true;
					} else if($row == "wellness_credits") {
						$wellness_credits = true;
					} else if($row == "medical_credits") {
						$medical_credits = true;
					} else if($row == "start_date") {
						$start_date = true;
					}
				}

				if(!$fname || !$lname || !$nric || !$dob || !$email || !$mobile || !$start_date) {
					return array(
						'status'	=> FALSE,
						'message' => 'Excel is invalid format. Please download the recommended file for Employee Enrollment.'
					);
				}

				foreach ($data_array as $key => $value) {
					if($value->first_name != null && $value->last_name != null) {
						array_push($temp_users, $value);
					}
				}

				if(sizeof($temp_users) == 0) {
					return array('status' => FALSE, 'message' => 'Excel File data is empty.');
				}

				if($total <= 0) {
					return array(
						'status'	=> FALSE,
						'message'	=> "We realised the current headcount you wish to enroll is over the current vacant member seat/s."
					);
				}

				if(sizeof($temp_users) > $total) {
					return array(
						'status'	=> FALSE,
						'message'	=> "We realised the current headcount you wish to enroll is over the current vacant member seat/s. You need to enroll ".$total." employee/s only."
					);
				} else {
					$customer_active_plan_id = PlanHelper::getCompanyAvailableActivePlanId($result->customer_buy_start_id);
					if(!$customer_active_plan_id) {
						$active_plan = DB::table('customer_active_plan')->where('plan_id', $planned->customer_plan_id)->orderBy('created_at', 'desc')->first();
					} else {
						$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $customer_active_plan_id)->first();
					}
					
					foreach ($temp_users as $key => $user) {

						$check_user = DB::table('user')->where('Email', $user->work_email)->where('Active', 1)->where('UserType', 5)->count();
						$check_temp_user = DB::table('customer_temp_enrollment')
						->where('email', $user->work_email)->where('enrolled_status', 'false')
						->count();

						if(filter_var($user->work_email, FILTER_VALIDATE_EMAIL)) {
							$email_error = false;
							$email_message = '';
						} else {
							$email_error = true;
							$email_message = '*Error email format.';
						}
						// validations
						if($check_user > 0) {
							$email_error = true;
							$email_message = '*Email Already taken.';
						}

						if($check_temp_user > 0) {
							$email_error = true;
							$email_message = '*Email Already taken.';
						}

						if(is_null($user->first_name)) {
							$first_name_error = true;
							$first_name_message = '*First Name is empty';
						} else {
							$first_name_error = false;
							$first_name_message = '';
						}

						if(is_null($user->last_name)) {
							$last_name_error = true;
							$last_name_message = '*Last Name is empty';
						} else {
							$last_name_error = false;
							$last_name_message = '';
						}

						if(is_null($user->date_of_birth)) {
							$dob_error = true;
							$dob_message = '*Date of Birth is empty';
						} else {
							$dob_error = false;
							$dob_message = '';
						}

						if(is_null($user->mobile)) {
							$mobile_error = true;
							$mobile_message = '*Mobile Contact is empty';
						} else {
							$mobile_error = false;
							$mobile_message = '';
						}

						if(is_null($user->nricfin)) {
							$nric_error = true;
							$nric_message = '*NRIC/FIN is empty';
						} else {
							if(strlen($user->nricfin) < 9) {
								$nric_error = true;
								$nric_message = '*NRIC/FIN is must be 8 characters';
							} else {
								$nric_error = false;
								$nric_message = '';
							}
						}

						if(is_null($user->start_date)) {
							// $start_date_error = true;
							// $start_date_message = '*Start Date is empty';
							$start_date_result = true;
							$start_date_error = false;
							$start_date_message = '';
							$user->start_date = $planned->plan_start;
						} else {
							$start_date_result = self::validateStartDate($user->start_date);
							if(!$start_date_result) {
								$start_date_error = true;
								$start_date_message = '*Start Date is not well formatted Date';
							} else {
								$start_date_result = true;
								$start_date_error = false;
								$start_date_message = '';
							}
						}

						if(is_null($user->medical_credits) || !isset($user->medical_credits)) {
							$credit_medical_amount = 0;
							$credits_medical_error = false;
							$credits_medical_message = '';
						} else {
							if(is_numeric($user->medical_credits)) {
								$credits_medical_error = false;
								$credits_medical_message = '';
								$credit_medical_amount = $user->medical_credits;
							} else {
								$credits_medical_error = true;
								$credits_medical_message = 'Credits is not a number.';
								$credit_medical_amount = $user->medical_credits;
							}
						}

						if(is_null($user->wellness_credits) || !isset($user->wellness_credits)) {
							$credit_wellness_amount = 0;
							$credits_wellness_error = false;
							$credits_wellnes_message = '';
						} else {
							if(is_numeric($user->wellness_credits)) {
								$credits_wellness_error = false;
								$credits_wellnes_message = '';
								$credit_wellness_amount = $user->wellness_credits;
							} else {
								$credits_wellness_error = true;
								$credits_wellnes_message = 'Credits is not a number.';
								$credit_wellness_amount = $user->wellness_credits;
							}
						}

						if($email_error || $first_name_error || $last_name_error || $dob_error || $mobile_error || $nric_error || $credits_medical_error || $credits_wellness_error || $start_date_error) {
							$error_status = true;
						} else {
							$error_status = false;
						}

						$error_logs = array(
							'error' 				=> $error_status,
							"email_error"			=> $email_error,
							"email_message"			=> $email_message,
							"first_name_error"		=> $first_name_error,
							"first_name_message"	=> $first_name_message,
							"last_name_error"		=> $last_name_error,
							"last_name_message"		=> $last_name_message,
							"nric_error"			=> $nric_error,
							"nric_message"			=> $nric_message,
							"dob_error"				=> $dob_error,
							"dob_message"			=> $dob_message,
							"mobile_error"			=> $mobile_error,
							"mobile_message"		=> $mobile_message,
							"credits_medical_error"	=> $credits_medical_error,
							"credits_medical_message" => $credits_medical_message,
							"credits_wellness_error" => $credits_wellness_error,
							"credits_wellnes_message" => $credits_wellnes_message,
							"start_date_error"		=> $start_date_error,
							"start_date_message"	=> $start_date_message
						);

						$user->mobile = preg_replace('/\s+/', '', $user->mobile);

						$temp_enrollment_data = array(
							'customer_buy_start_id'			=> $result->customer_buy_start_id,
							'active_plan_id'				=> $active_plan->customer_active_plan_id,
							'first_name'					=> $user->first_name,
							'last_name'						=> $user->last_name,
							'nric'							=> $user->nricfin,
							'dob'							=> $dob_error ? $user->date_of_birth : date('d/m/Y', strtotime($user->date_of_birth)),
							'email'							=> $user->work_email,
							'mobile'						=> $user->mobile,
							'job_title'						=> $user->job_title,
							'credits'						=> $credit_medical_amount,
							'wellness_credits'				=> $credit_wellness_amount,
							'start_date'					=> $start_date_result ? date('d/m/Y', strtotime($user->start_date)) : null,
							'error_logs'					=> serialize($error_logs)
						);

						$insert_temp_user = $temp_enroll->insertTempEnrollment($temp_enrollment_data);
					}
				}

	        // } else {
	        // 	return array('status' => FALSE, 'message' => 'Employee list should be equal or lesser than the active employee count from purchase.');
	        // }

				return array(
					'status'	=> TRUE,
					'message' => 'Success.'
				);
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'Invalid File.'
				);
			}
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Empty File.'
		);
	}

	public function getTempEnrollment( )
	{
		$session = self::checkSession();
		// return json_encode($session);
		$enroll_users = [];

		$added_purchase_status = Session::get('added_purchase_status');

		if($added_purchase_status) {
			$result = Session::get('temp_enrollment_users');
			// return array('result' => $result);

			foreach ($result as $key => $value) {
				$temp = array(
					'enrollee'		=> $value,
					'error_logs' 	=> unserialize($value['error_logs']),
					// 'start_date'	=> date('Y-m-d', strtotime($value['start_date'])),
					'active_plan'	=> null
				);

				array_push($enroll_users, $temp);
			}
		} else {
			// $result = DB::table('customer_temp_enrollment')
			// 					->join('customer_active_plan', 'customer_active_plan.customer_active_plan_id', '=', 'customer_temp_enrollment.active_plan_id')
			// 					->where('customer_temp_enrollment.customer_buy_start_id', $session->customer_buy_start_id)
			// 					->where('customer_temp_enrollment.enrolled_status', "false")
			// 					->get();
			$result = DB::table('customer_temp_enrollment')
			->join('customer_active_plan', 'customer_active_plan.customer_active_plan_id', '=', 'customer_temp_enrollment.active_plan_id')
			->where('customer_temp_enrollment.customer_buy_start_id', $session->customer_buy_start_id)
			->where('customer_temp_enrollment.enrolled_status', "false")
			->get();
			$result = DB::table('customer_temp_enrollment')
			->where('customer_buy_start_id', $session->customer_buy_start_id)
			->where('enrolled_status', "false")
			->get();

			foreach ($result as $key => $value) {
				$temp = array(
					'enrollee'		=> $value,
					'error_logs' 	=> unserialize($value->error_logs),
					// 'start_date'	=> date('d/m/Y', strtotime($value->start_date)),
					'active_plan'	=> DB::table('customer_active_plan')->where('customer_active_plan_id', $value->active_plan_id)->first()
				);

				array_push($enroll_users, $temp);
			}

		}
		return $enroll_users;

	}


	public function createPurchasePlanInvoice($data)
	{
		$invoice = new CorporateInvoice();

		$count = DB::table('customer_active_plan')->count();
		$get_invoice_number = DB::table('corporate_invoice')->count();
		$invoice_number = str_pad($count + $get_invoice_number, 6, "0", STR_PAD_LEFT);

		$first_plan = DB::table('customer_active_plan')->where('plan_id', $data->plan_id)->first();
		$first_invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $first_plan->customer_active_plan_id)->first();
		if($first_invoice) {
			$individual_price = $first_invoice->individual_price;
		} else {
			$individual_price = 99;
		}
		$due_date = date('Y-m-d', strtotime('+5 days', strtotime($data->created_at)));

		$data_invoice = array(
			'customer_active_plan_id'	=> $data->id,
			'invoice_number'			=> 'OMC'.$invoice_number,
			'individual_price'			=> $individual_price,
			'invoice_date'				=> $data->created_at,
			'invoice_due'				=> $due_date,
			'employees'					=> $data->employees,
			'customer_id'				=> $data->customer_start_buy_id,
			'invoice_type'				=> 'invoice'
		);

		$invoice->createCorporateInvoice($data_invoice);

	}

	public function newPaymentAddedPurchaseEmployee( )
	{
		$input = Input::all();
		$result = self::checkSession();

		$temp_enrollees = Session::get('temp_enrollment_users');
		$new_active_plan = Session::get('new_active_plan');
		
		if(!$new_active_plan || !$temp_enrollees) {
			return array('status' => FALSE, 'message' => 'Something went wrong. Please contact Mednefits Team.');
		}

		$new_active_plan['employees'] = sizeOf($temp_enrollees);

		Session::put('new_active_plan', $new_active_plan);
		$final_active_plan = Session::get('new_active_plan');


		$customer_plan_status = new \CustomerPlanStatus( );
		$user_plan_history = new UserPlanHistory();
		$plan_type = new UserPlanType();
		$group_package = new PackagePlanGroup();
		$bundle = new Bundle();
		$user_package = new UserPackage();
		$wallet_class = new Wallet();
		$employee_logs = new WalletHistory();
		$allocation_data = new EmployeeCreditAllocation( );
		$customer_credits = new CustomerCredits();
		$customer_credit_logs = new CustomerCreditLogs( );
		$customer_wellness_credits_logs = new \CustomerWellnessCreditLogs();

		$corporate = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
		$company_details = DB::table('corporate')->where('corporate_id', $corporate->corporate_id)->first();
		// company credits
		// $customer = DB::table('customer_credits')->where('customer_id', $result->customer_buy_start_id)->first();
		// create active plan
		try {
			$result_active_plan = \CorporateActivePlan::create($final_active_plan);
			// create invoice
			self::createPurchasePlanInvoice($result_active_plan);
			$customer_active_plan_id = $result_active_plan->id;
			$plan_id = $result_active_plan->plan_id;
			$customer_id = $result_active_plan->customer_start_buy_id;
			$employees = $result_active_plan->employees;
			$customer_plan_status->addjustCustomerStatus('employees_input', $plan_id , 'increment', $employees);

			foreach($temp_enrollees as $key => $enrollees) {
				$temp_enrollee_data = array(
					'customer_buy_start_id'	=> $enrollees['customer_buy_start_id'],
					'active_plan_id'		=> $customer_active_plan_id,
					'first_name'			=> $enrollees['first_name'],
					'last_name'				=> $enrollees['last_name'],
					'nric'					=> $enrollees['nric'],
					'dob'					=> date('Y-m-d', strtotime($enrollees['dob'])),
					'email'					=> $enrollees['email'],
					'mobile'				=> $enrollees['mobile'],
					'error_logs'			=> $enrollees['error_logs'],
					'credits'				=> $enrollees['credits'],
					'wellness_credits'		=> $enrollees['wellness_credits'],
					'start_date'			=> $enrollees['start_date'],
					'enrolled_status'		=> "true"
				);

				try {
					$temp_enroll = \TempEnrollment::create($temp_enrollee_data);

					if($temp_enroll) {
						$date = str_replace('/', '-', $enrollees['start_date']);
						$start_date = date('Y-m-d', strtotime($date));
						
						// create user
						$password = StringHelper::get_random_password(8);
						$user = new User();

						$user_data = array(
							'Name'			=> $enrollees['first_name'].' '.$enrollees['last_name'],
							'Password'	=> md5($password),
							'Email'			=> $enrollees['email'],
							'PhoneNo'		=> $enrollees['mobile'],
							'PhoneCode'	=> NULL,
							'NRIC'			=> $enrollees['nric'],
							'Job_Title'	=> $enrollees['job_title'],
							'Active'		=> 1
						);

						try {
							$user_id = $user->createUserFromCorporate($user_data);
							$corporate_member = array(
								'corporate_id'	=> $corporate->corporate_id,
								'user_id'				=> $user_id,
								'first_name'		=> $enrollees['first_name'],
								'last_name'			=> $enrollees['last_name'],
								'type'					=> 'member'
							);

							$cm = \CorporateMembers::create($corporate_member);

							$group_package_id = $group_package->getPackagePlanGroupDefault();
							$result_bundle = $bundle->getBundle($group_package_id);

							foreach ($result_bundle as $key => $value) {
								$user_package->createUserPackage($value->care_package_id, $user_id);
							}

							$plan_type_data = array(
								'user_id'			=> $user_id,
								'package_group_id'	=> $group_package_id,
								'duration'			=> '1 year',
								'plan_start'		=> $start_date,
								'active_plan_id'	=> $customer_active_plan_id
							);
							$plan_type->createUserPlanType($plan_type_data);

								// store user plan history
							$user_plan_history_data = array(
								'user_id'		=> $user_id,
								'type'			=> "started",
								'date'			=> $start_date,
								'customer_active_plan_id' => $customer_active_plan_id
							);

							$user_plan_history->createUserPlanHistory($user_plan_history_data);
							$customer = DB::table('customer_credits')->where('customer_id', $customer_id)->first();
							if($enrollees['credits'] > 0) {
								// medical credits
								if($customer->balance >= $enrollees['credits']) {
									$result_customer_active_plan = self::allocateCreditBaseInActivePlan($customer_id, $enrollees['credits'], "medical");

									if($result_customer_active_plan) {
										$new_customer_active_plan_id = $result_customer_active_plan;
									} else {
										$new_customer_active_plan_id = NULL;
									}
									// give credits
									$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
									$update_wallet = $wallet_class->addCredits($user_id, $enrollees['credits']);

									$wallet_history = array(
										'wallet_id'			=> $wallet->wallet_id,
										'credit'			=> $enrollees['credits'],
										'logs'				=> 'added_by_hr',
										'running_balance'	=> $enrollees['credits'],
										'customer_active_plan_id' => $new_customer_active_plan_id
									);

									$employee_logs->createWalletHistory($wallet_history);
									

									$customer_credits_result = $customer_credits->deductCustomerCredits($customer->customer_credits_id, $enrollees['credits']);
									$customer_credits_left = DB::table('customer_credits')->where('customer_credits_id', $customer->customer_credits_id)->first();
									
									if($customer_credits_result) {
										$company_deduct_logs = array(
											'customer_credits_id'	=> $customer->customer_credits_id,
											'credit'				=> $enrollees['credits'],
											'logs'					=> 'added_employee_credits',
											'user_id'				=> $user_id,
											'running_balance'		=> $customer->balance - $enrollees['credits'],
											'customer_active_plan_id' => $new_customer_active_plan_id
										);

										$customer_credit_logs->createCustomerCreditLogs($company_deduct_logs);
									}
								}
							}


							if($enrollees['wellness_credits'] > 0) {
								// wellness credits
								if($customer->wellness_credits >= $enrollees['wellness_credits']) {
									$result_customer_active_plan = self::allocateCreditBaseInActivePlan($customer_id, $enrollees['wellness_credits'], "wellness");

									if($result_customer_active_plan) {
										$new_customer_active_plan_id = $result_customer_active_plan;
									} else {
										$new_customer_active_plan_id = NULL;
									}
									// give credits
									$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
									$update_wallet = $wallet_class->addWellnessCredits($user_id, $enrollees['wellness_credits']);

									$wallet_history = array(
										'wallet_id'		=> $wallet->wallet_id,
										'credit'		=> $enrollees['wellness_credits'],
										'logs'			=> 'added_by_hr',
										'running_balance'	=> $enrollees['wellness_credits'],
										'customer_active_plan_id' => $new_customer_active_plan_id
									);

									\WellnessWalletHistory::create($wallet_history);
									$customer_credits_result = $customer_credits->deductCustomerWellnessCredits($customer->customer_credits_id, $enrollees['wellness_credits']);
									
									if($customer_credits_result) {
										$company_deduct_logs = array(
											'customer_credits_id'	=> $customer->customer_credits_id,
											'credit'				=> $enrollees['wellness_credits'],
											'logs'					=> 'added_employee_credits',
											'user_id'				=> $user_id,
											'running_balance'		=> $customer->wellness_credits - $enrollees['wellness_credits'],
											'customer_active_plan_id' => $new_customer_active_plan_id
										);
										$customer_wellness_credits_logs->createCustomerWellnessCreditLogs($company_deduct_logs);
									}
								}
							}

							$customer_plan_status->addjustCustomerStatus('enrolled_employees', $plan_id, 'increment', 1);

							$email_data['company']   = ucwords($company_details->company_name);
							$email_data['emailName'] = $enrollees['first_name'].' '.$enrollees['last_name'];
							$email_data['emailTo']   = $enrollees['email'];
							$email_data['email'] = $enrollees['email'];
							$email_data['emailPage'] = 'email-templates.latest-templates.mednefits-welcome-member-enrolled';
							$email_data['start_date'] = date('d F Y', strtotime($start_date));
							$email_data['name'] = $enrollees['first_name'].' '.$enrollees['last_name'];
							$email_data['emailSubject'] = "WELCOME TO MEDNEFITS CARE";
							$email_data['pw'] = $password;
							// $email_data['url'] = url('/');
							$api = "https://api.medicloud.sg/employees/welcome_email";
							\httpLibrary::postHttp($api, $email_data, []);
							// EmailHelper::sendEmail($email_data);
							// $email_data['emailTo']   = 'info@medicloud.sg';
							// EmailHelper::sendEmail($email_data);
						} catch(Exception $e) {
							return array('status' => FALSE, 'message' => $e->getMessage());
						}
					}
				} catch(Exception $e) {
					return array('status' => FALSE, 'message' => $e->getMessage());
				}
			}
			self::flushSessionAddedPurchase();
			return array('status' => TRUE, 'message' => 'Success.');
		} catch(Exception $e) {
			return array('status' => FALSE, 'message' => 'Error.', 'error' => $e->getMessage());
		}
	}

	public function flushSessionAddedPurchase( )
	{
		Session::forget('new_active_plan');
		Session::forget('temp_enrollment_users');
		Session::forget('added_purchase_status');
	}

	public function removeEnrollee($id)
	{
		$added_purchase_status = Session::get('added_purchase_status');

		if($added_purchase_status) {
			$temp_enrollees = Session::get('temp_enrollment_users');

			foreach($temp_enrollees as $key => $value) {
				if($value['temp_enrollment_id'] === $id) {
					unset($temp_enrollees[$key]);
				}
			}

			// array_push($temp_enrollees, $data);

			$temp_enrollees_final = Session::put('temp_enrollment_users', $temp_enrollees);
			$result = Session::get('temp_enrollment_users');
			return array(
				'status'	=> TRUE,
				'enrolless' => $result,
				'message'	=> 'Success.'
			);
		} else {
			$result = DB::table('customer_temp_enrollment')->where('temp_enrollment_id', $id)->delete();
			if($result) {
				return array(
					'status'	=> TRUE,
					'message'	=> 'Success.'
				);
			}
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Failed.',
			'data'		=> $result
		);
	}

	public function checkEnrollUserCount($customer_plan_id, $customer_id)
	{

		// $account_link = DB::table('')
		$active_plans = DB::table('customer_active_plan')->where('plan_id', $customer_plan_id)->get();
		$format = [];
		foreach ($active_plans as $key => $active) {
			$enrolled = DB::table('user_plan_history')->where('customer_active_plan_id', $active->customer_active_plan_id)->where('type', 'started')->count();
			$expired = DB::table('user_plan_history')->where('customer_active_plan_id', $active->customer_active_plan_id)->whereIn('type', ['expired', 'deleted_expired'])->count();

			if($active->employees > $enrolled) {
				$temp = array(
					'customer_active_plan_id' 	=> $active->customer_active_plan_id,
					'enrolled'					=> $enrolled,
					'expired'					=> $expired,
					'total_employees'			=> $active->employees,
					'to_enroll'					=> $active->employees - $enrolled
				);

				array_push($format, $temp);
			}

		}

		return $format;
	}

	public function validateStartDate($date)
	{
		return (bool)strtotime($date);
	}

	public function insertFromWebInput( )
	{
		$result = self::checkSession();
		$customer_id = $result->customer_buy_start_id;
		$users = Input::all();
		
		$planned = DB::table('customer_plan')->where('customer_buy_start_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
		$plan_status = DB::table('customer_plan_status')->where('customer_plan_id', $planned->customer_plan_id)->orderBy('created_at', 'desc')->first();

		$total = $plan_status->employees_input - $plan_status->enrolled_employees;
		if($total <= 0) {
			return array(
				'status'	=> FALSE,
				'message'	=> "We realised the current headcount you wish to enroll is over the current vacant member seat/s."
			);
		} else {

			if(sizeof($users['users']) > $total) {
				return array(
					'status'	=> FALSE,
					'message'	=> "We realised the current headcount you wish to enroll is over the current vacant member seat/s. You need to enroll ".$total." employee/s only"
				);
			}

			$customer_active_plan_id = PlanHelper::getCompanyAvailableActivePlanId($result->customer_buy_start_id);
			if(!$customer_active_plan_id) {
				$active_plan = DB::table('customer_active_plan')->where('plan_id', $planned->customer_plan_id)->orderBy('created_at', 'desc')->first();
			} else {
				$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $customer_active_plan_id)->first();
			}

			$temp_enroll = new TempEnrollment();
			// loop
			foreach ($users['users'] as $key => $user) {
				$credit = 0;
				$check_user = DB::table('user')->where('Email', $user['work_email'])->where('Active', 1)->where('UserType', 5)->count();
				$check_temp_user = DB::table('customer_temp_enrollment')
				->where('email', $user['work_email'])
				->where('enrolled_status', 'false')
				->count();

				if(filter_var($user['work_email'], FILTER_VALIDATE_EMAIL)) {
					$email_error = false;
					$email_message = '';
				} else {
					$email_error = true;
					$email_message = '*Error email format.';
				}
				// validations
				if($check_user > 0) {
					$email_error = true;
					$email_message = '*Email Already taken.';
				}

				if($check_temp_user > 0) {
					$email_error = true;
					$email_message = '*Email Already taken.';
				}

				if(is_null($user['first_name'])) {
					$first_name_error = true;
					$first_name_message = '*First Name is empty';
				} else {
					$first_name_error = false;
					$first_name_message = '';
				}

				if(is_null($user['last_name'])) {
					$last_name_error = true;
					$last_name_message = '*Last Name is empty';
				} else {
					$last_name_error = false;
					$last_name_message = '';
				}

				if(is_null($user['date_of_birth'])) {
					$dob_error = true;
					$dob_message = '*Date of Birth is empty';
				} else {
					$dob_error = false;
					$dob_message = '';
				}

				if(is_null($user['mobile'])) {
					$mobile_error = true;
					$mobile_message = '*Mobile Contact is empty';
				} else {
					$mobile_error = false;
					$mobile_message = '';
				}

				if(is_null($user['job_title'])) {
					$job_title_error = true;
					$job_title_message = '*Job is empty';
				} else {
					$job_title_error = false;
					$job_title_message = '';
				}

				if(is_null($user['nric'])) {
					$nric_error = true;
					$nric_message = '*NRIC/FIN is empty';
				} else {
					if(strlen($user['nric']) < 9) {
						$nric_error = true;
						$nric_message = '*NRIC/FIN is must be 8 characters';
					} else {
						$nric_error = false;
						$nric_message = '';
					}
				}

				if(is_null($user['plan_start'])) {
					$start_date_error = true;
					$start_date_message = '*Start Date is empty';
					$start_date_result = false;
				} else {
					// $start_date_result = self::validateStartDate($user['plan_start']);
					// if(!$start_date_result) {
					// 	$start_date_error = true;
					// 	$start_date_message = '*Start Date is not well formatted Date';
					// } else {
					$start_date_error = false;
					$start_date_message = '';
					// }
				}


				if(!isset($user['medical_credits']) || is_null($user['medical_credits'])) {
					$credit_medical_amount = 0;
					$credits_medical_error = false;
					$credits_medical_message = '';
				} else {
					if(is_numeric($user['medical_credits'])) {
						$credits_medical_error = false;
						$credits_medical_message = '';
						$credit_medical_amount = $user['medical_credits'];
					} else {
						$credits_medical_error = true;
						$credits_medical_message = 'Credits is not a number.';
						$credit_medical_amount = $user['medical_credits'];
					}
				}

				if(!isset($user['wellness_credits']) || is_null($user['wellness_credits'])) {
					$credit_wellness_amount = 0;
					$credits_wellness_error = false;
					$credits_wellnes_message = '';
				} else {
					if(is_numeric($user['wellness_credits'])) {
						$credits_wellness_error = false;
						$credits_wellnes_message = '';
						$credit_wellness_amount = $user['wellness_credits'];
					} else {
						$credits_wellness_error = true;
						$credits_wellnes_message = 'Credits is not a number.';
						$credit_wellness_amount = $user['wellness_credits'];
					}
				}

				if($email_error || $first_name_error || $last_name_error | $dob_error || $mobile_error || $job_title_error || $nric_error || $credits_medical_error || $credits_wellness_error || $start_date_error) {
					$error_status = true;
				} else {
					$error_status = false;
				}

				$error_logs = array(
					'error' 				=> $error_status,
					"email_error"			=> $email_error,
					"email_message"			=> $email_message,
					"first_name_error"		=> $first_name_error,
					"first_name_message"	=> $first_name_message,
					"last_name_error"		=> $last_name_error,
					"last_name_message"		=> $last_name_message,
					"nric_error"			=> $nric_error,
					"nric_message"			=> $nric_message,
					"dob_error"				=> $dob_error,
					"dob_message"			=> $dob_message,
					"mobile_error"			=> $mobile_error,
					"mobile_message"		=> $mobile_message,
					"job_title_error"		=> $job_title_error,
					"job_title_message"		=> $job_title_message,
					"credits_medical_error"	=> $credits_medical_error,
					"credits_medical_message" => $credits_medical_message,
					"credits_wellness_error" => $credits_wellness_error,
					"credits_wellnes_message" => $credits_wellnes_message,
					"start_date_error"		=> $start_date_error,
					"start_date_message"	=> $start_date_message
				);

				$temp_enrollment_data = array(
					'customer_buy_start_id'	=> $customer_id,
					'active_plan_id'		=> $active_plan->customer_active_plan_id,
					'first_name'			=> $user['first_name'],
					'last_name'				=> $user['last_name'],
					'nric'					=> $user['nric'],
					'dob'					=> $user['date_of_birth'],
					'email'					=> $user['work_email'],
					'mobile'				=> $user['mobile'],
					'job_title'				=> $user['job_title'],
					'credits'				=> $credit_medical_amount,
					'wellness_credits'		=> $credit_wellness_amount,
					'start_date'			=> $user['plan_start'],
					'error_logs'			=> serialize($error_logs)
				);
				try {
					$temp_enroll->insertTempEnrollment($temp_enrollment_data);
				} catch(Exception $e) {
					$email = [];
					$email['end_point'] = url('insert/enrollee_web_input', $parameter = array(), $secure = null);
					$email['logs'] = 'Save Temp Enrollment - '.$e->getMessage();
					$email['emailSubject'] = 'Error log.';
					EmailHelper::sendErrorLogs($email);
					return array('status' => FALSE, 'message' => 'Failed to create enrollment employee. Please contact Mednefits team.');
				}
			}


		}

		return array('status' => TRUE);
	}

	public function checkAttributes($data)
	{
		$check_user = DB::table('user')->where('Email', $data['work_email'])->where('Active', 1)->where('UserType', 5)->count();
		$check_temp_user = DB::table('customer_temp_enrollment')
		->where('email', $data['work_email'])
		->where('enrolled_status', 'false')
		->where('temp_enrollment_id', '!=', $data['temp_enrollment_id'])
		->count();
		if(filter_var($data['work_email'], FILTER_VALIDATE_EMAIL)) {
			$email_error = false;
			$email_message = '';
		} else {
			$email_error = true;
			$email_message = '*Error email format.';
		}
			// validations
		if($check_user > 0) {
			$email_error = true;
			$email_message = '*Email Already taken.';
		}

		if($check_temp_user > 0) {
			$email_error = true;
			$email_message = '*Email Already taken.';
		}

		if(is_null($data['first_name'])) {
			$first_name_error = true;
			$first_name_message = '*First Name is empty';
		} else {
			$first_name_error = false;
			$first_name_message = '';
		}

		if(is_null($data['last_name'])) {
			$last_name_error = true;
			$last_name_message = '*Last Name is empty';
		} else {
			$last_name_error = false;
			$last_name_message = '';
		}

		if(is_null($data['date_of_birth'])) {
			$dob_error = true;
			$dob_message = '*Date of Birth is empty';
		} else {
			$dob_error = false;
			$dob_message = '';
		}

		if(is_null($data['mobile'])) {
			$mobile_error = true;
			$mobile_message = '*Mobile Contact is empty';
		} else {
			$mobile_error = false;
			$mobile_message = '';
		}

		if(is_null($data['nric'])) {
			$nric_error = true;
			$nric_message = '*NRIC/FIN is empty';
		} else {
			if(strlen($data['nric']) < 9) {
				$nric_error = true;
				$nric_message = '*NRIC/FIN is must be 8 characters';
			} else {
				$nric_error = false;
				$nric_message = '';
			}
		}

		if(is_null($data['credits'])) {
			$credits = 0;
			$credits_medical_error = false;
			$credits_medical_message = '';
		} else {
			if(is_numeric($data['credits'])) {
				$credits_medical_error = false;
				$credits_medical_message = '';
			} else {
				$credits_error = true;
				$credits_medical_message = 'Credits is not a number.';
			}
		}

		if(is_null($data['wellness_credits'])) {
			$credit_wellness_amount = 0;
			$credits_wellness_error = false;
			$credits_wellnes_message = '';
		} else {
			if(is_numeric($data['wellness_credits'])) {
				$credits_wellness_error = false;
				$credits_wellnes_message = '';
			} else {
				$credits_wellness_error = true;
				$credits_wellnes_message = 'Credits is not a number.';
			}
		}

		if(is_null($data['start_date'])) {
			$start_date_error = true;
			$start_date_message = '*Start Date is empty';
			$start_date_result = false;
		} else {
			$start_date_result = self::validateStartDate($data['start_date']);
			if(!$start_date_result) {
				$start_date_error = true;
				$start_date_message = '*Start Date is not well formatted Date';
			} else {
				$start_date_error = false;
				$start_date_message = '';
			}
		}

		if($email_error || $first_name_error || $last_name_error || $dob_error || $mobile_error || $nric_error || $credits_medical_error || $credits_wellness_error || $start_date_error) {
			$error_status = true;
		} else {
			$error_status = false;
		}

		$error_logs = array(
			"error" 				=> $error_status,
			"email_error"			=> $email_error,
			"email_message"			=> $email_message,
			"first_name_error"		=> $first_name_error,
			"first_name_message"	=> $first_name_message,
			"last_name_error"		=> $last_name_error,
			"last_name_message"		=> $last_name_message,
			"nric_error"			=> $nric_error,
			"nric_message"			=> $nric_message,
			"dob_error"				=> $dob_error,
			"dob_message"			=> $dob_message,
			"mobile_error"			=> $mobile_error,
			"mobile_message"		=> $mobile_message,
			"credits_medical_error"	=> $credits_medical_error,
			"credits_medical_message" => $credits_medical_message,
			"credits_wellness_error" => $credits_wellness_error,
			"credits_wellnes_message" => $credits_wellnes_message,
			"start_date_error"		=> $start_date_error,
			"start_date_message"	=> $start_date_message
		);

		return $error_logs;
	}

	public function updateSessionAttributeTempEnrollees($data)
	{
		$temp_enrollees = Session::get('temp_enrollment_users');

		foreach($temp_enrollees as $key => $value) {
			if($value['temp_enrollment_id'] === $data['temp_enrollment_id']) {
				unset($temp_enrollees[$key]);
			}
		}

		array_push($temp_enrollees, $data);

		$temp_enrollees_final = Session::put('temp_enrollment_users', $temp_enrollees);
		$result = Session::get('temp_enrollment_users');
		return $result;
	}

	public function updateEnrolleeDetails( )
	{
		// $result = self::checkSession();
		$input = Input::all();
		$temp_enroll = new TempEnrollment();
		$input['date_of_birth'] = $input['dob'];
		$input['work_email'] = $input['email'];
		// $active_plan = DB::table('customer_active_plan')->where('customer_start_buy_id', $input['customer_id'])->where('status', "true")->first();

		$added_purchase_status = Session::get('added_purchase_status');
		try {
			if($added_purchase_status) {
				$error_logs = self::checkSessionAttribute($input);
				$data = array(
					'temp_enrollment_id'		=> $input['temp_enrollment_id'],
					'customer_buy_start_id'		=> $input['customer_id'],
					'first_name'				=> $input['first_name'],
					'last_name'					=> $input['last_name'],
					'nric'						=> $input['nric'],
					'dob'						=> $input['dob'],
					'email'						=> $input['email'],
					'mobile'					=> $input['mobile'],
					'job_title'					=> $input['job_title'],
					'credits'					=> $input['credits'],
					'wellness_credits'			=> $input['wellness_credits'],
					'start_date'				=> date('d/m/Y', strtotime($input['start_date'])),
					'error_logs'				=> serialize($error_logs)
				);
				$result = self::updateSessionAttributeTempEnrollees($data);
			} else {
				$error_logs = self::checkAttributes($input);
				$data = array(
					'temp_enrollment_id'		=> $input['temp_enrollment_id'],
					'customer_buy_start_id'		=> $input['customer_id'],
					'first_name'				=> $input['first_name'],
					'last_name'					=> $input['last_name'],
					'nric'						=> $input['nric'],
					'dob'						=> $input['dob'],
					'email'						=> $input['email'],
					'mobile'					=> $input['mobile'],
					'job_title'					=> $input['job_title'],
					'credits'					=> $input['credits'],
					'wellness_credits'			=> $input['wellness_credits'],
					'start_date'				=> date('d/m/Y', strtotime($input['start_date'])),
					'error_logs'				=> serialize($error_logs)
				);
				$result = $temp_enroll->updateEnrollee($data);

				if($result) {
					return array(
						'status'	=> TRUE,
						'message'	=> 'Success.'
					);
				} else {
					return array(
						'status'	=> FALSE,
						'message'	=> 'Failed.',
						'reason'	=> $result
					);
				}

			}
		} catch(Exception $e) {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Failed.',
				'reason'	=> $e->getMessage()
			);
		}
	}

	public function checkSessionAttribute($data)
	{
		$check_user = DB::table('user')->where('Email', $data['work_email'])->where('Active', 1)->where('UserType', 5)->count();
			// $check_temp_user = DB::table('customer_temp_enrollment')
			// 								->where('email', $data['work_email'])
			// 								->where('enrolled_status', 'false')
			// 								->where('temp_enrollment_id', '!=', $data['temp_enrollment_id'])
			// 								->count();
		if(filter_var($data['work_email'], FILTER_VALIDATE_EMAIL)) {
			$email_error = false;
			$email_message = '';
		} else {
			$email_error = true;
			$email_message = '*Error email format.';
		}
			// validations
		if($check_user > 0) {
			$email_error = true;
			$email_message = '*Email Already taken.';
		}

			// if($check_temp_user > 0) {
			// 	$email_error = true;
			// 	$email_message = '*Email Already taken.';
			// }

		if(is_null($data['first_name'])) {
			$first_name_error = true;
			$first_name_message = '*First Name is empty';
		} else {
			$first_name_error = false;
			$first_name_message = '';
		}

		if(is_null($data['last_name'])) {
			$last_name_error = true;
			$last_name_message = '*Last Name is empty';
		} else {
			$last_name_error = false;
			$last_name_message = '';
		}

		if(is_null($data['date_of_birth'])) {
			$dob_error = true;
			$dob_message = '*Date of Birth is empty';
		} else {
			$dob_error = false;
			$dob_message = '';
		}

		if(is_null($data['mobile'])) {
			$mobile_error = true;
			$mobile_message = '*Mobile Contact is empty';
		} else {
			$mobile_error = false;
			$mobile_message = '';
		}

		if(is_null($data['nric'])) {
			$nric_error = true;
			$nric_message = '*NRIC/FIN is empty';
		} else {
			if(strlen($data['nric']) < 9) {
				$nric_error = true;
				$nric_message = '*NRIC/FIN is must be 8 characters';
			} else {
				$nric_error = false;
				$nric_message = '';
			}
		}

		if(is_null($data['credits'])) {
			$credits = 0;
			$credits_medical_error = false;
			$credits_medical_message = '';
		} else {
			if(is_numeric($data['credits'])) {
				$credits_medical_error = false;
				$credits_medical_message = '';
			} else {
				$credits_error = true;
				$credits_medical_message = 'Credits is not a number.';
			}
		}

		if(is_null($data['wellness_credits'])) {
			$credit_wellness_amount = 0;
			$credits_wellness_error = false;
			$credits_wellnes_message = '';
		} else {
			if(is_numeric($data['wellness_credits'])) {
				$credits_wellness_error = false;
				$credits_wellnes_message = '';
			} else {
				$credits_wellness_error = true;
				$credits_wellnes_message = 'Credits is not a number.';
			}
		}

		if(is_null($data['start_date'])) {
			$start_date_error = true;
			$start_date_message = '*Start Date is empty';
			$start_date_result = false;
		} else {
			$start_date_result = self::validateStartDate($data['start_date']);
			if(!$start_date_result) {
				$start_date_error = true;
				$start_date_message = '*Start Date is not well formatted Date';
			} else {
				$start_date_error = false;
				$start_date_message = '';
			}
		}

		if($email_error || $first_name_error || $last_name_error || $dob_error || $mobile_error || $nric_error || $credits_medical_error || $credits_wellness_error || $start_date_error) {
			$error_status = true;
		} else {
			$error_status = false;
		}

		$error_logs = array(
			"error" 				=> $error_status,
			"email_error"			=> $email_error,
			"email_message"			=> $email_message,
			"first_name_error"		=> $first_name_error,
			"first_name_message"	=> $first_name_message,
			"last_name_error"		=> $last_name_error,
			"last_name_message"		=> $last_name_message,
			"nric_error"			=> $nric_error,
			"nric_message"			=> $nric_message,
			"dob_error"				=> $dob_error,
			"dob_message"			=> $dob_message,
			"mobile_error"			=> $mobile_error,
			"mobile_message"		=> $mobile_message,
			"credits_medical_error"	=> $credits_medical_error,
			"credits_medical_message" => $credits_medical_message,
			"credits_wellness_error" => $credits_wellness_error,
			"credits_wellnes_message" => $credits_wellnes_message,
			"start_date_error"		=> $start_date_error,
			"start_date_message"	=> $start_date_message
		);

		return $error_logs;
	}

	public function finishEnroll( )
	{
		$input = Input::all();
		$result = self::checkSession();
		$customer_id = $result->customer_buy_start_id;
		
		$planned = DB::table('customer_plan')->where('customer_buy_start_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
		$plan_status = DB::table('customer_plan_status')->where('customer_plan_id', $planned->customer_plan_id)->orderBy('created_at', 'desc')->first();

		$total = $plan_status->employees_input - $plan_status->enrolled_employees;
		// return $total;
		if($total <= 0) {
			return array(
				'status'	=> FALSE,
				'message'	=> "We realised the current headcount you wish to enroll is over the current vacant member seat/s."
			);
		}
		// return "yeah";
		$data_enrollee = DB::table('customer_temp_enrollment')
		->where('temp_enrollment_id', $input['temp_enrollment_id'])
		->where('enrolled_status', '=', 'false')
		->first();
		
		if(empty($data_enrollee)) {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Enrollee does not exist.'
			);
		}

		$user = new User();

		try {

			$customer_active_plan_id = PlanHelper::getCompanyAvailableActivePlanId($result->customer_buy_start_id);
			if(!$customer_active_plan_id) {
				$active_plan = DB::table('customer_active_plan')->where('customer_start_buy_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
			} else {
				$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $customer_active_plan_id)->first();
				
			}

			// $active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $data_enrollee->active_plan_id)->first();
			$corporate = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

			if($data_enrollee->start_date != NULL) {
				$temp_start_date = \DateTime::createFromFormat('d/m/Y', $data_enrollee->start_date);
				$start_date = $temp_start_date->format('Y-m-d');
			} else {
				$start_date = date('Y-m-d', strtotime($active_plan->plan_start));
			}

			$password = StringHelper::get_random_password(8);

			$data = array(
				'Name'			=> $data_enrollee->first_name.' '.$data_enrollee->last_name,
				'Password'	=> md5($password),
				'Email'			=> $data_enrollee->email,
				'PhoneNo'		=> $data_enrollee->mobile,
				'PhoneCode'	=> "+".$data_enrollee->mobile_area_code,
				'NRIC'			=> $data_enrollee->nric,
				'Job_Title'	=> $data_enrollee->job_title,
				'DOB'			=> date('Y-m-d', strtotime($data_enrollee->dob)),
				'Active'		=> 1,
				'account_already_update' => 1
			);

			$user_id = $user->createUserFromCorporate($data);

			$corporate_member = array(
				'corporate_id'	=> $corporate->corporate_id,
				'user_id'		=> $user_id,
				'first_name'	=> $data_enrollee->first_name,
				'last_name'		=> $data_enrollee->last_name,
				'type'			=> 'member'
			);
			DB::table('corporate_members')->insert($corporate_member);
			$plan_type = new UserPlanType();
			// $check_plan_type = $plan_type->checkUserPlanType($user_id);

			// if($check_plan_type == 0) {
			$group_package = new PackagePlanGroup();
			$bundle = new Bundle();
			$user_package = new UserPackage();

			$group_package_id = $group_package->getPackagePlanGroupDefault();
			$result_bundle = $bundle->getBundle($group_package_id);

			foreach ($result_bundle as $key => $value) {
				$user_package->createUserPackage($value->care_package_id, $user_id);
			}

			$plan_type_data = array(
				'user_id'			=> $user_id,
				'package_group_id'	=> $group_package_id,
				'duration'			=> '1 year',
				'plan_start'		=> $start_date,
				'active_plan_id'	=> $active_plan->customer_active_plan_id
			);
			$plan_type->createUserPlanType($plan_type_data);
			// }

			// store user plan history
			$user_plan_history = new UserPlanHistory();
			$user_plan_history_data = array(
				'user_id'		=> $user_id,
				'type'			=> "started",
				'date'			=> $start_date,
				'customer_active_plan_id' => $active_plan->customer_active_plan_id
			);

			$user_plan_history->createUserPlanHistory($user_plan_history_data);

			// check company credits
			$customer = DB::table('customer_credits')->where('customer_id', $result->customer_buy_start_id)->first();

			if($data_enrollee->credits > 0) {
				$customer_credit_logs = new CustomerCreditLogs( );
				$result_customer_active_plan = self::allocateCreditBaseInActivePlan($result->customer_buy_start_id, $data_enrollee->credits, "medical");
				if($result_customer_active_plan) {
					$customer_active_plan_id = $result_customer_active_plan;
				} else {
					$customer_active_plan_id = NULL;
				}

				// if($data_enrollee->credits > $customer->balance) {
					$customer_credits_result = DB::table('customer_credits')->where('customer_id', $result->customer_buy_start_id)->increment("balance", $credits);
					if($customer_credits_result) {
						// credit log for wellness
						$customer_credits_logs = array(
							'customer_credits_id'	=> $customer->customer_credits_id,
							'credit'				=> $credits,
							'logs'					=> 'admin_added_credits',
							'running_balance'		=> $customer->balance + $credits,
							'customer_active_plan_id' => $customer_active_plan_id,
							'currency_type'	=> $customer->currency_type
						);

						$customer_credit_logs->createCustomerCreditLogs($customer_credits_logs);
					}
					$customer = DB::table('customer_credits')->where('customer_id', $result->customer_buy_start_id)->first();
				// }
				// medical credits
				// give credits
				$wallet_class = new Wallet();
				$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
				$update_wallet = $wallet_class->addCredits($user_id, $data_enrollee->credits);

				$employee_logs = new WalletHistory();

				$wallet_history = array(
					'wallet_id'		=> $wallet->wallet_id,
					'credit'			=> $data_enrollee->credits,
					'logs'				=> 'added_by_hr',
					'running_balance'	=> $data_enrollee->credits,
					'customer_active_plan_id' => $customer_active_plan_id
				);

				$employee_logs->createWalletHistory($wallet_history);
				$customer_credits = new CustomerCredits();

				$customer_credits_result = $customer_credits->deductCustomerCredits($customer->customer_credits_id, $data_enrollee->credits);
				
				if($customer_credits_result) {
					$company_deduct_logs = array(
						'customer_credits_id'	=> $customer->customer_credits_id,
						'credit'				=> $data_enrollee->credits,
						'logs'					=> 'added_employee_credits',
						'user_id'				=> $user_id,
						'running_balance'		=> $customer->balance - $data_enrollee->credits,
						'customer_active_plan_id' => $customer_active_plan_id
					);

					$customer_credit_logs->createCustomerCreditLogs($company_deduct_logs);
				}
			}


			if($data_enrollee->wellness_credits > 0) {
				// wellness credits
				// if($customer->wellness_credits >= $data_enrollee->wellness_credits) {
					$result_customer_active_plan = self::allocateCreditBaseInActivePlan($result->customer_buy_start_id, $data_enrollee->wellness_credits, "wellness");

					if($result_customer_active_plan) {
						$customer_active_plan_id = $result_customer_active_plan;
					} else {
						$customer_active_plan_id = NULL;
					}
					// give credits
					$wallet_class = new Wallet();
					$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
					$update_wallet = $wallet_class->addWellnessCredits($user_id, $data_enrollee->wellness_credits);

					$wallet_history = array(
						'wallet_id'		=> $wallet->wallet_id,
						'credit'		=> $data_enrollee->wellness_credits,
						'logs'			=> 'added_by_hr',
						'running_balance'	=> $data_enrollee->credits,
						'customer_active_plan_id' => $customer_active_plan_id
					);

					\WellnessWalletHistory::create($wallet_history);
					$customer_credits = new CustomerCredits();
					$customer_credits_result = $customer_credits->deductCustomerWellnessCredits($customer->customer_credits_id, $data_enrollee->wellness_credits);
					
					if($customer_credits_result) {
						$company_deduct_logs = array(
							'customer_credits_id'	=> $customer->customer_credits_id,
							'credit'				=> $data_enrollee->wellness_credits,
							'logs'					=> 'added_employee_credits',
							'user_id'				=> $user_id,
							'running_balance'		=> $customer->wellness_credits - $data_enrollee->wellness_credits,
							'customer_active_plan_id' => $customer_active_plan_id
						);
						$customer_credits_logs = new CustomerWellnessCreditLogs();
						$customer_credits_logs->createCustomerWellnessCreditLogs($company_deduct_logs);
					}
				// }
			}


			// check added purchase and update if not updated yet
			// self::updateAddedPurchasePlan($active_plan->customer_active_plan_id);
			$Customer_PlanStatus = new CustomerPlanStatus( );
			$Customer_PlanStatus->addjustCustomerStatus('enrolled_employees', $active_plan->plan_id, 'increment', 1);
			DB::table('customer_temp_enrollment')
			->where('temp_enrollment_id', $input['temp_enrollment_id'])
			->update(['enrolled_status' => "true", 'active_plan_id' => $active_plan->customer_active_plan_id]);
			$email_data = [];
			$company = DB::table('corporate')->where('corporate_id', $corporate->corporate_id)->first();
			// check if there is a mobile phone
			if($data_enrollee->mobile) {
				$compose = [];
				$compose['name'] = $data_enrollee->first_name.' '.$data_enrollee->last_name;
				$compose['company'] = $company->company_name;
				$compose['plan_start'] = date('F d, Y', strtotime($start_date));
				$compose['email'] = null;
				$compose['nric'] = $data_enrollee->nric;
				$compose['password'] = $password;
				$compose['phone'] = $data_enrollee->mobile;

				$compose['message'] = SmsHelper::formatWelcomeEmployeeMessage($compose);
				$result_sms = SmsHelper::sendSms($compose);

				if($result_sms['status'] == true) {
					return array('status' => TRUE, 'message' => 'Employee Account Created.');
				}
			}

			$email_data = [];
			$email_data['company']   = ucwords($company->company_name);
			$email_data['emailName'] = $data_enrollee->first_name.' '.$data_enrollee->last_name;
			$email_data['emailTo']   = $data_enrollee->email;
			$email_data['email'] = $data_enrollee->mobile ? $data_enrollee->mobile : $data_enrollee->email;
			// $email_data['email'] = 'allan.alzula.work@gmail.com';
			$email_data['emailPage'] = 'email-templates.latest-templates.mednefits-welcome-member-enrolled';
			$email_data['start_date'] = date('d F Y', strtotime($start_date));
			$email_data['name'] = $data_enrollee->first_name.' '.$data_enrollee->last_name;
			$email_data['emailSubject'] = "FOR MEMBER: WELCOME TO MEDNEFITS CARE";
			$email_data['pw'] = $password;
			$email_data['plan'] = $active_plan;
		    // $email_data['url'] = url('/');
		    // $api = "https://api.medicloud.sg/employees/welcome_email";
      //   	\httpLibrary::postHttp($api, $email_data, []);
			EmailHelper::sendEmail($email_data);

			return array('status' => TRUE, 'message' => 'Employee Account Created.');

		} catch(Exception $e) {
			return $e;
			return array('status' => FALSE, 'message' => 'Error Occured while creating new employee.', 'error' => $e);
		}

	}

	public function updateAddedPurchasePlan($id)
	{
		$check = DB::table('customer_added_active_plan_purchase')
		->where('customer_active_plan', $id)
		->where('status', 0)
		->first();

		if($check) {
			// update status
			$added_class = new AddedActivePlanPurchase( );
			$added_class->updateActivePlanPurchase($id);
		}
	}

	public function employeeLists( )
	{
		$input = Input::all();
		$result = self::checkSession();
		$account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
		$final_user = [];
		$paginate = [];

		$types = !empty($input['status']) && sizeof($input['status']) > 0 ? $input['status'] : null;
		$per_page = !empty($input['limit']) ? $input['limit'] : 5;
		$search = !empty($input['search']) ? $input['search'] : null;
		if($types)	{
			foreach($types as $key => $type) {
				if(!in_array($type, ['pending', 'activated', 'active', 'removed'])) {
					return ['status' => false, 'message' => 'status should be pending, activated, active or removed'];
				}
			}
			
			$ids = [];
			foreach($types as $key => $type) {
				if($type == "pending") {
					$users = DB::table('user')
							->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
							->where('user.member_activated', 0)
							->where('corporate_members.corporate_id', $account_link->corporate_id)
							->lists('user.UserID');
					if(sizeof($users) > 0) {
						foreach($users as $key => $user) {
							array_push($ids, $user);
						}
					}
				}

				if($type == "activated") {
					$users = DB::table('user')
							->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
							->where('user.member_activated', 1)
							->where('corporate_members.corporate_id', $account_link->corporate_id)
							->lists('user.UserID');
					if(sizeof($users) > 0) {
						foreach($users as $key => $user) {
							array_push($ids, $user);
						}
					}
				}

				if($type == "active") {
					$users = DB::table('user')
							->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
							->where('user.Active', 1)
							->where('corporate_members.corporate_id', $account_link->corporate_id)
							->lists('user.UserID');
					if(sizeof($users) > 0) {
						foreach($users as $key => $user) {
							// check if there is panel and non-panel already created
							$panel = DB::table('transaction_history')->where('UserID', $user)->first();
							$non_panel = DB::table('e_claim')->where('user_id', $user)->first();
							
							if($panel || $non_panel) {
								array_push($ids, $user);
							}
						}
					}
				}

				if($type == "removed") {
					$users = DB::table('user')
							->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
							->where('user.Active', 0)
							->where('corporate_members.corporate_id', $account_link->corporate_id)
							->lists('user.UserID');
					if(sizeof($users) > 0) {
						foreach($users as $key => $user) {
							array_push($ids, $user);
						}
					}
				}
			}
			
			$unique_ids = array();
			foreach($ids as $v){
				isset($k[$v]) || ($k[$v]=1) && $unique_ids[] = $v;
			}
			
			if(sizeof($unique_ids) > 0) {
				$users = DB::table('user')
						->whereIn('UserID', $unique_ids)
						->select('UserID', 'Name', 'Email', 'NRIC', 'PhoneNo', 'PhoneCode', 'Job_Title', 'DOB', 'created_at', 'Zip_Code', 'bank_account', 'Active', 'bank_code', 'bank_brh', 'wallet', 'bank_name', 'emp_no', 'member_activated', 'Status')
						->paginate($per_page);
			} else {
				$users = false;
			}
		} else {
			if($search) {
				$users = DB::table('user')
				->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
				->where('corporate_members.corporate_id', $account_link->corporate_id)
				->where('user.Name', 'like', '%'.$search.'%')
				->select('user.UserID', 'user.Name', 'user.Email', 'user.NRIC', 'user.PhoneNo', 'user.PhoneCode', 'user.Job_Title', 'user.DOB', 'user.created_at', 'user.Zip_Code', 'user.bank_account', 'user.Active', 'user.bank_code', 'user.bank_brh', 'user.wallet', 'user.bank_name','user.emp_no', 'user.member_activated', 'user.Status', 'user.passport')
				->paginate($per_page);
			} else {
				$users = DB::table('user')
				->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
				->where('corporate_members.corporate_id', $account_link->corporate_id)
				->select('user.UserID', 'user.Name', 'user.Email', 'user.NRIC', 'user.PhoneNo', 'user.PhoneCode', 'user.Job_Title', 'user.DOB', 'user.created_at', 'user.Zip_Code', 'user.bank_account', 'user.Active', 'user.bank_code', 'user.bank_brh', 'user.wallet', 'user.bank_name', 'emp_no', 'user.member_activated', 'user.Status', 'user.passport')
				->orderBy('corporate_members.removed_status', 'asc')
				->orderBy('user.UserID', 'asc')
				->paginate($per_page);
			}
		}
		
		if($users) {
			$paginate['last_page'] = $users->getLastPage();
			$paginate['current_page'] = $users->getCurrentPage();
			$paginate['total_data'] = $users->getTotal();
			$paginate['from'] = $users->getFrom();
			$paginate['to'] = $users->getTo();
			$paginate['count'] = $users->count();
		}

		// spending account
		$spending_account = DB::table('spending_account_settings')->where('customer_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
		$medical_wallet = (int)$spending_account->medical_enable == 1 ? true : false;
		$wellness_wallet = (int)$spending_account->wellness_enable == 1 ? true : false;

		// return $users;
		$filter = 'current_term';
		$with_employee_id = false;
		foreach ($users as $key => $user) {
			if($user->emp_no) {
				$with_employee_id = true;
			}
			$ids = StringHelper::getSubAccountsID($user->UserID);
			$wallet = DB::table('e_wallet')->where('UserID', $user->UserID)->orderBy('created_at', 'desc')->first();
			$medical_credit_data = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $user->UserID);
			$wellness_credit_data = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $user->UserID);
			// get medical entitlement
			$wallet_entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $user->UserID)->orderBy('created_at', 'desc')->first();
		  	// check if account is schedule for deletion
			$deletion = DB::table('customer_plan_withdraw')->where('user_id', $user->UserID)->first();
			$dependets = DB::table('employee_family_coverage_sub_accounts')
			->where('owner_id', $user->UserID)
			->where('deleted', 0)
			->count();

		    // check if their is a plan tier
			$plan_tier = DB::table('plan_tier_users')
			->join('plan_tiers', 'plan_tiers.plan_tier_id', '=', 'plan_tier_users.plan_tier_id')
			->where('plan_tier_users.user_id', $user->UserID)
			->first();

			$get_employee_plan = DB::table('user_plan_type')->where('user_id', $user->UserID)->orderBy('created_at', 'desc')->first();
			$plan_extension = false;
			$deleted = false;
			$deletion_text = null;
			$date_deleted = false;
			$replacement_text = null;
			$schedule = false;
			$plan_withdraw = false;
			$emp_status = 'active';
			// check if user has replace property
			$user_active_plan_history = DB::table('user_plan_history')->where('user_id', $user->UserID)->where('type', 'started')->orderBy('created_at', 'desc')->first();

			$replace = DB::table('customer_replace_employee')
			->where('old_id', $user->UserID)
			->where('active_plan_id', $user_active_plan_history->customer_active_plan_id)
			->orderBy('created_at', 'desc')
			->first();

			$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_active_plan_history->customer_active_plan_id)->first();
			$plan_type = $active_plan->account_type;
			if($active_plan->account_type == 'stand_alone_plan') {
				$plan_name = "Pro Plan";
			} else if($active_plan->account_type == 'insurance_bundle') {
				$plan_name = "Insurance Bundle";
			} else if($active_plan->account_type == 'trial_plan'){
				$plan_name = "Trial Plan";
			} else if($active_plan->account_type == 'lite_plan') {
				$plan_name = "Basic Plan";
			} else if($active_plan->account_type == 'enterprise_plan') {
				$plan_name = "Enterprise Plan";
			} else if($active_plan->account_type == 'out_of_pocket') {
				$plan_name = "Out of Pocket";
			}

			$employee_status = PlanHelper::getEmployeeStatus($user->UserID);

			if($employee_status['status'] == true) {
				$schedule = $employee_status['schedule_status'];
				$deletion_text = $employee_status['schedule'];
				$expiry_date = $employee_status['expiry_date'];
				$deleted = $employee_status['deleted'];
				$plan_withdraw = $employee_status['plan_withdraw'];
				$emp_status = $employee_status['emp_status'];
			} else {
				$plan_user_history = DB::table('user_plan_history')
				->where('user_id', $user->UserID)
				->where('type', 'started')
				->orderBy('created_at', 'desc')
				->first();

				if(!$plan_user_history) {
                    // create plan user history
					PlanHelper::createUserPlanHistory($user->UserID, $link_account->customer_buy_start_id, $customer_id);
					$plan_user_history = DB::table('user_plan_history')
					->where('user_id', $user->UserID)
					->where('type', 'started')
					->orderBy('created_at', 'desc')
					->first();
				}

				$active_plan = DB::table('customer_active_plan')
				->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)
				->first();
				if(($active_plan->account_type != "lite_plan") && ($active_plan->account_type != "out_of_pocket")) {
					$plan = DB::table('customer_plan')
					->where('customer_plan_id', $active_plan->plan_id)
					->orderBy('created_at', 'desc')
					->first();

					$active_plan_first = DB::table('customer_active_plan')
					->where('plan_id', $active_plan->plan_id)
					->first();

					if((int)$active_plan_first->plan_extention_enable == 1) {            
						$plan_user = DB::table('user_plan_type')
						->where('user_id', $user->UserID)
						->orderBy('created_at', 'desc')
						->first();

						$active_plan_extension = DB::table('plan_extensions')
						->where('customer_active_plan_id', $active_plan_first->customer_active_plan_id)
						->first();

						if($plan_user->fixed == 1 || $plan_user->fixed == "1") {
							$temp_valid_date = date('F d, Y', strtotime('+'.$active_plan_extension->duration, strtotime($active_plan_extension->plan_start)));
							$expiry_date = date('F d, Y', strtotime('-1 day', strtotime($temp_valid_date)));
						} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
							$expiry_date = date('F d, Y', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
						}

						if($active_plan_extension->account_type == 'stand_alone_plan') {
							$plan_name = "Pro Plan";
						} else if($active_plan_extension->account_type == 'insurance_bundle') {
							$plan_name = "Insurance Bundle";
						} else if($active_plan_extension->account_type == 'trial_plan'){
							$plan_name = "Trial Plan";
						} else if($active_plan_extension->account_type == 'lite_plan') {
							$plan_name = "Lite Plan";
						}
					} else {
						$plan_user = DB::table('user_plan_type')
						->where('user_id', $user->UserID)
						->orderBy('created_at', 'desc')
						->first();

						$plan = DB::table('customer_plan')
						->where('customer_plan_id', $active_plan->plan_id)
						->first();

						if($plan_user->fixed == 1 || $plan_user->fixed == "1") {
							$temp_valid_date = date('F d, Y', strtotime('+'.$active_plan_first->duration, strtotime($plan->plan_start)));
							$expiry_date = date('m/d/Y', strtotime('-1 day', strtotime($temp_valid_date)));
						} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
							$expiry_date = date('m/d/Y', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
						}
					}
				} else {
					$expiry_date = date('m/d/Y', strtotime($spending_account->medical_spending_end_date));
				}
			}

			$medical = null;
			$wellness = null;
			if($active_plan->account_type == 'enterprise_plan') {
				// get pending allocation for medical
				$e_claim_amount_pending_medication = DB::table('e_claim')
				->whereIn('user_id', $ids)
				->where('spending_type', 'medical')
				->where('status', 0)
				->sum('claim_amount');

				// get pending allocation for wellness
				$e_claim_amount_pending_wellness = DB::table('e_claim')
				->whereIn('user_id', $ids)
				->where('spending_type', 'wellness')
				->where('status', 0)
				->sum('claim_amount');

				$medical = array(
					'entitlement' => number_format($wallet_entitlement->medical_entitlement, 2),
					'credits_allocation' =>  number_format($medical_credit_data['allocation'], 2),
					'credits_spent' 	=>  number_format($medical_credit_data['get_allocation_spent'], 2),
					'e_claim_amount_pending_medication' =>  number_format($e_claim_amount_pending_medication, 2),
					'visits'			=> $user_active_plan_history->total_visit_limit,
					'balance'			=> $user_active_plan_history->total_visit_limit - $user_active_plan_history->total_visit_created,
					'utilised'			=> $user_active_plan_history->total_visit_created,
					'in_network' 	=>  number_format($medical_credit_data['in_network'], 2),
					'out_network' 	=>  number_format($medical_credit_data['out_network'], 2)
				);

				$wellness_balance = $wellness_credit_data['allocation'] - $wellness_credit_data['get_allocation_spent'];
				$wellness = array(
					'entitlement' => number_format($wallet_entitlement->wellness_entitlement, 2),
					'credits_allocation_wellness'	 => number_format($wellness_credit_data['allocation'], 2),
					'credits_spent_wellness' 		=> number_format($wellness_credit_data['get_allocation_spent'], 2),
					'balance'						=> $active_plan->account_type == 'super_pro_plan' ? 'UNLIMITED' : number_format($wellness_credit_data['allocation'] - $wellness_credit_data['get_allocation_spent'], 2),
					'e_claim_amount_pending_wellness'	=> number_format($e_claim_amount_pending_wellness, 2),
				);
			} else {
				// get pending allocation for medical
				$e_claim_amount_pending_medication = DB::table('e_claim')
				->whereIn('user_id', $ids)
				->where('spending_type', 'medical')
				->where('status', 0)
				->sum('claim_amount');

				// get pending allocation for wellness
				$e_claim_amount_pending_wellness = DB::table('e_claim')
				->whereIn('user_id', $ids)
				->where('spending_type', 'wellness')
				->where('status', 0)
				->sum('claim_amount');

				$medicalBalance = $medical_credit_data['balance'] > 0 ? number_format($medical_credit_data['balance'], 2) : "0.00";
				$medical = array(
					'entitlement' => number_format($wallet_entitlement->medical_entitlement, 2),
					'credits_allocation' => number_format($medical_credit_data['allocation'], 2),
					'credits_spent' 	=> number_format($medical_credit_data['get_allocation_spent'], 2),
					'balance'			=> $active_plan->account_type == 'super_pro_plan' ? 'UNLIMITED' : $medicalBalance,
					'e_claim_amount_pending_medication' => number_format($e_claim_amount_pending_medication, 2)
				);

				$wellnessBalance = $wellness_credit_data['balance'] > 0 ? number_format($wellness_credit_data['balance'], 2) : "0.00";
				$wellness = array(
					'entitlement' => number_format($wallet_entitlement->wellness_entitlement, 2),
					'credits_allocation_wellness'	 => number_format($wellness_credit_data['allocation'], 2),
					'credits_spent_wellness' 		=> number_format($wellness_credit_data['get_allocation_spent'], 2),
					'balance'						=> $active_plan->account_type == 'super_pro_plan' ? 'UNLIMITED' : $wellnessBalance,
					'e_claim_amount_pending_wellness'	=> number_format($e_claim_amount_pending_wellness, 2)
				);
			}

			if($employee_status['status'] == true) {
				$expiry_date = $employee_status['expiry_date'];
				$today = PlanHelper::endDate(date('Y-m-d'));
				$expiry_date = PlanHelper::endDate($expiry_date);
				if($today > $expiry_date) {
					$medical['credits_allocation'] = "0.00";
					$medical['balance'] = "0.00";
					$wellness['credits_allocation'] = "0.00";
					$wellness['balance'] = "0.00";
				}
			}

			$phone_no = (int)$user->PhoneNo;
			$country_code = $user->PhoneCode;
			$member_id = str_pad($user->UserID, 6, "0", STR_PAD_LEFT);

			if((int)$user->Active == 0) {
				$emp_status = 'deleted';
			}

			$cap_per_visit = $wallet->cap_per_visit_medical;

			if($plan_tier) {
				if($wallet->cap_per_visit_medical > 0) {
					$plan_tier->gp_cap_per_visit = $wallet->cap_per_visit_medical;
				} else {
					$cap_per_visit = $plan_tier->gp_cap_per_visit;
				}
			}

			if((int)$user->Active == 1 && (int)$user->member_activated == 1) {
				// statuses
				$panel = DB::table('transaction_history')->where('UserID', $user->UserID)->first();
				$non_panel = DB::table('e_claim')->where('user_id', $user->UserID)->first();
								
				if($panel || $non_panel) {
					$emp_status = 'active';
				} else if((int)$user->Active == 1 && (int)$user->member_activated == 1 && (int)$user->Status == 1){
					$emp_status = 'activated';
				}
			}
			
			if(date('Y-m-d', strtotime($get_employee_plan->plan_start)) > date('Y-m-d') || (int)$user->member_activated == 0 || (int)$user->member_activated == 1 && (int)$user->Status == 0) {
				$emp_status = 'pending';
			}

			$temp = array(
				'spending_account'	=> array(
					'medical' 	=> $medical,
					'wellness'	=> $wellness,
					'currency_type' => $wallet->currency_type
				),
				'account_type'	=>	$active_plan->account_type,
				'plan_method_type'	=>	$active_plan->plan_method,
				'medical_wallet'		=> $medical_wallet,
				'wellness_wallet'		=> $wellness_wallet,
				'dependents'	  		=> $dependets,
				'plan_tier'				=> $plan_tier,
				'gp_cap_per_visit'		=> $cap_per_visit > 0 ? $cap_per_visit : null,
				'name'					=> $user->Name,
				'email'					=> $user->Email,
				'enrollment_date' 		=> $user->created_at,
				'plan_name'				=> $plan_name,
				'start_date'			=> date('F d, Y', strtotime($get_employee_plan->plan_start)),
				'end_date'				=> $active_plan->account_type != 'out_of_pocket' ? date('F d, Y', strtotime($plan_user_history->end_date)) : null,
				'expiry_date'			=> $expiry_date,
				'user_id'				=> $user->UserID,
				'member_id'				=> $member_id,
				'employee_id'			=> $user->emp_no,
				'member_activated'		=> (int)$user->member_activated == 1 ? true : false,
				'nric'					=> $user->NRIC,
				'mobile_no'				=> $phone_no == 0 || $phone_no == null || $phone_no == '' || $phone_no == '0' ? null : $country_code.(string)$phone_no,
				'phone_no'				=> $phone_no,
				'country_code'			=> $country_code,
				'job_title'				=> $user->Job_Title,
				'dob'					=> $user->DOB ? date('Y-m-d', strtotime($user->DOB)) : null,
				'postal_code'			=> $user->Zip_Code,
				'bank_account'			=> $user->bank_account,
				'bank_code'				=> $user->bank_code,
				'bank_branch'			=> $user->bank_brh,
				'bank_name'				=> $user->bank_name,
				'passport'				=> $user->passport,
				'nric'					=> $user->NRIC,
				// 'company'				=> ucwords($user->company_name),
				'employee_plan'			=> $get_employee_plan,
				'date_deleted'  		=> $date_deleted,
				'deletion'      		=> $deleted,
				'deletion_text'    		=> $deletion_text,
				'schedule'				=> $schedule,
				'plan_withdraw_status' 	=> $plan_withdraw,
				'emp_status'			=> $emp_status,
				'account_status'		=> (int)$user->Active == 1 ? true : false,
				'plan_type'				=> $plan_type,
				'wallet_enabled' 		=> (int)$user->wallet == 1 ? true : false,
				'total_visit_limit'          => $user_active_plan_history->total_visit_limit,
            	'total_visit_created'       => $user_active_plan_history->total_visit_created,
				'total_balance_visit'       => $user_active_plan_history->total_visit_limit - $user_active_plan_history->total_visit_created,
				'medical_spending_account_validity'	=> date('d/m/Y', strtotime($spending_account->medical_spending_start_date)).' - '.date('d/m/Y', strtotime($spending_account->medical_spending_end_date)),
				'wellness_spending_account_validity'	=> date('d/m/Y', strtotime($spending_account->wellness_spending_start_date)).' - '.date('d/m/Y', strtotime($spending_account->wellness_spending_end_date)),
			);
			array_push($final_user, $temp);
		}


		$paginate['data'] = $final_user;
		$paginate['with_employee_id'] = $with_employee_id;
		$paginate['medical_wallet'] = $medical_wallet;
		$paginate['wellness_wallet'] = $wellness_wallet;
		return $paginate;
	}

	public function getCorporateUserByAllocated($corporate_id, $customer_id) 
	{
		$allocation_medical_users = DB::table("corporate_members")
		->join("e_wallet", "e_wallet.UserID", "=", "corporate_members.user_id")
		->join("wallet_history", "wallet_history.wallet_id", "=", "e_wallet.wallet_id")
		->where("corporate_members.corporate_id", $corporate_id)
		->whereIn("wallet_history.logs", ["added_by_hr"])
		->groupBy("corporate_members.user_id")
		->get();

		$allocation_wellness_users = DB::table("corporate_members")
		->join("e_wallet", "e_wallet.UserID", "=", "corporate_members.user_id")
		->join("wellness_wallet_history", "wellness_wallet_history.wallet_id", "=", "e_wallet.wallet_id")
		->where("corporate_members.corporate_id", $corporate_id)
		->whereIn("wellness_wallet_history.logs", ["added_by_hr"])
		->groupBy("corporate_members.user_id")
		->get();


		$allocation_users = array_merge($allocation_medical_users, $allocation_wellness_users);

		$id_arr = array();
		$users_allocation = array();

		for( $x = 0; $x < count($allocation_users); $x++ ){
			if( !in_array( $allocation_users[$x]->user_id , $id_arr) ){
				array_push( $id_arr, $allocation_users[$x]->user_id );
				array_push( $users_allocation, $allocation_users[$x] );
			}
		}

		return $users_allocation;
	}
	
	public function userCompanyCreditsAllocated( )
	{

		$input = Input::all();
		$result = self::checkSession();
		$customer_id = $result->customer_buy_start_id;
		$allocated = 0;
		$total_allocation = 0;
		$deleted_employee_allocation = 0;
		$total_deduction_credits = 0;

		$allocated_wellness = 0;
		$total_allocation_wellness = 0;
		$deleted_employee_allocation_wellness = 0;
		$total_wellnesss_allocated = 0;
		$total_deduction_credits_wellness = 0;
		$total_medical_allocation = 0;
		$total_medical_allocated = 0;
		$credits = 0;
		$get_allocation_spent = 0;
		$credits_wellness = 0;
		$get_allocation_spent_wellness = 0;
		$total_medical_balance = 0;
		$total_wellness_balance = 0;
		$currency_type = "sgd";
		$temp_total_allocation = 0;
		$temp_total_deduction = 0;
		$total_medical_allocation = 0;
		$temp_total_allocation_wellness = 0;
		$temp_total_deduction_wellness = 0;
		$total_medical_supp_credits = 0;
		$total_wellness_supp_credits = 0;
		$total_wellness_bonus = 0;
		$total_medical_bonus = 0;
		// get plan
		$plan = DB::table('customer_plan')
		->where('customer_buy_start_id', $customer_id)
		->orderBy('created_at', 'desc')
		->first();
		// if($check_accessibility == true) {
		$company_credits = DB::table('customer_credits')->where('customer_id', $customer_id)->first();
		$currency_type = $company_credits->currency_type;
		$account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
		$filter = 'current_term';

		// if((int)$company_credits->unlimited_medical_credits == 0 && (int)$company_credits->unlimited_wellness_credits == 0) {
			$user_spending_dates_medical = CustomerHelper::getCustomerCreditReset($customer_id, $filter, 'medical');
			$user_spending_dates_wellness = CustomerHelper::getCustomerCreditReset($customer_id, $filter, 'wellness');

			// check if customer has a credit reset in medical
			$customer_credit_reset_medical = DB::table('credit_reset')
			->where('id', $customer_id)
			->where('spending_type', 'medical')
			->where('user_type', 'company')
			->orderBy('created_at', 'desc')
			->first();

			if($user_spending_dates_medical['id']) {
				$temp_total_allocation = DB::table('customer_credit_logs')
				->where('customer_credits_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_added_credits')
				->where('customer_credit_logs_id', '>=', $user_spending_dates_medical['id'])
				->where('created_at', '>=', $user_spending_dates_medical['start'])
				->where('created_at', '<=', $user_spending_dates_medical['end'])
				->sum('credit');

				$temp_total_deduction = DB::table('customer_credit_logs')
				->where('customer_credits_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_deducted_credits')
				->where('customer_credit_logs_id', '>=', $user_spending_dates_medical['id'])
				->where('created_at', '>=', $user_spending_dates_medical['start'])
				->where('created_at', '<=', $user_spending_dates_medical['end'])
				->sum('credit');

				$total_medical_bonus = DB::table('customer_credit_logs')
				->where('customer_credits_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_added_bonus_credits')
				->where('customer_credit_logs_id', '>=', $user_spending_dates_medical['id'])
				->where('created_at', '>=', $user_spending_dates_medical['start'])
				->where('created_at', '<=', $user_spending_dates_medical['end'])
				->sum('credit');
			} else {
				$temp_total_allocation = DB::table('customer_credit_logs')
				->where('customer_credits_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_added_credits')
				->where('created_at', '>=', $user_spending_dates_medical['start'])
				->where('created_at', '<=', $user_spending_dates_medical['end'])
				->sum('credit');

				$temp_total_deduction = DB::table('customer_credit_logs')
				->where('customer_credits_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_deducted_credits')
				->where('created_at', '>=', $user_spending_dates_medical['start'])
				->where('created_at', '<=', $user_spending_dates_medical['end'])
				->sum('credit');

				$total_medical_bonus = DB::table('customer_credit_logs')
				->where('customer_credits_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_added_bonus_credits')
				->where('created_at', '>=', $user_spending_dates_medical['start'])
				->where('created_at', '<=', $user_spending_dates_medical['end'])
				->sum('credit');
			}
			$total_medical_allocation = $temp_total_allocation + $total_medical_bonus - $temp_total_deduction;
			$customer_credit_reset_wellness = DB::table('credit_reset')
			->where('id', $customer_id)
			->where('spending_type', 'wellness')
			->where('user_type', 'company')
			->orderBy('created_at', 'desc')
			->first();

			// if($customer_credit_reset_wellness) {
			if($user_spending_dates_wellness['id']) {
				$temp_total_allocation_wellness = DB::table('customer_credits')
				->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_wellness_credits_logs.logs', 'admin_added_credits')
				->where('customer_wellness_credits_logs.customer_wellness_credits_history_id', '>=', $user_spending_dates_wellness['id'])
				->where('customer_wellness_credits_logs.created_at', '>=', $user_spending_dates_wellness['start'])
				->where('customer_wellness_credits_logs.created_at', '<=', $user_spending_dates_wellness['end'])
				->sum('customer_wellness_credits_logs.credit');

				$temp_total_deduction_wellness = DB::table('customer_credits')
				->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_wellness_credits_logs.logs', 'admin_deducted_credits')
				->where('customer_wellness_credits_logs.customer_wellness_credits_history_id', '>=', $user_spending_dates_wellness['id'])
				->where('customer_wellness_credits_logs.created_at', '>=', $user_spending_dates_wellness['start'])
				->where('customer_wellness_credits_logs.created_at', '<=', $user_spending_dates_wellness['end'])
				->sum('customer_wellness_credits_logs.credit');

				$total_wellness_bonus = DB::table('customer_wellness_credits_logs')
				->where('customer_credits_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_added_bonus_credits')
				->where('customer_wellness_credits_history_id', '>=', $user_spending_dates_wellness['id'])
				->where('created_at', '>=', $user_spending_dates_wellness['start'])
				->where('created_at', '<=', $user_spending_dates_wellness['end'])
				->sum('credit');
			} else {
				$temp_total_allocation_wellness = DB::table('customer_credits')
				->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_wellness_credits_logs.logs', 'admin_added_credits')
				->where('customer_wellness_credits_logs.created_at', '>=', $user_spending_dates_wellness['start'])
				->where('customer_wellness_credits_logs.created_at', '<=', $user_spending_dates_wellness['end'])
				->sum('customer_wellness_credits_logs.credit');

				$temp_total_deduction_wellness = DB::table('customer_credits')
				->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_wellness_credits_logs.logs', 'admin_deducted_credits')
				->where('customer_wellness_credits_logs.created_at', '>=', $user_spending_dates_wellness['start'])
				->where('customer_wellness_credits_logs.created_at', '<=', $user_spending_dates_wellness['end'])
				->sum('customer_wellness_credits_logs.credit');

				$total_wellness_bonus = DB::table('customer_wellness_credits_logs')
				->where('customer_credits_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_added_bonus_credits')
				->where('created_at', '>=', $user_spending_dates_wellness['start'])
				->where('created_at', '<=', $user_spending_dates_wellness['end'])
				->sum('credit');
			}
			$total_allocation_wellness = $temp_total_allocation_wellness + $total_wellness_bonus - $temp_total_deduction_wellness;
		// }

		$spending_account_settings = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$start = $spending_account_settings->medical_spending_start_date;
		$user_allocated = PlanHelper::getActivePlanUsers($customer_id);
		$get_allocation_spent = 0;
		$get_allocation_spent_wellness = 0;
		$end = PlanHelper::endDate($spending_account_settings->medical_spending_start_date);
		// $temp = [];
		foreach ($user_allocated as $key => $user) {
			$wallet = DB::table('e_wallet')->where('UserID', $user)->first();
			$member_spending_dates_medical = MemberHelper::getMemberCreditReset($user, $filter, 'medical');

			if($member_spending_dates_medical) {
				$medical_wallet = PlanHelper::memberMedicalUpdatedCreditsSummary($wallet->wallet_id, $user, $member_spending_dates_medical['start'], $member_spending_dates_medical['end']);
				$get_allocation_spent += $medical_wallet['get_allocation_spent'];
				$allocated += $medical_wallet['allocation'];
				$total_deduction_credits += $medical_wallet['total_deduction_credits'];
				// $deleted_employee_allocation += $medical_wallet['deleted_employee_allocation'];
				$total_medical_balance += $medical_wallet['balance'];
			} else {
				$get_allocation_spent += 0;
				$allocated += 0;
				$total_deduction_credits += 0;
				$deleted_employee_allocation += 0;
				$total_medical_balance += 0;
			}

			$member_spending_dates_wellness = MemberHelper::getMemberCreditReset($user, $filter, 'wellness');

			if($member_spending_dates_wellness) {
				$wellness_wallet = PlanHelper::memberWellnessUpdatedCreditsSummary($wallet->wallet_id, $user, $member_spending_dates_wellness['start'], $member_spending_dates_wellness['end']);
				$get_allocation_spent_wellness += $wellness_wallet['get_allocation_spent'];
				$allocated_wellness += $wellness_wallet['allocation'];
				$total_deduction_credits_wellness += $wellness_wallet['total_deduction_credits_wellness'];
				// $deleted_employee_allocation_wellness += $wellness_wallet['deleted_employee_allocation_wellness'];
				$total_wellness_balance += $wellness_wallet['balance'];
			} else {
				$get_allocation_spent_wellness =+ 0;
				$allocated_wellness += 0;
				$total_deduction_credits_wellness += 0;
				$deleted_employee_allocation_wellness += 0;
				$total_wellness_balance += 0;
			}
		}
		
		$total_medical_allocated = $allocated - $deleted_employee_allocation;
		$total_wellnesss_allocated = $allocated_wellness - $deleted_employee_allocation_wellness;
		$credits = $total_medical_allocation - $total_medical_allocated;
		$credits_wellness = $total_allocation_wellness - $total_wellnesss_allocated;

		$total_medical_supp_credits = $total_medical_allocated * $spending_account_settings->medical_supplementary_credits;
		$total_wellness_supp_credits = $total_wellnesss_allocated * $spending_account_settings->wellness_supplementary_credits;

		// if((int)$company_credits->unlimited_medical_credits == 1 && (int)$company_credits->unlimited_wellness_credits == 1) {
			// $total_medical_allocation = 0;
			// $credits = 0;
			// $total_medical_allocated = 0;
			// $total_medical_balance = 0;
			// $total_allocation_wellness = 0;
			// $credits_wellness = 0;
			// $total_wellnesss_allocated = 0;
			// $total_wellness_balance = 0;
		// }

		if($plan->account_type != "enterprise_plan" && $filter == "current_term") {
			// if($company_credits->balance != $credits) {
			// 		// update medical credits
			// 	\CustomerCredits::where('customer_id', $customer_id)->update(['balance' => $credits]);
			// }

			// if($company_credits->wellness_credits != $credits_wellness) {
			// 		// update wellness credits
			// 	\CustomerCredits::where('customer_id', $customer_id)->update(['wellness_credits' => $credits_wellness]);
			// }

			$credit_update = array(
				'balance' => $credits,
				// 'medical_supp_credits' => $total_medical_supp_credits,
				'wellness_credits' => $credits_wellness,
				// 'wellness_supp_credits' => $total_wellness_supp_credits
			);

			if($company_credits->medical_supp_credits <= 0) {
				$credit_update['medical_supp_credits'] = $total_medical_supp_credits;
			}

			if($company_credits->wellness_supp_credits <= 0) {
				$credit_update['wellness_supp_credits'] = $total_wellness_supp_credits;
			}
			\CustomerCredits::where('customer_id', $customer_id)->update($credit_update);
		}
		// }
		
		return array(
			'total_medical_company_allocation' => number_format($total_medical_allocation, 2),
			'total_medical_company_unallocation' => number_format($credits, 2),
			'total_medical_employee_allocated' => number_format($total_medical_allocated, 2),
			'total_medical_employee_spent'		=> $get_allocation_spent < 0 ? "0.00" : number_format($get_allocation_spent, 2),
			'total_medical_employee_balance' => number_format($total_medical_balance, 2),
			'total_medical_employee_balance_number' => $total_medical_balance,
			'total_medical_wellness_allocation' => number_format($total_allocation_wellness, 2),
			'total_medical_wellness_unallocation' => number_format($credits_wellness, 2),
			'total_wellness_employee_allocated' => number_format($total_wellnesss_allocated, 2),
			'total_wellness_employee_spent'		=> number_format($get_allocation_spent_wellness, 2),
			'total_wellness_employee_balance' => number_format($total_wellness_balance, 2),
			'total_wellness_employee_balance_number' => $total_wellness_balance,
			'total_medical_supp_credits'		=> $total_medical_supp_credits,
			'total_wellness_supp_credits'		=> $total_wellness_supp_credits,
			'company_id' => $customer_id,
			'currency' => $currency_type,
			'unlimited_credits'			=> $plan->account_type == "enterprise_plan" ? true : false,
			'filter'		=> $filter
		);
	}

	public function userCompanyCreditsAllocatedolder()
	{

		$result = self::checkSession();
		$customer_id = $result->customer_buy_start_id;
		$allocated = 0;
		$total_allocation = 0;
		$deleted_employee_allocation = 0;
		$total_deduction_credits = 0;

		$allocated_wellness = 0;
		$total_allocation_wellness = 0;
		$deleted_employee_allocation_wellness = 0;
		$total_wellnesss_allocated = 0;
		$total_deduction_credits_wellness = 0;
		$total_medical_allocation = 0;
		$total_medical_allocated = 0;
		$credits = 0;
		$get_allocation_spent = 0;
		$credits_wellness = 0;
		$get_allocation_spent_wellness = 0;
		$total_medical_balance = 0;
		$total_wellness_balance = 0;
		$currency_type = "sgd";
		$total_allocation_wellness = 0;

		// $check_accessibility = self::hrStatus( );
		$company_credits = DB::table('customer_credits')->where('customer_id', $customer_id)->first();
		// $check_accessibility = PlanHelper::checkCompanyAllocated($customer_id);
		// if($check_accessibility == true) {
		$account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();

		if((int)$company_credits->unlimited_medical_credits == 0 && (int)$company_credits->unlimited_wellness_credits == 0) {

	    	// check if customer has a credit reset in medical
			$customer_credit_reset_medical = DB::table('credit_reset')
			->where('id', $customer_id)
			->where('spending_type', 'medical')
			->where('user_type', 'company')
			->orderBy('created_at', 'desc')
			->first();

			if($customer_credit_reset_medical) {
				$date = date('Y-m-d', strtotime($customer_credit_reset_medical->date_resetted));
				$temp_total_allocation = DB::table('customer_credit_logs')
				->where('customer_credits_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_added_credits')
					// ->where('customer_credit_logs_id', '>=', $customer_credit_reset_medical->wallet_history_id)
				->where('created_at', '>=', $date)
				->sum('credit');

				$temp_total_deduction = DB::table('customer_credit_logs')
				->where('customer_credits_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_deducted_credits')
				->where('customer_credit_logs_id', '>=', $customer_credit_reset_medical->wallet_history_id)
				->sum('credit');
			} else {
				$temp_total_allocation = DB::table('customer_credits')
				->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_credit_logs.logs', 'admin_added_credits')
				->sum('customer_credit_logs.credit');

				$temp_total_deduction = DB::table('customer_credits')
				->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_credit_logs.logs', 'admin_deducted_credits')
				->sum('customer_credit_logs.credit');

			}
			$total_medical_allocation = $temp_total_allocation - $temp_total_deduction;


			    // check if customer has a credit reset in medical
			$customer_credit_reset_wellness = DB::table('credit_reset')
			->where('id', $customer_id)
			->where('spending_type', 'wellness')
			->where('user_type', 'company')
			->orderBy('created_at', 'desc')
			->first();

			if($customer_credit_reset_wellness) {
				$date = date('Y-m-d', strtotime($customer_credit_reset_wellness->date_resetted));
				$temp_total_allocation_wellness = DB::table('customer_credits')
				->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_wellness_credits_logs.logs', 'admin_added_credits')
					// ->where('customer_wellness_credits_logs.customer_wellness_credits_history_id', '>=', $customer_credit_reset_wellness->wallet_history_id)
				->where('customer_wellness_credits_logs.created_at', '>=', $date)
				->sum('customer_wellness_credits_logs.credit');

				$temp_total_deduction_wellness = DB::table('customer_credits')
				->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_wellness_credits_logs.logs', 'admin_deducted_credits')
					// ->where('customer_wellness_credits_logs.customer_wellness_credits_history_id', '>=', $customer_credit_reset_wellness->wallet_history_id)
				->where('customer_wellness_credits_logs.created_at', '>=', $date)
				->sum('customer_wellness_credits_logs.credit');
			} else {
				$temp_total_allocation_wellness = DB::table('customer_credits')
				->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_wellness_credits_logs.logs', 'admin_added_credits')
				->sum('customer_wellness_credits_logs.credit');

				$temp_total_deduction_wellness = DB::table('customer_credits')
				->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_wellness_credits_logs.logs', 'admin_deducted_credits')
				->sum('customer_wellness_credits_logs.credit');
			}
			$total_allocation_wellness = $temp_total_allocation_wellness - $temp_total_deduction_wellness;
		}

		if((int)$company_credits->unlimited_medical_credits == 1 && (int)$company_credits->unlimited_wellness_credits == 1) {
			$user_allocated = PlanHelper::getUnlimitedCorporateUserByAllocated($account_link->corporate_id, $customer_id);
		} else {
			$user_allocated = PlanHelper::getCorporateUserByAllocated($account_link->corporate_id, $customer_id);
		}
	        // return $user_allocated;
		$get_allocation_spent = 0;
		$get_allocation_spent_wellness = 0;

		foreach ($user_allocated as $key => $user) {
			$wallet = DB::table('e_wallet')->where('UserID', $user)->first();
			$medical_wallet = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $user);
			$wellness_wallet = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $user);

			$get_allocation_spent += $medical_wallet['get_allocation_spent'];
			$allocated += $medical_wallet['allocation'];
			$total_deduction_credits += $medical_wallet['total_deduction_credits'];
			$deleted_employee_allocation += $medical_wallet['deleted_employee_allocation'];
			$total_medical_balance += $medical_wallet['medical_balance'];

			$get_allocation_spent_wellness =+ $wellness_wallet['get_allocation_spent'];
			$allocated_wellness += $wellness_wallet['allocation'];
			$total_deduction_credits_wellness += $wellness_wallet['total_deduction_credits_wellness'];
			$deleted_employee_allocation_wellness += $wellness_wallet['deleted_employee_allocation_wellness'];
			$total_wellness_balance += $wellness_wallet['wellness_balance'];
		}


		$total_medical_allocated = $allocated - $deleted_employee_allocation;
		$total_wellnesss_allocated = $allocated_wellness - $deleted_employee_allocation_wellness;
		$credits = $total_medical_allocation - $total_medical_allocated;
		$credits_wellness = $total_allocation_wellness - $total_wellnesss_allocated;
		$currency_type = $company_credits->currency_type;

		if((int)$company_credits->unlimited_medical_credits == 1 && (int)$company_credits->unlimited_wellness_credits == 1) {
			$total_medical_allocation = 0;
			$credits = 0;
			$total_medical_allocated = 0;
			$total_medical_balance = 0;
			$total_allocation_wellness = 0;
			$credits_wellness = 0;
			$total_wellnesss_allocated = 0;
			$total_wellness_balance = 0;
		}

		if($company_credits->balance != $credits) {
				// update medical credits
			\CustomerCredits::where('customer_id', $customer_id)->update(['balance' => $credits]);
		}

		if($company_credits->wellness_credits != $credits_wellness) {
				// update wellness credits
			\CustomerCredits::where('customer_id', $customer_id)->update(['wellness_credits' => $credits_wellness]);
		}
		// }
		
		return array(
			'total_medical_company_allocation' => number_format($total_medical_allocation, 2),
			'total_medical_company_unallocation' => number_format($credits, 2),
			'total_medical_employee_allocated' => number_format($total_medical_allocated, 2),
			'total_medical_employee_spent'		=> number_format($get_allocation_spent, 2),
			'total_medical_employee_balance' => number_format($total_medical_balance, 2),
			'total_medical_employee_balance_number' => $total_medical_balance,
			'total_medical_wellness_allocation' => number_format($total_allocation_wellness, 2),
			'total_medical_wellness_unallocation' => number_format($credits_wellness, 2),
			'total_wellness_employee_allocated' => number_format($total_wellnesss_allocated, 2),
			'total_wellness_employee_spent'		=> number_format($get_allocation_spent_wellness, 2),
			'total_wellness_employee_balance' => number_format($total_wellness_balance, 2),
			'total_wellness_employee_balance_number' => $total_wellness_balance,
			'company_id' => $result->customer_buy_start_id,
			'currency_type' => $currency_type
		);
	}

	public function userCompanyCreditsAllocatedOld()
	{

		$result = self::checkSession();
		$customer_id = $result->customer_buy_start_id;
		$allocated = 0;
		$total_allocation = 0;
		$deleted_employee_allocation = 0;
		$total_deduction_credits = 0;

		$allocated_wellness = 0;
		$total_allocation_wellness = 0;
		$deleted_employee_allocation_wellness = 0;
		$total_wellnesss_allocated = 0;
		$total_deduction_credits_wellness = 0;
		$total_medical_allocation = 0;
		$total_medical_allocated = 0;
		$credits = 0;
		$get_allocation_spent = 0;
		$credits_wellness = 0;
		$get_allocation_spent_wellness = 0;

		$check_accessibility = self::hrStatus( );

		if($check_accessibility['accessibility'] == 1) {
			$company_credits = DB::table('customer_credits')->where('customer_id', $customer_id)->first();
			$account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();

	    	// check if customer has a credit reset in medical
			$customer_credit_reset_medical = DB::table('credit_reset')
			->where('id', $customer_id)
			->where('spending_type', 'medical')
			->where('user_type', 'company')
			->orderBy('created_at', 'desc')
			->first();

			if($customer_credit_reset_medical) {
				// $start = date('Y-m-d', strtotime($customer_credit_reset_medical->date_resetted));
	    		// $end = SpendingInvoiceLibrary::getEndDate($customer_credit_reset_medical->date_resetted);
				// $temp_total_allocation = DB::table('customer_credits')
				// ->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				// ->where('customer_credits.customer_id', $customer_id)
				// ->where('customer_credit_logs.logs', 'admin_added_credits')
				// // ->where('customer_credit_logs.created_at', '>=', date('Y-m-d', strtotime($start)))
				// ->where('customer_credit_logs.customer_credit_logs_id', '>=', $customer_credit_reset_medical->wallet_history_id)
				// ->sum('customer_credit_logs.credit');

				$temp_total_allocation = DB::table('customer_credit_logs')
				->where('customer_credits_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_added_credits')
				->where('customer_credit_logs_id', '>=', $customer_credit_reset_medical->wallet_history_id)
				->sum('credit');


				// $temp_total_deduction = DB::table('customer_credits')
				// ->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				// ->where('customer_credits.customer_id', $customer_id)
				// ->where('customer_credit_logs.logs', 'admin_deducted_credits')
				// // ->where('customer_credit_logs.created_at', '>=', date('Y-m-d', strtotime($start)))
				// ->where('customer_credit_logs.customer_credit_logs_id', '>=', $customer_credit_reset_medical->wallet_history_id)
				$temp_total_deduction = DB::table('customer_credit_logs')
				->where('customer_credits_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_deducted_credits')
				->where('customer_credit_logs_id', '>=', $customer_credit_reset_medical->wallet_history_id)
				->sum('credit');
			} else {
				$temp_total_allocation = DB::table('customer_credits')
				->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_credit_logs.logs', 'admin_added_credits')
				->sum('customer_credit_logs.credit');

				$temp_total_deduction = DB::table('customer_credits')
				->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_credit_logs.logs', 'admin_deducted_credits')
				->sum('customer_credit_logs.credit');

			}
			$total_allocation = $temp_total_allocation - $temp_total_deduction;


		    // check if customer has a credit reset in medical
			$customer_credit_reset_wellness = DB::table('credit_reset')
			->where('id', $customer_id)
			->where('spending_type', 'wellness')
			->where('user_type', 'company')
			->orderBy('created_at', 'desc')
			->first();
			// return array('res' => $customer_credit_reset_wellness);
			if($customer_credit_reset_wellness) {
				// $start = date('Y-m-d', strtotime($customer_credit_reset_medical->date_resetted));
				// $temp_total_allocation_wellness = DB::table('customer_credits')
				// ->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				// ->where('customer_credits.customer_id', $customer_id)
				// ->where('customer_wellness_credits_logs.logs', 'admin_added_credits')
				// // ->where('customer_wellness_credits_logs.created_at', '>=', date('Y-m-d', strtotime($start)))
				// ->where('customer_wellness_credits_logs.customer_wellness_credits_history_id', '>=', $customer_credit_reset_wellness->wallet_history_id)
				// ->sum('customer_wellness_credits_logs.credit');

				$temp_total_allocation_wellness = DB::table('customer_wellness_credits_logs')
				->where('customer_wellness_credits_history_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_added_credits')
				->where('customer_wellness_credits_history_id', '>=', $customer_credit_reset_wellness->wallet_history_id)
				->sum('credit');

				// $temp_total_deduction_wellness = DB::table('customer_credits')
				// ->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				// ->where('customer_credits.customer_id', $customer_id)
				// ->where('customer_wellness_credits_logs.logs', 'admin_deducted_credits')
				// // ->where('customer_wellness_credits_logs.created_at', '>=', date('Y-m-d', strtotime($start)))
				// ->where('customer_wellness_credits_logs.customer_wellness_credits_history_id', '>=', $customer_credit_reset_wellness->wallet_history_id)
				// ->sum('customer_wellness_credits_logs.credit');
				$temp_total_deduction_wellness = DB::table('customer_wellness_credits_logs')
				->where('customer_wellness_credits_history_id', $company_credits->customer_credits_id)
				->where('logs', 'admin_deducted_credits')
				->where('customer_wellness_credits_history_id', '>=', $customer_credit_reset_wellness->wallet_history_id)
				->sum('credit');
			} else {
				$temp_total_allocation_wellness = DB::table('customer_credits')
				->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_wellness_credits_logs.logs', 'admin_added_credits')
				->sum('customer_wellness_credits_logs.credit');

				$temp_total_deduction_wellness = DB::table('customer_credits')
				->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
				->where('customer_credits.customer_id', $customer_id)
				->where('customer_wellness_credits_logs.logs', 'admin_deducted_credits')
				->sum('customer_wellness_credits_logs.credit');
			}
			$total_allocation_wellness = $temp_total_allocation_wellness - $temp_total_deduction_wellness;
	        // return array('medical' => $total_allocation, 'wellness' => $total_allocation_wellness);

			$user_allocated = PlanHelper::getCorporateUserByAllocated($account_link->corporate_id, $customer_id);
	        // return $user_allocated;
			$get_allocation_spent = 0;
			$get_allocation_spent_wellness = 0;
			foreach ($user_allocated as $key => $user) {
				$get_allocation = 0;
				$deducted_allocation = 0;
				$e_claim_spent = 0;
				$in_network_temp_spent = 0;
				$credits_back = 0;

				$get_allocation_wellness = 0;
				$deducted_allocation_wellness = 0;
				$e_claim_spent_wellness = 0;
				$in_network_temp_spent_wellness = 0;
				$credits_back_wellness = 0;

				$wallet = DB::table('e_wallet')->where('UserID', $user)->orderBy('created_at', 'desc')->first();

				$pro_allocation_medical = DB::table('wallet_history')
				->where('wallet_id', $wallet->wallet_id)
				->where('logs', 'pro_allocation')
				->sum('credit');


				$member = DB::table('corporate_members')->where('user_id', $user)->first();
				// check if employee has reset credits
				$employee_credit_reset_medical = DB::table('credit_reset')
				->where('id', $user)
				->where('spending_type', 'medical')
				->where('user_type', 'employee')
				->orderBy('created_at', 'desc')
				->first();

				if($employee_credit_reset_medical) {
					$start = date('Y-m-d', strtotime($employee_credit_reset_medical->date_resetted));
					$wallet_history_id = $employee_credit_reset_medical->wallet_history_id;
					$wallet_history = DB::table('wallet_history')
					->where('wallet_id', $wallet->wallet_id)
					->where('wallet_history_id',  '>=', $wallet_history_id)
					->where('created_at', '>=', $start)
					->get();
				} else {
					$wallet_history = DB::table('wallet_history')->where('wallet_id', $wallet->wallet_id)->get();
				}

				foreach ($wallet_history as $key => $history) {
					if($history->logs == "added_by_hr") {
						$get_allocation += $history->credit;
					}

					if($history->logs == "deducted_by_hr") {
						$deducted_allocation += $history->credit;
					}

					if($history->where_spend == "e_claim_transaction") {
						$e_claim_spent += $history->credit;
					}

					if($history->where_spend == "in_network_transaction") {
						$in_network_temp_spent += $history->credit;
					}

					if($history->where_spend == "credits_back_from_in_network") {
						$credits_back += $history->credit;
					}
				}


				

				if($pro_allocation_medical > 0) {
					$allocation = $pro_allocation_medical;
				} else {
					$allocation = $get_allocation;
					$total_deduction_credits += $deducted_allocation;

					if($member->removed_status == 1) {
						$deleted_employee_allocation += $get_allocation - $deducted_allocation;
					}
				}

				if($pro_allocation_medical > 0) {
					$allocation = 0;
					// $deleted_employee_allocation = 0;
					// $total_deduction_credits = 0;
				}

				$get_allocation_spent += $in_network_temp_spent - $credits_back + $e_claim_spent;
				
				$allocated += $allocation;

				$pro_allocation_wellness = DB::table('wellness_wallet_history')
				->where('wallet_id', $wallet->wallet_id)
				->where('logs', 'pro_allocation')
				->sum('credit');


				
				$employee_credit_reset_wellness = DB::table('credit_reset')
				->where('id', $user)
				->where('spending_type', 'wellness')
				->where('user_type', 'employee')
				->orderBy('created_at', 'desc')
				->first();
				if($employee_credit_reset_wellness) {
					$start = date('Y-m-d', strtotime($employee_credit_reset_wellness->date_resetted));
					$wallet_history_id = $employee_credit_reset_wellness->wallet_history_id;
					// $wallet_wellness_history = DB::table('wellness_wallet_history')
					// 							->join('e_wallet', 'e_wallet.wallet_id', '=', 'wellness_wallet_history.wallet_id')
					// 							->where('e_wallet.UserID', $user->UserID)
					// 							->where('wellness_wallet_history.wellness_wallet_history_id',  '>=', $wallet_history_id)
					// 							->get();
					$wallet_wellness_history = DB::table('wellness_wallet_history')
					->where('wallet_id', $wallet->wallet_id)
					->where('wellness_wallet_history_id',  '>=', $wallet_history_id)
					->get();
				} else {
					$wallet_wellness_history = DB::table('wellness_wallet_history')->where('wallet_id', $wallet->wallet_id)->get();
				}

				foreach ($wallet_wellness_history as $key => $history) {
					if($history->logs == "added_by_hr") {
						$get_allocation_wellness += $history->credit;
					}

					if($history->logs == "deducted_by_hr") {
						$deducted_allocation_wellness += $history->credit;
					}

					if($history->where_spend == "e_claim_transaction") {
						$e_claim_spent_wellness += $history->credit;
					}

					if($history->where_spend == "in_network_transaction") {
						$in_network_temp_spent_wellness += $history->credit;
					}

					if($history->where_spend == "credits_back_from_in_network") {
						$credits_back_wellness += $history->credit;
					}
				}
				
				$allocation_wellness = $get_allocation_wellness;

				if($pro_allocation_wellness > 0) {
					$allocation = $pro_allocation_wellness;
				} else {
					$allocation = $allocation_wellness;
					$total_deduction_credits_wellness += $deducted_allocation_wellness;

					if($member->removed_status == 1) {
						$deleted_employee_allocation_wellness += $get_allocation_wellness - $deducted_allocation_wellness;
					}
				}

				$get_allocation_spent_wellness += $in_network_temp_spent_wellness - $credits_back_wellness + $e_claim_spent_wellness;

				if($pro_allocation_wellness > 0) {
					$allocation_wellness = 0;
					// $deleted_employee_allocation_wellness = 0;
				}
				
				$allocated_wellness += $allocation_wellness;
			}


			// $total_allocated = $total_allocation - $allocated - $deleted_employee_allocation + $total_deduction_credits;

			

			$total_medical_allocation = $total_allocation;
			$total_medical_allocated = $allocated - $deleted_employee_allocation - $total_deduction_credits;

			$total_wellnesss_allocated = $allocated_wellness - $deleted_employee_allocation_wellness - $total_deduction_credits_wellness;

			$credits = $total_medical_allocation - $total_medical_allocated;
			$credits_wellness = $total_allocation_wellness - $total_wellnesss_allocated;

			if($company_credits->balance != $credits) {
				// update medical credits
				\CustomerCredits::where('customer_id', $customer_id)->update(['balance' => $credits]);
			}

			if($company_credits->wellness_credits != $credits_wellness) {
				// update wellness credits
				\CustomerCredits::where('customer_id', $customer_id)->update(['wellness_credits' => $credits_wellness]);
			}
		}

		return array(
			'total_medical_company_allocation' => number_format($total_medical_allocation, 2),
			'total_medical_company_unallocation' => number_format($credits, 2),
			'total_medical_employee_allocated' => number_format($total_medical_allocated, 2),
			'total_medical_employee_spent'		=> $get_allocation_spent < 0 ? "0.00" : number_format($get_allocation_spent, 2),
			'total_medical_employee_balance' => number_format($total_medical_allocated - $get_allocation_spent, 2),
			'total_medical_employee_balance_number' => $total_medical_allocated - $get_allocation_spent,
			'total_medical_wellness_allocation' => number_format($total_allocation_wellness, 2),
			'total_medical_wellness_unallocation' => number_format($credits_wellness, 2),
			'total_wellness_employee_allocated' => number_format($total_wellnesss_allocated, 2),
			'total_wellness_employee_spent'		=> number_format($get_allocation_spent_wellness, 2),
			'total_wellness_employee_balance' => number_format($total_wellnesss_allocated - $get_allocation_spent_wellness, 2),
			'total_wellness_employee_balance_number' => $total_wellnesss_allocated - $get_allocation_spent_wellness,
			'company_id' => $result->customer_buy_start_id,
		);
	}

	// search user/employee
	public function searchEmployee( )
	{
		$result = self::checkSession();
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_id = $result->hr_dashboard_id;

		$input = Input::all();
		$search = $input['search'];
		$id = $result->customer_buy_start_id;
		$account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
		$id = $account_link->corporate_id;
		$final_user = [];
		$paginate = [];
		$users = DB::table('user')
		->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
		->join('corporate', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
		->where(function($query) use ($search, $id){
			$query->where('user.Name', 'like', '%'.$search.'%')
			// ->where('corporate_members.removed_status', 0)
			->where('corporate.corporate_id', $id);
		})
		->orWhere(function($query) use ($search, $id){
			$query->where('user.Email', 'like', '%'.$search.'%')
			// ->where('corporate_members.removed_status', 0)
			->where('corporate.corporate_id', $id);
		})
		->orWhere(function($query) use ($search, $id){
			$query->where('user.PhoneNo', 'like', '%'.$search.'%')
			// ->where('corporate_members.removed_status', 0)
			->where('corporate.corporate_id', $id);
		})
		->groupBy('user.UserID')
		->select('user.UserID', 'user.Name', 'user.Email', 'user.NRIC', 'user.PhoneNo', 'user.PhoneCode', 'user.Job_Title', 'user.DOB', 'user.created_at', 'corporate.company_name', 'corporate_members.removed_status', 'user.Zip_Code', 'user.bank_account', 'user.Active', 'user.bank_code', 'user.bank_brh', 'user.wallet', 'user.bank_name')
		->get();

		if(sizeof($users) == 0) {
			$users = [];
			// check dependents
			$dependents = DB::table('employee_family_coverage_sub_accounts')
			->join('user', 'user.UserID', '=', 'employee_family_coverage_sub_accounts.user_id')
			->join('corporate_members', 'corporate_members.user_id', '=', 'employee_family_coverage_sub_accounts.owner_id')
			->where(function($query) use ($search, $id){
				$query->where('user.Name', 'like', '%'.$search.'%')
				->where('employee_family_coverage_sub_accounts.deleted', 0)
				->where('corporate_members.corporate_id', $id);
			})
			->orWhere(function($query) use ($search, $id){
				$query->where('user.NRIC', 'like', '%'.$search.'%')
				->where('employee_family_coverage_sub_accounts.deleted', 0)
				->where('corporate_members.corporate_id', $id);
			})
							// ->groupBy('user.UserID')
			->select('user.UserID as dependent_user_id', 'corporate_members.user_id as employee_user_id')
			->get();

			foreach ($dependents as $key => $dependent) {
				$user = DB::table('user')
				->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
				->join('corporate', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
				->where('user.UserID', $dependent->employee_user_id)
				->select('user.UserID', 'user.Name', 'user.Email', 'user.NRIC', 'user.PhoneNo', 'user.PhoneCode', 'user.Job_Title', 'user.DOB', 'user.created_at', 'corporate.company_name', 'corporate_members.removed_status', 'user.Zip_Code', 'user.bank_account', 'user.Active', 'user.bank_code', 'user.bank_brh', 'user.wallet', 'user.bank_name')
				->first();
				if($user) {
					array_push($users, $user);
				}
			}
			// return $users;
		}

		// spending account
		$spending_account = DB::table('spending_account_settings')->where('customer_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
		$medical_wallet = (int)$spending_account->medical_enable == 1 ? true : false;
		$wellness_wallet = (int)$spending_account->wellness_enable == 1 ? true : false;

		$filter = "current_term";
		foreach ($users as $key => $user) {
			$ids = StringHelper::getSubAccountsID($user->UserID);
			$wallet = DB::table('e_wallet')->where('UserID', $user->UserID)->orderBy('created_at', 'desc')->first();
			$medical_credit_data = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $user->UserID);
			$wellness_credit_data = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $user->UserID);
			// get medical entitlement
			$wallet_entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $user->UserID)->orderBy('created_at', 'desc')->first();
		  // check if account is schedule for deletion
			$deletion = DB::table('customer_plan_withdraw')->where('user_id', $user->UserID)->first();
			$dependets = DB::table('employee_family_coverage_sub_accounts')
			->where('owner_id', $user->UserID)
			->where('deleted', 0)
			->count();

		    // check if their is a plan tier
			$plan_tier = DB::table('plan_tier_users')
			->join('plan_tiers', 'plan_tiers.plan_tier_id', '=', 'plan_tier_users.plan_tier_id')
			->where('plan_tier_users.user_id', $user->UserID)
			->first();

			$get_employee_plan = DB::table('user_plan_type')->where('user_id', $user->UserID)->orderBy('created_at', 'desc')->first();
			// check if user has replace property
			$user_active_plan_history = DB::table('user_plan_history')
			->where('user_id', $user->UserID)
			// ->where('type', 'started')
			->orderBy('created_at', 'desc')
			->first();
			$plan_extension = false;
			$deleted = false;
			$deletion_text = null;
			$date_deleted = false;
			$replacement_text = null;
			$schedule = false;
			$plan_withdraw = false;
			$emp_status = 'active';
			// $get_employee_plan = DB::table('user_plan_type')->where('user_id', $user->UserID)->orderBy('created_at', 'desc')->first();
			// check if user has replace property
			$user_active_plan_history = DB::table('user_plan_history')->where('user_id', $user->UserID)->where('type', 'started')->orderBy('created_at', 'desc')->first();

			$replace = DB::table('customer_replace_employee')
			->where('old_id', $user->UserID)
			->where('active_plan_id', $user_active_plan_history->customer_active_plan_id)
			->orderBy('created_at', 'desc')
			->first();

			$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_active_plan_history->customer_active_plan_id)->first();
			$plan_type = $active_plan->account_type;
			if($active_plan->account_type == 'stand_alone_plan') {
				$plan_name = "Pro Plan";
			} else if($active_plan->account_type == 'insurance_bundle') {
				$plan_name = "Insurance Bundle";
			} else if($active_plan->account_type == 'trial_plan'){
				$plan_name = "Trial Plan";
			} else if($active_plan->account_type == 'lite_plan') {
				$plan_name = "Basic Plan";
			} else if($active_plan->account_type == 'enterprise_plan') {
				$plan_name = "Enterprise Plan";
			}

			$employee_status = PlanHelper::getEmployeeStatus($user->UserID);

			if($employee_status['status'] == true) {
				$schedule = $employee_status['schedule_status'];
				$deletion_text = $employee_status['schedule'];
				$expiry_date = $employee_status['expiry_date'];
				$deleted = $employee_status['deleted'];
				$plan_withdraw = $employee_status['plan_withdraw'];
				$emp_status = $employee_status['emp_status'];
			} else {
				$plan_user_history = DB::table('user_plan_history')
				->where('user_id', $user->UserID)
				->where('type', 'started')
				->orderBy('created_at', 'desc')
				->first();

				if(!$plan_user_history) {
                    // create plan user history
					PlanHelper::createUserPlanHistory($user->UserID, $link_account->customer_buy_start_id, $customer_id);
					$plan_user_history = DB::table('user_plan_history')
					->where('user_id', $user->UserID)
					->where('type', 'started')
					->orderBy('created_at', 'desc')
					->first();
				}

				$active_plan = DB::table('customer_active_plan')
				->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)
				->first();

				$plan = DB::table('customer_plan')
				->where('customer_plan_id', $active_plan->plan_id)
				->orderBy('created_at', 'desc')
				->first();

				$active_plan_first = DB::table('customer_active_plan')
				->where('plan_id', $active_plan->plan_id)
				->first();

				if((int)$active_plan_first->plan_extention_enable == 1) {            
					$plan_user = DB::table('user_plan_type')
					->where('user_id', $user->UserID)
					->orderBy('created_at', 'desc')
					->first();

					$active_plan_extension = DB::table('plan_extensions')
					->where('customer_active_plan_id', $active_plan_first->customer_active_plan_id)
					->first();

					if($plan_user->fixed == 1 || $plan_user->fixed == "1") {
						$temp_valid_date = date('F d, Y', strtotime('+'.$active_plan_extension->duration, strtotime($active_plan_extension->plan_start)));
						$expiry_date = date('F d, Y', strtotime('-1 day', strtotime($temp_valid_date)));
					} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
						$expiry_date = date('F d, Y', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
					}

					if($active_plan_extension->account_type == 'stand_alone_plan') {
						$plan_name = "Pro Plan";
					} else if($active_plan_extension->account_type == 'insurance_bundle') {
						$plan_name = "Insurance Bundle";
					} else if($active_plan_extension->account_type == 'trial_plan'){
						$plan_name = "Trial Plan";
					} else if($active_plan_extension->account_type == 'lite_plan') {
						$plan_name = "Lite Plan";
					} else if($active_plan_extension->account_type == 'enterprise_plan') {
						$plan_name = "Enterprise Plan";
					}
				} else {
					$plan_user = DB::table('user_plan_type')
					->where('user_id', $user->UserID)
					->orderBy('created_at', 'desc')
					->first();

					$plan = DB::table('customer_plan')
					->where('customer_plan_id', $active_plan->plan_id)
					->first();

					if($plan_user->fixed == 1 || $plan_user->fixed == "1") {
						$temp_valid_date = date('F d, Y', strtotime('+'.$active_plan_first->duration, strtotime($plan->plan_start)));
						$expiry_date = date('m/d/Y', strtotime('-1 day', strtotime($temp_valid_date)));
					} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
						$expiry_date = date('m/d/Y', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
					}
				}
			}

			if(date('Y-m-d', strtotime($get_employee_plan->plan_start)) > date('Y-m-d')) {
				$emp_status = 'pending';
			}

			$medical = null;
			$wellness = null;
			if($active_plan->account_type == 'enterprise_plan') {
				// get pending allocation for medical
				$e_claim_amount_pending_medication = DB::table('e_claim')
				->whereIn('user_id', $ids)
				->where('spending_type', 'medical')
				->where('status', 0)
				->sum('claim_amount');

				// get pending allocation for wellness
				$e_claim_amount_pending_wellness = DB::table('e_claim')
				->whereIn('user_id', $ids)
				->where('spending_type', 'wellness')
				->where('status', 0)
				->sum('claim_amount');

				$medical = array(
					'entitlement' => number_format($wallet_entitlement->medical_entitlement, 2),
					'credits_allocation' => number_format($medical_credit_data['allocation'], 2),
					'credits_spent' 	=> number_format($medical_credit_data['get_allocation_spent'], 2),
					'e_claim_amount_pending_medication' => number_format($e_claim_amount_pending_medication, 2),
					'visits'			=> $user_active_plan_history->total_visit_limit,
					'balance'			=> $user_active_plan_history->total_visit_limit - $user_active_plan_history->total_visit_created,
					'utilised'			=> $user_active_plan_history->total_visit_created,
					'in_network' 	=> number_format($medical_credit_data['in_network'], 2),
					'out_network' 	=> number_format($medical_credit_data['out_network'], 2)
				);
				$balance_wellness = $wellness_credit_data['allocation'] - $wellness_credit_data['get_allocation_spent'];
				$wellness = array(
					'entitlement' => number_format($wallet_entitlement->wellness_entitlement, 2),
					'credits_allocation_wellness'	 => $wellness_credit_data['allocation'] > 0 ? number_format($wellness_credit_data['allocation'], 2) : "0.00",
					'credits_spent_wellness' 		=> number_format($wellness_credit_data['get_allocation_spent'], 2),
					'balance'						=> $active_plan->account_type == 'super_pro_plan' ? 'UNLIMITED' : $balance_wellness > 0 ? number_format($balance_wellness, 2) : 0,
					'e_claim_amount_pending_wellness'	=> number_format($e_claim_amount_pending_wellness, 2)
				);
			} else {
				// get pending allocation for medical
				$e_claim_amount_pending_medication = DB::table('e_claim')
				->whereIn('user_id', $ids)
				->where('spending_type', 'medical')
				->where('status', 0)
				->sum('claim_amount');

				// get pending allocation for wellness
				$e_claim_amount_pending_wellness = DB::table('e_claim')
				->whereIn('user_id', $ids)
				->where('spending_type', 'wellness')
				->where('status', 0)
				->sum('claim_amount');

				$medical = array(
					'entitlement' => number_format($wallet_entitlement->medical_entitlement, 2),
					'credits_allocation' => $medical_credit_data['allocation'] > 0 ? number_format($medical_credit_data['allocation'], 2) : "0.00",
					'credits_spent' 	=> number_format($medical_credit_data['get_allocation_spent'], 2),
					'balance'			=> $medical_credit_data['balance'] > 0 ? number_format($medical_credit_data['balance'], 2) : "0.00",
					'e_claim_amount_pending_medication' => number_format($e_claim_amount_pending_medication, 2)
				);
				$balance_wellness = $wellness_credit_data['allocation'] - $wellness_credit_data['get_allocation_spent'];
				$wellness = array(
					'entitlement' => number_format($wallet_entitlement->wellness_entitlement, 2),
					'credits_allocation_wellness'	 => $wellness_credit_data['allocation'] > 0 ? number_format($wellness_credit_data['allocation'], 2) : "0.00",
					'credits_spent_wellness' 		=> number_format($wellness_credit_data['get_allocation_spent'], 2),
					'balance'						=> $balance_wellness > 0 ? number_format($balance_wellness, 2) : "0.00",
					'e_claim_amount_pending_wellness'	=> number_format($e_claim_amount_pending_wellness, 2)
				);
			}

			$phone_no = (int)$user->PhoneNo;
			$country_code = $user->PhoneCode;

			$cap_per_visit = $wallet->cap_per_visit_medical;

			if($plan_tier) {
				if($wallet->cap_per_visit_medical != 0 || $wallet->cap_per_visit_medical != null) {
					$plan_tier->gp_cap_per_visit = $wallet->cap_per_visit_medical;
				} else {
					$cap_per_visit = $plan_tier->gp_cap_per_visit;
				}
			}

			$member_id = str_pad($user->UserID, 6, "0", STR_PAD_LEFT);
			if((int)$user->Active == 0) {
				$emp_status = 'deleted';
			}
			$temp = array(
				'spending_account'	=> array(
					'medical' 	=> $medical,
					'wellness'	=> $wellness,
					'currency_type' => $wallet->currency_type
				),
				'account_type'	=>	$active_plan->account_type,
				'plan_method_type'	=>	$active_plan->plan_method,
				'dependents'	  		=> $dependets,
				'plan_tier'				=> $plan_tier,
				'gp_cap_per_visit'		=> $cap_per_visit > 0 ? $cap_per_visit : null,
				'name'					=> $user->Name,
				'email'					=> $user->Email,
				'enrollment_date' 		=> $user->created_at,
				'plan_name'				=> $plan_name,
				'start_date'			=> $get_employee_plan->plan_start,
				'expiry_date'			=> $expiry_date,
				'user_id'				=> $user->UserID,
				'member_id'				=> $member_id,
				'mobile_no'				=> $country_code.(string)$phone_no,
				'phone_no'				=> $phone_no,
				'country_code'			=> $country_code,
				'job_title'				=> $user->Job_Title,
				'dob'					=> $user->DOB ? date('Y-m-d', strtotime($user->DOB)) : null,
				'postal_code'			=> $user->Zip_Code,
				'bank_account'			=> $user->bank_account,
				'bank_code'				=> $user->bank_code,
				'bank_branch'			=> $user->bank_brh,
				'bank_name'				=> $user->bank_name,
				'company'				=> ucwords($user->company_name),
				'employee_plan'			=> $get_employee_plan,
				'date_deleted'  		=> $date_deleted,
				'deletion'      		=> $deleted,
				'deletion_text'    		=> $deletion_text,
				'schedule'				=> $schedule,
				'plan_withdraw_status' 	=> $plan_withdraw,
				'emp_status'			=> $emp_status,
				'account_status'		=> (int)$user->Active == 1 ? true : false,
				'plan_type'				=> $plan_type,
				'wallet_enabled' => (int)$user->wallet == 1 ? true : false,
				'medical_wallet'		=> $medical_wallet,
				'wellness_wallet'		=> $wellness_wallet,
				'medical_spending_account_validity'	=> date('d/m/Y', strtotime($spending_account->medical_spending_start_date)).' - '.date('d/m/Y', strtotime($spending_account->medical_spending_end_date)),
				'wellness_spending_account_validity'	=> date('d/m/Y', strtotime($spending_account->wellness_spending_start_date)).' - '.date('d/m/Y', strtotime($spending_account->wellness_spending_end_date)),
			);
			array_push($final_user, $temp);
		}

		$paginate['status'] = true;
		$paginate['data'] = $final_user;

		if($admin_id) {
			$admin_logs = array(
				'admin_id'  => $admin_id,
				'admin_type' => 'mednefits',
				'type'      => 'admin_hr_search_employee',
				'data'      => SystemLogLibrary::serializeData($input)
			);
			SystemLogLibrary::createAdminLog($admin_logs);
		} else {
			$admin_logs = array(
				'admin_id'  => $hr_id,
				'admin_type' => 'hr',
				'type'      => 'admin_hr_search_employee',
				'data'      => SystemLogLibrary::serializeData($input)
			);
			SystemLogLibrary::createAdminLog($admin_logs);
		}

		return $paginate;
	}

	public function floatvalue($val){
		return str_replace(",", "", $val);
		$val = str_replace(",",".",$val);
		$val = preg_replace('/\.(?=.*\.)/', '', $val);
		return floatval($val);
	}

	public function updateEmployeeDetails( )
	{
		$result = self::checkSession();
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_id = $result->hr_dashboard_id;
		$input = Input::all();
		$mobile = preg_replace('/\s+/', '', $input['phone_no']);
		$mobile = (int)$mobile;
		// check if mobile already existed or duplicate
		if(!empty($input['phone_no'])) {
			$check_mobile = DB::table('user')
			->where('PhoneNo', (string)$mobile)
			->whereNotIn('UserID', [$input['user_id']])
			->where('UserType', 5)
			->where('Active', 1)
			->first();

			if($check_mobile) {
				return array('status' => false, 'message' => 'Mobile Number already taken.');
			}
		}	
		if(
			$this->isEmpty($input['phone_no'])
		 	&& $this->isEmpty($input['nric']) 
			&& $this->isEmpty($input['passport'])
		  )
		{
			return array('status' => false, 'message' => 'Please key in either Mobile No, NRIC or passport number to proceed.');
		}




		// check email address
		if(!empty($input['email'])) {
			$check_email= DB::table('user')
			->where('Email', $input['email'])
			->where('UserType', 5)
			->whereNotIn('UserID', [$input['user_id']])
			->where('Active', 1)
			->first();

			if($check_email) {
				return array('status' => false, 'message' => 'Email Address already taken.');
			}
		}
		
		$update = array(
			'Name'				=> $input['name'],
			'NRIC'				=> $input['nric'],
			'Passport'			=> $input['passport'],
			'Zip_Code'			=> !empty($input['postal_code']) ? $input['postal_code'] : null,
			'bank_account'		=> $input['bank_account'],
			'Email'				=> $input['email'],
			'PhoneNo'			=> $input['phone_no'],
			'PhoneCode'			=> "+".$input['country_code'],
			'DOB'				=> $input['dob'],
			'emp_no'			=> $input['emp_id'],
			'bank_name'			=> $input['bank_name']
		);

		try {
			$user = DB::table('user')->where('UserID', $input['user_id'])->update($update);
			if($admin_id) {
				$update['user_id'] = $input['user_id'];
				$admin_logs = array(
					'admin_id'  => $admin_id,
					'admin_type' => 'mednefits',
					'type'      => 'admin_hr_updated_employee_details',
					'data'      => SystemLogLibrary::serializeData($update)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			} else {
				$update['user_id'] = $input['user_id'];
				$admin_logs = array(
					'admin_id'  => $hr_id,
					'admin_type' => 'hr',
					'type'      => 'admin_hr_updated_employee_details',
					'data'      => SystemLogLibrary::serializeData($update)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			}
			return array(
				'status'	=> TRUE,
				'message' => 'Success.'
			);
		} catch (Exception $e) {
			return array(
				'status'	=> FALSE,
				'message' => 'Failed.',
				'reason'	=> var_dump($e)
			);
		}
	}

	private function isEmpty ($input)
	{
		return empty($input) || $input === null;
	}

	public function withDrawEmployees( )
	{
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_data = StringHelper::getJwtHrSession();
		$hr_id = $hr_data->hr_dashboard_id;
		$input = Input::all();
		$date = PlanHelper::endDate(date('Y-m-d'));

		foreach ($input['users'] as $key => $user) {
			$check_withdraw = DB::table('customer_plan_withdraw')->where('user_id', $user['user_id'])->count();
			$expiry = date('Y-m-d', strtotime($user['expiry_date']));
			$expired_date = date('Y-m-d', strtotime('+1 day', strtotime($user['expiry_date'])));
			// return $check_withdraw;
			if($check_withdraw == 0) {
				if($date >= $expired_date) {
					// create refund and delete now
					try {
						$result = MemberHelper::removeEmployee($user['user_id'], $expiry, true, $user);
						if(!$result) {
							return array('status' => FALSE, 'message' => 'Failed to create withdraw employee. Please contact Mednefits and report the issue.');
						}
					} catch(Exception $e) {
						$email = [];
						$email['end_point'] = url('hr/employees/withdraw', $parameter = array(), $secure = null);
						$email['logs'] = 'Withdraw Employee Failed - '.$e;
						$email['emailSubject'] = 'Error log.';
						EmailHelper::sendErrorLogs($email);
						return array('status' => FALSE, 'message' => 'Failed to create withdraw employee. Please contact Mednefits and report the issue.');
					}
				} else {
					try {
						$result = MemberHelper::createWithDrawEmployees($user['user_id'], $expiry, true, true, false, $user);
					} catch(Exception $e) {
						$email = [];
						$email['end_point'] = url('hr/employees/withdraw', $parameter = array(), $secure = null);
						$email['logs'] = 'Withdraw Employee Failed - '.$e;
						$email['emailSubject'] = 'Error log.';
						EmailHelper::sendErrorLogs($email);
						return array('status' => FALSE, 'message' => 'Failed to create withdraw employee. Please contact Mednefits and report the issue.');
					}
				}
			}

			if($admin_id) {
				$admin_logs = array(
					'admin_id'  => $admin_id,
					'admin_type' => 'mednefits',
					'type'      => 'admin_hr_removed_employee',
					'data'      => SystemLogLibrary::serializeData($user)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			} else {
				$admin_logs = array(
					'admin_id'  => $hr_id,
					'admin_type' => 'hr',
					'type'      => 'admin_hr_removed_employee',
					'data'      => SystemLogLibrary::serializeData($user)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			}
		}


		return array('status' => TRUE, 'message' => 'Withdraw Employee(s) Successful.');
	}

	public function withDrawDependent( )
	{
		$user = Input::all();
		$date = date('Y-m-d');
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_data = StringHelper::getJwtHrSession();
		$hr_id = $hr_data->hr_dashboard_id;

		if(empty($user['user_id']) || $user['user_id'] == null) {
			return array('status' => false, 'message' => 'Dependent User ID is required.');
		}

		if(empty($user['expiry_date']) || $user['expiry_date'] == null) {
			return array('status' => false, 'message' => 'Dependent Last Day of Coverage is required.');
		}

		$type = PlanHelper::getUserAccountType($user['user_id']);
		
		if($type != "dependent") {
			return array('status' => false, 'message' => 'User ID is not a Dependent Account.');
		}


		$check_withdraw = DB::table('dependent_plan_withdraw')->where('user_id', $user['user_id'])->first();
		$expiry = date('Y-m-d', strtotime($user['expiry_date']));
		$user_id = $user['user_id'];

		if($check_withdraw) {
			return array('status' => false, 'message' => 'Dependent Account already in Dependent Plan Withdraw.');
		}

		$replace = DB::table('customer_replace_dependent')
		->where('old_id', $user_id)
		->first();

		if($replace) {
			return array('status' => false, 'message' => 'Dependent Account already in Replace Dependent Plan.');
		}

		$seat = DB::table('dependent_replacement_seat')
		->where('user_id', $user_id)
		->first();

		if($seat) {
			return array('status' => false, 'message' => 'Dependent Account already in Replace Dependent Plan Seat.');
		}

		if($date >= $expiry) {
				// create refund and delete now
			try {
				$result = PlanHelper::removeDependent($user['user_id'], $expiry, true, false);
				if(!$result) {
					return array('status' => FALSE, 'message' => 'Failed to create withdraw dependent. Please contact Mednefits and report the issue.');
				}
			} catch(Exception $e) {
				$email = [];
				$email['end_point'] = url('hr/with_draw_dependent', $parameter = array(), $secure = null);
				$email['logs'] = 'Withdraw Dependent Failed - '.$e;
				$email['emailSubject'] = 'Error log.';
				EmailHelper::sendErrorLogs($email);
				return array('status' => FALSE, 'message' => 'Failed to create withdraw employee. Please contact Mednefits and report the issue.');
			}
		} else {
			try {
				$result = PlanHelper::createDependentWithdraw($user['user_id'], $expiry, true, true, false);
					// if($result) {
					// 	self::updateCustomerPlanStatusDeleteUser($user['user_id']);
					// }
			} catch(Exception $e) {
				$email = [];
				$email['end_point'] = url('hr/with_draw_dependent', $parameter = array(), $secure = null);
				$email['logs'] = 'Withdraw Dependent Failed - '.$e;
				$email['emailSubject'] = 'Error log.';
				EmailHelper::sendErrorLogs($email);
				return array('status' => FALSE, 'message' => 'Failed to create withdraw dependent. Please contact Mednefits and report the issue.');
			}
		}

		if($admin_id) {
			$admin_logs = array(
				'admin_id'  => $admin_id,
				'admin_type' => 'mednefits',
				'type'      => 'admin_hr_removed_dependent',
				'data'      => SystemLogLibrary::serializeData($user)
			);
			SystemLogLibrary::createAdminLog($admin_logs);
		} else {
			$admin_logs = array(
				'admin_id'  => $hr_id,
				'admin_type' => 'hr',
				'type'      => 'admin_hr_removed_dependent',
				'data'      => SystemLogLibrary::serializeData($user)
			);
			SystemLogLibrary::createAdminLog($admin_logs);
		}

		return array('status' => TRUE, 'message' => 'Withdraw Dependent Successful.');
	}

	public function updateCustomerPlanStatusDeleteUser($id)
	{
		$user_plan = DB::table('user_plan_history')->where('user_id', $id)->orderBy('date', 'desc')->first();
		$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_plan->customer_active_plan_id)->first();
		$Customer_PlanStatus = new CustomerPlanStatus( );

		$customer_plan_id = $active_plan->plan_id;
		// check if active plan has a plan extenstion
		if((int)$active_plan->plan_extention_enable == 1) {
		// check active plan extension
			$extension = DB::table('plan_extensions')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->first();

			if($extension && (int)$extension->enable == 1) {
				$active_plan = $extension;
			}
		}

		if($active_plan->account_type == "stand_alone_plan" || $active_plan->account_type == "lite_plan" || $active_plan->account_type == "trial_plan") {
			// deduct inputted and enrolled
			$Customer_PlanStatus->addjustCustomerStatus('employees_input', $customer_plan_id, 'decrement', 1);
			$Customer_PlanStatus->addjustCustomerStatus('enrolled_employees', $customer_plan_id, 'decrement', 1);
		} else {
			$Customer_PlanStatus->addjustCustomerStatus('enrolled_employees', $customer_plan_id, 'decrement', 1);
			// \CustomerPlanStatus::where('customer_plan_id', $active_plan->plan_id)->decrement('enrolled_employees', 1);
		}
	}

	public function createWithDrawEmployees($user_id, $expiry_date, $history, $refund_status, $vacate_seat)
	{
		$withdraw = new PlanWithdraw();
		$user_plan_history = new UserPlanHistory();

		// $active_plan = DB::table('user_plan_history')->where('user_id', $user_id)->orderBy('date', 'desc')->first();
		$active_plan = DB::table('user_plan_history')
		->where('user_id', $user_id)
		->where('type', 'started')
		->orderBy('created_at', 'desc')
		->first();

		$plan = DB::table('user_plan_type')->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();

		$calculate = false;
		$total_refund = 0;
		// check if active plan is a trial plan and has a plan extension
		$plan_active = DB::table('customer_active_plan')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->first();
		if($plan_active->account_type == "trial_plan") {
			// check if there is a plan extension
			$extension = DB::table('plan_extensions')
			->where('customer_active_plan_id', $plan_active->customer_active_plan_id)
			->first();

			if($extension && (int)$extension->enable == 1) {
	          	// check plan start if satisfies for the employee plan
				$expiry_date = date('Y-m-d', strtotime($expiry_date));
				$extension_plan_start = date('Y-m-d', strtotime($extension->plan_start));

				if($expiry_date >= $extension_plan_start) {
					$calculate = true;
					$plan_start = $extension_plan_start;
					$invoice = DB::table('corporate_invoice')
					->where('customer_active_plan_id', $plan_active->customer_active_plan_id)
					->where('plan_extention_enable', 1)
					->first();
				} else {
					$calculate = false;
					$plan_start = $plan->plan_start;
				}
			} else {
				$calculate = false;
				$plan_start = $plan->plan_start;
			}
		} else {
			$invoice = DB::table('corporate_invoice')
			->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
			->first();
			$calculate = true;
			$plan_start = $plan->plan_start;
		}

		if($calculate) {
			$diff = date_diff(new DateTime(date('Y-m-d', strtotime($plan_start))), new DateTime(date('Y-m-d')));
			$days = $diff->format('%a') + 1;

			$total_days = date("z", mktime(0,0,0,12,31,date('Y'))) + 1;
			$remaining_days = $total_days - $days;

			$cost_plan_and_days = ($invoice->individual_price/$total_days);
			$temp_total = $cost_plan_and_days * $remaining_days;
			$total_refund = $temp_total * 0.70;
		}

		$amount = $total_refund;

		// check if active plan and date refund is exist
		$refund = DB::table('payment_refund')
		->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
		->where('date_refund')
		->where('status', 0)
		->first();

		if($refund) {
			// save plan withdraw logs
			$payment_refund_id = $refund->payment_refund_id;
		} else {
			$payment_refund_id = PlanHelper::createPaymentsRefund($active_plan->customer_active_plan_id, date('Y-m-d', strtotime($expiry_date)));
		}


		$data = array(
			'payment_refund_id'			=> $payment_refund_id,
			'user_id'					=> $user_id,
			'customer_active_plan_id'	=> $active_plan->customer_active_plan_id,
			'date_withdraw'				=> $expiry_date,
			'amount'					=> $amount
			// 'refund_status'				=> 2,
			// 'vacate_seat'				=> 1
		);

		if($plan_active->account_type == "lite_plan") {
			$data['refund_status'] = 2;
		} else {
			$data['refund_status'] = $refund_status == true ? 0 : 2;
			$data['vacate_seat'] = 1;
		}
		// save history
		// if($history) {
		// 	$user_plan_history_data = array(
		// 		'user_id'		=> $user_id,
		// 		'type'			=> "deleted_expired",
		// 		'date'			=> $expiry_date,
		// 		'customer_active_plan_id' => $active_plan->customer_active_plan_id
		// 	);
		// }

		try {
			// if($history) {
			// 	$user_plan_history->createUserPlanHistory($user_plan_history_data);
			// }
			$withdraw->createPlanWithdraw($data);
			PlanHelper::revemoDependentAccounts($user_id, date('Y-m-d', strtotime($expiry_date)));
			return TRUE;
		} catch(Exception $e) {
			$email = [];
			$email['end_point'] = url('hr/employees/withdraw', $parameter = array(), $secure = null);
			$email['logs'] = 'Withdraw Schdedule Employee Failed - '.$e->getMessage();
			$email['emailSubject'] = 'Error log.';
			EmailHelper::sendErrorLogs($email);
			return FALSE;
		}

	}

	public function removeEmployee($id, $expiry_date, $refund_status)
	{
		$plan = DB::table('user_plan_type')->where('user_id', $id)->orderBy('created_at', 'desc')->first();
		$calculate = false;
		$total_refund = 0;
		// check if active plan is a trial plan and has a plan extension

		$active_plan = DB::table('user_plan_history')
		->where('user_id', $id)
		->where('type', 'started')
		->orderBy('date', 'desc')
		->first();

		$plan_active = DB::table('customer_active_plan')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->first();

		if($plan_active->account_type == "trial_plan") {
			// check if there is a plan extension
			$extension = DB::table('plan_extensions')
			->where('customer_active_plan_id', $plan_active->customer_active_plan_id)
			->first();
			if($extension && (int)$extension->enable == 1) {
	          	// check plan start if satisfies for the employee plan
				$expiry_date = date('Y-m-d', strtotime($expiry_date));
				$extension_plan_start = date('Y-m-d', strtotime($extension->plan_start));

				if($expiry_date >= $extension_plan_start) {
					$calculate = true;
					$invoice = DB::table('corporate_invoice')
					->where('customer_active_plan_id', $plan_active->customer_active_plan_id)
					->where('plan_extention_enable', 1)
					->first();
					$plan_start = $extension_plan_start;
				} else {
					$calculate = false;
					$plan_start = $plan->plan_start;
				}
			} else {
				$calculate = false;
				$plan_start = $plan->plan_start;
			}
		} else {
			$invoice = DB::table('corporate_invoice')
			->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
			->first();
			$calculate = true;
			$plan_start = $plan->plan_start;
		}
		// return array('res' => $plan_start, 'calculate' => $calculate);
		$plan = DB::table('user_plan_type')->where('user_id', $id)->orderBy('created_at', 'desc')->first();

		if($calculate) {
			$diff = date_diff(new DateTime(date('Y-m-d', strtotime($plan_start))), new DateTime(date('Y-m-d')));
			$days = $diff->format('%a') + 1;
			
			$total_days = date("z", mktime(0,0,0,12,31,date('Y'))) + 1;
			$remaining_days = $total_days - $days;

			$cost_plan_and_days = ($invoice->individual_price/$total_days);
			$temp_total = $cost_plan_and_days * $remaining_days;
			$total_refund = $temp_total * 0.70;
		}
		
		$withdraw = new PlanWithdraw();

		// save history
		$user_plan_history = new UserPlanHistory();
		$user_plan_history_data = array(
			'user_id'		=> $id,
			'type'			=> "deleted_expired",
			'date'			=> $expiry_date,
			'customer_active_plan_id' => $active_plan->customer_active_plan_id
		);

		try {
			$user_plan_history->createUserPlanHistory($user_plan_history_data);

			// check if active plan and date refund is exist
			$refund = DB::table('payment_refund')
			->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
			->where('date_refund', date('Y-m-d', strtotime($expiry_date)))
			->where('status', 0)
			->first();

			if($refund) {
				// save plan withdraw logs
				$payment_refund_id = $refund->payment_refund_id;
			} else {
				$payment_refund_id = PlanHelper::createPaymentsRefund($active_plan->customer_active_plan_id, date('Y-m-d', strtotime($expiry_date)));
			}

			$amount = $total_refund;
			$data = array(
				'payment_refund_id'			=> $payment_refund_id,
				'user_id'					=> $id,
				'customer_active_plan_id'	=> $active_plan->customer_active_plan_id,
				'date_withdraw'				=> $expiry_date,
				'amount'					=> $amount,
				// 'refund_status'				=> $refund_status == true ? 0 : 2
				// 'refund_status'				=> 2
			);

			if($plan_active->account_type == "lite_plan") {
				$data['refund_status'] = 2;
			} else {
				$data['refund_status'] = $refund_status == true ? 0 : 2;
			}

			$withdraw->createPlanWithdraw($data);

			$user = DB::table('user')->where('UserID', $id)->first();
			$user_data = array(
				'Active'	=> 0
			);
			// update user and set to inactive
			DB::table('user')->where('UserID', $id)->update($user_data);
			// set company members removed to 1
			DB::table('corporate_members')->where('user_id', $id)->update(['removed_status' => 1]);
			PlanHelper::revemoDependentAccounts($id, date('Y-m-d', strtotime($expiry_date)));
			// if($refund_status == false) {
			PlanHelper::updateCustomerPlanStatusDeleteUserVacantSeat($id);
			// } else {
			// 	self::updateCustomerPlanStatusDeleteUser($id);
			// }
			if($plan_active->account_type == "lite_plan" && $expiry_date < date('Y-m-d')) {
				// return member medical and wellness balance
				// PlanHelper::returnMemberMedicalBalance($id);
				// PlanHelper::returnMemberWellnessBalance($id);
			}
			return TRUE;
		} catch(Exception $e) {
			$email = [];
			$email['end_point'] = url('hr/employees/withdraw', $parameter = array(), $secure = null);
			$email['logs'] = 'Withdraw Employee Failed - '.$e;
			$email['emailSubject'] = 'Error log.';
			EmailHelper::sendErrorLogs($email);
			return FALSE;
		}

		return TRUE;
	}

	public function replaceEmployee( )
	{
		$input = Input::all();
		$user = new User();
		
		$id = PlanHelper::getCusomerIdToken();
		$customer_id = $id;
		if(empty($input['replace_id']) || $input['replace_id'] == null) {
			return array('status' => false, 'message' => 'Employee ID is required.');
		}

		$replace_id = $input['replace_id'];
		if(empty($input['fullname']) || $input['fullname'] == null) {
			return array('status' => false, 'message' => 'Full Name is required.');
		}

		if(empty($input['dob']) || $input['dob'] == null) {
			return array('status' => false, 'message' => 'Date of Birth is required.');
		}

		if(empty($input['mobile']) || $input['mobile'] == null) {
			return array('status' => false, 'message' => 'Mobile No. is required.');
		}

		// if(empty($input['postal_code']) || $input['postal_code'] == null) {
		// 	return array('status' => false, 'message' => 'Postal Code is required.');
		// }

		if(empty($input['last_day_coverage']) || $input['last_day_coverage'] == null) {
			return array('status' => false, 'message' => 'Last Day of Coverage of Employee is required.');
		}

		if(empty($input['plan_start']) || $input['plan_start'] == null) {
			return array('status' => false, 'message' => 'Plan Start of new Employee is required.');
		}

		$validate_last_day_of_coverage = PlanHelper::validateStartDate($input['last_day_coverage']);

		if(!$validate_last_day_of_coverage) {
			return array('status' => false, 'message' => 'Last Day of Coverage of must be a date.');
		}

		$validate_plan_start = PlanHelper::validateStartDate($input['plan_start']);

		if(!$validate_plan_start) {
			return array('status' => false, 'message' => 'Plan Start of must be a date.');
		}

		// check if employee already in replace
		$replace_employee = DB::table('customer_replace_employee')
		->where('old_id', $replace_id)
		->first();

		if($replace_employee) {
			return array('status' => false, 'message' => 'Employee already in Replacement.');
		}

		$medical = 0;
		$wellness = 0;

		$medical = (float)$input['medical_credits'];
		$wellness = (float)$input['wellness_credits'];

		if($medical > 0 || $wellness > 0)	{
			$customer_id = PlanHelper::getCustomerId($replace_id);
			$spending = CustomerHelper::getAccountSpendingStatus($customer_id);
			$customer_credits = DB::table('customer_credits')->where("customer_id", $customer_id)->first();

			if($medical > 0)	{
				if($spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" && $spending['paid_status'] == false) {
					return ['status' => FALSE, 'message' => 'Unable to allocate medical credits since your company is not yet paid for the Plan. Please make payment to enable medical allocation.'];
				}

				if($spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" && $spending['paid_status'] == true) {
					if($medical > $customer_credits->balance) {
						return ['status' => FALSE, 'message' => 'Company Medical Balance is not sufficient for this Member'];
					}
				}
			}

			if($wellness > 0)	{
				if($spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "pre_paid" && $spending['paid_status'] == false) {
					return ['status' => FALSE, 'message' => 'Unable to allocate wellness credits since your company is not yet paid for the Plan. Please make payment to enable wellness allocation.'];
				}

				if($spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "pre_paid" && $spending['paid_status'] == true) {
					if($wellness > $customer_credits->wellness_credits) {
						return ['status' => FALSE, 'message' => 'Company Wellness Balance is not sufficient for this Member'];
					}
				}
			}
		}

		// check if employee exit
		$employee = DB::table('user')
		->where('UserID', $replace_id)
		->where('UserType', 5)
		->first();

		if(!$employee) {
			return array('status' => false, 'message' => 'Employee does not exist.');
		}

		$customer_spending = CustomerHelper::getCustomerWalletStatus($customer_id);
		$customer_data = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();
		
		// check last day of coverage and plan start
		$last_day_of_coverage = date('Y-m-d', strtotime($input['last_day_coverage']));
		$plan_start = date('Y-m-d', strtotime($input['plan_start']));

		$customer_data = DB::table('customer_buy_start')->where('customer_buy_start_id', $id)->first();
		// check company credits
		$customer = DB::table('customer_credits')->where('customer_id', $id)->first();
		$input['postal_code'] = !empty($input['postal_code']) ? $input['postal_code'] : null;
		if($last_day_of_coverage == $plan_start) {
			// return "yeah";
			$result = PlanHelper::createReplacementEmployee($replace_id, $input, $id, false, $medical, $wellness);
			if($result['status'] == true) {
				return array('status' => true, 'message'	=> $result['message']);
			} else {
				return array('status' => false, 'message' => 'Unable to create new employee. Please contact Mednefits Team for assistance.');
			}
		} else {
			// return "schedule";
			// schedule employee replacement
			$replace = new CustomerReplaceEmployee( );
			// $user = DB::table('user')->where('UserID', $replace_id)->first();
			$user_plan_history = DB::table('user_plan_history')->where('user_id', $replace_id)
			->where('type', 'started')
			->orderBy('created_at', 'desc')
			->first();

			if(!$user_plan_history) {
				$active_plan = DB::table('customer_active_plan')
				->where('customer_start_buy_id', $id)
				->orderBy('created_at', 'desc')
				->first();
			} else {
				$active_plan = DB::table('customer_active_plan')
				->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
				->first();
			}

			$deactive_employee_status = 0;
			$replace_status = 0;
			MemberHelper::getEmployeeSpendingAccountSummaryNew($input);

			if(date('Y-m-d') >= $last_day_of_coverage) {
				
				$user_data = array(
					'Active'	=> 0,
					'updated_at' => date('Y-m-d')
				);
				// update user and set to inactive
				DB::table('user')->where('UserID', $replace_id)->update($user_data);
				// set company members removed to 1
				DB::table('corporate_members')->where('user_id',$replace_id)->update(['removed_status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

				$user_plan_history_class = new UserPlanHistory();
				$user_plan_history_data = array(
					'user_id'		=> $replace_id,
					'type'			=> "deleted_expired",
					'date'			=> date('Y-m-d', strtotime($input['last_day_coverage'])),
					'customer_active_plan_id' => $active_plan->customer_active_plan_id
				);
				$user_plan_history_class->createUserPlanHistory($user_plan_history_data);
				$deactive_employee_status = 1;

			}

			// create replace employee right away
			$pending = 1;
			$customer = DB::table('customer_credits')->where('customer_id', $id)->first();
			$corporate = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $id)->first();

			$password = StringHelper::get_random_password(8);

			if(date('Y-m-d') >= $plan_start) {
				$pending = 0;
			}

			if(!empty($input['email']) && $input['email'] != null) {
				$communication_type = "email";
			} else if($input['mobile']) {
				$communication_type = "sms";
			} else {
				$communication_type = "email";
			}

			$data = array(
				'Name'          => $input['fullname'],
				'Password'  => md5($password),
				'Email'         => !empty($input['email']) ? $input['email'] : null,
				'PhoneNo'       => $input['mobile'],
				'PhoneCode' => "+".$input['country_code'],
				'NRIC'          => null,
				'Job_Title'  => 'Other',
				'DOB'       => $input['dob'],
				'Zip_Code'  => $input['postal_code'],
				'Active'        => 1,
				'pending'		=> $pending,
				'communication_type' => $communication_type
			);

			$user_id = $user->createUserFromCorporate($data);

			if($user_id) {
				$corporate_member = array(
					'corporate_id'      => $corporate->corporate_id,
					'user_id'           => $user_id,
					'first_name'        => $input['fullname'],
					'last_name'         => $input['fullname'],
					'type'              => 'member',
					'created_at'        => date('Y-m-d H:i:s'),
					'updated_at'        => date('Y-m-d H:i:s'),
				);
				DB::table('corporate_members')->insert($corporate_member);
				$plan_type = new UserPlanType();
				$check_plan_type = $plan_type->checkUserPlanType($user_id);

				if($check_plan_type == 0) {
					$group_package = new PackagePlanGroup();
					$bundle = new Bundle();
					$user_package = new UserPackage();

					$group_package_id = $group_package->getPackagePlanGroupDefault();
					$result_bundle = $bundle->getBundle($group_package_id);

					foreach ($result_bundle as $key => $value) {
						$user_package->createUserPackage($value->care_package_id, $user_id);
					}

					$plan_type_data = array(
						'user_id'               => $user_id,
						'package_group_id'      => $group_package_id,
						'duration'              => '1 year',
						'plan_start'            => date('Y-m-d', strtotime($input['plan_start']))
					);
					$plan_type->createUserPlanType($plan_type_data);
				}

                // store user plan history
				$user_plan_history = new UserPlanHistory();
				$user_plan_history_data = array(
					'user_id'       => $user_id,
					'type'          => "started",
					'date'          => date('Y-m-d', strtotime($input['plan_start'])),
					'customer_active_plan_id' => $active_plan->customer_active_plan_id
				);

				$user_plan_history->createUserPlanHistory($user_plan_history_data);

				$wallet = \Wallet::where('UserID', $user_id)->first();
				$data_create_history = array(
					'wallet_id'             => $wallet->wallet_id,
					'credit'                    => 0,
					'running_balance' => 0,
					'logs'                      => 'wallet_created',
					'currency_type'		=> $customer_data->currency_type
				);
				\WalletHistory::create($data_create_history);

				if($customer_spending['medical'] == true) {
					if($medical > 0) {
						$customer_credit_logs = new CustomerCreditLogs( );
						$credits = $medical;
		
						if($credits > 0 && $spending['account_type'] != "lite_plan" && $spending['medical_method'] != "pre_paid" || $credits > 0 && $spending['account_type'] == "lite_plan" && $spending['medical_method'] == "post_paid") {
							$result_customer_active_plan = self::allocateCreditBaseInActivePlan($customer_id, $credits, "medical");
		
							if($result_customer_active_plan) {
								$customer_active_plan_id = $result_customer_active_plan;
							} else {
								$customer_active_plan_id = NULL;
							}
							$customer_credits_result = DB::table('customer_credits')->where('customer_id', $customer_id)->increment("balance", $credits);
							if($customer_credits_result) {
								// credit log for wellness
								$customer_credits_logs = array(
									'customer_credits_id'	=> $customer->customer_credits_id,
									'credit'				=> $credits,
									'logs'					=> 'admin_added_credits',
									'running_balance'		=> $customer->balance + $credits,
									'customer_active_plan_id' => $customer_active_plan_id,
									'currency_type'	=> $customer->currency_type,
									'user_id'               => null
								);
		
								$customer_credit_logs->createCustomerCreditLogs($customer_credits_logs);
							}
		
							// give credits
							$wallet_class = new Wallet();
							$update_wallet = $wallet_class->addCredits($user_id, $credits);
							$employee_logs = new WalletHistory();
		
							$wallet_history = array(
								'wallet_id'     => $wallet->wallet_id,
								'credit'            => $credits,
								'logs'              => 'added_by_hr',
								'running_balance'   => $credits,
								'customer_active_plan_id' => $customer_active_plan_id,
								'currency_type'		=> $customer_data->currency_type
							);
		
							$employee_logs->createWalletHistory($wallet_history);
							$customer_credits = new CustomerCredits();

							$customer_credits_left = DB::table('customer_credits')->where('customer_credits_id', $customer->customer_credits_id)->first();
							$data['medical_credit_history'] = $wallet_history;
							if($customer_credits_result) {
								$company_deduct_logs = array(
									'customer_credits_id'   => $customer->customer_credits_id,
									'credit'                => $credits,
									'logs'                  => 'added_employee_credits',
									'user_id'               => $user_id,
									'running_balance'       => 0,
									'customer_active_plan_id' => $customer_active_plan_id,
									'currency_type'		=> $customer_data->currency_type
								);
		
								$customer_credit_logs->createCustomerCreditLogs($company_deduct_logs);
								\CustomerHelper::addSupplementaryCredits($customer->customer_id, 'medical', $credits);
							}
						} else if($customer->balance >= $credits && $spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" && $spending['paid_status'] == true) {
							$result_customer_active_plan = self::allocateCreditBaseInActivePlan($customer_id, $credits, "medical");
							if($result_customer_active_plan) {
								$customer_active_plan_id = $result_customer_active_plan;
							} else {
								$customer_active_plan_id = NULL;
							}
		
							// give credits
							$wallet_class = new Wallet();
							$update_wallet = $wallet_class->addCredits($user_id, $credits);
							$employee_logs = new WalletHistory();
		
							$wallet_history = array(
								'wallet_id'     => $wallet->wallet_id,
								'credit'            => $credits,
								'logs'              => 'added_by_hr',
								'running_balance'   => $credits,
								'customer_active_plan_id' => $customer_active_plan_id,
								'currency_type'		=> $customer_data->currency_type
							);
		
							$employee_logs->createWalletHistory($wallet_history);
							$customer_credits = new CustomerCredits();
		
							$customer_credits_result = $customer_credits->deductCustomerCredits($customer->customer_credits_id, $credits);
							$customer_credits_left = DB::table('customer_credits')->where('customer_credits_id', $customer->customer_credits_id)->first();
							$data['medical_credit_history'] = $wallet_history;
							if($customer_credits_result) {
								$company_deduct_logs = array(
									'customer_credits_id'   => $customer->customer_credits_id,
									'credit'                => $credits,
									'logs'                  => 'added_employee_credits',
									'user_id'               => $user_id,
									'running_balance'       => 0,
									'customer_active_plan_id' => $customer_active_plan_id,
									'currency_type'		=> $customer_data->currency_type
								);
		
								$customer_credit_logs->createCustomerCreditLogs($company_deduct_logs);
							}
						}
					}
				}
				
				if($customer_spending['wellness'] == true) {
					if($wellness > 0) {
						$credits = $wellness;
						$customer_credits_logs = new CustomerWellnessCreditLogs();
						$result_customer_active_plan = self::allocateCreditBaseInActivePlan($customer_id, $credits, "wellness");
		
						if($result_customer_active_plan) {
							$customer_active_plan_id = $result_customer_active_plan;
						} else {
							$customer_active_plan_id = NULL;
						}
		
						if($credits > 0 && $spending['account_type'] != "lite_plan" && $spending['wellness_method'] != "pre_paid" || $credits > 0 && $spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "post_paid") {
							$customer_credits_result = DB::table('customer_credits')->where('customer_id', $customer_id)->increment("wellness_credits", $credits);
							if($customer_credits_result) {
								// credit log for wellness
								$customer_wellness_credits_logs = array(
									'customer_credits_id'	=> $customer->customer_credits_id,
									'credit'				=> $credits,
									'logs'					=> 'admin_added_credits',
									'running_balance'		=> $customer->wellness_credits + $credits,
									'customer_active_plan_id' => $customer_active_plan_id,
									'currency_type'	=> $customer->currency_type,
									'user_id'               => null
								);
	
								$customer_credits_logs->createCustomerWellnessCreditLogs($customer_wellness_credits_logs);
							}
							$wallet_class = new Wallet();
							$update_wallet = $wallet_class->addWellnessCredits($user_id, $credits);
	
							$wallet_history = array(
								'wallet_id'     => $wallet->wallet_id,
								'credit'        => $credits,
								'logs'          => 'added_by_hr',
								'running_balance'   => $credits,
								'customer_active_plan_id' => $customer_active_plan_id,
								'currency_type'		=> $customer_data->currency_type
							);
	
							\WellnessWalletHistory::create($wallet_history);
							$customer_credits = new CustomerCredits();
							$data['wellness_credit_history'] = $wallet_history;
							if($customer_credits_result) {
								$company_deduct_logs = array(
									'customer_credits_id'   => $customer->customer_credits_id,
									'credit'                => $credits,
									'logs'                  => 'added_employee_credits',
									'user_id'               => $user_id,
									'running_balance'       => 0,
									'customer_active_plan_id' => $customer_active_plan_id,
									'currency_type'		=> $customer_data->currency_type
								);
								
								$customer_credits_logs->createCustomerWellnessCreditLogs($company_deduct_logs);
								\CustomerHelper::addSupplementaryCredits($customer->customer_id, 'wellness', $credits);
							}
						} else if((float)$customer->wellness_credits >= (float)$credits && $spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "pre_paid" && $spending['paid_status'] == true) {
							$wallet_class = new Wallet();
							$update_wallet = $wallet_class->addWellnessCredits($user_id, $credits);
		
							$wallet_history = array(
								'wallet_id'     => $wallet->wallet_id,
								'credit'        => $credits,
								'logs'          => 'added_by_hr',
								'running_balance'   => $credits,
								'customer_active_plan_id' => $customer_active_plan_id,
								'currency_type'		=> $customer_data->currency_type
							);
		
							\WellnessWalletHistory::create($wallet_history);
							$customer_credits = new CustomerCredits();
							$customer_credits_result = $customer_credits->deductCustomerWellnessCredits($customer->customer_credits_id, $credits);
							$data['wellness_credit_history'] = $wallet_history;
							if($customer_credits_result) {
								$company_deduct_logs = array(
									'customer_credits_id'   => $customer->customer_credits_id,
									'credit'                => $credits,
									'logs'                  => 'added_employee_credits',
									'user_id'               => $user_id,
									'running_balance'       => 0,
									'customer_active_plan_id' => $customer_active_plan_id,
									'currency_type'		=> $customer_data->currency_type
								);
								
								$customer_credits_logs->createCustomerWellnessCreditLogs($company_deduct_logs);
							}
						}
					}
				}

				$data_entitlement = array(
					'member_id'								=> $user_id,
					'medical_usage_date'					=> date('Y-m-d', strtotime($input['plan_start'])),
					'medical_proration'						=> 'months',
					'medical_entitlement'					=> $medical,
					'medical_allocation'					=> $medical,
					'medical_entitlement_balance'			=> $medical,
					'wellness_usage_date'					=> date('Y-m-d', strtotime($input['plan_start'])),
					'wellness_proration'					=> 'months',
					'wellness_entitlement'					=>  $wellness,
					'wellness_allocation'					=>  $wellness,
					'wellness_entitlement_balance'			=>  $wellness,
					'currency_type'							=> $customer_data->currency_type,
					'created_at'							=> date('Y-m-d H:i:s'),
					'updated_at'							=> date('Y-m-d H:i:s'),
				);
				
				DB::table('employee_wallet_entitlement')->insert($data_entitlement);

				$replace_status = 1;

				$user = DB::table('user')->where('UserID', $user_id)->first();
				$company = DB::table('corporate')->where('corporate_id', $corporate->corporate_id)->first();

				if($user) {
					if($user->communication_type == "sms") {
						$compose = [];
						$compose['name'] = $user->Name;
						$compose['company'] = $company->company_name;
						$compose['plan_start'] = date('F d, Y', strtotime($input['plan_start']));
						$compose['email'] = $user->Email;
						$compose['nric'] = $user->PhoneNo;
						$compose['password'] = $password;
						$compose['phone'] = $user->PhoneNo;

						$compose['message'] = SmsHelper::formatWelcomeEmployeeMessage($compose);
						SmsHelper::sendSms($compose);

					} else {
						if($input['email']) {
							$email_data['company']   = ucwords($company->company_name);
							$email_data['emailName'] = $input['fullname'];
							$email_data['name'] = $input['fullname'];
							$email_data['emailTo']   = $input['email'];
							$email_data['email']   = $input['mobile'];
							$email_data['emailPage'] = 'email-templates.latest-templates.mednefits-welcome-member-enrolled';
							$email_data['emailSubject'] = 'WELCOME TO MEDNEFITS CARE';
							$email_data['start_date'] = date('d F Y', strtotime($input['plan_start']));
							$email_data['pw'] = $password;
							$email_data['url'] = url('/');
							$email_data['plan'] = $active_plan;
							EmailHelper::sendEmail($email_data);
						} else {
							$compose = [];
							$compose['name'] = $user->Name;
							$compose['company'] = $company->company_name;
							$compose['plan_start'] = date('F d, Y', strtotime($input['plan_start']));
							$compose['email'] = $user->Email;
							$compose['nric'] = $user->PhoneNo;
							$compose['password'] = $password;
							$compose['phone'] = $user->PhoneNo;

							$compose['message'] = SmsHelper::formatWelcomeEmployeeMessage($compose);
							SmsHelper::sendSms($compose);
						}
					}
				}
			} else {
				return array('status' => false, 'message' => 'Failed to replace employee.');
			}

			$status = 0;
			if($deactive_employee_status == 1) {
				$status = 1;
				PlanHelper::removeDependentAccountsReplace($replace_id, date('Y-m-d', strtotime($input['last_day_coverage'])));
			}

			$replace_data = array(
				'old_id'				=> $replace_id,
				'new_id'				=> $user_id,
				'active_plan_id'		=> $active_plan->customer_active_plan_id,
				'expired_and_activate'	=> date('Y-m-d', strtotime($input['last_day_coverage'])),
				'start_date'			=> date('Y-m-d', strtotime($input['plan_start'])),
				'status'				=> $status,
				'replace_status'		=> 1,
				'deactive_employee_status' => $deactive_employee_status,
				'first_name'			=> $input['fullname'],
				'last_name'				=> null,
				'nric'					=> null,
				'dob'					=> date('Y-m-d', strtotime($input['dob'])),
				'postal_code'			=> $input['postal_code'],
				'medical'				=> $medical,
				'wellness'				=> $wellness,
				'mobile'					=> $input['mobile'],
				'country_code'					=> $input['country_code']
			);

			$result = $replace->createReplaceEmployee($replace_data);

			if($result) {
				return array('status' => true, 'message' => 'Old Employee will be deactivated by '.date('d F Y', strtotime($input['last_day_coverage'])).' and new employee will be activated by '.date('d F Y', strtotime($input['plan_start'])));
			}
		}

		return array(
			'status'	=> TRUE,
			'message'	=> 'Success'
		);
	}

	public function automaticRemoveEmployee( )
	{
		$user = DB::table('user')->where('UserID', $id)->first();
		$user_data = array(
			'Active'	=> 0
		);
		// update user and set to inactive
		DB::table('user')->where('UserID', $id)->update($user_data);
		// set company members removed to 1
		DB::table('corporate_members')->where('user_id',$id)->update(['removed_status' => 1]);
		$user_plan_history = new UserPlanHistory();
		$user_plan_history_data = array(
			'user_id'		=> $id,
			'type'			=> "expired",
			'date'			=> $input['plan_start']
		);
		$user_plan_history->createUserPlanHistory($user_plan_history_data);
	}

	public function getCompanyContacts( )
	{
		$result = self::checkSession();

		$business_contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

		$business_information = DB::table('customer_business_information')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

		$payment_method = DB::table('customer_payment_method')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

		$billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

		if(!$billing_contact) {
			$billing_contact_create = array(
				'customer_buy_start_id'	=> $result->customer_buy_start_id,
				'billing_name'			=> $business_information->company_name,
				'first_name'			=> $business_contact->first_name,
				'last_name'				=> $business_contact->last_name,
				'billing_address'		=> $business_information->company_address,
				'billing_email'			=> $business_contact->work_email,
				'postal'				=> $business_information->postal_code,
				'phone'					=> $business_contact->phone,
				'created_at'			=> date('Y-m-d H:i:s'),
				'updated_at'			=> date('Y-m-d H:i:s')
			);

			DB::table('customer_billing_contact')->insert($billing_contact_create);
			$billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
		}

		$business_contact_details = array(
			'customer_business_contact_id' => $business_contact->customer_business_contact_id,
			'first_name'		=> $business_contact->first_name,
			'last_name'			=> $business_contact->last_name,
			'work_email'		=> $business_contact->work_email,
			'phone'				=> $business_contact->phone,
			'job_title'			=> $business_contact->job_title
		);

		// $name = explode(" ", trim($billing_contact->billing_name));
		// $first_name = $name;
		// $last_name = null;
		// if(!empty($name[0]) && !empty($name[1])) {
		// 	$first_name = $name[0];

		// 	for( $i = 1; $i < count( $name ) - 1; $i++ ){
		// 		$first_name .= ' ' . $name[$i];
		// 	}

		// 	$last_name =  $name[ sizeof($name) - 1 ];
		// } else {
		// 	$first_name = $billing_contact->billing_name;
		// 	$last_name = $billing_contact->billing_name;
		// }

		$billing_contact_details = array(
			'customer_billing_contact_id' => $billing_contact->customer_billing_contact_id,
			'first_name'		=> $billing_contact->first_name,
			'last_name'			=> $billing_contact->last_name,
			'work_email'		=> $billing_contact->billing_email,
			'billing_address'	=> $billing_contact->billing_address,
			'postal'			=> $billing_contact->postal,
			'phone'				=> $billing_contact->phone
		);

		// if($business_contact->billing_contact == "true") {
		// 	$business_contact_details = $business_contact;
			// $billing_contact_status = false;
		// } else {
		// 	$business_contact_details = DB::table('customer_billing_contact')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
		// 	$billing_contact_status = true;
		// }

		// if($business_contact->billing_address == "false") {
		// 	$business_billing_address = array(
		// 		'company_name'	=> $business_information->company_name,
		// 		'billing_address' => $business_information->company_address,
		// 		'postal'				=> $business_information->postal_code
		// 	);
			// $billing_address_status = false;
		// } else {
		// 	$temp_data = DB::table('customer_billing_address')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
		// 	$business_billing_address = array(
		// 		'company_name'		=> $business_information->company_name,
		// 		'billing_address' => $temp_data->billing_address,
		// 		'postal'					=> $temp_data->postal_code
		// 	);
		// 	$billing_address_status = true;
		// }

		$data = array(
			'business_information'	=> $business_information,
			'business_contact'			=> $business_contact_details,
			'billing_contact'		=> $billing_contact_details,
			// 'billing_address'				=> $business_billing_address,
			'payment_method'				=> $payment_method,
			// 'billing_contact_status' => $billing_contact_status,
			// 'billing_address_status' => $billing_address_status
		);

		return array(
			'status'	=> TRUE,
			'data'		=> $data
		);
	}

	public function updateBusinessInformation( )
	{
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_data = StringHelper::getJwtHrSession();
		$hr_id = $hr_data->hr_dashboard_id;
		$input = Input::all();

		$check = DB::table('customer_business_information')->where('customer_business_information_id', $input['customer_business_information_id'])->count();

		if($check == 0) {
			return array(
				'status'	=> FALSE,
				'message'	=> 'No business information exist'
			);
		}

		$business_information = new CorporateBusinessInformation();

		$data = array(
			'company_address'	=> $input['address'],
			'postal_code'			=> $input['postal']
		);

		$result = $business_information->updateCorporateBusinessInformation($input['customer_business_information_id'], $data);

		if($result) {
			if($admin_id) {
				$admin_logs = array(
					'admin_id'  => $admin_id,
					'admin_type' => 'mednefits',
					'type'      => 'admin_hr_updated_company_business_information',
					'data'      => SystemLogLibrary::serializeData($input)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			} else {
				$admin_logs = array(
					'admin_id'  => $hr_id,
					'admin_type' => 'hr',
					'type'      => 'admin_hr_updated_company_business_information',
					'data'      => SystemLogLibrary::serializeData($input)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			}
			return array(
				'status'	=> TRUE,
				'message'	=> 'Success.'
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Failed.'
		);
	}

	public function updateBusinessContact( )
	{
		$input = Input::all();
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_data = StringHelper::getJwtHrSession();
		$hr_id = $hr_data->hr_dashboard_id;

		$check = DB::table('customer_business_contact')->where('customer_business_contact_id', $input['customer_business_contact_id'])->count();

		if($check == 0) {
			return array(
				'status'	=> FALSE,
				'message'	=> 'No business contact exist'
			);
		}

		$business_contact = new CorporateBusinessContact();

		$data = array(
			'first_name'	=> $input['first_name'],
			'last_name'		=> $input['last_name'],
			'job_title'		=> $input['job_title'],
			'work_email'	=> $input['work_email'],
			'phone'				=> $input['phone']
		);

		$result = $business_contact->updateBusinessContact($input['customer_business_contact_id'], $data);

		if($result) {
			$data['customer_business_contact_id'] = $input['customer_business_contact_id'];
			if($admin_id) {
				$admin_logs = array(
					'admin_id'  => $admin_id,
					'admin_type' => 'mednefits',
					'type'      => 'admin_hr_updated_company_business_contact',
					'data'      => SystemLogLibrary::serializeData($data)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			} else {
				$admin_logs = array(
					'admin_id'  => $hr_id,
					'admin_type' => 'hr',
					'type'      => 'admin_hr_updated_company_business_contact',
					'data'      => SystemLogLibrary::serializeData($data)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			}

			return array(
				'status'	=> TRUE,
				'message'	=> 'Success.'
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Failed.'
		);
	}

	public function updateBillingContact( )
	{
		$input = Input::all();
		$result = self::checkSession();
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_id = $result->hr_dashboard_id;

		// customer_billing_contact_id
		$details = array(
			'first_name'		=> $input['first_name'],
			'last_name'			=> $input['last_name'],
			'billing_email'		=> $input['work_email'],
			'updated_at'		=> date('Y-m-d H:i:s')
		);

		$result = DB::table('customer_billing_contact')
		->where('customer_billing_contact_id', $input['customer_billing_contact_id'])
		->update($details);

		if($result) {
			if($admin_id) {
				$admin_logs = array(
					'admin_id'  => $admin_id,
					'admin_type' => 'mednefits',
					'type'      => 'admin_hr_updated_company_billing_contact',
					'data'      => SystemLogLibrary::serializeData($input)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			} else {
				$admin_logs = array(
					'admin_id'  => $hr_id,
					'admin_type' => 'hr',
					'type'      => 'admin_hr_updated_company_billing_contact',
					'data'      => SystemLogLibrary::serializeData($input)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			}
			return array(
				'status'	=> TRUE,
				'message'	=> 'Success.'
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Failed.'
		);
	}

	public function benefitsTransactions( )
	{
		$result = self::checkSession();
		$transactions = [];
		// get invoice
		$invoice = new CorporateInvoice();
		$active_plan = new CorporateActivePlan();
		$paginate = [];

		$active_plans = DB::table('customer_active_plan')
		->where('customer_start_buy_id', $result->customer_buy_start_id)
		->orderBy('created_at', 'desc')
		->paginate(10);

		$paginate['current_page'] = $active_plans->getCurrentPage();
		$paginate['from'] = $active_plans->getFrom();
		$paginate['last_page'] = $active_plans->getLastPage();
		$paginate['per_page'] = $active_plans->getPerPage();
		$added = 0;

		$all_active_plans = DB::table('customer_active_plan')
		->where('customer_start_buy_id', $result->customer_buy_start_id)
		->get();

		foreach ($all_active_plans as $key => $plan) {
			if((int)$plan->plan_extention_enable == 1) {
				// get plan extention
				$extention = DB::table('plan_extensions')
				->where('customer_active_plan_id', $plan->customer_active_plan_id)
				->first();

				if($extention->enable == 1 && $extention->active == 1) {
					$invoice_plan_link = DB::table('plan_extension_plan_invoice')
					->where('plan_extension_id', $extention->plan_extention_id)
					->first();
					if($invoice_plan_link) {
						$invoice = DB::table('corporate_invoice')
						->where('corporate_invoice_id', $invoice_plan_link->invoice_id)
						->first();
						if($invoice) {
							$added++;
						}
					}
				}
			}

			$added += DB::table('dependent_plans')
			->where('customer_active_plan_id', $plan->customer_active_plan_id)
			->where('tagged', 0)
			->count();
		}

		foreach ($active_plans as $key => $plan) {
			$data = [];
			$get_active_plan = $active_plan->getActivePlan($plan->customer_active_plan_id);
			// $check = $invoice->checkCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);
			// $get_invoice = $invoice->getCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);
			$get_invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)->first();

			if($plan->paid == "true") {
				$data['paid'] = true;
			} else {
				$data['paid'] = false;
			}

			if((int)$get_active_plan->new_head_count == 1) {
				$head_count = TRUE;
			} else {
				$head_count = FALSE;
			}

			$company_plan = DB::table('customer_plan')->where('customer_plan_id', $get_active_plan->plan_id)->first();

			$calculated_prices_end_date = null;
			if((int)$get_active_plan->new_head_count == 0) {
				if($get_active_plan->duration || $get_active_plan->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($calculated_prices_end_date['plan_start'])));
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($company_plan->plan_start)));
				}
				$calculated_prices_end_date = date('Y-m-d', strtotime('-1 day', strtotime($end_plan_date)));
				// $calculated_prices = PlanHelper::calculateInvoicePlanPrice($get_invoice->individual_price, $plan->plan_start, $calculated_prices_end_date);
				$data['price']          = number_format($get_invoice->individual_price, 2);
				$data['amount']					= $get_invoice->employees * $get_invoice->individual_price;
				$data['total']					= $get_invoice->employees * $get_invoice->individual_price;
				$data['amount_due']     = number_format($get_invoice->employees * $get_invoice->individual_price, 2);
				if((int)$get_invoice->override_total_amount_status == 1) {
					$calculated_prices = $get_invoice->override_total_amount;
					$data['calculated_prices'] = $calculated_prices;
				} else {
					$data['calculated_prices'] = $get_invoice->individual_price;
				}

				// get dependent tag
				$dependent_tags = DB::table('dependent_plans')
				->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
				->where('tagged', 1)
				->get();
				$dependent_amount = 0;
				$dependent_amount_due = 0;

				foreach ($dependent_tags as $key => $dependent_plan) {
					$invoice = DB::table('dependent_invoice')->where('dependent_plan_id', $dependent_plan->dependent_plan_id)->first();
					$data['amount'] += $invoice->individual_price * $invoice->total_dependents;
				}

				$data['amount'] = number_format($data['amount'], 2);
				$data['total'] = $data['amount'];
			} else {
				$calculated_prices_end_date = PlanHelper::getCompanyPlanDates($get_active_plan->customer_start_buy_id);
				$end_plan_date = $calculated_prices_end_date['plan_end'];
				$calculated_prices_end_date = $calculated_prices_end_date['plan_end'];
				if((int)$get_invoice->override_total_amount_status == 1) {
					$calculated_prices = $get_invoice->override_total_amount;
				} else {
					$calculated_prices = PlanHelper::calculateInvoicePlanPrice($get_invoice->individual_price, $get_active_plan->plan_start, $calculated_prices_end_date);
				}
				// $calculated_prices = \DecimalHelper::formatDecimal($calculated_prices);
				// $duration = PlanHelper::getPlanDuration($get_active_plan->customer_start_buy_id, $get_active_plan->plan_start);
				$data['price']          = number_format($calculated_prices, 2);
				$data['amount']					= number_format($get_invoice->employees * $calculated_prices, 2);
				$data['total']					= number_format($get_invoice->employees * $calculated_prices, 2);
				$data['amount_due']     = number_format($get_invoice->employees * $calculated_prices, 2);
				$data['calculated_prices'] = $calculated_prices;
			}

			$data['invoice_number'] = $get_invoice->invoice_number;
			$data['invoice_date']		= $get_invoice->invoice_date;
			$data['invoice_due']		= $get_invoice->invoice_due;
			// $data['number_employess'] = $get_invoice->employees;
			$data['plan_start']     = $plan->plan_start;
			$data['plan_end'] 			= $end_plan_date;

			$temp_invoice = array(
				'transaction'		=> 'Invoice - '.$data['invoice_number'],
				'date_issue'		=> date('d/m/Y', strtotime($get_invoice->created_at)),
				'type'					=> 'Invoice',
				'amount'				=> 'S$'.$data['total'],
				'status'				=> $data['paid'],
				'paid'					=> $data['paid'],
				'link'					=> url('benefits/invoice?invoice_id='.$get_invoice->corporate_invoice_id, $parameters = array(), $secure = null),
				'receipt_link'			=> $data['paid'] ? url('benefits/receipt?invoice_id='.$get_invoice->corporate_invoice_id, $parameters = array(), $secure = null) : null,
				'head_count'		=> $head_count,
				'invoice_id' => $get_invoice->corporate_invoice_id,
				'type_invoice'		=> 'employee',
				'calculated_prices'		=> $data['calculated_prices'],
				'currency_type'	=> $get_invoice->currency_type
			);
			array_push($transactions, $temp_invoice);

			if((int)$get_active_plan->plan_extention_enable == 1) {
				// get plan extention
				$extention = DB::table('plan_extensions')
				->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
				->first();

				if($extention->enable == 1 && $extention->active == 1) {
					$invoice_plan_link = DB::table('plan_extension_plan_invoice')
					->where('plan_extension_id', $extention->plan_extention_id)
					->first();
					if($invoice_plan_link) {
						$invoice = DB::table('corporate_invoice')
						->where('corporate_invoice_id', $invoice_plan_link->invoice_id)
						->first();

						if((int)$extention->paid == 1) {
							$data['paid'] = true;
						} else {
							$data['paid'] = false;
						}

						if($invoice) {
							$data['price']          = number_format($invoice->individual_price, 2);
							$data['amount']					= number_format($invoice->employees * $invoice->individual_price, 2);
							$data['total']					= number_format($invoice->employees * $invoice->individual_price, 2);
							$data['amount_due']     = number_format($invoice->employees * $invoice->individual_price, 2);

							$temp_invoice = array(
								'transaction'		=> 'Invoice - '.$invoice->invoice_number,
								'date_issue'		=> date('d/m/Y', strtotime($invoice->created_at)),
								'type'					=> 'Invoice',
								'amount'				=> 'S$'.$data['total'],
								'status'				=> $data['paid'],
								'paid'					=> $data['paid'],
								'link'					=> url('benefits/invoice?invoice_id='.$invoice->corporate_invoice_id, $parameters = array(), $secure = null),
								'receipt_link'			=> $data['paid'] ? url('benefits/receipt?invoice_id='.$invoice->corporate_invoice_id, $parameters = array(), $secure = null) : null,
								'head_count'			=> $head_count,
								'invoice_id' => $invoice->corporate_invoice_id,
								'type_invoice'		=> 'employee'
							);
							array_push($transactions, $temp_invoice);
						}
					}
				}
			}

			// get dependent not tag
			$dependents = DB::table('dependent_plans')
			->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
			->where('tagged', 0)
			->get();

			foreach ($dependents as $key => $dependent_plan) {
				$invoice = DB::table('dependent_invoice')->where('dependent_plan_id', $dependent_plan->dependent_plan_id)->first();

				if((int)$dependent_plan->new_head_count == 0) {
					$data['price']          = number_format($invoice->individual_price, 2);
					$data['amount']			= number_format($invoice->total_dependents * $invoice->individual_price, 2);
					$amount_due 			= $invoice->total_dependents * $invoice->individual_price;
					$data['total']			= $invoice->total_dependents * $invoice->individual_price;

					if((int)$dependent_plan->payment_status == 1) {
						$data['paid'] = true;
						$data['payment_date'] = date('F d, Y', strtotime($invoice->paid_date));
						$data['notes']		  = $invoice->remarks;
						$temp_amount_due = $amount_due - $invoice->paid_amount;
						if($temp_amount_due <= 0) {
							$data['amount_due']     = 0.00;
						} else {
							$data['amount_due'] = $temp_amount_due;
						}
					} else {
						$data['paid'] = false;
						$data['amount_due']     = $amount_due;
					}

					if($dependent_plan->duration || $dependent_plan->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$dependent_plan->duration, strtotime($dependent_plan->plan_start)));
						$data['duration'] = $dependent_plan->duration;
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($dependent_plan->plan_start)));
						$data['duration'] = '12 months';
					}
				} else {
					$calculated_prices_end_date = PlanHelper::getCompanyPlanDates($get_active_plan->customer_start_buy_id);
					$calculated_prices_end_date = $calculated_prices_end_date['plan_end'];
					$calculated_prices = PlanHelper::calculateInvoicePlanPrice($invoice->individual_price, $dependent_plan->plan_start, $calculated_prices_end_date);
					// $dependent_plan->calculated_prices = $calculated_prices;
					// $dependent_plan->amount = number_format($calculated_prices * $dependent->total_dependents, 2);

					$data['price']          = number_format($calculated_prices, 2);
					$amount_due = $invoice->total_dependents * $calculated_prices;
					$data['amount']					= number_format($invoice->total_dependents * $calculated_prices, 2);
					$data['total']					= $invoice->total_dependents * $calculated_prices;
					$data['duration'] = $dependent_plan->duration;

					if((int)$dependent_plan->payment_status == 1) {
						$data['paid'] = true;
						$data['payment_date'] = date('F d, Y', strtotime($invoice->paid_date));
						$data['notes']		  = $invoice->remarks;
						$temp_amount_due = $amount_due - $invoice->paid_amount;
						if($temp_amount_due <= 0) {
							$data['amount_due']     = 0.00;
						} else {
							$data['amount_due'] = $temp_amount_due;
						}
					} else {
						$data['paid'] = false;
						$data['amount_due']     = $amount_due;
					}
				}

				$temp_invoice = array(
					'transaction'		=> 'Invoice - '.$invoice->invoice_number,
					'date_issue'		=> date('d/m/Y', strtotime($invoice->created_at)),
					'type'					=> 'Invoice',
					'amount'				=> 'S$'.number_format($data['total'], 2),
					'status'				=> $data['paid'],
					'paid'					=> $data['paid'],
					'link'					=> url('benefits/invoice?invoice_id='.$invoice->dependent_invoice_id, $parameters = array(), $secure = null),
					'receipt_link'			=> null,
					'invoice_id' => $invoice->dependent_plan_id,
					'type_invoice'		=> 'dependent',
					'currency_type'	=> $invoice->currency_type
				);
				array_push($transactions, $temp_invoice);
			}
		}
		
		$paginate['to'] = sizeof($transactions);
		$paginate['total'] = $active_plans->getTotal() + $added;
		$paginate['data'] = $transactions;
		$paginate['added'] = $added;
		return $paginate;
		// $new_transactions = array_merge($transactions, $statements);
	}

	public function calculateInvoicePlanPrice($default_price, $start, $end)
	{
		$diff = date_diff(new \DateTime(date('Y-m-d', strtotime($start))), new \DateTime(date('Y-m-d', strtotime($end))));
		$days = $diff->format('%a');

		$total_days = date("z", mktime(0,0,0,12,31,date('Y'))) + 1;
		$remaining_days = $days;

		$cost_plan_and_days = ($default_price / $total_days);
		return $cost_plan_and_days * $remaining_days;
	}

	public function getHrBenfitSpendingInvoice( )
	{
		// get company credit invoices
		$result = self::checkSession();
		return self::getCompanyCreditsLists($result->customer_buy_start_id);
	}

	public function getStatementFull($customer_id, $start, $end, $plan)
	{
		$input = Input::all();
        // $start = date('Y-m-01', strtotime($input['start']));
        // $end = date('Y-m-t', strtotime($input['end']));
		$result = self::checkSession();
		$lite_plan = false;
		$final_end = date('Y-m-d H:i:s', strtotime('+22 hours', strtotime($end)));
		$e_claim = [];
		$transaction_details = [];
		$total_consultation = 0;
		$total_transaction_spent = 0;
		$total_e_claim_spent = 0;

        // get all hr employees, spouse and dependents
		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();

		$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();

		if($plan->account_type === "lite_plan" || $plan->account_type == "insurance_bundle" && $plan->secondary_account_type == "insurance_bundle_lite") {
			$lite_plan = true;
		}

		foreach ($corporate_members as $key => $member) {
			$ids = StringHelper::getSubAccountsID($member->user_id);
            // get e claim
			if($lite_plan) {
				$temp_trans_lite_plan = DB::table('transaction_history')
				->whereIn('UserID', $ids)
                                // ->where('mobile', 1)
				->where('in_network', 1)
				->where('lite_plan_enabled', 1)
                                // ->where('health_provider_done', 0)
				->where('deleted', 0)
				->where('paid', 1)
				->where('date_of_transaction', '>=', $start)
				->where('date_of_transaction', '<=', $final_end)
				->orderBy('created_at', 'desc')
				->get();

				$temp_trans = DB::table('transaction_history')
				->whereIn('UserID', $ids)
                                // ->where('mobile', 1)
				->where('in_network', 1)
				->where('credit_cost', '>', 0)
                                // ->where('health_provider_done', 0)
				->where('deleted', 0)
				->where('paid', 1)
				->where('date_of_transaction', '>=', $start)
				->where('date_of_transaction', '<=', $final_end)
				->orderBy('created_at', 'desc')
				->get();
				$transactions_temp = array_merge($temp_trans_lite_plan, $temp_trans);
				$transactions = self::my_array_unique($transactions_temp);
			} else {
                // get in-network transactions
				$transactions = DB::table('transaction_history')
				->whereIn('UserID', $ids)
                                // ->where('in_network', 1)
				->where('health_provider_done', 0)
				->where('deleted', 0)
				->where('date_of_transaction', '>=', $start)
				->where('date_of_transaction', '<=', $final_end)
				->orderBy('date_of_transaction', 'desc')
				->get();

			}


            // in-network transactions
			foreach ($transactions as $key => $trans) {
				if($trans) {
					$total_transaction_spent += $trans->credit_cost;
					$receipt_images = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->get();
					$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
					$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
					$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
					$procedure_temp = "";

					if((int)$trans->lite_plan_enabled == 1) {
						$total_consultation += $trans->co_paid_amount;
					}

                // get services
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
						$receipt_files = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->get();
					} else {
						$receipt_status = FALSE;
						$receipt_files = FALSE;
					}

					$total_amount = number_format($trans->credit_cost, 2);

					if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
						$receipt_status = TRUE;
						$health_provider_status = TRUE;
						$payment_type = "Cash";
						if((int)$trans->lite_plan_enabled == 1) {
							$total_amount = number_format($trans->co_paid_amount, 2);
						}
					} else {
						$payment_type = "Mednefits Credits";
						$health_provider_status = FALSE;
						if((int)$trans->lite_plan_enabled == 1) {
							$total_amount = number_format($trans->credit_cost + $trans->co_paid_amount, 2);
						}
					}

                // get clinic type
					$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
					$type = "";
					if((int)$clinic_type->head == 1 || $clinic_type->head == "1") {
						if($clinic_type->Name == "General Practitioner") {
							$type = "general_practitioner";
						} else if($clinic_type->Name == "Dental Care") {
							$type = "dental_care";
						} else if($clinic_type->Name == "Traditional Chinese Medicine") {
							$type = "tcm";
						} else if($clinic_type->Name == "Health Screening") {
							$type = "health_screening";
						} else if($clinic_type->Name == "Wellness") {
							$type = "wellness";
						} else if($clinic_type->Name == "Health Specialist") {
							$type = "health_specialist";
						}
					} else {
						$find_head = DB::table('clinic_types')
						->where('ClinicTypeID', $clinic_type->sub_id)
						->first();
						if($find_head->Name == "General Practitioner") {
							$type = "general_practitioner";
						} else if($find_head->Name == "Dental Care") {
							$type = "dental_care";
						} else if($find_head->Name == "Traditional Chinese Medicine") {
							$type = "tcm";
						} else if($find_head->Name == "Health Screening") {
							$type = "health_screening";
						} else if($find_head->Name == "Wellness") {
							$type = "wellness";
						} else if($find_head->Name == "Health Specialist") {
							$type = "health_specialist";
						}
					}

                // check user if it is spouse or dependent
					if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
						$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
						$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
						$sub_account = ucwords($temp_account->Name);
						$sub_account_type = $temp_sub->user_type;
						$owner_id = $temp_sub->owner_id;
					} else {
						$sub_account = FALSE;
						$sub_account_type = FALSE;
						$owner_id = $customer->UserID;
					}



					$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

					$format = array(
						'clinic_name'       => $clinic->Name,
						'clinic_image'      => $clinic->image,
						'amount'            => number_format($trans->procedure_cost, 2),
						'total_amount'            => $total_amount,
						'clinic_type_and_service' => $clinic_name,
						'service'           => $procedure,
						'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->created_at)),
						'member'            => ucwords($customer->Name),
						'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
						'receipt_status'    => $receipt_status,
						'health_provider_status' => $health_provider_status,
						'user_id'           => $trans->UserID,
						'type'              => 'In-Network',
						'month'             => date('M', strtotime($trans->created_at)),
						'day'               => date('d', strtotime($trans->created_at)),
						'time'              => date('h:ia', strtotime($trans->created_at)),
						'clinic_type'       => $type,
						'owner_account'     => $sub_account,
						'owner_id'          => $owner_id,
						'sub_account_user_type' => $sub_account_type,
						'co_paid'           => $trans->co_paid_amount,
						'receipt_files'      => $receipt_files,
						'payment_type'      => $payment_type,
						'spending_type'     => $trans->spending_type,
						'consultation'      => number_format($trans->co_paid_amount, 2),
						'lite_plan'         => $trans->lite_plan_enabled == 1 ? true : false
					);

					array_push($transaction_details, $format);
				}
			}
		}

        // sort in-network transaction
		usort($transaction_details, function($a, $b) {
			return strtotime($b['date_of_transaction']) - strtotime($a['date_of_transaction']);
		});

		return array(
			'total_transaction_spent'   => number_format($total_transaction_spent, 2),
			'in_network_transactions'   => $transaction_details,
			'total_consultation'        => number_format($total_consultation, 2)
		);
	}

	public function getCompanyCreditsLists($customer_id)
	{
		$credits_statements = DB::table('company_credits_statement')->where('statement_customer_id', $customer_id)->get();

		$format = [];
		$lite_plan = false;
		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();

		foreach ($credits_statements as $key => $data) {
			self::insertOrCheckTransactionCredits($data->statement_customer_id, $data->statement_id, $data->statement_start_date, $data->statement_end_date, $plan);
			$results = self::getStatementFull($data->statement_customer_id, $data->statement_start_date, $data->statement_end_date, $plan);

			if($results['total_transaction_spent'] > 0 || $results['total_consultation'] > 0) {
				$temp = array(
					'transaction'		=> 'Invoice - '.$data->statement_number,
					'date_issue'		=> date('d/m/Y', strtotime($data->created_at)),
					'type'				=> 'Invoice',
					'amount'			=> 'S$'.number_format($results['total_transaction_spent'] + $results['total_consultation'], 2),
					'status'			=> (int)$data->statement_status,
					'head_count'		=> FALSE,
					'statement_id' 	=> $data->statement_id
				);

				array_push($format, $temp);

				// if((int)$data->statement_status == 1) {
				// 	// receipt
				// 	$temp = array(
				// 		'transaction'		=> 'Invoice - '.$data->statement_number,
				// 		'date_issue'		=> date('d/m/Y', strtotime($data->paid_date)),
				// 		'type'				=> 'Receipt',
				// 		'amount'			=> 'S$'.number_format($data->paid_amount, 2),
				// 		'status'			=> $data->statement_status,
				// 		'statement_id' 		=> $data->statement_id
				// 	);
				// 	array_push($format, $temp);
				// }
			}
		}

		return $format;
	}

	public function my_array_unique($array, $keep_key_assoc = false){
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

	public function insertOrCheckTransactionCredits($customer_id, $statement_id, $start_date, $end_date, $plan)
	{
		$start = date('Y-m-d', strtotime($start_date));
		$temp_end = date('Y-m-t', strtotime($end_date));
		$end = date('Y-m-d h:i:s', strtotime('+22 hours', strtotime($end_date)));
		$lite_plan = false;
		$check = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->count();

		if($check == 0) {
			return FALSE;
		}

		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
		$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();

		if($plan->account_type === "lite_plan" || $plan->account_type == "insurance_bundle" && $plan->secondary_account_type == "insurance_bundle_lite") {
			$lite_plan = true;
		}

		foreach ($corporate_members as $key => $member) {
			$ids = StringHelper::getSubAccountsID($member->user_id);

			if($lite_plan) {
				$temp_trans_lite_plan = DB::table('transaction_history')
				->whereIn('UserID', $ids)
                                // ->where('in_network', 1)
				->where('lite_plan_enabled', 1)
				->where('deleted', 0)
				->where('paid', 1)
				->where('date_of_transaction', '>=', $start)
				->where('date_of_transaction', '<=', $end)
				->orderBy('created_at', 'desc')
				->get();

				$temp_trans = DB::table('transaction_history')
				->whereIn('UserID', $ids)
                            // ->where('mobile', 1)
                            // ->where('in_network', 1)
				->where('health_provider_done', 0)
				->where('deleted', 0)
				->where('paid', 1)
				->where('date_of_transaction', '>=', $start)
				->where('date_of_transaction', '<=', $end)
				->orderBy('created_at', 'desc')
				->get();
				$transactions_temp = array_merge($temp_trans_lite_plan, $temp_trans);
				$in_network = self::my_array_unique($transactions_temp);
			} else {	
				$in_network = DB::table('transaction_history')
				->whereIn('UserID', $ids)
				->where('credit_cost', '>', 0)
				->where('date_of_transaction', '>=', $start)
				->where('date_of_transaction', '<=', $end)
				->get();
			}
			$e_claim = DB::table('e_claim')
			->whereIn('user_id', $ids)
			->where('date', '>=', $start)
			->where('date', '<=', $end)
			->where('status', 1)
			->get();

		  // array_push($temp, $e_claim);
			foreach($e_claim as $key => $res) {
				$check_eclaim_transaction = DB::table('statement_e_claim_transactions')
				->where('e_claim_id', $res->e_claim_id)
				->count( );
				if($check_eclaim_transaction == 0) {
					$temp_e_claim = array(
						'statement_id'  => $statement_id,
						'e_claim_id'    => $res->e_claim_id
					);
					\CompanyStatementEclaimTransaction::create($temp_e_claim);
				}
			}

			foreach ($in_network as $key => $trans) {
				$check_in_network_transaction = DB::table('statement_in_network_transactions')
				->where('transaction_id', $trans->transaction_id)
				->count( );
				if($check_in_network_transaction == 0) {
		          // insert
					$temp_transaction = array(
						'statement_id'      => $statement_id,
						'transaction_id'    => $trans->transaction_id
					);
					\CompanyStatementInNetworkTransaction::create($temp_transaction);
				}
			}
		}

	}

	public function getTotalCreditsInNetworkTransactions($statement_id, $plan)
	{
		$total_credits = 0;
		$in_network_transaction_array = [];
		$lite_plan = false;
		$in_network_transactions = 0;
		$total_consultation = 0;
	// $in_network_transactions = DB::table('statement_in_network_transactions')
	// 							->join('transaction_history', 'transaction_history.transaction_id', '=', 'statement_in_network_transactions.transaction_id')
	// 							->where('transaction_history.deleted', 0)
	// 							->where('statement_in_network_transactions.statement_id', $statement_id)
	// 							->sum('credit_cost');

		if($plan->account_type === "lite_plan" || $plan->account_type == "insurance_bundle" && $plan->secondary_account_type == "insurance_bundle_lite") {
			$lite_plan = true;
		}

		$in_network_transaction_temp = DB::table('statement_in_network_transactions')
		->where('statement_id', $statement_id)
		->get();

		foreach ($in_network_transaction_temp as $key => $in_network_temp) {
			array_push($in_network_transaction_array, $in_network_temp->transaction_id);
		}

		$statement_in_network_amount = 0;

		$transaction_details = [];
		if(sizeof($in_network_transaction_array) > 0) {
			$transactions = DB::table('transaction_history')
			->where('deleted', 0)
			->whereIn('transaction_id', $in_network_transaction_array)
			->orderBy('created_at', 'desc')
			->get();

      // in-network transactions
			foreach ($transactions as $key => $trans) {
				$mednefits_fee = 0;
				$treatment = 0;
				$consultation = 0;
				if($trans) {
					if($trans->deleted == 0 || $trans->deleted == "0") {
						$in_network_transactions += $trans->credit_cost;
						$receipt_images = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->get();
						$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
						$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
						$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
						$procedure_temp = "";

						if($lite_plan && $trans->lite_plan_enabled == 1) {
							if($trans->spending_type == 'medical') {
								$table_wallet_history = 'wallet_history';
							} else {
								$table_wallet_history = 'wellness_wallet_history';
							}
							if($lite_plan && $trans->lite_plan_enabled == 1) {
								$logs_lite_plan = DB::table($table_wallet_history)
								->where('logs', 'deducted_from_mobile_payment')
								->where('lite_plan_enabled', 1)
								->where('id', $trans->transaction_id)
								->first();

								if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
									$in_network_transactions += floatval($trans->co_paid_amount);
									$consultation_credits = true;
									$service_credits = true;
								} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
									$in_network_transactions += floatval($trans->co_paid_amount);
									$consultation_credits = true;
									$service_credits = true;
								} else if($trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 0 || $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "0"){
									$total_consultation += floatval($trans->co_paid_amount);
								}
							}
						}

              // clinic fees
						if($trans->co_paid_status == 0) {
							if(strrpos($trans->clinic_discount, '%')) {
								$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
								$clinicType = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();

						// check if procedure cost or credit cost has value
								if($trans->credit_cost != 0) {
									$fee = number_format((float)$trans->credit_cost / $trans->credit_divisor, 2);
								} else {
									$percentage = chop($trans->clinic_discount, '%');
									$discount = $percentage / 100;
									$sub = $trans->procedure_cost * $discount;
									$fee = number_format($sub, 2);
								}

							} else {
					      // return 'use whole number';
					      // $discount_clinic = str_replace('$', '', $transaction->clinic_discount);
					      // $discount = $discount_clinic;
					      // $final = $transaction->procedure_cost - $discount;
								$fee = number_format((float)$trans->co_paid_amount, 2);
							}
						} else if($trans->co_paid_status == 1){
						// $fee = $transaction->procedure_cost;
							$fee = $trans->co_paid_amount;
						}

						$mednefits_fee += $fee;

						if($trans->credit_cost > 0) {
							$mednefits_credits = number_format((float)$trans->credit_cost, 2);
							$cash = number_format(0, 2);
						} else {
							$mednefits_credits = number_format(0, 2);
							$cash = number_format((float)$trans->procedure_cost);
						}

              // get services
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
							}
							$clinic_name = ucwords($procedure);
						} else {
							$service_lists = DB::table('clinic_procedure')
							->where('ProcedureID', $trans->ProcedureID)
							->first();
							if($service_lists) {
								$procedure = ucwords($service_lists->Name);
								$clinic_name = ucwords($procedure);
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

						$total_amount = number_format($trans->credit_cost, 2);
						$treatment = number_format($trans->credit_cost, 2);
						if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
							$receipt_status = TRUE;
							$health_provider_status = TRUE;
							$payment_type = "Cash";

							if($lite_plan && $trans->lite_plan_enabled == 1 || $lite_plan && $trans->lite_plan_enabled == "1") {
								$total_amount = number_format($trans->co_paid_amount, 2);
								$treatment = 0;
								$consultation = number_format($trans->co_paid_amount, 2);
							}
						} else {
							$payment_type = "Mednefits Credits";
							$health_provider_status = FALSE;
							if($lite_plan && $trans->lite_plan_enabled == 1 || $lite_plan && $trans->lite_plan_enabled == 1) {
								$total_amount = number_format($trans->credit_cost + $trans->co_paid_amount, 2);
								$treatment = number_format($trans->credit_cost, 2);
								$consultation = number_format($trans->co_paid_amount, 2);
							}
						}

              // get clinic type
						$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
						$type = "";

              // check user if it is spouse or dependent
						if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
							$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
							$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
							$sub_account = ucwords($temp_account->Name);
							$sub_account_type = $temp_sub->user_type;
							$owner_id = $temp_sub->owner_id;
						} else {
							$sub_account = FALSE;
							$sub_account_type = FALSE;
							$owner_id = $customer->UserID;
						}


						$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);
						$format = array(
							'clinic_name'       => $clinic->Name,
							'amount'            => $total_amount,
							'clinic_type_and_service' => $clinic_name,
							'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->created_at)),
							'member'            => ucwords($customer->Name),
							'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
							'receipt_status'    => $receipt_status,
							'health_provider_status' => $health_provider_status,
							'user_id'           => $trans->UserID,
							'type'              => 'In-Network',
							'month'             => date('M', strtotime($trans->created_at)),
							'day'               => date('d', strtotime($trans->created_at)),
							'time'              => date('h:ia', strtotime($trans->created_at)),
							'clinic_type'       => $clinic_type->Name,
							'owner_account'     => $sub_account,
							'owner_id'          => $owner_id,
							'sub_account_user_type' => $sub_account_type,
							'co_paid'           => $trans->co_paid_amount,
							'payment_type'      => $payment_type,
							'nric'							=> $customer->NRIC,
							'mednefits_credits'			=> $mednefits_credits,
							'cash'									=> $cash,
							'mednefits_fee'					=> $fee,
							'treatment'			=> number_format($treatment, 2),
							'consultation'		=> number_format($consultation, 2)
						);

						array_push($transaction_details, $format);
					}

				}
			}

		}

		return array('credits' => $in_network_transactions, 'transactions' => $transaction_details, 'total_consultation' => $total_consultation);
	}


	public function benefitsInvoice( )
	{
		$input = Input::all();

		if(empty($input['token'])) {
			return View::make('errors.503');
		}

		$result = self::checkToken($input['token']);
		
		if(empty($input['invoice_id'])) {
			return View::make('errors.503');
		}
		$invoice = DB::table('corporate_invoice')->where('corporate_invoice_id', $input['invoice_id'])->first();

		if(!$invoice) {
			return View::make('errors.503');
		}

		$get_active_plan = DB::table('customer_active_plan')
		->where('customer_active_plan_id', $invoice->customer_active_plan_id)
		->first();

		$data = self::benefitsNoHeadCountInvoice($input['invoice_id']);
		// return $data;
		// return View::make('pdf-download.globalTemplates.plan-invoice', $data);
		$pdf = PDF::loadView('pdf-download.globalTemplates.plan-invoice', $data);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');
		return $pdf->stream();
		// return $pdf->download($data['invoice_number'].' - '.time().'.pdf');
	}

	public function getAddedHeadCountInvoice($id) 
	{
		$invoice = DB::table('corporate_invoice')->where('corporate_invoice_id', $id)->first();

		$active_plan = DB::table('customer_active_plan')
		->where('customer_active_plan_id', $invoice->customer_active_plan_id)
		->first();

		if((int)$active_plan->new_head_count != 1) {
			return array('status' => FALSE, 'message' => 'Plan data is not an added head count to current active plan.');
		}

		$first_plan = DB::table('customer_active_plan')->where('plan_id', $active_plan->plan_id)->first();
		$first_plan_invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $first_plan->customer_active_plan_id)->first();
		$plan = DB::table('customer_plan')->where('customer_plan_id', $active_plan->plan_id)->first();
		// get invoice data
		$invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->first();
		$data['currency_type'] = strtoupper($invoice->currency_type);
		$data['number_employess'] = $invoice->employees;
		$data['invoice_number'] = $invoice->invoice_number;
		$data['invoice_date'] = date('F d, Y', strtotime($invoice->invoice_date));
		$data['payment_due'] = date('F d, Y', strtotime($invoice->invoice_due));
		$data['invoice_due'] = $data['payment_due'];
		$data['employees'] = $invoice->employees;
		$data['start_date'] = date('F d, Y', strtotime($active_plan->plan_start));
		$data['plan_start'] = $data['start_date'];

		$calculated_prices_end_date = null;
		if((int)$active_plan->new_head_count == 0) {
			if($active_plan->duration || $active_plan->duration != "") {
				$end_plan_date = date('Y-m-d', strtotime('+'.$active_plan->duration, strtotime($plan->plan_start)));
			} else {
				$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
			}
			$calculated_prices_end_date = $end_plan_date;
			if((int)$invoice->override_total_amount_status == 1) {
				$calculated_prices = $invoice->override_total_amount;
			} else {
				$calculated_prices = $invoice->individual_price;
			}
		} else {
			// $first_plan = DB::table('customer_active_plan')->where('plan_id', $active_plan->plan_id)->first();
			$duration = null;
			$calculated_prices_end_date = PlanHelper::getCompanyPlanDates($active_plan->customer_start_buy_id);
			$end_plan_date = $calculated_prices_end_date['plan_end'];
			$calculated_prices_end_date = $calculated_prices_end_date['plan_end'];

			if((int)$invoice->override_total_amount_status == 1) {
				$calculated_prices = $invoice->override_total_amount;
			} else {
				$calculated_prices = PlanHelper::calculateInvoicePlanPrice($invoice->individual_price, $active_plan->plan_start, $calculated_prices_end_date);
			}
			$calculated_prices = \DecimalHelper::formatDecimal($calculated_prices);
			$duration = PlanHelper::getPlanDuration($active_plan->customer_start_buy_id, $active_plan->plan_start);
		}

		if($active_plan->account_type == 'stand_alone_plan') {
			$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
			$data['complimentary'] = FALSE;
			$data['account_type'] = "Pro Plan";
		} else if($active_plan->account_type == 'insurance_bundle') {
			$data['plan_type'] = " Bundled Mednefits Care (Corporate)";
			$data['complimentary'] = TRUE;
			$data['account_type'] = "Insurance Bundle";
		} else if($active_plan->account_type == 'trial_plan'){
			$data['plan_type'] = "Trial Account (Corporate)";
			$data['complimentary'] = FALSE;
			$data['account_type'] = "Trial Plan";
		} else if($active_plan->account_type == 'lite_plan') {
			$data['plan_type'] = "Basic Plan Mednefits Care (Corporate)";
			$data['account_type'] = "Basic Plan";
			$data['complimentary'] = FALSE;
		} else if($active_plan->account_type == "enterprise_plan") {
			$data['plan_type'] = "Enterprise Plan Mednefits Care (Corporate)";
			$data['account_type'] = "Enterprise Plan";
			$data['complimentary'] = FALSE;
		}

		$data['notes'] = null;
		$payment = DB::table('customer_cheque_logs')
		->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
		->orderBy('created_at', 'desc')
		->first();
		$data['paid'] = false;
		if($active_plan->paid == "true") {
			$data['paid'] = true;
			if($payment) {
				$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
				$data['notes']		  = $payment->remarks;
			}
		}


		// if($plan->account_type == "stand_alone_plan" || $plan->account_type === "stand_alone_plan") {
			// $calculated_prices = self::calculateInvoicePlanPrice($invoice->individual_price, $active_plan->plan_start, $calculated_prices_end_date);
		$data['price']          = number_format($calculated_prices, 2);
		$data['amount']					= number_format($invoice->employees * $calculated_prices, 2);
		$data['total']					= number_format($invoice->employees * $calculated_prices, 2);
		$amount_due     = $invoice->employees * $calculated_prices;
		// } else {
		// 	$data['price']          = number_format($invoice->individual_price, 2);
		// 	$data['amount']					= number_format($invoice->employees * $invoice->individual_price, 2);
		// 	$data['total']					= number_format($invoice->employees * $invoice->individual_price, 2);
		// 	$amount_due     = $invoice->employees * $invoice->individual_price;
		// }

		if($active_plan->paid === "true") {
			if($payment) {
				$temp_amount_due = $amount_due - $payment->paid_amount;
				if($temp_amount_due <= 0) {
					$data['amount_due']     = "0.00";
				} else {
					$data['amount_due'] = number_format($temp_amount_due, 2);
				}
			} else {
				$data['amount_due']     = number_format($amount_due, 2);
			}
		} else {
			$data['amount_due']     = number_format($amount_due, 2);
		}

		$data['plan_end'] 			= date('F d, Y', strtotime($end_plan_date));
		$data['same_as_invoice'] = $first_plan_invoice->invoice_number;
		$next_billing = date('F d, Y', strtotime('-1 month', strtotime($data['plan_end'])));
		$data['next_billing'] = date('F d, Y', strtotime('-1 day', strtotime($next_billing)));

		$contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $active_plan->customer_start_buy_id)->first();

		$business_info = DB::table('customer_business_information')->where('customer_buy_start_id', $active_plan->customer_start_buy_id)->first();


		$data['email'] = $contact->work_email;
		$data['phone']     = $contact->phone;
		$data['company'] = ucwords($business_info->company_name);
		$data['postal'] = $business_info->postal_code;

		if($contact->billing_status == "true" || $contact->billing_status == true) {
			$data['name'] = ucwords($contact->first_name).' '.ucwords($contact->last_name);
			$data['address'] = $business_info->company_address;
		} else {
			$billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $active_plan->customer_start_buy_id)->first();
			$data['name'] = ucwords($billing_contact->billing_name);
			$data['address'] = $billing_contact->billing_address;
		}

		$data['customer_active_plan_id'] = $active_plan->customer_active_plan_id;
		$data['dependents'] = [];
		return $data;
	}

	public function benefitsNoHeadCountInvoice($id)
	{
		$invoice = DB::table('corporate_invoice')->where('corporate_invoice_id', $id)->first();

		$get_active_plan = DB::table('customer_active_plan')
		->where('customer_active_plan_id', $invoice->customer_active_plan_id)
		->first();

		if(!$get_active_plan) {
			return array('status' => FALSE, 'message' => 'Active Plan does not exist');
		}

		$contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();
		$business_info = DB::table('customer_business_information')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();
		$data['building_name'] = $business_info->building_name;
		$data['unit_number'] = $business_info->unit_number;
		$data['email'] = $contact->work_email;
		$data['phone']     = $contact->phone;
		$data['company'] = ucwords($business_info->company_name);
		$data['postal'] = $business_info->postal_code;
		$data['currency_type'] = strtoupper($invoice->currency_type);
		if($contact->billing_status == "true" || $contact->billing_status == true) {
			$data['name'] = ucwords($contact->first_name).' '.ucwords($contact->last_name);
			$data['address'] = $business_info->company_address;
		} else {
			$billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();
			$data['name'] = ucwords($billing_contact->billing_name);
			$data['address'] = $billing_contact->billing_address;
		}

		$plan = DB::table('customer_plan')->where('customer_plan_id', $get_active_plan->plan_id)->first();
		$plan_start = $plan->plan_start;

		$account = DB::table('customer_buy_start')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();

		$data['complimentary'] = FALSE;
		$data['plan_type'] = "Standalone Mednefits Care (Corporate)";

		if($get_active_plan->account_type == "stand_alone_plan") {
			$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
			$data['account_type'] = "Pro Plan";
			$data['complimentary'] = FALSE;
		} else if($get_active_plan->account_type == "insurance_bundle") {
			$data['plan_type'] = "Bundled Mednefits Care (Corporate)";
			$data['account_type'] = "Insurance Bundle";
			$data['complimentary'] = TRUE;
		} else if($get_active_plan->account_type == "trial_plan") {
			$data['plan_type'] = "Trial Plan Mednefits Care (Corporate)";
			$data['account_type'] = "Trial Plan";
			$data['complimentary'] = FALSE;
		} else if($get_active_plan->account_type == "lite_plan") {
			$data['plan_type'] = "Basic Plan Mednefits Care (Corporate)";
			$data['account_type'] = "Basic Plan";
			$data['complimentary'] = FALSE;
		} else if($get_active_plan->account_type == "enterprise_plan") {
			$data['plan_type'] = "Enterprise Plan Mednefits Care (Corporate)";
			$data['account_type'] = "Enterprise Plan";
			$data['complimentary'] = FALSE;
		}

		$data['invoice_number'] = $invoice->invoice_number;
		$data['invoice_date']		= date('F d, Y', strtotime($invoice->invoice_date));
		$data['invoice_due']		= date('F d, Y', strtotime($invoice->invoice_due));
		$data['number_employess'] = $invoice->employees;
		$data['plan_start']     = date('F d, Y', strtotime($get_active_plan->plan_start));
		$data['notes']		  = null;
		$data['paid'] = false;
		if($get_active_plan->new_head_count == 0) {
			if((int)$invoice->plan_extention_enable == 1) {
				$extension = DB::table('plan_extensions')
				->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
				->first();
				if($extension) {
					$data['plan_start']     = date('F d, Y', strtotime($extension->plan_start));
					if($extension->duration || $extension->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$extension->duration, strtotime($extension->plan_start)));
						$data['duration'] = $extension->duration;
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($extension->plan_start)));
						$data['duration'] = '12 months';
					}
					$data['price']          = number_format($invoice->individual_price, 2);
					$data['amount']					= number_format($data['number_employess'] * $invoice->individual_price, 2);
					$amount_due = $data['number_employess'] * $invoice->individual_price;
					$data['total']					= $data['number_employess'] * $invoice->individual_price;

					if($extension->account_type == "stand_alone_plan") {
						$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
						$data['account_type'] = "Pro Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "insurance_bundle") {
						$data['plan_type'] = "Bundled Mednefits Care (Corporate)";
						$data['account_type'] = "Insurance Bundle";
						$data['complimentary'] = TRUE;
					} else if($extension->account_type == "trial_plan") {
						$data['plan_type'] = "Trial Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Trial Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "lite_plan") {
						$data['plan_type'] = "Basic Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Basic Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "enterprise_plan") {
						$data['plan_type'] = "Enterprise Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Enterprise Plan";
						$data['complimentary'] = FALSE;
					}

					$invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
					->where('plan_extention_enable', 1)
					->first();

					if((int)$extension->paid == 1) {
						$data['paid'] = true;
						$payment = DB::table('customer_cheque_logs')->where('invoice_id', $invoice->corporate_invoice_id)->first();
						if($payment) {
							if(empty($payment->date_received) || $payment->date_received == null) {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							} else {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							}
							$data['notes']		  = $payment->remarks;

							$temp_amount_due = $amount_due - $payment->paid_amount;
							if($temp_amount_due <= 0) {
								$data['amount_due']     = "0.00";
							} else {
								$data['amount_due'] = $temp_amount_due;
							}

						} else {
							$data['amount_due']     = $amount_due;
						}
					} else {
						$data['paid'] = false;
						$data['amount_due']     = $amount_due;
					}
				} else {
					if($get_active_plan->duration || $get_active_plan->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
						$data['duration'] = $get_active_plan->duration;
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
						$data['duration'] = '12 months';
					}
					$data['price']          = number_format($invoice->individual_price, 2);
					$data['amount']					= number_format($data['number_employess'] * $invoice->individual_price, 2);
					$amount_due = $data['number_employess'] * $invoice->individual_price;
					$data['total']					= $data['number_employess'] * $invoice->individual_price;

					$payment = DB::table('customer_cheque_logs')->where('invoice_id', $id)->first();

					if($get_active_plan->paid == "true") {
						$data['paid'] = true;
						if($payment) {
							if(empty($payment->date_received) || $payment->date_received == null) {
								$data['payment_date'] = date('F d, Y', strtotime($get_active_plan->paid_date));
							} else {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							}
							$data['notes']		  = $payment->remarks;
						}
					} else {
						$data['paid'] = false;
					}

					if($get_active_plan->paid == "true") {
						if($payment) {
							$temp_amount_due = $amount_due - $payment->paid_amount;
							if($temp_amount_due <= 0) {
								$data['amount_due']     = "0.00";
							} else {
								$data['amount_due'] = $temp_amount_due;
							}
						} else {
							$data['amount_due']     = $amount_due;
						}
					} else {
						$data['amount_due']     = $amount_due;
					}

					if($get_active_plan->duration || $get_active_plan->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
						$data['duration'] = $get_active_plan->duration;
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
						$data['duration'] = '12 months';
					}
				}
			} else {

				$data['price']          = number_format($invoice->individual_price, 2);
				$data['amount']					= number_format($data['number_employess'] * $invoice->individual_price, 2);
				$amount_due = $data['number_employess'] * $invoice->individual_price;
				$data['total']					= $data['number_employess'] * $invoice->individual_price;

				$payment = DB::table('customer_cheque_logs')->where('invoice_id', $id)->first();

				if($get_active_plan->paid == "true") {
					$data['paid'] = true;
					if($payment) {
						if(empty($payment->date_received) || $payment->date_received == null) {
							$data['payment_date'] = date('F d, Y', strtotime($get_active_plan->paid_date));
						} else {
							$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
						}
						$data['notes']		  = $payment->remarks;
					}
				} else {
					$data['paid'] = false;
				}

				if($get_active_plan->paid == "true") {
					if($payment) {
						$temp_amount_due = $amount_due - $payment->paid_amount;
						if($temp_amount_due <= 0) {
							$data['amount_due']     = "0.00";
						} else {
							$data['amount_due'] = $temp_amount_due;
						}
					} else {
						$data['amount_due']     = $amount_due;
					}
				} else {
					$data['amount_due']     = $amount_due;
				}

				if($get_active_plan->duration || $get_active_plan->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($get_active_plan->plan_start)));
					$data['duration'] = $get_active_plan->duration;
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($get_active_plan->plan_start)));
					$data['duration'] = '12 months';
				}
			}
		} else {
			if((int)$invoice->plan_extention_enable == 1) {
				$extension = DB::table('plan_extensions')
				->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
				->first();
				if($extension) {
					if($get_active_plan->duration || $get_active_plan->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
						$data['duration'] = $get_active_plan->duration;
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
						$data['duration'] = '12 months';
					}
					$data['price']          = number_format($invoice->individual_price, 2);
					$data['amount']					= number_format($data['number_employess'] * $invoice->individual_price, 2);
					$amount_due = $data['number_employess'] * $invoice->individual_price;
					$data['total']					= number_format($data['number_employess'] * $invoice->individual_price, 2);

					if($extension->account_type == "stand_alone_plan") {
						$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
						$data['account_type'] = "Pro Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "insurance_bundle") {
						$data['plan_type'] = "Bundled Mednefits Care (Corporate)";
						$data['account_type'] = "Insurance Bundle";
						$data['complimentary'] = TRUE;
					} else if($extension->account_type == "trial_plan") {
						$data['plan_type'] = "Trial Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Trial Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "lite_plan") {
						$data['plan_type'] = "Basic Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Basic Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "enterprise_plan") {
						$data['plan_type'] = "Enterprise Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Enterprise Plan";
						$data['complimentary'] = FALSE;
					}

					$invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
					->where('plan_extention_enable', 1)
					->first();

					if((int)$extension->paid == 1) {
						$data['paid'] = true;
						$payment = DB::table('customer_cheque_logs')->where('invoice_id', $invoice->corporate_invoice_id)->first();
						if($payment) {
							if(empty($payment->date_received) || $payment->date_received == null) {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							} else {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							}
							$data['notes']		  = $payment->remarks;

							$temp_amount_due = $amount_due - $payment->paid_amount;
							if($temp_amount_due <= 0) {
								$data['amount_due']     = "0.00";
							} else {
								$data['amount_due'] = number_format($temp_amount_due, 2);
							}

						} else {
							$data['amount_due']     = number_format($amount_due, 2);
						}
					} else {
						$data['paid'] = false;
						$data['amount_due']     = number_format($amount_due, 2);
					}

				} else {
					$first_plan = DB::table('customer_active_plan')->where('plan_id', $get_active_plan->plan_id)->first();
					$end_plan_date = date('Y-m-d', strtotime('+'.$first_plan->duration, strtotime($plan->plan_start)));
					$calculated_prices = self::calculateInvoicePlanPrice($invoice->individual_price, $get_active_plan->plan_start, $end_plan_date);
					$data['price']          = number_format($calculated_prices, 2);
					$amount_due = $data['number_employess'] * $calculated_prices;
					$data['amount']					= number_format($data['number_employess'] * $calculated_prices, 2);
					$data['total']					= number_format($data['number_employess'] * $calculated_prices, 2);
					$data['duration'] = $get_active_plan->duration;
				}
			} else {
				// $first_plan = DB::table('customer_active_plan')->where('plan_id', $active_plan->plan_id)->first();
				$duration = null;
				$calculated_prices_end_date = PlanHelper::getCompanyPlanDates($get_active_plan->customer_start_buy_id);
				$end_plan_date = $calculated_prices_end_date['plan_end'];
				$calculated_prices_end_date = $calculated_prices_end_date['plan_end'];

				if((int)$invoice->override_total_amount_status == 1) {
					$calculated_prices = $invoice->override_total_amount;
				} else {
					$calculated_prices = PlanHelper::calculateInvoicePlanPrice($invoice->individual_price, $get_active_plan->plan_start, $calculated_prices_end_date);
				}
				$calculated_prices = \DecimalHelper::formatDecimal($calculated_prices);
				$duration = PlanHelper::getPlanDuration($get_active_plan->customer_start_buy_id, $get_active_plan->plan_start);

				$data['price']          = number_format($calculated_prices, 2);
				$data['amount']					= number_format($invoice->employees * $calculated_prices, 2);
				$data['total']					= $invoice->employees * $calculated_prices;
				$amount_due     = $invoice->employees * $calculated_prices;

				$payment = DB::table('customer_cheque_logs')->where('invoice_id', $id)->first();

				if($get_active_plan->paid == "true") {
					$data['paid'] = true;
					if($payment) {
						if(empty($payment->date_received) || $payment->date_received == null) {
							$data['payment_date'] = date('F d, Y', strtotime($get_active_plan->paid_date));
						} else {
							$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
						}
						$data['notes']		  = $payment->remarks;
					}
				} else {
					$data['paid'] = false;
				}

				if($get_active_plan->paid == "true") {
					if($payment) {
						$temp_amount_due = $amount_due - $payment->paid_amount;
						if($temp_amount_due <= 0) {
							$data['amount_due']     = "0.00";
						} else {
							$data['amount_due'] = number_format($temp_amount_due, 2);
						}
					} else {
						$data['amount_due']     = number_format($amount_due, 2);
					}
				} else {
					$data['amount_due']     = number_format($amount_due, 2);
				}
			}

		}

		$data['customer_active_plan_id'] = $get_active_plan->customer_active_plan_id;
		$data['plan_end'] 			= date('F d, Y', strtotime('-1 day', strtotime($end_plan_date)));
		$data['customer_active_plan_id'] = $get_active_plan->customer_active_plan_id;

		$dependents_data = [];
	// check if active plan has a dependents associated for this
		$dependents = DB::table('dependent_plans')
		->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
		->where('tagged', 1)
		->get();
		$dependent_amount = 0;
		$dependent_amount_due = 0;

		foreach ($dependents as $key => $dependent) {
			$invoice_dependent = DB::table('dependent_invoice')
			->where('dependent_plan_id', $dependent->dependent_plan_id)
			->first();
			if($dependent->account_type == "stand_alone_plan") {
				$account_type = "Pro Plan";
			} else if($dependent->account_type == "insurance_bundle") {
				$account_type = "Insurance Bundle";
			} else if($dependent->account_type == "trial_plan") {
				$account_type = "Trial Plan";
			} else if($dependent->account_type == "lite_plan") {
				$account_type = "Basic Plan";
			} else if($dependent->account_type == "enterprise_plan") {
				$account_type = "Enterprise Plan";
			}

			if((int)$dependent->payment_status == 0) {
				$dependent_amount_due += $invoice_dependent->individual_price * $invoice_dependent->total_dependents;
			}

			$dependent_amount += $invoice_dependent->individual_price * $invoice_dependent->total_dependents;


			if($dependent->duration || $dependent->duration != "") {
				$end_date_temp = date('Y-m-d', strtotime('+'.$dependent->duration, strtotime($dependent->plan_start)));
				$end_date = date('F d, Y', strtotime('-1 day', strtotime($end_date_temp)));
				$duration = $dependent->duration;
			} else {
				$end_date = date('F d, Y', strtotime('+1 year', strtotime($dependent->plan_start)));
				$duration = '12 months';
			}

			$temp = array(
				'account_type'		=> $account_type,
				'total_dependents'	=> $invoice_dependent->total_dependents,
				'price'  => $invoice_dependent->individual_price,
				'amount'			=> number_format($invoice_dependent->individual_price * $invoice_dependent->total_dependents, 2),
				'plan_start'		=> date('F d, Y', strtotime($dependent->plan_start)),
				'plan_end'			=> $end_date,
				'duration'			=> $duration
			);

			array_push($dependents_data, $temp);
		}

		// return $dependents_data;
		$data['dependents'] = $dependents_data;
		$data['amount_due'] = number_format($data['amount_due'] + $dependent_amount_due, 2);
		$data['total'] = $data['amount_due'];
		// $data['total'] = number_format($data['total'] + $dependent_amount, 2);
		
		// return $data['amount_due'];
		$data['customer_active_plan_id'] = $get_active_plan->customer_active_plan_id;
		$data['plan_end'] 			= date('F d, Y', strtotime('-1 day', strtotime($end_plan_date)));

		return $data;
	}

	// {
	// 	$invoice = new CorporateInvoice();
	// 	$active_plan = new CorporateActivePlan();
	// 	$get_active_plan = $active_plan->getActivePlan($id);

	// 	if(!$get_active_plan) {
	// 		return View::make('errors.503');
	// 	}

	// 	// check if head count or not
	// 	if($get_active_plan->new_head_count == 0 || $get_active_plan->new_head_count == "0") {
	// 		$data = self::benefitsNoHeadCountInvoice($id);
	// 		// return $data;
	// 		// return View::make('pdf-download/hr-accounts-transaction', $data);
	// 		$pdf = PDF::loadView('pdf-download.hr-accounts-transaction', $data);
	// 	} else {
	// 		$data = self::getAddedHeadCountInvoice($id);
	// 		// return View::make('pdf-download/hr-accounts-transaction-new-head-count', $data);
	// 		$pdf = PDF::loadView('pdf-download.hr-accounts-transaction-new-head-count', $data);
	// 	}

	// 	$pdf->getDomPDF()->get_option('enable_html5_parser');
	// 	$pdf->setPaper('A4', 'portrait');
	// 	return $pdf->stream();
	// 	// return $pdf->download($data['invoice_number'].' - '.time().'.pdf');
	// }

	public function oldbenefitsNoHeadCountInvoice($id)
	{


		$invoice = new CorporateInvoice();
		$active_plan = new CorporateActivePlan();
		$get_active_plan = $active_plan->getActivePlan($id);

		// if(!$get_active_plan) {
		// 	return View::make('errors.503');
		// }

		$corporate_business_contact = new CorporateBusinessContact();

		$check = $invoice->checkCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);

		if($check == 0) {
			if($get_active_plan->cheque =="true" && $get_active_plan->credit == "false") {
				$due_date = date('Y-m-d', strtotime('-5 days', strtotime($get_active_plan->plan_start)));
			} else {
				$due_date = $get_active_plan->created_at;
			}
			$count = DB::table('corporate_invoice')->count();
			$check = 10;
			$invoice_number = str_pad($check + $count, 6, "0", STR_PAD_LEFT);
			$data_invoice = array(
				'customer_active_plan_id'	=> $get_active_plan->customer_active_plan_id,
				'invoice_number'					=> 'OMC'.$invoice_number,
				'invoice_date'						=> $get_active_plan->created_at,
				'invoice_due'							=> $due_date,
				'employees'								=> $get_active_plan->employees,
				'customer_id'							=> $get_active_plan->customer_start_buy_id,
				'invoice_type'						=> 'invoice'
			);
			$invoice->createCorporateInvoice($data_invoice);
		}

		$get_invoice = $invoice->getCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);
		$contact = $corporate_business_contact->getCorporateBusinessContact($get_active_plan->customer_start_buy_id);
		$corporate_business_info = new CorporateBusinessInformation();
		$business_info = $corporate_business_info->getCorporateBusinessInfo($get_active_plan->customer_start_buy_id);
		// return $get_invoice;
		$count_deleted_employees = DB::table('customer_plan_withdraw')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)->count();

		$data['email'] = $contact->work_email;
		if($contact['billing_status'] === "true" || $contact['billing_status'] === true) {
			$data['name'] = ucwords($contact->first_name).' '.ucwords($contact->last_name);
		} else {
			$billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();
			$data['name'] = ucwords($billing_contact->billing_name);
			$data['address'] = $billing_contact->billing_address;
		}
		$data['phone']     = $contact->phone;
		if($contact->billing_status === "true") {
			$data['address'] = $business_info->company_address;
			$data['postal'] = $business_info->postal_code;
			$data['company'] = ucwords($business_info->company_name);
		} else {
			$data['postal'] = $business_info->postal_code;
			$data['company'] = ucwords($business_info->company_name);
		}

		$payment = DB::table('customer_cheque_logs')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)->first();

		if($get_active_plan->paid === "true") {
			$data['paid'] = true;
			if($payment) {
				if(empty($payment->date_received) || $payment->date_received == null) {
					$data['payment_date'] = date('F d, Y', strtotime($get_active_plan->paid_date));
				} else {
					$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
				}
				$data['notes']		  = $payment->remarks;
			}
		} else {
			$data['paid'] = false;
		}

		$plan = DB::table('customer_plan')->where('customer_plan_id', $get_active_plan->plan_id)->first();
		// $get_plan = DB::table('customer_plan')->where('customer_plan_id', $get_active_plan->plan_id)->first();
		$plan_start = $plan->plan_start;

		$account = DB::table('customer_buy_start')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();

		if($get_active_plan->account_type == 'stand_alone_plan') {
			$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
			$data['complimentary'] = FALSE;
			$data['account_type'] = "Pro Plan";
		} else if($get_active_plan->account_type == 'insurance_bundle') {
			$data['plan_type'] = " Bundled Mednefits Care (Corporate)";
			$data['complimentary'] = TRUE;
			$data['account_type'] = "Insurance Bundle";
		} else if($get_active_plan->account_type == 'trial_plan'){
			$data['plan_type'] = "Trial Account (Corporate)";
			$data['complimentary'] = FALSE;
			$data['account_type'] = "Trial Plan";
		} else if($get_active_plan->account_type == 'lite_plan') {
			$data['plan_type'] = "Lite Plan Mednefits Care (Corporate)";
			$data['account_type'] = "Lite Plan";
			$data['complimentary'] = FALSE;
		}

		self::checkPaymentHistory($id);


		if((int)$get_active_plan->new_head_count == 0) {
			if($get_active_plan->duration || $get_active_plan->duration != "") {
				$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($get_active_plan->plan_start)));
				$data['duration'] = $get_active_plan->duration;
			} else {
				$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
				$data['duration'] = '+1 year';
			}
			$data['price']          = number_format($get_invoice->individual_price, 2);
			$data['amount']					= number_format($get_invoice->employees * $get_invoice->individual_price, 2);
			$data['total']					= number_format($get_invoice->employees * $get_invoice->individual_price, 2);
			$amount_due     = $get_invoice->employees * $get_invoice->individual_price;
		} else {
			$first_plan = DB::table('customer_active_plan')->where('plan_id', $get_active_plan->plan_id)->first();
			$end_plan_date = date('Y-m-d', strtotime('+'.$first_plan->duration, strtotime($plan->plan_start)));
			$calculated_prices = self::calculateInvoicePlanPrice( $get_invoice->individual_price, $plan->plan_start, $end_plan_date);
			$data['price']          = number_format($calculated_prices, 2);
			$data['amount']					= number_format($get_invoice->employees * $calculated_prices, 2);
			$data['total']					= number_format($get_invoice->employees * $calculated_prices, 2);
			$amount_due     = $get_invoice->employees * $calculated_prices;
			$data['duration'] = $get_active_plan->duration;
		}

		if($get_active_plan->paid === "true") {
			if($payment) {
				$temp_amount_due = $amount_due - $payment->paid_amount;
				if($temp_amount_due <= 0) {
					$data['amount_due']     = "0.00";
				} else {
					$data['amount_due'] = number_format($temp_amount_due, 2);
				}
			} else {
				$data['amount_due']     = number_format($amount_due, 2);
			}
		} else {
			$data['amount_due']     = number_format($amount_due, 2);
		}

		$data['invoice_number'] = $get_invoice->invoice_number;
		$data['invoice_date']		= $get_invoice->invoice_date;
		$data['invoice_due']		= $get_invoice->invoice_due;
		$data['number_employess'] = $get_invoice->employees;
		$data['plan_start']     = $plan->plan_start;
		$data['plan_end'] 			= date('Y-m-d', strtotime('-1 day', strtotime($end_plan_date)));

		$data['notes'] = DB::table('customer_cheque_logs')->where('customer_active_plan_id', $id)->first();
		$data['customer_active_plan_id'] = $id;

		return $data;
		// return View::make('invoice.purchase-plan-invoice', $data);
	}

	public function getoldAddedHeadCountInvoice($id)
	{
		$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $id)->first();

		if($active_plan->new_head_count != "1" || $active_plan->new_head_count != 1) {
			return array('status' => FALSE, 'message' => 'Plan data is not an added head count to current active plan.');
		}

		$first_plan = DB::table('customer_active_plan')->where('plan_id', $active_plan->plan_id)->first();
		$first_plan_invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $first_plan->customer_active_plan_id)->first();
		$plan = DB::table('customer_plan')->where('customer_plan_id', $active_plan->plan_id)->first();
		// get invoice data
		$invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->first();

		// $count_deleted_employees = DB::table('customer_plan_withdraw')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->count();
		$data['number_employess'] = $invoice->employees;
		$data['invoice_number'] = $invoice->invoice_number;
		$data['invoice_date'] = date('M d Y', strtotime($invoice->invoice_date));
		$data['payment_due'] = date('M d Y', strtotime($invoice->invoice_due));
		$data['employees'] = $invoice->employees;
		$data['start_date'] = date('M d Y', strtotime($active_plan->plan_start));

		if($first_plan->duration || $first_plan->duration != "") {
			$end_plan_date = date('Y-m-d', strtotime('+'.$first_plan->duration, strtotime($plan->plan_start)));
		} else {
			$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
		}

		if($active_plan->account_type == 'stand_alone_plan') {
			$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
			$data['complimentary'] = FALSE;
			$data['account_type'] = "Pro Plan";
		} else if($active_plan->account_type == 'insurance_bundle') {
			$data['plan_type'] = " Bundled Mednefits Care (Corporate)";
			$data['complimentary'] = TRUE;
			$data['account_type'] = "Insurance Bundle";
		} else if($active_plan->account_type == 'trial_plan'){
			$data['plan_type'] = "Trial Account (Corporate)";
			$data['complimentary'] = FALSE;
			$data['account_type'] = "Trial Plan";
		} else if($active_plan->account_type == 'lite_plan') {
			$data['plan_type'] = "Lite Plan Mednefits Care (Corporate)";
			$data['account_type'] = "Lite Plan";
			$data['complimentary'] = FALSE;
		}

		$payment = DB::table('customer_cheque_logs')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->first();

		if($active_plan->paid == "true") {
			$data['paid'] = true;
			if($payment) {
				$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
				$data['notes']		  = $payment->remarks;
			}
		} else {
			$data['paid'] = false;
		}


		if($plan->account_type == "stand_alone_plan" || $plan->account_type === "stand_alone_plan") {
			$calculated_prices = self::calculateInvoicePlanPrice( $invoice->individual_price, $active_plan->plan_start, $end_plan_date);
			$data['price']          = number_format($calculated_prices, 2);
			$data['amount']					= number_format($invoice->employees * $calculated_prices, 2);
			$data['total']					= number_format($invoice->employees * $calculated_prices, 2);
			$amount_due     = $invoice->employees * $calculated_prices;
		} else {
			$data['price']          = number_format($invoice->individual_price, 2);
			$data['amount']					= number_format($invoice->employees * $invoice->individual_price, 2);
			$data['total']					= number_format($invoice->employees * $invoice->individual_price, 2);
			$amount_due     = $invoice->employees * $invoice->individual_price;
		}

		if($active_plan->paid === "true") {
			if($payment) {
				$temp_amount_due = $amount_due - $payment->paid_amount;
				if($temp_amount_due <= 0) {
					$data['amount_due']     = "0.00";
				} else {
					$data['amount_due'] = number_format($temp_amount_due, 2);
				}
			} else {
				$data['amount_due']     = number_format($amount_due, 2);
			}
		} else {
			$data['amount_due']     = number_format($amount_due, 2);
		}

		$data['plan_end'] 			= date('M d Y', strtotime('-1 day', strtotime($end_plan_date)));
		$data['same_as_invoice'] = $first_plan_invoice->invoice_number;
		$next_billing = date('M d Y', strtotime('-1 month', strtotime($data['plan_end'])));
		$data['next_billing'] = date('M d Y', strtotime('-1 day', strtotime($next_billing)));


		// billing contact
		$corporate_business_contact = new CorporateBusinessContact();
		$contact = $corporate_business_contact->getCorporateBusinessContact($active_plan->customer_start_buy_id);

		$corporate_business_info = new CorporateBusinessInformation();
		$business_info = $corporate_business_info->getCorporateBusinessInfo($active_plan->customer_start_buy_id);

		if($contact->billing_contact == "false" || $contact->billing_contact == false) {
			$corporate_billing_contact = new CorporateBillingContact();
			$result_corporate_billing_contact = $corporate_billing_contact->getCorporateBillingContact($active_plan->customer_start_buy_id);
			$data['first_name'] = ucwords($result_corporate_billing_contact->first_name);
			$data['last_name']	= ucwords($result_corporate_billing_contact->last_name);
			$data['email']			= $result_corporate_billing_contact->work_email;
			$data['billing_contact_status'] = false;
		} else {
			$data['first_name'] = ucwords($contact->first_name);
			$data['last_name']	= ucwords($contact->last_name);
			$data['email']			= $contact->work_email;
			$data['billing_contact_status'] = true;
			$data['phone']     = $contact->phone;
		}

		if($contact->billing_address == "true") {
			$corporate_billing_address = new CorporateBillingAddress();
			$billing_address = $corporate_billing_address->getCorporateBillingAddress($active_plan->customer_start_buy_id);
			$data['address'] = $billing_address->billing_address;
			$data['postal'] = $billing_address->postal_code;
			$data['company'] = ucwords($business_info->company_name);
			$data['billing_address_status'] = true;
		} else {
			$data['address'] = $business_info->company_address;
			$data['postal'] = $business_info->postal_code;
			$data['company'] = ucwords($business_info->company_name);
			$data['billing_address_status'] = false;
		}

		// $data['notes'] = DB::table('customer_cheque_logs')->where('customer_active_plan_id', $id)->first();
		$data['customer_active_plan_id'] = $id;

		return $data;
	}

	public function formatInvoiceNumber($id)
	{
		$invoice = DB::table('corporate_invoice')->where('corporate_invoice_id', $id)->first();
		$first_invoice = DB::table('corporate_invoice')->where('customer_id', $invoice->customer_id)->get();

		// if($first_invoice->corporate_invoice_id == $id) {
		// 	return "first".$id;
		// } else {
		// 	return "not first".$id;
		// }
		return $first_invoice;
	}

	public function checkPaymentHistory($id)
	{
		$payment_history = DB::table('customer_payment_history')->where('customer_active_plan_id', $id)->first();

		if(!$payment_history) {
            // create payment history
			$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $id)->first();
			$plan = DB::table('customer_plan')->where('customer_plan_id', $active_plan->plan_id)->first();

			$first_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();

			if($first_plan->customer_active_plan_id == $id) {
				$status = 'started';
			} else {
				$status = 'added';
			}
			$employees = DB::table('customer_plan_withdraw')->where('customer_active_plan_id', $id)->count();

			$number_of_empoyees = $active_plan->employees + $employees;

			$data = array(
				'customer_buy_start_id'     => $active_plan->customer_start_buy_id,
				'customer_active_plan_id'   => $active_plan->customer_active_plan_id,
				'plan_start'                => $active_plan->plan_start,
				'status'                    => $status,
				'employees'                 => $number_of_empoyees,
				'amount'                    => $number_of_empoyees * 99,
				'stripe_transaction_id'     => Null
			);

			return \CorporatePaymentHistory::create($data);
		}
	}

	public function validateDate($date)
	{
		$d = \DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') === $date;
	}

	public function benefitsReceipt( )
	{
		$result = self::checkSession();
		$input = Input::all();
  	// return $input['invoice_id'];
		if(empty($input['invoice_id']) || $input['invoice_id'] == null) {
			return View::make('errors.503');
		}

		$id = $input['invoice_id'];

		$invoice = CorporateInvoice::where('corporate_invoice_id', $id)->first();

		if(!$invoice) {
			return View::make('errors.503');
		}

		$get_active_plan = DB::table('customer_active_plan')
		->where('customer_active_plan_id', $invoice->customer_active_plan_id)
		->first();

		if(!$get_active_plan) {
			return View::make('errors.503');
		}

		$contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();

		$business_info = DB::table('customer_business_information')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();


		$data['email'] = $contact->work_email;
		$data['phone']     = $contact->phone;

		if($contact->billing_status === "true" || $contact->billing_status === true) {
			$data['name'] = ucwords($contact->first_name).' '.ucwords($contact->last_name);
		} else {
			$billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();
			$data['name'] = ucwords($billing_contact->billing_name);
			$data['address'] = $billing_contact->billing_address;
		}

		if($contact->billing_status === "true") {
			$data['address'] = $business_info->company_address;
			$data['postal'] = $business_info->postal_code;
			$data['company'] = ucwords($business_info->company_name);
		} else {
			$data['postal'] = $business_info->postal_code;
			$data['company'] = ucwords($business_info->company_name);
		}


		$plan = DB::table('customer_plan')->where('customer_plan_id', $get_active_plan->plan_id)->first();
		$plan_start = $plan->plan_start;

		$account = DB::table('customer_buy_start')->where('customer_buy_start_id', $get_active_plan->customer_start_buy_id)->first();

		$data['complimentary'] = FALSE;
		$data['plan_type'] = "Standalone Mednefits Care (Corporate)";

		if($get_active_plan->account_type == "stand_alone_plan") {
			$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
			$data['account_type'] = "Pro Plan";
			$data['complimentary'] = FALSE;
		} else if($get_active_plan->account_type == "insurance_bundle") {
			$data['plan_type'] = "Bundled Mednefits Care (Corporate)";
			$data['account_type'] = "Insurance Bundle";
			$data['complimentary'] = TRUE;
		} else if($get_active_plan->account_type == "trial_plan") {
			$data['plan_type'] = "Trial Plan Mednefits Care (Corporate)";
			$data['account_type'] = "Trial Plan";
			$data['complimentary'] = FALSE;
		} else if($get_active_plan->account_type == "lite_plan") {
			$data['plan_type'] = "Lite Plan Mednefits Care (Corporate)";
			$data['account_type'] = "Lite Plan";
			$data['complimentary'] = FALSE;
		}

		$data['invoice_number'] = $invoice->invoice_number;
		$data['invoice_date']		= date('F d, Y', strtotime($invoice->invoice_date));
		$data['invoice_due']		= date('F d, Y', strtotime($invoice->invoice_due));
		$data['number_employess'] = $invoice->employees;
		$data['plan_start']     = date('F d, Y', strtotime($get_active_plan->plan_start));

		if($get_active_plan->new_head_count == 0) {
			if((int)$invoice->plan_extention_enable == 1) {
				$extension = DB::table('plan_extensions')
				->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
				->first();
				if($extension) {
					if($extension->duration || $extension->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$extension->duration, strtotime($extension->plan_start)));
						$data['duration'] = $extension->duration;
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
						$data['duration'] = '12 months';
					}
					$data['price']          = number_format($invoice->individual_price, 2);
					$data['amount']					= number_format($data['number_employess'] * $invoice->individual_price, 2);
					$amount_due = $data['number_employess'] * $invoice->individual_price;
					$data['total']					= number_format($data['number_employess'] * $invoice->individual_price, 2);

					if($extension->account_type == "stand_alone_plan") {
						$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
						$data['account_type'] = "Pro Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "insurance_bundle") {
						$data['plan_type'] = "Bundled Mednefits Care (Corporate)";
						$data['account_type'] = "Insurance Bundle";
						$data['complimentary'] = TRUE;
					} else if($extension->account_type == "trial_plan") {
						$data['plan_type'] = "Trial Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Trial Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "lite_plan") {
						$data['plan_type'] = "Lite Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Lite Plan";
						$data['complimentary'] = FALSE;
					}

					$invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
					->where('plan_extention_enable', 1)
					->first();

					if((int)$extension->paid == 1) {
						$data['paid'] = true;
						$payment = DB::table('customer_cheque_logs')->where('invoice_id', $invoice->corporate_invoice_id)->first();
						if($payment) {
							if(empty($payment->date_received) || $payment->date_received == null) {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							} else {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							}
							$data['notes']		  = $payment->remarks;

							$temp_amount_due = $amount_due - $payment->paid_amount;
							if($temp_amount_due <= 0) {
								$data['amount_due']     = "0.00";
							} else {
								$data['amount_due'] = number_format($temp_amount_due, 2);
							}

						} else {
							$data['amount_due']     = number_format($amount_due, 2);
						}
					} else {
						$data['paid'] = false;
						$data['amount_due']     = number_format($amount_due, 2);
					}
				} else {
					if($get_active_plan->duration || $get_active_plan->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
						$data['duration'] = $get_active_plan->duration;
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
						$data['duration'] = '12 months';
					}
					$data['price']          = number_format($invoice->individual_price, 2);
					$data['amount']					= number_format($data['number_employess'] * $invoice->individual_price, 2);
					$amount_due = $data['number_employess'] * $invoice->individual_price;
					$data['total']					= number_format($data['number_employess'] * $invoice->individual_price, 2);

					$payment = DB::table('customer_cheque_logs')->where('invoice_id', $id)->first();

					if($get_active_plan->paid == "true") {
						$data['paid'] = true;
						if($payment) {
							if(empty($payment->date_received) || $payment->date_received == null) {
								$data['payment_date'] = date('F d, Y', strtotime($get_active_plan->paid_date));
							} else {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							}
							$data['notes']		  = $payment->remarks;
						}
					} else {
						$data['paid'] = false;
					}

					if($get_active_plan->paid == "true") {
						if($payment) {
							$temp_amount_due = $amount_due - $payment->paid_amount;
							if($temp_amount_due <= 0) {
								$data['amount_due']     = "0.00";
							} else {
								$data['amount_due'] = number_format($temp_amount_due, 2);
							}
						} else {
							$data['amount_due']     = number_format($amount_due, 2);
						}
					} else {
						$data['amount_due']     = number_format($amount_due, 2);
					}

					if($get_active_plan->duration || $get_active_plan->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
						$data['duration'] = $get_active_plan->duration;
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
						$data['duration'] = '12 months';
					}
				}
			} else {

				$data['price']          = number_format($invoice->individual_price, 2);
				$data['amount']					= number_format($data['number_employess'] * $invoice->individual_price, 2);
				$amount_due = $data['number_employess'] * $invoice->individual_price;
				$data['total']					= number_format($data['number_employess'] * $invoice->individual_price, 2);

				$payment = DB::table('customer_cheque_logs')->where('invoice_id', $id)->first();

				// if($get_active_plan->paid == "true") {
				// 	$data['paid'] = true;
				// 	if($payment) {
				// 		if(empty($payment->date_received) || $payment->date_received == null) {
				// 			$data['payment_date'] = date('F d, Y', strtotime($get_active_plan->paid_date));
				// 		} else {
				// 			$data['amount_due']     = number_format($amount_due, 2);
				// 		}
				// 	} else {
				// 		$data['paid'] = false;
				// 		$data['amount_due']     = number_format($amount_due, 2);
				// 	}
				// } else {
				if($get_active_plan->duration || $get_active_plan->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
					$data['duration'] = $get_active_plan->duration;
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
					$data['duration'] = '12 months';
				}
				$data['price']          = number_format($invoice->individual_price, 2);
				$data['amount']					= number_format($data['number_employess'] * $invoice->individual_price, 2);
				$amount_due = $data['number_employess'] * $invoice->individual_price;
				$data['total']					= number_format($data['number_employess'] * $invoice->individual_price, 2);

				$payment = DB::table('customer_cheque_logs')->where('invoice_id', $id)->first();

				if($get_active_plan->paid == "true") {
					$data['paid'] = true;
					if($payment) {
						if(empty($payment->date_received) || $payment->date_received == null) {
							$data['payment_date'] = date('F d, Y', strtotime($get_active_plan->paid_date));
						} else {
							$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
						}
						$data['notes']		  = $payment->remarks;
					}
				} else {
					$data['paid'] = false;
				}

				if($get_active_plan->paid == "true") {
					if($payment) {
						$temp_amount_due = $amount_due - $payment->paid_amount;
						if($temp_amount_due <= 0) {
							$data['amount_due']     = "0.00";
						} else {
							$data['amount_due'] = number_format($temp_amount_due, 2);
						}
					} else {
						$data['amount_due']     = number_format($amount_due, 2);
					}
				} else {
					$data['amount_due']     = number_format($amount_due, 2);
				}

				if($get_active_plan->duration || $get_active_plan->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
					$data['duration'] = $get_active_plan->duration;
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
					$data['duration'] = '12 months';
				}
				// }
			}
		} else {
			if((int)$invoice->plan_extention_enable == 1) {
				$extension = DB::table('plan_extensions')
				->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
				->first();
				if($extension) {
					if($get_active_plan->duration || $get_active_plan->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
						$data['duration'] = $get_active_plan->duration;
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
						$data['duration'] = '12 months';
					}
					$data['price']          = number_format($invoice->individual_price, 2);
					$data['amount']					= number_format($data['number_employess'] * $invoice->individual_price, 2);
					$amount_due = $data['number_employess'] * $invoice->individual_price;
					$data['total']					= number_format($data['number_employess'] * $invoice->individual_price, 2);

					if($extension->account_type == "stand_alone_plan") {
						$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
						$data['account_type'] = "Pro Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "insurance_bundle") {
						$data['plan_type'] = "Bundled Mednefits Care (Corporate)";
						$data['account_type'] = "Insurance Bundle";
						$data['complimentary'] = TRUE;
					} else if($extension->account_type == "trial_plan") {
						$data['plan_type'] = "Trial Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Trial Plan";
						$data['complimentary'] = FALSE;
					} else if($extension->account_type == "lite_plan") {
						$data['plan_type'] = "Lite Plan Mednefits Care (Corporate)";
						$data['account_type'] = "Lite Plan";
						$data['complimentary'] = FALSE;
					}

					$invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)
					->where('plan_extention_enable', 1)
					->first();

					if((int)$extension->paid == 1) {
						$data['paid'] = true;
						$payment = DB::table('customer_cheque_logs')->where('invoice_id', $invoice->corporate_invoice_id)->first();
						if($payment) {
							if(empty($payment->date_received) || $payment->date_received == null) {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							} else {
								$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
							}
							$data['notes']		  = $payment->remarks;

							$temp_amount_due = $amount_due - $payment->paid_amount;
							if($temp_amount_due <= 0) {
								$data['amount_due']     = "0.00";
							} else {
								$data['amount_due'] = number_format($temp_amount_due, 2);
							}

						} else {
							$data['amount_due']     = number_format($amount_due, 2);
						}
					} else {
						$data['paid'] = false;
						$data['amount_due']     = number_format($amount_due, 2);
					}

				} else {
					$first_plan = DB::table('customer_active_plan')->where('plan_id', $get_active_plan->plan_id)->first();
					$end_plan_date = date('Y-m-d', strtotime('+'.$first_plan->duration, strtotime($plan->plan_start)));
					$calculated_prices = self::calculateInvoicePlanPrice($invoice->individual_price, $get_active_plan->plan_start, $end_plan_date);
					$data['price']          = number_format($calculated_prices, 2);
					$amount_due = $data['number_employess'] * $calculated_prices;
					$data['amount']					= number_format($data['number_employess'] * $calculated_prices, 2);
					$data['total']					= number_format($data['number_employess'] * $calculated_prices, 2);
					$data['duration'] = $get_active_plan->duration;
				}
			} else {

				$payment = DB::table('customer_cheque_logs')->where('invoice_id', $id)->first();

				if($get_active_plan->paid == "true") {
					$data['paid'] = true;
					if($payment) {
						if(empty($payment->date_received) || $payment->date_received == null) {
							$data['payment_date'] = date('F d, Y', strtotime($get_active_plan->paid_date));
						} else {
							$data['payment_date'] = date('F d, Y', strtotime($payment->date_received));
						}
						$data['notes']		  = $payment->remarks;
					}
				} else {
					$data['paid'] = false;
				}


				$first_plan = DB::table('customer_active_plan')->where('plan_id', $get_active_plan->plan_id)->first();
				$end_plan_date = date('Y-m-d', strtotime('+'.$first_plan->duration, strtotime($plan->plan_start)));
				$calculated_prices = self::calculateInvoicePlanPrice($invoice->individual_price, $get_active_plan->plan_start, $end_plan_date);
				$data['price']          = number_format($calculated_prices, 2);
				$amount_due = $data['number_employess'] * $calculated_prices;
				$data['amount']					= number_format($data['number_employess'] * $calculated_prices, 2);
				$data['total']					= number_format($data['number_employess'] * $calculated_prices, 2);
				$data['duration'] = $get_active_plan->duration;

				if($get_active_plan->paid == "true") {
					if($payment) {
						$temp_amount_due = $amount_due - $payment->paid_amount;
						if($temp_amount_due <= 0) {
							$data['amount_due']     = "0.00";
						} else {
							$data['amount_due'] = number_format($temp_amount_due, 2);
						}
					} else {
						$data['amount_due']     = number_format($amount_due, 2);
					}
				} else {
					$data['amount_due']     = number_format($amount_due, 2);
				}
			}

		}

		$data['customer_active_plan_id'] = $get_active_plan->customer_active_plan_id;
		$data['plan_end'] 			= date('F d, Y', strtotime('-1 day', strtotime($end_plan_date)));

		if($get_active_plan->cheque == "true" && $get_active_plan->credit == "false") {
			if($get_active_plan->paid_cheque == "true" || $get_active_plan->paid == "true") {
				$data['paid'] = true;
				$payment = DB::table('customer_cheque_logs')->where('invoice_id', $id)->first();
				if($payment) {
					$data['paid_date'] = date('F d, Y', strtotime($payment->date_received));
					$data['payment_remarks'] = $payment->remarks;
				}
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'Not yet paid.'
				);
			}

			$data['payment_method'] = "CHEQUE";
		} else if($get_active_plan->cheque == "false" && $get_active_plan->credit == "true") {
			

			$first_plan = DB::table('customer_active_plan')->where('plan_id', $get_active_plan->plan_id)->first();
			$end_plan_date = date('Y-m-d', strtotime('+'.$first_plan->duration, strtotime($plan->plan_start)));

			$calculated_prices_end_date = null;
			if((int)$get_active_plan->new_head_count == 0) {
				if($get_active_plan->duration || $get_active_plan->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
				}
				$calculated_prices_end_date = $end_plan_date;
			} else {
				$first_plan = DB::table('customer_active_plan')->where('plan_id', $get_active_plan->plan_id)->first();

				$duration = null;
				if((int)$first_plan->plan_extention_enable == 1) {
					$plan_extension = DB::table('plan_extensions')
					->where('customer_active_plan_id', $first_plan->customer_active_plan_id)
					->first();
					$duration = $plan_extension->duration;
				} else {
					$duration = $first_plan->duration;
				}

				$end_plan_date = date('Y-m-d', strtotime('+'.$duration, strtotime($plan->plan_start)));


				if($get_active_plan->account_type != "trial_plan") {
					$calculated_prices_end_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($plan->plan_start)));
				} else {
					$calculated_prices_end_date = $end_plan_date;
				}
			}

			$calculated_prices = self::calculateInvoicePlanPrice($invoice->individual_price, $get_active_plan->plan_start, $calculated_prices_end_date);
			$data['price']          = number_format($calculated_prices, 2);
			$amount_due = $data['number_employess'] * $calculated_prices;
			$data['amount']					= number_format($data['number_employess'] * $calculated_prices, 2);
			$data['total']					= number_format($data['number_employess'] * $calculated_prices, 2);
			$data['duration'] = $get_active_plan->duration;

			if($get_active_plan->paid == "true") {
				$data['paid'] = true;
				$payment = DB::table('customer_cheque_logs')->where('invoice_id', $id)->first();
				if($payment) {
					$data['paid_date'] = $payment->date_received;
					$data['payment_remarks'] = $payment->remarks;
				}
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'Not yet paid.'
				);
			}

			$data['payment_method'] = "CHEQUE";
		} else if($get_active_plan->cheque == "false" && $get_active_plan->credit == "true") {
			if($get_active_plan->paid == "true") {
				$data['paid'] = true;
				$payment = DB::table('customer_cheque_logs')->where('invoice_id', $id)->first();
				if($payment) {
					$data['paid_date'] = $payment->date_received;
					$data['payment_remarks'] = $payment->remarks;
				}
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'Not yet paid.'
				);
			}
			$data['payment_method'] = "CREDIT CARD";
		}

		$data['paid_amount'] = $data['amount'];

	// return View::make('pdf-download.9-10-18.benefits-spending-account-payment-receipt', $data);
		$pdf = PDF::loadView('pdf-download.9-10-18.benefits-spending-account-payment-receipt', $data);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');
		return $pdf->download($data['invoice_number'].' Receipt - '.time().'.pdf');
	}

	public function oldbenefitsReceipt($id)
	{

		$corporate_business_contact = new CorporateBusinessContact();
		$invoice = new CorporateInvoice();
		$active_plan = new CorporateActivePlan();

		$get_active_plan = $active_plan->getActivePlan($id);

		if(!$get_active_plan) {
			return View::make('errors.503');
		}

		$result = self::checkSession();
		$check = $invoice->checkCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);

		if($check == 0) {
			$count = DB::table('corporate_invoice')->count();
			$check = 10;
			$invoice_number = str_pad($check + $count, 6, "0", STR_PAD_LEFT);
			$data_invoice = array(
				'customer_active_plan_id'	=> $get_active_plan->customer_active_plan_id,
				'invoice_number'					=> 'OMC'.$invoice_number,
				'invoice_date'						=> $get_active_plan->created_at,
				'invoice_due'							=> $get_active_plan->created_at,
				'employees'								=> $get_active_plan->employees,
				'customer_id'							=> $result->customer_buy_start_id,
				'invoice_type'						=> 'invoice'
			);
			$invoice->createCorporateInvoice($data_invoice);
		}

		$get_invoice = $invoice->getCorporateInvoiceActivePlan($get_active_plan->customer_active_plan_id);
		$contact = $corporate_business_contact->getCorporateBusinessContact($get_active_plan->customer_start_buy_id);
		$corporate_business_info = new CorporateBusinessInformation();
		$business_info = $corporate_business_info->getCorporateBusinessInfo($get_active_plan->customer_start_buy_id);

		if($contact->billing_contact == "false" || $contact->billing_contact == false) {
			$corporate_billing_contact = new CorporateBillingContact();
			$result_corporate_billing_contact = $corporate_billing_contact->getCorporateBillingContact($get_active_plan->customer_start_buy_id);
			$data['first_name'] = ucwords($result_corporate_billing_contact->first_name);
			$data['last_name']	= ucwords($result_corporate_billing_contact->last_name);
			$data['email']			= $result_corporate_billing_contact->work_email;
			$data['billing_contact_status'] = false;
		} else {
			$data['first_name'] = ucwords($contact->first_name);
			$data['last_name']	= ucwords($contact->last_name);
			$data['email']			= $contact->work_email;
			$data['billing_contact_status'] = true;
			$data['phone']     = $contact->phone;
		}

		if($contact->billing_address == "true") {
			$corporate_billing_address = new CorporateBillingAddress();
			$billing_address = $corporate_billing_address->getCorporateBillingAddress($get_active_plan->customer_start_buy_id);
			$data['address'] = $billing_address->billing_address;
			$data['postal'] = $billing_address->postal_code;
			$data['company'] = ucwords($business_info->company_name);
			$data['billing_address_status'] = true;
		} else {
			$data['address'] = $business_info->company_address;
			$data['postal'] = $business_info->postal_code;
			$data['company'] = ucwords($business_info->company_name);
			$data['billing_address_status'] = false;
		}

		$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
		// $count_deleted_employees = DB::table('customer_plan_withdraw')->where('customer_active_plan_id', $get_active_plan->customer_active_plan_id)->count();

		if($get_active_plan->cheque == "true" && $get_active_plan->credit == "false") {
			if($get_active_plan->paid_cheque == "true") {
				if($get_active_plan->paid === "true") {
					$data['paid'] = true;
					$payment = DB::table('customer_cheque_logs')->where('customer_active_plan_id', $id)->first();
					if($payment) {
						if(empty($payment->date_received) || $payment->date_received == null) {
							$data['paid_date'] = $get_active_plan->paid_date;
						} else {
							$data['paid_date'] = $payment->date_received;
						}
						$data['notes'] = $payment->remarks;
					} else {
						$data['paid_date'] = $get_active_plan->paid_date;
					}
				} else {
					// $data['paid'] = false;
					return array(
						'status'	=> FALSE,
						'message'	=> 'Not yet paid.'
					);
				}
			} else {
				// $data['paid'] = false;
				return array(
					'status'	=> FALSE,
					'message'	=> 'Not yet paid.'
				);
			}
			$data['payment_method'] = "CHEQUE";
		} else if($get_active_plan->cheque == "false" && $get_active_plan->credit == "true") {
			if($get_active_plan->paid === "true") {
				$data['paid'] = true;
				// $data['paid_date'] = $get_active_plan->created_at;
				$payment = DB::table('customer_cheque_logs')->where('customer_active_plan_id', $id)->first();
				if($payment) {
					if(empty($payment->date_received) || $payment->date_received == null) {
						$data['paid_date'] = $get_active_plan->paid_date;
					} else {
						$data['paid_date'] = $payment->date_received;
					}
					$data['notes'] = $payment->remarks;
				} else {
					$data['paid_date'] = $get_active_plan->paid_date;
				}
			} else {
				// $data['paid'] = false;
				return array(
					'status'	=> FALSE,
					'message'	=> 'Not yet paid.'
				);
			}
			$data['payment_method'] = "CREDIT CARD";
		}

		$data['invoice_number'] = $get_invoice->invoice_number;
		$data['invoice_date']		= $get_invoice->invoice_date;
		$data['invoice_due']		= $get_invoice->invoice_due;
		$data['next_billing']   = date('Y-m-d', strtotime('-30 days', strtotime($get_invoice->invoice_due)));
		$data['number_employess'] = $get_active_plan->employees;
		$data['plan_start']     = $get_active_plan->plan_start;

		$plan = DB::table('customer_plan')->where('customer_plan_id', $get_active_plan->plan_id)->first();

		if($get_active_plan->new_head_count == 0) {
			if($get_active_plan->duration || $get_active_plan->duration != "") {
				$end_plan_date = date('Y-m-d', strtotime('+'.$get_active_plan->duration, strtotime($get_active_plan->plan_start)));
			} else {
				$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
			}
		} else {
			$first_plan = DB::table('customer_active_plan')->where('plan_id', $get_active_plan->plan_id)->first();
			$end_plan_date = date('Y-m-d', strtotime('+'.$first_plan->duration, strtotime($plan->plan_start)));
		}
		$data['plan_end'] 			= $end_plan_date;

		if($plan->account_type == "stand_alone_plan" || $plan->account_type === "stand_alone_plan") {

			if($get_active_plan->new_head_count == 0) {
				$data['price']          = number_format($get_invoice->individual_price, 2);
				$data['amount']					= number_format($get_invoice->employees * $get_invoice->individual_price, 2);
				$data['total']					= number_format($get_invoice->employees * $get_invoice->individual_price, 2);
				$data['amount_due']     = number_format($get_invoice->employees * $get_invoice->individual_price, 2);
			} else {
				$calculated_prices = self::calculateInvoicePlanPrice($get_invoice->individual_price, $get_active_plan->plan_start, $end_plan_date);
				$data['price']          = number_format($calculated_prices, 2);
				$data['amount']					= number_format($get_invoice->employees * $calculated_prices, 2);
				$data['total']					= number_format($get_invoice->employees * $calculated_prices, 2);
				$data['amount_due']     = number_format($get_invoice->employees * $calculated_prices, 2);
			}
		} else {
			$data['price']          = number_format($get_invoice->individual_price, 2);
			$data['amount']					= number_format($get_invoice->employees * $get_invoice->individual_price, 2);
			$data['total']					= number_format($get_invoice->employees * $get_invoice->individual_price, 2);
			$data['amount_due']     = number_format($get_invoice->employees * $get_invoice->individual_price, 2);
		}

		$check_log = DB::table('customer_cheque_logs')->where('customer_active_plan_id', $id)->first();
		// return array('result' => $check_log);
		if($check_log) {
			if($check_log->paid_amount == 0) {
				// update
				DB::table('customer_cheque_logs')
				->where('cheque_logs_id', $check_log->cheque_logs_id)
				->update(['paid_amount' => $data['total']]);
				$check_log = DB::table('customer_cheque_logs')->where('customer_active_plan_id', $id)->first();
			}
			$data['amount_paid'] = number_format($check_log->paid_amount, 2);
		} else {
			$data['amount_paid'] = $data['total'];
		}
		// return $data;
		// return View::make('pdf-download.hr-receipt', $data);

		$pdf = PDF::loadView('pdf-download.hr-receipt', $data);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');

		return $pdf->stream();
		// return $pdf->download($get_invoice->invoice_number.' - Receipt Plan - '.time().'.pdf');
	}

	public function createPaymentsRefund($id)
	{
		$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $id)->first();

		$exist_refund = DB::table('customer_plan_withdraw')->where('customer_active_plan_id', $id)->count();

		if($exist_refund > 0) {
			$refund_count = DB::table('payment_refund')
			->join('customer_active_plan', 'customer_active_plan.customer_active_plan_id', '=', 'payment_refund.customer_active_plan_id')
			->join('customer_plan', 'customer_plan.customer_plan_id', '=', 'customer_active_plan.plan_id')
			->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_plan.customer_buy_start_id')
			->where('customer_buy_start.customer_buy_start_id', $active_plan->customer_start_buy_id)
			->count();
        // create refund payment
			$check = 10 + $refund_count;
			$temp_invoice_number = str_pad($check, 6, "0", STR_PAD_LEFT);
			$invoice_number = 'OMC'.$temp_invoice_number.'A';
			if($refund_count > 0) {
				++$invoice_number;
			}

			$data = array(
				'customer_active_plan_id'   => $id,
				'cancellation_number'       => $invoice_number
			);

			$result = \PaymentRefund::create($data);
			return $result->id;
		}

		return FALSE;
	}


	public function getWithdrawEmployees( )
	{
		$result = self::checkSession();
		$customer_plans = DB::table('customer_plan')->where('customer_buy_start_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->get();

		$new_data = [];

		foreach ($customer_plans as $key => $cplan) {
			$active_plans = DB::table('customer_active_plan')->where('plan_id', $cplan->customer_plan_id)->get();
			foreach ($active_plans as $key => $plan) {
				if($plan->account_type == "stand_alone_plan" || $plan->account_type == "lite_plan" || $plan->account_type == "enterprise_plan") {
					$withdraws = DB::table('payment_refund')
					->where('customer_active_plan_id', $plan->customer_active_plan_id)
					->get();

					foreach ($withdraws as $key => $withdraw) {
						$refunds = DB::table('customer_plan_withdraw')
						->where('payment_refund_id', $withdraw->payment_refund_id)
						->whereIn('refund_status', [0, 1])
						->get();

						$amount = 0;
						foreach ($refunds as $key => $user) {
							if((int)$user->has_no_user == 0) {
								$employee = DB::table('user')->where('UserID', $user->user_id)->first();
								$plan = DB::table('user_plan_type')->where('user_id', $user->user_id)->orderBy('created_at', 'desc')->first();
								$invoice = DB::table('corporate_invoice')
									->where('customer_active_plan_id', $user->customer_active_plan_id)
									->first();
								$company_active_plan = DB::table('customer_active_plan', $user->customer_active_plan_id)->first();
								if((int)$company_active_plan->new_head_count == 1) {
									$calculated_prices_end_date = PlanHelper::getCompanyPlanDatesByPlan($company_active_plan->customer_start_buy_id, $company_active_plan->plan_id);
									$individual_price = PlanHelper::calculateInvoicePlanPrice($invoice->individual_price, $company_active_plan->plan_start, $calculated_prices_end_date['plan_end']);
									$individual_price = DecimalHelper::formatDecimal($individual_price);
								} else {
									$individual_price = $invoice->individual_price;
								}
			
								$diff = date_diff(new DateTime(date('Y-m-d', strtotime($plan->plan_start))), new DateTime(date('Y-m-d', strtotime($user->date_withdraw))));
								$days = $diff->format('%a') + 1;
								// $total_days = date("z", mktime(0,0,0,12,31,date('Y')));
								$total_days = MemberHelper::getMemberTotalDaysSubscription($plan->plan_start, $cplan->plan_end);
								$remaining_days = $total_days - $days + 1;
			
								$cost_plan_and_days = ($individual_price/$total_days);
								$temp_total = $cost_plan_and_days * $remaining_days;
								$temp_sub_total = $temp_total * 0.70;
			
								// check withdraw amount
								if($user->amount != $temp_sub_total) {
									// update amount
									\PlanWithdraw::where('plan_withdraw_id', $user->plan_withdraw_id)->update(['amount' => $temp_sub_total]);
								}
			
								$withdraw_data = DB::table('customer_plan_withdraw')->where('user_id', $user->user_id)->first();
								$amount += $temp_sub_total;
							} else {
								$amount += $user->amount;
							}
						}

						if($amount > 0) {
							$temp = array(
								'customer_active_plan_id' => $withdraw->customer_active_plan_id,
								'payment_refund_id'		  => $withdraw->payment_refund_id,
								'total_amount'	=> DecimalHelper::formatDecimal($amount, 2),
								'total_employees' => sizeof($refunds),
								'date_withdraw'	 => $withdraw->date_refund,
								'refund_data'		=> $withdraw,
								'currency_type' => $withdraw->currency_type
							);

							array_push($new_data, $temp);
						}
					}
				}
			}
		}


		return $new_data;
	}

	public function viewRefundedUserList($id)
	{
		$result = self::checkSession();
		$users = [];
		$withdraws = DB::table('customer_plan_withdraw')->where('payment_refund_id', $id)->get();
		$link_account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
		$corporate = DB::table('corporate')->where('corporate_id', $link_account->corporate_id)->select('company_name')->first();
		foreach ($withdraws as $key => $user) {
			$temp = array(
				'withdraw_user_data'	=> $user,
				'user'								=> DB::table('user')->where('UserID', $user->user_id)->first()
			);
			array_push($users, $temp);
		}

		return array('status' => TRUE, 'data' => $users, 'company' => $corporate);
	}

	public function accountBilling( )
	{
		$result = self::checkSession();
		$plan_data = DB::table('customer_active_plan')
		->where('customer_start_buy_id', $result->customer_buy_start_id)
		->where('status', 'true')
		->first();
		$plan['plan_type'] = 'Mednefits Care (Corporate)';
		$plan['employee_count'] = $completed = DB::table('customer_link_customer_buy')
		->join('corporate', 'corporate.corporate_id', '=', 'customer_link_customer_buy.corporate_id')
		->join('corporate_members', 'corporate_members.corporate_id', '=', 'corporate.corporate_id')
		->where('customer_link_customer_buy.customer_buy_start_id', $result->customer_buy_start_id)
		->where('corporate_members.removed_status', 0)
		->count();
		$plan['billing_frequency'] = 'Annual';
		$plan['start_date'] = date('d/m/Y', strtotime($plan_data->plan_start));
		$plan['payment_method'] = DB::table('customer_payment_method')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

		$business_contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
		$business_information = DB::table('customer_business_information')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();


		if($business_contact->billing_address == "false") {
			$business_billing_address = array(
				'company_name'	=> $business_information->company_name,
				'billing_address' => $business_information->company_address,
				'postal'				=> $business_information->postal_code
			);
			$billing_address_status = false;
		} else {
			$temp_data = DB::table('customer_billing_address')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
			$business_billing_address = array(
				'company_name'		=> $business_information->company_name,
				'billing_address' => $temp_data->billing_address,
				'postal'					=> $temp_data->postal_code
			);
			$billing_address_status = true;
		}

		$data = array(
			'billing_address'				=> $business_billing_address,
			'billing_address_status' => $billing_address_status
		);

		$plan[] = $data;

		return $plan;
	}

	public function updatePaymentMethod( )
	{
		$result = self::checkSession();
		$input = Input::all();
		$method = new CorporatePayment();
		if($input['payment_method'] == "cheque") {
			$cheque = "true";
			$credit = "false";
		} else {
			$cheque = "false";
			$credit = "true";
		}

		$data = array(
			'cheque'			=> $cheque,
			'credit_card'	=> $credit
		);

		$update = $method->updatePayment($data, $result->customer_buy_start_id);

		if($update) {
			return array(
				'status'	=> TRUE,
				'message'	=> 'Success.'
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Error.'
		);
	}

	public function updateBillingAddress( )
	{
		$result = self::checkSession();
		$input = Input::all();
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_id = $result->hr_dashboard_id;

		$details = array(
			'billing_address'	=> $input['billing_address'],
			'postal'			=> $input['postal'],
			'updated_at'		=> date('Y-m-d H:i:s')
		);

		DB::table('customer_billing_contact')
		->where('customer_buy_start_id', $result->customer_buy_start_id)
		->update($details);

		DB::table('customer_business_information')
		->where('customer_buy_start_id', $result->customer_buy_start_id)
		->update(['company_name' => $input['company_name'], 'updated_at' => date('Y-m-d H:i:s')]);

		$account_link = DB::table('customer_link_customer_buy')
		->where('customer_buy_start_id', $result->customer_buy_start_id)
		->first();
		DB::table('corporate')->where('corporate_id', $account_link->corporate_id)->update(['company_name' => $input['company_name'], 'updated_at' => date('Y-m-d H:i:s')]);

		if($admin_id) {
			$admin_logs = array(
				'admin_id'  => $admin_id,
				'admin_type' => 'mednefits',
				'type'      => 'admin_hr_updated_company_billing_address',
				'data'      => SystemLogLibrary::serializeData($input)
			);
			SystemLogLibrary::createAdminLog($admin_logs);
		} else {
			$admin_logs = array(
				'admin_id'  => $hr_id,
				'admin_type' => 'hr',
				'type'      => 'admin_hr_updated_company_billing_address',
				'data'      => SystemLogLibrary::serializeData($input)
			);
			SystemLogLibrary::createAdminLog($admin_logs);
		}

		return array(
			'status'	=> TRUE,
			'message'	=> 'Success.'
		);
	}

	public function newPurchaseFromExcel( )
	{

		$input = Input::all();

		// return $input;
		$result = self::checkSession();

		$rules = array(
			'file' => 'required|mimes:xlsx,xls|max:200000'
		);



		if(Input::hasFile('file'))
		{

			$validator = \Validator::make( Input::all() , $rules);
			$file = Input::file('file');

			if($validator->passes()){
				$fname = false;
				$lname = false;
				$nric = false;
				$dob = false;
				$email = false;
				$mobile = false;
				$job = false;
				$credits = false;
				$credit = 0;
				$medical_credits = false;
				$wellness_credits = false;
				$start_date = false;

				$temp_file = time().$file->getClientOriginalName();
				$file->move('excel_upload', $temp_file);
				$data_array = Excel::load(public_path()."/excel_upload/".$temp_file)->ignoreEmpty(true)->get();
				$headerRow = $data_array->first()->keys();
				$temp_users = [];
				$temp_enrollment_users = [];

				$plan = DB::table('customer_plan')->where('customer_buy_start_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
		        // return array('result' => $plan);
				if($plan->account_type == "insurance_bundle" || $plan->account_type == "trial_plan") {
					return array('status' => false, 'message' => 'Your Plan Account Type is not allowed to add another employees. Please contact Mednefits for assistance.');
				}

				foreach ($headerRow as $key => $row) {
					if($row == "first_name") {
						$fname = true;
					} else if($row == "last_name") {
						$lname = true;
					} else if($row == "nricfin") {
						$nric = true;
					} else if($row == "date_of_birth") {
						$dob = true;
					} else if($row == "work_email") {
						$email = true;
					} elseif ($row == "mobile") {
						$mobile = true;
					} else if($row == "wellness_credits") {
						$wellness_credits = true;
					} else if($row == "medical_credits") {
						$medical_credits = true;
					} else if($row == "start_date") {
						$start_date = true;
					}
				}

				if(!$fname || !$lname || !$nric || !$dob || !$email || !$mobile) {
					return array(
						'status'	=> FALSE,
						'message' 	=> 'Excel is invalid format. Please download the recommended file for Employee Enrollment.'
					);
				}

				foreach ($data_array as $key => $value) {
					if($value->first_name != null && $value->last_name != null) {
						array_push($temp_users, $value);
					}
				}

		        // return $temp_users;
				$amount = 99 * sizeof($temp_users);
				$credits = 0;
				$account = DB::table('customer_buy_start')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

		        // , strtotime($plan->plan_start)
				$new_active_plan = array(
					'customer_start_buy_id'		=> $result->customer_buy_start_id,
					'plan_id'					=> $plan->customer_plan_id,
					'plan_amount'				=> $amount,
					'discount'					=> 0,
					'plan_start'				=> date('Y-m-d'),
					'employees'					=> sizeof($temp_users),
		        	// 'duration'								=> $input['duration'],
					'duration'					=> '1 year',
					'stripe_transaction_id'		=> "NULL",
					'end_date_policy'			=> null,
					'cheque'					=> "true",
					'credit'					=> "false",
					'stripe_transaction_id'		=> "NULL",
					'new_head_count'			=> 1,
					'account_type'				=> $account->account_type
				);

		       // $active_plan = new CorporateActivePlan();
				Session::put('new_active_plan', $new_active_plan);

				try {
					// $id = $active_plan->createCorporateActivePlan($new_active_plan);
					foreach ($temp_users as $key => $user) {
						$check_user = DB::table('user')->where('Email', $user->work_email)->where('UserType', 5)->count();


						if(filter_var($user->work_email, FILTER_VALIDATE_EMAIL)) {
							$email_error = false;
							$email_message = '';
						} else {
							$email_error = true;
							$email_message = '*Error email format.';
						}
						// validations
						if($check_user > 0) {
							$email_error = true;
							$email_message = '*Email Already taken.';
						}

						if(is_null($user->first_name)) {
							$first_name_error = true;
							$first_name_message = '*First Name is empty';
						} else {
							$first_name_error = false;
							$first_name_message = '';
						}

						if(is_null($user->last_name)) {
							$last_name_error = true;
							$last_name_message = '*Last Name is empty';
						} else {
							$last_name_error = false;
							$last_name_message = '';
						}

						if(is_null($user->date_of_birth)) {
							$dob_error = true;
							$dob_message = '*Date of Birth is empty';
						} else {
							$dob_error = false;
							$dob_message = '';
						}

						if(is_null($user->mobile)) {
							$mobile_error = true;
							$mobile_message = '*Mobile Contact is empty';
						} else {
							$mobile_error = false;
							$mobile_message = '';
						}

						if(is_null($user->nricfin)) {
							$nric_error = true;
							$nric_message = '*NRIC/FIN is empty';
						} else {
							if(strlen($user->nricfin) < 9) {
								$nric_error = true;
								$nric_message = '*NRIC/FIN is must be 8 characters';
							} else {
								$nric_error = false;
								$nric_message = '';
							}
						}

						if(is_null($user->start_date)) {
							$start_date_error = false;
							$start_date_message = '';
							$start_date_result = true;
							$user->start_date = $plan->plan_start;
						} else {
							$start_date_result = self::validateStartDate($user->start_date);
							if(!$start_date_result) {
								$start_date_error = true;
								$start_date_message = '*Start Date is not well formatted Date';
							} else {
								$start_date_error = false;
								$start_date_message = '';
							}
						}

						if(is_null($user->medical_credits) || !isset($user->medical_credits)) {
							$credit_medical_amount = 0;
							$credits_medical_error = false;
							$credits_medical_message = '';
						} else {
							if(is_numeric($user->medical_credits)) {
								$credits_medical_error = false;
								$credits_medical_message = '';
								$credit_medical_amount = $user->medical_credits;
							} else {
								$credits_medical_error = true;
								$credits_medical_message = 'Credits is not a number.';
								$credit_medical_amount = $user->medical_credits;
							}
						}

						if(is_null($user->wellness_credits) || !isset($user->wellness_credits)) {
							$credit_wellness_amount = 0;
							$credits_wellness_error = false;
							$credits_wellnes_message = '';
						} else {
							if(is_numeric($user->wellness_credits)) {
								$credits_wellness_error = false;
								$credits_wellnes_message = '';
								$credit_wellness_amount = $user->wellness_credits;
							} else {
								$credits_wellness_error = true;
								$credits_wellnes_message = 'Credits is not a number.';
								$credit_wellness_amount = $user->wellness_credits;
							}
						}

						if($email_error || $first_name_error || $last_name_error || $dob_error || $mobile_error || $nric_error || $credits_medical_error || $credits_wellness_error || $start_date_error) {
							$error_status = true;
						} else {
							$error_status = false;
						}

						$error_logs = array(
							"error" 				=> $error_status,
							"email_error"			=> $email_error,
							"email_message"			=> $email_message,
							"first_name_error"		=> $first_name_error,
							"first_name_message"	=> $first_name_message,
							"last_name_error"		=> $last_name_error,
							"last_name_message"		=> $last_name_message,
							"nric_error"			=> $nric_error,
							"nric_message"			=> $nric_message,
							"dob_error"				=> $dob_error,
							"dob_message"			=> $dob_message,
							"mobile_error"			=> $mobile_error,
							"mobile_message"		=> $mobile_message,
							"credits_medical_error"	=> $credits_medical_error,
							"credits_medical_message" => $credits_medical_message,
							"credits_wellness_error" => $credits_wellness_error,
							"credits_wellnes_message" => $credits_wellnes_message,
							"start_date_error"		=> $start_date_error,
							"start_date_message"	=> $start_date_message
						);

						$temp_enrollment_data = array(
							'temp_enrollment_id'			=> self::generateRandomString(),
							'customer_buy_start_id'			=> $result->customer_buy_start_id,
							'first_name'					=> $user->first_name,
							'last_name'						=> $user->last_name,
							'nric'							=> $user->nricfin,
							'dob'							=> $dob_error ? $user->date_of_birth : date('d/m/Y', strtotime($user->date_of_birth)),
							'email'							=> $user->work_email,
							'mobile'						=> $user->mobile,
							'job_title'						=> $user->job_title,
							'credits'						=> $credit_medical_amount,
							'wellness_credits'				=> $credit_wellness_amount,
							'start_date'					=> $start_date_result ? date('d/m/Y', strtotime($user->start_date)) : null,
							'error_logs'					=> serialize($error_logs)
						);

						// $insert_temp_user = $temp_enroll->insertTempEnrollment($temp_enrollment_data);
						array_push($temp_enrollment_users, $temp_enrollment_data);
					}

			        // $add_active_plan = new AddedActivePlanPurchase();
			        // $add_active_plan->createAddedActivePlanPurchase(array('customer_active_plan' => $id));
					Session::put('temp_enrollment_users', $temp_enrollment_users);
					Session::put('added_purchase_status', true);
					return array(
						'status'	=> TRUE,
						'message' => 'Success.',
						'added_purchase' => TRUE
			        	// 'customer_active_plan_id' => $id
					);
				} catch(Exception $e) {
					$email = [];
					$email['end_point'] = url('hr/new_purchase_active_plan/excel', $parameter = array(), $secure = null);
					$email['logs'] = 'Save Employee Enrollment HR Dashboard - '.$e->getMessage();
					$email['emailSubject'] = 'Error log.';
				// EmailHelper::sendErrorLogs($email);
					return array('status' => FALSE, 'message' => 'Failed to create employee enrollment. Please contact Mednefits Team.', 'error' => $e->getMessage());
				}

			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'Invalid File.'
				);
			}
		} else {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Empty File.'
			);
		}
	}

	public function generateRandomString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function createActivePlanInvoice($data, $payment_status)
	{
		$check = DB::table('corporate_invoice')->count();
		$invoice_number = str_pad($check + 1, 6, "0", STR_PAD_LEFT);

		if($payment_status === false) {
			$payment_due = date('Y-m-d', strtotime('-5 days', strtotime($data->created_at)));
		} else {
			$payment_due = date('Y-m-d', strtotime($data->created_at));
		}

		$first_plan = DB::table('customer_active_plan')
		->join('corporate_invoice', 'corporate_invoice.customer_active_plan_id', '=', 'customer_active_plan.customer_active_plan_id')
		->where('customer_active_plan.plan_id', $data->plan_id)
		->first();

		$data_invoice = array(
			'customer_active_plan_id'	=> $data->customer_active_plan_id,
			'invoice_number'					=> 'OMC'.$invoice_number,
			'invoice_date'						=> $data->created_at,
			'invoice_due'							=> $payment_due,
			'employees'								=> $data->employees,
			'customer_id'							=> $data->customer_start_buy_id,
			'individual_price'				=> $first_plan->individual_price,
			'invoice_type'						=> 'invoice'
		);

		\CorporateInvoice::create($data_invoice);
	}

	public function newPurchaseFromWebInput()
	{
		$users = Input::all();
		$result = self::checkSession();
		
		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $result->customer_buy_start_id)->orderby('created_at', 'desc')->first();

		if($plan->account_type == "insurance_bundle") {
			return array('status' => false, 'message' => 'Your Plan Account Type is not allowed to add another employees. Please contact Mednefits for assistance.');
		}

		if($plan->account_type == "insurance_bundle") {
			$paid = "true";
			$paid_cheque = "true";
			$paid_date = date('Y-m-d');
			$payment_status = true;
		} else if($plan->account_type == "trial_plan") {
			return array('status' => FALSE, 'message' => 'Trial Accounts cannot add employees. Please contact Mednefits.');
		} else {
			$paid = "false";
			$paid_cheque = "false";
			$paid_date = NULL;
			$payment_status = false;
		}


		$amount = 99 * sizeof($users['data']);
		$account = DB::table('customer_buy_start')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

		$new_active_plan = array(
			'customer_start_buy_id'		=> $result->customer_buy_start_id,
			'plan_id'					=> $plan->customer_plan_id,
			'plan_amount'				=> $amount,
			'discount'					=> 0,
			'plan_start'				=> date('Y-m-d'),
			'employees'					=> sizeof($users['data']),
			'duration'					=> '1 year',
			'stripe_transaction_id'		=> "NULL",
			'end_date_policy'			=> date('Y-m-d', strtotime('+'.$users['duration'], strtotime($plan->plan_start))),
			'cheque'					=> "false",
			'credit'					=> "true",
			'stripe_transaction_id'		=> "NULL",
			'new_head_count'			=> 1,
			'account_type'				=> $plan->account_type,
			'paid'						=> $paid,
			'paid_date'					=> $paid_date,
			'paid_cheque'				=> $paid_cheque
		);

		Session::put('new_active_plan', $new_active_plan);
		try {
			$temp_enrollment_users = [];
			foreach ($users['data'] as $key => $user) {
				$temp_enroll = new TempEnrollment();
				$check_user = DB::table('user')->where('Email', $user['work_email'])->where('UserType', 5)->count();

				if(filter_var($user['work_email'], FILTER_VALIDATE_EMAIL)) {
					$email_error = false;
					$email_message = '';
				} else {
					$email_error = true;
					$email_message = '*Error email format.';
				}
			// validations
				if($check_user > 0) {
					$email_error = true;
					$email_message = '*Email Already taken.';
				}

				if(is_null($user['first_name'])) {
					$first_name_error = true;
					$first_name_message = '*First Name is empty';
				} else {
					$first_name_error = false;
					$first_name_message = '';
				}

				if(is_null($user['last_name'])) {
					$last_name_error = true;
					$last_name_message = '*Last Name is empty';
				} else {
					$last_name_error = false;
					$last_name_message = '';
				}

				if(is_null($user['date_of_birth'])) {
					$dob_error = true;
					$dob_message = '*Date of Birth is empty';
				} else {
					$dob_error = false;
					$dob_message = '';
				}

				if(is_null($user['mobile'])) {
					$mobile_error = true;
					$mobile_message = '*Mobile Contact is empty';
				} else {
					$mobile_error = false;
					$mobile_message = '';
				}

				if(is_null($user['nric'])) {
					$nric_error = true;
					$nric_message = '*NRIC/FIN is empty';
				} else {
					if(strlen($user['nric']) < 9) {
						$nric_error = true;
						$nric_message = '*NRIC/FIN is must be 8 characters';
					} else {
						$nric_error = false;
						$nric_message = '';
					}
				}

				if(is_null($user['start_date'])) {
					$start_date_error = true;
					$start_date_message = '*Start Date is empty';
					$start_date_result = false;
				} else {
					// $start_date_result = self::validateStartDate($user['start_date']);
					// if(!$start_date_result) {
					// 	$start_date_error = true;
					// 	$start_date_message = '*Start Date is not well formatted Date';
					// } else {
					$start_date_error = false;
					$start_date_message = '';
					// }
				}

				if(is_null($user['medical_credits']) || !isset($user['medical_credits'])) {
					$credit_medical_amount = 0;
					$credits_medical_error = false;
					$credits_medical_message = '';
				} else {
					if(is_numeric($user['medical_credits'])) {
						$credits_medical_error = false;
						$credits_medical_message = '';
						$credit_medical_amount = $user['medical_credits'];
					} else {
						$credits_medical_error = true;
						$credits_medical_message = 'Credits is not a number.';
						$credit_medical_amount = $user['medical_credits'];
					}
				}

				if(is_null($user['wellness_credits']) || !isset($user['wellness_credits'])) {
					$credit_wellness_amount = 0;
					$credits_wellness_error = false;
					$credits_wellnes_message = '';
				} else {
					if(is_numeric($user['wellness_credits'])) {
						$credits_wellness_error = false;
						$credits_wellnes_message = '';
						$credit_wellness_amount = $user['wellness_credits'];
					} else {
						$credits_wellness_error = true;
						$credits_wellnes_message = 'Credits is not a number.';
						$credit_wellness_amount = $user['wellness_credits'];
					}
				}

				if($email_error || $first_name_error || $last_name_error || $dob_error || $mobile_error || $nric_error || $credits_medical_error || $credits_wellness_error || $start_date_error) {
					$error_status = true;
				} else {
					$error_status = false;
				}

				$error_logs = array(
					"error" 				=> $error_status,
					"email_error"			=> $email_error,
					"email_message"			=> $email_message,
					"first_name_error"		=> $first_name_error,
					"first_name_message"	=> $first_name_message,
					"last_name_error"		=> $last_name_error,
					"last_name_message"		=> $last_name_message,
					"nric_error"			=> $nric_error,
					"nric_message"			=> $nric_message,
					"dob_error"				=> $dob_error,
					"dob_message"			=> $dob_message,
					"mobile_error"			=> $mobile_error,
					"mobile_message"		=> $mobile_message,
					"credits_medical_error"	=> $credits_medical_error,
					"credits_medical_message" => $credits_medical_message,
					"credits_wellness_error" => $credits_wellness_error,
					"credits_wellnes_message" => $credits_wellnes_message,
					"start_date_error"		=> $start_date_error,
					"start_date_message"	=> $start_date_message
				);

				$temp_enrollment_data = array(
					'temp_enrollment_id'			=> self::generateRandomString(),
					'customer_buy_start_id'			=> $result->customer_buy_start_id,
					'first_name'					=> $user['first_name'],
					'last_name'						=> $user['last_name'],
					'nric'							=> $user['nric'],
					'dob'							=> $dob_error ? $user['date_of_birth'] : date('d/m/Y', strtotime($user['date_of_birth'])),
					'email'							=> $user['work_email'],
					'mobile'						=> $user['mobile'],
					'job_title'						=> $user['job_title'],
					'credits'						=> $credit_medical_amount,
					'wellness_credits'				=> $credit_wellness_amount,
					'start_date'					=> $user['start_date'],
					'error_logs'					=> serialize($error_logs)
				);

				array_push($temp_enrollment_users, $temp_enrollment_data);
			}

			Session::put('temp_enrollment_users', $temp_enrollment_users);
			Session::put('added_purchase_status', true);
			return array(
				'status'	=> TRUE,
				'message' => 'Success.',
				'added_purchase' => TRUE
			);
		} catch(Exception $e) {
			$email = [];
			$email['end_point'] = url('hr/save/web_input/new_active_plan', $parameter = array(), $secure = null);
			$email['logs'] = 'Create new Plan in HR Dashboard Web Input - '.$e->getMessage();
			$email['emailSubject'] = 'Error log.';
			EmailHelper::sendErrorLogs($email);
			return array('status' => FALSE, 'message' => 'Failed to create new employee enrollment. Please contact Mednefits Team.', 'error' => $e->getMessage());
		}

	}

	public function paymentMethod( )
	{
		// $result = self::checkSession();
		$input = Input::all();
		// if($input['cheque'] == "true" && $input['credit_card'] == "false" || $input['cheque'] == "false" && $input['credit_card'] == "true") {

			// if($input['cheque'] == "true" && $input['credit_card'] == "false") {
		$active_plan = array(
			'cheque'										=> "true",
			'credit'										=> "false"
		);
				// return "yeah";
		$corporate_active_plan = new CorporateActivePlan();
		$corporate_active_plan->updateCorporateActivePlan($active_plan, $input['customer_active_plan_id']);
		self::finishEnrollFromAddedPurchase($input['customer_active_plan_id']);
		$add_active_plan = new AddedActivePlanPurchase();
		$add_active_plan->updateActivePlanPurchase($input['customer_active_plan_id']);
		return array('status' => TRUE, 'message' => 'Success.');
			// } else {
			// 	$active_plan = array(
			// 		'cheque'										=> "false",
			// 		'credit'										=> "true"
			// 	);
			// 	// return "yeah";
			// 	$corporate_active_plan = new CorporateActivePlan();
			// 	$corporate_active_plan->updateCorporateActivePlan($active_plan, $input['customer_active_plan_id']);
			// 	self::payFromCredit();

			// }

		// } else {
		// 	return array(
		// 		'status'	=> FALSE,
		// 		'message'	=> 'cheque should be false and credit_card should be true or cheque should be true or credit_card should be false'
		// 	);
		// }
	}

	public function payFromCredit( )
	{

		$input = Input::all();

		if(empty($input['stripeToken'])) {
			return array('status' => FALSE, 'message' => 'No token.');
		}

		// $result = self::checkSession();

		$token  = $input['stripeToken'];
		$stripe = StripeHelper::config();
		\Stripe\Stripe::setApiKey($stripe['secret_key']);

	}

	public function finishEnrollFromAddedPurchase($id)
	{
		$result = self::checkSession();
		// $plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $id)->first();
		$enrollees = DB::table('customer_temp_enrollment')
		->where('active_plan_id', $id)
		->where('enrolled_status', "false")
		->get();

		foreach ($enrollees as $key => $enrollee) {
			$data_enrollee = DB::table('customer_temp_enrollment')
			->where('temp_enrollment_id', $enrollee->temp_enrollment_id)
			->where('enrolled_status', "false")
			->first();
			// return json_encode($data_enrollee);
			if(empty($data_enrollee)) {
				array(
					'status'	=> FALSE,
					'message'	=> 'Enrollee does not exist.'
				);
			} else {
				// return $data_enrollee;
				$user = new User();
				$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $id)->first();
				$corporate = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $data_enrollee->customer_buy_start_id)->first();

				if($data_enrollee->start_date != NULL || $data_enrollee->start_date != "NULL") {
					$start_date = date('Y-m-d', strtotime($data_enrollee->start_date));
				} else {
					$start_date = date('Y-m-d', strtotime($data_enrollee->active_plan));
				}

				$password = StringHelper::get_random_password(8);
				$data = array(
					'Name'			=> $data_enrollee->first_name.' '.$data_enrollee->last_name,
					'Password'	=> md5($password),
					'Email'			=> $data_enrollee->email,
					'PhoneNo'		=> $data_enrollee->mobile,
					'PhoneCode'	=> NULL,
					'NRIC'			=> $data_enrollee->nric,
					'Job_Title'	=> $data_enrollee->job_title,
					'Active'		=> 1
				);

				$user_id = $user->createUserFromCorporate($data);
				$corporate_member = array(
					'corporate_id'	=> $corporate->corporate_id,
					'user_id'				=> $user_id,
					'first_name'		=> $data_enrollee->first_name,
					'last_name'			=> $data_enrollee->last_name,
					'type'					=> 'member'
				);
				DB::table('corporate_members')->insert($corporate_member);
				$plan_type = new UserPlanType();
				$check_plan_type = $plan_type->checkUserPlanType($user_id);

				if($check_plan_type == 0) {
					$group_package = new PackagePlanGroup();
					$bundle = new Bundle();
					$user_package = new UserPackage();

					$group_package_id = $group_package->getPackagePlanGroupDefault();
					$result_bundle = $bundle->getBundle($group_package_id);

					foreach ($result_bundle as $key => $value) {
						$user_package->createUserPackage($value->care_package_id, $user_id);
					}

					$plan_type_data = array(
						'user_id'						=> $user_id,
						'package_group_id'	=> $group_package_id,
						'duration'					=> '1 year',
						'plan_start'			=> $start_date,
						'active_plan_id'		=> $active_plan->customer_active_plan_id
					);
					$plan_type->createUserPlanType($plan_type_data);
				}

				// store user plan history
				$user_plan_history = new UserPlanHistory();
				$user_plan_history_data = array(
					'user_id'		=> $user_id,
					'type'			=> "started",
					'date'			=> $start_date,
					'customer_active_plan_id' => $active_plan->customer_active_plan_id
				);

				$user_plan_history->createUserPlanHistory($user_plan_history_data);

				// assign credits
				$customer = DB::table('customer_credits')->where('customer_id', $result->customer_buy_start_id)->first();

				if($data_enrollee->credits < $customer->balance) {
					// give credits
					$wallet_class = new Wallet();
					$wallet = DB::table('e_wallet')->where('UserID', $user_id)->orderby('created_at', 'desc')->first();
					$update_wallet = $wallet_class->addCredits($user_id, $data_enrollee->credits);

					$employee_logs = new WalletHistory();

					$wallet_history = array(
						'wallet_id'		=> $wallet->wallet_id,
						'credit'			=> $data_enrollee->credits,
						'logs'				=> 'added_by_hr'
					);

					$employee_logs->createWalletHistory($wallet_history);

					$user_credits = DB::table('e_wallet')->where('UserID', $user_id)->orderby('created_at', 'desc')->first();

					$allocation_data = new EmployeeCreditAllocation( );
					$allocation = array(
						'user_id'						=> $user_id,
						'credit_allocated'	=> $user_credits->balance
					);

					$allocation_data->createAllocation($allocation);

					$customer_credits = new CustomerCredits();


					$customer_credits_result = $customer_credits->deductCustomerCredits($customer->customer_credits_id, $user_credits->balance);
					$customer_credits_left = DB::table('customer_credits')->where('customer_credits_id', $customer->customer_credits_id)->first();
					// return $customer_credits_result;
					if($customer_credits_result) {
						$company_deduct_logs = array(
							'customer_credits_id'	=> $customer->customer_credits_id,
							'credit'							=> $user_credits->balance,
							'logs'								=> 'added_employee_credits',
							'user_id'							=> $user_id,
							'running_balance'			=> $customer_credits_left->balance
						);

						$customer_credit_logs = new CustomerCreditLogs( );
						$customer_credit_logs->createCustomerCreditLogs($company_deduct_logs);
					}
				}

				$customer_plan_status = new \CustomerPlanStatus( );
				$customer_plan_status->addjustCustomerStatus('employees_input', $active_plan->plan_id, 'increment', 1);
				$customer_plan_status->addjustCustomerStatus('enrolled_employees', $active_plan->plan_id, 'increment', 1);
				// \CustomerPlanStatus::where('customer_plan_id', $active_plan->plan_id)->increment('enrolled_employees', 1);
				// \CustomerPlanStatus::where('customer_plan_id', $active_plan->plan_id)->increment('employees_input', 1);
				DB::table('customer_temp_enrollment')
				->where('temp_enrollment_id', $enrollee->temp_enrollment_id)
				->update(['enrolled_status' => "true"]);
				$company = DB::table('corporate')->where('corporate_id', $corporate->corporate_id)->first();
				// $email_data['company']   = $company->company_name;
				// $email_data['emailName'] = $data_enrollee->first_name.' '.$data_enrollee->last_name;
				// $email_data['emailTo']   = $data_enrollee->email;
				// $email_data['emailPage'] = 'email-templates.employee-welcome-email';
				// $email_data['emailSubject'] = 'WELCOME TO MEDNEFITS CARE';
				// $email_data['coverage'] = url('pdf/Mednefits-u2019s Health Partners & Benefits (corp).pdf', $parameters = array(), $secure = null);
				$email_data['company']   = ucwords($company->company_name);
				$email_data['emailName'] = $data_enrollee->first_name.' '.$data_enrollee->last_name;
				$email_data['emailTo']   = $data_enrollee->email;
				$email_data['email'] = $data_enrollee->email;
				$email_data['emailPage'] = 'email-templates.latest-templates.mednefits-welcome-member-enrolled';
				$email_data['plan_start'] = date('d F Y', strtotime($start_date));
				$email_data['name'] = $data_enrollee->first_name.' '.$data_enrollee->last_name;
				$email_data['emailSubject'] = "WELCOME TO MEDNEFITS CARE";
				$email_data['pw'] = $password;
				$email_data['url'] = url('/');

				EmailHelper::sendEmail($email_data);

				$email_data['emailTo']   = 'info@medicloud.sg';
				EmailHelper::sendEmail($email_data);
			}
		}
		return TRUE;
	}

	public function taskList( )
	{

		$task = [];

		// get pending enrollment employee
		$result = self::checkSession();
		$total_employees = 0;

		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $result->customer_buy_start_id)->orderby('created_at', 'desc')->first();
		$plan_status = DB::table('customer_plan_status')->where('customer_plan_id', $plan->customer_plan_id)->first();

		// check plan expiration

		$active_plans = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->get();

		if((int)$active_plans[0]->plan_extention_enable == 1) {
			$plan_extention = DB::table('plan_extensions')
			->where('customer_active_plan_id', $active_plans[0]->customer_active_plan_id)
			->first();
			if($plan_extention) {
				if($plan_extention->duration || $plan_extention->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$plan_extention->duration, strtotime($plan_extention->plan_start)));
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan_extention->plan_start)));
				}
			} else {
				if($active_plans[0]->duration || $active_plans[0]->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$active_plans[0]->duration, strtotime($plan->plan_start)));
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
				}
			}
		} else {
			if($active_plans[0]->duration || $active_plans[0]->duration != "") {
				$end_plan_date = date('Y-m-d', strtotime('+'.$active_plans[0]->duration, strtotime($plan->plan_start)));
			} else {
				$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
			}
		}

		$date = date('Y-m-d', strtotime('-1 month', strtotime($end_plan_date)));

		if(date('Y-m-d') >= $date) {
			array_push($task, array(
				'status'	=> TRUE,
				'type'		=> 'expiring_plan',
				'message'	=> 'Your Care Plan will expire in '.date('F d, Y', strtotime($end_plan_date)).'. Please renew your Care Plan before the said date.'
			));

			return $task;
		}

		// $added_purchase = 0;
		// foreach ($active_plans as $key => $plan_data) {
		// 	// $total_employees += $plan_data->employees;
		// 	$count = DB::table('customer_added_active_plan_purchase')->where('customer_active_plan', $plan_data->customer_active_plan_id)->where('status', 0)->count();
		// 	if($count > 0) {
		// 		$added_purchase++;
		// 	}
		// }

		// if($added_purchase > 0) {
		// 	array_push($task, array(
		// 		'status'	=> TRUE,
		// 		'type'		=> 'pending_active_plan_enrollment',
		// 	));
		// }

		$in_progress = $plan_status->employees_input - $plan_status->enrolled_employees;

		array_push($task, array(
			'status'	=> TRUE,
			'type'		=> 'pending_enrollment',
			'total_employees'		=> $in_progress
		));

		// get dependent status
		$dependent_plan_status = DB::table('dependent_plan_status')
		->where('customer_plan_id', $plan->customer_plan_id)
		->orderBy('created_at', 'desc')
		->first();
		if($dependent_plan_status) {
			$vacant = $dependent_plan_status->total_dependents - $dependent_plan_status->total_enrolled_dependents;
			array_push($task, array(
				'status'	=> TRUE,
				'type'		=> 'pending_enrollment_dependent',
				'total_employees'		=> $vacant
			));
		}

		// get employee list pending activation
		$active_plans = DB::table('customer_active_plan')->where('paid', 'false')->where('plan_id', $plan->customer_plan_id)->get();
		// return $active_plans;
		$total_employees = 0;
		$pending_data = [];
		foreach ($active_plans as $key => $plan_data) {
			// $total_employees += $plan_data->employees;

			// get invoice
			$invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $plan_data->customer_active_plan_id)->first();

			$invoice_due = date('d/m/Y', strtotime($invoice->invoice_due));

			// get dependents
			$dependents = DB::table('dependent_plans')
			->where('customer_active_plan_id', $plan_data->customer_active_plan_id)
			->sum('total_dependents');

			array_push($task, array(
				'total_employees'	=>	$plan_data->employees,
				'total_dependents'	=> $dependents,
				'active_plan'		=> 	$plan_data,
				'invoice_due'		=> $invoice_due,
				'type'				=> 'pending_activation',
			));
		}

		// get dependent plan
		// $dependents = DB::table('dependent_plans')->where('customer_plan_id', $plan->customer_plan_id)->get();

		// foreach ($dependents as $key => $dependent) {
		// 	if((int)$dependent->payment_status == 0) {
		// 		$dependent_invoice = DB::table('dependent_invoice')
		// 								->where('dependent_plan_id', $dependent->dependent_plan_id)
		// 								->first();
		// 		$invoice_due = date('d/m/Y', strtotime($dependent_invoice->invoice_due));

		// 		array_push($task, array(
		// 			'total_employees'	=>	$dependent_invoice->total_dependents,
		// 			'dependent_active_plan'		=> 	$dependent,
		// 			'invoice_due'		=> $invoice_due,
		// 			'type'				=> 'pending_activation_dependent',
		// 		));
		// 	}
		// }

		// employee replacement seat
		$replacement_seats = DB::table('employee_replacement_seat')
		->where('customer_id', $result->customer_buy_start_id)
		->where('vacant_status', 0)
								// ->where('last_date_of_coverage', '>=', date('Y-m-d'))
		->get();

		foreach ($replacement_seats as $key => $seat) {

			$temp = array(
				'date_of_enrollment' => date('d/m/Y', strtotime($seat->date_enrollment)),
				'replacement_seat_id' => $seat->employee_replacement_seat_id,
				'customer_id'	=> $seat->customer_id,
				'type'		=> 'vacant_seat',
				'user_type' => 'employee'
			);

			array_push($task, $temp);
		}
		
		// dependent replace
		$dependent_seats = DB::table('dependent_replacement_seat')
		->where('customer_id', $result->customer_buy_start_id)
		->where('vacant_status', 0)
		->get();

		foreach ($dependent_seats as $key => $seat) {

			$temp = array(
				'date_of_enrollment' => date('d/m/Y', strtotime($seat->date_enrollment)),
				'replacement_seat_id' => $seat->dependent_replacement_seat_id,
				'customer_id'	=> $seat->customer_id,
				'type'		=> 'vacant_seat',
				'user_type' => 'dependent'
			);

			array_push($task, $temp);
		}

		return $task;
	}

	public function getEmployeeCredits($id)
	{
		$result = self::checkSession();
		$get_allocation = 0;
		$get_allocation_spent = 0;

		$company_credits = DB::table('customer_credits')->where('customer_id', $result->customer_buy_start_id)->first();
			// $employee_credits = DB::table('e_wallet')->where('UserID', $id)->first();
		$wallet = DB::table('e_wallet')->where('UserID', $id)->orderBy('created_at', 'desc')->first();

		$get_allocation = DB::table('e_wallet')
		->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
		->where('e_wallet.UserID', $id)
		->where('wallet_history.logs', 'added_by_hr')
		->sum('wallet_history.credit');
		$deduct_allocation = DB::table('e_wallet')
		->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
		->where('e_wallet.UserID', $id)
		->where('wallet_history.logs', 'deducted_by_hr')
		->sum('wallet_history.credit');

		$e_claim_spent = DB::table('wallet_history')
		->where('wallet_id', $wallet->wallet_id)
		->where('where_spend', 'e_claim_transaction')
		->sum('credit');

		$in_network_temp_spent = DB::table('wallet_history')
		->where('wallet_id', $wallet->wallet_id)
		->where('where_spend', 'in_network_transaction')
		->sum('credit');
		$credits_back = DB::table('wallet_history')
		->where('wallet_id', $wallet->wallet_id)
		->where('where_spend', 'credits_back_from_in_network')
		->sum('credit');
		$get_allocation_spent = $in_network_temp_spent - $credits_back + $e_claim_spent;
		$allocation = number_format($get_allocation, 2, '.', '');

		$employee_credits = array(
			'user_id'			=> $id,
			'wallet_id'		=> $wallet->wallet_id,
			'allocation'	=> number_format($allocation - $deduct_allocation, 2),
			'usage'				=> number_format($get_allocation_spent, 2)
		);

		return array(
			'status'	=> TRUE,
			'data'		=> array('company_credits' => $company_credits, 'employee_credits' => $employee_credits)
		);
	}

	public function companyCredits( )
	{
		$result = self::checkSession();
		$allocated = 0;
		$spent = 0;
		$deleted_employee_allocation = 0;
		$total_deducted_credits = 0;
		// get corporate id
		$link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

		$corporate_members = DB::table('corporate_members')->where('corporate_id', $link->corporate_id)->get();

		foreach ($corporate_members as $key => $user) {
			$wallet = DB::table('e_wallet')->where('UserID', $user->user_id)->orderBy('created_at', 'desc')->first();

			$employee_allocated = DB::table('wallet_history')->where('wallet_id', $wallet->wallet_id)->where('logs', 'added_by_hr')->sum('credit');

			$deducted_allocation = DB::table('wallet_history')->where('wallet_id', $wallet->wallet_id)->where('logs', 'deducted_by_hr')->sum('credit');
			$total_deducted_credits += $deducted_allocation;
			$allocated += $employee_allocated;

			if($user->removed_status == 1) {
				$deleted_employee_allocation += $employee_allocated - $deducted_allocation;
			}

			$temp = DB::table('wallet_history')->where('wallet_id', $wallet->wallet_id)->whereIn('where_spend', ['in_network_transaction', 'e_claim_transaction'])->sum('credit');

			$credits_back = DB::table('wallet_history')->where('wallet_id', $wallet->wallet_id)->where('logs', 'credits_back_from_in_network')->sum('credit');

			$spent += $temp - $credits_back;
		}

		$data = array(
			'allocated'	=> number_format($allocated - $deleted_employee_allocation - $total_deducted_credits, 2),
			'spent'			=> number_format($spent, 2)
		);

		return array(
			'status'	=> TRUE,
			'data'		=> $data
		);

	}

	public function employeeAssignCredits( )
	{
		$result = self::checkSession();
		$input = Input::all();
		// $result->customer_buy_start_id
		// check employee existence
		$check_user = DB::table('user')->where('UserID', $input['user_id'])->count();
		if($check_user == 0) {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Employee does not exist.'
			);
		}

		$check_customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $result->customer_buy_start_id)->count();
		if($check_customer == 0) {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Customer does not exist.'
			);
		}

		// check company credits remaning balance
		$company_credits = DB::table('customer_credits')->where('customer_id', $result->customer_buy_start_id)->first();

		// get total
		$total_allocated = DB::table('customer_credit_logs')->where('customer_credits_id', $company_credits->customer_credits_id)->where('logs', 'admin_added_credits')->sum('credit');
		$deducted_allocated = DB::table('customer_credit_logs')->where('customer_credits_id', $company_credits->customer_credits_id)->where('logs', 'added_employee_credits')->sum('credit');

		$total_allocation = $total_allocated - $deducted_allocated;

		// return $total_allocation;
		// return json_encode($company_credits);
		if($input['amount'] > $total_allocation) {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Cannot add credits to this employee. Credits you assigned to this employee is greater than the current company balance which the company has '.number_format($total_allocation, 2).' credits.'
			);
		}


		// give credits to employee
		$wallet = new Wallet( );
		$wallet_result = $wallet->addCredits($input['user_id'], $input['amount']);

		$wallet_id = $wallet->getWalletId($input['user_id']);
		$employee_credits_left = DB::table('e_wallet')->where('wallet_id', $wallet_id)->first();
		if($wallet_result) {
			// deduct company credits and save to logs
			$customer_credits = new CustomerCredits();
			$customer_credits_result = $customer_credits->deductCustomerCredits($company_credits->customer_credits_id, $input['amount']);
			$customer_credits_left = DB::table('customer_credits')->where('customer_credits_id', $company_credits->customer_credits_id)->first();
			if($customer_credits_result) {
				$company_deduct_logs = array(
					'customer_credits_id'	=> $company_credits->customer_credits_id,
					'credit'							=> $input['amount'],
					'logs'								=> 'added_employee_credits',
					'user_id'							=> $input['user_id'],
					'running_balance'			=> $customer_credits_left->balance
				);

				$customer_credit_logs = new CustomerCreditLogs( );
				$customer_credit_logs->createCustomerCreditLogs($company_deduct_logs);

				$employee_credits_logs = array(
					'wallet_id'	=> $wallet_id,
					'credit'		=> $input['amount'],
					'logs'			=> 'added_by_hr',
					'running_balance' => $employee_credits_left->balance
				);

				$employee_logs = new WalletHistory();
				$employee_logs->createWalletHistory($employee_credits_logs);

				$allocation_data = new EmployeeCreditAllocation( );
				$allocation = array(
					'user_id'						=> $input['user_id'],
					'credit_allocated'	=> $employee_credits_left->balance
				);

				$allocation_data->createAllocation($allocation);
				return array(
					'status'	=> TRUE,
					'message'	=> 'Employee successfully assigned $'.number_format($input['amount'], 2, '.', '').'.'
				);

				return array(
					'status'	=> TRUE,
					'message'	=> 'Employee successfully assigned $'.number_format($input['amount'], 2, '.', '').'.'
				);
			}

		} else {
			$total = $input['amount'] - $company_credits->balance;
			return array(
				'status'	=> FALSE,
				'message'	=> 'Company credits is not enough to assign to employee. Current Company credits is '.number_format($company_credits->balance, 2).'. Need '.number_format($total, 2)
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Error.'
		);
	}

	public function confirmPassword()
	{
		$result = self::checkSession();
		$input = Input::all();
		$check = DB::table('customer_hr_dashboard')->where('customer_buy_start_id', $result->customer_buy_start_id)->where('password', md5($input['password']))->count();

		if($check > 0) {
			return array(
				'status'	=> TRUE,
				'message' => 'Success.'
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Invalid Password.'
		);
	}

	public function getActivePlan($id)
	{
		$result = DB::table('customer_active_plan')->where('customer_active_plan_id', $id)->first();

		if($result) {
			return array(
				'status'	=> TRUE,
				'data'	=> $result
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Error.'
		);
	}

	public function forgotPassword( )
	{
		$input = Input::all();
		$account = DB::table('customer_hr_dashboard')->where('email', $input['email'])->first();

		if($account) {
			$hostName = $_SERVER['HTTP_HOST'];
			$protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
			$server = $protocol.$hostName;
			// $password = StringHelper::get_random_password(8);
			$reset_link = StringHelper::getEncryptValue();
			$data = array(
				// 'password'	=> md5($password)
				'reset_link'	=> $reset_link
				// 'remember_token'	=> str_random(5).'-'.str_random(5).'-'.str_random(5).'-'.str_random(5)
			);

			$hr  = new HRDashboard();
			$result = $hr->updateCorporateHrDashboard($account->hr_dashboard_id, $data);
			$contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $account->customer_buy_start_id)->first();
			$billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $account->customer_buy_start_id)->first();
			$contacts = DB::table('company_contacts')->where('customer_id', $account->customer_buy_start_id)->get();

			// $config = StringHelper::Deployment( );

			// if($config == 1) {
			// 	$data = array(
			// 		'email' => $input['email'],
			// 		'name'	=> ucwords($contact->first_name),
			// 		'context'	=> "Forgot your company password?",
			// 		'activeLink'	=> $server.'/app/resetcompanypassword?token='.$reset_link
			// 	);
			// 	$url = "https://api.medicloud.sg/hr/reset_pass";
			// 	// $url = "http://localhost:3000/hr/reset_pass";
			// 	return ApiHelper::resetPassword($data, $url);
			// } else {
			$emailDdata['emailName']= ucwords($contact->first_name);
	      		// $emailDdata['emailPage']= 'email-templates.hr-password-reset';
			$emailDdata['emailPage']= 'email-templates.latest-templates.global-reset-password-template';
			$emailDdata['email']= $input['email'];
	      		// $emailDdata['password']= $password;
			$emailDdata['name'] = ucwords($contact->first_name).' '.ucwords($contact->last_name);
			$emailDdata['context'] = "Forgot your company password?";
			$emailDdata['emailSubject'] = 'HR/Benefits Password Reset';
			$emailDdata['activeLink'] = $server.'/app/resetcompanypassword?token='.$reset_link;
			$emailDdata['emailTo']= $input['email'];
				EmailHelper::sendEmail($emailDdata);

			if($contact) {
				if((int)$contact->send_email_communication == 1 && $contact->work_email) {
					$emailDdata['emailTo']= $contact->work_email;
					EmailHelper::sendEmail($emailDdata);
				}
			}

			if($billing_contact) {
				if((int)$billing_contact->send_email_communication == 1 && $billing_contact->billing_email) {
					$emailDdata['emailTo']= $billing_contact->billing_email;
					EmailHelper::sendEmail($emailDdata);
				}
			}

			if(sizeof($contacts) > 0) {
				foreach ($contacts as $key => $cont) {
					if((int)$cont->send_email_communication == 1 && $cont->email) {
						$emailDdata['emailTo']= $cont->email;
						EmailHelper::sendEmail($emailDdata);
					}
				}
			}

			// }
		}

		return array(
			'status'	=> TRUE,
			'message'	=> 'Instruction for Reset Password sent to your email account.'
		);
	}

	public function getHrPasswordTokenDetails($token)
	{
		$check = DB::table('customer_hr_dashboard')->where('reset_link', $token)->first();

		if($check) {
			return array('status' => TRUE, 'data' => $check->hr_dashboard_id);
		}

		return array('status' => FALSE, 'message' => 'Token expired.');
	}

	public function getTokenDetails( )
	{
		$input = Input::all();

		if(empty($input['token']) || $input['token'] == null) {
			return array('status' => false, 'message' => 'token required.');
		}
		$check = DB::table('customer_hr_dashboard')->where('reset_link', $input['token'])->first();

		if($check) {
			$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $check->customer_buy_start_id)->first();
			$agree_status = $customer->agree_status == "true" ? true : false;
			if($check->active == 1)	{
				// create token
				$jwt = new JWT();
				$secret = Config::get('config.secret_key');
				$check->signed_in = FALSE;
				$check->expire_in = strtotime('+15 days', time());
				
				$token = $jwt->encode($check, $secret);
				return array('status' => true, 'data' => ['hr_dashboard_id' => $check->hr_dashboard_id, 'valid_token' => true, 'activated' => true, 't_c' => $agree_status]);
			}

			// check if token is still valid
			$today = strtotime(date('Y-m-d H:i:s'));
			$expiry = strtotime($check->expiration_time);

			if($today > $expiry) {
				return array('status' => false, 'message' => 'token is expired', 'data' => ['valid_token' => true, 'activated' => false, 'expired_token' => true, 't_c' => $agree_status]);
			}

			return array('status' => true, 'data' => ['hr_dashboard_id' => $check->hr_dashboard_id, 'valid_token' => true, 'activated' => false, 'expired_token' => false, 't_c' => $agree_status]);
		}

		return array('status' => false, 'message' => 'Token expired.', 'data' => ['valid_token' => false, 'activated' => false, 'expired_token' => true]);
	}

	public function resetPasswordData( )
	{
		$input = Input::all();
		$hr  = new HRDashboard();

		$data = array(
			'password'	=> md5($input['new_password']),
			'reset_link' => NULL
		);

		$result = $hr->updateCorporateHrDashboard($input['hr_id'], $data);

		if($result) {
			return array('status' => TRUE, 'message' => 'Password Successfully Changed.');
		}

		return array('status' => FALSE, 'message' => 'Something went wrong.');
	}

	public function testGetExcel( )
	{
		$rules = array(
			'file' => 'required|mimes:xlsx,xls|max:200000'
		);

		if(Input::hasFile('file'))
		{
			$validator = \Validator::make( Input::all() , $rules);
			if($validator->passes()){
				$file = Input::file('file');
				$temp_file = time().$file->getClientOriginalName();
				$file->move('excel_upload', $temp_file);
				$data_array = Excel::load(public_path()."/excel_upload/".$temp_file)->ignoreEmpty(true)->get();


			}

			return $data_array;

		}
	}

	public function allocateEmployeeCredits( )
	{
		$input = Input::all();
		$result = self::checkSession();
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_id = $result->hr_dashboard_id;

		if(empty($input['user_id'])) {
			return array('status' => FALSE, 'message' => 'Please select an employee to allocate/deduct credits.');
		}

		if(empty($input['credits'])) {
			return array('status' => FALSE, 'message' => 'Please input credits.');
		}

		if(empty($input['spending_type'])) {
			return array('status' => FALSE, 'message' => 'Please specify Medical or Spending wallet.');
		}

		$check_user = DB::table('user')->where('UserID', $input['user_id'])->count();
		if($check_user == 0) {
			return array('status' => FALSE, 'message' => 'Employee does not exist.');
		}

		// check if credits is greater than zero
		if($input['credits'] <= 0) {
			return array('status' => FALSE, 'message' => 'Credits should be greater than zero.');
		}

		// check company credits
		$check_company = DB::table('customer_buy_start')->where('customer_buy_start_id', $result->customer_buy_start_id)->count();
		if($check_company == 0) {
			return array('status' => FALSE, 'message' => 'Company does not exist.');
		}

		$company_credits = DB::table('customer_credits')->where('customer_id', $result->customer_buy_start_id)->first();
		$customer_id = $result->customer_buy_start_id;
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
		$wallet = new Wallet( );
		$wallet_id = $wallet->getWalletId($input['user_id']);
		$employee_credits_left = DB::table('e_wallet')->where('wallet_id', $wallet_id)->first();

		$user_plan_history = DB::table('user_plan_history')
		->where('user_id', $input['user_id'])
		->orderBy('created_at', 'desc')
		->where('type', 'started')
		->first();
		// check what type of spending account
		if($input['spending_type'] == "medical") {

			if($input['allocation_type'] == "add") {
				$customer_credit_logs = new CustomerCreditLogs( );
				// if($company_credits->balance >= $input['credits']) {
					$result_customer_active_plan = self::allocateCreditBaseInActivePlan($result->customer_buy_start_id, $input['credits'], "medical");

					if($result_customer_active_plan) {
						$customer_active_plan_id = $result_customer_active_plan;
					} else {
						$customer_active_plan_id = FALSE;
					}

					if($input['credits'] > $company_credits->balance) {
						$customer_credits_result = DB::table('customer_credits')->where('customer_id', $customer_id)->increment("balance", $input['credits']);
						if($customer_credits_result) {
							// credit log for wellness
							$customer_credits_logs = array(
								'customer_credits_id'	=> $company_credits->customer_credits_id,
								'credit'				=> $input['credits'],
								'logs'					=> 'admin_added_credits',
								'running_balance'		=> $company_credits->balance + $credits,
								'customer_active_plan_id' => $customer_active_plan_id,
								'currency_type'	=> $company_credits->currency_type
							);

							$customer_credit_logs->createCustomerCreditLogs($customer_credits_logs);
						}
						$company_credits = DB::table('customer_credits')->where('customer_id', $customer_id)->first();
					}
					$wallet_result = $wallet->addCredits($input['user_id'], $input['credits']);
					// return $wallet_result;
					$wallet_id = $wallet->getWalletId($input['user_id']);
					$employee_credits_left = DB::table('e_wallet')->where('wallet_id', $wallet_id)->first();

					if($wallet_result) {
						// deduct company credits and save to logs
						$customer_credits = new CustomerCredits();
						$customer_credits_result = $customer_credits->deductCustomerCredits($company_credits->customer_credits_id, $input['credits']);
						$customer_credits_left = DB::table('customer_credits')->where('customer_credits_id', $company_credits->customer_credits_id)->first();
						// return $customer_credits_result;
						if($customer_credits_result) {
							$company_deduct_logs = array(
								'customer_credits_id'	=> $company_credits->customer_credits_id,
								'credit'							=> $input['credits'],
								'logs'								=> 'added_employee_credits',
								'user_id'							=> $input['user_id'],
								'running_balance'			=> $customer_credits_left->balance,
								'customer_active_plan_id' => $customer_active_plan_id,
								'currency_type'	=> $customer->currency_type
							);

							
							$customer_credit_logs->createCustomerCreditLogs($company_deduct_logs);

							$employee_credits_logs = array(
								'wallet_id'	=> $wallet_id,
								'credit'		=> $input['credits'],
								'logs'			=> 'added_by_hr',
								'running_balance' => $employee_credits_left->balance,
								'customer_active_plan_id' => $customer_active_plan_id,
								'currency_type'	=> $customer->currency_type
							);

							$employee_logs = new WalletHistory();
							$employee_logs->createWalletHistory($employee_credits_logs);

							if($admin_id) {
								$admin_logs = array(
									'admin_id'  => $admin_id,
									'admin_type' => 'mednefits',
									'type'      => 'admin_hr_employee_allocate_credits',
									'data'      => SystemLogLibrary::serializeData($input)
								);
								SystemLogLibrary::createAdminLog($admin_logs);
							} else {
								$admin_logs = array(
									'admin_id'  => $hr_id,
									'admin_type' => 'hr',
									'type'      => 'admin_hr_employee_allocate_credits',
									'data'      => SystemLogLibrary::serializeData($input)
								);
								SystemLogLibrary::createAdminLog($admin_logs);
							}

							return array(
								'status'	=> TRUE,
								'message'	=> 'Employee successfully assigned medical credits '.strtoupper($customer->currency_type)." ".number_format($input['credits'], 2).'.'
							);
						}
					}
				// } else {
				// 	return array(
				// 		'status'	=> FALSE,
				// 		'message'	=> 'Company medical credits is not enough to assign to employee.'
				// 	);
				// }
			} else {
				if($input['credits'] > $employee_credits_left->balance) {
					return array('status' => FALSE, 'message' => "Insufficient medical balance credits to deduct.");
				}

				
				// deduct credit logic
				$employee_logs = new WalletHistory();
				$employee_credits_logs = array(
					'wallet_id'	=> $wallet_id,
					'credit'		=> $input['credits'],
					'logs'			=> 'deducted_by_hr',
					'running_balance' => $employee_credits_left->balance - $input['credits'],
					'customer_active_plan_id' => $user_plan_history->customer_active_plan_id,
					'currency_type'	=> $customer->currency_type
				);

				try {
					$deduct_history = $employee_logs->createWalletHistory($employee_credits_logs);
					$wallet_history_id = $deduct_history->id;

					try {
						$wallet_result = $wallet->deductCredits($input['user_id'], $input['credits']);
						if($admin_id) {
							$admin_logs = array(
								'admin_id'  => $admin_id,
								'admin_type' => 'mednefits',
								'type'      => 'admin_hr_employee_allocate_credits',
								'data'      => SystemLogLibrary::serializeData($input)
							);
							SystemLogLibrary::createAdminLog($admin_logs);
						} else {
							$admin_logs = array(
								'admin_id'  => $hr_id,
								'admin_type' => 'hr',
								'type'      => 'admin_hr_employee_allocate_credits',
								'data'      => SystemLogLibrary::serializeData($input)
							);
							SystemLogLibrary::createAdminLog($admin_logs);
						}
						return array(
							'status'	=> TRUE,
							'message'	=> 'Employee successfully deducted medical credits '.strtoupper($customer->currency_type)." ".number_format($input['credits'], 2).'.'
						);
					} catch(Exception $e) {
						$email = [];
						$email['end_point'] = url('hr/employee/deduct_credits', $parameter = array(), $secure = null);
						$email['logs'] = 'HR Platform Deduct Medical Credits - '.$e->getMessage();
						$email['emailSubject'] = 'Error log. - Wallet History ID: '.$wallet_history_id;
					    // delete failed wallet history
						$employee_logs->deleteFailedWalletHistory($wallet_history_id);
						EmailHelper::sendErrorLogs($email);

						return array('status' => FALSE, 'message' => 'Failed to deduct medical credits to this employee. Please contact Mednefits and report the issue.');
					}


				} catch(Exception $e) {
					$email = [];
					$email['end_point'] = url('hr/employee/deduct_credits', $parameter = array(), $secure = null);	
					$email['logs'] = 'HR Platform Deduct Medical Credits - '.$e->getMessage();
					$email['emailSubject'] = 'Error log. - Wallet History ID: '.$wallet_history_id;
				    // delete failed wallet history
					$employee_logs->deleteFailedWalletHistory($wallet_history_id);
					EmailHelper::sendErrorLogs($email);
				}
			}

		} else if($input['spending_type'] == "wellness") {
			if($input['allocation_type'] == "add") {
				$customer_credit_logs = new CustomerWellnessCreditLogs( );
				// if($company_credits->wellness_credits >= $input['credits']) {
					$result_customer_active_plan = self::allocateCreditBaseInActivePlan($result->customer_buy_start_id, $input['credits'], "wellness");

					if($result_customer_active_plan) {
						$customer_active_plan_id = $result_customer_active_plan;
					} else {
						$customer_active_plan_id = FALSE;
					}
					$customer_credit_logs = new CustomerWellnessCreditLogs( );
					if($input['credits'] > $company_credits->wellness_credits) {
						$customer_credits_result = DB::table('customer_credits')->where('customer_id', $customer_id)->increment("wellness_credits", $input['credits']);
						if($customer_credits_result) {
							// credit log for wellness
							$customer_wellness_credits_logs = array(
								'customer_credits_id'	=> $company_credits->customer_credits_id,
								'credit'				=> $input['credits'],
								'logs'					=> 'admin_added_credits',
								'running_balance'		=> $company_credits->wellness_credits + $input['credits'],
								'customer_active_plan_id' => $customer_active_plan_id,
								'currency_type'	=> $company_credits->currency_type
							);

							$customer_credit_logs->createCustomerWellnessCreditLogs($customer_wellness_credits_logs);
						}
						$company_credits = DB::table('customer_credits')->where('customer_id', $customer_id)->first();
					}

					$wallet_result = $wallet->addWellnessCredits($input['user_id'], $input['credits']);
					$wallet_id = $wallet->getWalletId($input['user_id']);
					$employee_credits_left = DB::table('e_wallet')->where('wallet_id', $wallet_id)->first();

					if($wallet_result) {
						// deduct company credits and save to logs
						$customer_credits = new CustomerCredits();
						$customer_credits_result = $customer_credits->deductCustomerWellnessCredits($company_credits->customer_credits_id, $input['credits']);
						$customer_credits_left = DB::table('customer_credits')->where('customer_credits_id', $company_credits->customer_credits_id)->first();

						if($customer_credits_result) {
							$company_deduct_logs = array(
								'customer_credits_id'	=> $company_credits->customer_credits_id,
								'credit'				=> $input['credits'],
								'logs'					=> 'added_employee_credits',
								'user_id'				=> $input['user_id'],
								'running_balance'		=> $customer_credits_left->wellness_credits,
								'customer_active_plan_id' => $customer_active_plan_id,
								'currency_type'	=> $customer->currency_type
							);

							
							$customer_credit_logs->createCustomerWellnessCreditLogs($company_deduct_logs);

							$employee_credits_logs = array(
								'wallet_id'	=> $wallet_id,
								'credit'		=> $input['credits'],
								'logs'			=> 'added_by_hr',
								'running_balance' => $employee_credits_left->wellness_balance,
								'customer_active_plan_id' => $customer_active_plan_id,
								'currency_type'	=> $customer->currency_type
							);

							\WellnessWalletHistory::create($employee_credits_logs);
							if($admin_id) {
								$admin_logs = array(
									'admin_id'  => $admin_id,
									'admin_type' => 'mednefits',
									'type'      => 'admin_hr_employee_allocate_credits',
									'data'      => SystemLogLibrary::serializeData($input)
								);
								SystemLogLibrary::createAdminLog($admin_logs);
							} else {
								$admin_logs = array(
									'admin_id'  => $hr_id,
									'admin_type' => 'hr',
									'type'      => 'admin_hr_employee_allocate_credits',
									'data'      => SystemLogLibrary::serializeData($input)
								);
								SystemLogLibrary::createAdminLog($admin_logs);
							}
							return array(
								'status'	=> TRUE,
								'message'	=> 'Employee successfully assigned wellness credits ' . strtoupper($customer->currency_type) . ' ' .number_format($input['credits'], 2).'.'
							);
						}
					}
				// } else {
				// 	return array(
				// 		'status'	=> FALSE,
				// 		'message'	=> 'Company wellness credits is not enough to assign to employee.'
				// 	);
				// }
			} else {
				if($input['credits'] > $employee_credits_left->wellness_balance) {
					return array('status' => FALSE, 'message' => "Insufficient wellness balance credits to deduct.");
				}

				// deduct credit logic
				$employee_credits_logs = array(
					'wallet_id'	=> $wallet_id,
					'credit'		=> $input['credits'],
					'logs'			=> 'deducted_by_hr',
					'running_balance' => $employee_credits_left->wellness_balance - $input['credits'],
					'customer_active_plan_id' => $user_plan_history->customer_active_plan_id,
					'currency_type'	=> $customer->currency_type
				);

				try {
					$deduct_history = \WellnessWalletHistory::create($employee_credits_logs);
					$wellness_wallet_history_id = $deduct_history->id;

					try {
						$wallet_result = $wallet->addWellnessCredits($input['user_id'], $input['credits']);
						if($admin_id) {
							$admin_logs = array(
								'admin_id'  => $admin_id,
								'admin_type' => 'mednefits',
								'type'      => 'admin_hr_employee_allocate_credits',
								'data'      => SystemLogLibrary::serializeData($input)
							);
							SystemLogLibrary::createAdminLog($admin_logs);
						} else {
							$admin_logs = array(
								'admin_id'  => $hr_id,
								'admin_type' => 'hr',
								'type'      => 'admin_hr_employee_allocate_credits',
								'data'      => SystemLogLibrary::serializeData($input)
							);
							SystemLogLibrary::createAdminLog($admin_logs);
						}
						return array(
							'status'	=> TRUE,
							'message'	=> 'Employee successfully deducted wellness credits $'.number_format($input['credits'], 2).'.'
						);
					} catch(Exception $e) {
						$email = [];
						$email['end_point'] = url('hr/employee/deduct_credits', $parameter = array(), $secure = null);
						$email['logs'] = 'HR Platform Deduct Wellness Credits - '.$e->getMessage();
						$email['emailSubject'] = 'Error log. - Wallet History ID: '.$wellness_wallet_history_id;
					    // delete failed wallet history
						\WellnessWalletHistory::where('wellness_wallet_history_id', $wellness_wallet_history_id)->delete();
						EmailHelper::sendErrorLogs($email);

						return array('status' => FALSE, 'message' => 'Failed to deduct wellness credits to this employee. Please contact Mednefits and report the issue.');
					}


				} catch(Exception $e) {
					$email = [];
					$email['end_point'] = url('hr/employee/deduct_credits', $parameter = array(), $secure = null);	
					$email['logs'] = 'HR Platform Deduct Wellness Credits - '.$e->getMessage();
					$email['emailSubject'] = 'Error log. - Wallet History ID: '.$wallet_history_id;
				    // delete failed wallet history
					$employee_logs->deleteFailedWalletHistory($wallet_history_id);
					EmailHelper::sendErrorLogs($email);
				}
			}
		} else {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Please choose a spending account type ["medical", "wellness"]'
			);
		}

		

		return array(
			'status'	=> FALSE,
			'message'	=> 'Error.'
		);
	}

	public function deductEmployeeCredits( )
	{
		$input = Input::all();
		$result = self::checkSession();
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_id = $result->hr_dashboard_id;

		$check_user = DB::table('user')->where('UserID', $input['user_id'])->count();
		if($check_user == 0) {
			return array('status' => FALSE, 'message' => 'Employee does not exist.');
		}

		// check company credits
		$check_company = DB::table('customer_buy_start')->where('customer_buy_start_id', $result->customer_buy_start_id)->count();
		if($check_company == 0) {
			return array('status' => FALSE, 'message' => 'Company does not exist.');
		}

		$user_plan_history = DB::table('user_plan_history')
		->where('user_id', $input['user_id'])
		->orderBy('created_at', 'desc')
		->where('type', 'started')
		->first();
		// get user balance
		$wallet = new Wallet( );
		$wallet_id = $wallet->getWalletId($input['user_id']);
		$employee_credits_left = DB::table('e_wallet')->where('wallet_id', $wallet_id)->first();

		if($input['spending_type'] == "medical") {
			if($input['credits'] > $employee_credits_left->balance) {
				return array('status' => FALSE, 'message' => "Insufficient medical balance credits to deduct.");
			}

			// deduct credit logic
			$employee_logs = new WalletHistory();
			$employee_credits_logs = array(
				'wallet_id'	=> $wallet_id,
				'credit'		=> $input['credits'],
				'logs'			=> 'deducted_by_hr',
				'running_balance' => $employee_credits_left->balance - $input['credits'],
				'customer_active_plan_id' => $user_plan_history->customer_active_plan_id
			);

			try {
				$deduct_history = $employee_logs->createWalletHistory($employee_credits_logs);
				$wallet_history_id = $deduct_history->id;

				try {
					$wallet_result = $wallet->deductCredits($input['user_id'], $input['credits']);
					if($admin_id) {
						$admin_logs = array(
							'admin_id'  => $admin_id,
							'admin_type' => 'mednefits',
							'type'      => 'admin_hr_employee_deducted_credits',
							'data'      => SystemLogLibrary::serializeData($input)
						);
						SystemLogLibrary::createAdminLog($admin_logs);
					} else {
						$admin_logs = array(
							'admin_id'  => $hr_id,
							'admin_type' => 'hr',
							'type'      => 'admin_hr_employee_deducted_credits',
							'data'      => SystemLogLibrary::serializeData($input)
						);
						SystemLogLibrary::createAdminLog($admin_logs);
					}
					return array(
						'status'	=> TRUE,
						'message'	=> 'Employee successfully deducted medical credits $'.number_format($input['credits'], 2).'.'
					);
				} catch(Exception $e) {
					$email = [];
					$email['end_point'] = url('hr/employee/deduct_credits', $parameter = array(), $secure = null);
					$email['logs'] = 'HR Platform Deduct Medical Credits - '.$e->getMessage();
					$email['emailSubject'] = 'Error log. - Wallet History ID: '.$wallet_history_id;
				    // delete failed wallet history
					$employee_logs->deleteFailedWalletHistory($wallet_history_id);
					EmailHelper::sendErrorLogs($email);

					return array('status' => FALSE, 'message' => 'Failed to deduct medical credits to this employee. Please contact Mednefits and report the issue.');
				}


			} catch(Exception $e) {
				$email = [];
				$email['end_point'] = url('hr/employee/deduct_credits', $parameter = array(), $secure = null);	
				$email['logs'] = 'HR Platform Deduct Medical Credits - '.$e->getMessage();
				$email['emailSubject'] = 'Error log. - Wallet History ID: '.$wallet_history_id;
			    // delete failed wallet history
				$employee_logs->deleteFailedWalletHistory($wallet_history_id);
				EmailHelper::sendErrorLogs($email);
			}
		} else if($input['spending_type'] == "wellness") {
			if($input['credits'] > $employee_credits_left->wellness_balance) {
				return array('status' => FALSE, 'message' => "Insufficient wellness balance credits to deduct.");
			}

			// deduct credit logic
			$employee_credits_logs = array(
				'wallet_id'	=> $wallet_id,
				'credit'		=> $input['credits'],
				'logs'			=> 'deducted_by_hr',
				'running_balance' => $employee_credits_left->wellness_balance - $input['credits'],
				'customer_active_plan_id' => $user_plan_history->customer_active_plan_id
			);

			try {
				$deduct_history = \WellnessWalletHistory::create($employee_credits_logs);
				$wellness_wallet_history_id = $deduct_history->id;

				try {
					$wallet_result = $wallet->addWellnessCredits($input['user_id'], $input['credits']);
					if($admin_id) {
						$admin_logs = array(
							'admin_id'  => $admin_id,
							'admin_type' => 'mednefits',
							'type'      => 'admin_hr_employee_deducted_credits',
							'data'      => SystemLogLibrary::serializeData($input)
						);
						SystemLogLibrary::createAdminLog($admin_logs);
					} else {
						$admin_logs = array(
							'admin_id'  => $hr_id,
							'admin_type' => 'hr',
							'type'      => 'admin_hr_employee_deducted_credits',
							'data'      => SystemLogLibrary::serializeData($input)
						);
						SystemLogLibrary::createAdminLog($admin_logs);
					}
					return array(
						'status'	=> TRUE,
						'message'	=> 'Employee successfully deducted wellness credits $'.number_format($input['credits'], 2).'.'
					);
				} catch(Exception $e) {
					$email = [];
					$email['end_point'] = url('hr/employee/deduct_credits', $parameter = array(), $secure = null);
					$email['logs'] = 'HR Platform Deduct Wellness Credits - '.$e->getMessage();
					$email['emailSubject'] = 'Error log. - Wallet History ID: '.$wellness_wallet_history_id;
				    // delete failed wallet history
					\WellnessWalletHistory::where('wellness_wallet_history_id', $wellness_wallet_history_id)->delete();
					EmailHelper::sendErrorLogs($email);

					return array('status' => FALSE, 'message' => 'Failed to deduct wellness credits to this employee. Please contact Mednefits and report the issue.');
				}


			} catch(Exception $e) {
				$email = [];
				$email['end_point'] = url('hr/employee/deduct_credits', $parameter = array(), $secure = null);	
				$email['logs'] = 'HR Platform Deduct Wellness Credits - '.$e->getMessage();
				$email['emailSubject'] = 'Error log. - Wallet History ID: '.$wallet_history_id;
			    // delete failed wallet history
				$employee_logs->deleteFailedWalletHistory($wallet_history_id);
				EmailHelper::sendErrorLogs($email);
			}
		} else {
			return array('status' => FALSE, 'message' => 'Failed to deduct credits to this employee. Please contact Mednefits and report the issue.');
		}


	}

	public function searchCompanyEmployeeCredits( )
	{
		$result = self::checkSession();
		$input = Input::all();
		$customer_id = $result->customer_buy_start_id;
		$search = $input['search'];
		$final_user = [];
		$paginate = [];

		$allocated = 0;
		$total_allocation = 0;
		$deleted_employee_allocation = 0;
		$total_deduction_credits = 0;

		$users = DB::table('user')
		->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
		->join('corporate', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
		->join('customer_link_customer_buy', 'customer_link_customer_buy.corporate_id', '=', 'corporate.corporate_id')
		->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_link_customer_buy.customer_buy_start_id')
		->where(function($query) use ($search, $customer_id){
			$query->where('customer_buy_start.customer_buy_start_id', $customer_id)
			->where('user.Active', 1)
			->where('user.Name', 'like', '%'.$search.'%');
		})
		->orWhere(function($query) use ($search, $customer_id){
			$query->where('customer_buy_start.customer_buy_start_id', $customer_id)
			->where('user.Active', 1)
			->where('user.NRIC', 'like', '%'.$search.'%');
		})
		->select('user.UserID', 'user.Name', 'user.Email', 'user.NRIC', 'user.PhoneNo', 'user.Job_Title', 'user.DOB', 'user.created_at', 'corporate.company_name', 'corporate_members.removed_status')
		->get();

		foreach ($users as $key => $user) {
			$get_allocation = 0;
			$get_allocation_spent = 0;

			$wallet = DB::table('e_wallet')->where('UserID', $user->UserID)->orderBy('created_at', 'desc')->first();

			$get_allocation = DB::table('e_wallet')
			->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user->UserID)
			->where('wallet_history.logs', 'added_by_hr')
			->sum('wallet_history.credit');
			$deduct_allocation = DB::table('e_wallet')
			->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user->UserID)
			->where('wallet_history.logs', 'deducted_by_hr')
			->sum('wallet_history.credit');
			if($user->removed_status == 1) {
				$deleted_employee_allocation += $get_allocation - $deduct_allocation;
			}

			$e_claim_spent = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'e_claim_transaction')
			->sum('credit');

			$in_network_temp_spent = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'in_network_transaction')
			->sum('credit');
			$credits_back = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'credits_back_from_in_network')
			->sum('credit');
			$get_allocation_spent = $in_network_temp_spent - $credits_back + $e_claim_spent;

			// get credits allocation
			// if($get_allocation) {
			$allocation = $get_allocation - $deduct_allocation;
			// } else {
			// 	$allocation = number_format($user->balance, 2, '.', '');
			// }
			// get transaction
			// $get_allocation_spent = DB::table('employee_credit_spend_logs')->where('owner_id', $user->UserID)->sum('credit_spent');
			$allocated += $allocation;

			if($user->removed_status == 0 || $user->removed_status == "0") {
				$get_employee_plan = DB::table('user_plan_type')->where('user_id', $user->UserID)->orderBy('created_at', 'desc')->first();
				$temp = array(
					'allocation'			=> array(
						'credits_allocation' => number_format($allocation, 2), 
						'credits_spent' => number_format($get_allocation_spent, 2)
					),
					'name'						=> $user->Name,
					'email'						=> $user->Email,
					'enrollment_date' => $user->created_at,
					'wallet_id'				=> $wallet->wallet_id,
					'credits'					=> number_format($wallet->balance, 2),
					'user_id'					=> $user->UserID,
					'nric'						=> $user->NRIC,
					'phone_no'				=> $user->PhoneNo,
					'job_title'				=> $user->Job_Title,
					'dob'							=> $user->DOB,
					'company'					=> ucwords($user->company_name),
					'employee_plan'		=> $get_employee_plan
				);
				array_push($final_user, $temp);
			}
		}

		$paginate['data'] = $final_user;
		return $paginate;
	}

	public function getAllUserWithCredits($per_page)
	{
		$result = self::checkSession();

		// $active_plan = DB::table('customer_active_plan')->where('customer_start_buy_id', $result->customer_buy_start_id)->where('status', "true")->first();
		$final_user = [];
		$paginate = [];

		$allocated = 0;
		$total_allocation = 0;
		$deleted_employee_allocation = 0;
		$total_deduction_credits = 0;

		$users = DB::table('user')
		->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
		->join('corporate', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
		->join('customer_link_customer_buy', 'customer_link_customer_buy.corporate_id', '=', 'corporate.corporate_id')
		->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_link_customer_buy.customer_buy_start_id')
		->where('customer_buy_start.customer_buy_start_id', $result->customer_buy_start_id)
		->where('user.Active', 1)
		->select('user.UserID', 'user.Name', 'user.Email', 'user.NRIC', 'user.PhoneNo', 'user.Job_Title', 'user.DOB', 'user.created_at', 'corporate.company_name', 'corporate_members.removed_status')
		->paginate($per_page);

		$paginate['last_page'] = $users->getLastPage();
		$paginate['current_page'] = $users->getCurrentPage();
		$paginate['total_data'] = $users->getTotal();
		$paginate['from'] = $users->getFrom();
		$paginate['to'] = $users->getTo();
		$paginate['count'] = $users->count();

		foreach ($users as $key => $user) {
			$get_allocation = 0;
			$get_allocation_spent = 0;

			$wallet = DB::table('e_wallet')->where('UserID', $user->UserID)->orderBy('created_at', 'desc')->first();

			$get_allocation = DB::table('e_wallet')
			->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user->UserID)
			->where('wallet_history.logs', 'added_by_hr')
			->sum('wallet_history.credit');
			$deduct_allocation = DB::table('e_wallet')
			->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user->UserID)
			->where('wallet_history.logs', 'deducted_by_hr')
			->sum('wallet_history.credit');
			if($user->removed_status == 1) {
				$deleted_employee_allocation += $get_allocation - $deduct_allocation;
			}

			$e_claim_spent = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'e_claim_transaction')
			->sum('credit');

			$in_network_temp_spent = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'in_network_transaction')
			->sum('credit');
			$credits_back = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'credits_back_from_in_network')
			->sum('credit');
			$get_allocation_spent = $in_network_temp_spent - $credits_back + $e_claim_spent;

			// get credits allocation
			// if($get_allocation) {
			$allocation = $get_allocation - $deduct_allocation;
			// } else {
			// 	$allocation = number_format($user->balance, 2, '.', '');
			// }
			// get transaction
			// $get_allocation_spent = DB::table('employee_credit_spend_logs')->where('owner_id', $user->UserID)->sum('credit_spent');
			$allocated += $allocation;

			if($user->removed_status == 0 || $user->removed_status == "0") {
				$get_employee_plan = DB::table('user_plan_type')->where('user_id', $user->UserID)->orderBy('created_at', 'desc')->first();
				$temp = array(
					'allocation'			=> array(
						'credits_allocation' => number_format($allocation, 2), 
						'credits_spent' => number_format($get_allocation_spent, 2)
					),
					'name'						=> $user->Name,
					'email'						=> $user->Email,
					'enrollment_date' => $user->created_at,
					'wallet_id'				=> $wallet->wallet_id,
					'credits'					=> number_format($wallet->balance, 2),
					'user_id'					=> $user->UserID,
					'nric'						=> $user->NRIC,
					'phone_no'				=> $user->PhoneNo,
					'job_title'				=> $user->Job_Title,
					'dob'							=> $user->DOB,
					'company'					=> ucwords($user->company_name),
					'employee_plan'		=> $get_employee_plan
				);
				array_push($final_user, $temp);
			}
		}

		// check company credits or update
		// $credits = $total_allocation - $allocated;
		// if($company_credits->balance != $credits) {
		// 	// update
		// 	\CustomerCredits::where('customer_id', $result->customer_buy_start_id)->update(['balance' => $credits]);
		// }
		$paginate['data'] = $final_user;
		return $paginate;
		// return array(

		// 			'users' => $final_user,
		// 			// 'allocated' => number_format($allocated - $deleted_employee_allocation, 2),
		// 			// 'total_allocated' => number_format($total_allocation, 2),
		// 			// 'company_id' => $result->customer_buy_start_id,
		// 			// 'company_credits' => $total_allocation - $allocated,
		// 			'total_deduction_credits' => $total_deduction_credits
		// 		);
	}

	public function checkCompanyCredits( )
	{
		$result = self::checkSession();

		$allocated = 0;
		$total_allocation = 0;
		$deleted_employee_allocation = 0;
		$total_deduction_credits = 0;

		$company_credits = DB::table('customer_credits')->where('customer_id', $result->customer_buy_start_id)->first();

		$total_allocation = DB::table('customer_credits')
		->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
		->where('customer_credits.customer_id', $result->customer_buy_start_id)
		->where('customer_credit_logs.logs', 'admin_added_credits')
		->sum('customer_credit_logs.credit');
		$account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

		$users = DB::table('user')
		->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
		->join('corporate', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
		->where('corporate.corporate_id', $account_link->corporate_id)
		->where('corporate_members.removed_status', 0)
		->select('user.UserID', 'user.Name', 'user.Email', 'user.NRIC', 'user.PhoneNo', 'user.Job_Title', 'user.DOB', 'user.created_at', 'corporate.company_name', 'corporate_members.removed_status')
		->get();

		foreach ($users as $key => $user) {
			$get_allocation = 0;
			$deducted_credits = 0;
			$deducted_allocation = 0;
			$get_allocation_spent = 0;
			$e_claim_spent = 0;
			$in_network_temp_spent = 0;
			$credits_back = 0;

			$wallet = DB::table('e_wallet')->where('UserID', $user->UserID)->orderBy('created_at', 'desc')->first();
			// $get_allocation = DB::table('e_wallet')
		//                              ->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
		//                              ->where('e_wallet.UserID', $user->UserID)
		//                              ->where('wallet_history.logs', 'added_by_hr')
		//                              ->sum('wallet_history.credit');
		//    $deduct_allocation = DB::table('e_wallet')
		//                              ->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
		//                              ->where('e_wallet.UserID', $user->UserID)
		//                              ->where('wallet_history.logs', 'deducted_by_hr')
		//                              ->sum('wallet_history.credit');
		//    $total_deduction_credits += $deduct_allocation;
		//    if($user->removed_status == 1) {
		//    	$deleted_employee_allocation += $get_allocation - $deduct_allocation;
		//    }

		//     $e_claim_spent = DB::table('wallet_history')
		//                          ->where('wallet_id', $wallet->wallet_id)
		//                          ->where('where_spend', 'e_claim_transaction')
		//                          ->sum('credit');

		//      $in_network_temp_spent = DB::table('wallet_history')
		//                          ->where('wallet_id', $wallet->wallet_id)
		//                          ->where('where_spend', 'in_network_transaction')
		//                          ->sum('credit');
		//      $credits_back = DB::table('wallet_history')
		//                          ->where('wallet_id', $wallet->wallet_id)
		//                          ->where('where_spend', 'credits_back_from_in_network')
		//                          ->sum('credit');
		//      $get_allocation_spent = $in_network_temp_spent - $credits_back + $e_claim_spent;

			// 	$allocation = number_format($get_allocation - $deduct_allocation, 2, '.', '');
			// 	$allocated += $allocation;


			$wallet_history = DB::table('wallet_history')->where('wallet_id', $wallet->wallet_id)->get();

			foreach ($wallet_history as $key => $history) {

				if($history->logs == "added_by_hr") {
					$get_allocation += $history->credit;
				}

				if($history->logs == "deducted_by_hr") {
					$deducted_allocation += $history->credit;
				}

				if($history->where_spend == "e_claim_transaction") {
					$e_claim_spent += $history->credit;
				}

				if($history->where_spend == "in_network_transaction") {
					$in_network_temp_spent += $history->credit;
				}

				if($history->where_spend == "credits_back_from_in_network") {
					$credits_back += $history->credit;
				}
			}

			$total_deduction_credits += $deducted_allocation;

			if($user->removed_status == 1) {
				$deleted_employee_allocation += $get_allocation - $deducted_allocation;
			}

			$get_allocation_spent = $in_network_temp_spent - $credits_back + $e_claim_spent;
			$allocation = $get_allocation;
			$allocated += $allocation;
		}

		// check company credits or update
		$credits = $total_allocation - $allocated;
		if($company_credits->balance != $credits) {
			// update
			\CustomerCredits::where('customer_id', $result->customer_buy_start_id)->update(['balance' => $credits]);
		}

		return array(
			'allocated' => number_format($allocated - $deleted_employee_allocation, 2),
			'total_allocated' => number_format($total_allocation, 2),
			'company_id' => $result->customer_buy_start_id,
			'company_credits' => $credits,
			'total_deduction_credits' => $total_deduction_credits
		);
	}

	public function getCompanyMembers( )
	{
		$session = self::checkSession();

		// get all hr employees, spouse and dependents
		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $session->customer_buy_start_id)->first();

		$corporate_members = DB::table('corporate_members')
		->join('user', 'user.UserID', '=', 'corporate_members.user_id')
		// ->where('corporate_members.removed_status', 0)
		->where('corporate_members.corporate_id', $account->corporate_id)
		->select('user.Name', 'user.Image', 'user.NRIC', 'corporate_members.user_id')
		->get();

		return array('status' => TRUE, 'data' => $corporate_members);
	}

	// remove temp enrollees
	public function removeEnrollees( )
	{
		$input = Input::all();
		$session = self::checkSession();

		$added_purchase_status = Session::get('added_purchase_status');

		if($added_purchase_status) {
			self::flushSessionAddedPurchase();
			return array('status' => TRUE);
		} else {
			$temp = new TempEnrollment( );
			return array('status' => TRUE, 'data' => $temp->removeEnrollees($session->customer_buy_start_id, $input['ids']));
		}

	}

	public function getActivePlanHr( )
	{
		$session = self::checkSession();
		$active_plan = DB::table('customer_active_plan')->where('customer_start_buy_id', $session->customer_buy_start_id)->first();
		return $active_plan->customer_active_plan_id;
	}

	public function newFormatInvoiceNumber( )
	{
		$invoices = DB::table('corporate_invoice')->get();

		$result = [];

		foreach ($invoices as $key => $invoice) {
			$check = 10;
			$invoice_number = str_pad($check + $key, 6, "0", STR_PAD_LEFT);
			$data_invoice = array(
				'invoice_number'					=> 'OMC'.$invoice_number,
			);
			$update = \CorporateInvoice::where('corporate_invoice_id', $invoice->corporate_invoice_id)->update($data_invoice);
			array_push($result, $update);
		}

		return $result;
	}

	public function updateHrPassword( )
    {
        $input = Input::all();

        $session = self::checkSession();
        // get admin session from mednefits admin login
        $admin_id = Session::get('admin-session-id');
        $hr_id = $session->hr_dashboard_id;

        // $checkPassword = DB::table('customer_hr_dashboard')->where('hr_dashboard_id', $session->hr_dashboard_id)->where('password', md5($input['current_password']))->count();

        // if($checkPassword == 0) {
        //     return array('status' => FALSE, 'message' => 'Current Password is invalid.');
        // }

		$result = \HRDashboard::where('hr_dashboard_id', $session->hr_dashboard_id)->update(['password' => md5($input['new_password']), 'password' => md5($input['confirm_password'])]);
		
		if($input['new_password']!=($input['confirm_password'])) {
			return array('status' => FALSE, 'message' => 'Password did not match.');
		}

        if($admin_id) {
            $input['hr_dashboard_id'] = $session->hr_dashboard_id;
            $admin_logs = array(
                'admin_id'  => $admin_id,
                'admin_type' => 'mednefits',
                'type'      => 'admin_hr_updated_account_password',
                'data'      => SystemLogLibrary::serializeData($input)
            );
            SystemLogLibrary::createAdminLog($admin_logs);
        } else {
            $admin_logs = array(
                'admin_id'  => $hr_id,
                'admin_type' => 'hr',
                'type'      => 'admin_hr_updated_account_password',
                'data'      => SystemLogLibrary::serializeData($input)
            );
            SystemLogLibrary::createAdminLog($admin_logs);
        }

        return array('status' => TRUE, 'message' => 'Successfully Update HR Account Password.');
    }


	public function refundDetails()
	{
		$input = Input::all();
		$result = self::checkToken($input['token']);
		$id = $input['id'];
		$users = [];

		
		$link_account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

		$amount_due = 0;
		$unit_price = 0;

		$refund_payment = DB::table('payment_refund')->where('payment_refund_id', $id)->first();	
		if($refund_payment) {
			$company_active_plan = DB::table('customer_active_plan')
			->where('customer_active_plan_id', $refund_payment->customer_active_plan_id)
			->first();
			$company_plan = DB::table('customer_plan')
			->where('customer_plan_id', $company_active_plan->plan_id)
			->first();

			$temp_end_date = date('Y-m-d', strtotime('+1 year', strtotime($company_plan->plan_start)));
			$end_date = date('Y-m-d', strtotime('-1 day', strtotime($temp_end_date)));
			$total_refund = 0;
			$individual_price = 0;
			$withdraws = DB::table('customer_plan_withdraw')->where('payment_refund_id', $id)->whereIn('refund_status', [0,1])->get();
			foreach ($withdraws as $key => $user) {
				if((int)$user->has_no_user == 0) {
					$employee = DB::table('user')->where('UserID', $user->user_id)->first();
					$plan = DB::table('user_plan_type')->where('user_id', $user->user_id)->orderBy('created_at', 'desc')->first();
					$invoice = DB::table('corporate_invoice')
						->where('customer_active_plan_id', $refund_payment->customer_active_plan_id)
						->first();

					if((int)$company_active_plan->new_head_count == 1) {
						$calculated_prices_end_date = PlanHelper::getCompanyPlanDatesByPlan($company_active_plan->customer_start_buy_id, $company_active_plan->plan_id);
						$individual_price = PlanHelper::calculateInvoicePlanPrice($invoice->individual_price, $company_active_plan->plan_start, $calculated_prices_end_date['plan_end']);
    					$individual_price = DecimalHelper::formatDecimal($individual_price);
					} else {
						$individual_price = $invoice->individual_price;
					}
					
					$diff = date_diff(new DateTime(date('Y-m-d', strtotime($plan->plan_start))), new DateTime(date('Y-m-d', strtotime($user->date_withdraw))));
					$days = $diff->format('%a') + 1;
					$total_days = MemberHelper::getMemberTotalDaysSubscription($plan->plan_start, $company_plan->plan_end);
					// $total_days = date("z", mktime(0,0,0,12,31,date('Y')));
					$remaining_days = $total_days - $days + 1;

					$cost_plan_and_days = ($individual_price/$total_days);
					$temp_total = $cost_plan_and_days * $remaining_days;
					$temp_sub_total = $temp_total * 0.70;

					// check withdraw amount
					if($user->amount != $temp_sub_total) {
						// update amount
						\PlanWithdraw::where('plan_withdraw_id', $user->plan_withdraw_id)->update(['amount' => $temp_sub_total]);
					}

					$withdraw_data = DB::table('customer_plan_withdraw')->where('user_id', $user->user_id)->first();
					$total_refund += $temp_sub_total;
					$unit_price = $temp_sub_total;

					$temp = array(
						'user_id'			=> $user->user_id,
						'name'				=> ucwords($employee->Name),
						'nric'				=> $employee->NRIC,
						'period_of_used' => date('d/m/Y', strtotime($plan->plan_start)).' - '.date('d/m/Y', strtotime($user->date_withdraw)),
						'period_of_unused' => date('d/m/Y', strtotime($user->date_withdraw)).' - '.date('d/m/Y', strtotime($end_date)),
						'days_used'			=> $days,
						'first_period_of_unused' => date('d/m/Y', strtotime($user->date_withdraw)),
						'last_period_of_unused' => date('d/m/Y', strtotime($end_date)),
						'remaining_days' => $remaining_days,
						'total_days'		=> $total_days,
						'before_amount'	=> $temp_total,
						'after_amount' => $temp_sub_total,
						'has_no_user'	=> false
					);
				} else {
					$total_refund += $user->amount;
					$unit_price = $user->amount;
					$diff = date_diff(new DateTime(date('Y-m-d', strtotime($user->date_started))), new DateTime(date('Y-m-d', strtotime($user->date_withdraw))));
					$days = $diff->format('%a') + 1;
					$total_days = date("z", mktime(0,0,0,12,31,date('Y'))) + 1;
					$remaining_days = $total_days - $days;

					$temp = array(
						'user_id'			=> null,
						'name'				=> null,
						'nric'				=> null,
						'period_of_used' => date('d/m/Y', strtotime($user->date_started)).' - '.date('d/m/Y', strtotime($user->date_withdraw)),
						'period_of_unused' => date('d/m/Y', strtotime($user->date_withdraw)).' - '.date('d/m/Y', strtotime($end_date)),
						'days_used'			=> $days,
						'first_period_of_unused' => date('d/m/Y', strtotime($user->date_withdraw)),
						'last_period_of_unused' => date('d/m/Y', strtotime($user->unused)),
						'remaining_days' => $remaining_days,
						'total_days'		=> $total_days,
						'before_amount'	=> $user->amount,
						'after_amount' => $user->amount,
						'has_no_user'	=> true
					);
				}

				if($user->paid == 0) {
					$amount_due += $temp['after_amount'];
				}

				array_push($users, $temp);
			}

			$corporate_business_contact = new CorporateBusinessContact();
			$contact = $corporate_business_contact->getCorporateBusinessContact($company_active_plan->customer_start_buy_id);
			$corporate_business_info = new CorporateBusinessInformation();
			$business_info = $corporate_business_info->getCorporateBusinessInfo($company_active_plan->customer_start_buy_id);

			if($contact->billing_contact == "false" || $contact->billing_contact == false) {
				$corporate_billing_contact = new CorporateBillingContact();
				$result_corporate_billing_contact = $corporate_billing_contact->getCorporateBillingContact($company_active_plan->customer_start_buy_id);
				$data['first_name'] = ucwords($result_corporate_billing_contact->first_name);
				$data['last_name']	= ucwords($result_corporate_billing_contact->last_name);
				$data['email']			= $result_corporate_billing_contact->work_email;
				$data['phone']			= $result_corporate_billing_contact->phone;
				$data['billing_contact_status'] = false;
			} else {
				$data['first_name'] = ucwords($contact->first_name);
				$data['last_name']	= ucwords($contact->last_name);
				$data['email']			= $contact->work_email;
				$data['billing_contact_status'] = true;
				$data['phone']     = $contact->phone;
			}

			if($contact->billing_address == "true") {
				$corporate_billing_address = new CorporateBillingAddress();
				$billing_address = $corporate_billing_address->getCorporateBillingAddress($company_active_plan->customer_start_buy_id);
				$data['address'] = $billing_address->billing_address;
				$data['postal'] = $billing_address->postal_code;
				$data['company'] = ucwords($business_info->company_name);
				$data['billing_address_status'] = true;
			} else {
				$data['address'] = $business_info->company_address;
				$data['postal'] = $business_info->postal_code;
				$data['company'] = ucwords($business_info->company_name);
				$data['billing_address_status'] = false;
			}
			
			$refund_data = array(
				'plan_type'		=> $refund_payment->account_type ? PlanHelper::getAccountType($refund_payment->account_type) : PlanHelper::getAccountType($company_plan->account_type),
				'total_refund' => \DecimalHelper::formatDecimal($total_refund),
				'plan_start' => date('d/m/Y', strtotime($company_plan->plan_start)),
				'plan_end'		=>  date('d/m/Y', strtotime($end_date)),
				'amount_due'	=> \DecimalHelper::formatDecimal($amount_due),
				'cancellation_number' => $refund_payment->cancellation_number,
				'paid' => $refund_payment->payment_amount,
				'quantity'	=> sizeof($users),
				'date_refund' => date('d/m/Y', strtotime($refund_payment->date_refund)),
				'unutilised_date' => date('d/m/Y', strtotime('+1 day', strtotime($refund_payment->date_refund))),
				'invoice_date' => $refund_payment->invoice_date ? date('d F Y', strtotime($refund_payment->invoice_date)) : null,
				'invoice_due' => $refund_payment->invoice_due ? date('d F Y', strtotime($refund_payment->invoice_due)) : null,
				'payment_date' => $refund_payment->payment_date ? date('d F Y', strtotime($refund_payment->payment_date)) : null,
				'payment_status' => $refund_payment->status,
				'billing_info' => $data,
				'cancellation_date' => date('F j, Y', strtotime($refund_payment->date_refund)),
				'users' => $users,
				'currency_type' => strtoupper($refund_payment->currency_type),
				'unit_price'	=> \DecimalHelper::formatDecimal($unit_price)
			);
			
			if($refund_payment->account_type == "enterprise_plan")	{
				// return View::make('pdf-download.globalTemplates.cancellation-invoice', $refund_data);
				$pdf = PDF::loadView('pdf-download.globalTemplates.cancellation-invoice', $refund_data);
			} else {
				// return View::make('pdf-download.globalTemplates.cancellation-invoice', $refund_data);
				$pdf = PDF::loadView('pdf-download.globalTemplates.cancellation-invoice', $refund_data);
			}

			
			$pdf->getDomPDF()->get_option('enable_html5_parser');
			$pdf->setPaper('A4', 'portrait');
			return $pdf->stream($refund_data['cancellation_number'].' CANCELLATION - '.time().'.pdf');
		}
	}

	public function checkPlanWithdraw($id)
	{
		$result = self::checkSession();
		$withdraws = DB::table('customer_plan_withdraw')->where('customer_active_plan_id', $id)->get();
		$company_active_plan = DB::table('customer_active_plan')
		->where('customer_active_plan_id', $id)
		->where('account_type', 'stand_alone_plan')
		->first();

		if($company_active_plan) {
			foreach ($withdraws as $key => $user) {
				$employee = DB::table('user')->where('UserID', $user->user_id)->first();
				$plan = DB::table('user_plan_type')->where('user_id', $user->user_id)->orderBy('created_at', 'desc')->first();

				$diff = date_diff(new DateTime(date('Y-m-d', strtotime($plan->plan_start))), new DateTime(date('Y-m-d', strtotime($user->date_withdraw))));
				$days = $diff->format('%a') + 1;

				$total_days = date("z", mktime(0,0,0,12,31,date('Y'))) + 1;
				$remaining_days = $total_days - $days;

				$cost_plan_and_days = (99/$total_days);
				$temp_total = $cost_plan_and_days * $remaining_days;

				$temp_sub_total = $temp_total * 0.70;

				// check withdraw amount
				if($user->amount != $temp_sub_total) {
					// update amount
					\PlanWithdraw::where('plan_withdraw_id', $user->plan_withdraw_id)->update(['amount' => $temp_sub_total]);
				}
			}
		}
	}



	public function formatInvoiceDate( )
	{
		$corporate_invoices = DB::table('corporate_invoice')->get();
		$result = [];

		foreach ($corporate_invoices as $key => $invoice) {
			$check = DB::table('customer_active_plan')->where('customer_active_plan_id', $invoice->customer_active_plan_id)->first();

			if($check) {
				if($check->new_head_count == 1) {
					// update corporate invoice
					$data = array(
						'invoice_date' 	=> $check->created_at,
						'invoice_due'		=> date('Y-m-d', strtotime('+5 days', strtotime($check->created_at)))
					);
				} else {
					$data = array(
						'invoice_date' 	=> $check->created_at,
						'invoice_due'		=> date('Y-m-d', strtotime('-5 days', strtotime($check->plan_start)))
					);
				}

				$result[] = \CorporateInvoice::where('customer_active_plan_id', $invoice->customer_active_plan_id)->update($data);
			}
		}

		return $result;
	}

	public function downloadTransactionReceipt($transaction_id)
	{

		$trans = \TransactionHelper::getTransactionDetails($transaction_id);
		// return View::make('pdf-download.pdf-member-successful-transaction', $trans);
		$pdf = PDF::loadView('pdf-download.pdf-member-successful-transaction', $trans);
		return $pdf->stream();
		// $consultation_cash = false;
		// $consultation_credits = false;
		// $service_cash = false;
		// $service_credits = false;
		// $consultation = 0;
		// $trans = DB::table('transaction_history')->where('transaction_id', $transaction_id)->first();

		// if($trans) {
		// 	if($trans->spending_type == 'medical') {
		// 		$table_wallet_history = 'wallet_history';
		// 	} else {
		// 		$table_wallet_history = 'wellness_wallet_history';
		// 	}
		// 	if($trans->procedure_cost >= 0 && $trans->paid == 1 || $trans->procedure_cost >= 0 && $trans->paid == "1") {
		// 		if($trans->lite_plan_enabled == 1) {
		// 			$logs_lite_plan = DB::table($table_wallet_history)
		// 			->where('logs', 'deducted_from_mobile_payment')
		// 			->where('lite_plan_enabled', 1)
		// 			->where('id', $trans->transaction_id)
		// 			->first();

		// 			if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
		// 				$consultation_credits = true;
		// 				$service_credits = true;
		// 				$consultation = floatval($logs_lite_plan->credit);
		// 			} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
		// 				$consultation_credits = true;
		// 				$service_credits = true;
		// 				$consultation = floatval($logs_lite_plan->credit);
		// 			} else if($trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 0 || $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "0"){
		// 				$consultation = floatval($trans->consultation_fees);
		// 			}
		// 		}


		// 		$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
		// 		$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
		// 		$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
		// 		$procedure_temp = "";
		// 		$procedure = "";

		// 		// get services
		// 		if((int)$trans->multiple_service_selection == 1)
		// 		{
		// 					// get multiple service
		// 			$service_lists = DB::table('transaction_services')
		// 			->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
		// 			->where('transaction_services.transaction_id', $trans->transaction_id)
		// 			->get();

		// 			foreach ($service_lists as $key => $service) {
		// 				if(sizeof($service_lists) - 2 == $key) {
		// 					$procedure_temp .= ucwords($service->Name).' and ';
		// 				} else {
		// 					$procedure_temp .= ucwords($service->Name).',';
		// 				}
		// 				$procedure = rtrim($procedure_temp, ',');
		// 			}
		// 			$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
		// 		} else {
		// 			$service_lists = DB::table('clinic_procedure')
		// 			->where('ProcedureID', $trans->ProcedureID)
		// 			->first();
		// 			if($service_lists) {
		// 				$procedure = ucwords($service_lists->Name);
		// 				$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
		// 			} else {
		// 						// $procedure = "";
		// 				$clinic_name = ucwords($clinic_type->Name);
		// 			}
		// 		}

		// 				// check if there is a receipt image
		// 		$receipts = DB::table('user_image_receipt')
		// 		->where('transaction_id', $trans->transaction_id)
		// 		->get();

		// 		$doc_files = [];
		// 		$receipt_status = FALSE;
		// 		if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
		// 					// $receipt_status = TRUE;
		// 			$health_provider_status = TRUE;
		// 		} else {
		// 			$health_provider_status = FALSE;
		// 		}

		// 		$type = "";
		// 		if($clinic_type->head == 1 || $clinic_type->head == "1") {
		// 			if($clinic_type->Name == "GP") {
		// 				$type = "general_practitioner";
		// 			} else if($clinic_type->Name == "Dental") {
		// 				$type = "dental_care";
		// 			} else if($clinic_type->Name == "TCM") {
		// 				$type = "tcm";
		// 			} else if($clinic_type->Name == "Screening") {
		// 				$type = "health_screening";
		// 			} else if($clinic_type->Name == "Wellness") {
		// 				$type = "wellness";
		// 			} else if($clinic_type->Name == "Specialist") {
		// 				$type = "health_specialist";
		// 			}
		// 		} else {
		// 			$find_head = DB::table('clinic_types')
		// 			->where('ClinicTypeID', $clinic_type->sub_id)
		// 			->first();
		// 			if($find_head->Name == "GP") {
		// 				$type = "general_practitioner";
		// 			} else if($find_head->Name == "Dental") {
		// 				$type = "dental_care";
		// 			} else if($find_head->Name == "TCM") {
		// 				$type = "tcm";
		// 			} else if($find_head->Name == "Screening") {
		// 				$type = "health_screening";
		// 			} else if($find_head->Name == "Wellness") {
		// 				$type = "wellness";
		// 			} else if($find_head->Name == "Specialist") {
		// 				$type = "health_specialist";
		// 			}
		// 		}

		// 	// check user if it is spouse or dependent
		// 		if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
		// 			$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
		// 			$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
		// 			$sub_account = ucwords($temp_account->Name);
		// 			$sub_account_type = $temp_sub->user_type;
		// 			$owner_id = $temp_sub->owner_id;
		// 			$dependent_relationship = $temp_sub->relationship ? ucwords($temp_sub->relationship) : 'Dependent';
		// 		} else {
		// 			$sub_account = FALSE;
		// 			$sub_account_type = FALSE;
		// 			$dependent_relationship = FALSE;
		// 			$owner_id = $customer->UserID;
		// 		}

		// 		$half_credits = false;
		// 		$total_amount = $trans->procedure_cost;
		// 		$procedure_cost = $trans->credit_cost;

		// 		if((int)$trans->health_provider_done == 1) {
		// 			$payment_type = "Cash";
		// 			$transaction_type = "cash";
		// 			if((int)$trans->lite_plan_enabled == 1) {
		// 				if((int)$trans->half_credits == 1) {
		// 					$total_amount = $trans->credit_cost + $trans->consultation_fees;
		// 					$cash = $trans->cash_cost;
		// 				} else {
		// 					$total_amount = $trans->procedure_cost;
		// 					$total_amount = $trans->procedure_cost + $trans->consultation_fees;
		// 					$cash = $trans->procedure_cost;
		// 				}
		// 			} else {
		// 				if((int)$trans->half_credits == 1) {
		// 					$cash = $trans->cash_cost;
		// 				} else {
		// 					$cash = $trans->procedure_cost;
		// 				}
		// 			}
		// 		} else {
		// 			if($trans->credit_cost > 0 && $trans->cash_cost > 0) {
		// 				$payment_type = 'Mednefits Credits + Cash';
		// 				$half_credits = true;
		// 			} else {
		// 				$payment_type = 'Mednefits Credits';
		// 			}
		// 			$transaction_type = "credits";
		// 			// $cash = number_format($trans->credit_cost, 2);
		// 			if((int)$trans->lite_plan_enabled == 1) {
		// 				if((int)$trans->half_credits == 1) {
		// 					$total_amount = $trans->credit_cost + $trans->cash_cost + $trans->consultation_fees;
		// 					$procedure_cost = $trans->credit_cost + $trans->consultation_fees;
		// 					$transaction_type = "credit_cash";
		// 		// $total_amount = $trans->credit_cost + $trans->cash_cost;
		// 					$cash = $trans->cash_cost;
		// 				} else {
		// 					$total_amount = $trans->credit_cost + $trans->cash_cost + $trans->consultation_fees;
		// 		// $total_amount = $trans->procedure_cost;
		// 					if($trans->credit_cost > 0) {
		// 						$cash = 0;
		// 					} else {
		// 						$cash = $trans->procedure_cost - $trans->consultation_fees;
		// 					}
		// 				}
		// 			} else {
		// 				$total_amount = $trans->procedure_cost;
		// 				if((int)$trans->half_credits == 1) {
		// 					$cash = $trans->cash_cost;
		// 				} else {
		// 					if($trans->credit_cost > 0) {
		// 						$cash = 0;
		// 					} else {
		// 						$cash = $trans->procedure_cost;
		// 					}
		// 				}
		// 			}
		// 		}

		// 		$bill_amount = 0;
		// 		if((int)$trans->half_credits == 1) {
		// 			if((int)$trans->lite_plan_enabled == 1) {
		// 				if((int)$trans->health_provider_done == 1) {
		// 					$bill_amount = $trans->procedure_cost;
		// 				} else {
		// 					$bill_amount = $trans->credit_cost + $trans->cash_cost;
		// 				}
		// 			} else {
		// 				$bill_amount = 	$trans->procedure_cost;
		// 			}
		// 		} else {
		// 			if((int)$trans->lite_plan_enabled == 1) {
		// 				if((int)$trans->lite_plan_use_credits == 1) {
		// 					$bill_amount = 	$trans->procedure_cost;
		// 				} else {
		// 					if((int)$trans->health_provider_done == 1) {
		// 						$bill_amount = 	$trans->procedure_cost;
		// 					} else {
		// 						$bill_amount = 	$trans->credit_cost + $trans->cash_cost;
		// 					}
		// 				}
		// 			} else {
		// 				$bill_amount = 	$trans->procedure_cost;
		// 			}
		// 		}

		// 		if((int)$trans->health_provider_done == 1 && (int)$trans->deleted == 0) {
		// 			$procedure_cost = $trans->procedure_cost;
		// 		} else if($trans->credit_cost > 0 && $trans->deleted == 0 || $trans->credit_cost > "0" && $trans->deleted == "0") {
		// 			$total_in_network_spent_credits_transaction = $trans->credit_cost;
		// 		}

		// 		$refund_text = 'NO';
		// 		if((int)$trans->refunded == 1 && (int)$trans->deleted == 1) {
		// 			$status_text = 'REFUNDED';
		// 			$refund_text = 'YES';
		// 		} else if((int)$trans->health_provider_done == 1 && (int)$trans->deleted == 1) {
		// 			$status_text = 'REMOVED';
		// 			$refund_text = 'YES';
		// 		} else {
		// 			$status_text = FALSE;
		// 		}

		// 		$paid_by_credits = $trans->credit_cost;
		// 		if((int)$trans->lite_plan_enabled == 1) {
		// 			if($consultation_credits == true) {
		// 				$paid_by_credits += $consultation;
		// 			}
		// 		}

		// 		$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);
		// 		if($trans->currency_type == "myr" && $trans->default_currency == "myr" || $trans->default_currency == "myr" && $trans->currency_type == "sgd") {
		// 			$total_amount = $total_amount * $trans->currency_amount;
		// 			$trans->credit_cost = $trans->credit_cost * $trans->currency_amount;
		// 			$trans->cap_per_visit = $trans->cap_per_visit * $trans->currency_amount;
		// 			$trans->cash_cost = $trans->cash_cost * $trans->currency_amount;
		// 			$consultation_credits = $consultation_credits * $trans->currency_amount;
		// 			$paid_by_credits = $paid_by_credits * $trans->currency_amount;
		// 			$trans->consultation_fees = $trans->consultation_fees * $trans->currency_amount;
		// 			$trans->currency_type = "myr";
		// 		} else  if($trans->default_currency == "sgd" || $trans->currency_type == "myr") {
		// 			$trans->currency_type = "sgd";
		// 		}


		// 		$format = array(
		// 			'clinic_name'       => $clinic->Name,
		// 			'clinic_image'      => $clinic->image,
		// 			'health_provider_address'	=> $clinic->Address,
		// 			'health_provider_city'	=> $clinic->City,
		// 			'health_provider_country'	=> $clinic->Country,
		// 			'health_provider_phone'	=> $clinic->Phone,
		// 			'health_provider_postal'	=> $clinic->Postal,
		// 			'total_amount'            => number_format($total_amount, 2),
		// 			'credits'    => number_format($total_amount, 2),
		// 			'bill_amount'    => number_format($bill_amount, 2),
		// 			'health_provider_name' => $clinic_name,
		// 			'service'         => $procedure,
		// 			'transaction_date' => date('d F Y, h:ia', strtotime($trans->date_of_transaction)),
		// 			'member'            => ucwords($customer->Name),
		// 			'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
		// 			'trans_id'          => $trans->transaction_id,
		// 			'receipt_status'    => $receipt_status,
		// 			'health_provider_status' => $health_provider_status,
		// 			'user_id'           => $trans->UserID,
		// 			'type'              => $payment_type,
		// 			'month'             => date('M', strtotime($trans->created_at)),
		// 			'day'               => date('d', strtotime($trans->created_at)),
		// 			'time'              => date('h:ia', strtotime($trans->created_at)),
		// 			'clinic_type'       => $type,
		// 			'owner_account'     => $sub_account,
		// 			'owner_id'          => $owner_id,
		// 			'sub_account_user_type' => $sub_account_type,
		// 			'co_paid'           => $trans->consultation_fees,
		// 			'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE,
		// 			'refund_text'       => $refund_text,
		// 			'cash'              => $cash,
		// 			'status_text'       => $status_text,
		// 			'spending_type'     => ucwords($trans->spending_type),
		// 			'consultation'      => (int)$trans->lite_plan_enabled == 1 ?number_format($trans->consultation_fees, 2) : "0.00",
		// 			'lite_plan'         => (int)$trans->lite_plan_enabled == 1 ? true : false,
		// 			'consultation_credits' => $consultation_credits,
		// 			'service_credits'   => $service_credits,
		// 			'transaction_type'  => $transaction_type,
		// 			'logs_lite_plan'    => isset($logs_lite_plan) ? $logs_lite_plan : null,
		// 			'dependent_relationship'    => $dependent_relationship,
		// 			'cap_transaction'   => $half_credits,
		// 			'cap_per_visit'     => $trans->cap_per_visit > 0 ? number_format($trans->cap_per_visit, 2) : 'Not Applicable',
		// 			'cap_per_visit_status' => $trans->cap_per_visit > 0 ? true : false,
		// 			'paid_by_cash'      => number_format($trans->cash_cost, 2),
		// 			'paid_by_credits'   => number_format($paid_by_credits, 2),
		// 			"currency_symbol" 	=> $trans->currency_type == "myr" ? "MYR" : "SGD",
		// 			"currency_type" 		=> $trans->currency_type == "myr" ? "MYR" : "SGD",
		// 			'files'							=> $doc_files
		// 		);

		// 		return View::make('pdf-download.pdf-member-successful-transaction', $format);
		// 		$pdf = PDF::loadView('pdf-download.pdf-member-successful-transaction', $format);
		// 		return $pdf->stream();
		// 	}
		// }
	
	}
	
	public function downloadTransactionReceiptOld($transaction_id)
	{

		$transaction = DB::table('transaction_history')->where('transaction_id', $transaction_id)->first();
		// return $transaction;
		$clinic = DB::table('clinic')->where('ClinicID', $transaction->ClinicID)->first();
		$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
		$trans_id = str_pad($transaction_id, 6, "0", STR_PAD_LEFT);
		$type = "";
		$image = "";
		$procedure = "";
		$procedure_temp = "";
		$lite_plan_status = false;
		$lite_plan = false;
		$lite_plan_status = false;
		$total_amount = 0;
		$service_credits = false;
		$consultation_credits = false;
		$consultation = 0;
		$wallet_status = false;
		// $lite_plan = StringHelper::litePlanStatus($transaction->UserID);

		if((int)$transaction->lite_plan_enabled == 1) {
			$lite_plan_status = true;
		}


		// if($transaction->multiple_service_selection == 1 || $transaction->multiple_service_selection == "1") {
		// 	$services = DB::table('transaction_services')->where('transaction_id', $transaction->transaction_id)->get();
		// 	foreach ($services as $key => $value) {
		// 		$procedure_data = DB::table('clinic_procedure')->where('ProcedureID', $value->service_id)->first();
		// 		$procedure_temp .= ucwords($procedure_data->Name).',';
		// 	}
		// 	$procedure = rtrim($procedure_temp, ',');
		// } else {
		// 	$procedure_data = DB::table('clinic_procedure')->where('ProcedureID', $transaction->ProcedureID)->first();
		// 	$procedure = ucwords($procedure_data->Name);
		// }

		// $clinic_type_properties = TransactionHelper::getClinicImageType($clinic_type);
		// $type = $clinic_type_properties['type'];
		// $image = $clinic_type_properties['image'];

		// $total_amount = floatval($transaction->credit_cost);
		// if($transaction->credit_cost > 0) {
		// 	$transaction_type = 'Mednefits Credits';
		// 	$amount = $transaction->credit_cost;

		// 	if($lite_plan_status) {
		// 		$total_amount = floatval($transaction->consultation_fees) + floatval($transaction->credit_cost);
		// 	}
		// } else {
		// 	$transaction_type = 'Cash';
		// 	$amount = $transaction->procedure_cost;
		// 	$total_amount = floatval($amount);
		// }
		$user_id = StringHelper::getUserId($transaction->UserID);
		$company_wallet_status = PlanHelper::getCompanyAccountType($user_id);

		if($company_wallet_status) {
			if($company_wallet_status == "Health Wallet") {
				$wallet_status = true;
			}
		}
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
					$bill_amount = $transaction->credit_cost + $transaction->cash_cost;
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

		$consultation_fee = $consultation_fee_converted;
		if($transaction->default_currency == "myr" && $transaction->currency_type == "myr" || $transaction->default_currency == "myr" && $transaction->currency_type == "sgd") {
			$default_currency = "myr";
		} else {
			$default_currency = "sgd";
		}

	    // send email
		$email['member'] = ucwords($customer->Name);
		$email['credits'] = number_format($bill_amount, 2);
		$email['transaction_id'] = strtoupper(substr($clinic->Name, 0, 3)).$trans_id;
		$email['transaction_date'] = date('d F Y, h:ia', strtotime($transaction->created_at));
		$email['health_provider_name'] = ucwords($clinic->Name);
		$email['health_provider_address'] = $clinic->Address;
		$email['health_provider_city'] = $clinic->City;
		$email['health_provider_country'] = $clinic->Country;
		$email['health_provider_phone'] = $clinic->Phone;
		$email['service'] = ucwords($clinic_type->Name).' - '.$procedure;
		$email['transaction_type'] = $payment_type;
		$email['lite_plan_status'] = $lite_plan_status;
		$email['consultation'] = number_format($consultation_fee, 2);
		$email['total_amount'] = number_format($total_amount, 2);
		$email['lite_plan_enabled'] = $transaction->lite_plan_enabled;
		$email['clinic_type_image'] = $image;
		$email['currency_symbol'] = $currency_symbol;
		$email['paid_by_credits'] = number_format($paid_by_credits, 2);
		$email['paid_by_cash'] = number_format($cash_cost, 2);
		return View::make('pdf-download.member-successful-transac', $email);
		$pdf = PDF::loadView('pdf-download.member-successful-transac', $email);
    	// $pdf->setPaper('A4', 'landscape');

		// return $pdf->download($email['transaction_id'].' - '.time().'.pdf');
		// return $pdf->render();
		return $pdf->stream();
	}

	public function downloadStatementInNetwork()
	{

		$input = Input::all();
		$result = self::checkToken($input['token']);
		if(!$result) {
			return array('status' => FALSE, 'message' => 'Invalid Token.');
		}

		$lite_plan = false;
		$statement_id = $input['id'];

		$statement = DB::table('company_credits_statement')
		->where('statement_id', $statement_id)
		->first();

		if(!$statement) {
			return 'No Statement Found.';
		}

		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $statement->statement_customer_id)->first();

		if($plan->account_type === "lite_plan" || $plan->account_type == "insurance_bundle" && $plan->secondary_account_type == "insurance_bundle_lite") {
			$lite_plan = true;
		}

		$results = self::getTotalCreditsInNetworkTransactions($statement_id, $plan);
		$company_details = DB::table('customer_business_information')->where('customer_buy_start_id', $statement->statement_customer_id)->first();
		$amount_due = DB::table('transaction_history')
		->join('statement_in_network_transactions', 'statement_in_network_transactions.transaction_id', '=', 'transaction_history.transaction_id')
		->where('statement_id', $statement_id)
		->where('statement_in_network_transactions.status', 0)
		->where('transaction_history.deleted', 0)
		->sum('transaction_history.credit_cost');


		$format = array(
			'company' => $company_details ? ucwords($company_details->company_name) : '',
			'contact_email' => $statement->statement_contact_email,
			'contact_name' => ucwords($statement->statement_contact_name),
			'contact_contact_number' => $statement->statement_contact_number,
			'customer_id' => $statement->statement_customer_id,
			'statement_date' => date('j M Y', strtotime($statement->statement_date)),
			'statement_due' => date('j M Y', strtotime($statement->statement_due)),
			'statement_start_date' => date('j M', strtotime($statement->statement_start_date)),
			'statement_end_date'	=> date('j M Y', strtotime($statement->statement_end_date)),
			'statement_id'	=> $statement_id,
			'statement_number' => $statement->statement_number,
			'statement_status'	=> $statement->statement_status,
			'statement_total_amount' => number_format($results['credits'] + $results['total_consultation'], 2),
			'statement_amount_due' => number_format($amount_due, 2),
			'paid_date' => $statement->paid_date ? date('j M Y', strtotime($statement->paid_date)) : NULL,
			'paid_amount'	=> number_format($statement->paid_amount, 2),
			'in_network'				=> $results['transactions'],
			'period'			=> date('d F', strtotime($statement->statement_start_date)).' - '.date('d F Y', strtotime($statement->statement_end_date)),
			'lite_plan'		=> $lite_plan
		);

			// return $format;

		if($input['type'] == "csv") {
			return self::downloadCSV($format);
		} else {
		  	// return View::make('pdf-download.company-transaction-list-invoice', $format);

			$pdf = PDF::loadView('pdf-download.company-transaction-list-invoice', $format);
			$pdf->getDomPDF()->get_option('enable_html5_parser');
			$pdf->setPaper('A4', 'landscape');

			return $pdf->stream();
				// return $pdf->download($statement->statement_number.' - In-Network Transactions - '.time().'.pdf');
		}

	}

	public function downloadCSV($data)
	{
		$lite_plan = $data['lite_plan'];
		// $lite_plan;
		$container = array();
		foreach ($data['in_network'] as $key => $trans) {
			$temp = array(
				'TRANSACTION ID'	=> $trans['transaction_id'],
				'MEMBER' 	=> $trans['member'],
				'DATE'		=> $trans['date_of_transaction'],
				'ITEMS/SERVICE' => $trans['clinic_type'].' - '.$trans['clinic_type_and_service'],
				'PROVIDER'	=> $trans['clinic_name'],
				'TOTAL AMOUNT'	=> $trans['amount']
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



	public function downloadStatementPDF()
	{

		$input = Input::all();
		$result = self::checkToken($input['token']);

		if(!$result) {
			return array('status' => FALSE, 'message' => 'Invalid Token.');
		}

		$id = $input['id'];
		$lite_plan = false;

		// check if company exist
		$check = DB::table('customer_buy_start')->where('customer_buy_start_id', $result->customer_buy_start_id)->count();

		if($check == 0) {
			return array('status' => FALSE, 'message' => 'HR account does not exist.');
		}

		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->first();

		$e_claim = [];
		$transaction_details = [];
		$statement_in_network_amount = 0;
		$statement_e_claim_amount = 0;
		$total_consultation = 0;

		if($plan->account_type === "lite_plan") {
			$lite_plan = true;
		}

		// check if there is no statement
		$statement = DB::table('company_credits_statement')
		->where('statement_id', $id)
		->first();
		// get transaction if there is another transaction
		$today = date("Y-m-d");
		if($today < date('Y-m-d', strtotime($statement->statement_date))) {
			return "Unable to show Benefits Spending Invoice. Invoice is still in progress.";
		}



		$statement_id = $statement->statement_id;
		$statement = DB::table('company_credits_statement')
		->where('statement_id', $statement_id)
		->first();

		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

		$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();
        // return $corporate_members;
		$start = date('Y-m-d', strtotime($statement->statement_start_date));
		$temp_end = date('Y-m-t', strtotime($statement->statement_start_date));
		$end = date('Y-m-d H:i:s', strtotime('+24 hours', strtotime($temp_end)));

		foreach ($corporate_members as $key => $member) {
			$ids = StringHelper::getSubAccountsID($member->user_id);
			// if(sizeof($in_network_transaction_array) > 0) {
			$transactions = DB::table('transaction_history')
			->whereIn('UserID', $ids)
			->where('deleted', 0)
			->where('paid', 1)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $end)
			->get();

				// in-network transactions
			foreach ($transactions as $key => $trans) {
				if($trans) {
					if((int)$trans->deleted == 0 || $trans->deleted == "0") {
						$statement_in_network_amount += $trans->credit_cost;

						if($trans->spending_type == 'medical') {
							$table_wallet_history = 'wallet_history';
						} else {
							$table_wallet_history = 'wellness_wallet_history';
						}

						if((int)$trans->lite_plan_enabled == 1) {
							$logs_lite_plan = DB::table($table_wallet_history)
							->where('logs', 'deducted_from_mobile_payment')
							->where('lite_plan_enabled', 1)
							->where('id', $trans->transaction_id)
							->first();

							if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
								$total_consultation += floatval($trans->co_paid_amount);
							} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
								$total_consultation += floatval($trans->co_paid_amount);
							} else if($trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 0 || $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "0"){
								$total_consultation += floatval($trans->co_paid_amount);
							}
						}
					}

				}
			}

			// }

		}

		$total_amount = $statement_in_network_amount;
		$statement_total_amount = $total_amount + $total_consultation;
		
		if((int)$statement->statement_status == 1) {
			$amount_due = $statement->paid_amount - $statement_total_amount;
		} else {
			$amount_due = $statement_total_amount;
		}

		// return $amount_due;

		// $statement_e_claim_amount = 0.00;
		$company = DB::table('customer_business_information')->where('customer_buy_start_id', $statement->statement_customer_id)->first();
		$new_statement = array(
			"created_at"                => $statement->created_at,
			"statement_contact_email"   => $statement->statement_contact_email,
			"statement_contact_name"    => ucwords($statement->statement_contact_name),
			"statement_contact_number"  => $statement->statement_contact_number,
			"statement_customer_id"     => $statement->statement_customer_id,
			"statement_date"            => date('d F Y', strtotime($statement->statement_date)),
			'statement_due'             => date('d F Y', strtotime($statement->statement_due)),
			"statement_end_date"        => date('d F Y', strtotime($statement->statement_end_date)),
			"statement_id"              => $statement->statement_id,
			"statement_in_network_amount"   => number_format($statement_in_network_amount, 2),
			"statement_number"              => $statement->statement_number,
			"statement_reimburse_e_claim"   => $statement->statement_reimburse_e_claim,
			"statement_start_date"          => date('d F', strtotime($statement->statement_start_date)),
			"statement_status"              => (int)$statement->statement_status,
			"statement_total_amount"        => number_format($total_amount + $total_consultation, 2),
			"total_due"						=> number_format($amount_due, 2),
			"updated_at"                    => $statement->updated_at,
			"payment_remarks"				=> $statement->payment_remarks,
			"paid_date"        				=> date('d F Y', strtotime($statement->paid_date)),
			'company'						=> ucwords($company->company_name),
			'company_address'				=> ucwords($company->company_address),
			'sub_total'						=> number_format($total_amount + $total_consultation, 2),
			'lite_plan'						=> $lite_plan,
			'total_consultation'			=> number_format($total_consultation, 2)
		);

		// return $new_statement;
		// return View::make('invoice.hr-statement-invoice', $new_statement);
		$pdf = PDF::loadView('invoice.hr-statement-invoice', $new_statement);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');

		return $pdf->stream();
	}

	public function downloadStatementEclaim( )
	{
		$input = Input::all();
		$result = self::checkToken($input['token']);

		if(!$result) {
			return array('status' => FALSE, 'message' => 'Invalid Token.');
		}

		$statement_id = $input['id'];

		$statement = DB::table('company_credits_statement')
		->where('statement_id', $statement_id)
		->first();

		if(!$statement) {
			return 'No Statement Found.';
		}

		$e_claim_transaction_array = [];
		$e_claim_transaction_details = [];

		$e_claim_transaction_temp = DB::table('statement_e_claim_transactions')
		->where('statement_id', $statement_id)
		->get();
		if(sizeOf($e_claim_transaction_temp) == 0) {
			return 'No Statement Found.';
		}

		foreach ($e_claim_transaction_temp as $key => $e_claim_temp) {
			array_push($e_claim_transaction_array, $e_claim_temp->e_claim_id);
		}

		if(sizeof($e_claim_transaction_array) > 0) {
			$e_claim_result = DB::table('e_claim')
			->whereIn('e_claim_id', $e_claim_transaction_array)
			->where('status', 1)
			->orderBy('created_at', 'desc')
			->get();
			foreach($e_claim_result as $key => $res) {
				if($res) {
					if($res->status == 0) {
						$status_text = 'Pending';
					} else if($res->status == 1) {
						$status_text = 'Approved';
					} else if($res->status == 2) {
						$status_text = 'Rejected';
					} else {
						$status_text = 'Pending';
					}

	              // get docs
	              // $docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();

	              // if(sizeof($docs) > 0) {
	              //     $e_claim_receipt_status = TRUE;
	              //     $doc_files = [];
	              //     foreach ($docs as $key => $doc) {
	              //         if($doc->file_type == "pdf") {
	              //             $fil = url('').'/receipts/'.$doc->doc_file;
	              //         } else if($doc->file_type == "image") {
	              //             $fil = $doc->doc_file;
	              //         }

	              //         $temp_doc = array(
	              //             'e_claim_doc_id'    => $doc->e_claim_doc_id,
	              //             'e_claim_id'            => $doc->e_claim_id,
	              //             'file'                      => $fil,
	              //             'file_type'             => $doc->file_type
	              //         );

	              //         array_push($doc_files, $temp_doc);
	              //     }
	              // } else {
	              //     $e_claim_receipt_status = FALSE;
	              //     $doc_files = FALSE;
	              // }

					$member = DB::table('user')->where('UserID', $res->user_id)->first();

	              // check user if it is spouse or dependent
					if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
						$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
						$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
						$sub_account = ucwords($temp_account->Name);
						$sub_account_type = $temp_sub->user_type;
						$owner_id = $temp_sub->owner_id;
					} else {
						$sub_account = FALSE;
						$sub_account_type = FALSE;
						$owner_id = $member->UserID;
					}



					$temp = array(
						'status'            => $res->status,
						'status_text'       => $status_text,
						'claim_date'        => date('d F Y', strtotime($res->date)),
						'time'              => $res->time,
						'service'           => ucwords($res->service),
						'merchant'          => ucwords($res->merchant),
						'amount'            => $res->amount,
						'member'            => ucwords($member->Name),
						'type'              => 'E-Claim',
						'transaction_id'    => $res->e_claim_id,
						'visit_date'        => date('d F Y', strtotime($res->date)).', '.$res->time,
						'owner_id'          => $owner_id,
						'sub_account_type'  => $sub_account_type,
						'sub_account'       => $sub_account,
						'month'             => date('M', strtotime($res->approved_date)),
						'day'               => date('d', strtotime($res->approved_date)),
						'time'              => date('h:ia', strtotime($res->approved_date)),
	                  // 'files'             => $doc_files,
	                  // 'receipt_status'    => $e_claim_receipt_status,
					);

					array_push($e_claim_transaction_details, $temp);

				}
			}
		}

		$format['statement'] = date('d F', strtotime($statement->statement_start_date)).' - '.date('d F Y', strtotime($statement->statement_end_date));
		$format['transaction_details'] = $e_claim_transaction_details;

		return View::make('pdf-download/hr-statement-full-eclaim', $format);

		$pdf = PDF::loadView('pdf-download.hr-statement-full-eclaim', $format);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'landscape');

		return $pdf->stream();
			// return $pdf->download($statement->statement_number.' - Eclaim Transactions - '.time().'.pdf');
	}

	public function hrLoginAdmin( )
	{
		$input = Input::all();
		$data = [];
		$data['token'] = $input['token'];
		if(isset($input['admin_id']) && $input['admin_id'] != null) {
			Session::put('admin-session-id', $input['admin_id']);
		}
		return View::make('hr_dashboard.login_hr_via_token', $data);
	}

	public function updatePlanAccountType( )
	{
		$customers = DB::table('customer_buy_start')->get();
		$format = [];

		foreach($customers as $key => $customer) {
			$plans = DB::table("customer_plan")->where("customer_buy_start_id", $customer->customer_buy_start_id)->get();

			foreach($plans as $key => $plan) {
				$active = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
				if($active) {
					$format[]['id'] = $plan->customer_plan_id;
					$format[]['result'] = \CorporatePlan::where('customer_plan_id', $plan->customer_plan_id)->update(['account_type' => $active->account_type]);
				}
				// else {

				// 	return array('active' => $active, 'id' => $plan->customer_plan_id);

				// }
				// return $plan->customer_plan_id;
				// update plan
			}
		}

		return $format;
	}


	public function newGetCompanyEmployeeWithCredits( )
	{
		$input = Input::all();

		if(empty($input['token']) || $input['token'] == null) {
			return ['status' => false, 'message' => 'token is required'];
		}
		$result = self::checkToken($input['token']);

		if(!$result) {
			return array('status' => FALSE, 'message' => 'Invalid Token.');
		}

		$account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
	    // get user plan
		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
		$spending = DB::table('spending_account_settings')->where('customer_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
		$active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
		$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $result->customer_buy_start_id)->first();
		$final_user = [];

		$users = DB::table('user')
		->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
		->join('corporate', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
		->where('corporate.corporate_id', $account_link->corporate_id)
		->where('corporate_members.removed_status', 0)
		->get();
		$users_count = sizeOf($users);
		$last_term_credits = false;
		for($x = 0; $x < $users_count; $x++) {
			$get_allocation = 0;
			$deducted_credits = 0;
			$e_claim_spent = 0;
			$in_network_temp_spent = 0;
			$credits_back = 0;

			$get_allocation_wellness = 0;
			$deducted_credits_wellness = 0;
			$e_claim_spent_wellness = 0;
			$in_network_temp_spent_wellness = 0;
			$credits_back_wellness = 0;

			$wallet = DB::table('e_wallet')->where('UserID', $users[$x]->UserID)->orderBy('created_at', 'desc')->first();
			$medical_credit_data = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $users[$x]->UserID);
			$wellness_credit_data = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $users[$x]->UserID);
			$plan_dates = PlanHelper::getEmployeePlanCoverageDate($users[$x]->UserID, $result->customer_buy_start_id);
			$status = 'Active';
			$get_employee_plan = DB::table('user_plan_type')->where('user_id', $users[$x]->UserID)->orderBy('created_at', 'desc')->first();

			if((int)$users[$x]->Active == 0) {
				$status = 'Removed';
			}

			if(date('Y-m-d', strtotime($get_employee_plan->plan_start)) > date('Y-m-d') || (int)$users[$x]->member_activated == 0 || (int)$users[$x]->member_activated == 1 && (int)$users[$x]->Status == 0) {
				$status = 'Pending';
			}

			if((int)$users[$x]->Active == 1 && (int)$users[$x]->member_activated == 1) {
				// statuses
				$panel = DB::table('transaction_history')->where('UserID', $users[$x]->UserID)->first();
				$non_panel = DB::table('e_claim')->where('user_id', $users[$x]->UserID)->first();
								
				if($panel || $non_panel) {
					$status = 'Active';
				} else if((int)$users[$x]->Active == 1 && (int)$users[$x]->member_activated == 1 && (int)$users[$x]->Status == 1){
					$status = 'Logged In';
				}
			}

			$dependents = DB::table('employee_family_coverage_sub_accounts')
							->where('owner_id', $users[$x]->UserID)
							->where('deleted', 0)
							->count();

			$temp = array(
				'Status'	=> $status,
				'Name'		=> ucwords($users[$x]->Name),
				'Family Coverage'	=> $dependents,
				'Mobile No'		=> $users[$x]->PhoneCode.$users[$x]->PhoneNo,
				'Email'		=> $users[$x]->Email,
				'Date of Birth'		=> $users[$x]->DOB,
				'Postal'	=>$users[$x]->Zip_Code,
				'Employee ID' =>$users[$x]->emp_no,
				'Bank Name'			=> $users[$x]->bank_name,
				'Bank Account'		=> $users[$x]->bank_account,
				'Plan Type' => PlanHelper::getAccountType($plan->account_type)." (Corporate)",
				'Start Date' => date('d F Y', strtotime($plan_dates['plan_start'])),
				'End Date'	=> date('d F Y', strtotime($plan_dates['plan_end'])),
				'Medical Allocation' => number_format($medical_credit_data['allocation'], 2),
				'Medical Usage' => number_format($medical_credit_data['get_allocation_spent'], 2),
				'Medical Balance' => number_format($medical_credit_data['balance'], 2),
				'Wellness Allocation' => number_format($wellness_credit_data['allocation'], 2),
				'Wellness Usage' => number_format($wellness_credit_data['get_allocation_spent'], 2),
				'Wellness Balance' => number_format($wellness_credit_data['balance'], 2),
				
			);

			// if((int)$spending_accounts->wellness_enable == 1) {
			// 	$temp['Wellness Allocation Last Term'] = number_format($wellness_credit_data['allocation'], 2);
			// 	$temp['Wellness Usage Last Term'] = number_format($wellness_credit_data['get_allocation_spent'], 2);
			// 	$temp['Wellness Balance Last Term'] = number_format($wellness_credit_data['balance'], 2);
			// }

			$medical_last_term_credits = PlanHelper::getMemberCreditReset($users[$x]->UserID, 'medical');
			if($medical_last_term_credits) {
				$last_term_credits = true;
				// $temp['medical_last_term_credits'] = $medical_last_term_credits;
				$medical_last_term_credit_data = PlanHelper::memberMedicalAllocatedCreditsByDates($wallet->wallet_id, $users[$x]->UserID, $medical_last_term_credits['start'], PlanHelper::endDate($medical_last_term_credits['end']));

				$temp['Medical Allocation Last Term'] = DecimalHelper::formatDecimal($medical_last_term_credit_data['allocation']);
				$temp['Medical Usage_Last Term'] = DecimalHelper::formatDecimal($medical_last_term_credit_data['total_spent']);
				$temp['Medical Balance_Last Term'] = DecimalHelper::formatDecimal($medical_last_term_credit_data['balance']);
			} else {
				$temp['Medical Allocation Last Term'] = 0;
				$temp['Medical Usage_Last Term'] = 0;
				$temp['Medical Balance_Last Term'] = 0;
			}
			

			if((int)$spending_accounts->wellness_enable == 1) {
				$wellness_last_term_credits = PlanHelper::getMemberCreditReset($users[$x]->UserID, 'wellness');
				if($wellness_last_term_credits) {
					$last_term_credits = true;
					$wellness_last_term_credit_data = PlanHelper::memberWellnessAllocatedCreditsBydates($wallet->wallet_id, $users[$x]->UserID, $wellness_last_term_credits['start'], PlanHelper::endDate($wellness_last_term_credits['end']));

					$temp['Wellness Allocation Last_Term'] = DecimalHelper::formatDecimal($wellness_last_term_credit_data['allocation']);
					$temp['Wellness Usage Last Term'] = DecimalHelper::formatDecimal($wellness_last_term_credit_data['total_spent']);
					$temp['Wellness Balance_Last Term'] = DecimalHelper::formatDecimal($wellness_last_term_credit_data['balance']);
				}
			} else {
				$temp['Wellness Allocation Last Term'] = 0;
				$temp['Wellness Usage Last Term'] = 0;
				$temp['Wellness Balance_Last Term'] = 0;
			}		
			// if($spending->medical_reimbursement == 1 || $spending->wellness_reimbursement == 1) {
			// 	$temp['Bank_Name'] = $users[$x]->bank_name;
			// 	$temp['Bank_Account'] =  $users[$x]->bank_account;
			// } 
			$final_user[] = $temp;
		}
		
		return $excel = Excel::create('Employee Information', function($excel) use($final_user) {
			$excel->sheet('Sheetname', function($sheet) use($final_user) {
				$sheet->fromArray( $final_user );
			});
		})->export('xls');

		return array('status' => TRUE, 'data' => $final_user, 'last_term_credits' => $last_term_credits, 'medical' => (int)$spending_accounts->medical_enable == 1 ? true : false, 'wellness' => (int)$spending_accounts->wellness_enable == 1 ? true : false);

	}

	public function getCompanyEmployeeWithCredits( )
	{
		$result = self::checkSession();
		$customer_id = $result->customer_buy_start_id;
		$final_user = [];
		$container = array();

		$account = DB::table('customer_business_information')->where('customer_buy_start_id', $customer_id)->first();

		$title = ucwords($account->company_name).' - Employee Lists and Credits Left';

		$users = DB::table('user')
		->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
		->join('corporate', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
		->join('customer_link_customer_buy', 'customer_link_customer_buy.corporate_id', '=', 'corporate.corporate_id')
		->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_link_customer_buy.customer_buy_start_id')
		->join('customer_active_plan', 'customer_active_plan.customer_start_buy_id', '=', 'customer_buy_start.customer_buy_start_id')
		->join('user_plan_type', 'user_plan_type.user_id', '=', 'user.UserID')
		->join('package_group', 'package_group.package_group_id', '=', 'user_plan_type.package_group_id')
		->join('e_wallet', 'e_wallet.UserID', '=', 'user.UserID')
		->where('customer_buy_start.customer_buy_start_id', $customer_id)
					// ->where('user.Active')
		->where('corporate_members.removed_status', 0)
		->where('customer_active_plan.status', "true")
		->groupBy('user.UserID')
		->select('user.UserID', 'user.Name', 'user.Email', 'user.NRIC', 'user.PhoneNo', 'user.Job_Title', 'user.DOB', 'user.created_at', 'package_group.name as package_name', 'user_plan_type.plan_start', 'e_wallet.wallet_id', 'e_wallet.balance', 'corporate.company_name', 'corporate_members.removed_status', 'customer_active_plan.account_type')
		->get();

		foreach ($users as $key => $user) {
			$user_id = $user->UserID;

			$in_network_spent = 0;
			$ids = StringHelper::getSubAccountsID($user_id);

			// get user wallet_id
			$wallet = DB::table('e_wallet')->where('UserID', $user_id)->orderBy('created_at', 'desc')->first();

			$e_claim_spent = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'e_claim_transaction')
			->sum('credit');

			$in_network_temp_spent = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'in_network_transaction')
			->sum('credit');
			$credits_back = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'credits_back_from_in_network')
			->sum('credit');
			$in_network_spent = $in_network_temp_spent - $credits_back;
			// get e_claim last 3 transactions
			$e_claim_result = DB::table('e_claim')
			->whereIn('user_id', $ids)
			->orderBy('created_at', 'desc')
			->take(3)
			->get();

			// get credits allocation
			$temp_allocation = DB::table('e_wallet')
			->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('logs', ['added_by_hr'])
			->sum('wallet_history.credit');
			$deducted_allocation = DB::table('e_wallet')
			->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('logs', ['deducted_by_hr'])
			->sum('wallet_history.credit');
			$allocation = $temp_allocation - $deducted_allocation;


			$current_spending = number_format($in_network_spent + $e_claim_spent, 2);

			// wellness transaction logs wallet
			$e_claim_spent_wellness = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'e_claim_transaction')
			->sum('credit');

			$in_network_temp_spent_wellness = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'in_network_transaction')
			->sum('credit');

			$credits_back_wellness = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'credits_back_from_in_network')
			->sum('credit');

			$in_network_spent_wellness = $in_network_temp_spent_wellness - $credits_back_wellness;
			// get wellness credits allocation

			$temp_allocation_wellness = DB::table('e_wallet')
			->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('logs', ['added_by_hr'])
			->sum('wellness_wallet_history.credit');
			$deducted_allocation_wellness = DB::table('e_wallet')
			->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('logs', ['deducted_by_hr'])
			->sum('wellness_wallet_history.credit');
			$allocation_wellness = $temp_allocation_wellness - $deducted_allocation_wellness;


			$current_spending_wellness = number_format($in_network_spent_wellness + $e_claim_spent_wellness, 2);

			// get user plan details
			if($user->account_type == "stand_alone_plan") {
				$plan_type = "Stand Alone Plan";
			} else if($user->account_type == "insurance_bundle") {
				$plan_type = "Insurance Bundle Plan";
			} else if($user->account_type == "trial_plan") {
				$plan_type = "Trial Plan";
			} else {
				$plan_type = "";
			}

			// get user plan
			$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();
			$active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();

			$plan_user = DB::table('user_plan_type')->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
			$start_date = date('d F Y', strtotime($plan_user->plan_start));
			if($plan_user->fixed == 1 | $plan_user->fixed == "1") {
				$temp_valid_date = date('d F Y', strtotime('+'.$active_plan->duration, strtotime($plan->plan_start)));
				$end_date = date('d F Y', strtotime('-1 day', strtotime($temp_valid_date)));
			} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
				$end_date = date('d F Y', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
			}

			$container[] = array(
				'Name'		=> ucwords($user->Name),
				'NRIC'		=> $user->NRIC,
				'Email'		=> $user->Email,
				'Plan Type' => $plan_type." (Corporate)",
				'Start Date' => $start_date,
				'End Date'	=> $end_date,
				'Medical Credit Allocation' => number_format($allocation, 2),
				'Medical Credit Usage' => $current_spending,
				'Medical Credits' => number_format($allocation - $current_spending, 2),
				'Wellness Credit Allocation' => number_format($allocation_wellness, 2),
				'Wellness Credit Usage' => $current_spending_wellness,
				'Wellness Credits' => number_format($allocation_wellness - $current_spending_wellness, 2)
			);

		}

		return $excel = Excel::create($title, function($excel) use($container) {
			$excel->sheet('Sheetname', function($sheet) use($container) {
				$sheet->fromArray( $container );
			});
		})->export('xls');
		return $container;
	}

	// check company plan expiration

	public function getCompanyExpirePlan( )
	{
		$customers = DB::table('customer_buy_start')->get();
		// $end_dates = [];
		foreach ($customers as $key => $customer) {
			$plan = PlanHelper::getCompanyPlanDates($customer->customer_buy_start_id);
			if($plan) {
				$date = date('Y-m-d', strtotime('-1 month', strtotime($plan['plan_end'])));

				$expired = false;

				if(date('Y-m-d') == $date) {
					$expired = true;
					// send notification to renew plan
					$business_info = DB::table('customer_business_information')->where('customer_buy_start_id', $customer->customer_buy_start_id)->first();
					$business_contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $customer->customer_buy_start_id)->first();

					$email = [];
					$email['emailTo'] = $business_contact->work_email;
					// $email['emailTo'] = 'allan.alzula.work@gmail.com';
					$email['emailName'] = ucwords($business_contact->first_name).' '.ucwords($business_contact->last_name);
					$email['emailSubject'] = 'Mednefits Care Plan Expiration';
					$email['emailPage'] = 'email-templates.company_care_plan_one_month_expiration';
					$email['date'] = date('F d, Y', strtotime($plan['plan_end']));
					$email['company'] = ucwords($business_info->company_name);
					EmailHelper::sendEmail($email);
					$temp = array(
						'customer_id' => $customer->customer_buy_start_id,
						'plan_end_date'	=> $plan['plan_end'],
						'month_before_date'				=> $date,
						'status'	=> $expired
					);
					try {
						$admin_logs = array(
							'admin_id'  => null,
							'type'      => 'company_expire_notification_system_generate',
							'data'      => SystemLogLibrary::serializeData($temp)
						);
						SystemLogLibrary::createAdminLog($admin_logs);
					} catch(Exception $e) {

					}
				}

				// $temp = array(
				// 	'customer_id' => $customer->customer_buy_start_id,
				// 	'plan_end_date'	=> $plan['plan_end'],
				// 	'month_before_date'				=> $date,
				// 	'status'	=> $expired
				// );

				// array_push($end_dates, $temp);
				
			}
		}

		// return $end_dates;
	}

	public function getCompanyTotalAllocation( )
	{
		$session = self::checkSession();
		$input = Input::all();
		$start = date('Y-m-01', strtotime($input['start']));
		$end = date('Y-m-d', strtotime($input['end']));
		$filter = isset($input['filter']) ? $input['filter'] : 'current_term';
		$spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';

		$user_spending_dates = CustomerHelper::getCustomerCreditReset($session->customer_buy_start_id, $filter, $spending_type);
		$company_credits = DB::table('customer_credits')->where('customer_id', $session->customer_buy_start_id)->first();
		if($user_spending_dates) {
      if($spending_type == 'medical') {
        $credit_data = CustomerHelper::customerMedicalAllocatedCreditsByDates($session->customer_buy_start_id, $user_spending_dates['start'], $user_spending_dates['end'], $user_spending_dates['id']);
      } else {
        $credit_data = CustomerHelper::customerWellnessAllocatedCreditsByDates($session->customer_buy_start_id, $user_spending_dates['start'], $user_spending_dates['end'], $user_spending_dates['id']);
      }
    } else {
      $credit_data = null;
    }

    if($credit_data) {
			return array('status' => TRUE, 'total_allocation' => $credit_data, 'currency_type' => $company_credits->currency_type);
    } else {
    	return array('status' => TRUE, 'total_allocation' => 0, 'currency_type' => $company_credits->currency_type);
    }

		// $company_credits = DB::table('customer_credits')->where('customer_id', $session->customer_buy_start_id)->first();
		// $start = date('Y-m-d', strtotime($company_credits->created_at));
		// // check if customer has a credit reset in medical
		// $customer_credit_reset_medical = DB::table('credit_reset')
		// ->where('id', $session->customer_buy_start_id)
		// ->where('spending_type', 'medical')
		// ->where('user_type', 'company')
		// ->where('date_resetted', '>=', $start)
		// ->where('date_resetted', '<=', $end)
		// ->orderBy('created_at', 'desc')
		// ->first();
		// // return array('result' => $customer_credit_reset_medical);
		// if($customer_credit_reset_medical) {
		// 	// $wallet_start_date = date('Y-m-d', strtotime($customer_credit_reset_medical->date_resetted));
		//   // total medical credits allocation
		// 	$temp_total_medical_allocation = DB::table('customer_credits')
		// 	->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
		// 	->where('customer_credits.customer_id', $session->customer_buy_start_id)
		// 	->where('customer_credit_logs.customer_credit_logs_id', '>=', $customer_credit_reset_medical->wallet_history_id)
		// 	// ->where('customer_credit_logs.created_at', '>=', $wallet_start_date)
		// 	// ->where('customer_credit_logs.created_at', '<=', $end)
		// 	->where('customer_credit_logs.logs', 'admin_added_credits')
		// 	->sum('customer_credit_logs.credit');

		// 	$temp_total_medical_deduction = DB::table('customer_credits')
		// 	->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
		// 	->where('customer_credits.customer_id', $session->customer_buy_start_id)
		// 	// ->whereYear('customer_credit_logs.created_at', '>=', $wallet_start_date)
		// 	// ->whereYear('customer_credit_logs.created_at', '<=', $end)
		// 	->where('customer_credit_logs.customer_credit_logs_id', '>=', $customer_credit_reset_medical->wallet_history_id)
		// 	->where('customer_credit_logs.logs', 'admin_deducted_credits')
		// 	->sum('customer_credit_logs.credit');
		// } else {
		// 	// total medical credits allocation
		// 	$temp_total_medical_allocation = DB::table('customer_credits')
		// 	->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
		// 	->where('customer_credits.customer_id', $session->customer_buy_start_id)
		// 	->where('customer_credit_logs.created_at', '>=', $start)
		// 	->where('customer_credit_logs.created_at', '<=', $end)
		// 	->where('customer_credit_logs.logs', 'admin_added_credits')
		// 	->sum('customer_credit_logs.credit');

		// 	$temp_total_medical_deduction = DB::table('customer_credits')
		// 	->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
		// 	->where('customer_credits.customer_id', $session->customer_buy_start_id)
		// 	->where('customer_credit_logs.created_at', '>=', $start)
		// 	->where('customer_credit_logs.created_at', '<=', $end)
		// 	->where('customer_credit_logs.logs', 'admin_deducted_credits')
		// 	->sum('customer_credit_logs.credit');
		// }

		// $total_medical_allocation = $temp_total_medical_allocation - $temp_total_medical_deduction;

		// $customer_credit_reset_wellness = DB::table('credit_reset')
		// ->where('id', $session->customer_buy_start_id)
		// ->where('spending_type', 'wellness')
		// ->where('user_type', 'company')
		// ->where('date_resetted', '>=', $start)
		// ->where('date_resetted', '<=', $end)
		// ->orderBy('created_at', 'desc')
		// ->first();
		// if($customer_credit_reset_wellness) {
		// 	$wallet_start_date = date('Y-m-d', strtotime($customer_credit_reset_medical->date_resetted));
		// 	$temp_total_wellness_allocation = DB::table('customer_credits')
		// 	->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
		// 	->where('customer_credits.customer_id', $session->customer_buy_start_id)
		// 	->where('customer_wellness_credits_logs.customer_wellness_credits_history_id', '>=', $customer_credit_reset_wellness->wallet_history_id)
		// 	// ->where('customer_wellness_credits_logs.created_at', '>=', $wallet_start_date)
		// 	// ->where('customer_wellness_credits_logs.created_at', '<=', $end)
		// 	->where('customer_wellness_credits_logs.logs', 'admin_added_credits')
		// 	->sum('customer_wellness_credits_logs.credit');

		// 	$temp_total_wellness_deduction = DB::table('customer_credits')
		// 	->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
		// 	->where('customer_credits.customer_id', $session->customer_buy_start_id)
		// 	->where('customer_wellness_credits_logs.customer_wellness_credits_history_id', '>=', $customer_credit_reset_wellness->wallet_history_id)
		// 	// ->where('customer_wellness_credits_logs.created_at', '>=', $wallet_start_date)
		// 	// ->where('customer_wellness_credits_logs.created_at', '<=', $end)
		// 	->where('customer_wellness_credits_logs.logs', 'admin_deducted_credits')
		// 	->sum('customer_wellness_credits_logs.credit');
		// } else {
		// 	$temp_total_wellness_allocation = DB::table('customer_credits')
		// 	->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
		// 	->where('customer_credits.customer_id', $session->customer_buy_start_id)
		// 	->where('customer_wellness_credits_logs.created_at', '>=', $start)
		// 	->where('customer_wellness_credits_logs.created_at', '<=', $end)
		// 	->where('customer_wellness_credits_logs.logs', 'admin_added_credits')
		// 	->sum('customer_wellness_credits_logs.credit');

		// 	$temp_total_wellness_deduction = DB::table('customer_credits')
		// 	->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
		// 	->where('customer_credits.customer_id', $session->customer_buy_start_id)
		// 	->where('customer_wellness_credits_logs.created_at', '>=', $start)
		// 	->where('customer_wellness_credits_logs.created_at', '<=', $end)
		// 	->where('customer_wellness_credits_logs.logs', 'admin_deducted_credits')
		// 	->sum('customer_wellness_credits_logs.credit');
		// }

		// $total_wellness_allocation = $temp_total_wellness_allocation - $temp_total_wellness_deduction;
		// return array('status' => TRUE, 'total_allocation' => $total_medical_allocation, 'total_wellness_allocation' => $total_wellness_allocation, 'currency_type' => $company_credits->currency_type);
	}

	public function updateEmployeeCredits( )
	{
		$users = DB::table('user')
		->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
		->where('user.Active', 1)
		         // ->where('UserID', 6125)
		->get();

		$credits = [];

		foreach ($users as $key => $user) {
			$wallet = DB::table('e_wallet')->where('UserID', $user->UserID)->first();

			$employee_allocation = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('logs', 'added_by_hr')
			->sum('credit');
			$deducted_allocation = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('logs', 'deducted_by_hr')
			->sum('credit');
			$credits_spent = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->whereIn('where_spend', ['in_network_transaction', 'e_claim_transaction'])
			->sum('credit');
			$credits_back = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('logs', 'credits_back_from_in_network')
			->sum('credit');
			$balance = $employee_allocation - $deducted_allocation - $credits_spent + $credits_back;
			$result = \Wallet::where('wallet_id', $wallet->wallet_id)->update(['balance' => $balance ]);
      // $temp = array(
      // 	'user_id'			=> $user->UserID,
      // 	'name'				=> $user->Name,
      // 	'allocation'	=> $employee_allocation,
      // 	'deducted_allocation' => $deducted_allocation,
      // 	'credit_spent'	=> $credits_spent,
      // 	'credits_back'	=> $credits_back,
      // 	'balance'			=> $balance
      // );

			array_push($credits, $result);
		}

		return $credits;
	}

	public function getCompanyPlanDetails( )
	{
		$session = self::checkSession();
		$today = date('Y-m-d', strtotime('-1 day'));
		$plan = PlanHelper::getCompanyPlanDates($session->customer_buy_start_id);

		$first_date = strtotime($today);
		$end_date = strtotime($plan['plan_end']);
		$time_left = $end_date - $first_date;
		$diff = round((($time_left/24)/60)/60);

		$date = date('Y-m-d', strtotime('-1 month', $end_date));

		$final_end_date = date('Y-m-d', strtotime('-1 day', $end_date));

		$total_dependents = 0;
		// for employee
		$plan_status = DB::table('customer_plan_status')->where('customer_plan_id', $plan['customer_plan_id'])->first();
		// for dependent
		$dependent_status = DB::table('dependent_plan_status')
		->where('customer_plan_id', $plan['customer_plan_id'])
		->first();

		if($dependent_status) {
			$total_dependents = $dependent_status->total_dependents;
		}
		return array(
			'start_date' 	=> date('d/m/Y', strtotime($plan['plan_start'])),
			'end_date'		=> date('d/m/Y', strtotime($final_end_date)),
			'plan_days_to_expire' => $diff >= 0 ? $diff : 0,
			'employees'		=> $plan_status->employees_input,
			'dependents'	=> $total_dependents
		);
	}


	public function calculateInvoicePlanPriceCompany($number_of_employees, $default_price, $start, $end)
	{
		$diff = date_diff(new \DateTime(date('Y-m-d', strtotime($start))), new \DateTime(date('Y-m-d', strtotime($end))));
		$days = $diff->format('%a');

		$total_days = date("z", mktime(0,0,0,12,31,date('Y'))) + 1;
		$remaining_days = $days;

		$cost_plan_and_days = ($default_price / $total_days);
		return $cost_plan_and_days * $remaining_days;
	}

	public function allocateCreditBaseInActivePlan($id, $credit, $type)
	{
		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $id)->orderBy('created_at', 'desc')->first();

		$active_plans = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->get();

		foreach($active_plans as $key => $active) {
			$total_medical_allocation = 0;
			$total_wellness_allocation = 0;

			$active->total_unallocated_medical = 0;
			$active->total_unallocated_wellness = 0;

			if($type == "medical") {
		    	// get allocated amount for medical
				$total_allocated_amount_medical = DB::table('customer_credit_logs')->whereNotNull('customer_active_plan_id')->where('customer_active_plan_id', $active->customer_active_plan_id)->where('logs', 'admin_added_credits')->sum('credit');
				$active->total_allocated_amount_medical = $total_allocated_amount_medical;

			} else {
				$total_allocated_amount_wellness = DB::table('customer_wellness_credits_logs')->whereNotNull('customer_active_plan_id')->where('customer_active_plan_id', $active->customer_active_plan_id)->where('logs', 'admin_added_credits')->sum('credit');
				$active->total_allocated_amount_wellness = $total_allocated_amount_wellness;
			}



	        // get users by customer active plan
	        // $users_id[] = DB::table('user_plan_history')->where('customer_active_plan_id', $active->customer_active_plan_id)->pluck('user_id');

	        // if(sizeof($users_id) > 0) {
	            // medical
			if($type == "medical") {
				$temp_medical_allocation = DB::table('e_wallet')
				->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
				->where('wallet_history.logs', 'added_by_hr')
				->where('wallet_history.customer_active_plan_id', $active->customer_active_plan_id)
		                                    // ->whereIn('e_wallet.UserID', $users_id)
				->sum('wallet_history.credit');

				$temp_medical_deduct_allocation = DB::table('e_wallet')
				->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
				->where('wallet_history.logs', 'deducted_by_hr')
				->where('wallet_history.customer_active_plan_id', $active->customer_active_plan_id)
		                                    // ->whereIn('e_wallet.UserID', $users_id)
				->sum('wallet_history.credit');
				$total_medical_allocation = $temp_medical_allocation - $temp_medical_deduct_allocation;
			} else {
		            // wellness
				$temp_wellness_allocation = DB::table('e_wallet')
				->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
				->where('wellness_wallet_history.logs', 'added_by_hr')
				->where('wellness_wallet_history.customer_active_plan_id', $active->customer_active_plan_id)
		                                    // ->whereIn('e_wallet.UserID', $users_id)
				->sum('wellness_wallet_history.credit');

				$temp_wellness_deduct_allocation = DB::table('e_wallet')
				->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
				->where('wellness_wallet_history.logs', 'deducted_by_hr')
				->where('wellness_wallet_history.customer_active_plan_id', $active->customer_active_plan_id)
		                                    // ->whereIn('e_wallet.UserID', $users_id)
				->sum('wellness_wallet_history.credit');
				$total_wellness_allocation = $temp_wellness_allocation - $temp_wellness_deduct_allocation;
			}


	        // } else {
	        // 	if($type == "medical") {
	        //     	$active->total_unallocated_medical = 0;
	        // 	} else {
	        //     	$active->total_unallocated_wellness = 0;
	        // 	}
	        // }

			$active->total_unallocated_medical = $total_medical_allocation;
			$active->total_unallocated_wellness = $total_wellness_allocation;

			if($type == "medical") {
				$active->total_unallocated_medical = $total_allocated_amount_medical - $total_medical_allocation;
			} else {
				$active->total_unallocated_wellness = $total_allocated_amount_wellness - $total_wellness_allocation;
			}

			if($type == "medical") {
				if($active->total_unallocated_medical > 0) {
					if($active->total_unallocated_medical >= $credit) {
						return $active->customer_active_plan_id;
					}
				}
			} else {
				if($active->total_unallocated_wellness > 0) {
					if($active->total_unallocated_wellness >= $credit) {
						return $active->customer_active_plan_id;
					}
				}
			}
		}

		return $active_plans[0]->customer_active_plan_id;
	}

	public function getPlanActivePlans( )
	{

		$session = self::checkSession();
		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $session->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
		
		$active_plans = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->get();
        // return $active_plans;

		foreach($active_plans as $key => $active) {
			$total_medical_allocation = 0;
			$total_wellness_allocation = 0;
			$invoice = DB::table('corporate_invoice')
			->where('customer_active_plan_id', $active->customer_active_plan_id)
			->first();

			$calculated_prices_end_date = null;
			if((int)$active->new_head_count == 0) {
				if($active->duration || $active->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$active->duration, strtotime($plan->plan_start)));
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
				}
				$calculated_prices_end_date = $end_plan_date;
				if($invoice && (int)$invoice->override_total_amount_status == 1) {
					$calculated_prices = $invoice->override_total_amount;
				} else {
					$calculated_prices = $invoice ? $invoice->individual_price : 0;
				}
			} else {
				$calculated_prices_end_date = PlanHelper::getCompanyPlanDates($active->customer_start_buy_id);
				$end_plan_date = $calculated_prices_end_date['plan_end'];
				$calculated_prices_end_date = $calculated_prices_end_date['plan_end'];

				if($invoice && (int)$invoice->override_total_amount_status == 1) {
					$calculated_prices = $invoice->override_total_amount;
				} else {
					$calculated_prices = PlanHelper::calculateInvoicePlanPrice($invoice ? $invoice->individual_price : 0, $active->plan_start, $calculated_prices_end_date);
				}
			}

			$active->plan_amount = $invoice ? number_format($calculated_prices * $invoice->employees, 2) : 0;
			$active->employees = $invoice ? $invoice->employees : $active->employees;

            // get depost
			$deposits = DB::table("spending_deposit_credits")
			->where("customer_active_plan_id", $active->customer_active_plan_id)
			->get();

			$total_deposit_medical = 0;
			$total_deposit_wellness = 0;

			foreach ($deposits as $key => $deposit) {
				if($deposit->medical_credits > 0 && $deposit->welness_credits > 0) {
					$percent_medical = floatval($deposit->percent);
					$percent_wellness = floatval($deposit->wellness_percent);

					$total_medical = $deposit->medical_credits;
					$total_deposit_medical += $total_medical * $percent_medical;

					$total_wellness = $deposit->welness_credits;
					$total_deposit_wellness += $total_wellness * $percent_wellness;
				} else if($deposit->medical_credits > 0) {
					$percent_medical = floatval($deposit->percent);
					$total_medical = $deposit->medical_credits;
					$total_deposit_medical += $total_medical * $percent_medical;
				} else if($deposit->welness_credits > 0) {
					$percent_wellness = floatval($deposit->wellness_percent);
					$total_wellness = $deposit->welness_credits;
					$total_deposit_wellness += $total_wellness * $percent_wellness;
				}
			}

			$active->total_deposit_medical = number_format($total_deposit_medical, 2);
			$active->total_deposit_wellness = number_format($total_deposit_wellness, 2);
            // get allocated amount for medical
			$total_allocated_amount_medical = DB::table('customer_credit_logs')->whereNotNull('customer_active_plan_id')->where('customer_active_plan_id', $active->customer_active_plan_id)->where('logs', 'admin_added_credits')->sum('credit');
			$total_allocated_amount_wellness = DB::table('customer_wellness_credits_logs')->whereNotNull('customer_active_plan_id')->where('customer_active_plan_id', $active->customer_active_plan_id)->where('logs', 'admin_added_credits')->sum('credit');

			$active->total_allocated_amount_medical = number_format($total_allocated_amount_medical, 2);
			$active->total_allocated_amount_wellness = number_format($total_allocated_amount_wellness, 2);
			$active->total_allocated_amount_medical = number_format($total_allocated_amount_medical, 2);
			$active->total_allocated_amount_wellness = number_format($total_allocated_amount_wellness, 2);

            // check for dependents
			$active->dependents = DB::table('dependent_plans')
			->where('customer_active_plan_id', $active->customer_active_plan_id)
			->count();

			// get plan extention if any
			$extension = DB::table('plan_extensions')->where('customer_active_plan_id', $active->customer_active_plan_id)->first();

			if($extension) {
				$plan_extension_plan_invoice = DB::table('plan_extension_plan_invoice')->where('plan_extension_id', $extension->plan_extention_id)->first();

				if($plan_extension_plan_invoice) {
					$invoice_extention = DB::table('corporate_invoice')
					->where('corporate_invoice_id', $plan_extension_plan_invoice->invoice_id)
					->first();

					if($invoice_extention) {
						$extension->amount = number_format($invoice_extention->employees * $invoice_extention->individual_price, 2);
					} else {
						$extension->amount = number_format($active->employees * $extension->individual_price, 2);
					}

					$invoice = DB::table('corporate_invoice')
					->where('corporate_invoice_id', $plan_extension_plan_invoice->invoice_id)
					->first();

					if($invoice) {
						$extension->employees = $invoice->employees;
					} else {
						$extension->employees = $active->employees;
					}
				} else {
					$extension->employees = $active->employees;
					$extension->amount = number_format($active->employees * $extension->individual_price, 2);
				}

				$active->plan_extension = $extension;
			}

			// check dependent plan
			$dependent_plans = DB::table('dependent_plans')
			->where('customer_plan_id', $active->plan_id)
			->where('customer_active_plan_id', $active->customer_active_plan_id)
			->get();

			foreach ($dependent_plans as $key => $dependent) {
				$occupied = DB::table('dependent_plan_history')
				->where('dependent_plan_id', $dependent->dependent_plan_id)
				->where('type', 'started')
				->count();
				$dependent->occupied = $occupied;
				$dependent->vacant = $dependent->total_dependents - $occupied;

				if((int)$dependent->new_head_count == 1) {
					$plan = DB::table('customer_plan')->where('customer_plan_id', $dependent->customer_plan_id)->first();
					$duration = $dependent->duration;
					$invoice_dependent = DB::table('dependent_invoice')
					->where('dependent_plan_id', $dependent->dependent_plan_id)
					->first();
					// $end_plan_date = date('Y-m-d', strtotime('+'.$duration, strtotime($plan->plan_start)));
					// $end_plan_date = date('Y-m-d', strtotime('-2 day', strtotime($end_plan_date)));
					// $calculated_prices = PlanHelper::calculateInvoicePlanPrice($invoice->individual_price, $dependent->plan_start, $end_plan_date);
					// $dependent->calculated_prices = $calculated_prices;
					// $dependent->amount = number_format($calculated_prices * $dependent->total_dependents, 2);
					$calculated_prices_end_date = PlanHelper::getCompanyPlanDates($plan->customer_buy_start_id);
					$calculated_prices_end_date = $calculated_prices_end_date['plan_end'];
					$calculated_prices = PlanHelper::calculateInvoicePlanPrice($invoice_dependent->individual_price, $dependent->plan_start, $calculated_prices_end_date);
					$dependent->calculated_prices = $calculated_prices;
					$dependent->amount = number_format($calculated_prices * $dependent->total_dependents, 2);
				} else {
					$dependent->amount = number_format($dependent->individual_price * $dependent->total_dependents, 2);
				}
			}

			$active->dependents = $dependent_plans;

			$employee_occcupied = DB::table('user_plan_history')
			->where('customer_active_plan_id', $active->customer_active_plan_id)
			->where('type', 'started')
			->count();
			$active->occupied = $employee_occcupied > $active->employees ? $active->employees : $employee_occcupied;
			$active->vacant = $employee_occcupied > $active->employees ? 0 : $active->employees - $employee_occcupied;
		}

		return array('status' => TRUE, 'data' => $active_plans);
	}

	public function getActivePlanDetails($id)
	{
		$check = DB::table('customer_active_plan')->where('customer_active_plan_id', $id)->first();

		if(!$check) {
			return array('status' => FALSE, 'message' => 'Active Plan not found.');
		}

        // check if there is an invoice invoice
		$invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $id)->first();
		$plan = DB::table('customer_plan')->where('customer_plan_id', $check->plan_id)->first();

		$calculated_prices_end_date = null;
		if((int)$check->new_head_count == 0) {
			if($check->duration || $check->duration != "") {
				$end_plan_date = date('Y-m-d', strtotime('+'.$check->duration, strtotime($plan->plan_start)));
			} else {
				$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
			}
			$calculated_prices_end_date = $end_plan_date;
			if((int)$invoice->override_total_amount_status == 1) {
				$calculated_prices = $invoice->override_total_amount;
			} else {
				$calculated_prices = $invoice->individual_price;
			}
		} else {
			// $first_plan = DB::table('customer_active_plan')->where('plan_id', $check->plan_id)->first();
			// $duration = null;
			// if((int)$first_plan->plan_extention_enable == 1) {
			// 	$plan_extension = DB::table('plan_extensions')
			// 	->where('customer_active_plan_id', $first_plan->customer_active_plan_id)
			// 	->first();
			// 	$duration = $plan_extension->duration;
			// } else {
			// 	$duration = $first_plan->duration;
			// }

   //          // $check->plan_amount = number_format(self::calculateInvoicePlanPrice($invoice->employees, $invoice->individual_price, $check->plan_start, $end_plan_date) * $invoice->employees, 2);
			// if($check->new_head_count == 0) {
			// 	if($check->duration || $check->duration != "") {
			// 		$end_plan_date = date('Y-m-d', strtotime('+'.$check->duration, strtotime($plan->plan_start)));
			// 	} else {
			// 		$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
			// 	}
			// 	$check->plan_amount = number_format($invoice->individual_price * $invoice->employees, 2);
			// } else {
			// 	$first_plan = DB::table('customer_active_plan')->where('plan_id', $check->plan_id)->first();
			// 	$end_plan_date = date('Y-m-d', strtotime('+'.$first_plan->duration, strtotime($plan->plan_start)));
			// 	$check->plan_amount = number_format(self::calculateInvoicePlanPrice($invoice->employees, $invoice->individual_price, $check->plan_start, $end_plan_date) * $invoice->employees, 2);
			// }
   //          // $check->employees = $invoice->employees;
   //      // } else {
   //      //     $check->plan_amount = number_format($invoice->individual_price * $invoice->employees, 2);
   //      //     // $check->employees = $invoice->employees;
   //      // }
			// $end_plan_date = date('Y-m-d', strtotime('+'.$duration, strtotime($plan->plan_start)));

			// if($check->account_type != "trial_plan") {
			// 	$calculated_prices_end_date = date('Y-m-d', strtotime('+'.$check->duration, strtotime($plan->plan_start)));
			// } else {
			// 	$calculated_prices_end_date = $end_plan_date;
			// }
			$calculated_prices_end_date = PlanHelper::getCompanyPlanDates($check->customer_start_buy_id);
			$end_plan_date = $calculated_prices_end_date['plan_end'];
			$calculated_prices_end_date = $calculated_prices_end_date['plan_end'];
			if((int)$invoice->override_total_amount_status == 1) {
				$calculated_prices = $invoice->override_total_amount;
			} else {
				$calculated_prices = PlanHelper::calculateInvoicePlanPrice($invoice->individual_price, $check->plan_start, $calculated_prices_end_date);
			}

		}

		$check->plan_amount = number_format($calculated_prices * $invoice->employees, 2);
		$check->employees = $invoice->employees;

        // count number of pending enrollment
		$active_users = DB::table('user_plan_history')->where('customer_active_plan_id', $check->customer_active_plan_id)->where('type', 'started')->count();

		$check->active_users = $active_users;

		$pending_enrollment = $check->employees - $active_users;
		if($pending_enrollment <= 0) {
			$check->pending_enrollment = 0;
		} else {
			$check->pending_enrollment = $pending_enrollment;
		}

        // check if there is a payment refund
		$refund = DB::table('payment_refund')->where('customer_active_plan_id', $check->customer_active_plan_id)->first();

		if($refund) {
			$employees = DB::table('customer_plan_withdraw')->where('customer_active_plan_id', $check->customer_active_plan_id)->count();
			$withdraw_amount = DB::table('customer_plan_withdraw')->where('customer_active_plan_id', $check->customer_active_plan_id)->sum('amount');
			$refund_data = array(
				'cancellation_number' => $refund->cancellation_number,
				'employees'           => $employees,
				'refund_amount'       => $withdraw_amount,
				'payment_status'      => $refund->status,
				'paid_amount'         => $refund->payment_amount,
				'payment_remarks'     => $refund->payment_remarks,
				'date_refund'         => $refund->date_refund
			);
		} else {
			$refund_data = NULL;
		}

        // get credits deposits
		$deposits = DB::table('spending_deposit_credits')
		->where('customer_active_plan_id', $id)
		->get();

		foreach ($deposits as $key => $deposit) {

			if($deposit->medical_credits > 0 && $deposit->welness_credits > 0) {
				$percent_medical = floatval($deposit->percent);
				$percent_wellness = floatval($deposit->wellness_percent);
                // calculate both credits
				$medical_deposit = $deposit->medical_credits * $percent_medical;
				$wellness_deposit = $deposit->welness_credits * $percent_wellness;
				$total_deposit = $medical_deposit + $wellness_deposit;
				$total = $deposit->medical_credits + $deposit->welness_credits;
				$total_all_credits_text = 'Medical + Wellness';
			} else if($deposit->medical_credits > 0) {
				$percent_medical = floatval($deposit->percent);
				$total_deposit = $deposit->medical_credits * $percent_medical;
				$total = $deposit->medical_credits;
				$total_all_credits_text = 'Medical';
			} else if($deposit->welness_credits > 0) {
				$percent_wellness = floatval($deposit->wellness_percent);
				$total_deposit = $deposit->welness_credits * $percent_wellness;
				$total = $deposit->welness_credits;
				$total_all_credits_text = 'Wellness';
			}

			$deposit->total_credits = number_format($total, 2);
			$deposit->total_deposit = number_format($total_deposit, 2);
			$deposit->total_all_credits_text = $total_all_credits_text;
		}

		// get dependents
		// check for dependents
		$dependents = DB::table('dependent_plans')
		->where('customer_active_plan_id', $check->customer_active_plan_id)
		->get();

		foreach ($dependents as $key => $dependent) {
			$occupied = DB::table('dependent_plan_history')
			->where('dependent_plan_id', $dependent->dependent_plan_id)
			->where('type', 'started')
			->count();

			$dependent->occupied = $occupied;
			$dependent->vacant = $dependent->total_dependents - $occupied;

			// if(!$invoice_dependent) {

			// }

			// $invoice_dependent = DB::table('dependent_invoice')
			// ->where('dependent_plan_id', $dependent->dependent_plan_id)
			// ->first();

			// $dependent->total_amount = number_format($invoice_dependent->individual_price * $invoice_dependent->total_dependents, 2);
			if((int)$dependent->new_head_count == 1) {
				$plan = DB::table('customer_plan')->where('customer_plan_id', $dependent->customer_plan_id)->first();
				// $duration = $dependent->duration;

				// $end_plan_date = date('Y-m-d', strtotime('+'.$duration, strtotime($plan->plan_start)));
				// if($dependent->account_type != "trial_plan") {
				// 	$calculated_prices_end_date = date('Y-m-d', strtotime('+'.$dependent->duration, strtotime($plan->plan_start)));
				// } else {
				// 	$calculated_prices_end_date = $end_plan_date;
				// }


				// // $end_plan_date = date('Y-m-d', strtotime('+'.$dependent->duration, strtotime($dependent->plan_start)));
				// $calculated_prices = self::calculateInvoicePlanPrice($dependent->individual_price, $dependent->plan_start, $calculated_prices_end_date);
				// $dependent->calculated_prices = $calculated_prices;
				// $dependent->total_amount = number_format($calculated_prices * $dependent->total_dependents, 2);
				$invoice_dependent = DB::table('dependent_invoice')
				->where('dependent_plan_id', $dependent->dependent_plan_id)
				->first();
				$calculated_prices_end_date = PlanHelper::getCompanyPlanDates($plan->customer_buy_start_id);
				$calculated_prices_end_date = $calculated_prices_end_date['plan_end'];
				$calculated_prices = PlanHelper::calculateInvoicePlanPrice($invoice_dependent->individual_price, $dependent->plan_start, $calculated_prices_end_date);
				$dependent->calculated_prices = $calculated_prices;
				$dependent->total_amount = number_format($calculated_prices * $dependent->total_dependents, 2);
				$dependent->duration = PlanHelper::getPlanDuration($plan->customer_buy_start_id, $dependent->plan_start);
			} else {
				$dependent->total_amount = number_format($dependent->individual_price * $dependent->total_dependents, 2);
			}

			if((int)$dependent->payment_status == 1) {
				$dependent->payment_status = true;
			} else {
				$dependent->payment_status = false;
			}
		}

		// get plan extention if any
		$extension = DB::table('plan_extensions')->where('customer_active_plan_id', $check->customer_active_plan_id)->first();

		if($extension) {

			$plan_extension_plan_invoice = DB::table('plan_extension_plan_invoice')
			->where('plan_extension_id', $extension->plan_extention_id)
			->first();

			if($plan_extension_plan_invoice) {
				$invoice_extention = DB::table('corporate_invoice')
				->where('corporate_invoice_id', $plan_extension_plan_invoice->invoice_id)
				->first();                 
				if($invoice_extention) {
					$extension->amount = number_format($invoice_extention->employees * $invoice_extention->individual_price, 2);
					$extension->invoice = $invoice_extention;

					$payment_data = DB::table('customer_cheque_logs')->where('invoice_id', $plan_extension_plan_invoice->invoice_id)->first();

					if(!$payment_data) {
						$payments = array(
							'customer_active_plan_id'   => $extension->customer_active_plan_id,
							'invoice_id'                => $plan_extension_plan_invoice->invoice_id,
							'created_at'				=> date('Y-m-d H:i:s'),
							'updated_at'				=> date('Y-m-d H:i:s')
						);

						DB::table('customer_cheque_logs')->insert($payments);
						$payment_data = CustomerChequeLogs::where('invoice_id', $invoice_extention->corporate_invoice_id)->first();
					}

					$extension->payment_record = $payment_data;
					$extension->paid_date = $payment_data->date_received;
					$extension->employees = $invoice_extention->employees;
					$secondary_account_type = null;
					if($extension->secondary_account_type == null) {
						if($plan->secondary_account_type == null) {
							$secondary_account_type = "pro_trial_plan_bundle";
						} else if($plan->secondary_account_type == "pro_trial_plan_bundle" || $plan->secondary_account_type == "trial_plan"){
							$secondary_account_type = "pro_trial_plan_bundle";
						} else {
							$secondary_account_type = "trial_plan_lite";
						}
					} else if($extension->secondary_account_type == "pro_trial_plan_bundle" || $extension->secondary_account_type == "trial_plan"){
						$secondary_account_type = "pro_trial_plan_bundle";
					} else {
						$secondary_account_type = "trial_plan_lite";
					}

					$extension->secondary_account_type = $secondary_account_type;

				} else {
					$extension->amount = number_format($extension->employees * $extension->individual_price, 2);
					$extension->employees = $extension->employees;
				}   
			} else {
				$extension->amount = number_format($check->employees * $extension->individual_price, 2);
				$extension->employees = $check->employees;
			}
		}

		$data = array(
			'refund'    => $refund_data,
            // 'payment_data'  => $payment_data,
			'active_plan'   => $check,
			'invoice'       => $invoice,
			'deposits'  => $deposits,
			'dependents' => $dependents,
			'extension'	=> $extension
		);

		return array('status' => TRUE, 'data' => $data);
	}

	public function downloadSpendingReceipt( )
	{
		$input = Input::all();

		$result = self::checkToken($input['token']);

		if(!$result) {
			return array('status' => FALSE, 'message' => 'Invalid Token.');
		}

		$business_info = DB::table('customer_business_information')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

		$statement = DB::table('company_credits_statement')->where('statement_id', $input['statement_id'])->first();

		$format['paid_date'] = date('F d, Y', strtotime($statement->paid_date));
		$format['paid_amount'] = number_format($statement->paid_amount, 2);
		$format['payment_remarks'] = $statement->payment_remarks;
		$format['invoice_number'] = $statement->statement_number;
		$format['company'] = ucwords($business_info->company_name);
		$format['statement_number'] = $statement->statement_number;
	    // return View::make('pdf-download.9-10-18.benefits-spending-account-payment-receipt', $format);
		$pdf = PDF::loadView('pdf-download.9-10-18.benefits-spending-account-payment-receipt', $format);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');

		return $pdf->stream();

	    // return array('result' => $statement);

	}

	// create manual credits claim
	public function createManualCreditClaim( )
	{
		$input = Input::all();

		$findUserID = $input['user_id'];

        // return date('Y-m-d H:i:s', strtotime($input['date']));

		if($findUserID){
			$email = [];
			if(!isset($input['services'])) {
				$returnObject->status = FALSE;
				$returnObject->message = 'Please choose a service.';
				return Response::json($returnObject);
			} else if(sizeof($input['services']) == 0) {
				$returnObject->status = FALSE;
				$returnObject->message = 'Please choose a service.';
				return Response::json($returnObject);
			}

			if(!isset($input['clinic_id'])) {
				$returnObject->status = FALSE;
				$returnObject->message = 'Please choose a clinic.';
				return Response::json($returnObject);
			}

			if(!isset($input['amount'])) {
				$returnObject->status = FALSE;
				$returnObject->message = 'Please enter an amount.';
				return Response::json($returnObject);
			}

			$service_id = $input['services'][0];
            // check user type
			$type = StringHelper::checkUserType($findUserID);

			$user = DB::table('user')->where('UserID', $findUserID)->first();
			if($type['user_type'] == 5 && $type['access_type'] == 0 || $type['user_type'] == "5" && $type['access_type'] == "0" || $type['user_type'] == 5 && $type['access_type'] == 1 || $type['user_type'] == "5" && $type['access_type'] == "1")
			{
				$user_id = $findUserID;
				$customer_id = $findUserID;
			} else {
                // find owner
				$owner = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $findUserID)->first();
				$user_id = $owner->owner_id;
				$customer_id = $findUserID;
			}

            // check user credits and amount key in
			$credits = DB::table('e_wallet')->where('UserID', $user_id)->first();

			if($input['amount'] > $credits->balance) {
				$returnObject->status = FALSE;
				$returnObject->message = 'You have insufficient credit in your account';
				$returnObject->sub_mesage = 'You may choose to pay directly to health provider.';
			} else {
                // deduct credits
				$transaction = new Transaction();
				$wallet = new Wallet( );

				$clinic_data = DB::table('clinic')->where('ClinicID', $input['clinic_id'])->first();

                // $procedure = DB::table('clinic_procedure')->where('ProcedureID', $input['procedure_id'])->first();
				$wallet_data = $wallet->getUserWallet($user_id);

                // check if multiple services selected
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

				if($clinic_data->co_paid_status == 1 || $clinic_data->co_paid_status == "1") {
					$co_paid_amount = $clinic_data->gst_amount;
					$co_paid_status = $clinic_data->co_paid_status;
				} else {
					$co_paid_amount = $clinic_data->co_paid_amount;
					$co_paid_status = $clinic_data->co_paid_status;
				}

                // return $services;
				$data = array(
					'UserID'                => $customer_id,
					'ProcedureID'           => $services,
					'date_of_transaction'   => date('Y-m-d H:i:s', strtotime($input['date'])),
					'ClinicID'              => $input['clinic_id'],
					'procedure_cost'        => $input['amount'],
					'AppointmenID'          => 0,
					'revenue'               => 0,
					'debit'                 => 0,
					'clinic_discount'       => $clinic_data->discount,
					'medi_percent'          => $clinic_data->medicloud_transaction_fees,
					'wallet_use'            => 1,
					'current_wallet_amount' => $wallet_data->balance,
					'credit_cost'           => $input['amount'],
					'paid'                  => 1,
					'co_paid_status'            => $co_paid_status,
					'co_paid_amount'            => $co_paid_amount,
					'DoctorID'              => 0,
					'backdate_claim'        => 1,
					'in_network'            => 1,
					'mobile'                => 1,
					'multiple_service_selection' => $multiple_service_selection
				);
                // return $data;
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

                        // deduct credit
						$history = new WalletHistory( );

						$employee_credits_left = DB::table('e_wallet')->where('wallet_id', $wallet_data->wallet_id)->first();


						$credits_logs = array(
							'wallet_id'     => $wallet_data->wallet_id,
							'credit'        => $input['amount'],
							'logs'          => 'deducted_from_mobile_payment',
							'running_balance' => $employee_credits_left->balance - $input['amount'],
							'where_spend'   => 'in_network_transaction',
							'id'            => $transaction_id
						);

						try {
							$deduct_history = $history->createWalletHistory($credits_logs);
							$wallet_history_id = $deduct_history->id;
							if($deduct_history) {
								try {
									$wallet->deductCredits($user_id, $input['amount']);
									$clinic = DB::table('clinic')->where('ClinicID', $input['clinic_id'])->first();
									$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
									$trans_id = str_pad($transaction_id, 6, "0", STR_PAD_LEFT);
									$transaction_results = array(
										'clinic_name'       => ucwords($clinic->Name),
										'amount'            => number_format($input['amount'], 2),
										'transaction_time'  => date('Y-m-d h:i', strtotime($result->created_at)),
										'transation_id'     => strtoupper(substr($clinic->Name, 0, 3)).$trans_id,
										'services'          => $procedure
									);

									Notification::sendNotification('Customer Payment - Mednefits', 'User '.ucwords($user->Name).' has made a payment for '.$procedure.' at $SGD'.$input['amount'].' to your clinic', url('app/setting/claim-report', $parameter = array(), $secure = null), $input['clinic_id'], $user->Image);

                                    // send realtime update to claim clinic admin
                                    // PusherHelper::sendClaimNotification($result);

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



                                    // send email
									$email['member'] = ucwords($user->Name);
									$email['credits'] = number_format($input['amount'], 2);
									$email['transaction_id'] = strtoupper(substr($clinic->Name, 0, 3)).$trans_id;
									$email['trans_id'] = $transaction_id;
									$email['transaction_date'] = date('d F Y, h:ia', strtotime($result->date_of_transaction));
									$email['health_provider_name'] = ucwords($clinic->Name);
									$email['health_provider_address'] = $clinic->Address;
									$email['health_provider_city'] = $clinic->City;
									$email['health_provider_country'] = $clinic->Country;
									$email['health_provider_phone'] = $clinic->Phone;
									$email['service'] = ucwords($clinic_type->Name).' - '.$procedure;
									$email['emailSubject'] = 'Member - Successful Transaction';
									$email['emailTo'] = $user->Email;
									$email['emailName'] = ucwords($user->Name);
									$email['url'] = 'http://staging.medicloud.sg';
									$email['clinic_type_image'] = $image;
									$email['transaction_type'] = 'Mednefits Credits';
									$email['emailPage'] = 'email-templates.member-successful-transaction';
									$email['dl_url'] = url();

									try {
										EmailHelper::sendEmailWithAttachment($email);
                                        // $email['emailTo'] = 'info@medicloud.sg';
                                        // EmailHelper::sendEmailWithAttachment($email);

                                        // send to clinic
										$clinic_email = DB::table('user')->where('UserType', 3)->where('Ref_ID', $input['clinic_id'])->first();

										if($clinic_email) {
											$email['emailSubject'] = 'Health Partner - Successful Transaction By Mednefits Credits';
											$email['nric'] = $user->NRIC;
											$email['emailTo'] = $clinic_email->Email;
											$email['emailPage'] = 'email-templates.health-partner-successful-transaction';
											EmailHelper::sendEmailClinicWithAttachment($email);

                                            // $email['emailTo'] = 'info@medicloud.sg';
                                            // EmailHelper::sendEmailClinicWithAttachment($email);
										}
										$returnObject->status = TRUE;
										$returnObject->message = 'Payment Successfull';
										$returnObject->data = $transaction_results;
									} catch(Exception $e) {
										$email['end_point'] = url('v1/clinic/send_payment', $parameter = array(), $secure = null);
										$email['logs'] = 'Mobile Payment Credits Send Email Attachments - '.$e->getMessage();
										$email['emailSubject'] = 'Error log.';
										EmailHelper::sendErrorLogs($email);
										$returnObject->status = TRUE;
										$returnObject->message = 'Payment Successfull';
										$returnObject->data = $transaction_results;
									}

								} catch(Exception $e) {
									$email['end_point'] = url('v1/clinic/send_payment', $parameter = array(), $secure = null);
									$email['logs'] = 'Mobile Payment Credits - '.$e->getMessage();
									$email['emailSubject'] = 'Error log. - Transaction ID: '.$transaction_id.' Wallet History ID: '.$wallet_history_id;

                                // delete transaction history log
									$transaction->deleteFailedTransactionHistory($transaction_id);
                                // delete failed wallet history
									$history->deleteFailedWalletHistory($wallet_history_id);
                                // credit back
									$wallet->addCredits($user_id, $input['amount']);
									$returnObject->status = FALSE;
									$returnObject->message = 'Payment unsuccessfull. Please try again later';

									EmailHelper::sendErrorLogs($email);
								}


							} else {
								$returnObject->status = FALSE;
								$returnObject->message = 'Payment unsuccessfull. Please try again later';
							}

						} catch(Exception $e) {
							$email['end_point'] = url('v1/clinic/send_payment', $parameter = array(), $secure = null);
							$email['logs'] = 'Mobile Payment Credits - '.$e->getMessage();
							$email['emailSubject'] = 'Error log. - Transaction ID: '.$transaction_id;

                        // delete transaction history log
							$transaction->deleteFailedTransactionHistory($transaction_id);

							$returnObject->status = FALSE;
							$returnObject->message = 'Payment unsuccessfull. Please try again later';

							EmailHelper::sendErrorLogs($email);
						}
					}
				} catch(Exception $e) {
					$returnObject->status = FALSE;
					$returnObject->message = 'Cannot process payment credits. Please try again.';
                // send email logs
					$email['end_point'] = url('v1/clinic/send_payment', $parameter = array(), $secure = null);
					$email['logs'] = 'Mobile Payment Credits - '.$e->getMessage();
					$email['emailSubject'] = 'Error log.';
					EmailHelper::sendErrorLogs($email);
				}
			}

			return Response::json($returnObject);
		} else {
			$returnObject->status = FALSE;
			$returnObject->message = StringHelper::errorMessage("Token");
			return Response::json($returnObject);
		}
	}

	public function getSpendingDeposits( )
	{
		$session = self::checkSession();
		$paginate = [];
		$format = [];
		$deposits = DB::table("spending_deposit_credits")
		->where("customer_id", $session->customer_buy_start_id)
		->paginate(5);

		$paginate['current_page'] = $deposits->getCurrentPage();
		$paginate['from'] = $deposits->getFrom();
		$paginate['last_page'] = $deposits->getLastPage();
		$paginate['per_page'] = $deposits->getPerPage();
		$paginate['to'] = $deposits->getTo();
		$paginate['total'] = $deposits->getTotal();

		foreach ($deposits as $key => $deposit) {
			$percent = floatval($deposit->percent);
			$wellness_percent = floatval($deposit->wellness_percent);
			$total_deposit_medical = 0;
			$total_deposit_wellness = 0;
			if($deposit->medical_credits > 0 && $deposit->welness_credits > 0) {
				$total_medical = $deposit->medical_credits;
				$total_deposit_medical = $total_medical * $percent;

				$total_wellness = $deposit->welness_credits;
				$total_deposit_wellness = $total_wellness * $wellness_percent;
			} else if($deposit->medical_credits > 0) {
				$total_medical = $deposit->medical_credits;
				$total_deposit_medical = $total_medical * $percent;
			} else if($deposit->welness_credits > 0) {
				$total_wellness = $deposit->welness_credits;
				$total_deposit_wellness = $total_wellness * $wellness_percent;
			}

			$temp_amount = $total_deposit_medical + $total_deposit_wellness;
			$deposit->amount = 'S$'.number_format($temp_amount, 2);
			$deposit->status = $deposit->payment_status == 1 ? true : false;
			$deposit->date_issue = date('d/m/Y', strtotime($deposit->created_at));
			$deposit->type = 'Invoice';
			$deposit->transaction = 'Invoice - '. $deposit->deposit_number;
			$deposit->link = url('benefits/deposit', $parameter = array('id' => $deposit->deposit_id), $secure = null);
			array_push($format, $deposit);
		}

		$paginate['data'] = $format;
		return $paginate;
	}

	public function getSpendingDeposit() 
	{

		$input = Input::all();

		$id = $input['id'];
		$result = self::checkToken($input['token']);

		if(!$result) {
			return array('status' => FALSE, 'message' => 'Invalid Token.');
		}

		$deposit = DB::table("spending_deposit_credits")->where("deposit_id", $id)->first();

		if(!$deposit) {
			return array('status' => FALSE, 'message' => 'Deposit not found.');
		}

		$data = [];

		$contact = DB::table('customer_business_contact')
		->where('customer_buy_start_id', $deposit->customer_id)
		->first();

		$data['email'] = $contact->work_email;
		$data['phone']     = $contact->phone;

		$business_info = DB::table('customer_business_information')->where('customer_buy_start_id', $deposit->customer_id)->first();
		$data['company'] = ucwords($business_info->company_name);
		$data['postal'] = $business_info->postal_code;

		if($contact->billing_status == "true" || $contact->billing_status == true) {
			$data['name'] = ucwords($contact->first_name).' '.ucwords($contact->last_name);
			$data['address'] = $business_info->company_address;
		} else {
			$billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $deposit->customer_id)->first();
			$data['name'] = ucwords($billing_contact->billing_name);
			$data['address'] = $billing_contact->billing_address;
		}


		$percent = floatval($deposit->percent);
		$wellness_percent = floatval($deposit->wellness_percent);
		
		$data['percent'] = $percent;
		$data['medical_status'] = false;
		$data['wellness_status'] = false;
		$data['medical_deposit_amount'] = 0;
		$medical_deposit_amount = 0;
		$data['wellness_deposit_amount'] = 0;
		$wellness_deposit_amount = 0;
		$data['total_wellness'] = 0;
		$data['total_medical'] = 0;

		if($deposit->medical_credits > 0) {
			$data['total_medical'] = $deposit->medical_credits;
			$medical_deposit_amount = $deposit->medical_credits * $percent;
			$data['medical_deposit_amount'] = number_format($medical_deposit_amount, 2);
			$data['medical_status'] = true;
		} 

		if($deposit->welness_credits > 0) {
			$data['total_wellness'] = $deposit->welness_credits;
			$wellness_deposit_amount = $deposit->welness_credits * $wellness_percent;
			$data['wellness_deposit_amount'] = number_format($wellness_deposit_amount, 2);
			$data['wellness_status'] = true;
		}

		$total_price = $medical_deposit_amount + $wellness_deposit_amount;
		$amount_due = $total_price - $deposit->amount_paid;
		$data['price'] = number_format($total_price, 2);
		$data['amount'] = number_format($total_price, 2);
		$data['total'] = number_format($total_price, 2);
		$data['amount_due'] = number_format($amount_due, 2);
		$data['paid'] = $deposit->payment_status == 1 ? true : false;
		$data['notes'] = $deposit->payment_remarks;
		$data['invoice_number'] = $deposit->deposit_number;
		$data['invoice_date'] = date('F d, Y', strtotime($deposit->invoice_date));
		$data['invoice_due'] = date('F d, Y', strtotime($deposit->invoice_due));
		$data['active_plan_id'] = $deposit->customer_active_plan_id;
		$data['currency_type'] = strtoupper($deposit->currency_type);
		$active_plan = DB::table("customer_active_plan")->where("customer_active_plan_id", $deposit->customer_active_plan_id)->first();

		if($active_plan->account_type == "insurance_bundle") {
			$data['account_type'] = 'Insurance Bundle';
		} else if($active_plan->account_type == "stand_alone_plan") {
			$data['account_type'] = 'Stand Alone Plan';
		} else if($active_plan->account_type == "lite_plan") {
			$data['account_type'] = 'Lite Plan';
		} else {
			$data['account_type'] = 'Trial Plan';
		}

		if((int)$deposit->payment_status == 1) {
			$data['payment_date'] = date('F d, Y', strtotime($deposit->payment_date));
			if($deposit->payment_remarks) {
				$data['notes'] = $deposit->payment_remarks;
			}
		}

    // return View::make('pdf-download.spending-deposit-invoice', $data);
		$pdf = PDF::loadView('pdf-download.spending-deposit-invoice', $data);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');

		return $pdf->stream($data['company'].' - '.$data['invoice_number'].'.pdf');
	}

	public function testGetActivePlanID($customer_id)
	{
		$results = PlanHelper::getCompanyAvailableActivePlanId($customer_id);
		return array('results' => $results);
	}

	public function employeePackages( )
	{
		$returnObject = new stdClass();
		$e_card = new UserPackage();
		$data = StringHelper::getEmployeeSession( );
		$id = $data->UserID;

		$findUserID = DB::table('user')->where('UserID', $id)->first();
		if($findUserID){
			$result = $e_card->newEcardDetails($id);
			$spending = MemberHelper::getMemberSpendingCoverageDate($id);
			$result['valid_start_claim'] = $spending['start_date'];
			$result['valid_end_claim'] = $spending['end_date'];
			$result['spending_feature_status_type'] = true;
			// check for spending feature
			$customer_id = PlanHelper::getCustomerId($id);
			$spending = CustomerHelper::getAccountSpendingBasicPlanStatus($customer_id);
			
			if($spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" && $spending['paid_status'] == false || $spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "pre_paid" && $spending['paid_status'] == false) {
				$result['spending_feature_status_type'] = false;
			}

			if($spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" || $spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "pre_paid") {
				$current_balance = PlanHelper::reCalculateEmployeeBalance($id);
				if($current_balance <= 0) {
					$result['spending_feature_status_type'] = false;
				}
			}

			if($spending['account_type'] == "enterprise_plan" && $spending['currency_type'] == "myr") {
				if($spending['wellness_enabled'] == false) {
					$result['spending_feature_status_type'] = false;
				}
			}

			$transaction_access = MemberHelper::checkMemberAccessTransactionStatus($id);

			if($transaction_access)	{
				$result['spending_feature_status_type'] = false;
			}

			// check member wallet spending validity
            $validity = MemberHelper::getMemberWalletValidity($id, 'medical');
			if(!$validity)	{
				$result['spending_feature_status_type'] = false;
			}

			return $result;
		} else {
			$returnObject->status = FALSE;
			$returnObject->message = 'User does not exist.';
			return Response::json($returnObject);
		}
	}

	public function getIntroMessage( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		// get company information
		$company = DB::table('customer_business_information')
		->where('customer_buy_start_id', $customer_id)
		->first();

		if(!$company) {
			return array('status' => false, 'message' => 'Company has no Business Information');
		}

		$contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $customer_id)->first();
		$hr = DB::table('customer_hr_dashboard')->where('customer_buy_start_id', $customer_id)->first();

		$data = [];
		$data['company_name'] = ucwords($company->company_name);
		$data['contact_name'] = ucwords($hr->fullname);

		$plan = DB::table('customer_plan')
		->where('customer_buy_start_id', $customer_id)
		->orderBy('created_at', 'desc')
		->first();

		if($plan) {
			// get total enrolled employees and dependents
			$employees = DB::table('customer_plan_status')
			->where('customer_plan_id', $plan->customer_plan_id)
			->first();

			$dependents = DB::table('dependent_plan_status')
			->where('customer_plan_id', $plan->customer_plan_id)
			->orderBy('created_at', 'desc')
			->first();

			if($dependents) {
				$data['total_enrolled'] = $employees->enrolled_employees + $dependents->total_enrolled_dependents;
				$data['dependents'] = true;
			} else {
				$data['total_enrolled'] = $employees->enrolled_employees;
				$data['dependents'] = false;
			}

			$data['plan_start'] = date('d F Y', strtotime($plan->plan_start));
			// get plan start and end start
			$active_plan = DB::table('customer_active_plan')
			->where('plan_id', $plan->customer_plan_id)
			->first();

			if((int)$active_plan->plan_extention_enable == 1) {
				$plan_extention = DB::table('plan_extensions')
				->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
				->first();
				if($plan_extention) {
					if($plan_extention->duration || $plan_extention->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$plan_extention->duration, strtotime($plan_extention->plan_start)));
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan_extention->plan_start)));
					}
				} else {
					if($active_plan->duration || $active_plan->duration != "") {
						$end_plan_date = date('Y-m-d', strtotime('+'.$active_plan->duration, strtotime($plan->plan_start)));
					} else {
						$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
					}
				}
			} else {
				if($active_plan->duration || $active_plan->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$active_plan->duration, strtotime($plan->plan_start)));
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
				}
			}

			$data['plan_end'] = date('d F Y', strtotime('-1 day', strtotime($end_plan_date)));

			return array('status' => true, 'data' => $data);
		}

		return array('status' => false);
	}

	public function getCompanyPlanDueAmount( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		$total_due = 0;
		$plans = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->get();
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();
		foreach ($plans as $key => $plan) {
			$pending_active_plans = DB::table('customer_active_plan')
			->where('plan_id', $plan->customer_plan_id)
			->where('paid', 'false')
			->get();

			foreach ($pending_active_plans as $key => $active) {
				$invoices = DB::table('corporate_invoice')
				->where('customer_active_plan_id', $active->customer_active_plan_id)
				->get();

				foreach ($invoices as $key => $invoice) {
					$amount = 0;
					// $payment = DB::table('customer_cheque_logs')->where('')
					if((int)$invoice->plan_extention_enable == 1) {
						$plan_extention = DB::table('plan_extensions')
						->where('customer_active_plan_id', $invoice->customer_active_plan_id)
						->first();
						if($plan_extention) {
							$amount = $invoice->individual_price * $invoice->employees;
						}
					} else {
						if((int)$active->new_head_count == 0) {
							$amount = $invoice->individual_price * $invoice->employees;
						} else {
							// $first_plan = DB::table('customer_active_plan')->where('plan_id', $active->plan_id)->first();
							// $plan = DB::table('customer_plan')->where('customer_plan_id', $active->plan_id)->first();

							// if($first_plan->duration || $first_plan->duration != "") {
							// 	$end_plan_date = date('Y-m-d', strtotime('+'.$first_plan->duration, strtotime($plan->plan_start)));
							// } else {
							// 	$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
							// }

							if((int)$invoice->override_total_amount_status == 1) {
								$calculated_prices = $invoice->override_total_amount;
							} else {
								$plan_dates = PlanHelper::getCompanyPlanDatesByPlan($plan->customer_buy_start_id, $plan->customer_plan_id);
								$calculated_prices = PlanHelper::calculateInvoicePlanPrice($invoice->individual_price, $active->plan_start, $plan_dates['plan_end']);
							}
							$amount = $invoice->employees * $calculated_prices;
						}
					}

					$total_due += $amount;
				}
			}

			// get dependents
			$dependents = DB::table('dependent_plans')->where('customer_plan_id', $plan->customer_plan_id)->get();

			foreach($dependents as $key => $dependent) {
				$invoices = DB::table('dependent_invoice')->where('dependent_plan_id', $dependent->dependent_plan_id)->get();

				foreach($invoices as $key => $invoice) {
					$amount = $invoice->individual_price * $invoice->total_dependents;
					$total_due += $amount;
				}
			}
		}

		return array('status' => true, 'total_due' => number_format($total_due, 2), 'currency_type' => $customer->currency_type);
	}

	public function getSpendingPendingAmountDue( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();
		$total_due = 0;
		$data_due = null;
		$paid = false;
		$due_date = null;
		$spendings = DB::table('company_credits_statement')
		->where('statement_customer_id', $customer_id)
		->where('statement_status', 0)
		->get();
		foreach ($spendings as $key => $spend) {
			$statement = SpendingInvoiceLibrary::getInvoiceSpending($spend->statement_id, false);
			if($statement['total_in_network_amount'] > 0 || $statement['total_consultation'] > 0) {
				$due = $statement['total_in_network_amount'] + $statement['total_consultation'];
				$total_due += $due;
			}

			if($key == count( $spendings ) -1) {
				// $data_due = $spend;
				$due_date = date('d F Y', strtotime($spend->statement_due));
			}
		}

		// check for spending invoice purchse
		$spending_purchase_invoices = DB::table('spending_purchase_invoice')
										->where('customer_id', $customer_id)
										->where('payment_status', 0)
										->first();
		
		if($spending_purchase_invoices) {
			$total_due += $spending_purchase_invoices->medical_purchase_credits + $spending_purchase_invoices->wellness_purchase_credits;
			$due_date = date('d F Y', strtotime($spending_purchase_invoices->invoice_due));
		}

		if($due_date) {
			return array('status' => true, 'spending_total_due' => number_format($total_due, 2), 'due_date' => $due_date, 'currency_type' => $customer->currency_type);
		} else {
			return array('status' => true, 'spending_total_due' => number_format($total_due, 2), 'currency_type' => $customer->currency_type);
		}

	}

	public function totalMembers( )
	{
		$input = Input::all();
		// $customer_id = $input['customer_id'];
		$customer_id = PlanHelper::getCusomerIdToken();
		$total_active_members = 0;
		$total_active_dependents = 0;

		// $total_members = $plan_status->enrolled_employees + $total_enrolled_dependents;
		$account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
		$corporate_members = DB::table('corporate_members')
		->join('user', 'user.UserID', '=', 'corporate_members.user_id')
		->where('corporate_members.corporate_id', $account_link->corporate_id)
		->where('user.Active', 1)
		->get();

		$total_active_members = sizeof($corporate_members);
		foreach ($corporate_members as $key => $member) {
			$total_active_dependents += DB::table('employee_family_coverage_sub_accounts')
			->where('owner_id', $member->user_id)
			->where('deleted', 0)
			->count();
		}

		$total_members = $total_active_members + $total_active_dependents;

		return array('status' => true, 'total_members' => $total_members);
	}

	public function getEmployeeSpendingAccountSummaryNew( )
	{
		$input = Input::all();
		return MemberHelper::getEmployeeSpendingAccountSummaryNew($input);
		if(isset($input['calibrate_medical'])) {
			if($input['calibrate_medical'] == "true") {
				$input['calibrate_medical'] = true;
			} else {
				$input['calibrate_medical'] = false;
			}
		}

		if(isset($input['calibrate_wellness'])) {
			if($input['calibrate_wellness'] == "true") {
				$input['calibrate_wellness'] = true;
			} else {
				$input['calibrate_wellness'] = false;
			}
		}
		
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['employee_id']) || $input['employee_id'] == null) {
			return array('status' => false, 'message' => 'Employee ID is required.');
		}

		$check_employee = DB::table('user')
		->where('UserID', $input['employee_id'])
		->where('UserType', 5)
		->first();

		if(!$check_employee) {
			return array('status' => false, 'message' => 'Employee does not exist.');
		}

		$user_plan_history = DB::table('user_plan_history')
		->where('user_id', $input['employee_id'])
		->where('type', 'started')
		->orderBy('date', 'desc')
		->first();

		// $plan_active = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)->first();

		// if($plan_active->account_type == "enterprise_plan") {
		// 	return array('status' => false, 'message' => 'Enterprise Plan account cannot access employee credits summary');
		// }

		$coverage = PlanHelper::getEmployeePlanCoverageDate($input['employee_id'], $customer_id);
		$last_day_coverage = PlanHelper::endDate($input['last_date_of_coverage']);
		$ids = StringHelper::getSubAccountsID($check_employee->UserID);
		$spending = CustomerHelper::getAccountSpendingBasicPlanStatus($customer_id);

		$wallet = DB::table('e_wallet')
		->where('UserID', $check_employee->UserID)
		->orderBy('created_at', 'desc')
		->first();

		// get employee plan duration
		if(!empty($input['pro_allocation_start_date']) && !empty($input['pro_allocation_end_date'])) {
			$start = new DateTime($input['pro_allocation_start_date']);
			$end = new DateTime(date('Y-m-d', strtotime($input['pro_allocation_end_date'])));
			$diff = $start->diff($end);
			$coverage_end = new DateTime($input['pro_allocation_end_date']);
		} else {
			$end = new DateTime(date('Y-m-d', strtotime($coverage['plan_end'])));
			$start = new DateTime($coverage['plan_start']);
			$diff = $start->diff($end);
			$coverage_end = new DateTime($last_day_coverage);

		}

		$plan_employee_duration = new DateTime($coverage['plan_start']);
		$plan_employee_duration = $plan_employee_duration->diff(new DateTime(date('Y-m-d', strtotime($coverage['plan_end']))));
		$plan_duration = $plan_employee_duration->days + 1;
		// get empployee plan coverage from last day of employee
		$diff_coverage = $start->diff($coverage_end);
		$coverage_diff = $diff_coverage->days + 1;
		$employee_status = PlanHelper::getEmployeeStatus($input['employee_id']);
		// get total allocation of employee from plan start and plan end
		$employee_credit_reset_medical = DB::table('credit_reset')
		->where('id', $check_employee->UserID)
		->where('spending_type', 'medical')
		->where('user_type', 'employee')
		->orderBy('created_at', 'desc')
		->first();

		$check_wallet_status = DB::table('member_wallet_status')->where('member_id', $input['employee_id'])->first();

		if($employee_credit_reset_medical) {
			$minimum_date_medical = date('Y-m-d', strtotime($employee_credit_reset_medical->date_resetted));
		} else {
			$minimum_date_medical = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->min('created_at');
			if(!$minimum_date_medical) {
				$minimum_date_medical = $wallet->created_at;
			}
		}

		$plan_end_date = PlanHelper::endDate($coverage['plan_end']);
		$total_allocation_medical_temp = 0;
		$total_allocation_medical_deducted = 0;
		$total_allocation_medical_credits_back = 0;
		$total_medical_e_claim_spent = 0;
		$total_medical_in_network_spent = 0;

		$pending_e_claim_medical = DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('spending_type', 'medical')
		->where('status', 0)
		->sum('amount');

		$usage_date = date('d/m/Y');
		// get pro allocation medical
		$pro_allocation_medical_date = DB::table('wallet_history')
		->where('wallet_id', $wallet->wallet_id)
		->where('logs', 'pro_allocation')
		->orderBy('created_at', 'desc')
		->first();

		$pro_allocation_wellness_date = DB::table('wellness_wallet_history')
		->where('wallet_id', $wallet->wallet_id)
		->where('logs', 'pro_allocation')
		->orderBy('created_at', 'desc')
		->first();

		if($pro_allocation_medical_date) {
			$usage_date = date('d/m/Y', strtotime($pro_allocation_medical_date->created_at));
		} else {
			if($pro_allocation_wellness_date) {
				$usage_date = date('d/m/Y', strtotime($pro_allocation_wellness_date->created_at));
			}
		}

		$medical_credit_data = PlanHelper::memberMedicalAllocatedCreditsByDates($wallet->wallet_id, $check_employee->UserID, $minimum_date_medical, $plan_end_date);
		$total_allocation_medical = $medical_credit_data['allocation'];
		$total_medical_spent = $medical_credit_data['get_allocation_spent'];
		$has_medical_allocation = false;
		$pro_temp = $coverage_diff / $plan_duration;

		$total_current_usage = $total_medical_spent + $pending_e_claim_medical;
		if($pro_allocation_medical_date) {
			$total_pro_medical_allocation = $pro_allocation_medical_date->credit;
			$medical_balance = $total_pro_medical_allocation - $total_current_usage;
		} else {
			$total_pro_medical_allocation = $pro_temp * $total_allocation_medical;
			$medical_balance = $total_pro_medical_allocation - $total_current_usage;
		}
		
		
		if($total_allocation_medical > 0) {
			$has_medical_allocation = true;
		}

		$exceed = false;
		if($medical_balance < 0) {
			$exceed = true;
		}

		if($employee_status['status'] == false) {
			if($total_current_usage > $total_pro_medical_allocation) {
				$exceed = true;
			}
		} else {
			if($pro_allocation_medical_date) {
				$total_pro_medical_allocation = $pro_allocation_medical_date->credit;
				$medical_balance = $total_pro_medical_allocation - $total_current_usage;
			} else {
				$medical_balance = $total_allocation_medical - $total_current_usage;
			}

			if($medical_balance < 0) {
				$exceed = true;
			} else {
				$exceed = false;
			}
		}

		$remaining_allocated_medical_credits = 0;

		if($spending['medical_method'] == "pre_paid")	{
			$remaining_allocated_medical_credits = $total_allocation_medical - $total_pro_medical_allocation;
		}
		
		$medical = array(
			'status' => true,
			'initial_allocation' 	=> number_format($total_allocation_medical, 2),
			'pro_allocation'		=> number_format($total_pro_medical_allocation, 2),
			'current_usage'			=> number_format($total_current_usage, 2),
			'pending_e_claim'		=> number_format($pending_e_claim_medical, 2),
			'spent'					=> number_format($total_medical_spent, 2),
			'exceed'				=> $exceed,
			'exceeded_by'			=> number_format($total_medical_spent - $total_pro_medical_allocation, 2),
			'balance'				=> number_format($medical_balance, 2),
			'exceed_balance'				=> $exceed == true ? number_format(abs($medical_balance), 2) : "0.00",
			'remaining_allocated_credits'	=> number_format($remaining_allocated_medical_credits, 2),
			'credits_to_be_returned'	=> $exceed == true ? number_format($remaining_allocated_medical_credits - abs($medical_balance), 2) : "0.00",
			'currency_type'	=> $wallet->currency_type,
			'plan_method'			=> $spending['medical_method'],
			'pro_allocation_status'		=> false,
			'balance_credits_date'	=> date('d/m/Y'),
			'returned_balance_status'	=> false
		);

		if($check_wallet_status && (int)$check_wallet_status->medical_pro_allocation_status == 1) {
			$medical['initial_allocation'] = number_format($check_wallet_status->medical_initial_allocation, 2);
			$medical['pro_allocation'] = number_format($check_wallet_status->medical_pro_allocation, 2);
			$medical['pro_allocation_status'] = $check_wallet_status->medical_pro_allocation_status == 1 ? true : false;

			if($spending['medical_method'] == "pre_paid")	{
				$medical['remaining_allocated_credits'] = number_format($check_wallet_status->medical_initial_allocation - $check_wallet_status->medical_pro_allocation, 2);
				$medical['remaining_credits_date'] = date('d/m/Y', strtotime($check_wallet_status->medical_return_credits_date));
				$medical['returned_credit_status'] = false;
				if(date('Y-m-d', strtotime($check_wallet_status->medical_return_credits_date)) < date('Y-m-d')) {
					$medical['balance_credits_date'] = date('d/m/Y', strtotime("+1 day", strtotime($check_wallet_status->medical_return_credits_date)));
				} else {
					$medical['balance_credits_date'] = date('d/m/Y');
					// get lates balance
					$medical_balance = $check_wallet_status->medical_pro_allocation - $total_current_usage;
				}

				if($employee_status['status'] == true) {
					$return_date = date('Y-m-d', strtotime($employee_status['expiry_date']));
					if(date('Y-m-d') >= $return_date) {
						$medical['returned_credit_status'] = true;
						$medical['balance_credits_date'] = date('d/m/Y', strtotime($return_date));
					}

					$return_balance_date = date('Y-m-d', strtotime('+1 day', strtotime($employee_status['expiry_date'])));
					if(date('Y-m-d') >= $return_balance_date) {
						$medical['balance_credits_date'] = date('d/m/Y', strtotime($return_balance_date));
						$medical['returned_balance_status'] = true;
					} else {
						$medical['balance_credits_date'] = date('d/m/Y');
					}
				}
			} else {
				if($employee_status['status'] == true) {
					$return_balance_date = date('Y-m-d', strtotime('+1 day', strtotime($employee_status['expiry_date'])));
					if(date('Y-m-d') >= $return_balance_date) {
						$medical['balance_credits_date'] = date('d/m/Y', strtotime($return_balance_date));
						$medical['returned_balance_status'] = true;
					} else {
						$medical['balance_credits_date'] = date('d/m/Y');
					}
				}
			}
		} else {
			if($employee_status['status'] == true) {
				$med_allocate = DB::table('employee_wallet_entitlement')->where('member_id', $input['employee_id'])->orderBy('created_at', 'desc')->first();
				$return_balance_date = date('Y-m-d', strtotime('+1 day', strtotime($employee_status['expiry_date'])));
				$medical['initial_allocation'] = number_format($med_allocate->medical_entitlement, 2);
				
				if(date('Y-m-d') >= $return_balance_date) {
					$medical['balance_credits_date'] = date('d/m/Y', strtotime($return_balance_date));
					$medical['returned_balance_status'] = true;
					if($medical_balance < 0) {
						$medical['balance'] = "0.00";
					}
				} else {
					$medical['balance_credits_date'] = date('d/m/Y');
				}
				$medical['balance'] = number_format($med_allocate->medical_entitlement - $total_current_usage, 2);
				$medical_balance = $med_allocate->medical_entitlement - $total_current_usage;

				if($medical_balance < 0) {
					$medical['exceed'] = true;
				} else {
					$medical['exceed'] = false;
				}
			}
		}

		$employee_credit_reset_wellness = DB::table('credit_reset')
		->where('id', $check_employee->UserID)
		->where('spending_type', 'wellness')
		->where('user_type', 'employee')
		->orderBy('created_at', 'desc')
		->first();

		if($employee_credit_reset_wellness) {
			$minimum_date_wellness = date('Y-m-d', strtotime($employee_credit_reset_wellness->date_resetted));
		} else {
			$minimum_date_wellness = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->min('created_at');
			if(!$minimum_date_wellness) {
				$minimum_date_wellness = $wallet->created_at;
			}
		}

		$total_allocation_wellness_temp = 0;
		$total_allocation_wellness_deducted = 0;
		$total_allocation_wellness_credits_back = 0;
		$total_wellness_e_claim_spent_wellness = 0;
		$total_wellness_in_network_spent = 0;
		$has_wellness_allocation = false;

		$pending_e_claim_wellness = DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('spending_type', 'wellness')
		->where('status', 0)
		->sum('amount');

		$wellness_credit_data = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $check_employee->UserID, $minimum_date_wellness, $plan_end_date);
		$total_allocation_wellness = $wellness_credit_data['allocation'];
		$total_wellness_in_network_spent = $wellness_credit_data['in_network_spent'];
		$total_wellness_e_claim_spent_wellness = $wellness_credit_data['e_claim_spent'];
		$total_wellness_spent = $wellness_credit_data['get_allocation_spent'];
		$exceed_wellness = false;
		$total_current_usage_wellness = $total_wellness_in_network_spent + $total_wellness_e_claim_spent_wellness + $pending_e_claim_wellness;
		
		if($pro_allocation_wellness_date) {
			$total_pro_wellness_allocation = $pro_allocation_wellness_date->credit;
			$wellness_balance = $total_pro_wellness_allocation - $total_current_usage_wellness;
		} else {
			$total_pro_wellness_allocation = $pro_temp * $total_allocation_wellness;
			$wellness_balance = $total_pro_wellness_allocation - $total_current_usage_wellness;
		}
		
		if($wellness_balance < 0) {
			$exceed_wellness = true;
		}

		if($employee_status['status'] == false) {
			if($total_current_usage_wellness > $total_pro_wellness_allocation) {
				$exceed_wellness = true;
			}	
		} else {
			if($pro_allocation_wellness_date) {
				$total_pro_wellness_allocation = $pro_allocation_wellness_date->credit;
				$wellness_balance = $total_pro_wellness_allocation - $total_current_usage_wellness;
			} else {
				$wellness_balance = $total_allocation_wellness - $total_current_usage_wellness;
				if($wellness_balance < 0) {
					$exceed_wellness = true;
				} else {
					$exceed_wellness = false;
				}
			}
		}
		
		if($total_allocation_wellness > 0) {
			$has_wellness_allocation = true;
		}
		
		$remaining_allocated_wellness_credits = 0;
		if($spending['wellness_method'] == "pre_paid")	{
			$remaining_allocated_wellness_credits = $total_allocation_wellness - $total_pro_wellness_allocation;
		}

		$wellness = array(
			'status' => true,
			'initial_allocation' 	=> number_format($total_allocation_wellness, 2),
			'pro_allocation'		=> number_format($total_pro_wellness_allocation, 2),
			'current_usage'			=> number_format($total_current_usage_wellness, 2),
			'spent'					=> number_format($total_wellness_spent, 2),
			'pending_e_claim'		=> number_format($pending_e_claim_wellness, 2),
			'exceed'				=> $exceed_wellness,
			'exceeded_by'			=> number_format($total_wellness_spent - $total_pro_wellness_allocation, 2),
			'balance'				=> number_format($wellness_balance, 2),
			'exceed_balance'		=> $exceed == true ? number_format(abs($wellness_balance), 2) : "0.00",
			'remaining_allocated_credits' => number_format($remaining_allocated_wellness_credits, 2),
			'credits_to_be_returned'	=> $exceed == true ? number_format($remaining_allocated_wellness_credits - abs($wellness_balance), 2) : "0.00",
			'currency_type'	=> $wallet->currency_type,
			'plan_method'			=> $spending['wellness_method'],
			'pro_allocation_status'		=> false,
			'balance_credits_date'	=> date('d/m/Y')
		);

		if($check_wallet_status && (int)$check_wallet_status->wellness_pro_allocation_status == 1) {
			$wellness['initial_allocation'] = number_format($check_wallet_status->wellness_initial_allocation, 2);
			$wellness['pro_allocation'] = number_format($check_wallet_status->wellness_pro_allocation, 2);
			$wellness['pro_allocation_status'] = $check_wallet_status->wellness_pro_allocation_status == 1 ? true : false;
			$wellness['returned_credit_status'] = false;
			if($spending['wellness_method'] == "pre_paid")	{
				$wellness['remaining_allocated_credits'] = number_format($check_wallet_status->wellness_initial_allocation - $check_wallet_status->wellness_pro_allocation, 2);
				$wellness['remaining_credits_date'] = date('d/m/Y', strtotime($check_wallet_status->wellness_return_credits_date));
				
				if(date('Y-m-d', strtotime($check_wallet_status->wellness_return_credits_date)) < date('Y-m-d')) {
					$wellness['balance_credits_date'] = date('d/m/Y', strtotime("+1 day", strtotime($check_wallet_status->wellness_return_credits_date)));
				} else {
					$wellness['balance_credits_date'] = date('d/m/Y');
					// get lates balance
					$wellness_balance = $check_wallet_status->wellness_pro_allocation - $total_wellness_spent;
				}

				if($employee_status['status'] == true) {
					$return_date = date('Y-m-d', strtotime($employee_status['expiry_date']));
					if(date('Y-m-d') >= $return_date) {
						$wellness['returned_credit_status'] = true;
						$wellness['balance_credits_date'] = date('d/m/Y', strtotime($return_date));
					}

					$return_balance_date = date('Y-m-d', strtotime('+1 day', strtotime($employee_status['expiry_date'])));
					if(date('Y-m-d') >= $return_balance_date) {
						$wellness['balance_credits_date'] = date('d/m/Y', strtotime($return_balance_date));
						$wellness['returned_balance_status'] = true;
					} else {
						$wellness['balance_credits_date'] = date('d/m/Y');
					}
				}
			} else {
				if($employee_status['status'] == true) {
					$return_balance_date = date('Y-m-d', strtotime('+1 day', strtotime($employee_status['expiry_date'])));
					if(date('Y-m-d') >= $return_balance_date) {
						$wellness['returned_balance_status'] = true;
						$wellness['balance_credits_date'] = date('d/m/Y', strtotime($return_balance_date));
					}
				}
			}
		} else {
			if($employee_status['status'] == true) {
				$well_allocate = DB::table('employee_wallet_entitlement')->where('member_id', $input['employee_id'])->orderBy('created_at', 'desc')->first();
				$wellness['initial_allocation'] = number_format($well_allocate->wellness_entitlement, 2);
				$return_balance_date = date('Y-m-d', strtotime('+1 day', strtotime($employee_status['expiry_date'])));
				if(date('Y-m-d') >= $return_balance_date) {
					$wellness['balance_credits_date'] = date('d/m/Y', strtotime($return_balance_date));
					$wellness['returned_balance_status'] = true;
					if($wellness_balance < 0) {
						$medical['balance'] = "0.00";
					}
				} else {
					$wellness['balance_credits_date'] = date('d/m/Y');
				}

				$wellness['balance'] = number_format($well_allocate->wellness_entitlement - $total_current_usage_wellness, 2);
				$wellness_balance = $well_allocate->wellness_entitlement - $total_current_usage_wellness;

				if($wellness_balance < 0) {
					$wellness['exceed'] = true;
				} else {
					$wellness['exceed'] = false;
				}
			}
		}

		if($pro_allocation_medical_date) {
			$date = array(
				'pro_rated_start' => $pro_allocation_medical_date->pro_allocation_start_date ? date('d/m/Y', strtotime($pro_allocation_medical_date->pro_allocation_start_date)) : date('d/m/Y', strtotime($pro_allocation_medical_date->created_at)),
				'pro_rated_end' => $pro_allocation_medical_date->pro_allocation_end_date ? date('d/m/Y', strtotime($pro_allocation_medical_date->pro_allocation_end_date)) : date('d/m/Y', strtotime($pro_allocation_medical_date->created_at)),
				'usage_start'	=> date('d/m/Y', strtotime($coverage['plan_start'])),
				'usage_end'		=> $usage_date,
				'currency_type'	=> $wallet->currency_type
			);
		} else {
			$date = array(
				'pro_rated_start' => !empty($input['pro_allocation_start_date']) ? date('d/m/Y', strtotime($input['pro_allocation_start_date'])) : date('d/m/Y', strtotime($coverage['plan_start'])),
				'pro_rated_end' => !empty($input['pro_allocation_end_date']) ? date('d/m/Y', strtotime($input['pro_allocation_end_date'])) : date('d/m/Y', strtotime($last_day_coverage)),
				'usage_start'	=> date('d/m/Y', strtotime($coverage['plan_start'])),
				'usage_end'		=> $usage_date,
				'pro_allocation_start_date' => !empty($input['pro_allocation_start_date']) ? $input['pro_allocation_start_date'] : null,
				'pro_allocation_end_date' => !empty($input['pro_allocation_end_date']) ? $input['pro_allocation_end_date'] : null,
				'currency_type'	=> $wallet->currency_type
			);
		}

		$wallet_status = array(
			'member_id'		=> $input['employee_id'],
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		
		$calibrate_medical = false;
		$calibrate_wellness = false;
		if($has_medical_allocation) {
			// start calibration for medical
			if(isset($input['calibrate_medical'])) {
				$calibrate_medical = false;
				$calibrate_medical = json_decode($input['calibrate_medical']);
				if($input['calibrate_medical'] == true && $total_allocation_medical > 0 && $exceed == false) {
					$new_allocation = $total_pro_medical_allocation;
					$to_return_to_company = $total_allocation_medical - $total_pro_medical_allocation;
				} else if($input['calibrate_medical'] == true && $exceed == true && $total_allocation_medical > 0) {
					$new_allocation = $total_pro_medical_allocation;
					$balance = abs($medical_balance);
					$to_return_to_company = $remaining_allocated_medical_credits - $balance;
				}

				if($input['calibrate_medical'] == true && $total_allocation_medical > 0) {
					$calibrate_medical = true;
					$calibrated_medical = DB::table('wallet_history')
					->where('wallet_id', $wallet->wallet_id)
					->where('logs', 'pro_allocation')
					->first();

					if($calibrated_medical) {
						return array('status' => false, 'message' => 'Medical Spending Account Pro Allocation is already updated.');
					}
					// begin medical callibration
					$calibrate_medical_data = array(
						'wallet_id'         => $wallet->wallet_id,
						'credit'            => $total_pro_medical_allocation,
						'logs'              => 'pro_allocation',
						'running_balance'   => $total_pro_medical_allocation,
						'spending_type'     => 'medical',
						'pro_allocation_start_date' => !empty($input['pro_allocation_start_date']) ? date('Y-m-d', strtotime($input['pro_allocation_start_date'])) : null,
						'pro_allocation_end_date' => !empty($input['pro_allocation_end_date']) ? date('Y-m-d', strtotime($input['pro_allocation_end_date'])) : null,
						'created_at'        => date('Y-m-d H:i:s'),
						'updated_at'        => date('Y-m-d H:i:s'),
						'currency_type'	=> $wallet->currency_type
					);

          			// begin medical callibration
					$calibrate_medical_deduction_parameter = array(
						'wallet_id'         => $wallet->wallet_id,
						'credit'            => $total_allocation_medical - $total_pro_medical_allocation,
						'logs'              => 'pro_allocation_deduction',
						'running_balance'   => $total_allocation_medical - $total_pro_medical_allocation,
						'spending_type'     => 'medical',
						'created_at'        => date('Y-m-d H:i:s'),
						'updated_at'        => date('Y-m-d H:i:s'),
						'currency_type'	=> $wallet->currency_type
					);

					$calibrate_medical_deduction_by_hr = array(
						'wallet_id'         => $wallet->wallet_id,
						'credit'            => $to_return_to_company,
						'logs'              => 'deducted_by_hr',
						'running_balance'   => $to_return_to_company,
						'spending_type'     => 'medical',
						'created_at'        => date('Y-m-d H:i:s'),
						'updated_at'        => date('Y-m-d H:i:s'),
						'currency_type'	=> $wallet->currency_type
					);

					$new_balance = $total_pro_medical_allocation - $total_medical_spent;

					if($new_balance < 0) {
						$new_balance = 0;
					}

					// DB::table('wallet_history')->insert($calibrate_medical_data);
					// DB::table('wallet_history')->insert($calibrate_medical_deduction_parameter);
					// DB::table('wallet_history')->insert($calibrate_medical_deduction_by_hr);
					// DB::table('e_wallet')->where('wallet_id', $wallet->wallet_id)->update(['balance' => $new_balance]);

					$wallet_status['medical_return_credits_date'] = date('Y-m-d', strtotime($input['last_date_of_coverage']));
					$wallet_status['medical_pro_allocation_status'] = 1;
					$wallet_status['medical_initial_allocation'] = $total_allocation_medical;
					$wallet_status['medical_pro_allocation'] = $total_pro_medical_allocation;
				}
			}
		}
		
		if($has_wellness_allocation) {
			// start calibration for medical
			if(isset($input['calibrate_wellness'])) {
				if($input['calibrate_wellness'] == true && $total_allocation_wellness > 0 && $exceed == false) {
					$new_allocation = $total_pro_wellness_allocation;
					$to_return_to_company = $total_allocation_wellness - $total_pro_wellness_allocation;
				} else if($input['calibrate_wellness'] == true && $exceed == true && $total_allocation_wellness > 0) {
					$new_allocation = $total_pro_wellness_allocation;
					// $to_return_to_company = $total_allocation_wellness - $total_wellness_spent;

					$balance = abs($wellness_balance);
					$to_return_to_company = $remaining_allocated_wellness_credits - $balance;
				}

				if($input['calibrate_wellness'] == true && $total_allocation_wellness > 0 ) {
					$calibrate_wellness = true;
					$calibrated_wellness = DB::table('wellness_wallet_history')
					->where('wallet_id', $wallet->wallet_id)
					->where('logs', 'pro_allocation')
					->first();

					if($calibrated_wellness) {
						return array('status' => false, 'message' => 'Wellness Spending Account Pro Allocation is already updated.');
					}

					$new_balance = $total_pro_wellness_allocation - $total_wellness_spent;
					// begin medical callibration
					$calibrate_wellness_data = array(
						'wallet_id'         => $wallet->wallet_id,
						'credit'            => $total_pro_wellness_allocation,
						'logs'              => 'pro_allocation',
						'running_balance'   => $total_pro_wellness_allocation,
						'spending_type'     => 'wellness',
						'pro_allocation_start_date' => !empty($input['pro_allocation_start_date']) ? date('Y-m-d', strtotime($input['pro_allocation_start_date'])) : null,
						'pro_allocation_end_date' => !empty($input['pro_allocation_end_date']) ? date('Y-m-d', strtotime($input['pro_allocation_end_date'])) : null,
						'created_at'        => date('Y-m-d H:i:s'),
						'updated_at'        => date('Y-m-d H:i:s'),
						'currency_type'	=> $wallet->currency_type
					);

          			// begin medical callibration
					$calibrate_wellness_deduction_parameter = array(
						'wallet_id'         => $wallet->wallet_id,
						'credit'            => $total_allocation_wellness - $total_pro_wellness_allocation,
						'logs'              => 'pro_allocation_deduction',
						'running_balance'   => $total_allocation_wellness - $total_pro_wellness_allocation,
						'spending_type'     => 'wellness',
						'created_at'        => date('Y-m-d H:i:s'),
						'updated_at'        => date('Y-m-d H:i:s'),
						'currency_type'	=> $wallet->currency_type
					);

					$calibrate_wellness_deduction_by_hr = array(
						'wallet_id'         => $wallet->wallet_id,
						'credit'            => $to_return_to_company,
						'logs'              => 'deducted_by_hr',
						'running_balance'   => $to_return_to_company,
						'spending_type'     => 'wellness',
						'created_at'        => date('Y-m-d H:i:s'),
						'updated_at'        => date('Y-m-d H:i:s'),
						'currency_type'	=> $wallet->currency_type
					);
					
					if($new_balance < 0) {
						$new_balance = 0;
					}
					DB::table('wellness_wallet_history')->insert($calibrate_wellness_data);
					DB::table('wellness_wallet_history')->insert($calibrate_wellness_deduction_parameter);
					DB::table('wellness_wallet_history')->insert($calibrate_wellness_deduction_by_hr);
					DB::table('e_wallet')->where('wallet_id', $wallet->wallet_id)->update(['wellness_balance' => $new_balance]);
					
					$wallet_status['wellness_return_credits_date'] = date('Y-m-d', strtotime($input['last_date_of_coverage']));
					$wallet_status['wellness_pro_allocation_status'] = 1;
					$wallet_status['wellness_initial_allocation'] = $total_allocation_wellness;
					$wallet_status['wellness_pro_allocation'] = $total_pro_wellness_allocation;
				}
			}
		}
		
		if(!$check_wallet_status && $has_medical_allocation && $calibrate_medical || !$check_wallet_status && $has_wellness_allocation && $calibrate_wellness) {
			DB::table('member_wallet_status')->insert($wallet_status);
		}

		if($has_medical_allocation || $has_wellness_allocation) {
			if(isset($input['calibrate_welless']) || isset($input['calibrate_medical'])) {
				return array('status' => true, 'message' => 'Spending Account successfully updated to Pro Allocation credits.');
			}
		}

		return array('status' => true, 'medical' => $medical, 'wellness' => $wellness, 'date' => $date);
	}

	// public function getEmployeeSpendingAccountSummary( )
	// {
	// 	$input = Input::all();
	// 	$customer_id = $input['customer_id'];

	// 	if(empty($input['employee_id']) || $input['employee_id'] == null) {
	// 		return array('status' => false, 'message' => 'Employee ID is required.');
	// 	}

	// 	$check_employee = DB::table('user')
	// 	->where('UserID', $input['employee_id'])
	// 	->where('UserType', 5)
	// 	->first();

	// 	if(!$check_employee) {
	// 		return array('status' => false, 'message' => 'Employee does not exist.');
	// 	}

	// 	$ids = StringHelper::getSubAccountsID($check_employee->UserID);
	// 	$coverage = PlanHelper::getEmployeePlanDetails($check_employee->UserID, $customer_id);
	// 	$plan_end_date = PlanHelper::getEmployeePlanEndDate($check_employee->UserID);

	// 	$start = new DateTime($coverage['plan_start']);
	// 	$end = new DateTime(date('Y-m-d', strtotime('+1 day', strtotime($plan_end_date))));
	// 	$diff = $start->diff($end);
	// 	$plan_duration = $diff->days;

	// 	$coverage_end = new DateTime($coverage['end_date']);
	// 	$diff_coverage = $start->diff($coverage_end);
	// 	$coverage_diff = $diff_coverage->days;

	// 	$plan_end_date = PlanHelper::endDate($plan_end_date);
	// 	$wallet = DB::table('e_wallet')
	// 	->where('UserID', $check_employee->UserID)
	// 	->orderBy('created_at', 'desc')
	// 	->first();

	// 	$last_day_coverage = PlanHelper::getEmployeeLastDayCoverage($check_employee->UserID);
	// 	if($last_day_coverage) {
	// 		$last_day_coverage = PlanHelper::endDate($last_day_coverage);
	// 	} else {
	// 		$last_day_coverage = PlanHelper::endDate($coverage['end_date']);
	// 	}

	// 	$employee_credit_reset_medical = DB::table('credit_reset')
	// 	->where('id', $check_employee->UserID)
	// 	->where('spending_type', 'medical')
	// 	->where('user_type', 'employee')
	// 	->orderBy('created_at', 'desc')
	// 	->first();

	// 	if($employee_credit_reset_medical) {
	// 		$minimum_date_medical = date('Y-m-d', strtotime($employee_credit_reset_medical->date_resetted));
	// 	} else {
	// 		$minimum_date_medical = DB::table('wallet_history')
	// 		->where('wallet_id', $wallet->wallet_id)
	// 		->min('created_at');
	// 		if(!$minimum_date_medical) {
	// 			$minimum_date_medical = $wallet->created_at;
	// 		}
	// 	}

	// 	$total_allocation_medical_temp = 0;
	// 	$total_allocation_medical_deducted = 0;
	// 	$total_allocation_medical_credits_back = 0;
	// 	$total_medical_e_claim_spent = 0;
	// 	$total_medical_in_network_spent = 0;

	// 	$medical_wallet_history = DB::table('wallet_history')
	// 	->where('wallet_id', $wallet->wallet_id)
	// 	->where('created_at', '>=', $minimum_date_medical)
	// 	->where('created_at', '<=', $plan_end_date)
	// 	->get();
	// 	// return $medical_wallet_history;
	// 	$pending_e_claim_medical = DB::table('e_claim')
	// 	->whereIn('user_id', $ids)
	// 	->where('spending_type', 'medical')
	// 	->where('status', 0)
	// 	->sum('amount');

		
	// 	foreach ($medical_wallet_history as $key => $history) {
	// 		if($history->logs == "added_by_hr") {
	// 			$total_allocation_medical_temp += $history->credit;
	// 		}

	// 		if($history->logs == "deducted_by_hr") {
	// 			$total_allocation_medical_deducted += $history->credit;
	// 		}

	// 		if($history->where_spend == "e_claim_transaction") {
	// 			$total_medical_e_claim_spent += $history->credit;
	// 		}

	// 		if($history->where_spend == "in_network_transaction") {
	// 			$total_medical_in_network_spent += $history->credit;
	// 		}

	// 		if($history->where_spend == "credits_back_from_in_network") {
	// 			$total_allocation_medical_credits_back += $history->credit;
	// 		}
	// 	}

	// 	$total_allocation_medical_temp = $total_allocation_medical_temp - $total_allocation_medical_deducted;
	// 	$total_allocation_medical = $total_allocation_medical_temp;
	// 	$total_medical_spent_temp = $total_medical_in_network_spent + $total_medical_e_claim_spent;
	// 	$total_medical_spent = $total_medical_spent_temp - $total_allocation_medical_credits_back;
	// 	$has_medical_allocation = false;
	// 	$pro_temp = $coverage_diff / $plan_duration;

	// 	if($total_allocation_medical > 0) {
	// 		$total_current_usage = $total_medical_spent + $pending_e_claim_medical;
	// 		$total_pro_medical_allocation = $pro_temp * $total_allocation_medical;
	// 		$has_medical_allocation = true;
	// 	} else {
	// 		$total_current_usage = 0;
	// 		$total_medical_spent = 0;
	// 		$total_pro_medical_allocation = 0;
	// 	}

	// 	$exceed = false;

	// 	if($has_medical_allocation) {
	// 		if($total_current_usage > $total_pro_medical_allocation) {
	// 			$exceed = true;
	// 		}

	// 		$medical = array(
	// 			'status' => true,
	// 			'initial_allocation' 	=> $total_allocation_medical,
	// 			'pro_allocation'		=> $total_pro_medical_allocation,
	// 			'current_usage'			=> $total_current_usage,
	// 			'pending_e_claim'		=> $pending_e_claim_medical,
	// 			'spent'					=> $total_medical_spent,
	// 			'exceed'				=> $exceed
	// 		);

	// 		$calibrate_medical = false;

	// 		if(!empty($input['calibrate_medical'])) {
	// 			$calibrate_medical = json_decode($input['calibrate_medical']);

	// 			if($calibrate_medical == true && $exceed == true) {
    //      			 // check if wallet is alredy calibrated
	// 				$calibrated_medical = DB::table('wallet_history')
	// 				->where('wallet_id', $wallet->wallet_id)
	// 				->where('logs', 'pro_allocation')
	// 				->first();
	// 				if($calibrated_medical) {
	// 					return array('status' => false, 'message' => 'Medical Spending Account Pro Allocation is already updated.');
	// 				}
    //       			// begin medical callibration
	// 				$calibrate_medical_data = array(
	// 					'wallet_id'         => $wallet->wallet_id,
	// 					'credit'            => $total_pro_medical_allocation,
	// 					'logs'              => 'pro_allocation',
	// 					'running_balance'   => $total_pro_medical_allocation,
	// 					'spending_type'     => 'medical',
	// 					'created_at'        => date('Y-m-d H:i:s'),
	// 					'updated_at'        => date('Y-m-d H:i:s')
	// 				);

    //       			// begin medical callibration
	// 				$calibrate_medical_deduction_parameter = array(
	// 					'wallet_id'         => $wallet->wallet_id,
	// 					'credit'            => $total_allocation_medical - $total_pro_medical_allocation,
	// 					'logs'              => 'pro_allocation_deduction',
	// 					'running_balance'   => $total_allocation_medical - $total_pro_medical_allocation,
	// 					'spending_type'     => 'medical',
	// 					'created_at'        => date('Y-m-d H:i:s'),
	// 					'updated_at'        => date('Y-m-d H:i:s')
	// 				);

	// 				$new_balance = $total_pro_medical_allocation - $total_medical_spent;

	// 				DB::table('wallet_history')->insert($calibrate_medical_data);
	// 				DB::table('wallet_history')->insert($calibrate_medical_deduction_parameter);
	// 				DB::table('e_wallet')->where('wallet_id', $wallet->wallet_id)->update(['balance' => $new_balance]);

	// 			} else if($calibrate_medical == true && $exceed == false) {
	// 				return array('status' => false, 'message' => 'Medical Spending Account is on Track.');
	// 			}
	// 		}
	// 	} else {
	// 		$medical = false;
	// 	}

	// 	$employee_credit_reset_wellness = DB::table('credit_reset')
	// 	->where('id', $check_employee->UserID)
	// 	->where('spending_type', 'wellness')
	// 	->where('user_type', 'employee')
	// 	->orderBy('created_at', 'desc')
	// 	->first();

	// 	if($employee_credit_reset_wellness) {
	// 		$minimum_date_wellness = date('Y-m-d', strtotime($employee_credit_reset_wellness->date_resetted));
	// 	} else {
	// 		$minimum_date_wellness = DB::table('wallet_history')
	// 		->where('wallet_id', $wallet->wallet_id)
	// 		->min('created_at');
	// 		if(!$minimum_date_wellness) {
	// 			$minimum_date_wellness = $wallet->created_at;
	// 		}
	// 	}

	// 	$total_allocation_wellness_temp = 0;
	// 	$total_allocation_wellness_deducted = 0;
	// 	$total_allocation_wellness_credits_back = 0;
	// 	$total_wellness_e_claim_spent_wellness = 0;
	// 	$total_wellness_in_network_spent = 0;
	// 	$has_wellness_allocation = false;

	// 	$wellness_wallet_history = DB::table('wellness_wallet_history')
	// 	->where('wallet_id', $wallet->wallet_id)
	// 	->where('created_at', '>=', $minimum_date_wellness)
	// 	->where('created_at', '<=', $plan_end_date)
	// 	->get();

	// 	$pending_e_claim_wellness = DB::table('e_claim')
	// 	->whereIn('user_id', $ids)
	// 	->where('spending_type', 'wellness')
	// 	->where('status', 0)
	// 	->sum('amount');

	// 	foreach ($wellness_wallet_history as $key => $history) {
	// 		if($history->logs == "added_by_hr") {
	// 			$total_allocation_wellness_temp += $history->credit;
	// 		}

	// 		if($history->logs == "deducted_by_hr") {
	// 			$total_allocation_wellness_deducted += $history->credit;
	// 		}

	// 		if($history->where_spend == "e_claim_transaction") {
	// 			$total_wellness_e_claim_spent_wellness += $history->credit;
	// 		}

	// 		if($history->where_spend == "in_network_transaction") {
	// 			$total_wellness_in_network_spent += $history->credit;
	// 		}

	// 		if($history->where_spend == "credits_back_from_in_network") {
	// 			$total_allocation_wellness_credits_back += $history->credit;
	// 		}
	// 	}

	// 	$total_allocation_wellness = $total_allocation_wellness_temp - $total_allocation_wellness_deducted;
	// 	$total_wellness_spent_temp = $total_wellness_in_network_spent + $total_wellness_e_claim_spent_wellness;
	// 	$total_wellness_spent = $total_wellness_spent_temp - $total_allocation_wellness_credits_back;
	// 	// return $total_allocation_wellness;
	// 	$exceed_wellness = false;
	// 	if($total_allocation_wellness > 0) {
	// 		$has_wellness_allocation = true;
	// 		$total_current_usage_wellness = $total_wellness_in_network_spent + $total_wellness_e_claim_spent_wellness + $pending_e_claim_wellness;
	// 		$total_pro_wellness_allocation = $pro_temp * $total_allocation_wellness;
	// 		if($total_current_usage_wellness > $total_pro_wellness_allocation) {
	// 			$exceed_wellness = true;
	// 		}
	// 	} else {
	// 		$total_current_usage_wellness = 0;
	// 		$total_pro_wellness_allocation = 0;
	// 		$total_wellness_spent = 0;
	// 	}

	// 	if($has_wellness_allocation) {
	// 		$wellness = array(
	// 			'status' => true,
	// 			'initial_allocation' 	=> $total_allocation_wellness,
	// 			'pro_allocation'		=> $total_pro_wellness_allocation,
	// 			'current_usage'			=> $total_current_usage_wellness,
	// 			'spent'					=> $total_wellness_spent,
	// 			'pending_e_claim'		=> $pending_e_claim_wellness,
	// 			'exceed'				=> $exceed_wellness
	// 		);

	// 		$calibrate_wellness = false;
	// 		if(!empty($input['calibrate_wellness'])) {
	// 			$calibrate_wellness = json_decode($input['calibrate_wellness']);

	// 			if($calibrate_wellness == true && $exceed_wellness == true) {
    //       			// check if wallet is alredy calibrated
	// 				$calibrated_wellness = DB::table('wellness_wallet_history')
	// 				->where('wallet_id', $wallet->wallet_id)
	// 				->where('logs', 'pro_allocation')
	// 				->first();
	// 				if($calibrated_wellness) {
	// 					return array('status' => false, 'message' => 'Wellness Spending Account Pro Allication is already updated.');
	// 				}
    //       			// begin medical callibration
	// 				$calibrate_wellness_data = array(
	// 					'wallet_id'         => $wallet->wallet_id,
	// 					'credit'            => $total_pro_wellness_allocation,
	// 					'logs'              => 'pro_allocation',
	// 					'running_balance'   => $total_pro_wellness_allocation,
	// 					'spending_type'     => 'wellnessl',
	// 					'created_at'        => date('Y-m-d H:i:s'),
	// 					'updated_at'        => date('Y-m-d H:i:s')
	// 				);

    //       			// begin medical callibration
	// 				$calibrate_wellness_deduction_parameter = array(
	// 					'wallet_id'         => $wallet->wallet_id,
	// 					'credit'            => $total_allocation_wellness - $total_pro_wellness_allocation,
	// 					'logs'              => 'pro_allocation_deduction',
	// 					'running_balance'   => $total_allocation_wellness - $total_pro_wellness_allocation,
	// 					'spending_type'     => 'wellness',
	// 					'created_at'        => date('Y-m-d H:i:s'),
	// 					'updated_at'        => date('Y-m-d H:i:s')
	// 				);

	// 				$balance = $total_pro_wellness_allocation - $$total_wellness_spent;

	// 				DB::table('wallet_history')->insert($calibrate_wellness_data);
	// 				DB::table('wallet_history')->insert($calibrate_wellness_deduction_parameter);
	// 				DB::table('e_wallet')->where('wallet_id', $wallet->wallet_id)->update(['balance' => $balance]);

	// 			} else if($calibrate_wellness == true && $exceed_wellness == false) {
	// 				return array('status' => false, 'message' => 'Wellness Spending Account is on Track.');
	// 			}
	// 		}
	// 	} else {
	// 		$wellness = false;
	// 	}

	// 	$date = array(
	// 		'pro_rated_start' => date('d/m/Y', strtotime($coverage['plan_start'])),
	// 		'pro_rated_end' => date('d/m/Y', strtotime($last_day_coverage)),
	// 		'usage_start'	=> date('d/m/Y', strtotime($minimum_date_medical)),
	// 		'usage_end'		=> date('d/m/Y', strtotime($last_day_coverage))
	// 	);

	// 	return array('status' => true, 'medical' => $medical, 'wellness' => $wellness, 'date' => $date);
	// }

	public function createEmployeeReplacementSeat( )
	{
		$input = Input::all();
		$admin_id = Session::get('admin-session-id');
		$hr_data = StringHelper::getJwtHrSession();
		$hr_id = $hr_data->hr_dashboard_id;
		$customer_id = PlanHelper::getCusomerIdToken();
		$check_withdraw = DB::table('customer_plan_withdraw')->where('user_id', $input['employee_id'])->count();
		$expiry = date('Y-m-d', strtotime($input['last_date_of_coverage']));
		$expired_date = date('Y-m-d', strtotime('+1 day', strtotime($input['last_date_of_coverage'])));
		$date = PlanHelper::endDate(date('Y-m-d'));

		if($check_withdraw == 0) {
			if($date >= $expired_date) {
				// create refund and delete now
				try {
					$result = MemberHelper::removeEmployee($input['employee_id'], $expiry, true, $input);
					if(!$result) {
						return array('status' => FALSE, 'message' => 'Failed to create withdraw employee. Please contact Mednefits and report the issue.');
					}
				} catch(Exception $e) {
					$email = [];
					$email['end_point'] = url('hr/employees/withdraw', $parameter = array(), $secure = null);
					$email['logs'] = 'Withdraw Employee Failed - '.$e;
					$email['emailSubject'] = 'Error log.';
					EmailHelper::sendErrorLogs($email);
					return array('status' => FALSE, 'message' => 'Failed to create withdraw employee. Please contact Mednefits and report the issue.');
				}
			} else {
				try {
					$result = MemberHelper::createWithDrawEmployees($input['employee_id'], $expiry, true, true, true, $input);
				} catch(Exception $e) {
					$email = [];
					$email['end_point'] = url('hr/employees/withdraw', $parameter = array(), $secure = null);
					$email['logs'] = 'Withdraw Employee Failed - '.$e;
					$email['emailSubject'] = 'Error log.';
					EmailHelper::sendErrorLogs($email);
					return array('status' => FALSE, 'message' => 'Failed to create withdraw employee. Please contact Mednefits and report the issue.');
				}
			}

			if($admin_id) {
				$admin_logs = array(
					'admin_id'  => $admin_id,
					'admin_type' => 'mednefits',
					'type'      => 'admin_hr_removed_employee',
					'data'      => SystemLogLibrary::serializeData($input)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			} else {
				$admin_logs = array(
					'admin_id'  => $hr_id,
					'admin_type' => 'hr',
					'type'      => 'admin_hr_removed_employee',
					'data'      => SystemLogLibrary::serializeData($input)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			}
		} else {
			return array('status' => false, 'message' => 'Employee already deleted.');
		}

		return array('status' => true, 'message' => 'Hold Seat updated.');
	}

	public function checkEmployeePlanRefundType( )
	{
		$input = Input::all();
		// $customer_id = $input['customer_id'];
		// $customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['employee_id']) || $input['employee_id'] == null) {
			return array('status' => false, 'message' => 'Employee ID is required.');
		}

		$check_employee = DB::table('user')
		->where('UserID', $input['employee_id'])
		->where('UserType', 5)
		->first();

		if(!$check_employee) {
			return array('status' => false, 'message' => 'Account does not exist.');
		}

		$result = PlanHelper::checkEmployeePlanRefundType($input['employee_id']);

		return array('status' => true, 'refund_status' => $result);
	}

	public function getPendingEmployeeDeactivate( )
	{
		$input = Input::all();
		// $customer_id = $input['customer_id'];
		$customer_id = PlanHelper::getCusomerIdToken();
		$total_pending = 0;
		$user_ids = [];
		$results = PlanHelper::getCompanyEmployee($customer_id);
		array_push($user_ids, $results);
		// return $user_ids;
		return PlanHelper::getEmployeePendingRemoveAccount($user_ids);
	}

	public function enrolleEmployeeSeat( )
	{
		$input = Input::all();
		// $customer_id = $input['customer_id'];
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['employee_replacement_seat_id']) || $input['employee_replacement_seat_id'] == null) {
			return array('status' => false, 'message' => 'Employee Replacement Seat ID is required.');
		}

		$check = DB::table('employee_replacement_seat')
		->where('employee_replacement_seat_id', $input['employee_replacement_seat_id'])
		->where('customer_id', $customer_id)
		->first();

		if(!$check) {
			return array('status' => false, 'message' => 'Vacant Seat data not exist.');
		}

		if((int)$check->vacant_status == 1) {
			return array('status' => false, 'message' => 'Vacant Seat already occupied.');
		}

		if((int)$check->vacant_status == 1) {
			return array('status' => false, 'message' => 'This seat is already been occupied.');
		}

		if(empty($input['first_name']) || $input['first_name'] == null) {
			return array('status' => false, 'message' => 'First Name is required');
		}

		if(empty($input['last_name']) || $input['last_name'] == null) {
			return array('status' => false, 'message' => 'Last Name is required');
		}

		if(empty($input['dob']) || $input['dob'] == null) {
			return array('status' => false, 'message' => 'Date of Birth is required');
		}

		if(empty($input['plan_start']) || $input['plan_start'] == null) {
			return array('status' => false, 'message' => 'Start Date is required');
		}

		if(empty($input['email']) || $input['email'] == null) {
			return array('status' => false, 'message' => 'Email Address is required');
		}

		if(empty($input['postal_code']) || $input['postal_code'] == null) {
			return array('status' => false, 'message' => 'Postal Code is required');
		}

		if(empty($input['nric']) || $input['nric'] == null) {
			return array('status' => false, 'message' => 'NRIC/FIN is required');
		}

		if(empty($input['mobile']) || $input['mobile'] == null) {
			return array('status' => false, 'message' => 'Mobile Contact is required');
		}

		$valid_dob = PlanHelper::validateStartDate($input['dob']);

		if(!$valid_dob) {
			return array('status' => false, 'message' => 'Date of Birth should be a date.');
		}

		$valid_start_date= PlanHelper::validateStartDate($input['plan_start']);

		if(!$valid_start_date) {
			return array('status' => false, 'message' => 'Start Date should be a date.');
		}

		$valid_nric = PlanHelper::validIdentification($input['nric']);

		if(!$valid_nric) {
			return array('status' => false, 'message' => 'Invalid NRIC/FIN format.');
		}

		$user = DB::table('user')
		->where('Email', $input['email'])
		->where('UserType', 5)
		->where('Active', 1)
		->first();

		if($user) {
			return array('status' => false, 'message' => 'Email Address is already taken');
		}

		// $customer_id = $check->customer_id;
		$corporate = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
		$user = new User();

		$customer_active_plan_id = $check->customer_active_plan_id;
		$active_plan = DB::table('customer_active_plan')
		->where('customer_active_plan_id', $customer_active_plan_id)
		->first();

		$start_date = date('Y-m-d', strtotime($input['plan_start']));

		$password = StringHelper::get_random_password(8);
		$data = array(
			'Name'          => $input['first_name'].' '.$input['last_name'],
			'Password'      => md5($password),
			'Email'         => $input['email'],
			'PhoneNo'       => $input['mobile'],
			'PhoneCode'     => '+65',
			'NRIC'          => $input['nric'],
			'Job_Title'     => 'Other',
			'Active'        => 1,
			'Zip_Code'      => $input['postal_code'],
			'DOB'           => $input['dob']
		);

		$user_id = $user->createUserFromCorporate($data);
		$corporate_member = array(
			'corporate_id'  => $corporate->corporate_id,
			'user_id'       => $user_id,
			'first_name'    => $input['first_name'],
			'last_name'     => $input['last_name'],
			'type'          => 'member',
			'created_at'    => date('Y-m-d h:i:s'),
			'updated_at'    => date('Y-m-d h:i:s')
		);

		DB::table('corporate_members')->insert($corporate_member);
		$plan_type = new UserPlanType();

		$plan_add_on = PlanHelper::getCompanyAccountTypeEnrollee($customer_id);
		$result = PlanHelper::getEnrolleePackages($active_plan->customer_active_plan_id, $plan_add_on);

		$group_package = new PackagePlanGroup();
		$bundle = new Bundle();
		$user_package = new UserPackage();

		if(!$result) {
			$group_package_id = $group_package->getPackagePlanGroupDefault();
		} else {
			$group_package_id = $result;
		}

		$result_bundle = $bundle->getBundle($group_package_id);

		foreach ($result_bundle as $key => $value) {
			$user_package->createUserPackage($value->care_package_id, $user_id);
		}

		$plan_type_data = array(
			'user_id'           => $user_id,
			'package_group_id'  => $group_package_id,
			'duration'          => '1 year',
			'plan_start'        => $start_date,
			'active_plan_id'    => $active_plan->customer_active_plan_id
		);

		$plan_type->createUserPlanType($plan_type_data);
		$user_plan_history = new UserPlanHistory();
		$user_plan_history_data = array(
			'user_id'       => $user_id,
			'type'          => "started",
			'date'          => $start_date,
			'customer_active_plan_id' => $active_plan->customer_active_plan_id
		);

		$user_plan_history->createUserPlanHistory($user_plan_history_data);
        // update vacant seat
		DB::table('employee_replacement_seat')
		->where('employee_replacement_seat_id', $check->employee_replacement_seat_id)
		->update(['updated_at' => date('Y-m-d H:i:s'), 'vacant_status' => 1]);

        // check company credits
		$customer = DB::table('customer_credits')->where('customer_id', $customer_id)->first();

		if($input['medical_credits'] > 0) {
            // medical credits
			if($customer->balance >= $input['medical_credits']) {

				$result_customer_active_plan = PlanHelper::allocateCreditBaseInActivePlan($customer_id, $input['medical_credits'], "medical");

				if($result_customer_active_plan) {
					$customer_active_plan_id = $result_customer_active_plan;
				} else {
					$customer_active_plan_id = NULL;
				}

                // give credits
				$wallet_class = new Wallet();
				$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
				$update_wallet = $wallet_class->addCredits($user_id, $input['medical_credits']);

				$employee_logs = new WalletHistory();

				$wallet_history = array(
					'wallet_id'     => $wallet->wallet_id,
					'credit'            => $input['medical_credits'],
					'logs'              => 'added_by_hr',
					'running_balance'   => $input['medical_credits'],
					'customer_active_plan_id' => $customer_active_plan_id
				);

				$employee_logs->createWalletHistory($wallet_history);
				$customer_credits = new CustomerCredits();

				$customer_credits_result = $customer_credits->deductCustomerCredits($customer->customer_credits_id, $input['medical_credits']);

				if($customer_credits_result) {
					$company_deduct_logs = array(
						'customer_credits_id'   => $customer->customer_credits_id,
						'credit'                => $input['medical_credits'],
						'logs'                  => 'added_employee_credits',
						'user_id'               => $user_id,
						'running_balance'       => $customer->balance - $input['medical_credits'],
						'customer_active_plan_id' => $customer_active_plan_id
					);

					$customer_credit_logs = new CustomerCreditLogs( );
					$customer_credit_logs->createCustomerCreditLogs($company_deduct_logs);
				}
			}
		}

		if($input['wellness_credits'] > 0) {
            // wellness credits
			if($customer->wellness_credits >= $input['wellness_credits']) {
				$result_customer_active_plan = self::allocateCreditBaseInActivePlan($customer_id, $input['wellness_credits'], "wellness");

				if($result_customer_active_plan) {
					$customer_active_plan_id = $result_customer_active_plan;
				} else {
					$customer_active_plan_id = NULL;
				}
                // give credits
				$wallet_class = new Wallet();
				$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
				$update_wallet = $wallet_class->addWellnessCredits($user_id, $input['wellness_credits']);

				$wallet_history = array(
					'wallet_id'     => $wallet->wallet_id,
					'credit'        => $input['wellness_credits'],
					'logs'          => 'added_by_hr',
					'running_balance'   => $input['wellness_credits'],
					'customer_active_plan_id' => $customer_active_plan_id
				);

				\WellnessWalletHistory::create($wallet_history);
				$customer_credits = new CustomerCredits();
				$customer_credits_result = $customer_credits->deductCustomerWellnessCredits($customer->customer_credits_id, $input['wellness_credits']);

				if($customer_credits_result) {
					$company_deduct_logs = array(
						'customer_credits_id'   => $customer->customer_credits_id,
						'credit'                => $input['wellness_credits'],
						'logs'                  => 'added_employee_credits',
						'user_id'               => $user_id,
						'running_balance'       => $customer->wellness_credits - $input['wellness_credits'],
						'customer_active_plan_id' => $customer_active_plan_id
					);
					$customer_credits_logs = new CustomerWellnessCreditLogs();
					$customer_credits_logs->createCustomerWellnessCreditLogs($company_deduct_logs);
				}
			}
		}

        // send email to new employee
		$email_data = [];
		$company = DB::table('corporate')->where('corporate_id', $corporate->corporate_id)->first();
		$email_data['company']   = ucwords($company->company_name);
		$email_data['emailName'] = $input['first_name'].' '.$input['last_name'];
		$email_data['emailTo']   = $input['email'];
		$email_data['email'] = $input['email'];
        // $email_data['email'] = 'allan.alzula.work@gmail.com';
		$email_data['emailPage'] = 'email-templates.latest-templates.mednefits-welcome-member-enrolled';
		$email_data['start_date'] = date('d F Y', strtotime($start_date));
		$email_data['name'] = $input['first_name'].' '.$input['last_name'];
		$email_data['emailSubject'] = "WELCOME TO MEDNEFITS CARE";
		$email_data['pw'] = $password;
		$api = "https://api.medicloud.sg/employees/welcome_email";
        // \httpLibrary::postHttp($api, $email_data, []);
		return array('status' => true, 'message' => 'Employee Enrolled.');
	}

	public function checkVacantEmployeeSeat( )
	{
		$input = Input::all();
		// $customer_id = $input['customer_id'];
		$customer_id = PlanHelper::getCusomerIdToken();


		if(empty($input['employee_replacement_seat_id']) || $input['employee_replacement_seat_id'] == null) {
			return array('status' => false, 'message' => 'Employee Vacan seat ID is required.');
		}

		$seat = DB::table('employee_replacement_seat')
		->where('employee_replacement_seat_id', $input['employee_replacement_seat_id'])
		->where('customer_id', $customer_id)
		->first();

		if(!$seat) {
			return array('status' => false, 'message' => 'Employee Vacant Seat does not exists');
		}

		if((int)$seat->vacant_status == 1) {
			return array('status' => false, 'message' => 'Employee Vacant Seat already occupied');	
		}

		return array('status' => true);
	}

	public function getEmployeePlanInformation( )
	{
		$input = Input::all();

		if(empty($input['employee_id']) || $input['employee_id'] == null) {
			return array('status' => false, 'message' => 'Employee ID is required.');
		}

		$plan_name = [];

        // get user employee plan
		$plan_user_history = DB::table('user_plan_history')
		->where('user_id', $input['employee_id'])
		->where('type', 'started')
		->orderBy('created_at', 'desc')
		->first();
		$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)->first();
		$plan_name[] = array('plan_type' => PlanHelper::getPlanNameType($active_plan->account_type, $active_plan->plan_method), 'user_type' => 'Employee'); 


        // get dependents
		$dependents = DB::table('employee_family_coverage_sub_accounts')
		->where('owner_id', $input['employee_id'])
		->where('deleted', 0)
		->get();

		$dependent_plan_data = [];
		foreach ($dependents as $key => $dependent) {
			$dependent_history = DB::table('dependent_plan_history')
			->where('user_id', $dependent->user_id)
			->orderBy('created_at', 'desc')
			->first();
			if($dependent_history) {
				$dependent_plan = DB::table('dependent_plans')
				->where('dependent_plan_id', $dependent_history->dependent_plan_id)
				->first();
				if($dependent_plan) {
					$dependent_plan_data[] = PlanHelper::getPlanNameType($dependent_plan->account_type, $dependent_plan->plan_method);
				}
			}
		}

		if(sizeof($dependent_plan_data) > 0) {	
			$dependents_data_final = array_unique($dependent_plan_data);

			foreach ($dependents_data_final as $key => $final_dep) {
				array_push($plan_name, array('user_type' => 'Dependents', 'plan_type' => $final_dep));
			}
		}


        // $final = array_unique($plan_name);

		return $plan_name;
	}

	public function employeeResetAccount( )
	{
		$input = Input::all();

		if(empty($input['employee_id']) || $input['employee_id'] == null) {
			return array('status' => false, 'message' => 'Employee ID is required.');
		}

		return PlanHelper::resetEmployeeAccount($input['employee_id']);
	}

	public function downloadInNetwork( )
	{
		$input = Input::all();
		$session = self::checkToken($input['token']);
		$transaction_details = [];
		$start = date('Y-m-d', strtotime($input['start']));
		$end = SpendingInvoiceLibrary::getEndDate($input['end']);
		$in_network_spent = 0;
		$e_claim_spent = 0;
		$e_claim_pending = 0;
		$health_screening_breakdown = 0;
		$general_practitioner_breakdown = 0;
		$dental_care_breakdown = 0;
		$tcm_breakdown = 0;
		$health_specialist_breakdown = 0;
		$wellness_breakdown = 0;
		$allocation = 0;
		$total_credits = 0;
		$total_cash = 0;
		$deleted_employee_allocation = 0;
		$deleted_transaction_cash = 0;
		$deleted_transaction_credits = 0;
		$total_e_claim_spent = 0;

		$total_in_network_transactions = 0;
		$total_deleted_in_network_transactions = 0;
		$total_search_cash = 0;
		$total_search_credits = 0;
		$total_in_network_spent = 0;
		$total_deducted_allocation = 0;
		$break_down_calculation = 0;

		$total_credits_transactions = 0;
		$total_cash_transactions = 0;
		$total_credits_transactions_deleted = 0;
		$total_cash_transactions_deleted = 0;

		$total_in_network_spent_credits_transaction = 0;
		$total_in_network_spent_cash_transaction = 0;
		$total_lite_plan_consultation = 0;
		$lite_plan = false;
		$spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';
		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $session->customer_buy_start_id)->first();
		$corporate_members = DB::table('corporate_members')
		->where('corporate_id', $account->corporate_id)
		->get();
		$container = array();

		if($spending_type == 'medical') {
			$table_wallet_history = 'wallet_history';
		} else {
			$table_wallet_history = 'wellness_wallet_history';
		}

		foreach ($corporate_members as $key => $member) {
			$ids = StringHelper::getSubAccountsID($member->user_id);
			$transactions = DB::table('transaction_history')
			->whereIn('UserID', $ids)
			->where('spending_type', $spending_type)
			->where('paid', 1)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $end)
			->orderBy('date_of_transaction', 'desc')
			->get();

			foreach ($transactions as $key => $trans) {
				if($trans) {

					if($trans->procedure_cost >= 0 && $trans->paid == 1 || $trans->procedure_cost >= 0 && $trans->paid == "1") {
						
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

						$total_amount = number_format($trans->procedure_cost, 2);

						if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
							$payment_type = "Cash";
							if($lite_plan && $trans->lite_plan_enabled == 1 || $lite_plan && $trans->lite_plan_enabled == "1") {
								$total_amount = number_format($trans->procedure_cost + $trans->co_paid_amount, 2);
							}
						} else {
							$payment_type = "Mednefits Credits";
							if($lite_plan && $trans->lite_plan_enabled == 1 || $lite_plan && $trans->lite_plan_enabled == "1") {
								$total_amount = number_format($trans->procedure_cost + $trans->co_paid_amount, 2);
							}
						}


						$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

						$container[] = array(
							'TRANSACTION ID'	=> strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
							'MEMBER'			=> ucwords($customer->Name),
							'PROVIDER'			=> ucwords($clinic->Name),
							'DATE'				=> date('d F Y, h:ia', strtotime($trans->date_of_transaction)), 
							'ITEMS/SERVICE'		=> $clinic_name,
							'TOTAL AMOUNT'		=> $total_amount,
							'PAYMENT TYPE'		=> $payment_type
						);
					}
				}
			}
		}

		return \Excel::create('In-Network Transactions', function($excel) use($container) {

			$excel->sheet('In-Network', function($sheet) use($container) {
				$sheet->fromArray( $container );
			});

		})->export('csv');
	}
	
	public function spendingAccountStatus( )
	{
		$customer_id = PlanHelper::getCusomerIdToken();

		if(!$customer_id) {
			return array('status' => false, 'message' => 'customer_id is required');
		}
	  
		return CustomerHelper::getAccountSpendingStatus($customer_id);
	}

	public function getExcelLink( )
	{
		$customer_id = PlanHelper::getCusomerIdToken();

		if(!$customer_id) {
			return array('status' => false, 'message' => 'customer_id is required');
		}

		$status = CustomerHelper::getAccountSpendingStatus($customer_id);
		// return $status;
		$link = CustomerHelper::getExcelLink($status);
		return $link;
	}

	public function getEmployeeListsBulk( )
  	{

	  	$input = Input::all();
	  
		if(empty($input['spending_type']) || $input['spending_type'] == null)	{
			return array('status' => false, 'message' => 'spending_type is required');
		}

		$customer = StringHelper::getJwtHrSession();
		$spending_type = $input['spending_type'];
		$customer_id = $customer->customer_buy_start_id;
		$spending = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderby('created_at', 'desc')->first();
		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $spending->customer_id)->first();
		// $members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();
		$members = \CustomerHelper::getActivePlanUsers($customer_id);
		$customer_wallet = DB::table('customer_credits')->where('customer_id', $spending->customer_id)->first();
		$pending = DB::table('spending_purchase_invoice')->where('customer_plan_id', $spending->customer_plan_id)->where('payment_status', 0)->count();
		$plan = DB::table('customer_plan')->where('customer_plan_id', $spending->customer_plan_id)->first();
		$total_allocation = 0;
		$total_company_medical_allocation = 0;
		$total_company_medical_supp = 0;
		$total_company_wellness_allocation = 0;
		$total_company_wellness_supp = 0;
		$total_medical_percentage = $spending->medical_supplementary_credits;
		$total_wellness_percentage = $spending->wellness_supplementary_credits;
		$total_supp = 0;
		$term_start = null;
		$term_end = null;
		$term_duration = null;

		if($spending_type == 'medical') {
			$term_start = $spending->medical_spending_start_date;
			$term_end = $spending->medical_spending_end_date;
			$account_type = $plan->account_type;
			$plan_method = $spending->medical_plan_method;
		} else {
			$term_start = $spending->wellness_spending_start_date;
			$term_end = $spending->wellness_spending_end_date;
			$account_type = $plan->account_type;
			$plan_method = $spending->wellness_plan_method;
		}

		$date1 = new DateTime($term_start);
		$date2 = new DateTime($term_end);
		$interval = $date1->diff($date2);
		$term_duration = $interval->m + 1;

		foreach ($members as $key => $member) {
			$member_id = $member;
			$wallet = DB::table('e_wallet')->where('UserID', $member_id)->first();
			if($spending_type == 'medical') {
				$member_spending_dates_medical = MemberHelper::getMemberCreditReset($member_id, 'current_term', 'medical');
				$allocation  = PlanHelper::memberMedicalUpdatedCreditsSummary($wallet->wallet_id, $member_id, $member_spending_dates_medical['start'], $member_spending_dates_medical['end']);
				$total_supp += $allocation['total_supp'];
				$total_allocation += $allocation['allocation'];
				$total_company_medical_allocation += $allocation['allocation'];
				$total_company_medical_supp += $allocation['total_supp'];
			} else if($spending_type == 'wellness'){
				$member_spending_dates_wellness = MemberHelper::getMemberCreditReset($member_id, 'current_term', 'wellness');
				$allocation  = PlanHelper::memberWellnessUpdatedCreditsSummary($wallet->wallet_id, $member_id, $member_spending_dates_wellness['start'], $member_spending_dates_wellness['end']);
				$total_supp += $allocation['total_supp'];
				$total_allocation += $allocation['allocation'];
				$total_company_wellness_allocation += $allocation['allocation'];
				$total_company_wellness_supp += $allocation['total_supp'];
			} else {
				$member_spending_dates_medical = MemberHelper::getMemberCreditReset($member_id, 'current_term', 'medical');
				$member_spending_dates_wellness = MemberHelper::getMemberCreditReset($member_id, 'current_term', 'wellness');
				$allocation_medical  = PlanHelper::memberMedicalUpdatedCreditsSummary($wallet->wallet_id, $member_id, $member_spending_dates_medical['start'], $member_spending_dates_medical['end']);
				$allocation_wellness  = PlanHelper::memberWellnessUpdatedCreditsSummary($wallet->wallet_id, $member_id, $member_spending_dates_wellness['start'], $member_spending_dates_wellness['end']);
				$temp_allocation = $allocation_medical['allocation'] + $allocation_wellness['allocation'];
				$temp_supp = $allocation_medical['total_supp'] + $allocation_wellness['total_supp'];
				$total_company_medical_allocation += $allocation_medical['allocation'];
				$total_company_wellness_allocation += $allocation_wellness['allocation'];
				$total_company_medical_supp += $allocation_medical['total_supp'];
				$total_company_wellness_supp += $allocation_wellness['total_supp'];
				$total_supp += $temp_allocation;
				$total_allocation += $temp_supp;
			}
		}

		$limit = !empty($input['per_page']) ? $input['per_page'] : 25;
		$members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->where('removed_status', 0)->paginate($limit);

		$paginate = [];
		$final_user = [];
		$paginate['last_page'] = $members->getLastPage();
		$paginate['current_page'] = $members->getCurrentPage();
		$paginate['total_data'] = $members->getTotal();
		$paginate['from'] = $members->getFrom();
		$paginate['to'] = $members->getTo();
		$paginate['count'] = $members->count();

		foreach ($members as $key => $member) {
			$user = DB::table('user')->where('UserID', $member->user_id)->first();
			$wallet = DB::table('e_wallet')->where('UserID', $member->user_id)->first();
			
			if($spending_type == 'medical') {
				$allocation  = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $member->user_id);
				$schedule = DB::table('wallet_entitlement_schedule')
									->where('member_id', $member->user_id)
									->where('spending_type', 'medical')
									->where('status', 0)
									->orderBy('created_at', 'desc')
									->first();

				$member->allocation['current_allocation'] = $allocation['allocation'];
				$member->allocation['new_allocation'] = $schedule ? $schedule->new_allocation_credits : 0;
				$member->allocation['effective_date'] = $schedule ? date('d/m/Y', strtotime($schedule->effective_date)) : date('d/m/Y');
				$member->allocation['allocation_schedule'] = $schedule ? true : false;
			} else if($spending_type == 'wellness'){
				$allocation  = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $member->user_id);
				$schedule = DB::table('wallet_entitlement_schedule')
									->where('member_id', $member->user_id)
									->where('spending_type', 'wellness')
									->where('status', 0)
									->orderBy('created_at', 'desc')
									->first();
				
				$member->allocation['current_allocation'] = $allocation['allocation'];
				$member->allocation['new_allocation'] = $schedule ? $schedule->new_allocation_credits : 0;
				$member->allocation['effective_date'] = $schedule ? date('d/m/Y', strtotime($schedule->effective_date)) : date('d/m/Y');
				$member->allocation['allocation_schedule'] = $schedule ? true : false;
			} else {
				$allocation_medical  = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $member->user_id);
				$schedule_medical = DB::table('wallet_entitlement_schedule')
									->where('member_id', $member->user_id)
									->where('spending_type', 'medical')
									->where('status', 0)
									->orderBy('created_at', 'desc')
									->first();
				
				$allocation_wellness  = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $member->user_id);
				$schedule_wellness = DB::table('wallet_entitlement_schedule')
									->where('member_id', $member->user_id)
									->where('spending_type', 'wellness')
									->where('status', 0)
									->orderBy('created_at', 'desc')
									->first();

				$member->medical_allocation['current_allocation'] = $allocation_medical['allocation'];
				$member->medical_allocation['new_allocation'] = $schedule_medical ? $schedule_medical->new_allocation_credits : 0;
				$member->medical_allocation['effective_date'] = $schedule_medical ? date('d/m/Y', strtotime($schedule_medical->effective_date)) : date('d/m/Y');
				$member->medical_allocation['allocation_schedule'] = $schedule_medical ? true : false;

				$member->wellness_allocation['current_allocation'] = $allocation_wellness['allocation'];
				$member->wellness_allocation['new_allocation'] = $schedule_wellness ? $schedule_wellness->new_allocation_credits : 0;
				$member->wellness_allocation['effective_date'] = $schedule_wellness ? date('d/m/Y', strtotime($schedule_wellness->effective_date)) : date('d/m/Y');
				$member->wellness_allocation['allocation_schedule'] = $schedule_wellness ? true : false;
			}
			
			
			$member->member_id = $member->user_id;
			$member->fullname = $user->Name;
			array_push($final_user, $member);
		}

		$paginate['data'] = $final_user;
		
		$user_spending_dates = CustomerHelper::getCustomerCreditReset($spending->customer_id, 'current_term', $spending_type);

		if($spending_type == "medical")	{
			$company_credits = \CustomerHelper::getCustomerMedicalTotalCredits($spending->customer_id, $user_spending_dates);
		} else {
			$company_credits = \CustomerHelper::getCustomerWellnessTotalCredits($spending->customer_id, $user_spending_dates);
		}
		
		return [
			'status' => true, 
			'customer_id' => $customer_id, 
			'currency_type' => strtoupper($customer_wallet->currency_type), 
			'medical_enable' => (int)$spending->medical_enable == 1 ? true : false, 
			'wellness_enable' => (int)$spending->wellness_enable == 1 ? true : false,
			'total_company_medical_allocation'	=> $total_company_medical_allocation,
			'total_company_medical_supp'		=> $total_company_medical_supp,
			'total_medical_supplementary_usage'		=> $total_company_medical_supp,
			'total_company_wellness_supp'		=> $total_company_wellness_supp,
			'total_wellness_supplementary_usage'		=> $total_company_wellness_supp,
			'total_company_wellness_allocation'	=> $total_company_wellness_allocation,
			'total_medical_company_supplementary_allocation' => $total_company_medical_allocation * $total_medical_percentage,
			'total_wellnes_company_supplementary_allocation' => $total_company_wellness_allocation * $total_wellness_percentage,
			'total_purchase_credits' => $company_credits['total_purchase_credits'],
			'total_bonus_credits' => $company_credits['total_bonus_credits'],
			'total_allocated_credits' => $total_allocation,
			'total_credits'		=> $account_type == "lite_plan" && $plan_method == "pre_paid" ? $company_credits['total_purchase_credits'] + $company_credits['total_bonus_credits'] : $company_credits['total_purchase_credits'] + $company_credits['total_bonus_credits'] + $total_supp,
			'term_start'	=> $term_start,
			'term_end'	=> $term_end,
			'term_duration'	=> $term_duration,
			'spending_type'	=> $spending_type,
			'payment_status' => $account_type == "lite_plan" && $plan_method == "pre_paid" && $pending > 0 ? false : true,
			'members' => $paginate
		];
  }

  	public function downloadSpendingInvoice( )
    {

		$input = Input::all();

        if(empty($input['id']) || $input['id'] == null) {
            return ['status' => false, 'message' => 'id is required'];
        }

		$result = StringHelper::getJwtHrToken($input['token']);
		$customer_id = $result->customer_buy_start_id;
		
		if(!$customer_id) {
			return ['status' => false, 'message' => 'Invalid access token'];
		}

		$spendingPurchase = DB::table('spending_purchase_invoice')
								->where('spending_purchase_invoice_id', $input['id'])
								->where('customer_id', $customer_id)
								->first();
        if(!$spendingPurchase) {
            return ['status' => false, 'message' => 'Spending Purchase does not exists'];
        }

        $active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $spendingPurchase->customer_active_plan_id)->first();
        $customer_wallet = DB::table('customer_credits')->where('customer_id', $spendingPurchase->customer_id)->first();
        
        $data = array();
        $data['payment_status'] = $spendingPurchase->payment_status == 1 ? 'PAID' : 'PENDING';
        $data['paid'] = $spendingPurchase->payment_status == 1 ? true : false;
        $data['invoice_date'] = date('d F Y', strtotime($spendingPurchase->invoice_date));
        $data['invoice_number'] = $spendingPurchase->invoice_number;
        $total = (float)$spendingPurchase->medical_purchase_credits + (float)$spendingPurchase->wellness_purchase_credits;
        $data['total']  = number_format($total, 2);
        $data['amount_due'] = number_format($total - (float)$spendingPurchase->payment_amount, 2);
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
        $data['account_type'] = PlanHelper::getAccountType($active_plan->account_type);
        $data['plan_type'] = 'Basic Plan Mednefits Care (Corporate)';
        $data['currency_type']   = strtoupper($customer_wallet->currency_type);
        // medical spending account
        $data['medical_spending_account'] = (float)$spendingPurchase->medical_purchase_credits > 0 ? true : false;
        $data['medical_credits_purchase'] = number_format($spendingPurchase->medical_purchase_credits, 2);
        $data['medical_credit_bonus'] = number_format($spendingPurchase->medical_credit_bonus, 2);
        $data['medical_total_credits']  = number_format($spendingPurchase->medical_purchase_credits + $spendingPurchase->medical_credit_bonus, 2);
        $data['medical_discount_credits']  = number_format($spendingPurchase->medical_credit_bonus, 2);

        // wellness spending account
        $data['wellness_spending_account'] = (float)$spendingPurchase->wellness_purchase_credits > 0 ? true : false;
        $data['wellness_credits_purchase'] = number_format($spendingPurchase->wellness_purchase_credits, 2);
        $data['wellness_credit_bonus'] = number_format($spendingPurchase->wellness_credit_bonus, 2);
        $data['wellness_total_credits']  = number_format($spendingPurchase->wellness_purchase_credits + $spendingPurchase->wellness_credit_bonus, 2);
        $data['wellness_discount_credits']  = number_format($spendingPurchase->wellness_credit_bonus, 2);
		
		// return View::make('invoice.spending-purchase-invoice', $data);
		$pdf = PDF::loadView('invoice.spending-purchase-invoice', $data);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');
		return $pdf->stream($data['invoice_number'].' - '.time().'.pdf');
	}
	
	public function getSpendingInvoicePurchaseLists( )
    {
		$input = Input::all();

		$result = self::checkSession();
		$customer_id = $result->customer_buy_start_id;
		if(!$customer_id) {
			return ['status' => false, 'message' => 'Invalid access token'];
		}

        $limit = !empty($input['limit']) ? $input['limit'] : 10;
        $pagination = [];

        $invoices = DB::table('spending_purchase_invoice')->where('customer_id', $customer_id)->paginate($limit);
        $format = [];

        $pagination['last_page'] = $invoices->getLastPage();
		$pagination['current_page'] = $invoices->getCurrentPage();
		$pagination['from'] = $invoices->getFrom();
		$pagination['count'] = $invoices->count();
		

        foreach($invoices as $key => $spendingPurchase) {
            $active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $spendingPurchase->customer_active_plan_id)->first();
            $customer_wallet = DB::table('customer_credits')->where('customer_id', $spendingPurchase->customer_id)->first();
            
            $data = array();
            $data['spending_purchase_invoice_id'] = $spendingPurchase->spending_purchase_invoice_id;
            $data['payment_status'] = $spendingPurchase->payment_status == 1 ? 'PAID' : 'PENDING';
            $data['paid'] = $spendingPurchase->payment_status == 1 ? true : false;
            $data['invoice_date'] = date('d/m/Y', strtotime($spendingPurchase->invoice_date));
            $data['invoice_number'] = $spendingPurchase->invoice_number;
            $total = (float)$spendingPurchase->medical_purchase_credits + (float)$spendingPurchase->wellness_purchase_credits;
            $data['total']  = number_format($total, 2);
            $data['amount_due'] = number_format($total - (float)$spendingPurchase->payment_amount, 2);
            $data['invoice_due'] = date('d/m/Y', strtotime($spendingPurchase->invoice_due));
            $data['payment_date'] = $spendingPurchase->payment_date ? date('d/m/Y', strtotime($spendingPurchase->payment_date)) : null;
            $data['remarks']    = $spendingPurchase->remarks;
            $data['company_name']   = $spendingPurchase->company_name;
            $data['company_address']   = $spendingPurchase->company_address;
            $data['postal']   = $spendingPurchase->postal;
            $data['contact_name']   = $spendingPurchase->contact_name;
            $data['contact_number']   = $spendingPurchase->contact_number;
            $data['contact_email']   = $spendingPurchase->contact_email;
            $data['plan_start']   = date('d/m/Y', strtotime($spendingPurchase->plan_start));
            $data['plan_end']   = date('d/m/Y', strtotime($spendingPurchase->plan_start));
            $data['duration']   = $spendingPurchase->duration;
            $data['account_type'] = \PlanHelper::getAccountType($active_plan->account_type);
            $data['plan_type'] = 'Pre-paid Credits Plan Mednefits Care (Corporate)';
            $data['currency_type']   = strtoupper($customer_wallet->currency_type);
            // medical spending account
            $data['medical_spending_account'] = (float)$spendingPurchase->medical_purchase_credits > 0 ? true : false;
            $data['medical_credits_purchase'] = number_format($spendingPurchase->medical_purchase_credits, 2);
            $data['medical_credit_bonus'] = number_format($spendingPurchase->medical_credit_bonus, 2);
            $data['medical_total_credits']  = number_format($spendingPurchase->medical_purchase_credits + $spendingPurchase->medical_credit_bonus, 2);

            // wellness spending account
            $data['wellness_spending_account'] = (float)$spendingPurchase->wellness_purchase_credits > 0 ? true : false;
            $data['wellness_credits_purchase'] = number_format($spendingPurchase->wellness_purchase_credits, 2);
            $data['wellness_credit_bonus'] = number_format($spendingPurchase->wellness_credit_bonus, 2);
			$data['wellness_total_credits']  = number_format($spendingPurchase->wellness_purchase_credits + $spendingPurchase->wellness_credit_bonus, 2);
			$data['spending_type'] = "purchase";
            $format[] = $data;
        }

		// check if there is a spending transaction invoice
		$credits_statements = DB::table('company_credits_statement')
                                ->where('statement_customer_id', $customer_id)
								->paginate($limit);
		$total_spending_transaction_count = 0;
		foreach ($credits_statements as $key => $data) {
			if(date('Y-m-d') >= date('Y-m-d', strtotime($data->statement_date))) {
				$statement = SpendingInvoiceLibrary::getInvoiceSpending($data->statement_id, false);
				$statement['total_due'] = $statement['statement_amount_due'];
			
				$temp = array(
					'invoice_number'    => $data->statement_number,
					'invoice_date'        => date('d/m/Y', strtotime($data->statement_date)),
					'type'              => 'Invoice',
					'total'            => 'S$'.$statement['statement_total_amount'],
					'paid'            => (int)$data->statement_status,
					'statement_id'      => $data->statement_id,
					'currency_type'     => $statement['currency_type'],
					'spending_type'		=> 'transaction'
				);
				// $total_spending_transaction_count = $credits_statements->count();
				$total_spending_transaction_count++;
				array_push($format, $temp);
			}
		}

		$pagination['count'] = $invoices->count() + $total_spending_transaction_count;
		$pagination['to'] = $invoices->getTo() + $total_spending_transaction_count;
		$pagination['total_data'] = $invoices->getTotal() + $total_spending_transaction_count;
        $pagination['data'] = $format;
		return $pagination;
	}
	
	public function getPlanDetailsold( )  
	{
		$input = Input::all();

		$result = self::checkSession();
		$customer_id = $result->customer_buy_start_id;
		if(!$customer_id) {
			return ['status' => false, 'message' => 'Invalid access token'];
		}

		// get latest plan
		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();
		// get active plan lists
		$active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
		// get customer plan status
		$plan_status = DB::table('customer_plan_status')->where('customer_plan_id', $plan->customer_plan_id)->orderBy('created_at', 'desc')->first();
	
		// format employee plan details
		$employee_acount_details = [
		  'customer_active_plan'      => $active_plan->customer_active_plan_id,
		  'customer_id'               => $customer_id,
		  'customer_plan_id'          => $plan->customer_plan_id,
		  'plan_start'                => date('Y-m-d', strtotime($plan->plan_start)),
		  'duration'                  => $active_plan->duration,
		  'plan_type'                 => \PlanHelper::getAccountType($plan->account_type),
		  'total_enrolled_employees'  => $plan_status->enrolled_employees,
		  'account_type'              => $plan->account_type
		];
	
		// check if there is a dependent plan
		$dependent_plan = DB::table('dependent_plans')->where('customer_plan_id', $plan->customer_plan_id)->first();
	
		if($dependent_plan) {
		  $dependent_plan_status = DB::table('dependent_plan_status')->where('customer_plan_id', $plan->customer_plan_id)->orderBy('created_at', 'desc')->first();
		  $dependent_acount_details = [
			'customer_id'               => $customer_id,
			'customer_active_plan'      => $active_plan->customer_active_plan_id,
			'customer_plan_id'          => $plan->customer_plan_id,
			'dependent_plan_id'         => $dependent_plan->dependent_plan_id,
			'plan_start'                => date('Y-m-d', strtotime($dependent_plan->plan_start)),
			'duration'                  => $dependent_plan->duration,
			'plan_type'                 => \PlanHelper::getAccountType($dependent_plan->account_type),
			'total_enrolled_employees'  => $dependent_plan_status->total_enrolled_dependents,
			'account_type'              => $plan->account_type
		  ];
		} else {
		  $dependent_acount_details = [
			'customer_id'               => $customer_id,
			'customer_active_plan'      => $active_plan->customer_active_plan_id,
			'customer_plan_id'          => $plan->customer_plan_id,
			'plan_start'                => null,
			'duration'                  => null,
			'plan_type'                 => null,
			'total_enrolled_employees'  => null
		  ];
		}
	
		return ['status' => true, 'employee_acount_details' => $employee_acount_details, 'dependent_acount_details' => $dependent_acount_details];
	}

	public function getPlanDetails( )  
	{
		$input = Input::all();

		$result = self::checkSession();
		$customer_id = $result->customer_buy_start_id;
		if(!$customer_id) {
			return ['status' => false, 'message' => 'Invalid access token'];
		}

		if(empty($input['type']) || $input['type'] == null) {
			return ['status' => false, 'message' => 'type is required'];
		}
	  
		$type = $input['type'];

		if($type == "new") {
			// get latest plan
			$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();
		} else {
			if(empty($input['customer_plan_id']) || $input['customer_plan_id'] == null) {
				return ['status' => false, 'message' => 'customer_plan_id is required'];
			}
			// old plans
			$plan = DB::table('customer_plan')->where('customer_plan_id', $input['customer_plan_id'])->orderBy('created_at', 'desc')->first();
		}
	  
		if($plan->account_type == "lite_plan") {
			// get active plan lists
			$active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
			// get customer plan status
			$plan_status = DB::table('customer_plan_status')->where('customer_plan_id', $plan->customer_plan_id)->orderBy('created_at', 'desc')->first();
	
			// format employee plan details
			$employee_acount_details = [
			  'customer_active_plan'      => $active_plan->customer_active_plan_id,
			  'customer_id'               => $customer_id,
			  'customer_plan_id'          => $plan->customer_plan_id,
			  'plan_start'                => date('Y-m-d', strtotime($plan->plan_start)),
			  'duration'                  => $active_plan->duration,
			  'plan_type'                 => \PlanHelper::getAccountType($plan->account_type),
			  'total_enrolled_employees'  => $plan_status->enrolled_employees,
			  'account_type'              => $plan->account_type
			];
	
			if($plan->account_type != "lite_plan")  {
			  $employee_acount_details['invoice_date'] = null;
			  $employee_acount_details['invoice_due'] = null;
			  // get invoice
			  $payment = DB::table('customer_cheque_logs')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->first();
			  if($payment) {
				$invoice = DB::table('corporate_invoice')->where('corporate_invoice_id', $payment->invoice_id)->first();
				if($invoice) {
				  $employee_acount_details['invoice_date'] = $invoice->invoice_date;
				  $employee_acount_details['invoice_due'] = $invoice->invoice_due;
				}
			  }
			}
	
			// check if there is a dependent plan
			$dependent_plan = DB::table('dependent_plans')->where('customer_plan_id', $plan->customer_plan_id)->first();
	
			if($dependent_plan) {
			  $dependent_plan_status = DB::table('dependent_plan_status')->where('customer_plan_id', $plan->customer_plan_id)->orderBy('created_at', 'desc')->first();
			  $dependent_acount_details = [
				'customer_id'               => $customer_id,
				'customer_active_plan'      => $active_plan->customer_active_plan_id,
				'customer_plan_id'          => $plan->customer_plan_id,
				'dependent_plan_id'         => $dependent_plan->dependent_plan_id,
				'plan_start'                => date('Y-m-d', strtotime($dependent_plan->plan_start)),
				'duration'                  => $dependent_plan->duration,
				'plan_type'                 => \PlanHelper::getAccountType($dependent_plan->account_type),
				'total_enrolled_employees'  => $dependent_plan_status->total_enrolled_dependents,
				'account_type'              => $plan->account_type
			  ];
	
			  if($dependent_plan->account_type != "lite_plan")  {
				$invoice = DB::table('dependent_invoice')->where('dependent_plan_id', $dependent_plan->dependent_plan_id)->first();
				if($invoice) {
				  $dependent_acount_details['invoice_date'] = $invoice->invoice_date;
				  $dependent_acount_details['invoice_due'] = $invoice->invoice_due;
				}
			  }
			} else {
			  $dependent_acount_details = [
				'customer_id'               => $customer_id,
				'customer_active_plan'      => $active_plan->customer_active_plan_id,
				'customer_plan_id'          => $plan->customer_plan_id,
				'plan_start'                => null,
				'duration'                  => null,
				'plan_type'                 => null,
				'total_enrolled_employees'  => null,
				'invoice_date'              => null,
				'invoice_due'              => null,
			  ];
			}
	
			$data['data'] = array(
			  'status' => true,
			  'customer_active_plan_id' => $active_plan->customer_active_plan_id,
			  'employee_acount_details' => $employee_acount_details, 
			  'dependent_acount_details' => $dependent_acount_details
			);
			$data['paginate'] = false;
		} else {
			// paginate
			// get active plan lists
			$active_plans = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->paginate(1);
			// get customer plan status
			// $plan_status = DB::table('customer_plan_status')->where('customer_plan_id', $plan->customer_plan_id)->orderBy('created_at', 'desc')->first();
			foreach($active_plans as $key => $active_plan) {
			  // format employee plan details
			  $employee_acount_details = [
				'customer_active_plan'      => $active_plan->customer_active_plan_id,
				'customer_id'               => $customer_id,
				'customer_plan_id'          => $plan->customer_plan_id,
				'plan_start'                => date('Y-m-d', strtotime($plan->plan_start)),
				'duration'                  => $active_plan->duration,
				'plan_type'                 => PlanHelper::getAccountType($plan->account_type),
				'total_enrolled_employees'  => $active_plan->employees,
				'individual_price'          => 300,
				'account_type'              => $plan->account_type
			  ];
	
			  if($plan->account_type != "lite_plan")  {
				$employee_acount_details['invoice_date'] = null;
				$employee_acount_details['invoice_due'] = null;
				// get invoice
				$payment = DB::table('customer_cheque_logs')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->first();
				if($payment) {
				  $invoice = DB::table('corporate_invoice')->where('corporate_invoice_id', $payment->invoice_id)->first();
				  if($invoice) {
					$employee_acount_details['invoice_date'] = $invoice->invoice_date;
					$employee_acount_details['invoice_due'] = $invoice->invoice_due;
					$employee_acount_details['individual_price'] = $invoice->individual_price;
				  }
				}
			  }
	
			  // check if there is a dependent plan
			  $dependent_plan = DB::table('dependent_plans')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->first();
	
			  if($dependent_plan) {
				// $dependent_plan_status = DB::table('dependent_plan_status')->where('customer_plan_id', $plan->customer_plan_id)->orderBy('created_at', 'desc')->first();
				$dependent_acount_details = [
				  'customer_id'               => $customer_id,
				  'customer_active_plan'      => $active_plan->customer_active_plan_id,
				  'customer_plan_id'          => $plan->customer_plan_id,
				  'dependent_plan_id'         => $dependent_plan->dependent_plan_id,
				  'plan_start'                => date('Y-m-d', strtotime($dependent_plan->plan_start)),
				  'duration'                  => $dependent_plan->duration,
				  'plan_type'                 => PlanHelper::getAccountType($dependent_plan->account_type),
				  'total_enrolled_employees'  => $dependent_plan->total_dependents,
				  'account_type'              => $plan->account_type
				];
	
				if($dependent_plan->account_type != "lite_plan")  {
				  $invoice = DB::table('dependent_invoice')->where('dependent_plan_id', $dependent_plan->dependent_plan_id)->first();
				  if($invoice) {
					$dependent_acount_details['invoice_date'] = $invoice->invoice_date;
					$dependent_acount_details['invoice_due'] = $invoice->invoice_due;
					$dependent_acount_details['individual_price'] = $invoice->individual_price;
				  }
				}
			  } else {
				$dependent_acount_details = [
				  'customer_id'               => $customer_id,
				  'customer_active_plan'      => $active_plan->customer_active_plan_id,
				  'customer_plan_id'          => $plan->customer_plan_id,
				  'plan_start'                => null,
				  'duration'                  => null,
				  'plan_type'                 => null,
				  'total_enrolled_employees'  => null,
				  'invoice_date'              => null,
				  'invoice_due'              => null,
				];
			  }
	
			  $data['data'] = array(
				'status' => true,
				'customer_active_plan_id' => $active_plan->customer_active_plan_id,
				'employee_acount_details'   => $employee_acount_details, 
				'dependent_acount_details'  => $dependent_acount_details,
			  );
			}
	
			$data['paginate'] = true;
			$data['last_page'] = $active_plans->getLastPage();
			$data['current_page'] = $active_plans->getCurrentPage();
			$data['total_data'] = $active_plans->getTotal();
			$data['from'] = $active_plans->getFrom();
			$data['to'] = $active_plans->getTo();
			$data['count'] = $active_plans->count();
		}
	   
		return $data;
	}

	public function getEnrollmentHistories( )
	{
		$input = Input::all();

		$result = self::checkSession();
		$customer_id = $result->customer_buy_start_id;
		if(!$customer_id) {
			return ['status' => false, 'message' => 'Invalid access token'];
		}

		if(empty($input['customer_active_plan_id']) || $input['customer_active_plan_id'] == null) {
			return ['status' => false, 'message' => 'customer_active_plan_id is required'];
		}

		$per_page = !empty($input['per_page']) ? $input['per_page'] : 1;
		// get active plan lists
		$active_plans = DB::table('enrollment_status')->where('customer_active_plan_id', $input['customer_active_plan_id'])->orderBy('created_at', 'asc')->paginate($per_page);
		$pagination = [];
		$pagination['last_page'] = $active_plans->getLastPage();
		$pagination['current_page'] = $active_plans->getCurrentPage();
		$pagination['total_data'] = $active_plans->getTotal();
		$pagination['from'] = $active_plans->getFrom();
		$pagination['to'] = $active_plans->getTo();
		$pagination['count'] = $active_plans->count();

		foreach($active_plans as $key => $active) {
			$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $active->customer_active_plan_id)->first();
		
			$employees = DB::table('enrollment_status_history')->where('enrollment_status_id', $active->id)->where('type', 'employee')->count();
			$dependents = DB::table('enrollment_status_history')->where('enrollment_status_id', $active->id)->where('type', 'dependent')->count();
			$enable_status = DB::table('enrollment_status_history')->where('enrollment_status_id', $active->id)->where('type', 'employee')->where('send_activation', 0)->count();

			$pagination['data'][] = [
				'id'					  => $active->id,
				'customer_active_plan_id' => $active->customer_active_plan_id,
				'plan_type'               => PlanHelper::getAccountType($active_plan->account_type),
				'plan_start'              => date('Y-m-d', strtotime($active->plan_start)),
				'total_enrolled_employees'  => $employees,
				'total_enrolled_dependents'  => $dependents,
				'schedule_date'           => $active->schedule_date ? date('Y-m-d', strtotime($active->schedule_date)) : null,
        		'enabled'                 => $enable_status > 0 ? true : false,
				'date_of_edit'            => date('Y-m-d', strtotime($active->date_of_enrollment)),
			];
		}
		
		return ['status' => true, 'data' => $pagination];
	}

	public function getInvoiceHistories( )
	{
		$input = Input::all();

		$result = self::checkSession();
		$customer_id = $result->customer_buy_start_id;
		if(!$customer_id) {
			return ['status' => false, 'message' => 'Invalid access token'];
		}
		$per_page = !empty($input['per_page']) ? $input['per_page'] : 1;
		// get active plan lists
		$active_plans = DB::table('customer_active_plan')->where('customer_start_buy_id', $customer_id)->orderBy('created_at', 'desc')->paginate($per_page);
		$pagination = [];
		$pagination['last_page'] = $active_plans->getLastPage();
		$pagination['current_page'] = $active_plans->getCurrentPage();
		$pagination['total_data'] = $active_plans->getTotal();
		$pagination['from'] = $active_plans->getFrom();
		$pagination['to'] = $active_plans->getTo();
		$pagination['count'] = $active_plans->count();

		foreach($active_plans as $key => $active) {
			$total = 0;
      		$amount_due = 0;
			$invoice = DB::table('corporate_invoice')->where('customer_active_plan_id', $active->customer_active_plan_id)->first();
			$end_plan_date = null;
			$calculated_prices = 0;
			$plan_amount = 0;
			$duration = null;
			$new_head_count = false;

			$plan = DB::table('customer_plan')->where('customer_plan_id', $active->plan_id)->orderBy('created_at', 'desc')->first();
			if($active->new_head_count == 0) {
				if($active->duration || $active->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$active->duration, strtotime($plan->plan_start)));
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
				}
				if($invoice && (int)$invoice->override_total_amount_status == 1) {
					$calculated_prices = $invoice->override_total_amount;
				} else {
					$calculated_prices = $invoice ? $invoice->individual_price : 0;
				}
				$plan_amount = $invoice ? $calculated_prices * $invoice->employees : 0;
				$new_head_count = false;
			} else {
				// $calculated_prices_end_date = CustomerHelper::getCompanyPlanDates($active->customer_start_buy_id);
				$calculated_prices_end_date = $plan->plan_end;
				$duration = CustomerHelper::getPlanDuration($active->customer_start_buy_id, $active->plan_start);
				if($invoice && (int)$invoice->override_total_amount_status == 1) {
					$calculated_prices = $invoice->override_total_amount;
				} else {
					$calculated_prices = CustomerHelper::calculateInvoicePlanPrice($invoice ? $invoice->individual_price : 0, $active->plan_start, $calculated_prices_end_date);
					$calculated_prices = $calculated_prices;
				}
				$plan_amount = $invoice ? $calculated_prices * $invoice->employees : 0;
				$new_head_count = true;
			}

			$total += $plan_amount;
			if($invoice) {
				// get dependent if any
				$dependents = DB::table('dependent_plans')
				->where('customer_active_plan_id', $invoice->customer_active_plan_id)
				->get();
				$payment_data = DB::table('customer_cheque_logs')->where('invoice_id', $invoice->corporate_invoice_id)->first();
			} else {
				$dependents = [];
				$payment_data = null;
			}
			
	  
			foreach ($dependents as $key => $dependent) {
			  $invoice_dependent = DB::table('dependent_invoice')
			  ->where('dependent_plan_id', $dependent->dependent_plan_id)
			  ->first();
	  
			  if((int)$dependent->new_head_count == 1) {
				$calculated_prices_end_date = $plan->plan_end;
				$calculated_prices_end_date = date('Y-m-d', strtotime('+1 day', strtotime($calculated_prices_end_date['plan_end'])));
				$calculated_prices = CustomerHelper::calculateInvoicePlanPrice($invoice_dependent->individual_price, $dependent->plan_start, $calculated_prices_end_date);
				$total += $calculated_prices * $dependent->total_dependents;
			  } else {
				$total += $dependent->individual_price * $dependent->total_dependents;
			  }
			}
			
			$pagination['data'][] = [
				'invoice_id'      => $invoice ? $invoice->corporate_invoice_id : null,
				'invoice_date'    => $invoice ? date('Y-m-d', strtotime($invoice->invoice_date)) : null,
				'invoice_due'    => $invoice ? date('Y-m-d', strtotime($invoice->invoice_due)) : null,
				'invoice_number'  => $invoice ? $invoice->invoice_number : null,
				'total'           => DecimalHelper::formatDecimal($total),
        		'amount_due'      => $payment_data ? DecimalHelper::formatDecimal($total - $payment_data->paid_amount) : $total,
				'payment_amount'  => $payment_data ? $payment_data->paid_amount : 0,
				'payment_date'    => $payment_data && $active->paid == "true" ? date('Y-m-d', strtotime($payment_data->date_received)) : null,
				'payment_remarks' => $payment_data ? $payment_data->remarks : null,
				'currency_type'   => $invoice ? $invoice->currency_type : null,
				'payment_status'  => $active->paid == "true" ? true : false
			];
		}
		
		return ['status' => true, 'data' => $pagination];
	}

	public function updateEnrollmentSchedule( )
	{
		$input = Input::all();
		if(empty($input['id']) || $input['id'] == null) {
			return ['status' => false, 'message' => 'id is required'];
		}
		
		if(empty($input['schedule_date']) || $input['schedule_date'] == null) {
			return ['status' => false, 'message' => 'schedule_date is required'];
		}

		$result = self::checkSession();
		$customer_id = $result->customer_buy_start_id;
		if(!$customer_id) {
			return ['status' => false, 'message' => 'Invalid access token'];
		}

		$enrollment_status = DB::table('enrollment_status')->where('id', $input['id'])->first();

		if(!$enrollment_status) {
		return ['status' => false, 'message' => 'data not found'];
		}

		$result = DB::table('enrollment_status')->where('id', $input['id'])->update(['schedule_date' => date('Y-m-d', strtotime($input['schedule_date']))]);

		if($result) {
		return ['status' => true, 'message' => 'Updated schedule date'];
		}
		
		return ['status' => false, 'message' => 'Failed to update schedule date'];
	}

	public function getHrDetails( )
	{
		$input = Input::all();
		$session = self::checkSession();
		$hr_id = $session->hr_dashboard_id;
		
		if(!$hr_id) {
			return ['status' => false, 'message' => 'Invalid access token'];
		}
		// get hr details
		$hr = DB::table('customer_hr_dashboard')->where('hr_dashboard_id', $hr_id)->first();

		if(!$hr->fullname) {
			// update info
			$customer = DB::table('customer_business_contact')->where('customer_buy_start_id', $hr->customer_buy_start_id)
			->first();

			if($customer) {
				$hr_acount_details = [
					'fullname'			=> $customer->first_name.' '.$customer->last_name,
					// 'email'				=> $customer->work_email,
					'phone_number'		=> $customer->phone,
					'phone_code'		=> "+65"
				];

				DB::table('customer_hr_dashboard')->where('hr_dashboard_id', $hr->hr_dashboard_id)->update($hr_acount_details);
				$hr = DB::table('customer_hr_dashboard')->where('hr_dashboard_id', $hr_id)->first();
			}
		}

		$phone_code = str_replace('+', '', $hr->phone_code);
		$phone_number = str_replace('+', '', $hr->phone_number);
		$hr_acount_details = [
			'full_name'			=>$hr->fullname,
			'email'				=>$hr->email,
			'phone'				=>(int)$phone_number,
			'phone_code'		=>"+".$phone_code,
			'id'				=>$hr->hr_dashboard_id
		];

		return ['status' => true, 'hr_account_details' => $hr_acount_details];

	}

	public function updateHrAccountDetails ( )
    {
        $input = Input::all();

        $session = self::checkSession();
        $admin_id = Session::get('admin-session-id');
        $hr_id = $session->hr_dashboard_id;
	

		$phone_code = str_replace('+', '', $input['phone_code']);
		$phone_number = str_replace('+', '', $input['phone_number']);

        $data = array(
            'fullname'                  => $input['fullname'],
            'email'                     => $input['email'],
			'phone_number'              => $phone_number,
			'phone_code'				=> $phone_code,
            'updated_at'                => date('Y-m-d H:i:s')
        );

        $result = DB::table('customer_hr_dashboard')
        ->where('hr_dashboard_id', $hr_id)
        ->update($data);

        if($admin_id) {
            $input['hr_dashboard_id'] = $session->hr_dashboard_id;
            $admin_logs = array(
                'admin_id'  => $admin_id,
                'admin_type' => 'mednefits',
                'type'      => 'admin_hr_updated_account_details',
                'data'      => SystemLogLibrary::serializeData($input)
            );
            SystemLogLibrary::createAdminLog($admin_logs);
        } else {
            $admin_logs = array(
                'admin_id'  => $hr_id,
                'admin_type' => 'hr',
                'type'      => 'admin_hr_updated_account_details',
                'data'      => SystemLogLibrary::serializeData($input)
            );
            SystemLogLibrary::createAdminLog($admin_logs);
        }

        return array('status' => TRUE, 'message' => 'Successfully Update HR Account Details.');
    }

	public function getOldPlansLists()
	{
		$result = self::checkSession();
		$customer_id = $result->customer_buy_start_id;
		if(!$customer_id) {
			return ['status' => false, 'message' => 'Invalid access token'];
		}
	
		$plans = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->where('active', 0)->orderBy('plan_start', 'desc')->get();
		return ['status' => true, 'data' => $plans];
	}
	
	public function createCompanyPassword ( )
	{
		$input = Input::all();

		if(empty($input['hr_dashboard_id']) || $input['hr_dashboard_id'] == null) {
			return array('status' => false, 'message' => 'HR is required.');
		}
		$check = DB::table('customer_hr_dashboard')->where('reset_link', $input['token'])->first();

		if($check) {
			$new_password = array(
				'password'	=> md5($input['new_password']),
				'active'	=> 1,
				'hr_activated'	=> 1,
			);
			$hr = new HRDashboard();
			$result = $hr->updateCorporateHrDashboard($input['hr_dashboard_id'], $new_password);

			if($result)	{
				$jwt = new JWT();
				$secret = Config::get('config.secret_key');
				$check->signed_in = TRUE;
				$token = $jwt->encode($check, $secret);
				return array ('status' => TRUE, 'message' => 'Successfully created password.', 'token' => $token);
			}
			
		// return array('status' => true, 'data' => ['hr_dashboard_id' => $check->hr_dashboard_id, 'valid_token' => true, 'activated' => false]);
		
		}

		return array ('status' => FALSE, 'message' => 'Failed created password.');
	}
	public function resendHrActivationLnk( )
	{
		$input = Input::all();

		if(empty($input['token']) || $input['token'] == null)	{
			return ['status' => false, 'message' => 'token is required'];
		}

		// check token existence
		$check_token = DB::table('customer_hr_dashboard')->where('reset_link', $input['token'])->first();

		if(!$check_token) {
			return ['status' => false, 'message' => 'token does not exist'];
		}

		$reset_link = StringHelper::getEncryptValue();
		$result = DB::table('customer_hr_dashboard')
					->where('hr_dashboard_id', $check_token->hr_dashboard_id)
					->update(['reset_link' => $reset_link, 'updated_at' => date('Y-m-d H:i:s'), 'expiration_time' => date('Y-m-d H:i:s', strtotime('+7 days'))]);

		if($result)	{
			// resend email activation
			// send hr email activation
			$email_data = array();
			$email_data['emailSubject'] = 'WELCOME TO MEDNEFITS CARE';
			$email_data['emailName'] = ucwords($check_token->fullname);
			$email_data['emailPage'] = 'email-templates.latest-templates.activation-email';
			$email_data['emailTo'] = $check_token->email;
			$email_data['button'] = url('/company-activation#/activation-link')."?activation_token=".$reset_link;
			EmailHelper::sendEmail($email_data);
		}

		return ['status' => true, 'message' => 'Activation Email Send. Please check it on your inbox'];
	}

	public function sendSpendingActivateInquiry( )
	{
		$input = Input::all();

		if(empty($input['content']) || $input['content'] == null)	{
			return ['status' => false, 'message' => 'Enquiry message is required'];
		}
		
		$customer = StringHelper::getJwtHrSession();
		$customer_id = $customer->customer_buy_start_id;
		$receiver = Config::get('config.spending_inquiry_email');
		$email_data = array();
		$email_data['emailSubject'] = 'Activate Spending Account';
		$email_data['emailName'] = ucwords($customer->fullname);
		$email_data['emailPage'] = 'email-templates.spending-activation-inquiry';
		$email_data['emailTo'] = $receiver;
		$email_data['content'] = $input['content'];
		EmailHelper::sendEmail($email_data);
		return ['status' => true, 'message' => 'Activate Spending Account Inquiry has been sent'];
	}

	public function enrolledUsersFromActivePlan( )
	{

		$input = Input::all();
		if(empty($input['customer_active_plan_id']) || $input['customer_active_plan_id'] == null)	{
			return ['status' => false, 'message' => 'customer_active_plan_id is required'];
		}

		$check = DB::table('customer_active_plan')->where('customer_active_plan_id', $input['customer_active_plan_id'])->first();

		if(!$check) {
			return ['status' => FALSE, 'message' => 'Customer Active Plan does not exist.'];
		}

		$pagination = [];
		$limit = !empty($input['per_page']) ? $input['per_page'] : 10;
		$corporate_plan = CorporatePlan::where('customer_buy_start_id', $check->customer_start_buy_id)->orderBy('created_at', 'desc')->first();
		$end_date_policy = $corporate_plan->plan_end;

		if(isset($input['search']) && $input['search'] != null)	{
			$users = DB::table('customer_active_plan')
					->join('user_plan_history', 'user_plan_history.customer_active_plan_id', '=', 'customer_active_plan.customer_active_plan_id')
					->join('user', 'user.UserID', '=', 'user_plan_history.user_id')
					->where('user_plan_history.customer_active_plan_id', $input['customer_active_plan_id'])
					->where('user_plan_history.type', 'started')
					->where('user.Name', 'like', '%'.$input['search'].'%')
					->where('user.Active', 1)
					->select('user.UserID', 'user.Name')
					->paginate($limit);
		} else {
			$users = DB::table('customer_active_plan')
					->join('user_plan_history', 'user_plan_history.customer_active_plan_id', '=', 'customer_active_plan.customer_active_plan_id')
					->join('user', 'user.UserID', '=', 'user_plan_history.user_id')
					->where('user_plan_history.customer_active_plan_id', $input['customer_active_plan_id'])
					->where('user_plan_history.type', 'started')
					->where('user.Active', 1)
					->select('user.UserID', 'user.Name')
					->paginate($limit);
		}
		
		$pagination['last_page'] = $users->getLastPage();
		$pagination['current_page'] = $users->getCurrentPage();
		$pagination['total_data'] = $users->getTotal();
		$pagination['from'] = $users->getFrom();
		$pagination['to'] = $users->getTo();
		$pagination['count'] = $users->count();


		$corporate_members = [];
		foreach ($users as $key => $user) {
			$user->Name = ucwords($user->Name);
			$dependents = DB::table('employee_family_coverage_sub_accounts')->where('owner_id', $user->UserID)->where('deleted', 0)->select('user_id')->get();

			foreach($dependents as $key => $dependent)	{
				$member = DB::table('user')->where('UserID', $dependent->user_id)->first();
				$dependent->Name = ucwords($member->Name);
			}
			
			$user_data = array(
				'member'			=> $user,
				'dependents'        => $dependents
			);
			array_push($corporate_members, $user_data);
		}
		$pagination['plan_start'] = $check->plan_start;
		$pagination['data'] = $corporate_members;

		return ['status' => TRUE, 'data' => $pagination];
	}
	public function updateActivePlanDetails( )
	{
		$input = Input::all();
		$admin_id = Session::get('admin-session-id');
		$result = self::checkSession();
		$hr_id = $result->hr_dashboard_id;
		
		if(empty($input['customer_active_plan_id']) || $input['customer_active_plan_id'] == null) {
			return ['status' => false, 'message' => 'customer_active_plan_id is required'];
		}

		if(empty($input['start_date']) || $input['start_date'] == null) {
			return ['status' => false, 'message' => 'start_date is required'];
		}

		if(empty($input['plan_duration']) || $input['plan_duration'] == null) {
			return ['status' => false, 'message' => 'plan_duration is required'];
		}

		$customer_active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $input['customer_active_plan_id'])->first();

		if(!$customer_active_plan)  {
			return ['status' => false, 'message' => 'Active Plan does not exist'];
		}

		if($customer_active_plan->account_type == "enterprise_plan")  {
			if(empty($input['invoice_start']) || $input['invoice_start'] == null) {
				return ['status' => false, 'message' => 'invoice_start is required'];
			}

			if(empty($input['invoice_due']) || $input['invoice_due'] == null) {
				return ['status' => false, 'message' => 'invoice_due is required'];
			}

			if(empty($input['individual_price']) || $input['individual_price'] == null) {
				return ['status' => false, 'message' => 'individual_price is required'];
			}

			// update detals
			$validate_date = \PlanHelper::validateStartDate($input['invoice_start']);

			if(!$validate_date) {
				return ['status' => false, 'message' => 'Invoice Date must be a date'];
			}

			// update detals
			$validate_date = \PlanHelper::validateStartDate($input['invoice_due']);

			if(!$validate_date) {
				return ['status' => false, 'message' => 'Invoice Due Date must be a date'];
			}
		}

		// update detals
		$validate_plan_start = \PlanHelper::validateStartDate($input['start_date']);

		if(!$validate_plan_start) {
			return ['status' => false, 'message' => 'Start Date must be a date'];
		}

		$data = array();
		$plan_start = date('Y-m-d', strtotime($input['start_date']));
		$plan_end = date('Y-m-d', strtotime('+'.$input['plan_duration'], strtotime($plan_start)));
		$plan_end = date('Y-m-d', strtotime('-1 day', strtotime($plan_end)));
		
		// update data
		$customer_active_plan_update = array(
			'plan_start'      => $plan_start,
			'duration'        => $input['plan_duration'],
			'end_date_policy' => $plan_end,
			'updated_at'      => date('Y-m-d H:i:s')
		);

		$result = DB::table('customer_active_plan')->where('customer_active_plan_id', $input['customer_active_plan_id'])->update($customer_active_plan_update);

		if($result) {
			array_merge($data, $customer_active_plan_update);
			if((int)$customer_active_plan->new_head_count == 0) {
				DB::table('customer_plan')->where('customer_plan_id', $customer_active_plan->plan_id)->update(['plan_start' => $plan_start, 'plan_end' => $plan_end]);
				$data['customer_plan_id'] = $customer_active_plan->plan_id;
				if($admin_id) {
				$admin_logs = array(
					'admin_id'  => $admin_id,
					'type'      => 'updated_company_primary_plan',
					'data'      => \SystemLogLibrary::serializeData($data)
				);
				\SystemLogLibrary::createAdminLog($admin_logs);
				}
			}

			if($customer_active_plan->account_type == "enterprise_plan")  {
				// update invoice
				$invoice = array(
				'invoice_date'      => date('Y-m-d', strtotime($input['invoice_start'])),
				'invoice_due'       => date('Y-m-d', strtotime($input['invoice_due'])),
				'individual_price'  => $input['individual_price'],
				'updated_at'      => date('Y-m-d H:i:s')
				);

				DB::table('corporate_invoice')->where('customer_active_plan_id', $input['customer_active_plan_id'])->update($invoice);
				array_merge($data, $invoice);
			}

			
			if($admin_id) {
				$data['customer_active_plan'] = $input['customer_active_plan_id'];
				$admin_logs = array(
					'admin_id'  => $admin_id,
					'admin_type' => 'mednefits',
					'type'      => 'updated_company_active_plan',
					'data'      => \SystemLogLibrary::serializeData($data)
				);
				\SystemLogLibrary::createAdminLog($admin_logs);
			} else {
				$data['customer_active_plan'] = $input['customer_active_plan_id'];
				$admin_logs = array(
					'admin_id'  => $hr_id,
					'admin_type' => 'hr',
					'type'      => 'updated_company_active_plan',
					'data'      => \SystemLogLibrary::serializeData($data)
				);
				\SystemLogLibrary::createAdminLog($admin_logs);
			}
			return ['status' => true, 'message' => 'Plan details updated'];
		}

		return ['status' => false, 'message' => 'Failed to update plan details'];
	}

	public function updateActiveDependentDetails( )
	{
		$input = Input::all();
		$admin_id = Session::get('admin-session-id');
		$result = self::checkSession();
		$hr_id = $result->hr_dashboard_id;

		if(empty($input['dependent_plan_id']) || $input['dependent_plan_id'] == null) {
			return ['status' => false, 'message' => 'dependent_plan_id is required'];
		}

		if(empty($input['start_date']) || $input['start_date'] == null) {
			return ['status' => false, 'message' => 'start_date is required'];
		}

		if(empty($input['plan_duration']) || $input['plan_duration'] == null) {
			return ['status' => false, 'message' => 'plan_duration is required'];
		}


		$customer_active_plan = DB::table('dependent_plans')->where('dependent_plan_id', $input['dependent_plan_id'])->first();

		if(!$customer_active_plan)  {
			return ['status' => false, 'message' => 'Dependent Plan does not exist'];
		}

		if($customer_active_plan->account_type == "enterprise_plan")  {
			if(empty($input['invoice_start']) || $input['invoice_start'] == null) {
				return ['status' => false, 'message' => 'invoice_start is required'];
			}

			if(empty($input['invoice_due']) || $input['invoice_due'] == null) {
				return ['status' => false, 'message' => 'invoice_due is required'];
			}

			if(empty($input['individual_price']) || $input['individual_price'] == null) {
				return ['status' => false, 'message' => 'individual_price is required'];
			}

			// update detals
			$validate_date = \PlanHelper::validateStartDate($input['invoice_start']);

			if(!$validate_date) {
				return ['status' => false, 'message' => 'Invoice Date must be a date'];
			}

			// update detals
			$validate_date = \PlanHelper::validateStartDate($input['invoice_due']);

			if(!$validate_date) {
				return ['status' => false, 'message' => 'Invoice Due Date must be a date'];
			}
		}

		// update detals
		$validate_plan_start = \PlanHelper::validateStartDate($input['start_date']);

		if(!$validate_plan_start) {
			return ['status' => false, 'message' => 'Start Date must be a date'];
		}

		$data = array();
		$plan_start = date('Y-m-d', strtotime($input['start_date']));
		$plan_end = date('Y-m-d', strtotime('+'.$input['plan_duration'], strtotime($plan_start)));
		$plan_end = date('Y-m-d', strtotime('-1 day', strtotime($plan_end)));
		
		// update data
		$customer_active_plan_update = array(
			'plan_start'      => $plan_start,
			'duration'        => $input['plan_duration'],
			'updated_at'      => date('Y-m-d H:i:s')
		);

		$result = DB::table('dependent_plans')->where('dependent_plan_id', $input['dependent_plan_id'])->update($customer_active_plan_update);

		if($result) {
			array_merge($data, $customer_active_plan_update);
			if($customer_active_plan->account_type == "enterprise_plan")  {
				// update invoice
				$invoice = array(
				'invoice_date'      => date('Y-m-d', strtotime($input['invoice_start'])),
				'invoice_due'       => date('Y-m-d', strtotime($input['invoice_due'])),
				'individual_price'  => $input['individual_price'],
				'updated_at'      => date('Y-m-d H:i:s')
				);

				DB::table('dependent_invoice')->where('dependent_plan_id', $input['dependent_plan_id'])->update($invoice);
				array_merge($data, $invoice);
			}

			if($admin_id) {
				$data['dependent_plan_id'] = $input['dependent_plan_id'];
				$admin_logs = array(
				'admin_id'  => $admin_id,
				'admin_type' => 'mednefits',
				'type'      => 'admin_updated_dependent_details',
				'data'      => \SystemLogLibrary::serializeData($data)
				);
				\SystemLogLibrary::createAdminLog($admin_logs);
			} else {
				$data['dependent_plan_id'] = $input['dependent_plan_id'];
				$admin_logs = array(
				'admin_id'  => $hr_id,
				'admin_type' => 'hr',
				'type'      => 'admin_updated_dependent_details',
				'data'      => \SystemLogLibrary::serializeData($data)
				);
				\SystemLogLibrary::createAdminLog($admin_logs);
			}
			return ['status' => true, 'message' => 'Plan details updated'];
		}

		return ['status' => false, 'message' => 'Failed to update plan details'];
	}

	public function downloadPlanInvoice( )
	{
		$input = Input::all();

		if(empty($input['token'])) {
			return View::make('errors.503');
		}
		
		if(empty($input['customer_active_plan_id']) || $input['customer_active_plan_id'] == null)   {
			return ['status' => false, 'message' => 'customer_active_plan_id is required'];
		}
		
		$invoices = DB::table('corporate_invoice')->where('customer_active_plan_id', $input['customer_active_plan_id'])->get();

		if(sizeof($invoices) == 0)   {
			return ['status' => false, 'message' => 'no invoices found'];
		}

		// directory
		$path = public_path().'/plan_invoices/' . $input['customer_active_plan_id'];
		File::makeDirectory($path, $mode = 0777, true, true);
		$zip_file = public_path().'/invoices.zip';
		foreach($invoices as $key => $invoice)  {
			$data = [];

			$statement = "
							SELECT 
							*
						FROM
							(SELECT 
								invoicesTbl.customer_active_plan_id AS planId,
									customerBusinessContact.company_name,
									(CASE
										WHEN customerBusinessContactTbl.billing_status = TRUE THEN CONCAT(customerBusinessContactTbl.first_name, ' ', customerBusinessContactTbl.last_name)
										ELSE customerBillingContact.billing_name
									END) AS contactName,
									(CASE
										WHEN customerBusinessContactTbl.billing_status = TRUE THEN customerBusinessContact.company_address
										ELSE customerBillingContact.billing_address
									END) AS contactAddress,
									customerBusinessContactTbl.phone AS contactPhone,
									customerBusinessContactTbl.work_email AS contactEmail,
									invoicesTbl.invoice_number,
									invoicesTbl.invoice_date,
									invoicesTbl.invoice_due,
									customerChequeLogs.date_received,
									ROUND(IF(((invoicesTbl.employees * invoicesTbl.individual_price) - IFNULL(customerChequeLogs.paid_amount, 0)) < 0, 0, ((invoicesTbl.employees * invoicesTbl.individual_price) - IFNULL(customerChequeLogs.paid_amount, 0))), 2) AS amtDue,
									(CASE
									WHEN
										customerActivePlan.new_head_count = 1
									THEN
										(CASE
											WHEN 
										customerActivePlan.secondary_account_type is null AND customerActivePlan.account_type = 'stand_alone_plan'
										|| customerActivePlan.secondary_account_type is not null AND customerActivePlan.secondary_account_type = 'pro_plan_bundle' 
										AND customerActivePlan.account_type = 'insurance_bundle'
										THEN 'Seat Addition - Pro Plan'
									WHEN 
										customerActivePlan.secondary_account_type is null AND customerActivePlan.account_type = 'lite_plan'
												|| customerActivePlan.secondary_account_type is not null AND customerActivePlan.secondary_account_type = 'insurance_bundle_lite' 
										AND customerActivePlan.account_type = 'insurance_bundle'
										THEN 'Seat Addition - Basic Plan'
									WHEN 
										customerActivePlan.secondary_account_type is null AND customerActivePlan.account_type = 'enterprise_plan'
										THEN 'Seat Addition - Enterprise Plan'
										END)
									ELSE (CASE
										WHEN 
									customerActivePlan.secondary_account_type is null AND customerActivePlan.account_type = 'stand_alone_plan'
									|| customerActivePlan.secondary_account_type is not null AND customerActivePlan.secondary_account_type = 'pro_plan_bundle' 
										AND customerActivePlan.account_type = 'insurance_bundle'
									THEN 'Plan Creation - Pro Plan'
									WHEN 
									customerActivePlan.secondary_account_type is null AND customerActivePlan.account_type = 'lite_plan'
									|| customerActivePlan.secondary_account_type is not null AND customerActivePlan.secondary_account_type = 'insurance_bundle_lite' 
										AND customerActivePlan.account_type = 'insurance_bundle'
									THEN 'Plan Creation - Basic Plan'
									WHEN 
									customerActivePlan.secondary_account_type is null AND customerActivePlan.account_type = 'enterprise_plan'
									THEN 'Plan Creation - Enterprise Plan'
									END)
								END) AS planType,
								(CASE
									WHEN 
									customerActivePlan.secondary_account_type is null AND customerActivePlan.account_type = 'stand_alone_plan'
									|| customerActivePlan.secondary_account_type is not null AND customerActivePlan.secondary_account_type = 'pro_plan_bundle' 
									AND customerActivePlan.account_type = 'insurance_bundle'
									THEN 'Pro Plan'
								WHEN 
									customerActivePlan.secondary_account_type is null AND customerActivePlan.account_type = 'lite_plan'
									|| customerActivePlan.secondary_account_type is not null AND customerActivePlan.secondary_account_type = 'insurance_bundle_lite' 
									AND customerActivePlan.account_type = 'insurance_bundle'
									THEN 'Basic Plan'
								WHEN 
									customerActivePlan.secondary_account_type is null AND customerActivePlan.account_type = 'enterprise_plan'
									THEN 'Enterprise Plan'
								ELSE  customerActivePlan.account_type
								END) AS activeType,
								(CASE
									WHEN customerActivePlan.secondary_account_type is null AND customerActivePlan.account_type = 'lite_plan'
									|| customerActivePlan.secondary_account_type is not null AND customerActivePlan.secondary_account_type = 'insurance_bundle_lite' 
									AND customerActivePlan.account_type = 'insurance_bundle' 
									THEN 1
									ELSE 0
								END) AS complementary,
									invoicesTbl.customer_active_plan_id AS ActivePlanId,
									invoicesTbl.employees AS NoOfEmployee,
									'Annual' AS billingFrequency,
									@plan_start:=(CASE
										WHEN invoicesTbl.plan_extention_enable THEN planExtension.plan_start
										ELSE customerActivePlan.plan_start
									END) AS startDate,
									@plan_end:=(CASE
										WHEN
											customerActivePlan.new_head_count = 0
										THEN
											(CASE
												WHEN
													invoicesTbl.plan_extention_enable
												THEN
													DATE_SUB((CASE
														WHEN INSTR(planExtension.duration, 'year') > 0 THEN DATE_ADD(planExtension.plan_start, INTERVAL planExtension.duration YEAR)
														ELSE DATE_ADD(planExtension.plan_start, INTERVAL planExtension.duration MONTH)
													END), INTERVAL 1 DAY)
												ELSE DATE_SUB((CASE
													WHEN INSTR(customerActivePlan.duration, 'year') > 0 THEN DATE_ADD(customerActivePlan.plan_start, INTERVAL customerActivePlan.duration YEAR)
													ELSE DATE_ADD(customerActivePlan.plan_start, INTERVAL customerActivePlan.duration MONTH)
												END), INTERVAL 1 DAY)
											END)
										ELSE customerPlan.plan_end
									END) AS endDate,
									customerActivePlan.duration AS planDuration,
									invoicesTbl.employees AS quantity,
									(CASE 
									WHEN 
										customerActivePlan.new_head_count = 0 
									THEN
									truncate((invoicesTbl.employees * invoicesTbl.individual_price),2)
									ELSE 
										truncate((invoicesTbl.employees * truncate((invoicesTbl.individual_price / DAYOFYEAR(CONCAT(YEAR(NOW()), '-12-31'))) * (DATEDIFF(@plan_end, @plan_start)+1),
												2)),2)
									END) AS amt,
									(CASE
										WHEN customerActivePlan.new_head_count = 0 THEN invoicesTbl.individual_price
										ELSE truncate((invoicesTbl.individual_price / DAYOFYEAR(CONCAT(YEAR(NOW()), '-12-31'))) * (DATEDIFF(@plan_end, @plan_start)+1), 2)
									END) AS price,
									invoicesTbl.currency_type,
									customerActivePlan.paid
							FROM
								medi_corporate_invoice AS invoicesTbl
							LEFT JOIN medi_customer_business_contact AS customerBusinessContactTbl ON customerBusinessContactTbl.customer_buy_start_id = invoicesTbl.customer_id
							LEFT JOIN medi_customer_billing_contact AS customerBillingContact ON customerBillingContact.customer_buy_start_id = invoicesTbl.customer_id
							LEFT JOIN medi_customer_business_information AS customerBusinessContact ON customerBusinessContact.customer_buy_start_id = invoicesTbl.customer_id
							LEFT JOIN (SELECT 
								*
							FROM
								medi_customer_cheque_logs
							WHERE
								invoice_id IS NOT NULL
							GROUP BY customer_active_plan_id , invoice_id) AS customerChequeLogs ON customerChequeLogs.customer_active_plan_id = invoicesTbl.customer_active_plan_id
								AND customerChequeLogs.invoice_id = invoicesTbl.corporate_invoice_id
							LEFT JOIN medi_customer_active_plan AS customerActivePlan ON customerActivePlan.customer_active_plan_id = invoicesTbl.customer_active_plan_id
								AND customerActivePlan.customer_start_buy_id = invoicesTbl.customer_id
							LEFT JOIN medi_plan_extensions AS planExtension ON planExtension.customer_active_plan_id = invoicesTbl.customer_active_plan_id
							LEFT JOIN medi_customer_plan AS customerPlan ON customerPlan.customer_buy_start_id = customerActivePlan.customer_start_buy_id
								AND customerPlan.customer_plan_id = customerActivePlan.plan_id
							JOIN (SELECT @planStart:=0, @planEnd:=0) AS dumTbl ON 1 = 1
							WHERE
								invoicesTbl.corporate_invoice_id = ".$invoice->corporate_invoice_id." AND customerActivePlan.account_type != 'trial_plan' UNION ALL SELECT 
								'dependent_data',
									'dependent_data',
									'dependent_data',
									'dependent_data',
									'dependent_data',
									'dependent_data',
									'dependent_data',
									'dependent_data',
									'dependent_data',
									'dependent_data',
									'dependent_data',
									(CASE
										WHEN dependentPlan.account_type = 'stand_alone_plan' 
										THEN 'Plan Creation - Pro Plan'
										WHEN dependentPlan.account_type = 'lite_plan' 
										THEN 'Plan Creation - Basic Plan'
										WHEN dependentPlan.account_type = 'enterprise_plan' 
										THEN 'Plan Creation - Enterprise Plan'
									END),
									(CASE
									WHEN dependentPlan.account_type = 'stand_alone_plan' 
										THEN 'Pro Plan'
									WHEN dependentPlan.account_type = 'lite_plan' 
										THEN 'Basic Plan'
									WHEN dependentPlan.account_type = 'enterprise_plan' 
										THEN 'Enterprise Plan'
									END),
									'dependent_data',
									'dependent_data',
									dependentInvoice.total_dependents,
									'dependent_data',
									dependentPlan.plan_start,
									DATE_SUB((CASE
										WHEN INSTR(dependentPlan.duration, 'year') > 0 THEN DATE_ADD(dependentPlan.plan_start, INTERVAL dependentPlan.duration YEAR)
										ELSE DATE_ADD(dependentPlan.plan_start, INTERVAL dependentPlan.duration MONTH)
									END), INTERVAL 1 DAY) AS plan_end,
									dependentPlan.duration,
									dependentInvoice.total_dependents AS dependent_quantity,
									ROUND(dependentInvoice.total_dependents * dependentInvoice.individual_price, 2) AS dependent_amt,
									dependentInvoice.individual_price AS dependent_price,
									'dependent_data',
									'dependent_data'
							FROM
								medi_dependent_plans AS dependentPlan
							LEFT JOIN medi_dependent_invoice AS dependentInvoice ON dependentInvoice.dependent_plan_id = dependentPlan.dependent_plan_id
							WHERE
								type = 'active_plan' AND tagged = 1
									AND dependentPlan.customer_active_plan_id = ".$invoice->customer_active_plan_id."
								AND dependentPlan.account_type != 'trial_plan') AS mainTbl
						WHERE
							amt > 0;
			";
			$result = DB::select($statement);

			if(sizeof($result) == 0) {
				return ['status' => false, 'message' => 'no results found'];
			}
		
			$employee = $result[0];
			$dependent = null;
		
			if(sizeof($result) > 1) {
				$dependent = $result[1];
			}
			
			$data['email'] = $employee->contactEmail;
			$data['phone']     = $employee->contactPhone;
			$data['company'] = $employee->company_name;
			$data['postal'] = null;
			$data['currency_type'] = strtoupper($employee->currency_type);
			$data['name'] = $employee->contactName;
			$data['address'] = $employee->contactAddress;
			$data['account_type'] = PlanHelper::getAccountType($employee->activeType);
			$data['complimentary'] = (int)$employee->complementary == 1 ? true : false;
			$data['plan_type'] = $employee->planType;
			$data['invoice_number'] = $employee->invoice_number;
			$data['invoice_date']		= date('F d, Y', strtotime($employee->invoice_date));
			$data['invoice_due']		= date('F d, Y', strtotime($employee->invoice_due));
			$data['number_employess'] = $employee->NoOfEmployee;
			$data['plan_start']     = date('F d, Y', strtotime($employee->startDate));
			$data['plan_end'] = date('F d, Y', strtotime($employee->endDate));
			$data['duration'] = $employee->planDuration;
			$data['notes'] = null;
			$data['head_count'] = false;
			
			$data['paid'] = $employee->paid == "true" ? true : false;
			
			$data['customer_active_plan_id'] = $employee->planId;
		
			if($data['paid'] == true) {
				$data['payment_date'] = date('F d, Y', strtotime($employee->date_received));
			}
			
			// dependents
			$data['dependents'] = [];
			$amount_due = 0;
			$total = 0;
			$amount_due += (float)$employee->amtDue;
			$total += (float)$employee->amt;
			
			if($dependent) {
				$data['dependents'][] = array(
				'account_type'		=> $dependent->activeType,
				'total_dependents'	=> $dependent->NoOfEmployee,
				'price'  => $dependent->price,
				'amount'			=> number_format($dependent->amt, 2),
				'plan_start'		=> date('F d, Y', strtotime($dependent->startDate)),
				'plan_end'			=> date('F d, Y', strtotime($dependent->endDate)),
				'duration'			=> $dependent->planDuration,
				'currency_type' => strtoupper($employee->currency_type)
				);
				
				$amount_due += (float)$dependent->amt;
				$total += (float)$dependent->amt;
			}
		
			$data['amount_due'] = number_format($amount_due, 2);
			$data['total'] = number_format($total, 2);
			$data['price'] = number_format($employee->price, 2);
			$data['amount'] = number_format((float)$employee->amt, 2);
			// return View::make('pdf-download.globalTemplates.plan-invoice', $data);
			$pdf = \PDF::loadView('pdf-download.globalTemplates.plan-invoice', $data);
			$pdf->getDomPDF()->get_option('enable_html5_parser');
			$pdf->setPaper('A4', 'portrait');
			$pdf->save($path."/".$data['invoice_number'].'.pdf');
			// return $pdf->stream();
			// return [
			// 	'res' => $pdf->save($path."/".$data['invoice_number'].'.pdf'),
			// 	'path'	=> $path."/".$data['invoice_number'].'.pdf'
			// ];
			// return $path."/".$data['invoice_number'].'.pdf';
			// file_put_contents($path."/".$data['invoice_number'].'.pdf', $pdf->output());
		}
	
		// return view('pdf-download.admin-corporate-transactions-download-invoice', $data);
		// then zip and  download pdf
		$zip = new \ZipArchive();
		$zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
		$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));

		foreach ($files as $name => $file)
		{
			// We're skipping all subfolders
			if (!$file->isDir()) {
				$filePath     = $file->getRealPath();

				// extracting filename with substr/strlen
				$relativePath = $input['customer_active_plan_id'].'/' . substr($filePath, strlen($path) + 1);
				$zip->addFile($filePath, $relativePath);
			}
		}
		$zip->close();
		$response = Response::download($zip_file, NULL, array('content-type' => 'application/zip'));
		if(ob_get_length() > 0) {
			ob_clean();
		}
		return $response;
	}

	public function removeAllEnrolleeTemp()
	{
		$session = self::checkSession();
		$customer_id = $session->customer_buy_start_id;

		$temp_employees = DB::table('customer_temp_enrollment')
			->where('customer_buy_start_id', $customer_id)
			->where('enrolled_status', 'false')
			->delete();

		return array(
			'status'	=> TRUE,
			'message'	=> 'Success.'
		);

	}
}
