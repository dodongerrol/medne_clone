<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta charset="UTF-8">
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='-1'>
<meta http-equiv='pragma' content='no-cache'>
<title><?php echo $title;?></title>
<link href='https://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<link rel="shortcut icon" href="{{ URL::asset('assets/new_landing/images/favicon.ico') }}" type="image/ico">
{{ HTML::style('assets/css/medicloudv3.css') }}

{{ HTML::style('assets/css/13inch-macpro.css') }}
{{ HTML::style('assets/css/macpro-fix.css') }}
{{ HTML::style('assets/css/medi-ipad2.css') }}
{{ HTML::style('assets/css/medi-ipad-mini.css') }}

<!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.min.js"></script>
{{ HTML::script('assets/js/popup/jquery.reveal.js') }}-->
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>-->

<!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.min.js"></script>
{{ HTML::style('assets/js/popup/reveal.css') }}
{{ HTML::script('assets/js/popup/jquery.reveal.js') }}-->


{{ HTML::script('assets/js/jquery-1.11.1.js') }}
{{ HTML::script('assets/js/jquery-ui.js') }}


{{ HTML::style('assets/css/datepicker-appointment.css') }}

{{ HTML::script('assets/js/clinic-section-ajax.js') }}
{{ HTML::script('assets/js/jquery-blockUI.js') }}
{{ HTML::script('assets/js/form-validate.js') }}
{{ HTML::script('assets/js/common-validation.js') }}

{{ HTML::script('assets/admin/moment-with-locales.js') }}
{{ HTML::script('assets/admin/bootstrap-datetimepicker.js') }}
{{ HTML::style('assets/admin/bootstrap-datetimepicker.css') }}

{{ HTML::style('assets/css/jquery-ui.css') }}

<!-- Temporarly desabled -->
<!--<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">-->

{{ HTML::script('assets/js/jquery-timepicker/jquery.timepicker.js') }}
{{ HTML::style('assets/js/jquery-timepicker/jquery.timepicker.css') }}

{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
{{ HTML::script('assets/js/nric-validation.js') }}
<!--{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}-->


{{ HTML::script('assets/js/scroll/jquery.mCustomScrollbar.concat.min.js') }}
{{ HTML::style('assets/js/scroll/jquery.mCustomScrollbar.css') }}

{{ HTML::style('assets/js/jspopup/style.css') }}



</head>

<body>
<div id="main-container">
  
  <div class="header">
  
    <div class="section-left">
      <div class="logo-container">
        <img src="{{ URL::asset('assets/images/Mednefits Logo V2.svg') }}" width="261" height="52"  alt=""/>
      </div><!--END LOGO CONTAINER--> 
    </div><!--END SECTION RIGHT-->
    
   <div class="section-right">
     <div id="main-nav">
     	<ul>
     		<li><a href="{{URL::to('app/clinic/appointment-home-view')}}">HOME</a></li>
<!--            <li><a href="#">ANALYTICS</a></li>-->
            <li class="active"><a href="{{URL::to('app/clinic/clinic-details')}}">SETTINGS</a></li>
            <li><a href="{{URL::to('app/auth/logout')}}">LOGOUT</a></li>
          <div class="clear"></div>
        </ul>
     </div><!--END OF -->
   </div><!--END SECTION RIGHT-->
    
  </div><!--END HEADER-->