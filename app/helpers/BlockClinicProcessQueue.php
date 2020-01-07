<?php

class BlockClinicProcessQueue 
{
	
	public function fire($job, $data)
	{
    $link = new \CustomerLinkBuy();
		$account = $link->getData($data['customer_id']);
    $corporate = new \CorporateMembers();
		$corporate_members = $corporate->getActiveMembers($account->corporate_id);

		$block_access = new \CompanyBlockClinicAccess();
		foreach ($corporate_members as $key => $member) {
      foreach ($clinic_ids as $key => $id) {
      	// check if clinic block already exits
        $check = DB::table('company_block_clinic_access')
                  ->where('customer_id', $member->user_id)
                  ->where('account_type', 'employee')
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

		// $job->delete();
		
	}

  public function execute($data, $status)
  {
    $link = new \CustomerLinkBuy();
    $account = $link->getData($data['customer_id']);
    $corporate = new \CorporateMembers();
    $corporate_members = $corporate->getActiveMembers($account->corporate_id);

    $block_access = new \CompanyBlockClinicAccess();
    $clinic_ids = $data['ids'];
    foreach ($corporate_members as $key => $member) {
      foreach ($clinic_ids as $key => $id) {
        // check if clinic block already exits
        $check = DB::table('company_block_clinic_access')
                  ->where('customer_id', $member->user_id)
                  ->where('account_type', 'employee')
                  ->where('clinic_id', $id)
                  ->first();

        if(!$check) {
          // create block access
          $data = array(
            'customer_id' => $member->user_id,
            'clinic_id'   => $id,
            'account_type' => 'employee',
            'status'      => $status
          );
          $result = $block_access->createData($data);
        } else {
          // if((int)$check->status == 0) {
          $result = $block_access->updateData($check->company_block_clinic_access_id, ['status' => $status]);
          // DB::table('company_block_clinic_access')->where('company_block_clinic_access_id', $check->company_block_clinic_access_id)->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
          // }
        }
      }
    }
    
  }
}
?>