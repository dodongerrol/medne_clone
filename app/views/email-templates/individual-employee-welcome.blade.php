<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Mednefits-Confirmation</title>
<link href='https://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
</head>



<body style="font-family: sans-serif;">


<!--template wraper-->
<div id="medi-emailtemplate-wrapper" style="width:90%; background-color: #CCC; margin-top:3%; margin-bottom:3%; margin-left:auto; margin-right:auto;  ">
  
 
  <!--header wraper-->
  <div id="medi-header-wrapper">
    <div id="header-line" style="height:10px; background-color:#72d0f6;"></div>
    <div id="header-logo-container" style="background-color:#1868ad;">
      <img style="display:block; margin:0px; padding:0px" src="https://www.medicloud.sg/e-template-img/Email-header-Banner.jpg" width="100%"    alt=""/> 
    </div>  
  </div>
  <!--end of header wraper-->
  
 
  <!--detail-container-->
  <div id="detail-container" style="background-color:#fff;padding-top: 20px;padding-bottom: 20px;">
    <h2><b>We are happy to have your company on-board!</b></h2>
  </div>
  <div id="detail-container" style="padding-top: 20px;padding-bottom: 20px;background-color:#fff; " >
    <div id="title-name" style="font-size:15px; font-family: 'Varela Round', sans-serif; padding-bottom:10px;"  >
         Hi {{$emailName}},
    </div>
    <br />
    <div id="title-detail" style="word-wrap:break-word;">Thank you for joining us. We are excited to embark this journey with you. This email serves as your welcome pack.  
    </div>
    <br />
    <div id="title-detail" style="word-wrap:break-word;">Mednefits Care allows you to experience amazing health benefits, that covers up to 70% of your primary and preventive health care. We’ve designed health benefits experience that’s simple and human - the kind we want for ourselves, and our loved ones.
    </div>
    <br >
    <div id="title-detail" style="word-wrap:break-word;">
    Your plan will start on <span style="text-decoration: underline;">{{$plan_start}}</span>. You will receive your account details on the start date.
    </div>
  </div>
  <!--end of detail-container-->
  

   
   <!--detail-container-->
  <div id="detail-container" style="padding-top: 20px;padding-bottom: 20px;background-color:#fff; " >      
    <div id="title-detail" style=" font-size:14px; word-wrap:break-word; padding-bottom:10px;" >
      Download your Mednefits Care <a href="{{$welcome_pack}}" style="color: #5e96c5!important">Welcome Pack here</a>. You will receive:
      <p></p>
      1. A Mednefits Care Certificate & Receipt
      <br />
      2. An e-guide on How Mednefits Works - inside you will see a step by step guide to using our product and services.
      <br />
      3. Your plan benefits coverage
      <br />
      4. You may also view our panel health partner list by clicking this <a href="https://docs.google.com/spreadsheets/d/1YtsLDjgdHu6bKkZWRGtBIdeyWhwPTnDdQGFrUsBOZ9g/pubhtml" style="color: #5e96c5!important">link</a> - our number of health partners increases everyday, hence you may expect an increasing number of health partners updated in this list.
      <br />
      You will receive your account details on the start date. 
      <p></p>
      <div id="title-detail" style=" font-size:14px; word-wrap:break-word; padding-bottom:10px;" >
        If you have any queries, feel free to contact us at <a href="mailto:happiness@mednefits.com" style="color: black!important">happiness@mednefits.com</a>, we are always happy to hear from you.
      </div>
    </div>

      
    <div id="title-detail" style=" font-size:14px; word-wrap:break-word; padding-bottom:10px;" >
              Thank you, <br />
      <strong>Your Mednefits Team </strong> </div>
  </div>
  <!--end of detail-container-->
   
   
   
  
  <!--app-banner-->
  <div id="app-banner" style=" position:relative;">
          <div id="banner-large" style=" position:relative;">
          <a href="http://onelink.to/pyxjqg">
          <img style="display:block;" src="https://www.medicloud.sg/e-template-img/Email-Footer-Banner.jpg" width="100%"  alt=""/> 
          </a>
          </div>
 
    </div>
    <!--end of app-banner-->
  
  
  
  
 <!--footer contacts--> 
 <div id="footer-contact" style="text-align:center; background-color:#fff; padding-top:50px; padding-bottom:0px;">
 
   <div id="bluelogo" style="text-align:center">
   <img src="https://medicloud.sg/e-template-img/mednefits+logo+v3+(blue-box)+LARGE.png" alt="" border="0" style="width: 40px;height: 40px;">
   </div>
   
    <div id="contact-detail" style="padding-left:18px; font-family: 'Varela Round', sans-serif; color:#9c9c9c; padding-bottom:20px;"> 
       <span> +65 6254 7889 </span> 
       <span> 
           <a style="text-decoration:none;  color:#9c9c9c; border-left: 1px solid; margin-left: 3px; padding-left: 6px;" href="mailto:happiness@mednefits.com">        happiness@mednefits.com
           </a>
       </span> 
       </div>
       
    <div id="footer-social" style="text-align:center; padding-bottom:0px; margin-left: auto; margin-right: auto; padding-bottom: 20px; text-align: center; width: 162px; ">
      <div class="social-icn" style=" width:50px; height:50px; float: left;  margin-right:2px;"> 
          <a href="https://www.facebook.com/Mednefits">
            <img src="https://medicloud.sg/e-template-img/Facebook.png" width="50" height="50"  alt=""/> 
          </a> 
      </div>
      <div class="social-icn" style=" width:50px; height:50px; float: left;  margin-right:2px;"> 
          <a href="https://www.instagram.com/mednefits">
              <img src="https://medicloud.sg/e-template-img/Instagram.png" width="50" height="50"  alt=""/>
          </a> 
      </div>
     
      <div class="social-icn" style=" width:50px; height:50px; float: left;  margin-right:2px;">
          <a href=" https://www.linkedin.com/company/13238401">
           <img src="https://medicloud.sg/e-template-img/Linkedin.png" width="50" height="50"  alt=""/> 
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
    <a style="text-decoration:none; color:#9c9c9c; " href="https://www.medicloud.sg/terms.html">Terms of use</a> 
    &#124;
    <a style="text-decoration:none; color:#9c9c9c; " href="https://www.medicloud.sg/privacy-policy.html"> Privacy Policy</a></div>
  </div>
  <!--end of footer--> 
  
  
  
  
</div>
<!--end of template wraper-->
</body>
</html>