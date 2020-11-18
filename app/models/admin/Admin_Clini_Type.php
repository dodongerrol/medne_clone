<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Input;

class Admin_Clinic_Type extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'clinic_types';
	protected $primaryKey = 'ClinicTypeID';

 	
 	//Get all clinic types
	public function GetClinicTypes()
	{
		$clinicTypeData = DB::table('clinic_types')
		    ->where('Active',1)
		    ->lists('Name','ClinicTypeID');
			return $clinicTypeData;
	}


	public function getClinicWithSub()
	{
		$clinic_types = [];
		$result = Admin_Clinic_Type::where('head', 1)->orderBy('position', 'asc')->get();
		// return $result;
		foreach ($result as $key => $value) {
        $temp = array(
            'head_clinic'   => $value,
            'sub_clinic'    => Admin_Clinic_Type::where('sub_id', $value->ClinicTypeID)->get()
        );

        array_push($clinic_types, $temp);
    }

    return $clinic_types;
	}

	// ---  get all clinic types details -- 

	public function getClinicTypesFromWeb( )
	{
		$clinicTypeData = DB::table('clinic_types')
						->select('ClinicTypeID','Name', 'clinic_type_image_url')
		    		// ->where('Active',1)
		    		->orderBy('position', 'desc')
		    		->get();
			return $clinicTypeData;
	}

	public function GetAllClinicTypes()
	{
		$input = Input::all();
		$lang = isset($input['lang']) ? $input['lang'] : "en";
		
		$clinicTypeData = DB::table('clinic_types')
						->select('ClinicTypeID','Name', 'clinic_type_image_url')
			    		->where('Active',1)
			    		->where('head',1)
			    		->orderBy('position', 'asc')
			    		->get();

		foreach($clinicTypeData as $type) {
			$type->Name = $lang == "malay" ? \MalayTranslation::benefitsCategoryTranslate($type->Name) : $type->Name;
		}

		if(!empty($input['type']) && $input['type'] != null) {
			$format = [];
			$promotionals = DB::table('promotional_links')
								->where('active', 1)
								->get();

			foreach ($promotionals as $key => $promotional) {
				$temp = array(
					'ClinicTypeID'			=> '0'.$promotional->promotional_link_id,
					'Name'					=> $promotional->name,
					'clinic_type_image_url'	=> $promotional->image_link,
					'web_link'				=> $promotional->link,
					'promotional_link'		=> true,
					'type'					=> $promotional->type
				);

				array_push($format, $temp);
			}

			return array_merge($clinicTypeData, $format);
		} else {
			return $clinicTypeData;	
		}
	}

	public function NewAllClinicTypes( )
	{
		$clinicTypeData = DB::table('clinic_types')
						->select('ClinicTypeID','Name', 'clinic_type_image_url')
		    		->where('Active',1)
		    		->where('head',1)
		    		->orderBy('new_position', 'asc')
		    		->get();
			return $clinicTypeData;
	}

	// ---  get selected clinic type details -- 

	public function findClinicTypeDetails($value){

            $clinicTypeData = DB::table('clinic_types')
                ->where('ClinicTypeID', '=', $value)
                ->where('Active', '=', 1)    
                ->first();

                return $clinicTypeData;
        }



     // nhr main searach functions for mobile

     public function getSpeciality($search)
        {
        	$clinicTypeData = DB::table('clinic_types')
        		->select('ClinicTypeID as clinic_type_id','Name as name')
                ->where('Name', 'like', "%$search%")
                ->where('Active', '=', 1)    
                ->get();

                return $clinicTypeData;
        }   

}
