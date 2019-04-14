<?php

class General_Library_Mobile {
    
    public static function FindClinicDoctor($clinicid, $doctorid){
        if(!empty($clinicid) && !empty($doctorid)){
            $doctoravailability = new DoctorAvailability();
            $findClinicDoctors = $doctoravailability->FindClinicDoctor($clinicid, $doctorid);
            if($findClinicDoctors){
                return $findClinicDoctors;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }
    public static function FindClinicDoctorProcedures($doctorid, $procedureid){
        if(!empty($procedureid) && !empty($doctorid)){
            $doctorprocedure = new DoctorProcedures();
            $findClinicDoctorProcedure = $doctorprocedure->FindClinicDoctorProcedures($doctorid,$procedureid);
            if($findClinicDoctorProcedure){
                return $findClinicDoctorProcedure;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }
    public static function FindProcedureAppointments($doctorid, $procedureid, $bookdate, $starttime,$endtime){
        if(!empty($procedureid) && !empty($doctorid)){
            $userappointment = new UserAppoinment();
            $findProcedureAppointment = $userappointment->FindProcedureAppointments($doctorid,$procedureid,$bookdate, $starttime,$endtime);
            if($findProcedureAppointment){
                return $findProcedureAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }
    public static function FindTimelyAppointments($doctorid, $bookdate, $starttime,$endtime){
        if(!empty($bookdate) && !empty($doctorid)){
            $userappointment = new UserAppoinment();
            $findProcedureAppointment = $userappointment->FindTimelyAppointments($doctorid,$bookdate, $starttime,$endtime);
            if($findProcedureAppointment){
                return $findProcedureAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }
    public static function FindTodayAppointments($doctorid, $bookdate){
        if(!empty($bookdate) && !empty($doctorid)){
            $userappointment = new UserAppoinment();
            $findProcedureAppointment = $userappointment->FindTodayAppointments($doctorid,$bookdate);
            if($findProcedureAppointment){
                return $findProcedureAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }
// nhr
    public static function FindTodayExtraEvents($doctorid, $bookdate){
        if(!empty($bookdate) && !empty($doctorid)){
            $userEvents = new ExtraEvents();
            $findEvents = $userEvents->FindTodayExtraEvents($doctorid,$bookdate);
            if($findEvents){
                return $findEvents;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }


    public static function FindTodayBreaks($doctorid, $bookdate){
        if(!empty($bookdate) && !empty($doctorid)){
            $day = date('D', $bookdate);
            $userEvents = new ExtraEvents();
            $findEvents = $userEvents->FindTodayBreaks($doctorid,$day);
            if($findEvents){
                return $findEvents;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }

    public static function FindTodayTimeOFF($doctorid, $bookdate){
        if(!empty($bookdate) && !empty($doctorid)){
            
            $userEvents = new ManageHolidays();
            $findEvents = $userEvents->FindTodayTimeOff($doctorid,$bookdate);
            if($findEvents){
                return $findEvents;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }

    public static function NewAppointment($newarray){
        if(count($newarray) > 0){
            $userappointment = new UserAppoinment();
            $newAppointment = $userappointment->NewAppointment($newarray);
            if($newAppointment){
                return $newAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }
    public static function FindAppointment($appointid){
        if(!empty($appointid)){
            $userappointment = new UserAppoinment();
            $findAppointment = $userappointment->findAppointment($appointid);
            if($findAppointment){
                return $findAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }
    public static function UpdateAppointment($updateArray, $appointid){
        if(!empty($appointid)){
            $userappointment = new UserAppoinment();
            $findAppointment = $userappointment->updateUserAppointment($updateArray,$appointid);
            if($findAppointment){
                return $findAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }
    public static function NumberOfBookings($doctorid, $bookingdate){
        if(!empty($doctorid)){
            $userappointment = new UserAppoinment();
            $findAppointment = $userappointment->NumberOfBookings($doctorid, $bookingdate);
            if($findAppointment){
                return $findAppointment;
            }else{
                return 0;
            }
        }else{
            return 0;
        }  
    }
    public static function FindUserAppointments($userid){
        if(!empty($userid)){
            $userappointment = new UserAppoinment();
            $findAppointments = $userappointment->FindUserAppointments($userid);
            if($findAppointments){
                return $findAppointments;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }
    
}

