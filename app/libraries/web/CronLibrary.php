<?php

class CronLibrary{
    
    public static function RemindAppointment(){
        $date = date('d-m-Y');
        $appointmentArray = Doctor_Library::GetTodayAppointment($date);
        if($appointmentArray){
            $content ='Your session is today';
            foreach($appointmentArray as $appointment){
                $devices[] = $appointment->Token;  
            }
            //$deviceArray = implode($device, ',');
            $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
            PushLibrary::PushSingleDevice($content,$data,$devices); 
        }
    }
    
    public static function RemindAppointmentInHours(){
        $hoursTime = date('H.i', strtotime(StringHelper::CurrentTime() . " +3 hours"));

        $date = date('d-m-Y'); 
        $appointmentArray = Doctor_Library::GetTodayAppointmentInHours($date);
        if($appointmentArray){
            foreach($appointmentArray as $appointment){
                if($hoursTime == substr($appointment->Time, 0, -2)){
                    $content ='Your session will start at '.$appointment->Time.' Today' ;
                    $devices = array($appointment->Token);
                    $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                    PushLibrary::PushSingleDevice($content,$data,$devices); 
                }      
            }
        }
    }
    
    public static function RemindAppointmentInMinutes(){
        echo StringHelper::CurrentTime().'<br>';
        $hoursTime = date('H.i', strtotime(StringHelper::CurrentTime() . " +30 minutes"));
        echo $hoursTime;
        $date = date('d-m-Y'); 
        $appointmentArray = Doctor_Library::GetTodayAppointmentInHours($date);
        if($appointmentArray){
            foreach($appointmentArray as $appointment){
                if($hoursTime == substr($appointment->Time, 0, -2)){
                    $content ='Your session will start at '.$appointment->Time.' Today' ;
                    $devices = array($appointment->Token);
                    $data = array('custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
                    PushLibrary::PushSingleDevice($content,$data,$devices); 
                }      
            }
        }
    }
    
    public static function DiactivateBookings1(){
        $findActiveBookings = Clinic_Library::FindActiveAppointments();       
        if($findActiveBookings){
            //echo count($findActiveBookings);
            foreach($findActiveBookings as $activeBooking){
                if(strtotime($activeBooking->BookDate) <= strtotime(date("d-m-Y"))){
                    //echo '<pre>'; print_r($activeBooking); echo '</pre>';
                    $updateArray['Status'] = 2; 
                    $updateArray['updated_at'] = time(); 
                    Doctor_Library::UpdateAppointment($updateArray,$activeBooking->UserAppoinmentID);            
                }   
            }
            $date = new DateTime('now', new DateTimeZone('Asia/Singapore'));
            $thistime = $date->format('H:i:sA');
                $emailDdata['emailName']= 'Mednefits Booking Automatic Conclude';
                $emailDdata['emailPage']= 'email-templates.cron-booking-deactivate';
                $emailDdata['emailTo']= 'allan.alzula@gmail.com';
                $emailDdata['emailSubject'] = "Cron for Booking Deactivation";
                $emailDdata['actionDate'] = date('d-m-Y');
                $emailDdata['actionTime'] = $thistime;
                $emailDdata['totalRecords'] = count($findActiveBookings);
                EmailHelper::sendEmailDirect($emailDdata);
        }else{
            //echo 'no record found';
        }
    }
    public static function DiactivateBookings(){
        $currentdate = strtotime(date('d-m-Y'));
        //$findActiveBookings = Clinic_Library::FindActiveAppointments();      
        $findActiveBookings = Clinic_Library::FindAvailableAppointments($currentdate);      
        if($findActiveBookings){
            foreach($findActiveBookings as $activeBooking){
                //if(strtotime($activeBooking->BookDate) <= strtotime(date("d-m-Y"))){
                    $updateArray['Status'] = 2; 
                    $updateArray['updated_at'] = time(); 
                    //Doctor_Library::UpdateAppointment($updateArray,$activeBooking->UserAppoinmentID); 
                    General_Library::UpdateAppointment($updateArray,$activeBooking->UserAppoinmentID);
                //}   
            }
            //$date = new DateTime('now', new DateTimeZone('Asia/Singapore'));
            //$thistime = $date->format('H:i:sA');
            $currenttime = StringHelper::CurrentTime();
            
                $emailDdata['emailName']= 'Mednefits Booking Automatic Conclude';
                $emailDdata['emailPage']= 'email-templates.cron-booking-deactivate';
                $emailDdata['emailTo']= 'info@medicloud.sg';
                $emailDdata['emailSubject'] = "Cron for Booking Deactivation";
                $emailDdata['actionDate'] = date('d-m-Y');
                $emailDdata['actionTime'] = $currenttime;
                $emailDdata['totalRecords'] = count($findActiveBookings);
                EmailHelper::sendEmailDirect($emailDdata);
                
                $emailDdata['emailTo']= 'developer.mednefits@gmail.com';
                EmailHelper::sendEmailDirect($emailDdata);
        }else{
            //echo 'no record found';
        }
    }
    /* Use      :   Used to send sms one day before
     * 
     */
    public static function SMSAppointmentBeforeDay(){
        //StringHelper::Set_Default_Timezone();
        $today = date('d-m-Y');
        $tomorrow = strtotime("+1 day",  strtotime($today));
        $appointmentArray = General_Library::AppointmentByDate($tomorrow);
        $fullDetails = null;
        if($appointmentArray){
            foreach($appointmentArray as $appointArray){
                $findFullDetails = General_Library::FindClinicFromProcedure($appointArray->DoctorID,$appointArray->ProcedureID);
                if($findFullDetails){
                    if(strlen($appointArray->PhoneNo) > 8) {
                        //SendEmail
                        $formatDate = date('l, j F Y',$appointArray->BookDate);
                        $emailDdata['bookingid'] = $appointArray->UserAppoinmentID;
                        $emailDdata['remarks'] = $appointArray->Remarks;
                        $emailDdata['bookingTime'] = date('h:i A',$appointArray->StartTime).' - '.date('h:i A',$appointArray->EndTime);
                        $emailDdata['bookingNo'] = 0;
                        $emailDdata['bookingDate'] = $formatDate; 
                        $emailDdata['doctorName'] = $findFullDetails->DocName;
                        $emailDdata['doctorSpeciality'] = $findFullDetails->Specialty;
                        $emailDdata['clinicName'] = $findFullDetails->CliName;
                        $emailDdata['clinicAddress'] = $findFullDetails->CliAddress;
                        $emailDdata['clinicProcedure'] = $findFullDetails->ProName;
                        $emailDdata['emailName']= $appointArray->Name;
                        $emailDdata['emailPage']= 'email-templates.notification-day';
                        $emailDdata['emailTo']= $appointArray->Email;
                        $emailDdata['emailSubject'] = 'Your appointment is tomorrow!';
                        // EmailHelper::sendEmail($emailDdata);
                        
                        //send SMS
                        $starttime = date('h:i A',$appointArray->StartTime);
                        // $smsMessage = "Hello ".$appointArray->Name.", your booking with ".$findFullDetails->DocName." at ".$findFullDetails->CliName." is tomorrow at ".$starttime." thank you for using medicloud."; 

                        $smsMessage = "Hello ".$appointArray->Name.", your appointment with ".$findFullDetails->DocName." at ".$findFullDetails->CliName.", ".$findFullDetails->CliAddress.", Ph:".$findFullDetails->CliPhone." is tomorrow at ".$starttime.". Thank you for using Mednefits. Get the free app at mednefits.com";    

                        $sendSMS = StringHelper::SendOTPSMS($appointArray->PhoneNo,$smsMessage);
                        $saveSMS = StringHelper::saveSMSMLogs($findFullDetails->ClinicID, $appointArray->Name, $appointArray->PhoneCode, $appointArray->PhoneNo, $smsMessage);  
                    }
                }
                $fullDetails = null;
            }  
        }
    }
    /* Use      :   Used to send sms one hour before
     * 
     */
    public static function SMSAppointmentBeforeHour(){
        StringHelper::Set_Default_Timezone();
        $today = strtotime(date('d-m-Y'));   
        $currenttime = date('h:i A',time());
        $onehour = strtotime("+60 minutes", strtotime($currenttime));
        $appointmentArray = General_Library::AppointmentByHour($today, $onehour);
        if($appointmentArray){
            foreach($appointmentArray as $appointArray){
                $findFullDetails = General_Library::FindClinicFromProcedure($appointArray->DoctorID,$appointArray->ProcedureID);
                if($findFullDetails){
                    $arrayDetsil['clinicdetails'] = $findFullDetails;
                    $arrayDetsil['appointmentdetails'] = $appointArray;  
                    $fullDetails[] = $arrayDetsil;
                    
                    //Send email 
                    $formatDate = date('l, j F Y',$appointArray->BookDate);
                    $emailDdata['bookingid'] = $appointArray->UserAppoinmentID;
                    $emailDdata['remarks'] = $appointArray->Remarks;
                    $emailDdata['bookingTime'] = date('h:i A',$appointArray->StartTime).' - '.date('h:i A',$appointArray->EndTime);
                    $emailDdata['bookingNo'] = 0;
                    $emailDdata['bookingDate'] = $formatDate; 
                    $emailDdata['doctorName'] = $findFullDetails->DocName;
                    $emailDdata['doctorSpeciality'] = $findFullDetails->Specialty;
                    $emailDdata['clinicName'] = $findFullDetails->CliName;
                    $emailDdata['clinicAddress'] = $findFullDetails->CliAddress;
                    $emailDdata['clinicProcedure'] = $findFullDetails->ProName;
                    $emailDdata['emailName']= $appointArray->Name;
                    $emailDdata['emailPage']= 'email-templates.notification-hour';
                    $emailDdata['emailTo']= $appointArray->Email;
                    $emailDdata['emailSubject'] = 'Your appointment is in an hour’s time';
                    // EmailHelper::sendEmail($emailDdata);
                    
                    //send SMS
                    $starttime = date('h:i A',$appointArray->StartTime);
                    $smsMessage = "Hello ".$appointArray->Name.", your booking with ".$findFullDetails->DocName." is in an hour at ".$findFullDetails->CliName." login to the app to find contact details and directions.";        
                    $sendSMS = StringHelper::SendOTPSMS($appointArray->PhoneNo,$smsMessage);
                    $saveSMS = StringHelper::saveSMSMLogs($findFullDetails->ClinicID, $appointArray->Name, $appointArray->PhoneCode, $appointArray->PhoneNo, $smsMessage);
                }
                $fullDetails = null;
            }
        }
    }



   //nhr delete google event ///2016-3-5
    

     //nhr delete google event ///2016-3-5

    public static function deleteGoogleEvent()
    {
        $date = date('Y-m-d',strtotime(date('Y-m-d') . "-7 days"));
        return $date;
        $userAppointment = new UserAppoinment();
        $userAppointment->deleteGoogleEvent($date);


    }
}
