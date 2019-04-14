<?php

class UserPlanType extends Eloquent 
{

	protected $table = 'user_plan_type';
    protected $guarded = ['user_plan_type_id'];

    public function getUserPlan($id)
    {
        return UserPlanType::where('user_id', '=', $id)->first();
    }

    public function createUserPlanType($data)
    {
    	return UserPlanType::create($data);
    }

    public function checkUserPlanType($id)
    {
    	return UserPlanType::where('user_id', $id)->count();
    }
}
