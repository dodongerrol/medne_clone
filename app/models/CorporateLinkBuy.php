<?php

class CorporateLinkBuy extends Eloquent 
{

    protected $table = 'customer_link_customer_buy';
    protected $guarded = ['customer_link_customer_buy_id'];

    public function insert($data)
    {
        return CorporateLinkBuy::create($data);
    }

    public function getCorporateLink($id)
    {
    	return CorporateLinkBuy::where('customer_buy_start_id', $id)->first();
    }

    public function checkUserLink($id)
    {
        return CorporateLinkBuy::where('customer_buy_start_id', $id)->count();
    }
}
