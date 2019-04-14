<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserAppointmentDetails extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_appointment_details';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
        
        
        
        
        
        
        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
        //                              WEB                                   //
        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
        
        public function InsertAppointmentDetails ($dataArray){
            $this->AppoinmentID = $dataArray['appointment'];
            $this->Diagnosis = $dataArray['diagnosis'];
            $this->Created_on = time();
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
        
        /*
         * 
         * 
         */
        public function SlotDetailsWithDate ($doctorslotid,$slottype,$currentdate){ 
        //public function SlotDetailsWithDate ($doctorslotid,$currentdate){         
            $allData = DB::table('doctor_slot_details')
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('SlotType', '=', $slottype)
                ->where('Date', '=', $currentdate)    
                //->orderBy('SlotID', 'ASC')
                    
                ->where('Active', '=', 1)        
                ->get();

            return $allData;
            
        }
        
        
        
        
        
        
        
        
        
        
        
        public function updateDoctorSlot ($dataArray){
            
            $allData = DB::table('doctor_slots')
            ->where('DoctorSlotID', '=', $dataArray['doctorslotid'])
            ->update(array('ConsultationCharge' => $dataArray['consultationcharge'],
                    'TimeSlot' => $dataArray['timeslot'],
                    'ClinicSession' => $dataArray['clinicsession'],
                    'StartTime' => $dataArray['starttime'],
                    'EndTime' => $dataArray['endtime'],
                    'updated_at' => time() ));
            
            return $allData;
        }
        
        
        
        //Grab doctor slow
        //this is temporary till confirm
        public function getDoctorSlot($doctorid,$clinicid){
            $allData = DB::table('doctor_slots')
                    ->where('DoctorID', '=', $doctorid)
                    ->where('ClinicID', '=', $clinicid)
                    ->first();
                    //->count();
            return $allData;
        }
        
        /*
         * 
         * Next     : need to find for current week
         */
        public function DetailsByType ($doctorslotid,$slottype){         
            $allData = DB::table('doctor_slot_details')
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('SlotType', '=', $slottype)    
                //->orderBy('SlotID', 'ASC')
                ->get();

            return $allData;
            
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        //Get doctor count for clinic
        public function doctorCount ($clinicid){
            $allData = DB::table('doctor_availability')
                    ->where('ClinicID', '=', $clinicid)
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
                    ->select('clinic.ClinicID', 'clinic.Name', 'clinic.Address','clinic.image','clinic.Lat','clinic.Lng','clinic.Phone','clinic.Opening')
                    ->where('doctor_availability.DoctorID','=',$doctorid)
                    ->first();
                if($doctorClinic){
                    return $doctorClinic;
                }else{
                    return FALSE;
                }
        }
        
        

        

       

}
