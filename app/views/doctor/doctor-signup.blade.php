<?php //include ("../common/header.php");?>
@include('common.header')


  <div class="mc-background-effect"> 
    <div class="mc-logo-center"><img src="{{ URL::asset('assets/images/mc-logo-center.png') }}" width="175" height="102" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/mc-logo.png') }}"></div>
    <div class="mc-login-form-container">
        <a href="{{ URL::to('app/auth/login') }}"><div class="icn-close"><img src="{{ URL::asset('assets/images/icn-close.png') }}" alt="icn-close" longdesc="{{ URL::asset('assets/images/icn-close.png') }}"></div> </a>
        <form action="" method="POST" id="form-signup">
        <fieldset>
          <div class="mc-form-bg">
              <div class="div-space-top">
                  <input id="Email" name="Email" value="<?php echo $email;?>" type="text" placeholder="Email">
              </div>
              <div class="div-space-bottom">
                  <input id="Password" name="Password" type="password" placeholder="Password">
              </div>
              <div class="div-space-bottom"><input name="ConPassword" type="password" placeholder="Reconfirm Password"></div>
            <div id="Doctor-Signup" class="mc-btn-drkblue mc-fl" userid="<?php echo $userid;?>">SIGNUP</div>
          </div>
        </fieldset>
    </form>
    </div>
    
   
  </div>

@include('common.footer')