@include('common.home_header')

{{ HTML::script('assets/dashboard/clinic_config.js') }}

<div id="config_alert_box">
    message goes here
</div>

<input type="hidden" id="clinicID" value="">
<div id="calender_header">
		<div class="header-list">
			<ul class="nav navbar-nav">
			<li>
				<div class="dropdown" >
				<span><img src="{{ URL::asset('assets/images/ico_Profile.svg') }}" alt=""></span>
		          <span  class="dropdown-toggle doctor-selection" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id=""></span><span class="caret"></span>
		          <ul class="dropdown-menu" id="doctor-list">
		        
		          </ul>
		        </div>
		    </li>
		    <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
		    <li>
		        <div class="dropdown" style="margin-top: 2px;">
		        <span>&nbsp;</span>
		          <span  class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="calender-selection">Weekly</span><span class="caret"></span>
		          <ul class="dropdown-menu " id="calendar-view-option">
		            <li><a href="#" id="w">Weekly</a></li>
		            <li><a href="#" id="d">Daily</a></li>
		            <li><a href="#" id="m">Monthly</a></li>
		          </ul>
		        </div>
		    </li>    
		    </ul> 
		</div>

		<div class="header-dates"></button><input type="hidden" name="" id="dp">
  			<button type="button" class="btn btn-default" id="btn-today">Today</button>
			<div id="datepicker-button" class="btn-group" role="group" aria-label="...">
  			<button type="button" class="btn btn-default" id="btn-left"><img src="{{ URL::asset('assets/images/ico_left arrow.svg') }}" alt="">
  			<button type="button" class="btn btn-default" id="btn-title"></button>
  			<button type="button" class="btn btn-default" id="btn-right"><img src="{{ URL::asset('assets/images/ico_right arrow.svg') }}" alt=""></button>
		</div>
		</div>

		<!-- <div class="header_tool pull-right">
			<a href="" title="datepicker-button"><img src="{{ URL::asset('assets/images/ico_add.svg') }}" alt="" width="20px" height="20px"></a>
			<a href="" title=""><img src="{{ URL::asset('assets/images/ico_Notification.svg') }}" alt=""></a>
			<a href="" title=""><img src="{{ URL::asset('assets/images/ico_Settings.svg') }}" alt=""></a>
		</div> -->

</div>



	<div id="calendar">
		
	</div>

	<!-- ......................... dialog box for Clinic first time login configuration window ............................. -->


<div id="clinic-config-dialog"  style="padding: 0px; display: none; font-family:'Open Sans', sans-serif;">

<div id="setup-uncompleted-line">
	<div id="setup-completed-line" style="width: 0px;"></div>
</div>

  <ul class="nav nav-tabs setup" style="background: #104159;">

    <li id="step-1" class="active disabledTab">
  	<label class="lbl-setup-complete"><b>Welcome</b></label>
	<a data-toggle="tab" href="#setupHome">
		<span id="lbl-step-1" class="step-no">1</span>
		<span id="lbl-step-1-ok" class="glyphicon glyphicon-ok step-no" style="background: #2AA4D8; position: absolute; display: none;"></span>
	</a>
	</li>
	<li id="step-2" class="disabledTab"> <!-- class="disabledTab" -->
  	<label class="lbl-setup-complete"><b>Hours</b></label>
		<a data-toggle="tab" href="#setupHours">
		<span id="lbl-step-2" class="step-no">2</span>
		<span id="lbl-step-2-ok" class="glyphicon glyphicon-ok step-no" style="background: #2AA4D8; position: absolute; display: none;"></span>
	</a>
	</li>
	<li id="step-3" class="disabledTab">
  	<label class="lbl-setup-complete"><b>Doctors</b></label>
		<a data-toggle="tab" href="#setupDoctor">
		<span id="lbl-step-3" class="step-no">3</span>
		<span id="lbl-step-3-ok" class="glyphicon glyphicon-ok step-no" style="background: #2AA4D8; position: absolute; display: none;"></span>
	</a>
	</li>
	<li id="step-4" class="disabledTab">
  	<label class="lbl-setup-complete"><b>Service</b></label>
		<a data-toggle="tab" href="#setupService">
		<span id="lbl-step-4" class="step-no">4</span>
		<span id="lbl-step-4-ok" class="glyphicon glyphicon-ok step-no" style="background: #2AA4D8; position: absolute; display: none;"></span>
	</a>
	</li>
	<li id="step-5" class="disabledTab">
  	<label class="lbl-setup-complete"><b>DONE!</b></label>
		<a data-toggle="tab" href="#setupDone">
		<span id="lbl-step-5" class="step-no">5</span>
		<span id="lbl-step-5-ok" class="glyphicon glyphicon-ok step-no" style="background: #2AA4D8; position: absolute; display: none;"></span>
	</a>
	</li>
  </ul>

  <div class="tab-content config-tab-wapper" style="background: #BFEDF9; padding: 30px 30px 30px 30px;">

<!-- Welcome tab contents -->

    <div id="setupHome" class="tab-pane fade in active">
      
    <div>
      	<h3 style="float: left;">Welcome to Medicloud.</h3>
      	<span style="float: right; padding-top: 22px;">
      		<button id="welcome-next" class="config-nxt-btn" style="font-size: medium;">Next <i class="glyphicon glyphicon-chevron-right" style="font-size: small;"></i></button>
      	</span>
    </div><br><br>
    <span style="font-size: 12px; color: #777;">We'll get you setup in no time</span>

    <div class="panel panel-default" style="margin-top: 15px;">
    	<div class="panel-body">
    	<span style="font-size: 12px; color: blue;">This will only take a moment.</span>

    	<div class="row col-md-12"><br>
			<div class="col-md-2" style="clear: both; padding-right: 25px;">
				<label class="con-detail-lbl">Clinic Name</label>
			</div>
			<div class="col-md-8" style="padding: 0px; padding-left: 30px;">
				<input type="text" id="con-clinic-name" class="dropdown-btn " value="{{ $clinic_data->Name }}" placeholder="Clinic Name" style="height: 25px; width: 260px; font-size: 12px;">
			</div>
		</div>

		<div class="row col-md-12" style="padding-top: 15px;">
			<div class="col-md-2" style="clear: both; padding-right: 25px;">
				<label class="con-detail-lbl">Phone No</label>
			</div>
			<div class="col-md-8" style="padding: 0px; padding-left: 30px;">
  				<div id="code-dropdown" class="btn-group" style="border: 1px solid #d9d9d9; border-radius: 5px; display: block; width: 280px;">
    				<button type="button" id="con-mobile-code" class="btn dropdown-toggle" data-toggle="dropdown" style="height: 25px; font-size: 12px; color: #686868; background: #F4F4F4; border-right: 1px solid #d9d9d9; width: 35px; text-align: left;">{{ $clinic_data->PhoneCode }}</button>
    				<input type="text" id="con-mobile" class="dropdown-btn " value="{{ $clinic_data->Phone }}" placeholder="Main Phone" style="height: 28px; width: 197px; font-size: 12px; border: 0px;">
    				<ul class="dropdown-menu" id="config-mobile-code-list" style="width: 280px; position: static; max-height: 80px; overflow-y: auto; overflow-x: hidden;">

					</ul>
  				</div>
			</div>
		</div>

    	</div>
    </div>

    </div>

<!-- Hours tab contents -->

    <div id="setupHours" class="tab-pane fade">

    <div>
      	<h3 style="float: left;">Set Yours Business Hours</h3>
      	<span style="float: right; padding-top: 22px;">
      		<button id="hour-back" class="config-back-btn" style="font-size: medium;"><i class="glyphicon glyphicon-chevron-left" style="font-size: small;"></i></button>
      		<button id="hour-next" class="config-nxt-btn" style="font-size: medium;">Next <i class="glyphicon glyphicon-chevron-right" style="font-size: small;"></i></button>
      	</span>
    </div><br><br>
    <span style="font-size: 16px; color: #777; font-size: 12px;">Let your customers know when you're open</span>


    <div class="panel panel-default" style="margin-top: 15px;">
    	<div class="panel-body">

    	<div style="text-align: center;">
    		<span style="font-size: 12px;; color: #777;">Your time Zone is set to <u style="cursor: pointer;">SINGAPORE (UTC+08:00)</u>.</span>
    	</div>

    	<div id="clinic-time-panel">
    		
    	</div>


    		</div>
    	</div>

    </div>

<!-- Doctor tab contents -->

    <div id="setupDoctor" class="tab-pane fade">

    <div>
      	<h3 style="float: left;">Add Doctors</h3>
      	<span style="float: right; padding-top: 22px;">
      		<button id="doctor-back" class="config-back-btn" style="font-size: medium;"><i class="glyphicon glyphicon-chevron-left" style="font-size: small;"></i></button>
      		<button id="doctor-next" class="config-nxt-btn" style="font-size: medium;">Next <i class="glyphicon glyphicon-chevron-right" style="font-size: small;"></i></button>
      	</span>
    </div><br><br>
    <span style="font-size: 12px; color: #777;">Dont worry - you can always edit these later</span>

    <div class="panel panel-default" style="margin-top: 15px;">
    	<div class="panel-body" style="padding-left: 10px;">

    	<div id="clinic-doctors-panel">

    	</div>

    	<hr style="border: 1px solid rgb(216, 216, 216) !important; border-color: rgb(216, 216, 216); -webkit-box-shadow: 0 0 5px rgb(216, 216, 216); -moz-box-shadow: 0 0 5px red; box-shadow: 0 0 5px rgb(216, 216, 216);">

		<div class="row col-md-12">
			<span class="col-md-1" style="padding-bottom: 5px; padding: 0px;">
    			<img alt="" src="{{ URL::asset('assets/images/ico_Profile.svg') }}" width="40" height="40">
    		</span>
			<div class="col-md-4">
				<input type="text" id="con-doctor-name" class="dropdown-btn " value="" placeholder="Name" style="height: 30px; width: 140px; font-size: 12px;">
			</div>
			<div class="col-md-4" style="padding-left: 0px;">
				<input type="text" id="con-doctor-email" class="dropdown-btn " value="" placeholder="Email" style="height: 30px; width: 150px; font-size: 12px;">
			</div>
			<button id="config-doc-add-btn" class="config-doc-add-btn" style="font-size: 15px; width: 45px;">Add</button>
		</div>

    	</div>
    </div>

    </div>

<!-- Services tab contents -->

    <div id="setupService" class="tab-pane fade">

    <div>
      	<h3 style="float: left;">Add the Services You Offer</h3>
      	<span style="float: right; padding-top: 22px;">
      		<button id="service-back" class="config-back-btn" style="font-size: medium;"><i class="glyphicon glyphicon-chevron-left" style="font-size: small;"></i></button>
      		<button id="service-next" class="config-nxt-btn" style="font-size: medium;">Next <i class="glyphicon glyphicon-chevron-right" style="font-size: small;"></i></button>
      	</span>
    </div><br><br>
    <span style="font-size: 12px; color: #777;">Dont worry - you'll be able to edit these later</span>

    <div class="panel panel-default" style="margin-top: 15px;">
    	<div class="panel-body">
    		
    	<div class="row col-md-12">
    		<span class="col-md-4" style="padding-bottom: 5px; padding-left: 0px;">
    			<label class="con-detail-lbl" style="color: #999999;">Service Name</label>
    		</span>
			<div class="col-md-2">
				<label class="con-detail-lbl" style="color: #999999;">Time</label>
			</div>
			<div class="col-md-1">
				<label class="con-detail-lbl" style="color: #999999;">Price</label>
			</div>
			<div class="col-md-1">
				<label class="con-detail-lbl" style="color: #999999;">Doctors</label>
			</div>
			<a id="doc-tiptool" href="#" data-toggle="tooltip" title="Hooray!" style="display: none;"></a>
		</div>

		<div id="clinic-service-panel">

    	</div>

		
    	</div>
    </div>

    </div>

<!-- DONE tab contents -->

    <div id="setupDone" class="tab-pane fade">

    <h3>All done! Time to get down to business.</h3>

      	<div class="panel panel-default" style="margin-top: 15px; border-color: #BFEDF9 !important;">
    		<div class="panel-body" style="padding: 0px;">

    		<div class="stepup-dashboard stepup-details-holder">

				<div class="calendar-link setup-dashboard-link">
					<span><img src="{{ URL::asset('assets/images/calendar-thumbnails.png') }}"></span>
					<span class="setup-calendar-link-text" style="font-weight: bold; color: #1B9BD7;">Start Booking Appointments</span>
					<div class="clearfix"></div>
				</div>

				<div class="setup-features-list">
					<span class="bookingpage-link setup-bookingpage-link">
						<div class="features-img-holder">
							<img src="{{ URL::asset('assets/images/booking-page-thumbnail.png') }}">
						</div>
						<a href="#">See My Booking Page &gt;&gt;</a>
							
					</span>

					<span class="booking-btn-link margin-left-collapse setup-dashboard-link">
						<div class="features-img-holder">
							<img src="{{ URL::asset('assets/images/booknow-btn-thumbnail.png') }}">
						</div>
						<a href="#">Get the Booking Button &gt;&gt;</a>
					</span>

					<div class="clearfix"></div>

					<span class="integrations-link margin-top-collapse setup-dashboard-link">
						<div class="features-img-holder">
							<img src="{{ URL::asset('assets/images/integration-thumbnail.png') }}">
						</div>
						<a href="#">View Integrations &gt;&gt;</a>
					</span>

					<span class="staff-services-link margin-left-collapse margin-top-collapse setup-dashboard-link">
						<div class="features-img-holder">
							<img src="{{ URL::asset('assets/images/staff-or-service-thumbnail.png') }}">
						</div>
						<a href="#">Add Staff / Services &gt;&gt;</a>
					</span>

					<div class="clearfix"></div>

				</div>

			</div>

    		</div>
    	</div>

    	<div style="text-align: center;">
    	<button id="config-back" class="config-back-btn" style="font-size: medium;"><i class="glyphicon glyphicon-chevron-left" style="font-size: small;"></i></button>
      	<button id="config-done" class="config-nxt-btn" style="font-size: medium;">Close</button>
    	</div>

    </div>


</div>
</div>



<script type="text/javascript">

jQuery(document).ready(function($) {

	$('#calendar').fullCalendar({
      	header: {
        	left: 'prev,next today',
        	center: 'title',
        	right: 'month,agendaWeek,agendaDay'
      	},

      	defaultView: 'agendaWeek',
      	editable: true,
      	firstDay: 1,
      	slotDuration: '00:15:00',
      	slotLabelInterval: '01:00:00',
      	allDaySlot: false,
      	timezone: 'Asia/SingaPore',
      	columnFormat: 'ddd, MMM DD',
      	selectable: true,
      	selectHelper: true,
      	editable: true,
      	nowIndicator:true,
      	selectConstraint:{
        	start: '00:00', 
        	end: '24:00', 
      	},


      	eventTextColor: 'black',
      	height:'auto',
      	contentHeight:'auto',

  	});// end of calendar
		
	dialog = $( "#clinic-config-dialog" ).dialog({
               
        modal: true,
        draggable: false,
        resizable: false,
        // position: ['center', 'top'],
        show: 'blind',
        hide: 'blind',
        width: 550,
        dialogClass: 'setup-config-ui-dialog',
                
    });


    var view = $('#calendar').fullCalendar('getView');
  	$('#btn-title').text(view.title);
  	highlightCurrentDate();
  	setOpacity();

  	$('.timepicker').timepicker({

	      'timeFormat' : 'h:i A',
	});

});

function highlightCurrentDate(){

  var d = new Date();

  var month = d.getMonth()+1;
  var day = d.getDate();

  var output = d.getFullYear() + '-' + ((''+month).length<2 ? '0' : '') + month + '-' +((''+day).length<2 ? '0' : '') + day;
  // alert(output);

  $("th[data-date*="+output+"]").addClass("header-date");
}

function setOpacity() {

	$(".fc-today").css("opacity","0.1");

}

</script>



<style type="text/css">

	#setupHours .toggle-off.btn-xs {
	    padding-left: 10px;
	    padding-top: 3px;
	}

	#setupHours .toggle-on.btn-xs {
	    padding-right: 10px;
	    padding-top: 3px;
	}
	.ui-widget-overlay {
	    opacity: .6 !important;
	    background: #FFFCFC !important;
	}
	#config-mobile-code-list li:hover, #config-clinic-type-list li a:hover {
		cursor: pointer;
		background: #1997D4;
    	color: white !important;
	}
}

</style>




@include('common.footer')

