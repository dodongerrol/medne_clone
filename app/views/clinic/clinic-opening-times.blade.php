@include('common.header-clinic-section')
<!--END HEADER-->
<?php //echo '<pre>'; print_r($clinictimes); echo'</pre>';  ?>
  <div class="clear"></div>
<script type="text/javascript">		
$(function() {
     $("#date-holiday").datepicker({
        dateFormat: "DD, d MM, yy"
        //dateFormat: "dd-mm-yy"
    }).datepicker("setDate", "0");	
//$( "#datepicker2" ).datepicker({dateFormat: 'DD, d MM, yy',});

});		 

</script> 
<style>
.ui-datepicker {
    display: none; left: 30.7% !important; padding: 0.2em 0.2em 0; position: absolute !important; width: 11em !important; z-index: 9999 !important;
}
</style>
  
<div id="clinic-form-container">
    <!--START OF FORM NAV-->

    @include('clinic.clinic-nav-section')

    <!--END OF FORM NAV-->

 <div class="form-nav-page">
     <div id="nortifications-section"></div>
  <div class="opening-hrs mar-left mar-t3">
      
  <div class="open--title">OPENING HOURS</div>
<div class="radio-btn-container">
        <div class="radio">
            <input type="radio" value="0" name="radio" id="opening-times-24">
            <label for="radio1"><span><span></span></span>Open 24 Hours</label>
        </div> 
   </div>
        
     <div class="radio-btn-container">
        <div class="radio">
          <input type="radio" value="1" name="radio" id="opening-times-custom">
          <label for="radio2"><span><span></span></span>Open on selected hours</label>
        </div> 
     </div>
 </div>
  <!--END OF OPENING HRS-->
  
  
  
<div class="clear"></div>
   
   <!--END OF SEARCH BAR-->
   <div class="clear"></div>
   
   <div class="dr-available-select-container">
     <div class="dr-available-day">
     <label>SELECT DAYS</label>
     </div><!--END OF DR AVAILABLE DAY-->
      <div class="clear"></div>
     <div class="selection-available-day">
       <div id="weekmon" week="0" class="week-selection day-box-gray">MON</div>
       <div id="weektus" week="0" class="week-selection day-box-gray">TUE</div>
       <div id="weekwed" week="0" class="week-selection day-box-gray">WED</div>
       <div id="weekthu" week="0" class="week-selection day-box-gray">THU</div>
       <div id="weekfri" week="0" class="week-selection day-box-gray">FRI</div>
       <div id="weeksat" week="0" class="week-selection day-box-gray">SAT</div>
       <div id="weeksun" week="0" class="week-selection day-box-gray mar-0">SUN</div>
       <div class="clear"></div>
     </div><!--END OF SELECTION AVAILABLE DAY-->
     
     
     
<!--     <div id="custom-opening-time-disable" class="dr-available-time">
         <div class="field-container "> 
             <div id="clinic-times-added1" class="btn-time font-type-Montserrat mar-t4">ADD TIME</div>
         <div class="clear"></div>   
        </div>
     </div>-->
     
     
     <div id="custom-opening-time-enable" class="dr-available-time"><!-- Start Available time -->
       <div class="time-container-available new-mal2 fl status-opening-times">
               <div class="dr-available-time2">
             <label>OPENING TIME</label>
             </div><!--END OF DR AVAILABLE TIME-->
     <div class="clear"></div>
             <div class="field-type">
             
             
             
             <!--JQUERY TIME PICKER-->
            <div class="container">
    <div class="row">
        <div class='col-sm-6'>
            <div class="form-group">
                <div class='input-group date' id='startTime'>
                    <input id='timeOpen' type='text' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-time"></span>
                    </span>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
               $('#startTime').datetimepicker({
			format: 'LT',
			useCurrent: true
			
		});

            });
        </script>
    </div>
</div>
             
  <!--END JQUERY TIME PICKER-->            
             
                 <!--<input class="time-input fl" type="text">-->
              </div><!--END OF FIELD TYPE-->
       </div><!--END OF TIME CONTAINER AVAILABLE-->
       
       
       
    <div class="time-container-available fl status-opening-times">
        <div class="dr-available-time2">
          <label>CLOSING TIME</label>
        </div><!--END OF DR AVAILABLE TIME-->
     <div class="clear"></div>
    <div class="field-type">
                  <!--JQUERY TIME PICKER-->
    <div class="container">
        <div class="row">
            <div class='col-sm-6'>
                <div class="form-group">
                    <div class='input-group date' id="endTime">
                        <input id='timeClose' type='text' class="form-control" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                $(function () {
                   $('#endTime').datetimepicker({
                            format: 'LT',
                            useCurrent: true
                    });

                });
            </script>
        </div>
    </div>
             
  <!--END JQUERY TIME PICKER-->  
    </div><!--END OF FIELD TYPE-->
    </div><!--END OF TIME CONTAINER AVAILABLE-->
       
    

    
    
    
        <div class="field-container "> 
            <?php if($clinictimes){
                //if(($clinictimes[0]->Repeat==1 && $clinictimes[0]->Status==1) || ($clinictimes[0]->Status==1 && strtotime(date('d-m-Y')) <= strtotime($clinictimes[0]->To_Date))){
                if(($clinictimes[0]->Repeat==1 && $clinictimes[0]->Status==1) || ($clinictimes[0]->Status==1 && strtotime(date('d-m-Y')) <= $clinictimes[0]->To_Date)){    
                    echo '<div curdoctorid="" id="clinic-times-added" class="btn-time font-type-Montserrat mar-t4 new-mart">ADD TIME</div>';
                }else{
                    echo '<div id="" class="btn-time font-type-Montserrat mar-t4 ">ADD TIME</div>';
                }
            }else{
                echo '<div curdoctorid="" id="clinic-times-added" class="btn-time font-type-Montserrat mar-t4 new-mart">ADD TIME</div>';
            }?>
           
         <div class="clear"></div>   
        </div>

        <div class="clear"></div>      
</div><!--END OF AVAILABLE TIME-->
     
     
    





<div id="load-opening-times-ajax"><!-- Start Ajax Load -->
    <?php if($clinictimes){ ?>
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
             //if(($clinictimes[0]->Repeat==1 && $clinictimes[0]->Status==1) || (strtotime(date('d-m-Y')) <= strtotime($clinictimes[0]->To_Date))){
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
                            <div id="delete-opening-times" clinictimeid="{{$clnTimes->ClinicTimeID}}"  class="icn-close"><img width="17" height="21" alt="" src="{{ URL::asset('assets/images/icn-close-dark.png') }}"></div>
                           <div class="clear"></div>
       			</div>
                    </div><!--ND OF TIME BAR CONTAINER-->
                    <div class="clear"></div>
         <?php       }
                //}else{
               //     echo "No Times has been set";
               // }
         }else{
             //echo "No Times has been set";
         }?>
          
         
     </div><!--END OF AVAILABILITY DAY GRAPH-->
<!--</div>End ajax Load  -->
     
     
     <div class="time-option-container">
         <div id="load-repeat-ajax">
    <?php if($clinictimes[0]){ 
        if($clinictimes[0]->Repeat==0){
            //if(strtotime(date('d-m-Y')) <= strtotime($clinictimes[0]->To_Date)){
            //if(($clinictimes[0]->Repeat==1 && $clinictimes[0]->Status==1) || (strtotime(date('d-m-Y')) <= strtotime($clinictimes[0]->To_Date))){
            if(($clinictimes[0]->Repeat==1 && $clinictimes[0]->Status==1) || (strtotime(date('d-m-Y')) <= $clinictimes[0]->To_Date)){    
                echo '<div id="repeat-times-action" managetimeid="'.$clinictimes[0]->ManageTimeID.'" repeatid="0" class="btn-update font-type-Montserrat  mar-left-2 ">Start Repeat</div>';
                echo '<div class="repeat">Repeat Expires on : '.date('d-m-Y',$clinictimes[0]->To_Date).'</div>';
            }else{
                echo '<div class="field-type">
                <input class="check " id="checkbox1" type="checkbox" name="checkbox" ><label for="checkbox1">Repeat Weekly</label>
                </div>';
            }   
        }else{
            echo '<div id="repeat-times-action" managetimeid="'.$clinictimes[0]->ManageTimeID.'" repeatid="1" class="btn-cancel font-type-Montserrat ">Stop Repeat</div>';
        }
    }else{ ?>
        <div class="field-type">
        <input class="check " id="checkbox1" type="checkbox" name="checkbox" ><label for="checkbox1">Repeat Weekly</label>
        </div><!--END OF FIELD TYPE-->
    <?php } ?>
        
         </div>   
        <div class="clear"></div>      
     </div> <!--END OF TIME OPTION CONTAINER-->
     
    <?php }else{
        echo '<div class="notify2">No times has been set</div>';
    }?>
     
     </div><!--End ajax Load --> 
     
     </div><!--END OF DR AVAILABLE SELECT CONTAINER-->
     <div class="line"></div>
     
     
     
     
     <div class="holiday-container">
     
     
     
     <div class="field-container">
         <div class="field-name">
         <label>HOLIDAYS</label>
         </div><!--END OF FIELD NAME--> 
         <div class="field-type">
         <input class="input-date" type="text" id="date-holiday">
         </div><!--END OF FIELD TYPE-->
       </div><!--END OF FIELD CONTAINER-->
     
     
     
       <div class="radio-btn-container">
         <div class="radio">
          <input id="radio-fulday" type="radio" name="radio-holiday" value="0" >
            <label for="radio-holiday"><span><span></span></span>Full Day</label>
          </div> 
        </div><!--END OF RADIO BTN CONTAINER-->
       <div class="clear"></div>
       <div class="radio-btn-container">
         <div class="radio new-mart2">
             <input id="radio-custom" type="radio" name="radio-holiday" value="1" >
            <label for="radio-holiday"><span><span></span></span>Closed on selected hours</label></div> 
       </div><!--END OF RADIO BTN CONTAINER-->
       <div class="clear"></div>
       
       
       
       
       
       <div class="holiday-time">
		<div class="dr-available-time">
  			<div class="time-container-available fl holiday-status-section">
                <div class="field-type">
                                  <!--JQUERY TIME PICKER-->
            <div class="container">
    <div class="row">
        <div class='col-sm-6'>
            <div class="form-group">
                <div class='input-group date' id='from'>
                    <input id='timeStart' type='text' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-time"></span>
                    </span>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
               $('#from').datetimepicker({
			format: 'LT',
			useCurrent: true
		});

            });
        </script>
    </div>
</div>
             
  <!--END JQUERY TIME PICKER--> 
                </div><!--END OF FIELD TYPE-->
             		<div class="clear"></div>
                    <div class="dr-available-time2">
                     <label>Closed From</label>
                     </div><!--END OF DR AVAILABLE TIME-->
       		</div><!--END OF TIME CONTAINER AVAILABLE-->
       
              
              
               <div class="time-container-available fl holiday-status-section">
                     <div class="field-type">
                                      <!--JQUERY TIME PICKER-->
            <div class="container">
    <div class="row">
        <div class='col-sm-6'>
            <div class="form-group">
                <div class='input-group date' id='Till'>
                    <input id='timeEnd' type='text' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-time"></span>
                    </span>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
               $('#Till').datetimepicker({
			format: 'LT',
			useCurrent: true
		});

            });
        </script>
    </div>
</div>
             
  <!--END JQUERY TIME PICKER--> 
                            </div><!--END OF FIELD TYPE-->
                     <div class="clear"></div>
                         <div class="dr-available-time2">
                            <label>Closed till</label>
                                </div><!--END OF DR AVAILABLE TIME-->
               </div><!--END OF TIME CONTAINER AVAILABLE-->
       
        <div class="field-container "> 
            <div id="add-clinic-holidays" class="btn-holiday font-type-Montserrat mar-t3">ADD HOLIDAY</div>
         <div class="clear"></div>   
         </div>
       
       
        <div class="clear"></div>
       
   </div><!--END OF DR AVAILABLE TIME -->  
    </div><!--END OF HOLIDAY TIME-->   
       
    <div id="load-clinic-holiday-ajax">   <!-- Start loading clinic holidays-->
       <div class="holiday-graph">
   		  <div class="holiday-title mar-left-2 fl">
             <label>DATE</label>
             </div><!--END OF DR AVAILABLE TITLE-->
             
          <div class="holiday-title2 fl padding-left-4 ">
                 <label>UNAVAILABLE</label>
                 </div><!--END OF DR AVAILABLE TITLE-->
          <div class="clear"></div>
           
        <?php if($clinicholidays){
            foreach($clinicholidays as $clnHolidays){ 
                if($clnHolidays->Type==0){
                    $holidaytimes = 'Full Day';
                }else{
                    $holidaytimes = $clnHolidays->From_Time.' - '.$clnHolidays->To_Time;
                }
                
                ?>
            <div class="holiday-bar-container">
                <div class="dr-detail-inner">
                    <label>{{$clnHolidays->Holiday}}</label>
                        <label class=" label2">{{$holidaytimes}}</label>
                        <div id="delete-clinic-holiday" clinicholidayid="{{$clnHolidays->ManageHolidayID}}" class="icn-close"><img width="17" height="21" src="{{ URL::asset('assets/images/icn-close-dark.png') }}" alt=""></div>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
          </div><!--END OF TIME BAR CONTAINER-->    
        <?php } } ?>   
        
          <div class="clear"></div>    
     </div>
    </div><!--End load clinic holidays-->   
       
       
       
<!--    <div class="field-container mar-left-4  mar-t "> 
        <div class="btn-update font-type-Montserrat ">Update Changes</div>
        <div class="btn-cancel font-type-Montserrat ">Cancel</div>
    </div>  -->
       
     </div><!--END OF HOLIDAY CONTAINER-->
   </div><!--END OF FORM NAV PAGE-->
  
  <div class="clear"></div>
  
  
</div><!--END OF CLINIC FORM CONTAINER-->






@include('common.footer-clinic-section')