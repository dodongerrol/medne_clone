<?php
use Illuminate\Support\Facades\Input;

class PushNotificationController extends \BaseController {
	public function saveDeviceToken( )
	{
		$input = Input::all();

		if(empty($input['token']) || $input['token'] == null) {
			return array('status' => false, 'message' => 'Device Token is required.');
		}

		if(empty($input['platform']) || $input['platform'] == null) {
			return array('status' => false, 'message' => 'Device Platform is required.');
		}

		$user_id = null;

		if(!empty($input['user_id']) || $input['user_id'] != null) {
			$user_id = $input['user_id'];
		} else {
			$authSession = new OauthSessions();
			$getRequestHeader = StringHelper::requestHeader();
			if(!empty($getRequestHeader['Authorization'])){
				$getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  				if($getAccessToken){
					$findUserID = $authSession->findUserID($getAccessToken->session_id);
		   			if($findUserID){
		   				$user_id = $findUserID;
		   			}
  				}
			}
		}

		$device_token = new DeviceTokens();
		$result = $device_token->createOrUpdate($input);

		if($result) {
			return array('status' => true, 'message' => 'Device Token saved.');
		} else {
			return array('status' => false, 'message' => 'Failed to save Device Token.');
		}
	}
}
