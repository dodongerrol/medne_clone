<?php

use Illuminate\Support\Facades\Input;
//use Symfony\Component\Security\Core\User\User;
class Api_V1_ClinicController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		echo "index";
	}
    public function Search(){   
        $returnObject = new stdClass();
        /*$findUserID = AuthLibrary::validToken();
        if($findUserID){
            $returnObject = ClinicLibrary::ProcessSearch($findUserID);
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("Token");
        }*/
        $findUserID = AuthLibrary::validToken();
         // $findUserID = AuthLibrary::validToken();
        //$returnObject = ClinicLibrary::ProcessSearch($findUserID);
        $returnObject = Clinic_Library_v1::ProcessSearch($findUserID);
        return Response::json($returnObject);
    }

        
        public function ClinicDetails($clinicid){   
            $returnObject = new stdClass();
            /*$findUserID = AuthLibrary::validToken();
            if($findUserID){
                $returnObject = ClinicLibrary::ProcessClinicDetails();
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }*/
            //$returnObject = ClinicLibrary::ProcessClinicDetails();
            // $findUserID = AuthLibrary::validToken();
            // if($findUserID){
            //     $findUserID = $findUserID;
            // }else{
                // $findUserID = 1;
            // }
            $findUserID = AuthLibrary::validToken();
            $returnObject = Clinic_Library_v1::ClinicDetails($clinicid,$findUserID);
            return Response::json($returnObject);
        }
        
                
        /* Use          : Used to find nearby clinic for users
         * 
         * 
         */
        public function Nearby(){   
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            
            // if($findUserID){
            //     $findUserID = $findUserID;
            // }else{
            //     $findUserID = false;
            // }
            // $findUserID = 3;
            $returnObject = Clinic_Library_v1::ProcessNearby($findUserID);
            //$returnObject = ClinicLibrary::ProcessNearby($findUserID);
            return Response::json($returnObject);
        }

        public function NewNearby(){   
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            
            // if($findUserID){
            //     $findUserID = $findUserID;
            // }else{
            //     $findUserID = false;
            // }
            // return $findUserID;
            $returnObject = Clinic_Library_v1::ProcessNewNearby($findUserID);
            //$returnObject = ClinicLibrary::ProcessNearby($findUserID);
            return Response::json($returnObject);
        }
        
        
        
        
        /* Use      :   Used to view booking history
         * Access   :   By Mobile
         * 
         */
        public function AppointmentHistory(){  
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            // $findUserID =2299;
            if(!empty($findUserID)){
                //$returnObject = ClinicLibrary::AppointmentHistory($findUserID); 
                $returnObject = Clinic_Library_v1::AppointmentHistory($findUserID); 
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }  
            return Response::json($returnObject);
        }
        
        /* Use      :   Used to find booking details 
         * Access   :   Public
         * 
         */
        public function AppointmentDetails($appointmentid){  
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            if(!empty($findUserID)){
                //$returnObject = ClinicLibrary::AppointmentDetails($findUserID); 
                $returnObject = Clinic_Library_v1::AppointmentDetails($findUserID,$appointmentid); 
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }  
            return Response::json($returnObject);
        }
        
        /* Use          :   Used to delete appointment 
         * Access       :   Public 
         * 
         */
        public function AppointmentDelete(){  
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            //$findUserID =34;
            if(!empty($findUserID)){
                $returnObject = ClinicLibrary::AppointmentDelete();     
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }  
            return Response::json($returnObject);
        }
        
        /* Use      :   Used to find panel clinics nearby
         * Access   :   Public
         * 
         */
        public function PanelClinicNearby(){   
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            //$findUserID = 1;
            /*if($findUserID){
                $returnObject = ClinicLibrary::ProcessPanelClinicNearby($findUserID);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }*/
            $returnObject = ClinicLibrary::ProcessPanelClinicNearby();
            return Response::json($returnObject);
        }
        
        
        public function UserAppointmentValidation(){   
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            if($findUserID){
                $returnObject = ClinicLibrary::UserAppointmentValidation($findUserID);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }
            return Response::json($returnObject);
        }
        
        
        public function ProcedureDetails($procedureid){   
            $returnObject = new stdClass();
            /*$findUserID = AuthLibrary::validToken();
            if($findUserID){
                $returnObject = ClinicLibrary::ProcessClinicDetails();
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }*/
            //$returnObject = ClinicLibrary::ProcessClinicDetails();
            $returnObject = Clinic_Library_v1::ProcedureDetails($procedureid);
            return Response::json($returnObject);
        }
        
        /* Use          :   Used to get doctors procedure list
         * Parameter    :   Doctor id
         */
        public function ClinicDoctorProcedures($doctorid){   
            $returnObject = new stdClass();
            /*$findUserID = AuthLibrary::validToken();
            if($findUserID){
                $returnObject = ClinicLibrary::ProcessClinicDetails();
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }*/
            //$returnObject = ClinicLibrary::ProcessClinicDetails();
            $returnObject = Clinic_Library_v1::ClinicDoctorProcedures($doctorid);
            return Response::json($returnObject);
        }
      
        
        
        
        
        
        ///            Testing Area               //

        
        public function see(){
            $getRequestHeader = StringHelper::requestHeader();
            $array = array();
            $array['name'] = "Rizvi";
            $array['Address'] = "61 Kotta road Borrella";
            $array['Auth'] = $getRequestHeader['Authorization'];
            
            return Response::json($array);
            //echo 'Hi';
            //echo "<pre>";
            //print_r($getRequestHeader);
            //echo '</pre>';
            //echo '<hr>';
            //echo $getRequestHeader['User-Agent'];
            //echo '<hr>';
            //foreach ($getRequestHeader as $name => $value) {
                //echo "Hello: $name: $value\n";
            //}
             
             
        }
        public function post(){
            $name = Input::get('Name');
            $message = Input::get('Message');    
            $getRequestHeader = StringHelper::requestHeader();
            
            $array = array();
            $array['name'] = $name;
            $array['message'] = $message;
            $array['auth'] = $getRequestHeader['Authorization'];
            return Response::json($array);
        }
        







        //     //////////////////////////////////////////nhr////////////////////////////////////////////////////////////////

// 
        public function getClnicType()
        {   
            $returnObject = new stdClass();
           
            $returnObject = Clinic_Library_v1::getClnicType();

            return Response::json($returnObject);
        }

        public function NewClnicType()
        {   
            $returnObject = new stdClass();
           
            $returnObject = Clinic_Library_v1::NewClnicType();

            return Response::json($returnObject);
        }

        public function getClnicTypeSub()
        {   
            $returnObject = new stdClass();
           
            $returnObject = Clinic_Library_v1::getClnicTypeSub();

            return Response::json($returnObject);
        }
        

        public function getClinicByType($typeid)
        {
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            if($findUserID){
                $returnObject = Clinic_Library_v1::getClinicByType($typeid,$findUserID);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }
            return Response::json($returnObject);
        }


        public function favourite()
        {   
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            if($findUserID){
                $returnObject = Clinic_Library_v1::favourite($findUserID);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }
            return Response::json($returnObject);
        }



        public function mainSearch()
        {
            $returnObject = new stdClass();
            $returnObject = Clinic_Library_v1::mainSearch();
            return Response::json($returnObject);
        }

        public function subSearch()
        {
            $returnObject = new stdClass();
            $returnObject = Clinic_Library_v1::subSearch();
            return Response::json($returnObject);
        }


        public function getFavouriteClinics()
        {
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            // $findUserID = 32;
            if($findUserID){
                $data = Clinic_Library_v1::getFavouriteClinics($findUserID);

                if ($data) {
                    $returnObject->status = TRUE;
                    $returnObject->data = $data;
                }else {
                    $returnObject->status = FALSE;
                    $returnObject->data = array();
                }

            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }
            return Response::json($returnObject);
        }



        public function getPromoMessage()
        {
            $returnObject = new stdClass();

            $returnObject->status = true;
            $returnObject->data['message'] = 'Get $10 off all procedures booked via Medicloud. Book you appointment now to enjoy the savings!';
            $returnObject->data['url'] = 'https://medicloud.sg/promo.html';
            return Response::json($returnObject);
        }

}
