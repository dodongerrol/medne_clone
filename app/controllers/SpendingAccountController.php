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

		$account_credits = DB::table('mednefits_credits')
							->join('spending_purchase_invoice', 'spending_purchase_invoice.mednefits_credits_id', '=', 'mednefits_credits.id')
							->where('mednefits_credits.customer_id', $customer_id)
							->where('mednefits_credits.start_term', $input['start'])
							->where('mednefits_credits.end_term', $input['end'])
							->first();

		if(!$account_credits) {
			return ['status' => false, 'message' => 'no mednefits credits account for this customer'];
		}
		// get utilised credits both medical and wellness
		$creditAccount = DB::table('customer_credits')->where('customer_id', $customer_id)->first();

		$utilised_credits = SpendingHelper::getMednefitsAccountSpending($customer_id, $input['start'], $input['end'], 'all', false);
		$format = array(
		'customer_id'           => $account_credits->customer_id,
		'mednefits_credits_id'  => $account_credits->id,
		'total_credits'         => $account_credits->credits + $account_credits->bonus_credits,
		'available_credits'     => $account_credits->credits + $account_credits->bonus_credits,
		'purchased_credits'     => $account_credits->credits,
		'bonus_credits'         => $account_credits->bonus_credits,
		'total_utilised_credits'  => $utilised_credits['credits'],
		'payment_status'        => (int)$account_credits->payment_status == 1 ? true : false
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
		// get utilised credits both medical and wellness
		$creditAccount = DB::table('mednefits_credits')
									->where('customer_id', $customer_id)
									->where('start_term', $input['start'])
									->where('end_term', $input['end'])
									->first();

		if($creditAccount) {
			$total_credits = $creditAccount->credits + $creditAccount->bonus_credits;
		}

		if($input['type'] == "medical") {
			$credits = \SpendingHelper::getMednefitsAccountSpending($customer_id, $input['start'], $input['end'], 'medical', true);
			$format = array(
				'customer_id'		=> $spending_account_settings->customer_id,
				'id'            => $spending_account_settings->spending_account_setting_id,
				'payment_method'	=> $spending_account_settings->medical_payment_method_panel,
				'benefits_start'	=> $spending_account_settings->medical_spending_start_date,
				'benefits_end'		=> $spending_account_settings->medical_spending_end_date,
				'total_company_budget' => $total_credits,
				'total_balance' => $credits['medical_credits'],
				'roll_over'     => (int)$spending_account_settings->medical_roll_over == 1 ? true : false,
				'non_panel_submission' => (int)$spending_account_settings->medical_active_non_panel_claim == 1 ? true : false,
				'non_panel_reimbursement' => (int)$spending_account_settings->medical_reimbursement == 1 ? true : false,
				'benefits_coverage' => $spending_account_settings->wellness_benefits_coverage,
				'status'          => (int)$spending_account_settings->medical_enable == 1 ? true : false
			);
		} else {
			$credits = \SpendingHelper::getMednefitsAccountSpending($customer_id, $input['start'], $input['end'], 'welenss', true);
			// format details
			$format = array(
				'customer_id'		  => $spending_account_settings->customer_id,
				'id'            => $spending_account_settings->spending_account_setting_id,
				'payment_method'	=> $spending_account_settings->medical_payment_method_panel,
				'benefits_start'	=> $spending_account_settings->medical_spending_start_date,
				'benefits_end'		=> $spending_account_settings->medical_spending_end_date,
				'total_company_budget' => $total_credits,
				'total_balance' => $credits['wellness_credits'],
				'roll_over'     => (int)$spending_account_settings->wellness_roll_over == 1 ? true : false,
				'non_panel_submission' => (int)$spending_account_settings->wellness_active_non_panel_claim == 1 ? true : false,
				'non_panel_reimbursement' => (int)$spending_account_settings->wellness_reimbursement == 1 ? true : false,
				'benefits_coverage' => (int)$spending_account_settings->wellness_enable == 1 ? $spending_account_settings->wellness_benefits_coverage : 'out_of_pocket',
				'status'          => (int)$spending_account_settings->wellness_enable == 1 ? true : false
			);
		}
		
		return ['status' => true, 'data' => $format];
	}

	public function updateWalletDetails(Request $request)
	{
		if(empty($request->get('customer_id')) || $request->get('customer_id') == null) {
		return array('status' => false, 'message' => 'Customer ID is required.');
		}

		if(empty($request->get('id')) || $request->get('id') == null) {
		return array('status' => false, 'message' => 'id is required.');
		}

		if(empty($request->get('type')) || $request->get('type') == null) {
				return array('status' => false, 'message' => 'type is required.');
		}
		
			if(!in_array($request->get('type'), ['medical', 'wellness'])) {
				return ['status' => false, 'message' => 'only medical and wellness'];
		}
		
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $request->get('customer_id'))->first();

			if(!$customer) {
				return ['status' => false, 'message' => 'customer does not exist'];
		}
		
		// get spending settings
			$spending_account_settings = DB::table('spending_account_settings')
									->where('customer_id', $request->get('id'))
									->orderBy('created_at', 'desc')
									->first();
		
		if(!$spending_account_settings) {
		return ['status' => false, 'message' => 'spending account does not exits'];
		}
		$update = array(
		'updated_at' => date('Y-m-d H:i:s')
		);

		if(!empty($request->get('start')) && !empty($request->get('end'))) {
		if($request->get('type') == "medical") {
			$update['medical_spending_start_date'] = date('Y-m-d', strtotime($request->get('start')));
			$update['medical_spending_end_date'] = date('Y-m-d', strtotime($request->get('end')));
		} else {
			$update['wellness_spending_start_date'] = date('Y-m-d', strtotime($request->get('start')));
			$update['wellness_spending_end_date'] = date('Y-m-d', strtotime($request->get('end')));
		}
		}

		if($request->get('type') == "medical") {
		if(!empty($request->get('active_non_panel_claim')) || $request->get('active_non_panel_claim') != null) {
			$update['medical_active_non_panel_claim'] = $request->get('active_non_panel_claim') === true || $request->get('active_non_panel_claim') === "true" ? 1 : 0;
		}

		if(!empty($request->get('reimbursement')) || $request->get('reimbursement') != null) {
			$update['medical_reimbursement'] = $request->get('reimbursement') === true || $request->get('reimbursement') === "true" ? 1 : 0;
		}

		if(!empty($request->get('payment_method_panel')) || $request->get('payment_method_panel') != null) {
			$update['medical_payment_method_panel'] = $request->get('payment_method_non_panel');
		}

		if(!empty($request->get('payment_method_non_panel')) || $request->get('payment_method_non_panel') != null) {
			$update['medical_payment_method_non_panel'] = $request->get('payment_method_non_panel');
		}
		} else {
		if(!empty($request->get('active_non_panel_claim')) || $request->get('active_non_panel_claim') != null) {
			$update['wellness_active_non_panel_claim'] = $request->get('active_non_panel_claim') === true || $request->get('active_non_panel_claim') === "true" ? 1 : 0;
		}

		if(!empty($request->get('reimbursement')) || $request->get('reimbursement') != null) {
			$update['wellness_reimbursement'] = $request->get('reimbursement') === true || $request->get('reimbursement') == "true" ? 1 : 0;
		}

		if(!empty($request->get('payment_method_panel')) || $request->get('ayment_method_panel') != null) {
			$update['wellness_payment_method_panel'] = $request->get('payment_method_panel');
		}

		if(!empty($request->get('payment_method_non_panel')) || $request->get('payment_method_non_panel') != null) {
			$update['wellness_payment_method_non_panel'] = $request->get('payment_method_non_panel');
		}
		}
		
		$updateData = DB::table('spending_account_settings')
						->where('spending_account_setting_id', $request->get('id'))
						->update($update);
		
		if($updateData) {
		return ['status' => true, 'message' => 'Wallet details updated.'];
		}

		return ['status' => false, 'message' => 'Failed to update wallet details.'];
	}

	public function activeWellnessWallet(Request $request)
	{
		if(empty($request->get('customer_id')) || $request->get('customer_id') == null) {
		return array('status' => false, 'message' => 'Customer ID is required.');
		}

		if(empty($request->get('id')) || $request->get('id') == null) {
		return array('status' => false, 'message' => 'id is required.');
		}
		
		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $request->get('customer_id'))->first();

			if(!$customer) {
				return ['status' => false, 'message' => 'customer does not exist'];
		}
		
		// get spending settings
			$spending_account_settings = DB::table('spending_account_settings')
									->where('customer_id', $request->get('id'))
									->orderBy('created_at', 'desc')
									->first();
		
		if(!$spending_account_settings) {
		return ['status' => false, 'message' => 'spending account does not exits'];
		}

		$update = array(
		'updated_at' => date('Y-m-d H:i:s'),
		'wellness_enable' => true,
		);

		$updateData = DB::table('spending_account_settings')
						->where('spending_account_setting_id', $request->get('id'))
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

	public function updateSpendingPaymentMethod(Request $request)
	{
		if(empty($request->get('customer_id')) || $request->get('customer_id') == null) {
		return array('status' => false, 'message' => 'Customer ID is required.');
		}

		if(empty($request->get('id')) || $request->get('id') == null) {
		return array('status' => false, 'message' => 'id is required.');
		}

		if(empty($request->get('medical_panel_payment_method')) || $request->get('medical_panel_payment_method') == null) {
		return array('status' => false, 'message' => 'medical_panel_payment_method is required.');
		}

		if(empty($request->get('medical_non_panel_payment_method')) || $request->get('medical_non_panel_payment_method') == null) {
		return array('status' => false, 'message' => 'medical_non_panel_payment_method is required.');
		}

		if(empty($request->get('wellness_non_panel_payment_method')) || $request->get('wellness_non_panel_payment_method') == null) {
		return array('status' => false, 'message' => 'wellness_non_panel_payment_method is required.');
		}
		
		if(!in_array($request->get('medical_panel_payment_method'), ['giro', 'bank_transfer', 'mednefits_credits', 'out_of_pocket'])) {
		return ['status' => false, 'message' => 'medical_panel_payment_method should only be giro, bank_transfer or mednefits_credits'];
		}

		if(!in_array($request->get('medical_non_panel_payment_method'), ['giro', 'bank_transfer', 'mednefits_credits', 'out_of_pocket'])) {
		return ['status' => false, 'message' => 'medical_panel_paymentmedical_non_panel_payment_method_method should only be giro, bank_transfer or mednefits_credits'];
		}

		if(!in_array($request->get('wellness_non_panel_payment_method'), ['giro', 'bank_transfer', 'mednefits_credits', 'out_of_pocket'])) {
		return ['status' => false, 'message' => 'wellness_non_panel_payment_method should only be giro, bank_transfer or mednefits_credits'];
		}

		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $request->get('customer_id'))->first();

			if(!$customer) {
				return ['status' => false, 'message' => 'customer does not exist'];
		}
		
		// get spending settings
			$spending_account_settings = DB::table('spending_account_settings')
									->where('customer_id', $request->get('id'))
									->orderBy('created_at', 'desc')
									->first();
		
		if(!$spending_account_settings) {
		return ['status' => false, 'message' => 'spending account does not exits'];
		}

		$update = array(
		'updated_at' => date('Y-m-d H:i:s'),
		'medical_payment_method_panel' => $request->get('medical_panel_payment_method'),
		'medical_payment_method_non_panel' => $request->get('medical_non_panel_payment_method'),
		'wellness_payment_method_non_panel' => $request->get('wellness_non_panel_payment_method'),
		);

		$updateData = DB::table('spending_account_settings')
						->where('spending_account_setting_id', $request->get('id'))
						->update($update);
		
		if($updateData) {
		return ['status' => true, 'message' => 'Payment methods has been successfully updated.'];
		}

		return ['status' => false, 'message' => 'Failed to update Payment methods'];
	}

	public function getBenefitsCoverageDetails(Request $request)
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

		if(empty($request->get('type')) || $request->get('type') == null) {
		return array('status' => false, 'message' => 'type is required.');
		}
		
		if(!in_array($request->get('type'), ['basic_plan', 'enterprise_plan', 'out_of_plan', 'stand_alone_plan', 'insurance_bundle'])) {
		return ['status' => true, 'message' => 'type should only be basic_plan, enterprise_plan, out_of_plan, stand_alone_plan or insurance_bundle'];
		}

		$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $request->get('customer_id'))->first();

			if(!$customer) {
				return ['status' => false, 'message' => 'customer does not exist'];
		}

		if($request->get('type') != "enterprise_plan") {
		$credits = \SpendingHelper::getMednefitsAccountSpending($request->get('customer_id'), $request->get('start'), $request->get('end'), 'all', false);
		
		return [
			'status' => true,
			'spent' => $credits['credits'],
			'currency_type' => $customer->currency_type
		];
		} else {
		$account_link = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $request->get('customer_id'))->first();
		$user_allocated = \CustomerHelper::getActivePlanUsers($account_link->corporate_id, $request->get('customer_id'));
		$total = 0;
		$panel = 0;
		$non_panel = 0;

		foreach ($user_allocated as $key => $user) {
			$data = \MemberHelper::getMemberEnterprisePlanTransactionCounts($user, $request->get('start'), $request->get('end'));
			$total += $data['total'];
			$panel += $data['panels'];
			$non_panel += $data['non_panels'];
		}
		
		return [
			'total_panel'     => $panel,
			'total_non_panel' => $non_panel,
			'average' => sizeof($user_allocated) > 0 ? sizeof($user_allocated) / $total : 0
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
										->get();
		
		return ['status' => true, 'data' => $spending_account_settings];
	}
}
