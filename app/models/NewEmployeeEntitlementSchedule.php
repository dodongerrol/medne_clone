<?php

class NewEmployeeEntitlementSchedule extends Eloquent 
{

	protected $table = 'wallet_entitlement_schedule';
  protected $guarded = ['wallet_entitlement_schedule_id'];

  public function createData($data)
  {
  	return NewEmployeeEntitlementSchedule::create($data);
  }

  public function updateData($id, $data)
  {
  	return NewEmployeeEntitlementSchedule::where('wallet_entitlement_schedule_id', $id)->update($data);
  }
}
