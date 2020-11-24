<?php
use Illuminate\Support\Facades\Input;
class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
 public function __construct(){

 }

 public function firstTimeLogin( )
 {

  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['title'] = 'First time login';
  $now = new \DateTime();
  $data['date'] = $now;
  return View::make('first_time_login.index', $data);
}

 public function testEmailSend( )
 {

  $data['to'] = 'jeamar1234@gmail.com';
  // $data['to'] = 'info@medicloud.sg';
  // $data['to'] = 'shielamaealvarez@ymail.com';
  $data['subject'] = "Email Test";
  $data['credits'] = 1;
  $data['transaction_id'] = 1;
  $data['transaction_date'] = '10 April 2020, 11:11am ';
  $data['health_provider_name'] = 'Medicloud Family Clinic Medicloud Family Clinic';
  $data['health_provider_address'] = '7 Temasek Boulevard #18-02';
  $data['health_provider_city'] = 'Suntec Tower One,';
  $data['health_provider_country'] = 'SG 038987 ';
  $data['health_provider_phone'] = '+65 6254 7889';
  $data['member'] = 1;
  $data['consultation'] = 1;
  $data['total_amount'] = 1;
  $data['nric'] = "Email Test";
  $data['clinic_type_image'] = "Email Test";
  $data['service'] = "Email Test";
  $data['lite_plan_status'] = true;
  $data['lite_plan_enabled'] = 1;
  $data['consultation'] = 1;
  $data['total_amount'] = 1;
  $data['transaction_type'] = 1;
  $data['currency_symbol'] = 'SGD';
  $data['total_credits'] = 1;
  $data['health_provider_postal'] = 1;
  $data['cap_per_visit_status'] = true;
  $data['cap_per_visit'] = 1;
  $data['paid_by_credits'] = 1;
  $data['paid_by_cash'] = 1;
  $data['bill_amount'] = 1;

  // return View::make('pdf-download/pdf-member-successful-transaction', $data);
  // return View::make('email-templates/email-member-successful-transaction', $data);

  $pdf = PDF::loadView('pdf-download.globalTemplates.plan-invoice', $data);
  $pdf->getDomPDF()->get_option('enable_html5_parser');
  $pdf->setPaper('A4', 'portrait');
  return $pdf->stream();
  
  // return $pdf->render();
  // return $pdf->download('sample.pdf');

  // return Mail::send('email-templates.email-member-successful-transaction', $data, function($message) use ($data){
  //   $pdf = PDF::loadView('pdf-download.pdf-member-successful-transaction', $data);
  //   $pdf->getDomPDF()->get_option('enable_html5_parser');
  //   $pdf->setPaper('A4', 'portrait');
  //   $message->from('noreply@medicloud.sg', 'MediCloud');
  //   $message->to($data['to'], 'MediCloud');
  //   $message->subject($data['subject']);
  //   $message->attachData($pdf->output(), 'sample-attach.pdf');
  // });
}

public function testEmailClinicPeak( )
{
  $data = array();
  $data['to'] = 'allan.alzula.work@gmail.com';
  $data['subject'] = "Email Test Clinic Peak";
  $data['clinic_name'] = 'Allan Test';
  $data['clinic_id'] = 4332;
  $data['transaction_id'] = 200;
  $data['name'] = 'Allan Alzula';
  $data['date_of_transaction'] = '2018-08-08';
  $data['peak_hour_amount'] = '190.00';
  return Mail::send('email-templates.clinic-peak-hours', $data, function($message) use ($data){
    $message->from('noreply@medicloud.sg', 'MediCloud');
    $message->to($data['to'], 'MediCloud');
    $message->subject($data['subject']);
  });

}
public function getPusherConfig( )
{
  return PusherHelper::getChannel( );
}
public function getNotificationConfig( )
{
  $config = Notification::config();
  return $config['app_id'];
}

public function getCompanyForgotPasswordView( )
{
  $input = Input::all();

  if(empty($input['token']) || $input['token'] == null) {
    return "token is required";
  }

  $result = DB::table('customer_hr_dashboard')->where('reset_link', $input['token'])->first();


  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['title'] = 'Reset Company Password';
  $now = new \DateTime();
  $data['date'] = $now;
  
  if(!$result) {
      // return "Reset Token is invalid";
    $data['token'] = false;
  } else {
    $data['hr_id'] = $result->hr_dashboard_id;
    $data['token'] = true;
  }
  

    // return $data;
  
  return View::make('hr_dashboard.forgot-password-company', $data);
}

public function getMemberForgotPasswordView( )
{
  $input = Input::all();
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['title'] = 'Reset Member Password';
  $now = new \DateTime();
  $data['date'] = $now;
  $check = DB::table('user')->where('ResetLink', $input['token'])->first();
  
  if($check) {
    $expire_token = false;
  } else {
    $expire_token = true;
  }

  $data['expire_token'] = $expire_token;
  $data['base_link'] = "/app/resetmemberpassword?token=".$input['token'];
    // return $data;
  return View::make('Eclaim.eclaim-forgot-password', $data);
}

public function getClinicForgotPasswordView( )
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['title'] = 'Reset Member Password';
  $now = new \DateTime();
  $data['date'] = $now;

  return View::make('auth.clinic-reset-password', $data);
}


public function transactionView( )
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['title'] = 'Claim Preview';
  $now = new \DateTime();
  $data['date'] = $now;
  return View::make('settings.payments.transaction-view-page', $data);
}


public function transactionDashboardView( )
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['title'] = 'Claim Preview';
  $now = new \DateTime();
  $data['date'] = $now;
  return View::make('dashboard.transaction-dashboard', $data);
}

public function viewPreviewMobileClaim($id)
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['title'] = 'Claim Preview';
  $now = new \DateTime();
  $data['date'] = $now;
  return View::make('dashboard.preview', $data);
}

public function userView( )
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $now = new \DateTime();
  $data['date'] = $now;
      // return $data;
  return View::make('user_view.index', $data);
}

public function sendSMS( )
{
  $phone = '+639265105102';
		// $phone = '+639269856938';
  $from = '+12015080436';
  $messages = 'Lorem ipsum dolor sit amet';
  return StringHelper::testSMS($phone, $messages);
}

public function getMembersPdf( ) {
  return View::make('members.members_coverage');
}
public function index( )
{

  return View::make('landing.home');
}

function healthProfessionals( )
{
  return View::make('landing.health-professionals');
}

function corporate( )
{
  return View::make('landing.corporate');
}

function privacy( )
{
  return View::make('landing.privacy-policy');
}

function promo( )
{
  return View::make('landing.promo');
}

function terms( )
{
  return View::make('landing.terms');
}

public function temp_index( ){
  return View::make('new_landing.index');
}

public function individual( ){
  return View::make('new_landing.individual');
}

public function health_partner( ){
  return View::make('new_landing.health-partner');
}

public function our_story( ){
  return View::make('new_landing.our-story');
}

public function get_mednefits( ){
  return View::make('new_landing.get-mednefits');
}

public function provider_terms( ){
  return View::make('new_landing.provider-terms');
}

public function user_terms( ){
  return View::make('new_landing.user-terms');
}

public function privacy_policy( ){
  return View::make('new_landing.privacy-policy');
}

public function insurance_license( ){
  return View::make('new_landing.insurance-license');
}

public function buy_insurance( ){
  return View::make('new_landing.buy-insurance');
}

public function bonus_credits( ){
  return View::make('new_landing.bonus-credits');
}

public function health_benefits( ){
  return View::make('new_landing.health-benefits');
}
public function mednefits_care_plan( ){
  return View::make('new_landing.mednefits-care-plan');
}

public function mednefits_employer( ){
  return View::make('new_landing.mednefits-employer');
}

public function how_it_works( ){
  return View::make('new_landing.how-it-works');
}

public function outpatient_care( ){
  return View::make('new_landing.outpatient-care');
}

public function hospital_care( ){
  return View::make('new_landing.hospital-care');
}

public function try_three_months( ){
  return View::make('new_landing.try-three-months');
}

public function our_health_partners( ){
  return View::make('new_landing.our-health-partners');
}

public function mednefits_care_bundle_corporate( ){
  return View::make('new_landing.mednefits-care-bundle-corporate-insurance');
}

public function introPageLogin( ){
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new \DateTime();
  return View::make('hr_dashboard.introduction', $data);
}

public function hrDashboard( ){
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new \DateTime();
  return View::make('hr_dashboard.index', $data);
}

public function oldhrDashboardLogin( ) {
  return Redirect::to('business-portal-login');
}

public function hrDashboardLogin( ) {
  return View::make('hr_dashboard.login-hr');
}
public function getCompanyActivationView( ) {
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new \DateTime();
  return View::make('hr_dashboard.activation-link', $data);
}

public function hrForgotPassword( ) {
  return View::make('hr_dashboard.forgot-password-hr');
}

public function clinicLogin( ) {
  return View::make('hr_dashboard.login-clinic');
}

public function quote( )
{

  return View::make('quote.index');
}

public function testEmail( )
{
  $data = array();
  $data['fname']   = 'allan';
  $data['lname']   = 'alzula';
  $data['company'] = 'none';
  $data['email']   = 'allan.alzula.work@gmail.com';
  $data['phone']   = 'none';
  $data['messages'] = 'hello, this is a test';
  Mail::later(5, 'email-templates.contact-medicloud-email',$data, function($message) use ($data)
  {
    $message->from('noreply@medicloud.sg', 'MediCloud');
    $message->to($data['email'], 'MediCloud');
    $message->subject('Welcome!');
  });
}

public function contactMedicloud( ) {
  $input = Input::all();
  $data = array();
  $data['fname']   = $input['fname'];
  $data['lname']   = $input['lname'];
  $data['company'] = $input['company'];
  $data['email']   = $input['email'];
  $data['phone']   = $input['phone'];
  $data['messages'] = $input['message'];

  $data['to'] = 'info@medicloud.sg';
  $data['subject'] = "Contact Me";

  return Mail::send('email-templates.contact-medicloud-email', $data, function($message) use ($data){
    $message->from('noreply@medicloud.sg', 'MediCloud');
    $message->to($data['to'], 'MediCloud');
    $message->subject($data['subject']);

  });
}

public function sendTryThreeMonths( )
{
  $input = Input::all();
  $data = array();
  $data['fname']   = ucwords($input['fname']);
  $data['lname']   = ucwords($input['lname']);
  $data['company'] = ucwords($input['company']);
  $data['email']   = $input['email'];
  $data['employees']   = $input['employee'];

  $data['to'] = 'happiness@mednefits.com';
      // $data['to'] = 'allan.alzula.work@gmail.com';
  $data['subject'] = "Mednefits Care (3 months free)";
  return Mail::queue('email-templates.try-three-months-email', $data, function($message) use ($data){
    $message->from('noreply@medicloud.sg', 'MediCloud');
    $message->to($data['to'], 'MediCloud');
    $message->subject($data['subject']);
  });
}

public function subscribeMedicloud( )
{
  $data = array();
  $data['to'] = "filbert@medicloud.sg";
  $data['subject'] = "Subscriber";
  $data['email'] = $_POST['email'];

  return Mail::send('email-templates.subscribe-medicloud-email', $data, function($message) use ($data){
    $message->from('noreply@medicloud.sg', 'MediCloud');
    $message->to($data['to'], 'MediCloud');
    $message->subject($data['subject']);

  });
}





public function test( ) {
 $user = new UserAppoinment( );
 return $user->findUserbyclinicID(14954002);;
}



public function showCalender()
{ 
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new DateTime();
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData){
    $sessionData = $getSessionData;
  }else{
    return Redirect::to('provider-portal-login');
  }

  $clinic = new Admin_Clinic();
  $clinics = $clinic->getClinicInfo($getSessionData->Ref_ID);
  
  // get operating hours
  Calendar_Library::addClinicManageTimeSlots($getSessionData->Ref_ID);
  $clinicTimes = General_Library::FindAllClinicTimesNew(3,$getSessionData->Ref_ID,strtotime(date('d-m-Y'))); 
  
        // return $clinics;
  	if ($clinics[0]->Name == "" || $clinics[0]->configure == 0) { //first time login

		// if($clinics[0]->configure == 0) {
     $clinic_type = new CalendarController();
     $clinic_type = $clinic_type->getWebClinicTypes($getSessionData->Ref_ID);
     $clinic_type = json_decode($clinic_type);

     $data['doctorlist'] = [];
     $data['doctorprocedurelist'] = [];

     $data['clinic_type'] = $clinic_type;
     $data['title'] = 'Calendar';
     $data['clinic_data'] = $clinics[0];
      // return $data;
			// return "true";
     return View::make('dashboard.first_time_calender',$data);
		// }


   }else{

    $doctors = new CalendarController();
    // $Procedure = new CalendarController();
    $doctors_lists = $doctors->getClinicDoctors($getSessionData->Ref_ID);
    
    if(sizeof($doctors_lists) > 0) {
      // $doctors_lists = json_decode($doctors_lists);
      $Procedures = $doctors->loadDoctorProcedures($getSessionData->Ref_ID, $doctors_lists[0]->DoctorID);
      if($Procedures) {
        $Procedures = json_decode($Procedures);
      } else {
        $Procedures = null;
      }
    } else {
      $doctors_lists = null;
      $Procedures = null;
    }

    $ua = new UserAppoinment();
    $ua_count = $ua->getClinicAppointments($getSessionData->Ref_ID);

    $data['title'] = 'Calendar';
    $data['doctorlist'] = $doctors_lists;
    $data['doctorprocedurelist'] = $Procedures;
    $data['clincID'] = $getSessionData->Ref_ID;
    $data['appCount'] = count($ua_count);

  		// return $data;
    return View::make('dashboard.calender',$data);


  }

}

public function summaryDashboard()
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new DateTime();
  // $data['title'] = 'Calendar';
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData){
    $sessionData = $getSessionData;
  }else{
    return Redirect::to('provider-portal-login');
  }
  // $doctors = new CalendarController();
  // $doctors = $doctors->getClinicDoctors($getSessionData->Ref_ID);
  // if($doctors) {
  //   $doctors = json_decode($doctors);
  //   $Procedure = new CalendarController();

  //   $Procedure = $Procedure->loadDoctorProcedures($getSessionData->Ref_ID,$doctors[0]->DoctorID);
  //   if($Procedure) {
  //     $Procedure = json_decode($Procedure);
  //   } else {
  //     $Procedure = null;
  //   }
  // } else {
  //   $Procedure = null;
  //   $doctors = null;
  // }


  $data['title'] = 'Dashboard';
  // $data['doctorlist'] = $doctors;
  // $data['doctorprocedurelist'] = $Procedure;
  $data['clincID'] = $getSessionData->Ref_ID;
  return View::make('dashboard.transaction-dashboard',$data);

}

public function needHelpPage()
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['title'] = 'Health Partner - Need Help';
  $now = new \DateTime();
  $data['date'] = $now;
  return View::make('dashboard.medni-tutorials',$data);

}



public function getEvents()
{


}

/* ----- Use to Display clinic details page -----*/

public function showMainCalendarSingleView(){
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new DateTime();
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){
                // print_r($getSessionData);
                // $clinicDetails = Clinic_Library::ClinicDetailsPage($getSessionData);

    $doctors = new CalendarController();
    $doctors_lists = $doctors->getClinicDoctors($getSessionData->Ref_ID);

    if(sizeOf($doctors_lists) > 0) {
      // $doctors_lists = json_decode($doctors_lists);
      $Procedure = new CalendarController();

      $Procedure = $Procedure->loadDoctorProcedures($getSessionData->Ref_ID, $doctors_lists[0]->DoctorID);
      $Procedure = json_decode($Procedure);
    } else {
      $Procedure = [];
      $doctors_lists = [];
    }

    $ua = new UserAppoinment();
    $ua_count = $ua->getClinicAppointments($getSessionData->Ref_ID);

    $data['title'] = 'Calendar';
    $data['doctorlist'] = $doctors_lists;
    $data['doctorprocedurelist'] = $Procedure;
    $data['clincID'] = $getSessionData->Ref_ID;
    $data['appCount'] = count($ua_count);
    
    $view = View::make('dashboard.calendar_single',$data);
    return $view;
                // return $clinicDetails;
  }else{

    return Redirect::to('provider-portal-login');
  }
}

/* ----- Use to Display clinic details page -----*/

public function showMainCalendarGroupView(){
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new DateTime();
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){

    $doctors = new CalendarController();
    $doctors = $doctors->getClinicDoctors($getSessionData->Ref_ID);
    $doctors = json_decode($doctors);

    $Procedure = new CalendarController();

    $Procedure = $Procedure->loadDoctorProcedures($getSessionData->Ref_ID,$doctors[0]->DoctorID);
    $Procedure = json_decode($Procedure);

    $ua = new UserAppoinment();
    $ua_count = $ua->getClinicAppointments($getSessionData->Ref_ID);

    $data['title'] = 'Calendar';
    $data['doctorlist'] = $doctors;
    $data['doctorprocedurelist'] = $Procedure;
    $data['clincID'] = $getSessionData->Ref_ID;
    $data['appCount'] = count($ua_count);

    $view = View::make('dashboard.calendar_group',$data);
    return $view;
  }else{

    return Redirect::to('provider-portal-login');
  }
}

/* ----- Use to Display clinic details page -----*/

public function MainSettingsPage(){
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $dataArray['server'] = $protocol.$hostName;
  $dataArray['date'] = new DateTime();
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){
            // print_r($getSessionData);
            // $clinicDetails = Clinic_Library::ClinicDetailsPage($getSessionData);
    $dataArray['title'] = "Settings Page";
    $view = View::make('settings.main',$dataArray);
    return $view;
            // return $clinicDetails;
  }else{

    return Redirect::to('provider-portal-login');
  }
}
public function getMobileExercise()
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $dataArray['server'] = $protocol.$hostName;
  $dataArray['date'] = new DateTime();
  $dataArray['title'] = "Update Details";
  return View::make('mobile-exercise.index', $dataArray);
}
public function claimReportPage()
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $dataArray['server'] = $protocol.$hostName;
  $dataArray['date'] = new DateTime();
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){
    $dataArray['title'] = "Claim Report Page";
    $dataArray['clincID'] = $getSessionData->Ref_ID;
    $clinic_details = DB::table('clinic')->where('ClinicID', $getSessionData->Ref_ID)->first();
    
    return View::make('clinic.claim', $dataArray);
  }else{

    return Redirect::to('provider-portal-login');
  }
}

public function claimReportPageApi() {
  $input = Input::all();
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){
    $transaction = new Transaction();
    return $transaction->getTransactionAppointments($getSessionData->Ref_ID, $input['book_date']);
  }else{

    return Redirect::to('provider-portal-login');
  }
}


    /////////////////////////load ajax pages///////////////////////////

public function ajaxGetAccountPage()
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new DateTime();
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){

   $clinic = new Admin_Clinic();
   $data['account'] = $clinic->getClinicdata($getSessionData->Ref_ID);
              	// dd($data['account']);

   return View::make('settings.account.main',$data);
 }else{
  return Redirect::to('provider-portal-login');
}
}


public function ajaxGetStaffPage()
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new DateTime();
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){

   $data['doctors'] = Clinic_Library::FindAllClinicDoctors($getSessionData->Ref_ID);
   $data['staff'] = Settings_Library::getClinicStaff($getSessionData->Ref_ID);

   return View::make('settings.staff.staff-main',$data);
 }else{
  return Redirect::to('provider-portal-login');
}

}


public function ajaxGetServicPage()
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new DateTime();
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){

    $data['services'] = Clinic_Library::FindClinicProcedures($getSessionData->Ref_ID);
    return View::make('settings.services.list',$data);

  }else{
    return Redirect::to('provider-portal-login');
  }
}

public function getServices( )
{
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){
    $data = [];
    $results = Clinic_Library::FindClinicProcedures($getSessionData->Ref_ID);
    foreach ($results as $key => $res) {
      $temp = array(
        'id' => $res->ProcedureID,
        'name' => ucwords($res->Name)
      );

      array_push($data, $temp);
    }

    return $data;
  }else{
    return Redirect::to('provider-portal-login');
  }
}


public function ajaxGetNotifyPage(){
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){
    return View::make('settings.notify.main');
  }else{
    return Redirect::to('provider-portal-login');
  }
}


public function ajaxGetProfilePage(){
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){
    return View::make('settings.profile.main');
  }else{
    return Redirect::to('provider-portal-login');
  }
}

public function ajaxGetPaymentPage(){
  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){
    return View::make('settings.payments.main');
  }else{
    return Redirect::to('provider-portal-login');
  }
}




// ============================================  config window functions  =======================================================



public function loadClinicDetails(){

  $getSessionData = StringHelper::getMainSession(3);
  $select = "";
  if($getSessionData != FALSE){

   Calendar_Library::addClinicManageTimeSlots($getSessionData->Ref_ID);
   $clinicTimes = General_Library::FindAllClinicTimesNew(3,$getSessionData->Ref_ID,strtotime(date('d-m-Y')));

  return $clinicTimes;

}else{
  return Redirect::to('provider-portal-login');
}
}

public function updateClinicWorkingHours(){

  $getSessionData = StringHelper::getMainSession(3);

  if($getSessionData != FALSE){

    Calendar_Library::updateClinicWorkingHours($getSessionData->Ref_ID);
  }else{
    return 0;
  }
}


public function loadDoctorDetails(){

  $getSessionData = StringHelper::getMainSession(3);

  if($getSessionData != FALSE){

   $clinicDoctors = new stdClass();
   $doctoravailability = new DoctorAvailability();
   $clinicDoctors = $doctoravailability->FindAllClinicDoctors($getSessionData->Ref_ID);

   if ($clinicDoctors) {

    $select = '<input id="doctors-count" type="hidden" value="1">
    <div id="div1" style="height: 250px; width: 475px; overflow-y: auto; overflow-x: hidden;">';

    foreach ($clinicDoctors as $value) {

     $select .=  '<div class="row col-md-12">
     <span class="col-md-1" style="padding-bottom: 5px; padding-left: 0px;">
     <img alt="" src="'.URL::asset('assets/images/ico_Profile.svg').'" width="40" height="40">
     </span>
     <div class="col-md-4" style="padding-left: 0px;">
     <label class="con-detail-lbl">'.$value->DocName.'</label>
     </div>
     <div class="col-md-4" style="padding-left: 0px;">
     <label class="con-detail-lbl">'.$value->DocEmail.'</label>
     </div>
     <span id="'.$value->DoctorID.'" class="col-md-1 text-center con-detail-lbl glyphicon glyphicon-remove" style="padding: 0;width: 12px; margin-top: 13px; cursor: pointer; margin-left: 50px;"></span>

     <hr style="clear: both; border-top: 1px solid #999999;">
     </div>' ;

   }

   $select .= '</div>';

 }
 else{

  $select = '<input id="doctors-count" type="hidden" value="0">
  <div class="alert alert-info" style="text-align: center; font-size: 14px; background: none !important; border: 0px; -webkit-box-shadow: none !important; margin: 0px;">
  Enter the name and email and Click Add !
  </div>';


}

    	// dd($select);

return $select;

}else{
  return Redirect::to('provider-portal-login');
}
}


public function addDoctorDetails(){

  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){
   $NewDoctor = Settings_Library::addDoctor($getSessionData->Ref_ID);
			// dd($NewDoctor);
   Calendar_Library::addDoctorManageTimes($getSessionData->Ref_ID, $NewDoctor);

 }else{
  return Redirect::to('provider-portal-login');
}
}


public function loadServiceDetails(){

  $getSessionData = StringHelper::getMainSession(3);
  if($getSessionData != FALSE){

   $data = Clinic_Library::FindClinicProcedures($getSessionData->Ref_ID);
   $data3 = Clinic_Library::FindAllClinicDoctors($getSessionData->Ref_ID);
			// dd($data);

   $doctorsList = '';

   foreach ($data3 as $value) {

    $doctorsList .= '<li><span><label><input id="'.$value->DoctorID.'" class="service-doc-list" type="checkbox" style="margin: 0px !important; cursor: pointer;">'.$value->DocName.'</label></span></li>';
  }



  if ($data) {

   $select = '<input id="service-count" type="hidden" value="1"><div style="height: 185px; width: 470px; overflow-y: auto; overflow-x: hidden;">';

   foreach ($data as $value) {

     $data2 = Calendar_Library::FindDoctorProcedures($value->ProcedureID,$getSessionData->Ref_ID);
     $docCount =0;
     $doctor_list = "";

     if ($data2){

       $docCount = count($data2);

					// dd($data2);

       foreach ($data2 as $value2) {

        $doctor_list .= $value2->DocName."\n";

      }
    }

				// dd($docCount);

    $select .=  '<div class="row col-md-12">
    <span class="col-md-4" style="padding-bottom: 5px; padding-left: 0px;">
    <label class="con-detail-lbl">'.$value->Name.'</label>
    </span>
    <div class="col-md-2">
    <label class="con-detail-lbl">'.$value->Duration.' '.$value->Duration_Format.'</label>
    </div>
    <div class="col-md-1">
    <label class="con-detail-lbl">'.$value->Price.'</label>
    </div>
    <div class="col-md-1">
    <a id="doc-tiptool" href="#" data-toggle="tooltip" title="'.$doctor_list.'">
    <span class="col-md-1 text-center con-detail-lbl glyphicon glyphicon-user" style="padding: 0;width: 12px; margin-top: 13px;"><span style="padding-left: 5px;">'.$docCount.'</span></span></a>

    </div>
    <span id="'.$value->ProcedureID.'" class="col-md-1 con-detail-lbl glyphicon glyphicon-remove" style="padding: 0;width: 12px; margin-top: 14px; cursor: pointer;"></span>
    </div>' ;

  }

  $select .=  '</div><div class="row col-md-12" style="margin-top: 15px;">

  <hr style="clear: both; border-top: 1px solid #999999; margin-top: 0px;">

  <div class="col-md-4" style="padding-left: 0px;">
  <input type="text" id="con-service-name" class="dropdown-btn " value="" placeholder="Service Name" style="height: 30px; width: 140px; font-size: 12px;">
  </div>
  <div class="col-md-1" style="padding-left: 0px; padding-right: 25px;">
  <input type="text" id="con-service-time" class="dropdown-btn " value="" placeholder="0" style="height: 35px; width: 38px; font-size: 12px;">
  </div>
  <div class="col-md-1" style="padding: 0px; padding-right: 5px;">
  <input type="button" id="con-time-format" class="dropdown-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" value="Mins" style="height: 35px; width: 30px; font-size: 12px; background: #ececec; border: 1px solid #999999; color: #333;">
  <ul id="time-format-list" class="dropdown-menu">
  <li><a href="#">Mins</a></li>
  <li><a href="#">Hours</a></li>
  </ul>
  </div>
  <div class="col-md-1">
  <input type="text" id="con-service-cost" class="dropdown-btn " value="" placeholder="$ 0" style="height: 35px; width: 40px; font-size: 12px;">
  </div>
  <div class="col-md-1">
  <a style="background: #ececec; border: 1px solid #999999; width: 20px; height: 25px; color: #333;" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  <i class="glyphicon glyphicon-user" style="padding-top: 3px;"></i>
  <span class="caret"></span>
  </a>

  <ul class="dropdown-menu" style="float: right; position: static; height: 135px; overflow-y: auto; overflow-x: hidden;">
  <li><span>Who can perform this service?</span></li>
  <li role="separator" class="divider"></li>'.$doctorsList.'
  </ul>

  </div>
  <button id="service-add-btn-config" class="config-doc-add-btn" style="font-size: 15px; margin-left: 0px; width: 33px;">Add</button>
  </div>';
} else {

  $select = '<input id="service-count" type="hidden" value="0">
  <div style="height: 185px; width: 470px; overflow-y: auto; overflow-x: hidden;">
  </div>

  <div class="row col-md-12" style="margin-top: 15px;">

  <hr style="clear: both; border-top: 1px solid #999999; margin-top: 0px;">

  <div class="col-md-4" style="padding-left: 0px;">
  <input type="text" id="con-service-name" class="dropdown-btn " value="" placeholder="Service Name" style="height: 30px; width: 140px; font-size: 12px;">
  </div>
  <div class="col-md-1" style="padding-left: 0px; padding-right: 25px;">
  <input type="text" id="con-service-time" class="dropdown-btn " value="" placeholder="0" style="height: 35px; width: 38px; font-size: 12px;">
  </div>
  <div class="col-md-1" style="padding: 0px; padding-right: 5px;">
  <input type="button" id="con-time-format" class="dropdown-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" value="Mins" style="height: 35px; width: 30px; font-size: 12px; background: #ececec; border: 1px solid #999999; color: #333;">
  <ul id="time-format-list" class="dropdown-menu">
  <li><a href="#">Mins</a></li>
  <li><a href="#">Hours</a></li>
  </ul>
  </div>
  <div class="col-md-1">
  <input type="text" id="con-service-cost" class="dropdown-btn " value="" placeholder="$ 0" style="height: 35px; width: 40px; font-size: 12px;">
  </div>
  <div class="col-md-1">
  <a style="background: #ececec; border: 1px solid #999999; width: 20px; height: 25px; color: #333;" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  <i class="glyphicon glyphicon-user" style="padding-top: 3px;"></i>
  <span class="caret"></span>
  </a>

  <ul class="dropdown-menu" style="float: right; position: static; height: 135px; overflow-y: auto; overflow-x: hidden;">
  <li><span>Who can perform this service?</span></li>
  <li role="separator" class="divider"></li>'.$doctorsList.'
  </ul>

  </div>
  <button id="service-add-btn-config" class="config-doc-add-btn" style="font-size: 15px; margin-left: 0px; width: 33px;">Add</button>
  </div>';

}

return $select;

}else{
  return Redirect::to('provider-portal-login');
}
}



public function saveClinicService(){

  $getSessionData = StringHelper::getMainSession(3);

  if($getSessionData != FALSE){

   $saveService = Calendar_Library::saveClinicService($getSessionData->Ref_ID);

   Calendar_Library::addDoctorService($getSessionData->Ref_ID,$saveService->id);

 }else{
  return Redirect::to('provider-portal-login');
}
}

public function DeleteClinicService()
{
  $getSessionData = StringHelper::getMainSession(3);
  return Calendar_Library::DeleteClinicService($getSessionData->Ref_ID);


}


public function saveClinicDetails(){

  $getSessionData = StringHelper::getMainSession(3);

  if($getSessionData != FALSE){

   Calendar_Library::saveClinicDetails($getSessionData->Ref_ID);

 }else{
  return Redirect::to('provider-portal-login');
}
}



	// ----------- Update Clinic Default day ----------- //


public function updateClinicDetails(){

  $getSessionData = StringHelper::getMainSession(3);

  if($getSessionData != FALSE){

   Settings_Library::updateClinicDetails($getSessionData->Ref_ID);

 }else{
  return Redirect::to('provider-portal-login');
}
}





  // smsm platform nhr 2016-8-4

public function sendCustomSms()
{
 $getSessionData = StringHelper::getMainSession(3);

 if($getSessionData != FALSE){

   return Clinic_Library::sendCustomSms($getSessionData->Ref_ID);

 }else{
  return Redirect::to('provider-portal-login');
}
}

public function testConcludeEmail( )
{
  $dataArray = [];
  return Mail::send('email-templates.survey', $dataArray, function($message) use ($dataArray){
    $message->from('noreply@medicloud.sg', 'MediCloud');
    $message->to('allan.alzula.work@gmail.com', 'allan');
    $message->subject('test email');

  });
}

public function concludePage($id, $rate)
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new DateTime();
  $appointment = new UserAppoinment();
  $result = $appointment->getUserAppointmentDetails($id);
    // return var_dump($result);
  if($result) {
    $data['rating'] = $result;
    $data['rate'] = $rate;
    return View::make('email-templates.conclude-page', $data);
  } else {
    return View::make("errors.503");
  }

}

public function welcomePackCorporate( )
{
  return View::make("invoice.welcome-pack-corporate");
}
public function welcomePackIndividual($id)
{
  $account_start = new CorporateBuyStart();
  $account_start_result = $account_start->getAccountStart($id);

  if(!$account_start_result) {
    return View::make('errors.503');
  }

  $data = [];
  $data['id'] = $id;

  return View::make("invoice.welcome-pack-individual", $data);
}

public function getContract($id)
{
  $account_start = new CorporateBuyStart();
  $account_start_result = $account_start->getAccountStart($id);

  if(!$account_start_result) {
    return View::make('errors.503');
  }

  $data = [];
  $data['id'] = $id;
  return View::make("invoice.contract-page", $data);
}

public function saveClinicRating( )
{
  $input = Input::all();
  $rating = new ClinicRatings();
  $data = array(
    'user_id'           => $input['user_id'],
    'clinic_id'         => $input['clinic_id'],
    'rating'            => $input['rating'],
    'appointment_id'    => $input['appointment_id'],
    'feedback'          => $input['feedback']
  );
    // return $data;
  $result_check = $rating->checkClinicRating($input['appointment_id']);

  if($result_check > 0) {
    $result = $rating->updateClinicRating($input['appointment_id'], $data);
  } else {
    $result = $rating->createClinicRating($data);
  }

  if($result) {
   return array(
    'status'  => TRUE
  );
 }

 return array(
  'status'  => FALSE
);
}


public function testDebugJson( )
{
  $input = Input::all();
  return $input;
}

public function searchUser( )
{
  $input = Input::all();
  $data = [];
  
  $results = DB::table('user')
  ->where('PhoneNo', 'LIKE', '%'.(int)$input['q'].'%')
  ->where('UserType', 5)
  ->where('Active', 1)
  ->orderBy('UserID', 'desc')
  ->select('UserID as id', 'Name as name', 'PhoneNo as mobile', 'Image as image', 'Email as email', 'UserType as user_type', 'access_type', 'Active as status', 'DOB as dob')
  ->orderBy('UserID')
  ->get();
  
  foreach ($results as $key => $result) {
    if($result->dob) {
      $result->dob = date('d/m/Y', strtotime($result->dob));
    }
  }

  $data['number_of_results'] = sizeOf($results);
  $data['results'] = $results;

  return $data;
}


public function getAllUsers( )
{
  $input = Input::all();
  $data = [];
  $format = [];
  $getSessionData = StringHelper::getMainSession(3);
  $clinic_id = $getSessionData->Ref_ID;
  $results = DB::table('user')
  ->where('PhoneNo', 'LIKE', '%'.$input['q'].'%')
  ->where('Active', 1)
  ->orderBy('UserType', 'desc')
  ->select('UserID as id', 'Name as name', 'NRIC as nric', 'Image as image', 'Email as email', 'UserType as user_type', 'access_type')
  ->orderBy('UserID')
  ->get();
  
  foreach ($results as $key => $user) {
      // check block access
    $block = PlanHelper::checkCompanyBlockAccess($user->id, $clinic_id);

    if(!$block) {
      $user_id = StringHelper::getUserId($user->id);
      $customer_id = PlanHelper::getCustomerId($user_id);

      if($customer_id) {
        $info = DB::table('customer_business_information')->where('customer_buy_start_id', $customer_id)->first();
        if($info) {
          $user->company_name = ucwords($info->company_name);
        }
      }
      $format[] = $user;
    }
  }

    // return $results;
  $data['items'] = $format;
  $data['total_count'] = 1;
  $data['incomplete_results'] = false;

  return $data;
}

public function getAllSpecialUsers( )
{
  $input = Input::all();
  $data = [];
  $results = DB::table('user')
  ->where('NRIC', 'LIKE', '%'.$input['q'].'%')
  ->where('UserType', 5)
  ->where('Active', 1)
  ->select('UserID as id', 'Name as name', 'NRIC as nric', 'Image as image', 'Email as email', 'UserType as user_type', 'access_type')
  ->get();
  $data['items'] = $results;
  $data['total_count'] = sizeof($results);
  $data['incomplete_results'] = false;

  return $data;
}

public function gerServiceDetails($id)
{
  $result = DB::table('clinic_procedure')
  ->where('ProcedureID', $id)
  ->first();
  return $result->Name;
}

public function gerUserDetails($id)
{
  $result = DB::table('user')
  ->where('UserID', $id)
  ->get();
  return $result;
}

public function getCorporateName($id)
{
  $result = DB::table('corporate_members')
  ->join('corporate', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
  ->select('corporate.company_name')
  ->where('corporate_members.user_id', $id)
  ->first();
  if($result) {
    return ucwords($result->company_name);
  } else {
    return NULL;
  }
}

public function oldeClaim( )
{
  return Redirect::to("member-portal-login");
}

public function eClaimLogin( )
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new DateTime;
  return View::make('Eclaim.eclaim_login', $data);
}

public function eClaimHome( )
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new DateTime;
  return View::make('Eclaim.index', $data);
}

public function getSALandingPageView( )
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new DateTime;
  $data['path'] = app_path();
  return View::make('spendingAccountLandingPage.index', $data);
}
public function getEnquiryFormView( )
{
  $hostName = $_SERVER['HTTP_HOST'];
  $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $data['server'] = $protocol.$hostName;
  $data['date'] = new DateTime;
  $data['path'] = app_path();
  return View::make('spendingAccountLandingPage.enquiryform', $data);
}


public function getClinicSocketDetails( )
{
  $getSessionData = StringHelper::getMainSession(3);
  $clinic_id = $getSessionData->Ref_ID;
  $user_id = $getSessionData->UserID;
  $connection_pay_direct = StringHelper::socketConnection($clinic_id, $user_id);
  $connection_check_in = StringHelper::socketConnectionCheckIn($clinic_id, $user_id);
  $connection_check_in_remove = StringHelper::socketConnectionCheckInRemove($clinic_id, $user_id);

  return array('status' => TRUE, 'socket_connection_pay_direct' => $connection_pay_direct, 'socket_connection_check_in' => $connection_check_in, 'connection_check_in_remove' => $connection_check_in_remove);
}

public function testSocketConnection( )
{

  $getSessionData = StringHelper::getMainSession(3);
  $clinic_id = $getSessionData->Ref_ID;
  $user_id = $getSessionData->UserID;
  $connection = StringHelper::socketConnection($clinic_id, $user_id);

  $data = array(
    'name'  => 'allan',
    'id'    => 1
  );

  return PusherHelper::sendNewClaimNotification($data, $connection);
}

public function testDeactivateUser( )
{
 $input = Input::all();

 if(empty($input['user_id']) || $input['user_id'] == null) {
  return array('status' => false, 'message' => 'User ID is required.');
}

$active = 0;

if($input['type'] == 1) {
  $active = 1;
} else {
  $active = 0;
}

DB::table('user')->where('UserID', $input['user_id'])->update(['Active' => $active]);
return array('status' => true);
}
}
