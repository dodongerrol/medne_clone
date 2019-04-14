<?php

class Auth_Library{

    public static function ForgotPassword(){
        $user = new User();
        $email = Input::get('email');
        if(!empty($email)){
            $findUser = $user->checkClinicEmail($email);
            if($findUser){
                //Generate reset Link
                // $password = StringHelper::get_random_password(8);
                $updateArray['userid']= $findUser->UserID;
                // $updateArray['Password']= md5($password);
                $resetlink = StringHelper::getEncryptValue();
                $updateArray['ResetLink'] = $resetlink;
                // $updateArray['Recon'] = 0;
                $updateArray['updated_at'] = time();
                $updatedUser = $user->updateUser($updateArray);
                //send email to reset password
                if($updatedUser){
                    // $findNewUser = $user->getUserDetails($findUser->UserID);
                        // $emailDdata['resetLink']= $resetlink;
                        $emailDdata['emailName']= $findUser->Name;
                        $emailDdata['name']= $findUser->Name;
                        $emailDdata['context'] = "Forgot your health provider password?";
                        // $emailDdata['emailPage']= 'email-templates.reset-password';
                        $emailDdata['emailPage'] = 'email-templates.latest-templates.global-reset-password-template';
                        $emailDdata['emailTo']= $findUser->communication_email;
                        $emailDdata['emailSubject'] = 'Password Reset';
                        $emailDdata['login_email'] = $findUser->Email;
                        $emailDdata['communication_email'] = $findUser->communication_email;
                        // $emailDdata['password'] = $password;
                        $emailDdata['activeLink'] = URL::to('app/auth/password-reset?token='.$resetlink);
                        EmailHelper::sendEmail($emailDdata);
                        $emailDdata['emailTo'] = $findUser->Email;
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
        }
    }


    public static function FindUserDetails($userid){
        $user = new User();
        $findUser = $user->getUserDetails($userid);
        if($findUser){
            return $findUser;
        }else{
            return FALSE;
        }
    }



    public static function Showme(){
        $user = new User();
        $findUser = $user->getUserDetails(1);
        print_r($findUser);
    }

    public static function FindUserEmail($email){
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

    public static function FindUserID($id){
        $user = new User();
        if(!empty($id)){
            $finduserid = $user->FindUserID($id);
            if($finduserid){
                return $finduserid;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

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

    /* Use      :   Check email exist (Clinic, Doctor or User)
     *
     */
    public static function CheckEmailExist($email){
        $user = new User();
        $findEmailUser = $user->checkEmailExist($email);
        if($findEmailUser){
            return $findEmailUser;
        }else{
            return FALSE;
        }
    }

    public static function UpdateUsers($dataArray){
        $user = new User();
        if(!empty($dataArray)){
            $updateUser = $user->updateUserProfile($dataArray);
            if($updateUser){
                return $updateUser;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindProfileByRefID($refid){
        $user = new User();
        if(!empty($refid)){
            $updateUser = $user->UserProfileByRef($refid);
            if($updateUser){
                return $updateUser;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindNricUser(){
        $allInputs = Input::all();
        if(!empty($allInputs)){
            $nricUser = self::FindUserByNric($allInputs['nric']);
            if($nricUser){
                $existPhone = preg_replace('/\s+/','',$nricUser->PhoneNo);
                $plusmark = substr($existPhone, 0, 1);
                //$plusmark = substr($nricUser->PhoneNo, 0, 1);
                //if(is_int($plusmark)){
                if($plusmark == "+"){
                    $newphone = substr($existPhone, 3);
                }else{
                    $newphone = substr($existPhone, 2);
                }
                $dataArray['userid'] = $nricUser->UserID;
                $dataArray['name'] = $nricUser->Name;
                $dataArray['email'] = $nricUser->Email;
                $dataArray['phone'] = $newphone;
                $dataArray['code'] = $nricUser->PhoneCode;
                return $dataArray;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    public static function FindUserByNric($nric){
        $user = new User();
        if(!empty($nric)){
            $findUser = $user->FindUserByNric($nric);
            if($findUser){
                return $findUser;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindRealUser($nric,$email){
        $user = new User();
        if(!empty($nric) && !empty($email)){
            $findUser = $user->FindRealUser($nric,$email);
            if($findUser){
                return $findUser;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public static function ValidateLoginSession($userid){
        $findUser = self::FindUserDetails($userid);
        if($findUser){
            if($findUser->UserType==3){
                $findClinic = Clinic_Library::FindClinicDetails($findUser->Ref_ID);
                if($findClinic){
                    return $findUser;
                }else{
                    Session::forget('user-session');
                    return FALSE;
                }
            }elseif($findUser->UserType==2){
                $findDoctor = Doctor_Library::FindDoctorDetails($findUser->Ref_ID);
                if($findDoctor){
                    return $findUser;
                }else{
                    Session::forget('user-session');
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

}
