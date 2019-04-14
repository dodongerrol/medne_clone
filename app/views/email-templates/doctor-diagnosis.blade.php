<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Booking Complete</title>
<style>
@media only screen 
and (min-device-width : 320px) 
and (max-device-width : 480px) { 

#breef{ font-size:11px !important; width:100% !important;} 
#logo{ width:50% !important; }
#btn{ width:200px !important;}
#btn_find{ padding-top:5%  !important; margin-bottom:5%  !important;}
#address{ font-size:12px !important; }
.tbl-c{ margin-left:0px !important;  margin-top:10px !important;}
.clr{ height:10px !important;}
.tbl-r{ font-size:.9em !important;}
}
a{ color:#fff !important}
</style>


</head>

<body style="padding:0; margin:0;">
<div style=" background-color:#188fc8; max-width:749px; min-width:320px; margin:0% auto; text-align:left; "><!--Email template container -->
  <div style="background-color:#e51e25; height:5px;"></div>
<div id="email_header">
    <div id="logo" style="padding-left:10%; padding-top:40px; width:30%;"><img src="http://medicloud.sg/medicloud_web/public/assets/images/etp_logo_c.png" width="100%"   alt=""/></div>
  </div>
  
  
  
  <div id="email_container" style="text-align:left; padding-left:10px;">
    <div id="detail" style="width:56%; padding-left:10%; padding-top:4.0%; float:left;">
      <div id="title" style=" font-size: 4vw; color:#ffffff; font-style:bold;   font-family: Arial, sans-serif; font-weight:bold;">Thanks!</div>
      <br /> 
      <div id="breef" style="  padding-top:4.0%; font-size:.9em; color:#ffffff; font-family: Arial, sans-serif; line-height:1.7">Your appointmnet is concluded. Hope you enjoyed <br /> 
our service. Given below is the summary of your <br /> 
appointment


     
 <br /> 
 <br />
 


 </div>
      
     <br /> 
 <br />
 <div id="table-wrapper">
   <div class="tbl-c" style="float:left;">
     <div style="color:#004d71; font-family: Arial, sans-serif; font-size:12px; ">CUSTOMER NAME</div>
     <div class="tbl-r" style="color:#fff; font-family: Arial, sans-serif; font-size:17px; width:100%;" ><?php echo $pateintName;?></div>
   </div>
   <div class="tbl-c"  style="float:left; margin-left:50px; ">
     <div style="color:#004d71; font-family: Arial, sans-serif; font-size:12px; ">DATE & TIME</div>
     <div class="tbl-r" style="color:#fff; font-family: Arial, sans-serif; font-size:17px; width:100%;" ><?php echo $bookingDate;?></div>
   </div>
   
   <div class="clr" style="clear: both; height:50px;"></div>
<div class="tbl-c"  style="float:left;">
  <div style="color:#004d71; font-family: Arial, sans-serif; font-size:12px; "> CLINIC NAME</div>
     <div class="tbl-r" style="color:#fff; font-family: Arial, sans-serif; font-size:17px;" ><?php echo $clinicName;?></div>
 </div>
   <div class="tbl-c"  style="float:left; margin-left:50px;">
     <div style="color:#004d71; font-family: Arial, sans-serif; font-size:12px; ">LOCATION</div>
     <div class="tbl-r" style="color:#fff; font-family: Arial, sans-serif; font-size:17px;" ><?php echo $clinicAddress;?></div>
   </div>
 
 

 <div class="clr" style="clear: both; height:50px;"></div>
 <div class="tbl-c"  style="float:left;">
  <div style="color:#004d71; font-family: Arial, sans-serif; font-size:12px; ">DOCTOR NAME</div>
     <div class="tbl-r" style="color:#fff; font-family: Arial, sans-serif; font-size:17px;" ><?php echo $doctorName;?></div>
 </div>  
   
 </div>
 



 
 
 
 
 
 
 
 
 
    </div>
    <div id="pic_girl" style="float:left; width:34%; margin-top:-20px;"><img src="http://medicloud.sg/medicloud_web/public/assets/images/pic_girl.png" width="100%" alt=""/></div>
  </div>
  
  
  <div style="clear:both; margin-top:20px; height:90px;"></div>
<div style="text-align:center;" ><img src="http://medicloud.sg/medicloud_web/public/assets/images/etp_logo_small_c.png" width="71" height="45"  alt=""/></div>
  
  <div id="address" style="font-size:1em;  font-family: Arial, sans-serif; text-align:center; line-height:1.4; padding-bottom:20%;"> <span style="color:#004d71;" >Medicloud Pte Ltd</span><br /> 
<span style="color:#ffffff;" >1 Temasek Boulevard #18-02, Suntec Tower One, Singapore 038987<br />
 <br /> 

Need help call us on <span style="color:#004d71;">+65 6590 4608</span><br /> 
<a href="#" style="color:#fff !important; text-decoration:none;">www.medicloud.sg </a>| <a href="#" style="color:#fff !important; text-decoration:none;">hello@medicloud.sg</a></span><br />  </div>
</div>
<!--End of Email template container -->
</body>
</html>





