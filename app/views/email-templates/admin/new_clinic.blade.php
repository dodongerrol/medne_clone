<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Add clinic</title>
<style>
@media only screen 
and (min-device-width : 320px) 
and (max-device-width : 480px) { 

#breef{ font-size:11px !important;} 
#logo{ width:50% !important; }
#btn{ width:200px !important;}
#btn_find{ padding-top:5%  !important; margin-bottom:5%  !important;}
#address{ font-size:12px !important; }
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
    <div id="detail" style="width:50%; padding-left:10%; padding-top:4.0%; float:left;">
      <div id="title" style=" font-size: 4vw; color:#ffffff; font-style:bold;   font-family: Arial, sans-serif; font-weight:bold;">Hello!</div>
      <br /> 
      <div id="breef" style="  padding-top:4.0%; font-size:.9em; color:#ffffff; font-family: Arial, sans-serif; line-height:1.7">Welcome! and Thank you for registering with us. <br /> 
Medicloud is built to make healthcare better.  <br /> <br /> 

Our clinic system helps you better manage your 
appointment and increase patient satisfaction. <br /> 
Given below is your access credentials 
     
 <br /> 
 <br />

<div style="float:left; color:#004d71;">Name:</div> <div style="float:left; padding-left:84px;"><?php echo $emailName; ?> </div><br />
<div style="float:left; color:#004d71;">Email: </div><div style="float:left; padding-left:86px;"><?php echo $email; ?></div><br />
<div style="float:left; color:#004d71;">Password: </div><div style="float:left; padding-left:59px;"><?php echo $password; ?></div><br />


 </div>
 
      
    </div>
    <div id="pic_girl" style="float:left; width:35%; margin-top:-20px;"><img src="http://medicloud.sg/medicloud_web/public/assets/images/pic_girl.png" width="100%" alt=""/></div>
  </div>
  
  
  <div id="btn_find" style="clear:both; padding-top:10%; text-align:center;  margin-bottom:67px;">
    <div id="btn" style=" font-size:20px; padding-top:2%; padding-left:4%; padding-right:4%;  padding-bottom:2%; background-color:#004d71; width:210px; text-align:center; margin:0% auto; -webkit-border-radius: 71px; -moz-border-radius: 71px; border-radius: 71px; color:#fff; font-family: Arial, sans-serif;">Clinic Login</div>
  </div>
  
  
  <div style="text-align:center;" ><img src="http://medicloud.sg/medicloud_web/public/assets/images/etp_logo_small_c.png" width="71" height="45"  alt=""/></div>
  
  <div id="address" style="font-size:1em;  font-family: Arial, sans-serif; text-align:center; line-height:1.4; padding-bottom:20%;"> <span style="color:#004d71;" >Medicloud Pte Ltd</span><br /> 
<span style="color:#ffffff;" >70 Anson Road, Hub Synergy Point B1​, S079905<br /> 
 <br /> 

Need help call us on <span style="color:#004d71;">+65 6590 4608</span><br /> 
<a href="#" style="color:#fff !important; text-decoration:none;">www.medicloud.sg </a>| <a href="#" style="color:#fff !important; text-decoration:none;">hello@medicloud.sg</a></span><br />  </div>
</div>
<!--End of Email template container -->
</body>
</html>











