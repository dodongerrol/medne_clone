<!DOCTYPE html>
<html ng-app="eclaim">
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-78188906-2"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-78188906-2');
	</script>
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- <meta charset="UTF-8" /> -->
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="-1" />
	<title>E-Claim</title>
	<link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/ico">

	<!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto" rel="stylesheet"> -->
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/bootstrap.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/font-awesome.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/style.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/custom.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/responsive.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/fonts.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/pre-loader.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/sweetalert.css?_={{ $date->format('U') }}">

	<style type="text/css">
		/* For Firefox */
		input.mobile-num-input {
		    -moz-appearance:textfield;
		}
		/* Webkit browsers like Safari and Chrome */
		input.mobile-num-input::-webkit-inner-spin-button,
		input.mobile-num-input::-webkit-outer-spin-button {
		    -webkit-appearance: none;
		    margin: 0;
		}
	</style>
</head>
<body eclaim-login>
	<!-- <div class="container">
		<div class="login-need-help-container">
			<a href="#" class="pull-right need-help-text tooltips">
				<h3>Need help?</h3>
				<div class="tooltip-container">
					<h3 class="tooltip-title">We're here to help.</h3>
					<h3 class="tooltip-phone-email-title">You may ring us</h3>
					<h3 class="tooltip-phone-email-details">+65 6254 7889</h3>
					<h3 class="tooltip-phone-email-details">Mon - Fri 10:00 to 19:00</h3>
					<br>
					<h3 class="tooltip-phone-email-title">Drop us a note, anytime</h3>
					<h3 class="tooltip-phone-email-details">happiness@mednefits.com</h3>
				</div>
			</a>
		</div>
		<div class="col-sm-12 col-md-12 col-lg-12">
			<img src="assets/hr-dashboard/img/mednefits_logo_v3_(blue)_LARGE.png" class="center-block login-logo">
			<h2 class="text-center text-below-image">for business</h2>
			<form class="med-form" ng-submit="loginHr()">
				<div class="form-group">
					<input type="email" name="email" class="form-control med-input" placeholder="Email Address" ng-model="login_details.email" required>
				</div>
				<div class="form-group">
					<input type="password" class="form-control med-input" placeholder="Enter password" ng-model="login_details.password" required style="margin-bottom: 15px">
				</div>
				<div class="checkbox">
			    <label style="color: #777;font-size: 15px;">
			      <input type="checkbox" ng-model="login_details.signed_in" style="margin-top: 5px;"> Stay signed in
			    </label>
			  </div>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block med-button" id="login-btn">Log in</button>
				</div>
				<span ng-if="ng_fail">*Please check your login credentials</span>
				<a href="/company-benefits-dashboard-forgot-password" class="forgot-password pull-right">Forgot password?</a>
			</form>
		</div>
	</div> -->

	<div class="login-wrapper">
		<div class="login-need-help-container">
			<a href="#" class="pull-right need-help-text tooltips">
				<h3>Need help?</h3>
				<div class="tooltip-container">
					<h3 class="tooltip-title">We're here to help.</h3>
					<h3 class="tooltip-phone-email-title">You may ring us</h3>
					<h3 class="tooltip-phone-email-details">+65 6254 7889</h3>
					<h3 class="tooltip-phone-email-details">Mon - Fri 9:30am to 6:30pm</h3>
					<br>
					<h3 class="tooltip-phone-email-title">Drop us a note, anytime</h3>
					<h3 class="tooltip-phone-email-details">happiness@mednefits.com</h3>
				</div>
			</a>
		</div>
		<div class="col-sm-12 col-md-12 col-lg-12" id="login-container">
			<div class="login-container-header">
				<img src="../assets/hr-dashboard/img/mednefits_logo_v3_(blue)_LARGE.png" class="center-block login-logo">
				<h2 class="text-center text-below-image">for member</h2>
			</div>
			<div class="notification-wrapper">
				<div class="notification-container">
					<div>
						<img src="./assets/images/danger.png">
					</div>
					<div>
						<div class="notification-text">Notification: User ID Change</div>
						<p>NRIC/FIN and email address will no longer be valid as your user ID. <br>
						Please click <a class="here-text" ng-click="goToUpdateDetails()">here</a> to change your user ID to your mobile number.</p>
					</div>
				</div>
			</div>
			<form class="med-form" ng-submit="login()">
				<div class="form-group">
					<!-- valid-number pattern="[0-9]*" type="tel" -->
					<input type="text" name="text" class="form-control med-input mobile-num-input" placeholder="Mobile Number" ng-model="email" />
				</div>
				<div class="form-group">
					<input type="password" class="form-control med-input" placeholder="Enter password" ng-model="password"  style="margin-bottom: 15px">
				</div>
				<div class="checkbox">
			    <label>
			      <input type="checkbox"> Stay signed in
			    </label>
			  </div>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block med-button" id="login-btn">Log in</button>
				</div>
				<span ng-if="invalid_credentials">*Please check your login credentials</span>
				<a href="javascript:void(0)" class="forgot-password pull-right" ng-click="showForgotPassword()">Forgot password?</a>
			</form>
		</div>

		<div class="col-sm-12 col-md-12 col-lg-12" id="forgot-password" hidden>
			<div class="login-container-header">
				<img src="../assets/hr-dashboard/img/mednefits_logo_v3_(blue)_LARGE.png" class="center-block login-logo">
				<h2 class="text-center text-below-image">for member</h2>
			</div>
			<form class="med-form" ng-submit="resetPassword()">
				<div class="form-group">
					<input type="text" name="email" class="form-control med-input" placeholder="Mobile Number or Email Address" ng-model="login_details.email" required/>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block med-button" id="reset-password">Reset Password</button>
				</div>
				<span ng-bind="callback.text"></span>
				<a href="javascript:void(0)" class="forgot-password pull-right" ng-click="showLogin()">Login</a>
			</form>
		</div>
	</div>

	<div class="circle-loader" hidden>
		<div class="preloader-container">
			<div class="white-space-50"></div>
			<div class="preloader-wrapper big active">
		    <div class="spinner-layer spinner-blue-only">
		      <div class="circle-clipper left">
		        <div class="circle"></div>
		      </div><div class="gap-patch">
		        <div class="circle"></div>
		      </div><div class="circle-clipper right">
		        <div class="circle"></div>
		      </div>
		    </div>
		  </div>
		</div>
	</div>

</body>

	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/jquery.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/bootstrap.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/angular.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/sweetalert.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/process/eclaim_login.js?_={{ $date->format('U') }}"></script>

</html>
