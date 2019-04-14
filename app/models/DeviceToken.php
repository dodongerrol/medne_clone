<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class DeviceToken extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'device_token';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = array('password', 'remember_token');
        
        //Add new allergy
        public function AddDeviceToken($dataArray){
                $this->UserID = $dataArray['userid'];
                $this->Token = $dataArray['device_token'];
                $this->Device_Type = $dataArray['device_type'];
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
      
        
        public function GetDeviceToken($profileid){           
            $findToken = DB::table('device_token')
                    ->where('UserID', '=', $profileid)
                    ->where('Active', '=', 1)
                    ->first();
            
            if($findToken){
                return $findToken;
            }else{
                return FALSE;
            }  
        }
        
        public function UpdateDeviceToken($dataArray,$tokenid){         
            $allData = DB::table('device_token')
                ->where('DeviceTokenID', '=', $tokenid)
                ->update($dataArray);
            
            return $allData;
        }
        
       
}
