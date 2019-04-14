<?php

class PaymentLogsStripe extends Eloquent 
{

	protected $table = 'customer_payment_stripe_logs';
    protected $guarded = ['payment_stripe_logs_id'];

    public function createLog($data)
    {
    	return PaymentLogsStripe::create($data);
    }

    public function getLog($id)
    {
    	return PaymentLogsStripe::where('payment_stripe_logs_id', $id)->first();
    }
}
