<?php

use Carbon\Carbon;

class NewPromoCode extends Eloquent {


	protected $table = 'promocode';
    protected $fillable = ['code', 'amount', 'active', 'created_at', 'updated_at'];


    function createPromoCode($data)
    {
        // return $data['code'];
        $result = NewPromoCode::where('code', '=', $data['code'])->count();
        if($result > 0)  {
            return 2;
        } else {
            $temp = NewPromoCode::create($data);
            return 1;
        }
    }

    function updatePromo($data, $id)
    {
        $result = NewPromoCode::where('promo_code_id', '=', $id)->update($data);
        if($result == 1) {
            return 3;
        }
    }

    function getPromoCode( )
    {
        return DB::table('promocode')->get();
    }

    function matchCode($code)
    {
        return NewPromoCode::where('code', $code)->where('active', 1)->first();
    }

    function matchPromoCode($data, $id)
    {   
        $wallet = new Wallet( );
        $returnObject = new stdClass();
        $promocode_history = new UserPromoCodeHistory( );
        $result = NewPromoCode::where('code', '=', $data['code'])
                                ->where('active', '=', 1)
                                ->count();

        if($result > 0) {


            $data_new = NewPromoCode::where('code', '=', $data['code'])
                                ->where('active', '=', 1)
                                ->first();
            $check = $promocode_history->checkUserPromoCodeExistence($data_new->promo_code_id, $id);

            if($check == 1) {
                $res['status'] = FALSE;
                $res['data']['status'] = 0;
                $res['data']['message'] = "Already got the promo code";
                $returnObject = $res;
                return $returnObject;
            } else {
                $result_new = $wallet->updateUserWalletFromPromoCode($data_new, $id);
                if($result_new)
                {
                    $res['status'] = TRUE;
                    $res['data']['status'] = 1;
                    $res['data']['message'] = "Promo Matched";
                    $returnObject = $res;
                    return $returnObject;
                } else {
                    $res['status'] = FALSE;
                    $res['data']['status'] = 0;
                    $res['data']['message'] = "Promo code does not exist";
                    $returnObject = $res;
                    return $returnObject;
                }
            }
        } else {
            $res['status'] = FALSE;
            $res['data']['status'] = 0;
            $res['data']['message'] = "Promo code does not exist";
            $returnObject = $res;
            return $returnObject;
        }
    }

    function removePromoCode($id)
    {
        return NewPromoCode::where('promo_code_id', '=', $id)->delete();
    }
}
