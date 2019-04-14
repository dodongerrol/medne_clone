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
         <div class="mc-day">
           <div>MONDAY</div>
           <div class="date">14.25.2014</div>
         </div>
         <div class="mc-day">
           <div>TUESDAY</div>
            <div class="date">14.25.2014</div>
         </div>
         <div class="mc-day">
           <div>WEDNESDAY</div>
            <div class="date">14.25.2014</div>
         </div>
         <div class="mc-day">
           <div>THURSDAY</div>
            <div class="date">14.25.2014</div>
         </div>
         <div class="mc-day">
           <div>FRIDAY</div>
            <div class="date">14.25.2014</div>
         </div>
         <div class="mc-day">
           <div>SATURDAY</div>
            <div class="date">14.25.2014</div>
         </div>
         <div class="mc-day">
           <div>SUNDAY</div>
            <div class="date">14.25.2014</div>
         </div>
       </div>      

      
       <div class="mc-border-line"></div>
<?php 


function getCurrentSlots($slotdetails,$slottype,$gactive){
    return StringHelper::getMySlotValues($slotdetails,$slottype,$gactive);
}
  
       
           $totalTimes = 15;
           for($i=0; $i<$totalTimes;$i++){ ?>          
               <div class="mc-day-option-container" <?php if($default ==1){echo 'loadmain="1"';} ?> docslot="<?php echo $doctorslotexist;?>">
                   <?php if($timeslot == "30min"){ ?>
                   <div class="mc-day-block" cdate="14-11-2014">
                   <div id="mona<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"mona".$i,0))){echo getCurrentSlots($slotdetails,"mona".$i,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"mona".$i,1)==0){echo "mc-slot-color";}?>"></div>
                    <div id="monb<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"monb".$i,0))){echo getCurrentSlots($slotdetails,"monb".$i,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"monb".$i,1)==0){echo "mc-slot-color";}?>"></div>
                    
<!--                    <div id="mona<?php echo $i;?>" insertedid="<?php //if(array_key_exists($i,$slotdetails)){ if($slotdetails[$i]->SlotID=="mona".$i){echo $slotdetails[$i]->SlotDetailID;} } ?>" class="getmyslot mc-slot1 mc-slot-color"></div>
                    <div id="monb<?php echo $i;?>" insertedid="<?php //if(array_key_exists($i,$slotdetails)){ if($slotdetails[$i]->SlotID=="monb".$i){echo $slotdetails[$i]->SlotDetailID;} }?>" class="getmyslot mc-slot2 mc-slot-color"></div>-->
                    </div>
                   <div class="mc-day-block" cdate="15-11-2014">
                    <div id="tusa<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"tusa".$i,0))){echo getCurrentSlots($slotdetails,"tusa".$i,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"tusa".$i,1)==0){echo "mc-slot-color";}?>"></div>
                    <div id="tusb<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"tusb".$i,0))){echo getCurrentSlots($slotdetails,"tusb".$i,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"tusb".$i,1)==0){echo "mc-slot-color";}?>"></div>
<!--                    <div id="tusa<?php echo $i;?>" class="mc-slot1"></div>
                        <div id="tusb<?php echo $i;?>" class="mc-slot2"></div>-->
                   </div>
                   <div class="mc-day-block" cdate="16-11-2014">
                    <div id="wena<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"wena".$i,0))){echo getCurrentSlots($slotdetails,"wena".$i,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"wena".$i,1)==0){echo "mc-slot-color";}?>"></div>
                    <div id="wenb<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"wenb".$i,0))){echo getCurrentSlots($slotdetails,"wenb".$i,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"wenb".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="17-11-2014">
                    <div id="thua<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"thua".$i,0))){echo getCurrentSlots($slotdetails,"thua".$i,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"thua".$i,1)==0){echo "mc-slot-color";}?>"></div>
                    <div id="thub<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"thub".$i,0))){echo getCurrentSlots($slotdetails,"thub".$i,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"thub".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="18-11-2014">
                    <div id="fria<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"fria".$i,0))){echo getCurrentSlots($slotdetails,"fria".$i,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"fria".$i,1)==0){echo "mc-slot-color";}?>"></div>
                    <div id="frib<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"frib".$i,0))){echo getCurrentSlots($slotdetails,"frib".$i,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"frib".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="19-11-2014">
                    <div id="sata<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"sata".$i,0))){echo getCurrentSlots($slotdetails,"sata".$i,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"sata".$i,1)==0){echo "mc-slot-color";}?>"></div>
                    <div id="satb<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"satb".$i,0))){echo getCurrentSlots($slotdetails,"satb".$i,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"satb".$i,1)==0){echo "mc-slot-color";}?>"></div>
                   </div>
                   <div class="mc-day-block" cdate="20-11-2014">
                    <div id="suna<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"suna".$i,0))){echo getCurrentSlots($slotdetails,"suna".$i,0);}?>" class="getmyslot mc-slot1 <?php if(getCurrentSlots($slotdetails,"suna".$i,1)==0){echo "mc-slot-color";}?>"></div>
                    <div id="sunb<?php echo $i;?>" insertedid="<?php if(!empty(getCurrentSlots($slotdetails,"sunb".$i,0))){echo getCurrentSlots($slotdetails,"sunb".$i,0);}?>" class="getmyslot mc-slot2 <?php if(getCurrentSlots($slotdetails,"sunb".$i,1)==0){echo "mc-slot-color";}?>"></div>
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