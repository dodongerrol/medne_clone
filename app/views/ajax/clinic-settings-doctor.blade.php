<div class="mc-dr-profile-detail"><!--DOCTOR PROFILE DETAILS-->
        <div class="mc-dr-profile-image"><img src="<?php echo $image;?>" width="125" height="125" alt="img-profile" longdesc="images/mc-profile-img.png"></div>
       
        <div class="mc-profile-form"><!--PROFILE FORM-->
        <form action="" id="form-doctor" method="get">
        <fieldset>
          <div class="mc-form-spacer">
              <input id="doc-name" name="Name" value="<?php echo $name;?>" type="text" placeholder="Name">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
               <input id="doc-qualification" name="Qualifications" value="<?php echo $qualifications;?>" type="text" placeholder="Qualifications">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
               <input id="doc-speciality" name="Specialty" value="<?php echo $specialty;?>" type="text" placeholder="Specialty">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
               <input id="doc-mobile" name="Mobile" value="<?php echo $mobile;?>" type="text" placeholder="Mobile">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
               <input id="doc-phone" class="inputshort " name="" value="<?php echo $emergency;?>" type="text" placeholder="Phone"> <label class=" mc-label mc-fl" >Emergency</label>
          <div class="mc-clear"></div>
          <div class="mc-border-line"></div>
          </div>
          
          
           <div class="mc-form-spacer">
               <input id="doc-email" name="Email" value="<?php echo $email;?>" type="text" placeholder="">
          <div class="mc-border-line"></div>
          </div>
         
           <div class="mc-btn-container">
            <?php if($default ==1){ ?>  
             <div id="update-default-doctordetails" doctorid="<?php echo $docid;?>" clinicid="<?php echo $clinicid;?>" loadmain="1" class="mc-btn-save-changes">SAVE CHANGES</div>
            <?php }else{?> 
            <div id="update-doctordetails" class="mc-btn-save-changes">SAVE CHANGES</div>
            <?php } ?>
             <div class="mc-btn-cancel">Cancel</div>
           </div>
        </fieldset>
        </form>
        </div><!--PROFILE FORM END-->
    </div><!--DOCTOR PROFILE DETAILS END-->
    
   
    <div class="mc-profile-detail-rcolum mc-fl"> <!--PROFILE DETAIL RIGHT Start-->
<!--     {{Form::open(array("","id"=>"frm-doctorcharge"))}}-->
      <div class="mc-header2">Consultation Charges</div>
      <div><input id="consult-charge" value="<?php if($consultcharge !=""){ echo $consultcharge;}?>" type="text" placeholder="00" class="mc-price-display"></div>
      
<!--            <div class="mc-btn-container">
             	<div class="mc-btn-save-changes">SAVE CHANGES</div>
             	<div class="mc-btn-cancel">Cancel</div>
           	</div>-->
      
    <!--<div class="mc-clear"></div>
      <div class="mc-header3">Consultation Duration</div>
      <div  class="select-box">
        <select id="slot-duration">
        <option value="">Select Time</option>     
            <option value="30min" <?php //if($timeslot !=""){ if($timeslot=='30min'){echo 'selected="selected"'; }} ?>>30min</option>
            <option value="60min" <?php //if($timeslot !=""){ if($timeslot=='60min'){echo 'selected="selected"'; }} ?>>60min</option>   
        </select>
       </div>-->
     <div class="mc-clear"></div>   
     <div class="mc-header3 mc-label13">BOOKINGS</div>
        <div class="mc-border-line"></div>
        <div class="queue">
            <div class="queue-check"><input id="queuetag" <?php if($clinicsession==1||$clinicsession==3){echo 'checked="checked"';}?> type="checkbox"> <label class="mc-label13">Queue No.</label></div>
            <div class="queue-input-container"><input value="<?php echo $queueno;?>" id="queue-no" class="input-queue" type="text"> <label class="mc-label14">Queue No.</label></div>
       <div class="queue-input-container">
       <div class="select-box-queue mc-fl">
        <select id="queue-time">
        <option value="15min" <?php if($queuetime !=""){ if($queuetime=='15min'){echo 'selected="selected"'; }} ?>>15min</option>    
        <option value="30min" <?php if($queuetime !=""){ if($queuetime=='30min'){echo 'selected="selected"'; }} ?>>30min</option>
        <option value="45min" <?php if($queuetime !=""){ if($queuetime=='45min'){echo 'selected="selected"'; }} ?>>45min</option>
        <option value="60min" <?php if($queuetime !=""){ if($queuetime=='60min'){echo 'selected="selected"'; }} ?>>60min</option>
        </select>
       </div><label class="mc-label14 mc-pad-lr">Duration</label></div>
     </div>
     
     <div class="queue">
       <div class="queue-check"><input id="slottag" <?php if($clinicsession==2||$clinicsession==3){echo 'checked="checked"';}?> type="checkbox"> <label class="mc-label13">Appointment Time</label></div>
      <div class="queue-input-container">
      <div class="select-box-queue mc-fl">
        <select id="slot-duration">
            <option value="30min" <?php if($timeslot !=""){ if($timeslot=='30min'){echo 'selected="selected"'; }} ?>>30min</option>
            <option value="60min" <?php if($timeslot !=""){ if($timeslot=='60min'){echo 'selected="selected"'; }} ?>>60min</option>
        </select>
       </div><label class="mc-label14 mc-pad-lr">Duration</label></div>
      </div>
        
        
      
    <div class="mc-btn-container">
    <?php if($default ==1){ ?> 
        <div id="update-doctorcharge" doctorid="<?php echo $docid;?>" clinicid="<?php echo $clinicid;?>" loadmain="1" insertid="<?php if($doctorslotexist!=0){echo $doctorslotexist;}?>" class="mc-btn-save-changes">SAVE CHANGES</div>      
    <?php }else{?>     
    <div id="save-doctorcharge" insertid="<?php if($doctorslotexist!=0){echo $doctorslotexist;}?>" class="mc-btn-save-changes">SAVE CHANGES</div>
    <?php } ?>
<!--    <div id="hime"  class="mc-btn-cancel">Cancel</div>-->
    </div>
<!--      </form>-->
    </div> <!--PROFILE DETAIL RIGHT End-->
    
    


<div id="second-ajax-call">  <!-- Start second Ajax call --> 
<!-- Start Second part -->    
    <div class="mc-clear"></div>  
    <div class="mc-calendar">
      
      
      <div class="mc-calendar-space-none mc-fl">
        <div class="mc-color-box-blue mc-fl"></div>
        <label class="mc-fl mc-label2">AVAILABLE</label>
        <div class="mc-clear"></div>
      </div>
      
      <div class="mc-calendar-space mc-fl">
        <div class="mc-color-box-gray mc-fl"></div>
        <label class="mc-fl mc-label2">BUSY</label>
        <div class="mc-clear"></div>
      </div>
      
      <div class="mc-date-toggle-container mc-fl" currentday="<?php echo $today;?>" <?php if($default ==1){echo "loadmain='1'";}else{echo "loadmain=''";} ?> doctor-slotid="<?php echo $doctorslotexist;?>">
        <div class="mc-arrow-white mc-fl"><img src="{{ URL::asset('assets/images/icn-arrow-white.png') }}" id="date-backward" width="17" height="27" alt="img-arrow" longdesc="images/icn-arrow-white.png"></div>
        <div class="mc-date-blue mc-fl">
            <input type="text" id="datepick6" class="input-hidden">
<!--        <img src="{{ URL::asset('assets/images/icn-date-blue.png') }}" width="25" height="27" alt="img-date-blue" longdesc="images/icn-date-blue.png" id="datepick68"> -->
        </div>
        <label class="mc-fl mc-label3">Today, <?php echo date('F j, Y ');?></label>
        <div class="mc-arrow-blue mc-fl"><img src="{{ URL::asset('assets/images/icn-arrow-blue.png') }}" id="date-forward" width="17" height="27" alt="img-arrow" longdesc="images/icn-arrow-blue.png"></div>
      </div>
      
      
      
      <div class="mc-calendar-option"> 
        <!--<div class="option">Day</div>
        <div class="select">Week</div>
        <div class="option">Month</div>-->
      </div>
    </div>
   
   <div class="mc-clear"></div>
   
    <div class="mc-calendar-view">
    <div class="calendar-time" id="dmk">
      
       <!--<div class="mc-time mc-label4">7 am <?php echo $timeslot;?></div>--> 
        <div class="mc-time mc-label4">7 am</div>
        <div class="mc-time mc-label4">8 am</div>
        <div class="mc-time mc-label4">9 am</div>
        <div class="mc-time mc-label4">10 am</div>
        <div class="mc-time mc-label4">11 am</div>
        <div class="mc-time mc-label4">Noon</div>
        <div class="mc-time mc-label4">1 pm</div>
        <div class="mc-time mc-label4">2 pm</div>
        <div class="mc-time mc-label4">3 pm</div>
        <div class="mc-time mc-label4">4 pm</div>
        <div class="mc-time mc-label4">5 pm</div>
        <div class="mc-time mc-label4">6 pm</div>
        <div class="mc-time mc-label4">7 pm</div>
        <div class="mc-time mc-label4">8 pm</div>
        <div class="mc-time mc-label4">9 pm</div>

    </div>
      
      <div class="mc-fl width100"> 
<!--      <div class="mc-fl mc-blancer"> -->
    
    <div class="mc-calendar-day">
        <?php for($a=0; $a<7;$a++) {
            $dayOfWeek = date('d-m-Y', strtotime($today.' +'.$a.' day'));
            $getWeek = date('l', strtotime($dayOfWeek));
            $dayNow = date("d-m-Y");
            ?>
        <div class="mc-day">
           <div><?php if($dayOfWeek == $dayNow){echo "<span class='highlight'>".$getWeek."</span>";}else{echo $getWeek;}?></div>
           <div class="date"><?php if($dayOfWeek == $dayNow){echo "<span class='highlight'>".$dayOfWeek."</span>";}else{echo $dayOfWeek;}?></div>
        </div>
        <?php } ?>
    </div>      

      
       <div class="mc-border-line"></div>
<?php 
function getCurrentSlots($slotdetails,$slottype,$gactive,$valDate){
    return StringHelper::getMySlotValues($slotdetails,$slottype,$gactive,$valDate);
}
function getProDate($today,$count){
    $mydate = date('d-m-Y', strtotime($today.' +'.$count.' day'));
    return $mydate;
}
function getProWeek($dayOfWeek){
    $myWeek = date('l', strtotime($dayOfWeek));
    $getWeeknow = strtolower(substr($myWeek, 0, 3));
    return $getWeeknow;
}


      
           $totalTimes = 15;
           for($i=0; $i<$totalTimes;$i++){             
               $count = 0;
               ?>          
               <div class="mc-day-option-container" <?php if($default ==1){echo 'loadmain="1"';} ?> docslot="<?php echo $doctorslotexist;?>">
                   <?php if($timeslot == "30min"){ ?>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count);?>">
                   <div id="<?php echo getProWeek(getProDate($today,$count)).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'a'.$i,0,getProDate($today,$count)))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'a'.$i,0,getProDate($today,$count));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'a'.$i,1,getProDate($today,$count))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count)).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'b'.$i,0,getProDate($today,$count)))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'b'.$i,0,getProDate($today,$count));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'b'.$i,1,getProDate($today,$count))==0){echo "mc-slot-color";}?>"></div>
                    </div>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count."+1");?>">
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+1')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'a'.$i,0,getProDate($today,$count.'+1')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'a'.$i,0,getProDate($today,$count.'+1'));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'a'.$i,1,getProDate($today,$count.'+1'))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+1')).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'b'.$i,0,getProDate($today,$count.'+1')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'b'.$i,0,getProDate($today,$count.'+1'));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'b'.$i,1,getProDate($today,$count.'+1'))==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count."+2");?>">
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+2')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'a'.$i,0,getProDate($today,$count.'+2')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'a'.$i,0,getProDate($today,$count.'+2'));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'a'.$i,1,getProDate($today,$count.'+2'))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+2')).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'b'.$i,0,getProDate($today,$count.'+2')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'b'.$i,0,getProDate($today,$count.'+2'));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'b'.$i,1,getProDate($today,$count.'+2'))==0){echo "mc-slot-color";}?>"></div>   
                   </div>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count."+3");?>">
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+3')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'a'.$i,0,getProDate($today,$count.'+3')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'a'.$i,0,getProDate($today,$count.'+3'));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'a'.$i,1,getProDate($today,$count.'+3'))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+3')).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'b'.$i,0,getProDate($today,$count.'+3')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'b'.$i,0,getProDate($today,$count.'+3'));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'b'.$i,1,getProDate($today,$count.'+3'))==0){echo "mc-slot-color";}?>"></div>   
                   </div>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count."+4");?>">
                       <div id="<?php echo getProWeek(getProDate($today,$count.'+4')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'a'.$i,0,getProDate($today,$count.'+4')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'a'.$i,0,getProDate($today,$count.'+4'));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'a'.$i,1,getProDate($today,$count.'+4'))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+4')).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'b'.$i,0,getProDate($today,$count.'+4')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'b'.$i,0,getProDate($today,$count.'+4'));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'b'.$i,1,getProDate($today,$count.'+4'))==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count."+5");?>">
                       <div id="<?php echo getProWeek(getProDate($today,$count.'+5')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'a'.$i,0,getProDate($today,$count.'+5')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'a'.$i,0,getProDate($today,$count.'+5'));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'a'.$i,1,getProDate($today,$count.'+5'))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+5')).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'b'.$i,0,getProDate($today,$count.'+5')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'b'.$i,0,getProDate($today,$count.'+5'));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'b'.$i,1,getProDate($today,$count.'+5'))==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count."+6");?>">
                     <div id="<?php echo getProWeek(getProDate($today,$count.'+6')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'a'.$i,0,getProDate($today,$count.'+6')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'a'.$i,0,getProDate($today,$count.'+6'));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'a'.$i,1,getProDate($today,$count.'+6'))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+6')).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'b'.$i,0,getProDate($today,$count.'+6')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'b'.$i,0,getProDate($today,$count.'+6'));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'b'.$i,1,getProDate($today,$count.'+6'))==0){echo "mc-slot-color";}?>"></div>  
                   </div>
                   <?php }elseif($timeslot == "60min"){ ?>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count);?>">
                    <div id="<?php echo getProWeek(getProDate($today,$count)).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'a'.$i,0,getProDate($today,$count)))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'a'.$i,0,getProDate($today,$count));}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'a'.$i,1,getProDate($today,$count))==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count."+1");?>">
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+1')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'a'.$i,0,getProDate($today,$count.'+1')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'a'.$i,0,getProDate($today,$count.'+1'));}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'a'.$i,1,getProDate($today,$count.'+1'))==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count."+2");?>">
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+2')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'a'.$i,0,getProDate($today,$count.'+2')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'a'.$i,0,getProDate($today,$count.'+2'));}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'a'.$i,1,getProDate($today,$count.'+2'))==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count."+3");?>">
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+3')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'a'.$i,0,getProDate($today,$count.'+3')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'a'.$i,0,getProDate($today,$count.'+3'));}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'a'.$i,1,getProDate($today,$count.'+3'))==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count."+4");?>">
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+4')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'a'.$i,0,getProDate($today,$count.'+4')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'a'.$i,0,getProDate($today,$count.'+4'));}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'a'.$i,1,getProDate($today,$count.'+4'))==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count."+5");?>">
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+5')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'a'.$i,0,getProDate($today,$count.'+5')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'a'.$i,0,getProDate($today,$count.'+5'));}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'a'.$i,1,getProDate($today,$count.'+5'))==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count."+6");?>">
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+6')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'a'.$i,0,getProDate($today,$count.'+6')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'a'.$i,0,getProDate($today,$count.'+6'));}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'a'.$i,1,getProDate($today,$count.'+6'))==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <?php //} ?> 
                   <?php }else{ ?> 
                   <div class="mc-day-block" cdate="14-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                    </div>
                   <div class="mc-day-block" cdate="15-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="16-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="17-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="18-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="19-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="20-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                   </div>
                   <?php } ?>
            </div>
            <div class="mc-border-line"></div> 
      <?php  }
       ?>
   
 </div>       
    </div>
    <!-- End Second part -->
</div><!-- End second Ajax call -->