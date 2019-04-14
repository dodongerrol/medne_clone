<?php

class EmployeeCreditSpentLogs extends Eloquent 
{

	protected $table = 'employee_credit_spend_logs';
  protected $guarded = ['employee_credit_spend_logs_id'];

  public function createLog($data)
  {
  	return EmployeeCreditSpentLogs::create($data);
  }
}
