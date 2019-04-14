<?php

class CorporateBuyStart extends Eloquent 
{

	protected $table = 'customer_buy_start';
    protected $guarded = ['customer_buy_start_id'];

    public function checkCorporateStart($id)
    {
    	return CorporateBuyStart::where('customer_buy_start_id', '=', $id)->where('status', 0)->first();
    }

    public function insertCarePlanBusiness($data)
    {
    	$result = CorporateBuyStart::create($data);
    	if($result) {
    		Session::put('customer_buy_start_id', $result->id);
    		return TRUE;
    	} else {
    		return FALSE;
    	}
    }

    public function updateCorporateBuyStart($id)
    {
        return CorporateBuyStart::where('customer_buy_start_id', $id)->update(['status' => 1]);
    }

    public function updateCorporateBuyStartData($data, $id)
    {
        return CorporateBuyStart::where('customer_buy_start_id', $id)->update($data);
    }

    public function getAccountStart($id)
    {
        return CorporateBuyStart::where('customer_buy_start_id', $id)->first();
    }

    public function updateAgreeStatus($id)
    {
        return CorporateBuyStart::where('customer_buy_start_id', $id)->update(['agree_status' => "true"]);
    }
    // public function updateCarePlanBusinessData($id, $data)
    // {
    //      return CorporateBuyStart::where('customer_buy_start_id', $id)->update($data);
    // }
}
