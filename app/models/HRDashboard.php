<?php

class HRDashboard extends Eloquent 
{

	protected $table = 'customer_hr_dashboard';
    protected $guarded = ['hr_dashboard_id'];
    protected $hidden = ['password'];

    public function getHRDashboard($id)
    {
        return HRDashboard::where('customer_buy_start_id', $id)->first();
    }

    public function insertHRDashboard($data)
    {
        return HRDashboard::create($data);
    }

    public function checkEmail($email) 
    {
        return HRDashboard::where('email', $email)->count();
    }

    public function updateCorporateHrDashboard($id, $data)
    {
        return HRDashboard::where('hr_dashboard_id', $id)->update($data);
    }

    public function checkHR($id)
    {
        return HRDashboard::where('customer_buy_start_id', $id)->count();
    }

    public function updateHRDashboardData($data, $id)
    {
        return HRDashboard::where('customer_buy_start_id', $id)->update($data);
    }
    
}
