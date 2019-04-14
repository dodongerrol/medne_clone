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
            <!--<img src="{{ URL::asset('assets/images/icn-date-blue.png') }}" width="25" height="27" alt="img-date-blue" longdesc="images/icn-date-blue.png" id="datepick6"> -->
        </div>
        <label class="mc-fl mc-label3">Today, <?php echo date('F j, Y ');?></label>
        <div class="mc-arrow-blue mc-fl"><img src="{{ URL::asset('assets/images/icn-arrow-blue.png') }}" id="date-forward" width="17" height="27" alt="img-arrow" longdesc="images/icn-arrow-blue.png"></div>
      </div>
      
      
      
      <div class="mc-calendar-option"> 
<!--        <div class="option">Day</div>
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
      <!--<div class="mc-fl mc-blancer">--> 
    
    <div class="mc-calendar-day">
        <?php for($a=0; $a<7;$a++) {
            $dayOfWeek = date('d-m-Y', strtotime($today.' +'.$a.' day'));
            $getWeek = date('l', strtotime($dayOfWeek));
            $dayNow = date("d-m-Y");
            ?>
        <div class="mc-day">
           <div><?php if($dayOfWeek == $dayNow){echo "<span class='highlight'>".$getWeek."</span>";}else{echo $getWeek;}?></div>
           <div class="date"><?php if($dayOfWeek == $dayNow){echo "<span class='highlight'>".$dayOfWeek."</span>";}else{echo $dayOfWeek;};?></div>
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
   
 </div>       
    </div>
<!-- End Second part -->