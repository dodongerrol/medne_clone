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

    public static function getAdminID( )
	{	
		// return Session::flush();
		$result = Session::get('admin');
		if($result) {
			try {
				return $result['admin_id'][0];
			} catch(Exception $e) {
				return $result['admin_id'][0];
			}	
		}

		return false;
	}
}