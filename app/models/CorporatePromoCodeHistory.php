<?php

class CorporatePromoCodeHistory extends Eloquent 
{

	protected $table = 'customer_promo_code_history';
    protected $guarded = ['customer_promo_code_history_id'];

    public function checkExistingPromoCode($id, $code)
    {
        return CorporatePromoCodeHistory::where('customer_buy_start_id', $id)
                                    ->where('code', $code)->count();
    }

    public function insertRecordPromo($id, $code)
    {
        return CorporatePromoCodeHistory::create(['customer_buy_start_id' => $id, 'code' => $code]);
    }
}
