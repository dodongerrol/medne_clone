<?php
use Illuminate\Support\Facades\Input;

class HrController extends \BaseController {
	public function confirmHrAdminOtp( )
	{
		$input = Input::all();

		if(empty($input['otp_code']) || $input['otp_code'] == null) {
			return ['status' => false, 'message' => 'OTP CODE is required'];
		}

		$member = DB::table('user')->where('OTPCode', $input['otp_code'])->where('is_hr_admin', 1)->select('UserID')->first();
		
		if($member) {
			// check if member is an role adminitrator
			$customerAdminRoles = DB::table('customer_admin_roles')->where('member_id', $member->UserID)->where('status', 1)->first();

			if($customerAdminRoles) {
				// create token
				// create token
				$jwt = new JWT();
				$secret = Config::get('config.secret_key');
				$member->user_type = "member_admin";
				$member->signed_in = FALSE;
				$member->expire_in = strtotime('+15 days', time());
				$token = $jwt->encode($member, $secret);

				$admin_logs = array(
					'admin_id'  => $member->UserID,
					'admin_type' => 'member',
					'type'      => 'admin_member_hr_login_portal',
					'data'      => SystemLogLibrary::serializeData($input)
				);
				SystemLogLibrary::createAdminLog($admin_logs);

				// update otp to null
				DB::table('user')->where('UserID', $member->UserID)->update(['OTPCode' => null]);
				return array(
					'status'	=> TRUE,
					'message'	=> 'Success.',
					'token' => $token
				);
			}
		}

		return ['status' => 'Invalid OTP CODE'];
	}
}
