@include('common.header-clinic-section')
<!--END HEADER-->

 <script type="text/javascript">     

$(function() {
    $("#datepicker").datepicker({
        dateFormat: "DD, d MM, yy"
    }).datepicker("setDate", "0");  
    $( "#datepicker" ).datepicker({dateFormat: 'DD, d MM, yy',});
  
  
    $("#main-calander-selection").datepicker({
        dateFormat: "DD, d MM, yy"
    }).datepicker("setDate", "0");  
    $( "#main-calander-selection" ).datepicker({dateFormat: 'DD, d MM, yy',});
  
  
    $("#booking-datepick").datepicker({
        dateFormat: "DD, d MM, yy"
    }).datepicker("setDate", "0");  
    //$( "#booking-datepick" ).datepicker({dateFormat: 'DD, d MM, yy',});
   
  });
  setInterval(function(){
    UpdateChannelBooking({{time()}}); 
}, 15000);
</script> 
  <div class="clear"></div>
  <div id="channel-notification" ></div>
  <?php //echo '<pre>';print_r($loadarray);echo '</pre>'; ?>
  <div id="clinic-form-container">
    <div class="form-nav "> 
    
    <div class="calander">
    
      <div id="main-calander-selection"></div>
    </div> 
    <!--END CALANDER-->
    
<a href="{{URL::to('app/clinic/appointment-home-view')}}">     
    <div class="all-doctor">
    <div class="nav-btn">
    
   <div class="new-fl new-mal5 new-mart3" >
      <input  id="" type="checkbox" name="checkbox" value="1" ><label  for="checkbox1"><span></span></label>
    </div>
<div class="icn-face new-marr6 new-mal4"> <img src="{{ URL::asset('assets/images/dr-img-place-holder.png') }}" width="50" height="50"  alt=""/></div>
        <!--END OF ICN FACE-->  
        <div class="btn-name2 wd2">
            <div class="btn-name-title2 padding-top">View All Doctors</div>
        </div>
        <!--END OF BTN NAME2-->
        
               <div class="clear"></div>
      </div>
    
    </div></a>
    <!--END OF ALLL DOCTOR-->
<div id="nav-scroll">
      <div class="nav-scroll-content">
<?php 
    $activedoctorcount = 0;
    if($loadarray['doctors']){
        //$activedoctorcount = 8;
        foreach($loadarray['doctors'] as $doctor){ 
            if($doctor['available'] ==1){
                $activedoctorcount = $activedoctorcount +1;
                $doctorchecked = "checked";
            }else{
                $doctorchecked = '';
            }
            if(empty($doctor['doctor_image'])){
                $docimage = URL::asset('assets/images/no-doctor.png');
            }else{
                $docimage = $doctor['doctor_image'];
            }
            ?>
               
            <div class="nav-btn"> 
            <div class="fl check-space" >
              <!--<input id="" type="checkbox" name="checkbox" value="1" ><label for="checkbox1"><span></span></label>-->
                <input class="create-doctor-list" id="{{$doctor['doctor_id']}}" type="checkbox" name="checkbox" value="{{$doctor['doctor_id']}}" {{$doctorchecked}}>
              <label for="{{$doctor['doctor_id']}}"><span></span></label>
            </div>
             
            <a href="{{URL::to('app/clinic/appointment-doctor-view/'.$doctor['doctor_id'])}}">    
              <div class="icn-face"> <img src=" {{$docimage}}" width="50" height="50"  alt=""/> </div>
              <!--END OF ICN FACE-->

            <div class="btn-name3">
                <div class="btn-name-title3">{{substr($doctor['doctor_name'],0,20) }} </div>
                <div class="btn-name-sub3">{{substr($doctor['specialty'],0,25) }} </div>
            </div>
              <!--END OF BTN NAME2-->
              <?php
                   $syncimage= URL::asset('assets/images/synced.png');
                   $notsyncimage= URL::asset('assets/images/notsynced.png');
                   $doc = new Doctor();
                   $data = $doc->FindDoctor($doctor['doctor_id']);
                   $token = $data->token;
                   if (!is_null($token)) {
                      $gcimg = 'google_calicon.svg';
                      $gcimg = '<img class="google-img" style="float:right;" src="'.URL::asset('assets/images/'.$gcimg).'" width="15" height="15" title="Google Synced"  alt=""/>';
                    }
                    else
                    {
                      $gcimg = '';
                    }
                  ?>  
            <?php ($doctor['available']==1)? $openimg = 'icn-dot-green.png' : $openimg = 'icn-dot-red.png';?>
              <div class="icn-dot">
              <img  src="{{ URL::asset('assets/images/'.$openimg) }}" width="15" height="15"  alt=""/>
              <?php echo $gcimg ?>
              </div>
              
              <div class="clear"></div>
             </a>
                
            </div>
            <!--END OF NAV BTN--> 

             
<?php   }
}else{
    echo 'No Doctors Found';
}
$activeslot = String_Helper_Web::SlotChanges($activedoctorcount);
?>
      </div></div><!--End of Scroll view-->
      
      
    </div>

     
    
    <div class="form-nav-page pb2">
        <!--<div id="load-ajax-doctor-lists"> Start ajax - full load-->
      <div class="clinic-home">
       <div id="fullcalendar-container" >
           <div id="load-slot-section">
  <div class="calendar-time">
    <div class="time-title ">TIME</div>
    <?php 
    
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

<!--<div class="appointments-60min-container g fl">-->
    <?php 
    if($activedoctorcount <=6){ $charector = 12; } else { $charector = 8;}
    if($loadarray['doctors']){ 
        $roundcount2 =1; 
        //$slotsize = String_Helper_Web::SlotChanges($maindoctorcount);
        foreach($loadarray['doctors'] as $doctor){
        //if($doctor['doctor_id'] == 89 || $doctor['doctor_id'] == 44 || $doctor['doctor_id'] == 33){
        if($doctor['available'] == 1 && $roundcount2 <= $activedoctorcount){    
            $roundcount2 = $roundcount2 +1;
        //echo '<div class="appointments-60min-container g fl">';
         echo '<div class="appointments-'.$activeslot.'-slot ">';
            //echo '<div id="title-t" class="'.$activeslot.'-slot15min"><div class=" '.$activeslot.'-textblock-15min f1 c1">Rizvi</div></div>';
         echo '<div class="'.$activeslot.'-slot"><div class="day ">'.substr($doctor['doctor_name'],0,$charector).'</div></div>';
        
    if($loadarray['clinicavailability']){ $mainslotcounter = 0; $defualtcounter =0;
        foreach($loadarray['clinicavailability'] as $clinicavailable){ 
            $startTime = strtotime($clinicavailable['starttime']);
            $endTime = strtotime($clinicavailable['endtime']);
            
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
                                         $doctorstarttime = strtotime($doctortime['starttime']);
                                         $doctorendtime = strtotime($doctortime['endtime']);
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
                                                        if($existingappointment->StartTime == $i && $endduration == $existingappointment->EndTime ){
                                                            $bookingvalue = 1;
                                                            $bookingcount = 1;
                                                            if($existingappointment->Status==0 || $existingappointment->Status==1){

                                                                echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.$activeslot.'-slot15min-blue" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="1" id="'.$i.'" bookingdate="'.$existingappointment->BookDate.'">
                                                                    <div class="'.$activeslot.'-textblock-15min f1 c1">'.$nName.'</div></div>';
                                                            }elseif($existingappointment->Status==2){
                                                                echo '<div class="show-open-times color-concluded button small  '.$pop.$activeslot.'-slot15min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="2" id="'.$i.'" bookingdate="'.$existingappointment->BookDate.'">

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
                                                                echo '<div class="show-open-times color-concluded button small'.$pop.$activeslot.'-slot30min" bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="2">
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
                                                                echo '<div class="show-open-times '.$findColorClass.' button small '.$pop.$activeslot.'-slot60min-blue " bookingid="'.$existingappointment->UserAppoinmentID.'" doctortype="3" opentype="1">
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
                                        if($bookingvalue == 0){
                                        if($mainslotcounter == $defualtcounter && String_Helper_Web::GetActiveTime($i,$loadarray['currentdate'])==1){ ?>
                                        <div doctortype="3"  class="show-open-times {{$activeslot}}-slot15min-blue button small pop1 color-available"  clinictimeid="{{$availabletime}}" doctorid="{{$doctor['doctor_id']}}" id="{{$i}}" bookingdate="{{date('d-m-Y')}}">
                                        <div class="{{$activeslot}}-textblock-15min f1 c1 "></div></div>    
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



    <!--</div>  Calander Scroll -->
</div><!--END OF CALENDAR DAY--> 

       </div>
 
</div> 
              
      </div>
      <!--END OF CLINIC HOME--> 
      
      <!-- Start pop up -->
<!--<div id="myModal" class="reveal-modal" >-->
<div id="popup"  >    
    <!--<div id="">Hello here</div>-->
    <div id="ajax-load-bookingpopup">
     </div>
    <!--<a class="close-reveal-modal">&#215;</a>-->
</div>
<!-- End pop up -->
      
    <!--</div> End of full ajax load -->
    </div>
    <!--END OF FORM NAV PAGE-->
    
    <div class="clear"></div>
  </div>
  <!--END OF CLINIC FORM CONTAINER--> 
  

</div>
<!--END OF MAIN CONTAINER-->

<script>
//    (function($){
//        $(window).load(function(){
//            $("#content-5").mCustomScrollbar({
//                axis:"x",
//                theme:"dark-thin",
//                autoExpandScrollbar:true,
//                advanced:{autoExpandHorizontalScroll:true}
//            });     
//        });
//    })(jQuery);
</script>


@include('common.footer-clinic-section')
