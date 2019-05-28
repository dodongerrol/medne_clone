<?php

class PusherHelper {
	public static function config( )
	{
		$pusher = new \Pusher\Pusher("7ffa471d8a83267ae780", "bb8a0663a3302d2328a1", "431237", array('cluster' => 'ap1'));
		return $pusher;
	}

	public static function getChannel( )
	{
		// $config = StringHelper::Deployment( );

		// if($config == 1) {
			return array('channel' => 'live-channel', 'key' => '7ffa471d8a83267ae780');
		// }	else if($config == 2) {
		// 	return array('channel' => 'dev-channel', 'key' => '7ffa471d8a83267ae780');
		// } else {
		// 	return array('channel' => 'test-channel', 'key' => '7ffa471d8a83267ae780');
		// }

		// return array('key' => '7ffa471d8a83267ae780');
	}

	public static function sendClaimNotification($data)
	{
		$pusher = self::config( );
		$channel = self::getChannel( );
		return $pusher->trigger([$channel['channel']], 'claim-notification-event', $data);
	}

	public static function sendNewClaimNotification($data, $sub)
	{
		$pusher = self::config( );
		$channel = self::getChannel( );
		return $pusher->trigger([$channel['channel']], $sub, $data);
	}

	public static function sendClinicClaimNotification($transaction_id, $clinic_id)
	{

		$clinic = DB::table('user')->where('Ref_ID', $clinic_id)->where('UserType', 3)->first();

		if($clinic) {
			$clinic_id = $clinic_id;
    		$user_id = $clinic->UserID;
    		$connection = StringHelper::socketConnection($clinic_id, $user_id);
    		$payload = array(
    			'connection_type'	=> $connection,
    			'clinic_id'			=> $clinic_id,
    			'transaction_id'	=> $transaction_id
    		);
    		$api = "https://sockets.medicloud.sg/sockets/send_clinic_claim_notification";

    		return httpLibrary::postHttp($api, $payload, []);
		}

	}

	public static function sendClinicCheckInNotification($transaction_id, $clinic_id)
	{

		$clinic = DB::table('user')->where('Ref_ID', $clinic_id)->where('UserType', 3)->first();

		if($clinic) {
			$clinic_id = $clinic_id;
    		$user_id = $clinic->UserID;
    		$connection = StringHelper::socketConnectionCheckIn($clinic_id, $user_id);
    		$payload = array(
    			'connection_type'	=> $connection,
    			'clinic_id'			=> $clinic_id,
    			'check_in_id'	=> $transaction_id
    		);
    		$api = "https://sockets.medicloud.sg/sockets/send_clinic_check_in";

    		return httpLibrary::postHttp($api, $payload, []);
		}

	}
}
?>