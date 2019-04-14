<?php


class ClinicTypes extends Eloquent {

	protected $table = 'clinic_types';
    protected $guarded = ['ClinicTypeID'];
	
    public function getClinicType($id)
    {
        return ClinicTypes::where('ClinicTypeID', $id)->first();
    }

    public function getSubClinics($id)
    {
    	return ClinicTypes::where('sub', 1)
              ->where('sub_id', $id)
              ->get();
    }
}
