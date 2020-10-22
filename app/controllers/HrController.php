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

	public function getAccountPermissions( )
	{
		$result = StringHelper::getJwtHrSession();

		$permissions = \UserPermissionsHelper::getUserPemissions($result->id, $result->user_type);
		return ['status' => true, 'data' => $permissions];
	}

	public function validateExernalAdminToken( )
	{
		$input = Input::all();

		if(empty($input['token']) || $input['token'] == null) {
			return ['status' => false, 'message' => 'token is required'];
		}

		// check token if exist in external user
		$checkToken = DB::table('user')->where('ActiveLink', $input['token'])->where('UserType', 6)->select('UserID', 'expiration_time', 'member_activated')->first();

		if(!$checkToken) {
			return ['status' => false, 'message' => 'token does not exist'];
		}

		if((int)$checkToken->member_activated == 1) {
			return array('status' => true, 'data' => ['external_user_id' => $checkToken->UserID, 'valid_token' => true, 'activated' => true]);
		}

		// check if token is still valid
		$today = strtotime(date('Y-m-d H:i:s'));
		$expiry = strtotime($checkToken->expiration_time);

		if($today > $expiry) {
			return array('status' => false, 'message' => 'token is expired', 'data' => ['valid_token' => true, 'activated' => false, 'expired_token' => true]);
		}

		return array('status' => true, 'data' => ['external_user_id' => $checkToken->UserID, 'valid_token' => true, 'activated' => false, 'expired_token' => false, 'token' => $input['token']]);
	}

	public function createExternalAdminUserPassword( )
	{
		$input = Input::all();

		if(empty($input['token']) || $input['token'] == null) {
			return ['status' => false, 'message' => 'token is required.'];
		}

		if(empty($input['external_user_id']) || $input['external_user_id'] == null) {
			return ['status' => false, 'message' => 'external_user_id is required.'];
		}

		if(empty($input['confirm_password']) || $input['confirm_password'] == null) {
			return ['status' => false, 'message' => 'confirm_password is required.'];
		}

		if(empty($input['password']) || $input['password'] == null) {
			return ['status' => false, 'message' => 'password is required.'];
		}

		// check confirm_password and password similarities
		if($input['confirm_password'] != $input['password']) {
			return ['status' => false, 'message' => 'Password and Confim Password does not exist'];
		}

		// check token if exist in external user
		$checkToken = DB::table('user')->where('ActiveLink', $input['token'])->where('UserType', 6)->select('UserID', 'expiration_time', 'member_activated')->first();

		if(!$checkToken) {
			return ['status' => false, 'message' => 'token does not exist'];
		}

		if((int)$checkToken->member_activated == 1) {
			return ['status' => false, 'message' => 'External Admin already activated'];
		}

		// create password and update account status
		$updateExternal = DB::table('user')->where('UserID', $checkToken->UserID)->update(['Password' => md5($input['password']), 'member_activated' => 1, 'Active' => 1, 'updated_at' => date('Y-m-d')]);
		return ['status' => 'Successfully Create External Administrator Password'];
	}
}
