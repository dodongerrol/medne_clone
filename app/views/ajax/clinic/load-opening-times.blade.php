<?php //echo '<pre>'; print_r($clinictimes); echo '</pre>';?>
<div class="availability-day-graph">
   		  <div class="dr-available-title mar-left-2 fl">
             <label>DAYS OF THE WEEK</label>
             </div><!--END OF DR AVAILABLE TITLE-->
             
          <div class="dr-available-title fl padding-left-4 ">
                 <label>BUSINESS HOURS</label>
                 </div><!--END OF DR AVAILABLE TITLE-->
          <div class="clear"></div>
           
          
         <?php if($clinictimes){
             //if($clinictimes[0]->Status==1 || ($clinictimes[0]->Status==0 && strtotime(date('d-m-Y')) <= strtotime($clinictimes[0]->To_Date))){ 
                foreach($clinictimes as $clnTimes){ 
                    if($clnTimes->Type==0){
                        $clinicTime = "24 Hours";
                    }else{
                        $clinicTime = $clnTimes->StartTime.' - '.$clnTimes->EndTime ;
                    }
                    ?>
                    <div class="dr-time-bar-container ">
          		<div class="dr-detail-inner mar-t">
                            <label >{{StringHelper::GetOpenWeeks($clnTimes)}} </label>
                            <label class=" label2">{{$clinicTime}}</label>
                            <div id="delete-opening-times" clinictimeid="{{$clnTimes->ClinicTimeID}}" class="icn-close"><img width="17" height="21" alt="" src="{{ URL::asset('assets/images/icn-close-dark.png') }}"></div>
                           <div class="clear"></div>
       			</div>
                    </div><!--ND OF TIME BAR CONTAINER-->
                    <div class="clear"></div>
         <?php       }
         //}else{
         //   echo "No Times has been set";
        //}
         }else{
             echo "No Times has been set";
         }?>
          
         
</div><!--END OF AVAILABILITY DAY GRAPH-->