<?php

class CorporateCompanyContacts extends Eloquent 
{

    protected $table = 'company_contacts';
    protected $guarded = ['medi_company_contact_id'];

    public function getHrLocations($id)
    {
        return CorporateHrLocation::where('customer_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertCorporateHrLocations($data)
    {
        return CorporateHrLocation::create($data);
    }

    public function updateCorporateHrLocations($id, $data)
    {
        return CorporateHrLocation::where('medi_company_contact_id', $id)->update($data);
    }
    
}
