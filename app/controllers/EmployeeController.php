<?php
use Illuminate\Support\Facades\Input;

class EmployeeController extends \BaseController {
	public function updateCapPerVisitEmployee( )
	{
		$input = Input::all();

		$result = StringHelper::getJwtHrSession();
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_id = $result->hr_dashboard_id;
		
		if(empty($input['employee_id']) || $input['employee_id'] == null) {
			return array('status' => false, 'message' => 'Employee ID is required.');
		}

		$cap = array(
			'cap_per_visit_medical'		=> $input['cap_amount'],
			'cap_per_visit_wellness'	=> $input['cap_amount'],
			'updated_at'				=> date('Y-m-d H:i:s')
		);

		DB::table('e_wallet')->where('UserID', $input['employee_id'])->update($cap);
		$cap['employee_id'] = $input['employee_id'];
		if($admin_id) {
			$admin_logs = array(
                'admin_id'  => $admin_id,
                'admin_type' => 'mednefits',
                'type'      => 'admin_updated_cap_per_visit_cap',
                'data'      => SystemLogLibrary::serializeData($cap)
            );
            SystemLogLibrary::createAdminLog($admin_logs);
		} else {
			$admin_logs = array(
                'admin_id'  => $hr_id,
                'admin_type' => 'hr',
                'type'      => 'admin_updated_cap_per_visit_cap',
                'data'      => SystemLogLibrary::serializeData($cap)
            );
            SystemLogLibrary::createAdminLog($admin_logs);
		}

		return array('status' => true, 'message' => 'Cap updated.');
	}

	public function validateMember( ) 
	{
		$input = Input::all();

		if(empty($input['nric']) || $input['nric'] == null) {
			return array('status' => false, 'message' => 'NRIC is required.');
		}

		if(empty($input['password']) || $input['password'] == null) {
			return array('status' => false, 'message' => 'Password is required.');
		}

		$member = DB::table('user')
						->where('NRIC', 'like', '%'.$input['nric'].'%')
						->where('Password', md5($input['password']))
						->where('UserType', 5)
                        ->where('Active', 1)
						->whereIn('access_type', [1, 0])
						->first();

		if(!$member) {
			return array('status' => false, 'message' => 'NRIC/FIN or Password is incorrect.');
		}

        if((int)$member->account_update_status == 1 || (int)$member->account_update_date == 1) {
            return array('status' => true, 'updated' => true);
        }

		$temp = array(
			'user_id' => $member->UserID
		);

		$jwt = new JWT();
		$secret = Config::get('config.secret_key');
		$token = $jwt->encode($temp, $secret);

		return array('status' => true, 'token' => $token, 'updated' => false);
	}

	public function getEmployeeDetails( )
	{
		$input = Input::all();
		$token = StringHelper::getToken();

		if(!$token) {
			return array('status' => false, 'message' => 'Token is required.');
		}

		$secret = Config::get('config.secret_key');
		$result = FALSE;
        try {
            $result = JWT::decode($token, $secret);
        } catch(Exception $e) {
            return FALSE;
        }
       
        if(!$result) {
        	return array('status' => false, 'message' => 'Token is invalid.');
        }

        $member_id = $result->user_id;

        // get user details
        $member = DB::table('user')->where('UserID', $member_id)->first();

        $details = array(
        	'name'			=> ucwords($member->Name),
        	'dob' => $member->DOB ? date('d/m/Y', strtotime($member->DOB)) : null,
        	'mobile' => $member->PhoneNo,
        	'mobile_country_code' => $member->PhoneCode
        );

        $dependents = array();
        // check for dependents
        $dependent_temp = DB::table('employee_family_coverage_sub_accounts')
        				->where('owner_id', $member_id)
        				->where('deleted', 0)
        				->get();

       	foreach ($dependent_temp as $key => $dependent) {
       		$user = DB::table('user')->where('UserID', $dependent->user_id)->first();

       		$dependents[] = array(
       			'dependent_id' 	=> $dependent->user_id,
       			'name'			=> ucwords($user->Name),
       			'dob'			=> $user->DOB ? date('d/m/Y', strtotime($user->DOB)) : null
       		);
       	}

        $details['dependents'] = $dependents;
        return array('status' => true, 'data' => $details);
	}

	public function updateEmployeeDetails( )
	{
		$input = Input::all();
		$token = StringHelper::getToken();

		if(!$token) {
			return array('status' => false, 'message' => 'Token is required.');
		}

        if(empty($input['otp_code']) || $input['otp_code'] == null) {
            return array('status' => false, 'message' => 'OTP Code is required.');
        }

		if(empty($input['dob']) || $input['dob'] == null) {
			return array('status' => false, 'message' => 'DOB is required.');
		}

		if(empty($input['mobile_country_code']) || $input['mobile_country_code'] == null) {
			return array('status' => false, 'message' => 'Mobile Country Code is required.');
		}

		if(empty($input['mobile']) || $input['mobile'] == null) {
			return array('status' => false, 'message' => 'Mobile Phone is required.');
		}

		$secret = Config::get('config.secret_key');
		$result = FALSE;
        try {
            $result = JWT::decode($token, $secret);
        } catch(Exception $e) {
            return FALSE;
        }
       
        if(!$result) {
        	return array('status' => false, 'message' => 'Token is invalid.');
        }

        $member_id = $result->user_id;
        $mobile_number = (int)$input['mobile'];
        
        $result = DB::table('user')->where('UserID', $member_id)->where('OTPCode', $input['otp_code'])->first();
        if(!$result) {
            return array('status' => false, 'message' => 'Incorrect code, please try again.');
        }

        $check = DB::table('user')
        				->where('PhoneNo', (string)$mobile_number)
        				->whereNotIn('UserID', [$member_id])
        				->whereIn('access_type', [1, 0])
        				->where('UserType', 5)
        				->where('Active', 1)
        				->first();

        if($check) {
        	return array('status' => false, 'message' => 'Mobile Number is already taken.');
        }

        $member_data = array(
        	'DOB' => date('Y-m-d', strtotime($input['dob'])),
        	'PhoneNo'	=> $mobile_number,
        	'PhoneCode'	=> $input['mobile_country_code'],
        	'account_update_status'	=> 1,
        	'account_update_date'	=> date('Y-m-d H:i:s'),
            'OTPCode'   => null,
            'OTPStatus' => 0
        );

        DB::table('user')->where('UserID', $member_id)->update($member_data);

        // update dependents
        foreach ($input['dependents'] as $key => $dependent) {
        	$dependent_data = array(
        		'DOB' => date('Y-m-d', strtotime($dependent['dob'])),
        		'account_update_status'	=> 1,
        		'account_update_date'	=> date('Y-m-d H:i:s'),
        	);

        	DB::table('user')->where('UserID', $dependent['dependent_id'])->update($dependent_data);
        }

        return array('status' => true, 'message' => 'Success');
	}

	public function checkMobileExistence( )
	{
		$input = Input::all();
		$token = StringHelper::getToken();

		if(!$token) {
			return array('status' => false, 'message' => 'Token is required.');
		}

		$secret = Config::get('config.secret_key');
		$result = FALSE;
        try {
            $result = JWT::decode($token, $secret);
        } catch(Exception $e) {
            return FALSE;
        }
       
        if(!$result) {
        	return array('status' => false, 'message' => 'Token is invalid.');
        }

        $member_id = $result->user_id;

        $mobile_number = (int)$input['mobile'];
        
        $check = DB::table('user')
        				->where('PhoneNo', (string)$mobile_number)
        				->whereNotIn('UserID', [$member_id])
        				->whereIn('access_type', [1, 0])
        				->where('UserType', 5)
        				->where('Active', 1)
        				->first();

        if($check) {
        	return array('status' => false, 'message' => 'Mobile Number is already taken.');
        } else {
        	return array('status' => true, 'message' => 'Mobile Number is vacant.');
        }
	}

    public function sendMemberSmsOtp( )
    {
        $input = Input::all();
        $token = StringHelper::getToken();
        if(!$token) {
            return array('status' => false, 'message' => 'Token is required.');
        }

        $secret = Config::get('config.secret_key');
        $result = FALSE;
        try {
            $result = JWT::decode($token, $secret);
        } catch(Exception $e) {
            return FALSE;
        }
       
        if(!$result) {
            return array('status' => false, 'message' => 'Token is invalid.');
        }

        if(empty($input['mobile']) || $input['mobile'] == null) {
            return array('status' => false, 'message' => 'Mobile Number is required.');
        }

        if(empty($input['mobile_country_code']) || $input['mobile_country_code'] == null) {
            return array('status' => false, 'message' => 'Mobile Country Code is required.');
        }

        $member_id = $result->user_id;
        $mobile_number = (int)$input['mobile'];
        $code = $input['mobile_country_code'];
        $phone = $code.$mobile_number;

        $otp_code = StringHelper::OTPChallenge();
        StringHelper::TestSendOTPSMS($phone, $otp_code);
        DB::table('user')->where('UserID', $member_id)->update(['OTPCode' => $otp_code]);
        return array('status' => true, 'message' => 'OTP SMS sent');
        return $otp_code;
    }

    public function validateOpt( )
    {
        $input = Input::all();
        $token = StringHelper::getToken();
        if(!$token) {
            return array('status' => false, 'message' => 'Token is required.');
        }

        $secret = Config::get('config.secret_key');
        $result = FALSE;
        try {
            $result = JWT::decode($token, $secret);
        } catch(Exception $e) {
            return FALSE;
        }
       
        if(!$result) {
            return array('status' => false, 'message' => 'Token is invalid.');
        }

        if(empty($input['otp_code']) || $input['otp_code'] == null) {
            return array('status' => false, 'message' => 'OTP Code is required.');
        }

        $member_id = $result->user_id;
        $result = DB::table('user')->where('UserID', $member_id)->where('OTPCode', $input['otp_code'])->first();
        if(!$result) {
            return array('status' => false, 'message' => 'Invalid OTP Code.');
        }

        return array('status' => true, 'message' => 'OTPCode is valid');
    }

    public function getBlockClinicTypeLists( )
    {
        $input = Input::all();

        if(empty($input['region']) || $input['region'] == null) {
          return array('status' => false, 'message' => 'Region is required.');
        }

        if(empty($input['status']) || $input['status'] == null) {
          return array('status' => false, 'message' => 'Status should be block or open.');
        }

        $region_type = ["sgd", "myr", "all_region"];
        if(!in_array($input['region'], $region_type)) {
          return array('status' => false, 'message' => 'Region Type must be sgd or myr');
        }

        $result = StringHelper::getJwtHrSession();
        $customer_id = $result->customer_buy_start_id;
        $account_type = "company";

        $clinic_type_lists = DB::table('clinic_types')->get();
        $format = array();

        foreach ($clinic_type_lists as $key => $list) {
            if($input['status'] == "block") {
                if($input['region'] == "all_region") {
                    $clinic_block = DB::table('clinic_types')
                                        ->join('clinic', 'clinic.Clinic_Type', '=', 'clinic_types.ClinicTypeID')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->whereIn('clinic.currency_type', ["sgd", "myr"])
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->first();

                    $sgd = null;
                    $myr = null;
                    $sgd = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.Active', 1)
                                        ->where('clinic.currency_type', 'sgd')
                                        ->first();

                    $myr = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('clinic.Active', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.currency_type', 'myr')
                                        ->where('company_block_clinic_access.status', 1)
                                        ->first();

                    $sgd_clinic = DB::table('clinic')->where('currency_type', 'sgd')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    $myr_clinic = DB::table('clinic')->where('currency_type', 'myr')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();

                    if($clinic_block) {
                        $list->block_clinic = true;
                        if($sgd && $sgd_clinic){
                            $list->region[] = "Singapore";
                        } 

                        if($myr && $myr_clinic){
                            $list->region[] = "Malaysia";
                        }
                        array_push($format, $list);
                    } else {
                        $list->block_clinic = false;
                        if(!$sgd && $sgd_clinic || $sgd && $sgd_clinic){
                            $list->region[] = "Singapore";
                        } 
                        if(!$myr && $myr_clinic || $myr && $myr_clinic){
                            $list->region[] = "Malaysia";
                        }
                    }
                } else {
                    $clinic_block = DB::table('clinic_types')
                                        ->join('clinic', 'clinic.Clinic_Type', '=', 'clinic_types.ClinicTypeID')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('clinic.currency_type', $input['region'])
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->first();

                    $sgd = null;
                    $myr = null;
                    $myr_clinic = null;
                    $sgd_clinic = null;
                    if($input['region'] == "sgd"){
                        $sgd = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.Active', 1)
                                        ->where('clinic.currency_type', 'sgd')
                                        ->first();
                        $sgd_clinic = DB::table('clinic')->where('currency_type', 'sgd')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    } else if($input['region'] == "myr"){
                        $myr = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('clinic.Active', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.currency_type', 'myr')
                                        ->where('company_block_clinic_access.status', 1)
                                        ->first();
                        $myr_clinic = DB::table('clinic')->where('currency_type', 'myr')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    }

                    if($clinic_block) {
                        $list->block_clinic = true;
                        if($sgd && $sgd_clinic){
                            $list->region[] = "Singapore";
                        } 

                        if($myr && $myr_clinic){
                            $list->region[] = "Malaysia";
                        }
                        array_push($format, $list);
                    } else {
                        $list->block_clinic = false;
                    }
                }
            } else {
                if($input['region'] == "all_region") {
                    $clinic_block = DB::table('clinic_types')
                                        ->join('clinic', 'clinic.Clinic_Type', '=', 'clinic_types.ClinicTypeID')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->whereIn('clinic.currency_type', ["sgd", "myr"])
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->first();

                    $sgd = null;
                    $myr = null;
                    $sgd = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.Active', 1)
                                        ->where('clinic.currency_type', 'sgd')
                                        ->first();

                    $myr = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('clinic.Active', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.currency_type', 'myr')
                                        ->where('company_block_clinic_access.status', 1)
                                        ->first();

                    $sgd_clinic = DB::table('clinic')->where('currency_type', 'sgd')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    $myr_clinic = DB::table('clinic')->where('currency_type', 'myr')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    // return [$sgd,$sgd_clinic,$myr,$myr_clinic];
                    if($clinic_block) {
                        
                        if(!$sgd && $sgd_clinic || !$myr && $myr_clinic){
                            $list->open_clinic = true;

                            if(!$sgd && $sgd_clinic){
                                $list->region[] = "Singapore";
                            } 
                            if(!$myr && $myr_clinic){
                                $list->region[] = "Malaysia";
                            }

                            if($sgd_clinic || $myr_clinic) {
                                array_push($format, $list);
                            }
                        }else{
                            $list->open_clinic = false;

                            if($sgd && $sgd_clinic){
                                $list->region[] = "Singapore";
                            }

                            if($myr && $myr_clinic){
                                $list->region[] = "Malaysia";
                            }
                        }

                    } else {
                        $list->open_clinic = true;
                        if(!$sgd && $sgd_clinic){
                            $list->region[] = "Singapore";
                        } 

                        if(!$myr && $myr_clinic){
                            $list->region[] = "Malaysia";
                        }
                        if($sgd_clinic || $myr_clinic) {
                            array_push($format, $list);
                        }
                    }
                } else {
                    $clinic_block = DB::table('clinic_types')
                                        ->join('clinic', 'clinic.Clinic_Type', '=', 'clinic_types.ClinicTypeID')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('clinic.currency_type', $input['region'])
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->first();

                    $sgd = null;
                    $myr = null;
                    $myr_clinic = null;
                    $sgd_clinic = null;
                    if($input['region'] == "sgd"){
                        $sgd = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.Active', 1)
                                        ->where('clinic.currency_type', 'sgd')
                                        ->first();
                        $sgd_clinic = DB::table('clinic')->where('currency_type', 'sgd')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    } else if($input['region'] == "myr"){
                        $myr = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('clinic.Active', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.currency_type', 'myr')
                                        ->where('company_block_clinic_access.status', 1)
                                        ->first();
                        $myr_clinic = DB::table('clinic')->where('currency_type', 'myr')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    }
                    
                    if(!$clinic_block) {
                        $list->open_clinic = true;
                        if(!$sgd && $sgd_clinic || $sgd && $sgd_clinic){
                            $list->region[] = "Singapore";
                        } 

                        if(!$myr && $myr_clinic || $myr && $myr_clinic){
                            $list->region[] = "Malaysia";
                        }
                        array_push($format, $list);
                    } else {
                        $list->open_clinic = false;
                    }
                }
            }
            
            // array_push($format, $list);
        }

        return $format;
    }

    public function getBlockClinicTypeListsEmployee( )
    {
        $input = Input::all();

        if(empty($input['region']) || $input['region'] == null) {
          return array('status' => false, 'message' => 'Region is required.');
        }

        if(empty($input['user_id']) || $input['user_id'] == null) {
          return array('status' => false, 'message' => 'Member ID is required.');
        }

        if(empty($input['status']) || $input['status'] == null) {
          return array('status' => false, 'message' => 'Status should be block or open.');
        }

        $region_type = ["sgd", "myr", "all_region"];
        if(!in_array($input['region'], $region_type)) {
          return array('status' => false, 'message' => 'Region Type must be sgd or myr');
        }

        $result = StringHelper::getJwtHrSession();
        $customer_id = $input['user_id'];
        $account_type = "employee";

        $clinic_type_lists = DB::table('clinic_types')->get();
        $format = array();

        foreach ($clinic_type_lists as $key => $list) {
            if($input['status'] == "block") {
                if($input['region'] == "all_region") {
                    $clinic_block = DB::table('clinic_types')
                                        ->join('clinic', 'clinic.Clinic_Type', '=', 'clinic_types.ClinicTypeID')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->whereIn('clinic.currency_type', ["sgd", "myr"])
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->first();

                    $sgd = null;
                    $myr = null;
                    $sgd = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.Active', 1)
                                        ->where('clinic.currency_type', 'sgd')
                                        ->first();

                    $myr = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('clinic.Active', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.currency_type', 'myr')
                                        ->where('company_block_clinic_access.status', 1)
                                        ->first();

                    $sgd_clinic = DB::table('clinic')->where('currency_type', 'sgd')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    $myr_clinic = DB::table('clinic')->where('currency_type', 'myr')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();

                    if($clinic_block) {
                        $list->block_clinic = true;
                        if($sgd && $sgd_clinic){
                            $list->region[] = "Singapore";
                        } 

                        if($myr && $myr_clinic){
                            $list->region[] = "Malaysia";
                        }
                        array_push($format, $list);
                    } else {
                        $list->block_clinic = false;
                        if(!$sgd && $sgd_clinic || $sgd && $sgd_clinic){
                            $list->region[] = "Singapore";
                        } 
                        if(!$myr && $myr_clinic || $myr && $myr_clinic){
                            $list->region[] = "Malaysia";
                        }
                    }
                } else {
                    $clinic_block = DB::table('clinic_types')
                                        ->join('clinic', 'clinic.Clinic_Type', '=', 'clinic_types.ClinicTypeID')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('clinic.currency_type', $input['region'])
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->first();

                    $sgd = null;
                    $myr = null;
                    $myr_clinic = null;
                    $sgd_clinic = null;
                    if($input['region'] == "sgd"){
                        $sgd = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.Active', 1)
                                        ->where('clinic.currency_type', 'sgd')
                                        ->first();
                        $sgd_clinic = DB::table('clinic')->where('currency_type', 'sgd')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    } else if($input['region'] == "myr"){
                        $myr = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('clinic.Active', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.currency_type', 'myr')
                                        ->where('company_block_clinic_access.status', 1)
                                        ->first();
                        $myr_clinic = DB::table('clinic')->where('currency_type', 'myr')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    }

                    if($clinic_block) {
                        $list->block_clinic = true;
                        if($sgd && $sgd_clinic){
                            $list->region[] = "Singapore";
                        } 

                        if($myr && $myr_clinic){
                            $list->region[] = "Malaysia";
                        }
                        array_push($format, $list);
                    } else {
                        $list->block_clinic = false;
                    }
                }
            } else {
                if($input['region'] == "all_region") {
                    $clinic_block = DB::table('clinic_types')
                                        ->join('clinic', 'clinic.Clinic_Type', '=', 'clinic_types.ClinicTypeID')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->whereIn('clinic.currency_type', ["sgd", "myr"])
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->first();

                    $sgd = null;
                    $myr = null;
                    $sgd = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.Active', 1)
                                        ->where('clinic.currency_type', 'sgd')
                                        ->first();

                    $myr = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('clinic.Active', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.currency_type', 'myr')
                                        ->where('company_block_clinic_access.status', 1)
                                        ->first();

                    $sgd_clinic = DB::table('clinic')->where('currency_type', 'sgd')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    $myr_clinic = DB::table('clinic')->where('currency_type', 'myr')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    // return [$sgd,$sgd_clinic,$myr,$myr_clinic];
                    if($clinic_block) {
                        
                        if(!$sgd && $sgd_clinic || !$myr && $myr_clinic){
                            $list->open_clinic = true;

                            if(!$sgd && $sgd_clinic){
                                $list->region[] = "Singapore";
                            } 
                            if(!$myr && $myr_clinic){
                                $list->region[] = "Malaysia";
                            }

                            if($sgd_clinic || $myr_clinic) {
                                array_push($format, $list);
                            }
                        }else{
                            $list->open_clinic = false;

                            if($sgd && $sgd_clinic){
                                $list->region[] = "Singapore";
                            }

                            if($myr && $myr_clinic){
                                $list->region[] = "Malaysia";
                            }
                        }

                    } else {
                        $list->open_clinic = true;
                        if(!$sgd && $sgd_clinic){
                            $list->region[] = "Singapore";
                        } 

                        if(!$myr && $myr_clinic){
                            $list->region[] = "Malaysia";
                        }
                        if($sgd_clinic || $myr_clinic) {
                            array_push($format, $list);
                        }
                    }
                } else {
                    $clinic_block = DB::table('clinic_types')
                                        ->join('clinic', 'clinic.Clinic_Type', '=', 'clinic_types.ClinicTypeID')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('clinic.currency_type', $input['region'])
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->first();

                    $sgd = null;
                    $myr = null;
                    $myr_clinic = null;
                    $sgd_clinic = null;
                    if($input['region'] == "sgd"){
                        $sgd = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('company_block_clinic_access.status', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.Active', 1)
                                        ->where('clinic.currency_type', 'sgd')
                                        ->first();
                        $sgd_clinic = DB::table('clinic')->where('currency_type', 'sgd')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    } else if($input['region'] == "myr"){
                        $myr = DB::table('clinic')
                                        ->join('company_block_clinic_access', 'company_block_clinic_access.clinic_id', '=', 'clinic.ClinicID')
                                        ->where('clinic.Clinic_Type', $list->ClinicTypeID)
                                        ->where('clinic.Active', 1)
                                        ->where('company_block_clinic_access.account_type', $account_type)
                                        ->where('company_block_clinic_access.customer_id', $customer_id)
                                        ->where('clinic.currency_type', 'myr')
                                        ->where('company_block_clinic_access.status', 1)
                                        ->first();
                        $myr_clinic = DB::table('clinic')->where('currency_type', 'myr')->where('Clinic_Type', $list->ClinicTypeID)->where('Active', 1)->first();
                    }
                    
                    if(!$clinic_block) {
                        $list->open_clinic = true;
                        if(!$sgd && $sgd_clinic || $sgd && $sgd_clinic){
                            $list->region[] = "Singapore";
                        } 

                        if(!$myr && $myr_clinic || $myr && $myr_clinic){
                            $list->region[] = "Malaysia";
                        }
                        array_push($format, $list);
                    } else {
                        $list->open_clinic = false;
                    }
                }
            }
            
            // array_push($format, $list);
        }

        return $format;
    }

    public function getCompanyBlockClinicLists( )
    {
        $input = Input::all();

        if(empty($input['region']) || $input['region'] == null) {
          return array('status' => false, 'message' => 'Region is required.');
        }

        $region_type = ["sgd", "myr", "all_region"];
        if(!in_array($input['region'], $region_type)) {
          return array('status' => false, 'message' => 'Region Type must be sgd or myr');
        }

        $result = StringHelper::getJwtHrSession();
        $customer_id = $result->customer_buy_start_id;
        $account_type = "company";

        $limit = !empty($input['per_page']) ? $input['per_page'] : 10;

        if(isset($input['search']) && !empty($input['search']) || isset($input['search']) && $input['search'] != null) {
          if($input['region'] == "all_region") {
           $results = DB::table('company_block_clinic_access')
                    ->where('customer_id', $customer_id)
                    ->join('clinic', 'clinic.ClinicID', '=', 'company_block_clinic_access.clinic_id')
                    ->where('clinic.Name', 'like', '%'.$input['search'].'%')
                    ->where('clinic.Active', 1)
                    ->whereIn('clinic.currency_type', ["sgd", "myr", "all_region"])
                    ->where('company_block_clinic_access.status', 1)
                    ->where('company_block_clinic_access.account_type', $account_type)
                    ->get();
          } else {
            $results = DB::table('company_block_clinic_access')
                    ->where('customer_id', $customer_id)
                    ->join('clinic', 'clinic.ClinicID', '=', 'company_block_clinic_access.clinic_id')
                    ->where('clinic.Name', 'like', '%'.$input['search'].'%')
                    ->where('clinic.Active', 1)
                    ->where('clinic.currency_type', $input['region'])
                    ->where('company_block_clinic_access.status', 1)
                    ->where('company_block_clinic_access.account_type', $account_type)
                    ->get();
          }
        } else {
          if($input['region'] == "all_region") {
            $results = DB::table('company_block_clinic_access')
                    ->where('customer_id', $customer_id)
                    ->join('clinic', 'clinic.ClinicID', '=', 'company_block_clinic_access.clinic_id')
                    ->where('company_block_clinic_access.status', 1)
                    ->where('clinic.Active', 1)
                    ->whereIn('clinic.currency_type', ["sgd", "myr", "all_region"])
                    ->where('company_block_clinic_access.account_type', $account_type)
                    ->paginate($limit);
          } else {
            $results = DB::table('company_block_clinic_access')
                    ->where('customer_id', $customer_id)
                    ->join('clinic', 'clinic.ClinicID', '=', 'company_block_clinic_access.clinic_id')
                    ->where('company_block_clinic_access.status', 1)
                    ->where('clinic.Active', 1)
                    ->where('clinic.currency_type', $input['region'])
                    ->where('company_block_clinic_access.account_type', $account_type)
                    ->paginate($limit);
          }
          

        }
        return $results;
        return array('status' => true, 'data' => $results);
    }

    public function getCompanyBlockClinicListsEmployee( )
    {
        $input = Input::all();

        if(empty($input['region']) || $input['region'] == null) {
          return array('status' => false, 'message' => 'Region is required.');
        }

        if(empty($input['user_id']) || $input['user_id'] == null) {
          return array('status' => false, 'message' => 'Member ID is required.');
        }

        $region_type = ["sgd", "myr", "all_region"];
        if(!in_array($input['region'], $region_type)) {
          return array('status' => false, 'message' => 'Region Type must be sgd or myr');
        }

        $result = StringHelper::getJwtHrSession();
        $customer_id = $input['user_id'];
        $account_type = "employee";

        $limit = !empty($input['per_page']) ? $input['per_page'] : 10;

        if(isset($input['search']) && !empty($input['search']) || isset($input['search']) && $input['search'] != null) {
          if($input['region'] == "all_region") {
           $results = DB::table('company_block_clinic_access')
                    ->where('customer_id', $customer_id)
                    ->join('clinic', 'clinic.ClinicID', '=', 'company_block_clinic_access.clinic_id')
                    ->where('clinic.Name', 'like', '%'.$input['search'].'%')
                    ->where('clinic.Active', 1)
                    ->whereIn('clinic.currency_type', ["sgd", "myr", "all_region"])
                    ->where('company_block_clinic_access.status', 1)
                    ->where('company_block_clinic_access.account_type', $account_type)
                    ->get();
          } else {
            $results = DB::table('company_block_clinic_access')
                    ->where('customer_id', $customer_id)
                    ->join('clinic', 'clinic.ClinicID', '=', 'company_block_clinic_access.clinic_id')
                    ->where('clinic.Name', 'like', '%'.$input['search'].'%')
                    ->where('clinic.Active', 1)
                    ->where('clinic.currency_type', $input['region'])
                    ->where('company_block_clinic_access.status', 1)
                    ->where('company_block_clinic_access.account_type', $account_type)
                    ->get();
          }
        } else {
          if($input['region'] == "all_region") {
            $results = DB::table('company_block_clinic_access')
                    ->where('customer_id', $customer_id)
                    ->join('clinic', 'clinic.ClinicID', '=', 'company_block_clinic_access.clinic_id')
                    ->where('company_block_clinic_access.status', 1)
                    ->where('clinic.Active', 1)
                    ->whereIn('clinic.currency_type', ["sgd", "myr", "all_region"])
                    ->where('company_block_clinic_access.account_type', $account_type)
                    ->paginate($limit);
          } else {
            $results = DB::table('company_block_clinic_access')
                    ->where('customer_id', $customer_id)
                    ->join('clinic', 'clinic.ClinicID', '=', 'company_block_clinic_access.clinic_id')
                    ->where('company_block_clinic_access.status', 1)
                    ->where('clinic.Active', 1)
                    ->where('clinic.currency_type', $input['region'])
                    ->where('company_block_clinic_access.account_type', $account_type)
                    ->paginate($limit);
          }
          

        }
        return $results;
        return array('status' => true, 'data' => $results);
    }

    public function getCompanyActiveClinicLists( )
    {
        $input = Input::all();
        if(empty($input['region']) || $input['region'] == null) {
          return array('status' => false, 'message' => 'Region is required.');
        }

        $region_type = ["sgd", "myr", "all_region"];
        if(!in_array($input['region'], $region_type)) {
          return array('status' => false, 'message' => 'Region Type must be sgd or myr');
        }

        $result = StringHelper::getJwtHrSession();
        $customer_id = $result->customer_buy_start_id;
        $account_type = "company";

        $format = [];
        $limit = !empty($input['per_page']) ? $input['per_page'] : 10;

        $results = DB::table('company_block_clinic_access')
                            ->where('customer_id', $customer_id)
                          ->where('account_type', $account_type)
                          ->where('status', 1)
                          ->get();
        $new_array = [];
        foreach ($results as $key => $result) {
          $new_array[] = $result->clinic_id;
        }

        if(isset($input['search']) && !empty($input['search']) || isset($input['search']) && $input['search'] != null) {
            if(sizeof($new_array) > 0) {
                  if($input['region'] == "all_region") {
                    $clinics = DB::table('clinic')
                            ->whereNotIn('ClinicID', $new_array)
                            ->where('Name', 'like', '%'.$input['search'].'%')
                            ->where('Active', 1)
                            ->whereIn('currency_type', ["company", "employee"])
                            ->orderBy('Created_on', 'desc')
                            ->get();
                  } else {
                    $clinics = DB::table('clinic')
                            ->whereNotIn('ClinicID', $new_array)
                            ->where('Name', 'like', '%'.$input['search'].'%')
                            ->where('Active', 1)
                            ->where('currency_type', $input['region'])
                            ->orderBy('Created_on', 'desc')
                            ->get();
                  }

              } else {
                if($input['region'] == "all_region") {
                    $clinics = DB::table('clinic')
                            ->where('Name', 'like', '%'.$input['search'].'%')
                            ->where('Active', 1)
                            ->whereIn('currency_type', ["company", "employee"])
                            ->orderBy('Created_on', 'desc')
                            ->get();
                  } else {
                    $clinics = DB::table('clinic')
                            ->where('Name', 'like', '%'.$input['search'].'%')
                            ->where('Active', 1)
                            ->where('currency_type', $input['region'])
                            ->orderBy('Created_on', 'desc')
                            ->get();
                  }
              }
          
        } else {
            if(sizeof($new_array) > 0) {
              if($input['region'] == "all_region") {
                $clinics = DB::table('clinic')
                        ->whereNotIn('ClinicID', $new_array)
                        ->where('Active', 1)
                        ->orderBy('Created_on', 'desc')
                        ->paginate($limit);
              } else {
                $clinics = DB::table('clinic')
                        ->whereNotIn('ClinicID', $new_array)
                        ->where('Active', 1)
                        ->where('currency_type', $input['region'])
                        ->orderBy('Created_on', 'desc')
                        ->paginate($limit);
              }
            } else {
                if($input['region'] == "all_region") {
                $clinics = DB::table('clinic')
                        ->where('Active', 1)
                        ->orderBy('Created_on', 'desc')
                        ->paginate($limit);
              } else {
                $clinics = DB::table('clinic')
                        ->where('Active', 1)
                        ->where('currency_type', $input['region'])
                        ->orderBy('Created_on', 'desc')
                        ->paginate($limit);
              }
            }
        }


        return $clinics;
        return array('status' => true, 'data' => $clinics);
    }

    public function getCompanyActiveClinicListsEmployee( )
    {
        $input = Input::all();
        if(empty($input['region']) || $input['region'] == null) {
          return array('status' => false, 'message' => 'Region is required.');
        }

        if(empty($input['user_id']) || $input['user_id'] == null) {
          return array('status' => false, 'message' => 'Member ID is required.');
        }

        $region_type = ["sgd", "myr", "all_region"];
        if(!in_array($input['region'], $region_type)) {
          return array('status' => false, 'message' => 'Region Type must be sgd or myr');
        }

        $result = StringHelper::getJwtHrSession();
        $customer_id = $input['user_id'];
        $account_type = "employee";

        $format = [];
        $limit = !empty($input['per_page']) ? $input['per_page'] : 10;

        $results = DB::table('company_block_clinic_access')
                            ->where('customer_id', $customer_id)
                          ->where('account_type', $account_type)
                          ->where('status', 1)
                          ->get();
        $new_array = [];
        foreach ($results as $key => $result) {
          $new_array[] = $result->clinic_id;
        }

        if(isset($input['search']) && !empty($input['search']) || isset($input['search']) && $input['search'] != null) {
          if($input['region'] == "all_region") {
            if(sizeof($new_array) > 0) {
                $clinics = DB::table('clinic')
                        ->whereNotIn('ClinicID', $new_array)
                        ->where('Name', 'like', '%'.$input['search'].'%')
                        ->where('Active', 1)
                        ->whereIn('currency_type', ["company", "employee"])
                        ->orderBy('Created_on', 'desc')
                        ->get();
            } else {
                $clinics = DB::table('clinic')
                        ->where('Name', 'like', '%'.$input['search'].'%')
                        ->where('Active', 1)
                        ->whereIn('currency_type', ["company", "employee"])
                        ->orderBy('Created_on', 'desc')
                        ->get();
            }
          } else {
            if(sizeof($new_array) > 0) {
                $clinics = DB::table('clinic')
                        ->whereNotIn('ClinicID', $new_array)
                        ->where('Name', 'like', '%'.$input['search'].'%')
                        ->where('Active', 1)
                        ->where('currency_type', $input['region'])
                        ->orderBy('Created_on', 'desc')
                        ->get();
            } else {
                $clinics = DB::table('clinic')
                        ->where('Name', 'like', '%'.$input['search'].'%')
                        ->where('Active', 1)
                        ->where('currency_type', $input['region'])
                        ->orderBy('Created_on', 'desc')
                        ->get();
            }
          }
          
        } else {
          if($input['region'] == "all_region") {
            if(sizeof($new_array) > 0) {
                $clinics = DB::table('clinic')
                        ->whereNotIn('ClinicID', $new_array)
                        ->where('Active', 1)
                        ->orderBy('Created_on', 'desc')
                        ->paginate($limit);
            } else {
                $clinics = DB::table('clinic')
                        ->where('Active', 1)
                        ->orderBy('Created_on', 'desc')
                        ->paginate($limit);
            }
          } else {
            if(sizeof($new_array) > 0) {
                $clinics = DB::table('clinic')
                        ->whereNotIn('ClinicID', $new_array)
                        ->where('Active', 1)
                        ->where('currency_type', $input['region'])
                        ->orderBy('Created_on', 'desc')
                        ->paginate($limit);
            } else {
                $clinics = DB::table('clinic')
                        ->where('Active', 1)
                        ->where('currency_type', $input['region'])
                        ->orderBy('Created_on', 'desc')
                        ->paginate($limit);
            }
          }
        }


        return $clinics;
        return array('status' => true, 'data' => $clinics);
    }

    public function createCompanyBlockClinicLists( )
    {
        $input = Input::all();
        if(empty($input['type']) || $input['type'] == null) {
          return array('status' => false, 'message' => 'Block access type access is required');
        }

        $region_type = ["sgd", "myr", "all_region"];

        if(!in_array($input['region'], $region_type)) {
          return array('status' => false, 'message' => 'Region Type must be sgd or myr');
        }

        $result = StringHelper::getJwtHrSession();
        $customer_id = $result->customer_buy_start_id;
        $hr_id = $result->hr_dashboard_id;
        $admin_id = Session::get('admin-session-id');
        $account_type = "company";

        $check = $customer = DB::table('customer_buy_start')
                    ->where('customer_buy_start_id', $customer_id)
                    ->first();
        if(!$customer) {
            return array('status' => false, 'message' => 'Customer/Company does not exist.');
        }

        if($input['type'] == "clinic_type") {
          if(empty($input['clinic_type_id']) || $input['clinic_type_id'] == null) {
            return array('status' => false, 'messsage' => 'Clinic Type ID is required');
          }

          if(empty($input['access_status']) || $input['access_status'] == null) {
            return array('status' => false, 'messsage' => 'Access status is required');
          }

          // $clinic_type = DB::table('clinic_types')
          //                 ->where('ClinicTypeID', $input['clinic_type_id'])
          //                 ->first();

          // if(!$clinic_type) {
          //   return array('status' => false, 'message' => 'Clinic Type does not exist');
          // }
          
          if($input['region'] == "all_region") {
            $clinic_ids = DB::table('clinic')
                            ->join('clinic_types', 'clinic_types.ClinicTypeID', '=', 'clinic.Clinic_Type')
                            ->whereIn('clinic.Clinic_Type', $input['clinic_type_id'])
                            ->get();
          } else {
            $clinic_ids = DB::table('clinic')
                            ->join('clinic_types', 'clinic_types.ClinicTypeID', '=', 'clinic.Clinic_Type')
                            ->where('clinic.currency_type', $input['region'])
                            ->whereIn('clinic.Clinic_Type', $input['clinic_type_id'])
                            ->get();
          }
          
          if($input['access_status'] == "block") {
            foreach ($clinic_ids as $key => $clinic_id) {
                $id = $clinic_id->ClinicID;
              // check if clinic block already exits
              $check = DB::table('company_block_clinic_access')
                        ->where('customer_id', $customer_id)
                        ->where('clinic_id', $id)
                        ->first();

              if(!$check) {
                // create block access
                $data = array(
                  'customer_id' => $customer_id,
                  'clinic_id'   => $id,
                  'account_type' => $account_type,
                  'status'      => 1,
                  'created_at'  => date('Y-m-d H:i:s'),
                  'updated_at'  => date('Y-m-d H:i:s')
                );
                $result = DB::table('company_block_clinic_access')->insert($data);
                if($result) {
                  if($admin_id) {
                    $block = array(
                      'customer_id' => $customer_id,
                      'clinic_id'   => $id,
                      'status'      => 1
                    );
                    $admin_logs = array(
                        'admin_id'  => $admin_id,
                        'type'      => 'admin_company_block_clinic_access',
                        'data'      => serialize($block)
                    );
                    SystemLogLibrary::createAdminLog($admin_logs);
                  } else {
                    $block = array(
                      'customer_id' => $customer_id,
                      'clinic_id'   => $id,
                      'status'      => 1
                    );
                    $admin_logs = array(
                        'admin_id'  => $hr_id,
                        'type'      => 'admin_company_block_clinic_access',
                        'data'      => serialize($block)
                    );
                    SystemLogLibrary::createAdminLog($admin_logs);
                  }
                }
              } else {
                if((int)$check->status == 0) {
                  $result = DB::table('company_block_clinic_access')->where('company_block_clinic_access_id', $check->company_block_clinic_access_id)->update(['status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                }
              }
            }
          } else {
            foreach ($clinic_ids as $key => $clinic_id) {
                $id = $clinic_id->ClinicID;
              // check if clinic block already exits
              $check = DB::table('company_block_clinic_access')
                        ->where('customer_id', $customer_id)
                        ->where('clinic_id', $id)
                        ->first();
              if($check && (int)$check->status == 1) {
                $result = DB::table('company_block_clinic_access')->where('company_block_clinic_access_id', $check->company_block_clinic_access_id)->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
                if($result) {
                  if($admin_id) {
                    $block = array(
                      'customer_id' => $customer_id,
                      'clinic_id'   => $input['clinic_id'],
                      'status'      => 0
                    );
                      $admin_logs = array(
                          'admin_id'  => $admin_id,
                          'type'      => 'admin_company_block_clinic_access',
                          'data'      => serialize($block)
                      );
                      SystemLogLibrary::createAdminLog($admin_logs);
                  } else {
                    $block = array(
                      'customer_id' => $customer_id,
                      'clinic_id'   => $input['clinic_id'],
                      'status'      => 0
                    );
                      $admin_logs = array(
                          'admin_id'  => $hr_id,
                          'type'      => 'admin_company_block_clinic_access',
                          'data'      => serialize($block)
                      );
                      SystemLogLibrary::createAdminLog($admin_logs);
                  }
                }
              }
            }
          }
        } else {
          if(empty($input['clinic_id']) || $input['clinic_id'] == null) {
            return array('status' => false, 'message' => 'Clinic ID is required.');
          }
          $status_codes = [0, 1];
            if(!empty($input['status'])) {
                if(!in_array((int)$input['status'], $status_codes)) {
                    return array('status' => false, 'message' => 'Status should only be 1 or 0');
                }
            }

            $check = DB::table('company_block_clinic_access')->where('clinic_id', $input['clinic_id'])
                                          ->where('customer_id', $customer_id)
                                          ->first();
          
          $status = $input['status'];

            if(!$check) {
                // create
                $block = array(
              'customer_id' => $customer_id,
              'clinic_id'   => $input['clinic_id'],
              'account_type' => $account_type,
              'status'      => $status,
              'created_at'  => date('Y-m-d H:i:s'),
              'updated_at'  => date('Y-m-d H:i:s')
              );

              DB::table('company_block_clinic_access')->insert($block);
            } else {
                DB::table('company_block_clinic_access')->where('clinic_id', $input['clinic_id'])
                                          ->where('customer_id', $customer_id)
                                          ->update(['status' => $status, 'updated_at'  => date('Y-m-d H:i:s')]);
            }

          if($admin_id) {
            $block = array(
              'customer_id' => $customer_id,
              'clinic_id'   => $input['clinic_id'],
              'status'      => $status
            );
              $admin_logs = array(
                  'admin_id'  => $admin_id,
                  'type'      => 'admin_company_block_clinic_access',
                  'data'      => serialize($block)
              );
              \SystemLogLibrary::createAdminLog($admin_logs);
          } else {
            $block = array(
              'customer_id' => $customer_id,
              'clinic_id'   => $input['clinic_id'],
              'status'      => $status
            );
              $admin_logs = array(
                  'admin_id'  => $hr_id,
                  'type'      => 'admin_company_block_clinic_access',
                  'data'      => serialize($block)
              );
              \SystemLogLibrary::createAdminLog($admin_logs);
          }
        }

        return array('status' => true, 'message' => 'Clinic Block Lists updated.');
    }

    public function createCompanyBlockClinicListsEmployee( )
    {
        $input = Input::all();
        if(empty($input['type']) || $input['type'] == null) {
          return array('status' => false, 'message' => 'Block access type access is required');
        }

        if(empty($input['region']) || $input['region'] == null) {
          return array('status' => false, 'message' => 'Region is required');
        }

        if(empty($input['user_id']) || $input['user_id'] == null) {
          return array('status' => false, 'message' => 'Member ID is required');
        }

        $region_type = ["sgd", "myr", "all_region"];

        if(!in_array($input['region'], $region_type)) {
          return array('status' => false, 'message' => 'Region Type must be sgd or myr');
        }

        $result = StringHelper::getJwtHrSession();
        $customer_id = $input['user_id'];
        $hr_id = $result->hr_dashboard_id;
        $admin_id = Session::get('admin-session-id');
        $account_type = "employee";

        $check = $customer = DB::table('customer_buy_start')
                    ->where('customer_buy_start_id', $customer_id)
                    ->first();
        if(!$customer) {
            return array('status' => false, 'message' => 'Customer/Company does not exist.');
        }

        if($input['type'] == "clinic_type") {
          if(empty($input['clinic_type_id']) || $input['clinic_type_id'] == null) {
            return array('status' => false, 'messsage' => 'Clinic Type ID is required');
          }

          if(empty($input['access_status']) || $input['access_status'] == null) {
            return array('status' => false, 'messsage' => 'Access status is required');
          }

          // $clinic_type = DB::table('clinic_types')
          //                 ->where('ClinicTypeID', $input['clinic_type_id'])
          //                 ->first();

          // if(!$clinic_type) {
          //   return array('status' => false, 'message' => 'Clinic Type does not exist');
          // }
          
          if($input['region'] == "all_region") {
            $clinic_ids = DB::table('clinic')
                            ->join('clinic_types', 'clinic_types.ClinicTypeID', '=', 'clinic.Clinic_Type')
                            ->whereIn('clinic.Clinic_Type', $input['clinic_type_id'])
                            ->get();
          } else {
            $clinic_ids = DB::table('clinic')
                            ->join('clinic_types', 'clinic_types.ClinicTypeID', '=', 'clinic.Clinic_Type')
                            ->where('clinic.currency_type', $input['region'])
                            ->whereIn('clinic.Clinic_Type', $input['clinic_type_id'])
                            ->get();
          }
          
          if($input['access_status'] == "block") {
            foreach ($clinic_ids as $key => $clinic_id) {
                $id = $clinic_id->ClinicID;
              // check if clinic block already exits
              $check = DB::table('company_block_clinic_access')
                        ->where('customer_id', $customer_id)
                        ->where('clinic_id', $id)
                        ->first();

              if(!$check) {
                // create block access
                $data = array(
                  'customer_id' => $customer_id,
                  'clinic_id'   => $id,
                  'account_type' => $account_type,
                  'status'      => 1,
                  'created_at'  => date('Y-m-d H:i:s'),
                  'updated_at'  => date('Y-m-d H:i:s')
                );
                $result = DB::table('company_block_clinic_access')->insert($data);
                if($result) {
                  if($admin_id) {
                    $block = array(
                      'customer_id' => $customer_id,
                      'clinic_id'   => $id,
                      'status'      => 1
                    );
                    $admin_logs = array(
                        'admin_id'  => $admin_id,
                        'type'      => 'admin_company_block_clinic_access',
                        'data'      => serialize($block)
                    );
                    SystemLogLibrary::createAdminLog($admin_logs);
                  } else {
                    $block = array(
                      'customer_id' => $customer_id,
                      'clinic_id'   => $id,
                      'status'      => 1
                    );
                    $admin_logs = array(
                        'admin_id'  => $hr_id,
                        'type'      => 'admin_company_block_clinic_access',
                        'data'      => serialize($block)
                    );
                    SystemLogLibrary::createAdminLog($admin_logs);
                  }
                }
              } else {
                if((int)$check->status == 0) {
                  $result = DB::table('company_block_clinic_access')->where('company_block_clinic_access_id', $check->company_block_clinic_access_id)->update(['status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                }
              }
            }
          } else {
            foreach ($clinic_ids as $key => $clinic_id) {
                $id = $clinic_id->ClinicID;
              // check if clinic block already exits
              $check = DB::table('company_block_clinic_access')
                        ->where('customer_id', $customer_id)
                        ->where('clinic_id', $id)
                        ->first();
              if($check && (int)$check->status == 1) {
                $result = DB::table('company_block_clinic_access')->where('company_block_clinic_access_id', $check->company_block_clinic_access_id)->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
                if($result) {
                  if($admin_id) {
                    $block = array(
                      'customer_id' => $customer_id,
                      'clinic_id'   => $input['clinic_id'],
                      'status'      => 0
                    );
                      $admin_logs = array(
                          'admin_id'  => $admin_id,
                          'type'      => 'admin_company_block_clinic_access',
                          'data'      => serialize($block)
                      );
                      SystemLogLibrary::createAdminLog($admin_logs);
                  } else {
                    $block = array(
                      'customer_id' => $customer_id,
                      'clinic_id'   => $input['clinic_id'],
                      'status'      => 0
                    );
                      $admin_logs = array(
                          'admin_id'  => $hr_id,
                          'type'      => 'admin_company_block_clinic_access',
                          'data'      => serialize($block)
                      );
                      SystemLogLibrary::createAdminLog($admin_logs);
                  }
                }
              }
            }
          }
        } else {
          if(empty($input['clinic_id']) || $input['clinic_id'] == null) {
            return array('status' => false, 'message' => 'Clinic ID is required.');
          }
          $status_codes = [0, 1];
            if(!empty($input['status'])) {
                if(!in_array((int)$input['status'], $status_codes)) {
                    return array('status' => false, 'message' => 'Status should only be 1 or 0');
                }
            }

            $check = DB::table('company_block_clinic_access')->where('clinic_id', $input['clinic_id'])
                                          ->where('customer_id', $customer_id)
                                          ->first();
          
          $status = $input['status'];

            if(!$check) {
                // create
                $block = array(
              'customer_id' => $customer_id,
              'clinic_id'   => $input['clinic_id'],
              'account_type' => $account_type,
              'status'      => $status,
              'created_at'  => date('Y-m-d H:i:s'),
              'updated_at'  => date('Y-m-d H:i:s')
              );

              DB::table('company_block_clinic_access')->insert($block);
            } else {
                DB::table('company_block_clinic_access')->where('clinic_id', $input['clinic_id'])
                                          ->where('customer_id', $customer_id)
                                          ->update(['status' => $status, 'updated_at'  => date('Y-m-d H:i:s')]);
            }

          if($admin_id) {
            $block = array(
              'customer_id' => $customer_id,
              'clinic_id'   => $input['clinic_id'],
              'status'      => $status
            );
              $admin_logs = array(
                  'admin_id'  => $admin_id,
                  'type'      => 'admin_company_block_clinic_access',
                  'data'      => serialize($block)
              );
              \SystemLogLibrary::createAdminLog($admin_logs);
          } else {
            $block = array(
              'customer_id' => $customer_id,
              'clinic_id'   => $input['clinic_id'],
              'status'      => $status
            );
              $admin_logs = array(
                  'admin_id'  => $hr_id,
                  'type'      => 'admin_company_block_clinic_access',
                  'data'      => serialize($block)
              );
              \SystemLogLibrary::createAdminLog($admin_logs);
          }
        }

        return array('status' => true, 'message' => 'Clinic Block Lists updated.');
    }

    public function employeeCapPerVisit( )
    {
        $input = Input::all();
        $result = StringHelper::getJwtHrSession();
        $customer_id = $result->customer_buy_start_id;
        $per_page = isset($input['per_page']) || !empty($input['per_page']) ? $input['per_page'] : 25;
        $account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
        $final_user = [];
        $paginate = [];

        $users = DB::table('user')
        ->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
        ->join('corporate', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
        ->where('corporate.corporate_id', $account_link->corporate_id)
        ->where('user.Active', 1)
        ->select('user.UserID', 'user.Name')
        ->orderBy('corporate_members.removed_status', 'asc')
        ->orderBy('user.UserID', 'asc')
        ->paginate($per_page);

        $paginate['last_page'] = $users->getLastPage();
        $paginate['current_page'] = $users->getCurrentPage();
        $paginate['total_data'] = $users->getTotal();
        $paginate['from'] = $users->getFrom();
        $paginate['to'] = $users->getTo();
        $paginate['count'] = $users->count();

        foreach ($users as $key => $user) {
            $wallet = DB::table('e_wallet')->where('UserID', $user->UserID)->first();
            $cap_amount = $wallet->cap_per_visit_medical;
            $final_user[] = array(
                'user_id'   => $user->UserID,
                'name'      => ucwords($user->Name),
                'cap_amount'    => $cap_amount,
                'currency_type' => strtoupper($wallet->currency_type)
            );
        }

        $paginate['data'] = $final_user;
        return $paginate;
    }

    public function downloadCaperPervisitCSV( )
    {
        $input = Input::all();
        $result = StringHelper::checkToken($input['token']);
        $customer_id = $result->customer_buy_start_id;
        $per_page = isset($input['per_page']) || !empty($input['per_page']) ? $input['per_page'] : 25;
        $account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
        $final_user = [];

        $users = DB::table('user')
        ->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
        ->join('corporate', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
        ->where('corporate.corporate_id', $account_link->corporate_id)
        ->where('user.Active', 1)
        ->select('user.UserID', 'user.Name')
        ->orderBy('corporate_members.removed_status', 'asc')
        ->orderBy('user.UserID', 'asc')
        ->get();

        foreach ($users as $key => $user) {
            $wallet = DB::table('e_wallet')->where('UserID', $user->UserID)->first();
            $cap_amount = $wallet->cap_per_visit_medical;
            $final_user[] = array(
                'Member ID'   => $user->UserID,
                'Employee Name'      => ucwords($user->Name),
                'Cap Per Visit'    => $cap_amount > 0 ? $cap_amount : "Not Applicable"
            );
        }

        return \Excel::create('Employee Cap Per Visit', function($excel) use($final_user) {
            $excel->sheet('Cap Per Visit', function($sheet) use($final_user) {
                $sheet->fromArray( $final_user );
            });
        })->export('csv');
    }

    public function uploadCaperPervisit( )
    {
        $input = Input::all();
        $result = StringHelper::getJwtHrSession();
        $customer_id = $result->customer_buy_start_id;
        $admin_id = Session::get('admin-session-id');
        $hr_id = $result->hr_dashboard_id;
        $per_page = isset($input['per_page']) || !empty($input['per_page']) ? $input['per_page'] : 25;

        if(Input::hasFile('file')) {
            $account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
            $file = Input::file('file');
            $temp_file = time().$file->getClientOriginalName();
            $file->move('excel_upload', $temp_file);
            $data_array = Excel::load(public_path()."/excel_upload/".$temp_file)->get();
            $headerRow = $data_array->first()->keys();
            
            $memberid = false;
            $name = false;
            $cap = false;
            foreach ($headerRow as $key => $row) {
                if($row == "member_id") {
                    $memberid = true;
                } else if($row == "employee_name") {
                    $name = true;
                } else if($row == "cap_per_visit") {
                    $cap = true;
                }
            }

            if(!$memberid || !$name || !$cap) {
                return array(
                    'status'    => FALSE,
                    'message' => 'Excel is invalid format. Please download the recommended file for Employee Cap Per Visit.'
                );
            }

            foreach ($data_array as $key => $user) {
                if($user['member_id'] || $user['member_id'] != null) {
                    // check user
                    $member = DB::table('user')->where('UserID', $user['member_id'])->first();
                    if(!$member) {
                        return array('status' => false, 'message' => 'Member with ID '.$user['member_id'].' does not exist');
                    }

                    // check if user is assign to company
                    $check_member = DB::table('corporate_members')->where('user_id', $user['member_id'])->first();
                    if(!$check_member) {
                        return array('status' => false, 'message' => 'Member with ID '.$user['member_id'].' - '.$user['employee_name'].' is not assigned to this company');
                    }

                    $cap_amount = 0;
                    if(is_numeric($user['cap_per_visit'])) {
                        $cap_amount = $user['cap_per_visit'];
                        $result = DB::table('e_wallet')->where('UserID', $user['member_id'])->update(['cap_per_visit_medical' => $cap_amount, 'updated_at' => date('Y-m-d H:i:s')]);
                        if($admin_id) {
                            $admin_logs = array(
                                'admin_id'  => $admin_id,
                                'admin_type' => 'mednefits',
                                'type'      => 'admin_updated_cap_per_visit_cap',
                                'data'      => SystemLogLibrary::serializeData($cap)
                            );
                            SystemLogLibrary::createAdminLog($admin_logs);
                        } else {
                            $admin_logs = array(
                                'admin_id'  => $hr_id,
                                'admin_type' => 'hr',
                                'type'      => 'admin_updated_cap_per_visit_cap',
                                'data'      => SystemLogLibrary::serializeData($cap)
                            );
                            SystemLogLibrary::createAdminLog($admin_logs);
                        }
                    }
                }
            }

            return array('status' => true, 'message' => 'Employee Cap Per Visit updated');
        } else {
            return array('status' => false, 'message' => 'File is required');
        }


    }
}
