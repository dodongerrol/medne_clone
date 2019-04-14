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
    $mainEndTime = 0; $i=0;
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
                                $findCurrentAppointment = General_Library::FindTimelyAppointment($loadarray['current_doctor'],$loadarray['currentprocedure'],strtotime($loadarray['currentdate']),$i);  
                                if($findCurrentAppointment){ $activeStartTime = 1; }
                                //if($activeStartTime==0 && $mainactive ==0){
                                //if($activeStartTime==0 && $mainactive ==0 ){
                                    
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
                    <!--<option value="" >End Time</option>-->
    <?php
    if($mainEndTime != 0 && $mainEndTime <= $i){
        echo '<option value="'.$mainEndTime.'" >'.date('h:i A',$mainEndTime).'</option>';
    }

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
                                ///if($loadarray['start_time'] == $newstarttime){
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