<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-78188906-2"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-78188906-2');
	</script>
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta charset="UTF-8">
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='-1'>
	<meta http-equiv='pragma' content='no-cache'>
	<title>{{$title}}</title>
	<link rel="shortcut icon" href="{{ URL::asset('assets/new_landing/images/favicon.ico') }}" type="image/ico">
	{{ HTML::style('assets/css/jquery-confirm.css') }}
	{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
	{{ HTML::style('assets/css/bootstrap/css/bootstrap-theme.css') }}
	{{ HTML::style('assets/css/jquery.toast.css') }}
	{{ HTML::style('assets/css/jquery-ui.css') }}
	{{ HTML::style('assets/css/font-awesome.min.css') }}
	{{ HTML::style('assets/css/offline-theme-default.css') }}
	{{ HTML::style('assets/css/offline-language-english.css') }}
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/css/medicloudv3.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/dashboard/calender.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/css/datepicker-appointment.css?_={{ $date->format('U') }}">
	{{ HTML::style('assets/css/fullcalendar.min.css') }}
	{{ HTML::style('assets/css/scheduler.css') }}
	{{ HTML::script('assets/js/jquery.min.js') }}
	{{ HTML::script('assets/js/jquery.toast.js') }}
	<script type="text/javascript" src="<?php echo $server; ?>/assets/js/socket.io.js?_={{ $date->format('U') }}"></script>
	{{ HTML::script('assets/css/bootstrap/js/bootstrap.min.js') }}
	{{ HTML::script('assets/js/jquery.validate.min.js') }}
	{{ HTML::script('assets/js/jquery-ui.js') }}
	{{ HTML::script('assets/js/moment.min.js') }}
	{{ HTML::script('assets/js/fullcalendar.min.js') }}
	{{ HTML::script('assets/js/scheduler.js') }}
	{{ HTML::script('assets/js/jquery-blockUI.js') }}
	{{ HTML::script('assets/js/jquery-confirm.js') }}
	{{ HTML::script('assets/js/jquery-timepicker/jquery.timepicker.js') }}
	{{ HTML::style('assets/js/jquery-timepicker/jquery.timepicker.css') }}
	{{ HTML::script('assets/js/jquery.autocomplete.min.js') }}
	{{ HTML::script('assets/js/pdfobject.js') }}
	{{ HTML::script('assets/js/jquery-blockUI.js') }}
	{{ HTML::script('assets/js/jquery.toast.js') }}
	{{ HTML::script('assets/js/jquery-confirm.js') }}
	{{ HTML::script('assets/js/jquery-timepicker/jquery.timepicker.js') }}
	{{ HTML::style('assets/js/jquery-timepicker/jquery.timepicker.css') }}
	{{ HTML::script('assets/dashboard/country_code.js') }}
	{{ HTML::style('assets/css/bootstrap/css/bootstrap-toggle.min.css') }}
	{{ HTML::script('assets/css/bootstrap/js/bootstrap-toggle.min.js') }}
	{{ HTML::script('assets/e-claim/js/angular.min.js') }}
	{{ HTML::script('assets/js/FileSaver.min.js') }}
	{{ HTML::script('assets/js/json-export-excel.min.js') }}
	{{ HTML::script('assets/js/offline.min.js') }}
	<script type="text/javascript" src="<?php echo $server; ?>/assets/settings/sms.js?_={{ $date->format('U') }}"></script>
</head>
<input type="hidden" id="h_base_url" value="{{URL('/')}}">
<nav class="navbar navbar-default">
	<div class="container-fluid">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
        	<img alt="Brand" src="{{ URL::asset('assets/images/Mednefits Logo V2.svg') }}" class="nav-logo" >
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	        <ul class="nav navbar-nav">
	        <!-- <li ><a href="#">Dashboard <span class="sr-only">(current)</span></a></li> -->
	        <li ><a href="#" style="cursor: default;">&nbsp;<span class="sr-only">(current)</span></a></li>
	        <!-- <li class="active"><a href="{{URL::to('app/clinic/dashboard-summary')}}">Dashboard</a></li> -->
	        <!-- <li class=""><a href="{{URL::to('app/clinic/appointment-home-view')}}">Calendar</a></li> -->
	        <!-- <li class=""><a href="{{URL::to('app/setting/main-setting')}}">Settings</a></li> -->
	        <li class="{{Request::path() == 'app/clinic/dashboard-summary' ? 'active' : '';}}">{{ HTML::link('app/clinic/dashboard-summary', 'Dashboard')}}</li>
	        <li class="{{Request::path() == 'app/setting/claim-report' ? 'active' : '';}}">{{ HTML::link('app/setting/claim-report', 'Claim')}}</li>
	        <li class="{{Request::path() == 'app/clinic/appointment-home-view' ? 'active' : '';}}">{{ HTML::link('app/clinic/appointment-home-view', 'Calendar')}}</li>
	        <!-- <li class=""><a href="#" id="sms_link" data-toggle="modal" data-target="#sms-modal">SMS</a></li> -->
	        <!-- <li class="{{Request::path() == 'app/setting/main-setting' ? 'active' : '';}}">{{ HTML::link('app/setting/main-setting', 'Settings')}}</li> -->

	        <!-- <li><span style="color: #FFF;float: left;margin: 15px 0 0 15px;">24/7 Support : 62547889</span></li> -->
	        <!-- <li class="dropdown" >
	          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Settings <span class="caret"></span></a>
	          <ul class="dropdown-menu">
	            <li><a href="{{URL::to('app/setting/main-setting')}}">Settings</a></li>
	          </ul>
	        </li> -->
	      </ul>

	      
	      	<!-- <a href="{{URL::to('app/auth/logout')}}" style="color: white;"><span class="glyphicon glyphicon-off" aria-hidden="true"></span></a> -->

		    <div class="navbar-text pull-right logout-section" style="padding-right: 38px;">
		    	<span href="#" class="help-li" style="position: relative;cursor: pointer;">
			    	<span class="weight-700 color-white3 help-text" style="margin-right: 25px">Need Help?</span>
			    	<div class="help-container text-left">
			    		<div class="arrow-custom"></div>
	            <h3 class="font-medium2 color-blue-custom weight-700">We're here to help.</h3>
	            <div class="white-space-20"></div>
	            <p class="font-medium2 weight-700">You may ring us</p>
	            <p class="weight-700">+65 3163 5403</p>
	            <p class="weight-700">+60 330 995 774</p>
	            <p class="weight-700">Mon - Fri 9:30am to 6:30pm</p>
	            <div class="white-space-20"></div>
	            <p class="font-medium2 weight-700">Drop us a note, anytime</p>
	            <p class="weight-700">support@mednefits.com</p>
	            <div class="white-space-20"></div>
	            <div style="border-bottom: 1px solid #aaa;"></div>
	            <div class="white-space-20"></div>
	            <a class="btn-learn-how" href="{{URL::to('app/clinic/mednefits-tutorials')}}">Learn how Mednefits Works</a>
	          </div>
          </span>
		    	<div class="btn-group btn-login-div">
				    <a id="btn-logout"  href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				   	 	<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
			    		<!-- <img src="{{ URL::asset('assets/images/Logout-button.png') }}" width="20" height="20"> -->
				    </a>
				    <ul class="dropdown-menu" style="">
				    	<li><a href="javascript:void(0)" id="sms_link" data-toggle="modal" data-target="#sms-modal">SMS</a></li>
					    <li><a href="{{URL::to('app/setting/main-setting')}}">Settings</a></li>
					    <li><a href="{{URL::to('app/auth/logout')}}">Logout</a></li>
					  </ul>
				</div>
		    </div>
		    
		    @if(Request::path() == 'app/clinic/appointment-home-viewsds')
		    <div style="width: 250px;display: inline-block;float: right;margin: 10px 50px 0 0;" >
		    	@include('dashboard.search')
	      	<!-- <div class="input-group">
				   <input id="search-customer-feature" type="text" class="form-control" placeholder="Search IC Number" name="" style="height: 32px !important;">
				    <span class="input-group-btn">
				      <button id="search-feature-open-modal" class="btn btn-default btn-search-top" type="button" style="background: #CADDEC;width: 30px;">
				      	<i class="fa fa-search" style="color: #1868AC"></i>
				      </button>
				    </span>
				  </div> -->
				</div>
		   </div>
		   @endif


	</div><!-- /.container-fluid -->
</nav>




<!-- sms model  nhr 2016-8-3-->
<style type="text/css">
	.btn-search-top:hover{
		background: #CADDEC !important;
	}

	div.modal-content{ top: 100px; }
	p, .modal-title{ margin: 0 0 0 25px; }
	#content,#content-two{ padding: 20px 0px 20px 40px; }
	.tf{ background: white !important;border: 1px solid #929292 !important; }
	.control-label { text-align: left !important }
	.modal-footer { padding: 15px 0px !important; margin: 0 85px 0 38px !important; }
	.btn {     background: #6fc9f5; border: none; }
	.font { font-family: "Montserrat", sans-serif; }
	#sms-mobile-codes { cursor: pointer }
	#sms-mobile-codes li:hover { cursor: pointer; background: #1997D4 !important; color: white !important;}
	.bt:hover { background: #6fc9f5 !important; }

	#search-booking-modal .modal-body ul li a{
		color: #000;
	}

	#search-booking-modal .modal-body ul li.active a{
		color: #000;
		font-weight: 700;
	}

	#search-booking-modal input{
		height: 38px !important; 
	}

	.ui-timepicker-disabled { display: none; }

	#sms-modal,
	#sms-modal *{
		box-sizing: border-box !important; 
	}
	#sms-modal .modal-body{
		background: none;
	}
	#sms-modal .modal-body .header{
    padding: 10px;
    margin: -15px -15px 15px -15px;
    background: rgb(111, 201, 245);
	}
	#sms-modal .modal-body #sms-content{
    padding: 0 20px;
	}
	#sms-modal .modal-footer{
    margin: 0 35px !important;
	}
</style>



<div id="sms-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title font">Mednefits SMS Platform</h4>
      </div>
      <div class="modal-body">
      	<div class="header">
       		<p class="font" id="error">Send SMS via Mednefits</p>
        </div>
        <div id="sms-content">
	      	<form class="form-horizontal">
		      	<div class="form-group">
					    <label for="name" class="col-sm-2 c control-label ">Name</label>
					    <div class="col-sm-10" >
					      <input type="text" id="name" class="form-control tf" placeholder="Sender's Name">
					    </div>
						</div>
				  	</br>

						<div class="form-group">
					    <label for="doctor" class="col-sm-2 c control-label ">Phone</label>

					    <div id="code-dropdown" class="col-sm-2" >

							<input type="button" class="tf form-control input-sm mobile-input-code input-style dropdown-toggle" id="phone_code" data-toggle="dropdown" value="+65">

							<ul class="dropdown-menu" id="sms-mobile-codes" style="margin-left: 15px; width: 270px; max-height: 180px; overflow-y: auto; overflow-x: hidden;">
							</ul>
					    </div>

					    <div class="col-sm-8" >
					      <input type="text" id="phone" class="form-control tf" placeholder="Phone Number">
					    </div>
						</div>
						</br>

						<div class="form-group">
						    <label for="message" class="col-sm-2 c control-label ">Message</label>
						    <div class="col-sm-10" >
						       <textarea id="message"  class="tf form-control" style="height:100px !important" placeholder="Message"></textarea>
						    </div>
						</div>

	      	</form>
	      	<span class="error font" style="margin-left: 78px; display:none"></span>
	      </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-primary font bt" id="send_message" ">Send Message</button>
        <button type="button" class="btn btn-primary font bt" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">

	// $('#btn-logout').popover({

	// 	html: 'true',
	//     title : 'Are you sure ?',
	//     content : '<a href="{{URL::to('app/auth/logout')}}" id="logout" class="btn" style="background: #1797D4; color: white;">Logout</a> <button class="btn" id="logout-cancel" style="    background: white; border: 1px solid #C9C9C9; color: black;">Cancel</button>'

	// });

	$( ".help-li" ).mouseover(function() {
	  $('.help-container').show();
	});

	$( ".help-container" ).mouseleave(function() {
	  $('.help-container').hide();
	});

	$( "#btn-logout" ).click(function() {
	  $('.help-container').hide();
	});

	$(document).on('click', '#logout-cancel', function(event) {
		$('#btn-logout').popover('hide');
	});


	$(document).on('click', '#sms-mobile-codes li', function(event) {

  		id = $(this).attr('id');
  		$('#phone_code').val(id);
	});

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

</script>

<style>
	.logout-section .popover .popover-title {background-color: #EAEAEA; color: black;}
</style>


<body>
