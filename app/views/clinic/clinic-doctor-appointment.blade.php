@include('common.header-clinic-section')
  <script type="text/javascript">
    
$(function() {
    $("#datepicker").datepicker({
        dateFormat: "DD, d MM, yy"
    }).datepicker("setDate", "0");  
    $( "#datepicker" ).datepicker({dateFormat: 'DD, d MM, yy',});
  
  
   $("#datepicker2").datepicker({
        dateFormat: "DD, d MM, yy"
    }).datepicker("setDate", "0");  
    $( "#datepicker2" ).datepicker({dateFormat: 'DD, d MM, yy',});
     
  }); 
setInterval(function(){
    UpdateChannelBooking({{time()}},{{$loadarray['currentdoctor']}}); 
}, 15000);
</script>

  <div class="clear"></div>
  <div id="channel-notification-doctor" ></div>
  <div id="clinic-form-container">
    <div class="form-nav "> 
    
    <div class="calander">
    
        <div id="main-calander-selection" singledoctor="1" doctorids="{{$loadarray['currentdoctor']}}"></div>
<!--      <div id="datepicker2"></div>-->
    </div> 
    <!--END CALANDER-->
    <div id="nav-scroll">
      <div class="nav-scroll-content">
          
<a href="{{URL::to('app/clinic/appointment-home-view')}}">      
    <div class="all-doctor">
    <div class="nav-btn">
    
    
<div class="icn-face mar-pic"> <img src="{{ URL::asset('assets/images/dr-img-place-holder.png') }}" width="50" height="50"  alt=""/></div>
        <!--END OF ICN FACE-->
        
        <div class="btn-name2 wd2">
          <div class="btn-name-title2 padding-top">View All Doctors</div>
         
        </div>
        <!--END OF BTN NAME2-->
        
               <div class="clear"></div>
      </div>
    
    </div></a>
    <!--END OF ALLL DOCTOR-->

    
    <?php 
    $activedoctorcount = 0;
    if($loadarray['doctors']){
        //$activedoctorcount = 8;

        foreach($loadarray['doctors'] as $doctor){ 
            if($doctor['available'] ==1){
                $activedoctorcount = $activedoctorcount +1;
                //$doctorchecked = "checked";
            }else{
                //$doctorchecked = '';
            }   
            if($doctor['doctor_id'] ==$loadarray['currentdoctor']){
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
      </div></div>

      
      
      
    </div>



              
               
    <div class="form-nav-page pb2">
      <div class="clinic-home">
       <div id="fullcalendar-container">
           <div id="load-slot-section">
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
?>
  <div class="appointments-seven-day  fl">
   <div class="seven-slot-day g ">
    <div class="day c3">{{$getWeek}}</div>
    <div class="day c3">{{$dayOfWeek}}</div>
    
  </div>
    
    <?php 
    $defaultStart = strtotime($dayOfWeek."06.00 AM");;
    $defaultEnd = strtotime("+1 day",$defaultStart);
    
    $findDoctorAvailability = Array_Helper::DoctorArrayWithCurrentDate($loadarray['currentdoctor'],$findWeek, $dayOfWeek);
    //echo '<pre>'; print_r($findDoctorAvailability); echo '</pre>';
    $findClinicHolidays = General_Library::FilterCurrentClinicHolidays(3,$loadarray['clinicid'], $dayOfWeek);
    $mainslotcounter = 0; $defualtcounter =0;
    for($d=$defaultStart; $d<$defaultEnd; $d = strtotime("+15 minutes", $d)){
        $roundCount = 0; $timevalue=0;
        //if($loadarray['clinic_availability']){
        if($loadarray['clinic_availability']){  
            //echo $loadarray['clinic_availability'][0]->To_Date.'-'.$dayOfWeek;
            if($loadarray['clinic_availability'][0]->Repeat==1 || $loadarray['clinic_availability'][0]->To_Date >= strtotime($dayOfWeek)){
            
            foreach($loadarray['clinic_availability'] as $clinicAvailability){
                if($roundCount ==0){
                    if($clinicAvailability->$findWeek ==1){
                        $startTime = strtotime($dayOfWeek.$clinicAvailability->StartTime);
                        $endTime = strtotime($dayOfWeek.$clinicAvailability->EndTime);

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
                                                    echo '<div class="show-open-times seven-slot15min-blue button small pop1 new-fsc-available" clinictimeid="'.$availabletime.'" doctorid="'.$loadarray['currentdoctor'].'" id="'.$d.'" bookingdate="'.$dayOfWeek.'" doctortype="1">
                                                        <div class=" single-textblock-15min f1 c1"></div></div>';
                                                }else{
                                                    echo '<div class="seven-slot15min-blue new-fsc-expired"><div class=" single-textblock-15min f1 c1">GG</div></div>';
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
  
  

 <!--  <table class="table" border="1">
<tr>
<td>Date</td>
<td>Event Start Time</td>
<td>Event End Time</td>
<td>Summery</td>
<td>Organizer</td>
 -->

<?php
//    $results= $loadarray['googleevents'];
//     foreach ($results->getItems() as $event) { 
//           $start = $event->start->dateTime;   
//            $end = $event->end->dateTime;
         

  
   
   

//             $stimestart = date('H:i:s',strtotime($start));
//             $stimeend = date('H:i:s',strtotime($end));
//             $date=date('Y-m-d',strtotime($start));
//             $organizer=$event->organizer->email;
//             $gevent=$event->getSummary();

//  echo "<tr >";
// echo "<td>" .$date."</td>"; 
// echo "<td>" .$stimestart."</td>"; 
//     echo "<td>" .$stimeend."</td>"; 
//      echo "<td>" .$gevent."</td>"; 
//      echo "<td>" .$organizer."</td>";
 
//  echo "</tr>";
          
            //dd($date);
    // }
    //  echo($stimestart);
    // echo($stimeend);
    // echo($date);
    // echo($gevent);

    // echo(
    //  <span>

    //  Start Time <br>
    //  End Time<br>
    //  Date<br>
    //  Description
     
         
    //  </span>
    //  );
     
     ?> 
 
 <!-- </table> -->
  
  
  
  
  

<div class="clear"></div>

<!--</div> --> <!--END OF scroll--> 
<!--</div>--><!--END OF CALENDAR DAY--> 


  
 
</div> 
        
        
      </div>
      <!--END OF CLINIC HOME--> 
        <div id="popup"  ><!-- Start Popup -->    
            <!--<div id="">Hello here</div>-->
            <div id="ajax-load-bookingpopup">
             </div>
            <!--<a class="close-reveal-modal">&#215;</a>-->
        </div> <!-- End Popup -->
        
      </div><!--End of ajax -->
    </div>
    <!--END OF FORM NAV PAGE-->
    
    <div class="clear"></div>
  </div>
  <!--END OF CLINIC FORM CONTAINER--> 
</div>
<!--END OF MAIN CONTAINER-->

@include('common.footer-clinic-section')
