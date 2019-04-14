<?php

class CompanyCreditsStatement extends Eloquent 
{

	protected $table = 'company_credits_statement';
  protected $guarded = ['statement_id'];

  public function createCompanyCreditsStatement($data)
  {
  	return CompanyCreditsStatement::create($data);
  }

  public function addEclaimAmount($id, $amount)
  {
  	return CompanyCreditsStatement::where('statement_id', $id)->increment('statement_e_claim_amount', $amount);
  }
}
