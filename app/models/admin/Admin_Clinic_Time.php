<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Admin_Clinic_Time extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'clinic_time';
	protected $primaryKey = 'ClinicID';
	public $timestamps = true;


	 //Get all clinic times
	public function GetClinicTime($id)
	{
		$clinicTimeData = DB::table('clinic_time')
		    // ->where('Active',1)
		    ->where('ClinicID',$id)
		    ->get();
			return $clinicTimeData;
	}


	public function GetClinicTimeIdDetails($id)
	{
		$clinicDetails = DB::table('clinic_time')
		    // ->where('Active',1)
		    ->where('ClinicTimeID',$id)
		    ->get();
			return $clinicDetails;
	}

	public function UpdateTimeIdDetails($dataArray)
    { 		
		$allData = DB::table('clinic_time')
                ->where('ClinicTimeID','=', $dataArray['clinictimeid'])
                ->update($dataArray);            
            return $allData;
    }


 	
 	
}
