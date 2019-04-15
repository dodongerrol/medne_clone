<?php

class Clinic_Library {
    
    /* Use          :   Used to find clinic details
     * Access       :   Public 
     */
    public static function FindClinicDetails($clinicid){
        $clinic = new Clinic();
        $clinicDetails = $clinic->FindClinicDetails($clinicid);
        if($clinicDetails){
            return $clinicDetails;
        }else{
            return FALSE;
        }
    }
    
    public static function ClinicSettings($clinicid){
        $getSessionData = StringHelper::getMainSession(3);
        if($getSessionData != FALSE){  
            $clinicDetails = self::FindClinicDetails($clinicid);
            $findInsuranceCompany = Insurance_Library::FindAllInsurance();
            $returnArray['insurances'] = $findInsuranceCompany;
            $returnArray['clinics'] = $clinicDetails;
            $returnArray['title'] = "Medicloud Clinic Settings";
            return $returnArray;         
        }else{
            return Redirect::to('provider-portal-login');
        } 
    }
    
    public static function BookingPage($sessiondata,$currentdate){
        //$findClinicDoctors = Doctor_Library::FindClinicDoctors($sessiondata);
        $findClinicDoctors = Doctor_Library::FindActiveClinicDoctors($sessiondata);
        //echo '<pre>'; print_r($findClinicDoctors); echo '</pre>';
        if($findClinicDoctors){ 
            $doctorPlace = 1; $firstDoctorID=0;
            foreach($findClinicDoctors as $clinicdoctor){
                $findDoctor = Doctor_Library::FindDoctorDetails($clinicdoctor->DoctorID);
                if($findDoctor){
                    if($doctorPlace == 1) {
                        $firstDoctorID = $findDoctor->DoctorID; 
                        $doctorPlace ++;
                    }
                    $returnArray['doctors']['clinicid'] = $sessiondata;
                    $returnArray['doctors']['doctorid'] = $findDoctor->DoctorID;
                    $returnArray['doctors']['name'] = $findDoctor->Name;
                    //$returnArray['doctors']['image'] = URL::to('/assets/'.$findDoctor->image);
                    $returnArray['doctors']['image'] = $findDoctor->image;
                    $returnArray['doctors']['email'] = $findDoctor->Email;
                    $returnArray['doctors']['specialty'] = $findDoctor->Specialty;
                    $returnArray['doctors']['bookdate'] = $currentdate;
                        //echo '<pre>'; print_r($findDoctor); echo '</pre>';
                if($firstDoctorID == $findDoctor->DoctorID){
                    //$getDoctorslot = $docController->findDoctorSlots($firstDoctorID,$sessiondata);
                    $getDoctorslot = Doctor_Library::FindClinicDoctorSlot($firstDoctorID,$sessiondata);
                    
                    if($getDoctorslot){
                        $doctorSlotMng = Doctor_Library::DoctorSlotManageByDate($getDoctorslot->DoctorSlotID,$currentdate);
                        $cancelQueueTotal = Doctor_Library::CancelledAppointmentQueue($getDoctorslot->DoctorSlotID,$currentdate);
                        
                        $returnArray['doctors']['doctorslot']['doctorslotid'] = $getDoctorslot->DoctorSlotID;
                        $returnArray['doctors']['doctorslot']['clinicsession'] = $getDoctorslot->ClinicSession;
                        $returnArray['doctors']['doctorslot']['consultationcharge'] = $getDoctorslot->ConsultationCharge;
                        $returnArray['doctors']['doctorslot']['timeslot'] = $getDoctorslot->TimeSlot;
                        $returnArray['doctors']['doctorslot']['queuenumber'] = $getDoctorslot->QueueNumber;
                        $returnArray['doctors']['doctorslot']['queuetime'] = $getDoctorslot->QueueTime;

                        if($doctorSlotMng){     
                            $returnArray['doctors']['doctorslot']['slotmanageid'] = $doctorSlotMng->DoctorSlotManageID;
                            $returnArray['doctors']['doctorslot']['currentqueuetotal'] = $doctorSlotMng->CurrentTotalQueue;
                            $returnArray['doctors']['doctorslot']['queuestop'] = $doctorSlotMng->Status;
                        }else {
                            $returnArray['doctors']['doctorslot']['currentqueuetotal'] =null; 
                            $returnArray['doctors']['doctorslot']['queuestop'] = null;
                            $returnArray['doctors']['doctorslot']['slotmanageid'] =null;
                        }
                        if($cancelQueueTotal > 0){
                            $returnArray['doctors']['doctorslot']['queuecancelled'] = $cancelQueueTotal;
                        }else { $returnArray['doctors']['doctorslot']['queuecancelled']= null; }

                        $slottype = ArrayHelper::getSlotType($getDoctorslot->TimeSlot);                       
                        $findQueueAppointment = Doctor_Library::FindAppointmentQueue($getDoctorslot->DoctorSlotID,$currentdate);
                        $findSlotDetails = Doctor_Library::FindSlotdetailsWithDate($getDoctorslot->DoctorSlotID,$slottype,$currentdate);
                    
                    if($findSlotDetails){  
                        if($getDoctorslot->ClinicSession==3 || $getDoctorslot->ClinicSession==2){
                            foreach($findSlotDetails as $slotDetail){
                                //$findSlotAppointment = $docController->findAppointmentSlot($slotDetail->SlotDetailID,$currentdate);
                                $findSlotAppointment = Doctor_Library::FindSlotBooking($slotDetail->SlotDetailID,$currentdate);         
                                if(!empty($findSlotAppointment)){
                                    //$findSlotUser = $authcontroller->getUserDetails($findSlotAppointment->UserID);
                                    $findSlotUser = Auth_Library::FindUserDetails($findSlotAppointment->UserID);
                                    $slotarray['appoint']['appointid'] = $findSlotAppointment->UserAppoinmentID;
                                    $slotarray['appoint']['userid'] = $findSlotAppointment->UserID;
                                    $slotarray['appoint']['slotdetailid'] = $findSlotAppointment->SlotDetailID;
                                    $slotarray['appoint']['booktype'] = $findSlotAppointment->BookType;
                                    $slotarray['appoint']['mediatype'] = $findSlotAppointment->MediaType;
                                    $slotarray['appoint']['bookdate'] = $findSlotAppointment->BookDate;
                                    $slotarray['appoint']['status'] = $findSlotAppointment->Status;
                                    $slotarray['appoint']['active'] = $findSlotAppointment->Active; 
                                    if($findSlotUser){
                                        $slotarray['appoint']['user']['userid'] = $findSlotUser->UserID;
                                        $slotarray['appoint']['user']['name'] = $findSlotUser->Name;
                                        $slotarray['appoint']['user']['email'] = $findSlotUser->Email;
                                    }else{
                                        $slotarray['appoint']['user'] = null;
                                    }
                                }else{
                                    $slotarray['appoint'] = null;
                                }
                                $slotarray['slot']['slotdetailid'] = $slotDetail->SlotDetailID;
                                $slotarray['slot']['doctorslotid'] = $slotDetail->DoctorSlotID;
                                $slotarray['slot']['slotid'] = $slotDetail->SlotID;
                                $slotarray['slot']['date'] = $slotDetail->Date;
                                $slotarray['slot']['time'] = $slotDetail->Time;
                                $slotarray['slot']['available'] = $slotDetail->Available;
                                $slotarray['slot']['active'] = $slotDetail->Active;

                                $slotAppoint[] = $slotarray; 
                            }
                            $returnArray['doctors']['slot-details'] = $slotAppoint;
                            $slotAppoint = null;
                        }else{                                   
                            $returnArray['doctors']['slot-details'] = null;
                        }  
                    }else{ 
                        $returnArray['doctors']['slot-details'] = null; 
                    }
                        //for queue appointment
                        if($findQueueAppointment){
                            foreach($findQueueAppointment as $fqueueAppoint){
                                $queueArray['appointmentid'] = $fqueueAppoint->UserAppoinmentID;
                                $queueArray['bookno'] = $fqueueAppoint->BookNumber;
                                $queueArray['status'] = $fqueueAppoint->Status;
                                $queueArray['bookdate'] = $fqueueAppoint->BookDate;
                                //$findSlotUser = $authcontroller->getUserDetails($fqueueAppoint->UserID);
                                $findSlotUser = Auth_Library::FindUserDetails($fqueueAppoint->UserID);
                                if($findSlotUser){
                                    $queueArray['user']['userid'] = $findSlotUser->UserID;
                                    $queueArray['user']['name'] = $findSlotUser->Name;
                                    $queueArray['user']['email'] = $findSlotUser->Email;
                                }else{
                                    $queueArray['user'] = null;
                                }                           
                                $queueAppoint[] = $queueArray;
                            }
                            $returnArray['doctors']['queue-booking'] = $queueAppoint;
                        }else{                          
                            $returnArray['doctors']['queue-booking'] = null;
                        }
                    }else{
                        $returnArray['doctors']['doctorslot'] = null;
                        $returnArray['doctors']['slot-details'] = null;
                        $returnArray['doctors']['queue-booking'] = null;
                    } 
                }else{
                    $returnArray['doctors']['slot-details'] = null;
                    $returnArray['doctors']['queue-booking'] = null;
                }
                    $doctors[] = $returnArray;
                }//end find doctor
            }//end foreach 
            return $doctors;
        }else{
            return FALSE;
        }
    }
    
    public static function FindActiveAppointments(){
        $userappointment = new UserAppoinment();
        $findActiveAppointments = $userappointment->FindActiveAppointments();
        if($findActiveAppointments){
            return $findActiveAppointments;
        }else{
            return FALSE;
        }
    }  
    public static function FindAvailableAppointments($bookdate){
        $userappointment = new UserAppoinment();
        $findActiveAppointments = $userappointment->FindAvailableAppointments($bookdate);
        if($findActiveAppointments){
            return $findActiveAppointments;
        }else{
            return FALSE;
        }
    }  
    
    
    public static function ClinicSettingDashboard($clinicid){
        $currentdate = date('d-m-Y');
        $returnObject = self::ProcessClinicSettingDashboard($clinicid,$currentdate,0);
        //echo '<pre>'; print_r($returnObject); echo '</pre>';
        $returnObject['title'] = "Medicloud Clinic Dashboard";
        $view = View::make('clinic.setting-dashboard', $returnObject);
        return $view;
    }

    private static function ProcessClinicSettingDashboard($clinicid,$currentdate,$pagestatus){
        $apm = new UserAppoinment();
        $returnArray = array();
        $slotdoctor = array(); $queuedoctor = array();
        $mainArray = self::FindTodayAvailableDoctors($clinicid,$currentdate);
        if(!empty($mainArray)){
            foreach($mainArray as $slotArray){
                //$localArray['count'] = count($mainArray);
                if($slotArray->ClinicSession==2){
                    $slotDetailArray = array();
                    $queueAppoint = array();
                    
                    //for slots
                    $getslottype = ArrayHelper::getSlotType($slotArray->TimeSlot);
                    $findSlotDetails = self::FindActiveSlotDetailsType($slotArray->DoctorSlotID,$currentdate,$getslottype);
                    //$findSlotDetails = self::FindActiveSlotDetails($slotArray->DoctorSlotID,$currentdate);
                    
                    if($findSlotDetails){
                        //$localArray['count'] = count($mainArray);
                        $localArray['doctorid'] = $slotArray->DoctorID;
                        $localArray['clinicid'] = $slotArray->ClinicID;
                        $localArray['name'] = $slotArray->Name;
                        $localArray['email'] = $slotArray->Email;
                        $localArray['image'] = $slotArray->image;
                        $localArray['qualification'] = $slotArray->Qualifications;
                        $localArray['speciality'] = $slotArray->Specialty;
                        $localArray['doctorslotid'] = $slotArray->DoctorSlotID;
                        $localArray['clinicsesstion'] = $slotArray->ClinicSession;
                        $localArray['timeslot'] = $slotArray->TimeSlot;
                        $localArray['consultation'] = $slotArray->ConsultationCharge;
                        $localArray['date'] = $currentdate;
                        foreach($findSlotDetails as $slotdetails){
                            $findSlotAppointment = Doctor_Library::FindSlotBooking($slotdetails->SlotDetailID,$currentdate);
                            if(!empty($findSlotAppointment)){
                                $findSlotUser = Auth_Library::FindUserDetails($findSlotAppointment->UserID);
                                $deArray['appoint']['appointid'] = $findSlotAppointment->UserAppoinmentID;
                                $deArray['appoint']['userid'] = $findSlotAppointment->UserID;
                                $deArray['appoint']['slotdetailid'] = $findSlotAppointment->SlotDetailID;
                                $deArray['appoint']['booktype'] = $findSlotAppointment->BookType;
                                $deArray['appoint']['mediatype'] = $findSlotAppointment->MediaType;
                                $deArray['appoint']['bookdate'] = $findSlotAppointment->BookDate;
                                $deArray['appoint']['status'] = $findSlotAppointment->Status;
                                $deArray['appoint']['active'] = $findSlotAppointment->Active; 
                                $deArray['appoint']['user']['userid'] = $findSlotUser->UserID;
                                $deArray['appoint']['user']['name'] = $findSlotUser->Name;
                                $deArray['appoint']['user']['email'] = $findSlotUser->Email;
                                //$slotcount[] = count($findSlotAppointment);
                                //if($findSlotAppointment->Status==2){$slotcompleted[] = count($findSlotAppointment); } 
                            }else{ 
                                $deArray['appoint'] = null;
                            }
                            
                            $deArray['slot']['slotdetailid'] = $slotdetails->SlotDetailID;
                            $deArray['slot']['doctorslotid'] = $slotdetails->SlotID;
                            $deArray['slot']['slottype'] = $slotdetails->SlotType;
                            $deArray['slot']['date'] = $slotdetails->Date;
                            $deArray['slot']['time'] = $slotdetails->Time;
                            $deArray['slot']['available'] = $slotdetails->Available;
                            $slotDetailArray[] = $deArray; 
                        }
                        $localArray['slots'] = $slotDetailArray;
                        $localArray['queue'] = null;
                        $returnArray[] = $localArray;
                        $slotdoctor[]= 1;
                    }else{
                       //$localArray['slots'] = null;
                    }                   
                }elseif($slotArray->ClinicSession==1){
                   //for Queue 
                        $findQueueAppointment = Doctor_Library::FindAppointmentQueue($slotArray->DoctorSlotID,$currentdate);
                        $cancelQueueTotal = Doctor_Library::CancelledAppointmentQueue($slotArray->DoctorSlotID,$currentdate);
                        $doctorSlotMng = Doctor_Library::DoctorSlotManageByDate($slotArray->DoctorSlotID,$currentdate);
                        
                        $localArray['doctorid'] = $slotArray->DoctorID;
                        $localArray['clinicid'] = $slotArray->ClinicID;
                        $localArray['name'] = $slotArray->Name;
                        $localArray['email'] = $slotArray->Email;
                        $localArray['image'] = $slotArray->image;
                        $localArray['qualification'] = $slotArray->Qualifications;
                        $localArray['speciality'] = $slotArray->Specialty;
                        $localArray['doctorslotid'] = $slotArray->DoctorSlotID;
                        $localArray['clinicsesstion'] = $slotArray->ClinicSession;
                        $localArray['consultation'] = $slotArray->ConsultationCharge;  
                        $localArray['queueno'] = $slotArray->QueueNumber;
                        $localArray['queuetime'] = $slotArray->QueueTime;
                        $localArray['date'] = $currentdate;
                        $localArray['slots'] = null;
                        if($doctorSlotMng){
                            $localArray['queuestop']['slotmanageid'] = $doctorSlotMng->DoctorSlotManageID;
                            //$localArray['doctorslot']['currentqueuetotal'] = $doctorSlotMng->CurrentTotalQueue;
                            $localArray['queuestop']['status'] = $doctorSlotMng->Status;
                        }else{
                            $localArray['queuestop']= null;
                        }
                        
                        if($findQueueAppointment){ 
                            $queuecount = count($findQueueAppointment);
                            
                            foreach($findQueueAppointment as $fqueueAppoint){                             
                                $queueArray['total'] = $queuecount; 
                                $queueArray['cancelled'] = $cancelQueueTotal; 
                                $queueArray['appointmentid'] = $fqueueAppoint->UserAppoinmentID;
                                $queueArray['bookno'] = $fqueueAppoint->BookNumber;
                                $queueArray['status'] = $fqueueAppoint->Status;
                                $queueArray['bookdate'] = $fqueueAppoint->BookDate;
                                $findSlotUser = Auth_Library::FindUserDetails($fqueueAppoint->UserID);
                                $queueArray['user']['userid'] = $findSlotUser->UserID;
                                $queueArray['user']['name'] = $findSlotUser->Name;
                                $queueArray['user']['email'] = $findSlotUser->Email; 
                                $queueAppoint[] = $queueArray;
                            }
                            
                            
                            $localArray['queue'] = $queueAppoint; 
                            $queueAppoint = null;
                        }else{                          
                            $localArray['queue'] = null;
                        }
                    $returnArray[] = $localArray;   
                    $queuedoctor[]= 1;
                }
                
            }
        }
        $docQueueSlot = array_merge($slotdoctor,$queuedoctor);
        $returnResults['clinicid'] = $clinicid;
        $returnResults['totalDoctors'] = count($docQueueSlot);
        $returnResults['currentPage'] = $pagestatus;
        $returnResults['currentDate'] = $currentdate;
        $returnResults['displayDate'] = date("l j F Y", strtotime($currentdate));
        $returnObject['alldoctors'] = $returnArray;
        //echo count($returnObject['doctors'])/1;
        $returnResults['doctors'] = array_slice( $returnObject['alldoctors'], $pagestatus, 2); 
        
        //echo '<pre>'; print_r($returnObject); echo '</pre>';
        //echo '<pre>'; print_r($returnResults); echo '</pre>';
        return $returnResults;
        
        //return $returnObject;
    }
    
    public static function ClinicSettingDashboardAJAX(){
        $inputdata = Input::all();
        if(!empty($inputdata)){
            $returnObject = self::ProcessClinicSettingDashboard($inputdata['clinicid'],$inputdata['currentdate'],$inputdata['pageno']);

            $view = View::make('ajax.clinic-doctor-dashboard', $returnObject);
            return $view;
        }else{
           return 0; 
        }    
    }
    
    /* Use  : It is used generate doctor available date  page in clinic 
     * Access : Public 
     * 
     */
    public static function ClinicDashboardBooking($doctorslotid){
        $currentdate = date('d-m-Y');
        $returnObject = self::ProcessClinicDashboardBooking($doctorslotid,$currentdate); 
        if($returnObject){
            $returnObject['title'] = "Medicloud Clinic Dashboard";
            $view = View::make('clinic.dashboard-booking', $returnObject);
            return $view;
        }else{
            return Redirect::to('app/clinic/settings-dashboard');
        }
        
    }

    public static function ClinicDetails($id)
    {
        $clinic_details = [];
        $clinic = new Clinic();
        $clinic_type = new ClinicTypes();

        $clinic_details['clinic'] = $clinic->ClinicDetail($id);
        $clinic_details['clinic']['currency_amount'] = 3.00;
        // return $clinic_details['clinic']['Clinic_Type'];
        $clinic_details['clinic_type'] = $clinic_type->getClinicType($clinic_details['clinic']['Clinic_Type']);
        return $clinic_details;
    }
    
    /* Use : Use to process for generating doctor availabel date page
     * Access : Private
     * 
     */
    private static function ProcessClinicDashboardBooking($doctorslotid,$currentdate){
        $findDoctorDetails = self::FindDoctorDetailsBySlot($doctorslotid);
        //echo '<pre>'; print_r($findDoctorDetails); echo '</pre>'; die;
        if($findDoctorDetails){
            $localArray['doctorid'] = $findDoctorDetails->DoctorID;
            $localArray['clinicid'] = $findDoctorDetails->ClinicID;
            $localArray['name'] = $findDoctorDetails->Name;
            $localArray['email'] = $findDoctorDetails->Email;
            $localArray['image'] = $findDoctorDetails->image;
            $localArray['qualification'] = $findDoctorDetails->Qualifications;
            $localArray['speciality'] = $findDoctorDetails->Specialty;
            $localArray['doctorslotid'] = $findDoctorDetails->DoctorSlotID;
            $localArray['clinicsesstion'] = $findDoctorDetails->ClinicSession;
            $localArray['consultation'] = $findDoctorDetails->ConsultationCharge;  
            $localArray['timeslot'] = $findDoctorDetails->TimeSlot;
            $localArray['queueno'] = $findDoctorDetails->QueueNumber;
            $localArray['queuetime'] = $findDoctorDetails->QueueTime;
            $localArray['date'] = $currentdate;
            $localArray['slots'] = null;
        if($findDoctorDetails->ClinicSession==1){
            $findQueueAppointment = Doctor_Library::FindAppointmentQueue($findDoctorDetails->DoctorSlotID,$currentdate);
            $cancelQueueTotal = Doctor_Library::CancelledAppointmentQueue($findDoctorDetails->DoctorSlotID,$currentdate);
            $doctorSlotMng = Doctor_Library::DoctorSlotManageByDate($findDoctorDetails->DoctorSlotID,$currentdate);
            
            if($findQueueAppointment){ 
                $queuecount = count($findQueueAppointment);
                foreach($findQueueAppointment as $fqueueAppoint){                             
                    $queueArray['total'] = $queuecount; 
                    $queueArray['cancelled'] = $cancelQueueTotal; 
                    $queueArray['appointmentid'] = $fqueueAppoint->UserAppoinmentID;
                    $queueArray['bookno'] = $fqueueAppoint->BookNumber;
                    $queueArray['status'] = $fqueueAppoint->Status;
                    $queueArray['bookdate'] = $fqueueAppoint->BookDate;
                    $findSlotUser = Auth_Library::FindUserDetails($fqueueAppoint->UserID);
                    $queueArray['user']['userid'] = $findSlotUser->UserID;
                    $queueArray['user']['name'] = $findSlotUser->Name;
                    $queueArray['user']['email'] = $findSlotUser->Email; 
                    $queueAppoint[] = $queueArray;
                }
                $localArray['queue'] = $queueAppoint; 
            }else{                          
                $localArray['queue'] = null;
            }
            if($doctorSlotMng){
                $localArray['queuestop']['slotmanageid'] = $doctorSlotMng->DoctorSlotManageID;
                $localArray['queuestop']['status'] = $doctorSlotMng->Status;
            }else{
                $localArray['queuestop']= null;
            }
        }elseif($findDoctorDetails->ClinicSession==2){
            //$findSlotDetails = self::FindActiveSlotDetails($findDoctorDetails->DoctorSlotID,$currentdate);
            $getslottype = ArrayHelper::getSlotType($findDoctorDetails->TimeSlot);
            $findSlotDetails = self::FindActiveSlotDetailsType($findDoctorDetails->DoctorSlotID,$currentdate,$getslottype);
            if($findSlotDetails){
                foreach($findSlotDetails as $slotdetails){
                    $findSlotAppointment = Doctor_Library::FindSlotBooking($slotdetails->SlotDetailID,$currentdate);
                    if(!empty($findSlotAppointment)){
                        $findSlotUser = Auth_Library::FindUserDetails($findSlotAppointment->UserID);
                        $deArray['appoint']['appointid'] = $findSlotAppointment->UserAppoinmentID;
                        $deArray['appoint']['userid'] = $findSlotAppointment->UserID;
                        $deArray['appoint']['slotdetailid'] = $findSlotAppointment->SlotDetailID;
                        $deArray['appoint']['booktype'] = $findSlotAppointment->BookType;
                        $deArray['appoint']['mediatype'] = $findSlotAppointment->MediaType;
                        $deArray['appoint']['bookdate'] = $findSlotAppointment->BookDate;
                        $deArray['appoint']['status'] = $findSlotAppointment->Status;
                        $deArray['appoint']['active'] = $findSlotAppointment->Active; 
                        $deArray['appoint']['user']['userid'] = $findSlotUser->UserID;
                        $deArray['appoint']['user']['name'] = $findSlotUser->Name;
                        $deArray['appoint']['user']['email'] = $findSlotUser->Email;
                        //$slotcount[] = count($findSlotAppointment);
                        //if($findSlotAppointment->Status==2){$slotcompleted[] = count($findSlotAppointment); } 
                    }else{ 
                        $deArray['appoint'] = null;
                    }

                    $deArray['slot']['slotdetailid'] = $slotdetails->SlotDetailID;
                    $deArray['slot']['doctorslotid'] = $slotdetails->SlotID;
                    $deArray['slot']['slottype'] = $slotdetails->SlotType;
                    $deArray['slot']['date'] = $slotdetails->Date;
                    $deArray['slot']['time'] = $slotdetails->Time;
                    $deArray['slot']['available'] = $slotdetails->Available;
                    $slotDetailArray[] = $deArray; 
            }
                $localArray['slots'] = $slotDetailArray;
                $localArray['queue'] = null;
                //$returnArray[] = $localArray;
            }else{

            }       
        }
        return $localArray;
        }else{
           return FALSE;
        }
    }
    
    /* Use : this function is used by AJAX to display doctor booking detail page
     * 
     * 
     */
    public static function ClinicDashboardBookingAJAX(){
        $inputdata = Input::all();
        if(!empty($inputdata)){
            $returnObject = self::ProcessClinicDashboardBooking($inputdata['doctorslotid'],$inputdata['currentdate']);
            //echo '<pre>'; print_r($returnObject); echo '</pre>';
            $view = View::make('ajax.clinic-dashboard-booking', $returnObject);
            return $view;
        }else{
           return 0; 
        } 
    }
    
    
    /* Use : Used to find today available doctors
     * Access : Public
     * 
     */
    public static function FindTodayAvailableDoctors($clinicid,$currentdate){
        $doctorslot = new DoctorSlots();
        $findAvailableDoctors = $doctorslot->TodayAvailableDoctors($clinicid,$currentdate);
        if($findAvailableDoctors){
            return $findAvailableDoctors;
        }else{
            return FALSE;
        }
    } 
    
    /* Use : Used to find active slots for today 
     * Access : Public 
     * 
     */
    public static function FindActiveSlotDetails($doctorslotid,$currentdate){
        $doctorslotdetail = new DoctorSlotDetails();
        $findAvailableSlots = $doctorslotdetail->FindActiveSlotDetails($doctorslotid,$currentdate);
        if($findAvailableSlots){
            return $findAvailableSlots;
        }else{
            return FALSE;
        }
    }
    public static function FindActiveSlotDetailsType($doctorslotid,$currentdate,$slottype){
        $doctorslotdetail = new DoctorSlotDetails();
        if(!empty($doctorslotid) && !empty($currentdate) && !empty($slottype)){
            $findAvailableSlots = $doctorslotdetail->FindActiveSlotDetailsType($doctorslotid,$currentdate,$slottype);
            if($findAvailableSlots){
                return $findAvailableSlots;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    /* Use : Used to find doctor details by slot
     * Access : Public 
     * 
     */
    public static function FindDoctorDetailsBySlot($doctorslotid){
        $doctorslot = new DoctorSlots();
        $doctorDetails = $doctorslot->FindDoctorDetailBySlot($doctorslotid);
        if($doctorDetails){
            return $doctorDetails;
        }else{
            return FALSE;
        }
    }
    
    /*
     * 
     */
    public static function CloudineryImageUploadWithResize($width,$height){
        $imageUpload = Image_Library::CloudinaryUploadWithResize($width,$height);
        if($imageUpload){
            $data['img'] = $imageUpload;
            return $data;
        }else{
            return 0;
        }        
    }
    
    /* Use         :    Display clinic details page
     * 
     */
    public static function ClinicDetailsPage($clinicdata){
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            //echo '<pre>';print_r($findClinicDetails);echo '</pre>';
            $clinicArray = Array_Helper::GetClinicDetailArray($clinicdata,$findClinicDetails);
            $dataArray['clinicdetails'] = $clinicArray;
            $dataArray['title'] = "Clinic Details Page";
            $view = View::make('clinic.clinic-details', $dataArray);     
            return $view;
        }else{
            return FALSE;
        }
    }
    
    public static function ClinicDetailsPageUpdate(){
        $inputdata = Input::all();
        
        if(!empty($inputdata)){
            $dataArray['clinicid'] = $inputdata['clinicid'];
            $dataArray['Name'] = $inputdata['name'];
            $dataArray['image'] = $inputdata['image'];
            $dataArray['Address'] = $inputdata['address'];
            $dataArray['City'] = $inputdata['city'];
            $dataArray['State'] = $inputdata['state'];
            $dataArray['Country'] = $inputdata['country'];
            $dataArray['Postal'] = $inputdata['postal'];
            $dataArray['Description'] = $inputdata['description'];
            $dataArray['Phone'] = $inputdata['phone'];
            $dataArray['Website'] = $inputdata['website'];
            $dataArray['Custom_title'] = $inputdata['title'];
            
            $clinicUpdate = self::UpdateClinicDetails($dataArray);
            if($clinicUpdate){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    public static function UpdateClinicDetails($dataArray){
        $clinic = new Clinic();
        $updated = $clinic->UpdateClinicDetails($dataArray);
        if($updated){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    public static function ClinicAddProcedurePage($clinicdata){
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            $allProcedures = self::FindClinicProcedures($findClinicDetails->ClinicID);
            if($allProcedures){
                $dataArray['procedures'] = $allProcedures;
            }else{
                $dataArray['procedures'] = null;
            }
            //$clinicArray = Array_Helper::GetClinicDetailArray($clinicdata,$findClinicDetails);
            $dataArray['clinicdetails'] = $findClinicDetails;
            $dataArray['title'] = "Clinic Add Procedure Page";
            $view = View::make('clinic.add-procedures', $dataArray);     
            return $view;
        }else{
            return FALSE;
        }
    }
    
    /* Use      : Used to add procedure by ajax call
     * 
     * 
     */
    public static function ClinicAddProcedure(){
        $allInputs = Input::all();
        if(!empty($allInputs)){
            $clinicprocedure = new ClinicProcedures();
            $dataArray['clinicid'] = $allInputs['clinicid'];
            $dataArray['name'] = $allInputs['name'];
            $dataArray['description'] = null;
            $dataArray['duration'] = $allInputs['duration'];
            $dataArray['durationformat'] = "mins";
            $dataArray['price'] = $allInputs['price'];
            
            $addProcedure = $clinicprocedure->AddProcedures($dataArray);
            if($addProcedure){
                $allProcedures = self::FindClinicProcedures($allInputs['clinicid']);
                if($allProcedures){
                    $loadArray['procedures'] = $allProcedures;
                    $view = View::make('ajax.clinic.load-procedures', $loadArray);     
                    return $view;
                    //return $allProcedures;
                }else{
                    return 0;
                }
                //return $addProcedure;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    /* Use      :       Used to delete procedures by AJAX
     * 
     * 
     */
    public static function ClinicDeleteProcedure(){
        $allInputs = Input::all();
        if(!empty($allInputs)){
            $clinicprocedure = new ClinicProcedures();
            $findProcedure = self::GetClinicProcedure($allInputs['procedureid']);
            if($findProcedure){
                $findActiveBooking = General_Library::FindProcedureBooking($findProcedure->ProcedureID);
                if($findActiveBooking){
                    return 5;
                }
                $updateArray['procedureid'] = $findProcedure->ProcedureID;
                $updateArray['Active'] = 0;
                $updateArray['updated_at'] = time();
                $updated = self::UpdateProcedure($updateArray);
                if($updated){
                    $allProcedures = self::FindClinicProcedures($findProcedure->ClinicID);
                    if($allProcedures){
                        $loadArray['procedures'] = $allProcedures;
                    }else{
                        $loadArray['procedures'] = null;
                    }
                        $view = View::make('ajax.clinic.load-procedures', $loadArray);     
                        return $view;
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
    
    
    public static function FindClinicProcedures($clinicid){
        $clinicprocedure = new ClinicProcedures();
        $findProcedures = $clinicprocedure->FindClinicProcedures($clinicid);
        if($findProcedures){
            return $findProcedures;
        }else{
            return FALSE;
        }
    }
    
    public static function GetClinicProcedure($procedureid){
        $clinicprocedure = new ClinicProcedures();
        $findProcedures = $clinicprocedure->GetClinicProcedure($procedureid);
        if($findProcedures){
            return $findProcedures;
        }else{
            return FALSE;
        }
    }
    
    public static function UpdateProcedure($updateArray){
        $clinicprocedure = new ClinicProcedures();
        if(!empty($updateArray)){
            $updated = $clinicprocedure->UpdateProcedure($updateArray);
            if($updated){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        } 
    }
    
    public static function ClinicAddDoctorPage($clinicdata){
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            $allProcedures = self::FindClinicProcedures($findClinicDetails->ClinicID);
            //if($allProcedures){
            //    $dataArray['procedures'] = $allProcedures;
            //}else{
            //    $dataArray['procedures'] = null;
            //}
            //$clinicArray = Array_Helper::GetClinicDetailArray($clinicdata,$findClinicDetails);
            $dataArray['clinicprocedures'] = $allProcedures;
            $dataArray['clinicdetails'] = $findClinicDetails;
            $dataArray['title'] = "Clinic Add Doctor Page";
            $view = View::make('clinic.add-doctors', $dataArray);     
            return $view;
        }else{
            return FALSE;
        }
    }
    
    public static function ClinicAddDoctor(){
        $allInputs = Input::all();
        if(!empty($allInputs)){
            
            $totalProcedures = explode(',', $allInputs['procedure']);
            
            $findEmailExist = Auth_Library::CheckEmailExist($allInputs['email']);
            if($findEmailExist == TRUE){
                return 2;
            }else{          
                $dataArray['name'] = $allInputs['name'];
                $dataArray['email'] = $allInputs['email'];
                $dataArray['qualification'] = $allInputs['qualification'];
                $dataArray['speciality'] = $allInputs['speciality'];
                $dataArray['image'] = $allInputs['image'];            
                $dataArray['code'] = $allInputs['code'];
                $dataArray['emergency_code'] = $allInputs['emergency_code'];
                $dataArray['phone'] = $allInputs['phone'];
                $dataArray['emergency_phone'] = $allInputs['emergency_phone'];

                $addDoctor = Doctor_Library::NewDoctor($dataArray);
                if($addDoctor){
                    //Add to Availability 
                    $arrayAvail['doctorid'] = $addDoctor;
                    $arrayAvail['clinicid'] = $allInputs['clinicid'];       
                    self::AddDoctorAvailability($arrayAvail);
                    
                    $activelink = StringHelper::getEncryptValue();
                    $dataUser['name'] = $allInputs['name'];
                    $dataUser['usertype'] = 2;
                    $dataUser['email'] = $allInputs['email'];
                    $dataUser['code'] = $allInputs['code'];
                    $dataUser['mobile'] = $allInputs['phone'];
                    $dataUser['nric'] = null;
                    $dataUser['ref_id'] = $addDoctor;
                    $dataUser['activelink'] = $activelink;
                    $dataUser['status'] = 0;

                    $addUser = Auth_Library::AddNewUser($dataUser);
                    if($addUser){
                        //Sendging email 
                        $emailData['emailName']= $allInputs['name'];
                        $emailData['emailPage']= 'email-templates.new-doctor';
                        $emailData['emailTo']= $allInputs['email'];
                        $emailData['emailSubject'] = 'Please complete your registration';
                        $emailData['createLink'] = URL::to('app/auth/register?activate='.$activelink);
                        EmailHelper::sendEmail($emailData);
                        
                        
                        //$dataProcedure['procedureid'] = $allInputs['procedure'];
                        $dataProcedure['clinicid'] = $allInputs['clinicid'];
                        $dataProcedure['doctorid'] = $addDoctor;
                        foreach($totalProcedures as $ttlProcedure){
                            $dataProcedure['procedureid'] = $ttlProcedure;
                            $addProcedure = Doctor_Library::AddDoctorProcedures($dataProcedure);
                        }
                        
                        return 1;
                    }else{
                        return 0;
                    }
                }else{
                    return 0;
                }
            }
        }else{
            return 0;
        } 
    }
    
    public static function UpdateDoctorDetails($clinicdata){
        $allInputs = Input::all();
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            $totalProcedures = explode(',', $allInputs['procedure']);
            
            $dataArray['doctorid'] = $allInputs['doctorid'];
            $dataArray['Name'] = $allInputs['name'];
            $dataArray['Email'] = $allInputs['email'];
            $dataArray['Qualifications'] = $allInputs['qualification'];
            $dataArray['Specialty'] = $allInputs['speciality'];
            if(!empty($allInputs['image'])){
                $dataArray['image'] = $allInputs['image'];
            }
            $dataArray['Code'] = $allInputs['code'];
            $dataArray['Emergency_Code'] = $allInputs['emergency_code'];
            $dataArray['Phone'] = $allInputs['phone'];
            $dataArray['Emergency'] = $allInputs['emergency_phone'];

            $updated = Doctor_Library::UpdateDoctor($dataArray);

                $existingProcedureList = Doctor_Library::FindDoctorProcedures($findClinicDetails->ClinicID,$allInputs['doctorid']);
                if($existingProcedureList){
                    foreach($existingProcedureList as $procedureList){
                        $arrayProcedure['Active'] = 0;
                        $arrayProcedure['updated_at'] = time();
                        Doctor_Library::UpdateProcedure($arrayProcedure,$procedureList->DoctorProcedureID);
                    }
                }
                
                $procedureChanges = FALSE;
                if(!empty($totalProcedures)){
                    foreach($totalProcedures as $newprocedure){
                        $findSingleProcedure = Doctor_Library::FindSingleProcedures($findClinicDetails->ClinicID,$allInputs['doctorid'],$newprocedure);
                        if($findSingleProcedure){
                            $activeProcedure['Active'] = 1;
                            $activeProcedure['updated_at'] = time();
                            $procedureChanges = Doctor_Library::UpdateProcedure($activeProcedure,$findSingleProcedure->DoctorProcedureID);
                        }else{
                            $dataProcedure['clinicid'] = $findClinicDetails->ClinicID;
                            $dataProcedure['doctorid'] = $allInputs['doctorid'];
                            $dataProcedure['procedureid'] = $newprocedure;
                            $procedureChanges = Doctor_Library::AddDoctorProcedures($dataProcedure);
                        }
                    }
                }
                               
            if($updated || $procedureChanges){      
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }    
    }
    
    
    
    
    public static function ClinicDoctorsviewPage($clinicdata){
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            $findClinicDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            if($findClinicDoctors && count($findClinicDoctors > 0)){
                $clinicData['clinicid'] = $findClinicDoctors[0]->ClinicID;
                $clinicData['name'] = $findClinicDoctors[0]->CliName;
                $clinicData['image'] = $findClinicDoctors[0]->CliImage;
                $clinicData['phone'] = $findClinicDoctors[0]->CliPhone;
                
                foreach($findClinicDoctors as $clinicDoctors){
                    $doctorProcedures = Doctor_Library::FindDoctorProcedures($findClinicDoctors[0]->ClinicID,$clinicDoctors->DoctorID);
                    $procedureValue = array();
                    if($doctorProcedures){
                        foreach($doctorProcedures as $docProcedures){
                            $docProc['name'] = $docProcedures->Name;
                            $procedureValue[] = $docProc;
                        }     
                        $procedures = implode(', ', array_map(function ($a) {
                            return $a['name'];
                          }, $procedureValue));
                        $docData['procedures'] = $procedures;
                    }else{
                        $docData['procedures'] = " - ";
                    }
                   
                    $docData['doctorid']= $clinicDoctors->DoctorID;
                    $docData['name']= $clinicDoctors->DocName;
                    $docData['email']= $clinicDoctors->DocEmail;
                    $docData['image']= $clinicDoctors->DocImage;
                    $docData['phone']= $clinicDoctors->DocPhone;
                    $docData['qualification']= $clinicDoctors->Qualifications;
                    $docData['speciality']= $clinicDoctors->Specialty;
                    $newDoctorData[] = $docData;
                }
                $dataArray['clinic'] = $clinicData;
                $dataArray['doctors'] = $newDoctorData;
                $dataArray['title'] = "Clinic Doctors View Page";
                $view = View::make('clinic.doctors-view', $dataArray);     
                return $view;
            }else{
                return Redirect::to('app/clinic/clinic-doctors-home');
            }
        }else{
            return FALSE;
        }
    }
    
    public static function AddDoctorAvailability($dataArray){
        $doctoravailability = new DoctorAvailability();
        $insertAvailability = $doctoravailability->insertDoctorAvailability($dataArray);
        if($insertAvailability){
            return $insertAvailability;
        }else{
            return FALSE;
        }
    }
    public static function FindAllClinicDoctors($clinicid){
        if(!empty($clinicid)){
            $doctoravailability = new DoctorAvailability();
            $allClinicDoctors = $doctoravailability->FindAllClinicDoctors($clinicid);
            if($allClinicDoctors){
                return $allClinicDoctors;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
        
    }
    public static function FindClinicDoctorSelection($clinicid,$doctorlist){
        if(!empty($clinicid)){
            $doctoravailability = new DoctorAvailability();
            $allClinicDoctors = $doctoravailability->FindClinicDoctorSelection($clinicid,$doctorlist);
            if($allClinicDoctors){
                return $allClinicDoctors;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
        
    }
    
    
    public static function ClinicDoctorsHomePage($clinicdata){
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            $dataArray['clinicdata'] = $findClinicDetails;
            $dataArray['title'] = "Clinic Doctors Home Page";
            $view = View::make('clinic.doctor-view-home', $dataArray);     
            return $view;
        }else{
            return Redirect::to('provider-portal-login');
        }
    }
    
    public static function ClinicDoctorsDelete($clinicdata){
        $allInputs = Input::all();
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            $findDoctorAppointment = General_Library::FindDoctorBooking($allInputs['doctorid']);
            if($findDoctorAppointment){
                return 5;
            }
            $updateArray['doctorid'] = $allInputs['doctorid'];
            $updateArray['Active'] = 0;
            $updateArray['updated_at'] = time();
            $updateDoctor = Doctor_Library::UpdateDoctor($updateArray);
            if($updateDoctor){
                $findClinicDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
                if($findClinicDoctors && count($findClinicDoctors > 0)){
                    $clinicData['clinicid'] = $findClinicDoctors[0]->ClinicID;
                    $clinicData['name'] = $findClinicDoctors[0]->CliName;
                    $clinicData['image'] = $findClinicDoctors[0]->CliImage;
                    $clinicData['phone'] = $findClinicDoctors[0]->CliPhone;

                    foreach($findClinicDoctors as $clinicDoctors){
                        $doctorProcedures = Doctor_Library::FindDoctorProcedures($findClinicDoctors[0]->ClinicID,$clinicDoctors->DoctorID);

                        if($doctorProcedures){
                            foreach($doctorProcedures as $docProcedures){
                                $docProc['name'] = $docProcedures->Name;
                                $procedureValue[] = $docProc;
                            }              
                            $procedures = implode(', ', array_map(function ($a) {
                                return $a['name'];
                              }, $procedureValue));
                            $docData['procedures'] = $procedures;
                        }else{
                            $docData['procedures'] = " - ";
                        }

                        $docData['doctorid']= $clinicDoctors->DoctorID;
                        $docData['name']= $clinicDoctors->DocName;
                        $docData['email']= $clinicDoctors->DocEmail;
                        $docData['image']= $clinicDoctors->DocImage;
                        $docData['phone']= $clinicDoctors->DocPhone;
                        $docData['qualification']= $clinicDoctors->Qualifications;
                        $docData['speciality']= $clinicDoctors->Specialty;
                        $newDoctorData[] = $docData;
                    }
                    $dataArray['clinic'] = $clinicData;
                    $dataArray['doctors'] = $newDoctorData;
                    $dataArray['title'] = "Clinic Doctors View Page";
                    $view = View::make('ajax.clinic.load-doctors-view', $dataArray);     
                    return $view;
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
    
    
    public static function ClinicUpdatePasswordPage($clinicdata){
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            $dataArray['clinicuserdata'] = $clinicdata;
            $dataArray['title'] = "Clinic Update Password Page";
            $view = View::make('clinic.clinic-password-update', $dataArray);     
            return $view;
        }else{
            return Redirect::to('provider-portal-login');
        }
    }
    
    /* Use          :       Used to update clinic password
     * Parameter    :       Array
     */
    public static function  ClinicPasswordUpdate($clinicdata){
        $allInputs = Input::all();
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        $findClinicUser = Auth_Library::FindUserDetails($allInputs['clinicuserid']);
        if($findClinicDetails && $findClinicUser){   
            $oldpass = StringHelper::encode($allInputs['oldpass']);
            if($oldpass == $findClinicUser->Password){
                $updateArray['userid'] = $findClinicUser->UserID;
                $updateArray['Password'] = StringHelper::encode($allInputs['newpass']);
                $updateArray['updated_at'] = time();
                $updateClinicUser = Auth_Library::UpdateUsers($updateArray);
                if($updateClinicUser){
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

    public static function CalendarIntegrationViewPage($clinicdata){
        StringHelper::Set_Default_Timezone();
        $currentdate = date('d-m-Y');
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            $allDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            $doctorsArray['doctors'] = $allDoctors;
            $dataArray['loadarrays'] = $doctorsArray;
            if($allDoctors){
                //echo '<pre>'; print_r($allDoctors[0]); echo '</pre>';
                // $findDoctorTimes = General_Library::FindDoctorAvailabilityTimes(2,$allDoctors[0]->DoctorID);
                $findDoctorTimes = General_Library::FindAllClinicTimes(2,$allDoctors[0]->DoctorID,  strtotime($currentdate));
                $findDoctorHolidays = General_Library::FindExistingClinicHolidays(2,$allDoctors[0]->DoctorID);
                $dataArray['loadarrays']['currentdoctor'] = $allDoctors[0];
                $dataArray['loadarrays']['doctortimes'] = $findDoctorTimes;
                $dataArray['loadarrays']['doctorholidays'] = $findDoctorHolidays;
            }else{
                $dataArray['loadarrays']['currentdoctor'] = null;
            }
            //To make Current doctor null
            $dataArray['loadarrays']['currentdoctor'] = null;

            $dataArray['title'] = "Google Calender Integration";
            $view = View::make('clinic.clinic-calendar-integration', $dataArray);
            return $view;
        }else{
            return Redirect::to('provider-portal-login');
        }
    }


    public static function buttonIntegrationViewPage($clinicdata){
        StringHelper::Set_Default_Timezone();
        $currentdate = date('d-m-Y');
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            

            $dataArray['title'] = "Widget Button Integration";
            $dataArray['clinicID'] = $clinicdata->Ref_ID;
            $view = View::make('clinic.clinic-button-integration', $dataArray);
            return $view;
        }else{
            return Redirect::to('provider-portal-login');
        }
    }

    




    /* Use      :   Used to set clinic opening times 
     * 
     */
    public static function ClinicOpeningTimesPage($clinicdata){
        StringHelper::Set_Default_Timezone();
        $currentdate = date('d-m-Y');
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            $findClinicTimes = General_Library::FindAllClinicTimes(3,$findClinicDetails->ClinicID,strtotime($currentdate));         
            $findClinicHolidays = General_Library::FindExistingClinicHolidays(3,$findClinicDetails->ClinicID);
            //echo '<pre>'; print_r($findClinicTimes); echo '</pre>';
            $dataArray['clinictimes'] = $findClinicTimes;
            $dataArray['clinicholidays'] = $findClinicHolidays;
            $dataArray['title'] = "Set Clinic Opening Times";
            $view = View::make('clinic.clinic-opening-times', $dataArray);     
            return $view;
        }else{
            return Redirect::to('provider-portal-login');
        }
    }
    
    
    /* Use      :   Used to process opening times
     * 
     */
    public static function ClinicOpeningTimes($clinicdata){
        StringHelper::Set_Default_Timezone();
        $currentdate = date('d-m-Y');
        $allInputs = Input::all();
        
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            if($allInputs['timerepeat']!= "false"){ $repeat =1; }else{$repeat =0; }
            $existingManageTimes = General_Library::FindExistingManageTimes(3, $findClinicDetails->ClinicID,  strtotime($currentdate));
            
            if(!$existingManageTimes){
                $dataArray['partyid'] = $findClinicDetails->ClinicID;
                //$dataArray['timerepeat'] = $repeat;
                $dataArray['timetype'] = $allInputs['timetype'];
                $dataArray['party'] = 3;
                //$dataArray['clinicid'] = $findClinicDetails->ClinicID;
                //$dataArray['doctorid'] = 0;
                $dataArray['from_date'] = strtotime(date("d-m-Y"));
                if($repeat == 0){
                    $lasttime = StringHelper::FindStopRepeatDate(date('d-m-Y'));
                    $dataArray['to_date'] = strtotime($lasttime);
                }else{
                    $dataArray['to_date'] = 0;
                }
                $dataArray['timerepeat'] = 1;
                $dataArray['to_date'] = 0;
                $addManageTimes = self::AddManageTimes($dataArray);
                if($addManageTimes){
                    if($allInputs['timetype']==0){
                        $timeopen = 0;
                        $timeclose = 0;
                    }else{
                        $timeopen = $allInputs['starttime'];
                        $timeclose = $allInputs['endtime'];
                    }
                    $weekArray['managetimeid'] = $addManageTimes;
                    $weekArray['starttime'] = $timeopen;
                    $weekArray['endtime'] = $timeclose;
                    $weekArray['wemon'] = $allInputs['wemon'];
                    $weekArray['wetus'] = $allInputs['wetus'];
                    $weekArray['wewed'] = $allInputs['wewed'];
                    $weekArray['wethu'] = $allInputs['wethu'];
                    $weekArray['wefri'] = $allInputs['wefri'];
                    $weekArray['wesat'] = $allInputs['wesat'];
                    $weekArray['wesun'] = $allInputs['wesun'];

                    $addClinicTimes = self::AddClinicTimes($weekArray);
                    if($addClinicTimes){
                        $findClinicTimes = General_Library::FindAllClinicTimes(3,$findClinicDetails->ClinicID, strtotime($currentdate));
                        $dataArray['clinictimes'] = $findClinicTimes;
                        $view = View::make('ajax.clinic.load-opening-times', $dataArray);     
                        return $view;
                    }
                    
                    return $addManageTimes;
                }else{
                    return 0;
                }
            }elseif($existingManageTimes->Type==1){
                $findActiveTimes = General_Library::FindClinicActiveTimes($existingManageTimes->ManageTimeID);
                $newStartTime = strtotime($allInputs['starttime']);
                $newEndTime = strtotime($allInputs['endtime']);
                if($findActiveTimes){
                    foreach($findActiveTimes as $existTime){
                        $startTime = strtotime($existTime->StartTime);
                        $endTime = strtotime($existTime->EndTime);  
                        if(($allInputs['wemon']==1 && $existTime->Mon==1) || ($allInputs['wetus']==1 && $existTime->Tue==1) || ($allInputs['wewed']==1 && $existTime->Wed==1) || ($allInputs['wethu']==1 && $existTime->Thu==1) || ($allInputs['wefri']==1 && $existTime->Fri==1) || ($allInputs['wesat']==1 && $existTime->Sat==1) || ($allInputs['wesun']==1 && $existTime->Sun==1)){
                            if(($startTime <= $newStartTime && $newStartTime < $endTime) || ($startTime < $newEndTime && $newEndTime <= $endTime) || ($startTime > $newStartTime && $newEndTime > $endTime)){ 
                                return 2;
                                break;
                            }
                        }     
                    }
                }

                if($allInputs['timetype']==0){
                    $timeopen = 0;
                    $timeclose = 0;
                }else{
                    $timeopen = $allInputs['starttime'];
                    $timeclose = $allInputs['endtime'];
                }
                $weekArray['managetimeid'] = $existingManageTimes->ManageTimeID;
                $weekArray['starttime'] = $timeopen;
                $weekArray['endtime'] = $timeclose;
                $weekArray['wemon'] = $allInputs['wemon'];
                $weekArray['wetus'] = $allInputs['wetus'];
                $weekArray['wewed'] = $allInputs['wewed'];
                $weekArray['wethu'] = $allInputs['wethu'];
                $weekArray['wefri'] = $allInputs['wefri'];
                $weekArray['wesat'] = $allInputs['wesat'];
                $weekArray['wesun'] = $allInputs['wesun'];

                $addClinicTimes = self::AddClinicTimes($weekArray);
                if($addClinicTimes){
                    $findClinicTimes = General_Library::FindAllClinicTimes(3,$findClinicDetails->ClinicID,strtotime($currentdate));
                    $dataArray['clinictimes'] = $findClinicTimes;
                    $view = View::make('ajax.clinic.load-opening-times', $dataArray);     
                    return $view;
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
    
    
    public static function AddManageTimes($dataArray){
        if(!empty($dataArray)){
            $managetimes = new ManageTimes();
            $insertManageTimes = $managetimes->AddManageTimes($dataArray);
            if($insertManageTimes){
                return $insertManageTimes;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }   
    }
    public static function AddClinicTimes($dataArray){
        if(!empty($dataArray)){
            $clinictimes = new ClinicTimes();
            $insertClinicTimes = $clinictimes->AddClinicTimes($dataArray);
            if($insertClinicTimes){
                return $insertClinicTimes;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }   
    }
    
    /* Use      :   Used to Delete clinic opening times and doctor availability times 
     * Access   :   AJAX
     */
    public static function ClinicDeleteOpeningTimes($clinicdata){
        StringHelper::Set_Default_Timezone();
        $currentdate = date('d-m-Y');
        $allInputs = Input::all();
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            if($allInputs['doctorid'] !=0){
                $findTimeBooking = General_Library::FindTimeBooking($allInputs['doctorid'],$allInputs['clinictimeid']);
                if($findTimeBooking){
                    return 5;
                }
            }
            $updateArray['clinictimeid'] = $allInputs['clinictimeid'];
            $updateArray['Active'] = 0;
            $updateArray['Updated_at'] = time();
            $updateClinicTimes = General_Library::UpdateClinicTimes($updateArray);
            if($updateClinicTimes){
                if($allInputs['doctorid']){
                    //$findDoctorTimes = General_Library::FindDoctorAvailabilityTimes(2,$allInputs['doctorid']);
                    $findDoctorTimes = General_Library::FindAllClinicTimes(2,$allInputs['doctorid'],strtotime($currentdate));
                    $dataArray['loadarrays']['doctortimes'] = $findDoctorTimes;
                    $view = View::make('ajax.clinic.load-doctor-availability-times', $dataArray);     
                    return $view;
                }else{
                    $findClinicTimes = General_Library::FindAllClinicTimes(3,$findClinicDetails->ClinicID, strtotime($currentdate));
                    $dataArray['clinictimes'] = $findClinicTimes;
                    $view = View::make('ajax.clinic.load-opening-times', $dataArray);     
                    return $view;
                }
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    public static function AddClinicHolidays($clinicdata){
        $allInputs = Input::all();
        
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            if($allInputs['holidaytype'] == 0){
                $fromtime = 0;
                $totime = 0;
            }else{
                $fromtime = $allInputs['timestart'];
                $totime = $allInputs['timeend'];
            }
            if($allInputs['doctorid'] !=0){
                $partyid = $allInputs['doctorid'];
                $party = 2;
            }else{
                $partyid = $findClinicDetails->ClinicID;
                $party = 3;
            }
            $dataArray['party'] = $party;
            $dataArray['partyid'] = $partyid;
            $dataArray['title'] = null;
            $dataArray['holidaytype'] = $allInputs['holidaytype'];
            $myholiday = date ("d-m-Y", strtotime($allInputs['dateholiday'])); 
            $dataArray['holiday'] = $myholiday;
            $dataArray['fromtime'] = $fromtime;
            $dataArray['totime'] = $totime;
           
            $addNewHolidays = General_Library::AddManageHolidays($dataArray);
            if($addNewHolidays){
                if($allInputs['doctorid']!= 0){
                    //return 1;
                    $findClinicHolidays = General_Library::FindExistingClinicHolidays(2,$allInputs['doctorid']);
                    $dataArray['loadarrays']['doctorholidays'] = $findClinicHolidays;
                    $view = View::make('ajax.clinic.load-doctor-holidays', $dataArray);     
                    return $view;
                }else{
                    $findClinicHolidays = General_Library::FindExistingClinicHolidays(3,$findClinicDetails->ClinicID);
                    $dataArray['clinicholidays'] = $findClinicHolidays;
                    $view = View::make('ajax.clinic.load-clinic-holidays', $dataArray);     
                    return $view;
                }
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    /* Use      :   Used to delete clinic holidays
     * 
     * 
     */
    public static function DeleteClinicHolidays($clinicdata){
        $allInputs = Input::all();
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            $updateArray['manageholidayid'] = $allInputs['holidayid'];
            $updateArray['Active'] = 0;
            $updateArray['updated_at'] = time();
            
            $updateClinicHolidays = General_Library::UpdateManageHolidays($updateArray);
            if($updateClinicHolidays){
                if($allInputs['doctorin']==1){
                    $findClinicHolidays = General_Library::FindExistingClinicHolidays(2,$allInputs['partyid']);
                    $dataArray['loadarrays']['doctorholidays'] = $findClinicHolidays;
                    $view = View::make('ajax.clinic.load-doctor-holidays', $dataArray);     
                    return $view;
                }else{
                    $findClinicHolidays = General_Library::FindExistingClinicHolidays(3,$findClinicDetails->ClinicID);
                    $dataArray['clinicholidays'] = $findClinicHolidays;
                    $view = View::make('ajax.clinic.load-clinic-holidays', $dataArray);     
                    return $view;
                }
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    
    /* Use      :   Used to set doctor availability 
     * 
     */
    public static function ClinicDoctorAvailabilityPage($clinicdata){
        StringHelper::Set_Default_Timezone();
        $currentdate = date('d-m-Y');
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            $allDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            $doctorsArray['doctors'] = $allDoctors;
            $dataArray['loadarrays'] = $doctorsArray;
            if($allDoctors){
                //echo '<pre>'; print_r($allDoctors[0]); echo '</pre>';
               // $findDoctorTimes = General_Library::FindDoctorAvailabilityTimes(2,$allDoctors[0]->DoctorID);
                $findDoctorTimes = General_Library::FindAllClinicTimes(2,$allDoctors[0]->DoctorID,  strtotime($currentdate));
                $findDoctorHolidays = General_Library::FindExistingClinicHolidays(2,$allDoctors[0]->DoctorID);
                $dataArray['loadarrays']['currentdoctor'] = $allDoctors[0];
                $dataArray['loadarrays']['doctortimes'] = $findDoctorTimes;
                $dataArray['loadarrays']['doctorholidays'] = $findDoctorHolidays;
            }else{
                $dataArray['loadarrays']['currentdoctor'] = null;
            }
            //To make Current doctor null
            $dataArray['loadarrays']['currentdoctor'] = null;
            
            $dataArray['title'] = "Set Clinic Opening Times";
            $view = View::make('clinic.doctors-availability', $dataArray);     
            return $view;
        }else{
            return Redirect::to('provider-portal-login');
        }
    }
    
    public static function LoadClinicDoctorAvailabilityPage($clinicdata){
        StringHelper::Set_Default_Timezone();
        $currentdate = date('d-m-Y');
        $allInputs = Input::all();
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            $allDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            //$dataArray['doctors'] = $allDoctors;
            
            $currentDoctorDetails = null;
            foreach($allDoctors as $doct){
                if($doct->DoctorID == $allInputs['doctorid']){
                    $currentDoctorDetails = $doct;
                }
            } 
            //$findDoctorTimes = General_Library::FindDoctorAvailabilityTimes(2,$allInputs['doctorid']);
            $findDoctorTimes = General_Library::FindAllClinicTimes(2,$allInputs['doctorid'],  strtotime($currentdate));
            $findDoctorHolidays = General_Library::FindExistingClinicHolidays(2,$allInputs['doctorid']);
            
            $doctorsArray['doctors'] = $allDoctors;
            $dataArray['loadarrays'] = $doctorsArray;
            $dataArray['loadarrays']['currentdoctor'] = $currentDoctorDetails;
            $dataArray['loadarrays']['currentdate'] = $currentdate;
            $dataArray['loadarrays']['doctortimes'] = $findDoctorTimes;
            $dataArray['loadarrays']['doctorholidays'] = $findDoctorHolidays;
            
            $view = View::make('ajax.clinic.load-doctor-availability', $dataArray);     
            return $view;
        }else{
            return 0;
        }
    }
    
    /* Use      :   Used to add doctor availability times
     * Access   :   AJAX
     */
    public static function AddDoctorAvailabilityTimes($clinicdata){
        StringHelper::Set_Default_Timezone();
        $currentdate = date('d-m-Y');
        $allInputs = Input::all();
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            if($allInputs['timerepeat']=="true"){ $repeat =1; } else {$repeat =0; }
            $existingManageTimes = General_Library::FindExistingManageTimes(2, $allInputs['doctorid'],  strtotime($currentdate));
            //print_r($existingManageTimes);
            if(!$existingManageTimes){
                if($repeat == 0){
                    $finaltime = StringHelper::FindStopRepeatDate(date('d-m-Y'));
                    $dataArray['to_date'] = strtotime($finaltime);
                }else{
                    $dataArray['to_date'] = 0;
                }
                $dataArray['partyid'] = $allInputs['doctorid'];
                //$dataArray['timerepeat'] = $repeat;
                $dataArray['timetype'] = 1;
                $dataArray['party'] = 2;
                $dataArray['from_date'] = strtotime($currentdate);
                //$dataArray['to_date'] = 0;
                //New changes
                $dataArray['timerepeat'] = 1;
                $dataArray['to_date'] = 0;

                $addManageTimes = self::AddManageTimes($dataArray);
                if($addManageTimes){
                    $weekArray['managetimeid'] = $addManageTimes;
                    $weekArray['starttime'] = $allInputs['starttime'];
                    $weekArray['endtime'] = $allInputs['endtime'];
                    $weekArray['wemon'] = $allInputs['wemon'];
                    $weekArray['wetus'] = $allInputs['wetus'];
                    $weekArray['wewed'] = $allInputs['wewed'];
                    $weekArray['wethu'] = $allInputs['wethu'];
                    $weekArray['wefri'] = $allInputs['wefri'];
                    $weekArray['wesat'] = $allInputs['wesat'];
                    $weekArray['wesun'] = $allInputs['wesun'];

                    $addClinicTimes = self::AddClinicTimes($weekArray);
                    if($addClinicTimes){
                        //$findDoctorTimes = General_Library::FindDoctorAvailabilityTimes(2,$allInputs['doctorid']);
                        $findDoctorTimes = General_Library::FindAllClinicTimes(2,$allInputs['doctorid'],strtotime($currentdate));
                        $dataArray['loadarrays']['doctortimes'] = $findDoctorTimes;
                        $dataArray['loadarrays']['currentdate'] = $currentdate;
                        $view = View::make('ajax.clinic.load-doctor-availability-times', $dataArray);     
                        return $view;
                        
                        //$findClinicTimes = General_Library::FindAllClinicTimes(3,$findClinicDetails->ClinicID);
                        //$dataArray['clinictimes'] = $findClinicTimes;
                        //$view = View::make('ajax.clinic.load-opening-times', $dataArray);     
                        //return $view;
                    }

                    //return $addManageTimes;
                }else{
                    return 0;
                }
            }else{
                $findActiveTimes = General_Library::FindClinicActiveTimes($existingManageTimes->ManageTimeID);
                $newStartTime = strtotime($allInputs['starttime']);
                $newEndTime = strtotime($allInputs['endtime']);
                if($findActiveTimes){
                    foreach($findActiveTimes as $existTime){
                        $startTime = strtotime($existTime->StartTime);
                        $endTime = strtotime($existTime->EndTime);  
                        if(($allInputs['wemon']==1 && $existTime->Mon==1) || ($allInputs['wetus']==1 && $existTime->Tue==1) || ($allInputs['wewed']==1 && $existTime->Wed==1) || ($allInputs['wethu']==1 && $existTime->Thu==1) || ($allInputs['wefri']==1 && $existTime->Fri==1) || ($allInputs['wesat']==1 && $existTime->Sat==1) || ($allInputs['wesun']==1 && $existTime->Sun==1)){
                            if(($startTime <= $newStartTime && $newStartTime < $endTime) || ($startTime < $newEndTime && $newEndTime <= $endTime) || ($startTime > $newStartTime && $newEndTime > $endTime)){ 
                                return 2;
                                break;
                            }
                        }     
                    }
                }

                $weekArray['managetimeid'] = $existingManageTimes->ManageTimeID;
                $weekArray['starttime'] = $allInputs['starttime'];
                $weekArray['endtime'] = $allInputs['endtime'];
                $weekArray['wemon'] = $allInputs['wemon'];
                $weekArray['wetus'] = $allInputs['wetus'];
                $weekArray['wewed'] = $allInputs['wewed'];
                $weekArray['wethu'] = $allInputs['wethu'];
                $weekArray['wefri'] = $allInputs['wefri'];
                $weekArray['wesat'] = $allInputs['wesat'];
                $weekArray['wesun'] = $allInputs['wesun'];

                $addClinicTimes = self::AddClinicTimes($weekArray);
                if($addClinicTimes){
                    //$findDoctorTimes = General_Library::FindDoctorAvailabilityTimes(2,$allInputs['doctorid']);
                    $findDoctorTimes = General_Library::FindAllClinicTimes(2,$allInputs['doctorid'],strtotime($currentdate));
                    $dataArray['loadarrays']['doctortimes'] = $findDoctorTimes;
                    $view = View::make('ajax.clinic.load-doctor-availability-times', $dataArray);     
                    return $view;
                        
                    //$findClinicTimes = General_Library::FindAllClinicTimes(3,$findClinicDetails->ClinicID);
                    //$dataArray['clinictimes'] = $findClinicTimes;
                    //$view = View::make('ajax.clinic.load-opening-times', $dataArray);     
                    //return $view;
                }else{
                    return 0;
                }
                
            }
            
            
            
            
            
            
            /*
            $allDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            
            $currentDoctorDetails = null;
            foreach($allDoctors as $doct){
                if($doct->DoctorID == $allInputs['doctorid']){
                    $currentDoctorDetails = $doct;
                }
            } 
            
            $doctorsArray['doctors'] = $allDoctors;
            $dataArray['loadarrays'] = $doctorsArray;
            $dataArray['loadarrays']['currentdoctor'] = $currentDoctorDetails;
            
            $view = View::make('ajax.clinic.load-doctor-availability', $dataArray);     
            return $view
             *
             */
        }else{
            return 0;
        }
    }
    
    /* Use      :   Used to manage repeat times in actions
     * Access   :   Ajax request
     */
    
    public static function RepeatTimeActions($clinicdata){
        $allInputs = Input::all();
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            if($allInputs['repeatid'] == 1){
                $timerepeat = 0;
                $findToDateNow = StringHelper::FindStopRepeatDate(date('d-m-Y'));
                $findToDate = strtotime($findToDateNow);
            }else{
                $timerepeat = 1;
                $findToDate = 0;
            } 
            
            $updateArray['To_Date'] = $findToDate;
            $updateArray['Repeat'] = $timerepeat;
            $updateArray['Status'] = $timerepeat;
            $updateArray['managetimeid'] = $allInputs['managetimeid'];
            $updateArray['updated_at'] = time();
                    
            $updateManageTimes = General_Library::UpdateManageTimes($updateArray);
            if($updateManageTimes){
                if($allInputs['repeatid'] == 1){
                    $data = '<div id="repeat-times-action" managetimeid="'.$allInputs['managetimeid'].'" repeatid="0" class="btn-update font-type-Montserrat  mar-left-2 ">Start Repeat</div>';
                }else{
                    $data ='<div id="repeat-times-action" managetimeid="'.$allInputs['managetimeid'].'" repeatid="1" class="btn-cancel font-type-Montserrat ">Stop Repeat</div>';
                } 
                return $data;
            }else{
                return 0;
            }
            
        }else{
            return 0;
        }
    }
    
    /* Use         :    Display clinic details page
     * 
     */
    public static function ClinicHomeAppointmentPage($clinicdata){
        //StringHelper::Set_Default_Timezone(); 
        $currentDate = date('d-m-Y');
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            $findAllDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            if($findAllDoctors){
                $findWeek = StringHelper::FindWeekFromDate($currentDate); 
                $findClinicAvailability = General_Library::FindCurrentDayAvailableTimes(3,$findClinicDetails->ClinicID,$findWeek, strtotime($currentDate));
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$findClinicDetails->ClinicID, $currentDate);
                if($findClinicAvailability){
                    $myclinicholiday = Array_Helper::ClinicHolidayArray($findClinicHolidays);   
                    $myclinicdata = Array_Helper::ClinicAvailabilityArray($findClinicAvailability);
                }else{
                    $myclinicdata = null;
                    $myclinicholiday = null;
                }
                $doctorlist1 = Array_Helper::DoctorDetailArray($findAllDoctors,$findWeek,$currentDate);
                // nhr 2016-2-9
                
                foreach ($doctorlist1 as $doctor) {
                    if ($doctor['available']==1) {
                        $doctorid = $doctor['doctor_id'];
                        self::syncAppointment($currentDate,$doctorid);
                      
                    }
                }

                
                $doctorlist = Array_Helper::DoctorDetailArray($findAllDoctors,$findWeek,$currentDate);
            }else{
                $doctorlist = null;
                $myclinicdata = null;
                $myclinicholiday = null;
            }
            
            $dataArray['loadarray']['currentdate'] = $currentDate;
            $dataArray['loadarray']['clinicavailability'] = $myclinicdata;
            $dataArray['loadarray']['clinicholiday'] = $myclinicholiday;
            //$dataArray['loadarray']['existingappointments'] = $allExistingAppointments;
            // dd($myclinicdata);
            $dataArray['loadarray']['doctors'] = $doctorlist;
            $dataArray['title'] = "Clinic Home Appointment View";
            $view = View::make('clinic.clinic-home-appointment', $dataArray);     
            return $view;
        }else{
            return FALSE;
        }
    }
    public static function ClinicHomeAppointmentPage1($clinicdata){
        StringHelper::Set_Default_Timezone(); 
        $currentDate = date('d-m-Y');

        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            $findAllDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            if($findAllDoctors){
                $findWeek = StringHelper::FindWeekFromDate($currentDate); 
                $findClinicAvailability = General_Library::FindCurrentDayAvailableTimes(3,$findClinicDetails->ClinicID,$findWeek, strtotime($currentDate));
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$findClinicDetails->ClinicID, $currentDate);
                //$allExistingAppointments = General_Library::FindAllExistingAppointments($allInputs['doctorid'], strtotime($currentDate));
                if($findClinicAvailability){
                    $myclinicholiday = Array_Helper::ClinicHolidayArray($findClinicHolidays);   
                    $myclinicdata = Array_Helper::ClinicAvailabilityArray($findClinicAvailability);
                }else{
                    $myclinicdata = null;
                    $myclinicholiday = null;
                }
                
                /*foreach($findAllDoctors as $findDoctor){      
                    //$findDoctorAvailability = General_Library::FindDayAvailableTime(2,$findDoctor->DoctorID,$findWeek,strtotime($currentDate));
                    $findDoctorAvailability = General_Library::FindCurrentDayAvailableTimes(2,$findDoctor->DoctorID,$findWeek,strtotime($currentDate));
                    $doctorHoliday = General_Library::FindPartyFullDayHolidays(2,$findDoctor->DoctorID,$currentDate); 
                    (!$doctorHoliday && $findDoctorAvailability) ? $activeDoctorTime = 1 : $activeDoctorTime = 0;
                    $doctorDayHolidays = General_Library::FindCurrentDayHolidays(2,$findDoctor->DoctorID,$currentDate);        
                    
                    $findDoctor->available = $activeDoctorTime;
                    $findDoctor->available_times = $findDoctorAvailability;
                    $findDoctor->holidays = $doctorDayHolidays;
                    $doctorlist[] = $findDoctor;
                }*/
                
                $doctorlist1 = Array_Helper::DoctorDetailArray($findAllDoctors,$findWeek,$currentDate);
                // nhr 2016-2-9
                
                foreach ($doctorlist1 as $doctor) {
                    if ($doctor['available']==1) {
                        $doctorid = $doctor['doctor_id'];
                        self::syncAppointment($currentDate,$doctorid);
                      
                    }
                }//end of for each
                $doctorlist = Array_Helper::DoctorDetailArray($findAllDoctors,$findWeek,$currentDate);
            }else{
                $doctorlist = null;
                $myclinicdata = null;
                $myclinicholiday = null;
            }
            
            $dataArray['loadarray']['currentdate'] = $currentDate;
            $dataArray['loadarray']['clinicavailability'] = $myclinicdata;
            $dataArray['loadarray']['clinicholiday'] = $myclinicholiday;
            //$dataArray['loadarray']['existingappointments'] = $allExistingAppointments;
            
            $dataArray['loadarray']['doctors'] = $doctorlist;
            $dataArray['title'] = "Clinic Home Appointment View";
            $view = View::make('clinic.clinic-home-appointment', $dataArray);     
            return $view;
        }else{
            return FALSE;
        }
    }
    
 
    
    
    /* Use      :   Used to open booking page
     * Access   :   Ajax request
     */
    
    public static function OpenBookingPage($clinicdata){
        $allInputs = Input::all(); 
        StringHelper::Set_Default_Timezone();
        $currentDate = date('d-m-Y');
        $currentDate = $allInputs['bookingdate'];
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            $findAllDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            if($findAllDoctors){
                $findWeek = StringHelper::FindWeekFromDate($currentDate); 
                $findClinicAvailability = General_Library::FindCurrentDayAvailableTimes(3,$findClinicDetails->ClinicID,$findWeek, strtotime($currentDate));
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$findClinicDetails->ClinicID, $currentDate);
                
                if($findClinicAvailability){
                    $myclinicholiday = Array_Helper::ClinicHolidayArray($findClinicHolidays);   
                    $myclinicdata = Array_Helper::ClinicAvailabilityArray($findClinicAvailability);
                }else{
                    $myclinicholiday = null;
                    $myclinicdata = null;
                }
                $doctorAvailability = Array_Helper::DoctorAvailabilityArray($allInputs['doctorid'],$findWeek,$currentDate);
                
                $bookingarray['loadarray']['clinicavailability'] = $myclinicdata;
                $bookingarray['loadarray']['clinicholiday'] = $myclinicholiday;
                $bookingarray['loadarray']['doctoravailability'] = $doctorAvailability;
                
            
                $doctorlist = Array_Helper::DoctorsArray($findAllDoctors);
                $doctorProcedures = Doctor_Library::FindDoctorProcedures($findClinicDetails->ClinicID,$allInputs['doctorid']);
                //$startime = date('h.i A',substr($allInputs['starttime'], 0, -1));
                $startime = date('h.i A',$allInputs['starttime']);
                
                $mydate = date('l, j F Y',strtotime($currentDate));
                $bookingarray['loadarray']['currentdate'] = $currentDate;
                $bookingarray['loadarray']['showcurrentdate'] = $mydate;
                $bookingarray['loadarray']['doctors'] = $doctorlist;
                $bookingarray['loadarray']['current_doctor'] = $allInputs['doctorid'];
                $bookingarray['loadarray']['clinictimeid'] = $allInputs['clinictimeid'];
                $bookingarray['loadarray']['start_time'] = $startime;
                //$bookingarray['loadarray']['slot_place'] = substr($allInputs['starttime'], -1);
                $bookingarray['loadarray']['doctor_procedure'] = $doctorProcedures;
                $view = View::make('ajax.clinic.subpages.load-booking-popup', $bookingarray);     
                return $view;     
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    public static function LoadBookingPopup($clinicdata){
        $allInputs = Input::all(); 
        StringHelper::Set_Default_Timezone();
        $currentDate = date('d-m-Y');
        $currentDate = $allInputs['bookingdate'];
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            $findAllDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            if($findAllDoctors){
                $findWeek = StringHelper::FindWeekFromDate($currentDate); 
                $findClinicAvailability = General_Library::FindCurrentDayAvailableTimes(3,$findClinicDetails->ClinicID,$findWeek, strtotime($currentDate));
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$findClinicDetails->ClinicID, $currentDate);
                
                if($findClinicAvailability){
                    $myclinicholiday = Array_Helper::ClinicHolidayArray($findClinicHolidays);   
                    $myclinicdata = Array_Helper::ClinicAvailabilityArray($findClinicAvailability);
                }else{
                    $myclinicholiday = null;
                    $myclinicdata = null;
                }
                $doctorAvailability = Array_Helper::DoctorAvailabilityArray($allInputs['doctorid'],$findWeek,$currentDate);
                $bookingarray['loadarray']['clinicavailability'] = $myclinicdata;
                $bookingarray['loadarray']['clinicholiday'] = $myclinicholiday;
                $bookingarray['loadarray']['doctoravailability'] = $doctorAvailability;
                
            //echo '<pre>';print_r($doctorAvailability);echo '</pre>';
                $doctorlist = Array_Helper::DoctorsArray($findAllDoctors);
                $doctorProcedures = Doctor_Library::FindDoctorProcedures($findClinicDetails->ClinicID,$allInputs['doctorid']);
                //$startime = date('h.i A',substr($allInputs['starttime'], 0, -1));
                
                
                //$startime = date('h.i A',$allInputs['starttime']);
                //$startime = 0;
                //$mydate = date('d-y-M',$currentDate);
                //echo $mydate;
                $bookingarray['loadarray']['currentdate'] = $currentDate;
                $bookingarray['loadarray']['doctors'] = $doctorlist;
                $bookingarray['loadarray']['current_doctor'] = $allInputs['doctorid'];
                //$bookingarray['loadarray']['clinictimeid'] = $allInputs['clinictimeid'];
                //$bookingarray['loadarray']['current_doctor'] = 89;
                $bookingarray['loadarray']['clinictimeid'] = 32;
                $bookingarray['loadarray']['start_time'] = $allInputs['starttime'];
                //$bookingarray['loadarray']['slot_place'] = substr($allInputs['starttime'], -1);
                $bookingarray['loadarray']['doctor_procedure'] = $doctorProcedures;
                $bookingarray['loadarray']['currentprocedure'] = $allInputs['procedureid'];
                $bookingarray['loadarray']['currentprocedureduration'] = $allInputs['duration'];
                
                $view = View::make('ajax.clinic.subpages.load-booking-popup-ajax', $bookingarray);     
                return $view;
            }else{
                
            }
        }else{
            return 0;
        }  
    }
    
    public static function ChangeProcedures($clinicdata){
        $allInputs = Input::all(); 
        StringHelper::Set_Default_Timezone();
        //$currentDate = $allInputs['bookingdate'];
        $newDate = strtotime($allInputs['bookingdate']);
        $newFormatDate = date('d-m-Y',$newDate);
        $currentDate = $newFormatDate;
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            $findAllDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            if($findAllDoctors){
                $findWeek = StringHelper::FindWeekFromDate($currentDate); 
                $findClinicAvailability = General_Library::FindCurrentDayAvailableTimes(3,$findClinicDetails->ClinicID,$findWeek, strtotime($currentDate));
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$findClinicDetails->ClinicID, $currentDate);
                
                if($findClinicAvailability){
                    $myclinicholiday = Array_Helper::ClinicHolidayArray($findClinicHolidays);   
                    $myclinicdata = Array_Helper::ClinicAvailabilityArray($findClinicAvailability);
                }else{
                    $myclinicholiday = null;
                    $myclinicdata = null;
                }
                $doctorAvailability = Array_Helper::DoctorAvailabilityArray($allInputs['doctorid'],$findWeek,$currentDate);
                $bookingarray['loadarray']['clinicavailability'] = $myclinicdata;
                $bookingarray['loadarray']['clinicholiday'] = $myclinicholiday;
                $bookingarray['loadarray']['doctoravailability'] = $doctorAvailability;
                $bookingarray['loadarray']['currentdate'] = $currentDate;
                $bookingarray['loadarray']['currentprocedure'] = $allInputs['procedureid'];
                $bookingarray['loadarray']['currentprocedureduration'] = $allInputs['duration'];
                $bookingarray['loadarray']['current_doctor'] = $allInputs['doctorid'];
                $bookingarray['loadarray']['start_time'] = $allInputs['starttime'];
                $view = View::make('ajax.clinic.subpages.load-startend-time', $bookingarray);     
                return $view;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
     
    }
    public static function ChangeStartDate($clinicdata){
        $allInputs = Input::all(); 
        StringHelper::Set_Default_Timezone();
        //$currentDate = $allInputs['bookingdate'];
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            $starttime = $allInputs['starttime'];
            $lastvalue = $allInputs['lastvalue'];
            $findLastValue = strtotime("+15 minutes", $lastvalue);
            $findEndTime = String_Helper_Web::FindEndTime($starttime,$allInputs['duration']);
            if($findEndTime > $findLastValue){
                return 0;
            }else{
                $endTimeHtml = '<option value="'.$findEndTime.'" >'.date('h:i A',$findEndTime).'</option>';
                return $endTimeHtml;
            }
            

        }else{
            return 0;
        }
    }
    
    
    
    public static function OpenBookingUpdate($clinicdata){
        StringHelper::Set_Default_Timezone();
        $allInputs = Input::all(); 
           
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            $findAllDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            if($findAllDoctors){
                $findUserAppointment = General_Library::FindUserAppointment($allInputs['bookingid']);
                if($findUserAppointment){
                    //echo $allInputs['bookingid'];die();
                    $currentDate = date('d-m-Y',$findUserAppointment->BookDate);
                    $findWeek = StringHelper::FindWeekFromDate($currentDate); 
                    $findClinicAvailability = General_Library::FindCurrentDayAvailableTimes(3,$findClinicDetails->ClinicID,$findWeek, strtotime($currentDate));
                    $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$findClinicDetails->ClinicID, $currentDate);

                    if($findClinicAvailability){
                        $myclinicholiday = Array_Helper::ClinicHolidayArray($findClinicHolidays);   
                        $myclinicdata = Array_Helper::ClinicAvailabilityArray($findClinicAvailability);
                    }else{
                        $myclinicholiday = null;
                        $myclinicdata = null;
                    }
                    $doctorAvailability = Array_Helper::DoctorAvailabilityArray($findUserAppointment->DoctorID,$findWeek,$currentDate);
                    
                    $bookingarray['loadarray']['clinicavailability'] = $myclinicdata;
                    $bookingarray['loadarray']['clinicholiday'] = $myclinicholiday;
                    $bookingarray['loadarray']['doctoravailability'] = $doctorAvailability;
                    
                    $doctorlist = Array_Helper::DoctorsArray($findAllDoctors);
                    $doctorProcedures = Doctor_Library::FindDoctorProcedures($findClinicDetails->ClinicID,$findUserAppointment->DoctorID);
                    $existPhone = preg_replace('/\s+/','',$findUserAppointment->PhoneNo);
                    $findPlusSign = substr($existPhone, 0, 1);
                    if($findPlusSign == '+'){
                        $PhoneOnly = substr($existPhone, 3);
                    }else{
                        $PhoneOnly = substr($existPhone, 2);
                    }
                
                    $bookingarray['loadarray']['bookingid'] = $findUserAppointment->UserAppoinmentID;
                    $bookingarray['loadarray']['userid'] = $findUserAppointment->UserID;
                    $bookingarray['loadarray']['username'] = $findUserAppointment->Name;
                    $bookingarray['loadarray']['useremail'] = $findUserAppointment->Email;
                    $bookingarray['loadarray']['usernric'] = $findUserAppointment->NRIC;
                    $bookingarray['loadarray']['userphone'] = $PhoneOnly;
                    $bookingarray['loadarray']['usercode'] = $findUserAppointment->PhoneCode;
                    $bookingarray['loadarray']['userremarks'] = $findUserAppointment->Remarks;
                    $bookingarray['loadarray']['currentdate'] = $currentDate;
                    $bookingarray['loadarray']['current_doctor'] = $findUserAppointment->DoctorID;
                    $bookingarray['loadarray']['current_procedure'] = $findUserAppointment->ProcedureID;
                    $bookingarray['loadarray']['clinictimeid'] = $findUserAppointment->ClinicTimeID;
                    $bookingarray['loadarray']['start_time'] = $findUserAppointment->StartTime;
                    $bookingarray['loadarray']['end_time'] = $findUserAppointment->EndTime;
                    //$bookingarray['loadarray']['slot_place'] = substr($allInputs['starttime'], -1);
                    $bookingarray['loadarray']['doctors'] = $doctorlist;
                    $bookingarray['loadarray']['doctor_procedure'] = $doctorProcedures;
                    $view = View::make('ajax.clinic.subpages.load-booking-popup-update', $bookingarray);     
                    return $view;
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

    

    public static function LoadDoctorProcedures($clinicdata){
        $allInputs = Input::all();
       
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            $doctorProcedures = Doctor_Library::FindDoctorProcedures($findClinicDetails->ClinicID,$allInputs['doctorid']);
            if($doctorProcedures){
                $dataArray['loadarray']['doctor_procedure'] = $doctorProcedures;
                $view = View::make('ajax.clinic.subpages.load-procedurelist', $dataArray);     
                return $view;
            }else{
                $return = '<select id="doctor-procedures">
                    <option value="" >Select a Procedure</option>
                </select>';
                return $return;
            }
        }else{
            return 0;
        }
    }
    
    /* Use      :   Used to make new appointment by Clinic
     * 
     */
    public static function NewClinicAppointment($clinicdata){
        $allInputs = Input::all();
        StringHelper::Set_Default_Timezone();

        //$currentDate = date('d-m-Y');
        $newDate = strtotime($allInputs['bookdate']);
        $newFormatDate = date('d-m-Y',$newDate);
        $currentDate = $newFormatDate;

        $userexistStatus = 0;
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            $findPlusSign = substr($allInputs['phone'], 0, 1);
            if($findPlusSign == 0){
                $PhoneOnly = $allInputs['code'].substr($allInputs['phone'], 1);
            }else{
                $PhoneOnly = $allInputs['code'].$allInputs['phone'];
            }
            
            //$findUser = Auth_Library::FindRealUser($allInputs['nric'],$allInputs['email']);
            $findUser = Auth_Library::FindUserEmail($allInputs['email']);
            if($findUser){
                //$userid = $findUser->UserID;
                $userid = $findUser;
                $userexistStatus = 1;
            }else{
                
                $userData['name'] = $allInputs['name'];
                $userData['usertype'] = 1;
                $userData['email'] = $allInputs['email'];
                $userData['nric'] = $allInputs['nric'];
                $userData['code'] = $allInputs['code'];  
                $userData['mobile'] = $PhoneOnly;
                $userData['ref_id'] = 0; 
                $userData['activelink'] = null; 
                $userData['status'] = 0;  
                $newuser = Auth_Library::AddNewUser($userData);
                if($newuser){
                    $userid = $newuser;
                }else{
                    return 0;
                }
            }
            
               $bookingtime = $allInputs['endtime'] - $allInputs['starttime'];
               $slottime = abs($bookingtime)/60;
               
                $starttime = date('h:i A',$allInputs['starttime']);
                $endtime = date('h:i A',$allInputs['endtime']);
                $startTimeNow = strtotime($currentDate.$starttime);
                $endTimeNow = strtotime($currentDate.$endtime);
          
                $starttime = date('h:i A',$allInputs['starttime']);
                $endtime = date('h:i A',$allInputs['endtime']);
                $startTimeNow = strtotime($currentDate.$starttime);
                $endTimeNow = strtotime($currentDate.$endtime);
                        
            //$existingAppointment = General_Library::FindExistingAppointment($allInputs['doctorid'],strtotime($allInputs['bookdate']),$allInputs['starttime'],$allInputs['endtime']);
            $existingAppointment = General_Library::FindExistingAppointment($allInputs['doctorid'],strtotime($currentDate));
            
            $activeAppointment = 0;
            if($existingAppointment){
                foreach($existingAppointment as $appointExist){
                    if($activeAppointment ==0){
                        if(($appointExist->StartTime <= $startTimeNow && $appointExist->EndTime > $startTimeNow) || ($appointExist->StartTime < $endTimeNow && $appointExist->EndTime >= $endTimeNow)){
                        //if(($appointExist->StartTime <= $allInputs['starttime'] && $appointExist->EndTime > $allInputs['starttime']) || ($appointExist->StartTime < $allInputs['endtime'] && $appointExist->EndTime >= $allInputs['endtime'])){    
                            $activeAppointment = 1;
                        }
                    }    
                }
            }

            if($activeAppointment==1 || ($activeAppointment !=1 && $slottime != $allInputs['duration'])){
                return 1;
            }
            //return 1;
            //$starttime = date('h:i A',$allInputs['starttime']);
            //$endtime = date('h:i A',$allInputs['endtime']);
            $bookArray['userid'] = $userid;
            $bookArray['clinictimeid'] = $allInputs['clinictimeid'];
            $bookArray['doctorid'] = $allInputs['doctorid'];
            $bookArray['procedureid'] = $allInputs['procedureid'];
            //$bookArray['starttime'] = $allInputs['starttime'];
            //$bookArray['endtime'] = $allInputs['endtime'];
            $bookArray['starttime'] = strtotime($currentDate.$starttime);
            $bookArray['endtime'] = strtotime($currentDate.$endtime);
                //$bookArray['slotplace'] = $allInputs['slotplace'];
            $bookArray['remarks'] = $allInputs['remarks'];
            $bookArray['bookdate'] = strtotime($currentDate);
            $bookArray['mediatype'] = 1;
            $bookArray['patient']=$allInputs['name'];
            $newBooking = General_Library::NewAppointment($bookArray);
            if($newBooking){
                $findUserDetails = Auth_Library::FindUserDetails($userid);
                $findDoctorDetails = Doctor_Library::FindDoctorDetails($allInputs['doctorid']);
                $findClinicProcedure = General_Library::FindClinicProcedure($allInputs['procedureid']);
                //Update User Details 
                if($userexistStatus==1){
                    $userupdate['userid'] = $findUserDetails->UserID;
                    $userupdate['Name'] = $allInputs['name'];
                    $userupdate['PhoneCode'] = $allInputs['code'];
                    $userupdate['PhoneNo'] = $PhoneOnly;
                    Auth_Library::UpdateUsers($userupdate);
                }
                //Send SMS
                if(StringHelper::Deployment()==1){
                    if(strlen($findUserDetails->PhoneNo) > 8) {
                        $smsMessage = "Hello ".$findUserDetails->Name." your booking with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name." is confirmed on ".$currentDate." from ".date('h:i A',$allInputs['starttime'])." to ".date('h:i A',$allInputs['endtime']).". Thank you for using Mednefits. Get the free app at mednefits.com";
                        //$smsMessage = "Hello ".$findUserDetails->Name." your booking with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name." is confirmed on ".$allInputs['bookdate'].". Thank you for using medicloud.";
                        $sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
                        $saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $findUserDetails->Name, $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage);
                    }
                }
                
                if($findClinicProcedure){
                    $procedurename = $findClinicProcedure->Name;
                }else{
                    $procedurename = null;
                }
                //Send Email User
                $formatDate = date('l, j F Y',strtotime($currentDate));
                $emailDdata['bookingid'] = $newBooking;
                $emailDdata['remarks'] = $allInputs['remarks'];
                $emailDdata['bookingTime'] = date('h:i A',$allInputs['starttime']).' - '.date('h:i A',$allInputs['endtime']);
                $emailDdata['bookingNo'] = 0;
                $emailDdata['bookingDate'] = $formatDate; 
                $emailDdata['doctorName'] = $findDoctorDetails->Name;
                $emailDdata['doctorSpeciality'] = $findDoctorDetails->Specialty;
                $emailDdata['clinicName'] = $findClinicDetails->Name;
                $emailDdata['clinicAddress'] = $findClinicDetails->Address;
                $emailDdata['clinicProcedure'] = $procedurename; 
                $emailDdata['emailName']= $findUserDetails->Name;
                $emailDdata['emailPage']= 'email-templates.booking';
                $emailDdata['emailTo']= $findUserDetails->Email;
                $emailDdata['emailSubject'] = 'Booking Confirmed';
                EmailHelper::sendEmail($emailDdata);
                //copy to company
                $emailDdata['emailTo']= Config::get('config.booking_email');
                EmailHelper::sendEmail($emailDdata);
                //Send email to Doctor
                $emailDdata['emailPage']= 'email-templates.booking-doctor';
                $emailDdata['emailTo']= $findDoctorDetails->Email;
                EmailHelper::sendEmail($emailDdata);
                //Send email to Clinic
                $emailDdata['emailPage']= 'email-templates.booking-doctor';
                $emailDdata['emailTo']= $clinicdata->Email;
                EmailHelper::sendEmail($emailDdata);
                
                $event_id = self::insertGoogleCalenderAppointment($bookArray,$findDoctorDetails); //nhr
                $ua = new UserAppoinment();
                $ua->updateUserAppointment(array('event_type'=>0,'Gc_event_id'=>$event_id),$newBooking);

                return $newBooking;


            }else{
                return 0; 
            }
        }else{
            return 0;
        }
    }
    
    public static function LoadDoctorsAppointmentView($clinicdata){
        $allInputs = Input::all();
        //$view = View::make('ajax.clinic.subpages.load-slot-section');     
        //return $view;
        
        StringHelper::Set_Default_Timezone();
        //$currentDate = $allInputs['bookdate'];
        $mydateformat = strtotime($allInputs['bookdate']);
        $newformatdate = date('d-m-Y',$mydateformat);
        $currentDate = $newformatdate;
        
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            $findAllDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            if($findAllDoctors){
                $findWeek = StringHelper::FindWeekFromDate($currentDate); 
                $findClinicAvailability = General_Library::FindCurrentDayAvailableTimes(3,$findClinicDetails->ClinicID,$findWeek, strtotime($currentDate));
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$findClinicDetails->ClinicID, $currentDate);
                //$allExistingAppointments = General_Library::FindAllExistingAppointments($allInputs['doctorid'], strtotime($currentDate));
                if($findClinicAvailability){
                    $myclinicholiday = Array_Helper::ClinicHolidayArray($findClinicHolidays);   
                    $myclinicdata = Array_Helper::ClinicAvailabilityArray($findClinicAvailability);
                }else{
                    $myclinicdata = null;
                    $myclinicholiday = null;
                }
                
                $doctorlist1 = Array_Helper::DoctorDetailArray($findAllDoctors,$findWeek,$currentDate);
                // nhr 2016-2-9
                
                foreach ($doctorlist1 as $doctor) {
                    if ($doctor['available']==1) {
                        $doctorid = $doctor['doctor_id'];
                        self::syncAppointment($currentDate,$doctorid);
                      
                    }
                }
                
                $doctorlist = Array_Helper::DoctorDetailArray($findAllDoctors,$findWeek,$currentDate);
            }else{
                $doctorlist = null;
                $myclinicdata = null;
                $myclinicholiday = null;
            }
            
            $dataArray['loadarray']['currentdate'] = $currentDate;
            $dataArray['loadarray']['clinicavailability'] = $myclinicdata;
            $dataArray['loadarray']['clinicholiday'] = $myclinicholiday;
            //$dataArray['loadarray']['existingappointments'] = $allExistingAppointments;
            
            $dataArray['loadarray']['doctors'] = $doctorlist;
            $dataArray['title'] = "Clinic Home Appointment View";
            //print_r($dataArray);
            $view = View::make('ajax.clinic.subpages.load-slot-section', $dataArray);     
            return $view;
        }else{
            return 0;
        }
        
        
        
        
        /*
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){
            $doctorProcedures = Doctor_Library::FindDoctorProcedures($findClinicDetails->ClinicID,$allInputs['doctorid']);
            if($doctorProcedures){
                $dataArray['loadarray']['doctor_procedure'] = $doctorProcedures;
                $view = View::make('ajax.clinic.subpages.load-procedurelist', $dataArray);     
                return $view;
            }else{
                $return = '<select id="doctor-procedures">
                    <option value="" >Select a Procedure</option>
                </select>';
                return $return;
            }
        }else{
            return 0;
        }
        */
    }
    public static function LoadDoctorsSelectionView($clinicdata){
        $allInputs = Input::all();
        $doctorlist = explode(",", $allInputs['doctorid']);
        
        StringHelper::Set_Default_Timezone();
        if($allInputs['currentdate'] ==0){
            $currentDate = date('d-m-Y');
        }else{
            $currentDate = $allInputs['currentdate'];
        }
        
        
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            //$findAllDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            $findAllDoctors = self::FindClinicDoctorSelection($clinicdata->Ref_ID,$doctorlist);
            if($findAllDoctors){
                $findWeek = StringHelper::FindWeekFromDate($currentDate); 
                $findClinicAvailability = General_Library::FindCurrentDayAvailableTimes(3,$findClinicDetails->ClinicID,$findWeek, strtotime($currentDate));
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$findClinicDetails->ClinicID, $currentDate);
                //$allExistingAppointments = General_Library::FindAllExistingAppointments($allInputs['doctorid'], strtotime($currentDate));
                if($findClinicAvailability){
                    $myclinicholiday = Array_Helper::ClinicHolidayArray($findClinicHolidays);   
                    $myclinicdata = Array_Helper::ClinicAvailabilityArray($findClinicAvailability);
                }else{
                    $myclinicholiday = null;
                    $myclinicdata = null;
                }
                
                $doctorlist1 = Array_Helper::DoctorDetailArray($findAllDoctors,$findWeek,$currentDate);
                // nhr 2016-2-9
                
                foreach ($doctorlist1 as $doctor) {
                    if ($doctor['available']==1) {
                        $doctorid = $doctor['doctor_id'];
                        self::syncAppointment($currentDate,$doctorid);
                      
                    }
                }
                
                $doctorlist = Array_Helper::DoctorDetailArray($findAllDoctors,$findWeek,$currentDate);
            }else{
                $doctorlist = null;
                $myclinicdata = null;
                $myclinicholiday = null;
            } 
            $dataArray['loadarray']['currentdate'] = $currentDate;
            $dataArray['loadarray']['clinicavailability'] = $myclinicdata;
            $dataArray['loadarray']['clinicholiday'] = $myclinicholiday;
            //$dataArray['loadarray']['existingappointments'] = $allExistingAppointments;
            
            $dataArray['loadarray']['doctors'] = $doctorlist;
            $dataArray['title'] = "Clinic Home Appointment View";
            //print_r($dataArray);
            //$view = View::make('ajax.clinic.subpages.load-slot-section', $dataArray); 
            $view = View::make('ajax.clinic.subpages.load-doctorslot-selections', $dataArray);  
            return $view;
        }else{
            return 0;
        }
    }
    
    /* Use      :   This is individual doctor appointment page
     * 
     */
    public static function DoctorAppointmentPage($clinicdata,$doctorid){
        StringHelper::Set_Default_Timezone();
        $currentDate = date('d-m-Y');
        
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            $findAllDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            if($findAllDoctors){
                $findWeek = StringHelper::FindWeekFromDate($currentDate); 
                $days7 = date('d-m-Y', strtotime($currentDate.' +6 day'));
                //echo $days7;
                /*$findClinicAvailability = General_Library::FindCurrentDayAvailableTimes(3,$findClinicDetails->ClinicID,$findWeek, strtotime($currentDate));
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$findClinicDetails->ClinicID, $currentDate);

                if($findClinicAvailability){
                    $myclinicholiday = Array_Helper::ClinicHolidayArray($findClinicHolidays);   
                    $myclinicdata = Array_Helper::ClinicAvailabilityArray($findClinicAvailability);
                }else{
                    $myclinicdata = null;
                }*/
                
                
                //$clinicDays7Holidays = General_Library::FindClinic7DayHolidays(3,$findClinicDetails->ClinicID, $currentDate,$days7);
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$findClinicDetails->ClinicID, $currentDate);
                
                //$entireClinicAvailablity = General_Library::FindEntireClinicAvailablity(3,$findClinicDetails->ClinicID, strtotime($currentDate),  strtotime($days7));
                $entireClinicAvailablity = General_Library::FindLimitClinicAvailablity(3,$findClinicDetails->ClinicID, strtotime($currentDate));             
                $doctorlist = Array_Helper::DoctorStatusArray($findAllDoctors,$findWeek,$currentDate);
                
            }else{
                $doctorlist = null;
                //$myclinicdata = null;
                //$myclinicholiday = null;
            }

            // nhr
                self::syncAppointment($currentDate,$doctorid);
            //echo '<pre>'; print_r($entireClinicAvailablity); echo '</pre>';
            
            $dataArray['loadarray']['currentdate'] = $currentDate;
            $dataArray['loadarray']['currentdoctor'] = $doctorid;
            $dataArray['loadarray']['clinicid'] = $findClinicDetails->ClinicID;
            $dataArray['loadarray']['doctors'] = $doctorlist;
            $dataArray['loadarray']['clinic_availability'] = $entireClinicAvailablity;
            //$dataArray['loadarray']['doctor_holidays'] = 1;
            $dataArray['title']= 'Clinic doctor appointment page';
            // $dataArray['loadarray']['googleevents'] =$googleresult;
            //echo '<pre>'; print_r($doctorlist); echo '</pre>';
            $view = View::make('clinic.clinic-doctor-appointment', $dataArray);  
            return $view;
        }else{
            return FALSE;
        }
    }
    
    public static function LoadSingleDoctorView($clinicdata){
        $allInputs = Input::all();
        $doctorid = $allInputs['doctorid'];
        StringHelper::Set_Default_Timezone();
        $currentDate = $allInputs['currentdate'];
        ////nhr////////////
            self::syncAppointment($currentDate,$doctorid);

        //////////////////
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            $findAllDoctors = self::FindAllClinicDoctors($findClinicDetails->ClinicID);
            if($findAllDoctors){
                $findWeek = StringHelper::FindWeekFromDate($currentDate); 
                $days7 = date('d-m-Y', strtotime($currentDate.' +6 day'));
                
                
                //$clinicDays7Holidays = General_Library::FindClinic7DayHolidays(3,$findClinicDetails->ClinicID, $currentDate,$days7);
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$findClinicDetails->ClinicID, $currentDate);
                
                //$entireClinicAvailablity = General_Library::FindEntireClinicAvailablity(3,$findClinicDetails->ClinicID, strtotime($currentDate),  strtotime($days7));
                $entireClinicAvailablity = General_Library::FindLimitClinicAvailablity(3,$findClinicDetails->ClinicID, strtotime($currentDate));
                $doctorlist = Array_Helper::DoctorStatusArray($findAllDoctors,$findWeek,$currentDate);
                
            }else{
                $doctorlist = null;
                $myclinicdata = null;
                $myclinicholiday = null;
            }
            
            //echo '<pre>'; print_r($entireClinicAvailablity); echo '</pre>';
            
            $dataArray['loadarray']['currentdate'] = $currentDate;
            $dataArray['loadarray']['currentdoctor'] = $allInputs['doctorid'];
            $dataArray['loadarray']['clinicid'] = $findClinicDetails->ClinicID;
            $dataArray['loadarray']['doctors'] = $doctorlist;
            $dataArray['loadarray']['clinic_availability'] = $entireClinicAvailablity;
            //$dataArray['loadarray']['doctor_holidays'] = 1;
            $dataArray['title']= 'Clinic doctor appointment page';
            
            //echo '<pre>'; print_r($doctorlist); echo '</pre>';
            $view = View::make('ajax.clinic.subpages.load-singledoctor-appointment', $dataArray);  
            return $view;
        }else{
            return 0;
        }
    }
    
    /* Use      :   Used to make Update appointment by Clinic
     * 
     */
    public static function UpdateClinicAppointmentFromReserve($clinicdata){ 
        // return Input::all();
        $allInputs = Input::all();
        StringHelper::Set_Default_Timezone();
        $duration = $allInputs['duration'];
        $stime = strtotime($allInputs['starttime']);
        $etime = $stime+($duration*60);
        $userid = $allInputs['userid'];
        //$currentDate = date('d-m-Y');
        $newDate = strtotime($allInputs['bookdate']);
        $newFormatDate = date('d-m-Y',$newDate);
        $currentDate = $newFormatDate;

        $userexistStatus = 0;
        $findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
        $findUserAppointment = General_Library::FindUserAppointment($allInputs['bookingid']);
        if($findClinicDetails && $findUserAppointment){

            $findPlusSign = substr($allInputs['phone'], 0, 1);
            if($findPlusSign == 0){
                $PhoneOnly = $allInputs['code'].substr($allInputs['phone'], 1);
            }else{
                $PhoneOnly = $allInputs['code'].$allInputs['phone'];
            }

            


            $bookingtime = $etime - $stime;
            $slottime = abs($bookingtime)/60;

            $starttime = date('h:i A',$stime);
            $endtime = date('h:i A',$etime);
            $startTimeNow = strtotime($currentDate.$starttime);
            $endTimeNow = strtotime($currentDate.$endtime);

            $starttime = date('h:i A',$stime);
            $endtime = date('h:i A',$etime);
            $startTimeNow = strtotime($currentDate.$starttime);
            $endTimeNow = strtotime($currentDate.$endtime);


                $currentdate = date("d-m-Y",strtotime($newFormatDate));

               $findAppointments = General_Library_Mobile::FindTodayAppointments($allInputs['doctorid'],strtotime($currentdate));
               $findExtraEvents = General_Library_Mobile::FindTodayExtraEvents($allInputs['doctorid'],strtotime($currentdate));
                $activeAvailability=0;
               if ($findAppointments || $findExtraEvents) {
                   if($findAppointments){
                        foreach($findAppointments as $todayAppoint){

                            if( (( $startTimeNow>=$todayAppoint->StartTime && $startTimeNow<$todayAppoint->EndTime) ||
                                ( $endTimeNow >$todayAppoint->StartTime && $endTimeNow<=$todayAppoint->EndTime) ||
                                ( $startTimeNow<= $todayAppoint->StartTime && $endTimeNow>=$todayAppoint->EndTime)) && $todayAppoint->UserAppoinmentID!=$allInputs['bookingid']){

                                $activeAvailability=1;
                                break;

                            }

                        }
                    }

                    if($findExtraEvents){
                        foreach($findExtraEvents as $todayAppoint1){

                            if( ( $startTimeNow>=$todayAppoint1->start_time && $startTimeNow<$todayAppoint1->end_time) ||
                                ( $endTimeNow >$todayAppoint1->start_time && $endTimeNow<=$todayAppoint1->end_time) ||
                                ( $startTimeNow<= $todayAppoint1->start_time && $endTimeNow>=$todayAppoint1->end_time)){

                                $activeAvailability=1;
                                break;

                            }

                        }
                    }


                }

// check clinic tome off

                $toff = new ManageHolidays();
                $off = $toff->FindTodayClinicTimeOFF($clinicdata->Ref_ID, $newFormatDate);

                if($off){
                    $s_time = strtotime($starttime);
                    $e_time = strtotime($endtime);
                    foreach ($off as $v) {
                        if ($v->Type==0) { //full day off
                            return 2;
                        } else {
                            $ostime = strtotime($v->From_Time);
                            $oetime = strtotime($v->To_Time);

                            if (($s_time>=$ostime&&$s_time<$oetime) || $e_time>$ostime&&$e_time<=$oetime) {
                                return 2; //block on clinic break
                            }

                        }

                    }

                }

              //chek clininc breaks

            $findWeek = StringHelper::FindWeekFromDate($currentdate);//Mon
            $brk = new ExtraEvents();
            $breaks = $brk->findClinicBreaks($findWeek, $clinicdata->Ref_ID);

            if($breaks){
                $s_time = strtotime($starttime);
                $e_time = strtotime($endtime);

                foreach ($breaks as $k) {
                    $bstime = strtotime($k->start_time);
                    $betime = strtotime($k->end_time);

                    if (($s_time>=$bstime&&$s_time<$betime) || $e_time>$bstime&&$e_time<=$betime) {
                        return 2; //block on clinic break
                    }


                }
            }


             // if ($activeAvailability==1) {
             //       return 0;
             //   }


                // $bookArray['userid'] = $findUserAppointment;
                $bookArray['clinictimeid'] = 0;//allInputs['clinictimeid'];
                $bookArray['doctorid'] = $allInputs['doctorid'];
                $bookArray['procedureid'] = $allInputs['procedureid'];
                $bookArray['starttime'] = strtotime($currentDate.$starttime);
                $bookArray['endtime'] = strtotime($currentDate.$endtime);
                $bookArray['remarks'] = $allInputs['remarks'];
                $bookArray['bookdate'] = strtotime($currentDate);
                // $bookArray['mediatype'] = 1;
                // $bookArray['patient']=$allInputs['name'];
                $bookArray['price']=$allInputs['price'];
                $bookArray['duration']=$allInputs['duration'];
                $updateAppointment = General_Library::UpdateAppointment($bookArray,$findUserAppointment->UserAppoinmentID);

                $bookArray['patient'] = 'Mednefits User';

                if($updateAppointment){

                    $findUserDetails = Auth_Library::FindUserDetails($userid);
                    $findDoctorDetails = Doctor_Library::FindDoctorDetails($allInputs['doctorid']);
                    $findClinicProcedure = General_Library::FindClinicProcedure($allInputs['procedureid']);

                    //Send SMS
                    if(StringHelper::Deployment()==1){
                        $smsMessage = "Hello ".$findUserDetails->Name." your booking with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name." is updated on ".$currentDate." from ".date('h:i A',$stime)." to ".date('h:i A',$etime).". Thank you for using Mednefits.";
                        //$smsMessage = "Hello ".$findUserDetails->Name." your booking with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name." is confirmed on ".$allInputs['bookdate'].". Thank you for using medicloud.";
                       $sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
                       $saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, 'Mednefits User', $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage);
                    }

                    if($findClinicProcedure){
                        $procedurename = $findClinicProcedure->Name;
                    }else{
                        $procedurename = null;
                    }

                    $formatDate = date('l, j F Y',strtotime($currentDate));
                    $emailDdata['bookingid'] = $allInputs['bookingid'];
                    $emailDdata['remarks'] = $allInputs['remarks'];
                    $emailDdata['bookingTime'] = date('h:i A',$stime).' - '.date('h:i A',$etime);
                    $emailDdata['bookingNo'] = 0;
                    $emailDdata['bookingDate'] = $formatDate;
                    $emailDdata['doctorName'] = $findDoctorDetails->Name;
                    $emailDdata['doctorSpeciality'] = $findDoctorDetails->Specialty;
                    $emailDdata['clinicName'] = $findClinicDetails->Name;
                    $emailDdata['clinicPhoneCode'] = $findClinicDetails->Phone_Code;
                    $emailDdata['clinicPhone'] = $findClinicDetails->Phone;
                    $emailDdata['clinicAddress'] = $findClinicDetails->Address;
                    $emailDdata['clinicProcedure'] = $procedurename;
                    $emailDdata['emailName']= $findUserDetails->Name;
                    $emailDdata['emailPhone']= $findUserDetails->PhoneNo;
                    $emailDdata['emailPage']= 'email-templates.booking';
                    $emailDdata['emailTo']= $findUserDetails->Email;
                    $emailDdata['emailSubject'] = 'Booking Confirmed';
                   if(StringHelper::Deployment()==1){
                        EmailHelper::sendEmail($emailDdata);
                    }
                    //copy to company
                    $emailDdata['emailTo']= Config::get('config.booking_email');
                    if(StringHelper::Deployment()==1){
                        EmailHelper::sendEmail($emailDdata);
                    }
                    //Send email to Doctor
                    $emailDdata['emailPage']= 'email-templates.booking-doctor';
                    $emailDdata['emailTo']= $findDoctorDetails->Email;
                    if(StringHelper::Deployment()==1){
                        EmailHelper::sendEmail($emailDdata);
                    }
                    //Send email to Clinic
                    $emailDdata['emailPage']= 'email-templates.booking';
                    $emailDdata['emailTo']= $clinicdata->Email;
                    if(StringHelper::Deployment()==1){
                        EmailHelper::sendEmail($emailDdata);
                    }

                     $app = new UserAppoinment();
                     $value = $app->getAppointment($allInputs['bookingid']);

                     if($value->Gc_event_id !=null){
                        $gc = new GoogleCalenderController();
                        try {
                        $gc->removeEvent($value->DoctorID,$value->Gc_event_id);

                        } catch (Exception $e) {}
                     }

                    $event_id = Clinic_Library::insertGoogleCalenderAppointment($bookArray,$findDoctorDetails); //nhr
                    $ua = new UserAppoinment();
                    $ua->updateUserAppointment(array('event_type'=>4,'Gc_event_id'=>$event_id),$allInputs['bookingid']);
                    $transaction = new Transaction( );
                    $transaction->checkAppointmentUpdateTransaction($clinicdata->Ref_ID, $userid, $allInputs['bookingid'], $allInputs['procedureid'], $allInputs['doctorid']);
                    return $updateAppointment;

                } else {
                    return 0;
                }

        }else {
            return 0;
        }
    }
    public static function UpdateClinicAppointment($clinicdata){ 
        $allInputs = Input::all();
        //StringHelper::Set_Default_Timezone();
        //$currentDate = date('d-m-Y');

        $newDate = strtotime($allInputs['bookdate']);
        $newFormatDate = date('d-m-Y',$newDate);
        $currentDate = $newFormatDate;
        

        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        $findUserAppointment = General_Library::FindUserAppointment($allInputs['bookingid']);
        if($findClinicDetails && $findUserAppointment){
            $starttime = date('h:i A',$allInputs['starttime']);
            $endtime = date('h:i A',$allInputs['endtime']);
            $mainStartTime = strtotime($currentDate.$starttime);
            $mainEndTime = strtotime($currentDate.$endtime);
            
            $bookingtime = $allInputs['endtime'] - $allInputs['starttime'];
            $slottime = abs($bookingtime)/60;
            
            $existingAppointment = General_Library::FindExistingAppointment($allInputs['doctorid'],strtotime($currentDate));
            
            $activeAppointment = 0;
            if($existingAppointment){
                foreach($existingAppointment as $appointExist){
                    //if($activeAppointment ==0){
                    if($activeAppointment ==0 && $appointExist->UserAppoinmentID != $findUserAppointment->UserAppoinmentID){    
                        //if(($appointExist->StartTime <= $allInputs['starttime'] && $appointExist->EndTime > $allInputs['starttime']) || ($appointExist->StartTime < $allInputs['endtime'] && $appointExist->EndTime >= $allInputs['endtime'])){
                        if(($appointExist->StartTime <= $mainStartTime && $appointExist->EndTime > $mainStartTime) || ($appointExist->StartTime < $mainEndTime && $appointExist->EndTime >= $mainEndTime)){    
                            $activeAppointment = 1;
                            break;
                        }
                    }    
                }
            }
            
            if($activeAppointment==1 || ($activeAppointment !=1 && $slottime != $allInputs['duration'])){
                return 1;
            }
            //$starttime = date('h:i A',$allInputs['starttime']);
            //$endtime = date('h:i A',$allInputs['endtime']);
            //$bookArray['userid'] = $userid;
            //$bookArray['clinictimeid'] = $allInputs['clinictimeid'];
            $bookArray['DoctorID'] = $allInputs['doctorid'];
            $bookArray['ProcedureID'] = $allInputs['procedureid'];
            $bookArray['StartTime'] = $mainStartTime;
            $bookArray['EndTime'] = $mainEndTime;
            $bookArray['Remarks'] = $allInputs['remarks'];
            $bookArray['BookDate'] = strtotime($currentDate);
            $bookArray['MediaType'] = 1;
            
            $updateAppointment = General_Library::UpdateAppointment($bookArray,$findUserAppointment->UserAppoinmentID);
            if($updateAppointment){
                $findPlusSign = substr($allInputs['phone'], 0, 1);
                if($findPlusSign == 0){
                    $PhoneOnly = $allInputs['code'].substr($allInputs['phone'], 1);
                }else{
                    $PhoneOnly = $allInputs['code'].$allInputs['phone'];
                }
                $userupdate['userid'] = $findUserAppointment->UserID;
                $userupdate['Name'] = $allInputs['name'] ? $allInputs['name'] : 'Mednefits User';
                $userupdate['PhoneCode'] = $allInputs['code'];
                $userupdate['PhoneNo'] = $PhoneOnly;
                Auth_Library::UpdateUsers($userupdate);
                
                $findClinicProcedure = General_Library::FindClinicProcedure($allInputs['procedureid']);
                if($findClinicProcedure){
                    $procedurename = $findClinicProcedure->Name;
                }else{
                    $procedurename = null;
                }
                $findUserDetails = Auth_Library::FindUserDetails($findUserAppointment->UserID);
                $findDoctorDetails = Doctor_Library::FindDoctorDetails($allInputs['doctorid']);
                //Send Email 
                $formatDate = date('l, j F Y',strtotime($currentDate));
                $emailDdata['bookingid'] = $findUserAppointment->UserAppoinmentID;
                $emailDdata['remarks'] = $allInputs['remarks'];
                $emailDdata['bookingTime'] = date('h:i A',$allInputs['starttime']).' - '.date('h:i A',$allInputs['endtime']);
                $emailDdata['bookingNo'] = 0;
                $emailDdata['bookingDate'] = $formatDate; 
                $emailDdata['doctorName'] = $findDoctorDetails->Name;
                $emailDdata['doctorSpeciality'] = $findDoctorDetails->Specialty;
                $emailDdata['clinicName'] = $findClinicDetails->Name;
                $emailDdata['clinicAddress'] = $findClinicDetails->Address;
                $emailDdata['clinicProcedure'] = $procedurename; 
                $emailDdata['emailName']= $findUserDetails->Name;
                $emailDdata['emailPage']= 'email-templates.booking';
                $emailDdata['emailTo']= $findUserDetails->Email;
                $emailDdata['emailSubject'] = 'Booking Confirmed';
                EmailHelper::sendEmail($emailDdata);

                //Copy to company

                $emailDdata['emailTo']= Config::get('config.booking_email');
                EmailHelper::sendEmail($emailDdata);
                //Email to Doctor 
                $emailDdata['emailPage']= 'email-templates.booking-doctor';
                $emailDdata['emailTo']= $findDoctorDetails->Email;
                EmailHelper::sendEmail($emailDdata);
                //Email to Clinic
                $emailDdata['emailPage']= 'email-templates.booking-doctor';
                $emailDdata['emailTo']= $clinicdata->Email;
                EmailHelper::sendEmail($emailDdata);
                return 5;
            }else{
                $findPlusSign = substr($allInputs['phone'], 0, 1);
                if($findPlusSign == 0){
                    $PhoneOnly = $allInputs['code'].substr($allInputs['phone'], 1);
                }else{
                    $PhoneOnly = $allInputs['code'].$allInputs['phone'];
                }
                $userupdate['userid'] = $findUserAppointment->UserID;
                $userupdate['Name'] = $allInputs['name'];
                $userupdate['PhoneCode'] = $allInputs['code'];
                $userupdate['PhoneNo'] = $PhoneOnly;
                $userUpdated = Auth_Library::UpdateUsers($userupdate);
                if($userUpdated){
                    return 5;
                }
            }
        }else{
            return 0;
        }
    }
    
    public static function UpdateClinicAppointment_OLD($clinicdata){ 
        $allInputs = Input::all();
        StringHelper::Set_Default_Timezone();
        $currentDate = date('d-m-Y');
        
        
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        $findUserAppointment = General_Library::FindUserAppointment($allInputs['bookingid']);
        if($findClinicDetails && $findUserAppointment){
            $bookingtime = $allInputs['endtime'] - $allInputs['starttime'];
            $slottime = abs($bookingtime)/60;
            
            $existingAppointment = General_Library::FindExistingAppointment($allInputs['doctorid'],strtotime($allInputs['bookdate']));
            
            $activeAppointment = 0;
            if($existingAppointment){
                foreach($existingAppointment as $appointExist){
                    if($activeAppointment ==0){
                        if(($appointExist->StartTime <= $allInputs['starttime'] && $appointExist->EndTime > $allInputs['starttime']) || ($appointExist->StartTime < $allInputs['endtime'] && $appointExist->EndTime >= $allInputs['endtime'])){
                            $activeAppointment = 1;
                        }
                    }    
                }
            }
            
            if($activeAppointment==1 || ($activeAppointment !=1 && $slottime != $allInputs['duration'])){
                return 1;
            }
            $starttime = date('h:i A',$allInputs['starttime']);
            $endtime = date('h:i A',$allInputs['endtime']);
            //$bookArray['userid'] = $userid;
            //$bookArray['clinictimeid'] = $allInputs['clinictimeid'];
            $bookArray['DoctorID'] = $allInputs['doctorid'];
            $bookArray['ProcedureID'] = $allInputs['procedureid'];
            //$bookArray['StartTime'] = $allInputs['starttime'];
            //$bookArray['EndTime'] = $allInputs['endtime'];
            $bookArray['StartTime'] = strtotime($allInputs['bookdate'].$starttime);
            $bookArray['EndTime'] = strtotime($allInputs['bookdate'].$endtime);
                //$bookArray['slotplace'] = $allInputs['slotplace'];
            $bookArray['Remarks'] = $allInputs['remarks'];
            $bookArray['BookDate'] = strtotime($allInputs['bookdate']);
            $bookArray['MediaType'] = 1;
            
            $updateAppointment = General_Library::UpdateAppointment($bookArray,$findUserAppointment->UserAppoinmentID);
            if($updateAppointment){
                $findPlusSign = substr($allInputs['phone'], 0, 1);
                if($findPlusSign == 0){
                    $PhoneOnly = $allInputs['code'].substr($allInputs['phone'], 1);
                }else{
                    $PhoneOnly = $allInputs['code'].$allInputs['phone'];
                }
                $userupdate['userid'] = $findUserAppointment->UserID;
                $userupdate['Name'] = $allInputs['name'];
                $userupdate['PhoneCode'] = $allInputs['code'];
                $userupdate['PhoneNo'] = $PhoneOnly;
                Auth_Library::UpdateUsers($userupdate);
                
                $findClinicProcedure = General_Library::FindClinicProcedure($allInputs['procedureid']);
                if($findClinicProcedure){
                    $procedurename = $findClinicProcedure->Name;
                }else{
                    $procedurename = null;
                }
                $findUserDetails = Auth_Library::FindUserDetails($findUserAppointment->UserID);
                $findDoctorDetails = Doctor_Library::FindDoctorDetails($allInputs['doctorid']);
                //Send Email 
                $formatDate = date('l, j F Y',strtotime($allInputs['bookdate']));
                $emailDdata['bookingid'] = $findUserAppointment->UserAppoinmentID;
                $emailDdata['remarks'] = $allInputs['remarks'];
                $emailDdata['bookingTime'] = date('h:i A',$allInputs['starttime']).' - '.date('h:i A',$allInputs['endtime']);
                $emailDdata['bookingNo'] = 0;
                $emailDdata['bookingDate'] = $formatDate; 
                $emailDdata['doctorName'] = $findDoctorDetails->Name;
                $emailDdata['doctorSpeciality'] = $findDoctorDetails->Specialty;
                $emailDdata['clinicName'] = $findClinicDetails->Name;
                $emailDdata['clinicAddress'] = $findClinicDetails->Address;
                $emailDdata['clinicProcedure'] = $procedurename; 
                $emailDdata['emailName']= $findUserDetails->Name;
                $emailDdata['emailPage']= 'email-templates.booking';
                $emailDdata['emailTo']= $findUserDetails->Email;
                $emailDdata['emailSubject'] = 'Booking Confirmed';
                EmailHelper::sendEmail($emailDdata);
                //Email to Doctor 
                $emailDdata['emailPage']= 'email-templates.booking-doctor';
                $emailDdata['emailTo']= $findDoctorDetails->Email;
                EmailHelper::sendEmail($emailDdata);
                //Email to Clinic
                $emailDdata['emailPage']= 'email-templates.booking-doctor';
                $emailDdata['emailTo']= $clinicdata->Email;
                EmailHelper::sendEmail($emailDdata);
                return 5;
            }
        }else{
            return 0;
        }
    }

    
    /* Use      :   Used to Delete appointment by Clinic
     * 
     */
    public static function DeleteClinicAppointment($clinicdata){ 
        $allInputs = Input::all();
        StringHelper::Set_Default_Timezone();
        $currentDate = date('d-m-Y');
        
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        $findUserAppointment = General_Library::FindUserAppointment($allInputs['bookingid']);
        if($findClinicDetails && $findUserAppointment){
            $bookArray['Active'] = 0;
            $bookArray['Status'] = 3;
            
            $updateAppointment = General_Library::UpdateAppointment($bookArray,$findUserAppointment->UserAppoinmentID);
            if($updateAppointment){
                $findUserDetails = Auth_Library::FindUserDetails($findUserAppointment->UserID);
                $findDoctorDetails =    Doctor_Library::FindDoctorDetails($findUserAppointment->DoctorID);
                $findClinicProcedure = General_Library::FindClinicProcedure($findUserAppointment->ProcedureID);
                //Send SMS
                if(StringHelper::Deployment()==1){
                    // $smsMessage = "Hello ".$findUserDetails->Name." we are sorry to see you cancelled your booking with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name.". Please feel free to get in touch with us on happiness@mednefits.com and let us know if we can be of any assistance.";
                    // $sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
                    // $saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $findUserDetails->Name, $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage);
                }
                if($findClinicProcedure){
                    $procedurename = $findClinicProcedure->Name;
                }else{
                    $procedurename = null;
                }
                //Send Email 
                $formatDate = date('l, j F Y',strtotime($findUserAppointment->BookDate));
                $emailDdata['bookingid'] = $findUserAppointment->UserAppoinmentID;
                $emailDdata['remarks'] = $findUserAppointment->Remarks;
                $emailDdata['bookingTime'] = date('h:i A',$findUserAppointment->StartTime).' - '.date('h:i A',$findUserAppointment->EndTime);
                $emailDdata['bookingNo'] = 0;
                $emailDdata['bookingDate'] = $formatDate; 
                $emailDdata['doctorName'] = $findDoctorDetails->Name;
                $emailDdata['doctorSpeciality'] = $findDoctorDetails->Specialty;
                $emailDdata['clinicName'] = $findClinicDetails->Name;
                $emailDdata['clinicAddress'] = $findClinicDetails->Address;
                $emailDdata['clinicProcedure'] = $procedurename;
                
                $emailDdata['emailName']= $findUserDetails->Name;
                $emailDdata['emailPage']= 'email-templates.booking-cancel';
                $emailDdata['emailTo']= $findUserDetails->Email;
                $emailDdata['emailSubject'] = 'Booking Cancelled';
                EmailHelper::sendEmail($emailDdata);
                
                $gc = new GoogleCalenderController();
                try {
                $gc->removeEvent($findUserAppointment->DoctorID,$findUserAppointment->Gc_event_id);
                    
                } catch (Exception $e) {}


                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }   
    }

    /* Use      :   Used to Delete appointment by Clinic
     * 
     */
    public static function ConcludeClinicAppointment($clinicdata){ 
        $allInputs = Input::all();
        StringHelper::Set_Default_Timezone();
        $currentDate = date('d-m-Y');
        
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        $findUserAppointment = General_Library::FindUserAppointment($allInputs['bookingid']);
        if($findClinicDetails && $findUserAppointment){
            $bookArray['Active'] = 1;
            $bookArray['Status'] = 2;
            
            $updateAppointment = General_Library::UpdateAppointment($bookArray,$findUserAppointment->UserAppoinmentID);
            if($updateAppointment){
                $findClinicProcedure = General_Library::FindClinicProcedure($findUserAppointment->ProcedureID);
                if($findClinicProcedure){
                    $procedurename = $findClinicProcedure->Name;
                }else{
                    $procedurename = null;
                }
                $findUserDetails = Auth_Library::FindUserDetails($findUserAppointment->UserID);
                $findDoctorDetails = Doctor_Library::FindDoctorDetails($findUserAppointment->DoctorID);
                //Send SMS
                if(StringHelper::Deployment()==1){
                    $smsMessage = "Hello ".$findUserDetails->Name.", thank you for using Mednefits, it was a pleasure serving you. We hope that the medicloud booking service was seamless, please feel free to get in touch with us on mednefits.com and share your experience.";
                    $sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
                    $saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $findUserDetails->Name, $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage);
                }
                //Email to User
                $formatDate = date('l, j F Y',strtotime($findUserAppointment->BookDate));
                $emailDdata['bookingid'] = $findUserAppointment->UserAppoinmentID;
                $emailDdata['remarks'] = $findUserAppointment->Remarks;
                $emailDdata['bookingTime'] = date('h:i A',$findUserAppointment->StartTime).' - '.date('h:i A',$findUserAppointment->EndTime);
                $emailDdata['bookingNo'] = 0;
                $emailDdata['bookingDate'] = $formatDate; 
                $emailDdata['doctorName'] = $findDoctorDetails->Name;
                $emailDdata['doctorSpeciality'] = $findDoctorDetails->Specialty;
                $emailDdata['clinicName'] = $findClinicDetails->Name;
                $emailDdata['clinicAddress'] = $findClinicDetails->Address;
                $emailDdata['clinicProcedure'] = $procedurename;
                $emailDdata['emailName']= $findUserDetails->Name;
                $emailDdata['emailPage']= 'email-templates.booking-conclude';
                $emailDdata['emailTo']= $findUserDetails->Email;
                $emailDdata['emailSubject'] = 'Your booking is concluded!';
                EmailHelper::sendEmail($emailDdata);
                
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
        
    }
    
    public static function UpdateDoctorPage($clinicdata,$doctorid){
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        $findDoctorDetails = Doctor_Library::FindSingleClinicDoctor($clinicdata->Ref_ID,$doctorid);
        if($findClinicDetails && $findDoctorDetails){
            $allProcedures = self::FindClinicProcedures($findClinicDetails->ClinicID);
            
            $doctorProcedureList = Doctor_Library::FindDoctorProcedures($findClinicDetails->ClinicID,$findDoctorDetails->DoctorID);
            
            $doctorArray = Array_Helper::DoctorDetails($findDoctorDetails);
            $dataArray['doctordetails'] = $doctorArray;
            $dataArray['doctordetails']['doctor_phone'] = $findDoctorDetails->DocPhone;
            $dataArray['doctordetails']['doctor_emergency'] = $findDoctorDetails->DocEmergency;
            $dataArray['doctordetails']['doctor_emergency_code'] = $findDoctorDetails->DocEmCode;
            $dataArray['doctordetails']['doctor_code'] = $findDoctorDetails->DocCode;
            $dataArray['doctorprocedures'] = $doctorProcedureList;
            $dataArray['clinicprocedures'] = $allProcedures;
            $dataArray['clinicdetails'] = $findClinicDetails;
            $dataArray['title'] = "Clinic Doctor Update Page";
            $view = View::make('clinic.update-doctor', $dataArray);     
            return $view;
        }else{
            return FALSE;
        }
    }

 
// nhr 2016-1-28
 public static function insertGoogleCalenderAppointment($bookArray,$findDoctorDetails)
 {
    $calender = new GoogleCalenderController();
    return $calender->insertEvent($bookArray,$findDoctorDetails);
 }  


 public static function syncAppointment($currentDate,$doctorid)
  {
            
            $doctor = new Doctor();
            $data = $doctor->FindDoctor($doctorid);
            $token = $data->token;
            // return $token;
            if (!is_null($token) || ($token!='')) {

            $userAppointment = new UserAppoinment();
            $userAppointment->removeGoogleEvent($doctorid);
            
            $rmevents = new ExtraEvents();
            $rmevents->removeEvent($doctorid);   
            //$dataArray['loadarray']['sync_msg'] = 1;
            $cal = new GoogleCalenderController();
            $results = $cal->getdoctorcalender_data($currentDate,$doctorid);
            $count = count($results);
            $proc = new ClinicProcedures();
            $timeSlots = $proc->GetClinicProcedureTime();
            $proID = $timeSlots[0]->ProcedureID;
            // return json_encode($results->getItems());
            $temp = array();
            if ($count>0) {
                
                foreach ($results->getItems() as $event) { 
                $start = $event->start->dateTime;   
                $end = $event->end->dateTime;
              
                $stimestart = date('Y-m-d H:i:s',strtotime($start));
                $stimeend = date('Y-m-d H:i:s',strtotime($end));
                $date = date('Y-m-d',strtotime($start));
                $organizer = $event->organizer->email;
                $gevent = $event->getSummary().'-';
                // $temp_gevent = $event->getSummary();
                $event_id = $event->id;
                
                $duration = (strtotime($stimeend)-strtotime($stimestart))/60; 
                $txt = explode('-',$gevent);
               
                // $gevent = $txt[1];
                // $cont = array(
                //     'start' => $event->start->dateTime,
                //     'end'   => $event->end->dateTime,
                //     'event_id' => $event->id,
                //     'duration' =>  $duration,
                //     'gevent' => $gevent
                // );
                //$slot = ceil($duration/15);
                // if ($gevent != " New Medicloud Appointment" ) {
                if ( !strpos($gevent, "New Mednefits Appointment") || !strpos($gevent, "New Medicloud Appointment")) {
                // array_push($temp, $gevent);

                        //     $tmpStime = strtotime($stimestart);
                        // foreach ($timeSlots as $value) {
                        //     $slot = $value->Duration;
                        //     $divValue = floor($duration/$slot);

                        //     //remaining duration
                        //     $duration = $duration % $slot;

                        //     if ($slot == 15 && $duration>0){
                        //       $divValue++;
                        //     }

                        //     if ($divValue>0) {
                        //         $proID = $proc->GetClinicProcedureByTime($slot)->ProcedureID;
                        //       // echo $divValue." slots of ".$slot." mins"."\n";

                        //         for ($i=0; $i < $divValue; $i++) { 
                        //             // $tmp = 15*$i;
                        //             $k = $i;
                        //             $mnts = $slot*$i;
                        //             // echo $mnts;
                        //             $stime = strtotime("+".$mnts." minutes", $tmpStime);
                        //             $etime = $stime+($slot*60);


                        //         $userAppointment = new UserAppoinment();
                        //         $userAppointment->NewAppointment1(array(
                        //             // 'userid' => 1826,//2275,
                        //             'userid' => 2275,
                        //             'starttime' => $stime,
                        //             'endtime' => $etime,
                        //             'remarks' => $gevent,
                        //             'clinictimeid' => 0,
                        //             'doctorid' => $doctorid,
                        //             'procedureid' => $proID,
                        //             'bookdate' => strtotime($date),
                        //             'mediatype' => 1,
                        //             'event_id' => $event_id,
                        //             ));
                                
                        //         }
                        //         $tmpStime = $etime;
                        //     }

                        //     // echo $value->Duration;
                        // }
                    $guid = StringHelper::getGUID();
                    $events = new ExtraEvents();
                    $events->insertEvent(array(
                        'id' => $guid,
                        'start_time' => strtotime($stimestart),
                        'end_time' => strtotime($stimeend),
                        'remarks' => $gevent,
                        'type' => 1,
                        'doctor_id' => $doctorid,
                        'date' => strtotime($date),
                        'event_id' => $event_id,
                        ));
                 
                } //end of new entry
               
                }
            }
            return $temp;
        }
  } 
    
  
  public static function UpdateBookingChannel($clinicdata){
        $allInputs = Input::all();
        $currentdate = strtotime(date('d-m-Y'));
        $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails){
            if($allInputs['doctorid']){
                $findChannelBooking = General_Library::FindChannelBookingDoctor($clinicdata->Ref_ID,$allInputs['doctorid'],$currentdate,$allInputs['defaulttime']);
            }else{
                $findChannelBooking = General_Library::FindChannelBooking($clinicdata->Ref_ID,$currentdate,$allInputs['defaulttime']);
            }
            if($findChannelBooking){
                return count($findChannelBooking);
            }else{
                return 0;
            }
        }else{
            return 0;
        }
  }

  // public static function UpdateBookingChannel($clinicdata){
  //       $allInputs = Input::all();
  //       $currentdate = strtotime(date('d-m-Y'));
  //       $findClinicDetails = self::FindClinicDetails($clinicdata->Ref_ID);
  //       if($findClinicDetails){
  //           if($allInputs['doctorid']){
  //               $findChannelBooking = General_Library::FindChannelBookingDoctor($clinicdata->Ref_ID,$allInputs['doctorid'],$currentdate,$allInputs['defaulttime']);
  //           }else{
  //               $findChannelBooking = General_Library::FindChannelBooking($clinicdata->Ref_ID,$currentdate,$allInputs['defaulttime']);
  //           }
  //           if($findChannelBooking){
  //               return count($findChannelBooking);
  //           }else{
  //               return 0;
  //           }
  //       }else{
  //           return 0;
  //       }
  // }
  

// ..........nhr...................................

  public static function insertActivityLog($dataArray)
  {
      $activity = new ActivityLog();
      $activity->insertActivityLog($dataArray);
  }

// nhr 2016/8/4 send custom smsMessage
public static function sendCustomSms($clinicID)
{   
    $allInputs = Input::all();
    $name = $allInputs['name'];
    $code = $allInputs['code'];
    $phone = $allInputs['phone'];
    $message = $allInputs['message'];

    $newPhone = $code.$phone;
    $smsMessage = $allInputs['message']." Powered by Mednefits. Get the free app at http://goo.gl/mtz4JL ";
                 
    $sendSMS = StringHelper::SendOTPSMS($newPhone,$smsMessage);

    if ($sendSMS) {

        $data['name']           = $name;
        $data['message']        = $smsMessage;
        $data['phone_code']     = $code;
        $data['phone_number']   = $phone;
        $data['clinic_id']      = $clinicID;

        $sms = new SmsHistory();
        $sms->insert($data);

        return 1;
    }else {
        return 0;
    }
                    
}



//End of Class// 
}
