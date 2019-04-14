<?php

class ClinicRatings extends Eloquent 
{

	protected $table = 'clinic_rating';
  protected $guarded = ['clinic_rating_id'];

  public function createClinicRating($data)
  {
  	return ClinicRatings::create($data);
  }

  public function checkClinicRating($id)
  {
  	return ClinicRatings::where('appointment_id', $id)->count();
  }

  public function updateClinicRating($id, $data)
  {
  	return ClinicRatings::where('appointment_id', $id)->update($data);
  }
}
