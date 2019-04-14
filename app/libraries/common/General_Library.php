<?php

class General_Library {

    public static function ActivePromoCode(){
        $promocode = new PromoCode();
        $findPromoCode = $promocode->FindPromoCode();
        if($findPromoCode){
            return $findPromoCode;
        }else{
            return FALSE;
        }
    }
    public static function FindActivePromoCode($code){
        $promocode = new PromoCode();
        $findPromoCode = $promocode->GetActivePromoCode($code);
        if($findPromoCode){
            return $findPromoCode;
        }else{
            return FALSE;
        }
    }

    public static function InsertUserPromoCode($dataArray){
        $userpromocode = new UserPromoCode();
        $insertedid = $userpromocode->InsertUserPromoCode($dataArray);
        if($insertedid){
            return $insertedid;
        }else{
            return FALSE;
        }
    }

    public static function FindUserPromoCode($userid,$promocodeid){
        $userpromocode = new UserPromoCode();
        if(!empty($userid) && !empty($promocodeid)){
            $findUserPromoCode = $userpromocode->FindUserPromoCode($userid,$promocodeid);
            if($findUserPromoCode){
                return $findUserPromoCode;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindExistingManageTimes($party,$partyid,$currentdate){
        $managetimes = new ManageTimes();
        if(!empty($party) && !empty($partyid)){
            $findManageTimes = $managetimes->FindExistingManageTimes($party,$partyid,$currentdate);
            if($findManageTimes){
                return $findManageTimes;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindAllClinicTimes($party,$partyid,$currentdate){
        $managetimes = new ManageTimes();
        if(!empty($party) && !empty($partyid)){
            $findClinicTimes = $managetimes->FindAllClinicTimes($party,$partyid,$currentdate);
            if($findClinicTimes){
                return $findClinicTimes;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    // nhr 2016/4/29  get without acitive check
    public static function FindAllClinicTimesNew($party,$partyid,$currentdate){
        $managetimes = new ManageTimes();
        if(!empty($party) && !empty($partyid)){
            $findClinicTimes = $managetimes->FindAllClinicTimesNew($party,$partyid,$currentdate);
            if($findClinicTimes){
                return $findClinicTimes;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    /* Use      :   Used to update Clinic Times
     *
     */
    public static function UpdateClinicTimes($dataArray){
        $clinictimes = new ClinicTimes();
        if(!empty($dataArray)){
            $updateClinicTimes = $clinictimes->UpdateClinicTimes($dataArray);
            if($updateClinicTimes){
                return $updateClinicTimes;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public static function AddManageHolidays($dataArray){
        $manageholidays = new ManageHolidays();
        if(!empty($dataArray)){
            $addManageHolidays = $manageholidays->AddManageHolidays($dataArray);
            if($addManageHolidays){
                return $addManageHolidays;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindExistingClinicHolidays($party,$partyid){
        $manageholidays = new ManageHolidays();
        if(!empty($party) && !empty($partyid)){
            $clinicHolidays = $manageholidays->FindExistingClinicHolidays($party,$partyid);
            if($clinicHolidays){
                return $clinicHolidays;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public static function FindFullDayHolidays($party,$partyid){
        $manageholidays = new ManageHolidays();
        if(!empty($party) && !empty($partyid)){
            $fulldayHolidays = $manageholidays->FindFullDayHolidays($party,$partyid);
            if($fulldayHolidays){
                return $fulldayHolidays;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public static function FindCustomTimeHolidays($party,$partyid){
        $manageholidays = new ManageHolidays();
        if(!empty($party) && !empty($partyid)){
            $fulldayHolidays = $manageholidays->FindCustomTimeHolidays($party,$partyid);
            if($fulldayHolidays){
                return $fulldayHolidays;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public static function UpdateManageHolidays($dataArray){
        $manageholidays = new ManageHolidays();
        if(!empty($dataArray)){
            $updateManageHolidays = $manageholidays->UpdateManageHolidays($dataArray);
            if($updateManageHolidays){
                return $updateManageHolidays;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    /*public static function FindDoctorAvailabilityTimes($party,$partyid){
        $managetimes = new ManageTimes();
        if(!empty($party) && !empty($partyid)){
            $findClinicTimes = $managetimes->FindAllClinicTimes($party,$partyid);
            if($findClinicTimes){
                return $findClinicTimes;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }*/
    /* Use      :   Used to update manage times
     *
     */
    public static function UpdateManageTimes($updateArray){
        $managetimes = new ManageTimes();
        if(!empty($updateArray)){
            $updateManageTimes = $managetimes->UpdateManageTimes($updateArray);
            if($updateManageTimes){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    /* Use          :   Used to find day holiday by clinic and doctor
     *
     */
    public static function FindPartyFullDayHolidays($party,$partyid,$holiday){
        $manageholidays = new ManageHolidays();
        if(!empty($party) && !empty($partyid) && !empty($holiday)){
            $findHolidays = $manageholidays->FindPartyFullDayHolidays($party,$partyid,$holiday);
            if($findHolidays){
                return $findHolidays;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindDayAvailableTime($party,$partyid,$findweek,$currentdate){
        $managetimes = new ManageTimes();
        if(!empty($party) && !empty($partyid) && !empty($findweek)){
            $dayAvailableTimes = $managetimes->FindDayAvailableTime($party,$partyid,$findweek,$currentdate);
            if($dayAvailableTimes){
                return $dayAvailableTimes;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    /* Use          :   Used to find current day holiday by clinic and doctor
     *
     */
    public static function FindCurrentDayHolidays($party,$partyid,$holiday){
        $manageholidays = new ManageHolidays();
        if(!empty($party) && !empty($partyid) && !empty($holiday)){
            $findHolidays = $manageholidays->FindCurrentDayHolidays($party,$partyid,$holiday);
            if($findHolidays){
                return $findHolidays;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FilterCurrentClinicHolidays($party,$partyid,$holiday){
        $findClinicHolidays = self::FindCurrentDayHolidays($party,$partyid,$holiday);
        if($findClinicHolidays){
            $myclinicholiday = Array_Helper::ClinicHolidayStatusArray($findClinicHolidays);
            return $myclinicholiday;
        }else{
            return FALSE;
        }
    }

    public static function FindCurrentDayAvailableTimes1($party,$partyid,$findweek,$currentdate){
        $managetimes = new ManageTimes();
        if(!empty($party) && !empty($partyid) && !empty($findweek)){
            $dayAvailableTimes = $managetimes->FindCurrentDayAvailableTimes($party,$partyid,$findweek,$currentdate);
            if($dayAvailableTimes){
                return $dayAvailableTimes;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindCurrentDayAvailableTimes($party,$partyid,$findweek,$currentdate){
        $managetimes = new ManageTimes();
        if(!empty($party) && !empty($partyid) && !empty($findweek)){
            $dayAvailableTimes = $managetimes->FindCurrentDayAvailableTimes($party,$partyid,$findweek,$currentdate);
            if($dayAvailableTimes){
                return $dayAvailableTimes;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    /* Use      :   Used to find upcoming holidays
     * Access   :   PUBLIC
     */
    public static function FindUpcomingHolidays($party,$partyid,$holiday){
        $manageholidays = new ManageHolidays();
        if(!empty($party) && !empty($partyid)){
            $upcomingHolidays = $manageholidays->FindUpcomingHolidays($party,$partyid,$holiday);
            if($upcomingHolidays){
                return $upcomingHolidays;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    /* Use      :   Used to make an Appointment
     * Access   :   PUBLIC
     */
    public static function NewAppointment($dataArray){
        $appointmnet = new UserAppoinment();
        if(!empty($dataArray)){
            $newAppointment = $appointmnet->NewAppointment($dataArray);
            if($newAppointment){
                return $newAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function UpdateAppointmentOnDrag($dataArray,$bookingid){
        $appointmnet = new UserAppoinment();
        if(!empty($dataArray)){
            $newAppointment = $appointmnet->UpdateAppointment($dataArray,$bookingid);
            if($newAppointment){
                return $newAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function UpdateAppointmentDelete($dataArray,$bookingid){
        $appointmnet = new UserAppoinment();
        if(!empty($dataArray)){
            $newAppointment = $appointmnet->updateAppointment($dataArray,$bookingid);
            if($newAppointment){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function UpdateAppointment($dataArray,$bookingid){
        $appointmnet = new UserAppoinment();
        if(!empty($dataArray)){
            $newAppointment = $appointmnet->updateAppointmentFromEdit($dataArray,$bookingid);
            if($newAppointment){
                return $newAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public static function FindExistingAppointment($doctorid,$bookdate){
        $appointmnet = new UserAppoinment();
        if(!empty($doctorid) && !empty($bookdate)){
            $newAppointment = $appointmnet->FindExistingAppointment($doctorid,$bookdate);
            if($newAppointment){
                return $newAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindAllExistingAppointments($doctorid,$bookdate){
        $appointmnet = new UserAppoinment();
        if(!empty($doctorid) && !empty($bookdate)){
            $newAppointment = $appointmnet->FindAllExistingAppointments($doctorid,$bookdate);
            if($newAppointment){
                return $newAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public static function FindEntireClinicAvailablity($party,$partyid,$currentdate,$days7){
        $managetimes = new ManageTimes();
        if(!empty($party) && !empty($partyid)){
            $entireAvailability = $managetimes->FindEntireClinicAvailablity($party,$partyid,$currentdate,$days7);
            if($entireAvailability){
                return $entireAvailability;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindLimitClinicAvailablity($party,$partyid,$currentdate){
        $managetimes = new ManageTimes();
        if(!empty($party) && !empty($partyid)){
            $entireAvailability = $managetimes->FindLimitClinicAvailablity($party,$partyid,$currentdate);
            if($entireAvailability){
                return $entireAvailability;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public static function FindUserAppointment($appointmentid){
        $appointmnet = new UserAppoinment();
        if(!empty($appointmentid)){
            $newAppointment = $appointmnet->FindUserAppointment($appointmentid);
            if($newAppointment){
                return $newAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
//    public static function FindClinic7DayHolidays($party,$partyid,$holiday,$days7){
//        $manageholidays = new ManageHolidays();
//        if(!empty($party) && !empty($partyid)){
//            $upcomingHolidays = $manageholidays->FindClinic7DayHolidays($party,$partyid,$holiday,$days7);
//            if($upcomingHolidays){
//                return $upcomingHolidays;
//            }else{
//                return FALSE;
//            }
//        }else{
//            return FALSE;
//        }
//    }

    public static function FindTimelyAppointment($doctorid,$procedureid,$bookdate,$startime){
        $appointmnet = new UserAppoinment();
        if(!empty($doctorid)&& !empty($procedureid) && !empty($bookdate)){
            $newAppointment = $appointmnet->FindTimelyAppointment($doctorid,$procedureid,$bookdate,$startime);
            if($newAppointment){
                return $newAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function AppointmentByDate($bookdate){
        $appointmnet = new UserAppoinment();
        if(!empty($bookdate)){
            $findAppointment = $appointmnet->AppointmentByDate($bookdate);
            if($findAppointment){
                return $findAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function AppointmentByHour($bookdate, $starttime){
        $appointmnet = new UserAppoinment();
        if(!empty($bookdate)){
            $findAppointment = $appointmnet->AppointmentByHour($bookdate, $starttime);
            if($findAppointment){
                return $findAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public static function FindClinicFromProcedure($doctorid,$procedureid){
        $docprocedure = new DoctorProcedures();
        if(!empty($doctorid) && !empty($procedureid)){
            $findDetails = $docprocedure->FindClinicFromProcedure($doctorid,$procedureid);
            if($findDetails){
                return $findDetails;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindClinicProcedure($procedureid){
        $clinicprocedure = new ClinicProcedures();
        if(!empty($procedureid)){
            $findDetails = $clinicprocedure->GetClinicProcedure($procedureid);
            if($findDetails){
                return $findDetails;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindClinicActiveTimes($managetimeid){
        $clinicitmes = new ClinicTimes();
        if(!empty($managetimeid)){
            $findDetails = $clinicitmes->FindClinicActivetimes($managetimeid);
            if($findDetails){
                return $findDetails;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindTimeBooking($doctorid,$clinictimeid){
        $appointmnet = new UserAppoinment();
        if(!empty($doctorid) && !empty($clinictimeid)){
            $findAppointment = $appointmnet->FindTimeBooking($doctorid,$clinictimeid);
            if($findAppointment){
                return $findAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindProcedureBooking($procedureid){
        $appointmnet = new UserAppoinment();
        if(!empty($procedureid)){
            $findAppointment = $appointmnet->FindProcedureBooking($procedureid);
            if($findAppointment){
                return $findAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindDoctorBooking($doctorid){
        $appointmnet = new UserAppoinment();
        if(!empty($doctorid)){
            $findAppointment = $appointmnet->FindDoctorBooking($doctorid);
            if($findAppointment){
                return $findAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindChannelBooking($clinicid,$currentdate,$currentTime){
        $appointmnet = new UserAppoinment();
        if(!empty($clinicid) && !empty($currentdate) && !empty($currentTime)){
            $findAppointment = $appointmnet->FindChannelBooking($clinicid,$currentdate,$currentTime);
            if($findAppointment){
                return $findAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function ClinicProcedureBoth($procedureid){
        $clinicprocedure = new ClinicProcedures();
        if(!empty($procedureid)){
            $findDetails = $clinicprocedure->ClinicProcedureBoth($procedureid);
            if($findDetails){
                return $findDetails;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public static function FindChannelBookingDoctor($clinicid,$doctorid,$currentdate,$currentTime){
        $appointmnet = new UserAppoinment();
        if(!empty($clinicid) && !empty($currentdate) && !empty($currentTime)){
            $findAppointment = $appointmnet->FindChannelBookingDoctor($clinicid,$doctorid,$currentdate,$currentTime);
            if($findAppointment){
                return $findAppointment;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

}
