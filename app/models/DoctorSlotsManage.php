<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class DoctorSlotsManage extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'doctor_slots_manage';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
        public function insertDoctorSlotManage ($dataArray){
                $this->DoctorSlotID = $dataArray['doctorslotid'];
                $this->TotalQueue = $dataArray['totalqueue'];
                $this->CurrentTotalQueue = $dataArray['currenttotalqueue'];
                $this->Date = $dataArray['date'];
                $this->Created_on = time();   
                $this->created_at = time();
                $this->Status = 1;
                $this->Active = 1;
			
                if($this->save()){
                    $insertedId = $this->id;
                    return $insertedId;
                }else{
                    return false;
                }      
        }
        
        public function updateDoctorSlotManage ($dataArray,$doctorslotmgid){
            
            $allData = DB::table('doctor_slots_manage')
            ->where('DoctorSlotManageID', '=', $doctorslotmgid)
            ->update($dataArray);
            
            return $allData;
        }
        
        //this is temporary till confirm
        public function getDoctorSlotManage($slotid){
            $allData = DB::table('doctor_slots_manage')
                    ->where('DoctorSlotID', '=', $slotid)
                    ->where('Active', '=', 1)
                    ->first();
            return $allData;
        }
        public function DoctorSlotManageByDate($slotid,$date){
            $allData = DB::table('doctor_slots_manage')
                    ->where('DoctorSlotID', '=', $slotid)
                    ->where('Date', '=', $date)
                    ->where('Active', '=', 1)
                    ->first();
            return $allData;
        }
        public function getSlotManageById($slotmanageid){
            $allData = DB::table('doctor_slots_manage')
                    ->where('DoctorSlotManageID', '=', $slotmanageid)
                    ->where('Active', '=', 1)
                    ->first();
            return $allData;
        }
        
        public function StopedQueueByDate($slotid,$date){
            $allData = DB::table('doctor_slots_manage')
                    ->where('DoctorSlotID', '=', $slotid)
                    ->where('Date', '=', $date)
                    ->where('Status', '=', 1)
                    ->where('Active', '=', 1)
                    ->first();
            return $allData;
        }
        
        
        
        
        
        

        
        
        
        
        //Grab doctor slow
        //this is temporary till confirm
        public function getDoctorSlot($doctorid,$clinicid){
            $allData = DB::table('doctor_slots')
                    ->where('DoctorID', '=', $doctorid)
                    ->where('ClinicID', '=', $clinicid)
                    ->where('Active', '=', 1)
                    ->first();
                    //->count();
            return $allData;
        }
        
        /* Use          :   It is used to get slot by doctor id
         * Parameter    :   Doctor id
         */
        public function getDoctorSlotByDoctorId($doctorid){
            $allData = DB::table('doctor_slots')
                    ->where('DoctorID', '=', $doctorid)
                    ->where('Active', '=', 1)
                    ->first();
            return $allData;
        }
        
        public function findDoctorSlot($doctorslotid){
            $allData = DB::table('doctor_slots')
                    ->where('DoctorSlotID', '=', $doctorslotid)
                    ->first();
            return $allData;
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        ///Need to delete
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
        
        
        public function FindSlotDoctorClinic($slotid){
            $doctorSlotClinic = DB::table('doctor_slots')
                    ->join('clinic', 'doctor_slots.ClinicID', '=', 'clinic.ClinicID')
                    ->join('doctor', 'doctor_slots.DoctorID', '=', 'doctor.DoctorID')
                    ->select('doctor_slots.DoctorSlotID','doctor_slots.ClinicSession','doctor_slots.ConsultationCharge','doctor_slots.TimeSlot','doctor_slots.TimeSlot', 'clinic.ClinicID', 'clinic.Name', 'clinic.Address','clinic.image','clinic.Lat','clinic.Lng','clinic.Phone','clinic.Opening','doctor.DoctorID','doctor.Name','doctor.Qualifications','doctor.Specialty','doctor.image')
                    ->where('doctor_slots.DoctorSlotID','=',$slotid)
                    ->first();
                if($doctorSlotClinic){
                    return $doctorSlotClinic;
                }else{
                    return FALSE;
                }
        }
        
        

        

       

}
