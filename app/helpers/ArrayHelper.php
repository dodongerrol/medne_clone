<?php
class ArrayHelper{
    
    public static function clinicProfile($clinicData){
        
        $jsonClinic = array();
        
        //$jsonClinic['clinic_id']= $clinicData->ClinicID;
        $jsonClinic['name']= $clinicData->Name;
        //$jsonClinic['address']= $clinicData->Address;
        //$jsonClinic['image_url']= URL::to('/assets/'.$clinicData->image);
        //$jsonClinic['lattitude']= $clinicData->Lat;
        //$jsonClinic['longitude']= $clinicData->Lng;
        //$jsonClinic['telephone']= $clinicData->Phone;   
        //$jsonClinic['open']= $clinicData->Opening;
        //$jsonClinic['doctor_count']= $doctor->findDoctorCount($clinicData->ClinicID);
        
        print_r($clinicData);
        //return $jsonClinic;
    }
    
    public static function searchSlot($slotArray,$value){
        //$os = array("Mac", "NT", "Irix", "Linux");
        //echo 'called me';
//        for($i=0; $i< count($slotArray);$i++){
//        $findRec = array_search($value, $slotArray[$i]['slot']);
//            if($findRec){
//                return 1;
//            }else{
//                return 0;
//            }
//        }
        
        foreach($slotArray as $slot){
            $findRec = array_search($value, $slot['slot']);
            if($findRec){
                echo $findRec;
                //return 1;
            }else{
                return 0;
            }
        }
        
        echo 'now';
        //echo '<pre>';
        //print_r($slotArray);
        //echo '</pre>';
        //print_r($slotArray[0]['slot']);
        /*for($i=0; $i< count($slotArray);$i++){
            if (in_array($value, $slotArray[$i]['slot'])) {
                //echo "Got it";
                $output = $slotArray[$i]['slot']['time'];
            }else{
               $output = null;
            }
        }*/
        //echo $output;
        //return $output;
        //echo count($slotArray);
    }


    public static function getTimeSlot_By12($timeno){
        if($timeno==0){return '7.00AM';}
        elseif($timeno==1){return '7.30AM';}
        elseif($timeno==2){return '8.00AM';}
        elseif($timeno==3){return '8.30AM';}
        elseif($timeno==4){return '9.00AM';}
        elseif($timeno==5){return '9.30AM';}
        elseif($timeno==6){return '10.00AM';}
        elseif($timeno==7){return '10.30AM';}
        elseif($timeno==8){return '11.00AM';}
        elseif($timeno==9){return '11.30AM';}
        elseif($timeno==10){return '12.00PM';}
        elseif($timeno==11){return '12.30PM';}
        elseif($timeno==12){return '1.00PM';}
        elseif($timeno==13){return '1.30PM';}
        elseif($timeno==14){return '2.00PM';}
        elseif($timeno==15){return '2.30PM';}
        elseif($timeno==16){return '3.00PM';}
        elseif($timeno==17){return '3.30PM';}
        elseif($timeno==18){return '4.00PM';}
        elseif($timeno==19){return '4.30PM';}
        elseif($timeno==20){return '5.00PM';}
        elseif($timeno==21){return '5.30PM';}
        elseif($timeno==22){return '6.00PM';}
        elseif($timeno==23){return '6.30PM';}
        elseif($timeno==24){return '7.00PM';}
        elseif($timeno==25){return '7.30PM';}
        elseif($timeno==26){return '8.00PM';}
        elseif($timeno==27){return '8.30PM';}
        elseif($timeno==28){return '9.00PM';}
        elseif($timeno==29){return '9.30PM';}
    }
    public static function getTimeSlot($timeno){
        if($timeno==0){return '07.00AM';}
        elseif($timeno==1){return '07.30AM';}
        elseif($timeno==2){return '08.00AM';}
        elseif($timeno==3){return '08.30AM';}
        elseif($timeno==4){return '09.00AM';}
        elseif($timeno==5){return '09.30AM';}
        elseif($timeno==6){return '10.00AM';}
        elseif($timeno==7){return '10.30AM';}
        elseif($timeno==8){return '11.00AM';}
        elseif($timeno==9){return '11.30AM';}
        elseif($timeno==10){return '12.00PM';}
        elseif($timeno==11){return '12.30PM';}
        elseif($timeno==12){return '13.00PM';}
        elseif($timeno==13){return '13.30PM';}
        elseif($timeno==14){return '14.00PM';}
        elseif($timeno==15){return '14.30PM';}
        elseif($timeno==16){return '15.00PM';}
        elseif($timeno==17){return '15.30PM';}
        elseif($timeno==18){return '16.00PM';}
        elseif($timeno==19){return '16.30PM';}
        elseif($timeno==20){return '17.00PM';}
        elseif($timeno==21){return '17.30PM';}
        elseif($timeno==22){return '18.00PM';}
        elseif($timeno==23){return '18.30PM';}
        elseif($timeno==24){return '19.00PM';}
        elseif($timeno==25){return '19.30PM';}
        elseif($timeno==26){return '20.00PM';}
        elseif($timeno==27){return '20.30PM';}
        elseif($timeno==28){return '21.00PM';}
        elseif($timeno==29){return '21.30PM';}
    }
    
    
    public static function getTimeSlot90_By12($timeno){
        if($timeno==0){return '7.00AM';}
        elseif($timeno==1){return '8.00AM';}
        elseif($timeno==2){return '9.00AM';}
        elseif($timeno==3){return '10.00AM';}
        elseif($timeno==4){return '11.00AM';}
        elseif($timeno==5){return '12.00PM';}
        elseif($timeno==6){return '1.00PM';}
        elseif($timeno==7){return '2.00PM';}
        elseif($timeno==8){return '3.00PM';}
        elseif($timeno==9){return '4.00PM';}
        elseif($timeno==10){return '5.00PM';}
        elseif($timeno==11){return '6.00PM';}
        elseif($timeno==12){return '7.00PM';}
        elseif($timeno==13){return '8.00PM';}
        elseif($timeno==14){return '9.00PM';}
    }
    public static function getTimeSlot90($timeno){
        if($timeno==0){return '07.00AM';}
        elseif($timeno==1){return '08.00AM';}
        elseif($timeno==2){return '09.00AM';}
        elseif($timeno==3){return '10.00AM';}
        elseif($timeno==4){return '11.00AM';}
        elseif($timeno==5){return '12.00PM';}
        elseif($timeno==6){return '13.00PM';}
        elseif($timeno==7){return '14.00PM';}
        elseif($timeno==8){return '15.00PM';}
        elseif($timeno==9){return '16.00PM';}
        elseif($timeno==10){return '17.00PM';}
        elseif($timeno==11){return '18.00PM';}
        elseif($timeno==12){return '19.00PM';}
        elseif($timeno==13){return '20.00PM';}
        elseif($timeno==14){return '21.00PM';}
    }

    /*  Use             :   Used to find itmeslot
     * Access           :   No public access is allowed
     * 
     */
    public static function getSlotType($type){
        if($type !=""){
            if($type=="30min"){
                return 1;
            }elseif($type=="60min"){
                return 2;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    public static function ActivePlusTime($slottime,$slotdate){
        if(!empty($slottime)){
            $newSlotTime = substr($slottime,0, -2);  
            if(self::ActivePlusDate($slotdate)==1){ 
                return 1;
            }elseif(self::ActiveEqualDate($slotdate)==1){
                if($newSlotTime >= StringHelper::CurrentTime()){
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
    public static function ActiveTime($slottime,$slotdate){
        if(!empty($slottime)){
            $newSlotTime = substr($slottime,0, -2);  
            if($newSlotTime >= StringHelper::CurrentTime() && self::ActiveDate($slotdate)==1){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }  
    }
    
    public static function ActiveDate($date){
        if(!empty($date)){
            //if($date >= date('d-m-Y')){
            if(strtotime($date) >= strtotime(date("d-m-Y"))){    
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    public static function ActivePlusDate($date){
        if(!empty($date)){ 
            //if($date > date('d-m-Y')){
            if(strtotime($date) > strtotime(date("d-m-Y"))){    
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    public static function ActiveEqualDate($date){
        if(!empty($date)){
            //if($date == date('d-m-Y')){
            if(strtotime($date) == strtotime(date("d-m-Y"))){    
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    public static function encode($password)
    {
            return md5($password);
    }
    public static function requestHeader()
    {
        /*
            Description: 
                For Third Party API
            Developer: 
                Stephen
            Date of refactor:
                April 9, 2020
        */ 
        $thirdPartyAuthorization = '';
        $getRequestHeader = getallheaders();

        if (!empty($getRequestHeader['X-ACCESS-KEY']) && !empty($getRequestHeader['X-MEMBER-ID'])) {
            $getRequestHeader['Authorization'] = self::verifyXAccessKey();
        } else {
            if(!empty($getRequestHeader['authorization']) && $getRequestHeader['authorization'] != null) {
                $getRequestHeader['Authorization'] = $getRequestHeader['authorization'];
            }
        }
        
        return $getRequestHeader;
    }
    
    public static function DoctorArray($doctorObject){
        if(!empty($doctorObject)){
            $doctorArray['doctor_id']= $doctorObject->DoctorID;
            $doctorArray['name']= $doctorObject->Name;
            $doctorArray['qualifications']= $doctorObject->Qualifications;
            $doctorArray['specialty']= $doctorObject->Specialty;
            //$doctorArray['image_url']= URL::to('/assets/'.$doctorObject->image);
            $doctorArray['image_url']= $doctorObject->image;
            $doctorArray['availability']= $doctorObject->Availability;
            $doctorArray['can_book']= $doctorObject->Active;
            $doctorArray['available_dates']= "";
            return $doctorArray;
        }else{
           return FALSE; 
        } 
    }
    public static function ClinicArray($clinicObject){
        if(!empty($clinicObject)){
            $userprofile = AuthLibrary::FindUserProfileByRefID($clinicObject->ClinicID);
            if(!empty($userprofile->Email)){
                $clinicemail = $userprofile->Email;
            }else{
                $clinicemail = null;
            }
            $clinicArray['clinic_id']= $clinicObject->ClinicID;
            $clinicArray['name']= $clinicObject->Name;
            $clinicArray['email']= $clinicemail;
            $clinicArray['address']= $clinicObject->Address;
            //$clinicArray['image_url']= URL::to('/assets/'.$clinicObject->image);
            $clinicArray['image_url']= $clinicObject->image;

            $clinicArray['lattitude']= $clinicObject->Lat;
            $clinicArray['longitude']= $clinicObject->Lng;
            $clinicArray['telephone']= $clinicObject->Phone;
            $clinicArray['clinic_price']= $clinicObject->Clinic_Price;
            $clinicArray['open']= $clinicObject->Opening;
            return $clinicArray;
        }else{
            return FALSE;
        }    
    }
    public static function InsurancesArray($panelInsurance){
        if(!empty($panelInsurance)){
            $doctorArray['panel_insurance']['insurance_id']= $panelInsurance->CompanyID;
            $doctorArray['panel_insurance']['name']= $panelInsurance->Name;
            $doctorArray['panel_insurance']['image_url']= URL::to('/assets/'.$panelInsurance->Image);
            $doctorArray['annotation_url']= URL::to('/assets/'.$panelInsurance->Annotation);  
        }else{
            $findAnnotation = InsuranceLibrary::FindAnnotation();
            //$findAnnotation = $insurance->findAnnotation();
            if($findAnnotation){
                $doctorArray['annotation_url']= URL::to('/assets/'.$findAnnotation->Annotation);
            }else{
                $doctorArray['annotation_url']= null;
            }
            $doctorArray['panel_insurance'] =null;
        }
        return $doctorArray;
        
    }
    public static function QueueArray($doctorSlot, $nowSaving, $totalPatientCount, $nextAvailableNumber, $currentdate){
        if(!empty($doctorSlot) && !empty($currentdate)){
            $doctorArray['queue_id'] = $doctorSlot->DoctorSlotID;
            $doctorArray['no_of_patients'] = $totalPatientCount;
            $doctorArray['now_serving'] = $nowSaving;
            $doctorArray['fee'] = $doctorSlot->ConsultationCharge;
            $doctorArray['date'] = $currentdate;
            $doctorArray['next_availble_queue_no'] = $nextAvailableNumber;
            $doctorArray['estimated_time'] = $doctorSlot->QueueTime;
            
            return $doctorArray;
        }else{
            return FALSE;
        }   
    }

    public static function SlotArray($findDoctorSlot,$currentdate,$totalPatientCount,$nowSaving,$nextavailableslot,$nextStartTime,$nextEndTime,$nextStatus,$sdArray){
        if(!empty($findDoctorSlot) && !empty($currentdate)){
            $doctorArray['doctorslot_id'] = $findDoctorSlot->DoctorSlotID;
            $doctorArray['no_of_patients'] = $totalPatientCount;
            $doctorArray['now_serving'] = $nowSaving;
            $doctorArray['fee'] = $findDoctorSlot->ConsultationCharge;
            $doctorArray['date'] = $currentdate;
            //$doctorArray['booking']['timeslot']['next_availble_slot_id'] = $nextavailableslot;
            if($nextStatus==1){
            $doctorArray['next_available_slot']['slot_id'] = $nextavailableslot;
            $doctorArray['next_available_slot']['start_time'] = $nextStartTime;
            $doctorArray['next_available_slot']['end_time'] = $nextEndTime;
            $doctorArray['next_available_slot']['status'] = $nextStatus;
            }else{
                $doctorArray['next_available_slot'] = null;
            }
            $doctorArray['times'] = $sdArray;
        }else{
            $doctorArray = null;
        }
        return $doctorArray;
    }

    
        
}