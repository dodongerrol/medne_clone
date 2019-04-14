<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class SmsHistory extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sms_history';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
      
        
      public function insert($dataArray){

             $this->name         = $dataArray['name'];
             $this->clinic_id    = $dataArray['clinic_id'];
             $this->phone_code   = $dataArray['phone_code'];
             $this->phone_number = $dataArray['phone_number'];
             $this->message      = $dataArray['message'];
             if($this->save()){
                $insertedId = $this->id;
                return $insertedId;
            }else{
                return false;
            }  

      } 

    



}
