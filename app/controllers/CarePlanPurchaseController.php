<?php

use Illuminate\Support\Facades\Input;
class CarePlanPurchaseController extends \BaseController {

	public function getToken( )
	{
		$stripe = StripeHelper::config();
		return $stripe['publishable_key'];
	}

	public function resumeCarePlanPurchase( )
	{
		$check = Session::get('customer_buy_start_id');

		if($check) {
			return array(
				'status'	=> TRUE,
				'data'		=> array('customer_buy_start_id' => $check)
			);
		} else {
			return array(
				'status'	=> FALSE,
			);
		}
	}

	public function getCarePlanPurchase($id)
	{
		$corporate_start_buy = new CorporateBuyStart();
		$check = $corporate_start_buy->checkCorporateStart($id);

		if($check) {
			$corporate_business_info = new CorporateBusinessInformation();
			$corporate_plan = new CorporatePlan();
			$corporate_business_contact = new CorporateBusinessContact();
			$corporate_billing_contact = new CorporateBillingContact();
			$corporate_billing_address = new CorporateBillingAddress();
			$hr = new HRDashboard();

			if($check->status == 0) {
				return array(
					'status' => TRUE,
					'data'	 => array(
								'customer_buy_start' => $check,
								'customer_business_info'	=> $corporate_business_info->getCorporateBusinessInfo($id),
								'customer_plan'	=> $corporate_plan->getCorporatePlan($id),
								'customerbusiness_contact'	=> $corporate_business_contact->getCorporateBusinessContact($id),
								'billing_contact'	=> $corporate_billing_contact->getCorporateBillingContact($id),
								'billing_address'	=> $corporate_billing_address->getCorporateBillingAddress($id),
								'hr_dashboard'		=> $hr->getHRDashboard($id)
							)
				);
			}
		}

		return array(
			'status'	=> FALSE
		);
	}

	public function insertCarePlanBusiness( )
	{
		$input = Input::all();
		
		$date = Date('Y-m-d', strtotime($input['plan_start']));

		if($date < date('Y-m-d')) {
			return array(
				'status'	=> FALSE,
				'message'	=> "Plan Start must be greater by today's date"
			);
		}

		$corporate_start_buy = new CorporateBuyStart();
		if(isset($input['customer_start_id'])) {
			$check_start_buy = self::checkCorporateBuyExistence($input['customer_buy_start_id']);
			if($input['cover_type'] == "team/corporate")
			{
				$data = array(
					'cover_type'					=> 'team/corporate',
					'company_postal_code'	=> $input['company_postal_code'],
					'employees'						=> $input['employees'],
					'plan_start'					=> $date,
					'contact_name'				=> $input['contact_name'],
					'contact_email'				=> $input['contact_email'],
					'corporate'						=> "true",
					'individual'					=> "false"
				);
			} else if($input['cover_type'] == "individual") {
				$data = array(
					'cover_type'					=> 'individual',
					'company_postal_code'	=> $input['company_postal_code'],
					'employees'						=> 1,
					'plan_start'					=> $date,
					'contact_name'				=> $input['contact_name'],
					'contact_email'				=> $input['contact_email'],
					'corporate'						=> "false",
					'individual'					=> "true",
					'contact_dob'					=> date('Y-m-d', strtotime($input['contact_dob'])),
					'contact_gender'			=> $input['contact_gender']
				);
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'cover_type must be individual or team/corporate.'	
				);
			}

			$result = $corporate_start_buy->updateCorporateBuyStartData($data, $input['customer_start_id']);
		} else {
			if($input['cover_type'] == "team/corporate")
			{
				$data = array(
					'cover_type'					=> 'team/corporate',
					'company_postal_code'	=> $input['company_postal_code'],
					'employees'						=> $input['employees'],
					'plan_start'					=> $date,
					'contact_name'				=> $input['contact_name'],
					'contact_email'				=> $input['contact_email'],
					'corporate'						=> "true",
					'individual'					=> "false"
				);
			} else if($input['cover_type'] == "individual") {
			 	// return $interval->y;
			 	if($input['age'] < 18 && $input['age'] > 65) {
			 		return array(
						'status'	=> FALSE,
						'message'	=> 'Oops, you did not meet the eligible age requirement between 18 to 65 years.'	
					);
			 	}
				$data = array(
					'cover_type'					=> 'individual',
					'company_postal_code'	=> $input['company_postal_code'],
					'employees'						=> 1,
					'plan_start'					=> $date,
					'contact_name'				=> $input['contact_name'],
					'contact_email'				=> $input['contact_email'],
					'corporate'						=> "false",
					'individual'					=> "true",
					'contact_age'					=> $input['age'],
					'contact_gender'			=> $input['contact_gender']
				);
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'cover_type must be individual or team/corporate.'	
				);
			}
			$result = $corporate_start_buy->insertCarePlanBusiness($data);
		}

		if($result) {
			return array(
				'status'	=> TRUE,
				'message'	=> 'Success!'	
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Insertion failed.'	
		);
	}

	// individual
	public function insertCustomerPersonalDetails( ) 
	{
		$input = Input::all();
		$corporate_start_buy = new CorporateBuyStart();

		$check = self::checkCorporateBuyExistence($input['customer_buy_start_id']);
		// return $check;
		if($check) {

			if($check->cover_type == "individual" && $check->individual == "true") {
				$customer_personal_details = new CustomerPersonalDetails();
				$user = new User();

				$check_user = $user->checkIndividualUser($input['email']);
				if($check_user > 0) {
					return array(
						'status'	=> FALSE,
						'message'	=> 'Email Address already taken.'
					);
				}

				$check_personal_details = $customer_personal_details->getCustomerPersonalDetails($input['customer_buy_start_id']);
				$data = array(
					'first_name'		=> $input['first_name'],
					'last_name'			=> $input['last_name'],
					'nric'					=> $input['nric'],
					'dob'						=> date('Y-m-d', strtotime($input['contact_dob'])),
					'gender'				=> $input['contact_gender'],
					'job_title'			=> $input['job_title'],
					'mobile'				=> $input['mobile'] ? $input['mobile'] : NULL,
					'email'					=> $input['email'],
					'address'				=> $input['address'] ? $input['address'] : NULL,
					'postal_code'		=> $input['postal_code'],
					'customer_buy_start_id' => $input['customer_buy_start_id']
				);
				if($check_personal_details > 0) {
					$result = $customer_personal_details->updateCustomerPersonalDetails($input['customer_buy_start_id'], $data);
				} else {
					$result = $customer_personal_details->createCustomerPersonalDetails($data);
				}

				if($result) {
					return array(
						'status'	=> TRUE,
						'message'	=> 'Success!'	
					);
				}

				return array(
					'status'	=> FALSE,
					'message'	=> 'Insertion failed.',
					'reason'	=> $result
				);
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'This API is only accessible by individual account purchase.'
				);
			}

		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Corporate Buy Plan does not exist.'
		);
	}

	public function insertCarePlanBusinessInformation( )
	{
		$input = Input::all();
		$corporate_start_buy = new CorporateBuyStart();

		$check = self::checkCorporateBuyExistence($input['customer_buy_start_id']);

		if($check) {
			$corporate_business_info = new CorporateBusinessInformation();


			$check_corporate_business_info = $corporate_business_info->checkBusinessInfo($input['customer_buy_start_id']);
			$data = array(
				'customer_buy_start_id'	=> $input['customer_buy_start_id'],
				'company_name'				=> $input['company_name'],
				'nature_of_business'		=> $input['nature_of_business'],
				'company_address'			=> $input['company_address'],
				'postal_code'				=> $input['postal_code'],
				'establishment'				=> $input['establishment']
			);

			if($check_corporate_business_info) {
				$result = $corporate_business_info->updateusinessInfo($input['customer_buy_start_id'], $data);
			} else {
				$result = $corporate_business_info->insertBusinessCorporateInfo($data);
			}

			if($result) {
				return array(
					'status'	=> TRUE,
					'message'	=> 'Success!'	
				);
			}

			return array(
				'status'	=> FALSE,
				'message'	=> 'Insertion failed.'	
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Corporate Buy Plan does not exist.'
		);
	}

	public function insertCorporatePlan( )
	{
		$input = Input::all();
		$corporate_start_buy = new CorporateBuyStart();

		$check = self::checkCorporateBuyExistence($input['customer_buy_start_id']);

		if($check) {

			if($input['choose_plan'] == "free" || $input['choose_plan'] == "per_year") {
				$corporate_plan = new CorporatePlan();

				$check_plan = $corporate_plan->checkPlan($input['customer_buy_start_id']);
				// $plan = $corporate_plan->getCorporatePlan($input['customer_buy_start_id']);
				$data = array(
					'customer_buy_start_id'	=> $input['customer_buy_start_id'],
					'choose_plan'				=> $input['choose_plan'],
					'plan_amount'				=> $input['plan_amount'],
					'plan_start'				=> date('Y-m-d', strtotime($input['plan_start'])),
					'active'						=> 1
				);

				if($check_plan) {
					$result = $corporate_plan->updateCorporatePlanChoose($input['customer_buy_start_id'], $data);
				} else {
					$result = $corporate_plan->insertCorporatePlan($data);
				}

				if($input['cover_type'] == "individual") {
					$update_date = array(
						'company_postal_code'	=> $input['company_postal_code'],
						'contact_gender'			=> $input['gender'],
						'plan_start'					=> date('Y-m-d', strtotime($input['plan_start'])),
						'contact_age'					=> $input['age'],
					);
				} else if($input['cover_type'] == "team/corporate") {
					$update_date = array(
						'company_postal_code'	=> $input['company_postal_code'],
						'plan_start'					=> date('Y-m-d', strtotime($input['plan_start'])),
					);
				}
				DB::table('customer_buy_start')->where('customer_buy_start_id', $input['customer_buy_start_id'])->update($update_date);

				if($result) {
					return array(
						'status'	=> TRUE,
						'message'	=> 'Success!',
						'data'		=> $result
					);
				}
				return array(
					'status'	=> FALSE,
					'message'	=> 'Insertion failed.'	
				);

			} else {	
				return array(
					'status'	=> FALSE,
					'message'	=> 'Please choose either free or per_year.'
				);
			}

		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Corporate Buy Plan does not exist.'
		);
	}

	public function insertCorporateBusinessContact( )
	{
		$input = Input::all();
		$corporate_start_buy = new CorporateBuyStart();

		$check = self::checkCorporateBuyExistence($input['customer_buy_start_id']);

		if($check) {

			if($input['billing_contact'] == true || $input['billing_contact'] == false) {
				if($input['billing_address'] == true || $input['billing_address'] == false) {
					$corporate_business_contact = new CorporateBusinessContact();
					$data = array(
						'customer_buy_start_id'	=> $input['customer_buy_start_id'],
						'first_name'				=> $input['first_name'],
						'last_name'					=> $input['last_name'],
						'job_title'					=> $input['job_title'],
						'work_email'				=> $input['work_email'],
						'phone'						=> $input['phone'],
						'billing_contact'			=> $input['billing_contact'] ? "true" : "false",
						'billing_address'			=> $input['billing_address'] ? "true" : "false"
					);

					$result = $corporate_business_contact->insertCorporateBusinessContact($data);
					if($result) {
						if($input['billing_contact'] == false) {
							$corporate_billing_contact = new CorporateBillingContact();
							$cbc = array(
								'customer_buy_start_id'	=> $input['customer_buy_start_id'],
								'first_name'				=> $input['billing_first_name'],
								'last_name'					=> $input['billing_last_name'],
								'work_email'				=> $input['billing_work_email']
							);
							$corporate_billing_contact->insertCorporateBillingContact($cbc);
						}

						if($input['billing_address'] == true) {
							$corporate_billing_address = new CorporateBillingAddress();
							$cba = array(
								'customer_buy_start_id'	=> $input['customer_buy_start_id'],
								'billing_address'			=> $input['billing_address_data'],
								'postal_code'				=> $input['postal_code']
							);
							$corporate_billing_address->insertCorporateBillingAddress($cba);
						}
						return array(
							'status'	=> TRUE,
							'message'	=> 'Success!',
							'data'		=> $result
						);
					}
			
					return array(
						'status'	=> FALSE,
						'message'	=> 'Insertion failed.',
						'reason'	=> $insert_corporate
					);
				} else {
					return array(
						'status'	=> FALSE,
						'message'	=> 'billing_address value only allow true or false'
					);
				}
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'billing_contact value only allow true or false'
				);
			}

			
			$result = $corporate_business_contact->insertBusinessCorporateInfo($data);
			if($result) {
				return array(
					'status'	=> TRUE,
					'message'	=> 'Success!',
					'data'		=> $result
				);
			}

			return array(
				'status'	=> FALSE,
				'message'	=> 'Insertion failed.'	
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Corporate Buy Plan does not exist.'
		);
	}

	public function createHRDashboardAccount()
	{
		$input = Input::all();
		$corporate_start_buy = new CorporateBuyStart();

		$check = self::checkCorporateBuyExistence($input['customer_buy_start_id']);

		if($check) {

			if($check->cover_type == "team/corporate" && $check->corporate == "true") {
				$hr = new HRDashboard();

				$check_hr_account = $hr->checkHR($input['customer_buy_start_id']);

				$hr_data = array(
					'customer_buy_start_id'	=> $input['customer_buy_start_id'],
					'email'									=>	$input['email'],
					'password'							=> md5($input['password']),
					'temp_password'					=> $input['password']
				);

				if($check_hr_account > 0) {
						$result = $hr->updateHRDashboardData($hr_data, $input['customer_buy_start_id']);
				} else {
					$check_email = $hr->checkEmail($input['email']);

					if($check_email > 0) {
						return array(
							'status'	=> FALSE,
							'message'	=> 'Email Address already taken. Please choose another Email Address.'
						);
					}
					$result = $hr->insertHRDashboard($hr_data);
				}
			} else {
				$data = array(
					'plan_start'	=> date('Y-m-d', strtotime($input['plan_start']))
				);
				$result = $corporate_start_buy->updateCorporateBuyStartData($data, $input['customer_buy_start_id']);
			}

			if($result) {
				return array(
					'status'	=> TRUE,
					'message'	=> 'Success!',
					'data'		=> $result
				);
			}

			return array(
				'status'	=> FALSE,
				'message'	=> 'Insertion failed.'	
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Corporate Buy Plan does not exist.'
		);
	}

	public function insertPayment( )
	{
		$input = Input::all();
		$corporate_start_buy = new CorporateBuyStart();
		$corporate_plan = new CorporatePlan();
		$check = self::checkCorporateBuyExistence($input['customer_buy_start_id']);
		$plan = $corporate_plan->getCorporatePlan($input['customer_buy_start_id']);
		
		if($check) {
			$corporate_payment = new CorporatePayment();

			$data_payment = array(
				'customer_buy_start_id'	=> $input['customer_buy_start_id'],
				'cheque'								=> $input['cheque'] ? "true" : "false",
				'credit_card'						=> $input['credit_card'] ? "true" : "false"
			);

			$check_corporate_payment = $corporate_payment->checkCorporatePayment($input['customer_buy_start_id']);

			if($check_corporate_payment > 0) {
				$result_payment = $corporate_payment->updatePayment($data_payment, $input['customer_buy_start_id']);
			} else {
				$result_payment = $corporate_payment->insertCorporatePayment($data_payment);
			}


			if($result_payment) {
				if($input['cheque'] == true && $input['credit_card'] == false || $input['cheque'] == false && $input['credit_card'] == true) {

					if($input['cheque'] == true && $input['credit_card'] == false) {
						$check_cheque = new CorporateCheque();
						$payment_data = $corporate_plan->getCorporatePlan($input['customer_buy_start_id']);
						$to_pay = $check->employees * 99;
						$total = $to_pay - $payment_data->discount;
						$corporate_active_plan = new CorporateActivePlan();

						$active_plan = array(
							'customer_start_buy_id'			=> $input['customer_buy_start_id'],
							'stripe_transaction_id'			=> "NULL",
							'plan_amount'								=> $to_pay,
							'discount'									=> $payment_data->discount,
							'status'										=> "false",
							'plan_start'								=> date('Y-m-d', strtotime($check->plan_start)),
							'duration'									=> "1 year",
							'end_date_policy'						=> date('Y-m-d', strtotime('+1 year', strtotime($check->plan_start))),
							'employees'									=> $check->employees,
							'cheque'										=> "true",
							'credit'										=> "false",
							'plan_id'										=> $plan->customer_plan_id,
						);

						$check_active_plan = $corporate_active_plan->checkActivePlan($input['customer_buy_start_id']);

						if($check_active_plan > 0) {
							$corporate_active_plan->updateCorporateActivePlanByCustomerStartId($active_plan, $input['customer_buy_start_id']);
							$get_active_plan = $corporate_active_plan->getActivePlanData($input['customer_buy_start_id']);
							$active_plan_id = $get_active_plan->customer_active_plan_id;
						} else {
							$corporate_active_plan_id = $corporate_active_plan->createCorporateActivePlan($active_plan);
							$active_plan_id = $corporate_active_plan_id->id;
						}
						$get_active_plan = $corporate_active_plan->getActivePlanData($input['customer_buy_start_id']);
						$data = array(
							'customer_buy_start_id'		=> $input['customer_buy_start_id'],
							'customer_active_plan_id' => $active_plan_id
						);

						$result_cheque = $check_cheque->checkCheque($input['customer_buy_start_id'], $input['start_date']);
						if($result_cheque > 0) {
							$result = $check_cheque->updateCheque($input['customer_buy_start_id'], $data);
						} else {
							$result = $check_cheque->insertCorporateCheque($data);	
						}

						$data_history = array(
							'customer_buy_start_id'		=> $input['customer_buy_start_id'],
							'plan_start'							=> $get_active_plan->plan_start,
							'status'									=> 'started',
							'employees'								=> $check->employees,
							'amount'									=> $to_pay,
							'stripe_transaction_id'		=> 'NULL',
							'customer_active_plan_id' => $get_active_plan->customer_active_plan_id
						);

						$corporate_payment_history = new CorporatePaymentHistory();
						$corporate_payment_history->insertCorporatePaymentHistory($data_history);

						$generate = self::generateAccount($input['customer_buy_start_id']);
						self::sendWelcomeEmailCheque($input['customer_buy_start_id'], $generate['password']);
						$corporate_start_buy->updateCorporateBuyStart($input['customer_buy_start_id']);
						if($result && $generate) {
							return array(
								'status'	=> TRUE,
								'message'	=> 'Success!',
								'data'		=> $result
							);
						}

						return array(
							'status'	=> FALSE,
							'message'	=> 'Insertion failed.'	
						);

					} elseif($input['cheque'] == false && $input['credit_card'] == true) {
						$payment_data = $corporate_plan->getCorporatePlan($input['customer_buy_start_id']);

						if($check->cover_type == "team/corporate" && $check->corporate == "true") {
							$to_pay = $check->employees * 99;
							$total = $to_pay - $payment_data->discount;
						} else {
							$to_pay = $check->employees * 125;
							$total = $to_pay - $payment_data->discount;
						}

						$corporate_active_plan = new CorporateActivePlan();

						$active_plan = array(
							'customer_start_buy_id'			=> $input['customer_buy_start_id'],
							'stripe_transaction_id'			=> "NULL",
							'plan_amount'								=> $to_pay,
							'discount'									=> $payment_data->discount,
							'status'										=> "false",
							'plan_start'								=> date('Y-m-d', strtotime($check->plan_start)),
							'duration'									=> "1 year",
							'end_date_policy'						=> date('Y-m-d', strtotime('+1 year', strtotime($check->plan_start))),
							'employees'									=> $check->employees,
							'cheque'										=> "false",
							'credit'										=> "true",
							'plan_id'										=> $plan->customer_plan_id
						);

						$check_corporate_active_plan = $corporate_active_plan->checkActivePlan($input['customer_buy_start_id']);
						if($check_corporate_active_plan > 0) {
							$corporate_active_plan->updateCorporateActivePlanByCustomerStartId($active_plan, $input['customer_buy_start_id']);
						} else {
							$corporate_active_plan->createCorporateActivePlan($active_plan);
						}
						return array(
							'status'	=> TRUE,
							'message'	=> 'Success! Please insert details for credit card payment.'
						);
					}

				} else {
					return array(
						'status'	=> FALSE,
						'message'	=> 'cheque should be false and credit_card should be true or cheque should be true or credit_card should be false'
					);
				}
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'Insertion failed.'	
				);
			} 


		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Corporate Buy Plan does not exist.'
		);
	}


	public function generateAccount($id) 
	{
		$corporate_start_buy = new CorporateBuyStart();
		$check = self::checkCorporateBuyExistence($id);
		$corporate_link = new CorporateLinkBuy();

		if($check) {
			if($check->cover_type == "team/corporate" && $check->corporate == "true") {
				$corporate_account = new Corporate();
				$corporate_business_info = new CorporateBusinessInformation();
				$corporate_business_contact = new CorporateBusinessContact();

				$contact = $corporate_business_contact->getCorporateBusinessContact($id);

				$corporate_data_business_info = $corporate_business_info->getCorporateBusinessInfo($id);
				$hr_dashboard = DB::table('customer_hr_dashboard')->where('customer_buy_start_id', $id)->first();
				// $password = StringHelper::get_random_password(8);
				$data_corporate_account = array(
					'email'					=> $contact->work_email,
					'password'			=> md5($hr_dashboard->temp_password),
					'company_name'	=> $corporate_data_business_info->company_name
				);
				$insert_corporate = $corporate_account->createCorporate($data_corporate_account);
				$result_corporate_link = $corporate_link->insert(array('customer_buy_start_id' => $id, 'corporate_id' => $insert_corporate->id));

				if($insert_corporate && $result_corporate_link) {
					return array('status' => TRUE, 'password' => $hr_dashboard->temp_password);
				} else {
					return array('status' => FALSE);
				}
			} else {
				$user = new User();
				$customer_details = new CustomerPersonalDetails();
				
				$check_corporate_link = $corporate_link->checkUserLink($id);
				if($check_corporate_link > 0) {
					$result_check_corporate_link = $corporate_link->getCorporateLink($id);
					$password = StringHelper::get_random_password(8);
					$result_new_user = $user->updateIndividualUserFromPurchase(md5($password), $result_check_corporate_link->user_id);
					if($result_new_user) {
						return array('status' => TRUE, 'password' => $password);
					} else {
						return array('status' => FALSE);
					}
				} else {
					$plan_type = new UserPlanType();
					
					$personal_details = $customer_details->getCustomerPersonalDetailsData($id);
					$password = StringHelper::get_random_password(8);
					$personal_details['password'] = md5($password);
					$result_new_user = $user->createIndividualUserFromPurchase($personal_details);
					// return $result_new_user;
					$check_plan_type = $plan_type->checkUserPlanType($result_new_user);
					// return $check_plan_type;
					$active_plan = new CorporateActivePlan();
					$active_plan_result = $active_plan->getActivePlanData($id);

					if($check_plan_type == 0) {
						$group_package = new PackagePlanGroup();
						$bundle = new Bundle();
						$user_package = new UserPackage();
						
						$group_package_id = $group_package->getPackagePlanGroupDefault();
						$result_bundle = $bundle->getBundle($group_package_id);
						// return $result_bundle;
						foreach ($result_bundle as $key => $value) {
							$user_package->createUserPackage($value->care_package_id, $result_new_user);
						}

						$plan_type_data = array(
							'user_id'						=> $result_new_user,
							'package_group_id'	=> $group_package_id,
							'duration'					=> '1 year',
							'plan_start'			=> date('Y-m-d', strtotime($active_plan_result->plan_start))
						);
						$plan_type->createUserPlanType($plan_type_data);
					}
					// store user plan history
					$user_plan_history = new UserPlanHistory();
					$user_plan_history_data = array(
						'user_id'		=> $result_new_user,
						'type'			=> "started",
						'date'			=> date('Y-m-d', strtotime($active_plan_result->plan_start)),
						'customer_active_plan_id' => $active_plan_result->customer_active_plan_id
					);

					$user_plan_history->createUserPlanHistory($user_plan_history_data);

					$result_corporate_link = $corporate_link->insert(array('customer_buy_start_id' => $id, 'user_id' => $result_new_user));
					if($result_new_user && $result_corporate_link) {
						return array('status' => TRUE, 'password' => $password);
					} else {
						return array('status' => FALSE);
					}
				}
			}
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Corporate Buy Plan does not exist.'
		);

	}

	public function payCreditCardPlan( )
	{

		$hostName = $_SERVER['HTTP_HOST'];
    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $server = $protocol.$hostName;

		$input = Input::all();
		$corporate_start_buy = new CorporateBuyStart();

		$check = self::checkCorporateBuyExistence($input['customer_buy_start_id']);

		if($check) {
			if($check->status == 1) {
				return array(
					'status'	=> TRUE,
					'message'	=> 'Corporate plan already paid.'
				);
			}

			if(empty($input['stripeToken'])) {
				Session::put('error_message_purchase', 'No token.');
				return Redirect::to($server.'/uat/care-plan#/steps/payment_failed');
			}

			$token  = $input['stripeToken'];
			$stripe = StripeHelper::config();
			\Stripe\Stripe::setApiKey($stripe['secret_key']);

			$corporate_business_contact = new CorporateBusinessContact();
			$result_corporate_business_contact = $corporate_business_contact->getCorporateBusinessContact($input['customer_buy_start_id']);

			if($check->cover_type == "team/corporate" && $check->corporate == "true") {
				if($result_corporate_business_contact->billing_contact == false) {
					$corporate_billing_contact = new CorporateBillingContact();
					$result_corporate_billing_contact = $corporate_billing_contact->getCorporateBillingContact($input['customer_buy_start_id']);

					$first_name = $result_corporate_billing_contact->first_name;
					$last_name = $result_corporate_billing_contact->last_name;
					$email = $result_corporate_billing_contact->work_email;
					// return "0";
				} else {
					$first_name = $result_corporate_business_contact->first_name;
					$last_name = $result_corporate_business_contact->last_name;
					$email = $result_corporate_business_contact->work_email;
				}
			} else {
				$customer_details = new CustomerPersonalDetails();
				$customer_details_data = $customer_details->getCustomerPersonalDetailsData($input['customer_buy_start_id']);
				$email = $customer_details_data->email;
			}

			$corporate_plan = new CorporatePlan();
			$payment_data = $corporate_plan->getCorporatePlan($input['customer_buy_start_id']);
			if($check->cover_type == "team/corporate" && $check->corporate == "true") {
				$to_pay = $check->employees * 99;
				$total = $to_pay - floatval($payment_data->discount);
				$title = 'Care Plan Subscrition - ' . date('F d, Y', strtotime($check->plan_start)) . ' - Corporate';
			} else {
				$to_pay = $check->employees * 125;
				$total = $to_pay - floatval($payment_data->discount);
				$title = 'Care Plan Subscrition - ' . date('F d, Y', strtotime($check->plan_start)) . ' - Individual';
			}

			$corporate_business_info = new CorporateBusinessInformation();
			$corporate_business_info_data = $corporate_business_info->getCorporateBusinessInfo($input['customer_buy_start_id']);

			// return self::generateAccount($input['customer_buy_start_id']);
			try {
				$customer = \Stripe\Customer::create(array(
			      'email' => $email,
			      'source'  => $token
				));

				$charge = \Stripe\Charge::create(
					array(
						'customer' 		=> $customer->id,
						'amount' 			=> 1 * 100,
						'currency' 		=> 'sgd',
						'description'	=> $title,
						'metadata'		=> array('customer_start_id' => $input['customer_buy_start_id'], 'plan_start' => $check->plan_start, 'total_amount' => $to_pay, 'discount' => $payment_data->discount, 'total_pay' => $total)
						)
				);
				if($charge['outcome']['network_status'] == "approved_by_network" && $charge['outcome']['type'] == "authorized" && $charge['paid'] == true) {

					$corporate_start_buy = new CorporateBuyStart();
					$check = self::checkCorporateBuyExistence($input['customer_buy_start_id']);
					$corporate_account = new Corporate();
					$corporate_business_info = new CorporateBusinessInformation();
					$corporate_link = new CorporateLinkBuy();

					$generate = self::generateAccount($input['customer_buy_start_id']);
					$corporate_active_plan = new CorporateActivePlan();
					$get_active_plan = $corporate_active_plan->getActivePlanData($input['customer_buy_start_id']);

					if($generate['status']) {
						$data_history = array(
							'customer_buy_start_id'		=> $input['customer_buy_start_id'],
							'plan_start'							=> $get_active_plan->plan_start,
							'status'									=> 'started',
							'employees'								=> $check->employees,
							'amount'									=> $to_pay,
							'stripe_transaction_id'		=> $charge['id'],
							'customer_active_plan_id' => $get_active_plan->customer_active_plan_id
						);

						$corporate_payment_history = new CorporatePaymentHistory();
						$corporate_payment_history->insertCorporatePaymentHistory($data_history);

						$active_plan = array(
							'stripe_transaction_id'			=> $charge['id'],
							'status'										=> "true",
							'paid'											=> "true"
						);

						$corporate_active_plan->updateCorporateActivePlanByCustomerStartId($active_plan, $input['customer_buy_start_id']);

						$logs = new PaymentLogsStripe();
						$logs->createLog(array('message' => serialize($charge), 'customer_start_id' => $input['customer_buy_start_id']));
						self::sendWelcomeEmail($input['customer_buy_start_id'], $generate['password']);
						$corporate_start_buy->updateCorporateBuyStart($input['customer_buy_start_id']);
						return Redirect::to($server.'/uat/care-plan#/steps/payment_success');
					} else {
						return array(
							'status'	=> FALSE,
							'message'	=> 'Update failed.'
						);
					}
				} else {
					$data = [];
					$data['error'] = $charge['outcome']['seller_message'];
					return View::make('errors.payment_failed', $data);
				}
			} catch(\Stripe\Error\Card $e) {
			  // Since it's a decline, \Stripe\Error\Card will be caught
			  $body = $e->getJsonBody();
			  $err  = $body['error'];
			  $erro_message = $err['message'];
			} catch (\Stripe\Error\RateLimit $e) {
			  // Too many requests made to the API too quickly
			  $body = $e->getJsonBody();
			  $err  = $body['error'];
			  $erro_message = $err['message'];
			} catch (\Stripe\Error\InvalidRequest $e) {
			  // Invalid parameters were supplied to Stripe's API
			  $body = $e->getJsonBody();
			  $err  = $body['error'];
			  $erro_message = $err['message'];
			} catch (\Stripe\Error\Authentication $e) {
			  // Authentication with Stripe's API failed
			  // (maybe you changed API keys recently)
			  $body = $e->getJsonBody();
			  $err  = $body['error'];
			  $erro_message = $err['message'];
			} catch (\Stripe\Error\ApiConnection $e) {
			  // Network communication with Stripe failed
			  $body = $e->getJsonBody();
			  $err  = $body['error'];
			  $erro_message = $err['message'];
			} catch (\Stripe\Error\Base $e) {
			  // Display a very generic error to the user, and maybe send
			  // yourself an email
			  $body = $e->getJsonBody();
			  $err  = $body['error'];
			  $erro_message = $err['message'];
			} catch (Exception $e) {
			  // Something else happened, completely unrelated to Stripe
			  $erro_message = 'Ooops. Something went wrong.';
			}
			$data = [];
			$data['error'] = $erro_message;
			return View::make('errors.payment_failed', $data);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Corporate Buy Plan does not exist.'
		);
	}

	public function corporateMatchCode( )
	{
		$input = Input::all();

		$check = self::checkCorporateBuyExistence($input['customer_buy_start_id']);

		if($check) {
			$promo_history = new CorporatePromoCodeHistory();
			$result_promo = $promo_history->checkExistingPromoCode($input['customer_buy_start_id'], $input['code']);

			if($result_promo > 0) {
				return array(
					'status'	=> FALSE,
					'message'	=> 'Promo Code already taken.'
				);
			}

			$promo_code = new NewPromoCode();

			$new_promocode = $promo_code->matchCode($input['code']);
			// return $new_promocode;
			if($new_promocode) {
				$corporate_plan = new CorporatePlan();
				$update_result = $corporate_plan->updateCorporatePlan($input['customer_buy_start_id'], $new_promocode->amount);
				// return $update_result;
				if($update_result) {
					$promo_history->insertRecordPromo($input['customer_buy_start_id'], $input['code']);
					return array(
						'status'	=> TRUE,
						'message'	=> 'Promo inserted'
					);
				} else {
					return array(
						'status'	=> FALSE,
						'message'	=> 'Insertion Failed.'
					);
				}
			}

			return array(
				'status'	=> FALSE,
				'message'	=> 'Promo Code does not exist.'
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Corporate Buy Plan does not exist.'
		);
	}

	public function updateCorporateBuyStart( )
	{
		$input = Input::all();
		$corporate_start_buy = new CorporateBuyStart();
		$check = self::checkCorporateBuyExistence($input['customer_buy_start_id']);
		if($check) {
			if($check->cover_type == "team/corporate" && $check->corporate == "true") {
				$result = $corporate_start_buy->updateCorporateBuyStartData($input['customer_buy_start_id'], $input);
			} else if($check->cover_type == "individual" && $check->individual == "true"){
				$result = $corporate_start_buy->updateCorporateBuyStartData($input['customer_buy_start_id'], $input);
			}
			if($result) {
				return array(
					'status'	=> TRUE,
					'message'	=> 'Updated.'
				);
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'Update Failed.'
				);
			}
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Corporate Buy Plan does not exist.'
		);
	}

	public function updateCorporatePlan( )
	{
		$input = Input::all();
		$corporate_plan = new CorporatePlan();
		$check = self::checkCorporateBuyExistence($input['customer_buy_start_id']);
		if($check) {
			$result = $corporate_plan->updateCorporatePlanData($input['customer_plan_id'], $input);
			if($result) {
				return array(
					'status'	=> TRUE,
					'message'	=> 'Updated.'
				);
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'Update Failed.'
				);
			}
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Corporate Buy Plan does not exist.'
		);
	}

	public function updateBusinessInformation( )
	{
		$input = Input::all();
		$corporate_business_info = new CorporateBusinessInformation();
		$check = self::checkCorporateBuyExistence($input['customer_buy_start_id']);
		if($check) {
			$result = $corporate_business_info->updateCorporateBusinessInformation($input['customer_business_information_id'], $input);
			if($result) {
				return array(
					'status'	=> TRUE,
					'message'	=> 'Updated.'
				);
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'Update Failed.'
				);
			}
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Corporate Buy Plan does not exist.'
		);
	}

	public function updateHrDashboard( )
	{
		$input = Input::all();
		$corporate_hr_dashboard = new HRDashboard();
		$check = self::checkCorporateBuyExistence($input['customer_buy_start_id']);
		if($check) {
			$data = array(
				'email'		=> $input['email'],
				'password'	=> md5($input['password'])
			);
			$result = $corporate_hr_dashboard->updateCorporateHrDashboard($input['hr_dashboard_id'], $data);
			if($result) {
				return array(
					'status'	=> TRUE,
					'message'	=> 'Updated.'
				);
			} else {
				return array(
					'status'	=> FALSE,
					'message'	=> 'Update Failed.'
				);
			}
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Corporate Buy Plan does not exist.'
		);
	}

	public function checkCorporateBuyExistence($id)
	{
		$corporate_start_buy = new CorporateBuyStart();

		$check = $corporate_start_buy->checkCorporateStart($id);

		if($check) {
			return $check;
		} else {
			return FALSE;
		}
	}


	// send emails
	public function sendWelcomeEmail($id, $password)
	{
		$check = self::checkCorporateBuyExistence($id);

		if($check->cover_type = "team/corporate" && $check->corporate == "true") {
			$corporate_business_contact = new CorporateBusinessContact();
			$contact = $corporate_business_contact->getCorporateBusinessContact($id);
			$hr = new HRDashboard();

			if($contact->billing_contact == false) {
				$corporate_billing_contact = new CorporateBillingContact();
				$result_corporate_billing_contact = $corporate_billing_contact->getCorporateBillingContact($id);

				$first_name = $result_corporate_billing_contact->first_name;
				$last_name = $result_corporate_billing_contact->last_name;
				$contact_email = $result_corporate_billing_contact->work_email;
			} else {
				$contact_email = $contact->work_email;
				$first_name = $contact->first_name;
				$last_name = $contact->last_name;
			}

			$user_contact = $hr->getHRDashboard($id);
			$login_email = $user_contact->email;
			$email['emailPage']	= 'email-templates.company-welcome-pack-credit';
			$email['invoice_link'] = url('get/corporate/invoice', $parameters = array($id), $secure = null);
			$email['welcome_pack'] = url('get/welcome-pack-corporate', $parameters = array(), $secure = null);
			$email['contract'] = url('get/contract', $parameters = array($id), $secure = null);
			$email['emailSubject'] = 'WELCOME TO MEDNEFITS CARE (COMPANY WELCOME PACK)';
			// return $email;
		} else {
			$user = new User();
			$customer_personal_details = new CustomerPersonalDetails();
			$active_plan = new CorporateActivePlan();
			$user_result = $user->getUserByLink($id);
			$active_plan_result = $active_plan->getActivePlanData($id);
			$login_email = $user_result->Email;
			$contact = $customer_personal_details->getCustomerPersonalDetailsData($id);
			$contact_email = $contact->email;
			$first_name = $contact->first_name;
			$last_name = $contact->last_name;

			$email['welcome_pack'] = url('get/welcome-pack-individual', $parameters = array($id), $secure = null);
			$email['emailPage']	= 'email-templates.individual-employee-welcome';
			$email['plan_start'] = date('d F Y', strtotime($active_plan_result->plan_start));
			$email['emailSubject'] = 'WELCOME TO MEDNEFITS CARE (INDIVIDUAL WELCOME PACK)';
		}

		$email['emailName'] = $first_name.' '.$last_name;
		$email['login_id'] = $login_email;
		$email['login_password'] = $password;
		$email['emailTo']	= $contact_email;

		// if(StringHelper::Deployment() == 1){
        EmailHelper::sendEmail($email);
    // }
	}

	public function sendWelcomeEmailCheque($id, $password)
	{
		$check = self::checkCorporateBuyExistence($id);

		// if($check->cover_type = "team/corporate" && $check->corporate == "true") {
			$hr = new HRDashboard();
			$corporate_business_contact = new CorporateBusinessContact();
			$user_contact = $hr->getHRDashboard($id);
			$login_email = $user_contact->email;
			$contact = $corporate_business_contact->getCorporateBusinessContact($id);
			$contact_email = $contact->work_email;
			$first_name = $contact->first_name;
			$last_name = $contact->last_name;
		// }
		//  else {
		// 	$user = new User();
		// 	$customer_personal_details = new CustomerPersonalDetails();
		// 	$user_result = $user->getUserByLink($id);

		// 	$login_email = $user_result->Email;
		// 	$contact = $customer_personal_details->getCustomerPersonalDetailsData($id);
		// 	$contact_email = $contact->email;
		// 	$first_name = $contact->first_name;
		// 	$last_name = $contact->last_name;
		// }

		$email['emailName'] = $first_name.' '.$last_name;
		$email['login_id'] = $login_email;
		$email['login_password'] = $password;
		$email['emailTo']	= $contact_email;
		$email['invoice_link'] = $id;
		$email['emailSubject'] = 'WELCOME TO MEDNEFITS CARE (COMPANY WELCOME PACK)';
		$email['emailPage']				= 'email-templates.company-welcome-pack-cheque';
		$email['invoice_link'] = url('get/invoice', $parameters = array($id), $secure = null);
		// if(StringHelper::Deployment() == 1){
        EmailHelper::sendEmail($email);
    // }
	}

	public function activateCarePlanUser( )
	{
		$active_plan = new CorporateActivePlan();
		$result = $active_plan->activateCarePlanUser();
		return $result;
	}

}
