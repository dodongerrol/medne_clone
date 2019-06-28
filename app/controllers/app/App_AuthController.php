<?php
use Illuminate\Support\Facades\Input;

class App_AuthController extends \BaseController {


        /* Use          :   Used to add user group (clinic, doctor and user)
         * Parameter    :   User details
         * Access       :
         * Return       :   User id
         */
        public static function AddNewUser($dataArray){
          $user = new User();
          if(is_array($dataArray) && count($dataArray)>0){
            $addUser = $user->addNewUser($dataArray);
            if($addUser){
              return $addUser;
            }else{
              return FALSE;
            }
          }
        }
        
        /* Use          :   Used to get user details
         * Parameter    :   User id
         * Access       :
         * Return       :   user details as array
         */
        public static function getUserDetails($userid){
          $user = new User();
          if(isset($userid)){
            $getUser = $user->getUserDetails($userid);
            if($getUser){
              return $getUser;
            }else{
              return FALSE;
            }
          }
        }

        /* Use          :   Used to complete doctor regitration
         * Parameter    :   Activation code
         * Access       :
         * Return       :   
         */
        public function CompleteDoctorRegistration(){
          $user = new User();
          $activateData = Input::get('activate');
          if(isset($activateData) && !empty($activateData)){
            $findUser = $user->findDoctorByActivationCode($activateData);
            if($findUser){
              $hostName = $_SERVER['HTTP_HOST'];
              $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
              $returnArray['server'] = $protocol.$hostName;
              $returnArray['date'] = new DateTime();
              $returnArray['userid'] = $findUser->UserID;
              $returnArray['email'] = $findUser->Email;
              $returnArray['title'] = "Medicloud Doctor sign up";
                    //$returnArray['email'] = $findUser->Email;
                    //$returnArray['email'] = $findUser->Email;
              $view = View::make('doctor.doctor-signup', $returnArray);
              return $view;
            }else{
              return Redirect::to('provider-portal-login');
            }
          }else{
            return Redirect::to('provider-portal-login');
          }

        }
        
        /* Use          :   Used to complete doctor sign up process
         * By           :   AJAX
         * 
         */
        public function MainSignUp(){
          $user = new User();
          $alldata = Input::all();
          if(is_array($alldata) && count($alldata)>0){
            $findUser = $this->getUserDetails($alldata['userid']);
            if($findUser){
              $updateArray['userid']=$findUser->UserID;
              $updateArray['Password'] = StringHelper::encode($alldata['password']);
              $updateArray['Status'] = 1;
              $updateArray['updated_at'] = time();
              $updatedUser = $user->updateUser($updateArray);
              if($updatedUser){
                Session::put('user-session', $findUser->UserID);
                return 1;
              }else{
                return 0;
              }
            }else{
              return 0;
            }   
          }else{
            return 0;
          }
        }

        /* Use          :   Used to login (clinic, doctor)
         * Parameter    :   
         * Access       :
         * Return       :   
         */
        
        public function MainLogin(){
          $returnArray['title'] = "Medicloud login";
          $hostName = $_SERVER['HTTP_HOST'];
          $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
          $returnArray['server'] = $protocol.$hostName;
          $returnArray['date'] = new DateTime();
            // echo substr($_SERVER["SERVER_PROTOCOL"],0,5).'/:';
          $getSessionData = StringHelper::getAuthSession();
          if($getSessionData != FALSE && count($getSessionData)> 0){
            if($getSessionData->UserType == 2 && ($getSessionData->Ref_ID != null || $getSessionData->Ref_ID != "")){
              return Redirect::to('app/doctor/dashboard');
            }elseif($getSessionData->UserType == 3 && ($getSessionData->Ref_ID != null || $getSessionData->Ref_ID != "")){
              $check = DB::table('clinic')->where('ClinicID', $getSessionData->Ref_ID)->first();
              if($check->configure == 1 || $check->configure == "1") {
                return Redirect::to('app/setting/claim-report');
              } else {
                return Redirect::to('app/clinic/appointment-home-view');
              }
                    //return Redirect::to('app/clinic/booking');
                    //return Redirect::to('app/clinic/settings-dashboard');
                    //return Redirect::to('app/clinic/clinic-details');
                    // return Redirect::to('app/clinic/appointment-home-view');

                    // return View::make('claim.report', $returnArray);
            }
          }else{
                // $view = View::make('auth.login', $returnArray);
                // return $view;
            return Redirect::to('provider-portal-login');
                // return View::make('hr_dashboard.login-clinic', $returnArray);
                // return View::make('claim.report', $returnArray);
          }
        }
        /* Use          :   Used to login (clinic, doctor)
         * Parameter    :   
         * Access       :
         * Return       :   
         * By           :   Ajax
         */
        
        // login from admin
        public function loginClinicAdmin( )
        {
          $user = new User();
          $token = Input::get('token');

          if(empty($token)) {
            return array('status' => false, 'message' => 'Login Token is required.');
          }

          $jwt = new JWT();
          $secret = Config::get('config.secret_key');

          try {
            $result = JWT::decode($token, $secret);
                    // $findUser = $user->checkLoginFromAdmin($email, $password);
            $findUser = $user->checkLoginFromAdminUserID($result->id);
            if($findUser){
              Session::put('user-session', $findUser->UserID);
              if($findUser->UserType == 3){
                $check = DB::table('clinic')->where('ClinicID', $findUser->Ref_ID)->first();

                if($check->configure == 1 || $check->configure == "1") {
                  return Redirect::to('app/setting/claim-report');
                } else {
                  return Redirect::to('app/clinic/appointment-home-view');
                }
              }
            }else{
              return array('status' => FALSE, 'message' => 'Something went wrong when loggin in from admin previledge.');
            }     
          } catch(Exception $e) {
            return array('result' => $e->getMessage());
            return array('status' => false, 'message' => 'Login Token is invalid.');
          }

        }

        public function ProcessLogin(){
          $user = new User();
          $returnArray['title'] = "Medicloud login";
          $email = Input::get('email');
          $password = Input::get('password');

          if(!empty($email) && !empty($password)){
            $findUser = $user->checkLogin($email,$password);
            if($findUser){
              Session::put('user-session', $findUser->UserID);
              $admin_logs = array(
                'admin_id'  => $findUser->UserID,
                'admin_type' => 'clinic',
                'type'      => 'admin_clinic_login_portal',
                'data'      => SystemLogLibrary::serializeData(array('email' => $email))
              );
              SystemLogLibrary::createAdminLog($admin_logs);
              if($findUser->UserType == 1){
                return 1;
              }elseif($findUser->UserType == 2){
                return 2;
              }elseif($findUser->UserType == 3){
                $check = DB::table('clinic')->where('ClinicID', $findUser->Ref_ID)->first();

                if($check->configure == 1 || $check->configure == "1") {
                  return 3;
                } else {
                  return 4;
                }
              }
            }else{
              return 0;
            }     
          }else{
            return 0;
          }

        }
        public function LogOutNow(){
          $admin_logs = array(
              'admin_id'  => Session::get('user-session'),
              'admin_type' => 'clinic',
              'type'      => 'admin_clinic_logout_portal',
              'data'      => SystemLogLibrary::serializeData(array('date' => date('Y-m-d H:i:s')))
          );
          SystemLogLibrary::createAdminLog($admin_logs);
          Session::forget('user-session');
          return Redirect::to('provider-portal-login');
        }
        
        public function ProcessForgotPassword(){
          $processForgotPassword = Auth_Library::ForgotPassword();
          return $processForgotPassword;



            /*
            $user = new User();
            $email = Input::get('email');
            if(!empty($email)){
                $findUser = $user->checkEmailExist($email);
                if($findUser){
                    //Generate reset Link
                    $updateArray['userid']=$findUser->UserID;
                    $updateArray['ResetLink'] = StringHelper::getEncryptValue();
                    $updateArray['Recon'] = 0;
                    $updateArray['updated_at'] = time();
                    $updatedUser = $user->updateUser($updateArray);
                    //send email to reset password
                    if($updatedUser){
                        $findNewUser = $this->getUserDetails($findUser->UserID);
                            $emailDdata['emailName']= $findNewUser->Name;
                            $emailDdata['emailPage']= 'email-templates.test';
                            $emailDdata['emailTo']= $findNewUser->Email;
                            $emailDdata['emailSubject'] = 'Reset your password';

                            $emailDdata['name'] = $findNewUser->Name;
                            $emailDdata['email'] = $findNewUser->Email;
                            //$emailDdata['activelink'] = URL::to("app/auth/register?activate=".$doctorDetails->ActiveLink);
                            $emailDdata['activelink'] = "<p>Please click <a href='".URL::to('app/auth/password-reset?resetcode='.$findNewUser->ResetLink)."'> This Link</a> to reset your password</p>";
                            EmailHelper::sendEmail($emailDdata);
                           
                        return 1;    
                    }else{
                        return 0;
                    }                 
                }else{
                    return 0;
                }
            }else{
                return 0;
              }*/
            }
            public function ForgotPassword(){
              $hostName = $_SERVER['HTTP_HOST'];
              $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
              $returnArray['server'] = $protocol.$hostName;
              $returnArray['date'] = new DateTime();    
              $returnArray['title'] = "Medicloud forgot password";
              $view = View::make('auth.forgot-password', $returnArray);
              return $view;      
            }

            public function ResetPassword(){
              $hostName = $_SERVER['HTTP_HOST'];
              $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
              $returnArray['server'] = $protocol.$hostName;
              $returnArray['date'] = new DateTime();
              $user = new User();
              $resetCode = Input::get('token');
              if(!empty($resetCode)){
                $findUser = $user->findDoctorByResetCode($resetCode);
                //if(count($findUser) > 0){ 
                if($findUser){    
                  $returnArray['userid'] = $findUser->UserID;
                  $returnArray['title'] = "Reset Health Provider Password";
                  return View::make('auth.clinic-reset-password', $returnArray);
                    // $view = View::make('auth.reset-password', $returnArray);
                }else{
                  return Redirect::to('provider-portal-login');
                }
              }else{
                return Redirect::to('provider-portal-login');
              }   
            }
            public function ProcessResetPassword(){
              $user = new User();
              $userid = Input::get('userid');
              $oldpass = Input::get('oldpass');
              $newpass = Input::get('newpass');

           // if(!empty($userid) && !empty($oldpass) && !empty($newpass)){
              if(!empty($userid) && !empty($newpass)){ 
                $findUser = $user->getUserDetails($userid);
                $user_type = $findUser->UserType;

                if($findUser){               
                  $compnarePassword = StringHelper::encode($oldpass);
                    //if($compnarePassword == $findUser->Password){
                        //Update password
                  $updateArray['userid'] = $findUser->UserID;
                  $updateArray['Password'] = StringHelper::encode($newpass);
                  $updateArray['ResetLink'] = null;
                  $updateArray['updated_at'] = time();
                  $updatedUser = $user->updateUser($updateArray);
                  if($updatedUser){
                    return array('status' => TRUE, 'message' => 'Successfully updated Password.');
                  }else{
                    return array('status' => FALSE, 'message' => 'Failed to update Password.');
                  }
                    //}else{
                    //    return 2;
                    //}
                }else{
                 return array('status' => FALSE, 'message' => 'User not found.');
               }
             }else{
              return array('status' => FALSE, 'message' => 'ID not found.');
            }      
          }
        
        /* Use          :   Used to find user by email 
         * Access       :   No public access is allowed
         * Parameter    :   Email
         */
        public function findUserEmail($email){
          $user = new User();
          if(!empty($email)){
            $finduserid = $user->checkEmail($email);
            if($finduserid){
              return $finduserid;
            }else{
              return FALSE;
            }
          }else{
            return FALSE;
          }      
        }
        
        
        public function GetReports(){
          $appointment = new UserAppoinment();
          $returnArray['title'] = "This is clinic main statistic";
          $hostName = $_SERVER['HTTP_HOST'];
          $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
          $returnArray['server'] = $protocol.$hostName;
          $returnArray['date'] = new DateTime();
          $findAppointments = $appointment->FindClinicAppointment();
          foreach($findAppointments as $findApp){
            $totalCanceled = $appointment->FindClinicAppointmentChanges($findApp->ClinicID,3);
            $totalConcluded = $appointment->FindClinicAppointmentChanges($findApp->ClinicID,2);
            $appArray['doctorslot'] = $findApp->DoctorSlotID;
            $appArray['clinicid'] = $findApp->ClinicID;
            $appArray['clinicname'] = $findApp->Name;
            $appArray['address'] = $findApp->Address;
            $appArray['totalbooking'] = $findApp->total;
            $appArray['totalcanceled'] = $totalCanceled;
            $appArray['totalconcluded'] = $totalConcluded;
            $totalArray[] = $appArray;

          }
          $returnArray['clinics'] = $totalArray;
          $view = View::make('auth.base-report', $returnArray);
          return $view; 
        }
        
        public function FindNricUser(){
          $findUser = Auth_Library::FindNricUser();
          if($findUser){
            return $findUser;
          }else{
            return 0;
          }      
        }


    // =================== Testing Area ======================

    // This is for testing
    /*public function FileUpload(){
        echo $uri = public_path('assets/upload/doctor/');
        $returnArray['title'] = "Medicloud application";
        $view = View::make('doctor.upload', $returnArray);
        return $view;
    }
    public function Upload(){
        $imageResize = Image_Library::ImageResizing(1,100);
        if($imageResize){
            echo 'Uploaded';
        }else{
            echo 'Failed';
        }    
      }*/



    /*public function TestShowFileUpload(){
        //echo $uri = public_path('assets/upload/doctor/');
        $returnArray['img'] = 0;
        $returnArray['title'] = "Medicloud application";
        $view = View::make('auth.test_upload', $returnArray);
        return $view;
    }
    public function TestUpload(){
        //echo 'Hi';
        $imageUpload = Image_Library::CloudinaryUpload();
        //echo '<pre>'; print_r($imageUpload); echo '</pre>';
        $returnArray['img'] = 1;
        $returnArray['image'] =$imageUpload;
        $view = View::make('auth.test_upload', $returnArray);
        return $view;
      }*/


    // nhr/////////////////////////////////////////2016/5/6

      public function newClinic()
      {   
        $data['title'] = 'Create New Clinic';
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
        return View::make('auth.create_new_clinic',$data);
      }


    //============================= Code End =================================//


        /*public function getmenow(){
            Auth_Library::Showme();
        }
        
        public function sendOTP(){
            $mobile = '+94712432312';
            $otpcode = 555555;
            $otpsmsSent = StringHelper::TestSendOTPSMS($mobile,$otpcode); 
          }*/






	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//echo "create";
    $users = User::all();

    return View::make('auth/test')->with('users', $users);
  }


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		echo "stores";
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
        /*public function sendEmail(){
           $data['emailName']='Rizvi';
            $data['emailPage']='email-templates.test';
            $data['emailTo']='rizvimweb@gmail.com';
            $data['emailSubject']='Welcome to the Medicloud';
            
            $data['name'] = 'rizvi';
            $data['email'] = 'rizvimweb@gmail.com';
            $data['password'] = '123456';
            $data['activelink'] = "<p>Please click <a href='http://localhost:81/medicloud_web/public/app/auth/register?value=44dkdslkff'> This Link</a>to complete your registration</p>";
            $send = EmailHelper::sendEmail($data);
                echo 'email sent';
              }*/


            }
