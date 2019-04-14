<?php 
    $activedoctorcount = 0;
    if($loadarray['doctors']){
        //$activedoctorcount = 1;
        foreach($loadarray['doctors'] as $doctor){ 
            if($doctor['available'] ==1){
                $activedoctorcount = $activedoctorcount +1;
            }
        }
    }
    $activeslot = String_Helper_Web::SlotChanges($activedoctorcount);
?>

<div class="calendar-time">
    <div class="time-title ">TIME</div>
    <?php 
//echo '<pre>'; print_r($loadarray); echo '</pre>';    
    //Time Mark
    
    if($loadarray['clinicavailability']){
        foreach($loadarray['clinicavailability'] as $clinicavailable){ 
            $startTime = strtotime($clinicavailable['starttime']);
            $endTime = strtotime($clinicavailable['endtime']);
            
             for($i=$startTime; $i<$endTime; $i = strtotime("+60 minutes", $i)){
                 //$returnHoliday = String_Helper_Web::HolidayTimeCondition($loadarray['clinicholiday'],$i);
                 $returnHoliday = String_Helper_Web::HolidayTimeConditionStatus($loadarray['clinicholiday'],$i);
                 if($returnHoliday!=1){
            ?>
            <div class="time-count ">{{date('h:ia', $i);}} </div>
       <?php  } }  } }?>
    
    

    
    <div class="clear"></div>
    </div> <!--END OF CALENDAR TIME-->
    
    
    
    <div class="calendar-day " > 
        <?php if($activedoctorcount >7){ ?>    
        <div id="content-8" >
        <div class="scroll-content">
        <?php }?>
        <!--<div id="content-5" class="content horizontal-images light"> Start Scroll view -->

       
    <div class="clear"></div>



    
    
    
<div class="clear"></div>

<!--<div class="appointments-60min-container g fl">
    <?php 
    if($loadarray['clinicavailability']){
        foreach($loadarray['clinicavailability'] as $clinicavailable){ 
            $startTime = strtotime($clinicavailable['starttime']);
            $endTime = strtotime($clinicavailable['endtime']);
            
             //for($i=$startTime; $i<=$endTime; $i = strtotime("+30 minutes", $i)){
             for($i=$startTime; $i<$endTime; $i = strtotime("+30 minutes", $i)){    
                 $returnHoliday = String_Helper_Web::HolidayTimeCondition($loadarray['clinicholiday'],$i);
                 if($returnHoliday!=1){
                     //Find doctor availability 
                     if($loadarray['doctors']){
                         foreach($loadarray['doctors'] as $doctor){
                             
                             if($doctor['doctor_id'] == 89 || $doctor['doctor_id'] == 44){
                             //if($doctor['doctor_id'] == 44){    
                                 if($doctor['available_times']){
                                     $doctortimecount = 0; $activeAvailability = 0;
                                     foreach($doctor['available_times'] as $doctortime){
                                         $doctorstarttime = strtotime($doctortime['starttime']);
                                         $doctorendtime = strtotime($doctortime['endtime']);
                                         $returnDoctorHoliday = String_Helper_Web::HolidayTimeCondition($doctor['holidays'],$i);
                                         if($doctorstarttime <= $i && $doctorendtime > $i && $returnDoctorHoliday !=1){
                                            if($doctortimecount ==0){
                                                $activeAvailability = 1;
                                                $doctortimecount = 1;
                                            }
                                         }
                                     }
                                    if($activeAvailability == 1){
                                        //echo '<div class="appointments-60min-container g fl">';
                                        echo '<div class="appointments-deafult-f ">'.date('g:i a',$i).'</div>';
                                        echo '<div class="appointments-deafult-f border-b ">'.date('h:i',$i).'</div>';
                                        //echo '</div>';
                                    }else{
                                        //echo '<div class="appointments-60min-container g fl">';
                                        echo '<div class="appointments-deafult-f ">in no</div>';
                                        echo '<div class="appointments-deafult-f border-b ">in no</div>';
                                        //echo '</div>';
                                    }
                                 }else{
                                    //echo '<div class="appointments-60min-container g fl">';
                                    echo '<div class="appointments-deafult-f ">out no</div>';
                                    echo '<div class="appointments-deafult-f border-b ">out no</div>';
                                    //echo '</div>';
                                 }
                             }
                             
                         }
                     }
                     
        

            } }  } }?>
   

</div>-->


<!--<div class="appointments-60min-container g fl">-->
    <?php 
    if($activedoctorcount <=6){ $charector = 12; } else { $charector = 8;}
    if(strtotime($loadarray['currentdate'])>=  strtotime(date('d-m-Y'))){
        $loadarrayDate = date('d-m-Y');
    }else{
        $loadarrayDate = $loadarray['currentdate'];
    }
    
    if($loadarray['doctors']){ 
        $roundcount2 =1;
        //$slotsize = String_Helper_Web::SlotChanges($maindoctorcount);
        foreach($loadarray['doctors'] as $doctor){
        //if($doctor['doctor_id'] == 89 || $doctor['doctor_id'] == 44 || $doctor['doctor_id'] == 33){
        if($doctor['available'] == 1 && $roundcount2 <= $activedoctorcount){  
            $roundcount2 = $roundcount2 +1;
        //echo '<div class="appointments-60min-container g fl">';
         echo '<div class="appointments-'.$activeslot.'-slot ">';
            echo '<div class="'.$activeslot.'-slot"><div class="day ">'.substr($doctor['doctor_name'],0,$charector).'</div></div>';
    if($loadarray['clinicavailability']){ $mainslotcounter = 0; $defualtcounter =0;
        foreach($loadarray['clinicavailability'] as $clinicavailable){ 
            $startTime = strtotime($loadarray['currentdate'].$clinicavailable['starttime']);
            $endTime = strtotime($loadarray['currentdate'].$clinicavailable['endtime']);
            
            //$startTime = strtotime($clinicavailable['starttime']);
            //$endTime = strtotime($clinicavailable['endtime']);
             //for($i=$startTime; $i<=$endTime; $i = strtotime("+30 minutes", $i)){
             for($i=$startTime; $i<$endTime; $i = strtotime("+15 minutes", $i)){    
                 //$returnHoliday = String_Helper_Web::HolidayTimeCondition($loadarray['clinicholiday'],$i);
                 $returnHoliday = String_Helper_Web::HolidayTimeConditionStatus($loadarray['clinicholiday'],$i);
                 if($returnHoliday!=1){
                     
                               $mainslotcounter = $mainslotcounter +1;
                               $defualtcounter = $defualtcounter +1;
                               if($mainslotcounter == $defualtcounter){
                                   
                                 if($doctor['available_times']){
                                     $doctortimecount = 0; $activeAvailability = 0; 
                                     foreach($doctor['available_times'] as $doctortime){
                                         $doctorstarttime = strtotime($loadarray['currentdate'].$doctortime['starttime']);
                                         $doctorendtime = strtotime($loadarray['currentdate'].$doctortime['endtime']);
                                         //$doctorstarttime = strtotime($doctortime['starttime']);
                                         //$doctorendtime = strtotime($doctortime['endtime']);
                                         $returnDoctorHoliday = String_Helper_Web::HolidayTimeCondition($doctor['holidays'],$i);
                                         if($doctorstarttime <= $i && $doctorendtime > $i && $returnDoctorHoliday !=1){
                                            if($doctortimecount ==0){
                                                $activeAvailability = 1;
                                                $doctortimecount = 1;
                                                $availabletime = $doctortime['clinictimeid'];
                                            }
                                         }
                                     }
                                    if($activeAvailability == 1){
                                        $bookingvalue = 0; $bookingcount = 0; $remainingslot = 0;
                                        if($doctor['existingappointments']){                                          
                                            foreach($doctor['existingappointments'] as $existingappointment){
                                                // nhr change style for popupe
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
                                                    //$getvalue = $existingappointment->EndTime - $existingappointment->StartTime;
                                                    //$timeDiff = abs($getvalue)/60;
                                                    $duration = $existingappointment->Duration;
                                                    $endduration = strtotime("+".$duration." minutes", $i);
                                                    if($duration ==15){
                                                        //echo '<pre>'; print_r($existingappointment); echo '</pre>';
                                                        //echo $existingappointment->StartTime.'=='.$i.'<br>';
                                                        if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                            $bookingvalue = 1;
                                                            $bookingcount = 1;
                                                            if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.$activeslot.'-slot15min-blue" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="1">
                                                                    <div class="'.$activeslot.'-textblock-15min f1 c1">'.$nName.'</div></div>';
                                                            }elseif($existingappointment->Status==2){
                                                                echo '<div class="show-open-times color-concluded button small '.$pop.$activeslot.'-slot15min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="2">
                                                                    <div class="'.$activeslot.'-textblock-15min f1 c1">'.$nName.'</div></div>';
                                                            }
                                                            
                                                        }
                                                    }elseif($duration ==30){ 
                                                        if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                            $bookingvalue = 2;
                                                            $bookingcount = 1;
                                                            $remainingslot = 1;
                                                            $mainslotcounter = $mainslotcounter - 1;
                                                            if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.$activeslot.'-slot30min-blue" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="1">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }elseif($existingappointment->Status==2){
                                                                echo '<div class="show-open-times color-concluded button small '.$pop.$activeslot.'-slot30min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="2">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }  
                                                        }
                                                    }elseif($duration == 45){ 
                                                        if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                            $bookingvalue = 3;
                                                            $bookingcount = 1;
                                                            $remainingslot = 2;
                                                            $mainslotcounter = $mainslotcounter - 2;
                                                            if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.$activeslot.'-slot45min-blue" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="1">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }elseif($existingappointment->Status==2){
                                                                echo '<div class="show-open-times color-concluded button small '.$pop.$activeslot.'-slot45min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="2">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }  
                                                        }
                                                    }elseif($duration == 60){
                                                        if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                            $bookingvalue = 4;
                                                            $bookingcount = 1;
                                                            $mainslotcounter = $mainslotcounter - 3;
                                                            if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                echo '<div class="show-open-times button '.$findColorClass.' small '.$pop.$activeslot.'-slot60min-blue " bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="1">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }elseif($existingappointment->Status==2){
                                                                echo '<div class="show-open-times color-concluded button small '.$pop.$activeslot.'-slot60min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="2">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }
                                                            
                                                            
                                                        }
                                                    }elseif($duration == 75){
                                                        if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                            $bookingvalue = 5;
                                                            $bookingcount = 1;
                                                            $mainslotcounter = $mainslotcounter - 4;
                                                            if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.$activeslot.'-slot75min-blue " bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="1">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }elseif($existingappointment->Status==2){
                                                                echo '<div class="show-open-times color-concluded button small '.$pop.$activeslot.'-slot75min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="2">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }
                                                            
                                                        }
                                                    }elseif($duration == 90){
                                                        if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                            $bookingvalue = 6;
                                                            $bookingcount = 1;
                                                            $mainslotcounter = $mainslotcounter - 5;
                                                            if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.$activeslot.'-slot90min-blue " bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="1">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }elseif($existingappointment->Status==2){
                                                                echo '<div class="show-open-times color-concluded button small '.$pop.$activeslot.'-slot90min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="2">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }
                                                            
                                                        }
                                                    }elseif($duration == 105){
                                                        if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                            $bookingvalue = 7;
                                                            $bookingcount = 1;
                                                            $mainslotcounter = $mainslotcounter - 6;
                                                            if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.$activeslot.'-slot105min-blue " bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="1">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }elseif($existingappointment->Status==2){
                                                                echo '<div class="show-open-times color-concluded button small '.$pop.$activeslot.'-slot105min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="2">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }
                                                            
                                                        }
                                                    }elseif($duration == 120){
                                                        if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                            $bookingvalue = 8;
                                                            $bookingcount = 1;
                                                            $mainslotcounter = $mainslotcounter - 7;
                                                            if($existingappointment->Status==0 || $existingappointment->Status==1){
                                                                echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.$activeslot.'-slot120min-blue " bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="1">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }elseif($existingappointment->Status==2){
                                                                echo '<div class="show-open-times color-concluded button small '.$pop.$activeslot.'-slot120min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="2">
                                                                    <div class="'.$activeslot.'-textblock f1 c1">'.$nName.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c2 ">'.$nProcedure.'</div>
                                                                    <div class="'.$activeslot.'-textblock2 c1 ">'.$existingappointment->PhoneNo.'</div></div>';
                                                            }
                                                            
                                                        }
                                                    }
                                                    
                                                } 
                                            }
                                        }
                                        //if($mainslotcounter == $defualtcounter){
                                        if($bookingvalue == 0){
                                        if($mainslotcounter == $defualtcounter && String_Helper_Web::GetActiveTime($i,$loadarray['currentdate'])==1){ ?>
                                        <div doctortype="3"  class="show-open-times {{$activeslot}}-slot15min-blue button small pop1 color-available"  clinictimeid="{{$availabletime}}" doctorid="{{$doctor['doctor_id']}}" id="{{$i}}" bookingdate="{{$loadarray['currentdate']}}">
                                        <div class="{{$activeslot}}-textblock-15min f1 c1 "> </div></div>    
                                        <!--<div class="button small pop1" data-bpopup='{"follow":[false,false],"position":[30%,400]}'>-->
                                        <?php            
                                        }else{
                                            echo '<div class="'.$activeslot.'-slot15min-blue new-fsc-expired"><div class=" '.$activeslot.'-textblock-15min f1 c1"></div></div>';
                                        }}
                                    }else{
                                        echo '<div class="'.$activeslot.'-slot15min"><div class=" '.$activeslot.'-textblock-15min f1 c1"></div></div>';
                                        //echo '<div class="single-slot15min-gray"><div class=" single-textblock-15min f1 c1">un</div></div>';
                                    }
                                 }else{
                                        echo '<div class="'.$activeslot.'-slot15min"><div class=" '.$activeslot.'-textblock-15min f1 c1"></div></div>';
                                        //echo '<div class="single-slot15min-gray"><div class=" single-textblock-15min f1 c1">un</div></div>';
                                 }

                                 
                                 }else{
                                    $mainslotcounter = $mainslotcounter +1;
                                }
            }
            
                                 
            
            }  } } //echo 'slot counter -'.$mainslotcounter;
            echo '</div>';
            } }  }else{
                if(count($loadarray['doctors'])==0){
                    echo '<div class="clinic-empty-state">
                    <div class="info">Doctor view is not available <br> Please Click Here to add your first doctor</div>
                    <a href="'.URL::to('app/clinic/clinic-doctor').'"><div class="btn-add font-type-Montserrat">Add a Doctor</div></a>
                    </div>';
                }
            }
?>
   

<!--</div>-->






<?php if($activedoctorcount >7){ ?>  
     </div></div>
<?php } ?>


<!--    </div>  Calander Scroll -->
</div><!--END OF CALENDAR DAY--> 
