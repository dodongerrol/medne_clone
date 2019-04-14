<?php

class Cart extends Eloquent 
{

	protected $table = 'cart';
    protected $guarded = ['cartID'];

    public function createCart($data)
    {
    	return Cart::create($data);
    }
}
