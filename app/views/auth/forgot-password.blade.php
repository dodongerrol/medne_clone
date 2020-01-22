@include('common.header')



<!--   <div class="mc-background-color">
 
 

    <div class="mc-header">
      <div class="mc-logo-container mc-fl"><img src="{{ URL::asset('assets/images/mc-logo-left.png') }}" width="197" height="43" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/mc-logo-left.png') }}"></div>
        <div class="mc-main-menu ">

        </div>
        <div class="clear"></div>
    </div> 
  
    
    
    <div class="mc-border-line"></div>
    
    

   
      <div class="reset-wrapper">
        <form action="" method="POST" id="form-signup">  
        <div class="reset-container">
            <div id="ajax-error"></div>
          <div class="reset-title">Forgot your Password ?</div>
          <div class="reset-title-sub">Enter your Email address below and we'll send you password rest instructions.</div>
          
          <div class="reset-input-wrapper">
              <div style="width:350px;">  <input id="Email" type="text" placeholder="Email" name="Email" class="reset-input"> </div>
  	</div>
            <div class="mc-btn-reset-changes" id="auth-forgot">RESET</div>
          
        <div class="mc-clear"></div>  
        </div>
       </form>      
      </div>
    
   
  
    <div class="mc-clear"></div> 
    
<div class="mc-footer">
      <div class="mc-fl">
  <div class="mc-copyright mc-label5">© 2014 Medicloud. All rights reserved</div>
        <div class="mc-links mc-label3">About | Terms of Service | Privacy Policy</div>
      </div>
      
      
      <div class="mc-fr">
        <div class="mc-social-icon mc-fl"><img src="{{ URL::asset('assets/images/img-mail.png') }}" width="52" height="55"></div>
        <div class="mc-social-icon mc-fl"><img src="{{ URL::asset('assets/images/img-fb.png') }}" width="52" height="55"></div>
        <div class="mc-social-icon mc-fl"><img src="{{ URL::asset('assets/images/img-twtr.png') }}" width="52" height="55"></div>
        <div class="mc-social-icon mc-fl"><img src="{{ URL::asset('assets/images/img-gp.png') }}" width="52" height="55"></div>
      </div>
    </div>
    
  </div> -->


 <style type="text/css">
  body{
    background-color: #1897d4;
  }
  .main-div {
    margin:auto;
    width: 300px;
    height: 100px;
    text-align:center;
  }
 
 .form-control {
   height: 45px;
   background-color: white !important;
   /* border: none !important; */
   color: #555;
   padding: 0px 22px;
 }

#btn-login {
    font-size: 9px !important;
    background: #0B5192 !important;
}

input::-webkit-input-placeholder {
color: #999999 !important;
}

input::-moz-placeholder{
color: #999999 !important;
}

#Email-error {
  padding: 0px;
  padding-right: 140px;
}

#div_msg1 div {
  /* border: 0px; */
  background-image: none;
}
#login-slide{
  width: 500px;
    background: white;
    height: 320px;
        padding-top: 50px;
}

  </style>

<div style="clear: both"></div>
  <div class="mc-background-effect" style="margin-top:5 %; height: 100%;"> 
    <div class="mc-logo-center" style="width: 250px;padding-top: 15px;"><img src="{{ URL::asset('../assets/userWeb/img/Mednefits Logo V2.svg') }}" style="width: 100%" alt="medicloud-logo"></div>

    <div style="height: 490px; width: 500px; margin: auto;">
    
      <div id="login-slide">
        <!-- <div class="mc-logo-center"><img src="{{ URL::asset('assets/images/MediCloud-Logo-v1-(white).png') }}" width="175" height="102" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/MediCloud-Logo-v1-(white).png') }}"></div> -->

    <div class="main-div">
      <h6 style="color:#565656;"><b style="font-size: 13px;">Forgot Your Password?</b></h6>
      <h6 style="color:#565656; margin-bottom: 20px;"><b style="font-size: 13px;">We'll get you setup in no time</b></h6>
      <form action="" method="POST" id="form-signup">
      <div>
        <input type="email" class="form-control" id="Email" name="Email" value="" placeholder="Email">
        <br>
        <button class="btn btn-block" style="color: white; background: #00ADEF; height: 45px;" id="auth-forgot"><b>Reset Password</b></button>
      </div>
      </form>
      <br>
      <div id="div_msg1"></div>
    </div>
    
      </div>

    </div><br><br>

</div>
@include('common.footer')