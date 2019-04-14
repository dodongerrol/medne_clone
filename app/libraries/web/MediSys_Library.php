<?php
	
	class MediSys_Library {

		public static function getMedisysToken( )
		{
			$fields_string = "";
      $url = 'http://medisys.ddns.net:1000/token';
      $fields  = array(
			'grant_type'		=> 'password',
      		'username'			=> 'medicloud_dev',
      		'password'			=> 'XSL[Zu66G^',
      		'InstitutionCode'	=> 'C1',
      		'client_id'			=> 'medicloudSIT'
      	);
      foreach($fields as $key=>$value) { 
      	$fields_string .= $key.'='.$value.'&'; 
      }
      rtrim($fields_string, '&');

			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
			$result = curl_exec($ch);
			curl_close($ch);
			$return = json_decode($result, true);
			
			$token = $return['access_token'];
			if($token) {
				return $token;
			} else {
				return FALSE;
			}
		}

		public static function getMedisysDoctorList( )
		{
			
			$token = StringHelper::getMedisysSessionToken( );
    	$url = 'http://medisys.ddns.net:1000/api/doctor';

    	//open connection
			$ch = curl_init();
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    		'Authorization: bearer '.$token,
	    	));
			//execute post
			$result = curl_exec($ch);
			//close connection
			curl_close($ch);

			$respose = json_encode($result, true);
			return $respose;
		}

		public static function getMedisysDoctorBlocks( $doctor_id )
		{
			$token = StringHelper::getMedisysSessionToken( );
			// $from = date('Y-m-d\TH:i:s\Z');
			$from = "2016-11-04T02:53:37.163Z";
			$to   = "2016-11-04T03:53:37.163Z";
			$type = 1;
 			$url = 'http://medisys.ddns.net:1000/api/doctor/'.$doctor_id.'/getblocks?from='.$from.'&to='.$to.'&type='.$type;

    	//open connection
			$ch = curl_init();
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    		'Authorization: bearer '.$token,
    	));
			//execute post
			$result = curl_exec($ch);
			//close connection
			curl_close($ch);

			$respose = json_encode($result, true);
			return $respose;
		}

		public static function createAppointmentMedisys( $doctor_id )
		{
				$token = StringHelper::getMedisysSessionToken( );
				$url = 'http://medisys.ddns.net:1000/api/Appointment/Create';

				$new_object = new StdClass();
				$new_object->DoctorProfileId = $doctor_id;
				$new_object->StartTime = "2016-11-04T02:53:37.163Z";
				$new_object->EndTime =  "2016-11-04T03:53:37.163Z";
				$data_array["PatientName"] = "Allan Alzula";
				$data_array["NRIC"] = "S8524103F";
				$data_array["Contact"] = "+639265105102";
				$new_object->Patient = $data_array;

				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    		'Authorization: bearer '.$token,
	    	));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($new_object));
				$result = curl_exec($ch);
				curl_close($ch);
				$return = json_decode($result, true);

				// echo json_encode($return);
				return $return;
		}

		public static function getMedisysAppointment( $calendar_id )
		{
				$token = StringHelper::getMedisysSessionToken( );
				$url = 'http://medisys.ddns.net:1000/api/Appointment/'.$calendar_id;

				//open connection
				$ch = curl_init();
				//set the url, number of POST vars, POST data
				curl_setopt($ch,CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    		'Authorization: bearer '.$token,
	    	));
				//execute post
				$result = curl_exec($ch);
				//close connection
				curl_close($ch);

				$respose = json_encode($result, true);
				return $respose;
		}
	}
	
?>