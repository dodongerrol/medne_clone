@include('common.header')


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

#name-error {
  padding: 0px;
  padding-right: 160px;
}

#email-error {
  padding: 0px;
  padding-right: 160px;
  padding-bottom: 10px;
}

#password-error {
  padding: 0px;
  padding-right: 140px;
  padding-bottom: 10px;
}

#div_msg div {
  font-size: 13px;
  background-image: none;
}
#login-slide{
  width: 500px;
    background: white;
    height: 550px;
    padding-top: 80px;
}
#detail-slide{
      width: 260px;
    background: #F2F2F2;
    height: 550px;
    /*padding-top: 20px;*/
}

  </style>
<br>
<div class="pull-right" style="padding-right: 30px;">
  <span style="font-size: 12px; color: #104159;">Already have an account? </span> &nbsp;
  <a href="{{URL::to('app/auth/login')}}" class="btn btn-primary" id="btn-login">LOGIN NOW</a>
</div>
<div style="clear: both"></div>
  <div class="mc-background-effect" style=" height: 100%;">
    <div class="mc-logo-center" style="width: 250px;padding-top: 15px;"><img src="{{ URL::asset('../assets/userWeb/img/Mednefits Logo V2.svg') }}" style="width: 100%" alt="medicloud-logo"></div>

  <div style="height: 620px; width: 760px; margin: auto;">
    <div class="col-md-6" id="login-slide">

      <!-- <div class="mc-logo-center"><img src="{{ URL::asset('assets/images/MediCloud-Logo-v1-(white).png') }}" width="175" height="102" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/MediCloud-Logo-v1-(white).png') }}"></div> -->

    <div class="main-div">
      <h6 style="color:#565656; margin-bottom: 20px;"><b style="font-size: 13px;">Welcome, Lets Begin</b></h6>
      <form action="" method="POST" id="clinic-form-signup">
      <div>
        <input type="name" class="form-control" id="name" name="name" value="" placeholder="Name"><br>
        <input type="email" class="form-control" id="email" name="email" value="" placeholder="Email"><br>
        <input type="password" class="form-control" id="password" name="password" value="" placeholder="Password">
        <br>
        <div id="div_msg"></div>
        <h6 style="margin-bottom: 20px;"> By clicking on the Create an Account below, you confirm that you accept the Medicloud <a style="color: #0A508C;" href="https://www.medicloud.sg/terms.html" title="" target="_blank">Terms and Conditions</a></h6>
      
        <button class="btn btn-block" style="color: white; background: #00ADEF;" id="create-clinic"><b>Create an account</b></button>
        <br>
      </div>
      </form>
    </div>
    </div>

    <div class="col-md-6" id="detail-slide">
    <div style="margin-top: 20%; text-align: center; color: #646464; font-weight: bold; font-size: 13px;">
      <p>Test all features for free and without obligation for 7 days.</p>
      <p style="font-weight: normal;">Then Change to our pay as you grow pricing plan.</p>
      <br>
      <p style="font-size: 20px; color: #565656;">Our Customers</p>
      <br>
      <div style="padding-bottom: 30px;">
        <img src="{{ URL::asset('assets/images/Care-Passion.png') }}" width="70" height="70" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/Care-Passion.png') }}">
      </div>
      <div style="padding-bottom: 30px;">
        <img src="{{ URL::asset('assets/images/Dental-Focus.png') }}" width="100" height="55" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/Dental-Focus.png') }}">
      </div>
      <div style="padding-bottom: 30px;">
        <img src="{{ URL::asset('assets/images/i-Dental.png') }}" width="115" height="45" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/i-Dental.png') }}">
      </div>
      <div>
        <img src="{{ URL::asset('assets/images/DRS.Chua-&-Partners.png') }}" width="180" height="45" alt="medicloud-logo" longdesc="{{ URL::asset('assets/images/DRS.Chua-&-Partners.png') }}">
      </div>
    </div>  
    </div>

  </div><br><br>

</div>




@include('common.footer')