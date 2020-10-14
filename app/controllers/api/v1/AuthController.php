<?php

use Illuminate\Support\Facades\Input;
use League\OAuth2\Server\Util\KeyAlgorithm\DefaultAlgorithm;
use League\OAuth2\Server\Util\KeyAlgorithm\KeyAlgorithmInterface;
//use Symfony\Component\Security\Core\User\User;
class Api_V1_AuthController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	protected static $algorithm;

	public function __construct() {
        // $this->afterFilter("auth.headers");
	}

	public function index()
	{
		echo "index";
	}

	public function loadCloudinaryConfig( )
	{
        // // load cloudinary config
		\Cloudinary::config(array(
      "cloud_name" => "mednefits-com",
      "api_key" => "881921989926795",
      "api_secret" => "zNoFc7EHPMtafUEt0r8gxkv4V5U"
    ));
	}
	public function Login(){
            // return Input::all();
		$findLogin = AuthLibrary::Login();
		return Response::json($findLogin);
	}

  public function newLogin(){
            // return Input::all();
    $findLogin = AuthLibrary::newLogin();
    return Response::json($findLogin);
  }

        //Login by mobile app
	/*
        public function login_old(){
            $dataArray = array();
            $token = Authorizer::issueAccessToken();
            if($token){
                $dataArray['error']= "false";
                //$dataArray['status'] = TRUE;
                $dataArray['data'] = $token;
            }else{
                $dataArray['status'] = FALSE;
                $dataArray['message'] = StringHelper::errorMessage("Login");
            }

            return Response::json($dataArray);
          }*/

        /*  Access      :   Public
            Function    :   User sign up from mobile
            Parameter   :   User details
            Author      :   Rizvi
            Return      :   User id
            Updated     :
        */

    // one tap login
            public function oneTapLogin( )
            {
            	$input = Input::all();
            	$returnObject = new stdClass();

            	$user = DB::table('user')->where('UserType', 5)->where('UserID', $input['user_id'])->first();

            	if($user) {
            // create auth session
            		$session_data = array(
            			'client_id'             => $input['client_id'],
            			'owner_type'            => 'user',
            			'owner_id'              => $user->UserID,
            			'client_redirect_uri'   => NULL
            		);
            		$session_class = new OauthSessions( );
            		$session = $session_class->createSession($session_data);
            		if($session) {
                // $check_session = DB::table('oauth_access_tokens')->where('session_id', $session->)
            			$token_data = array(
            				'id'        => self::getAlgorithm()->generate(40),
            				'session_id'  => $session->id,
            				'expire_time' => time() + 72000
            			);
            			$token_class = new OauthAccessTokens( );
            			$token = $token_class->createToken($token_data);
            			$get_token = DB::table('oauth_access_tokens')->where('session_id', $token->session_id)->orderBy('created_at', 'desc')->first();
            			if($get_token) {
            				$returnObject->error= "false";
            				$returnObject->data['access_token'] = $get_token->id;
            				$returnObject->data['token_type'] = 'Bearer';
                    $returnObject->data['expires_in'] = 7200;
                    $returnObject->data['user_id'] = $user->UserID;
            				// $findRealAppointment = ClinicLibrary::FindRealAppointment($user->UserID);
            				// $activePromoCode = General_Library::ActivePromoCode();

                    $status = StringHelper::checkEligibleFeature($user->UserID);

                    if($status) {
                     if($user->pin_setup === 0) {
                      $returnObject->data['pin_setup'] = TRUE;
                    } else {
                      $returnObject->data['pin_setup'] = FALSE;
                    }
                  } else {
                   $returnObject->data['pin_setup'] = FALSE;
                 }

            				// if(!$findRealAppointment){
            				// 	$returnObject->data['promocode']['status'] = 1;
            				// 	$returnObject->data['promocode']['message'] = "We are very excited to have you onboard, select the clinic of your choice and start booking for your next appointment now!";
            				// }else{
                 $returnObject->data['promocode'] = null;
            				// }
               } else {
                $returnObject->error= "true";
                $returnObject->status = FALSE;
                $returnObject->message = 'Error on switching account';
              }
            } else {
             $returnObject->error= "true";
             $returnObject->status = FALSE;
             $returnObject->message = 'Error on switching account';
           }
         } else {
          $returnObject->error= "true";
          $returnObject->status = FALSE;
          $returnObject->message = 'Error on switching account';
        }

        return Response::json($returnObject);
        // return $returnObject;
      }

      public function getAlgorithm()
      {
       if (is_null(self::$algorithm)) {
        self::$algorithm = new DefaultAlgorithm();
      }

      return self::$algorithm;
    }

    public function Signup(){
     $returnObject = new stdClass();
     $returnObject = AuthLibrary::SignUp();

     return Response::json($returnObject);


   }

        /*  Access      :   Public
            Function    :   Update
            Parameter   :   Input values
            Author      :   Rizvi
            Return      :   Json (True / False)
            Updated     :
        */
            public function UpdateUserProfile(){
            	$returnObject = new stdClass();
            	$findUserID = AuthLibrary::validToken();
            	if($findUserID){
            		$returnObject = AuthLibrary::ProfileUpdate($findUserID);
            	}else{
            		$returnObject->status = FALSE;
            		$returnObject->message = StringHelper::errorMessage("Token");
            	}
            	return Response::json($returnObject);
            }

            public function newUpdateUserProfile(){
            	$returnObject = new stdClass();
            	$findUserID = AuthLibrary::validToken();
            	if($findUserID){
            		$returnObject = AuthLibrary::newProfileUpdate($findUserID);
            	}else{
            		$returnObject->status = FALSE;
            		$returnObject->message = StringHelper::errorMessage("Token");
            	}
            	return Response::json($returnObject);
            }


        // user pin
            public function pin(){
            	$returnObject = new stdClass();
            	$findUserID = AuthLibrary::validToken();
            	if($findUserID){
            		$input = Input::all();
            		$pin_data = $input['pin'];
            		$returnObject = AuthLibrary::pin($findUserID, $pin_data);
            	}else{
            		$returnObject->status = FALSE;
            		$returnObject->message = StringHelper::errorMessage("Token");
            	}
            	return Response::json($returnObject);
            }

            public function updatePin( )
            {
            	$returnObject = new stdClass();
            	$findUserID = AuthLibrary::validToken();
            	if($findUserID){
            		$input = Input::all();

                // check if eligible for

            		$pin_data = $input['pin'];
            		$returnObject = AuthLibrary::updatePin($findUserID, $pin_data);
            	}else{
            		$returnObject->status = FALSE;
            		$returnObject->message = StringHelper::errorMessage("Token");
            	}
            	return Response::json($returnObject);
            }

        //Use for forgot password by Mobile app
            public function Forgot_Password(){
            	$returnObject = new stdClass();
            	$returnObject = AuthLibrary::Forgot_Password();
            //$returnObject->status = FALSE;
            //$returnObject->message = StringHelper::errorMessage("EmptyValues");
            	return Response::json($returnObject);
            }

            public function Forgot_PasswordV2(){
              $returnObject = new stdClass();
              $returnObject = AuthLibrary::Forgot_PasswordV2();
            //$returnObject->status = FALSE;
            //$returnObject->message = StringHelper::errorMessage("EmptyValues");
              return Response::json($returnObject);
            }

        // get reset details
            public function ResetPasswordDetails( ) {
             $user = new User();
             $returnObject = new stdClass();
             $resetCode = Input::get('resetcode');
             if(!empty($resetCode)){
              $findUser = $user->findDoctorByResetCode($resetCode);
                // return var_dump($findUser);
              if($findUser){
               $returnObject->user_id = $findUser->UserID;
               $returnObject->status = TRUE;
             } else {
               $returnObject->status = FALSE;
             }
           } else {
            $returnObject->status = FALSE;
          }
          return Response::json($returnObject);
        }

        public function ProcessResetPassword(){
         $user = new User();
         $returnObject = new stdClass();
         $userid = Input::get('userid');
         $oldpass = Input::get('oldpass');
         $newpass = Input::get('newpass');
         if(!empty($userid) && !empty($newpass)){
          $findUser = $user->getUserDetails($userid);
                // return $findUser;
          if($findUser){
           $updateArray['userid']=$findUser->UserID;
           $updateArray['Password'] = StringHelper::encode($newpass);
           $updateArray['updated_at'] = time();
           $updateArray['ResetLink'] = null;
           $updatedUser = $user->updateUser($updateArray);
                    // if($updatedUser){
           $returnObject->status = TRUE;
           $returnObject->message = 'Password Successfully Updated!';
                    // }else{
                    //     $returnObject->message = 'Error occured white updating the password';
                    //     $returnObject->status = FALSE;
                    // }
         }else{
           $returnObject->message = 'User not found.';
           $returnObject->status = FALSE;
         }
       }else{
        $returnObject->message = 'Empty Values';
        $returnObject->status = FALSE;
      }
      return Response::json($returnObject);
    }

    public function newProcessResetPassword(){
      $user = new User();
      $userid = Input::get('userid');
                // $oldpass = Input::get('oldpass');
      $newpass = Input::get('newpass');

      if(!empty($userid) && !empty($newpass)){
        $findUser = $user->getUserDetails($userid);
        $user_type = $findUser->UserType;

        if($findUser){
                  // $compnarePassword = StringHelper::encode($oldpass);
                        //Update password
          $updateArray['userid'] = $findUser->UserID;
          $updateArray['Password'] = StringHelper::encode($newpass);
          $updateArray['ResetLink'] = null;
          $updateArray['updated_at'] = time();
          $updatedUser = $user->updateUser($updateArray);
          if($updatedUser){
                            // delete token
            $get_session = DB::table('oauth_sessions')->where('owner_id', $findUser->UserID)->orderBy('created_at', 'desc')->first();

            if($get_session) {
              DB::table('oauth_access_tokens')->where('session_id', $get_session->id)->delete();
            }

            return array('status' => TRUE, 'message' => 'Successfully updated Password.');
          }else{
            return array('status' => FALSE, 'message' => 'Failed to update Password.');
          }
        }else{
         return array('status' => FALSE, 'message' => 'User not found.');
       }
     }else{
      return array('status' => FALSE, 'message' => 'ID not found.');
    }
  }

        //Use for forgot password by Mobile app
  public function Check_Email(){
   $email = Input::get ('email');
   $returnArray = array();
   if($email ==""){
    $returnArray['status'] = FALSE;
    $returnArray['message'] = StringHelper::errorMessage("EmailEmpty");
               // echo json_encode("Sorry! empty value");
                //return Response::json("Sorry! empty value");
  }else{
    $user = new User();
    $findUserEmail = $user->checkEmailMobile($email);
    if($findUserEmail){
     $returnArray['status'] = TRUE;
     $returnArray['data']['userid'] = $findUserEmail;

   }else{
     $returnArray['status'] = FALSE;
     $returnArray['message'] = StringHelper::errorMessage("EmailExist");
   }

 }
 return Response::json($returnArray);
}


        // Used to logout the user
public function LogOut(){
 $AccessToken = new OauthAccessTokens();
 $returnObject = array();
 $getRequestHeader = StringHelper::requestHeader();
 if($getRequestHeader['Authorization']!=""){
  $token = $getRequestHeader['Authorization'];
  $getAccessToken = $AccessToken->FindToken($token);
  if($getAccessToken){
   $deleteToken = $AccessToken->DeleteToken($getAccessToken->id);
   if($deleteToken == TRUE){
    $returnObject['status'] = TRUE;
    $returnObject['data']['message'] = StringHelper::errorMessage("logout");
  }else{
    $returnObject['status'] = FALSE;
    $returnObject['message'] = StringHelper::errorMessage("logerror");
  }
                    //return Response::json($returnObject);
}
}else{
  $returnObject['status'] = FALSE;
  $returnObject['message'] = StringHelper::errorMessage("Token");
}
return Response::json($returnObject);
}


        /* Use          :   Used to build user profile page
         * Access       :   Public
         * Parameter    :   Token
         * Return       :   User details by array
         */

        public function UserProfile(){
        	$returnObject = new stdClass();
        	$getUserID = $this->returnValidToken();
            // return $getUserID;
        	if(!empty($getUserID)){
        		$returnObject = $this->GetUserProfileInformation($getUserID);
        	}else{
        		$returnObject->status = FALSE;
        		$returnObject->login_status = FALSE;
        		$returnObject->message = StringHelper::errorMessage("Token");
        	}
        	return Response::json($returnObject);


        /*    $AccessToken = new Api_V1_AccessTokenController();
            $getRequestHeader = StringHelper::requestHeader();
            $authSession = new OauthSessions();
            $returnObject = new stdClass();

            if($getRequestHeader['Authorization']==""){
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }else{
                $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
                if($getAccessToken){
                    $findUserID = $authSession->findUserID($getAccessToken->session_id);
                    if($findUserID){
                        $returnObject = $this->GetUserProfileInformation($findUserID);
                    }else{
                        $returnObject->status = FALSE;
                        $returnObject->message = StringHelper::errorMessage("Token");
                    }
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("Token");
                }
            }
            return Response::json($returnObject);

         */
          }

        /* Use      :   Used to return valid token
         * Access   :   No public access is allowed
         *
         */
        public function returnValidToken(){
        	$AccessToken = new Api_V1_AccessTokenController();
        	$authSession = new OauthSessions();
        	$getRequestHeader = StringHelper::requestHeader();
            // return $getRequestHeader['Authorization'];
        	if(!empty($getRequestHeader['Authorization'])){
        		$getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
                // return $getAccessToken->session_id;
        		if($getAccessToken){
        			$findUserID = $authSession->findUserID($getAccessToken->session_id);
                    // return $findUserID;
        			if($findUserID){
        				return $findUserID;
        			}
        		}
        	}
        }

        /* Use          :   Used to delete user allergy
         * By           :   Mobile
         * Parameter    :   value
         */

        public function DeleteUserAllergy(){
        	$userallergy = new UserAllergy();
        	$allergyid = Input::get ('value');
        	$returnObject = new stdClass();
        	if($allergyid=="" || $allergyid ==null){
        		$returnObject->status = FALSE;
        		$returnObject->message = StringHelper::errorMessage("EmailEmpty");
        	}else{
        		$getUserID = $this->returnValidToken();
        		$dataArray['allergyid'] = $allergyid;
        		$dataArray['Active'] = 0;
        		if(!empty($getUserID)){
        			$updateAllergy = $userallergy->updateUserAllergy($dataArray);
        			if($updateAllergy){
        				$returnObject->status = TRUE;
        			}else{
        				$returnObject->status = FALSE;
        				$returnObject->message = StringHelper::errorMessage("Update");
        			}
        		}else{
        			$returnObject->status = FALSE;
        			$returnObject->message = StringHelper::errorMessage("Token");
        		}
        	}
        	return Response::json($returnObject);
        }

        /* Use          :   Used to delete user medical condition
         * By           :   Mobile
         * Parameter    :   value
         */
        public function DeleteUserCondition(){
        	$usercondition = new UserCondition();
        	$conditionid = Input::get ('value');
        	$returnObject = new stdClass();
        	if($usercondition=="" || $usercondition ==null){
        		$returnObject->status = FALSE;
        		$returnObject->message = StringHelper::errorMessage("EmailEmpty");
        	}else{
        		$getUserID = $this->returnValidToken();
        		$dataArray['conditionid'] = $conditionid;
        		$dataArray['Active'] = 0;
        		if(!empty($getUserID)){
        			$deleteCondition = $usercondition->updateUserCondition($dataArray);
        			if($deleteCondition){
        				$returnObject->status = TRUE;
        			}else{
        				$returnObject->status = FALSE;
        				$returnObject->message = StringHelper::errorMessage("Update");
        			}
        		}else{
        			$returnObject->status = FALSE;
        			$returnObject->message = StringHelper::errorMessage("Token");
        		}
        	}
        	return Response::json($returnObject);
        }


        public function DeleteUserMedication(){
        	$usermedication = new UserMedication();
        	$medicationid = Input::get ('value');
        	$returnObject = new stdClass();
        	if($usermedication=="" || $usermedication ==null){
        		$returnObject->status = FALSE;
        		$returnObject->message = StringHelper::errorMessage("EmailEmpty");
        	}else{
        		$getUserID = $this->returnValidToken();
        		$dataArray['medicationid'] = $medicationid;
        		$dataArray['Active'] = 0;
        		if(!empty($getUserID)){
        			$deleteMedication = $usermedication->updateUserMedication($dataArray);
        			if($deleteMedication){
        				$returnObject->status = TRUE;
        			}else{
        				$returnObject->status = FALSE;
        				$returnObject->message = StringHelper::errorMessage("Update");
        			}
        		}else{
        			$returnObject->status = FALSE;
        			$returnObject->message = StringHelper::errorMessage("Token");
        		}
        	}
        	return Response::json($returnObject);
        }

        //Delete medical histroy
        //Parameter history id
        //Return json
        public function DeleteMedicalHistory(){
        	$medicalhistory = new UserMedicalHistory();
        	$historydetails = new UserMedicalHistoryDetail();

        	$historyid = Input::get ('value');
        	$returnObject = new stdClass();
        	if($historyid=="" || $historyid ==null){
        		$returnObject->status = FALSE;
        		$returnObject->message = StringHelper::errorMessage("EmailEmpty");
        	}else{
        		$getUserID = $this->returnValidToken();
        		if(!empty($getUserID)){
        			$dataArray['historyid'] = $historyid;
        			$dataArray['Active'] = 0;
        			$deletedHistory = $medicalhistory->updateMedicalHistory($dataArray);
        			if($deletedHistory){
                        //delete history details
        				$detailArray['historyid'] = $historyid;
        				$detailArray['Active'] = 0;

        				$deletedDetails = $historydetails->updateHistoryDetails($detailArray);
                        //if($deletedDetails){
        				$returnObject->status = TRUE;
                        //}
        			}else{
        				$returnObject->status = FALSE;
        				$returnObject->message = StringHelper::errorMessage("Update");
        			}
        		}else{
        			$returnObject->status = FALSE;
        			$returnObject->message = StringHelper::errorMessage("Token");
        		}
        	}
        	return Response::json($returnObject);
        }


        //No public direct access allowed
        public function GetUserProfile($profileid){
        	$user = new User();
        	if($profileid == "" || $profileid == null){
        		return FALSE;
        	}else{
        		$findUserProfile = $user->getUserProfileMobile($profileid);
        		return $findUserProfile;
        	}
        }

        //No public direct access allowed
        public function GetUserAllergies($profileid){
        	$userallergy = new UserAllergy();
        	if($profileid == "" || $profileid == null){
        		return FALSE;
        	}else{
        		$findUserAllergy = $userallergy->getUserAllergies($profileid);
        		return $findUserAllergy;
        	}
        }

        //No public direct access allowed
        public function GetUserMedications($profileid){
        	$usermedication = new UserMedication();
        	if($profileid == "" || $profileid == null){
        		return FALSE;
        	}else{
        		$findUserMedication = $usermedication->getUserMedications($profileid);
        		return $findUserMedication;
        	}
        }
        //No public direct access allowed
        public function GetUserConditions($profileid){
        	$usercondition = new UserCondition();
        	if($profileid == "" || $profileid == null){
        		return FALSE;
        	}else{
        		$findUserCondition = $usercondition->getUserConditions($profileid);
        		return $findUserCondition;
        	}
        }
        //No public direct access allowed
        public function GetUserMedicalHistory($profileid){
        	$userhistory = new UserMedicalHistory();
        	if($profileid == "" || $profileid == null){
        		return FALSE;
        	}else{
        		$findUserHistory = $userhistory->getUserMedicalHistory($profileid);
        		return $findUserHistory;
        	}
        }




        //No public direct access allowed
        public function GetUserProfileInformation($profileid){
          $input = Input::all();
        	$userinsurancepolicy = new UserInsurancePolicy();
        	$returnArray = new stdClass();
        	$findUserProfile = $this->GetUserProfile($profileid);
          $user_id = StringHelper::getUserId($profileid);
          // $properties = !empty($input['type']) && $input['type'] == "with_medical_properties" ? "with_medical_properties" : "profile";
          $properties = "with_medical_properties";

          if($findUserProfile){
            $userPolicy = $userinsurancepolicy->FindUserInsurancePolicy($findUserProfile->UserID);
            $returnArray->status = TRUE;
            $returnArray->login_status = TRUE;
            $wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
            $user_plan_history = DB::table('user_plan_history')
                      ->where('user_id', $user_id)
                      ->where('type', 'started')
                      ->first();

            $customer_active_plan = DB::table('customer_active_plan')
              ->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
              ->first();
            $returnArray->data['profile']['user_id'] = $findUserProfile->UserID;
            $returnArray->data['profile']['email'] = $findUserProfile->Email;
            $returnArray->data['profile']['account_type'] = $customer_active_plan->account_type;
            $returnArray->data['profile']['full_name'] = $findUserProfile->Name;
            $returnArray->data['profile']['nric'] = $findUserProfile->NRIC;
            $returnArray->data['profile']['fin'] = $findUserProfile->FIN;
            $returnArray->data['profile']['mobile_phone'] = $findUserProfile->PhoneNo;
            $returnArray->data['profile']['dob'] = $findUserProfile->DOB;
            $returnArray->data['profile']['age'] = $findUserProfile->Age;
            $returnArray->data['profile']['weight'] = $findUserProfile->Weight;
            $returnArray->data['profile']['height'] = $findUserProfile->Height;
            $returnArray->data['profile']['currency_type'] = $wallet->currency_type;
            if((int)$findUserProfile->UserType == 5 && (int)$findUserProfile->access_type == 0 || (int)$findUserProfile->UserType == 5 && (int)$findUserProfile->access_type == 1) {
              $returnArray->data['profile']['to_update_auto_logout'] = $findUserProfile->account_update_status == 0 && $findUserProfile->account_already_update == 0 ? true : false;
            } else {
              $returnArray->data['profile']['to_update_auto_logout'] = false;
            }

            if(!empty($findUserProfile->Weight) && !empty($findUserProfile->Height)){
             $bmi = $findUserProfile->Weight / (($findUserProfile->Height / 100) * ($findUserProfile->Height / 100));
           }else {$bmi = 0; }

           $returnArray->data['profile']['bmi'] =  $bmi;
           $returnArray->data['profile']['blood_type'] = $findUserProfile->Blood_Type;
                    //This one need to change when image upload available
           $returnArray->data['profile']['photo_url'] = $findUserProfile->Image;
           if($userPolicy){
             $returnArray->data['profile']['insurance_company'] = $userPolicy->Name;
             $returnArray->data['profile']['insurance_policy_no'] = $userPolicy->PolicyNo;
             $returnArray->data['profile']['insurance_policy_name'] = $userPolicy->PolicyName;
           }else{
             $returnArray->data['profile']['insurance_company'] = null;
             $returnArray->data['profile']['insurance_policy_no'] = null;
             $returnArray->data['profile']['insurance_policy_name'] = null;
           }
                    //Insurance details
           if($userPolicy){
             $returnArray->data['insurance']['insurance_id'] = $userPolicy->UserInsurancePolicyID;
             $returnArray->data['insurance']['name'] = $userPolicy->Name;
             $returnArray->data['insurance']['policy_no'] = $userPolicy->PolicyNo;
             $returnArray->data['insurance']['policy_name'] = $userPolicy->PolicyName;
             $returnArray->data['insurance']['expire_date'] = 0;
                        //$returnArray->data['insurance']['image_url'] = URL::to('/assets/'.$userPolicy->Image);
             $returnArray->data['insurance']['image_url'] = $userPolicy->Image;
           }else{
             $returnArray->data['insurance'] = null;
           }

           if($properties == "with_medical_properties") {
            $findUserAllergy = $this->GetUserAllergies($profileid);
            $findUserMedication = $this->GetUserMedications($profileid);
            $findUserCondition = $this->GetUserConditions($profileid);
            $findMedicalHistory = $this->GetUserMedicalHistory($profileid);
              //Allagies
              if($findUserAllergy){
                foreach($findUserAllergy as $allergy){
                  $getAllergy['allergy_id'] = $allergy->AllergyID;
                  $getAllergy['name'] = $allergy->Name;
                  $allAllergy[] = $getAllergy;
                }
                $returnArray->data['allergies'] = $allAllergy;
              }else{
                $returnArray->data['allergies'] = null;
              }

              //Medications
              if($findUserMedication){
                foreach($findUserMedication as $medication){
                  $getMedication['medication_id'] = $medication->MedicationID;
                  $getMedication['name'] = $medication->Name;
                  $getMedication['dosage'] = $medication->Dosage;
                  $allMedication[] = $getMedication;
                }
                $returnArray->data['medications'] = $allMedication;
              }else{
                $returnArray->data['medications']= null;
              }

              //Conditions
              if($findUserCondition){
                foreach($findUserCondition as $condition){
                  $getCondition['condition_id'] = $condition->ConditionID;
                  $getCondition['name'] = $condition->Name;
                  $newDate = date("d-m-Y", strtotime($condition->Date));
                  $getCondition['date'] = $newDate;
                  $allConditions[] = $getCondition;
                }
                $returnArray->data['conditions'] = $allConditions;
              }else{
                $returnArray->data['conditions'] = null;
              }

                              //History
                if($findMedicalHistory){
                  foreach($findMedicalHistory as $history){
                    $historyDetail = new UserMedicalHistoryDetail();
                    $findHistoryDetail = $historyDetail->getUserMedicalHistoryDetails($history->HistoryID);
                    $newDate = date("d-m-Y", strtotime($history->Date));
                    $getHistory['date'] = $newDate;
                    $getHistory['record_id'] = $history->HistoryID;
                    $getHistory['visit_type'] = $history->VisitType;
                                        //$getHistory['list']['doctor'] = $history->Name;
                    $getHistory['doctor'] = $history->Doctor_Name;
                    $getHistory['clinic_name'] = $history->Clinic_Name;
                                        //need to check clinic
                                        ///$getHistory['list']['clinic_name'] = $history->Name;
                    $getHistory['note'] = $history->Note;
                                        //$getHistory['date'] = $newDate;
                    if($findHistoryDetail){
                        foreach($findHistoryDetail as $hisDetail){
                          $getHistoryDetail['attachment_id'] = $hisDetail->DetailID;
                          $getHistoryDetail['url'] = URL::to('/assets/'.$hisDetail->Image);
                          $getHistory1[] = $getHistoryDetail;
                        }
                        $getHistory['attachments'] = $getHistory1;
                      }else{
                      $getHistory['attachments'] = null;
                    }
                    $allHistory[] = $getHistory;
                  }

                  $returnArray->data['history'] = $allHistory;
              }else{
                $returnArray->data['history'] = null;
              }
           }
      //  check if user is new or old
      $date = date('Y-m-d');
      $date_created = date('Y-m-d', strtotime('+14 days', strtotime($user_plan_history->created_at)));
      if($date_created > $date)  {
        $returnArray->data['profile']['status'] = "new";
      } else {
        $returnArray->data['profile']['status'] = "old";
      }
      }else{
        $returnArray->status = FALSE;
        $returnArray->message = StringHelper::errorMessage("NoRecords");
      }
    return $returnArray;
}

        //Add new user allergy
public function AddNewAllergy(){
 $allergy = Input::get('allergy');
 $userid = Input::get('userid');

 $userallergy = new UserAllergy();
 $dataArray = array();
 $dataArray['allergy']= $allergy;
 $dataArray['userid']= $userid;

 $returnObject = new stdClass();

 $insertedID = $userallergy->insertAllergy($dataArray);
 if($insertedID){
  $returnObject->status = TRUE;
  $returnObject->data['record_id'] = $insertedID;
}else{
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Tryagain");
}
return Response::json($returnObject);
}

        //Add new user medical condition
public function AddNewMedicalCondition(){
 $condition = Input::get ('condition');
 $userid = Input::get ('userid');
 $date = Input::get ('date');
 $newDate = date("d-m-Y", strtotime($date));

 $medicalcondition = new UserCondition();
 $dataArray = array();
 $dataArray['condition']= $condition;
 $dataArray['userid']= $userid;
 $dataArray['date']= $newDate;

 $returnObject = new stdClass();

 $insertedID = $medicalcondition->insertMedicalCondition($dataArray);
 if($insertedID){
  $returnObject->status = TRUE;
  $returnObject->data['record_id'] = $insertedID;
}else{
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Tryagain");
}
return Response::json($returnObject);
}

        //Add new user medication
public function AddNewUserMedication(){
 $medication = Input::get ('medication');
 $userid = Input::get ('userid');
 $dosage = Input::get ('dosage');

 $usermedication = new UserMedication();
 $dataArray = array();
 $dataArray['medication']= $medication;
 $dataArray['userid']= $userid;
 $dataArray['dosage']= $dosage;

 $returnObject = new stdClass();

 $insertedID = $usermedication->insertUserMedication($dataArray);
 if($insertedID){
  $returnObject->status = TRUE;
  $returnObject->data['record_id'] = $insertedID;
}else{
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Tryagain");
}
return Response::json($returnObject);
}

        //Add new medical history
public function AddNewMedicalHistory(){
 $visittype = Input::get ('visit_type');
 $userid = Input::get ('user_id');
 $doctor = Input::get ('doctor');
 $clinic = Input::get ('clinic_name');
 $note = Input::get ('note');
 $date = Input::get ('date');
 $newDate = date("d-m-Y", strtotime($date));

 $medicalhistory = new UserMedicalHistory();
 $dataArray = array();
 $dataArray['visittype']= $visittype;
 $dataArray['userid']= $userid;
 $dataArray['doctor']= $doctor;
 $dataArray['clinic']= $clinic;
 $dataArray['note']= $note;
 $dataArray['date']= $newDate;

 $returnObject = new stdClass();

 $insertedID = $medicalhistory->insertMedicalHistory($dataArray);
 if($insertedID){
  $returnObject->status = TRUE;
  $returnObject->data['record_id'] = $insertedID;
}else{
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Tryagain");
}
return Response::json($returnObject);
}

public function UpdateMedicalHistory(){
 $allUpdatedata = Input::all();
 $userhistory = new UserMedicalHistory();
            //$insurancepolicy = new UserInsurancePolicy();
 $returnObject = new stdClass();

 if(is_array($allUpdatedata) && count($allUpdatedata) >0 ){
  if(!empty($allUpdatedata['visit_type'])) {
   $dataArray['VisitType'] = $allUpdatedata['visit_type'];
 }if(!empty($allUpdatedata['doctor'])) {
   $dataArray['Doctor_Name'] = $allUpdatedata['doctor'];
 }if($allUpdatedata['clinic_name']) {
   $dataArray['Clinic_Name'] = $allUpdatedata['clinic_name'];
 }if(!empty($allUpdatedata['note'])) {
   $dataArray['Note'] = $allUpdatedata['note'];
 }if(!empty($allUpdatedata['date'])) {
   $dataArray['Note'] = date("d-m-Y", strtotime($allUpdatedata['date']));
 }
 $dataArray['updated_at'] = time();
 $dataArray['historyid'] = $allUpdatedata['history_id'];

 $updateHistory = $userhistory->updateMedicalHistory($dataArray);
 if($updateHistory){
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
}


public function ChangePassword(){
 $returnObject = new stdClass();
 $findUserID = AuthLibrary::validToken();
 if($findUserID){
                // check User ID
  $type = StringHelper::checkUserType($findUserID);
  if((int)$type['user_type'] == 5 && (int)$type['access_type'] == 0 || (int)$type['user_type'] == 5 && (int)$type['access_type'] == 1)
  {
   $returnObject = AuthLibrary::ChangePassword($findUserID);
 } else {
   $returnObject->status = FALSE;
   $returnObject->message = 'Only Employee User can update the password.';
 }

}else{
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
}
return Response::json($returnObject);
}

public function AddDeviceToken(){
 $returnObject = new stdClass();
 $findUserID = AuthLibrary::validToken();
 if($findUserID){
  $returnObject = AuthLibrary::AddDeviceToken($findUserID);
}else{
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
}
return Response::json($returnObject);
}

        /* Use          :   Used to disable a user profile
         * Access       :   Public
         *
         */
        public function DisableProfile(){
        	$returnObject = new stdClass();
        	$findUserID = AuthLibrary::validToken();
            //$findUserID =1;
        	if($findUserID){
        		$returnObject = AuthLibrary::DisableProfile($findUserID);
        	}else{
        		$returnObject->status = FALSE;
        		$returnObject->message = StringHelper::errorMessage("Token");
        	}
        	return Response::json($returnObject);
        }

        public function OTPProfileUpdate(){
        	$returnObject = new stdClass();
        	$findUserID = AuthLibrary::validToken();
        	if($findUserID){
        		$returnObject = AuthLibrary::OTPProfileUpdate($findUserID);
        	}else{
        		$returnObject->status = FALSE;
        		$returnObject->message = StringHelper::errorMessage("Token");
        	}
        	return Response::json($returnObject);
        }
        public function OTPCodeValidation(){
        	$returnObject = new stdClass();
        	$findUserID = AuthLibrary::validToken();
        	if($findUserID){
        		$returnObject = AuthLibrary::OTPCodeValidate($findUserID);
        	}else{
        		$returnObject->status = FALSE;
        		$returnObject->message = StringHelper::errorMessage("Token");
        	}
        	return Response::json($returnObject);
        }

        public function OTPCodeResend(){
        	$returnObject = new stdClass();
        	$findUserID = AuthLibrary::validToken();
        	if($findUserID){
        		$returnObject = AuthLibrary::OTPCodeResend($findUserID);
        	}else{
        		$returnObject->status = FALSE;
        		$returnObject->message = StringHelper::errorMessage("Token");
        	}
        	return Response::json($returnObject);
        }

        public function FindCoordinate(){
        	$response = Geocode::make()->address('#01-01 Blk 51 Avenue 3 Ang Mo Kio');

        	if ($response) {
        		echo $response->latitude()."<br>";
        		echo $response->longitude()."<br>";
        		echo $response->formattedAddress()."<br>";
        		echo $response->locationType();
        	}
        }

        public function getUserWallet( )
        {

          $AccessToken = new Api_V1_AccessTokenController();
          $returnObject = new stdClass();
          $authSession = new OauthSessions();
          $getRequestHeader = StringHelper::requestHeader();
          $input = Input::all();

          if(!empty($getRequestHeader['Authorization'])){
            $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
            if($getAccessToken){
              $findUserID = $authSession->findUserID($getAccessToken->session_id);
              if($findUserID){
                $e_claim = [];
                $transaction_details = [];
                $in_network_spent = 0;
                $out_of_pocket_spent = 0;
                $ids = StringHelper::getSubAccountsID($findUserID);
                $user_id = StringHelper::getUserId($findUserID);
                $user_plan_history = DB::table('user_plan_history')
                ->where('user_id', $user_id)
                ->where('type', 'started')
                ->orderBy('created_at', 'desc')
                ->first();

                $customer_active_plan = DB::table('customer_active_plan')
                ->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
                ->first();

                $spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';
                $filter = isset($input['filter']) ? $input['filter'] : 'current_term';
                $dates = MemberHelper::getMemberDateTerms($user_id, $filter, $spending_type);
                $user_spending_dates = MemberHelper::getMemberCreditReset($user_id, $filter, $spending_type);
                $wallet = DB::table('e_wallet')->where('UserID', $user_id)->orderBy('created_at', 'desc')->first();
                $user_type = PlanHelper::getUserAccountType($findUserID);

                if($user_spending_dates) {
                  if($spending_type == 'medical') {
                    $table_wallet_history = 'wallet_history';
                    $history_column_id = "wallet_history_id";
                    $credit_data = PlanHelper::memberMedicalAllocatedCreditsByDates($wallet->wallet_id, $user_id, $user_spending_dates['start'], $user_spending_dates['end']);
                  } else {
                    $table_wallet_history = 'wellness_wallet_history';
                    $history_column_id = "wellness_wallet_history_id";
                    $credit_data = PlanHelper::memberWellnessAllocatedCreditsByDates($wallet->wallet_id, $user_id, $user_spending_dates['start'], $user_spending_dates['end']);
                  }
                } else {
                  $credit_data = null;
                }

                if($dates) {
                  if($user_type == "employee") {
                    $e_claim_result = DB::table('e_claim')
                    ->whereIn('user_id', $ids)
                    ->where('spending_type', $spending_type)
                    ->where('date', '>=', $dates['start'])
                    ->where('date', '<=', $dates['end'])
                    ->where('status', 1)
                    ->orderBy('date', 'desc')
                    ->take(3)
                    ->get();

                    // get in-network transactions
                    $transactions = DB::table('transaction_history')
                    ->whereIn('UserID', $ids)
                    ->where('spending_type', $spending_type)
                    ->where('created_at', '>=', $dates['start'])
                    ->where('created_at', '<=', $dates['end'])
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
                  } else {
                    $e_claim_result = DB::table('e_claim')
                    ->where('user_id', $findUserID)
                    ->where('spending_type', $spending_type)
                    ->where('date', '>=', $dates['start'])
                    ->where('date', '<=', $dates['end'])
                    ->where('status', 1)
                    ->orderBy('date', 'desc')
                    ->take(3)
                    ->get();

                    // get in-network transactions
                    $transactions = DB::table('transaction_history')
                    ->where('UserID', $findUserID)
                    ->where('spending_type', $spending_type)
                    ->where('created_at', '>=', $dates['start'])
                    ->where('created_at', '<=', $dates['end'])
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
                  }

                } else {
                  $e_claim_result = [];
                  $transactions = [];
                }

                foreach($e_claim_result as $key => $res) {
                  if($res->status == 0) {
                    $status_text = 'Pending';
                  } else if($res->status == 1) {
                    $status_text = 'Approved';
                    $res->amount = $res->claim_amount;
                  } else if($res->status == 2) {
                    $status_text = 'Rejected';
                  } else {
                    $status_text = 'Pending';
                  }

                  if($res->default_currency == "sgd") {
                    $currency_symbol = "SGD";
                  } else if($res->default_currency == "myr" && $res->currency_type == "sgd") {
                    $currency_symbol = "MYR";
                    $res->amount = $res->amount;
                  } else {
                    $currency_symbol = "MYR";
                  }

                  $member = DB::table('user')->where('UserID', $res->user_id)->first();

                  $temp = array(
                    'status'      => $res->status,
                    'status_text' => $status_text,
                    'claim_date'  => date('d F Y', strtotime($res->created_at)),
                    'time'        => $res->time,
                    'service'     => $res->service,
                    'merchant'    => $res->merchant,
                    'amount'      => number_format($res->amount, 2),
                    'converted_amount'      => number_format($res->amount, 2),
                    'member'      => ucwords($member->Name),
                    'type'        => 'E-Claim',
                    'transaction_id' => $res->e_claim_id,
                    'visit_date'  => date('d F Y', strtotime($res->date)).', '.$res->time,
                    'spending_type' => $res->spending_type,
                    'currency_symbol'   => $currency_symbol
                  );

                  array_push($e_claim, $temp);
                }

                foreach ($transactions as $key => $trans) {
                  if($trans) {
                    $wallet_status = false;
                    $clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
                    $clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
                    $customer = DB::table('user')->where('UserID', $trans->UserID)->first();
                    $procedure_temp = "";
                    $procedure = "";

                    // $company_wallet_status = PlanHelper::getCompanyAccountType($user_id);

                    // if($company_wallet_status) {
                    //  if($company_wallet_status == "Health Wallet") {
                    //   $wallet_status = true;
                    // }
                    // }
                    // get services
                  if((int)$trans->multiple_service_selection == 1)
                  {
                    // get multiple service
                    $service_lists = DB::table('transaction_services')
                    ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
                    ->where('transaction_services.transaction_id', $trans->transaction_id)
                    ->get();

                    foreach ($service_lists as $key => $service) {
                      if(sizeof($service_lists) - 2 == $key) {
                        $procedure_temp .= ucwords($service->Name).' and ';
                      } else {
                        $procedure_temp .= ucwords($service->Name).',';
                      }
                      $procedure = rtrim($procedure_temp, ',');
                    }
                    $clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
                  } else {
                    $service_lists = DB::table('clinic_procedure')
                    ->where('ProcedureID', $trans->ProcedureID)
                    ->first();
                    if($service_lists) {
                      $procedure = ucwords($service_lists->Name);
                      $clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
                    } else {
                      $clinic_name = ucwords($clinic_type->Name);
                    }
                  }


                  $total_amount = 0;

                  if(strripos($trans->procedure_cost, '$') !== false) {
                   $temp_cost = explode('$', $trans->procedure_cost);
                   $cost = $temp_cost[1];
                 } else {
                   $cost = floatval($trans->procedure_cost);
                 }

                 $total_amount = $cost;

                 if((int)$trans->health_provider_done == 1) {
                  $receipt_status = TRUE;
                  $health_provider_status = TRUE;
                  $credit_status = FALSE;
                  if((int)$trans->lite_plan_enabled == 1) {
                    $total_amount = $cost + $trans->consultation_fees;
                  } else {
                    $total_amount = $cost;
                  }
                  $type = "cash";
                } else {
                  $health_provider_status = FALSE;
                  $credit_status = TRUE;
                  if((int)$trans->lite_plan_enabled == 1) {
                    if((int)$trans->half_credits == 1) {
                      $total_amount = $trans->credit_cost + $trans->consultation_fees + $trans->cash_cost;
                    // $total_amount = $trans->credit_cost + $trans->cash_cost;
                    } else {
                      $total_amount = $trans->credit_cost + $trans->consultation_fees + $trans->cash_cost;
                    }
                  } else {
                    $total_amount = $cost;
                  }
                  $type = "credits";
                }

                if($trans->default_currency == "sgd") {
                  $currency_symbol = "SGD";
                  $converted_amount = $total_amount;
                } else if($trans->default_currency == "myr" && $trans->currency_type == "sgd") {
                  $currency_symbol = "MYR";
                  $converted_amount = $total_amount * $trans->currency_amount;
                } else {
                  $currency_symbol = "MYR";
                  $converted_amount = $total_amount * $trans->currency_amount;
                }

                $clinic_sub_name = strtoupper(substr($clinic->Name, 0, 3));
                $transaction_id = $clinic_sub_name.str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);
                $out_of_pocket_spent += $converted_amount;
                $format = array(
                  'clinic_name'       => $clinic->Name,
                  'clinic_image'      => $clinic->image,
                  'amount'            => number_format($converted_amount, 2),
                  'converted_amount'  => number_format($converted_amount, 2),
                  'clinic_type_and_service' => $clinic_name,
                  'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->created_at)),
                  'customer'          => ucwords($customer->Name),
                  'transaction_id'    => $transaction_id,
                  'cash_status'       => $health_provider_status,
                  'credit_status'     => $credit_status,
                  'user_id'           => $trans->UserID,
                  'refunded'          => (int)$trans->refunded == 1? TRUE : FALSE,
                  'currency_symbol'   => $currency_symbol
                );

                array_push($transaction_details, $format);
              }
            }

            $allocation = $credit_data ? $credit_data['allocation'] : 0;
            $current_spending = $credit_data ? $credit_data['get_allocation_spent'] : 0;
            $e_claim_spent = $credit_data ? $credit_data['e_claim_spent'] : 0;
            $in_network_spent = $credit_data ? $credit_data['in_network_spent'] : 0;

            $balance = $credit_data ? $credit_data['balance'] : 0;

            // if($customer_active_plan->account_type != "enterprise_plan")  {
              PlanHelper::reCalculateEmployeeBalance($user_id);
            // }

            $total_visit_limit = 0;
            $total_visit_created = 0;
            $total_visit_balance = 0;

            $currency_symbol = strtoupper($wallet->currency_type);
            if($customer_active_plan && $customer_active_plan->account_type == "enterprise_plan") {
              $balance = number_format($balance, 2);
              if($filter == "current_term") {
                if($user_type == "employee") {
                  $total_visit_limit = $user_plan_history->total_visit_limit;
                  $total_visit_created = $user_plan_history->total_visit_created;
                  $total_visit_balance = $total_visit_limit - $total_visit_created;
                } else {
                  $user_plan_history = DB::table('dependent_plan_history')
                                            ->where('user_id', $findUserID)
                                            ->where('type', 'started')
                                            ->orderBy('created_at', 'desc')
                                            ->first();
                  $total_visit_limit = $user_plan_history->total_visit_limit;
                  $total_visit_created = $user_plan_history->total_visit_created;
                  $total_visit_balance = $total_visit_limit - $total_visit_created;
                }

              } else {
                if($user_type == "employee") {
                  $plan_history = MemberHelper::getMemberPreviousPlanHistory($user_id);
                } else {
                  $plan_history = MemberHelper::getDependentPreviousPlanHistory($findUserID);
                }

                if($plan_history) {
                  $total_visit_limit = $plan_history->total_visit_limit;
                  $total_visit_created = $plan_history->total_visit_created;
                  $total_visit_balance = $total_visit_limit - $total_visit_created;
                }
              }

            } else {
              $balance = number_format($balance, 2);
            }

            $customer_id = PlanHelper::getCustomerId($user_id);
            $wallet_data = array(
              'spending_type'             => $spending_type,
              'balance'                   => $customer_active_plan->account_type == "out_of_network" ? number_format($out_of_pocket_spent, 2) : $balance,
              'in_network_credits_spent'  => number_format($in_network_spent, 2),
              'e_claim_credits_spent'     => number_format($e_claim_spent, 2),
              'e_claim_transactions'      => $e_claim,
              'in_network_transactions'   => $transaction_details,
              'currency_symbol'           => $currency_symbol,
              'account_type'              => $customer_active_plan->account_type,
              'total_visit'               => $total_visit_limit,
              'total_utilised'            => $total_visit_created,
              'total_visit_balance'       => $total_visit_balance,
              'user_type'                 => $user_type,
            );

            $spending = CustomerHelper::getAccountSpendingBasicPlanStatus($customer_id);
            $wallet_data['spending_status'] = array(
              'medical' => $spending['medical_enabled'],
              'wellness'  => $spending['wellness_enabled']
            );

            $returnObject->status = true;
            $returnObject->message = "Success";
            $returnObject->data = $wallet_data;

            return Response::json($returnObject);
          } else {
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("Token");
            return Response::json($returnObject);
          }
          } else {
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("Token");
            return Response::json($returnObject);
          }
         } else {
          $returnObject->status = FALSE;
          $returnObject->message = StringHelper::errorMessage("Token");
          return Response::json($returnObject);
          }

        }

        public function getMemberPartialWallet( )
        {

          $AccessToken = new Api_V1_AccessTokenController();
          $returnObject = new stdClass();
          $authSession = new OauthSessions();
          $getRequestHeader = StringHelper::requestHeader();
          $input = Input::all();

          if(!empty($getRequestHeader['Authorization'])){
            $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
            if($getAccessToken){
              $findUserID = $authSession->findUserID($getAccessToken->session_id);
              if($findUserID){
                $spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';
                $user_id = StringHelper::getUserId($findUserID);
                $user_type = PlanHelper::getUserAccountType($findUserID);
                $wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
                $balance = 0;

                PlanHelper::reCalculateEmployeeBalance($user_id);
                $user_plan_history = DB::table('user_plan_history')->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
                $customer_active_plan = DB::table('customer_active_plan')
                ->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
                ->first();

                if($customer_active_plan && $customer_active_plan->account_type == "enterprise_plan") {
                  if($user_type == "employee") {
                    $returnObject->data = ['visits' => $user_plan_history->total_visit_limit - $user_plan_history->total_visit_created, 'account_type' => $customer_active_plan->account_type];
                  } else {
                    $user_plan_history = DB::table('dependent_plan_history')
                                              ->where('user_id', $findUserID)
                                              ->where('type', 'started')
                                              ->orderBy('created_at', 'desc')
                                              ->first();

                    $returnObject->data = ['visits' => $user_plan_history->total_visit_limit - $user_plan_history->total_visit_created, 'account_type' => $customer_active_plan->account_type];
                  }
                } else {
                  if($spending_type == 'medical') {
                    $credit_data = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $user_id);
                  } else {
                    $credit_data = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $user_id);
                  }

                  $balance = $credit_data ? $credit_data['balance'] : 0;
                  $currency_symbol = strtoupper($wallet->currency_type);
                  $balance = number_format($balance, 2);
                  $returnObject->data = ['balance' => $balance, 'currency_symbol' => $currency_symbol, 'account_type' => $customer_active_plan->account_type];
                }

                $returnObject->status = true;
                $returnObject->message = "Success";
                return Response::json($returnObject);
              } else {
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
                return Response::json($returnObject);
              }
            } else {
              $returnObject->status = FALSE;
              $returnObject->message = StringHelper::errorMessage("Token");
              return Response::json($returnObject);
            }
          } else {
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("Token");
            return Response::json($returnObject);
          }
        }

  public function getUserCredits( )
  {
        // $ip = file_get_contents('https://api.ipify.org');
        // // return $ip;
   $rm = false;
        // try {
        //     $dataArray = file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip);
        //     if($dataArray) {
        //         // $dataArray = json_encode($dataArray, true);
        //         return $dataArray->geoplugin_countryCode;
        //         return $dataArray;
        //         if($dataArray['geoplugin_countryCode'] == "MY") {
        //             $rm = true;
        //         }
        //     }

        // } catch(Exception $e) {
        //     return $e->getMessage();
        // }


   $AccessToken = new Api_V1_AccessTokenController();
   $returnObject = new stdClass();
   $authSession = new OauthSessions();
   $getRequestHeader = StringHelper::requestHeader();
   if(!empty($getRequestHeader['Authorization'])){
    $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
    if($getAccessToken){
     $findUserID = $authSession->findUserID($getAccessToken->session_id);
     if($findUserID){
      $in_network_spent = 0;
      $e_claim_spent = 0;
      $allocation = 0;
                    // $wallet = new Wallet( );
      $user_id = StringHelper::getUserId($findUserID);
                    // new credits info
      $returnObject->status = TRUE;
      $returnObject->message = 'Success';

      $wallet = DB::table('e_wallet')->where('UserID', $user_id)->orderBy('created_at', 'desc')->first();

      $jsonArray['wallet_id'] = $wallet->wallet_id;
      $wallet_reset = DB::table('credit_reset')
      ->where('id', $user_id)
      ->where('user_type', 'employee')
      ->where('spending_type', 'medical')
      ->orderBy('created_at', 'desc')
      ->first();;

      if($wallet_reset) {
       $e_claim_spent = DB::table('wallet_history')
       ->where('wallet_id', $wallet->wallet_id)
       ->where('where_spend', 'e_claim_transaction')
       ->where('created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
       ->sum('credit');

       $in_network_temp_spent = DB::table('wallet_history')
       ->where('wallet_id', $wallet->wallet_id)
       ->where('where_spend', 'in_network_transaction')
       ->where('created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
       ->sum('credit');
       $credits_back = DB::table('wallet_history')
       ->where('wallet_id', $wallet->wallet_id)
       ->where('where_spend', 'credits_back_from_in_network')
       ->where('created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
       ->sum('credit');

       $temp_allocation = DB::table('e_wallet')
       ->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
       ->where('e_wallet.UserID', $user_id)
       ->whereIn('wallet_history.logs', ['added_by_hr'])
       ->where('wallet_history.created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
       ->sum('wallet_history.credit');

       $deducted_allocation = DB::table('e_wallet')
       ->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
       ->where('e_wallet.UserID', $user_id)
       ->whereIn('wallet_history.logs', ['deducted_by_hr'])
       ->where('wallet_history.created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
       ->sum('wallet_history.credit');
     } else {
                        // get all medical credits transactions from transaction history
       $e_claim_spent = DB::table('wallet_history')
       ->where('wallet_id', $wallet->wallet_id)
       ->where('where_spend', 'e_claim_transaction')
       ->sum('credit');

       $in_network_temp_spent = DB::table('wallet_history')
       ->where('wallet_id', $wallet->wallet_id)
       ->where('where_spend', 'in_network_transaction')
       ->sum('credit');
       $credits_back = DB::table('wallet_history')
       ->where('wallet_id', $wallet->wallet_id)
       ->where('where_spend', 'credits_back_from_in_network')
       ->sum('credit');

       $temp_allocation = DB::table('e_wallet')
       ->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
       ->where('e_wallet.UserID', $user_id)
       ->whereIn('logs', ['added_by_hr'])
       ->sum('wallet_history.credit');

       $deducted_allocation = DB::table('e_wallet')
       ->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
       ->where('e_wallet.UserID', $user_id)
       ->whereIn('logs', ['deducted_by_hr'])
       ->sum('wallet_history.credit');
     }

     $in_network_spent = $in_network_temp_spent - $credits_back;
     $allocation = $temp_allocation - $deducted_allocation;
     $current_spending = $in_network_spent + $e_claim_spent;


                    // get all wellness credits transactions from transaction history
                    // $e_claim_spent_wellness = DB::table('wellness_wallet_history')
                    //         ->where('wallet_id', $wallet->wallet_id)
                    //         ->where('where_spend', 'e_claim_transaction')
                    //         ->sum('credit');

                    // $in_network_temp_spent_wellness = DB::table('wellness_wallet_history')
                    //         ->where('wallet_id', $wallet->wallet_id)
                    //         ->where('where_spend', 'in_network_transaction')
                    //         ->sum('credit');
                    // $credits_back_wellness = DB::table('wellness_wallet_history')
                    //         ->where('wallet_id', $wallet->wallet_id)
                    //         ->where('where_spend', 'credits_back_from_in_network')
                    //         ->sum('credit');
                    // $in_network_spent_wellness = $in_network_temp_spent_wellness - $credits_back_wellness;

                    // $temp_allocation_wellness = DB::table('e_wallet')
                    //     ->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
                    //     ->where('e_wallet.UserID', $user_id)
                    //     ->where('logs', 'added_by_hr')
                    //     ->sum('wellness_wallet_history.credit');

                    // $deducted_allocation_wellness = DB::table('e_wallet')
                    //     ->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
                    //     ->where('e_wallet.UserID', $user_id)
                    //     ->where('logs', 'deducted_by_hr')
                    //     ->sum('wellness_wallet_history.credit');

                    // $allocation_wellness = $temp_allocation_wellness - $deducted_allocation_wellness;
                    // $current_spending_wellness = $in_network_spent_wellness + $e_claim_spent_wellness;

     $total_allocation = $allocation;
     $total_current_spending = $current_spending;

     $total_in_network_spent = $in_network_spent;
     $total_e_claim_spent = $e_claim_spent;

     $current_balance = $allocation - $current_spending;

                    // if($rm) {
                    //     $balance =  $current_balance / 3;
                    // } else {
     $balance = $current_balance;
                    // }

     $jsonArray['balance'] = number_format($balance, 2);

                    // check and update user wallet
     if($wallet->balance != $current_balance) {
       DB::table('e_wallet')->where('UserID', $findUserID)->update(['balance' => $current_balance, 'updated_at' => date('Y-m-d h:i:s')]);
     }

     $jsonArray['in_network_credits_spent'] = $total_in_network_spent > 0 ? number_format($total_in_network_spent, 2) : 0.00;

     $jsonArray['e_claim_credits_spent'] = $total_e_claim_spent > 0 ? number_format($total_e_claim_spent, 2) : 0.00;
     $jsonArray['currency_symbol'] = "S$";
     $jsonArray['profile'] = DB::table('user')->where('UserID', $findUserID)->first();
     $returnObject->data = $jsonArray;
     return Response::json($returnObject);
   } else {
    $returnObject->status = FALSE;
    $returnObject->message = StringHelper::errorMessage("Token");
    return Response::json($returnObject);
  }
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function GetCredits( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
   if($findUserID){
    $in_network_spent = 0;
    $e_claim_spent = 0;
    $allocation = 0;
                    // $wallet = new Wallet( );
    $user_id = StringHelper::getUserId($findUserID);
                    // new credits info

    $returnObject->status = TRUE;
    $returnObject->message = 'Success';

    $wallet = DB::table('e_wallet')->where('UserID', $user_id)->orderBy('created_at', 'desc')->first();

    $jsonArray['wallet_id'] = $wallet->wallet_id;
    $wallet_reset = DB::table('credit_reset')
    ->where('id', $user_id)
    ->where('user_type', 'employee')
    ->where('spending_type', 'medical')
    ->orderBy('created_at', 'desc')
    ->first();;

    if($wallet_reset) {
     $e_claim_spent = DB::table('wallet_history')
     ->where('wallet_id', $wallet->wallet_id)
     ->where('where_spend', 'e_claim_transaction')
     ->where('created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
     ->sum('credit');

     $in_network_temp_spent = DB::table('wallet_history')
     ->where('wallet_id', $wallet->wallet_id)
     ->where('where_spend', 'in_network_transaction')
     ->where('created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
     ->sum('credit');
     $credits_back = DB::table('wallet_history')
     ->where('wallet_id', $wallet->wallet_id)
     ->where('where_spend', 'credits_back_from_in_network')
     ->where('created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
     ->sum('credit');

     $temp_allocation = DB::table('e_wallet')
     ->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
     ->where('e_wallet.UserID', $user_id)
     ->whereIn('wallet_history.logs', ['added_by_hr'])
     ->where('wallet_history.created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
     ->sum('wallet_history.credit');

     $deducted_allocation = DB::table('e_wallet')
     ->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
     ->where('e_wallet.UserID', $user_id)
     ->whereIn('wallet_history.logs', ['deducted_by_hr'])
     ->where('wallet_history.created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
     ->sum('wallet_history.credit');
   } else {
                        // get all medical credits transactions from transaction history
     $e_claim_spent = DB::table('wallet_history')
     ->where('wallet_id', $wallet->wallet_id)
     ->where('where_spend', 'e_claim_transaction')
     ->sum('credit');

     $in_network_temp_spent = DB::table('wallet_history')
     ->where('wallet_id', $wallet->wallet_id)
     ->where('where_spend', 'in_network_transaction')
     ->sum('credit');
     $credits_back = DB::table('wallet_history')
     ->where('wallet_id', $wallet->wallet_id)
     ->where('where_spend', 'credits_back_from_in_network')
     ->sum('credit');

     $temp_allocation = DB::table('e_wallet')
     ->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
     ->where('e_wallet.UserID', $user_id)
     ->whereIn('logs', ['added_by_hr'])
     ->sum('wallet_history.credit');

     $deducted_allocation = DB::table('e_wallet')
     ->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
     ->where('e_wallet.UserID', $user_id)
     ->whereIn('logs', ['deducted_by_hr'])
     ->sum('wallet_history.credit');
   }

   $in_network_spent = $in_network_temp_spent - $credits_back;
   $allocation = $temp_allocation - $deducted_allocation;
   $current_spending = $in_network_spent + $e_claim_spent;


                    // get all wellness credits transactions from transaction history
                    // $e_claim_spent_wellness = DB::table('wellness_wallet_history')
                    //         ->where('wallet_id', $wallet->wallet_id)
                    //         ->where('where_spend', 'e_claim_transaction')
                    //         ->sum('credit');

                    // $in_network_temp_spent_wellness = DB::table('wellness_wallet_history')
                    //         ->where('wallet_id', $wallet->wallet_id)
                    //         ->where('where_spend', 'in_network_transaction')
                    //         ->sum('credit');
                    // $credits_back_wellness = DB::table('wellness_wallet_history')
                    //         ->where('wallet_id', $wallet->wallet_id)
                    //         ->where('where_spend', 'credits_back_from_in_network')
                    //         ->sum('credit');
                    // $in_network_spent_wellness = $in_network_temp_spent_wellness - $credits_back_wellness;

                    // $temp_allocation_wellness = DB::table('e_wallet')
                    //     ->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
                    //     ->where('e_wallet.UserID', $user_id)
                    //     ->where('logs', 'added_by_hr')
                    //     ->sum('wellness_wallet_history.credit');

                    // $deducted_allocation_wellness = DB::table('e_wallet')
                    //     ->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
                    //     ->where('e_wallet.UserID', $user_id)
                    //     ->where('logs', 'deducted_by_hr')
                    //     ->sum('wellness_wallet_history.credit');

                    // $allocation_wellness = $temp_allocation_wellness - $deducted_allocation_wellness;
                    // $current_spending_wellness = $in_network_spent_wellness + $e_claim_spent_wellness;

   $total_allocation = $allocation;
   $total_current_spending = $current_spending;

   $total_in_network_spent = $in_network_spent;
   $total_e_claim_spent = $e_claim_spent;

   $jsonArray['balance'] = number_format($total_allocation - $total_current_spending, 2);

   $current_balance = $allocation - $current_spending;
                    // check and update user wallet
   if($wallet->balance != $current_balance) {
     DB::table('e_wallet')->where('UserID', $findUserID)->update(['balance' => $current_balance, 'updated_at' => date('Y-m-d h:i:s')]);
   }

   $jsonArray['in_network_credits_spent'] = $total_in_network_spent > 0 ? number_format($total_in_network_spent, 2) : 0.00;

   $jsonArray['e_claim_credits_spent'] = $total_e_claim_spent > 0 ? number_format($total_e_claim_spent, 2) : 0.00;
   $returnObject->data = $jsonArray;
   return Response::json($returnObject);
 } else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function getPromoCredit( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $authSession = new OauthSessions();
 $returnObject = new stdClass();
 $getRequestHeader = StringHelper::requestHeader();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
   if($findUserID){
    $promo = new NewPromoCode( );
    $input = Input::all();
    return Response::json($promo->matchPromoCode($input, $findUserID));
  } else {
    $returnObject->status = FALSE;
    $returnObject->message = StringHelper::errorMessage("Token");
    return Response::json($returnObject);
  }
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}


public function createBackUpEmail( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $user = new EmailBackup();
 $input = Input::all();
 $getRequestHeader = StringHelper::requestHeader();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
   if($findUserID){
    $check = $user->checkEmail($findUserID, $input['email']);
    if($check == 1) {
     $returnObject->status = TRUE;
                        // $data->status = TRUE;
     $returnObject->message = 'Already get the maximum of email backup.';
     return Response::json($returnObject);
   } else if($check == 2) {
     $returnObject->status = TRUE;
                        // $data->status = TRUE;
     $returnObject->message = 'Email already existed';
     return Response::json($returnObject);
   } else if($check == 3) {
     $returnObject->status = TRUE;
                        // $data->status = TRUE;
     $returnObject->message = 'Successfully created email backup.';
     return Response::json($returnObject);
   }
 } else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}


    // e card
public function getEcardDetails( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $e_card = new UserPackage();
 $input = Input::all();
 $getRequestHeader = StringHelper::requestHeader();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
   if($findUserID){
    $returnObject->status = TRUE;
    $returnObject->data = $e_card->newEcardDetails($findUserID);
                    // return $e_card->newEcardDetails($findUserID);
    if($returnObject->data == 0) {
     $returnObject->status = FALSE;
     $returnObject->message = 'User does not have a package plan.';
     $returnObject->data = FALSE;
   } elseif($returnObject->data == -1) {
     $returnObject->status = FALSE;
     $returnObject->data = FALSE;
     $returnObject->message = 'User package plan is not activated yet.';
   }
   return Response::json($returnObject);
 } else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function getNewClinicDetails($id)
{
  $AccessToken = new Api_V1_AccessTokenController();
  $returnObject = new stdClass();
  $authSession = new OauthSessions();
  $input = Input::all();
  $getRequestHeader = StringHelper::requestHeader();
  $returnObject->production = TRUE;


  if(!empty($getRequestHeader['Authorization'])){
    $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
    if($getAccessToken){
     $findUserID = $authSession->findUserID($getAccessToken->session_id);
      // return $findUserID;
     if($findUserID){
      $returnObject->status = TRUE;
      $returnObject->message = "Success.";
      $clinic = Clinic_Library_v1::FindClinicProfile($id);
      if(!$clinic) {
       $returnObject->status = FALSE;
       $returnObject->message = 'Clinic not found.';
       return Response::json($returnObject);
     }
     $clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
     $owner_id = StringHelper::getUserId($findUserID);
     $wallet_checker = DB::table('e_wallet')->where('UserID', $owner_id)->first();
     // check block access
     $block = PlanHelper::checkCompanyBlockAccess($owner_id, $id);

     if($block) {
      if($wallet_checker->currency_type === 'myr') {
        if($clinic->currency_type === 'sgd') {
            $returnObject->status = FALSE;
            $returnObject->status_type = 'access_block';
            $returnObject->head_message = 'Registration Unavailable';
            $returnObject->message = 'Sorry, your acccount is not enabled to access Singapore providers. Kindly contact your HR for more details.';
            return Response::json($returnObject);
        } else {
          $returnObject->status = FALSE;
          $returnObject->status_type = 'access_block';
          $returnObject->head_message = 'Registration Unavailable';
          $returnObject->message = 'Sorry, your account is not enabled to access this provider at the moment. Kindly contact your HR for more details.';
          return Response::json($returnObject);
        }
      } else {
        $returnObject->status = FALSE;
        $returnObject->status_type = 'access_block';
        $returnObject->head_message = 'Registration Unavailable';
        $returnObject->message = 'Sorry, your account is not enabled to access this provider at the moment. Kindly contact your HR for more details.';
        return Response::json($returnObject);
      }
     }

     // check if enable to access feature
      $transaction_access = MemberHelper::checkMemberAccessTransactionStatus($owner_id, 'panel');

      if($transaction_access)	{
        $returnObject->status = FALSE;
        $returnObject->status_type = 'access_block';
        $returnObject->head_message = 'Registration Unavailable';
        $returnObject->message = 'Sorry, your account is not enabled to access this provider at the moment. Kindly contact your HR for more details.';
        return Response::json($returnObject);
      }

      // check if employee/user is still coverge
     $user_type = PlanHelper::getUserAccountType($findUserID);

     // // check visit limit
     if($user_type == "employee") {
      $user_plan_history = DB::table('user_plan_history')->where('user_id', $owner_id)->orderBy('created_at', 'desc')->first();
      $customer_active_plan = DB::table('customer_active_plan')
      ->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
      ->first();
    } else {
      $user_plan_history = DB::table('dependent_plan_history')->where('user_id', $findUserID)->orderBy('created_at', 'desc')->first();
      $customer_active_plan = DB::table('dependent_plans')
                    ->where('dependent_plan_id', $user_plan_history->dependent_plan_id)
                    ->first();
    }

     if($customer_active_plan->account_type == "enterprise_plan")	{
      $limit = $user_plan_history->total_visit_limit - $user_plan_history->total_visit_created;

      if($limit <= 0) {
        $returnObject->status = FALSE;
        $returnObject->status_type = 'access_block';
        $returnObject->head_message = 'Registration Unavailable';
        $returnObject->message = 'Maximum of 14 visits already reached.';
        return Response::json($returnObject);
      }

      if($wallet_checker->currency_type === 'myr') {
        if($clinic->currency_type === 'sgd') {
            $returnObject->status = FALSE;
            $returnObject->status_type = 'access_block';
            $returnObject->head_message = 'Registration Unavailable';
            $returnObject->message = 'Sorry, your acccount is not enabled to access Singapore providers. Kindly contact your HR for more details.';
            return Response::json($returnObject);
        }
      }
    }

    if($user_type == "employee") {
      $plan_coverage = PlanHelper::checkEmployeePlanStatus($findUserID);
    } else {
      $plan_coverage = PlanHelper::getDependentPlanCoverage($findUserID);
    }

    if($plan_coverage['expired'] == true && $plan_coverage['plan_type'] != "out_of_pocket") {
     $returnObject->status = FALSE;
     $returnObject->status_type = 'access_block';
     $returnObject->head_message = 'Registration Unavailable';
     $returnObject->message = 'Employee Plan Coverage has expired';
     $returnObject->data = $plan_coverage;
     $returnObject->employee_status = false;
     return Response::json($returnObject);
   }

   if($plan_coverage['pending'] == true) {
     $returnObject->status = FALSE;
     $returnObject->status_type = 'access_block';
     $returnObject->head_message = 'Registration Unavailable';
     $returnObject->message = 'Employee Plan Account is still pending';
     $returnObject->data = $plan_coverage;
     $returnObject->employee_status = false;
     return Response::json($returnObject);
   }

   $current_balance = 0;
   if($customer_active_plan->account_type != "super_pro_plan") {
    //  check if lite plan user
     $current_balance = PlanHelper::reCalculateEmployeeBalance($owner_id);
   }

   $user = DB::table('user')->where('UserID', $findUserID)->first();
   $wallet = DB::table('e_wallet')->where('UserID', $owner_id)->first();

   // if($wallet->currency_type != $clinic->currency_type && $wallet->currency_type == "myr") {
   //   $returnObject->status = FALSE;
   //   $returnObject->message = 'Member is prohibited to access this clinic from Singpapore';
   //   $returnObject->employee_status = false;
   //   return Response::json($returnObject);
   // }

   $procedures = DB::table('clinic_procedure')
   ->where('ClinicID', $id)
   ->where('scan_pay_show', 1)
   ->where('Active', 1)
   ->get();


   // format clinic data
   ($clinic->Email) ? $email = $clinic->Email : $email = null;
   ($clinic->Description) ? $descr = $clinic->Description : $descr = null;
   ($clinic->Website) ? $website = $clinic->Website : $website = null;
   ($clinic->Custom_title) ? $custitle = $clinic->Custom_title : $custitle = null;
   ($clinic->Clinic_Price) ? $clprice = $clinic->Clinic_Price : $clprice = null;

   if(strpos($clinic->Phone_Code, '+') !== false) {
     if(strpos($clinic->Phone, '+') !== false) {
      $jsonArray['telephone'] = $clinic->Phone;
    } else {
      $jsonArray['telephone'] = $clinic->Phone_Code.$clinic->Phone;
    }
  } else {
   if(strpos($clinic->Phone, '+') !== false) {
    $jsonArray['telephone']= $clinic->Phone;
  } else {
    $jsonArray['telephone']= '+'.$clinic->Phone;
  }
}

$jsonArray['clinic_id'] = $clinic->ClinicID;
$jsonArray['user_id'] = $findUserID;
$jsonArray['name'] = $clinic->CLName;
$jsonArray['email'] = $email;
$jsonArray['address'] = $clinic->CLAddress.' '.$clinic->CLCity.' '.$clinic->CLState.' '.$clinic->CLPostal;
$jsonArray['image_url'] = $clinic->CLImage;
$jsonArray['member'] = ucwords($user->Name);
$jsonArray['nric'] = $user->NRIC;


$current_balance = 0;
// if($customer_active_plan->account_type != "super_pro_plan") {
//   $current_balance = PlanHelper::reCalculateEmployeeBalance($owner_id);
// }
$jsonArray['dob'] = date('d/m/Y', strtotime($user->DOB));
$jsonArray['mobile'] = $user->PhoneCode." ".$user->PhoneNo;
$jsonArray['plan_type'] = $plan_coverage['plan_type'];
$current_balance = PlanHelper::reCalculateEmployeeBalance($owner_id);
        // check if employee has plan tier cap
$customer_id = PlanHelper::getCustomerId($owner_id);

$plan_tier = null;

if($customer_id) {
 $plan_tier = DB::table('plan_tiers')
 ->join('plan_tier_users', 'plan_tier_users.plan_tier_id', '=', 'plan_tier_users.plan_tier_id')
 ->where('plan_tiers.active', 1)
 ->where('plan_tier_users.status', 1)
 ->where('plan_tier_users.user_id', $findUserID)
 ->where('plan_tiers.customer_id', $customer_id)
 ->first();
}

$cap_currency_symbol = "SGD";
$cap_amount = 0;

$currency_data = DB::table('currency_options')->where('currency_type', $wallet->currency_type)->first();
if($currency_data) {
  $currency_value = $currency_data->currency_value;
} else {
  $currency_value = 3.00;
}

if($plan_tier) {
  if($wallet->cap_per_visit_medical > 0) {
    $cap_amount = $wallet->cap_per_visit_medical;
  } else {
    $cap_amount = $plan_tier->gp_cap_per_visit;
  }
} else {
  if($wallet->cap_per_visit_medical > 0) {
    $cap_amount = $wallet->cap_per_visit_medical;
  }
}

if($customer_active_plan && $customer_active_plan->account_type == "enterprise_plan") {
  $balance = 1000;
  $current_balance = 1000;
} else {
  $balance = $current_balance;
}

$real_balance = $current_balance;

if($clinic->currency_type == "myr" && $wallet->currency_type == "sgd") {
 $currency = "MYR";
 $cap_currency_symbol = "MYR";
 $balance = number_format($balance * $currency_value, 2);
 $cap_amount = $cap_amount * $currency_value;
 $current_balance = $current_balance * $currency_value;
} else if($clinic->currency_type == "sgd" && $wallet->currency_type == "sgd"){
 $currency = "SGD";
 $balance = number_format($balance, 2);
} else if($clinic->currency_type == "myr" && $wallet->currency_type == "myr") {
  $balance = number_format($balance, 2);
  $currency = "MYR";
  $cap_currency_symbol = "MYR";
} else  if($clinic->currency_type == "sgd" && $wallet->currency_type == "myr") {
  $currency = "SGD";
  $cap_currency_symbol = "SGD";
  $balance = number_format($balance / $currency_value, 2);
  $cap_amount = $cap_amount / $currency_value;
  $current_balance = $current_balance / $currency_value;
}

if($customer_active_plan && $customer_active_plan->account_type == "enterprise_plan") {
  $currency = "";
}

$jsonArray['real_balance'] = $real_balance;
$jsonArray['current_balance'] = $currency.' '.$balance;
$jsonArray['balance'] = $current_balance;
$jsonArray['current_balance_in_sgd'] = $current_balance;
$jsonArray['currency_symbol'] = $currency;
$jsonArray['cap_currency_symbol'] = $cap_currency_symbol;
$jsonArray['cap_per_visit_amount'] = $cap_amount;

$check_in_time = date('Y-m-d H:i:s');

if(!empty($input['check_in_time']) && $input['check_in_time'] != null) {
  $check_in_time = date('Y-m-d H:i:s', strtotime($input['check_in_time']));
}

$check_in_time = date('Y-m-d H:i:s');

if(!empty($input['check_in_time']) && $input['check_in_time'] != null) {
  $check_in_time = date('Y-m-d H:i:s', strtotime($input['check_in_time']));
}

$check_in_data = array(
  'user_id'         => $findUserID,
  'clinic_id'       => $clinic->ClinicID,
  'check_in_time'   => $check_in_time,
  'check_out_time'  => $check_in_time,
  'check_in_type'   => 'in_network_transaction',
  'cap_per_visit'   => $cap_amount,
  'currency_symbol' => $clinic->currency_type == "myr" ? "myr" : "sgd",
  'currency_value'  => $clinic->currency_type == "myr" ? $currency_value : 0.00,
);

$check_in_class = new EmployeeClinicCheckIn( );
        // create clinic check in data
$check_in = $check_in_class->createData($check_in_data);
$jsonArray['check_in_id'] = $check_in->id;
$jsonArray['check_in_time'] = date('d M, h:i a', strtotime($check_in_time));
$jsonArray['check_in_expiry_time'] = date('Y-m-d H:i:s', strtotime('+120 minutes', strtotime($check_in_time)));
$returnObject->data = $jsonArray;
$returnObject->data['clinic_procedures'] = ArrayHelperMobile::ClinicProcedures($procedures);
$default_service = null;

if($clinic_type->Name == "GP" && sizeof($returnObject->data['clinic_procedures']) == 0) {
  $default_service = ClinicHelper::getDefaultService( );
} else if($clinic_type->Name != "GP" && sizeof($returnObject->data['clinic_procedures']) == 0){
  $service = DB::table('clinic_procedure')
              ->where('ClinicID', $clinic->ClinicID)
              ->where('Active', 1)
              ->orderBy('Position', 'asc')
              ->first();

  if($service) {
    $service_result = ClinicHelper::getServiceDetails($service->ProcedureID);
    if($service_result) {
      $default_service = $service_result;
    } else {
      $default_service = ClinicHelper::getDefaultService( );
    }
  } else {
    $default_service = ClinicHelper::getDefaultService( );
  }
}
// get transaction consultation
$returnObject->data['default_service'] = $default_service;
$returnObject->data['consultation_fee_symbol'] = "SGD";
$consultation_status = StringHelper::newLitePlanStatus($findUserID);
$returnObject->data['consultation_status'] = $consultation_status;
if($consultation_status == true && (int)$clinic_type->lite_plan_enabled == 1) {
  $clinic_co_payment = TransactionHelper::getCoPayment($clinic, date('Y-m-d H:i:s'), $owner_id);
  $consultation_fees = $clinic_co_payment['consultation_fees'] == 0 ? $clinic->consultation_fees : $clinic_co_payment['consultation_fees'];
  $returnObject->data['consultation_fees'] = $clinic->currency_type == "myr" ? $consultation_fees * $currency_value : $consultation_fees;
  $returnObject->data['consultation_fee_symbol'] = $clinic->currency_type == "myr" ? "MYR" : "SGD";
} else {
  $returnObject->data['consultation_fee_symbol'] = null;
  $returnObject->data['consultation_fees'] = 0;
}

        // send socket connection
PusherHelper::sendClinicCheckInNotification($check_in->id, $clinic->ClinicID);
return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

        // mobile payments
public function getClinicDetails($id)
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
            // $input = Input::all();
 $getRequestHeader = StringHelper::requestHeader();
            // if(StringHelper::Deployment() == 1){
 $returnObject->production = TRUE;
            // } else {
            //     $returnObject->production = FALSE;
            // }
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
                    // return $findUserID;
   if($findUserID){
    $returnObject->status = TRUE;
    $returnObject->message = "Success.";
    $clinic = Clinic_Library_v1::FindClinicProfile($id);
    if(!$clinic) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Clinic not found.';
     return Response::json($returnObject);
   }

   $procedures = DB::table('clinic_procedure')
   ->where('ClinicID', $id)
   ->where('scan_pay_show', 1)
   ->where('Active', 1)
   ->get();
   if(!$procedures) {
     $returnObject->status = FALSE;
     $returnObject->message = "Clinic ".$clinic->CLName." does not have services.";
     return Response::json($returnObject);
   }

                        // format clinic data
   ($clinic->Email) ? $email = $clinic->Email : $email = null;
   ($clinic->Description) ? $descr = $clinic->Description : $descr = null;
   ($clinic->Website) ? $website = $clinic->Website : $website = null;
   ($clinic->Custom_title) ? $custitle = $clinic->Custom_title : $custitle = null;
   ($clinic->Clinic_Price) ? $clprice = $clinic->Clinic_Price : $clprice = null;

   if(strpos($clinic->Phone_Code, '+') !== false) {
     if(strpos($clinic->Phone, '+') !== false) {
      $jsonArray['telephone'] = $clinic->Phone;
    } else {
      $jsonArray['telephone'] = $clinic->Phone_Code.$clinic->Phone;
    }
  } else {
   if(strpos($clinic->Phone, '+') !== false) {
    $jsonArray['telephone']= $clinic->Phone;
  } else {
    $jsonArray['telephone']= '+'.$clinic->Phone;
  }
}

$jsonArray['clinic_id'] = $clinic->ClinicID;
$jsonArray['name'] = $clinic->CLName;
$jsonArray['email'] = $email;
$jsonArray['address'] = $clinic->CLAddress.' '.$clinic->CLCity.' '.$clinic->CLState.' '.$clinic->CLPostal;
$jsonArray['image_url'] = $clinic->CLImage;
$jsonArray['lattitude'] = $clinic->CLLat;
$jsonArray['longitude'] = $clinic->CLLng;
$jsonArray['description'] = $descr;
$jsonArray['website'] = $website;
$jsonArray['custom_title'] = $custitle;
$jsonArray['clinic_price'] = $clprice;


$credits = DB::table('e_wallet')->where('UserID', $findUserID)->first();

                        // get medical credits spent
$e_claim_spent = DB::table('wallet_history')
->where('wallet_id', $credits->wallet_id)
->where('where_spend', 'e_claim_transaction')
->sum('credit');

$in_network_temp_spent = DB::table('wallet_history')
->where('wallet_id', $credits->wallet_id)
->where('where_spend', 'in_network_transaction')
->sum('credit');
$credits_back = DB::table('wallet_history')
->where('wallet_id', $credits->wallet_id)
->where('where_spend', 'credits_back_from_in_network')
->sum('credit');
$in_network_spent = $in_network_temp_spent - $credits_back;
                        // get e_claim last 3 transactions
$e_claim_result = DB::table('e_claim')
->where('user_id', $findUserID)
->orderBy('created_at', 'desc')
->take(3)
->get();

                            // get credits allocation
$temp_allocation = DB::table('e_wallet')
->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
->where('e_wallet.UserID', $findUserID)
->whereIn('logs', ['added_by_hr'])
->sum('wallet_history.credit');
$deducted_allocation = DB::table('e_wallet')
->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
->where('e_wallet.UserID', $findUserID)
->whereIn('logs', ['deducted_by_hr'])
->sum('wallet_history.credit');

$allocation = $temp_allocation - $deducted_allocation;
$current_spending = $in_network_spent + $e_claim_spent;

$jsonArray['current_balance'] = number_format($allocation - $current_spending, 2);
$current_balance = $allocation - $current_spending;
                        // check and update user wallet
if($credits->balance != $current_balance) {
 DB::table('e_wallet')->where('UserID', $findUserID)->update(['balance' => $current_balance, 'updated_at' => date('Y-m-d h:i:s')]);
}
$returnObject->data = $jsonArray;
$returnObject->data['clinic_procedures'] = ArrayHelperMobile::ClinicProcedures($procedures);
return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function floatvalue($val){
 return str_replace(",", "", $val);
 $val = str_replace(",",".",$val);
 $val = preg_replace('/\.(?=.*\.)/', '', $val);
 return floatval($val);
}

public function payCredits( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();

 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);

  if($getAccessToken){
    $findUserID = $authSession->findUserID($getAccessToken->session_id);

    if($findUserID){
      $email = [];
      if(!isset($input['services'])) {
       $returnObject->status = FALSE;
       $returnObject->message = 'Please choose a service.';
       return Response::json($returnObject);
     } else if(sizeof($input['services']) == 0) {
       $returnObject->status = FALSE;
       $returnObject->message = 'Please choose a service.';
       return Response::json($returnObject);
     }

     if(!isset($input['clinic_id'])) {
       $returnObject->status = FALSE;
       $returnObject->message = 'Please choose a clinic.';
       return Response::json($returnObject);
     }

     if(!isset($input['amount'])) {
       $returnObject->status = FALSE;
       $returnObject->message = 'Please enter an amount.';
       return Response::json($returnObject);
     }

         // check if employee/user is still coverge
     $user_type = PlanHelper::getUserAccountType($findUserID);

     if($user_type == "employee") {
      $plan_coverage = PlanHelper::checkEmployeePlanStatus($findUserID);
    } else {
      $plan_coverage = PlanHelper::getDependentPlanCoverage($findUserID);
    }

    if($plan_coverage['expired'] == true) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Employee Plan Coverage is expired';
     $returnObject->data = $plan_coverage;
     return Response::json($returnObject);
   }

   $block = PlanHelper::checkCompanyBlockAccess($findUserID, $input['clinic_id']);

   if($block) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Clinic not accessible to your Company. Please contact Your company for more information.';
     return Response::json($returnObject);
   }

   $lite_plan_status = false;
   $clinic_peak_status = false;

   $currency = 3.00;
   $service_id = $input['services'][0];
                        // check user type
   $type = StringHelper::checkUserType($findUserID);
   $lite_plan_status = StringHelper::newLitePlanStatus($findUserID);

   $user = DB::table('user')->where('UserID', $findUserID)->first();
   if((int)$type['user_type'] == 5 && (int)$type['access_type'] == 0 || (int)$type['user_type'] == 5 && (int)$type['access_type'] == 1)
   {
     $user_id = $findUserID;
     $customer_id = $findUserID;
     $email_address = $user->Email;
     $dependent_user = false;
   } else {
                            // find owner
     $owner = DB::table('employee_family_coverage_sub_accounts')
     ->where('user_id', $findUserID)
     ->first();
     $user_id = $owner->owner_id;
     $user_email = DB::table('user')->where('UserID', $user_id)->first();
     $email_address = $user_email->Email;
     $customer_id = $findUserID;
     $dependent_user = true;
   }

          // get clinic info and type
   $clinic = DB::table('clinic')->where('ClinicID', $input['clinic_id'])->first();
   $clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
         // check user credits and amount key in
   $spending_type = "medical";
   $credits = DB::table('e_wallet')->where('UserID', $user_id)->first();
   $consultation_fees = 0;

   if($clinic_type->spending_type == "medical") {
     $user_credits = self::floatvalue($credits->balance);
     $spending_type = "medical";
   } else {
     $user_credits = self::floatvalue($credits->wellness_balance);
     $spending_type = "wellness";
   }

   $input_amount = self::floatvalue($input['amount']);
   if($clinic->currency_type == "myr") {
     $total_amount = $input_amount / 3;
   } else {
     $total_amount = $input_amount;
   }

   $peak_amount = 0;
   $clinic_co_payment = TransactionHelper::getCoPayment($clinic, date('Y-m-d H:i:s'), $user_id);
   $peak_amount = $clinic_co_payment['peak_amount'];
   $co_paid_amount = $clinic_co_payment['co_paid_amount'];
   $co_paid_status = $clinic_co_payment['co_paid_status'];
   $clinic_peak_status = $clinic_co_payment['clinic_peak_status'];
   $consultation_fees = $clinic_co_payment['consultation_fees'] == 0 ? $clinic->consultation_fees : $clinic_co_payment['consultation_fees'];

          // check if user has a plan tier
   $plan_tier = PlanHelper::getEmployeePlanTier($customer_id);

   if($plan_tier) {
            // check medical cap
    if($plan_tier->medical_annual_cap != 0) {
      $medical_cap = PlanHelper::getEmployeeAnnualCapMedical($user_id);

      if($medical_cap > $plan_tier->medical_annual_cap) {
       $returnObject->status = FALSE;
       $returnObject->message = 'You have hit the maximum medical annual cap as you are in Plan tier member.';
       $returnObject->sub_mesage = 'Your maximum medical annual cap is '.number_format($plan_tier->medical_annual_cap, 2).'.';
       return Response::json($returnObject);
     }
   }

   if($plan_tier->wellness_annual_cap != 0) {
    $wellness_cap = PlanHelper::getEmployeeAnnualCapWellness($user_id);
    if($wellness_cap > $plan_tier->wellness_annual_cap) {
     $returnObject->status = FALSE;
     $returnObject->message = 'You have hit the maximum wellness annual cap as you are in Plan tier member.';
     $returnObject->sub_mesage = 'Your maximum medical annual cap is '.number_format($plan_tier->medical_annual_cap, 2).'.';
     return Response::json($returnObject);
   }
 }

 if((int)$clinic_type->consultation == 1) {
  if($plan_tier->gp_cap_per_visit != 0) {
   if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
    $total_credits = self::floatvalue($total_amount) + $consultation_fees;
  } else {
    $total_credits =self::floatvalue($total_amount);
  }

  if($total_credits > $plan_tier->gp_cap_per_visit) {
    $returnObject->status = FALSE;
    $returnObject->message = 'You have hit the maximum GP CAP PER VISIT as you are in Plan tier member.';
    $returnObject->sub_mesage = 'You can only pay '.number_format($plan_tier->gp_cap_per_visit, 2).' Per GP Visit.';
    return Response::json($returnObject);
  }
}
}
} else {
  if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
   $total_credits = self::floatvalue($total_amount) + $consultation_fees;
   if($total_credits > $user_credits) {
    $returnObject->status = FALSE;
    $returnObject->message = 'You have insufficient '.$spending_type.' credits in your account';
    $returnObject->sub_mesage = 'Lite Plan users needs an additional S$'.number_format($co_paid_amount, 2).' to be able to pay for the transaction.';
    return Response::json($returnObject);
  }
} else {
  $total_credits = self::floatvalue($total_amount);
  if(self::floatvalue($total_amount) > $user_credits) {
    $returnObject->status = FALSE;
    $returnObject->message = 'You have insufficient '.$spending_type.' credits in your account';
    $returnObject->sub_mesage = 'You may choose to pay directly to health provider.';
    return Response::json($returnObject);
  }
}
}
          // return $total_credits * 3;
$transaction = new Transaction();
$wallet = new Wallet( );

          // check if multiple services selected
$multiple = false;
if(sizeof($input['services']) > 1) {
 $services = 0;
 $multiple_service_selection = 1;
 $multiple = true;
} else {
 $services = $input['services'][0];
 $multiple_service_selection = 0;
 $multiple = false;
}

$consultation = 0;
if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
 $lite_plan_enabled = 1;
} else {
 $lite_plan_enabled = 0;
}

$data = array(
 'UserID'                => $customer_id,
 'ProcedureID'           => $services,
 'date_of_transaction'   => date('Y-m-d H:i:s'),
 'claim_date'            => date('Y-m-d H:i:s'),
 'ClinicID'              => $input['clinic_id'],
 'procedure_cost'        => $total_amount,
 'AppointmenID'          => 0,
 'revenue'               => 0,
 'debit'                 => 0,
 'clinic_discount'       => $clinic->discount,
 'medi_percent'          => $clinic->medicloud_transaction_fees,
 'currency_type'         => $clinic->currency_type,
 'wallet_use'            => 1,
 'current_wallet_amount' => $credits->balance,
 'credit_cost'           => $total_amount ,
 'paid'                  => 1,
 'co_paid_status'            => $co_paid_status,
 'co_paid_amount'            => $co_paid_amount,
 'DoctorID'              => 0,
 'backdate_claim'        => 1,
 'in_network'            => 1,
 'mobile'                => 1,
 'multiple_service_selection' => $multiple_service_selection,
 'currency_type'         => $clinic->currency_type,
 'lite_plan_enabled'     => $lite_plan_enabled,
 'consultation_fees'     => $consultation_fees
);

if((int)$clinic_type->lite_plan_enabled == 1 && $lite_plan_status) {
 if($clinic->currency_type == "myr") {
  $consultation = number_format($co_paid_amount / 3, 2);
} else {
  $consultation = number_format($co_paid_amount, 2);
}
}

if($clinic_peak_status) {
  $data['peak_hour_status'] = 1;
  if($clinic->co_paid_status == 1 || $clinic->co_paid_status == "1") {
    $gst_peak = $peak_amount * $clinic->gst_percent;
    $data['peak_hour_amount'] = $peak_amount + $gst_peak;
  } else {
    $data['peak_hour_amount'] = $peak_amount;
  }
}

if($currency) {
  $data['currency_amount'] = $currency;
}

try {
  $result = $transaction->createTransaction($data);
  $transaction_id = $result->id;

  if($result) {
    $procedure = "";
    $procedure_temp = "";
              // insert transation services
    $ts = new TransctionServices( );
    $save_ts = $ts->createTransctionServices($input['services'], $transaction_id);

    if($multiple == true) {
      foreach ($input['services'] as $key => $value) {
        $procedure_data = DB::table('clinic_procedure')->where('ProcedureID', $value)->first();
        $procedure_temp .= ucwords($procedure_data->Name).',';
      }
      $procedure = rtrim($procedure_temp, ',');
    } else {
     $procedure_data = DB::table('clinic_procedure')->where('ProcedureID', $service_id)->first();
     $procedure = ucwords($procedure_data->Name);
   }


              // deduct medical/wellness credit
   $history = new WalletHistory( );

   if($spending_type == "medical") {
    $credits_logs = array(
      'wallet_id'     => $credits->wallet_id,
      'credit'        => $total_amount,
      'logs'          => 'deducted_from_mobile_payment',
      'running_balance' => $credits->balance - $total_amount,
      'where_spend'   => 'in_network_transaction',
      'id'            => $transaction_id
    );

    if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
      $lite_plan_credits_log = array(
        'wallet_id'     => $credits->wallet_id,
        'credit'        => $consultation_fees,
        'logs'          => 'deducted_from_mobile_payment',
        'running_balance' => $credits->balance - $total_amount - $consultation_fees,
        'where_spend'   => 'in_network_transaction',
        'id'            => $transaction_id,
        'lite_plan_enabled' => 1,
      );
    }
  } else {
    $credits_logs = array(
      'wallet_id'     => $credits->wallet_id,
      'credit'        => $input_amount,
      'logs'          => 'deducted_from_mobile_payment',
      'running_balance' => $credits->wellness_balance - $total_amount,
      'where_spend'   => 'in_network_transaction',
      'id'            => $transaction_id
    );
    if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
      $lite_plan_credits_log = array(
        'wallet_id'     => $credits->wallet_id,
        'credit'        => $consultation_fees,
        'logs'          => 'deducted_from_mobile_payment',
        'running_balance' => $credits->wellness_balance - $total_amount - $consultation_fees,
        'where_spend'   => 'in_network_transaction',
        'id'            => $transaction_id,
        'lite_plan_enabled' => 1,
      );
    }
  }

  try {
    if($spending_type == "medical") {
      $deduct_history = \WalletHistory::create($credits_logs);
      $wallet_history_id = $deduct_history->id;

      if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
       \WalletHistory::create($lite_plan_credits_log);
     }
   } else {
    $deduct_history = \WellnessWalletHistory::create($credits_logs);
    $wallet_history_id = $deduct_history->id;

    if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
     \WellnessWalletHistory::create($lite_plan_credits_log);
   }
 }

 if($deduct_history) {
  try {
    if($spending_type == "medical") {
      $wallet->deductCredits($user_id, $total_amount);

      if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
       $wallet->deductCredits($user_id, $consultation_fees);
     }
   } else {
    $wallet->deductWellnessCredits($user_id, $total_amount);

    if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
     $wallet->deductWellnessCredits($user_id, $consultation_fees);
   }
 }

 $trans_id = str_pad($transaction_id, 6, "0", STR_PAD_LEFT);

 $SGD = null;

 if($clinic->currency_type == "myr") {
  $currency_symbol = "RM ";
  $email_currency_symbol = "RM";
                      // $total_amount = $total_amount * 3;
} else {
  $email_currency_symbol = "S$";
  $currency_symbol = '$SGD ';
}

$transaction_results = array(
  'clinic_name'       => ucwords($clinic->Name),
  'amount'            => number_format($input_amount, 2),
  'transaction_time'  => date('Y-m-d h:i', strtotime($result->created_at)),
  'transation_id'     => strtoupper(substr($clinic->Name, 0, 3)).$trans_id,
  'services'          => $procedure,
  'currency_symbol'   => $email_currency_symbol,
  'dependent_user'    => $dependent_user
);

Notification::sendNotification('Customer Payment - Mednefits', 'User '.ucwords($user->Name).' has made a payment for '.$procedure.' at '.$currency_symbol.$input_amount.' to your clinic', url('app/setting/claim-report', $parameter = array(), $secure = null), $input['clinic_id'], $user->Image);

$type = "";
$image = "";
if($clinic_type->head == 1 || $clinic_type->head == "1") {
  if($clinic_type->Name == "General Practitioner") {
   $type = "General Practitioner";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
 } else if($clinic_type->Name == "Dental Care") {
   $type = "Dental Care";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
 } else if($clinic_type->Name == "Traditional Chinese Medicine") {
   $type = "Traditional Chinese Medicine";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
 } else if($clinic_type->Name == "Health Screening") {
   $type = "Health Screening";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
 } else if($clinic_type->Name == "Wellness") {
   $type = "Wellness";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
 } else if($clinic_type->Name == "Health Specialist") {
   $type = "Health Specialist";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
 }
} else {
  $find_head = DB::table('clinic_types')
  ->where('ClinicTypeID', $clinic_type->sub_id)
  ->first();
  if($find_head->Name == "General Practitioner") {
   $type = "General Practitioner";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
 } else if($find_head->Name == "Dental Care") {
   $type = "Dental Care";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
 } else if($find_head->Name == "Traditional Chinese Medicine") {
   $type = "Traditional Chinese Medicine";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
 } else if($find_head->Name == "Health Screening") {
   $type = "Health Screening";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
 } else if($find_head->Name == "Wellness") {
   $type = "Wellness";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
 } else if($find_head->Name == "Health Specialist") {
   $type = "Health Specialist";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
 }
}

if($clinic->currency_type == "myr") {
  if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
    $final_amount = $total_credits * 3;
  } else {
    $final_amount = $total_amount * 3;
  }
} else {
  $final_amount = $total_credits;
}

$email['member'] = ucwords($user->Name);
$email['credits'] = number_format($input_amount, 2);
$email['transaction_id'] = strtoupper(substr($clinic->Name, 0, 3)).$trans_id;
$email['trans_id'] = $transaction_id;
$email['transaction_date'] = date('d F Y, h:ia');
$email['health_provider_name'] = ucwords($clinic->Name);
$email['health_provider_address'] = $clinic->Address;
$email['health_provider_city'] = $clinic->City;
$email['health_provider_country'] = $clinic->Country;
$email['health_provider_phone'] = $clinic->Phone;
$email['service'] = ucwords($clinic_type->Name).' - '.$procedure;
$email['emailSubject'] = 'Member - Successful Transaction';
$email['emailTo'] = $email_address ? $email_address : 'info@medicloud.sg';
                    // $email['emailTo'] = 'allan.alzula.work@gmail.com';
$email['emailName'] = ucwords($user->Name);
$email['clinic_type_image'] = $image;
$email['transaction_type'] = 'Mednefits Credits';
$email['emailPage'] = 'email-templates.member-successful-transaction-v2';
$email['dl_url'] = url();
$email['lite_plan_enabled'] = $clinic_type->lite_plan_enabled;
$email['lite_plan_status'] = $lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1 ? TRUE : FALSE ;
$email['total_amount'] = number_format($final_amount, 2);
$email['consultation'] = $consultation_fees;
$email['currency_symbol'] = $email_currency_symbol;
$email['pdf_file'] = 'pdf-download.member-successful-transac-v2';

try {
  EmailHelper::sendPaymentAttachment($email);
  $clinic_email = DB::table('user')->where('UserType', 3)->where('Ref_ID', $input['clinic_id'])->first();

  if($clinic_email) {
   $email['emailSubject'] = 'Health Partner - Successful Transaction By Mednefits Credits';
   $email['nric'] = $user->NRIC;
   $email['emailTo'] = $clinic_email->Email;
                                                                                // $email['emailTo'] = 'allan.alzula.work@gmail.com';
   $email['emailPage'] = 'email-templates.health-partner-successful-transaction-v2';
   $api = "https://admin.medicloud.sg/send_clinic_transaction_email";
   $email['pdf_file'] = 'pdf-download.health-partner-successful-transac-v2';
                                                          // httpLibrary::postHttp($api, $email, array());
   EmailHelper::sendPaymentAttachment($email);
 }
 $returnObject->status = TRUE;
 $returnObject->message = 'Payment Successfull';
 $returnObject->data = $transaction_results;
} catch(Exception $e) {
  $email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
  $email['logs'] = 'Mobile Payment Credits Send Email Attachments - '.$e->getMessage();
  $email['emailSubject'] = 'Error log.';
  EmailHelper::sendErrorLogs($email);
  $returnObject->status = TRUE;
  $returnObject->message = 'Payment Successfull';
  $returnObject->data = $transaction_results;
}

} catch(Exception $e) {
  $email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
  $email['logs'] = 'Mobile Payment Credits - '.$e->getMessage();
  $email['emailSubject'] = 'Error log. - Transaction ID: '.$transaction_id.' Wallet History ID: '.$wallet_history_id;
                      // delete transaction history log
  $transaction->deleteFailedTransactionHistory($transaction_id);
                                                                    // delete failed wallet history
  if($spending_type == "medical") {
    $history->deleteFailedWalletHistory($wallet_history_id);
                                                                        // credits back
    $wallet->addCredits($user_id, $input['amount']);
  } else {
    \WellnessWalletHistory::where('wellness_wallet_history_id', $wallet_history_id)->delete();
    $wallet->addWellnessCredits($user_id, $input['amount']);
  }
  $returnObject->status = FALSE;
  $returnObject->message = 'Payment unsuccessfull. Please try again later';
  EmailHelper::sendErrorLogs($email);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = 'Payment unsuccessfull. Please try again later';
}
} catch(Exception $e) {
  $email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
  $email['logs'] = 'Mobile Payment Credits - '.$e;
  $email['emailSubject'] = 'Error log. - Transaction ID: '.$transaction_id;

                                                        // delete transaction history log
  $transaction->deleteFailedTransactionHistory($transaction_id);

  $returnObject->status = FALSE;
  $returnObject->message = 'Payment unsuccessfull. Please try again later';

  EmailHelper::sendErrorLogs($email);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = 'Payment unsuccessfull. Please try again later';
}
} catch(Exception $e) {
  $returnObject->status = FALSE;
  $returnObject->message = 'Cannot process payment credits. Please try again.';
            // send email logs
  $email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
  $email['logs'] = 'Mobile Payment Credits - '.$e;
  $email['emailSubject'] = 'Error log.';
  EmailHelper::sendErrorLogs($email);
}
return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}


    // check pin
public function checkUserPin( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
        // $input =  json_decode(file_get_contents('php://input'), true);
 $input = Input::all();

 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
                // return $findUserID;
   if($findUserID){
    $email = [];
    if(!isset($input['services'])) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Please choose a service.';
     return Response::json($returnObject);
   } else if(sizeof($input['services']) == 0) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Please choose a service.';
     return Response::json($returnObject);
   }

   if(!isset($input['clinic_id'])) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Please choose a clinic.';
     return Response::json($returnObject);
   }

   if(!isset($input['amount'])) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Please enter an amount.';
     return Response::json($returnObject);
   }


   $lite_plan_status = false;
   $clinic_peak_status = false;
                    // $currency = StringHelper::getMYRSGD();
   $currency = 3.00;
   $service_id = $input['services'][0];
                    // check user type
   $type = StringHelper::checkUserType($findUserID);
   $lite_plan_status = StringHelper::litePlanStatus($findUserID);

   $user = DB::table('user')->where('UserID', $findUserID)->first();
   if((int)$type['user_type'] == 5 && (int)$type['access_type'] == 0 || (int)$type['user_type'] == 5 && (int)$type['access_type'] == 1)
   {
     $user_id = $findUserID;
     $customer_id = $findUserID;
     $email_address = $user->Email;
   } else {
                        // find owner
     $owner = DB::table('employee_family_coverage_sub_accounts')
     ->where('user_id', $findUserID)
     ->first();
     $user_id = $owner->owner_id;
     $user_email = DB::table('user')->where('UserID', $user_id)->first();
     $email_address = $user_email->Email;
     $customer_id = $findUserID;
   }

                    // get clinic info and type
   $clinic = DB::table('clinic')->where('ClinicID', $input['clinic_id'])->first();
   $clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
                    // check user credits and amount key in
   $spending_type = "medical";
   $credits = DB::table('e_wallet')->where('UserID', $user_id)->first();


   if($clinic_type->spending_type == "medical") {
     $user_credits = self::floatvalue($credits->balance);
     $spending_type = "medical";
   } else {
     $user_credits = self::floatvalue($credits->wellness_balance);
     $spending_type = "wellness";
   }

   $peak_amount = 0;

                    // check clinic peak hours
   $result = ClinicHelper::getCheckClinicPeakHour($clinic, date('Y-m-d H:i:s'));
   if($result['status']) {
     $peak_amount = $result['amount'];
     $clinic_peak_status = true;

                    // check user company peak status
     $user_peak = PlanHelper::getUserCompanyPeakStatus($user_id);
     if($user_peak) {
      if($clinic->co_paid_status == 1 || $clinic->co_paid_status == "1") {
       $gst = $clinic->peak_hour_amount * $clinic->gst_percent;
       $co_paid_amount = $clinic->peak_hour_amount + $gst;
       $co_paid_status = $clinic->co_paid_status;
     } else {
       $co_paid_amount = $clinic->peak_hour_amount;
       $co_paid_status = $clinic->co_paid_status;
     }
   }
   else {
    if($clinic->co_paid_status == 1 || $clinic->co_paid_status == "1") {
     $gst = $clinic->co_paid_amount * $clinic->gst_percent;
     $co_paid_amount = $clinic->co_paid_amount + $gst;
     $co_paid_status = $clinic->co_paid_status;
   } else {
     $co_paid_amount = $clinic->co_paid_amount;
     $co_paid_status = $clinic->co_paid_status;
   }
 }
} else {
 if($clinic->co_paid_status == 1 || $clinic->co_paid_status == "1") {
  $gst = $clinic->co_paid_amount * $clinic->gst_percent;
  $co_paid_amount = $clinic->co_paid_amount + $gst;
  $co_paid_status = $clinic->co_paid_status;
} else {
  $co_paid_amount = $clinic->co_paid_amount;
  $co_paid_status = $clinic->co_paid_status;
}
}

if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
 $total_credits = self::floatvalue($input['amount']) + $co_paid_amount;
 if($total_credits > $user_credits) {
  $returnObject->status = FALSE;
  $returnObject->message = 'You have insufficient '.$spending_type.' credits in your account';
  $returnObject->sub_mesage = 'Lite Plan users needs an additional S$'.number_format($co_paid_amount, 2).' to be able to pay for the transaction.';
  return Response::json($returnObject);
}
} else {
 if(self::floatvalue($input['amount']) > $user_credits) {
  $returnObject->status = FALSE;
  $returnObject->message = 'You have insufficient '.$spending_type.' credits in your account';
  $returnObject->sub_mesage = 'You may choose to pay directly to health provider.';
  return Response::json($returnObject);
}
}

                    // return self::floatvalue($input['amount']);

                    // else {
$transaction = new Transaction();
$wallet = new Wallet( );

                        // check if multiple services selected
$multiple = false;
if(sizeof($input['services']) > 1) {
 $services = 0;
 $multiple_service_selection = 1;
 $multiple = true;
} else {
 $services = $input['services'][0];
 $multiple_service_selection = 0;
 $multiple = false;
}

$total_amount = $input['amount'];
$consultation = 0;

if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
 $lite_plan_enabled = 1;
} else {
 $lite_plan_enabled = 0;
}

$data = array(
 'UserID'                => $customer_id,
 'ProcedureID'           => $services,
 'date_of_transaction'   => date('Y-m-d H:i:s'),
 'claim_date'            => date('Y-m-d H:i:s'),
 'ClinicID'              => $input['clinic_id'],
 'procedure_cost'        => $input['amount'],
 'AppointmenID'          => 0,
 'revenue'               => 0,
 'debit'                 => 0,
 'clinic_discount'       => $clinic->discount,
 'medi_percent'          => $clinic->medicloud_transaction_fees,
 'currency_type'         => $clinic->currency_type,
 'wallet_use'            => 1,
 'current_wallet_amount' => $credits->balance,
 'credit_cost'           => $input['amount'],
 'paid'                  => 1,
 'co_paid_status'            => $co_paid_status,
 'co_paid_amount'            => $co_paid_amount,
 'DoctorID'              => 0,
 'backdate_claim'        => 1,
 'in_network'            => 1,
 'mobile'                => 1,
 'multiple_service_selection' => $multiple_service_selection,
 'currency_type'         => $clinic->currency_type,
 'lite_plan_enabled'     => $lite_plan_enabled
);

if((int)$clinic_type->lite_plan_enabled == 1 && $lite_plan_status) {
 $total_amount = $input['amount'] + $co_paid_amount;
 $consultation = number_format($co_paid_amount, 2);
}

if($clinic_peak_status) {
 $data['peak_hour_status'] = 1;
 if($clinic->co_paid_status == 1 || $clinic->co_paid_status == "1") {
  $gst_peak = $peak_amount * $clinic->gst_percent;
  $data['peak_hour_amount'] = $peak_amount + $gst_peak;
} else {
  $data['peak_hour_amount'] = $peak_amount;
}

}

if($currency) {
 $data['currency_amount'] = $currency;
}

                        // return $data;

try {
 $result = $transaction->createTransaction($data);
 $transaction_id = $result->id;
 if($result) {
  $procedure = "";
  $procedure_temp = "";
                                // insert transation services
  $ts = new TransctionServices( );
  $save_ts = $ts->createTransctionServices($input['services'], $transaction_id);

  if($multiple == true) {
   foreach ($input['services'] as $key => $value) {
    $procedure_data = DB::table('clinic_procedure')->where('ProcedureID', $value)->first();
    $procedure_temp .= ucwords($procedure_data->Name).',';
  }
  $procedure = rtrim($procedure_temp, ',');
} else {
 $procedure_data = DB::table('clinic_procedure')->where('ProcedureID', $service_id)->first();
 $procedure = ucwords($procedure_data->Name);
}

                                // deduct medical/wellness credit
$history = new WalletHistory( );
if($spending_type == "medical") {
 $credits_logs = array(
   'wallet_id'     => $credits->wallet_id,
   'credit'        => $input['amount'],
   'logs'          => 'deducted_from_mobile_payment',
   'running_balance' => $credits->balance - $input['amount'],
   'where_spend'   => 'in_network_transaction',
   'id'            => $transaction_id
 );

 if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
  $lite_plan_credits_log = array(
    'wallet_id'     => $credits->wallet_id,
    'credit'        => $co_paid_amount,
    'logs'          => 'deducted_from_mobile_payment',
    'running_balance' => $credits->balance - $input['amount'] - $co_paid_amount,
    'where_spend'   => 'in_network_transaction',
    'id'            => $transaction_id,
    'lite_plan_enabled' => 1,
  );
}
} else {
 $credits_logs = array(
   'wallet_id'     => $credits->wallet_id,
   'credit'        => $input['amount'],
   'logs'          => 'deducted_from_mobile_payment',
   'running_balance' => $credits->wellness_balance - $input['amount'],
   'where_spend'   => 'in_network_transaction',
   'id'            => $transaction_id
 );

 if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
  $lite_plan_credits_log = array(
    'wallet_id'     => $credits->wallet_id,
    'credit'        => $co_paid_amount,
    'logs'          => 'deducted_from_mobile_payment',
    'running_balance' => $credits->balance - $input['amount'] - $co_paid_amount,
    'where_spend'   => 'in_network_transaction',
    'id'            => $transaction_id,
    'lite_plan_enabled' => 1,
  );
}
}

try {

 if($spending_type == "medical") {
  $deduct_history = \WalletHistory::create($credits_logs);
  $wallet_history_id = $deduct_history->id;

  if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
   \WalletHistory::create($lite_plan_credits_log);
 }
} else {
  $deduct_history = \WellnessWalletHistory::create($credits_logs);
  $wallet_history_id = $deduct_history->id;

  if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
   \WellnessWalletHistory::create($lite_plan_credits_log);
 }
}


if($deduct_history) {
  try {
   if($spending_type == "medical") {
    $wallet->deductCredits($user_id, $input['amount']);

    if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
     $wallet->deductCredits($user_id, $co_paid_amount);
   }
 } else {
  $wallet->deductWellnessCredits($user_id, $input['amount']);

  if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
   $wallet->deductWellnessCredits($user_id, $co_paid_amount);
 }
}

$trans_id = str_pad($transaction_id, 6, "0", STR_PAD_LEFT);

if($lite_plan_status && $clinic_type->lite_plan_enabled == 1) {
  $total_amount = $consultation + $input['amount'];
} else {
  $total_amount = $input['amount'];
}
$transaction_results = array(
  'clinic_name'       => ucwords($clinic->Name),
  'amount'            => number_format($input['amount'], 2),
  'transaction_time'  => date('Y-m-d h:i', strtotime($result->created_at)),
  'transation_id'     => strtoupper(substr($clinic->Name, 0, 3)).$trans_id,
  'services'          => $procedure
);

Notification::sendNotification('Customer Payment - Mednefits', 'User '.ucwords($user->Name).' has made a payment for '.$procedure.' at $SGD'.$total_amount.' to your clinic', url('app/setting/claim-report', $parameter = array(), $secure = null), $input['clinic_id'], $user->Image);


$type = "";
$image = "";
if($clinic_type->head == 1 || $clinic_type->head == "1") {
  if($clinic_type->Name == "General Practitioner") {
   $type = "General Practitioner";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
 } else if($clinic_type->Name == "Dental Care") {
   $type = "Dental Care";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
 } else if($clinic_type->Name == "Traditional Chinese Medicine") {
   $type = "Traditional Chinese Medicine";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
 } else if($clinic_type->Name == "Health Screening") {
   $type = "Health Screening";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
 } else if($clinic_type->Name == "Wellness") {
   $type = "Wellness";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
 } else if($clinic_type->Name == "Health Specialist") {
   $type = "Health Specialist";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
 }
} else {
  $find_head = DB::table('clinic_types')
  ->where('ClinicTypeID', $clinic_type->sub_id)
  ->first();
  if($find_head->Name == "General Practitioner") {
   $type = "General Practitioner";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
 } else if($find_head->Name == "Dental Care") {
   $type = "Dental Care";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
 } else if($find_head->Name == "Traditional Chinese Medicine") {
   $type = "Traditional Chinese Medicine";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
 } else if($find_head->Name == "Health Screening") {
   $type = "Health Screening";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
 } else if($find_head->Name == "Wellness") {
   $type = "Wellness";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
 } else if($find_head->Name == "Health Specialist") {
   $type = "Health Specialist";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
 }
}

                                            // send email
$email['member'] = ucwords($user->Name);
$email['credits'] = number_format($input['amount'], 2);
$email['transaction_id'] = strtoupper(substr($clinic->Name, 0, 3)).$trans_id;
$email['trans_id'] = $transaction_id;
$email['transaction_date'] = date('d F Y, h:ia');
$email['health_provider_name'] = ucwords($clinic->Name);
$email['health_provider_address'] = $clinic->Address;
$email['health_provider_city'] = $clinic->City;
$email['health_provider_country'] = $clinic->Country;
$email['health_provider_phone'] = $clinic->Phone;
$email['service'] = ucwords($clinic_type->Name).' - '.$procedure;
$email['emailSubject'] = 'Member - Successful Transaction';
$email['emailTo'] = $email_address;
$email['emailName'] = ucwords($user->Name);
$email['url'] = 'http://staging.medicloud.sg';
$email['clinic_type_image'] = $image;
$email['transaction_type'] = 'Mednefits Credits';
$email['emailPage'] = 'email-templates.member-successful-transaction';
$email['dl_url'] = url();
$email['lite_plan_enabled'] = $clinic_type->lite_plan_enabled;
$email['lite_plan_status'] = $lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1 ? TRUE : FAlSE ;
$email['total_amount'] = number_format($total_amount, 2);
$email['consultation'] = $consultation;

try {
  EmailHelper::sendEmailWithAttachment($email);
  $api = "https://admin.medicloud.sg/send_member_transaction_email";
                                                // httpLibrary::postHttp($api, $email, array());
                                                // send to clinic
  $clinic_email = DB::table('user')->where('UserType', 3)->where('Ref_ID', $input['clinic_id'])->first();

  if($clinic_email) {
   $email['emailSubject'] = 'Health Partner - Successful Transaction By Mednefits Credits';
   $email['nric'] = $user->NRIC;
   $email['emailTo'] = $clinic_email->Email;
                                                    // $email['emailTo'] = 'allan.alzula.work@gmail.com';
   $email['emailPage'] = 'email-templates.health-partner-successful-transaction';
   $api = "https://admin.medicloud.sg/send_clinic_transaction_email";
                                                    // httpLibrary::postHttp($api, $email, array());
   EmailHelper::sendEmailClinicWithAttachment($email);
 }
 $returnObject->status = TRUE;
 $returnObject->message = 'Payment Successfull';
 $returnObject->data = $transaction_results;
} catch(Exception $e) {
  $email['end_point'] = url('v1/clinic/send_payment', $parameter = array(), $secure = null);
  $email['logs'] = 'Mobile Payment Credits Send Email Attachments - '.$e->getMessage();
  $email['emailSubject'] = 'Error log.';
  EmailHelper::sendErrorLogs($email);
  $returnObject->status = TRUE;
  $returnObject->message = 'Payment Successfull';
  $returnObject->data = $transaction_results;
}

} catch(Exception $e) {
 $email['end_point'] = url('v1/clinic/send_payment', $parameter = array(), $secure = null);
 $email['logs'] = 'Mobile Payment Credits - '.$e->getMessage();
 $email['emailSubject'] = 'Error log. - Transaction ID: '.$transaction_id.' Wallet History ID: '.$wallet_history_id;

                                            // delete transaction history log
 $transaction->deleteFailedTransactionHistory($transaction_id);
                                            // delete failed wallet history
 if($spending_type == "medical") {
  $history->deleteFailedWalletHistory($wallet_history_id);
                                                // credits back
  $wallet->addCredits($user_id, $input['amount']);
} else {
  \WellnessWalletHistory::where('wellness_wallet_history_id', $wallet_history_id)->delete();
  $wallet->addWellnessCredits($user_id, $input['amount']);
}

$returnObject->status = FALSE;
$returnObject->message = 'Payment unsuccessfull. Please try again later';

EmailHelper::sendErrorLogs($email);
}


} else {
  $returnObject->status = FALSE;
  $returnObject->message = 'Payment unsuccessfull. Please try again later';
}

} catch(Exception $e) {
 $email['end_point'] = url('v1/clinic/send_payment', $parameter = array(), $secure = null);
 $email['logs'] = 'Mobile Payment Credits - '.$e->getMessage();
 $email['emailSubject'] = 'Error log. - Transaction ID: '.$transaction_id;

                                    // delete transaction history log
 $transaction->deleteFailedTransactionHistory($transaction_id);

 $returnObject->status = FALSE;
 $returnObject->message = 'Payment unsuccessfull. Please try again later';

 EmailHelper::sendErrorLogs($email);
}
}
} catch(Exception $e) {
 $returnObject->status = FALSE;
 $returnObject->message = 'Cannot process payment credits. Please try again.';
                            // send email logs
 $email['end_point'] = url('v1/clinic/send_payment', $parameter = array(), $secure = null);
 $email['logs'] = 'Mobile Payment Credits - '.$e->getMessage();
 $email['emailSubject'] = 'Error log.';
 EmailHelper::sendErrorLogs($email);
}
                    // }



return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

    // record payment transaction
public function savePayment( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
                // return $findUserID;
   if($findUserID){

    return Response::json($returnObject);
  } else {
    $returnObject->status = FALSE;
    $returnObject->message = StringHelper::errorMessage("Token");
    return Response::json($returnObject);
  }
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function notifyClinicDirectPayment( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();

 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
   if($findUserID){

    if(!isset($input['services'])) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Please choose a service.';
     return Response::json($returnObject);
   } else if(sizeof($input['services']) == 0) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Please choose a service.';
     return Response::json($returnObject);
   }

   if(!isset($input['clinic_id'])) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Please choose a clinic.';
     return Response::json($returnObject);
   }

        				// check block access
   $block = PlanHelper::checkCompanyBlockAccess($findUserID, $input['clinic_id']);

   if($block) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Clinic not accessible to your Company. Please contact Your company for more information.';
     return Response::json($returnObject);
   }

   $returnObject->status = TRUE;
   $returnObject->message = 'Success.';
                    // check user type
   $type = StringHelper::checkUserType($findUserID);
   $lite_plan_status = false;
   $lite_plan_status = StringHelper::newLitePlanStatus($findUserID);

   $user = DB::table('user')->where('UserID', $findUserID)->first();
   if($type['user_type'] == 5 && $type['access_type'] == 0 || $type['user_type'] == "5" && $type['access_type'] == "0" || $type['user_type'] == 5 && $type['access_type'] == 1 || $type['user_type'] == "5" && $type['access_type'] == "1")
   {
     $user_id = $findUserID;
     $customer_id = $findUserID;
   } else {
                        // find owner
     $owner = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $findUserID)->first();
     $user_id = $owner->owner_id;
                        // $user_id = $findUserID;
     $customer_id = $findUserID;
   }

   $transaction = new Transaction();
   $wallet = new Wallet( );
   $clinic_data = DB::table('clinic')->where('ClinicID', $input['clinic_id'])->first();
   $clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic_data->Clinic_Type)->first();

                    // check if multiple services selected
   $multiple = false;
   if(sizeof($input['services']) == 1) {
     $services = $input['services'][0];
     $multiple_service_selection = 0;
     $multiple = false;
   } else {
     $services = 0;
     $multiple_service_selection = 1;
     $multiple = true;
   }

   $wallet_data = $wallet->getUserWallet($user_id);

   if($clinic_data->co_paid_status == 1 || $clinic_data->co_paid_status == "1") {
     $co_paid_amount = $clinic_data->gst_amount;
     $co_paid_status = $clinic_data->co_paid_status;
   } else {
     $co_paid_amount = $clinic_data->co_paid_amount;
     $co_paid_status = $clinic_data->co_paid_status;
   }

   if($lite_plan_status && $clinic_type->lite_plan_enabled == 1) {
     $lite_plan_enabled = 1;
   } else {
     $lite_plan_enabled = 0;
   }

   $data = array(
     'UserID'                => $customer_id,
     'ProcedureID'           => $services,
     'date_of_transaction'   => date('Y-m-d H:i:s'),
     'ClinicID'              => $input['clinic_id'],
     'procedure_cost'        => 0,
     'AppointmenID'          => 0,
     'revenue'               => 0,
     'debit'                 => 0,
     'medi_percent'          => $clinic_data->medicloud_transaction_fees,
     'clinic_discount'       => $clinic_data->discount,
     'wallet_use'            => 0,
     'current_wallet_amount' => $wallet_data->balance,
     'credit_cost'           => 0,
     'paid'                  => 0,
     'co_paid_status'        => $co_paid_status,
     'co_paid_amount'        => $co_paid_amount,
     'DoctorID'              => 0,
     'backdate_claim'        => 1,
     'in_network'            => 1,
     'mobile'                => 1,
     'health_provider_done'  => 1,
     'multiple_service_selection' => $multiple_service_selection,
     'spending_type'         => $clinic_type->spending_type,
     'lite_plan_enabled'     => $lite_plan_enabled
   );

   try {

     $result = $transaction->createTransaction($data);
     $transaction_id = $result->id;

     if($result) {
      // insert transation services
      $ts = new TransctionServices( );
      $save_ts = $ts->createTransctionServices($input['services'], $transaction_id);
      // send notification to browser
      Notification::sendNotification('Customer Payment - Mednefits', 'Customer '.ucwords($user->Name).' will pay directly to your clinic.', url('app/setting/claim-report', $parameter = array(), $secure = null), $input['clinic_id'], $user->Image);
      // send realtime update to claim clinic admin
      PusherHelper::sendClinicClaimNotification($transaction_id, $input['clinic_id']);

      // check if check_in_id exist
      if(!empty($input['check_in_id']) && $input['check_in_id'] != null) {
                  // check check_in_id data
        $check_in = DB::table('user_check_in_clinic')
        ->where('check_in_id', $input['check_in_id'])
        ->first();
        if($check_in) {
                  // update check in date
          DB::table('user_check_in_clinic')
          ->where('check_in_id', $input['check_in_id'])
          ->update(['check_out_time' => date('Y-m-d H:i:s'), 'id' => $transaction_id, 'status' => 1]);
          PusherHelper::sendClinicCheckInRemoveNotification($input['check_in_id'], $check_in->clinic_id);
        }
      }

      $returnObject->status = TRUE;
      $returnObject->message = 'Transaction Done.';
    }
  } catch(Exception $e) {
   $returnObject->status = FALSE;
   $returnObject->message = 'Cannot process payment direct to health provider. Please try again.';
                        // send email logs
   $email = [];
   $email['end_point'] = url('v1/clinic/payment_direct', $parameter = array(), $secure = null);
   $email['logs'] = 'Mobile Payment Direct - '.$e;
   $email['emailSubject'] = 'Error log.';
   EmailHelper::sendErrorLogs($email);
 }

 return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function saveInNetworkReceipt( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
                // return $findUserID;
   if($findUserID){

                    // check if there is a file
    if(empty($input['transaction_id']) || $input['transaction_id'] == null) {
     $returnObject->status = FALSE;
     $returnObject->message = 'E-Claim Transaction ID is required.';
     return Response::json($returnObject);
   }

                    // check if there is a file
   if(Input::hasFile('file')) {
                        // check if imag is valid
     $rules = array(
       'file' => 'required | mimes:jpeg,jpg,png,pdf,xls,xlsx,csv',
     );
     $file = $input['file'];
                        // return $file->getClientOriginalExtension();
     $validator = Validator::make(Input::all(), $rules);
     if ($validator->passes()) {

      $transaction_id = (int)preg_replace('/[^0-9]/', '', $input['transaction_id']);
      $transaction = DB::table('transaction_history')->where('transaction_id', $transaction_id)->first();

      if($transaction) {

       $file_name = time().' - '.$file->getClientOriginalName();
       if($file->getClientOriginalExtension() == "pdf") {
                                    // return "pdf";
        $receipt_file = $file_name;
        $receipt_type = "pdf";
        $file->move(public_path().'/receipts/', $file_name);
      } else if($file->getClientOriginalExtension() == "xls" || $file->getClientOriginalExtension() == "xlsx" || $file->getClientOriginalExtension() == "csv") {
        $receipt_file = $file_name;
        $receipt_type = "xls";
        $file->move(public_path().'/receipts/', $file_name);
      } else {
        $image = \Cloudinary\Uploader::upload($file->getPathName());
        $receipt_file = $image['secure_url'];
        $receipt_type = "image";
      }

      $receipt = new UserImageReceipt( );
      $data = array(
        'transaction_id'    => $transaction_id,
        'file'      => $receipt_file,
        'type'     => $receipt_type
      );

      $result = $receipt->saveReceipt($data);

      if($result) {
                                    // if(StringHelper::Deployment()==1){
        if($data['type'] != "image" || $data['type'] !== "image") {
                                            //   aws
         $s3 = AWS::get('s3');
         $s3->putObject(array(
           'Bucket'     => 'mednefits',
           'Key'        => 'receipts/'.$file_name,
           'SourceFile' => public_path().'/receipts/'.$file_name,
         ));
       }
                                    // }
       $returnObject->status = TRUE;
       $returnObject->message = 'Success.';
       $returnObject->data = $result;
     } else {
      $returnObject->status = FALSE;
      $returnObject->message = 'Failed to save receipt.';
    }
  } else {
   $returnObject->status = FALSE;
   $returnObject->message = 'E-Claim Transaction ID not found';
 }
} else {
  $returnObject->status = FALSE;
  $returnObject->message = "Invalid receipt file. File should be Image, PDF or Excel.";
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = "Invalid receipt file. File should be Image, PDF or Excel.";
}

return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function saveEclaimReceipt( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
                // return $findUserID;
   if($findUserID){

    if(empty($input['transaction_id']) || $input['transaction_id'] == null) {
     $returnObject->status = FALSE;
     $returnObject->message = 'E-Claim Transaction ID is required.';
     return Response::json($returnObject);
   }

                    // check if there is a file
   if(Input::hasFile('file')) {
                        // check if imag is valid
     $rules = array(
       'file' => 'required | mimes:jpeg,jpg,png,pdf,xls,xlsx,csv',
     );

     $validator = Validator::make(Input::all(), $rules);
     if ($validator->passes()) {

      $transaction_id = (int)preg_replace('/[^0-9]/', '', $input['transaction_id']);
      $transaction = DB::table('e_claim')->where('e_claim_id', $transaction_id)->first();

      if($transaction) {

       $file = $input['file'];
       $file_name = time().' - '.$file->getClientOriginalName();
       if($file->getClientOriginalExtension() == "pdf") {
        $receipt_file = $file_name;
        $receipt_type = "pdf";
        $file->move(public_path().'/receipts/', $file_name);
      } else if($file->getClientOriginalExtension() == "xls" || $file->getClientOriginalExtension() == "xlsx") {
        $receipt_file = $file_name;
        $receipt_type = "xls";
        $file->move(public_path().'/receipts/', $file_name);
      } else {
        $image = \Cloudinary\Uploader::upload($file->getPathName());
        $receipt_file = $image['secure_url'];
        $receipt_type = "image";
      }

      $e_claim_docs = new EclaimDocs( );
      $receipt = array(
        'e_claim_id'    => $transaction_id,
        'doc_file'      => $receipt_file,
        'file_type'     => $receipt_type
      );

      $result = $e_claim_docs->createEclaimDocs($receipt);

      if($result) {
        if($receipt['file_type'] != "image" || $receipt['file_type'] !== "image") {
                                        //   aws
         $s3 = AWS::get('s3');
         $s3->putObject(array(
           'Bucket'     => 'mednefits',
           'Key'        => 'receipts/'.$file_name,
           'SourceFile' => public_path().'/receipts/'.$file_name,
         ));
       }
       $returnObject->status = TRUE;
       $returnObject->message = 'Success.';
       $returnObject->data = $result;
     } else {
      $returnObject->status = FALSE;
      $returnObject->message = 'Failed to save receipt.';
    }
  } else {
   $returnObject->status = FALSE;
   $returnObject->message = 'E-Claim Transaction ID not found';
 }
} else {
  $returnObject->status = FALSE;
  $returnObject->message = "Invalid receipt file. File should be Image, PDF or Excel.";
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = "Invalid receipt file. File should be Image, PDF or Excel.";
}

return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function saveImageReceiptBulk( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
   $temp = [];
   if($findUserID){
    $files = Input::file('files');
    foreach ($files as $key => $file) {
     $rules = array('file' => 'required', 'required|mimes:png,gif,jpeg');
     $validator = Validator::make(array('file'=> $file), $rules);
                        // $file = $value;
                        // array_push($temp, $file->getPathName());
                        // check if there is a file
                        // if($value::hasFile('file')) {
                            // check if imag is valid
                            // $rules = array(
                            //     'file' => 'required|mimes:png,gif,jpeg,jpg|max:100000'
                            // );

                            // $validator = Validator::make(Input::all(), $rules);

                            // if ($validator->passes()) {
                                // self::loadCloudinaryConfig();

                                // $file = $file;
     $image = \Cloudinary\Uploader::upload($file->getPathName(), array("width" => 500, "height" => 500));
                                // return $image;
     $returnObject->images[] = $image['secure_url'];

     $receipt = new UserImageReceipt( );

     $receipt_data = array(
       'user_id'   => $findUserID,
       'image'     => $image['secure_url'],
       'transaction_id' => $input['transaction_id']
     );

     $result = $receipt->saveReceipt($receipt_data);

     if($result) {
      $returnObject->status = TRUE;
      $returnObject->message = 'Success.';
      $returnObject->data = $result;
    } else {
      $returnObject->status = FALSE;
      $returnObject->message = 'Failed to save receipt.';
    }
                            // } else {
                            //     $returnObject->status = FALSE;
                            //     $returnObject->message = "Invalid image format.";
                            // }
                        // } else {
                        //     $returnObject->status = FALSE;
                        //     $returnObject->message = "Please provide an receipt image.";
                        // }
  }
  $returnObject->result = $temp;
  return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function getEmployeeMembers( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
                // return $findUserID;
   if($findUserID){
    $returnObject->status = TRUE;
    $returnObject->message = 'Success.';
                    // check user type
    $result = [];

    $owner = DB::table('user')->where('UserID', $findUserID)->first();

    $temp = array(
      'user_id'   => $owner->UserID,
      'name'      => $owner->Name,
      'user_type' => 'owner'
    );

    array_push($result, $temp);

    $temp_result = DB::table('employee_family_coverage_sub_accounts')
    ->join('user', 'user.UserID', '=', 'employee_family_coverage_sub_accounts.user_id')
    ->where('employee_family_coverage_sub_accounts.owner_id', $findUserID)
    ->select('user.UserID as user_id', 'user.name', 'employee_family_coverage_sub_accounts.user_type')
    ->get();
    foreach ($temp_result as $key => $value) {
     array_push($result, $value);
   }

   $returnObject->data = $result;

   return Response::json($returnObject);
 } else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function getFamilCoverageAccounts( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
                // return $findUserID;
   if($findUserID){
    $returnObject->status = TRUE;
    $returnObject->message = 'Success.';
                    // check user type


    $user = DB::table('user')->where('UserID', $findUserID)->first();
    $count = 1;
    if($user->UserType == 5 && $user->access_type == 0) {
                        // get spouse account
     $users = DB::table('employee_family_coverage_sub_accounts')->where('owner_id', $findUserID)->where('deleted', 0)->get();
                        // return $returnObject->result = $users;
     $profile = array(
       'user_id'   => $user->UserID,
       'name'      => ucwords($user->Name),
       'nric'      => $user->NRIC,
       'dob'       => date('d/m/Y', strtotime($user->DOB)),
       'type'      => 'Owner'
     );
   } else if($user->UserType == 5 && $user->access_type == 2 || $user->UserType == 5 && $user->access_type == 3) {
                        // get owner details
     $owner = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $findUserID)->first();
     $users = DB::table('employee_family_coverage_sub_accounts')->where('owner_id', $owner->owner_id)->where('deleted', 0)->get();
     $owner_profile = DB::table('user')->where('UserID', $owner->owner_id)->first();
     $profile = array(
       'user_id'   => $owner_profile->UserID,
       'name'      => ucwords($owner_profile->Name),
       'dob'       => date('d/m/Y', strtotime($owner_profile->DOB)),
       'nric'      => $owner_profile->NRIC,
       'type'      => 'Owner'
     );
   }

   $returnObject->data['users'][] = $profile;
   if(sizeof($users) > 0) {
     $returnObject->status = TRUE;
     foreach ($users as $key => $value) {
      $temp_profile = DB::table('user')->where('UserID', $value->user_id)->first();
                            // if($value->relationship == 'spouse') {
      $profile = array(
        'user_id'   => $temp_profile->UserID,
        'name'      => ucwords($temp_profile->Name),
        'dob'       => date('d/m/Y', strtotime($temp_profile->DOB)),
        'nric'      => $temp_profile->NRIC,
        'type'      => ucwords($value->relationship)
      );
      $returnObject->data['users'][] = $profile;
                            // } else if($value->relationship == "dependent") {
                            //     $profile = array(
                            //         'user_id'   => $temp_profile->UserID,
                            //         'name'      => ucwords($temp_profile->Name),
                            //         'nric'      => $temp_profile->NRIC,
                            //         'type'      => $value->relationship
                            //     );
                            //     $returnObject->data['users']['all'][] = $profile;
                            //     $returnObject->data['users']['dependents'][] = $profile;
                            // }
    }
  }
                    // else {
                    //     $returnObject->status = TRUE;
                    //     $returnObject->data['users'] = [];
                    // }

  return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function setWalletSettings( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
                // return $findUserID;
   if($findUserID){
    $pin = $input['pin'];

                    // check user type
    $type = StringHelper::checkUserType($findUserID);
                    // $returnObject->result = $type;
    if($type['user_type'] == 5 && $type['access_type'] == 2 || $type['user_type'] == 5 && $type['access_type'] == 3) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Spouse and Dependents are not eligible to update the wallet settings.';
     return Response::json($returnObject);
   }

                    // check pin size
   if(ceil(log10($pin)) == 6) {
     $user = new User();
     $result = $user->pin($findUserID, $pin);
     if($result) {
      if(empty($input['bank_name']) || !isset($input['bank_name'])) {
       $returnObject->status = FALSE;
       $returnObject->message = 'Please fill in Bank Name';
       return Response::json($returnObject);
     } else if(empty($input['account_name']) || !isset($input['account_name'])) {
       $returnObject->status = FALSE;
       $returnObject->message = 'Please fill in Account Name';
       return Response::json($returnObject);
     } else if(empty($input['account_number']) || !isset($input['account_number'])) {
       $returnObject->status = FALSE;
       $returnObject->message = 'Please fill in Account Number';
       return Response::json($returnObject);
     }
                            // update wallet
     $wallet = new WalletSettings();
     $settings = $wallet->walletSetting($input, $findUserID);
     if($settings) {
       $returnObject->status = TRUE;
       $returnObject->message = 'Wallet and Pin Setting successfully updated.';
     } else {
       $returnObject->status = FALSE;
       $returnObject->message = 'Error in updating Wallet Settings.';
     }
     return Response::json($returnObject);
   } else {
    return Response::json($result);
  }
} else {
 $returnObject->status = FALSE;
 $returnObject->message = 'Pin must be 6 digits';
}
return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function getWalletSettings( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
                // return $findUserID;
   if($findUserID){
    $returnObject->status = TRUE;
    $returnObject->message = 'Success.';

                    // check type
    $user_id = StringHelper::getUserId($findUserID);

    $pin = DB::table('user')->where('UserID', $user_id)->first();
                    // $returnObject->data['bank_lists'] = StringHelper::BankList();
                    // $bank_delails = DB::table('wallet_settings')->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
                    // $returnObject->data['wallet_details'] = ;
    $returnObject->data['pin_status'] = $pin->pin_setup == 1 || $pin->pin_setup == "1" ? TRUE : FALSE;
    $returnObject->data['pin_code'] = $pin->user_pin != "0" ? $pin->user_pin : "";
    return Response::json($returnObject);
  } else {
    $returnObject->status = FALSE;
    $returnObject->message = StringHelper::errorMessage("Token");
    return Response::json($returnObject);
  }
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function getNetworkTransactions( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);

   if($findUserID){
    $returnObject->status = TRUE;
    $returnObject->message = 'Success.';
    $user = DB::table('user')->where('UserID', $findUserID)->first();
    $lite_plan_status = false;
            // $lite_plan_status = StringHelper::litePlanStatus($findUserID);

                    // $type = StringHelper::checkUserType($findUserID);
    $transaction_details = [];
    $ids = StringHelper::getSubAccountsID($findUserID);
    $transactions = DB::table('transaction_history')
    ->whereIn('UserID', $ids)
    ->orderBy('created_at', 'desc')
    ->where('paid', 1)
    ->get();
    foreach ($transactions as $key => $trans) {
     if($trans) {
      $receipt_images = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->get();
      $clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
      $clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
      $customer = DB::table('user')->where('UserID', $trans->UserID)->first();
      $procedure_temp = "";
                            // get services
      if($trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1")
      {
                                // get multiple service
       $service_lists = DB::table('transaction_services')
       ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
       ->where('transaction_services.transaction_id', $trans->transaction_id)
       ->get();

       foreach ($service_lists as $key => $service) {
        $procedure_temp .= ucwords($service->Name).',';
        $procedure = rtrim($procedure_temp, ',');
      }
      $clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
    } else {
     $service_lists = DB::table('clinic_procedure')
     ->where('ProcedureID', $trans->ProcedureID)
     ->first();
     if($service_lists) {
      $procedure = ucwords($service_lists->Name);
      $clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
    } else {
      $clinic_name = ucwords($clinic_type->Name);
    }
  }

                            // check if there is a receipt image
  $receipt = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->count();

  if($receipt > 0) {
   $receipt_status = TRUE;
 } else {
   $receipt_status = FALSE;
 }


 $clinic_sub_name = strtoupper(substr($clinic->Name, 0, 3));
 $transaction_id = $clinic_sub_name.str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

 $total_amount = 0;

 if(strripos($trans->procedure_cost, '$') !== false) {
   $temp_cost = explode('$', $trans->procedure_cost);
                                // $cost = number_format($temp_cost[1]);
   $cost = $temp_cost[1];
 } else {
                                // $cost = number_format($trans->procedure_cost, 2);
   $cost = floatval($trans->procedure_cost);
 }

 $total_amount = $cost;

 if((int)$trans->health_provider_done == 1) {
   $receipt_status = TRUE;
   $health_provider_status = TRUE;
                 // $receipt_status = TRUE;
   if((int)$trans->lite_plan_enabled == 1) {
    $total_amount = $cost + $trans->consultation_fees;
  } else {
    $total_amount = $cost;
  }
  $type = "cash";
} else {
 $health_provider_status = FALSE;
 if((int)$trans->lite_plan_enabled == 1) {
  $total_amount = $cost + $trans->consultation_fees;
} else {
  $total_amount = $cost;
}
$type = "credits";
}

$currency_symbol = null;
$converted_amount = null;

if($trans->currency_type == "sgd") {
  $currency_symbol = "S$";
  $converted_amount = $total_amount;
} else if($trans->currency_type == "myr") {
  $currency_symbol = "RM";
  $converted_amount = $total_amount * $trans->currency_amount;
}

$format = array(
  'clinic_name'       => $clinic->Name,
  'amount'            => number_format($total_amount, 2),
  'converted_amount'  => number_format($converted_amount, 2),
  'currency_symbol'   => $currency_symbol,
  'clinic_type_and_service' => $clinic_name,
  'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->created_at)),
  'customer'          => ucwords($customer->Name),
  'transaction_id'    => (string)$transaction_id,
  'receipt_status'    => $receipt_status,
  'health_provider_status' => $health_provider_status,
  'user_id'           => (string)$trans->UserID,
  'type'              => $type,
  'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE
);

array_push($transaction_details, $format);
}
}
$returnObject->data = $transaction_details;
return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function getInNetworkDetails($id)
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
   $transaction_id = (int)preg_replace('/[^0-9]/', '', $id);

   if($findUserID){
    $returnObject->status = TRUE;
    $returnObject->message = 'Success.';
            // $user = DB::table('user')->where('UserID', $findUserID)->first();
    $user_id = StringHelper::getUserId($findUserID);
    $lite_plan_status = false;
                    // $lite_plan_status = StringHelper::litePlanStatus($findUserID);
    $total_amount = 0;
    $service_credits = false;
    $consultation_credits = false;
    $consultation = 0;
    $wallet_status = false;

    $transaction_details = [];
    $transaction = DB::table('transaction_history')->where('transaction_id', $transaction_id)->first();
    $company_wallet_status = PlanHelper::getCompanyAccountType($user_id);

    if($company_wallet_status) {
     if($company_wallet_status == "Health Wallet") {
      $wallet_status = true;
    }
  }

  if($transaction) {
   $receipt_images = DB::table('user_image_receipt')->where('transaction_id', $transaction->transaction_id)->get();
   $clinic = DB::table('clinic')->where('ClinicID', $transaction->ClinicID)->first();
   $clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
   $customer = DB::table('user')->where('UserID', $transaction->UserID)->first();
   $procedure_temp = "";

   if((int)$transaction->lite_plan_enabled == 1) {
    if($transaction->spending_type == 'medical') {
     $table_wallet_history = 'wallet_history';
   } else {
     $table_wallet_history = 'wellness_wallet_history';
   }

   $logs_lite_plan = DB::table($table_wallet_history)
   ->where('logs', 'deducted_from_mobile_payment')
   ->where('lite_plan_enabled', 1)
   ->where('id', $transaction->transaction_id)
   ->first();

   if($logs_lite_plan && $transaction->credit_cost > 0 && (int)$transaction->lite_plan_use_credits == 0) {
     $consultation_credits = true;
                  // $service_credits = true;
     $consultation = $logs_lite_plan->credit;
   } else if($logs_lite_plan && $transaction->procedure_cost >= 0 && (int)$transaction->lite_plan_use_credits == 1) {
     $consultation_credits = true;
                  // $service_credits = true;
     $consultation = $logs_lite_plan->credit;
   } else if($transaction->procedure_cost >= 0 && (int)$transaction->lite_plan_use_credits == 0) {
                // $total_consultation += floatval($trans->co_paid_amount);
    $consultation = floatval($transaction->consultation_fees);
  } else {
    $consultation = floatval($transaction->consultation_fees);
  }
}

$doc_files = [];
foreach ($receipt_images as $key => $doc) {
 if($doc->type == "pdf" || $doc->type == "xls") {
  if(StringHelper::Deployment()==1){
   $fil = 'https://s3-ap-southeast-1.amazonaws.com/mednefits/receipts/'.$doc->file;
 } else {
   $fil = url('').'/receipts/'.$doc->file;
 }
} else if($doc->type == "image") {
  $fil = FileHelper::formatImageAutoQuality($doc->file);
}

$temp_doc = array(
  'transaction_doc_id'    => $doc->image_receipt_id,
  'transaction_id'            => $doc->transaction_id,
  'file'                      => $fil,
  'file_type'             => $doc->type
);

array_push($doc_files, $temp_doc);
}

                        // get services
if($transaction->multiple_service_selection == 1 || $transaction->multiple_service_selection == "1")
{
                            // get multiple service
  $service_lists = DB::table('transaction_services')
  ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
  ->where('transaction_services.transaction_id', $transaction->transaction_id)
  ->get();

  foreach ($service_lists as $key => $service) {
   $procedure_temp .= ucwords($service->Name).',';
   $procedure = rtrim($procedure_temp, ',');
 }
 $service = $procedure;
} else {
  $service_lists = DB::table('clinic_procedure')
  ->where('ProcedureID', $transaction->ProcedureID)
  ->first();
  if($service_lists) {
   $procedure = ucwords($service_lists->Name);
   $service = $procedure;
 } else {
                                    // $procedure = "";
   $service = ucwords($clinic_type->Name);
 }
}

$type = "";
$image = "";
if($clinic_type->head == 1 || $clinic_type->head == "1") {
  if($clinic_type->Name == "General Practitioner") {
   $type = "General Practitioner";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
 } else if($clinic_type->Name == "Dental Care") {
   $type = "Dental Care";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
 } else if($clinic_type->Name == "Traditional Chinese Medicine") {
   $type = "Traditional Chinese Medicine";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
 } else if($clinic_type->Name == "Health Screening") {
   $type = "Health Screening";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
 } else if($clinic_type->Name == "Wellness") {
   $type = "Wellness";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
 } else if($clinic_type->Name == "Health Specialist") {
   $type = "Health Specialist";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
 }
} else {
  $find_head = DB::table('clinic_types')
  ->where('ClinicTypeID', $clinic_type->sub_id)
  ->first();
  if($find_head->Name == "General Practitioner") {
   $type = "General Practitioner";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
 } else if($find_head->Name == "Dental Care") {
   $type = "Dental Care";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
 } else if($find_head->Name == "Traditional Chinese Medicine") {
   $type = "Traditional Chinese Medicine";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
 } else if($find_head->Name == "Health Screening") {
   $type = "Health Screening";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
 } else if($find_head->Name == "Wellness") {
   $type = "Wellness";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
 } else if($find_head->Name == "Health Specialist") {
   $type = "Health Specialist";
   $image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
 }
}

$half_credits = false;
$total_amount = $transaction->procedure_cost;

                          // check if there is a receipt image
                          // $receipt = DB::table('user_image_receipt')->where('transaction_id', $transaction->transaction_id)->count();

                          // if($receipt > 0) {
                          //     $receipt_status = TRUE;
                          // } else {
                          //     $receipt_status = FALSE;
                          // }

                          // if($transaction->health_provider_done == 1 || $transaction->health_provider_done == "1") {
                          //     $receipt_status = TRUE;
                          //     $health_provider_status = TRUE;
                          //     // $receipt_status = TRUE;
                          // } else {
                          //     $health_provider_status = FALSE;
                          // }
$procedure_cost = number_format($transaction->procedure_cost, 2);
if($transaction->health_provider_done == 1 || $transaction->health_provider_done == "1") {
  $payment_type = 'Cash';
  if((int)$transaction->lite_plan_enabled == 1 && $wallet_status == true) {
   $total_amount = $transaction->procedure_cost + $transaction->consultation_fees;
 }
} else {
  if($transaction->credit_cost > 0 && $transaction->cash_cost > 0) {
    $payment_type = 'Mednefits Credits + Cash';
    $half_credits = true;
  } else {
    $payment_type = 'Mednefits Credits';
  }
  $service_credits = true;
  if((int)$transaction->lite_plan_enabled == 1 && $wallet_status == true) {
   $total_amount = $transaction->procedure_cost + $transaction->consultation_fees;
 } else {
   $total_amount = $transaction->procedure_cost;
 }
}
$lite_plan_status = (int)$transaction->lite_plan_enabled == 1 ? TRUE : FALSE;

if((int)$transaction->lite_plan_enabled == 1 && $wallet_status == false) {
  $service_credits = false;
  $consultation_credits = false;
  $lite_plan_status = false;
}

$currency_symbol = null;
$converted_amount = null;
  // $consultation = null;
$converted_consultation = null;
$converted_procedure_cost = null;

if($transaction->currency_type == "sgd") {
  $currency_symbol = "S$";
  $converted_amount = $total_amount;
  $converted_procedure_cost = $procedure_cost;
  $converted_consultation = (int)$transaction->lite_plan_enabled == 1 ? number_format($consultation, 2) : "0.00";
} else if($transaction->currency_type == "myr") {
  $currency_symbol = "RM";
  $converted_amount = $total_amount * $transaction->currency_amount;
  $converted_procedure_cost = $procedure_cost * $transaction->currency_amount;
  $converted_consultation = (int)$transaction->lite_plan_enabled == 1 ? number_format($consultation * $transaction->currency_amount, 2) : "0.00";
}

$transaction_details = array(
  'clinic_name'       => $clinic->Name,
  'clinic_image'      => $clinic->image ? $clinic->image : 'https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514443281/rjfremupirvnuvynz4bv.jpg',
  'clinic_type'       => $type,
  'clinic_type_image' => $image,
  'amount'      => number_format($total_amount, 2),
  'converted_amount' => number_format($converted_amount, 2),
  'procedure_cost'    => $procedure_cost,
  'converted_procedure_cost' => number_format($converted_procedure_cost, 2),
  'services' => $service,
  'date_of_transaction' => date('d F Y, h:ia', strtotime($transaction->created_at)),
  'customer'            => ucwords($customer->Name),
  'transaction_id'    => (string)$id,
  'user_id'           => (string)$transaction->UserID,
  'payment_type'      => $payment_type,
  'service_credits'   => $service_credits,
  'consultation_credits' => $consultation_credits,
  'consultation'      => (int)$transaction->lite_plan_enabled == 1 ? number_format($consultation, 2) : "0.00",
  'converted_consultation'  => $converted_consultation,
  'lite_plan'         => $lite_plan_status,
  'wallet_status'     => $wallet_status,
  'lite_plan_enabled' => $transaction->lite_plan_enabled,
  'cap_transaction'   => $half_credits,
  'cap_per_visit'     => $transaction->currency_type == "myr" ? number_format($transaction->cap_per_visit * $transaction->currency_amount, 2) : number_format($transaction->cap_per_visit, 2),
  'paid_by_cash'      => $transaction->currency_type == "myr" ? number_format($transaction->cash_cost * $transaction->currency_amount, 2) : number_format($transaction->cash_cost, 2),
  'paid_by_credits'      => $transaction->currency_type == "myr" ? number_format($transaction->credit_cost * $transaction->currency_amount, 2) : number_format($transaction->credit_cost, 2),
  "currency_symbol" => $transaction->currency_type == "myr" ? "RM" : "S$",
  'files'             => $doc_files
);
}

$returnObject->data = $transaction_details;
return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function uploadOutOfNetworkReceipt( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
                // return $findUserID;
   if($findUserID){
    $returnObject->status = TRUE;
    $returnObject->message = 'Success.';

    if(Input::file('file')) {
     $file_types = ["jpeg","jpg","png","pdf","xls","xlsx","PNG"];

     $file = $input['file'];

     $result_type = in_array($file->getClientOriginalExtension(), $file_types);

     if(!$result_type) {
      $returnObject->status = FALSE;
      $returnObject->message = 'Invalid file. Only accepts Image, PDF and Excel.';
      return Response::json($returnObject);
    }

    $file_folder = 'receipts';
    $file_name = time().' - '.str_random(30).'.'.$file->getClientOriginalExtension();
    if($file->getClientOriginalExtension() == "pdf") {
      $receipt = array(
        'receipt_file'  => $file_name,
        'receipt_type'  => "pdf"
      );
                            // upload to folder
      $file->move(public_path().'/receipts/', $file_name);
    } else if($file->getClientOriginalExtension() == "xls" || $file->getClientOriginalExtension() == "xlsx") {
      $receipt = array(
        'receipt_file'  => $file_name,
        'receipt_type'  => "xls"
      );
                            // upload to folder
      $file->move(public_path().'/receipts/', $file_name);
    } else {
      $image = \Cloudinary\Uploader::upload($file->getPathName());
      $receipt = array(
        'receipt_file'  => $image['secure_url'],
        'receipt_type'  => "image"
      );
    }
    $returnObject->status = TRUE;
    $returnObject->message = 'Success';
    $returnObject->receipt = $receipt;
  } else {
   $returnObject->status = FALSE;
   $returnObject->message = 'Please input a file.';
 }
 return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function uploadInNetworkReceipt( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
                // return $findUserID;
   if($findUserID){
    $returnObject->status = TRUE;
    $returnObject->message = 'Success.';

    $transaction_id = (int)preg_replace('/[^0-9]/', '', $input['transaction_id']);

    $check = DB::table('transaction_history')->where('transaction_id', $transaction_id)->count();

    if($check == 0) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Transaction data does not exist.';
     return Response::json($returnObject);
   }

   if(Input::file('file')) {
     $rules = array(
       'file' => 'required | mimes:jpeg,jpg,png,pdf,xls,xlsx',
     );

     $validator = Validator::make(Input::all(), $rules);

     if($validator->fails()) {
      $returnObject->status = FALSE;
      $returnObject->message = 'Invalid file. Only accepts Image, PDF and Excel.';
      return Response::json($returnObject);
    }
    $file = $input['file'];
                        // $file_types = ["jpeg","jpg","png","pdf","xls","xlsx","PNG"];

                        // $result_type = in_array($file->getClientOriginalExtension(), $file_types);

                        // if(!$result_type) {
                        //     $returnObject->status = FALSE;
                        //     $returnObject->message = 'Invalid file. Only accepts Image, PDF and Excel.';
                        //     return Response::json($returnObject);
                        // }


    $file_name = time().' - '.$file->getClientOriginalName();
    $aws_upload = false;
    if($file->getClientOriginalExtension() == "pdf") {
      $receipt = array(
        'user_id'           => $findUserID,
        'transaction_id'    => $transaction_id,
        'file'  => $file_name,
        'type'  => "pdf"
      );
      $file->move(public_path().'/receipts/', $file_name);
      $aws_upload = true;
    } else if($file->getClientOriginalExtension() == "xls" || $file->getClientOriginalExtension() == "xlsx") {
      $receipt = array(
        'user_id'           => $findUserID,
        'transaction_id'    => $transaction_id,
        'file'  => $file_name,
        'type'  => "excel"
      );
      $file->move(public_path().'/receipts/', $file_name);
      $aws_upload = true;
    } else {
      $image = \Cloudinary\Uploader::upload($file->getPathName());
      $receipt = array(
        'user_id'           => $findUserID,
        'file'      => $image['secure_url'],
        'type'      => "image",
        'transaction_id'    => $transaction_id,
      );
    }


    $trans_docs = new UserImageReceipt( );

    $result = $trans_docs->saveReceipt($receipt);

    if($result) {
      if(StringHelper::Deployment()==1){
       if($aws_upload == true) {
                                    //   aws
        $s3 = AWS::get('s3');
        $s3->putObject(array(
          'Bucket'     => 'mednefits',
          'Key'        => 'receipts/'.$file_name,
          'SourceFile' => public_path().'/receipts/'.$file_name,
        ));
      }
    }
    $returnObject->status = TRUE;
    $returnObject->message = 'Receipt save.';
    $returnObject->data = $result;
  } else {
    return array('status' => FALSE, 'message' => 'Failed to save transaction receipt.');
  }
} else {
 $returnObject->status = FALSE;
 $returnObject->message = 'Please select a file.';
}
return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function getEclaimTransactions( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
   if($findUserID){
    $returnObject->status = TRUE;
    $returnObject->message = 'Success.';

    $user_id = StringHelper::getUserId($findUserID);
    $spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';
    $filter = isset($input['filter']) ? $input['filter'] : 'current_term';
    $dates = MemberHelper::getMemberDateTerms($user_id, $filter);
    $ids = StringHelper::getSubAccountsID($findUserID);
    $user_type = PlanHelper::getUserAccountType($findUserID);
    $e_claim = [];
    $paginate = [];

    if($dates) {
      if(isset($input['paginate']) && !empty($input['paginate']) && $input['paginate'] == true) {
        $per_page = !empty($input['per_page']) ? $input['per_page'] : 5;
        if($user_type == "employee") {
          $e_claims = DB::table('e_claim')
                      ->whereIn('user_id', $ids)
                      ->where('date', '>=', $dates['start'])
                      ->where('date', '<=', $dates['end'])
                      ->orderBy('date', 'desc')
                      ->paginate($per_page);
        } else {
          $e_claims = DB::table('e_claim')
                      ->where('user_id', $findUserID)
                      ->where('date', '>=', $dates['start'])
                      ->where('date', '<=', $dates['end'])
                      ->orderBy('date', 'desc')
                      ->paginate($per_page);
        }

      } else {
        if($user_type == "employee") {
          $e_claims = DB::table('e_claim')
                      ->whereIn('user_id', $ids)
                      ->where('date', '>=', $dates['start'])
                      ->where('date', '<=', $dates['end'])
                      ->orderBy('date', 'desc')
                      ->get();
        } else {
          $e_claims = DB::table('e_claim')
                      ->where('user_id', $findUserID)
                      ->where('date', '>=', $dates['start'])
                      ->where('date', '<=', $dates['end'])
                      ->orderBy('date', 'desc')
                      ->get();
        }

      }

      foreach ($e_claims as $key => $res) {
        $member = DB::table('user')->where('UserID', $res->user_id)->first();

        // check user if it is spouse or dependent
       if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
          $temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
          $temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
          $sub_account = ucwords($temp_account->Name);
          $sub_account_type = $temp_sub->user_type;
          $owner_id = $temp_sub->owner_id;
        } else {
          $sub_account = FALSE;
          $sub_account_type = FALSE;
          $owner_id = $member->UserID;
        }

        $id = str_pad($res->e_claim_id, 6, "0", STR_PAD_LEFT);

        $currency_symbol = "SGD";
        if($res->default_currency == "myr" && $res->currency_type == "sgd") {
          $currency_symbol = "MYR";
          if($res->status == 1) {
            $res->amount = $res->claim_amount;
          } else {
            $res->amount = $res->amount;
          }
        } else if($res->default_currency == "myr" && $res->currency_type == "myr") {
          $currency_symbol = "MYR";
          if($res->status == 1) {
            $res->amount = $res->claim_amount;
          } else {
            $res->amount = $res->amount;
          }
        } else if($res->default_currency == "sgd" && $res->currency_type == "myr") {
          if($res->status == 1) {
            $res->amount = $res->claim_amount;
          } else {
            $res->amount = $res->amount;
          }
        } else {
          $currency_symbol = "SGD";
          if($res->status == 1) {
            $res->amount = $res->claim_amount;
          } else {
            $res->amount = $res->amount;
          }
        }

        if((int)$res->status == 1) {
          $res->amount = $res->claim_amount > 0 ? $res->claim_amount : $res->amount;
        }

        $temp = array(
          'status'            => $res->status,
          'claim_date'        => date('d F Y', strtotime($res->created_at)),
          'time'              => $res->time,
          'service'           => ucwords($res->service),
          'merchant'          => ucwords($res->merchant),
          'amount'            => number_format($res->amount, 2),
          'member'            => ucwords($member->Name),
          'type'              => 'E-Claim',
          'transaction_id'    => 'MNF'.$id,
          'visit_date'        => date('d F Y', strtotime($res->date)).', '.$res->time,
          'owner_id'          => $owner_id,
          'sub_account_type'  => $sub_account_type,
          'sub_account'       => $sub_account,
          'month'             => date('M', strtotime($res->approved_date)),
          'day'               => date('d', strtotime($res->approved_date)),
          'time'              => date('h:ia', strtotime($res->approved_date)),
          'spending_type'     => $res->spending_type,
          'currency_symbol'   => $currency_symbol
        );

        array_push($e_claim, $temp);
      }
    }

    if(isset($input['paginate']) && !empty($input['paginate']) && $input['paginate'] == true) {
      $paginate['total'] = $e_claims->getTotal();
      $paginate['per_page'] = $e_claims->getPerPage();
      $paginate['current_page'] = $e_claims->getCurrentPage();
      $paginate['last_page'] = $e_claims->getLastPage();
      $paginate['from'] = $e_claims->getFrom();
      $paginate['to'] = $e_claims->getTo();
      $paginate['data'] = $e_claim;
      $returnObject->data = $paginate;
    } else {
      $returnObject->data = $e_claim;
    }

  return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function getEclaimDetails($id)
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
   $transaction_id = (int)preg_replace('/[^0-9]/', '', $id);

   if($findUserID){
    $returnObject->status = TRUE;
    $returnObject->message = 'Success.';
    $user = DB::table('user')->where('UserID', $findUserID)->first();
    $rejected_status = false;
    $transaction = DB::table('e_claim')->where('e_claim_id', $transaction_id)->first();

    if($transaction) {
     if($transaction->status == 0) {
      $status_text = 'Pending';
    } else if($transaction->status == 1) {
      $status_text = 'Approved';
      // $transaction->amount = $transaction->claim_amount > 0 ? $transaction->claim_amount : $transaction->amount;
    } else if($transaction->status == 2) {
      $status_text = 'Rejected';
      $rejected_status = true;
    } else {
      $status_text = 'Pending';
    }

    if($transaction->currency_type == "myr" && $transaction->default_currency == "myr") {
      $currency_symbol = "MYR";
    } else if($transaction->currency_type == "sgd" && $transaction->default_currency == "myr"){
      $currency_symbol = "MYR";
      $transaction->amount = $transaction->amount;
      $transaction->claim_amount = $transaction->claim_amount;
    } else if($transaction->currency_type == "myr" && $transaction->default_currency == "sgd"){
      $currency_symbol = "SGD";
      $transaction->amount = $transaction->amount;
      $transaction->claim_amount = $transaction->claim_amount;
    } else {
      $currency_symbol = "SGD";
    }

    // get docs
    $docs = DB::table('e_claim_docs')->where('e_claim_id', $transaction->e_claim_id)->get();

    if(sizeof($docs) > 0) {
      $e_claim_receipt_status = TRUE;
      $doc_files = [];
      foreach ($docs as $key => $doc) {
       if($doc->file_type == "pdf" || $doc->file_type == "xls") {
        if(StringHelper::Deployment()==1){
         $fil = 'https://s3-ap-southeast-1.amazonaws.com/mednefits/receipts/'.$doc->doc_file;
       } else {
         $fil = url('').'/receipts/'.$doc->doc_file;
       }
     } else if($doc->file_type == "image") {
      $fil = FileHelper::formatImageAutoQuality($doc->doc_file);
    }

    $temp_doc = array(
      'e_claim_doc_id'    => $doc->e_claim_doc_id,
      'e_claim_id'            => $doc->e_claim_id,
      'file'                      => $fil,
      'file_type'             => $doc->file_type
    );

    array_push($doc_files, $temp_doc);
  }
} else {
  $e_claim_receipt_status = FALSE;
  $doc_files = FALSE;
}

$member = DB::table('user')->where('UserID', $transaction->user_id)->first();

if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
  $temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
  $temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
  $sub_account = ucwords($temp_account->Name);
  $sub_account_type = $temp_sub->user_type;
  $owner_id = $temp_sub->owner_id;
} else {
  $sub_account = FALSE;
  $sub_account_type = FALSE;
  $owner_id = $member->UserID;
}

$date = null;
$claim_amount = null;
if($transaction->status == 1) {
  $date = date('d F Y', strtotime($transaction->approved_date));
  $claim_amount = $transaction->claim_amount;
} else {
  $date = date('d F Y', strtotime($transaction->date)).', '.$transaction->time;
}

$id = str_pad($transaction->e_claim_id, 6, "0", STR_PAD_LEFT);
$temp = array(
  'status_text'       => $status_text,
  'claim_date'        => date('d F Y', strtotime($transaction->created_at)),
  'date'              => $date,
  'time'              => $transaction->time,
  'service'           => $transaction->service,
  'merchant'          => $transaction->merchant,
  'amount'            => number_format($transaction->amount, 2),
  'claim_amount'      => number_format($claim_amount, 2),
  'member'            => ucwords($member->Name),
  'type'              => 'E-Claim',
  'transaction_id'    => 'MNF'.$id,
  'files'             => $doc_files,
  'visit_date'        => date('d F Y', strtotime($transaction->date)).', '.$transaction->time,
  'receipt_status'    => $e_claim_receipt_status,
  'owner_id'          => $owner_id,
  'owner_account'       => $sub_account,
  'sub_account_type'  => $sub_account_type,
  'rejected_status'   => $rejected_status,
  'rejected_message'   => $transaction->rejected_reason,
  'spending_type'     => ucwords($transaction->spending_type),
  'currency_symbol'   => $currency_symbol,
  'status'            => $transaction->status
);
$returnObject->status = TRUE;
$returnObject->data = $temp;
} else {
 $returnObject->status = FALSE;
 $returnObject->message = 'E-Claim transaction not found.';
}
return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function getHealthLists( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
   if($findUserID){
    $returnObject->status = TRUE;
    $returnObject->message = 'Success.';

   $user_id = StringHelper::getUserId($findUserID);
   $customer_id = PlanHelper::getCustomerId($user_id);
    if($customer_id) {
      // check if user is an enterprise plan
        $user_plan_history = DB::table('user_plan_history')->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
        $customer_active_plan = DB::table('customer_active_plan')
                                ->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
                                ->first();

        if($customer_active_plan->account_type == "enterprise_plan")  {
          if($input['spending_type'] == "medical") {
            $spending_types = DB::table('health_types')->where('account_type', $customer_active_plan->account_type)->where('active', 1)->get();
            foreach($spending_types as $key => $spending) {
              if($spending->cap_amount_enterprise > 0)  {
                $spending->cap_amount = (float)$spending->cap_amount_enterprise;
              }
            }
          } else {
            $spending_types = DB::table('health_types')->where('type', $input['spending_type'])->where('active', 1)->get();
          }
        } else {
          if(empty($input['spending_type']) || $input['spending_type'] == null) {
            $returnObject->status = FALSE;
            $returnObject->message = 'Spending Type is required. Please choose either medical or wellness type';
            return Response::json($returnObject);
          }
          // get claim type service cap
          $get_company_e_claim_services = DB::table('company_e_claim_service_types')
          ->where('customer_id', $customer_id)
          ->where('type', $input['spending_type'])
          ->where('active', 1)
          ->get();
          if(sizeof($get_company_e_claim_services) > 0) {
            $spending_types = $get_company_e_claim_services;
          } else {
            $spending_types = DB::table('health_types')->where('type', $input['spending_type'])->where('active', 1)->get();
          }
        }
    } else {
      $spending_types = DB::table('health_types')->where('type', $input['spending_type'])->where('active', 1)->get();
    }

  $returnObject->data = $spending_types;
  return Response::json($returnObject);
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
} else {
 $returnObject->status = FALSE;
 $returnObject->message = StringHelper::errorMessage("Token");
 return Response::json($returnObject);
}
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}

public function checkPendingEclaims($user_ids, $spending_type)
{
 $amount = DB::table('e_claim')
 ->whereIn('user_id', $user_ids)
 ->where('status', 0)
 ->where('spending_type', $spending_type)
 ->sum('amount');

 return $amount;
}

public function createEclaim( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
 $input = Input::all();
 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
   if($findUserID){

    if(sizeof(Input::file('files')) == 0) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Please input a file.';
     return Response::json($returnObject);
   }

   $file_types = ["jpeg","jpg","png","pdf","xls","xlsx","PNG", "JPG", "JPEG"];

   if(empty($input['amount']) || $input['amount'] == null) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Please indicate the amount.';
     return Response::json($returnObject);
   }

   if(empty($input['claim_amount']) || $input['claim_amount'] == null) {
    $returnObject->status = FALSE;
    $returnObject->message = 'Please indicate the claim amount.';
    return Response::json($returnObject);
  }

   if(empty($input['merchant']) || $input['merchant'] == null) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Please indicate the Provider.';
     return Response::json($returnObject);
   }

   if(empty($input['service']) || $input['service'] == null) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Please choose a claim type.';
     return Response::json($returnObject);
   }

   if(empty($input['spending_type']) || $input['spending_type'] == null) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Please choose a spending wallet.';
     return Response::json($returnObject);
   }

   if(empty($input['date']) || $input['date'] == null) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Date of Visit is required.';
     return Response::json($returnObject);
   }

   if(empty($input['time']) || $input['time'] == null) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Time of Visit is required.';
     return Response::json($returnObject);
   }

   if(empty($input['spending_type']) || $input['spending_type'] == null) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Spending Account is required (Medical or Wellness)';
     return Response::json($returnObject);
   }

             // validate wellness
   $spending = ["medical", "wellness"];

   if(!in_array($input['spending_type'], $spending)) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Spending Account should be medical or wellness only.';
     return Response::json($returnObject);
   }

   $validate_date = SpendingInvoiceLibrary::validateStartDate($input['date']);

   if(!$validate_date) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Date of Visit must be a date.';
     return Response::json($returnObject);
   }

   $validate_time = SpendingInvoiceLibrary::validateStartDate($input['time']);

   if(!$validate_time) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Time of Visit must be a time (00:00 AM/PM).';
     return Response::json($returnObject);
   }
             // $rules = array('file' => 'mimes:jpeg,png,gif,bmp,pdf,doc,docx');
   $rules = array('file' => 'image|max:20000000');
                      // loop through the files ang validate
   foreach (Input::file('files') as $key => $file) {
        // return var_dump($file);
     if(!$file) {
      $returnObject->status = FALSE;
      $returnObject->message = 'Please input a file.';
      return Response::json($returnObject);
    }

      // check if file is image
    $validator = Validator::make(
      array('file' => $file),
      $rules
    );

    if($validator->passes()) {
      $file_size = $file->getSize();
        // check file size if exceeds 10 mb
      if($file_size > 20000000) {
        $returnObject->status = FALSE;
        $returnObject->message = $file->getClientOriginalName().' file is too large. File must be 10mb size of image.';
        return Response::json($returnObject);
      }
    } else {
      $returnObject->status = FALSE;
      $returnObject->message = $file->getClientOriginalName().' file is not valid. Only accepts Image.';
      return Response::json($returnObject);
    }
  }

  $returnObject->status = TRUE;
  $returnObject->message = 'Success.';
  $ids = StringHelper::getSubAccountsID($findUserID);
  $user_id = StringHelper::getUserId($findUserID);
  $check_user_balance = DB::table('e_wallet')->where('UserID', $user_id)->first();

  // check if enable to access feature
  $transaction_access = MemberHelper::checkMemberAccessTransactionStatus($user_id, 'non_panel');

  if($transaction_access)	{
    $returnObject->status = FALSE;
    $returnObject->status_type = 'access_block';
    $returnObject->head_message = 'E-Claim Unavailable';
    $returnObject->message = 'Sorry, your account is not enabled to access this provider at the moment. Kindly contact your HR for more details.';
    return Response::json($returnObject);
  }

  $customer_id = PlanHelper::getCustomerId($user_id);

  // $checkSpendingAccessTransaction = \SpendingHelper::checkSpendingCreditsAccessNonPanel($customer_id);

  // if($checkSpendingAccessTransaction['enable'] == false) {
  //   $returnObject->status = FALSE;
  //   $returnObject->status_type = 'zero_balance';
  //   $returnObject->head_message = 'E-Claim Unavailable';
  //   $returnObject->message = 'Sorry, your account is not enabled to access this feature at the moment. Kindly contact your HR for more details.';
  //   $returnObject->sub_message = '';
  //   return Response::json($returnObject);
  // }
  $spending = CustomerHelper::getAccountSpendingStatus($customer_id);

  if($input['spending_type'] == "medical" && $spending['medical_non_panel_submission'] == false || $input['spending_type'] == "wellness" && $spending['wellness_non_panel_submission'] == false) {
    $returnObject->status = FALSE;
    $returnObject->status_type = 'access_block';
    $returnObject->head_message = 'E-Claim Unavailable';
    $returnObject->message = 'Sorry, your account is not enabled to access this feature at the moment. Kindly contact your HR for more details.';
    $returnObject->sub_message = '';
    return Response::json($returnObject);
  }

  $user_type = PlanHelper::getUserAccountType($input['user_id']);

  if($user_type == "employee") {
    $user_plan_history = DB::table('user_plan_history')->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
    $customer_active_plan = DB::table('customer_active_plan')
    ->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
    ->first();
  } else {
    $user_plan_history = DB::table('dependent_plan_history')->where('user_id', $input['user_id'])->orderBy('created_at', 'desc')->first();
    $customer_active_plan = DB::table('dependent_plans')
    ->where('dependent_plan_id', $user_plan_history->dependent_plan_id)
    ->first();
  }

  if($customer_active_plan->account_type == "enterprise_plan")	{
    if($input['spending_type'] == "medical" && $check_user_balance->currency_type == "myr") {
      $returnObject->status = FALSE;
      $returnObject->head_message = 'Non-Panel Error';
      $returnObject->message = 'Member is prohibited to access the medical wallet';
      return Response::json($returnObject);
    }

    $limit = $user_plan_history->total_visit_limit - $user_plan_history->total_visit_created;

    if($limit <= 0) {
      $returnObject->status = FALSE;
      $returnObject->head_message = 'Non-Panel Error';
      $returnObject->message = 'Maximum of 14 visits already reached.';
      return Response::json($returnObject);
    }

    if($limit <= 0) {
      $returnObject->status = FALSE;
      $returnObject->head_message = 'Non-Panel Error';
      $returnObject->message = 'Maximum of 14 visits already reached.';
      return Response::json($returnObject);
    }

    // check if A&E already get for 2 times
    $claim_status = EclaimHelper::checkMemberClaimAEstatus($user_id);

    if($claim_status && $input['service'] == "Accident & Emergency") {
      $returnObject->status = FALSE;
      $returnObject->head_message = '2/2 A&E used';
      $returnObject->message = "Looks like you've reached the maximum of 2 approved A&E this term.";
      return Response::json($returnObject);
    }
  }

  // // check if enable to access feature
  // $transaction_access = MemberHelper::checkMemberAccessTransactionStatus($user_id, 'non_panel');

  // if($transaction_access)	{
  //   $returnObject->status = FALSE;
  //   $returnObject->head_message = 'Non-Panel Error';
  //   $returnObject->message = 'Non-Panel function is disabled for your company.';
  //   return Response::json($returnObject);
  // }

  $input_amount = 0;
  if($check_user_balance->currency_type == strtolower($input['currency_type']) && $check_user_balance->currency_type == "myr") {
    $input_amount = trim($input['amount']);
  } else {
    if(Input::has('currency_type') && $input['currency_type'] != null) {
      if(strtolower($input['currency_type']) == "myr" && $check_user_balance->currency_type == "sgd") {
        $input_amount = $input['amount'] / $input['currency_exchange_rate'];
      } else if (strtolower($input['currency_type']) == "sgd" && $check_user_balance->currency_type == "myr") {
        $input_amount = $input['amount'] * $input['currency_exchange_rate'];
      } else {
        $input_amount = trim($input['amount']);
      }
    } else {
      $input_amount = trim($input['amount']);
    }
  }

  $date = date('Y-m-d', strtotime($input['date']));
  if($customer_active_plan && $customer_active_plan->account_type != "enterprise_plan") {
    $spending = EclaimHelper::getSpendingBalance($user_id, $date, strtolower($input['spending_type']));
    $balance = number_format($spending['balance'], 2);
    $amount = trim($input_amount);
    $balance = TransactionHelper::floatvalue($balance);

    if(!$check_user_balance) {
      $returnObject->status = FALSE;
      $returnObject->head_message = 'Non-Panel Error';
      $returnObject->message = 'User does not have a wallet data.';
      return Response::json($returnObject);
    }

    if($spending['back_date'] == false) {
      if($amount > $balance) {
        $returnObject->status = FALSE;
        $returnObject->head_message = 'Non-Panel Error';
        $returnObject->message = 'You have insufficient '.ucwords($input['spending_type']).' Credits for this transaction. Please check with your company HR for more details.';
        return Response::json($returnObject);
      }

      // $check_pending = EclaimHelper::checkPendingEclaims($ids, $input['spending_type']);
     $check_pending = EclaimHelper::checkPendingEclaimsByVisitDate($ids, strtolower($input['spending_type']), $date);
     if($input['spending_type'] == "medical") {
       $claim_amounts = $balance - $check_pending;
     } else {
       $claim_amounts = $balance - $check_pending;
     }

     $claim_amounts = trim($claim_amounts);

     if($amount > $claim_amounts) {
       $returnObject->status = FALSE;
       $returnObject->head_message = 'Non-Panel Error';
       $returnObject->message = 'Sorry, we are not able to process your claim. You have a claim currently waiting for approval and might exceed your credits limit. You might want to check with your company’s benefits administrator for more information.';
       return Response::json($returnObject);
     }
    }
 } else {
  $amount = trim($input_amount);
}

$time = date('h:i A', strtotime($input['time']));
$claim = new Eclaim();
$data = array(
 'user_id'   => $input['user_id'],
 'service'   => $input['service'],
 'merchant'  => $input['merchant'],
 'amount'    => $amount,
 'claim_amount' => isset($input['claim_amount']) ? trim($input['claim_amount']) : 0,
 'date'      => $date,
 'time'      => $time,
 'spending_type' => $input['spending_type'],
 'default_currency' => $check_user_balance->currency_type
);

$visit_deduction = false;

if($customer_id) {
    // get claim type service cap
  $get_company_e_claim_service = DB::table('company_e_claim_service_types')
  ->where('name', $input['service'])
  ->where('type', $input['spending_type'])
  ->where('customer_id', $customer_id)
  ->where('active', 1)
  ->first();
  if($get_company_e_claim_service) {
    $data['cap_amount'] = $get_company_e_claim_service->cap_amount;
  }
}
$data['spending_type'] = !empty($input['spending_type']) ? $input['spending_type'] : "medical";
if($customer_active_plan->account_type == "enterprise_plan")  {
  $service = DB::table('health_types')->where('name', trim($input['service']))->where('type', 'medical')->where('visit_deduction', 1)->first();

  if($service) {
    $data['cap_amount'] = (float)$service->cap_amount_enterprise;
    $data['enterprise_visit_deduction'] = 1;
  }
}

if(Input::has('currency_type') && $input['currency_type'] != null) {
  $data['currency_type'] = strtolower($input['currency_type']);
  $data['currency_value'] = $input['currency_exchange_rate'];
}

try {
 $result = $claim->createEclaim($data);
 $id = $result->id;

 if($result) {

  // deduct visit for enterprise plan user
  if($customer_active_plan->account_type == "enterprise_plan")	{
    // check if service is enable for deduction
    $service = DB::table('health_types')->where('name', trim($input['service']))->where('type', 'medical')->where('visit_deduction', 1)->first();

    if($service) {
      MemberHelper::deductPlanHistoryVisit($input['user_id']);
    }
  }

  $e_claim_docs = new EclaimDocs( );
    // loop ang process
  foreach (Input::file('files') as $key => $file) {
   $file_name = time().' - '.$file->getClientOriginalName();
   if($file->getClientOriginalExtension() == "pdf") {
    $receipt_file = $file_name;
    $receipt_type = "pdf";
    $file->move(public_path().'/receipts/', $file_name);

    $receipt = array(
      'e_claim_id'    => $id,
      'doc_file'      => $receipt_file,
      'file_type'     => $receipt_type
    );

    $result_doc = $e_claim_docs->createEclaimDocs($receipt);
  } else if($file->getClientOriginalExtension() == "xls" || $file->getClientOriginalExtension() == "xlsx") {
    $receipt_file = $file_name;
    $receipt_type = "xls";
    $file->move(public_path().'/receipts/', $file_name);

    $receipt = array(
      'e_claim_id'    => $id,
      'doc_file'      => $receipt_file,
      'file_type'     => $receipt_type
    );

    $result_doc = $e_claim_docs->createEclaimDocs($receipt);
  } else {
    $file_name = StringHelper::get_random_password(6).' - '.$file_name;
    $file->move(public_path().'/temp_uploads/', $file_name);
    $result_doc = Queue::connection('redis_high')->push('\EclaimFileUploadQueue', array('file' => public_path().'/temp_uploads/'.$file_name, 'e_claim_id' => $id));
    $receipt = array(
      'file_type'     => "image"
    );
  }

  if($result_doc) {
    if($receipt['file_type'] != "image" || $receipt['file_type'] !== "image") {
     //   aws
     $s3 = AWS::get('s3');
     $s3->putObject(array(
      'Bucket'     => 'mednefits',
      'Key'        => 'receipts/'.$file_name,
      'SourceFile' => public_path().'/receipts/'.$file_name,
    ));
   }

   try {
    //  logs
    $admin_logs = array(
      'admin_id'  => $input['user_id'],
      'admin_type' => 'member',
      'type'      => 'admin_employee_create_e_claim_details',
      'data'      => SystemLogLibrary::serializeData($result)
    );
    SystemLogLibrary::createAdminLog($admin_logs);
   } catch(Exeption $e) {

   }

 } else {
  $email = [];
  $email['end_point'] = url('v2/user/create_e_claim', $parameter = array(), $secure = null);
  $email['logs'] = 'E-Claim Mobile Receipt Submission - '.$e;
  $email['emailSubject'] = 'Error log.';
  EmailHelper::sendErrorLogs($email);
  $returnObject->status = TRUE;
  $returnObject->message = 'E-Claim created successfully but failed to create E-Receipt.';
}
                  // sleep(1);
}

                                // get customer id
$customer_id = StringHelper::getCustomerId($user_id);

if($customer_id) {
                                    // send notification
 $user = DB::table('user')->where('UserID', $findUserID)->first();
 Notification::sendNotificationToHR('Employee E-Claim', 'Employee '.ucwords($user->Name).' created an E-Claim.', url('company-benefits-dashboard#/e-claim', $parameter = array(), $secure = null), $customer_id, 'https://www.medicloud.sg/assets/new_landing/images/favicon.ico');
}
EclaimHelper::sendEclaimEmail($user_id, $id);
$returnObject->status = TRUE;
$returnObject->message = 'E-Claim successfully created.';

}
} catch(Exception $e) {
                          // send email logs
       $email = [];
       $email['end_point'] = url('v2/user/create_e_claim', $parameter = array(), $secure = null);
       $email['logs'] = 'E-Claim Submission - '.$e;
       $email['emailSubject'] = 'Error log.';
       DB::table('e_claim')->where('e_claim_id', $id)->delete();
       EmailHelper::sendErrorLogs($email);
       $returnObject->status = FALSE;
       $returnObject->message = 'E-Claim failed to create. Please contact Mednefits Team.';
     }

     return Response::json($returnObject);
     } else {
      $returnObject->status = FALSE;
      $returnObject->message = StringHelper::errorMessage("Token");
      return Response::json($returnObject);
    }
    } else {
     $returnObject->status = FALSE;
     $returnObject->message = StringHelper::errorMessage("Token");
     return Response::json($returnObject);
   }
   } else {
    $returnObject->status = FALSE;
    $returnObject->message = StringHelper::errorMessage("Token");
    return Response::json($returnObject);
  }
}

public function payCreditsNew( )
{
 $AccessToken = new Api_V1_AccessTokenController();
 $returnObject = new stdClass();
 $authSession = new OauthSessions();
 $getRequestHeader = StringHelper::requestHeader();
      // $input =  json_decode(file_get_contents('php://input'), true);
 $input = Input::all();

 if(!empty($getRequestHeader['Authorization'])){
  $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
  if($getAccessToken){
   $findUserID = $authSession->findUserID($getAccessToken->session_id);
              // return $findUserID;
   if($findUserID){
    $email = [];
    if(!isset($input['services'])) {
     $returnObject->status = FALSE;
     $returnObject->message = 'Please choose a service.';
     return Response::json($returnObject);
     } else if(sizeof($input['services']) == 0) {
       $returnObject->status = FALSE;
       $returnObject->message = 'Please choose a service.';
       return Response::json($returnObject);
     }

     if(!isset($input['clinic_id'])) {
       $returnObject->status = FALSE;
       $returnObject->message = 'Please choose a clinic.';
       return Response::json($returnObject);
     }

     if(!isset($input['amount'])) {
       $returnObject->status = FALSE;
       $returnObject->message = 'Please enter an amount.';
       return Response::json($returnObject);
     }

 // check block access
     $block = PlanHelper::checkCompanyBlockAccess($findUserID, $input['clinic_id']);

     if($block) {
       $returnObject->status = FALSE;
       $returnObject->message = 'Clinic not accessible to your Company. Please contact Your company for more information.';
       return Response::json($returnObject);
     }

     $lite_plan_status = false;
     $clinic_peak_status = false;
                  // $currency = StringHelper::getMYRSGD();
     $currency = 3.00;
     $service_id = $input['services'][0];
                  // check user type
     $type = StringHelper::checkUserType($findUserID);
     $lite_plan_status = StringHelper::newLitePlanStatus($findUserID);

     $user = DB::table('user')->where('UserID', $findUserID)->first();
     if((int)$type['user_type'] == 5 && (int)$type['access_type'] == 0 || (int)$type['user_type'] == 5 && (int)$type['access_type'] == 1)
     {
     $user_id = $findUserID;
     $customer_id = $findUserID;
     $email_address = $user->Email;
     $dependent_user = false;
     } else {
                    // find owner
       $owner = DB::table('employee_family_coverage_sub_accounts')
       ->where('user_id', $findUserID)
       ->first();
       $user_id = $owner->owner_id;
       $user_email = DB::table('user')->where('UserID', $user_id)->first();
       $email_address = $user_email->Email;
       $customer_id = $findUserID;
       $dependent_user = true;
     }

// get clinic info and type
     $clinic = DB::table('clinic')->where('ClinicID', $input['clinic_id'])->first();
     $clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
     $consultation_fees = 0;
// check user credits and amount key in

     $spending_type = "medical";
     $wallet_user = DB::table('e_wallet')->where('UserID', $user_id)->first();


     if($clinic_type->spending_type == "medical") {
       $user_credits = self::floatvalue($wallet_user->balance);
       $spending_type = "medical";
       } else {
         $user_credits = self::floatvalue($wallet_user->wellness_balance);
         $spending_type = "wellness";
       }

       $input_amount = self::floatvalue($input['amount']);
       if($clinic->currency_type == "myr") {
         $total_amount = $input_amount / 3;
       // $total_amount = $input_amount;
         } else {
           $total_amount = $input_amount;
         }

         $clinic_co_payment = TransactionHelper::getCoPayment($clinic, date('Y-m-d H:i:s'), $user_id);
         $peak_amount = $clinic_co_payment['peak_amount'];
         $co_paid_amount = $clinic_co_payment['co_paid_amount'];
         $co_paid_status = $clinic_co_payment['co_paid_status'];
         $consultation_fees = $clinic_co_payment['consultation_fees'] == 0 ? $clinic->consultation_fees : $clinic_co_payment['consultation_fees'];

// check if user has a plan tier
         $plan_tier = PlanHelper::getEmployeePlanTier($customer_id, $user_id);
         $cap_amount = 0;
         if($plan_tier) {
          if($wallet_user->cap_per_visit_medical > 0) {
            $cap_amount = $wallet_user->cap_per_visit_medical;
            } else {
              $cap_amount = $plan_tier->gp_cap_per_visit;
            }
            } else {
              if($wallet_user->cap_per_visit_medical > 0) {
                $cap_amount = $wallet_user->cap_per_visit_medical;
              }
            }

            $credits = 0;
            $cash = 0;
            $half_credits = false;
// return $cap_amount;
            if($cap_amount > 0) {
              if($total_amount > $cap_amount) {
                $cash = $total_amount - $cap_amount;
                $credits = $cap_amount;
                $half_credits = true;
                } else {
                  $credits = $total_amount;
                }
                } else {
                  $credits = $total_amount;
                  $cash_cost = 0;
                }
// return array('credits' => $credits, 'cash' => $cash, 'half_credits' => $half_credits);

                if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
                 $total_credits = self::floatvalue($credits) + $co_paid_amount;
                 if($total_credits > $user_credits) {
                  $returnObject->status = FALSE;
                  $returnObject->message = 'You have insufficient '.$spending_type.' credits in your account';
                  $returnObject->sub_mesage = 'Lite Plan users needs an additional S$'.number_format($co_paid_amount, 2).' to be able to pay for the transaction.';
                  return Response::json($returnObject);
                }
                } else {
                  $total_credits = self::floatvalue($credits);
                  if(self::floatvalue($credits) > $user_credits) {
                    $returnObject->status = FALSE;
                    $returnObject->message = 'You have insufficient '.$spending_type.' credits in your account';
                    $returnObject->sub_mesage = 'You may choose to pay directly to health provider.';
                    return Response::json($returnObject);
                  }
                }

// return $credits;


               // else {
                $transaction = new Transaction();
                $wallet = new Wallet( );

                    // check if multiple services selected
                $multiple = false;
                if(sizeof($input['services']) > 1) {
                 $services = 0;
                 $multiple_service_selection = 1;
                 $multiple = true;
                 } else {
                   $services = $input['services'][0];
                   $multiple_service_selection = 0;
                   $multiple = false;
                 }

                 $consultation = 0;

                 if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
                   $lite_plan_enabled = 1;
                   } else {
                     $lite_plan_enabled = 0;
                   }

                   $data = array(
                   'UserID'                => $customer_id,
                   'ProcedureID'           => $services,
                   'date_of_transaction'   => date('Y-m-d H:i:s'),
                   'claim_date'            => date('Y-m-d H:i:s'),
                   'ClinicID'              => $input['clinic_id'],
                   'procedure_cost'        => $total_amount,
                   'AppointmenID'          => 0,
                   'revenue'               => 0,
                   'debit'                 => 0,
                   'clinic_discount'       => $clinic->discount,
                   'medi_percent'          => $clinic->medicloud_transaction_fees,
                   'currency_type'         => $clinic->currency_type,
                   'wallet_use'            => 1,
                   'current_wallet_amount' => $wallet_user->balance,
                   'credit_cost'           => $credits,
                   'paid'                  => 1,
                   'co_paid_status'            => $co_paid_status,
                   'co_paid_amount'            => $co_paid_amount,
                   'DoctorID'              => 0,
                   'backdate_claim'        => 1,
                   'in_network'            => 1,
                   'mobile'                => 1,
                   'multiple_service_selection' => $multiple_service_selection,
                   'currency_type'         => $clinic->currency_type,
                   'lite_plan_enabled'     => $lite_plan_enabled,
                   'cash_cost'            => $cash,
                   'half_credits'          => $half_credits == true ? 1 : 0,
                   'consultation_fees'      => $consultation_fees,
                   'cap_per_visit'        => $cap_amount
                   );

                   if((int)$clinic_type->lite_plan_enabled == 1 && $lite_plan_status) {
  // $total_amount = $total_amount + $co_paid_amount;
                    if($clinic->currency_type == "myr") {
                      $consultation = number_format($co_paid_amount / 3, 2);
                      } else {
                        $consultation = number_format($co_paid_amount, 2);
                      }

                    }

                    if($clinic_peak_status) {
                     $data['peak_hour_status'] = 1;
                     if($clinic->co_paid_status == 1 || $clinic->co_paid_status == "1") {
                      $gst_peak = $peak_amount * $clinic->gst_percent;
                      $data['peak_hour_amount'] = $peak_amount + $gst_peak;
                      } else {
                        $data['peak_hour_amount'] = $peak_amount;
                      }

                    }

                    if($currency) {
                     $data['currency_amount'] = $currency;
                   }

                   try {
                     $result = $transaction->createTransaction($data);
                     $transaction_id = $result->id;
                     if($result) {
                      $procedure = "";
                      $procedure_temp = "";
                                // insert transation services
                      $ts = new TransctionServices( );
                      $save_ts = $ts->createTransctionServices($input['services'], $transaction_id);

                      if($multiple == true) {
                       foreach ($input['services'] as $key => $value) {
                        $procedure_data = DB::table('clinic_procedure')->where('ProcedureID', $value)->first();
                        $procedure_temp .= ucwords($procedure_data->Name).',';
                      }
                      $procedure = rtrim($procedure_temp, ',');
                      } else {
                       $procedure_data = DB::table('clinic_procedure')->where('ProcedureID', $service_id)->first();
                       $procedure = ucwords($procedure_data->Name);
                     }

                                // deduct medical/wellness credit
                     $history = new WalletHistory( );
                     if($spending_type == "medical") {
                       $credits_logs = array(
                       'wallet_id'     => $wallet_user->wallet_id,
                       'credit'        => $credits,
                       'logs'          => 'deducted_from_mobile_payment',
                       'running_balance' => $wallet_user->balance - $credits,
                       'where_spend'   => 'in_network_transaction',
                       'id'            => $transaction_id
                       );

                       if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
                        $lite_plan_credits_log = array(
                        'wallet_id'     => $wallet_user->wallet_id,
                        'credit'        => $consultation_fees,
                        'logs'          => 'deducted_from_mobile_payment',
                        'running_balance' => $wallet_user->balance - $credits - $consultation_fees,
                        'where_spend'   => 'in_network_transaction',
                        'id'            => $transaction_id,
                        'lite_plan_enabled' => 1,
                        );
                      }
                      } else {
                       $credits_logs = array(
                       'wallet_id'     => $wallet_user->wallet_id,
                       'credit'        => $input_amount,
                       'logs'          => 'deducted_from_mobile_payment',
                       'running_balance' => $wallet_user->wellness_balance - $credits,
                       'where_spend'   => 'in_network_transaction',
                       'id'            => $transaction_id
                       );

                       if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
                        $lite_plan_credits_log = array(
                        'wallet_id'     => $wallet_user->wallet_id,
                        'credit'        => $consultation_fees,
                        'logs'          => 'deducted_from_mobile_payment',
                        'running_balance' => $wallet_user->balance - $credits - $consultation_fees,
                        'where_spend'   => 'in_network_transaction',
                        'id'            => $transaction_id,
                        'lite_plan_enabled' => 1,
                        );
                      }
                    }

                    try {

                     if($spending_type == "medical") {
                      $deduct_history = \WalletHistory::create($credits_logs);
                      $wallet_history_id = $deduct_history->id;

                      if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
                       \WalletHistory::create($lite_plan_credits_log);
                     }
                     } else {
                      $deduct_history = \WellnessWalletHistory::create($credits_logs);
                      $wallet_history_id = $deduct_history->id;

                      if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
                       \WellnessWalletHistory::create($lite_plan_credits_log);
                     }
                   }


                   if($deduct_history) {
                    try {
                     if($spending_type == "medical") {
                      $wallet->deductCredits($user_id, $total_amount);

                      if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
                       $wallet->deductCredits($user_id, $consultation_fees);
                     }
                     } else {
                      $wallet->deductWellnessCredits($user_id, $total_amount);

                      if($lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1) {
                       $wallet->deductWellnessCredits($user_id, $consultation_fees);
                     }
                   }

                   $trans_id = str_pad($transaction_id, 6, "0", STR_PAD_LEFT);
                   $SGD = null;

                   if($clinic->currency_type == "myr") {
                    $currency_symbol = "RM ";
                    $email_currency_symbol = "RM";
                    $total_amount = $total_amount * 3;
                    } else {
                      $email_currency_symbol = "S$";
                      $currency_symbol = '$SGD ';
                    }

                    $transaction_results = array(
                    'clinic_name'       => ucwords($clinic->Name),
                    'total_payment'     => number_format($total_amount, 2),
                    'credits'            => $clinic->currency_type == "myr" ? number_format($credits * 3, 2) : number_format($credits, 2),
                    'cash'              => $clinic->currency_type == "myr" ? number_format($cash * 3, 2) : number_format($cash, 2),
                    'transaction_time'  => date('Y-m-d h:i', strtotime($result->created_at)),
                    'transation_id'     => strtoupper(substr($clinic->Name, 0, 3)).$trans_id,
                    'services'          => $procedure,
                    'currency_symbol'   => $email_currency_symbol,
                    'dependent_user'    => $dependent_user,
                    'half_credits_payment' => $half_credits
                    );

                    Notification::sendNotification('Customer Payment - Mednefits', 'User '.ucwords($user->Name).' has made a payment for '.$procedure.' at '.$currency_symbol.$input_amount.' to your clinic', url('app/setting/claim-report', $parameter = array(), $secure = null), $input['clinic_id'], $user->Image);

                    $type = "";
                    $image = "";

                    $clinic_type_properties = TransactionHelper::getClinicImageType($clinic_type);
                    $type = $clinic_type_properties['type'];
                    $image = $clinic_type_properties['image'];

   // check if check_in_id exist
                    if(!empty($input['check_in_id']) && $input['check_in_id'] != null) {
    // check check_in_id data
                      $check_in = DB::table('user_check_in_clinic')
                      ->where('check_in_id', $input['check_in_id'])
                      ->first();
                      if($check_in) {
      // update check in date
                        DB::table('user_check_in_clinic')
                        ->where('check_in_id', $input['check_in_id'])
                        ->update(['check_out_time' => date('Y-m-d H:i:s'), 'id' => $transaction_id, 'status' => 1]);
                        PusherHelper::sendClinicCheckInRemoveNotification($input['check_in_id'], $check_in->clinic_id);
                      }
                    }

// return $transaction_results;

                    $returnObject->status = TRUE;
                    $returnObject->message = 'Payment Successfull';
                    $returnObject->data = $transaction_results;
  // send email
                    $email['member'] = ucwords($user->Name);
                    $email['credits'] = $clinic->currency_type == "myr" ? number_format($credits * 3, 2) : number_format($credits, 2);
                    $email['transaction_id'] = strtoupper(substr($clinic->Name, 0, 3)).$trans_id;
                    $email['trans_id'] = $transaction_id;
                    $email['transaction_date'] = date('d F Y, h:ia');
                    $email['health_provider_name'] = ucwords($clinic->Name);
                    $email['health_provider_address'] = $clinic->Address;
                    $email['health_provider_city'] = $clinic->City;
                    $email['health_provider_country'] = $clinic->Country;
                    $email['health_provider_phone'] = $clinic->Phone;
                    $email['service'] = ucwords($clinic_type->Name).' - '.$procedure;
                    $email['emailSubject'] = 'Member - Successful Transaction';
                    $email['emailTo'] = $email_address ? $email_address : 'info@medicloud.sg';
                    $email['emailName'] = ucwords($user->Name);
                    $email['url'] = 'http://staging.medicloud.sg';
                    $email['clinic_type_image'] = $image;
                    $email['transaction_type'] = 'Mednefits Credits';
                    $email['emailPage'] = 'email-templates.member-successful-transaction-v2';
                    $email['dl_url'] = url();
                    $email['lite_plan_enabled'] = $clinic_type->lite_plan_enabled;
                    $email['lite_plan_status'] = $lite_plan_status && (int)$clinic_type->lite_plan_enabled == 1 ? TRUE : FAlSE ;
                    $email['total_amount'] = $clinic->currency_type == "myr" ? number_format($total_credits * 3, 2) : number_format($total_credits, 2);
                    $email['consultation'] = $consultation_fees;
                    $email['currency_symbol'] = $email_currency_symbol;
                    $email['pdf_file'] = 'pdf-download.member-successful-transac-v2';
// return $email;
                    try {
                      EmailHelper::sendPaymentAttachment($email);
    // send to clinic
                      $clinic_email = DB::table('user')->where('UserType', 3)->where('Ref_ID', $input['clinic_id'])->first();

                      if($clinic_email) {
                       $email['emailSubject'] = 'Health Partner - Successful Transaction By Mednefits Credits';
                       $email['nric'] = $user->NRIC;
                       $email['emailTo'] = $clinic_email->Email;
                                                      // $email['emailTo'] = 'allan.alzula.work@gmail.com';
                       $email['emailPage'] = 'email-templates.health-partner-successful-transaction-v2';
                       $api = "https://admin.medicloud.sg/send_clinic_transaction_email";
                       $email['pdf_file'] = 'pdf-download.health-partner-successful-transac-v2';
     // httpLibrary::postHttp($api, $email, array());
                       EmailHelper::sendPaymentAttachment($email);
                     }
                     $returnObject->status = TRUE;
                     $returnObject->message = 'Payment Successfull';
                     $returnObject->data = $transaction_results;
                     } catch(Exception $e) {
                      $email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
                      $email['logs'] = 'Mobile Payment Credits Send Email Attachments - '.$e;
                      $email['emailSubject'] = 'Error log.';
                      EmailHelper::sendErrorLogs($email);
                      $returnObject->status = TRUE;
                      $returnObject->message = 'Payment Successfull';
                      $returnObject->data = $transaction_results;
                    }

                    } catch(Exception $e) {
                     $email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
                     $email['logs'] = 'Mobile Payment Credits - '.$e;
                     $email['emailSubject'] = 'Error log. - Transaction ID: '.$transaction_id.' Wallet History ID: '.$wallet_history_id;


    // delete transaction history log
                     $transaction->deleteFailedTransactionHistory($transaction_id);
   // delete failed wallet history
                     if($spending_type == "medical") {
                      $history->deleteFailedWalletHistory($wallet_history_id);
         // credits back
                      $wallet->addCredits($user_id, $credits);
                      } else {
                        \WellnessWalletHistory::where('wellness_wallet_history_id', $wallet_history_id)->delete();
                        $wallet->addWellnessCredits($user_id, $credits);
                      }
                      $returnObject->status = FALSE;
                      $returnObject->message = 'Payment unsuccessfull. Please try again later';
                      EmailHelper::sendErrorLogs($email);
                    }


                    } else {
                      $returnObject->status = FALSE;
                      $returnObject->message = 'Payment unsuccessfull. Please try again later';
                    }

                    } catch(Exception $e) {
                     $email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
                     $email['logs'] = 'Mobile Payment Credits - '.$e;
                     $email['emailSubject'] = 'Error log. - Transaction ID: '.$transaction_id;

                                      // delete transaction history log
                     $transaction->deleteFailedTransactionHistory($transaction_id);

                     $returnObject->status = FALSE;
                     $returnObject->message = 'Payment unsuccessfull. Please try again later';

                     EmailHelper::sendErrorLogs($email);
                   }
                 }
                 } catch(Exception $e) {
                   $returnObject->status = FALSE;
                   $returnObject->message = 'Cannot process payment credits. Please try again.';
                              // send email logs
                   $email['end_point'] = url('v2/clinic/send_payment', $parameter = array(), $secure = null);
                   $email['logs'] = 'Mobile Payment Credits - '.$e;
                   $email['emailSubject'] = 'Error log.';
                   EmailHelper::sendErrorLogs($email);
                 }
                 return Response::json($returnObject);
                 } else {
                  $returnObject->status = FALSE;
                  $returnObject->message = StringHelper::errorMessage("Token");
                  return Response::json($returnObject);
                }
                } else {
                 $returnObject->status = FALSE;
                 $returnObject->message = StringHelper::errorMessage("Token");
                 return Response::json($returnObject);
               }
               } else {
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
                return Response::json($returnObject);
              }
            }

            public function getCurrencyLists( )
            {

              $returnObject = new stdClass();
              $returnObject->status = TRUE;
              $returnObject->data = EclaimHelper::getCurrencies();
              return Response::json($returnObject);
            }

            public function getAppUpdateNotification( )
            {
              $AccessToken = new Api_V1_AccessTokenController();
              $returnObject = new stdClass();
              $authSession = new OauthSessions();
              $getRequestHeader = StringHelper::requestHeader();

              if(!empty($getRequestHeader['Authorization'])){
                $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
                if($getAccessToken){
                 $findUserID = $authSession->findUserID($getAccessToken->session_id);
                 if($findUserID){
                  $returnObject->status = TRUE;
                  $returnObject->message = 'Success.';

                  $user_id = StringHelper::getUserId($findUserID);
                  $notification = DB::table('user_notification')
                  ->where('user_id', $user_id)
                  ->where('notified', 0)
                  ->where('type', 'app_update')
                  ->where('platform', 'all')
                  ->orderBy('created_at', 'desc')
                  ->first();
                  if($notification) {
                    $temp = array(
                    'notification_id' => $notification->user_notification_id,
                    'message'         => $notification->data
                    );
                    $returnObject->data = $temp;
                    } else {
                      $returnObject->data = null;
                    }
                    return Response::json($returnObject);
                    } else {
                      $returnObject->status = FALSE;
                      $returnObject->message = StringHelper::errorMessage("Token");
                      return Response::json($returnObject);
                    }
                    } else {
                     $returnObject->status = FALSE;
                     $returnObject->message = StringHelper::errorMessage("Token");
                     return Response::json($returnObject);
                   }
                   } else {
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("Token");
                    return Response::json($returnObject);
                  }
                }

                public function removeCheckIn( )
                {
                  $AccessToken = new Api_V1_AccessTokenController();
                  $returnObject = new stdClass();
                  $authSession = new OauthSessions();
                  $getRequestHeader = StringHelper::requestHeader();
                  $input = Input::all();

                  if(empty($input['check_in_id']) || $input['check_in_id'] == null) {
                    $returnObject->status = FALSE;
                    $returnObject->message = 'Check-In ID is required.';
                    return Response::json($returnObject);
                  }

                  if(!empty($getRequestHeader['Authorization'])){
                    $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
                    if($getAccessToken){
                     $findUserID = $authSession->findUserID($getAccessToken->session_id);
                     if($findUserID){
                      $returnObject->status = TRUE;
                      $returnObject->message = 'Success.';
    // check if notification exits
                      $check = DB::table('user_check_in_clinic')
                      ->where('check_in_id', $input['check_in_id'])
                      ->where('user_id', $findUserID)
                      ->where('status', 0)
                      ->first();

                      if(!$check) {
                        $returnObject->status = TRUE;
                        $returnObject->message = 'Success.';
                        return Response::json($returnObject);
                      }

                      DB::table('user_check_in_clinic')
                      ->where('check_in_id', $input['check_in_id'])
                      ->where('user_id', $findUserID)
                      ->delete();
                      PusherHelper::sendClinicCheckInRemoveNotification($input['check_in_id'], $check->clinic_id);
                      return Response::json($returnObject);
                      } else {
                        $returnObject->status = FALSE;
                        $returnObject->message = StringHelper::errorMessage("Token");
                        return Response::json($returnObject);
                      }
                      } else {
                       $returnObject->status = FALSE;
                       $returnObject->message = StringHelper::errorMessage("Token");
                       return Response::json($returnObject);
                     }
                     } else {
                      $returnObject->status = FALSE;
                      $returnObject->message = StringHelper::errorMessage("Token");
                      return Response::json($returnObject);
                    }
                  }

                  public function updateUserNotification( )
                  {
                    $AccessToken = new Api_V1_AccessTokenController();
                    $returnObject = new stdClass();
                    $authSession = new OauthSessions();
                    $getRequestHeader = StringHelper::requestHeader();
                    $input = Input::all();

                    if(empty($input['notification_id']) || $input['notification_id'] == null) {
                      $returnObject->status = FALSE;
                      $returnObject->message = 'Notification ID is required.';
                      return Response::json($returnObject);
                    }

                    if(!empty($getRequestHeader['Authorization'])){
                      $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
                      if($getAccessToken){
                       $findUserID = $authSession->findUserID($getAccessToken->session_id);
                       if($findUserID){
                        $returnObject->status = TRUE;
                        $returnObject->message = 'Success.';
                        $user_id = StringHelper::getUserId($findUserID);
            // check if notification exits
      $check = DB::table('user_notification')
      ->where('user_notification_id', $input['notification_id'])
      ->where('user_id', $user_id)
      ->where('type', 'app_update')
      ->where('platform', 'all')
      ->first();

      if(!$check) {
        $returnObject->status = FALSE;
        $returnObject->message = 'User Notification does not exist.';
        return Response::json($returnObject);
      }

      DB::table('user_notification')
      ->where('user_notification_id', $input['notification_id'])
      ->update(['notified' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

      return Response::json($returnObject);
    } else {
      $returnObject->status = FALSE;
      $returnObject->message = StringHelper::errorMessage("Token");
      return Response::json($returnObject);
    }
  } else {
   $returnObject->status = FALSE;
   $returnObject->message = StringHelper::errorMessage("Token");
   return Response::json($returnObject);
 }
} else {
  $returnObject->status = FALSE;
  $returnObject->message = StringHelper::errorMessage("Token");
  return Response::json($returnObject);
}
}
  public function checkEclaimVisit( )
  {
    $AccessToken = new Api_V1_AccessTokenController();
    $returnObject = new stdClass();
    $authSession = new OauthSessions();
    $getRequestHeader = StringHelper::requestHeader();
    $input = Input::all();

    if(empty($input['visit_date']) || $input['visit_date'] == null) {
      $returnObject->status = FALSE;
      $returnObject->message = 'visit_date is required';
      return Response::json($returnObject);
    }

    if(empty($input['spending_type']) || $input['spending_type'] == null) {
      $returnObject->status = FALSE;
      $returnObject->message = 'spending_type is required';
      return Response::json($returnObject);
    }

    if(!empty($getRequestHeader['Authorization'])){
      $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
      if($getAccessToken){
         $findUserID = $authSession->findUserID($getAccessToken->session_id);
         if($findUserID){
          $user_id = StringHelper::getUserId($findUserID);
          $user_active_plan_history = DB::table('user_plan_history')
                                      ->where('user_id', $user_id)
                                      ->orderBy('created_at', 'desc')
                                      ->first();

          $customer_active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_active_plan_history->customer_active_plan_id)->first();
          if($customer_active_plan->account_type != "enterprise_plan" || $customer_active_plan->account_type == "enterprise_plan" && $input['spending_type'] == "wellness") {
            $date = date('Y-m-d', strtotime($input['visit_date']));
            $spending = EclaimHelper::getSpendingBalance($user_id, $date, strtolower($input['spending_type']));
            // return $spending;
            $ids = StringHelper::getSubAccountsID($user_id);
            // get pending back dates
            $claim_amounts = EclaimHelper::checkPendingEclaimsByVisitDate($ids, strtolower($input['spending_type']), $date);
            $balance = $spending['balance'] - $claim_amounts;

            $term_status = null;
            if($spending['back_date'] == true) {
              $term_status = "Last";
            } else {
              $term_status = "Current";
            }

            $data = array(
              'balance' => DecimalHelper::formatDecimal($balance),
              'term_status' => $term_status,
              'currency_type' => $spending['currency_type'],
              'last_term' => $spending['back_date'],
              'claim_amounts' => $claim_amounts
            );
          } else {
            $wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
            $data = array(
              'balance' => 99999,
              'term_status' => 'Current',
              'currency_type' => $wallet->currency_type,
              'last_term' => false,
              'claim_amounts' => 0
            );
          }

          $returnObject->status = true;
          $returnObject->data = $data;
          return Response::json($returnObject);
        } else {
          $returnObject->status = FALSE;
          $returnObject->message = StringHelper::errorMessage("Token");
          return Response::json($returnObject);
        }
      } else {
       $returnObject->status = FALSE;
       $returnObject->message = StringHelper::errorMessage("Token");
       return Response::json($returnObject);
     }
    } else {
      $returnObject->status = FALSE;
      $returnObject->message = StringHelper::errorMessage("Token");
      return Response::json($returnObject);
    }
  }

  public function getDatesCoverage( )
  {
    $AccessToken = new Api_V1_AccessTokenController();
    $returnObject = new stdClass();
    $authSession = new OauthSessions();
    $getRequestHeader = StringHelper::requestHeader();

    if(!empty($getRequestHeader['Authorization'])){
      $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
      if($getAccessToken){
         $findUserID = $authSession->findUserID($getAccessToken->session_id);
         if($findUserID){
          $user_id = StringHelper::getUserId($findUserID);
          $data = MemberHelper::getMemberSpendingCoverageDate($user_id);
          $returnObject->status = true;
          $returnObject->data = ['start' => date('Y-m-d', strtotime($data['start_date'])), 'end' => date('Y-m-d', strtotime($data['end_date'])), 'today' => $data['today'], 'grace_period' => $data['grace_period']];
          return Response::json($returnObject);
        } else {
          $returnObject->status = FALSE;
          $returnObject->message = StringHelper::errorMessage("Token");
          return Response::json($returnObject);
        }
      } else {
       $returnObject->status = FALSE;
       $returnObject->message = StringHelper::errorMessage("Token");
       return Response::json($returnObject);
     }
    } else {
      $returnObject->status = FALSE;
      $returnObject->message = StringHelper::errorMessage("Token");
      return Response::json($returnObject);
    }
  }

  public function getMemberAccountSpendingStatus( )
  {
    $AccessToken = new Api_V1_AccessTokenController();
    $returnObject = new stdClass();
    $authSession = new OauthSessions();
    $getRequestHeader = StringHelper::requestHeader();

    if(!empty($getRequestHeader['Authorization'])){
      $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
      if($getAccessToken){
         $findUserID = $authSession->findUserID($getAccessToken->session_id);
         if($findUserID){
          $input = Input::all();
          $user_id = StringHelper::getUserId($findUserID);
          $customer_id = PlanHelper::getCustomerId($user_id);
          $type = !empty($input['type']) && $input['type'] == 'spending' ? 'spending' : 'e_claim';
          $spending = CustomerHelper::getAccountSpendingBasicPlanStatus($customer_id);
          $user_type = PlanHelper::getUserAccountType($findUserID);

          if($type == "spending") {
            $returnObject->status = true;
            // check if user id deactivated
            $deactivated = MemberHelper::checkMemberDeactivated($user_id);

            if($deactivated) {
              $returnObject->status = FALSE;
              $returnObject->status_type = 'zero_balance';
              $returnObject->head_message = 'Registration on Hold';
              $returnObject->message = 'Sorry, your account is not enabled to access this feature at the moment. Kindly contact your HR for more detail';
              $returnObject->sub_message = '';
              return Response::json($returnObject);
            }

            // if($spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" && $spending['paid_status'] == false || $spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "pre_paid" && $spending['paid_status'] == false) {
            //     $returnObject->status = FALSE;
            //     $returnObject->status_type = 'zero_balance';
            //     $returnObject->head_message = 'Registration on Hold';
            //     // $returnObject->message = 'Sorry, you have no credits to access this feature at the moment. Kindly contact your HR for more details.';
            //     $returnObject->message = 'Sorry, your account is not enabled to access this feature at the moment. Kindly contact your HR for more details.';
            //     $returnObject->sub_message = '';
            //     return Response::json($returnObject);
            // }

            // if($spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" || $spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "pre_paid") {
            //   $current_balance = PlanHelper::reCalculateEmployeeBalance($user_id);

            //   if($current_balance <= 0) {
            //     $returnObject->status = FALSE;
            //     $returnObject->status_type = 'zero_balance';
            //     $returnObject->head_message = 'Registration on Hold';
            //     $returnObject->message = 'Sorry, you have no credits to access this feature at the moment.';
            //     $returnObject->sub_message = 'Kindly contact your HR for more details.';
            //     return Response::json($returnObject);
            //   }
            // }

             // check for member transaction
             $transaction_access = MemberHelper::checkMemberAccessTransactionStatus($user_id, 'panel');
             if($transaction_access)	{
               $returnObject->status = FALSE;
               $returnObject->status_type = 'registration_hold';
               $returnObject->head_message = 'Registration On Hold';
               $returnObject->message = 'Sorry, your account is not enabled to access this feature at the moment.';
               $returnObject->sub_message = 'Kindly contact your HR for more details.';
               return Response::json($returnObject);
             }

            // // check visit limit
            if($user_type == "employee") {
              $user_plan_history = DB::table('user_plan_history')->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
              $customer_active_plan = DB::table('customer_active_plan')
              ->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
              ->first();
            } else {
              $user_plan_history = DB::table('dependent_plan_history')->where('user_id', $findUserID)->orderBy('created_at', 'desc')->first();
              $customer_active_plan = DB::table('dependent_plans')
                            ->where('dependent_plan_id', $user_plan_history->dependent_plan_id)
                            ->first();
            }

            if($customer_active_plan->account_type == "enterprise_plan")	{
              $limit = $user_plan_history->total_visit_limit - $user_plan_history->total_visit_created;

              if($limit <= 0) {
                $returnObject->status = FALSE;
                $returnObject->status_type = 'exceed_limit';
                $returnObject->head_message = '14/14 visits used';
                $returnObject->message = "Looks like you've reached the maximum of 14 visits this term.";
                $returnObject->sub_message = '';
                return Response::json($returnObject);
              }
            }

            // check member wallet spending validity
            $validity = MemberHelper::getMemberWalletValidity($user_id, 'medical');

            if(!$validity) {
              $returnObject->status = FALSE;
              $returnObject->status_type = 'zero_balance';
              $returnObject->head_message = 'Registration on Hold';
              $returnObject->message = 'Sorry, your account is not enabled to access this feature at the moment. Kindly contact your HR for more detail';
              $returnObject->sub_message = '';
              return Response::json($returnObject);
            }

            $returnObject->status = TRUE;
            $returnObject->status_type = 'with_balance';
            $returnObject->message = 'You have access this feature at the moment.';
            $returnObject->sub_message = '';
            return Response::json($returnObject);
          } else {
            $deactivated = MemberHelper::checkMemberDeactivated($user_id);

            if($deactivated) {
              $returnObject->status = FALSE;
              $returnObject->status_type = 'without_e_claim';
              $returnObject->head_message = 'E-Claim Unavailable';
              $returnObject->message = 'Sorry, your account is not enabled to access this feature at the moment. Kindly contact your HR for more detail';
              $returnObject->sub_message = '';
              return Response::json($returnObject);
            }

            // if($spending['account_type'] == "lite_plan" && $spending['medical_method'] == "pre_paid" && $spending['paid_status'] == false || $spending['account_type'] == "lite_plan" && $spending['wellness_method'] == "pre_paid" && $spending['paid_status'] == false) {
            //   $returnObject->status = FALSE;
            //   $returnObject->status_type = 'without_e_claim';
            //   $returnObject->head_message = 'E-Claim Unavailable';
            //   $returnObject->message = 'Sorry, you have no credits to access this feature at the moment.';
            //   $returnObject->sub_message = 'Kindly contact your HR for more details.';
            //   return Response::json($returnObject);
            // }

            if($spending['account_type'] == "enterprise_plan" && $spending['currency_type'] == "myr") {
              if($spending['wellness_enabled'] == false) {
                $returnObject->status = FALSE;
                $returnObject->status_type = 'without_e_claim';
                $returnObject->head_message = 'E-Claim Unavailable';
                $returnObject->message = 'Sorry, your account is not enabled to access this feature at the moment.';
                $returnObject->sub_message = 'Kindly contact your HR for more details.';
                return Response::json($returnObject);
              }
            }

            // check if e-claim platform is enable
            $customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();

            if($customer && (int)$customer->access_e_claim == 0) {
              $returnObject->status = FALSE;
              $returnObject->status_type = 'without_e_claim';
              $returnObject->head_message = 'E-Claim Unavailable';
              $returnObject->message = 'The E-Claim function has been disabled for your company.';
              $returnObject->sub_message = 'Kindly contact your HR for more details.';
              return Response::json($returnObject);
            }

            // check for member transaction
            $transaction_access = MemberHelper::checkMemberAccessTransactionStatus($user_id, 'non_panel');

            if($transaction_access)	{
              $returnObject->status = FALSE;
              $returnObject->status_type = 'without_e_claim';
              $returnObject->head_message = 'E-claim Unavailable';
              $returnObject->message = 'Sorry, your account is not enabled to access this feature at the moment.';
              $returnObject->sub_message = 'Kindly contact your HR for more details.';
              return Response::json($returnObject);
            }

            // // check visit limit
            if($user_type == "employee") {
              $user_plan_history = DB::table('user_plan_history')->where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
              $customer_active_plan = DB::table('customer_active_plan')
              ->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)
              ->first();
            } else {
              $user_plan_history = DB::table('dependent_plan_history')->where('user_id', $findUserID)->orderBy('created_at', 'desc')->first();
              $customer_active_plan = DB::table('dependent_plans')
                            ->where('dependent_plan_id', $user_plan_history->dependent_plan_id)
                            ->first();
            }

            if($customer_active_plan->account_type == "enterprise_plan")	{
              $limit = $user_plan_history->total_visit_limit - $user_plan_history->total_visit_created;

              if($limit <= 0) {
                $returnObject->status = FALSE;
                $returnObject->status_type = 'exceed_limit';
                $returnObject->head_message = '14/14 visits used';
                $returnObject->message = "Looks like you've reached the maximum of 14 visits this term.";
                $returnObject->sub_message = '';
                return Response::json($returnObject);
              }
            }

            if($customer_active_plan->account_type == "out_of_pocket")	{
              $returnObject->status = FALSE;
              $returnObject->status_type = 'without_e_claim';
              $returnObject->head_message = 'E-Claim Unavailable ';
              $returnObject->message = 'Sorry, your account is not enabled to access this feature at the moment. Kindly contact your HR for more detail';
              $returnObject->sub_message = '';
              return Response::json($returnObject);
            }

            // check member wallet spending validity
            $validity = MemberHelper::getMemberWalletValidity($user_id, 'wellness');

            if(!$validity) {
              $returnObject->status = FALSE;
              $returnObject->status_type = 'without_e_claim';
              $returnObject->head_message = 'E-Claim Unavailable ';
              $returnObject->message = 'Sorry, your account is not enabled to access this feature at the moment. Kindly contact your HR for more detail';
              $returnObject->sub_message = '';
              return Response::json($returnObject);
            }

            $returnObject->status = TRUE;
            $returnObject->status_type = 'with_e_claim';
            $returnObject->message = 'You have access this feature at the moment.';
            return Response::json($returnObject);
          }

          $returnObject->status = TRUE;
          $returnObject->status_type = 'with_balance';
          $returnObject->message = 'You have access this feature at the moment.';
          return Response::json($returnObject);
        } else {
          $returnObject->status = FALSE;
          $returnObject->message = StringHelper::errorMessage("Token");
          return Response::json($returnObject);
        }
      } else {
       $returnObject->status = FALSE;
       $returnObject->message = StringHelper::errorMessage("Token");
       return Response::json($returnObject);
     }
    } else {
      $returnObject->status = FALSE;
      $returnObject->message = StringHelper::errorMessage("Token");
      return Response::json($returnObject);
    }
  }

  public function sendOtpMobile( ) {
      $input = Input::all();

      if (isset($input['country']) && strtoupper($input['country']) == 'MYR') {
        return self::sendMYROtpMobile($input);
      } else {
        return self::sendSGDOtpMobile($input);
      }
  }

  public function checkMemberExist( ) {
      $input = Input::all();
      /*
          Developer: Stephen
          Date: Sept 16. 2020
          Description:
            Refactor code. Added verification for MY new process

          Key Legend:
            - country (either SGD or MYR)
            - mobile  (if This key is empty or not exist but NRIC is not empty it Means that login process will follow the new MY login process.)
            - nric (This key will be the identifier that new login process will MY login process)
            - passport (This key will be the identifier that new login process will MY login process)
      */

      // Check county
      if (isset($input['country']) && strtoupper($input['country']) == 'MYR') {
        // Follow the new MY login process.
        return self::MYRMemberVerification($input);
      } else {
        // Follow the SGD login process.
        return self::SGDMemberVerification($input);
      }
  }

  public function validateOtpMobile( ) {
    $input = Input::all();

    if (isset($input['country']) && strtoupper($input['country']) == 'MYR') {
      return self::validateMYROTPCode($input);
    } else {
      return self::validateSGDOTPCode($input);
    }
  }

  public function addPostalCodeMember( ) {
      $input = Input::all();
      $returnObject = new stdClass();

      if(empty($input['postal_code']) || $input['postal_code'] == null) {
        $returnObject->status = false;
        $returnObject->message = 'postal code is required.';
        return Response::json($returnObject);
      }

      if(empty($input['user_id']) || $input['user_id'] == null) {
        $returnObject->status = false;
        $returnObject->message = 'user id is required.';
        return Response::json($returnObject);
      }

      $checker = DB::table('user')
      ->select('UserID', 'Name as name', 'member_activated')
      ->where('UserID', $input['user_id'])->first();

      if(!$checker) {
        $returnObject->status = false;
        $returnObject->message = 'User not found!';
        return Response::json($returnObject);
      }

      $member_id = $checker->UserID;
      DB::table('user')->where('UserID', $member_id)->update(['Zip_Code' => $input['postal_code']]);
      $returnObject->status = true;
      $returnObject->message = 'Postal Code already set';
      $returnObject->data = $checker;
      return Response::json($returnObject);
  }

  public function createNewPasswordByMember() {
    $input = Input::all();
    $returnObject = new stdClass();

    if(empty($input['password']) || $input['password'] == null) {
        $returnObject->status = false;
        $returnObject->message = 'Password is required.';
        return Response::json($returnObject);
    }

    if(empty($input['password_confirm']) || $input['password_confirm'] == null) {
        $returnObject->status = false;
        $returnObject->message = 'Confirm Password is required.';
        return Response::json($returnObject);
    }

    if(empty($input['user_id']) || $input['user_id'] == null) {
        $returnObject->status = false;
        $returnObject->message = 'User ID is required.';
        return Response::json($returnObject);
    }

    $checker = DB::table('user')
      ->select('UserID', 'Name as name', 'member_activated')
      ->where('UserID', $input['user_id'])->first();

      if(!$checker) {
        $returnObject->status = false;
        $returnObject->message = 'User not found!';
        return Response::json($returnObject);
      }

      if($checker->member_activated) {
        $returnObject->status = false;
        $returnObject->message = 'User was active, please sign in!';
        return Response::json($returnObject);
      }

      if($input['password'] !== $input['password_confirm']) {
        $returnObject->status = false;
        $returnObject->message = 'Password Mismatched.';
        return Response::json($returnObject);
      }

      $newPassword = [
        'Password' => StringHelper::encode($input['password_confirm']),
        'member_activated' => 1,
        'account_update_status' => 1,
        'account_already_update'  => 1
      ];

      DB::table('user')->where('UserID', $checker->UserID)->update($newPassword);
      $token = StringHelper::createLoginToken($checker->UserID, $input['client_id']);
      if(!$token->status) {
        return Response::json($token);
      }
      $returnObject->status = true;
      $returnObject->token = $token->data['access_token'];
      $returnObject->message = 'Your Password has been created, Account was active!';
      return Response::json($returnObject);
  }

  public function getCompanyMemberLists( ) {
    $returnObject = new stdClass();
    $getRequestHeader = StringHelper::requestHeader();

    if(!empty($getRequestHeader['X-ACCESS-KEY']) || $getRequestHeader['x-access-key']){
      $getRequestHeader['X-ACCESS-KEY'] = !empty($getRequestHeader['X-ACCESS-KEY']) ? $getRequestHeader['X-ACCESS-KEY'] : $getRequestHeader['x-access-key'];
      $customer = CustomerHelper::getCustomerIdFromToken($getRequestHeader['X-ACCESS-KEY']);
      if($customer['status'] == false) {
        $returnObject->status = FALSE;
        $returnObject->message = $customer['message'];
        return Response::json($returnObject);
      }

      // get member lists
      $members = DB::table('customer_link_customer_buy')
                  ->join('corporate', 'customer_link_customer_buy.corporate_id', '=', 'corporate.corporate_id')
                  ->join('corporate_members', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
                  ->join('user', 'user.UserID', '=', 'corporate_members.user_id')
                  ->where('customer_link_customer_buy.customer_buy_start_id', $customer['customer_id'])
                  ->where('corporate_members.removed_status', 0)
                  ->select("user.UserID as member_id", "user.Name as fullname", "user.NRIC as nric", "user.Email as email_address", "user.PhoneNo as phone_number", "user.PhoneCode as phone_code")
                  ->get();
      $returnObject->status = TRUE;
      $returnObject->message = 'Success';
      $returnObject->data = $members;
      return Response::json($returnObject);
    } else {
      $returnObject->status = FALSE;
      $returnObject->message = 'X-ACCESS-KEY is required';
    }
    return Response::json($returnObject);
  }

  public function updateReadyOnBoarding( ) {
    $AccessToken = new Api_V1_AccessTokenController();
    $returnObject = new stdClass();
    $authSession = new OauthSessions();
    $getRequestHeader = StringHelper::requestHeader();

    if(!empty($getRequestHeader['Authorization'])){
      $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
      if($getAccessToken){
        $findUserID = $authSession->findUserID($getAccessToken->session_id);

        if($findUserID){
          $user_type = PlanHelper::getUserAccountType($findUserID);

          // if($user_type == "employee") {
            // check and update login status
            $user = DB::table('user')->where('UserID', $findUserID)->first();
            if($user) {
              // update
              DB::table('user')->where('UserID', $findUserID)->update(['Status' => 1]);
            }
          // }
        }
      }

      $returnObject->status = true;
      $returnObject->message = 'done';
      return Response::json($returnObject);
    } else {
      $returnObject->status = FALSE;
      $returnObject->message = StringHelper::errorMessage("Token");
      return Response::json($returnObject);
    }
  }

  function SGDMemberVerification($keys) {
    $returnObject = new stdClass();

    if(empty($keys['mobile']) || $keys['mobile'] == null) {
      $returnObject->status = false;
      $returnObject->message = 'Mobile Number is required.';
      return Response::json($returnObject);
    }

    $checker = DB::table('user')
    ->select('UserID as user_id', 'Name as name', 'member_activated', 'Zip_Code as postal_code', 'disabled_otp')
    ->where('PhoneNo', $keys['mobile'])->first();

    if(!$checker) {
        $returnObject->status = false;
        $returnObject->message = 'Unregistered Member.';
        return Response::json($returnObject);
    }

    if($checker->postal_code == null || $checker->postal_code === null) {
        $checker->postal_code = 0;
    }
    else {
        $checker->postal_code = 1;
    }

    $returnObject->status = true;
    $returnObject->message = 'Member is already registered';
    $returnObject->data = $checker;
    return Response::json($returnObject);
  }

  function MYRMemberVerification($keys) {
    $returnObject = new stdClass();
    $userModel = new User();

    // Login using Mobile number
    if (isset($keys['mobile'])) {
        // Check Member mobile number if already registered
        $userDetails = $userModel->checkMemberExistence(array(
                                      array( 'paramKey' => 'PhoneNo', 'paramKeyValue'=> $keys['mobile']),
                                      array( 'paramKey' => 'PhoneCode', 'paramKeyValue'=> $keys['PhoneCode'])
                                  ));

        if (!$userDetails || $keys['PhoneCode'] != '+60') {
          $returnObject->status = false;
          $returnObject->message = 'Unregistered Member.';

          return Response::json($returnObject);
        } else  {
          $returnObject->status = true;
          $returnObject->data = $userDetails;

          return Response::json($returnObject);
        }
    }

    // Login using NRIC
    if (!empty($keys['nric']) && isset($keys['nric'])) {
      // Check if NRIC already exist
      $userDetails = $userModel->checkMemberExistence(array(
                                    array( 'paramKey' => 'NRIC', 'paramKeyValue'=> $keys['nric'])
                                ));

      if (!$userDetails) {
          $returnObject->status = false;
          $returnObject->message = 'Unregistered Member.';

          return Response::json($returnObject);
      } else {
        $returnObject->status = true;
        $returnObject->data = $userDetails;

        return Response::json($returnObject);
      }
    }

    // Login using Passport
    if (!empty($keys['passport']) && isset($keys['passport'])) {
      // Check if passport already exist
      $userDetails = $userModel->checkMemberExistence(array(
                                    array( 'paramKey' => 'passport', 'paramKeyValue'=> $keys['passport'])
                                ));

      if (!$userDetails) {
          $returnObject->status = false;
          $returnObject->message = 'Unregistered Member.';

          return Response::json($returnObject);
      } else {
        $returnObject->status = true;
        $returnObject->data = $userDetails;

        return Response::json($returnObject);
      }
    }

    // If condition on this part it mean either mobile, passport or nric key are empty
    $returnObject->status = false;
    $returnObject->message = 'mobile, passport, or nric key params are all empty. Please provide data either one the three key params.';

    return Response::json($returnObject);

  }

  function validateMYROTPCode ($input) {
    $userDetails = new User();
    $returnObject = new stdClass();

    if(empty($input['otp_code']) || $input['otp_code'] == null) {
      $returnObject->status = false;
      $returnObject->message = 'OTP Code is required.';
      return Response::json($returnObject);
    }

    if(empty($input['user_id']) || $input['user_id'] == null) {
      $returnObject->status = false;
      $returnObject->message = 'User ID is required.';
      return Response::json($returnObject);
    }

    // Check if user id exist.
    $userRecord = $userDetails->checkMemberExistence(array(
                                  array( 'paramKey' => 'UserID', 'paramKeyValue'=> $input['user_id'])
                              ));

    if(!$userRecord) {
      $returnObject->status = false;
      $returnObject->message = 'User not exist.';
      return Response::json($returnObject);
    }

    // Verify OTP shit
    $OTPVerified = $userDetails->checkMemberExistence(array(
                                      array( 'paramKey' => 'OTPCode', 'paramKeyValue'=> $input['otp_code'])
                                  ));

    if ($OTPVerified) {

      if(strtoupper($input['country']) == "SGD") {
        // remove otp data record.
        $userDetails->updateMemberRecord($input['user_id'], array( 'OTPCode' => NULL ));
      } else {
        // update user mobile number and remove otp data record.
        $userDetails->updateMemberRecord($input['user_id'], array( 'PhoneNo'=> $input['mobile'], 'OTPCode' => NULL, 'PhoneCode' => $input['phoneCode']));
      }

      // Get new set of member records.
      $userNewRecord = $userDetails->checkMemberExistence(array(
                                        array( 'paramKey' => 'UserID', 'paramKeyValue'=> $input['user_id'])
                                    ));

      $returnObject->status = true;
      $returnObject->message = 'OTP verified.';
      $returnObject->data = $userNewRecord;
      return Response::json($returnObject);
    } else {
      $returnObject->status = false;
      $returnObject->message = 'Invalid OTP';
      return Response::json($returnObject);
    }
  }

  function validateSGDOTPCode ($input) {
    $returnObject = new stdClass();

    if(empty($input['otp_code']) || $input['otp_code'] == null) {
      $returnObject->status = false;
      $returnObject->message = 'OTP Code is required.';
      return Response::json($returnObject);
    }

    if(empty($input['user_id']) || $input['user_id'] == null) {
      $returnObject->status = false;
      $returnObject->message = 'User ID is required.';
      return Response::json($returnObject);
    }

    $checker = DB::table('user')
                ->select('UserID as user_id', 'Name as name', 'member_activated')
                ->where('UserID', $input['user_id'])->first();

    if(!$checker) {
      $returnObject->status = false;
      $returnObject->message = 'User not found!';
      return Response::json($returnObject);
    }

    $member_id = $checker->user_id;
    $result = DB::table('user')->where('UserID', $member_id)->where('OTPCode', $input['otp_code'])->first();
    if(!$result) {
        $returnObject->status = false;
        $returnObject->message = 'Invalid OTP.';
        return Response::json($returnObject);
    }

    DB::table('user')->where('UserID', $member_id)->update(['OTPCode' => NULL]);
    $returnObject->status = true;
    $returnObject->message = 'OTP Code is valid';
    $returnObject->data = $checker;
    return Response::json($returnObject);
  }

  function sendMYROtpMobile ($input) {
    $returnObject = new stdClass();
    $userDetails = new User();

      if(empty($input['mobile']) || $input['mobile'] == null) {
          $returnObject->status = false;
          $returnObject->message = 'Mobile Number is required.';
          return Response::json($returnObject);
      }

      if(empty($input['mobile_country_code']) || $input['mobile_country_code'] == null) {
          $returnObject->status = false;
          $returnObject->message = 'Mobile Country Code is required.';
          return Response::json($returnObject);
      }

      if(empty($input['userId']) || !isset($input['userId'])) {
        $returnObject->status = false;
        $returnObject->message = 'User ID is required.';
        return Response::json($returnObject);
      }

      // Check if mobile number already
      $mobileExist = $userDetails->checkMemberExistence(array(
          array( 'paramKey' => 'PhoneNo', 'paramKeyValue'=> $input['mobile']),
          // array( 'paramKey' => 'UserID', 'paramKeyValue'=> $input['userId'])
      ));

      if ($mobileExist) {
        $returnObject->status = false;
        $returnObject->message = 'Mobile number already been used.';
        return Response::json($returnObject);
      }

      $mobile_number = (int)$input['mobile'];
      $code = $input['mobile_country_code'];
      $phone = $code.$mobile_number;

      // Send OTP message
      $otp_code = StringHelper::OTPChallenge();
      $data = array();
      $data['phone'] = $phone;
      $data['message'] = 'Your Mednefits OTP is '.$otp_code;
      $data['sms_type'] = "LA";
      SmsHelper::sendSms($data);

      // Update User OTP record
      $userDetails->updateMemberRecord($input['userId'], array('OTPCode' => $otp_code));
      // Get User record.
      $userRecord = $userDetails->checkMemberExistence(array(
        array( 'paramKey' => 'UserID', 'paramKeyValue'=> $input['userId'])
    ));

      $returnObject->status = true;
      $returnObject->message = 'OTP SMS sent';
      $returnObject->data = $userRecord;
      return Response::json($returnObject);
  }

  function sendSGDOtpMobile ($input) {
    $returnObject = new stdClass();

      if(empty($input['mobile']) || $input['mobile'] == null) {
          $returnObject->status = false;
          $returnObject->message = 'Mobile Number is required.';
          return Response::json($returnObject);
      }

      if(empty($input['mobile_country_code']) || $input['mobile_country_code'] == null) {
          $returnObject->status = false;
          $returnObject->message = 'Mobile Country Code is required.';
          return Response::json($returnObject);
      }

      $checker = DB::table('user')
      ->select('UserID as user_id', 'Name as name', 'PhoneNo as mobile_number')
      ->where('PhoneNo', $input['mobile'])->first();

      if(!$checker) {
        $returnObject->status = false;
        $returnObject->message = 'User not found!';
        return Response::json($returnObject);
      }

      $member_id = $checker->user_id;
      $mobile_number = (int)$input['mobile'];
      $code = $input['mobile_country_code'];
      $phone = $code.$mobile_number;

      $otp_code = StringHelper::OTPChallenge();
      // StringHelper::TestSendOTPSMS($phone, $otp_code);
      $data = array();
      $data['phone'] = $phone;
      $data['message'] = 'Your Mednefits OTP is '.$otp_code;
      $data['sms_type'] = "LA";
      SmsHelper::sendSms($data);
      DB::table('user')->where('UserID', $member_id)->update(['OTPCode' => $otp_code]);
      $returnObject->status = true;
      $returnObject->message = 'OTP SMS sent';
      $returnObject->data = $checker;
      return Response::json($returnObject);
  }

  function registerMobileNumber() {
    $input = Input::all();
    $returnObject = new stdClass();
    $userDetails = new User();

    if (empty($input['mobile_number']) || empty($input['phoneCode'])) {
      $returnObject->status = false;
      $returnObject->message = 'Mobile number and Phone code are required.';
      return Response::json($returnObject);
    } else if (empty($input['userId'])) {
      $returnObject->status = false;
      $returnObject->message = 'user ID is required.';
      return Response::json($returnObject);
    } else {
      // Check if mobile number already
      $mobileExist = $userDetails->checkMemberExistence(array(
                                  array( 'paramKey' => 'PhoneNo', 'paramKeyValue'=> $input['mobile_number']),
                                  // array( 'paramKey' => 'UserID', 'paramKeyValue'=> $input['userId']),
                                  array( 'paramKey' => 'Active', 'paramKeyValue'=> 1),
                              ));

      if ($mobileExist) {
        $returnObject->status = false;
        $returnObject->message = 'Mobile number already been used.';
        return Response::json($returnObject);
      } else {
        // Update User OTP record
        $userDetails->updateMemberRecord($input['userId'], array('PhoneNo' => $input['mobile_number'], 'PhoneCode' => $input['phoneCode']));
        $returnObject->status = true;
        $returnObject->message = 'Mobile number successfully registered.';
        return Response::json($returnObject);
      }

    }
  }
}
