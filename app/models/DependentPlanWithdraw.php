<?php

class DependentPlanWithdraw extends Eloquent 
{

	protected $table = 'dependent_plan_withdraw';
    protected $guarded = ['dependent_plan_withdraw_id'];

    public function createPlanWithdraw($data)
    {
    	return DependentPlanWithdraw::create($data);
    }

    public function updateDependentPlanWithdraw($id, $data)
    {
    	return DependentPlanWithdraw::where('dependent_plan_withdraw_id', $id)->update($data);
    }
}
