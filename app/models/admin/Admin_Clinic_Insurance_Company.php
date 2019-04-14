<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Admin_Clinic_Insurance_Company extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'clinic_insurence_company';
	protected $primaryKey = 'ClinicInsurenceID';
	public $timestamps = false;

	public function AddClinicInsuranceData($id, $clinicId)
    {
    	$clinicInsurance = DB::table('clinic_insurence_company')
    	    ->insert(
	            array('InsuranceID' => $id, 'Active' => '1', 'ClinicID'=> $clinicId)
	        ); 

	    return $clinicInsurance;         
    }


    public function UpdateInsurenceStatusInactive($id)
    {
    	$insuranceStatus = DB::table('clinic_insurence_company')
			    ->where('ClinicID',$id)					    
			    ->update(array('Active' => '0'));

	    return $insuranceStatus;         
    }

    public function UpdateInsurenceStatusActive($id, $insuranceIDNew)
    {
    	$insuranceStatus = DB::table('clinic_insurence_company')
			    ->where('ClinicID',$id)
			    ->where('InsuranceID', '=', $insuranceIDNew)
			    ->update(array('Active' => '1'));

	    return $insuranceStatus;         
    }

    //Get all insurence company details selected value
	public function GetInsurenceCompanyDetails($id, $insuranceIDNew)
	{
		$insuranceData = DB::table('clinic_insurence_company')
               	->where('ClinicID',$id)
                ->where('InsuranceID', '=', $insuranceIDNew)
                ->first();
                
		return $insuranceData;
	}

}
