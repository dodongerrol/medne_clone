<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserAllergy extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_allergy';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = array('password', 'remember_token');
        
        //Add new allergy
        public function insertAllergy ($dataArray){
                $this->UserID = $dataArray['userid'];
                $this->Name = $dataArray['allergy'];
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
        public function getUserAllergies($profileid){           
            $findAllergy = DB::table('user_allergy')
                    ->where('UserID', '=', $profileid)
                    ->where('Active', '=', 1)
                    ->orWhere('Active', '=', 5)
                    ->get();
            
            if($findAllergy){
                return $findAllergy;
            }else{
                return FALSE;
            }  
        }
        
        public function updateUserAllergy ($dataArray){         
            $allData = DB::table('user_allergy')
                ->where('AllergyID', '=', $dataArray['allergyid'])
                ->update($dataArray);
            
            return $allData;
        }
        
       
}
