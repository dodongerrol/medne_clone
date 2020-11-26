<!DOCTYPE html>
<html lang="en" ng-app="login">
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-78188906-2"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-78188906-2');
	</script>
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta charset="UTF-8">
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='-1'>
	<meta http-equiv='pragma' content='no-cache'>
	<title>Mednefits: Modern Employees Digital Benefits</title>
	<link rel="shortcut icon" href="{{ URL::asset('assets/hr-dashboard/img/icons/favicon.ico') }}" type="image/ico">

	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
	{{ HTML::style('assets/hr-dashboard/css/bootstrap.min.css') }}
	{{ HTML::style('assets/hr-dashboard/css/font-awesome.min.css') }}
	{{ HTML::style('assets/hr-dashboard/css/style.css') }}
	<!-- {{ HTML::style('assets/hr-dashboard/css/responsive.css') }} -->

</head>
<body login-section>
	<div class="container">
		<div class="login-need-help-container">
			<a href="#" class="pull-right need-help-text tooltips">
				<h3>Need help?</h3>
				<div class="tooltip-container">
					<h3 class="tooltip-title">We're here to help.</h3>
					<h3 class="tooltip-phone-email-title">You may ring us</h3>
					<h3 class="tooltip-phone-email-details">+65 3163 5403</h3>
					<h3 class="tooltip-phone-email-details">+60 330 995 774</h3>
					<h3 class="tooltip-phone-email-details">Mon - Fri 9:00am to 6:00pm</h3>
					<br>
					<h3 class="tooltip-phone-email-title">Drop us a note, anytime</h3>
					<h3 class="tooltip-phone-email-details">happiness@mednefits.com</h3>
				</div>
			</a>
		</div>
		<div class="col-sm-12 col-md-12 col-lg-12 new-account" id="login-container">
			<img src="{{ URL::asset('assets/hr-dashboard/img/Mednefits Logo V1.svg') }}" class="center-block login-logo">
			<h2 class="text-center text-below-image">for health provider</h2>
			<form class="med-form" ng-submit="loginClinic()">
				<div class="form-group">
					<label for="email">Email</label>
					<input type="email" name="email" class="form-control med-input" placeholder="Enter Email Address" ng-model="login_details.email" required/>
				</div>
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" class="form-control med-input" placeholder="Enter Password" ng-model="login_details.password" required style="margin-bottom: 15px">
				</div>
				<div class="checkbox">
			    <label style="color: #777;font-size: 15px;">
			      <input type="checkbox" style="margin-top: 5px;"> Stay signed in
			    </label>
			  </div>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block med-button" id="login-btn">Log in</button>
				</div>
				<span ng-if="ng_fail">*Please check your login credentials</span>
				<a href="javascript:void(0)" class="forgot-password pull-right" ng-click="showForgotPassword()">Forgot password?</a>
			</form>
		</div>

		<div class="col-sm-12 col-md-12 col-lg-12 new-account" id="forgot-password" hidden>
			<img src="{{ URL::asset('assets/hr-dashboard/img/Mednefits Logo V1.svg') }}" class="center-block login-logo">
			<h2 class="text-center text-below-image">for health provider</h2>
			<form class="med-form" ng-submit="resetPassword()">
				<div class="form-group">
					<label for="email">Email</label>
					<input type="email" name="email" class="form-control med-input" placeholder="Communication Email Address" ng-model="login_details.email" required/>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block med-button" id="reset-password">Reset Password</button>
				</div>
				<span ng-bind="callback.text"></span>
				<a href="javascript:void(0)" class="forgot-password pull-right" ng-click="showLogin()">Login</a>
			</form>
		</div>
	</div>

</body>


	{{ HTML::script('assets/hr-dashboard/js/jquery.min.js') }}
	{{ HTML::script('assets/hr-dashboard/js/bootstrap.min.js') }}
	{{ HTML::script('assets/hr-dashboard/js/parallax.min.js') }}
	{{ HTML::script('assets/hr-dashboard/js/angular.min.js') }}
	{{ HTML::script('assets/hr-dashboard/process/clinic_login.js') }}
	<!-- {{ HTML::script('assets/hr-dashboard/js/main.js') }} -->
</html>
