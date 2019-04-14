
<style type="text/css">
	.div-chk .toggle {
		float:right;
	}	
</style>


<br>
<div class="col-md-12" style="padding: 0px;">
	<div class="col-md-2">
		<span style="float: right;"><img alt="" src="{{ URL::asset('assets/images/ico_Profile.svg') }}" width="75" height="75"></span>
	</div>
	<div class="col-md-8" style="padding-top: 15px; padding-bottom: 15px; border-bottom: 2px solid #DEDEDE;">
		<div style="float: left; font-size: 25px;">Working Hours for you :</div>
		
	</div>

	

</div>

<br><br><br><br><br>


<?php 
foreach ($findDoctorTimes as $value) {


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


<div class="row">
	<div class="col-md-2">&nbsp;</div>
	<div class="col-md-1">
		<label style="float: right;" class="day-name">{{$day}}</label>
	</div>
	<div class="col-md-1 div-chk">
		<input data-toggle="toggle" data-size="mini" style="float: right;" name="{{$value->ClinicTimeID}}" class="chk_activate" type="checkbox" data-onstyle="info" <?php echo $checked ?>>
	</div>
	<div class="col-md-2" >
		<input class="timepicker time-from" style="float: right; color:#777676;" type="button" value="{{$value->StartTime}}">
	</div>
	<span class="col-md-1 text-center" style="padding: 0;width: 12px;">to</span>
	<div class="col-md-2">
		<input type="button" class="timepicker time-to" style=" color:#777676;" value="{{$value->EndTime}}">
	</div>
</div>
<br>

<?php }

 ?>



<script type="text/javascript">

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
	
		$("[data-toggle='toggle']").bootstrapToggle('destroy');                 
    $("[data-toggle='toggle']").bootstrapToggle();
		

	jQuery(document).ready(function($) {

		

		$('.timepicker').timepicker({
      'timeFormat' : 'h:i A',
    });

	  $('input.chk_activate').change(function(evt) {
      	evt.stopPropagation();
				evt.preventDefault();

      	var time_id = $(this).attr('name');
	    	var status = $(this).prop('checked');
	    	if (status) { status=1;} else { status=0;}
				// alert(status);
	    	$('#alert_box').css('display', 'block');
				$('#alert_box').html('Updating...');

	    	$.ajax({
		      url: base_url+'setting/staff/updateWorkingHoursStatus',
		      type: 'POST',
		      data:{time_id:time_id, status:status}
		    })
		    .done(function(data) {
		    	$('#alert_box').css('display', 'none');
		    });	

   		})

	    $('.timepicker').on('changeTime', function() {
		    
	    	var time_from = $(this).closest('.row').find('.time-from').val();
	    	var time_to = $(this).closest('.row').find('.time-to').val();
	    	var day_name = $(this).closest('.row').find('.day-name').text();
	    	var doctor_id = $('#h-doctor-id').val();

	    	$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
				
			$.ajax({
		      url: base_url+'setting/staff/updateWorkingHours',
		      type: 'POST',
		      data:{doctor_id:doctor_id, time_from:time_from, time_to:time_to, day_name:day_name}
		    })
		    .done(function(data) {
		    	$('#alert_box').css('display', 'none');
		    });	

 
		});




	});
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