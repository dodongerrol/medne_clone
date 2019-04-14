<?php

class DependentPlanStatus extends Eloquent 
{

	protected $table = 'dependent_plan_status';
    protected $guarded = ['dependent_plan_status_id'];

    public function incrementEnrolledDependents($customer_plan_id)
    {
    	return DependentPlanStatus::where('customer_plan_id', $customer_plan_id)->increment('total_enrolled_dependents', 1);
    }

    public function decrementtEnrolledDependents($customer_plan_id)
    {
    	return DependentPlanStatus::where('customer_plan_id', $customer_plan_id)->decrement('total_enrolled_dependents', 1);
    }

    public function addjustCustomerStatus($field, $plan_id, $type, $number)
	{
		if($type == "increment") {
		  DependentPlanStatus::where('customer_plan_id', $plan_id)->increment($field, $number);
		} else {
		  DependentPlanStatus::where('customer_plan_id', $plan_id)->decrement($field, $number);
		}
	}
}
