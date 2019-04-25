<?php

class EmployeeClinicCheckIn extends Eloquent 
{

	protected $table = 'user_check_in_clinic';
  protected $guarded = ['check_in_id'];

  public function createData($data)
  {
  	return EmployeeClinicCheckIn::create($data);
  }
}
