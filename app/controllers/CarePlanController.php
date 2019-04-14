<?php
use Illuminate\Support\Facades\Input;
class  CarePlanController  extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	public function __construct(){

    }

     public function index( )
     {
     	$hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $now = new \DateTime();
        $data['date'] = $now;
     		return View::make('care_plan.index', $data);
     }
     


}
