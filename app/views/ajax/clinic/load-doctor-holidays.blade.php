<div class="holiday-graph">
   		  <div class="holiday-title mar-left-2 fl">
             <label>DATE</label>
             </div><!--END OF DR AVAILABLE TITLE-->
             
          <div class="holiday-title2 fl padding-left-4 ">
                 <label>UNAVAILABLE</label>
                 </div><!--END OF DR AVAILABLE TITLE-->
          <div class="clear"></div>
           
        <?php if($loadarrays['doctorholidays']) {
            foreach($loadarrays['doctorholidays'] as $docHolidays){ 
                if($docHolidays->Type==0){
                    $holidaytimes = "Full Day";
                }else{
                    $holidaytimes = $docHolidays->From_Time.' - '.$docHolidays->To_Time;
                }
                
                ?>
                <div class="holiday-bar-container">
                    <div class="dr-detail-inner">
                        <label>{{$docHolidays->Holiday}}</label>
                            <label class=" label2">{{$holidaytimes}}</label>
                            <div id="delete-clinic-holiday" doctorin="1" partyid="{{$docHolidays->PartyID}}" clinicholidayid="{{$docHolidays->ManageHolidayID}}" class="icn-close"><img width="17" height="21" src="{{ URL::asset('assets/images/icn-close-dark.png') }}" alt=""></div>
                        <div class="clear"></div>
                    </div>
                 <div class="clear"></div>
                </div><!--END OF TIME BAR CONTAINER-->
        <?php     }
        }else{
            echo 'No Holidays found';
        }?>  
          <div class="clear"></div>    
</div>