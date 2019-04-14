<?php

class AddedActivePlanPurchase extends Eloquent 
{

	protected $table = 'customer_added_active_plan_purchase';
  protected $guarded = ['customer_added_active_plan_purchase_id'];

  public function createAddedActivePlanPurchase($data)
  {
  	return AddedActivePlanPurchase::create($data);
  }

  public function updateActivePlanPurchase($id)
  {
  	return AddedActivePlanPurchase::where('customer_active_plan', $id)->update(['status' => 1]);
  }
}
