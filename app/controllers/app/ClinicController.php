<?php

use Illuminate\Support\Facades\Input;
//use Symfony\Component\Security\Core\User\User;
use App\User;
class App_ClinicController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

        public function __construct(){
            //echo 'Hi';
        }
        /* Use          :       Used to manage doctors details by clinic
         * Access       :       public
         * Return       :       view
         *
         */
        public function ManageDoctors(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){

            $dataArray = array();
            $doctorArray = array();
            $dataArray['title'] = "Medicloud Doctor Settings";

            //$docAviController = new App_DoctorAvailController();
            $docController = new App_DoctorController();
            //It should be called by session

            //$findClinicDoctors = $docAviController->FindClinicDoctors($clinicid);
            $findClinicDoctors = $docController->FindClinicDoctors($getSessionData->Ref_ID);
            if($findClinicDoctors){

                foreach($findClinicDoctors as $CDoctor){
                    $findDoctor = $docController->FindDoctor($CDoctor->DoctorID);
                    if($findDoctor){
                        $doctorArray['error'] = 0;
                        $doctorArray['doctorid'] = $findDoctor->DoctorID;
                        $doctorArray['doctorname'] = $findDoctor->Name;
                        //start
                        $doctorArray['docqualifications'] = $findDoctor->Qualifications;
                        $doctorArray['docspecialty'] = $findDoctor->Specialty;
                        $doctorArray['docimage'] = $findDoctor->image;
                        $doctorArray['docmobile'] = $findDoctor->Phone;
                        $doctorArray['docemergency'] = $findDoctor->Emergency;
                        $doctorArray['docemail'] = $findDoctor->Email;
                        //end
                        $DArray[] = $doctorArray;
                    }
                }
            }else{
                return Redirect::to('app/clinic/new-doctor');
            }
            $dataArray['clinicid'] = $getSessionData->Ref_ID;
            $dataArray['doctors'] = $DArray;

            if($doctorArray['error']==0){
                //Default Doctordetail start
                $getDoctorslot = $docController->findDoctorSlots($dataArray['doctors'][0]['doctorid'],$getSessionData->Ref_ID);

                $dataArray['default'] = 1;
                $dataArray['today'] = date("d-m-Y");
                //$dataArray['today'] = '08-12-2014';
                $dataArray['docid'] = $dataArray['doctors'][0]['doctorid'];
                $dataArray['name'] = $dataArray['doctors'][0]['doctorname'];
                $dataArray['qualifications'] = $dataArray['doctors'][0]['docqualifications'];
                $dataArray['specialty'] = $dataArray['doctors'][0]['docspecialty'];
                //$dataArray['image'] = URL::to('/assets/'.$dataArray['doctors'][0]['docimage']);
                $dataArray['image'] = $dataArray['doctors'][0]['docimage'];
                $dataArray['mobile'] = $dataArray['doctors'][0]['docmobile'];
                $dataArray['emergency'] = $dataArray['doctors'][0]['docemergency'];
                $dataArray['email'] = $dataArray['doctors'][0]['docemail'];
                if($getDoctorslot){
                    $slottype = ArrayHelper::getSlotType($getDoctorslot->TimeSlot);
                    //$findSlotDetails = $docController->getAllSlotDetails($getDoctorslot->DoctorSlotID);
                    $findSlotDetails = $docController->FindSlotDetailsByType($getDoctorslot->DoctorSlotID,$slottype);

                    if($getDoctorslot->ClinicSession==1){
                    //if($getDoctorslot->ClinicSession==1 || $getDoctorslot->ClinicSession==3){
                        $dataArray['queueno'] = $getDoctorslot->QueueNumber;
                        $dataArray['queuetime'] = $getDoctorslot->QueueTime;
                    }else{
                        $dataArray['queueno'] = 0;
                        $dataArray['queuetime'] = null;
                    }
                    if($getDoctorslot->ClinicSession==2 ){
                    //if($getDoctorslot->ClinicSession==2 || $getDoctorslot->ClinicSession==3){
                        $dataArray['timeslot'] = $getDoctorslot->TimeSlot;
                    }else{
                        $dataArray['timeslot'] = null;
                    }
                    if(!empty($getDoctorslot->ConsultationCharge)){
                        $dataArray['consultcharge'] = $getDoctorslot->ConsultationCharge;
                    }else{ $dataArray['consultcharge'] = 000;}

                    //$dataArray['consultcharge'] = $getDoctorslot->ConsultationCharge;
                    //$dataArray['timeslot'] = $getDoctorslot->TimeSlot;
                    $dataArray['doctorslotexist'] = $getDoctorslot->DoctorSlotID;
                    $dataArray['clinicsession'] = $getDoctorslot->ClinicSession;

                    if(is_array($findSlotDetails) && count($findSlotDetails)>0){

                        $myslots = array();
                        foreach($findSlotDetails as $k){
                            $myslots[] = (array) $k;
                        }

                        $dataArray['slotdetails'] = $myslots;
                    }else{
                        $dataArray['slotdetails'] = null;
                    }
                }else{
                    $dataArray['consultcharge'] = null;
                    $dataArray['timeslot'] = null;
                    $dataArray['doctorslotexist'] = 0;
                    $dataArray['clinicsession'] = 0;
                    $dataArray['queueno'] = 0;
                    $dataArray['queuetime'] = null;
                }
            }
            $view = View::make('clinic.manage-doctors', $dataArray);
            return $view;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        public function AddDoctorToClinic(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){

                $dataArray = array();
                $docController = new App_DoctorController();
                $dataArray['title'] = "Medicloud Add a new doctor";
                $clinicid = $getSessionData->Ref_ID;

                $findClinicDoctors = $docController->FindClinicDoctors($clinicid);
                if($findClinicDoctors){
                    foreach($findClinicDoctors as $CDoctor){
                        $findDoctor = $docController->FindDoctor($CDoctor->DoctorID);
                        if($findDoctor){
                            $doctorArray['error'] = 0;
                            $doctorArray['doctorid'] = $findDoctor->DoctorID;
                            $doctorArray['doctorname'] = $findDoctor->Name;
                            $DArray[] = $doctorArray;
                        }
                    }
                }else{
                    $doctorArray['error'] = 1;
                    $doctorArray['error-message'] = "No doctors available in this clinic";
                    $DArray[] = $doctorArray;
                }
                $dataArray['default'] = 0;
                $dataArray['today'] = date("d-m-Y");
                $dataArray['doctors'] = $DArray;
                $dataArray['clinicid'] = $clinicid;
                //for testing
                $dataArray['timeslot'] = "";
                $dataArray['doctorslotexist'] = "";
                $view = View::make('clinic.manage-doctors', $dataArray);
                return $view;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }


        /* Use          :   Used to load doctor data
         * Call         :   AJAX
         *
         */

        public function ClinicData(){
            $data = Input::all();
            //echo $data['doctorid'];
            $doctor = new App_DoctorController();
            if($data['doctorid']!="" && $data['doctorid']!=0){
                $doctorDetails = $doctor->findDoctor($data['doctorid']);

                if($doctorDetails){
                    $getDoctorslot = $doctor->findDoctorSlots($data['doctorid'],$data['clinicid']);
                    $returnObject = array();
                    $returnObject['default'] = 0;
                    $returnObject['name'] = $doctorDetails->Name;
                    $returnObject['qualifications'] = $doctorDetails->Qualifications;
                    $returnObject['specialty'] = $doctorDetails->Specialty;
                    //$returnObject['image'] = URL::to('/assets/'.$doctorDetails->image);
                    $returnObject['image'] = $doctorDetails->image;
                    $returnObject['mobile'] = $doctorDetails->Phone;
                    $returnObject['emergency'] = $doctorDetails->Emergency;
                    $returnObject['email'] = $doctorDetails->Email;
                    if($getDoctorslot){
                        //get slot details
                        $findSlotDetails = $doctor->getAllSlotDetails($getDoctorslot->DoctorSlotID);
                        if($getDoctorslot->ClinicSession==1){
                        //if($getDoctorslot->ClinicSession==1 || $getDoctorslot->ClinicSession==3){
                            $returnObject['queueno'] = $getDoctorslot->QueueNumber;
                            $returnObject['queuetime'] = $getDoctorslot->QueueTime;
                        }else{
                            $returnObject['queueno'] = 0;
                            $returnObject['queuetime'] = null;
                        }
                        if($getDoctorslot->ClinicSession==2){
                        //if($getDoctorslot->ClinicSession==2 || $getDoctorslot->ClinicSession==3){
                            //$returnObject['consultcharge'] = $getDoctorslot->ConsultationCharge;
                            $returnObject['timeslot'] = $getDoctorslot->TimeSlot;
                        }else{
                            //$returnObject['consultcharge'] = 000;
                            $returnObject['timeslot'] = null;
                        }
                        if($getDoctorslot->ConsultationCharge > 0){
                            $returnObject['consultcharge'] = $getDoctorslot->ConsultationCharge;
                        }else { $returnObject['consultcharge'] = 000; }


                        //$returnObject['consultcharge'] = $getDoctorslot->ConsultationCharge;
                        //$returnObject['timeslot'] = $getDoctorslot->TimeSlot;
                        $returnObject['doctorslotexist'] = $getDoctorslot->DoctorSlotID;
                        $returnObject['clinicsession'] = $getDoctorslot->ClinicSession;
                        if(is_array($findSlotDetails) && count($findSlotDetails)>0){
                            $myslots = array();
                            foreach($findSlotDetails as $k){
                                $myslots[] = (array) $k;
                            }
                            $returnObject['slotdetails'] = $myslots;
                        }else{
                            $returnObject['slotdetails'] = array();
                        }
                    }else{
                        $returnObject['consultcharge'] = null;
                        $returnObject['timeslot'] = null;
                        $returnObject['queueno'] = 0;
                        $returnObject['queuetime'] = null;
                        $returnObject['doctorslotexist'] = 0;
                        $returnObject['clinicsession'] =0;
                    }
                    $returnObject['today'] = date("d-m-Y");

                    $view = View::make('ajax.clinic-settings-doctor', $returnObject);
                    return $view;
                }else{

                }

            }else{
                //return FALSE;
            }
        }

        /*
         *
         */
        public function ClinicDashboard(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $returnObject['title'] = "Medicloud Clinic Dashboard";
                $view = View::make('clinic.dashboard', $returnObject);
                return $view;
            }else{
                return Redirect::to('provider-portal-login');
            }

        }

        /* Use          :   Used to book appointment
         * By           :   Clinic
         *
         *
         */
        //need to fix this function
        public function BookNewAppointment(){
            $appointment = new UserAppoinment();

            $dataArray['userid'] = 1;
            $dataArray['booktype'] = 1; // 0 - Queue number, 1 - Slot
            $dataArray['doctorslotid'] = 0;
            $dataArray['slotdetailid'] = 2;
            $dataArray['mediatype'] = 1; // 0 - Mobile, 1 - Web
            $dataArray['booknumber'] = 0;
            $formatDate = date("d-m-Y", strtotime('22-12-2014'));
            $dataArray['bookdate'] = $formatDate;

            $appointmentID = $appointment->insertUserAppointment($dataArray);

            if(!empty($appointmentID)){
                $returnArray['bookid'] = $appointmentID;

                $view = View::make('clinic.appointment-test', $returnArray);
                return $view;
            }

        }
        /* Use          :   Display booking page (Slot & Queue)
         * Access       :   Public
         * Parameter    :   Token & Current date
         */
        public function BookingPage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $currentdate = date('d-m-Y');
                $returnArray['title'] = "Medicloud Doctor Booking";
                //$findClinicDoctors = $this->ProcessBookingPage($getSessionData->Ref_ID,date('d-m-Y'));
                $findClinicDoctors = Clinic_Library::BookingPage($getSessionData->Ref_ID,$currentdate);
                if($findClinicDoctors){
                    //echo '<pre>'; print_r($findClinicDoctors); echo '</pre>';
                    $returnArray['doctors'] = $findClinicDoctors;
                    $returnArray['today'] = $currentdate;
                    $view = View::make('clinic.booking', $returnArray);
                    return $view;
                }else{
                    return Redirect::to('app/clinic/manage-doctors');
                }
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        public function BookingPage1(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $returnArray['title'] = "Medicloud Doctor Booking";
                $findClinicDoctors = $this->ProcessBookingPage($getSessionData->Ref_ID,date('d-m-Y'));
                if($findClinicDoctors){
                    $returnArray['doctors'] = $findClinicDoctors;
                    $returnArray['today'] = date("d-m-Y");
                    $view = View::make('clinic.booking', $returnArray);
                    return $view;
                }else{
                    return Redirect::to('app/clinic/manage-doctors');
                }
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        /* Use          :       Used to generate booking page
         * Access       :       Private
         * Parameter    :
         */
        private function ProcessBookingPage1($sessiondata,$currentdate){
            $authcontroller = new App_AuthController();
                $docController = new App_DoctorController();
                $findClinicDoctors = $docController->FindClinicDoctors($sessiondata);
                if($findClinicDoctors){
                    $doctorPlace = 1; $firstDoctorID=0;
                    foreach($findClinicDoctors as $clinicdoctor){
                        $findDoctor = $docController->FindDoctor($clinicdoctor->DoctorID);
                        if($findDoctor){
                            if($doctorPlace==1) {
                                $firstDoctorID = $findDoctor->DoctorID;
                                $doctorPlace ++;
                            }
                            $returnArray['doctors']['clinicid'] = $sessiondata;
                            $returnArray['doctors']['doctorid'] = $findDoctor->DoctorID;
                            $returnArray['doctors']['name'] = $findDoctor->Name;
                            $returnArray['doctors']['image'] = URL::to('/assets/'.$findDoctor->image);
                            $returnArray['doctors']['email'] = $findDoctor->Email;
                            $returnArray['doctors']['specialty'] = $findDoctor->Specialty;
                            $returnArray['doctors']['bookdate'] = $currentdate;


                        if($firstDoctorID == $findDoctor->DoctorID){

                            $getDoctorslot = $docController->findDoctorSlots($firstDoctorID,$sessiondata);
                            if($getDoctorslot){
                                $doctorSlotMng = $docController->DoctorSlotManageByDate($getDoctorslot->DoctorSlotID,$currentdate);
                                $cancelQueueTotal = $docController->cancelledAppointmentQueue($getDoctorslot->DoctorSlotID,$currentdate);
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
                                }else {$returnArray['doctors']['doctorslot']['currentqueuetotal'] =null;
                                        $returnArray['doctors']['doctorslot']['queuestop'] = null;
                                        $returnArray['doctors']['doctorslot']['slotmanageid'] =null;
                                }
                                if($cancelQueueTotal > 0){
                                    $returnArray['doctors']['doctorslot']['queuecancelled'] = $cancelQueueTotal;
                                }else { $returnArray['doctors']['doctorslot']['queuecancelled']= null; }

                                $slottype = ArrayHelper::getSlotType($getDoctorslot->TimeSlot);
                                $findQueueAppointment = $docController->findAppointmentQueue($getDoctorslot->DoctorSlotID,$currentdate);
                                //$findSlotDetails = $docController->SlotDetailsForDate($getDoctorslot->DoctorSlotID,$currentdate);
                                $findSlotDetails = $docController->SlotDetailsForDate($getDoctorslot->DoctorSlotID,$slottype,$currentdate);
                                //if(!empty($findSlotDetails) && $getDoctorslot->ClinicSession==3 || $getDoctorslot->ClinicSession==2){
                            if($findSlotDetails){
                                if($getDoctorslot->ClinicSession==3 || $getDoctorslot->ClinicSession==2){
                                    foreach($findSlotDetails as $slotDetail){
                                        $findSlotAppointment = $docController->findAppointmentSlot($slotDetail->SlotDetailID,$currentdate);
                                        if(!empty($findSlotAppointment)){
                                            $findSlotUser = $authcontroller->getUserDetails($findSlotAppointment->UserID);
                                            $slotarray['appoint']['appointid'] = $findSlotAppointment->UserAppoinmentID;
                                            $slotarray['appoint']['userid'] = $findSlotAppointment->UserID;
                                            $slotarray['appoint']['slotdetailid'] = $findSlotAppointment->SlotDetailID;
                                            $slotarray['appoint']['booktype'] = $findSlotAppointment->BookType;
                                            $slotarray['appoint']['mediatype'] = $findSlotAppointment->MediaType;
                                            $slotarray['appoint']['bookdate'] = $findSlotAppointment->BookDate;
                                            $slotarray['appoint']['status'] = $findSlotAppointment->Status;
                                            $slotarray['appoint']['active'] = $findSlotAppointment->Active;
                                            $slotarray['appoint']['user']['userid'] = $findSlotUser->UserID;
                                            $slotarray['appoint']['user']['name'] = $findSlotUser->Name;
                                            $slotarray['appoint']['user']['email'] = $findSlotUser->Email;
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
                            }}else{ $returnArray['doctors']['slot-details'] = null; }
                                //for queue appointment
                                if($findQueueAppointment){
                                    foreach($findQueueAppointment as $fqueueAppoint){
                                        $queueArray['appointmentid'] = $fqueueAppoint->UserAppoinmentID;
                                        $queueArray['bookno'] = $fqueueAppoint->BookNumber;
                                        $queueArray['status'] = $fqueueAppoint->Status;
                                        $queueArray['bookdate'] = $fqueueAppoint->BookDate;
                                        $findSlotUser = $authcontroller->getUserDetails($fqueueAppoint->UserID);
                                        $queueArray['user']['userid'] = $findSlotUser->UserID;
                                        $queueArray['user']['name'] = $findSlotUser->Name;
                                        $queueArray['user']['email'] = $findSlotUser->Email;
                                        $queueAppoint[] = $queueArray;
                                    }

                                    $returnArray['doctors']['queue-booking'] = $queueAppoint;
                                }else{
                                    $returnArray['doctors']['queue-booking'] = null;
                                }
                            }else{
                                return null;
                                //return Redirect::to('app/clinic/manage-doctors');
                                $returnArray['doctors']['doctorslot'] = null;
                                $returnArray['doctors']['slot-details'] = null;
                                $returnArray['doctors']['queue-booking'] = null;
                        }}else{
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


        private function ProcessBookingPage2($sessiondata,$currentdate){
            $authcontroller = new App_AuthController();
                $docController = new App_DoctorController();
                $findClinicDoctors = $docController->FindClinicDoctors($sessiondata);
                if($findClinicDoctors){
                    foreach($findClinicDoctors as $clinicdoctor){
                        $findDoctor = $docController->FindDoctor($clinicdoctor->DoctorID);
                        if($findDoctor){
                            $returnArray['doctors']['clinicid'] = $sessiondata;
                            $returnArray['doctors']['doctorid'] = $findDoctor->DoctorID;
                            $returnArray['doctors']['name'] = $findDoctor->Name;
                            $returnArray['doctors']['image'] = URL::to('/assets/'.$findDoctor->image);
                            $returnArray['doctors']['email'] = $findDoctor->Email;
                            $returnArray['doctors']['specialty'] = $findDoctor->Specialty;
                            //$returnArray['doctors']['bookdate'] = date('d-m-Y');
                            $returnArray['doctors']['bookdate'] = $currentdate;

                            $getDoctorslot = $docController->findDoctorSlots($findDoctor->DoctorID,$sessiondata);
                            if($getDoctorslot){
                                $returnArray['doctors']['doctorslot']['doctorslotid'] = $getDoctorslot->DoctorSlotID;
                                $returnArray['doctors']['doctorslot']['clinicsession'] = $getDoctorslot->ClinicSession;
                                $returnArray['doctors']['doctorslot']['consultationcharge'] = $getDoctorslot->ConsultationCharge;
                                $returnArray['doctors']['doctorslot']['timeslot'] = $getDoctorslot->TimeSlot;
                                $returnArray['doctors']['doctorslot']['queuenumber'] = $getDoctorslot->QueueNumber;
                                $returnArray['doctors']['doctorslot']['queuetime'] = $getDoctorslot->QueueTime;

                                $findQueueAppointment = $docController->findAppointmentQueue($getDoctorslot->DoctorSlotID,'28-12-2014');
                                $findSlotDetails = $docController->SlotDetailsForDate($getDoctorslot->DoctorSlotID,'10-12-2014');
                                if(!empty($findSlotDetails)){
                                    foreach($findSlotDetails as $slotDetail){
                                        $findSlotAppointment = $docController->findAppointmentSlot($slotDetail->SlotDetailID,'10-12-2014');
                                        if(!empty($findSlotAppointment)){
                                            $findSlotUser = $authcontroller->getUserDetails($findSlotAppointment->UserID);
                                            $slotarray['appoint']['appointid'] = $findSlotAppointment->UserAppoinmentID;
                                            $slotarray['appoint']['userid'] = $findSlotAppointment->UserID;
                                            $slotarray['appoint']['slotdetailid'] = $findSlotAppointment->SlotDetailID;
                                            $slotarray['appoint']['booktype'] = $findSlotAppointment->BookType;
                                            $slotarray['appoint']['mediatype'] = $findSlotAppointment->MediaType;
                                            $slotarray['appoint']['bookdate'] = $findSlotAppointment->BookDate;
                                            $slotarray['appoint']['status'] = $findSlotAppointment->Status;
                                            $slotarray['appoint']['active'] = $findSlotAppointment->Active;
                                            $slotarray['appoint']['user']['userid'] = $findSlotUser->UserID;
                                            $slotarray['appoint']['user']['name'] = $findSlotUser->Name;
                                            $slotarray['appoint']['user']['email'] = $findSlotUser->Email;
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
                                //for queue appointment
                                if($findQueueAppointment){
                                    foreach($findQueueAppointment as $fqueueAppoint){
                                        $queueArray['appointmentid'] = $fqueueAppoint->UserAppoinmentID;
                                        $queueArray['bookno'] = $fqueueAppoint->BookNumber;
                                        $queueArray['status'] = $fqueueAppoint->Status;
                                        $queueArray['bookdate'] = $fqueueAppoint->BookDate;
                                        $findSlotUser = $authcontroller->getUserDetails($fqueueAppoint->UserID);
                                        $queueArray['user']['userid'] = $findSlotUser->UserID;
                                        $queueArray['user']['name'] = $findSlotUser->Name;
                                        $queueArray['user']['email'] = $findSlotUser->Email;
                                        $queueAppoint[] = $queueArray;
                                    }

                                    $returnArray['doctors']['queue-booking'] = $queueAppoint;
                                }else{
                                    $returnArray['doctors']['queue-booking'] = null;
                                }
                            }else{
                                $returnArray['doctors']['doctorslot'] = null;
                            }
                            $doctors[] = $returnArray;
                        }
                    }
                    return $doctors;
                    //return $findClinicDoctors;
                }else{
                    return null;
                }
        }

        /* Use          :   Display booking page (Slot & Queue)
         * Access       :   Public
         * Parameter    :   Token & Current date
         */
        public function AjaxBookingPage(){
            $inputdata = Input::all();
            if(!empty($inputdata['currentdate'])){
                $formatDate = date("d-m-Y", strtotime($inputdata['currentdate']));
            }else{
                $formatDate = date('d-m-Y');
            }

            $findClinicDoctors = $this->ProcessAjaxBookingPage($inputdata['clinicid'],$inputdata['doctorid'],$formatDate);
            $returnArray['doctors'] = $findClinicDoctors;

                //echo '<pre>';
                //print_r($findClinicDoctors);
                //echo '</pre>';

                $view = View::make('ajax.booking', $returnArray);
                return $view;
        }


        /* Use          :       Used to generate booking page
         * Access       :       Private
         * Parameter    :
         */
        private function ProcessAjaxBookingPage($clinicid,$doctorid,$currentdate){
            $authcontroller = new App_AuthController();
            $docController = new App_DoctorController();
                        $findDoctor = $docController->FindDoctor($doctorid);
                        if($findDoctor){
                            $returnArray['doctors']['clinicid'] = $clinicid;
                            $returnArray['doctors']['doctorid'] = $findDoctor->DoctorID;
                            $returnArray['doctors']['name'] = $findDoctor->Name;
                            //$returnArray['doctors']['image'] = URL::to('/assets/'.$findDoctor->image);
                            $returnArray['doctors']['image'] = $findDoctor->image;
                            $returnArray['doctors']['email'] = $findDoctor->Email;
                            $returnArray['doctors']['specialty'] = $findDoctor->Specialty;
                            //$returnArray['doctors']['bookdate'] = date('d-m-Y');
                            $returnArray['doctors']['bookdate'] = $currentdate;

                            $getDoctorslot = $docController->findDoctorSlots($findDoctor->DoctorID,$clinicid);
                            if($getDoctorslot){
                                $doctorSlotMng = $docController->DoctorSlotManageByDate($getDoctorslot->DoctorSlotID,$currentdate);
                                $cancelQueueTotal = $docController->cancelledAppointmentQueue($getDoctorslot->DoctorSlotID,$currentdate);
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
                                }else {$returnArray['doctors']['doctorslot']['currentqueuetotal'] =null;
                                        $returnArray['doctors']['doctorslot']['queuestop'] = null;
                                        $returnArray['doctors']['doctorslot']['slotmanageid'] = null;
                                }
                                if($cancelQueueTotal > 0){
                                    $returnArray['doctors']['doctorslot']['queuecancelled'] = $cancelQueueTotal;
                                }else { $returnArray['doctors']['doctorslot']['queuecancelled']= null; }

                                $slottype = ArrayHelper::getSlotType($getDoctorslot->TimeSlot);
                                $findQueueAppointment = $docController->findAppointmentQueue($getDoctorslot->DoctorSlotID,$currentdate);
                                //$findSlotDetails = $docController->SlotDetailsForDate($getDoctorslot->DoctorSlotID,$currentdate);
                                $findSlotDetails = $docController->SlotDetailsForDate($getDoctorslot->DoctorSlotID,$slottype,$currentdate);
                                if(!empty($findSlotDetails)){
                                    foreach($findSlotDetails as $slotDetail){
                                        $findSlotAppointment = $docController->findAppointmentSlot($slotDetail->SlotDetailID,$currentdate);
                                        if(!empty($findSlotAppointment)){
                                            $findSlotUser = $authcontroller->getUserDetails($findSlotAppointment->UserID);
                                            $slotarray['appoint']['appointid'] = $findSlotAppointment->UserAppoinmentID;
                                            $slotarray['appoint']['userid'] = $findSlotAppointment->UserID;
                                            $slotarray['appoint']['slotdetailid'] = $findSlotAppointment->SlotDetailID;
                                            $slotarray['appoint']['booktype'] = $findSlotAppointment->BookType;
                                            $slotarray['appoint']['mediatype'] = $findSlotAppointment->MediaType;
                                            $slotarray['appoint']['bookdate'] = $findSlotAppointment->BookDate;
                                            $slotarray['appoint']['status'] = $findSlotAppointment->Status;
                                            $slotarray['appoint']['active'] = $findSlotAppointment->Active;
                                            $slotarray['appoint']['user']['userid'] = $findSlotUser->UserID;
                                            $slotarray['appoint']['user']['name'] = $findSlotUser->Name;
                                            $slotarray['appoint']['user']['email'] = $findSlotUser->Email;
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
                                //for queue appointment
                                if($findQueueAppointment){
                                    foreach($findQueueAppointment as $fqueueAppoint){
                                        $queueArray['appointmentid'] = $fqueueAppoint->UserAppoinmentID;
                                        $queueArray['bookno'] = $fqueueAppoint->BookNumber;
                                        $queueArray['status'] = $fqueueAppoint->Status;
                                        $queueArray['bookdate'] = $fqueueAppoint->BookDate;
                                        $findSlotUser = $authcontroller->getUserDetails($fqueueAppoint->UserID);
                                        $queueArray['user']['userid'] = $findSlotUser->UserID;
                                        $queueArray['user']['name'] = $findSlotUser->Name;
                                        $queueArray['user']['email'] = $findSlotUser->Email;
                                        $queueAppoint[] = $queueArray;
                                    }

                                    $returnArray['doctors']['queue-booking'] = $queueAppoint;
                                }else{
                                    $returnArray['doctors']['queue-booking'] = null;
                                }
                            }else{
                                $returnArray['doctors']['doctorslot'] = null;
                            }
                            $doctors[] = $returnArray;
                            return $doctors;
                        }else{
                            return null;
                        }
        }

        public function ClinicSettings(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::ClinicSettings($getSessionData->Ref_ID);
                $view = View::make('clinic.settings', $clinicDetails);
                return $view;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }




        /* Use : Used to generate clinic dashboard
         * Access : public
         *
         */
        public function ClinicSettingDashboard(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $returnObject = Clinic_Library::ClinicSettingDashboard($getSessionData->Ref_ID);
                return $returnObject;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        /* Use : Used to refresh the details page by AJAX request
         * Access : Public by AJAX
         *
         */
        public function ClinicSettingDashboardAJAX(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $returnObject = Clinic_Library::ClinicSettingDashboardAJAX();
                return $returnObject;
            }else{
                return 0;
            }
        }

        /* Use : Ths function is used to generate dashboard booking detail page
         * Access : Public
         *
         */
        public function ClinicDashboardBooking($doctorslotid){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $returnObject = Clinic_Library::ClinicDashboardBooking($doctorslotid);
                return $returnObject;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        public function ClinicDetails($id){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $returnObject = Clinic_Library::ClinicDetails($id);
                return $returnObject;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        public function ClinicDetailsFromSession( )
        {
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $returnObject = Clinic_Library::ClinicDetails($getSessionData->Ref_ID);
                return $returnObject;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        /* Use : This function is used to generate by ajax request
         * Access : Ajax
         *
         */
        public function ClinicDashboardBookingAJAX(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $returnObject = Clinic_Library::ClinicDashboardBookingAJAX();
                return $returnObject;
            }else{
                return 0;
            }
        }

        public function ClinicDetailsPage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                //print_r($getSessionData);
                $clinicDetails = Clinic_Library::ClinicDetailsPage($getSessionData);
                //$dataArray['title'] = "Clinic Details Page";
                //$view = View::make('clinic.clinic-details', $dataArray);
                //return $view;
                return $clinicDetails;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        public function ClinicProfileImageUpload(){
            
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $imageUpload = Clinic_Library::CloudineryImageUploadWithResize(200,200);
                if($imageUpload) {
                    DB::table('clinic')->where('clinicID', $getSessionData->Ref_ID)->update(['image' => $imageUpload['img']]);
                }
                return $imageUpload;
            }else{
                return Redirect::to('provider-portal-login');
            }

            return $imageUpload;
        }

        /* Use      : Used to add new procedure
         *
         *
         */
        public function ClinicAddProcedurePage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                //print_r($getSessionData);
                $clinicDetails = Clinic_Library::ClinicAddProcedurePage($getSessionData);
                //$dataArray['title'] = "Clinic Details Page";
                //$view = View::make('clinic.clinic-details', $dataArray);
                //return $view;
                return $clinicDetails;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        /* Use      :   Used to add new doctors
         *
         *
         */
        public function ClinicAddDoctorPage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::ClinicAddDoctorPage($getSessionData);
                return $clinicDetails;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        /* Use      :   Used to view Doctors Page
         *
         *
         */
        public function ClinicDoctorsViewPage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::ClinicDoctorsviewPage($getSessionData);
                return $clinicDetails;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }


        public function CalendarIntegrationViewPage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::CalendarIntegrationViewPage($getSessionData);
                return $clinicDetails;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }


        public function buttonIntegrationViewPage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::buttonIntegrationViewPage($getSessionData);
                return $clinicDetails;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        /* Use      :   Used to view empty page when no doctors available
         *
         *
         */
        public function ClinicDoctorsHomePage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::ClinicDoctorsHomePage($getSessionData);
                return $clinicDetails;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        /* Use      :   Used to update clinic password
         *
         *
         */
        public function ClinicUpdatePasswordPage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::ClinicUpdatePasswordPage($getSessionData);
                return $clinicDetails;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        /* Use      :   Used to set clinic opening times
         *
         *
         */
        public function ClinicOpeningTimesPage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::ClinicOpeningTimesPage($getSessionData);
                return $clinicDetails;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }
        /* Use      :   Used to set doctors availability
         *
         *
         */
        public function ClinicDoctorAvailabilityPage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::ClinicDoctorAvailabilityPage($getSessionData);
                return $clinicDetails;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }

        /* Use      :   Clinic appointment view page
         *
         *
         */
        public function ClinicHomeAppointmentPage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::ClinicHomeAppointmentPage($getSessionData);
                return $clinicDetails;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }
        /* Use      :   Clinic appointment view page
         *
         *
         */
        public function SingleDoctorAppointmentPage($doctorid){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::DoctorAppointmentPage($getSessionData,$doctorid);
                return $clinicDetails;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }
        public function UpdateDoctorPage($doctorid){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::UpdateDoctorPage($getSessionData,$doctorid);
                return $clinicDetails;
            }else{
                return Redirect::to('provider-portal-login');
            }
        }





        //===================================================================//
        //                  xxxxx  AJAX calling methods  xxxx
        //===================================================================//

        /* Use      : Used to update clinic details
         *
         */
        public function ClinicDetailsPageUpdate(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetailsUpdated = Clinic_Library::ClinicDetailsPageUpdate();
                return $clinicDetailsUpdated;
            }else{
                return 0;
            }
        }
        /* Use      : Used to add new procedure by ajax
         *
         *
         */
        public function ClinicAddProcedure(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::ClinicAddProcedure();
                return $clinicDetails;
            }else{
                return 0;
            }
        }

        public function ClinicDeleteProcedure(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $clinicDetails = Clinic_Library::ClinicDeleteProcedure();
                return $clinicDetails;
            }else{
                return 0;
            }
        }

        public function ClinicAddDoctors(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $addDoctor = Clinic_Library::ClinicAddDoctor();
                return $addDoctor;
            }else{
                return 0;
            }
        }
        public function UpdateDoctorDetails(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $addDoctor = Clinic_Library::UpdateDoctorDetails($getSessionData);
                return $addDoctor;
            }else{
                return 0;
            }
        }


        public function ClinicDoctorsDelete(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $addDoctor = Clinic_Library::ClinicDoctorsDelete($getSessionData);
                return $addDoctor;
            }else{
                return 0;
            }
        }
        public function ClinicPasswordUpdate(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $addDoctor = Clinic_Library::ClinicPasswordUpdate($getSessionData);
                return $addDoctor;
            }else{
                return 0;
            }
        }

        /* Use      :   Used to Add Clinic Opening times
         *
         */
        public function ClinicOpeningTimes(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::ClinicOpeningTimes($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }

        /* Use      :   Used to Delete Clinic Opening times and Doctor availability times
         *
         */
        public function ClinicDeleteOpeningTimes(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::ClinicDeleteOpeningTimes($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        /*  Use     :   Used to Add clinic holidays
         *
         */
        public function AddClinicHolidays(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::AddClinicHolidays($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        /*  Use     :   Used to Delete clinic holidays
         *
         */
        public function DeleteClinicHolidays(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::DeleteClinicHolidays($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        /*  Use     :   Used to Delete clinic holidays
         *
         */
        public function LoadClinicDoctorAvailabilityPage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::LoadClinicDoctorAvailabilityPage($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        /*  Use     :   Used to Delete clinic holidays
         *
         */
        public function AddDoctorAvailabilityTimes(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::AddDoctorAvailabilityTimes($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        /*  Use     :   Used to Delete clinic holidays
         *
         */
        public function RepeatTimeActions(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::RepeatTimeActions($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        /*  Use     :   Used to Open booking page
         *
         */
        public function OpenBookingPage(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::OpenBookingPage($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        public function OpenBookingUpdate(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::OpenBookingUpdate($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }

        /*  Use     :   Used to Open booking page
         *
         */
        public function LoadDoctorProcedures(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::LoadDoctorProcedures($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        public function LoadBookingPopup(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::LoadBookingPopup($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        public function ChangeProcedures(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::ChangeProcedures($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        public function ChangeStartDate(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::ChangeStartDate($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }



        /* Use      :   Make new appointment
         *
         *
         */
        public function NewClinicAppointment(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::NewClinicAppointment($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        public function LoadDoctorsAppointmentView(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::LoadDoctorsAppointmentView($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        public function LoadDoctorsSelectionView(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::LoadDoctorsSelectionView($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        public function LoadSingleDoctorView(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::LoadSingleDoctorView($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        /* Use      :   Make Update appointment
         *
         *
         */
        public function UpdateClinicAppointment(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::UpdateClinicAppointment($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        /* Use      :   Make Delete appointment
         *
         *
         */
        public function DeleteClinicAppointment(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::DeleteClinicAppointment($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        /* Use      :   Make Conclude appointment
         *
         *
         */
        public function ConcludeClinicAppointment(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::ConcludeClinicAppointment($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }
        public static function UpdateBookingChannel(){
            $getSessionData = StringHelper::getMainSession(3);
            if($getSessionData != FALSE){
                $openingTimes = Clinic_Library::UpdateBookingChannel($getSessionData);
                return $openingTimes;
            }else{
                return 0;
            }
        }


        //============================ Code End ===============================//

        public function ajax(){
            $dataArray['title'] = "Test ajax";
            $view = View::make('clinic.ajax', $dataArray);
            return $view;
        }

        public function ajaxpost(){
            //$dataArray['title'] = "Test ajax";
            //$view = View::make('clinic.ajax', $dataArray);
            //return $view;
            $data = Input::all();
            echo $data['username'];
            echo '<br>';
            echo $data['token'];




        }



        public function test(){
            echo 'test is confirm';
        }
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

    public function saveAppointmentFromReserve( )
    {

        // return "hello";
        $getSessionData = StringHelper::getMainSession(3);
        if($getSessionData != FALSE){
            if (Input::has('bookingid')){
                $booking = Clinic_Library::UpdateClinicAppointmentFromReserve($getSessionData);
            } else {
                $booking = Calendar_Library::saveAppointmentFromReserve($getSessionData);
            }

            return $booking;
        }else{
            return 0;
        }
    }

    public function resetValue( )
    {
        $clinic = new Clinic( );
        return $clinic->resetClinic();
    }

    public function sendEmailConfirmation($id)
    {
        $result = DB::table('user_appoinment')
                    ->join('transaction_history', 'transaction_history.AppointmenID', '=', 'user_appoinment.UserAppoinmentID')
                    ->where('user_appoinment.UserAppoinmentID', $id)
                    ->first();
        // return DB::table('user_appoinment')
        //         ->join('doctor', 'doctor.DoctorID', '=', 'user_appoinment.DoctorID')
        //         ->join('transaction_history', 'transaction_history.AppointmenID', '=', 'UserAppoinmentID')
        //         ->join('clinic', 'clinic.ClinicID', '=', 'transaction_history.ClinicID')
        //         ->join('user', 'user.UserID', '=', 'transaction_history.UserID')
        //         ->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_history.ProcedureID')
        //         ->where('UserAppoinmentID', $id)
        //         ->select('user_appoinment.UserAppoinmentID', 'user_appoinment.BookDate','user_appoinment.StartTime', 'user_appoinment.EndTime', 'doctor.Name as doctor_name','doctor.Specialty', 'clinic.Name as clinic_name', 'clinic.Phone_Code as phone_code', 'clinic.Phone as phone', 'clinic.Address as clinic_address', 'user.Name as user_name', 'clinic_procedure.Name as procedure_name')
        //         ->get();
        $doctor = DB::table('doctor')->where('DoctorID', $result->DoctorID)->first();
        $clinic = DB::table('clinic')
                    ->join('user', 'user.Ref_ID', '=', 'clinic.ClinicID')
                    ->where('user.UserType', 3)
                    ->where('clinic.ClinicID', $result->ClinicID)
                    ->select('clinic.Name', 'clinic.Address', 'clinic.Phone', 'clinic.Phone_Code', 'user.Email')
                    ->first();
        $user = DB::table('user')->where('UserID', $result->UserID)->first();
        $procedure = DB::table('clinic_procedure')->where('ProcedureID', $result->ProcedureID)->first();

        $emailDdata = [];

        $formatDate = date('l, j F Y', $result->BookDate);
        $emailDdata['bookingid'] = $result->AppointmenID;
        $emailDdata['remarks'] = $result->Remarks;
        $emailDdata['bookingTime'] = date('h:i A', $result->StartTime).' - '.date('h:i A',$result->EndTime);
        $emailDdata['bookingNo'] = 0;
        $emailDdata['bookingDate'] = $formatDate;
        $emailDdata['doctorName'] = $doctor->Name;
        $emailDdata['doctorSpeciality'] = $doctor->Specialty;
        $emailDdata['clinicName'] = $clinic->Name;
        $emailDdata['clinicPhoneCode'] = $clinic->Phone_Code;
        $emailDdata['clinicPhone'] = $clinic->Phone;
        $emailDdata['clinicAddress'] = $clinic->Address;
        $emailDdata['clinicProcedure'] = $procedure->Name;
        $emailDdata['emailName']= $user->Name;
        $emailDdata['emailPhone']= $user->PhoneNo;
        $emailDdata['emailPage']= 'email-templates.booking';
        $emailDdata['emailTo']= $user->Email;
        $emailDdata['emailSubject'] = 'Booking Confirmed';
        // return $emailDdata;
        // if(StringHelper::Deployment()==1){
            EmailHelper::sendEmail($emailDdata);
        // }
        //copy to company
        $emailDdata['emailTo']= Config::get('config.booking_email');
        // if(StringHelper::Deployment()==1){
            EmailHelper::sendEmail($emailDdata);
        // }
        //Send email to Doctor
        $emailDdata['emailPage']= 'email-templates.booking-doctor';
        $emailDdata['emailTo']= $doctor->Email;
        // if(StringHelper::Deployment()==1){
            EmailHelper::sendEmail($emailDdata);
        // }
        //Send email to Clinic
        $emailDdata['emailPage']= 'email-templates.booking';
        $emailDdata['emailTo']= $clinic->Email;

        // if(StringHelper::Deployment()==1){
         EmailHelper::sendEmail($emailDdata);
        // }
        return $emailDdata;
    }

    public function getUserCareDetails($id)
    {
        $returnObject = new stdClass();
        $e_card = new UserPackage();
        $findUserID = DB::table('user')->where('UserID', $id)->first();
        if($findUserID){
            $returnObject->status = TRUE;
            $returnObject->data = $e_card->newEcardDetails($id);
            $result = $e_card->newEcardDetails($id);
            $first_plan = PlanHelper::getUserFirstPlanStart($id);
            if($first_plan) {
                $result['valid_start_claim'] = $first_plan;
            } else {
                $result['valid_start_claim'] = date('Y-m-d', strtotime($result['start_date']));
            }
            $result['valid_end_claim'] = date('Y-m-d', strtotime($result['valid_date']));
            return $result;
        } else {
            $returnObject->status = FALSE;
            $returnObject->message = 'User does not exist.';
            return Response::json($returnObject);
        }
    }

    public function getNRICUser( )
    {
        $input = Input::all();
        $search = $input['search'];
        return DB::table('user')
                ->where(function($query) use ($search){
                    $query->where('NRIC', 'like', '%'.$search.'%')
                    ->where('UserType', 5)
                    ->where('access_type', 0);
                })
                ->orWhere(function($query) use ($search){
                    $query->where('NRIC', 'like', '%'.$search.'%')
                    ->where('UserType', 5)
                    ->where('access_type', 1);
                })
                ->get();
    }

    public function scanPayStatus( )
    {
        $input = Input::all();
        $getSessionData = StringHelper::getMainSession(3);

        if(!$getSessionData) {
            return array('status' => FALSE, 'message' => 'Session Expired.');
        }
        // check if clinic procedure is owned

        $procedure = DB::table('clinic_procedure')->where('ClinicID', $getSessionData->Ref_ID)->where('ProcedureID', $input['procedure_id'])->count();

        if($procedure == 0) {
            return array('status' => FALSE, 'message' => 'Procedure is not owned by your Clinic.');
        }

        if($input['service_status'] === true || $input['service_status'] === "true") {
            $service_scan_pay_status = 1;
        } else {
            $service_scan_pay_status = 0;
        }

        $result = \ClinicProcedures::where('ProcedureID', $input['procedure_id'])->update(['scan_pay_show' => $service_scan_pay_status]);

        return array('status' => TRUE, 'message' => 'Success.');
    }
}
