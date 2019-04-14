<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
 /*   if( ! Request::secure() ){
        return Redirect::secure( Request::path() );
    }*/
    // if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //     $statusCode = 204;

    //     $headers = [
    //         'Access-Control-Allow-Origin'      => 'http://medicloud.dev',
    //         'Allow'                            => 'GET, POST, OPTIONS',
    //         'Access-Control-Allow-Headers'     => 'Origin, Content-Type, Accept, Authorization, X-Requested-With',
    //         'Access-Control-Allow-Credentials' => 'true'
    //     ];

    //     return Response::make(null, $statusCode, $headers);
    // }
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, authorization, X-Request-With');
    header('Content-Type', 'application/json');
    // header('Accept', 'application/json');
    header('Access-Control-Allow-Credentials: true');
});


App::after(function($request, $response)
{
	// $response->headers->set('Access-Control-Allow-Origin', 'http://medicloud.dev');
 //    $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
 //    $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With');
 //    $response->headers->set('Access-Control-Allow-Credentials', 'true');
 //    return $response;
        // $response->header("Cache-Control", "no-cache,no-store, must-revalidate");
        // $response->header("Pragma", "no-cache");
        // $response->header("Content-Type", "application/json");
        // return $response;
    // header('Content-Type', 'application/json');
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

Route::filter('auth.admin', function()
{
	if(!Session::get('admin-session')){
        return Redirect::to('/admin/auth/login');
    }
});

Route::filter('auth.clinic', function()
{
	if(!Session::get('user-session')){
        return Redirect::to('/provider-portal-login');
    }
});

// Route::filter('auth.hr', function()
// {
//     $headers = [];
// 	if(!Session::get('hr-session')){
//         // return Redirect::to('company-benefits-dashboard-login');
//         return Response::json('Forbidden', 403, $headers);
//     } else {
//         $result = StringHelper::getHrSession();
//         if(!$result) {
//             // return Redirect::to('company-benefits-dashboard-login');
//             return Response::json('Forbidden', 403, $headers);
//         }
//     }
// });

Route::filter('auth.v1', function($request, $response)
{
    $returnObject = new stdClass();
    $returnObject->error = TRUE;
    $returnObject->message = 'You have an invalid token. Please login again';
    
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, authorization, X-Request-With');
    header('Content-Type', 'application/json');
    header('Accept', 'application/json');
    header('Access-Control-Allow-Credentials: true');

    // return StringHelper::requestHeader();
    if(!StringHelper::requestHeader()){
        return Response::json($returnObject, 200);
    } else {
        // return StringHelper::requestHeader();
        // check if there is a header authorization
        $token = StringHelper::getToken();
        // return $token;
        if(!$token) {
        	$returnObject->expired = true;
          return Response::json($returnObject, 200);
        }

        $findUserID = AuthLibrary::validToken();

        if(!$findUserID) {
          $returnObject->status = FALSE;
          $returnObject->expired = true;
          $returnObject->message = StringHelper::errorMessage("Token");
          return Response::json($returnObject, 200);
        }

        $user = DB::table('user')->where('UserID', $findUserID)->where('Active', 1)->first();

        if(!$user) {
          $returnObject->status = FALSE;
          $returnObject->expired = true;
          $returnObject->message = 'You account was deactivated. Please contact Mednefits Team.';
          return Response::json($returnObject, 200);
        }
    }
});

Route::filter('auth.v2', function($request, $response)
{
    $returnObject = new stdClass();
    $returnObject->error = TRUE;
    $returnObject->message = 'You have an invalid token. Please login again';
    
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, authorization, X-Request-With');
    header('Content-Type', 'application/json');
    header('Accept', 'application/json');
    header('Access-Control-Allow-Credentials: true');

    // return StringHelper::requestHeader();
    if(!StringHelper::requestHeader()){
        return Response::json($returnObject, 200);
    } else {
        // return StringHelper::requestHeader();
        // check if there is a header authorization
        $token = StringHelper::getToken();
        // return $token;
        if(!$token) {
            $returnObject->expired = true;
          return Response::json($returnObject, 200);
        }

        $findUserID = AuthLibrary::validToken();

        if(!$findUserID) {
          $returnObject->status = FALSE;
          $returnObject->expired = true;
          $returnObject->message = StringHelper::errorMessage("Token");
          return Response::json($returnObject, 200);
        }

        $user = DB::table('user')->where('UserID', $findUserID)->where('Active', 1)->first();

        if(!$user) {
          $returnObject->status = FALSE;
          $returnObject->expired = true;
          $returnObject->message = 'You account was deactivated. Please contact Mednefits Team.';
          return Response::json($returnObject, 200);
        }
    }
});

Route::filter('auth.headers', function($request, $response) {
    $response->header("Cache-Control","no-cache,no-store, must-revalidate");
    $response->header("Pragma", "no-cache");
    $response->header("Content-Type", "application/json");
    return $response;
});

Route::filter('auth.jwt_hr', function()
{
    $headers = [];
    if(!StringHelper::requestHeader()){
        $headers[]['error'] = true;
        // return Redirect::to('company-benefits-dashboard-login');
        return Response::json('You have an invalid token. Please login again', 403, $headers);
    } else {
        $headers[]['error'] = true;
        // check if there is a header authorization
        $token = StringHelper::getToken();
        // return $token;
        if(!$token) {
            return Response::json('You have an invalid token. Please login again', 403, $headers);
        }

        $result = StringHelper::getJwtHrSession();
        if(!$result) {
            // return Redirect::to('company-benefits-dashboard-login');
            return Response::json('You account was deactivated. Please contact Mednefits Team.', 401, $headers);
        }

        // decode and check the properites
        $secret = Config::get('config.secret_key');
        $value = JWT::decode($token, $secret);

        if($value->signed_in == false) {
            if(time() > $value->expire_in) {
                return Response::json('Ooops! Your login session has expired. Please login again.', 403, $headers);
            }
        }
    }
});


Route::filter('auth.employee', function()
{
    $headers = [];
    if(!Session::get('employee-session')){
        // return Redirect::to('company-benefits-dashboard-login');
        return Response::json('Forbidden', 403, $headers);
    } else {
        $result = StringHelper::getEmployeeSession();
        if(!$result) {
            // return Redirect::to('company-benefits-dashboard-login');
            return Response::json('Forbidden', 403, $headers);
        }
    }
});
/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
