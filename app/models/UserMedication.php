<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserMedication extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_medication';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = array('password', 'remember_token');
        
        public function insertUserMedication ($dataArray){
                $this->UserID = $dataArray['userid'];
                $this->Name = $dataArray['medication'];
                $this->Dosage = $dataArray['dosage'];
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
        public function getUserMedications($profileid){           
            $findMedication = DB::table('user_medication')
                    ->where('UserID', '=', $profileid)
                    ->where('Active', '=', 1)
                    ->orWhere('Active', '=', 5)
                    ->get();
            
            if($findMedication){
                return $findMedication;
            }else{
                return FALSE;
            }  
        }
        
        public function updateUserMedication ($dataArray){         
            $allData = DB::table('user_medication')
                ->where('MedicationID', '=', $dataArray['medicationid'])
                ->update($dataArray);
            
            return $allData;
        }
        
       
}
