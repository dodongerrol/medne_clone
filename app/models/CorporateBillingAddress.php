<?php

class CorporateBillingAddress extends Eloquent 
{

	protected $table = 'customer_billing_address';
    protected $guarded = ['customer_billing_address_id'];

    public function getCorporateBillingAddress($id)
    {
        return CorporateBillingAddress::where('customer_buy_start_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertCorporateBillingAddress($data)
    {
        return CorporateBillingAddress::create($data);
    }

    public function updateBillingAddress($id, $data)
    {
        return CorporateBillingAddress::where('customer_buy_start_id', $id)->update($data);
    }
    
}
