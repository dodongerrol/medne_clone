<?php

class EmployeeCreditAllocation extends Eloquent 
{

	protected $table = 'employee_credit_allocation';
  protected $guarded = ['eca_id'];

  public function createAllocation($data)
  {
  	return EmployeeCreditAllocation::create($data);
  }
}
