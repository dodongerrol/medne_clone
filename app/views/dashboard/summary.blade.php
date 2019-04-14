@include('common.home_header')
<script type="text/javascript" src="<?php echo $server; ?>/assets/dashboard/dashboard.js?_={{ $date->format('U') }}"></script>
<div class="summary-dashboard-wrapper">
	<div class="col-md-12 statistics text-center">
		<div class="col-md-2" style="    z-index: 1;">
			<!-- <select class="form-control">
				<option value="">Sep 08, 2016 - Sep 15, 2016</option>
			</select> -->
			<button id="summary-datepicker-button" class="btn"><span class="date-from"></span> - <span class="date-to"></span> <i class="fa fa-caret-down"></i></button>

			<div class="summary-datepicker-wrapper">
				<div class="custom row" >
					<div class="col-md-12" style="margin-bottom: 20px">
						<div class="form-inline text-left" >
							<label style="margin-right: 15px;">Date Range:</label>
							<select id="custom-date-range" class="form-control">
								<option value="">Custom</option>
								<option>Today</option>
								<option>Last Week</option>
								<option>Last Month</option>
								<option>This Month</option>
								<option>All Records</option>
							</select>
						</div>
						<a id="close-summary-datepicker" href="#" style="position: absolute;top: -10px;right: 53px;"><i class="fa fa-times-circle red" style="font-size: 20px;"></i></a>
					</div>
				</div>

				<div class="range row">
					<div class="col-md-4 no-padding-right" style="width: 40%;">
						<input id="dateFrom" type="text" class="form-control datePick">
					</div>
					<div class="col-md-1 no-padding" style="font-size: 21px;">
						-
					</div>
					<div class="col-md-4 no-padding-left" style="width: 40%;">
						<input id="dateTo" type="text" class="form-control datePick">
					</div>
				</div>

				<div class="calendar row">
					<div id="summary-datepicker"></div>		
				</div>
				
				<div class="row">
					<div class="col-md-11 text-center">
						<button class="btn btn-primary btn-sm " style="background-color: #337ab7 !important;border: none !important;border-radius: 4px;" id="submit-date-filter">Submit</button>
					</div>
				</div>		
			</div>
			
		</div>
		<div class="col-md-2 border-left">
			<h2 class="click-tooltip" data-toggle="popover" data-placement="bottom" data-content="Shows all concluded appointments" id="appointments">0</h2>
			<p>Appointments</p>
		</div>
		<div class="col-md-2 border-left">
			<h2 class="click-tooltip" data-toggle="popover" data-placement="bottom" data-content="Total revenue ($ amount input in the calculator after each appointment) of all appointments.">$<span id="total_revenue">0</span></h2>
			<p>Total Revenue</p>
		</div>
		<div class="col-md-2 border-left">
			<h2 class="click-tooltip" data-toggle="popover" data-placement="bottom" data-content="“This is the amount we co-paid as part of the corporate rate initiative for our clients.">$<span id="collected">0</span></h2>
			<p>Mednefits Co-Paid</p>
		</div>
		<!-- <div class="col-md-2 border-left">
			<h2 class="green click-tooltip" data-toggle="popover" data-placement="bottom" data-content="Medi-Credit minus Medicloud transaction fees." hidden>$100.00</h2>
			<h2 class="red click-tooltip" data-toggle="popover" data-placement="bottom" data-content="Medi-Credit minus Medicloud transaction fees." >-$<span id="credits">0</span></h2>
			<p>Credits</p>
		</div> -->
	</div>

	<div class="col-md-7 schedule" style="height: 100%">
		<div class="schedule-wrapper" >
			<div class="col-md-11-5 schedule-header">
				<h4 style="line-height: 2;">SCHEDULE <a href="{{URL::to('app/clinic/appointment-home-view')}}" class="btn btn-default pull-right book-appointment">Book Appointment</a></h4>
			</div>

			<div class="schedule-loop" id="schedule-view">
			</div>
		</div>
	</div>

	<div class="col-md-5 transaction" style="height: 100%">
		<div class="transaction-wrapper" >
			<div class="col-md-12 header">
				<h4 class="bold" style="line-height: 2;">TRANSACTION HISTORY </h4>
			</div>

			<div id="transaction-view">
			</div>

		</div>
	</div>
</div>

<!-- ..................modal pop up...... -->

<div class="modal fade" id="editAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="editAppointmentModalLabel" data-id="">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="editAppointmentModalLabel" style="padding-left: 35px;">Appointment</h4>
		</div>

	    <div class="modal-body" style="font-size: 12px;">

			<ul class="nav nav-tabs" id="tabs" style="padding-bottom: 5px;">
				<li class="active enabledTab"><a data-toggle="tab" href="#booking">BOOKING</a></li>
				<li id="patient-tab" class="disabledTab"><a data-toggle="tab" href="#patient">PATIENT</a></li>
			</ul>

		  	<!-- Details tab contents -->

		    <div class="tab-content">
			    <div id="booking" class="tab-pane fade in active">

			        <div class="panel panel-default" style="margin: 0px;">
			            <div class="panel-body">
			            <input type="hidden" id="user_id">
						  	<!-- Doctor list dropdown -->

						  	<div class="form-group">
							    <label class="col-sm-2 details-label" style="padding: 0px; padding-left: 33px;">Doctor</label>
							    <div class="col-sm-8">
							    	<label class="dropdown-btn input-width" data-toggle="dropdown" style="height: 38px; cursor: pointer;"><i class="glyphicon glyphicon-ok" style="color: #1b9bd7"></i>&nbsp;&nbsp;<span class="doctor-selection" id=""></span><div class="ext-right"><i class="glyphicon glyphicon-chevron-down"></i></div></label>
							    		<ul class="dropdown-menu dropdown-btn ul-width" id="appointment-doctor-list">
										    <?php foreach ($doctorlist as $val) { ?>
									        <li><a href="#" id="{{ $val->DoctorID}}">
									        <span><img src="{{ URL::asset('assets/images/ico_Profile.svg') }}" alt="">&nbsp;&nbsp;</span>{{ $val->DocName}}</a></li>
									        <?php } ?>
										</ul>
							    </div>
						    </div>
						    <br><br><br>

						    <!-- slot blocker service list dropdown -->
						    <div class="form-group" id="abc">
							    <label class="col-sm-2 details-label" style="padding: 0px; padding-left: 33px;">Service</label>

							    <input type="hidden" name="" value="" id="h-duration">
							    <div class="col-sm-3">
							    	<label class="dropdown-btn slot-blocker-width" data-toggle="dropdown" id="service-lbl" style="height: 38px; cursor: pointer;">

							    		<i id="ok-icon" class="glyphicon" style="color: #1b9bd7"></i>&nbsp;&nbsp;
							    		<span class="service-selection" id="">Select a service</span>
							    		<div class="ext-right"><i class="glyphicon glyphicon-chevron-down"></i></div>

							    	</label>
						    		<ul class="dropdown-menu ul-width" id="service-list" style="width: 392px; max-height: 258px; overflow-y: auto; overflow-x: hidden;">

								        <li id="" style="padding: 5px 15px 5px 15px; color: #555555;">
											<span class="service" id="0">Slot Blocker</span>
											<span class="pull-right">Custom</span>
										</li>

								         <li class="divider"></li>

							        	<li id="" style="padding: 5px 15px 5px 15px; color: #555555;">
											<span class="service">*name</span>
											<span class="pull-right">*duration *duration-format</span>
										</li>

									</ul>
							    </div>

							    <div id="slot-blocker-service">
							    	<label class="col-sm-1 details-label">Duration</label>
							    	<input type="text" id="block-time-Duration" class="dropdown-btn col-sm-1" style="height: 15px;">
							    	<div class="col-sm-1" style="padding: 0px; padding-left: 9px;">
							    		<label class="dropdown-btn" data-toggle="dropdown" id="" style="height: 38px; cursor: pointer; width: 33px;">
							    			<span class="blocker-time-format" id="min">Mins</span>
							    		</label>
							    		<ul class="dropdown-menu" id="select-blocker-time-Format" style="min-width: 85px;">
									        <li><a href="#" id="mins">Mins</a></li>
									        <li><a href="#" id="hours">Hours</a></li>
										</ul>
							    	</div>
							    </div>

						    </div>
						    <br><br><br>

						    <!-- Cost & Time duration -->

						    <div id="Cost-Time-duration">
							    <div class="form-group">
								    <label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">Price</label>

								    <div class="col-sm-2" >
								     	<input type="text" id="service-price" value="5" class="dropdown-btn" style="height: 35px; width: 90px;">
								    </div>

								    <label class="col-sm-1 details-label">Duration</label>
								    <input type="text" id="service-time-Duration" class="dropdown-btn col-sm-1" style="height: 15px; width: 55px;">

								    <div class="col-sm-2" style="padding: 0px; padding-left: 9px;">
								    	<label class="dropdown-btn" data-toggle="dropdown" id="" style="height: 38px; cursor: pointer;">
								    		<span class="time-format" id="min">Mins</span>
								    		<div class="ext-right"><i class="glyphicon glyphicon-chevron-down"></i></div>
								    	</label>
							    		<ul class="dropdown-menu" id="select-time-Format">
									        <li><a href="#" id="mins">Mins</a></li>
									        <li><a href="#" id="hours">Hours</a></li>
										</ul>
								    </div>
							    </div>
							    <br><br><br>
						    </div>

						    <!-- Date & Time pickers -->

						    <div class="form-group">
							    <label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">Day</label>

								<div class="col-sm-4">
									<input type="text" id="appointment-date" class="dropdown-btn" style="height: 30px; width: 180px; cursor: pointer;" value="Friday, September 02, 2016">
								</div>
							    <label class="col-sm-1 details-label" style="padding-right: 0px;">Time</label>
							    <div class="col-sm-2" style="padding: 0px; padding-left: 5px;">
									<div class="right-inner-addon">
									    <i class="glyphicon glyphicon-chevron-down" style="padding: 10px 0px;"></i>
									    <input type="search" class="dropdown-btn" id="appointment-time" style="width: 65px; height: 35px; cursor: pointer;" />
									</div>
							    </div>
						    </div>
						    <br><br><br>

						    <!-- Notes -->

						    <div class="form-group">
						    	<label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">Notes</label>

							    <div class="col-sm-8">
							    	<input type="text" id="notes" class="dropdown-btn input-width" placeholder="Notes / Instructions" style="height: 27px;">
							    </div>
						    </div>

						    <div class="form-group" style="display: none" id="error_div1">
							    <label class="details-label col-sm-2"></label>
							    <div class="col-sm-8 alert alert-danger" id="error1" style="margin: 0px; margin-top: 12px;">
							    </div>
						    </div>

						    <div style="clear: both;">
						    </div>
						    <br>
						    <br>

						    <div class="form-group">
							    <label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">&nbsp;</label>

							    <div class="col-sm-8">
							    	<button type="button" class="btn btn-update font-type-Montserrat col-sm-3" id="continue" onclick="continueEdit()">Continue</button>
							    	<button type="button" class=" hide btn btn-update font-type-Montserrat col-sm-3" id="blocker">Save Blocker</button>
							    </div>
						    </div>

						    <br>


			            </div>
			        </div>
			    </div>


			    <!-- Patient tab contents -->

			    <div id="patient" class="tab-pane fade">
			    
				    <div class="panel panel-default" style="margin: 0px;">
				    	<div class="panel-body" style="text-align: center; padding-bottom: 30px;">
				    	<br>

				    	<!-- search text box-->

				    	<!-- <div id="search-panel" >
						    <div class="col-xs-10" style="padding-left: 30px">
							    <div class="right-inner-addon">
							        <i class="glyphicon glyphicon-search"></i>
							        <input type="search" class="dropdown-btn" id="search-customer" placeholder="Search By NRIC or Phone Number" />
							    </div>
							</div>
							<br><br><br>
							<hr><br>

							<div class="col-xs-10" style="padding-left: 205px;"><button type="button" id="add-new-customer" class="btn btn-update font-type-Montserrat" style="height: 26px;"><i class="glyphicon glyphicon-plus" style="font-size: 11px;"></i> New Patient</button>
							</div><br><br>
						</div> -->

						<div id="new-customer">
							<!-- <div> -->

							<!-- <br> -->
							<!-- <div class="ext-right" style="padding-right: 15px;"><span id="close"><i class="glyphicon glyphicon-remove" style="color: #999999;"></i></span>
							</div> -->
							<!-- <br> -->

							<!-- Customer Name -->
							<div class="form-group">
							    <label class="details-label col-sm-2"><span><img src="{{ URL::asset('assets/images/ico_Profile.svg') }}" alt="" width="50" height="50"></span></label>

							    <div class="col-sm-8">
							    	<input type="text" id="customer-name" class="dropdown-btn input-width" placeholder="Name" style="height: 27px;">
							    </div>
						    </div><br><br><br>

							<!-- NRIC -->
						    <div class="form-group">
						    	<label class="details-label col-sm-2">&nbsp;</label>

							    <div class="col-sm-8">
							    	<div class="right-inner-addon">
							        	<i class="glyphicon glyphicon-ok" id="nric-valid-icon"></i>
											<input type="text" id="customer-nric" class="dropdown-btn input-width" placeholder="NRIC / Fin / Passport" style="height: 27px; width: 335px;">
							    	</div>
							    </div>
						    </div><br><br><br>

							<!-- Telephone -->
							<div class="form-group">
							    <label class="details-label col-sm-2">&nbsp;</label>

							    <div class="col-md-8">
										<div id="mobile-dropdown" class="btn-group" style="border: 1px solid #d9d9d9; border-radius: 5px; display: block; width: 375px;">
										<button type="button" id="phone-code" class="btn dropdown-toggle" data-toggle="dropdown" style="height: 28px; font-size: 12px; color: #686868; background: #F4F4F4; border-right: 1px solid #d9d9d9; width: 35px; text-align: left;">+65</button>
										<input type="text" id="phone-no" class="dropdown-btn " value="" placeholder="Mobile Number" style="height: 28px; width: 293px; font-size: 12px; border: 0px;">
										<ul class="dropdown-menu" id="phone-code-list" style="width: 375px; max-height: 150px; overflow-y: auto; overflow-x: hidden;">

										</ul>
										</div>
								</div>
						    </div><br><br><br>

							<!-- Email -->

							<div class="form-group">
							    <label class="details-label col-sm-2">&nbsp;</label>

							    <div class="col-sm-8">
							    	<input type="text" id="customer-email" class="dropdown-btn input-width" placeholder="Email" style="height: 27px;">
							    </div>
						    </div><br><br><br>

							<!-- Address -->

							<div class="form-group">
							    <label class="details-label col-sm-2">&nbsp;</label>

							    <div class="col-sm-8">
							    	<input type="text" id="customer-address" class="dropdown-btn input-width" placeholder="Address" style="height: 27px;">
							    </div>
						    </div><br><br><br>

							<!-- City / State / Zip -->

							<div class="form-group">
							    <label class="details-label col-sm-2">&nbsp;</label>

							    <div class="col-sm-3">
							    	<input type="text" id="city-name" class="dropdown-btn" placeholder="City" style="height: 32px; width: 130px;">
							    </div>
							    <div class="col-sm-2">
							    	<input type="text" id="state-name" class="dropdown-btn" placeholder="State" style="height: 33px; width: 90px;">
							    </div>
							    <div class="col-sm-2">
							    	<input type="text" id="zip-code" class="dropdown-btn" placeholder="Zip" style="height: 33px; width: 60px;">
							    </div>
						    </div><br><br>
						    <div style="clear: both"></div>

						    <div class="form-group" style="display: none" id="error_div2">
							    <label class="details-label col-sm-2"></label>
							    <div class="col-sm-8 alert alert-danger text-left" id="error2" style="margin-top: 12px">
									
							    </div>
						    </div>

						    <div style="clear: both"></div>
						    </div><br><br>

						    <div class="form-group">
							    <label class="details-label col-sm-2">&nbsp;</label>
							    <div class="col-sm-8"><button type="button" id="save-appointment" class="btn btn-update font-type-Montserrat ext-left">Save Appointment</button></div>
							    <div class="col-sm-8"><button type="button" id="update-appointment" class="btn btn-update font-type-Montserrat ext-left hide" onclick="updateAppointment()">Update Appointment</button></div>
						    </div>
						    <br>

						</div>

				    </div>
			    </div>
			</div>


	  	</div>

    </div>

  </div>
</div>
<!-- </div> -->

<script type="text/javascript">

	$('.click-tooltip').popover({
		trigger : "hover"
	});

	$( "#summary-datepicker-button" ).click(function(){
    	$( ".summary-datepicker-wrapper" ).toggle();
    	// setTimeout(function() {
    	// 	$( ".summary-datepicker-wrapper" ).addClass('summary-datepicker-wrapper-animation');
    	// }, 10);
    });

    $( "#close-summary-datepicker" ).click(function(){
    	$( ".summary-datepicker-wrapper" ).toggle();
    	// setTimeout(function() {
    	// 	$( ".summary-datepicker-wrapper" ).removeClass('summary-datepicker-wrapper-animation');
    	// }, 10);
    });

    $( "#appointment-date" ).datepicker({

	    dateFormat : "DD, MM dd" ,
	    minDate : 0,

	 });
    
    $('#appointment-time').timepicker({

      'timeFormat' : 'h:i A',
    });

    function viewAppointment(id) {
    	$.ajax({
  			 url: window.base_url + 'clinic/view/appointment/' + id,
  			 type: 'GET',
  		})
  		.done(function(data){
  			// console.log(data);
  			$( ".detailpopup #content-section" ).html(data);

  		});
    }	

    function editUserAppointmentment(id, clinicID, DoctorID) {
    	// console.log(id, clinicID);
    	popupReset();
	  $('#editAppointmentModal #booking .panel-body #slot-blocker-service').removeClass('show');
	  $('#editAppointmentModal #booking .panel-body #slot-blocker-service').addClass('hide');
	  $('#editAppointmentModal #booking .panel-body #service-lbl').removeClass('slot-blocker-width');
	  $('#editAppointmentModal #booking .panel-body #service-lbl').addClass('input-width');
	  $('#editAppointmentModal #booking .panel-body #Cost-Time-duration').removeClass('hide');
	  $('#editAppointmentModal #booking .panel-body #Cost-Time-duration').addClass('show');

	  $('#editAppointmentModal #patient .panel-body #new-customer').removeClass('hide');
	  $('#editAppointmentModal #patient .panel-body #new-customer').addClass('show');
	  $('#editAppointmentModal #patient .panel-body #search-panel').removeClass('show');
	  $('#editAppointmentModal #patient .panel-body #search-panel').addClass('hide');

	  $('#save-appointment').addClass('hide');
	  $('#update-appointment').removeClass('hide');

	  $('#tabs .enabledTab').addClass('active');
	  $('#booking').addClass('active');
	  $('#tabs .disabledTab').removeClass('active');
	  $('#patient').removeClass('in active');


	  var id = id;
	  $('.service-selection').attr('id', $('#h-procedure-id').val() );
      $('#editAppointmentModal').attr('data-id', id);
	  $('.doctor-selection').attr('id', $('#h-doctor-id').val());
	  $('.doctor-selection').text($('#h-doctor-name').val());
	  $('.service-selection').text( $('#appointment-service-detail').val() );
	  $('#service-price').val( $('#h-procedure-price').val() );
	  $('#service-time-Duration').val( $('#h-procedure-duration').val() );
	  $('#appointment-date').val( $('#appointment-date-lbl').val() );
	  $('#appointment-time').val( $('#h-app-time').val() );
	  $('#user_id').val($('#userid').val());
	  var phone = $('#appointment-phone-detail').val();
	  var code = $('#h-cus-phone-code').val();
	  var length = $('#h-cus-phone-code').val().length;

	  var PhoneNo = phone.substring(length);

	  $('#customer-name').val($('#appointment-customer-detail').val());
	  $('#customer-nric').val($('#appointment-nric-detail').val());
	  $('#phone-code').text(code);
	  $('#phone-no').val(PhoneNo);
	  $('#customer-email').val($('#appointment-email-detail').val());
	  $('#customer-address').val($('#h-cus-address').val());
	  $('#city-name').val($('#h-cus-city').val())
	  $('#state-name').val($('#h-cus-state').val());
	  $('#zip-code').val($('#h-cus-zip').val())

	  $('#notes').val($('#appointment-note-detail').text())

	  $('.time-format').html('Mins');
	  $('.time-format').attr('id', 'mins');

	  $('#editAppointmentModal #booking #ok-icon').addClass('glyphicon-ok');
	  $('#editAppointmentModal #booking #ok-icon').removeClass('glyphicon-arrow-right');
	  $('#editAppointmentModal #booking #ok-icon').removeClass('arrow-color');
	  getPrcedureDetails(clinicID);
	  getDoctorProcedure(clinicID, DoctorID);
	  $('#editAppointmentModal').modal('show');
	}


	function getDoctorProcedure(clinicID, DoctorID) {

	    var docID = DoctorID;
	    var clinicID = clinicID;

	    $.ajax({
	    url: base_url+'calendar/getDoctorProcedure',
	    type: 'POST',
	    data: {docID: docID, clinicID:clinicID },
	    })

	    .done(function(data) {
	      $('#service-list').html(data);
	      $('.slot-block').html(data);

	    });
	}

	function getPrcedureDetails(clinicID) {

	  var clinicID = clinicID
	  var procedureID = $('.service-selection').attr('id');

	  $.ajax({
	      url: base_url+'calendar/load-procedure-details',
	      type: 'POST',
	      dataType: 'json',
	      data: {procedureID: procedureID, clinicID:clinicID },
	    })
	    .done(function(data) {
	      // alert(data.Price);

	      $("#service-time-Duration").val(data.Duration);
	      $("#service-price").val(data.Price);

	    });

	}

	function popupReset() {

	  $('.service-selection').attr('id', '');
	  $('.service-selection').html('Select a service');
	  $('#block-time-Duration').val('');
	  $('#service-time-Duration').val('');
	  $('#Cost-Time-duration').removeClass('show').addClass('hide');
	  $('#notes').val('');

	  $('#slot-blocker-service').removeClass('show').addClass('hide');
	  $('#service-lbl').removeClass('slot-blocker-width').addClass('input-width');

	  $('#customer-name').val('');
	  $('#customer-nric').val('');
	  $('#phone-code').val('');
	  $('#phone-no').val('');
	  $('#customer-email').val('');
	  $('#customer-address').val('');
	  $('#city-name').val('');
	  $('#state-name').val('');
	  $('#zip-code').val('');
	  $('#search-customer').val('');

	  $('#tabs .enabledTab').addClass('active');
	  $('#booking').addClass('active');
	  $('#tabs .disabledTab').removeClass('active');
	  $('#patient').removeClass('in active');

	  $('#search-panel').addClass('show')
	  $('#new-customer').removeClass('show').addClass('hide');
	  $('#blocker').removeClass('show').addClass('hide');
	  $('#continue').removeClass('hide').addClass('show');

	  $('#save-appointment').removeClass('hide');
	  $('#update-appointment').addClass('hide');

	  $('#editAppointmentModal #booking #ok-icon').removeClass('glyphicon-ok');
	  $('#editAppointmentModal #booking #ok-icon').addClass('glyphicon-arrow-right');
	  $('#editAppointmentModal #booking #ok-icon').addClass('arrow-color');

	  $('#editAppointmentModal #patient-tab').removeClass('hide').addClass('show');

	  $('#error_div2').css('display', 'none');
	  $('#error_div1').css('display', 'none');
	}


	// function popupValidation(){

	  function continueEdit() {

	   var doctorID        = $('.doctor-selection').attr('id');
	   var procedureID     = $('.service-selection').attr('id');
	   var duration        = (procedureID==0)? $('#block-time-Duration').val() : $('#service-time-Duration').val();
	   var date            = $('#appointment-date').val();
	   var stime            = $('#appointment-time').val();
	   var price           = $('#service-price').val();
	   var remarks         = $('#notes').val();
	   var default_time    = $('#h-datetime').val();

	   var name = $('#customer-name').val();
	   var nric = $('#customer-nric').val();
	   var code = $('#phone-code').val();
	   var phone = $('#phone-no').val();
	   var email = $('#customer-email').val();
	   var address = $('#customer-address').val();
	   var city = $('#city-name').val();
	   var statate = $('#state-name').val();
	   var zip = $('#zip-code').val();

	   var er_count = 0;
	   var error = '';


	   if (procedureID=='') {error += 'Please select a procedure!<br>'; er_count++;}
	   if (duration=='') {error += 'Please insert a duration!<br>'; er_count++;}
	   if (price=='' && procedureID!=0) {error += 'Please insert a price!<br>'; er_count++;}
	   if (date=='') {error += 'Please select a date!<br>'; er_count++;}
	   if (stime=='') {error += 'Please select a time!<br>'; er_count++;}

	   $('#error_div1').css('display', 'block');
	   $('#error1').html(error);
	   if (er_count==0) {
	      $('#error_div1').css('display', 'none');
	      $('#tabs .enabledTab').removeClass('active');
	      $('#booking').removeClass('active');
	      $('#tabs .disabledTab').addClass('active');
	      $('#patient').addClass('in active');
	    }

	  }


	  $(document).on('keydown', '#phone-no', function(c) {

	        if (!(c.keyCode>=96 && c.keyCode<=105) && !(c.keyCode>=48 && c.keyCode<=57) && c.keyCode!=8 && c.keyCode!=9) {
	            return false;
	        }

	    });

	  $(document).on('keyup', '#customer-nric', function(c) {

	        var NRIC = $('#customer-nric').val();
	        var validate = /^[STFG]\d{7}[A-Z]$/igm;

	        if (validate.test(NRIC)) {

	          $('#nric-valid-icon').addClass('glyphicon-ok');

	        }else {

	          $('#nric-valid-icon').removeClass('glyphicon-ok');

	        }

	  });

	// }

	$("#service-list").on("click","li", function(){

	  val = $('.service',this).text();
	  id = $('.service',this).attr('id');

	    $('.service-selection').html(val);
	    $('.service-selection').attr('id', id);

	    $('.blocker-time-format').html('Mins');
	    $('.blocker-time-format').attr('id', 'mins');
	    $('.time-format').html('Mins');
	    $('.time-format').attr('id', 'mins');

	    if (id == '0') {

	          $('#editAppointmentModal #patient-tab').removeClass('show').addClass('hide');

	          $('#editAppointmentModal #editAppointmentModalLabel').text("Blocker");

	          $('#editAppointmentModal #booking .panel-body #service-lbl').removeClass('input-width').addClass('slot-blocker-width');
	          $('#editAppointmentModal #booking .panel-body #slot-blocker-service').removeClass('hide').addClass('show');
	          $('#editAppointmentModal #booking .panel-body #Cost-Time-duration').removeClass('show').addClass('hide');

	          $('#blocker').removeClass('hide').addClass('show');
	          $('#continue').removeClass('show').addClass('hide');
	          $('#block-time-Duration').val($('#h-duration').val());

	    }

	    else{
	          $('#editAppointmentModal #patient-tab').removeClass('hide').addClass('show');

	          $('#editAppointmentModal #editAppointmentModalLabel').text("Appointment");

	          $('#editAppointmentModal #booking .panel-body #slot-blocker-service').removeClass('show').addClass('hide');
	          $('#editAppointmentModal #booking .panel-body #service-lbl').removeClass('slot-blocker-width').addClass('input-width');
	          $('#editAppointmentModal #booking .panel-body #Cost-Time-duration').removeClass('hide').addClass('show');

	          $('#blocker').removeClass('show').addClass('hide');
	          $('#continue').removeClass('hide').addClass('show');

	    }

	    $('#editAppointmentModal #booking #ok-icon').addClass('glyphicon-ok');
	    $('#editAppointmentModal #booking #ok-icon').removeClass('glyphicon-arrow-right');
	    $('#editAppointmentModal #booking #ok-icon').removeClass('arrow-color');

	  //alert(id);
	  getPrcedureDetails();

	  });

	$("#appointment-doctor-list li a").click(function(){
	  	val = $(this).text();
	  	id = $(this).attr('id');

	    $('.doctor-selection').html(val);
	    $('.doctor-selection').attr('id', id);
	    popupReset();
	    getDoctorProcedure();

	  });

function updateAppointment() {

   var id = $('#editAppointmentModal').attr('data-id');
   var doctorID        = $('.doctor-selection').attr('id');
   var procedureID     = $('.service-selection').attr('id');
   var duration        = (procedureID==0)? $('#block-time-Duration').val() : $('#service-time-Duration').val();
   var time_format     = $('.time-format').attr('id');
   var date            = $('#appointment-date').val();
   var stime           = $('#appointment-time').val();
   var price           = $('#service-price').val();
   var remarks         = $('#notes').val();

   var name = $('#customer-name').val();
   var nric = $('#customer-nric').val();
   var code = $('#phone-code').text();
   var phone = $('#phone-no').val();
   var email = $('#customer-email').val();
   var address = $('#customer-address').val();
   var city = $('#city-name').val();
   var statate = $('#state-name').val();
   var zip = $('#zip-code').val();
   var user_id = $('#user_id').val();
   // ................... validate user ......................

   var er_count = 0;
   var error = '';
   var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;

   if (name=='') { error += 'Please insert name!<br>'; er_count++; }
   if (nric=='') { error += 'Please insert nric/fin/passport!<br>'; er_count++; }
   if (code=='') { error += 'Please insert code!<br>'; er_count++; }
   // if (phone=='') { error += 'Please insert phone number!<br>'; er_count++; }
   // if (email=='') { error += 'Please insert email!<br>'; er_count++; }
   if (email == '' || !re.test(email)) { error += 'Please insert valid email!<br>'; er_count++; }

   $('#error_div2').css('display', 'block');
   $('#error2').html(error);
   if (er_count==0) {$('#error_div2').css('display', 'none');} else { return false;}

   if (time_format == 'hours' ){

      duration = Math.floor( duration * 60);

    }

   $('#update-appointment').text('Processing ...');

   $.ajax({
      url: base_url+'calendar/updateAppointment',
      type: 'POST',
      // dataType: 'json',
      data: {
        appointment_id:id, doctorid: doctorID, procedureid:procedureID, duration:duration, bookdate:date, starttime:stime, price:price, remarks:remarks, name:name, nric:nric, code:code, phone:phone, email:email, address:address, city:city, statate:statate, zip:zip, user_id: user_id },
    })
    .done(function(data) {
      if (data==0) {
        alert('Double booking not allowed!');
        $('#update-appointment').text('update Appointment');
      }else if(data==2){
        alert('Sorry! Clinic is closed.');
        $('#update-appointment').text('update Appointment');
      } else {
        $('#editAppointmentModal').modal('hide');
        $('#update-appointment').text('update Appointment');
      	getAppointmentLists( );
      }

    });


  }


  function getAppointmentLists( ) {
	  	jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
	  	$.ajax({
  			 url: window.base_url + 'clinic/appointments/list',
  			 type: 'GET',
  		})
  		.done(function(data){
  			$('#schedule-view').html(data);
  			jQuery.unblockUI();
  			$('.info-detail').popover({
				html:true,
				container:'body',
				template : '<div class="detailpopup popover" role="tooltip">' +
								'<div class="arrow"></div>' +
								'<h3 class="popover-title"></h3>' +
								'<div class="popover-content">' +
									
								'</div>'+
							'</div>',
				content : '<div class="header">' +
								'<h4>Appointment Details</h4>' +
							'</div>' +
							'<div id="content-section">' +

								'<div class="body">' +
									'<h5>--</h5>' +
									'<div class="white-space-20" ></div>' +

									'<p><label>Staff</label> <span>--</span></p>' +
									'<p><label>Services</label> <span>--</span></p>' +
									'<div class="white-space-20" ></div>' +

									'<p><label>Cost $</label> <span>--</span></p>' +
									'<p><label>Customer</label> <span>--</span></p>' +
									'<div class="white-space-20" ></div>' +

									'<p><label>Booked From</label> <span>--</span></p>' +
								'</div>' +
								'<div class="footer">' +
									'<h5><a href="javascript:void(0)" style="color:#76C9EC">Edit Appointment >></a> <a href="javascript:void(0)" class="pull-right">Delete</span></a>' +
								'</div>' +
							'</div>'

			});
  		});
	  }

	  // pin
    function test( ) {
    	$.confirm({
		    title: 'Confirm!',
		    content: 'Simple confirm!',
		    buttons: {
		        confirm: function () {
		            $.alert('Confirmed!');
		        },
		        cancel: function () {
		            $.alert('Canceled!');
		        },
		        somethingElse: {
		            text: 'Something else',
		            btnClass: 'btn-primary',
		            keys: ['enter', 'shift'],
		            action: function(){
		                $.alert('Something else?');
		            }
		        }
		    }
		});
	    // $.confirm({
	    //     title: 'Please input the Clinic PIN',
	    //     content: 'url:' + window.location.origin + '/confirms/pin.html',
	    //     buttons: {
		   //      confirm: function () {
		   //          $.alert('Confirmed!');
		   //      },
		   //      cancel: function () {
		   //          $.alert('Canceled!');
		   //      }
		   //  }
	    //     buttons: {
	    //         sayMyName: {
	    //             text: 'Say my name',
	    //             btnClass: 'btn-warning',
	    //             columnClass: 'medium',
	    //             action: function () {
	    //                 var input = this.$content.find('input#input-name');
	    //                 var errorText = this.$content.find('.text-danger');
	    //                 if (input.val() == '') {
	    //                     errorText.html('Please don\'t keep the name field empty!').slideDown(200);
	    //                     return false;
	    //                 } else {
	    //                     $.alert('Hello ' + input.val() + ', i hope you have a great day!');
	    //                 }
	    //             }
	    //         },
	    //         later: function () {
	    //             // do nothing.
	    //         }
	    //     }
	    // });
	 //    $.confirm({
		//     title: 'Confirm!',
		//     content: 'Simple confirm!',
		//     buttons: {
		//         confirm: function () {
		//             $.alert('Confirmed!');
		//         },
		//         cancel: function () {
		//             $.alert('Canceled!');
		//         },
		//         somethingElse: {
		//             text: 'Something else',
		//             btnClass: 'btn-primary',
		//             keys: ['enter', 'shift'],
		//             action: function(){
		//                 $.alert('Something else?');
		//             }
		//         }
		//     }
		// });
    }
</script>

@include('common.footer')

