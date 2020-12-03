@include('common.header')

 <!--  <div class="mc-background-effect">
    <div class="mc-logo-center"><img src="{{ URL::asset('assets/images/mc-logo-center.svg') }}" width="175" height="102" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/mc-logo.png') }}"></div>
    <div class="mc-login-form-container">
    <a href="#"><div class="icn-close"><img src="{{ URL::asset('assets/images/icn-close.png') }}" alt="icn-close" longdesc="{{ URL::asset('assets/images/icn-close.png') }}"></div> </a>
    <form action="" method="POST" id="form-signup">
    <fieldset>
      <div class="mc-form-bg">
          <div id="ajax-error"></div>
          <div class="div-space-top"><input id="Email" name="Email" type="text" placeholder="Email"></div>
          <div class="div-space-bottom"><input id="Password" name="Password" type="password" placeholder="Password"></div>
          <div class="mc-clear"></div>
          <div style="margin-top: 37px; margin-bottom: 20px; float: left;">
          <div class="mc-fl">{{HTML::link('/app/auth/forgot', 'Forgot ?',array('class' => 'forgot mc-btn-lightblue'))}}</div>
          </div>
          <div style="margin-top: 1px; margin-bottom: 20px; float: left;">
          <div class="mc-fl  mc-btn-drkblue" id="auth-login">LOGIN</div>
          </div>
        <div class="mc-clear"></div>

      </div>
    </fieldset>
    </form>
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
  font-size: 10px !important;
  padding: 10px 18px;
  background: #104159 !important;
  border: 0px;
}

input::-webkit-input-placeholder {
color: #999999 !important;
}

input::-moz-placeholder{
color: #999999 !important;
}

#Email-error {
  padding: 0px;
  padding-right: 160px;
  padding-bottom: 10px;
}

#Password-error {
  padding: 0px;
  padding-right: 140px;
  padding-bottom: 10px;
}

#ajax-error div {
  border: 0px;
  background-image: none;
}
#login-slide{
  width: 500px;
    background: white;
    height: 450px;
    padding-top: 50px;
}
#detail-slide{
      width: 260px;
    background: #F2F2F2;
    height: 450px;
}

  </style>
<br>
<!-- <div class="pull-right" style="padding-right: 30px;">
  <span style="font-size: 12px; color: #104159;">Need a New Account? </span> &nbsp;
  <a href="{{URL::to('app/auth/newClinic')}}" class="btn btn-primary" id="btn-create-cliic">CREATE AN ACCOUNT</a>
</div> -->
<div style="clear: both"></div>

  <div class="mc-background-effect" style="margin-top: 5%; height: 100%;">

  <div class="mc-logo-center" style="width: 250px;padding-top: 15px;"><img src="{{ URL::asset('../assets/userWeb/img/Mednefits Logo V2.svg') }}" style="width: 100%" alt="medicloud-logo"></div>

    <div style="height: 550px; width: 760px; margin: auto;">

      <div class="col-md-6" id="login-slide">
      <!-- <div class="mc-logo-center"><img src="{{ URL::asset('assets/images/MediCloud-Logo-v1-(white).png') }}" width="175" height="102" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/MediCloud-Logo-v1-(white).png') }}"></div> -->

    <div class="main-div">
      <h6 style="color:#565656; margin-bottom: 20px;"><b style="font-size: 14px;">HEALTH PROFESSIONAL LOGIN</b></h6>
      <h6 style="color:#565656; margin-bottom: 20px;"><b style="font-size: 13px;">Welcome, Please Sign in</b></h6>
      <form action="" method="POST" id="form-signup">
      <div>
        <input type="email" class="form-control" id="Email" name="Email" value="" placeholder="Email"><br>
        <input type="password" class="form-control" id="Password" name="Password" value="" placeholder="Password">
        <br>
        <div id="ajax-error"></div>
        <span class="pull-left" style="font-size: 12px;"><input type="checkbox" name="" value=""><b style="padding-left: 5px;">Stay signed in</b></span>
        <span class="pull-right"><a href="{{URL::to('app/auth/forgot')}}" style="color: #1A98D5; font-size: 12px;"><b>Forgot Password?</b></a></span>
        <br><br>
        <button class="btn btn-block" style="color: white; background: #00ADEF;" id="auth-login"><b>Sign in</b></button>
      </div>
      </form>
    </div>
    </div>

    <div class="col-md-6" id="detail-slide">
    <div style="margin-top: 20%; text-align: center; color: #646464; font-weight: bold; font-size: 13px;">
      <p style="font-size: 25px; color: #565656;">Do you have questions?</p>
      <br>
      <p style="margin-bottom: 5px;">You can reach us from 10:00 am to 7:00 pm, Monday to Friday under the following telephone number</p>
      <p style="font-size: 16px; color: #565656;">+65 3163 5403</p>
      <p style="font-size: 16px; color: #565656;">+60 330 995 774</p>
      <br>
      <p style="margin-bottom: 0px;">or send us an email</p>
      <p style="font-size: 16px; color: #00AFF0;">support@mednefits.com</p>
      <br>
      <p>our friendly support team will assist you.</p>
    </div>  
    </div>

    </div><br><br>
  </div>




@include('common.footer')
