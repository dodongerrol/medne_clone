<?php

class CorporateCompanyContacts extends Eloquent 
{

    protected $table = 'company_contacts';
    protected $guarded = ['medi_company_contact_id'];

    public function getCompanyContacts($id)
    {
        return CorporateCompanyContacts::where('customer_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertCompanyContacts($data)
    {
        return CorporateCompanyContacts::create($data);
    }

    public function updateCompanyContacts($id, $data)
    {
        return CorporateCompanyContacts::where('medi_company_contact_id', $id)->update($data);
    }
    
}
