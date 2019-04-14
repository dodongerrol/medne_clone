<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ManageTimes extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'manage_times';
        
        public function AddManageTimes ($dataArray){
                $this->Party = $dataArray['party'];
                $this->PartyID = $dataArray['partyid'];
                $this->Repeat = $dataArray['timerepeat'];
                $this->Type = $dataArray['timetype'];
                //$this->ClinicID = $dataArray['clinicid'];
                //$this->DoctorID = $dataArray['doctorid'];
                $this->From_Date = $dataArray['from_date'];
                $this->To_Date = $dataArray['to_date'];     
                $this->Status = 1;
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
        
        
        public function FindExistingManageTimes1($party,$partyid){ 
            $allData = DB::table('manage_times')
                ->where('Party', '=', $party)
                ->where('PartyID', '=', $partyid)
                ->where('Status', '=', 1)    
                ->where('Active', '=', 1)    
                //->get();
                ->first();
            return $allData;
        }
        public function FindExistingManageTimes($party,$partyid,$currentdate){ 
            $allData = DB::table('manage_times')
                ->where('Party', '=', $party)
                //->where('PartyID', '=', $partyid)
                    ->where('From_Date', '<=', $currentdate)
                    //->where('To_Date', '=', 0)
                    //->orWhere('To_Date', '>=', $currentdate)
                    
                    //->where('Repeat', '=', 1 || 'To_Date', '>=', $currentdate)
                ->where('PartyID', '=', $partyid)    
                ->where('Status', '=', 1)    
                ->where('Active', '=', 1) 
                //->get();
                ->where(function ($mydata) use ($currentdate) {
                    $mydata->where('Repeat', '=', 1)
                    ->orWhere('To_Date', '>=', $currentdate);   
                })     
                ->first();
            return $allData;
        }
        
        public function FindAllClinicTimes1($party,$partyid){ 
            $allData = DB::table('manage_times')
                ->join('clinic_time', 'manage_times.ManageTimeID', '=', 'clinic_time.ManageTimeID')
                ->select('manage_times.ManageTimeID','manage_times.Party','manage_times.PartyID','manage_times.Type','manage_times.From_Date','manage_times.To_Date','manage_times.Repeat','manage_times.Status',
                        'clinic_time.ClinicTimeID','clinic_time.StartTime','clinic_time.EndTime','clinic_time.Mon','clinic_time.Tue','clinic_time.Wed','clinic_time.Thu','clinic_time.Fri','clinic_time.Sat','clinic_time.Sun') 
                ->where('manage_times.PartyID', '=', $partyid)    
                ->where('manage_times.Party', '=', $party)
                ->where('clinic_time.Active', '=', 1)
                ->where('manage_times.Active', '=', 1)    
                ->get();
            return $allData;
        }
        
        public function FindAllClinicTimes_old($party,$partyid,$currentdate){ 
            $a = 0;
            $allData = DB::table('manage_times')
                ->join('clinic_time', 'manage_times.ManageTimeID', '=', 'clinic_time.ManageTimeID')
                ->select('manage_times.ManageTimeID','manage_times.Party','manage_times.PartyID','manage_times.Type','manage_times.From_Date','manage_times.To_Date','manage_times.Repeat','manage_times.Status',
                        'clinic_time.ClinicTimeID','clinic_time.StartTime','clinic_time.EndTime','clinic_time.Mon','clinic_time.Tue','clinic_time.Wed','clinic_time.Thu','clinic_time.Fri','clinic_time.Sat','clinic_time.Sun') 
                   
                    
                    
                ->where('manage_times.Party', '=', $party)
                  
                    
                    //->where('manage_times.To_Date', '=', 0)
                    ->where('manage_times.Repeat', '=', 1 || 'manage_times.To_Date', '>=', $currentdate)
                ->where('manage_times.PartyID', '=', $partyid)     
                   
                //$allData->where(function ($allData) use ($a, $currentdate) {
                //    $allData->where('manage_times.To_Date', '=', $a)
                //          ->orWhere('manage_times.To_Date', '>=', $currentdate);   
                //});
                    
                ->where('clinic_time.Active', '=', 1)
                ->where('manage_times.Active', '=', 1)
                   
                    ->where('manage_times.From_Date', '<=', $currentdate)
                    //->where('manage_times.To_Date', '=', 0)
                    
                    
                ->get();
            return $allData;
        }
        public function FindAllClinicTimes($party,$partyid,$currentdate){ 
            $a = 0;
            $allData = DB::table('manage_times')
                ->join('clinic_time', 'manage_times.ManageTimeID', '=', 'clinic_time.ManageTimeID')
                ->select('manage_times.ManageTimeID','manage_times.Party','manage_times.PartyID','manage_times.Type','manage_times.From_Date','manage_times.To_Date','manage_times.Repeat','manage_times.Status',
                        'clinic_time.ClinicTimeID','clinic_time.StartTime','clinic_time.EndTime','clinic_time.Mon','clinic_time.Tue','clinic_time.Wed','clinic_time.Thu','clinic_time.Fri','clinic_time.Sat','clinic_time.Sun','clinic_time.Active') 
                   
                   
                ->where('manage_times.Party', '=', $party)
                    //->where('manage_times.To_Date', '=', 0)
                
                ->where('manage_times.PartyID', '=', $partyid)                       
                    
                ->where('clinic_time.Active', '=', 1)
                ->where('manage_times.Active', '=', 1)
                ->where('manage_times.From_Date', '<=', $currentdate)
                // ->where('manage_times.From_Date', '>=', $currentdate)
                //->where('manage_times.Repeat', '=', 1 || 'manage_times.To_Date', '>=', $currentdate)    
       
                ->where(function ($mydata) use ($currentdate) {
                    $mydata->where('manage_times.Repeat', '=', 1)
                    ->orWhere('manage_times.To_Date', '>=', $currentdate);   
                })    
                ->get();
            return $allData;
        }
        // nhr 2016/4/29 get without active check
         public function FindAllClinicTimesNew($party,$partyid,$currentdate){ 
            $a = 0;
            $allData = DB::table('manage_times')
                ->join('clinic_time', 'manage_times.ManageTimeID', '=', 'clinic_time.ManageTimeID')
                ->select('manage_times.ManageTimeID','manage_times.Party','manage_times.PartyID','manage_times.Type','manage_times.From_Date','manage_times.To_Date','manage_times.Repeat','manage_times.Status',
                        'clinic_time.ClinicTimeID','clinic_time.StartTime','clinic_time.EndTime','clinic_time.Mon','clinic_time.Tue','clinic_time.Wed','clinic_time.Thu','clinic_time.Fri','clinic_time.Sat','clinic_time.Sun','clinic_time.Active') 
                   
                   
                ->where('manage_times.Party', '=', $party)
                    //->where('manage_times.To_Date', '=', 0)
                
                ->where('manage_times.PartyID', '=', $partyid)                       
                    
                // ->where('clinic_time.Active', '=', 1)
                ->where('manage_times.Active', '=', 1)
                ->where('manage_times.From_Date', '<=', $currentdate)
                //->where('manage_times.Repeat', '=', 1 || 'manage_times.To_Date', '>=', $currentdate)    
       
                ->where(function ($mydata) use ($currentdate) {
                    $mydata->where('manage_times.Repeat', '=', 1)
                    ->orWhere('manage_times.To_Date', '>=', $currentdate);   
                })    
                ->get();
            return $allData;
        }

        /* Use      :   Used to update manage times
         * 
         */
        public function UpdateManageTimes ($dataArray){ 
            $allData = DB::table('manage_times')
                ->where('ManageTimeID', '=', $dataArray['managetimeid'])
                ->update($dataArray);
            
            return $allData;
        }
        public function FindDayAvailableTime1($party,$partyid,$findweek){ 
            $allData = DB::table('manage_times')
                ->join('clinic_time', 'manage_times.ManageTimeID', '=', 'clinic_time.ManageTimeID')
                ->select('manage_times.ManageTimeID','manage_times.Party','manage_times.PartyID','manage_times.Type','manage_times.From_Date','manage_times.To_Date','manage_times.Repeat','manage_times.Status',
                        'clinic_time.ClinicTimeID','clinic_time.StartTime','clinic_time.EndTime','clinic_time.Mon','clinic_time.Tue','clinic_time.Wed','clinic_time.Thu','clinic_time.Fri','clinic_time.Sat','clinic_time.Sun') 
                ->where('manage_times.PartyID', '=', $partyid)    
                ->where('manage_times.Party', '=', $party)
                ->where('clinic_time.'.$findweek, '=', 1)    
                ->where('clinic_time.Active', '=', 1)
                ->where('manage_times.Active', '=', 1)    
                ->first();
            return $allData;
        }
        public function FindDayAvailableTime($party,$partyid,$findweek,$currentdate){ 
            $allData = DB::table('manage_times')
                ->join('clinic_time', 'manage_times.ManageTimeID', '=', 'clinic_time.ManageTimeID')
                ->select('manage_times.ManageTimeID','manage_times.Party','manage_times.PartyID','manage_times.Type','manage_times.From_Date','manage_times.To_Date','manage_times.Repeat','manage_times.Status',
                        'clinic_time.ClinicTimeID','clinic_time.StartTime','clinic_time.EndTime','clinic_time.Mon','clinic_time.Tue','clinic_time.Wed','clinic_time.Thu','clinic_time.Fri','clinic_time.Sat','clinic_time.Sun') 
                ->where('manage_times.PartyID', '=', $partyid)    
                ->where('manage_times.Party', '=', $party)
                ->where('clinic_time.'.$findweek, '=', 1)    
                ->where('clinic_time.Active', '=', 1)
                ->where('manage_times.Active', '=', 1) 
                    ->where('manage_times.From_Date', '<=', $currentdate)
                    ->where('manage_times.Repeat', '=', 1 || 'manage_times.To_Date', '>=', $currentdate)
                ->first();
            return $allData;
        }
        
        public function FindCurrentDayAvailableTimes1($party,$partyid,$findweek){ 
            $allData = DB::table('manage_times')
                ->join('clinic_time', 'manage_times.ManageTimeID', '=', 'clinic_time.ManageTimeID')
                ->select('manage_times.ManageTimeID','manage_times.Party','manage_times.PartyID','manage_times.Type','manage_times.From_Date','manage_times.To_Date','manage_times.Repeat','manage_times.Status',
                        'clinic_time.ClinicTimeID','clinic_time.StartTime','clinic_time.EndTime','clinic_time.Mon','clinic_time.Tue','clinic_time.Wed','clinic_time.Thu','clinic_time.Fri','clinic_time.Sat','clinic_time.Sun') 
                ->where('manage_times.PartyID', '=', $partyid)    
                ->where('manage_times.Party', '=', $party)
                ->where('clinic_time.'.$findweek, '=', 1)    
                ->where('clinic_time.Active', '=', 1)
                    //->where('manage_times.Status', '=', 1) 
                ->where('manage_times.Active', '=', 1)    
                ->get();
            return $allData;
        }
        public function FindCurrentDayAvailableTimes_OLD($party,$partyid,$findweek,$currentdate){ 
            $allData = DB::table('manage_times')
                ->join('clinic_time', 'manage_times.ManageTimeID', '=', 'clinic_time.ManageTimeID')
                ->select('manage_times.ManageTimeID','manage_times.Party','manage_times.PartyID','manage_times.Type','manage_times.From_Date','manage_times.To_Date','manage_times.Repeat','manage_times.Status',
                        'clinic_time.ClinicTimeID','clinic_time.StartTime','clinic_time.EndTime','clinic_time.Mon','clinic_time.Tue','clinic_time.Wed','clinic_time.Thu','clinic_time.Fri','clinic_time.Sat','clinic_time.Sun') 
                ->where('manage_times.PartyID', '=', $partyid)    
                ->where('manage_times.Party', '=', $party)
                ->where('clinic_time.'.$findweek, '=', 1)    
                ->where('clinic_time.Active', '=', 1)
                ->where('manage_times.Active', '=', 1)        
                    ->where('manage_times.From_Date', '<=', $currentdate)
                    //->where('manage_times.Repeat', '=', 1 || 'manage_times.To_Date', '>=', $currentdate)
                    ->where('manage_times.Repeat', '=', 1)
                    ->orWhere('manage_times.To_Date', '>=', $currentdate)
                
                ->get();
            return $allData;
        }
        public function FindCurrentDayAvailableTimes($party,$partyid,$findweek,$currentdate){
            $allData = DB::table('manage_times')
                ->join('clinic_time', 'manage_times.ManageTimeID', '=', 'clinic_time.ManageTimeID')
                ->select('manage_times.ManageTimeID','manage_times.Party','manage_times.PartyID','manage_times.Type','manage_times.From_Date','manage_times.To_Date','manage_times.Repeat','manage_times.Status',
                        'clinic_time.ClinicTimeID','clinic_time.StartTime','clinic_time.EndTime','clinic_time.Mon','clinic_time.Tue','clinic_time.Wed','clinic_time.Thu','clinic_time.Fri','clinic_time.Sat','clinic_time.Sun') 
                ->where('manage_times.PartyID', '=', $partyid)    
                ->where('manage_times.Party', '=', $party)
                ->where('clinic_time.'.$findweek, '=', 1)    
                ->where('clinic_time.Active', '=', 1)
                ->where('manage_times.Active', '=', 1)        
                    ->where('manage_times.From_Date', '<=', $currentdate)
                    ->orderBy('starttime', 'desc')
                    //->where('manage_times.Repeat', '=', 1 || 'manage_times.To_Date', '>=', $currentdate)
                    //->where('manage_times.Repeat', '=', 1)
                    //->orWhere('manage_times.To_Date', '>=', $currentdate)
                
                
                ->where(function ($mydata) use ($currentdate) {
                    $mydata->where('manage_times.Repeat', '=', 1)
                    ->orWhere('manage_times.To_Date', '>=', $currentdate);   
                })
                ->get();
            return $allData;
        }
        public function FindEntireClinicAvailablity($party,$partyid,$currentdate,$days7){ 
            $allData = DB::table('manage_times')
                ->join('clinic_time', 'manage_times.ManageTimeID', '=', 'clinic_time.ManageTimeID')
                ->select('manage_times.ManageTimeID','manage_times.Party','manage_times.PartyID','manage_times.Type','manage_times.From_Date','manage_times.To_Date','manage_times.Repeat','manage_times.Status',
                        'clinic_time.ClinicTimeID','clinic_time.StartTime','clinic_time.EndTime','clinic_time.Mon','clinic_time.Tue','clinic_time.Wed','clinic_time.Thu','clinic_time.Fri','clinic_time.Sat','clinic_time.Sun') 
                
                ->where('manage_times.PartyID', '=', $partyid)    
                ->where('manage_times.Party', '=', $party)
                //->where('clinic_time.'.$findweek, '=', 1)    
                ->where('clinic_time.Active', '=', 1)
                ->where('manage_times.Active', '=', 1)        
                ->where('manage_times.From_Date', '<=', $currentdate)
                ->where('manage_times.Repeat', '=', 1 || 'manage_times.To_Date', '<=', $days7)
                    //->where('manage_times.To_Date', '=', 0)
                    //->orWhere('manage_times.To_Date', '>=', $currentdate)
                
                ->get();
            return $allData;
        }
        public function FindLimitClinicAvailablity($party,$partyid,$currentdate){ 
            $allData = DB::table('manage_times')
                ->join('clinic_time', 'manage_times.ManageTimeID', '=', 'clinic_time.ManageTimeID')
                ->select('manage_times.ManageTimeID','manage_times.Party','manage_times.PartyID','manage_times.Type','manage_times.From_Date','manage_times.To_Date','manage_times.Repeat','manage_times.Status',
                        'clinic_time.ClinicTimeID','clinic_time.StartTime','clinic_time.EndTime','clinic_time.Mon','clinic_time.Tue','clinic_time.Wed','clinic_time.Thu','clinic_time.Fri','clinic_time.Sat','clinic_time.Sun') 
                
                ->where('manage_times.PartyID', '=', $partyid)    
                ->where('manage_times.Party', '=', $party)
                //->where('clinic_time.'.$findweek, '=', 1)    
                ->where('clinic_time.Active', '=', 1)
                ->where('manage_times.Active', '=', 1)        
                ->where('manage_times.From_Date', '<=', $currentdate)
                //->where('manage_times.Repeat', '=', 1 || 'manage_times.To_Date', '>=', $currentdate)
                    //->where('manage_times.To_Date', '=', 0)
                    //->orWhere('manage_times.To_Date', '>=', $currentdate)
                ->where(function ($mydata) use ($currentdate) {
                    $mydata->where('manage_times.Repeat', '=', 1)
                    ->orWhere('manage_times.To_Date', '>=', $currentdate);   
                })
                ->get();
            return $allData;
        }



        #################################################### nhr ##################################################


        public function findDoctorManageTime($doctorID)
        {
            $data = DB::table('manage_times')
                ->where('active', '=', 1)
                ->where('PartyID', '=', $doctorID)
                ->where('Party', '=', 2)
                ->get();

        return $data;
        }

        public function findClinicManageTime($ClinicID)
        {
            $data = DB::table('manage_times')
                ->where('active', '=', 1)
                ->where('PartyID', '=', $ClinicID)
                ->where('Party', '=', 3)
                ->get();

        return $data;
        }
        
}