<?php
use Carbon\Carbon;

class WalletHistory extends Eloquent  {

		protected $table = 'wallet_history';
    protected $guarded = ['wallet_history_id'];

    function createWalletHistory($data)
    {
        return WalletHistory::create($data);
    }

    function deleteFailedWalletHistory($id)
    {
    	return WalletHistory::where('wallet_history_id', $id)->delete();
    }
    
}
