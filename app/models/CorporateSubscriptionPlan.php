<?php

class CorporateSubscriptionPlan extends Eloquent 
{

		protected $table = 'customer_subscription_plan';
    protected $guarded = ['customer_subscription_plan_id'];

    public function insertCorporateSubscriptionPlan($data)
    {
        return CorporateSubscriptionPlan::create($data);
    }
    
}
