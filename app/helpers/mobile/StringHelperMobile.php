<?php

class StringHelperMobile{
    
    /* Use      :   Used to find clinic open status
     * 
     */
    public static function FindClinicOpenStatus($party,$partyid,$clinicholidays,$currentDate){
        if(!empty($party) && !empty($partyid)){
            $findWeek = StringHelper::GetWeekFromTime();
            $findAvailableTimes = General_Library::FindCurrentDayAvailableTimes($party,$partyid,$findWeek, strtotime($currentDate));
            //echo '<pre>Times '; print_r($findAvailableTimes); echo '</pre>';
            
            if($findAvailableTimes){
                $clinicHolidays = self::ProcessHolidayStatus($clinicholidays);
                
                $openstatus = 0;$timecounter =0;
                foreach($findAvailableTimes as $opentimes){
                    if($timecounter == 0){
                        //if(strtotime($opentimes->From_Date) <=  strtotime($currentDate) && $opentimes->To_Date == 0 || strtotime($opentimes->To_Date) >= strtotime($currentDate)){   
                            if($opentimes->Type==0 && $clinicHolidays == 0){
                                $openstatus = 1;
                                $timecounter = 1;
                            }else{
                                if(strtotime($opentimes->StartTime) <= time() && strtotime($opentimes->EndTime) >=time() && $clinicHolidays == 0 ){
                                    $openstatus = 1;
                                    $timecounter = 1;
                                }
                            }
                        //}
                    }
                }
                return $openstatus;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    /* Use      :   Used to find holiday status
     * Access   :   Public 
     * 
     */
    public static function ProcessHolidayStatus($holidayClinicArray){
        if(!empty($holidayClinicArray)){
            foreach($holidayClinicArray as $holidayArray){
                if($holidayArray['type'] == 0){
                    return 0;
                }else{   
                    if(strtotime($holidayArray['starttime']) <= time() && strtotime($holidayArray['endtime']) >= time()){
                        return 1;
                    }
                }
            }
            return 0;
        }else{
            return 0;
        }
    }

    public static function HolidayTimeCondition($holidayArray,$currentTime){
        if(!empty($holidayArray)){
            foreach($holidayArray as $clholiday){
                if($clholiday['type']==0){
                    return 1;
                }else{
                    $openTime = strtotime($clholiday['starttime']);
                    $closeTime = strtotime($clholiday['endtime']);
                    if($currentTime >= $openTime && $currentTime < $closeTime){
                        return 1;
                    }
                }
            }
            return 0;
        }else{
            return 0;
        }
    }

    //end of class
}
