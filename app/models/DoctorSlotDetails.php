<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class DoctorSlotDetails extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'doctor_slot_details';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
        public function insertSlotDetails ($dataArray){
            $this->DoctorSlotID = $dataArray['doctorslot'];
            $this->SlotID = $dataArray['slotid'];
            $this->SlotType = $dataArray['slottype'];
            $newDate = date("d-m-Y", strtotime($dataArray['date']));
            $this->Date = $newDate;
            $this->Time = $dataArray['slottime'];
            $this->Available = 1;
            $this->created_at = time();
            $this->Created_on = time();
            $this->Active = 1;

            if($this->save()){
                $insertedId = $this->id;
                return $insertedId;
            }else{
                return false;
            }      
        }
        
        
        public function updateSlotDetails ($dataArray){         
            $allData = DB::table('doctor_slot_details')
                ->where('SlotDetailID', '=', $dataArray['slotdetailid'])
                ->update($dataArray);
            
            return $allData;
        }
        
        public function getSlotDetails ($slotdetailid){         
            $allData = DB::table('doctor_slot_details')
                ->where('SlotDetailID', '=', $slotdetailid)
                ->first();    
            return $allData;
        }
        
        public function getAllSlotDetails ($doctorslotid){         
            $allData = DB::table('doctor_slot_details')
                ->where('DoctorSlotID', '=', $doctorslotid)
                //->orderBy('SlotID', 'ASC')
                ->get();

            return $allData;
            
        }
        public function getBookingSlot ($doctorslotid,$slottype,$currentdate){         
            $allData = DB::table('doctor_slot_details')
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('SlotType', '=', $slottype)    
                ->where('Date', '=', $currentdate)
                ->where('Active', '=', 1)    
                
                ->orderBy('Time', 'ASC')
                ->get();
            return $allData;     
        }
        public function ActiveBookingSlot ($doctorslotid,$slottype,$currentdate){         
            $allData = DB::table('doctor_slot_details')
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('SlotType', '=', $slottype)    
                ->where('Date', '=', $currentdate)
                ->where('Active', '=', 1)    
                
                ->where('Time', '>=',StringHelper::CurrentTimeSetup($currentdate))
                    
                    
                ->orderBy('Time', 'ASC')
                ->get();
            //echo StringHelper::CurrentTimeSetup();
            // echo '<pre>'; print_r($allData); echo '</pre>';
            return $allData;     
        }
        public function FindActiveSlotForBooking ($doctorslotid,$slottype,$currentdate){         
            $allData = DB::table('doctor_slot_details')
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('SlotType', '=', $slottype)    
                ->where('Date', '=', $currentdate)
                ->where('Available', '=', 1)    
                ->where('Active', '=', 1)    
                
                ->where('Time', '>=',StringHelper::CurrentTimeSetup($currentdate))    
                ->orderBy('Time', 'ASC')
                ->get();
            return $allData;     
        }
        public function FindBookingSlot ($slotdetailid,$slottype,$currentdate){         
            $allData = DB::table('doctor_slot_details')
                ->where('SlotDetailID', '=', $slotdetailid)
                ->where('SlotType', '=', $slottype)
                ->where('Available', '=', 1)    
                ->where('Date', '=', $currentdate)
                ->where('Active', '=', 1)    
                
                ->orderBy('Time', 'ASC')
                ->first();
            return $allData;     
        }
        public function ActiveSlotDetails ($slotdetailid){         
            $allData = DB::table('doctor_slot_details')
                ->where('SlotDetailID', '=', $slotdetailid)
                ->where('Active', '=', 1)    
                ->first();    
            return $allData;
        }
        public function FindBookingConfirmSlot ($slotdetailid,$currentdate){         
            $allData = DB::table('doctor_slot_details')
                ->where('SlotDetailID', '=', $slotdetailid)
                //->where('SlotType', '=', $slottype)
                ->where('Available', '=', 1)    
                ->where('Date', '=', $currentdate)
                ->where('Active', '=', 1)    
                
                ->orderBy('Time', 'ASC')
                ->first();
            return $allData;     
        }
        
        
        
        
        
        
        
        
        
        
        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
        //                              WEB                                   //
        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
        
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
                ->orderBy('Time', 'ASC')    
                ->where('Active', '=', 1)        
                ->get();

            return $allData;
            
        }
        public function FindAppointmentSlotDetail ($doctorslotid,$slotdetailid,$currentdate){ 
        //public function SlotDetailsWithDate ($doctorslotid,$currentdate){         
            $returnData = DB::table('doctor_slot_details')
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('SlotDetailID', '=', $slotdetailid)    
                ->where('Date', '=', $currentdate)    
                ->where('Available', '=', 2)      
                ->where('Active', '=', 1)        
                ->first();

            return $returnData;
            
        }
        
        public function FindActiveSlotDetails ($doctorslotid,$currentdate){          
            $returnData = DB::table('doctor_slot_details')
                ->select('SlotDetailID','SlotID','SlotType','Date','Time','Available')
                ->where('DoctorSlotID', '=', $doctorslotid) 
                ->where('Date', '=', $currentdate)    
                //->where('Time', '>=',StringHelper::CurrentTimeSetup($currentdate))     
                ->where('Active', '=', 1)        
                ->get();

            return $returnData;
            
        }
        public function FindActiveSlotDetailsType ($doctorslotid,$currentdate,$slottype){          
            $returnData = DB::table('doctor_slot_details')
                ->select('SlotDetailID','SlotID','SlotType','Date','Time','Available')
                ->where('DoctorSlotID', '=', $doctorslotid) 
                ->where('SlotType', '=', $slottype)    
                ->where('Date', '=', $currentdate)    
                //->where('Time', '>=',StringHelper::CurrentTimeSetup($currentdate))     
                ->where('Active', '=', 1)        
                ->get();

            return $returnData;
            
        }
        
        
        public function FindSlotDetails ($slotdetailid){         
            $allData = DB::table('doctor_slot_details')
                ->where('SlotDetailID', '=', $slotdetailid)
                ->where('Active', '=', 1)    
                ->first();    
            return $allData;
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        public function insertDoctorSlot ($dataArray){
                $this->DoctorID = $dataArray['doctorid'];
                $this->ClinicID = $dataArray['clinicid'];
                $this->ClinicSession = $dataArray['clinicsession'];
                $this->ConsultationCharge = $dataArray['consultationcharge'];
                $this->TimeSlot = $dataArray['timeslot'];
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
