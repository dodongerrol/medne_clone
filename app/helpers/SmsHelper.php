<?php
// require $_SERVER['DOCUMENT_ROOT'] . '/twilio-php/Twilio/autoload.php';
use Twilio\Rest\Client;
class SmsHelper
{
	public static function twilioConfigs( )
	{
		$config = array();
		$config['sid'] = 'AC1f79827f4e92575fed4ad9562423ca5a';
		$config['token'] = '2a769e9f78b36d59cae26003067071c3';
        // $config['from'] = '+18653200485';
		$config['from'] = 'Mednefits';
		return $config;
	}

	public static function sendSms($data)
	{
		$config = self::twilioConfigs();
		$client = new Client($config['sid'], $config['token']);

		if(strrpos($data['phone'], '+65') !== false) {
			$from = $config['from'];
		} else {
			$from = '+18653200485';
		}

		try {
			$result = $client->messages->create(
				$data['phone'],
				array(
					'from' => $from,
					'body' => $data['message'],
				)
			);

			return array('status' => true, 'result' => $result);
		} catch(Exception $e) {
			return array('status' => false, 'error' => $e->getMessage());
		}
	}

	public static function checkPhone($phone)
	{
		$config = self::twilioConfigs();
		$client = new Client($config['sid'], $config['token']);

		try {
			$phone_number = $client->lookups->v1->phoneNumbers($phone)
                                    ->fetch(array("type" => "carrier"));
    	return $phone_number->carrier;
		} catch(Exception $e) {
			return false;
		}
	}

	public static function formatNumber($data)
	{
		if($data->PhoneCode) {
			if(strripos($data->PhoneNo, '+') !== false) {
				$phone = $data->PhoneNo;
			} else {
				$phone = $data->PhoneCode.$data->PhoneNo;
			}
		} else {
			$phone = $data->PhoneNo;
		}

		// check phone
		$check = self::checkPhone($phone);

		if($check['error_code'] == null) {
			return $phone;
		} else {
			return false;
		}
	}

	public static function formatForgotPasswordMessage($data)
	{
		return "Reset Password SMS: Reset your Mednefits account password here: ".$data->server."/app/resetmemberpassword?token=".$data->ResetLink;
		// return "Hello ".ucwords($data->Name)."! \nForgot your password? Click on the link below to reset your password.\n".$data->server."/app/resetmemberpassword?token='.$data->ResetLink.\nIf you did not request to reset your password, ignore this sms and the link will expire on its own. - Mednefits";
	}

	public static function formatWelcomeEmployeeMessage($data)
	{
		if($data['email'] && $data['nric']) {
			$contact = $data['email']." or ".$data['nric'];
		} else if($data['nric'] && $data['email'] == null) {
			$contact = $data['nric'];
		} else {
			$contact = $data['email'];
		}

		return "Hi ".ucwords($data['name']).", your company ".ucwords($data['company'])." has enrolled you into the Mednefits health benefits program. Your plan will start on ".$data['plan_start'].". Your Member Account Login ID is ".$contact." and Password is ".$data['password'].". Download Mednefits App in either on Apple App Store or Android PlayStore.";
	}
}
?>