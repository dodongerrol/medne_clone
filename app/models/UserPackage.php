<?php
use Illuminate\Support\Facades\Input;

class UserPackage extends Eloquent
{

	protected $table = 'user_package';
    protected $guarded = ['user_package_id'];

    public function getEcardDetails($id)
    {
        $user = new User();
        $plan = new UserPlanType();
        $data = [];
        $data['packages'] = DB::table('user_package')
                    ->join('care_package', 'care_package.care_package_id', '=', 'user_package.care_package_id')
                    ->where('user_package.user_id', $id)
                    ->get();

        if(sizeof($data['packages']) == 0) {
            return 0;
        }

        $user_details = $user->getUserProfileMobile($id);
        $company = DB::table('corporate_members')
                    ->join('corporate', 'corporate.corporate_id', '=',  'corporate_members.corporate_id')
                    ->where('corporate_members.user_id', '=', $id)
                    ->first();
        if($company && $user_details->UserType == 5 && $user_details->access_type == 0) {
            $data['company_name']  = ucwords($company->company_name);
        } elseif ($user_details->UserType == 5 && $user_details->access_type == 1) {
            $data['company_name'] = 'Self-Employed';
        } elseif ($user_details->UserType == 5 && $user_details->access_type == 2) {
           $data['company_name'] = 'N/A';
        }

        $user_plan_type = $plan->getUserPlan($id);

        if(date('Y-m-d', strtotime($user_plan_type->created_at)) > date('Y-m-d')) {
            return -1;
        }

        if(empty($user_plan_type->duration)) {
            $data['start_date'] = date('d F Y', strtotime($user_plan_type->created_at));
            $data['valid_date'] = date('d F Y', strtotime('+1 year', strtotime($user_plan_type->created_at)));
        } else {
            $data['start_date'] = date('d F Y', strtotime($user_plan_type->created_at));
            $data['valid_date'] = date('d F Y', strtotime('+'.$user_plan_type->duration, strtotime($user_plan_type->created_at)));
        }

        if($user_details->UserType == 5 && $user_details->access_type == 0) {
            $plan_type = 'Corporate';
        } else if($user_details->UserType == 5 && $user_details->access_type == 1) {
            $plan_type = 'Individual';
        } else {
            $plan_type = 'N/A';
        }
        $data['fullname'] = $user_details->Name;
        $data['member_id'] = $user_details->UserID;
        $data['nric'] = $user_details->NRIC;
        $data['plan_type'] = $plan_type;
        return $data;
    }

    public function createUserPackage($care_package_id, $id)
    {
        UserPackage::create(['care_package_id' => $care_package_id, 'user_id' => $id, 'plan_type' => 'individual']);
    }

    public function newEcardDetails($id)
    {
        $user = new User();
        $plan = new UserPlanType();
        $data = [];
        $dependent_user = false;
        $type = StringHelper::checkUserType($id);
        $input = Input::all();
        $lang = isset($input['lang']) ? $input['lang'] : "en";

        if((int)$type['user_type'] == 5 && (int)$type['access_type'] == 0 || (int)$type['user_type'] == 5 && (int)$type['access_type'] == 1)
        {
            $owner_id = $id;
            $user_id = $id;
        } else {
            // find owner
            $owner = DB::table('employee_family_coverage_sub_accounts')
                        ->where('user_id', $id)
                        ->first();
            $owner_id = $owner->owner_id;
            $user_id = $id;
            $dependent_user = true;
        }
        
        $corporate_member = DB::table('corporate_members')->where('user_id', $owner_id)->first();
        $data['plan_add_on'] = "NIL";
        $data['dependent_user'] = $dependent_user;
        $cap_per_visit = $lang == "malay" ?  \MalayTranslation::extraTextTranslate("Not Applicable") : "Not Applicable";
        if($corporate_member) {
            $user_details = $user->getUserProfileMobile($user_id);
            $company = DB::table('corporate')
                    ->where('corporate_id', '=', $corporate_member->corporate_id)
                    ->first();
            $wallet = DB::table('e_wallet')->where('UserID', $owner_id)->orderBy('created_at', 'desc')->first();

            if($company) {
                if($company && $user_details->UserType == 5 && $user_details->access_type == 0) {
                    $data['company_name']  = ucwords($company->company_name);
                } elseif ($user_details->UserType == 5 && $user_details->access_type == 1) {
                    $data['company_name'] = 'Self-Employed';
                } elseif ($user_details->UserType == 5 && $user_details->access_type == 2) {
                   $data['company_name'] = 'N/A';
                }

                $customer_id = PlanHelper::getCustomerId($owner_id);
                if($customer_id) {
                    if($dependent_user) {
                        $data['company_name']  = ucwords($company->company_name);
                        // get dependents plan history
                        $dependent_plan_history = DB::table('dependent_plan_history')
                                                    ->where('user_id', $user_id)
                                                    ->where('type', 'started')
                                                    ->orderBy('created_at', 'desc')
                                                    ->first();
                        if(!$dependent_plan_history) {
                            $new_data_history = DependentHelper::createDependentPlanHistory($owner_id, $user_id);
                            // create plan history
                            $plan_history = array(
                                'user_id'               => $user_id,
                                'dependent_plan_id'     => $new_data_history->dependent_plan_id,
                                'package_group_id'      => $new_data_history->package_group_id,
                                'plan_start'            => $new_data_history->plan_start,
                                'duration'              => $new_data_history->duration,
                                'type'                  => $new_data_history->type,
                                'fixed'                 => $new_data_history->fixed,
                                'created_at'            => date('Y-m-d H:i:s'),
                                'updated_at'            => date('Y-m-d H:i:s')
                            );
                            DB::table('dependent_plan_history')->insert($plan_history);
                            $dependent_plan_history = DB::table('dependent_plan_history')
                                                    ->where('user_id', $user_id)
                                                    ->orderBy('created_at', 'desc')
                                                    ->first();
                        }

                        $dependent_plan = DB::table('dependent_plans')->where('dependent_plan_id', $dependent_plan_history->dependent_plan_id)->first();
                        // return array('res' => $dependent_plan);

                        $plan = DB::table('customer_plan')->where('customer_plan_id', $dependent_plan->customer_plan_id)->orderBy('created_at', 'desc')->first();

                        $active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
                        $data['start_date'] = date('d F Y', strtotime($dependent_plan_history->plan_start));
                        

                        $dependent_plan_extenstion = DB::table('dependent_plans')->where('customer_plan_id', $dependent_plan->customer_plan_id)->where('type', 'extension_plan')->first();

                        if($dependent_plan_extenstion && $dependent_plan_extenstion->activate_plan_extension == 1) {
                            $temp_valid_date = date('d F Y', strtotime('+'.$dependent_plan_extenstion->duration, strtotime($dependent_plan_extenstion->plan_start)));
                            $data['valid_date'] = date('d F Y', strtotime('-1 day', strtotime($temp_valid_date)));
                        } else {
                            if((int)$dependent_plan_history->fixed == 1) {
                                $temp_valid_date = date('d F Y', strtotime('+'.$active_plan->duration, strtotime($plan->plan_start)));
                                $data['valid_date'] = date('d F Y', strtotime('-1 day', strtotime($temp_valid_date)));
                            } else if((int)$dependent_plan_history->fixed == 0) {
                                $data['valid_date'] = date('d F Y', strtotime('+'.$plan_user->duration, strtotime($dependent_plan_history->plan_start)));
                            }
                        }

                        $credit_data = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $owner_id);
                        $data['fullname'] = ucwords($user_details->Name);
                        $data['member_id'] = $user_details->UserID;
                        $data['nric'] = $user_details->NRIC;
                        $data['employee_id'] = $user_details->emp_no;
                        $data['dob'] = date('d/m/Y', strtotime($user_details->DOB));
                        $data['plan_type'] = PlanHelper::getDependentPlanType($dependent_plan_history->dependent_plan_id);
                        $data['care_online'] = TRUE;
                        $data['packages'] = PlanHelper::getDependentsPackages($dependent_plan_history->dependent_plan_id, $dependent_plan_history, $owner_id);
                        $data['plan_add_on'] = PlanHelper::getCompanyAccountType($owner_id);
                        $data['mobile'] = null;

                        if($data['plan_type'] == "Enterprise Plan") {
                            $data['plan_add_on'] = "N.A.";
                        }

                        $wallet_entitlement = DB::table('employee_wallet_entitlement')
                        ->where('member_id', $owner_id)
                        ->orderBy('created_at', 'desc')
                        ->first();
                        $data['account_type'] = $active_plan->account_type;
                        $data['account_status'] = MemberHelper::getMemberWalletStatus($user_details->UserID, 'medical');
                        if($data['plan_type'] == "Enterprise Plan") {
                            $data['plan_add_on'] = "N.A.";
                            $data['annual_entitlement'] = 14;
                        } else if($data['plan_type'] == "Out of Pocket") {
                            $data['plan_add_on'] = "N.A.";
                            $data['annual_entitlement'] = 'Not applicable';
                        } else {
                            $data['annual_entitlement'] = strtoupper($wallet->currency_type).' '.number_format($credit_data['allocation'], 2);
                        }
                        // get cap per visit
                        // check if their is a plan tier
                        $plan_tier = DB::table('plan_tier_users')
                        ->join('plan_tiers', 'plan_tiers.plan_tier_id', '=', 'plan_tier_users.plan_tier_id')
                        ->where('plan_tier_users.user_id', $user_id)
                        ->first();
                        // $cap_per_visit = $wallet->cap_per_visit_medical;

                        if($plan_tier) {
                            if($wallet->cap_per_visit_medical > 0) {
                                $cap_per_visit = strtoupper($wallet->currency_type)." ".number_format($wallet->cap_per_visit_medical, 2);
                            } else {
                                $cap_per_visit = strtoupper($wallet->currency_type)." ".number_format($plan_tier->gp_cap_per_visit, 2);
                            }
                        } else {
                            if($wallet->cap_per_visit_medical > 0) {
                                $cap_per_visit = "S$ ".number_format($wallet->cap_per_visit_medical, 2);
                            }
                        }

                        $data['cap_per_visit'] = $cap_per_visit;
                        
                        return $data;
                    } else {
                        $plan_user_history = DB::table('user_plan_history')
                                        ->where('user_id', $id)
                                        ->where('type', 'started')
                                        ->orderBy('created_at', 'desc')
                                        ->first();

                        if(!$plan_user_history) {
                            // create plan user history
                            PlanHelper::createUserPlanHistory($id, $customer_id);
                            $plan_user_history = DB::table('user_plan_history')
                                            ->where('user_id', $id)
                                            ->where('type', 'started')
                                            ->orderBy('created_at', 'desc')
                                            ->first();
                        }

                        $plan_user = DB::table('user_plan_type')->where('user_id', $id)->orderBy('created_at', 'desc')->first();
                        $active_plan = DB::table('customer_active_plan')
                                    ->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)
                                    ->first();
                        $customer_active_plan_id = $active_plan->customer_active_plan_id;
                        $plan = DB::table('customer_plan')
                                ->where('customer_plan_id', $active_plan->plan_id)
                                ->first();
                        $active_plan_first = DB::table('customer_active_plan')
                                                ->where('plan_id', $active_plan->plan_id)
                                                ->first();

                        $data['start_date'] = date('d F Y', strtotime($plan_user->plan_start));
                        $active_plan_data = null;
                        if((int)$active_plan_first->plan_extention_enable == 1) {
                            
                            $plan_user = DB::table('user_plan_type')
                                    ->where('user_id', $id)
                                    ->orderBy('created_at', 'desc')
                                    ->first();

                            $active_plan_extension = DB::table('plan_extensions')
                                            ->where('customer_active_plan_id', $active_plan_first->customer_active_plan_id)
                                            ->first();
                            
                            if((int)$plan_user->fixed == 1 || $plan_user->fixed == "1") {
                                $temp_valid_date = date('d F Y', strtotime('+'.$active_plan_extension->duration, strtotime($active_plan_extension->plan_start)));
                                $data['valid_date'] = date('d F Y', strtotime('-1 day', strtotime($temp_valid_date)));
                            } else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
                                $data['valid_date'] = date('d F Y', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
                            }
                            $data['plan_extension'] = true;
                            $active_plan_data = $active_plan_extension;
                        } else {
                            $plan_user = DB::table('user_plan_type')
                                ->where('user_id', $id)
                                ->orderBy('created_at', 'desc')
                                ->first();


                            if((int)$plan_user->fixed == 1 || $plan_user->fixed == "1") {
                                $temp_valid_date = date('d F Y', strtotime('+'.$active_plan_first->duration, strtotime($plan->plan_start)));
                                $data['valid_date'] = date('d F Y', strtotime('-1 day', strtotime($temp_valid_date)));
                            } else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
                                $data['valid_date'] = date('d F Y', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
                            }

                            $active_plan_data = $active_plan_first;
                        }
                        
                        // $wallet_entitlement = DB::table('employee_wallet_entitlement')
                        // ->where('member_id', $id)
                        // ->orderBy('created_at', 'desc')
                        // ->first();
                        $credit_data = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $id);
                        $plan_type = 'Corporate';
                        $data['plan_add_on'] = PlanHelper::getCompanyAccountType($user_details->UserID);
                        $data['packages'] = PlanHelper::getUserPackages($active_plan_data, $id, $data['plan_add_on'], $plan_user);
                        $data['fullname'] = ucwords($user_details->Name);
                        $data['member_id'] = $user_details->UserID;
                        $data['employee_id'] = $user_details->emp_no;
                        $data['nric'] = $user_details->NRIC;
                        if((int)$active_plan_first->plan_extention_enable == 1) {
                            $data['plan_type'] = PlanHelper::getEmployeePlanTypeExtenstion($active_plan->customer_active_plan_id, $active_plan_data);
                        } else {
                            $data['plan_type'] = PlanHelper::getEmployeePlanType($active_plan->customer_active_plan_id);
                        }
                        $data['care_online'] = TRUE;
                        $data['dob'] = date('d/m/Y', strtotime($user_details->DOB));
                        $data['mobile'] = (string)$user_details->PhoneCode." ".(string)$user_details->PhoneNo;
                        if($data['plan_type'] == "Enterprise Plan") {
                            $data['plan_add_on'] = "N.A.";
                        }

                        $data['account_type'] = $active_plan->account_type;
                        $data['account_status'] = MemberHelper::getMemberWalletStatus($user_details->UserID, 'medical');
                        if($data['plan_type'] == "Enterprise Plan") {
                            $data['plan_add_on'] = "N.A.";
                            $data['annual_entitlement'] = 14;
                        } else if($data['plan_type'] == "Out of Pocket") {
                            $data['plan_add_on'] = "N.A.";
                            $data['annual_entitlement'] = 'Not applicable';
                        } else {
                            $data['annual_entitlement'] = strtoupper($wallet->currency_type).' '.number_format($credit_data['allocation'], 2);
                        }
                        // get cap per visit
                        // check if their is a plan tier
                        $plan_tier = DB::table('plan_tier_users')
                        ->join('plan_tiers', 'plan_tiers.plan_tier_id', '=', 'plan_tier_users.plan_tier_id')
                        ->where('plan_tier_users.user_id', $user_id)
                        ->first();

                        if($plan_tier) {
                            if($wallet->cap_per_visit_medical > 0) {
                                $cap_per_visit = strtoupper($wallet->currency_type)." ".number_format($wallet->cap_per_visit_medical, 2);
                            } else {
                                $cap_per_visit = strtoupper($wallet->currency_type)." ".number_format($plan_tier->gp_cap_per_visit, 2);
                            }
                        } else {
                            if($wallet->cap_per_visit_medical > 0) {
                                $cap_per_visit = strtoupper($wallet->currency_type)." ".number_format($wallet->cap_per_visit_medical, 2);
                            }
                        }

                        $data['cap_per_visit'] = $cap_per_visit;
                        $data['currency_type'] = $wallet->currency_type;
                        return $data;
                    }
                } else {
                    $data['packages'] = DB::table('user_package')
                        ->join('care_package', 'care_package.care_package_id', '=', 'user_package.care_package_id')
                        ->where('user_package.user_id', $id)
                        ->get();
                    $user_plan_type = $plan->getUserPlan($id);

                    if(empty($user_plan_type->duration)) {
                        $data['start_date'] = date('d F Y', strtotime($user_plan_type->created_at));
                        $data['valid_date'] = date('d F Y', strtotime('+1 year', strtotime($user_plan_type->created_at)));
                    } else {
                        $data['start_date'] = date('d F Y', strtotime($user_plan_type->created_at));
                        $data['valid_date'] = date('d F Y', strtotime('+'.$user_plan_type->duration, strtotime($user_plan_type->created_at)));
                    }

                    $plan_type = 'Corporate';
                    $data['fullname'] = ucwords($user_details->Name);
                    $data['member_id'] = $user_details->UserID;
                    $data['nric'] = $user_details->NRIC;
                    $data['plan_type'] = $plan_type;
                    $data['care_online'] = FAlSE;
                    $data['dob'] = date('d/m/Y', strtotime($user_details->DOB));
                    $data['mobile'] = (string)$user_details->PhoneCode." ".(string)$user_details->PhoneNo;
                }
            } else {
                $data['no_company'] = true;
            }

            return $data;
        } else {
            $data['packages'] = DB::table('user_package')
                    ->join('care_package', 'care_package.care_package_id', '=', 'user_package.care_package_id')
                    ->where('user_package.user_id', $id)
                    ->get();

            $user_details = $user->getUserProfileMobile($id);
            $purchase_status = DB::table('customer_link_customer_buy')
                                ->where('user_id', $id)
                                ->first();
            // return json_encode($purchase_status);
            $data['company_name'] = '';
            if($purchase_status) {
                $active_plan = DB::table('customer_active_plan')->where('customer_start_buy_id', $purchase_status->customer_buy_start_id)->first();

                // if(date('Y-m-d', strtotime($active_plan->plan_start)) > date('Y-m-d')) {
                //     return -1;
                // }

                $data['start_date'] = date('d F Y', strtotime($active_plan->plan_start));
                $data['valid_date'] = date('d F Y', strtotime('+'.$active_plan->duration, strtotime($active_plan->plan_start)));
                $plan_type = 'Individual';
                $data['fullname'] = ucwords($user_details->Name);
                $data['member_id'] = $user_details->UserID;
                $data['nric'] = $user_details->NRIC;
                $data['plan_type'] = $plan_type;
                $data['care_online'] = TRUE;
            } else {
                $data['public_user'] = TRUE;
                $data['no_data'] = TRUE;
                // $user_plan_type = $plan->getUserPlan($id);

                // // if(date('Y-m-d', strtotime($user_plan_type->plan_start)) > date('Y-m-d')) {
                // //     return -1;
                // // }

                // if(empty($user_plan_type->duration)) {
                //     if(self::validateDate($plan->plan_start)) {
                //         $plan_start = date('Y-m-d', strtotime($user_plan_type->plan_start));
                //     } else {
                //         $plan_start = date('Y-m-d', strtotime($user_plan_type->created_at));
                //     }
                //     // $plan_start = $user_plan_type->plan_start ? $user_plan_type->plan_start : $user_plan_type->created_at;
                //     $data['start_date'] = date('d F Y', strtotime($plan_start));
                //     $data['valid_date'] = date('d F Y', strtotime('+1 year', strtotime($plan_start)));
                // } else {
                //     if(self::validateDate($plan->plan_start)) {
                //         $plan_start = date('Y-m-d', strtotime($user_plan_type->plan_start));
                //     } else {
                //         $plan_start = date('Y-m-d', strtotime($user_plan_type->created_at));
                //     }
                //     // $plan_start = $user_plan_type->plan_start ? $user_plan_type->plan_start : $user_plan_type->created_at;
                //     $data['start_date'] = date('d F Y', strtotime($plan_start));
                //     $data['valid_date'] = date('d F Y', strtotime('+'.$user_plan_type->duration, strtotime($plan_start)));
                // }

                // $plan_type = 'Individual';
                // $data['fullname'] = ucwords($user_details->Name);
                // $data['member_id'] = $user_details->UserID;
                // $data['nric'] = $user_details->NRIC;
                // $data['plan_type'] = $plan_type;
                // $data['care_online'] = FAlSE;
            }

            return $data;
        }
    }

    public function validateDate($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

}
