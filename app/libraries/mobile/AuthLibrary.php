<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;

class AuthLibrary{

  public static function Login(){
    $returnObject = new stdClass();
    $input = Input::all();
    $token = StringHelper::customLoginToken($input);
    if($token->status){
      $findUserID = self::FindUserFromToken($token->data['access_token']);
      $activePromoCode = General_Library::ActivePromoCode();
      $user = DB::table('user')->where('UserID', $findUserID)->where('Active', 1)->first();

      if(!$user) {
        $returnObject->status = FALSE;
        $returnObject->message = StringHelper::errorMessage("Login");
        return $returnObject;
      }

      // if((int)$user->account_update_status == 0) {
      //   $returnObject->status = FALSE;
      //   $returnObject->error = 'update_credentials';
      //   $returnObject->url = url().'/app/update_user_id_web?platform=mobile';
      //   $returnObject->error_description = 'Please click here to change your user ID to your mobile number.';
      //   return $returnObject;
      // }

      $token->data['user_id'] = $findUserID;
      $returnObject->error= "false";
      $returnObject = $token;
      $returnObject->data['promocode'] = null;
      $admin_logs = array(
        'admin_id'  => $findUserID,
        'admin_type' => 'member',
        'type'      => 'admin_member_login_mobile',
        'data'      => SystemLogLibrary::serializeData($token)
      );
      SystemLogLibrary::createAdminLog($admin_logs);

    } else if($token->fields == FALSE) {
      $returnObject = $token;
    } else{
      $returnObject->status = FALSE;
      $returnObject->url = null;
      $returnObject->error = 'invalid_credentials';
            // $returnObject->error_description = 'The user credentials were incorrect.';
      $returnObject->error_description = 'Invalid Credentials';
    }
    return $returnObject;
  }

  public static function newLogin(){
    $returnObject = new stdClass();
    $input = Input::all();
    $token = StringHelper::newCustomLoginToken($input);
    $lang = isset($input['lang']) ? $input['lang'] : "en";
    if($token->status){
      $findUserID = self::FindUserFromToken($token->data['access_token']);
      // $activePromoCode = General_Library::ActivePromoCode();
      $user = DB::table('user')->where('UserID', $findUserID)->first();

      if(!$user) {
        $returnObject->status = FALSE;
        if($lang == "malay") {
          $returnObject->message = \MalayTranslation::malayMessages('user_not_exist');
        } else {
          $returnObject->message = StringHelper::errorMessage("Login");
        }
        return $returnObject;
      }

      // if((int)$user->account_update_status == 0) {
      //   $returnObject->status = FALSE;
      //   $returnObject->error = 'invalid_credentials';
      //   $returnObject->url = url().'/app/update_user_id_web?platform=mobile';
      //   $returnObject->error_description = 'Please click here to change your user ID to your mobile number.';
      //   return $returnObject;
      // }

      $token->data['user_id'] = $findUserID;
      $returnObject->error= "false";
      $returnObject = $token;
      $returnObject->data['promocode'] = null;
      $admin_logs = array(
        'admin_id'  => $findUserID,
        'admin_type' => 'member',
        'type'      => 'admin_member_login_mobile',
        'data'      => SystemLogLibrary::serializeData($token)
      );
      SystemLogLibrary::createAdminLog($admin_logs);

    } else if($token->fields == FALSE) {
      $returnObject = $token;
    } else{
      $returnObject->status = FALSE;
      $returnObject->url = null;
      $returnObject->error = 'invalid_credentials';

      if($lang == "malay") {
        $returnObject->message = \MalayTranslation::malayMessages('invalid_credentials');
        $returnObject->error_description = $returnObject->message;
      } else {
        $returnObject->message = 'Invalid Credentials';
        $returnObject->error_description = 'Invalid Credentials';
      }
    }
    return $returnObject;
  }

  public static function validToken(){
    $authController = new Api_V1_AuthController();
    $findUserID = $authController->returnValidToken();
    if($findUserID){
      return $findUserID;
    }
  }
  public static function SignUp(){
    $returnObject = new stdClass();
    $inputdata = Input::all();
    if(!empty($inputdata)){
      if(!empty($inputdata['phone'])) {
        $mobile = $inputdata['phone'];
      } else {
        $mobile = 0;
      }
      $dataArray['name']= $inputdata['full_name'];
      $dataArray['password']= StringHelper::encode($inputdata['password']);
      $dataArray['email']= $inputdata['email'];
      $dataArray['mobile']= $mobile;
      $dataArray['latitude']= $inputdata['latitude'];
      $dataArray['longitude']= $inputdata['longitude'];
      $dataArray['nric']= null;
      $dataArray['fin']= null;
      $dataArray['createdat']=time();
      $dataArray['active']=1;
      $dataArray['usertype']=1;
            $dataArray['source'] = 2;//mobile;
            // return $inputdata['email'];
            $userExist = self::EmailExist($inputdata['email']);
            if($userExist){
              $returnObject->status = FALSE;
              $returnObject->message = StringHelper::errorMessage("EmailDuplicate");
            }else{
              $userID = self::AddNewUser($dataArray);
              if($userID){
                $activePromoCode = General_Library::ActivePromoCode();
                $emailDdata['emailName']= $inputdata['full_name'];
                $emailDdata['emailPage']= 'email-templates.welcome';
                $emailDdata['emailTo']= $inputdata['email'];
                $emailDdata['pw']= $inputdata['password'];
                $emailDdata['emailSubject'] = 'Welcome to Mednefits';

                    // $emailDdata['activeLink'] = "<a href='".URL::to('provider-portal-login')."'> Find out more </a>";
                EmailHelper::sendEmail($emailDdata);

                $returnObject->status = TRUE;
                $returnObject->data['userid'] = $userID;
                    //if($activePromoCode){
                $returnObject->data['promocode']['status'] = 1;
                        //$returnObject->data['promocode']['message'] = "Welcome, we are very excited to have you onboard, you are entitled for a $15 Discount on your first booking via Medicloud, please enter the promo code '".$activePromoCode->Code."' upon confirmation of your appointment to claim your discount";
                $returnObject->data['promocode']['message'] = "We are very excited to have you onboard, select the clinic of your choice and start booking for your next appointment now!";
                    //}else{
                    //    $returnObject->data['promocode'] = null;
                    //}
                $wallet = new Wallet( );
                $data = array(
                  'UserID'        => $userID,
                  'balance'       => "0",
                  'created_at'    => Carbon::now(),
                  'updated_at'    => Carbon::now()
                );
                $wallet->createWallet($data);
              }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Register");
              }
            }
          }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmptyValues");
          }
          return $returnObject;
        }


        public static function SignUp_Old(){
          $user = new User();
          $userpolicy = new UserInsurancePolicy();
          $returnObject = new stdClass();
          $dataArray['name']= Input::get ('full_name');
          $dataArray['password']= StringHelper::encode(Input::get ('password'));
          $dataArray['email']= Input::get ('email');
          $dataArray['mobile']= Input::get('mobile_phone');
          $dataArray['insuranceid']= Input::get ('insurance_company');
          $dataArray['policyname']= Input::get ('insurance_policy_name');
          $dataArray['policyno']= Input::get ('insurance_policy_no');
          $dataArray['isprimary']= 1;
          $dataArray['latitude']= Input::get ('latitude');
          $dataArray['longitude']= Input::get ('longitude');
          $dataArray['nric']= Input::get ('nric');
          $dataArray['fin']= null;
          $dataArray['createdat']=time();
          $dataArray['active']=1;
          $dataArray['usertype']=1;

          $findUserEmail = $user->checkEmail(Input::get ('email'));
          if($findUserEmail){
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmailDuplicate");
          }else{
            $userID = $user->authSignup($dataArray);
            if($userID){
              $emailDdata['emailName']= Input::get ('full_name');
              $emailDdata['emailPage']= 'email-templates.welcome';
              $emailDdata['emailTo']= Input::get ('email');
              $emailDdata['emailSubject'] = 'Thank you for registering with us';

              $emailDdata['activeLink'] = "<a href='".URL::to('provider-portal-login')."'> Find out more </a>";
              EmailHelper::sendEmail($emailDdata);

              $dataArray['userid'] = $userID;
              $insertUserPolicy = $userpolicy->addInsurancePolicy($dataArray);
              $returnObject->status = TRUE;
              $returnObject->data['userid'] = $userID;
            }else{
              $returnObject->status = FALSE;
              $returnObject->message = StringHelper::errorMessage("Register");
            }
          }
          return $returnObject;
        }


        public static function ProfileUpdate($userid){
        //$user = new User();
          $insurancepolicy = new UserInsurancePolicy();
          $returnObject = new stdClass();
        //$allUpdatedata = Input::all();
          if(!empty($userid)){
            if(Input::get('full_name')) {
              $dataArray['Name'] = Input::get('full_name');
            }

            if(Input::get('email')) {
              $dataArray['Email'] = Input::get('email');
            }

            if(Input::get('nric')) {
              $dataArray['NRIC'] = Input::get('nric');
            }

            if(Input::get('mobile_phone')) {
              $dataArray['PhoneNo'] = Input::get('mobile_phone');
            }

            if(Input::get('dob')) {
              $newDate = date("d-m-Y", strtotime(Input::get('dob')));
              $dataArray['DOB'] = $newDate;
              $dataArray['Age'] = StringHelper::findAge($newDate);
            }

            if(Input::get('weight')) {
              $dataArray['Weight'] = Input::get('weight');
            }

            if(Input::get('height')) {
              $dataArray['Height'] = Input::get('height');
            }

            if(Input::get('bmi')) {
              $dataArray['Bmi'] = Input::get('bmi');
            }

            if(Input::get('blood_type')) {
              $dataArray['Blood_Type'] = Input::get('blood_type');
            }

            if(Input::file('file')){
                // $uploadFile = Image_Library::ImageResizing(1,100);
              $uploadFile = Image_Library::CloudinaryUploadFileWithResizer(Input::file('file'), 150, 150);
              if($uploadFile){
                $dataArray['Image'] = $uploadFile;
              }
            } 
            // else {
            //     $dataArray['Image'] = Input::get('photo_url');
            // }
            $dataArray['userid'] = $userid;
            $dataArray['updated_at'] = time();
            //$updateUserProfile = $user->updateUserProfile($dataArray);
            $updateUserProfile = self::UpdateUserProfile($dataArray);
            if($updateUserProfile){
              $returnObject->status = TRUE;
            }else{
              $returnObject->status = FALSE;
              $returnObject->message = StringHelper::errorMessage("Update");
            }
          }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("Update");
          }
          return $returnObject;



        /*
        $allUpdatedata = Input::all();
            $user = new User();
            $insurancepolicy = new UserInsurancePolicy();
            $returnObject = new stdClass();

            if(is_array($allUpdatedata) && count($allUpdatedata) >0 ){
                if(Input::get('full_name')) {
                    $dataArray['Name'] = Input::get('full_name');
                }if(Input::get('email')) {
                    $dataArray['Email'] = Input::get('email');
                }if(Input::get('nric')) {
                    $dataArray['NRIC'] = Input::get('nric');
                }if(Input::get('mobile_phone')) {
                    $dataArray['PhoneNo'] = Input::get('mobile_phone');
                }if(Input::get('dob')) {
                    $newDate = date("d-m-Y", strtotime(Input::get('dob')));
                    $dataArray['DOB'] = $newDate;
                    $dataArray['Age'] = StringHelper::findAge($newDate);
                }if(Input::get('weight')) {
                    $dataArray['Weight'] = Input::get('weight');
                }if(Input::get('height')) {
                    $dataArray['Height'] = Input::get('height');
                }if(Input::get('bmi')) {
                    $dataArray['Bmi'] = Input::get('bmi');
                }
                if(Input::get('insurance_id')) {
                    $insurance_exist = 1;
                    //$dataArrayPolicy['InsuaranceCompanyID'] = Input::get('insurance_company');
                    $dataArrayPolicy['InsuaranceCompanyID'] = Input::get('insurance_id');
                }if(Input::get('insurance_policy_no')) {
                    $insurance_exist = 1;
                    $dataArrayPolicy['PolicyNo'] = Input::get('insurance_policy_no');
                }
                $dataArrayPolicy['userid'] = Input::get('user_id');
                $dataArray['userid'] = Input::get('user_id');
                $dataArray['updated_at'] = time();

                $updateUserProfile = $user->updateUserProfile($dataArray);
                if($insurance_exist == 1){
                    $findInsurance = $insurancepolicy->getUserInsurancePolicyi(Input::get('user_id'));
                    if($findInsurance){
                        $policyStatus = $insurancepolicy->updateInsurancePolicy($dataArrayPolicy);
                    }else{
                        $policyStatus = $insurancepolicy->addInsurancePolicy($dataArrayPolicy);
                    }
                }

                if($updateUserProfile || $policyStatus){
                    $returnObject->status = TRUE;
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("Update");
                }
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("EmailEmpty");
            }
            return Response::json($returnObject);
         *
         */
          }

          public static function newProfileUpdate($userid){
        //$user = new User();
            $insurancepolicy = new UserInsurancePolicy();
            $returnObject = new stdClass();
            $input = Input::all();
            if(!empty($userid)){
            // validate iput

              if(Input::get('full_name')) {
                $dataArray['Name'] = Input::get('full_name');
              }

             //    if(Input::get('email')) {
             //       $user_check = DB::table('user')
             //       ->where('Email', Input::get('email'))
             //       ->where('UserType', 5)
             //       ->whereNotIn('userID', [$userid])
             //       ->where('Active', 1)
             //       ->first();
             //       if($user_check) {
             //         $returnObject->status = FALSE;
             //         $returnObject->message = StringHelper::errorMessage("EmailDuplicate");
             //         return $returnObject;
             //     }
             //     $dataArray['Email'] = Input::get('email');
             // }

              if(Input::get('nric')) {
              	// validate nric
               $validate_nric = StringHelper::validIdentification(Input::get('nric'));

               if(!$validate_nric) {
                $returnObject->status = FALSE;
                $returnObject->message = 'Invalid NRIC/FIN.';
                return $returnObject;
              }

              $dataArray['NRIC'] = Input::get('nric');
            }

            if(Input::get('mobile_phone')) {
                // check mobile phone
              $mobile = preg_replace('/\s+/', '', Input::get('mobile_phone'));
              $mobile = (int)$mobile;
              $check_mobile = DB::table('user')
                          ->where('PhoneNo', (string)$mobile)
                          ->whereNotIn('UserID', [$userid])
                          ->first();

              if($check_mobile) {
                $returnObject->status = FALSE;
                $returnObject->message = 'Mobile Number is already taken.';
                return $returnObject;
              }

              $dataArray['PhoneNo'] = (string)$mobile;
            }

            if(Input::get('dob')) {
             $validate_dob = StringHelper::validateDate(Input::get('dob'));
             if(!$validate_dob) {
              $returnObject->status = FALSE;
              $returnObject->message = 'Date of birth must be a date.';
              return $returnObject;
            }
            $newDate = date("d-m-Y", strtotime(Input::get('dob')));
            $dataArray['DOB'] = $newDate;
            $dataArray['Age'] = StringHelper::findAge($newDate);
          }

          if(Input::get('weight')) {
            $dataArray['Weight'] = Input::get('weight');
          }

          if(Input::get('height')) {
            $dataArray['Height'] = Input::get('height');
          }

          if(Input::get('bmi')) {
            $dataArray['Bmi'] = Input::get('bmi');
          }

          if(Input::get('blood_type')) {
            $dataArray['Blood_Type'] = Input::get('blood_type');
          }

          if(Input::file('file')){
                      // $uploadFile = Image_Library::ImageResizing(1,100);
            $uploadFile = Image_Library::CloudinaryUploadFileWithResizer(Input::file('file'), 150, 150);
            if($uploadFile){
              $dataArray['Image'] = $uploadFile;
            }
          } 
                  // else {
                  //     $dataArray['Image'] = Input::get('photo_url');
                  // }
          $dataArray['userid'] = $userid;
          $dataArray['updated_at'] = time();
                  //$updateUserProfile = $user->updateUserProfile($dataArray);
          $updateUserProfile = self::UpdateUserProfile($dataArray);
                  // if($updateUserProfile){
          $returnObject->status = TRUE;
          $returnObject->message = 'Profile updated successfully.';
                  // }else{
                  //     $returnObject->status = FALSE;
                  //     $returnObject->message = StringHelper::errorMessage("Update");
                  // }
        }else{
          $returnObject->status = FALSE;
          $returnObject->message = StringHelper::errorMessage("Update");
        }
        return $returnObject;
      }

      public static function FindUserIDByEmail($email){
        $user = new User();
        $findUserByEmail = $user->Forgot_Password_Mobile($email);
        if($findUserByEmail){
          return $findUserByEmail;
        }else{
          return FALSE;
        }
      }

      public static function pin($user_id, $pin)
      {
        $returnObject = new stdClass();


        // check if elligible to update user
        $eligible = StringHelper::checkEligibleFeature($user_id);
        if($eligible) {
          $digit = strlen($pin);
          if(is_numeric($pin)) {
            if( (int)$digit == 6) {
              $user = new User();
              $result = $user->pin($user_id, $pin);
              return $result;
            } else {
              $returnObject->status = FALSE;
              $returnObject->message = 'Pin must be 6 digits.';
            }
          } else {
            $returnObject->status = FALSE;
            $returnObject->message = 'Pin must be a number.';
          }

        } else {
          $returnObject->status = FALSE;
          $returnObject->message = 'Account Type is not permitted to update pin.';
        }


        return $returnObject;
      }

      public static function updatePin($user_id, $pin)
      {
        $returnObject = new stdClass();
        if(ceil(log10($pin)) == 6) {
          $user = new User();
          $result = $user->pin($user_id, $pin);
          return $result;
        } else {
          $returnObject->status = FALSE;
          $returnObject->message = 'Pin must be 6 digits';
        }

        return $returnObject;
      }

      public static function Delete_Token(){
        $AccessToken = new OauthAccessTokens();
        $getRequestHeader = StringHelper::requestHeader();
        //if($getRequestHeader['Authorization'] !=""){
        if(!empty($getRequestHeader['Authorization'])){
          $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
          if($getAccessToken){
            $deleteToken = $AccessToken->DeleteToken($getAccessToken->id);
            if($deleteToken){
              return TRUE;
            }else{
              return FALSE;
            }
          }
        }else{
          return FALSE;
        }
      }

      public static function Forgot_Password(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $server = $protocol.$hostName;
        // return $server;
        $email = Input::get ('email');
        $returnObject = new stdClass();
        if(!empty($email)){
          $findUserID = self::FindUserIDByEmail($email);
            // return $findUserID;
          if($findUserID){
            $returnObject->status = TRUE;
                //$returnObject->data['userid'] = $findUserID;
            $returnObject->message = "New password is on the way to your email, check your inbox.";
            $deleteToken = self::Delete_Token();
                // $password = StringHelper::get_random_password(8);
            $updateArray['userid'] = $findUserID;
            $updateArray['ResetLink'] = StringHelper::getEncryptValue();
                // $updateArray['Password'] = md5($password);
            $updateArray['Recon'] = 0;
            $updateArray['updated_at'] = time();
            $userUpdated = self::UpdateUserProfile($updateArray);
            if($userUpdated){
              $findNewUser = self::FindUserProfile($findUserID);

              if($findNewUser){

                $config = StringHelper::Deployment( );

                if($config == 1) {
                  $data = array(
                    'email'     => $findNewUser->Email,
                    'name'      => ucwords($findNewUser->Name),
                    'context'   => "Forgot your employee password?",
                    'activeLink'    => $server.'/app/resetmemberpassword?token='.$findNewUser->ResetLink
                  );
                  $url = "https://api.medicloud.sg/employees/reset_pass";

                            // $url = "http://localhost:3000/employees/reset_pass";
                  ApiHelper::resetPassword($data, $url);
                } else {
                  $emailDdata['emailName'] = $findNewUser->Name;
                            // $emailDdata['emailPage'] = 'email-templates.reset-password';
                  $emailDdata['emailPage'] = 'email-templates.latest-templates.global-reset-password-template';
                  $emailDdata['emailTo'] = $findNewUser->Email;
                  $emailDdata['emailSubject'] = 'Employee Password Reset';
                  $emailDdata['name'] = $findNewUser->Name;
                  $emailDdata['context'] = "Forgot your employee password?";
                            // $emailDdata['password'] = $password;
                            // $emailDdata['login_email'] = $findNewUser->Email;
                  $emailDdata['activeLink'] = $server.'/app/resetmemberpassword?token='.$findNewUser->ResetLink;
                  EmailHelper::sendEmail($emailDdata);
                }

              }
            }
          }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("Forgot");
          }
        }else{
          $returnObject->status = FALSE;
          $returnObject->message = 'Sorry, your email address has not been signed up with Mednefits';
        }
        return $returnObject;

      }

      public static function Forgot_PasswordV2(){
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $server = $protocol.$hostName;

        if($server == "https://mobileapi.medicloud.sg") {
          $server = "https://medicloud.sg";
        }
        
        $input = Input::all();
        $email = Input::get ('email');
        $send_type = !empty(Input::get ('send_type')) ? Input::get ('send_type') : "both";
        $lang = isset($input['lang']) ? $input['lang'] : "en";
        
        $returnObject = new stdClass();
        if(!empty($email)){
          $findUserID = null;

          $findUserEmail = DB::table('user')
                          ->where('Email', $email)
                          ->where('UserType', 5)
                          ->where('Active', 1)
                          ->whereIn('access_type', [1, 0])
                          ->get();
          if(sizeof($findUserEmail) > 0) {
            if(sizeof($findUserEmail) > 0) {
              $findUserID = $findUserEmail[0]->UserID;
            } else {
              $returnObject->status = false;
              if($lang == "malay") {
                $returnObject->message = \MalayTranslation::malayMessages('issue_reset_pass_email');
              } else {
                $returnObject->message = "Sorry, your email address has not been signed up with Mednefits";
              }
              return $returnObject;
            }
          } else {
            $email = $email;
            // phone number
            $findUserPhone = DB::table('user')
            ->where('PhoneNo', (string)$email)
            ->where('UserType', 5)
            ->where('Active', 1)
            ->whereIn('access_type', [1, 0])
            ->get();
            
            if(sizeof($findUserPhone) > 0) {
              if(sizeof($findUserPhone) > 0) {
                $findUserID = $findUserPhone[0]->UserID;
              } else {
                $returnObject->status = false;
                if($lang == "malay") {
                  $returnObject->message = \MalayTranslation::malayMessages('issue_reset_pass_email');
                } else {
                  $returnObject->message = "Sorry, your email address has not been signed up with Mednefits";
                }
                return $returnObject;
              }
            } else {
                // backup phone
              $findUserBackUpPhone = DB::table('user')
              ->where('backup_mobile', (string)$email)
              ->where('UserType', 5)
              ->where('Active', 1)
              ->whereIn('access_type', [1, 0])
              ->get();

              if(sizeof($findUserBackUpPhone) > 0) {
                if(sizeof($findUserBackUpPhone) == 1) {
                  $findUserID = $findUserBackUpPhone[0];
                } else {
                  $returnObject->status = false;
                  if($lang == "malay") {
                    $returnObject->message = \MalayTranslation::malayMessages('issue_reset_pass_mobile');
                  } else {
                    $returnObject->message = "Sorry, your phone has not been signed up with Mednefits";
                  }
                  return $returnObject;
                }
              } else {
                $returnObject->status = false;
                if($lang == "malay") {
                  $returnObject->message = \MalayTranslation::malayMessages('issue_reset_pass_mobile');
                } else {
                  $returnObject->message = "Sorry, your phone has not been signed up with Mednefits";
                }
                return $returnObject;
              }
            }
          }
          
          if($findUserID){
            $returnObject->status = TRUE;
            //$returnObject->data['userid'] = $findUserID;
            $returnObject->message = "New password is on the way to your email, check your inbox.";
            $deleteToken = self::Delete_Token();
            $user = DB::table('user')->where('UserID', $findUserID)->first();

            if($user->ResetLink) {
              $updateArray['ResetLink'] = $user->ResetLink;
            } else {
              $updateArray['ResetLink'] = StringHelper::getEncryptValue();
            }

            $updateArray['userid'] = $findUserID;
            $updateArray['Recon'] = 0;
            $updateArray['updated_at'] = date('Y-m-d H:i:s');
            $userUpdated = self::UpdateUserProfile($updateArray);
            $findNewUser = self::FindUserProfile($findUserID);

            if($findNewUser){
              // check type of communication type
              if($send_type == "email") {
                if($findNewUser->Email) {
                  $emailDdata['emailName'] = $findNewUser->Name;
                  $emailDdata['emailPage'] = 'email-templates.latest-templates.global-reset-password-template';
                  $emailDdata['emailTo'] = $findNewUser->Email;
                  $emailDdata['emailSubject'] = 'Employee Password Reset';
                  $emailDdata['name'] = $findNewUser->Name;
                  $emailDdata['context'] = "Forgot your employee password?";
                  $emailDdata['activeLink'] = $server.'/app/resetmemberpassword?token='.$findNewUser->ResetLink;
                  EmailHelper::sendEmail($emailDdata);
                  $returnObject->status = TRUE;
                  $returnObject->type = "email";
                  $returnObject->message = "We’ve sent an email to you with a link to reset your password";
                  return $returnObject;
                } else {
                  $returnObject->status = false;
                  if($lang == "malay") {
                    $returnObject->message = \MalayTranslation::malayMessages('issue_reset_pass_email');
                  } else {
                    $returnObject->message = "Sorry, your email address has not been signed up with Mednefits";
                  }
                  return $returnObject;
                }
             } else if($send_type == "sms"){
                 // check phone if valid then send else use email
                if($findNewUser->PhoneNo || $findNewUser->backup_mobile) {
                  if(!$findNewUser->PhoneNo) {
                    $findNewUser->PhoneNo = $findNewUser->backup_mobile;
                    $findNewUser->PhoneCode = $findNewUser->backup_mobile_area_code;
                  }
                  
                  // check and format phone number
                  $phone = SmsHelper::newformatNumber($findNewUser);

                  if($send_type == "sms" && $phone) {
                    $findNewUser->phone = $phone;
                    $findNewUser->server = $server;
                    $message = SmsHelper::formatForgotPasswordMessage($findNewUser);
                    // send messge
                    $compose = [];
                    $compose['phone'] = $phone;
                    $compose['message'] = $message;
                    $compose['sms_type'] = "LA";
                    SmsHelper::sendSms($compose);
                    $returnObject->status = TRUE;
                    $returnObject->type = "sms";
                    $returnObject->message = "We’ve sent an sms to you with a link to reset your password";
                    return $returnObject;
                  } else {
                    $returnObject->status = false;
                    if($lang == "malay") {
                      $returnObject->message = \MalayTranslation::malayMessages('issue_reset_pass_mobile');
                    } else {
                      $returnObject->message = "Sorry, your email address has not been signed up with Mednefits";
                    }
                    return $returnObject;
                  }
                }
             } else {
                if($findNewUser->Email) {
                  $emailDdata['emailName'] = $findNewUser->Name;
                  $emailDdata['emailPage'] = 'email-templates.latest-templates.global-reset-password-template';
                  $emailDdata['emailTo'] = $findNewUser->Email;
                  $emailDdata['emailSubject'] = 'Employee Password Reset';
                  $emailDdata['name'] = $findNewUser->Name;
                  $emailDdata['context'] = "Forgot your employee password?";
                  $emailDdata['activeLink'] = $server.'/app/resetmemberpassword?token='.$findNewUser->ResetLink;
                  EmailHelper::sendEmail($emailDdata);
                  $returnObject->status = TRUE;
                  $returnObject->type = "email";
                  $returnObject->message = "We’ve sent an email to you with a link to reset your password";
                  return $returnObject;
                } else {
                  if($findNewUser->PhoneNo || $findNewUser->backup_mobile) {
                    if(!$findNewUser->PhoneNo) {
                      $findNewUser->PhoneNo = $findNewUser->backup_mobile;
                      $findNewUser->PhoneCode = $findNewUser->backup_mobile_area_code;
                    }
                    
                    // check and format phone number
                    $phone = SmsHelper::newformatNumber($findNewUser);
  
                    if($send_type == "sms" && $phone) {
                      $findNewUser->phone = $phone;
                      $findNewUser->server = $server;
                      $message = SmsHelper::formatForgotPasswordMessage($findNewUser);
                      // send messge
                      $compose = [];
                      $compose['phone'] = $phone;
                      $compose['message'] = $message;
                      $compose['sms_type'] = "LA";
                      SmsHelper::sendSms($compose);
                      $returnObject->status = TRUE;
                      $returnObject->type = "sms";
                      $returnObject->message = "We’ve sent an sms to you with a link to reset your password";
                      return $returnObject;
                    } else {
                      $returnObject->status = false;
                      if($lang == "malay") {
                        $returnObject->message = \MalayTranslation::malayMessages('issue_reset_pass_email');
                      } else {
                        $returnObject->message = "Sorry, your email address has not been signed up with Mednefits";
                      }
                      return $returnObject;
                    }
                  }
                }
             }
             
            $returnObject->status = TRUE;
            $returnObject->type = "email";
            $returnObject->message = "We’ve sent an email or sms to you with a link to reset your password";
            return $returnObject;
          }
        }else{
          $returnObject->status = FALSE;
          $returnObject->message = StringHelper::errorMessage("Forgot");
        }
      }else{
        $returnObject->status = FALSE;
        $returnObject->message = StringHelper::errorMessage("EmptyValues");
      }
      return $returnObject;
    }

    public static function UpdateUserProfile($updateArray){
      $user = new User();
      $updatedUser = $user->updateUserProfile($updateArray);
      if($updatedUser){
        return TRUE;
      }else{
        return FALSE;
      }
    }

    public static function FindUserProfile($profileid){
      $user = new User();
      $findUserProfile = $user->getUserProfileMobile($profileid);
      if($findUserProfile){
        return $findUserProfile;
      }else{
        return FALSE;
      }
    }


    public static function ChangePassword($profileid){
      $allInputdata = Input::all();
      $returnObject = new stdClass();

    // get admin session from mednefits admin login
      $admin_id = Session::get('admin-session-id');

      if(!empty($allInputdata)){
        $findUser = self::FindUserProfile($profileid);
            // return $findUser;
        if($findUser){
          if($findUser->Password == StringHelper::encode($allInputdata['oldpassword'])){
            $updateArray['userid'] = $findUser->UserID;
            $updateArray['Password'] = StringHelper::encode($allInputdata['password']);
            $updateArray['updated_at'] = date('Y-m-d H:i:s');
            $updated = self::UpdateUserProfile($updateArray);
            if($updated){
              $allInputdata['user_id'] = $profileid;
              if($admin_id) {
                $admin_logs = array(
                  'admin_id'  => $admin_id,
                  'admin_type' => 'mednefits',
                  'type'      => 'admin_employee_reset_password',
                  'data'      => SystemLogLibrary::serializeData($allInputdata)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
              } else {
                $admin_logs = array(
                  'admin_id'  => $profileid,
                  'admin_type' => 'member',
                  'type'      => 'admin_employee_reset_password',
                  'data'      => SystemLogLibrary::serializeData($allInputdata)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
              }
              $returnObject->status = TRUE;
              $returnObject->web_message = 'Password updated successfully!';
            }else{
              $returnObject->status = FALSE;
              $returnObject->web_message = 'Cannot use Old Password to New Password!';
              $returnObject->message = StringHelper::errorMessage("Update");
            }
          }else{
            $returnObject->status = FALSE;
            $returnObject->web_message = 'Incorrect Old Password.';
            $returnObject->message = StringHelper::errorMessage("Tryagain");
          }
        }else{
          $returnObject->status = FALSE;
          $returnObject->message = StringHelper::errorMessage("Tryagain");
        }
      }else{
        $returnObject->status = FALSE;
        $returnObject->message = StringHelper::errorMessage("EmptyValues");
      }
      return $returnObject;
    }

    public static function AddDeviceToken($findUserID){
      $devicedetails = Input::all();
      $returnObject = new stdClass();
      if(!empty($devicedetails)){
        $findDeviceToken = self::FindDeviceToken($findUserID);
        if($findDeviceToken){
                //$updateArray['tokenid'] = $findDeviceToken->DeviceTokenID;
          $updateArray['Token'] = $devicedetails['device_token'];
          $updateArray['Device_Type'] = $devicedetails['device_type'];
          $updateArray['updated_at'] = time();
          $tokenUpdated = self::UpadeDeviceToken($updateArray,$findDeviceToken->DeviceTokenID);
          if($tokenUpdated){
            $returnObject->status = TRUE;
          }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("Tryagain");
          }
        }else{
          $dataArray['userid'] = $findUserID;
          $dataArray['device_token'] = $devicedetails['device_token'];
          $dataArray['device_type'] = $devicedetails['device_type'];
          $insertToken = self::InsertDeviceToken($dataArray);
          if($insertToken){
            $returnObject->status = TRUE;
          }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("Tryagain");
          }
        }
      }else{
        $returnObject->status = FALSE;
        $returnObject->message = StringHelper::errorMessage("EmptyValues");
      }
      return $returnObject;
    }

    public static function InsertDeviceToken($insertArray){
      $devicetoken = new DeviceToken();
      $insertid = $devicetoken->AddDeviceToken($insertArray);
      if($insertid){
        return $insertid;
      }else{
        return FALSE;
      }
    }
    public static function FindDeviceToken($profileid){
      if(!empty($profileid)){
        $devicetoken = new DeviceToken();
        $getDetails = $devicetoken->GetDeviceToken($profileid);
        if($getDetails){
          return $getDetails;
        }else{
          return FALSE;
        }
      }else{
        return FALSE;
      }
    }
    public static function UpadeDeviceToken($updateArray,$tokenid){
      if(!empty($updateArray)){
        $devicetoken = new DeviceToken();
        $updated = $devicetoken->UpdateDeviceToken($updateArray,$tokenid);
        if($updated){
          return TRUE;
        }else{
          return FALSE;
        }
      }else{
        return FALSE;
      }
    }

    public static function DisableProfile($userid){
      $returnObject = new stdClass();
      $returnObject->status = FALSE;
      $returnObject->message = StringHelper::errorMessage("Update");
        // $updateArray['userid'] = $userid;
        // $updateArray['Active'] = 0;
        // $profileBlock = self::UpdateUserProfile($updateArray);
        // if($profileBlock){
        //     $returnObject->status = TRUE;
        //     $returnObject->message = StringHelper::errorMessage("BlockProfile");
        // }else{
        //     $returnObject->status = FALSE;
        //     $returnObject->message = StringHelper::errorMessage("Update");
        // }
      return $returnObject;
    }

    public static function EmailExist($email){
      $user = new User();
      if(!empty($email)){
        $emailExist = $user->checkEmailMobile($email);
        if($emailExist){
          return TRUE;
        }else{
          return FALSE;
        }
      }else{
        return FALSE;
      }
    }
    public static function AddNewUser($inputArray){
      $user = new User();
      if(!empty($inputArray)){
        $userID = $user->authSignup($inputArray);
        if($userID){
          return $userID;
        }else{
          return FALSE;
        }
      }else{
        return FALSE;
      }
    }

    public static function FindUserFromToken($token){
      $accesstoken = new OauthAccessTokens();
      $findUserID = $accesstoken->FindUserID($token);
      if($findUserID){
        return $findUserID;
      }else{
        return FALSE;
      }
    }

    /* Use : Used to update provide while booking slot or queue
     * Access : Public
     *
     */
    public static function OTPProfileUpdate($userid){
      $returnObject = new stdClass();
      $mobileno = Input::get('mobile_phone');
      $promocode = Input::get('promo_code');
      $otpChallenge = StringHelper::OTPChallenge();
      if(Input::get('nric')) {
        $dataArray['updated_at'] = time();
        $dataArray['NRIC'] = Input::get('nric');
      }if(!empty($mobileno)) {
        $dataArray['updated_at'] = time();
        $dataArray['PhoneNo'] = $mobileno;
        $dataArray['OTPStatus'] = 0;
        $dataArray['OTPCode'] = $otpChallenge;
      }
      if(!empty($promocode)){
        $findPromoCode = General_Library::FindActivePromoCode($promocode);
        if($findPromoCode){
          $findUserPromoCode = General_Library::FindUserPromoCode($userid,$findPromoCode->PromoCodeID);
          if($findUserPromoCode){
                    //$returnObject->promostatus = FALSE;
            $promostatus = 2;
          }else{
            $promoArray['userid'] = $userid;
            $promoArray['clinicid'] = 0;
            $promoArray['promocodeid'] = $findPromoCode->PromoCodeID;
            $promoArray['promocode'] = $promocode;
            $userpromoid = General_Library::InsertUserPromoCode($promoArray);
            if($userpromoid){
                        //$returnObject->promostatus = TRUE;
                        //$returnObject->status = TRUE;
              $promostatus = 1;
            }else{
                        //$returnObject->promostatus = FALSE;
                        //$returnObject->status = FALSE;
                        //$returnObject->message = StringHelper::errorMessage("PromoError");
              $promostatus = 2;
            }
          }
        }else{
                //$returnObject->promostatus = FALSE;
                //$returnObject->status = FALSE;
                //$returnObject->message = StringHelper::errorMessage("PromoError");
          $promostatus = 2;
        }
      }else{
        $promostatus = 0;
      }
      $dataArray['userid'] = $userid;
        //$dataArray['updated_at'] = time();
      $updateUserProfile = self::UpdateUserProfile($dataArray);
      if($updateUserProfile){
            //Send OPT challenge
        if(!empty($mobileno)) {
          $returnObject = self::SendOTPSMS($mobileno,$otpChallenge);
          if($returnObject==True && $promostatus !=2){
            $returnObject->status = TRUE;
          }else{
                    //$returnObject = $returnObject;
            if($promostatus==2){
              $returnObject->status = FALSE;
              $returnObject->message = StringHelper::errorMessage("PromoError");
            }else{
              $returnObject->status = FALSE;
              $returnObject->message = StringHelper::errorMessage("OTPError");
            }
          }
        }else{
          if($promostatus != 2){
            $returnObject->status = TRUE;
          }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("PromoError");
          }
        }
      }else{
        if($promostatus==1){
          $returnObject->status = TRUE;
        }elseif($promostatus==2){
          $returnObject->status = FALSE;
          $returnObject->message = StringHelper::errorMessage("PromoError");
        }else{
          $returnObject->status = FALSE;
          $returnObject->message = StringHelper::errorMessage("Update");
        }
      }
      return $returnObject;
    }



    public static function OTPCodeValidate($userid){
      $returnObject = new stdClass();
      if(!empty(Input::get('otp_code'))) {
        $findOTP = self::FindOTPCode($userid,Input::get('otp_code'));
        if($findOTP){
          $updateArray['OTPStatus'] = 1;
          $updateArray['userid'] = $userid;
          $updateArray['updated_at'] = time();
          $updateUserProfile = self::UpdateUserProfile($updateArray);
          if($updateUserProfile){
            $returnObject->status = TRUE;
          }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("Update");
          }
        }else{
          $returnObject->status = FALSE;
          $returnObject->message = StringHelper::errorMessage("NoRecords");
        }
      }else{
        $returnObject->status = FALSE;
        $returnObject->message = StringHelper::errorMessage("EmptyValues");
      }
      return $returnObject;
    }

    public static function FindOTPCode($userid,$otpcode){
      $user = new User();
      if(!empty($userid) && !empty($otpcode)){
        $findotpcode = $user->FindOTPCode($userid,$otpcode);
        if($findotpcode){
          return $findotpcode;
        }else{
          return FALSE;
        }
      }else{
        return FALSE;
      }
    }
    public static function OTPCodeResend($userid){
      $returnObject = new stdClass();
      $otpChallenge = StringHelper::OTPChallenge();
      $updateArray['OTPStatus'] = 0;
      $updateArray['OTPCode'] = $otpChallenge;
      $updateArray['userid'] = $userid;
      $updateArray['updated_at'] = time();
      $updateUserProfile = self::UpdateUserProfile($updateArray);
      if($updateUserProfile){
        $findUserProfile = self::FindUserProfile($userid);
            //send otp again
        $returnObject = self::SendOTPSMS($findUserProfile->PhoneNo,$otpChallenge);
      }else{
        $returnObject->status = FALSE;
        $returnObject->message = StringHelper::errorMessage("Update");
      }
      return $returnObject;
    }
    /* Use : Send OTP SMS
     * Access : public
     * Parameter : mobile number and otp message
     */
    public static function SendOTPSMS($mobile,$otpcode){
      $returnObject = new stdClass();
      $otpsmsSent = StringHelper::SendOTPSMS($mobile,$otpcode);
      if($otpsmsSent){
        $returnObject->status = TRUE;
      }else{
        $returnObject->status = FALSE;
        $returnObject->message = StringHelper::errorMessage("OTPError");
      }
      return $returnObject;
    }

    public static function FindUserProfileByRefID($refid){
      $user = new User();
      $findUserProfile = $user->UserProfileByRef($refid);
      if($findUserProfile){
        return $findUserProfile;
      }else{
        return FALSE;
      }
    }
  }
