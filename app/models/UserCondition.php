<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserCondition extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_condition';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = array('password', 'remember_token');
        
        public function insertMedicalCondition ($dataArray){
                $this->UserID = $dataArray['userid'];
                $this->Name = $dataArray['condition'];
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
        public function getUserConditions($profileid){           
            $findCondition = DB::table('user_condition')
                    ->where('UserID', '=', $profileid)
                    ->where('Active', '=', 1)
                    ->orWhere('Active', '=', 5)
                    ->get();
            
            if($findCondition){
                return $findCondition;
            }else{
                return FALSE;
            }  
        }
        
        public function updateUserCondition ($dataArray){         
            $allData = DB::table('user_condition')
                ->where('ConditionID', '=', $dataArray['conditionid'])
                ->update($dataArray);
            
            return $allData;
        }
        
       
}
