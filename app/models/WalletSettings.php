<?php

class WalletSettings extends Eloquent 
{

	protected $table = 'wallet_settings';
  protected $guarded = ['wallet_setting_id'];

  public function walletSetting($data, $user_id)
  {
  	// check if wallet settings already exist
  	$check = WalletSettings::where('user_id', $user_id)->orderBy('created_at')->count();
  	$wallet = array(
  			'user_id'					=> $user_id,
  			'bank_name'				=> $data['bank_name'],
  			'account_name'			=> $data['account_name'],
  			'account_number'	=> $data['account_number']
  		);
  	if($check == 0) {
  		// create
  		$wallet_result = WalletSettings::create($wallet);
  	} else {
  		// update
  		$details = WalletSettings::where('user_id', $user_id)->orderBy('created_at')->first();
  		$wallet_result = WalletSettings::where('wallet_setting_id', $details->wallet_setting_id)->update($wallet);
  	}

  	if($wallet_result) {
  		return TRUE;
  	} else {
  		return FALSE;
  	}
  }
}
