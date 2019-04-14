<?php

class CompanyStatementEclaimTransaction extends Eloquent 
{

	protected $table = 'statement_e_claim_transactions';
  protected $guarded = ['statement_e_claim_transaction_id'];

  public function createStatementEclaimTransaction($data)
  {
  	$check = CompanyStatementEclaimTransaction::where('statement_id', $data['statement_id'])
  					->where('e_claim_id', $data['e_claim_id'])->count();

  	if($check == 0) {
  		return CompanyStatementEclaimTransaction::create($data);
  	}
  }
}
