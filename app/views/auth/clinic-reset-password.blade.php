<!DOCTYPE html>
<html ng-app="clinic">
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
<body clinic-directive>
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
		<div class="col-sm-12 col-md-12 col-lg-12">
			<img src="{{ $server }}/assets/hr-dashboard/img/Mednefits Logo V1.svg" class="center-block login-logo">
			<h2 class="text-center text-below-image">for health provider</h2>

			<p ng-if="!password_success" class="text-center" style="font-size: 20px;color: #222;margin: 40px 0;">We received your reset password request.<br>Please enter your new password!</p>

			<form ng-if="!password_success" class="med-form" ng-submit="changePassword( forgot_password_data )" id="form-forgot" style="margin-bottom: 100px;">
				<input type="hidden" value="{{ $userid }}" id="clinic-id">
				<div class="form-group">
					<input type="password" name="" class="form-control med-input" placeholder="New Password" ng-model="forgot_password_data.new_password" required>
				</div>
				<div class="form-group">
					<input type="password" name="" class="form-control med-input" placeholder="Confirm Password" ng-model="forgot_password_data.new_password2" required>
				</div>
				<p ng-if="new_password_error" class="text-center" style="color: #e61111">Password did not match!</p>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block med-button" id="login-btn">CHANGE PASSWORD</button>
				</div>
			</form>
			
			<div ng-if="password_success" class="success-content">
				<img src="{{ $server }}/assets/hr-dashboard/img/verified.png" class="center-block login-logo" style="height: 80px;">
				<p class="text-center" style="font-size: 20px;color: #222;margin: 15px 0;">Your password has been reset successfully!<br>Now <a href="/provider-portal-login">login</a> with your new password.</p>
			</div>
		</div>
	</div>
</body>


	{{ HTML::script('assets/hr-dashboard/js/jquery.min.js') }}
	{{ HTML::script('assets/hr-dashboard/js/bootstrap.min.js') }}
	{{ HTML::script('assets/hr-dashboard/js/angular.min.js') }}
	{{ HTML::script('assets/clinic/js/clinicDirective.js') }}
</html>