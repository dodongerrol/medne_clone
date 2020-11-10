<?php
use Illuminate\Support\Facades\Input;

class CronController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		echo "index";
	}
        
        /* Use          :   Used to notify user on the day of session
         * Access       :   Cron
         * Parameter    :   
         */
        public function RemindAppointment(){
            CronLibrary::RemindAppointment();
            //Insurance_Library::RemindAppointment();
        }
        
        /* Use          :   Used to notify user 3 hours before the session
         * Access       :   Cron
         * Parameter    :
         */
        public function RemindAppointmentInHours(){
            CronLibrary::RemindAppointmentInHours();
            //Insurance_Library::RemindAppointmentInHours();
        }

        /* Use          :   Used to nitify the user 30 minutes before 
         * Access       :   Cron
         * Parameter    :   
         */
        public function RemindAppointmentInMinutes(){
            CronLibrary::RemindAppointmentInMinutes();
            //Insurance_Library::RemindAppointmentInMinutes();
        }
        /* Use          :   Used to diactivate all booing at night 11.59PM 
         * Access       :   Cron
         * Parameter    :   
         */
        public function DiactivateBookings(){
            CronLibrary::DiactivateBookings();
            //Insurance_Library::RemindAppointmentInMinutes();
        }

        /* Use          :   Used to notify user one day before the appointment 
         * Access       :   Cron
         * Parameter    :   
         * For          :   Direct SMS
         */
        public function SMSAppointmentBeforeDay(){
            CronLibrary::SMSAppointmentBeforeDay();
            //Insurance_Library::RemindAppointment();
        }

        /* Use          :   Used to notify user one hour before the appointment 
         * Access       :   Cron
         * Parameter    :   
         * For          :   Direct SMS
         */
        public function SMSAppointmentBeforeHour(){
            return CronLibrary::SMSAppointmentBeforeHour();
            //Insurance_Library::RemindAppointment();
        }
        

    // NHR 2016-3-25
// Delete past google events 
        public function deleteGoogleEvent()
        {
            return CronLibrary::deleteGoogleEvent();
        }    
        
        
        /* Use          :   Used to Inset diagnosis by doctor
     * Access       :   Ajax public
     * Parameter    :   
     */
    public function CronTest(){
        $emailDdata['emailName']= "Rizvi";
        $emailDdata['emailPage']= 'email-templates.test';
        $emailDdata['emailTo']= "nwnhemantha@gmail.com";
        $emailDdata['emailSubject'] = 'Email test by Cron';

        $emailDdata['name'] = "Raja";
        $emailDdata['email'] = "nwnhemantha@gmail.com";
        $emailDdata['activelink'] = "<p>Please click <a href='#'> This Link</a> to complete your registration</p>";
        EmailHelper::sendEmail($emailDdata);  
    }
    
    public function generateStatementOfAccount( )
    {   
        $payment = new PaymentRecord();
        $statement = new StatementOfAccount();
        $date = date("Y-m-d", strtotime( '-2 days' ) );
        $payment_record = $payment->getPaymentRecordByDate($date);
        $counter = 0;
        // return $payment_record;
        foreach ($payment_record as $key => $value) {
            $data = array(
                'generate_status'   => 1
            );
            $statement->updateStatementOfAccount($data, $value->payment_record_id);
            $counter++;
        }
        return $counter;
    }

    public function activateReplaceNewEmployee( )
    {
        $input = Input::all();

        if(!empty($input['date']) && $input['date'] != null) {
            $date = date('Y-m-d', strtotime($input['date']));
        } else {
            $date = date('Y-m-d', strtotime('-1 day'));
        }
        // return $date;
        $employees = 0;
        $dependent_accounts = 0;
        $replace_accounts = 0;
        $replacements = DB::table('customer_replace_employee')
                            ->where('start_date', '<=', $date)
                            ->where('replace_status', 0)
                            ->get();

        foreach ($replacements as $key => $replace) {
            $user_dat = DB::table('user')->where('UserID', $replace->old_id)->first();
                // set company members removed to 1
            if($user_dat) {
                MemberHelper::memberReturnCreditBalance($replace->old_id);
            }
            $active_plan = DB::table('customer_active_plan')
                            ->where('customer_active_plan_id', $replace->active_plan_id)
                            ->first();

            $id = $active_plan->customer_start_buy_id;
            $replace_id = $replace->old_id;
            $input = [];
            $input['first_name'] = $replace->first_name;
            $input['last_name'] = $replace->last_name;
            $input['email'] = $replace->email;
            $input['mobile'] = $replace->mobile;
            $input['nric'] = $replace->nric;
            $input['dob'] = $replace->dob;
            $input['postal_code'] = $replace->postal_code;
            $input['plan_start'] = $replace->start_date;
            // $input['last_day_coverage'] = 
            $medical = $replace->medical;
            $wellness = $replace->wellness;

            $result = PlanHelper::createReplacementEmployeeSchedule($replace_id, $input, $id, true, $medical, $wellness);
            $employees++;
            try {
                
                $admin_logs = array(
                    'admin_id'  => null,
                    'type'      => 'activate_replace_employee_schedule_system_generate',
                    'data'      => SystemLogLibrary::serializeData($replace)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
            } catch(Exception $e) {

            }
        }

        // employee deactivate account
        $employee_deactivates = DB::table('customer_replace_employee')
                            ->where('start_date', '<=', $date)
                            ->where('deactive_employee_status', 0)
                            ->get();

        foreach ($employee_deactivates as $key => $deactivate) {
            $replace_id = $deactivate->old_id;
            $active_plan = DB::table('customer_active_plan')
                            ->where('customer_active_plan_id', $deactivate->active_plan_id)
                            ->first();

            $user = DB::table('user')->where('UserID', $replace_id)->first();

            if((int)$user->Active == 1) {
                $user_data = array(
                    'Active'    => 0,
                    'updated_at' => date('Y-m-d')
                );
                // update user and set to inactive
                DB::table('user')->where('UserID', $replace_id)->update($user_data);
                // set company members removed to 1
                DB::table('corporate_members')->where('user_id',$replace_id)->update(['removed_status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

                $user_plan_history_data = array(
                    'user_id'       => $replace_id,
                    'type'          => "deleted_expired",
                    'date'          => date('Y-m-d', strtotime($deactivate->expired_and_activate)),
                    'customer_active_plan_id' => $active_plan->customer_active_plan_id
                );
                UserPlanHistory::create($user_plan_history_data);
                DB::table('customer_replace_employee')
                        ->where('customer_replace_employee_id', $deactivate->customer_replace_employee_id)
                        ->update(['deactive_employee_status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                PlanHelper::removeDependentAccountsReplace($replace_id, $deactivate->expired_and_activate);
            }
        }

        $employee_pending = DB::table('customer_replace_employee')
                            ->where('start_date', '<=', $date)
                            ->get();
       
        foreach ($employee_pending as $key => $pending) {
           $user = DB::table('user')->where('UserID', $pending->new_id)->first();

           if((int)$user->pending == 1) {
            // update pending to 0
             $user_data = array(
                'pending'    => 0,
                'updated_at' => date('Y-m-d')
            );
            // update user and set to inactive
            DB::table('user')->where('UserID', $pending->new_id)->update($user_data);
           }
        }

        // return $employee_pending;

        // dependents replacement
        $dependents = DB::table('customer_replace_dependent')
                        ->where('expired_date', $date)
                        ->where('deactivate_dependent_status', 0)
                        ->get();

        foreach ($dependents as $key => $dependent) {
            $replace_id = $dependent->old_id;
            $dependent_plan = DB::table('dependent_plan_history')
                                ->where('user_id', $dependent->old_id)
                                ->where('type', 'started')
                                ->orderBy('created_at', 'desc')
                                ->first();

            $depent_plan_history = new DependentPlanHistory();
            $user_plan_history_data = array(
                'user_id'       => $replace_id,
                'type'          => "deleted_expired",
                'plan_start'          => date('Y-m-d', strtotime($dependent->expired_date)),
                'dependent_plan_id' => $dependent_plan->dependent_plan_id,
                'duration'      => $dependent_plan->duration,
                'package_group_id' => $dependent_plan->package_group_id,
                'fixed'         => $dependent_plan->fixed
            );

            $result = $depent_plan_history->createData($user_plan_history_data);
            $user_data = array(
                'Active'    => 0
            );
            // update user and set to inactive
            DB::table('user')->where('UserID', $replace_id)->update($user_data);
            // set company members removed to 1
            DB::table('employee_family_coverage_sub_accounts')
            ->where('user_id', $replace_id)
            ->update(['deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
            DB::table('customer_replace_dependent')
                ->where('customer_replace_dependent_id', $dependent->customer_replace_dependent_id)
                ->update(['deactivate_dependent_status' => 1]);
            if((int)$dependent->replace_status == 1) {
                DB::table('customer_replace_dependent')
                ->where('customer_replace_dependent_id', $dependent->customer_replace_dependent_id)
                ->update(['status' => 1]);
            }

            $plan_tier = DB::table('plan_tier_users')
                            ->join('plan_tiers', 'plan_tiers.plan_tier_id', '=', 'plan_tier_users.plan_tier_id')
                            ->where('plan_tier_users.user_id', $dependent->old_id)
                            ->where('plan_tiers.active', 1)
                            ->first();
            if($plan_tier) {
                DB::table('plan_tier_users')
                                ->where('plan_tier_user_id', $plan_tier->plan_tier_user_id)
                                ->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
            }

            $dependent_accounts++;
            try {
                $admin_logs = array(
                    'admin_id'  => null,
                    'type'      => 'activate_remove_dependent_schedule_system_generate',
                    'data'      => SystemLogLibrary::serializeData($dependent)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
            } catch(Exception $e) {

            }
        }

        // dependents replace
        $dependent_replacement = DB::table('customer_replace_dependent')
                        ->where('start_date', $date)
                        ->where('replace_status', 0)
                        ->get();
        $plan_tier_user = new PlanTierUsers();

        foreach ($dependent_replacement as $key => $replace_account) {
            $dependent_plan = DB::table('dependent_plan_history')
                                ->where('user_id', $replace_account->old_id)
                                ->where('type', 'started')
                                ->orderBy('created_at', 'desc')
                                ->first();
            $plan_tier = DB::table('plan_tier_users')
                            ->join('plan_tiers', 'plan_tiers.plan_tier_id', '=', 'plan_tier_users.plan_tier_id')
                            ->where('plan_tier_users.user_id', $replace_account->old_id)
                            ->where('plan_tiers.active', 1)
                            ->first();
            $plan_tier_id = null;
            $user = array(
                'first_name'    => $replace_account->first_name,
                'last_name'     => $replace_account->last_name,
                'nric'          => $replace_account->nric,
                'dob'           => date('Y-m-d', strtotime($replace_account->dob))
            );

            $user_id = PlanHelper::createDependentAccountUser($user);
            if($user_id)
            {
                $owner_id = PlanHelper::getDependentOwnerID($replace_account->old_id);
                $family = array(
                    'owner_id'      => $owner_id,
                    'user_id'       => $user_id,
                    'user_type'     => 'dependent',
                    'relationship'  => $replace_account->relationship,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                );
                $family_result = DB::table('employee_family_coverage_sub_accounts')->insert($family);
                if($family_result) {
                    $history = array(
                        'user_id'           => $user_id,
                        'dependent_plan_id' => $dependent_plan->dependent_plan_id,
                        'package_group_id'  => $dependent_plan->package_group_id,
                        'plan_start'        => date('Y-m-d', strtotime($replace_account->start_date)),
                        'duration'          => '12 months',
                        'fixed'             => 1,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s')
                    );
                    DB::table('dependent_plan_history')->insert($history);
                    if($plan_tier) {
                        $plan_tier_id = $plan_tier->plan_tier_id;
                        // update replace plan tier id
                        DB::table('plan_tier_users')
                            ->where('plan_tier_user_id', $plan_tier->plan_tier_user_id)
                            ->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
                        // create new plan tier user
                        $plan_tier_user_data = array(
                            'plan_tier_id'  => $plan_tier_id,
                            'user_id'       => $user_id,
                            'status'        => 1,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s')
                        );
                        $plan_tier_user->createData($plan_tier_user_data);
                    }

                    DB::table('customer_replace_dependent')
                        ->where('customer_replace_dependent_id', $replace_account->customer_replace_dependent_id)
                        ->update(['replace_status' => 1, 'new_id' => $user_id]);
                    if((int)$replace_account->deactivate_dependent_status == 1) {
                        DB::table('customer_replace_dependent')
                        ->where('customer_replace_dependent_id', $replace_account->customer_replace_dependent_id)
                        ->update(['status' => 1]);
                    }
                    $replace_accounts++;
                    try {
                        $admin_logs = array(
                            'admin_id'  => null,
                            'type'      => 'activate_replace_dependent_schedule_system_generate',
                            'data'      => SystemLogLibrary::serializeData($replace_account)
                        );
                        SystemLogLibrary::createAdminLog($admin_logs);
                    } catch(Exception $e) {

                    }
                }
            }

        }

        return array('status' => true, 'message' => 'Success', 'employees' => $employees, 'dependent_accounts' => $dependent_accounts, 'replace_accounts' => $replace_accounts);
    }
    
    public function activateRemoveReplaceEmployee( )
    {
       $input = Input::all();
       $date = date('Y-m-d', strtotime('-1 day'));

       if(!empty($input['date']) && $input['date'] != null) {
        $removes = DB::table('customer_replace_employee')
                                ->where('expired_and_activate', $input['date'] )
                                ->where('deactive_employee_status', 0)
                                ->get();
       } else {
            $removes = DB::table('customer_replace_employee')
                                ->where('expired_and_activate', $date)
                                ->where('deactive_employee_status', 0)
                                ->get();
       }

        $replace = new CustomerReplaceEmployee( );

        foreach ($removes as $key => $remove) {
            $active_plan = DB::table('customer_active_plan')
                            ->where('customer_active_plan_id', $remove->active_plan_id)
                            ->first();
            $id = $active_plan->customer_start_buy_id;
            // $user = DB::table('user')->where('UserID', $replace_id)->first();
            $user_plan_history = DB::table('user_plan_history')->where('user_id', $remove->old_id)->orderBy('created_at', 'desc')->first();

            if(!$user_plan_history) {
                $active_plan = DB::table('customer_active_plan')
                            ->where('customer_start_buy_id', $id)
                            ->first();
            } else {
                $active_plan = DB::table('customer_active_plan')
                                ->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
                                ->first();
            }

            $user_data = array(
                'Active'    => 0,
                'updated_at' => date('Y-m-d')
            );
            // update user and set to inactive
            DB::table('user')->where('UserID', $remove->old_id)->update($user_data);
            // set company members removed to 1
            DB::table('corporate_members')->where('user_id', $remove->old_id)->update(['removed_status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

            $user_plan_history = new UserPlanHistory();
            $user_plan_history_data = array(
                'user_id'       => $remove->old_id,
                'type'          => "deleted_expired",
                'date'          => date('Y-m-d', strtotime($remove->expired_and_activate)),
                'customer_active_plan_id' => $active_plan->customer_active_plan_id
            );
            $user_plan_history->createUserPlanHistory($user_plan_history_data);

            $replace_data = array(
                'deactive_employee_status'        => 1
            );              
            $replace->updateCustomerReplace($remove->customer_replace_employee_id, $replace_data);
            $wallets = MemberHelper::memberReturnCreditBalance($remove->old_id);

            PlanHelper::removeDependentAccounts($remove->old_id, $remove->expired_and_activate, false, true);
            try {
                $admin_logs = array(
                    'admin_id'  => null,
                    'type'      => 'activate_remove_employee_schedule_system_generate',
                    'data'      => SystemLogLibrary::serializeData($remove)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
            } catch(Exception $e) {

            }
        }
        
        return array('status' => true, 'message' => 'Success');   
    }


    public function removeEmployeeSeat( )
    {
        $date = date('Y-m-d', strtotime('-1 day'));
        $success = 0;
        $dependent_success = 0;

        $employee_seats = DB::table('employee_replacement_seat')
                            ->where('remove_status', 0)
                            ->where('last_date_of_coverage', $date)
                            ->get();

        foreach ($employee_seats as $key => $seat) {
            $user_plan_history = DB::table('user_plan_history')
                                ->where('user_id', $seat->user_id)
                                ->orderBy('created_at', 'desc')
                                ->first();

 
            $active_plan = DB::table('customer_active_plan')
                                ->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
                                ->first();

            $user_data = array(
                'Active'    => 0,
                'updated_at' => date('Y-m-d')
            );
            // update user and set to inactive
            DB::table('user')->where('UserID', $seat->user_id)->update($user_data);
            // set company members removed to 1
            DB::table('corporate_members')->where('user_id', $seat->user_id)->update(['removed_status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

            $user_plan_history = new UserPlanHistory();
            $user_plan_history_data = array(
                'user_id'       => $seat->user_id,
                'type'          => "deleted_expired",
                'date'          => date('Y-m-d', strtotime($seat->last_date_of_coverage)),
                'customer_active_plan_id' => $active_plan->customer_active_plan_id
            );
            $user_plan_history->createUserPlanHistory($user_plan_history_data);

            DB::table('employee_replacement_seat')
                ->where('employee_replacement_seat_id', $seat->employee_replacement_seat_id)
                ->update(['updated_at' => date('Y-m-d H:i:s'), 'remove_status' => 1]);
            PlanHelper::removeDependentAccounts($seat->user_id, $seat->last_date_of_coverage);
            $success++;
            try {
                $admin_logs = array(
                    'admin_id'  => null,
                    'type'      => 'replace_employee_seat_schedule_system_generate',
                    'data'      => SystemLogLibrary::serializeData($seat)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
            } catch(Exception $e) {

            }
        }

        $dependent_seats = DB::table('dependent_replacement_seat')
                            ->where('remove_status', 0)
                            ->where('last_date_of_coverage', $date)
                            ->get();

        foreach ($dependent_seats as $key => $seat) {
            $dependent_plan = DB::table('dependent_plan_history')
                                ->where('user_id', $seat->user_id)
                                ->where('type', 'started')
                                ->orderBy('created_at', 'desc')
                                ->first();

            $depent_plan_history = new DependentPlanHistory();
            $user_plan_history_data = array(
                'user_id'       => $seat->user_id,
                'type'          => "deleted_expired",
                'plan_start'          => date('Y-m-d', strtotime($seat->last_date_of_coverage)),
                'dependent_plan_id' => $dependent_plan->dependent_plan_id,
                'duration'      => $dependent_plan->duration,
                'package_group_id' => $dependent_plan->package_group_id,
                'fixed'         => $dependent_plan->fixed
            );

            $result = $depent_plan_history->createData($user_plan_history_data);
            $user_data = array(
                'Active'    => 0
            );
            // update user and set to inactive
            DB::table('user')->where('UserID', $seat->user_id)->update($user_data);
            // set company members removed to 1
            DB::table('employee_family_coverage_sub_accounts')
                ->where('user_id', $seat->user_id)
                ->update(['deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
            DB::table('dependent_replacement_seat')
                ->where('dependent_replacement_seat_id', $seat->dependent_replacement_seat_id)
                ->update(['remove_status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
            $plan_tier = DB::table('plan_tier_users')
                            ->join('plan_tiers', 'plan_tiers.plan_tier_id', '=', 'plan_tier_users.plan_tier_id')
                            ->where('plan_tier_users.user_id', $seat->user_id)
                            ->where('plan_tiers.active', 1)
                            ->first();
            if($plan_tier) {
                DB::table('plan_tier_users')
                    ->where('plan_tier_user_id', $plan_tier->plan_tier_user_id)
                    ->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
                DB::table('plan_tiers')
                    ->where('plan_tier_id', $plan_tier->plan_tier_id)
                    ->decrement('dependent_enrolled_count', 1);
            }

            $dependent_success++;
            try {
                $admin_logs = array(
                    'admin_id'  => null,
                    'type'      => 'replace_dependent_seat_schedule_system_generate',
                    'data'      => SystemLogLibrary::serializeData($seat)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
            } catch(Exception $e) {

            }

        }

        return array('status' => true, 'employee' => $success, 'dependent' => $dependent_success);
    }

    public function createAutomaticDeletion( )
    {
        $input = Input::all();

        if(!empty($input['date']) && $input['date'] != null) {
            $date = date('Y-m-d', strtotime($input['date']));
        } else {
            $date = date('Y-m-d', strtotime('-1 day'));
        }
        
        $employee = 0;
        $dependents = 0;
        $withdraw = DB::table('customer_plan_withdraw')->where('date_withdraw', '<=', $date)->where('refund_status', 0)->get();
        $user_plan_history = new UserPlanHistory();
        

        foreach ($withdraw as $key => $user) {
            if((int)$user->has_no_user == 0) {
                $active_plan = DB::table('user_plan_history')->where('user_id', $user->user_id)->orderBy('date', 'desc')->first();

                // $check_history = DB::table('user_plan_history')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->where('type', 'deleted_expired')->first();

                // if(!$check_history) {
                    $user_plan_history_data = array(
                        'user_id'       => $user->user_id,
                        'type'          => "deleted_expired",
                        'date'          => $user->date_withdraw,
                        'customer_active_plan_id' => $active_plan->customer_active_plan_id
                    );

                    $user_plan_history->createUserPlanHistory($user_plan_history_data);
                // }

                $user_dat = DB::table('user')->where('UserID', $user->user_id)->first();
                // set company members removed to 1
                DB::table('corporate_members')->where('user_id', $user_dat->UserID)->update(['removed_status' => 1]);
                if($user_dat->Active == 1) {
                    $user_data = array(
                        'Active'    => 0
                    );
                    // update user and set to inactive
                    DB::table('user')->where('UserID', $user_dat->UserID)->update($user_data);
                }
                $refund_status = false;
                $keep_seat = false;

                if((int)$user->refund_status == 0) {
                    $refund_status = true;
                }

                \PlanWithdraw::where('user_id', $user->user_id)->update(['refund_status' => 1]);
                // update customer plan draw to 1
                if((int)$user->keep_seat == 0) {
                    PlanHelper::updateNewCustomerPlanStatusDeleteUser($user->user_id, $refund_status);
                } else if((int)$user->vacate_seat == 1) {
                    PlanHelper::updateCustomerPlanStatusDeleteUserVacantSeat($user->user_id);
                    $keep_seat = true;
                }
                PlanHelper::removeDependentAccounts($user->user_id, $user->date_withdraw, $refund_status, $keep_seat);
                $employee++;

                try {
                    $admin_logs = array(
                        'admin_id'  => null,
                        'type'      => 'removed_employee_schedule_system_generate',
                        'data'      => SystemLogLibrary::serializeData($user)
                    );
                    SystemLogLibrary::createAdminLog($admin_logs);
                } catch(Exception $e) {

                }
            }
        }

        // remove refunded = 2
        if(!empty($input['member_id']) && $input['member_id'] != null) {
            $removes = DB::table('customer_plan_withdraw')->where('user_id', $input['member_id'])->where('date_withdraw', '<=', $date)->where('refund_status', 2)->get();
        } else {
            $removes = DB::table('customer_plan_withdraw')->where('date_withdraw', '<=', $date)->where('refund_status', 2)->get();
        }
        
        foreach ($removes as $key => $removed_employee) {
            if((int)$removed_employee->has_no_user == 0) {
                $user_dat = DB::table('user')->where('UserID', $removed_employee->user_id)->first();
                // set company members removed to 1
                if($user_dat) {
                    $plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->first();
                    if($plan->account_type == "lite_plan") {
                        MemberHelper::memberReturnCreditBalance($removed_employee->user_id);
                    }
                }

                if($user_dat && $user_dat->Active == 1) {
                    $active_plan = DB::table('user_plan_history')->where('user_id', $removed_employee->user_id)->orderBy('date', 'desc')->first();
                    $user_plan_history_data = array(
                        'user_id'       => $removed_employee->user_id,
                        'type'          => "deleted_expired",
                        'date'          => $removed_employee->date_withdraw,
                        'customer_active_plan_id' => $active_plan->customer_active_plan_id
                    );

                    $user_plan_history->createUserPlanHistory($user_plan_history_data);
                    DB::table('corporate_members')->where('user_id', $user_dat->UserID)->update(['removed_status' => 1]);
                    $user_data = array(
                        'Active'    => 0
                    );
                    // update user and set to inactive
                    DB::table('user')->where('UserID', $user_dat->UserID)->update($user_data);
                    if((int)$removed_employee->vacate_seat == 1) {
                        PlanHelper::updateCustomerPlanStatusDeleteUserVacantSeat($removed_employee->user_id);
                    }
                }

                try {
                    PlanHelper::removeDependentAccounts($removed_employee->user_id, $removed_employee->date_withdraw, true, true);
                } catch(Exception $e) {
                    // return $e->getMessage();
                }
            }
        }
      
        // remove dependent status = 0
        $dependent_withdraws = DB::table('dependent_plan_withdraw')
                                ->where('date_withdraw', '<=', $date)
                                ->where('status', 0)
                                ->get();
        $dependent_plan_withdraw_class = new DependentPlanWithdraw();
        foreach ($dependent_withdraws as $key => $dependent) {
            if((int)$dependent->has_no_user == 0) {
                $user_data = array(
                    'Active'    => 0
                );
                // update user and set to inactive
                DB::table('user')->where('UserID', $dependent->user_id)->update($user_data);
                // set company members removed to 1
                DB::table('employee_family_coverage_sub_accounts')
                ->where('user_id', $dependent->user_id)
                ->update(['deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
                PlanHelper::updateCustomerDependentPlanStatusDeleteUser($dependent->user_id);
                // check if dependent has plan tier
                $plan_tier_user = DB::table('plan_tier_users')
                                    ->where('user_id', $dependent->user_id)
                                    ->first();
                if($plan_tier_user) {
                    // update status
                    DB::table('plan_tier_users')
                                    ->where('user_id', $dependent->user_id)
                                    ->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
                    // update plan tier dependent count
                    $tier = new PlanTier();
                    $tier->decrementDependentEnrolledHeadCount($plan_tier_user->plan_tier_id);
                }
                $dependent_plan_withdraw_class->updateDependentPlanWithdraw($dependent->dependent_plan_withdraw_id, array('status' => 1));
                $dependents++;

                try {
                    $admin_logs = array(
                        'admin_id'  => null,
                        'type'      => 'removed_dependent_schedule_system_generate',
                        'data'      => SystemLogLibrary::serializeData($dependent)
                    );
                    SystemLogLibrary::createAdminLog($admin_logs);
                } catch(Exception $e) {

                }
            }
        }


        // remove dependent status = 2
        $dependent_no_refund_withdraws = DB::table('dependent_plan_withdraw')
                                ->where('date_withdraw', '<=', $date)
                                ->where('status', 2)
                                ->get();

        foreach ($dependent_no_refund_withdraws as $key => $dependent) {
            if((int)$dependent->has_no_user == 0) {
                $user = DB::table('user')->where('UserID', $dependent->user_id)->first();

                if($user && (int)$user->Active == 1) {
                    $user_data = array(
                        'Active'    => 0
                    );
                    // update user and set to inactive
                    DB::table('user')->where('UserID', $dependent->user_id)->update($user_data);
                    // set company members removed to 1
                    DB::table('employee_family_coverage_sub_accounts')
                    ->where('user_id', $dependent->user_id)
                    ->update(['deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
                    PlanHelper::updateCustomerDependentPlanStatusDeleteUser($dependent->user_id);
                    // check if dependent has plan tier
                    $plan_tier_user = DB::table('plan_tier_users')
                                        ->where('user_id', $dependent->user_id)
                                        ->first();
                    if($plan_tier_user) {
                        // update status
                        DB::table('plan_tier_users')
                                        ->where('user_id', $dependent->user_id)
                                        ->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
                        // update plan tier dependent count
                        $tier = new PlanTier();
                        $tier->decrementDependentEnrolledHeadCount($plan_tier_user->plan_tier_id);
                    }

                    if((int)$dependent->vacate_seat == 1) {
                        PlanHelper::updateCustomerDependentPlanStatusDeleteUserVacantSeat($dependent->user_id);
                    } else {
                        PlanHelper::updateCustomerDependentPlanStatusDeleteUser($dependent->user_id);
                    }

                    // $dependent_plan_withdraw_class->updateDependentPlanWithdraw($dependent->dependent_plan_withdraw_id, array('status' => 1));
                    $dependents++;
                }

            }
        }

        return array('status' => true, 'employee' => $employee, 'dependent' => $dependents);
    }

    public function removeDepdentsEmployeeAccounts( )
    {
        $date = date('Y-m-d');

        $withdraws = DB::table('customer_plan_withdraw')
                        ->where('date_withdraw', '<', $date)
                        // ->where('refund_status', 0)
                        ->get();

        $format = 0;

        foreach ($withdraws as $key => $employee) {
            $type = null;
            // $dependents = DB::table('employee_family_coverage_sub_accounts')
                            // ->where('deleted', 0)
                            // ->get();

            if((int)$employee->refund_status == 1) {
                $type = "refund";
                PlanHelper::removeDependentAccounts($employee->user_id, $employee->date_withdraw, true, false);
                // foreach ($dependents as $key => $dependent) {
                // }
            } else {
                $type = "no_refund";
                PlanHelper::removeDependentAccounts($employee->user_id, $employee->date_withdraw, false, true);
            }


            // $temp = array(
            //     'user_id' => $employee->user_id,
            //     'dependents'    => $dependents
            // );

            // array_push($format, $temp);
            $format++;
        }

        return $format;
    }


                
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	

}
