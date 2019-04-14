<?php

class CustomerPersonalDetails extends Eloquent 
{

	protected $table = 'customer_personal_details';
  protected $guarded = ['customer_personal_details_id'];

  public function createCustomerPersonalDetails($data)
  {
  	return CustomerPersonalDetails::create($data);
  }

  public function getCustomerPersonalDetails($id)
  {
  	return CustomerPersonalDetails::where('customer_buy_start_id', $id)->count();
  }

  public function getCustomerPersonalDetailsData($id)
  {
    return CustomerPersonalDetails::where('customer_buy_start_id', $id)->first();
  }

  public function updateCustomerPersonalDetails($id, $data)
  {
  	return CustomerPersonalDetails::where('customer_buy_start_id', $id)->update($data);
  }
}
