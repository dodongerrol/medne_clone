<?php 
    $firstDoctorSlot = $doctors[0]['doctors']['doctorslot'];
    $firstDoctor = $doctors[0]['doctors'];
    $firstDoctorSlotDetails = $doctors[0]['doctors']['slot-details'];
    
    if($firstDoctorSlot['timeslot']=='30min'){
        $slotTimes = 0; $slotCount = 30;
    }elseif($firstDoctorSlot['timeslot']=='60min'){
        $slotTimes = 1; $slotCount = 15;
    } 
?>
<div class="mc-doctor-profile-container window-h"><!--DOCTOR PROFILE CONTAINER-->
      <div class="mc-queue-container mc-fl"><!--mc-queue-container-->   
     <div class="mc-event-counter">
     <div class="queue-counter">
<!--     <div  class="queue-title mc-pad-t3">Queue Number</div>
     <?php //if($firstDoctorSlot['clinicsession']==1 || $firstDoctorSlot['clinicsession']==3){ ?>
        <div id="queueno" class="queue-number"><?php //echo count($firstDoctor['queue-booking'])+1;?></div>
        <div class="queue-title ">of <?php //echo $firstDoctorSlot['queuenumber'];?> Available</div>
        <div doctorslotid="<?php //echo $firstDoctorSlot['doctorslotid'];?>" book-date="<?php echo $firstDoctor['bookdate'];?>" class="mc-btn-booknow" id="queue-book" data-reveal-id="myModal">BOOK NOW</div>
     <?php //}else{ ?>  
        <div id="queueno" class="queue-number">0</div>
        <div  class="queue-title ">of 0 Available</div>
        <div class="mc-btn-booknow" data-reveal-id="myModal">BOOK NOW</div>
     <?php //} ?> -->
     <div  class="queue-title mc-pad-t3">Queue Number</div>
     <?php if($firstDoctorSlot['clinicsession']==1 || $firstDoctorSlot['clinicsession']==3){ 
            $totalQueue = $firstDoctorSlot['queuenumber'] + $firstDoctorSlot['queuecancelled'];
            if($totalQueue == count($firstDoctor['queue-booking'])){
                $nextQueue = 0;
            }else{
                $nextQueue = count($firstDoctor['queue-booking'])+1; 
            }
            if($firstDoctorSlot['queuestop'] !=1 && ArrayHelper::ActiveDate($firstDoctor['bookdate'])==1){
                if($nextQueue == 0){
                    echo '<div id="queueno" class="queue-number">0</div>';
                    echo '<div  class="queue-title ">of 0 Available</div>';
                    echo '<div class="mc-btn-booknow" data-reveal-id="myModal">BOOK NOW</div>';
                }else{
         ?>
        <div id="queueno" class="queue-number"><?php echo $nextQueue;?></div>
        <div class="queue-title ">of <?php echo $totalQueue;?> Available</div>
        <div doctorslotid="<?php echo $firstDoctorSlot['doctorslotid'];?>" book-date="<?php echo $firstDoctor['bookdate'];?>" class="mc-btn-booknow" id="queue-book" data-reveal-id="myModal">BOOK NOW</div>
        <?php } }else{ ?>  
        <div id="queueno" class="queue-number">0</div>
        <div  class="queue-title ">of 0 Available</div>
        <div class="mc-btn-booknow" data-reveal-id="myModal">BOOK NOW</div>
     <?php } }?>
     
        
     </div>
      <div class="clear"></div>
      </div>  
     <div class="queue-bar-container">
         
         
        <?php  
        if(!empty($doctors[0]['doctors']['queue-booking'])){ 
            foreach($doctors[0]['doctors']['queue-booking'] as $queueBook){
                if($queueBook['status'] == 0){ ?>
                    <div class="queue-bar-blue appoint-delete" bktype="0" id="<?php echo $queueBook['appointmentid'];?>" data-reveal-id="myModal">
                    <div class="client-name-dblue"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
                    <div class="client-remark " ><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div> 
                    </div>                          
        <?php    }elseif($queueBook['status'] == 1){ ?>
               <div class="queue-bar-dblue">              
               <div class="client-name-white"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
               </div>      
        <?php   }elseif($queueBook['status'] == 2){ ?>
                    <div class="queue-bar-blue">
                        <div class="client-name"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div>
                        <div class="client-remark">Concluded</div>
                    </div>
        <?php    }elseif($queueBook['status'] == 3){ ?>
                    <div class="queue-bar-gray appoint-delete" bktype="0" id="<?php echo $queueBook['appointmentid'];?>" sts="1" data-reveal-id="myModal">
                    <div class="client-name"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
                    <div class="client-remark"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div> 
                     <div class="client-remark">Canceled</div>
                    </div>
        <?php    }  } } ?>
          
<!--      <div class="mc-btn-stop" id="queue-stopped">Stop the Queue</div>-->
    <?php if($firstDoctorSlot['clinicsession']==1 || $firstDoctorSlot['clinicsession']==3 && ArrayHelper::ActiveDate($firstDoctor['bookdate'])==1){ 
            if($firstDoctorSlot['queuestop']!=1){
        ?>
        <div currenttotal="<?php echo count($firstDoctor['queue-booking']);?>" queuetotal="<?php echo $firstDoctorSlot['queuenumber'];?>" doctorslotid="<?php echo $firstDoctorSlot['doctorslotid'];?>" class="mc-btn-stop" id="queue-stopped">Stop the Queue</div>
    <?php }else{ ?>    
<!--        <div class="mc-btn-stop">Start the Queue</div>-->
        <div slotmanageid="<?php echo $firstDoctorSlot['slotmanageid'];?>" id="queue-started" class="mc-btn-stop">Start the Queue</div>
    <?php } }?>
        
      <div class="clear"></div>
      </div><!--mc-queue-container END-->
      
      </div><!--DOCTOR PROFILE CONTAINER END-->
      <div class="appointments mc-fl"><!--appointments-->
      <div class="mc-event-counter">
     <div class="appoint-counter">
     <div  class="queue-title ">Appointment By Time</div>
     <?php  
     $mycount = 1; $slotEstimatedTime =0;$slotDetailID=0;$slotDate=date('d-m-Y');
        if(!empty($doctors[0]['doctors']['slot-details'])){   
            foreach($doctors[0]['doctors']['slot-details'] as $slotBook){
                //if(empty($slotBook['appoint']) && $slotBook['slot']['available'] == 1 && ArrayHelper::ActiveTime($slotBook['slot']['time'],$firstDoctor['bookdate'])==1){
                if(empty($slotBook['appoint']) && $slotBook['slot']['available'] == 1 && ArrayHelper::ActivePlusTime($slotBook['slot']['time'],$firstDoctor['bookdate'])==1){    
                   if($mycount == 1){ 
                       $slotEstimatedTime = $slotBook['slot']['time'];
                       $slotDetailID = $slotBook['slot']['slotdetailid'];
                       $slotDate = $slotBook['slot']['date'];
                    ?>
                    <div class="queue-number"><?php echo substr($slotBook['slot']['time'], 0, -2);?></div>
                    <div  class="queue-title "><?php echo substr($slotBook['slot']['time'], -2);?></div>
                <?php   $mycount ++; 
                   }    
                }
            }   
        }
        if($mycount == 1){
            echo '<div class="queue-number">00</div>';
            echo '<div  class="queue-title ">No appointment available</div>';
        }
        ?>
     
                
     
<!--        <div class="queue-number">11.00</div>
        <div  class="queue-title ">Am</div>-->
        <?php if($mycount != 1){ ?>
        <div slottime="<?php echo $slotEstimatedTime;?>" slotdetailid="<?php echo $slotDetailID;?>" slotdate="<?php echo $slotDate;?>" class="mc-btn-booknow" id="slot-book" data-reveal-id="myModal">BOOK NOW</div>
        <?php }else{
            echo '<div  class="mc-btn-booknow" data-reveal-id="myModal">BOOK NOW</div>';
        }?>
     </div>
      <div class="clear"></div>
      
      
      <div class="appointments-calendar">
      <div class="event-container3">
      <div class="time-cal3"> <!-- Start time cal3 -->
        <!--<div class="event-time3 mc-mar-t0 time-width">
        <?php  
        //if(!empty($doctors[0]['doctors']['slot-details'])){ 
        //    foreach($doctors[0]['doctors']['slot-details'] as $slotBook){
        //        if(!empty($slotBook['appoint']) && $slotBook['appoint']['slotdetailid'] == $slotBook['appoint']['slotdetailid']){ ?>
                    <div class="time3"><?php //echo $slotBook['slot']['time'];?></div>
        <?Php  // }
         //   }
        //}?>
        </div> -->
        <!--  
        <div class="event-display mc-mar-t0">
        <?php  
        //if(!empty($doctors[0]['doctors']['slot-details'])){ 
        //    foreach($doctors[0]['doctors']['slot-details'] as $slotBook){
               
        //        if(!empty($slotBook['appoint']) && $slotBook['appoint']['slotdetailid'] == $slotBook['appoint']['slotdetailid']){
        //            if($slotBook['appoint']['status']==0){ ?>
                            <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                            <div class="day-event-blue">
                                <div class="client-name-dblue"><?php //echo $slotBook['appoint']['user']['name'];?></div> 
                                <div bktype="1" id="<?php //echo $slotBook['appoint']['appointid'];?>" class="client-remark appoint-delete"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div> 
                           </div>
                <?php //   }elseif($slotBook['appoint']['status']==1){ ?>
                            <div class="mc-dot2"> <img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                            <div class="day-event-dblue"> 
                             <div class="client-name-white"><?php //echo $slotBook['appoint']['user']['name'];?></div>
                            </div>
                <?php  // }elseif($slotBook['appoint']['status']==2){ ?>
                            <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}" alt=""/></div>
                            <div class="day-event-gray"> 
                             <div class="client-name"><?php //echo $slotBook['appoint']['user']['name'];?></div>
                             <div class="client-remark">Concluded</div>
                            </div>
                <?php  //  }elseif($slotBook['appoint']['status']==3){ ?>
                            <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                            <div class="day-event-white"> 
                              <div class="client-name-gray">Unavailable</div>          
                            </div>
                <?php  //  }
             //   }
                 
        //} } ?>    
                 
        </div> -->
        
        
        <div class="event-time3 mar-top24 time-width">
          <?php 
          if($slotTimes==0){
                $availableCount = 0;
                for($i=0; $i<$slotCount; $i++){
                    $getSearchSlot=0; $getTime =0;
                    $getTime = ArrayHelper::getTimeSlot($i);

                  for($a=0; $a< count($firstDoctorSlotDetails);$a++){
                      if (in_array($getTime, $firstDoctorSlotDetails[$a]['slot'])) {
                          $getSearchSlot = $firstDoctorSlotDetails[$a]['slot']['time'];
                      }
                  }
                  if($getSearchSlot == $getTime){
                      echo '<div class="time3">'.$getTime.'</div>';
                      $availableCount=0;
                  }else{
                      if($availableCount==0){
                        echo '<div class="time3">'.$getTime.'</div>';
                        $availableCount=1;
                      } 
                  }
                }
              ?>    

          <?php }elseif($slotTimes==1){    
                $availableCount = 0;
                for($i=0; $i<$slotCount; $i++){
                    $getSearchSlot=0; $getTime =0;
                    $getTime = ArrayHelper::getTimeSlot90($i);

                  for($a=0; $a< count($firstDoctorSlotDetails);$a++){
                      if (in_array($getTime, $firstDoctorSlotDetails[$a]['slot'])) {
                          $getSearchSlot = $firstDoctorSlotDetails[$a]['slot']['time'];
                      }
                  }
                  if($getSearchSlot == $getTime){
                      echo '<div class="time3">'.$getTime.'</div>';
                      $availableCount=0;
                  }else{
                      if($availableCount==0){
                        echo '<div class="time3">'.$getTime.'</div>';
                        $availableCount=1;
                      } 
                  }
                }
            
           } ?>
        </div>
        
        <div class="event-display mar-top24">
          <?php 
          $availableCount = 0;
          for($i=0; $i<$slotCount; $i++){
              $getSearchSlot=0; $getTime =0;
              //$getTime = ArrayHelper::getTimeSlot($i);
              if($slotTimes==0){$getTime = ArrayHelper::getTimeSlot($i);}elseif($slotTimes==1){$getTime = ArrayHelper::getTimeSlot90($i);}
              
            for($a=0; $a< count($firstDoctorSlotDetails);$a++){
                if (in_array($getTime, $firstDoctorSlotDetails[$a]['slot'])) {
                    $getSearchSlot = $firstDoctorSlotDetails[$a]['slot']['time'];
                    $currentSlotDetail = $firstDoctorSlotDetails[$a];
                }
            }
              
              
              if(!empty($firstDoctorSlotDetails)){
                  if($getSearchSlot == $getTime){ 

                        //if(!empty($firstDoctorSlotDetails[$i]['appoint']) && $firstDoctorSlotDetails[$i]['slot']['slotdetailid']==$firstDoctorSlotDetails[$i]['appoint']['slotdetailid'] && $firstDoctorSlotDetails[$i]['slot']['available'] == 2){
                        if(!empty($currentSlotDetail['appoint']) && $currentSlotDetail['slot']['slotdetailid']==$currentSlotDetail['appoint']['slotdetailid'] && $currentSlotDetail['slot']['available'] == 2){    
                            
                            if($currentSlotDetail['appoint']['status']==0){ ?>
                                <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}"   alt=""/></div>
                                <div slottime="<?php echo $currentSlotDetail['slot']['time'];?>" bktype="1" id="<?php echo $currentSlotDetail['appoint']['appointid'];?>" class="day-event-blue appoint-delete" data-reveal-id="myModal">
                                <div class="client-name-dblue"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div> <div class="client-remark"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div> 
                                </div> 
                    <?php   }elseif($currentSlotDetail['appoint']['status']==1){ ?>
                                <div class="mc-dot2"> <img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                                <div class="day-event-lblue"> 
                                <div class="client-name-white"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div>
                                </div>
                    <?php   }if($currentSlotDetail['appoint']['status']==2){ ?>
                                <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}"   alt=""/></div>
                                <div class="day-event-gray"> 
                                <div class="client-name"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div>
                                <div class="client-remark">Concluded</div>
                                </div>
                    <?php   }if($currentSlotDetail['appoint']['status']==3){ ?>
                                <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}"   alt=""/></div>
                                <div class="day-event-gray"> 
                                <div class="client-name"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div>
                                <div class="client-remark">Canceled</div>
                                </div>
                    <?php   }
                       
                        $availableCount=0;    
                        }else{ 
                            //if(ArrayHelper::ActiveTime($currentSlotDetail['slot']['time'],$firstDoctor['bookdate'])==0){
                            if(ArrayHelper::ActivePlusTime($currentSlotDetail['slot']['time'],$firstDoctor['bookdate'])==0){ ?>
                            <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}"   alt=""/></div>
                            <div class="day-event-gray"> 
                            <div class="client-name"><?php //echo $currentSlotDetail['appoint']['user']['name'];?></div>
                            <div class="client-remark">Expired</div>
                            </div>    
                        <?php }else{ ?>       
                            <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                            <div slotid="<?php echo $currentSlotDetail['slot']['slotdetailid'];?>" slottime="<?php echo $currentSlotDetail['slot']['time'];?>" data-reveal-id="myModal" class="day-event-dblue slot-popup"> <div class="client-name-white"></div>
                            </div>
                        <?php } $availableCount=0;   
                        } 
                  }else{ 
                    if($availableCount==0){ ?>
                    <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                    <div class="day-event-white"> <div class="client-name-gray">Unavailable</div></div>
            <?php   $availableCount= 1;} } }else{ if($availableCount==0){ ?>
                <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                <div class="day-event-white"> <div class="client-name-gray">Unavailable</div></div>
            <?php $availableCount=1; } } }?>  
        </div>
        
      </div><!-- End time cal3 -->
    </div>
      
      </div>
      </div><!--appointments end-->
      
      
      </div>
      </div> <!-- End doctor profile container -->
<div class="mc-clear"></div>

<div id="myModal" class="reveal-modal"> <!-- Start Booking page -->
    <form name="" action="" id="form-booking" method="POST">
    <div class="mc-doctor-profile-container"><!--DOCTOR PROFILE CONTAINER-->
   
      <div class="mc-shortform-container mc-fl"><!--mc-queue-container-->
        <div class="mc-short-form mc-mar-t4">
          <div class="short-img mc-fl"><img src="<?php echo $firstDoctor['image'];?>" width="80" height="80"  alt=""/></div>
          <div class="mc-fl"> 
          <div class="mc-header3 mc-label16 short-pd2"><?php echo $firstDoctor['name'];?></div>
           <div class="mc-header3 mc-label10 short-pd"><?php echo $firstDoctor['specialty'];?></div>
          </div>
        </div>
        <div class="mc-clear"></div>
        <div class="shortform-fields">
            <div class="short-form-title mc-fl"><label class="mc-label13">Patient Name</label></div> 
            <input id="user-name" name="user_name" class="shortform-input" type="text">
        </div>
		
        <div class="shortform-fields"> 
        <div class="short-form-title mc-fl"> <label class="mc-label13">NRIC</label></div> 
        <input id="user-nric" name="user_nric" class="shortform-input" type="text">
        </div>
        
        <div class="shortform-fields"> 
        <div class="short-form-title mc-fl"> <label class="mc-label13">Phone</label></div>  
        <input id="user-mobile" name="user_mobile" class="shortform-input" type="text">
        </div>
        
        <div class="shortform-fields"> 
        <div class="short-form-title mc-fl"> <label class="mc-label13">Email Address</label></div>  
        <input id="user-email" name="user_email" class="shortform-input" type="text">
        </div>
        
        <div class="amount-display">
            <label class="mc-label17 mc-fl" id="doctor-charge">
            <?php if(!empty($firstDoctorSlot)){ echo '$'.$firstDoctorSlot['consultationcharge']; }else{ echo '$00';}?>
            </label>
        <div class="mc-fl">
          <div class="mc-header3 mc-label16 short-pd3">Estimated</div>
          <div class="mc-header3 mc-label10 short-pd ">Consultation Cost</div>
        </div>
        </div>
        
        
        <div class="short-form-btn-container">
            <div slotdetailid="<?php echo $slotDetailID;?>" bookoption="" doctorslotid="<?php echo $firstDoctorSlot['doctorslotid'];?>" id="now-booking" class="mc-btn-booknow2 mc-mar-ls mc-fl">BOOK NOW</div>
<!--  
<div slotdate="<?php //echo $slotDate;?>" slotdetailid="<?php //echo $slotDetailID;?>" bookoption="" book-date="<?php //echo $firstDoctor['bookdate'];?>" doctorslotid="<?php //echo $firstDoctorSlot['doctorslotid'];?>" id="now-booking" class="mc-btn-booknow2 mc-mar-ls mc-fl">BOOK NOW</div>
<div class="mc-btn-cancel">Cancel</div>-->
        </div>
         <div class="mc-clear"></div>
        <div class="short-form-btn-container">
        <?php if(ArrayHelper::ActiveDate($firstDoctor['bookdate'])==1){ ?>    
        <div bktype="" deleteid="" class="mc-btn-stop mc-mar-ls" id="cancel-booking">Cancel Booking</div>
        <?php } ?>
        <div class="mc-blank"></div>
        </div>
      </div>
       
      
      <!--mc-queue-container END-->
      
       <div class="appointments mc-fl">
      <div class="mc-event-counter">
     <div id="queue-slot" class="queue-counter2">
        <div id="book-type-text" class="queue-title "></div>
        <div id="queue-no" class="queue-number book-type-q">
            <?php if($firstDoctorSlot['clinicsession']==1 || $firstDoctorSlot['clinicsession']==3){ echo count($firstDoctor['queue-booking'])+1; }?>
        </div>
        <div id="slot-time" class="queue-number book-type-a"><?php echo substr($slotEstimatedTime, 0, -2); ?></div>
        <div id="slot-time-peak" class="queue-title book-type-a"><?php echo substr($slotEstimatedTime, -2); ?></div>
        <div id="book-date" class="queue-title book-type-a"><br /><br><?php echo date('l jS F Y', strtotime($slotDate));?></div>
        <div id="book-date"  class="queue-title book-type-q">
<!--            of <?php //echo $firstDoctorSlot['queuenumber'];?> Available -->
            <?php echo date('l jS F Y', strtotime($firstDoctor['bookdate']));?>
            
        </div>
     </div>
      <div class="clear"></div>
      </div>   
      </div>    
      </div> <!-- End doctor profile container -->
    </form>
    <a class="close-reveal-modal">&#215;</a>
</div> <!-- End booking page -->