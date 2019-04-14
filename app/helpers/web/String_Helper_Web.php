<?php

class String_Helper_Web{
    
    public static function HolidayTimeCondition($holidayArray,$currentTime){
        if(!empty($holidayArray)){
            foreach($holidayArray as $clholiday){
                $convertdate = date('d-m-Y', $currentTime);
                $openTime = strtotime($convertdate.$clholiday['fromtime']);
                $closeTime = strtotime($convertdate.$clholiday['totime']);
                //$openTime = strtotime($clholiday['fromtime']);
                //$closeTime = strtotime($clholiday['totime']);
                if($currentTime >= $openTime && $currentTime < $closeTime){
                    return 1;
                }
            }
            return 0;
        }else{
            return 0;
        }
    }
    public static function HolidayTimeConditionStatus($holidayArray,$currentTime){
        if(!empty($holidayArray)){ 
            foreach($holidayArray as $clholiday){ 
                if($clholiday==1){
                    return 1;
                }else{
                    $convertdate = date('d-m-Y', $currentTime);
                    $openTime = strtotime($convertdate.$clholiday['fromtime']);
                    $closeTime = strtotime($convertdate.$clholiday['totime']);
                    //$openTime = strtotime($clholiday['fromtime']);
                    //$closeTime = strtotime($clholiday['totime']);
                    if($currentTime >= $openTime && $currentTime < $closeTime){
                        return 1;
                    }else{
                        //return 0;
                    }
                }
                
            }
            return 0;
        }else{
            return 0;
        }
    }
    
    public static function SlotChanges($dataValue){
        if($dataValue ==1){
            $cssclass = 'single';
        }elseif($dataValue ==2){
            $cssclass = 'two';
        }elseif($dataValue ==3){
            $cssclass = 'three';
        }elseif($dataValue ==4){
            $cssclass = 'four';
        }elseif($dataValue ==5){
            $cssclass = 'five';
        }elseif($dataValue ==6){
            $cssclass = 'six';
        }elseif($dataValue ==7){
            $cssclass = 'seven';
        }elseif($dataValue >7){
            $cssclass = 'dynamic';
        }elseif($dataValue ==0){
            $cssclass = 'single';
        }
        return $cssclass;
    }

    public static function GetActiveTime($starttime,$currentdate){
        if(!empty($starttime)){ 
            if(ArrayHelper::ActivePlusDate($currentdate)==1){ 
                return 1;
            }elseif(ArrayHelper::ActiveEqualDate($currentdate)==1){
                if($starttime >= strtotime(self::CurrentTime())){
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
    public static function CurrentTime(){
        $dateTime = StringHelper::TimeZone();
        $timezone = $dateTime->format('h.i A');
        return $timezone;
    }

    public static function FindEndTime($starttime,$duration){
        StringHelper::TimeZone();
        $endtime = strtotime("+{$duration} minutes", $starttime);
        return $endtime;
    }

    public static function BookingColor($eventType,$mediaType){
        $color = 'color-clinic-booking';
        if($eventType==3){
            $color = 'color-widget-booking';
        }elseif($eventType==1){
            $color = 'color-google-event';
        }elseif($eventType==0 && $mediaType==0){
            $color = 'color-app-booking';
        }elseif($eventType==0 && $mediaType==1){
            $color = 'color-clinic-booking';
        }
        return $color;
    }
    //End of class 
}

