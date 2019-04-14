<div class="field-container">
    <div class="field-name">
    <label>DOCTOR</label>
    </div><!--END OF FIELD NAME-->
    <div class="clear"></div> 
    <div class="field-type">
        <div class="select-box-v2a fl" > 
            <select id="doctors-select" name="doctors_select">
            <option value="">Select a Doctor</option>
        <?php 
        $docspeciality = null;
        if($loadarray['doctors']){
            foreach($loadarray['doctors'] as $doctor){
                if($loadarray['current_doctor']== $doctor['doctor_id']){
                    $docspeciality = $doctor['specialty'];
                    echo '<option clinicid="'.$doctor['clinic_id'].'" value="'.$doctor['doctor_id'].'" selected="selected">'.$doctor['doctor_name'].'</option>';
                }else{
                    echo '<option clinicid="'.$doctor['clinic_id'].'" value="'.$doctor['doctor_id'].'">'.$doctor['doctor_name'].'</option>';
                } 
            }
        }?>
        </select>
        </div>
        <label class="fl font-type-oxygen S3 mar-t6 mar-left-2 l-gray">{{$docspeciality}} </label>
    <div class="clear"></div>
    </div><!--END OF FIELD TYPE-->
</div>

<div class="field-container">
            <div class="field-name">
            <label>PROCEDURE</label>
            </div><!--END OF FIELD NAME-->
            <div class="clear"></div> 
         	<div class="field-type">
                <div class="select-box-v2a fl" id="load-doctor-procedures"> 
                    <select id="doctor-procedures" name="doctor_procedure">
                    <option value="" >Select a Procedure</option>
                    <?php if($loadarray['doctor_procedure']){
                    foreach($loadarray['doctor_procedure'] as $doctorProcedure){
                        $duration = $doctorProcedure->Duration;
                        echo '<option duration="'.$duration.'" durformat="'.$doctorProcedure->Duration_Format.'" value="'.$doctorProcedure->ProcedureID.'">'.$doctorProcedure->Name.'</option>'; 
                    }
                }?>
                </select>
            </div>
                    <label class="fl font-type-oxygen S3 mar-t6 mar-left-2 l-gray" id="procedure-time"></label>
                <div class="clear"></div>
            </div><!--END OF FIELD TYPE-->
        </div>

<div class="clear"></div>
         
        <div class="field-container">
            <div class="field-name">
             	<label>REMARKS</label>
            </div><!--END OF FIELD NAME--> 
             
            <div class="field-type">
                <textarea id="remarks" name="remarks" cols="50" rows="4"></textarea>
            </div><!--END OF FIELD TYPE-->
     	</div><!--END OF FIELD CONTAINER-->  
         
        <div class="field-container">
         <div class="field-name">
         <label>DATE</label>
         </div><!--END OF FIELD NAME--> 
         <div id="popdate" class="field-type">
             <input id="booking-date" name="booking_date" class="input-date" type="text" id="booking-datepick" value="{{$loadarray['currentdate']}}">
         </div><!--END OF FIELD TYPE-->
       </div><!--END OF FIELD CONTAINER-->
       
<div id="load-startend-time-ajax"><!-- Start load startend time ajax-->       
<div class="field-container">
        <div class="field-container-small wdview8 new-wd"> 
            <div class="field-type">
                 <div class="field-name">
                  <label>START</label>
                </div><!--END OF FIELD NAME--> 
                <div class="select-box-v2c fl">
                    <select id="start-time"   name="start_time">
                    <option value="" >Start Time</option>
    <?php
    $mainEndTime = 0;
    //echo '<pre>'; print_r($loadarray['clinicavailability']); echo '</pre>';
    StringHelper::Set_Default_Timezone();
    if($loadarray['clinicavailability']){
        //if($loadarray['slot_place']=='b'){
        ///    $mystarttime = strtotime("+15 minutes", strtotime($loadarray['start_time']));
        //}else{
         //   $mystarttime = strtotime($loadarray['start_time']);
       // }
        $activeStartTime = 0; $mainactive=0;
        foreach($loadarray['clinicavailability'] as $clinicavailable){ 
            $startTime = strtotime($clinicavailable['starttime']);
            $endTime = strtotime($clinicavailable['endtime']);
            
            for($i=$startTime; $i<$endTime; $i = strtotime("+15 minutes", $i)){    
                $slot ='a';
                //if($slot =='a'){$slot ='b';}else{$slot='a';}
                 $returnHoliday = String_Helper_Web::HolidayTimeCondition($loadarray['clinicholiday'],$i);
                 if($returnHoliday!=1){
                        if($loadarray['doctoravailability']['available_times']){
                             $doctortimecount = 0; $activeAvailability = 0; 
                             foreach($loadarray['doctoravailability']['available_times'] as $doctortime){
                                 $doctorstarttime = strtotime($doctortime['starttime']);
                                 $doctorendtime = strtotime($doctortime['endtime']);
                                 $returnDoctorHoliday = String_Helper_Web::HolidayTimeCondition($loadarray['doctoravailability']['holidays'],$i);
                                 if($doctorstarttime <= $i && $doctorendtime > $i && $returnDoctorHoliday !=1){
                                    if($doctortimecount ==0){
                                        $activeAvailability = 1;
                                        $doctortimecount = 1;
                                        $availabletime = $doctortime['clinictimeid'];
                                    }
                                 }
                             }
                            if($activeAvailability == 1 && String_Helper_Web::GetActiveTime($i,$loadarray['currentdate'])==1){
                            //if($activeAvailability == 1){    
                                $newstarttime = date("h.i A", $i);
                                //To find start date
                                $findCurrentAppointment = General_Library::FindTimelyAppointment($loadarray['current_doctor'],$loadarray['currentprocedure'],strtotime($loadarray['currentdate']),$i);  
                                if($findCurrentAppointment){ $activeStartTime = 1; }
                                //if($activeStartTime==0 && $mainactive ==0){
                                //if(strtotime($loadarray['start_time']) == $i){
                                //if($mystarttime == $i){   
                                   
                                if($loadarray['start_time'] == $i && $mainactive ==0){      
                                    echo '<option value="'.$i.'" selected="selected">'.$newstarttime.'</option>';
                                    $activeStartTime = 1;
                                    $mainactive = 1;
                                    $mainEndTime = String_Helper_Web::FindEndTime($i,$loadarray['currentprocedureduration']);
                                }else{
                                    if($mainactive==0){
                                        $activeStartTime = 0;
                                    }
                                    echo '<option value="'.$i.'" >'.$newstarttime.'</option>';
                                }
                            }
                        }
                     
                 }
            }
        }
    }


    ?>
                </select>
                </div>
                



            </div><!--END OF FIELD TYPE-->    
         </div><!--END OF FIELD CONTAINER SMALL-->
         
         
         <div class="field-container-small wdview8 new-wd">
           <div class="field-name">
             <label>END</label>
           </div><!--END OF FIELD NAME--> 
              <div class="field-type">
                  <div class="select-box-v2c fl">
                  <select id="end-time" name="end_time">
<!--                    <option value="" >End Time</option>-->
    <?php
    if($mainEndTime != 0){
        echo '<option value="'.$mainEndTime.'" >'.date('h:i A',$mainEndTime).'</option>';
    }
    //echo '<pre>'; print_r($loadarray['clinicavailability']); echo '</pre>';

    /*if($loadarray['clinicavailability']){
        foreach($loadarray['clinicavailability'] as $clinicavailable){ 
            $startTime = strtotime($clinicavailable['starttime']);
            $endTime = strtotime($clinicavailable['endtime']);
            
            for($i=$startTime; $i<=$endTime; $i = strtotime("+15 minutes", $i)){    
                 $returnHoliday = String_Helper_Web::HolidayTimeCondition($loadarray['clinicholiday'],$i);
                 if($returnHoliday!=1){
                        if($loadarray['doctoravailability']['available_times']){
                             $doctortimecount = 0; $activeAvailability = 0; 
                             foreach($loadarray['doctoravailability']['available_times'] as $doctortime){
                                 $doctorstarttime = strtotime($doctortime['starttime']);
                                 $doctorendtime = strtotime($doctortime['endtime']);
                                 $returnDoctorHoliday = String_Helper_Web::HolidayTimeCondition($loadarray['doctoravailability']['holidays'],$i);
                                 if($doctorstarttime <= $i && $doctorendtime >= $i && $returnDoctorHoliday !=1){
                                    if($doctortimecount ==0){
                                        $activeAvailability = 1;
                                        $doctortimecount = 1;
                                        $availabletime = $doctortime['clinictimeid'];
                                    }
                                 }
                             }
                            if($activeAvailability == 1 && String_Helper_Web::GetActiveTime($i,$loadarray['currentdate'])==1){
                            //if($activeAvailability == 1){    
                                $newstarttime = date('h:i A',$i);
                                //if($loadarray['start_time'] == $newstarttime){
                                if($mainEndTime == $i){    
                                    echo '<option value="'.$i.'" selected="selected">'.$newstarttime.'</option>';
                                }else{
                                    echo '<option value="'.$i.'" >'.$newstarttime.'</option>';
                                }
                            }
                        }
                     
                 }
            }
        }
    }*/


    ?>
                </select>
                  </div>
                  <!--<input id="end-time" name="end_time" type="text" class="time input-xsmall-d fl" data-time-format="h:i A" />-->
<!--               <div class="select-box-v2b fl"> 
                  <select>
                  <option>AM</option>
                  <option>PM</option>
                  </select>
                </div>-->
              
<!--                  <script>
                $(function() {
                    $('#end-time').timepicker();
                });
            </script>-->
                 
               </div><!--END OF FIELD TYPE-->
               
               
         </div><!--END OF FIELD CONTAINER SMALL-->
         <div class="clear"></div>
        </div>
    </div> <!--End load startend time ajax -->
    <script type="text/javascript">		 
$(function() {
    $("#booking-date").datepicker({
       // dateFormat: "DD, d MM, yy"
        dateFormat: "dd-mm-yy"
    });
  });
</script> 