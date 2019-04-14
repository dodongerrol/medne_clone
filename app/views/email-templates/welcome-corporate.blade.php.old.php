<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
<head>
<meta charset="utf-8">
<title>Medicloud-Confirmation</title>
<link href='https://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
</head>

<style type="text/css">@media only screen and (max-width: 479px){ 
    body body{width:auto!important;}
    body table[class=full] {width: 100%!important; clear: both; }
    body table[class=mobile] {width: 100%!important; padding-left: 20px; padding-right: 20px; clear: both; }
    body table[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
    body td[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
    body .erase {display: none;}
    body .buttonScale {float: none!important; text-align: center!important; display: inline-block!important; clear: both;}
    body .buttonScale2 {width: 100%!important; text-align: center!important; vertical-align: middle!important; height: 0px!important; padding-left: 0px!important; padding-right: 0px!important; padding-bottom: 5px!important; padding-top: 5px!important; clear: both;}
    body .eraseMob {display: none!important;}
    body .font30 {font-size: 30px!important; line-height: 34px!important;}
    body .image310 img {width: 100%!important;}
    body .image515 img {width: 100%!important;}
    body .image275 img {width: 100%!important; text-align: center!important; clear: both; }
    body td[class=pad20] {padding-left: 20px!important; padding-right: 20px!important; text-align: center!important; clear: both;}
    body .break {display: block!important;}
    body table[class=mcenter] {text-align:center; vertical-align:middle; clear:both!important; float:none; margin: 0px!important;}
    body *[class=h10] {width: 100%!important; height: 10px!important;}
    body *[class=h20] {width: 100%!important; height: 20px!important;}
    body *[class=h30] {width: 100%!important; height: 30px!important;}
    body *[class=h40] {width: 100%!important; height: 40px!important;}
    body table[class=sponsor] {text-align:center; float:none; width:260px;}
    body table[class=mcenter2] {text-align:center; vertical-align:middle; clear:both!important; float:none; margin: 0px!important;}
    body table[class=table33] {width: 100%!important; text-align: center!important; clear: both; }
    body .image197 img {width: 100%!important;}
    body .image560 img {width: 100%!important;} 
    body .image226 img {width: 100%!important;}
    


}
</style>

<body>



<!--template wraper-->
<div id="medi-emailtemplate-wrapper" style="width:90%; border: 1px solid #CCC; margin-top:3%; margin-bottom:3%; margin-left:auto; margin-right:auto;  ">
  
 
  <!--header wraper-->
  <div id="medi-header-wrapper">
    
        <div id="header-line" style="height:10px; background-color:#72d0f6;">
        </div>
    
            <div id="header-logo-container" style="background-color:#1868ad;">
                <img style="display:block; margin:0px; padding:0px" src="https://www.medicloud.sg/e-template-img/e-template-header.jpg" width="100%"    alt=""/> 
    </div>  
  </div>
  <!--end of header wraper-->
  
  
 
 
 
 
  <!--detail-container-->
  <div id="detail-container" style=" padding:40px; background-color:#fff;    padding-bottom: 10px; " >
  
        <div id="title-name" style=" font-size:20px; font-family: 'Open Sans', sans-serif; padding-bottom:10px; font-weight: 700"  >
            <b>WELCOME TO MEDICLOUD !</b>
      </div>
    <br>
             <div id="title-detail" style="font-size: 18px; word-wrap:break-word; font-family: 'Open Sans'" >

             Hi {{$data->first_name}},
             <br>
             <br>
              
              We are excited to have you on-board. As your company have recently joined Medicloud to provide employees health and wellness benefits, this email serves as a confirmation that you are now successfully registered with us.
              <br><br>
              Registerd Emaill :  {{$emailTo}}<br> 
              Password : {{$pass}}<br>
              Medi-Credit: <b>{{$data->credit}}</b>
              <br><br>
              Medi-Credit will act as an e-voucher for you to spend on any health and wellness services listed in our platform.<br>
              Download our app today to access your Medicloud - health and wellness benefits on the go.
              <br><br>
              If you have any questions,feel free to contact us at <span style="color:#156aaf;"><a style="text-decoration:none;" href="mailto:info@medicloud.sg">info@medicloud.sg</a></span>, we are always happy to here from you!

            </div>
  </div>
  <!--end of detail-container-->
  
  
  
  
   <!--booking-detail-->
  <!-- <div id="booking-detail" style=" padding:20px 15px; background-color:#f8f6f6; font-family: 'Open Sans', sans-serif; " >
     
    
     
    <div id="clear" style="clear:both;"></div>
  </div> -->
   <!--end of booking-detail-->
   
   
   
   
   
   
   
   <!--detail-container-->
  <div id="detail-container" style="  background-color:#fff; " >
    <!-- <div id="title-detail" style=" font-size:14px; word-wrap:break-word; padding-bottom:10px;" >
                 In the case that any issue arises with your booking, our team will inform you. 
    </div> -->
      
    <div id="title-detail" style=" font-size: 18px; word-wrap:break-word; padding-bottom:10px;padding:20px 40px;" >
               <!--  If it wasn't you, then please report to <span style="color:#156aaf;"><a style="text-decoration:none;" href="mailto:info@medicloud.sg">info@medicloud.sg</a></span>. -->
      
    <div id="title-detail" style=" font-size: 18px; word-wrap:break-word; padding-bottom:10px;font-family: 'Open Sans', sans-serif;" >
              Thank you, <br />
      <strong>Your Medicloud Team </strong> </div>
  </div>
  <!--end of detail-container-->
   
   
   
  
  <!--app-banner-->
  <div id="app-banner" style=" position:relative;">
          <div id="banner-large" style=" position:relative;">
          <img style="display:block;" src="https://www.medicloud.sg/e-template-img/e-template-app-banner.jpg" width="100%"  alt=""/> 
          </div>
 
    </div>
    <!--end of app-banner-->
  
  
  
  
 <!--footer contacts--> 
 <div id="footer-contact" style="text-align:center; background-color:#fff; padding-top:50px; padding-bottom:0px;">
 
   <div id="bluelogo" style="text-align:center">
   <img src="https://www.medicloud.sg/e-template-img/blue-logo.jpg" width="50" height="30"  alt=""/>

   </div>
   <br>
    <div id="contact-detail" style="padding-left:18px; font-family: 'Open Sans', sans-serif; color:#9c9c9c; padding-bottom:0px;"> 
       <span> +65 6635 8374 </span> 
       <span> 
           <a style="text-decoration:none;  color:#9c9c9c; border-left: 1px solid; margin-left: 3px; padding-left: 6px;" href="mailto:info@medicloud.sg">        info@medicloud.sg
           </a>
       </span> 
       </div>
    <div style="font-family: 'Open Sans', sans-serif; color:#9c9c9c; padding-bottom:20px;">10 Anson Road, International Plaza #21-05,S079903, Singapore</div>  
       
    <div id="footer-social" style="text-align:center; padding-bottom:0px; margin-left: auto; margin-right: auto; padding-bottom: 20px; text-align: center; width: 162px; ">
      <div class="social-icn" style=" width:50px; height:50px; float: left;  margin-right:2px;"> 
          <a href="https://www.facebook.com/medicloudsg/">
            <img src="https://www.medicloud.sg/e-template-img/Facebook.png" width="50" height="50"  alt=""/> 
          </a> 
      </div>
      <div class="social-icn" style=" width:50px; height:50px; float: left;  margin-right:2px;"> 
          <a href="https://www.instagram.com/">
              <img src="https://www.medicloud.sg/e-template-img/Instagram.png" width="50" height="50"  alt=""/>
          </a> 
      </div>
     
      <div class="social-icn" style=" width:50px; height:50px; float: left;  margin-right:2px;"> 
          <a href="http://bit.ly/1Uh2Q2s">
           <img src="https://www.medicloud.sg/e-template-img/Linkedin.png" width="50" height="50"  alt=""/> 
          </a> 
      </div>
      <div id="clear" style="clear:both;"></div>  
    </div>
 </div>
<!--end of footer contacts--> 
 
 
  
  
  <!--footer--> 
  <div id="footer" style="text-align:center; font-family: 'Open Sans', sans-serif; color:#9c9c9c; background-color:#F5F5F5;padding:40px 0;">
    <div class="footer-unsubscribe" style=" font-size: 15px;">You can unsubscribe by clicking the link below</div>
    <div class="footer-unsubscribe" style=" font-size: 16px;margin-top: 5px;font-weight: 700">
    <a style="text-decoration:none; color:#444;" href="mailto:info@medicloud.sg">Unsubscribe</a> &#124; 
    <a style="text-decoration:none; color:#444; " href="https://www.medicloud.sg/terms.html">Terms of use</a> 
    &#124;
    <a style="text-decoration:none; color:#444; " href="https://www.medicloud.sg/privacy-policy.html"> Privacy Policy</a></div>
  </div>
  <!--end of footer--> 
  
  
  
  
</div>
<!--end of template wraper-->
</body>
</html>