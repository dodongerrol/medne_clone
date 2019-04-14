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
    <h2><b>Now you can enjoy your benefits provided by your company!</b></h2>
  </div>
  <div id="detail-container" style="padding-top: 20px;padding-bottom: 20px;background-color:#fff; " >
    <div id="title-name" style=" font-size:15px; font-family: 'Varela Round', sans-serif; padding-bottom:10px;"  >
         Hi {{$emailName}},
    </div>
    <p> </p>
     <div id="title-detail" style=" word-wrap:break-word;">This email serves as a confirmation that your account has now successfully activated. The account login below only
allows you to access via Mednefits App.
    </div>
  </div>
  <!--end of detail-container-->
  

   
   <!--detail-container-->
  <div id="detail-container" style="padding-bottom: 20px;background-color:#fff; " >      
    <div id="title-detail" style=" font-size:14px; word-wrap:break-word; padding-bottom:10px;" >
      <div style="width: 100%;">
        <span style="font-weight: bold;text-decoration: underline;">Account Details for Mednefits App</span>
        <br />
        <span>Download Mednefits App in either on <a href="http://onelink.to/pyxjqg">Apple App Store</a> or <a href="http://onelink.to/pyxjqg">Android PlayStore</a></span>
        <br />
        <span><span style="font-weight: bold;">Login ID: </span><a href="">{{$login_id}}</a></span>
        <br />
        <span><span style="font-weight: bold;">Password: </span><a href="">{{$login_password}}</a></span>
      </div>
      <p> </p>
      <div style="width: 100%;">
          A breakdown of member’s welcome pack:
          <br />
          1. See <a href="{{$how}}" style="color: #0C80BC!important;">How Mednefits Works</a>
          <br />
          2. <a href="{{$coverage}}" style="color: #0C80BC!important;">View your coverage</a>
          <br />
          3. <a href="https://docs.google.com/spreadsheets/d/1YtsLDjgdHu6bKkZWRGtBIdeyWhwPTnDdQGFrUsBOZ9g/pubhtml" style="color: #0C80BC!important;">See which health partners is nearer to you </a> - our health partners increases everyday, hence you may expect the increasing number of partners updated in this list
      </div>
      <div id="title-detail" style=" font-size:14px; word-wrap:break-word; padding-bottom:10px;" >
        If you have any queries, do contact your company HR who manages your benefits.
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