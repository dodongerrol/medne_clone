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
					<h3 class="tooltip-phone-email-details">+65 6254 7889</h3>
					<h3 class="tooltip-phone-email-details">Mon - Fri 9:30am to 6:30pm</h3>
					<br>
					<h3 class="tooltip-phone-email-title">Drop us a note, anytime</h3>
					<h3 class="tooltip-phone-email-details">happiness@mednefits.com</h3>
				</div>
			</a>
		</div>
		<div class="col-sm-12 col-md-12 col-lg-12">
			<img src="assets/hr-dashboard/img/mednefits_logo_v3_(blue)_LARGE.png" class="center-block login-logo">
			<h2 class="text-center text-below-image">for business</h2>
			<form class="med-form" ng-submit="loginHr()" id="form-forgot">
				<div class="form-group">
					<input type="email" name="email" class="form-control med-input" placeholder="Email Address" ng-model="login_details.email" required>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block med-button" id="login-btn">Reset Password</button>
				</div>
			</form>
			<div class="form-group" id="success-message" style="display: none;">
				<button type="button" class="btn btn-info btn-block med-button">Password Reset Details sent to your Email Account</button>
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