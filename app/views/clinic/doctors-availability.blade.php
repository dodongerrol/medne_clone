@include('common.header-clinic-section')
<!--END HEADER-->
  
  <div class="clear"></div>
  <script type="text/javascript">		
$(function() {
     $("#date-holiday").datepicker({
       // dateFormat: "DD, d MM, yy"
        dateFormat: "dd-mm-yy"
    }).datepicker("setDate", "0");	
//$( "#datepicker2" ).datepicker({dateFormat: 'DD, d MM, yy',});

});		 
</script>
<?php //echo '<pre>'; print_r($loadarrays);echo '</pre>';  ?>  
  
<div id="clinic-form-container">
    <!--START OF FORM NAV-->

    @include('clinic.clinic-nav-section')

    <!--END OF FORM NAV-->


    <div id="load-doctor-availability-ajax">
      <?php if($loadarrays['currentdoctor']!=null){ ?>
    @include('ajax.clinic.load-doctor-availability')
     <?php }else{ ?>
      
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
        <img src="{{ URL::asset('assets/images/sample2.png') }}" width="60" height="60"  alt=""/>
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
                                    echo '<option value="'.$doctor->DoctorID.'">'.$doctor->DocName.'</option>';
                                }
                            }?>
                        </select>
                    </div>
         	<!--<input class="label-dr-available-input fl" type="text">-->
              <label class="label-dr-available fl mar-left-2 mar-t2 font-type-oxygen l-gray S12"></label>
              <label class="label-dr-available fl mar-t2 font-type-oxygen d-gray S2 padding-top-2 mar-left-3"></label>
                 </div><!--END OF FIELD TYPE-->
     	 </div><!--END OF FIELD CONTAINER DR AVAILABLE-->
   <div class="clear"></div>    
   </div><!--END OF SEARCH BAR-->
   <div class="clear"></div>
   
<!--   <div class="dr-available-select-container">
     <div class="dr-available-day">
     <label>SELECT DAYS</label>
     </div>END OF DR AVAILABLE DAY
      <div class="clear"></div>
     <div class="selection-available-day">
       <div  class="day-box-gray">MON</div>
       <div  class="day-box-gray">TUE</div>
       <div  class="day-box-gray">WED</div>
       <div  class="day-box-gray">THU</div>
       <div  class="day-box-gray">FRI</div>
       <div  class="day-box-gray">SAT</div>
       <div  class="day-box-gray mar-0">SUN</div>
       <div class="clear"></div>
     </div>END OF SELECTION AVAILABLE DAY
     
     
     <div class="dr-available-time">
       <div class="time-container-available fl">
               <div class="dr-available-time2">
             <label>START TIME</label>
             </div>END OF DR AVAILABLE TIME
     <div class="clear"></div>
             <div class="field-type">
             
             
             
             JQUERY TIME PICKER
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
             
  END JQUERY TIME PICKER            
             
                 <input class="time-input fl" type="text">
              </div>END OF FIELD TYPE
       </div>END OF TIME CONTAINER AVAILABLE
       
       
       
       <div class="time-container-available fl">
               <div class="dr-available-time2">
             <label>END TIME</label>
             </div>END OF DR AVAILABLE TIME
     <div class="clear"></div>
             <div class="field-type">
                  JQUERY TIME PICKER
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
             
  END JQUERY TIME PICKER  
              </div>END OF FIELD TYPE
       </div>END OF TIME CONTAINER AVAILABLE
       
        <div class="field-container "> 
           <div class="btn-time font-type-Montserrat mar-t4">ADD TIME</div>
         <div class="clear"></div>   
         </div>
       
       
        <div class="clear"></div>
       
     </div>END OF AVAILABLE TIME
     
     
     
     <div class="availability-day-graph">
   		  <div class="dr-available-title mar-left-2 fl">
             <label>DAYS OF THE WEEK</label>
             </div>END OF DR AVAILABLE TITLE
             
          <div class="dr-available-title fl padding-left-4 ">
                 <label>AVAILABILITY</label>
                 </div>END OF DR AVAILABLE TITLE 
          <div class="clear"></div>
           

          
           <div class="clear"></div>    
     </div>END OF AVAILABILITY DAY GRAPH 
     
     <div class="time-option-container">
     
     
      <div class="field-type">
                  <input class="check " id="checkbox1" type="checkbox" name="checkbox" value="1" checked="checked"><label for="checkbox1">Repeat Weekly</label>
              </div>END OF FIELD TYPE
              
        <div class="clear"></div>      
     </div> END OF TIME OPTION CONTAINER
     </div>END OF DR AVAILABLE SELECT CONTAINER
     -->
     
     
     
     
     
     
     
     
     
     
     
     
     <div class="line"></div>
     
     
     
     
<!--     <div class="holiday-container">
     
     
     
     <div class="field-container">
         <div class="field-name">
         <label>HOLIDAYS</label>
         </div>END OF FIELD NAME 
         <div class="field-type">
             <input id="date-holiday" class="input-date" type="text">
         </div>END OF FIELD TYPE
       </div>END OF FIELD CONTAINER
     
     
     
       <div class="radio-btn-container">
         <div class="radio">
          <input id="radio-fulday" type="radio" name="radio-holiday" value="0"><label for="radio1"><span><span></span></span>Full Day</label>
          </div> 
        </div>END OF RADIO BTN CONTAINER
       <div class="clear"></div>
       <div class="radio-btn-container">
         <div class="radio"><input id="radio1" type="radio" name="radio-holiday" value="1"><label for="radio1"><span><span></span></span>Unavailable on selected hours</label></div> 
       </div>END OF RADIO BTN CONTAINER
       <div class="clear"></div>
       
       
       
       
       
       <div class="holiday-time">
		<div class="dr-available-time">
  			<div class="time-container-available fl">
                <div class="field-type">
                                  JQUERY TIME PICKER
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
             
  END JQUERY TIME PICKER 
                </div>END OF FIELD TYPE
             		<div class="clear"></div>
                    <div class="dr-available-time2">
                     <label>Unavailable From</label>
                     </div>END OF DR AVAILABLE TIME
       		</div>END OF TIME CONTAINER AVAILABLE
       
       
       
              
              
              
               <div class="time-container-available fl">
                     <div class="field-type">
                                      JQUERY TIME PICKER
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
             
  END JQUERY TIME PICKER 
                            </div>END OF FIELD TYPE
                     <div class="clear"></div>
                         <div class="dr-available-time2">
                            <label>Unavailable till</label>
                                </div>END OF DR AVAILABLE TIME
               </div>END OF TIME CONTAINER AVAILABLE
       
        <div class="field-container "> 
           <div class="btn-holiday font-type-Montserrat mar-t3">ADD HOLIDAY</div>
         <div class="clear"></div>   
         </div>
       
       
        <div class="clear"></div>
       
   </div>END OF DR AVAILABLE TIME   
    </div>END OF HOLIDAY TIME   
       
       
       <div class="holiday-graph">
   		  <div class="holiday-title mar-left-2 fl">
             <label>DATE</label>
             </div>END OF DR AVAILABLE TITLE
             
          <div class="holiday-title2 fl padding-left-4 ">
                 <label>UNAVAILABLE</label>
                 </div>END OF DR AVAILABLE TITLE
          <div class="clear"></div>
           
           
          <div class="holiday-bar-container">
          		<div class="dr-detail-inner">
      				 <label>September 25, 2015</label>
        				<label class=" label2">2:00pm - 5:00pm</label>
        				<div class="icn-close"><img width="17" height="21" src="{{ URL::asset('assets/images/icn-close-dark.png') }}" alt=""></div>
         			<div class="clear"></div>
       			</div>
                 <div class="clear"></div>
          </div>END OF TIME BAR CONTAINER
          
          
          
          <div class="holiday-bar-container">
          		<div class="dr-detail-inner">
      				 <label>October 2, 2015</label>
        				<label class=" label2">Full Day</label>
        				<div class="icn-close"><img width="17" height="21" src="{{ URL::asset('assets/images/icn-close-dark.png') }}" alt=""></div>
         			<div class="clear"></div>
       			</div>
                 <div class="clear"></div>
          </div>END OF TIME BAR CONTAINER
          
          <div class="clear"></div>    
     </div>
       
      
       
     </div>END OF HOLIDAY CONTAINER
     
     -->
     
     
     
     
     
     
     
     
     
     
     </div><!--END OF FORM NAV PAGE--> <?php } ?>
  </div>
  <div class="clear"></div>
  
  
</div><!--END OF CLINIC FORM CONTAINER-->








@include('common.footer-clinic-section')