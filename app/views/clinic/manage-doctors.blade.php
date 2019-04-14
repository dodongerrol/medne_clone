@include('common.header-clinic')
<style type="text/css">

@import url("sinkin-sans-fontfacekit/web fonts/sinkinsans_300light_macroman/stylesheet.css");
body,td,th,input,textarea {font-family: 'sinkin_sans300_light'; }
.mc-price-display{ font-family: 'Nunito', sans-serif;}



.mc-calendar-day .mc-day .date {
    color: #999999;
    float: left;
    font-size: 12px;
    height: 35px;
    padding-top: 2px !important;
    text-align: center;
    width: 100%;
}

.mc-btn-add-doctor a {
    color: #fff !important;
    text-decoration: none !important;
}

.width100{ width:93% !important;}

.mc-calendar-view {
    
    margin-bottom: 30px;
    width: 100%;
	/*background-color:orange;*/
}
	
	
.mc-calendar-view .calendar-time {
    float: left;
    height: 929px;
    width: 5% !important;
	/*background-color:red;*/
}
	
	.calendar-time .mc-time {
   /* height: 20px;*/
    width: 100% !important;
}


.mc-calendar-day .mc-day {
    color: #cccccc;
    float: left;
    font-size: 14px;
    height: 35px;
    padding-top: 28px;
    text-align: center;
    width: 14.2%;
}



.mc-day-option-container .mc-day-block {
    float: left;
    height: 73px;
    margin-right: 10px;
    width: 13.4%;
}


.mc-day-option-container .mc-day-block-last {
    float: left;
    height: 73px;
    width: 14.2%;
}


.mc-day-block .mc-slot1 {
    background-color: #1b9bd7;
    color: #666666;
    font-size: 12px;
    height: 30px;
    margin-bottom: 1px;
    padding: 6px 0 0 5px;
    width: 100% !important;
}


.mc-day-block .mc-slot2 {
    background-color: #1b9bd7;
    color: #666666;
    font-size: 12px;
    height: 30px;
    margin-bottom: 1px;
    padding: 6px 0 0 5px;
    width: 100% !important;
}


.mc-day-block-last .mc-slot1-last {
    background-color: #1b9bd7;
    height: 36px;
    margin-bottom: 1px;
    width: 100% !important;
}

.mc-day-block-last .mc-slot2-last {
    background-color: #1b9bd7;
    height: 36px;
    width: 100% !important;
}

 #ui-datepicker-div {
     left: 40.7% !important ;
    top: 120% !important ;
} 

@media 
(-webkit-min-device-pixel-ratio: 2), 
(min-resolution: 192dpi) { 

.width100{ width:93% !important;}

.mc-calendar-view {
    
    margin-bottom: 30px;
    width: 100%;
	/*background-color:orange;*/
}
	
	
.mc-calendar-view .calendar-time {
    float: left;
    height: 929px;
    width: 5% !important;
/*	background-color:red;*/
}
	
	.calendar-time .mc-time {
   /* height: 20px;*/
    width: 100% !important;
}


.mc-calendar-day .mc-day {
    color: #cccccc;
    float: left;
    font-size: 14px;
    height: 35px;
    padding-top: 28px;
    text-align: center;
    width: 14.2%;
}



.mc-day-option-container .mc-day-block {
    float: left;
    height: 73px;
    margin-right: 10px;
    width: 13.4%;
}


.mc-day-option-container .mc-day-block-last {
    float: left;
    height: 73px;
    width: 13.2%;
}


.mc-day-block .mc-slot1 {
    background-color: #1b9bd7;
    color: #666666;
    font-size: 12px;
    height: 30px;
    margin-bottom: 1px;
    padding: 6px 0 0 5px;
    width: 100% !important;
}


.mc-day-block .mc-slot2 {
    background-color: #1b9bd7;
    color: #666666;
    font-size: 12px;
    height: 30px;
    margin-bottom: 1px;
    padding: 6px 0 0 5px;
    width: 100% !important;
}


.mc-day-block-last .mc-slot1-last {
    background-color: #1b9bd7;
    height: 36px;
    margin-bottom: 1px;
    width: 100% !important;
}

.mc-day-block-last .mc-slot2-last {
    background-color: #1b9bd7;
    height: 36px;
    width: 100% !important;
}

/*#ui-datepicker-div {
    left: 40.7% ;
    top: 138%;
}*/

.mc-bg-white .mc-container .mc-background-color .mc-calendar .mc-date-toggle-container { height: 28px; width: 285px; margin-left: 14%; margin-top: 24px; }

}
/*  hack*/
@media screen and (-webkit-min-device-pixel-ratio:0) {
/*    #ui-datepicker-div {
        left: 37.7% ;
        top: 120%;
    }*/
}

</style>
        <div class="mc-border-line"></div>
    
    
    <!--DOCTOR SELECTION START-->
    <div class="mc-dr-selection-container">
      <div class="icn-doctor mc-fl"><img src="{{ URL::asset('assets/images/icn-doctor.png') }}" width="23" height="23" alt="img-icon-doctor" longdesc="images/icn-doctor.png"></div>
      
      <div class="mc-fl">
      	<div class="select-box mc-fl">
            <select id="select-doctor" clid="<?php echo $clinicid;?>">
                <option value="0">Select a Doctor</option>
        <?php 
        if($doctors[0]['error'] != 1){
            foreach($doctors as $doctor){
                if($doctors[0]['doctorid']==$doctor['doctorid'] && $default==1){
                    echo '<option selected="selected" value='.$doctor['doctorid'].'>'.$doctor['doctorname'].'</option>';
                }else{
                    echo '<option value='.$doctor['doctorid'].'>'.$doctor['doctorname'].'</option>';
                }
            }
        }
        ?>
<!--        <option>Dr.Ishani Rodrigo</option>
        <option>Dr.Ruberu</option>
        <option>Dr.Sunil Wijesinghe</option>
-->
        </select>
       </div>
          
<!--          <div class="mc-fl mc-errors" id="ajax-error-site">
          <?php 
            //if($doctors[0]['error'] == 1){
            //    echo $doctors[0]['error-message'];
           // }?>
          </div>-->
      </div>
      <div class="mc-btn-add-doctor mc-fr">{{ HTML::link('/app/clinic/new-doctor', 'ADD A DOCTOR')}}</div>
    </div>
    <!--DOCTOR SELECTIONR END-->
    
    <div class="mc-border-line"></div>
    <div id="ajax-error-site">
     
    </div>
    <div class="mc-doctor-profile-container"><!--DOCTOR PROFILE CONTAINER-->
        
        
    <div id="doctor-load-data"> <!-- for ajax load view start-->    
    <?php 
    if($doctors[0]['error'] != 1 && $default==1){
        //echo $doctors[0]['doctorid'];
    ?>
    @include('ajax.clinic-settings-doctor')
    <?php }else{
    ?>
    <div class="mc-dr-profile-detail"><!--DOCTOR PROFILE DETAILS-->
        <div class="mc-dr-profile-image"><img src="{{ URL::asset('assets/images/mc-profile-img.png') }}" width="125" height="125" alt="img-profile" longdesc="images/mc-profile-img.png"></div>
       
        <div class="mc-profile-form"><!--PROFILE FORM-->
            <form name="" action="" id="form-doctor" method="POST">
        <fieldset>
          <div class="mc-form-spacer">
              <input id="doc-name" class="mc-clear"  name="Name" type="text" placeholder="Name">  
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
          <input id="doc-qualification" name="Qualification" type="text" placeholder="Qualifications">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
          <input id="doc-speciality" name="Specialty" type="text" placeholder="Specialty">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
          <input id="doc-mobile" name="Mobile" type="text" placeholder="Mobile">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
          <input id="doc-phone" class="inputshort " name="" type="text" placeholder="Phone"> <label class=" mc-label mc-fl" >Emergency</label>
          <div class="mc-clear"></div>
          <div class="mc-border-line"></div>
          </div>
          
          
           <div class="mc-form-spacer">
          <input id="doc-email" name="Email" type="text" placeholder="Email">
          <div class="mc-border-line"></div>
          </div>
         
           <div class="mc-btn-container">
               <div id="save-doctordetails" class="mc-btn-save-changes" clinicid="<?php echo $clinicid;?>">SAVE CHANGES
<!--               <div class="mc-btn-save-changes"><input type="submit" class="mc-btn-save-changes" name='Submit'></div>-->
               </div>
               
             <div class="mc-btn-cancel">Cancel</div>
           </div>
        </fieldset>
        </form>
        </div><!--PROFILE FORM END-->
    </div><!--DOCTOR PROFILE DETAILS END-->
    
   
    <div class="mc-profile-detail-rcolum mc-fl"> <!--PROFILE DETAIL RIGHT -->
      <div class="mc-header2">Consultation Charges</div>
      <div><input id="consult-charge" class="mc-price-display" type="text" name="" placeholder="000" value="" ></div>
      
<!--       	<div class="mc-btn-container">
             	<div class="mc-btn-save-changes">SAVE CHANGES</div>
             	<div class="mc-btn-cancel">Cancel</div>
           	</div>-->
      
      <div class="mc-clear"></div>
<!--      <div class="mc-header3">Consultation Duration</div>
      <div class="select-box">
        <select id="slot-duration">
        <option>Select Time</option>
        <option>30min</option>
        <option>60min</option>
        </select>
       </div>-->
       <div class="mc-header3 mc-label13">BOOKINGS</div>
        <div class="mc-border-line"></div>
        <div class="queue">
            <div class="queue-check"><input id="queuetag" type="checkbox"> <label class="mc-label13">Queue No.</label></div>
            <div class="queue-input-container"><input id="queue-no" value="" class="input-queue" type="text"> <label class="mc-label14">Queue No.</label></div>
       <div class="queue-input-container">
       <div class="select-box-queue mc-fl">
        <select id="queue-time">
        <option>15min</option>    
        <option>30min</option>
        <option>45min</option>
        <option>60min</option>
        </select>
       </div><label class="mc-label14 mc-pad-lr">Duration</label></div>
     </div>
     
     <div class="queue">
         <div class="queue-check"><input id="slottag" type="checkbox"> <label class="mc-label13">Appointment Time</label></div>
      <div class="queue-input-container">
      <div class="select-box-queue mc-fl">
        <select id="slot-duration">
        <option>30min</option>
        <option>60min</option>
        </select>
       </div><label class="mc-label14 mc-pad-lr">Duration</label></div>
      </div>
        
      
      <div class="mc-btn-container">
    <div id="save-doctorcharge" doctorid=""  loadmain="1" clinicid="<?php echo $clinicid;?>" class="mc-btn-save-changes">SAVE CHANGES</div>
    <div class="mc-btn-cancel">Cancel</div>
    </div>
      
    </div>
    <?php //} ?>
    <!--</div> <!-- ajax load view end -->
    <!--PROFILE DETAIL RIGHT END -->
    
    
<div id="second-ajax-call">  <!-- Start second Ajax call -->    
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
        <label class="mc-fl mc-label3">Today, <?php echo date('F j, Y ');?></label>
        <div class="mc-arrow-blue mc-fl"><img src="{{ URL::asset('assets/images/icn-arrow-blue.png') }}" width="17" height="27" alt="img-arrow" longdesc="images/icn-arrow-blue.png"></div>
      </div>
      
      
      
      <div class="mc-calendar-option"> 
    <!--<div class="option">Day</div>
        <div class="select">Week</div>
    <div class="option">Month</div>-->
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
      
      <div class="mc-fl width100"> 
      <!--<div class="mc-fl mc-blancer">-->
    
    <div class="mc-calendar-day">
        <?php for($a=0; $a<7;$a++) {
            $dayOfWeek = date('d-m-Y', strtotime($today.' +'.$a.' day'));
            $getWeek = date('l', strtotime($dayOfWeek));
            $dayNow = date("d-m-Y");
            ?>
        <div class="mc-day">
           <div><?php if($dayOfWeek == $dayNow){echo "<span class='highlight'>".$getWeek."</span>";}else{echo $getWeek;}?></div>
           <div class="date"><?php if($dayOfWeek == $dayNow){ echo "<span class='highlight'>".$dayOfWeek."</span>";}else{echo $dayOfWeek;} ?></div>
        </div>
        <?php } ?>
    </div>      

      
       <div class="mc-border-line"></div>
       <?php 
           $totalTimes = 15;
           $round =1;
           for($i=0; $i<$totalTimes;$i++){ ?>          
               <div class="mc-day-option-container" docslot="<?php echo $doctorslotexist;?>">
                   <?php if($timeslot == "30min"){ ?>
                   <div class="mc-day-block" cdate="14-11-2014">
                    <div id="mona<?php echo $i;?>" insertedid="" class="getmyslot mc-slot1 mc-slot-color"></div>
                    <div id="monb<?php echo $i;?>" insertedid="" class="getmyslot mc-slot2 mc-slot-color"></div>
                    </div>
                   <div class="mc-day-block" cdate="15-11-2014">
                    <div id="tusa<?php echo $i;?>" insertedid="" class="getmyslot mc-slot1 mc-slot-color"></div>
                    <div id="tusb<?php echo $i;?>" insertedid="" class="getmyslot mc-slot2 mc-slot-color"></div>
<!--                    <div id="tusa<?php echo $i;?>" class="mc-slot1"></div>
                        <div id="tusb<?php echo $i;?>" class="mc-slot2"></div>-->
                   </div>
                   <div class="mc-day-block" cdate="16-11-2014">
                    <div id="wena<?php echo $i;?>" insertedid="" class="getmyslot mc-slot1 mc-slot-color"></div>
                    <div id="wenb<?php echo $i;?>" insertedid="" class="getmyslot mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="17-11-2014">
                    <div id="thua<?php echo $i;?>" insertedid="" class="getmyslot mc-slot1 mc-slot-color"></div>
                    <div id="thub<?php echo $i;?>" insertedid="" class="getmyslot mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="18-11-2014">
                    <div id="fria<?php echo $i;?>" insertedid="" class="getmyslot mc-slot1 mc-slot-color"></div>
                    <div id="frib<?php echo $i;?>" insertedid="" class="getmyslot mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="19-11-2014">
                    <div id="sata<?php echo $i;?>" insertedid="" class="getmyslot mc-slot1 mc-slot-color"></div>
                    <div id="satb<?php echo $i;?>" insertedid="" class="getmyslot mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="20-11-2014">
                    <div id="suna<?php echo $i;?>" insertedid="" class="getmyslot mc-slot1 mc-slot-color"></div>
                    <div id="sunb<?php echo $i;?>" insertedid="" class="getmyslot mc-slot2 mc-slot-color"></div>
                   </div>
                   <?php }elseif($timeslot == "60min"){ ?>
                   <div class="mc-day-block" id="myslot1">
                    <div id="mon<?php echo $i;?>" class="getmyslot mc-slot1hr "></div>
                   </div>
                   <div class="mc-day-block" id="myslot2">
                    <div id="tus<?php echo $i;?>" class="getmyslot mc-slot1hr mc-slot-color "></div>
                   </div>
                   <div class="mc-day-block" id="myslot3">
                    <div id="wen<?php echo $i;?>" class="getmyslot mc-slot1hr "></div>
                   </div>
                   <div class="mc-day-block" id="myslot4">
                    <div id="thu<?php echo $i;?>" class="getmyslot mc-slot1hr "></div>
                   </div>
                   <div class="mc-day-block" id="myslot5">
                    <div id="fri<?php echo $i;?>" class="getmyslot mc-slot1hr "></div>
                   </div>
                   <div class="mc-day-block" id="myslot6">
                    <div id="sat<?php echo $i;?>" class="getmyslot mc-slot1hr "></div>
                   </div>
                   <div class="mc-day-block" id="myslot7">
                    <div id="sun<?php echo $i;?>" class="getmyslot mc-slot1hr "></div>
                   </div>
                   <?php }else{ ?> 
                   <div class="mc-day-block" cdate="14-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                    </div>
                   <div class="mc-day-block" cdate="15-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="16-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="17-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="18-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="19-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                   </div>
                   <div class="mc-day-block" cdate="20-11-2014">
                    <div id="" insertedid="" class="mc-slot1 mc-slot-color"></div>
                    <div id="" insertedid="" class="mc-slot2 mc-slot-color"></div>
                   </div>
                   <?php } ?>
            </div>
            <div class="mc-border-line"></div> 
      <?php  }
       ?>
   
 </div>       
    </div> <?php } ?>
</div> <!-- Second part ajax call end -->  
   
    </div> <!-- ajax load view end --> 
    <div class="mc-clear"></div>
    
    </div><!--DOCTOR PROFILE CONTAINER END-->
    
    
<div class="mc-clear"></div>
@include('common.footer-clinic')