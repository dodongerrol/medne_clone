<?php

class DashboardController extends \BaseController {


	public function getMinimumDate( )
	{
		$transaction = new Transaction( );
		return $transaction->getMinimumDate();
	}
	public function countAppointments( ) 
	{
		$appointment = new UserAppoinment( );
		$clinic = StringHelper::getAuthSession();
		$input = Input::all();
		return $appointment->countAppointments($clinic->Ref_ID, $input['start'], $input['end']);
	}

	public function listAppointments( ) 
	{
		$appointment = new UserAppoinment( );
		$clinic = StringHelper::getAuthSession();
		$data['result'] = $appointment->listAppointments($clinic->Ref_ID);
		return View::make('dashboard.schedule', $data);
	}

	public function getClinicTotalRevenue( ) 
	{
		$clinic = StringHelper::getAuthSession();
		$transaction = new Transaction( );
		$input = Input::all();
		return $transaction->getClinicTotalRevenue($clinic->Ref_ID, $input['start'], $input['end']);
	}

	public function getClinicCredits( )
	{
		$clinic = StringHelper::getAuthSession();
		$transaction = new Transaction( );
		$input = Input::all();
		return $transaction->getClinicCredits($clinic->Ref_ID, $input['start'], $input['end']);
	}

	public function getClinicCollected( )
	{
		$clinic = StringHelper::getAuthSession();
		$transaction = new Transaction( );
		$input = Input::all();
		return $transaction->getClinicCollected($clinic->Ref_ID, $input['start'], $input['end']);
	}

	public function viewAppointment($id)
	{
		$appointment = new UserAppoinment( );
		$data['result'] = $appointment->viewAppointment($id);
		return View::make('dashboard.view-appointment', $data);
	}

	public function viewTransactionHistoryLimitView( )
	{
		$clinic = StringHelper::getAuthSession();
		$transaction = new Transaction( );
		$data['results'] = $transaction->viewTransactionHistoryLimitView($clinic->Ref_ID);
		// $data['result_bulk_trans'] = $transaction->viewTransactionBulkHistoryLimitView($clinic->Ref_ID);
		// return $data;
		return View::make('dashboard.transaction-views', $data);
	}

	public function viewScheduleByDate( )
	{
		$input = Input::all();
		$clinic = StringHelper::getAuthSession();
		$appointment = new UserAppoinment( );

		$data['result'] = $appointment->viewAppointmentByDate($input['start'], $input['end'], $clinic->Ref_ID);
		return View::make('dashboard.schedule', $data);
	}

	public function viewTransactionByDate( )
	{
		$input = Input::all();
		$clinic = StringHelper::getAuthSession();
		$transaction = new Transaction( );

		$data['results'] = $transaction->viewTransactionByDate($input['start'], $input['end'], $clinic->Ref_ID);
		return View::make('dashboard.transaction-views', $data);
	}

	public function paymentTransactionHistory( )
	{
		$input = Input::all();
		$clinic = StringHelper::getAuthSession();
		$transaction = new Transaction( );
		// $result_bulk = $bulk->getBulkTransactionByDate($clinic->Ref_ID, $input['start'], $input['end']);
		$data['results'] = $transaction->paymentTransactionHistory($input['start'], $input['end'], $input['search'], $clinic->Ref_ID);
		// return $data;
		return View::make('settings.payments.payment-transaction-view', $data);
	}

	public function paymentDownloadTransactionHistory($start, $end)
	{
		$clinic = StringHelper::getAuthSession();
		$transaction = new Transaction( );
		$container = array();
		$data = $transaction->paymentTransactionHistory($start, $end, null, $clinic->Ref_ID);
		$title = 'Payment Transaction History ('.$start.' - '.$end.')';

		foreach ($data as $key => $value) {
			if((int)$value->credit_cost == 0){
				$transaction_fees = 0;
			} else if((int)$value->credit_cost > 0) {
				$transaction_fees = (int)$value->procedure_cost * $value->medi_percent;
			}
			$collected_amount = (int)$value->procedure_cost - (int)$value->credit_cost;
			$container[] = array(
				'PaymentDate'					=> date('M', strtotime($value->updated_at)).' '.date('d', strtotime($value->updated_at)).' '.date('Y', strtotime($value->updated_at)),
				'Customer'						=> ucwords($value->Name),
				'Staff'							=> ucwords($value->doctor_name),
				'Service/Class'					=> ucwords($value->clinic_procedure_name),
				'Initial Booking Date'			=> date('M', $value->Created_on).' '.date('d', $value->Created_on).' '.date('Y', $value->Created_on),
				'Appt/Class Date'				=> date('M', $value->BookDate).' '.date('d', $value->BookDate).' '.date('Y', $value->BookDate),
				'Total Amount'					=> "$".$value->procedure_cost,
				'Collected Amount'				=> "$".$collected_amount,
				'Medi-Credit'					=> $value->credit_cost,
				'Medicloud Transaction Fees'	=> $transaction_fees
			);
		}

		$excel = Excel::create($title, function($excel) use($container) {
				$excel->sheet('Sheetname', function($sheet) use($container) {
						$sheet->fromArray( $container );

				});
		})->export('xls');
	}

	public function paymentSearchDownloadTransactionHistory($search)
	{	
		// return "search download";
		$clinic = StringHelper::getAuthSession();
		$transaction = new Transaction( );
		$container = array();
		$data = $transaction->paymentTransactionHistory(null, null, $search, $clinic->Ref_ID);
		$title = 'Payment Transaction History - ( Search Term - '.$search.' )';

		foreach ($data as $key => $value) {
			if((int)$value->credit_cost == 0){
				$transaction_fees = 0;
			} else if((int)$value->credit_cost > 0) {
				$transaction_fees = (int)$value->procedure_cost * $value->medi_percent;
			}
			$collected_amount = (int)$value->procedure_cost - (int)$value->credit_cost;
			$container[] = array(
				'PaymentDate'					=> date('M', strtotime($value->updated_at)).' '.date('d', strtotime($value->updated_at)).' '.date('Y', strtotime($value->updated_at)),
				'Customer'						=> ucwords($value->Name),
				'Staff'							=> ucwords($value->doctor_name),
				'Service/Class'					=> ucwords($value->clinic_procedure_name),
				'Initial Booking Date'			=> date('M', $value->Created_on).' '.date('d', $value->Created_on).' '.date('Y', $value->Created_on),
				'Appt/Class Date'				=> date('M', $value->BookDate).' '.date('d', $value->BookDate).' '.date('Y', $value->BookDate),
				'Total Amount'					=> "$".$value->procedure_cost,
				'Collected Amount'				=> "$".$collected_amount,
				'Medi-Credit'					=> $value->credit_cost,
				'Medicloud Transaction Fees'	=> $transaction_fees
			);
		}

		$excel = Excel::create($title, function($excel) use($container) {
				$excel->sheet('Sheetname', function($sheet) use($container) {
						$sheet->fromArray( $container );

				});
		})->export('xls');
	}

	/*
		NOTE: 
			- Applying MVC Structure
			- Other Terms for CLINIC is PROVIDER
	*/
	function getProvidersDetail(){
		try {
			// Get session
			$sessionHolder = StringHelper::getMainSession(3);
			return array(
				'data' => new ClinicDetail($sessionHolder->Ref_ID),
				'success' => true
			);
		} catch (Exception $error) {
			return array(
				'message' => $error,
				'success' => false
			);
		}

		
	}
	/*
		Payload Legend:
			*	providersDetails
			*	provider_id

			parent key in a array
				- providersDetails
			Child Keys
				- providersInfo
				- providersOperatingHours
				- providersBreakHours (for public holiday day value must be 'br')
	*/
	function updateProvidersDetail() {
		$payload = Input::all();

			if (!isset($payload['provider_id'])) {
				$getSessionData = StringHelper::getMainSession(3);
				$payload['provider_id'] = $getSessionData->Ref_ID;
			}
			
			$clinic  = new Clinic;
			// Update Providers info, operating hours and break hours.
			if (isset($payload['providersDetails'])) {
				if (isset($payload['providersDetails']['providersInfo'])) {
					// update providers info
					$clinic->updateClinicInfo($payload['providersDetails']['providersInfo'], $payload['provider_id']);
				}
				
				if (isset($payload['providersDetails']['providersOperatingHours'])) {
					// update providers operating hours
					$clinic->updateOperatingHours($payload['providersDetails']['providersOperatingHours'], $payload['provider_id']);
				}
				
				if (isset($payload['providersDetails']['providersBreakHours'])) {
					// update providers break hours
					$clinic->updateBreakHours($payload['providersDetails']['providersBreakHours'], $payload['provider_id']);
				}
					
					return array(
						'message' => 'Providers details successfully updated.',
						'success' => true
					);
			} else {
				return array(
					'message' => 'providersDetails is required.',
					'success' => false
				);
			}
	}

	function getProviderOperatingHours () {
		try {
			
			$clinic  = new Clinic;
			
			$getSessionData = StringHelper::getMainSession(3);
			
			$operatingHours = $clinic->getProviderOperatingHour($getSessionData->Ref_ID);

			return array(
				'data' => $operatingHours,
				'success' => true
			);

		} catch(Exception $error) {
			return array(
				'message' => $error,
				'success' => false
			);
		}
	}

	function getProviderBreakHours () {
		try {
			
			$clinic  = new Clinic;
			
			$getSessionData = StringHelper::getMainSession(3);
			
			$breakHours = $clinic->getProviderBreakHours($getSessionData->Ref_ID);

			return array(
				'data' => $breakHours,
				'success' => true
			);

		} catch(Exception $error) {
			return array(
				'message' => $error,
				'success' => false
			);
		}
	}
}
