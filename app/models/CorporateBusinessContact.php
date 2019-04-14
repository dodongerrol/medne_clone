<?php

class CorporateBusinessContact extends Eloquent 
{

	protected $table = 'customer_business_contact';
    protected $guarded = ['customer_business_contact_id'];

    public function getCorporateBusinessContact($id)
    {
        return CorporateBusinessContact::where('customer_buy_start_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertCorporateBusinessContact($data)
    {
        return CorporateBusinessContact::create($data);
    }

    public function updateBusinessContact($id, $data)
    {
        return CorporateBusinessContact::where('customer_business_contact_id', $id)->update($data);
    }

    public function updateBusinessContactbyCustomerID($id, $data)
    {
        return CorporateBusinessContact::where('customer_buy_start_id', $id)->update($data);
    }
    
}
