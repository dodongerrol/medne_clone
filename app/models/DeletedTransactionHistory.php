<?php

class DeletedTransactionHistory extends Eloquent 
{

	protected $table = 'deleted_transaction';
  protected $guarded = ['deleted_transaction_id'];

  public function createDeletedTransaction($data)
  {
  	return DeletedTransactionHistory::create($data);
  }
}
