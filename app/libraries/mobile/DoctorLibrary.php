<?php
use Carbon\Carbon;
class DoctorLibrary{
    
    public static function FindDoctorDetails(){
        
    }
    
    public static function FindDoctorCount($clinicid){
        if(!empty($clinicid)){
            $doctoryAvailable = new DoctorAvailability();
            $doctorCount = $doctoryAvailable->doctorCount($clinicid);
            if($doctorCount){
                return $doctorCount;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    public static function DoctorsInClinic($clinicid){
        $doctorAvailability = new DoctorAvailability();
        if(!empty($clinicid)){
            $doctorArray = $doctorAvailability->DoctorsInClinic($clinicid);
            if($doctorArray){
                return $doctorArray;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function findDoctorsForClinic($clinicid){
        $doctorAvailability = new DoctorAvailability();
        if(!empty($clinicid)){
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
    
    public static function findDoctor($doctorid){
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
        
        
    public static function UpdateAppointment($dataArray,$appointid){
        $userappointment = new UserAppoinment();
        if(count($dataArray) > 0 && !empty($appointid)){
            $updated = $userappointment->updateUserAppointment($dataArray,$appointid);
            if($updated){
                return $updated;
            }else{
                return FALSE;
            }  
        }else{
            return FALSE;
        }  
    }
    /* Use              :   Used to get doctor details with slot and booking
     * 
     * 
     */
    public static function DoctorDetails(){
        $doctorArray = array();
        $getDoctorID = Input::get('value'); 
        $returnObject = new stdClass();
        $currentdate = date("d-m-Y");
        if(empty($getDoctorID) || $getDoctorID == "" || $getDoctorID == null){
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmptyValues");
        }else{
            $findDoctor = self::findDoctor($getDoctorID);
            if($findDoctor){   
                //Doctor Array
                $doctorArray['doctor'] = ArrayHelper::DoctorArray($findDoctor);
                $findClinic = self::FindClinicForDoctor($findDoctor->DoctorID);
                if($findClinic){
                    //Clinic Array
                    $doctorArray['clinic'] = ArrayHelper::ClinicArray($findClinic); 
                    $panelInsurance = InsuranceLibrary::FindClinicInsurance($findClinic->ClinicID);
                    $clinicOpenStatus = StringHelper::GetClinicOpenStatus($findClinic->ClinicID);
                    $clinicOpenTime = StringHelper::GetClinicOpenTimes($findClinic->ClinicID);
                    
                    $doctorArray['clinic']['open']= $clinicOpenTime;
                    $doctorArray['clinic']['open_status']= $clinicOpenStatus;    
                    if($panelInsurance){
                        $panelCount[] = count($panelInsurance);
                        $doctorArray['clinic']['panel_insurance']['insurance_id']= $panelInsurance->CompanyID;
                        $doctorArray['clinic']['panel_insurance']['name']= $panelInsurance->Name;
                        $doctorArray['clinic']['panel_insurance']['image_url']= URL::to('/assets/'.$panelInsurance->Image);
                        //$doctorArray['clinic']['annotation_url']= URL::to('/assets/'.$panelInsurance->Annotation);
                        $doctorArray['clinic']['annotation_url']= $panelInsurance->Annotation;
                    }else{
                        $findAnnotation = InsuranceLibrary::FindAnnotation();
                        if($findAnnotation){
                            //$doctorArray['clinic']['annotation_url']= URL::to('/assets/'.$findAnnotation->Annotation);
                            $doctorArray['clinic']['annotation_url']= $findAnnotation->Annotation;
                        }else{
                            $doctorArray['clinic']['annotation_url']= null;
                        }
                        $doctorArray['clinic']['panel_insurance'] =null;
                    }
                
                //For booking information
                $findDoctorSlot = self::FindDoctorSlot($findDoctor->DoctorID,$findClinic->ClinicID);
                if(!empty($findDoctorSlot)){
                    $doctorArray['booking']['booking_option'] = $findDoctorSlot->ClinicSession;
                    $totalPatientCount = self::FindTotalBooking($findDoctorSlot->DoctorSlotID,$currentdate);
                    $slotCount['total'] = 0; $totalQueue = 0; $nowSaving = 0; $slotAvailable=0;
                    $nextAvailableNumber =1;
                    
                    $findSlotManage = self::DoctorSlotManageByDate($findDoctorSlot->DoctorSlotID,$currentdate);
                    
                    if($findDoctorSlot->ClinicSession == 1){
                    //if($findDoctorSlot->ClinicSession == 1 || $findDoctorSlot->ClinicSession == 3){    
                        //$getQueueAppointment = $appointment->getQueueDetails(0,$findDoctorSlot->DoctorSlotID,date("d-m-Y"));
                        $getQueueAppointment = self::FindQueueBookingDetails(0,$findDoctorSlot->DoctorSlotID,$currentdate);
                        if($getQueueAppointment) {                      
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
                    if($findDoctorSlot->ClinicSession == 2){
                    //if($findDoctorSlot->ClinicSession == 2 || $findDoctorSlot->ClinicSession == 3){    
                        $slotType = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                        //$findSlotDetails = $slotdetails->getBookingSlot($findDoctorSlot->DoctorSlotID,date("d-m-Y"));
                        $findSlotDetails = self::FindSlotDetails($findDoctorSlot->DoctorSlotID,$slotType, $currentdate);
                        if($findSlotDetails){    
                            $slotTime =0;
                            foreach($findSlotDetails as $sdetails){
                                if($slotTime ==0) { 
                                    if($sdetails->Available == 1){
                                    //if($sdetails->Available == 1 && ArrayHelper::ActiveTime($sdetails->Time,date("d-m-Y"))==1){    
                                    $slotAvailable = 1; $slotTime ++;
                                    } 
                                }
                                $slotCount['total'] = self::FindSlotBookingCount(1,$sdetails->SlotDetailID); 
                                //echo count(self::FindSlotBookingCount(1,$sdetails->SlotDetailID));
                            }         
                        }
                    } 
                    //$totalPatientCount = $totalQueue + $slotCount['total'];
                    
                    if($nextno ==0){$nextno="";}else{$nextno = $nextAvailableNumber;}
                    if($findSlotManage && $findSlotManage->Status==1){ $nextno="";                             
                    }elseif($nextno > $findDoctorSlot->QueueNumber){ $nextno=""; }
                   
                    if(!empty($nextno) && $slotAvailable ==1){ $typebooking =3;}
                    elseif($slotAvailable ==1 && empty($nextno)){ $typebooking =2;}
                    elseif(!empty($nextno) && $slotAvailable ==0){ $typebooking =1;}
                    else{$typebooking = 0;}
                    $doctorArray['booking']['type'] = $typebooking;

                    if($findDoctorSlot->ClinicSession == 1){
                    //if($findDoctorSlot->ClinicSession == 1 || $findDoctorSlot->ClinicSession == 3){    
                        //if(empty($nextAvailableNumber)){ $nextAvailableNumber = 1; }

                        $doctorArray['booking']['queue'] = ArrayHelper::QueueArray($findDoctorSlot, $nowSaving, $totalPatientCount, $nextAvailableNumber, $currentdate);
                        
                        if($findSlotManage){if($findSlotManage->Status == 1){$doctorArray['booking']['queue'] = null;}}
                        if($nextAvailableNumber > $findDoctorSlot->QueueNumber){$doctorArray['booking']['queue'] = null;}
                    }else{
                        $doctorArray['booking']['queue'] = null;
                    }
                    if($findDoctorSlot->ClinicSession == 2){    
                    //if($findDoctorSlot->ClinicSession == 2 || $findDoctorSlot->ClinicSession == 3){        
                        if($findSlotDetails){
                            if($findDoctorSlot->TimeSlot == '30min'){$timegap = 30;}elseif($findDoctorSlot->TimeSlot == '60min'){$timegap = 60;}
                            $slotOrder = 0; $slotArray = array();
                            foreach($findSlotDetails as $sdetails){
                                //$findSlotBooking = $appointment->getSlotBooking(1,$sdetails->SlotDetailID);
                                
                                if($slotOrder == 0){
                                    if($sdetails->Available == 1){
                                    //if($sdetails->Available == 1 && ArrayHelper::ActiveTime($sdetails->Time,date('d-m-Y'))==1){
                                        $nextavailableslot = $sdetails->SlotDetailID;
                                        $nextStartTime = $sdetails->Time;
                                        $newnexttime = substr($sdetails->Time, -2); $exactnexttime = substr($sdetails->Time,0, -2);
                                        $nextEndTime = date("H.i",strtotime('+'.$timegap.' minutes',strtotime($exactnexttime))).$newnexttime;
                                        $nextStatus = $sdetails->Available;
                                        $slotOrder ++;
                                    }
                                }
                                if(empty($nextavailableslot)){ $nextavailableslot = 0;}
                                
                                if($nextavailableslot == $sdetails->SlotDetailID){$slotArray['next_availble_slot_id'] = $nextavailableslot;}
                                else{$slotArray['next_availble_slot_id'] = 0;}
                                    //if(ArrayHelper::ActiveTime($sdetails->Time,date('d-m-Y'))==1){
                                $slotArray['slot_id'] = $sdetails->SlotDetailID;
                                $slotArray['start_time'] = $sdetails->Time;
                                $newtime = substr($sdetails->Time, -2);
                                $exactTime = substr($sdetails->Time,0, -2);
                                $slotArray['end_time'] = date("H.i",strtotime('+'.$timegap.' minutes',strtotime($exactTime))).$newtime;
                                //if(ArrayHelper::ActiveTime($sdetails->Time,date('d-m-Y'))==1){
                                $slotArray['status'] = $sdetails->Available;
                                //}else{
                                //    $slotArray['status'] = 3;
                                //}
                                    //}
                                $sdArray[] = $slotArray;
                            }
                            if(empty($nextStartTime)){ $nextStartTime =null;}
                            if(empty($nextEndTime)){ $nextEndTime =null;}
                            if(empty($nextStatus)){ $nextStatus =0;}
                            
                            $doctorArray['booking']['timeslot'] = ArrayHelper::SlotArray($findDoctorSlot,$currentdate,$totalPatientCount,$nowSaving,$nextavailableslot,$nextStartTime,$nextEndTime,$nextStatus,$sdArray);
                            
                        }else{
                            $doctorArray['booking']['timeslot'] = null;
                        }
                    }else{
                         $doctorArray['booking']['timeslot'] = null;
                    }   
                }
                $returnDoctorArray = $doctorArray;
                $returnObject->status = TRUE;
                $returnObject->data = $returnDoctorArray;
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("NoRecords");
                } 
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("NoRecords");
            }
        }
        return $returnObject;  
    }
    
    public static function FindClinicForDoctor($doctorid){
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
    public static function FindDoctorSlot($doctorid,$clinicid){
        $doctorslot = new DoctorSlots();
        if(!empty($doctorid) && !empty($clinicid)){
            $findDoctorSlot = $doctorslot->getDoctorSlot($doctorid,$clinicid);
            if($findDoctorSlot){
                return $findDoctorSlot;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    public static function DoctorSlotManageByDate($doctorslotid,$currentdate){
        $slotmanage = new DoctorSlotsManage();
        if(!empty($doctorslotid) && !empty($currentdate)){
            $findSlotManage = $slotmanage->DoctorSlotManageByDate($doctorslotid,$currentdate);
            if($findSlotManage){
                return $findSlotManage;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindStoppedQueueByDate($doctorslotid,$currentdate){
        $slotmanage = new DoctorSlotsManage();
        if(!empty($doctorslotid) && !empty($currentdate)){
            $queuestopped = $slotmanage->StopedQueueByDate($doctorslotid,$currentdate);
            if($queuestopped){
                return $queuestopped;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    public static function FindQueueBookingDetails($booktype,$doctorslotid,$currentdate){
        $appointment = new UserAppoinment();
        if(!empty($doctorslotid) && !empty($currentdate)){
            $getQueueAppointment = $appointment->getQueueDetails($booktype,$doctorslotid,$currentdate);
            if($getQueueAppointment){
                return $getQueueAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindSlotDetails($doctorslotid,$slotType,$currentdate){
        $slotdetails = new DoctorSlotDetails();
        if(!empty($doctorslotid) && !empty($slotType) && !empty($currentdate)){
            $findSlotDetails = $slotdetails->ActiveBookingSlot($doctorslotid,$slotType,$currentdate);
            if($findSlotDetails){
                return $findSlotDetails;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindActiveSlotForBooking($doctorslotid,$slotType,$currentdate){
        $slotdetails = new DoctorSlotDetails();
        if(!empty($doctorslotid) && !empty($slotType) && !empty($currentdate)){
            $findSlotDetails = $slotdetails->FindActiveSlotForBooking($doctorslotid,$slotType,$currentdate);
            if($findSlotDetails){
                return $findSlotDetails;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindSlotBookingCount($booktype,$doctorslotid){
        $appointment = new UserAppoinment();
        if(!empty($doctorslotid)){
            $findSlotBookingCount = $appointment->getSlotBooking($booktype,$doctorslotid);
            if($findSlotBookingCount){
                return count($findSlotBookingCount);
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    /* Use          :   Used to get Doctors slot and queue for future dates
     * Parameter    :   booktype, date, doctorid and clinic id
     * Access       :   public
     */
    public static function DoctorSlotsForDate($mainbooktype){
        $allUpdatedata = Input::all(); 
        $returnObject = new stdClass();    
        $norecord = 0;   
        if(count($allUpdatedata) > 0){ 
            $findDoctorSlot = self::FindDoctorSlot($allUpdatedata['doctorid'],$allUpdatedata['clinicid']);
            $currentDate = date("d-m-Y", strtotime($allUpdatedata['date']));
            if($findDoctorSlot && ArrayHelper::ActiveDate($currentDate)==1){ 
            //if($findDoctorSlot && strtotime($currentDate) >= strtotime(date("d-m-Y"))){    
                $totalPatientCount = self::FindTotalBooking($findDoctorSlot->DoctorSlotID,$currentDate);
                if($mainbooktype == 1){ // This is for slot
                    if($findDoctorSlot->ClinicSession == 2 || $findDoctorSlot->ClinicSession == 3){
                        $slotType = ArrayHelper::getSlotType($findDoctorSlot->TimeSlot);
                        $findSlotDetails = self::FindSlotDetails($findDoctorSlot->DoctorSlotID,$slotType,$currentDate);
                        if($findSlotDetails){
                            if($findDoctorSlot->TimeSlot == '30min'){$timegap = 30;}
                            elseif($findDoctorSlot->TimeSlot == '60min'){$timegap = 60;}
                            $slotOrder = 0; 
                            foreach($findSlotDetails as $sdetails){
                                if($slotOrder == 0){
                                    if($sdetails->Available == 1){
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
                                if($nextavailableslot == $sdetails->SlotDetailID){$slotArray['next_availble_slot_id'] = $nextavailableslot;}
                                else{$slotArray['next_availble_slot_id'] = 0;}
                                $slotArray['start_time'] = $sdetails->Time;
                                $newtime = substr($sdetails->Time, -2); $exacttime = substr($sdetails->Time,0, -2);
                                $slotArray['end_time'] = date("H.i",strtotime('+'.$timegap.' minutes',strtotime($exacttime))).$newtime;
                                $slotArray['status'] = $sdetails->Available;
                                
                                $sdArray[] = $slotArray;
                            }
                            $nowSaving = 0;
                                    
                            if(empty($nextStartTime)){ $nextStartTime =null;}
                            if(empty($nextEndTime)){ $nextEndTime =null;}
                            if(empty($nextStatus)){ $nextStatus =0;}
                            
                            //$bookArray['times']  = ArrayHelper::SlotArray($findDoctorSlot,$currentDate,$totalPatientCount,$nowSaving,$nextavailableslot,$nextStartTime,$nextEndTime,$nextStatus,$sdArray);
                            $bookArray  = ArrayHelper::SlotArray($findDoctorSlot,$currentDate,$totalPatientCount,$nowSaving,$nextavailableslot,$nextStartTime,$nextEndTime,$nextStatus,$sdArray);
                            $returnObject->status = TRUE;
                            $returnObject->data['timeslot'] = $bookArray;
                        }else{ $norecord =1;}
                    }else{ $norecord =1;}
                }elseif($mainbooktype == 0){ //This is for queue
                    $findSlotManage = self::DoctorSlotManageByDate($findDoctorSlot->DoctorSlotID,$currentDate);
                    if($findDoctorSlot->ClinicSession == 1 || $findDoctorSlot->ClinicSession == 3 && ArrayHelper::ActiveDate($currentDate)==1){
                        $getQueueAppointment = self::FindQueueBookingDetails(0,$findDoctorSlot->DoctorSlotID,$currentDate);
                        if($getQueueAppointment){
                                $order = 1;
                            foreach($getQueueAppointment as $appoint){
                                if($order == 1){ $nextAvailableNumber = $appoint->BookNumber + 1; }
                                if($appoint->Status == 1){ $nowSaving = $appoint->BookNumber; }
                                $order ++;
                            }
                        }
                            if(empty($nowSaving)){ $nowSaving = 0;}
                            if(empty($nextAvailableNumber)){$nextAvailableNumber = 1;}
                            
                            $bookArray = ArrayHelper::QueueArray($findDoctorSlot, $nowSaving, $totalPatientCount, $nextAvailableNumber, $currentDate);
                            if($findSlotManage && $findSlotManage->Status ==1){
                                $norecord =1;
                            }elseif($nextAvailableNumber > $findDoctorSlot->QueueNumber){
                                $norecord =1;
                            }else{
                                $returnObject->status = TRUE;
                                $returnObject->data['queue'] = $bookArray;
                            }                            
                    }else{  $norecord = 1; }
                }else{ $norecord =1; }
            }else{ $norecord =1;}
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmptyValues");
        }
        if($norecord ==1){
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("NoRecords");
        }
        return $returnObject; 
    }
    
    
    public static function DoctorSlotsForDate1($mainbooktype){
        $allUpdatedata = Input::all();    
        $appointment = new UserAppoinment();
        $slotdetail = new DoctorSlotDetails();
        $doctorslot = new DoctorSlots();
        $slotmanage = new DoctorSlotsManage();
        $returnObject = new stdClass();
        $norecord = 0;
        if(count($allUpdatedata) > 0){
            //$findDoctorSlot = $doctorslot->getDoctorSlot($allUpdatedata['doctorid'],$allUpdatedata['clinicid']);
            $findDoctorSlot = self::FindDoctorSlot($allUpdatedata['doctorid'],$allUpdatedata['clinicid']);
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
            $returnObject->message = StringHelper::errorMessage("EmptyValues");
        }
        if($norecord == 1){
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("NoRecords");
        }
        return $returnObject;            
    }
    
    public static function FindTotalBooking($doctorslotid,$bookdate){
        $appointment = new UserAppoinment();
        if(!empty($doctorslotid) && !empty($bookdate)){
            $totalCount = $appointment->FindTotalBooking($doctorslotid,$bookdate);
            if($totalCount){
                return $totalCount;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    
    public static function QueueuBookingCount($booktype,$slotid,$bookdate){
        $userappointment = new UserAppoinment();
        if(!empty($slotid) && !empty($bookdate)){
            $queuebooking = $userappointment->CountQueueBooking($booktype,$slotid,$bookdate);
            if($queuebooking){
                return $queuebooking;
            }else{
                return 0;
            }  
        }else{
            return 0;
        }  
    }
    public static function SlotsBooking($findUserID){
        $returnObject = new stdClass();
        $findUserProfile = AuthLibrary::FindUserProfile($findUserID);
        $findAnyAppointment = ClinicLibrary::FindRealAppointment($findUserID);
         if($findAnyAppointment){
            if(strpos($findUserProfile->PhoneNo, "+") === false) {
                $phone = $findUserProfile->PhoneCode.$findUserProfile->PhoneNo;
                // $phone = "phone have a + sign";
            } else {
                $phone = $findUserProfile->PhoneNo;
                // $phone = "phone does not have a + sign";
            }

            if($findUserProfile->OTPStatus==1){
                $returnObject->status = TRUE;
                $returnObject->reconciliation = 2;
                $returnObject->data['nric'] = $findUserProfile->NRIC;
                $returnObject->data['phone'] = $phone;
                
                //$returnObject = self::Booking($findUserID,1);
                
            }else{
                $returnObject->status = FALSE;
                $returnObject->reconciliation = 1;
                $returnObject->data['nric'] = $findUserProfile->NRIC;
                $returnObject->data['phone'] = $phone;
            }
        }else{
            //bookingh
            
            if(!empty($findUserProfile->NRIC) && !empty($findUserProfile->PhoneNo)){
                if(strpos($findUserProfile->PhoneNo, "+") === false) {
                    $phone = $findUserProfile->PhoneCode.$findUserProfile->PhoneNo;
                } else {
                    $phone = $findUserProfile->PhoneNo;
                }
                if($findUserProfile->OTPStatus==1){
                    $returnObject->status = TRUE;
                    $returnObject->reconciliation = 2;
                    $returnObject->data['nric'] = $findUserProfile->NRIC;
                    $returnObject->data['phone'] = $phone;
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->reconciliation = 1;
                    $returnObject->data['nric'] = $findUserProfile->NRIC;
                    $returnObject->data['phone'] = $phone;
                }
            }else{
                $returnObject->status = FALSE;
                $returnObject->reconciliation = 0;
                $returnObject->data['promo'] = null;
                    
                /*$activePromoCode = General_Library::ActivePromoCode();
                if($activePromoCode){
                    $returnObject->status = FALSE;
                    $returnObject->reconciliation = 0;
                    $returnObject->data['promo']['status'] = $activePromoCode->Active;
                    $returnObject->data['promo']['code'] = $activePromoCode->Code;
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->reconciliation = 0;
                    $returnObject->data['promo'] = null;
                }*/
            } 
            
        }
        return $returnObject;
    }
    
    public static function QueueBooking($findUserID){
        $returnObject = new stdClass();
        $findUserProfile = AuthLibrary::FindUserProfile($findUserID);
        $findAnyAppointment = ClinicLibrary::FindRealAppointment($findUserID);
        if($findAnyAppointment){
            if($findUserProfile->OTPStatus==1){
                $returnObject->status = TRUE;
                $returnObject->reconciliation = 2;
                $returnObject->data['nric'] = $findUserProfile->NRIC;
                $returnObject->data['phone'] = $findUserProfile->PhoneNo;
                
                //$returnObject = self::Booking($findUserID,0); 
                
            }else{
                $returnObject->status = FALSE;
                $returnObject->reconciliation = 1;
                $returnObject->data['nric'] = $findUserProfile->NRIC;
                $returnObject->data['phone'] = $findUserProfile->PhoneNo;
            }
        }else{
            if(!empty($findUserProfile->NRIC) && !empty($findUserProfile->PhoneNo)){
                if($findUserProfile->OTPStatus==1){
                    $returnObject->status = TRUE;
                    $returnObject->reconciliation = 2;
                    $returnObject->data['nric'] = $findUserProfile->NRIC;
                    $returnObject->data['phone'] = $findUserProfile->PhoneNo;
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->reconciliation = 1;
                    $returnObject->data['nric'] = $findUserProfile->NRIC;
                    $returnObject->data['phone'] = $findUserProfile->PhoneNo;
                }
            }else{
                $returnObject->status = FALSE;
                $returnObject->reconciliation = 0;
                $returnObject->data['promo'] = null;
      
                /*$activePromoCode = General_Library::ActivePromoCode();
                if($activePromoCode){
                    $returnObject->status = FALSE;
                    $returnObject->reconciliation = 0;
                    $returnObject->data['promo']['status'] = $activePromoCode->Active;
                    $returnObject->data['promo']['code'] = $activePromoCode->Code;
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->reconciliation = 0;
                    $returnObject->data['promo'] = null;
                }*/
            }        
        }
        
        /*
        if(!empty($findUserProfile->NRIC) && !empty($findUserProfile->PhoneNo)){
            if($findUserProfile->OTPStatus==1){
                //$returnObject = self::Booking($findUserID,0);

                $returnObject->reconciliation = 2;
                $returnObject->data['nric'] = $findUserProfile->NRIC;
                $returnObject->data['phone'] = $findUserProfile->PhoneNo;
            }else{
                $returnObject->status = FALSE;
                $returnObject->reconciliation = 1;
                $returnObject->data['nric'] = $findUserProfile->NRIC;
                $returnObject->data['phone'] = $findUserProfile->PhoneNo;
            }
        }else{
            $returnObject->status = FALSE;
            $returnObject->reconciliation = 0;
            $returnObject->data['nric'] = $findUserProfile->NRIC;
            $returnObject->data['phone'] = $findUserProfile->PhoneNo;
        }*/
        return $returnObject;
    }
    
    
    public static function ConfirmQueueBooking($findUserID){
        $returnObject = self::Booking($findUserID,0);
        return $returnObject;
    }
    public static function ConfirmSlotBooking($findUserID){
        $returnObject = self::Booking($findUserID,1);
        return $returnObject;
    }
    
    /* Use          :   Used to process both booking Queue and Slots
    * Access       :   Private
    * Parameter    :   Input array and booking type (Queue / slot)
    * 
    */
    private static function Booking($findUserID,$mainbooktype){
       $allUpdatedata = Input::all();  
       $returnObject = new stdClass();
       $userappointment = new UserAppoinment();
       $slotdetail = new DoctorSlotDetails();
       $slotmanage = new DoctorSlotsManage();
       $doctorSlot = new DoctorSlots( );
       // $wallet = new Wallet( );
       // $procedure = new ClinicProcedures( );
       // $transaction_data = new Transaction( );
       $dataArray = array();
       
       //$findDoctorSlot = ClinicLibrary::FindDoctorSlot($allUpdatedata['doctorslot_id']);
       $findDoctorSlot = ClinicLibrary::FindDoctorSlotClinicService($allUpdatedata['doctorslot_id']);
       if(!empty($findDoctorSlot) && !empty($allUpdatedata['date'])){
       //if(!empty($findDoctorSlot) && !empty($allUpdatedata['doctorslot_id']) && !empty($allUpdatedata['date'])){    
           $formatDate = date("d-m-Y", strtotime($allUpdatedata['date']));
           //$findDoctorSlot = ClinicLibrary::FindDoctorSlot($allUpdatedata['doctorslot_id']);
           //$findDoctorSlot = $doctorSlot->findDoctorSlot($allUpdatedata['doctorslot_id']);
           if($mainbooktype == 0){
               //$doctorslot = $allUpdatedata['queue_id'];
               $doctorslot = $allUpdatedata['doctorslot_id'];
               $bookno = $allUpdatedata['queue_no'];
               $slotdetailid = 0;
               $getslotTime = 0;
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
               //$findSlotDetails = $slotdetail->FindBookingSlot($slotdetailid,$slottype,$formatDate);
               $findSlotDetails = self::FindBookingConfirmSlot($slotdetailid,$formatDate);
               if(!$findSlotDetails){
                   $returnObject->status = FALSE;
                   $returnObject->message = StringHelper::errorMessage("NoSlot");
                   return $returnObject;
               }
               $getslotTime = $findSlotDetails->Time;
           }
           $dataArray['userid'] = $findUserID;
           $dataArray['booktype'] = $mainbooktype;
           $dataArray['doctorslotid'] = $doctorslot;
           $dataArray['slotdetailid'] = $slotdetailid;
           $dataArray['mediatype'] = 0;
           $dataArray['booknumber'] = $bookno;
           $dataArray['bookdate'] = $formatDate;

           $newAppointment = $userappointment->insertUserAppointment($dataArray);
           // $bookno = 14953995;
           if($newAppointment){
               $userProfile = AuthLibrary::FindUserProfile($findUserID);
               if($userProfile){
                    $emailDdata['bookingTime'] = $getslotTime;
                    $emailDdata['bookingNo'] = $bookno;
                    $emailDdata['bookingDate'] = $formatDate; 
                    $emailDdata['doctorName'] = $findDoctorSlot->DName.' , '.$findDoctorSlot->Specialty;
                    $emailDdata['clinicName'] = $findDoctorSlot->CName;
                    $emailDdata['clinicAddress'] = $findDoctorSlot->Address;
                   
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
               // test for mobile
               // $user_appointment_data = $userappointment->findAppointmentData($newAppointment);
               // $getProcedure = $procedure->ClinicProcedureByID($user_appointment_data->ProcedureID);
               // $doctorslot = $doctorSlot->findClinicIDbyDoctorSlotID($allUpdatedata['doctorslot_id']);
               // $wallet_id = $wallet->getWalletId($findUserID);
               // return $doctorslot['ClinicID'];
                // $transaction = array(
                //     'wallet_id'             => $wallet_id,
                //     'ClinicID'              => $doctorslot['ClinicID'],
                //     'UserID'                => $findUserID,
                //     'ProcedureID'           => $user_appointment_data->ProcedureID,
                //     'DoctorID'              => $user_appointment_data->DoctorID,
                //     'AppointmenID'          => $newAppointment,
                //     'procedure_cost'        => $getProcedure->Price,
                //     'revenue'               => null,
                //     'debit'                 => null,
                //     'medi_percent'          => 0.1,
                //     'date_of_transaction'   => Carbon::now(),
                //     'created_at'            => Carbon::now(),
                //     'updated_at'            => Carbon::now()
                // );

                // $transaction_data->createTransaction($transaction);

           }else{
               $returnObject->status = FALSE;
               $returnObject->message = StringHelper::errorMessage("Tryagain");
           }
       }else{
           $returnObject->status = FALSE;
           $returnObject->message = StringHelper::errorMessage("EmptyValues");
       }
       return $returnObject;
   }
   
   public static function FindBookingConfirmSlot($slotdetailid,$currentdate){
       $slotdetail = new DoctorSlotDetails();
        if(!empty($slotdetailid) && !empty($currentdate)){
            $findSlotDetail = $slotdetail->FindBookingConfirmSlot($slotdetailid,$currentdate);
            if($findSlotDetail){
                return $findSlotDetail;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
   }
   public static function FindDoctorSlotDetails($slotdetailid){
       $slotdetail = new DoctorSlotDetails();
        if(!empty($slotdetailid)){
            $findSlotDetail = $slotdetail->getSlotDetails($slotdetailid);
            if($findSlotDetail){
                return $findSlotDetail;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
   }
    
}

