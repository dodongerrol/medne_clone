<?php

use Illuminate\Support\Facades\Input;
//use Symfony\Component\Security\Core\User\User;
class Api_V1_DoctorController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		echo "index";
	}
        

        /* Use          :   Used to provide doctor details
         * Access       :   Public
         * Input        :   Doctor id   
         */
        public function DoctorDetails(){
            $returnObject = new stdClass();
            /*$findUserID = AuthLibrary::validToken();
            if($findUserID){
                $returnObject = DoctorLibrary::DoctorDetails();
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }*/
            $returnObject = DoctorLibrary::DoctorDetails();
            return Response::json($returnObject);
        }
        /* Use          :   Used to provide doctor details
         * Access       :   Public
         * Input        :   Doctor id   
         */
        public function FullDoctorDetails(){
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken(); 
            /*$findUserID = AuthLibrary::validToken();
            if($findUserID){
                $returnObject = DoctorLibrary::DoctorDetails();
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }*/
            //$returnObject = Doctor_Library_v1::FullDoctorDetails($clinicid,$doctorid,$procedureid,$findUserID);
            $returnObject = Doctor_Library_v1::FullDoctorDetails($findUserID);
            return Response::json($returnObject);
        }
        public function AccessMoreSlots(){
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken(); 
            /*$findUserID = AuthLibrary::validToken();
            if($findUserID){
                $returnObject = DoctorLibrary::DoctorDetails();
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }*/
            //$returnObject = Doctor_Library_v1::FullDoctorDetails($clinicid,$doctorid,$procedureid,$findUserID);
            $returnObject = Doctor_Library_v1::AccessMoreSlots($findUserID);
            return Response::json($returnObject);
        }
        
        //Public access is allowed
        /*
         - Parameter Doctor id and date
         - Retrun json (doctorArray)
         */
        public function getDoctorAvailableSlots(){
            $getDate = Input::get('date');
            $getDoctorID = Input::get('doctorid');
            $AccessToken = new Api_V1_AccessTokenController();
            $returnObject = array();
            $doctorArray = array();
            
            $getRequestHeader = StringHelper::requestHeader();
            //$getRequestHeader = "rUw9hmlK3H0AvMr8SN12hNd0O4VuxCSaPJfo0AJG";
            if($getRequestHeader['Authorization']==""){
            //if($getRequestHeader == ""){    
                $returnObject['status'] = FALSE;
                $returnObject['message'] = StringHelper::errorMessage("Token");
            }else{
                $getAccessToken = $AccessToken->FindToken($getRequestHeader['Authorization']);
                //$getAccessToken = $AccessToken->FindToken($getRequestHeader);
                
                if($getAccessToken){
                    if($getDate !="" && $getDoctorID !=""){
                        $doctorArray['session'] = "Morning";
                        $doctorArray['session_id'] = 1;
                        for($i=0; $i<3; $i++){
                            $doctorSlot['slot_id'] = 1;
                            $doctorSlot['start_time'] = "9AM";   
                            $doctorSlot['end_time'] = "9.30AM";
                            $doctorSlot['is_availble'] = 1;   
                            $returnDoctorSlot[] = $doctorSlot;
                        }  
                        
                        $doctorArray['times'] = $returnDoctorSlot;      
                        $returnDoctorArray[] = $doctorArray;
                        $returnObject['status'] = TRUE;
                        $returnObject['data'] = $returnDoctorArray;
                    }else{
                        $returnObject['status'] = FALSE;
                        $returnObject['message'] = StringHelper::errorMessage("EmptyValues");
                    }
                }else{
                    $returnObject['status'] = FALSE;
                    $returnObject['message'] = StringHelper::errorMessage("Token");
                }
                
            }
            return Response::json($returnObject);
        }
        
        
        //No public access to this method
        public function findDoctorCount($clinicid){
            $doctorAvailability = new DoctorAvailability();
            $doctorCount = $doctorAvailability->doctorCount($clinicid);
            if($doctorCount){
                return $doctorCount;
            }else{
                return 0;
            }
        }
        
        
        //No public access to this method 
        /* Used to fine doctors to a clinic
         - Parameter : Clinic ID
         - Return json 
         */
        public function findDoctorsForClinic($clinicid){
            $doctorAvailability = new DoctorAvailability();
            if($clinicid !=""){
                $doctorArray = $doctorAvailability->findDoctorsForClinic($clinicid);
                if($doctorArray){
                    return $doctorArray;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
            
        }

        //No public access to this method 
        /* Used to fine doctors to a clinic
         - Parameter : Clinic ID
         - Return json 
         */
        public function findDoctor($doctorid){
            $doctor = new Doctor();
            if($doctorid !=""){
                $doctorDetail = $doctor->doctorDetails($doctorid);
                if($doctorDetail){
                    return $doctorDetail;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
            
        }

        
        public function FindClinicForDoctor($doctorid){
            $doctorAvailability = new DoctorAvailability();
            if($doctorid !=""){
                $findClinic = $doctorAvailability->FindClinicForDoctor($doctorid);
                if($findClinic){
                    return $findClinic;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }
        
        /* Use          :       Used to book queue number
         * Access       :       Public
         * Parameter    :       Input array
         */
        public function QueueBooking_Old(){
            $allUpdatedata = Input::all();
            $returnBooking = $this->Booking($allUpdatedata,0);  
            return Response::json($returnBooking);
        }
        
        /* Use          :       Used to book Time slot
         * Access       :       Public
         * Parameter    :       Input array
         */
        public function SlotsBooking_Old(){
            $allUpdatedata = Input::all();
            $returnBooking = $this->Booking($allUpdatedata,1);
            return Response::json($returnBooking);
        }
        
        /* Use          :       Used to get refresh data
         * Access       :       Public
         * Parameter    :       Input array
         */
        public function QueueRefresh(){
            $allUpdatedata = Input::all();
            $returnBooking = $this->RefreshMoreData($allUpdatedata,0);
            return Response::json($returnBooking);
        }
        
        /* Use          :       Used to get refresh data
         * Access       :       Public
         * Parameter    :       Input array
         */
        public function SlotRefresh(){
            $allUpdatedata = Input::all();
            $returnBooking = $this->RefreshMoreData($allUpdatedata,1);
            return Response::json($returnBooking);
        }
        
        public function QueueBooking(){
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            if($findUserID){             
                $returnObject = DoctorLibrary::QueueBooking($findUserID);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }             
            return Response::json($returnObject);
        }
        public function SlotsBooking(){
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            if($findUserID){             
                $returnObject = DoctorLibrary::SlotsBooking($findUserID);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }             
            return Response::json($returnObject);
        }
        
        
        public function ConfirmQueueBooking(){
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken();
            if($findUserID){             
                $returnObject = DoctorLibrary::ConfirmQueueBooking($findUserID);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }             
            return Response::json($returnObject);
        }
        public function ConfirmSlotBooking(){
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken(); 
            if($findUserID){             
                //$returnObject = DoctorLibrary::ConfirmSlotBooking($findUserID);
                $returnObject = Doctor_Library_v1::ConfirmSlotBooking($findUserID);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }             
            return Response::json($returnObject);
        }
        public function BookingDelete(){
            $returnObject = new stdClass();
            $findUserID = AuthLibrary::validToken(); 
            if($findUserID){             
                $returnObject = Doctor_Library_v1::BookingDelete($findUserID);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }             
            return Response::json($returnObject);
        }
        
        
        private function Booking_Old($allUpdatedata,$mainbooktype){
            $userappointment = new UserAppoinment();
            //$authcontroller = new Api_V1_AuthController();
            $slotdetail = new DoctorSlotDetails();
            $slotmanage = new DoctorSlotsManage();
            $doctorSlot = new DoctorSlots();
            $findUserID = AuthLibrary::validToken();
            $returnObject = new stdClass();
            if(count($allUpdatedata) > 0){
                $dataArray = array();
                //$findUserID = $authcontroller->returnValidToken();
                
                $formatDate = date("d-m-Y", strtotime($allUpdatedata['date']));
                //$findUserID =1;
                if(!empty($findUserID)){
                    $findDoctorSlot = $doctorSlot->findDoctorSlot($allUpdatedata['doctorslot_id']);
                    if($mainbooktype == 0){
                        //$doctorslot = $allUpdatedata['queue_id'];
                        $doctorslot = $allUpdatedata['doctorslot_id'];
                        $bookno = $allUpdatedata['queue_no'];
                        $slotdetailid = 0;
                        $findSlotManage = $slotmanage->DoctorSlotManageByDate($doctorslot,$formatDate);
                        if($findSlotManage){
                            if($findSlotManage->Status==1){
                                $returnObject->status = FALSE;
                                $returnObject->message = StringHelper::errorMessage("QueueBlock");
                                return $returnObject;
                            }
                        }
                        $findQueueAppoint = $userappointment->FindBookedQueue($mainbooktype,$doctorslot,$formatDate,$bookno);
                        if($findQueueAppoint){
                            $returnObject->status = FALSE;
                            $returnObject->message = StringHelper::errorMessage("NoQueue");
                            return $returnObject;
                        }
                        $QueueCount = $userappointment->CountQueueBooking($mainbooktype,$doctorslot,$formatDate);
                        if($QueueCount >= $findDoctorSlot->QueueNumber){
                            $returnObject->status = FALSE;
                            $returnObject->message = StringHelper::errorMessage("MoreQueue");
                            return $returnObject;
                        } 
                    }elseif($mainbooktype == 1){
                        $slotdetailid = $allUpdatedata['slot_id'];
                        $doctorslot = $allUpdatedata['doctorslot_id']; 
                        $bookno = 0;
                        $slottype = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                        //$findSlotDetails = $slotdetail->getBookingSlot($slotdetailid,$slottype,$formatDate);
                        $findSlotDetails = $slotdetail->FindBookingSlot($slotdetailid,$slottype,$formatDate);
                        if(!$findSlotDetails){
                            $returnObject->status = FALSE;
                            $returnObject->message = StringHelper::errorMessage("NoSlot");
                            return $returnObject;
                        }
                    }
                    $dataArray['userid'] = $findUserID;
                    $dataArray['booktype'] = $mainbooktype;
                    $dataArray['doctorslotid'] = $doctorslot;
                    $dataArray['slotdetailid'] = $slotdetailid;
                    $dataArray['mediatype'] = 0;
                    $dataArray['booknumber'] = $bookno;
                    $dataArray['bookdate'] = $formatDate;
                    
                    $newAppointment = $userappointment->insertUserAppointment($dataArray);
                    if($newAppointment){
                        $userProfile = AuthLibrary::FindUserProfile($findUserID);
                        if($userProfile){
                            $emailDdata['emailName']= $userProfile->Name;
                            $emailDdata['emailPage']= 'email-templates.booking';
                            $emailDdata['emailTo']= $userProfile->Email;
                            $emailDdata['emailSubject'] = 'Thank you for making your clinic reservation';
                            EmailHelper::sendEmail($emailDdata);
                        }
                        if($mainbooktype == 1){
                            $updateArray['slotdetailid'] = $allUpdatedata['slot_id'];
                            $updateArray['Available'] = 2;
                            $updateArray['updated_at'] = time();
                            $slotdetail->updateSlotDetails($updateArray);
                        }
                        $returnObject->status = TRUE;
                        $returnObject->data['record_id'] = $newAppointment;
                    }else{
                        $returnObject->status = FALSE;
                        $returnObject->message = StringHelper::errorMessage("Tryagain");
                    }
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("Token");
                }
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("EmptyValues");
            }
            //return Response::json($returnObject);
            return $returnObject;
        }

        
        
        
        
        /* Use          :   Used to process data for Queue and Slots
         * Access       :   Private
         * Parameter    :   Input array and booking type (Queue / slot)
         * 
         */
        private function RefreshMoreData($allUpdatedata,$mainbooktype){
            $appointment = new UserAppoinment();
            $authcontroller = new Api_V1_AuthController();
            $slotdetail = new DoctorSlotDetails();
            $doctorslot = new DoctorSlots();
            $returnObject = new stdClass();
            if(count($allUpdatedata) > 0){
                $dataArray = array();
                $findUserID = $authcontroller->returnValidToken();
                //$findUserID =1;
                if(!empty($findUserID)){
                    $norecord = 0;
                    if($mainbooktype == 0){
                        $currentDate = date("d-m-Y", strtotime($allUpdatedata['date']));
                        $queueid = $allUpdatedata['queue_id'];
                        $findDoctorSlot = $doctorslot->findDoctorSlot($queueid);
                        if(count($findDoctorSlot) > 0){          
                            if($findDoctorSlot->ClinicSession == 1 || $findDoctorSlot->ClinicSession == 3){
                                $getQueueAppointment = $appointment->getQueueDetails(0,$findDoctorSlot->DoctorSlotID,$currentDate);
                                if(count($getQueueAppointment) > 0){
                                    $totalQueue = count($getQueueAppointment);
                                        $order = 1;
                                    foreach($getQueueAppointment as $appoint){
                                        if($order == 1){ $nextAvailableNumber = $appoint->BookNumber + 1; }
                                        if($appoint->Status == 1){ $nowSaving = $appoint->BookNumber; }
                                        $order ++;
                                    }
                                    if(empty($nowSaving)){ $nowSaving =0;}
                                    if(empty($totalQueue)){ $totalQueue =0;}
                                    if(empty($nextAvailableNumber)){$nextAvailableNumber = 1;}
                                    $bookArray['queue_id'] = $findDoctorSlot->DoctorSlotID;
                                    $bookArray['no_of_patients'] = $totalQueue;
                                    $bookArray['now_serving'] = $nowSaving;
                                    $bookArray['fee'] = $findDoctorSlot->ConsultationCharge;
                                    $bookArray['date'] = $currentDate;
                                    $bookArray['next_availble_queue_no'] = $nextAvailableNumber;
                                    $bookArray['estimated_time'] = $findDoctorSlot->QueueTime;   
                                    $returnObject->status = TRUE;
                                    $returnObject->data['queue'] = $bookArray;
                                }else{ $norecord = 1; }                             
                            }else{  $norecord = 1; }
                        }else{ $norecord = 1; } 
                    }elseif($mainbooktype == 1){
                        $currentDate = date("d-m-Y", strtotime($allUpdatedata['date']));
                        $doctorslotid = $allUpdatedata['doctorslot_id'];
                        $findDoctorSlot = $doctorslot->findDoctorSlot($doctorslotid);
                        if(count($findDoctorSlot) > 0){  
                            if($findDoctorSlot->ClinicSession == 2 || $findDoctorSlot->ClinicSession == 3){
                                $findSlotDetails = $slotdetail->getBookingSlot($findDoctorSlot->DoctorSlotID,$currentDate);
                                if(count($findSlotDetails)){
                                if($findDoctorSlot->TimeSlot == '30min'){$timegap = 30;}elseif($findDoctorSlot->TimeSlot == '60min'){$timegap = 60;}
                                    $slotOrder = 0;
                                    $slotCount['total'] = 0;
                                    foreach($findSlotDetails as $sdetails){
                                        $findSlotBooking = $appointment->getSlotBooking(1,$sdetails->SlotDetailID);
                                        if(!empty($findSlotBooking)){
                                            $slotCount['total'] = count($findSlotBooking);
                                        }
                                        if($slotOrder == 0){
                                            if($sdetails->Available == 1){
                                                $nextavailableslot = $sdetails->SlotDetailID;
                                                $slotOrder ++;
                                            }
                                        }
                                        $slotArray['slot_id'] = $sdetails->SlotDetailID;
                                        $slotArray['start_time'] = $sdetails->Time;
                                        $newtime = substr($sdetails->Time, -2);
                                        $slotArray['end_time'] = date("H:i",strtotime('+'.$timegap.' minutes',strtotime($sdetails->Time))).$newtime;
                                        $slotArray['status'] = $sdetails->Available;
                                        $sdArray[] = $slotArray;
                                    }
                                    $nowSaving = 0;
                                    if($slotCount['total'][0]==0){ $totalPatientCount=0;}else{$totalPatientCount =$slotCount['total'][0];}
                                    if(empty($nextavailableslot)){ $nextavailableslot = 0;}
                                    $bookArray['doctorslot_id'] = $findDoctorSlot->DoctorSlotID;
                                    $bookArray['no_of_patients'] = $totalPatientCount;
                                    $bookArray['now_serving'] = $nowSaving;
                                    $bookArray['fee'] = $findDoctorSlot->ConsultationCharge;
                                    $bookArray['date'] = $currentDate;
                                    $bookArray['next_availble_slot_id'] = $nextavailableslot;
                                    $bookArray['times'] = $sdArray;
                                    
                                    $returnObject->status = TRUE;
                                    $returnObject->data['timeslot'] = $bookArray;
                                }else{ $norecord = 1; }   
                            }else{ $norecord = 1; }
                        }else{ $norecord = 1; }   
                    }else{ $norecord = 1; }           
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("Token");
                }
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("EmptyValues");
            }
            if($norecord == 1){
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("NoRecords");
            }
            return $returnObject;            
        }
        
        /* Use          :   Used to pass queue number with custom date
         * Access       :   Public 
         * 
         */
        
        public function DoctorSlotsForDate(){
            $returnObject = new stdClass();
            /*$findUserID = AuthLibrary::validToken();
            if($findUserID){
                $returnObject = DoctorLibrary::DoctorSlotsForDate(1);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }*/
            $returnObject = DoctorLibrary::DoctorSlotsForDate(1);
            return Response::json($returnObject);
        }
        /* use          :   Used to provide slots with custom date
         * Access       :   Public 
         * 
         */
        public function DoctorQueueForDate(){
            $returnObject = new stdClass();
            /*$findUserID = AuthLibrary::validToken();
            if($findUserID){
                $returnObject = DoctorLibrary::DoctorSlotsForDate(0);
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }*/
            $returnObject = DoctorLibrary::DoctorSlotsForDate(0);
            return Response::json($returnObject);
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        public function DoctorDetails1(){
            $getDoctorID = Input::get('value');
            $returnObjects = $this->getDoctorDetails($getDoctorID);
            return Response::json($returnObjects);
        }
        
        /* Use          :   Used to process doctor details based on doctor id
         * Access       :   Private
         * Parameter    :   Doctor id
         */
        private function getDoctorDetails1($getDoctorID){
            $doctorArray = array();
            $doctorslot = new DoctorSlots();
            $slotdetails = new DoctorSlotDetails();
            $appointment = new UserAppoinment();
            $insurance = new Api_V1_InsuranceController();
            $slotmanage = new DoctorSlotsManage();
            
            $authcontroller = new Api_V1_AuthController();
            $returnObject = new stdClass();
            $findUserID = $authcontroller->returnValidToken();
            $findUserID =1;
            if(!empty($findUserID)){
                if(empty($getDoctorID) || $getDoctorID == "" || $getDoctorID == null){
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("EmptyValues");
                }else{
                    $findDoctor = $this->findDoctor($getDoctorID);
                    if($findDoctor){
                        $doctorArray['doctor']['doctor_id']= $findDoctor->DoctorID;
                        $doctorArray['doctor']['name']= $findDoctor->Name;
                        $doctorArray['doctor']['qualifications']= $findDoctor->Qualifications;
                        $doctorArray['doctor']['specialty']= $findDoctor->Specialty;
                        $doctorArray['doctor']['image_url']= URL::to('/assets/'.$findDoctor->image);
                        $doctorArray['doctor']['availability']= $findDoctor->Availability;
                        $doctorArray['doctor']['can_book']= $findDoctor->Active;
                        $doctorArray['doctor']['available_dates']= "";
                        //should have clinic for doctor
                        $findClinic = $this->FindClinicForDoctor($findDoctor->DoctorID);
                        if($findClinic){
                        $doctorArray['clinic']['clinic_id']= $findClinic->ClinicID;
                        $doctorArray['clinic']['name']= $findClinic->Name;
                        $doctorArray['clinic']['address']= $findClinic->Address;
                        $doctorArray['clinic']['image_url']= URL::to('/assets/'.$findClinic->image);

                        $doctorArray['clinic']['lattitude']= $findClinic->Lat;
                        $doctorArray['clinic']['longitude']= $findClinic->Lng;
                        $doctorArray['clinic']['telephone']= $findClinic->Phone;
                        $doctorArray['clinic']['open']= $findClinic->Opening;
                        //start
                        $panelInsurance = $insurance->getClinicInsuranceCompany($findClinic->ClinicID);
                        if($panelInsurance){
                            $panelCount[] = count($panelInsurance);
                            //Panel clinic based on user insurance company
                            $doctorArray['clinic']['panel_insurance']['insurance_id']= $panelInsurance->CompanyID;
                            $doctorArray['clinic']['panel_insurance']['name']= $panelInsurance->Name;
                            $doctorArray['clinic']['panel_insurance']['image_url']= URL::to('/assets/'.$panelInsurance->Image);

                            //Panel annotation or nonpanel annotation
                            $doctorArray['clinic']['annotation_url']= URL::to('/assets/'.$panelInsurance->Annotation);  

                        }else{
                            $findAnnotation = $insurance->findAnnotation();
                            if($findAnnotation){
                                $doctorArray['clinic']['annotation_url']= URL::to('/assets/'.$findAnnotation->Annotation);
                            }else{
                                $doctorArray['clinic']['annotation_url']= null;
                            }
                            $doctorArray['clinic']['panel_insurance'] =null;
                        }
                        //For booking information
                        $findDoctorSlot = $doctorslot->getDoctorSlot($findDoctor->DoctorID,$findClinic->ClinicID);
                        if(!empty($findDoctorSlot)){
                            $findSlotManage = $slotmanage->DoctorSlotManageByDate($findDoctorSlot->DoctorSlotID,date("d-m-Y"));
                            $slotCount['total'] = 0;
                            if($findDoctorSlot->ClinicSession == 1 || $findDoctorSlot->ClinicSession == 3){
                                $getQueueAppointment = $appointment->getQueueDetails(0,$findDoctorSlot->DoctorSlotID,date("d-m-Y"));
                                if(count($getQueueAppointment) > 0){                      
                                    $totalQueue = count($getQueueAppointment);
                                        $order = 1;
                                    foreach($getQueueAppointment as $appoint){
                                        if($order == 1){
                                            $nextAvailableNumber = $appoint->BookNumber + 1;
                                        }
                                        if($appoint->Status == 1){
                                            $nowSaving = $appoint->BookNumber;
                                        }
                                        $order ++;
                                    }
                                }
                                $nextno = 1;
                            }else{ $nextno = 0;}
                            if($findDoctorSlot->ClinicSession == 2 || $findDoctorSlot->ClinicSession == 3){
                                $slotType = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                                //$findSlotDetails = $slotdetails->getBookingSlot($findDoctorSlot->DoctorSlotID,date("d-m-Y"));
                                $findSlotDetails = $slotdetails->getBookingSlot($findDoctorSlot->DoctorSlotID,$slotType, date("d-m-Y"));
                                if(count($findSlotDetails)>0){ $slotTime =0;
                                foreach($findSlotDetails as $sdetails){
                                    if($slotTime ==0) {if($sdetails->Available == 1){
                                        $slotAvailable = 1; $slotTime ++;
                                    } }
                                    $findSlotBookingCount = $appointment->getSlotBooking(1,$sdetails->SlotDetailID);
                                    if(!empty($findSlotBookingCount)){
                                        $slotCount['total'] = count($findSlotBookingCount);
                                    }
                                }}
                            }
                            if(empty($totalQueue)){ $totalQueue =0;}
                            $totalPatientCount = $totalQueue + $slotCount['total'];
                            if(empty($nowSaving)){ $nowSaving = 0; }

                            if(empty($slotAvailable)){$slotAvailable=0;}  
                            //if(empty($nextAvailableNumber)){ $nextno =1;}else{$nextno = $nextAvailableNumber;}
                            if(empty($nextAvailableNumber)){ $nextAvailableNumber =1;}
                            if($nextno ==0){$nextno="";}else{$nextno = $nextAvailableNumber;}
                            
                            if($findSlotManage && $findSlotManage->Status==1){    
                                $nextno="";                             
                            }elseif($nextno >$findDoctorSlot->QueueNumber){$nextno="";}
                            //echo $nextno;
                            if(!empty($nextno) && $slotAvailable ==1){ $typebooking =3;}
                            elseif($slotAvailable ==1){ $typebooking =2;}
                            elseif(!empty($nextno)){ $typebooking =1;}
                            else{$typebooking =0;}
                            $doctorArray['booking']['type'] = $typebooking;
                            
                            if($findDoctorSlot->ClinicSession == 1 || $findDoctorSlot->ClinicSession == 3){
                                if(empty($nextAvailableNumber)){ $nextAvailableNumber = 1; }
                                
                                $doctorArray['booking']['queue']['queue_id'] = $findDoctorSlot->DoctorSlotID;
                                $doctorArray['booking']['queue']['no_of_patients'] = $totalPatientCount;
                                $doctorArray['booking']['queue']['now_serving'] = $nowSaving;
                                $doctorArray['booking']['queue']['fee'] = $findDoctorSlot->ConsultationCharge;
                                $doctorArray['booking']['queue']['date'] = date('d-m-Y');
                                $doctorArray['booking']['queue']['next_availble_queue_no'] = $nextAvailableNumber;
                                $doctorArray['booking']['queue']['estimated_time'] = $findDoctorSlot->QueueTime; 
                                if($findSlotManage){if($findSlotManage->Status == 1){$doctorArray['booking']['queue'] = null;}}
                                if($nextAvailableNumber > $findDoctorSlot->QueueNumber){$doctorArray['booking']['queue'] = null;}
                            }else{
                                $doctorArray['booking']['queue'] = null;
                            }
                            if($findDoctorSlot->ClinicSession == 2 || $findDoctorSlot->ClinicSession == 3){
                                //$findSlotDetails = $slotdetails->getBookingSlot($findDoctorSlot->DoctorSlotID,date("d-m-Y"));
                                if(count($findSlotDetails) > 0){
                                    if($findDoctorSlot->TimeSlot == '30min'){$timegap = 30;}elseif($findDoctorSlot->TimeSlot == '60min'){$timegap = 60;}
                                    $slotOrder = 0;
                                    foreach($findSlotDetails as $sdetails){
                                        //$findSlotBooking = $appointment->getSlotBooking(1,$sdetails->SlotDetailID);
                                        
                                        if($slotOrder == 0){
                                            if($sdetails->Available == 1 && ArrayHelper::ActiveTime($sdetails->Time,date('d-m-Y'))==1){
                                                $nextavailableslot = $sdetails->SlotDetailID;
                                                $nextStartTime = $sdetails->Time;
                                                $newnexttime = substr($sdetails->Time, -2); $exactnexttime = substr($sdetails->Time,0, -2);
                                                $nextEndTime = date("H.i",strtotime('+'.$timegap.' minutes',strtotime($exactnexttime))).$newnexttime;
                                                $nextStatus = $sdetails->Available;
                                                $slotOrder ++;
                                            }
                                        }
                                        if(empty($nextavailableslot)){ $nextavailableslot = 0;}
                                        $slotArray['slot_id'] = $sdetails->SlotDetailID;
                                        if($nextavailableslot ==$sdetails->SlotDetailID){$slotArray['next_availble_slot_id'] = $nextavailableslot;}
                                        else{$slotArray['next_availble_slot_id'] = 0;}
                                           
                                        $slotArray['start_time'] = $sdetails->Time;
                                        $newtime = substr($sdetails->Time, -2);
                                        $exactTime = substr($sdetails->Time,0, -2);
                                        $slotArray['end_time'] = date("H.i",strtotime('+'.$timegap.' minutes',strtotime($exactTime))).$newtime;
                                        if(ArrayHelper::ActiveTime($sdetails->Time,date('d-m-Y'))==1){
                                            $slotArray['status'] = $sdetails->Available;
                                        }else{
                                            $slotArray['status'] = 3;
                                        }
                                        
                                        $sdArray[] = $slotArray;
                                    }
                                    if(empty($nextStartTime)){ $nextStartTime =null;}
                                    if(empty($nextEndTime)){ $nextEndTime =null;}
                                    if(empty($nextStatus)){ $nextStatus =0;}
                                    $doctorArray['booking']['timeslot']['doctorslot_id'] = $findDoctorSlot->DoctorSlotID;
                                    $doctorArray['booking']['timeslot']['no_of_patients'] = $totalPatientCount;
                                    $doctorArray['booking']['timeslot']['now_serving'] = $nowSaving;
                                    $doctorArray['booking']['timeslot']['fee'] = $findDoctorSlot->ConsultationCharge;
                                    $doctorArray['booking']['timeslot']['date'] = date("d-m-Y");
                                    //$doctorArray['booking']['timeslot']['next_availble_slot_id'] = $nextavailableslot;
                                    $doctorArray['booking']['timeslot']['next_available_slot']['slot_id'] = $nextavailableslot;
                                    $doctorArray['booking']['timeslot']['next_available_slot']['start_time'] = $nextStartTime;
                                    $doctorArray['booking']['timeslot']['next_available_slot']['end_time'] = $nextEndTime;
                                    $doctorArray['booking']['timeslot']['next_available_slot']['status'] = $nextStatus;
                                    
                                    $doctorArray['booking']['timeslot']['times'] = $sdArray;
                                }else{
                                    $doctorArray['booking']['timeslot'] = null;
                                }
                            }else{
                                 $doctorArray['booking']['timeslot'] = null;
                            }   
                        }
                        }else{
                            $doctorArray['clinic'] =null;
                        } 
                        $returnDoctorArray = $doctorArray;
                        $returnObject->status = TRUE;
                        $returnObject->data = $returnDoctorArray;
                           
                    }else{
                        $returnObject->status = FALSE;
                        $returnObject->message = StringHelper::errorMessage("NoRecords");
                    }
                }
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Token");
            }
            return $returnObject;      
        }
        
        public function DoctorSlotsForDate1(){
            $allUpdatedata = Input::all();
            $returnBooking = $this->MoreDataForDate($allUpdatedata,1);
            return Response::json($returnBooking);
        }
        public function DoctorQueueForDate1(){
            $allUpdatedata = Input::all();
            $returnBooking = $this->MoreDataForDate($allUpdatedata,0);
            return Response::json($returnBooking);
        }
        
        /* Use          : Used to pass queue number and slots based on date 
         * Access       : Private
         * Parameter    : Doctor id and date
         */
        private function MoreDataForDate1($allUpdatedata,$mainbooktype){
            $appointment = new UserAppoinment();
            $authcontroller = new Api_V1_AuthController();
            $slotdetail = new DoctorSlotDetails();
            $doctorslot = new DoctorSlots();
            $slotmanage = new DoctorSlotsManage();
            $returnObject = new stdClass();
            $norecord = 0;
            if(count($allUpdatedata) > 0){
                //$dataArray = array();
                $findUserID = $authcontroller->returnValidToken();
                //$findDoctor = $this->findDoctor($allUpdatedata['doctorid']);
                $findUserID =1;
                if(!empty($findUserID)){
                    $findDoctorSlot = $doctorslot->getDoctorSlot($allUpdatedata['doctorid'],$allUpdatedata['clinicid']);
                    $currentDate = date("d-m-Y", strtotime($allUpdatedata['date']));
                    if($mainbooktype == 0){
                        $findSlotManage = $slotmanage->DoctorSlotManageByDate($findDoctorSlot->DoctorSlotID,$currentDate);
                        //$queueid = $allUpdatedata['queue_id'];
                        //$findDoctorSlot = $doctorslot->findDoctorSlot($queueid);
                        if(count($findDoctorSlot) > 0){          
                            if($findDoctorSlot->ClinicSession == 1 || $findDoctorSlot->ClinicSession == 3 && ArrayHelper::ActiveDate($currentDate)==1){
                                $getQueueAppointment = $appointment->getQueueDetails(0,$findDoctorSlot->DoctorSlotID,$currentDate);
                                //if(count($getQueueAppointment) > 0){
                                if(count($getQueueAppointment) > 0){
                                    $totalQueue = count($getQueueAppointment);
                                        $order = 1;
                                    foreach($getQueueAppointment as $appoint){
                                        if($order == 1){ $nextAvailableNumber = $appoint->BookNumber + 1; }
                                        if($appoint->Status == 1){ $nowSaving = $appoint->BookNumber; }
                                        $order ++;
                                    }
                                }
                                    if(empty($nowSaving)){ $nowSaving = 0;}
                                    if(empty($totalQueue)){ $totalQueue =0;}
                                    if(empty($nextAvailableNumber)){$nextAvailableNumber = 1;}
                                    $bookArray['queue_id'] = $findDoctorSlot->DoctorSlotID;
                                    $bookArray['no_of_patients'] = $totalQueue;
                                    $bookArray['now_serving'] = $nowSaving;
                                    $bookArray['fee'] = $findDoctorSlot->ConsultationCharge;
                                    $bookArray['date'] = $currentDate;
                                    $bookArray['next_availble_queue_no'] = $nextAvailableNumber;
                                    $bookArray['estimated_time'] = $findDoctorSlot->QueueTime; 
                                    if($findSlotManage && $findSlotManage->Status ==1){
                                        $returnObject->status = FALSE;
                                        $returnObject->message = StringHelper::errorMessage("NoRecords");
                                    }elseif($nextAvailableNumber > $findDoctorSlot->QueueNumber){
                                        $returnObject->status = FALSE;
                                        $returnObject->message = StringHelper::errorMessage("NoRecords");
                                    }else{
                                        $returnObject->status = TRUE;
                                        $returnObject->data['queue'] = $bookArray;
                                    }
                                    
                                //}else{ $norecord = 1; }                             
                            }else{  $norecord = 1; }
                        }else{ $norecord = 1; } 
                    }elseif($mainbooktype == 1){
                        //$findDoctorSlot = $doctorslot->getDoctorSlot($allUpdatedata['doctorid'],$allUpdatedata['clinicid']);
                        //$currentDate = date("d-m-Y", strtotime($allUpdatedata['date']));
                        //$doctorslotid = $allUpdatedata['doctorslot_id'];
                        //$findDoctorSlot = $doctorslot->findDoctorSlot($doctorslotid);
                        if(count($findDoctorSlot) > 0){  
                            if($findDoctorSlot->ClinicSession == 2 || $findDoctorSlot->ClinicSession == 3){
                                $slotType = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                                //$findSlotDetails = $slotdetail->getBookingSlot($findDoctorSlot->DoctorSlotID,$currentDate);
                                $findSlotDetails = $slotdetail->getBookingSlot($findDoctorSlot->DoctorSlotID,$slotType,$currentDate);
                                if(count($findSlotDetails)){
                                if($findDoctorSlot->TimeSlot == '30min'){$timegap = 30;}elseif($findDoctorSlot->TimeSlot == '60min'){$timegap = 60;}
                                    $slotOrder = 0;
                                    $slotCount['total'] = 0;
                                    foreach($findSlotDetails as $sdetails){
                                        $findSlotBooking = $appointment->getSlotBooking(1,$sdetails->SlotDetailID);
                                        if(!empty($findSlotBooking)){
                                            $slotCount['total'] = count($findSlotBooking);
                                        }
                                        if($slotOrder == 0){
                                            if($sdetails->Available == 1 && ArrayHelper::ActiveTime($sdetails->Time,$currentDate)==1){
                                                $nextavailableslot = $sdetails->SlotDetailID;
                                                $nextStartTime = $sdetails->Time;
                                                $newnexttime = substr($sdetails->Time, -2); $exactnexttime = substr($sdetails->Time,0, -2);
                                                $nextEndTime = date("H.i",strtotime('+'.$timegap.' minutes',strtotime($exactnexttime))).$newnexttime;
                                                $nextStatus = $sdetails->Available;
                                                $slotOrder ++;
                                            }
                                        }
                                        if(empty($nextavailableslot)){ $nextavailableslot = 0;}
                                        $slotArray['slot_id'] = $sdetails->SlotDetailID;
                                        if($nextavailableslot ==$sdetails->SlotDetailID){$slotArray['next_availble_slot_id'] = $nextavailableslot;}
                                        else{$slotArray['next_availble_slot_id'] = 0;}
                                        $slotArray['start_time'] = $sdetails->Time;
                                        $newtime = substr($sdetails->Time, -2); $exacttime = substr($sdetails->Time,0, -2);
                                        $slotArray['end_time'] = date("H:i",strtotime('+'.$timegap.' minutes',strtotime($exacttime))).$newtime;
                                        if(ArrayHelper::ActiveTime($sdetails->Time,$currentDate)==1){
                                            $slotArray['status'] = $sdetails->Available;
                                        }else{
                                            $slotArray['status'] = 3;
                                        }
                                        
                                        $sdArray[] = $slotArray;
                                    }
                                    $nowSaving = 0;
                                    if($slotCount['total'][0]==0){ $totalPatientCount=0;}else{$totalPatientCount =$slotCount['total'][0];}
                                    if(empty($nextStartTime)){ $nextStartTime =null;}
                                    if(empty($nextEndTime)){ $nextEndTime =null;}
                                    if(empty($nextStatus)){ $nextStatus =0;}
                                    $bookArray['doctorslot_id'] = $findDoctorSlot->DoctorSlotID;
                                    $bookArray['no_of_patients'] = $totalPatientCount;
                                    $bookArray['now_serving'] = $nowSaving;
                                    $bookArray['fee'] = $findDoctorSlot->ConsultationCharge;
                                    $bookArray['date'] = $currentDate;
                                    //$bookArray['next_availble_slot_id'] = $nextavailableslot;
                                    $bookArray['next_available_slot']['slot_id'] = $nextavailableslot;
                                    $bookArray['next_available_slot']['start_time'] = $nextStartTime;
                                    $bookArray['next_available_slot']['end_time'] = $nextEndTime;
                                    $bookArray['next_available_slot']['status'] = $nextStatus;
                                    
                                    $bookArray['times'] = $sdArray;
                                    
                                    $returnObject->status = TRUE;
                                    $returnObject->data['timeslot'] = $bookArray;
                                }else{ $norecord = 1; }   
                            }else{ $norecord = 1; }
                        }else{ $norecord = 1; }   
                    }else{ $norecord = 1; }           
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("Token");
                }
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("EmptyValues");
            }
            if($norecord == 1){
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("NoRecords");
            }
            return $returnObject;            
        }
















        //for testing
        public function test(){
            $test = new Api_V1_ClinicController();
            $test->index();
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
