<span class="button b-close"><span>X</span></span>


<form id="form-clinic-booking" method="POST">        
    <div class="pop-title">NEW APPOINTMENT </div>
    <div class="clear"></div>
    <div class="wdview10 portfolio-field-section new-mal3 mar-control new-mart4  wdview7 ">
      
    <div id="load-popup-booking-ajax"> <!--Start LOAD POPUP AJAX -->    
        
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
                        if($loadarray['current_procedure'] == $doctorProcedure->ProcedureID){
                            echo '<option duration="'.$duration.'" durformat="'.$doctorProcedure->Duration_Format.'" value="'.$doctorProcedure->ProcedureID.'" selected="selected">'.$doctorProcedure->Name.'</option>'; 
                        }else{
                            echo '<option duration="'.$duration.'" durformat="'.$doctorProcedure->Duration_Format.'" value="'.$doctorProcedure->ProcedureID.'">'.$doctorProcedure->Name.'</option>'; 
                        }
                        
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
                <textarea id="remarks" name="remarks" cols="50" rows="4">{{$loadarray['userremarks']}}</textarea>
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
    //echo '<pre>'; print_r($loadarray['clinicavailability']); echo '</pre>';
    StringHelper::Set_Default_Timezone();
    if($loadarray['clinicavailability']){
        //if($loadarray['slot_place']=='b'){
        ///    $mystarttime = strtotime("+15 minutes", strtotime($loadarray['start_time']));
        //}else{
         //   $mystarttime = strtotime($loadarray['start_time']);
       // }
        foreach($loadarray['clinicavailability'] as $clinicavailable){ 
            $startTime = strtotime($loadarray['currentdate'].$clinicavailable['starttime']);
            $endTime = strtotime($loadarray['currentdate'].$clinicavailable['endtime']);

            //$startTime = strtotime($clinicavailable['starttime']);
            //$endTime = strtotime($clinicavailable['endtime']);
            
            for($i=$startTime; $i<$endTime; $i = strtotime("+15 minutes", $i)){    
                $slot ='a';
                //if($slot =='a'){$slot ='b';}else{$slot='a';}
                 $returnHoliday = String_Helper_Web::HolidayTimeCondition($loadarray['clinicholiday'],$i);
                 if($returnHoliday!=1){
                        if($loadarray['doctoravailability']['available_times']){
                             $doctortimecount = 0; $activeAvailability = 0; 
                             foreach($loadarray['doctoravailability']['available_times'] as $doctortime){
                                $doctorstarttime = strtotime($loadarray['currentdate'].$doctortime['starttime']);
                                $doctorendtime = strtotime($loadarray['currentdate'].$doctortime['endtime']);
            
                                //$doctorstarttime = strtotime($doctortime['starttime']);
                                //$doctorendtime = strtotime($doctortime['endtime']);
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
                                if($loadarray['start_time'] == $i){
                                //if($mystarttime == $i){    
                                    echo '<option value="'.$i.'" selected="selected">'.$newstarttime.'</option>';
                                }else{
                                    echo '<option value="'.$i.'" >'.$newstarttime.'</option>';
                                }
                            }else{
                                if($activeAvailability == 1 && $loadarray['start_time'] == $i){
                                    echo '<option value="'.$i.'" selected="selected">'.date("h.i A", $loadarray['start_time']).'</option>';
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
                
                <!--<input id="start-time" name="start_time" type="text" class="time input-xsmall-d fl" value="{{$loadarray['start_time'] }}"   data-time-format="h:i A"/>-->
<!--                <div class="select-box-v2b fl"> 
                    <select>
                    <?php if($loadarray['start_time']=="AM"){
                        echo '<option value="AM">AM</option>';
                    }else{
                        echo '<option value="PM">PM</option>';
                    }?>
                    
                    <option>PM</option>
                    </select>
                </div>-->

<!--                <script>
                    $(function() {
                        $('#start-time').timepicker();
                    });
                </script>        -->
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
    $newstarttime = date("h.i A", $loadarray['end_time']);
    echo '<option value="'.$loadarray['end_time'].'" selected="selected">'.$newstarttime.'</option>';

    /*
    if($loadarray['clinicavailability']){
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
                            //if($activeAvailability == 1        
                                $newstarttime = date('h:i A',$i);
                                if($loadarray['end_time'] == $i){
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
    </div><!-- end of Load Popup ajax -->  
       
       
       
      <div class="field-container new-mart">
         
         	<div class="field-name">
         		<label>NRIC OR FIN</label>
         			</div><!--END OF FIELD NAME--> 
         	<div class="field-type">
                    <input id="nric" name="nric" class="" type="text" value="{{$loadarray['usernric']}}">
                <!--<input type="text">-->
                </div><!--END OF FIELD TYPE-->
                    
       	</div><!--END OF FIELD CONTAINER-->
        <!--NOTE: UNCOMMENT THE DEFAULT INPUT FOR THE NORMAL STATE 
        THE CLASS INPUT-TICK HAS THE GREEN TICK FOR VALIDATION PURPOSE -->
        
        
        
        <div class="field-container">
         
         	<div class="field-name">
         		<label>PATIENT NAME</label>
         			</div><!--END OF FIELD NAME--> 
         	<div class="field-type">
                    <input id="name" name="name" type="text" value="{{$loadarray['username']}}">
         			</div><!--END OF FIELD TYPE-->
                    
       	</div><!--END OF FIELD CONTAINER-->
        
      
      
          <div class="clear"></div>
         
        <div class="field-container">
        <div class="field-container-xsmall">
            <div class="field-name">
              <label>Phone</label>
            </div><!--END OF FIELD NAME--> 
            <div class="field-type">
                <input id="code" name="code" type="text" value="{{$loadarray['usercode']}}" class="input-xsmall">
             </div><!--END OF FIELD TYPE-->
             <div class="field-name">
              <label>Code</label>
            </div><!--END OF FIELD NAME-->     
         </div><!--END OF FIELD CONTAINER SMALL-->
         
         
        <div class="field-container-medium new-mart5">
            <div class="field-type">
                <input id="phone" name="phone" type="text" class="input-medium" value="{{$loadarray['userphone']}}">
               		</div><!--END OF FIELD TYPE-->
               
            <div class="field-name new-mart2">
                <label>Phone No</label>
            </div><!--END OF FIELD NAME--> 
               
        </div><!--END OF FIELD CONTAINER SMALL-->
        </div><!--END OF FIELD CONTAINER-->
        
        <div class="clear"></div>
        
        <div class="field-container">
         
            <div class="field-name">
                <label>EMAIL</label>
                </div><!--END OF FIELD NAME--> 
            <div class="field-type">
                <input id="email" name="email" type="text" value="{{$loadarray['useremail']}}">
            </div><!--END OF FIELD TYPE-->          
       	</div><!--END OF FIELD CONTAINER-->
        
       
        <div class="field-container"> 
            <div id="new-appointment" bookingid="{{$loadarray['bookingid']}}" clinictimeid="{{$loadarray['clinictimeid']}}" class="btn-update font-type-Montserrat">UPDATE NOW</div> 
            <div id="conclude-appointment" bookingid="{{$loadarray['bookingid']}}" class="btn-concluded font-type-Montserrat mar-left-2">CONCLUDED </div> 
            <div id="cancel-appointment" bookingid="{{$loadarray['bookingid']}}" class="btn-cancel font-type-Montserrat mar-left-2">CANCEL</div> 
            <div id="process-loading" ></div>
        </div><!--END OF FIELD CONTAINER-->
      <div class="clear"></div>
    
    </div>          
    </form>   
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
	
	
//	$("#booking-datepick").datepicker({
//        dateFormat: "DD, d MM, yy"
//    }).datepicker("setDate", "0");	
//    $( "#booking-datepick" ).datepicker({dateFormat: 'DD, d MM, yy',});
    
    $("#booking-date").datepicker({
       // dateFormat: "DD, d MM, yy"
        dateFormat: "dd-mm-yy"
    });
    //}).datepicker("setDate", "0");
   
  });
</script> 
