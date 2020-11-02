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

	public static function getMemberResetCreditWallet($user_id, $wallet_id, $filter)
	{

		if($filter == "current_term") {

		} else {
			$reset = DB::table('credit_reset')
	                ->where('id', $user_id)
	                ->where('spending_type', $spending_type)
	                ->where('user_type', 'employee')
	                ->get();
	    
	    $first_wallet_history = DB::table($wallet_table_logs)->where('wallet_id', $wallet_id)->first();
	    $allocation_date = date('Y-m-d', strtotime($wallet->created_at));
	    $temp_start_date = $allocation_date;
	    $start_date = null;
	    $end_date = null;
	    if(sizeof($reset) > 0) {
	      for( $i = 0; $i < sizeof( $reset ); $i++ ){
	        $temp_end_date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $reset[$i]->date_resetted ) ) ));
	        $temp_end_date = PlanHelper::endDate($temp_end_date);
	        if( strtotime( $temp_start_date ) < strtotime($date) && strtotime($date) < strtotime( $temp_end_date ) ){
	          $start_date = $temp_start_date;
	          $end_date = $temp_end_date;
	        }
	        $temp_start_date = $reset[$i]->date_resetted;
	        $back_date = true;
	        if( $i == (sizeof( $reset )-1) ){
	          if( $start_date == null && $end_date == null ){
	            $back_date = false;
	            $start_date = $temp_start_date;
	            $end_date = PlanHelper::endDate(date('Y-m-d',(strtotime ( '+1 day' , strtotime( date('Y-m-d') )))));
	          }
	        }
	      }
	    } else {
	      $last_wallet_history = DB::table($wallet_table_logs)->where('wallet_id', $wallet_id)->orderBy('created_at', 'desc')->first();
	      $start_date = $allocation_date;
	      $end_date = PlanHelper::endDate($last_wallet_history->created_at);
	    }
		}

	}

	public static function getMemberCreditReset($member_id, $term, $spending_type)
	{
		$today = date('Y-m-d H:i:s');
		if($term == "current_term") {
			$credit_resets = DB::table('credit_reset')
												->where('id', $member_id)->where('user_type', 'employee')
												->where('spending_type', $spending_type)
												->orderBy('created_at', 'desc')
												->first();
			if($credit_resets) {
				return ['start' => $credit_resets->date_resetted, 'end' => $today, 'id' => $credit_resets->wallet_history_id];
			} else {
				$customer_id = PlanHelper::getCustomerId($member_id);
				$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
				$entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $member_id)->orderBy('created_at', 'desc')->first();
				$wallet = DB::table('e_wallet')->where('UserID', $member_id)->first();
				if(!$entitlement) {
					PlanHelper::createMemberEntitlement($member_id);
					// $entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $member_id)->orderBy('created_at', 'desc')->first();
				}
				$first_plan = PlanHelper::getUserFirstPlanByCreatedAt($member_id);
				return ['start' => date('Y-m-d', strtotime($first_plan)), 'end' => PlanHelper::endDate($spending_accounts->medical_spending_end_date), 'id' => null];
			}
		} else {
			$credit_resets = DB::table('credit_reset')
												->where('id', $member_id)->where('user_type', 'employee')
												->where('spending_type', $spending_type)
												->get();

			if(sizeof($credit_resets) > 1) {
				$credit_reset_start = DB::table('credit_reset')
												->where('id', $member_id)->where('user_type', 'employee')
												->where('spending_type', $spending_type)
												->orderBy('created_at', 'desc')
												->skip(1)
												->take(1)
												->first();
				if($credit_reset_start) {
					$credit_reset_end = DB::table('credit_reset')
													->where('id', $member_id)->where('user_type', 'employee')
													->where('spending_type', $spending_type)
													->orderBy('created_at', 'desc')
													->first();
					if($credit_reset_end) {
						return ['start' => $credit_reset_start->date_resetted, 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($credit_reset_end->date_resetted)))), 'id' => $credit_reset_end->wallet_history_id];
					} else {
						$wallet = DB::table('e_wallet')->where('UserID', $member_id)->first();
						$wallet_history = DB::table('wallet_history')->where('wallet_id', $wallet->wallet_id)->orderBy('created_at', 'desc')->first();
						return ['start' => $credit_reset_start->date_resetted, 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($wallet_history->created_at)))), 'id' => $credit_reset_start->wallet_history_id];
					}
				} else {
					$wallet = DB::table('e_wallet')->where('UserID', $member_id)->first();
					return ['start' => date('Y-m-d', strtotime($wallet->created_at)), 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($credit_resets[0]->date_resetted)))), 'id' => $credit_resets[0]->wallet_history_id];
				}
			} else if(sizeof($credit_resets) == 1){
				// $wallet = DB::table('e_wallet')->where('UserID', $member_id)->first();
				// $first_plan = PlanHelper::getUserFirstPlanStart($member_id);
				$customer_id = PlanHelper::getCustomerId($member_id);
				$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $customer_id)->first();
				return ['start' => date('Y-m-d', strtotime($spending_accounts->medical_spending_start_date)), 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($credit_resets[0]->date_resetted)))), 'id' => $credit_resets[0]->wallet_history_id];
			} else {
				$customer_id = PlanHelper::getCustomerId($member_id);
				// $spending_accounts = DB::table('spending_account_settings')->where('customer_id', $customer_id)->get();
				// if(sizeof($spending_accounts) > 1) {
				// 	$spending_accounts = DB::table('spending_account_settings')
				// 								->where('customer_id', $customer_id)
				// 								->orderBy('created_at', 'desc')
				// 								->skip(1)
				// 								->take(1)
				// 								->first();
				// 	if(!$spending_accounts) {
				// 		$spending_accounts = $spending_accounts[0];
				// 	}
				// } else {
				// 	$spending_accounts = $spending_accounts[0];
				// }
				// return ['start' => date('Y-m-d', strtotime($spending_accounts->medical_spending_start_date)), 'end' => PlanHelper::endDate(date('Y-m-d', strtotime('-1 day', strtotime($spending_accounts->medical_spending_end_date)))), 'id' => null];
				return false;
			}
		}
	}

	public static function getMemberSpendingDateTerms($member_id, $term, $spending_type)
	{
		$customer_id = PlanHelper::getCustomerId($member_id);
		if($term == "current_term") {
			$member_wallet = DB::table('employee_wallet_entitlement')->where('member_id', $member_id)->orderBy('created_at', 'desc')->first();
			$spending_account = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
			if($spending_type == "medical") {
				return ['start' => $member_wallet->medical_usage_date, 'end' => PlanHelper::endDate($spending_account->medical_spending_end_date)];
			} else {
				return ['start' => $member_wallet->wellness_usage_date, 'end' => PlanHelper::endDate($spending_account->wellness_spending_end_date)];
			}
		} else {
			// $member_wallets = DB::table('employee_wallet_entitlement')->where('member_id', $member_id)->get();

			// if(sizeof($member_wallets) > 1) {
				// $member_wallet = DB::table('employee_wallet_entitlement')->where('member_id', $member_id)->orderBy('employee_wallet_entitlement_id', 'desc')->skip(1)->take(1)->first();

				// if($member_wallet) {
					$spending_accounts = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('spending_account_setting_id', 'desc')->get();

					if(sizeof($spending_accounts) > 1) {
						$spending_account = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('spending_account_setting_id', 'desc')->skip(1)->take(1)->first();

						if($spending_account) {
							if($spending_type == "medical") {
								return ['start' => $spending_account->medical_spending_start_date, 'end' => PlanHelper::endDate($spending_account->medical_spending_end_date)];
							} else {
								return ['start' => $spending_account->wellness_spending_start_date, 'end' => PlanHelper::endDate($spending_account->wellness_spending_end_date)];
							}
						} else {
							return false;
						}
					} else if(sizeof($spending_accounts) == 1) {
						if($spending_type == "medical") {
							return ['start' => $spending_accounts[0]->medical_spending_start_date, 'end' => PlanHelper::endDate($spending_accounts[0]->medical_spending_end_date)];
						} else {
							return ['start' => $spending_accounts[0]->wellness_spending_start_date, 'end' => PlanHelper::endDate($spending_accounts[0]->wellness_spending_end_date)];
						}
					} else {
						return false;
					}

					
					
				// } else {
				// 	return false;
				// }
			// } else if(sizeof($member_wallets) == 1){

			// }

			
			
		}
	}

	public static function activateNewEntitlement($member_id, $id)
	{
		$customer_id = PlanHelper::getCustomerId($member_id);
		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$customer_active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
		$member_entitlment = DB::table('wallet_entitlement_schedule')
                            ->where('wallet_entitlement_schedule_id', $id)
                            ->where('status', 0)
                            ->get();
		$spending = CustomerHelper::getAccountSpendingStatus($customer_id);
		$wallet_entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $member_id)->orderBy('created_at', 'desc')->first();
		$data = [];
		$data['member_id'] = $member_id;
		$spending_type = null;
		$entitlement_id = null;
		$medical_spending_method = $spending['medical_payment_method_panel'] != "mednefits_credits" ? 'post_paid' : 'pre_paid';
		$wellness_spending_method = $spending['wellness_payment_method_non_panel'] != "mednefits_credits" ? 'post_paid' : 'pre_paid';

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

			$data['spending_method'] = $entitlement->spending_method;
		}

		if($data) {
			$total_balance_remaining = 0;
			$customer_wallet = DB::table('customer_credits')->where('customer_id', $customer_id)->first();
			// get membder credits allocation
			$wallet = DB::table('e_wallet')->where('UserID', $member_id)->orderBy('created_at', 'desc')->first();

			if($spending_type == "medical") {
				$medical_credit_data = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $member_id);
				if($data['medical_allocation'] > $medical_credit_data['allocation']) {
					$new_medical_allocation = $data['medical_allocation'] - $medical_credit_data['allocation'];
					$type_allocation_medical = "added_by_hr";
				} else {
					if($data['medical_allocation'] == 0) {
						$new_medical_allocation = $medical_credit_data['allocation'];
					} else {
						$new_medical_allocation = $medical_credit_data['allocation'] - $data['medical_allocation'];
					}
					
					$type_allocation_medical = "deducted_by_hr";
				}

				$last_customer_active_plan_id_medical = DB::table('wallet_history')
															->where('wallet_id', $wallet->wallet_id)
															->where('logs', 'added_by_hr')
															->orderBy('created_at', 'desc')
															->first();
				if(!$last_customer_active_plan_id_medical) {
					$last_customer_active_plan_id_medical = $customer_active_plan->customer_active_plan_id;
				} else {
					$last_customer_active_plan_id_medical = $last_customer_active_plan_id_medical->customer_active_plan_id;
				}

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
						'spending_method'		=> $medical_spending_method,
						'created_at'			=> date('Y-m-d H:i:s'),
						'updated_at'			=> date('Y-m-d H:i:s')
					);
					DB::table('wallet_history')->insert($employee_credit_logs);
					if($spending['medical_method'] == "post_paid" && $spending['with_mednefits_credits'] == false || $spending['medical_method'] == "pre_paid" && $spending['with_mednefits_credits'] == false) {
						$employee_credit_logs['logs'] = 'added_by_hr_supplementary';
						DB::table('wallet_history')->insert($employee_credit_logs);

						// add customer allocation credits
						$customer_credits_logs = array(
							'customer_credits_id'	=> $customer_wallet->customer_credits_id,
							'credit'				=> $new_medical_allocation,
							'logs'					=> 'admin_added_credits',
							'running_balance'		=> 0,
							'customer_active_plan_id' => $last_customer_active_plan_id_medical,
							'currency_type'	=> $customer_wallet->currency_type,
							'spending_method'		=> $medical_spending_method,
							'created_at'			=> date('Y-m-d H:i:s'),
							'updated_at'			=> date('Y-m-d H:i:s')
						);

						DB::table('customer_credit_logs')->insert($company_credit_logs);
					}
					
					if($spending['medical_method'] != "post_paid" && $spending['with_mednefits_credits'] == true || $spending['medical_method'] != "pre_paid" && $spending['with_mednefits_credits'] == true) {
						$company_credits_result = DB::table('customer_credits')->where('customer_id', $customer_id)->decrement('balance', $new_medical_allocation);
					}
					
					$company_credit_logs = array(
						'customer_credits_id' => $customer_wallet->customer_credits_id,
						'credit'							=> $new_medical_allocation,
						'logs'								=> 'added_employee_credits',
						'user_id'							=> $member_id,
						'running_balance'			=> $customer_wallet->balance - $new_medical_allocation,
						'customer_active_plan_id' => $last_customer_active_plan_id_medical,
						'currency_type'	=> $customer_wallet->currency_type,
						'spending_method'		=> $medical_spending_method,
						'created_at'			=> date('Y-m-d H:i:s'),
						'updated_at'			=> date('Y-m-d H:i:s')
					);

					DB::table('customer_credit_logs')->insert($company_credit_logs);
					// credit wallet activity
					$creditWalletActivityData = array(
						'mednefits_credits_id'	=> $spending['mednefits_credits_id'],
						'customer_id'			=> $customer_id,
						'credit'				=> $new_medical_allocation,
						'type'					=> "added_employee_entitlement",
						'spending_type'			=> "medical",
						'currency_type'			=> $customer_wallet->currency_type,
						'created_at'			=> date('Y-m-d H:i:s'),
						'updated_at'			=> date('Y-m-d H:i:s')
					);

					DB::table('credit_wallet_activity')->insert($creditWalletActivityData);

					if($spending['with_mednefits_credits'] == true && $medical_spending_method == "pre_paid") {
						// create medical prepaid credits logs
						$medicalCreditsHistory = array(
							'mednefits_credits_id'	=> $spending['mednefits_credits_id'],
							'customer_id'			=> $customer_id,
							'credits'				=> $new_medical_allocation,
							'member_id'				=> $member_id,
							'credit_type'			=> 'added_employee_credits',
							'top_up_status'			=> 0,
							'currency_type'			=> $customer_wallet->currency_type,
							'created_at'			=> date('Y-m-d H:i:s'),
							'updated_at'			=> date('Y-m-d H:i:s')
						);

						DB::table('medical_credits')->insert($medicalCreditsHistory);
					}

					if($spending['with_mednefits_credits'] == true) {
						// check if needs to create top up or update top up invoice
						$checkTopUp = \SpendingHelper::checkPendingTopUpInvoice($customer_id, $new_medical_allocation);

						if(!$checkTopUp) {
							$toTopUp = array(
								'customer_id' 	=> $customer_id,
								'credits'		=> $new_medical_allocation,
								'member_id'		=> $member_id,
								'created_at'	=> date('Y-m-d H:i:s'),
								'updated_at'	=> date('Y-m-d H:i:s'),
								'status'		=> 0
							);

							DB::table('top_up_credits')->insert($toTopUp);
						}
					}
				} else {
					$wallet_result = DB::table('e_wallet')->where('UserID', $member_id)->decrement('balance', $new_medical_allocation);
					$employee_credit_logs = array(
						'wallet_id'					=> $wallet->wallet_id,
						'credit'						=> $new_medical_allocation,
						'logs'							=> 'deducted_by_hr',
						'running_balance'		=> $wallet->balance - $new_medical_allocation,
						'customer_active_plan_id' => $last_customer_active_plan_id_medical,
						'currency_type'	=> $wallet->currency_type,
						'created_at'			=> date('Y-m-d H:i:s'),
						'updated_at'			=> date('Y-m-d H:i:s'),
						'spending_method'		=> $medical_spending_method,
						'created_at'			=> date('Y-m-d H:i:s'),
						'updated_at'			=> date('Y-m-d H:i:s')
					);
					DB::table('customer_credits')->where('customer_id', $customer_id)->increment('balance', $new_medical_allocation);
					DB::table('wallet_history')->insert($employee_credit_logs);

					// credit wallet activity
					$creditWalletActivityData = array(
						'mednefits_credits_id'	=> $spending['mednefits_credits_id'],
						'customer_id'			=> $customer_id,
						'credit'				=> $new_medical_allocation,
						'type'					=> "deducted_employee_entitlement",
						'spending_type'			=> "medical",
						'currency_type'			=> $customer_wallet->currency_type,
						'created_at'			=> date('Y-m-d H:i:s'),
						'updated_at'			=> date('Y-m-d H:i:s')
					);

					DB::table('credit_wallet_activity')->insert($creditWalletActivityData);
					if($spending['with_mednefits_credits'] == true && $medical_spending_method == "pre_paid") {
						// create medical prepaid credits logs
						$medicalCreditsHistory = array(
							'mednefits_credits_id'	=> $spending['mednefits_credits_id'],
							'customer_id'			=> $customer_id,
							'credits'				=> $new_medical_allocation,
							'member_id'				=> $member_id,
							'credit_type'			=> 'deducted_employee_credits',
							'top_up_status'			=> 0,
							'currency_type'			=> $customer_wallet->currency_type,
							'created_at'			=> date('Y-m-d H:i:s'),
							'updated_at'			=> date('Y-m-d H:i:s')
						);

						DB::table('medical_credits')->insert($medicalCreditsHistory);
					}
				}

			} else if($spending_type == "wellness"){
				$wellness_credit_data = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $member_id);
				if($data['wellness_allocation'] > $wellness_credit_data['allocation']) {
						$new_wellness_allocation = $data['wellness_allocation'] - $wellness_credit_data['allocation'];
						$type_allocation_wellness = "added_by_hr";
					} else {
						if($data['wellness_allocation'] == 0)	{
							$new_wellness_allocation = $wellness_credit_data['allocation'];
						} else {
							$new_wellness_allocation = $wellness_credit_data['allocation'] - $data['wellness_allocation'];
						}
						
						$type_allocation_wellness = "deducted_by_hr";
					}

					$last_customer_active_plan_id_wellness = DB::table('wellness_wallet_history')
															->where('wallet_id', $wallet->wallet_id)
															->where('logs', 'added_by_hr')
															->orderBy('created_at', 'desc')
															->first();
					if(!$last_customer_active_plan_id_wellness) {
						$last_customer_active_plan_id_wellness = $customer_active_plan->customer_active_plan_id;
					} else {
						$last_customer_active_plan_id_wellness = $last_customer_active_plan_id_wellness->customer_active_plan_id;
					}
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
							'spending_method'		=> $wellness_spending_method,
							'created_at'			=> date('Y-m-d H:i:s'),
							'updated_at'			=> date('Y-m-d H:i:s')
						);
						DB::table('wellness_wallet_history')->insert($employee_credit_logs);
						if(($spending['wellness_method'] == "post_paid" && $spending['with_mednefits_credits'] == false) || ($spending['wellness_method'] == "pre_paid" && $spending['with_mednefits_credits'] == false)) {
							$employee_credit_logs['logs'] = 'added_by_hr_supplementary';
							DB::table('wellness_wallet_history')->insert($employee_credit_logs);
							// add company credits
							$customer_wellness_credits_logs = array(
								'customer_credits_id'	=> $customer_wallet->customer_credits_id,
								'credit'				=> $new_wellness_allocation,
								'logs'					=> 'admin_added_credits',
								'running_balance'		=> 0,
								'customer_active_plan_id' => $last_customer_active_plan_id_wellness,
								'currency_type'	=> $customer_wallet->currency_type,
								'spending_method'		=> $wellness_spending_method,
								'created_at'			=> date('Y-m-d H:i:s'),
								'updated_at'			=> date('Y-m-d H:i:s')
							);

							DB::table('customer_wellness_credits_logs')->insert($company_credit_logs);
						}

						if($spending['wellness_method'] != "post_paid" && $spending['with_mednefits_credits'] == true || $spending['wellness_method'] != "pre_paid" && $spending['with_mednefits_credits'] == true) {
							$company_credits_result = DB::table('customer_credits')->where('customer_id', $customer_id)->decrement('wellness_credits', $new_wellness_allocation);
						}
						
						$company_credit_logs = array(
							'customer_credits_id' => $customer_wallet->customer_credits_id,
							'credit'							=> $new_wellness_allocation,
							'logs'								=> 'added_employee_credits',
							'user_id'							=> $member_id,
							'running_balance'			=> $customer_wallet->wellness_credits - $new_wellness_allocation,
							'customer_active_plan_id' => $last_customer_active_plan_id_wellness,
							'currency_type'	=> $customer_wallet->currency_type,
							'spending_method'		=> $wellness_spending_method,
							'created_at'			=> date('Y-m-d H:i:s'),
							'updated_at'			=> date('Y-m-d H:i:s')
						);
						DB::table('customer_wellness_credits_logs')->insert($company_credit_logs);

						// credit wallet activity
						$creditWalletActivityData = array(
							'mednefits_credits_id'	=> $spending['mednefits_credits_id'],
							'customer_id'			=> $customer_id,
							'credit'				=> $new_wellness_allocation,
							'type'					=> "added_employee_entitlement",
							'spending_type'			=> "wellness",
							'currency_type'			=> $customer_wallet->currency_type,
							'created_at'			=> date('Y-m-d H:i:s'),
							'updated_at'			=> date('Y-m-d H:i:s')
						);

						DB::table('credit_wallet_activity')->insert($creditWalletActivityData);

						if($spending['with_mednefits_credits'] == true && $wellness_spending_method == "pre_paid") {
							// create prepaid credits logs
							$wellnessCreditsHistory = array(
								'mednefits_credits_id'	=> $spending['mednefits_credits_id'],
								'customer_id'			=> $customer_id,
								'credits'				=> $new_wellness_allocation,
								'member_id'				=> $member_id,
								'credit_type'			=> 'added_employee_credits',
								'top_up_status'			=> 0,
								'currency_type'			=> $customer_wallet->currency_type,
								'created_at'			=> date('Y-m-d H:i:s'),
								'updated_at'			=> date('Y-m-d H:i:s')
							);

							DB::table('wellness_credits')->insert($wellnessCreditsHistory);
						}
						if($spending['with_mednefits_credits'] == true) {
							// check if needs to create top up or update top up invoice
							$checkTopUp = \SpendingHelper::checkPendingTopUpInvoice($customer_id, $new_wellness_allocation);
	
							if(!$checkTopUp) {
								$toTopUp = array(
									'customer_id' 	=> $customer_id,
									'credits'		=> $new_wellness_allocation,
									'member_id'		=> $member_id,
									'created_at'	=> date('Y-m-d H:i:s'),
									'updated_at'	=> date('Y-m-d H:i:s'),
									'status'		=> 0
								);
	
								DB::table('top_up_credits')->insert($toTopUp);
							}
						}
					} else {
						$wallet_result = DB::table('e_wallet')->where('UserID', $member_id)->decrement('wellness_balance', $new_wellness_allocation);
						$employee_credit_logs = array(
							'wallet_id'					=> $wallet->wallet_id,
							'credit'						=> $new_wellness_allocation,
							'logs'							=> 'deducted_by_hr',
							'running_balance'		=> $wallet->wellness_balance - $new_wellness_allocation,
							'customer_active_plan_id' => $last_customer_active_plan_id_wellness,
							'currency_type'	=> $wallet->currency_type,
							'spending_method'		=> $wellness_spending_method,
							'created_at'			=> date('Y-m-d H:i:s'),
							'updated_at'			=> date('Y-m-d H:i:s')
						);
						DB::table('customer_credits')->where('customer_id', $customer_id)->increment('wellness_credits', $new_wellness_allocation);
						DB::table('wellness_wallet_history')->insert($employee_credit_logs);
						
						// credit wallet activity
						$creditWalletActivityData = array(
							'mednefits_credits_id'	=> $spending['mednefits_credits_id'],
							'customer_id'			=> $customer_id,
							'credit'				=> $new_wellness_allocation,
							'type'					=> "deducted_employee_entitlement",
							'spending_type'			=> "wellness",
							'currency_type'			=> $customer_wallet->currency_type,
							'created_at'			=> date('Y-m-d H:i:s'),
							'updated_at'			=> date('Y-m-d H:i:s')
						);

						DB::table('credit_wallet_activity')->insert($creditWalletActivityData);
						if($spending['with_mednefits_credits'] == true && $wellness_spending_method == "mednefits_credits") {
							// create prepaid credits logs
							$wellnessCreditsHistory = array(
								'mednefits_credits_id'	=> $spending['mednefits_credits_id'],
								'customer_id'			=> $customer_id,
								'credits'				=> $new_wellness_allocation,
								'member_id'				=> $member_id,
								'credit_type'			=> 'deducted_employee_credits',
								'top_up_status'			=> 0,
								'currency_type'			=> $customer_wallet->currency_type,
								'created_at'			=> date('Y-m-d H:i:s'),
								'updated_at'			=> date('Y-m-d H:i:s')
							);
							DB::table('wellness_credits')->insert($wellnessCreditsHistory);
						}
					}
			}

			// create entitlement
			$data['currency_type'] = $wallet->currency_type;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			$result = DB::table('employee_wallet_entitlement')->insert($data);
			if($result) {
				DB::table('wallet_entitlement_schedule')->where('wallet_entitlement_schedule_id', $entitlement_id)->update(['status' => 1]);
				$admin_logs = array(
					'admin_id'  => null,
					'type'      => 'system_create_activate_new_credit_allocation',
					'data'      => \SystemLogLibrary::serializeData($result)
				);
				\SystemLogLibrary::createAdminLog($admin_logs);
			}
			return $result;
		} else {
			return false;
		}
	}

	public static function activateNewEntitlementold2($member_id, $id)
	{
		$customer_id = PlanHelper::getCustomerId($member_id);
		$member_entitlment = DB::table('wallet_entitlement_schedule')
                            ->where('wallet_entitlement_schedule_id', $id)
                            ->where('status', 0)
                            ->get();
		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$customer_active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
		$wallet_entitlement = DB::table('employee_wallet_entitlement')->where('member_id', $member_id)->orderBy('created_at', 'desc')->first();
		$spending = CustomerHelper::getAccountSpendingStatus($customer_id);
		$medical_spending_method = $spending['medical_payment_method_panel'] != "mednefits_credits" ? 'post_paid' : 'pre_paid';
		$wellness_spending_method = $spending['wellness_payment_method_panel'] != "mednefits_credits" ? 'post_paid' : 'pre_paid';

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
			$customer_credit_logs = new CustomerCreditLogs( );
			$customer_wellness_credit_logs = new CustomerWellnessCreditLogs( );

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
					if(!$last_customer_active_plan_id_medical) {
						$last_customer_active_plan_id_medical = $customer_active_plan->customer_active_plan_id;
					} else {
						$last_customer_active_plan_id_medical = $last_customer_active_plan_id_medical->customer_active_plan_id;
					}

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
						if($spending['medical_payment_method_panel'] != "mednefits_credits") {
							$employee_credit_logs['logs'] = 'added_by_hr_supplementary';
							DB::table('wallet_history')->insert($employee_credit_logs);

							$company_credit_logs = array(
								'customer_credits_id' => $customer_wallet->customer_credits_id,
								'credit'							=> $new_medical_allocation,
								'logs'								=> 'admin_added_credits',
								'user_id'							=> $member_id,
								'running_balance'			=> $customer_wallet->balance + $new_medical_allocation,
								'customer_active_plan_id' => $last_customer_active_plan_id_medical,
								'currency_type'	=> $customer_wallet->currency_type,
								'created_at'			=> date('Y-m-d H:i:s'),
								'updated_at'			=> date('Y-m-d H:i:s'),
								'spending_method'		=> $medical_spending_method
							);
							DB::table('customer_credit_logs')->insert($company_credit_logs);

							$company_credits_result = DB::table('customer_credits')->where('customer_id', $customer_id)->decrement('medical_supp_credits', $new_medical_allocation);
						} else {
							$company_credits_result = DB::table('customer_credits')->where('customer_id', $customer_id)->decrement('balance', $new_medical_allocation);
						}
						
						$company_credit_logs = array(
							'customer_credits_id' => $customer_wallet->customer_credits_id,
							'credit'							=> $new_medical_allocation,
							'logs'								=> 'added_employee_credits',
							'user_id'							=> null,
							'running_balance'			=> $customer_wallet->balance - $new_medical_allocation,
							'customer_active_plan_id' => $last_customer_active_plan_id_medical,
							'currency_type'	=> $customer_wallet->currency_type,
							'created_at'			=> date('Y-m-d H:i:s'),
							'updated_at'			=> date('Y-m-d H:i:s'),
							'spending_method'		=> $medical_spending_method
						);
						DB::table('customer_credit_logs')->insert($company_credit_logs);
						// credit wallet activity
						$creditWalletActivityData = array(
							'mednefits_credits_id'	=> $customer_spending['mednefits_credits_id'],
							'customer_id'			=> $customer_id,
							'credit'				=> $credits,
							'type'					=> "added_employee_credits",
							'spending_type'			=> "wellness",
							'currency_type'			=> $customer->currency_type,
							'created_at'			=> date('Y-m-d H:i:s'),
							'updated_at'			=> date('Y-m-d H:i:s')
						);

						DB::table('credit_wallet_activity')->insert($creditWalletActivityData);
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
							'updated_at'			=> date('Y-m-d H:i:s'),
							'spending_method'		=> $medical_spending_method
						);
						DB::table('customer_credits')->where('customer_id', $customer_id)->increment('balance', $new_medical_allocation);
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
					if(!$last_customer_active_plan_id_wellness) {
						$last_customer_active_plan_id_wellness = $customer_active_plan->customer_active_plan_id;
					} else {
						$last_customer_active_plan_id_wellness = $last_customer_active_plan_id_wellness->customer_active_plan_id;
					}
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
						
						if($spending['wellness_method'] == "post_paid") {
							$employee_credit_logs['logs'] = 'added_by_hr_supplementary';
							DB::table('wellness_wallet_history')->insert($employee_credit_logs);

							$company_credit_logs = array(
								'customer_credits_id' => $customer_wallet->customer_credits_id,
								'credit'							=> $new_wellness_allocation,
								'logs'								=> 'admin_added_credits',
								'user_id'							=> null,
								'running_balance'			=> $customer_wallet->wellness_credits + $new_wellness_allocation,
								'customer_active_plan_id' => $last_customer_active_plan_id_wellness,
								'currency_type'	=> $customer_wallet->currency_type,
								'created_at'			=> date('Y-m-d H:i:s'),
								'updated_at'			=> date('Y-m-d H:i:s')
							);
							DB::table('customer_wellness_credits_logs')->insert($company_credit_logs);
							// $company_credits_result = DB::table('customer_credits')->where('customer_id', $customer_id)->decrement('wellness_supp_credits', $new_wellness_allocation);
						} else {
							$company_credits_result = DB::table('customer_credits')->where('customer_id', $customer_id)->decrement('wellness_credits', $new_wellness_allocation);
						}

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
						DB::table('customer_wellness_credits_logs')->insert($company_credit_logs);
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
						DB::table('customer_credits')->where('customer_id', $customer_id)->increment('wellness_credits', $new_wellness_allocation);
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

	public static function newActivateNewEntitlementold2($member_id, $id)
	{
		$customer_id = PlanHelper::getCustomerId($member_id);
		$member_entitlment = DB::table('wallet_entitlement_schedule')
                            ->where('wallet_entitlement_schedule_id', $id)
                            ->where('status', 0)
                            ->get();
		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$customer_active_plan = DB::table('customer_active_plan')->where('plan_id', $plan->customer_plan_id)->first();
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
			$customer_credit_logs = new CustomerCreditLogs( );
			$customer_wellness_credit_logs = new CustomerWellnessCreditLogs( );
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
					if(!$last_customer_active_plan_id_medical) {
						$last_customer_active_plan_id_medical = $customer_active_plan->customer_active_plan_id;
					} else {
						$last_customer_active_plan_id_medical = $last_customer_active_plan_id_medical->customer_active_plan_id;
					}

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
						$employee_credit_logs['logs'] = 'added_by_hr_supplementary';
						DB::table('wallet_history')->insert($employee_credit_logs);

						$customer_credits_logs = array(
							'customer_credits_id'	=> $customer_wallet->customer_credits_id,
							'credit'				=> $new_medical_allocation,
							'logs'					=> 'admin_added_credits',
							'running_balance'		=> $customer_wallet->balance + $new_medical_allocation,
							'customer_active_plan_id' => $last_customer_active_plan_id_medical,
							'currency_type'	=> $customer_wallet->currency_type
						);

						$customer_credit_logs->createCustomerCreditLogs($customer_credits_logs);
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
					if(!$last_customer_active_plan_id_wellness) {
						$last_customer_active_plan_id_wellness = $customer_active_plan->customer_active_plan_id;
					} else {
						$last_customer_active_plan_id_wellness = $last_customer_active_plan_id_wellness->customer_active_plan_id;
					}
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
						$employee_credit_logs['logs'] = 'added_by_hr_supplementary';
						DB::table('wellness_wallet_history')->insert($employee_credit_logs);

						$customer_wellness_credits_logs = array(
							'customer_credits_id'	=> $customer_wallet->customer_credits_id,
							'credit'				=> $input['credits'],
							'logs'					=> 'admin_added_credits',
							'running_balance'		=> $customer_wallet->wellness_credits + $input['credits'],
							'customer_active_plan_id' => $last_customer_active_plan_id_wellness,
							'currency_type'	=> $customer_wallet->currency_type
						);

						$customer_wellness_credit_logs->createCustomerWellnessCreditLogs($customer_wellness_credits_logs);
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
						DB::table('customer_wellness_credits_logs')->insert($company_credit_logs);
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

	public static function getMemberSpendingCoverageDate($member_id)
	{
		// $customer_id = PlanHelper::getCustomerId($member_id);
		// $spending_accounts = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();

		// return ['start_date' => $spending_accounts->medical_spending_start_date, 'end_date' => date('Y-m-d', strtotime('+3 months', strtotime($spending_accounts->medical_spending_end_date)))];
		$current_term = MemberHelper::getMemberCreditReset($member_id, 'current_term', 'medical');
		$last_term = MemberHelper::getMemberCreditReset($member_id, 'last_term', 'medical');
		$today = date('Y-m-d');
		$grace_period = null;
		if($last_term) {
			$grace_period = date('Y-m-d', strtotime('+3 months', strtotime($current_term['start'])));
			if($grace_period <= $today) {
				return ['start_date' => $current_term['start'], 'end_date' => $current_term['end'], 'today' => $today, 'grace_period' => $grace_period];
			} else {
				return ['start_date' => $last_term['start'], 'end_date' => $current_term['end'], 'today' => $today, 'grace_period' => $grace_period];
			}
		} else {
			return ['start_date' => $current_term['start'], 'end_date' => $current_term['end'], 'today' => $today, 'grace_period' => $grace_period];
		}
		// return ['current_term' => $current_term, 'last_term' => $last_term];
	}

	public static function getMemberTotalDaysSubscription($plan_start, $plan_end)	
	{
		$total_days = date_diff(new \DateTime(date('Y-m-d', strtotime($plan_start))), new \DateTime(date('Y-m-d', strtotime($plan_end))));
		return $total_days->format('%a') + 1;
	}

	public static function checkMemberAccessTransactionStatus($member_id, $type)
	{
		$status = DB::table('member_block_transaction')->where('member_id', $member_id)->where('status', 1)->first();

		if($status) {
			return true;
		} else {
			// get member account settings
			// check for spending transaction access
			$customer_id = \PlanHelper::getCustomerId($member_id);
			$spending = \CustomerHelper::getAccountSpendingStatus($customer_id);

			if($type == "non_panel") {
				if($spending['medical_non_panel_submission'] == false && $spending['wellness_non_panel_submission'] == false) {
					return true;
				}
			}

			// check of from top up and pending
			$top_up_user = DB::table('top_up_credits')->where('member_id', $member_id)->where('status', 0)->first();

			// check if account is active
			$accountStatus = self::getMemberWalletStatus($member_id, 'medical');

			if($accountStatus == "expired" || $accountStatus == "deactivated") {
				return true;
			}

			// check if plan is paid
			$plan_history = DB::table('user_plan_history')->where('user_id', $member_id)->where('type', 'started')->orderBy('created_at', 'desc')->first();
			$customer_active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $plan_history->customer_active_plan_id)->first();

			if($customer_active_plan && $customer_active_plan->account_type == "enterprise_plan")	{
				if($customer_active_plan->paid == "false") {
					return true;
				}
			}

			$accessTransaction = $type == "panel" ? \SpendingHelper::checkSpendingCreditsAccess($customer_id) : \SpendingHelper::checkSpendingCreditsAccessNonPanel($customer_id);
			
			if(!$top_up_user) {
				// check if first purchase is already paid
				$account_credits = DB::table('spending_purchase_invoice')
										->where('customer_id', $customer_id)
										->first();
				if($account_credits && (int)$account_credits->payment_status == 1) {
					return false;
				}
			}
			
			if(!$accessTransaction['enable']) {
				return true;
			}
			
			return false;
		}
	}

	public static function deductPlanHistoryVisit($member_id)
	{
		$user_type = PlanHelper::getUserAccountType($member_id);

		if($user_type == "employee") {
			$plan_history = DB::table('user_plan_history')->where('user_id', $member_id)->where('type', 'started')->orderBy('created_at', 'desc')->first();

			if($plan_history)
			{
				if($plan_history->total_visit_created < 14)	{
					// increase visit created
					DB::table('user_plan_history')->where('user_plan_history_id', $plan_history->user_plan_history_id)->increment('total_visit_created', 1);
				}
				return true;
			}
		} else {
			$plan_history = DB::table('dependent_plan_history')->where('user_id', $member_id)->where('type', 'started')->orderBy('created_at', 'desc')->first();

			if($plan_history)
			{
				if($plan_history->total_visit_created < 14)	{
					// increase visit created
					DB::table('dependent_plan_history')->where('dependent_plan_history_id', $plan_history->dependent_plan_history_id)->increment('total_visit_created', 1);
				}
				return true;
			}
		}
		
		return false;
	}

	public static function returnPlanHistoryVisit($member_id)
	{
		$user_type = PlanHelper::getUserAccountType($member_id);

		if($user_type == "employee") {
			$plan_history = DB::table('user_plan_history')->where('user_id', $member_id)->where('type', 'started')->orderBy('created_at', 'desc')->first();

			if($plan_history)
			{
				// increase visit created
				if($plan_history->total_visit_created > 0)	{
					DB::table('user_plan_history')->where('user_plan_history_id', $plan_history->user_plan_history_id)->decrement('total_visit_created', 1);
				}
				return true;
			}
		} else {
			$plan_history = DB::table('dependent_plan_history')->where('user_id', $member_id)->where('type', 'started')->orderBy('created_at', 'desc')->first();

			if($plan_history)
			{
				if($plan_history->total_visit_created > 0)	{
					// increase visit created
					DB::table('dependent_plan_history')->where('dependent_plan_history_id', $plan_history->dependent_plan_history_id)->decrement('total_visit_created', 1);
				}
				return true;
			}
		}

		return false;
	}

	public static function getMemberPreviousPlanHistory($member_id)
	{
		$plan_history = DB::table('user_plan_history')
								->where('user_id', $member_id)
								->where('type', 'started')
								->orderBy('created_at', 'desc')
								->skip(1)
								->take(1)
								->first();

		return $plan_history  ? $plan_history  : false;
	}

	public static function getDependentPreviousPlanHistory($member_id)
	{
		$plan_history = DB::table('dependent_plan_history')
								->where('user_id', $member_id)
								->where('type', 'started')
								->orderBy('created_at', 'desc')
								->skip(1)
								->take(1)
								->first();

		return $plan_history  ? $plan_history  : false;
	}

	public static function createMemberTransactionAccessBlock($member_id)
	{
		$plan_history = DB::table('user_plan_history')->where('user_id', $member_id)->where('type', 'started')->orderBy('created_at', 'desc')->first();
		$customer_active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $plan_history->customer_active_plan_id)->first();

		if($customer_active_plan && $customer_active_plan->account_type == "enterprise_plan")	{
			// create block transaction
			$customer_id = PlanHelper::getCustomerId($member_id);
			$payment_status = CustomerHelper::checkCustomerEnterprisePayment($customer_id);

			if($payment_status == false)	{
				$data = array(
					'member_id'		=> $member_id,
					'customer_id'	=> $customer_id,
					'status'		=> 1,
					'type'			=> 'all',
					'created_at'	=> date('Y-m-d H:i:s'),
					'updated_at'	=> date('Y-m-d H:i:s')
				);
				DB::table('member_block_transaction')->insert($data);
			}
		}
	}
	
	public static function memberReturnCreditBalance($member_id)
	{
		$wallet = DB::table('e_wallet')
		->where('UserID', $member_id)
		->orderBy('created_at', 'desc')
		->first();

		$medical = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $member_id);
		$wellness = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $member_id);

		if($medical['balance'] > 0) {
			// return credits to company
			$calibrate_medical_deduction_by_hr = array(
				'wallet_id'         => $wallet->wallet_id,
				'credit'            => $medical['balance'],
				'logs'              => 'deducted_by_hr',
				'running_balance'   => $medical['balance'],
				'spending_type'     => 'medical',
				'created_at'        => date('Y-m-d H:i:s'),
				'updated_at'        => date('Y-m-d H:i:s'),
				'currency_type'		=> $wallet->currency_type
			);

			DB::table('wallet_history')->insert($calibrate_medical_deduction_by_hr);
		}

		if($wellness['balance'] > 0) {
			// return credits to company
			$calibrate_wellness_deduction_by_hr = array(
				'wallet_id'         => $wallet->wallet_id,
				'credit'            => $wellness['balance'],
				'logs'              => 'deducted_by_hr',
				'running_balance'   => $wellness['balance'],
				'spending_type'     => 'wellness',
				'created_at'        => date('Y-m-d H:i:s'),
				'updated_at'        => date('Y-m-d H:i:s'),
				'currency_type'		=> $wallet->currency_type
			);
			DB::table('wellness_wallet_history')->insert($calibrate_wellness_deduction_by_hr);
		}

		return ['medical' => $medical, 'wellness' => $wellness];
	}

	public static function createWallet($member_id)
	{
		$wallet = DB::table('e_wallet')
		->where('UserID', $member_id)
		->orderBy('created_at', 'desc')
		->first();

		if(!$wallet) {
			$data = array(
				'UserID'	=> $member_id,
				'balance'	=> 0,
				'wellness_balance' => 0,
				'created_at'	=> date('Y-m-d H:i:s'),	
				'updated_at'	=> date('Y-m-d H:i:s'),	
			);
			DB::table('e_wallet')->insert($data);
		}
	}

	public static function getEmployeeSpendingAccountSummaryNew($input)
	{
		if(isset($input['calibrate_medical'])) {
			if($input['calibrate_medical'] == "true") {
				$input['calibrate_medical'] = true;
			} else if($input['calibrate_medical'] == "false"){
				$input['calibrate_medical'] = false;
			}
		}

		if(isset($input['calibrate_wellness'])) {
			if($input['calibrate_wellness'] == "true") {
				$input['calibrate_wellness'] = true;
			} else if($input['calibrate_wellness'] == "false"){
				$input['calibrate_wellness'] = false;
			}
		}
		
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['employee_id']) || $input['employee_id'] == null) {
			return array('status' => false, 'message' => 'Employee ID is required.');
		}

		$check_employee = DB::table('user')
		->where('UserID', $input['employee_id'])
		->where('UserType', 5)
		->first();

		if(!$check_employee) {
			return array('status' => false, 'message' => 'Employee does not exist.');
		}

		$coverage = PlanHelper::getEmployeePlanCoverageDate($input['employee_id'], $customer_id);
		$last_day_coverage = PlanHelper::endDate($input['last_date_of_coverage']);
		$ids = StringHelper::getSubAccountsID($check_employee->UserID);
		$spending = CustomerHelper::getAccountSpendingBasicPlanStatus($customer_id);

		$wallet = DB::table('e_wallet')
		->where('UserID', $check_employee->UserID)
		->orderBy('created_at', 'desc')
		->first();

		// get employee plan duration
		if(!empty($input['pro_allocation_start_date']) && !empty($input['pro_allocation_end_date'])) {
			$start = new DateTime($input['pro_allocation_start_date']);
			$end = new DateTime(date('Y-m-d', strtotime($input['pro_allocation_end_date'])));
			$diff = $start->diff($end);
			$coverage_end = new DateTime($input['pro_allocation_end_date']);
		} else {
			$end = new DateTime(date('Y-m-d', strtotime($coverage['plan_end'])));
			$start = new DateTime($coverage['plan_start']);
			$diff = $start->diff($end);
			$coverage_end = new DateTime($last_day_coverage);

		}

		$plan_employee_duration = new DateTime($coverage['plan_start']);
		$plan_employee_duration = $plan_employee_duration->diff(new DateTime(date('Y-m-d', strtotime($coverage['plan_end']))));
		$plan_duration = $plan_employee_duration->days + 1;
		// get empployee plan coverage from last day of employee
		$diff_coverage = $start->diff($coverage_end);
		$coverage_diff = $diff_coverage->days + 1;
		$employee_status = PlanHelper::getEmployeeStatus($input['employee_id']);
		// get total allocation of employee from plan start and plan end
		$employee_credit_reset_medical = DB::table('credit_reset')
		->where('id', $check_employee->UserID)
		->where('spending_type', 'medical')
		->where('user_type', 'employee')
		->orderBy('created_at', 'desc')
		->first();

		$user_plan_history = DB::table('user_plan_history')
		->where('user_id', $check_employee->UserID)
		->where('type', 'started')
		->orderBy('date', 'desc')
		->first();

		$customer_active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)->first();
		$check_wallet_status = DB::table('member_wallet_status')->where('member_id', $input['employee_id'])->first();

		if($employee_credit_reset_medical) {
			$minimum_date_medical = date('Y-m-d', strtotime($employee_credit_reset_medical->date_resetted));
		} else {
			$minimum_date_medical = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->min('created_at');
			if(!$minimum_date_medical) {
				$minimum_date_medical = $wallet->created_at;
			}
		}

		$plan_end_date = PlanHelper::endDate($coverage['plan_end']);
		$total_allocation_medical_temp = 0;
		$total_allocation_medical_deducted = 0;
		$total_allocation_medical_credits_back = 0;
		$total_medical_e_claim_spent = 0;
		$total_medical_in_network_spent = 0;

		$pending_e_claim_medical = DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('spending_type', 'medical')
		->where('status', 0)
		->sum('amount');

		$usage_date = date('d/m/Y');
		// get pro allocation medical
		$pro_allocation_medical_date = DB::table('wallet_history')
		->where('wallet_id', $wallet->wallet_id)
		->where('logs', 'pro_allocation')
		->orderBy('created_at', 'desc')
		->first();

		$pro_allocation_wellness_date = DB::table('wellness_wallet_history')
		->where('wallet_id', $wallet->wallet_id)
		->where('logs', 'pro_allocation')
		->orderBy('created_at', 'desc')
		->first();

		if($pro_allocation_medical_date) {
			$usage_date = date('d/m/Y', strtotime($pro_allocation_medical_date->created_at));
		} else {
			if($pro_allocation_wellness_date) {
				$usage_date = date('d/m/Y', strtotime($pro_allocation_wellness_date->created_at));
			}
		}

		$medical_credit_data = PlanHelper::memberMedicalAllocatedCreditsByDates($wallet->wallet_id, $check_employee->UserID, $minimum_date_medical, $plan_end_date);
		$total_allocation_medical = $medical_credit_data['allocation'];
		$total_medical_spent = $medical_credit_data['get_allocation_spent'];
		$has_medical_allocation = false;
		$pro_temp = $coverage_diff / $plan_duration;

		$total_current_usage = $total_medical_spent + $pending_e_claim_medical;
		if($pro_allocation_medical_date) {
			$total_pro_medical_allocation = $pro_allocation_medical_date->credit;
			$medical_balance = $total_pro_medical_allocation - $total_current_usage;
		} else {
			$total_pro_medical_allocation = $pro_temp * $total_allocation_medical;
			$medical_balance = $total_pro_medical_allocation - $total_current_usage;
		}
		
		
		if($total_allocation_medical > 0) {
			$has_medical_allocation = true;
		}

		$exceed = false;
		if($medical_balance < 0) {
			$exceed = true;
		}

		if($employee_status['status'] == false) {
			if($total_current_usage > $total_pro_medical_allocation) {
				$exceed = true;
			}
		} else {
			if($pro_allocation_medical_date) {
				$total_pro_medical_allocation = $pro_allocation_medical_date->credit;
				$medical_balance = $total_pro_medical_allocation - $total_current_usage;
			} else {
				$medical_balance = $total_allocation_medical - $total_current_usage;
			}

			if($medical_balance < 0) {
				$exceed = true;
			} else {
				$exceed = false;
			}
		}

		$remaining_allocated_medical_credits = 0;

		if($spending['medical_method'] == "pre_paid")	{
			$remaining_allocated_medical_credits = $total_allocation_medical - $total_pro_medical_allocation;
		}
		
		$medical = array(
			'status' => true,
			'initial_allocation' 	=> number_format($total_allocation_medical, 2),
			'pro_allocation'		=> number_format($total_pro_medical_allocation, 2),
			'current_usage'			=> number_format($total_current_usage, 2),
			'pending_e_claim'		=> number_format($pending_e_claim_medical, 2),
			'spent'					=> number_format($total_medical_spent, 2),
			'exceed'				=> $exceed,
			'exceeded_by'			=> number_format($total_medical_spent - $total_pro_medical_allocation, 2),
			'balance'				=> number_format($medical_balance, 2),
			'exceed_balance'				=> $exceed == true ? number_format(abs($medical_balance), 2) : "0.00",
			'remaining_allocated_credits'	=> number_format($remaining_allocated_medical_credits, 2),
			'credits_to_be_returned'	=> $exceed == true ? number_format($remaining_allocated_medical_credits - abs($medical_balance), 2) : "0.00",
			'currency_type'	=> $wallet->currency_type,
			'plan_method'			=> $spending['medical_method'],
			'pro_allocation_status'		=> false,
			'balance_credits_date'	=> date('d/m/Y'),
			'returned_balance_status'	=> false
		);

		if($check_wallet_status && (int)$check_wallet_status->medical_pro_allocation_status == 1) {
			$medical['initial_allocation'] = number_format($check_wallet_status->medical_initial_allocation, 2);
			$medical['pro_allocation'] = number_format($check_wallet_status->medical_pro_allocation, 2);
			$medical['pro_allocation_status'] = $check_wallet_status->medical_pro_allocation_status == 1 ? true : false;

			if($spending['medical_method'] == "pre_paid")	{
				$medical['remaining_allocated_credits'] = number_format($check_wallet_status->medical_initial_allocation - $check_wallet_status->medical_pro_allocation, 2);
				$medical['remaining_credits_date'] = date('d/m/Y', strtotime($check_wallet_status->medical_return_credits_date));
				$medical['returned_credit_status'] = false;
				if(date('Y-m-d', strtotime($check_wallet_status->medical_return_credits_date)) < date('Y-m-d')) {
					$medical['balance_credits_date'] = date('d/m/Y', strtotime("+1 day", strtotime($check_wallet_status->medical_return_credits_date)));
				} else {
					$medical['balance_credits_date'] = date('d/m/Y');
					// get lates balance
					$medical_balance = $check_wallet_status->medical_pro_allocation - $total_current_usage;
				}

				if($medical['exceed'] == true)	{
					$medical['credits_to_be_returned'] = number_format(($check_wallet_status->medical_initial_allocation - $check_wallet_status->medical_pro_allocation) - abs($medical_balance), 2);
				}

				if($employee_status['status'] == true) {
					$return_date = date('Y-m-d', strtotime($employee_status['expiry_date']));
					if(date('Y-m-d') >= $return_date) {
						$medical['returned_credit_status'] = true;
						$medical['balance_credits_date'] = date('d/m/Y', strtotime($return_date));
					}

					$return_balance_date = date('Y-m-d', strtotime('+1 day', strtotime($employee_status['expiry_date'])));
					if(date('Y-m-d') >= $return_balance_date) {
						$medical['balance_credits_date'] = date('d/m/Y', strtotime($return_balance_date));
						$medical['returned_balance_status'] = true;
					} else {
						$medical['balance_credits_date'] = date('d/m/Y');
					}
				}
			} else {
				if($medical['exceed'] == true)	{
					$medical['credits_to_be_returned'] = number_format(($check_wallet_status->medical_initial_allocation - $check_wallet_status->medical_pro_allocation) - abs($medical_balance), 2);
				}
				if($employee_status['status'] == true) {
					$return_balance_date = date('Y-m-d', strtotime('+1 day', strtotime($employee_status['expiry_date'])));
					if(date('Y-m-d') >= $return_balance_date) {
						$medical['balance_credits_date'] = date('d/m/Y', strtotime($return_balance_date));
						$medical['returned_balance_status'] = true;
					} else {
						$medical['balance_credits_date'] = date('d/m/Y');
					}
				}
			}
		} else {
			if($employee_status['status'] == true) {
				$med_allocate = DB::table('employee_wallet_entitlement')->where('member_id', $input['employee_id'])->orderBy('created_at', 'desc')->first();
				$return_balance_date = date('Y-m-d', strtotime('+1 day', strtotime($employee_status['expiry_date'])));
				$medical['initial_allocation'] = number_format($med_allocate->medical_entitlement, 2);
				
				if(date('Y-m-d') >= $return_balance_date) {
					$medical['balance_credits_date'] = date('d/m/Y', strtotime($return_balance_date));
					$medical['returned_balance_status'] = true;
					if($medical_balance < 0) {
						$medical['balance'] = "0.00";
					}
				} else {
					$medical['balance_credits_date'] = date('d/m/Y');
				}
				$medical['balance'] = number_format($med_allocate->medical_entitlement - $total_current_usage, 2);
				$medical_balance = $med_allocate->medical_entitlement - $total_current_usage;

				if($medical_balance < 0) {
					$medical['exceed'] = true;
				} else {
					$medical['exceed'] = false;
				}
			}
		}

		$employee_credit_reset_wellness = DB::table('credit_reset')
		->where('id', $check_employee->UserID)
		->where('spending_type', 'wellness')
		->where('user_type', 'employee')
		->orderBy('created_at', 'desc')
		->first();

		if($employee_credit_reset_wellness) {
			$minimum_date_wellness = date('Y-m-d', strtotime($employee_credit_reset_wellness->date_resetted));
		} else {
			$minimum_date_wellness = DB::table('wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->min('created_at');
			if(!$minimum_date_wellness) {
				$minimum_date_wellness = $wallet->created_at;
			}
		}

		$total_allocation_wellness_temp = 0;
		$total_allocation_wellness_deducted = 0;
		$total_allocation_wellness_credits_back = 0;
		$total_wellness_e_claim_spent_wellness = 0;
		$total_wellness_in_network_spent = 0;
		$has_wellness_allocation = false;

		$pending_e_claim_wellness = DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('spending_type', 'wellness')
		->where('status', 0)
		->sum('amount');

		$wellness_credit_data = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $check_employee->UserID, $minimum_date_wellness, $plan_end_date);
		$total_allocation_wellness = $wellness_credit_data['allocation'];
		$total_wellness_in_network_spent = $wellness_credit_data['in_network_spent'];
		$total_wellness_e_claim_spent_wellness = $wellness_credit_data['e_claim_spent'];
		$total_wellness_spent = $wellness_credit_data['get_allocation_spent'];
		$exceed_wellness = false;
		$total_current_usage_wellness = $total_wellness_in_network_spent + $total_wellness_e_claim_spent_wellness + $pending_e_claim_wellness;
		
		if($pro_allocation_wellness_date) {
			$total_pro_wellness_allocation = $pro_allocation_wellness_date->credit;
			$wellness_balance = $total_pro_wellness_allocation - $total_current_usage_wellness;
		} else {
			$total_pro_wellness_allocation = $pro_temp * $total_allocation_wellness;
			$wellness_balance = $total_pro_wellness_allocation - $total_current_usage_wellness;
		}
		
		if($wellness_balance < 0) {
			$exceed_wellness = true;
		}

		if($employee_status['status'] == false) {
			if($total_current_usage_wellness > $total_pro_wellness_allocation) {
				$exceed_wellness = true;
			}	
		} else {
			if($pro_allocation_wellness_date) {
				$total_pro_wellness_allocation = $pro_allocation_wellness_date->credit;
				$wellness_balance = $total_pro_wellness_allocation - $total_current_usage_wellness;
			} else {
				$wellness_balance = $total_allocation_wellness - $total_current_usage_wellness;
				if($wellness_balance < 0) {
					$exceed_wellness = true;
				} else {
					$exceed_wellness = false;
				}
			}
		}
		
		if($total_allocation_wellness > 0) {
			$has_wellness_allocation = true;
		}
		
		$remaining_allocated_wellness_credits = 0;
		if($spending['wellness_method'] == "pre_paid")	{
			$remaining_allocated_wellness_credits = $total_allocation_wellness - $total_pro_wellness_allocation;
		}

		$wellness = array(
			'status' => true,
			'initial_allocation' 	=> number_format($total_allocation_wellness, 2),
			'pro_allocation'		=> number_format($total_pro_wellness_allocation, 2),
			'current_usage'			=> number_format($total_current_usage_wellness, 2),
			'spent'					=> number_format($total_wellness_spent, 2),
			'pending_e_claim'		=> number_format($pending_e_claim_wellness, 2),
			'exceed'				=> $exceed_wellness,
			'exceeded_by'			=> number_format($total_wellness_spent - $total_pro_wellness_allocation, 2),
			'balance'				=> number_format($wellness_balance, 2),
			'exceed_balance'		=> $exceed == true ? number_format(abs($wellness_balance), 2) : "0.00",
			'remaining_allocated_credits' => number_format($remaining_allocated_wellness_credits, 2),
			'credits_to_be_returned'	=> $exceed == true ? number_format($remaining_allocated_wellness_credits - abs($wellness_balance), 2) : "0.00",
			'currency_type'	=> $wallet->currency_type,
			'plan_method'			=> $spending['wellness_method'],
			'pro_allocation_status'		=> false,
			'balance_credits_date'	=> date('d/m/Y')
		);

		if($check_wallet_status && (int)$check_wallet_status->wellness_pro_allocation_status == 1) {
			$wellness['initial_allocation'] = number_format($check_wallet_status->wellness_initial_allocation, 2);
			$wellness['pro_allocation'] = number_format($check_wallet_status->wellness_pro_allocation, 2);
			$wellness['pro_allocation_status'] = $check_wallet_status->wellness_pro_allocation_status == 1 ? true : false;
			$wellness['returned_credit_status'] = false;
			if($spending['wellness_method'] == "pre_paid")	{
				$wellness['remaining_allocated_credits'] = number_format($check_wallet_status->wellness_initial_allocation - $check_wallet_status->wellness_pro_allocation, 2);
				$wellness['remaining_credits_date'] = date('d/m/Y', strtotime($check_wallet_status->wellness_return_credits_date));
				
				if(date('Y-m-d', strtotime($check_wallet_status->wellness_return_credits_date)) < date('Y-m-d')) {
					$wellness['balance_credits_date'] = date('d/m/Y', strtotime("+1 day", strtotime($check_wallet_status->wellness_return_credits_date)));
				} else {
					$wellness['balance_credits_date'] = date('d/m/Y');
					// get lates balance
					$wellness_balance = $check_wallet_status->wellness_pro_allocation - $total_wellness_spent;
				}

				if($wellness['exceed'] == true)	{
					$wellness['credits_to_be_returned'] = number_format(($check_wallet_status->wellness_initial_allocation - $check_wallet_status->wellness_pro_allocation) - abs($wellness_balance), 2);
				}

				if($employee_status['status'] == true) {
					$return_date = date('Y-m-d', strtotime($employee_status['expiry_date']));
					if(date('Y-m-d') >= $return_date) {
						$wellness['returned_credit_status'] = true;
						$wellness['balance_credits_date'] = date('d/m/Y', strtotime($return_date));
					}

					$return_balance_date = date('Y-m-d', strtotime('+1 day', strtotime($employee_status['expiry_date'])));
					if(date('Y-m-d') >= $return_balance_date) {
						$wellness['balance_credits_date'] = date('d/m/Y', strtotime($return_balance_date));
						$wellness['returned_balance_status'] = true;
					} else {
						$wellness['balance_credits_date'] = date('d/m/Y');
					}
				}
			} else {
				if($wellness['exceed'] == true)	{
					$wellness['credits_to_be_returned'] = number_format(($check_wallet_status->wellness_initial_allocation - $check_wallet_status->wellness_pro_allocation) - abs($wellness_balance), 2);
				}
				if($employee_status['status'] == true) {
					$return_balance_date = date('Y-m-d', strtotime('+1 day', strtotime($employee_status['expiry_date'])));
					if(date('Y-m-d') >= $return_balance_date) {
						$wellness['returned_balance_status'] = true;
						$wellness['balance_credits_date'] = date('d/m/Y', strtotime($return_balance_date));
					}
				}
			}
		} else {
			if($employee_status['status'] == true) {
				$well_allocate = DB::table('employee_wallet_entitlement')->where('member_id', $input['employee_id'])->orderBy('created_at', 'desc')->first();
				$wellness['initial_allocation'] = number_format($well_allocate->wellness_entitlement, 2);
				$return_balance_date = date('Y-m-d', strtotime('+1 day', strtotime($employee_status['expiry_date'])));
				if(date('Y-m-d') >= $return_balance_date) {
					$wellness['balance_credits_date'] = date('d/m/Y', strtotime($return_balance_date));
					$wellness['returned_balance_status'] = true;
					if($wellness_balance < 0) {
						$medical['balance'] = "0.00";
					}
				} else {
					$wellness['balance_credits_date'] = date('d/m/Y');
				}
				
				$wellness['balance'] = number_format($well_allocate->wellness_entitlement - $total_current_usage_wellness, 2);
				$wellness_balance = $well_allocate->wellness_entitlement - $total_current_usage_wellness;

				if($wellness_balance < 0) {
					$wellness['exceed'] = true;
				} else {
					$wellness['exceed'] = false;
				}
			}
		}

		if($pro_allocation_medical_date) {
			$date = array(
				'pro_rated_start' => $pro_allocation_medical_date->pro_allocation_start_date ? date('d/m/Y', strtotime($pro_allocation_medical_date->pro_allocation_start_date)) : date('d/m/Y', strtotime($pro_allocation_medical_date->created_at)),
				'pro_rated_end' => $pro_allocation_medical_date->pro_allocation_end_date ? date('d/m/Y', strtotime($pro_allocation_medical_date->pro_allocation_end_date)) : date('d/m/Y', strtotime($pro_allocation_medical_date->created_at)),
				'usage_start'	=> date('d/m/Y', strtotime($coverage['plan_start'])),
				'usage_end'		=> $usage_date,
				'currency_type'	=> $wallet->currency_type
			);
		} else {
			$date = array(
				'pro_rated_start' => !empty($input['pro_allocation_start_date']) ? date('d/m/Y', strtotime($input['pro_allocation_start_date'])) : date('d/m/Y', strtotime($coverage['plan_start'])),
				'pro_rated_end' => !empty($input['pro_allocation_end_date']) ? date('d/m/Y', strtotime($input['pro_allocation_end_date'])) : date('d/m/Y', strtotime($last_day_coverage)),
				'usage_start'	=> date('d/m/Y', strtotime($coverage['plan_start'])),
				'usage_end'		=> $usage_date,
				'pro_allocation_start_date' => !empty($input['pro_allocation_start_date']) ? $input['pro_allocation_start_date'] : null,
				'pro_allocation_end_date' => !empty($input['pro_allocation_end_date']) ? $input['pro_allocation_end_date'] : null,
				'currency_type'	=> $wallet->currency_type
			);
		}

		$wallet_status = array(
			'member_id'		=> $input['employee_id'],
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		
		$calibrate_medical = false;
		$calibrate_wellness = false;

		if($customer_active_plan->account_type != "enterprise_plan")	{
			if($has_medical_allocation) {
				// start calibration for medical
				if(isset($input['calibrate_medical'])) {
					$calibrate_medical = false;
					$calibrate_medical = json_decode($input['calibrate_medical']);
					if($input['calibrate_medical'] == true && $total_allocation_medical > 0 && $exceed == false) {
						$new_allocation = $total_pro_medical_allocation;
						$to_return_to_company = $total_allocation_medical - $total_pro_medical_allocation;
					} else if($input['calibrate_medical'] == true && $exceed == true && $total_allocation_medical > 0) {
						$new_allocation = $total_pro_medical_allocation;
						$balance = abs($medical_balance);
						$to_return_to_company = $remaining_allocated_medical_credits - $balance;
					}
	
					if($input['calibrate_medical'] == true && $total_allocation_medical > 0) {
						$calibrate_medical = true;
						$calibrated_medical = DB::table('wallet_history')
						->where('wallet_id', $wallet->wallet_id)
						->where('logs', 'pro_allocation')
						->first();
	
						if($calibrated_medical) {
							return array('status' => false, 'message' => 'Medical Spending Account Pro Allocation is already updated.');
						}
						// begin medical callibration
						$calibrate_medical_data = array(
							'wallet_id'         => $wallet->wallet_id,
							'credit'            => $total_pro_medical_allocation,
							'logs'              => 'pro_allocation',
							'running_balance'   => $total_pro_medical_allocation,
							'spending_type'     => 'medical',
							'pro_allocation_start_date' => !empty($input['pro_allocation_start_date']) ? date('Y-m-d', strtotime($input['pro_allocation_start_date'])) : null,
							'pro_allocation_end_date' => !empty($input['pro_allocation_end_date']) ? date('Y-m-d', strtotime($input['pro_allocation_end_date'])) : null,
							'created_at'        => date('Y-m-d H:i:s'),
							'updated_at'        => date('Y-m-d H:i:s'),
							'currency_type'	=> $wallet->currency_type
						);
	
						  // begin medical callibration
						$calibrate_medical_deduction_parameter = array(
							'wallet_id'         => $wallet->wallet_id,
							'credit'            => $total_allocation_medical - $total_pro_medical_allocation,
							'logs'              => 'pro_allocation_deduction',
							'running_balance'   => $total_allocation_medical - $total_pro_medical_allocation,
							'spending_type'     => 'medical',
							'created_at'        => date('Y-m-d H:i:s'),
							'updated_at'        => date('Y-m-d H:i:s'),
							'currency_type'	=> $wallet->currency_type
						);
	
						$calibrate_medical_deduction_by_hr = array(
							'wallet_id'         => $wallet->wallet_id,
							'credit'            => $to_return_to_company,
							'logs'              => 'deducted_by_hr',
							'running_balance'   => $to_return_to_company,
							'spending_type'     => 'medical',
							'created_at'        => date('Y-m-d H:i:s'),
							'updated_at'        => date('Y-m-d H:i:s'),
							'currency_type'	=> $wallet->currency_type
						);
	
						$new_balance = $total_pro_medical_allocation - $total_medical_spent;
	
						if($new_balance < 0) {
							$new_balance = 0;
						}
	
						DB::table('wallet_history')->insert($calibrate_medical_data);
						DB::table('wallet_history')->insert($calibrate_medical_deduction_parameter);
						DB::table('wallet_history')->insert($calibrate_medical_deduction_by_hr);
						DB::table('e_wallet')->where('wallet_id', $wallet->wallet_id)->update(['balance' => $new_balance]);
	
						$wallet_status['medical_return_credits_date'] = date('Y-m-d', strtotime($input['last_date_of_coverage']));
						$wallet_status['medical_pro_allocation_status'] = 1;
						$wallet_status['medical_initial_allocation'] = $total_allocation_medical;
						$wallet_status['medical_pro_allocation'] = $total_pro_medical_allocation;
					}
				}
			}
		}
		
		if($has_wellness_allocation) {
			// start calibration for medical
			if(isset($input['calibrate_wellness'])) {
				if($input['calibrate_wellness'] == true && $total_allocation_wellness > 0 && $exceed == false) {
					$new_allocation = $total_pro_wellness_allocation;
					$to_return_to_company = $total_allocation_wellness - $total_pro_wellness_allocation;
				} else if($input['calibrate_wellness'] == true && $exceed == true && $total_allocation_wellness > 0) {
					$new_allocation = $total_pro_wellness_allocation;
					// $to_return_to_company = $total_allocation_wellness - $total_wellness_spent;

					$balance = abs($wellness_balance);
					$to_return_to_company = $remaining_allocated_wellness_credits - $balance;
				}

				if($input['calibrate_wellness'] == true && $total_allocation_wellness > 0 ) {
					$calibrate_wellness = true;
					$calibrated_wellness = DB::table('wellness_wallet_history')
					->where('wallet_id', $wallet->wallet_id)
					->where('logs', 'pro_allocation')
					->first();

					if($calibrated_wellness) {
						return array('status' => false, 'message' => 'Wellness Spending Account Pro Allocation is already updated.');
					}

					$new_balance = $total_pro_wellness_allocation - $total_wellness_spent;
					// begin medical callibration
					$calibrate_wellness_data = array(
						'wallet_id'         => $wallet->wallet_id,
						'credit'            => $total_pro_wellness_allocation,
						'logs'              => 'pro_allocation',
						'running_balance'   => $total_pro_wellness_allocation,
						'spending_type'     => 'wellness',
						'pro_allocation_start_date' => !empty($input['pro_allocation_start_date']) ? date('Y-m-d', strtotime($input['pro_allocation_start_date'])) : null,
						'pro_allocation_end_date' => !empty($input['pro_allocation_end_date']) ? date('Y-m-d', strtotime($input['pro_allocation_end_date'])) : null,
						'created_at'        => date('Y-m-d H:i:s'),
						'updated_at'        => date('Y-m-d H:i:s'),
						'currency_type'	=> $wallet->currency_type
					);

          			// begin medical callibration
					$calibrate_wellness_deduction_parameter = array(
						'wallet_id'         => $wallet->wallet_id,
						'credit'            => $total_allocation_wellness - $total_pro_wellness_allocation,
						'logs'              => 'pro_allocation_deduction',
						'running_balance'   => $total_allocation_wellness - $total_pro_wellness_allocation,
						'spending_type'     => 'wellness',
						'created_at'        => date('Y-m-d H:i:s'),
						'updated_at'        => date('Y-m-d H:i:s'),
						'currency_type'	=> $wallet->currency_type
					);

					$calibrate_wellness_deduction_by_hr = array(
						'wallet_id'         => $wallet->wallet_id,
						'credit'            => $to_return_to_company,
						'logs'              => 'deducted_by_hr',
						'running_balance'   => $to_return_to_company,
						'spending_type'     => 'wellness',
						'created_at'        => date('Y-m-d H:i:s'),
						'updated_at'        => date('Y-m-d H:i:s'),
						'currency_type'	=> $wallet->currency_type
					);
					
					if($new_balance < 0) {
						$new_balance = 0;
					}
					DB::table('wellness_wallet_history')->insert($calibrate_wellness_data);
					DB::table('wellness_wallet_history')->insert($calibrate_wellness_deduction_parameter);
					DB::table('wellness_wallet_history')->insert($calibrate_wellness_deduction_by_hr);
					DB::table('e_wallet')->where('wallet_id', $wallet->wallet_id)->update(['wellness_balance' => $new_balance]);
					
					$wallet_status['wellness_return_credits_date'] = date('Y-m-d', strtotime($input['last_date_of_coverage']));
					$wallet_status['wellness_pro_allocation_status'] = 1;
					$wallet_status['wellness_initial_allocation'] = $total_allocation_wellness;
					$wallet_status['wellness_pro_allocation'] = $total_pro_wellness_allocation;
				}
			}
		}
		
		if(!$check_wallet_status && $has_medical_allocation && $calibrate_medical || !$check_wallet_status && $has_wellness_allocation && $calibrate_wellness) {
			DB::table('member_wallet_status')->insert($wallet_status);
		}

		if($has_medical_allocation || $has_wellness_allocation) {
			if(isset($input['calibrate_welless']) || isset($input['calibrate_medical'])) {
				return array('status' => true, 'message' => 'Spending Account successfully updated to Pro Allocation credits.');
			}
		}

		return array('status' => true, 'medical' => $medical, 'wellness' => $wellness, 'date' => $date);
	}

	public static function removeEmployee($id, $expiry_date, $refund_status, $input)
	{
		$plan = DB::table('user_plan_type')->where('user_id', $id)->orderBy('created_at', 'desc')->first();
		$calculate = false;
		$total_refund = 0;
		// check if active plan is a trial plan and has a plan extension

		$active_plan = DB::table('user_plan_history')
		->where('user_id', $id)
		->where('type', 'started')
		->orderBy('date', 'desc')
		->first();

		$plan_active = DB::table('customer_active_plan')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->first();

		if($plan_active->account_type == "trial_plan") {
			// check if there is a plan extension
			$extension = DB::table('plan_extensions')
			->where('customer_active_plan_id', $plan_active->customer_active_plan_id)
			->first();
			if($extension && (int)$extension->enable == 1) {
	          	// check plan start if satisfies for the employee plan
				$expiry_date = date('Y-m-d', strtotime($expiry_date));
				$extension_plan_start = date('Y-m-d', strtotime($extension->plan_start));

				if($expiry_date >= $extension_plan_start) {
					$calculate = true;
					$invoice = DB::table('corporate_invoice')
					->where('customer_active_plan_id', $plan_active->customer_active_plan_id)
					->where('plan_extention_enable', 1)
					->first();
					$plan_start = $extension_plan_start;
				} else {
					$calculate = false;
					$plan_start = $plan->plan_start;
				}
			} else {
				$calculate = false;
				$plan_start = $plan->plan_start;
			}
		} else {
			$invoice = DB::table('corporate_invoice')
			->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
			->first();
			$calculate = true;
			$plan_start = $plan->plan_start;
		}
		$plan = DB::table('user_plan_type')->where('user_id', $id)->orderBy('created_at', 'desc')->first();

		if($calculate) {
			$diff = date_diff(new DateTime(date('Y-m-d', strtotime($plan_start))), new DateTime(date('Y-m-d', strtotime($expiry_date))));
			$days = $diff->format('%a') + 1;
			
			$total_days = date("z", mktime(0,0,0,12,31,date('Y')));
			$remaining_days = $total_days - $days + 1;

			$cost_plan_and_days = ($invoice->individual_price/$total_days);
			$temp_total = $cost_plan_and_days * $remaining_days;
			$total_refund = $temp_total * 0.70;
		}
		
		$withdraw = new PlanWithdraw();
		// save history
		$user_plan_history = new UserPlanHistory();
		$user_plan_history_data = array(
			'user_id'		=> $id,
			'type'			=> "deleted_expired",
			'date'			=> $expiry_date,
			'customer_active_plan_id' => $active_plan->customer_active_plan_id
		);

		try {
			if($plan_active->account_type != "out_of_pocket") {
				self::getEmployeeSpendingAccountSummaryNew($input);
			}
			
			$user_plan_history->createUserPlanHistory($user_plan_history_data);

			if($plan_active->account_type != "enterprise_plan")	{
				// check if active plan and date refund is exist
				$refund = DB::table('payment_refund')
				->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
				->where('date_refund', date('Y-m-d', strtotime($expiry_date)))
				->where('status', 0)
				->first();

				if($refund) {
					// save plan withdraw logs
					$payment_refund_id = $refund->payment_refund_id;
				} else {
					$payment_refund_id = PlanHelper::createPaymentsRefund($active_plan->customer_active_plan_id, date('Y-m-d', strtotime($expiry_date)));
				}
			} else {
				$payment_refund_id = PlanHelper::createPaymentsRefund($active_plan->customer_active_plan_id, date('Y-m-d', strtotime($expiry_date)));
			}
			

			$amount = $total_refund;
			$data = array(
				'payment_refund_id'			=> $payment_refund_id,
				'user_id'					=> $id,
				'customer_active_plan_id'	=> $active_plan->customer_active_plan_id,
				'date_withdraw'				=> $expiry_date,
				'amount'					=> $amount
			);

			if($plan_active->account_type == "lite_plan" || $plan_active->account_type == "out_of_pocket") {
				$data['refund_status'] = 2;
				$data['keep_seat']	= 1;
				$data['vacate_seat']	= 1;
			} else {
				$data['refund_status'] = $refund_status == true ? 0 : 2;
			}

			$withdraw->createPlanWithdraw($data);
			$user = DB::table('user')->where('UserID', $id)->first();
			$user_data = array(
				'Active'	=> 0
			);
			// update user and set to inactive
			DB::table('user')->where('UserID', $id)->update($user_data);
			// set company members removed to 1
			DB::table('corporate_members')->where('user_id', $id)->update(['removed_status' => 1]);
			PlanHelper::revemoDependentAccounts($id, date('Y-m-d', strtotime($expiry_date)));
			PlanHelper::updateCustomerPlanStatusDeleteUserVacantSeat($id);

			if($plan_active->account_type == "lite_plan" && $expiry_date < date('Y-m-d')) {
				// return member medical and wellness balance
				PlanHelper::returnMemberMedicalBalance($id);
				PlanHelper::returnMemberWellnessBalance($id);
			}
			return TRUE;
		} catch(Exception $e) {
			$email = [];
			$email['end_point'] = url('hr/employees/withdraw', $parameter = array(), $secure = null);
			$email['logs'] = 'Withdraw Employee Failed - '.$e;
			$email['emailSubject'] = 'Error log.';
			EmailHelper::sendErrorLogs($email);
			return FALSE;
		}
		return TRUE;
	}

	public function createWithDrawEmployees($user_id, $expiry_date, $history, $refund_status, $vacate_seat, $input)
	{
		$withdraw = new PlanWithdraw();
		$user_plan_history = new UserPlanHistory();

		// $active_plan = DB::table('user_plan_history')->where('user_id', $user_id)->orderBy('date', 'desc')->first();
		$active_plan = DB::table('user_plan_history')
		->where('user_id', $user_id)
		->where('type', 'started')
		->orderBy('created_at', 'desc')
		->first();

		$plan = DB::table('user_plan_type')->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
		$calculate = false;
		$total_refund = 0;
		// check if active plan is a trial plan and has a plan extension
		$plan_active = DB::table('customer_active_plan')->where('customer_active_plan_id', $active_plan->customer_active_plan_id)->first();
		if($plan_active->account_type == "trial_plan") {
			// check if there is a plan extension
			$extension = DB::table('plan_extensions')
			->where('customer_active_plan_id', $plan_active->customer_active_plan_id)
			->first();

			if($extension && (int)$extension->enable == 1) {
	          	// check plan start if satisfies for the employee plan
				$expiry_date = date('Y-m-d', strtotime($expiry_date));
				$extension_plan_start = date('Y-m-d', strtotime($extension->plan_start));

				if($expiry_date >= $extension_plan_start) {
					$calculate = true;
					$plan_start = $extension_plan_start;
					$invoice = DB::table('corporate_invoice')
					->where('customer_active_plan_id', $plan_active->customer_active_plan_id)
					->where('plan_extention_enable', 1)
					->first();
				} else {
					$calculate = false;
					$plan_start = $plan->plan_start;
				}
			} else {
				$calculate = false;
				$plan_start = $plan->plan_start;
			}
		} else {
			$invoice = DB::table('corporate_invoice')
			->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
			->first();
			$calculate = true;
			$plan_start = $plan->plan_start;
		}

		if($calculate) {
			$diff = date_diff(new DateTime(date('Y-m-d', strtotime($plan_start))), new DateTime(date('Y-m-d', strtotime($expiry_date))));
			$days = $diff->format('%a') + 1;

			$total_days = date("z", mktime(0,0,0,12,31,date('Y'))) + 1;
			$remaining_days = $total_days - $days;

			$cost_plan_and_days = ($invoice->individual_price/$total_days);
			$temp_total = $cost_plan_and_days * $remaining_days;
			$total_refund = $temp_total * 0.70;
		}

		$amount = $total_refund;
		// check if active plan and date refund is exist
		$refund = DB::table('payment_refund')
		->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
		->where('date_refund')
		->where('status', 0)
		->first();

		if($plan_active->account_type != "enterprise_plan")	{
			// check if active plan and date refund is exist
			$refund = DB::table('payment_refund')
			->where('customer_active_plan_id', $active_plan->customer_active_plan_id)
			->where('date_refund', date('Y-m-d', strtotime($expiry_date)))
			->where('status', 0)
			->first();

			if($refund) {
				// save plan withdraw logs
				$payment_refund_id = $refund->payment_refund_id;
			} else {
				$payment_refund_id = PlanHelper::createPaymentsRefund($active_plan->customer_active_plan_id, date('Y-m-d', strtotime($expiry_date)));
			}
		} else {
			$payment_refund_id = PlanHelper::createPaymentsRefund($active_plan->customer_active_plan_id, date('Y-m-d', strtotime($expiry_date)));
		}

		$data = array(
			'payment_refund_id'			=> $payment_refund_id,
			'user_id'					=> $user_id,
			'customer_active_plan_id'	=> $active_plan->customer_active_plan_id,
			'date_withdraw'				=> $expiry_date,
			'amount'					=> $amount
		);

		if($plan_active->account_type == "lite_plan" || $plan_active->account_type == "out_of_pocket") {
			$data['refund_status'] = 2;
			$data['keep_seat']	= 1;
			$data['vacate_seat']	= 1;
		} else {
			$data['refund_status'] = $refund_status == true ? 0 : 2;
			$data['vacate_seat'] = 1;
		}

		try {
			$withdraw->createPlanWithdraw($data);
			if($plan_active->account_type != "out_of_pocket") {
				self::getEmployeeSpendingAccountSummaryNew($input);
			}
			
			PlanHelper::revemoDependentAccounts($user_id, date('Y-m-d', strtotime($expiry_date)));
			return TRUE;
		} catch(Exception $e) {
			$email = [];
			$email['end_point'] = url('hr/employees/withdraw', $parameter = array(), $secure = null);
			$email['logs'] = 'Withdraw Schdedule Employee Failed - '.$e->getMessage();
			$email['emailSubject'] = 'Error log.';
			EmailHelper::sendErrorLogs($email);
			return FALSE;
		}
	}

	public static function getMemberPaginate($corporate_id, $pageNumber)
	{
		$perPage = 100;
		$statement = "SELECT 
			UserID as user_id
		FROM
			(SELECT 
				user.UserID
			FROM
				medi_user AS user
			LEFT JOIN medi_corporate_members AS corporateMembers ON corporateMembers.user_id = user.UserID
			WHERE
				corporateMembers.corporate_id = ".$corporate_id." UNION ALL SELECT 
				*
			FROM
				(SELECT 
				coverageAccounts.user_id
			FROM
				medi_user AS user
			LEFT JOIN medi_corporate_members AS corporateMembers ON corporateMembers.user_id = user.UserID
			LEFT JOIN medi_employee_family_coverage_sub_accounts AS coverageAccounts ON coverageAccounts.owner_id = user.UserID
			WHERE
				corporateMembers.corporate_id = ".$corporate_id."
					AND coverageAccounts.user_id IS NOT NULL
			ORDER BY coverageAccounts.user_id) AS dependent) AS mainTbl
			group by UserID";
		
		$db = DB::select($statement);
		$slice = array_slice($db, $perPage * ($pageNumber - 1), $perPage);
		$info = Paginator::make($slice, count($db), $perPage);
		return $info;
	}
	
	public static function getMemberByDateStarted($start, $end, $corporate_id, $customer_plan_id)
	{
		// employees
		$members = DB::table('user_plan_history')
						->join('corporate_members', 'corporate_members.user_id', '=', 'user_plan_history.user_id')
						->where('corporate_members.corporate_id', $corporate_id)
						->where('corporate_members.removed_status', 0)
						->where('user_plan_history.type', 'started')
						->where('user_plan_history.created_at', '>=', $start)
						->where('user_plan_history.created_at', '<=', $end)
						->count();

		// dependents
		$dependents = DB::table('dependent_plan_history')
						->join('dependent_plans', 'dependent_plans.dependent_plan_id', '=', 'dependent_plan_history.dependent_plan_id')
						->join('user', 'user.UserID', '=', 'dependent_plan_history.user_id')
						->where('user.Active', 1)
						->where('dependent_plan_history.type', 'started')
						->where('dependent_plans.customer_plan_id', $customer_plan_id)
						->where('dependent_plan_history.created_at', '>=', $start)
						->where('dependent_plan_history.created_at', '<=', $end)
						->count();
		
		return $members + $dependents;
	}

	public static function checkMemberDeactivated($member_id)
	{
		$check = DB::table('hr_employee_deactivate')->where('user_id', $member_id)->first();

		if($check) {
			return true;
		}

		return false;
	}

	public static function memberMedicalPrepaid($user_id, $start, $end, $spending_method)
	{
		
		$customer_id = \PlanHelper::getCustomerId($user_id);
		$spending = \CustomerHelper::getAccountSpendingStatus($customer_id);
		$get_allocation = 0;
		$deducted_credits = 0;
		$credits_back = 0;
		$deducted_by_hr_medical = 0;
		$in_network_temp_spent = 0;
		$e_claim_spent = 0;
		$deleted_employee_allocation = 0;
		$total_deduction_credits = 0;
		$allocation = 0;
		$medical_balance = 0;
		$balance = 0;
		$total_supp = 0;
		$in_network = 0;
		$out_network = 0;

        // check if employee has reset credits
		$employee_credit_reset_medical = DB::table('credit_reset')
		->where('id', $user_id)
		->where('spending_type', 'medical')
		->where('user_type', 'employee')
		->orderBy('created_at', 'desc')
		->first();

		$user = DB::table('user')->where('UserID', $user_id)->first();
		$e_wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
		$user_plan_history = DB::table('user_plan_history')
								->where('user_id', $user_id)
								->where('type', 'started')
								->orderBy('created_at', 'desc')
								->first();
		if($user_plan_history ) {
			if($employee_credit_reset_medical) {
				$start = $employee_credit_reset_medical->date_resetted;
				$wallet_history_id = $employee_credit_reset_medical->wallet_history_id;
				$wallet_history = DB::table('wallet_history')
								->join('e_wallet', 'e_wallet.wallet_id', '=', 'wallet_history.wallet_id')
								->where('wallet_history.wallet_id', $e_wallet->wallet_id)
								->where('e_wallet.UserID', $user_id)
								// ->where('spending_method', $spending_method)
								->where('wallet_history.created_at',  '>=', $start)
								->get();
			} else {
				$wallet_history = DB::table('wallet_history')
									->where('wallet_id', $e_wallet->wallet_id)
									// ->where('spending_method', $spending_method)
									->get();
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
					$out_network += $history->credit;
				}

				if($history->where_spend == "in_network_transaction") {
					$in_network_temp_spent += $history->credit;
					if($history->spending_type == "medical")	{
						$in_network += $history->credit;
					} else {
						$out_network += $history->credit;
					}
				}

				if($history->where_spend == "credits_back_from_in_network") {
					$credits_back += $history->credit;
				}

				if($history->logs == "added_by_hr_supplementary") {
					$total_supp += $history->credit;
				}
			}

			$pro_allocation = DB::table('wallet_history')
			->where('wallet_id', $e_wallet->wallet_id)
			->where('logs', 'pro_allocation')
			->sum('credit');
			
			$get_allocation_spent_temp = $in_network_temp_spent - $credits_back;
			$get_allocation_spent = $get_allocation_spent_temp + $e_claim_spent;
			$medical_balance = 0;

			if($spending['medical_method'] == "pre_paid") {
				
				$allocation = $get_allocation - $deducted_credits;
				$balance = $allocation - $get_allocation_spent;
				$medical_balance = $balance;
				$total_deduction_credits += $deducted_credits;

				if($balance < 0) {
					$allocation = $get_allocation_spent;
				}
			} else {
				if($pro_allocation > 0 && (int)$user->Active == 0) {
					$allocation = $pro_allocation;
					$balance = $pro_allocation - $get_allocation_spent;
					$medical_balance = $balance;

					if($balance < 0) {
						$balance = 0;
						$medical_balance = $balance;
						$allocation = $get_allocation - $deducted_credits;
					}
				} else if($pro_allocation == 0 && (int)$user->Active == 0){
					$allocation = 0;
					$balance = 0;
					$medical_balance = 0;
					$total_deduction_credits += $deducted_credits;
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
			}
			return array('allocation' => $allocation, 'get_allocation_spent' => $get_allocation_spent, 'balance' => $balance);

		} else {
			return false;
		}
	}

	public static function memberWellnessPrepaid($user_id, $start, $end)
	{
		
		$customer_id = \PlanHelper::getCustomerId($user_id);
		$spending = \CustomerHelper::getAccountSpendingStatus($customer_id);
		$get_allocation = 0;
		$deducted_credits = 0;
		$credits_back = 0;
		$deducted_by_hr_medical = 0;
		$in_network_temp_spent = 0;
		$e_claim_spent = 0;
		$deleted_employee_allocation = 0;
		$total_deduction_credits = 0;
		$allocation = 0;
		$medical_balance = 0;
		$balance = 0;
		$total_supp = 0;
		$in_network = 0;
		$out_network = 0;

        // check if employee has reset credits
		$employee_credit_reset_medical = DB::table('credit_reset')
		->where('id', $user_id)
		->where('spending_type', 'medical')
		->where('user_type', 'employee')
		->orderBy('created_at', 'desc')
		->first();

		$user = DB::table('user')->where('UserID', $user_id)->first();
		$e_wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
		$user_plan_history = DB::table('user_plan_history')
								->where('user_id', $user_id)
								->where('type', 'started')
								->orderBy('created_at', 'desc')
								->first();
		if($user_plan_history ) {
			if($employee_credit_reset_medical) {
				$start = $employee_credit_reset_medical->date_resetted;
				$wallet_history_id = $employee_credit_reset_medical->wallet_history_id;
				$wallet_history = DB::table('wellness_wallet_history')
								->join('e_wallet', 'e_wallet.wallet_id', '=', 'wellness_wallet_history.wallet_id')
								->where('wellness_wallet_history.wallet_id', $e_wallet->wallet_id)
								->where('e_wallet.UserID', $user_id)
								// ->where('spending_method', 'pre_paid')
								->where('wellness_wallet_history.created_at',  '>=', $start)
								->get();
			} else {
				$wallet_history = DB::table('wellness_wallet_history')
									->where('wallet_id', $e_wallet->wallet_id)
									// ->where('spending_method', 'pre_paid')
									->get();
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
					$out_network += $history->credit;
				}

				if($history->where_spend == "in_network_transaction") {
					$in_network_temp_spent += $history->credit;
					if($history->spending_type == "medical")	{
						$in_network += $history->credit;
					} else {
						$out_network += $history->credit;
					}
				}

				if($history->where_spend == "credits_back_from_in_network") {
					$credits_back += $history->credit;
				}

				if($history->logs == "added_by_hr_supplementary") {
					$total_supp += $history->credit;
				}
			}

			$pro_allocation = DB::table('wellness_wallet_history')
			->where('wallet_id', $e_wallet->wallet_id)
			->where('logs', 'pro_allocation')
			->sum('credit');
			
			$get_allocation_spent_temp = $in_network_temp_spent - $credits_back;
			$get_allocation_spent = $get_allocation_spent_temp + $e_claim_spent;
			$medical_balance = 0;

			if($spending['medical_method'] == "pre_paid") {
				
				$allocation = $get_allocation - $deducted_credits;
				$balance = $allocation - $get_allocation_spent;
				$medical_balance = $balance;
				$total_deduction_credits += $deducted_credits;

				if($balance < 0) {
					$allocation = $get_allocation_spent;
				}
			} else {
				if($pro_allocation > 0 && (int)$user->Active == 0) {
					$allocation = $pro_allocation;
					$balance = $pro_allocation - $get_allocation_spent;
					$medical_balance = $balance;

					if($balance < 0) {
						$balance = 0;
						$medical_balance = $balance;
						$allocation = $get_allocation - $deducted_credits;
					}
				} else if($pro_allocation == 0 && (int)$user->Active == 0){
					$allocation = 0;
					$balance = 0;
					$medical_balance = 0;
					$total_deduction_credits += $deducted_credits;
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
			}
			return array('allocation' => $allocation, 'get_allocation_spent' => $get_allocation_spent, 'balance' => $balance);

		} else {
			return false;
		}
	}

	public function getTransactionSpent($customer_id, $start, $end)
    {
        $end = \PlanHelper::endDate($end);
        $account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
        $user_allocated = \CustomerHelper::getActivePlanUsers($customer_id);

        $total_spent = 0;

        foreach($user_allocated as $key => $user) {
            $ids = StringHelper::getSubAccountsID($user);

			if(sizeof($ids) > 0) {
				 // panel
				 $total_spent += DB::table('transaction_history')
					->whereIn('UserID', $ids)
					->where('procedure_cost', '>', 0)
					->where('deleted', 0)
					->sum('procedure_cost');
			}
        }

        return $total_spent;
	}
	
	public static function getMemberWalletValidity($member_id, $spending_type)
	{
		$today = date('Y-m-d');
		$user_plan_history = DB::table('user_plan_history')->where('user_id', $member_id)->where('type', 'started')->orderBy('created_at', 'desc')->first();
		$valid = false;
		if(!$user_plan_history) {
			return false;
		}

		$customer_id = PlanHelper::getCustomerId($member_id);
		$spending = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$start = date('Y-m-d', strtotime($user_plan_history->date));
		// return ['start' => $start, 'medical_spending_end_date' => $spending->medical_spending_end_date, 'wellness_spending_end_date' => $spending->wellness_spending_end_date];
		// if($spending_type == "medical") {
			$end = date('Y-m-d', strtotime($spending->medical_spending_end_date));
		// } else {
			// $end = date('Y-m-d', strtotime($spending->wellness_spending_end_date));
		// }

		if($spending->medical_benefits_coverage == "out_of_pocket") {
			$start = date('Y-m-d', strtotime($spending->medical_spending_start_date));
		}

		if($spending->wellness_benefits_coverage == "out_of_pocket") {
			$start = date('Y-m-d', strtotime($spending->wellness_spending_start_date));
		}

		$end = PlanHelper::endDate($end);
		if($start <= $today && $end >= $today) {
			$valid = true;
		}
		
		$end = date('Y-m-d', strtotime($spending->wellness_spending_end_date));
		$end = PlanHelper::endDate($end);
		if($start <= $today && $end >= $today) {
			$valid = true;
		}

		return $valid;
	}

	public static function getMemberWalletStatus($member_id, $spending_type)
	{
		$emp_status = "active";
		$today = date('Y-m-d');
		$user_plan_history = DB::table('user_plan_history')->where('user_id', $member_id)->where('type', 'started')->orderBy('created_at', 'desc')->first();

		if(!$user_plan_history) {
			return false;
		}

		$customer_active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)->first();
		$member = DB::table('user')->where('UserID', $member_id)->first();
		$customer_id = PlanHelper::getCustomerId($member_id);
		$spending = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$start = date('Y-m-d', strtotime($user_plan_history->date));

		if($spending_type == "medical") {
			$end = date('Y-m-d', strtotime($spending->medical_spending_end_date));
		} else {
			$end = date('Y-m-d', strtotime($spending->wellness_spending_end_date));
		}

		$end = PlanHelper::endDate($end);


		if($start < $today) {
			$status = "active";
		}

		if($start <= $today && $end >= $today) {
			if((int)$member->member_activated == 0 || (int)$member->member_activated == 1 && (int)$member->Status == 0) {
				$emp_status = 'pending';
			}

			$panel = DB::table('transaction_history')->where('UserID', $member_id)->first();
			$non_panel = DB::table('e_claim')->where('user_id', $member_id)->first();
							
			if($panel || $non_panel) {
				$emp_status = 'active';
			} else if((int)$member->member_activated == 1){
				$emp_status = 'active';
			}
		}

		if($today > $end) {
			$emp_status = 'expired';
		}

		if((int)$member->Active == 0) {
			$emp_status = 'deactivated';
		}

		if((int)$spending->medical_enable == 0) {
			$emp_status = 'deactivated';
		}

		if($customer_active_plan->account_type == "out_of_pocket") {
			$emp_status = "active";
		}

		return $emp_status;
	}

	public static function getMemberEnterprisePlanTransactionCounts($member_id, $start, $end)
	{
		$end = \PlanHelper::endDate($end);
		$ids = \StringHelper::getSubAccountsID($member_id);

		$panels = DB::table('transaction_history')
					->whereIn('UserID', $ids)
					->where('spending_type', 'medical')
					->where('paid', 1)
					->where('deleted', 0)
					->where('enterprise_visit_deduction', 1)
					->where('date_of_transaction', '>=', $start)
					->where('date_of_transaction', '<=', $end)
					->count();

		$non_panels = DB::table('e_claim')
					->whereIn('user_id', $ids)
					->whereIn('status', [1, 0])
					->where('enterprise_visit_deduction', 1)
					->where('spending_type', 'medical')
					->where('created_at', '>=', $start)
					->where('created_at', '<=', $end)
					->count();
		
		$user_plan_history = DB::table('user_plan_history')
				->where('user_id', $member_id)
				->where('type', 'started')
				->orderBy('created_at', 'desc')
				->first();
		
		return ['visits' => $user_plan_history->total_visit_limit, 'panels' => $panels, 'non_panels' => $non_panels, 'total' => $panels + $non_panels];
	}

	public static function getMemberWalletPaymentMethod($member_id)
	{
		$top_up_user = DB::table('top_up_credits')->where('member_id', $member_id)->where('status', 0)->first();
		// check for spending transaction access
		$customer_id = \PlanHelper::getCustomerId($member_id);
		$spending_account_settings = DB::table('spending_account_settings')
                                  ->where('customer_id', $customer_id)
                                  ->orderBy('created_at', 'desc')
                                  ->first();
		$medical_payment_method = $spending_account_settings->medical_payment_method_panel == "mednefits_credits" ? 'pre_paid' : 'post_paid';
		if(!$top_up_user) {
			// check if first purchase is already paid
			$account_credits = DB::table('spending_purchase_invoice')
									->where('customer_id', $customer_id)
									->first();
			if($account_credits && (int)$account_credits->payment_status == 0) {
				$medical_payment_method = 'post_paid';
			}
		}
			
		return $medical_payment_method;
	}
}
?>