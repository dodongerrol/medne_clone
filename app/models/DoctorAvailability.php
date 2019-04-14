<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class DoctorAvailability extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'doctor_availability';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
        
	//protected $hidden = array('password', 'remember_token');
        public function DoctorsInClinic($clinicid){
            $allData = DB::table('doctor_availability')
                ->join('doctor_slots', function($join){
                    $join->on('doctor_availability.DoctorID', '=', 'doctor_slots.DoctorID');
                    $join->on('doctor_availability.ClinicID', '=', 'doctor_slots.ClinicID');        
                })
                ->join('doctor', 'doctor_slots.DoctorID', '=', 'doctor.DoctorID')
                ->select('doctor_availability.DoctorAvailabilityID', 'doctor_availability.DoctorID','doctor_availability.ClinicID','doctor_availability.Active','doctor_slots.DoctorSlotID','doctor_slots.ClinicSession','doctor_slots.ConsultationCharge','doctor_slots.TimeSlot','doctor_slots.QueueNumber','doctor_slots.QueueTime',
                        'doctor.DoctorID','doctor.Name','doctor.Email','doctor.Qualifications','doctor.Specialty','doctor.image','doctor.Phone','doctor.Active')    
                ->where('doctor_availability.ClinicID', '=', $clinicid)
                ->where('doctor_availability.Active', '=', 1)
                ->where('doctor_slots.Active', '=', 1)
                ->where('doctor.Active', '=', 1)        
                ->get();
            return $allData;
        }
        
        //Get doctor count for clinic
        public function doctorCount ($clinicid){
            $allData = DB::table('doctor_availability')
                    ->where('ClinicID', '=', $clinicid)
                    ->where('Active', '=', 1)
                    ->count();
            if($allData){
                 return $allData;
            }else{
               return FALSE; 
            }
        }
        
        //Grab full doctor list for a particular clinic
        public function findDoctorsForClinic ($clinicid){
            $allData = DB::table('doctor_availability')
                    ->where('ClinicID', '=', $clinicid)
                    ->where('Active', '=', 1)
                    ->get();
                    //->count();
            return $allData;
        }
        
        //for temporary panel clinic 
        //**** need to change when user have insurance policy *****//
        public function FindClinicForDoctor($doctorid){
                $doctorClinic = DB::table('doctor_availability')
                    ->join('clinic', 'doctor_availability.ClinicID', '=', 'clinic.ClinicID')
                    //->join('orders', 'users.id', '=', 'orders.user_id')
                    ->select('clinic.ClinicID', 'clinic.Name', 'clinic.Address','clinic.image','clinic.Lat','clinic.Lng','clinic.Phone','clinic.Opening','clinic.Clinic_Price')
                    ->where('doctor_availability.DoctorID','=',$doctorid)
                    ->where('doctor_availability.Active','=',1)   
                    ->where('clinic.Active','=',1)       
                    ->first();
                if($doctorClinic){
                    return $doctorClinic;
                }else{
                    return FALSE;
                }
        }
        
        //Add new records
        public function insertDoctorAvailability($dataArray){
            $this->DoctorID = $dataArray['doctorid'];
            $this->ClinicID = $dataArray['clinicid'];
            $this->StartTime = 0;
            $this->EndTime = 0;
            $this->created_at = time();
            $this->updated_at = 0;
            $this->Active = 1;
            
            if($this->save()){
                $insertedId = $this->id;
                return $insertedId;
            }else{
                return false;
            } 
        }

        public function FindActiveDoctorsForClinic ($clinicid){
            $allData = DB::table('doctor_availability')
                //->join('doctor_slots', 'doctor_availability.ClinicID', '=', 'doctor_slots.ClinicID' ,'AND', 'doctor_availability.DoctorID', '=', 'doctor_slots.DoctorID')
                //->join('doctor_slots', 'doctor_availability.DoctorID', '=', 'doctor_slots.DoctorID')
                ->join('doctor_slots', function($join){
                    $join->on('doctor_availability.DoctorID', '=', 'doctor_slots.DoctorID');
                    $join->on('doctor_availability.ClinicID', '=', 'doctor_slots.ClinicID');        
                })
                ->select('doctor_availability.DoctorAvailabilityID', 'doctor_availability.DoctorID','doctor_availability.ClinicID','doctor_availability.Active','doctor_slots.DoctorSlotID','doctor_slots.ClinicSession','doctor_slots.ConsultationCharge','doctor_slots.TimeSlot','doctor_slots.QueueNumber','doctor_slots.QueueTime')    
                ->where('doctor_availability.ClinicID', '=', $clinicid)
                ->where('doctor_availability.Active', '=', 1)
                ->where('doctor_slots.Active', '=', 1)
                ->where('doctor_slots.ClinicSession', '!=', 0)
                ->get();
                    //->count();
            return $allData;
        }
        
        public function FindAllClinicDoctors ($clinicid){
            $allData = DB::table('doctor_availability')
                    ->join('clinic', 'doctor_availability.ClinicID', '=', 'clinic.ClinicID')
                    ->join('doctor', 'doctor_availability.DoctorID', '=', 'doctor.DoctorID')        
                ->select('doctor_availability.DoctorAvailabilityID', 
                        'doctor.DoctorID','doctor.Name as DocName','doctor.Email as DocEmail','doctor.image as DocImage','doctor.Phone as DocPhone','doctor.Qualifications','doctor.Specialty','doctor.Active as DocActive',
                        'clinic.ClinicID', 'clinic.Name as CliName', 'clinic.Clinic_Type','clinic.image as CliImage','clinic.Phone as CliPhone','clinic.Active as CliActive', 'doctor.phone_code as DocPhoneCode', 'clinic.Phone_Code as CliPhoneCode')    
                ->where('doctor_availability.ClinicID', '=', $clinicid)
                ->where('doctor_availability.Active', '=', 1)
                ->where('clinic.Active', '=', 1)
                ->where('doctor.Active', '=', 1)
                ->get();
                    //->count();
            return $allData;
        }
        
        public function FindClinicDoctorSelection ($clinicid,$doctorlist){
            $allData = DB::table('doctor_availability')
                    ->join('clinic', 'doctor_availability.ClinicID', '=', 'clinic.ClinicID')
                    ->join('doctor', 'doctor_availability.DoctorID', '=', 'doctor.DoctorID')        
                ->select('doctor_availability.DoctorAvailabilityID', 
                        'doctor.DoctorID','doctor.Name as DocName','doctor.Email as DocEmail','doctor.image as DocImage','doctor.Phone as DocPhone','doctor.Qualifications','doctor.Specialty','doctor.Active as DocActive',
                        'clinic.ClinicID', 'clinic.Name as CliName', 'clinic.Clinic_Type','clinic.image as CliImage','clinic.Phone as CliPhone','clinic.Active as CliActive')    
                ->where('doctor_availability.ClinicID', '=', $clinicid)
                ->whereIn('doctor_availability.DoctorID', $doctorlist)    
                ->where('doctor_availability.Active', '=', 1)
                ->where('clinic.Active', '=', 1)
                ->where('doctor.Active', '=', 1)
                ->get();
                    //->count();
            //print_r($allData);
            return $allData;
        }
        
        public function FindSingleClinicDoctor($clinicid, $doctorid){
                $doctorClinic = DB::table('doctor_availability')
                    ->join('clinic', 'doctor_availability.ClinicID', '=', 'clinic.ClinicID')
                    ->join('doctor', 'doctor_availability.DoctorID', '=', 'doctor.DoctorID')    
                    //->join('orders', 'users.id', '=', 'orders.user_id')
                    ->select('clinic.ClinicID', 'clinic.Name as CliName', 'clinic.Address','clinic.image as CliImage','clinic.Lat','clinic.Lng','clinic.Phone as CliPhone','clinic.Opening','clinic.Clinic_Price',
                            'doctor.DoctorID','doctor.Name as DocName','doctor.Email as DocEmail','doctor.image as DocImage','doctor.Qualifications','doctor.Specialty','doctor.Phone as DocPhone','doctor.Emergency as DocEmergency','doctor.Code as DocCode','doctor.Emergency_Code as DocEmCode')
                    ->where('doctor_availability.DoctorID','=',$doctorid)
                    ->where('doctor_availability.ClinicID','=',$clinicid)    
                    ->where('doctor_availability.Active','=',1)   
                    ->where('clinic.Active','=',1)       
                    ->first();
                if($doctorClinic){
                    return $doctorClinic;
                }else{
                    return FALSE;
                }
        }

        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
        //                  For Mobile                          //
        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
        
        public function FindClinicDoctor($clinicid, $doctorid){
                $doctorClinic = DB::table('doctor_availability')
                    ->join('clinic', 'doctor_availability.ClinicID', '=', 'clinic.ClinicID')
                    ->join('doctor', 'doctor_availability.DoctorID', '=', 'doctor.DoctorID')    
                    //->join('orders', 'users.id', '=', 'orders.user_id')
                    ->select('clinic.ClinicID', 'clinic.Name as CLName', 'clinic.Address as CLAddress','clinic.City as CLCity','clinic.State as CLState','clinic.Postal as CLPostal','clinic.image as CLImage','clinic.Lat as CLLat','clinic.Lng as CLLng','clinic.Phone as Phone','clinic.Clinic_Price','clinic.Description','clinic.Website','clinic.Custom_title',
                            'doctor.DoctorID','doctor.Name as DocName','doctor.Email as DocEmail','doctor.image as DocImage','doctor.Qualifications','doctor.Specialty')
                    ->where('doctor_availability.DoctorID','=',$doctorid)
                    ->where('doctor_availability.ClinicID','=',$clinicid)    
                    ->where('doctor_availability.Active','=',1)   
                    ->where('clinic.Active','=',1) 
                    ->where('doctor.Active','=',1)     
                    ->first();
            return $doctorClinic;
        }
        public function FindSingleClinicDoctorBoth($clinicid, $doctorid){
                $doctorClinic = DB::table('doctor_availability')
                    ->join('clinic', 'doctor_availability.ClinicID', '=', 'clinic.ClinicID')
                    ->join('doctor', 'doctor_availability.DoctorID', '=', 'doctor.DoctorID')    
                    //->join('orders', 'users.id', '=', 'orders.user_id')
                    ->select('clinic.ClinicID', 'clinic.Name as CliName', 'clinic.Address','clinic.image as CliImage','clinic.Lat','clinic.Lng','clinic.Phone as CliPhone','clinic.Opening','clinic.Clinic_Price',
                            'doctor.DoctorID','doctor.Name as DocName','doctor.Email as DocEmail','doctor.image as DocImage','doctor.Qualifications','doctor.Specialty','doctor.Phone as DocPhone','doctor.Emergency as DocEmergency','doctor.Code as DocCode','doctor.Emergency_Code as DocEmCode')
                    ->where('doctor_availability.DoctorID','=',$doctorid)
                    ->where('doctor_availability.ClinicID','=',$clinicid)    
                    //->where('doctor_availability.Active','=',1)   
                    //->where('clinic.Active','=',1)       
                    ->first();
                if($doctorClinic){
                    return $doctorClinic;
                }else{
                    return FALSE;
                }
        }

}
