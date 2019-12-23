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
		$input = Input::all();
		$result = self::checkSession();

		$customer_id = $result->customer_buy_start_id;
		$spending_account = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$medical = false;
		$wellness = false;

		if(!$spending_account) {
			return array('status' => false, 'message' => 'Customer does not have a spending account');
		}

		if((int)$spending_account->medical_enable == 1) {
			$medical = true;
		}

		if((int)$spending_account->wellness_enable == 1) {
			$wellness = true;
		}

		return array('status' => true, 'medical' => $medical, 'wellness' => $wellness);
	}
}
