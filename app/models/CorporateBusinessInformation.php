<?php

class CorporateBusinessInformation extends Eloquent 
{

	protected $table = 'customer_business_information';
    protected $guarded = ['customer_business_information_id'];

    public function getCorporateBusinessInfo($id)
    {
    	return CorporateBusinessInformation::where('customer_buy_start_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertBusinessCorporateInfo($data)
    {
    	return CorporateBusinessInformation::create($data);
    }

    public function updateCorporateBusinessInformation($id, $data)
    {
        return CorporateBusinessInformation::where('customer_business_information_id', $id)->update($data);
    }

    public function createCorporateBusinessInformation($data) 
    {
        return CorporateBusinessInformation::create($data);
    }

    public function checkBusinessInfo($id)
    {
        $result = CorporateBusinessInformation::where('customer_buy_start_id', $id)->count();

        if($result > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function updateusinessInfo($id, $data)
    {
        return CorporateBusinessInformation::where('customer_buy_start_id', $id)->update($data);
    }
    
}
