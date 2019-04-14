<?php

use Carbon\Carbon;

class Wallet extends Eloquent {

	protected $table = 'e_wallet';
    protected $guarded = ['wallet_id'];

    function createWallet($data)
    {
        $check = Wallet::where('UserID', '=', $data['UserID'])->count();
        if($check == 0) {
            return Wallet::create($data);
        } else {
            return 0;
        }
    }

    function activateWallet($id)
    {
        $result = Wallet::where('UserID', '=', $id)->count();

        if($result > 0) {
            $res = Wallet::where('UserID', '=', $id)->update(['active' => 1]);
            if($res) {
                return "true";
            } else {
                return "false";
            }
        } else {
            return "false";
        }
    }

    function updateWallet($id, $credit)
    {
        return Wallet::where('UserID', '=', $id)->update(['balance' => $credit, 'updated_at' => Carbon::now()]);
    }
    function updateWalletByWalletId($id, $credit)
    {
        return Wallet::where('wallet_id', '=', $id)->update(['balance' => $credit, 'updated_at' => Carbon::now()]);
    }
    function UpdateAllWithWallet($id)
    {
        $find = Wallet::where('UserID', '=', $id)->count( );
        if($find == 0) {
            $values = array(
                'UserID'        => $id,
                'balance'       => "0",
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            );

            return Wallet::create($values);
        } else {
            return false;
        }
    }

    function updateWalletActive($id) {
        $update = array(
            'active' => 1, 
            'updated_at' => Carbon::now()
        );

        return Wallet::where('UserID', '=', $id)->update($update);
    }

    function getWalletId($id)
    {
        $wallet_id = Wallet::where('UserID', '=', $id)->orderBy('created_at', 'desc')->first();
        return $wallet_id->wallet_id;
    }

    function getUserWallet($id)
    {
        return Wallet::where('UserID', '=', $id)->orderBy('created_at', 'desc')->first();
    }

    function getWalletAmount($id)
    {
        return Wallet::where('wallet_id', '=', $id)->orderBy('created_at', 'desc')->first();
    }

    function updateUserWalletFromPromoCode($data, $userid)
    {   

        // return $data;
        $wallet_history = new WalletHistory( );
        $promo_code_history = new UserPromoCodeHistory( );

        $user_wallet = Wallet::where('UserID', '=', $userid)->first();
        // return $user_wallet;
        $new_data = (int) $data->amount + (int) $user_wallet->balance;
        $result = Wallet::where('UserID', '=', $userid)
                        ->update(['balance' => $new_data, 'updated_at' => Carbon::now() ]);
        if($result) {
            $data_new = array( 
                'wallet_id'         => $user_wallet->wallet_id,
                'credit'            => '+'.$data->amount,
                'logs'              => 'promo_code',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            );
            $wallet_history_result = $wallet_history->createWalletHistory($data_new);
            if($wallet_history_result) {
                $promo_code_data_history = array(
                    'promo_code_id'     => $data->promo_code_id,
                    'code'              => $data->code,
                    'amount'            => $data->amount,
                    'UserID'            => $userid,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now()
                );
                return $promo_code_history->createUserPromoCodeHistory($promo_code_data_history);
            }
        }
    }

    public function addCredits($id, $credits)
    {
        return Wallet::where('UserID', $id)->increment('balance', $credits);
    }

    public function addWellnessCredits($id, $credits)
    {
        return Wallet::where('UserID', $id)->increment('wellness_balance', $credits);
    }

    public function deductCredits($id, $credits)
    {
        return Wallet::where('UserID', $id)->decrement('balance', $credits);
    }

    public function deductWellnessCredits($id, $credits)
    {
        return Wallet::where('UserID', $id)->decrement('wellness_balance', $credits);
    }
}
