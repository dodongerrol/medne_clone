<?php

class CustomerWellnessCreditLogs extends Eloquent 
{

	protected $table = 'customer_wellness_credits_logs';
  protected $guarded = ['customer_wellness_credits_history_id'];

  public function getCustomerWellnessCreditLogs($id)
  {
  	return CustomerWellnessCreditLogs::where('customer_credits_id', $id)->get();
  }

  public function createCustomerWellnessCreditLogs($data)
  {
  	return CustomerWellnessCreditLogs::create($data);
  }
}
