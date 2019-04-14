@include('common.header')


<!--  <div class="mc-background-effect"> 
    <div class="mc-logo-center"><img src="{{ URL::asset('assets/images/mc-logo-center.png') }}" width="175" height="102" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/mc-logo.png') }}"></div>
    <div class="mc-login-form-container">
        <a href="{{ URL::to('app/auth/login') }}"><div class="icn-close"><img src="{{ URL::asset('assets/images/icn-close.png') }}" alt="icn-close" longdesc="{{ URL::asset('assets/images/icn-close.png') }}"></div> </a>
        <form action="" method="POST" id="form-signup">
        <fieldset>
          <div class="mc-form-bg">
              <div class="div-space-top">
                  <input id="Email" name="OldPassword" value="" type="text" placeholder="Old Password">
              </div>
              <div class="div-space-bottom">
                  <input id="Password" name="Password" type="password" placeholder="Password">
              </div>
              <div class="div-space-bottom"><input name="ConPassword" type="password" placeholder="Reconfirm Password"></div>
            <div id="Doctor-Signup" class="mc-btn-drkblue mc-fl" userid="">Reset Password</div>
          </div>
        </fieldset>
    </form>
    </div>
  </div>-->

<div class="mc-background-color"><!--MC BACKGROUND COLOR -->
 
 
  <!--HEADER START-->
    <div class="mc-header">
      <div class="mc-logo-container mc-fl"><img src="{{ URL::asset('assets/images/mc-logo-left.png') }}" width="197" height="43" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/mc-logo-left.png') }}"></div>
        <div class="mc-main-menu ">
<!--        <div class="mc-fr">{{ HTML::link('/app/auth/login', 'LOGIN',array('class' => 'mc-btn-logout'))}}</div>-->
<!--         <ul>
          <li>HOME</li>
          <li>DASHBOARD</li>
          <li>PAYMENTS</li>
          <li>SETTINGS</li>
          </ul>-->
        </div>
        <div class="clear"></div>
    </div>
   <!--HEADER END-->   
    
    
    <div class="mc-border-line"></div>
    
    
    <!--DOCTOR SELECTION START-->
   
      <div class="reset-wrapper2">
        <form action="" method="POST" id="form-reset">   
        <div class="reset-container-confirm">
        <div id="ajax-error"></div>
          <div class="reset-title">Reset Your Password ?</div>
          <div class="reset-title-sub">Please enter your new password to reset.</div>
        <!--<div class="reset-input-wrapper">
            <div style="width:350px;">
                <input type="password" id="OldPassword" placeholder="Old Password" name="OldPassword" class="reset-input">
            </div>
  	</div>  -->
             <div class="reset-input-wrapper">
             <div style="width:350px;">    
                 <input type="password" id="Password" placeholder="New Password" name="Password" class="reset-input"></div>
  	</div>   
             <div class="reset-input-wrapper">
            <div style="width:350px;">
                <input type="password" id="ConPassword" placeholder="Retype Password" name="ConPassword" class="reset-input"></div>
  	</div>
          <div class="mc-btn-reset-changes" id="auth-reset" userid="<?php echo $userid;?>">RESET</div> 
        </div>
      </form>
        <div class="mc-clear"></div>
      </div>
    <div class="mc-clear"></div>
   
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
    
  </div><!--MC BACKGROUND COLOR END -->

@include('common.footer')