<?php

class CustomerCreditLogs extends Eloquent 
{

	protected $table = 'customer_credit_logs';
  protected $guarded = ['customer_credit_logs_id'];

  public function getCustomerCreditLogs($id)
  {
  	return CustomerCredits::where('customer_id', $id)->get();
  }

  public function createCustomerCreditLogs($data)
  {
  	return CustomerCreditLogs::create($data);
  }
}
