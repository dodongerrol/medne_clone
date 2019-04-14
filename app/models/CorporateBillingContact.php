<?php

class CorporateBillingContact extends Eloquent 
{

	protected $table = 'customer_billing_contact';
    protected $guarded = ['customer_billing_contact_id'];

    public function getCorporateBillingContact($id)
    {
        return CorporateBillingContact::where('customer_buy_start_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertCorporateBillingContact($data)
    {
        return CorporateBillingContact::create($data);
    }

    public function updateBillingContact($id, $data)
    {
        return CorporateBillingContact::where('customer_billing_contact_id', $id)->update($data);
    }

    public function updateBillingContactbyCustomerID($id, $data)
    {
        return CorporateBillingContact::where('customer_buy_start_id', $id)->update($data);
    }
    
}
