<!DOCTYPE html>
<html ng-app="hr">
<head>
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='-1'>
	<meta http-equiv='pragma' content='no-cache'>
	<title>Mednefits: Modern Employees Digital Benefits</title>
	<link rel="shortcut icon" href="assets/hr-dashboard/img/icons/favicon.ico" type="image/ico">

	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

	{{ HTML::style('assets/hr-dashboard/css/bootstrap.min.css') }}
	{{ HTML::style('assets/hr-dashboard/css/font-awesome.min.css') }}
	{{ HTML::style('assets/hr-dashboard/css/style.css') }}
	<!-- {{ HTML::style('assets/hr-dashboard/css/responsive.css') }} -->

</head>
<body forgot-section>
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
					<h3 class="tooltip-phone-email-details">support@mednefits.com</h3>
				</div>
			</a>
		</div>
		<div class="col-sm-12 col-md-12 col-lg-12 new-account" id="forgot-password">
			<img src="assets/hr-dashboard/img/Mednefits Logo V1.svg" class="center-block login-logo">
			<h2 class="text-center text-below-image">for business</h2>
			<form class="med-form" ng-submit="loginHr()" id="form-forgot">
				<div class="form-group">
					<label for="email">Email</label>
					<input type="email" name="email" class="form-control med-input" placeholder="Enter HR Admin's work email address" ng-model="login_details.email" required>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block med-button" id="login-btn">Reset Password</button>
				</div>
			</form>
			<div class="form-group new-account" id="success-message" style="display: none;">
				
				<div class="success-container">
					<img src="assets/hr-dashboard/img/new-account/envelope.svg"  class="center-block">

					<div class="success-item">
						We’ve sent an email to <b><span ng-bind="login_details.email"></span></b>. <br>
						Click the link in that email to reset your password. <br>
						Didn’t receive an email from us? <span><a style="cursor:pointer" ng-click="loginHr()">Resend email.</a></span>
					</div>
				</div>
				<!-- <button type="button" class="btn btn-info btn-block med-button">Password Reset Details sent to your Email Account</button> -->
			</div>
		</div>
	</div>

</body>


	{{ HTML::script('assets/hr-dashboard/js/jquery.min.js') }}
	{{ HTML::script('assets/hr-dashboard/js/bootstrap.min.js') }}
	{{ HTML::script('assets/hr-dashboard/js/parallax.min.js') }}
	{{ HTML::script('assets/hr-dashboard/js/angular.min.js') }}
	{{ HTML::script('assets/hr-dashboard/process/hr_login.js') }}
</html>