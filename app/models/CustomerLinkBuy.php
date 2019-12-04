<?php

class CustomerLinkBuy extends Eloquent 
{

	protected $table = 'customer_link_customer_buy';
  protected $guarded = ['customer_link_customer_buy_id'];

  public function getData($id)
  {
  	return AdminLogs::where('customer_buy_start_id', $id)->first();
  }
}
