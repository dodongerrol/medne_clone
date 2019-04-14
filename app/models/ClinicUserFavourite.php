<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class clinicUserFavourite extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'clinic_user_favourite';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
      
        
      public function insertFavourite($user_id,$clinic_id,$favourite){

             $this->user_id       = $user_id;
             $this->clinic_id     = $clinic_id;
             $this->favourite     = $favourite;
             if($this->save()){
                $insertedId = $this->id;
                return $insertedId;
            }else{
                return false;
            }  

      } 

    


    public function getStatus($clinic_id,$user_id){  

        $data = DB::table('clinic_user_favourite')
                ->where('user_id', '=', $user_id)
                ->where('clinic_id', '=', $clinic_id)
                ->first();

        return $data; 
    }

  


    public function updateFavourite($dataArray){
            
            $allData = DB::table('clinic_user_favourite')
                ->where('user_id', '=', $dataArray['user_id'])
                ->where('clinic_id', '=', $dataArray['clinic_id'])
                ->update($dataArray);
            
            return $allData;
        }



}
