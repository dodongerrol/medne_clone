<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Admin_Clinic_Types_Detail extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'clinic_types_detail';
	protected $primaryKey = 'ClinicTypesDetailID';

 	
 	public function AddClinicTypeDetail($clinicTypeId, $clinicId)
    {
    	$clinicTypeDetail = DB::table('clinic_types_detail')
    	    ->insert(
	            array('ClinicTypeID' => $clinicTypeId, 'Active' => '1', 'ClinicID'=> $clinicId,'created_at'=>time())
	        ); 

	    return $clinicTypeDetail;         
    }

    public function ClinicTypeByID($id)
	{
		$findResult = DB::table('clinic_types_detail')
		->where('ClinicID',$id)
		->where('Active','1')
		->lists('ClinicTypeID');

		return $findResult; 
	}

	public function UpdateClinicTypeDetails($id, $clinicTypeId)
    {
    	$clinicTypeStatus = DB::table('clinic_types_detail')
			    ->where('ClinicID',$id)					    
			    ->update(array('ClinicTypeID' => $clinicTypeId));

	    return $clinicTypeStatus;         
    }
}
