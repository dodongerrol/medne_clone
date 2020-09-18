<?php

class CorporateHrLocation extends Eloquent 
{

    protected $table = 'company_locations';
    protected $guarded = ['LocationID'];

    public function getHrLocations($id)
    {
        return CorporateHrLocation::where('customer_id', $id)->orderBy('created_at','desc')->first();
    }

    public function insertCorporateHrLocations($data)
    {
        return CorporateHrLocation::create($data);
    }

    public function updateCorporateHrLocations($id, $data)
    {
        return CorporateHrLocation::where('LocationID', $id)->update($data);
    }
    
}
