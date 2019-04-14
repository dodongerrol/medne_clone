<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class OauthSessions extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'oauth_sessions';
    protected $guarded = array();
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
        
	//protected $hidden = array('password', 'remember_token');
        
       
        
        //
        //**** need to change when user have insurance policy *****//
        public function findUserID($sessionid){
                $getUserId = DB::table('oauth_sessions')
                    ->join('user', 'oauth_sessions.owner_id', '=', 'user.UserID')
                    ->select('user.UserID')
                    ->where('oauth_sessions.id','=',$sessionid)
                    // ->where('user.Active','=',1)
                    // ->where('user.UserType','=',1)    
                    ->where('user.UserType','=',5)    
                    ->first();
                if($getUserId){
                    return $getUserId->UserID;
                }else{
                    return FALSE;
                }
        }
        
        public function createSession($data) 
        {
            return OauthSessions::create($data);
        }

       

}
