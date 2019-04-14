<?php

use Illuminate\Support\Facades\Input;
//use Intervention\Image\ImageManagerStatic as Image;
//use Symfony\Component\Security\Core\User\User;
class App_DoctorController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		echo "index";
	}
        
        //return parameter and no direct access
        public function FindDoctor($doctorid){
            if($doctorid !=""){
               $doctor = new Doctor();
               $findDoctor = $doctor->doctorDetails($doctorid);
               if($findDoctor){
                   return $findDoctor;
               }else{
                   return false;
               }       
            }else{
                return FALSE;
            }   
        }
        
        /* Access by : Ajax
         * Parameter : Array
         * Use : Used to insert doctor slot and retrieve slot details
         * Return : view controller
         */
        public function ManageDoctorSlots(){
            $returnBooking = Doctor_Library::ManageDoctorSlot(); 
            return $returnBooking;    
        }
        
        
        public function ManageDoctorSlots1(){
            $alldata = Input::all();
            $doctorslot = new DoctorSlots();
            $dataArray = array(); 
            
            if(is_array($alldata) & count($alldata) >0 ){
                
                if($alldata['doctorslotid'] =="" || $alldata['doctorslotid'] ==0){ 
                    $dataArray['doctorid']= $alldata['doctorid'];
                    $dataArray['clinicid']= $alldata['clinicid'];
                    
                    $dataArray['queueno'] = $alldata['queueno'];
                    $dataArray['queuetime'] = $alldata['queuetime'];
                    $dataArray['clinicsession'] = $alldata['clinicsession'];
                    $dataArray['consultationcharge']= $alldata['consultcharge'];
                    $dataArray['timeslot']= $alldata['slot'];
                    $dataArray['starttime']= "";
                    $dataArray['endtime']= ""; 

                    $insertSlot = $doctorslot->insertDoctorSlot($dataArray);
                    if($insertSlot){
//                        $findSlotManage = $this->DoctorSlotManageBySlot($insertSlot);
//                        if($findSlotManage){
//                            //$insertDoctorSlotManage = $this->InsertDoctorSlotManage();
//                        }else{
//                            $dsMng['doctorslotid'] = $insertSlot;
//                            $dsMng['totalqueue'] = $alldata['queueno'];
//                            $dsMng['currenttotalqueue'] = 0;
//                            $dsMng['date'] = date("d-m-Y");
//                            $insertDoctorSlotManage = $this->InsertDoctorSlotManage($dsMng);
//                        }
                        //return $insertSlot;
                        //$findSlotDetails = $this->getAllSlotDetails($insertSlot);
                        
                        $findDoctorSlot = $this->findDoctorSlotByID($insertSlot);
                        $slottype = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                        $findSlotDetails = $this->FindSlotDetailsByType($findDoctorSlot->DoctorSlotID,$slottype);
                        
                        $returnObject['doctorslotexist'] = $insertSlot;
                        
                        $returnObject['clinicsession'] = $findDoctorSlot->ClinicSession;
                        if($findDoctorSlot->ClinicSession==1 || $findDoctorSlot->ClinicSession==3){       
                            $returnObject['queueno'] = $findDoctorSlot->QueueNumber;
                            $returnObject['queuetime'] = $findDoctorSlot->QueueTime;
                        }else{
                            $returnObject['queueno'] = 0;
                            $returnObject['queuetime'] =0;
                        }
                        if($findDoctorSlot->ClinicSession==2 || $findDoctorSlot->ClinicSession==3){       
                            $returnObject['timeslot'] = $findDoctorSlot->TimeSlot;
                            $returnObject['consultcharge'] = $findDoctorSlot->ConsultationCharge;
                        }else{
                            $returnObject['timeslot'] = 0;
                            $returnObject['consultcharge'] = 000;
                        }
                        
                        
                        
                        $returnObject['default'] = 0;
                        if(is_array($findSlotDetails) && count($findSlotDetails)>0){
                            $myslots = array();
                            foreach($findSlotDetails as $k){  
                                $myslots[] = (array) $k;
                            }
                            $returnObject['slotdetails'] = $myslots;
                        }else{
                            $returnObject['slotdetails'] = null;
                        }
                        $returnObject['today'] = date("d-m-Y");
                        //$returnObject['today'] = '08-12-2014';
//                        if(isset($alldata['fromdoctor'])){
//                            $view = View::make('ajax.doctor-settings-slot', $returnObject);
//                        }else{
//                            $view = View::make('ajax.clinic-settings-slot', $returnObject);
//                        }
                        $view = View::make('ajax.manage-slot-settings', $returnObject);
                        return $view;
                    }else{
                        return FALSE;
                    }
                }else{
                    $findDoctorSlot = $this->findDoctorSlotByID($alldata['doctorslotid']);
                    
                    //$dataArray['doctorid']= $alldata['doctorid'];
                    $dataArray['doctorslotid']= $alldata['doctorslotid'];
                    //$dataArray['clinicsession']= $alldata['clinicsession'];
                    $dataArray['consultationcharge']= $alldata['consultcharge'];
                    //$dataArray['timeslot']= $alldata['slot'];
                    //$dataArray['queueno'] = $alldata['queueno'];
                    $dataArray['queuetime'] = $alldata['queuetime'];
                    //$dataArray['starttime']= 0;
                    //$dataArray['endtime']= 0; 
                    $dataArray['active']= 1; 
                    $returnObject['today'] = date("d-m-Y");
                    //$returnObject['today'] = '08-12-2014';
                    $countQueueBooking = Doctor_Library::FindTotalQueueBooking($findDoctorSlot->DoctorSlotID,date("d-m-Y"));
                    if($countQueueBooking < $alldata['queueno']){
                        $dataArray['queueno'] = $alldata['queueno'];
                    }
                    $countSlotBooking = Doctor_Library::FindTotalSlotBooking($findDoctorSlot->DoctorSlotID,date("d-m-Y"));
                    
                    if(empty($countSlotBooking)){
                        $dataArray['timeslot']= $alldata['slot'];
                    }
                    
                    if($alldata['clinicsession'] ==3){
                        $dataArray['clinicsession'] = 3;     
                    }elseif($alldata['clinicsession'] ==2){
                        if($findDoctorSlot->ClinicSession==3 || $findDoctorSlot->ClinicSession==1){
                            if(empty($countQueueBooking)){
                                $dataArray['clinicsession'] = 2;
                            }    
                        }else{
                            $dataArray['clinicsession'] = 2;
                        }  
                    }elseif($alldata['clinicsession'] ==1){
                        if($findDoctorSlot->ClinicSession==3 || $findDoctorSlot->ClinicSession==2){
                            if(empty($countSlotBooking)){
                                $dataArray['clinicsession'] = 1;
                            }
                        }else{
                            $dataArray['clinicsession'] = 1;
                        }           
                    }elseif($alldata['clinicsession'] ==0){
                        $dataArray['clinicsession'] = 0;
                    }
                    
                    
                    
                    //$updateSlot = $doctorslot->updateDoctorSlot($dataArray);
                    $updateSlot = $this->DoctorSlotsUpdate($dataArray);
                    if($updateSlot){  
                        $findDoctorSlot = $this->findDoctorSlotByID($findDoctorSlot->DoctorSlotID);
                        //$findSlotDetails = $this->getAllSlotDetails($alldata['doctorslotid']);
                        $slottype = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                        $findSlotDetails = $this->FindSlotDetailsByType($findDoctorSlot->DoctorSlotID,$slottype);
                        //$findDoctorSlot = $this->findDoctorSlotByID($alldata['doctorslotid']);
                        $returnObject['doctorslotexist'] = $findDoctorSlot->DoctorSlotID;
                        if($findDoctorSlot->ClinicSession==1 || $findDoctorSlot->ClinicSession==3){       
                            $returnObject['queueno'] = $findDoctorSlot->QueueNumber;
                            $returnObject['queuetime'] = $findDoctorSlot->QueueTime;
                        }else{
                            $returnObject['queueno'] = 0;
                            $returnObject['queuetime'] =0;
                        }
                        if($findDoctorSlot->ClinicSession==2 || $findDoctorSlot->ClinicSession==3){       
                            $returnObject['timeslot'] = $findDoctorSlot->TimeSlot;
                            $returnObject['consultcharge'] = $findDoctorSlot->ConsultationCharge;
                        }else{
                            $returnObject['timeslot'] = 0;
                            $returnObject['consultcharge'] = 000;
                        }
                        
                        //$returnObject['timeslot'] = $alldata['slot'];
                        //$returnObject['clinicsession'] = $alldata['clinicsession'];
                        //$returnObject['queueno'] = $alldata['queueno'];
                        //$returnObject['queuetime'] = $alldata['queuetime'];
                        
                        $returnObject['default'] = 0;
                        if(is_array($findSlotDetails) && count($findSlotDetails)>0){
                            $myslots = array();
                            foreach($findSlotDetails as $k){  
                                $myslots[] = (array) $k;
                            }
                            $returnObject['slotdetails'] = $myslots;
                        }else{
                            $returnObject['slotdetails'] = null;
                        }
//                        if(isset($alldata['fromdoctor'])){
//                            $view = View::make('ajax.doctor-settings-slot', $returnObject);
//                        }else{
//                            $view = View::make('ajax.clinic-settings-slot', $returnObject);
//                        }
                        $view = View::make('ajax.manage-slot-settings', $returnObject);
                        return $view;
                    }else{
                        return 0;
                    }
                }
                
            }else{
                return 0;
            }            
        }
        
        public function DoctorSlotsUpdate($updateArray){
            $doctorslot = new DoctorSlots();
            
            $dataArray['doctorslotid'] = $updateArray['doctorslotid'];
            if(!empty($updateArray['clinicsession'])){
                $dataArray['ClinicSession'] = $updateArray['clinicsession'];
            }if(!empty($updateArray['consultationcharge'])){
                $dataArray['ConsultationCharge'] = $updateArray['consultationcharge'];
            }if(!empty($updateArray['timeslot'])){
                $dataArray['TimeSlot'] = $updateArray['timeslot'];
            }if(!empty($updateArray['queueno'])){
                $dataArray['QueueNumber'] = $updateArray['queueno'];
            }if(!empty($updateArray['queuetime'])){
                $dataArray['QueueTime'] = $updateArray['queuetime'];
            }
            $dataArray['updated_at'] = time();
            $dataArray['Active'] = $updateArray['active'];
                 
            $updateDoctorSlot = $doctorslot->updateDoctorSlot($dataArray);
            if($updateDoctorSlot){
                return true;
            }else{
                return FALSE;
            }
        }


        //no public access is allowed 
        //
        public function findDoctorSlots($doctorid,$clinicid){
            if($doctorid!="" && $clinicid!=""){
                $doctorslot = new DoctorSlots();
            
                $findDoctorslot = $doctorslot->getDoctorSlot($doctorid,$clinicid);
                return $findDoctorslot;
            }else{
                return FALSE;
            }           
        }
        public function findDoctorSlotByID($doctorslotid){
            $doctorslot = new DoctorSlots();
            $findDoctorslot = $doctorslot->findDoctorSlot($doctorslotid);
            if($findDoctorslot){
                return $findDoctorslot;
            }else{
                return FALSE;
            }
        }
        
        public function findDoctorSlotForDoctor($doctorid){
            $doctorslot = new DoctorSlots();
            if($doctorid !=""){
                $findDoctorSlot = $doctorslot->getDoctorSlotByDoctorId($doctorid);
                if($findDoctorSlot){
                    return $findDoctorSlot;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }
        
        public function UpdateDoctorSlots(){
            $alldata = Input::all();
            //echo $alldata['doctorid'];
            //exit();
            if(is_array($alldata) & count($alldata) >0 ){
                $doctorslot = new DoctorSlots();
                $dataArray = array();
                $dataArray['doctorid']= $alldata['doctorid'];
                $dataArray['clinicid']= $alldata['clinicid'];
                $dataArray['clinicsession']= "";
                $dataArray['consultationcharge']= $alldata['consultcharge'];
                $dataArray['timeslot']= $alldata['slot'];
                $dataArray['starttime']= "";
                $dataArray['endtime']= ""; 

                $insertSlot = $doctorslot->insertDoctorSlot($dataArray);
                if($insertSlot){
                    return $insertSlot;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }            
        }
        
        //Ajax call used to add new doctor
        public function AddNewDoctor(){
            $allInputData = Input::all();
            $doctor = new Doctor();
            $doctoravailability = new DoctorAvailability();
            
            if(is_array($allInputData) && count($allInputData) >0){ 
                $findDoctorEmail = $doctor->findDoctorByEmail($allInputData['email']);
                if(!$findDoctorEmail){
                    $newDoctor = $doctor->insertDoctor($allInputData);
                    if($newDoctor){
                        $data['doctorid'] = $newDoctor;
                        $data['clinicid'] = $allInputData['clinicid'];
                        $doctoravailability->insertDoctorAvailability($data);
                        //Insert into user table
                        //$findDoctor = $doctor->doctorDetails($newDoctor);
                        $allInputData['usertype']= 2;
                        $allInputData['ref_id']= $newDoctor;
                        $allInputData['activelink']= StringHelper::getEncryptValue();
                        $allInputData['status']= 0;
                        $newUserDoctor = App_AuthController::AddNewUser($allInputData);
                        if($newUserDoctor){
                            //Send email when add new doctor
                            $doctorDetails = App_AuthController::getUserDetails($newUserDoctor);
                            $emailDdata['emailName']= $doctorDetails->Name;
                            $emailDdata['emailPage']= 'email-templates.new-doctor';
                            $emailDdata['emailTo']= $doctorDetails->Email;
                            $emailDdata['emailSubject'] = 'Please complete your regitration';
                            $emailDdata['activeLink'] = "<a href='".URL::to('app/auth/register?activate='.$doctorDetails->ActiveLink)."'> Create Password Link</a>";
                            $emailDdata['createLink'] = URL::to('app/auth/register?activate='.$doctorDetails->ActiveLink);
                            EmailHelper::sendEmail($emailDdata);
                        }                      
                        return $newDoctor;
                    }else{
                        return 0;
                    }
                }else{     
                    return 0;
                }
            }else{
                return 0;
            }
        }
        
        
        public function UpdateDoctor(){
            $allUpdatedata = Input::all();
            $doctor = new Doctor();
            
            if(is_array($allUpdatedata) && count($allUpdatedata) >0 ){
                if(Input::get('name')) {
                    $dataArray['Name'] = Input::get('name');
                }if(Input::get('qualification')) {
                    $dataArray['Qualifications'] = Input::get('qualification');
                }if(Input::get('email')) {
                    $dataArray['Email'] = Input::get('email');
                }if(Input::get('speciality')) {
                    $dataArray['Specialty'] = Input::get('speciality');
                }if(Input::get('mobile')) {
                    $dataArray['Phone'] = Input::get('mobile');
                }if(Input::get('phone')) {
                    $dataArray['Emergency'] = Input::get('phone');
                }
                $dataArray['doctorid'] = Input::get('doctorid');
                $dataArray['updated_at'] = time();
                        
                $updateDoctor = $doctor->updateDoctor($dataArray);
                if($updateDoctor){
                    return $updateDoctor;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }         
        }
        
        //find doctors for clinic
        public function FindClinicDoctors($clinicid){
            if($clinicid !=""){
               $doctorAvailable = new DoctorAvailability();
                $findClinicDoctors = $doctorAvailable->findDoctorsForClinic($clinicid);
                if(is_array($findClinicDoctors) && count($findClinicDoctors) >0){
                    return $findClinicDoctors;
                } 
            }else{
                return FALSE;
            }
            
        }
        
        
        public function ManageSlotDetails(){
            $dataArray = Input::all();
            $returnArray = array(); 
            if(is_array($dataArray) && count($dataArray) > 0){
                $findDoctorSlot = $this->findDoctorSlotsById($dataArray['doctorslot']);
                $slottype = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                $dataArray['slottype'] = $slottype;
                //if($dataArray['insertedid'] != null || $dataArray['insertedid'] != ""){ 
                    $findSlotDetail = $this->getSlotDetails($dataArray['insertedid']);
                    if($findSlotDetail){                        
                        if($findSlotDetail->Active==1){
                            $appointmentExist = $this->AppointmentBySlotDetailsID($findSlotDetail->SlotDetailID);
                            if($appointmentExist == FALSE){
                                $dataArray['active'] = 0;
                                $updateSlotDetail = $this->updateSlotDetails($dataArray);
                            }else{
                                //$updateSlotDetail = FALSE;
                                $returnArray['result'] = "Booked";
                                return $returnArray;
                            }  
                        }else{
                            $dataArray['active'] = 1;
                            $updateSlotDetail = $this->updateSlotDetails($dataArray);
                        }
                        if($updateSlotDetail){
                            $returnArray['active'] = $dataArray['active'];
                            $returnArray['result'] = "Update";
                        }else{
                            $returnArray['result'] = null;
                        }       
                    //}else{
                    //    $returnArray['result'] = null;
                    //}
                    
                    
                }else{
                    $insertSlotDetail = $this->insertSlotDetails($dataArray);
                    if($insertSlotDetail){
                        $returnArray['id'] = $insertSlotDetail;
                        $returnArray['result'] = "Insert";
                    }else{
                        $returnArray['result'] = null;
                    }
                }
 
                return $returnArray; 
                
            }else{
                return FALSE;
            }
        }
        
        //No direct access
        public function insertSlotDetails($dataArray){

            $slotdetails = new DoctorSlotDetails();
            
            if(is_array($dataArray) && count($dataArray) > 0){
                $addnewSlots = $slotdetails->insertSlotDetails($dataArray);
                if($addnewSlots){
                    return $addnewSlots;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }
        
        //No direct access to public
        public function updateSlotDetails($dataArray){
            $slotdetails = new DoctorSlotDetails();
            
            if(is_array($dataArray) && count($dataArray) > 0){
                $updateArray['updated_at'] = time();
                $updateArray['slotdetailid'] = $dataArray['insertedid'];
                $updateArray['Active'] = $dataArray['active'];       
                if(!empty($dataArray['available'])){
                    $updateArray['Available'] = $dataArray['available'];
                }
                
                
                $updateSlotDetails = $slotdetails->updateSlotDetails($updateArray);
                if($updateSlotDetails){
                    return $updateSlotDetails;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }
        
        //No public direct access
        public function getSlotDetails($slotdetailid){
            $slotdetails = new DoctorSlotDetails();
            if($slotdetailid !=""){
                $findSlotDetails = $slotdetails->getSlotDetails($slotdetailid);
                if($findSlotDetails){
                    return $findSlotDetails;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
            
        }
        
        /* Use          :   It is used to get all slot details for a slot
         * Access       :   Private
         * Parameter    :   Doctor slot id
         */
        public function getAllSlotDetails($doctorslotid){
            $slotdetails = new DoctorSlotDetails();
            if($doctorslotid !=""){
                $findAllSlotDetails = $slotdetails->getAllSlotDetails($doctorslotid);
                if($findAllSlotDetails){
                    return $findAllSlotDetails;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }  
        }
        /*
         * 
         * 
         */
        public function FindSlotDetailsByType($doctorslotid,$slottype){
            $slotdetails = new DoctorSlotDetails();
            if(!empty($doctorslotid) && !empty($slottype)){
                //$findAllSlotDetails = $slotdetails->getAllSlotDetails($doctorslotid);
                $findAllSlotDetails = $slotdetails->DetailsByType($doctorslotid,$slottype);
                if($findAllSlotDetails){
                    return $findAllSlotDetails;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }  
        }
        
        /* Use          :   Used to return slot details in a date 
         * Access       :   No direct access is allowed
         * Parameter    :   Slot id and date
         */
        public function SlotDetailsForDate($doctorslotid,$slottype,$currentdate){
            $slotdetails = new DoctorSlotDetails();
            if($doctorslotid !="" && $currentdate !="" && $slottype!=""){
                $findAllSlotDetails = $slotdetails->SlotDetailsWithDate($doctorslotid,$slottype,$currentdate);
                if($findAllSlotDetails){
                    return $findAllSlotDetails;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }  
        }
        //No public direct access
        public function findDoctorSlotsById($doctorslotid){
            if(!empty($doctorslotid)){
                $doctorslot = new DoctorSlots();
                $findDoctorslot = $doctorslot->findDoctorSlot($doctorslotid);
                return $findDoctorslot;
            }else{
                return FALSE;
            }   
        }
        
        public function ManageSlotsForDate(){
            $dataArray = Input::all();
            if($dataArray['doctorslotid'] != null || $dataArray['doctorslotid'] != ""){               
                $findDoctorSlot = $this->findDoctorSlotsById($dataArray['doctorslotid']);
                if($findDoctorSlot){
                    $slottype = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                    $findSlotDetails = $this->FindSlotDetailsByType($findDoctorSlot->DoctorSlotID,$slottype);
                    //$findSlotDetails = $this->getAllSlotDetails($findDoctorSlot->DoctorSlotID);
                    $returnObject['doctorslotexist'] = $findDoctorSlot->DoctorSlotID;
                    $returnObject['timeslot'] = $findDoctorSlot->TimeSlot;
                    $returnObject['default'] = 0;
                    //$returnObject['today'] = date("d-m-Y");
                    
                    if($dataArray['moveme']==1){
                        $returnObject['today'] = date('d-m-Y', strtotime($dataArray['currentday'].' +7 day'));
                    }elseif($dataArray['moveme']==0){
                        $returnObject['today'] = date('d-m-Y', strtotime($dataArray['currentday'].' -7 day'));
                    }else{
                        $returnObject['today'] = $dataArray['currentday'];
                    }
                     
                    if(is_array($findSlotDetails) && count($findSlotDetails)>0){
                        $myslots = array();
                        foreach($findSlotDetails as $k){  
                            $myslots[] = (array) $k;
                        }
                        $returnObject['slotdetails'] = $myslots;
                       
                    }else{
                        $returnObject['slotdetails'] = null;
                    }
                    $view = View::make('ajax.manage-slot-settings', $returnObject);
                    //$view = View::make('ajax.clinic-settings-slot', $returnObject);
                    return $view;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }   
        }
        
        /* Use      :   Used to view doctor setting page
         * Access   :   Public access is allowed
         * Return   :   Array
         */
        public function DoctorSettings(){
            $getSessionData = StringHelper::getMainSession(2);
            if($getSessionData != FALSE){
                //$findDoctorArray = $this->manageDoctorSettings($getSessionData->Ref_ID);
                $findDoctorArray = Doctor_Library::ManageDoctorSettings($getSessionData->Ref_ID);
                //if(is_array($findDoctorArray) && count($findDoctorArray)>0){
                if($findDoctorArray){    
                   $view = View::make('doctor.doctor-settings',$findDoctorArray);
                   return $view; 
                }else{
                   return Redirect::to('app/doctor/dashboard');
                }
            }else{
                return Redirect::to('provider-portal-login');
            }
            
        }
        
        /* Use      :   It is used to process doctor setting information
         * Access   :   Private
         * Return   :   Array 
         * Not calling for the moment
         */  
        /*private function manageDoctorSettings($doctorid){
            $dataArray['title'] = "Medicloud Doctor Settings";
            $doctorAvailable = new DoctorAvailability();
            $findDoctor = $this->FindDoctor($doctorid);
            if($findDoctor){
                $findClinicForDoctor = $doctorAvailable->FindClinicForDoctor($findDoctor->DoctorID);
                //find doctor slots
                $findDoctorSlot = $this->findDoctorSlotForDoctor($findDoctor->DoctorID);
                if($findDoctorSlot){
                    $slottype = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                    $findSlotDetails = $this->FindSlotDetailsByType($findDoctorSlot->DoctorSlotID,$slottype);
                    //$findSlotDetails = $this->getAllSlotDetails($findDoctorSlot->DoctorSlotID); 
                    if(is_array($findSlotDetails) && count($findSlotDetails)>0){ 
                        $myslots = array();
                        foreach($findSlotDetails as $k){  
                            $myslots[] = (array) $k;
                        }   
                        $dataArray['slotdetails'] = $myslots;
                    }else{
                        $dataArray['slotdetails'] = null;
                    }
                    if($findDoctorSlot->ClinicSession==1 || $findDoctorSlot->ClinicSession==3){
                        $dataArray['queueno'] = $findDoctorSlot->QueueNumber;
                        $dataArray['queuetime'] = $findDoctorSlot->QueueTime;        
                    }else{
                        $dataArray['queueno'] = 0;
                        $dataArray['queuetime'] = null;
                    }
                    if($findDoctorSlot->ClinicSession==2 || $findDoctorSlot->ClinicSession==3){
                        $dataArray['timeslot'] = $findDoctorSlot->TimeSlot; 
                    }else{
                        $dataArray['timeslot'] = null;
                    }
                    if(!empty($findDoctorSlot->ConsultationCharge)){
                        $dataArray['consultcharge'] = $findDoctorSlot->ConsultationCharge;
                    }else{ $dataArray['consultcharge'] = 000;}
                    
                    $dataArray['doctorslotexist'] = $findDoctorSlot->DoctorSlotID;
                    
                    $dataArray['clinicsession'] = $findDoctorSlot->ClinicSession;
                }else{
                    $dataArray['doctorslotexist'] = null;
                    $dataArray['consultcharge'] = null;
                    $dataArray['timeslot'] = null;
                    $dataArray['clinicsession'] =0;
                    $dataArray['queueno'] = 0;
                    $dataArray['queuetime'] = null;
                }
                $dataArray['clinicid'] = $findClinicForDoctor->ClinicID;
                $dataArray['doctorid'] = $findDoctor->DoctorID;
                $dataArray['name'] = $findDoctor->Name;
                $dataArray['email'] = $findDoctor->Email;
                $dataArray['qualification'] = $findDoctor->Qualifications;
                $dataArray['specialty'] = $findDoctor->Specialty;
                //$dataArray['image'] = URL::to('/assets/'.$findDoctor->image);
                $dataArray['image'] = $findDoctor->image;
                $dataArray['phone'] = $findDoctor->Phone;
                $dataArray['emergency'] = $findDoctor->Emergency;
                $dataArray['default'] = 1;
                $dataArray['today'] = date("d-m-Y");
                //$dataArray['today'] = '08-12-2014';
                return $dataArray;
            }else{
                return FALSE;
            }
           
        }*/
        
        /* Use          :   Used to view doctor dashboard
         * Parameter    :   Session
         */
        public function doctorDashboard(){
            $getSessionData = StringHelper::getMainSession(2);
            if($getSessionData != FALSE){
                $returnArray['title'] = "Medicloud doctor dashboard";
                $view = View::make('doctor.dashboard', $returnArray);
                return $view;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }
        
        /* Use          :   Used to find queue booking
         * Access       :   No public access is allowed
         * Parameter    :   Slot id and book date
         */
        public function findAppointmentQueue($slotid,$bookdate){
            $appointment = new UserAppoinment();
            $findQueueBook = $appointment->findQueueBooking($slotid,$bookdate);
            return $findQueueBook;
            
        }
        public function cancelledAppointmentQueue($slotid,$bookdate){
            $appointment = new UserAppoinment();
            $findQueueBook = $appointment->findCancelledQueueBooking($slotid,$bookdate);
            return $findQueueBook;
            
        }
        
        /* Use          :   Used to find slot booking
         * Access       :   No direct access is allowed
         * Parameter    :   Slotdetail id
         */
        public function findAppointmentSlot($slotdetailid,$bookdate){
            $appointment = new UserAppoinment();
            $findSlotBook = $appointment->findSlotBooking($slotdetailid,$bookdate);
            return $findSlotBook;
            
        }
        
        /* Use          :       Used to book queue number
         * Access       :       Public by AJAX
         * Parameter    :       Input array
         */
        public function QueueSlotBooking(){
            //echo 'hi';
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData == FALSE){
                return 0;
            }else{
                $bookingdata = Input::all(); 
                if(!empty($bookingdata)){
                    $returnBooking = $this->MainBooking($bookingdata,$bookingdata['booktype']);  
                    return $returnBooking;
                }else{
                    return 0;
                }   
            }
        }
        
        /* Use          :   Used to process both booking Queue and Slots
         * Access       :   Private
         * Parameter    :   Input array and booking type (Queue / slot)
         * 
         */
        private function MainBooking($bookingdata,$mainbooktype){
            //print_r($bookingdata);
            $userappointment = new UserAppoinment();
            $authcontroller = new App_AuthController();
            $slotdetail = new DoctorSlotDetails();
            $doctorslot = $bookingdata['doctorslotid'];
            $findDoctorSlot = Doctor_Library::FindDoctorSlotClinic($doctorslot);
            if(count($bookingdata) > 0 && !empty($findDoctorSlot)){
                $formatDate = date("d-m-Y", strtotime($bookingdata['bookdate']));
                //$doctorslot = $bookingdata['doctorslotid'];
                if($mainbooktype == 0){            
                    $bookno = $bookingdata['queueno'];
                    $slotdetailid = 0;
                    $getslotTime = 0;
                }elseif($mainbooktype == 1){
                    $slotdetailid = $bookingdata['slotdetailid'];
                    $findSlotDetails = Doctor_Library::FindDoctorSlotDetail($slotdetailid);
                    if($findSlotDetails){
                        $getslotTime = $findSlotDetails->Time;
                    }else{
                        $getslotTime = 0;
                    }
                    //$doctorslot = 0; 
                    $bookno = 0;
                }
                $bookno = intval($bookno); 
                $findExistingUser = $authcontroller->findUserEmail($bookingdata['email']);
                if($findExistingUser){
                    $findUserID = $findExistingUser;
                }else{
                    $userArray['name'] = $bookingdata['name'];
                    $userArray['usertype'] = 1;
                    $userArray['email'] = $bookingdata['email'];
                    $userArray['mobile'] = $bookingdata['mobile'];
                    $userArray['nric'] = $bookingdata['nric'];
                    $userArray['ref_id'] = 0;
                    $userArray['activelink'] = StringHelper::getEncryptValue();
                    $userArray['status'] = 0;
                    
                    $findUserID = $authcontroller->AddNewUser($userArray);
                    if($findUserID){
                        $emailDdata['emailName']= $bookingdata['name'];
                        $emailDdata['emailPage']= 'email-templates.welcome';
                        $emailDdata['emailTo']= $bookingdata['email'];
                        $emailDdata['emailSubject'] = 'Thank you for registering with us';

                        $emailDdata['activeLink'] = "<a href='".URL::to('provider-portal-login')."'> Find out more </a>";
                        EmailHelper::sendEmail($emailDdata);
                    }
                }
                if($findUserID){
                    //$formatDate = date("d-m-Y", strtotime($bookingdata['bookdate']));
                    $dataArray['userid'] = $findUserID;
                    $dataArray['booktype'] = $mainbooktype;
                    $dataArray['doctorslotid'] = $doctorslot;
                    $dataArray['slotdetailid'] = $slotdetailid;
                    $dataArray['mediatype'] = 1;
                    $dataArray['booknumber'] = $bookno;
                    $dataArray['bookdate'] = $formatDate;
                    
                    $newAppointment = $userappointment->insertUserAppointment($dataArray);
                    if($newAppointment){
                        $emailDdata['bookingTime'] = $getslotTime;
                        $emailDdata['bookingNo'] = $bookno;
                        $emailDdata['bookingDate'] = $formatDate; 
                        $emailDdata['doctorName'] = $findDoctorSlot->DName.' , '.$findDoctorSlot->Specialty;
                        $emailDdata['clinicName'] = $findDoctorSlot->CName;
                        $emailDdata['clinicAddress'] = $findDoctorSlot->Address;
                        
                        $emailDdata['emailName']= $bookingdata['name'];
                        $emailDdata['emailPage']= 'email-templates.booking';
                        $emailDdata['emailTo']= $bookingdata['email'];
                        $emailDdata['emailSubject'] = 'Thank you for making your clinic reservation';
                        //$emailDdata['activeLink'] = "<a href='".URL::to('provider-portal-login')."'> Find out more </a>";
                        EmailHelper::sendEmail($emailDdata);
                        
                        if($mainbooktype == 1){
                            $updateArray['slotdetailid'] = $slotdetailid;
                            $updateArray['Available'] = 2;
                            $updateArray['updated_at'] = time();
                            $slotdetail->updateSlotDetails($updateArray);
                        }
                        return $newAppointment;
                    }else{
                        return 0;
                    }
                }else{
                    return 0;
                }     
            }else{
                return 0;
            }
            
        }
       
        
    /* Use      :   Used to get appointment details 
     * Access   :   No public access is allowed
     */
    public function getAppointmentDetails($appointmentid){
        $userappointment = new UserAppoinment();
        if(!empty($appointmentid)){
            $getAppointment = $userappointment->getAppointment($appointmentid);
            if($getAppointment){
                return $getAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }    
    
    public function UpdateAppointment($dataArray,$appointid){
        $userappointment = new UserAppoinment();
        $updated = $userappointment->updateUserAppointment($dataArray,$appointid);
        return $updated;
    }

    
    public function DeleteUserAppointment(){
        $inputData = Input::all();
        if(!empty($inputData)){
            $appointmentDetails = $this->getAppointmentDetails($inputData['bookingid']);
            if($appointmentDetails != FALSE){
                $appointid = $appointmentDetails->UserAppoinmentID;
                //Need to assign queue and slot separately
                if($inputData['booktype']==1){
                    $dataArray['Active'] = 0;
                }
                $dataArray['Status'] = 3;
                $dataArray['updated_at'] = time();
                $updated = $this->UpdateAppointment($dataArray,$appointid);
                if($updated){
                    if($inputData['booktype']==1){
                        $updateSlot['insertedid'] = $appointmentDetails->SlotDetailID;
                        $updateSlot['available'] = 1;
                        $updateSlot['active'] = 1;  
                        $this->updateSlotDetails($updateSlot);
                    }
                    //Send an meail 
                    $findDoctorClinic = Doctor_Library::FindDoctorSlotClinic($appointmentDetails->DoctorSlotID);
                    $findDoctorSlotDetails = Doctor_Library::FindDoctorSlotDetail($appointmentDetails->SlotDetailID);
                    $findUserDetails = Auth_Library::FindUserDetails($appointmentDetails->UserID);
                    if($findDoctorSlotDetails){ $booktime = $findDoctorSlotDetails->Time; }else{$booktime = 0;}
                    $emailDdata['bookingTime'] = $booktime;
                    $emailDdata['bookingNo'] = $appointmentDetails->BookNumber;
                    $emailDdata['bookingDate'] = $appointmentDetails->BookDate; 
                    $emailDdata['doctorName'] = $findDoctorClinic->DName.' , '.$findDoctorClinic->Specialty;
                    $emailDdata['clinicName'] = $findDoctorClinic->CName;
                    $emailDdata['clinicAddress'] = $findDoctorClinic->Address;
                    $emailDdata['clinicPhone'] = $findDoctorClinic->CPhone;
                    
                    $emailDdata['emailName']= $findUserDetails->Name;
                    $emailDdata['emailPage']= 'email-templates.booking-cancel';
                    $emailDdata['emailTo']= $findUserDetails->Email;
                    $emailDdata['emailSubject'] = 'Medicloud Booking cancelled';
                    //$emailDdata['activeLink'] = "<a href='".URL::to('provider-portal-login')."'> Find out more </a>";
                    EmailHelper::sendEmail($emailDdata);
                    
                    return 1;
                }else{
                    return 0;
                }    
            }else{
                return 0;
            }   
        }else{
            return 0;
        }
    }
    
    
    public function OpenDeleteAppointment(){
        $authcontroller = new App_AuthController();
        $inputData = Input::all();
        if(!empty($inputData)){
            $appointmentDetails = $this->getAppointmentDetails($inputData['bookingid']);
            $findDoctorSlot = $this->findDoctorSlotsById($appointmentDetails->DoctorSlotID);
            if($appointmentDetails != FALSE){
                $findUser = $authcontroller->getUserDetails($appointmentDetails->UserID);
                    $returnArray['appointmentid'] = $appointmentDetails->UserAppoinmentID;
                    $returnArray['bookno'] = $appointmentDetails->BookNumber;
                    $newDate = date('l jS F Y', strtotime($appointmentDetails->BookDate));
                    $returnArray['bookdate'] = $newDate;
                if(!empty($findUser)){
                    $returnArray['userid'] = $findUser->UserID;
                    $returnArray['name'] = $findUser->Name;
                    $returnArray['nric'] = $findUser->NRIC;
                    $returnArray['phone'] = $findUser->PhoneNo;
                    $returnArray['email'] = $findUser->Email;
                }
                if(!empty($findDoctorSlot)){
                    $returnArray['consultcharge'] = $findDoctorSlot->ConsultationCharge;
                }
                
                return $returnArray;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    
    /* Use          :   Used to Stope the queue
     * Access       :   Public AJAX
     * 
     */
    public function StoppedDoctorQueue(){
        $inputData = Input::all();
        //print_r($inputData);
        if(!empty($inputData)){
            $findSlotManage = $this->DoctorSlotManageByDate($inputData['doctorslotid'],$inputData['currentdate']);
            if($findSlotManage){
                $dataArray['slotmanageid'] =  $findSlotManage->DoctorSlotManageID;
                $dataArray['queuetotal'] =  $inputData['queuetotal'];
                $dataArray['currentqueuetotal'] = $inputData['currenttotal'];
                $dataArray['date'] =  $inputData['currentdate'];
                $dataArray['status'] =  1;
                $updated = $this->updateDoctorSlotManage($dataArray);
                return $updated;
            }else{
                $dataArray['doctorslotid'] = $inputData['doctorslotid'];
                $dataArray['totalqueue'] = $inputData['queuetotal'];
                $dataArray['currenttotalqueue'] = $inputData['currenttotal'];
                $dataArray['date'] = $inputData['currentdate'];
                
                $inserted = $this->InsertDoctorSlotManage($dataArray);
                return $inserted;
            }
        }else{
            return 0;
        }
    }
    /* Use          :   Used to start the queue
     * Access       :   Public Ajax
     * 
     */
    public function StartedDoctorQueue(){
        $inputData = Input::all();
        //print_r($inputData);
        if(!empty($inputData)){
            $findSlotManage = $this->DoctorSlotManageById($inputData['slotmanageid']);
            //print_r($findSlotManage);
            if($findSlotManage){
                $dataArray['slotmanageid'] = $findSlotManage->DoctorSlotManageID;
                $dataArray['status'] = 0;
                $updated = $this->updateDoctorSlotManage($dataArray);  
                return $updated;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    /* Use          :   Used to find manage slot by id 
     * Access       :   No public access is allowed
     * 
     */
    public function DoctorSlotManageById($slotmanageid){
        $doctorslotmanage = new DoctorSlotsManage();
        if(!empty($slotmanageid)){
            $findSlotManage = $doctorslotmanage->getSlotManageById($slotmanageid);
            if($findSlotManage){
                return $findSlotManage;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    /*  
     * Access       :   No public access is allowed
     * 
     */
    public function DoctorSlotManageBySlot($slotid){
        $doctorslotmanage = new DoctorSlotsManage();
        if(!empty($slotid)){
            $findSlotManage = $doctorslotmanage->getDoctorSlotManage($insertSlot);
            if($findSlotManage){
                return $findSlotManage;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public function DoctorSlotManageByDate($slotid,$currentdate){
        $doctorslotmanage = new DoctorSlotsManage();
        if(!empty($slotid)){
            $findSlotManage = $doctorslotmanage->DoctorSlotManageByDate($slotid,$currentdate);
            if($findSlotManage){
                return $findSlotManage;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
     
    /*
     * 
     * 
     */
    public function InsertDoctorSlotManage($dataArray){
        $doctorslotmanage = new DoctorSlotsManage();
        if(is_array($dataArray) && !empty($dataArray)){
            $insertID = $doctorslotmanage->insertDoctorSlotManage($dataArray);
            if(!empty($insertID)){
                return $insertID;
            }else{
                return 0;
            }
        }else{
            return 0;
        }  
    }
    
    public function updateDoctorSlotManage($dataArray){
        $doctorslotmanage = new DoctorSlotsManage();
        if(is_array($dataArray) && count($dataArray) > 0){

            if(!empty($dataArray['queuetotal'])){
                $updateArray['TotalQueue'] = $dataArray['queuetotal'];
            }if(!empty($dataArray['currentqueuetotal'])){
                $updateArray['CurrentTotalQueue'] = $dataArray['currentqueuetotal'];
            }if(!empty($dataArray['date'])){
                $updateArray['Date'] = $dataArray['date'];
            }if($dataArray['status']>=0){
                $updateArray['Status'] = $dataArray['status'];
            }
            $updateArray['updated_at'] = time();
            
            $updated = $doctorslotmanage->updateDoctorSlotManage($updateArray,$dataArray['slotmanageid']);
            if($updated){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    public function AppointmentBySlotDetailsID($slotdetailid){
            $appointment = new UserAppoinment();
            $findSlotBook = $appointment->getAppointmentBYSlotDetailID($slotdetailid);
            if($findSlotBook){
                return $findSlotBook;
            }else{
                return FALSE;
            }          
    }


    public function DoctorHome(){
        $getSessionData = StringHelper::getMainSession(2);
        if($getSessionData != FALSE){ 
            $returnHome = Doctor_Library::DoctorHome($getSessionData->Ref_ID);
            if($returnHome != FALSE){
                return $returnHome;
            }else{
                return Redirect::to('app/doctor/dashboard');
            }
        }else{
            return Redirect::to('provider-portal-login');
        }    
    }
    
    /* Use          :   Used to make appointment by doctor
     * Access       :   Public 
     * 
     */
    public function DoctorBooking(){
        $getSessionData = StringHelper::getMainSession(2);
        if($getSessionData == FALSE){
            return 0;
        }else{
            $bookingdata = Input::all(); 
            //print_r($bookingdata);
            
            if(!empty($bookingdata)){
                $returnBooking = Doctor_Library::DoctorBooking($bookingdata,$bookingdata['booktype']);  
                return $returnBooking;
            }else{
                return 0;
            }   
        }
    }
    
    /*
     * 
     */
    public function AjaxDoctorBooking(){
        $getSessionData = StringHelper::getMainSession(2);
        if($getSessionData != FALSE){
            $returnData = Doctor_Library::AjaxDoctorBooking();
            return $returnData;
        }else{
            return 0;
        }    
    }
    
    
    /* Use          :   Used to Inset diagnosis by doctor
     * Access       :   Ajax public
     * Parameter    :   
     */
    public function DoctorDiagnosis(){
        $getSessionData = StringHelper::getMainSession(2);
        if($getSessionData != FALSE){
            $returnData = Doctor_Library::DoctorDiagnosis();
            return $returnData;
        }else{
            return 0;
        }    
    }
    
    
    
    
    
    // This is for testing
    public function DoctorUpload(){
        echo $uri = public_path().'/assets/';
        $returnArray['title'] = "Medicloud application";
        $view = View::make('doctor.upload', $returnArray);
        return $view;
    }
    public function Upload1(){
        $file = Input::file('file');
        echo '<pre>';
        print_r($file);
        echo '</pre>';
        $originalName = $file->getClientOriginalName();

        $destinationPath = public_path().'/assets/upload/doctor/';
        //$filename = $destinationPath . '' . str_random(32) . '.' . $file->getClientOriginalExtension();  
        //Image::make($file->getRealPath())->resize('100','100')->save($filename);
        
        //$uploaded = $file->move($destinationPath, $originalName);
        //$path = $destinationPath.'/'.$originalName;
       
        echo '=<br>';
        
        
    }
    public function Upload() {
        $file = Input::file('file'); 
        //var_dump($file);
        $rules = array(
            'file' => 'required|mimes:png,gif,jpeg|max:20000'
        );
        $validator = \Validator::make( Input::all() , $rules);
        if($validator->passes()){
            //get File Name
            $newFilename = $file->getClientOriginalName();
            $newFilename = $this->cleanFileName( $newFilename );
            //destination path
            //$destinationPath= public_path('assets') .'/upload/doctor/';//'uploads/jav_'.str_random(8);    
            $destinationPath = public_path().'/assets/upload/doctor/';
            //while (File::exists( $destinationPath . $newFilename)) {
            //    $newFilename = uniqid() . "_" . $newFilename;
            //}

            $uploadSuccess  = Image::make( $file->getRealPath() )
                                ->resize(250,null, function ($constraint) { $constraint->aspectRatio(); })
                                ->save($destinationPath.$newFilename);
                                // see http://image.intervention.io/api/resize

            if($uploadSuccess){            
                echo 'success';
            } 
            else {
               echo 'error'; 
            }                           
        }else{
            echo 'error. Invalid file format or size >2Mb'; 
        }

    }  
private function cleanFileName($fileName)
    {
        //remove blanks
        $fileName = preg_replace('/\s+/', '', $fileName);
        //remove charactes
        $fileName = preg_replace("/[^A-Za-z0-9_-\s.]/", "", $fileName);

    return $fileName;
    }
    public function upl(){
        //        echo '<br>';
//        echo $filename->getClientMimeType();
//        echo '<br>';
//        echo $filename->getClientSize();
//        echo '<br>';
        //echo $file->getRealPath();
        //echo $filename->originalName;
        //var_dump(Input::file('file'));
        
             //Image::make($filename->getRealPath())->resize(200, 200)->save($path);
//        $uploadSuccess  = Image::make($filename->getRealPath() )
//                                ->resize(250,null, function ($constraint) { $constraint->aspectRatio(); })
//                                ->save($path);
//        
        /*
        $img = Image::make($destinationPath.'/'.$originalName);
        $img->resize(100, 100);
        $img->save($path);
          */ 
        
        //$destinationPath = 'http://localhost:81/medicloud_web/public/app/doctor/file-upload';
        //$filename->move($destinationPath, $originalName);
        //return Input::file('file')->move(__DIR__.'public/assets/',Input::file('file')->getClientOriginalName());
        
        //$img = Image::make($filename->getRealPath().'/'.);
        //$img->resize(300, null, function ($constraint) {
        //    $nwimg = $constraint->aspectRatio();
        //});
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
