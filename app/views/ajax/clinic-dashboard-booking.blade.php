<div class="appointments mc-fl"> <!-- Start Slot and Queue Section -->
        
      <?php if($clinicsesstion==2){ 
          if((strtotime($date) >= strtotime(date("d-m-Y")))){ $validDate = TRUE; }else{ $validDate = FALSE; }
            $slotDetailArrays = $slots;
            if($timeslot=='30min'){
                $slotTimes = 0; $slotCount = 30;
            }elseif($timeslot=='60min'){
                $slotTimes = 1; $slotCount = 15;
            }
            $displaydate =  date('l jS F Y', strtotime($date));  
        ?>  
      <div class="mc-event-counter">
        <div class="profile-image">
          <img src="<?php echo $image;?>" width="170" height="161"  alt=""/> </div>
              <div class="dr-detail-container">
                     <div class="txt-align">
                       <label class="mc-label7  height13"><?php echo $name;?></label>
                     </div>
                <div class="txt-align mar-top2">
                       <label class="mc-label8 "><?php echo $speciality;?></label>
                </div>
              </div>
          
          <!--<div class="appoint-counter mar-none">
             <div class="queue-number mc-fl ">11.00 </div>
             <div class="am-div mc-fl"> AM</div> 
            <div class="clear"></div> 
             <div class=" cd-popup-trigger mc-btn-booknow mrl21">Book Appointment</div>
          </div>-->
          <div class="appoint-counter mar-none">
            <?php  
            $mycount = 1; $slotEstimatedTime =0;$slotDetailID=0;
               if(!empty($slotDetailArrays)){     
                   foreach($slotDetailArrays as $slotBook){
                       if(empty($slotBook['appoint']) && $slotBook['slot']['available'] == 1 && ArrayHelper::ActiveTime($slotBook['slot']['time'],$slotBook['slot']['date'])==1){ 
                          if($mycount == 1){ 
                              //$slotEstimatedTime = $slotBook['slot']['time'];
                              $slotDetailID = $slotBook['slot']['slotdetailid'];
                              //$slotDate = $slotBook['slot']['date'];
                              //$displaydate =  date('l jS F Y', strtotime($slotDate));
                              $newslottime12 = date('h:i', strtotime(substr($slotBook['slot']['time'], 0, -2)));
                              $slotEstimatedTime = $newslottime12.substr($slotBook['slot']['time'], -2);
                           ?>
               <div class="queue-number mc-fl "><?php echo $newslottime12; //echo substr($slotBook['slot']['time'], 0, -2);?></div>
               <div class="am-div mc-fl"><?php echo substr($slotBook['slot']['time'], -2);?></div> 
               <?php   $mycount ++; 
                          }    
                       }
                   }

               }
               ?>
               <div class="clear"></div> 
                <?php //if($mycount != 1 && $validDate == TRUE){ 
                    if($mycount != 1){?>
                <div bookdetailpage="1" doctorslotid="<?php echo $doctorslotid;?>" displaydate="<?php echo $displaydate;?>" doccharge="<?php echo $consultation;?>" slottime="<?php echo $slotEstimatedTime;?>" slotdetailid="<?php echo $slotDetailID;?>" slotdate="<?php echo $date;?>"  data-reveal-id="clinicmyModal" class="mc-btn-booknow3 slot-book-new ipad-margin">Book Appointment</div>
                <?php }else{
                    echo '<div  class="mc-btn-booknow3 ipad-margin" data-reveal-id="myModal">Book Appointment</div>';
                }?>
             </div>
        <div class="clear"></div>
     </div> 
     
      <div class="appointments-calendar">
      <div class="event-container3 ">
      <div class="time-cal3"> 
        <!--<div class="event-time3 mar-top24 time-width">
          <div class="time3">8:30 pm</div>
      </div>-->
        <div class="event-time3 mar-top24 time-width">
            <?php 
          if($slotTimes==0){
                $availableCount = 0;
                for($i=0; $i<$slotCount; $i++){
                    $getSearchSlot=0; $getTime =0;
                    $getTime = ArrayHelper::getTimeSlot($i);
                    $getTime12 = ArrayHelper::getTimeSlot_By12($i);
                  for($a=0; $a< count($slotDetailArrays);$a++){
                      if (in_array($getTime, $slotDetailArrays[$a]['slot'])) {
                          $getSearchSlot = $slotDetailArrays[$a]['slot']['time'];
                      }
                  }
                  if($getSearchSlot == $getTime){
                      echo '<div class="time3">'.$getTime12.'</div>';
                      $availableCount=0;
                  }else{
                      if($availableCount==0){
                        echo '<div class="time3">'.$getTime12.'</div>';
                        $availableCount=1;
                      } 
                  }
                }
             }elseif($slotTimes==1){ 
              
                $availableCount = 0;
                for($i=0; $i<$slotCount; $i++){
                    $getSearchSlot=0; $getTime =0;
                    $getTime = ArrayHelper::getTimeSlot90($i);
                    $getTime12 = ArrayHelper::getTimeSlot_By12($i);
                  for($a=0; $a< count($slotDetailArrays);$a++){
                      if (in_array($getTime, $slotDetailArrays[$a]['slot'])) {
                          $getSearchSlot = $slotDetailArrays[$a]['slot']['time'];
                      }
                  }
                  if($getSearchSlot == $getTime){
                      echo '<div class="time3">'.$getTime12.'</div>';
                      $availableCount=0;
                  }else{
                      if($availableCount==0){
                        echo '<div class="time3">'.$getTime12.'</div>';
                        $availableCount=1;
                      } 
                  }
                }
             
           } 
           ?>
          </div>
          
          
        <div class="event-display mar-top24"> 
          <?php 
          $availableCount = 0;
          for($i=0; $i<$slotCount; $i++){
              $getSearchSlot=0; $getTime =0;
              if($slotTimes==0){$getTime = ArrayHelper::getTimeSlot($i);}elseif($slotTimes==1){$getTime = ArrayHelper::getTimeSlot90($i);}
              
            for($a=0; $a< count($slotDetailArrays);$a++){
                if (in_array($getTime, $slotDetailArrays[$a]['slot'])) {
                    $getSearchSlot = $slotDetailArrays[$a]['slot']['time'];
                    $currentSlotDetail = $slotDetailArrays[$a];
                }
            }
             
              if(!empty($slotDetailArrays)){
                  if($getSearchSlot == $getTime){ 
                      $newslottime12 = date('h:i', strtotime(substr($currentSlotDetail['slot']['time'], 0, -2)));
                       $slotEstimatedTime = $newslottime12.substr($currentSlotDetail['slot']['time'], -2);
                       
                        //if(!empty($firstDoctorSlotDetails[$i]['appoint']) && $firstDoctorSlotDetails[$i]['slot']['slotdetailid']==$firstDoctorSlotDetails[$i]['appoint']['slotdetailid'] && $firstDoctorSlotDetails[$i]['slot']['available'] == 2){
                        if(!empty($currentSlotDetail['appoint']) && $currentSlotDetail['slot']['slotdetailid']==$currentSlotDetail['appoint']['slotdetailid'] && $currentSlotDetail['slot']['available'] == 2){    
                            
                            if($currentSlotDetail['appoint']['status']==0){ ?>
                                <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                                <div class="day-event-blue wd85 clinic-appoint-delete" data-reveal-id="clinicmyModal" doctorslotid="<?php echo $doctorslotid;?>" bookdate="<?php echo $date;?>" id="<?php echo $currentSlotDetail['appoint']['appointid'];?>" slottime="<?php echo $slotEstimatedTime;?>" bktype="1">
                                    <div class="client-name-dblue"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div> 
                                    <!--<div class="client-remark"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div> -->
                                    <!--<div doctorslotid="<?php echo $doctorslotid;?>" bookdate="<?php echo $date;?>" id="<?php echo $currentSlotDetail['appoint']['appointid'];?>" slottime="<?php echo $slotEstimatedTime;?>" bktype="1" class="client-remark clinic-appoint-delete" data-reveal-id="clinicmyModal"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>-->
                                    <div class="client-remark " ><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>
                                </div>
             
            
                                <!--<div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}"   alt=""/></div>
                                <div class="day-event-blue wd85" >
                                    <div class="client-name-dblue"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div> 
                                    <div bookdate="<?php echo $date;?>" id="<?php echo $currentSlotDetail['appoint']['appointid'];?>" slottime="<?php echo $slotEstimatedTime;?>" bktype="1" class="client-remark clinic-appoint-delete" data-reveal-id="myModal"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>
                                </div>  -->
                                
                    <?php   }elseif($currentSlotDetail['appoint']['status']==1){ ?>
                                <div class="mc-dot2"> <img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                                <div class="day-event-lblue wd85 clinic-appoint-delete" data-reveal-id="clinicmyModal" doctorslotid="<?php echo $doctorslotid;?>" id="<?php echo $currentSlotDetail['appoint']['appointid'];?>" slottime="<?php echo $slotEstimatedTime;?>" bktype="1"> 
                                 <div class="client-name-white"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div>
                                 <!--<div doctorslotid="<?php echo $doctorslotid;?>" id="<?php echo $currentSlotDetail['appoint']['appointid'];?>" slottime="<?php echo $slotEstimatedTime;?>" bktype="1" class="client-remark clinic-appoint-delete" data-reveal-id="clinicmyModal"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>-->
                                 <div class="client-remark " ><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>
                                </div>
                                
                                
                                
                                <!---<div class="mc-dot2"> <img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                                <div class="day-event-lblue wd85"> 
                                <div class="client-name-white"><?php //echo $currentSlotDetail['appoint']['user']['name'];?></div>
                                <div id="<?php //echo $currentSlotDetail['appoint']['appointid'];?>" slottime="<?php echo $currentSlotDetail['slot']['time'];?>" bktype="1" class="client-remark clinic-appoint-delete" data-reveal-id="myModal"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>
                                </div>-->
                    <?php   }if($currentSlotDetail['appoint']['status']==2){ ?>
                                <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png.png') }}" width="11" height="10"  alt=""/></div>
                                <div doctorslotid="<?php echo $doctorslotid;?>" id="<?php echo $currentSlotDetail['appoint']['appointid'];?>" slottime="<?php echo $slotEstimatedTime;?>" bktype="1" sts="1" class="day-event-gray wd85 clinic-appoint-delete" data-reveal-id="clinicmyModal">
                                  <div class="client-name"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div>
                                  <div class="client-remark">Concluded</div>
                                </div>
                                
                                
                                
                                <!--<div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}"   alt=""/></div>
                                <div class="day-event-gray "> 
                                <div class="client-name"><?php //echo $currentSlotDetail['appoint']['user']['name'];?></div>
                                <div class="client-remark">Concluded</div>
                                </div>-->
                    <?php   }if($currentSlotDetail['appoint']['status']==3){ ?>
                                <!--<div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}"   alt=""/></div>
                                <div class="day-event-gray"> 
                                <div class="client-name"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div>
                                <div class="client-remark">Canceled</div>
                                </div>-->
                    <?php   }
                       
                        $availableCount=0;   
                        }else{ if(ArrayHelper::ActivePlusTime($currentSlotDetail['slot']['time'],$currentSlotDetail['slot']['date'])==0){ ?>
                            <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}" width="11" height="10"  alt=""/></div>
                            <div class="day-event-gray wd85">
                              <div class="client-name"></div>
                              <div class="client-remark">Expired</div>
                            </div>
                                
                                
                            <!---<div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}"   alt=""/></div>
                            <div class="day-event-gray"> 
                            <div class="client-name"><?php //echo $currentSlotDetail['appoint']['user']['name'];?></div>
                            <div class="client-remark">Expired</div>
                            </div>    -->
                        <?php }else{ ?>  
                                <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                                <div data-reveal-id="clinicmyModal" class="day-event-dblue wd85 slot-popup-clinic" doccharge="<?php echo $consultation;?>" displaydate="<?php echo $displaydate;?>" doctorslotid="<?php echo $doctorslotid;?>" slotid="<?php echo $currentSlotDetail['slot']['slotdetailid'];?>" slottime="<?php echo $slotEstimatedTime;?>"> 
                                    <div class="client-name-white">Available</div>
                                </div>
                            
                            
                            <!--<div class="mc-dot2 "><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                            <div doccharge="<?php echo $consultation;?>" displaydate="<?php echo $displaydate;?>" doctorslotid="<?php echo $doctorslotid;?>" slotid="<?php echo $currentSlotDetail['slot']['slotdetailid'];?>" slottime="<?php echo $currentSlotDetail['slot']['time'];?>" data-reveal-id="clinicmyModal" class="day-event-dblue slot-popup-clinic"> 
                            <div class="client-name-white"></div>
                            </div>-->
                        <?php }$availableCount=0;   
                        } 
                  }else{ 
                    if($availableCount==0){ ?>
                       <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}" width="10" height="10"  alt=""/></div>
                       <div class="day-event-white wd85"> <div class="client-name-gray">Unavailable</div></div>     
             
            <?php   $availableCount= 1;} } }else{ if($availableCount==0){ ?>
                        <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}" width="10" height="10"  alt=""/></div>
                       <div class="day-event-white wd85"> <div class="client-name-gray">Unavailable</div></div> 
            <?php $availableCount=1; } } }?>  
        </div>  
          
    
        
        
      </div>
    </div>
      
      </div>
        
        <?php }elseif($clinicsesstion==1){ 
            if((strtotime($date) >= strtotime(date("d-m-Y")))){ $validDate = TRUE; }else{ $validDate = FALSE; }
            if(empty($queue[0]['total'])) $queuetotal = 1; else $queuetotal = $queue[0]['total']+1;
                $clinicQueueCount = $queueno + $queue[0]['cancelled'];
          ?> 
      <div class="que-section"><!--que-section-->
            <div class="profile-image">
                <img src="<?php echo $image;?>" width="170" height="161"  alt=""/> 
            </div>
            <div class="dr-detail-container">
                <div class="txt-align">
                  <label class="mc-label7  height13"><?php echo $name;?></label>
                </div>
                <div class="txt-align mar-top2">
                    <label class="mc-label8 "><?php echo $speciality;?></label>
                </div>
            </div>
           <div class="appoint-counter">
               <?php 
                if($queuetotal <= $clinicQueueCount && $queuestop['status']!=1 && $validDate == TRUE){ ?>
               <div class="queue-number3 mc-fl ">
                <?php     echo $queuetotal.'/'.$clinicQueueCount; ?>
               </div>   
               <?php  }else{ ?>
               <div class="queue-number-stopped mc-fl ">
                <?php    echo "Stopped"; ?>
               </div>    
               <?php } ?>
<!--               <div class="queue-number3 mc-fl ">
                <?php 
                if($queuetotal <= $clinicQueueCount && $queuestop['status']!=1 && $validDate == TRUE){
                    echo $queuetotal.'/'.$clinicQueueCount;
                }else{
                    echo "Full";
                }
                ?>
               </div>-->
              <div class="clear"></div> 
              <div class="btn-wrapper3" >
              <?php 
                if($queuestop['status']!=1 && $queuetotal <= $clinicQueueCount && $validDate == TRUE){
              //if($queuestop['status']!=1){ ?>    
                  <div queueno="<?php echo $queuetotal;?>" class=" cd-popup-trigger mc-btn-booknow3 queue-book-new" id="" data-reveal-id="clinicmyModal">Book Queue</div>
              <?php }else{ ?>
                  <div  class=" cd-popup-trigger mc-btn-booknow3" >Book Queue</div>
              <?php  } ?>     
              </div>
           </div>
     
            <div class="clear"></div>
        
            <!-- start -->
            <?php  
            if(!empty($queue)){ 
                foreach($queue as $queueBook){
                    if($queueBook['status'] == 0){ ?>
                        <div class="queue-bar-blue wd75 mrl14 mrl19 wd100 clinic-appoint-delete" data-reveal-id="clinicmyModal" doctorslotid="<?php echo $doctorslotid;?>" bookdate="<?php echo $date;?>" bktype="0" id="<?php echo $queueBook['appointmentid'];?>">
                        <div class="client-name-dblue"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
                        <!--<div doctorslotid="<?php echo $doctorslotid;?>" bookdate="<?php echo $date;?>" bktype="0" id="<?php echo $queueBook['appointmentid'];?>" class="client-remark clinic-appoint-delete" data-reveal-id="clinicmyModal"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>--> 
                        <div class="client-remark " ><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>
                        </div>
            
            
                    <!--    <div class="queue-bar-blue " >
                        <div class="client-name-dblue"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
                        <div docname="<?php echo $name;?>" docimage="<?php echo $image;?>" docspeciality="<?php echo $speciality;?>" doctorid="<?php echo $doctorid;?>" clinicid="<?php echo $clinicid;?>" bookdate="<?php //echo $docDetails['date'];?>" bktype="0" id="<?php //echo $queueBook['appointmentid'];?>" class="client-remark clinic-appoint-delete" data-reveal-id="clinicmyModal"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>
                        </div>    -->                       
            <?php    }elseif($queueBook['status'] == 1){ ?>
                        <div class="queue-bar-dblue wd75 mrl14 mrl19 wd100 clinic-appoint-delete" data-reveal-id="clinicmyModal" doctorslotid="<?php echo $doctorslotid;?>" bookdate="<?php echo $date;?>" bktype="0" id="<?php echo $queueBook['appointmentid'];?>">
                        <div class="client-name-white"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
                        <!--<div doctorslotid="<?php echo $doctorslotid;?>" bookdate="<?php echo $date;?>" bktype="0" id="<?php echo $queueBook['appointmentid'];?>" class="client-remark clinic-appoint-delete" data-reveal-id="clinicmyModal"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>--> 
                        <div class="client-remark " ><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>
                        </div>
                    
                    
                    
                   <!--<div class="queue-bar-lblue2">              
                   <div class="client-name-white"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
                   <div docname="<?php //echo $docDetails['name'];?>" docimage="<?php //echo $docDetails['image'];?>" docspeciality="<?php //echo $docDetails['speciality'];?>" doctorid="<?php //echo $docDetails['doctorid'];?>" clinicid="<?php //echo $docDetails['clinicid'];?>" bookdate="<?php //echo $docDetails['date'];?>" bktype="0" id="<?php //echo $queueBook['appointmentid'];?>" class="client-remark clinic-appoint-delete" data-reveal-id="clinicmyModal"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>
                   </div>  -->    
            <?php   }elseif($queueBook['status'] == 2){ ?>
                    <div class="queue-bar-gray wd75 mrl14 mrl19 wd100 clinic-appoint-delete" doctorslotid="<?php echo $doctorslotid;?>" bookdate="<?php echo $date;?>" bktype="0" sts="1" id="<?php echo $queueBook['appointmentid'];?>" data-reveal-id="clinicmyModal">
                    <div class="client-name"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
                    <div class="client-remark"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div> 
                     <div class="client-remark">Concluded</div>
                    </div>
                   
                   
                    <!--    <div class="queue-bar-blue">
                            <div class="client-name"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div>
                            <div class="client-remark">Concluded</div>
                        </div> -->
            <?php    }elseif($queueBook['status'] == 3){ ?>
                        <div bookdate="<?php echo $date;?>" bktype="0" id="<?php echo $queueBook['appointmentid'];?>" sts="1" class="queue-bar-gray wd75 mrl14 mrl19 wd100 clinic-appoint-delete" data-reveal-id="clinicmyModal">
                        <div class="client-name"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
                        <div class="client-remark"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div> 
                         <div class="client-remark">Canceled</div>
                        </div>
                   
                   
                   
                    <!--    <div docname="<?php //echo $docDetails['name'];?>" docimage="<?php //echo $docDetails['image'];?>" docspeciality="<?php //echo $docDetails['speciality'];?>" doctorid="<?php //echo $docDetails['doctorid'];?>" clinicid="<?php //echo $docDetails['clinicid'];?>" bookdate="<?php //echo $docDetails['date'];?>" bktype="0" id="<?php //echo $queueBook['appointmentid'];?>" sts="1" class="queue-bar-gray clinic-appoint-delete" data-reveal-id="clinicmyModal">
                        <div class="client-name"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
                        <div class="client-remark"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div> 
                         <div class="client-remark">Canceled</div>
                        </div> -->
            <?php    }  } } ?>
            
            <!-- end -->
            
            
        
    
      <div class="clear"></div>
        <div class="btn-wrapper2 mrl21 btn-wrapper4 mrl22">
         <?php if($queuestop['status']==1){ ?>
     `   <div bookdetailpage="1" currentdate="<?php echo $date;?>" slotmanageid="<?php echo $queuestop['slotmanageid'];?>" doctorslotid="<?php echo $doctorslotid;?>" id="" class="mc-btn-stop5 mc-fl clinic-queue-started ipad-margin3">Start the Queue</div>
         <?php }else{ ?>
        <div bookdetailpage="1" currentdate="<?php echo $date;?>" currenttotal="<?php echo $queue[0]['total'];?>" queuetotal="<?php echo $queueno;?>" doctorslotid="<?php echo $doctorslotid;?>" id="" class="mc-btn-stop5 mc-fl clinic-queue-stopped ipad-margin3">Stop the Queue</div>
         <?php } ?> 
        <div class="clear"></div>
       </div>
      
      </div><!--end queue-section-->
      
      <?php } ?>
      
      <div class="clear"></div>
      
      
      </div> <!-- End Slot and Queue Section -->
  
      
      
      
    
  
  
  
  
  
  <!--queue numbers starts here -->
  <div class="mc-queue-container width50 mc-fl">
        <div class="day-display">
        <div class="gobal-mart global-marl">
          <label class="mc-label11"><?php echo date("l", strtotime($date));?>  </label>
          <div class="mc-clear"></div>
          <label class="mc-label11"><?php echo date("F d, Y", strtotime($date));?></label>
        </div>
        <div class="calendar-wrapper"><div class="DashboardBookingDatePicker" doctorslotid="<?php echo $doctorslotid;?>" id=""></div></div>
      </div>
 
</div>
   <!--queue numbers ends here -->
  
  
<!-- Start Booking popup--> 
<div id="clinicmyModal" class="reveal-modal"> <!-- Start Booking page -->
    <form name="" action="" id="form-booking" method="POST">
    <div class="mc-doctor-profile-container"><!--DOCTOR PROFILE CONTAINER-->
   
      <div class="mc-shortform-container mc-fl"><!--mc-queue-container-->
        <div class="mc-short-form mc-mar-t4">
            <div class="short-img mc-fl"><img id="docimage" src="<?php echo $image;?>" width="80" height="80"  alt=""/></div>
          <div class="mc-fl"> 
              <div class="mc-header3 mc-label16 short-pd2" id="docname"><?php echo $name;?></div>
              <div class="mc-header3 mc-label10 short-pd" id="docspeciality"><?php echo $speciality;?></div>
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
            <?php echo $consultation;//if(!empty($firstDoctorSlot)){ echo '$'.$firstDoctorSlot['consultationcharge']; }else{ echo '$00';}?>
            </label>
        <div class="mc-fl">
          <div class="mc-header3 mc-label16 short-pd3">Estimated</div>
          <div class="mc-header3 mc-label10 short-pd ">Consultation Cost</div>
        </div>
        </div>
        
        
        <div class="short-form-btn-container">
            <div bookdetailpage="1" nowdate="<?php echo $date;?>" slotdetailid="" bookoption="" doctorslotid="<?php echo $doctorslotid;?>" id="clinic-doctor-booking" class="mc-btn-booknow2 mc-mar-ls mc-fl">BOOK NOW</div>
        </div>
         <div class="mc-clear"></div>
        <div class="short-form-btn-container">
            <div bookdetailpage="1" bktype="" deleteid="" class="mc-btn-stop mc-mar-ls" id="cancel-clinic-booking">Cancel Booking</div>
        <div class="mc-blank"></div>
        </div>
      </div>
       
      
      <!--mc-queue-container END-->
      
       <div class="appointments2 mc-fl">
      <div class="mc-event-counter">
     <div id="queue-slot" class="queue-counter2">
        <div id="book-type-text" class="queue-title "></div>
        <div id="queue-no" class="queue-number5 book-type-q d-margine-controller">
            <?php //echo count($queue)+1; //if($firstDoctorSlot['clinicsession']==1 || $firstDoctorSlot['clinicsession']==3){ echo count($firstDoctor['queue-booking'])+1; }?>
        </div>
        <div id="slot-time" class="queue-number4 book-type-a"><?php //echo substr($slotEstimatedTime, 0, -2); ?></div>
        <div id="slot-time-peak" class="queue-title book-type-a"><?php //echo substr($slotEstimatedTime, -2); ?></div>
        <div id="book-date" class="queue-title book-type-a"><br /><br><?php //echo date('l jS F Y', strtotime($slotDate));?></div>
        <div id="book-date"  class="queue-title book-type-q">
            <?php echo date('l jS F Y', strtotime($date));?>
        </div>
        
     </div>
      <div class="clear"></div>
      </div>   
      </div>    
      </div> <!-- End doctor profile container -->
    </form>
    <a class="close-reveal-modal">&#215;</a>
</div> <!-- End booking page --> 