<?php

class Array_Helper{

    public static function GetClinicDetailArray($clinicuserArray,$clinicDetails){
        if(!empty($clinicDetails)){
            $data = array();
            $data['clinicid']= $clinicDetails->ClinicID;
            $data['clinicuserid']= $clinicuserArray->UserID;
            $data['name']= $clinicDetails->Name;
            $data['email']= $clinicuserArray->Email;
            $data['communication_email']= $clinicDetails->communication_email;
            $data['password']= $clinicuserArray->Password;
            $data['description']= $clinicDetails->Description;
            $data['custom_title']= $clinicDetails->Custom_title;
            $data['image']= $clinicDetails->image;
            $data['address']= $clinicDetails->Address;
            $data['city']= $clinicDetails->City;
            $data['state']= $clinicDetails->State;
            $data['country']= $clinicDetails->Country;
            $data['postal']= $clinicDetails->Postal;
            $data['district']= $clinicDetails->District;
            $data['MRT']= $clinicDetails->MRT;
            $data['lat']= $clinicDetails->Lat;
            $data['lng']= $clinicDetails->Lng;
            $data['phone_code']= $clinicDetails->Phone_Code;
            $data['code']= $clinicDetails->Phone_Code;
            $data['phone']= $clinicDetails->Phone;
            $data['website']= $clinicDetails->Website;
            $data['clinic_type']= $clinicDetails->Clinic_Type;
            $data['message']= $clinicDetails->Personalized_Message;
            // if($clinicDetails->Country == 'Singapore'){
            //     $data['code']= "+65";
            // }else{
            //     $data['code']= null;
            // }
            return $data;
        }else{
            return FALSE;
        }
    }


    public static function ClinicHolidayArray($findClinicHolidays){
        if($findClinicHolidays){

     // dd($findClinicHolidays);
            if($findClinicHolidays->Type == 0){
                $myclinicholiday['holiday_status'] = 1;
            }else{
                foreach($findClinicHolidays as $clinicHoliday){
                    /*$clinicholidaydata = array();
                    $clinicholidaydata['holidayid'] = $clinicHoliday->ManageHolidayID;
                    $clinicholidaydata['type'] = $clinicHoliday->Type;
                    $clinicholidaydata['holiday'] = $clinicHoliday->Holiday;
                    $clinicholidaydata['fromtime'] = $clinicHoliday->From_Time;
                    $clinicholidaydata['totime'] = $clinicHoliday->To_Time;
                    $clinicholidaydata['holiday_status'] = 0;
                     */
                    $clinicholidaydata = self::ProcessClinicHoliday($clinicHoliday);
                    $myclinicholiday[] = $clinicholidaydata;
                }
            }
        }else{
            $myclinicholiday = null;
        }
        return $myclinicholiday;
    }
    public static function ProcessClinicHoliday($clinicHoliday){
        if($clinicHoliday){
            $clinicholidaydata['holidayid'] = $clinicHoliday->ManageHolidayID;
            $clinicholidaydata['type'] = $clinicHoliday->Type;
            $clinicholidaydata['holiday'] = $clinicHoliday->Holiday;
            $clinicholidaydata['fromtime'] = $clinicHoliday->From_Time;
            $clinicholidaydata['totime'] = $clinicHoliday->To_Time;
            $clinicholidaydata['holiday_status'] = 0;
        }else{
            $clinicholidaydata = null;
        }
        return $clinicholidaydata;
    }
    public static function ClinicHolidayStatusArray($findClinicHolidays){
        if($findClinicHolidays){
            if($findClinicHolidays->Type == 0){
                $clinicholidaydata = 1;
            }else{
                foreach($findClinicHolidays as $clinicHoliday){
                    $clinicholidayarray = self::ProcessClinicHoliday($clinicHoliday);
                    $clinicholidaydata[] = $clinicholidayarray;
                }
            }
        }else{
            $clinicholidaydata = 1;
        }
        return $clinicholidaydata;
    }

    public static function ClinicAvailabilityArray($findClinicAvailability){
        if(!empty($findClinicAvailability)){
            foreach($findClinicAvailability as $clinicAvailability){
                $clinicdata = array();
                //$clinicdata['clinicid'] = $clinicAvailability->PartyID;
                $clinicdata['managetimeid'] = $clinicAvailability->ManageTimeID;
                $clinicdata['clinictimeid'] = $clinicAvailability->ClinicTimeID;
                $clinicdata['day_status'] = $clinicAvailability->Type;
                $clinicdata['fromdate'] = $clinicAvailability->From_Date;
                $clinicdata['todate'] = $clinicAvailability->To_Date;
                $clinicdata['repeat'] = $clinicAvailability->Repeat;
                $clinicdata['starttime'] = $clinicAvailability->StartTime;
                $clinicdata['endtime'] = $clinicAvailability->EndTime;
                $clinicdata['mon'] = $clinicAvailability->Mon;
                $clinicdata['tue'] = $clinicAvailability->Tue;
                $clinicdata['wed'] = $clinicAvailability->Wed;
                $clinicdata['thu'] = $clinicAvailability->Thu;
                $clinicdata['fri'] = $clinicAvailability->Fri;
                $clinicdata['sat'] = $clinicAvailability->Sat;
                $clinicdata['sun'] = $clinicAvailability->Sun;

                $myclinicdata[] = $clinicdata;
            }
        }else{
            $myclinicdata = null;
        }
        return $myclinicdata;
    }

    public static function DoctorDetailArray($findAllDoctors,$findWeek,$currentDate){
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
        return $doctorlist;
    }

    public static function DoctorDetailArray1($findAllDoctors,$findWeek,$currentDate){
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
        return $doctorlist;
    }

    public static function DoctorArrayWithCurrentDate($doctorid,$findWeek,$currentDate){
        if(!empty($doctorid)){
            //foreach($findAllDoctors as $findDoctor){
                //$doctorDetail = self::DoctorDetails($findDoctor);

                $findDoctorAvailability = General_Library::FindCurrentDayAvailableTimes(2,$doctorid,$findWeek,strtotime($currentDate));
                $doctorHoliday = General_Library::FindPartyFullDayHolidays(2,$doctorid,$currentDate);
                (!$doctorHoliday && $findDoctorAvailability) ? $activeDoctorTime = 1 : $activeDoctorTime = 1;
                $doctorDayHolidays = General_Library::FindCurrentDayHolidays(2,$doctorid,$currentDate);
// dd($findDoctorAvailability);
                if($activeDoctorTime ==1){
                    $doctorDetail['doctorid'] = $doctorid;
                    $doctorDetail['available'] = $activeDoctorTime;
                    $doctorDetail['available_times'] = self::ClinicAvailabilityArray($findDoctorAvailability);
                    $doctorDetail['holidays'] = self::ReturnHolidayArray($doctorDayHolidays);
                    $doctorDetail['existingappointments'] = General_Library::FindAllExistingAppointments($doctorid, strtotime($currentDate));
                    $events = new ExtraEvents();
                    $doctorDetail['extraAppointment'] = $events->getEvents($doctorid, strtotime($currentDate));
                    $doctorDetail['doctor-breaks'] = $events->getAllDoctorBreaks($doctorid);
                    $doctorDetailList = $doctorDetail;

                }else{
                    $doctorDetailList = null;
                }

            //}
        }else{
            $doctorDetailList = null;
        }
        return $doctorDetailList;
    }

    public static function DoctorDetails($doctordetails){
        if(!empty($doctordetails)){
            $doctorArray['doctor_id']= $doctordetails->DoctorID;
            $doctorArray['doctor_name']= $doctordetails->DocName;
            $doctorArray['doctor_email']= $doctordetails->DocEmail;
            $doctorArray['qualifications']= $doctordetails->Qualifications;
            $doctorArray['specialty']= $doctordetails->Specialty;
            $doctorArray['doctor_image']= $doctordetails->DocImage;
            $doctorArray['clinic_id']= $doctordetails->ClinicID;
            $doctorArray['clinic_name']= $doctordetails->CliName;
            $doctorArray['clinic_image']= $doctordetails->CliImage;
            $doctorArray['clinic_phone']= $doctordetails->CliPhone;

        }else{
            $doctorArray = null;
        }
        return $doctorArray;
    }
    public static function DoctorsArray($doctorsArray){
        foreach($doctorsArray as $DocArray){
            $doctorArray = self::DoctorDetails($DocArray);
            $returnArray[] = $doctorArray;
        }
        return $returnArray;
    }

    public static function ReturnHolidayArray($findHolidays){
        if($findHolidays){
            // foreach($findHolidays as $holidays){
                $clinicholidaydata['holidayid'] = $findHolidays->ManageHolidayID;
                $clinicholidaydata['type'] = $findHolidays->Type;
                $clinicholidaydata['holiday'] = $findHolidays->Holiday;
                $clinicholidaydata['fromtime'] = $findHolidays->From_Time;
                $clinicholidaydata['totime'] = $findHolidays->To_Time;
                $myclinicholiday[] = $clinicholidaydata;
            // }
        }else{
            $myclinicholiday = null;
        }
        return $myclinicholiday;
    }

    public static function DoctorAvailabilityArray($doctorid,$findWeek,$currentDate){
        if(!empty($doctorid)){
            //foreach($findAllDoctors as $findDoctor){
                //$doctorDetail = self::DoctorDetails($findDoctor);

                $findDoctorAvailability = General_Library::FindCurrentDayAvailableTimes(2,$doctorid,$findWeek,strtotime($currentDate));
                $doctorHoliday = General_Library::FindPartyFullDayHolidays(2,$doctorid,$currentDate);
                //(!$doctorHoliday && $findDoctorAvailability) ? $activeDoctorTime = 1 : $activeDoctorTime = 0;
                $doctorDayHolidays = General_Library::FindCurrentDayHolidays(2,$doctorid,$currentDate);

                //$doctorDetail['available'] = $activeDoctorTime;
                $doctorDetail['available_times'] = self::ClinicAvailabilityArray($findDoctorAvailability);
                $doctorDetail['holidays'] = self::ReturnHolidayArray($doctorDayHolidays);
                //$doctorlist[] = $doctorDetail;
            //}
        }else{
            $doctorDetail = null;
        }
        return $doctorDetail;
    }
    public static function DoctorStatusArray($findAllDoctors,$findWeek,$currentDate){
        if(!empty($findAllDoctors)){
            foreach($findAllDoctors as $findDoctor){
                $doctorDetail = self::DoctorDetails($findDoctor);

                $findDoctorAvailability = General_Library::FindCurrentDayAvailableTimes(2,$findDoctor->DoctorID,$findWeek,strtotime($currentDate));
                $doctorHoliday = General_Library::FindPartyFullDayHolidays(2,$findDoctor->DoctorID,$currentDate);
                (!$doctorHoliday && $findDoctorAvailability) ? $activeDoctorTime = 1 : $activeDoctorTime = 0;
                $doctorDayHolidays = General_Library::FindCurrentDayHolidays(2,$findDoctor->DoctorID,$currentDate);

                $doctorDetail['available'] = $activeDoctorTime;
                //$doctorDetail['available_times'] = self::ClinicAvailabilityArray($findDoctorAvailability);
                //$doctorDetail['holidays'] = self::ReturnHolidayArray($doctorDayHolidays);
                //$doctorDetail['existingappointments'] = General_Library::FindAllExistingAppointments($findDoctor->DoctorID, strtotime($currentDate));
                $doctorlist[] = $doctorDetail;
            }
        }else{
            $doctorlist = null;
        }
        return $doctorlist;
    }
    //End of class
}
