<?php //echo '<pre>'; print_r($loadarrays['doctortimes']); echo '</pre>'; ?>
<div class="availability-day-graph">
        <div class="dr-available-title new-mal4 fl">
            <label>DAYS OF THE WEEK</label>
        </div><!--END OF DR AVAILABLE TITLE-->
             
        <div class="dr-available-title fl padding-left-4 ">
            <label>AVAILABILITY</label>
            </div><!--END OF DR AVAILABLE TITLE-->
        <div class="clear"></div>
           
         <?php if($loadarrays['doctortimes']){ 
             foreach($loadarrays['doctortimes'] as $docTimes){
                 $mydoctortime = $docTimes->StartTime.' - '.$docTimes->EndTime ;?>
            <div class="dr-time-bar-container">
                <div class="dr-detail-inner">
                    <label >{{StringHelper::GetOpenWeeks($docTimes)}}</label>
                        <label class=" label2">{{$mydoctortime}}</label>
                        <div id="delete-opening-times" doctorid="{{ $docTimes->PartyID }}" clinictimeid="{{$docTimes->ClinicTimeID}}" class="icn-close"><img width="17" height="21" alt="" src="{{ URL::asset('assets/images/icn-close-dark.png') }}"></div>
                    <div class="clear"></div>
                </div>
            </div><!--END OF TIME BAR CONTAINER-->
             <?php } }else{
             echo 'No Doctor Availability Found';
         }?>   
           <div class="clear"></div>    
</div><!--END OF AVAILABILITY DAY GRAPH-->


<div class="time-option-container">
     <div id="load-repeat-ajax">
    <?php if($loadarrays['doctortimes'][0]){
        if($loadarrays['doctortimes'][0]->Repeat==0){
            //if(strtotime(date('d-m-Y')) <= strtotime($loadarrays['doctortimes'][0]->To_Date)){
            if(($loadarrays['doctortimes'][0]->Repeat==1 && $loadarrays['doctortimes'][0]->Status==1) || (strtotime($loadarrays['currentdate']) <= $loadarrays['doctortimes'][0]->To_Date)){
                echo '<div id="repeat-times-action" managetimeid="'.$loadarrays['doctortimes'][0]->ManageTimeID.'" repeatid="0" class="btn-update font-type-Montserrat  mar-left-2 ">Start Repeat</div>';
            }else{
                echo '<div class="field-type">
                <input class="check " id="checkbox1" type="checkbox" name="checkbox" ><label for="checkbox1">Repeat Weekly</label>
                </div>';
            }
        }else{
            echo '<div id="repeat-times-action" managetimeid="'.$loadarrays['doctortimes'][0]->ManageTimeID.'" repeatid="1" class="btn-cancel font-type-Montserrat ">Stop Repeat</div>';
        }
    }else{?>
      <div class="field-type">
        <input class="check " id="checkbox1" type="checkbox" name="checkbox" value="1" ><label for="checkbox1">Repeat Weekly</label>
    </div><!--END OF FIELD TYPE-->
    <?php } ?>
    
     </div>
        <div class="clear"></div>      
</div> <!--END OF TIME OPTION CONTAINER-->