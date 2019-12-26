<?php
class CustomerHelper
{
	public static function getCustomerWalletStatus($customer_id)
	{
		$spending_account = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$medical = false;
		$wellness = false;

		if((int)$spending_account->medical_enable == 1) {
			$medical = true;
		}

		if((int)$spending_account->wellness_enable == 1) {
			$wellness = true;
		}

		return ['status' => true, 'medical' => $medical, 'wellness' => $wellness];
	}
}
?>