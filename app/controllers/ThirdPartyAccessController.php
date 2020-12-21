<?php

use Illuminate\Support\Facades\Input;
class ThirdPartyAccessController extends \BaseController {
	public function checkMember( )
	{
		$input = Input::all();

		if(empty($input['token']) || $input['token'] == null) {
			return ['status' => false, 'message' => 'token is required.'];
		}

		// if(empty($input['search']) || $input['search'] == null) {
		// 	return ['status' => false, 'message' => 'search is required.'];
		// }
		
		$email = !empty($input['email']) ? $input['email'] : null;
		$phone = !empty($input['phone']) ? $input['phone'] : null;

		// check token
		$token = DB::table('customer_accessKey')->where('integration', 'ntuc')->where('accessKey', $input['token'])->first();

		if(!$token) {
			return ['status' => false, 'message' => 'token is invalid.'];
		}

		if($email) {
			// make a query
			$member = DB::table('user')
					->where('Email', $email)
					->where('UserType', 5)
					->where('Active', 1)
					->first();
			
			if($member) {
				$data = array(
					'member_id'	=> $member->UserID,
					'email'		=> $member->Email,
					'phone'		=> $member->PhoneNo
				);
				return array('status' => true, 'data' => $data);
			}
		} 
		
		if($phone) {
			$member = DB::table('user')
					->where('PhoneNo', $phone)
					->where('UserType', 5)
					->where('Active', 1)
					->first();
			
			if($member) {
				$data = array(
					'member_id'	=> $member->UserID,
					'email'		=> $member->Email,
					'phone'		=> $member->PhoneNo
				);
		
				return array('status' => true, 'data' => $data);
			}
		}
		return ['status' => false, 'message' => 'member does not exist or active'];
	}
}
