<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/jquery.validate.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<style>
   p{
       font-family: 'Lato', sans-serif;
       color: grey;
   }

</style>
<br><br><br><br>
<div class="container ">

<div class="section center-block" align="center">
   <div class="logo-container">
       <img src="{{ URL::asset('assets/images/mc-v2-logo.png') }}" width="261" height="52"  alt=""/>
   </div><!--END LOGO CONTAINER-->
</div><!--END SECTION RIGHT-->
       <br><br><br><br><br><br>
<p align="center">Thank you for connecting your google calendar, now your bookings will be in sync with google calendar,<br>if you wish to remove the calendar sync.
   Please go to Settings, Select Calendar Integration and Select<br> the doctor you wish to remove the sync and click on Remove Credentials
   </p><br><br><br><br>
<div align="center">
           <!-- <a href="http://ec2-54-255-185-218.ap-southeast-1.compute.amazonaws.com/nuclei_mc_r1/public/app/auth/login" id=""  type="submit" class="btn btn-primary  ">CLINIC LOGIN</a> -->
           <a href="{{ action('App_AuthController@MainLogin') }}" id=""  type="submit" class="btn btn-primary  ">CLINIC LOGIN</a>
</div>
</div>


