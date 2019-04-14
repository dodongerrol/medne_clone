<?php

class QRCodeController extends \BaseController {

	public function viewQRcodes( )
	{
		$getSessionData = StringHelper::getMainSession(3);
    if($getSessionData != FALSE){
      $hostName = $_SERVER['HTTP_HOST'];
      $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
      $dataArray['check_in'] = $protocol.$hostName.'/app/check_in/view/'.$getSessionData->Ref_ID;
      // $dataArray['payment'] = $protocol.$hostName.'/v1/clinic/details/'.$getSessionData->Ref_ID;
      $dataArray['server'] = $protocol.$hostName.'/v1/clinic/details/'.$getSessionData->Ref_ID;
      $dataArray['clinic'] = DB::table('clinic')->where('ClinicID', $getSessionData->Ref_ID)->first();
      $dataArray['title'] = "QR Code Page";
      return View::make('settings.qr_code.qr-codes-view', $dataArray);
    }else{
      return Redirect::to('provider-portal-login');
    }
	}

	public function viewBigCheckInQR( )
	{
		$getSessionData = StringHelper::getMainSession(3);
    if($getSessionData != FALSE){
      $hostName = $_SERVER['HTTP_HOST'];
      $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
      $dataArray['server'] = $protocol.$hostName.'/app/check_in/view/'.$getSessionData->Ref_ID;
      $dataArray['title'] = "Check In QR Code Page";
      return View::make('settings.qr_code.clinic-checkin', $dataArray);
    }else{
      return Redirect::to('provider-portal-login');
    }
	}

	public function viewBigPaymentQR( )
	{
		$getSessionData = StringHelper::getMainSession(3);
    if($getSessionData != FALSE){
      $hostName = $_SERVER['HTTP_HOST'];
      $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
      $dataArray['server'] = $protocol.$hostName.'/v1/clinic/details/'.$getSessionData->Ref_ID;
      $dataArray['title'] = "Check In QR Code Page";
      $dataArray['clinic'] = DB::table('clinic')->where('ClinicID', $getSessionData->Ref_ID)->first();
      return View::make('settings.qr_code.clinic-payment', $dataArray);
    }else{
      return Redirect::to('provider-portal-login');
    }
	}

	public function checkInView($id)
	{
		$user_id = 1;
		$clinic_id = $id;

		$data['user'] = DB::table('user')->where('UserID', $user_id)->first();
		$data['clinic'] = DB::table('clinic')->where('ClinicID', $clinic_id)->first();
		$data['procedures'] = DB::table('clinic_procedure')->where('ClinicID', $clinic_id)->get();
		return View::make('settings.qr_code.check-in-view', $data);
	}

  public function paymentView($id)
  {
    // $user_id = 1;
    $clinic_id = $id;
    $returnObject = new stdClass();
    // $data['user'] = DB::table('user')->where('UserID', $user_id)->first();
    $clinic = DB::table('clinic')->where('ClinicID', $clinic_id)->first();
    if($clinic) {
      // $returnObject->status = TRUE;
      // $returnObject->data['clinic'] = $data['clinic'];
      // $returnObject->data['procedures'] = DB::table('clinic_procedure')->where('ClinicID', $clinic_id)->get();;
      $hostName = $_SERVER['HTTP_HOST'];
      $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
      $dataArray['server'] = $protocol.$hostName.'/v1/clinic/details/'.$clinic_id;
      $dataArray['title'] = "Check In QR Code Page";
      $dataArray['clinic'] = $clinic;
      return View::make('settings.qr_code.clinic-payment', $dataArray);
    } else {
      $returnObject->status = FALSE;
      $returnObject->message = 'Clinic does not exist.';
    }
    return Response::json($returnObject);
    // return View::make('settings.qr_code.payment-view', $data);
  }

}
