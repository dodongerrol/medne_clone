<?php

class TempUpdateDependent extends Eloquent 
{

    protected $table = 'dependent_temporary_update';
    protected $guarded = ['id'];

    public function getTempUpdateDependent($id)
    {
        return TempUpdateEmployee::where('customer_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertTempUpdateDependent($data)
    {
        return TempUpdateEmployee::create($data);
    }

    public function updateTempUpdateDependent($id, $data)
    {
        return CorporateCompanyContacts::where('id', $id)->update($data);
    }
    
}
