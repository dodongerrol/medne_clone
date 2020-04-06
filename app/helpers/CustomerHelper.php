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
			$today = date('Y-m-d H:i:s');
			$credit_resets = DB::table('credit_reset')
												->where('id', $member_id)
												->where('user_type', 'company')
												->where('spending_type', $spending_type)
												->orderBy('created_at', 'desc')
												->first();
			if($spending_type == "medical") {
				if($credit_resets) {
					return ['start' => $credit_resets->date_resetted, 'end' => $today, 'id' => $credit_resets->wallet_history_id];
				} else {
					$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $member_id)->orderBy('created_at', 'desc')->first();
					$wallet = DB::table('customer_credits')->where('customer_id', $member_id)->first();
					return ['start' => date('Y-m-d', strtotime($wallet->created_at)), 'end' => date('Y-m-d', strtotime($spending_accounts->medical_spending_end_date)), 'id' => null];
				}
			} else {
				if($credit_resets) {
					return ['start' => $credit_resets->date_resetted, 'end' => $today, 'id' => $credit_resets->wallet_history_id];
				} else {
					$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $member_id)->orderBy('created_at', 'desc')->first();
					$wallet = DB::table('customer_credits')->where('customer_id', $member_id)->first();
					return ['start' => date('Y-m-d', strtotime($wallet->created_at)), 'end' => date('Y-m-d', strtotime($spending_accounts->medical_spending_end_date)), 'id' => null];
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
				$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $member_id)->first();
				$wallet = DB::table('customer_credits')->where('customer_id', $member_id)->first();
				return ['start' => date('Y-m-d', strtotime($spending_accounts->medical_spending_start_date)), 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($credit_resets[0]->date_resetted)))), 'id' => null];
			} else {
				return false;
			}
		}
	}

	public static function getCustomerDateTerms($member_id, $term, $spending_type)
	{
		if($term == "current_term") {
			// $today = date('Y-m-d H:i:s');
			// $credit_resets = DB::table('credit_reset')
			// 									->where('id', $member_id)
			// 									->where('user_type', 'company')
			// 									->where('spending_type', $spending_type)
			// 									->orderBy('created_at', 'desc')
			// 									->first();
			// if($spending_type == "medical") {
			// 	if($credit_resets) {
			// 		return ['start' => $credit_resets->date_resetted, 'end' => $today, 'id' => $credit_resets->wallet_history_id];
			// 	} else {
			// 		$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $member_id)->orderBy('created_at', 'desc')->first();
			// 		$wallet = DB::table('customer_credits')->where('customer_id', $member_id)->first();
			// 		return ['start' => date('Y-m-d', strtotime($spending_accounts->medical_spending_start_date)), 'end' => date('Y-m-d', strtotime($spending_accounts->medical_spending_end_date)), 'id' => null];
			// 	}
			// } else {
			// 	if($credit_resets) {
			// 		return ['start' => $credit_resets->date_resetted, 'end' => $today, 'id' => $credit_resets->wallet_history_id];
			// 	} else {
			// 		$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $member_id)->orderBy('created_at', 'desc')->first();
			// 		$wallet = DB::table('customer_credits')->where('customer_id', $member_id)->first();
			// 		return ['start' => date('Y-m-d', strtotime($spending_accounts->medical_spending_start_date)), 'end' => date('Y-m-d', strtotime($spending_accounts->medical_spending_end_date)), 'id' => null];
			// 	}
			// }
			$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $member_id)->orderBy('created_at', 'desc')->first();
			return ['start' => date('Y-m-d', strtotime($spending_accounts->medical_spending_start_date)), 'end' => date('Y-m-d', strtotime($spending_accounts->medical_spending_end_date)), 'id' => null];
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
				$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $member_id)->first();
				$wallet = DB::table('customer_credits')->where('customer_id', $member_id)->first();
				return ['start' => date('Y-m-d', strtotime($spending_accounts->medical_spending_start_date)), 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($credit_resets[0]->date_resetted)))), 'id' => null];
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

	public static function getAccountSpendingStatus($customer_id)	
	{
		$spending = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$activePlan = DB::table('customer_active_plan')->where('customer_start_buy_id', $customer_id)->first();

		return array(
			'customer_id'		=> $customer_id,
			'account_type'		=> $activePlan->account_type,
			'medical_method'	=> $spending->medical_plan_method,
			'wellness_method'	=> $spending->wellness_plan_method,
			'paid_status'		=> $activePlan->paid == 'true' ? true : false
		);
	}

	public static function getExcelLinkBasicPlan($status)
	{
		if($status['medical_method'] == "pre_paid" && $status['wellness_method'] == "pre_paid" && $status['paid_status'] == true)	{
			return array(
				'status' => true,
				'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/employee/Employee-Enrollment-Listing-Post-Medical-Post-Wellness.xlsx',
				'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/depedents/Employees-and-Dependents-Post-Medical-Post-Wellness.xlsx'
			);
		} else if($status['medical_method'] == "pre_paid" && $status['wellness_method'] == "post_paid" && $status['paid_status'] == true)	{
			return array(
				'status' => true,
				'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/employee/Employee-Enrollment-Listing-Post-Medical-Post-Wellness.xlsx',
				'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/depedents/Employees-and-Dependents-Post-Medical-Post-Wellness.xlsx'
			);
		} else if($status['medical_method'] == "post_paid" && $status['wellness_method'] == "pre_paid" && $status['paid_status'] == true)	{
			return array(
				'status' => true,
				'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/employee/Employee-Enrollment-Listing-Post-Medical-Post-Wellness.xlsx',
				'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/depedents/Employees-and-Dependents-Post-Medical-Post-Wellness.xlsx'
			);
		} else if($status['medical_method'] == "pre_paid" && $status['wellness_method'] == "pre_paid" && $status['paid_status'] == false) {
			return array(
				'status' => true,
				'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/employee/Employee-Enrollment-Listing-Pending-Medical-Wellness.xlsx',
				'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/depedents/Employees-and-Dependents-Pending-Medical-Wellness.xlsx'
			);
		} else if($status['medical_method'] == "pre_paid" && $status['wellness_method'] == "post_paid" && $status['paid_status'] == false)	{
			return array(
				'status' => true,
				'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/employee/Employee-Enrollment-Listing-Pending-Medical-Post-Wellness.xlsx',
				'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/depedents/Employees-and-Dependents-Pending-Medical-Post-Wellness.xlsx'
			);
		} else if($status['medical_method'] == "post_paid" && $status['wellness_method'] == "pre_paid" && $status['paid_status'] == false) {
			return array(
				'status' => true,
				'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/employee/Employee-Enrollment-Listing-Post-Medical-Pending-Wellness.xlsx',
				'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/depedents/Employees-and-Dependents-Post-Medical-Pending-Wellness.xlsx'
			);
		} else {
			return array('status' => false);
		}
	}

	public static function getAccountSpendingBasicPlanStatus($customer_id)	
	{
		$spending = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$activePlan = DB::table('customer_active_plan')->where('customer_start_buy_id', $customer_id)->first();

		return array(
			'customer_id'		=> $customer_id,
			'account_type'		=> $activePlan->account_type,
			'medical_method'	=> $spending->medical_plan_method,
			'wellness_method'	=> $spending->wellness_plan_method,
			'paid_status'		=> $activePlan->paid == 'true' ? true : false
		);
	}
}
?>