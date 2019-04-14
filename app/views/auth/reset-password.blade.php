@include('common.header')


<!-- 
<div class="mc-background-color">
 
 

    <div class="mc-header">
      <div class="mc-logo-container mc-fl"><img src="{{ URL::asset('assets/images/mc-logo-left.png') }}" width="197" height="43" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/mc-logo-left.png') }}"></div>
        <div class="mc-main-menu ">

        </div>
        <div class="clear"></div>
    </div>
   
    
    <div class="mc-border-line"></div>
    
    

   
      <div class="reset-wrapper2">
        <form action="" method="POST" id="form-reset">   
        <div class="reset-container-confirm">
        <div id="ajax-error"></div>
          <div class="reset-title">Reset Your Password ?</div>
          <div class="reset-title-sub">Please enter your new password to reset.</div>
   
             <div class="reset-input-wrapper">
             <div style="width:350px;">    
                 <input type="password" id="Password" placeholder="New Password" name="Password" class="reset-input"></div>
  	</div>   
             <div class="reset-input-wrapper">
            <div style="width:350px;">
                <input type="password" id="ConPassword" placeholder="Retype Password" name="ConPassword" class="reset-input"></div>
  	</div>
          <div class="mc-btn-reset-changes" id="auth-reset" userid="<?php echo $userid;?>">RESET</div> 
          <div class="mc-clear"></div>
        </div>
            
      </form>
        <div class="mc-clear"></div>
      </div>
    <div class="mc-clear"></div>`
   
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

#btn-create-cliic {
    font-size: 9px !important;
    background: #0B5192 !important;
}

input::-webkit-input-placeholder {
color: #999999 !important;
}

input::-moz-placeholder{
color: #999999 !important;
}

#Password-error {
  padding: 0px;
  padding-right: 170px;
  padding-bottom: 10px;
}

#ConPassword-error {
  padding: 0px;
  padding-right: 140px;
  padding-bottom: 10px;
}
#login-slide{
  width: 500px;
    background: white;
    height: 500px;
}
#div_msg2 div {
  /* border: 0px; */
  background-image: none;
}

  </style>


  <div class="mc-background-effect" style="margin-top: 5%; height: 100%;">

  <div style="height: 500px; width: 500px; margin: auto;">
    
      <div id="login-slide">

      <div class="mc-logo-center"><img src="{{ URL::asset('assets/images/MediCloud-Logo-v1-(white).png') }}" width="175" height="102" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/MediCloud-Logo-v1-(white).png') }}"></div>

      <div class="main-div">
        <h6 style="color:#565656; margin-bottom: 20px;"><b style="font-size: 13px;">Hello, You are almost done</b></h6>
        <form action="" method="POST" id="form-reset">
        <div>
          <div id="ajax-error"></div>
          <input type="password" class="form-control" id="Password" name="Password" value="" placeholder="Enter a New Password"><br>
          <input type="password" class="form-control" id="ConPassword" name="ConPassword" value="" placeholder="Enter again">
          <br>
          <div id="div_msg2"></div>
          <button class="btn btn-block" style="color: white; background: #00ADEF; height: 45px;" id="auth-reset" userid="<?php echo $userid;?>"><b>Done</b></button>
        </div>
        </form>
      </div>

      </div>
  </div><br><br>

</div>


@include('common.footer')