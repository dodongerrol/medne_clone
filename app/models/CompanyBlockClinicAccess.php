<?php

class CompanyBlockClinicAccess extends Eloquent 
{

	protected $table = 'company_block_clinic_access';
  protected $guarded = ['company_block_clinic_access_id'];

  public function createData($data)
  {
  	return CompanyBlockClinicAccess::create($data);
  }

  public function updateData($id, $data)
  {
  	return CompanyBlockClinicAccess::where('company_block_clinic_access_id', $id)->update($data);
  }
}
