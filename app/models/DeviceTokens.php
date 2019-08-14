<?php

class DeviceTokens extends Eloquent 
{

	protected $table = 'device_tokens';
  protected $guarded = ['device_token_id'];

  public function createOrUpdate($data)
  {
  	$data = array(
      'user_id' => $data['user_id'],
      'token'   => $data['token'],
      'platform'  => $data['platform']
    );

    $check_token = DeviceTokens::where('token', $data['token'])->first();
    if($check_token) {
      // update
      $result = DeviceTokens::where('token', $data['token'])->update($data);
    } else {
      // create
      $result = DeviceTokens::create($data);
    }

    if($result) {
      return true;
    } else {
      return false;
    }
  }
}
