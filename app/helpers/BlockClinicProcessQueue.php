<?php

class BlockClinicProcessQueue 
{
	
	public function fire($job, $data)
	{
		$account = \DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $data['customer_id'])->first();
		$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->where('removed_status', 0)->get();

		$block_access = new \CompanyBlockClinicAccess();
		foreach ($corporate_members as $key => $member) {
      foreach ($clinic_ids as $key => $id) {
      	// check if clinic block already exits
        $check = DB::table('company_block_clinic_access')
                  ->where('customer_id', $member->user_id)
                  ->where('account_type', 'company')
                  ->where('clinic_id', $id)
                  ->first();

        if(!$check) {
          // create block access
          $data = array(
            'customer_id' => $member->user_id,
            'clinic_id'   => $id,
            'account_type' => 'employee',
            'status'      => 1
          );
          $result = $block_access->createData($data);
        } else {
          if((int)$check->status == 0) {
            $result = $block_access->updateData($check->company_block_clinic_access_id, ['status' => 1]);
          }
        }

        echo "done processing";
      }
    }

		$job->delete();
		
	}
}
?>