<?php

class AdminHelper{
    public static function Encode($password){
        return md5($password);
    }
        
    public static function EncryptValue(){
        $encryptValue = sha1("MEDICLOUD-SINGAPORE-SG65###");
        return $encryptValue;
    }
    public static function AuthSession(){
        $adminvalue = Session::get('admin-session');
        $returnAdmin = Admin_AuthController::FindAdmin($adminvalue);
        return $returnAdmin;
    }
}