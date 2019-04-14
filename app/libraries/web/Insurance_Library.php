<?php

class Insurance_Library {
    
    public static function FindAllInsurance(){
        $insurancecompany = new InsuranceCompany();
        $findCompany = $insurancecompany->findInsuranceCompany();
        if($findCompany){
            return $findCompany;
        }else{
            return FALSE;
        }
    }
    
    public static function AllClinicInsuranceCompany($clinicid){
        $clinicInsurance = new ClinicInsurenceCompany();
        $panelInsurance = $clinicInsurance->FindClinicInsuranceCompnay($clinicid);

        if($panelInsurance){
            return $panelInsurance;
        }else{
            return FALSE;
        }
    }
    
    
    
    
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
    
    
    
    
    
    
}