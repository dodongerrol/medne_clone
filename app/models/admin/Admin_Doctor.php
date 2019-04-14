<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Admin_Doctor extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'doctor';
	protected $primaryKey = 'DoctorID';

 	// Add all clinic details
    public function AddDoctor()
    {

    	$this->Name 		   	= Input::get('name');                            
		$this->Qualifications  	= Input::get('qualifications');            
		$this->Specialty       	= Input::get('specialty');            
		$this->Emergency       	= Input::get('emergency');            
		$this->Phone           	= Input::get('phone');            
		$this->Email           	= Input::get('email');		                                                
		$this->Active          	= 1;
		if(Input::hasFile('file'))
		{
			$upload 		= Image_Library::CloudinaryUpload(); // image upload			
    		$this->image 	= $upload;        
		}
		else
		{
            $this->image 	= 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1439201585/i8tt42cpmkd43aorrjtx.png';		
		}     

    	if($this->save()){
    		$doctorId = $this->DoctorID;
    		return $doctorId;
    	}else{
    		return false;
    	}      
    }

 	//Get all doctor details
	public function GetDoctorDetails()
	{
		$doctorData = DB::table('doctor')			
			->get();		

			return $doctorData;
	}

 	public function UpdateDoctor($dataArray)
    { 		
		$allData = DB::table('doctor')
                ->where('DoctorID', '=', $dataArray['doctorid'])
                ->update($dataArray);
            
            return $allData;
    }

}
