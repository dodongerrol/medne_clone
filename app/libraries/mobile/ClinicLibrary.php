<?php

class ClinicLibrary{
    
    /* Use         :   Used to find appointment for users
     * Access       :   No public access is allowed
     */
    public static function FindAllUserAppointment($userid){
        $userappointment = new UserAppoinment();
        $findAllAppointment = $userappointment->GetAppointmentByUser($userid);
        return $findAllAppointment;
    }
    
    public static function FindClinicDetails($clinicid){
        $clinic = new Clinic();
        $findClinic = $clinic->ClinicDetails($clinicid);
        return $findClinic;
    }
    
    public static function AppointmentHistory1($findUserID){
        $doctorslot = new DoctorSlots();
        $slotdetails = new DoctorSlotDetails();
        
        $returnObject = new stdClass();
        $allAppointments = self::FindAllUserAppointment($findUserID);
                    
        if(!empty($allAppointments) && count($allAppointments)>0){
            $upcoming = null;
            $history = null;
            $round =1; $nowSaving=0; $nowbooktype = 0;
            foreach($allAppointments as $appointment){            
                $doctorSlotClinic = $doctorslot->FindSlotDoctorClinic($appointment->DoctorSlotID);
                if($appointment->BookType == 1){
                    $findSlotDetail = $slotdetails->ActiveSlotDetails($appointment->SlotDetailID);
                    $appointtime = $findSlotDetail->Time;
                }else{
                    if($round==1){
                        $findNowSaving = self::FindNowSaving($appointment->DoctorSlotID,$appointment->BookDate);
                        if($findNowSaving){
                            $nowSaving = $findNowSaving->BookNumber;
                            $round ++;
                        }
                        /*if($appointment->Status==1){
                            $nowSaving = $appointment->BookNumber;
                            $round ++;
                        }*/
                    }
                    $appointtime = null;
                }
                $findInsurance = self::ProcessPanelNonePanelInsurance($findUserID,$doctorSlotClinic->ClinicID);
                $findPatientCount = self::FindPatientCount($appointment->DoctorSlotID,$appointment->BookDate);
                $clinicOpenTime = StringHelper::GetClinicOpenTimes($doctorSlotClinic->ClinicID);
                
                if($appointment->BookType==0){$nowbooktype = 1;}if($appointment->BookType==1){$nowbooktype = 2;}
                    $dataBook['booking']['booking_id'] = $appointment->UserAppoinmentID;
                    $dataBook['booking']['type'] = $nowbooktype;
                    $dataBook['booking']['date'] = $appointment->BookDate;
                    $dataBook['booking']['time'] = $appointtime;
                    $dataBook['booking']['no_of_patients'] = $findPatientCount;
                    $dataBook['booking']['now_serving'] = $nowSaving;
                    $dataBook['booking']['fee'] = $doctorSlotClinic->ConsultationCharge;
                    $dataBook['booking']['queue_no'] = $appointment->BookNumber;
                    
                    $dataBook['doctor']['doctor_id'] = $doctorSlotClinic->DoctorID;
                    $dataBook['doctor']['name'] = $doctorSlotClinic->Name;
                    $dataBook['doctor']['qualifications'] = $doctorSlotClinic->Qualifications;
                    $dataBook['doctor']['specialty'] = $doctorSlotClinic->Specialty;
                    //$dataBook['doctor']['image_url'] = URL::to('/assets/'.$doctorSlotClinic->image);
                    $dataBook['doctor']['image_url'] = $doctorSlotClinic->image;
                    
                    $dataBook['clinic']['clinic_id'] = $doctorSlotClinic->ClinicID;
                    $dataBook['clinic']['annotation_url'] = $findInsurance['annotation_url'];       
                    //$dataBook['clinic']['image_url'] = URL::to('/assets/'.$doctorSlotClinic->image);
                    $dataBook['clinic']['image_url'] = $doctorSlotClinic->image;
                    //$dataBook['clinic']['name'] = $doctorSlotClinic->Name;
                    $dataBook['clinic']['name'] = $doctorSlotClinic->CName;
                    $dataBook['clinic']['address'] = $doctorSlotClinic->Address;
                    $dataBook['clinic']['lattitude'] = $doctorSlotClinic->Lat;
                    $dataBook['clinic']['longitude'] = $doctorSlotClinic->Lng;
                    $dataBook['clinic']['telephone'] = $doctorSlotClinic->Phone;
                    $dataBook['clinic']['open'] = $clinicOpenTime;
                    $dataBook['clinic']['doctor_count'] = DoctorLibrary::FindDoctorCount($doctorSlotClinic->ClinicID);
                    $dataBook['clinic']['panel_insurance']['insurance_id'] = $findInsurance['insurance_id'];
                    $dataBook['clinic']['panel_insurance']['name'] = $findInsurance['name'];
                    $dataBook['clinic']['panel_insurance']['image_url'] = URL::to('/assets/'.$findInsurance['image_url']);       
                    
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
    
    /* Use          :   Used to process panal and none panal clinic insurance 
     * Access       :   Private
     * Parameter    :   User id and clinic id
     */
    private static function ProcessPanelNonePanelInsurance($userid,$clinicid){
        $clinicinsurance = new ClinicInsurenceCompany();
        $userpolicy = new UserInsurancePolicy();
        $findClinicInsurance = $clinicinsurance->FindClinicInsuranceCompnay($clinicid);
        $findUserPolicy = $userpolicy->getUserInsurancePolicy($userid);
        
        if($findClinicInsurance && $findUserPolicy){
            if($findUserPolicy->CompanyID == $findClinicInsurance->CompanyID){
                $returnData['insurance_id'] = $findClinicInsurance->CompanyID;
                $returnData['name'] = $findClinicInsurance->Name;
                $returnData['image_url'] = $findClinicInsurance->Image;
                $returnData['annotation_url'] = $findClinicInsurance->Annotation;         
            }else{
                $returnData = self::NonePanelInsuranceArray();
            }       
        } else{
            $returnData = self::NonePanelInsuranceArray();     
        }
        return $returnData;
    }
    /* Use          :   Used to return none panel clinic array
     * Access       :   Private
     * 
     */
    private static function NonePanelInsuranceArray(){
        $findNonePanelInsurance = self::FindNonePanelInsurance();
        if($findNonePanelInsurance){
            $returnData['insurance_id'] = $findNonePanelInsurance->CompanyID;
            $returnData['name'] = $findNonePanelInsurance->Name;
            $returnData['image_url'] = $findNonePanelInsurance->Image;
            $returnData['annotation_url'] = $findNonePanelInsurance->Annotation;
        }else{
            $returnData['insurance_id'] = null;
            $returnData['name'] = null;
            $returnData['image_url'] = null;
            $returnData['annotation_url'] = null;
        }
        return $returnData;
    }


    /* Use          :   Used to find none panel clinic insurance
     * Access       :   No public direct access is allowed
     * Parameter    :   null
     */
    public static function FindNonePanelInsurance(){
        $insurancecompany = new InsuranceCompany();
        //$findNonePanelClinic = $insurancecompany->InsuranceCompanyByID(4);
        $findNonePanelClinic = $insurancecompany->InsuranceCompanyByID(100);
        if($findNonePanelClinic){
            return $findNonePanelClinic;
        }else{
            return FALSE;
        }
    }
    
    /* Use      :   Used to find default annotation
     * 
     */
    public static function FindClinicTypeAnnotation($clinicType){
        if($clinicType==1){ $id = 111;}
        elseif($clinicType==2){ $id = 112;}
        elseif($clinicType==3){ $id = 113;}
        $insurancecompany = new InsuranceCompany();
        //$findNonePanelClinic = $insurancecompany->InsuranceCompanyByID(4);
        $findNonePanelClinic = $insurancecompany->InsuranceCompanyByID(111);
        if($findNonePanelClinic){
            return $findNonePanelClinic;
        }else{
            return FALSE;
        }
    }
    
    /* Use          :   Used to find patient count for clinic
     * Access       :   Public Static
     * 
     */
    public static function FindPatientCount($doctorslotid,$date){
        $userappointment = new UserAppoinment();
        $formatDate = $formatDate = date("d-m-Y", strtotime($date));
        $findPatientCount = $userappointment->findNumberOfBooking($doctorslotid,$formatDate);
        if($findPatientCount > 0){
            return $findPatientCount;
        }else{
            return 0;
        }
    }
    
    /* Use          :   Used to find nearby data
     * Access       :   Public
     * 
     */
    public static function ProcessNearby1($findUserID){
        $getLat = Input::get('lat');
        $getLng = Input::get('lng');
        $getType = Input::get('type');
        $returnObject = new stdClass();
        if(!empty($getLat) && !empty($getLng)&& !empty($getType)){
            $findNearbyData = self::FindNearby($getLat,$getLng,$getType);
            if($findNearbyData){
                $panelCount = null; $clinictype =0; $clinictypename = null; $count=1;
                foreach($findNearbyData as $cdata){
                    $userprofile = AuthLibrary::FindUserProfileByRefID($cdata->ClinicID);
                    if(!empty($userprofile->Email)){
                        $clinicemail = $userprofile->Email;
                    }else{
                        $clinicemail = null;
                    }
                    if($count ==1) {
                        $clinictype = $cdata->ClinicTypeID; $clinictypename = $cdata->ClinicType;
                        $count++;
                    }
                    $clinicOpenStatus = StringHelper::GetClinicOpenStatus($cdata->ClinicID);
                    $clinicOpenTime = StringHelper::GetClinicOpenTimes($cdata->ClinicID);
                    
                    $jsonArray['clinic_id']= $cdata->ClinicID;
                    $jsonArray['name']= $cdata->Name;
                    $jsonArray['email']= $clinicemail;
                    $jsonArray['address']= $cdata->Address;
                    //$jsonArray['image_url']= URL::to('/assets/'.$cdata->image);
                    $jsonArray['image_url']= $cdata->image;
                    $jsonArray['lattitude']= $cdata->Lat;
                    $jsonArray['longitude']= $cdata->Lng;
                    $jsonArray['telephone']= $cdata->Phone;   
                    $jsonArray['clinic_price']= $cdata->Clinic_Price;
                    //$jsonArray['open']= $cdata->Opening;
                    $jsonArray['open']= $clinicOpenTime;
                    $jsonArray['open_status']= $clinicOpenStatus;
                    $jsonArray['doctor_count']= DoctorLibrary::FindDoctorCount($cdata->ClinicID);
                    
                    //$panelInsurance = self::ProcessPanelInsurance($findUserID,$cdata->ClinicID);
                    $panelInsurance = FALSE;
                    if($panelInsurance){
                        $panelCount[] = count($panelInsurance);
                        $jsonArray['panel_insurance']['insurance_id']= $panelInsurance['insurance_id'];
                        $jsonArray['panel_insurance']['name']= $panelInsurance['name'];
                        $jsonArray['panel_insurance']['image_url']= $panelInsurance['image_url'];
                        $jsonArray['annotation_url']= $panelInsurance['annotation_url'];  
                        $panelimage = $panelInsurance['image_url'];
                        $panelinsurancename = $panelInsurance['name'];
                        $panelinsuranceimage = $panelInsurance['annotation_url'];
                        $panelinsuranceid = $panelInsurance['insurance_id'];
                    }else{
                            //$findNonePanelInsurance = self::FindNonePanelInsurance();
                        $findNonePanelInsurance = self::FindClinicTypeAnnotation($getType);
                        if($findNonePanelInsurance){ 
                            if($clinicOpenStatus==1){
                                $jsonArray['annotation_url']= $findNonePanelInsurance->Annotation;
                            }else{
                                $jsonArray['annotation_url']= $findNonePanelInsurance->Annotation_Default;
                            }
                        }else{
                            $jsonArray['annotation_url']= null;
                        }                  
                        $jsonArray['panel_insurance'] =null;
                    } 
                    $returnArray[] = $jsonArray;
                }
                $returnObject->status = TRUE;
                if($panelCount !=null){
                    $returnObject->data['panel_insurance']['insuranceid'] = $panelinsuranceid;
                    $returnObject->data['panel_insurance']['name'] = $panelinsurancename;
                    //$returnObject->data['panel_insurance']['image'] = $panelinsuranceimage;
                    $returnObject->data['panel_insurance']['image'] = $panelimage;
                }
                $findPrimaryInsurance = InsuranceLibrary::FindUserInsurancePolicy($findUserID);
                if($findPrimaryInsurance){
                    $returnObject->data['user_primary_insurance']['policy_name'] = $findPrimaryInsurance->PolicyName;
                    $returnObject->data['user_primary_insurance']['policy_no'] = $findPrimaryInsurance->PolicyNo;
                    $returnObject->data['user_primary_insurance']['image'] = $findPrimaryInsurance->Image;
                    $returnObject->data['user_primary_insurance']['company_name'] = $findPrimaryInsurance->Name;
                }else{
                    $returnObject->data['user_primary_insurance'] = null;
                }
                $returnObject->data['total_count'] = count($findNearbyData);
                $returnObject->data['panel_count'] = count($panelCount);
                $returnObject->data['clinic_type'] = $clinictype;
                $returnObject->data['clinic_type_name'] = $clinictypename;
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
    
    /*
    public static function ProcessNearby_Final($findUserID){
        $getLat = Input::get('lat');
        $getLng = Input::get('lng');
        $returnObject = new stdClass();
        if(!empty($getLat) && !empty($getLng)){
            $findNearbyData = self::FindNearby($getLat,$getLng);
            if($findNearbyData){
                $panelCount = null;
                foreach($findNearbyData as $cdata){
                    $jsonArray['clinic_id']= $cdata->ClinicID;
                    $jsonArray['name']= $cdata->Name;
                    $jsonArray['address']= $cdata->Address;
                    //$jsonArray['image_url']= URL::to('/assets/'.$cdata->image);
                    $jsonArray['image_url']= $cdata->image;
                    $jsonArray['lattitude']= $cdata->Lat;
                    $jsonArray['longitude']= $cdata->Lng;
                    $jsonArray['telephone']= $cdata->Phone;   
                    $jsonArray['open']= $cdata->Opening;
                    $jsonArray['doctor_count']= DoctorLibrary::FindDoctorCount($cdata->ClinicID);
                    
                    //$panelInsurance = self::ProcessPanelInsurance($findUserID,$cdata->ClinicID);
                    $panelInsurance = FALSE;
                    if($panelInsurance){
                        $panelCount[] = count($panelInsurance);
                        $jsonArray['panel_insurance']['insurance_id']= $panelInsurance['insurance_id'];
                        $jsonArray['panel_insurance']['name']= $panelInsurance['name'];
                        $jsonArray['panel_insurance']['image_url']= $panelInsurance['image_url'];
                        $jsonArray['annotation_url']= $panelInsurance['annotation_url'];  
                        $panelimage = $panelInsurance['image_url'];
                        $panelinsurancename = $panelInsurance['name'];
                        $panelinsuranceimage = $panelInsurance['annotation_url'];
                        $panelinsuranceid = $panelInsurance['insurance_id'];
                    }else{
                        $findNonePanelInsurance = self::FindNonePanelInsurance();
                        if($findNonePanelInsurance){
                            $jsonArray['annotation_url']= $findNonePanelInsurance->Annotation;
                        }else{
                            $jsonArray['annotation_url']= null;
                        }                  
                        $jsonArray['panel_insurance'] =null;
                    } 
                    $returnArray[] = $jsonArray;
                }
                $returnObject->status = TRUE;
                if($panelCount !=null){
                    $returnObject->data['panel_insurance']['insuranceid'] = $panelinsuranceid;
                    $returnObject->data['panel_insurance']['name'] = $panelinsurancename;
                    //$returnObject->data['panel_insurance']['image'] = $panelinsuranceimage;
                    $returnObject->data['panel_insurance']['image'] = $panelimage;
                }
                $findPrimaryInsurance = InsuranceLibrary::FindUserInsurancePolicy($findUserID);
                if($findPrimaryInsurance){
                    $returnObject->data['user_primary_insurance']['policy_name'] = $findPrimaryInsurance->PolicyName;
                    $returnObject->data['user_primary_insurance']['policy_no'] = $findPrimaryInsurance->PolicyNo;
                    $returnObject->data['user_primary_insurance']['image'] = $findPrimaryInsurance->Image;
                    $returnObject->data['user_primary_insurance']['company_name'] = $findPrimaryInsurance->Name;
                }else{
                    $returnObject->data['user_primary_insurance'] = null;
                }
                $returnObject->data['total_count'] = count($findNearbyData);
                $returnObject->data['panel_count'] = count($panelCount);
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
     */
    
    /*
    public static function ProcessNearby_original($findUserID){
        $getLat = Input::get('lat');
        $getLng = Input::get('lng');
        $returnObject = new stdClass();
        if(!empty($getLat) && !empty($getLng)){
            $findNearbyData = self::FindNearby($getLat,$getLng);
            if($findNearbyData){
                $panelCount = null;
                foreach($findNearbyData as $cdata){
                    $jsonArray['clinic_id']= $cdata->ClinicID;
                    $jsonArray['name']= $cdata->Name;
                    $jsonArray['address']= $cdata->Address;
                    //$jsonArray['image_url']= URL::to('/assets/'.$cdata->image);
                    $jsonArray['image_url']= $cdata->image;
                    $jsonArray['lattitude']= $cdata->Lat;
                    $jsonArray['longitude']= $cdata->Lng;
                    $jsonArray['telephone']= $cdata->Phone;   
                    $jsonArray['open']= $cdata->Opening;
                    $jsonArray['doctor_count']= DoctorLibrary::FindDoctorCount($cdata->ClinicID);
                    
                    $panelInsurance = self::ProcessPanelInsurance($findUserID,$cdata->ClinicID);
                    if($panelInsurance){
                        $panelCount[] = count($panelInsurance);
                        $jsonArray['panel_insurance']['insurance_id']= $panelInsurance['insurance_id'];
                        $jsonArray['panel_insurance']['name']= $panelInsurance['name'];
                        $jsonArray['panel_insurance']['image_url']= $panelInsurance['image_url'];
                        $jsonArray['annotation_url']= $panelInsurance['annotation_url'];  
                        $panelimage = $panelInsurance['image_url'];
                        $panelinsurancename = $panelInsurance['name'];
                        $panelinsuranceimage = $panelInsurance['annotation_url'];
                        $panelinsuranceid = $panelInsurance['insurance_id'];
                    }else{
                        $findNonePanelInsurance = self::FindNonePanelInsurance();
                        if($findNonePanelInsurance){
                            $jsonArray['annotation_url']= $findNonePanelInsurance->Annotation;
                        }else{
                            $jsonArray['annotation_url']= null;
                        }                  
                        $jsonArray['panel_insurance'] =null;
                    } 
                    $returnArray[] = $jsonArray;
                }
                $returnObject->status = TRUE;
                if($panelCount !=null){
                    $returnObject->data['panel_insurance']['insuranceid'] = $panelinsuranceid;
                    $returnObject->data['panel_insurance']['name'] = $panelinsurancename;
                    //$returnObject->data['panel_insurance']['image'] = $panelinsuranceimage;
                    $returnObject->data['panel_insurance']['image'] = $panelimage;
                }
                $findPrimaryInsurance = InsuranceLibrary::FindUserInsurancePolicy($findUserID);
                if($findPrimaryInsurance){
                    $returnObject->data['user_primary_insurance']['policy_name'] = $findPrimaryInsurance->PolicyName;
                    $returnObject->data['user_primary_insurance']['policy_no'] = $findPrimaryInsurance->PolicyNo;
                    $returnObject->data['user_primary_insurance']['image'] = $findPrimaryInsurance->Image;
                    $returnObject->data['user_primary_insurance']['company_name'] = $findPrimaryInsurance->Name;
                }else{
                    $returnObject->data['user_primary_insurance'] = null;
                }
                $returnObject->data['total_count'] = count($findNearbyData);
                $returnObject->data['panel_count'] = count($panelCount);
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
    */
    
    /* Use          :   Used to find nearby clinic
     * Access       :   Public
     * 
     */
    public static function FindNearby($getLat,$getLng,$getType, $radius){
        $clinic = new Clinic();
        $radius = $radius;
        $nearbyData = $clinic->Nearby($getLat,$getLng,$radius,$getType);
        if($nearbyData){
            return $nearbyData;
        }else{
            return FALSE;
        }
    }

    public static function FindNewNearby($getLat,$getLng,$getType,$page,$radius){
        $clinic = new Clinic();
        $radius = $radius;
        $nearbyData = $clinic->newNearby($getLat,$getLng,$radius,$getType,(int)$page);
        if($nearbyData){
            return $nearbyData;
        }else{
            return FALSE;
        }
    }
    
    private static function ProcessPanelInsurance($userid,$clinicid){
        $clinicinsurance = new ClinicInsurenceCompany();
        $userpolicy = new UserInsurancePolicy();
        if(!empty($userid) && !empty($clinicid)){
            $findClinicInsurance = $clinicinsurance->FindClinicInsuranceCompnay($clinicid);
            //$findUserPolicy = $userpolicy->getUserInsurancePolicy($userid);
            $findUserPolicy = $userpolicy->FindUserInsurancePolicy($userid);
            if($findClinicInsurance && $findUserPolicy){
                if($findUserPolicy->CompanyID == $findClinicInsurance->CompanyID){
                    $returnData['insurance_id'] = $findClinicInsurance->CompanyID;
                    $returnData['name'] = $findClinicInsurance->Name;
                    $returnData['image_url'] = $findClinicInsurance->Image;
                    $returnData['annotation_url'] = $findClinicInsurance->Annotation;  
                    return $returnData;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    public static function ProcessClinicDetails(){
        $getID = Input::get('value');
        $returnObject = new stdClass();
        $currentdate = date("d-m-Y");
        if(empty($getID)){
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmptyValues"); 
        }else{
            $clinicData = self::FindClinic($getID);
            if($clinicData){
                $userprofile = AuthLibrary::FindUserProfileByRefID($clinicData->ClinicID);
                if(!empty($userprofile->Email)){
                    $clinicemail = $userprofile->Email;
                }else{
                    $clinicemail = null;
                }
                $clinicOpenStatus = StringHelper::GetClinicOpenStatus($clinicData->ClinicID);
                $clinicOpenTime = StringHelper::GetClinicOpenTimes($clinicData->ClinicID);
                if(empty($clinicData->Website)){ $website = Null; }else{ $website = $clinicData->Website;}
                if(empty($clinicData->Custom_title)){ $custitle = Null; }else{ $custitle = $clinicData->Custom_title;}
                if(empty($clinicData->Description)){ $descr = Null; }else{ $descr = $clinicData->Description;}
                if(empty($clinicData->Clinic_Price)){ $clprice = Null; }else{ $clprice = $clinicData->Clinic_Price;}
                
                $jsonArray['clinic_id']= $clinicData->ClinicID;
                $jsonArray['name']= $clinicData->Name;
                $jsonArray['email']= $clinicemail;
                $jsonArray['address']= $clinicData->Address;
                //$jsonArray['image_url']= URL::to('/assets/'.$clinicData->image);
                $jsonArray['image_url']= $clinicData->image;
                $jsonArray['lattitude']= $clinicData->Lat;
                $jsonArray['longitude']= $clinicData->Lng;
                $jsonArray['telephone']= $clinicData->Phone;   
                $jsonArray['description']= $descr;
                $jsonArray['website']= $website;
                $jsonArray['custom_title']= $custitle;
                $jsonArray['clinic_price']= $clprice;
                $jsonArray['open']= $clinicOpenTime;
                $jsonArray['open_status']= $clinicOpenStatus;
                $doctorsForClinic = DoctorLibrary::DoctorsInClinic($clinicData->ClinicID); 
                $panelInsurance = InsuranceLibrary::FindClinicInsurance($clinicData->ClinicID);
                $panelInsurance = FALSE;
                if($panelInsurance){
                    $jsonArray['panel_insurance']['insurance_id']= $panelInsurance->CompanyID;
                    $jsonArray['panel_insurance']['name']= $panelInsurance->Name;
                    //$jsonArray['panel_insurance']['image_url']= URL::to('/assets/'.$panelInsurance->Image);
                    $jsonArray['panel_insurance']['image_url']= $panelInsurance->Image;
                    $jsonArray['annotation_url']= $panelInsurance->Annotation; 
                }else{
                    $findNonePanelInsurance = self::FindNonePanelInsurance();
                    if($findNonePanelInsurance){
                        $jsonArray['annotation_url']= $findNonePanelInsurance->Annotation;
                    }else{
                        $jsonArray['annotation_url']= null;
                    }                  
                    $jsonArray['panel_insurance'] =null;                        
                }              
                if($doctorsForClinic){
                    $slotType = FALSE; $activeslot = 0;
                    $jsonArray['doctor_count']= count($doctorsForClinic);
                    foreach($doctorsForClinic as $doctorClinic){
                        $doctorArray['doctor_id']= $doctorClinic->DoctorID;
                        $doctorArray['name']= $doctorClinic->Name;
                        $doctorArray['qualifications']= $doctorClinic->Qualifications;
                        $doctorArray['specialty']= $doctorClinic->Specialty;
                        //$doctorArray['image_url']= URL::to('/assets/'.$doctorClinic->image);
                        $doctorArray['image_url']= $doctorClinic->image;
                        $doctorArray['can_book']= $doctorClinic->Active;
                        
                        $findStoppedQueue = DoctorLibrary::FindStoppedQueueByDate($doctorClinic->DoctorSlotID,$currentdate);
                        $queueBookingCount = DoctorLibrary::QueueuBookingCount(0,$doctorClinic->DoctorSlotID,$currentdate);
                        
                        $slotType = ArrayHelper::getSlotType($doctorClinic->TimeSlot);
                        //$slotAvailable = DoctorLibrary::FindSlotDetails($doctorClinic->DoctorSlotID,$slotType,$currentdate);
                        $slotAvailable = DoctorLibrary::FindActiveSlotForBooking($doctorClinic->DoctorSlotID,$slotType,$currentdate);
                        
                        if($doctorClinic->ClinicSession==1){   
                            if($findStoppedQueue || $queueBookingCount >= $doctorClinic->QueueNumber){
                                $booktype = 0;
                            }else{
                                $booktype = 1;
                            }
                        }elseif($doctorClinic->ClinicSession==2){
                            if($slotAvailable){
                                $booktype = 2; 
                            }else{
                                $booktype = 0; 
                            }
                        }
                        /*elseif($doctorClinic->ClinicSession==3){
                            if($slotAvailable && $findStoppedQueue || $queueBookingCount >= $doctorClinic->QueueNumber){
                                $booktype = 2;
                            }elseif($slotAvailable && !$findStoppedQueue && $queueBookingCount < $doctorClinic->QueueNumber){
                                $booktype = 3;
                            }elseif(!$slotAvailable && $findStoppedQueue || $queueBookingCount >= $doctorClinic->QueueNumber){
                                $booktype = 0;
                            }elseif(!$slotAvailable && !$findStoppedQueue && $queueBookingCount < $doctorClinic->QueueNumber){
                                $booktype = 1;
                            }
                        }*/
                        else{
                            $booktype = 0;
                        }
                        //$doctorcount[] = 1;
                        $returnDoctorArray[] = $doctorArray;
                        if($booktype !=0){
                            
                            $doctorArray['book_type']= $booktype;                         
                        }  
                    }
                    //$jsonArray['doctor_count']= count($doctorcount);
                    $jsonArray['doctors']= $returnDoctorArray;     
                }else{
                    $jsonArray['doctor_count']= 0;
                    $jsonArray['doctors']= null;
                }
                $returnObject->status = TRUE;
                $returnObject->data = $jsonArray;
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("NoRecords"); 
            }
        }
        return $returnObject;
    }
    
//    public static function ProcessClinicDetails1(){
//        $getID = Input::get('value');
//        $returnObject = new stdClass();
//        if(empty($getID)){
//            $returnObject->status = FALSE;
//            $returnObject->message = StringHelper::errorMessage("EmptyValues"); 
//        }else{
//            $clinicData = self::FindClinic($getID);
//            if($clinicData){
//                $jsonArray['clinic_id']= $clinicData->ClinicID;
//                $jsonArray['name']= $clinicData->Name;
//                $jsonArray['address']= $clinicData->Address;
//                $jsonArray['image_url']= URL::to('/assets/'.$clinicData->image);
//                $jsonArray['lattitude']= $clinicData->Lat;
//                $jsonArray['longitude']= $clinicData->Lng;
//                $jsonArray['telephone']= $clinicData->Phone;   
//                $jsonArray['open']= $clinicData->Opening;
//                $jsonArray['doctor_count']= DoctorLibrary::FindDoctorCount($clinicData->ClinicID); 
//                $panelInsurance = InsuranceLibrary::FindClinicInsurance($clinicData->ClinicID);
//                if($panelInsurance){
//                    $jsonArray['panel_insurance']['insurance_id']= $panelInsurance->CompanyID;
//                    $jsonArray['panel_insurance']['name']= $panelInsurance->Name;
//                    $jsonArray['panel_insurance']['image_url']= URL::to('/assets/'.$panelInsurance->Image);
//                    $jsonArray['annotation_url']= $panelInsurance->Annotation; 
//                }else{
//                    $findNonePanelInsurance = self::FindNonePanelInsurance();
//                    if($findNonePanelInsurance){
//                        $jsonArray['annotation_url']= $findNonePanelInsurance->Annotation;
//                    }else{
//                        $jsonArray['annotation_url']= null;
//                    }                  
//                    $jsonArray['panel_insurance'] =null;                        
//                }
//                $doctorsForClinic = DoctorLibrary::findDoctorsForClinic($clinicData->ClinicID);
//                if($doctorsForClinic){
//                    foreach($doctorsForClinic as $doctorClinic){
//                        $findDoctor = DoctorLibrary::findDoctor($doctorClinic->DoctorID);
//
//                        $doctorArray['doctor_id']= $findDoctor->DoctorID;
//                        $doctorArray['name']= $findDoctor->Name;
//                        $doctorArray['qualifications']= $findDoctor->Qualifications;
//                        $doctorArray['specialty']= $findDoctor->Specialty;
//                        $doctorArray['image_url']= URL::to('/assets/'.$findDoctor->image);
//                        $doctorArray['availability']= $findDoctor->Availability;
//                        $doctorArray['can_book']= $findDoctor->Active;
//
//                        $returnDoctorArray[] = $doctorArray;
//                    }
//                    $jsonArray['doctors']= $returnDoctorArray;     
//                }else{
//                    $jsonArray['doctors']= null;
//                }
//                $returnObject->status = TRUE;
//                $returnObject->data = $jsonArray;
//            }else{
//                $returnObject->status = FALSE;
//                $returnObject->message = StringHelper::errorMessage("NoRecords"); 
//            }
//        }
//        return $returnObject;
//    }
    
    
    
 
    public static function FindClinic($clinicid){
        $clinic = new Clinic();
        $clinicData = $clinic->ClinicDetails($clinicid);
        if($clinicData){
            return $clinicData;
        }else{
            return FALSE;
        }
    }

    public static function ProcessSearch1($UserID){
        $search = Input::get('search');
        $returnObject = new stdClass();
        if(!empty($search)){
            $clinicData = self::FindClinicSearch($search);
            if($clinicData){
                $panelCount = null;
                foreach($clinicData as $cdata){ 
                    $userprofile = AuthLibrary::FindUserProfileByRefID($cdata->ClinicID);
                    if(!empty($userprofile->Email)){
                        $clinicemail = $userprofile->Email;
                    }else{
                        $clinicemail = null;
                    }
                    $clinicOpenStatus = StringHelper::GetClinicOpenStatus($cdata->ClinicID);
                    $clinicOpenTime = StringHelper::GetClinicOpenTimes($cdata->ClinicID);
                    
                    $jsonArray['clinic_id']= $cdata->ClinicID;
                    $jsonArray['name']= $cdata->Name;
                    $jsonArray['email']= $clinicemail;
                    $jsonArray['address']= $cdata->Address;
                    //$jsonArray['image_url']= URL::to('/assets/'.$cdata->image);
                    $jsonArray['image_url']= $cdata->image;
                    $jsonArray['lattitude']= $cdata->Lat;
                    $jsonArray['longitude']= $cdata->Lng;
                    $jsonArray['telephone']= $cdata->Phone;  
                    $jsonArray['clinic_price']= $cdata->Clinic_Price;
                    //$jsonArray['open']= $cdata->Opening;
                    $jsonArray['open']= $clinicOpenTime;
                    $jsonArray['open_status']= $clinicOpenStatus;
                    
                    $jsonArray['doctor_count']= DoctorLibrary::FindDoctorCount($cdata->ClinicID);
                    //$panelInsurance = $insurance->getClinicInsuranceCompany($cdata->ClinicID);
                    $panelInsurance = self::ProcessPanelInsurance($UserID,$cdata->ClinicID);
                    $panelInsurance = FALSE;
                    if($panelInsurance){
                        $panelCount[] = count($panelInsurance);
                        $jsonArray['panel_insurance']['insurance_id']= $panelInsurance['insurance_id'];
                        $jsonArray['panel_insurance']['name']= $panelInsurance['name'];
                        //$jsonArray['panel_insurance']['image_url']= URL::to('/assets/'.$panelInsurance['image_url']);
                        $jsonArray['panel_insurance']['image_url']= $panelInsurance['image_url'];
                        $jsonArray['annotation_url']= $panelInsurance['annotation_url'];      
                    }else{
                        $findNonePanelInsurance = self::FindNonePanelInsurance();
                        if($findNonePanelInsurance){
                            $jsonArray['annotation_url']= $findNonePanelInsurance->Annotation;
                        }else{
                            $jsonArray['annotation_url']= null;
                        }                  
                        $jsonArray['panel_insurance'] =null;
                    } 
                    
                    $returnArray[] = $jsonArray;
                }
                $returnObject->status = TRUE;
                $returnObject->data['total_count'] = count($clinicData);
                $returnObject->data['panel_count'] = count($panelCount);
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
    
    
    
    public static function FindClinicSearch($search){
        $clinic = new Clinic();
        $clinicData = $clinic->search($search);
        if($clinicData){
            return $clinicData;
        }else{
            return FALSE;
        }
    }

    public static function FindClinicSearchWithCurrency($search, $user_id){
        $clinic = new Clinic();
        $wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();
        $clinicData = $clinic->searchWithCurrency($search, $wallet->currency_type);
        if($clinicData){
            return $clinicData;
        }else{
            return FALSE;
        }
    }
    
    /* use          :   Used to find appointment details 
     * Access       :   Public 
     * 
     */
    public static function AppointmentDetails($findUserID){
        $doctorslot = new DoctorSlots();
        $slotdetails = new DoctorSlotDetails();
        
        $returnObject = new stdClass();
        $bookingid = Input::get('booking_id');
        $findAppointment = self::FindAppointment($bookingid);
        if($findAppointment){
            $round =1; $nowSaving=0;$nowbooktype=0;
            $doctorSlotClinic = $doctorslot->FindSlotDoctorClinic($findAppointment->DoctorSlotID);
            if($findAppointment->BookType == 1){
                $findSlotDetail = $slotdetails->ActiveSlotDetails($findAppointment->SlotDetailID);
                $appointtime = $findSlotDetail->Time;
            }else{
                $findNowSaving = self::FindNowSaving($findAppointment->DoctorSlotID,$findAppointment->BookDate);
                if($findNowSaving){
                    $nowSaving = $findNowSaving->BookNumber;
                }
               /* if($round==1){
                    if($findAppointment->Status==1){
                        $nowSaving = $findAppointment->BookNumber;
                        $round ++;
                    }
                }*/
                $appointtime = null;
            }
                
            $findInsurance = self::ProcessPanelNonePanelInsurance($findUserID,$doctorSlotClinic->ClinicID);
            $findPatientCount = self::FindPatientCount($findAppointment->DoctorSlotID,$findAppointment->BookDate);
            if($findAppointment->BookType==0){$nowbooktype = 1;}elseif($findAppointment->BookType==1){$nowbooktype = 2;}
            $clinicOpenTime = StringHelper::GetClinicOpenTimes($doctorSlotClinic->ClinicID);
            
            $dataBook['booking']['booking_id'] = $findAppointment->UserAppoinmentID;
            $dataBook['booking']['type'] = $nowbooktype;
            $dataBook['booking']['date'] = $findAppointment->BookDate;
            $dataBook['booking']['time'] = $appointtime;
            $dataBook['booking']['no_of_patients'] = $findPatientCount;
            $dataBook['booking']['now_serving'] = $nowSaving;
            $dataBook['booking']['fee'] = $doctorSlotClinic->ConsultationCharge;
            $dataBook['booking']['queue_no'] = $findAppointment->BookNumber;
            
            $dataBook['doctor']['doctor_id'] = $doctorSlotClinic->DoctorID;
            $dataBook['doctor']['name'] = $doctorSlotClinic->Name;
            $dataBook['doctor']['qualifications'] = $doctorSlotClinic->Qualifications;
            $dataBook['doctor']['specialty'] = $doctorSlotClinic->Specialty;
            //$dataBook['doctor']['image_url'] = URL::to('/assets/'.$doctorSlotClinic->image);
            $dataBook['doctor']['image_url'] = $doctorSlotClinic->image;

            $dataBook['clinic']['clinic_id'] = $doctorSlotClinic->ClinicID;
            $dataBook['clinic']['annotation_url'] = $findInsurance['annotation_url'];       
            //$dataBook['clinic']['image_url'] = URL::to('/assets/'.$doctorSlotClinic->image);
            $dataBook['clinic']['image_url'] = $doctorSlotClinic->image;
            $dataBook['clinic']['name'] = $doctorSlotClinic->CName;
            $dataBook['clinic']['address'] = $doctorSlotClinic->Address;
            $dataBook['clinic']['lattitude'] = $doctorSlotClinic->Lat;
            $dataBook['clinic']['longitude'] = $doctorSlotClinic->Lng;
            $dataBook['clinic']['telephone'] = $doctorSlotClinic->Phone;
            $dataBook['clinic']['open'] = $clinicOpenTime;
            $dataBook['clinic']['doctor_count'] = DoctorLibrary::FindDoctorCount($doctorSlotClinic->ClinicID);
            $dataBook['clinic']['panel_insurance']['insurance_id'] = $findInsurance['insurance_id'];
            $dataBook['clinic']['panel_insurance']['name'] = $findInsurance['name'];
            //$dataBook['clinic']['panel_insurance']['image_url'] = URL::to('/assets/'.$findInsurance['image_url']);
            $dataBook['clinic']['panel_insurance']['image_url'] = $findInsurance['image_url'];
           
            $returnObject->status = TRUE;
            $returnObject->data = $dataBook;
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("NoRecords");
        }            
        return $returnObject;   
        
    }
    
    /* Use          :   Used to delete an appointment
     * Access       :   Public 
     * Parameter    : 
     */
    public static function AppointmentDelete(){
        $returnObject = new stdClass();
        $bookingid = Input::get('booking_id');
        $findAppointment = self::FindAppointment($bookingid);
        if($findAppointment){
            if($findAppointment->BookType==1){
                $dataArray['Active'] = 0;
            }
            $dataArray['Status'] = 3;
            $updated = DoctorLibrary::UpdateAppointment($dataArray,$findAppointment->UserAppoinmentID);
            if($updated){
                if($findAppointment->BookType==1){
                    $updateSlot['slotdetailid'] = $findAppointment->SlotDetailID;
                    $updateSlot['Available'] = 1;
                    $updateSlot['Active'] = 1;  
                    self::UpdateSlotDetails($updateSlot);
                }
                //Send Email
                $findDoctorClinic = self::FindDoctorSlotClinicService($findAppointment->DoctorSlotID);
                $findDoctorSlotDetails = DoctorLibrary::FindDoctorSlotDetails($findAppointment->SlotDetailID);
                $findUserDetails = AuthLibrary::FindUserProfile($findAppointment->UserID);
                if($findDoctorSlotDetails){ $booktime = $findDoctorSlotDetails->Time; }else{$booktime = 0;}
                
                $emailDdata['bookingTime'] = $booktime;
                $emailDdata['bookingNo'] = $findAppointment->BookNumber;
                $emailDdata['bookingDate'] = $findAppointment->BookDate; 
                $emailDdata['doctorName'] = $findDoctorClinic->DName.' , '.$findDoctorClinic->Specialty;
                $emailDdata['clinicName'] = $findDoctorClinic->CName;
                $emailDdata['clinicAddress'] = $findDoctorClinic->Address;
                $emailDdata['clinicPhone'] = $findDoctorClinic->CPhone;

                $emailDdata['emailName']= $findUserDetails->Name;
                $emailDdata['emailPage']= 'email-templates.booking-cancel';
                $emailDdata['emailTo']= $findUserDetails->Email;
                $emailDdata['emailSubject'] = 'Booking Cancelled';
                //$emailDdata['activeLink'] = "<a href='".URL::to('app/auth/login')."'> Find out more </a>";
                EmailHelper::sendEmail($emailDdata);
                
                
                $returnObject->status = TRUE;
                $returnObject->message = StringHelper::errorMessage("Deleted");
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Update");
            }    
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("NoRecords");
        }
        return $returnObject;  
    }
    
    
     
    public static function FindAppointment($appointmentid){
        $userappointment = new UserAppoinment();
        if(!empty($appointmentid)){
            $findAppointment = $userappointment->findAppointment($appointmentid);
            if($findAppointment){
                return $findAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public static function UpdateSlotDetails($dataArray){
        $slotdetails = new DoctorSlotDetails();
        if(count($dataArray) > 0){
            $updated = $slotdetails->updateSlotDetails($dataArray);
            if($updated){
                return $updated;
            }else{
                return FALSE;
            }  
        }else{
            return FALSE;
        }  
    }
    
    public static function FindPanelNearby($getLat,$getLng,$panelinsurance){
        $clinic = new Clinic();
        $radius = 1;
        $nearbyData = $clinic->PanelClinicNearby($getLat,$getLng,$radius,$panelinsurance);
        if($nearbyData){
            return $nearbyData;
        }else{
            return FALSE;
        }
    }
    
    public static function ProcessPanelClinicNearby(){
        $AllInputdata = Input::all();
        $returnObject = new stdClass();
        if(!empty($AllInputdata)){ 
            $findPanelNearby = self::FindPanelNearby($AllInputdata['lat'],$AllInputdata['lng'],$AllInputdata['insurance_id']);
            if($findPanelNearby){
                $findInsuranceCompnay = InsuranceLibrary::FindInsuranceCompany($findPanelNearby[0]->InsuranceID);
                
                foreach($findPanelNearby as $cdata){
                    $jsonArray['clinic_id']= $cdata->ClinicID;
                    $jsonArray['name']= $cdata->Name;
                    $jsonArray['address']= $cdata->Address;
                    //$jsonArray['image_url']= URL::to('/assets/'.$cdata->image);
                    $jsonArray['image_url']= $cdata->image;
                    $jsonArray['lattitude']= $cdata->Lat;
                    $jsonArray['longitude']= $cdata->Lng;
                    $jsonArray['telephone']= $cdata->Phone;   
                    $jsonArray['open']= $cdata->Opening;
                    $jsonArray['doctor_count']= DoctorLibrary::FindDoctorCount($cdata->ClinicID);
                    
                    $jsonArray['panel_insurance']['insurance_id']= $findInsuranceCompnay->CompanyID;
                    $jsonArray['panel_insurance']['name']= $findInsuranceCompnay->Name;
                    //$jsonArray['panel_insurance']['image_url']= URL::to('/assets/'.$findInsuranceCompnay->Image);
                    $jsonArray['panel_insurance']['image_url']= $findInsuranceCompnay->Image;
                    $jsonArray['annotation_url']= $findInsuranceCompnay->Annotation;      
                    
                    $returnArray[] = $jsonArray;
                }
                $returnObject->status = TRUE;
                $returnObject->data['total_count'] = count($findPanelNearby);
                $returnObject->data['panel_count'] = 1;
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
    
    public static function FindNowSaving($doctorslotid,$bookdate){
        $userappointment = new UserAppoinment();
        if(!empty($doctorslotid) && !empty($bookdate)){
            $findNowSaving = $userappointment->FindNowSaving($doctorslotid,$bookdate);
            if($findNowSaving){
                return $findNowSaving;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public static function UserAppointmentValidation($findUserID){
        $returnObject = new stdClass();
        //$AllInputdata = Input::all();
        //if(!empty($AllInputdata)){ 
            $findAnyAppointment = self::FindAnyAppointment($findUserID);
            $count = count($findAnyAppointment);
            if($count>=3){
                $returnObject->status = TRUE;
                $returnObject->message = StringHelper::errorMessage("ActiveBooking");
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
    public static function FindAnyAppointment($findUserID){
        $userappointment = new UserAppoinment();
        if(!empty($findUserID)){
            $findAppointment = $userappointment->FindAnyAppointment($findUserID);
            if($findAppointment){
                return $findAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindRealAppointment($findUserID){
        $userappointment = new UserAppoinment();
        if(!empty($findUserID)){
            $findAppointment = $userappointment->FindRealAppointment($findUserID);
            if($findAppointment){
                return $findAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindDoctorSlot($doctorslotid){
        $doctorslot = new DoctorSlots();
        if(!empty($doctorslot)){
            $findDoctorSlot = $doctorslot->findDoctorSlot($doctorslotid);
            if($findDoctorSlot){
                return $findDoctorSlot;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    public static function FindDoctorSlotClinicService($doctorslotid){
        $doctorslot = new DoctorSlots();
        if(!empty($doctorslot)){
            $findDoctorSlot = $doctorslot->FindDoctorSlotClinicService($doctorslotid);
            if($findDoctorSlot){
                return $findDoctorSlot;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    public static function FindClinicTimesStatus($clinicid,$getweek){
        $clinictime = new ClinicTimes();
        if(!empty($clinicid)){
            $findClinicTime = $clinictime->FindClinicTimesStatus($clinicid,$getweek);
            if($findClinicTime){
                return $findClinicTime;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    public static function FindClinicTimes($clinicid){
        $clinictime = new ClinicTimes();
        if(!empty($clinicid)){
            $findClinicTime = $clinictime->FindClinicTimes($clinicid);
            if($findClinicTime){
                return $findClinicTime;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
}