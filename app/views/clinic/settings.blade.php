@include('common.header-clinic')
   <!--HEADER END-->   
<?php 
echo '<pre>'; print_r($clinics); echo '</pre>';
?>
    
    <div class="mc-border-line"></div>
    
    <!--DOCTOR SELECTIONR END-->
    
   <div class="mc-clear"></div>
   <div class="mc-blank"></div>
<div class="mc-border-line"></div>
     
    <div class="mc-doctor-profile-container"><!--DOCTOR PROFILE CONTAINER-->
    <div class="mc-dr-profile-detail mc-pad-l"><!--DOCTOR PROFILE DETAILS-->
        <div class="mc-dr-profile-image"><img src="{{ URL::asset('assets/images/mc-profile-img.png') }}" width="125" height="125"  alt=""/></div>
        <div class="mc-profile-form"><!--PROFILE FORM-->
        <form class="form-booking" method="get">
        <fieldset>
          <div class="mc-form-spacer">
              <input name="" type="text" placeholder="Name" value="<?php echo $clinics->Name;?>">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
          <input name="" type="text" placeholder="Address" value="<?php echo $clinics->Address;?>">
          <div class="mc-border-line"></div>
          </div>
          
           <div class="mc-form-spacer">
          <input name="" type="text" placeholder="Phone" value="<?php echo $clinics->Phone;?>">
          <div class="mc-border-line"></div>
          </div>
          
          <div class="mc-header3 mc-label13">INSURANCE PANEL</div>
     <div class="mc-border-line"></div>
        <div class="insurance-company">
        <?php 
        if(!empty($insurances)) { 
            foreach($insurances as $insur){ ?>
                <div class="insu-check">
                    <input type="checkbox"> <label class="mc-label15"><?php echo $insur->Name;?></label>
                </div>
        <?php  }
        }   
        ?>
        <!--    <div class="insu-check"><input type="checkbox"> <label class="mc-label15">AIA</label></div>
            <div class="insu-check"><input type="checkbox"> <label class="mc-label15">AXA</label></div>
            <div class="insu-check"><input type="checkbox"> <label class="mc-label15">Aviva </label></div>
            <div class="insu-check"><input type="checkbox"> <label class="mc-label15">Prudential</label></div>
            <div class="insu-check"><input type="checkbox"> <label class="mc-label15">Great Eastern</label></div>
            <div class="insu-check"><input type="checkbox"> <label class="mc-label15">NTCU Income</label></div>
            <div class="insu-check"><input type="checkbox"> <label class="mc-label15">Tokio Marine</label></div>
            <div class="insu-check"><input type="checkbox"> <label class="mc-label15">Manulife</label></div>
        -->
        </div>
          
            <div class="mc-header3 mc-label13">OPENING TIMES</div>
     <div class="mc-border-line mc-mar-line"></div>
          
          
          <div class="open-time">
            <div class="time-container">
              <div class="open-day mc-fl"><input type="checkbox"> <label class="mc-label15">Monday</label></div>
              <div class="select-time  mc-fl">
<div class="time-extended">
  <label class="mc-label14 mc-pad-r2">From</label><input class="input-queue" type="text"> <label class="mc-label14 mc-pad-r2">To</label><input class="input-queue" type="text"> 
</div>
<div class="mc-clear"></div>
<div class="time-extended">
              <label class="mc-label14 mc-pad-r2">From</label><input class="input-queue" type="text"> <label class="mc-label14 mc-pad-r2">To</label><input class="input-queue" type="text"> 
               </div>
              </div>
              <div class="mc-btn-add-more">ADD MORE</div>
            </div>
          
          </div><!--open time end -->
          
          
          
          <div class="mc-clear"></div>
          <div class="mc-border-line mc-mar-line"></div>
          <div class="mc-clear"></div>
          
          <div class="open-time">
            <div class="time-container">
              <div class="open-day-tuesday mc-fl"><input type="checkbox"> <label class="mc-label15">Tuesday</label></div>
              <div class="select-time  mc-fl">
<div class="time-extended">
  <label class="mc-label14 mc-pad-r2">From</label><input class="input-queue" type="text"> <label class="mc-label14 mc-pad-r2">To</label><input class="input-queue" type="text"> 
</div>
<div class="mc-clear"></div>

              </div>
              <div class="mc-btn-add-more mc-fl">ADD MORE</div>
            </div>
          
          </div><!--open time end -->
          
          
          
          
          
           <div class="mc-clear"></div>
          <div class="mc-border-line mc-mar-line"></div>
          <div class="mc-clear"></div>
          
          <div class="open-time">
            <div class="time-container">
              <div class="open-day-wed mc-fl"><input type="checkbox"> <label class="mc-label15">Wednesday</label></div>
              <div class="select-time  mc-fl">

<div class="mc-clear"></div>
<div class="time-extended">
              <label class="mc-label14 mc-pad-r2">From</label><input class="input-queue" type="text"> <label class="mc-label14 mc-pad-r2">To</label><input class="input-queue" type="text"> 
               </div>
              </div>
              <div class="mc-btn-add-more mc-fl">ADD MORE</div>
            </div>
          
          </div><!--open time end -->
          
          
          
           <div class="mc-clear"></div>
          <div class="mc-border-line mc-mar-line"></div>
          <div class="mc-clear"></div>
          
          <div class="open-time">
            <div class="time-container">
              <div class="open-day-thu mc-fl"><input type="checkbox"> <label class="mc-label15">Thursday</label></div>
              <div class="select-time  mc-fl">

<div class="mc-clear"></div>
<div class="time-extended">
              <label class="mc-label14 mc-pad-r2">From</label><input class="input-queue" type="text"> <label class="mc-label14 mc-pad-r2">To</label><input class="input-queue" type="text"> 
               </div>
              </div>
              <div class="mc-btn-add-more mc-fl">ADD MORE</div>
            </div>
          
          </div><!--open time end -->
          
          
          
          
          <div class="mc-clear"></div>
          <div class="mc-border-line mc-mar-line"></div>
          <div class="mc-clear"></div>
          
          <div class="open-time">
            <div class="time-container">
              <div class="open-day-fri mc-fl"><input type="checkbox"> <label class="mc-label15">Friday</label></div>
              <div class="select-time  mc-fl">
<div class="time-extended">
  <label class="mc-label14 mc-pad-r2">From</label><input class="input-queue" type="text"> <label class="mc-label14 mc-pad-r2">To</label><input class="input-queue" type="text"> 
</div>
<div class="mc-clear"></div>

              </div>
              <div class="mc-btn-add-more mc-fl">ADD MORE</div>
            </div>
          
          </div><!--open time end -->
          
          
          
          
          
          <div class="mc-clear"></div>
          <div class="mc-border-line mc-mar-line"></div>
          <div class="mc-clear"></div>
          
          <div class="open-time">
            <div class="time-container">
              <div class="open-day-sat mc-fl"><input type="checkbox"> <label class="mc-label15">Saturday</label></div>
              <div class="select-time  mc-fl">
<div class="time-extended">
  <label class="mc-label14 mc-pad-r2">From</label><input class="input-queue" type="text"> <label class="mc-label14 mc-pad-r2">To</label><input class="input-queue" type="text"> 
</div>
<div class="mc-clear"></div>
<div class="time-extended">
              <label class="mc-label14 mc-pad-r2">From</label><input class="input-queue" type="text"> <label class="mc-label14 mc-pad-r2">To</label><input class="input-queue" type="text"> 
               </div>
              </div>
              <div class="mc-btn-add-more mc-fl">ADD MORE</div>
            </div>
          
          </div><!--open time end -->
          
          
          
          
          <div class="mc-clear"></div>
          <div class="mc-border-line mc-mar-line"></div>
          <div class="mc-clear"></div>
          
          <div class="open-time">
            <div class="time-container">
              <div class="open-day-sun mc-fl"><input type="checkbox"> <label class="mc-label15">Sunday</label></div>
              <div class="select-time  mc-fl">
<div class="time-extended">
  <label class="mc-label14 mc-pad-r2">From</label><input class="input-queue" type="text"> <label class="mc-label14 mc-pad-r2">To</label><input class="input-queue" type="text"> 
</div>
<div class="mc-clear"></div>
<div class="time-extended">
              <label class="mc-label14 mc-pad-r2">From</label><input class="input-queue" type="text"> <label class="mc-label14 mc-pad-r2">To</label><input class="input-queue" type="text"> 
               </div>
              </div>
              <div class="mc-btn-add-more mc-fl">ADD MORE</div>
            </div>
          
          </div><!--open time end -->
          
          
          
          <div class="mc-clear"></div>
          <div class="mc-border-line mc-mar-line"></div>
          <div class="mc-clear"></div>
          
          
          <div class="mc-btn-container">
             	<div class="mc-btn-save-changes">SAVE CHANGES</div>
             	<div class="mc-btn-cancel">Cancel</div>
           	</div>
        </fieldset>
        </form>
          <div class="mc-clear"></div>
         <div class="mc-blank"></div>
        
        </div><!--PROFILE FORM END-->
    </div><!--DOCTOR PROFILE DETAILS END-->
    
   
   
   
    <div class="mc-profile-detail-rcolum mc-fl mc-mar-l"> <!--PROFILE DETAIL RIGHT -->
     <div class="mc-header3 mc-label13">BOOKINGS</div>
     <div class="mc-border-line"></div>
     
     <div class="queue">
       <div class="queue-check"><input type="checkbox"> <label class="mc-label13">Queue No.</label></div>
       <div class="queue-input-container"><input class="input-queue" type="text"> <label class="mc-label14">Queue No.</label></div>
       <div class="queue-input-container"><input class="input-queue" type="text"> <label class="mc-label14">Duration</label></div>
     </div>
     
     <div class="queue">
       <div class="queue-check"><input type="checkbox"> <label class="mc-label13">Appointment Time</label></div>
      <div class="queue-input-container"><input class="input-queue" type="text"> <label class="mc-label14">Duration</label></div>
     </div>
    </div> 
      
    </div> 
    <!--PROFILE DETAIL RIGHT END -->
    <div class="mc-clear"></div>  
    
    
@include('common.footer-clinic')