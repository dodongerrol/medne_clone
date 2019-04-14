<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class DoctorSlots extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'doctor_slots';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
        public function insertDoctorSlot ($dataArray){
                $this->DoctorID = $dataArray['doctorid'];
                $this->ClinicID = $dataArray['clinicid'];
                $this->ClinicSession = $dataArray['clinicsession'];
                $this->ConsultationCharge = $dataArray['consultationcharge'];
                $this->TimeSlot = $dataArray['timeslot'];
                
                //this is for queue numbers
                    $this->QueueNumber = $dataArray['queueno'];
                    $this->QueueTime = $dataArray['queuetime'];
                
                $this->created_at = time();
                $this->StartTime = $dataArray['starttime'];
                $this->EndTime = $dataArray['endtime'];
                $this->Active = 1;
			
                if($this->save()){
                    $insertedId = $this->id;
                    return $insertedId;
                }else{
                    return false;
                }      
        }
//        public function updateDoctorSlot ($dataArray){
//            
//            $allData = DB::table('doctor_slots')
//            ->where('DoctorSlotID', '=', $dataArray['doctorslotid'])
//            ->update(array('ConsultationCharge' => $dataArray['consultationcharge'],
//                    'TimeSlot' => $dataArray['timeslot'],
//                    'ClinicSession' => $dataArray['clinicsession'],
//                    'QueueNumber' => $dataArray['queueno'],
//                    'QueueTime' => $dataArray['queuetime'],
//                    'StartTime' => $dataArray['starttime'],
//                    'EndTime' => $dataArray['endtime'],
//                    'updated_at' => time() ));
//            
//            return $allData;
//        }
        public function updateDoctorSlot ($dataArray){
            
            $allData = DB::table('doctor_slots')
            ->where('DoctorSlotID', '=', $dataArray['doctorslotid'])
            ->update($dataArray);
            
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
                    ->where('Active', '=', 1)
                    ->first();
            return $allData;
        }
        public function FindDoctorSlotClinicService($doctorslotid){
            $allData = DB::table('doctor_slots')
                    ->join('doctor', 'doctor_slots.DoctorID', '=', 'doctor.DoctorID')
                    ->join('clinic', 'doctor_slots.ClinicID', '=', 'clinic.ClinicID')
                    ->select('doctor_slots.DoctorSlotID','doctor_slots.DoctorID','doctor_slots.ClinicID','doctor_slots.ClinicSession','doctor_slots.ConsultationCharge','doctor_slots.TimeSlot','doctor_slots.QueueNumber','doctor_slots.QueueTime',
                            'doctor.Name as DName','doctor.Email','doctor.Qualifications','doctor.Specialty','doctor.Phone','doctor.image',
                            'clinic.Name as CName','clinic.Address','clinic.image','clinic.Phone as CPhone')
                    ->where('doctor_slots.DoctorSlotID', '=', $doctorslotid)
                    //->where('doctor_slots.ClinicID', '=', $clinicid)
                    //->whereIn('doctor_slots.ClinicSession', array(1,2))           
                    ->where('doctor_slots.Active', '=', 1)
                    ->where('doctor.Active', '=', 1)
                    ->where('clinic.Active', '=', 1)
                    ->first();

            return $allData;
        }
        
        
        
        
        
        
    
        
        
    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
    //                              WEB                                   //
    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
        
    public function DoctorSlotForDoctor($doctorid){
        $allData = DB::table('doctor_slots')
                ->where('DoctorID', '=', $doctorid)
                ->where('Active', '=', 1)
                ->first();
        return $allData;
    }    
    public function FindDoctorSlotBySlotID($doctorslotid){
        $allData = DB::table('doctor_slots')
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('Active', '=', 1)
                ->first();
        return $allData;
    }    
        
    public function TodayAvailableDoctors($clinicid, $currentdate){
        $allData = DB::table('doctor_slots')
                ->join('doctor', 'doctor_slots.DoctorID', '=', 'doctor.DoctorID')
                ->select('doctor_slots.DoctorSlotID','doctor_slots.DoctorID','doctor_slots.ClinicID','doctor_slots.ClinicSession','doctor_slots.ConsultationCharge','doctor_slots.TimeSlot','doctor_slots.QueueNumber','doctor_slots.QueueTime',
                        'doctor.Name','doctor.Email','doctor.Qualifications','doctor.Specialty','doctor.Phone','doctor.image')
                //->where('doctor_slots.ClinicSession', '=', 1)
                ->where('doctor_slots.ClinicID', '=', $clinicid)
                ->whereIn('doctor_slots.ClinicSession', array(1,2))           
                ->where('doctor_slots.Active', '=', 1)
                ->where('doctor.Active', '=', 1)
                ->get();
        
        return $allData;
    }
    public function FindDoctorDetailBySlot($doctorslotid){
        $allData = DB::table('doctor_slots')
                ->join('doctor', 'doctor_slots.DoctorID', '=', 'doctor.DoctorID')
                ->select('doctor_slots.DoctorSlotID','doctor_slots.DoctorID','doctor_slots.ClinicID','doctor_slots.ClinicSession','doctor_slots.ConsultationCharge','doctor_slots.TimeSlot','doctor_slots.QueueNumber','doctor_slots.QueueTime',
                        'doctor.Name','doctor.Email','doctor.Qualifications','doctor.Specialty','doctor.Phone','doctor.image')
                ->where('doctor_slots.DoctorSlotID', '=', $doctorslotid)
                //->where('doctor_slots.ClinicID', '=', $clinicid)
                ->whereIn('doctor_slots.ClinicSession', array(1,2))           
                ->where('doctor_slots.Active', '=', 1)
                ->where('doctor.Active', '=', 1)
                ->first();
        
        return $allData;
    }
    public function TodayAvailableDoctors1($currentdate){
        //Findslot Available
        $slotData = DB::table('doctor_slots')
                ->join('doctor', 'doctor_slots.DoctorID', '=', 'doctor.DoctorID')
                ->join('doctor_slot_details', 'doctor_slots.DoctorSlotID', '=', 'doctor_slot_details.DoctorSlotID')
                ->select('doctor_slots.DoctorSlotID','doctor_slots.DoctorID','doctor_slots.ClinicID','doctor_slots.ClinicSession','doctor_slots.ConsultationCharge','doctor_slots.TimeSlot',
                        'doctor_slot_details.SlotDetailID','doctor_slot_details.SlotID','doctor_slot_details.SlotType','doctor_slot_details.Date','doctor_slot_details.Time','doctor_slot_details.Available',
                        'doctor.Name','doctor.Email','doctor.Qualifications','doctor.Specialty','doctor.Phone','doctor.image')
                ->where('doctor_slots.ClinicSession', '=', 2)
                //->groupBy('doctor_slots.DoctorID')
                ->where('doctor_slots.Active', '=', 1)
                
                ->where('doctor_slot_details.Date', '=', $currentdate)
                ->where('doctor_slot_details.Time', '>=',StringHelper::CurrentTimeSetup($currentdate))
                //->where('doctor_slot_details.Available', '=', 1)
                ->where('doctor_slot_details.Active', '=', 1)
                ->get();
        
        
        $queueData = DB::table('doctor_slots')
                ->join('doctor', 'doctor_slots.DoctorID', '=', 'doctor.DoctorID')
                //->join('doctor_slot_details', 'doctor_slots.DoctorSlotID', '=', 'doctor_slot_details.DoctorSlotID')
                ->select('doctor_slots.DoctorSlotID','doctor_slots.DoctorID','doctor_slots.ClinicID','doctor_slots.ClinicSession','doctor_slots.ConsultationCharge','doctor_slots.QueueNumber','doctor_slots.QueueTime',
                        'doctor.Name','doctor.Email','doctor.Qualifications','doctor.Specialty','doctor.Phone')
                ->where('doctor_slots.ClinicSession', '=', 1)
                ->where('doctor_slots.Active', '=', 1)
                
                //->where('doctor_slot_details.Date', '=', $currentdate)
                //->where('doctor_slot_details.Time', '>=',StringHelper::CurrentTimeSetup($currentdate))
                //->where('doctor_slot_details.Active', '=', 1)
                ->get();
        
        $totalArray = array_merge($slotData,$queueData);
        //$returnArray['doctordetails'] = $totalArray;
        return $totalArray;
    }    
        
     
    public function FindDoctorSlotClinic($doctorslotid){
        $allData = DB::table('doctor_slots')
                ->join('doctor', 'doctor_slots.DoctorID', '=', 'doctor.DoctorID')
                ->join('clinic', 'doctor_slots.ClinicID', '=', 'clinic.ClinicID')
                ->select('doctor_slots.DoctorSlotID','doctor_slots.DoctorID','doctor_slots.ClinicID','doctor_slots.ClinicSession','doctor_slots.ConsultationCharge','doctor_slots.TimeSlot','doctor_slots.QueueNumber','doctor_slots.QueueTime',
                        'doctor.Name as DName','doctor.Email','doctor.Qualifications','doctor.Specialty','doctor.Phone','doctor.image',
                        'clinic.Name as CName','clinic.Address','clinic.image','clinic.Phone as CPhone')
                ->where('doctor_slots.DoctorSlotID', '=', $doctorslotid)
                //->where('doctor_slots.ClinicID', '=', $clinicid)
                //->whereIn('doctor_slots.ClinicSession', array(1,2))           
                ->where('doctor_slots.Active', '=', 1)
                ->where('doctor.Active', '=', 1)
                ->where('clinic.Active', '=', 1)
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
                    ->select('doctor_slots.DoctorSlotID','doctor_slots.ClinicSession','doctor_slots.ConsultationCharge','doctor_slots.TimeSlot','doctor_slots.TimeSlot', 'clinic.ClinicID', 'clinic.Name as CName', 'clinic.Address','clinic.image','clinic.Lat','clinic.Lng','clinic.Phone','clinic.Opening','doctor.DoctorID','doctor.Name','doctor.Qualifications','doctor.Specialty','doctor.image')
                    ->where('doctor_slots.DoctorSlotID','=',$slotid)
                    ->first();
                if($doctorSlotClinic){
                    return $doctorSlotClinic;
                }else{
                    return FALSE;
                }
        }
        public function FindClinicDoctorSlot($doctorid,$clinicid){
            $allData = DB::table('doctor_slots')
                    ->where('DoctorID', '=', $doctorid)
                    ->where('ClinicID', '=', $clinicid)
                    ->where('Active', '=', 1)
                    ->first();
            return $allData;
        } 
        
        public function findClinicIDbyDoctorSlotID($id)
        {
            return  DoctorSlots::where('DoctorSlotID', '=', $id)->first();
        }
        

       

}
