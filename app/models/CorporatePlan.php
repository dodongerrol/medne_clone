<?php

class CorporatePlan extends Eloquent 
{

	protected $table = 'customer_plan';
    protected $guarded = ['customer_plan_id'];

    public function getCorporatePlan($id)
    {
    	return CorporatePlan::where('customer_buy_start_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertCorporatePlan($data)
    {
    	return CorporatePlan::create($data);
    }

    public function updateCorporatePlan($id, $amount)
    {
        return CorporatePlan::where('customer_buy_start_id', $id)->update(['discount' => $amount]);
    }

    public function updateCorporatePlanData($id, $data)
    {
        return CorporatePlan::where('customer_plan_id', $id)->update($data);
    }

    public function checkPlan($id)
    {
        $result = CorporatePlan::where('customer_buy_start_id', $id)->count();

        if($result > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function updateCorporatePlanChoose($id, $data)
    {
        return CorporatePlan::where('customer_buy_start_id', $id)->update($data);
    }
}
