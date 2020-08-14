<?php

class TransctionServices extends Eloquent 
{

	protected $table = 'transaction_services';
  protected $guarded = ['ts_id'];

  public function createTransctionServices($data, $id)
  {
  	foreach ($data as $key => $value) {
		if($value) {
			TransctionServices::create(['service_id' => $value, 'transaction_id' => $id]);
		}
  		
  	}

  	return TRUE;
  }
}
