<?php

class UserImageReceipt extends Eloquent 
{

	protected $table = 'user_image_receipt';
  protected $guarded = ['image_receipt_id'];

  public function saveReceipt($data)
  {
  	return UserImageReceipt::create($data);
  }
}
