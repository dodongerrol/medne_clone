<?php

class Orders extends Eloquent 
{

	protected $table = 'orders';
    protected $guarded = ['order_id'];

    public function createOrder($data)
    {
    	return Orders::create($data);
    }
}
