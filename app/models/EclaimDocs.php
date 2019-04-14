<?php

class EclaimDocs extends Eloquent 
{

	protected $table = 'e_claim_docs';
  protected $guarded = ['e_claim_doc_id'];

  public function createEclaimDocs($data)
  {
  	return EclaimDocs::create($data);
  }
}
