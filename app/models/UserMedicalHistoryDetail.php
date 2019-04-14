<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserMedicalHistoryDetail extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_medical_history_detail';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = array('password', 'remember_token');
        
      
        //get user allergy 
        //parameter : user id 
        //Out put : array 
        public function getUserMedicalHistoryDetails($historyid){ 
            $findHistoryDetail = DB::table('user_medical_history_detail')
                    //->leftJoin('doctor', 'user_medical_history.DoctorID', '=', 'doctor.DoctorID')
                    //->join('doctor', 'user_medical_history.DoctorID', '=', 'doctor.DoctorID')
                    //->select('doctor.Name', 'user_medical_history.HistoryID','user_medical_history.VisitType','user_medical_history.Note','user_medical_history.Date')
                    //->where('user_medical_history.UserID','=',$profileid)
                    ->where('HistoryID','=',$historyid)
                    ->where('Active','=',1)
                    ->get();
                if($findHistoryDetail){
                    return $findHistoryDetail;
                }else{
                    return FALSE;
                }     
        }
        
        public function updateHistoryDetails ($dataArray){         
            $allData = DB::table('user_medical_history_detail')
                ->where('HistoryID', '=', $dataArray['historyid'])
                ->update($dataArray);
            
            return $allData;
        }
        
       
}
