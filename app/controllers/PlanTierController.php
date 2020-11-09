<?php
use Illuminate\Support\Facades\Input;
class PlanTierController extends \BaseController {

	public function getPlanTiers( )
	{
		$customer_id = PlanHelper::getCusomerIdToken();
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();
		$plan_tiers = DB::table('plan_tiers')->where('customer_id', $customer_id)->where('active', 1)->get();

		return array('status' => true, 'data' => $plan_tiers, 'currency_type' => $customer->currency_type);
	}
	public function createPlanTier( )
	{
		$input = Input::all();
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_data = StringHelper::getJwtHrSession();
		$hr_id = $hr_data->hr_dashboard_id;
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['medical_annual_cap'])) {
			return array('status' => false, 'message' => 'Medical Annual Cap is required.');
		}

		if(empty($input['wellness_annual_cap'])) {
			return array('status' => false, 'message' => 'Wellness Annual Cap is required.');
		}

		// if(empty($input['gp_cap_per_visit'])) {
		// 	return array('status' => false, 'message' => 'GP Cap Per Visit is required.');
		// }

		if(empty($input['member_head_count'])) {
			return array('status' => false, 'message' => 'Member Head Count is required.');
		}

		// check remaining head count for member
		$plan_status = DB::table('customer_plan_status')
						->where('customer_id', $customer_id)
						->orderBy('created_at', 'desc')
						->first();

		if(!$plan_status) {
			return array('status' => false, 'message' => 'Customer Plan Head Count not found.');
		}

		if($input['dependent_head_count'] > 0) {
			$check_dependents = DB::table('dependent_plans')->where('customer_plan_id', $plan_status->customer_plan_id)->first();

			if($check_dependents) {
				if(empty($input['dependent_head_count'])) {
					return array('status' => false, 'message' => 'Dependent Head Count is required.');
				}

				$dependents = DB::table('dependent_plan_status')
								->where('customer_plan_id', $plan_status->customer_plan_id)
								->orderBy('created_at', 'desc')
								->first();

				$total_left_count = $dependents->total_dependents - $dependents->total_enrolled_dependents;

				$plan_tier_dependent_head_count = DB::table('plan_tiers')
												->where('customer_id', $customer_id)
												->sum('dependent_head_count');

				$plan_tier_dependent_enrolled_count = DB::table('plan_tiers')
												->where('customer_id', $customer_id)
												->sum('dependent_enrolled_count');

				$total_left_plan_tier_dependents = $plan_tier_dependent_head_count - $plan_tier_dependent_enrolled_count;
				$total_dependents = $total_left_count + $total_left_plan_tier_dependents;
				// return $total_dependents;

				if($input['dependent_head_count'] > $total_dependents) {
					return array('status' => false, 'message' => 'Dependents Head Count exceeded.');
				}
			}
		}


		// get plan tier head count
		$plan_tier_member_head_count = DB::table('plan_tiers')
										->where('customer_id', $customer_id)
										->where('active', 1)
										->sum('member_head_count');

		$plan_tier_member_enrolled_count = DB::table('plan_tiers')
											->where('customer_id', $customer_id)
											->where('active', 1)
											->sum('member_enrolled_count');

		$total_left_plan_tier_members = $plan_tier_member_head_count - $plan_tier_member_enrolled_count;

		$total_member_left_count = $plan_status->employees_input - $plan_status->enrolled_employees;
		$total_members = $total_member_left_count - $total_left_plan_tier_members;
		// return $total_member_left_count;

		if($input['member_head_count'] > $total_members) {
			return array('status' => false, 'message' => 'Member Head Count exceeded.');
		}

		$plan_tier_count = DB::table('plan_tiers')
										->where('customer_id', $customer_id)
										->count();
		$plan_tier_name_count = $plan_tier_count + 1;

		$tier = array(
			'customer_id'			=> $customer_id,
			'plan_tier_name'		=> 'Plan Tier '.$plan_tier_name_count,
			'medical_annual_cap'	=> $input['medical_annual_cap'],
			'wellness_annual_cap'	=> $input['wellness_annual_cap'],
			'gp_cap_per_visit'		=> $input['gp_cap_status'] ? $input['gp_cap_per_visit'] : 0,
			'member_head_count'		=> $input['member_head_count'],
			'dependent_head_count'	=> $input['dependent_head_count'] ? $input['dependent_head_count'] : 0
		);

		// return $tier;

		$result = \PlanTier::create($tier);

		if($result) {
			if($admin_id) {
				$admin_logs = array(
                    'admin_id'  => $admin_id,
                    'admin_type' => 'mednefits',
                    'type'      => 'admin_hr_created_plan_tier_details',
                    'data'      => SystemLogLibrary::serializeData($tier)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
			} else {
				$admin_logs = array(
                    'admin_id'  => $hr_id,
                    'admin_type' => 'hr',
                    'type'      => 'admin_hr_created_plan_tier_details',
                    'data'      => SystemLogLibrary::serializeData($tier)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
			}
			return array('status' => true, 'message' => 'Plan Tier Created.');
		}

		return array('status' => false, 'message' => 'Failed to create Plan Tier.');
	}

	public function updatePlanTier( )
	{
		$input = Input::all();
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_data = StringHelper::getJwtHrSession();
		$hr_id = $hr_data->hr_dashboard_id;

		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['plan_tier_id'])) {
			return array('status' => false, 'message' => 'Plan Tier ID is required.');
		}

		$plan_tier = DB::table('plan_tiers')->where('plan_tier_id', $input['plan_tier_id'])->where('active', 1)->first();

		if(!$plan_tier) {
			return array('status' => false, 'message' => 'Plan Tier does not exist.');
		}

		if(empty($input['medical_annual_cap'])) {
			return array('status' => false, 'message' => 'Medical Annual Cap is required.');
		}

		if(empty($input['wellness_annual_cap'])) {
			return array('status' => false, 'message' => 'Wellness Annual Cap is required.');
		}

		// if(empty($input['gp_cap_per_visit'])) {
		// 	return array('status' => false, 'message' => 'GP Cap Per Visit is required.');
		// }

		if(empty($input['member_head_count'])) {
			return array('status' => false, 'message' => 'Member Head Count is required.');
		}

		// check remaining head count for member
		$plan_status = DB::table('customer_plan_status')
						->where('customer_id', $customer_id)
						->orderBy('created_at', 'desc')
						->first();

		if(!$plan_status) {
			return array('status' => false, 'message' => 'Customer Plan Head Count not found.');
		}


		if((int)$plan_tier->member_head_count == (int)$input['member_head_count']) {
			if($plan_tier->dependent_head_count != (int)$input['dependent_head_count'] && $input['dependent_head_count'] > 0) {
				$check_dependents = DB::table('dependent_plans')->where('customer_plan_id', $plan_status->customer_plan_id)->first();
				if($check_dependents) {
					if(empty($input['dependent_head_count'])) {
						return array('status' => false, 'message' => 'Dependent Head Count is required.');
					}

					$dependents = DB::table('dependent_plan_status')
									->where('customer_plan_id', $plan_status->customer_plan_id)
									->orderBy('created_at', 'desc')
									->first();

					$total_left_count = $dependents->total_dependents - $dependents->total_enrolled_dependents;

					$plan_tier_dependent_head_count = DB::table('plan_tiers')
													->where('customer_id', $customer_id)
													->whereNotIn('plan_tier_id', [$input['plan_tier_id']])
													->where('active', 1)
													->sum('dependent_head_count');

					$plan_tier_dependent_enrolled_count = DB::table('plan_tiers')
													->where('customer_id', $customer_id)
													->where('active', 1)
													->whereNotIn('plan_tier_id', [$input['plan_tier_id']])
													->sum('dependent_enrolled_count');

					$total_left_plan_tier_dependents = $plan_tier_dependent_head_count - $plan_tier_dependent_enrolled_count;
					$total_dependents = $total_left_count + $total_left_plan_tier_dependents;
					// return $total_dependents;

					if($input['dependent_head_count'] > $total_dependents) {
						return array('status' => false, 'message' => 'Dependents Head Count exceeded.');
					}
				}
			}

			$tier = array(
				'medical_annual_cap'	=> $input['medical_annual_cap'],
				'wellness_annual_cap'	=> $input['wellness_annual_cap'],
				'gp_cap_per_visit'		=> $input['gp_cap_per_visit'],
				'member_head_count'		=> $input['member_head_count'],
				'dependent_head_count'	=> $input['dependent_head_count']
			);


			$result = \PlanTier::where('plan_tier_id', $input['plan_tier_id'])->update($tier);

			if($result) {
				if($result) {
					if($admin_id) {
						$admin_logs = array(
		                    'admin_id'  => $admin_id,
		                    'admin_type' => 'mednefits',
		                    'type'      => 'admin_hr_updated_plan_tier_details',
		                    'data'      => SystemLogLibrary::serializeData($input)
		                );
		                SystemLogLibrary::createAdminLog($admin_logs);
					} else {
						$admin_logs = array(
		                    'admin_id'  => $hr_id,
		                    'admin_type' => 'hr',
		                    'type'      => 'admin_hr_updated_plan_tier_details',
		                    'data'      => SystemLogLibrary::serializeData($input)
		                );
		                SystemLogLibrary::createAdminLog($admin_logs);
					}
					return array('status' => true, 'message' => 'Plan Tier Updated.');
				}
				return array('status' => true, 'message' => 'Plan Tier Updated.');
			}

			return array('status' => false, 'message' => 'Failed to update Plan Tier.');
		}

		$check_dependents = DB::table('dependent_plans')->where('customer_plan_id', $plan_status->customer_plan_id)->first();

		if($check_dependents) {
			if(empty($input['dependent_head_count'])) {
				return array('status' => false, 'message' => 'Dependent Head Count is required.');
			}

			$dependents = DB::table('dependent_plan_status')
							->where('customer_plan_id', $plan_status->customer_plan_id)
							->orderBy('created_at', 'desc')
							->first();

			$total_left_count = $dependents->total_dependents - $dependents->total_enrolled_dependents;

			$plan_tier_dependent_head_count = DB::table('plan_tiers')
											->where('customer_id', $customer_id)
											->whereNotIn('plan_tier_id', [$input['plan_tier_id']])
											->where('active', 1)
											->sum('dependent_head_count');

			$plan_tier_dependent_enrolled_count = DB::table('plan_tiers')
											->where('customer_id', $customer_id)
											->whereNotIn('plan_tier_id', [$input['plan_tier_id']])
											->where('active', 1)
											->sum('dependent_enrolled_count');

			$total_left_plan_tier_dependents = $plan_tier_dependent_head_count - $plan_tier_dependent_enrolled_count;
			$total_dependents = $total_left_count + $total_left_plan_tier_dependents;
			// return $total_dependents;

			if($input['dependent_head_count'] > $total_dependents) {
				return array('status' => false, 'message' => 'Dependents Head Count exceeded.');
			}
		}

		// get plan tier head count
		$plan_tier_member_head_count = DB::table('plan_tiers')
										->where('customer_id', $customer_id)
										->where('active', 1)
										->whereNotIn('plan_tier_id', [$input['plan_tier_id']])
										->sum('member_head_count');

		$plan_tier_member_enrolled_count = DB::table('plan_tiers')
											->where('customer_id', $customer_id)
											->where('active', 1)
											->whereNotIn('plan_tier_id', [$input['plan_tier_id']])
											->sum('member_enrolled_count');

		$total_left_plan_tier_members = $plan_tier_member_head_count - $plan_tier_member_enrolled_count;

		$total_member_left_count = $plan_status->employees_input - $plan_status->enrolled_employees;
		$total_members = $total_member_left_count - $total_left_plan_tier_members;

		if($input['member_head_count'] > $total_members) {
			return array('status' => false, 'message' => 'Member Head Count exceeded.');
		}


		// check lates plan employee status count
		// $total_employee_count = $plan_status->

		$tier = array(
			'medical_annual_cap'	=> $input['medical_annual_cap'],
			'wellness_annual_cap'	=> $input['wellness_annual_cap'],
			'gp_cap_per_visit'		=> $input['gp_cap_per_visit'],
			'member_head_count'		=> $input['member_head_count'],
			'dependent_head_count'	=> $input['dependent_head_count']
		);


		$result = \PlanTier::where('plan_tier_id', $input['plan_tier_id'])->update($tier);

		if($result) {
			if($admin_id) {
				$admin_logs = array(
                    'admin_id'  => $admin_id,
                    'admin_type' => 'mednefits',
                    'type'      => 'admin_hr_updated_plan_tier_details',
                    'data'      => SystemLogLibrary::serializeData($input)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
			} else {
				$admin_logs = array(
                    'admin_id'  => $hr_id,
                    'admin_type' => 'hr',
                    'type'      => 'admin_hr_updated_plan_tier_details',
                    'data'      => SystemLogLibrary::serializeData($input)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
			}
			return array('status' => true, 'message' => 'Plan Tier Updated.');
		}

		return array('status' => false, 'message' => 'Failed to update Plan Tier.');
	}

	public function createWebInputTier( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();
		$plan_tier_id = null;

		if(empty($input['employees']) || sizeof($input['employees']) == 0) {
			return array('satus' => false, 'message' => 'Employee/s is required.');
		}

		$planned = DB::table('customer_plan')
					->where('customer_buy_start_id', $customer_id)
					->orderBy('created_at', 'desc')
					->first();

		// $checkEnrollVacantSeats = $planned->account_type == "lite_plan" || $planned->account_type == "out_of_pocket" ? false : true;
		if($planned->account_type == "stand_alone_plan" || $planned->account_type == "insurance_bundle" || $planned->account_type == "enterprise_plan") {
			$plan_status = DB::table('customer_plan_status')
							->where('customer_plan_id', $planned->customer_plan_id)
							->orderBy('created_at', 'desc')
							->first();

			$total = $plan_status->employees_input - $plan_status->enrolled_employees;


			if($total <= 0) {
				return array(
					'status'	=> FALSE,
					'message'	=> "We realised the current headcount you wish to enroll is over the current vacant member seat/s."
				);
			}

			if(sizeof($input['employees']) > $total) {
				return array(
					'status'	=> FALSE,
					'message'	=> "We realised the current headcount you wish to enroll is over the current vacant member seat/s."
				);
			}

			if($plan_tier_id) {
				$total_left_count = $plan_tier->member_head_count - $plan_tier->member_enrolled_count;

				if(sizeof($input['employees']) > $total_left_count) {
					return array(
						'status'	=> FALSE,
						'message'	=> "Current Member headcount you wish to enroll to this Plan Tier is over the current vacant member seat/s. Your are trying to enroll a total of ".sizeof($input['employees'])." of current total left of ".$total_left_count." for this Plan Tier"
					);
				}
			}
		}

		// get active plan id for member
		$customer_active_plan_id = PlanHelper::getCompanyAvailableActivePlanId($customer_id);

		if(!$customer_active_plan_id) {
			$active_plan = DB::table('customer_active_plan')->where('customer_start_buy_id', $customer_id)->orderBy('created_at', 'desc')->first();
			$customer_active_plan_id = $active_plan->customer_active_plan_id;
		}

		if($planned->account_type == "stand_alone_plan" || $planned->account_type == "insurance_bundle" || $planned->account_type == "enterprise_plan") {
			$total_dependents_entry = 0;
			$total_dependents = 0;
			// check total depedents to be save
			foreach ($input['employees'] as $key => $employee) {
				if(!empty($employee['dependents']) && sizeof($employee['dependents']) > 0) {
					$total_dependents_entry += sizeof($employee['dependents']);
				}
			}

			$dependent_plan_status = DB::table('dependent_plan_status')
									->where('customer_plan_id', $planned->customer_plan_id)
									->orderBy('created_at', 'desc')
									->first();

			if($dependent_plan_status) {
				$total_dependents = $dependent_plan_status->total_dependents - $dependent_plan_status->total_enrolled_dependents;
				if($total_dependents <= 0 && $total_dependents_entry > 0) {
					return array(
						'status'	=> FALSE,
						'message'	=> "We realised the current dependent headcount you wish to enroll is over the current vacant member seat/s."
					);
				}
			} else if(!$dependent_plan_status && $total_dependents_entry > 0){
				return array(
						'status'	=> FALSE,
						'message'	=> "Please purchase a dependent plan to be able to enroll the dependent accounts."
					);
			}

			if($plan_tier_id) {
				if($plan_tier->dependent_head_count > 0) {
					$plan_tier_dependent_total = $plan_tier->dependent_head_count - $plan_tier->dependent_enrolled_count;

					if($total_dependents_entry > $plan_tier_dependent_total) {
						return array(
							'status'	=> FALSE,
							'message'	=> "Current Dependent headcount you wish to enroll to this Plan Tier is over the current vacant member seat/s. Your are trying to enroll a total of ".$total_dependents_entry." of current total left of ".$plan_tier_dependent_total." for this Plan Tier"
						);
					}
				}
			}
		}

		$customer_active_plan = DB::table('customer_active_plan')
									->where('customer_active_plan_id', $customer_active_plan_id)
									->first();

		$format = [];
		$temp_enroll = new TempEnrollment();
		$temp_dependent_enroll = new DependentTempEnrollment();
		$group_number = CustomerHelper::getMemberLastGroupNumber($customer_id);
		foreach ($input['employees'] as $key => $user) {
			$credit = 0;
			$user['mobile_country_code'] = !empty($user['mobile_area_code']) ? trim($user['mobile_area_code']) : null;
			$user['mobile'] = !empty($user['mobile']) ? trim($user['mobile']) : null;
			$user['medical_credits'] = !empty($user['medical_entitlement']) ? $user['medical_entitlement'] : 0;
			$user['wellness_credits'] = !empty($user['wellness_entitlement']) ? $user['wellness_entitlement'] : 0;
			$error_member_logs = PlanHelper::enrollmentEmployeeValidation($user, false);

			$temp_enrollment_data = array(
				'customer_buy_start_id'	=> $customer_id,
				'active_plan_id'		=> $customer_active_plan_id,
				'plan_tier_id'			=> $plan_tier_id,
				'first_name'				=> trim($user['fullname']),
				'dob'					=> trim($user['dob']),
				'email'					=> !empty($user['email']) ? trim($user['email']) : null,
				'mobile'				=> !empty($user['mobile']) ? trim($user['mobile']) : null,
				'mobile_area_code'		=> !empty($user['mobile_area_code']) ? trim($user['mobile_area_code']) : 65,
				'job_title'				=> 'Other',
				'credits'				=> $user['medical_credits'],
				'wellness_credits'		=> $user['wellness_credits'],
				'start_date'			=> $user['plan_start'],
				'medical_balance_entitlement'			=> $user['medical_credits'],
				'wellness_balance_entitlement'			=> $user['wellness_credits'],
				'emp_no'			=> !empty($user['employee_id']) ? $user['employee_id'] : null,
				'bank_name'			=> !empty($user['bank_name']) ? $user['bank_name'] : null,
				'bank_account_number'			=> !empty($user['bank_account_number']) ? $user['bank_account_number'] : null,
				'cap_per_visit'			=> !empty($user['cap_per_visit']) ? $user['cap_per_visit'] : null,
				'postal_code'			=> null,
				'group_number'			=> $group_number,
				'nric' => $user['nric'] ?? null,
				'passport' => $user['passport'] ?? null,
				'error_logs'			=> serialize($error_member_logs)
			);

			try {
				$enroll_result = $temp_enroll->insertTempEnrollment($temp_enrollment_data);

				if($enroll_result) {
					if(!empty($user['dependents']) && sizeof($user['dependents']) > 0) {
						foreach ($user['dependents'] as $key => $dependent) {
							$dependent['plan_start'] = date('Y-m-d', strtotime($dependent['plan_start']));
							$error_dependent_logs = PlanHelper::enrollmentDepedentValidation($dependent);

							// get active plan id for member
							$depedent_plan_id = PlanHelper::getCompanyAvailableDependenPlanId($customer_id);

							if(!$depedent_plan_id) {
								$dependent_plan = DB::table('dependent_plans')
													->where('customer_plan_id', $customer_active_plan->plan_id )
													->orderBy('created_at', 'desc')
													->first();
								$depedent_plan_id = $dependent_plan->dependent_plan_id;
							}

							$temp_enrollment_dependent = array(
								'employee_temp_id'		=> $enroll_result->id,
								'dependent_plan_id'		=> $depedent_plan_id,
								'plan_tier_id'			=> $plan_tier_id,
								'first_name'			=> $dependent['fullname'],
								'dob'					=> $dependent['dob'],
								'plan_start'			=> $dependent['plan_start'],
								'relationship'			=> $dependent['relationship'],
								'error_logs'			=> serialize($error_dependent_logs)
							);

							// array($format, $temp_enrollment_dependent)
							$temp_dependent_enroll->createEnrollment($temp_enrollment_dependent);
						}
					}
				}
			} catch(Exception $e) {
				$email = [];
				$email['end_point'] = url('hr/create/employee_enrollment', $parameter = array(), $secure = null);
				$email['logs'] = 'Save Temp Enrollment - '.$e->getMessage();
				$email['emailSubject'] = 'Error log.';
				return $e;
				EmailHelper::sendErrorLogs($email);
				return array('status' => FALSE, 'message' => 'Failed to create enrollment employee. Please contact Mednefits team.', 'error' => $e);
			}
		}

		return array('status' => true);
	}

	public function getPlanTierEnrollment( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();
		$enroll_users = [];
		$enrolles = DB::table('customer_temp_enrollment')
					->where('customer_buy_start_id', $customer_id)
					->where('enrolled_status', "false")
					->get();

		foreach ($enrolles as $key => $enroll) {
			// if($enroll->dob) {
			// 	$enroll_dob = date_create_from_format("Y-m-d", $enroll->dob);
			// 	if($enroll_dob) {
			// 		$enroll->dob = date_format($enroll_dob, "d/m/Y");
			// 	} else {
			// 		$enroll->dob = date('d/m/Y', strtotime($enroll->dob));
			// 	}
			// } else {
			// 	$enroll->dob = null;
			// }
			$enroll->fullname = $enroll->first_name;
			if($enroll->email == null) {
				$enroll->email = '';
			}

			if($enroll->mobile_area_code) {
				$enroll->format_mobile = "+".$enroll->mobile_area_code.$enroll->mobile;
			} else {
				$enroll->format_mobile = $enroll->mobile;
			}

			$depedents_format = [];
			$dependent_error = false;
			$dependents = DB::table('dependent_temp_enrollment')
							->where('employee_temp_id', $enroll->temp_enrollment_id)
							->get();

			if(sizeof($dependents) > 0) {
				foreach ($dependents as $key => $dep) {
					$error_logs = unserialize($dep->error_logs);
					if($error_logs['error']) {
						$dependent_error = true;
					}

					if(($dep->plan_start == "1970-01-01") || ($dep->plan_start == "0000-00-00") || ($dep->plan_start == null)) {
						$dep->plan_start = null;
					} else {
						$dep->plan_start = date('d/m/Y', strtotime($dep->plan_start));
					}
					if($dep->dob) {
						$dob = date_create_from_format("Y-m-d", $dep->dob);
						if($dob) {
							$dep->dob = date_format($dob,"d/m/Y");
						} else {
							$dep->dob = date('d/m/Y', strtotime($dep->dob));
						}
					} else {
						$dep->dob = null;
					}

					$dep->fullname = $dep->first_name;
					$dep_temp = array(
						'enrollee'		=> $dep,
						'error_logs' 	=> $error_logs
					);
					array_push($depedents_format, $dep_temp);
				}
			}

			$error_logs = unserialize($enroll->error_logs);

			if($error_logs['error']) {
				$error_logs['error'] = true;
			}

			if($dependent_error) {
				$error_logs['error'] = true;
			}
			$temp = array(
				'employee'		=> $enroll,
				'dependents'	=> $depedents_format,
				'error_logs' 	=> $error_logs
			);

			array_push($enroll_users, $temp);
		}

		return array('status' => true, 'data' => $enroll_users);
	}

	public function updatePlanTierEmployeeEnrollment( )
	{
		$input = Input::all();
		$temp_enroll = new TempEnrollment();

		if(empty($input['temp_enrollment_id']) || $input['temp_enrollment_id'] == null) {
			return array('status' => false, 'message' => 'Employee Enrollee ID is required.');
		}

		$check_enrollee = DB::table('customer_temp_enrollment')
							->where('temp_enrollment_id', $input['temp_enrollment_id'])
							->first();

		if(!$check_enrollee) {
			return array('status' => false, 'message' => 'Employee Enrollee does not exist.');
		}

		if(empty($input['fullname']) || $input['fullname'] == null) {
			return array('status' => false, 'message' => 'Employee Full Name is required.');
		}

		$postal_code = null;

		if(!empty($input['postal_code']) && $input['postal_code'] != null) {
			$postal_code = $input['postal_code'];
		}

		$data = array(
			'temp_enrollment_id'		=> $input['temp_enrollment_id'],
			'credits'					=> $input['medical_credits'],
			'wellness_credits'			=> $input['wellness_credits']
		);

		$temp_enroll->updateEnrollee($data);
		// $temp = \DateTime::createFromFormat('d/m/Y', $input['plan_start']);
		// $input['plan_start'] = $temp->format('Y-m-d');
		$input['mobile_country_code'] = $input['mobile_area_code'];
		$error_logs = PlanHelper::enrollmentEmployeeValidation($input, true);
		$mobile = preg_replace('/\s+/', '', $input['mobile']);

		$data = array(
			'temp_enrollment_id'		=> $input['temp_enrollment_id'],
			'first_name'				=> $input['fullname'],
			'dob'						=> $input['dob'],
			'email'						=> $input['email'],
			'mobile'					=> $mobile,
			'nric' 						=> $input['nric'] ?? null,
			'passport' 					=> $input['passport'] ?? null,
			'mobile_area_code'			=> $input['mobile_area_code'],
			'job_title'					=> $input['job_title'],
			'credits'					=> $input['medical_credits'],
			'wellness_credits'			=> $input['wellness_credits'],
			'postal_code'				=> $postal_code,
			'start_date'				=> $input['plan_start'],
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

	public function updatePlanTierDependentEnrollment( )
	{
		$input = Input::all();

		if(empty($input['dependent_temp_id']) || $input['dependent_temp_id'] == null) {
			return array('status' => false, 'message' => 'Dependent Enrollee ID is required.');
		}

		if(empty($input['fullname']) || $input['fullname'] == null) {
			return array('status' => false, 'message' => 'Dependent Enrollee Full Name is required.');
		}

		// if(empty($input['last_name']) || $input['last_name'] == null) {
		// 	return array('status' => false, 'message' => 'Dependent Enrollee Last Name is required.');
		// }

		// if(empty($input['nric']) || $input['nric'] == null) {
		// 	return array('status' => false, 'message' => 'Dependent Enrollee NRIC/FIN is required.');
		// }

		if(empty($input['dob']) || $input['dob'] == null) {
			return array('status' => false, 'message' => 'Dependent Enrollee Date of Birth is required.');
		}

		if(empty($input['dependent_temp_id']) || $input['dependent_temp_id'] == null) {
			return array('status' => false, 'message' => 'Dependent Enrollee ID is required.');
		}

		$check_dependent_enrollee = DB::table('dependent_temp_enrollment')
									->where('dependent_temp_id', $input['dependent_temp_id'])
									->first();

		if(!$check_dependent_enrollee) {
			return array('status' => false, 'message' => 'Dependent Enrollee not found.');
		}

		$dependent_enrollment = new DependentTempEnrollment();
		$plan_start = $input['plan_start'];
		$input['plan_start'] = date('d/m/Y', strtotime($input['plan_start']));

		$error_dependent_logs = PlanHelper::enrollmentDepedentValidation($input);
		$temp_enrollment_dependent = array(
			'dependent_temp_id'		=> $input['dependent_temp_id'],
			'first_name'			=> $input['fullname'],
			// 'last_name'				=> $input['last_name'],
			// 'nric'					=> $input['nric'],
			'dob'					=> $input['dob'],
			'plan_start'			=> $plan_start,
			'relationship'			=> $input['relationship'],
			'error_logs'			=> serialize($error_dependent_logs)
		);

		$result = $dependent_enrollment->updateEnrollement($temp_enrollment_dependent);

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

	public function finishEnrollEmployeeTier( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['temp_enrollment_id']) || $input['temp_enrollment_id'] == null) {
			return array('status' => false, 'message' => 'Employee Enrollee ID is required.');
		}

		$communcation_send = !empty($input['communication_send']) ? $input['communication_send'] : 'immediate';
		$schedule_date = null;

		if($communcation_send == "schedule") {
			if(empty($input['schedule_date']) || $input['schedule_date'] == null) {
				return ['status' => false, 'message' => 'Schedule Date of account activation is required'];
			}

			$schedule_date = date('Y-m-d', strtotime($input['schedule_date']));
		}

		$check_enrollee = DB::table('customer_temp_enrollment')
							->where('temp_enrollment_id', $input['temp_enrollment_id'])
							->where('enrolled_status', 'false')
							->first();


		if(!$check_enrollee) {
			return array('status' => false, 'message' => 'Employee Enrollee does not exist.');
		}

		$error_logs = unserialize($check_enrollee->error_logs);

		if($error_logs['error'] == true) {
			return array('status' => false, 'message' => 'Please fix the Empoyee Enrollee details as it has errors on employee details.');
		}

		$create_user = PlanHelper::createEmployee($input['temp_enrollment_id'], $customer_id, $communcation_send, $schedule_date);
		return array('result' => $create_user);
	}

	public function removeTier( )
	{

		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['plan_tier_id']) || $input['plan_tier_id'] == null) {
			return array('status' => false, 'message' => 'Plan Tier ID is required.');
		}

		$plan_tier = DB::table('plan_tiers')
						->where('plan_tier_id', $input['plan_tier_id'])
						->where('customer_id', $customer_id)
						->first();

		if(!$plan_tier) {
			return array('status' => false, 'message' => 'Plan Tier does not exist.');
		}

		// update plan tier
		DB::table('plan_tiers')->where('plan_tier_id', $input['plan_tier_id'])->update(['active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
		return array('status' => true, 'message' => 'Plan Tier removed');
	}
}
