<?php

class CorporateCostCenter extends Eloquent 
{

    protected $table = 'cost_center';
    protected $guarded = ['cost_center_id'];

    public function getCostCenter($id)
    {
        return CorporateCostCenter::where('customer_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertCostCenter($data)
    {
        return CorporateCostCenter::create($data);
    }

    public function updateCostCenter($id, $data)
    {
        return CorporateCostCenter::where('cost_center_id', $id)->update($data);
    }
    
}
