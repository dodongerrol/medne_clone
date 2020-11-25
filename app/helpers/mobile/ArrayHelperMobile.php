<?php
use Illuminate\Support\Facades\Input;

class ArrayHelperMobile{
    
    public static function ClinicProfile($clinicData,$currentDate,$findUserID){
        if(!empty($clinicData)){
            $clinicOpenTime = self::newProcessClinicOpeningTimes($clinicData->ClinicOPenTime, $clinicData->ClinicID);
            //$clinicActiveHoliday = self::ProcessClinicHolidays($clinicData->ClinicHolidays);
            $clinicActiveHoliday = self::ProcessClinicHolidays($clinicData->ClinicHolidays,$currentDate);
            // $clinicOpenStatus = StringHelperMobile::FindClinicOpenStatus(3,$clinicData->ClinicID,$clinicActiveHoliday,$currentDate);
            $clinicOpenStatus = Clinic_Library_v1::openStatus($clinicData->ClinicID); //nhr

            ($clinicData->Email) ? $email =$clinicData->Email : $email = null;
            ($clinicData->Description) ? $descr = $clinicData->Description : $descr = null;
            ($clinicData->Website) ? $website = $clinicData->Website : $website = null;
            ($clinicData->Custom_title) ? $custitle = $clinicData->Custom_title : $custitle = null;
            ($clinicData->Clinic_Price) ? $clprice = $clinicData->Clinic_Price : $clprice = null;
            
            if($clinicData->Phone_Code != null && strpos($clinicData->Phone_Code, '+') !== false) {
                if(strpos($clinicData->Phone, '+') !== false) {
                    $jsonArray['telephone'] = $clinicData->Phone;
                } else { 
                    $jsonArray['telephone'] = $clinicData->Phone_Code.$clinicData->Phone;
                }
            } else {
                if(strpos($clinicData->Phone, '+') !== false) {
                    $jsonArray['telephone']= $clinicData->Phone;
                } else {
                    $jsonArray['telephone']= '+'.$clinicData->Phone;
                }
            }

            $jsonArray['clinic_id']= $clinicData->ClinicID;
            $jsonArray['name']= $clinicData->CLName;
            $jsonArray['email']= $email;
            $jsonArray['address']= $clinicData->CLAddress.' '.$clinicData->CLCity.' '.$clinicData->CLState.' '.$clinicData->CLPostal;
            $jsonArray['image_url']= $clinicData->CLImage;
            $jsonArray['lattitude']= $clinicData->CLLat;
            $jsonArray['longitude']= $clinicData->CLLng;
            $jsonArray['description']= $descr;
            $jsonArray['website']= $website;
            $jsonArray['custom_title']= $custitle;
            $jsonArray['clinic_price']= $clprice;
            $jsonArray['open']= $clinicOpenTime;
            $jsonArray['holidays']= $clinicActiveHoliday;
            $jsonArray['open_status']= $clinicOpenStatus;
            

            try {
            
                if ($findUserID==FALSE || $findUserID==NULL) {
                   $favourite = null;
                }else{
                    $fav = new ClinicUserFavourite();
                    $exist = $fav->getStatus($clinicData->ClinicID,$findUserID);
                    if ($exist) {
                        $favourite = $exist->favourite;
                    } else {
                        $favourite = 0;
                    }
                }

                $jsonArray['favourite']= $favourite;
            } catch (Exception $e) {
                $jsonArray['favourite']= null;
            }

            return $jsonArray;
        }else{
            return null;
        }
        
    }
    

    public static function ClinicProfile_new($clinicData,$currentDate,$findUserID){ //nhr
        //StringHelper::Set_Default_Timezone();
        if(!empty($clinicData)){
            $clinicOpenTime = self::ProcessClinicOpeningTimes($clinicData->ClinicOPenTime);
            //$clinicActiveHoliday = self::ProcessClinicHolidays($clinicData->ClinicHolidays);
            // $clinicActiveHoliday = self::ProcessClinicHolidays($clinicData->ClinicHolidays,$currentDate);
            // $clinicOpenStatus = StringHelperMobile::FindClinicOpenStatus(3,$clinicData->ClinicID,$clinicActiveHoliday,$currentDate);
            $clinicOpenStatus = Clinic_Library_v1::openStatus($clinicData->ClinicID); //nhr

            ($clinicData->Email) ? $email =$clinicData->Email : $email = null;
            ($clinicData->Description) ? $descr = $clinicData->Description : $descr = null;
            ($clinicData->Website) ? $website = $clinicData->Website : $website = null;
            ($clinicData->Custom_title) ? $custitle = $clinicData->Custom_title : $custitle = null;
            ($clinicData->Clinic_Price) ? $clprice = $clinicData->Clinic_Price : $clprice = null;
            
            $jsonArray['clinic_id']= $clinicData->ClinicID;
            $jsonArray['name']= $clinicData->CLName;
            $jsonArray['email']= $email;
            $jsonArray['address']= $clinicData->CLAddress.' '.$clinicData->CLCity.' '.$clinicData->CLState.' '.$clinicData->CLPostal;
            $jsonArray['image_url']= FileHelper::formatImageAutoQuality($clinicData->CLImage);
            $jsonArray['lattitude']= $clinicData->CLLat;
            $jsonArray['longitude']= $clinicData->CLLng;
            $jsonArray['telephone']= $clinicData->Phone;   
            $jsonArray['description']= $descr;
            $jsonArray['website']= $website;
            $jsonArray['custom_title']= $custitle;
            $jsonArray['clinic_price']= $clprice;
            // $jsonArray['open']= $clinicOpenTime;
            // $jsonArray['holidays']= $clinicActiveHoliday;
            $jsonArray['open_status']= $clinicOpenStatus;
            $jsonArray['clinic_type']= $clinicData->ClinicType;
            
            // dd($findUserID);

        try {
            
            if ($findUserID==FALSE || $findUserID==NULL) {
               $favourite = null;
            }else{
                $fav = new ClinicUserFavourite();
                $exist = $fav->getStatus($clinicData->ClinicID,$findUserID);
                if ($exist) {
                    $favourite = $exist->favourite;
                } else {
                    $favourite = 0;
                }
            }

            $jsonArray['favourite']= $favourite;
        } catch (Exception $e) {
            $jsonArray['favourite']= null;
        }


            //$jsonArray['annotation_url']= $clprice;
            
            return $jsonArray;
        }else{
            return null;
        }
        
    }

    public static function ClinicDoctors($clinicDoctors){
        if(!empty($clinicDoctors)){
            foreach($clinicDoctors as $clDoctor){
                $doctorArray = self::ClinicDoctorDetails($clDoctor);
                
                //$doctorArray['doctor_id']= $clDoctor->DoctorID;
                //$doctorArray['name']= $clDoctor->DocName;
                //$doctorArray['qualifications']= $clDoctor->Qualifications;
                //$doctorArray['specialty']= $clDoctor->Specialty;
                //$doctorArray['image_url']= $clDoctor->DocImage;

                $returnArray[] = $doctorArray; 
            }
            $jsonArray = $returnArray;
            return $jsonArray;
        }else{
            $jsonArray = [];
            return $jsonArray;
        }  
    }
    public static function ClinicDoctorDetails($doctordetails){
        // if(!empty($doctordetails)){
        //         // return $doctordetails;
        //         $doctorArray['doctor_id'] = $doctordetails->DoctorID;
        //         $doctorArray['name'] = $doctordetails->DocName;
        //         $doctorArray['qualifications'] = $doctordetails->Qualifications;
        //         $doctorArray['specialty'] = $doctordetails->Specialty;

        //         if(isset($doctordetails->phone_code)) {
        //             $doctordetails->phone_code = $doctordetails->phone_code;
        //         } else if(isset($doctordetails->DocPhone)) {
        //             $doctordetails->phone_code = $doctordetails->DocPhone;
        //         }

        //         if(isset($doctordetails->phone_code)) {
        //             if($doctordetails->phone_code) {
        //                 if(strpos($doctordetails->phone_code, '+') !== false) {
        //                     $doctorArray['DocPhone'] = $doctordetails->phone_code;
        //                 } else {
        //                     $doctorArray['DocPhone'] = $doctordetails->phone_code.$doctordetails->DocPhone;
        //                 }
        //             } else {
        //                 $doctorArray['DocPhone'] = $doctordetails->DocPhone;
        //             }
        //         } else {
        //             if(isset($doctordetails->phone_code)) {
        //                 if(strpos($doctordetails->CliPhone, '+') !== false) {
        //                     $doctorArray['DocPhone'] = $doctordetails->phone_code;
        //                 } else {
        //                     $doctorArray['DocPhone'] = $doctordetails->phone_code.$doctordetails->Phone;
        //                 }
        //             } else {
        //                 $doctorArray['DocPhone'] = $doctordetails->Phone;
        //             }
        //         }
        //         if(empty($doctordetails->DocImage)){
        //             $doctorArray['image_url']= URL::to('/assets/images/no-doctor.png');
        //         }else{
        //             $doctorArray['image_url']= $doctordetails->DocImage;
        //         }
                
        //     return $doctorArray;
        // }else{
        //     $doctorArray = null;
        //     return $doctorArray;
        // }
        if(!empty($doctordetails)){
                // return $doctordetails;
                $doctorArray['doctor_id'] = $doctordetails->DoctorID;
                $doctorArray['name'] = $doctordetails->DocName;
                $doctorArray['qualifications'] = $doctordetails->Qualifications;
                $doctorArray['specialty'] = $doctordetails->Specialty;
                if($doctordetails->DocPhone) {
                    if($doctordetails->DocPhoneCode) {
                        if(strpos($doctordetails->DocPhone, '+') !== false) {
                            $doctorArray['DocPhone'] = $doctordetails->DocPhone;
                        } else {
                            $doctorArray['DocPhone'] = $doctordetails->DocPhoneCode.$doctordetails->DocPhone;
                        }
                    } else {
                        $doctorArray['DocPhone'] = $doctordetails->DocPhone;
                    }
                } else {
                    if($doctordetails->CliPhoneCode) {
                        if(strpos($doctordetails->CliPhone, '+') !== false) {
                            $doctorArray['DocPhone'] = $doctordetails->CliPhone;
                        } else {
                            $doctorArray['DocPhone'] = $doctordetails->CliPhoneCode.$doctordetails->CliPhone;
                        }
                    } else {
                        $doctorArray['DocPhone'] = $doctordetails->CliPhone;
                    }
                }
                if(empty($doctordetails->DocImage)){
                    $doctorArray['image_url']= URL::to('/assets/images/no-doctor.png');
                }else{
                    $doctorArray['image_url']= $doctordetails->DocImage;
                }
                
            return $doctorArray;
        }else{
            $doctorArray = [];
            return $doctorArray;
        }
    }

    public static function newClinicDoctorDetails($doctordetails){
        if(!empty($doctordetails)){
                // return $doctordetails;
                $doctorArray['doctor_id'] = $doctordetails->DoctorID;
                $doctorArray['name'] = $doctordetails->DocName;
                $doctorArray['qualifications'] = $doctordetails->Qualifications;
                $doctorArray['specialty'] = $doctordetails->Specialty;
                if($doctordetails->DocPhone) {
                    if($doctordetails->DocPhoneCode) {
                        if(strpos($doctordetails->DocPhone, '+') !== false) {
                            $doctorArray['DocPhone'] = $doctordetails->DocPhone;
                        } else {
                            $doctorArray['DocPhone'] = $doctordetails->DocPhoneCode.$doctordetails->DocPhone;
                        }
                    } else {
                        $doctorArray['DocPhone'] = $doctordetails->DocPhone;
                    }
                } else {
                	$doctor_availability = DB::table('doctor_availability')
                	                        ->where('DoctorID', $doctordetails->DoctorID)
                	                        ->first();
                	if($doctor_availability) {
                    $clinic = DB::table('clinic')->where('ClinicID', $doctor_availability->ClinicID)->first();

                    if($clinic) {
                      if(strpos($clinic->Phone, '+') !== false) {
                          $doctorArray['DocPhone'] = $clinic->Phone;
                      } else {
                          $doctorArray['DocPhone'] = $clinic->Phone_Code.$clinic->Phone;
                      }
                    } else {
                    	$doctorArray['DocPhone'] = null;
                    }
                	} else {
                		$doctorArray['DocPhone'] = null;
                	}
                }
                if(empty($doctordetails->DocImage)){
                    $doctorArray['image_url']= URL::to('/assets/images/no-doctor.png');
                }else{
                    $doctorArray['image_url']= $doctordetails->DocImage;
                }
                
            return $doctorArray;
        }else{
            $doctorArray = null;
            return $doctorArray;
        }
    }
    public static function ClinicProcedures($clinicprocedures){
        $procedureArray = [];
        if(!empty($clinicprocedures)){
            foreach($clinicprocedures as $clProcedure){
                $dataArray = self::ClinicProcedureDetails($clProcedure);
                $procedureArray[] = $dataArray;
            }
            return $procedureArray;
        }else{
            // $procedureArray = null;
            return $procedureArray;
        }
    }
    public static function ClinicProcedureDetails($clinicprocedures){
        $input = Input::all();
        $lang = isset($input['lang']) ? $input['lang'] : "en";
        if(!empty($clinicprocedures)){
                $dataArray['procedureid'] = $clinicprocedures->ProcedureID;
                $dataArray['name'] = $lang == "malay" ? \MalayTranslation::servicesCategory($clinicprocedures->Name) : $clinicprocedures->Name;
                $dataArray['duration'] = $clinicprocedures->Duration.' '.$clinicprocedures->Duration_Format;
                $dataArray['price'] = $lang == "malay" && strtolower($clinicprocedures->Price) == "as charged" || $lang == "malay" && strtolower($clinicprocedures->Price) == "as charge" ?  \MalayTranslation::extraTextTranslate($clinicprocedures->Price) : $clinicprocedures->Price;
            return $dataArray;
        }else{
            $dataArray = null;
            return $dataArray;
        }
    }
    
    
    /* Use      :   Used to find Clinic opening times
     * Access   :   Public
     */
    public static function ProcessClinicOpeningTimes($clinicopentimes){
        if($clinicopentimes!=false){
            foreach($clinicopentimes as $CLTimeValue){
                $weeks = StringHelper::GetOpenWeeks($CLTimeValue);
                $cltime['timeid'] = $CLTimeValue->ClinicTimeID;
                $cltime['weeks'] =  $weeks;
                $cltime['starttime'] = $CLTimeValue->StartTime; 
                $cltime['endtime'] =  $CLTimeValue->EndTime; 
                $clinictime[] = $cltime;
            }
            $clinicOpenTime = $clinictime;
        }else{
            $clinicOpenTime = null;
        }
        return $clinicOpenTime;
    }

    public static function newProcessClinicOpeningTimes($clinicopentimes, $clinic_id){
        $clinictime = [];
        $input = Input::all();
        $lang = isset($input['lang']) ? $input['lang'] : "en";

        if($clinicopentimes!=false){
            foreach($clinicopentimes as $CLTimeValue){
                $weeks = StringHelper::GetOpenWeeks($CLTimeValue);
                $results = Settings_Library::getNewClinicBreaksByWeek($clinic_id, $weeks);

                if($results) {
                    usort($results, function($a, $b){
                        return strtotime($a->start_time) - strtotime($b->start_time); 
                    });

                    $cltime['weeks'] =  $weeks;
                    $cltime['timeid'] = $CLTimeValue->ClinicTimeID;
                    $temp_first_times = [];
                    $temp_second_times = [];
                    $starttime = $CLTimeValue->StartTime;
                    $endtime =  $CLTimeValue->EndTime; 

                    $starttime_str = strtotime(date('Y-m-d h:i A', strtotime($CLTimeValue->StartTime)));
                    $endtime_str =  strtotime(date('Y-m-d h:i A', strtotime($CLTimeValue->EndTime)));

                    $temp_break = null;

                    // return $results;

                    foreach ($results as $key => $result) {
                        $start_break = strtotime(date('Y-m-d h:i A', strtotime($result->start_time)));
                        $end_break = strtotime(date('Y-m-d h:i A', strtotime($result->end_time)));

                        if( $key == 0 ){
                            $temp_first_times['starttime'] = $starttime;
                            $temp_first_times['endtime'] = $result->start_time;
                            $temp_first_times['timeid'] = $cltime['timeid'];
                            $temp_first_times['weeks'] = $lang == "malay" ? \MalayTranslation::dayTransalation($cltime['weeks']) : $cltime['weeks'];
                            $clinictime[] = $temp_first_times;
                            $temp_break = $result;
                        }else{
                            $temp_first_times['starttime'] = $temp_break->end_time;
                            $temp_first_times['endtime'] = $result->start_time;
                            $temp_first_times['timeid'] = $cltime['timeid'];
                            $temp_first_times['weeks'] = $lang == "malay" ? \MalayTranslation::dayTransalation($cltime['weeks']) : $cltime['weeks'];
                            $clinictime[] = $temp_first_times;
                            $temp_break = $result;
                        }

                        if( $key == sizeof($results) - 1 ){
                            $temp_second_times['starttime'] = $result->end_time;
                            $temp_second_times['endtime'] = $endtime;
                            $temp_second_times['timeid'] = $cltime['timeid'];
                            $temp_second_times['weeks'] = $lang == "malay" ? \MalayTranslation::dayTransalation($cltime['weeks']) : $cltime['weeks'];
                            $clinictime[] = $temp_second_times;
                        }
                    }
                } else {
                    $cltime['weeks'] =  $lang == "malay" ? \MalayTranslation::dayTransalation($weeks) : $weeks;
                    $cltime['timeid'] = $CLTimeValue->ClinicTimeID;
                    $cltime['starttime'] = $CLTimeValue->StartTime;
                    $cltime['endtime'] =  $CLTimeValue->EndTime;
                    $cltime['endtime_str'] =  null;
                    $cltime['starttime_str'] = null;
                    array_push($clinictime, $cltime);
                }

                


            }
            $clinicOpenTime = $clinictime;
        }else{
            $clinicOpenTime = $clinictime;
        }
        return $clinicOpenTime;
    }
    
    /* Use      :   Used to find clinic upcoming holidays
     * Access   :   Public 
     */
    public static function ProcessClinicHolidays1($clinicholidays,$currentdate){
        if($clinicholidays!=false){
            $clinicholiday = array();
// dd($clinicholidays);
                foreach($clinicholidays as $CLHolidayValue){
                    if(strtotime($currentdate) <= strtotime($CLHolidayValue->Holiday)){
                        $clholiday['holidayid'] = $CLHolidayValue->ManageHolidayID;
                        $clholiday['type'] = $CLHolidayValue->Type;
                        $clholiday['holiday'] = $CLHolidayValue->Holiday;
                        $clholiday['starttime'] = $CLHolidayValue->From_Time;
                        $clholiday['endtime'] = $CLHolidayValue->To_Time;

                        $clinicholiday[] = $clholiday;
                    }
                }
                $clinicActiveHoliday = $clinicholiday;
             

        }else{
            $clinicActiveHoliday = null;
        }
        return $clinicActiveHoliday;
    }


    public static function ProcessClinicHolidays($clinicholidays,$currentdate){
            if($clinicholidays!=false){
                $clinicholiday = array();
                
                if(is_array($clinicholidays)){
                    foreach($clinicholidays as $CLHolidayValue){
                        if(strtotime($currentdate) <= strtotime($CLHolidayValue->Holiday)){
                            $clholiday['holidayid'] = $CLHolidayValue->ManageHolidayID;
                            $clholiday['type'] = $CLHolidayValue->Type;
                            $clholiday['holiday'] = $CLHolidayValue->Holiday;
                            $clholiday['starttime'] = $CLHolidayValue->From_Time;
                            $clholiday['endtime'] = $CLHolidayValue->To_Time;

                            $clinicholiday[] = $clholiday;
                        }
                    }
                    $clinicActiveHoliday = $clinicholiday;
                } else {

                        if(strtotime($currentdate) <= strtotime($clinicholidays->Holiday)){
                            $clholiday['holidayid'] = $clinicholidays->ManageHolidayID;
                            $clholiday['type'] = $clinicholidays->Type;
                            $clholiday['holiday'] = $clinicholidays->Holiday;
                            $clholiday['starttime'] = $clinicholidays->From_Time;
                            $clholiday['endtime'] = $clinicholidays->To_Time;

                            $clinicholiday[] = $clholiday;
                        }
                    
                    $clinicActiveHoliday = $clinicholiday;

                }

            }else{
                $clinicActiveHoliday = null;
            }
            return $clinicActiveHoliday;
        }
    
    public static function DoctorDetailArray($doctorid,$findWeek,$currentDate){
        if(!empty($doctorid)){
            $findDoctorAvailability = General_Library::FindCurrentDayAvailableTimes(2,$doctorid,$findWeek,strtotime($currentDate));
            $doctorHoliday = General_Library::FindPartyFullDayHolidays(2,$doctorid,$currentDate); 
            (!$doctorHoliday && $findDoctorAvailability) ? $activeDoctorTime = 1 : $activeDoctorTime = 0;
            $doctorDayHolidays = General_Library::FindCurrentDayHolidays(2,$doctorid,$currentDate); 
            
            $doctorDetail['available'] = $activeDoctorTime;
            $doctorDetail['available_times'] = Array_Helper::ClinicAvailabilityArray($findDoctorAvailability);
            $doctorDetail['holidays'] = self::ReturnHolidayArray($doctorDayHolidays);
            $doctorDetail['existingappointments'] = General_Library::FindAllExistingAppointments($doctorid, strtotime($currentDate));
            //$doctorlist[] = $doctorDetail;
        }else{
            $doctorDetail = null;
        }
        return $doctorDetail;
        
        /*
        if(!empty($findAllDoctors)){
            foreach($findAllDoctors as $findDoctor){     
                $doctorDetail = self::DoctorDetails($findDoctor);
                
                $findDoctorAvailability = General_Library::FindCurrentDayAvailableTimes(2,$findDoctor->DoctorID,$findWeek,strtotime($currentDate));
                $doctorHoliday = General_Library::FindPartyFullDayHolidays(2,$findDoctor->DoctorID,$currentDate); 
                (!$doctorHoliday && $findDoctorAvailability) ? $activeDoctorTime = 1 : $activeDoctorTime = 0;
                $doctorDayHolidays = General_Library::FindCurrentDayHolidays(2,$findDoctor->DoctorID,$currentDate);        

                $doctorDetail['available'] = $activeDoctorTime;
                $doctorDetail['available_times'] = self::ClinicAvailabilityArray($findDoctorAvailability);
                $doctorDetail['holidays'] = self::ReturnHolidayArray($doctorDayHolidays);
                $doctorDetail['existingappointments'] = General_Library::FindAllExistingAppointments($findDoctor->DoctorID, strtotime($currentDate));
                $doctorlist[] = $doctorDetail;
            }
        }else{
            $doctorlist = null;
        }
        return $doctorlist; */
    }

    public static function ReturnHolidayArray($findHolidays){
        if($findHolidays){
            // foreach($findHolidays as $holidays){ 
                $clinicholidaydata['holidayid'] = $findHolidays->ManageHolidayID;
                $clinicholidaydata['type'] = $findHolidays->Type;
                $clinicholidaydata['holiday'] = $findHolidays->Holiday;
                $clinicholidaydata['starttime'] = $findHolidays->From_Time;
                $clinicholidaydata['endtime'] = $findHolidays->To_Time;
                $myclinicholiday[] = $clinicholidaydata;
            // }
        }else{
            $myclinicholiday = null;
        }
        return $myclinicholiday;
    }

    //end of Class
}
