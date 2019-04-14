<!DOCTYPE html>
<html>
<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-78188906-2"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-78188906-2');
	</script>
<head>
<title> {{ $title }}</title>
<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
<link rel="shortcut icon" href="{{ asset('assets/images/Medicloud-Favicon_16x16px.ico') }}" type="image/ico">
<link href='https://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "ca-pub-8344843655918366",
    enable_page_level_ads: true
  });
</script>
@if( (strpos($clincname, 'Only Group') !== false ) )
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');


fbq('init', '300800066938054');
fbq('track', 'OnlyGroup (Aesthetics)');</script>
<noscript>
	<img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=300800066938054&ev=PageView&noscript=1"
/></noscript>
@endif
{{ HTML::style('assets/css/medicloudv3.css') }}
{{ HTML::style('assets/css/datepicker-appointment.css') }}
<!-- Latest compiled and minified CSS -->

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<!-- Latest compiled and minified JavaScript -->
<script src="https://code.jquery.com/jquery-2.2.0.min.js" ></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.14.0/jquery.validate.min.js" ></script>
 <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
{{ HTML::script('assets/js/socket.io.js') }}
{{ HTML::script('assets/js/doctor-widget.js') }}
{{ HTML::script('assets/js/jquery-timepicker/jquery.timepicker.js') }}

{{ HTML::style('assets/js/jquery-timepicker/jquery.timepicker.css') }}

{{ HTML::script('assets/dashboard/country_code.js') }}
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "ca-pub-8344843655918366",
    enable_page_level_ads: true
  });
</script>
<style type="text/css">
body { background-color: #eeeeee; }
.box { border: 1px solid #fff; margin: auto; background-color: white; padding: 0px 30px 0px 30px; }
.borderless tbody tr td, .borderless tbody tr th, .borderless thead tr th { border: none; }
/*.table{ margin-left: 20px;}*/
.field-name { color: #C0C0C0; }
.btn-cancel, .btn-update { border: none !important; }
.ui-timepicker-wrapper ul.ui-timepicker-list li.ui-timepicker-disabled { display: none; }
.clear { clear: both; }
.ui-datepicker .ui-datepicker-buttonpane { margin-bottom: 80px; }
/*widget css added by Irshad
	  Note: Alignment fixes made to the widget.
	  Dated:04 March 2016    */


.custom-float-l { float: left; }
.custom-float-r { float: right; }
.custom-padding-r { padding-right: 0px; }
.custom-padding-t { padding-top: 15px; }
.custom-margin-t0 { margin-top: 0px !important; }
.custom-padding-b { padding-bottom: 10px !important; }
.custom-margin-l { margin-left: 10px; }
.ui-datepicker { border: 1px solid #f0f0f0 !important; }
.error { padding-left: 10px !important; }
td { padding: 8px 8px 8px 0px !important; }
hr { /* margin-bottom: 12px; margin-top: 12px; */ border-color: #afafaf;}
.mobile-width2 { width: 46%; padding: 0 0 0 9px;}
.form-control {height: 40px !important;}

/*iPhone 2G-4S in portrait & landscape*/
@media only screen and (min-device-width : 320px) and (max-device-width : 480px) {
body { /*background-color:#F00;  height:100vh;*/ } /*red*/
}




/*iPhone 5 & 5S in portrait & landscape*/
@media only screen and (min-device-width : 320px) and (max-device-width : 568px) {
body { background-color: #eeeeee; /* height:100vh;*/ }/*green*/
.box { background-color: #FFF; border: 1px solid #fff; margin: auto; padding: 0 15px; width: auto !important; }
.mobile-logo { width: 44% !important; float: left !important; }
.mobile-header { font-size: 11px !important; padding-top: 7px !important; float: left !important; width: 50% !important; }
.mobile-pad-l { padding-left: 15px; }
.mobile-pad-l-2 { padding-left: 15px; }
.mobile-pad-l-3 { padding-left: 15px; }
.mobile-pad-l-4 { padding-left: 25px; }
.mobile-pad-r { padding-right: 0px; }
.mobile-width { width: 100%!important; }
.mobile-width2 { width: 69%!important; padding-left: 18px !important;}
.mobile-pad-none { padding: 0px !important; }
.mobile-input { width: 39% !important }
.mobile-input-code { width: 100% !important }
.mob-clear { clear: both; }
hr { margin-bottom: 12px; margin-top: 20px; }
.btn-update { padding: 15px 20px !important; }
.btn-cancel { padding: 15px 16px !important; }
.ui-datepicker { display: none; padding: 0.2em 0.2em 0; width: auto !important; }
.mobile-title-width { width: 23%; }
#ui-datepicker-div {width: 261px !important;}
#chk-condition {display: none;}
#term-text {position: absolute; padding-top: 5px;}
.nav-tabs {padding: 5px 0px !important;}
.nav-tabs>li {font-size: 11px;}
.nav>li>a {padding: 10px 3px !important;}
}

@media only screen and (min-width: 768px){
	.form-horizontal .control-label {padding: 0px; padding-right: 25px; padding-top: 15px !important; }
	.btn-cancel, .btn-update { height: 20px; }
	td { width: 265px !important; }
	#con-check {margin: 1.5em 0.5em -0.3em 2.1em !important;}
}




/*iPhone 6 in portrait & landscape*/
@media only screen and (min-device-width : 375px) and (max-device-width : 667px) {
.nav-tabs>li {font-size: 12px !important;}
.nav>li>a {padding: 10px 7px !important;}
.mobile-header {padding-top: 10px !important;}

}





/*iPad mini in portrait & landscape*/
@media only screen and (min-device-width : 768px) and (max-device-width : 1024px) and (-webkit-min-device-pixel-ratio: 1) {
body { /*background-color: #F39;  height:100vh;*/ }/*pink*/
}




/*iPad 3 & 4 Media Queries
Retina iPad in portrait & landscape*/
@media only screen and (min-device-width : 768px) and (max-device-width : 1024px) and (-webkit-min-device-pixel-ratio: 2) {
body { /*background-color: #30F;  height:100vh;*/ }/*BLUE*/
}
</style>



</head>
<body>

<?php
//	if ($doctors) {
//		$items = array();
//		$items[''] = '-- select --';
//		foreach ($doctors as $doctor)
//		{
//		    $items[$doctor->DoctorID] = $doctor->DocName;
//		}
//	} else {
//		$items = array();
//		$items[''] = '-- select --';
//	}

        if ($procedure) {
		$items = array();
		$items[''] = '-- Select --';
		foreach ($procedure as $proc)
		{
		    $items[$proc->ProcedureID] = $proc->Name;
		}
	} else {
		$items = array();
		$items[''] = '-- Select --';
	}

 ?>
<script type="text/javascript">
	$(function( ){
		$('#date').attr('disabled', true);
	});
</script>

<input type="hidden" id="h-clinicID" value="{{$clincID}}">
<input type="hidden" id="h-clinic-email" value="{{$clincID}}">
<input type="hidden" id="h-procedureID" value="0">
<input type="hidden" id="h-otp-status" value="0">




<!-- Change By : Tiran Praneeth (Mx) -->


  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #114158;">
        <!-- <h4 class="modal-title" id="myModalLabel" style="padding-left: 35px;">Appointment</h4> -->
        <img class="mobile-logo"  src="{{ URL::asset('assets/images/mednefits logo v3 (white) LARGE.png') }}"   alt="" style="padding: 5px;border-right: 1px solid #ECECEC; float:left; width: 200px;" />&nbsp;
                <div class="mobile-header" style=" float:left; padding-left:10px; padding-top:15px; color: white; font-size: 16px;"  >{{ $clincname }}</div><br>
      </div>

      <div class="modal-body" style="font-size: 12px; padding: 0px;">

  <ul class="nav nav-tabs" id="tabs" style="padding: 5px 15px; border-bottom: 0px;">
    <!-- <li id="booking-tab" class="active disabledTab"><a data-toggle="tab" href="#booking">BOOKING</a></li> -->
    <li id="booking-tab" class="active disabledTab"><a data-toggle="tab" href="#booking">1. BOOKING</a></li>
    <li id="patient-tab" class="disabledTab"><a data-toggle="tab" href="#patient">2. YOUR DETAIL</a></li>
    <li id="confirm-tab" class="disabledTab"><a data-toggle="tab" href="#confirm">3. CONFIRM</a></li>
    <li id="done-tab" class="disabledTab"><a data-toggle="tab" href="#done">4. DONE</a></li>
  </ul>



  <!-- ================ Booking tab contents ================ -->



  <div class="tab-content">
    <div id="booking" class="tab-pane fade in active">

      <div class="panel panel-default" style="margin: 0px; border-radius: 0px !important;">
            <div class="panel-body">

            <div class="" id="screen1">

            <div class="clear"></div>
            <br>
		  		<form class="form-horizontal" id="form-1">

                <div class="form-group">
				    <label for="procedure" class="col-sm-2 col-xs-2 control-label custom-padding-r mobile-pad-l-2">Procedure</label>
				    <div class="col-sm-8 col-xs-11" >
				      {{ Form::select('procedure', $items, null, array('class' => 'form-control input-sm input-style', 'id'=>'procedure')) }}
				    </div>
				  </div>
				  <br>


				  <div class="form-group">
				    <label for="doctor" class="col-sm-2 col-xs-2 control-label custom-padding-r mobile-pad-l">Doctor</label>

				    <div class="col-sm-8 col-xs-11" >
				      {{ Form::select('doctor', array('' => '-- Select --'), null, array('class' => 'form-control input-sm input-style', 'id'=>'doctor')) }}
				    </div>
				  </div>
				  <br>


				  <div class="form-group">
				    <label for="" class="col-sm-2 col-xs-2 control-label custom-padding-r mobile-pad-l-3">Date</label>
				    <div class="col-sm-8 col-xs-11">
				      {{ Form::text('date', null, array('class' => 'form-control input-sm input-style', 'id'=>'date')) }}
				      <!-- <input type="button" class="form-control input-sm" id="date" value="" placeholder=""> -->
				    </div>
				  </div>
				  <br>


				  <div class="form-group ">
				    <label for="" class="col-sm-2 col-xs-10 control-label custom-padding-r mobile-pad-l-3">Time</label>
                    <div class="mob-clear"></div>
				    <div class="col-sm-2 col-xs-5 mobile-input" style="padding: 0 13px;">
				      <!-- {{ Form::text('time', null, array('class' => 'form-control input-sm ', 'id'=>'time')) }} -->
				      <input type="button" class="form-control input-sm input-style" id="time" value="" placeholder="" disabled>
				    </div>
                    <div class="custom-float-l custom-padding-t">To</div>


				    <div class="col-sm-2 col-xs-5 mobile-input" style="padding: 0 13px;">
				      {{ Form::text('lbl_time', null, array('class' => 'form-control input-sm input-style', 'id'=>'lbl_time' ,'readonly','style'=>" background:white !important")) }}
				    </div>
				    <!-- <span for="lbl_time" id="lbl_time" class="col-sm-2 control-label" style="text-align: left !important"></span> -->
				  </div>

				  	<br>
				  <div class="form-group custom-margin-b">
				    <label for="remarks" class="col-sm-2 col-xs-2 control-label custom-padding-r mobile-pad-l-2">Remarks</label>
				    <div class="col-sm-8" >
				      {{ Form::text('remarks',null, array('class' => 'form-control input-sm input-style', 'id'=>'remarks')) }}
				    </div>
				  </div>

                 <hr>

				  <div class="form-group">
				    <!--<label for="remarks" class="col-sm-2 control-label custom-padding-r"></label>-->
				    <div class="col-sm-8" >
				      <button class="col-sm-3  btn-update font-type-Montserrat" type="submit" id="btn-next">Next</button>
				      	<button class="col-sm-offset-1 col-sm-3 btn-cancel font-type-Montserrat custom-margin-l"  id="btn-cancel1" onclick="window.close();">Close</button>
				    </div>
				  </div>

				</form>

		  	</div>	<!-- //end of 1st screeen -->

            </div>
          </div>
    </div>





    <!-- ============= Patient tab contents ============= -->





    <div id="patient" class="tab-pane fade">

    <div class="panel panel-default" style="margin: 0px; border-radius: 0px !important;">
    	<div class="panel-body" >

    	<div class="" id="screen2">

            <div class="clear"></div>
            <br>
		  		<form class="form-horizontal" id="form-2">

				  <div class="form-group">
				    <label for="doctor" class="col-sm-2 col-xs-2 control-label custom-padding-r  ">NRIC/FIN</label>

				    <div class="col-sm-8 col-xs-11" >
				      {{ Form::text('nric',null, array('class' => 'form-control input-sm input-style', 'id'=>'nric')) }}
				    </div>
				  </div>
				  <br>


				  <div class="form-group">
				    <label for="procedure" class="col-sm-2 col-xs-2 control-label custom-padding-r mobile-title-width ">Your Name</label>
				    <div class="col-sm-8 col-xs-11" >
				      {{ Form::text('name',null, array('class' => 'form-control input-sm input-style','id'=>'name')) }}
				    </div>
				  </div>
				  <br>




				  <div class="form-group">
				    <label for="date" class="col-sm-2 col-xs-10 control-label custom-padding-r mobile-pad-l ">Phone</label>
                    <div class="mob-clear"></div>
				    <div id="code-dropdown" class="col-sm-2 col-xs-2 mobile-pad-r" >
						<!-- {{ Form::text('phone_code',null, array('class' => 'form-control input-sm mobile-input-code input-style dropdown-toggle' ,'id'=>'phone_code','data-toggle'=>"dropdown")) }} -->

						<input type="button" class="form-control input-sm mobile-input-code input-style dropdown-toggle" id="phone_code" data-toggle="dropdown" value="+65">

						<ul class="dropdown-menu" id="doc-mobile-codes" style="margin-left: 15px; width: 270px; max-height: 180px; overflow-y: auto; overflow-x: hidden;">
						</ul>
				    </div>

                     <div class="col-sm-5 col-xs-4 mobile-width2" >
				      {{ Form::text('phone',null, array('class' => 'form-control input-sm input-style','id'=>'phone')) }}
				    </div>
				  </div>
				  <br>


                  <div class="form-group ">
				    <label for="" class="col-sm-2 col-xs-2 control-label custom-padding-r mobile-pad-l-3 ">Email</label>
                    <div class="mob-clear"></div>
				    <div class="col-sm-8 col-xs-11" >
				     {{ Form::text('email',null, array('class' => 'form-control input-sm input-style','id'=>'email')) }}
				    </div>
                   </div>

				  	<br>

                 <hr>

				  <div class="form-group">
				    <!--<label for="remarks" class="col-sm-2 control-label custom-padding-r"></label>-->
				    <div class="col-sm-8" >
				      <button class="col-sm-3  btn-update font-type-Montserrat" type="submit" id="btn-next-2">Next</button>
				      	<button class="col-sm-offset-1 col-sm-3 btn-cancel font-type-Montserrat custom-margin-l"  id="btn-cancel2">Back</button>
				    </div>
				  </div>

				</form>

		  	</div>	<!-- //end of 1st screeen -->


    </div>
    </div>


  </div>




  <!-- ============== Confirm tab contents ============== -->




    <div id="confirm" class="tab-pane fade">

    <div class="panel panel-default" style="margin: 0px; border-radius: 0px !important;">
    	<div class="panel-body">

    	<div class="box" id="screen3">

		  	<!-- <div class="row">
		  		<div class="col-md-12">
		  		     <div class="mobile-width "  style=" width:510px; text-align:center; margin-left:auto; margin-right:auto;">
		  		<img class="mobile-logo"  src="{{ URL::asset('assets/images/mc-v2-logo.png') }}"   alt="" style="border-right: 1px solid #ECECEC; float:left;" />&nbsp;
                <div class="mobile-header" style=" float:left; padding-left:10px; padding-top:20px;"  >{{ $clincname }}</div><br>
		  	</div>
		  		</div>
		  	</div> -->


		  	<div class="row text-left">
		  		<div style="padding: 0px 15px;">
		  			<table style="margin-bottom: auto !important" class="table borderless">
		  			<tr class="text-left">
		  				<td ><span class="field-name">Doctor</span> <p id="sc3-doctor"> </p></td>
		  				<td ><span class="field-name">NRIC</span> <p id="sc3-nric"> </p></td>
		  			</tr>
		  			<tr class="text-left">
		  				<td ><span class="field-name">Procedure</span> <p id="sc3-procedure"> </p></td>
		  				<td ><span class="field-name">Name</span> <p id="sc3-name"> </p></td>
		  			</tr>
		  			<tr class="text-left">
		  				<td ><span class="field-name">Date & Time</span> <p id="sc3-datetime"> </p></td>
		  				<td ><span class="field-name">Email and Phone</span> <p id="sc3-emailphone"></p></td>
		  			</tr>
		  			<tr class="text-left">
		  				<td ><span class="field-name">Notes</span> <p id="sc3-notes"></p></td>
		  				<td ><span class="field-name">Price</span> <p id="sc3-price"></p></td>
		  			</tr>
		  		</table>
		  		</div>
		  	</div>

			<hr >
			<div class="row text-left">
				<div class="col-md-11">
					<form class="form-inline">
					  <div class="form-group">
					    <span class="field-name">CODE&nbsp;&nbsp;</span>
					    {{ Form::text('code',null, array('class' => 'form-control input-style', 'id'=>'code')) }}
					    &nbsp;<span id="lbl_otp_code_msg"></span>
					  </div>
					</form>
				</div>
			</div>
			<!-- <hr>  -->
			<div class="row text-left">
				<div class="col-md-10">
					<form class="form-inline">
					  <div class="form-group" style="padding-top: 10px;">
					    <input type="checkbox" value="0" name="chk-condition" id="chk-condition" style="display: none;">
					    <label style="vertical-align: bottom !important" for="chk-condition" class="field-name mobile-pad-l-4"><span id="con-check" style="margin: 0.25em 0.5em -0.3em 0.1em; padding: 2px; cursor: pointer;height: 15px;width: 15px;position: relative;top: 4px;font-size: 16px;margin-left: 25px !important;"><span></span></span></label>
					    <span id="term-text">I agree to the <a href="https://medicloud.sg/terms" target="_new" title="">terms and conditions</a> of <a href="https://medicloud.sg" target="_new" title="">medicloud.sg</a></span>
					  </div>
					</form>
				</div>
			</div>
			<hr style="margin-top: 25px;">
			<div class="row">
				<div class="col-md-12">
					<button class="col-sm-3 btn-update font-type-Montserrat" type="submit" id="btn-confirm">Confirm Booking</button>
					<button class="col-sm-offset-1 col-sm-3 btn-cancel font-type-Montserrat custom-margin-l"  id="btn-cancel3">Back</button>
				</div>
			</div>

				</div> <!-- //end of 3rd screeen -->



    </div>
    </div>


  </div>





  <!-- =================== DONE tab contents =================== -->





    <div id="done" class="tab-pane fade">

    <div class="panel panel-default" style="margin: 0px; border-radius: 0px !important;">
    	<div class="panel-body">

    	<!-- /Thank you page/ -->

		<div class="box" id="screen4" style="text-align: center;">
			<!-- <div class="row">
	  			<div class="mobile-width "  style=" width:510px; text-align:center; margin-left:auto; margin-right:auto;">
  					<img class="mobile-logo"  src="{{ URL::asset('assets/images/mc-v2-logo.png') }}" alt="" />&nbsp;
  				</div>
	  		</div> -->

			<br><br>

		  	<div>
		  		<span class="font-type-Montserrat">Thank you for your booking,<br>
		  		We have confirmed your booking and dispached an email <br>
		  		to your inbox, along with a sms notification <br>
		  		<br><br>
		  		we will send you timely reminders before your <br>
		  		appointment time.</span>
		  	</div>
		  	<br><br>
		  	<div class="row">
		  		<button class="col-sm-offset-5 col-sm-2 btn-update font-type-Montserrat"  id="" onclick="window.close();" style="margin: auto !important; float: none !important; min-width: 50px !important;">Close</button>
		  	</div>

		</div> <!-- //end of screen 4 -->

	    </div>
	</div>


  	</div>


    </div>

    </div>
  </div>
</div>

<style>

.panel-body { padding-bottom: 25px;}

.input-style { background: white !important; border: 1px solid #929292 !important; text-align: left;}

.modal-header {background: #114158; color: white; border-bottom: 0px !important;}

.modal-header .close, .modal-header .close > span {color: #FFFFFF !important; text-shadow: inherit;}

.modal-body {background: rgb(111, 201, 245);}

.modal-body .nav-tabs .active a {
	background-color: rgb(111, 201, 245);
    border: 1px solid rgba(221, 221, 221, 0);
    border-bottom-color: #0090D4;
    border-bottom-width: 0px;
    font-weight: bold;
    color: black !important;
}

.modal-body .nav-tabs .active a:hover {

	background-color: rgb(111, 201, 245);
    border: 1px solid rgba(221, 221, 221, 0);
    border-bottom-color: #0090D4;
    border-bottom-width: 0px !important;
    font-weight: bold;
    color: black !important;
}

.modal-body .nav-tabs li a:hover {
    border-color: rgba(238, 238, 238, 0);
    background-color: rgba(238, 238, 238, 0);
    color: black !important;
}

.modal-body .nav-tabs>li>a {
        color: #232323;
}

.modal-body>.nav-tabs>li.active>a:focus, .modal-body.nav-tabs>li.active>a:hover {
    cursor: default !important;
    background-color: transparent !important;
    border: 1px solid transparent !important;
    border-bottom-color: transparent !important;
}

.input-width { width: 355px; }

.ul-width { width: 390px; }

.details-label {
	color: #5A5A5A;
	margin-bottom: 0px;
	line-height: 35px;
}

.ext-left{
    float:left;
}

.ext-right{
    float:right;
}

.right-inner-addon {
    position: relative;
    padding-right: 30px;
}

.right-inner-addon input {
    padding-right: 30px;
    background: white;
    width: 455px;
    height: 25px;
}

.right-inner-addon i {
    position: absolute;
    right: 0px;
    padding: 15px 0px;
    pointer-events: none;
}

.slot-blocker-width { width: 135px; }

.show { display: block; }

.hide { display: none; }

.disabledTab {pointer-events: none;}

a.ui-datepicker-next.ui-corner-all:hover,
a.ui-datepicker-prev.ui-corner-all:hover {
    background: white !important;
    border: 0px !important;
    border-radius: 0px !important;
    cursor:pointer;
}

.ui-icon-circle-triangle-e{
    background-image: url("../../assets/images/ico_right arrow.svg") !important;
    background-position: -3px !important;
    background-size: 16px !important;
}

.ui-icon-circle-triangle-w {
    background-image: url("../../assets/images/ico_left arrow.svg") !important;
    background-position: 3px !important;
    background-size: 16px !important;
}
.ui-datepicker-header{
	    background: white !important;
    color: darkgray !important;
}

#ui-datepicker-div {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}

#doc-mobile-codes li:hover {
			cursor: pointer;
			background: #1997D4 !important;
    		color: white !important;
}

</style>

<script>
jQuery(document).ready(function($) {

$('#code-dropdown').on('shown.bs.dropdown', function () {

    var $this = $(this);
    // attach key listener when dropdown is shown
    $(document).keypress(function(e){

      // get the key that was pressed
      var key = String.fromCharCode(e.which);
      // look at all of the items to find a first char match
      $this.find("li").each(function(idx,item){
        $(item).addClass("hide"); // clear previous active item
        $(item).removeClass("show");

        if ($(item).text().charAt(0).toLowerCase() == key) {
          // set the item to selected (active)
          $(item).addClass("show");
          $(item).removeClass("hide");
        }
        else{
            $(item).addClass("hide");
            $(item).removeClass("show");
        }
      });

    });

})

// unbind key event when dropdown is hidden
$('#code-dropdown').on('hide.bs.dropdown', function () {

    var $this = $(this);

    $this.find("li").each(function(idx,item){

        $(item).addClass("show");
        $(item).removeClass("hide");
    });

    $(document).unbind("keypress");

})

});
</script>

</body>
</html>