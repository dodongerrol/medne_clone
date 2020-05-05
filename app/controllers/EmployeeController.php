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
        // StringHelper::TestSendOTPSMS($phone, $otp_code);
        $data = array();
        $data['phone'] = $phone;
        $data['message'] = $otp_code.' is your Mednefits verification code.';
        $data['sms_type'] = "LA";
        SmsHelper::sendSms($data);
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
                            ->where('Name', 'like', '%'.strtolower($input['search']).'%')
                            ->where('Active', 1)
                            ->whereIn('currency_type', ["sgd", "myr"])
                            ->orderBy('Created_on', 'desc')
                            ->get();
                  } else {
                    $clinics = DB::table('clinic')
                            ->whereNotIn('ClinicID', $new_array)
                            ->where('Name', 'like', '%'.strtolower($input['search']).'%')
                            ->where('Active', 1)
                            ->where('currency_type', $input['region'])
                            ->orderBy('Created_on', 'desc')
                            ->get();
                  }

              } else {
                if($input['region'] == "all_region") {
                    $clinics = DB::table('clinic')
                            ->where('Name', 'like', '%'.strtolower($input['search']).'%')
                            ->where('Active', 1)
                            ->whereIn('currency_type', ["sgd", "myr"])
                            ->orderBy('Created_on', 'desc')
                            ->get();
                    return $clinics;
                  } else {
                    $clinics = DB::table('clinic')
                            ->where('Name', 'like', '%'.strtolower($input['search']).'%')
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
                        ->whereIn('currency_type', ["sgd", "myr"])
                        ->orderBy('Created_on', 'desc')
                        ->get();
            } else {
                $clinics = DB::table('clinic')
                        ->where('Name', 'like', '%'.$input['search'].'%')
                        ->where('Active', 1)
                        ->whereIn('currency_type', ["sgd", "myr"])
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
          
          $clinic_datas = array();

          if($input['access_status'] == "block") {
            foreach ($clinic_ids as $key => $clinic_id) {
                $id = $clinic_id->ClinicID;
                array_push($clinic_datas, $id);
              // check if clinic block already exits
              $check = DB::table('company_block_clinic_access')
                        ->where('customer_id', $customer_id)
                        ->where('account_type', 'company')
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

            // process queue
            // Queue::connection('redis_high')->push('\BlockClinicProcessQueue', array('customer_id' => $customer_id, 'ids' => $clinic_datas));
            BlockClinicProcessQueue::execute(array('customer_id' => $customer_id, 'ids' => $clinic_datas), 1);
          } else {
            foreach ($clinic_ids as $key => $clinic_id) {
                $id = $clinic_id->ClinicID;
                array_push($clinic_datas, $id);
              // check if clinic block already exits
              $check = DB::table('company_block_clinic_access')
                        ->where('customer_id', $customer_id)
                        ->where('clinic_id', $id)
                        ->where('account_type', 'company')
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
            BlockClinicProcessQueue::execute(array('customer_id' => $customer_id, 'ids' => $clinic_datas), 0);
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

            $check = DB::table('company_block_clinic_access')
                        ->where('clinic_id', $input['clinic_id'])
                      ->where('customer_id', $customer_id)
                      ->where('account_type', 'company')
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

            BlockClinicProcessQueue::execute(array('customer_id' => $customer_id, 'ids' => [$input['clinic_id']]), $status);

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

        $check = $customer = DB::table('user')
                    ->where('UserID', $customer_id)
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
                'cap_amount'    => $cap_amount == null ? 0 : $cap_amount, 
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

    public function getMemberEntitlement( )
    {
        $input = Input::all();
        $result = StringHelper::getJwtHrSession();

        if(empty($input['member_id']) || $input['member_id'] == null) {
            return array('status' => false, 'message' => 'member_id is required');
        }

        $entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $input['member_id'])->orderBy('created_at', 'desc')->orderBy('employee_wallet_entitlement_id', 'desc')->first();
        // / check for existing entitlement
        $check_entitlement_medical = DB::table('wallet_entitlement_schedule')
                                ->where('member_id', $input['member_id'])
                                ->where('spending_type', 'medical')
                                ->where('status', 1)
                                ->orderBy('created_at', 'desc')
                                ->first();

        $check_entitlement_wellness = DB::table('wallet_entitlement_schedule')
                                ->where('member_id', $input['member_id'])
                                ->where('spending_type', 'wellness')
                                ->where('status', 1)
                                ->orderBy('created_at', 'desc')
                                ->first();
        $wallet = DB::table('e_wallet')->where('UserID', $input['member_id'])->first();
        $medical  = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $input['member_id']);
        $wellness  = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $input['member_id']);

        if($check_entitlement_medical || $check_entitlement_wellness) {
            $medical_calculation = array();
            $wellness_calculation = array();
            if($check_entitlement_medical && $check_entitlement_wellness) {
                $data = array(
                    'status' => true,
                    'employee_wallet_entitlement_id' => $entitlement->employee_wallet_entitlement_id,
                    'member_id' => $input['member_id'],
                    'original_medical_entitlement' => DecimalHelper::formatDecimal($medical['allocation']),
                    'old_medical_entitlement' => DecimalHelper::formatDecimal($check_entitlement_medical->old_entitlement_credits),
                    'medical_entitlement_date' => $entitlement->medical_usage_date,
                    'medical_proration'        => $entitlement->medical_proration,
                    'original_wellness_entitlement' => DecimalHelper::formatDecimal($wellness['allocation']),
                    'old_wellness_entitlement' => DecimalHelper::formatDecimal($check_entitlement_wellness->old_entitlement_credits),
                    'wellness_entitlement_date' => $entitlement->wellness_usage_date,
                    'wellness_proration'        => $entitlement->wellness_proration,
                    'updated_medical_entitlement' => true,
                    'updated_wellness_entitlement' => true,
                    'currency_type'                => strtoupper($entitlement->currency_type)
                );
            } else if($check_entitlement_medical) {
                $data = array(
                    'status' => true,
                    'employee_wallet_entitlement_id' => $entitlement->employee_wallet_entitlement_id,
                    'member_id' => $input['member_id'],
                    'original_medical_entitlement' => DecimalHelper::formatDecimal($medical['allocation']),
                    'old_medical_entitlement' => DecimalHelper::formatDecimal($check_entitlement_medical->old_entitlement_credits),
                    'medical_entitlement_date' => $entitlement->medical_usage_date,
                    'medical_proration'        => $entitlement->medical_proration,
                    'original_wellness_entitlement' => DecimalHelper::formatDecimal($wellness['allocation']),
                    'wellness_entitlement_date' => $entitlement->wellness_usage_date,
                    'wellness_proration'        => $entitlement->wellness_proration,
                    'updated_medical_entitlement' => true,
                    'updated_wellness_entitlement' => false,
                    'currency_type'                => strtoupper($entitlement->currency_type)
                );
            } else {
                $data = array(
                    'status' => true,
                    'employee_wallet_entitlement_id' => $entitlement->employee_wallet_entitlement_id,
                    'member_id' => $input['member_id'],
                    'original_medical_entitlement' => DecimalHelper::formatDecimal($medical['allocation']),
                    'medical_entitlement_date' => $entitlement->medical_usage_date,
                    'medical_proration'        => $entitlement->medical_proration,
                    'original_wellness_entitlement' => DecimalHelper::formatDecimal($wellness['allocation']),
                    'old_wellness_entitlement' => DecimalHelper::formatDecimal($check_entitlement_wellness->old_entitlement_credits),
                    'wellness_entitlement_date' => $entitlement->wellness_usage_date,
                    'wellness_proration'        => $entitlement->wellness_proration,
                    'updated_medical_entitlement' => false,
                    'updated_wellness_entitlement' => true,
                    'currency_type'                => strtoupper($entitlement->currency_type)
                );
            }
        } else {
            $data = array(
                'status' => true,
                'employee_wallet_entitlement_id' => $entitlement->employee_wallet_entitlement_id,
                'member_id' => $input['member_id'],
                'original_medical_entitlement' => DecimalHelper::formatDecimal($medical['allocation']),
                'old_medical_entitlement' => DecimalHelper::formatDecimal($entitlement->medical_entitlement),
                'medical_entitlement_date' => $entitlement->medical_usage_date,
                'medical_proration'        => $entitlement->medical_proration,
                'original_wellness_entitlement' => DecimalHelper::formatDecimal($wellness['allocation']),
                'old_wellness_entitlement' => DecimalHelper::formatDecimal($entitlement->wellness_entitlement),
                'wellness_entitlement_date' => $entitlement->wellness_usage_date,
                'wellness_proration'        => $entitlement->wellness_proration,
                'updated_medical_entitlement' => false,
                'updated_wellness_entitlement' => false,
                'currency_type'                => strtoupper($entitlement->currency_type)
            );
        }

        return $data;
    }

    public function calculateProRation( )
    {
        $input = Input::all();

        if(empty($input['member_id']) || $input['member_id'] == null) {
            return array('status' => false, 'message' => 'member_id is required');
        }

        if(empty($input['new_entitlement_credits']) || $input['new_entitlement_credits'] == null) {
            return array('status' => false, 'message' => 'new_entitlement_credits is required');
        }

        if(empty($input['entitlement_usage_date']) || $input['entitlement_usage_date'] == null) {
            return array('status' => false, 'message' => 'entitlement_usage_date is required');
        }

        if(empty($input['proration_type']) || $input['proration_type'] == null) {
            return array('status' => false, 'message' => 'proration_type is required');
        }

        if(empty($input['entitlement_spending_type']) || $input['entitlement_spending_type'] == null) {
            return array('status' => false, 'message' => 'entitlement_spending_type is required');
        }

        if(!in_array($input['proration_type'], ['days', 'months'])) {
            return array('status' => false, 'message' => 'proration_type must be days or months');
        }

        if(!in_array($input['entitlement_spending_type'], ['medical', 'wellness'])) {
            return array('status' => false, 'message' => 'entitlement_spending_type must be medical or wellness');
        }

        $member = DB::table('user')->where('UserID', $input['member_id'])->where('UserType', 5)->first();

        if(!$member) {
            return array('status' => false, 'message' => 'Member does not exist');
        }

        $wallet_entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $input['member_id'])->orderBy('created_at', 'desc')->first();

        if(!$wallet_entitlement) {
            return array('status' => false, 'message' => 'member wallet entitlement does not exist');
        }

        $customer_id = PlanHelper::getCustomerId($input['member_id']);
        // get customer spending account
        $plan_dates = [];
        $entitlement_usage_date = date('Y-m-d', strtotime($input['entitlement_usage_date']));
        $spending_account_company = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
        // get user plan dates
        // $plan_dates = PlanHelper::checkEmployeePlanStatus($input['member_id']);
        // plan duration'


        if($input['entitlement_spending_type'] == 'medical') {
            if($entitlement_usage_date > $spending_account_company->medical_spending_end_date) {
                return array('status' => false, 'message' => 'New Medical Entitlement Usage Date exceeded the Spending End Date.');
            }
            $plan_dates['valid_date'] = $spending_account_company->medical_spending_end_date;
            $plan_duration = new DateTime($wallet_entitlement->medical_usage_date);
            $plan_duration = $plan_duration->diff(new DateTime(date('Y-m-d', strtotime($plan_dates['valid_date']))));
            
            $medical_duration_start = new DateTime($wallet_entitlement->medical_usage_date);
            $medical_months = $medical_duration_start->diff(new DateTime(date('Y-m-d', strtotime($input['entitlement_usage_date']))));

            $entitlement_duration = new DateTime($input['entitlement_usage_date']);
            $entitlement_duration = $entitlement_duration->diff(new DateTime(date('Y-m-d', strtotime($plan_dates['valid_date']))));

            if($input['proration_type'] == "months") {
                $plan_month_duration = $medical_months->m + 1;
                $entitlement_duration = $entitlement_duration->m;
                $plan_duration = $plan_duration->m + 1;
            } else {
                $plan_month_duration = $medical_months->days;
                $entitlement_duration = $entitlement_duration->days + 1;
                $plan_duration = $plan_duration->days + 1;
            }

            $old_entitlement_credits = $wallet_entitlement->medical_entitlement;
            $new_entitlement_credits = ($wallet_entitlement->medical_entitlement * $plan_month_duration / $plan_duration) + ($input['new_entitlement_credits'] * $entitlement_duration / $plan_duration);
        } else {
            if($entitlement_usage_date > $spending_account_company->wellness_spending_end_date) {
                return array('status' => false, 'message' => 'New Wellness Entitlement Usage Date exceeded the Spending End Date.');
            }
            $plan_dates['valid_date'] = $spending_account_company->wellness_spending_end_date;
            $plan_duration = new DateTime($wallet_entitlement->wellness_usage_date);
            $plan_duration = $plan_duration->diff(new DateTime(date('Y-m-d', strtotime($plan_dates['valid_date']))));
            
            $wellness_duration_start = new DateTime($wallet_entitlement->wellness_usage_date);
            $wellness_months = $wellness_duration_start->diff(new DateTime(date('Y-m-d', strtotime($input['entitlement_usage_date']))));

            $entitlement_duration = new DateTime($input['entitlement_usage_date']);
            $entitlement_duration = $entitlement_duration->diff(new DateTime(date('Y-m-d', strtotime($plan_dates['valid_date']))));

            if($input['proration_type'] == "months") {
                $plan_month_duration = $wellness_months->m + 1;
                $entitlement_duration = $entitlement_duration->m;
                $plan_duration = $plan_duration->m + 1;
            } else {
                $plan_month_duration = $wellness_months->days;
                $entitlement_duration = $entitlement_duration->days + 1;
                $plan_duration = $plan_duration->days + 1;
            }

            $old_entitlement_credits = $wallet_entitlement->wellness_entitlement;
            $new_entitlement_credits = ($wallet_entitlement->wellness_entitlement * $plan_month_duration / $plan_duration) + ($input['new_entitlement_credits'] * $entitlement_duration / $plan_duration);
        }

       return[
        'new_allocation'            => DecimalHelper::formatDecimal($new_entitlement_credits),
        'old_entitlement_credits'   => DecimalHelper::formatDecimal($old_entitlement_credits),
        'new_entitlement_credits'   => $input['new_entitlement_credits'],
        'plan_month_duration'       => $plan_month_duration,
        'plan_year_duration'        => $plan_duration,
        'entitlement_duration'      => $entitlement_duration,
        'currency_type'             => strtoupper($wallet_entitlement->currency_type),
        'entitlement_spending_type' => $input['entitlement_spending_type'],
        'plan_dates'                => $plan_dates,
        'spending_account_company'  => $spending_account_company
       ];
    }

    public function createNewEntitlement( )
    {
        $input = Input::all();

        if(empty($input['member_id']) || $input['member_id'] == null) {
            return array('status' => false, 'message' => 'member_id is required');
        }

        // if(empty($input['new_entitlement_credits']) || $input['new_entitlement_credits'] == null) {
        //     return array('status' => false, 'message' => 'new_entitlement_credits is required');
        // }

        if(empty($input['entitlement_usage_date']) || $input['entitlement_usage_date'] == null) {
            return array('status' => false, 'message' => 'entitlement_usage_date is required');
        }

        if(empty($input['proration_type']) || $input['proration_type'] == null) {
            return array('status' => false, 'message' => 'proration_type is required');
        }

        if(empty($input['entitlement_spending_type']) || $input['entitlement_spending_type'] == null) {
            return array('status' => false, 'message' => 'entitlement_spending_type is required');
        }

        if(!in_array($input['proration_type'], ['days', 'months'])) {
            return array('status' => false, 'message' => 'proration_type must be days or months');
        }

        if(!in_array($input['entitlement_spending_type'], ['medical', 'wellness'])) {
            return array('status' => false, 'message' => 'entitlement_spending_type must be medical or wellness');
        }

        $member = DB::table('user')->where('UserID', $input['member_id'])->where('UserType', 5)->first();

        if(!$member) {
            return array('status' => false, 'message' => 'Member does not exist');
        }

        $wallet_entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $input['member_id'])->orderBy('created_at', 'desc')->first();

        if(!$wallet_entitlement) {
            return array('status' => false, 'message' => 'member wallet entitlement does not exist');
        }

        $customer_id = PlanHelper::getCustomerId($input['member_id']);
        // check for existing entitlement
        $check_entitlement = DB::table('wallet_entitlement_schedule')
                                ->where('member_id', $input['member_id'])
                                ->where('spending_type', $input['entitlement_spending_type'])
                                ->whereIn('status', [0, 1])
                                ->orderBy('created_at', 'desc')
                                ->first();

        if($check_entitlement && (int)$check_entitlement->status == 0) {
            return array('status' => false, 'message' => 'Member has still a schedule new entitlement');
        } else if($check_entitlement && (int)$check_entitlement->status == 1) {
            return array('status' => false, 'message' => 'Member has already have a '.strtoupper($input['entitlement_spending_type']).' new entitlement');
        }

        $today = date('Y-m-d');
        $new_usage_date = date('Y-m-d', strtotime($input['entitlement_usage_date']));
        // get user plan dates
        // $plan_dates = PlanHelper::checkEmployeePlanStatus($input['member_id']);
        // $plan_dates = DB::table('employee_wallet_entitlement')->where('member_id', $input['member_id'])->orderBy('created_at', 'desc')->first();
        // get customer spending account
        $plan_dates = [];
        $spending_account_company = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
        if($input['entitlement_spending_type'] == 'medical') {
            if($new_usage_date > $spending_account_company->medical_spending_end_date) {
                return array('status' => false, 'message' => 'New Medical Entitlement Usage Date exceeded the Spending End Date.');
            }
            $plan_dates['valid_date'] = $spending_account_company->medical_spending_end_date;
            $plan_duration = new DateTime($wallet_entitlement->medical_usage_date);
            $plan_duration = $plan_duration->diff(new DateTime(date('Y-m-d', strtotime($plan_dates['valid_date']))));
            
            $medical_duration_start = new DateTime($wallet_entitlement->medical_usage_date);
            $medical_months = $medical_duration_start->diff(new DateTime(date('Y-m-d', strtotime($input['entitlement_usage_date']))));

            $entitlement_duration = new DateTime($input['entitlement_usage_date']);
            $entitlement_duration = $entitlement_duration->diff(new DateTime(date('Y-m-d', strtotime($plan_dates['valid_date']))));

            if($input['proration_type'] == "months") {
                $plan_month_duration = $medical_months->m + 1;
                $entitlement_duration = $entitlement_duration->m;
                $plan_duration = $plan_duration->m + 1;
            } else {
                $plan_month_duration = $medical_months->days;
                $entitlement_duration = $entitlement_duration->days + 1;
                $plan_duration = $plan_duration->days + 1;
            }

            
            $new_entitlement_credits = ($wallet_entitlement->medical_entitlement * $plan_month_duration / $plan_duration) + ($input['new_entitlement_credits'] * $entitlement_duration / $plan_duration);

            $data = array(
                'member_id'                 => $input['member_id'],
                'new_usage_date'            => date('Y-m-d', strtotime($input['entitlement_usage_date'])),
                'old_usage_date'            => date('Y-m-d', strtotime($wallet_entitlement->medical_usage_date)),
                'proration'                 => $input['proration_type'],
                'new_allocation_credits'    => $new_entitlement_credits,
                'new_entitlement_credits'   => $input['new_entitlement_credits'],
                'old_entitlement_credits'   => $wallet_entitlement->medical_entitlement,
                'plan_end'                  => date('Y-m-d', strtotime($plan_dates['valid_date'])),
                'effective_date'            => date('Y-m-d', strtotime($input['entitlement_usage_date'])),
                'spending_type'             => $input['entitlement_spending_type'],
                'created_at'                => date('Y-m-d H:i:s'),
                'updated_at'                => date('Y-m-d H:i:s')
            );
        } else {
            if($new_usage_date > $spending_account_company->wellness_spending_end_date) {
                return array('status' => false, 'message' => 'New Wellness Entitlement Usage Date exceeded the Spending End Date.');
            }
            $plan_dates['valid_date'] = $spending_account_company->wellness_spending_end_date;
            $plan_duration = new DateTime($wallet_entitlement->wellness_usage_date);
            $plan_duration = $plan_duration->diff(new DateTime(date('Y-m-d', strtotime($plan_dates['valid_date']))));
            
            $wellness_duration_start = new DateTime($wallet_entitlement->wellness_usage_date);
            $wellness_months = $wellness_duration_start->diff(new DateTime(date('Y-m-d', strtotime($input['entitlement_usage_date']))));

            $entitlement_duration = new DateTime($input['entitlement_usage_date']);
            $entitlement_duration = $entitlement_duration->diff(new DateTime(date('Y-m-d', strtotime($plan_dates['valid_date']))));

            if($input['proration_type'] == "months") {
                $plan_month_duration = $wellness_months->m + 1;
                $entitlement_duration = $entitlement_duration->m;
                $plan_duration = $plan_duration->m + 1;
            } else {
                $plan_month_duration = $wellness_months->days;
                $entitlement_duration = $entitlement_duration->days + 1;
                $plan_duration = $plan_duration->days + 1;
            }

            
            $new_entitlement_credits = ($wallet_entitlement->wellness_entitlement * $plan_month_duration / $plan_duration) + ($input['new_entitlement_credits'] * $entitlement_duration / $plan_duration);

            $data = array(
                'member_id'                 => $input['member_id'],
                'new_usage_date'            => date('Y-m-d', strtotime($input['entitlement_usage_date'])),
                'old_usage_date'            => date('Y-m-d', strtotime($wallet_entitlement->wellness_usage_date)),
                'proration'                 => $input['proration_type'],
                'new_allocation_credits'    => $new_entitlement_credits,
                'new_entitlement_credits'   => $input['new_entitlement_credits'],
                'old_entitlement_credits'   => $wallet_entitlement->wellness_entitlement,
                'plan_end'                  => date('Y-m-d', strtotime($plan_dates['valid_date'])),
                'effective_date'            => date('Y-m-d', strtotime($input['entitlement_usage_date'])),
                'spending_type'             => $input['entitlement_spending_type'],
                'created_at'                => date('Y-m-d H:i:s'),
                'updated_at'                => date('Y-m-d H:i:s')
            );
        }

        $new_entitlment = new NewEmployeeEntitlementSchedule();
        $result = $new_entitlment->createData($data);
        if($result) {
            if($today >= $new_usage_date) {
                // activate now
                MemberHelper::activateNewEntitlement($input['member_id'], $result->id);
            }

            return array('status' => true, 'message' => 'New Entitlement has been created');
        } else {
            return array('status' => false, 'message' => 'Failed to create new entitlement for member');
        }
    }

    public function createNewAllocation( )
    {
        $input = Input::all();
        $result = StringHelper::getJwtHrSession();
        $admin_id = Session::get('admin-session-id');
        $hr_id = $result ? $result->hr_dashboard_id : null;
        if(empty($input['member_id']) || $input['member_id'] == null) {
            return array('status' => false, 'message' => 'member_id is required');
        }

        // if(empty($input['new_allocation_credits']) || $input['new_allocation_credits'] == null) {
        //     return array('status' => false, 'message' => 'new_allocation_credits is required');
        // }

        if(empty($input['effective_date']) || $input['effective_date'] == null) {
            return array('status' => false, 'message' => 'effective_date is required');
        }

        if(empty($input['spending_type']) || $input['spending_type'] == null) {
            return array('status' => false, 'message' => 'spending_type is required');
        }

        if(!in_array($input['spending_type'], ['medical', 'wellness'])) {
            return array('status' => false, 'message' => 'spending_type must be medical or wellness');
        }

        $member = DB::table('user')->where('UserID', $input['member_id'])->where('UserType', 5)->first();

        if(!$member) {
            return array('status' => false, 'message' => 'Member does not exist');
        }

        $wallet_entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $input['member_id'])->orderBy('created_at', 'desc')->first();

        if(!$wallet_entitlement) {
            return array('status' => false, 'message' => 'member wallet entitlement does not exist');
        }

        $customer_id = PlanHelper::getCustomerId($input['member_id']);
        $spending = CustomerHelper::getAccountSpendingStatus($customer_id);
        $customer_credits = DB::table('customer_credits')->where("customer_id", $customer_id)->first();
        // check for existing entitlement
        $check_entitlement = DB::table('wallet_entitlement_schedule')
                                ->where('member_id', $input['member_id'])
                                ->where('spending_type', $input['spending_type'])
                                ->where('status', 0)
                                ->orderBy('created_at', 'desc')
                                ->first();

        $today = date('Y-m-d');
        $new_usage_date = date('Y-m-d', strtotime($input['effective_date']));
        $wallet = DB::table('e_wallet')->where('UserID', $input['member_id'])->first();

        $plan_dates = [];
        $spending_account_company = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
        $id = null;

        if($input['spending_type'] == 'medical') {
          if($spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" && $spending['paid_status'] == false) {
            return ['status' => FALSE, 'message' => 'Unable to allocate medical credits since your company is not yet paid for the Plan. Please make payment to enable medical allocation.'];
          }
        } else {
          if($spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "pre_paid" && $spending['paid_status'] == false) {
            return ['status' => FALSE, 'message' => 'Unable to allocate wellness credits since your company is not yet paid for the Plan. Please make payment to enable wellness allocation.'];
          }
        }

        if($check_entitlement) {
            if($input['spending_type'] == 'medical') {
              if($new_usage_date > $spending_account_company->medical_spending_end_date) {
                return array('status' => false, 'message' => 'New Medical Entitlement Usage Date exceeded the Spending End Date.');
              }
              $medical_credit_data = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $input['member_id']);
              $new_allocation = $input['new_allocation_credits'] - $medical_credit_data['allocation'];

              if($spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" && $spending['paid_status'] == true) {  
                if((float)$input['new_allocation_credits'] > $medical_credit_data['allocation']) {
                  // check medical balance
                  if($new_allocation > $customer_credits->balance) {
                    return ['status' => FALSE, 'message' => 'Company Medical Balance is not sufficient for this Member', 'credit_balance_exceed' => true];
                  }
                }
              } else {
                // if($new_allocation > $customer_credits->medical_supp_credits) {
                //   return ['status' => FALSE, 'message' => 'Company Medical Balance is not sufficient for this Member', 'credit_balance_exceed' => true];
                // }
              }
            } else {
              if($new_usage_date > $spending_account_company->wellness_spending_end_date) {
                return array('status' => false, 'message' => 'New Wellness Entitlement Usage Date exceeded the Spending End Date.');
              }

              $wellness_credit_data = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $input['member_id']);
              $new_allocation = $input['new_allocation_credits'] - $wellness_credit_data['allocation'];
              if($spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "pre_paid" && $spending['paid_status'] == true) {
                if((float)$input['new_allocation_credits'] > $wellness_credit_data['allocation']) {
                  // check medical balance
                  if($new_allocation > $customer_credits->wellness_credits) {
                    return ['status' => FALSE, 'message' => 'Company Wellness Balance is not sufficient for this Member', 'credit_balance_exceed' => true];
                  }
                }
              } else {
                // if($new_allocation > $customer_credits->wellness_supp_credits) {
                //   return ['status' => FALSE, 'message' => 'Company Wellness Balance is not sufficient for this Member', 'credit_balance_exceed' => true];
                // }
              }
            }

            $data = array(
                'new_usage_date'            => $new_usage_date,
                'effective_date'            => $new_usage_date,
                'new_allocation_credits'    => $input['new_allocation_credits'],
                'new_entitlement_credits'   => $input['new_allocation_credits'],
                'updated_at'                => date('Y-m-d H:i:s')
            );
            DB::table('wallet_entitlement_schedule')->where('wallet_entitlement_schedule_id', $check_entitlement->wallet_entitlement_schedule_id)->update($data);
            $id = $check_entitlement->wallet_entitlement_schedule_id;
            $result = $check_entitlement;
        } else {
            if($input['spending_type'] == 'medical') {
                if($new_usage_date > $spending_account_company->medical_spending_end_date) {
                    return array('status' => false, 'message' => 'New Medical Entitlement Usage Date exceeded the Spending End Date.');
                }
                $plan_dates['valid_date'] = $spending_account_company->medical_spending_end_date;
                $medical_credit_data = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $input['member_id']);
                $credits = $medical_credit_data['allocation'];
                $new_allocation = $input['new_allocation_credits'] - $medical_credit_data['allocation'];
                if($spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" && $spending['paid_status'] == true) {
                  if((float)$input['new_allocation_credits'] > $credits) {
                    // check medical balance
                    if($new_allocation > $customer_credits->balance) {
                      return ['status' => FALSE, 'message' => 'Company Medical Balance is not sufficient for this Member'];
                    }
                  }
                } else {
                  // if($new_allocation > $customer_credits->medical_supp_credits) {
                  //   return ['status' => FALSE, 'message' => 'Company Medical Balance is not sufficient for this Member', 'credit_balance_exceed' => true];
                  // }
                }
                $data = array(
                    'member_id'                 => $input['member_id'],
                    'new_usage_date'            => $new_usage_date,
                    'old_usage_date'            => date('Y-m-d', strtotime($wallet_entitlement->medical_usage_date)),
                    'proration'                 => 'months',
                    'new_allocation_credits'    => $input['new_allocation_credits'],
                    'new_entitlement_credits'   => $input['new_allocation_credits'],
                    'old_entitlement_credits'   => $credits,
                    'plan_end'                  => date('Y-m-d', strtotime($plan_dates['valid_date'])),
                    'effective_date'            => $new_usage_date,
                    'spending_type'             => $input['spending_type'],
                    'created_at'                => date('Y-m-d H:i:s'),
                    'updated_at'                => date('Y-m-d H:i:s')
                );

            } else {
                if($new_usage_date > $spending_account_company->wellness_spending_end_date) {
                    return array('status' => false, 'message' => 'New Wellness Entitlement Usage Date exceeded the Spending End Date.');
                }
                $plan_dates['valid_date'] = $spending_account_company->wellness_spending_end_date;
                $wellness_credit_data = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $input['member_id']);
                $credits = $wellness_credit_data['allocation'];
                $new_allocation = $input['new_allocation_credits'] - $credits;
                if($spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "pre_paid" && $spending['paid_status'] == true) {
                  if((float)$input['new_allocation_credits'] > $credits) {
                    // check medical balance
                    if($new_allocation > $customer_credits->wellness_credits) {
                      return ['status' => FALSE, 'message' => 'Company Wellness Balance is not sufficient for this Member'];
                    }
                  }
                } else {
                  // if($new_allocation > $customer_credits->wellness_supp_credits) {
                  //   return ['status' => FALSE, 'message' => 'Company Wellness Balance is not sufficient for this Member', 'credit_balance_exceed' => true];
                  // }
                }

                $data = array(
                    'member_id'                 => $input['member_id'],
                    'new_usage_date'            => $new_usage_date,
                    'old_usage_date'            => date('Y-m-d', strtotime($wallet_entitlement->wellness_usage_date)),
                    'proration'                 => 'months',
                    'new_allocation_credits'    => $input['new_allocation_credits'],
                    'new_entitlement_credits'   => $input['new_allocation_credits'],
                    'old_entitlement_credits'   => $credits,
                    'plan_end'                  => date('Y-m-d', strtotime($plan_dates['valid_date'])),
                    'effective_date'            => $new_usage_date,
                    'spending_type'             => $input['spending_type'],
                    'created_at'                => date('Y-m-d H:i:s'),
                    'updated_at'                => date('Y-m-d H:i:s')
                );
            }
            $new_entitlment = new NewEmployeeEntitlementSchedule();
            $result = $new_entitlment->createData($data);
            $id = $result->id;
        }
        

        if($result) {
            if($today >= $new_usage_date) {
                // activate now
                MemberHelper::activateNewEntitlement($input['member_id'], $id);
            }
            if($admin_id) {
                $admin_logs = array(
                    'admin_id'  => $admin_id,
                    'admin_type' => 'mednefits',
                    'type'      => 'admin_create_employee_new_allocation',
                    'data'      => SystemLogLibrary::serializeData($result)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
            } else {
                $admin_logs = array(
                    'admin_id'  => $hr_id,
                    'admin_type' => 'hr',
                    'type'      => 'admin_create_employee_new_allocation',
                    'data'      => SystemLogLibrary::serializeData($result)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
            }
            return array('status' => true, 'message' => 'New Credits Allocation has been created');
        } else {
            return array('status' => false, 'message' => 'Failed to create new credits allocation for member');
        }
    }

    public function entitlementStatus( )
    {
        $input = Input::all();

        if(empty($input['member_id']) || $input['member_id'] == null) {
            return array('status' => false, 'message' => 'member_id is required');
        }

        $member = DB::table('user')->where('UserID', $input['member_id'])->where('UserType', 5)->first();

        if(!$member) {
            return array('status' => false, 'message' => 'Member does not exist');
        }

        $wallet = DB::table('e_wallet')->where('UserID', $input['member_id'])->first();
        $wallet_entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $input['member_id'])->orderBy('created_at', 'desc')->first();

        if(!$wallet_entitlement) {
            return array('status' => false, 'message' => 'member wallet entitlement does not exist');
        }

        $schedule_medical =  DB::table('wallet_entitlement_schedule')
                            ->where('member_id', $input['member_id'])
                            ->where('spending_type', 'medical')
                            ->where('status', 0)
                            ->orderBy('created_at', 'desc')
                            ->first();

        $schedule_wellness =  DB::table('wallet_entitlement_schedule')
                            ->where('member_id', $input['member_id'])
                            ->where('spending_type', 'wellness')
                            ->where('status', 0)
                            ->orderBy('created_at', 'desc')
                            ->first();

        if($schedule_medical || $schedule_wellness) {
            $medical_entitlement = null;
            $wellness_entitlement = null;
            if($schedule_medical) {
                $medical_entitlement = array(
                    'wallet_entitlement_schedule_id'    => $schedule_medical->wallet_entitlement_schedule_id,
                    'member_id'                         => $schedule_medical->member_id,
                    'new_entitlement_credits'           => $schedule_medical->new_entitlement_credits,
                    'new_allocation_credits'           => $schedule_medical->new_allocation_credits,
                    'effective_date'                    => $schedule_medical->effective_date,
                    'currency_type'                     => $wallet->currency_type
                );
            }

            if($schedule_wellness) {
                $wellness_entitlement = array(
                    'wallet_entitlement_schedule_id'    => $schedule_wellness->wallet_entitlement_schedule_id,
                    'member_id'                         => $schedule_wellness->member_id,
                    'new_entitlement_credits'           => $schedule_wellness->new_entitlement_credits,
                    'new_allocation_credits'           => $schedule_wellness->new_allocation_credits,
                    'effective_date'                    => $schedule_wellness->effective_date,
                    'currency_type'                     => $wallet->currency_type
                );
            }

            return array('status' => true, 'medical_entitlement' => $medical_entitlement, 'wellness_entitlement' => $wellness_entitlement);
        } else {
            return array('status' => false, 'message' => 'No entitlement schedule');
        }
    }

    public function getEmployeeDateTerms( )
    {
        $input = Input::all();
        $employee = StringHelper::getEmployeeSession( );
        $user_id = $employee->UserID;
        $current_term = MemberHelper::getMemberCreditReset($user_id, 'current_term', 'medical');
        $last_term = MemberHelper::getMemberCreditReset($user_id, 'last_term', 'medical');

        return ['status' => true, 'current_term' => $current_term, 'last_term' => $last_term];
    }

    public function downloadEmployeeBulkLists( )
    {
        $input = Input::all();
        $result = StringHelper::getJwtHrToken($input['token']);
        $customer_id = $result->customer_buy_start_id;
        $customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

        if(!$customer) {
          return array('status' => false, 'message' => 'customer_id is required');
        }

        $spending = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderby('created_at', 'desc')->first();
        $account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $spending->customer_id)->first();
        $members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->where('removed_status', 0)->get();
        $medical = (int)$spending->medical_enable == 1 ? true : false;
        $wellness = (int)$spending->wellness_enable == 1 ? true : false;


        $container = array();
        foreach ($members as $key => $member) {
          $user = DB::table('user')->where('UserID', $member->user_id)->first();
          // $entitlment_allocation = DB::table('employee_wallet_entitlement')->where('member_id', $member->user_id)->orderBy('created_at', 'desc')->first();
          $wallet = DB::table('e_wallet')->where('UserID', $member->user_id)->first();
          $medical  = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $member->user_id);
          $wellness  = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $member->user_id);

          $medical_schedule = DB::table('wallet_entitlement_schedule')
                                  ->where('member_id', $member->user_id)
                                  ->where('spending_type', 'medical')
                                  ->where('status', 0)
                                  ->orderBy('created_at', 'desc')
                                  ->first();

          $wellness_schedule = DB::table('wallet_entitlement_schedule')
                                  ->where('member_id', $member->user_id)
                                  ->where('spending_type', 'wellness')
                                  ->where('status', 0)
                                  ->orderBy('created_at', 'desc')
                                  ->first();

          $temp = array(
            'Member ID' => $user->UserID,
            'Full Name' => $user->Name
          );

          if($medical) {
            $temp['Current Medical Allocation'] = (string)$medical['allocation'];
            $temp['New Medical Allocation'] = $medical_schedule ? $medical_schedule->new_allocation_credits : null;
            $temp['Effective Date of New Medical Allocation (DD/MM/YYYY)'] = $medical_schedule ? date('d/m/Y', strtotime($medical_schedule->effective_date)) : date('d/m/Y');
          }

          if($wellness) {
            $temp['Current Wellness Allocation'] = (string)$wellness['allocation'];
            $temp['New Wellness Allocation'] = $wellness_schedule ? $wellness_schedule->new_allocation_credits : null;
            $temp['Effective Date of New Wellness Allocation (DD/MM/YYYY)'] = $wellness_schedule ? date('d/m/Y', strtotime($wellness_schedule->effective_date)) : date('d/m/Y');
          }

          $container[] = $temp;
        }

        return Excel::create('Bulk Allocation Employee Lists', function($excel) use($container) {
          $excel->sheet('Employees', function($sheet) use($container) {
            $sheet->setColumnFormat(array(
              'E' => 'dd/mm/yyyy',
              'H' => 'dd/mm/yyyy'
            ));
            $sheet->fromArray( $container );
          });
        })->export('xls');
    }

    public function uploadEmployeeBulkAllocation( )
    {
        $input = Input::all();
        $result = StringHelper::getJwtHrSession();
        $customer_id = $result->customer_buy_start_id;
        $customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

        if(!$customer) {
          return array('status' => false, 'message' => 'customer_id is required');
        }

        $customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

        if(!$customer) {
          return array('status' => false, 'mesasge' => 'Customer does not exist.');
        }

        if(Input::hasFile('file')) {
          $file = Input::file('file');
          $extensions = array("xls","xlsx","xlm","xla","xlc","xlt","xlw");
          $result = $file->getClientOriginalExtension();
          if(!in_array($result,$extensions)){
            return array('status' => false, 'message' => 'Invalid File.');
          }

          if($file->isValid()){
            $temp_file = time().$file->getClientOriginalName();
            $file->move('excel_upload', $temp_file);
            $data_array = Excel::selectSheets('Employees')->load(public_path()."/excel_upload/".$temp_file)->formatDates(false)->get();
            $headerRow = $data_array->first()->keys();
            // return $data_array;
            $member_id = false;
            $fullname = false;

            foreach ($headerRow as $key => $row) {
              if($row == "member_id") {
                $member_id = true;
              }

              if($row == "full_name") {
                $fullname = true;
              }
            }


            if(!$member_id || !$fullname) {
              return array('status' => false, 'message' => 'Please download the correct file for Bulk Allocation Employee Lists Excel. Do not modify any columns of the excel file');
            }

            $account_link = DB::table('customer_link_customer_buy')
                              ->where('customer_buy_start_id', $customer_id)
                              ->first();
            $business_info = DB::table('customer_business_information')
                                ->where('customer_buy_start_id', $customer_id)
                                ->first();

            foreach ($data_array as $key => $emp) {
              $member = DB::table('corporate_members')
              ->where('corporate_id', $account_link->corporate_id)
              ->where('user_id', $emp['member_id'])
              ->where('removed_status', 0)
              ->first();

              if(!$member) {
                $user = DB::table('user')
                ->where('UserID', $emp['member_id'])
                ->where('UserType', 5)
                ->first();
                if($user) {
                  return array('status' => false, 
                    'message' => 'Employee '.ucwords($user->Name).' is not a member of this Company '.ucwords($business_info->company_name)
                  );
                } else {
                  return array('status' => false, 'message' => 'Employee '.ucwords($emp['full_name']).' does not exist.');
                }
              }
            }

            $spending_account_company = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
            $spending = CustomerHelper::getAccountSpendingStatus($customer_id);
            $customer_credits = DB::table('customer_credits')->where("customer_id", $customer_id)->first();
            $format = [];
            $today = date('Y-m-d');

            foreach ($data_array as $key => $allocation) {
              $temp_medical = array();
              $wallet_entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $allocation['member_id'])->orderBy('created_at', 'desc')->first();
              $wallet = DB::table('e_wallet')->where('UserID', $allocation['member_id'])->first();

              if(isset($allocation['new_medical_allocation']) && $allocation['new_medical_allocation'] != null) {
                if($spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" && $spending['paid_status'] == false) {
                  return ['status' => FALSE, 'message' => 'Unable to allocate medical credits since your company is not yet paid for the Plan. Please make payment to enable medical allocation.'];
                }

                // validate date
                $validateDate = StringHelper::validateFormatDate($allocation['effective_date_of_new_medical_allocation_ddmmyyyy'], "d/m/Y", "d/n/Y");
                if(!$validateDate) {
                  return array('status' => false, 'message' => 'Invalid date format for Medical Allocation. Date should be d/m/Y format');
                }
                $credits  = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $allocation['member_id']);
                $credits = $credits['allocation'];
                $new_date = DateTime::createFromFormat('d/m/Y', $allocation['effective_date_of_new_medical_allocation_ddmmyyyy']);

                if($spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" && $spending['paid_status'] == true) {
                  $new_allocation = $allocation['new_medical_allocation'] - $credits;
                  // check medical balance
                  if($new_allocation > $customer_credits->balance) {
                    return ['status' => FALSE, 'message' => 'Company Medical Balance is not sufficient for this Member'];
                  }
                }

                $temp = array(
                  'member_id'                 => $allocation['member_id'],
                  'new_usage_date'            => $new_date->format('Y-m-d'),
                  'old_usage_date'            => date('Y-m-d', strtotime($wallet_entitlement->medical_usage_date)),
                  'proration'                 => 'months',
                  'new_allocation_credits'    => isset($allocation['new_medical_allocation']) && $allocation['new_medical_allocation'] ? $allocation['new_medical_allocation'] : 0,
                  'new_entitlement_credits'   => isset($allocation['new_medical_allocation']) && $allocation['new_medical_allocation'] ? $allocation['new_medical_allocation'] : 0,
                  'old_entitlement_credits'   => $credits,
                  'plan_end'                  => $spending_account_company->medical_spending_end_date,
                  'effective_date'            => $new_date->format('Y-m-d'),
                  'spending_type'             => 'medical',
                  'status'                    => 0,
                  'created_at'                => date('Y-m-d H:i:s'),
                  'updated_at'                => date('Y-m-d H:i:s')
                );
                $format[] = $temp;
              }

              if(isset($allocation['new_wellness_allocation']) && $allocation['new_wellness_allocation'] != null) {
                if($spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "pre_paid" && $spending['paid_status'] == false) {
                  return ['status' => FALSE, 'message' => 'Unable to allocate wellness credits since your company is not yet paid for the Plan. Please make payment to enable wellness allocation.'];
                }

                $validateDate = StringHelper::validateFormatDate($allocation['effective_date_of_new_wellness_allocation_ddmmyyyy'], "d/m/Y", "d/n/Y");
                if(!$validateDate) {
                  return array('status' => false, 'message' => 'Invalid date format for Wellness Allocation. Date should be d/m/Y format');
                }
                $credits  = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $allocation['member_id']);
                $credits = $credits['allocation'];
                $new_date = DateTime::createFromFormat('d/m/Y', $allocation['effective_date_of_new_wellness_allocation_ddmmyyyy']);

                if($spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "pre_paid" && $spending['paid_status'] == true) {
                  $new_allocation = $allocation['new_wellness_allocation'] - $credits;
    
                  if($new_allocation > $customer_credits->wellness_credits) {
                    return ['status' => FALSE, 'message' => 'Company Wellness Balance is not sufficient for this Member'];
                  }
                }

                $temp = array(
                  'member_id'                 => $allocation['member_id'],
                  'new_usage_date'            => $new_date->format('Y-m-d'),
                  'old_usage_date'            => date('Y-m-d', strtotime($wallet_entitlement->medical_usage_date)),
                  'proration'                 => 'months',
                  'new_allocation_credits'    => isset($allocation['new_wellness_allocation']) && $allocation['new_wellness_allocation'] ? $allocation['new_wellness_allocation'] : 0,
                  'new_entitlement_credits'   => isset($allocation['new_wellness_allocation']) && $allocation['new_wellness_allocation'] ? $allocation['new_wellness_allocation'] : 0,
                  'old_entitlement_credits'   => $credits,
                  'plan_end'                  => $spending_account_company->wellness_spending_end_date,
                  'effective_date'            => $new_date->format('Y-m-d'),
                  'spending_type'             => 'wellness',
                  'status'                    => 0,
                  'created_at'                => date('Y-m-d H:i:s'),
                  'updated_at'                => date('Y-m-d H:i:s')
                );
                $format[] = $temp;
              }
            }
            
            $new_entitlment = new NewEmployeeEntitlementSchedule();
            $future_dates = false;
            foreach ($format as $key => $new) {
              $id = null;
              if($new['spending_type'] == "medical") {
                $check_medical_entitlement = DB::table('wallet_entitlement_schedule')
                                        ->where('member_id', $new['member_id'])
                                        ->where('spending_type', 'medical')
                                        ->where('status', 0)
                                        ->orderBy('created_at', 'desc')
                                        ->first();
                if($check_medical_entitlement) {
                  // update
                  $new['created_at'] = $check_medical_entitlement->created_at;
                  $result = $new_entitlment->updateData($check_medical_entitlement->wallet_entitlement_schedule_id, $new);
                  $id = $check_medical_entitlement->wallet_entitlement_schedule_id;
                } else {
                  // create
                  $result = $new_entitlment->createData($new);
                  $id = $result->id;
                }
              } else {
                $check_wellness_entitlement = DB::table('wallet_entitlement_schedule')
                                        ->where('member_id', $new['member_id'])
                                        ->where('spending_type', 'wellness')
                                        ->where('status', 0)
                                        ->orderBy('created_at', 'desc')
                                        ->first();
                if($check_wellness_entitlement) {
                  // update
                  $new['created_at'] = $check_wellness_entitlement->created_at;
                  $result = $new_entitlment->updateData($check_wellness_entitlement->wallet_entitlement_schedule_id, $new);
                  $id = $check_wellness_entitlement->wallet_entitlement_schedule_id;
                } else {
                  // create
                  $result = $new_entitlment->createData($new);
                  $id = $result->id;
                }
              }
              
              $result_data[] = $new['new_usage_date'];
              if($today >= $new['new_usage_date']) {
                // activate now
                MemberHelper::newActivateNewEntitlement($new['member_id'], $id);
              } else {
                $future_dates = true;
              }
            }

            if($future_dates) {
              if(count(array_count_values($result_data)) == 1) {
                return array('status' => true, 'message' => 'The allocation amount will be updated on '.date('d/m/Y', strtotime($result_data[0])).'.');
              } else {
                return array('status' => true, 'message' => 'The allocation amount will be updated on scheduled dates');
              }
            } else {
              return array('status' => true, 'message' => 'The allocation amount has been successfully updated');
            }
          } else {
            return array('status' => false, 'message' => 'Invalid File.');
          }
        } else {  
          return array('status' => false, 'message' => 'Excel File is required.');
        }
    }

    public function getMemberCreditDetails( )
    {
        $input = Input::all();
        $result = StringHelper::getJwtHrSession();
        $customer_id = $result->customer_buy_start_id;

        if(empty($input['member_id']) || $input['member_id'] == null) {
            return array('status' => false, 'message' => 'member_id is required');
        }

        $member = DB::table('user')->where('UserID', $input['member_id'])->first();

        if(!$member) {
            return array('status' => false, 'message' => 'Member does not exist');
        }

        $ids = StringHelper::getSubAccountsID($input['member_id']);
        $plan_user_history = DB::table('user_plan_history')
                ->where('user_id', $input['member_id'])
                ->where('type', 'started')
                ->orderBy('created_at', 'desc')
                ->first();
        $active_plan = DB::table('customer_active_plan')
                ->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)
                ->first();
        $user_spending_dates = MemberHelper::getMemberCreditReset($input['member_id'], 'current_term', 'medical');
        $wallet = DB::table('e_wallet')->where('UserID', $input['member_id'])->orderBy('created_at', 'desc')->first();
        $wallet_entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $input['member_id'])->orderBy('created_at', 'desc')->first();

        if($user_spending_dates) {
            $medical_credit_data = PlanHelper::memberMedicalAllocatedCreditsByDates($wallet->wallet_id, $input['member_id'], $user_spending_dates['start'], $user_spending_dates['end']);
            $wellness_credit_data = PlanHelper::memberWellnessAllocatedCreditsByDates($wallet->wallet_id, $input['member_id'], $user_spending_dates['start'], $user_spending_dates['end']);
        } else {
            $medical_credit_data['allocation'] = 0;
            $medical_credit_data['get_allocation_spent'] = 0;
            $medical_credit_data['balance'] = 0;
            $wellness_credit_data['allocation'] = 0;
            $wellness_credit_data['get_allocation_spent'] = 0;
        }

        // get pending allocation for medical
        $e_claim_amount_pending_medication = DB::table('e_claim')
        ->whereIn('user_id', $ids)
        ->where('spending_type', 'medical')
        ->where('status', 0)
        ->sum('amount');

        // get pending allocation for wellness
        $e_claim_amount_pending_wellness = DB::table('e_claim')
        ->whereIn('user_id', $ids)
        ->where('spending_type', 'wellness')
        ->where('status', 0)
        ->sum('amount');

        $medical = array(
            'entitlement' => $wallet_entitlement->medical_entitlement,
            'credits_allocation' => $medical_credit_data['allocation'],
            'credits_spent'     => $medical_credit_data['get_allocation_spent'],
            'balance'           => $active_plan->account_type == 'super_pro_plan' || $active_plan->account_type == 'enterprise_plan' ? 'UNLIMITED' :  $medical_credit_data['balance'],
            'e_claim_amount_pending_medication' => $e_claim_amount_pending_medication,
            'currency_type'  =>  $wallet->currency_type
        );

        $wellness = array(
            'entitlement' => $wallet_entitlement->wellness_entitlement,
            'credits_allocation_wellness'    => $wellness_credit_data['allocation'],
            'credits_spent_wellness'        => $wellness_credit_data['get_allocation_spent'],
            'balance'                       => $active_plan->account_type == 'super_pro_plan' || $active_plan->account_type == 'enterprise_plan' ? 'UNLIMITED' : $wellness_credit_data['allocation'] - $wellness_credit_data['get_allocation_spent'],
            'e_claim_amount_pending_wellness'   => $e_claim_amount_pending_wellness,
            'currency_type'  =>  $wallet->currency_type
        );

        return array('status' => true, 'medical' => $medical, 'wellness' => $wellness);
    }
}
