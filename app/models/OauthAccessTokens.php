<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class OauthAccessTokens extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'oauth_access_tokens';
    protected $guarded = array();
	
        
        public function FindToken ($token){
            $allData = DB::table('oauth_access_tokens')
                    ->where('id', '=', $token)
                    ->first();
            if($allData){
                return $allData;
            }else{
                return FALSE;
            }          
        }
        public function DeleteToken ($token){
            $deleteData = DB::table('oauth_access_tokens')
                    ->where('id', '=', $token)
                    ->delete();
            if($deleteData){
                return TRUE;
            }else{
                return FALSE;
            }          
        }
        
        public function FindUserID($token){
            $getUserId = DB::table('oauth_access_tokens')
                    ->join('oauth_sessions', 'oauth_access_tokens.session_id', '=', 'oauth_sessions.id')                  
                    ->join('user', 'oauth_sessions.owner_id', '=', 'user.UserID')
                    ->select('user.UserID')
                    ->where('oauth_access_tokens.id','=',$token)
                    ->where('user.Active', 1)
                    // ->where('user.UserType','=',1)
                    ->where('user.UserType',5)
                    ->first();
                if($getUserId){
                    return $getUserId->UserID;
                }else{
                    return FALSE;
                }
        }

        public function createToken($data)
        {
            return OauthAccessTokens::create($data);
        }
        
       
}











