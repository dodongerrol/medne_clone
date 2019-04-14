<?php

class TempEnrollment extends Eloquent
{

	protected $table = 'customer_temp_enrollment';
  protected $guarded = ['temp_enrollment_id'];

  public function insertTempEnrollment($data)
  {
  	return TempEnrollment::create($data);
  }

  public function updateEnrollee($data)
  {
  	return TempEnrollment::where('temp_enrollment_id', $data['temp_enrollment_id'])->update($data);
  }

  public function removeEnrollees($customer_id, $ids)
  {
    // decrement plan status
    // foreach($ids as $key => $id) {
    //   $get = TempEnrollment::where('temp_enrollment_id', $id)->first();
    //   $active = DB::table('')
    // }
    return TempEnrollment::whereIn('temp_enrollment_id', $ids)->delete();
  }
}
