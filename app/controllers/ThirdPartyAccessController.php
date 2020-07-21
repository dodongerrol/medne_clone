<?php

use Illuminate\Support\Facades\Input;
class ThirdPartyAccessController extends \BaseController {
	public function checkMember( )
	{
		$input = Input::all();

		if(empty($input['token']) || $input['token'] == null) {
			return ['status' => false, 'message' => 'token is required.'];
		}

		if(empty($input['search']) || $input['search'] == null) {
			return ['status' => false, 'message' => 'search is required.'];
		}

		// check token
		$token = DB::table('customer_accessKey')->where('integration', 'ntuc')->where('accessKey', $input['token'])->first();

		if(!$token) {
			return ['status' => false, 'message' => 'token is invalid.'];
		}

		$search = $input['search'];
		// make a query
		$memberEmail = DB::table('user')
					->where('Email', $search)
					->where('UserType', 5)
					->where('Active', 1)
					->first();
		
		if($memberEmail) {
			$member = $memberEmail;
		} else {
			$memberPhone = DB::table('user')
					->where('PhoneNo', $search)
					->where('UserType', 5)
					->where('Active', 1)
					->first();
			
			if(!$memberPhone) {
				return ['status' => false, 'message' => 'member does not exist or active'];
			}

			$member = $memberPhone;
		}
		
		$data = array(
			'member_id'	=> $member->UserID,
			'email'		=> $member->Email,
			'phone'		=> $member->PhoneNo
		);

		return array('status' => true, 'data' => $data);
	}
}
