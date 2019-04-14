<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ClinicTimeManage extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'clinic_time_manage';
        
        public function AddClinicTimeManage ($dataArray){
                $this->Name = $dataArray['name'];
                $this->Email = $dataArray['email'];
                $this->Description = null;
                $this->Qualifications = $dataArray['qualification'];
                $this->Specialty = $dataArray['speciality'];
                $this->Availability = null;
                $this->image = 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1428405297/is9qvklrjvkmts1pvq8r.png';
                $this->Phone = $dataArray['mobile'];
                $this->Emergency = $dataArray['mobile'];
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
        
        
        public function FindClinicTimesStatus($clinicid,$week){ 
            $allData = DB::table('clinic_time')
                ->where('ClinicID', '=', $clinicid)
                ->where('Active', '=', 1)
                ->where($week, '=', 1)
                ->get();
            return $allData;
        }
        
        public function FindClinicTimes($clinicid){ 
            $allData = DB::table('clinic_time')
                ->where('ClinicID', '=', $clinicid)
                ->where('Active', '=', 1)
                ->get();
            return $allData;
        }
        
        
        
}