<?php
class PlanHelper {

	public static function endDate($end)
	{
		$temp_end = date('Y-m-d H:i:s', strtotime('+23 hours', strtotime($end)));
		$temp_minutes_end = date('Y-m-d H:i:s', strtotime('+59 minutes', strtotime($temp_end)));
		$final_end = date('Y-m-d H:i:s', strtotime('+59 seconds', strtotime($temp_minutes_end)));

		return $final_end;
	}

	public static function getEndDate($end)
	{
		$temp_end = date('Y-m-t H:i:s', strtotime('+23 hours', strtotime($end)));
		$temp_minutes_end = date('Y-m-d H:i:s', strtotime('+59 minutes', strtotime($temp_end)));
		$final_end = date('Y-m-d H:i:s', strtotime('+59 seconds', strtotime($temp_minutes_end)));

		return $final_end;
	}
	public static function getPlanExpiration($customer_id)
	{
		$today = date('Y-m-d', strtotime('-1 day'));

		$plan = DB::table('customer_plan')
		->where('customer_buy_start_id', $customer_id)
		->orderBy('created_at', 'desc')
		->first();
		$active_plan = DB::table('customer_active_plan')
		->where('plan_id', $plan->customer_plan_id)
		->first();
		if((int)$active_plan->plan_extention_enable == 1) {
			$plan_extention = DB::table('plan_extensions')
			->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
			->first();
			if($plan_extention) {
				if($plan_extention->duration || $plan_extention->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$plan_extention->duration, strtotime($plan_extention->plan_start)));
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan_extention->plan_start)));
				}
			} else {
				if($active_plan->duration || $active_plan->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$active_plan->duration, strtotime($plan->plan_start)));
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
				}
			}
		} else {
			if($active_plan->duration || $active_plan->duration != "") {
				$end_plan_date = date('Y-m-d', strtotime('+'.$active_plan->duration, strtotime($plan->plan_start)));
			} else {
				$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
			}
		}

		$first_date = strtotime($today);
		$end_date = strtotime($end_plan_date);
		$time_left = $end_date - $first_date;
		$diff = round((($time_left/24)/60)/60);

		$end_date = date('Y-m-d', strtotime('-1 day', strtotime($end_plan_date)));

		return array(
			'end_date'  => $end_date,
			'plan_days_to_expire' => $diff >= 0 ? $diff : 0,
			'expire'         => date('Y-m-d') >= $end_date ? TRUE : FALSE,
		);
	}

	public static function getDependentPlanType($dependent_plan_id)
	{
		$plan_name = "Mednefits Care Plan (Corporate)";

		$dependent_plan = DB::table('dependent_plans')->where('dependent_plan_id', $dependent_plan_id)->first();

		if(!$dependent_plan) {
			return $plan_name;
		}

		if($dependent_plan->account_type == "insurance_bundle") {
			if($dependent_plan->secondary_account_type == null) {
				if($dependent_plan->secondary_account_type == "pro_plan_bundle"){
					$plan_name = "Bundle Pro";
				} else {
					$plan_name = "Bundle Lite";
				}
			} else if($dependent_plan->secondary_account_type == "pro_plan_bundle"){
				$plan_name = "Bundle Pro";
			} else {
				$plan_name = "Bundle Lite";
			}
		} else if($dependent_plan->account_type == "stand_alone_plan") {
			$plan_name = "Pro Plan";
		} else if($dependent_plan->account_type == "lite_plan") {
			$plan_name = "Lite Plan";
		} else if($dependent_plan->account_type == "enterprise_plan") {
			$plan_name = "Enterprise Plan";
		}

		return $plan_name;
	}

	public static function getEmployeePlanType($customer_active_plan_id)
	{
		$plan_name = "Mednefits Care Plan (Corporate)";

		$active = DB::table('customer_active_plan')->where('customer_active_plan_id', $customer_active_plan_id)->first();

		if(!$active) {
			return $plan_name;
		}

		if($active->account_type == "insurance_bundle") {
			if($active->secondary_account_type == null) {
				$plan = DB::table('customer_plan')->where('customer_plan_id', $active->plan_id)->first();
				if($plan->secondary_account_type == null) {
					$plan_name = "Bundle Pro";
				} else if($plan->secondary_account_type == "pro_plan_bundle"){
					$plan_name = "Bundle Pro";
				} else {
					$plan_name = "Bundle Lite";
				}
			} else if($active->secondary_account_type == "pro_plan_bundle"){
				$plan_name = "Bundle Pro";
			} else {
				$plan_name = "Bundle Lite";
			}
		} else if($active->account_type == "stand_alone_plan") {
			$plan_name = "Pro Plan";
		} else if($active->account_type == "lite_plan") {
			$plan_name = "Lite Plan";
		} else if($active->account_type == "enterprise_plan") {
			$plan_name = "Enterprise Plan";
		}

		return $plan_name;
	}

	public static function getEmployeePlanTypeExtenstion($customer_active_plan_id, $active_plan_data)
	{
		$plan_name = "Mednefits Care Plan (Corporate)";

		$active = DB::table('customer_active_plan')->where('customer_active_plan_id', $customer_active_plan_id)->first();

		if(!$active) {
			return $plan_name;
		}

		if($active_plan_data->account_type == "insurance_bundle") {
			if($active_plan_data->secondary_account_type == null) {
				$plan = DB::table('customer_plan')->where('customer_plan_id', $active->plan_id)->first();
				if($plan->secondary_account_type == null) {
					$plan_name = "Bundle Pro";
				} else if($plan->secondary_account_type == "pro_plan_bundle"){
					$plan_name = "Bundle Pro";
				} else {
					$plan_name = "Bundle Lite";
				}
			} else if($active_plan_data->secondary_account_type == "pro_plan_bundle"){
				$plan_name = "Bundle Pro";
			} else {
				$plan_name = "Bundle Lite";
			}
		} else if($active_plan_data->account_type == "stand_alone_plan") {
			$plan_name = "Pro Plan";
		} else if($active_plan_data->account_type == "lite_plan") {
			$plan_name = "Lite Plan";
		} else if($active_plan_data->account_type == "enterprise_plan") {
			$plan_name = "Enterprise Plan";
		}

		return $plan_name;
	}

	public static function checkEmployeePlanStatus($user_id)
	{
		$corporate_member = DB::table('corporate_members')->where('user_id', $user_id)->first();

		if(!$corporate_member) {
			return FALSE;
		}

		$user = new User();
		$plan = new UserPlanType();
		$data = [];

		$user_details = $user->getUserProfileMobile($user_id);
		$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
		$company = DB::table('corporate_members')
		->join('corporate', 'corporate.corporate_id', '=',  'corporate_members.corporate_id')
		->where('corporate_members.user_id', '=', $user_id)
		->first();

		$purchase_status = DB::table('customer_link_customer_buy')
		->where('corporate_id', $company->corporate_id)
		->first();

		if(!$purchase_status) {
			return FALSE;
		}

		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $purchase_status->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
		$active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
		$plan_user = DB::table('user_plan_type')->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
		
		if((int)$active_plan->plan_extention_enable == 1) {
			$plan_user_history = DB::table('user_plan_history')
			->where('user_id', $user_id)
			->where('type', 'started')
			->orderBy('created_at', 'desc')
			->first();
			if(!$plan_user_history) {
                    // create plan user history
				self::createUserPlanHistory($user_id, $customer_id);
				$plan_user_history = DB::table('user_plan_history')
				->where('user_id', $user_id)
				->where('type', 'started')
				->orderBy('created_at', 'desc')
				->first();
			}

			$plan_user = DB::table('user_plan_type')
			->where('user_id', $user_id)
			->orderBy('created_at', 'desc')
			->first();
			
			$active_plan = DB::table('customer_active_plan')
			->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)
			->first();

			$plan = DB::table('customer_plan')
			->where('customer_plan_id', $active_plan->plan_id)
			->first();

			$first_active_plan = DB::table('customer_active_plan')
			->where('plan_id', $active_plan->plan_id)
			->first();

			$active_plan_extension = DB::table('plan_extensions')
			->where('customer_active_plan_id', $first_active_plan->customer_active_plan_id)
			->first();
			
			if((int)$plan_user->fixed == 1 || $plan_user->fixed == "1") {
				$temp_valid_date = date('Y-m-d', strtotime('+'.$active_plan_extension->duration, strtotime($active_plan_extension->plan_start)));
				$data['valid_date'] = date('F d, Y', strtotime('-1 day', strtotime($temp_valid_date)));
			} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
				$data['valid_date'] = date('F d, Y', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
			}
		} else {
			$plan_user_history = DB::table('user_plan_history')
			->where('user_id', $user_id)
			->where('type', 'started')
			->orderBy('created_at', 'desc')
			->first();
			if(!$plan_user_history) {
                    // create plan user history
				PlanHelper::createUserPlanHistory($user_id, $link_account->customer_buy_start_id, $customer_id);
				$plan_user_history = DB::table('user_plan_history')
				->where('user_id', $user_id)
				->where('type', 'started')
				->orderBy('created_at', 'desc')
				->first();
			}
			$plan_user = DB::table('user_plan_type')
			->where('user_id', $user_id)
			->orderBy('created_at', 'desc')
			->first();

			$active_plan = DB::table('customer_active_plan')
			->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)
			->first();

			$plan = DB::table('customer_plan')
			->where('customer_plan_id', $active_plan->plan_id)
			->first();

			$first_active_plan = DB::table('customer_active_plan')
			->where('plan_id', $active_plan->plan_id)
			->first();

			if((int)$plan_user->fixed == 1 || $plan_user->fixed == "1") {
				$temp_valid_date = date('Y-m-d', strtotime('+'.$first_active_plan->duration, strtotime($plan->plan_start)));
				$data['valid_date'] = date('F d, Y', strtotime('-1 day', strtotime($temp_valid_date)));
			} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
				$data['valid_date'] = date('F d, Y', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
			}
		}

		$data['company_name'] = ucwords($company->company_name);
		$data['start_date'] = date('F d, Y', strtotime($plan_user->plan_start));
		$data['fullname'] = ucwords($user_details->Name);
		$data['user_id'] = $user_details->UserID;
		$data['nric'] = $user_details->NRIC;
		$data['user_type'] = "employee";
		$data['currency_type'] = $wallet->currency_type;

		if(date('Y-m-d') > date('Y-m-d', strtotime($data['valid_date']))) {
			$data['expired'] = TRUE;
		} else {
			$data['expired'] = FALSE;
		}

		if(date('Y-m-d', strtotime($plan_user->plan_start)) > date('Y-m-d')) {
			$data['pending'] = true;
		} else {
			$data['pending'] = false;
		}

		return $data;
	}

	public static function getCompanyPlanDatesByPlan($customer_id, $plan_id) 
	{
		$plan = DB::table('customer_plan')
		->where('customer_buy_start_id', $customer_id)
		->orderBy('created_at', 'desc')
		->first();

		$active_plan = DB::table('customer_active_plan')
		->where('plan_id', $plan_id)
		->first();

		if((int)$active_plan->plan_extention_enable == 1) {
			$plan_extention = DB::table('plan_extensions')
			->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
			->first();
			if($plan_extention) {
				if($plan_extention->duration || $plan_extention->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$plan_extention->duration, strtotime($plan_extention->plan_start)));
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan_extention->plan_start)));
				}
			} else {
				if($active_plan->duration || $active_plan->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$active_plan->duration, strtotime($plan->plan_start)));
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
				}
			}
		} else {
			if($active_plan->duration || $active_plan->duration != "") {
				$end_plan_date = date('Y-m-d', strtotime('+'.$active_plan->duration, strtotime($plan->plan_start)));
			} else {
				$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
			}
		}

		$end_plan_date = date('Y-m-d', strtotime('-1 day', strtotime($end_plan_date)));

		return array('plan_start' => $plan->plan_start, 'plan_end' => $end_plan_date);
	}
	
	public static function getCustomerId($id)
	{
		$user = DB::table('user')->where('UserID', $id)->first();

		if($user) {
			$corporate_member = DB::table('corporate_members')->where('user_id', $id)->where('removed_status', 0)->first();
			if($corporate_member) {
				$corporate = DB::table('corporate')->where('corporate_id', $corporate_member->corporate_id)->first();
				if($corporate) {
					$account = DB::table('customer_link_customer_buy')->where('corporate_id', $corporate->corporate_id)->first();
					if($account) {
						return $account->customer_buy_start_id;
					} else {
						return FALSE;
					}
				} else {
					return FALSE;
				}
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public static function getCompanyAccountTypeEnrollee($customer_id)
	{
		$hr = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$hr) {
			return "NIL";
		}

		if((int)$hr->wallet == 1 && (int)$hr->qr_payment == 1) {
			return "Health Wallet";
		} else {
			return "NIL";

		}
	}

	public static function getCompanyAccountType($user_id)
	{
		$result = self::getCustomerId($user_id);
		$hr = DB::table('customer_buy_start')->where('customer_buy_start_id', $result)->first();

		if(!$hr) {
			return "NIL";
		}

		if((int)$hr->wallet == 1 && (int)$hr->qr_payment == 1) {
			return "Health Wallet";
		} else {
			return "NIL";

		}
	}

	public static function reCalculateEmployeeBalance($user_id)
	{
		$wallet = DB::table('e_wallet')->where('UserID', $user_id)->orderBy('created_at', 'desc')->first();

		$wallet_reset = DB::table('credit_reset')
		->where('id', $user_id)
		->where('user_type', 'employee')
		->where('spending_type', 'medical')
		->orderBy('created_at', 'desc')
		->first();

		if($wallet_reset) {
			$wallet_history_id = $wallet_reset->wallet_history_id;
                // get all medical credits transactions from transaction history
			$e_claim_spent = DB::table('wallet_history')
							->join('e_wallet', 'e_wallet.wallet_id', '=', 'wallet_history.wallet_id')
                            ->where('wallet_history.wallet_id', $wallet->wallet_id)
                            ->where('wallet_history.where_spend', 'e_claim_transaction')
                            // ->where('wallet_history.wallet_history_id', '>=', $wallet_history_id)
							->where('wallet_history.created_at', '>=', $wallet_reset->date_resetted)
							->sum('credit');

			$in_network_temp_spent = DB::table('wallet_history')
							->join('e_wallet', 'e_wallet.wallet_id', '=', 'wallet_history.wallet_id')
                            ->where('wallet_history.wallet_id', $wallet->wallet_id)
							->where('wallet_history.wallet_id', $wallet->wallet_id)
							->where('wallet_history.where_spend', 'in_network_transaction')
							// ->where('wallet_history.wallet_history_id', '>=', $wallet_history_id)
							->where('wallet_history.created_at', '>=', $wallet_reset->date_resetted)
							->sum('credit');

			$credits_back = DB::table('wallet_history')
							->join('e_wallet', 'e_wallet.wallet_id', '=', 'wallet_history.wallet_id')
                            ->where('wallet_history.wallet_id', $wallet->wallet_id)
							->where('wallet_history.where_spend', 'credits_back_from_in_network')
							// ->where('wallet_history.wallet_history_id', '>=', $wallet_history_id)
							->where('wallet_history.created_at', '>=', $wallet_reset->date_resetted)
							->sum('credit');
			$in_network_spent = $in_network_temp_spent - $credits_back;

			$temp_allocation = DB::table('e_wallet')
			->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('wallet_history.logs', ['added_by_hr'])
			// ->where('wallet_history.wallet_history_id',  '>=', $wallet_history_id)
			->where('wallet_history.created_at', '>=', $wallet_reset->date_resetted)
			->sum('wallet_history.credit');

			$deducted_allocation = DB::table('e_wallet')
			->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('wallet_history.logs', ['deducted_by_hr'])
			// ->where('wallet_history.wallet_history_id',  '>=', $wallet_history_id)
			->where('wallet_history.created_at', '>=', $wallet_reset->date_resetted)
			->sum('wallet_history.credit');
			$pro_allocation_deduction = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			// ->where('wallet_history.wallet_history_id',  '>=', $wallet_history_id)
			->where('wallet_history.created_at', '>=', $wallet_reset->date_resetted)
			->where('logs', 'pro_allocation_deduction')
			->sum('credit');
		} else {
                // get all medical credits transactions from transaction history
			$e_claim_spent = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'e_claim_transaction')
			->sum('credit');

			$in_network_temp_spent = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'in_network_transaction')
			->sum('credit');
			$credits_back = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'credits_back_from_in_network')
			->sum('credit');
			$in_network_spent = $in_network_temp_spent - $credits_back;

			$temp_allocation = DB::table('e_wallet')
			->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('logs', ['added_by_hr'])
			->sum('wallet_history.credit');

			$deducted_allocation = DB::table('e_wallet')
			->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('logs', ['deducted_by_hr'])
			->sum('wallet_history.credit');
			$pro_allocation_deduction = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('logs', 'pro_allocation_deduction')
			->sum('credit');
		}


		$allocation = $temp_allocation - $deducted_allocation - $pro_allocation_deduction;
		$current_spending = $in_network_spent + $e_claim_spent;

		// $current_balance = $allocation - $current_spending;

		$pro_allocation = DB::table('wallet_history')
									->where('wallet_id', $wallet->wallet_id)
									->where('logs', 'pro_allocation')
									->sum('credit');
		$user = DB::table('user')->where('UserID', $user_id)->first();

		if($pro_allocation > 0 && (int)$user->Active == 0) {
			$allocation = $pro_allocation;
			$current_balance = $pro_allocation - $current_spending;
			if($current_balance < 0) {
				$current_balance = 0;
			}
		} else {
			$current_balance = $allocation - $current_spending;
		}

		$current_balance = $current_balance >= 0 ? $current_balance : 0;
        // check and update user wallet
		if($wallet->balance != $current_balance) {
			DB::table('e_wallet')->where('UserID', $user_id)->update(['balance' => $current_balance, 'updated_at' => date('Y-m-d h:i:s')]);
		}

		return $current_balance;
	}

	public static function reCalculateEmployeeWellnessBalance($user_id)
	{
		$wallet = DB::table('e_wallet')->where('UserID', $user_id)->orderBy('created_at', 'desc')->first();

		$wallet_reset = DB::table('credit_reset')
		->where('id', $user_id)
		->where('user_type', 'employee')
		->where('spending_type', 'wellness')
		->orderBy('created_at', 'desc')
		->first();

		if($wallet_reset) {
			$wallet_history_id = $wallet_reset->wallet_history_id;
                // get all medical credits transactions from transaction history
			$e_claim_spent = DB::table('wellness_wallet_history')
							->join('e_wallet', 'e_wallet.wallet_id', '=', 'wellness_wallet_history.wallet_id')
                            ->where('wellness_wallet_history.wallet_id', $wallet->wallet_id)
                            ->where('wellness_wallet_history.where_spend', 'e_claim_transaction')
                            // ->where('wellness_wallet_history.wellness_wallet_history_id', '>=', $wellness_wallet_history_id)
							->where('wellness_wallet_history.created_at', '>=', $wallet_reset->date_resetted)
							->sum('credit');

			$in_network_temp_spent = DB::table('wellness_wallet_history')
							->join('e_wallet', 'e_wallet.wallet_id', '=', 'wellness_wallet_history.wallet_id')
                            ->where('wellness_wallet_history.wallet_id', $wallet->wallet_id)
							->where('wellness_wallet_history.wallet_id', $wallet->wallet_id)
							->where('wellness_wallet_history.where_spend', 'in_network_transaction')
							// ->where('wellness_wallet_history.wellness_wallet_history_id', '>=', $wellness_wallet_history_id)
							->where('wellness_wallet_history.created_at', '>=', $wallet_reset->date_resetted)
							->sum('credit');

			$credits_back = DB::table('wellness_wallet_history')
							->join('e_wallet', 'e_wallet.wallet_id', '=', 'wellness_wallet_history.wallet_id')
                            ->where('wellness_wallet_history.wallet_id', $wallet->wallet_id)
							->where('wellness_wallet_history.where_spend', 'credits_back_from_in_network')
							// ->where('wellness_wallet_history.wellness_wallet_history_id', '>=', $wellness_wallet_history_id)
							->where('wellness_wallet_history.created_at', '>=', $wallet_reset->date_resetted)
							->sum('credit');
			$in_network_spent = $in_network_temp_spent - $credits_back;

			$temp_allocation = DB::table('e_wallet')
			->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('wellness_wallet_history.logs', ['added_by_hr'])
			// ->where('wellness_wallet_history.wellness_wallet_history_id',  '>=', $wellness_wallet_history_id)
			->where('wellness_wallet_history.created_at', '>=', $wallet_reset->date_resetted)
			->sum('wellness_wallet_history.credit');

			$deducted_allocation = DB::table('e_wallet')
			->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('wellness_wallet_history.logs', ['deducted_by_hr'])
			// ->where('wellness_wallet_history.wellness_wallet_history_id',  '>=', $wellness_wallet_history_id)
			->where('wellness_wallet_history.created_at', '>=', $wallet_reset->date_resetted)
			->sum('wellness_wallet_history.credit');
			$pro_allocation_deduction = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			// ->where('wellness_wallet_history.wellness_wallet_history_id',  '>=', $wellness_wallet_history_id)
			->where('wellness_wallet_history.created_at', '>=', $wallet_reset->date_resetted)
			->where('logs', 'pro_allocation_deduction')
			->sum('credit');
		} else {
                // get all medical credits transactions from transaction history
			$e_claim_spent = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'e_claim_transaction')
			->sum('credit');

			$in_network_temp_spent = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'in_network_transaction')
			->sum('credit');
			$credits_back = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'credits_back_from_in_network')
			->sum('credit');
			$in_network_spent = $in_network_temp_spent - $credits_back;

			$temp_allocation = DB::table('e_wallet')
			->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('logs', ['added_by_hr'])
			->sum('wellness_wallet_history.credit');

			$deducted_allocation = DB::table('e_wallet')
			->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('logs', ['deducted_by_hr'])
			->sum('wellness_wallet_history.credit');
			$pro_allocation_deduction = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('logs', 'pro_allocation_deduction')
			->sum('credit');
		}


		$allocation = $temp_allocation - $deducted_allocation - $pro_allocation_deduction;
		$current_spending = $in_network_spent + $e_claim_spent;

		// $current_balance = $allocation - $current_spending;

		$pro_allocation = DB::table('wellness_wallet_history')
									->where('wallet_id', $wallet->wallet_id)
									->where('logs', 'pro_allocation')
									->sum('credit');
		$user = DB::table('user')->where('UserID', $user_id)->first();

		if($pro_allocation > 0 && (int)$user->Active == 0) {
			$allocation = $pro_allocation;
			$current_balance = $pro_allocation - $current_spending;
			if($current_balance < 0) {
				$current_balance = 0;
			}
		} else {
			$current_balance = $allocation - $current_spending;
		}

		$current_balance = $current_balance >= 0 ? $current_balance : 0;
        // check and update user wallet
		if($wallet->balance != $current_balance) {
			DB::table('e_wallet')->where('UserID', $user_id)->update(['balance' => $current_balance, 'updated_at' => date('Y-m-d h:i:s')]);
		}

		return $current_balance;
	}

	public static function getDependentsPackages($dependent_plan_id, $dependent_plan_history)
	{
		$dependent_plan = DB::table('dependent_plans')
		->where('dependent_plan_id', $dependent_plan_id)
		->first();

		$plan = DB::table('customer_plan')
		->where('customer_plan_id', $dependent_plan->customer_plan_id)
		->first();

		$hr = DB::table('customer_buy_start')
		->where('customer_buy_start_id', $plan->customer_buy_start_id)
		->first();

		if((int)$hr->wallet == 1 && (int)$hr->qr_payment == 1) {
			$wallet = 1;
		} else {
			$wallet = 0;
		}

		if($dependent_plan->account_type == "insurance_bundle") {
			if($dependent_plan->secondary_account_type == "pro_plan_bundle"){
				$secondary_account_type = "pro_plan_bundle";
			} else {
				$secondary_account_type = "insurance_bundle_lite";
			}
			$account_type = $dependent_plan->account_type;
			$secondary_account_type =  $secondary_account_type;
		} else if($dependent_plan->account_type == "trial_plan") {
			$package_group = DB::table('package_group')
			->where('default_selection', 1)
			->first();
			return $package_group;
		} else {
			$account_type = $dependent_plan->account_type;
			$secondary_account_type =  $dependent_plan->account_type;
		}

		$package_group = DB::table('package_group')
		->where('account_type', $account_type)
		->where('secondary_account_type', $secondary_account_type)
		->where('wallet', $wallet)
		->first();

		if((int)$dependent_plan_history->package_group_id !== (int)$package_group->package_group_id) {
			\DependentPlanHistory::where('dependent_plan_history_id', $dependent_plan_history->dependent_plan_history_id)->update(['package_group_id' => $package_group->package_group_id]);
		}

		$package_bundle = DB::table('package_bundle')
		->join('care_package', 'care_package.care_package_id', '=', 'package_bundle.care_package_id')
		->where('package_bundle.package_group_id', $package_group->package_group_id)
		->orderBy('care_package.position', 'desc')
		->get();
		return $package_bundle;
	}

	public static function getEnrolleePackages($customer_active_plan_id, $plan_add_on)
	{
		$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $customer_active_plan_id)->first();
		$account_type = null;
		$secondary_account_type = null;
		$wallet = 0;

		if($active_plan->account_type == "insurance_bundle") {
			if($active_plan->secondary_account_type == null) {
				$plan = DB::table('customer_plan')->where('customer_plan_id', $active_plan->plan_id)->first();
				if($plan->secondary_account_type == null) {
					$secondary_account_type = "pro_plan_bundle";
				} else if($plan->secondary_account_type == "pro_plan_bundle"){
					$secondary_account_type = "pro_plan_bundle";
				} else {
					$secondary_account_type = "insurance_bundle_lite";
				}
			} else if($active_plan->secondary_account_type == "pro_plan_bundle"){
				$secondary_account_type = "pro_plan_bundle";
			} else {
				$secondary_account_type = "insurance_bundle_lite";
			}
			$account_type = $active_plan->account_type;
			$secondary_account_type =  $secondary_account_type;
		} else if($active_plan->account_type == "trial_plan") {
			return false;
		} else {
			$account_type = $active_plan->account_type;
			$secondary_account_type =  $active_plan->account_type;
		}

		if($plan_add_on == "NIL") {
			$wallet = 0;
		} else {
			$wallet = 1;
		}

		$package_group = DB::table('package_group')
		->where('account_type', $account_type)
		->where('secondary_account_type', $secondary_account_type)
		->where('wallet', $wallet)
		->first();
		if($package_group) {
			return $package_group->package_group_id;
		} else {
			return false;
		}
	}

	public static function getUserPackages($active_plan_data, $user_id, $plan_add_on, $user_plan)
	{
		// $active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $customer_active_plan_id)->first();
		$active_plan = $active_plan_data;
		$account_type = null;
		$secondary_account_type = null;
		$wallet = 0;
		if(!$active_plan) {
			return DB::table('user_package')
			->join('care_package', 'care_package.care_package_id', '=', 'user_package.care_package_id')
			->where('user_package.user_id', $user_id)
			->get();
		}

		if($active_plan->account_type == "insurance_bundle") {
			if($active_plan->secondary_account_type == null) {
				$plan = DB::table('customer_plan')->where('customer_plan_id', $active_plan->plan_id)->first();
				if($plan->secondary_account_type == null) {
					$secondary_account_type = "pro_plan_bundle";
				} else if($plan->secondary_account_type == "pro_plan_bundle"){
					$secondary_account_type = "pro_plan_bundle";
				} else {
					$secondary_account_type = "insurance_bundle_lite";
				}
			} else if($active_plan->secondary_account_type == "pro_plan_bundle"){
				$secondary_account_type = "pro_plan_bundle";
			} else {
				$secondary_account_type = "insurance_bundle_lite";
			}
			$account_type = $active_plan->account_type;
			$secondary_account_type =  $secondary_account_type;
		} else if($active_plan->account_type == "trial_plan") {
			return DB::table('user_package')
			->join('care_package', 'care_package.care_package_id', '=', 'user_package.care_package_id')
			->where('user_package.user_id', $user_id)
			->get();
		} else {
			$account_type = $active_plan->account_type;
			$secondary_account_type =  $active_plan->account_type;
		}

		if($plan_add_on == "NIL") {
			$wallet = 0;
		} else {
			$wallet = 1;
		}

            // return $account_type.' - '.$secondary_account_type;

		$package_group = DB::table('package_group')
		->where('account_type', $account_type)
		->where('secondary_account_type', $secondary_account_type)
		->where('wallet', $wallet)
		->first();
            // return $package_group;
            // update user package plan
		if((int)$user_plan->package_group_id !== (int)$package_group->package_group_id) {
			\UserPlanType::where('user_plan_type_id', $user_plan->user_plan_type_id)->update(['package_group_id' => $package_group->package_group_id]);
		}

		$package_bundle = DB::table('package_bundle')
		->join('care_package', 'care_package.care_package_id', '=', 'package_bundle.care_package_id')
		->where('package_bundle.package_group_id', $package_group->package_group_id)
		->orderBy('care_package.position', 'asc')
		->get();
		return $package_bundle;
	}

	public static function getCompanyAvailableActivePlanId($customer_id)
	{
		$check = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$check) {
			return FALSE;
		}

		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();

		$active_plans = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->get();

		foreach ($active_plans as $key => $active) {
			$active_users = DB::table('user_plan_history')
			->where('customer_active_plan_id', $active->customer_active_plan_id)
			->where('type', 'started')
			->count();
			$pending_enrollment = $active->employees - $active_users;
			if($pending_enrollment <= 0) {
				$pending_enrollment = 0;
			} else {
				$pending_enrollment = $pending_enrollment;
				return $active->customer_active_plan_id;
			}
		}

		return $active_plans[0]->customer_active_plan_id;
	}

	public static function getCompanyAvailableDependenPlanId($customer_id)
	{
		$check = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$check) {
			return FALSE;
		}

		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();

		$dependent_plans = DB::table('dependent_plans')->where('customer_plan_id', $plan->customer_plan_id)->get();

		foreach ($dependent_plans as $key => $dependent) {
			$active_users = DB::table('dependent_plan_history')
			->where('dependent_plan_id', $dependent->dependent_plan_id)
			->where('type', 'started')
			->count();
			$pending_enrollment = $dependent->total_dependents - $active_users;
			if($pending_enrollment <= 0) {
				$pending_enrollment = 0;
			} else {
				$pending_enrollment = $pending_enrollment;
				return $dependent->dependent_plan_id;
			}
		}
	}

	public static function getUserCompanyPeakStatus($user_id)
	{
		$customer_id = self::getCustomerId($user_id);

		if(!$customer_id) {
			return false;
		}

		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if((int)$customer->peak_status == 1) {
			return true;
		} else {
			return false;
		}
	}

	public static function checkSession( )
	{
		$result = StringHelper::getJwtHrSession();
		if(!$result) {
			return array(
				'status'    => FALSE,
				'message'   => 'Need to authenticate user.'
			);
		}
		return $result;
	}

	public static function getCusomerIdToken( )
	{
		$result = StringHelper::getJwtHrSession();
		if(!$result) {
			return array(
				'status'    => FALSE,
				'message'   => 'Need to authenticate user.'
			);
		}
		return $result->customer_buy_start_id;
	}

	public static function validIdentification($number)
	{
		return true;

		// if (strlen($number) !== 9) {
		// 	return false;
		// }
		// $newNumber = strtoupper($number);
		// $icArray = [];
		// for ($i = 0; $i < 9; $i++) {
		// 	$icArray[$i] = $newNumber{$i};
		// }
		// $icArray[1] = intval($icArray[1], 10) * 2;
		// $icArray[2] = intval($icArray[2], 10) * 7;
		// $icArray[3] = intval($icArray[3], 10) * 6;
		// $icArray[4] = intval($icArray[4], 10) * 5;
		// $icArray[5] = intval($icArray[5], 10) * 4;
		// $icArray[6] = intval($icArray[6], 10) * 3;
		// $icArray[7] = intval($icArray[7], 10) * 2;

		// $weight = 0;
		// for ($i = 1; $i < 8; $i++) {
		// 	$weight += $icArray[$i];
		// }
		// $offset = ($icArray[0] === "T" || $icArray[0] == "G") ? 4 : 0;
		// $temp = ($offset + $weight) % 11;

		// $st = ["J", "Z", "I", "H", "G", "F", "E", "D", "C", "B", "A"];
		// $fg = ["X", "W", "U", "T", "R", "Q", "P", "N", "M", "L", "K"];

		// $theAlpha = "";
		// if ($icArray[0] == "S" || $icArray[0] == "T") {
		// 	$theAlpha = $st[$temp];
		// } else if ($icArray[0] == "F" || $icArray[0] == "G") {
		// 	$theAlpha = $fg[$temp];
		// }
		// return ($icArray[8] === $theAlpha);
	}

	public static function isDate($string) {
	    $matches = array();
	    $pattern = '/^([0-9]{1,2})\\/([0-9]{1,2})\\/([0-9]{4})$/';
	    if (!preg_match($pattern, $string, $matches)) return false;
	    if (!checkdate($matches[2], $matches[1], $matches[3])) return false;
	    return true;
	}

	public static function validateStartDate($dateStr)
	{ 
		return (bool)strtotime($dateStr);
            // date_default_timezone_set('UTC');
            // $date = DateTime::createFromFormat('Y-m-d', $dateStr);
            // return $date && ($date->format('Y-m-d') === $dateStr);
	}


        // public static function validateStartDate($date)
        // {
        //     return (bool)strtotime($date);
        // }
	public static function getCompanyPlanDates($customer_id) 
	{
		$plan = DB::table('customer_plan')
		->where('customer_buy_start_id', $customer_id)
		->orderBy('created_at', 'desc')
		->first();

		$active_plan = DB::table('customer_active_plan')
		->where('plan_id', $plan->customer_plan_id)
		->first();

		if((int)$active_plan->plan_extention_enable == 1) {
			$plan_extention = DB::table('plan_extensions')
			->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
			->first();
			if($plan_extention) {
				if($plan_extention->duration || $plan_extention->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$plan_extention->duration, strtotime($plan_extention->plan_start)));
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan_extention->plan_start)));
				}
			} else {
				if($active_plan->duration || $active_plan->duration != "") {
					$end_plan_date = date('Y-m-d', strtotime('+'.$active_plan->duration, strtotime($plan->plan_start)));
				} else {
					$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
				}
			}
		} else {
			if($active_plan->duration || $active_plan->duration != "") {
				$end_plan_date = date('Y-m-d', strtotime('+'.$active_plan->duration, strtotime($plan->plan_start)));
			} else {
				$end_plan_date = date('Y-m-d', strtotime('+1 year', strtotime($plan->plan_start)));
			}
		}

		$end_plan_date = date('Y-m-d', strtotime('-1 day', strtotime($end_plan_date)));

		return array('plan_start' => $plan->plan_start, 'plan_end' => $end_plan_date, 'customer_plan_id' => $plan->customer_plan_id);
	}

	public static function checkDuplicateNRIC($nric)
	{
		$user = DB::table('user')
					->where('NRIC', 'like', '%'.$nric.'%')
					->where('UserType', 5)
					->where('Active', 1)
					->first();

		// $enrolled = DB::table('customer_temp_enrollment')
		// 				->where('nric', 'like', '%'.$nric.'%')
		// 				->where('enrolled_status', 'true')
		// 				->first();

		if($user) {
			return true;
		} else {
			return false;
		}
	}

	public static function validateDate($date, $format = 'd-m-Y')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}

	public static function enrollmentEmployeeValidation($user, $except_enrolle_email_validation)
	{
		$customer_id = self::getCusomerIdToken();
		$mobile_error = false;
		$mobile_message = '';
		$mobile_area_error = false;
		$mobile_area_message = '';
		$email_error = false;
		$email_message = '';

		if(is_null($user['mobile'])) {
			$mobile_error = true;
			$mobile_message = '*Mobile Phone is empty';
		} else {
			// check mobile number
			$check_mobile = DB::table('user')
								->where('UserType', 5)
								->where('PhoneNo', $user['mobile'])
								->where('Active', 1)
								->first();
			if($check_mobile) {
				$mobile_error = true;
				$mobile_message = '*Mobile Phone No already taken.';
			} else {
				$mobile_error = false;
				$mobile_message = '';
			}
		}

		if(!empty($user['email'])) {
			$check_user = DB::table('user')->where('Email', $user['email'])->where('Active', 1)->where('UserType', 5)->count();

			if(filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
				$email_error = false;
				$email_message = '';
			} else {
				$email_error = true;
				$email_message = '*Error email format.';
			}
	            // validations
			if($check_user > 0) {
				$email_error = true;
				$email_message = '*Email Already taken.';
			}

			if(!$except_enrolle_email_validation) {
				$check_temp_user = DB::table('customer_temp_enrollment')
				->where('email', $user['email'])
				->where('enrolled_status', 'false')
				->count();
				if($check_temp_user > 0) {
					$email_error = true;
					$email_message = '*Email Already taken.';
				}
			}			
		}


		if(is_null($user['fullname'])) {
			$full_name_error = true;
			$full_name_message = '*Full Name is empty';
		} else {
			$full_name_error = false;
			$full_name_message = '';
		}

		if(isset($user['mobile_area_code']) && is_null($user['mobile_area_code']) || isset($inpt['mobile_country_code']) && is_null($user['mobile_country_code'])) {
			$mobile_area_error = true;
			$mobile_area_message = '*Mobile Country Code is empty';
		} else {
			$mobile_area_error = false;
			$mobile_area_message = '';
		}

		if(is_null($user['dob'])) {
			$dob_error = true;
			$dob_message = '*Date of Birth is empty';
		} else {
			$validate = self::isDate($user['dob']);
			if(!$validate) {
				$dob_error = true;
				$dob_message = '*Date of Birth is not a valid date.';
			} else {
				$dob_error = false;
				$dob_message = '';
			}
		}

		// if(is_null($user['postal_code'])) {
		// 	$postal_code_error = true;
		// 	$postal_code_message = '*Job is empty';
		// } else {
			$postal_code_error = false;
			$postal_code_message = '';
		// }

		$nric_error = false;
		$nric_message = '';

		// if(is_null($user['nric'])) {
		// 	$nric_error = true;
		// 	$nric_message = '*NRIC/FIN is empty';
		// } else {
		// 	if(strlen($user['nric']) < 9 || strlen($user['nric']) > 12) {
		// 		$nric_error = true;
		// 		$nric_message = '*NRIC/FIN is must be 9 or 12 characters';
		// 	} else {
		// 		if(!self::validIdentification($user['nric'])) {
		// 			$nric_error = true;
		// 			$nric_message = '*NRIC/FIN is must be valid';
		// 		}

		// 		// validate nric existence
		// 		$validate_nric = self::checkDuplicateNRIC($user['nric']);
		// 		if($validate_nric) {
		// 			$nric_error = true;
		// 			$nric_message = '*NRIC/FIN is assigned to other user. NRIC/FIN is unique for everyone.';
		// 		}
		// 	}
		// }

		if(is_null($user['plan_start'])) {
			$start_date_error = true;
			$start_date_message = '*Start Date is empty';
			$start_date_result = false;
		} else {
			$validate = self::isDate($user['plan_start']);
			if(!$validate) {
				$start_date_error = true;
				$start_date_message = '*Start Date is invalid date.';
				$start_date_result = false;
			} else {
				$plan = self::getCompanyPlanDates($customer_id);
				$start = strtotime($plan['plan_start']);
				$end = strtotime($plan['plan_end']);
				$plan_start = strtotime(date_format(date_create_from_format('d/m/Y', $user['plan_start']), 'Y-m-d'));
				if($plan_start >= $start && $plan_start <= $end) {
					$start_date_error = false;
					$start_date_message = '';
				} else {
					$start_date_error = true;
					$start_date_message = "*Start Date must be between company's plan start and plan end (".date('d/m/Y', $start)." - ".date('d/m/Y', $end).").";
					$start_date_result = false;
				}
			}
		}


		if(!isset($user['medical_credits']) || is_null($user['medical_credits'])) {
			$credit_medical_amount = 0;
			$credits_medical_error = false;
			$credits_medical_message = '';
		} else {
			if(is_numeric($user['medical_credits'])) {
				$credits_medical_error = false;
				$credits_medical_message = '';
			} else {
				$credits_medical_error = true;
				$credits_medical_message = 'Credits is not a number.';                
			}
		}

		if(!isset($user['wellness_credits']) || is_null($user['wellness_credits'])) {
			$credit_wellness_amount = 0;
			$credits_wellness_error = false;
			$credits_wellnes_message = '';
		} else {
			if(is_numeric($user['wellness_credits'])) {
				$credits_wellness_error = false;
				$credits_wellnes_message = '';
			} else {
				$credits_wellness_error = true;
				$credits_wellnes_message = 'Credits is not a number.';
			}
		}

		if($email_error || $full_name_error || $dob_error || $mobile_error || $postal_code_error || $mobile_area_error || $credits_medical_error || $credits_wellness_error || $start_date_error) {
			$error_status = true;
		} else {
			$error_status = false;
		}

		return array(
			'error'                 => $error_status,
			"email_error"           => $email_error,
			"email_message"         => $email_message,
			"full_name_error"      => $full_name_error,
			"full_name_message"    => $full_name_message,
			"dob_error"             => $dob_error,
			"dob_message"           => $dob_message,
			"mobile_error"          => $mobile_error,
			"mobile_message"        => $mobile_message,
			"mobile_area_error"     => $mobile_area_error,
			"mobile_area_message"   => $mobile_area_message,
			"postal_code_error"       => $postal_code_error,
			"postal_code_message"     => $postal_code_message,
			"credits_medical_error" => $credits_medical_error,
			"credits_medical_message" => $credits_medical_message,
			"credits_wellness_error" => $credits_wellness_error,
			"credits_wellnes_message" => $credits_wellnes_message,
			"start_date_error"      => $start_date_error,
			"start_date_message"    => $start_date_message
		);
	}

		public static function enrollmentDepedentValidation($user)
		{
			$customer_id = self::getCusomerIdToken();
			if(is_null($user['fullname'])) {
				$full_name_error = true;
				$full_name_message = '*First Name is empty';
			} else {
				$full_name_error = false;
				$full_name_message = '';
			}

			// if(is_null($user['last_name'])) {
			// 	$last_name_error = true;
			// 	$last_name_message = '*Last Name is empty';
			// } else {
			// 	$last_name_error = false;
			// 	$last_name_message = '';
			// }

			if(is_null($user['dob'])) {
				$dob_error = true;
				$dob_message = '*Date of Birth is empty';
			} else {
				$dob_error = false;
				$dob_message = '';
			}

			// if(is_null($user['nric'])) {
			// 	$nric_error = true;
			// 	$nric_message = '*NRIC/FIN is empty';
			// } else {
			// 	if(strlen($user['nric']) < 9 || strlen($user['nric']) > 12) {
			// 		$nric_error = true;
			// 		$nric_message = '*NRIC/FIN is must be 9 or 12 characters';
			// 	} else {
			// 		$nric_error = false;
			// 		$nric_message = '';
			// 	}
			// }

			$relationship_error = false;
			$relationship_message = '';

			if(!empty($user['relationship'])) {
				$rel = ["spouse", "child", "family"];
				if(!in_array($user['relationship'], $rel)) {
					$relationship_error = true;
					$relationship_message = '*Relationship type should be either Spouse, Child or Family.';
				} else {
					
				}
			}

			if(is_null($user['plan_start'])) {
				$start_date_error = true;
				$start_date_message = '*Start Date is empty';
				$start_date_result = false;
			} else {
				$validate = self::validateStartDate($user['plan_start']);
				if(!$validate) {
					$start_date_error = true;
					$start_date_message = '*Start Date is invalid date.';
					$start_date_result = false;
				} else {
					$plan = self::getCompanyPlanDates($customer_id);
					$start = strtotime($plan['plan_start']);
					$end = strtotime($plan['plan_end']);
					$plan_start = strtotime($user['plan_start']);
					if($plan_start >= $start && $plan_start <= $end) {
						$start_date_error = false;
						$start_date_message = '';
					} else {
						$start_date_error = true;
						$start_date_message = "*Start Date must be between company's plan start and plan end (".date('d/m/Y', $start)." - ".date('d/m/Y', $end).").";
						$start_date_result = false;
					}
				}
			}

			if($full_name_error || $dob_error || $start_date_error || $relationship_error) {
				$error_status = true;
			} else {
				$error_status = false;
			}

			return array(
				'error'                 => $error_status,
				"full_name_error"      => $full_name_error,
				"full_name_message"    => $full_name_message,
				"dob_error"             => $dob_error,
				"dob_message"           => $dob_message,
				"start_date_error"      => $start_date_error,
				"start_date_message"    => $start_date_message,
				"relationship_message"   => $relationship_message,
				"relationship_error"   => $relationship_error
			);
		}

		public static function createEmployee($temp_enrollment_id, $customer_id)
		{
			// get admin session from mednefits admin login
			$admin_id = Session::get('admin-session-id');
			$hr_data = StringHelper::getJwtHrSession();
			$hr_id = $hr_data->hr_dashboard_id;

			$data_enrollee = DB::table('customer_temp_enrollment')
			->where('temp_enrollment_id', $temp_enrollment_id)
			->first();
			if(!$data_enrollee) {
				return array('status' => false, 'message' => 'Enrollee does not exist.');
			}

			$planned = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();
			$plan_status = DB::table('customer_plan_status')->where('customer_plan_id', $planned->customer_plan_id)->orderBy('created_at', 'desc')->first();

			$total = $plan_status->employees_input - $plan_status->enrolled_employees;
            // return $total;
			if($total <= 0) {
				return array(
					'status'    => false,
					'message'   => "We realised the current headcount you wish to enroll is over the current vacant member seat/s."
				);
			}

			$user = new User();

			$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();
            // try {
			$customer_active_plan_id = PlanHelper::getCompanyAvailableActivePlanId($customer_id);
			if(!$customer_active_plan_id) {
				$active_plan = DB::table('customer_active_plan')->where('customer_start_buy_id', $customer_id)->orderBy('created_at', 'desc')->first();
			} else {
				$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $customer_active_plan_id)->first();
			}

			$corporate = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();

			if($data_enrollee->start_date != NULL) {
				$temp_start_date = \DateTime::createFromFormat('d/m/Y', $data_enrollee->start_date);
				$start_date = $temp_start_date->format('Y-m-d');
			} else {
				$start_date = date('Y-m-d', strtotime($active_plan->plan_start));
			}

			if($data_enrollee->email) {
				$communication_type = "email";
			} else if($data_enrollee->mobile) {
				$communication_type = "sms";
			} else {
				$communication_type = "email";
			}

			$password = StringHelper::get_random_password(8);
			$dob = date_format(date_create_from_format('d/m/Y', $data_enrollee->dob), 'Y-m-d');
			$data = array(
				'Name'          => $data_enrollee->first_name,
				'Password'      => md5($password),
				'Email'         => $data_enrollee->email,
				'PhoneNo'       => (int)$data_enrollee->mobile,
				'PhoneCode'     => $data_enrollee->mobile_area_code ? '+'.$data_enrollee->mobile_area_code : "+65",
				'NRIC'          => null,
				'Job_Title'     => $data_enrollee->job_title,
				'Active'        => 1,
				'Zip_Code'      => $data_enrollee->postal_code,
				'DOB'           => $dob,
				'pending'		=> 0,
				'account_update_status'		=> 1,
				'account_update_date' => date('Y-m-d H:i:s'),
				'account_already_update'	=> 1,
				'communication_type'	=> $communication_type
			);

			$user_id = $user->createUserFromCorporate($data);

			$corporate_member = array(
				'corporate_id'  => $corporate->corporate_id,
				'user_id'       => $user_id,
				'first_name'    => $data_enrollee->first_name,
				'last_name'     => $data_enrollee->last_name,
				'type'          => 'member',
				'created_at'    => date('Y-m-d h:i:s'),
				'updated_at'    => date('Y-m-d h:i:s')
			);

			DB::table('corporate_members')->insert($corporate_member);
			$plan_type = new UserPlanType();


			$plan_add_on = self::getCompanyAccountTypeEnrollee($customer_id);
			$result = self::getEnrolleePackages($active_plan->customer_active_plan_id, $plan_add_on);
			
			$group_package = new PackagePlanGroup();
			$bundle = new Bundle();
			$user_package = new UserPackage();
			
			if(!$result) {
				$group_package_id = $group_package->getPackagePlanGroupDefault();
			} else {
				$group_package_id = $result;
			}

			$result_bundle = $bundle->getBundle($group_package_id);

			foreach ($result_bundle as $key => $value) {
				$user_package->createUserPackage($value->care_package_id, $user_id);
			}

			$plan_type_data = array(
				'user_id'           => $user_id,
				'package_group_id'  => $group_package_id,
				'duration'          => '1 year',
				'plan_start'        => $start_date,
				'active_plan_id'    => $active_plan->customer_active_plan_id
			);

			$plan_type->createUserPlanType($plan_type_data);

			$user_plan_history = new UserPlanHistory();
			$user_plan_history_data = array(
				'user_id'       => $user_id,
				'type'          => "started",
				'date'          => $start_date,
				'customer_active_plan_id' => $active_plan->customer_active_plan_id
			);

			$user_plan_history->createUserPlanHistory($user_plan_history_data);

                // check company credits
			$customer = DB::table('customer_credits')->where('customer_id', $customer_id)->first();

			if($data_enrollee->credits > 0) {
                    // medical credits
				if($customer->balance >= $data_enrollee->credits) {

					$result_customer_active_plan = self::allocateCreditBaseInActivePlan($customer_id, $data_enrollee->credits, "medical");

					if($result_customer_active_plan) {
						$customer_active_plan_id = $result_customer_active_plan;
					} else {
						$customer_active_plan_id = NULL;
					}

                        // give credits
					$wallet_class = new Wallet();
					$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
					$update_wallet = $wallet_class->addCredits($user_id, $data_enrollee->credits);

					$employee_logs = new WalletHistory();

					$wallet_history = array(
						'wallet_id'     => $wallet->wallet_id,
						'credit'            => $data_enrollee->credits,
						'logs'              => 'added_by_hr',
						'running_balance'   => $data_enrollee->credits,
						'customer_active_plan_id' => $customer_active_plan_id,
						'currency_type'		=> $customer->currency_type
					);

					$employee_logs->createWalletHistory($wallet_history);
					$customer_credits = new CustomerCredits();

					$customer_credits_result = $customer_credits->deductCustomerCredits($customer->customer_credits_id, $data_enrollee->credits);
					$customer_credits_left = DB::table('customer_credits')->where('customer_credits_id', $customer->customer_credits_id)->first();
					$data['medical_credit_history'] = $wallet_history;
					if($customer_credits_result) {
						$company_deduct_logs = array(
							'customer_credits_id'   => $customer->customer_credits_id,
							'credit'                => $data_enrollee->credits,
							'logs'                  => 'added_employee_credits',
							'user_id'               => $user_id,
							'running_balance'       => $customer->balance - $data_enrollee->credits,
							'customer_active_plan_id' => $customer_active_plan_id,
							'currency_type'		=> $customer->currency_type
						);

						$customer_credit_logs = new CustomerCreditLogs( );
						$customer_credit_logs->createCustomerCreditLogs($company_deduct_logs);
					}
				}
			}

			if($data_enrollee->wellness_credits > 0) {
                    // wellness credits
				if($customer->wellness_credits >= $data_enrollee->wellness_credits) {
					$result_customer_active_plan = self::allocateCreditBaseInActivePlan($customer_id, $data_enrollee->wellness_credits, "wellness");

					if($result_customer_active_plan) {
						$customer_active_plan_id = $result_customer_active_plan;
					} else {
						$customer_active_plan_id = NULL;
					}
                        // give credits
					$wallet_class = new Wallet();
					$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
					$update_wallet = $wallet_class->addWellnessCredits($user_id, $data_enrollee->wellness_credits);

					$wallet_history = array(
						'wallet_id'     => $wallet->wallet_id,
						'credit'        => $data_enrollee->wellness_credits,
						'logs'          => 'added_by_hr',
						'running_balance'   => $data_enrollee->wellness_credits,
						'customer_active_plan_id' => $customer_active_plan_id,
						'currency_type'		=> $customer->currency_type
					);

					\WellnessWalletHistory::create($wallet_history);
					$customer_credits = new CustomerCredits();
					$customer_credits_result = $customer_credits->deductCustomerWellnessCredits($customer->customer_credits_id, $data_enrollee->wellness_credits);
					$data['wellness_credit_history'] = $wallet_history;
					if($customer_credits_result) {
						$company_deduct_logs = array(
							'customer_credits_id'   => $customer->customer_credits_id,
							'credit'                => $data_enrollee->wellness_credits,
							'logs'                  => 'added_employee_credits',
							'user_id'               => $user_id,
							'running_balance'       => $customer->wellness_credits - $data_enrollee->wellness_credits,
							'customer_active_plan_id' => $customer_active_plan_id,
							'currency_type'		=> $customer->currency_type
						);
						$customer_credits_logs = new CustomerWellnessCreditLogs();
						$customer_credits_logs->createCustomerWellnessCreditLogs($company_deduct_logs);
					}
				}
			}

			$Customer_PlanStatus = new CustomerPlanStatus( );
			$Customer_PlanStatus->addjustCustomerStatus('enrolled_employees', $active_plan->plan_id, 'increment', 1);
			DB::table('customer_temp_enrollment')
			->where('temp_enrollment_id', $temp_enrollment_id)
			->update(['enrolled_status' => "true", 'active_plan_id' => $active_plan->customer_active_plan_id]);

                    // check if there is a plan tier
			if($data_enrollee->plan_tier_id) {
                    // check plan tier if exist
				$plan_tier = DB::table('plan_tiers')
				->where('plan_tier_id', $data_enrollee->plan_tier_id)
				->first();
				if($plan_tier) {
					$plan_tier_user = new PlanTierUsers();
					$tier_history = array(
						'plan_tier_id'              => $data_enrollee->plan_tier_id,
						'user_id'                   => $user_id,
						'status'                    => 1
					);

					$plan_tier_user->createData($tier_history);
                        // increment member head count
					$plan_tier_class = new PlanTier();
					$plan_tier_class->increamentMemberEnrolledHeadCount($data_enrollee->plan_tier_id);
				}
			}

                // enrolle dependent if any
			self::enrollDependents($temp_enrollment_id, $customer_id, $user_id, $planned->customer_plan_id);
                // send email to new employee
			$company = DB::table('corporate')->where('corporate_id', $corporate->corporate_id)->first();
			$total_dependents_count = DB::table('dependent_temp_enrollment')
			->where('employee_temp_id', $temp_enrollment_id)
			->count();

			if($communication_type == "email") {
				if($data_enrollee->email) {
					$email_data = [];
					$email_data['company']   = ucwords($company->company_name);
					$email_data['emailName'] = $data_enrollee->first_name;
					$email_data['emailTo']   = $data_enrollee->email;
					$email_data['email'] = $data_enrollee->mobile ? $data_enrollee->mobile : $data_enrollee->email;
		                // $email_data['email'] = 'allan.alzula.work@gmail.com';
					$email_data['emailPage'] = 'email-templates.latest-templates.mednefits-welcome-member-enrolled';
					$email_data['start_date'] = date('d F Y', strtotime($start_date));
					$email_data['name'] = $data_enrollee->first_name;
					$email_data['plan'] = $active_plan;
					$email_data['emailSubject'] = "WELCOME TO MEDNEFITS CARE";
					$email_data['pw'] = $password;
					EmailHelper::sendEmail($email_data);
				} else {
					if($data_enrollee->mobile) {
						$user = DB::table('user')->where('UserID', $user_id)->first();
						$phone = SmsHelper::newformatNumber($user);

						if($phone) {
							$compose = [];
							$compose['name'] = $data_enrollee->first_name;
							$compose['company'] = $company->company_name;
							$compose['plan_start'] = date('F d, Y', strtotime($start_date));
							$compose['email'] = null;
							$compose['nric'] = $data_enrollee->mobile;
							$compose['password'] = $password;
							$compose['phone'] = $phone;
							$compose['sms_type'] = "LA";
							$compose['message'] = SmsHelper::formatWelcomeEmployeeMessage($compose);
							$result_sms = SmsHelper::sendSms($compose);
						}
					}
				}
			} else if($communication_type == "sms"){
				if($data_enrollee->mobile) {
					$user = DB::table('user')->where('UserID', $user_id)->first();
					$phone = SmsHelper::newformatNumber($user);

					if($phone) {
						$compose = [];
						$compose['name'] = $data_enrollee->first_name.' '.$data_enrollee->last_name;
						$compose['company'] = $company->company_name;
						$compose['plan_start'] = date('F d, Y', strtotime($start_date));
						$compose['email'] = null;
						$compose['nric'] = $data_enrollee->mobile;
						$compose['password'] = $password;
						$compose['phone'] = $phone;

						$compose['message'] = SmsHelper::formatWelcomeEmployeeMessage($compose);
						$result_sms = SmsHelper::sendSms($compose);
					}
				} else {
					$email_data = [];
					$email_data['company']   = ucwords($company->company_name);
					$email_data['emailName'] = $data_enrollee->first_name;
					$email_data['emailTo']   = $data_enrollee->email;
					$email_data['email'] = $data_enrollee->mobile ? $data_enrollee->mobile : $data_enrollee->email;
		                // $email_data['email'] = 'allan.alzula.work@gmail.com';
					$email_data['emailPage'] = 'email-templates.latest-templates.mednefits-welcome-member-enrolled';
					$email_data['start_date'] = date('d F Y', strtotime($start_date));
					$email_data['name'] = $data_enrollee->first_name;
					$email_data['plan'] = $active_plan;
					$email_data['emailSubject'] = "WELCOME TO MEDNEFITS CARE";
					$email_data['pw'] = $password;
					EmailHelper::sendEmail($email_data);
				}	
			} else {
				$email_data = [];
				$email_data['company']   = ucwords($company->company_name);
				$email_data['emailName'] = $data_enrollee->first_name;
				$email_data['emailTo']   = $data_enrollee->email ? $data_enrollee->email : 'info@medicloud.sg';
				$email_data['email'] = 'info@medicloud.sg';
				$email_data['emailPage'] = 'email-templates.latest-templates.mednefits-welcome-member-enrolled';
				$email_data['start_date'] = date('d F Y', strtotime($start_date));
				$email_data['name'] = $data_enrollee->first_name;
				$email_data['plan'] = $active_plan;
				$email_data['emailSubject'] = "WELCOME TO MEDNEFITS CARE";
				$email_data['pw'] = $password;
				EmailHelper::sendEmail($email_data);
			}

			if($admin_id) {
				$data['user_id'] = $user_id;

				$admin_logs = array(
            'admin_id'  => $admin_id,
            'admin_type' => 'mednefits',
            'type'      => 'admin_hr_created_employee',
            'data'      => SystemLogLibrary::serializeData($data)
        );
        SystemLogLibrary::createAdminLog($admin_logs);
			} else {
				$data['user_id'] = $user_id;
				$admin_logs = array(
              'admin_id'  => $hr_id,
              'admin_type' => 'hr',
              'type'      => 'admin_hr_created_employee',
              'data'      => SystemLogLibrary::serializeData($data)
          );
          SystemLogLibrary::createAdminLog($admin_logs);
			}

			return array('status' => true, 'message' => 'Employee Enrolled.', 'total_dependents_enrolled' => $total_dependents_count, 'total_employee_enrolled' => 1);
            // } catch(Exception $e) {
            //     return $e->getMessage();
            // }
		}

		public static function enrollDependents($temp_enrollment_id, $customer_id, $employee_id, $customer_plan_id)
		{
			// get admin session from mednefits admin login
			$admin_id = Session::get('admin-session-id');
			$hr_data = StringHelper::getJwtHrSession();
			$hr_id = $hr_data->hr_dashboard_id;

			$dependent_enrollees = DB::table('dependent_temp_enrollment')
			->where('employee_temp_id', $temp_enrollment_id)
			->get();

			if(sizeof($dependent_enrollees) > 0) {
                // process dependents
				$user = new User();
				$group_package = new PackagePlanGroup();
				$family = new FamilyCoverageAccounts();
				$dependent_plan_history = new DependentPlanHistory();
				$plan_tier_user = new PlanTierUsers();
				$dependent_enrollment = new DependentTempEnrollment( );
				$dependent_plan_status = new DependentPlanStatus( );

				foreach ($dependent_enrollees as $key => $dependent) {
					$data = array(
						'Name'          => $dependent->first_name,
						'Email'         => 'mednefits',
						'PhoneCode'     => '+65',
						'NRIC'          => null,
						'Active'        => 1,
						'DOB'           => $dependent->dob
					);

					$package_group = self::getDependentPackageGroup($dependent->dependent_plan_id);

					if(!$package_group) {
						$group_package_id = $group_package->getPackagePlanGroupDefault();
					} else {
						$group_package_id = $package_group->package_group_id;
					}

					$user_id = $user->createUserFromDependent($data);

					if($user_id) {
                        // create family account
						$family_data = array(
							'owner_id'      => $employee_id,
							'user_id'       => $user_id,
							'user_type'     => 'dependent',
							'relationship'  => $dependent->relationship
						);

						$result_family = $family->createData($family_data);

						if($result_family) {
							$data['family_data'] = $family_data;
							$history = array(
								'user_id'           => $user_id,
								'dependent_plan_id' => $dependent->dependent_plan_id,
								'package_group_id'  => $group_package_id,
								'plan_start'        => date('Y-m-d', strtotime($dependent->plan_start)),
								'duration'          => '12 months',
								'fixed'             => 1,
								'type'              => 'started'
							);

							$result_dependent_history = $dependent_plan_history->createData($history);

							if($result_dependent_history) {
								$data['dependent_history'] = $history;
                // check if there is a plan tier id
								if($dependent->plan_tier_id) {
									$tier_history = array(
										'plan_tier_id'              => $dependent->plan_tier_id,
										'user_id'                   => $user_id,
										'status'                    => 1
									);

									$plan_tier_user->createData($tier_history);
                                    // increment member head count
									$plan_tier_class = new PlanTier();
									$plan_tier_class->increamentDependentEnrolledHeadCount($dependent->plan_tier_id);

								}
                                // update dependent enrollmend
								$dependent_enrollment->updateEnrollementStatus($dependent->dependent_temp_id);

								$dependent_plan_status->incrementEnrolledDependents($customer_plan_id);
							}
							
							if($admin_id) {
								$data['user_id'] = $user_id;

								$admin_logs = array(
				            'admin_id'  => $admin_id,
				            'admin_type' => 'mednefits',
				            'type'      => 'admin_hr_created_dependent',
				            'data'      => SystemLogLibrary::serializeData($data)
				        );
				        SystemLogLibrary::createAdminLog($admin_logs);
							} else {
								$data['user_id'] = $user_id;
								$admin_logs = array(
				              'admin_id'  => $hr_id,
				              'admin_type' => 'hr',
				              'type'      => 'admin_hr_created_dependent',
				              'data'      => SystemLogLibrary::serializeData($data)
				          );
				          SystemLogLibrary::createAdminLog($admin_logs);
							}
						}
					}
				}
			}
		}

		public static function getDependentPackageGroup($dependent_plan_id)
		{
			$dependent_plan = DB::table('dependent_plans')
			->where('dependent_plan_id', $dependent_plan_id)
			->first();
			if(!$dependent_plan) {
				return false;
			}
			$plan = DB::table('customer_plan')
			->where('customer_plan_id', $dependent_plan->customer_plan_id)
			->first();

			$hr = DB::table('customer_buy_start')
			->where('customer_buy_start_id', $plan->customer_buy_start_id)
			->first();

			if((int)$hr->wallet == 1 && (int)$hr->qr_payment == 1) {
				$wallet = 1;
			} else {
				$wallet = 0;
			}

			if($dependent_plan->account_type == "insurance_bundle") {
				if($dependent_plan->secondary_account_type == "pro_plan_bundle"){
					$secondary_account_type = "pro_plan_bundle";
				} else {
					$secondary_account_type = "insurance_bundle_lite";
				}
				$account_type = $dependent_plan->account_type;
				$secondary_account_type =  $secondary_account_type;
			} else if($dependent_plan->account_type == "trial_plan") {
				$package_group = DB::table('package_group')
				->where('default_selection', 1)
				->first();
				return $package_group;
			} else {
				$account_type = $dependent_plan->account_type;
				$secondary_account_type =  $dependent_plan->account_type;
			}

			$package_group = DB::table('package_group')
			->where('account_type', $account_type)
			->where('secondary_account_type', $secondary_account_type)
			->where('wallet', $wallet)
			->first();
			return $package_group;
		}

		public static function allocateCreditBaseInActivePlan($id, $credit, $type)
		{
			$plan = DB::table('customer_plan')->where('customer_buy_start_id', $id)->orderBy('created_at', 'desc')->first();

			$active_plans = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->get();

			foreach($active_plans as $key => $active) {
				$total_medical_allocation = 0;
				$total_wellness_allocation = 0;

				$active->total_unallocated_medical = 0;
				$active->total_unallocated_wellness = 0;

				if($type == "medical") {
                    // get allocated amount for medical
					$total_allocated_amount_medical = DB::table('customer_credit_logs')->whereNotNull('customer_active_plan_id')->where('customer_active_plan_id', $active->customer_active_plan_id)->where('logs', 'admin_added_credits')->sum('credit');
					$active->total_allocated_amount_medical = $total_allocated_amount_medical;
					
				} else {
					$total_allocated_amount_wellness = DB::table('customer_wellness_credits_logs')->whereNotNull('customer_active_plan_id')->where('customer_active_plan_id', $active->customer_active_plan_id)->where('logs', 'admin_added_credits')->sum('credit');
					$active->total_allocated_amount_wellness = $total_allocated_amount_wellness;
				}



                // get users by customer active plan
                // $users_id[] = DB::table('user_plan_history')->where('customer_active_plan_id', $active->customer_active_plan_id)->pluck('user_id');

                // if(sizeof($users_id) > 0) {
                    // medical
				if($type == "medical") {
					$temp_medical_allocation = DB::table('e_wallet')
					->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
					->where('wallet_history.logs', 'added_by_hr')
					->where('wallet_history.customer_active_plan_id', $active->customer_active_plan_id)
                                                // ->whereIn('e_wallet.UserID', $users_id)
					->sum('wallet_history.credit');

					$temp_medical_deduct_allocation = DB::table('e_wallet')
					->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
					->where('wallet_history.logs', 'deducted_by_hr')
					->where('wallet_history.customer_active_plan_id', $active->customer_active_plan_id)
                                                // ->whereIn('e_wallet.UserID', $users_id)
					->sum('wallet_history.credit');
					$total_medical_allocation = $temp_medical_allocation - $temp_medical_deduct_allocation;
				} else {
                        // wellness
					$temp_wellness_allocation = DB::table('e_wallet')
					->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
					->where('wellness_wallet_history.logs', 'added_by_hr')
					->where('wellness_wallet_history.customer_active_plan_id', $active->customer_active_plan_id)
                                                // ->whereIn('e_wallet.UserID', $users_id)
					->sum('wellness_wallet_history.credit');

					$temp_wellness_deduct_allocation = DB::table('e_wallet')
					->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
					->where('wellness_wallet_history.logs', 'deducted_by_hr')
					->where('wellness_wallet_history.customer_active_plan_id', $active->customer_active_plan_id)
                                                // ->whereIn('e_wallet.UserID', $users_id)
					->sum('wellness_wallet_history.credit');
					$total_wellness_allocation = $temp_wellness_allocation - $temp_wellness_deduct_allocation;
				}
				

                // } else {
                //  if($type == "medical") {
                //      $active->total_unallocated_medical = 0;
                //  } else {
                //      $active->total_unallocated_wellness = 0;
                //  }
                // }

				$active->total_unallocated_medical = $total_medical_allocation;
				$active->total_unallocated_wellness = $total_wellness_allocation;

				if($type == "medical") {
					$active->total_unallocated_medical = $total_allocated_amount_medical - $total_medical_allocation;
				} else {
					$active->total_unallocated_wellness = $total_allocated_amount_wellness - $total_wellness_allocation;
				}

				if($type == "medical") {
					if($active->total_unallocated_medical > 0) {
						if($active->total_unallocated_medical >= $credit) {
							return $active->customer_active_plan_id;
						}
					}
				} else {
					if($active->total_unallocated_wellness > 0) {
						if($active->total_unallocated_wellness >= $credit) {
							return $active->customer_active_plan_id;
						}
					}
				}
			}
		}
		
		public static function memberMedicalAllocatedCredits($wallet_id, $user_id)
		{
			
			$get_allocation = 0;
			$deducted_credits = 0;
			$credits_back = 0;
			$deducted_by_hr_medical = 0;
			$in_network_temp_spent = 0;
			$e_claim_spent = 0;
			$deleted_employee_allocation = 0;
			$total_deduction_credits = 0;

            // check if employee has reset credits
			$employee_credit_reset_medical = DB::table('credit_reset')
			->where('id', $user_id)
			->where('spending_type', 'medical')
			->where('user_type', 'employee')
			->orderBy('created_at', 'desc')
			->first();
			$user = DB::table('user')->where('UserID', $user_id)->first();

			if($employee_credit_reset_medical) {
				$start = $employee_credit_reset_medical->date_resetted;
				$wallet_history_id = $employee_credit_reset_medical->wallet_history_id;
				$wallet_history = DB::table('wallet_history')
								->join('e_wallet', 'e_wallet.wallet_id', '=', 'wallet_history.wallet_id')
								->where('wallet_history.wallet_id', $wallet_id)
								->where('e_wallet.UserID', $user_id)
								->where('wallet_history.created_at',  '>=', $start)
								->get();
			} else {
				$wallet_history = DB::table('wallet_history')->where('wallet_id', $wallet_id)->get();
			}

			foreach ($wallet_history as $key => $history) {
				if($history->logs == "added_by_hr") {
					$get_allocation += $history->credit;
				}

				if($history->logs == "deducted_by_hr") {
					$deducted_credits += $history->credit;
				}

				if($history->where_spend == "e_claim_transaction") {
					$e_claim_spent += $history->credit;
				}

				if($history->where_spend == "in_network_transaction") {
					$in_network_temp_spent += $history->credit;
				}

				if($history->where_spend == "credits_back_from_in_network") {
					$credits_back += $history->credit;
				}
			}

			$pro_allocation = DB::table('wallet_history')
			->where('wallet_id', $wallet_id)
			->where('logs', 'pro_allocation')
			->sum('credit');

			$get_allocation_spent_temp = $in_network_temp_spent + $e_claim_spent;
			$get_allocation_spent = $get_allocation_spent_temp - $credits_back;
			$medical_balance = 0;

			if($pro_allocation > 0 && (int)$user->Active == 0 || $pro_allocation > 0 && (int)$user->Active == 1) {
				$allocation = $pro_allocation;
				$balance = $pro_allocation - $get_allocation_spent;
				$medical_balance = $balance;

				if($balance < 0) {
					$balance = 0;
					$medical_balance = $balance;
				}
			} else {
				$allocation = $get_allocation - $deducted_credits;
				$balance = $allocation - $get_allocation_spent;
				$medical_balance = $balance;
				$total_deduction_credits += $deducted_credits;

				if($user->Active == 0) {
					$deleted_employee_allocation = $get_allocation - $deducted_credits;
					$medical_balance = 0;
				}
			}

			if($pro_allocation > 0) {
				$allocation = $pro_allocation;
			}

			return array('allocation' => $allocation, 'get_allocation_spent' => $get_allocation_spent, 'balance' => $balance >= 0 ? $balance : 0, 'e_claim_spent' => $e_claim_spent, 'in_network_spent' => $get_allocation_spent_temp, 'deleted_employee_allocation' => $deleted_employee_allocation, 'total_deduction_credits' => $total_deduction_credits, 'medical_balance' => $medical_balance, 'total_spent' => $get_allocation_spent);
		}

		public static function memberWellnessAllocatedCredits($wallet_id, $user_id)
		{
			$get_wellness_allocation = 0;
			$deducted_by_hr_wellness = 0;
			$e_claim_wellness_spent = 0;
			$credits_back_wellness = 0;
			$in_network_wellness_temp_spent = 0;
			$deducted_wellness_credits = 0;
			$deleted_employee_allocation_wellness = 0;
			$total_deduction_credits_wellness = 0;
            // get all user wallet logs wellness
			$employee_credit_reset_wellness = DB::table('credit_reset')
			->where('id', $user_id)
			->where('spending_type', 'wellness')
			->where('user_type', 'employee')
			->orderBy('created_at', 'desc')
			->first();
			$user = DB::table('user')->where('UserID', $user_id)->first();

			if($employee_credit_reset_wellness) {
				$start = date('Y-m-d', strtotime($employee_credit_reset_wellness->date_resetted));
				$wallet_history_id = $employee_credit_reset_wellness->wallet_history_id;
				$wallet_history = DB::table('wellness_wallet_history')
				->join('e_wallet', 'e_wallet.wallet_id', '=', 'wellness_wallet_history.wallet_id')
				// ->where('e_wallet.UserID', $user_id)
				// ->where('wellness_wallet_history.wellness_wallet_history_id',  '>=', $wallet_history_id)
				// ->where('created_at', '>=', date('Y-m-d', strtotime($start)))
				->where('wellness_wallet_history.wallet_id', $wallet_id)
				->where('e_wallet.UserID', $user_id)
				->where('wellness_wallet_history.created_at',  '>=', $start)
				->get();
			} else {
				$wallet_history = DB::table('wellness_wallet_history')->where('wallet_id', $wallet_id)->get();
			}

			foreach ($wallet_history as $key => $history) {
				if($history->logs == "added_by_hr") {
					$get_wellness_allocation += $history->credit;
				}

				if($history->logs == "deducted_by_hr") {
					$deducted_wellness_credits += $history->credit;
					$deducted_by_hr_wellness = $history->credit;
				}

				if($history->where_spend == "e_claim_transaction") {
					$e_claim_wellness_spent += $history->credit;
				}

				if($history->where_spend == "in_network_transaction") {
					$in_network_wellness_temp_spent += $history->credit;
				}

				if($history->where_spend == "credits_back_from_in_network") {
					$credits_back_wellness += $history->credit;
				}
			}

			
			$pro_allocation = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet_id)
			->where('logs', 'pro_allocation')
			->sum('credit');

			$get_allocation_spent_temp_wellness = $in_network_wellness_temp_spent - $credits_back_wellness;
			$get_allocation_spent_wellness = $get_allocation_spent_temp_wellness + $e_claim_wellness_spent;
			$wellness_balance = 0;

			if($pro_allocation > 0 && (int)$user->Active == 0 || $pro_allocation > 0 && (int)$user->Active == 1) {
				$allocation_wellness = $pro_allocation;
				$balance = $pro_allocation - $get_allocation_spent_wellness;
				$wellness_balance = $balance;
				if($balance < 0) {
					$balance = 0;
					$wellness_balance = $balance;
				}
			} else {
				$allocation_wellness = $get_wellness_allocation - $deducted_wellness_credits;
				$total_deduction_credits_wellness = $deducted_wellness_credits;
				$balance = $allocation_wellness - $get_allocation_spent_wellness;
				$wellness_balance = $balance;
				if($user->Active == 0) {
					$deleted_employee_allocation_wellness = $allocation_wellness - $deducted_by_hr_wellness;
					$wellness_balance = 0;
				}
			}

			if($pro_allocation > 0) {
				$allocation_wellness = $pro_allocation;
			}

			return array('allocation' => $allocation_wellness, 'get_allocation_spent' => $get_allocation_spent_wellness, 'balance' => $balance >= 0 ? $balance : 0, 'e_claim_spent' => $e_claim_wellness_spent, 'in_network_spent' => $get_allocation_spent_temp_wellness, 'deleted_employee_allocation_wellness' => $deleted_employee_allocation_wellness, 'total_deduction_credits_wellness' => $total_deduction_credits_wellness, 'wellness_balance' => $wellness_balance, 'total_spent' => $get_allocation_spent_wellness);
		}

		public static function getPlanDuration($customer_id, $plan_start)
		{
			$plan_coverage = self::getCompanyPlanDates($customer_id);
			$date_plan_start = new \DateTime(date('Y-m-d', strtotime($plan_start)));
			$date_new_plan_start = new \DateTime(date('Y-m-d', strtotime($plan_coverage['plan_end'])));

			$interval = date_diff($date_plan_start, $date_new_plan_start);
			if($interval->m + (1) == 1) {
				$duration = $interval->m + (1). ' month';
			} else {
				$duration = $interval->m + (1). ' months';
			}

			return $duration;
		}

		public static function createUserPlanHistory($user_id, $customer_id)
		{
			$plan = DB::table('customer_plan')
			->where('customer_buy_start_id', $customer_id)
			->orderBy('created_at', 'desc')
			->first();
			$active_plan = DB::table('customer_active_plan')
			->where('plan_id', $plan->customer_plan_id)
			->orderBy('created_at', 'desc')
			->first();

			$data = array(
				'user_id'                   => $user_id,
				'customer_active_plan_id'   => $active_plan->customer_active_plan_id,
				'type'                      => 'started',
				'date'                      => date('Y-m-d', strtotime($active_plan->plan_start)),
				'created_at'                => date('Y-m-d h:i:s'),
				'updated_at'                => date('Y-m-d h:i:s')
			);

			DB::table('user_plan_history')->insert($data);
		}
		public static function calculateInvoicePlanPrice($default_price, $start, $end)
		{
			$diff = date_diff(new \DateTime(date('Y-m-d', strtotime($start))), new \DateTime(date('Y-m-d', strtotime('+1 day', strtotime($end)))));
			$days = $diff->format('%a');
			$total_days = date("z", mktime(0,0,0,12,31,date('Y'))) + 1;
			$remaining_days = $days;

			$cost_plan_and_days = ($remaining_days / $total_days);
			return $cost_plan_and_days * $default_price;
		}

		public static function getCorporateUserByAllocated($corporate_id, $customer_id) 
		{
			$users_medical = [];
			$users_wellness = [];

			$users_medical_temp = DB::table('corporate_members')
								->join('e_wallet', 'e_wallet.UserID', '=', 'corporate_members.user_id')
								->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
								->where('corporate_members.corporate_id', $corporate_id)
								->where('wallet_history.logs', 'added_by_hr')
								->groupBy('corporate_members.user_id')
								->get();

			foreach ($users_medical_temp as $key => $medical) {
				$users_medical[] = $medical->user_id;
			}
			// return sizeof($users_medical);
			$users_wellness_temp = DB::table('corporate_members')
								->join('e_wallet', 'e_wallet.UserID', '=', 'corporate_members.user_id')
								->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
								->where('corporate_members.corporate_id', $corporate_id)
								->where('wellness_wallet_history.logs', 'added_by_hr')
								->groupBy('corporate_members.user_id')
								->get();
			
			foreach ($users_wellness_temp as $key => $medical) {
				$users_wellness[] = $medical->user_id;
			}

			// $array_medical = json_decode(json_encode($users_medical), true);
			// $array_wellness = json_decode(json_encode($users_wellness), true);
			$new_array = array_unique(array_merge($users_medical, $users_wellness));
			return $new_array;
			// $customer_credit_reset_medical = DB::table('credit_reset')
			// ->where('id', $customer_id)
			// ->where('spending_type', 'medical')
			// ->where('user_type', 'company')
			// ->orderBy('created_at', 'desc')
			// ->first();

			// if($customer_credit_reset_medical) {
			// 	$allocation_medical_users = DB::table("corporate_members")
			// 	->join("e_wallet", "e_wallet.UserID", "=", "corporate_members.user_id")
			// 	->join("wallet_history", "wallet_history.wallet_id", "=", "e_wallet.wallet_id")
			// 	->join('credit_reset', 'credit_reset.id', '=', 'e_wallet.UserID')
			// 	->where('credit_reset.user_type', 'employee')
			// 	->where("corporate_members.corporate_id", $corporate_id)
			// 	->where("wallet_history.logs", "added_by_hr")
			// 	->orderBy('credit_reset.created_at', 'desc')
			// 	->groupBy("corporate_members.user_id")
			// 	->get();
			// } else {
			// 	$allocation_medical_users = DB::table("corporate_members")
			// 	->join("e_wallet", "e_wallet.UserID", "=", "corporate_members.user_id")
			// 	->join("wallet_history", "wallet_history.wallet_id", "=", "e_wallet.wallet_id")
			// 	->where("corporate_members.corporate_id", $corporate_id)
			// 	->where("wallet_history.logs", "added_by_hr")
			// 	->groupBy("corporate_members.user_id")
			// 	->get();
			// }

			// $customer_credit_reset_wellness = DB::table('credit_reset')
			// ->where('id', $customer_id)
			// ->where('spending_type', 'wellness')
			// ->where('user_type', 'company')
			// ->orderBy('created_at', 'desc')
			// ->first();

			// if($customer_credit_reset_wellness) {
			// 	$allocation_wellness_users = DB::table("corporate_members")
			// 	->join("e_wallet", "e_wallet.UserID", "=", "corporate_members.user_id")
			// 	->join("wellness_wallet_history", "wellness_wallet_history.wallet_id", "=", "e_wallet.wallet_id")
			// 	->join('credit_reset', 'credit_reset.id', '=', 'e_wallet.UserID')
			// 	->where('credit_reset.user_type', 'employee')
			// 	->where("corporate_members.corporate_id", $corporate_id)
			// 	->where("wellness_wallet_history.logs", "added_by_hr")
			// 	->orderBy('credit_reset.created_at', 'desc')
			// 	->groupBy("corporate_members.user_id")
			// 	->get();
			// } else {
			// 	$allocation_wellness_users = DB::table("corporate_members")
			// 	->join("e_wallet", "e_wallet.UserID", "=", "corporate_members.user_id")
			// 	->join("wellness_wallet_history", "wellness_wallet_history.wallet_id", "=", "e_wallet.wallet_id")
			// 	->where("corporate_members.corporate_id", $corporate_id)
			// 	->whereIn("wellness_wallet_history.logs", ["added_by_hr"])
			// 	->groupBy("corporate_members.user_id")
			// 	->get();
			// }



			// $allocation_users = array_merge($allocation_medical_users, $allocation_wellness_users);

			// $id_arr = array();
			// $users_allocation = array();

			// for( $x = 0; $x < count($allocation_users); $x++ ){
			// 	if( !in_array( $allocation_users[$x]->user_id , $id_arr) ){
			// 		array_push( $id_arr, $allocation_users[$x]->user_id );
			// 		array_push( $users_allocation, $allocation_users[$x] );
			// 	}
			// }

			// return $users_allocation;
		}

		public static function getUnlimitedCorporateUserByAllocated($corporate_id, $customer_id) 
		{
			$users_medical = [];
			$users_wellness = [];

			$users_medical_temp = DB::table('corporate_members')
								->where('corporate_id', $corporate_id)
								->get();

			foreach ($users_medical_temp as $key => $medical) {
				$users_medical[] = $medical->user_id;
			}

			return $users_medical;
		}

		public static function getResetWallet($user_id, $spending_type, $start, $end, $type)
		{
			$wallet_reset = DB::table('credit_reset')
			->where('id', $user_id)
			->where('user_type', $type)
			->where('spending_type', $spending_type)
			->where('date_resetted', '<=', $end)
			->first();

			if($wallet_reset) {
				return $wallet_reset;
			} else {
				return false;
			}
		}

		public static function getResetWalletDate($user_id, $spending_type, $start, $end, $type)
		{
			// if($start && $end) {
			// 	$wallet_reset = DB::table('credit_reset')
			// 	->where('id', $user_id)
			// 	->where('user_type', $type)
			// 	->where('spending_type', $spending_type)
			// 	->where('date_resetted', '>=', $start)
			// 	->where('date_resetted', '<=', $end)
			// 	->first();
			// } else {
				$wallet_reset = DB::table('credit_reset')
				->where('id', $user_id)
				->where('user_type', $type)
				->where('spending_type', $spending_type)
				->where('date_resetted', '<=', $end)
				->first();
			// }

			if($wallet_reset) {
				return date('Y-m-d', strtotime($wallet_reset->date_resetted));
			} else {
				return false;
			}
		}

		public static function createPaymentsRefund($id, $date_refund)
		{
			$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $id)->first();
			$refund_count = DB::table('payment_refund')
			->join('customer_active_plan', 'customer_active_plan.customer_active_plan_id', '=', 'payment_refund.customer_active_plan_id')
			->join('customer_plan', 'customer_plan.customer_plan_id', '=', 'customer_active_plan.plan_id')
			->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_plan.customer_buy_start_id')
			->where('customer_buy_start.customer_buy_start_id', $active_plan->customer_start_buy_id)
			->count();
            // create refund payment
			$check = 10 + $refund_count;
			$temp_invoice_number = str_pad($check, 6, "0", STR_PAD_LEFT);
			$invoice_number = 'OMC'.$temp_invoice_number.'A';
			if($refund_count > 0) {
				++$invoice_number;
			}

			$data = array(
				'customer_active_plan_id'   => $id,
				'cancellation_number'       => $invoice_number,
				'date_refund'               => $date_refund
			);

			$result = \PaymentRefund::create($data);
			return $result->id;
		}

		public static function createPaymentsRefundDependent($id, $date_refund)
		{
            // $active_plan = DB::table('dependent_plans')->where('customer_active_plan_id', $id)->first();
			$refund_count = DB::table('dependent_payment_refund')
			->join('dependent_plans', 'dependent_plans.dependent_plan_id', '=', 'dependent_payment_refund.dependent_plan_id')
			->join('customer_plan', 'customer_plan.customer_plan_id', '=', 'dependent_plans.customer_plan_id')
			->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_plan.customer_buy_start_id')
			->where('dependent_plans.dependent_plan_id', $id)
			->count();
            // create refund payment
			$check = 10 + $refund_count;
			$temp_invoice_number = str_pad($check, 6, "0", STR_PAD_LEFT);
			$invoice_number = 'OMC'.$temp_invoice_number.'A';
			if($refund_count > 0) {
				++$invoice_number;
			}

			$data = array(
				'dependent_plan_id'   => $id,
				'cancellation_number'       => $invoice_number,
				'date_refund'               => $date_refund
			);

			$result = \DependentPaymentRefund::create($data);
			return $result->id;
		}

		public static function getCompanyAvailableDependentPlanId($customer_id)
		{
			$check = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

			if(!$check) {
				return FALSE;
			}

			$plan = DB::table('customer_plan')
			->where('customer_buy_start_id', $customer_id)
			->orderBy('created_at', 'desc')
			->first();

			$dependent_plans = DB::table('dependent_plans')->where('customer_plan_id', $plan->customer_plan_id)->get();

			foreach ($dependent_plans as $key => $dependent) {
				if($dependent->type == "extension_plan" && (int)$dependent->activate_plan_extension == 1 || $dependent->type == "active_plan") {
					$active_users = DB::table('dependent_plan_history')
					->where('dependent_plan_id', $dependent->dependent_plan_id)
					->where('type', 'started')
					->count();

					$pending_enrollment = $dependent->total_dependents - $active_users;
					if($pending_enrollment <= 0) {
						$pending_enrollment = 0;
					} else {
						$pending_enrollment = $pending_enrollment;
						return $dependent->dependent_plan_id;
					}
				}
			}
		}

		public static function createDependentAccountUser($data)
		{
			$user_data = array(
				'Name' => $data['fullname'],
				'UserType' => 5,
				'access_type' => 2,
				'Email' => 'mednefits',
				'NRIC' => null,
				'DOB'   => $data['dob'],
				'PhoneCode' => null,
				'PhoneNo' => null,
				'Age' => 0,
				'Bmi' => 0,
				'Weight' => 0,
				'Height' => 0,
				'Image' => 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1427972951/ls7ipl3y7mmhlukbuz6r.png',
				'OTPCode' => '',
				'OTPStatus' => 0,
				'ClinicID' => null,
				'TimeSlotDuration' => '',
				'Blood_Type' => '',
				'Insurance_Company' => '',
				'Insurance_Policy_No' => '',
				'Lat' => '',
				'Lng' => '',
				'Recon' => 0,
				'Address' => '',
				'City' => '',
				'State' => '',
				'Country' => '',
				'Zip_Code' => '',
				'Ref_ID' => 0,
				'ActiveLink' => null,
				'Status' => 0,
				'source_type' => 1,
				'created_at' => time(),
				'updated_at' => time(),
				'Active' => 1,
				'Password' => 'mednefits',
				'account_update_status'		=> 1,
				'account_update_date' => date('Y-m-d H:i:s'),
				'account_already_update'	=> 1
			);

			$user_data_save = \User::create($user_data);
			return $user_data_save->id;
		}

		public static function getEmployeePlanCoverageDate($user_id, $customer_id)
		{
			$get_employee_plan = DB::table('user_plan_type')
			->where('user_id', $user_id)
			->orderBy('created_at', 'desc')
			->first();

			$user_active_plan_history = DB::table('user_plan_history')
			->where('user_id', $user_id)
            ->where('type', 'started')
			->orderBy('created_at', 'desc')
			->first();

			$current_employee_active_plan = DB::table('customer_active_plan')
			->where('customer_active_plan_id', $user_active_plan_history->customer_active_plan_id)
			->first();

			$plan = DB::table('customer_plan')
					->where('customer_plan_id', $current_employee_active_plan->plan_id)
					->orderBy('created_at', 'desc')
					->first();
			$active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
			if((int)$active_plan->plan_extention_enable == 1) {
				$plan_user_history = DB::table('user_plan_history')
					->where('user_id', $user_id)
					->where('type', 'started')
					->orderBy('created_at', 'desc')
					->first();
				if(!$plan_user_history) {
                    // create plan user history
					PlanHelper::createUserPlanHistory($user_id, $link_account->customer_buy_start_id, $customer_id);
					$plan_user_history = DB::table('user_plan_history')
					->where('user_id', $user_id)
					->where('type', 'started')
					->orderBy('created_at', 'desc')
					->first();
				}

				$plan_user = DB::table('user_plan_type')
				->where('user_id', $user_id)
				->orderBy('created_at', 'desc')
				->first();

				$active_plan_extension = DB::table('plan_extensions')
				->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
				->first();
				
				if($plan_user->fixed == 1 || $plan_user->fixed == "1") {
					$temp_valid_date = date('Y-m-d', strtotime('+'.$active_plan_extension->duration, strtotime($active_plan_extension->plan_start)));
					$expiry_date = date('Y-m-d', strtotime('-1 day', strtotime($temp_valid_date)));
				} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
					$expiry_date = date('Y-m-d', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
				}
			} else {
				$plan_user_history = DB::table('user_plan_history')
					->where('user_id', $user_id)
					->where('type', 'started')
					->orderBy('created_at', 'desc')
					->first();
					if(!$plan_user_history) {
                        // create plan user history
						PlanHelper::createUserPlanHistory($user_id, $link_account->customer_buy_start_id, $customer_id);
						$plan_user_history = DB::table('user_plan_history')
						->where('user_id', $user_id)
						->where('type', 'started')
						->orderBy('created_at', 'desc')
						->first();
					}
				$plan_user = DB::table('user_plan_type')
				->where('user_id', $user_id)
				->orderBy('created_at', 'desc')
				->first();

				$active_plan = DB::table('customer_active_plan')
				->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)
				->first();

				$plan = DB::table('customer_plan')
				->where('customer_plan_id', $active_plan->plan_id)
				->first();
				
				$first_active_plan = DB::table('customer_active_plan')
				->where('plan_id', $active_plan->plan_id)
				->first();
				
				if($plan_user->fixed == 1 || $plan_user->fixed == "1") {
					$temp_valid_date = date('Y-m-d', strtotime('+'.$first_active_plan->duration, strtotime($plan->plan_start)));
					$expiry_date = date('Y-m-d', strtotime('-1 day', strtotime($temp_valid_date)));
				} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
					$expiry_date = date('Y-m-d', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
				}
			}

			return array('plan_start' => date('Y-m-d', strtotime($get_employee_plan->plan_start)), 'plan_end' => $expiry_date);
		}

		public static function getEmployeePlanDetails($user_id, $customer_id)
		{
			$get_employee_plan = DB::table('user_plan_type')
			->where('user_id', $user_id)
			->orderBy('created_at', 'desc')
			->first();

			$user_active_plan_history = DB::table('user_plan_history')
			->where('user_id', $user_id)
                                    // ->where('type', 'started')
			->orderBy('created_at', 'desc')
			->first();

            // $replace = DB::table('customer_replace_employee')
            //                         ->where('old_id', $user_id)
            //                         ->where('active_plan_id', $user_active_plan_history->customer_active_plan_id)
            //                         ->orderBy('created_at', 'desc')
            //                         ->first();

			$active_plan = DB::table('customer_active_plan')
			->where('customer_active_plan_id', $user_active_plan_history->customer_active_plan_id)
			->first();
			$replace = self::getEmployeeLastDayCoverage($user_id);
            // return $replace;
			if($replace) {
				$expiry_date = date('Y-m-d', strtotime($replace));
                // return $expiry_date;
			} else {
				$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();

				$active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();

				if((int)$active_plan->plan_extention_enable == 1) {
					$plan_user_history = DB::table('user_plan_history')
					->where('user_id', $user_id)
					->where('type', 'started')
					->orderBy('created_at', 'desc')
					->first();
					if(!$plan_user_history) {
                        // create plan user history
						PlanHelper::createUserPlanHistory($user_id, $link_account->customer_buy_start_id, $customer_id);
						$plan_user_history = DB::table('user_plan_history')
						->where('user_id', $user_id)
						->where('type', 'started')
						->orderBy('created_at', 'desc')
						->first();
					}

					$plan_user = DB::table('user_plan_type')
					->where('user_id', $user_id)
					->orderBy('created_at', 'desc')
					->first();
					
					$active_plan = DB::table('customer_active_plan')
					->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)
					->first();
					
					$first_active_plan = DB::table('customer_active_plan')
					->where('plan_id', $active_plan->plan_id)
					->first();

					$plan = DB::table('customer_plan')
					->where('customer_plan_id', $first_active_plan->plan_id)
					->first();

					$active_plan_extension = DB::table('plan_extensions')
					->where('customer_active_plan_id', $first_active_plan->customer_active_plan_id)
					->first();
					
					if($plan_user->fixed == 1 || $plan_user->fixed == "1") {
						$temp_valid_date = date('Y-m-d', strtotime('+'.$active_plan_extension->duration, strtotime($active_plan_extension->plan_start)));
						$expiry_date = date('Y-m-d', strtotime('-1 day', strtotime($temp_valid_date)));
					} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
						$expiry_date = date('Y-m-d', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
					}
				} else {
					$plan_user_history = DB::table('user_plan_history')
					->where('user_id', $user_id)
					->where('type', 'started')
					->orderBy('created_at', 'desc')
					->first();
					if(!$plan_user_history) {
                        // create plan user history
						PlanHelper::createUserPlanHistory($user_id, $link_account->customer_buy_start_id, $customer_id);
						$plan_user_history = DB::table('user_plan_history')
						->where('user_id', $user_id)
						->where('type', 'started')
						->orderBy('created_at', 'desc')
						->first();
					}
					$plan_user = DB::table('user_plan_type')
					->where('user_id', $user_id)
					->orderBy('created_at', 'desc')
					->first();

					$active_plan = DB::table('customer_active_plan')
					->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)
					->first();

					$plan = DB::table('customer_plan')
					->where('customer_plan_id', $active_plan->plan_id)
					->first();
					
					$first_active_plan = DB::table('customer_active_plan')
					->where('plan_id', $active_plan->plan_id)
					->first();
					
					if($plan_user->fixed == 1 || $plan_user->fixed == "1") {
						$temp_valid_date = date('Y-m-d', strtotime('+'.$first_active_plan->duration, strtotime($plan->plan_start)));
						$expiry_date = date('Y-m-d', strtotime('-1 day', strtotime($temp_valid_date)));
					} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
						$expiry_date = date('Y-m-d', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
					}
				}

			}
			return array('plan_start' => date('Y-m-d', strtotime($get_employee_plan->plan_start)), 'end_date' => self::endDate($expiry_date));
		}

		public static function checkEmployeePlanRefundType($user_id)
		{
			$type = StringHelper::checkUserType($user_id);
			$refund = false;
			if((int)$type['user_type'] == 5 && (int)$type['access_type'] == 0 || (int)$type['user_type'] == 5 && (int)$type['access_type'] == 1)
			{
				$user_active_plan_history = DB::table('user_plan_history')
				->where('user_id', $user_id)
                                        // ->where('type', 'started')
				->orderBy('created_at', 'desc')
				->first();


				$active_plan = DB::table('customer_active_plan')
				->where('customer_active_plan_id', $user_active_plan_history->customer_active_plan_id)
				->first();

				if((int)$active_plan->plan_extention_enable == 1) {
					$active_plan_type = DB::table('plan_extensions')
					->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
					->first();
				} else {
					$active_plan_type = DB::table('customer_active_plan')
					->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
					->first();
				}

				if($active_plan_type->account_type == "stand_alone_plan" || $active_plan_type->account_type == "lite_plan" || $active_plan_type->account_type == "enterprise_plan") {
					$refund = true;
				}
			} else {
				$dependepent_plan_history = DB::table('dependent_plan_history')
				->where('user_id', $user_id)
				->orderBy('created_at', 'desc')
				->first();
				$dependent_plan = DB::table('dependent_plans')
				->where('dependent_plan_id', $dependepent_plan_history->dependent_plan_id)
				->first();

				if($dependent_plan->account_type == "stand_alone_plan" || $dependent_plan->account_type == "lite_plan" || $dependent_plan->account_type == "enterprise_plan") {
					$refund = true;
				}
			}


			return $refund;
		}

		public static function createReplacementEmployee($replace_id, $input, $id, $schedule, $medical, $wellness)
		{
			$replace = new CustomerReplaceEmployee( );
			$date_today = date('Y-m-d');
	    	$last_day_of_coverage = date('Y-m-d', strtotime($input['last_day_coverage']));
			$plan_start = date('Y-m-d', strtotime($input['plan_start']));
			$deactive_employee_status = 0;
            // return $wellness;
			$user = new User();
            // check company credits
			$customer = DB::table('customer_credits')->where('customer_id', $id)->first();
            // get user user plan
			$user_plan_history = DB::table('user_plan_history')
			->where('user_id', $replace_id)
			->where('type', 'started')
			->orderBy('created_at', 'desc')
			->first();
			
			if(!$user_plan_history) {
				$active_plan = DB::table('customer_active_plan')
				->where('customer_start_buy_id', $id)
				->orderBy('created_at', 'desc')
				->first();
			} else {
				$active_plan = DB::table('customer_active_plan')
				->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
				->first();
			}

			$deactive_employee_status = 0;
			// check if replacement is today
			if(date('Y-m-d') >= $last_day_of_coverage) {
				$user_data = array(
					'Active'	=> 0,
					'updated_at' => date('Y-m-d')
				);
				// update user and set to inactive
				DB::table('user')->where('UserID', $replace_id)->update($user_data);
				// set company members removed to 1
				DB::table('corporate_members')->where('user_id',$replace_id)->update(['removed_status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

				$user_plan_history_class = new UserPlanHistory();
				$user_plan_history_data = array(
					'user_id'		=> $replace_id,
					'type'			=> "deleted_expired",
					'date'			=> date('Y-m-d', strtotime($input['last_day_coverage'])),
					'customer_active_plan_id' => $active_plan->customer_active_plan_id
				);
				$user_plan_history_class->createUserPlanHistory($user_plan_history_data);
				$deactive_employee_status = 1;
			}

			$corporate = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $id)->first();
			$password = StringHelper::get_random_password(8);
			$pending = 1;

			if(date('Y-m-d') >= date('Y-m-d', strtotime($input['plan_start']))) {
				$pending = 0;
			}

			if(!empty($input['email']) && $input['email']) {
				$communication_type = "email";
			} else if($input['mobile']) {
				$communication_type = "sms";
			} else {
				$communication_type = "email";
			}

			$data = array(
				'Name'          => $input['fullname'],
				'Password'  => md5($password),
				'Email'         => !empty($input['email']) ? $input['email'] : null,
				'PhoneNo'       => $input['mobile'],
				'PhoneCode' => "+".$input['country_code'],
				'NRIC'          => null,
				'Job_Title'  => 'Other',
				'DOB'       => $input['dob'],
				'Zip_Code'  => $input['postal_code'],
				'pending'		=> $pending,
				'Active'        => 1,
				'communication_type' => $communication_type
			);

			$user_id = $user->createUserFromCorporate($data);

			if($user_id) {
				$corporate_member = array(
					'corporate_id'      => $corporate->corporate_id,
					'user_id'           => $user_id,
					'first_name'        => $input['fullname'],
					'last_name'         => $input['fullname'],
					'type'              => 'member',
					'created_at'        => date('Y-m-d H:i:s'),
					'updated_at'        => date('Y-m-d H:i:s'),
				);
				DB::table('corporate_members')->insert($corporate_member);
				$plan_type = new UserPlanType();
				$check_plan_type = $plan_type->checkUserPlanType($user_id);

				if($check_plan_type == 0) {
					$group_package = new PackagePlanGroup();
					$bundle = new Bundle();
					$user_package = new UserPackage();

					$group_package_id = $group_package->getPackagePlanGroupDefault();
					$result_bundle = $bundle->getBundle($group_package_id);

					foreach ($result_bundle as $key => $value) {
						$user_package->createUserPackage($value->care_package_id, $user_id);
					}

					$plan_type_data = array(
						'user_id'               => $user_id,
						'package_group_id'      => $group_package_id,
						'duration'              => '1 year',
						'plan_start'            => date('Y-m-d', strtotime($input['plan_start']))
					);
					$plan_type->createUserPlanType($plan_type_data);
				}

                // store user plan history
				$user_plan_history = new UserPlanHistory();
				$user_plan_history_data = array(
					'user_id'       => $user_id,
					'type'          => "started",
					'date'          => date('Y-m-d', strtotime($input['plan_start'])),
					'customer_active_plan_id' => $active_plan->customer_active_plan_id
				);

				$user_plan_history->createUserPlanHistory($user_plan_history_data);

				$wallet = \Wallet::where('UserID', $user_id)->first();
				$data_create_history = array(
					'wallet_id'             => $wallet->wallet_id,
					'credit'                    => 0,
					'running_balance' => 0,
					'logs'                      => 'wallet_created',
					'customer_active_plan_id' => $active_plan->customer_active_plan_id
				);
				\WalletHistory::create($data_create_history);

				if($medical > 0) {
          // medical credits
					if($customer->balance >= $medical) {

						$result_customer_active_plan = self::allocateCreditBaseInActivePlan($id, $medical, "medical");

						if($result_customer_active_plan) {
							$customer_active_plan_id = $result_customer_active_plan;
						} else {
							$customer_active_plan_id = NULL;
						}

                        // give credits
						$wallet_class = new Wallet();
						$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
						$update_wallet = $wallet_class->addCredits($user_id, $medical);

						$employee_logs = new WalletHistory();

						$wallet_history = array(
							'wallet_id'     => $wallet->wallet_id,
							'credit'            => $medical,
							'logs'              => 'added_by_hr',
							'running_balance'   => $medical,
							'customer_active_plan_id' => $customer_active_plan_id
						);

						$employee_logs->createWalletHistory($wallet_history);
						$customer_credits = new CustomerCredits();

						$customer_credits_result = $customer_credits->deductCustomerCredits($customer->customer_credits_id, $medical);
						
						if($customer_credits_result) {
							$company_deduct_logs = array(
								'customer_credits_id'   => $customer->customer_credits_id,
								'credit'                => $medical,
								'logs'                  => 'added_employee_credits',
								'user_id'               => $user_id,
								'running_balance'       => $customer->balance - $medical,
								'customer_active_plan_id' => $customer_active_plan_id
							);

							$customer_credit_logs = new CustomerCreditLogs( );
							$customer_credit_logs->createCustomerCreditLogs($company_deduct_logs);
						}
					}
				}

				if($wellness > 0) {
          // wellness credits
					if($customer->wellness_credits >= $wellness) {
						$result_customer_active_plan = self::allocateCreditBaseInActivePlan($id, $wellness, "wellness");

						if($result_customer_active_plan) {
							$customer_active_plan_id = $result_customer_active_plan;
						} else {
							$customer_active_plan_id = NULL;
						}
                        // give credits
						$wallet_class = new Wallet();
						$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
						$update_wallet = $wallet_class->addWellnessCredits($user_id, $wellness);

						$wallet_history = array(
							'wallet_id'     => $wallet->wallet_id,
							'credit'        => $wellness,
							'logs'          => 'added_by_hr',
							'running_balance'   => $wellness,
							'customer_active_plan_id' => $customer_active_plan_id
						);

						\WellnessWalletHistory::create($wallet_history);
						$customer_credits = new CustomerCredits();
						$customer_credits_result = $customer_credits->deductCustomerWellnessCredits($customer->customer_credits_id, $wellness);
						
						if($customer_credits_result) {
							$company_deduct_logs = array(
								'customer_credits_id'   => $customer->customer_credits_id,
								'credit'                => $wellness,
								'logs'                  => 'added_employee_credits',
								'user_id'               => $user_id,
								'running_balance'       => $customer->wellness_credits - $wellness,
								'customer_active_plan_id' => $customer_active_plan_id
							);
							$customer_credits_logs = new CustomerWellnessCreditLogs();
							$customer_credits_logs->createCustomerWellnessCreditLogs($company_deduct_logs);
						}
					}
				}

				if($schedule) {
					$replace_employee = DB::table('customer_replace_employee')
					->where('old_id', $replace_id)
					->first();
					$replace_data = array(
						'new_id'                => $user_id,
						'status'                => 1,
						'replace_status'        => 1
					);              
					$replace->updateCustomerReplace($replace_employee->customer_replace_employee_id, $replace_data);
				} else {
					$status = 0;
					if($deactive_employee_status == 1) {
						$status = 1;
						// remove dependents
						self::removeDependentAccountsReplace($replace_id, date('Y-m-d', strtotime($input['last_day_coverage'])));
					}

					$replace_data = array(
						'old_id'                => $replace_id,
						'new_id'                => $user_id,
						'active_plan_id'        => $active_plan->customer_active_plan_id,
						'expired_and_activate'  => date('Y-m-d', strtotime($input['last_day_coverage'])),
						'start_date'            => date('Y-m-d', strtotime($input['plan_start'])),
						'status'                => $status,
						'replace_status'        => 1,
						'deactive_employee_status' => $deactive_employee_status
					);
					$replace->createReplaceEmployee($replace_data);
				}

				$user = DB::table('user')->where('UserID', $user_id)->first();
				$company = DB::table('corporate')->where('corporate_id', $corporate->corporate_id)->first();
				// check for dependents
				if($user) {
					if($user->communication_type == "sms") {
						$compose = [];
						$compose['name'] = $user->Name;
						$compose['company'] = $company->company_name;
						$compose['plan_start'] = date('F d, Y', strtotime($input['plan_start']));
						$compose['email'] = $user->Email;
						$compose['nric'] = $user->PhoneNo;
						$compose['password'] = $password;
						$compose['phone'] = $user->PhoneNo;

						$compose['message'] = SmsHelper::formatWelcomeEmployeeMessage($compose);
						$result_sms = SmsHelper::sendSms($compose);
					} else {
						if($input['email']) {
							$email_data['company']   = ucwords($company->company_name);
							$email_data['emailName'] = $input['fullname'];
							$email_data['name'] = $input['fullname'];
							$email_data['emailTo']   = $input['email'];
							$email_data['email']   = $input['mobile'];
							$email_data['emailPage'] = 'email-templates.latest-templates.mednefits-welcome-member-enrolled';
							$email_data['emailSubject'] = 'WELCOME TO MEDNEFITS CARE';
							$email_data['start_date'] = date('d F Y', strtotime($input['plan_start']));
							$email_data['pw'] = $password;
							$email_data['url'] = url('/');
							$email_data['plan'] = $active_plan;
							EmailHelper::sendEmail($email_data);
						} else {
							$compose = [];
							$compose['name'] = $user->Name;
							$compose['company'] = $company->company_name;
							$compose['plan_start'] = date('F d, Y', strtotime($input['plan_start']));
							$compose['email'] = $user->Email;
							$compose['nric'] = $user->PhoneNo;
							$compose['password'] = $password;
							$compose['phone'] = $user->PhoneNo;

							$compose['message'] = SmsHelper::formatWelcomeEmployeeMessage($compose);
							$result_sms = SmsHelper::sendSms($compose);
						}
					}
				}

				return array('status' => true, 'message' => 'Employee Replaced.');
			} else {
				return array('status' => false, 'message' => 'Failed to replace employee.');
			}

		}

		public static function createReplacementEmployeeSchedule($replace_id, $input, $id, $schedule, $medical, $wellness)
		{
			$replace = new CustomerReplaceEmployee( );
			$date_today = date('Y-m-d');
	    	// $last_day_of_coverage = date('Y-m-d', strtotime($input['last_day_coverage']));
			$plan_start = date('Y-m-d', strtotime($input['plan_start']));
			
			$user_plan_history = DB::table('user_plan_history')
			->where('user_id', $replace_id)
			->where('type', 'started')
			->orderBy('created_at', 'desc')
			->first();
			
			if(!$user_plan_history) {
				$active_plan = DB::table('customer_active_plan')
				->where('customer_start_buy_id', $id)
				->orderBy('created_at', 'desc')
				->first();
			} else {
				$active_plan = DB::table('customer_active_plan')
				->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
				->first();
			}
            // return $wellness;
			$user = new User();
            // check company credits
			$customer = DB::table('customer_credits')->where('customer_id', $id)->first();

			$corporate = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $id)->first();
			$password = StringHelper::get_random_password(8);
			$pending = 1;

			if(date('Y-m-d') >= date('Y-m-d', strtotime($input['plan_start']))) {
				$pending = 0;
			}
			$data = array(
				'Name'          => $input['first_name'].' '.$input['last_name'],
				'Password'  => md5($password),
				'Email'         => $input['email'],
				'PhoneNo'       => $input['mobile'],
				'PhoneCode' => NULL,
				'NRIC'          => $input['nric'],
				'Job_Title'  => 'Other',
				'DOB'       => $input['dob'],
				'Zip_Code'  => $input['postal_code'],
				'pending'		=> $pending,
				'Active'        => 1
			);

			$user_id = $user->createUserFromCorporate($data);

			if($user_id) {
				$corporate_member = array(
					'corporate_id'      => $corporate->corporate_id,
					'user_id'           => $user_id,
					'first_name'        => $input['first_name'],
					'last_name'         => $input['last_name'],
					'type'              => 'member',
					'created_at'        => date('Y-m-d H:i:s'),
					'updated_at'        => date('Y-m-d H:i:s'),
				);
				DB::table('corporate_members')->insert($corporate_member);
				$plan_type = new UserPlanType();
				$check_plan_type = $plan_type->checkUserPlanType($user_id);

				if($check_plan_type == 0) {
					$group_package = new PackagePlanGroup();
					$bundle = new Bundle();
					$user_package = new UserPackage();

					$group_package_id = $group_package->getPackagePlanGroupDefault();
					$result_bundle = $bundle->getBundle($group_package_id);

					foreach ($result_bundle as $key => $value) {
						$user_package->createUserPackage($value->care_package_id, $user_id);
					}

					$plan_type_data = array(
						'user_id'               => $user_id,
						'package_group_id'      => $group_package_id,
						'duration'              => '1 year',
						'plan_start'            => date('Y-m-d', strtotime($input['plan_start']))
					);
					$plan_type->createUserPlanType($plan_type_data);
				}

                // store user plan history
				$user_plan_history = new UserPlanHistory();
				$user_plan_history_data = array(
					'user_id'       => $user_id,
					'type'          => "started",
					'date'          => date('Y-m-d', strtotime($input['plan_start'])),
					'customer_active_plan_id' => $active_plan->customer_active_plan_id
				);

				$user_plan_history->createUserPlanHistory($user_plan_history_data);

				$wallet = \Wallet::where('UserID', $user_id)->first();
				$data_create_history = array(
					'wallet_id'             => $wallet->wallet_id,
					'credit'                    => 0,
					'running_balance' => 0,
					'logs'                      => 'wallet_created',
					'customer_active_plan_id' => $active_plan->customer_active_plan_id
				);
				\WalletHistory::create($data_create_history);

				if($medical > 0) {
                    // medical credits
					if($customer->balance >= $medical) {

						$result_customer_active_plan = self::allocateCreditBaseInActivePlan($id, $medical, "medical");

						if($result_customer_active_plan) {
							$customer_active_plan_id = $result_customer_active_plan;
						} else {
							$customer_active_plan_id = NULL;
						}

                        // give credits
						$wallet_class = new Wallet();
						$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
						$update_wallet = $wallet_class->addCredits($user_id, $medical);

						$employee_logs = new WalletHistory();

						$wallet_history = array(
							'wallet_id'     => $wallet->wallet_id,
							'credit'            => $medical,
							'logs'              => 'added_by_hr',
							'running_balance'   => $medical,
							'customer_active_plan_id' => $customer_active_plan_id
						);

						$employee_logs->createWalletHistory($wallet_history);
						$customer_credits = new CustomerCredits();

						$customer_credits_result = $customer_credits->deductCustomerCredits($customer->customer_credits_id, $medical);
						
						if($customer_credits_result) {
							$company_deduct_logs = array(
								'customer_credits_id'   => $customer->customer_credits_id,
								'credit'                => $medical,
								'logs'                  => 'added_employee_credits',
								'user_id'               => $user_id,
								'running_balance'       => $customer->balance - $medical,
								'customer_active_plan_id' => $customer_active_plan_id
							);

							$customer_credit_logs = new CustomerCreditLogs( );
							$customer_credit_logs->createCustomerCreditLogs($company_deduct_logs);
						}
					}
				}

				if($wellness > 0) {
                    // wellness credits
					if($customer->wellness_credits >= $wellness) {
						$result_customer_active_plan = self::allocateCreditBaseInActivePlan($id, $wellness, "wellness");

						if($result_customer_active_plan) {
							$customer_active_plan_id = $result_customer_active_plan;
						} else {
							$customer_active_plan_id = NULL;
						}
                        // give credits
						$wallet_class = new Wallet();
						$wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
						$update_wallet = $wallet_class->addWellnessCredits($user_id, $wellness);

						$wallet_history = array(
							'wallet_id'     => $wallet->wallet_id,
							'credit'        => $wellness,
							'logs'          => 'added_by_hr',
							'running_balance'   => $wellness,
							'customer_active_plan_id' => $customer_active_plan_id
						);

						\WellnessWalletHistory::create($wallet_history);
						$customer_credits = new CustomerCredits();
						$customer_credits_result = $customer_credits->deductCustomerWellnessCredits($customer->customer_credits_id, $wellness);
						
						if($customer_credits_result) {
							$company_deduct_logs = array(
								'customer_credits_id'   => $customer->customer_credits_id,
								'credit'                => $wellness,
								'logs'                  => 'added_employee_credits',
								'user_id'               => $user_id,
								'running_balance'       => $customer->wellness_credits - $wellness,
								'customer_active_plan_id' => $customer_active_plan_id
							);
							$customer_credits_logs = new CustomerWellnessCreditLogs();
							$customer_credits_logs->createCustomerWellnessCreditLogs($company_deduct_logs);
						}
					}
				}

				$replace_employee = DB::table('customer_replace_employee')
				->where('old_id', $replace_id)
				->first();
				$replace_data = array(
					'new_id'                => $user_id,
					'status'                => 1,
					'replace_status'        => 1
				);              
				$replace->updateCustomerReplace($replace_employee->customer_replace_employee_id, $replace_data);

				$user = DB::table('user')->where('UserID', $user_id)->first();
				$company = DB::table('corporate')->where('corporate_id', $corporate->corporate_id)->first();
				// check for dependents
				if($user) {
					if($user->communication_type == "sms") {
						$compose = [];
						$compose['name'] = $user->Name;
						$compose['company'] = $company->company_name;
						$compose['plan_start'] = date('F d, Y', strtotime($input['plan_start']));
						$compose['email'] = $user->Email;
						$compose['nric'] = $user->NRIC;
						$compose['password'] = $password;
						$compose['phone'] = $user->PhoneNo;

						$compose['message'] = SmsHelper::formatWelcomeEmployeeMessage($compose);
						$result_sms = SmsHelper::sendSms($compose);

					} else {
						if($input['email']) {
							$email_data['company']   = ucwords($company->company_name);
							$email_data['emailName'] = $input['first_name'].' '.$input['last_name'];
							$email_data['name'] = $input['first_name'].' '.$input['last_name'];
							$email_data['emailTo']   = $input['email'];
							$email_data['email']   = $input['email'];
							$email_data['emailPage'] = 'email-templates.latest-templates.mednefits-welcome-member-enrolled';
							$email_data['emailSubject'] = 'WELCOME TO MEDNEFITS CARE';
							$email_data['start_date'] = date('d F Y', strtotime($input['plan_start']));
							$email_data['pw'] = $password;
							$email_data['url'] = url('/');
							$email_data['plan'] = $active_plan;
							EmailHelper::sendEmail($email_data);
						} else {
							$compose = [];
							$compose['name'] = $user->Name;
							$compose['company'] = $company->company_name;
							$compose['plan_start'] = date('F d, Y', strtotime($input['plan_start']));
							$compose['email'] = $user->Email;
							$compose['nric'] = $user->NRIC;
							$compose['password'] = $password;
							$compose['phone'] = $user->PhoneNo;

							$compose['message'] = SmsHelper::formatWelcomeEmployeeMessage($compose);
							$result_sms = SmsHelper::sendSms($compose);
						}
					}
				}

				return array('status' => true, 'message' => 'Employee Replaced.');
			} else {
				return array('status' => false, 'message' => 'Failed to replace employee.');
			}

		}

		public static function createReplacementDependent($replace_id, $input) 
		{
            // get plan tier if employee has a plan tier
			$last_day_of_coverage = date('Y-m-d', strtotime($input['last_day_coverage']));
			$deactive_dependent_status = 0;
			$plan_tier = DB::table('plan_tier_users')
			->join('plan_tiers', 'plan_tiers.plan_tier_id', '=', 'plan_tier_users.plan_tier_id')
			->where('plan_tier_users.user_id', $replace_id)
			->where('plan_tiers.active', 1)
			->first();

			$dependent_plan = DB::table('dependent_plan_history')
			->where('user_id', $replace_id)
			->where('type', 'started')
			->orderBy('created_at', 'desc')
			->first();

			$plan_tier_user = new PlanTierUsers();
			$dependent_replace = new CustomerReplaceDependent();
			if(date('Y-m-d') >= $last_day_of_coverage) {
                // remove now
                // save history
				$depent_plan_history = new DependentPlanHistory();
				$user_plan_history_data = array(
					'user_id'       => $replace_id,
					'type'          => "deleted_expired",
					'plan_start'          => $last_day_of_coverage,
					'dependent_plan_id' => $dependent_plan->dependent_plan_id,
					'duration'      => $dependent_plan->duration,
					'package_group_id' => $dependent_plan->package_group_id,
					'fixed'         => $dependent_plan->fixed
				);


				$result = $depent_plan_history->createData($user_plan_history_data);
				
				if(!$result) {
					return false;
				}

				$user_data = array(
					'Active'    => 0
				);
                // update user and set to inactive
				DB::table('user')->where('UserID', $replace_id)->update($user_data);
                // set company members removed to 1
				DB::table('employee_family_coverage_sub_accounts')
				->where('user_id', $replace_id)
				->update(['deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
                // create new Dependent Account
				$user = array(
					'fullname'    => $input['fullname'],
					'last_name'     => null,
					'nric'          => null,
					'dob'           => date('Y-m-d', strtotime($input['dob'])),
				);

				$user_id = self::createDependentAccountUser($user);
				if($user_id)
				{
					$owner_id = self::getDependentOwnerID($replace_id);
					$family = array(
						'owner_id'      => $owner_id,
						'user_id'       => $user_id,
						'user_type'     => 'dependent',
						'relationship'  => strtolower($input['relationship']),
						'created_at'    => date('Y-m-d H:i:s'),
						'updated_at'    => date('Y-m-d H:i:s')
					);
					$family_result = DB::table('employee_family_coverage_sub_accounts')->insert($family);
					if($family_result) {
						$history = array(
							'user_id'           => $user_id,
							'dependent_plan_id' => $dependent_plan->dependent_plan_id,
							'package_group_id'  => $dependent_plan->package_group_id,
							'plan_start'        => date('Y-m-d', strtotime($input['plan_start'])),
							'duration'          => '12 months',
							'fixed'             => 1,
							'created_at'    => date('Y-m-d H:i:s'),
							'updated_at'    => date('Y-m-d H:i:s')
						);

						DB::table('dependent_plan_history')->insert($history);
						$plan_tier_id = null;
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

                        // create replacement date
						$replace_data = array(
							'old_id'                => $replace_id,
							'new_id'                => $user_id,
							'dependent_plan_id'     => $dependent_plan->dependent_plan_id,
							'plan_tier_id'          => $plan_tier_id,
							'expired_date'          => $last_day_of_coverage,
							'deactivate_dependent_status' => 1,
							'replace_status'        => 1,
							'relationship'          => $input['relationship'],
							'postal_code'			=> null
						);

						$dependent_replace->createReplacement($replace_data);
						return true;
					}
				}
			} else {
				$replace_data = array(
					'old_id'                => $replace_id,
					'dependent_plan_id'     => $dependent_plan->dependent_plan_id,
					'expired_date'          => $last_day_of_coverage,
					'start_date'            => date('Y-m-d', strtotime($input['plan_start'])),
					'status'                => 0,
					'replace_status'        => 0,
					'deactivate_dependent_status' => 0,
					'first_name'            => $input['first_name'],
					'last_name'             => $input['last_name'],
					'nric'                  => $input['nric'],
					'dob'                   => date('Y-m-d', strtotime($input['dob'])),
					'relationship'          => $input['relationship'],
					'postal_code'			=> null
				);
				$dependent_replace->createReplacement($replace_data);
				return true;
			}
			return false;
		}

		public static function getCompanyEmployee($customer_id)
		{
			$account = DB::table('customer_link_customer_buy')
			->where('customer_buy_start_id', $customer_id)
			->first();

			return DB::table('corporate_members')
			->where('corporate_id', $account->corporate_id)
			->pluck('user_id');

		}

		public static function getEmployeeAnnualCapMedical($user_id)
		{
			$wallet = DB::table('e_wallet')
			->where('UserID', $user_id)
			->orderBy('created_at', 'desc')
			->first();

			$spend = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->whereYear('created_at', '>=', date('Y'))
			->whereYear('created_at', '<=', date('Y'))
			->where('logs', 'deducted_from_mobile_payment')
			->whereIn('where_spend', ['e_claim_transaction', 'in_network_transaction'])
			->sum('credit');

			$credits_back = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->whereYear('created_at', '>=', date('Y'))
			->whereYear('created_at', '<=', date('Y'))
			->where('logs', 'credits_back_from_in_network')
			->sum('credit');

			$total_spent = $spend - $credits_back;
			return $total_spent;
		}

		public static function getEmployeeAnnualCapWellness($user_id)
		{
			$wallet = DB::table('e_wallet')
			->where('UserID', $user_id)
			->orderBy('created_at', 'desc')
			->first();

			$spend = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->whereYear('created_at', '>=', date('Y'))
			->whereYear('created_at', '<=', date('Y'))
			->where('logs', 'deducted_from_mobile_payment')
			->whereIn('where_spend', ['e_claim_transaction', 'in_network_transaction'])
			->sum('credit');

			$credits_back = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->whereYear('created_at', '>=', date('Y'))
			->whereYear('created_at', '<=', date('Y'))
			->where('logs', 'credits_back_from_in_network')
			->sum('credit');

			$total_spent = $spend - $credits_back;
			return $total_spent;
		}

		public static function getEmployeePlanTier($user_id)
		{
			$customer_id = self::getCustomerId($user_id);
			$plan_tier = null;

			if($customer_id) {
				$plan_tier = DB::table('plan_tier_users')
				->join('plan_tiers', 'plan_tiers.plan_tier_id', '=', 'plan_tier_users.plan_tier_id')
				->where('plan_tier_users.status', 1)
				->where('plan_tiers.active', 1)
				->where('plan_tier_users.user_id', $user_id)
				->where('plan_tiers.customer_id', $customer_id)
				->first();
			}

			if($plan_tier) {
				return $plan_tier;
			} else {
				return false;
			}
		}

		public static function getAccountCorporateID($customer_id)
		{
			$account = DB::table('customer_link_customer_buy')
			->where('customer_buy_start_id', $customer_id)
			->first();

			if($account) {
				return $account->corporate_id;
			} else {
				return false;
			}
		}

		public static function getEmployeePendingRemoveAccount($user_ids)
		{
			return DB::table('customer_replace_employee')
			->whereIn('old_id', $user_ids)
			->where('deactive_employee_status', 0)
			->count();
		}

		public static function getCompanyMemberIds($customer_id)
		{
			$corporate_id = self::getAccountCorporateID($customer_id);

			if(!$corporate_id) {
				return false;
			}

			$user_ids = [];

			$members = DB::table('corporate_members')
			->where('corporate_id', $corporate_id)
			->get();

			foreach ($members as $key => $member) {
				$user_ids[] = (int)$member->user_id;

				$dependents = DB::table('employee_family_coverage_sub_accounts')
				->where('owner_id', $member->user_id)
                                // ->pluck('user_id');
				->get();

				if(sizeof($dependents) > 0) {
					foreach ($dependents as $key => $dependent) {
						$user_ids[] = (int)$dependent->user_id;
					}
				}
			}

			return $user_ids;
		}

		public static function getUserAccountType($user_id)
		{
			$type = StringHelper::checkUserType($user_id);
			if((int)$type['user_type'] == 5 && (int)$type['access_type'] == 0 || (int)$type['user_type'] == 5 && (int)$type['access_type'] == 1)
			{
				return "employee";
			} else if((int)$type['user_type'] == 5 && (int)$type['access_type'] == 2 || (int)$type['user_type'] == 5 && (int)$type['access_type'] == 3){
				return "dependent";
			} else {
				return "public";
			}
		}

		public static function getDependentOwnerID($user_id)
		{
			$type = StringHelper::checkUserType($user_id);
			if((int)$type['user_type'] == 5 && (int)$type['access_type'] == 0 || (int)$type['user_type'] == 5 && (int)$type['access_type'] == 1)
			{
				return $user_id;
			} else {
                // find owner
				$owner = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $user_id)->first();
				return $owner->owner_id;
			}
		}

		public static function checkSpouseDependent($user_id)
		{
			$check = DB::table('employee_family_coverage_sub_accounts')
			->where('owner_id', $user_id)
			->where('relationship', 'spouse')
			->where('deleted', 0)
			->first();

			if($check) {
				return true;
			}

			return false;
		}

		public static function removeDependent($id, $expiry_date, $refund_status, $vacate_seat)
		{

			$dependent_plan = DB::table('dependent_plan_history')
			->where('user_id', $id)
			->where('type', 'started')
			->orderBy('created_at', 'desc')
			->first();

			$diff = date_diff(new DateTime(date('Y-m-d', strtotime($dependent_plan->plan_start))), new DateTime(date('Y-m-d')));
			$days = $diff->format('%a') + 1;
            // $payment_log = DB::table('customer_cheque_logs')
            //                  ->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
            //                  ->first();
			$invoice = DB::table('dependent_invoice')
			->where('dependent_plan_id', $dependent_plan->dependent_plan_id)
			->first();

			$total_days = date("z", mktime(0,0,0,12,31,date('Y'))) + 1;
			$remaining_days = $total_days - $days;

			$cost_plan_and_days = ($invoice->individual_price/$total_days);
			$temp_total = $cost_plan_and_days * $remaining_days;
			$total_refund = $temp_total * 0.70;
			
			$withdraw = new DependentPlanWithdraw();

            // save history
			$depent_plan_history = new DependentPlanHistory();
			$user_plan_history_data = array(
				'user_id'       => $id,
				'type'          => "deleted_expired",
				'plan_start'          => $expiry_date,
				'dependent_plan_id' => $dependent_plan->dependent_plan_id,
				'duration'      => $dependent_plan->duration,
				'package_group_id' => $dependent_plan->package_group_id,
				'fixed'         => $dependent_plan->fixed
			);

			try {
				$depent_plan_history->createData($user_plan_history_data);
                // check if active plan and date refund is exist
				$refund = DB::table('dependent_payment_refund')
				->where('dependent_plan_id', $dependent_plan->dependent_plan_id)
				->where('date_refund', date('Y-m-d', strtotime($expiry_date)))
				->where('status', 0)
				->first();
				if($refund) {
                    // save plan withdraw logs
					$payment_refund_id = $refund->dependent_payment_refund_id;
				} else {
					$payment_refund_id = self::createPaymentsRefundDependent($dependent_plan->dependent_plan_id, date('Y-m-d', strtotime($expiry_date)));
				}

				$amount = number_format($total_refund, 2);
				$data = array(
					'dependent_payment_refund_id'         => $payment_refund_id,
					'user_id'                   => $id,
					'dependent_plan_id'   => $dependent_plan->dependent_plan_id,
					'date_withdraw'             => $expiry_date,
					'status'                    => 2,
					'amount'                    => $amount,
					'vacate_seat'				=> 1
				);

				$withdraw->createPlanWithdraw($data);

                // $user = DB::table('user')->where('UserID', $id)->first();
				$user_data = array(
					'Active'    => 0
				);
                // update user and set to inactive
				DB::table('user')->where('UserID', $id)->update($user_data);
                // set company members removed to 1
				DB::table('employee_family_coverage_sub_accounts')
				->where('user_id', $id)->update(['deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
				// if($vacate_seat) {
					self::updateCustomerDependentPlanStatusDeleteUserVacantSeat($id);
				// } else {
				// 	self::updateCustomerDependentPlanStatusDeleteUser($id);
				// }
                // check if dependent has plan tier
				$plan_tier_user = DB::table('plan_tier_users')
				->where('user_id', $id)
				->first();
				if($plan_tier_user) {
                    // update status
					DB::table('plan_tier_users')
					->where('user_id', $id)
					->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
                    // update plan tier dependent count
					$tier = new PlanTier();
					$tier->decrementDependentEnrolledHeadCount($plan_tier_user->plan_tier_id);
				}
				return TRUE;
			} catch(Exception $e) {
				$email = [];
				$email['end_point'] = url('hr/with_draw_dependent', $parameter = array(), $secure = null);
				$email['logs'] = 'Withdraw Employee Failed - '.$e;
				$email['emailSubject'] = 'Error log.';
				EmailHelper::sendErrorLogs($email);
				return FALSE;
			}

			return TRUE;
		}

		public static function createDependentWithdraw($id, $expiry_date, $status, $refund_status, $vacate_seat)
		{

			$dependent_plan = DB::table('dependent_plan_history')
			->where('user_id', $id)
			->where('type', 'started')
			->orderBy('created_at', 'desc')
			->first();

			$diff = date_diff(new DateTime(date('Y-m-d', strtotime($dependent_plan->plan_start))), new DateTime(date('Y-m-d')));
			$days = $diff->format('%a') + 1;
            // $payment_log = DB::table('customer_cheque_logs')
            //                  ->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
            //                  ->first();
			$invoice = DB::table('dependent_invoice')
			->where('dependent_plan_id', $dependent_plan->dependent_plan_id)
			->first();

			$total_days = date("z", mktime(0,0,0,12,31,date('Y'))) + 1;
			$remaining_days = $total_days - $days;

			$cost_plan_and_days = ($invoice->individual_price/$total_days);
			$temp_total = $cost_plan_and_days * $remaining_days;
			$total_refund = $temp_total * 0.70;
			
			$withdraw = new DependentPlanWithdraw();

            // save history
			$depent_plan_history = new DependentPlanHistory();
			$user_plan_history_data = array(
				'user_id'       => $id,
				'type'          => "deleted_expired",
				'plan_start'          => $expiry_date,
				'dependent_plan_id' => $dependent_plan->dependent_plan_id,
				'duration'      => $dependent_plan->duration,
				'package_group_id' => $dependent_plan->package_group_id,
				'fixed'         => $dependent_plan->fixed
			);

			try {
				$depent_plan_history->createData($user_plan_history_data);
                // check if active plan and date refund is exist
				$refund = DB::table('dependent_payment_refund')
				->where('dependent_plan_id', $dependent_plan->dependent_plan_id)
				->where('date_refund', date('Y-m-d', strtotime($expiry_date)))
				->where('status', 0)
				->first();
				if($refund) {
                    // save plan withdraw logs
					$payment_refund_id = $refund->dependent_payment_refund_id;
				} else {
					$payment_refund_id = self::createPaymentsRefundDependent($dependent_plan->dependent_plan_id, date('Y-m-d', strtotime($expiry_date)));
				}

				$amount = number_format($total_refund, 2);
				$data = array(
					'dependent_payment_refund_id'         => $payment_refund_id,
					'user_id'                   => $id,
					'dependent_plan_id'   => $dependent_plan->dependent_plan_id,
					'date_withdraw'             => $expiry_date,
					'amount'                    => $amount,
					'status'					=> 2,
					'vacate_seat'				=> 1
				);

				$withdraw->createPlanWithdraw($data);
				return TRUE;
			} catch(Exception $e) {
				$email = [];
				$email['end_point'] = url('hr/with_draw_dependent', $parameter = array(), $secure = null);
				$email['logs'] = 'Withdraw Employee Failed - '.$e;
				$email['emailSubject'] = 'Error log.';
				EmailHelper::sendErrorLogs($email);
				return FALSE;
			}

			return TRUE;
		}

		public static function updateCustomerDependentPlanStatusDeleteUser($id)
		{
			$user_plan = DB::table('dependent_plan_history')
			->where('user_id', $id)
			->orderBy('created_at', 'desc')
			->first();

			$active_plan = DB::table('dependent_plans')
			->where('dependent_plan_id', $user_plan->dependent_plan_id)
			->first();

			$Customer_PlanStatus = new DependentPlanStatus( );

			if($active_plan->account_type == "stand_alone_plan" || $active_plan->account_type == "lite_plan") {
                // deduct inputted and enrolled
				$Customer_PlanStatus->addjustCustomerStatus('total_dependents', $active_plan->customer_plan_id, 'decrement', 1);
				$Customer_PlanStatus->addjustCustomerStatus('total_enrolled_dependents', $active_plan->customer_plan_id, 'decrement', 1);
			} else {
				$Customer_PlanStatus->addjustCustomerStatus('total_enrolled_dependents', $active_plan->customer_plan_id, 'decrement', 1);
			}
		}

		public static function updateCustomerDependentPlanStatusDeleteUserVacantSeat($id)
		{
			$user_plan = DB::table('dependent_plan_history')
			->where('user_id', $id)
			->orderBy('created_at', 'desc')
			->first();

			$active_plan = DB::table('dependent_plans')
			->where('dependent_plan_id', $user_plan->dependent_plan_id)
			->first();

			$Customer_PlanStatus = new DependentPlanStatus( );
			$Customer_PlanStatus->addjustCustomerStatus('total_enrolled_dependents', $active_plan->customer_plan_id, 'decrement', 1);
		}

		public static function checkDependentRemovalStatus($user_id)
		{
			$check_withdraw = DB::table('dependent_plan_withdraw')->where('user_id', $user_id)->first();

			if($check_withdraw) {
				return array('status' => true, 'message' => 'Dependent Account already in Dependent Plan Withdrawal');
			}

			$replace = DB::table('customer_replace_dependent')
			->where('old_id', $user_id)
			->first();

			if($replace) {
				return array('status' => true, 'message' => 'Dependent Account already in Replace Dependent Plan.');
			}

			$seat = DB::table('dependent_replacement_seat')
			->where('user_id', $user_id)
			->first();

			if($seat) {
				return array('status' => true, 'message' => 'Dependent Account already in Replace Dependent Plan Seat.');
			}

			return array('status' => false);
		}

		public static function revemoDependentAccounts($user_id, $expiry)
		{
			$dependents = DB::table('employee_family_coverage_sub_accounts')
			->where('owner_id', $user_id)
			->where('deleted', 0)
			->get();

			$date = date('Y-m-d');

			foreach ($dependents as $key => $dependent) {
				if($date >= $expiry) {
				// create refund and delete now
					try {
						$result = self::removeDependent($dependent->user_id, $expiry, true, false);
						if(!$result) {
							return array('status' => FALSE, 'message' => 'Failed to create withdraw dependent. Please contact Mednefits and report the issue.');
						}
					} catch(Exception $e) {
						$email = [];
						$email['end_point'] = url('hr/with_draw_dependent', $parameter = array(), $secure = null);
						$email['logs'] = 'Withdraw Dependent Failed - '.$e;
						$email['emailSubject'] = 'Error log.';
						EmailHelper::sendErrorLogs($email);
						// return array('status' => FALSE, 'message' => 'Failed to create withdraw employee. Please contact Mednefits and report the issue.');
					}
				} else {
					try {
						$result = self::createDependentWithdraw($dependent->user_id, $expiry, true, true, false);
					} catch(Exception $e) {
						$email = [];
						$email['end_point'] = url('hr/with_draw_dependent', $parameter = array(), $secure = null);
						$email['logs'] = 'Withdraw Dependent Failed - '.$e;
						$email['emailSubject'] = 'Error log.';
						EmailHelper::sendErrorLogs($email);
						// return array('status' => FALSE, 'message' => 'Failed to create withdraw dependent. Please contact Mednefits and report the issue.');
					}
				}
			}
		}

		public static function removeDependentAccountsReplace($user_id, $expiry_date)
		{
			$dependents = DB::table('employee_family_coverage_sub_accounts')
			->where('owner_id', $user_id)
			->where('deleted', 0)
			->get();

			foreach ($dependents as $key => $dependent) {
                // check if dependent plan is refundable
				$dependepent_plan_history = DB::table('dependent_plan_history')
				->where('user_id', $dependent->user_id)
				->orderBy('created_at', 'desc')
				->first();
				$dependent_plan = DB::table('dependent_plans')
				->where('dependent_plan_id', $dependepent_plan_history->dependent_plan_id)
				->first();

				// if($dependent_plan->account_type == "stand_alone_plan" || $dependent_plan->account_type == "lite_plan") {
    //                 // create refund
				// 	$refund = self::removeDependent($dependent->user_id, $expiry_date, $refund_status, $vacate_seat);
				// } else {
					$dependent_plan = DB::table('dependent_plan_history')
					->where('user_id', $dependent->user_id)
					->where('type', 'started')
					->orderBy('created_at', 'desc')
					->first();

                    // save history
					$depent_plan_history = new DependentPlanHistory();
					$user_plan_history_data = array(
						'user_id'       => $dependent->user_id,
						'type'          => "deleted_expired",
						'plan_start'          => $expiry_date,
						'dependent_plan_id' => $dependent_plan->dependent_plan_id,
						'duration'      => $dependent_plan->duration,
						'package_group_id' => $dependent_plan->package_group_id,
						'fixed'         => $dependent_plan->fixed
					);

					try {
						$depent_plan_history->createData($user_plan_history_data);
                        // $user = DB::table('user')->where('UserID', $dependent->user_id)->first();
						$user_data = array(
							'Active'    => 0
						);
                        // update user and set to inactive
						DB::table('user')->where('UserID', $dependent->user_id)->update($user_data);
                        // set company members removed to 1
						DB::table('employee_family_coverage_sub_accounts')
						->where('user_id', $dependent->user_id)->update(['deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
						self::updateCustomerDependentPlanStatusDeleteUser($dependent->user_id);
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
					} catch(Exception $e) {
						$email = [];
						$email['end_point'] = url('hr/remove_dependent_account', $parameter = array(), $secure = null);
						$email['logs'] = 'Remove Dependent Account Failed - '.$e;
						$email['emailSubject'] = 'Error log.';
						EmailHelper::sendErrorLogs($email);
					}
				// }

				$admin_logs = array(
	                'admin_id'  => null,
	                'type'      => 'removed_dependent_schedule_system_generate',
	                'data'      => SystemLogLibrary::serializeData($dependent)
	            );
	            SystemLogLibrary::createAdminLog($admin_logs);
			}

			return true;
		}

		public static function removeDependentAccounts($user_id, $expiry_date, $refund_status, $vacate_seat)
		{
			$dependents = DB::table('employee_family_coverage_sub_accounts')
			->where('owner_id', $user_id)
			->where('deleted', 0)
			->get();

			foreach ($dependents as $key => $dependent) {
                // check if dependent plan is refundable
				$dependepent_plan_history = DB::table('dependent_plan_history')
				->where('user_id', $dependent->user_id)
				->orderBy('created_at', 'desc')
				->first();
				$dependent_plan = DB::table('dependent_plans')
				->where('dependent_plan_id', $dependepent_plan_history->dependent_plan_id)
				->first();

				if($dependent_plan->account_type == "stand_alone_plan" || $dependent_plan->account_type == "lite_plan") {
                    // create refund
					$refund = self::removeDependent($dependent->user_id, $expiry_date, $refund_status, $vacate_seat);
				} else {
					$dependent_plan = DB::table('dependent_plan_history')
					->where('user_id', $dependent->user_id)
					->where('type', 'started')
					->orderBy('created_at', 'desc')
					->first();

                    // save history
					$depent_plan_history = new DependentPlanHistory();
					$user_plan_history_data = array(
						'user_id'       => $dependent->user_id,
						'type'          => "deleted_expired",
						'plan_start'          => $expiry_date,
						'dependent_plan_id' => $dependent_plan->dependent_plan_id,
						'duration'      => $dependent_plan->duration,
						'package_group_id' => $dependent_plan->package_group_id,
						'fixed'         => $dependent_plan->fixed
					);

					try {
						$depent_plan_history->createData($user_plan_history_data);
                        // $user = DB::table('user')->where('UserID', $dependent->user_id)->first();
						$user_data = array(
							'Active'    => 0
						);
                        // update user and set to inactive
						DB::table('user')->where('UserID', $dependent->user_id)->update($user_data);
                        // set company members removed to 1
						DB::table('employee_family_coverage_sub_accounts')
						->where('user_id', $dependent->user_id)->update(['deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
						self::updateCustomerDependentPlanStatusDeleteUser($dependent->user_id);
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
					} catch(Exception $e) {
						$email = [];
						$email['end_point'] = url('hr/remove_dependent_account', $parameter = array(), $secure = null);
						$email['logs'] = 'Remove Dependent Account Failed - '.$e;
						$email['emailSubject'] = 'Error log.';
						EmailHelper::sendErrorLogs($email);
					}
				}

				$admin_logs = array(
	                'admin_id'  => null,
	                'type'      => 'removed_dependent_schedule_system_generate',
	                'data'      => SystemLogLibrary::serializeData($dependent)
	            );
	            SystemLogLibrary::createAdminLog($admin_logs);
			}

			return true;
		}

		public static function updateCustomerPlanStatusDeleteUser($id)
		{
			$user_plan = DB::table('user_plan_history')->where('user_id', $id)->orderBy('date', 'desc')->first();
			$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_plan->customer_active_plan_id)->first();
			$Customer_PlanStatus = new CustomerPlanStatus( );

			if($active_plan->account_type == "stand_alone_plan" || $active_plan->account_type == "lite_plan" || $active_plan->account_type == "trial_plan") {
                // deduct inputted and enrolled
				$Customer_PlanStatus->addjustCustomerStatus('employees_input', $active_plan->plan_id, 'decrement', 1);
				$Customer_PlanStatus->addjustCustomerStatus('enrolled_employees', $active_plan->plan_id, 'decrement', 1);
			} else {
				$Customer_PlanStatus->addjustCustomerStatus('enrolled_employees', $active_plan->plan_id, 'decrement', 1);
                // \CustomerPlanStatus::where('customer_plan_id', $active_plan->plan_id)->decrement('enrolled_employees', 1);
			}
		}

		public static function updateCustomerPlanStatusDeleteUserVacantSeat($id)
		{
			$user_plan = DB::table('user_plan_history')->where('user_id', $id)->orderBy('date', 'desc')->first();
			$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_plan->customer_active_plan_id)->first();
			$Customer_PlanStatus = new CustomerPlanStatus( );
			$Customer_PlanStatus->addjustCustomerStatus('enrolled_employees', $active_plan->plan_id, 'decrement', 1);
		}

		public static function updateNewCustomerPlanStatusDeleteUser($id, $refund_status)
		{
			$user_plan = DB::table('user_plan_history')->where('user_id', $id)->orderBy('date', 'desc')->first();
			$active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_plan->customer_active_plan_id)->first();
			$Customer_PlanStatus = new CustomerPlanStatus( );

			$customer_plan_id = $active_plan->plan_id;
			// check if active plan has a plan extenstion
			if((int)$active_plan->plan_extention_enable == 1) {
			// check active plan extension
				$extension = DB::table('plan_extensions')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->first();

				if($extension && (int)$extension->enable == 1) {
				  $active_plan = $extension;
				}
			}

			if($active_plan->account_type == "stand_alone_plan" && $refund_status == true || $active_plan->account_type == "lite_plan" && $refund_status == true && $active_plan->account_type == "trial_plan" && $active_plan->secondary_account_type == "pro_trial_plan_bundle") {
                // deduct inputted and enrolled
				$Customer_PlanStatus->addjustCustomerStatus('employees_input', $customer_plan_id, 'decrement', 1);
				$Customer_PlanStatus->addjustCustomerStatus('enrolled_employees', $customer_plan_id, 'decrement', 1);
			} else {
				$Customer_PlanStatus->addjustCustomerStatus('enrolled_employees', $customer_plan_id, 'decrement', 1);
                // \CustomerPlanStatus::where('customer_plan_id', $active_plan->plan_id)->decrement('enrolled_employees', 1);
			}
		}

		public static function getEmployeeLastDayCoverage($user_id)
		{

			$user = DB::table('user')->where('UserID', $user_id)->first();
			$replace = DB::table('customer_replace_employee')
			->where('old_id', $user_id)
			->first();
			if($replace) {
				return $replace->expired_and_activate;
			}

			$withdraw = DB::table('customer_plan_withdraw')
			->where('user_id', $user_id)
			->first();
			if($withdraw) {
				return $withdraw->date_withdraw;
			}

			$seat = DB::table('employee_replacement_seat')
			->where('user_id', $user_id)
			->first();
			if($seat) {
				return $seat->last_date_of_coverage;
			}

			if($user->Active == 0) {
				$user_active_plan_history = DB::table('user_plan_history')
				->where('user_id', $user_id)
                                    // ->where('type', 'started')
				->orderBy('created_at', 'desc')
				->first();
				return $user_active_plan_history->plan_start;
			}

			return false;
		}

		public static function getEmployeePlanEndDate($user_id)
		{
			$get_employee_plan = DB::table('user_plan_type')
			->where('user_id', $user_id)
			->orderBy('created_at', 'desc')
			->first();

			$user_active_plan_history = DB::table('user_plan_history')
			->where('user_id', $user_id)
			->orderBy('created_at', 'desc')
			->first();

			$active_plan_customer = DB::table('customer_active_plan')
			->where('customer_active_plan_id', $user_active_plan_history->customer_active_plan_id)
			->first();
			$plan = DB::table('customer_plan')
			->where('customer_buy_start_id', $active_plan_customer->customer_start_buy_id)
			->orderBy('created_at', 'desc')
			->first();

			$active_plan = DB::table('customer_active_plan')
			->where('plan_id', $plan->customer_plan_id)
			->first();


			if((int)$active_plan->plan_extention_enable == 1) {
				$plan_user_history = DB::table('user_plan_history')
				->where('user_id', $user_id)
				->where('type', 'started')
				->orderBy('created_at', 'desc')
				->first();
				if(!$plan_user_history) {
                        // create plan user history
					PlanHelper::createUserPlanHistory($user_id, $link_account->customer_buy_start_id, $customer_id);
					$plan_user_history = DB::table('user_plan_history')
					->where('user_id', $user_id)
					->where('type', 'started')
					->orderBy('created_at', 'desc')
					->first();
				}

				$plan_user = DB::table('user_plan_type')
				->where('user_id', $user_id)
				->orderBy('created_at', 'desc')
				->first();
				
				$active_plan = DB::table('customer_active_plan')
				->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)
				->first();
				
				$first_active_plan = DB::table('customer_active_plan')
				->where('plan_id', $active_plan->plan_id)
				->first();

				$plan = DB::table('customer_plan')
				->where('customer_plan_id', $first_active_plan->plan_id)
				->first();

				$active_plan_extension = DB::table('plan_extensions')
				->where('customer_active_plan_id', $first_active_plan->customer_active_plan_id)
				->first();
				
				if($plan_user->fixed == 1 || $plan_user->fixed == "1") {
					$temp_valid_date = date('Y-m-d', strtotime('+'.$active_plan_extension->duration, strtotime($active_plan_extension->plan_start)));
					$expiry_date = date('Y-m-d', strtotime('-1 day', strtotime($temp_valid_date)));
				} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
					$expiry_date = date('Y-m-d', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
				}
			} else {
				$plan_user_history = DB::table('user_plan_history')
				->where('user_id', $user_id)
				->where('type', 'started')
				->orderBy('created_at', 'desc')
				->first();
				if(!$plan_user_history) {
                    // create plan user history
					PlanHelper::createUserPlanHistory($user_id, $link_account->customer_buy_start_id, $customer_id);
					$plan_user_history = DB::table('user_plan_history')
					->where('user_id', $user_id)
					->where('type', 'started')
					->orderBy('created_at', 'desc')
					->first();
				}
				$plan_user = DB::table('user_plan_type')
				->where('user_id', $user_id)
				->orderBy('created_at', 'desc')
				->first();

				$active_plan = DB::table('customer_active_plan')
				->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)
				->first();

				$plan = DB::table('customer_plan')
				->where('customer_plan_id', $active_plan->plan_id)
				->first();
				
				$first_active_plan = DB::table('customer_active_plan')
				->where('plan_id', $active_plan->plan_id)
				->first();
				
				if($plan_user->fixed == 1 || $plan_user->fixed == "1") {
					$temp_valid_date = date('Y-m-d', strtotime('+'.$first_active_plan->duration, strtotime($plan->plan_start)));
					$expiry_date = date('Y-m-d', strtotime('-1 day', strtotime($temp_valid_date)));
				} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
					$expiry_date = date('Y-m-d', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
				}
			}

			return $expiry_date;
		}

		public static function checkCompanyBlockAccess($user_id, $clinic_id)
		{
			$customer_id = StringHelper::getCustomerId($user_id);

			if(!$customer_id) {
				return false;
			}

			$clinic_block = DB::table('company_block_clinic_access')
			->where('customer_id', $customer_id)
			->where('clinic_id', $clinic_id)
			->where('account_type', 'company')
			->where('status', 1)
			->first();

			if($clinic_block) {
				return true;
			} else {
				// check for employee
				$employee_block = DB::table('company_block_clinic_access')
						->where('customer_id', $user_id)
						->where('clinic_id', $clinic_id)
						->where('account_type', 'employee')
						->where('status', 1)
						->first();
				if($employee_block) {
					return true;
				}
				return false;
			}
		}

		public static function getPlanNameType($account_type)
		{
			if($account_type == "stand_alone_plan") {
				return "Pro Plan";
			} else if($account_type == "insurance_bundle") {
				return "Insurance Bundle";
				$data['complimentary'] = TRUE;
			} else if($account_type == "trial_plan") {
				return "Trial Plan";
			} else if($account_type == "lite_plan") {
				return "Lite Plan";
			} else if($account_type == "enterprise_plan") {
				return "Enterprise Plan";
			}
		}

		public static function getEmployeeCoverageStatus($user_id)
		{
			$get_employee_plan = DB::table('user_plan_type')
			->where('user_id', $user_id)
			->orderBy('created_at', 'desc')
			->first();

			$user_active_plan_history = DB::table('user_plan_history')
			->where('user_id', $user_id)
			->orderBy('created_at', 'desc')
			->first();

			$active_plan_customer = DB::table('customer_active_plan')
			->where('customer_active_plan_id', $user_active_plan_history->customer_active_plan_id)
			->first();
			$plan = DB::table('customer_plan')
			->where('customer_buy_start_id', $active_plan_customer->customer_start_buy_id)
			->orderBy('created_at', 'desc')
			->first();

			$active_plan = DB::table('customer_active_plan')
			->where('plan_id', $plan->customer_plan_id)
			->first();


			if((int)$active_plan->plan_extention_enable == 1) {
				$plan_user_history = DB::table('user_plan_history')
				->where('user_id', $user_id)
				->where('type', 'started')
				->orderBy('created_at', 'desc')
				->first();
				if(!$plan_user_history) {
                        // create plan user history
					PlanHelper::createUserPlanHistory($user_id, $link_account->customer_buy_start_id, $customer_id);
					$plan_user_history = DB::table('user_plan_history')
					->where('user_id', $user_id)
					->where('type', 'started')
					->orderBy('created_at', 'desc')
					->first();
				}

				$plan_user = DB::table('user_plan_type')
				->where('user_id', $user_id)
				->orderBy('created_at', 'desc')
				->first();
				
				$active_plan = DB::table('customer_active_plan')
				->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)
				->first();
				
				$first_active_plan = DB::table('customer_active_plan')
				->where('plan_id', $active_plan->plan_id)
				->first();

				$plan = DB::table('customer_plan')
				->where('customer_plan_id', $first_active_plan->plan_id)
				->first();

				$active_plan_extension = DB::table('plan_extensions')
				->where('customer_active_plan_id', $first_active_plan->customer_active_plan_id)
				->first();
				
				if($plan_user->fixed == 1 || $plan_user->fixed == "1") {
					$temp_valid_date = date('Y-m-d', strtotime('+'.$active_plan_extension->duration, strtotime($active_plan_extension->plan_start)));
					$expiry_date = date('Y-m-d', strtotime('-1 day', strtotime($temp_valid_date)));
				} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
					$expiry_date = date('Y-m-d', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
				}
			} else {
				$plan_user_history = DB::table('user_plan_history')
				->where('user_id', $user_id)
				->where('type', 'started')
				->orderBy('created_at', 'desc')
				->first();
				if(!$plan_user_history) {
                    // create plan user history
					PlanHelper::createUserPlanHistory($user_id, $link_account->customer_buy_start_id, $customer_id);
					$plan_user_history = DB::table('user_plan_history')
					->where('user_id', $user_id)
					->where('type', 'started')
					->orderBy('created_at', 'desc')
					->first();
				}
				$plan_user = DB::table('user_plan_type')
				->where('user_id', $user_id)
				->orderBy('created_at', 'desc')
				->first();

				$active_plan = DB::table('customer_active_plan')
				->where('customer_active_plan_id', $plan_user_history->customer_active_plan_id)
				->first();

				$plan = DB::table('customer_plan')
				->where('customer_plan_id', $active_plan->plan_id)
				->first();
				
				$first_active_plan = DB::table('customer_active_plan')
				->where('plan_id', $active_plan->plan_id)
				->first();
				
				if($plan_user->fixed == 1 || $plan_user->fixed == "1") {
					$temp_valid_date = date('Y-m-d', strtotime('+'.$first_active_plan->duration, strtotime($plan->plan_start)));
					$expiry_date = date('Y-m-d', strtotime('-1 day', strtotime($temp_valid_date)));
				} else if($plan_user->fixed == 0 | $plan_user->fixed == "0") {
					$expiry_date = date('Y-m-d', strtotime('+'.$plan_user->duration, strtotime($plan_user->plan_start)));
				}
			}

			$plan_start = date('Y-m-d', strtotime($get_employee_plan->plan_start));

			$expired = false;
			if(date('Y-m-d') > $expiry_date) {
				$expired = true;
			}

			return array('plan_start' => $plan_start, 'plan_end' => $expiry_date, 'expired' => $expired);
		}

		public static function resetEmployeeAccount($employee_id)
		{
			// get admin session from mednefits admin login
			$admin_id = Session::get('admin-session-id');
			$hr_data = StringHelper::getJwtHrSession();
			$hr_id = $hr_data->hr_dashboard_id;
			$user = DB::table('user')->where('UserID', $employee_id)->first();

			if(!$user) {
				return array('status' => false, 'message' => 'Employee not found');
			}

			$member_corporate = DB::table('corporate_members')->where('user_id', $employee_id)->first();
			$corporate = DB::table('corporate')->where('corporate_id', $member_corporate->corporate_id)->first();

			$user_plan = DB::table('user_plan_type')
							->where('user_id', $employee_id)
							->orderBy('created_at', 'desc')
							->first();
			$password = StringHelper::get_random_password(8);
			$result = DB::table('user')->where('UserID', $employee_id)->update(['password' => md5($password)]);
			$start_date = date('Y-m-d', strtotime($user_plan->plan_start));

			if($result) {
				if($user->communication_type == "email") {
					if($user->Email) {
						$user_plan_history = DB::table('user_plan_history')
												->where('user_id', $employee_id)
												->orderBy('created_at', 'desc')
												->first();
						$active_plan = DB::table('customer_active_plan')
											->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
											->first();

						$emailDdata['emailName']= ucwords($user->Name);
						$emailDdata['emailPage'] = 'email-templates.latest-templates.mednefits-welcome-member-enrolled';
						$emailDdata['emailTo']= $user->Email;
						$emailDdata['email']= $user->PhoneNo;
						$emailDdata['name']= $user->Name;
						$emailDdata['emailSubject'] = "WELCOME TO MEDNEFITS CARE";
						$emailDdata['pw'] = $password;
						$emailDdata['plan'] = $active_plan;
						$emailDdata['user_id'] = $employee_id;
						$emailDdata['company'] = ucwords($corporate->company_name);
						$emailDdata['start_date'] = date('F d, Y', strtotime($start_date));
						\EmailHelper::sendEmail($emailDdata);

						if($admin_id) {
							$admin_logs = array(
			                    'admin_id'  => $admin_id,
			                    'admin_type' => 'mednefits',
			                    'type'      => 'admin_hr_employee_reset_account_details',
			                    'data'      => SystemLogLibrary::serializeData($emailDdata)
			                );
			                SystemLogLibrary::createAdminLog($admin_logs);
						} else {
							$admin_logs = array(
			                    'admin_id'  => $hr_id,
			                    'admin_type' => 'hr',
			                    'type'      => 'admin_hr_employee_reset_account_details',
			                    'data'      => SystemLogLibrary::serializeData($emailDdata)
			                );
			                SystemLogLibrary::createAdminLog($admin_logs);
						}

						return array('status' => true, 'message' => 'Employee Account Resetted and sent using email.');
					} else {
						if($user->PhoneNo) {
							$phone = SmsHelper::newformatNumber($user);

		                    if($phone) {
		                    	$compose = [];
								$compose['name'] = $user->Name;
								$compose['company'] = $corporate->company_name;
								$compose['plan_start'] = date('F d, Y', strtotime($start_date));
								$compose['email'] = null;
								$compose['nric'] = $user->PhoneNo;
								$compose['password'] = $password;
								$compose['phone'] = $phone;
								$compose['sms_type'] = "LA";

								$compose['message'] = SmsHelper::formatWelcomeEmployeeMessage($compose);
								$result_sms = SmsHelper::sendSms($compose);

								if($admin_id) {
									$admin_logs = array(
					                    'admin_id'  => $admin_id,
					                    'admin_type' => 'mednefits',
					                    'type'      => 'admin_hr_employee_reset_account_details',
					                    'data'      => SystemLogLibrary::serializeData($compose)
					                );
					                SystemLogLibrary::createAdminLog($admin_logs);
								} else {
									$admin_logs = array(
					                    'admin_id'  => $hr_id,
					                    'admin_type' => 'hr',
					                    'type'      => 'admin_hr_employee_reset_account_details',
					                    'data'      => SystemLogLibrary::serializeData($compose)
					                );
					                SystemLogLibrary::createAdminLog($admin_logs);
								}

								return array('status' => true, 'message' => 'Employee Account Resetted and sent using sms.');
		                    } else {
		                    	return array('status' => false, 'message' => 'Employee Account Resetted and but was not able to send using sms because of mobile phone number malformed. Please update the mobile phone of this employee to be able to send an sms.');
		                    }
						}
					}
				} else {
					if($user->PhoneNo) {
						$phone = SmsHelper::newformatNumber($user);

	                    if($phone) {
	                    	$compose = [];
							$compose['name'] = $user->Name;
							$compose['company'] = $corporate->company_name;
							$compose['plan_start'] = date('F d, Y', strtotime($start_date));
							$compose['email'] = null;
							$compose['nric'] = $user->PhoneNo;
							$compose['password'] = $password;
							$compose['phone'] = $phone;

							$compose['message'] = SmsHelper::formatWelcomeEmployeeMessage($compose);
							$result_sms = SmsHelper::sendSms($compose);
							return array('status' => true, 'message' => 'Employee Account Resetted and sent using sms.');
	                    } else {
	                    	return array('status' => false, 'message' => 'Employee Account Resetted and but was not able to send using sms because of mobile phone number malformed. Please update the mobile phone of this employee to be able to send an sms.');
	                    }
					}
				}

			} else {
				return array('status' => false, 'message' => 'Failed to reset Employee Account.');
			}
		}

		public static function getEmployeeStatus($user_id)
		{
			// check if schedule for replacement
			$replacement = DB::table('customer_replace_employee')->where('old_id', $user_id)->first();
			$schedule = null;
			$schedule_status = false;
			$date_deleted = null;
			$deleted = false;
			$plan_withdraw = false;
			$status = null;
			$emp_status = null;

			if($replacement) {
				if((int)$replacement->deactive_employee_status == 1 && (int)$replacement->status == 1 && (int)$replacement->replace_status == 1) {
					// replaced
					if($replacement->start_date == null) {
						$schedule = 'Removed '.date('d/m/Y', strtotime($replacement->expired_and_activate));
					} else {
						$schedule = 'Removed '.date('d/m/Y', strtotime($replacement->expired_and_activate));
					}
					return array('status' => true, 'schedule' => $schedule, 'schedule_status' => $schedule_status, 'expiry_date' => date('m/d/Y', strtotime($replacement->expired_and_activate)), 'deleted' => true, 'plan_withdraw' => $plan_withdraw, 'emp_status' => 'deleted');
				} else if((int)$replacement->deactive_employee_status == 0 && (int)$replacement->replace_status == 0) {
					// to be replace
					$schedule_status = true;
					if($replacement->expired_and_activate) {
						$schedule = 'Last Day of Coverage/End Date '.date('d/m/Y', strtotime($replacement->expired_and_activate));
					} else {
						$schedule = 'Last Day of Coverage/End Date '.date('d/m/Y', strtotime($replacement->start_date));
					}
					return array('status' => true, 'schedule' => $schedule, 'schedule_status' => $schedule_status, 'expiry_date' => date('m/d/Y', strtotime($replacement->expired_and_activate)), 'deleted' => false, 'plan_withdraw' => $plan_withdraw, 'emp_status' => 'schedule');
				} else {
					//
					$schedule_status = true;
					if($replacement->expired_and_activate) {
						$schedule = 'Last Day of Coverage/End Date '.date('d/m/Y', strtotime($replacement->expired_and_activate));
					} else {
						$schedule = 'Last Day of Coverage/End Date '.date('d/m/Y', strtotime($replacement->start_date));
					}
				}

				if(date('Y-m-d') > $replacement->expired_and_activate) {
					$emp_status = 'pending_deletion';
				} else {
					$emp_status = 'shedule';
				}

				return array('status' => true, 'schedule' => $schedule, 'schedule_status' => $schedule_status, 'expiry_date' => date('m/d/Y', strtotime($replacement->expired_and_activate)), 'deleted' => false, 'plan_withdraw' => $plan_withdraw, 'emp_status' => $emp_status);
			}

			$deleted_accounts = DB::table("customer_plan_withdraw")->where("user_id", $user_id)->first();

			if($deleted_accounts) {
				$date_deleted = date('d/m/Y', strtotime($deleted_accounts->date_withdraw));
				$plan_withdraw = true;
				if((int)$deleted_accounts->refund_status == 0) {
					$schedule_status = true;
					$schedule = "Last Day of Coverage/End Date ".date('d/m/Y', strtotime($deleted_accounts->date_withdraw));
					
					if(date('Y-m-d') > $deleted_accounts->date_withdraw) {
						$emp_status = 'pending_deletion';
					} else {
						$emp_status = 'shedule';
					}

					return array('status' => true, 'schedule' => $schedule, 'schedule_status' => $schedule_status, 'expiry_date' => date('m/d/Y', strtotime($deleted_accounts->date_withdraw)), 'deleted' => true, 'plan_withdraw' => $plan_withdraw, 'emp_status' => $emp_status);
				} else if((int)$deleted_accounts->refund_status == 1 || (int)$deleted_accounts->refund_status == 2) {
					$schedule_status = false;
					$schedule = 'Removed on '.date('d/m/Y', strtotime($deleted_accounts->date_withdraw));

					if($deleted_accounts->refund_status == 2 && $deleted_accounts->date_withdraw > date('Y-m-d')) {
						// schedule
						$schedule_status = true;
						$schedule = 'Last Day of Coverage/End Date '.date('d/m/Y', strtotime($deleted_accounts->date_withdraw));
					}

					if(date('Y-m-d') > $deleted_accounts->date_withdraw) {
						$emp_status = 'pending_deletion';
					} else {
						$emp_status = 'shedule';
					}

					return array('status' => true, 'schedule' => $schedule, 'schedule_status' => $schedule_status, 'expiry_date' => date('m/d/Y', strtotime($deleted_accounts->date_withdraw)), 'deleted' => true, 'plan_withdraw' => $plan_withdraw, 'emp_status' => $emp_status);
				}
			}

			return array('status' => false, 'emp_status' => 'active');
		}

		public static function getDependentPlanCoverage($user_id)
		{
			$dependent_plan_history = DB::table('dependent_plan_history')
											->where('user_id', $user_id)
											->where('type', 'started')
											->orderBy('created_at', 'desc')
											->first();

            $dependent_plan = DB::table('dependent_plans')->where('dependent_plan_id', $dependent_plan_history->dependent_plan_id)->first();

            $plan = DB::table('customer_plan')->where('customer_plan_id', $dependent_plan->customer_plan_id)->orderBy('created_at', 'desc')->first();

            $active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
            $data['plan_start'] = date('F d, Y', strtotime($dependent_plan_history->plan_start));

            if((int)$dependent_plan_history->fixed == 1 || $dependent_plan_history->fixed == "1") {
                $temp_valid_date = date('Y-m-d', strtotime('+'.$active_plan->duration, strtotime($plan->plan_start)));
                $data['valid_date'] = date('F d, Y', strtotime('-1 day', strtotime($temp_valid_date)));
            } else if($dependent_plan_history->fixed == 0 | $dependent_plan_history->fixed == "0") {
                $data['valid_date'] = date('F d, Y', strtotime('+'. $plan_user->duration, strtotime($dependent_plan_history->plan_start)));
            }

            if(date('Y-m-d') > date('Y-m-d', strtotime($data['valid_date']))) {
				$data['expired'] = TRUE;
			} else {
				$data['expired'] = FALSE;
			}

			if(date('Y-m-d', strtotime($dependent_plan_history->plan_start)) > date('Y-m-d')) {
				$data['pending'] = true;
			} else {
				$data['pending'] = false;
			}

			$data['user_type'] = "dependents";

			return $data;
		}

		public static function hrStatus( )
		{
			$hr = StringHelper::getJwtHrSession();
			$settings = DB::table('customer_buy_start')->where('customer_buy_start_id', $hr->customer_buy_start_id)->first();

			if((int)$settings->qr_payment == 1 && (int)$settings->wallet == 1) {
				$accessibility = 1;
			} else {
				$accessibility = 0;
			}

			$session = array(
				'hr_dashboard_id'				=> $hr->hr_dashboard_id,
				'customer_buy_start_id'			=> $hr->customer_buy_start_id,
				'qr_payment'					=> $settings->qr_payment,
				'wallet'						=> $settings->wallet,
				'accessibility'					=> $accessibility,
				'expire_in'						=> $hr->expire_in,
				'signed_in'						=> $hr->signed_in
			);
			return $session;
		}

		public static function reCalculateCompanyBalance( )
		{
			$result = StringHelper::getJwtHrSession();
			$customer_id = $result->customer_buy_start_id;
			$allocated = 0;
			$total_allocation = 0;
			$deleted_employee_allocation = 0;
			$total_deduction_credits = 0;

			$allocated_wellness = 0;
			$total_allocation_wellness = 0;
			$deleted_employee_allocation_wellness = 0;
			$total_wellnesss_allocated = 0;
			$total_deduction_credits_wellness = 0;
			$total_medical_allocation = 0;
			$total_medical_allocated = 0;
			$credits = 0;
			$get_allocation_spent = 0;
			$credits_wellness = 0;
			$get_allocation_spent_wellness = 0;

			$check_accessibility = self::hrStatus( );

			if($check_accessibility['accessibility'] == 1) {
				$account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();

		    	// check if customer has a credit reset in medical
				$customer_credit_reset_medical = DB::table('credit_reset')
				->where('id', $customer_id)
				->where('spending_type', 'medical')
				->where('user_type', 'company')
				->orderBy('created_at', 'desc')
				->first();

				if($customer_credit_reset_medical) {
					// $start = date('Y-m-d', strtotime($customer_credit_reset_medical->date_resetted));
		    		// $end = SpendingInvoiceLibrary::getEndDate($customer_credit_reset_medical->date_resetted);
					$temp_total_allocation = DB::table('customer_credits')
					->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
					->where('customer_credits.customer_id', $customer_id)
					->where('customer_credit_logs.logs', 'admin_added_credits')
					// ->where('customer_credit_logs.created_at', '>=', date('Y-m-d', strtotime($start)))
					->where('customer_credit_logs.customer_credit_logs_id', '>=', $customer_credit_reset_medical->wallet_history_id)
					->sum('customer_credit_logs.credit');

					$temp_total_deduction = DB::table('customer_credits')
					->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
					->where('customer_credits.customer_id', $customer_id)
					->where('customer_credit_logs.logs', 'admin_deducted_credits')
					// ->where('customer_credit_logs.created_at', '>=', date('Y-m-d', strtotime($start)))
					->where('customer_credit_logs.customer_credit_logs_id', '>=', $customer_credit_reset_medical->wallet_history_id)
					->sum('customer_credit_logs.credit');
				} else {
					$temp_total_allocation = DB::table('customer_credits')
					->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
					->where('customer_credits.customer_id', $customer_id)
					->where('customer_credit_logs.logs', 'admin_added_credits')
					->sum('customer_credit_logs.credit');

					$temp_total_deduction = DB::table('customer_credits')
					->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
					->where('customer_credits.customer_id', $customer_id)
					->where('customer_credit_logs.logs', 'admin_deducted_credits')
					->sum('customer_credit_logs.credit');

				}
				$total_allocation = $temp_total_allocation - $temp_total_deduction;


			    // check if customer has a credit reset in medical
				$customer_credit_reset_wellness = DB::table('credit_reset')
				->where('id', $customer_id)
				->where('spending_type', 'wellness')
				->where('user_type', 'company')
				->orderBy('created_at', 'desc')
				->first();
				// return array('res' => $customer_credit_reset_wellness);
				if($customer_credit_reset_wellness) {
					$start = date('Y-m-d', strtotime($customer_credit_reset_medical->date_resetted));
					$temp_total_allocation_wellness = DB::table('customer_credits')
					->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
					->where('customer_credits.customer_id', $customer_id)
					->where('customer_wellness_credits_logs.logs', 'admin_added_credits')
					// ->where('customer_wellness_credits_logs.created_at', '>=', date('Y-m-d', strtotime($start)))
					->where('customer_wellness_credits_logs.customer_wellness_credits_history_id', '>=', $customer_credit_reset_wellness->wallet_history_id)
					->sum('customer_wellness_credits_logs.credit');

					$temp_total_deduction_wellness = DB::table('customer_credits')
					->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
					->where('customer_credits.customer_id', $customer_id)
					->where('customer_wellness_credits_logs.logs', 'admin_deducted_credits')
					// ->where('customer_wellness_credits_logs.created_at', '>=', date('Y-m-d', strtotime($start)))
					->where('customer_wellness_credits_logs.customer_wellness_credits_history_id', '>=', $customer_credit_reset_wellness->wallet_history_id)
					->sum('customer_wellness_credits_logs.credit');
				} else {
					$temp_total_allocation_wellness = DB::table('customer_credits')
					->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
					->where('customer_credits.customer_id', $customer_id)
					->where('customer_wellness_credits_logs.logs', 'admin_added_credits')
					->sum('customer_wellness_credits_logs.credit');

					$temp_total_deduction_wellness = DB::table('customer_credits')
					->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
					->where('customer_credits.customer_id', $customer_id)
					->where('customer_wellness_credits_logs.logs', 'admin_deducted_credits')
					->sum('customer_wellness_credits_logs.credit');
				}
				$total_allocation_wellness = $temp_total_allocation_wellness - $temp_total_deduction_wellness;
		        // return array('medical' => $total_allocation, 'wellness' => $total_allocation_wellness);

				$user_allocated = self::getCorporateUserByAllocated($account_link->corporate_id, $customer_id);
		        // return $user_allocated;
				$get_allocation_spent = 0;
				$get_allocation_spent_wellness = 0;
				foreach ($user_allocated as $key => $user) {
					if($user) {
						$get_allocation = 0;
						$deducted_allocation = 0;
						$e_claim_spent = 0;
						$in_network_temp_spent = 0;
						$credits_back = 0;

						$get_allocation_wellness = 0;
						$deducted_allocation_wellness = 0;
						$e_claim_spent_wellness = 0;
						$in_network_temp_spent_wellness = 0;
						$credits_back_wellness = 0;

						$wallet = DB::table('e_wallet')->where('UserID', $user)->orderBy('created_at', 'desc')->first();

						$pro_allocation_medical = DB::table('wallet_history')
						->where('wallet_id', $wallet->wallet_id)
						->where('logs', 'pro_allocation')
						->sum('credit');
						
						$member = DB::table('corporate_members')->where('user_id', $user)->first();

						if($pro_allocation_medical == 0) {
							// check if employee has reset credits
							$employee_credit_reset_medical = DB::table('credit_reset')
							->where('id', $user)
							->where('spending_type', 'medical')
							->where('user_type', 'employee')
							->orderBy('created_at', 'desc')
							->first();

							if($employee_credit_reset_medical) {
								$start = date('Y-m-d', strtotime($employee_credit_reset_medical->date_resetted));
				    			// $end = SpendingInvoiceLibrary::getEndDate($employee_credit_reset_medical->date_resetted);
								$wallet_history = DB::table('wallet_history')
								->where('wallet_id', $wallet->wallet_id)
								->where('created_at', '>=', date('Y-m-d', strtotime($start)))
				    								// ->where('created_at', '<=', $end)
								->get();
							} else {
								$wallet_history = DB::table('wallet_history')->where('wallet_id', $wallet->wallet_id)->get();
							}

							foreach ($wallet_history as $key => $history) {
								if($history->logs == "added_by_hr") {
									$get_allocation += $history->credit;
								}

								if($history->logs == "deducted_by_hr") {
									$deducted_allocation += $history->credit;
								}

								if($history->where_spend == "e_claim_transaction") {
									$e_claim_spent += $history->credit;
								}

								if($history->where_spend == "in_network_transaction") {
									$in_network_temp_spent += $history->credit;
								}

								if($history->where_spend == "credits_back_from_in_network") {
									$credits_back += $history->credit;
								}
							}

							if($pro_allocation_medical > 0) {
								$allocation = $pro_allocation_medical;
							} else {
								$allocation = $get_allocation;
								$total_deduction_credits += $deducted_allocation;

								if((int)$member->removed_status == 1) {
									$deleted_employee_allocation += $get_allocation - $deducted_allocation;
								}
							}

							$get_allocation_spent += $in_network_temp_spent - $credits_back + $e_claim_spent;
							
							$allocated += $allocation;

						if((int)$member->removed_status == 1) {
							$deleted_employee_allocation += $get_allocation - $deducted_allocation;
						}

						$pro_allocation_wellness = DB::table('wellness_wallet_history')
						->where('wallet_id', $wallet->wallet_id)
						->where('logs', 'pro_allocation')
						->sum('credit');

						if($pro_allocation_wellness == 0) {
							$employee_credit_reset_wellness = DB::table('credit_reset')
							->where('id', $user)
							->where('spending_type', 'wellness')
							->where('user_type', 'employee')
							->orderBy('created_at', 'desc')
							->first();

							if($employee_credit_reset_wellness) {
								$start = date('Y-m-d', strtotime($employee_credit_reset_wellness->date_resetted));
				    			// $end = SpendingInvoiceLibrary::getEndDate($employee_credit_reset_wellness->date_resetted);
								$wallet_wellness_history = DB::table('wellness_wallet_history')
								->where('wallet_id', $wallet->wallet_id)
								->where('created_at', '>=', date('Y-m-d', strtotime($start)))
				    										// ->where('created_at', '<=', $end)
								->get();
							} else {
								$wallet_wellness_history = DB::table('wellness_wallet_history')->where('wallet_id', $wallet->wallet_id)->get();
							}

							foreach ($wallet_wellness_history as $key => $history) {
								if($history->logs == "added_by_hr") {
									$get_allocation_wellness += $history->credit;
								}

								if($history->logs == "deducted_by_hr") {
									$deducted_allocation_wellness += $history->credit;
								}

								if($history->where_spend == "e_claim_transaction") {
									$e_claim_spent_wellness += $history->credit;
								}

								if($history->where_spend == "in_network_transaction") {
									$in_network_temp_spent_wellness += $history->credit;
								}

								if($history->where_spend == "credits_back_from_in_network") {
									$credits_back_wellness += $history->credit;
								}
							}
							
							$allocation_wellness = $get_allocation_wellness;


							if($pro_allocation_wellness > 0) {
								$allocation = $pro_allocation_wellness;
							} else {
								$allocation = $allocation_wellness;
								$total_deduction_credits_wellness += $deducted_allocation_wellness;

								if((int)$member->removed_status == 1) {
									$deleted_employee_allocation_wellness += $get_allocation_wellness - $deducted_allocation_wellness;
								}
							}

							$get_allocation_spent_wellness += $in_network_temp_spent_wellness - $credits_back_wellness + $e_claim_spent_wellness;
							$allocated_wellness += $allocation_wellness;
						}						
					}

				}


				// $total_allocated = $total_allocation - $allocated - $deleted_employee_allocation + $total_deduction_credits;

				$company_credits = DB::table('customer_credits')->where('customer_id', $customer_id)->first();

				$total_medical_allocation = $total_allocation;
				$total_medical_allocated = $allocated - $deleted_employee_allocation - $total_deduction_credits;

				$total_wellnesss_allocated = $allocated_wellness - $deleted_employee_allocation_wellness - $total_deduction_credits_wellness;

				$credits = $total_medical_allocation - $total_medical_allocated;
				$credits_wellness = $total_allocation_wellness - $total_wellnesss_allocated;

				if($company_credits->balance != $credits) {
					// update medical credits
					\CustomerCredits::where('customer_id', $customer_id)->update(['balance' => $credits]);
				}

				if($company_credits->wellness_credits != $credits_wellness) {
					// update wellness credits
					\CustomerCredits::where('customer_id', $customer_id)->update(['wellness_credits' => $credits_wellness]);
				}
			}

			return array(
				'total_medical_company_allocation' => number_format($total_medical_allocation, 2),
				'total_medical_company_unallocation' => number_format($credits, 2),
				'total_medical_employee_allocated' => number_format($total_medical_allocated, 2),
				'total_medical_employee_spent'		=> number_format($get_allocation_spent, 2),
				'total_medical_employee_balance' => number_format($total_medical_allocated - $get_allocation_spent, 2),
				'total_medical_employee_balance_number' => $total_medical_allocated - $get_allocation_spent,
				'total_medical_wellness_allocation' => number_format($total_allocation_wellness, 2),
				'total_medical_wellness_unallocation' => number_format($credits_wellness, 2),
				'total_wellness_employee_allocated' => number_format($total_wellnesss_allocated, 2),
				'total_wellness_employee_spent'		=> number_format($get_allocation_spent_wellness, 2),
				'total_wellness_employee_balance' => number_format($total_wellnesss_allocated - $get_allocation_spent_wellness, 2),
				'total_wellness_employee_balance_number' => $total_wellnesss_allocated - $get_allocation_spent_wellness,
				'company_id' => $result->customer_buy_start_id,
			);
		}
	}

	public static function getUserFirstPlanStart($user_id)
	{
			$plan_user = DB::table('user_plan_type')
										->where('user_id', $user_id)
										->first();

			if($plan_user) {
				return $plan_user->plan_start;
			} else {
				return false;
			}
	}

	public static function checkCompanyAllocated($customer_id)
	{
		$wallet = DB::table('customer_credits')->where('customer_id', $customer_id)->first();

		$medical = DB::table('customer_credit_logs')
						->where('customer_credits_id', $wallet->customer_credits_id)
						->where('logs', 'added_employee_credits')
						->first();

		$wellness = DB::table('customer_wellness_credits_logs')
						->where('customer_credits_id', $wallet->customer_credits_id)
						->where('logs', 'added_employee_credits')
						->first();

		if($medical || $wellness) {
			return true;
		} else {
			return false;
		}
	}
}
?>