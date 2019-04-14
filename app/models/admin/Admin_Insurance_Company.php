<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Admin_Insurance_Company extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'insurance_company';

	//Get all insurance company details
	public function GetInsuranceCompanyList()
	{
		$insuranceCompanyData = DB::table('insurance_company')
			->where('Active',1)
			->lists('Name','CompanyID');

			return $insuranceCompanyData;
	}

	public function InsuranceCompanyByID($id)
	{
		$findResult = DB::table('clinic_insurence_company')
		->where('ClinicID',$id)
		->where('Active','1')
		->lists('InsuranceID');

		return $findResult; 
	}

}
