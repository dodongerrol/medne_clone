<?php

class LocalNetworkPartners extends Eloquent 
{

	protected $table = 'local_network_partners';
  protected $guarded = ['local_network_partners_id'];

  public function createLocalNetworkPartners($data)
  {
  	return LocalNetworkPartners::create($data);
  }

}
