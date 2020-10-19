<?php
use Illuminate\Support\Facades\Input;

class SpendingAccountController extends \BaseController {
	public function getMednefitsCreditsAccount( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['start']) || $input['start'] == null) {
			return array('status' => false, 'message' => 'start term is required.');
		}

		if(empty($input['end']) || $input['end'] == null) {
			return array('status' => false, 'message' => 'end term is required.');
		}

		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$customer) {
			return ['status' => false, 'message' => 'cutomer does not exist'];
		}

		$account_credits = DB::table('mednefits_credits')
                        ->join('spending_purchase_invoice', 'spending_purchase_invoice.mednefits_credits_id', '=', 'mednefits_credits.id')
                        ->where('mednefits_credits.customer_id', $customer_id)
                        ->where('mednefits_credits.start_term', $input['start'])
                        // ->where('mednefits_credits.end_term', $input['end'])
                        ->get();

		if(sizeof($account_credits) == 0) {
			return ['status' => false, 'message' => 'no mednefits credits account for this customer'];
		}

		// get spending settings
		$spending_account_settings = DB::table('spending_account_settings')
								->where('customer_id', $customer_id)
								->orderBy('created_at', 'desc')
								->first();

		$total_credits = 0;
		$purchased_credits = 0;
		$bonus_credits = 0;
		$payment_status = false;
		$top_up_available = 0;
		$top_up_purchase = 0;
		$top_up_total_credits = 0;
		$top_up_bonus_credits = 0;
	
		// check for to top-up
		$toTopUp = DB::table('top_up_credits')
					->where('customer_id', $customer_id)
					->where('status', 0)
					->sum('credits');

		foreach($account_credits as $key => $credits) {
			if((int)$credits->payment_status == 1) {
				$payment_status = true;
			} else {
				$payment_status = false;
			}
		
			$purchased_credits += $credits->credits;
		
			if($credits->top_up == 1 && (int)$credits->payment_status == 0) {
				$top_up_purchase += $credits->credits;
			}
		}
	
		foreach($account_credits as $key => $credits) {
			$bonus_credits += $credits->bonus_credits;
			if($credits->top_up == 1 && (int)$credits->payment_status == 0) {
				$top_up_bonus_credits += $credits->credits;
			}
		}
	
		$total_credits = $purchased_credits + $bonus_credits;
		// get utilised credits both medical and wellness
		$creditAccount = DB::table('customer_credits')->where('customer_id', $customer_id)->first();
	
		$utilised_credits = \SpendingHelper::getMednefitsAccountSpending($customer_id, $input['start'], $input['end'], 'all', false);
		$refund_amount  = ($total_credits - $utilised_credits['credits']) - $bonus_credits;

		$format = array(
			'customer_id'           => $customer_id,
			'id'                    => $spending_account_settings->spending_account_setting_id,
			// 'mednefits_credits_id'  => $account_credits->id,
			'total_credits'         => number_format($total_credits, 2),
			'available_credits'     => number_format($total_credits - $utilised_credits['credits'], 2),
			'purchased_credits'     => number_format($purchased_credits, 2),
			'bonus_credits'         => number_format($bonus_credits, 2),
			'total_utilised_credits'  => number_format($utilised_credits['credits'], 2),
			'top_up_total_credits'  => number_format($toTopUp, 2),
			'top_up_purchase'       => number_format($toTopUp, 2),
			'top_up_bonus_credits'  => number_format($top_up_bonus_credits, 2),
			'payment_status'        =>  $payment_status,
			'to_top_up_status'      => $toTopUp > 0 ? true : false,
			'to_top_value'          => $toTopUp,
			'disable'               => (int)$spending_account_settings->activate_mednefits_credit_account == 0 ? true : false,
			'currency_type'			    => strtoupper($customer->currency_type),
			'refund_amount'					=> number_format($refund_amount, 2),
		);
		return ['status' => true, 'data' => $format];
	}
	  
	public function getMemberWalletDetails( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();
		
		if(empty($input['start']) || $input['start'] == null) {
			return array('status' => false, 'message' => 'start term is required.');
		}

		if(empty($input['end']) || $input['end'] == null) {
			return array('status' => false, 'message' => 'end term is required.');
		}

		if(empty($input['type']) || $input['type'] == null) {
			return array('status' => false, 'message' => 'type is required.');
		}

		if(!in_array($input['type'], ['medical', 'wellness'])) {
			return ['status' => false, 'message' => 'only medical and wellness'];
		}
		
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$customer) {
			return ['status' => false, 'message' => 'cutomer does not exist'];
		}
		
		// get spending settings
		$spending_account_settings = DB::table('spending_account_settings')
									->where('customer_id', $customer_id)
									->orderBy('created_at', 'desc')
									->first();

		if($spending_account_settings) {
			if($spending_account_settings->medical_benefits_coverage == null) {
				$plan = DB::table('customer_plan')
					->where('customer_buy_start_id', $customer_id)
					->orderBy('created_at', 'desc')
					->first();

				// update 
				$update = array(
					'medical_benefits_coverage'			=> $plan->account_type,
					'medical_payment_method_panel'		=> 'bank_transfer',
					'medical_payment_method_non_panel'	=> 'bank_transfer',
					'wellness_benefits_coverage'		=> $plan->account_type,
					'wellness_payment_method_panel'		=> 'bank_transfer',
					'wellness_payment_method_non_panel' => 'bank_transfer'
				);

				DB::table('spending_account_settings')
				->where('spending_account_setting_id', $spending_account_settings->spending_account_setting_id)
				->update($update);
				$spending_account_settings = DB::table('spending_account_settings')
				->where('customer_id', $customer_id)
				->orderBy('created_at', 'desc')
				->first();
			}
		}

		$total_credits = 0;
		$with_prepaid_credits = false;
		// get utilised credits both medical and wellness
		$creditAccount = DB::table('mednefits_credits')
									->where('customer_id', $customer_id)
									->where('start_term', $input['start'])
									->where('end_term', $input['end'])
									->first();

		if($creditAccount) {
			$total_credits = $creditAccount->credits + $creditAccount->bonus_credits;
			$with_prepaid_credits = true;
		}

		// get pending spending invoice
		$pendingInvoice = DB::table('spending_purchase_invoice')
		->join('mednefits_credits', 'mednefits_credits.id', '=', 'spending_purchase_invoice.mednefits_credits_id')
		->where('spending_purchase_invoice.customer_id', $customer_id)
		->where('spending_purchase_invoice.payment_status', 0)
		->first();

		if($input['type'] == "medical") {
			$credits = \SpendingHelper::getMednefitsAccountSpending($customer_id, $input['start'], $input['end'], 'medical', true);
			$panel_payment_method = $this->getPanelPaymentMethod($pendingInvoice, $spending_account_settings);
			// $panel_payment_method = $pendingInvoice && $spending_account_settings->medical_payment_method_panel == 'mednefits_credits' ? $spending_account_settings->medical_payment_method_panel_previous : $spending_account_settings->medical_payment_method_panel;
      		$non_panel_payment_method = $pendingInvoice && $spending_account_settings->medical_payment_method_non_panel == 'mednefits_credits' ? $spending_account_settings->medical_payment_method_non_panel_previous : $spending_account_settings->medical_payment_method_non_panel;
			$format = array(
				'customer_id'		=> $spending_account_settings->customer_id,
				'id'            => $spending_account_settings->spending_account_setting_id,
				'panel_payment_method'	=> $panel_payment_method,
				'non_panel_payment_method'	=> $non_panel_payment_method,
				'benefits_start'	=> $spending_account_settings->medical_spending_start_date,
				'benefits_end'		=> $spending_account_settings->medical_spending_end_date,
				'total_company_budget' => $credits['total_company_entitlement'],
				'total_company_entitlement' => $credits['total_company_entitlement'],
				'total_medical_entitlement' => $credits['total_medical_entitlement'],
				'total_balance' => $credits['medical_credits'],
				'roll_over'     => (int)$spending_account_settings->medical_roll_over == 1 ? true : false,
				'non_panel_submission' => (int)$spending_account_settings->medical_active_non_panel_claim == 1 ? true : false,
				'non_panel_reimbursement' => (int)$spending_account_settings->medical_reimbursement == 1 ? true : false,
				'benefits_coverage' => $spending_account_settings->medical_benefits_coverage,
				'status'          => (int)$spending_account_settings->medical_enable == 1 ? true : false,
				'wallet_status'         => (int)$spending_account_settings->medical_enable == 1 ? true : false,
				'with_prepaid_credits' => $with_prepaid_credits,
				'payment_status'  => $pendingInvoice ? false : true,
				'currency_type'	=> strtoupper($customer->currency_type)
			);
		} else {
			$credits = \SpendingHelper::getMednefitsAccountSpending($customer_id, $input['start'], $input['end'], 'wellness', true);
			$panel_payment_method = $pendingInvoice && $spending_account_settings->wellness_payment_method_panel == 'mednefits_credits' ? $spending_account_settings->wellness_payment_method_panel_previous : $spending_account_settings->wellness_payment_method_panel;
      		$non_panel_payment_method = $pendingInvoice && $spending_account_settings->wellness_payment_method_panel == 'mednefits_credits' ? $spending_account_settings->wellness_payment_method_panel_previous : $spending_account_settings->wellness_payment_method_non_panel;
			// format details
			$format = array(
				'customer_id'		  => $spending_account_settings->customer_id,
				'id'            => $spending_account_settings->spending_account_setting_id,
				'panel_payment_method'	=> $panel_payment_method,
				'non_panel_payment_method'	=> $non_panel_payment_method,
				'benefits_start'	=> $spending_account_settings->wellness_spending_start_date,
				'benefits_end'		=> $spending_account_settings->wellness_spending_end_date,
				'total_company_budget' => $total_credits,
				'total_company_entitlement' => $credits['total_company_entitlement'],
				'total_wellness_entitlement' => $credits['total_wellness_entitlement'],
				'total_balance' => $credits['wellness_credits'],
				'roll_over'     => (int)$spending_account_settings->wellness_roll_over == 1 ? true : false,
				'non_panel_submission' => (int)$spending_account_settings->wellness_active_non_panel_claim == 1 ? true : false,
				'non_panel_reimbursement' => (int)$spending_account_settings->wellness_reimbursement == 1 ? true : false,
				'benefits_coverage' => (int)$spending_account_settings->wellness_enable == 1 ? $spending_account_settings->wellness_benefits_coverage : 'out_of_pocket',
				'status'          => (int)$spending_account_settings->wellness_enable == 1 ? true : false,
				'wallet_status'         => (int)$spending_account_settings->wellness_enable == 1 ? true : false,
				'with_prepaid_credits' => $with_prepaid_credits,
				'payment_status'  => $pendingInvoice ? false : true,
				'currency_type'	=> strtoupper($customer->currency_type)
			);
		}
		
		return ['status' => true, 'data' => $format];
	}

	public function activateDeactivateWallet( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();
		
		if(empty($input['id']) || $input['id'] == null) {
			return array('status' => false, 'message' => 'id is required.');
		}

		if(empty($input['type']) || $input['type'] == null) {
			return array('status' => false, 'message' => 'type is required.');
		}
		
		if(!in_array($input['type'], ['medical', 'wellness'])) {
			return ['status' => false, 'message' => 'only medical and wellness'];
		}
		
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

			if(!$customer) {
				return ['status' => false, 'message' => 'customer does not exist'];
		}
		
		// get spending settings
			$spending_account_settings = DB::table('spending_account_settings')
									->where('spending_account_setting_id', $input['id'])
									->orderBy('created_at', 'desc')
									->first();
		
		if(!$spending_account_settings) {
			return ['status' => false, 'message' => 'spending account does not exits'];
		}

		$update = array(
			'updated_at' => date('Y-m-d H:i:s')
		);

		$status = !empty($input['status']) && $input['status'] === true || !empty($input['status']) && $input['status'] === "true" ? 1 : 0;
		if($input['type'] == "medical") {
			$update['medical_activate_allocation'] = $status;
		} else {
			$update['wellness_activate_allocation'] = $status;
		}

		$updateData = DB::table('spending_account_settings')
						->where('spending_account_setting_id', $input['id'])
						->update($update);
		
		if($updateData) {
			if($status == 1) {
				return ['status' => true, 'message' => ucwords($input['type']).' wallet has been successfully reactivated.'];
			} else {
				return ['status' => true, 'message' => ucwords($input['type']).' wallet has been successfully deactivated.'];
			}
		}

		return ['status' => false, 'message' => 'Failed to update wallet details.'];
	}

	public function updateWalletDetails( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['id']) || $input['id'] == null) {
			return array('status' => false, 'message' => 'id is required.');
		}

		if(empty($input['type']) || $input['type'] == null) {
				return array('status' => false, 'message' => 'type is required.');
		}
		
		if(!in_array($input['type'], ['medical', 'wellness'])) {
			return ['status' => false, 'message' => 'only medical and wellness'];
		}
		
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$customer) {
			return ['status' => false, 'message' => 'customer does not exist'];
		}
		
		// get spending settings
		$spending_account_settings = DB::table('spending_account_settings')
									->where('spending_account_setting_id', $input['id'])
									->orderBy('created_at', 'desc')
									->first();
		
		if(!$spending_account_settings) {
			return ['status' => false, 'message' => 'spending account does not exits'];
		}

		$update = array(
			'updated_at' => date('Y-m-d H:i:s')
		);

		if(!empty($input['start']) && !empty($input['end'])) {
			if($input['type'] == "medical") {
				$update['medical_spending_start_date'] = date('Y-m-d', strtotime($input['start']));
				$update['medical_spending_end_date'] = date('Y-m-d', strtotime($input['end']));
			} else {
				$update['wellness_spending_start_date'] = date('Y-m-d', strtotime($input['start']));
				$update['wellness_spending_end_date'] = date('Y-m-d', strtotime($input['end']));
			}
		}

		if($input['type'] == "medical") {
			if(isset($input['active_non_panel_claim'])) {
				$update['medical_active_non_panel_claim'] = $input['active_non_panel_claim'] == true || $input['active_non_panel_claim'] == "true" ? 1 : 0;
			}

			if(isset($input['reimbursement'])) {
				$update['medical_reimbursement'] = $input['reimbursement'] == true || $input['reimbursement'] == "true" ? 1 : 0;
			}

			if(!empty($input['payment_method_panel']) || $input['payment_method_panel'] != null) {
				$update['medical_payment_method_panel'] = $input['payment_method_non_panel'];
			}

			if(!empty($input['payment_method_non_panel']) || $input['payment_method_non_panel'] != null) {
				$update['medical_payment_method_non_panel'] = $input['payment_method_non_panel'];
			}
		} else {
			if(isset($input['active_non_panel_claim'])) {
				$update['wellness_active_non_panel_claim'] = $input['active_non_panel_claim'] == true || $input['active_non_panel_claim'] == "true" ? 1 : 0;
			}

			if(isset($input['reimbursement'])) {
				$update['wellness_reimbursement'] = $input['reimbursement'] == true || $input['reimbursement'] == "true" ? 1 : 0;
			}

			if(!empty($input['payment_method_panel']) || $input['ayment_method_panel'] != null) {
				$update['wellness_payment_method_panel'] = $input['payment_method_panel'];
			}

			if(!empty($input['payment_method_non_panel']) || $input['payment_method_non_panel'] != null) {
				$update['wellness_payment_method_non_panel'] = $input['payment_method_non_panel'];
			}
		}
		
		$updateData = DB::table('spending_account_settings')
						->where('spending_account_setting_id', $input['id'])
						->update($update);
		
		if($updateData) {
			return ['status' => true, 'message' => 'Wallet details updated.'];
		}

		return ['status' => false, 'message' => 'Failed to update wallet details.'];
	}

	public function activeWellnessWallet( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['id']) || $input['id'] == null) {
			return array('status' => false, 'message' => 'id is required.');
		}

		if(empty($input['benefits_start']) || $input['benefits_start'] == null) {
			return array('status' => false, 'message' => 'benefits_start is required.');
		}

		if(empty($input['benefits_end']) || $input['benefits_end'] == null) {
			return array('status' => false, 'message' => 'benefits_end is required.');
		}

		if(empty($input['non_panel_payment_method']) || $input['non_panel_payment_method'] == null) {
			return array('status' => false, 'message' => 'non_panel_payment_method is required.');
		}

		$non_panel_reimbursement = !empty($input['non_panel_reimbursement']) && $input['non_panel_reimbursement'] == true ? 1 : 0; 
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$customer) {
			return ['status' => false, 'message' => 'customer does not exist'];
		}
		
		// get spending settings
		$spending_account_settings = DB::table('spending_account_settings')
									->where('spending_account_setting_id', $input['id'])
									->orderBy('created_at', 'desc')
									->first();
		
		if(!$spending_account_settings) {
			return ['status' => false, 'message' => 'spending account does not exits'];
		}

		$update = array(
			'updated_at' => date('Y-m-d H:i:s'),
			'wellness_enable' => true,
			'wellness_spending_start_date'      => date('Y-m-d', strtotime($data['benefits_start'])),
			'wellness_spending_end_date'        => date('Y-m-d', strtotime($data['benefits_end'])),
			'wellness_payment_method_non_panel' => $data['non_panel_payment_method'],
			'wellness_benefits_coverage'        => 'lite_plan',
			'wellness_reimbursement'            => $non_panel_reimbursement,
			'wellness_benefits_coverage'        => $spending_account_settings->medical_benefits_coverage,
			'wellness_payment_method_panel_previous'		=> $data['non_panel_payment_method'] == 'mednefits_credits' ? 'bank_transfer' : $data['non_panel_payment_method'],
		  	'wellness_payment_method_non_panel_previous'	=> $data['non_panel_payment_method'] == 'mednefits_credits' ? 'bank_transfer' : $data['non_panel_payment_method'],
			'wellness_activate_allocation'      => 1,
			'wellness_enable'                   => 1,
		);

		if((int)$non_panel_reimbursement == 1) {
			$update['wellness_benefits_coverage'] = "lite_plan";
		  }

		$updateData = DB::table('spending_account_settings')
						->where('spending_account_setting_id', $input['id'])
						->update($update);
		
		if($updateData) {
			return ['status' => true, 'message' => 'Wellness Wallet has been successfully activated.'];
		}

		return ['status' => false, 'message' => 'Failed to activate Wellness Wallet.'];
	}

	public function getTermTotalSpent(Request $request)
	{
		if(empty($request->get('customer_id')) || $request->get('customer_id') == null) {
		return array('status' => false, 'message' => 'Customer ID is required.');
		}

		if(empty($request->get('start')) || $request->get('start') == null) {
		return array('status' => false, 'message' => 'start term is required.');
		}

		if(empty($request->get('end')) || $request->get('end') == null) {
		return array('status' => false, 'message' => 'end term is required.');
		}

		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $request->get('customer_id'))->first();

			if(!$customer) {
				return ['status' => false, 'message' => 'customer does not exist'];
		}
		
		// get spending settings
			$spending_account_settings = DB::table('spending_account_settings')
									->where('customer_id', $request->get('customer_id'))
									->orderBy('created_at', 'desc')
									->first();
	}

	public function updateSpendingPaymentMethod( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['id']) || $input['id'] == null) {
			return array('status' => false, 'message' => 'id is required.');
		}

		if(empty($input['medical_panel_payment_method']) || $input['medical_panel_payment_method'] == null) {
			return array('status' => false, 'message' => 'medical_panel_payment_method is required.');
		}

		if(empty($input['medical_non_panel_payment_method']) || $input['medical_non_panel_payment_method'] == null) {
			return array('status' => false, 'message' => 'medical_non_panel_payment_method is required.');
		}

		if(empty($input['wellness_non_panel_payment_method']) || $input['wellness_non_panel_payment_method'] == null) {
			return array('status' => false, 'message' => 'wellness_non_panel_payment_method is required.');
		}
		
		if(!in_array($input['medical_panel_payment_method'], ['giro', 'bank_transfer', 'mednefits_credits', 'out_of_pocket'])) {
			return ['status' => false, 'message' => 'medical_panel_payment_method should only be giro, bank_transfer or mednefits_credits'];
		}

		if(!in_array($input['medical_non_panel_payment_method'], ['giro', 'bank_transfer', 'mednefits_credits', 'out_of_pocket'])) {
			return ['status' => false, 'message' => 'medical_panel_paymentmedical_non_panel_payment_method_method should only be giro, bank_transfer or mednefits_credits'];
		}

		if(!in_array($input['wellness_non_panel_payment_method'], ['giro', 'bank_transfer', 'mednefits_credits', 'out_of_pocket'])) {
			return ['status' => false, 'message' => 'wellness_non_panel_payment_method should only be giro, bank_transfer or mednefits_credits'];
		}

		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$customer) {
			return ['status' => false, 'message' => 'customer does not exist'];
		}
		
		// get spending settings
		$spending_account_settings = DB::table('spending_account_settings')
									->where('spending_account_setting_id', $input['id'])
									->orderBy('created_at', 'desc')
									->first();
		
		if(!$spending_account_settings) {
			return ['status' => false, 'message' => 'spending account does not exits'];
		}

		$update = array(
			'updated_at' => date('Y-m-d H:i:s'),
			'medical_payment_method_panel' => $input['medical_panel_payment_method'],
			'medical_payment_method_non_panel' => $input['medical_non_panel_payment_method'],
			'wellness_payment_method_non_panel' => $input['wellness_non_panel_payment_method'],
		);

		$updateData = DB::table('spending_account_settings')
						->where('spending_account_setting_id', $input['id'])
						->update($update);
		
		if($updateData) {
			return ['status' => true, 'message' => 'Payment methods has been successfully updated.'];
		}

		return ['status' => false, 'message' => 'Failed to update Payment methods'];
	}

	public function getBenefitsCoverageDetails( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();
		
		if(empty($input['start']) || $input['start'] == null) {
			return array('status' => false, 'message' => 'start term is required.');
		}

		if(empty($input['end']) || $input['end'] == null) {
			return array('status' => false, 'message' => 'end term is required.');
		}

		if(empty($input['type']) || $input['type'] == null) {
			return array('status' => false, 'message' => 'type is required.');
		}
		
		if(!in_array($input['type'], ['basic_plan', 'enterprise_plan', 'out_of_pocket', 'stand_alone_plan', 'insurance_bundle'])) {
			return ['status' => true, 'message' => 'type should only be basic_plan, enterprise_plan, out_of_plan, stand_alone_plan or insurance_bundle'];
		}

		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$customer) {
			return ['status' => false, 'message' => 'cutomer does not exist'];
		}

		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();
		$spending_account_settings = DB::table('spending_account_settings')->where('customer_id', $customer_id)->orderBy('created_at', 'desc')->first();
		$plan = DB::table('customer_plan')->where('customer_buy_start_id', $customer_id)->orderBy('created_at', 'desc')->first();

		// get wallet use
		$medical = array(
			'panel'     => $spending_account_settings->medical_payment_method_panel == "mednefits_credits" ? true : false,
			'non_panel' => $spending_account_settings->medical_payment_method_non_panel == "mednefits_credits" ? true : false
		);

		// get wallet use
		$wellness = array(
			'panel'     => $spending_account_settings->wellness_payment_method_panel == "mednefits_credits" ? true : false,
			'non_panel' => $spending_account_settings->wellness_payment_method_non_panel == "mednefits_credits" ? true : false
		);
	
		if($input['type'] == "enterprise_plan") {
			if($plan->account_type == "out_of_pocket") {
				return ['status' => false, 'account_type' => 'out_of_pocket'];
			}

			$account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
			$user_allocated = \CustomerHelper::getActivePlanUsers($customer_id);
			$total = 0;
			$panel = 0;
			$non_panel = 0;
	  
			foreach ($user_allocated as $key => $user) {
			  $data = \MemberHelper::getMemberEnterprisePlanTransactionCounts($user, $input['start'], $input['end']);
			  $total += $data['total'];
			  $panel += $data['panels'];
			  $non_panel += $data['non_panels'];
			}
			
			return [
			  'total_panel'     => $panel,
			  'total_non_panel' => $non_panel,
			  'average' => sizeof($user_allocated) > 0 && $total > 0 ? sizeof($user_allocated) / $total : 0,
			  'id'			=> $spending_account_settings->spending_account_setting_id,
			  'customer_id'	=> $customer_id,
			  'medical'         => $medical,
			  'wellness'        => $wellness,
			  'currency_type'	=> strtoupper($customer->currency_type)
			];
		} else if($input['type'] == "out_of_pocket"){
			$credits = \MemberHelper::getTransactionSpent($customer_id, $input['start'], $input['end'], 'all', false);
			
			return [
				'status' => true,
				'spent' => $credits,
				'currency_type' => $customer->currency_type,
				'id'			=> $spending_account_settings->spending_account_setting_id,
				'customer_id'	=> $customer_id,
				'medical'         => $medical,
				'wellness'        => $wellness,
				'currency_type'	=> strtoupper($customer->currency_type)
			];
		} else {
			if($plan->account_type == "out_of_pocket") {
				return ['status' => false, 'account_type' => 'out_of_pocket'];
			}
			$credits = \SpendingHelper::getMednefitsAccountSpending($customer_id, $input['start'], $input['end'], 'all', false);
			
			return [
				'status' => true,
				'spent' => $credits,
				'currency_type' => $customer->currency_type,
				'id'			=> $spending_account_settings->spending_account_setting_id,
				'customer_id'	=> $customer_id,
				'medical'         => $medical,
				'wellness'        => $wellness,
				'currency_type'	=> strtoupper($customer->currency_type)
			];
		}
	}

	public function getTermsSpendingDates( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();
		$spending_account_settings = DB::table('spending_account_settings')
										->where('customer_id', $customer_id)
										->select('customer_id', 'medical_spending_start_date as start', 'medical_spending_end_date as end')
										->groupBy('medical_spending_start_date')
                                    	->orderBy('created_at', 'desc')
                                    	->limit(2)
										->get();
		
		$spending_account_setting = DB::table('spending_account_settings')
										->where('customer_id', $customer_id)
										->orderBy('created_at', 'desc')
										->first();

		$plan = DB::table('customer_plan')
					->where('customer_buy_start_id', $customer_id)
					->orderBy('created_at', 'desc')
					->first();
		$secondary_plan = null;
		if($plan->account_type == "enterprise_plan" && (int)$spending_account_setting->wellness_reimbursement == 1) {
		  if($spending_account_setting->wellness_benefits_coverage != "lite_plan") {
			// update wellness benefits method;
			DB::table('spending_account_settings')->where('spending_account_setting_id', $spending_account_setting->spending_account_setting_id)->update(['wellness_benefits_coverage' => 'lite_plan']);
			$secondary_plan = "lite_plan";
		  } else {
			DB::table('spending_account_settings')->where('spending_account_setting_id', $spending_account_setting->spending_account_setting_id)->update(['wellness_benefits_coverage' => 'out_of_pocket']);
		  }
		}
		return [
			'status' => true, 
			'data' => $spending_account_settings,
			'id'	=> $spending_account_setting->spending_account_setting_id,
			'customer_id'	=> $customer_id,
			'account_type' => $plan->account_type, 
			'secondary_plan' => $secondary_plan
		];
	}

	public function spendingAccountActivities( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();
  
		if(empty($input['start']) || $input['start'] == null) {
			return array('status' => false, 'message' => 'start term is required.');
		}

		if(empty($input['end']) || $input['end'] == null) {
			return array('status' => false, 'message' => 'end term is required.');
		}
		
		$limit = !empty($data['per_page']) ? $data['per_page'] : 5;
		// get spending account activity
		$activites = DB::table('spending_account_activity')->where('customer_id', $customer_id)->paginate($limit);
		
		$pagination = [];
		$pagination['current_page'] = $activites->getCurrentPage();
		$pagination['last_page'] = $activites->getLastPage();
		$pagination['total'] = $activites->getTotal();
		$pagination['per_page'] = $activites->getPerPage();
		$pagination['count'] = $activites->count();
		$format = [];

		foreach($activites as $key => $activity) {
			if($activity->type == "added_purchase_credits") {
				$activity->label = 'Purchased Credits';
				$activity->type_status = "added";
			} else if($activity->type == "added_bonus_credits") {
				$activity->label = 'Bonus Credits';
				$activity->type_status = "added";
			} else if($activity->type == "carried_forward_renewal_credits") {
				$activity->label = 'Carried-forward Purchased Credits';
				$activity->type_status = "added";
			} else if($activity->type == "carried_forward_bonus_credits") {
				$activity->label = 'Bonus Credits';
				$activity->type_status = "added";
			} else if($activity->type == "deduct_panel_spending") {
				$activity->label = 'Panel Monthly Spending';
				$activity->type_status = "deduct";
			} else if($activity->type == "deduct_non_panel_spending") {
				$activity->label = 'Non-Panel Spending';
				$activity->type_status = "deduct";
			} else if($activity->type == "deduct_non_panel_spending") {
				$activity->label = 'Refund';
				$activity->type_status = "deduct";
			}
			$activity->credit = number_format($activity->credit, 2);
			$format[] = $activity;
		}

		$pagination['data'] = $format;
		return ['status' => true, 'data' => $pagination];
	}

	public function getMemberAllocationActivity( )
	{	
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();
		
		if(empty($input['spending_type']) || $input['spending_type'] == null) {
			return ['status' => false, 'message' => 'spending_type is required.'];
		}

		$spending = \CustomerHelper::getAccountSpendingStatus($customer_id);
		$spending_method = null;

		if($input['spending_type'] == "medical") {
			$spending_method = $spending['medical_payment_method_panel'] == "mednefits_credits" ? 'pre_paid' : 'post_paid';
		} else {
			$spending_method = $spending['wellness_payment_method_non_panel'] == "mednefits_credits" ? 'pre_paid' : 'post_paid';
		}
	
		$per_page = !empty($input['limit']) ? $input['limit'] : 10;
		$pagination = [];
		$format = [];
		$info = DB::table('customer_business_information')->where('customer_buy_start_id', $customer_id)->first();

		$credit_wallet_activity = DB::table('credit_wallet_activity')
							->where('customer_id', $customer_id)
							->where('spending_type', $input['spending_type'])
							->orderBy('created_at', 'desc')
							->paginate($per_page);

		$pagination['current_page'] = $credit_wallet_activity->getCurrentPage();
		$pagination['last_page'] = $credit_wallet_activity->getLastPage();
		$pagination['total'] = $credit_wallet_activity->getTotal();
		$pagination['per_page'] = $credit_wallet_activity->getPerPage();
		$pagination['count'] = $credit_wallet_activity->count();

		//do some format
		foreach($credit_wallet_activity as $key => $activity) {
			$label = null;
			$type_status = null;

			if($activity->type == "added_employee_credits") {
				$label = "Member Enrollment";
				$type_status = "add";
			} if($activity->type == "new_employee_enrollment") {
				$label = "New Enrollment";
				$type_status = "add";
			} else if($activity->type == "added_employee_entitlement") {
				$label = "Entitlement Increase";
				$type_status = "add";
			} else if($activity->type == "deducted_employee_credits") {
				$label = "Entitlement Decrease";
				$type_status = "deduct";
			} else if($activity->type == "deducted_employee_entitlement") {
				$label = "Entitlement Decrease";
				$type_status = "deduct";
			} else {
				$label = "Member Removal";
				$type_status = "deduct";
			}
			
			$temp = array(
				'mednefits_credits_id'	=> $activity->mednefits_credits_id,
				'customer_id'	=> $activity->customer_id,
				'credit'	=> number_format($activity->credit, 2),
				'type' => $activity->type,
				'label'	=> $label,
				'type_status'	=> $type_status,
				'spending_type'	=> $activity->spending_type,
				'currency_type' => $activity->currency_type,
				'created_at' => date('j M Y', strtotime($activity->created_at)),
				'company' => $info->company_name,
			);

			array_push($format, $temp);
		}

		$pagination['data'] = $format;
		return $pagination;
	}
	
	public function getWalletDetails( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['type']) || $input['type'] == null) {
			return array('status' => false, 'message' => 'type is required.');
		}

		if(!in_array($input['type'], ['medical', 'wellness'])) {
			return ['status' => false, 'message' => 'only medical and wellness'];
		}

		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$customer) {
			return ['status' => false, 'message' => 'customer does not exist'];
		}

		// get spending settings
		$spending_account_settings = DB::table('spending_account_settings')
										->where('customer_id', $customer_id)
										->orderBy('created_at', 'desc')
										->first();
		
		if($spending_account_settings) {
			if($spending_account_settings->medical_benefits_coverage == null) {
				$plan = DB::table('customer_plan')
							->where('customer_buy_start_id', $customer_id)
							->orderBy('created_at', 'desc')
							->first();

				// update 
				$update = array(
					'medical_benefits_coverage'			=> $plan->account_type,
					'medical_payment_method_panel'		=> 'bank_transfer',
					'medical_payment_method_non_panel'	=> 'bank_transfer',
					'wellness_benefits_coverage'		=> $plan->account_type,
					'wellness_payment_method_panel'		=> 'bank_transfer',
					'wellness_payment_method_non_panel' => 'bank_transfer'
				);

				DB::table('spending_account_settings')
					->where('spending_account_setting_id', $spending_account_settings->spending_account_setting_id)
					->update($update);
				$spending_account_settings = DB::table('spending_account_settings')
					->where('customer_id', $customer_id)
					->orderBy('created_at', 'desc')
					->first();
			}
		}

		if($input['type'] == "medical") {
			// format details
			$format = array(
				'customer_id'		=> $spending_account_settings->customer_id,
				'payment_method'	=> $spending_account_settings->medical_payment_method_panel,
				'benefits_coverage'	=> $spending_account_settings->medical_benefits_coverage,
				'benefits_start'	=> $spending_account_settings->medical_spending_start_date,
				'benefits_end'		=> $spending_account_settings->medical_spending_end_date,
			);
		} else {
			if((int)$spending_account_settings->wellness_enable == 0) {
				return ['status' => false, 'message' => 'wellness wallet is not enabled'];
			}
	
			// format details
			$format = array(
				'customer_id'		=> $spending_account_settings->customer_id,
				'payment_method'	=> $spending_account_settings->medical_payment_method_panel,
				'benefits_coverage'	=> $spending_account_settings->wellness_benefits_coverage,
				'benefits_start'	=> $spending_account_settings->medical_spending_start_date,
				'benefits_end'		=> $spending_account_settings->medical_spending_end_date,
			);
		}
		
		return ['status' => true, 'data' => $format];
	}

	public function createMednefitsCreditsTopUp( ) 
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['total_credits']) || $input['total_credits'] == null) {
			return array('status' => false, 'message' => 'total credits is required.');
		}

		if(empty($input['purchase_credits']) || $input['purchase_credits'] == null) {
			return array('status' => false, 'message' => 'purchase credits is required.');
		}

		if(empty($input['bonus_percentage']) || $input['bonus_percentage'] == null) {
			return array('status' => false, 'message' => 'bonus_percentage is required.');
		}

		if(empty($input['bonus_credits']) || $input['bonus_credits'] == null) {
			return array('status' => false, 'message' => 'bonus_credits is required.');
		}

		if(empty($input['invoice_date']) || $input['invoice_date'] == null) {
			return array('status' => false, 'message' => 'invoice_date is required.');
		}
		
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$customer) {
			return ['status' => false, 'message' => 'customer does not exist'];
		}

		// check if there is payment false
		$account_credits = DB::table('mednefits_credits')
							->join('spending_purchase_invoice', 'spending_purchase_invoice.mednefits_credits_id', '=', 'mednefits_credits.id')
							->where('mednefits_credits.customer_id', $customer_id)
							->where('spending_purchase_invoice.payment_status', 0)
							->first();

		if($account_credits) {
			return ['status' => false, 'message' => 'Unable to create top up because there is pending payment for medenfits credits account.'];
		}

		$spending_account_settings = DB::table('spending_account_settings')
										->where('customer_id', $customer_id)
										->select('customer_id', 'customer_plan_id', 'medical_spending_start_date as start', 'medical_spending_end_date as end')
										->orderBy('created_at', 'desc')
										->first();
		$start = $spending_account_settings->start;
		$end = $spending_account_settings->end;

		// create mednefits credits data
		$mednefitCreditsAccount = array(
			'customer_plan_id'		=> $spending_account_settings->customer_plan_id,
			'customer_id'			=> $customer_id,
			'credits'				=> $input['purchase_credits'],
			'bonus_credits'			=> $input['bonus_credits'],
			'bonus_percentage'		=> $input['bonus_percentage'],
			'start_term'			=> date('Y-m-d', strtotime($start)),
			'end_term'				=> date('Y-m-d', strtotime($end)),
			'currency_type'			=> $customer->currency_type,
			'top_up'        		=> 1,
			'created_at'			=> date('Y-m-d H:i:s'),
			'updated_at'			=> date('Y-m-d H:i:s')
		);

		$mednefitsDataResult = DB::table('mednefits_credits')->insertGetId($mednefitCreditsAccount);
		// create spending account activity
		$spendingAccountActivityData = array(
			'mednefits_credits_id'	=> $mednefitsDataResult,
			'customer_id'			=> $customer_id,
			'credit'				=> $input['purchase_credits'],
			'type'					=> 'added_purchase_credits',
			'spending_type'			=> 'all',
			'currency_type'			=> $customer->currency_type,
			'created_at'				=> date('Y-m-d H:i:s'),
			'updated_at'				=> date('Y-m-d H:i:s')
		);

		Db::table('spending_account_activity')->insert($spendingAccountActivityData);
		$spendingAccountActivityData['credit'] = $input['bonus_credits'];
		$spendingAccountActivityData['type'] = 'added_bonus_credits';
		Db::table('spending_account_activity')->insert($spendingAccountActivityData);
	
		// create spending invoice purchase and reuse this one
		$invoice_number = \InvoiceLibrary::getInvoiceNuber('spending_purchase_invoice', 8);
		// billing
		$billing = DB::table('customer_billing_contact')->where('customer_buy_start_id', $customer_id)->first();
		$information = DB::table('customer_business_information')->where('customer_buy_start_id', $customer_id)->first();
		$active_plan = DB::table('customer_active_plan')->where('customer_start_buy_id', $customer_id)->orderBy('created_at', 'desc')->first();
		
		$spending_purchase = array(
				"customer_id"				=> $customer_id,
				"customer_plan_id"			=> $spending_account_settings->customer_plan_id,
				"customer_active_plan_id"	=> $active_plan->customer_active_plan_id,
				"plan_start"				=> date('Y-m-d', strtotime($start)),
				"plan_end"					=> date('Y-m-d', strtotime($end)),
				"duration"					=> "12 months",
				"invoice_number"			=> $invoice_number,
				"invoice_date"				=> date('Y-m-d'),
				"invoice_due"				=> date('Y-m-d', strtotime('+1 month')),
				"payment_amount"			=> 0,
				"payment_date"				=> null,
				"remarks"					=> null,
				"medical_purchase_credits"	=> $input['purchase_credits'],
				"medical_credit_bonus"		=> $input['bonus_credits'],
				"bonus_percentage"			=> $input['bonus_percentage'],
				"company_name"				=> $information->company_name,
				"company_address"			=> $information->company_address,
				"postal"					=> $information->postal_code,
				"contact_name"				=> $billing->first_name,
				"contact_number"			=> $billing->phone,
				"contact_email"				=> $billing->billing_email,
				'mednefits_credits_link'	=> 1,
				'mednefits_credits_id'		=> $mednefitsDataResult,
				'created_at'				=> date('Y-m-d H:i:s'),
				'updated_at'				=> date('Y-m-d H:i:s')
			);
		
		DB::table('spending_purchase_invoice')->insert($spending_purchase);
		// updated top up data
		DB::table('top_up_credits')->where('customer_id', $customer_id)
			->whereNull('mednefits_credits_id')
			->update(['mednefits_credits_id' => $mednefitsDataResult, 'status' => 1]);

		$emailDdata = array();
		$emailAddress = Config::get('config.pre_paid_credits_inquiry_email');
		$emailDdata['emailName']= $information->company_name;
		$emailDdata['emailPage']= 'email-templates.latest-templates.prepaid-account-template';
		$emailDdata['emailTo']= $emailAddress;
		$emailDdata['emailSubject'] = "Prepaid Credits Account Activation";
		\EmailHelper::sendEmail($emailDdata);
		return ['status' => true, 'message' => 'This top-up has been successfully completed.'];
	}

	public function activateBasicPlan( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$customer) {
			return ['status' => false, 'message' => 'customer does not exist'];
		}

		$plan = DB::table('customer_plan')
				->where('customer_buy_start_id', $customer_id)
				->orderBy('created_at', 'desc')
				->first();
		
		if($plan->account_type != "out_of_pocket") {
			return ['status' => false, 'message' => 'only out of pocket can activate mednefits basic plan'];
		}

		// update plan details
		$updateData = DB::table('customer_plan')
					->where('customer_plan_id', $plan->customer_plan_id)
					->update(['account_type' => 'lite_plan', 'updated_at' => date('Y-m-d H:i:s')]);
		DB::table('customer_active_plan')
					->where('plan_id', $plan->customer_plan_id)
					->update(['account_type' => 'lite_plan', 'updated_at' => date('Y-m-d H:i:s')]);
		
		$spending_account_settings = DB::table('spending_account_settings')
										->where('customer_id', $customer_id)
										->orderBy('created_at', 'desc')
										->first();
		$updateData = array(
			'medical_benefits_coverage'           => 'lite_plan',
			'medical_payment_method_panel'        => 'giro',
			'medical_payment_method_non_panel'    => 'giro',
			'wellness_benefits_coverage'          => 'lite_plan',
			'wellness_payment_method_panel'       => 'giro',
			'wellness_payment_method_non_panel'   => 'giro',
			'medical_spending_start_date'         => date('Y-m-d'),
			'medical_spending_end_date'           => date('Y-m-d', strtotime('+12 months')),
			'wellness_spending_start_date'        => date('Y-m-d'),
			'wellness_spending_end_date'          => date('Y-m-d', strtotime('+12 months')),
			'updated_at'                          => date('Y-m-d H:i:s')
		);
		
		// spending settings
		$updateSpendingSettings = DB::table('spending_account_settings')
									->where('spending_account_setting_id', $spending_account_settings->spending_account_setting_id)
									->update($updateData);
		return ['status' => true, 'message' => 'Mednefits Basic Plan has been successfully activated'];
	}

	public function enableDisableCreditsAccount( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['id']) || $input['id'] == null) {
			return array('status' => false, 'message' => 'spending settings id is required.');
		}
		
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$customer) {
			return ['status' => false, 'message' => 'customer does not exist'];
		}

		// check if there is payment false
		$account_credits = DB::table('mednefits_credits')
							->join('spending_purchase_invoice', 'spending_purchase_invoice.mednefits_credits_id', '=', 'mednefits_credits.id')
							->where('mednefits_credits.customer_id', $customer_id)
							->first();

		if(!$account_credits) {
			return ['status' => false, 'message' => 'Company does not have a Prepaid Credits Account.'];
		}

		$status = !empty($inpput['status']) && $inpput['status']=== true || !empty($inpput['status']) && $inpput['status'] === "true" ? 1 : 0;
		
		DB::table('spending_account_settings')
			->where('spending_account_setting_id', $inpput['id'])
			->update(['activate_mednefits_credit_account' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
		
		if($status == 1) {
			return ['status' => true, 'message' => 'Prepaid Credits Account has been successfully activated.'];
		} else {
			return ['status' => true, 'message' => 'Prepaid Credits Account has been successfully deactivated.'];
		}
	}

	public function activateMednefitCreditsAccount( )
	{
		$input = Input::all();
		$customer_id = PlanHelper::getCusomerIdToken();

		if(empty($input['total_credits']) || $input['total_credits'] == null) {
			return array('status' => false, 'message' => 'total credits is required.');
		}

		if(empty($input['purchase_credits']) || $input['purchase_credits'] == null) {
			return array('status' => false, 'message' => 'purchase credits is required.');
		}

		if(empty($input['bonus_percentage']) || $input['bonus_percentage'] == null) {
			return array('status' => false, 'message' => 'bonus_percentage is required.');
		}

		if(empty($input['bonus_credits']) || $input['bonus_credits'] == null) {
			return array('status' => false, 'message' => 'bonus_credits is required.');
		}

		if(empty($input['invoice_date']) || $input['invoice_date'] == null) {
			return array('status' => false, 'message' => 'invoice_date is required.');
		}
		
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

		if(!$customer) {
			return ['status' => false, 'message' => 'customer does not exist'];
		}

		// check if there is payment false
		$account_credits = DB::table('mednefits_credits')
							->join('spending_purchase_invoice', 'spending_purchase_invoice.mednefits_credits_id', '=', 'mednefits_credits.id')
							->where('mednefits_credits.customer_id', $customer_id)
							->first();

		if($account_credits) {
			return ['status' => false, 'message' => 'Company already have a Mednefits Credit Account created.'];
		}

		$spending_account_settings = DB::table('spending_account_settings')
										->where('customer_id', $customer_id)
										->select('customer_id', 'customer_plan_id', 'medical_spending_start_date as start', 'medical_spending_end_date as end', 'spending_account_setting_id')
										->orderBy('created_at', 'desc')
										->first();
		$start = $spending_account_settings->start;
		$end = $spending_account_settings->end;

		// create mednefits credits data
		$mednefitCreditsAccount = array(
			'customer_plan_id'		=> $spending_account_settings->customer_plan_id,
			'customer_id'			=> $customer_id,
			'credits'				=> $input['purchase_credits'],
			'bonus_credits'			=> $input['bonus_credits'],
			'bonus_percentage'		=> $input['bonus_percentage'],
			'start_term'			=> date('Y-m-d', strtotime($start)),
			'end_term'				=> date('Y-m-d', strtotime($end)),
			'currency_type'			=> $customer->currency_type,
			'top_up'        => 0,
			'created_at'			=> date('Y-m-d H:i:s'),
			'updated_at'			=> date('Y-m-d H:i:s')
		);

		$mednefitsDataResult = DB::table('mednefits_credits')->insertGetId($mednefitCreditsAccount);
		$mednefits_credits_id = $mednefitsDataResult;
		// create spending account activity
		$spendingAccountActivityData = array(
			'mednefits_credits_id'	=> $mednefits_credits_id,
			'customer_id'			=> $customer_id,
			'credit'				=> $input['purchase_credits'],
			'type'					=> 'added_purchase_credits',
			'spending_type'			=> 'all',
			'currency_type'			=> $customer->currency_type,
			'created_at'			=> date('Y-m-d H:i:s'),
			'updated_at'			=> date('Y-m-d H:i:s')
		);

		$spendingAccountActivityResult = DB::table('spending_account_activity')->insertGetId($spendingAccountActivityData);
		$spendingAccountActivityData['credit'] = $input['bonus_credits'];
		$spendingAccountActivityData['type'] = 'added_bonus_credits';
		DB::table('spending_account_activity')->insertGetId($spendingAccountActivityData);
	
		// create spending invoice purchase and reuse this one
		$invoice_number = \InvoiceLibrary::getInvoiceNuber('spending_purchase_invoice', 8);
		// billing
		$billing = DB::table('customer_billing_contact')->where('customer_buy_start_id', $customer_id)->first();
		$information = DB::table('customer_business_information')->where('customer_buy_start_id', $customer_id)->first();
		$active_plan = DB::table('customer_active_plan')->where('customer_start_buy_id', $customer_id)->orderBy('created_at', 'desc')->first();
		
		$spending_purchase = array(
			"customer_id"				=> $customer_id,
			"customer_plan_id"			=> $spending_account_settings->customer_plan_id,
			"customer_active_plan_id"	=> $active_plan->customer_active_plan_id,
			"plan_start"				=> date('Y-m-d', strtotime($start)),
			"plan_end"					=> date('Y-m-d', strtotime($end)),
			"duration"					=> "12 months",
			"invoice_number"			=> $invoice_number,
			"invoice_date"				=> date('Y-m-d', strtotime($input['invoice_date'])),
			"invoice_due"				=> date('Y-m-d', strtotime('+1 month', strtotime($input['invoice_date']))),
			"payment_amount"			=> 0,
			"payment_date"				=> null,
			"remarks"					=> null,
			"medical_purchase_credits"	=> $input['purchase_credits'],
			"medical_credit_bonus"		=> $input['bonus_credits'],
			"bonus_percentage"			=> $input['bonus_percentage'],
			"company_name"				=> $information->company_name,
			"company_address"			=> $information->company_address,
			"postal"					=> $information->postal_code,
			"contact_name"				=> $billing->first_name,
			"contact_number"			=> $billing->phone,
			"contact_email"				=> $billing->billing_email,
			'mednefits_credits_link'	=> 1,
			'mednefits_credits_id'		=> $mednefits_credits_id,
			'created_at'			=> date('Y-m-d H:i:s'),
			'updated_at'			=> date('Y-m-d H:i:s')
		);


		DB::table('spending_purchase_invoice')->insertGetId($spending_purchase);
		$update = array(
			'medical_payment_method_panel'      => 'mednefits_credits',
			'medical_payment_method_non_panel'  => 'mednefits_credits',
			'wellness_payment_method_panel'     => 'mednefits_credits',
			'wellness_payment_method_non_panel' => 'mednefits_credits',
			'activate_mednefits_credit_account'        => 1,
			'updated_at'			=> date('Y-m-d H:i:s')
		);

		DB::table('spending_account_settings')->where('spending_account_setting_id', $spending_account_settings->spending_account_setting_id)->update($update);
		$company = DB::table('customer_business_information')->where('customer_buy_start_id', $customer_id)->first();

		$emailDdata = array();
		$emailAddress = Config::get('config.pre_paid_credits_inquiry_email');
		$emailDdata['emailName']= $company->company_name;
		$emailDdata['emailPage']= 'email-templates.latest-templates.prepaid-account-template';
		$emailDdata['emailTo']= $emailAddress;
		$emailDdata['emailSubject'] = "Prepaid Credits Account Activation";
		\EmailHelper::sendEmail($emailDdata);
		return ['status' => true, 'message' => 'Company successfully created a Mednefits Credit Account.'];
	}

	public function downloadPrepaidInvoice( )
	{
		$input = Input::all();
		if(empty($input['token']) || $input['token'] == null) {
			return array('status' => false, 'message' => 'Token is required.');
		}

		$result = StringHelper::getJwtHrToken($input['token']);
		if(!$result) {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Need to authenticate user.'
			);
		}

		if(empty($input['id']) || $input['id'] == null) {
			return ['status' => false, 'message' => 'id is required'];
		}

		$customer_id = $result->customer_buy_start_id;
		$spendingPurchase = DB::table('spending_purchase_invoice')
								// ->join('mednefits_credits', 'mednefits_credits.id', '=', 'spending_purchase_invoice.mednefits_credits_id')
								->where('spending_purchase_invoice_id', $input['id'])
								// ->where('spending_purchase_invoice.customer_id', $customer_id)
								->first();
								
        if(!$spendingPurchase) {
            return ['status' => false, 'message' => 'Spending Purchase does not exists'];
        }

        $active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $spendingPurchase->customer_active_plan_id)->first();
        $customer_wallet = DB::table('customer_credits')->where('customer_id', $spendingPurchase->customer_id)->first();
        $billing = DB::table('customer_business_information')->where('customer_buy_start_id', $spendingPurchase->customer_id)->first();
        $data = array();
        $data['paid'] = $spendingPurchase->payment_status == 1 ? true : false;
        $data['invoice_date'] = date('d F Y', strtotime($spendingPurchase->invoice_date));
        $data['invoice_number'] = $spendingPurchase->invoice_number;
        $total = (float)$spendingPurchase->medical_purchase_credits + (float)$spendingPurchase->wellness_purchase_credits;
		$amount_due = $total - (float)$spendingPurchase->payment_amount;
		$data['total']  = number_format($total, 2);
        $data['paid'] = $amount_due > 0 ? false : true;
		if($data['paid'] == true) {
			$data['amount_due'] = "0.00";
		} else {
            $data['amount_due'] = number_format($total - (float)$spendingPurchase->payment_amount, 2);
        }
        $data['payment_status'] = $amount_due > 0 ? 'PENDING' : 'PAID';
        $data['invoice_due'] = date('d F Y', strtotime($spendingPurchase->invoice_due));
        $data['payment_date'] = $spendingPurchase->payment_date ? date('d F Y', strtotime($spendingPurchase->payment_date)) : null;
        $data['remarks']    = $spendingPurchase->remarks;
        $data['company_name']   = $spendingPurchase->company_name;
        $data['company_address']   = $spendingPurchase->company_address;
		$data['postal']   = $spendingPurchase->postal;
		$data['building_name']   = $billing->building_name;
        $data['unit_number']   = $billing->unit_number;
        $data['contact_name']   = $spendingPurchase->contact_name;
        $data['contact_number']   = $spendingPurchase->contact_number;
        $data['contact_email']   = $spendingPurchase->contact_email;
        $data['plan_start']   = date('d F Y', strtotime($spendingPurchase->plan_start));
        $data['plan_end']   = date('d F Y', strtotime($spendingPurchase->plan_end));
        $data['duration']   = $spendingPurchase->duration;
        $data['account_type'] = PlanHelper::getAccountType($active_plan->account_type);
        $data['plan_type'] = 'Basic Plan Mednefits Care (Corporate)';
        $data['currency_type']   = strtoupper($customer_wallet->currency_type);
        //spending account
        $data['spending_account'] = (float)$spendingPurchase->medical_purchase_credits > 0 ? true : false;
        $data['credits_purchase'] = number_format($spendingPurchase->medical_purchase_credits, 2);
        $data['credit_bonus'] = number_format($spendingPurchase->medical_credit_bonus, 2);
        $data['total_credits']  = number_format($spendingPurchase->medical_purchase_credits + $spendingPurchase->medical_credit_bonus, 2);
        $data['discount_credits']  = number_format($spendingPurchase->medical_credit_bonus, 2);
		
		// return View::make('pdf-download.globalTemplates.mednefits-credits-invoice', $data);
		$pdf = PDF::loadView('pdf-download.globalTemplates.mednefits-credits-invoice', $data);
		$pdf->getDomPDF()->get_option('enable_html5_parser');
		$pdf->setPaper('A4', 'portrait');
		return $pdf->stream($data['invoice_number'].' - '.time().'.pdf');
	}

	private function getPanelPaymentMethod($pendingInvoice, $spending_account_settings)
	{
		if (
			$pendingInvoice &&
			$spending_account_settings->medical_payment_method_panel == 'mednefits_credits'
		) {
			return $spending_account_settings->medical_payment_method_panel_previous == 'mednefits_credits' ? 'bank_transfer' : $spending_account_settings->medical_payment_method_panel_previous;
		}

		return $spending_account_settings->medical_payment_method_panel;
	}
}
