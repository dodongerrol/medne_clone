<?php

class CompanyStatementInNetworkTransaction extends Eloquent 
{

	protected $table = 'statement_in_network_transactions';
  protected $guarded = ['statement_in_network_transaction_id'];

  public function createStatementInNetworkTransaction($data)
  {
  	$check = CompanyStatementInNetworkTransaction::where('statement_id', $data['statement_id'])
  					->where('transaction_id', $data['transaction_id'])->count();
  	if($check == 0) {
  		return CompanyStatementInNetworkTransaction::create($data);
  	}
  }
}
