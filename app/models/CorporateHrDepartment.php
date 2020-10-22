<?php

class CorporateHrDepartment extends Eloquent 
{

    protected $table = 'company_departments';
    protected $guarded = ['id'];

    public function getHrDepartments($id)
    {
        return CorporateHrDepartment::where('customer_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertCorporateHrDepartments($data)
    {
        return CorporateHrDepartment::create($data);
    }

    public function updateCorporateHrDepartments($id, $data)
    {
        return CorporateHrDepartment::where('id', $id)->update($data);
    }
    
}
