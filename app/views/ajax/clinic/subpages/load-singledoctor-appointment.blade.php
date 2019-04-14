<div class="calendar-time">
    <div class="time-title">TIME</div>
   <?php 
    $defaultStart = strtotime("06.00 AM");;
    $defaultEnd = strtotime("+1 day",$defaultStart);
    for($d=$defaultStart; $d<= $defaultEnd; $d = strtotime("+60 minutes", $d)){
        echo '<div class="time-count ">'.date('h:ia',$d).'</div>';
    }
    ?>
    
    <!--<div class="time-count ">7.00 am </div>
      <div class="time-count ">7.00 am </div>
    -->
     
    <div class="clear"></div>
    </div> <!--END OF CALENDAR TIME-->
    
    
  <div class="calendar-day  ">
 <!-- <div class="mCustomScrollbar" data-mcs-theme="dark">-->
 <!--class="content horizontal-images light"-->
 <!-- <div id="content-8" >
<div class="scroll-content">-->
  
  
  
  
<?php 
//$today = date("d-m-Y");
for($a=0; $a<7;$a++) {
    $dayOfWeek = date('d-m-Y', strtotime($loadarray['currentdate'].' +'.$a.' day'));
    $getWeek = date('l', strtotime($dayOfWeek));
    //$dayNow = date("d-m-Y");
    $findWeek = StringHelper::FindWeekFromDate($dayOfWeek);
    
    $defaultStart = strtotime($dayOfWeek."06.00 AM");
    $defaultEnd = strtotime("+1 day",$defaultStart); 
    if(strtotime($dayOfWeek) >=  strtotime(date('d-m-Y'))){
        $loadarrayDate = date('d-m-Y');
    }else{
        $loadarrayDate = $dayOfWeek;
    }
    
?>
  <div class="appointments-seven-day  fl">
   <div class="seven-slot-day g ">
    <div class="day c3">{{$getWeek}}</div>
    <div class="day c3">{{$dayOfWeek}}</div>
    
  </div>
    
    <?php 
    $findDoctorAvailability = Array_Helper::DoctorArrayWithCurrentDate($loadarray['currentdoctor'],$findWeek, $dayOfWeek);
    //echo '<pre>'; print_r($findDoctorAvailability); echo '</pre>';
    $findClinicHolidays = General_Library::FilterCurrentClinicHolidays(3,$loadarray['clinicid'], $dayOfWeek);
    $mainslotcounter = 0; $defualtcounter =0;
    for($d=$defaultStart; $d<$defaultEnd; $d = strtotime("+15 minutes", $d)){
        $roundCount = 0; $timevalue=0;
        if($loadarray['clinic_availability']){
            if($loadarray['clinic_availability'][0]->Repeat==1 || $loadarray['clinic_availability'][0]->To_Date >= strtotime($dayOfWeek)){
            
            
            foreach($loadarray['clinic_availability'] as $clinicAvailability){
                if($roundCount ==0){
                    if($clinicAvailability->$findWeek ==1){
                        $startTime = strtotime($dayOfWeek.$clinicAvailability->StartTime);
                        $endTime = strtotime($dayOfWeek.$clinicAvailability->EndTime);
                        //$startTime = strtotime($clinicAvailability->StartTime);
                        //$endTime = strtotime($clinicAvailability->EndTime);

                        for($i=$startTime; $i<$endTime; $i = strtotime("+15 minutes", $i)){   
                            if($findClinicHolidays !=1){
                                $returnHoliday = String_Helper_Web::HolidayTimeCondition($findClinicHolidays,$i);
                                if($returnHoliday!=1){  
                                    if($i == $d){
                                        $mainslotcounter = $mainslotcounter +1;
                                        $defualtcounter = $defualtcounter +1;
                                        if($mainslotcounter == $defualtcounter){
                                        
                                        //for doctor 
                                        if($findDoctorAvailability){
                                            $doctortimecount = 0; $activeAvailability = 0; 
                                            if($findDoctorAvailability['available_times'][0]['repeat']==1 || $findDoctorAvailability['available_times'][0]['todate'] >= strtotime($dayOfWeek)){
                                            foreach($findDoctorAvailability['available_times'] as $doctortime){
                                                $doctorstarttime = strtotime($dayOfWeek.$doctortime['starttime']);
                                                $doctorendtime = strtotime($dayOfWeek.$doctortime['endtime']);
                                                //$doctorstarttime = strtotime($doctortime['starttime']);
                                                //$doctorendtime = strtotime($doctortime['endtime']);
                                                $returnDoctorHoliday = String_Helper_Web::HolidayTimeCondition($findDoctorAvailability['holidays'],$i);
                                                if($doctorstarttime <= $i && $doctorendtime > $i && $returnDoctorHoliday !=1){
                                                   //echo '<div class="seven-slot15min-blue"><div class=" single-textblock-15min f1 c1">'.date('h:i A',$d).'</div></div>';
                                                    if($doctortimecount ==0){
                                                        $activeAvailability = 1;
                                                        $doctortimecount = 1;
                                                        $availabletime = $doctortime['clinictimeid'];
                                                    }
                                                   
                                                   $timevalue = 1;
                                                   $roundCount = 1;
                                                   
                                                }
                                            } }
                                            if($activeAvailability == 1){
                                                $bookingvalue = 0; $bookingcount = 0; $remainingslot = 0;
                                                if($findDoctorAvailability['existingappointments']){                                          
                                                    foreach($findDoctorAvailability['existingappointments'] as $existingappointment){
                                                        // nhr change style for popup
                                                          if ($existingappointment->event_type==1) {
                                                            $pop = ' ';
                                                            $nName = "Google Event";
                                                            $nProcedure = '';
                                                          }else{
                                                            $pop = ' pop1 ';
                                                            $nName = $existingappointment->UsrName;
                                                            $nProcedure = $existingappointment->ProName;
                                                          }
                                                          $findColorClass = String_Helper_Web::BookingColor($existingappointment->event_type,$existingappointment->MediaType);
                                                        if($bookingcount == 0){
                                                            $duration = $existingappointment->Duration;
                                                            $endduration = strtotime("+".$duration." minutes", $i);
                                                            if($duration ==15){
                                                                //echo $existingappointment->StartTime.'=='.$i.'<br>';
                                                                if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                                    $bookingvalue = 1;
                                                                    $bookingcount = 1;
                                                                    if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                        echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.' seven-slot15min-blue" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="1">
                                                                    <div class=" single-textblock-15min f1 c1">'.$nName.'</div></div>';
                                                                    }elseif($existingappointment->Status==2){
                                                                        echo '<div class="show-open-times color-concluded button small '.$pop.' seven-slot15min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="2">
                                                                    <div class=" single-textblock-15min f1 c1">'.$nName.'</div></div>';
                                                                    }
                                                                    
                                                                }
                                                            }elseif($duration ==30){ 
                                                                if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                                    $bookingvalue = 2;
                                                                    $bookingcount = 1;
                                                                    $remainingslot = 1;
                                                                    $mainslotcounter = $mainslotcounter - 1;
                                                                    if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                        echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.' seven-slot30min-blue" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="1">    
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }elseif($existingappointment->Status==2){
                                                                        echo '<div class="show-open-times color-concluded button small '.$pop.' seven-slot30min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="2">    
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }
                                                                    
                                                                }
                                                            }elseif($duration == 45){ 
                                                                if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                                    $bookingvalue = 3;
                                                                    $bookingcount = 1;
                                                                    $remainingslot = 2;
                                                                    $mainslotcounter = $mainslotcounter - 2;
                                                                    if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                        echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.' seven-slot45min-blue" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="1">    
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }elseif($existingappointment->Status==2){
                                                                        echo '<div class="show-open-times color-concluded button small '.$pop.' seven-slot45min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="2">    
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }
                                                                    
                                                                }
                                                            }elseif($duration == 60){
                                                                if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                                    $bookingvalue = 4;
                                                                    $bookingcount = 1;
                                                                    $mainslotcounter = $mainslotcounter - 3;
                                                                    if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                        echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.' seven-slot60min-blue" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="1">    
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }elseif($existingappointment->Status==2){
                                                                        echo '<div class="show-open-times color-concluded button small '.$pop.' seven-slot60min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="2">    
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }
                                                                    
                                                                }
                                                            }elseif($duration == 75){
                                                                if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                                    $bookingvalue = 5;
                                                                    $bookingcount = 1;
                                                                    $mainslotcounter = $mainslotcounter - 4;
                                                                    if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                        echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.' seven-slot75min-blue" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="1">
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }elseif($existingappointment->Status==2){
                                                                        echo '<div class="show-open-times color-concluded button small '.$pop.' seven-slot75min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="2">
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }
                                                                    
                                                                }
                                                            }elseif($duration == 90){
                                                                if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                                    $bookingvalue = 6;
                                                                    $bookingcount = 1;
                                                                    $mainslotcounter = $mainslotcounter - 5;
                                                                    if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                        echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.' seven-slot90min-blue" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="1">
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }elseif($existingappointment->Status==2){
                                                                        echo '<div class="show-open-times color-concluded button small '.$pop.' seven-slot90min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="2">
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }
                                                                    
                                                                }
                                                            }elseif($duration == 105){
                                                                if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                                    $bookingvalue = 7;
                                                                    $bookingcount = 1;
                                                                    $mainslotcounter = $mainslotcounter - 6;
                                                                    if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                        echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.' seven-slot105min-blue" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="1">
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }elseif($existingappointment->Status==2){
                                                                        echo '<div class="show-open-times color-concluded button small '.$pop.' seven-slot105min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="2">
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }
                                                                    
                                                                }
                                                            }elseif($duration == 120){
                                                                if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                                    $bookingvalue = 8;
                                                                    $bookingcount = 1;
                                                                    $mainslotcounter = $mainslotcounter - 7;
                                                                    if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                        echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.' seven-slot120min-blue" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="1">
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }elseif($existingappointment->Status==2){
                                                                        echo '<div class="show-open-times color-concluded button small '.$pop.' seven-slot120min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="1" opentype="2">
                                                                    <div class="date-textblock1 f1 c1">'.$nName.'</div>
                                                                        <div class="date-textblock2 c2 ">'.$nProcedure.'</div>
                                                                        <div class="date-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                                    }
                                                                    
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                if($bookingvalue == 0){
                                                    if($mainslotcounter == $defualtcounter && String_Helper_Web::GetActiveTime($d,$dayOfWeek)==1){
                                                        echo '<div class="show-open-times seven-slot15min-blue button small pop1 color-available" clinictimeid="'.$availabletime.'" doctorid="'.$loadarray['currentdoctor'].'" id="'.$d.'" bookingdate="'.$dayOfWeek.'" doctortype="1">
                                                        <div class=" single-textblock-15min f1 c1"></div></div>';
                                                    }else{
                                                        echo '<div class="seven-slot15min-blue new-fsc-expired"><div class=" single-textblock-15min f1 c1"></div></div>';
                                                    }
                                                }
                                                
                                                /*if($bookingvalue == 0 && String_Helper_Web::GetActiveTime($d,$dayOfWeek)==1){ 
                                                    echo '<div class="show-open-times seven-slot15min-blue button small pop1" clinictimeid="'.$availabletime.'" doctorid="'.$loadarray['currentdoctor'].'" id="'.$d.'" bookingdate="'.$dayOfWeek.'" doctortype="1"><div class=" single-textblock-15min f1 c1">'.date('h:i A',$d).'</div></div>';
                                                }else{
                                                    echo '<div class="seven-slot15min-blue"><div class=" single-textblock-15min f1 c1">Expiredddd</div></div>';
                                                }*/
                                            }else{
                                                //echo '<div class="seven-slot15min"><div class=" single-textblock-15min f1 c1">a'.date('h:i A',$d).'</div></div>';
                                            }
                                            
                                        }else{
                                            //echo '<div class="seven-slot15min"><div class=" single-textblock-15min f1 c1">b'.date('h:i A',$d).'</div></div>';
                                        }
                                        
                                        }else{
                                            $mainslotcounter = $mainslotcounter +1;
                                            $timevalue = 1;
                                            $roundCount = 1;
                                        }
                                        //$timevalue = 1;
                                        //$roundCount = 1;
                                    }
                                    
                                    
                                }
                            }


                        }
                    }
                }
            }   
        }
        }
        if($timevalue==0){
            //echo '<div class="seven-slot15min-blue"><div class=" single-textblock-15min f1 c1">'.date('h:i A',$d).'</div></div>';
        //}else{
            echo '<div class="seven-slot15min"><div class=" single-textblock-15min f1 c1"></div></div>';
        }
        
         
    }
    
   
    
    
    ?> 
      
      
      
      

  </div><!--END OF APPOINTMENT 60MIN CONTAINER-->
<?php } ?>  
  
  
  
  
  
  
  

<div class="clear"></div>

<!--</div> --> <!--END OF scroll--> 
<!--</div>--><!--END OF CALENDAR DAY--> 


  
 
</div> 
