<?php

class CorporateActivePlan extends Eloquent 
{

	protected $table = 'customer_active_plan';
    protected $guarded = ['customer_active_plan_id'];

    public function createCorporateActivePlan($data)
    {
    	$result = CorporateActivePlan::create($data);
        return $result->id;
    }

    public function updateCorporateActivePlanByCustomerStartId($data, $id)
    {
    	return CorporateActivePlan::where('customer_start_buy_id', $id)->update($data);
    }
    public function updateCorporateActivePlan($data, $id)
    {
    	return CorporateActivePlan::where('customer_active_plan_id', $id)->update($data);
    }

    public function getActivePlan($id)
    {
    	return CorporateActivePlan::where('customer_active_plan_id', $id)->first();
    }

    public function getActivePlanData($id)
    {
        return CorporateActivePlan::where('customer_start_buy_id', $id)->first();
    }

    public function checkActivePlan($id)
    {
        return CorporateActivePlan::where('customer_start_buy_id', $id)->count();
    }

    public function activateCarePlanUser( )
    {
        $date = date('Y-m-d');
        $result = DB::table('customer_active_plan')
                ->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_active_plan.customer_start_buy_id')
                ->where('customer_active_plan.plan_start', '=', $date)
                ->get();
        $temp = [];
        // return $result;
        foreach ($result as $key => $value) {
            if($value->individual == "true" && $value->cover_type == "individual") {
                $user_link = DB::table('customer_link_customer_buy')->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_link_customer_buy.customer_buy_start_id')->first();
                if($user_link) {
                    $user = DB::table('user')->where('UserID', $user_link->user_id)->where('Active', 0)->first();
                    // array_push($temp, $user);
                    if($user) {
                        $password = StringHelper::get_random_password(8);

                        // send email activation
                        $email['emailName'] = $user->Name;
                        $email['login_id'] = $user->Email;
                        $email['login_password'] = $password;
                        $email['emailTo']   = $user->Email;
                        $email['emailSubject'] = 'HOORAY! YOUR MEDNEFITS CARE ACCOUNT IS ACTIVATED!';
                        $email['emailPage']    = 'email-templates.individual-employee-activation';
                        $email['how'] = url('pdf/How Mednefits Works.pdf', $parameters = array(), $secure = null);
                        $email['coverage'] = url('pdf/Mednefits-u2019s Health Partners & Benefits (ind).pdf', $parameters = array(), $secure = null);
                        EmailHelper::sendEmail($email);
                        DB::table('user')->where('UserID', $user->UserID)->update(['Password' => md5($password), 'Active' => 1]);
                    }
                }
            }
        }

        return $temp;
    }

    public function getPlans($id)
    {
        return CorporateActivePlan::where('customer_start_buy_id', $id)->get(); 
    }
}
