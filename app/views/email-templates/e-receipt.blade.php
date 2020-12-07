<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Medicloud-Confirmation</title>
<link href='https://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
</head>

<body>


<!--template wraper-->
<div id="medi-emailtemplate-wrapper" style="width:90%; background-color: #CCC; margin-top:3%; margin-bottom:3%; margin-left:auto; margin-right:auto;  ">
  
 
  <!--header wraper-->
  <div id="medi-header-wrapper">
    
        <div id="header-line" style="height:10px; background-color:#72d0f6;">
        </div>
    
            <div id="header-logo-container" style="background-color:#1868ad;">
                <img style="display:block; margin:0px; padding:0px" src="https://www.medicloud.sg/e-template-img/Email-header-Banner.jpg" width="100%"    alt=""/> 
    </div>  
  </div>
  <!--end of header wraper-->
  
  
 
 
 
 
  <!--detail-container-->
  <div id="detail-container" style=" padding:20px 15px; background-color:#fff; " >
  
       <div id="title-name" style=" font-size:15px; font-family: 'Varela Round', sans-serif; padding-bottom:10px;"  >
            Hello {{$emailName}},
    </div>
 
                     


             <div id="title-detail" style=" word-wrap:break-word;" >
                 Thank you for using Mednefits, it was a pleasure serving you. Hope you enjoyed our service. Below is the receipt for your transaction and your appointment information for future referrence. 
            </div>
  </div>
  <!--end of detail-container-->
  
     <!--booking-detail-->
  <div id="booking-detail" style=" padding:20px 15px; background-color:#f8f6f6; font-family: 'Varela Round', sans-serif; " >
     
     <div id="title-booking" style=" font-size:15px; color:#156aaf; font-family: 'Varela Round', sans-serif; font-weight: 400; padding-bottom:15px; ">
     Booking Details 
     </div>
     
    <div id="booking-fields" style="  border-top:1px solid #d9d9d9; " >
       <div id="fields-title" style=" width: 230px; float:left; padding:5px 0px; background-color:#f8f6f6; "><b>Appointment ID:</b></div>
      <div id="fields-result" style=" float:left; padding:5px 0px; background-color:#f8f6f6; ">{{$bookingid}}</div>
      <div id="clear" style="clear:both;"></div>  
    </div>
        

    <div id="booking-fields" style="  border-top:1px solid #d9d9d9; " >
       <div id="fields-title" style=" width: 230px; float:left; padding:5px 0px; background-color:#f8f6f6; "><b>Health/Wellness Partners:</b></div>
      <div id="fields-result" style=" float:left; padding:5px 0px; background-color:#f8f6f6; ">{{$clinicName}}</div>
      <div id="clear" style="clear:both;"></div>  
    </div>
    
          

    
     <div id="booking-fields" style=" border-top:1px solid #d9d9d9; " >
       <div id="fields-title" style=" width: 230px; float:left; padding:5px 0px; background-color:#f8f6f6; "><b>Health Professionals:</b></div>
      <div id="fields-result" style=" float:left; padding:5px 0px; background-color:#f8f6f6; ">{{$doctorName}}</div>
      <div id="clear" style="clear:both;"></div>  
    </div>
    
          

     <div id="booking-fields" style=" border-top:1px solid #d9d9d9; " >
       <div id="fields-title" style=" width: 230px; float:left; padding:5px 0px; background-color:#f8f6f6; "><b>Procedure: </b>  </div>
      <div id="fields-result" style=" float:left; padding:5px 0px; background-color:#f8f6f6; ">{{$clinicProcedure}}</div>
      <div id="clear" style="clear:both;"></div>  
    </div>
          

     <div id="booking-fields" style=" border-top:1px solid #d9d9d9; " >
       <div id="fields-title" style=" width: 230px; float:left; padding:5px 0px; background-color:#f8f6f6; "><b>Appointment Date:</b></div>
      <div id="fields-result" style=" float:left; padding:5px 0px; background-color:#f8f6f6; ">{{$bookingDate}}</div>
      <div id="clear" style="clear:both;"></div>  
    </div>
          

    
     <div id="booking-fields" style=" border-top:1px solid #d9d9d9; " >
       <div id="fields-title" style=" width: 230px; float:left; padding:5px 0px; background-color:#f8f6f6; "><b>Appointment Time:</b></div>
      <div id="fields-result" style=" float:left; padding:5px 0px; background-color:#f8f6f6; ">{{$bookingTime}}</div>
      <div id="clear" style="clear:both;"></div>  
    </div>
    
          

     <div id="booking-fields" style=" border-top:1px solid #d9d9d9; " >
       <div id="fields-title" style=" width: 230px; float:left; padding:5px 0px; background-color:#f8f6f6; "><b>Partner’s Phone No:</b></div>
      <div id="fields-result" style=" float:left; padding:5px 0px; background-color:#f8f6f6; ">({{$clinicPhoneCode}}) {{$clinicPhone}}</div>
      <div id="clear" style="clear:both;"></div>  
    </div>
    
    
          

     <div id="booking-fields" style=" border-top:1px solid #d9d9d9; " >
       <div id="fields-title" style=" width: 230px;  float:left; padding:5px 0px; background-color:#f8f6f6; "><b>Partner’s Address:</b></div>
      <div id="fields-result" style=" float:left; padding:5px 0px; background-color:#f8f6f6; ">{{$clinicAddress}}</div>
      <div id="clear" style="clear:both;"></div>  
    </div>
    
              

    
     <!-- <div id="booking-fields" style="border-bottom:1px solid #d9d9d9;  border-top:1px solid #d9d9d9; " >
       <div id="fields-title" style=" width: 230px;  float:left; padding:5px 0px; background-color:#f8f6f6; ">Location:</div>
      <div id="fields-result" style=" float:left; padding:5px 0px; background-color:#f8f6f6; color:#00217d;  ">View in Google Maps</div>
      <div id="clear" style="clear:both;"></div>  
    </div> -->
     
     
    <div id="clear" style="clear:both;"></div>
  </div>
   <!--end of booking-detail-->

  
   <!--booking-detail-->
  <!-- <div id="booking-detail" style=" padding:20px 15px; background-color:#f8f6f6; font-family: 'Varela Round', sans-serif; " >
     
     <div id="title-booking" style=" font-size:15px; color:#156aaf; font-family: 'Varela Round', sans-serif; font-weight: 400; padding-bottom:15px; ">
     Receipt Summary
     </div> 

     <div id="booking-fields" style=" border-top:1px solid #d9d9d9; " >
       <div id="fields-title" style=" width: 230px; float:left; padding:5px 0px; background-color:#f8f6f6; "><b>Total Amount:</b></div>
      <div id="fields-result" style=" float:left; padding:5px 0px; background-color:#f8f6f6; ">{{$total_amount}}</div>
      <div id="clear" style="clear:both;"></div>  
    </div>
    

     <div id="booking-fields" style=" border-top:1px solid #d9d9d9; " >
       <div id="fields-title" style=" width: 230px;  float:left; padding:5px 0px; background-color:#f8f6f6; "><b>Medi Credit:</b></div>
      <div id="fields-result" style=" float:left; padding:5px 0px; background-color:#f8f6f6; ">-{{$deducted}}</div>
      <div id="clear" style="clear:both;"></div>  
    </div>

    <div id="booking-fields" style=" border-top:1px solid #d9d9d9; " >
       <div id="fields-title" style=" width: 230px;  float:left; padding:5px 0px; background-color:#f8f6f6; "><b>Final Bill:</b></div>
      <div id="fields-result" style=" float:left; padding:5px 0px; background-color:#f8f6f6; ">${{$final_bill}}</div>
      <div id="clear" style="clear:both;"></div>  
    </div>
    <div id="clear" style="clear:both;"></div>
  </div> -->
   <!--end of booking-detail-->
   
   
   
   
   
   
   
   <!--detail-container-->
  <div id="detail-container" style=" padding:20px 15px; background-color:#fff; " >
    <div id="title-detail" style=" font-size:14px; word-wrap:break-word; padding-bottom:10px;" >
                 In the case that any issue arises with your booking, our team will inform you. 
    </div>

    <!-- <div id="title-detail" style=" font-size:14px; word-wrap:break-word; padding-bottom:10px;">
      In the event you need to reschedule your appointment, kindly please cancel your appointment and re-book it in the app. under "Appointment Tracking".
    </div> -->
      
    <div id="title-detail" style=" font-size:14px; word-wrap:break-word; padding-bottom:10px;" >
                If you have any questions, feel free to contact us at <span style="color:#156aaf;"><a style="text-decoration:none;" href="mailto:support@mednefits.com">support@mednefits.com</a></span>, we are
always happy to hear from you!
    </div>
      
    <div id="title-detail" style=" font-size:14px; word-wrap:break-word; padding-bottom:10px;" >
              Thank you, <br />
      <strong>Your Mednefits Team </strong> </div>
  </div>
  <!--end of detail-container-->
   
   
   
  
  <!--app-banner-->
  <div id="app-banner" style=" position:relative;">
          <div id="banner-large" style=" position:relative;">
          <img style="display:block;" src="https://www.medicloud.sg/e-template-img/Email-Footer-Banner.jpg" width="100%"  alt=""/> 
          </div>
 
    </div>
    <!--end of app-banner-->
  
  
  
  
 <!--footer contacts--> 
 <div id="footer-contact" style="text-align:center; background-color:#fff; padding-top:50px; padding-bottom:0px;">
 
   <div id="bluelogo" style="text-align:center">
   <img src="https://medicloud.sg/e-template-img/mednefits+logo+v3+(blue-box)+LARGE.png" alt="" border="0" style="width: 40px;height: 40px;">
   </div>
   
    <div id="contact-detail" style="padding-left:18px; font-family: 'Varela Round', sans-serif; color:#9c9c9c; padding-bottom:20px;"> 
       <span> +65 3163 5403 </span> 
       <span> 
           <a style="text-decoration:none;  color:#9c9c9c; border-left: 1px solid; margin-left: 3px; padding-left: 6px;" href="mailto:support@mednefits.com">        support@mednefits.com
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
