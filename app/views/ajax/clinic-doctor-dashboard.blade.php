<div class="mc-date-filter">
        <div class="mc-calendar4"> 
            <div class="mc-date-toggle-container3 mc-fl">    
              <div class="mc-date-blue mc-fl"><img src="{{ URL::asset('assets/images/icn-date-blue.png') }}" width="25" height="27" alt="img-date-blue" longdesc="{{ URL::asset('assets/images/icn-date-blue.png') }}"> </div>    
              <input id="datepicker_122" clinicid="<?php echo $clinicid;?>" class="input-d " type="text" value="<?php echo $displayDate;?>" >
            </div>
        </div>
    
    
     
    <div class="page-number2 mc-fr margin-control-new no-width">
       <?php if(!empty($totalDoctors)){
          for($i=0;$i<$totalDoctors/2; $i++){
              $pageno = $i+1;       
              $pagelink = $i*2;
              if($pagelink == $currentPage){ $pageselected = "num-selected";}else {$pageselected ="";}
              echo '<div clinicid="'.$doctors[0]['clinicid'].'" currentdate="'.$doctors[0]['date'].'" class="number move-page '.$pageselected.'" id="'.$pagelink.'">'.$pageno.'</div>';
          }
      }?>
      
    </div>
     <div class="clear"></div>
<!--     <div id="ajax-clinic-doctor-slider">  Ajax load page start -->
    
    <?php 
    if(!empty($doctors)){ 
        //echo '<pre>'; print_r($doctors); echo '</pre>';
        if((strtotime($currentDate) >= strtotime(date("d-m-Y")))){ $validDate = TRUE; }else{ $validDate = FALSE; }
    foreach($doctors as $docDetails){ 
        //echo '<pre>'; print_r($docDetails); echo '</pre>';
        if($docDetails['clinicsesstion']==2){
            
            if($docDetails['timeslot']=='30min'){
                $slotTimes = 0; $slotCount = 30;
            }elseif($docDetails['timeslot']=='60min'){
                $slotTimes = 1; $slotCount = 15;
            }
            $slotDetailArrays = $docDetails['slots'];
            $displaydate =  date('l jS F Y', strtotime($docDetails['date']));
        //echo '<pre>'; print_r($docDetails); echo '</pre>';
        ?>
    <!--slider should start here -->
    
    <div class="appointments mc-fl">
      <div class="mc-event-counter">
      <div class="profile-image">
        <img src="<?php echo $docDetails['image'];?>" width="170" height="161"  alt=""/> </div>
          
            <div class="dr-detail-container">
                   <div class="txt-align">
                     <label class="mc-label7  height13"> <?php echo $docDetails['name'];?></label>
                   </div>
              <div class="txt-align mar-top2">
                     <label class="mc-label8 "> <?php echo $docDetails['speciality'];?></label>
              </div>
            </div>
     <div class="appoint-counter mar-none">
     <?php  
     $mycount = 1; $slotEstimatedTime =0;$slotDetailID=0;$slotDate=date('d-m-Y');
        if(!empty($slotDetailArrays)){ 
        //if($doctors[0]['doctors']['slot-details'] !=null){     
            foreach($slotDetailArrays as $slotBook){
                if(empty($slotBook['appoint']) && $slotBook['slot']['available'] == 1 && ArrayHelper::ActiveTime($slotBook['slot']['time'],$slotBook['slot']['date'])==1){ 
                   if($mycount == 1){ 
                       //$slotEstimatedTime = $slotBook['slot']['time'];
                       $slotDetailID = $slotBook['slot']['slotdetailid'];
                       $slotDate = $slotBook['slot']['date'];
                       $displaydate =  date('l jS F Y', strtotime($slotDate));
                       $newslottime12 = date('h:i', strtotime(substr($slotBook['slot']['time'], 0, -2)));
                       $slotEstimatedTime = $newslottime12.substr($slotBook['slot']['time'], -2);
                    ?>
        <div class="queue-number mc-fl ipad-mar1"><?php echo $newslottime12;//echo substr($slotBook['slot']['time'], 0, -2);?></div>
        <div class="am-div mc-fl"><?php echo substr($slotBook['slot']['time'], -2);?></div> 
        <?php   $mycount ++; 
                   }    
                }
            }
            
        }
        ?>
        <div class="clear"></div>
        <?php if($mycount != 1 && $validDate == TRUE){ ?>
         
        <div class="btn-wrapper btn-margin " >
        <div clinicid="<?php echo $docDetails['clinicid'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" doctorslotid="<?php echo $docDetails['doctorslotid'];?>" displaydate="<?php echo $displaydate;?>" doccharge="<?php echo $docDetails['consultation'];?>" docimage="<?php echo $docDetails['image'];?>" docspeciality="<?php echo $docDetails['speciality'];?>" docname="<?php echo $docDetails['name'];?>" slottime="<?php echo $slotEstimatedTime;?>" slotdetailid="<?php echo $slotDetailID;?>" slotdate="<?php echo $slotDate;?>"  data-reveal-id="clinicmyModal" class="mc-btn-booknow7 mc-fl slot-book-new ipad-margin">Book Appointment</div>
        <a href="{{URL::to('/app/clinic/dashboard-booking/'.$docDetails['doctorslotid'])}}"><div class=" mc-btn-booknow3 mc-fl">Pick Another Date</div></a>
        <?php }else{ ?>
        <div class="btn-wrapper btn-margin " >
            <div  class="mc-btn-booknow7 mc-fl ipad-margin" data-reveal-id="myModal">Book Appointment</div>
            <a href="{{URL::to('/app/clinic/dashboard-booking/'.$docDetails['doctorslotid'])}}"><div class=" mc-btn-booknow3 mc-fl">Pick Another Date</div></a>
       <?php }?>
        </div>
       <!-- <div  class="queue-title ">Am</div>-->
    <!--   <div class="clear"></div> 
        <div class=" cd-popup-trigger mc-btn-booknow">Book Appointment</div>-->
     </div>
      <div class="clear"></div>
     </div>
     
     
      <div class="appointments-calendar">
         <!-- <div class=" empty-label-slot wd10"> Be the first to book </div> -->
      <div class="event-container3 ">
      <div class="time-cal3"> 
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
                    $getTime12 = ArrayHelper::getTimeSlot90_By12($i);
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
<!--          <div class="time3">9:00 am</div> -->
          </div>
          
          
        <!--<div class="event-display mar-top24">-->
            
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
                                <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}"   alt=""/></div>
                                <div class="day-event-blue wd85 clinic-appoint-delete" data-reveal-id="clinicmyModal" docname="<?php echo $docDetails['name'];?>" docimage="<?php echo $docDetails['image'];?>" docspeciality="<?php echo $docDetails['speciality'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" bookdate="<?php echo $docDetails['date'];?>" id="<?php echo $currentSlotDetail['appoint']['appointid'];?>" slottime="<?php echo $slotEstimatedTime;?>" bktype="1">
                                    <div class="client-name-dblue"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div> 
                                    <!--<div docname="<?php echo $docDetails['name'];?>" docimage="<?php echo $docDetails['image'];?>" docspeciality="<?php echo $docDetails['speciality'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" bookdate="<?php echo $docDetails['date'];?>" id="<?php echo $currentSlotDetail['appoint']['appointid'];?>" slottime="<?php echo $slotEstimatedTime;?>" bktype="1" class="client-remark clinic-appoint-delete" data-reveal-id="clinicmyModal"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>-->
                                <div class="client-remark " ><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>
                                </div> 
                    <?php   }elseif($currentSlotDetail['appoint']['status']==1){ ?>
                                <div class="mc-dot2"> <img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                                <div class="day-event-lblue wd85 clinic-appoint-delete" data-reveal-id="clinicmyModal" docname="<?php echo $docDetails['name'];?>" docimage="<?php echo $docDetails['image'];?>" docspeciality="<?php echo $docDetails['speciality'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" bookdate="<?php echo $docDetails['date'];?>" id="<?php echo $currentSlotDetail['appoint']['appointid'];?>" slottime="<?php echo $slotEstimatedTime;?>" bktype="1"> 
                                <div class="client-name-white"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div>
                                <!--<div id="<?php echo $currentSlotDetail['appoint']['appointid'];?>" slottime="<?php echo $slotEstimatedTime;?>" bktype="1" class="client-remark clinic-appoint-delete" data-reveal-id="clinicmyModal"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>-->
                                <div class="client-remark" ><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>
                                </div>
                    <?php   }if($currentSlotDetail['appoint']['status']==2){ ?>
                                <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}"   alt=""/></div>
                                <div id="<?php echo $currentSlotDetail['appoint']['appointid'];?>" bktype="1" sts="1" docname="<?php echo $docDetails['name'];?>" docimage="<?php echo $docDetails['image'];?>" doccharge="<?php echo $docDetails['consultation'];?>" displaydate="<?php echo $displaydate;?>" docspeciality="<?php echo $docDetails['speciality'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" doctorslotid="<?php echo $docDetails['doctorslotid'];?>" slotid="<?php echo $currentSlotDetail['slot']['slotdetailid'];?>" slottime="<?php echo $slotEstimatedTime;?>" data-reveal-id="clinicmyModal" class="day-event-gray wd85 clinic-appoint-delete">
                                <div class="client-name"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div>
                                <div class="client-remark">Concluded</div>
                                </div>
                                
                                
                                <!--<div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}"   alt=""/></div>
                                <div class="day-event-gray wd85"> 
                                <div class="client-name"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div>
                                <div class="client-remark">Concluded</div>
                                </div>-->
                    <?php   }if($currentSlotDetail['appoint']['status']==3){ ?>
                                <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}"   alt=""/></div>
                                <div class="day-event-gray wd85"> 
                                <div class="client-name"><?php echo $currentSlotDetail['appoint']['user']['name'];?></div>
                                <div class="client-remark">Canceled</div>
                                </div>
                    <?php   }
                       
                        $availableCount=0;   
                        }else{ if(ArrayHelper::ActivePlusTime($currentSlotDetail['slot']['time'],$currentSlotDetail['slot']['date'])==0){ ?>
                            <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot.png') }}"   alt=""/></div>
                            <div class="day-event-gray wd85"> 
                            <div class="client-name"><?php //echo $currentSlotDetail['appoint']['user']['name'];?></div>
                            <div class="client-remark">Expired</div>
                            </div>    
                        <?php }else{ ?>        
                            <div class="mc-dot2 "><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                            <div docname="<?php echo $docDetails['name'];?>" docimage="<?php echo $docDetails['image'];?>" doccharge="<?php echo $docDetails['consultation'];?>" displaydate="<?php echo $displaydate;?>" docspeciality="<?php echo $docDetails['speciality'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" doctorslotid="<?php echo $docDetails['doctorslotid'];?>" slotid="<?php echo $currentSlotDetail['slot']['slotdetailid'];?>" slottime="<?php echo $slotEstimatedTime;?>" data-reveal-id="clinicmyModal" class="day-event-dblue wd85 slot-popup-clinic"> 
                            <div class="client-name-white">Available</div>
                            </div>
                        <?php }$availableCount=0;   
                        } 
                  }else{ 
                    if($availableCount==0){ ?>
                    <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                    <div class="day-event-white wd85"> <div class="client-name-gray">Unavailable</div></div>
            <?php   $availableCount= 1;} } }else{ if($availableCount==0){ ?>
                <div class="mc-dot2"><img src="{{ URL::asset('assets/images/mc-dot-blue.png') }}" width="10" height="10"  alt=""/></div>
                <div class="day-event-white wd85"> <div class="client-name-gray">Unavailable</div></div>
            <?php $availableCount=1; } } }?>  
        </div>
            
         
        
      </div>
          <div class="clear"></div>
    </div>
      
      </div>
       <div class="clear"></div>
       <!--<div class="btn-wrapper2">
           <a href="{{URL::to('/app/clinic/dashboard-booking/'.$docDetails['doctorslotid'])}}">
               <div class=" mc-btn-booknow margine-control-ld">Pick Another Date</div></a>
       </div>-->
       
       <div class="btn-wrapper2 gobal-marl">
         <!--<a href="{{URL::to('/app/clinic/dashboard-booking/'.$docDetails['doctorslotid'])}}">  
             <div class=" mc-btn-booknow3 mc-fl ipad-margin2 ">Pick Another Date</div></a>-->
         <!--div class=" mc-btn-stop2 mc-fl">Stop the Queue</div>-->
         <div class="clear"></div>
       </div>
      </div>
  
        <?php }elseif($docDetails['clinicsesstion']==1){ 
                if(empty($docDetails['queue'][0]['total'])) $queuetotal = 1; 
                else $queuetotal = $docDetails['queue'][0]['total']+1;
                $doctorQueue = $docDetails['queue'];
                $clinicQueueCount = $docDetails['queueno'] + $docDetails['queue'][0]['cancelled'];
            ?>
  
  
  <!--queue numbers starts here -->
  
  
  <div class="mc-queue-container width50 mar-top3 mc-fl">
     
     
     <div class="profile-image mar-l">
        <img width="170" height="161" class="mar-l" alt="" src="<?php echo $docDetails['image'];?>"> </div>
     <div class="dr-detail-container mar-l2">
                   <div class="txt-align">
                     <label class="mc-label7  height13"><?php echo $docDetails['name'];?></label>
                   </div>
              <div class="txt-align mar-top2">
                     <label class="mc-label8 "><?php echo $docDetails['speciality'];?></label>
              </div>
            </div>
      
     <div class="mc-event-counter">
     <div class="appoint-counter width95">
     
<!--        <div class="queue-number3 mc-fl ">
            <?php 
            if($queuetotal <= $clinicQueueCount && $validDate == TRUE && $docDetails['queuestop']['status']!=1){
                //echo '03/20';
                echo $queuetotal.'/'.$clinicQueueCount;
            }else{
                echo "Stopped";
            }
            ?>
        </div>-->
        <?php 
            if($queuetotal <= $clinicQueueCount && $validDate == TRUE && $docDetails['queuestop']['status']!=1){ ?>
            <div class="queue-number3 mc-fl ">
            <?php   echo $queuetotal.'/'.$clinicQueueCount; ?>
            </div>    
            <?php }else{ ?>
            <div class="queue-number-stopped mc-fl ">
            <?php    echo "Stopped"; ?>
            </div>    
            <?php } ?>
         
       
       <div class="clear"></div> 
       <div class="btn-wrapper btn-margin2">
        <?php 
            if($queuetotal <= $clinicQueueCount && $validDate == TRUE && $docDetails['queuestop']['status']!=1){
        ?>
        <div queueno="<?php echo $queuetotal;?>" docname="<?php echo $docDetails['name'];?>" docimage="<?php echo $docDetails['image'];?>" docspeciality="<?php echo $docDetails['speciality'];?>" doccharge="<?php echo $docDetails['consultation'];?>" doctorslotid="<?php echo $docDetails['doctorslotid'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" book-date="<?php echo $docDetails['date'];?>" class="mc-btn-booknow7 queue-book-new mc-fl" id="" data-reveal-id="clinicmyModal">BOOK NOW</div>
        <a href="{{URL::to('/app/clinic/dashboard-booking/'.$docDetails['doctorslotid'])}}"><div class=" mc-btn-booknow3 mc-fl">Pick Another Date</div></a>
            <?php }else{ ?>
        <div  class="mc-btn-booknow7 mc-fl" id="" data-reveal-id="myModal">BOOK NOW</div>
        <a href="{{URL::to('/app/clinic/dashboard-booking/'.$docDetails['doctorslotid'])}}"><div class=" mc-btn-booknow3 mc-fl">Pick Another Date</div></a>
            <?php } ?>
       </div>
     </div>
      <div class="clear"></div>
      </div>
      
      
      <!--<div class=" empty-label-q wd10"> Be the first to book  </div>-->
      
      
      
      
     <div class="event-container4 mt5"> 
<!--     <div class="queue-bar-container ">-->
         <?php  
        if(!empty($doctorQueue)){ 
            foreach($doctorQueue as $queueBook){
                if($queueBook['status'] == 0){ ?>
                    <div class="queue-bar-blue wd10 clinic-appoint-delete" data-reveal-id="clinicmyModal" docname="<?php echo $docDetails['name'];?>" docimage="<?php echo $docDetails['image'];?>" docspeciality="<?php echo $docDetails['speciality'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" bookdate="<?php echo $docDetails['date'];?>" bktype="0" id="<?php echo $queueBook['appointmentid'];?>">
                    <div class="client-name-dblue"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
                    <!--<div docname="<?php echo $docDetails['name'];?>" docimage="<?php echo $docDetails['image'];?>" docspeciality="<?php echo $docDetails['speciality'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" bookdate="<?php echo $docDetails['date'];?>" bktype="0" id="<?php echo $queueBook['appointmentid'];?>" class="client-remark clinic-appoint-delete" data-reveal-id="clinicmyModal"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>-->
                    <div  class="client-remark" ><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>
                    </div>                          
        <?php    }elseif($queueBook['status'] == 1){ ?>
               <div class="queue-bar-lblue2 wd10 clinic-appoint-delete" data-reveal-id="clinicmyModal" docname="<?php echo $docDetails['name'];?>" docimage="<?php echo $docDetails['image'];?>" docspeciality="<?php echo $docDetails['speciality'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" bookdate="<?php echo $docDetails['date'];?>" bktype="0" id="<?php echo $queueBook['appointmentid'];?>">              
               <div class="client-name-white"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
               <!--<div docname="<?php echo $docDetails['name'];?>" docimage="<?php echo $docDetails['image'];?>" docspeciality="<?php echo $docDetails['speciality'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" bookdate="<?php echo $docDetails['date'];?>" bktype="0" id="<?php echo $queueBook['appointmentid'];?>" class="client-remark clinic-appoint-delete" data-reveal-id="clinicmyModal"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>-->
               <div class="client-remark"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div>
               </div>      
        <?php   }elseif($queueBook['status'] == 2){ ?>
                    <div docname="<?php echo $docDetails['name'];?>" docimage="<?php echo $docDetails['image'];?>" docspeciality="<?php echo $docDetails['speciality'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" bookdate="<?php echo $docDetails['date'];?>" bktype="0" id="<?php echo $queueBook['appointmentid'];?>" sts="1" class="queue-bar-gray wd10 clinic-appoint-delete" data-reveal-id="clinicmyModal">
                    <div class="client-name"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
                    <div class="client-remark"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div> 
                     <div class="client-remark">Concluded</div>
                    </div>


                    <!--<div class="queue-bar-blue wd10">
                        <div class="client-name"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div>
                        <div class="client-remark">Concluded</div>
                    </div>-->
        <?php    }elseif($queueBook['status'] == 3){ ?>
                    <div docname="<?php echo $docDetails['name'];?>" docimage="<?php echo $docDetails['image'];?>" docspeciality="<?php echo $docDetails['speciality'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" bookdate="<?php echo $docDetails['date'];?>" bktype="0" id="<?php echo $queueBook['appointmentid'];?>" sts="1" class="queue-bar-gray clinic-appoint-delete wd10" data-reveal-id="clinicmyModal">
                    <div class="client-name"><?php echo $queueBook['bookno'].'. '.$queueBook['user']['name'];?></div> 
                    <div class="client-remark"><img src="{{ URL::asset('assets/images/icn-close-white.png') }}" width="16" height="18"  alt=""/></div> 
                     <div class="client-remark">Canceled</div>
                    </div>
        <?php    }  } } ?>
    

        <?php if($docDetails['clinicsesstion']==1 ){ 
                //if($docDetails['queuestop']!=1){
            ?>
<!--            <div currenttotal="<?php //echo count($firstDoctor['queue-booking']);?>" queuetotal="<?php //echo $firstDoctorSlot['queuenumber'];?>" doctorslotid="<?php //echo $firstDoctorSlot['doctorslotid'];?>" class="mc-btn-stop" id="doctor-queue-stopped">Stop the Queue</div>-->
        <?php //}else{ ?>    
<!--            <div slotmanageid="<?php ///echo $firstDoctorSlot['slotmanageid'];?>" id="doctor-queue-started" class="mc-btn-stop">Start the Queue</div>-->
        <?php } //}?>
      
      <div class="clear"></div>
         
      <div class="clear"></div>
            <!--<a href="{{URL::to('/app/clinic/dashboard-booking/'.$docDetails['doctorslotid'])}}">
            <div class=" mc-btn-booknow margine-control-ld">Pick Another Date</div></a>-->
<!--       <div class=" mc-btn-booknow margine-control-ld">Pick Another Date</div>-->
    <!--   <?php if($docDetails['queuestop']['status']==0){ ?>
           <div currentpage="<?php echo $currentPage;?>" currentdate="<?php echo $docDetails['date'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" currenttotal="<?php echo $docDetails['queue'][0]['total'];?>" queuetotal="<?php echo $docDetails['queueno'];?>" doctorslotid="<?php echo $docDetails['doctorslotid'];?>" class="mc-btn-stop2 margine-control-ld" id="clinic-queue-stopped">Stop the Queue</div>
       <?php }else{ ?>
           <div currentpage="<?php echo $currentPage;?>" currentdate="<?php echo $docDetails['date'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" slotmanageid="<?php echo $docDetails['queuestop']['slotmanageid'];?>" id="clinic-queue-started" class="mc-btn-stop2 margine-control-ld">Start the Queue</div>
       <?php }?> -->
       
       
           
       <div class="btn-wrapper4">
       <!--<a href="{{URL::to('/app/clinic/dashboard-booking/'.$docDetails['doctorslotid'])}}">    
       <div class=" mc-btn-booknow3 mc-fl">Pick Another Date</div></a>-->
       <!--<div class=" mc-btn-stop2 mc-fl">Stop the Queue</div>-->
       <?php if($docDetails['queuestop']['status']==0){ ?>
           <div currentpage="<?php echo $currentPage;?>" currentdate="<?php echo $docDetails['date'];?>" doctorid="<?php echo $docDetails['doctorid'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" currenttotal="<?php echo $docDetails['queue'][0]['total'];?>" queuetotal="<?php echo $docDetails['queueno'];?>" doctorslotid="<?php echo $docDetails['doctorslotid'];?>" class="mc-btn-stop2 mc-fl clinic-queue-stopped" id="">Stop the Queue</div>
       <?php }else{ ?>
           <div currentpage="<?php echo $currentPage;?>" currentdate="<?php echo $docDetails['date'];?>" clinicid="<?php echo $docDetails['clinicid'];?>" slotmanageid="<?php echo $docDetails['queuestop']['slotmanageid'];?>" id="" class="mc-btn-stop2 mc-fl clinic-queue-started">Start the Queue</div>
       <?php }?>
       <div class="clear"></div>
      </div>
<!--     </div> -->
     </div>
      
      </div>
  
        <?php }  }
    }else{      
        echo 'No doctors available in this date';
    } ?>
  <!--queue numbers ends here -->
  
    
    <!--slider should end here -->
<!--    </div>  Ajax load page end -->
   
    
    <div class="clear"></div>
    <div class="page-number">
      <?php if(!empty($totalDoctors)){
          for($i=0;$i<$totalDoctors/2; $i++){
              $pageno = $i+1;       
              $pagelink = $i*2;
              if($pagelink == $currentPage){ $pageselected = "num-selected";}else {$pageselected ="";}
              echo '<div clinicid="'.$docDetails['clinicid'].'" currentdate="'.$docDetails['date'].'" class="number move-page '.$pageselected.'" id="'.$pagelink.'">'.$pageno.'</div>';
          }
      }?>  
    </div>
  </div>
  
<!-- Start Booking popup--> 
<div id="clinicmyModal" class="reveal-modal"> <!-- Start Booking page -->
    <form name="" action="" id="form-booking" method="POST">
    <div class="mc-doctor-profile-container"><!--DOCTOR PROFILE CONTAINER-->
   
      <div class="mc-shortform-container mc-fl"><!--mc-queue-container-->
        <div class="mc-short-form mc-mar-t4">
            <div class="short-img mc-fl"><img id="docimage" src="<?php //echo $firstDoctor['image'];?>" width="80" height="80"  alt=""/></div>
          <div class="mc-fl"> 
              <div class="mc-header3 mc-label16 short-pd2" id="docname"><?php //echo $firstDoctor['name'];?></div>
              <div class="mc-header3 mc-label10 short-pd" id="docspeciality"><?php //echo $firstDoctor['specialty'];?></div>
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
            <?php //if(!empty($firstDoctorSlot)){ echo '$'.$firstDoctorSlot['consultationcharge']; }else{ echo '$00';}?>
            </label>
        <div class="mc-fl">
          <div class="mc-header3 mc-label16 short-pd3">Estimated</div>
          <div class="mc-header3 mc-label10 short-pd ">Consultation Cost</div>
        </div>
        </div>
        
        
        <div class="short-form-btn-container">
            <div currentpage="<?php echo $currentPage;?>" clinicid="" doctorid="" nowdate="" slotdetailid="" bookoption="" doctorslotid="" id="clinic-doctor-booking" class="mc-btn-booknow2 mc-mar-ls mc-fl">BOOK NOW</div>
        </div>
         <div class="mc-clear"></div>
        <div class="short-form-btn-container">
            <div currentpage="<?php echo $currentPage;?>" bktype="" deleteid="" class="mc-btn-stop mc-mar-ls" id="cancel-clinic-booking">Cancel Booking</div>
        <div class="mc-blank"></div>
        </div>
      </div>
       
      
      <!--mc-queue-container END-->
      
       <div class="appointments2 mc-fl">
      <div class="mc-event-counter">
     <div id="queue-slot" class="queue-counter2">
        <div id="book-type-text" class="queue-title "></div>
        <div id="queue-no" class="queue-number5 book-type-q d-margine-controller">
            <?php //if($firstDoctorSlot['clinicsession']==1 || $firstDoctorSlot['clinicsession']==3){ echo count($firstDoctor['queue-booking'])+1; }?>
        </div>
        <div id="slot-time" class="queue-number4 book-type-a"><?php //echo substr($slotEstimatedTime, 0, -2); ?></div>
        <div id="slot-time-peak" class="queue-title book-type-a"><?php //echo substr($slotEstimatedTime, -2); ?></div>
        <div id="book-date" class="queue-title book-type-a"><br /><br><?php //echo date('l jS F Y', strtotime($slotDate));?></div>
        <div id="book-date"  class="queue-title book-type-q">
            <?php //echo date('l jS F Y', strtotime($firstDoctor['bookdate']));?>
        </div>
        
     </div>
      <div class="clear"></div>
      </div>   
      </div>    
      </div> <!-- End doctor profile container -->
    </form>
    <a class="close-reveal-modal">&#215;</a>
</div> <!-- End booking page --> 
<!-- End Booking popup-->  