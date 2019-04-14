<?php

class LocalNetwork extends Eloquent 
{

	protected $table = 'local_network';
  protected $guarded = ['local_network_id'];

  public function createLocalNetwork($data)
  {
  	return LocalNetwork::create(['local_network_name' => $data['name']]);
  }

}
