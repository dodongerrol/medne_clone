<?php
use Illuminate\Support\Facades\Input;

class Clinic_Library_v1{
    public static function FindClinic($clinicid){
        $clinic = new Clinic();
        $clinicData = $clinic->ClinicDetails($clinicid);
        if($clinicData){
            return $clinicData;
        }else{
            return FALSE;
        }
    }
    
    public static function FindClinicProfile($clinicid){
        $clinic = new Clinic();
        $clinicData = $clinic->FindClinicProfile($clinicid);
        if($clinicData){
            return $clinicData;
        }else{
            return FALSE;
        }
    }
    
    /* Use      :   Used to send clinic details service 
     * Access   :   Public Mobile
     */
    public static function ClinicDetails($clinicid,$findUserID){
        StringHelper::Set_Default_Timezone();
        $returnObject = new stdClass();
        $currentTime = date('d-m-Y');
        $clinicData = self::FindClinicProfile($clinicid);
        if($clinicData){
            
            
            $clinicProcedures = self::FindClinicProcedures($clinicid);
            $findClinicTimes = General_Library::FindAllClinicTimes(3,$clinicData->ClinicID,  strtotime($currentTime));
            $findClinicHolidays = General_Library::FindUpcomingHolidays(3,$clinicData->ClinicID,$currentTime);
            $clinicData->ClinicOPenTime = $findClinicTimes;
            $clinicData->ClinicHolidays = $findClinicHolidays;
            $clinicData->Email = $clinicData->Email;
            $jsonArray = ArrayHelperMobile::ClinicProfile($clinicData,$currentTime,$findUserID);
            $doctorsForClinic = Doctor_Library_v1::FindAllClinicDoctors($clinicData->ClinicID); 
            if($doctorsForClinic){
                $jsonArray['doctor_count'] = count($doctorsForClinic);
                $jsonArray['doctors'] = ArrayHelperMobile::ClinicDoctors($doctorsForClinic);   
            }else{
                $jsonArray['doctor_count'] = 0;
                $jsonArray['doctors'] = [];
            }
            $jsonArray['clinic_procedures'] = ArrayHelperMobile::ClinicProcedures($clinicProcedures);
            
            $returnObject->status = TRUE;
            $returnObject->data = $jsonArray;   
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("NoRecords");  
        }
        return $returnObject;  
    }
    
    /* Use     :   Used to get all clinic procedures 
     * Access   :   Public 
     */
    public static function FindClinicProcedures($clinicid){
        $clinicprocedure = new ClinicProcedures();
        $findClinicProcedure = $clinicprocedure->FindClinicProcedures($clinicid);
        if($findClinicProcedure){
            return $findClinicProcedure;
        }else{
            return FALSE;
        }
    }
    
    /* Use      :   Used to get doctors from a particular procedure
     * Access   :   PUBLIC
     * 
     */
    public static function ProcedureDetails($procedureid){
        $returnObject = new stdClass();
        $findClinicProcedure = self::FindClinicProcedure($procedureid);
        if($findClinicProcedure){
            $findDoctorProcedure = Doctor_Library_v1::FindDoctorsByProcedure($procedureid,$findClinicProcedure->ClinicID);
            if($findDoctorProcedure){
                $doctorArray = ArrayHelperMobile::ClinicDoctors($findDoctorProcedure); 
                $dataArray = ArrayHelperMobile::ClinicProcedureDetails($findClinicProcedure);

                $dataArray['doctors'] = $doctorArray;
                
                $returnObject->status = TRUE;
                $returnObject->data = $dataArray;
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("NoDoctor");  
            }
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("NoRecords"); 
        }
        return $returnObject;  
    }
    
    /* Use          :   Used to find doctors procedure list 
     * Access       :   Public 
     * Parameter    :   doctor id 
     */
    public static function ClinicDoctorProcedures($doctorid){
        $returnObject = new stdClass();
        $findDoctor = Doctor_Library_v1::FindDoctorDetails($doctorid);
        if($findDoctor){
            $findDoctor->DocName = $findDoctor->Name;
            $findDoctor->DocImage = $findDoctor->image;
            $findDoctor->DocPhone = $findDoctor->Phone;
            $findDoctor->DocPhoneCode = $findDoctor->phone_code;
            $dataArray = ArrayHelperMobile::newClinicDoctorDetails($findDoctor);
            //$dataArray['doctorid'] = $findDoctor->DoctorID;
            $doctorProcedureList = Doctor_Library_v1::FindDoctorsBProcedureList($findDoctor->DoctorID);
            if($doctorProcedureList){
                $findProcedureList = ArrayHelperMobile::ClinicProcedures($doctorProcedureList);
                $dataArray['clinic_procedures'] = $findProcedureList;
                
                $returnObject->status = TRUE;
                $returnObject->data = $dataArray;
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("NoProcedure");
            }
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("NoRecords");
        }
        return $returnObject;
    }
    
    

    /* Use          :   Used to find clinic procedure details
     * Parameter    :   procedure id 
     */ 
    public static function FindClinicProcedure($procedureid){
        if(!empty($procedureid)){
            $clinicprocedure = new ClinicProcedures();
            $findClinicPorcedure = $clinicprocedure->ClinicProcedureByID($procedureid);
            if($findClinicPorcedure){
                return $findClinicPorcedure;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }
    
    public static function AppointmentHistory($findUserID){
        $currentdate = date('d-m-Y');
        $returnObject = new stdClass();
        //$allAppointments = ClinicLibrary::FindAllUserAppointment($findUserID);
        $allAppointments = General_Library_Mobile::FindUserAppointments($findUserID);
        if($allAppointments){
            $upcoming = null; $history = null;
            
            foreach($allAppointments as $appointment){
                $findPatientCount = General_Library_Mobile::NumberOfBookings($appointment->DoctorID,$appointment->BookDate);
                //$findClinicProcedure = General_Library::FindClinicProcedure($appointment->ProcedureID);
                $findClinicProcedure = General_Library::ClinicProcedureBoth($appointment->ProcedureID);
                //$findClinicDoctor = Doctor_Library_v1::FindSingleClinicDoctor($findClinicProcedure->ClinicID,$appointment->DoctorID);
                $findClinicDoctor = Doctor_Library_v1::FindSingleClinicDoctorBoth($findClinicProcedure->ClinicID, $appointment->DoctorID);
                $findClinicTimes = General_Library::FindAllClinicTimes(3,$findClinicProcedure->ClinicID, strtotime($currentdate));
                $clinicOpenTime = ArrayHelperMobile::ProcessClinicOpeningTimes($findClinicTimes);
                
                $dataBook['booking']['booking_id'] = $appointment->UserAppoinmentID;
                $dataBook['booking']['type'] = 2;
                $dataBook['booking']['date'] = date('d-m-Y',$appointment->BookDate);
                $dataBook['booking']['book_date'] = date('m-d-Y',$appointment->BookDate);
                $dataBook['booking']['time'] = date('h:i A',$appointment->StartTime).' - '.date('h:i A',$appointment->EndTime);
                $dataBook['booking']['no_of_patients'] = $findPatientCount;
                //$dataBook['booking']['now_serving'] = $nowSaving;
                $dataBook['booking']['fee'] = $findClinicProcedure->Price;
                $dataBook['booking']['queue_no'] = $appointment->BookNumber;
                
                $dataBook['doctor']['doctor_id'] = $findClinicDoctor->DoctorID;
                $dataBook['doctor']['name'] = $findClinicDoctor->DocName;
                $dataBook['doctor']['qualifications'] = $findClinicDoctor->Qualifications;
                $dataBook['doctor']['specialty'] = $findClinicDoctor->Specialty;
                $dataBook['doctor']['image_url'] = $findClinicDoctor->DocImage;
                
                $dataBook['clinic']['clinic_id'] = $findClinicDoctor->ClinicID;
                //$dataBook['clinic']['annotation_url'] = $findInsurance['annotation_url'];       
                $dataBook['clinic']['image_url'] = $findClinicDoctor->CliImage;
                $dataBook['clinic']['name'] = $findClinicDoctor->CliName;
                $dataBook['clinic']['address'] = $findClinicDoctor->Address;
                $dataBook['clinic']['lattitude'] = $findClinicDoctor->Lat;
                $dataBook['clinic']['longitude'] = $findClinicDoctor->Lng;
                $dataBook['clinic']['telephone'] = $findClinicDoctor->CliPhone;
                $dataBook['clinic']['open'] = $clinicOpenTime;
                $dataBook['clinic']['doctor_count'] = DoctorLibrary::FindDoctorCount($findClinicDoctor->ClinicID);
                //$dataBook['clinic']['panel_insurance']['insurance_id'] = $findInsurance['insurance_id'];
                //$dataBook['clinic']['panel_insurance']['name'] = $findInsurance['name'];
                //$dataBook['clinic']['panel_insurance']['image_url'] = URL::to('/assets/'.$findInsurance['image_url']);   
                if($appointment->Status == 0){
                    $upcoming[] = $dataBook;
                }else{
                    $history[] = $dataBook;
                }
                
            } 
            $totalEvents['upcoming'] = $upcoming;
            $totalEvents['history'] = $history;
            $returnObject->status = TRUE;
            $returnObject->data = $totalEvents;
            
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("NoRecords");
        }
        return $returnObject;
    }

    
    public static function AppointmentDetails($findUserID,$appointmentid){
        $returnObject = new stdClass();
        $appointment = ClinicLibrary::FindAppointment($appointmentid);
        $transaction = new Transaction( );
        // dd($appointment);
        if($appointment){
                $findPatientCount = General_Library_Mobile::NumberOfBookings($appointment->DoctorID,$appointment->BookDate);
                //$findClinicProcedure = General_Library::FindClinicProcedure($appointment->ProcedureID);
                $findClinicProcedure = General_Library::ClinicProcedureBoth($appointment->ProcedureID);
                //$findClinicDoctor = Doctor_Library_v1::FindSingleClinicDoctor($findClinicProcedure->ClinicID,$appointment->DoctorID);
                $findClinicDoctor = Doctor_Library_v1::FindSingleClinicDoctorBoth($findClinicProcedure->ClinicID,$appointment->DoctorID);
                $findClinicTimes = General_Library::FindAllClinicTimes(3,$findClinicProcedure->ClinicID, $appointment->BookDate);
                $clinicOpenTime = ArrayHelperMobile::ProcessClinicOpeningTimes($findClinicTimes);
                
                 $fav = new ClinicUserFavourite();
                $exist = $fav->getStatus($findClinicDoctor->ClinicID,$findUserID);
                if ($exist) {
                    $favourite = $exist->favourite;
                } else {
                    $favourite = 0;
                }

                $dataBook['booking']['booking_id'] = $appointment->UserAppoinmentID;
                $dataBook['booking']['type'] = 2;
                $dataBook['booking']['date'] = date('d-m-Y',$appointment->BookDate);
                $dataBook['booking']['book_date'] = date('m-d-Y',$appointment->BookDate);
                //$dataBook['booking']['time'] = date('h:i A',$appointment->StartTime).' - '.date('h:i A',$appointment->EndTime);
                $dataBook['booking']['time'] = date('h:i A',$appointment->StartTime);
                $dataBook['booking']['no_of_patients'] = $findPatientCount;
                //$dataBook['booking']['now_serving'] = $nowSaving;
                $dataBook['booking']['fee'] = $findClinicProcedure->Price;
                $dataBook['booking']['queue_no'] = $appointment->BookNumber;
                $dataBook['booking']['appoint_status'] = $appointment->Status;
                
                $dataBook['procedure']['procedure_id'] = $findClinicProcedure->ProcedureID;
                $dataBook['procedure']['name'] = $findClinicProcedure->Name;
                $dataBook['procedure']['duration'] = $findClinicProcedure->Duration;

                $price = preg_replace('/[^A-Za-z0-9\-]/', '', $findClinicProcedure->Price);
                
                $dataBook['procedure']['price'] = $price;
                
                $dataBook['doctor']['doctor_id'] = $findClinicDoctor->DoctorID;
                $dataBook['doctor']['name'] = $findClinicDoctor->DocName;
                $dataBook['doctor']['qualifications'] = $findClinicDoctor->Qualifications;
                $dataBook['doctor']['specialty'] = $findClinicDoctor->Specialty;
                $dataBook['doctor']['image_url'] = $findClinicDoctor->DocImage;
                
                $dataBook['clinic']['clinic_id'] = $findClinicDoctor->ClinicID;
                $dataBook['clinic']['favourite'] = $favourite;
                //$dataBook['clinic']['annotation_url'] = $findInsurance['annotation_url'];       
                $dataBook['clinic']['image_url'] = $findClinicDoctor->CliImage;
                $dataBook['clinic']['name'] = $findClinicDoctor->CliName;
                $dataBook['clinic']['address'] = $findClinicDoctor->Address;
                $dataBook['clinic']['lattitude'] = $findClinicDoctor->Lat;
                $dataBook['clinic']['longitude'] = $findClinicDoctor->Lng;
                $dataBook['clinic']['telephone'] = $findClinicDoctor->CliPhone;
                $dataBook['clinic']['open'] = $clinicOpenTime;
                $dataBook['clinic']['doctor_count'] = DoctorLibrary::FindDoctorCount($findClinicDoctor->ClinicID);
                $transaction_data = $transaction->FindAppointmentTransaction($appointmentid);
                if($transaction_data) {
                    $transaction_status = TRUE;
                } else {
                    $transaction_status = FALSE;
                }
                $dataBook['transaction']['status'] = $transaction_status;
                if($transaction_data != FALSE) {
                    $balance_paid = (int)$transaction_data['procedure_cost'] - (int)$transaction_data['credit_cost'];
                    $dataBook['transaction']['final_amount'] = round($transaction_data['procedure_cost'], 2);
                    $dataBook['transaction']['wallet_deducted'] = round($transaction_data['credit_cost'], 2);
                    $dataBook['transaction']['balance_paid'] = round($balance_paid, 2);
                }
           
            $returnObject->status = TRUE;
            $returnObject->data = $dataBook;
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("NoRecords");
        }
        return $returnObject;
    }
    
    public static function ProcessSearch($UserID){
        $currentTime = date('d-m-Y');
        $search = Input::get('search');
        $returnObject = new stdClass();
        if(!empty($search)){
            $clinicData = ClinicLibrary::FindClinicSearch($search);
            if($clinicData){
                //$panelCount = null;
                foreach($clinicData as $cdata){ 
                    $userprofile = AuthLibrary::FindUserProfileByRefID($cdata->ClinicID);
                    if(!empty($userprofile->Email)){
                        $clinicemail = $userprofile->Email;
                    }else{
                        $clinicemail = null;
                    }
                    $clinicProcedures = self::FindClinicProcedures($cdata->ClinicID);
                    $findClinicTimes = General_Library::FindAllClinicTimes(3,$cdata->ClinicID,  strtotime($currentTime));
                    $findClinicHolidays = General_Library::FindUpcomingHolidays(3,$cdata->ClinicID,$currentTime);
                    $cdata->ClinicOPenTime = $findClinicTimes;
                    $cdata->ClinicHolidays = $findClinicHolidays;
                    $cdata->Email = $clinicemail;
                    $jsonArray = ArrayHelperMobile::ClinicProfile($cdata,$currentTime,$UserID);
                    
                    $jsonArray['doctor_count']= DoctorLibrary::FindDoctorCount($cdata->ClinicID);
                    $jsonArray['clinic_procedures'] = ArrayHelperMobile::ClinicProcedures($clinicProcedures);
                    
                    $returnArray[] = $jsonArray;
                }
                $returnObject->status = TRUE;
                $returnObject->data['total_count'] = count($clinicData);
                $returnObject->data['panel_count'] = 0;
                $returnObject->data['panel_insurance'] =null;
                $returnObject->data['clinics'] = $returnArray;
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
    
    
     public static function ProcessNewNearby($findUserID){
        $currentTime = date('d-m-Y');
        $getLat = Input::get('lat');
        $getLng = Input::get('lng');
        $getType = Input::get('type');
        $page = Input::get('page');
        // $radius = !empty(Input::get('radius')) ? Input::get('radius') : 5;
        $radius = 15;
        $returnObject = new stdClass();
        $clinic_type_data = new ClinicTypes();
        $clinictype = (int)$getType; 
        $clinictypename = $clinic_type_data->getClinicType($getType);

        if(!empty($getLat) && !empty($getLng)&& !empty($getType)){
            $findNearbyData = ClinicLibrary::FindNewNearby($getLat,$getLng,$getType,$page, $radius);
            // return $findNearbyData;
            $last_page = (int)$findNearbyData->getLastPage();
            // return $last_page;
            if(sizeof($findNearbyData) > 0) {
                if($findNearbyData){
                    $count = 1;
                    foreach($findNearbyData as $cdata){
                        // if($cdata) {
                            $userprofile = AuthLibrary::FindUserProfileByRefID($cdata->ClinicID);
                            if(!empty($userprofile->Email)){
                                $clinicemail = $userprofile->Email;
                            }else{
                                $clinicemail = null;
                            }
                            // if($count == 1) {
                            //     $clinictypename = $clinictypename->Name;
                            //     $count++;
                            // }
                            // $clinicProcedures = self::FindClinicProcedures($cdata->ClinicID);
                            $findClinicTimes = General_Library::FindAllClinicTimes(3,$cdata->ClinicID,  strtotime($currentTime));
                            $findClinicHolidays = General_Library::FindUpcomingHolidays(3,$cdata->ClinicID,$currentTime);
                            $cdata->ClinicOPenTime = $findClinicTimes;
                            $cdata->ClinicHolidays = $findClinicHolidays;
                            $cdata->Email = $clinicemail;
                            $jsonArray = ArrayHelperMobile::ClinicProfile_new($cdata,$currentTime,$findUserID);
                            
                            // $jsonArray['doctor_count']= DoctorLibrary::FindDoctorCount($cdata->ClinicID);
                            // $jsonArray['clinic_procedures'] = ArrayHelperMobile::ClinicProcedures($clinicProcedures);
                            
                            // $jsonArray['doctor_count']= DoctorLibrary::FindDoctorCount($cdata->ClinicID);
                            
                            $findNonePanelInsurance = ClinicLibrary::FindClinicTypeAnnotation($getType);
                            if($findNonePanelInsurance){ 
                                if($jsonArray['open_status'] ==1){
                                    $jsonArray['annotation_url']= $findNonePanelInsurance->Annotation;
                                }else{
                                    $jsonArray['annotation_url']= $findNonePanelInsurance->Annotation_Default;
                                }
                            }else{
                                $jsonArray['annotation_url']= null;
                            }                  
                            $jsonArray['panel_insurance'] =null;
                            
                            
                            $returnArray[] = $jsonArray;
                            $returnObject->data['clinics'] = $returnArray;
                        // } else {
                        //     $returnObject->status = FALSE;
                        //     $returnObject->message = StringHelper::errorMessage("NoRecords");
                        // }

                    }
                    $returnObject->status = TRUE;
                    $returnObject->data['user_primary_insurance'] = null;
                    $returnObject->data['total_count'] = count($findNearbyData);
                    $returnObject->data['current_page'] = (int)$page;
                    $returnObject->data['total_page'] = $last_page;
                    $returnObject->data['last_page'] = $last_page;
                    $returnObject->data['panel_count'] = 0;
                    $returnObject->data['clinic_type'] = $clinictype;
                    $returnObject->data['clinic_type_name'] = $clinictypename->Name;
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("NoRecords");  
                }
            } else {
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("NoRecords");  
            }
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmptyValues");   
        }  
        return $returnObject;
    }

    public static function ProcessNearby($findUserID){
        $currentTime = date('d-m-Y');
        $getLat = Input::get('lat');
        $getLng = Input::get('lng');
        $getType = Input::get('type');
        // $radius = !empty(Input::get('radius')) ? Input::get('radius') : 3;
        $radius = 15;
        $returnObject = new stdClass();
        $clinic_type_data = new ClinicTypes();
        $clinictypename = $clinic_type_data->getClinicType($getType);
        $clinictype = (int)$getType; 
        
        if(!empty($getLat) && !empty($getLng)&& !empty($getType)){
            $findNearbyData = ClinicLibrary::FindNearby($getLat,$getLng,$getType, $radius);
            // return $findNearbyData;
            // $last_page = (int)$findNearbyData->getLastPage();
            if(sizeof($findNearbyData) > 0) {
                if($findNearbyData){
                    $count=1;
                    foreach($findNearbyData as $cdata){
                        if($cdata) {
                            $userprofile = AuthLibrary::FindUserProfileByRefID($cdata->ClinicID);
                            if(!empty($userprofile->Email)){
                                $clinicemail = $userprofile->Email;
                            }else{
                                $clinicemail = null;
                            }
                            if($count ==1) {
                                $clinictypename = $clinictypename->Name;
                                $count++;
                            }
                            $clinicProcedures = self::FindClinicProcedures($cdata->ClinicID);
                            $findClinicTimes = General_Library::FindAllClinicTimes(3,$cdata->ClinicID,  strtotime($currentTime));
                            $findClinicHolidays = General_Library::FindUpcomingHolidays(3,$cdata->ClinicID,$currentTime);
                            $cdata->ClinicOPenTime = $findClinicTimes;
                            $cdata->ClinicHolidays = $findClinicHolidays;
                            $cdata->Email = $clinicemail;
                            $jsonArray = ArrayHelperMobile::ClinicProfile_new($cdata,$currentTime,$findUserID);
                            
                            $jsonArray['doctor_count']= DoctorLibrary::FindDoctorCount($cdata->ClinicID);
                            $jsonArray['clinic_procedures'] = ArrayHelperMobile::ClinicProcedures($clinicProcedures);
                            
                            $jsonArray['doctor_count']= DoctorLibrary::FindDoctorCount($cdata->ClinicID);
                            
                            $findNonePanelInsurance = ClinicLibrary::FindClinicTypeAnnotation($getType);
                            if($findNonePanelInsurance){ 
                                if($jsonArray['open_status'] ==1){
                                    $jsonArray['annotation_url']= $findNonePanelInsurance->Annotation;
                                }else{
                                    $jsonArray['annotation_url']= $findNonePanelInsurance->Annotation_Default;
                                }
                            }else{
                                $jsonArray['annotation_url']= null;
                            }                  
                            $jsonArray['panel_insurance'] =null;
                            
                            
                            $returnArray[] = $jsonArray;
                            $returnObject->status = TRUE;
                            
                            
                            $returnObject->data['user_primary_insurance'] = null;
                            $returnObject->data['total_count'] = count($findNearbyData);
                            // $returnObject->data['total_page'] = $last_page;
                            // $returnObject->data['last_page'] = $last_page;
                            $returnObject->data['panel_count'] = 0;
                            $returnObject->data['clinic_type'] = $clinictype;
                            $returnObject->data['clinic_type_name'] = $clinictypename;
                            $returnObject->data['clinics'] = $returnArray;
                        } else {
                            $returnObject->status = FALSE;
                            $returnObject->message = StringHelper::errorMessage("NoRecords");
                        }
                    }
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("NoRecords");  
                }
            } else {
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("NoRecords");  
            }
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmptyValues");   
        }  
        return $returnObject;
    }
    //End of class

    /////////////////////////////////////// nhr     ////////////////////////////////

    public static function getClnicType()
    {  
        $returnObject = new stdClass(); 

        $ct = new Admin_Clinic_Type();
        $data = $ct->GetAllClinicTypes();
        $dataArr['clinic_types'] = $data;
        $returnObject->status = TRUE;
        $returnObject->data = $dataArr;

        return $returnObject;
    }

    public static function NewClnicType()
    {  
        $returnObject = new stdClass(); 

        $ct = new Admin_Clinic_Type();
        $data = $ct->NewAllClinicTypes();
        $dataArr['clinic_types'] = $data;
        $returnObject->status = TRUE;
        $returnObject->data = $dataArr;

        return $returnObject;
    }

    public static function getClnicTypeSub()
    {  
        $returnObject = new stdClass(); 

        $ct = new Admin_Clinic_Type();
        $data = $ct->getClinicWithSub();
        $dataArr['clinic_types'] = $data;
        $returnObject->status = TRUE;
        $returnObject->data = $dataArr;

        return $returnObject;
    }




    public static function getClinicByType($typeid,$findUserID)
    {
        $returnObject = new stdClass();
        $clnicdata = new Clinic();
        $data = $clnicdata->findTypeClinic($typeid);
        $arraymain = array();
            
        if ($data) {

            foreach ($data as $value) {
                $arr = array();

                $arr['ClinicID']=$value->ClinicID;
                
                $fav = new ClinicUserFavourite();
                $exist = $fav->getStatus($value->ClinicID,$findUserID);
                if ($exist) {
                    $favourite = $exist->favourite;
                } else {
                    $favourite = 0;
                }
                

                $arr['Name']=$value->Name;
                $arr['image']=$value->image;
                $arr['Address']=$value->Address;
                $arr['City']=$value->City;
                $arr['State']=$value->State;
                $arr['Country']=$value->Country;
                $arr['Postal']=$value->Postal;
                $arr['Lat']=$value->Lat;
                $arr['Lng']=$value->Lng;
                $arr['Favourite']=$favourite;
                $arr['Open']=self::openStatus($value->ClinicID);
                array_push($arraymain, $arr);   
                
            }

            $returnObject->status = TRUE;
            $returnObject->data = $arraymain;
        }else {
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("NoRecords"); 
        }

        return $returnObject;
      
        
    }

    public static function openStatus($clinicid)
    {
        // $currentdate = time();
        StringHelper::Set_Default_Timezone();
        $week = date('D');
        $cTime = strtotime(date("h:i A"));
        $cDay  =  strtotime(date('d-m-Y'));

        $time = new ClinicTimes();
        $data = $time->findCurentClinicStatus($week,$clinicid);
        
        $returnval = 1;

        //check clinic time
        if($data){
            $cSTime = strtotime($data[0]->StartTime);
            $cETime = strtotime($data[0]->EndTime);
        
            if ($data[0]->Active == 1 && ($cTime<$cETime && $cTime>$cSTime)) {
                $returnval = 1;

            } else {
                $returnval = 0;
            }
            
        }else{
            $returnval = 0;
        }

        //check breaks
        $break = new ExtraEvents();
        $data1 = $break->findClinicBreaks($week, $clinicid);
        $i=0;
        if ($data1) {
            foreach ($data1 as $val) {
                $bSTime = strtotime($val->start_time);
                $bETime = strtotime($val->end_time);
                
                if ($cTime>=$bSTime && $cTime<=$bETime) {
                    $i++;
                }
                
            }
           
        }
        

        if ($i>0) {
            $returnval = 0;
        } 
        
        //check time off

        $timeoff = new ManageHolidays();
        $data2 = $timeoff->findClinicTimeoff($clinicid); 
        $j=0;
        if ($data2) {
           foreach ($data2 as $value) {
               $hType   = $value->Type;
               $hFrom   = strtotime($value->From_Holiday);
               $hTo     = strtotime($value->To_Holiday);
               $hStart  = strtotime($value->From_Time);
               $hEnd    = strtotime($value->To_Time);

               if ($hType==0) {//fullday
                   if($cDay==$hFrom){
                        $j++;
                   }

               } else {
                   if(($cDay>=$hFrom && $cDay<=$hTo) && ($cTime>=$hStart && $cTime<=$hEnd) ){
                        $j++;
                   }

               }
               
           }

        }

        if ($j>0) {
            $returnval = 0;
        } 
        
        

     return $returnval;

    }

public static function favourite($findUserID)
{
    $returnObject = new stdClass();
    $clinicid = Input::get('clinicid');
    $status = Input::get('status');

    $fav = new ClinicUserFavourite();
    $exist = $fav->getStatus($clinicid,$findUserID);  
// dd($exist[0]->favourite);
    if ($exist) {
        $fav1 = new ClinicUserFavourite();
        $array = array('user_id'=>$findUserID, 'clinic_id'=>$clinicid, 'favourite'=>$status);
        $fav1->updateFavourite($array);
        $returnObject->status = true;
        
    } else {
        $fav2 = new ClinicUserFavourite();
        $fav2->insertFavourite($findUserID,$clinicid,$status); 
        $returnObject->status = true; 
        
    }
    

    return $returnObject;
}



public static function mainSearch()
{   
    $returnObject = new stdClass();
    $search = Input::get('search');

    $specialty = self::getSpeciality($search);
    $procedure = self::getProcedure($search);
    $district = self::getDistrict($search);
    $mrt = self::getMrt($search);
    $clinics =  self::getClinics($search);
    $doctors =  self::getDoctors($search);


    // return $procedure;
    if (empty($specialty->data) && empty($procedure->data) && empty($district->data) & empty($mrt->data) && ($clinics==NULL) && ($doctors==NULL)) {
        $returnObject->status = 0;
        $returnObject->data = array();
    } else {
        $returnObject->status = true;
        $returnObject->data['specialty'] = $specialty;
        $returnObject->data['procedure'] = $procedure;
        $returnObject->data['district'] = $district;
        $returnObject->data['mrt'] = $mrt;
        
        if($clinics==null){
            $returnObject->data['clinics'] = array();
        }else{
            $clinics = ClinicHelper::removeBlockClinics($clinics);
            $returnObject->data['clinics'] = $clinics;
        }

        if($doctors==null){
            $returnObject->data['doctors'] = array();
        }else{
            $doctors = ClinicHelper::removeBlockClinics($doctors);
            $returnObject->data['doctors'] = $doctors;
        }
        
    }

    return $returnObject;
}


public static function subSearch()
{   
    $returnObject = new stdClass();
    $type = Input::get('type');
    $key = Input::get('key');


    $clinics = self::getSubClinics($type,$key);
    $doctors = self::getSubDoctors($type,$key);

    if (($clinics==NULL) && ($doctors==NULL)) {
        $returnObject->status = 0;
        $returnObject->data = array();
    } else {
        $returnObject->status = true;
        if ($clinics==null) {
           $returnObject->data['clinics'] = array();
        } else {
           $returnObject->data['clinics'] = $clinics;
        }

        if ($doctors==null) {
           $returnObject->data['doctors'] = array();
        } else {
           $returnObject->data['doctors'] = $doctors;
        }
      
    }
    
   
    return $returnObject;
}



public static function getSpeciality($search)
{   
    $returnObject = new stdClass();
    $sp = new Admin_Clinic_Type();
    $data = $sp->getSpeciality($search);
    $returnObject->type = 1;
    $returnObject->data = $data;

    return $returnObject;
}

public static function getProcedure($search)
{   
    $returnObject = new stdClass();
    $sp = new ClinicProcedures();
    $data = $sp->getProcedure($search);
    $returnObject->type = 2;
    $returnObject->data = $data;
   
    return $returnObject;
}

public static function getDistrict($search)
{   
    $returnObject = new stdClass();
    $sp = new Clinic();
    $data = $sp->getDistrict($search);

    $arr = array();
    foreach ($data as $key ) {
       array_push($arr, $key->district);
    }
    $returnObject->type = 3;
    $returnObject->data = $arr;

    return $returnObject;
}


public static function getMrt($search)
{   
    $returnObject = new stdClass();
    $sp = new Clinic();
    $data = $sp->getMrt($search);

    $arr = array();
    foreach ($data as $key ) {
       array_push($arr, $key->mrt);
    }

    $returnObject->type = 4;
    $returnObject->data = $arr;
   
    return $returnObject;
}

public static function getClinics($search)
{   
    $sp = new Clinic();
    $clinic = $sp->getClinics($search);

    if ($clinic) {
        foreach ($clinic as $value) {
            $data['clinic_id'] = $value->ClinicID;
            $data['name'] = $value->Name;
            $data['address'] = $value->Address. ' '.$value->Postal;
            $data['district'] = $value->District;
            $data['country'] = $value->Country;
            $data['telephone'] = $value->Phone;
            $data['clinic_image'] = $value->image;
            $data['open_status'] = self::openStatus($value->ClinicID);;

            $dataarr[] = $data;
        }
        
   return $dataarr;  
    } else {
       return null;
    }
}




public static function getDoctors($search)
{
    $doc = new Doctor();
    $doctors = $doc->getDoctors($search);

    // dd()

    if ($doctors) {
        foreach ($doctors as $value) {
            $sp = new Clinic();
            $clinic = $sp->ClinicDetails($value->ClinicID);
            
            if ($clinic) {
                $address = $clinic->Address;
            } else {
                $address = null;
            }
            

            $data['clinic_id'] = $value->ClinicID;
            $data['name'] = $value->Name;
            $data['address'] = $address;
            $data['qualifications'] = $value->Qualifications;
            $data['specialty'] = $value->Specialty;
            $data['phone'] = $value->Phone;
            $data['clinic_image'] = $value->image;
            $data['open_status'] = self::openStatus($value->ClinicID);;

            $dataarr[] = $data;
        }
    
    return $dataarr;  
    } else {
       return null;
    }
    
   

}


public static function getSubClinics($type,$key)
{   

    if ($type==1) { //serach by speciality 
        $cl = new Clinic();
        $clinic = $cl->getClinicsByType($key);

    } else if($type==2){ //serach by procedure
        $cl = new Clinic();
        $clinic = $cl->getClinicsByProcedure($key);

    } else if($type==3){ //search by district
        $cl = new Clinic();
        $clinic = $cl->getClinicsbyDistrict($key);

    } else if($type==4){ //search by mrt
        $cl = new Clinic();
        $clinic = $cl->getClinicsByMrt($key);

    }
    
    

    if ($clinic) {
        foreach ($clinic as $value) {
            $data['clinic_id'] = $value->ClinicID;
            $data['name'] = $value->Name;
            $data['address'] = $value->Address. ' '.$value->Postal;
            $data['district'] = $value->District;
            $data['country'] = $value->Country;
            $data['phone'] = $value->Phone;
            $data['open_status'] = self::openStatus($value->ClinicID);;

            $dataarr[] = $data;
        }
     return $dataarr;   
    } else {
      return null;
    }
    
    
}



public static function getSubDoctors($type,$key)
{
    if ($type==1) { //serach by speciality 
        $doc = new Doctor();
        $doctors = $doc->getDoctorByType($key);

    } else if($type==2){ //serach by procedure
        $doc = new Doctor();
        $doctors = $doc->getDoctorByProcedure($key);
        
    } else if($type==3){ //search by district
        $doc = new Doctor();
        $doctors = $doc->getDoctorByDistrict($key);

    } else if($type==4){ //search by mrt
        $doc = new Doctor();
        $doctors = $doc->getDoctorByMrt($key);

    }
// dd($doctors);
    if ($doctors) {
        foreach ($doctors as $value) {
            $data['clinic_id'] = $value->ClinicID;
            $data['name'] = $value->Name;
            $data['qualifications'] = $value->Qualifications;
            $data['specialty'] = $value->Specialty;
            $data['phone'] = $value->Phone;
            $data['open_status'] = self::openStatus($value->ClinicID);;

            $dataarr[] = $data;
        }
       return $dataarr; 
    } else {
      return null;
    }
    
    

}


public static function getFavouriteClinics($findUserID)
{
    $cl = new Clinic();
    $clinic = $cl->getFavouriteClinics($findUserID);
// dd($clinic);
    if ($clinic) {
        foreach ($clinic as $value) {
            $data['clinic_id'] = $value->ClinicID;
            $data['name'] = $value->Name;
            $data['address'] = $value->Address. ' '.$value->Postal;
            $data['district'] = $value->District;
            $data['country'] = $value->Country;
            $data['telephone'] = $value->Phone;
            $data['image_url'] = $value->image;
            $data['favourite'] = 1;
            $data['open_status'] = self::openStatus($value->ClinicID);;

            $dataarr[] = $data;
        }
     return $dataarr;   
    } else {
      return null;
    }
}



}
