<div class="mc-dr-profile-detail"><!--DOCTOR PROFILE DETAILS-->
        <div class="mc-dr-profile-image"><img src="<?php echo $image;?>" width="125" height="125" alt="img-profile" longdesc="images/mc-profile-img.png"></div>
       
        <div class="mc-profile-form"><!--PROFILE FORM-->
        <form action="" method="get">
        <fieldset>
          <div class="mc-form-spacer">
              <input id="doc-name" name="" value="<?php echo $name;?>" type="text" placeholder="Name">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
               <input id="doc-qualification" name="" value="<?php echo $qualifications;?>" type="text" placeholder="Qualifications">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
               <input id="doc-speciality" name="" value="<?php echo $specialty;?>" type="text" placeholder="Specialty">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
               <input id="doc-mobile" name="" value="<?php echo $mobile;?>" type="text" placeholder="Mobile">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
               <input id="doc-phone" class="inputshort " name="" value="<?php echo $emergency;?>" type="text" placeholder="Phone"> <label class=" mc-label mc-fl" >Emergency</label>
          <div class="mc-clear"></div>
          <div class="mc-border-line"></div>
          </div>
          
          
           <div class="mc-form-spacer">
               <input id="doc-email" name="" value="<?php echo $email;?>" type="text" placeholder="">
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
      
      <div class="mc-clear"></div>
      <div class="mc-header3">Consultation Duration</div>
      <div  class="select-box">
        <select id="slot-duration">
        <option value="">Select Time</option>     
            <option value="30min" <?php if($timeslot !=""){ if($timeslot=='30min'){echo 'selected="selected"'; }} ?>>30min</option>
<!--            <option value="40min" <?php if($timeslot !=""){ if($timeslot=='40min'){echo 'selected="selected"'; }} ?>>40min</option>
            <option value="50min" <?php if($timeslot !=""){ if($timeslot=='50min'){echo 'selected="selected"'; }} ?>>50min</option>-->
            <option value="60min" <?php if($timeslot !=""){ if($timeslot=='60min'){echo 'selected="selected"'; }} ?>>60min</option>   
        </select>
       </div>
      
    <div class="mc-btn-container">
    <?php if($default ==1){ ?> 
        <div id="update-doctorcharge" doctorid="<?php echo $docid;?>" clinicid="<?php echo $clinicid;?>" loadmain="1" insertid="<?php if($doctorslotexist!=0){echo $doctorslotexist;}?>" class="mc-btn-save-changes">SAVE CHANGES</div>      
    <?php }else{?>     
    <div id="save-doctorcharge" insertid="<?php if($doctorslotexist!=0){echo $doctorslotexist;}?>" class="mc-btn-save-changes">SAVE CHANGES</div>
    <?php } ?>
    <div id="hime"  class="mc-btn-cancel">Cancel</div>
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
      
      <div class="mc-date-toggle-container mc-fl">
        <div class="mc-arrow-white mc-fl"><img src="{{ URL::asset('assets/images/icn-arrow-white.png') }}" width="17" height="27" alt="img-arrow" longdesc="images/icn-arrow-white.png"></div>
        <div class="mc-date-blue mc-fl"><img src="{{ URL::asset('assets/images/icn-date-blue.png') }}" width="25" height="27" alt="img-date-blue" longdesc="images/icn-date-blue.png" id="datepick6"> </div>
        <label class="mc-fl mc-label3">Today, June 23, 2014</label>
        <div class="mc-arrow-blue mc-fl"><img src="{{ URL::asset('assets/images/icn-arrow-blue.png') }}" width="17" height="27" alt="img-arrow" longdesc="images/icn-arrow-blue.png"></div>
      </div>
      
      
      
      <div class="mc-calendar-option"> 
        <div class="option">Day</div>
        <div class="select">Week</div>
        <div class="option">Month</div>
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
      
      <div class="mc-fl mc-blancer"> 
      
    
    <div class="mc-calendar-day">
        <?php for($a=0; $a<7;$a++) {
            $dayOfWeek = date('d-m-Y', strtotime($today.' +'.$a.' day'));
            ?>
        <div class="mc-day">
           <div><?php echo date('l', strtotime($dayOfWeek));?></div>
           <div class="date"><?php echo $dayOfWeek;?></div>
        </div>
        <?php } ?>
    </div>      

      
       <div class="mc-border-line"></div>
<?php 
function getCurrentSlots($slotdetails,$slottype,$gactive,$valDate){
    return StringHelper::getMySlotValues($slotdetails,$slottype,$gactive,$valDate);
}
function getProDate($today,$count){
    return date('d-m-Y', strtotime($today.' +'.$count.' day'));
}
function getProWeek($dayOfWeek){
    $myWeek = date('l', strtotime($dayOfWeek));
    $getWeeknow = strtolower(substr($myWeek, 0, 3));
    echo $getWeeknow;
}
//getProWeek('08-12-2014');
//getProWeek(date('d-m-Y', strtotime($today.' +1 day')));
//getProWeek(date('d-m-Y', strtotime($today.' +2 day')));
//getProWeek(date('d-m-Y', strtotime($today.' +3 day')));
//getProWeek(date('d-m-Y', strtotime($today.' +4 day')));
//getProWeek(date('d-m-Y', strtotime($today.' +5 day')));
//getProWeek(date('d-m-Y', strtotime($today.' +6 day')));




      
           $totalTimes = 15;
           for($i=0; $i<$totalTimes;$i++){             
               $count = 0;
               ?>          
               <div class="mc-day-option-container" <?php if($default ==1){echo 'loadmain="1"';} ?> docslot="<?php echo $doctorslotexist;?>">
                   <?php if($timeslot == "30min"){ ?>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count);?>">
                   <div id="<?php echo getProWeek(getProDate($today,$count)).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'a'.$i,0,getProDate($today,$count)))){echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'a'.$i,0,getProDate($today,$count));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'a'.$i,1,getProDate($today,$count))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count)).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'b'.$i,0,getProDate($today,$count)))){echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'b'.$i,0,getProDate($today,$count));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'b'.$i,1,getProDate($today,$count))==0){echo "mc-slot-color";}?>"></div>
                    </div>
                   <div class="mc-day-block" cdate="<?php echo date('d-m-Y', strtotime($today.' +'.$count.'+1 day')); ?>">
                    <div id="tusa<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"tusa".$i,0,0))){echo getCurrentSlots($slotdetails,"tusa".$i,0,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"tusa".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                    <div id="tusb<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"tusb".$i,0,0))){echo getCurrentSlots($slotdetails,"tusb".$i,0,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"tusb".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo date('d-m-Y', strtotime($today.' +'.$count.'+2 day'));?>">
                    <div id="wena<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"wena".$i,0,0))){echo getCurrentSlots($slotdetails,"wena".$i,0,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"wena".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                    <div id="wenb<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"wenb".$i,0,0))){echo getCurrentSlots($slotdetails,"wenb".$i,0,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"wenb".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo date('d-m-Y', strtotime($today.' +'.$count.'+3 day'));?>">
                    <div id="thua<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"thua".$i,0,0))){echo getCurrentSlots($slotdetails,"thua".$i,0,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"thua".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                    <div id="thub<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"thub".$i,0,0))){echo getCurrentSlots($slotdetails,"thub".$i,0,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"thub".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo date('d-m-Y', strtotime($today.' +'.$count.'+4 day'));?>">
                    <div id="fria<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"fria".$i,0,0))){echo getCurrentSlots($slotdetails,"fria".$i,0,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"fria".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                    <div id="frib<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"frib".$i,0,0))){echo getCurrentSlots($slotdetails,"frib".$i,0,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"frib".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo date('d-m-Y', strtotime($today.' +'.$count.'+5 day'));?>">
                    <div id="sata<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"sata".$i,0,0))){echo getCurrentSlots($slotdetails,"sata".$i,0,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"sata".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                    <div id="satb<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"satb".$i,0,0))){echo getCurrentSlots($slotdetails,"satb".$i,0,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"satb".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="<?php echo date('d-m-Y', strtotime($today.' +'.$count.'+6 day'));?>">
                    <div id="suna<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"suna".$i,0,0))){echo getCurrentSlots($slotdetails,"suna".$i,0,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"suna".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                    <div id="sunb<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"sunb".$i,0,0))){echo getCurrentSlots($slotdetails,"sunb".$i,0,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"sunb".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <?php }elseif($timeslot == "60min"){ ?>
                   <div class="mc-day-block" cdate="14-11-2014">
                    <div id="mona<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"mona".$i,0))){echo getCurrentSlots($slotdetails,"mona".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"mona".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="15-11-2014">
                    <div id="tusa<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"tusa".$i,0))){echo getCurrentSlots($slotdetails,"tusa".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"tusa".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="16-11-2014">
                    <div id="wena<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"wena".$i,0))){echo getCurrentSlots($slotdetails,"wena".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"wena".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="17-11-2014">
                    <div id="thua<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"thua".$i,0))){echo getCurrentSlots($slotdetails,"thua".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"thua".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="18-11-2014">
                    <div id="fria<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"fria".$i,0))){echo getCurrentSlots($slotdetails,"fria".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"fria".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="19-11-2014">
                    <div id="sata<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"sata".$i,0))){echo getCurrentSlots($slotdetails,"sata".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"sata".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="20-11-2014">
                    <div id="suna<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"suna".$i,0))){echo getCurrentSlots($slotdetails,"suna".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"suna".$i,1)==0){echo "mc-slot-color";}?>"></div>
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



<!-- Slot details start -->

<div class="mc-day-option-container" <?php if($default ==1){echo 'loadmain="1"';} ?> docslot="<?php echo $doctorslotexist;?>">
                   <?php if($timeslot == "30min"){ ?>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count);?>">
                   <div id="<?php echo getProWeek(getProDate($today,$count)).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'a'.$i,0,getProDate($today,$count)))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'a'.$i,0,getProDate($today,$count));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'a'.$i,1,getProDate($today,$count))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count)).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'b'.$i,0,getProDate($today,$count)))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'b'.$i,0,getProDate($today,$count));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count)).'b'.$i,1,getProDate($today,$count))==0){echo "mc-slot-color";}?>"></div>
                    
<!--                    <div id="mona<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,'mona'.$i,0,getProDate($today,$count)))){ echo getCurrentSlots($slotdetails,'mona'.$i,0,getProDate($today,$count));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,'mona'.$i,1,getProDate($today,$count))==0){echo "mc-slot-color";}?>"></div>
                    <div id="monb<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,'monb'.$i,0,getProDate($today,$count)))){ echo getCurrentSlots($slotdetails,'monb'.$i,0,getProDate($today,$count));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,'monb'.$i,1,getProDate($today,$count))==0){echo "mc-slot-color";}?>"></div>-->
                    </div>
                   <div class="mc-day-block" cdate="<?php echo getProDate($today,$count."+1");?>">
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+1')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'a'.$i,0,getProDate($today,$count.'+1')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'a'.$i,0,getProDate($today,$count.'+1'));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'a'.$i,1,getProDate($today,$count.'+1'))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+1')).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'b'.$i,0,getProDate($today,$count.'+1')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'b'.$i,0,getProDate($today,$count.'+1'));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+1')).'b'.$i,1,getProDate($today,$count.'+1'))==0){echo "mc-slot-color";}?>"></div>
                    
<!--                    <div id="<?php echo 'tusa'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,'mona'.$i,0,getProDate($today,$count.'+1')))){ echo getCurrentSlots($slotdetails,'tusa'.$i,0,getProDate($today,$count.'+1'));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,'tusa'.$i,1,getProDate($today,$count.'+1'))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo 'tusb'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,'monb'.$i,0,getProDate($today,$count.'+1')))){ echo getCurrentSlots($slotdetails,'tusb'.$i,0,getProDate($today,$count.'+1'));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,'tusb'.$i,1,getProDate($today,$count.'+1'))==0){echo "mc-slot-color";}?>"></div>-->
                   </div>
                   <div class="mc-day-block" cdate="<?php echo date('d-m-Y', strtotime($today.' +'.$count.'+2 day'));?>">
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+2')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'a'.$i,0,getProDate($today,$count.'+2')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'a'.$i,0,getProDate($today,$count.'+2'));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'a'.$i,1,getProDate($today,$count.'+2'))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+2')).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'b'.$i,0,getProDate($today,$count.'+2')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'b'.$i,0,getProDate($today,$count.'+2'));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+2')).'b'.$i,1,getProDate($today,$count.'+2'))==0){echo "mc-slot-color";}?>"></div>   
<!--                    <div id="wena<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"wena".$i,0,0))){echo getCurrentSlots($slotdetails,"wena".$i,0,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"wena".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                    <div id="wenb<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"wenb".$i,0,0))){echo getCurrentSlots($slotdetails,"wenb".$i,0,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"wenb".$i,1,0)==0){echo "mc-slot-color";}?>"></div>-->
                   </div>
                   <div class="mc-day-block" cdate="<?php echo date('d-m-Y', strtotime($today.' +'.$count.'+3 day'));?>">
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+3')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'a'.$i,0,getProDate($today,$count.'+3')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'a'.$i,0,getProDate($today,$count.'+3'));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'a'.$i,1,getProDate($today,$count.'+3'))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+3')).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'b'.$i,0,getProDate($today,$count.'+3')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'b'.$i,0,getProDate($today,$count.'+3'));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+3')).'b'.$i,1,getProDate($today,$count.'+3'))==0){echo "mc-slot-color";}?>"></div>   
<!--                    <div id="thua<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"thua".$i,0,0))){echo getCurrentSlots($slotdetails,"thua".$i,0,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"thua".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                    <div id="thub<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"thub".$i,0,0))){echo getCurrentSlots($slotdetails,"thub".$i,0,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"thub".$i,1,0)==0){echo "mc-slot-color";}?>"></div>-->
                   </div>
                   <div class="mc-day-block" cdate="<?php echo date('d-m-Y', strtotime($today.' +'.$count.'+4 day'));?>">
                       <div id="<?php echo getProWeek(getProDate($today,$count.'+4')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'a'.$i,0,getProDate($today,$count.'+4')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'a'.$i,0,getProDate($today,$count.'+4'));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'a'.$i,1,getProDate($today,$count.'+4'))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+4')).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'b'.$i,0,getProDate($today,$count.'+4')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'b'.$i,0,getProDate($today,$count.'+4'));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+4')).'b'.$i,1,getProDate($today,$count.'+4'))==0){echo "mc-slot-color";}?>"></div>
<!--                    <div id="fria<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"fria".$i,0,0))){echo getCurrentSlots($slotdetails,"fria".$i,0,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"fria".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                    <div id="frib<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"frib".$i,0,0))){echo getCurrentSlots($slotdetails,"frib".$i,0,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"frib".$i,1,0)==0){echo "mc-slot-color";}?>"></div>-->
                   </div>
                   <div class="mc-day-block" cdate="<?php echo date('d-m-Y', strtotime($today.' +'.$count.'+5 day'));?>">
                       <div id="<?php echo getProWeek(getProDate($today,$count.'+5')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'a'.$i,0,getProDate($today,$count.'+5')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'a'.$i,0,getProDate($today,$count.'+5'));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'a'.$i,1,getProDate($today,$count.'+5'))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+5')).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'b'.$i,0,getProDate($today,$count.'+5')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'b'.$i,0,getProDate($today,$count.'+5'));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+5')).'b'.$i,1,getProDate($today,$count.'+5'))==0){echo "mc-slot-color";}?>"></div>
<!--                    <div id="sata<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"sata".$i,0,0))){echo getCurrentSlots($slotdetails,"sata".$i,0,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"sata".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                    <div id="satb<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"satb".$i,0,0))){echo getCurrentSlots($slotdetails,"satb".$i,0,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"satb".$i,1,0)==0){echo "mc-slot-color";}?>"></div>-->
                   </div>
                   <div class="mc-day-block" cdate="<?php echo date('d-m-Y', strtotime($today.' +'.$count.'+6 day'));?>">
                     <div id="<?php echo getProWeek(getProDate($today,$count.'+6')).'a'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'a'.$i,0,getProDate($today,$count.'+6')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'a'.$i,0,getProDate($today,$count.'+6'));}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'a'.$i,1,getProDate($today,$count.'+6'))==0){echo "mc-slot-color";}?>"></div>
                    <div id="<?php echo getProWeek(getProDate($today,$count.'+6')).'b'.$i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'b'.$i,0,getProDate($today,$count.'+6')))){ echo getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'b'.$i,0,getProDate($today,$count.'+6'));}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,getProWeek(getProDate($today,$count.'+6')).'b'.$i,1,getProDate($today,$count.'+6'))==0){echo "mc-slot-color";}?>"></div>  
<!--                    <div id="suna<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"suna".$i,0,0))){echo getCurrentSlots($slotdetails,"suna".$i,0,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"suna".$i,1,0)==0){echo "mc-slot-color";}?>"></div>
                    <div id="sunb<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"sunb".$i,0,0))){echo getCurrentSlots($slotdetails,"sunb".$i,0,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"sunb".$i,1,0)==0){echo "mc-slot-color";}?>"></div>-->
                   </div>
                   <?php }elseif($timeslot == "60min"){ ?>
                   <div class="mc-day-block" cdate="14-11-2014">
                    <div id="mona<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"mona".$i,0))){echo getCurrentSlots($slotdetails,"mona".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"mona".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="15-11-2014">
                    <div id="tusa<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"tusa".$i,0))){echo getCurrentSlots($slotdetails,"tusa".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"tusa".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="16-11-2014">
                    <div id="wena<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"wena".$i,0))){echo getCurrentSlots($slotdetails,"wena".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"wena".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="17-11-2014">
                    <div id="thua<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"thua".$i,0))){echo getCurrentSlots($slotdetails,"thua".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"thua".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="18-11-2014">
                    <div id="fria<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"fria".$i,0))){echo getCurrentSlots($slotdetails,"fria".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"fria".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="19-11-2014">
                    <div id="sata<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"sata".$i,0))){echo getCurrentSlots($slotdetails,"sata".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"sata".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="20-11-2014">
                    <div id="suna<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"suna".$i,0))){echo getCurrentSlots($slotdetails,"suna".$i,0);}?>" class="getmyslot mc-slot1hr <?php if(getCurrentSlots($slotdetails,"suna".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <?php } ?>        
            </div>

<!-- Slot details end -->