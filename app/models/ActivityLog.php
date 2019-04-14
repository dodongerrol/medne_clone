<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ActivityLog extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'activity_log';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
      
        
      public function insertActivityLog($dataArray){

             $this->id        = $dataArray['id'];
             $this->user_pin  = $dataArray['pin'];
             $this->date_time = strtotime(date('Y-m-d H:i:s'));
             $this->activity_header  = $dataArray['header'];
             $this->activity  = $dataArray['activity'];

             if($this->save()){
                $insertedId = $this->id;
                return $insertedId;
            }else{
                return false;
            }  

      } 

    public function removeEvent($doctor_id){
        DB::table('extra_events')
            ->where('type', '=', 1)
            ->where('doctor_id', '=', $doctor_id)
            ->delete();

    }    



public function getEvents($doctor_id,$date){           
    $events = DB::table('extra_events')
            ->where('doctor_id', '=', $doctor_id)
            ->where('date', '=', $date)
            // ->where('type', '=', 1)
            ->get();
        //     DB::enableQuerylog();
        // dd(DB::getQueryLog());
    return $events; 
}




}
