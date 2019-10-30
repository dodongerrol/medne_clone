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
}
