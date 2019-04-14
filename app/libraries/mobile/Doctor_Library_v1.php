<?php
use Carbon\Carbon;
class Doctor_Library_v1{
    
    public static function FindAllClinicDoctors($clinicid){
        $doctorAvailability = new DoctorAvailability();
        if(!empty($clinicid)){
            $findClinicDoctors = $doctorAvailability->FindAllClinicDoctors($clinicid);
            if($findClinicDoctors){
                return $findClinicDoctors;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    /* Use      :   Used to find the doctors by procedure
     * 
     */
    public static function FindDoctorsByProcedure($procedureid, $clinicid){
        $doctorprocedure = new DoctorProcedures();
        if(!empty($clinicid)){
            $DoctorsProcedure = $doctorprocedure->FindDoctorsByProcedure($procedureid, $clinicid);
            if($DoctorsProcedure){
                return $DoctorsProcedure;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    /* Use      :   Used to find doctors details 
     * 
     */
    public static function FindDoctorDetails($doctorid){
        $doctor = new Doctor();
        if(!empty($doctorid)){
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
    public static function FindDoctorsBProcedureList($doctorid){
        $doctorprocedure = new DoctorProcedures();
        if(!empty($doctorid)){
            $DoctorsProcedure = $doctorprocedure->DoctorsProcedureList($doctorid);
            if($DoctorsProcedure){
                return $DoctorsProcedure;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    /* Use          :   Used to return doctor details with timeslots
     * Access       :   Public
     * Parameter    :   clinicid, Doctorid and Procedureid
     */
    public static function FullDoctorDetails($findUserID){
        $returnObject = new stdClass();
        $allInputdata = Input::all();
        $currentdate = date("d-m-Y"); 
        // $currentdate = "02-06-2016"; 

        $timeSlotDetail = null;

        //if(count($allInputdata)>0){ 
            $findClinicDoctor = General_Library_Mobile::FindClinicDoctor($allInputdata['clinicid'], $allInputdata['doctorid']);
// dd($findClinicDoctor);
            if($findClinicDoctor){ 
                $doctorDetails = ArrayHelperMobile::ClinicDoctorDetails($findClinicDoctor);
                $findWeek = StringHelper::FindWeekFromDate($currentdate);
                $findDoctorProcedure = General_Library_Mobile::FindClinicDoctorProcedures($allInputdata['doctorid'],$allInputdata['procedureid']);
                //$findClinicTimes = General_Library::FindAllClinicTimes(3,$allInputdata['clinicid'], strtotime($currentdate));

		$findClinicTimes = General_Library::FindCurrentDayAvailableTimes(3,$allInputdata['clinicid'],$findWeek,strtotime($currentdate));
                //$findClinicHolidays = General_Library::FindUpcomingHolidays(3,$allInputdata['clinicid'], $currentdate);
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$allInputdata['clinicid'], $currentdate);
                $clinicUserProfile = AuthLibrary::FindUserProfileByRefID($allInputdata['clinicid']);
                $findClinicDoctor->ClinicOPenTime = $findClinicTimes;
                $findClinicDoctor->ClinicHolidays = $findClinicHolidays;
                $findClinicDoctor->Email = $clinicUserProfile->Email;
                $clinicDetails = ArrayHelperMobile::ClinicProfile($findClinicDoctor,$currentdate,$findUserID);
                
                $doctorAvailablity = ArrayHelperMobile::DoctorDetailArray($allInputdata['doctorid'],$findWeek,$currentdate);
                
                $clinicHolidays = ArrayHelperMobile::ProcessClinicHolidays($findClinicHolidays,$currentdate);

                $timeBookings = array();
                if($findClinicTimes && $findDoctorProcedure){
                    foreach($findClinicTimes as $clinicOpenTimes){ 
                        $startTime = strtotime($clinicOpenTimes->StartTime);
                        $endTime = strtotime($clinicOpenTimes->EndTime);
                        //$slotEndTime = strtotime("+".$findDoctorProcedure->Duration." minutes", $startTime);
                        //for($i=$startTime; $i<$endTime; $i = strtotime("+15 minutes", $i)){
                        for($i=$startTime; $i<$endTime; $i = strtotime("+".$findDoctorProcedure->Duration." minutes", $i)){
                            $returnHoliday = StringHelperMobile::HolidayTimeCondition($clinicHolidays,$i);
                            $slotEndTime = strtotime("+".$findDoctorProcedure->Duration." minutes", $i);  
                            if($returnHoliday!=1){ 
                                if($doctorAvailablity['available_times']){
                                    //$findAppointments = General_Library_Mobile::FindTimelyAppointments($allInputdata['doctorid'],strtotime($currentdate),$i,$slotEndTime);
                                    $findAppointments = General_Library_Mobile::FindTodayAppointments($allInputdata['doctorid'],strtotime($currentdate));
                                    $findExtraEvents = General_Library_Mobile::FindTodayExtraEvents($allInputdata['doctorid'],strtotime($currentdate));
                                    $findbreaks = General_Library_Mobile::FindTodayBreaks($allInputdata['doctorid'],strtotime($currentdate));
                                    $findTimeOff = General_Library_Mobile::FindTodayTimeOff($allInputdata['doctorid'],$currentdate);
                // dd($findTimeOff);
                                    $doctortimecount = 0; $activeAvailability = 0; 
                                    foreach($doctorAvailablity['available_times'] as $doctortime){ 
                                        $doctorstarttime = strtotime($doctortime['starttime']);
                                        $doctorendtime = strtotime($doctortime['endtime']);
                                        $returnDoctorHoliday = StringHelperMobile::HolidayTimeCondition($doctorAvailablity['holidays'],$i);
                                        if($doctorstarttime <= $i && $doctorendtime > $i && $returnDoctorHoliday !=1){
                                            //if(!$findAppointments && $doctortimecount ==0 && String_Helper_Web::GetActiveTime($i,$currentdate)==1){
                                            if($doctortimecount ==0 && String_Helper_Web::GetActiveTime($i,$currentdate)==1){ 
                               // echo date('h:i A',$i).'<pre>'; print_r($findAppointments); echo '</pre>';
                                                // dd($findAppointments);
                                                if($findAppointments || $findExtraEvents || $findbreaks || $findTimeOff){
                                                    if($findAppointments){
                                                        foreach($findAppointments as $todayAppoint){
                                                            if(($todayAppoint->StartTime <= $i && $todayAppoint->EndTime >$i) || ($todayAppoint->StartTime < $slotEndTime && $todayAppoint->EndTime >=$slotEndTime)){
                                                                $activeAvailability=1;
                                                                break; 
                                                            }
                                                        }
                                                        
                                                    }
                                                    if($findExtraEvents){
                                                        foreach($findExtraEvents as $today){
                                                            if(($today->start_time <= $i && $today->end_time >$i) || ($today->start_time < $slotEndTime && $today->end_time >=$slotEndTime)){
                                                                $activeAvailability=1;
                                                                break; 
                                                            }
                                                        }
                                                        
                                                    }

                                                    if($findbreaks){
                                                        foreach($findbreaks as $day){

                                                            $stime = strtotime($currentdate.$day->start_time);
                                                            $etime = strtotime($currentdate.$day->end_time);

                                                            if(($stime <= $i && $etime >$i) || ($stime < $slotEndTime && $etime >=$slotEndTime)){
                                                                $activeAvailability=1;
                                                                break; 
                                                            }
                                                        }
                                                        
                                                    }

                                                    if($findTimeOff){
                                                        foreach($findTimeOff as $off){

                                                            $sstime = strtotime($currentdate.$off->From_Time);
                                                            $setime = strtotime($currentdate.$off->To_Time);

                                                            if ($sstime==0) {
                                                                $sstime = strtotime($currentdate."12:00 AM");
                                                                $setime = strtotime($currentdate."11:45 PM");
                                                            } 
                                                            

                                                            if(($sstime <= $i && $setime >$i) || ($sstime < $slotEndTime && $setime >=$slotEndTime)){
                                                                $activeAvailability=1;
                                                                break; 
                                                            }
                                                        }
                                                        
                                                    }


                                                    if($activeAvailability==0){
                                                        $doctortimecount = 1;
                                                        $timeSlotBookings['start_time'] = date('h:i A',$i);
                                                        $timeSlotBookings['end_time'] = date('h:i A',$slotEndTime);
                                                        $timeSlotDetail[] = $timeSlotBookings;
                                                    }else{
                                                        $timeSlotBookings = null;
                                                    }
                                                }else{
                                                    //echo date('h:i A',$i);
                                                    //$activeAvailability = 1;
                                                    $doctortimecount = 1;
                                                    $timeSlotBookings['start_time'] = date('h:i A',$i);
                                                    $timeSlotBookings['end_time'] = date('h:i A',$slotEndTime);
                                                    $timeSlotDetail[] = $timeSlotBookings;
                                                }




                                    
                                            }  
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    $timeBookings['type'] = 2;
                    $timeBookings['procedureid'] = $allInputdata['procedureid'];
                    $timeBookings['bookingdate'] = $currentdate;
                    $price = preg_replace('/[^A-Za-z0-9\-]/', '', $findDoctorProcedure->Price);
                    $timeBookings['price'] = $price;
                    $timeBookings['duration'] = $findDoctorProcedure->Duration.' Min';
                    $timeBookings['queue'] = null;
                    $timeBookings['timeslot'] = $timeSlotDetail;

                    $doctorDetails['availability'] = $doctorAvailablity['available'];
                    $detailArray['doctor'] = $doctorDetails;
                    $detailArray['clinic'] = $clinicDetails;
                    $detailArray['booking'] = $timeBookings;
                    $returnObject->status = TRUE;
                    $returnObject->data = $detailArray;
                }else{
                    $timeBookings['type'] = 2;
                    $timeBookings['procedureid'] = $allInputdata['procedureid'];
                    $timeBookings['bookingdate'] = $currentdate;
                    $timeBookings['price'] = $findDoctorProcedure->Price;
                    $timeBookings['duration'] = $findDoctorProcedure->Duration.' Min';
                    $timeBookings['queue'] = null;
                    $timeBookings['timeslot'] = null;

                    $doctorDetails['availability'] = $doctorAvailablity['available'];
                    $detailArray['doctor'] = $doctorDetails;
                    $detailArray['clinic'] = $clinicDetails;
                    $detailArray['booking'] = $timeBookings;
                    $returnObject->status = TRUE;
                    $returnObject->data = $detailArray;

                    // $returnObject->message = StringHelper::errorMessage("NoRecords");
                }
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("NoRecords");
            } 
        //}else{
        //    $returnObject->status = FALSE;
        //    $returnObject->message = StringHelper::errorMessage("EmptyValues");
        //}
        return $returnObject;  
    }
    
    public static function AccessMoreSlots($findUserID){
        $returnObject = new stdClass();
        $allInputdata = Input::all();
        $currentdate = $allInputdata['bookingdate']; 
        $timeSlotDetail = null;
        if(count($allInputdata)>0){
                $findWeek = StringHelper::FindWeekFromDate($currentdate);
                $findDoctorProcedure = General_Library_Mobile::FindClinicDoctorProcedures($allInputdata['doctorid'],$allInputdata['procedureid']);
               // $findClinicTimes = General_Library::FindAllClinicTimes(3,$allInputdata['clinicid'], strtotime($currentdate));
		
		$findClinicTimes = General_Library::FindCurrentDayAvailableTimes(3,$allInputdata['clinicid'],$findWeek,strtotime($currentdate));
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$allInputdata['clinicid'], $currentdate);
                $doctorAvailablity = ArrayHelperMobile::DoctorDetailArray($allInputdata['doctorid'],$findWeek,$currentdate);
                $clinicHolidays = ArrayHelperMobile::ProcessClinicHolidays($findClinicHolidays,$currentdate);
                
                $timeBookings = array();
                if($findClinicTimes && $findDoctorProcedure){
                    foreach($findClinicTimes as $clinicOpenTimes){
                        $startTime = strtotime($currentdate.$clinicOpenTimes->StartTime);
                        $endTime = strtotime($currentdate.$clinicOpenTimes->EndTime);
                        //$startTime = strtotime($clinicOpenTimes->StartTime);
                        //$endTime = strtotime($clinicOpenTimes->EndTime);
                        
                        //$slotEndTime = strtotime("+".$findDoctorProcedure->Duration." minutes", $startTime);
                        //for($i=$startTime; $i<$endTime; $i = strtotime("+15 minutes", $i)){
                        for($i=$startTime; $i<$endTime; $i = strtotime("+".$findDoctorProcedure->Duration." minutes", $i)){
                            $returnHoliday = StringHelperMobile::HolidayTimeCondition($clinicHolidays,$i);
                            $slotEndTime = strtotime("+".$findDoctorProcedure->Duration." minutes", $i);
                            if($returnHoliday!=1){ 
                                if($doctorAvailablity['available_times']){
                                    //$findAppointments = General_Library_Mobile::FindProcedureAppointments($allInputdata['doctorid'], $allInputdata['procedureid'], strtotime($currentdate),$i,$slotEndTime);
                                    //$findAppointments = General_Library_Mobile::FindTimelyAppointments($allInputdata['doctorid'],strtotime($currentdate),$i,$slotEndTime);
                                    $findAppointments = General_Library_Mobile::FindTodayAppointments($allInputdata['doctorid'],strtotime($currentdate));
                                    $findExtraEvents = General_Library_Mobile::FindTodayExtraEvents($allInputdata['doctorid'],strtotime($currentdate));
                                    $findbreaks = General_Library_Mobile::FindTodayBreaks($allInputdata['doctorid'],strtotime($currentdate));
                                    $findTimeOff = General_Library_Mobile::FindTodayTimeOff($allInputdata['doctorid'],$currentdate);
// dd($findTimeOff);
                                    $doctortimecount = 0; $activeAvailability = 0; 
                                    foreach($doctorAvailablity['available_times'] as $doctortime){
                                        $doctorstarttime = strtotime($currentdate.$doctortime['starttime']);
                                        $doctorendtime = strtotime($currentdate.$doctortime['endtime']);
                                        //$doctorstarttime = strtotime($doctortime['starttime']);
                                        //$doctorendtime = strtotime($doctortime['endtime']);
                                        $returnDoctorHoliday = StringHelperMobile::HolidayTimeCondition($doctorAvailablity['holidays'],$i);
                                        if($doctorstarttime <= $i && $doctorendtime > $i && $returnDoctorHoliday !=1){
                                            //if(!$findAppointments && $doctortimecount == 0 && String_Helper_Web::GetActiveTime($i,$currentdate)==1){
                                            if($doctortimecount == 0 && String_Helper_Web::GetActiveTime($i,$currentdate)==1){
                                                if($findAppointments || $findExtraEvents || $findbreaks || $findTimeOff){
                                                    if($findAppointments){
                                                        foreach($findAppointments as $todayAppoint){
                                                            if(($todayAppoint->StartTime <= $i && $todayAppoint->EndTime >$i) || ($todayAppoint->StartTime < $slotEndTime && $todayAppoint->EndTime >=$slotEndTime)){
                                                                $activeAvailability=1;
                                                                break; 
                                                            }
                                                        }
                                                        
                                                    }
                                                    if($findExtraEvents){
                                                        foreach($findExtraEvents as $today){
                                                            if(($today->start_time <= $i && $today->end_time >$i) || ($today->start_time < $slotEndTime && $today->end_time >=$slotEndTime)){
                                                                $activeAvailability=1;
                                                                break; 
                                                            }
                                                        }
                                                        
                                                    }

                                                    if($findbreaks){
                                                        foreach($findbreaks as $day){

                                                            $stime = strtotime($currentdate.$day->start_time);
                                                            $etime = strtotime($currentdate.$day->end_time);

                                                            if(($stime <= $i && $etime >$i) || ($stime < $slotEndTime && $etime >=$slotEndTime)){
                                                                $activeAvailability=1;
                                                                break; 
                                                            }
                                                        }
                                                        
                                                    }
                                                    if($findTimeOff){
                                                        foreach($findTimeOff as $off){

                                                            $sstime = strtotime($currentdate.$off->From_Time);
                                                            $setime = strtotime($currentdate.$off->To_Time);

                                                            if ($sstime==0) {
                                                                $sstime = strtotime($currentdate."12:00 AM");
                                                                $setime = strtotime($currentdate."11:45 PM");
                                                            } 
                                                            

                                                            if(($sstime <= $i && $setime >$i) || ($sstime < $slotEndTime && $setime >=$slotEndTime)){
                                                                $activeAvailability=1;
                                                                break; 
                                                            }
                                                        }
                                                        
                                                    }

                                                    if($activeAvailability==0){
                                                        $doctortimecount = 1;
                                                        $timeSlotBookings['start_time'] = date('h:i A',$i);
                                                        $timeSlotBookings['end_time'] = date('h:i A',$slotEndTime);
                                                        $timeSlotDetail[] = $timeSlotBookings;
                                                    }else{
                                                        $timeSlotBookings = null;
                                                    }
                                                }else{
                                                    $doctortimecount = 1;
                                                    $timeSlotBookings['start_time'] = date('h:i A',$i);
                                                    $timeSlotBookings['end_time'] = date('h:i A',$slotEndTime);
                                                    $timeSlotDetail[] = $timeSlotBookings;
                                                }
                                                
                                            }  
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $timeBookings['type'] = 2;
                    $timeBookings['procedureid'] = $allInputdata['procedureid'];
                    $timeBookings['bookingdate'] = $currentdate;
                    $price = preg_replace('/[^A-Za-z0-9\-]/', '', $findDoctorProcedure->Price);
                
                    $timeBookings['price'] = $price;
                    $timeBookings['duration'] = $findDoctorProcedure->Duration.' Min';
                    $timeBookings['queue'] = null;
                    $timeBookings['timeslot'] = $timeSlotDetail;

                    //$doctorDetails['availability'] = $doctorAvailablity['available'];
                    //$detailArray['doctor'] = $doctorDetails;
                    //$detailArray['clinic'] = $clinicDetails;
                    $detailArray['booking'] = $timeBookings;
                    $returnObject->status = TRUE;
                    $returnObject->data = $detailArray;
                    
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("NoRecords");
                }
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmptyValues");
        }
        return $returnObject;  
    }
    
    public static function FullDoctorDetails_original($findUserID){
        $returnObject = new stdClass();
        $allInputdata = Input::all();
        $currentdate = date("d-m-Y"); 
        $timeSlotDetail = null;
        //if(count($allInputdata)>0){ 
            $findClinicDoctor = General_Library_Mobile::FindClinicDoctor($allInputdata['clinicid'], $allInputdata['doctorid']);

            if($findClinicDoctor){ 
                $doctorDetails = ArrayHelperMobile::ClinicDoctorDetails($findClinicDoctor);
                $findWeek = StringHelper::FindWeekFromDate($currentdate);
                $findDoctorProcedure = General_Library_Mobile::FindClinicDoctorProcedures($allInputdata['doctorid'],$allInputdata['procedureid']);
                $findClinicTimes = General_Library::FindAllClinicTimes(3,$allInputdata['clinicid'], strtotime($currentdate));
                //$findClinicHolidays = General_Library::FindUpcomingHolidays(3,$allInputdata['clinicid'], $currentdate);
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$allInputdata['clinicid'], $currentdate);
                $clinicUserProfile = AuthLibrary::FindUserProfileByRefID($allInputdata['clinicid']);
                $findClinicDoctor->ClinicOPenTime = $findClinicTimes;
                $findClinicDoctor->ClinicHolidays = $findClinicHolidays;
                $findClinicDoctor->Email = $clinicUserProfile->Email;
                $clinicDetails = ArrayHelperMobile::ClinicProfile($findClinicDoctor,$currentdate);
                $doctorAvailablity = ArrayHelperMobile::DoctorDetailArray($allInputdata['doctorid'],$findWeek,$currentdate);
                
                $clinicHolidays = ArrayHelperMobile::ProcessClinicHolidays($findClinicHolidays,$currentdate);

                $timeBookings = array();
                if($findClinicTimes && $findDoctorProcedure){
                    foreach($findClinicTimes as $clinicOpenTimes){ 
                        $startTime = strtotime($clinicOpenTimes->StartTime);
                        $endTime = strtotime($clinicOpenTimes->EndTime);
                        //$slotEndTime = strtotime("+".$findDoctorProcedure->Duration." minutes", $startTime);
                        //for($i=$startTime; $i<$endTime; $i = strtotime("+15 minutes", $i)){
                        for($i=$startTime; $i<$endTime; $i = strtotime("+".$findDoctorProcedure->Duration." minutes", $i)){
                            $returnHoliday = StringHelperMobile::HolidayTimeCondition($clinicHolidays,$i);
                            $slotEndTime = strtotime("+".$findDoctorProcedure->Duration." minutes", $i);  
                            if($returnHoliday!=1){ 
                                if($doctorAvailablity['available_times']){
                                    //$findAppointments = General_Library_Mobile::FindProcedureAppointments($allInputdata['doctorid'], $allInputdata['procedureid'], strtotime($currentdate),$i,$slotEndTime);
                                    $findAppointments = General_Library_Mobile::FindTimelyAppointments($allInputdata['doctorid'],strtotime($currentdate),$i,$slotEndTime);

                                    $doctortimecount = 0; $activeAvailability = 0; 
                                    foreach($doctorAvailablity['available_times'] as $doctortime){ 
                                        $doctorstarttime = strtotime($doctortime['starttime']);
                                        $doctorendtime = strtotime($doctortime['endtime']);
                                        $returnDoctorHoliday = StringHelperMobile::HolidayTimeCondition($doctorAvailablity['holidays'],$i);
                                        if($doctorstarttime <= $i && $doctorendtime > $i && $returnDoctorHoliday !=1){
                                            //if(!$findAppointments && $doctortimecount ==0 && String_Helper_Web::GetActiveTime($i,$currentdate)==1){
                                            if($doctortimecount ==0 && String_Helper_Web::GetActiveTime($i,$currentdate)==1){ 
                                                if($findAppointments){
                                                    if(($findAppointments->StartTime <= $i && $findAppointments->EndTime >$i) || ($findAppointments->StartTime < $slotEndTime && $findAppointments->EndTime >=$slotEndTime)){
                                                        $timeSlotBookings = null;
                                                    }else{
                                                        $activeAvailability = 1;
                                                        $doctortimecount = 1;
                                                        $timeSlotBookings['start_time'] = date('h:i A',$i);
                                                        $timeSlotBookings['end_time'] = date('h:i A',$slotEndTime);
                                                        $timeSlotDetail[] = $timeSlotBookings;
                                                    }      
                                                }else{
                                                    $activeAvailability = 1;
                                                    $doctortimecount = 1;
                                                    $timeSlotBookings['start_time'] = date('h:i A',$i);
                                                    $timeSlotBookings['end_time'] = date('h:i A',$slotEndTime);
                                                    $timeSlotDetail[] = $timeSlotBookings;
                                                }
                                                
                                                
                                                
                                                
                                            }  
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    $timeBookings['type'] = 2;
                    $timeBookings['procedureid'] = $allInputdata['procedureid'];
                    $timeBookings['bookingdate'] = $currentdate;
                    $timeBookings['price'] = $findDoctorProcedure->Price;
                    $timeBookings['duration'] = $findDoctorProcedure->Duration.' Min';
                    $timeBookings['queue'] = null;
                    $timeBookings['timeslot'] = $timeSlotDetail;

                    $doctorDetails['availability'] = $doctorAvailablity['available'];
                    $detailArray['doctor'] = $doctorDetails;
                    $detailArray['clinic'] = $clinicDetails;
                    $detailArray['booking'] = $timeBookings;
                    $returnObject->status = TRUE;
                    $returnObject->data = $detailArray;
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("NoRecords");
                }
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("NoRecords");
            } 
        //}else{
        //    $returnObject->status = FALSE;
        //    $returnObject->message = StringHelper::errorMessage("EmptyValues");
        //}
        return $returnObject;  
    }


    public static function AccessMoreSlots_original($findUserID){
        $returnObject = new stdClass();
        $allInputdata = Input::all();
        $currentdate = $allInputdata['bookingdate']; 
        $timeSlotDetail = null;
        if(count($allInputdata)>0){
            //$findClinicDoctor = General_Library_Mobile::FindClinicDoctor($allInputdata['clinicid'], $allInputdata['doctorid']);
   
            //if($findClinicDoctor){
                //$doctorDetails = ArrayHelperMobile::ClinicDoctorDetails($findClinicDoctor);
                $findWeek = StringHelper::FindWeekFromDate($currentdate);
                $findDoctorProcedure = General_Library_Mobile::FindClinicDoctorProcedures($allInputdata['doctorid'],$allInputdata['procedureid']);
                $findClinicTimes = General_Library::FindAllClinicTimes(3,$allInputdata['clinicid'], strtotime($currentdate));
                //$findClinicHolidays = General_Library::FindUpcomingHolidays(3,$allInputdata['clinicid'], $currentdate);
                $findClinicHolidays = General_Library::FindCurrentDayHolidays(3,$allInputdata['clinicid'], $currentdate);
                //$clinicUserProfile = AuthLibrary::FindUserProfileByRefID($allInputdata['clinicid']);
                //$findClinicDoctor->ClinicOPenTime = $findClinicTimes;
                //$findClinicDoctor->ClinicHolidays = $findClinicHolidays;
                //$findClinicDoctor->Email = $clinicUserProfile->Email;
                //$clinicDetails = ArrayHelperMobile::ClinicProfile($findClinicDoctor,$currentdate);
                $doctorAvailablity = ArrayHelperMobile::DoctorDetailArray($allInputdata['doctorid'],$findWeek,$currentdate);
                
                $clinicHolidays = ArrayHelperMobile::ProcessClinicHolidays($findClinicHolidays,$currentdate);
                
                $timeBookings = array();
                if($findClinicTimes && $findDoctorProcedure){
                    foreach($findClinicTimes as $clinicOpenTimes){
                        $startTime = strtotime($clinicOpenTimes->StartTime);
                        $endTime = strtotime($clinicOpenTimes->EndTime);
                        //$slotEndTime = strtotime("+".$findDoctorProcedure->Duration." minutes", $startTime);
                        //for($i=$startTime; $i<$endTime; $i = strtotime("+15 minutes", $i)){
                        for($i=$startTime; $i<$endTime; $i = strtotime("+".$findDoctorProcedure->Duration." minutes", $i)){
                            $returnHoliday = StringHelperMobile::HolidayTimeCondition($clinicHolidays,$i);
                            $slotEndTime = strtotime("+".$findDoctorProcedure->Duration." minutes", $i);
                            if($returnHoliday!=1){
                                if($doctorAvailablity['available_times']){
                                    //$findAppointments = General_Library_Mobile::FindProcedureAppointments($allInputdata['doctorid'], $allInputdata['procedureid'], strtotime($currentdate),$i,$slotEndTime);
                                    //$findAppointments = General_Library_Mobile::FindTimelyAppointments($allInputdata['doctorid'],strtotime($currentdate),$i,$slotEndTime);
                                    $findAppointments = General_Library_Mobile::FindTodayAppointments($allInputdata['doctorid'],strtotime($currentdate));
                                    $doctortimecount = 0; $activeAvailability = 0; 
                                    foreach($doctorAvailablity['available_times'] as $doctortime){
                                        $doctorstarttime = strtotime($doctortime['starttime']);
                                        $doctorendtime = strtotime($doctortime['endtime']);
                                        $returnDoctorHoliday = StringHelperMobile::HolidayTimeCondition($doctorAvailablity['holidays'],$i);
                                        if($doctorstarttime <= $i && $doctorendtime > $i && $returnDoctorHoliday !=1){
                                            //if(!$findAppointments && $doctortimecount == 0 && String_Helper_Web::GetActiveTime($i,$currentdate)==1){
                                            if($doctortimecount == 0 && String_Helper_Web::GetActiveTime($i,$currentdate)==1){
                                                if($findAppointments){
                                                    foreach($findAppointments as $todayAppoint){
                                                        if(($todayAppoint->StartTime <= $i && $todayAppoint->EndTime >$i) || ($todayAppoint->StartTime < $slotEndTime && $todayAppoint->EndTime >=$slotEndTime)){
                                                            $activeAvailability=1;
                                                        }
                                                    }
                                                    if($activeAvailability==0){
                                                        $doctortimecount = 1;
                                                        $timeSlotBookings['start_time'] = date('h:i A',$i);
                                                        $timeSlotBookings['end_time'] = date('h:i A',$slotEndTime);
                                                        $timeSlotDetail[] = $timeSlotBookings;
                                                    }else{
                                                        $timeSlotBookings = null;
                                                    }
                                                }else{
                                                    $doctortimecount = 1;
                                                    $timeSlotBookings['start_time'] = date('h:i A',$i);
                                                    $timeSlotBookings['end_time'] = date('h:i A',$slotEndTime);
                                                    $timeSlotDetail[] = $timeSlotBookings;
                                                }
                                                
                                            /*if($findAppointments){
                                                    if(($findAppointments->StartTime <= $i && $findAppointments->EndTime >$i) || ($findAppointments->StartTime < $slotEndTime && $findAppointments->EndTime >=$slotEndTime)){
                                                        $timeSlotBookings = null;
                                                    }else{
                                                        $activeAvailability = 1;
                                                        $doctortimecount = 1;
                                                        $timeSlotBookings['start_time'] = date('h:i A',$i);
                                                        $timeSlotBookings['end_time'] = date('h:i A',$slotEndTime);
                                                        $timeSlotDetail[] = $timeSlotBookings;
                                                    }      
                                            }else{
                                                $activeAvailability = 1;
                                                $doctortimecount = 1;
                                                $timeSlotBookings['start_time'] = date('h:i A',$i);
                                                $timeSlotBookings['end_time'] = date('h:i A',$slotEndTime);
                                                $timeSlotDetail[] = $timeSlotBookings;
                                            }*/
                                            
                                            
                                            
                                            //if($doctortimecount == 0 ){    
                                             //   $activeAvailability = 1;
                                             //   $doctortimecount = 1;
                                             //   $timeSlotBookings['start_time'] = date('h:i A', $i);
                                            //    $timeSlotBookings['end_time'] = date('h:i A',$slotEndTime);
                                            //    $timeSlotDetail[] = $timeSlotBookings;
                                                    
                                                /*if($findAppointments){
                                                    if($findAppointments->UserID == $findUserID){
                                                        //echo '<pre>'; print_r($findAppointments); echo '</pre>';
                                                        $activeAvailability = 1;
                                                        $doctortimecount = 1;
                                                        $timeSlotBookings['start_time'] = date('h:i A', $i);
                                                        $timeSlotBookings['end_time'] = date('h:i A',$slotEndTime);
                                                        $timeSlotBookings['appointmentid'] = $findAppointments->UserAppoinmentID;
                                                        $timeSlotBookings['status'] = 1;
                                                        $timeSlotDetail[] = $timeSlotBookings;
                                                    } 
                                                }else{
                                                    $activeAvailability = 1;
                                                    $doctortimecount = 1;
                                                    $timeSlotBookings['start_time'] = date('h:i A', $i);
                                                    $timeSlotBookings['end_time'] = date('h:i A',$slotEndTime);
                                                    $timeSlotBookings['appointmentid'] = 0;
                                                    $timeSlotBookings['status'] = 0;
                                                    $timeSlotDetail[] = $timeSlotBookings;
                                                }*/
                                                
                                            }  
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $timeBookings['type'] = 2;
                    $timeBookings['procedureid'] = $allInputdata['procedureid'];
                    $timeBookings['bookingdate'] = $currentdate;
                    $timeBookings['price'] = $findDoctorProcedure->Price;
                    $timeBookings['duration'] = $findDoctorProcedure->Duration.' Min';
                    $timeBookings['queue'] = null;
                    $timeBookings['timeslot'] = $timeSlotDetail;

                    //$doctorDetails['availability'] = $doctorAvailablity['available'];
                    //$detailArray['doctor'] = $doctorDetails;
                    //$detailArray['clinic'] = $clinicDetails;
                    $detailArray['booking'] = $timeBookings;
                    $returnObject->status = TRUE;
                    $returnObject->data = $detailArray;
                    
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("NoRecords");
                }
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmptyValues");
        }
        return $returnObject;  
    }
    
    public static function ConfirmSlotBooking($findUserID){
        $returnObject = self::MainBooking($findUserID,1);
        return $returnObject;
    }
    
    /* Use          :   Used to process both booking Queue and Slots
    *  Access       :   Private
    *  Parameter    :   Input array and booking type (Queue / slot)
    * 
    */
    private static function MainBooking($findUserID,$mainbooktype){

        $allInputData = Input::all(); 
        $returnObject = new stdClass();
        StringHelper::Set_Default_Timezone();
        $wallet = new Wallet( );
        $procedure = new ClinicProcedures( );
        $transaction_data = new Transaction( );
        $findClinicDetails = Clinic_Library::FindClinicDetails($allInputData['clinicid']);
        if(count($allInputData) > 0){
            $starttime = strtotime($allInputData['starttime']);
            $endtime = strtotime($allInputData['endtime']);
            $bookingdate = strtotime($allInputData['bookingdate']);
            $bookstartTime = strtotime($allInputData['bookingdate'].$allInputData['starttime']);
            $bookendTime = strtotime($allInputData['bookingdate'].$allInputData['endtime']);
            //$findAppointments = General_Library_Mobile::FindProcedureAppointments($allInputData['doctorid'], $allInputData['procedureid'], $bookingdate,$starttime,$endtime);
            $findAppointments = General_Library_Mobile::FindProcedureAppointments($allInputData['doctorid'], $allInputData['procedureid'], $bookingdate,$bookstartTime,$bookendTime);

                 // dd($findAppointments);       
            if(!$findAppointments){
                if($mainbooktype == 1){
                    $dataArray['userid'] = $findUserID;
                    $dataArray['clinictimeid'] = 0;
                    $dataArray['doctorid'] = $allInputData['doctorid'];
                    $dataArray['procedureid'] = $allInputData['procedureid'];
                    $dataArray['starttime'] = strtotime($allInputData['bookingdate'].$allInputData['starttime']);
                    $dataArray['endtime'] = strtotime($allInputData['bookingdate'].$allInputData['endtime']);
                    //$dataArray['starttime'] = $starttime;
                    //$dataArray['endtime'] = $endtime;
                    $dataArray['remarks'] = $allInputData['remarks'];
                    $dataArray['mediatype'] = 0;
                    $dataArray['bookdate'] = $bookingdate;

                    $p = new ClinicProcedures();
                    $pro = $p->GetClinicProcedure($allInputData['procedureid']);

                    $dataArray['price'] = $pro->Price;
                    $dataArray['duration'] = $pro->Duration;

                    // dd($dataArray);
                    $newAppointment = General_Library_Mobile::NewAppointment($dataArray);
                    if($newAppointment){
                        $findClinicDoctor = self::FindSingleClinicDoctor($allInputData['clinicid'],$allInputData['doctorid']);
                        $findClinicUser = AuthLibrary::FindUserProfileByRefID($findClinicDoctor->ClinicID);
                        $findClinicDetails = Clinic_Library::FindClinicDetails($findClinicDoctor->ClinicID);
                       
                        $userProfile = AuthLibrary::FindUserProfile($findUserID);
                        $findClinicProcedure = General_Library::FindClinicProcedure($allInputData['procedureid']);
                        if($userProfile){
                            if($findClinicProcedure){
                                $procedurename = $findClinicProcedure->Name;
                            }else{
                                $procedurename = null;
                            }
                            
                            //Send SMS
                            if(StringHelper::Deployment()==1){
                                // $smsMessage = "Hello ".$userProfile->Name." your booking with ".$findClinicDoctor->DocName." at ".$findClinicDoctor->CliName." is confirmed on ".$allInputData['bookingdate']." from ".$allInputData['starttime']."to ".$allInputData['endtime'].". Thank you for using medicloud.";
                               
                                // $smsMessage = "Hello ".$userProfile->Name." your appointment with ".$findClinicDoctor->DocName." at ".$findClinicDetails->Name.", ".$findClinicDetails->Address.", Ph:".$findClinicDetails->Phone." is confirmed for ".$allInputData['bookingdate'].", ".$allInputData['starttime'].". Your appointment ID is: ".$newAppointment.", thank you for using Mednefits. Get the free app at mednefits.com";
                                
                                // $sendSMS = StringHelper::SendOTPSMS($userProfile->PhoneNo,$smsMessage);

                                // $smsMessage_2 = "Hello ".$userProfile->Name.", your appointment with ".$findClinicDoctor->DocName." at ".$findClinicDetails->Name.", ".$findClinicDetails->Address.", Ph:".$findClinicDetails->Phone." is tomorrow at ".$allInputData['starttime'].". Thank you for using Mednefits. Get the free app at mednefits.sg ";

                                // $sendSMS = StringHelper::SendOTPSMS($userProfile->PhoneNo,$smsMessage_2);

                                // $saveSMS = StringHelper::saveSMSMLogs($allInputData['clinicid'], $userProfile->Name, $userProfile->PhoneCode, $userProfile->PhoneNo, $smsMessage);

                                // $saveSMS = StringHelper::saveSMSMLogs($allInputData['clinicid'], $userProfile->Name, $userProfile->PhoneCode, $userProfile->PhoneNo, $smsMessage_2);
                                
                            }
                            
                            //Email for User 
                            $formatDate = date('l, j F Y',$bookingdate);
                            $emailDdata['bookingid'] = $newAppointment;
                            $emailDdata['remarks'] = $allInputData['remarks'];
                            $emailDdata['bookingTime'] = date('h:i A',$starttime).' - '.date('h:i A',$endtime);
                            $emailDdata['bookingNo'] = 0;
                            $emailDdata['bookingDate'] = $formatDate; 
                            $emailDdata['doctorName'] = $findClinicDoctor->DocName;
                            $emailDdata['doctorSpeciality'] = $findClinicDoctor->Specialty;
                            $emailDdata['clinicName'] = $findClinicDoctor->CliName;
                            $emailDdata['clinicPhoneCode'] = $findClinicDetails->Phone_Code;
                            $emailDdata['clinicPhone'] = $findClinicDetails->Phone;
                            $emailDdata['clinicAddress'] = $findClinicDoctor->Address;
                            $emailDdata['clinicProcedure'] = $procedurename;

                            $emailDdata['emailName']= $userProfile->Name;
                            $emailDdata['emailPhone']= $userProfile->PhoneNo;
                            $dataArray['patient']=$userProfile->Name;
                            $emailDdata['emailPage']= 'email-templates.booking';
                            $emailDdata['emailTo']= $userProfile->Email;
                            $emailDdata['emailSubject'] = 'Booking Confirmed';
                            // EmailHelper::sendEmail($emailDdata);
                            //Copy to Company
                            $emailDdata['emailTo']= Config::get('config.booking_email');
                            // EmailHelper::sendEmail($emailDdata);
                            //Email for Doctor
                            $emailDdata['emailPage']= 'email-templates.booking-doctor';
                            $emailDdata['emailTo']= $findClinicDoctor->DocEmail;
                            // EmailHelper::sendEmail($emailDdata);
                            //Email to clinic
                            if($findClinicUser){
                                $emailDdata['emailPage']= 'email-templates.booking';
                                $emailDdata['emailTo']= $findClinicUser->Email;
                                // EmailHelper::sendEmail($emailDdata);
                            }
                               
                        } 
                        $returnObject->status = TRUE;
                        $returnObject->data['record_id'] = $newAppointment;
                        $returnObject->data['message'] = "Noted on your preferred timing. This is subjected to clinic’s availability. Clinic will give you a call for any changes to schedule required.";
                        // nhr 2016-2-23  //google event

                        $findDoctorDetails = Doctor_Library::FindDoctorDetails($allInputData['doctorid']);
                        $event_id = Clinic_Library::insertGoogleCalenderAppointment($dataArray,$findDoctorDetails); //nhr
                        $ua = new UserAppoinment();
                        $clinic = new Clinic( );
                        $ua->updateUserAppointment(array('event_type'=>0,'Gc_event_id'=>$event_id),$newAppointment);

                        $getProcedure = $procedure->ClinicProcedureByID($allInputData['procedureid']);
                        $wallet_id = $wallet->getWalletId($findUserID);
                        $discount = $clinic->getClinicPercentage($allInputData['clinicid']);
                        
                        if($findClinicDetails->co_paid_status == 1) {
                            $co_paid_amount = $findClinicDetails->co_paid_amount;
                            $co_paid_status = 1;
                        } else {
                            $co_paid_amount = $findClinicDetails->co_paid_amount;
                            $co_paid_status = 0;
                        }

                        $transaction = array(
                            'wallet_id'             => $wallet_id,
                            'ClinicID'              => $allInputData['clinicid'],
                            'UserID'                => $findUserID,
                            'ProcedureID'           => $allInputData['procedureid'],
                            'DoctorID'              => $allInputData['doctorid'],
                            'AppointmenID'          => $newAppointment,
                            'procedure_cost'        => $getProcedure->Price,
                            'revenue'               => null,
                            'debit'                 => null,
                            'medi_percent'          => $discount['medi_percent'],
                            'clinic_discount'       => $discount['discount'],
                            'co_paid_amount'        => $co_paid_amount,
                            'co_paid_status'        => $co_paid_status,
                            'date_of_transaction'   => Carbon::now(),
                            'created_at'            => Carbon::now(),
                            'updated_at'            => Carbon::now()
                        );

                        $transaction_data->createTransaction($transaction);

                    }
                }
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("OpenBooking");
            }
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmptyValues");
        }
        return $returnObject;
   }
   
   
 /* Use     :   Used to find a doctor in a clinic
  * Access  :   Public 
  * Param   :   Clinic Id and Doctor ID
  */ 
   public static function FindSingleClinicDoctor($clinicid, $doctorid){
        $doctorAvailability = new DoctorAvailability();
        if(!empty($clinicid) && !empty($doctorid)){
            $findClinicDoctor = $doctorAvailability->FindSingleClinicDoctor($clinicid, $doctorid);
            if($findClinicDoctor){
                return $findClinicDoctor;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    
    
    public static function BookingDelete($findUserID){
        $allInputData = Input::all(); 
        $returnObject = new stdClass();
        StringHelper::Set_Default_Timezone();
        if(!empty($allInputData['appointmentid']) && !empty($findUserID)){
            $findAppointment = General_Library_Mobile::FindAppointment($allInputData['appointmentid']);
            if($findAppointment){
                $updateArray['Status'] = 3;
                $updateArray['Active'] = 0;
                $updateAppointment = General_Library_Mobile::UpdateAppointment($updateArray,$findAppointment->UserAppoinmentID);
                if($updateAppointment){
                    $findClinicProcedure = General_Library::FindClinicProcedure($findAppointment->ProcedureID);
                    if($findClinicProcedure){
                        $procedurename = $findClinicProcedure->Name;
                    }else{
                        $procedurename = null;
                    }
                    //Send email
                    $userProfile = AuthLibrary::FindUserProfile($findUserID);
                    $findClinicDoctor = self::FindSingleClinicDoctor($allInputData['clinicid'],$findAppointment->DoctorID);
                    
                    //send SMS
                    if(StringHelper::Deployment()==1){
                        $smsMessage = "Hello ".$userProfile->Name." we are sorry to see you cancelled your booking with ".$findClinicDoctor->DocName." at ".$findClinicDoctor->CliName.". Please feel free to get in touch with us on happiness@mednefits.com and let us know if we can be of any assistance.";
                        $sendSMS = StringHelper::SendOTPSMS($userProfile->PhoneNo,$smsMessage);
                        $saveSMS = StringHelper::saveSMSMLogs($allInputData['clinicid'], $userProfile->Name, $userProfile->PhoneCode, $userProfile->PhoneNo, $smsMessage);
                    }
                    //Email to User 
                    $formatDate = date('l, j F Y',$findAppointment->BookDate);
                    $emailDdata['bookingid'] = $findAppointment->UserAppoinmentID;
                    $emailDdata['remarks'] = $findAppointment->Remarks;
                    $emailDdata['bookingTime'] = date('h:i A',$findAppointment->StartTime).' - '.date('h:i A',$findAppointment->EndTime);
                    $emailDdata['bookingNo'] = 0;
                    $emailDdata['bookingDate'] = $formatDate; 
                    $emailDdata['doctorName'] = $findClinicDoctor->DocName;
                    $emailDdata['doctorSpeciality'] = $findClinicDoctor->Specialty;
                    $emailDdata['clinicName'] = $findClinicDoctor->CliName;
                    $emailDdata['clinicAddress'] = $findClinicDoctor->Address;
                    $emailDdata['clinicProcedure'] = $procedurename;

                    $emailDdata['emailName']= $userProfile->Name;
                    $emailDdata['emailPage']= 'email-templates.booking-cancel';
                    $emailDdata['emailTo']= $userProfile->Email;
                    $emailDdata['emailSubject'] = 'Booking Cancelled';
                    EmailHelper::sendEmail($emailDdata);

                    $gc = new GoogleCalenderController();
                    try {
                    $gc->removeEvent($findAppointment->DoctorID,$findAppointment->Gc_event_id);
                        
                    } catch (Exception $e) {}
                
                    $returnObject->status = TRUE;
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("Update");
                }
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("NoRecords");
            }
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmptyValues");
        }
        return $returnObject;
    }
    public static function FindSingleClinicDoctorBoth($clinicid, $doctorid){
        $doctorAvailability = new DoctorAvailability();
        if(!empty($clinicid) && !empty($doctorid)){
            $findClinicDoctor = $doctorAvailability->FindSingleClinicDoctorBoth($clinicid, $doctorid);
            if($findClinicDoctor){
                return $findClinicDoctor;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    //End of class
}
