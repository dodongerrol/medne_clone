@include('admin.header-admin')

  <div class="mc-background-effect"> 
    <div class="mc-logo-center"><img src="{{ URL::asset('assets/images/mc-logo-center.png') }}" width="175" height="102" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/mc-logo.png') }}"></div>
    <div class="mc-login-form-container">
    <a href="#"><div class="icn-close"><img src="{{ URL::asset('assets/images/icn-close.png') }}" alt="icn-close" longdesc="{{ URL::asset('assets/images/icn-close.png') }}"></div> </a>
    <form action="" method="POST" id="admin-form-signin">
    <fieldset>
      <div class="mc-form-bg">
          <div id="ajax-error"></div>
          <div class="div-space-top"><input id="Email" name="Email" type="text" placeholder="Admin Email"></div>
          <div class="div-space-bottom"><input id="Password" name="Password" type="password" placeholder="Admin Password"></div>
          <div class="mc-clear"></div>
          <div style="margin-top: 37px; margin-bottom: 20px; float: left;">
          <div class="mc-fl"><!--{{HTML::link('/app/auth/forgot', 'Forgot ?',array('class' => 'forgot mc-btn-lightblue'))}}--></div>
          </div>
          <div style="margin-top: 1px; margin-bottom: 20px; float: left;">
          <div class="mc-fl  mc-btn-drkblue" id="admin-login">LOGIN</div>
          </div>
        <div class="mc-clear"></div>
        
      </div>
    </fieldset>
    </form>
    </div>
    
   
  </div>


@include('admin.footer-admin')