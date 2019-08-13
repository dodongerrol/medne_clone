<?php

class PlanTierUsers extends Eloquent 
{

	protected $table = 'plan_tier_users';
    protected $guarded = ['plan_tier_user_id'];

    public function createData($data)
    {
    	return PlanTierUsers::create($data);
    }
}
