

<br>

<div class="row" style="padding-bottom: 30px; padding-top: 15px; border-bottom: 1px solid #ddd;">
	<div class="col-md-2">
		<label style="font-weight: bold !important;">Time Zone</label>
	</div>
	<div class="col-md-8">
	<span style="font-size: 15px; color: #777;">Your Time Zone is set to <u style="cursor: pointer; color: #666666;">SINGAPORE (UTC+08:00)</u>.</span>
</div>
</div>

<br><br>

<div class="col-md-12">
	<div style="float: left; font-size: 22px;">Breaks For Your Clinic :</div>
</div>

<br><br><br><br>

<?php 
$month = array('mon' => 'Monday', 'tue' => 'Tuesday','wed' => 'Wednesday','thu' => 'Thursday','fri' => 'Friday','sat' => 'Saturday','sun' => 'Sunday');

foreach ($month as $key => $value) {

	foreach ($ClinicTimes as $clinic) {

	if($clinic->Mon==1){ $day = "Monday";}
	if($clinic->Tue==1){ $day = "Tuesday";}
	if($clinic->Wed==1){ $day = "Wednesday";}
	if($clinic->Thu==1){ $day = "Thursday";}
	if($clinic->Fri==1){ $day = "Friday";}
	if($clinic->Sat==1){ $day = "Saturday";}
	if($clinic->Sun==1){ $day = "Sunday";}

	if ($day == $value){

		// dd($doctorBreaks);

		$content = '';
	foreach ($clinicBreaks as $val) {

		
		if ($val->day == $key) {

			if ($clinic->Active == 1) {
		
			$content .= '<div  guid="" class="col-md-12 clinic-break" style="padding: 0;"> 
						<div class="col-md-4" style="padding-top: 5px;">
							<input guid="'.$val->id.'" class="break-timepicker clinic-break-time_from" style="float: right;" type="button" value="'.$val->start_time.'">
						</div>
						<span class="col-md-1 text-center" style="padding: 0; width: 12px; padding-top: 10px;">to</span>
						<div class="col-md-4" style="padding-top: 5px;">
							<input guid="'.$val->id.'" type="button" class="break-timepicker clinic-break-time_to" value="'.$val->end_time.'">
						</div>
						<span>
						<a guid="'.$val->id.'" href="#"  data-toggle="popover" class="clinic-break-pop" data-placement="left" data-trigger="focus" ><span class="glyphicon glyphicon-trash" aria-hidden="true" style="padding-top: 12px; color: black;"></span></a>
						</span>
						</div>';
			}
			else {

			$content .= '<div  guid="" class="col-md-12 clinic-break" style="padding: 0;"> 
						<div class="col-md-4" style="padding-top: 5px;">
							<input guid="'.$val->id.'" class="break-timepicker clinic-break-time_from" style="float: right; background: #EDEDEB; color: #ACACAA; border: 1px solid #ccc;" type="button" value="'.$val->start_time.'" disabled>
						</div>
						<span class="col-md-1 text-center" style="padding: 0; width: 12px; padding-top: 10px;">to</span>
						<div class="col-md-4" style="padding-top: 5px;">
							<input guid="'.$val->id.'" type="button" class="break-timepicker clinic-break-time_to" value="'.$val->end_time.'" style="background: #EDEDEB; color: #ACACAA; border: 1px solid #ccc;" disabled>
						</div>
						</div>';
			}
			
		}else {
			$content .= '';
		}

	}

	if ($clinic->Active == 1) {

 ?>

<div class="row line-break">
	<div class="col-md-2" style="padding-top: 11px;">
		<label style="padding-left: 16px;"><b>{{$value}}</b></label>
	</div>
	<div class="col-md-1" style="padding-top: 6px;">
		<button class="clinic-break-btn" id="add-break-{{$key}}">Add Break</button>
		<!-- <button class="add-break-btn"><i class="fa fa-plus" aria-hidden="true"></i></button> -->
	</div>
	<div class="col-md-6 clinic-break-panel-{{$key}}" style="padding: 0;">
		{{$content}}
	</div>
	<!-- <div class="add-break-time-wrapper">
		<div class="start-break-time-container">
			<select>
				<option>8:00 AM</option>
			</select>
		</div>
		<span>to</span>
		<div class="end-break-time-container">
			<select>
				<option>10:00 AM</option>
			</select>
		</div>
		<i class="fa fa-times" aria-hidden="true"></i>	
	</div> -->
</div>

<br>

<?php } else {?>

<div class="row line-break" style="color: #ACACAA;">
	<div class="col-md-2" style="padding-top: 11px;">
		<label style="padding-left: 16px;"><b>{{$value}}</b></label>
	</div>
	<div class="col-md-1" style="padding-top: 6px;">
		<button class="break-btn-disable" id="add-break-{{$key}}" disabled>Add Break</button>
	</div>
	<div class="col-md-6 clinic-break-panel-{{$key}}" style="padding: 0;">
		{{$content}}
	</div>
</div>

<br>


<?php } } } }?>



<script type="text/javascript">

	jQuery(document).ready(function($) {

		$('.break-timepicker').timepicker({

	      'timeFormat' : 'h:i A',
	    });

	    // --------- Set Navigation bar height ------------------

		var page_height = $('#profile-detail-wrapper').height()+52;
		var win_height = $(window).height()

		if (page_height > win_height){

		    $("#setting-navigation").height($('#profile-detail-wrapper').height()+52);
		    $("#profile-side-list").height($('#profile-detail-wrapper').height()+52);
		}
		else{

		    $("#setting-navigation").height($(window).height()-52);
		    $("#profile-side-list").height($(window).height()-52);
		}
	});

</script>

<style>

	.clinic-break-btn {
	        background: #1b9bd7;
		    border: 0px;
		    color: white;
		    width: 85px;
		    height: 26px;
		    border-radius: 3px;
		    font-weight: bold;
	}

	.break-timepicker {
		background: white;
	   	border: 1px solid #999999;
	   	height: 21px;
	   	color:#777676;
	}
	
</style>