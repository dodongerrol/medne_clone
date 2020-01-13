<?php
class MemberHelper
{
	public static function getMemberDateTerms($member_id, $term)
	{
		if($term == "current_term") {
			$user_plan_history = DB::table('user_plan_history')->where('user_id', $member_id)->where('type', 'started')->orderBy('created_at', 'desc')->first();
			if($user_plan_history) {
				$customer_active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)->first();
				$plan = DB::table('customer_plan')->where('customer_plan_id', $customer_active_plan->plan_id)->first();
				return ['start' => $user_plan_history->date, 'end' => PlanHelper::endDate($plan->plan_end)];
			} else {
				return false;
			}
		} else {
			$user_plan_history = DB::table('user_plan_history')->where('user_id', $member_id)->where('type', 'started')->orderBy('created_at', 'desc')->skip(1)->take(1)->first();
			if($user_plan_history) {
				$customer_active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)->first();
				$plan = DB::table('customer_plan')->where('customer_plan_id', $customer_active_plan->plan_id)->first();
				return ['start' => $plan->plan_start, 'end' => PlanHelper::endDate($plan->plan_end)];
			} else {
				return false;
			}
		}
	}

	public static function activateNewEntitlement($member_id, $id)
	{
		$customer_id = PlanHelper::getCustomerId($member_id);
		$member_entitlment = DB::table('wallet_entitlement_schedule')
                            ->where('wallet_entitlement_schedule_id', $id)
                            ->where('status', 0)
                            ->get();

    $wallet_entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $member_id)->orderBy('created_at', 'desc')->first();
    $data = [];
    $data['member_id'] = $member_id;
    $spending_type = null;
    $entitlement_id = null;
    foreach ($member_entitlment as $key => $entitlement) {
    	$spending_type = $entitlement->spending_type;
    	$entitlement_id = $entitlement->wallet_entitlement_schedule_id;
      if($entitlement->spending_type == "medical") {
          $data['medical_usage_date'] = $entitlement->new_usage_date;
          $data['medical_proration'] = $entitlement->proration;
          $data['medical_entitlement'] = $entitlement->new_entitlement_credits;
          $data['medical_allocation'] = $entitlement->new_allocation_credits;
          $data['medical_entitlement_balance'] = $entitlement->new_allocation_credits;
      } else {
          $data['medical_usage_date'] = $wallet_entitlement->medical_usage_date;
          $data['medical_proration'] = $wallet_entitlement->medical_proration;
          $data['medical_entitlement'] = $wallet_entitlement->medical_entitlement;
          $data['medical_allocation'] = $wallet_entitlement->medical_allocation;
          $data['medical_entitlement_balance'] = $wallet_entitlement->medical_entitlement_balance;
      }

      if($entitlement->spending_type == "wellness") {
          $data['wellness_usage_date'] = $entitlement->new_usage_date;
          $data['wellness_proration'] = $entitlement->proration;
          $data['wellness_entitlement'] = $entitlement->new_entitlement_credits;
          $data['wellness_allocation'] = $entitlement->new_allocation_credits;
          $data['wellness_entitlement_balance'] = $entitlement->new_allocation_credits;
      } else {
          $data['wellness_usage_date'] = $wallet_entitlement->wellness_usage_date;
          $data['wellness_proration'] = $wallet_entitlement->wellness_proration;
          $data['wellness_entitlement'] = $wallet_entitlement->wellness_entitlement;
          $data['wellness_allocation'] = $wallet_entitlement->wellness_allocation;
          $data['wellness_entitlement_balance'] = $wallet_entitlement->wellness_entitlement_balance;
      }
    }

    if($data) {
	    $customer_wallet = DB::table('customer_credits')->where('customer_id', $customer_id)->first();
	    // get membder credits allocation
	    $wallet = DB::table('e_wallet')->where('UserID', $member_id)->orderBy('created_at', 'desc')->first();

	    if($spending_type == "medical") {
	    	$medical_credit_data = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $member_id);
	    	if($data['medical_allocation'] > $medical_credit_data['allocation']) {
					$new_medical_allocation = $data['medical_allocation'] - $medical_credit_data['allocation'];
					$type_allocation_medical = "added_by_hr";
				} else {
					$new_medical_allocation = $medical_credit_data['allocation'] - $data['medical_allocation'];
					$type_allocation_medical = "deducted_by_hr";
				}

				$last_customer_active_plan_id_medical = DB::table('wallet_history')
																								->where('wallet_id', $wallet->wallet_id)
																								->where('logs', 'added_by_hr')
																								->orderBy('created_at', 'desc')
																								->first();
				$last_customer_active_plan_id_medical = $last_customer_active_plan_id_medical->customer_active_plan_id;

				if($type_allocation_medical == "added_by_hr") {
					// medical
					$wallet_result = DB::table('e_wallet')->where('UserID', $member_id)->increment('balance', $new_medical_allocation);
					$employee_credit_logs = array(
						'wallet_id'					=> $wallet->wallet_id,
						'credit'						=> $new_medical_allocation,
						'logs'							=> 'added_by_hr',
						'running_balance'		=> $wallet->balance + $new_medical_allocation,
						'customer_active_plan_id' => $last_customer_active_plan_id_medical,
						'currency_type'	=> $wallet->currency_type,
						'created_at'			=> date('Y-m-d H:i:s'),
						'updated_at'			=> date('Y-m-d H:i:s')
					);
					DB::table('wallet_history')->insert($employee_credit_logs);
					$company_credits_result = DB::table('customer_credits')->where('customer_id', $customer_id)->decrement('balance', $new_medical_allocation);
					$company_credit_logs = array(
						'customer_credits_id' => $customer_wallet->customer_credits_id,
						'credit'							=> $new_medical_allocation,
						'logs'								=> 'added_employee_credits',
						'user_id'							=> $member_id,
						'running_balance'			=> $customer_wallet->balance - $new_medical_allocation,
						'customer_active_plan_id' => $last_customer_active_plan_id_medical,
						'currency_type'	=> $customer_wallet->currency_type,
						'created_at'			=> date('Y-m-d H:i:s'),
						'updated_at'			=> date('Y-m-d H:i:s')
					);
					DB::table('customer_credit_logs')->insert($company_credit_logs);
				} else {
					$wallet_result = Wallet::where('UserID', $member_id)->decrement('balance', $new_medical_allocation);
					$employee_credit_logs = array(
						'wallet_id'					=> $wallet->wallet_id,
						'credit'						=> $new_medical_allocation,
						'logs'							=> 'deducted_by_hr',
						'running_balance'		=> $wallet->balance - $new_medical_allocation,
						'customer_active_plan_id' => $last_customer_active_plan_id_medical,
						'currency_type'	=> $wallet->currency_type,
						'created_at'			=> date('Y-m-d H:i:s'),
						'updated_at'			=> date('Y-m-d H:i:s')
					);
					DB::table('wallet_history')->insert($employee_credit_logs);
				}

	    } else if($spending_type == "wellness"){
	    	$wellness_credit_data = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $member_id);
	    	if($data['wellness_allocation'] > $wellness_credit_data['allocation']) {
					$new_wellness_allocation = $data['wellness_allocation'] - $wellness_credit_data['allocation'];
					$type_allocation_wellness = "added_by_hr";
				} else {
					$new_wellness_allocation = $wellness_credit_data['allocation'] - $data['wellness_allocation'];
					$type_allocation_wellness = "deducted_by_hr";
				}

				$last_customer_active_plan_id_wellness = DB::table('wellness_wallet_history')
																								->where('wallet_id', $wallet->wallet_id)
																								->where('logs', 'added_by_hr')
																								->orderBy('created_at', 'desc')
																								->first();
				$last_customer_active_plan_id_wellness = $last_customer_active_plan_id_wellness->customer_active_plan_id;
				if($type_allocation_wellness == "added_by_hr") {
					// wellness
					$wallet_result = DB::table('e_wallet')->where('UserID', $member_id)->increment('wellness_balance', $new_wellness_allocation);
					$employee_credit_logs = array(
						'wallet_id'					=> $wallet->wallet_id,
						'credit'						=> $new_wellness_allocation,
						'logs'							=> 'added_by_hr',
						'running_balance'		=> $wallet->wellness_balance + $new_wellness_allocation,
						'customer_active_plan_id' => $last_customer_active_plan_id_wellness,
						'currency_type'	=> $wallet->currency_type,
						'created_at'			=> date('Y-m-d H:i:s'),
						'updated_at'			=> date('Y-m-d H:i:s')
					);
					DB::table('wellness_wallet_history')->insert($employee_credit_logs);

					$company_credits_result = DB::table('customer_credits')->where('customer_id', $customer_id)->decrement('wellness_credits', $new_wellness_allocation);
					$company_credit_logs = array(
						'customer_credits_id' => $customer_wallet->customer_credits_id,
						'credit'							=> $new_wellness_allocation,
						'logs'								=> 'added_employee_credits',
						'user_id'							=> $member_id,
						'running_balance'			=> $customer_wallet->wellness_credits - $new_wellness_allocation,
						'customer_active_plan_id' => $last_customer_active_plan_id_wellness,
						'currency_type'	=> $customer_wallet->currency_type,
						'created_at'			=> date('Y-m-d H:i:s'),
						'updated_at'			=> date('Y-m-d H:i:s')
					);
					DB::table('customer_wellness_credits_logs')->create($company_credit_logs);
				} else {
					$wallet_result = Wallet::where('UserID', $member_id)->decrement('wellness_balance', $new_wellness_allocation);
					$employee_credit_logs = array(
						'wallet_id'					=> $wallet->wallet_id,
						'credit'						=> $new_wellness_allocation,
						'logs'							=> 'deducted_by_hr',
						'running_balance'		=> $wallet->wellness_balance - $new_wellness_allocation,
						'customer_active_plan_id' => $last_customer_active_plan_id_wellness,
						'currency_type'	=> $wallet->currency_type,
						'created_at'			=> date('Y-m-d H:i:s'),
						'updated_at'			=> date('Y-m-d H:i:s')
					);
					DB::table('wellness_wallet_history')->insert($employee_credit_logs);
				}
	    }

			// create entitlement
			$data['currency_type'] = $wallet->currency_type;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			$result = DB::table('employee_wallet_entitlement')->insert($data);
			if($result) {
				DB::table('wallet_entitlement_schedule')->where('wallet_entitlement_schedule_id', $entitlement_id)->update(['status' => 1, 'updated_at' => $data['updated_at']]);
			}
			return $result;
    } else {
    	return false;
    }
	}
}
?>