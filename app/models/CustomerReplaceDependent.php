<?php

class CustomerReplaceDependent extends Eloquent 
{

	protected $table = 'customer_replace_dependent';
    protected $guarded = ['customer_replace_dependent_id'];

    public function createReplacement($data)
    {
    	return CustomerReplaceDependent::create($data);
    }
}
