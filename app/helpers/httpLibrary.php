<?php
	class httpLibrary {
		public static function getHttp($url, $headers)
		{
			// return $headers;
			$curl = curl_init();
			if($headers) {
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			}
	    	curl_setopt($curl, CURLOPT_URL, $url);
	    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    	$resp = curl_exec($curl);
	    	$json = json_decode($resp, true);
	    	curl_close($curl);
	    	return $json;
		}
		public static function postHttp($url, $data, $headers)
		{
			$fields_string = http_build_query($data);
			$curl = curl_init();
			if($headers) {
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			}
	    	curl_setopt($curl, CURLOPT_URL, $url);
	    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    	curl_setopt($curl,CURLOPT_POST, count($data));
			curl_setopt($curl,CURLOPT_POSTFIELDS, $fields_string);
	    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    	$resp = curl_exec($curl);
	    	$json = json_decode($resp, true);
	    	curl_close($curl);
	    	return $json;
		} 
	}
?>