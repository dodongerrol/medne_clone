<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Booking</title>
<style>
@media only screen 
and (min-device-width : 320px) 
and (max-device-width : 480px) { 

#breef{ font-size:11px !important; width:100% !important;} 
#logo{ width:50% !important; }
#btn{ width:200px !important;}
#btn_find{ padding-top:5%  !important; margin-bottom:5%  !important;}
#address{ font-size:12px !important; }
.pd-control{  padding-left:10px !important;}
.pd-c2{  margin-left:11px !important;}
#table { font-size:12px; color:#fff; padding-left:14%;}
#pic_girl{ position:absolute !important; top:90px; }
#dr{ width: 205px;}
#footer{ height:0px !important; margin-top:30px !important; }
.bg{ background-position: 206px 53px !important;
    background-repeat: no-repeat !important;
    background-size: 40% 55% !important;}
}
a{ color:#fff !important}
</style>


</head>

<body style="padding:0; margin:0; background-color:#188fc8;">
<div class="bg" style=" background-color:#188fc8; max-width:749px; min-width:320px; margin:0% auto; text-align:left; position:relative;  "><!--Email template container -->
  <div style="background-color:#e51e25; height:5px;"></div>
<div id="email_header">
    <div id="logo" style="padding-left:10%; padding-top:40px; width:30%;">
   <img src="http://medicloud.sg/medicloud_web/public/assets/images/etp_logo_c.png" width="100%"   alt=""/>
    </div>
  </div>
  
  
  
  <div id="email_container" style="text-align:left; padding-left:10px;">
    <div id="detail" style="width:90%; padding-left:10%; padding-top:4.0%; float:left;">
      <div id="title" style=" font-size: 4vw; color:#ffffff; font-style:bold;   font-family: Arial, sans-serif; font-weight:bold;">Hello!</div>
      <br /> 
      <div id="breef" style="  padding-top:4.0%; font-size:.9em; color:#ffffff; font-family: Arial, sans-serif; line-height:1.7">
     
      Thank you for making your clinic reservation 
through Medicloud. <br /> You will be receiving timely  
push-notifications to your mobile app,<br /> alerting
you with timely updates of your reservation.<br />  
<br />

Here’s your booking details;
     
 <br /> 
 <br />
 </div>
 
      
    </div>
	<div class="clear"  style="clear:both;"></div>    


    <!--<div id="pic_girl" style="float:left; width:34%; margin-top:-20px; position:absolute; right:0%; top:10%;">
    <img style="position:absolute; z-index:-99;" src="http://dev.medicloud.sg/medicloud_v001/public/assets/images/pic_girl.png"  width="100%"  alt=""/>
    </div>-->
  </div>
  
  
  
  <div id="table" style="padding-left:11.5%;">
      <?php if($bookingTime !=0){ ?>
      <div style="float:left; color:#004d71; line-height: normal;">Time:</div>
     	 <div class=" pd-control" style="float:left; padding-left:10px; color:#fff;  font-family: Arial, sans-serif;"><?php echo $bookingTime;?></div>
      <div class="clear"  style="clear:both;"></div>
      <?php } else{ ?>
      <div style="float:left; color:#004d71; line-height: normal;">Queue:</div>
     	 <div class=" pd-control" style="float:left; padding-left:10px; color:#fff;  font-family: Arial, sans-serif;"><?php echo $bookingNo;?></div>
      <div class="clear"  style="clear:both;"></div>
      <?php } ?>
       <div style="float:left; color:#004d71;">Date:</div> 
           <div class=" pd-control pd-c2" style="float:left; padding-left:10px; color:#fff; font-family: Arial, sans-serif;"><?php echo $bookingDate;?></div>
             <div class="clear"  style="clear:both;"></div>

		<div style="float:left; color:#004d71;">Doctor: </div>
			<div class=" pd-control" style="float:left; padding-left:10px; color:#fff; font-family: Arial, sans-serif;"><?php echo $doctorName;?></div>
			  <div class="clear"  style="clear:both;"></div>

		<div style="float:left; color:#004d71;">Clinic: </div>
			<div class=" pd-control" style="float:left; padding-left:10px; color:#fff; font-family: Arial, sans-serif;"><?php echo $clinicName;?></div>
				<div class="clear"  style="clear:both;"></div>

		<div style="float:left; color:#004d71;">Address: </div>
			<div id="dr"  class=" pd-control" style="float:left; padding-left:10px; color:#fff; font-family: Arial, sans-serif;"><?php echo $clinicAddress;?></div>
			<div class="clear"  style="clear:both;"></div>

 </div><!--end table-->
  
  
 
 
 
 
 
 
  <div id="footer" style="clear:both; margin-top:20px; height:90px;"><!--footer start-->
<div style="text-align:center;" ><img src="http://medicloud.sg/medicloud_web/public/assets/images/etp_logo_small_c.png" width="71" height="45"  alt=""/></div>
  
  <div id="address" style="font-size:1em;  font-family: Arial, sans-serif; text-align:center; line-height:1.4; padding-bottom:20%;"> <span style="color:#004d71;" >Medicloud Pte Ltd</span><br /> 
<span style="color:#ffffff;" >1 Temasek Boulevard #18-02, Suntec Tower One, Singapore 038987<br />
 <br /> 

Need help call us on <span style="color:#004d71;">+65 6590 4608</span><br /> 
<a href="#" style="color:#fff !important; text-decoration:none;">www.medicloud.sg </a>| <a href="#" style="color:#fff !important; text-decoration:none;">hello@medicloud.sg</a></span><br />  </div>
</div><!--end footer-->

</div>

<!--End of Email template container -->
</body>
</html>





