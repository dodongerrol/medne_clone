<?php
use Illuminate\Support\Facades\Input;

class DependentController extends \BaseController {

	public function getDependentKeys($data)
	{
		$length = sizeof($data);
		$format = [];

		foreach ($data as $key => $value) {
			$temp = strpos($value, 'dependent') !== false;

			if($temp) {
				$format[] = $value;
			}
		}

		return $format;
	}

	public function formatKey($data)
	{
		$text = explode("dependent_", $data);
		$final = substr($text[1], 2);
		if($final) {
			return $final;
		}
	}

	public function uploadExcel( )
	{
		set_time_limit(10000);
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		if(Input::hasFile('file'))
		{
			$file = Input::file('file');

			$extensions = array("xls","xlsx","xlm","xla","xlc","xlt","xlw");
			$result = $file->getClientOriginalExtension();
			if(!in_array($result,$extensions)){
				return array('status' => false, 'message' => 'Invalid File.');
			}

			$temp_file = time().$file->getClientOriginalName();
			$file->move('excel_upload', $temp_file);
			try {
				// $data_array = Excel::selectSheetsByIndex(0)->load(public_path()."/excel_upload/".$temp_file)->formatDates(false)->get();
				$data_array = Excel::selectSheets("Member Information")->load(public_path()."/excel_upload/".$temp_file)->formatDates(false)->get();
			} catch(Exception $e) {
				return ['status' => false, 'message' => "Please use the sheet name 'Member Information'"];
			}

			$headerRow = $data_array->first()->keys();
			
			$temp_users = [];
			$row_keys = self::getDependentKeys($headerRow);
			$dependents_count = count($row_keys) / 5;

			$fullname = false;
			$dob = false;
			$email = false;
			$mobile = false;
			$job = false;
			$credits = false;
			$credit = 0;
			$postal_code = false;
			$medical_credits = false;
			$wellness_credits = false;
			$start_date = false;
			$mobile_area_code = false;

			foreach ($headerRow as $key => $row) {
				if($row == "full_name") {
					$fullname = true;
				} else if($row == "date_of_birth" || $row == "date_of_birth_ddmmyyyy") {
					$dob = true;
				} elseif ($row == "mobile" || $row == "mobile_number") {
					$mobile = true;
				} else if($row == "start_date" || $row == "start_date_ddmmyyyy") {
					$start_date = true;
				} else if($row == "mobile_country_code" || $row == "mobile_country_code" || $row == "country_code") {
					$mobile_area_code = true;
				}
			}

			if(!$fullname || !$dob || !$mobile || !$start_date || !$mobile_area_code) {
				return array(
					'status'	=> FALSE,
					'message' => 'Excel is invalid format. Please download the recommended file for Employee Enrollment.'
				);
			}
			
			foreach ($data_array as $key => $row) {
				$dependents = [];
				$temp_dependents = [];
				if($row->full_name !== null) {
					$dep_ctr = 1;
					$key_ctr = 1;
					foreach ($row_keys as $key_2 => $value_key) {
						foreach ($row as $field => $value) {
							if($field == $value_key) {
								if( $value != null ){
									$data_name = self::formatKey($field);
									if($data_name) {
										$temp_dependents[$data_name] = $value;
										if( $dep_ctr == 3 ){
											if($key_ctr == 3) {
												array_push($dependents, $temp_dependents);
											}
											$temp_dependents = [];
											$dep_ctr = 1;
											$key_ctr = 1;
										}else{
											$dep_ctr+=1;
											if( $value != null ){
												$key_ctr+=1;
											}
										}
									}
								}
							}
						}
					}

					$row['dependents'] = $dependents;
					array_push($temp_users, $row);
				}
			}
			
		  // validate all first
			if(sizeof($temp_users) == 0) {
				return array('satus' => false, 'message' => 'Employee/s is required.');
			}

			$plan_tier_id = null;

			if(!empty($input['plan_tier_id']) && $input['plan_tier_id'] != null) {
				$plan_tier_id = $input['plan_tier_id'];
				$plan_tier = DB::table('plan_tiers')->where('plan_tier_id', $plan_tier_id)->first();

				if(!$plan_tier) {
					return array('satus' => false, 'message' => 'Plan Tier not found.');
				}
			}

				// check employee plan head count status
			$planned = DB::table('customer_plan')
			->where('customer_buy_start_id', $customer_id)
			->orderBy('created_at', 'desc')
			->first();

			$plan_status = DB::table('customer_plan_status')
			->where('customer_plan_id', $planned->customer_plan_id)
			->orderBy('created_at', 'desc')
			->first();

			if($planned->account_type != "lite_plan") {
				$total = $plan_status->employees_input - $plan_status->enrolled_employees;

				if($total <= 0) {
					return array(
						'status'	=> FALSE,
						'message'	=> "We realised the current headcount you wish to enroll is over the current vacant member seat/s."
					);
				}

				if(sizeof($temp_users) > $total) {
					return array(
						'status'	=> FALSE,
						'message'	=> "We realised the current headcount you wish to enroll is over the current vacant member seat/s."
					);
				}

				$total_dependents_entry = 0;
				$total_dependents = 0;

				foreach ($temp_users as $key => $employee) {
					if(!empty($employee['dependents']) && sizeof($employee['dependents']) > 0) {
						$total_dependents_entry += sizeof($employee['dependents']);
					}
				}

				if($plan_tier_id) {
					$total_left_count = $plan_tier->member_head_count - $plan_tier->member_enrolled_count;
					if(sizeof($temp_users) > $total_left_count) {
						return array(
							'status'	=> FALSE,
							'message'	=> "Current Member headcount you wish to enroll to this Plan Tier is over the current vacant member seat/s. Your are trying to enroll a total of ".sizeof($temp_users)." of current total left of ".$total_left_count." for this Plan Tier."
						);
					}

				}

				if($total_dependents_entry > 0) {
					$dependent_plan_status = DB::table('dependent_plan_status')
					->where('customer_plan_id', $planned->customer_plan_id)
					->orderBy('created_at', 'desc')
					->first();
					
					if($dependent_plan_status) {
						$total_dependents = $dependent_plan_status->total_dependents - $dependent_plan_status->total_enrolled_dependents;

						if($total_dependents_entry > $total_dependents) {
							return array(
								'status'	=> FALSE,
								'message'	=> "We realised the current headcount you wish to enroll is over the current vacant dependent seat/s."
							);
						}
					} else if(!$dependent_plan_status && $total_dependents_entry > 0){
						return array('status' => false, 'message' => 'Dependent Plan is currently not available for this Company. Please purchase a dependent plan, contact Mednefits Team for more information.');
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
			}
			
			// get active plan id for member
			$customer_active_plan_id = PlanHelper::getCompanyAvailableActivePlanId($customer_id);
			$customer_active_plan = DB::table('customer_active_plan')
			->where('customer_active_plan_id', $customer_active_plan_id)
			->first();

			$format = [];
			$temp_enroll = new TempEnrollment();
			$temp_dependent_enroll = new DependentTempEnrollment();

			$group_number = CustomerHelper::getMemberLastGroupNumber($customer_id);
		  	// check employee and dependents validation
			foreach ($temp_users as $key => $user) {
				$credit = 0;
				$user['email'] = isset($user['work_email']) ? trim($user['work_email']) : null;
				$user['mobile'] = isset($user['mobile_number']) ? trim($user['mobile_number']) : trim($user['mobile']);
				$user['job_title'] = 'Other';
				$user['fullname'] = $user['full_name'];
				$user['passport'] = isset($user['passport_number']) ? trim($user['passport_number']) : null;
				
				if(isset($user['date_of_birth_ddmmyyyy'])) {
					$dob = $user['date_of_birth_ddmmyyyy'];
				} else {
					$dob = $user['date_of_birth'];
				}
				$dob_format = PlanHelper::validateDate($dob, 'd/m/Y');
				if($dob_format) {
					$user['dob'] = $dob;
				} else {
					$user['dob'] = date('d/m/Y', strtotime($dob));
				}

				if(isset($user['start_date_ddmmyyyy'])) {
					$start_date = $user['start_date_ddmmyyyy'];
				} else {
					$start_date = $user['start_date'];
				}

				$start_date_format = PlanHelper::validateDate($start_date, 'd/m/Y');
				if($start_date_format) {
					$user['plan_start'] = $start_date;
				} else {
					$user['plan_start'] = date('d/m/Y', strtotime($start_date));
				}
				
				$user['medical_credits'] = !isset($user['medical_allocation']) ? 0 : $user['medical_allocation'];
				$user['wellness_credits'] = !isset($user['wellness_allocation']) ? 0 : $user['wellness_allocation'];
				$user['cap_per_visit'] = isset($user['cap_per_visit']) && is_numeric($user['cap_per_visit']) ? $user['cap_per_visit'] : 0;
				$user['bank_name'] = !isset($user['bank_name']) ? 0 : $user['bank_name'];
				$user['bank_account_number'] = !isset($user['bank_account_number']) ? 0 : $user['bank_account_number'];
				$user['mobile_country_code'] = isset($user['mobile_country_code']) ? $user['mobile_country_code'] : $user['country_code'];
				$error_member_logs = PlanHelper::enrollmentEmployeeValidation($user, false);
				$mobile = preg_replace('/\s+/', '', $user['mobile']);

				$temp_enrollment_data = array(
					'customer_buy_start_id'	=> $customer_id,
					'active_plan_id'		=> $customer_active_plan_id,
					'plan_tier_id'			=> $plan_tier_id,
					'first_name'			=> trim($user['fullname']),
					'nric'					=> isset($user['nric']) ? trim($user['nric']) : null,
					'passport'				=> isset($user['passport']) ? trim($user['passport']) : null,
					'dob'					=> $user['dob'],
					'email'					=> $user['email'],
					'emp_no'				=> trim($user['employee_id']),
					'mobile'				=> (int)$mobile,
					'mobile_area_code'		=> trim($user['mobile_country_code']),
					'job_title'				=> $user['job_title'],
					'bank_name'				=> $user['bank_name'],
					'bank_account_number'	=> $user['bank_account_number'],
					'cap_per_visit'			=> $user['cap_per_visit'],
					'credits'				=> !isset($user['medical_allocation']) || $user['medical_allocation'] == null ? 0 : $user['medical_allocation'],
					'medical_balance_entitlement'				=> !isset($user['medical_allocation']) || $user['medical_allocation'] == null ? 0 : $user['medical_allocation'],
					'wellness_credits'		=> !isset($user['wellness_allocation']) || $user['wellness_allocation'] == null ? 0 : $user['wellness_allocation'],
					'wellness_balance_entitlement'				=> !isset($user['wellness_allocation']) || $user['wellness_allocation'] == null ? 0 : $user['wellness_allocation'],
					'postal_code'			=> null,
					'start_date'			=> $user['plan_start'],
					'group_number'			=> $group_number,
					'error_logs'			=> serialize($error_member_logs)
				);
				
				try {
					$enroll_result = $temp_enroll->insertTempEnrollment($temp_enrollment_data);
					if($enroll_result) {
						if(!empty($user['dependents']) && sizeof($user['dependents']) > 0) {
							foreach ($user['dependents'] as $key => $dependent) {
								$plan_start = \DateTime::createFromFormat('d/m/Y', $user['plan_start']);
								$dependent['plan_start'] = $plan_start->format('Y-m-d');
								$dependent['dob'] = date('Y-m-d', strtotime($dependent['date_of_birth']));
								$dependent['relationship'] = strtolower($dependent['relationship']);
								$dependent['fullname'] = $dependent['full_name'];
								$error_dependent_logs = PlanHelper::enrollmentDepedentValidation($dependent);
									// get active plan id for member
								$depedent_plan_id = PlanHelper::getCompanyAvailableDependenPlanId($customer_id);

								if(!$depedent_plan_id) {
									$dependent_plan = DB::table('dependent_plans')
									->where('customer_plan_id', $customer_active_plan->plan_id)
									->orderBy('created_at', 'desc')
									->first();
									$depedent_plan_id = $dependent_plan->dependent_plan_id;
								}

								$dob = \DateTime::createFromFormat('d/m/Y', $dependent['date_of_birth']);
								$dependent['dob'] = $dob->format('Y-m-d');

								$temp_enrollment_dependent = array(
									'employee_temp_id'		=> $enroll_result->id,
									'dependent_plan_id'		=> $depedent_plan_id,
									'plan_tier_id'			=> $plan_tier_id,
									'first_name'			=> trim($dependent['fullname']),
									'dob'					=> $dependent['dob'],
									'nric'					=> null,
									'plan_start'			=> $dependent['plan_start'],
									'relationship'			=> trim($dependent['relationship']),
									'error_logs'			=> serialize($error_dependent_logs)
								);
								$temp_dependent_enroll->createEnrollment($temp_enrollment_dependent);
							}
						}
					}
				} catch(Exception $e) {
					$email = [];
					$email['end_point'] = url('upload_excel_dependents', $parameter = array(), $secure = null);
					$email['logs'] = 'Save Temp Enrollment Excel - '.$e;
					$email['emailSubject'] = 'Error log.';
					EmailHelper::sendErrorLogs($email);
					return array('status' => FALSE, 'message' => 'Failed to create enrollment employee. Please contact Mednefits team.', 'res' => $temp_enrollment_data, 'e' => $e->getMessage());
				}

				array_push($format, $temp_enrollment_data);
			}

			return array('status' => true);
		}

		return array('status' => false, 'message' => 'Please provide the Excel File for Enrollment.');
	}

	public function getDepdentsCount( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		$plan = DB::table('customer_plan')
		->where('customer_buy_start_id', $customer_id)
		->orderBy('created_at', 'desc')
		->first();

		if($plan) {
			$dependents = DB::table('dependent_plan_status')
			->where('customer_plan_id', $plan->customer_plan_id)
			->orderBy('created_at', 'desc')
			->first();

			if($dependents) {
				return array('status' => true, 'total_number_of_seats' => $dependents->total_dependents, 'occupied_seats' => $dependents->total_enrolled_dependents, 'vacant_seats' => $dependents->total_dependents - $dependents->total_enrolled_dependents);
			}
		}

		return array('status' => false);
	}

	// public function updateDependentDetails( )
	// {
	// 	$input = Input::all();
	// 	$customer_id = PlanHelper::getCusomerIdToken();

	// 	$update = array(
	// 		'Name'				=> $input['name'],
	// 		// 'NRIC'				=> $input['nric'],
	// 		'Email'				=> $input['email'],
	// 		'PhoneNo'			=> $input['phone_no'],
	// 		'DOB'				=> $input['dob'],
	// 		'Job_Title'			=> $input['job_title']
	// 	);

	// 	try {
	// 		$user = DB::table('user')->where('UserID', $input['user_id'])->update($update);
	// 		return array(
	// 			'status'	=> TRUE,
	// 			'message' => 'Success.'
	// 		);
	// 	} catch (Exception $e) {
	// 		return array(
	// 			'status'	=> FALSE,
	// 			'message' => 'Failed.',
	// 			'reason'	=> var_dump($e)
	// 		);
	// 	}
	// }

	public function createDependentAccount( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_data = StringHelper::getJwtHrSession();
		$hr_id = $hr_data->hr_dashboard_id;

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

		if(empty($input['dependents']) || $input['dependents'] == null || sizeof($input['dependents']) == 0) {
			return array('status' => false, 'message' => 'Dependents data is required.');
		}

		// get dependent status
		$planned = DB::table('customer_plan')
		->where('customer_buy_start_id', $customer_id)
		->orderBy('created_at', 'desc')
		->first();

		if($planned->account_type != "lite_plan") {
			$dependent_plan_status = DB::table('dependent_plan_status')
			->where('customer_plan_id', $planned->customer_plan_id)
			->orderBy('created_at', 'desc')
			->first();
			$total_dependents = 0;
			if($dependent_plan_status) {
				$total_dependents = $dependent_plan_status->total_dependents - $dependent_plan_status->total_enrolled_dependents;
			} else {
				return array(
					'status'	=> false,
					'message'	=> "This Company does not have a Dependent Purchase. Please request a Dependent Plan Purchase to enable dependent accounts."
				);
			}

			if($total_dependents <= 0) {
				return array(
					'status'	=> false,
					'message'	=> "We realised the current dependent headcount you wish to enroll is over the current vacant member seat/s."
				);
			} else if(sizeof($input['dependents']) > $total_dependents) {
				return array(
					'status'	=> false,
					'message'	=> "We realised the current dependent headcount you wish to enroll is over the current vacant member seat/s."
				);
			}
		} else {
			$dependent_plan_status = DB::table('dependent_plan_status')
			->where('customer_plan_id', $planned->customer_plan_id)
			->orderBy('created_at', 'desc')
			->first();

			if(!$dependent_plan_status) {
				return ['status' => false, 'message' => 'Unable to create dependent account as company has no dependent account purchased.'];
			}
		}

		// // get plan tier if employee has a plan tier
		// $plan_tier = DB::table('plan_tier_users')
		// ->join('plan_tiers', 'plan_tiers.plan_tier_id', '=', 'plan_tier_users.plan_tier_id')
		// ->where('plan_tier_users.user_id', $input['employee_id'])
		// ->where('plan_tiers.active', 1)
		// ->first();

		$plan_tier_status = false;
		$plan_tier_id = null;
		// if($plan_tier) {
		// 	$plan_tier_status = true;
		// 	$plan_tier_id = $plan_tier->plan_tier_id;
		// 	// check plan tier enrollment count
		// 	$vacant_seats = $plan_tier->dependent_head_count - $plan_tier->dependent_enrolled_count;
		// 	if(sizeof($input['dependents']) > $vacant_seats) {
		// 		return array('status' => 'We realised the current dependent headcount you wish to enroll is over the current vacant member seat/s of this Plan Tier.');
		// 	}
		// }

		// validations for dependents input
		// foreach ($input['dependents'] as $key => $validation) {
		// }

		$dependent_plan_id = PlanHelper::getCompanyAvailableDependentPlanId($customer_id);
		// return $dependent_plan_id;
		if(!$dependent_plan_id) {
			$dependent_plan = DB::table('dependent_plans')
			->where('customer_plan_id', $planned->customer_plan_id)
			->orderBy('created_at', 'desc')
			->first();
			$dependent_plan_id = $dependent_plan->dependent_plan_id;
		}

		// get package group
		$package_group = PlanHelper::getDependentPackageGroup($dependent_plan_id);
		$plan_tier_user = new PlanTierUsers();
		$plan_tier_class = new PlanTier();
		$dependent_plan_status = new DependentPlanStatus( );

		foreach ($input['dependents'] as $key => $dependent) {
			// process dependent creation
			$user = array(
				'fullname'	=> $dependent['fullname'],
				'nric'			=> null,
				'dob'			=> date('Y-m-d', strtotime($dependent['dob']))
			);

			$user_id = PlanHelper::createDependentAccountUser($user);
			if($user_id) {
				// crete family coverage date
				$family = array(
					'owner_id'		=> $input['employee_id'],
					'user_id'		=> $user_id,
					'user_type'		=> 'dependent',
					'relationship'	=> $dependent['relationship'],
					'created_at'	=> date('Y-m-d H:i:s'),
					'updated_at'	=> date('Y-m-d H:i:s')
				);

				$family_result = DB::table('employee_family_coverage_sub_accounts')->insert($family);
				if($family_result) {
					$dependent_plan = DB::table('dependent_plans')->where('dependent_plan_id', $dependent_plan_id)->first();
					$user['family_data'] = $family;
					$history = array(
						'user_id'			=> $user_id,
						'dependent_plan_id'	=> $dependent_plan_id,
						'package_group_id'	=> $package_group->package_group_id,
						'plan_start'		=> date('Y-m-d', strtotime($dependent['start_date'])),
						'duration'			=> '12 months',
						'fixed'				=> 1,
						'created_at'	=> date('Y-m-d H:i:s'),
						'updated_at'	=> date('Y-m-d H:i:s')
					);

					DB::table('dependent_plan_history')->insert($history);
					$user['dependent_history'] = $history;
					// check if their is a plan tier id
					if($plan_tier_id) {
						$tier_history = array(
							'plan_tier_id'              => $plan_tier_id,
							'user_id'                   => $user_id,
							'status'                    => 1
						);

						$plan_tier_user->createData($tier_history);
                        // increment member head count
						$plan_tier_class->increamentDependentEnrolledHeadCount($plan_tier_id);
					}

					if($admin_id) {
						$user['user_id'] = $user_id;
						$admin_logs = array(
							'admin_id'  => $admin_id,
							'admin_type' => 'mednefits',
							'type'      => 'admin_hr_created_dependent',
							'data'      => SystemLogLibrary::serializeData($user)
						);
						SystemLogLibrary::createAdminLog($admin_logs);
					} else {
						$user['user_id'] = $user_id;
						$admin_logs = array(
							'admin_id'  => $hr_id,
							'admin_type' => 'hr',
							'type'      => 'admin_hr_created_dependent',
							'data'      => SystemLogLibrary::serializeData($user)
						);
						SystemLogLibrary::createAdminLog($admin_logs);
					}

					$dependent_plan_status->incrementEnrolledDependents($planned->customer_plan_id);
					// record enrollment status for member
					PlanHelper::createEnrollmentHistoryStatus($user_id, $dependent_plan->customer_active_plan_id, date('Y-m-d'), $history['plan_start'], null, "immediate", "dependent");
				}
			}
		}

		return array('status' => true, 'message' => 'Dependent Account successfully created.');
	}

	public function getEmployeeDependents( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();
		// return $customer_id;
		// if(!$customer_id['status']) {
		// 	return array('status' => false, 'message' => $customer_id['message']);
		// }
		// $customer_id = $input['customer_id'];

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

		$format = [];

		$dependents = DB::table('employee_family_coverage_sub_accounts')
		->join('user', 'user.UserID', '=', 'employee_family_coverage_sub_accounts.user_id')
		->where('employee_family_coverage_sub_accounts.owner_id', $input['employee_id'])
		->where('user.Active', 1)
		->where('employee_family_coverage_sub_accounts.deleted', 0)
		->get();

		$today = date('Y-m-d');

		foreach ($dependents as $key => $dependent) {
			// check for dependent withdraw
			$deletion = false;
			$deletion_text = null;
			$withdraw = DB::table('dependent_plan_withdraw')
			->where('user_id', $dependent->user_id)
			->first();

			if($withdraw) {
				$deletion = true;
				$deletion_text = 'Dependent is Schedule for Dependent Plan Withdrawal this '.date('F d, Y', strtotime($withdraw->date_withdraw));
			}

			$replace = DB::table('customer_replace_dependent')
			->where('old_id', $dependent->user_id)
			->first();

			if($replace) {
				$deletion = true;
				$deletion_text = 'Dependent is Schedule for Dependent Account Replacement this '.date('F d, Y', strtotime($replace->expired_date));
			}

			$seat = DB::table('dependent_replacement_seat')
			->where('user_id', $dependent->user_id)
			->first();

			if($seat) {
				$deletion = true;
				$deletion_text = 'Dependent is Schedule for Dependent Account Seat Replacement this '.date('F d, Y', strtotime($seat->last_date_of_coverage));
			}

			$dependent->deletion = $deletion;
			$dependent->deletion_text = $deletion_text;

			$dependent->relationship = $dependent->relationship ? $dependent->relationship : 'Dependent';


			if($dependent->DOB == null) {
				$dependent->dob = null;
			} else {
				$dependent->dob = date('Y-m-d', strtotime($dependent->DOB));
			}

			$dependent->name = $dependent->Name;
			// $dependent->nric = $dependent->NRIC;
			$dependent->member_id = str_pad($dependent->UserID, 6, "0", STR_PAD_LEFT);

			// dependent plan history
			$history = DB::table('dependent_plan_history')
							->where('user_id', $dependent->user_id)
							->where('type', 'started')
							->orderBy('created_at', 'desc')
							->first();
			
			$history->total_balance_visit = $history->total_visit_limit - $history->total_visit_created;
			$history->formatted_plan_start = date('F d, Y', strtotime($history->plan_start));
			$dependent->nric = $dependent->NRIC;
			$dependent->member_id = str_pad($dependent->UserID, 6, "0", STR_PAD_LEFT);
	

		$dependent_plan = DB::table('dependent_plans')->where('dependent_plan_id', $history->dependent_plan_id)->first();
		$plan = DB::table('customer_plan')->where('customer_plan_id', $dependent_plan->customer_plan_id)->orderBy('created_at', 'desc')->first();

		$active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
		$data['start_date'] = date('d F Y', strtotime($history->plan_start));
		
		$dependent_plan_extenstion = DB::table('dependent_plans')->where('customer_plan_id', $dependent_plan->customer_plan_id)->where('type', 'extension_plan')->first();

		if($dependent_plan_extenstion && $dependent_plan_extenstion->activate_plan_extension == 1) {
			$temp_valid_date = date('Y-m-d', strtotime('+'.$dependent_plan_extenstion->duration, strtotime($dependent_plan_extenstion->plan_start)));
			$dependent->plan_end_date = date('Y-m-d', strtotime('-1 day', strtotime($temp_valid_date)));
		} else {
			if((int)$history->fixed == 1) {
				$temp_valid_date = date('Y-m-d', strtotime('+'.$active_plan->duration, strtotime($plan->plan_start)));
				$dependent->plan_end_date = date('Y-m-d', strtotime('-1 day', strtotime($temp_valid_date)));
			} else if((int)$history->fixed == 0) {
				$dependent->plan_end_date = date('Y-m-d', strtotime('+'.$plan_user->duration, strtotime($history->plan_start)));
			}
		}


		$history->formatted_plan_end = date('F d, Y', strtotime($dependent->plan_end_date));
		$dependent->dependent_plan_history = $history;
		if($today > $dependent->plan_end_date) {
			$dependent->plan_expired_status = true;
		} else {
			$dependent->plan_expired_status = false;
		}
			}

			return array('status' => true, 'dependents' => $dependents);
		}

	public function updateDependentDetails( )
	{
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_data = StringHelper::getJwtHrSession();
		$hr_id = $hr_data->hr_dashboard_id;
		$input = Input::all();
		// $customer_id = PlanHelper::getCusomerIdToken();

		$check_dependent = DB::table('user')
		->where('UserID', $input['user_id'])
		->where('UserType', 5)
		->where('access_type', 2)
		->first();

		if(!$check_dependent) {
			return array('status' => false, 'message' => 'Dependent does not exist.');
		}

		if(empty($input['name']) || $input['name'] == null) {
			return array('status' => false, 'message' => 'Dependent Full Name is required.');
		}

		// if(empty($input['last_name']) || $input['last_name'] == null) {
		// 	return array('status' => false, 'message' => 'Dependent Last Name is required.');
		// }

		if(empty($input['dob']) || $input['dob'] == null) {
			return array('status' => false, 'message' => 'Dependent Date of Birth is required.');
		}

		if(empty($input['relationship']) || $input['relationship'] == null) {
			return array('status' => false, 'message' => 'Dependent Relationship is required.');
		}

		$dob_format = PlanHelper::validateDate($input['dob'], 'd/m/Y');
		if($dob_format) {
			// $user['dob'] = date('d/m/Y', strtotime($dob));
			$input['dob'] = $input['dob'];
		} else {
			// $user['dob'] = $dob;
			$input['dob'] = date('d/m/Y', strtotime($input['dob']));
		}

		$dob = \DateTime::createFromFormat('d/m/Y', $input['dob']);
		$dob = $dob->format('Y-m-d');
		// update profile
		$profile_data = array(
			'Name'	=> ucwords($input['name']),
			'DOB'	=> $dob
		);

		$profile = DB::table('user')->where('UserID', $input['user_id'])->update($profile_data);
		$dependent = DB::table('employee_family_coverage_sub_accounts')
		->where('user_id', $input['user_id'])
		->update(['relationship' => $input['relationship']]);

		if($admin_id) {
			$admin_logs = array(
				'admin_id'  => $admin_id,
				'admin_type' => 'mednefits',
				'type'      => 'admin_hr_updated_dependent_details',
				'data'      => SystemLogLibrary::serializeData($input)
			);
			SystemLogLibrary::createAdminLog($admin_logs);
		} else {
			$admin_logs = array(
				'admin_id'  => $hr_id,
				'admin_type' => 'hr',
				'type'      => 'admin_hr_updated_dependent_details',
				'data'      => SystemLogLibrary::serializeData($input)
			);
			SystemLogLibrary::createAdminLog($admin_logs);
		}

		return array('status' => true, 'message' => 'Dependent Profile updated.');
	}

	public function replaceDependent( )
	{
		$input = Input::all();
		$user = new User();
		// $result = self::checkSession();
		// $id = $input['customer_id'];
		$id = PlanHelper::getCusomerIdToken();

		if(empty($input['replace_id']) || $input['replace_id'] == null) {
			return array('status' => false, 'message' => 'Replace ID is required.');
		}

		$replace_id = $input['replace_id'];
		// check if employee exit
		$employee = DB::table('user')
		->where('UserID', $replace_id)
		->where('UserType', 5)
		->first();

		if(!$employee) {
			return array('status' => false, 'message' => 'Account does not exist.');
		}


		$type = PlanHelper::getUserAccountType($input['replace_id']);
		if($type != "dependent") {
			return array('status' => false, 'message' => 'Replace ID is not a Dependent Account.');
		}

		if(empty($input['fullname']) || $input['fullname'] == null) {
			return array('status' => false, 'message' => 'Full Name is required.');
		}

		// if(empty($input['last_name']) || $input['last_name'] == null) {
		// 	return array('status' => false, 'message' => 'Last Name is required.');
		// }

		// if(empty($input['nric']) || $input['nric'] == null) {
		// 	return array('status' => false, 'message' => 'NRIC/FIN is required.');
		// }


		if(empty($input['dob']) || $input['dob'] == null) {
			return array('status' => false, 'message' => 'Date of Birth is required.');
		}

		if(empty($input['relationship']) || $input['relationship'] == null) {
			return array('status' => false, 'message' => 'Relationship is required.');
		}


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

		$owner_id = PlanHelper::getDependentOwnerID($replace_id);
		if(strtolower($input['relationship']) == "spouse") {
			$chek_spouse_type = PlanHelper::checkSpouseDependent($owner_id);
			if($chek_spouse_type) {
				return array('status' => false, 'message' => 'Dependent Spouse is already taken.');
			}
		}

		$status_account = PlanHelper::checkDependentRemovalStatus($replace_id);

		if($status_account['status']) {
			return array('status' => false, 'message' => $status_account['message']);
		}

		// check last day of coverage and plan start
		$last_day_of_coverage = date('Y-m-d', strtotime($input['last_day_coverage']));
		$plan_start = date('Y-m-d', strtotime($input['plan_start']));


		if($last_day_of_coverage == $plan_start) {
			// return "replace";
			$result = PlanHelper::createReplacementDependent($replace_id, $input);
			if($result) {
				return array('status' => true, 'message'	=> 'Success');
			} else {
				return array('status' => false, 'message' => 'Unable to create new employee. Please contact Mednefits Team for assistance.');
			}
		} else {
			// return "schedule";
			// schedule employee replacement
			$plan_tier_id = null;
			$dependent_plan = DB::table('dependent_plan_history')
			->where('user_id', $replace_id)
			->where('type', 'started')
			->orderBy('created_at', 'desc')
			->first();

			$plan_tier_user = new PlanTierUsers();
			$dependent_replace = new CustomerReplaceDependent();

			if(date('Y-m-d') >= $last_day_of_coverage) {
				$depent_plan_history = new DependentPlanHistory();
				$user_plan_history_data = array(
					'user_id'       => $replace_id,
					'type'          => "deleted_expired",
					'plan_start'          => $last_day_of_coverage,
					'dependent_plan_id' => $dependent_plan->dependent_plan_id,
					'duration'      => $dependent_plan->duration,
					'package_group_id' => $dependent_plan->package_group_id,
					'fixed'         => $dependent_plan->fixed
				);


				$result = $depent_plan_history->createData($user_plan_history_data);
				
				if(!$result) {
					return false;
				}

				$user_data = array(
					'Active'    => 0
				);
                // update user and set to inactive
				DB::table('user')->where('UserID', $replace_id)->update($user_data);
                // set company members removed to 1
				DB::table('employee_family_coverage_sub_accounts')
				->where('user_id', $replace_id)
				->update(['deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);

				if(date('Y-m-d') >= $plan_start) {
	                // create new Dependent Account
					$user = array(
						'first_name'    => $input['first_name'],
						'last_name'     => $input['last_name'],
						'nric'          => $input['nric'],
						'dob'           => date('Y-m-d', strtotime($input['dob']))
					);

					$user_id = PlanHelper::createDependentAccountUser($user);
					if($user_id)
					{
						$family = array(
							'owner_id'      => $owner_id,
							'user_id'       => $user_id,
							'user_type'     => 'dependent',
							'relationship'  => $input['relationship'],
							'created_at'    => date('Y-m-d H:i:s'),
							'updated_at'    => date('Y-m-d H:i:s')
						);
						$family_result = DB::table('employee_family_coverage_sub_accounts')->insert($family);
						if($family_result) {
							$history = array(
								'user_id'           => $user_id,
								'dependent_plan_id' => $dependent_plan->dependent_plan_id,
								'package_group_id'  => $dependent_plan->package_group_id,
								'plan_start'        => date('Y-m-d', strtotime($input['plan_start'])),
								'duration'          => '12 months',
								'fixed'             => 1,
								'created_at'    => date('Y-m-d H:i:s'),
								'updated_at'    => date('Y-m-d H:i:s')
							);

							DB::table('dependent_plan_history')->insert($history);
							$plan_tier = DB::table('plan_tier_users')
							->join('plan_tiers', 'plan_tiers.plan_tier_id', '=', 'plan_tier_users.plan_tier_id')
							->where('plan_tier_users.user_id', $replace_id)
							->where('plan_tiers.active', 1)
							->first();
							if($plan_tier) {
								$plan_tier_id = $plan_tier->plan_tier_id;
	                            // update replace plan tier id
								DB::table('plan_tier_users')
								->where('plan_tier_user_id', $plan_tier->plan_tier_user_id)
								->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
	                            // create new plan tier user
								$plan_tier_user_data = array(
									'plan_tier_id'  => $plan_tier_id,
									'user_id'       => $user_id,
									'status'        => 1,
									'created_at'    => date('Y-m-d H:i:s'),
									'updated_at'    => date('Y-m-d H:i:s')
								);
								$plan_tier_user->createData($plan_tier_user_data);
							}

	                        // create replacement date
							$replace_data = array(
								'old_id'                => $replace_id,
								'new_id'                => $user_id,
								'dependent_plan_id'     => $dependent_plan->dependent_plan_id,
								'plan_tier_id'          => $plan_tier_id,
								'expired_date'          => $last_day_of_coverage,
								'deactivate_dependent_status' => 1,
								'replace_status'        => 1,
								'relationship'          => $input['relationship'],
								'postal_code'			=> null
							);

							$result = $dependent_replace->createReplacement($replace_data);
							if($result) {
								return array('status' => true, 'message' => 'Dependent Account successfully replaced.');
							}
						}
					}
				} else {
					$replace_data = array(
						'old_id'                => $replace_id,
						'dependent_plan_id'     => $dependent_plan->dependent_plan_id,
						'expired_date'          => $last_day_of_coverage,
						'start_date'            => date('Y-m-d', strtotime($input['plan_start'])),
						'status'                => 0,
						'replace_status'        => 0,
						'deactivate_dependent_status' => 1,
						'first_name'            => $input['first_name'],
						'last_name'             => $input['last_name'],
						'nric'                  => $input['nric'],
						'dob'                   => date('Y-m-d', strtotime($input['dob'])),
						'relationship'          => $input['relationship'],
						'postal_code'			=> null
					);

					$result = $dependent_replace->createReplacement($replace_data);

					if($result) {
						return array('status' => true, 'message' => 'Old Dependent Account is deactivated and new dependent account will be activated by '.date('d F Y', strtotime($input['plan_start'])));
					}
				}
				
			} else {
				$replace_data = array(
					'old_id'                => $replace_id,
					'dependent_plan_id'     => $dependent_plan->dependent_plan_id,
					'expired_date'          => $last_day_of_coverage,
					'start_date'            => date('Y-m-d', strtotime($input['plan_start'])),
					'status'                => 0,
					'replace_status'        => 0,
					'deactivate_dependent_status' => 0,
					'first_name'            => $input['first_name'],
					'last_name'             => $input['last_name'],
					'nric'                  => $input['nric'],
					'dob'                   => date('Y-m-d', strtotime($input['dob'])),
					'relationship'          => $input['relationship'],
					'postal_code'			=> null
				);

				$result = $dependent_replace->createReplacement($replace_data);

				if($result) {
					return array('status' => true, 'message' => 'Old Dependent Account will be deactivated by '.date('d F Y', strtotime($input['last_day_coverage'])).' and new dependent account will be activated by '.date('d F Y', strtotime($input['plan_start'])));
				}
			}
		}

		return array(
			'status'	=> TRUE,
			'message'	=> 'Success'
		);
	}

	public function createDependentVacantSeat( )
	{
		$input = Input::all();
		$customer_id = $input['customer_id'];
		// $customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['user_id']) || $input['user_id'] == null) {
			return array('status' => false, 'message' => 'User ID is required.');
		}


		if(empty($input['last_date_of_coverage']) || $input['last_date_of_coverage'] == null) {
			return array('status' => false, 'message' => 'Last Day Of Employee Coverage is required.');
		}

		// check if employee exit
		$employee = DB::table('user')
		->where('UserID', $input['user_id'])
		->where('UserType', 5)
		->first();

		if(!$employee) {
			return array('status' => false, 'message' => 'Account does not exist.');
		}


		$type = PlanHelper::getUserAccountType($input['user_id']);
		if($type != "dependent") {
			return array('status' => false, 'message' => 'User ID is not a Dependent Account.');
		}

		$validate_last_day_of_coverage = PlanHelper::validateStartDate($input['last_date_of_coverage']);

		if(!$validate_last_day_of_coverage) {
			return array('status' => false, 'message' => 'Last Day of Coverage should be a date.');
		}

		$status_account = PlanHelper::checkDependentRemovalStatus($input['user_id']);

		if($status_account['status']) {
			return array('status' => false, 'message' => $status_account['message']);
		}

		// $dependent_plan = DB::table('dependent_plan_history')
		// ->where('user_id', $input['user_id'])
		// ->where('type', 'started')
		// ->orderBy('created_at', 'desc')
		// ->first();

  //       // create replace dependent seat
		// $data = array(
		// 	'user_id'			=> $input['user_id'],
		// 	'customer_id'		=> $customer_id,
		// 	'date_enrollment'	=> date('Y-m-d', strtotime($input['date_enrollment'])),
		// 	'last_date_of_coverage'	=> date('Y-m-d', strtotime($input['last_date_of_coverage'])),
		// 	'created_at'		=> date('Y-m-d H:i:s'),
		// 	'updated_at'		=> date('Y-m-d H:i:s'),
		// 	'dependent_plan_id' => $dependent_plan->dependent_plan_id
		// );

		// $result = DB::table('dependent_replacement_seat')->insert($data);

		$refund_status = PlanHelper::checkEmployeePlanRefundType($input['user_id']);
		$expiry = date('Y-m-d', strtotime($input['last_date_of_coverage']));
		$date = date('Y-m-d');

		if($date >= $expiry) {
			// create refund and delete now
			try {
				$result = PlanHelper::removeDependent($input['user_id'], $expiry, $refund_status, true);
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
				$result = PlanHelper::createDependentWithdraw($input['user_id'], $expiry, true, $refund_status, true);
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

		if($result) {
			return array('status' => true, 'message' => 'Successfully created Vacant Seat for future hire.');
		} else {
			return array('status' => false, 'message' => 'Unable to create Vacant Seat for future hire.');
		}
		return "yeah";
	}

	public function checkVacantDependentSeat( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();


		if(empty($input['dependent_replacement_seat_id']) || $input['dependent_replacement_seat_id'] == null) {
			return array('status' => false, 'message' => 'Dependent Vacant seat ID is required.');
		}

		$seat = DB::table('dependent_replacement_seat')
		->where('dependent_replacement_seat_id', $input['dependent_replacement_seat_id'])
		->where('customer_id', $customer_id)
		->first();

		if(!$seat) {
			return array('status' => false, 'message' => 'Dependent Vacant Seat does not exists');
		}

		if((int)$seat->vacant_status == 1) {
			return array('status' => false, 'message' => 'Dependent Vacant Seat already occupied');	
		}
		
		$dependent = DB::table('employee_family_coverage_sub_accounts')
		->where('user_id', $seat->user_id)
		->first();
		$employee = DB::table('user')
		->where('UserID', $dependent->owner_id)
		->first();

		return array('status' => true, 'employee' => ucwords($employee->Name));
	}

	public function enrollDependent( )
	{
		$input = Input::all();
		// $customer_id = $input['customer_id'];
		$customer_id = PlanHelper::getCusomerIdToken();

		$seat = DB::table('dependent_replacement_seat')
		->where('dependent_replacement_seat_id', $input['dependent_replacement_seat_id'])
		->first();

		if(!$seat) {
			return array('status' => false, 'message' => 'Dependent Vacant seat does not exist');
		}

		if((int)$seat->vacant_status == 1) {
			return array('status' => false, 'message' => 'Dependent Vacant Seat already occupied');	
		}

		if(empty($input['first_name']) || $input['first_name'] == null) {
			return array('status' => false, 'message' => 'Dependent First Name is required.');
		}

		if(empty($input['last_name']) || $input['last_name'] == null) {
			return array('status' => false, 'message' => 'Dependent Last Name is required.');
		}

		if(empty($input['nric']) || $input['nric'] == null) {
			return array('status' => false, 'message' => 'Dependent NRIC/FIN is required.');
		}

		if(empty($input['dob']) || $input['dob'] == null) {
			return array('status' => false, 'message' => 'Dependent Date of Birth is required.');
		}

		if(empty($input['relationship']) || $input['relationship'] == null) {
			return array('status' => false, 'message' => 'Dependent Relationship is required.');
		}

		if(empty($input['start_date']) || $input['start_date'] == null) {
			return array('status' => false, 'message' => 'Dependent Start Date is required.');
		}

		$owner_id = PlanHelper::getDependentOwnerID($seat->user_id);

		if(strtolower($input['relationship']) == "spouse") {
			$check = PlanHelper::checkSpouseDependent($owner_id);

			if($check) {
				return array('status' => false, 'message' => 'Relationship Type Spouse is already taken.');
			}
		}

		$user = array(
			'first_name'	=> $input['first_name'],
			'last_name'		=> $input['last_name'],
			'nric'			=> $input['nric'],
			'dob'			=> date('Y-m-d', strtotime($input['dob']))
		);

		$plan_tier_user = new PlanTierUsers();

		$user_id = PlanHelper::createDependentAccountUser($user);
		if($user_id) {
			// crete family coverage date
			$family = array(
				'owner_id'		=> $owner_id,
				'user_id'		=> $user_id,
				'user_type'		=> 'dependent',
				'relationship'	=> strtolower($input['relationship']),
				'created_at'	=> date('Y-m-d H:i:s'),
				'updated_at'	=> date('Y-m-d H:i:s')
			);

			$family_result = DB::table('employee_family_coverage_sub_accounts')->insert($family);
			if($family_result) {
				$dependent_plan = DB::table('dependent_plan_history')
				->where('user_id', $seat->user_id)
				->where('type', 'started')
				->orderBy('created_at', 'desc')
				->first();
				$history = array(
					'user_id'			=> $user_id,
					'dependent_plan_id'	=> $dependent_plan->dependent_plan_id,
					'package_group_id'	=> $dependent_plan->package_group_id,
					'plan_start'		=> date('Y-m-d', strtotime($input['start_date'])),
					'duration'			=> '12 months',
					'fixed'				=> 1,
					'created_at'	=> date('Y-m-d H:i:s'),
					'updated_at'	=> date('Y-m-d H:i:s')
				);

				DB::table('dependent_plan_history')->insert($history);
				// check if their is a plan tier id
				// get plan tier if employee has a plan tier
				$plan_tier = DB::table('plan_tier_users')
				->join('plan_tiers', 'plan_tiers.plan_tier_id', '=', 'plan_tier_users.plan_tier_id')
				->where('plan_tier_users.user_id', $owner_id)
				->where('plan_tiers.active', 1)
				->first();
				if($plan_tier) {
					$tier_history = array(
						'plan_tier_id'              => $plan_tier->plan_tier_id,
						'user_id'                   => $user_id,
						'status'                    => 1
					);

					$plan_tier_user->createData($tier_history);
				}

				// update
				DB::table('dependent_replacement_seat')
				->where('dependent_replacement_seat_id', $input['dependent_replacement_seat_id'])
				->update(['vacant_status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
			}

			return array('status' => true);
		}

		return array('status' => false, 'message' => 'Failed to Create Dependent from Vacant Seat Replacement.');
	}

	public function getDependentInvoice()
	{
		$input = Input::all();

		if(empty($input['token']) || $input['token'] == null) {
			return array('status' => false, 'message' => 'Access Token is required');
		}

		$result = StringHelper::checkToken($input['token']);

		if(empty($input['dependent_plan_id']) || $input['dependent_plan_id'] == null) {
			return array('status' => false, 'message' => 'Dependent Plan ID is required');
		}

		// check depedent existence
		$dependent_plan = DB::table('dependent_plans')->where('dependent_plan_id', $input['dependent_plan_id'])->first();

		if(!$dependent_plan) {
			return array('status' => false, 'message' => 'Dependent Plan does not exist.');
		}
		
		// check dependent invoice
		$invoice = DB::table('dependent_invoice')->where('dependent_plan_id', $input['dependent_plan_id'])->first();
		$plan = DB::table('customer_plan')->where('customer_plan_id', $dependent_plan->customer_plan_id)->first();
		$customer_id = $plan->customer_buy_start_id;
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if((int)$dependent_plan->tagged == 0) {
			// $invoice_count = DB::table('dependent_invoice')->count();
			// $invoice_number = str_pad($invoice_count + 1, 6, "0", STR_PAD_LEFT);
			// $lite_plan = false;

			// if($dependent_plan->account_type == "insurance_bundle") {
			// 	if($dependent_plan->secondary_account_type == "insurance_bundle_lite") {
		 // 			// $individual_price = 5;
			// 		$lite_plan = true;
			// 	} else {
		 // 			// $individual_price = 99;
			// 	}
			// } else if($dependent_plan->account_type == "lite_plan") {
			// 	// $individual_price = 5;
			// 	$lite_plan = true;
			// }

			// if($lite_plan == true) {
			// 	$invoice_number_format = 'LMC'.$invoice_number;
			// } else {
			// 	$invoice_number_format = 'OMC'.$invoice_number;
			// }
			$invoice_number_format = $invoice->invoice_number;
		} else {
			$invoice_active_plan = DB::table('corporate_invoice')->where('customer_active_plan_id', $dependent_plan->customer_active_plan_id)->first();
			$invoice_number_format = $invoice_active_plan->invoice_number;
		}

		if(!$invoice) {
			// create invoice
			$data_invoice = array(
				'dependent_plan_id'	=> $dependent_plan_id,
				'invoice_date'		=> $dependent_plan->created_at,
				'invoice_due'		=> date('Y-m-d', strtotime('+1 month', strtotime($dependent_plan->created_at))),
				'total_dependents'	=> $dependent_plan->total_dependents,
				'individual_price'	=> $dependent_plan->individual_price,
				'plan_start'		=> $dependent_plan->plan_start,
				'invoice_number'	=> $invoice_number_format,
				'created_at'		=> date('Y-m-d H:i:s'),
				'update_at'		=> date('Y-m-d H:i:s'),
				'currency_type'	=> $customer->currency_type
			);

			DB::table('dependent_invoice')->create($data_invoice);
			$invoice = DB::table('dependent_invoice')->where('dependent_plan_id', $request->get('dependent_plan_id'))->first();
			$invoice_number_format = $invoice->invoice_number;
		}

		$contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $customer_id)->first();
		$business_info = DB::table('customer_business_information')->where('customer_buy_start_id', $customer_id)->first();

		$data['email'] = $contact->work_email;
		$data['phone']     = $contact->phone;
		$data['currency_type'] = strtoupper($customer->currency_type);
		if($contact->billing_status === "true" || $contact->billing_status === true) {
			$data['name'] = ucwords($contact->first_name).' '.ucwords($contact->last_name);
		} else {
			$billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id',  $customer_id)->first();
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

		
		$plan_start = $plan->plan_start;

		$data['complimentary'] = FALSE;
		$data['plan_type'] = "Standalone Mednefits Care (Corporate)";

		if($dependent_plan->account_type == "stand_alone_plan") {
			$data['plan_type'] = "Standalone Mednefits Care (Corporate)";
			$data['account_type'] = "Pro Plan";
			$data['complimentary'] = FALSE;
		} else if($dependent_plan->account_type == "insurance_bundle") {
			$data['plan_type'] = "Bundled Mednefits Care (Corporate)";
			$data['account_type'] = "Insurance Bundle";
			$data['complimentary'] = TRUE;
		} else if($dependent_plan->account_type == "trial_plan") {
			$data['plan_type'] = "Trial Plan Mednefits Care (Corporate)";
			$data['account_type'] = "Trial Plan";
			$data['complimentary'] = FALSE;
		} else if($dependent_plan->account_type == "lite_plan") {
			$data['plan_type'] = "Lite Plan Mednefits Care (Corporate)";
			$data['account_type'] = "Lite Plan";
			$data['complimentary'] = FALSE;
		} else if($dependent_plan->account_type == "enterprise_plan") {
			$data['plan_type'] = "Enterprise Mednefits Care (Corporate)";
			$data['account_type'] = "Enterprise Plan";
			$data['complimentary'] = FALSE;
		}

		// return array('rest' => $invoice);

		$data['invoice_number'] = $invoice_number_format;
		// $data['invoice_number'] = '0000MMCC01';
		$data['invoice_date']		= date('F d, Y', strtotime($invoice->invoice_date));
		$data['invoice_due']		= date('F d, Y', strtotime($invoice->invoice_due));
		$data['number_employess'] = $invoice->total_dependents;
		$data['plan_start']     = date('F d, Y', strtotime($dependent_plan->plan_start));

		if((int)$dependent_plan->new_head_count == 0) {
			$data['price']          = number_format($invoice->individual_price, 2);
			$data['amount']			= number_format($data['number_employess'] * $invoice->individual_price, 2);
			$amount_due 			= $data['number_employess'] * $invoice->individual_price;
			$data['total']			= $data['number_employess'] * $invoice->individual_price;

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

			// $plan = DB::table('customer_plan')->where('customer_plan_id', $dependent_plan->customer_plan_id)->first();
			$first_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
			$duration = $first_plan->duration;

			if($dependent_plan->duration || $dependent_plan->duration != "") {
				$end_plan_date = date('Y-m-d', strtotime('+'.$duration, strtotime($plan->plan_start)));
				$data['duration'] = $dependent_plan->duration;
			} else {
				$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
				$data['duration'] = '12 months';
			}
		} else {
			$calculated_prices_end_date = PlanHelper::getCompanyPlanDates($plan->customer_buy_start_id);
			$calculated_prices_end_date = $calculated_prices_end_date['plan_end'];
			$calculated_prices = PlanHelper::calculateInvoicePlanPrice($invoice->individual_price, $dependent_plan->plan_start, $calculated_prices_end_date);
			$end_plan_date = $calculated_prices_end_date;
			$data['price']          = number_format($calculated_prices, 2);
			$amount_due = $data['number_employess'] * $calculated_prices;
			$data['amount']					= number_format($data['number_employess'] * $calculated_prices, 2);
			$data['total']					= $data['number_employess'] * $calculated_prices;
			// $data['duration'] = $dependent_plan->duration;
			$data['duration'] = PlanHelper::getPlanDuration($plan->customer_buy_start_id, $dependent_plan->plan_start);

			if((int)$dependent_plan->payment_status == 1) {
				$data['paid'] = true;
				$data['payment_date'] = $invoice->paid_date ? date('F d, Y', strtotime($invoice->paid_date)) : date('F d, Y', strtotime($invoice->created_at));
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

		$data['total'] = number_format($data['total'], 2);
		$data['amount_due'] = number_format($data['amount_due'], 2);
		// return $data['amount_due'];
		$data['dependent_plan_id'] = $dependent_plan->dependent_plan_id;
		$data['plan_end'] 			= date('F d, Y', strtotime('-1 day', strtotime($end_plan_date)));

	    // return View::make('pdf-download.dependent-invoice-download', $data);
		$pdf = \PDF::loadView('pdf-download.dependent-invoice-download', $data);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');

		return $pdf->stream($data['invoice_number'].' - '.$data['company'].'.pdf');
	}

}
