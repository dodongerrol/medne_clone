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
<link rel="shortcut icon" href="{{ URL::asset('assets/new_landing/images/favicon.ico') }}" type="image/ico">
{{ HTML::style('assets/css/medicloud.css') }}
{{ HTML::style('assets/css/medicloud-ipad.css') }}
{{ HTML::style('assets/css/mob.css') }}
{{ HTML::style('assets/css/date-picker.css') }}

{{ HTML::script('assets/js/jquery-1.11.1.js') }}
{{ HTML::script('assets/js/doctor-form-validation.js') }}
{{ HTML::script('assets/js/clinic-ajax.js') }}
{{ HTML::script('assets/js/form-validate.js') }}
{{ HTML::script('assets/js/jquery-blockUI.js') }}

{{ HTML::script('assets/js/clinic-js-v1.js') }}

{{ HTML::style('assets/common/sinkin-sans-fontfacekit/web fonts/sinkinsans_300light_macroman/stylesheet.css') }}
<!--{{ HTML::style('assets/css/random-display.css') }}-->
<link href='https://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<style type="text/css">
/*@import url("sinkin-sans-fontfacekit/web fonts/sinkinsans_300light_macroman/stylesheet.css");*/
body,td,th,input,textarea {font-family: 'sinkin_sans300_light';}
.mc-btn-booknow, .mc-btn-stop { font-family: 'Montserrat', sans-serif;}

</style>
</head>

<body class="mc-bg-white">
<div class="mc-container">  <!--MC CONTAINER (MAIN)-->
  <div class="mc-background-color"><!--MC BACKGROUND COLOR -->
 
 
  <!--HEADER START-->
    <div class="mc-header mc-res-width">
      <div class="mc-logo-container mc-fl"><img src="{{ URL::asset('assets/images/mednefits logo v3 (white) LARGE.png') }}" width="197" height="43" alt="medicloud-logo" longdesc="images/mednefits logo v3 (white) LARGE.png"></div>
        <div class="mc-main-menu ">
        <div class=" mc-fr ">{{ HTML::link('/app/auth/logout', 'Logout','class="mc-btn-logout"')}}</div>
         <ul >
<!--          <li>HOME</li>
          <li>DASHBOARD</li>
          <li><a href="">SETTINGS</a></li>-->
          <!--<li>{{ HTML::link('/app/clinic/booking', 'HOME','class="a"')}}</li>-->
          <li>{{ HTML::link('/app/clinic/settings-dashboard', 'HOME','class="a"')}}</li>
<!--          <li>{{ HTML::link('/app/clinic/dashboard', 'DASHBOARD','class="a"')}}</li>-->
          <li>{{ HTML::link('/app/clinic/manage-doctors', 'SETTINGS','class="a"')}}</li>
          </ul>
          
          </div>
          
        <div class="clear"></div>
    </div>
   <!--HEADER END-->   