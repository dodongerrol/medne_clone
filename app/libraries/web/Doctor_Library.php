<?php

class Doctor_Library{
    
    public static function DoctorHome($doctorid){
        $returnObject = self::ProcessDoctorHome($doctorid,0);
        
        if($returnObject != 0){
            $returnArray['doctors'] = $returnObject;
            $returnArray['title'] = "Medicloud Doctor Home";
            $view = View::make('doctor.home', $returnArray);
            return $view;
        }else{
            return FALSE;
        }       
    }
    
    /* Use          :   Used to process the Doctor home
     * Access       :   Private
     * 
     */
    private static function ProcessDoctorHome($doctorid,$validdate){
        $findDoctor = self::FindDoctor($doctorid); 
        if($findDoctor){
            if($validdate == 0){$currentdate = date("d-m-Y");}else{$currentdate = $validdate; }
            
            $findDoctorSlot = self::FindDoctorSlot($findDoctor->DoctorID);
            if($findDoctorSlot){
                if($findDoctorSlot->ClinicSession!=0){
                $queuecount =0; $slotcount = array(); $slotcompleted = array(); $queuecompleted = array();
                $slottype = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                $findSlotDetails = self::FindSlotdetailsWithDate($findDoctorSlot->DoctorSlotID,$slottype,$currentdate);
                $findQueueAppointment = self::FindAppointmentQueue($findDoctorSlot->DoctorSlotID,$currentdate);
                $doctorSlotMng = self::DoctorSlotManageByDate($findDoctorSlot->DoctorSlotID,$currentdate);
                $cancelQueueTotal = self::CancelledAppointmentQueue($findDoctorSlot->DoctorSlotID,$currentdate);
                                
                $returnArray['doctors']['clinicid'] = $findDoctorSlot->ClinicID;
                $returnArray['doctors']['doctorid'] = $findDoctor->DoctorID;
                $returnArray['doctors']['name'] = $findDoctor->Name;
                //$returnArray['doctors']['image'] = URL::to('/assets/'.$findDoctor->image);
                $returnArray['doctors']['image'] = $findDoctor->image;
                $returnArray['doctors']['email'] = $findDoctor->Email;
                $returnArray['doctors']['specialty'] = $findDoctor->Specialty;
                $returnArray['doctors']['bookdate'] = $currentdate;
                
                $returnArray['doctors']['doctorslot']['doctorslotid'] = $findDoctorSlot->DoctorSlotID;
                $returnArray['doctors']['doctorslot']['clinicsession'] = $findDoctorSlot->ClinicSession;
                $returnArray['doctors']['doctorslot']['consultationcharge'] = $findDoctorSlot->ConsultationCharge;
                $returnArray['doctors']['doctorslot']['timeslot'] = $findDoctorSlot->TimeSlot;
                $returnArray['doctors']['doctorslot']['queuenumber'] = $findDoctorSlot->QueueNumber;
                $returnArray['doctors']['doctorslot']['queuetime'] = $findDoctorSlot->QueueTime;
                
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
                
                if($findSlotDetails){
                    if($findDoctorSlot->ClinicSession==3 || $findDoctorSlot->ClinicSession==2){
                
                    foreach($findSlotDetails as $slotDetail){
                        $findSlotAppointment = self::FindSlotBooking($slotDetail->SlotDetailID,$currentdate);
                        if(!empty($findSlotAppointment)){
                            $findSlotUser = Auth_Library::FindUserDetails($findSlotAppointment->UserID);
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
                            $slotcount[] = count($findSlotAppointment);
                            if($findSlotAppointment->Status==2){$slotcompleted[] = count($findSlotAppointment); } 
                             
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
                }else{
                    $returnArray['doctors']['slot-details'] = null;
                }
                }else{
                    $returnArray['doctors']['slot-details'] = null;
                }
                
                
                if($findQueueAppointment){
                    foreach($findQueueAppointment as $fqueueAppoint){
                        $queueArray['appointmentid'] = $fqueueAppoint->UserAppoinmentID;
                        $queueArray['bookno'] = $fqueueAppoint->BookNumber;
                        $queueArray['status'] = $fqueueAppoint->Status;
                        $queueArray['bookdate'] = $fqueueAppoint->BookDate;
                        $findSlotUser = Auth_Library::FindUserDetails($fqueueAppoint->UserID);
                        $queueArray['user']['userid'] = $findSlotUser->UserID;
                        $queueArray['user']['name'] = $findSlotUser->Name;
                        $queueArray['user']['email'] = $findSlotUser->Email;
                        $queueAppoint[] = $queueArray;
                        if($fqueueAppoint->Status==2){ $queuecompleted[] = count($findQueueAppointment); }
                    }
                    $queuecount = count($findQueueAppointment);
                    $returnArray['doctors']['queue-booking'] = $queueAppoint;
                }else{                          
                    $returnArray['doctors']['queue-booking'] = null;
                } 
                
                $totalBookings = $queuecount + count($slotcount) - $cancelQueueTotal;
                $totalCompleted = count($queuecompleted) + count($slotcompleted);
                $returnArray['doctors']['doctorslot']['totalbooking'] = $totalBookings;
                $returnArray['doctors']['doctorslot']['completed'] = $totalCompleted;
                $returnArray['doctors']['doctorslot']['pending'] = $totalBookings - $totalCompleted;
                return $returnArray;
            }else {return 0; }
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }


    /* Use          :   Used to find Doctor slots 
     * Access       :   No public direct access allowed
     * Parameter    :   Doctor id
     */
    public static function FindDoctorSlot($doctorid){
        if(!empty($doctorid)){
            $doctorslot = new DoctorSlots();
            $findDoctorSlot = $doctorslot->DoctorSlotForDoctor($doctorid);
            if($findDoctorSlot){
                return $findDoctorSlot;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    /* Use          :   Used to find doctor details
     * Access       :   Public 
     * Parameter    :   doctor id
     */
    public static function FindDoctor($doctorid){
        if(!empty($doctorid)){
            $doctor = new Doctor();
            $findDoctor = $doctor->FindDoctor($doctorid);
            if($findDoctor){
                return $findDoctor;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        } 
    }
    
    /* Use          :   Used to find slot details in particular date
     * Access       :   Public 
     * Parameter    :   doctorslot id, slot type and date
     */
    public static function FindSlotdetailsWithDate($doctorslotid,$slottype,$currentdate){
        if(!empty($doctorslotid) && !empty($currentdate) && !empty($slottype)){
            $slotdetails = new DoctorSlotDetails();
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
    
    /* Use          :   Used to Slots appointment 
     * Accesss      :   Public 
     * Parameter    :   Slotdetail id and book date
     */
    public static function FindSlotBooking($slotdetailid,$bookdate){
        $appointment = new UserAppoinment();
        $findSlotBook = $appointment->findSlotBooking($slotdetailid,$bookdate);
        if($findSlotBook){
            return $findSlotBook;
        }else{
            return FALSE;
        }
    }
    
    /* Use          :   Used to find Queue appointment 
     * Access       ;   Public 
     * Parameter    :   Doctorslot id and date
     */
    public static function FindAppointmentQueue($doctorslotid,$bookdate){
        $appointment = new UserAppoinment();
        $findQueueBook = $appointment->findQueueBooking($doctorslotid,$bookdate);
        if($findQueueBook){
            return $findQueueBook;
        }else{
            return FALSE;
        }
    }
    
    /* Use          :   Used to find Stoped queue information
     * Access       :   Public 
     * Parameter    :   doctorslot id and date
     */
    public static function DoctorSlotManageByDate($doctorslotid,$currentdate){
        $doctorslotmanage = new DoctorSlotsManage();
        $findSlotManage = $doctorslotmanage->DoctorSlotManageByDate($doctorslotid,$currentdate);
        if($findSlotManage){
            return $findSlotManage;
        }else{
            return FALSE;
        }
    }
    
    /* Use          :   Used to find cancelled appointment 
     * Access       :   Public 
     * Parameter    :   doctorslot id and date
     */
    public static function CancelledAppointmentQueue($doctorslotid,$bookdate){
        $appointment = new UserAppoinment();
        $findQueueBook = $appointment->findCancelledQueueBooking($doctorslotid,$bookdate);
        if($findQueueBook){
            return $findQueueBook;
        }else{
            return 0;
        } 
    }
    
    /*
     * 
     * 
     */
    public static function DoctorBooking($bookingdata,$mainbooktype){
        if(count($bookingdata) > 0){
            $doctorslot = $bookingdata['doctorslotid'];
            $findDoctorSlot = Doctor_Library::FindDoctorSlotClinic($doctorslot);
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
                $bookno = 0;
            }
            $bookno = intval($bookno); 
            $findExistingUser = Auth_Library::FindUserEmail($bookingdata['email']);
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

                $findUserID = Auth_Library::AddNewUser($userArray);
                if($findUserID){
                    $emailDdata['emailName']= $bookingdata['name'];
                    $emailDdata['emailPage']= 'email-templates.welcome';
                    $emailDdata['emailTo']= $bookingdata['email'];
                    $emailDdata['emailSubject'] = 'Welcome to Mednefits';

                    $emailDdata['activeLink'] = "<a href='".URL::to('provider-portal-login')."'> Find out more </a>";
                    EmailHelper::sendEmail($emailDdata);
                }
            }
            if($findUserID && $findDoctorSlot){
                $formatDate = date("d-m-Y", strtotime($bookingdata['bookdate']));
                $dataArray['userid'] = $findUserID;
                $dataArray['booktype'] = $mainbooktype;
                $dataArray['doctorslotid'] = $doctorslot;
                $dataArray['slotdetailid'] = $slotdetailid;
                $dataArray['mediatype'] = 1;
                $dataArray['booknumber'] = $bookno;
                $dataArray['bookdate'] = $formatDate;

                $newAppointment = self::AddNewAppointment($dataArray);
                if($newAppointment){
                    $emailDdata1['bookingTime'] = $getslotTime;
                    $emailDdata1['bookingNo'] = $bookno;
                    $emailDdata1['bookingDate'] = $formatDate; 
                    $emailDdata1['doctorName'] = $findDoctorSlot->DName.' , '.$findDoctorSlot->Specialty;
                    $emailDdata1['clinicName'] = $findDoctorSlot->CName;
                    $emailDdata1['clinicAddress'] = $findDoctorSlot->Address;
                    
                    
                    $emailDdata1['emailName']= $bookingdata['name'];
                    $emailDdata1['emailPage']= 'email-templates.booking';
                    $emailDdata1['emailTo']= $bookingdata['email'];
                    $emailDdata1['emailSubject'] = 'Thank you for making your clinic reservation';
                    //$emailDdata['activeLink'] = "<a href='".URL::to('provider-portal-login')."'> Find out more </a>";
                    EmailHelper::sendEmail($emailDdata1);
                    $emailDdata1 = null;    
                    if($mainbooktype == 1){
                        $updateArray['slotdetailid'] = $slotdetailid;
                        $updateArray['Available'] = 2;
                        $updateArray['updated_at'] = time();
                        self::UpdateSlotDetails($updateArray);
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
    
    
    public static function AddNewAppointment($dataArray){
        $userappointment = new UserAppoinment();
        if(is_array($dataArray) && count($dataArray)>0){
            $addAppointment = $userappointment->insertUserAppointment($dataArray);
            if($addAppointment){
                return $addAppointment;
            }else{
                return FALSE;
            }
        }
    }
    
    public static function UpdateSlotDetails($updateArray){
        $slotdetail = new DoctorSlotDetails();
        if(is_array($updateArray) && count($updateArray)>0){
            $updated = $slotdetail->updateSlotDetails($updateArray);
            if($updated){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        } 
    }
    
    public static function AjaxDoctorBooking(){
        $inputdata = Input::all();
        if(!empty($inputdata['currentdate'])){
            $formatDate = date("d-m-Y", strtotime($inputdata['currentdate']));
        }else{
            $formatDate = date('d-m-Y');
        }
        $findClinicDoctors = self::ProcessDoctorHome($inputdata['doctorid'],$formatDate);
        if($findClinicDoctors !=0){
            $returnArray['doctors'] = $findClinicDoctors;
            $view = View::make('ajax.doctor-home', $returnArray);
            return $view;
        }else{
            return 0;
        }    
    }
    
    /* Use          :   Used to conclude the appointment
     * Access       :   Public 
     * Third party  :   Push notification
     */
    public static function DoctorDiagnosis(){
        $inputdata = Input::all();
        if(is_array($inputdata) && !empty($inputdata)){
            $dataArray['appointment'] = $inputdata['appointment'];
            $dataArray['diagnosis'] = $inputdata['diagnosis'];      
            $inserted = self::AddAppointmentDetails($dataArray);
            if($inserted){
                $updateArray['Status'] = 2; 
                $updateArray['updated_at'] = time(); 
                $updateAppointment = self::UpdateAppointment($updateArray,$inputdata['appointment']);
                if($updateAppointment){
                    $findAppointment = self::FindAppointment($inputdata['appointment']);
                    $findDoctorSlot = Doctor_Library::FindDoctorSlotClinic($findAppointment->DoctorSlotID);
                    $findUserDetails = Auth_Library::FindUserDetails($findAppointment->UserID);
                    //Send confirmation email 
                    //$emailDdata['bookingTime'] = $getslotTime;
                    $emailDdata['bookingNo'] = $findAppointment->BookNumber;
                    $emailDdata['bookingDate'] = $findAppointment->BookDate; 
                    $emailDdata['pateintName'] = $findUserDetails->Name;
                    $emailDdata['doctorName'] = $findDoctorSlot->DName;
                    $emailDdata['clinicName'] = $findDoctorSlot->CName;
                    $emailDdata['clinicAddress'] = $findDoctorSlot->Address;
                    
                    
                    $emailDdata['emailName']= $findUserDetails->Name;
                    $emailDdata['emailPage']= 'email-templates.doctor-diagnosis';
                    $emailDdata['emailTo']= $findUserDetails->Email;
                    $emailDdata['emailSubject'] = 'Your session is concluded';
                    //$emailDdata['activeLink'] = "<a href='".URL::to('provider-portal-login')."'> Find out more </a>";
                    EmailHelper::sendEmail($emailDdata);
                    
                    //Push for concluded
                    $findDeviceToken = AuthLibrary::FindDeviceToken($findAppointment->UserID);
                    if($findDeviceToken){ 
                        $content = 'Your session is concluded';
                        $devices = array($findDeviceToken->Token);
                        $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                        PushLibrary::PushSingleDevice($content,$data,$devices);
                    }
                    if($findAppointment->BookType==0){
                            //$findQueueReminder = self::FindQueueReminder($findAppointment->DoctorSlotID,$findAppointment->BookDate);
                            $findQueueReminder = self::FindRemindQueuesForPush($findAppointment->DoctorSlotID,$findAppointment->BookDate,$findAppointment->BookNumber);   
                            if($findQueueReminder){
                                foreach($findQueueReminder as $findqr){
                                    $devices[] = $findqr->Token;
                                }
                                //$findDeviceToken = AuthLibrary::FindDeviceToken($findQueueReminder->UserID);
                                $content = 'Reminder to your session';
                                //$devices = array($findqr->Token);
                                $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                                PushLibrary::PushSingleDevice($content,$data,$devices);
                            }
                        //$findQueueSave = self::FindQueueSaving($findAppointment->BookType,$findAppointment->DoctorSlotID,$findAppointment->BookDate);
                        $findQueueSave = self::FindQueueForProcess($findAppointment->DoctorSlotID,$findAppointment->BookDate,$findAppointment->BookNumber);
                        if($findQueueSave){
                            $findExistingQueueSaving = self::FindQueueReminder($findAppointment->DoctorSlotID,$findAppointment->BookDate);
                            if($findExistingQueueSaving){
                                $updateArray['Status'] = 0; 
                                $updateArray['updated_at'] = time();
                                self::UpdateAppointment($updateArray,$findExistingQueueSaving->UserAppoinmentID);
                            }
                            $updateArray['Status'] = 1; 
                            $updateArray['updated_at'] = time(); 
                            $updateAppointment = self::UpdateAppointment($updateArray,$findQueueSave->UserAppoinmentID);
                            if($updateAppointment){ 
                                $findDeviceToken = AuthLibrary::FindDeviceToken($findQueueSave->UserID);
                                if($findDeviceToken){
                                    $content = 'Your session is started';
                                    $devices = array($findDeviceToken->Token);
                                    $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                                    PushLibrary::PushSingleDevice($content,$data,$devices);
                                } 
                                //$findQueuePeople = self::FindQueuePeople($findAppointment->DoctorSlotID,$findAppointment->BookDate,4);
                                $findQueuePeople = self::FindQueuePeopleAhead($findAppointment->DoctorSlotID,$findAppointment->BookDate,3,$findAppointment->BookNumber);
                                
                                if($findQueuePeople){
                                    if(!empty($findQueuePeople[2])){ 
                                        $findDeviceToken = AuthLibrary::FindDeviceToken($findQueuePeople[2]->UserID);
                                        if($findDeviceToken){
                                            $content = '3 People ahead to your session';
                                            $devices = array($findDeviceToken->Token);
                                            $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                                            PushLibrary::PushSingleDevice($content,$data,$devices);
                                        }
                                    } 
                                }
                            }
                        }
                    }elseif($findAppointment->BookType==1){
                        $findAppointSlotDetails = self::FindAppointmentSlotDetail($findAppointment->DoctorSlotID,$findAppointment->SlotDetailID,$findAppointment->BookDate);
                        //$findSlotReminder = self::FindSlotReminder($findAppointment->DoctorSlotID,$findAppointment->BookDate);
                        $findSlotReminder = self::FindSlotReminderList($findAppointment->DoctorSlotID,$findAppointment->BookDate,$findAppointSlotDetails->Time);
                        if($findSlotReminder){
                            foreach($findSlotReminder as $slotRemind){
                                $devices[] = $slotRemind->Token;
                            }
                            $content = 'Reminder to your session';
                            $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                            PushLibrary::PushSingleDevice($content,$data,$devices);
                        }
                        /*if($findSlotReminder){ print_r($findSlotReminder);
                            $findDeviceToken = AuthLibrary::FindDeviceToken($findSlotReminder->UserID);
                                if($findDeviceToken){
                                    $content = 'Your session reminder';
                                    $devices = array($findDeviceToken->Token);
                                    $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                                    //PushLibrary::PushSingleDevice($content,$data,$devices);
                                }
                        }*/
                        //$findSlotSave = self::FindSlotSaving($findAppointment->BookType,$findAppointment->DoctorSlotID,$findAppointment->BookDate);
                        $findSlotSave = self::FindNextSlotSaving($findAppointment->DoctorSlotID,$findAppointment->BookDate,$findAppointSlotDetails->Time);
                        if($findSlotSave){
                            $findExistingSlotSaving = self::FindSlotReminder($findAppointment->DoctorSlotID,$findAppointment->BookDate);
                            if($findExistingSlotSaving){
                                $updateArray['Status'] = 0; 
                                $updateArray['updated_at'] = time();
                                self::UpdateAppointment($updateArray,$findExistingSlotSaving->UserAppoinmentID);
                            }
                            
                            $updateArray['Status'] = 1; 
                            $updateArray['updated_at'] = time(); 
                            $updateAppointment = self::UpdateAppointment($updateArray,$findSlotSave->UserAppoinmentID);
                            if($updateAppointment){
                                $findDeviceToken = AuthLibrary::FindDeviceToken($findSlotSave->UserID);
                                if($findDeviceToken){
                                    $content = 'Your session is started';
                                    $devices = array($findDeviceToken->Token);
                                    $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                                    PushLibrary::PushSingleDevice($content,$data,$devices);
                                }
                                
                                //$findSlotsPeople = self::FindSlosPeople($findAppointment->DoctorSlotID,$findAppointment->BookDate,3);
                                $findSlotsPeople = self::FindSlosPeopleAhead($findAppointment->DoctorSlotID,$findAppointment->BookDate,$findAppointSlotDetails->Time,3);
                                if($findSlotsPeople){
                                    if(!empty($findSlotsPeople[2])){ 
                                        print_r($findSlotsPeople[2]);
                                        $findDeviceToken = AuthLibrary::FindDeviceToken($findSlotsPeople[2]->UserID);
                                        if($findDeviceToken){
                                            $content = '3 People ahead to your session';
                                            $devices = array($findDeviceToken->Token);
                                            $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                                            PushLibrary::PushSingleDevice($content,$data,$devices);
                                        }
                                    }
                                }   
                                
                            }
                        }
                    }      
                }
                return $inserted;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    public static function DoctorDiagnosis_ORIGINAL(){
        $inputdata = Input::all();
        if(is_array($inputdata) && !empty($inputdata)){
            $dataArray['appointment'] = $inputdata['appointment'];
            $dataArray['diagnosis'] = $inputdata['diagnosis'];      
            $inserted = self::AddAppointmentDetails($dataArray);
            if($inserted){
                $updateArray['Status'] = 2; 
                $updateArray['updated_at'] = time(); 
                $updateAppointment = self::UpdateAppointment($updateArray,$inputdata['appointment']);
                if($updateAppointment){
                    $findAppointment = self::FindAppointment($inputdata['appointment']);
                    //Push for concluded
                    $findDeviceToken = AuthLibrary::FindDeviceToken($findAppointment->UserID);
                    if($findDeviceToken){
                        $content = 'Your session is concluded';
                        $devices = array($findDeviceToken->Token);
                        $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                        PushLibrary::PushSingleDevice($content,$data,$devices);
                    }
                    if($findAppointment->BookType==0){
                            $findQueueReminder = self::FindQueueReminder($findAppointment->DoctorSlotID,$findAppointment->BookDate);
                            if($findQueueReminder){
                                $findDeviceToken = AuthLibrary::FindDeviceToken($findQueueReminder->UserID);
                                if($findDeviceToken){
                                    $content = 'Reminder to your session';
                                    $devices = array($findDeviceToken->Token);
                                    $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                                    PushLibrary::PushSingleDevice($content,$data,$devices);
                                }
                            }
                        $findQueueSave = self::FindQueueSaving($findAppointment->BookType,$findAppointment->DoctorSlotID,$findAppointment->BookDate);
                        if($findQueueSave){
                            $updateArray['Status'] = 1; 
                            $updateArray['updated_at'] = time(); 
                            $updateAppointment = self::UpdateAppointment($updateArray,$findQueueSave->UserAppoinmentID);
                            if($updateAppointment){
                                $findQueuePeople = self::FindQueuePeople($findAppointment->DoctorSlotID,$findAppointment->BookDate,3);
                                if($findQueuePeople){
                                    $findDeviceToken = AuthLibrary::FindDeviceToken($findQueuePeople->UserID);
                                    if($findDeviceToken){
                                        $content = '3 People ahead to your session';
                                        $devices = array($findDeviceToken->Token);
                                        $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                                        PushLibrary::PushSingleDevice($content,$data,$devices);
                                    }
                                }
                                $findDeviceToken = AuthLibrary::FindDeviceToken($findQueueSave->UserID);
                                if($findDeviceToken){
                                    $content = 'Your session is started';
                                    $devices = array($findDeviceToken->Token);
                                    $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                                    PushLibrary::PushSingleDevice($content,$data,$devices);
                                }   
                            }
                        }
                    }elseif($findAppointment->BookType==1){
                        $findSlotReminder = self::FindSlotReminder($findAppointment->DoctorSlotID,$findAppointment->BookDate);
                        if($findSlotReminder){
                            $findDeviceToken = AuthLibrary::FindDeviceToken($findSlotReminder->UserID);
                                if($findDeviceToken){
                                    $content = 'Your session reminder';
                                    $devices = array($findDeviceToken->Token);
                                    $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                                    PushLibrary::PushSingleDevice($content,$data,$devices);
                                }
                        }
                        $findSlotSave = self::FindSlotSaving($findAppointment->BookType,$findAppointment->DoctorSlotID,$findAppointment->BookDate);
                        if($findSlotSave){
                            $updateArray['Status'] = 1; 
                            $updateArray['updated_at'] = time(); 
                            $updateAppointment = self::UpdateAppointment($updateArray,$findSlotSave->UserAppoinmentID);
                            if($updateAppointment){
                                $findSlotsPeople = self::FindSlosPeople($findAppointment->DoctorSlotID,$findAppointment->BookDate,3);
                                if($findSlotsPeople){
                                    $findDeviceToken = AuthLibrary::FindDeviceToken($findSlotsPeople->UserID);
                                    if($findDeviceToken){
                                        $content = '3 People ahead to your session';
                                        $devices = array($findDeviceToken->Token);
                                        $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                                        PushLibrary::PushSingleDevice($content,$data,$devices);
                                    }
                                } 
                                $findDeviceToken = AuthLibrary::FindDeviceToken($findSlotSave->UserID);
                                if($findDeviceToken){
                                    $content = 'Your session is started';
                                    $devices = array($findDeviceToken->Token);
                                    $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                                    PushLibrary::PushSingleDevice($content,$data,$devices);
                                }
                            }
                        }
                    }      
                }
                return $inserted;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    /* Use          :   Used to add patient diagnosis by Doctor
     * Access       :   Public 
     * Parameter    :   Array
     */ 
    public static function AddAppointmentDetails($dataArray){
        $userappointmentdetail = new UserAppointmentDetails();
        if(is_array($dataArray) && count($dataArray)>0){
            $appointmentDetail = $userappointmentdetail->InsertAppointmentDetails($dataArray);
            if($appointmentDetail){
                return $appointmentDetail;
            }else{
                return FALSE;
            }
        }
    }
    
    public static function UpdateAppointment($dataArray,$appointid){
        $userappointment = new UserAppoinment();
        $updated = $userappointment->updateUserAppointment($dataArray,$appointid);
        if($updated){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    public static function FindAppointment($appointid){
        $userappointment = new UserAppoinment();
        $findAppointment = $userappointment->getAppointment($appointid);
        if($findAppointment){
            return $findAppointment;
        }else{
            return FALSE;
        }
    }
    
    public static function FindQueueSaving($booktype,$doctorslotid,$bookdate){
        $userappointment = new UserAppoinment();
        $findQueueSaving = $userappointment->FindQueueSaving($booktype,$doctorslotid,$bookdate);
        if($findQueueSaving){
            return $findQueueSaving;
        }else{
            return FALSE;
        }
    }
    public static function FindQueueForProcess($doctorslotid,$bookdate,$booknumber){
        $userappointment = new UserAppoinment();
        $findQueueSaving = $userappointment->FindQueueForProcess($doctorslotid,$bookdate,$booknumber);
        if($findQueueSaving){
            return $findQueueSaving;
        }else{
            return FALSE;
        }
    }
    public static function FindQueuePeople($doctorslotid,$bookdate,$limit){
        $userappointment = new UserAppoinment();
        $findQueuePeople = $userappointment->FindQueuePeople($doctorslotid,$bookdate,$limit);
        if($findQueuePeople){
            return $findQueuePeople;
        }else{
            return FALSE;
        }
    }
    public static function FindQueuePeopleAhead($doctorslotid,$bookdate,$limit,$booknumber){
        $userappointment = new UserAppoinment();
        $findQueuePeople = $userappointment->FindQueuePeopleAhead($doctorslotid,$bookdate,$limit,$booknumber);
        if($findQueuePeople){
            return $findQueuePeople;
        }else{
            return FALSE;
        }
    }
    
    public static function FindQueueReminder($doctorslotid,$bookdate){
        $userappointment = new UserAppoinment();
        $findQueueSaving = $userappointment->FindQueueReminder($doctorslotid,$bookdate);
        if($findQueueSaving){
            return $findQueueSaving;
        }else{
            return FALSE;
        }
    }
    public static function FindRemindQueuesForPush($doctorslotid,$bookdate,$booknumber){
        $userappointment = new UserAppoinment();
        $findRemindQueues = $userappointment->FindRemindQueuesForPush($doctorslotid,$bookdate,$booknumber);
        if($findRemindQueues){
            return $findRemindQueues;
        }else{
            return FALSE;
        }
    }
    
    
    public static function FindSlotSaving($booktype,$doctorslotid,$bookdate){
        $userappointment = new UserAppoinment();
        $findSlotSaving = $userappointment->FindSlotSaving($booktype,$doctorslotid,$bookdate);
        if($findSlotSaving){
            return $findSlotSaving;
        }else{
            return FALSE;
        }
    }
    public static function FindNextSlotSaving($doctorslotid,$bookdate,$slottime){
        $userappointment = new UserAppoinment();
        $findSlotSaving = $userappointment->FindNextSlotSaving($doctorslotid,$bookdate,$slottime);
        if($findSlotSaving){
            return $findSlotSaving;
        }else{
            return FALSE;
        }
    }
    
    public static function FindSlosPeople($doctorslotid,$bookdate,$limit){
        $userappointment = new UserAppoinment();
        $findSlotSaving = $userappointment->FindSlosPeople($doctorslotid,$bookdate,$limit);
        if($findSlotSaving){
            return $findSlotSaving;
        }else{
            return FALSE;
        }
    }
    public static function FindSlosPeopleAhead($doctorslotid,$bookdate,$slottime,$limit){
        $userappointment = new UserAppoinment();
        $findSlotSaving = $userappointment->FindSlosPeopleAhead($doctorslotid,$bookdate,$slottime,$limit);
        if($findSlotSaving){
            return $findSlotSaving;
        }else{
            return FALSE;
        }
    }
    
    public static function FindSlotReminder($doctorslotid,$bookdate){
        $userappointment = new UserAppoinment();
        $findSlotSaving = $userappointment->FindSlosReminder($doctorslotid,$bookdate);
        if($findSlotSaving){
            return $findSlotSaving;
        }else{
            return FALSE;
        }
    }
    public static function FindSlotReminderList($doctorslotid,$bookdate,$slottime){
        $userappointment = new UserAppoinment();
        $findSlotSaving = $userappointment->FindSlosReminderList($doctorslotid,$bookdate,$slottime);
        if($findSlotSaving){
            return $findSlotSaving;
        }else{
            return FALSE;
        }
    }
    public static function FindAppointmentSlotDetail($doctorslotid,$slotdetailid, $bookdate){
        $slotdetails = new DoctorSlotDetails();
        $returnSlotDetail = $slotdetails->FindAppointmentSlotDetail($doctorslotid,$slotdetailid,$bookdate);
        if($returnSlotDetail){
            return $returnSlotDetail;
        }else{
            return FALSE;
        }
    }
    
    public static function GetTodayAppointment($today){
        if($today){
            $userappointment = new UserAppoinment();
            $todayAppointment = $userappointment->GetTodayAppointment($today);
            if($todayAppointment){
                return $todayAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }    
    }
    public static function GetTodayAppointmentInHours($today){
        if($today){
            $userappointment = new UserAppoinment();
            $todayAppointment = $userappointment->GetTodayAppointmentInHours($today);
            if($todayAppointment){
                return $todayAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }    
    }
    public static function FindTotalQueueBooking($doctorslotid,$bookdate){
        $userappointment = new UserAppoinment();
        $findAppointmentCount = $userappointment->TotalQueueBooking($doctorslotid,$bookdate);
        if($findAppointmentCount){
            return $findAppointmentCount;
        }else{
            return FALSE;
        }
    }
    public static function FindTotalSlotBooking($doctorslotid,$bookdate){
        $userappointment = new UserAppoinment();
        $findAppointmentCount = $userappointment->TotalSlotBooking($doctorslotid,$bookdate);
        if($findAppointmentCount){
            return $findAppointmentCount;
        }else{
            return FALSE;
        }
    }
    public static function FindClinicDoctors($clinicid){
        if(!empty($clinicid)){
           $doctorAvailable = new DoctorAvailability();
            $findClinicDoctors = $doctorAvailable->findDoctorsForClinic($clinicid);
            if($findClinicDoctors){
                return $findClinicDoctors;
            } 
        }else{
            return FALSE;
        }
    }
    public static function FindActiveClinicDoctors($clinicid){
        if(!empty($clinicid)){
           $doctorAvailable = new DoctorAvailability();
            $findClinicDoctors = $doctorAvailable->FindActiveDoctorsForClinic($clinicid);
            if($findClinicDoctors){
                return $findClinicDoctors;
            } 
        }else{
            return FALSE;
        }
    }
    public static function FindDoctorDetails($doctorid){
        if(!empty($doctorid)){
            $doctor = new Doctor();
            $findDoctor = $doctor->FindDoctorDetails($doctorid);
            if($findDoctor){
                return $findDoctor;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        } 
    }
    public static function FindClinicDoctorSlot($doctorid,$clinicid){
        if(!empty($doctorid) && !empty($clinicid)){
            $doctorslot = new DoctorSlots();
            $findDoctorslot = $doctorslot->FindClinicDoctorSlot($doctorid,$clinicid);
            return $findDoctorslot;
        }else{
            return FALSE;
        }           
    }
    
    /* This function is used to manage Slot and Queue 
     * Access : Public 
     * 
     */
    public static function ManageDoctorSlot(){
        $alldata = Input::all();
        //$doctorslot = new DoctorSlots();
        $dataArray = array(); 
        
        if(!empty($alldata)){
            if(empty($alldata['doctorslotid'])){ 
                $dataArray['doctorid']= $alldata['doctorid'];
                $dataArray['clinicid']= $alldata['clinicid'];
                $dataArray['queueno'] = $alldata['queueno'];
                $dataArray['queuetime'] = $alldata['queuetime'];
                $dataArray['clinicsession'] = $alldata['clinicsession'];
                $dataArray['consultationcharge']= $alldata['consultcharge'];
                $dataArray['timeslot']= $alldata['slot'];
                $dataArray['starttime']= "";
                $dataArray['endtime']= ""; 
                if($alldata['clinicsession'] !=0){
                    $insertSlot = self::InsertDoctorSlot($dataArray);
                }else{
                    $insertSlot = FALSE;
                }
                if($insertSlot){
                    $findDoctorSlot = self::FindDoctorSlotBySlotID($insertSlot);
                    $slottype = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);

                    $returnObject['doctorslotexist'] = $insertSlot;

                    $returnObject['clinicsession'] = $findDoctorSlot->ClinicSession;
                    if($findDoctorSlot->ClinicSession==1 || $findDoctorSlot->ClinicSession==3){       
                        $returnObject['queueno'] = $findDoctorSlot->QueueNumber;
                        $returnObject['queuetime'] = $findDoctorSlot->QueueTime;
                    }else{
                        $returnObject['queueno'] = 0;
                        $returnObject['queuetime'] = 0;
                    }
                    if($findDoctorSlot->ClinicSession==2 || $findDoctorSlot->ClinicSession==3){       
                        $returnObject['timeslot'] = $findDoctorSlot->TimeSlot;
                    }else{
                        $returnObject['timeslot'] = 0;
                    }
                    if(!empty($findDoctorSlot->ConsultationCharge)){
                        $returnObject['consultcharge'] = $findDoctorSlot->ConsultationCharge;
                    }else{
                        $returnObject['consultcharge'] = 000;
                    }
                    //$returnObject['doctorid'] = $findDoctorSlot->DoctorID;
                    //$returnObject['clinicid'] = $findDoctorSlot->ClinicID;
                    
                    $returnObject['default'] = 0;
                    $returnObject['slotdetails'] = null;
                    $returnObject['today'] = date("d-m-Y");
                    $view = View::make('ajax.manage-slot-settings', $returnObject);
                    return $view;
                }else{
                    return 0;
                }
            }else{
                $findDoctorSlot = self::FindDoctorSlotBySlotID($alldata['doctorslotid']);

                $dataArray['doctorslotid']= $alldata['doctorslotid'];
                $dataArray['consultationcharge']= $alldata['consultcharge'];
                $dataArray['queuetime'] = $alldata['queuetime'];
                $dataArray['active']= 1; 
                $returnObject['today'] = date("d-m-Y");               
                
                $countQueueBooking = self::FindTotalQueueBooking($findDoctorSlot->DoctorSlotID,date("d-m-Y"));
                if($countQueueBooking < $alldata['queueno']){
                    $dataArray['queueno'] = $alldata['queueno'];
                }
                $countSlotBooking = self::FindTotalSlotBooking($findDoctorSlot->DoctorSlotID,date("d-m-Y"));
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
                    if(empty($countQueueBooking) && empty($countSlotBooking)){
                        $dataArray['clinicsession'] = 0;
                    }                   
                }

                $updateSlot = self::DoctorSlotsUpdate($dataArray);
                if($updateSlot){  

                    $findDoctorSlot = self::FindDoctorSlotBySlotID($findDoctorSlot->DoctorSlotID);
                    $slottype = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                    $findSlotDetails = self::FindSlotDetailsByType($findDoctorSlot->DoctorSlotID,$slottype);
                    
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
                    }else{
                        $returnObject['timeslot'] = 0;
                    }
                    if(!empty($findDoctorSlot->ConsultationCharge)){
                        $returnObject['consultcharge'] = $findDoctorSlot->ConsultationCharge;
                    }else{
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
    
    public static function ManageDoctorSlot_old(){
        $alldata = Input::all();
        $doctorslot = new DoctorSlots();
        $dataArray = array(); 

        if(!empty($alldata)){
            if(empty($alldata['doctorslotid'])){ 
                $dataArray['doctorid']= $alldata['doctorid'];
                $dataArray['clinicid']= $alldata['clinicid'];
                $dataArray['queueno'] = $alldata['queueno'];
                $dataArray['queuetime'] = $alldata['queuetime'];
                $dataArray['clinicsession'] = $alldata['clinicsession'];
                $dataArray['consultationcharge']= $alldata['consultcharge'];
                $dataArray['timeslot']= $alldata['slot'];
                $dataArray['starttime']= "";
                $dataArray['endtime']= ""; 

                //$insertSlot = $doctorslot->insertDoctorSlot($dataArray);
                $insertSlot = self::InsertDoctorSlot($dataArray);
                if($insertSlot){
                    $findDoctorSlot = self::FindDoctorSlotBySlotID($insertSlot);
                    //$findDoctorSlot = $this->findDoctorSlotByID($insertSlot);
                    $slottype = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                    //$findSlotDetails = $this->FindSlotDetailsByType($findDoctorSlot->DoctorSlotID,$slottype);

                    $returnObject['doctorslotexist'] = $insertSlot;

                    $returnObject['clinicsession'] = $findDoctorSlot->ClinicSession;
                    if($findDoctorSlot->ClinicSession==1 || $findDoctorSlot->ClinicSession==3){       
                        $returnObject['queueno'] = $findDoctorSlot->QueueNumber;
                        $returnObject['queuetime'] = $findDoctorSlot->QueueTime;
                    }else{
                        $returnObject['queueno'] = 0;
                        $returnObject['queuetime'] = 0;
                    }
                    if($findDoctorSlot->ClinicSession==2 || $findDoctorSlot->ClinicSession==3){       
                        $returnObject['timeslot'] = $findDoctorSlot->TimeSlot;
                    }else{
                        $returnObject['timeslot'] = 0;
                    }
                    if(!empty($findDoctorSlot->ConsultationCharge)){
                        $returnObject['consultcharge'] = $findDoctorSlot->ConsultationCharge;
                    }else{
                        $returnObject['consultcharge'] = 000;
                    }
                    
                    /*if(is_array($findSlotDetails) && count($findSlotDetails)>0){
                        $myslots = array();
                        foreach($findSlotDetails as $k){  
                            $myslots[] = (array) $k;
                        }
                        $returnObject['slotdetails'] = $myslots;
                    }else{
                        $returnObject['slotdetails'] = null;
                    }*/
                    
                    $returnObject['default'] = 0;
                    $returnObject['slotdetails'] = null;
                    $returnObject['today'] = date("d-m-Y");
                    $view = View::make('ajax.manage-slot-settings', $returnObject);
                    return $view;
                }else{
                    return 0;
                }
            }else{
                $findDoctorSlot = self::FindDoctorSlotBySlotID($alldata['doctorslotid']);
                //$findDoctorSlot = $this->findDoctorSlotByID($alldata['doctorslotid']);

                $dataArray['doctorslotid']= $alldata['doctorslotid'];
                $dataArray['consultationcharge']= $alldata['consultcharge'];
                $dataArray['queuetime'] = $alldata['queuetime'];
                $dataArray['active']= 1; 
                $returnObject['today'] = date("d-m-Y");
                
                //$dataArray['doctorid']= $alldata['doctorid'];               
                //$dataArray['clinicsession']= $alldata['clinicsession'];      
                //$dataArray['timeslot']= $alldata['slot'];
                //$dataArray['queueno'] = $alldata['queueno']; 
                //$dataArray['starttime']= 0;
                //$dataArray['endtime']= 0;              
                //$returnObject['today'] = '08-12-2014';
                
                //$countQueueBooking = Doctor_Library::FindTotalQueueBooking($findDoctorSlot->DoctorSlotID,date("d-m-Y"));
                $countQueueBooking = self::FindTotalQueueBooking($findDoctorSlot->DoctorSlotID,date("d-m-Y"));
                if($countQueueBooking < $alldata['queueno']){
                    $dataArray['queueno'] = $alldata['queueno'];
                }
                //$countSlotBooking = Doctor_Library::FindTotalSlotBooking($findDoctorSlot->DoctorSlotID,date("d-m-Y"));
                $countSlotBooking = self::FindTotalSlotBooking($findDoctorSlot->DoctorSlotID,date("d-m-Y"));
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
                    if(empty($countQueueBooking) && empty($countSlotBooking)){
                        $dataArray['clinicsession'] = 0;
                    }                   
                }
                //$updateSlot = $this->DoctorSlotsUpdate($dataArray);
                $updateSlot = self::DoctorSlotsUpdate($dataArray);
                if($updateSlot){  
                    //$findDoctorSlot = $this->findDoctorSlotByID($findDoctorSlot->DoctorSlotID);
                    //$findSlotDetails = $this->getAllSlotDetails($alldata['doctorslotid']);
                    //$findDoctorSlot = $this->findDoctorSlotByID($alldata['doctorslotid']);
                    $findDoctorSlot = self::FindDoctorSlotBySlotID($findDoctorSlot->DoctorSlotID);
                    $slottype = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                    //$findSlotDetails = $this->FindSlotDetailsByType($findDoctorSlot->DoctorSlotID,$slottype);
                    $findSlotDetails = self::FindSlotDetailsByType($findDoctorSlot->DoctorSlotID,$slottype);
                    
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
                    }else{
                        $returnObject['timeslot'] = 0;
                    }
                    if(!empty($findDoctorSlot->ConsultationCharge)){
                        $returnObject['consultcharge'] = $findDoctorSlot->ConsultationCharge;
                    }else{
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
    
    
    public static function InsertDoctorSlot($slotarray){
        if(!empty($slotarray)){
            $doctorslot = new DoctorSlots();
            $doctorslotid = $doctorslot->insertDoctorSlot($slotarray);
            if($doctorslotid){
                return $doctorslotid;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }           
    }
    public static function FindDoctorSlotBySlotID($doctorslotid){
        if(!empty($doctorslotid)){
            $doctorslot = new DoctorSlots();
            $finddoctorslot = $doctorslot->FindDoctorSlotBySlotID($doctorslotid);
            if($finddoctorslot){
                return $finddoctorslot;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }           
    }
    
    public static function FindSlotDetailsByType($doctorslotid,$slottype){
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
    public static function DoctorSlotsUpdate($updateArray){
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
    
        /* Use      :   It is used to process doctor setting information
         * Access   :   Private
         * Return   :   Array 
         */  
        public static function ManageDoctorSettings($doctorid){
            $dataArray['title'] = "Medicloud Doctor Settings";
            $findDoctor = self::FindDoctorDetails($doctorid);
            if($findDoctor){
                $findClinicForDoctor = self::FindClinicForDoctor($findDoctor->DoctorID);
                //find doctor slots
                $findDoctorSlot = self::FindDoctorSlotForDoctor($findDoctor->DoctorID);
                if($findDoctorSlot){
                    $slottype = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                    $findSlotDetails = self::FindSlotDetailsByType($findDoctorSlot->DoctorSlotID,$slottype);
                    if($findSlotDetails){ 
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
                //echo '<pre>';print_r($dataArray);echo '</pre>';
                return $dataArray;
            }else{
                return FALSE;
            }
           
        }
        
        
        public static function FindDoctorSlotForDoctor($doctorid){
            $doctorslot = new DoctorSlots();
            if(!empty($doctorid)){
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
        
        public static function FindClinicForDoctor($doctorid){
            $doctorAvailable = new DoctorAvailability();
            if(!empty($doctorid)){
                $doctorAvailable = $doctorAvailable->FindClinicForDoctor($doctorid);
                if($doctorAvailable){
                    return $doctorAvailable;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }  
        }
        
        public static function FindDoctorSlotClinic($doctorslotid){
            $doctorslot = new DoctorSlots();
            if(!empty($doctorslotid)){
                $doctorSlotClinic = $doctorslot->FindDoctorSlotClinic($doctorslotid);
                if($doctorSlotClinic){
                    return $doctorSlotClinic;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }  
        }
        public static function FindDoctorSlotDetail($slotdetailid){
            $doctorslotdetail = new DoctorSlotDetails();
            if(!empty($slotdetailid)){
                $doctorSlotDetails = $doctorslotdetail->FindSlotDetails($slotdetailid);
                if($doctorSlotDetails){
                    return $doctorSlotDetails;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }  
        }
        
    public static function NewDoctor($dataArray){
        $doctor = new Doctor();
        if(!empty($dataArray)){
            $addDoctor = $doctor->NewDoctor($dataArray);
            if($addDoctor){
                return $addDoctor;
            }else{
                return FALSE;
            }
        }
    }
    public static function AddDoctorProcedures($dataArray){
        $doctorprocedure = new DoctorProcedures();
        if(!empty($dataArray)){
            $addDoctorProcedure = $doctorprocedure->AddDoctorProcedures($dataArray);
            if($addDoctorProcedure){
                return $addDoctorProcedure;
            }else{
                return FALSE;
            }
        }
    }
    public static function FindDoctorProcedures($clinicid,$doctorid){
        if(!empty($clinicid) && !empty($doctorid)){
            $doctorprocedure = new DoctorProcedures();
            $findDoctorProcedure = $doctorprocedure->FindDoctorProcedures($clinicid,$doctorid);
            if($findDoctorProcedure){
                return $findDoctorProcedure;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
        
    public static function UpdateDoctor($dataArray){
        $doctor = new Doctor();
        if(!empty($dataArray)){
            $addDoctor = $doctor->updateDoctor($dataArray);
            if($addDoctor){
                return $addDoctor;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function UpdateProcedure($dataArray,$procedureid){
        $doctorprocedure = new DoctorProcedures();
        if(!empty($dataArray)){
            $updated = $doctorprocedure->UpdateProcedure($dataArray,$procedureid);
            if($updated){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindSingleProcedures($clinicid,$doctorid,$procedureid){
        $doctorprocedure = new DoctorProcedures();
        if(!empty($clinicid) && !empty($doctorid)){
            $procedure = $doctorprocedure->FindSingleProcedures($clinicid,$doctorid,$procedureid);
            if($procedure){
                return $procedure;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    

    
    public static function FindSingleClinicDoctor($clinicid,$doctorid){
        if(!empty($clinicid) && !empty($doctorid)){
           $doctorAvailable = new DoctorAvailability();
            $findClinicDoctor = $doctorAvailable->FindSingleClinicDoctor($clinicid,$doctorid);
            if($findClinicDoctor){
                return $findClinicDoctor;
            } 
        }else{
            return FALSE;
        }
    }
    
}
