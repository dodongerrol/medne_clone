<?php

class UserCheckInPayment extends Eloquent 
{

	protected $table = 'user_check_in_payment';
  protected $guarded = ['user_check_in_payment_id'];

  public function createUserCheckInPayment($data)
  {
  	return UserCheckInPayment::create($data);
  }
}
