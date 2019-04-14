<?php

class PlanWithdraw extends Eloquent 
{

	protected $table = 'customer_plan_withdraw';
  protected $guarded = ['plan_withdraw_id'];

  public function createPlanWithdraw($data)
  {
  	return PlanWithdraw::create($data);
  }
}
