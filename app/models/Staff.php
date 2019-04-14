<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class staff extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'staff';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
      
        
      public function insertstaff($dataArray){

             $this->name         = $dataArray['name'];
             $this->clinic_id    = $dataArray['clinicid'];
             $this->email        = $dataArray['email'];
             $this->active       = 1;
             if($this->save()){
                $insertedId = $this->id;
                return $insertedId;
            }else{
                return false;
            }  

      } 

    


    public function getStaffList($clinic_id){  

        $staff = DB::table('staff')
                ->where('active', '=', 1)
                ->where('clinic_id', '=', $clinic_id)
                ->get();

        return $staff; 
    }

    

    public function getStaffDetails($staff_id){  

        $staff = DB::table('staff')
                ->where('staff_id', '=', $staff_id)
                ->where('active', '=', 1)
                ->get();

        return $staff; 
    }


    public function updateStaff ($dataArray){
            
            $allData = DB::table('staff')
                ->where('staff_id', '=', $dataArray['staff_id'])
                ->update($dataArray);
            
            return $allData;
        }


      public function getStaffByPin($clinic_id, $pin){  

        $staff = DB::table('staff')
                ->where('active', '=', 1)
                ->where('clinic_id', '=', $clinic_id)
                ->where('pin_no', '=', $pin)
                // ->where('check_login', '=', 1)
                ->first();

        return $staff; 
    }

}
