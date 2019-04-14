 
<br>

<div class="col-md-12" style="padding: 0px;">
	<div class="col-md-12" style="padding: 0px;">
		<div class="col-md-2">
			<span style="float: right;"><img alt="" src="{{ URL::asset('assets/images/ico_Profile.svg') }}" width="75" height="75"></span>
		</div>
		<div class="col-md-8" style="padding-top: 15px; padding-bottom: 15px; border-bottom: 2px solid #DEDEDE;">
			<div style="float: left; font-size: 23px;">Time off for Your Clinic :</div>
			<!-- <span class="glyphicon glyphicon-plus staff-plus-btn" data-toggle="modal" data-target="#Clinic-time-off-Modal" style="float: right;" onclick="newTimeOff()"></span> -->
			<img src="{{ URL::asset('assets/images/ico_add new.svg') }}" width="25" height="25" data-toggle="modal" data-target="#Clinic-time-off-Modal" style="float: right; cursor: pointer; padding-top: 4px;" onclick="newTimeOff()">
			
		</div>
	</div>

	<br><br><br><br>

<?php if($Clinic_Holiday){ ?>

	<br>

	<div id="time-off-panel">


	<?php foreach ($Clinic_Holiday as $value) { 

		$Start_date = date('d M Y', strtotime($value->From_Holiday));
		$End_date = date('d M Y', strtotime($value->To_Holiday));
    	
		if($value->Type == 1 ){
	?>

		<div class="row line-break">
			<div class="col-md-2">&nbsp;</div>
			<div id="{{ $value->ManageHolidayID}}" class="clinic-time-off" style="cursor: pointer;">
				<div class="col-md-1" style="padding: 0px;">
					<img src="{{ URL::asset('assets/images/ico_time.svg') }}" width="60" height="65">
				</div>
				<div class="col-md-6" style="padding-top: 14px; font-size: 13px;">
					<div class="col-md-12" style="padding: 0px;"><span style="font-weight: bold;">{{ $Start_date }}, {{ $value->From_Time}} to {{ $End_date }}, {{ $value->To_Time}}</span></div>
					<div class="col-md-12" style="padding: 0px; color: #999999;">{{ $value->Note}}</div>
				</div>
				<!-- <div class="col-md-1">
					<span style="color: #999999;">No Repeat</span>
				</div> -->
				<div class="col-md-1" style="padding-top: 14px; font-size: 13px;">
					<span style="color: #999999;">Break</span>
				</div>
			</div>
		</div>
		<br>

		<?php } else { ?>

		<div class="row line-break">
			<div class="col-md-2">&nbsp;</div>
			<div id="{{ $value->ManageHolidayID}}" class="clinic-time-off" style="cursor: pointer;">
				<div class="col-md-1" style="padding: 0px;">
					<img src="{{ URL::asset('assets/images/ico_calender.svg') }}" width="60" height="60">
				</div>
				<div class="col-md-6" style="padding-top: 14px; font-size: 13px;">
					<div class="col-md-12" style="padding: 0px;"><span style="font-weight: bold;">{{ $Start_date }} to {{ $End_date }}</span></div>
					<div class="col-md-12" style="padding: 0px; color: #999999;">{{ $value->Note}}</div>
				</div>
				<!-- <div class="col-md-1">
					<span style="color: #999999;">No Repeat</span>
				</div> -->
				<div class="col-md-1" style="padding-top: 14px; font-size: 13px;">
					<span style="color: #999999;">Full Day</span>
				</div>
			</div>
		</div>
		<br>

		<?php } } ?>

	</div>

	<?php } else { ?>

	<div id="no-time-off-panel" class="">
		<div class="col-md-2">&nbsp;</div>
		<div class="col-md-8" style="text-align: center;border: 1px solid #BDBDBD; border-radius: 5px; padding-top: 25px; padding-bottom: 32px;">
			<div style="padding-bottom: 20px; font-weight: bold;">Add your first Time off by clicking below button</div>
			<div>
				<span id="time-off-add-btn" data-toggle="modal" data-target="#Clinic-time-off-Modal" onclick="newTimeOff()">
				<img src="{{ URL::asset('assets/images/ico_add new.svg') }}" width="25" height="25">
				<span style="margin: 4px; color: black;">Add Time Off</span></span>
			</div>
		</div>
	</div>

	<?php }?>

</div>

<div id="Clinic-time-off-Modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 id="time-off-modal-title" class="modal-title">Add New Time Off</h4>
      </div>
      <div class="modal-body" style="background: white; padding-bottom: 0px; font-size: 12px;">

        <div class="panel panel-default" style="border: 0px;">
        	<div class="panel-body" style="padding-bottom: 0px;">

        	<input id="h-clinic-holiday-id" type="hidden">

	        	<div id="clinic-custom-time-off">
			  		<div>
				    	<label class="col-sm-2 details-label no-padding">Start Date</label>

					    <div class="col-sm-3 no-padding">
					    	<input type="text" id="clinic-custom-start-date" class="dropdown-btn slot-blocker-width time-off-datepicker clinic-time-off-change">
					    </div>

					    <label class="col-sm-2 details-label no-padding" style="padding-left: 50px;">Start Time</label>
					    	
					    <div class="col-sm-3 no-padding">
					    	<input type="text" id="clinic-custom-start-time" class="dropdown-btn slot-blocker-width time-off-timepicker clinic-time-off-change" value="08:00 AM">
						</div>
					    <br><br><br>
				    </div>

				    <div>
				    	<label class="col-sm-2 details-label no-padding">End Date</label>

					    <div class="col-sm-3 no-padding">
					    	<input type="text" id="clinic-custom-end-date" class="dropdown-btn slot-blocker-width time-off-datepicker clinic-time-off-change">
					    </div>

					    <label class="col-sm-2 details-label no-padding" style="padding-left: 50px;">End Time</label>

					    <div class="col-sm-3 no-padding">
						    <input type="text" id="clinic-custom-end-time" class="dropdown-btn slot-blocker-width time-off-timepicker clinic-time-off-change" value="04:00 PM">
						</div>
					    <br><br><br>
				    </div>
				</div>

				<div id="clinic-day-time-off">

					<div class="form-group">
					    <label class="details-label col-sm-2 no-padding">Start Date</label>

					    <div class="col-sm-8 no-padding">
					    	<input type="text" id="clinic-day-start-date" class="dropdown-btn time-off-datepicker clinic-time-off-change" placeholder="" style="height: 27px; width: 410px;">
					    </div>
					</div>
					<br><br><br>

					<div class="form-group">
					    <label class="details-label col-sm-2 no-padding">End Date</label>

					    <div class="col-sm-8 no-padding">
					    	<input type="text" id="clinic-day-end-date" class="dropdown-btn time-off-datepicker clinic-time-off-change" placeholder="" style="height: 27px; width: 410px;">
					    </div>
					</div>
					<br><br><br>

				</div>

				<div class="form-group">
				    <label class="details-label col-sm-2 no-padding">Notes</label>

				    <div class="col-sm-8 no-padding">
				    	<input type="text" id="clinic-time-off-note" class="dropdown-btn" placeholder="Details ..." style="height: 27px; width: 410px;">
				    </div>
				</div>
				<br><br><br>

				<div class="form-group">
				    <label class="details-label col-sm-2 no-padding">&nbsp;</label>

				    <div class="col-sm-8 no-padding">
				    	<div class="staff-day-checkbox">
						    <input id="clinic-day-checkbox" type="checkbox" name="check" value="check1" checked>
						    <label for="clinic-day-checkbox" style="padding-left: 25px; padding-top: 8px;"><b style="padding-left: 10px;">All Day</b></label>
					  	</div>
				    </div>
				</div>
				<br><br><br>

				<div class="well well-sm" id="clinic-time-wall" style="text-align: center; background: #FDFFE5;">From 29 March 2016 to 30 March 2016</div>

		  		<div class="panel-footer" style="background-color: white; padding-bottom: 0px;">
		  			<div id="exist-clinic-time-off">
		  				<div class="col-sm-5 no-padding">&nbsp;</div>
			  			<div class="col-sm-3" style="padding-left: 65px;"><button type="button" id="update-clinic-time-off" class="btn btn-update font-type-open-sans ext-left">Save Changes</button></div>
			  			<div class="col-sm-2 no-padding"><button type="button" id="delete-clinic-time-off" class="btn btn-update font-type-open-sans ext-left">Delete</button></div>
			  		</div>

			  		<div style="padding-left: 200px;" id="new-clinic-time-off" class="row">
			  			<button type="button" id="Add-clinic-time-off" class="btn btn-update font-type-open-sans ext-left">Save Changes</button>
			  		</div>
		  		</div>

			</div>
      	</div>
      	
    </div>

  </div>
</div>
</div>

<script type="text/javascript">

	jQuery(document).ready(function($) {

		$('.time-off-timepicker').timepicker({

	      'timeFormat' : 'h:i A',
	    });


	    $( ".time-off-datepicker" ).datepicker({

		    dateFormat : "dd MM yy" ,
		    // minDate : 0,

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

	function newTimeOff (){

		var monthNames = ["January", "February", "March", "April", "May", "June",
						  "July", "August", "September", "October", "November", "December"
						];

		var today = new Date();
		var dd = today.getDate();
		var mm = monthNames[today.getMonth()]; //January is 0! 
		var yyyy = today.getFullYear();

		if(dd<10) {
		    dd='0'+dd
		}

		today = dd+' '+ mm +' '+ yyyy;

		$('#exist-clinic-time-off').css('display', 'none');
		$('#new-clinic-time-off').css('display', 'block')
		$('#time-off-modal-title').html('Add New Time Off');

		$('#clinic-custom-start-date').val(today);
		$('#clinic-custom-end-date').val(today);
		$('#clinic-day-start-date').val(today);
		$('#clinic-day-end-date').val(today);
		$('#clinic-custom-start-time').val('08:00 AM');
		$('#clinic-custom-end-time').val('05:00 PM');
		$('#clinic-time-off-note').val('');
		$('#h-clinic-holiday-id').val('');

		$( ".clinic-time-off-change" ).trigger( "change" );
		$( "#clinic-day-checkbox" ).trigger( "change" );
	}

	function existTimeOff (){
			// 
		$('#new-clinic-time-off').css('display', 'none');
		$('#exist-clinic-time-off').css('display', 'block');
	}

</script>