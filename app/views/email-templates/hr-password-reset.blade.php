<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
<head>
<meta charset="utf-8">
<title>Medicloud-Confirmation</title>
<link href='https://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
</head>


<style type="text/css">@media only screen and (max-width: 640px){
    body body{width:auto!important;}
    body table[class=full] {width: 100%!important; clear: both; }
    body table[class=mobile] {width: 100%!important; padding-left: 30px; padding-right: 30px; clear: both; }
    body table[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
    body td[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
    body .erase {display: none;}
    body .buttonScale {float: none!important; text-align: center!important; display: inline-block!important; clear: both;}
    body .buttonScale2 {float: none!important; text-align: center!important; vertical-align: bottom!important; height: 0px!important; padding-left: 10px!important; padding-right: 10px!important; display: inline-table!important; padding-bottom: 7px!important; clear: both;}
    body .image515 img {width: 100%!important;}
    body .break {display: block!important;}
    body td[class=pad20] {padding-left: 20px!important; padding-right: 20px!important; text-align: center!important; clear: both; }
    body *[class=h10] {width: 100%!important; height: 10px!important;}
    body *[class=h20] {width: 100%!important; height: 20px!important;}
    body *[class=h40] {width: 100%!important; height: 40px!important;}
    body *[class=h50] {width: 100%!important; height: 40px!important;}
    body *[class=h30] {width: 100%!important; height: 30px!important;}
    body table[class=sponsor] {text-align:center; float:none; width:360px;}
    body table[class=mcenter] {text-align:center; vertical-align:middle; clear:both!important; float:none; margin: 0px!important;}
    body table[class=table33] {width: 33%!important; text-align: center!important; }
    body .image197 img {width: 100%!important;}
    body .image560 img {width: 100%!important;}

}</style>

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
<div id="medi-emailtemplate-wrapper" style="width:90%; background-color: #CCC; margin-top:3%; margin-bottom:3%; margin-left:auto; margin-right:auto;  ">


  <!--header wraper-->
  <div id="medi-header-wrapper">

        <div id="header-line" style="height:10px; background-color:#72d0f6;">
        </div>

            <div id="header-logo-container" style="background-color:#1868ad;">
                <img style="display:block; margin:0px; padding:0px" src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/Email-header-Banner.jpg" width="100%"    alt=""/>
    </div>
  </div>
  <!--end of header wraper-->






  <!--detail-container-->
  <div id="detail-container" style=" padding:20px 15px; background-color:#fff; " >

       <div id="title-name" style=" font-size:15px; font-family: 'Varela Round', sans-serif; padding-bottom:10px;"  >
            Hello There,
    </div>

             <div id="title-detail" style=" word-wrap:break-word;" >
             Do you want to reset your password? <br>
              if so please click on the link below and enter a new password.
            </div>
  </div>


  <!--end of detail-container-->




   <!--booking-detail-->
  <div id="booking-detail" style=" padding:20px 15px; background-color:#f8f6f6; font-family: 'Varela Round', sans-serif; " >

    <p><b>Login Email Address: {{ $emailTo }}</b></p>
    <p><b>Login Password: {{ $password }}</b></p>

    <div id="clear" style="clear:both;"></div>
  </div>
   <!--end of booking-detail-->







   <!--detail-container-->
  <div id="detail-container" style=" padding:20px 15px; background-color:#fff; " >
    <!-- <div id="title-detail" style=" font-size:14px; word-wrap:break-word; padding-bottom:10px;" >
                 In the case that any issue arises with your booking, our team will inform you.
    </div> -->

    <div id="title-detail" style=" font-size:14px; word-wrap:break-word; padding-bottom:10px;" >
                If it wasn't you, then please report to <span style="color:#156aaf;"><a style="text-decoration:none;" href="mailto:support@mednefits.com">support@mednefits.com</a></span>.

    <div id="title-detail" style=" font-size:14px; word-wrap:break-word; padding-bottom:10px;" >
              Thank you, <br />
      <strong>Your Mednefits Team </strong> </div>
  </div>
  <!--end of detail-container-->




  <!--app-banner-->
  <div id="app-banner" style=" position:relative;">
          <div id="banner-large" style=" position:relative;">
          <a href="http://onelink.to/pyxjqg">
          <img style="display:block;" src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/Email-Footer-Banner.jpg" width="100%"  alt=""/>
          </a>
          </div>

    </div>
    <!--end of app-banner-->




 <!--footer contacts-->
 <div id="footer-contact" style="text-align:center; background-color:#fff; padding-top:50px; padding-bottom:0px;">

   <div id="bluelogo" style="text-align:center">
   <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/mednefits+logo+v3+(blue-box)+LARGE.png" alt="" border="0" style="width: 40px;height: 40px;">
   </div>

    <div id="contact-detail" style="padding-left:18px; font-family: 'Varela Round', sans-serif; color:#9c9c9c; padding-bottom:20px;">
       <span> +65 3163 5403 </span>
       <span> +60 330 995 774 </span>
       <span>
           <a style="text-decoration:none;  color:#9c9c9c; border-left: 1px solid; margin-left: 3px; padding-left: 6px;" href="mailto:support@mednefits.com">support@mednefits.com
           </a>
       </span>
       </div>

    <div id="footer-social" style="text-align:center; padding-bottom:0px; margin-left: auto; margin-right: auto; padding-bottom: 20px; text-align: center; width: 162px; ">
      <div class="social-icn" style=" width:50px; height:50px; float: left;  margin-right:2px;">
          <a href="https://www.facebook.com/Mednefits">
            <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/Facebook.png" width="50" height="50"  alt=""/>
          </a>
      </div>
      <div class="social-icn" style=" width:50px; height:50px; float: left;  margin-right:2px;">
          <a href="https://www.instagram.com/mednefits">
              <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/Instagram.png" width="50" height="50"  alt=""/>
          </a>
      </div>

      <div class="social-icn" style=" width:50px; height:50px; float: left;  margin-right:2px;">
          <a href="https://www.linkedin.com/company/medneﬁts">
           <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/Linkedin.png" width="50" height="50"  alt=""/>
          </a>
      </div>
      <div id="clear" style="clear:both;"></div>
    </div>
 </div>
<!--end of footer contacts-->




  <!--footer-->
  <div id="footer" style="text-align:center; font-family: 'Varela Round', sans-serif; color:#9c9c9c; background-color:#fff;">
    <div class="footer-unsubscribe" style=" font-size:12px;">You can unsubscribe by clicking the link below</div>
    <div class="footer-unsubscribe" style=" font-size:12px;">
    <a style="text-decoration:none; color:#9c9c9c;" href="mailto:info@medicloud.sg">Unsubscribe</a> &#124;
    <a style="text-decoration:none; color:#9c9c9c; " href="https://s3-ap-southeast-1.amazonaws.com/mednefits/terms.html">Terms of use</a>
    &#124;
    <a style="text-decoration:none; color:#9c9c9c; " href="https://s3-ap-southeast-1.amazonaws.com/mednefits/privacy-policy.html"> Privacy Policy</a></div>
  </div>
  <!--end of footer-->




</div>
<!--end of template wraper-->
</body>
</html>
