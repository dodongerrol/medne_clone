<?php

class TransactionProducts extends Eloquent 
{

	protected $table = 'transactions';
    protected $guarded = ['transaction_id'];

    public function createTransaction($data)
    {
    	return TransactionProducts::create($data);
    }
}
