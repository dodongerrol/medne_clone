<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserPromoCode extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_promo_code';

        public function InsertUserPromoCode ($dataArray){
            $this->UserID = $dataArray['userid'];
            $this->ClinicID = $dataArray['clinicid'];
            $this->PromoCodeID = $dataArray['promocodeid'];
            $this->Code = $dataArray['promocode'];
            $this->created_at = time();
            $this->updated_at = 0;
            $this->Active = 1;

            if($this->save()){
                $insertedId = $this->id;
                return $insertedId;
            }else{
                return false;
            }      
        }
	
        
        public function FindUserPromoCode($userid,$promocodeid){
            $promoCode = DB::table('user_promo_code')
                    ->where('UserID', '=', $userid)
                    ->where('PromoCodeID', '=', $promocodeid)
                    ->where('Active', '=', 1)
                    ->first();
            
            return $promoCode; 
        }
        
        
        
       
}
