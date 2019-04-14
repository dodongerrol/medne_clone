
<br>

<div class="row" style="padding-bottom: 30px; padding-top: 15px; border-bottom: 1px solid #ddd; margin-left: 5px;">
	<div class="col-md-2">
		<label style="font-weight: bold !important;">Time Zone</label>
	</div>
	<div class="col-md-8">
	<span style="font-size: 15px; color: #777;">Your Time Zone is set to <u style="cursor: pointer; color: #666666;">SINGAPORE (UTC+08:00)</u>.</span>
</div>
</div>

<br><br>

<div class="col-md-12">
	<div style="float: left; font-size: 22px;">Clinic Opening Times :</div>
</div>

<br><br><br><br>

<?php 
foreach ($ClinicTimes as $value) {


	if($value->Mon==1){ $day = "Monday";}
	if($value->Tue==1){ $day = "Tuesday";}
	if($value->Wed==1){ $day = "Wednesday";}
	if($value->Thu==1){ $day = "Thursday";}
	if($value->Fri==1){ $day = "Friday";}
	if($value->Sat==1){ $day = "Saturday";}
	if($value->Sun==1){ $day = "Sunday";}

	if ($value->Active==1) {
		$checked = 'checked';
	} else {
		$checked = '';
	}
	

 ?>

<div class="row" style="margin-left: 5px;">
	
	<div class="col-md-2">
		<label style="font-weight: bold !important; padding-top: 3px;" class="day-name">{{ $day }}</label>
	</div>
	<div class="col-md-1 div-chk">
		<input id="time" data-toggle="toggle" data-size="mini" style="float: right;" name="{{$value->ClinicTimeID}}" class="chk_activate" type="checkbox" data-onstyle="info" <?php echo $checked ?> >
	</div>
	<div class="col-md-3" >
		<input class="clinic-time-pick time-from pick-color" style="float: right;" type="button" value="{{$value->StartTime}}">
	</div>
	<span class="col-md-2 text-center" style="padding: 0;width: 25px; padding-top: 5px; color: #777676;">to</span>
	<div class="col-md-3">
		<input type="button" class="clinic-time-pick time-to pick-color" value="{{$value->EndTime}}">
	</div>
</div>

<br>

<?php }

 ?>

<script type="text/javascript">

	jQuery(document).ready(function($) {

		$("[data-toggle='toggle']").bootstrapToggle('destroy')                 
    	$("[data-toggle='toggle']").bootstrapToggle();

		$('.clinic-time-pick').timepicker({

	      'timeFormat' : 'h:i A',
	    });


	$(document).on('change', 'input.chk_activate', function(event) {

		// evt.stopPropagation();
		// evt.preventDefault();

      	var time_id = $(this).attr('name');
	    var status = $(this).prop('checked');
	    if (status) { status=1;} else { status=0;}

		// alert(status);

	    	$.ajax({
		      url: base_url+'setting/staff/updateWorkingHoursStatus',
		      type: 'POST',
		      data:{time_id:time_id, status:status}
		    })
		    .done(function(data) {

		    $('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');

        	setTimeout(function(){

				$('#alert_box').css('display', 'none');

			}, 500);
		    });

		    event.stopImmediatePropagation();
	    	return false;

	});

	$(document).on('change', '.clinic-time-pick', function(event) {

		var time_from = $(this).closest('.row').find('.time-from').val();
	    var time_to = $(this).closest('.row').find('.time-to').val();
	    var day_name = $(this).closest('.row').find('.day-name').text();

		$.ajax({
		    url: base_url+'calendar/updateClinicWorkingHours',
		    type: 'POST',
		    data:{ time_from:time_from, time_to:time_to, day_name:day_name }
		})
		.done(function(data) {

			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');

        	setTimeout(function(){

				$('#alert_box').css('display', 'none');

			}, 500);

		});

		event.stopImmediatePropagation();
	    return false;

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

	.div-chk .toggle {
		float:right;
	}
	.pick-color{
		background: white;
	   	border: 1px solid #999999;
	   	height: 21px;
	   	color:#777676;
	}
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