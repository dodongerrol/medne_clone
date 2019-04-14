<script type="text/javascript" src="<?php echo $server; ?>/assets/settings/account/account.js?_={{ $date->format('U') }}"></script>
<link rel="stylesheet" href="<?php echo $server; ?>/assets/settings/account/account.css?_={{ $date->format('U') }}">
<br>

<div class="account-container">

<div class="col-md-12" style="padding: 0px; padding-bottom: 15px; border-bottom: 1px solid #ccc;">
<span style="padding-top: 15px; font-size: large; font-weight: bold;">Customize your account preferences</span>
</div>
<br><br><br>

<?php

	$checked = '';
	$checked2 = '';
	$checked3 = '';

	if ($account[0]->Calendar_type == 1){

		$checked = 'checked';
	}
	else if ($account[0]->Calendar_type == 2){

		$checked2 = 'checked';
	}
	else if ($account[0]->Calendar_type == 3){

		$checked3 = 'checked';
	}


	if ($account[0]->Calendar_day == 1){
		$day = 'Monday';

	}else if ($account[0]->Calendar_day == 2){
		$day = 'Tuesday';

	}else if ($account[0]->Calendar_day == 3){
		$day = 'Wednesday';
		
	}else if ($account[0]->Calendar_day == 4){
		$day = 'Thursday';
		
	}else if ($account[0]->Calendar_day == 5){
		$day = 'Friday';
		
	}else if ($account[0]->Calendar_day == 5){
		$day = 'Saturday';
		
	}else if ($account[0]->Calendar_day == 7){
		$day = 'Sunday';
		
	}


?>


	
<div class="col-md-12" style="padding: 0px;">

	<div class="row line-break"><br>
		<div class="col-md-3" style="clear: both">
			<label class="acc-lbl">Default Calendar View</label>
		</div>
		<div class="col-md-4">
			<label class="acc-lbl">This will be your default calendar view</label><br>
			<label style="cursor: pointer;"><input id="2" type="radio" class="cal-type" name="type" <?php echo $checked2;?> style="width: 15px;"><b class="acc-lbl" style="padding-left: 10px;">Daily Calendar</b></label><br>
			<label style="cursor: pointer;"><input id="1" type="radio" class="cal-type" name="type" <?php echo $checked;?> style="width: 15px;"><b class="acc-lbl" style="padding-left: 10px;">Weekly Calendar</b></label><br>
			<label style="cursor: pointer;"><input id="3" type="radio" class="cal-type" name="type" <?php echo $checked3;?> style="width: 15px;"><b class="acc-lbl" style="padding-left: 10px;">Monthly Calendar</b></label>
		</div>
	</div>

	<div class="row line-break"><br>
		<div class="col-md-3" style="clear: both">
			<label class="acc-lbl" style="padding-top: 6px;">Start the Week with</label>
		</div>
		<div class="col-md-4">
			
  			<div class="right-inner-addon">
				<span class="caret" style="float: right; margin-top: 16px; position: absolute; margin-left: 105px; color: #666666 !important;"></span>
		        <input type="button" id="{{ $account[0]->Calendar_type }}" class="btn dropdown-toggle acc-dropdown day-section" value="{{ $day }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 80px !important;">

		        <ul class="dropdown-menu" id="day-list" style="min-width: 125px;">
	    			<li><a id="1" href="#">Monday</a></li>
	    			<li><a id="2" href="#">Tuesday</a></li>
	    			<li><a id="3" href="#">Wednesday</a></li>
	    			<li><a id="4" href="#">Thursday</a></li>
	    			<li><a id="5" href="#">Friday</a></li>
	    			<li><a id="6" href="#">Saturday</a></li>
	    			<li><a id="7" href="#">Sunday</a></li>
  				</ul>
		    </div>

		</div>
	</div>


	<div class="row line-break"><br>
		<div class="col-md-3" style="clear: both">
			<label class="acc-lbl" style="padding-top: 6px;">Calendar Time Unit</label>
		</div>
		<div class="col-md-4">

			<div class="right-inner-addon">
				<span class="caret" style="float: right; margin-top: 16px; position: absolute; margin-left: 105px; color: #666666 !important;"></span>
		        <input type="button" id="{{ $account[0]->Calendar_duration }}" class="btn dropdown-toggle acc-dropdown duration-section" value="{{ $account[0]->Calendar_duration }} Min" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 80px !important;">

		        <ul class="dropdown-menu" id="duration-list" style="min-width: 125px;">
					<li><a id="5" href="#">05 Min</a></li>
					<li><a id="15" href="#">15 Min</a></li>
					<li><a id="30" href="#">30 Min</a></li>
					<li><a id="45" href="#">45 Min</a></li>
					<li><a id="60" href="#">60 Min</a></li>
  				</ul>
		    </div>

		</div>
	</div>

	<div class="row line-break"><br>
		<div class="col-md-3" style="clear: both">
			<label class="acc-lbl" style="padding-top: 6px;">Calendar Start Hour</label>
		</div>
		<div class="col-md-4">
			<!-- <a class="btn dropdown-toggle acc-dropdown"><span class="hour-section" id="">12.00 AM</span>
				<span class="caret"></span>
			</a> -->
			<!-- <input type="button" class="btn dropdown-toggle acc-dropdown hour-section" value="{{ $account[0]->Calendar_Start_Hour }}"> -->

			<div class="right-inner-addon">
				<span class="caret" style="float: right; margin-top: 16px; position: absolute; margin-left: 105px; color: #666666 !important;"></span>
		        <input type="button" class="btn dropdown-toggle acc-dropdown hour-section" value="{{ $account[0]->Calendar_Start_Hour }}" style="width: 80px !important;">
		    </div>

		</div>
	</div>

	<div class="row line-break"><br>
		<div class="col-md-3" style="clear: both">
			<label class="acc-lbl" style="padding-top: 6px;">Enable PIN Verification</label>
		</div>
		<div class="col-md-4">
			<input id="clinic-pin-toggle"  type="checkbox" <?php {{ if($account[0]->Require_pin == 1 ){ echo "checked"; } }}?> data-toggle="toggle" data-size="mini" data-onstyle="info" class="abc" value="{{$account[0]->Require_pin}}">
		</div>
	</div>

</div>
</div>

<script type="text/javascript">

	// ------ Bootsrap toggle button config ------

    $("[data-toggle='toggle']").bootstrapToggle('destroy')                 
    $("[data-toggle='toggle']").bootstrapToggle();

    // -------------------------------------------

	$('.hour-section').timepicker({

	    'timeFormat' : 'h:i A',
	    'step': 60,
	});
	
	// --------- Set Navigation bar height ------------------

    var page_height = $('#setting-nav-panel').height()+52;
    var win_height = $(window).height();

    if (page_height > win_height){

        $("#setting-navigation").height($('#setting-nav-panel').height()+52);
    }
    else{

        $("#setting-navigation").height($(window).height()-52);
    }
    
</script>


<style>
	.toggle.btn {
		min-width: 40px;
		min-height: 25px; 
	}
	.btn-info {
   		background-image: -webkit-linear-gradient(top,#1b9bd7 0,#1b9bd7 100%);
    }
    .btn-info:focus, .btn-info:hover {
   		background-color: #1b9bd7;
    	background-position: 0px;
   	}
</style>