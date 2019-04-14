<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class PromoCode extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'promo_code';

	
        public function FindPromoCode(){
            $promoCode = DB::table('promo_code')
                    ->where('Active', '=', 1)
                     ->first();
            
            return $promoCode; 
        }
        
        public function GetActivePromoCode($code){
            $promoCode = DB::table('promo_code')
                    ->where('Code', '=', $code)
                    ->where('Active', '=', 1)
                    ->first();
            
            return $promoCode; 
        }
        
       
}
