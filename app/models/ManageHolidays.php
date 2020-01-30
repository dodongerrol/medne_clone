<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ManageHolidays extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'manage_holidays';
        
        /*** Backup Add Holiday Quiery ***/

        public function AddManageHolidays_Backup ($dataArray){
            $this->Party = $dataArray['party'];
            $this->PartyID = $dataArray['partyid'];
            $this->Type = $dataArray['holidaytype'];
            $this->Title = $dataArray['title'];
            $this->Holiday = $dataArray['holiday'];
            $this->From_Time = $dataArray['fromtime'];   
            $this->To_Time = $dataArray['totime'];     
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

        public function AddManageHolidays ($dataArray){
            $this->Party = $dataArray['party'];
            $this->PartyID = $dataArray['partyid'];
            $this->Type = $dataArray['holidaytype'];
            $this->Title = $dataArray['title'];
            $this->From_Holiday = $dataArray['fromholiday'];
            $this->To_Holiday = $dataArray['toholiday'];
            $this->From_Time = $dataArray['fromtime'];
            $this->To_Time = $dataArray['totime'];
            $this->Note = $dataArray['note'];
            $this->Created_on = time();
            $this->created_at = time();
            $this->updated_at = 0;
            $this->Active = 1;

            if($this->save()){
                return TRUE;
            }else{
                return false;
            }
        }
        
        public function FindExistingClinicHolidays($party,$partyid){ 
            $allData = DB::table('manage_holidays')
                ->where('Party', '=', $party)
                ->where('PartyID', '=', $partyid)  
                ->where('Active', '=', 1)    
                ->get();
            return $allData;
        }
        
        public function UpdateManageHolidays($dataArray){       
            $allData = DB::table('manage_holidays')
                ->where('ManageHolidayID', '=', $dataArray['manageholidayid'])
                ->update($dataArray);
            
            return $allData;
        }
        
        public function FindPartyFullDayHolidays($party,$partyid,$holiday){    
            $allData = DB::table('manage_holidays')
                ->where('Party', '=', $party)
                ->where('PartyID', '=', $partyid) 
                ->where('Type', '=', 0) 
                ->where('Holiday', '=', $holiday)    
                ->where('Active', '=', 1)    
                ->first();
            return $allData;
        }
        public function FindCurrentDayHolidays($party,$partyid,$holiday){    
            $allData = DB::table('manage_holidays')
                ->where('Party', '=', $party)
                ->where('PartyID', '=', $partyid) 
                //->where('Type', '=', 0) 
                ->where('Holiday', '=', $holiday)    
                ->where('Active', '=', 1)    
                ->first();
            return $allData;
        }
        
        public function FindUpcomingHolidays($party,$partyid,$holiday){ 
            $allData = DB::table('manage_holidays')
                ->where('Party', '=', $party)
                ->where('PartyID', '=', $partyid)  
                ->where('Holiday', '>=', $holiday)  
                ->where('Active', '=', 1)    
                ->get();
            return $allData;
        }
        
//        public function FindClinic7DayHolidays($party,$partyid,$holiday,$days7){ 
//            $allData = DB::table('manage_holidays')
//                ->where('Party', '=', $party)
//                ->where('PartyID', '=', $partyid)  
//                ->where('Holiday', '>=', $holiday && 'Holiday', '<=', $days7)  
//                ->where('Active', '=', 1)    
//                ->get();
//            return $allData;
//        }

        public function FindSelectedHoliday($Holiday_id){
            $allData = DB::table('manage_holidays')
                ->where('ManageHolidayID', '=', $Holiday_id) 
                ->where('Active', '=', 1)
                ->first();
            return $allData;

            dd($allData);
        }
        
        
        
        
       public function findClinicTimeoff($clinicid){

            $allData = DB::table('manage_holidays')
                ->where('PartyID', '=', $clinicid) 
                ->where('Party', '=', 3) 
                ->where('Active', '=', 1)
                ->get();
            return $allData;
        } 
        
        
        
        public function FindTodayTimeOFF($doctorid, $bookdate){

            $bookdate = date('Y-m-d', strtotime($bookdate));
            $results = DB::select("SELECT * FROM medi_manage_holidays WHERE PartyID=? and Party=? and Active=? and (? between STR_TO_DATE(From_Holiday,'%d-%m-%Y') AND STR_TO_DATE(To_Holiday,'%d-%m-%Y'))", array($doctorid,2,1,$bookdate));

            return $results;
        } 

        public function FindTodayClinicTimeOFF($clinicid, $bookdate){

            $results = DB::table('manage_holidays')
                        ->where('PartyID', $clinicid)
                        ->where('Party', 3)
                        ->where('Active', 1)
                        ->where('From_Holiday', '>=', $bookdate)
                        ->where('To_Holiday', '<=', $bookdate)
                        ->get();
            return $results;
            $results = DB::select("SELECT * FROM medi_manage_holidays WHERE PartyID=? and Party=? and Active=? and (? between From_Holiday AND To_Holiday)", array($clinicid,3,1,$bookdate));

            return $results;
        }

        public function FindFullDayHolidays($party,$partyid){

            $allData = DB::table('manage_holidays')
                ->where('Party', '=', $party)
                ->where('PartyID', '=', $partyid)
                ->where('Type', '=', 0)
                ->where('Active', '=', 1)
                ->get();
            return $allData;
        }

        public function FindCustomTimeHolidays($party,$partyid){
            
            $allData = DB::table('manage_holidays')
                ->where('Party', '=', $party)
                ->where('PartyID', '=', $partyid)
                ->where('Type', '=', 1)
                ->where('Active', '=', 1)
                ->get();
            return $allData;
        }
        
}