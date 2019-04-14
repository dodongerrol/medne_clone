<?php

class UserCheckIn extends Eloquent 
{

	protected $table = 'user_check_in';
  protected $guarded = ['user_check_in_id'];

  public function createUserCheckIn($data)
  {
  	return UserCheckIn::create($data);
  }
}
