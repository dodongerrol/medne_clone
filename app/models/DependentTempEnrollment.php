<?php

class DependentTempEnrollment extends Eloquent 
{

	protected $table = 'dependent_temp_enrollment';
    protected $guarded = ['dependent_temp_id'];

    public function createEnrollment($data)
    {
    	return DependentTempEnrollment::create($data);
    }

    public function updateEnrollement($data)
    {
    	return DependentTempEnrollment::where('dependent_temp_id', $data['dependent_temp_id'])->update($data);
    }

    public function updateEnrollementStatus($dependent_temp_id)
    {
        return DependentTempEnrollment::where('dependent_temp_id', $dependent_temp_id)->update(['enrolled_status' => 1]);
    }
}
