<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class InsuranceCompany extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'insurance_company';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
        //Get doctor count for clinic
        public function FindAnnotation ($name){
            $findResult = $allData = DB::table('insurance_company')
                    ->where('Name', '=', $name)
                    ->first();
            
            if($findResult){
                return $findResult;
            }else{
                return FALSE;
            }
        }
        
        //Get doctor count for clinic
        public function findInsuranceCompany (){
            $findResult = $allData = DB::table('insurance_company')
                    ->where('Active', '=', 1)
                    ->get();
           
            return $findResult; 
        }
        public function InsuranceCompanyByID ($companyid){
            $findResult = $allData = DB::table('insurance_company')
                    ->where('CompanyID', '=', $companyid)
                    ->where('Active', '=', 1)
                    ->first();
           
            return $findResult; 
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
	//protected $hidden = array('password', 'remember_token');
        
        //Get doctor count for clinic
        public function doctorCount ($clinicid){
            $allData = DB::table('doctor_availability')
                    ->where('ClinicID', '=', $clinicid)
                    ->count();
            return $allData;
        }
        
        //Grab full doctor list for a particular clinic
        public function findDoctorsForClinic ($clinicid){
            $allData = DB::table('doctor_availability')
                    ->where('ClinicID', '=', $clinicid)
                    ->get();
                    //->count();
            return $allData;
        }
        
        
        
        

        

       

}
