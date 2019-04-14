<?PHP
	function sendMessage($header,$msg,$uri,$clinic){
		$content = array(
			"en" => $msg
			);
			
		$headings = array(
			"en" => $header
			);	
		$url = $uri;
		
		$fields = array(
			'app_id' => "6dc4b805-0589-4bc1-98ef-ddc2ea8797e6",
			'filters' => array(array("field" => "tag", "key" => "clinicid", "relation" => "=", "value" => $clinic)),
			'data' => array("foo" => "bar"),
			'contents' => $content,
			'headings' => $headings,
			'url' => $url
		);
		
		$fields = json_encode($fields);
    //	print("\nJSON sent:\n");
    //	print($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic NTM3MTgwMjItN2YzZC00ZjdkLThkZWUtMDgzNWU2NThlMjY4'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}
	$header = $_POST['header'];
	$content = $_POST['content'];
	$url = $_POST['url'];
	$clinic = $_POST['clinic'];
	$response = sendMessage($header,$content,$url,$clinic);
//	$response = sendMessage("hader","gini lo <br> asf ","http://www.yahoo.com","4332");
	
?>