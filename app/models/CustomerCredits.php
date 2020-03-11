<?php

class CustomerCredits extends Eloquent 
{

	protected $table = 'customer_credits';
  protected $guarded = ['customer_credits_id'];

  public function getCustomerCredits($id)
  {
  	return CustomerCredits::where('customer_id', $id)->first();
  }

  public function createCustomerCredits($data)
  {
  	return CustomerCredits::create($data);
  }

  public function checkCustomerCredits($id)
  {
  	return CustomerCredits::where('customer_id', $id)->count();
  }

  public function deductCustomerCredits($id, $credits)
  {
    return CustomerCredits::where('customer_credits_id', $id)->decrement('balance', $credits);
  }

  public function deductCustomerMedicalSuppCredits($id, $credits)
  {
    return CustomerCredits::where('customer_credits_id', $id)->decrement('medical_supp_credits', $credits);
  }

  public function deductCustomerWellnessCredits($id, $credits)
  {
    return CustomerCredits::where('customer_credits_id', $id)->decrement('wellness_credits', $credits);
  }

   public function deductCustomerWellnessSuppCredits($id, $credits)
  {
    return CustomerCredits::where('customer_credits_id', $id)->decrement('wellness_supp_credits', $credits);
  }
}
