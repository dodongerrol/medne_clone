<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserAppoinment extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_appoinment';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = array('password', 'remember_token');
    public function NewAppointment($dataArray){
        $this->UserID = $dataArray['userid'];
        $this->ClinicTimeID = $dataArray['clinictimeid'];
        $this->DoctorID = $dataArray['doctorid'];
        $this->ProcedureID = $dataArray['procedureid'];
        $this->StartTime = $dataArray['starttime'];
        $this->EndTime = $dataArray['endtime'];
        $this->Remarks = $dataArray['remarks'];
        $this->BookType = 1;
        $this->DoctorSlotID = 0;
        $this->SlotDetailID = 0;
        $this->MediaType = $dataArray['mediatype']; // 0 - Mobile, 1 - Web
        $this->BookNumber = 0;
        $this->BookDate = $dataArray['bookdate'];
        $this->Price = $dataArray['price'];
        $this->duration = $dataArray['duration'];
        $this->Created_on = time();
        $this->created_at = time();
        // $this->updated_at = 0;
        $this->Status = 0;
        $this->Active = 1;
        $this->event_type = 0;

        // echo $dataArray;
        if($this->save()){
            $insertedId = $this->id;
            return $insertedId;
        }else{
            return false;
        }      
    }
// nhr
    public function NewAppointment1($dataArray){
        $this->UserID = $dataArray['userid'];
        $this->ClinicTimeID = $dataArray['clinictimeid'];
        $this->DoctorID = $dataArray['doctorid'];
        $this->ProcedureID = $dataArray['procedureid'];
        $this->StartTime = $dataArray['starttime'];
        $this->EndTime = $dataArray['endtime'];
        //$this->SlotPlace = $dataArray['slotplace'];
        $this->Remarks = $dataArray['remarks'];
        $this->BookType = 1;
        $this->DoctorSlotID = 0;
        $this->SlotDetailID = 0;
        $this->MediaType = $dataArray['mediatype']; // 0 - Mobile, 1 - Web
        $this->BookNumber = 0;
        $this->BookDate = $dataArray['bookdate'];
        $this->Created_on = time();
        $this->created_at = time();
        // $this->updated_at = 0;
        $this->Status = 0;
        $this->Active = 1;
        $this->Gc_event_id = $dataArray['event_id'];

        // echo $dataArray;
        if($this->save()){
            $insertedId = $this->id;
            return $insertedId;
        }else{
            return false;
        }      
    }

      public function removeGoogleEvent($doctorid)
    {
       
            DB::table('user_appoinment')
                ->where('event_type', '=', 1)
                ->where('DoctorID', '=', $doctorid)

                // ->where('BookDate', '>=', strtotime(date('Y-m-d')) )
                // ->Where(function ($query) {
                //     $query->where('BookDate', '>', strtotime(date('Y-m-d')) )
                //           ->orwhere('StartTime', '>', strtotime(date('Y-m-d H:i:s')) );
                // })
                ->delete();

            // dd($allDasta);
    }
    
//nhr delete past google event
    public function deleteGoogleEvent($date)
    {
            DB::table('user_appoinment')
                ->where('event_type', '=', 1)
                ->where('BookDate', '<=', strtotime($date))
                ->delete();
    }


    public function insertUserAppointment ($dataArray){
            $this->UserID = $dataArray['userid'];
            $this->BookType = $dataArray['booktype']; // 0 - Queue number, 1 - Slot
            $this->DoctorSlotID = $dataArray['doctorslotid'];
            $this->SlotDetailID = $dataArray['slotdetailid'];
            $this->MediaType = $dataArray['mediatype']; // 0 - Mobile, 1 - Web
            $this->BookNumber = $dataArray['booknumber'];
            $this->BookDate = $dataArray['bookdate'];
            $this->Created_on = time();
            $this->created_at = time();
            // $this->updated_at = 0;
            $this->Status = 0;
            $this->Active = 1;
            
            if($this->save()){
                $insertedId = $this->id;
                return $insertedId;
            }else{
                return false;
            }      
    }
    public function updateUserAppointment ($dataArray,$appointid){         
        $allData = DB::table('user_appoinment')
            ->where('UserAppoinmentID', '=', $appointid)
            ->update($dataArray);
        
        return $allData;
    }
    
   
    //Use           :   To get Queue history 
    //parameter :  
    //Out put : array 
    public function getQueueDetails($booktype,$slotid,$bookdate){           
        $findQueue = DB::table('user_appoinment')
                ->where('BookType', '=', $booktype)
                ->where('DoctorSlotID', '=', $slotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Active', '=', 1)
                ->orderBy('BookNumber', 'desc')
                ->get();
        return $findQueue; 
    }
    public function CountQueueBooking($booktype,$slotid,$bookdate){           
        $findQueue = DB::table('user_appoinment')
                ->where('BookType', '=', $booktype)
                ->where('DoctorSlotID', '=', $slotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Status', '!=', 3)
                ->where('Active', '=', 1)
                ->orderBy('BookNumber', 'desc')
                ->count();
        return $findQueue; 
    }
    
    /* Use          :   Used to get slot booking
     * 
     * Return       :   One records
     */
    public function getSlotBooking($booktype,$slotdetailid){           
        $findSlot = DB::table('user_appoinment')
                ->where('BookType', '=', $booktype)
                //->where('MediaType', '=', $mediatype)
                ->where('SlotDetailID', '=', $slotdetailid)
                //->where('BookDate', '=', $bookdate)
                ->where('Active', '=', 1)
                ->first();
        
        return $findSlot; 
    }
    
    
    /* Use          :   Used to get slot booking
     * 
     * Return       :   One records
     */
    public function GetAppointmentByUser($userid){           
        $findAppointment = DB::table('user_appoinment')
                ->where('UserID', '=', $userid)
                ->where('Active', '=', 1)
                ->orderBy('BookDate', 'desc')
                ->get();
        
        return $findAppointment; 
    }
    public function FindUserAppointments($userid){           
        $findAppointment = DB::table('user_appoinment')
                ->where('UserID', '=', $userid)
                ->where('Active', '=', 1)
                ->where('DoctorID', '!=', "")
                ->orderBy('BookDate', 'desc')
                ->get();
        
        return $findAppointment; 
    }
    
    public function findNumberOfBooking($slotid,$bookdate){           
        $countQueue = DB::table('user_appoinment')
                ->where('DoctorSlotID', '=', $slotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Active', '=', 1)
                ->count();
        return $countQueue; 
    }
    
    public function findAppointment($appointmentid){
        $findSlot = DB::table('user_appoinment')
                ->where('UserAppoinmentID', '=', $appointmentid)
                ->where('Active', '=', 1)
                ->first();
        
        return $findSlot; 
    }

// nhr 2016-2-3
    public function findAppointmentByGcEvent($Gc_event_id){
        $findSlot = DB::table('user_appoinment')
                ->where('Gc_event_id', '=', $Gc_event_id)
                ->where('Active', '=', 1)
                ->first();
        
        return $findSlot; 
    }


    public function FindBookedQueue($booktype,$slotid,$bookdate,$queueno){           
        $findQueue = DB::table('user_appoinment')
                ->where('BookType', '=', $booktype)
                ->where('DoctorSlotID', '=', $slotid)
                ->where('BookDate', '=', $bookdate)
                ->where('BookNumber', '=', $queueno)
                ->where('Status', '!=', 3)
                ->where('Active', '=', 1)
                //->orderBy('BookNumber', 'desc')
                ->first();
        return $findQueue; 
    }
    public function FindTotalBooking($doctorslotid,$bookdate){           
        $totalCount = DB::table('user_appoinment')
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Status', '!=', 3)
                ->where('Active', '=', 1)
                ->count();
        return $totalCount; 
    }
    
    public function FindNowSaving($doctorslotid,$bookdate){           
        $statusQueue = DB::table('user_appoinment')
                ->where('BookType', '=', 0)
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Status', '=', 1)
                ->where('Active', '=', 1)
                //->orderBy('BookNumber', 'DESC')
                ->first();
        return $statusQueue; 
    }
    //public function FindAnyAppointment($doctorslot,$userid){ 
    public function FindAnyAppointment($userid){    
        $statusAppointment = DB::table('user_appoinment')
                //->where('DoctorSlotID', '=', $doctorslot)
                ->where('UserID', '=', $userid)
                //->where('BookDate', '=', $bookdate)
                ->whereIn('Status', array(0,1))
                ->where('Active', '=', 1)
                ->get();
        return $statusAppointment; 
    }
    public function FindRealAppointment($userid){           
        $statusAppointment = DB::table('user_appoinment')
                //->where('DoctorSlotID', '=', $doctorslot)
                ->where('UserID', '=', $userid)
                //->where('BookDate', '=', $bookdate)
                ->whereIn('Status', array(0,1,2))
                ->where('Active', '=', 1)
                ->get();
        return $statusAppointment; 
    }
    public function FindProcedureAppointments($doctorid,$procedureid,$bookdate,$starttime,$endtime){           
        $getBooking = DB::table('user_appoinment')
            ->select('user_appoinment.UserAppoinmentID','user_appoinment.UserID','user_appoinment.ClinicTimeID','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.EndTime','user_appoinment.Remarks','user_appoinment.BookDate','user_appoinment.Status',
                    'user.Name as UsrName','user.NRIC','user.PhoneNo','user.Email')   
            ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')      
                
            ->where('user_appoinment.DoctorID', '=', $doctorid)
            ->where('user_appoinment.ProcedureID', '=', $procedureid)
            ->where('user_appoinment.BookDate', '=', $bookdate)
            ->where('user_appoinment.StartTime', '=', $starttime)
            ->where('user_appoinment.EndTime', '=', $endtime)    
            //->where('EndTime', '>=', $endtime)  
            //->join('clinic_procedure', 'user_appoinment.ProcedureID', '=', 'clinic_procedure.ProcedureID')     
            
                
            ->where('user_appoinment.Active', '=', 1)
            ->where('user.Active', '=', 1)    
            ->first();

        return $getBooking; 
    }
    public function NumberOfBookings($doctorid,$bookdate){           
        $countQueue = DB::table('user_appoinment')
                ->where('DoctorID', '=', $doctorid)
                ->where('BookDate', '=', $bookdate)
                ->where('Active', '=', 1)
                ->count();
        return $countQueue; 
    }
    public function FindTimelyAppointments($doctorid,$bookdate,$starttime,$endtime){           
        $getBooking = DB::table('user_appoinment')
            ->select('user_appoinment.UserAppoinmentID','user_appoinment.UserID','user_appoinment.ClinicTimeID','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.EndTime','user_appoinment.Remarks','user_appoinment.BookDate','user_appoinment.Status',
                    'user.Name as UsrName','user.NRIC','user.PhoneNo','user.Email')   
            ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')      
                
            ->where('user_appoinment.DoctorID', '=', $doctorid)
            
            //->where('user_appoinment.StartTime', '=', $starttime)
            //->where('user_appoinment.EndTime', '=', $endtime)
                
              
                   
            //->where('user_appoinment.StartTime', '<=', $starttime && 'user_appoinment.EndTime', '>=', $starttime)
            //->orWhere('user_appoinment.StartTime', '<=', $endtime && 'user_appoinment.EndTime', '>=', $endtime)    
            //->whereBetween('user_appoinment.StartTime', [$starttime, $endtime]) 
             //->whereBetween('user_appoinment.StartTime', array($starttime, $endtime))     
             //->orwhereBetween('user_appoinment.EndTime', array($starttime, $endtime))    
              
            //->where('user_appoinment.StartTime', '<=', $starttime, '<', 'user_appoinment.EndTime')    
            //->orWhere('user_appoinment.StartTime', '<', $endtime && 'user_appoinment.EndTime', '>=',$endtime )    
            ->where('user_appoinment.BookDate', '=', $bookdate) 
            ->whereBetween('user_appoinment.StartTime', [$starttime, $endtime])
            ->orwhereBetween('user_appoinment.EndTime', [$starttime, $endtime])  
                 
           //->where(function ($query) {
           //     $query->where('user_appoinment.StartTime', '<=', $starttime)
           //           ->where('user_appoinment.EndTime', '>', $starttime);
           //  }) 
            //->where(function ($query) {
            //    $query->where('user_appoinment.StartTime', '<=', $starttime && 'user_appoinment.EndTime', '>', $starttime)
            //          ->orWhere('user_appoinment.StartTime', '<', $endtime && 'user_appoinment.EndTime', '>=', $endtime);
            //})
               
            //->where('user_appoinment.StartTimes', '<=', $starttime && 'user_appoinment.EndTime', '>', $starttime)    
            //->orWhere('user_appoinment.StartTime', '<', $endtime && 'user_appoinment.EndTime', '>=', $endtime)     
                  
            ->where('user_appoinment.Active', '=', 1)
            ->where('user.Active', '=', 1)   
            ->first();

        return $getBooking; 
    }
    public function FindTodayAppointments($doctorid,$bookdate){           
        $getBooking = DB::table('user_appoinment')
            ->select('user_appoinment.UserAppoinmentID','user_appoinment.UserID','user_appoinment.ClinicTimeID','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.EndTime','user_appoinment.Remarks','user_appoinment.BookDate','user_appoinment.Status',
                    'user.Name as UsrName','user.NRIC','user.PhoneNo','user.Email')   
            ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')      
                
            ->where('user_appoinment.DoctorID', '=', $doctorid)
            ->where('user_appoinment.BookDate', '=', $bookdate)
                
           ->where('user_appoinment.Active', '=', 1)
           ->where('user.Active', '=', 1)   
           ->get(); 
        return $getBooking; 
    }
    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
    //                              WEB                                   //
    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
    
    
    public function updateAppointmentFromEdit($dataArray, $appointid) {
        return DB::table('user_appoinment')
        ->where('UserAppoinmentID', '=', $appointid)
        ->update($dataArray);
    }

    public function UpdateAppointment ($dataArray,$appointid){         
        return DB::table('user_appoinment')
            ->where('UserAppoinmentID', '=', $appointid)
            ->update($dataArray);
        
        // return $allData;
    }
    
    //Use           :   To get Queue history 
    //parameter :  
    //Out put : array 
    public function findQueueBooking($slotid,$bookdate){           
        $findQueue = DB::table('user_appoinment')
                ->where('BookType', '=', 0)
                ->where('DoctorSlotID', '=', $slotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Active', '=', 1)
                //->orderBy('BookNumber', 'desc')
                ->orderBy('BookNumber', 'ASC')
                ->get();
        return $findQueue; 
    }
    public function findSlotBooking($slotdetailid,$currentdate){           
        $findSlot = DB::table('user_appoinment')
                ->where('BookType', '=', 1)
                ->where('SlotDetailID', '=', $slotdetailid)
                ->where('BookDate', '=', $currentdate)
                ->where('Active', '=', 1)
                ->first();
        
        return $findSlot; 
    }
    
    public function getAppointment($appointmentid){
        $findSlot = DB::table('user_appoinment')
                ->where('UserAppoinmentID', '=', $appointmentid)
                ->where('Active', '=', 1)
                ->first();
        
        return $findSlot; 
    }
    
    public function findCancelledQueueBooking($slotid,$bookdate){           
        $countQueue = DB::table('user_appoinment')
                ->where('BookType', '=', 0)
                ->where('DoctorSlotID', '=', $slotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Status', '=', 3)
                ->where('Active', '=', 1)
                ->count();
                //->orderBy('BookNumber', 'desc')
                //->orderBy('BookNumber', 'ASC')
                //->get();
        return $countQueue; 
    }
    
    public function getAppointmentBYSlotDetailID($slotdetailid){
        $findSlot = DB::table('user_appoinment')
                ->where('SlotDetailID', '=', $slotdetailid)
                ->where('BookType', '=', 1)
                ->where('Active', '=', 1)
                ->first();
        
        return $findSlot; 
    }
    public function FindQueueForProcess($doctorslotid,$bookdate,$booknumber){           
        $statusQueue = DB::table('user_appoinment')
                ->where('BookType', '=', 0)
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Status', '=', 0)
                ->where('Active', '=', 1)
                ->where('BookNumber', '>', $booknumber)
                ->orderBy('BookNumber', 'ASC')
                ->first();
        return $statusQueue; 
    }
    public function FindQueueSaving($booktype,$doctorslotid,$bookdate){           
        $statusQueue = DB::table('user_appoinment')
                ->where('BookType', '=', $booktype)
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Status', '=', 0)
                ->where('Active', '=', 1)
                ->orderBy('BookNumber', 'ASC')
                ->first();
        return $statusQueue; 
    }
    public function FindQueuePeople($doctorslotid,$bookdate,$limit){           
        $statusQueue = DB::table('user_appoinment')
                ->where('BookType', '=', 0)
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Status', '=', 0)
                ->where('Active', '=', 1)
                ->take($limit)
                ->orderBy('BookNumber', 'DESC')
                ->first();
        return $statusQueue; 
    }
    public function FindQueuePeopleAhead($doctorslotid,$bookdate,$limit,$booknumber){           
        $statusQueue = DB::table('user_appoinment')
                ->where('BookType', '=', 0)
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Status', '=', 0)
                ->where('Active', '=', 1)     
                ->where('BookNumber', '>', $booknumber)
                ->take($limit)
                //->orderBy('BookNumber', 'DESC')
                ->orderBy('BookNumber', 'ASC')
                //->first();
                ->get();
        return $statusQueue; 
    }
    public function FindQueueReminder($doctorslotid,$bookdate){           
        $statusQueue = DB::table('user_appoinment')
                ->where('BookType', '=', 0)
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Status', '=', 1)
                ->where('Active', '=', 1)
                ->orderBy('BookNumber', 'DESC')
                ->first();
        return $statusQueue; 
    }
    
    public function FindRemindQueuesForPush($doctorslotid,$bookdate,$booknumber){           
        $statusQueue = DB::table('user_appoinment')
                ->join('device_token', 'user_appoinment.UserID', '=', 'device_token.UserID')
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorSlotID','user_appoinment.SlotDetailID','user_appoinment.BookNumber','user_appoinment.BookDate','device_token.Token','device_token.Device_Type')
                ->where('user_appoinment.BookType', '=', 0)
                ->where('user_appoinment.DoctorSlotID', '=', $doctorslotid)
                ->where('user_appoinment.BookDate', '=', $bookdate)
                //->where('user_appoinment.Status', '=', 0)
                //->orWhere('user_appoinment.Status', 1)
                ->whereIn('user_appoinment.Status', array(0,1))
                ->where('user_appoinment.Active', '=', 1)
                ->where('user_appoinment.BookNumber', '<', $booknumber)
                ->where('device_token.Active', '=', 1)
                ->orderBy('user_appoinment.BookNumber', 'DESC')
                
                /*->where('BookType', '=', 0)
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('BookDate', '=', $bookdate)                
                ->where('Status', '=', 1)
                ->orWhere('Status', 0)
                ->where('Active', '=', 1)
                ->where('BookNumber', '<', $booknumber)
                ->orderBy('BookNumber', 'DESC')
                 */
                ->get();
        return $statusQueue; 
    }
    public function FindSlosPeople($doctorslotid,$bookdate,$limit){           
        $statusQueue = DB::table('user_appoinment')
                ->join('doctor_slot_details', 'user_appoinment.SlotDetailID', '=', 'doctor_slot_details.SlotDetailID')
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorSlotID','user_appoinment.SlotDetailID','user_appoinment.MediaType','user_appoinment.BookNumber','user_appoinment.BookDate','user_appoinment.Status','user_appoinment.Active','doctor_slot_details.Time')
                ->where('user_appoinment.BookType', '=', 1)
                ->where('user_appoinment.DoctorSlotID', '=', $doctorslotid)
                ->where('user_appoinment.BookDate', '=', $bookdate)
                ->where('user_appoinment.Status', '=', 0)
                ->where('user_appoinment.Active', '=', 1)
                
                ->where('doctor_slot_details.Available', '=', 2)
                ->where('doctor_slot_details.Active', '=', 1)
                ->take($limit)
                ->orderBy('doctor_slot_details.Time', 'DESC')
                ->first();
        return $statusQueue; 
    }
    public function FindSlosPeopleAhead($doctorslotid,$bookdate,$slottime,$limit){           
        $slotAhead = DB::table('user_appoinment')
                ->join('doctor_slot_details', 'user_appoinment.SlotDetailID', '=', 'doctor_slot_details.SlotDetailID')
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorSlotID','user_appoinment.SlotDetailID','user_appoinment.MediaType','user_appoinment.BookNumber','user_appoinment.BookDate','user_appoinment.Status','user_appoinment.Active','doctor_slot_details.Time')
                ->where('user_appoinment.BookType', '=', 1)
                ->where('user_appoinment.DoctorSlotID', '=', $doctorslotid)
                ->where('user_appoinment.BookDate', '=', $bookdate)
                ->where('user_appoinment.Status', '=', 0)
                //->whereIn('user_appoinment.Status', '=', 0)
                ->where('user_appoinment.Active', '=', 1)
                ->where('doctor_slot_details.Time', '>', $slottime)
                ->where('doctor_slot_details.Available', '=', 2)
                ->where('doctor_slot_details.Active', '=', 1)
                ->take($limit)
                ->orderBy('doctor_slot_details.Time', 'ASC')
                ->get();
        return $slotAhead; 
    }
    public function FindSlosReminderList($doctorslotid,$bookdate,$slottime){           
        $statusQueue = DB::table('user_appoinment')
                ->join('doctor_slot_details', 'user_appoinment.SlotDetailID', '=', 'doctor_slot_details.SlotDetailID')
                ->join('device_token', 'user_appoinment.UserID', '=', 'device_token.UserID')
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorSlotID','user_appoinment.SlotDetailID','user_appoinment.MediaType','user_appoinment.BookNumber','user_appoinment.BookDate','user_appoinment.Status','user_appoinment.Active','doctor_slot_details.Time','device_token.Token','device_token.Device_Type')
                ->where('user_appoinment.BookType', '=', 1)
                ->where('user_appoinment.DoctorSlotID', '=', $doctorslotid)
                ->where('user_appoinment.BookDate', '=', $bookdate)
                
                ->whereIn('user_appoinment.Status', array(0,1))
                ->where('user_appoinment.Active', '=', 1)
                ->where('device_token.Active', '=', 1)
                ->where('doctor_slot_details.Available', '=', 2)
                ->where('doctor_slot_details.Active', '=', 1)
                
                
                //>orderBy('user_appoinment.BookNumber', 'DESC')
                //->where('user_appoinment.BookNumber', '<', $booknumber)
                
                
                //->where('user_appoinment.Status', '=', 1)
                //->where('user_appoinment.Active', '=', 1)
                
                
                ->where('doctor_slot_details.Time', '<', $slottime)
                ->orderBy('doctor_slot_details.Time', 'DESC')
                ->get();
        return $statusQueue; 
    }
    public function FindSlosReminder($doctorslotid,$bookdate){           
        $statusQueue = DB::table('user_appoinment')
                ->join('doctor_slot_details', 'user_appoinment.SlotDetailID', '=', 'doctor_slot_details.SlotDetailID')
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorSlotID','user_appoinment.SlotDetailID','user_appoinment.MediaType','user_appoinment.BookNumber','user_appoinment.BookDate','user_appoinment.Status','user_appoinment.Active','doctor_slot_details.Time')
                ->where('user_appoinment.BookType', '=', 1)
                ->where('user_appoinment.DoctorSlotID', '=', $doctorslotid)
                ->where('user_appoinment.BookDate', '=', $bookdate)
                ->where('user_appoinment.Status', '=', 1)
                ->where('user_appoinment.Active', '=', 1)
                
                ->where('doctor_slot_details.Available', '=', 2)
                ->where('doctor_slot_details.Active', '=', 1)
                ->orderBy('doctor_slot_details.Time', 'DESC')
                ->first();
        return $statusQueue; 
    }
    public function FindSlotSaving($booktype,$doctorslotid,$bookdate){           
        $statusQueue = DB::table('user_appoinment')
                ->join('doctor_slot_details', 'user_appoinment.SlotDetailID', '=', 'doctor_slot_details.SlotDetailID')
                ->where('user_appoinment.BookType', '=', $booktype)
                ->where('user_appoinment.DoctorSlotID', '=', $doctorslotid)
                ->where('user_appoinment.BookDate', '=', $bookdate)
                ->where('user_appoinment.Status', '=', 0)
                ->where('user_appoinment.Active', '=', 1)
                
                ->where('doctor_slot_details.Available', '=', 2)
                ->where('doctor_slot_details.Active', '=', 1)
                ->orderBy('doctor_slot_details.Time', 'ASC')
                ->first();
        return $statusQueue; 
    }
    public function FindNextSlotSaving($doctorslotid,$bookdate,$slottime){           
        $statusQueue = DB::table('user_appoinment')
                ->join('doctor_slot_details', 'user_appoinment.SlotDetailID', '=', 'doctor_slot_details.SlotDetailID')
                ->where('user_appoinment.BookType', '=', 1)
                ->where('user_appoinment.DoctorSlotID', '=', $doctorslotid)
                ->where('user_appoinment.BookDate', '=', $bookdate)
                ->where('user_appoinment.Status', '=', 0)
                ->where('user_appoinment.Active', '=', 1)
                ->where('doctor_slot_details.Time', '>', $slottime)
                
                ->where('doctor_slot_details.Available', '=', 2)
                ->where('doctor_slot_details.Active', '=', 1)
                ->orderBy('doctor_slot_details.Time', 'ASC')
                ->first();
        return $statusQueue; 
    }
    /*
     * 
     * 
     */
    public function GetTodayAppointment($bookdate){           
        $findAppointment = DB::table('user_appoinment')
                ->join('device_token', 'user_appoinment.UserID', '=', 'device_token.UserID')
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorSlotID','user_appoinment.SlotDetailID','user_appoinment.BookNumber','user_appoinment.BookDate','device_token.Token','device_token.Device_Type')
                ->where('user_appoinment.BookDate', '=', $bookdate)
                ->where('user_appoinment.Status', '=', 0)
                ->where('user_appoinment.Active', '=', 1)
                ->where('device_token.Active', '=', 1)
                ->get();
        return $findAppointment; 
    }
    
    public function GetTodayAppointmentInHours($bookdate){           
        $findAppointment = DB::table('user_appoinment')
                ->join('device_token', 'user_appoinment.UserID', '=', 'device_token.UserID')
                ->join('doctor_slot_details', 'user_appoinment.SlotDetailID', '=', 'doctor_slot_details.SlotDetailID')
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorSlotID','user_appoinment.SlotDetailID','user_appoinment.BookNumber','user_appoinment.BookDate','device_token.Token','device_token.Device_Type','doctor_slot_details.Time','doctor_slot_details.SlotDetailID')
                ->where('user_appoinment.BookDate', '=', $bookdate)
                ->where('user_appoinment.Status', '=', 0)
                ->where('user_appoinment.Active', '=', 1)
                ->where('device_token.Active', '=', 1)
                ->where('doctor_slot_details.Available', '=', 2)
                ->where('doctor_slot_details.Active', '=', 1)
                ->get();
        return $findAppointment; 
    }
    
    public function TotalQueueBooking($doctorslotid,$bookdate){           
        $countQueue = DB::table('user_appoinment')
                ->where('BookType', '=', 0)
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Active', '=', 1)
                ->count();

        return $countQueue; 
    }
    public function TotalSlotBooking($doctorslotid,$bookdate){           
        $countQueue = DB::table('user_appoinment')
                ->where('BookType', '=', 1)
                ->where('DoctorSlotID', '=', $doctorslotid)
                ->where('BookDate', '=', $bookdate)
                ->where('Active', '=', 1)
                ->count();

        return $countQueue; 
    }
    
    public function FindActiveAppointments(){           
        $statusAppointment = DB::table('user_appoinment')
                //->where('DoctorSlotID', '=', $doctorslot)
                //->where('UserID', '=', $userid)
                //->where('BookDate', '=', $bookdate)
                ->whereIn('Status', array(0,1))
                ->where('Active', '=', 1)
                ->get();
        return $statusAppointment; 
    }
    public function FindAvailableAppointments($bookdate){           
        $statusAppointment = DB::table('user_appoinment')
                //->where('DoctorSlotID', '=', $doctorslot)
                //->where('UserID', '=', $userid)
                //->where('BookDate', '=', $bookdate)
                ->whereIn('Status', array(0,1))
                ->where('BookDate', '<=', $bookdate)
                ->where('Active', '=', 1)
                ->get();
        return $statusAppointment; 
    }
    
    public function TestArrayMerge(){           
        $firstAppointment = DB::table('user_appoinment')
                ->where('UserAppoinmentID', '=', 802)
                ->where('Active', '=', 1)
                ->get();
        $secondAppointment = DB::table('user_appoinment')
                ->where('UserAppoinmentID', '=', 803)
                ->where('Active', '=', 1)
                ->get();
        $totalArray = array_merge($firstAppointment,$secondAppointment);
        return $totalArray; 
    }
   
    public function FindClinicAppointment(){           
        $statusQueue = DB::table('user_appoinment')               
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorSlotID','user_appoinment.SlotDetailID','user_appoinment.BookNumber','user_appoinment.BookDate',
                        'clinic.ClinicID','clinic.Name','clinic.Address',DB::raw('count(*) as total'))
                ->join('doctor_slots', 'user_appoinment.DoctorSlotID', '=', 'doctor_slots.DoctorSlotID')
                ->join('clinic', 'doctor_slots.ClinicID', '=', 'clinic.ClinicID')
                //->where('user_appoinment.Active', '=', 1)
                ->orderBy('total', 'DESC')
                //->where('clinic.ClinicID', '=', 32)
               ->groupBy('doctor_slots.ClinicID')
                ->get();
        return $statusQueue; 
    }
    public function FindClinicAppointmentChanges($clinicid,$status){           
        $statusQueue = DB::table('user_appoinment')               
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorSlotID','user_appoinment.SlotDetailID','user_appoinment.BookNumber','user_appoinment.BookDate',
                        'clinic.ClinicID','clinic.Name','clinic.Address')
                ->join('doctor_slots', 'user_appoinment.DoctorSlotID', '=', 'doctor_slots.DoctorSlotID')
                ->join('clinic', 'doctor_slots.ClinicID', '=', 'clinic.ClinicID')
                //->where('user_appoinment.Active', '=', 1)
                //->orderBy('total', 'DESC')
                ->where('clinic.ClinicID', '=', $clinicid)
                ->where('user_appoinment.Status', '=', $status)
                //->groupBy('doctor_slots.ClinicID')
                ->count();
        return $statusQueue; 
    }
    
    public function FindExistingAppointment($doctorid,$bookdate){           
        $getBooking = DB::table('user_appoinment')
            //->where('BookType', '=', 1)
            ->where('DoctorID', '=', $doctorid)
            ->where('BookDate', '=', $bookdate)
            //->where('StartTime', '<=', $starttime)
            //->where('EndTime', '>=', $endtime)    
            //->whereBetween('StartTime', array($starttime, $endtime))
            //->orwhereBetween('EndTime', array($starttime, $endtime))  
                
                
            //->where('EndTime', '>',$starttime  && 'StartTime', '<=',$starttime)
                
            ->where('Active', '=', 1)
                
            ->get();

        return $getBooking; 
    }
    public function FindAllExistingAppointments($doctorid,$bookdate){           
        $getBooking = DB::table('user_appoinment')
            ->select('user_appoinment.UserAppoinmentID','user_appoinment.UserID','user_appoinment.ClinicTimeID','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.EndTime','user_appoinment.Remarks','user_appoinment.BookDate','user_appoinment.Status','user_appoinment.event_type','user_appoinment.MediaType',
                    'clinic_procedure.ProcedureID','clinic_procedure.Name as ProName','user_appoinment.Duration','user_appoinment.Price',
                    'user.Name as UsrName','user.NRIC','user.PhoneNo','user.Email', 'user.UserType')   
            //->where('BookType', '=', 1)
            ->where('user_appoinment.DoctorID', '=', $doctorid)
            ->where('user_appoinment.BookDate', '=', $bookdate)
            //->where('StartTime', '<=', $starttime)
            //->where('EndTime', '>=', $endtime)  
            ->join('clinic_procedure', 'user_appoinment.ProcedureID', '=', 'clinic_procedure.ProcedureID')     
            ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')      
                
            ->where('user_appoinment.Active', '=', 1)
            ->where('clinic_procedure.Active', '=', 1)    
            ->get();

        return $getBooking; 
    }
    
    public function FindUserAppointment($appointmentid){           
        $findAppointment = DB::table('user_appoinment')               

                ->select('user_appoinment.UserAppoinmentID','user_appoinment.ClinicTimeID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.ProcedureID','user_appoinment.BookDate','user_appoinment.EndTime','user_appoinment.Remarks','user_appoinment.Status','user_appoinment.Gc_event_id',
                        'user.Name','user.Email','user.NRIC','user.PhoneNo','user.PhoneCode')

                ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')
                //->join('clinic', 'doctor_slots.ClinicID', '=', 'clinic.ClinicID')
                //->where('user_appoinment.Active', '=', 1)
                //->orderBy('total', 'DESC')
                ->where('user_appoinment.UserAppoinmentID', '=', $appointmentid)
                ->where('user_appoinment.Active', '=', 1)
                //->groupBy('doctor_slots.ClinicID')
                ->first();
        return $findAppointment; 
    }
    
    public function FindTimelyAppointment($doctorid,$procedureid,$bookdate,$startime){           
        $getBooking = DB::table('user_appoinment')
            //->where('BookType', '=', 1)
            ->where('DoctorID', '=', $doctorid)
            ->where('ProcedureID', '=', $procedureid)
            ->where('BookDate', '=', $bookdate)
            ->where('StartTime', '=', $startime)
            ->where('Active', '=', 1)
                
            ->first();

        return $getBooking; 
    }
    public function AppointmentByDate($bookingdate){           
        $findAppointment = DB::table('user_appoinment')               
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.ClinicTimeID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.ProcedureID','user_appoinment.BookDate','user_appoinment.EndTime','user_appoinment.Remarks','user_appoinment.Status',
                        'user.Name','user.Email','user.NRIC','user.PhoneNo', 'user.PhoneCode')
                ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')
                //->join('clinic', 'doctor_slots.ClinicID', '=', 'clinic.ClinicID')

                ->where('user_appoinment.BookDate', '=', $bookingdate)
                ->where('user_appoinment.Active', '=', 1)
                ->where('user.Active', '=', 1)
                ->get();
        return $findAppointment; 
    }
    public function AppointmentByHour($bookingdate,$starttime){           
        $findAppointment = DB::table('user_appoinment')               
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.ClinicTimeID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.ProcedureID','user_appoinment.BookDate','user_appoinment.EndTime','user_appoinment.Remarks','user_appoinment.Status',
                        'user.Name','user.Email','user.NRIC','user.PhoneNo', 'user.PhoneCode')
                ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')
                //->join('clinic', 'doctor_slots.ClinicID', '=', 'clinic.ClinicID')

                ->where('user_appoinment.BookDate', '=', $bookingdate)
                ->where('user_appoinment.StartTime', '=', $starttime)
                ->where('user_appoinment.Active', '=', 1)
                ->where('user.Active', '=', 1)
                ->get();
        return $findAppointment; 
    }
    
    public function FindTimeBooking($doctorid,$clinictimeid){           
        $getBooking = DB::table('user_appoinment')
            //->where('BookType', '=', 1)
            ->where('DoctorID', '=', $doctorid)
            ->where('ClinicTimeID', '=', $clinictimeid)
            ->where('Active', '=', 1)
                
            ->get();

        return $getBooking; 
    }     
    public function FindProcedureBooking($procedureid){           
        $getBooking = DB::table('user_appoinment')
            //->where('BookType', '=', 1)
            ->where('ProcedureID', '=', $procedureid)
            ->where('Active', '=', 1)
                
            ->get();

        return $getBooking; 
    }
    public function FindDoctorBooking($doctorid){           
        $getBooking = DB::table('user_appoinment')
            //->where('BookType', '=', 1)
            ->where('DoctorID', '=', $doctorid)
            ->where('Active', '=', 1)
                
            ->get();

        return $getBooking; 
    }
    public function FindChannelBooking($clinicid,$currentdate,$currentTime){           
        $getBooking = DB::table('user_appoinment')
            ->select('user_appoinment.UserAppoinmentID','user_appoinment.ClinicTimeID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.ProcedureID','user_appoinment.BookDate','user_appoinment.EndTime','user_appoinment.Remarks','user_appoinment.Status',
                    'clinic_procedure.Name','clinic_procedure.Duration','clinic_procedure.Price',
                    'clinic.ClinicID','clinic.Name as CLName')
            ->join('clinic_procedure', 'user_appoinment.ProcedureID', '=', 'clinic_procedure.ProcedureID')  
            ->join('clinic', 'clinic_procedure.ClinicID', '=', 'clinic.ClinicID')     
            ->where('clinic.ClinicID', '=', $clinicid)     
            ->where('user_appoinment.BookDate', '>=', $currentdate)  
            ->where('user_appoinment.Created_on', '>=', $currentTime)
            ->where('user_appoinment.Status', '=', 0)    
            ->where('user_appoinment.Active', '=', 1)
            ->where('clinic_procedure.Active', '=', 1)
            ->where('clinic.Active', '=', 1)    
            ->get();

        return $getBooking; 
    }
    public function FindChannelBookingDoctor($clinicid,$doctorid,$currentdate,$currentTime){           
        $getBooking = DB::table('user_appoinment')
            ->select('user_appoinment.UserAppoinmentID','user_appoinment.ClinicTimeID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.ProcedureID','user_appoinment.BookDate','user_appoinment.EndTime','user_appoinment.Remarks','user_appoinment.Status',
                    'clinic_procedure.Name','clinic_procedure.Duration','clinic_procedure.Price',
                    'clinic.ClinicID','clinic.Name as CLName')
            ->join('clinic_procedure', 'user_appoinment.ProcedureID', '=', 'clinic_procedure.ProcedureID')  
            ->join('clinic', 'clinic_procedure.ClinicID', '=', 'clinic.ClinicID')     
            ->where('clinic.ClinicID', '=', $clinicid)     
            ->where('user_appoinment.BookDate', '>=', $currentdate)  
            ->where('user_appoinment.Created_on', '>=', $currentTime)
            ->where('user_appoinment.DoctorID', '>=', $doctorid)    
            ->where('user_appoinment.Status', '=', 0)    
            ->where('user_appoinment.Active', '=', 1)
            ->where('clinic_procedure.Active', '=', 1)
            ->where('clinic.Active', '=', 1)    
            ->get();

        return $getBooking; 
    }



    //nhr 2016-7-13
    public function getClinicAppointments($clinicID)
    {
       $data = DB::table('user_appoinment')
                ->join('doctor_availability','doctor_availability.DoctorID','=','user_appoinment.DoctorID')
                ->where('doctor_availability.ClinicID','=',$clinicID)
                ->get();

        return $data;
    }

    function countAppointments($id, $start, $end)
    {
        if(strlen($start) > 0 && strlen($end) > 0) {
            return DB::table('user_appoinment')
                ->join('doctor_availability','doctor_availability.DoctorID','=','user_appoinment.DoctorID')
                ->where('user_appoinment.Status', '=', 2)
                ->where('doctor_availability.ClinicID','=',$id)
                ->where('user_appoinment.BookDate', '>=', strtotime($start))
                ->where('user_appoinment.BookDate', '<=', strtotime($end))   
                ->count();
        } else {
            return DB::table('user_appoinment')
                    ->join('doctor_availability','doctor_availability.DoctorID','=','user_appoinment.DoctorID')
                    ->where('user_appoinment.Status', '=', 2)
                    ->where('doctor_availability.ClinicID','=',$id)->count();
        }
    }

    function listAppointments($id)
    {   
        return DB::table('user_appoinment')
                ->join('doctor_availability','doctor_availability.DoctorID','=','user_appoinment.DoctorID')
                ->join('user', 'user.UserID', '=', 'user_appoinment.UserID')
                ->join('doctor', 'doctor.DoctorID', '=', 'user_appoinment.DoctorID')
                ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'user_appoinment.ProcedureID')
                ->where('doctor_availability.ClinicID','=', $id)
                ->select('user_appoinment.BookDate', 'user_appoinment.StartTime', 'user_appoinment.EndTime', 'user.Name as client_name', 'doctor.Name as doctor_name', 'clinic_procedure.Name as procedure_name', 'clinic_procedure.Price', 'user_appoinment.UserAppoinmentID', 'user_appoinment.Status')
                ->orderBy('user_appoinment.BookDate', 'desc')
                ->orderBy('user_appoinment.StartTime', 'desc')
                ->get();
    }

    function viewAppointment($id)
    {
        return DB::table('user_appoinment')
                ->join('doctor_availability','doctor_availability.DoctorID','=','user_appoinment.DoctorID')
                ->join('user', 'user.UserID', '=', 'user_appoinment.UserID')
                ->join('doctor', 'doctor.DoctorID', '=', 'user_appoinment.DoctorID')
                ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'user_appoinment.ProcedureID')
                ->where('user_appoinment.UserAppoinmentID','=', $id)
                ->select(
                    'user_appoinment.BookDate', 
                    'user_appoinment.StartTime', 
                    'user_appoinment.EndTime', 
                    'user.Name as client_name', 
                    'doctor.Name as doctor_name', 
                    'clinic_procedure.Name as procedure_name', 
                    'clinic_procedure.Price', 
                    'user_appoinment.UserAppoinmentID', 
                    'user_appoinment.Status', 
                    'user_appoinment.MediaType',
                    'doctor.DoctorID',
                    'clinic_procedure.ProcedureID',
                    'clinic_procedure.Duration',
                    'clinic_procedure.Price',
                    'user.City',
                    'user.Zip_Code',
                    'user.State',
                    'user.Address',
                    'user_appoinment.StartTime',
                    'user_appoinment.EndTime',
                    'user.PhoneCode',
                    'user_appoinment.Remarks',
                    'user.NRIC',
                    'clinic_procedure.ClinicID',
                    'user.PhoneNo',
                    'user.Email as client_email',
                    'user.UserID')
                ->get();
    }

    function viewAppointmentByDate($start, $end, $id)
    {
         return DB::table('user_appoinment')
                ->join('doctor_availability','doctor_availability.DoctorID','=','user_appoinment.DoctorID')
                ->join('user', 'user.UserID', '=', 'user_appoinment.UserID')
                ->join('doctor', 'doctor.DoctorID', '=', 'user_appoinment.DoctorID')
                ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'user_appoinment.ProcedureID')
                ->where('doctor_availability.ClinicID','=', $id)
                ->where('user_appoinment.StartTime', '>=', strtotime($start))
                ->where('user_appoinment.EndTime', '<=', strtotime($end))
                ->select('user_appoinment.BookDate', 'user_appoinment.StartTime', 'user_appoinment.EndTime', 'user.Name as client_name', 'doctor.Name as doctor_name', 'clinic_procedure.Name as procedure_name', 'clinic_procedure.Price', 'user_appoinment.UserAppoinmentID', 'user_appoinment.Status')
                ->get();
        // return strtotime($start).strtotime($end);
    }

    function findAppointmentData($id)
    {
        return UserAppoinment::where('UserAppoinmentID', '=', $id)->first();
    }

    public function getExistingAppointments($id, $start, $end)
    {
        return DB::table('user_appoinment')
                ->join('transaction_history', 'transaction_history.AppointmenID', '=', 'user_appoinment.UserAppoinmentID')
                ->join('doctor', 'doctor.DoctorID', '=', 'user_appoinment.DoctorID')
                ->where('user_appoinment.BookDate', '>=', $start)
                ->where('user_appoinment.BookDate', '<=', $end)
                ->where('transaction_history.ClinicID', $id)
                // ->where('user_appoinment.Status', 1)
                ->where('user_appoinment.Status', 0)
                ->select('user_appoinment.UserAppoinmentID', 'user_appoinment.BookDate', 'user_appoinment.Status', 'user_appoinment.StartTime', 'user_appoinment.EndTime', 'transaction_history.ClinicID', 'doctor.Name', 'doctor.DoctorID')
                ->get();

    }

    public function getPreviousBookings($date)
    {
        return UserAppoinment::where('BookDate', '>=', $date)->get();
    }

    public function updatePreviousBooking($id)
    {
        return UserAppoinment::where('UserAppoinmentID', $id)->update(['Status' => 2]);
    }

    public function checkUserFirstTimeBook($user_id)
    {
        return UserAppoinment::where('UserID', $user_id)->where('Status', 2)->count();
    }

    public function getUserAppointmentDetails($id)
    {
        $result = DB::table('user_appoinment')
                ->join('user', 'user.UserID', '=', 'user_appoinment.UserID')
                ->join('transaction_history', 'transaction_history.AppointmenID', '=', 'user_appoinment.UserAppoinmentID')
                ->join('clinic', 'clinic.ClinicID', '=', 'transaction_history.ClinicID')
                ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_history.ProcedureID')
                ->where('user_appoinment.UserAppoinmentID', $id)
                ->select('user_appoinment.UserAppoinmentID', 'transaction_history.UserID', 'transaction_history.ClinicID', 'clinic.Name as clinic_name', 'clinic_procedure.Name as procedure_name', 'clinic.Address', 'user.Email', 'clinic.Phone')
                ->first();
        if($result) {
            return $result;
        } else {
            return FALSE;
        }
    }
}
