<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Bookings</title>
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
<div style=" background-color:#188fc8; max-width:749px; min-width:320px; margin:0% auto; text-align:left;"><!--Email template container -->
  <div id="email_header">
    <div id="logo" style="padding-left:10%; padding-top:40px; width:30%;"><img src="https://res.cloudinary.com/www-medicloud-sg/image/upload/v1428563754/vxnxvm6udt9nhqp9f0vo.png" width="100%"   alt=""/></div>
  </div>
  
  
  
  <div id="email_container" style="text-align:left; padding-left:10px;">
    <div id="detail" style="width:50%; padding-left:10%; padding-top:6.0%; float:left;">
      <div id="title" style=" font-size: 5.9vw; color:#ffffff;  font-family: Arial, sans-serif; font-weight:bold;">Hello! <?php echo $emailName;?></div>
      <div id="breef" style=" font-size:.8em; color:#ffffff; font-family: Arial, sans-serif; line-height:1.7">Please check the login credentials below. <br /> 
      <?php echo $email;?><br>
      <?php echo $password;?>
 <br /> 
 <br />

 </div>
 
      
    </div>
    <div id="pic_girl" style="float:left; width:35%;"><img src="https://res.cloudinary.com/www-medicloud-sg/image/upload/v1428563629/ixqj6v482pp1t5zsvato.png" width="100%" alt=""/></div>
  </div>
  
  <div style="text-align:center;" ><img src="https://res.cloudinary.com/www-medicloud-sg/image/upload/v1428563683/nk6td2itti4txjy8gvkt.png" width="316" height="41"  alt=""/></div>
  
  <div id="address" style="font-size:1em;  font-family: Arial, sans-serif; text-align:center; line-height:1.4"> <span style="color:#004d71;" >Medicloud Pte Ltd</span><br /> 
<span style="color:#ffffff;" >1 Temasek Boulevard #18-02<br /> Suntec Tower One<br /> Singapore 038987<br /> <br /> 

Need help call us on 8157 1537<br /> 
<a href="#" style="color:#fff !important; text-decoration:none;">www.medicloud.sg </a>| <a href="#" style="color:#fff !important; text-decoration:none;">hello@medicloud.sg</a></span><br />  </div>
</div>
<!--End of Email template container -->
</body>
</html>
