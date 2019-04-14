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

}
