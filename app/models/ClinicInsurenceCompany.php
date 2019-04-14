<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ClinicInsurenceCompany extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'clinic_insurence_company';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
        //for temporary panel clinic 
        //**** need to change when user have insurance policy *****//
        public function FindClinicInsuranceCompnay($clinicid){
            if($clinicid != ""){
                $ClinicInsurance = DB::table('insurance_company')
                    ->join('clinic_insurence_company', 'insurance_company.CompanyID', '=', 'clinic_insurence_company.InsuranceID')
                    //->join('orders', 'users.id', '=', 'orders.user_id')
                    ->select('insurance_company.CompanyID', 'insurance_company.Name', 'insurance_company.Image','insurance_company.Annotation')
                    ->where('clinic_insurence_company.ClinicID','=',$clinicid)
                    ->where('clinic_insurence_company.Active','=',1)
                    ->where('insurance_company.Active','=',1)    
                    ->first();
                if(count($ClinicInsurance) > 0){
         
                    return $ClinicInsurance;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }


























        
        
        

        

       

}
