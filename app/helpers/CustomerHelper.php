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

	public static function getCustomerLastTerm($customer_id)	
	{
		$plans = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->get();

		if(sizeof($plans) > 1) {
			$plans = DB::table('customer_plan')
						->where('customer_buy_start_id', $customer_id)
						->orderBy('created_at', 'desc')
						->skip(1)
						->take(1)
						->first();
			return ['start' => date('Y-m-d', strtotime($plans->plan_start)), 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($plans->plan_end)))), 'id' => null];
		} else {
			return ['start' => date('Y-m-d', strtotime($plans[0]->plan_start)), 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($plans[0]->plan_end)))), 'id' => null];
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

	public static function getCustomerIdFromToken($xAccessKey)
	{
		$todate =  date("Y-m-d H:i:s");
		$accessKeyDetails = DB::table('customer_accessKey')->where('accessKey', $xAccessKey)->first();

		if($accessKeyDetails) {
			$accessKeyExpiryDate = $accessKeyDetails->expiry_date;
			if($todate == $accessKeyExpiryDate) {
				return ['status' => false, 'message' => 'Access key already expired.'];
			}
			return ['status' => true, 'customer_id' => $accessKeyDetails->customer_id];
		} else {
			return ['status' => false, 'message' => 'key does not exist.'];
		}
	}
	
	public static function getAccountSpendingStatus($customer_id)	
	{
		$spending = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$customer_wallet = DB::table('customer_credits')->where('customer_id', $customer_id)->first();
		$planData = DB::table('customer_plan')->where('customer_plan_id', $spending->customer_plan_id)->first();
		$spendingPurchase = DB::table('spending_purchase_invoice')->where('customer_plan_id', $spending->customer_plan_id)->where("payment_status", 0)->count();
		// $activePlan = DB::table('customer_active_plan')->where('plan_id', $spending->customer_plan_id)->where("paid", "false")->count();

		return array(
			'customer_id'		=> $customer_id,
			'currency_type'		=> $customer_wallet->currency_type,
			'account_type'		=> $planData->account_type,
			'medical_method'	=> $spending->medical_plan_method,
			'medical_reimbursement'	=> $spending->medical_reimbursement == 1 ? true : false,
			'medical_enabled'	=> $spending->medical_enable == 1 ? true : false,
			'wellness_method'	=> $spending->wellness_plan_method,
			'wellness_enabled'	=> $spending->wellness_enable == 1 ? true : false,
			'wellness_reimbursement'	=> $spending->wellness_reimbursement == 1 ? true : false,
			'paid_status'		=> $planData->account_type == "lite_plan" && $planData->plan_method == "pre_paid" && $spendingPurchase > 0 ? false : true,
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
		} else if($status['account_type'] == "enterprise_plan" && $status['wellness_enabled'] == true) {
			return array(
				'status' => true,
				'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/employee/Employee-Enrollment-Listing-Enterprise-Wellness.xlsx',
				'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/depedents/Employees-and-Dependents-Enterprise-Wellness.xlsx'
			);
		} else if($status['account_type'] == "enterprise_plan" && $status['wellness_enabled'] == false) {
			return array(
				'status' => true,
				'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/employee/Employee-Enrollment-Listing-Enterprise.xlsx',
				'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v3/depedents/Employees-and-Dependents-Enterprise.xlsx'
			);
		} else {
			return array('status' => false);
		}
	}

	public static function getExcelLink($status)
	{

		if($status['currency_type'] == "myr") {
			if($status['account_type'] == "lite_plan" &&  $status['medical_method'] == "pre_paid" && $status['wellness_method'] == "pre_paid" && $status['paid_status'] == true)	{
				if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All+-+R.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-All-R.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
					);
				}
			} else if($status['account_type'] == "lite_plan" &&  $status['medical_method'] == "pre_paid" && $status['wellness_method'] == "post_paid" && $status['paid_status'] == true) {
				if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All+-+R.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-All-R.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
					);
				}
			} else if($status['account_type'] == "lite_plan" &&  $status['medical_method'] == "post_paid" && $status['wellness_method'] == "pre_paid" && $status['paid_status'] == true)	{
				if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All+-+R.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-All-R.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
					);
				}
			} else if($status['account_type'] == "lite_plan" &&  $status['medical_method'] == "pre_paid" && $status['wellness_method'] == "pre_paid" && $status['paid_status'] == false) {
				if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+NO+-+R.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-NO-R.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+NO+SA.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+NO-SA.xlsx'
					);
				}
			} else if($status['account_type'] == "lite_plan" &&  $status['medical_method'] == "pre_paid" && $status['wellness_method'] == "post_paid" && $status['paid_status'] == false)	{
				if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All+-+R+-+Wellness.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-R-Wellness.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+Wellness.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-Wellness.xlsx'
					);
				}
			} else if($status['account_type'] == "lite_plan" && $status['medical_method'] == "post_paid" && $status['wellness_method'] == "pre_paid" && $status['paid_status'] == false) {
				if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All+-+R+-+Medical.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-R-Medical.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+Medical.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-Medical.xlsx'
					);
				}
			} else if($status['account_type'] == "lite_plan" && $status['medical_method'] == "post_paid" && $status['wellness_method'] == "post_paid" && $status['paid_status'] == true) {
				if($status['medical_enabled'] == true && $status['wellness_enabled'] == true) {
					if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
						return array(
							'status' => true,
							'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All+-+R.xlsx',
							'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-All-R.xlsx'
						);
					} else {
						return array(
							'status' => true,
							'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All.xlsx',
							'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
						);
					}
				} else if($status['medical_enabled'] == true) {
					if($status['medical_reimbursement'] == true) {
						return array(
							'status' => true,
							'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All+-+R+-+Medical.xlsx',
							'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-Medical.xlsx'
						);
					} else {
						return array(
							'status' => true,
							'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+Medical.xlsx',
							'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-Medical.xlsx'
						);
					}
				} else if($status['wellness_enabled'] == true) {
					if($status['wellness_reimbursement'] == true) {
						return array(
							'status' => true,
							'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All+-+R+-+Wellness.xlsx',
							'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-R-Wellness.xlsx'
						);
					} else {
						return array(
							'status' => true,
							'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+Medical.xlsx',
							'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-Wellness.xlsx'
						);
					}
				}
			} else if($status['account_type'] == "enterprise_plan" && $status['wellness_enabled'] == true) {
				if($status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/enterprise/employee/Employee+R-Wellness.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/enterprise/dependents/Employees-and-Dependents+R-Wellness+.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/enterprise/employee/Employee-Wellness.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/enterprise/dependents/Employees-and-Dependents-Wellness.xlsx'
					);
				}
			} else if($status['account_type'] == "enterprise_plan" && $status['wellness_enabled'] == false) {
				return array(
					'status' => true,
					'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/enterprise/employee/Employee+NO-SA-R.xlsx',
					'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/enterprise/dependents/Employees-and-Dependents+NO-SA-R.xlsx'
				);
			} else if($status['account_type'] != "enterprise_plan" && $status['account_type'] != "lite_plan") {
				if($status['medical_enabled'] == true && $status['wellness_enabled'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
					);
				} else if($status['medical_enabled'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+Medical.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-Medical.xlsx'
					);
				} else if($status['medical_enabled'] == false) {
					return array(
						'status' => true,
						'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+Wellness.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-Wellness.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
					);
				}
			} else {
				return array(
					'status' => true,
					'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/employee/Employee+SA+-+All.xlsx',
					'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
				);
			}
		} else {
			if($status['account_type'] == "lite_plan" &&  $status['medical_method'] == "pre_paid" && $status['wellness_method'] == "pre_paid" && $status['paid_status'] == true)	{
				if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All+-+R.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-All-R.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
					);
				}
			} else if($status['account_type'] == "lite_plan" &&  $status['medical_method'] == "pre_paid" && $status['wellness_method'] == "post_paid" && $status['paid_status'] == true) {
				if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All+-+R.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-All-R.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
					);
				}
			} else if($status['account_type'] == "lite_plan" &&  $status['medical_method'] == "post_paid" && $status['wellness_method'] == "pre_paid" && $status['paid_status'] == true)	{
				if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All+-+R.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-All-R.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
					);
				}
			} else if($status['account_type'] == "lite_plan" &&  $status['medical_method'] == "pre_paid" && $status['wellness_method'] == "pre_paid" && $status['paid_status'] == false) {
				if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+NO+-+R.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-NO-R.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+NO+SA.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+NO-SA.xlsx'
					);
				}
			} else if($status['account_type'] == "lite_plan" &&  $status['medical_method'] == "pre_paid" && $status['wellness_method'] == "post_paid" && $status['paid_status'] == false)	{
				if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All+-+R+-+Wellness.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-R-Wellness.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+Wellness.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-Wellness.xlsx'
					);
				}
			} else if($status['account_type'] == "lite_plan" && $status['medical_method'] == "post_paid" && $status['wellness_method'] == "pre_paid" && $status['paid_status'] == false) {
				if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All+-+R+-+Medical.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-R-Medical.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+Medical.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-Medical.xlsx'
					);
				}
			} else if($status['account_type'] == "lite_plan" && $status['medical_method'] == "post_paid" && $status['wellness_method'] == "post_paid" && $status['paid_status'] == true) {
				if($status['medical_enabled'] == true && $status['wellness_enabled'] == true) {
					if($status['medical_reimbursement'] == true || $status['wellness_reimbursement'] == true) {
						return array(
							'status' => true,
							'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All+-+R.xlsx',
							'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-All-R.xlsx'
						);
					} else {
						return array(
							'status' => true,
							'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All.xlsx',
							'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
						);
					}
				} else if($status['medical_enabled'] == true) {
					if($status['medical_reimbursement'] == true) {
						return array(
							'status' => true,
							'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All+-+R+-+Medical.xlsx',
							'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-Medical.xlsx'
						);
					} else {
						return array(
							'status' => true,
							'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+Medical.xlsx',
							'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-Medical.xlsx'
						);
					}
				} else if($status['wellness_enabled'] == true) {
					if($status['wellness_reimbursement'] == true) {
						return array(
							'status' => true,
							'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All+-+R+-+Wellness.xlsx',
							'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-R-Wellness.xlsx'
						);
					} else {
						return array(
							'status' => true,
							'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+Medical.xlsx',
							'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-Wellness.xlsx'
						);
					}
				}
			} else if($status['account_type'] == "enterprise_plan" && $status['wellness_enabled'] == true) {
				if($status['wellness_reimbursement'] == true) {
					return array(
						'status' => true,
						'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/enterprise/employee/Employee+R-Wellness.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/enterprise/dependents/Employees-and-Dependents+R-Wellness+.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/enterprise/employee/Employee-Wellness.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/enterprise/dependents/Employees-and-Dependents-Wellness.xlsx'
					);
				}
			} else if($status['account_type'] == "enterprise_plan" && $status['wellness_enabled'] == false) {
				return array(
					'status' => true,
					'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/enterprise/employee/Employee+NO-SA-R.xlsx',
					'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/enterprise/dependents/Employees-and-Dependents+NO-SA-R.xlsx'
				);
			} else if($status['account_type'] != "enterprise_plan" && $status['account_type'] != "lite_plan") {
				if($status['medical_enabled'] == true && $status['wellness_enabled'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
					);
				} else if($status['medical_enabled'] == true) {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+Medical.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-Medical.xlsx'
					);
				} else if($status['medical_enabled'] == false) {
					return array(
						'status' => true,
						'employee'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+Wellness.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-Wellness.xlsx'
					);
				} else {
					return array(
						'status' => true,
						'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All.xlsx',
						'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
					);
				}
			} else {
				return array(
					'status' => true,
					'employee'	=>	'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/employee/Employee+SA+-+All.xlsx',
					'dependent'	=> 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v4/basic/dependent/Employees-and-Dependents+SA-All.xlsx'
				);
			}
		}
	}

	public static function getAccountSpendingBasicPlanStatus($customer_id)	
	{
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();
		$spending = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$planData = DB::table('customer_plan')->where('customer_plan_id', $spending->customer_plan_id)->first();
		// $activePlan = DB::table('customer_active_plan')->where('plan_id', $spending->customer_plan_id)->where("paid", "false")->count();
		$spendingPurchase = DB::table('spending_purchase_invoice')->where('customer_plan_id', $spending->customer_plan_id)->where("payment_status", 0)->count();

		return array(
			'customer_id'		=> $customer_id,
			'account_type'		=> $planData->account_type,
			'medical_method'	=> $spending->medical_plan_method,
			'medical_enabled'	=> $spending->medical_enable == 1 ? true : false,
			'wellness_method'	=> $spending->wellness_plan_method,
			'wellness_enabled'	=> $spending->wellness_enable == 1 ? true : false,
			'paid_status'		=> $planData->account_type == "lite_plan" && $planData->plan_method == "pre_paid" && $spendingPurchase > 0 ? false : true,
			'currency_type'		=> $customer->currency_type
		);
	}

	public static function getCustomerMedicalTotalCredits($customer_id, $user_spending_dates)
  {
	
	$total_bonus = 0;
	$temp_total_allocation = 0;
	$temp_total_deduction = 0;
	$company_credits = DB::table('customer_credits')->where('customer_id', $customer_id)->first();
	// admin_added_bonus_credits
	if($user_spending_dates['id']) {
		$temp_total_allocation = DB::table('customer_credit_logs')
		->where('customer_credits_id', $company_credits->customer_credits_id)
		->where('logs', 'admin_added_credits')
		->where('customer_credit_logs_id', '>=', $user_spending_dates['id'])
		->where('created_at', '>=', $user_spending_dates['start'])
		->where('created_at', '<=', $user_spending_dates['end'])
		->sum('credit');

		$temp_total_allocation = DB::table('customer_credit_logs')
		->where('customer_credits_id', $company_credits->customer_credits_id)
		->where('logs', 'admin_added_credits')
		->where('customer_credit_logs_id', '>=', $user_spending_dates['id'])
		->where('created_at', '>=', $user_spending_dates['start'])
		->where('created_at', '<=', $user_spending_dates['end'])
		->sum('credit');

		$total_bonus = DB::table('customer_credit_logs')
		->where('customer_credits_id', $company_credits->customer_credits_id)
		->where('logs', 'admin_added_bonus_credits')
		->where('customer_credit_logs_id', '>=', $user_spending_dates['id'])
		->where('created_at', '>=', $user_spending_dates['start'])
		->where('created_at', '<=', $user_spending_dates['end'])
		->sum('credit');
	} else {
		$temp_total_allocation = DB::table('customer_credit_logs')
		->where('customer_credits_id', $company_credits->customer_credits_id)
		->where('logs', 'admin_added_credits')
		->where('created_at', '>=', $user_spending_dates['start'])
		->where('created_at', '<=', $user_spending_dates['end'])
		->sum('credit');

		$total_bonus = DB::table('customer_credit_logs')
		->where('customer_credits_id', $company_credits->customer_credits_id)
		->where('logs', 'admin_added_bonus_credits')
		->where('created_at', '>=', $user_spending_dates['start'])
		->where('created_at', '<=', $user_spending_dates['end'])
		->sum('credit');

		$temp_total_deduction = DB::table('customer_credit_logs')
		->where('customer_credits_id', $company_credits->customer_credits_id)
		->where('logs', 'admin_deducted_credits')
		->where('created_at', '>=', $user_spending_dates['start'])
		->where('created_at', '<=', $user_spending_dates['end'])
		->sum('credit');
	}

	$total_medical_allocation = $temp_total_allocation - $temp_total_deduction;
	return ['total_purchase_credits' => $total_medical_allocation, 'total_bonus_credits' => (float)$total_bonus];
  }

  public static function getCustomerWellnessTotalCredits($customer_id, $user_spending_dates)
  {
	
	$total_bonus = 0;
	$temp_total_allocation = 0;
	$temp_total_deduction = 0;
	$company_credits = DB::table('customer_credits')->where('customer_id', $customer_id)->first();
	// admin_added_bonus_credits
	if($user_spending_dates['id']) {
		$temp_total_allocation = DB::table('customer_wellness_credits_logs')
		->where('customer_credits_id', $company_credits->customer_credits_id)
		->where('logs', 'admin_added_credits')
		->where('customer_wellness_credits_history_id', '>=', $user_spending_dates['id'])
		->where('created_at', '>=', $user_spending_dates['start'])
		->where('created_at', '<=', $user_spending_dates['end'])
		->sum('credit');

		$temp_total_allocation = DB::table('customer_wellness_credits_logs')
		->where('customer_credits_id', $company_credits->customer_credits_id)
		->where('logs', 'admin_added_credits')
		->where('customer_wellness_credits_history_id', '>=', $user_spending_dates['id'])
		->where('created_at', '>=', $user_spending_dates['start'])
		->where('created_at', '<=', $user_spending_dates['end'])
		->sum('credit');

		$total_bonus = DB::table('customer_wellness_credits_logs')
		->where('customer_credits_id', $company_credits->customer_credits_id)
		->where('logs', 'admin_added_bonus_credits')
		->where('customer_wellness_credits_history_id', '>=', $user_spending_dates['id'])
		->where('created_at', '>=', $user_spending_dates['start'])
		->where('created_at', '<=', $user_spending_dates['end'])
		->sum('credit');
	} else {
		$temp_total_allocation = DB::table('customer_wellness_credits_logs')
		->where('customer_credits_id', $company_credits->customer_credits_id)
		->where('logs', 'admin_added_credits')
		->where('created_at', '>=', $user_spending_dates['start'])
		->where('created_at', '<=', $user_spending_dates['end'])
		->sum('credit');

		$total_bonus = DB::table('customer_wellness_credits_logs')
		->where('customer_credits_id', $company_credits->customer_credits_id)
		->where('logs', 'admin_added_bonus_credits')
		->where('created_at', '>=', $user_spending_dates['start'])
		->where('created_at', '<=', $user_spending_dates['end'])
		->sum('credit');

		$temp_total_deduction = DB::table('customer_wellness_credits_logs')
		->where('customer_credits_id', $company_credits->customer_credits_id)
		->where('logs', 'admin_deducted_credits')
		->where('created_at', '>=', $user_spending_dates['start'])
		->where('created_at', '<=', $user_spending_dates['end'])
		->sum('credit');
	}

	$total_medical_allocation = $temp_total_allocation - $temp_total_deduction;
	return ['total_purchase_credits' => $total_medical_allocation, 'total_bonus_credits' => (float)$total_bonus];
  }

  public static function getMemberLastGroupNumber($customer_id)
  {
	$link_account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();

	$member = DB::table('corporate_members')
				->where('corporate_id', $link_account->corporate_id)
				->orderBy('user_id', 'desc')
				->first();
	if($member) {
		$group_number = DB::table('user')
						->where('UserID', $member->user_id)
						->orderBy('group_number', 'desc')
						->first();

		if($group_number) {
			return $group_number->group_number + 1;
		}
	}
	return 1;
  }
  
	public static function addSupplementaryCredits($customer_id, $spending_type, $credits)
	{
		$spending = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();

		if($spending) {
			if($spending_type == "medical") {
				// update and increment medical wallet
				$total = $credits * $spending->medical_supplementary_credits;
				DB::table('customer_credits')->where('customer_id', $customer_id)->increment('medical_supp_credits', $total);
			}

			if($spending_type == "wellness")	{
				// update and increment medical wallet
				$total = $credits * $spending->wellness_supplementary_credits;
				DB::table('customer_credits')->where('customer_id', $customer_id)->increment('wellness_supp_credits', $total);
			}
		}
	}

	public static function getPlanDuration($customer_id, $plan_start)
	{
		$plan_coverage = \CustomerHelper::getCompanyPlanDates($customer_id);
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

	public static function calculateInvoicePlanPrice($default_price, $start, $end)
	{
		$diff = date_diff(new \DateTime(date('Y-m-d', strtotime($start))), new \DateTime(date('Y-m-d', strtotime('+1 day', strtotime($end)))));
		$days = $diff->format('%a');
		$total_days = date("z", mktime(0,0,0,12,31,date('Y'))) + 1;
		$remaining_days = $days;

		$cost_plan_and_days = ($remaining_days / $total_days);
		// return $remaining_days;
		return $cost_plan_and_days * $default_price;
	}

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

		return array('plan_start' => $plan->plan_start, 'plan_end' => $end_plan_date);
	}
	
	public static function getActiveMembers($customer_id)
	{
		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
		$members = DB::table('corporate_members')
						->join('user', 'user.UserID', '=', 'corporate_members.user_id')
						->where('corporate_members.corporate_id', $account->corporate_id)
						->where('corporate_members.removed_status', 0)
						->get();
		return $members;
	}
	
	public static function checkCustomerEnterprisePayment($customer_id)
	{
		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$customer_active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();

		if($customer_active_plan->account_type == "enterprise_plan" && $customer_active_plan->paid == "false")	{
			return false;
		} else {
			return true;
		}
	}
}
?>