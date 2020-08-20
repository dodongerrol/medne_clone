
@include('common.home_header')

<!-- {{ HTML::style('assets/settings/payments/payments.css') }} -->
<link rel="stylesheet" href="<?php echo $server; ?>/assets/settings/payments/payments.css?_={{ $date->format('U') }}">
<link rel="stylesheet" href="<?php echo $server; ?>/assets/settings/main.css?_={{ $date->format('U') }}">
<link rel="stylesheet" href="<?php echo $server; ?>/assets/settings/staff/staff.css?_={{ $date->format('U') }}">
<link rel="stylesheet" href="<?php echo $server; ?>/assets/css/settings-page.css?_={{ $date->format('U') }}">
<link rel="stylesheet" href="<?php echo $server; ?>/assets/claim/css/claim-loader.css?_={{ $date->format('U') }}">
<!-- <link rel="stylesheet" href="<?php //echo $server; ?>/assets/css/bootstrap-toggle.min.css?_={{ $date->format('U') }}"> -->
<!-- {{ HTML::style('assets/settings/main.css') }} -->
<script type="text/javascript" src="<?php echo $server; ?>/assets/settings/main.js?_={{ $date->format('U') }}"></script>

<script type="text/javascript" src="<?php echo $server; ?>/assets/js/jspdf.debug.js?_={{ $date->format('U') }}"></script>
<script type="text/javascript" src="<?php echo $server; ?>/assets/js/html2canvas.min.js?_={{ $date->format('U') }}"></script>
<script type="text/javascript" src="<?php echo $server; ?>/assets/js/jquery.printElement.min.js?_={{ $date->format('U') }}"></script>
<!-- <script type="text/javascript" src="<?php //echo $server; ?>/assets/js/bootstrap-toggle.min.js?_={{ $date->format('U') }}"></script> -->
<!-- {{ HTML::script('assets/settings/main.js') }} -->
<!-- {{ HTML::style('assets/settings/staff/staff.css') }} -->


  <div id="alert_box">
    message goes here
  </div>
  

  <div id="staff-setting-panel" class="col-sm-12" style="padding: 0px;">
            
    <!-- tabs left -->
    <div class="tabbable tabs-left" style="box-sizing: border-box !important;">

      <div class="side-pannel">
      <!-- <div class="side-pannel" style="padding: 0px; padding-right: 35px;"> -->
        <ul class="nav nav-tabs" id="setting-navigation" style="width: 100%;">
          <li class="active"><a id="account-tab" href="#account" data-toggle="tab">
          <img src="{{ URL::asset('assets/images/ico_Account.svg') }}" width="35" height="35" style="cursor: pointer;"><br>
          <span>ACCOUNT</span>
          </a></li>

          <li><a id="staff-tab" href="#staff" data-toggle="tab">
          <img src="{{ URL::asset('assets/images/Staff.png') }}" width="43" height="38" style="cursor: pointer;"><br>
          <span>STAFF</span>
          </a></li>

          <li><a id="service-tab" href="#service" data-toggle="tab">
          <img src="{{ URL::asset('assets/images/ico_services.svg') }}" width="37" height="37" style="cursor: pointer;"><br>
          <span>SERVICES</span>
          </a></li>

          <!-- <li><a id="notify-tab" href="#notify" data-toggle="tab">
          <i class="glyphicon glyphicon-flag" style="font-size: 25px;"></i><br><span>NOTIFICATIONS</span>
          </a></li> -->

          <li><a id="profile-tab" href="#profile" data-toggle="tab">
          <img src="{{ URL::asset('assets/images/ico_profile_2.svg') }}" width="48" height="48" style="cursor: pointer;"><br>
          <span>PROFILE</span>
          </a></li>

          <li><a id="payments-tab" href="#payments" data-toggle="tab">
          <img src="{{ URL::asset('assets/images/Transaction.png') }}" width="32" height="32" style="cursor: pointer;margin-bottom: 5px"><br>
          <span>TRANSACTIONS</span>
          </a></li>
        </ul>
      </div>

      <div id="setting-nav-panel" class="tab-content" style="">
      <!-- <div id="setting-nav-panel" class="tab-content col-sm-10" style="padding: 0px;"> -->
         <div class="tab-pane active" id="main-tab-account"></div>
         <div class="tab-pane" id="main-tab-staff"></div>
         <div class="tab-pane" id="main-tab-service"></div>
         <div class="tab-pane" id="main-tab-notify"></div>
         <div class="tab-pane" id="main-tab-profile"></div>
         <div class="tab-pane" id="main-tab-payments"></div>
      </div>
        
    </div>
    <!-- /tabs -->
  </div>

@include('common.footer')