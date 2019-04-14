<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Admin extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'admin';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = array('password', 'remember_token');
        
        //User login by Mobile app
        public function AdminLogin ($email, $password){
            $myadmin = DB::table('admin')
                     ->select('AdminID')
                     ->where('Email', '=', $email)
                     ->where('Password', '=', AdminHelper::Encode($password))
                     ->where('Token', '=', AdminHelper::EncryptValue())
                     ->where('Active', '=', 1)
                     ->first();
            
            if($myadmin){
                return $myadmin->AdminID;
            }else{
                return false;
            }  
        }
        public function FindAdmin($adminid){           
            $findUser = DB::table('admin')
                    ->where('AdminID', '=', $adminid)
                    ->where('Active', '=', 1)
                    ->first();
            
            if($findUser){
                return $findUser;
            }else{
                return FALSE;
            }  
        }
        
        
        
        
        
        
      

}
