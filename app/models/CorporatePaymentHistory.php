<?php

class CorporatePaymentHistory extends Eloquent 
{

	protected $table = 'customer_payment_history';
    protected $guarded = ['customer_payment_history_id'];

    public function insertCorporatePaymentHistory($data)
    {
        return CorporatePaymentHistory::create($data);
    }
    
}
