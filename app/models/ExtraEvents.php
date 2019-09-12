<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ExtraEvents extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'extra_events';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
      
        
      public function insertEvent($dataArray){

             $this->id          = $dataArray['id'];
             $this->type        = $dataArray['type'];
             $this->date        = $dataArray['date'];
             $this->start_time  = $dataArray['start_time'];
             $this->end_time    = $dataArray['end_time'];
             $this->doctor_id   = $dataArray['doctor_id'];
             $this->event_id    = $dataArray['event_id'];
             $this->remarks     = $dataArray['remarks'];

             if($this->save()){
                $insertedId = $this->id;
                return $insertedId;
            }else{
                return false;
            }  

      } 

      public function insertBreak($dataArray){

             $this->id          = $dataArray['id'];
             $this->type        = $dataArray['type'];
             $this->day        = $dataArray['day'];
             $this->start_time  = $dataArray['start_time'];
             $this->end_time    = $dataArray['end_time'];
             $this->doctor_id   = $dataArray['doctor_id'];
             $this->clinic_id   = $dataArray['clinic_id'];

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
                ->where('type', '=', 3)
                ->get();
            //     DB::enableQuerylog();
            // dd(DB::getQueryLog());
        return $events; 
    }

    public function getClinicEvents($clinic_id, $date){           
        $events = DB::table('extra_events')
                ->where('clinic_id', '=', $clinic_id)
                ->where('date', '=', $date)
                // ->where('type', '=', 1)
                ->get();
            //     DB::enableQuerylog();
            // dd(DB::getQueryLog());
        return $events; 
    }

    public function getExtraEvents($appointment_id){  

        $events = DB::table('extra_events')
                ->where('id', '=', $appointment_id)
                ->first();

            // DB::enableQuerylog();
            // dd(DB::getQueryLog());

        return $events; 
    }

    public function getDoctorExtraEvents($doctorid) {
        $events = DB::table('extra_events')
                ->where('doctor_id', '=', $doctorid)
                ->where('type', '!=', 1)
                ->get();
            //     DB::enableQuerylog();
            // dd(DB::getQueryLog());
        return $events; 
    }

    public function removeExtraEvents($event_id){
        DB::table('extra_events')
            ->where('id', '=', $event_id)
            ->delete();

    } 


    public function getDoctorBreaks($data){ 

        $events = DB::table('extra_events')
                ->where('doctor_id', '=', $data['doctor_id'])
                ->where('type', '=', $data['type'])
                ->get();

            // DB::enableQuerylog();
            // dd(DB::getQueryLog());

        return $events; 
    }



    public function updateBreak($dataArray)
    {
      $allData = DB::table('extra_events')
            ->where('id', '=', $dataArray['id'])
            ->update($dataArray);
            
            return $allData;
    }

    // 2016-5-18 

    public function findClinicBreaks($week, $clinicid){  

        $events = DB::table('extra_events')
                ->where('clinic_id', '=', $clinicid)
                ->where('day', '=', $week)
                ->where('type', '=', 3)
                ->orderBy('start_time', 'asc')
                ->get();
        return $events;
    }

    public function getClinicBreaks($data){ 

        $events = DB::table('extra_events')
                ->where('clinic_id', '=', $data['clinicid'])
                ->where('type', '=', $data['type'])
                ->get();

        return $events; 
    }

    public function getClinicBreaksByDay($data){ 

        $events = DB::table('extra_events')
                ->where('clinic_id', '=', $data['clinicid'])
                ->where('type', '=', $data['type'])
                ->where('day', '=', $data['day'])
                ->first();

        if($events) {
            return $events;
        }

        return FALSE; 
    }

    public function getNewClinicBreaksByDay($data){ 

        $events = DB::table('extra_events')
                ->where('clinic_id', '=', $data['clinicid'])
                ->where('type', '=', $data['type'])
                ->where('day', '=', $data['day'])
                ->orderBy('start_time', 'asc')
                ->get();

        if($events) {
            return $events;
        }

        return FALSE; 
    }


   public function FindTodayExtraEvents($doctorid,$bookdate)
    {
      $events = DB::table('extra_events')
                ->where('doctor_id', '=', $doctorid)
                ->where('date', '=', $bookdate)
                ->get();

        return $events;
    } 

    public function FindTodayBreaks($doctorid,$day)
    {
      $events = DB::table('extra_events')
                ->where('doctor_id', '=', $doctorid)
                ->where('day', '=', $day)
                ->get();

        return $events;
    }

    public function getAllBreaks(){ 

        $events = DB::table('extra_events')
                ->where('type', '=', 3)
                ->get();

        return $events; 
    }

    public function getAllDoctorBreaks($doctorid){ 

        $events = DB::table('extra_events')
                ->where('doctor_id', '=', $doctorid)
                ->where('type', '=', 3)
                ->get();

        return $events; 
    }

    public function getAllClinicBreaks($clinicid){ 

        $events = DB::table('extra_events')
                ->where('clinic_id', '=', $clinicid)
                ->where('type', '=', 3)
                ->get();

        return $events; 
    }


}
