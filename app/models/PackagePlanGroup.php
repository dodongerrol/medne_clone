<?php

class PackagePlanGroup extends Eloquent 
{

	protected $table = 'package_group';
  protected $guarded = ['package_group_id'];

  public function getPackagePlanGroupDefault()
  {
  	$result = PackagePlanGroup::where('default_selection', 1)->select('package_group_id')->first();
  	return $result->package_group_id;
  }
}
