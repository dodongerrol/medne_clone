<?php

class UserPlanHistory extends Eloquent 
{

	protected $table = 'user_plan_history';
  protected $guarded = ['user_plan_history_id'];

  public function createUserPlanHistory($data)
  {
  	return UserPlanHistory::create($data);
  }
}
