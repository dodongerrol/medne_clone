<?php

class WebHookController extends \BaseController {

	public function saveWebHookStripeLogs( )
	{
		$hook = new WebHook();
		$input = @file_get_contents("php://input");
		$result = json_decode($input, true);
		// return $result['type'];
		$data = array(
			'webhook_data'	=> serialize($input),
			'type'					=> $result['type']
		);
		return $hook->createWebHook($data);
	}


}
