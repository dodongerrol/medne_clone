<?php

use Illuminate\Support\Facades\Input;
//use Symfony\Component\Security\Core\User\User;
class Api_V1_AccessTokenController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		echo "index";
	}

	//No direct access to public
        
        
        
        public function FindToken($token){
            $AccessToken = new OauthAccessTokens();
            $getAccessToken = $AccessToken->FindToken($token);
            
            if($getAccessToken){
                return $getAccessToken;
            }else{
                return FALSE;
            }
        }
        
        
        
        
        
        
        
        
     

                
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
        
        


}
