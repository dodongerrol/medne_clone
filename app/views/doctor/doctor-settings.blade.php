@include('common.header-doctor')
<style type="text/css">

@import url("sinkin-sans-fontfacekit/web fonts/sinkinsans_300light_macroman/stylesheet.css");
body,td,th,input,textarea {font-family: 'sinkin_sans300_light'; }
.mc-price-display{ font-family: 'Nunito', sans-serif;}


.mc-calendar-day .mc-day .date {
    color: #999999;
    float: left;
    font-size: 12px;
    height: 35px;
    padding-top: 2px !important;
    text-align: center;
    width: 100%;
}

.width100{ width:93% !important;}

.mc-calendar-view {
    
    margin-bottom: 30px;
    width: 100%;
	/*background-color:orange;*/
}
	
	
.mc-calendar-view .calendar-time {
    float: left;
    height: 929px;
    width: 5% !important;
	/*background-color:red;*/
}
	
	.calendar-time .mc-time {
   /* height: 20px;*/
    width: 100% !important;
}


.mc-calendar-day .mc-day {
    color: #cccccc;
    float: left;
    font-size: 14px;
    height: 35px;
    padding-top: 28px;
    text-align: center;
    width: 14.2%;
}



.mc-day-option-container .mc-day-block {
    float: left;
    height: 73px;
    margin-right: 10px;
    width: 13.4%;
}


.mc-day-option-container .mc-day-block-last {
    float: left;
    height: 73px;
    width: 14.2%;
}


.mc-day-block .mc-slot1 {
    background-color: #1b9bd7;
    color: #666666;
    font-size: 12px;
    height: 30px;
    margin-bottom: 1px;
    padding: 6px 0 0 5px;
    width: 100% !important;
}


.mc-day-block .mc-slot2 {
    background-color: #1b9bd7;
    color: #666666;
    font-size: 12px;
    height: 30px;
    margin-bottom: 1px;
    padding: 6px 0 0 5px;
    width: 100% !important;
}


.mc-day-block-last .mc-slot1-last {
    background-color: #1b9bd7;
    height: 36px;
    margin-bottom: 1px;
    width: 100% !important;
}

.mc-day-block-last .mc-slot2-last {
    background-color: #1b9bd7;
    height: 36px;
    width: 100% !important;
}


@media 
(-webkit-min-device-pixel-ratio: 2), 
(min-resolution: 192dpi) { 

.width100{ width:93% !important;}

.mc-calendar-view {
    
    margin-bottom: 30px;
    width: 100%;
	/*background-color:orange;*/
}
	
	
.mc-calendar-view .calendar-time {
    float: left;
    height: 929px;
    width: 5% !important;
	/*background-color:red;*/
}
	
	.calendar-time .mc-time {
   /* height: 20px;*/
    width: 100% !important;
}


.mc-calendar-day .mc-day {
    color: #cccccc;
    float: left;
    font-size: 14px;
    height: 35px;
    padding-top: 28px;
    text-align: center;
    width: 14.2%;
}



.mc-day-option-container .mc-day-block {
    float: left;
    height: 73px;
    margin-right: 10px;
    width: 13.4%;
}


.mc-day-option-container .mc-day-block-last {
    float: left;
    height: 73px;
    width: 13.2%;
}


.mc-day-block .mc-slot1 {
    background-color: #1b9bd7;
    color: #666666;
    font-size: 12px;
    height: 30px;
    margin-bottom: 1px;
    padding: 6px 0 0 5px;
    width: 100% !important;
}


.mc-day-block .mc-slot2 {
    background-color: #1b9bd7;
    color: #666666;
    font-size: 12px;
    height: 30px;
    margin-bottom: 1px;
    padding: 6px 0 0 5px;
    width: 100% !important;
}


.mc-day-block-last .mc-slot1-last {
    background-color: #1b9bd7;
    height: 36px;
    margin-bottom: 1px;
    width: 100% !important;
}

.mc-day-block-last .mc-slot2-last {
    background-color: #1b9bd7;
    height: 36px;
    width: 100% !important;
}



}

</style>
    <div class="mc-border-line"></div>
   
   <div class="mc-clear"></div>
    
   
    <div class="mc-doctor-profile-container"><!--DOCTOR PROFILE CONTAINER-->
    <div class="mc-dr-profile-detail mc-pad-l"><!--DOCTOR PROFILE DETAILS-->
        <div class="mc-dr-profile-image"><img src="<?php echo $image;?>" width="125" height="125" alt="img-profile" longdesc="images/mc-profile-img.png"></div>
        <div class="mc-profile-form"><!--PROFILE FORM-->
        <form  method="get" id="form-doctor">
        <fieldset>
          <div class="mc-form-spacer">
          <input id="doc-name" name="Name" type="text" value="<?php echo $name;?>" placeholder="Name">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
          <input id="doc-qualification" name="Qualification" type="text" value="<?php echo $qualification;?>" placeholder="Qualification">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
          <input id="doc-speciality" name="Specialty" type="text" value="<?php echo $specialty;?>" placeholder="Specialty">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
          <input id="doc-mobile" name="Mobile" type="text" value="<?php echo $phone;?>" placeholder="Mobile">
          <div class="mc-border-line"></div>
          </div>
         
           <div class="mc-form-spacer">
          <input id="doc-phone" class="inputshort " name="" value="<?php echo $emergency;?>" type="text" placeholder="Phone"> <label class=" mc-label mc-fl" >Emergency</label>
          <div class="mc-clear"></div>
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
          <input id="doc-email" name="Email" type="text" value="<?php echo $email;?>" placeholder="Email">
          <div class="mc-border-line"></div>
          </div>
         
           <div class="mc-btn-container">
             <div id="update-default-doctordetails" loadmain="1" doctorid="<?php echo $doctorid;?>" class="mc-btn-save-changes">SAVE CHANGES</div>
             <div class="mc-btn-cancel">Cancel</div>
           </div>
        </fieldset>
        </form>
        </div><!--PROFILE FORM END-->
    </div><!--DOCTOR PROFILE DETAILS END-->
    
    
    <div class="mc-profile-detail-rcolum mc-fl mc-mar-l"> <!--PROFILE DETAIL RIGHT -->
      <div class="mc-header2">Consultation Charges</div>
      <div><input id="consult-charge" value="<?php if($consultcharge !=""){ echo $consultcharge;}?>" type="text" placeholder="00" class="mc-price-display"></div>
<!--      <div class="mc-price-display">100<span class=" span mc-font">$</span></div>-->

<!--      <div class="mc-clear"></div>
      <div class="mc-header3">Consultation Duration</div>
      <div class="select-box">
        <select id="slot-duration">
        <option value="30min" <?php if($timeslot !=""){ if($timeslot=='30min'){echo 'selected="selected"'; }} ?>>30min</option>
        <option value="60min" <?php if($timeslot !=""){ if($timeslot=='60min'){echo 'selected="selected"'; }} ?>>60min</option>  
        </select>
       </div>-->

     <div class="mc-clear"></div>   
     <div class="mc-header3 mc-label13">BOOKINGS</div>
        <div class="mc-border-line"></div>
        <div class="queue">
            <div class="queue-check"><input id="queuetag" <?php if($clinicsession==1||$clinicsession==3){echo 'checked="checked"';}?> type="checkbox"> <label class="mc-label13">Queue No.</label></div>
            <div class="queue-input-container"><input  id="queue-no" class="input-queue" type="text" value="<?php echo $queueno;?>"> <label class="mc-label14">Queue No.</label></div>
       <div class="queue-input-container">
       <div class="select-box-queue mc-fl">
        <select id="queue-time" >
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
        <?php if($doctorslotexist != null){ ?>  
        <div id="update-consultation" insertid="<?php echo $doctorslotexist;?>" doctorid="<?php echo $doctorid;?>" clinicid="<?php echo $clinicid;?>" class="mc-btn-save-changes">SAVE CHANGES</div>
        <?php }else{?>
        <div id="save-consultation" insertid="" doctorid="<?php echo $doctorid;?>" clinicid="<?php echo $clinicid;?>" class="mc-btn-save-changes">SAVE CHANGES</div>
        <?php } ?>
        <div class="mc-btn-cancel">Cancel</div>
        </div>
    </div> <!--PROFILE DETAIL RIGHT END -->
    
<div id="second-ajax-call"><!-- Start second Ajax call --> 
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
        <div class="mc-arrow-white mc-fl"><img id="date-backward" src="{{ URL::asset('assets/images/icn-arrow-white.png') }}" width="17" height="27" alt="img-arrow" longdesc="images/icn-arrow-white.png"></div>
        <div class="mc-date-blue mc-fl">
            <input type="text" id="datepick6" class="input-hidden">
            <!--<img src="images/icn-date-blue.png" width="25" height="27" alt="img-date-blue" longdesc="images/icn-date-blue.png"> -->
        </div>
        <label class="mc-fl mc-label3">Today, <?php echo date('F j, Y ');?></label>
        <div class="mc-arrow-blue mc-fl"><img id="date-forward" src="{{ URL::asset('assets/images/icn-arrow-blue.png') }}" width="17" height="27" alt="img-arrow" longdesc="images/icn-arrow-blue.png"></div>
      </div>
      
      
      
      <div class="mc-calendar-option"> 
<!--        <div class="select">Week</div>-->
      </div>
    </div>
    
    
    
    <div class="mc-calendar-view">
      <div class="calendar-time">
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
      <!--<div class="mc-fl mc-blancer">-->    
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
                   <?php } ?>        
            </div>
            <div class="mc-border-line"></div> 
      <?php  }
       ?>
            

        
        <div class="mc-clear"></div>
       
      </div>
    </div>
 </div> <!-- Second part ajax call end --> 
 
     <div class="mc-clear"></div>

@include('common.footer-doctor')   