<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Admin_Doctor_Availability extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'doctor_availability';
	// protected $primaryKey = 'Ref_ID';


	public function AddDoctorAvailability($doctorId, $clinicId)
    {
    	$doctorAvailabilityData = DB::table('doctor_availability')
    		->insert(
		            array('DoctorID' => $doctorId, 'Active' => '1', 'ClinicID'=> $clinicId)
		        );

	    return $doctorAvailabilityData;         
    }
    
    public function FindAllClinicDoctors ($clinicid){
        $allData = DB::table('doctor_availability')
                ->join('clinic', 'doctor_availability.ClinicID', '=', 'clinic.ClinicID')
                ->join('doctor', 'doctor_availability.DoctorID', '=', 'doctor.DoctorID')        
            ->select('doctor_availability.DoctorAvailabilityID', 
                    'doctor.DoctorID','doctor.Name as DocName','doctor.Email as DocEmail','doctor.image as DocImage','doctor.Phone as DocPhone','doctor.Qualifications','doctor.Specialty','doctor.Active as DocActive',
                    'clinic.ClinicID', 'clinic.Name as CliName', 'clinic.Clinic_Type','clinic.image as CliImage','clinic.Phone as CliPhone','clinic.Active as CliActive')    
            ->where('doctor_availability.ClinicID', '=', $clinicid)
            //->where('doctor_availability.Active', '=', 1)
            //->where('clinic.Active', '=', 1)
            //->where('doctor.Active', '=', 1)
            ->get();
        return $allData;
    }

}
