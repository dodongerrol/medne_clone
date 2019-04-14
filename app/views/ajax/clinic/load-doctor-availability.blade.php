<?php //echo '<pre>'; print_r($loadarrays);echo '</pre>'; ?> 
  <script type="text/javascript">		
$(function() {
     $("#date-holiday").datepicker({
        dateFormat: "DD, d MM, yy"
       // dateFormat: "dd-mm-yy"
    }).datepicker("setDate", "0");	
//$( "#datepicker2" ).datepicker({dateFormat: 'DD, d MM, yy',});

});		 
</script>
<?php 
    if(empty($loadarrays['currentdoctor']->DocImage)){
        $docimage = URL::asset('assets/images/no-doctor.png');
    }else{
        $docimage = $loadarrays['currentdoctor']->DocImage;
    }
?>
<div class="form-nav-page">
   <div class="dr-option-nav">
   <ul>
   <li><a href="{{URL::to('app/clinic/clinic-doctors-view')}}">VIEW DOCTORS</a></li>
   <li><a href="{{URL::to('app/clinic/doctor-availability')}}" class="active">SET AVAILABILITY</a></li>
   <li><a href="{{URL::to('app/clinic/clinic-doctor')}}" >ADD DOCTOR</a></li>
   </ul>
   </div> <!--END OF DR OPTION NAV-->
   
   <div class="clear"></div>
   
   <div class="search-bar">
   <div class="field-name">
        <div class="dr-available-profile-image fl">
        <img src="{{$docimage}}" width="60" height="60"  alt=""/>
        </div><!--END OF DR AVAILABL PROFILE IMAGE-->
   </div><!--END OF FIELD NAME-->
         
         <div class="field-container-dr-available">
  			<div class="field-name">
         		<label class="label-dr-available">Select A Doctor</label>
        		 </div><!--END OF FIELD NAME--> 
         <div class="clear"></div>
         	<div class="field-type">
                    <div class="select-box-v2">
                        <select id="load-doctors-availability" name="procedure">
                            <option value="">Select</option> 
                            <?php if($loadarrays['doctors']) {
                                foreach($loadarrays['doctors'] as $doctor){
                                    if($loadarrays['currentdoctor']->DoctorID == $doctor->DoctorID){
                                        echo '<option value="'.$doctor->DoctorID.'" selected>'.$doctor->DocName.'</option>';
                                    }else{
                                        echo '<option value="'.$doctor->DoctorID.'">'.$doctor->DocName.'</option>';
                                    }
                                    
                                }
                            }?>
                        </select>
                    </div>
         	<!--<input class="label-dr-available-input fl" type="text">-->
              <label class="label-dr-available fl mar-left-2 mar-t2 font-type-oxygen l-gray S12">Phone:</label>
              <label class="label-dr-available fl mar-t2 font-type-oxygen d-gray S2 padding-top-2 mar-left-3">{{ $loadarrays['currentdoctor']->DocPhone }}</label>
                 </div><!--END OF FIELD TYPE-->
     	 </div><!--END OF FIELD CONTAINER DR AVAILABLE-->
   <div class="clear"></div>    
   </div><!--END OF SEARCH BAR-->
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
     
     
     <div class="dr-available-time">
       <div class="time-container-available fl">
               <div class="dr-available-time2">
             <label>START TIME</label>
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
       
       
       
       <div class="time-container-available fl">
               <div class="dr-available-time2">
             <label>END TIME</label>
             </div><!--END OF DR AVAILABLE TIME-->
     <div class="clear"></div>
             <div class="field-type">
                  <!--JQUERY TIME PICKER-->
            <div class="container">
    <div class="row">
        <div class='col-sm-6'>
            <div class="form-group">
                <div class='input-group date' id='endTime'>
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
             <?php if($loadarrays['doctortimes']){ 
                 //if(($loadarrays['doctortimes'][0]->Repeat==1 && $loadarrays['doctortimes'][0]->Status==1) || ($loadarrays['doctortimes'][0]->Status==1 && strtotime(date('d-m-Y')) <= strtotime($loadarrays['doctortimes'][0]->To_Date))){
                if(($loadarrays['doctortimes'][0]->Repeat==1 && $loadarrays['doctortimes'][0]->Status==1) || ($loadarrays['doctortimes'][0]->Status==1 && strtotime(date('d-m-Y')) <= $loadarrays['doctortimes'][0]->To_Date)){
                     echo '<div id="clinic-times-added" curdoctorid="'.$loadarrays['currentdoctor']->DoctorID.'" class="btn-time font-type-Montserrat mar-t4">ADD TIME</div>';
                 }else{
                     echo '<div id="" curdoctorid="" class="btn-time font-type-Montserrat mar-t4">ADD TIME</div>';
                 }
             }else{ ?>
                 <div id="clinic-times-added" curdoctorid="{{ $loadarrays['currentdoctor']->DoctorID }}" class="btn-time font-type-Montserrat mar-t4">ADD TIME</div>
            <?php }?>
            
         <div class="clear"></div>   
         </div>
       
       
        <div class="clear"></div>
       
     </div><!--END OF AVAILABLE TIME-->
     
     
     <div id="load-opening-times-ajax"><!-- Start Load doctor time ajax -->
     <?php if($loadarrays['doctortimes']){ ?>    
     <div class="availability-day-graph">
   		  <div class="dr-available-title new-mal4 fl">
             <label>DAYS OF THE WEEK</label>
             </div><!--END OF DR AVAILABLE TITLE-->
             
          <div class="dr-available-title fl padding-left-4 ">
                 <label>AVAILABILITY</label>
                 </div><!--END OF DR AVAILABLE TITLE-->
          <div class="clear"></div>
           
         <?php if($loadarrays['doctortimes']){ 
             //if(($loadarrays['doctortimes'][0]->Repeat==1 && $loadarrays['doctortimes'][0]->Status==1) || (strtotime(date('d-m-Y')) <= strtotime($loadarrays['doctortimes'][0]->To_Date))){
             foreach($loadarrays['doctortimes'] as $docTimes){
                 $mydoctortime = $docTimes->StartTime.' - '.$docTimes->EndTime ;?>
            <div class="dr-time-bar-container">
                <div class="dr-detail-inner">
                    <label >{{StringHelper::GetOpenWeeks($docTimes)}}</label>
                        <label class=" label2">{{$mydoctortime}}</label>
                        <div id="delete-opening-times" doctorid="{{ $docTimes->PartyID }}" clinictimeid="{{$docTimes->ClinicTimeID}}" class="icn-close">
                            <img width="17" height="21" alt="" src="{{ URL::asset('assets/images/icn-close-dark.png') }}"></div>
                    <div class="clear"></div>
                </div>
            </div><!--END OF TIME BAR CONTAINER-->
             <?php }   
             //}else{
            // echo 'No Doctor Availability Found';
            //}
             }else{
             //echo 'No Doctor Availability Found';
         }?>
          
          
           <div class="clear"></div>    
     </div><!--END OF AVAILABILITY DAY GRAPH-->
<!--     </div> End Doctor time ajax -->

     <div class="time-option-container">
     <div id="load-repeat-ajax">
    <?php if($loadarrays['doctortimes'][0]){
        if($loadarrays['doctortimes'][0]->Repeat==0){
            //if(strtotime(date('d-m-Y')) <= strtotime($loadarrays['doctortimes'][0]->To_Date)){
            if(($loadarrays['doctortimes'][0]->Repeat==1 && $loadarrays['doctortimes'][0]->Status==1) || (strtotime($loadarrays['currentdate']) <= $loadarrays['doctortimes'][0]->To_Date)){
                echo '<div id="repeat-times-action" managetimeid="'.$loadarrays['doctortimes'][0]->ManageTimeID.'" repeatid="0" class="btn-update font-type-Montserrat  mar-left-2 ">Start Repeat</div>';
                echo '<div class="repeat">Repeat Expires on : '.date('d-m-Y',$loadarrays['doctortimes'][0]->To_Date).'</div>';
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

     <?php }else{
         echo '<div class="pro-banner">Doctors availablilty not added</div>';
     }?>
     </div><!-- End Doctor time ajax -->
     
     </div><!--END OF DR AVAILABLE SELECT CONTAINER-->
     <div class="line"></div>
     
     
     
     
     <div class="holiday-container">
     
     
     
     <div class="field-container">
         <div class="field-name">
         <label>HOLIDAYS</label>
         </div><!--END OF FIELD NAME--> 
         <div class="field-type">
             <input id="date-holiday" class="input-date" style="z-index:3; position:relative;" type="text">
         </div><!--END OF FIELD TYPE-->
       </div><!--END OF FIELD CONTAINER-->
     
     
     
       <div class="radio-btn-container">
         <div class="radio">
          <input id="radio-fulday" type="radio" name="radio-holiday" value="0">
          <label for="radio1"><span><span></span></span>Full Day</label>
          </div> 
        </div><!--END OF RADIO BTN CONTAINER-->
       <div class="clear"></div>
       <div class="radio-btn-container">
         <div class="radio">
             <input id="radio-custom" type="radio" name="radio-holiday" value="1">
             <label for="radio1"><span><span></span></span>Unavailable on selected hours</label></div> 
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
                     <label>Unavailable From</label>
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
                            <label>Unavailable till</label>
                                </div><!--END OF DR AVAILABLE TIME-->
               </div><!--END OF TIME CONTAINER AVAILABLE-->
       
        <div class="field-container "> 
           <div id="add-clinic-holidays" doctorid="{{ $loadarrays['currentdoctor']->DoctorID }}" class="btn-holiday font-type-Montserrat mar-t3">ADD HOLIDAY</div>
         <div class="clear"></div>   
         </div>
       
       
        <div class="clear"></div>
       
   </div><!--END OF DR AVAILABLE TIME -->  
    </div><!--END OF HOLIDAY TIME-->   
       
    <div id="load-doctor-holiday-ajax">   
       <div class="holiday-graph">
           <?php if($loadarrays['doctorholidays']) { ?>
            <div class="holiday-title new-mal4 fl">
             <label>DATE</label>
             </div><!--END OF DR AVAILABLE TITLE-->
             
          <div class="holiday-title2 fl padding-left-4 ">
                 <label>UNAVAILABLE</label>
                 </div><!--END OF DR AVAILABLE TITLE-->
          <div class="clear"></div>
           
        <?php //if($loadarrays['doctorholidays']) {
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
            //echo 'No Holidays found';
        }
?>  
          <div class="clear"></div>    
     </div>
    </div>   
      
       
     </div><!--END OF HOLIDAY CONTAINER-->
   </div><!--END OF FORM NAV PAGE-->