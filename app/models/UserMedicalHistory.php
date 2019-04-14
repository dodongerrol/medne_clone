<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserMedicalHistory extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_medical_history';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = array('password', 'remember_token');
        
        //Add new medical history
        public function insertMedicalHistory ($dataArray){
                $this->UserID = $dataArray['userid'];
                $this->VisitType = $dataArray['visittype'];
                $this->Doctor_Name = $dataArray['doctor'];
                $this->Clinic_Name = $dataArray['clinic'];
                $this->Note = $dataArray['note'];
                $this->Date = $dataArray['date'];
                $this->Created_on = time();
                $this->created_at = time();
                $this->Active = 1;
                
                if($this->save()){
                    $insertedId = $this->id;
                    return $insertedId;
                }else{
                    return false;
                }      
        }
      
        //get user allergy 
        //parameter : user id 
        //Out put : array 
        public function getUserMedicalHistory($profileid){ 
            $findHistory = DB::table('user_medical_history')
                    //->leftJoin('doctor', 'user_medical_history.DoctorID', '=', 'doctor.DoctorID')
                    //->join('doctor', 'user_medical_history.DoctorID', '=', 'doctor.DoctorID')
                    //->select('doctor.Name', 'user_medical_history.HistoryID','user_medical_history.VisitType','user_medical_history.Note','user_medical_history.Date')
                    //->where('user_medical_history.UserID','=',$profileid)
                    //->where('user_medical_history.Active','=',1)
                    ->where('UserID','=',$profileid)
                    ->where('Active','=',1)
                    ->get();
                if($findHistory){
                    return $findHistory;
                }else{
                    return FALSE;
                } 
        }
        
        public function updateMedicalHistory ($dataArray){         
            $allData = DB::table('user_medical_history')
                ->where('HistoryID', '=', $dataArray['historyid'])
                ->update($dataArray);
            
            return $allData;
        }
        
       
}
