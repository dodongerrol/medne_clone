<?php

require $_SERVER['DOCUMENT_ROOT'] . '/twilio-php/Twilio/autoload.php';
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Input;

class SMSController extends \BaseController 
{
	public function call($number)
	{
		// return $number;
		$config = array();
        $config['sid'] = 'AC1f79827f4e92575fed4ad9562423ca5a';
        $config['token'] = '2a769e9f78b36d59cae26003067071c3';
        $config['from'] = '+18653200485';
        // $config['from'] = 'Mednefits';

	    // Step 3: Instantiate a new Twilio Rest Client
	    $client = new Client($config['sid'], $config['token']);

	    try {
	        // Initiate a new outbound call
	        $call = $client->account->calls->create(
	            // Step 4: Change the 'To' number below to whatever number you'd like 
	            // to call.
	            $number,

	            // Step 5: Change the 'From' number below to be a valid Twilio number 
	            // that you've purchased or verified with Twilio.
	            $config['from'],

	            // Step 6: Set the URL Twilio will request when the call is answered.
	            array("url" => "http://medicloud.dev/accept_call")
	        );
	        echo "Started call: " . $call->sid;
	    } catch (Exception $e) {
	        echo "Error: " . $e->getMessage();
	    }
	}

	public function getCall()
	{
		$input = Input::all();
		// header("context-type: text/xml");

		// <Response>
		// 	<Say>Hello! This is a test! </Say>
		// </Response
	}
}
