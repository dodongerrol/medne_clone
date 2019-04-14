<?php

class PaymentRefund extends Eloquent 
{

	protected $table = 'payment_refund';
  protected $guarded = ['payment_refund_id'];

  public function PaymentRefundCart($data)
  {
  	return PaymentRefund::create($data);
  }
}
