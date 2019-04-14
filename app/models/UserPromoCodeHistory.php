<?php


class UserPromoCodeHistory extends Eloquent {

	protected $table = 'user_promo_code_history';
    protected $fillable = ['promo_code_id', 'code', 'amount', 'UserID', 'created_at', 'updated_at'];


    function createUserPromoCodeHistory($data)
    {
        return UserPromoCodeHistory::create($data);
    }

    function checkUserPromoCodeExistence($code_id, $userid)
    {
        return UserPromoCodeHistory::where('UserID', '=', $userid)
                                    ->where('promo_code_id', '=', $code_id)->count();
        
    }

    function getPromoCodeTopUp( )
    {
        return DB::table('user')
                ->join('user_promo_code_history', 'user_promo_code_history.UserID', '=', 'user.UserID')
                ->join('e_wallet', 'e_wallet.UserID', '=', 'user.UserID')
                ->groupBy('user_promo_code_history.UserID')
                ->get();
    }
}
