
<br>

<div class="col-md-12" style="padding: 0px;">
	<div class="col-md-2">
		<span style="float: right;"><img alt="" src="{{ URL::asset('assets/images/ico_Profile.svg') }}" width="75" height="75"></span>
	</div>
	<div class="col-md-8" style="padding-top: 15px; padding-bottom: 15px; /*border-bottom: 2px solid #DEDEDE;*/">
		<div style="float: left; font-size: 25px;">Breaks for you :</div>
		
	</div>

	

</div>

<br><br><br><br><br>

<?php 
$month = array('mon' => 'Monday', 'tue' => 'Tuesday','wed' => 'Wednesday','thu' => 'Thursday','fri' => 'Friday','sat' => 'Saturday','sun' => 'Sunday');

foreach ($month as $key => $value) {

	foreach ($findDoctorTimes as $doctor) {

	if($doctor->Mon==1){ $day = "Monday";}
	if($doctor->Tue==1){ $day = "Tuesday";}
	if($doctor->Wed==1){ $day = "Wednesday";}
	if($doctor->Thu==1){ $day = "Thursday";}
	if($doctor->Fri==1){ $day = "Friday";}
	if($doctor->Sat==1){ $day = "Saturday";}
	if($doctor->Sun==1){ $day = "Sunday";}

	if ($day == $value){

		// dd($doctorBreaks);

		$content = '';
	foreach ($doctorBreaks as $val) {

		
		if ($val->day == $key) {

			if ($doctor->Active == 1) {
		
			$content .= '<div  guid="" class="col-md-12 doc-break" style="padding: 0;"> 
						<div class="col-md-4" style="padding-top: 5px;">
							<input guid="'.$val->id.'" class="timepicker doc-break-time_from" style="float: right;" type="button" value="'.$val->start_time.'">
						</div>
						<span class="col-md-1 text-center" style="padding: 0; width: 12px; padding-top: 8px;">to</span>
						<div class="col-md-4" style="padding-top: 5px;">
							<input guid="'.$val->id.'" type="button" class="timepicker doc-break-time_to" value="'.$val->end_time.'">
						</div>
						<span>
						<a guid="'.$val->id.'" href="#"  data-toggle="popover" class="break-pop" data-placement="left" data-trigger="focus" ><span class="glyphicon glyphicon-trash" aria-hidden="true" style="padding-top: 10px; color: black;"></span></a>
						</span>
						</div>';
			}
			else {

			$content .= '<div  guid="" class="col-md-12 doc-break" style="padding: 0;"> 
						<div class="col-md-4" style="padding-top: 5px;">
							<input guid="'.$val->id.'" class="timepicker doc-break-time_from" style="float: right; background: #EDEDEB; color: #ACACAA;" type="button" value="'.$val->start_time.'" disabled>
						</div>
						<span class="col-md-1 text-center" style="padding: 0; width: 12px; padding-top: 8px;">to</span>
						<div class="col-md-4" style="padding-top: 5px;">
							<input guid="'.$val->id.'" type="button" class="timepicker doc-break-time_to" value="'.$val->end_time.'" style="background: #EDEDEB; color: #ACACAA;" disabled>
						</div>
						</div>';
			}
			
		}else {
			$content .= '';
		}

	}

	if ($doctor->Active == 1) {

 ?>
	

<div class="row line-break">
	<div class="col-md-2">&nbsp;</div>
	<div class="col-md-1" style="padding-top: 10px;">
		<label><b>{{$value}}</b></label>
	</div>
	<div class="col-md-1" style="padding-top: 4px;">
		<button class="staff-break-btn" id="add-break-{{$key}}">Add Break</button>
	</div> 
	<div class="col-md-6 doc-break-panel-{{$key}}" style="padding: 0;">
		{{$content}}
	</div>
</div>

<br>

<?php } else {?>


<div class="row line-break" style="color: #ACACAA;">
	<div class="col-md-2">&nbsp;</div>
	<div class="col-md-1" style="padding-top: 10px;">
		<label><b>{{$value}}</b></label>
	</div>
	<div class="col-md-1" style="padding-top: 4px;">
		<button class="break-btn-disable" id="add-break-{{$key}}" disabled>Add Break</button>
	</div> 
	<div class="col-md-6 doc-break-panel-{{$key}}" style="padding: 0;">
		{{$content}}
	</div>
</div>

<br>


<?php } } } }?>




<script type="text/javascript">

	jQuery(document).ready(function($) {

		$('.timepicker').timepicker({

	      'timeFormat' : 'h:i A',
	    });

	    // --------- Set Navigation bar height ------------------

	    var page_height = $('#detail-wrapper').height()+52;
	    var win_height = $(window).height();

	    // alert ('page - '+page_height+ ', window - '+win_height);

	    if (page_height > win_height){

	        $("#setting-navigation").height($('#detail-wrapper').height()+52);
	        $(".staff-side-list").height($('#detail-wrapper').height()+52);
	    }
	    else{

	        $("#setting-navigation").height($(window).height()-52);
	        $(".staff-side-list").height($(window).height()-52);
	    }

	    $("#staff-doctor-list").height(($('.staff-side-list').height() / 2) -75);
		$("#staff-list").height(($('.staff-side-list').height() / 2) -75);
		
	});

</script>