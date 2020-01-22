<?php
class CustomerHelper
{
	public static function getCustomerWalletStatus($customer_id)
	{
		$spending_account = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$medical = false;
		$wellness = false;

		if((int)$spending_account->medical_enable == 1) {
			$medical = true;
		}

		if((int)$spending_account->wellness_enable == 1) {
			$wellness = true;
		}

		return ['status' => true, 'medical' => $medical, 'wellness' => $wellness];
	}

	public static function getCustomerCreditReset($member_id, $term, $spending_type)
	{
		if($term == "current_term") {
			$credit_resets = DB::table('credit_reset')
												->where('id', $member_id)
												->where('user_type', 'company')
												->where('spending_type', $spending_type)
												->orderBy('created_at', 'desc')
												->first();
			if($spending_type == "medical") {
				if($credit_resets) {
					$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $member_id)->orderBy('created_at', 'desc')->first();
					$wallet = DB::table('customer_credits')->where('customer_id', $member_id)->first();
					$wallet_history = DB::table('customer_credit_logs')->where('customer_credits_id', $wallet->customer_credits_id)->orderBy('created_at', 'desc')->first();
					return ['start' => $credit_resets->date_resetted, 'end' => date('Y-m-d', strtotime($spending_accounts->medical_spending_end_date)), 'id' => $credit_resets->wallet_history_id];
				} else {
					$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $member_id)->orderBy('created_at', 'desc')->first();
					$wallet = DB::table('customer_credits')->where('customer_id', $member_id)->first();
					return ['start' => $spending_accounts->medical_spending_start_date, 'end' => date('Y-m-d', strtotime($spending_accounts->medical_spending_end_date)), 'id' => null];
				}
			} else {
				if($credit_resets) {
					$wallet = DB::table('customer_credits')->where('customer_id', $member_id)->first();
					$wallet_history = DB::table('customer_wellness_credits_logs')->where('customer_credits_id', $wallet->customer_credits_id)->orderBy('created_at', 'desc')->first();
					return ['start' => $credit_resets->date_resetted, 'end' => date('Y-m-d', strtotime($wallet_history->created_at)), 'id' => $credit_resets->wallet_history_id];
				} else {
					$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $member_id)->orderBy('created_at', 'desc')->first();
					$wallet = DB::table('customer_credits')->where('customer_id', $member_id)->first();
					return ['start' => $wallet->created_at, 'end' => date('Y-m-d', strtotime($spending_accounts->medical_spending_end_date)), 'id' => null];
				}
			}
		} else {
			$credit_resets = DB::table('credit_reset')
												->where('id', $member_id)
												->where('user_type', 'company')
												->where('spending_type', $spending_type)
												->get();

			if(sizeof($credit_resets) > 1) {
				$credit_reset_start = DB::table('credit_reset')
												->where('id', $member_id)
												->where('user_type', 'company')
												->where('spending_type', $spending_type)
												->orderBy('created_at', 'desc')
												->skip(1)
												->take(1)
												->first();

				if($credit_reset_start) {
					$credit_reset_end = DB::table('credit_reset')
													->where('id', $member_id)
													->where('user_type', 'company')
													->where('spending_type', $spending_type)
													->orderBy('created_at', 'desc')
													->first();
					if($credit_reset_end) {
						// return ['credit_reset_start' => $credit_reset_start, 'credit_reset_end' => $credit_reset_end];
						return ['start' => $credit_reset_start->date_resetted, 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($credit_reset_end->date_resetted)))), 'id' => $credit_reset_start->wallet_history_id];
					} else {
						$wallet = DB::table('customer_credits')->where('customer_id', $member_id)->first();
						if($spending_type == "medical") {
							$wallet_history = DB::table('customer_credit_logs')->where('wallet_id', $wallet->customer_credits_id)->orderBy('created_at', 'desc')->first();
							return ['start' => $credit_reset_start->date_resetted, 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($wallet_history->created_at)))), 'id' => $credit_reset_start->wallet_history_id];
						} else {
							$wallet_history = DB::table('customer_wellness_credits_logs')->where('wallet_id', $wallet->customer_credits_id)->orderBy('created_at', 'desc')->first();
							return ['start' => $credit_reset_start->date_resetted, 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($wallet_history->created_at)))), 'id' => $credit_reset_start->wallet_history_id];
						}
					}
				} else {
					$wallet = DB::table('customer_credits')->where('customer_id', $member_id)->first();
					return ['start' => date('Y-m-d', strtotime($wallet->created_at)), 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($credit_resets[0]->date_resetted)))), 'id' => $credit_resets[0]->wallet_history_id];
				}
			} else if(sizeof($credit_resets) == 1){
				$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $member_id)->orderBy('created_at', 'desc')->first();
				$wallet = DB::table('customer_credits')->where('customer_id', $member_id)->first();
				return ['start' => date('Y-m-d', strtotime($spending_accounts->medical_spending_start_date)), 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($credit_resets[0]->date_resetted)))), 'id' => $credit_resets[0]->wallet_history_id];
			} else {
				return false;
			}
		}
	}

	public static function customerMedicalAllocatedCreditsByDates($customer_id, $start, $end, $wallet_history_id)
	{

		if($wallet_history_id) {
			$temp_total_medical_allocation = DB::table('customer_credits')
			->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
			->where('customer_credits.customer_id', $customer_id)
			->where('customer_credit_logs.customer_credit_logs_id', '>=', $wallet_history_id)
			->where('customer_credit_logs.created_at', '>=', $start)
			->where('customer_credit_logs.created_at', '<=', $end)
			->where('customer_credit_logs.logs', 'admin_added_credits')
			->sum('customer_credit_logs.credit');

			$temp_total_medical_deduction = DB::table('customer_credits')
			->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
			->where('customer_credits.customer_id', $customer_id)
			->whereYear('customer_credit_logs.created_at', '>=', $start)
			->whereYear('customer_credit_logs.created_at', '<=', $end)
			->where('customer_credit_logs.customer_credit_logs_id', '>=', $wallet_history_id)
			->where('customer_credit_logs.logs', 'admin_deducted_credits')
			->sum('customer_credit_logs.credit');
		} else {
			$temp_total_medical_allocation = DB::table('customer_credits')
			->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
			->where('customer_credits.customer_id', $customer_id)
			->where('customer_credit_logs.created_at', '>=', $start)
			->where('customer_credit_logs.created_at', '<=', $end)
			->where('customer_credit_logs.logs', 'admin_added_credits')
			->sum('customer_credit_logs.credit');

			$temp_total_medical_deduction = DB::table('customer_credits')
			->join('customer_credit_logs', 'customer_credit_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
			->where('customer_credits.customer_id', $customer_id)
			->whereYear('customer_credit_logs.created_at', '>=', $start)
			->whereYear('customer_credit_logs.created_at', '<=', $end)
			->where('customer_credit_logs.logs', 'admin_deducted_credits')
			->sum('customer_credit_logs.credit');
		}

		$total_medical_allocation = $temp_total_medical_allocation - $temp_total_medical_deduction;
		return $total_medical_allocation;
	}

	public static function customerWellnessAllocatedCreditsByDates($customer_id, $start, $end, $wallet_history_id)
	{

		if($wallet_history_id) {
			$temp_total_wellness_allocation = DB::table('customer_credits')
			->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
			->where('customer_credits.customer_id', $customer_id)
			->where('customer_wellness_credits_logs.customer_wellness_credits_history_id', '>=', $wallet_history_id)
			->where('customer_wellness_credits_logs.created_at', '>=', $start)
			->where('customer_wellness_credits_logs.created_at', '<=', $end)
			->where('customer_wellness_credits_logs.logs', 'admin_added_credits')
			->sum('customer_wellness_credits_logs.credit');

			$temp_total_wellness_deduction = DB::table('customer_credits')
			->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
			->where('customer_credits.customer_id', $customer_id)
			->where('customer_wellness_credits_logs.customer_wellness_credits_history_id', '>=', $wallet_history_id)
			->where('customer_wellness_credits_logs.created_at', '>=', $start)
			->where('customer_wellness_credits_logs.created_at', '<=', $end)
			->where('customer_wellness_credits_logs.logs', 'admin_deducted_credits')
			->sum('customer_wellness_credits_logs.credit');
		} else {
			$temp_total_wellness_allocation = DB::table('customer_credits')
			->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
			->where('customer_credits.customer_id', $customer_id)
			->where('customer_wellness_credits_logs.created_at', '>=', $start)
			->where('customer_wellness_credits_logs.created_at', '<=', $end)
			->where('customer_wellness_credits_logs.logs', 'admin_added_credits')
			->sum('customer_wellness_credits_logs.credit');

			$temp_total_wellness_deduction = DB::table('customer_credits')
			->join('customer_wellness_credits_logs', 'customer_wellness_credits_logs.customer_credits_id', '=', 'customer_credits.customer_credits_id')
			->where('customer_credits.customer_id', $customer_id)
			->where('customer_wellness_credits_logs.created_at', '>=', $start)
			->where('customer_wellness_credits_logs.created_at', '<=', $end)
			->where('customer_wellness_credits_logs.logs', 'admin_deducted_credits')
			->sum('customer_wellness_credits_logs.credit');
		}

		$total_wellness_allocation = $temp_total_wellness_allocation - $temp_total_wellness_deduction;
		return $total_wellness_allocation;
	}
}
?>