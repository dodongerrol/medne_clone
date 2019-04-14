<?php

class CustomerReplaceEmployee extends Eloquent 
{

	protected $table = 'customer_replace_employee';
  protected $guarded = ['customer_replace_employee_id'];

  public function createReplaceEmployee($data)
  {
  	return CustomerReplaceEmployee::create($data);
  }

  public function updateCustomerReplace($id, $data)
  {
  	return CustomerReplaceEmployee::where('customer_replace_employee_id', $id)->update($data);
  }
}
