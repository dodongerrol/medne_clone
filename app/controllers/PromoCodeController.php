<?php
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
class PromoCodeController extends BaseController {

	
	public function index( )
	{	
		$data['title'] = 'Promo Code';
		return View::make('admin.promo_code', $data);
	}

	public function getPromoCode( )
	{
		$promo = new NewPromoCode( );
		return $promo->getPromoCode( );
	}

	public function createPromoCode( )
	{
		$promo = new NewPromoCode( );
		$input = Input::all();

		// return $input;
		if($input['id']) {
			$data = array(
				'code'			=> $input['code'],
				'amount'		=> $input['amount'],
				'active'		=> $input['active'],
	        	'updated_at'	=> Carbon::now()
			);

			return $promo->updatePromo($data, $input['id']);
		} else {
			$data = array(
				'code'			=> $input['code'],
				'amount'		=> $input['amount'],
				'active'		=> $input['active'],
				'created_at'	=> Carbon::now(),
	        	'updated_at'	=> Carbon::now()
			);
			
			return $promo->createPromoCode($data);
		}
	}

	public function updatePromo( )
	{
		$promo = new NewPromoCode( );
		$input = Input::all();

		// return $input;
		$data = array(
			'code'			=> $input['code'],
			'amount'		=> $input['amount'],
			'active'		=> $input['active'], 
        	'updated_at'	=> Carbon::now()
		);

		return $promo->updatePromo($data, $input['id']);
	}

	// for user match promo

	public function matchPromo( )
	{	
		$promo = new NewPromoCode( );
		$input = Input::all();

		return $promo->matchPromoCode($input);
	}

	// remove promo code
	public function removePromoCode($id)
	{	
		$promo = new NewPromoCode( );
		return $promo->removePromoCode($id);
	}
}
