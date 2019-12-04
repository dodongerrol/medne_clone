<?php

class CorporateMembers extends Eloquent 
{

	protected $table = 'corporate_members';
  protected $guarded = ['corporate_member_id'];

  public function getActiveMembers($id)
  {
  	return CorporateMembers::where('corporate_id', $id)->where('removed_status', 0)->get();
  }
}
