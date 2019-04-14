<?php

use Illuminate\Support\Facades\Input;
//use Symfony\Component\Security\Core\User\User;
class Admin_AuthController extends \BaseController {
    // public function test(){
    //     $user = new Admin_User();
    //     $user->mtest();
    //     //echo 'this is ok';
    // }

    public function MainLogin(){
        $returnArray['title'] = "Medicloud Admin Login";
        $getSessionData = AdminHelper::AuthSession();
        if($getSessionData){
            return Redirect::to('admin/clinic/all-clinics');
        }else{
            $view = View::make('admin.auth.login', $returnArray);
            return $view;
        }
        
        
//        if($getSessionData != FALSE && count($getSessionData)> 0){
//            if($getSessionData->UserType == 2 && ($getSessionData->Ref_ID != null || $getSessionData->Ref_ID != "")){
//                return Redirect::to('app/doctor/dashboard');
//            }elseif($getSessionData->UserType == 3 && ($getSessionData->Ref_ID != null || $getSessionData->Ref_ID != "")){
//                return Redirect::to('app/clinic/booking');
//            }
//        }else{
//            $view = View::make('admin.auth.login', $returnArray);
//            return $view;
        //}
    }
    
    public function LoginProcess(){
        $admin = new Admin();
        $email = Input::get('email');
        $password = Input::get('password');
        
        if(!empty($email) && !empty($password)){
            $findAdmin = $admin->AdminLogin($email,$password);
            if($findAdmin){
                Session::put('admin-session', $findAdmin);
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }    
    }

    
    public static function FindAdmin($adminid){
        $admin = new Admin();
        $findAdmin = $admin->FindAdmin($adminid);
        if($findAdmin){
            return $findAdmin;
        }else{
            return FALSE;
        }
    }
    
    public function LogOutNow(){
        Session::forget('admin-session');
        return Redirect::to('admin/auth/login');
    }   


} 