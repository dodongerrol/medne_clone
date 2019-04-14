<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta charset="UTF-8">
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='-1'>
<meta http-equiv='pragma' content='no-cache'>
<title>{{$title}}</title>
<link rel="shortcut icon" href="{{ URL::asset('assets/new_landing/images/favicon.ico') }}" type="image/ico">
<link href='https://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
{{ HTML::style('assets/css/jquery-confirm.css') }}
{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
{{ HTML::style('assets/css/bootstrap/css/bootstrap-theme.css') }}
{{ HTML::style('assets/css/medicloudv3.css') }}
{{ HTML::style('assets/css/jquery.toast.css') }}

{{ HTML::style('assets/dashboard/calender.css') }}
{{ HTML::style('assets/css/datepicker-appointment.css') }}
{{ HTML::style('assets/css/fullcalendar.css') }}

<!-- Latest compiled and minified CSS -->

<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous"> -->


<!-- Optional theme -->
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous"> -->
{{ HTML::style('assets/css/jquery-ui.css') }}

<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"> -->


<!-- Latest compiled and minified JavaScript -->

{{ HTML::script('assets/js/jquery.min.js') }}
{{ HTML::script('assets/css/bootstrap/js/bootstrap.min.js') }}
{{ HTML::script('assets/js/jquery-ui.js') }}
<!-- <script src="https://cdn.jsdelivr.net/jquery.validation/1.14.0/jquery.validate.min.js" ></script> -->
<!-- <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script> -->
<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script> -->

{{ HTML::script('assets/js/jquery-blockUI.js') }}
{{ HTML::script('assets/js/jquery.toast.js') }}
{{ HTML::script('assets/js/jquery-confirm.js') }}
{{ HTML::script('assets/js/jquery-timepicker/jquery.timepicker.js') }}

{{ HTML::style('assets/js/jquery-timepicker/jquery.timepicker.css') }}

{{ HTML::script('assets/dashboard/country_code.js') }}
{{ HTML::script('assets/settings/sms.js') }}
{{ HTML::style('assets/css/bootstrap/css/bootstrap-toggle.min.css') }}
<!-- <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet"> -->

<!-- <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script> -->
{{ HTML::script('assets/css/bootstrap/js/bootstrap-toggle.min.js') }}
</head>
<input type="hidden" id="h_base_url" value="{{URL('/')}}">

<nav class="navbar navbar-default">
	<div class="container-fluid">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
        	<img alt="Brand" src="{{ URL::asset('assets/images/mednefits logo v3 (white) LARGE.png') }}" class="nav-logo" >
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	        <ul class="nav navbar-nav">
	        <!-- <li ><a href="#">Dashboard <span class="sr-only">(current)</span></a></li> -->
	        <li ><a href="#" style="cursor: default;">&nbsp;<span class="sr-only">(current)</span></a></li>
	        <li class=""><a href="{{URL::to('app/clinic/dashboard-summary')}}">Dashboard</a></li>
	        <li class=""><a href="{{URL::to('app/clinic/appointment-home-view')}}">Calendar</a></li>
	        <li class="active"><a href="">Settings</a></li>
	        <li class=""><a href="#" id="sms_link" data-toggle="modal" data-target="#sms-modal">SMS</a></li>
	        <!-- <li class="dropdown active" >
	          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Settings <span class="caret"></span></a>
	          <ul class="dropdown-menu">
	            <li><a href="">Settings</a></li>
	          </ul>
	        </li> -->
	      </ul>

		    <p class="navbar-text pull-right logout-section" style="padding-right: 38px;"><a id="btn-logout" href="#"  data-toggle="popover" data-trigger="focus" data-placement="bottom"  style="color: white;">
		    <!-- <span class="glyphicon glyphicon-off" aria-hidden="true"></span> -->
		    <span><img src="{{ URL::asset('assets/images/ico_power-off.svg') }}" width="20" height="20"></span>
		    </a></p>
	   </div>

	    
	</div><!-- /.container-fluid -->
</nav>


<!-- sms model  nhr 2016-8-3-->
<style type="text/css">
	div.modal-content{ top: 100px; }
	p, .modal-title{ margin: 0 0 0 25px; }
	#content{ padding: 20px 0px 20px 40px; }
	.tf{ background: white !important;border: 1px solid #929292 !important; }
	.control-label { text-align: left !important }
	.modal-footer { padding: 15px 0px !important; margin: 0 85px 0 38px !important; }
	.btn {     background: #6fc9f5; border: none; }
	.font { font-family: "Montserrat", sans-serif; }
	#sms-mobile-codes { cursor: pointer }
	#sms-mobile-codes li:hover { cursor: pointer; background: #1997D4 !important; color: white !important;}
	.bt:hover { background: #6fc9f5 !important; }
</style>

<div id="sms-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title font">Medicloud SMS Platform</h4>
      </div>
      <div class="modal-body">
        <p class="font" id="error">Send SMS via Medicloud</p>
      </div>
      <div id="content">
      	
      <form class="form-horizontal">
      	
      	<div class="form-group">

		    <label for="name" class="col-sm-1 c control-label ">Name</label>
		    <div class="col-sm-8" >
		      <input type="text" id="name" class="form-control tf" placeholder="Sender's Name">
		    </div>
		</div>
		  </br>

		<div class="form-group">
		    <label for="doctor" class="col-sm-1 c control-label ">Phone</label>

		    <div id="code-dropdown" class="col-sm-2" >

				<input type="button" class="tf form-control input-sm mobile-input-code input-style dropdown-toggle" id="phone_code" data-toggle="dropdown" value="+65">

				<ul class="dropdown-menu" id="sms-mobile-codes" style="margin-left: 15px; width: 270px; max-height: 180px; overflow-y: auto; overflow-x: hidden;">
				</ul>
		    </div>


		    <div class="col-sm-5" >
		      <input type="text" id="phone" class="form-control tf" placeholder="Phone Number" style="width:264px">
		    </div>
		</div>  
		</br>

		<div class="form-group">
		    <label for="message" class="col-sm-1 c control-label ">Message</label>
		    <div class="col-sm-8" >
		       <textarea id="message"  class="tf form-control" style="height:100px !important" placeholder="Message"></textarea> 
		    </div>
		</div>

      </form>

      <span class="error font" style="margin-left: 78px; display:none"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary font bt" id="send_message">Send Message</button>
        <button type="button" class="btn btn-primary font bt" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>



<script type="text/javascript">
	
	$('#btn-logout').popover({

		html: 'true',
	    title : 'Are you sure ?',
	    content : '<a href="{{URL::to('app/auth/logout')}}" id="logout" class="btn" style="background: #1797D4; color: white;">Logout</a> <button class="btn" id="logout-cancel" style="    background: white; border: 1px solid #C9C9C9; color: black;">Cancel</button>'

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


<body style="font-family:'Open Sans', sans-serif;">