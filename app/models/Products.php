<?php

class Products extends Eloquent 
{

	protected $table = 'products';
    protected $guarded = ['product_id'];

    public function getProducts()
    {
        return Products::all();
    }

}
