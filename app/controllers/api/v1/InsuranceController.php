<?php

use Illuminate\Support\Facades\Input;
//use Symfony\Component\Security\Core\User\User;
class Api_V1_InsuranceController extends \BaseController {

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
        public function getClinicInsuranceCompany($clinicid){
            $clinicInsurance = new ClinicInsurenceCompany();
            $panelInsurance = $clinicInsurance->FindClinicInsuranceCompnay($clinicid);
            
            if($panelInsurance){
                return $panelInsurance;
            }else{
                return FALSE;
            }
        }
        
        //No direct access to public 
        public function findAnnotation (){
            $insuranceCompany = new InsuranceCompany();
            $findAnnotation = $insuranceCompany->FindAnnotation("Other");
            if($findAnnotation){
                return $findAnnotation;
            }else{
                return FALSE;
            }
        }
        
        /* Use          :   Used to show all insurance company
         * Access       :   public
         * Parameter    :   null
         * Return       :   Insurance array
         */
        public function getAllInsuranceCompany(){
            $returnObject = InsuranceLibrary::AllInsuranceCompany();
            return Response::json($returnObject); 
        }

        /* use          :   Used to add user insurance policy
         * Access       :   Public 
         * 
         */
        public function AddUserInsurancePolicy(){
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            //$findUserID = 1;
            if($findUserID){
                $returnObject = InsuranceLibrary::AddInsurancePolicy($findUserID);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }
            return Response::json($returnObject); 
        }
        
        
        /* Use          :   Used to return users insurance policy list
         * Access       :   Public
         * 
         */
        public function AllUserInsurancePolicy(){
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            //$findUserID = 8;
            if($findUserID){
                $returnObject = InsuranceLibrary::FindAllInsurancePolicy($findUserID);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }
            return Response::json($returnObject); 
        }

        /* Use          :   Used to delete insurance policy 
         * Access       :   Public 
         * Parameter    :   Insurance Id
         */
        public function DeleteInsurancePolicy(){
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            //$findUserID = 1;
            if($findUserID){
                $returnObject = InsuranceLibrary::DeleteInsurancePolicy($findUserID);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }
            return Response::json($returnObject); 
        }    
        
        /* Use          :   Used to change primany insurance policy
         * Access       :   Public
         * Parameter    :   Insurance id
         */
        public function ChangePrimaryPolicy(){
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            //$findUserID = 1;
            if($findUserID){
                $returnObject = InsuranceLibrary::ChangePrimaryPolicy($findUserID);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }
            return Response::json($returnObject); 
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
