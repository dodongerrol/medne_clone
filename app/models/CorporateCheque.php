<?php

class CorporateCheque extends Eloquent 
{

	protected $table = 'customer_cheque';
    protected $guarded = ['customer_cheque_id'];

    public function getCorporateCheque($id)
    {
    	return CorporateCheque::where('customer_buy_start_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertCorporateCheque($data)
    {
    	return CorporateCheque::create($data);
    }

    public function checkCheque($id, $start_date)
    {
        return CorporateCheque::where('customer_buy_start_id', $id)->count();
    }

    public function updateCheque($id, $data)
    {
        return CorporateCheque::where('customer_buy_start_id', $id)->update($data);
    }


}
