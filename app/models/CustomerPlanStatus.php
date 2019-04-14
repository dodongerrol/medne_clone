<?php

class CustomerPlanStatus extends Eloquent
{

	protected $table = 'customer_plan_status';
  protected $guarded = ['customer_plan_status_id'];


  public function addjustCustomerStatus($field, $plan_id, $type, $number)
  {
    if($type == "increment") {
      CustomerPlanStatus::where('customer_plan_id', $plan_id)->increment($field, $number);
    } else {
      CustomerPlanStatus::where('customer_plan_id', $plan_id)->decrement($field, $number);
    }
  }
}
