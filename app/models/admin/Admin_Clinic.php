<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Admin_Clinic extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'clinic';
	protected $primaryKey = 'ClinicID';
	public $timestamps = false;

	// public static function validate($input)
	// {
	// 	$rules = array(
	// 			'Name'=>'required',
	// 			'Description'=>'required',
	// 			);

	// 	return Validator::make($input, $rules);
	// }


    // Get all clinic details
    public function GetClinicDetails()
    {
       $clinicData = DB::table('clinic')                                                      
	 			->leftJoin ('doctor_availability', 'clinic.ClinicID', '=', 'doctor_availability.ClinicID')
                ->select('clinic.ClinicID','clinic.Name','clinic.image','clinic.City','clinic.Country','clinic.Lat','clinic.Lng','clinic.Phone','clinic.Opening','clinic.image','clinic.Active', 'clinic.medicloud_transaction_fees', DB::raw('COUNT(medi_doctor_availability.ClinicID) AS DoctorCount'),DB::raw('COUNT(CASE WHEN medi_doctor_availability.Active = 1 THEN medi_doctor_availability.ClinicID END)AS ActiveCount'),DB::raw('COUNT(CASE WHEN medi_doctor_availability.Active = 0 THEN medi_doctor_availability.ClinicID END)AS InactiveCount'))                
                ->groupBy('clinic.ClinicID');
                // ->get();
         return $clinicData;
  //       $collection = Datatable::query($clinicData)
  //       		->showColumns('ClinicID','Name', 'image','City','Country','Lat', 'Lng','Phone','Opening','ActiveCount','InactiveCount', 'medicloud_transaction_fees')
		//         ->setSearchWithAlias()
		//         ->searchColumns('ClinicID','Name')
		//         ->addColumn('medicloud_transaction_fees', function($model){
		//         	return $model->medicloud_transaction_fees.'%';
		//         })
		//         ->addColumn('Active',function($model)
		//         {
		//            if ($model->Active == 1)
		// 		   {
		// 				return 'Active';
		// 		   }
		// 		   else
		// 	   	   {
		// 	   	   		return 'Inactive';
		// 		   }
		//         })
		//         ->addColumn('Edit',function($model)
		//         {
		//             return "<a href=".$model->ClinicID."/edit class='btn btn-info'>Edit</a>" ;
		//         })
		//         ->addColumn('Set Time',function($model)
		//         {
		//             return "<a href=".$model->ClinicID."/new-time class='btn btn-info'>Set Time</a>" ;
		//         })
		//         ->make();
		// return $collection;
    }




// nhr new clinic add   2016-5-9

    public function newClinic()
    {
    	$this->Created_on   = time();                                                
		$this->Active       = 1;
		$this->configure 	= 0;

		if($this->save()){
    		$clinicId = $this->ClinicID;
    		return $clinicId;
    	}else{
    		return false;
    	}
    }

    // Add all clinic details
    public function AddClinic()
    {

    	$this->Custom_title	= Input::get('custom_title');                            
    	$this->Name 		= Input::get('name');                            
		$this->Description  = Input::get('description');            
		$this->Website  	= Input::get('website');            
		$this->Address      = Input::get('address');            
		$this->City         = Input::get('city');            
		$this->State        = Input::get('state');            
		$this->Country      = Input::get('country');            
		$this->Postal       = Input::get('postal');            
		$this->District     = Input::get('district');            
		$this->Lat          = Input::get('latitude');  
		$this->Lng          = Input::get('longitude');
		$this->Phone        = Input::get('phone');            
		$this->MRT          = Input::get('mrt');            
		$this->Opening      = Input::get('opening');            
		$this->Created_on   = time();                                                
		$this->Active       = 1; 
		if(Input::hasFile('file'))
		{
			$upload 		= Image_Library::CloudinaryUpload(); // image upload			
    		$this->image 	= $upload;        
		}
		else
		{
            $this->image 	= 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1439208475/medilogo_cn6d0x.png';		
		}

		if(Input::hasFile('clinic_price'))
		{
			$uploadClinicPrice 		= Image_Library::CloudinaryUploadFile(Input::file('clinic_price')); // image upload			
    		$this->Clinic_Price 	= $uploadClinicPrice;        
		}

    	if($this->save()){
    		$clinicId = $this->ClinicID;
    		return $clinicId;
    	}else{
    		return false;
    	}      
    }

    public function UpdateClinic($dataArray)
    { 		
		$allData = DB::table('clinic')
                ->where('ClinicID', '=', $dataArray['clinicid'])
                ->update($dataArray);
            
            return $allData;
    }

    //Get all clinic list
	public function GetClinicList()
	{
		$clinicData = DB::table('clinic')
		    ->where('Active',1)
		    ->lists('Name','ClinicID');
			return $clinicData;
	}
        public function GetAllClinics(){
            $getBooking = DB::table('clinic')
                //->where('Name', 'like', "%{$clinicname}%")  
                ->get();
            return $getBooking;
        }



        public function getClinicdata($clinicid)
        {
        	$clinicData = DB::table('clinic')
		    ->where('Active',1)
		    ->where('ClinicID',$clinicid)
		    ->get();
			return $clinicData;
		}
		
		public function getClinicInfo($clinicid) {
			return DB::table('clinic')
					->select(DB::raw('(CASE
							WHEN
								currency_type = "sgd"
							THEN
								"+65"
							ELSE
								"+60"
						END) as PhoneCode'),
						'ClinicID', 'Name', 'Clinic_Type', 'Description', 
						'Custom_title', 'Website', 'image', 'Address', 'City', 
						'State', 'Country', 'Postal', 'District', 'Lat', 'Lng', 
						'Phone_Code', 'Phone', 'MRT', 'Clinic_Price', 'Opening', 
						'Calendar_type', 'Calendar_day', 'Calendar_duration', 'Calendar_Start_Hour', 
						'Require_pin', 'Favourite', 'Personalized_Message', 'Created_on', 'Active', 
						'medicloud_transaction_fees', 'discount', 'configure', 'co_paid_amount', 'co_paid_status', 
						'position', 'gst_amount', 'gst', 'billing_name', 'billing_address', 'billing_status', 'communication_email', 
						'test_account', 'currency_type', 'peak_hour_status', 'peak_hour_amount', 'peak_hour_start', 
						'gst_percent', 'peak_hour_end', 'consultation_fees', 'consultation_gst_status')
					->where('Active',1)
					->where('ClinicID',$clinicid)
					->get();
		}
}
