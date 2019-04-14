<?php

class FamilyCoverageAccounts extends Eloquent 
{

	protected $table = 'employee_family_coverage_sub_accounts';
  	protected $guarded = ['sub_account_id'];

  	public function createData($data)
  	{
  		return FamilyCoverageAccounts::create($data);
  	}
}
