<?php
use Illuminate\Support\Facades\Input;
class UserCheckInController extends \BaseController {

	public function createUserCheckIn( )
	{
		$input = Input::all();
		$checkin = new UserCheckIn();
		$data = array(
			'user_id'				=> $input['user_id'],
			'date_check_in'	=> date('Y-m-d'),
			'clinic_id'			=> $input['clinic_id']
		);

		$result = $checkin->createUserCheckIn($data);
		if($result) {
			// send notification
			$user = DB::table('user')->where('UserID', $input['user_id'])->first();

			Notification::sendNotification('User Check-In - Mednefits', 'User '.ucwords($user->Name).' has checked-in to your clinic', null, $input['clinic_id'], $user->Image);
			return array(
				'status'	=> TRUE,
				'message'	=> 'Success.'
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Failed.'
		);
	}

	public function saveCheckInPayment( )
	{
		$input = Input::all();
		// return $input;
		$transaction = new Transaction();
		$clinic_data = DB::table('clinic')->where('ClinicID', $input['clinic_id'])->first();
		$user = DB::table('user')->where('UserID', $input['user_id'])->first();
		$procedure = DB::table('clinic_procedure')->where('ProcedureID', $input['procedure_id'])->first();
		$data = array(
			'UserID'							=> $input['user_id'],
			'ProcedureID'					=> $input['procedure_id'],
			'date_of_transaction'	=> date('Y-m-d'),
			'ClinicID'						=> $input['clinic_id'],
			'procedure_cost'			=> $input['amount'],
			'AppointmenID'				=> 0,
			'revenue'							=> 0,
			'debit'								=> 0,
			'medi_percent'				=> 0,
			'clinic_discount'			=> 0,
			'wallet_use'					=> 0,
			'current_wallet_amount' => 0,
			'credit_cost'					=> 0,
			'paid'								=> 1,
			'co_paid_status'			=> $clinic_data->co_paid_status,
			'DoctorID'						=> 0,
			'backdate_claim'			=> 1,
			'mobile'			=> 1
		);
		$result = $transaction->createTransaction($data);
		if($result) {
			// Notification::sendNotification('User Payment - Mednefits', 'User '.ucwords($user->Name).' has made a payment for '.ucwords($procedure->Name).' at $SGD'.$input['amount'].' to your clinic', url('app/setting/claim-report', $parameter = array(), $secure = null), $input['clinic_id'], $user->Image);
			Notification::sendNotification('User Payment - Mednefits', 'User '.ucwords($user->Name).' has made a payment for '.ucwords($procedure->Name).' at $SGD'.$input['amount'].' to your clinic', url('app/clinic/preview', $parameter = array($result->id), $secure = null), $input['clinic_id'], $user->Image);
			return array(
				'status'	=> TRUE,
				'message'	=> 'Success.'
			);
		}

		return array(
			'status'	=> FALSE,
			'message'	=> 'Failed.'
		);
	}

	public function checkUserPin( )
	{
		$input = Input::all();
	    $user = new User();

	    return $user->checkUserPin($input['user_id'], $input['pin']);
	}

	public function getClinicCheckInLists( )
	{
		$getSessionData = StringHelper::getMainSession(3);
		$clinic_id = $getSessionData->Ref_ID;
		
		if($getSessionData != FALSE){
			$format = [];
			$check_ins = DB::table('user_check_in_clinic')
							->where('clinic_id', $clinic_id)
							->where('check_in_type', 'in_network_transaction')
							->where('status', 0)
							->get();

			foreach ($check_ins as $key => $check) {
				$cap_per_visit = 0;
				$currency_symbol = "";
				$user = DB::table('user')->where('UserID', $check->user_id)->first();
				if($check->cap_per_visit == 0) {
					$cap_per_visit = "Not Applicable";
				} else {
					$cap_per_visit = number_format($check->cap_per_visit, 2);
					$currency_symbol = $check->currency_symbol == "myr" ? 'RM' : 'S$';
				}
				if($user) {
					$temp = array(
						'check_in_id' 	=> $check->check_in_id,
						'clinic_id'		=> $check->clinic_id,
						'registration_date' => date('d F Y, h:i a', strtotime($check->check_in_time)),
						'transaction_id'	=> $check->id,
						'cap_per_visit'		=> $cap_per_visit,
						'currency_symbol'	=> $currency_symbol,
						'name'			=> ucwords($user->Name),
						'nric'			=> $user->NRIC,
						'remarks'		=> (int)$check->status == 0 ? 'Pending' : 'Done',
						'expiry'		=> date('Y-m-d H:i:s', strtotime('+120 minutes', strtotime($check->check_in_time)))
					);

					array_push($format, $temp);
				}
			}

			return array('status' => true, 'data' => $format);
		} else {
			return array('status' => false, 'message' => 'Session expired.');
		}
	}

	public function getSpecificCheckIn( )
	{
		$input = Input::all();

		$getSessionData = StringHelper::getMainSession(3);
		$clinic_id = $getSessionData->Ref_ID;

		if($getSessionData != FALSE){
			if(empty($input['check_in_id']) || $input['check_in_id'] == null) {
				return array('status' => false, 'message' => 'Check In ID is required.');
			}

			$check = DB::table('user_check_in_clinic')
								->where('check_in_id', $input['check_in_id'])
								->where('clinic_id', $clinic_id)
								->where('check_in_type', 'in_network_transaction')
								->where('status', 0)
								->first();

			if(!$check) {
				return array('status' => false, 'message' => 'Check In data does not exist.');
			}

			$user = DB::table('user')->where('UserID', $check->user_id)->first();
					$cap_per_visit = 0;
			if($user) {
				$cap_per_visit = 0;
				$currency_symbol = "";
				if($check->cap_per_visit == 0) {
					$cap_per_visit = "Not Applicable";
				} else {
					$cap_per_visit = number_format($check->cap_per_visit, 2);
					$currency_symbol = $check->currency_symbol == "myr" ? 'RM' : 'S$';
				}
				$temp = array(
					'check_in_id' 	=> $check->check_in_id,
					'clinic_id'		=> $check->clinic_id,
					'registration_date' => date('d F Y, h:i a', strtotime($check->check_in_time)),
					'transaction_id'	=> $check->id,
					'cap_per_visit'		=> $cap_per_visit,
					'currency_symbol'	=> $currency_symbol,
					'name'			=> ucwords($user->Name),
					'nric'			=> $user->NRIC,
					'remarks'		=> (int)$check->status == 0 ? 'Pending' : 'Done',
					'expiry'		=> date('Y-m-d H:i:s', strtotime('+120 minutes', strtotime($check->check_in_time)))
				);

				return array('status' => true, 'data' => $temp);
			} else {
				return array('status' => false, 'message' => 'Member does not exist.');
			}
		} else {
			return array('status' => false, 'message' => 'Session expired.');
		}
	}

	public function deleteSpecificCheckIn( )
	{
		$input = Input::all();

		$getSessionData = StringHelper::getMainSession(3);
		$clinic_id = $getSessionData->Ref_ID;

		if($getSessionData != FALSE){
			if(empty($input['check_in_id']) || $input['check_in_id'] == null) {
				return array('status' => false, 'message' => 'Check In ID is required.');
			}

			$check = DB::table('user_check_in_clinic')
								->where('check_in_id', $input['check_in_id'])
								->where('clinic_id', $clinic_id)
								->where('check_in_type', 'in_network_transaction')
								->where('status', 0)
								->first();

			if(!$check) {
				return array('status' => false, 'message' => 'Check In data does not exist.');
			}

			DB::table('user_check_in_clinic')
				->where('check_in_id', $input['check_in_id'])
				->where('clinic_id', $clinic_id)
				->where('check_in_type', 'in_network_transaction')
				->where('status', 0)
				->delete();
			return array('status' => true, 'message' => 'Success');
		} else {
			return array('status' => false, 'message' => 'Session expired.');
		}
	}

	public function checkCheckInAutoDelete( )
	{
		$input = Input::all();

		$getSessionData = StringHelper::getMainSession(3);
		$clinic_id = $getSessionData->Ref_ID;

		if($getSessionData != FALSE){
			if(empty($input['check_in_id']) || $input['check_in_id'] == null) {
				return array('status' => false, 'message' => 'Check In ID is required.');
			}

			$check = DB::table('user_check_in_clinic')
								->where('check_in_id', $input['check_in_id'])
								->where('clinic_id', $clinic_id)
								->where('check_in_type', 'in_network_transaction')
								->where('status', 0)
								->first();

			if(!$check) {
				return array('status' => false, 'message' => 'Check In data does not exist.');
			}

			$expiry = date('Y-m-d H:i:s', strtotime('+120 minutes', strtotime($check->check_in_time)));
			$date = date('Y-m-d H:i:s');

			if($date > $expiry) {
				// delete
				DB::table('user_check_in_clinic')
					->where('check_in_id', $input['check_in_id'])
					->where('clinic_id', $clinic_id)
					->where('check_in_type', 'in_network_transaction')
					->where('status', 0)
					->delete();
				return array('status' => true, 'message' => 'Success');
			} else {
				return array('status' => false, 'message' => 'Expiry date not yet in time.');
			}
			
		} else {
			return array('status' => false, 'message' => 'Session expired.');
		}
	}

}
