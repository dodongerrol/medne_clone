<?php

class CorporatePayment extends Eloquent 
{

	protected $table = 'customer_payment_method';
    protected $guarded = ['customer_payment_id'];

    public function getCorporatePayment($id)
    {
    	return CorporatePayment::where('customer_buy_start_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertCorporatePayment($data)
    {
    	return CorporatePayment::create($data);
    }

    public function checkPayment($id, $start_date)
    {
        return CorporatePayment::where('customer_buy_start_id', $id)->whereDate('start_date', date('Y-m-d', strtotime($start_date)))->count();
    }

    public function updatePayment($data, $id)
    {
        return CorporatePayment::where('customer_buy_start_id', $id)->update($data);
    }

    public function checkCorporatePayment($id)
    {
        return CorporatePayment::where('customer_buy_start_id', $id)->count();
    }


}
