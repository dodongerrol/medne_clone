<?php

class DependentPlanHistory extends Eloquent 
{

	protected $table = 'dependent_plan_history';
    protected $guarded = ['dependent_plan_history_id'];

    public function createData($data)
    {
    	return DependentPlanHistory::create($data);
    }
}
