<?php

class TempUpdateEmployee extends Eloquent 
{

    protected $table = 'employee_temporary_update';
    protected $guarded = ['id'];

    public function getTempUpdateEmployee($id)
    {
        return TempUpdateEmployee::where('customer_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertTempUpdateEmployee($data)
    {
        return TempUpdateEmployee::create($data);
    }

    public function updateTempUpdateEmployee($id, $data)
    {
        return CorporateCompanyContacts::where('id', $id)->update($data);
    }
    
}
