<?php

use Illuminate\Support\Facades\Input;

class PlanRenewalController extends \BaseController {

	public function checkSession( )
	{
		$result = StringHelper::getJwtHrSession();
		if(!$result) {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Need to authenticate user.'
			);
		}
		return $result;
	}

	public function getEntitlementEnrolmentStatus( )
	{
		$result = self::checkSession();

		$customer_id = $result->customer_buy_start_id;
		return CustomerHelper::getCustomerWalletStatus($customer_id);
	}
}
