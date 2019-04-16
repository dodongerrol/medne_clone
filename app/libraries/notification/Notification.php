<?php
	class Notification {

		public static function config( )
		{

			$config = StringHelper::Deployment( );

			if($config == 1) {
				return array(
					'app_id'	=> '013e34e7-7eed-4a88-9f13-d00a610e61fc',
					'token'		=> 'OWVjMTNlMTAtNjRhYy00NTljLWI5YTEtZjU4OTMxOWRlNGUw'
				);
			} else if($config == 2) {
					return array(
						'app_id'	=> '3cc0159d-dbda-403f-893a-b3812e752679',
						'token'		=> 'ZGRjN2YwOWItMDNlOS00MTNiLWFmNTQtODY1YTRjODg1ZTYz'
					);
			} else {
				return array(
						'app_id'	=> 'e2def76a-19f4-442a-89a8-36ce47d3d58d',
						'token'		=> 'YjJmMDYyNmYtYzJhYi00NzZjLWFlYjEtYTc5OTBkMDYxOWI0'
					);
			}
			
		}


		public static function sendNotificationToHR($header, $msg, $url, $customer_id, $image)
		{
			$config = self::config();

			$content = array(
				"en" => $msg
			);

			$headings = array(
				"en" => $header
			);

			$fields = array(
				'app_id' => $config['app_id'],
				'filters' => array(array("field" => "tag", "key" => "customer_id", "relation" => "=", "value" => $customer_id)),
				'data' => array("foo" => "bar"),
				'contents' => $content,
				'headings' => $headings,
				'url' => $url,
				'big_picture' => $image,
				'adm_big_picture' => $image,
				'chrome_big_picture' => $image,
				'chrome_web_icon'	=> $image
			);
			
			$fields = json_encode($fields);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
													   'Authorization: Basic '.$config['token']));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);
			
			return $response;
		}

		// send from mobile query
		public static function sendNotification($header, $msg, $url, $clinic, $image)
		{

			$config = self::config();

			$content = array(
				"en" => $msg
			);

			$headings = array(
				"en" => $header
			);

			$fields = array(
				'app_id' => $config['app_id'],
				'filters' => array(array("field" => "tag", "key" => "clinicid", "relation" => "=", "value" => $clinic)),
				'data' => array("foo" => "bar"),
				'contents' => $content,
				'headings' => $headings,
				'url' => $url,
				'big_picture' => $image,
				'adm_big_picture' => $image,
				'chrome_big_picture' => $image,
				'chrome_web_icon'	=> $image
			);
			
			$fields = json_encode($fields);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
													   'Authorization: Basic '.$config['token']));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);
			
			return $response;
		}

		// send to employee
		public static function sendNotificationEmployee($header, $msg, $url, $clinic, $image)
		{

			$config = self::config();

			$content = array(
				"en" => $msg
			);

			$headings = array(
				"en" => $header
			);

			$fields = array(
				'app_id' => $config['app_id'],
				'filters' => array(array("field" => "tag", "key" => "employee_id", "relation" => "=", "value" => (int)$clinic)),
				'data' => array("foo" => "bar"),
				'contents' => $content,
				'headings' => $headings,
				'url' => $url,
				'big_picture' => $image,
				'adm_big_picture' => $image,
				'chrome_big_picture' => $image,
				'chrome_web_icon'	=> $image
			);
			
			$fields = json_encode($fields);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
													   'Authorization: Basic '.$config['token']));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);
			
			return $response;
		}
	}
?>