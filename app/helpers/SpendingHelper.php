<?php

class SpendingHelper {

    public static function getMednefitsAccountSpending($customer_id, $start, $end, $type, $with_user_allocation)
    {
        $medical = 0;
        $wellness = 0;
        $total_medical_balance = 0;
        $total_wellness_balance = 0;
        $total_company_entitlement = 0;
        $total_medical_entitlment = 0;
        $total_wellness_entitlment = 0;
        $end = \PlanHelper::endDate($end);
        $account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
        $user_allocated = \CustomerHelper::getActivePlanUsers($customer_id);
        $spending = \CustomerHelper::getAccountSpendingStatus($customer_id);
        $spending_method_medical = $spending['medical_payment_method_panel'] == "mednefits_credits" ? 'pre_paid' : 'post_paid';
        $spending_method_wellness = $spending['wellness_payment_method_non_panel'] == "mednefits_credits" ? 'pre_paid' : 'post_paid';

        if(sizeof($user_allocated) > 0) {
            $wallet_ids = DB::table('e_wallet')->whereIn('UserID', $user_allocated)->lists('wallet_id');

            if(sizeof($wallet_ids) > 0) {
                if($type == "all") {
                    // get all medical logs of credits
                    $medical = DB::table('wallet_history')
                            ->whereIn('wallet_id', $wallet_ids)
                            ->whereIn('where_spend', ['in_network_transaction', 'e_claim_transaction'])
                            // ->where('spending_method', 'pre_paid')
                            ->where('created_at', '>=', $start)
                            ->where('created_at', '<=', $end)
                            ->sum('credit');
                            
                    $medical_refund = DB::table('wallet_history')
                            ->whereIn('wallet_id', $wallet_ids)
                            ->where('logs', 'credits_back_from_in_network')
                            // ->where('spending_method', 'pre_paid')
                            ->where('created_at', '>=', $start)
                            ->where('created_at', '<=', $end)
                            ->sum('credit');
        
                    $medical = $medical - $medical_refund;
                }
               
                if($type == "all") {
                    // get all wellness logs of credits
                            $wellness = DB::table('wellness_wallet_history')
                            ->whereIn('wallet_id', $wallet_ids)
                            ->whereIn('where_spend', ['in_network_transaction', 'e_claim_transaction'])
                            ->where('spending_method', 'pre_paid')
                            ->where('created_at', '>=', $start)
                            ->where('created_at', '<=', $end)
                            ->sum('credit');
                            
                    $wellness_refund = DB::table('wellness_wallet_history')
                            ->whereIn('wallet_id', $wallet_ids)
                            ->where('logs', 'credits_back_from_in_network')
                            ->where('spending_method', 'pre_paid')
                            ->where('created_at', '>=', $start)
                            ->where('created_at', '<=', $end)
                            ->sum('credit');
        
                    $wellness = $wellness - $wellness_refund;
                }
            }
        }
        
        $format = [];
        // with user allocation
        if($with_user_allocation) {
            foreach($user_allocated as $key => $user) {
                // if($type == "all") {
                    $medical_credit = \MemberHelper::memberMedicalPrepaid($user, $start, $end, $spending_method_medical);
                    $wellness_credit = \MemberHelper::memberWellnessPrepaid($user, $start, $end, $spending_method_wellness);
                    $total_medical_entitlment += $medical_credit['allocation'];
                    $total_wellness_entitlment += $wellness_credit['allocation'];
                    $total_medical_balance += $medical_credit['allocation'] - $medical_credit['get_allocation_spent'];
                    $total_wellness_balance += $wellness_credit['allocation'] - $wellness_credit['get_allocation_spent'];
                // } else if($type == "medical") {
                //     $medical_credit = \MemberHelper::memberMedicalPrepaid($user, $start, $end);
                //     $total_medical_balance += $medical_credit['allocation'] - $medical_credit['get_allocation_spent'];
                // } else {
                //     $wellness_credit = \MemberHelper::memberWellnessPrepaid($user, $start, $end);
                //     $total_wellness_balance += $wellness_credit['allocation'] - $wellness_credit['get_allocation_spent'];
                // }
            }
        }
        $total_company_entitlement = $total_medical_entitlment + $total_wellness_entitlment;
        return [
            'total_company_entitlement' => $total_company_entitlement,
            'total_medical_entitlement' => $total_medical_entitlment,
            'total_wellness_entitlement' => $total_wellness_entitlment,
            'credits' => $medical + $wellness, 
            'medical_credits' => $total_medical_balance, 
            'wellness_credits' => $total_wellness_balance
        ];
    }

    public static function checkSpendingCreditsAccess($customer_id)
    {
        // get lists of users
        $user_allocated = \CustomerHelper::getActivePlanUsers($customer_id);
        $spending_account_settings = DB::table('spending_account_settings')
                                  ->where('customer_id', $customer_id)
                                  ->orderBy('created_at', 'desc')
                                  ->first();

        if($spending_account_settings->medical_payment_method_panel == 'mednefits_credits') {
            $total_credits = 0;
            $total_company_entitlement = 0;
            $total_medical_entitlment = 0;
            $total_wellness_entitlment = 0;
            $purchased_credits = 0;
            $bonus_credits = 0;
            $payment_status = false;
            // get total credits
            // $creditAccount = DB::table('mednefits_credits')
            //                 ->where('customer_id', $customer_id)
            //                 ->where('start_term', $spending_account_settings->medical_spending_start_date)
            //                 ->first();
            $account_credits = DB::table('mednefits_credits')
                            ->join('spending_purchase_invoice', 'spending_purchase_invoice.mednefits_credits_id', '=', 'mednefits_credits.id')
                            ->where('mednefits_credits.customer_id', $customer_id)
                            ->get();
            
            foreach($account_credits as $key => $credits) {
                if((int)$credits->payment_status == 1) {
                    $payment_status = true;
                } else {
                    $payment_status = false;
                }
            
                $purchased_credits += $credits->credits;
                $bonus_credits += $credits->bonus_credits;
            }

            $total_credits = $purchased_credits + $bonus_credits;
            $start = $spending_account_settings->medical_spending_start_date;
            $end = $spending_account_settings->medical_spending_end_date;
            foreach($user_allocated as $key => $user) {
                $medical_credit = \MemberHelper::memberMedicalPrepaid($user, $start, $end, 'post_paid');
                $wellness_credit = \MemberHelper::memberWellnessPrepaid($user, $start, $end, 'post_paid');
                $total_medical_entitlment += $medical_credit['allocation'];
                $total_wellness_entitlment += $wellness_credit['allocation'];
            }

            $total_company_entitlement = $total_medical_entitlment + $total_wellness_entitlment;
            $total_balance = $total_credits - $total_company_entitlement;

            return [
                'total_credits' => $total_credits, 
                'total_company_entitlement' => $total_company_entitlement,
                'payment_status'  => $payment_status,
                'enable' => $total_balance > 0 || $payment_status == true ? true : false
            ];
        } else {
            return ['enable' => true];
        }
    }

    public static function checkSpendingCreditsAccessNonPanel($customer_id)
    {
        // get lists of users
        $user_allocated = \CustomerHelper::getActivePlanUsers($customer_id);
        $spending_account_settings = DB::table('spending_account_settings')
                                  ->where('customer_id', $customer_id)
                                  ->orderBy('created_at', 'desc')
                                  ->first();

        if($spending_account_settings->medical_payment_method_panel == 'mednefits_credits') {
            $total_credits = 0;
            $total_company_entitlement = 0;
            $total_medical_entitlment = 0;
            $total_wellness_entitlment = 0;
            $purchased_credits = 0;
            $bonus_credits = 0;
            $payment_status = false;
            // get total credits
            $account_credits = DB::table('mednefits_credits')
                            ->join('spending_purchase_invoice', 'spending_purchase_invoice.mednefits_credits_id', '=', 'mednefits_credits.id')
                            ->where('mednefits_credits.customer_id', $customer_id)
                            ->get();
            
            foreach($account_credits as $key => $credits) {
                if((int)$credits->payment_status == 1) {
                    $payment_status = true;
                } else {
                    $payment_status = false;
                }
            
                $purchased_credits += $credits->credits;
                $bonus_credits += $credits->bonus_credits;
            }

            $total_credits = $purchased_credits + $bonus_credits;
            $start = $spending_account_settings->medical_spending_start_date;
            $end = $spending_account_settings->medical_spending_end_date;
            foreach($user_allocated as $key => $user) {
                $medical_credit = \MemberHelper::memberMedicalPrepaid($user, $start, $end, 'post_paid');
                $wellness_credit = \MemberHelper::memberWellnessPrepaid($user, $start, $end, 'post_paid');
                $total_medical_entitlment += $medical_credit['allocation'];
                $total_wellness_entitlment += $wellness_credit['allocation'];
            }

            $total_company_entitlement = $total_medical_entitlment + $total_wellness_entitlment;
            $total_balance = $total_credits - $total_company_entitlement;

            return [
                'total_credits' => $total_credits, 
                'total_company_entitlement' => $total_company_entitlement,
                'payment_status'  => $payment_status,
                'enable' => $total_balance > 0 ? true : false
            ];
        } else {
            return ['enable' => true];
        }
    }
}

?>