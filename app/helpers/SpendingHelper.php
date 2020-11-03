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
                            // ->where('spending_method', 'pre_paid')
                            ->where('created_at', '>=', $start)
                            ->where('created_at', '<=', $end)
                            ->sum('credit');
                            
                    $wellness_refund = DB::table('wellness_wallet_history')
                            ->whereIn('wallet_id', $wallet_ids)
                            ->where('logs', 'credits_back_from_in_network')
                            // ->where('spending_method', 'pre_paid')
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
        // get primary plan
        $plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();

        if($plan->account_type == "out_of_pocket") {
            return ['enable' => true];
        }
        // get lists of users
        $user_allocated = \CustomerHelper::getActivePlanUsers($customer_id);
        $spending_account_settings = DB::table('spending_account_settings')
                                  ->where('customer_id', $customer_id)
                                  ->orderBy('created_at', 'desc')
                                  ->first();
        $account_credits = DB::table('mednefits_credits')
                            ->join('spending_purchase_invoice', 'spending_purchase_invoice.mednefits_credits_id', '=', 'mednefits_credits.id')
                            ->where('mednefits_credits.customer_id', $customer_id)
                            ->get();
        // if($spending_account_settings->medical_payment_method_panel == 'mednefits_credits') {
        if(sizeof($account_credits) > 0) {
            $total_credits = 0;
            $total_company_entitlement = 0;
            $total_medical_entitlment = 0;
            $total_wellness_entitlment = 0;
            $purchased_credits = 0;
            $bonus_credits = 0;
            $payment_status = false;
            
            foreach($account_credits as $key => $credits) {
                $totalCredits = $credits->medical_purchase_credits + $credits->wellness_purchase_credits;
                $paidAmount = $credits->payment_amount;
                $amountDue = $totalCredits - $paidAmount;
                if($amountDue > 0) {
                    $payment_status = false;
                } else {
                    $payment_status = true;
                }
                // if((int)$credits->payment_status == 1) {
                //     $payment_status = true;
                // } else {
                //     $payment_status = false;
                // }
            
                $purchased_credits += $credits->credits;
                $bonus_credits += $credits->bonus_credits;
            }

            $total_credits = $purchased_credits + $bonus_credits;
            $start = $spending_account_settings->medical_spending_start_date;
            $end = $spending_account_settings->medical_spending_end_date;
            foreach($user_allocated as $key => $user) {
                // $medical_credit = \MemberHelper::memberMedicalPrepaid($user, $start, $end, 'post_paid');
                // $wellness_credit = \MemberHelper::memberWellnessPrepaid($user, $start, $end, 'post_paid');
                $medical_credit = \MemberHelper::newMedicalLatestAllocation($user);
                $wellness_credit = \MemberHelper::newWellnessLatestAllocation($user);
                $total_medical_entitlment += $medical_credit['allocation'];
                $total_wellness_entitlment += $wellness_credit['allocation'];
            }

            $total_company_entitlement = $total_medical_entitlment + $total_wellness_entitlment;
            $total_balance = $total_credits - $total_company_entitlement;
            $enable = true;

            if($total_credits >= $total_company_entitlement && $payment_status == false) {
                $enable = false;
            }

            if($total_credits < $total_company_entitlement) {
                $enable = true;
            }

            if($total_credits >= $total_company_entitlement &&  $payment_status == true) {
                $enable = true;
            }

            return [
                'total_credits' => $total_credits, 
                'total_company_entitlement' => $total_company_entitlement,
                'payment_status'  => $payment_status,
                'enable' => $enable,
                'test'  => $total_credits >= $total_company_entitlement
            ];
        } else {
            return ['enable' => true];
        }
    }

    public static function checkSpendingCreditsAccessNonPanel($customer_id)
    {
        // get primary plan
        $plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();

        if($plan->account_type == "out_of_pocket") {
            return ['enable' => true];
        }
        // get lists of users
        $user_allocated = \CustomerHelper::getActivePlanUsers($customer_id);
        $spending_account_settings = DB::table('spending_account_settings')
                                  ->where('customer_id', $customer_id)
                                  ->orderBy('created_at', 'desc')
                                  ->first();
        // get total credits
        $account_credits = DB::table('mednefits_credits')
        ->join('spending_purchase_invoice', 'spending_purchase_invoice.mednefits_credits_id', '=', 'mednefits_credits.id')
        ->where('mednefits_credits.customer_id', $customer_id)
        ->get();

        // if($spending_account_settings->medical_payment_method_panel == 'mednefits_credits') {
        if(sizeof($account_credits) > 0) {
            $total_credits = 0;
            $total_company_entitlement = 0;
            $total_medical_entitlment = 0;
            $total_wellness_entitlment = 0;
            $purchased_credits = 0;
            $bonus_credits = 0;
            $payment_status = false;
            $enable = true;
            
            foreach($account_credits as $key => $credits) {
                $totalCredits = $credits->medical_purchase_credits + $credits->wellness_purchase_credits;
                $paidAmount = $credits->payment_amount;
                $amountDue = $totalCredits - $paidAmount;
                if($amountDue > 0) {
                    $payment_status = false;
                } else {
                    $payment_status = true;
                }
                // if((int)$credits->payment_status == 1) {
                //     $payment_status = true;
                // } else {
                //     $payment_status = false;
                // }
            
                $purchased_credits += $credits->credits;
                $bonus_credits += $credits->bonus_credits;
            }

            $total_credits = $purchased_credits + $bonus_credits;
            $start = $spending_account_settings->medical_spending_start_date;
            $end = $spending_account_settings->medical_spending_end_date;
            foreach($user_allocated as $key => $user) {
                // $medical_credit = \MemberHelper::memberMedicalPrepaid($user, $start, $end, 'post_paid');
                // $wellness_credit = \MemberHelper::memberWellnessPrepaid($user, $start, $end, 'post_paid');
                $medical_credit = \MemberHelper::newMedicalLatestAllocation($user);
                $wellness_credit = \MemberHelper::newWellnessLatestAllocation($user);
                $total_medical_entitlment += $medical_credit['allocation'];
                $total_wellness_entitlment += $wellness_credit['allocation'];
            }

            $total_company_entitlement = $total_medical_entitlment + $total_wellness_entitlment;
            $total_balance = $total_credits - $total_company_entitlement;

            if($total_credits >= $total_company_entitlement && $payment_status == false) {
                $enable = false;
            }

            if($total_credits < $total_company_entitlement) {
                $enable = true;
            }
            
            if($total_credits >= $total_company_entitlement &&  $payment_status == true) {
                $enable = true;
            }

            return [
                'total_credits' => $total_credits, 
                'total_company_entitlement' => $total_company_entitlement,
                'payment_status'  => $payment_status,
                'enable' => $enable
            ];
        } else {
            return ['enable' => true];
        }
    }

    public static function checkTotalCreditsNonPanelTransactions($customer_id, $start, $end, $plan_method)
	{
		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
		$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();
		$total_transactions = null;
		$spending = CustomerHelper::getAccountSpendingStatus($customer_id);
		$credits = 0;

		foreach ($corporate_members as $key => $member) {
			$ids = \StringHelper::getSubAccountsID($member->user_id);

			if(sizeof($ids) > 0) {
				$total_transactions = DB::table('e_claim')
										->whereIn('user_id', $ids)
										->where('created_at', '>=', $start)
										->where('created_at', '<=', $end)
										->where('status', 1)
										->orderBy('created_at', 'desc')
										->get();
				
				foreach($total_transactions as $key => $e_claim) {
					if($e_claim->spending_type == "medical") {
						$table_wallet_history = 'wallet_history';
					} else {
						$table_wallet_history = 'wellness_wallet_history';
					}
		
					$logs = DB::table($table_wallet_history)
							->where('where_spend', 'e_claim_transaction')
							->where('id',  $e_claim->e_claim_id)
							->first();
					if($logs) {
						$credits += $logs->credit;
					} else {
						$credits += $res->amount;
					}
				}
			}
		}

		return ['credits' => $credits, 'total_consultation' => 0, 'transactions' => []];
    }
    
    public static function createNonPanelInvoice($customer_id, $start, $end, $plan_method)
	{
		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();
		$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();
		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$business_contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $customer_id)->first();
		$billing_contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $customer_id)->first();
		$spending = \CustomerHelper::getAccountSpendingStatus($customer_id);
		$total_e_claim_amount = 0;
		$total_in_network_amount = 0;
		$transactions = [];

		foreach ($corporate_members as $key => $member) {
			$ids = \StringHelper::getSubAccountsID($member->user_id);

			$trans = DB::table('e_claim')
							->where('status', 1)
							->whereIn('user_id', $ids)
							->where('created_at', '>=', $start)
							->where('created_at', '<=', $end)
							->orderBy('created_at', 'desc')
							->pluck('e_claim_id');
			if(sizeof($trans) > 0) {
				$transactions[] = $trans;
			}
		}
		
		$company_details = DB::table('customer_business_information')->where('customer_buy_start_id', $customer_id)->first();
		$number = \InvoiceLibrary::getInvoiceNuber('company_credits_statement', 3);
		$spending_invoice_day = $customer->spending_default_invoice_day;
		$day = date('t', strtotime('+1 month', strtotime($start)));
		// return $day;
		if((int)$spending_invoice_day == 31) {
			if($customer->invoice_step == "before") {
				if((int)$spending_invoice_day > (int)$day) {
					$statement_date = date('Y-m-'.$day, strtotime('-1 month', strtotime($start)));
				} else {
					$statement_date = date('Y-m-'.$spending_invoice_day, strtotime('-1 month', strtotime($start)));
				}
			} else {
				if((int)$spending_invoice_day > (int)$day) {
					$statement_date = date('Y-m-'.$day, strtotime('+1 month', strtotime($start)));
				} else {
					$statement_date = date('Y-m-'.$spending_invoice_day, strtotime('+1 month', strtotime($start)));
				}
			}
		} else {
			$statement_date = date('Y-m-'.$spending_invoice_day, strtotime('+1 month', strtotime($start)));
		}

		$statement_due = date('Y-m-d', strtotime('+15 days', strtotime($statement_date)));
		$statement_data = array(
			'statement_customer_id'     => $customer_id,
			'statement_number'          => $number,
			'statement_date'            => date('Y-m-d', strtotime($statement_date)),
			'statement_due'             => date('Y-m-d', strtotime($statement_due)),
			'statement_start_date'      => $start,
			'statement_end_date'        => $end,
			'statement_company_name'    => $company_details->company_name,
			'statement_company_address'    => $company_details->company_address,
			'statement_contact_name'    => $billing_contact->first_name.' '.$billing_contact->last_name,
			'statement_contact_number'  => $billing_contact->phone,
			'statement_contact_email'   => $billing_contact->billing_email,
			'statement_in_network_amount'   => $total_in_network_amount,
			'statement_e_claim_amount'       => $total_e_claim_amount,
			'currency_type'					=> $customer->currency_type,
			'type'						=> 'non_panel'
		);

		// create statement
		$statement_result = CompanyCreditsStatement::create($statement_data);
		$statement_id = $statement_result->id;
		foreach ($transactions as $key => $trans) {
			$check_transaction = DB::table('statement_e_claim_transactions')->where('e_claim_id', $trans)->first();

			if(!$check_transaction) {
				// insert to spending invoice transaction
				DB::table('statement_e_claim_transactions')->insert(['statement_id' => $statement_id, 'e_claim_id' => $trans, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
			}
		}

		return $statement_result;
    }
    
    public static function checkSpendingInvoiceNonPanelTransactions($customer_id, $start, $end, $invoice_id, $plan_method)
	{
		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
		$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();
		$lite_plan = false;
		$transactions = [];

		foreach ($corporate_members as $key => $member) {
			$ids = \StringHelper::getSubAccountsID($member->user_id);

			$trans = DB::table('e_claim')
							->where('status', 1)
							->whereIn('user_id', $ids)
							->where('created_at', '>=', $start)
							->where('created_at', '<=', $end)
							->lists('e_claim_id');

			if(sizeof($trans) > 0) {
                foreach($trans as $tran) {
                    array_push($transactions, $tran);
                }
                
			}
		}
		
		foreach ($transactions as $key => $trans) {
			$check_transaction = DB::table('statement_e_claim_transactions')->where('e_claim_id', $trans)->first();

			if(!$check_transaction) {
				// insert to spending invoice transaction
				DB::table('statement_e_claim_transactions')->insert(['statement_id' => $invoice_id, 'e_claim_id' => $trans, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
			}
		}
    }
    
    public static function getTotalEntitlements($customer_id, $start, $end)
    {
        $user_allocated = \CustomerHelper::getActivePlanUsers($customer_id, $start, $end);
        $total_medical_entitlement = 0;
        $total_wellness_entitlement = 0;
        $spending = \CustomerHelper::getAccountSpendingStatus($customer_id);
        $spending_method_medical = $spending['medical_payment_method_panel'] == "mednefits_credits" ? 'pre_paid' : 'post_paid';
        $spending_method_wellness = $spending['wellness_payment_method_non_panel'] == "mednefits_credits" ? 'pre_paid' : 'post_paid';

        foreach($user_allocated as $key => $user) {
            $medical_credit = \MemberHelper::memberMedicalPrepaid(
                $user, $start, $end, $spending_method_medical
            );
            $wellness_credit = \MemberHelper::memberWellnessPrepaid(
                $user, $start, $end, $spending_method_wellness
            );
            $total_medical_entitlement += $medical_credit['allocation'];
            $total_wellness_entitlement += $wellness_credit['allocation'];
        }

        return [
            'total_medical_entitlement' => $total_medical_entitlement,
            'total_wellness_entitlment' => $total_wellness_entitlement
        ];
    }

    public static function checkPendingTopUpInvoice($customer_id, $credits) 
    {
        $spendingAccountSettings = DB::table('spending_account_settings')
                                            ->where('customer_id', $customer_id)
                                            ->orderBy('created_at', 'desc')
                                            ->select('spending_account_setting_id', 'customer_id', 'medical_spending_start_date')
                                            ->first();

        $topUpData = DB::table('mednefits_credits')
                      ->join('spending_purchase_invoice', 'spending_purchase_invoice.mednefits_credits_id', '=', 'mednefits_credits.id')
                      ->where('mednefits_credits.customer_id', $customer_id)
                      ->where('mednefits_credits.start_term', $spendingAccountSettings->medical_spending_start_date)
                      ->where('mednefits_credits.top_up', 1)
                      ->get();

        foreach($topUpData as $topUp) {
            $amount_due = ($topUp->medical_purchase_credits + $topUp->wellness_purchase_credits) - $topUp->payment_amount;
			if($amount_due > 0) {
                // update
                $purchaseCredits = $credits;
                $bonusCredits = $credits * 0.20;
                DB::table('spending_purchase_invoice')
                    ->where('spending_purchase_invoice_id', $topUp->spending_purchase_invoice_id)
                    ->increment('medical_purchase_credits', $purchaseCredits);
                DB::table('spending_purchase_invoice')
                    ->where('spending_purchase_invoice_id', $topUp->spending_purchase_invoice_id)
                    ->increment('medical_credit_bonus', $bonusCredits);
                DB::table('mednefits_credits')
                    ->where('id', $topUp->id)
                    ->increment('credits', $purchaseCredits);
                DB::table('mednefits_credits')
                    ->where('id', $topUp->id)
                    ->increment('bonus_credits', $bonusCredits);
                return true;
			}
        }

        return false;
    }
}

?>