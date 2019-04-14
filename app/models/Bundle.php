<?php

class Bundle extends Eloquent 
{

	protected $table = 'package_bundle';
  protected $guarded = ['bundle_id'];

  public function getBundle($id)
  {
  	return Bundle::where('package_group_id', $id)->get();
  }
}
