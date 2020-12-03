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
		<div class="col-sm-12 col-md-12 col-lg-12 new-account">
			
			<a href="/company-benefits-dashboard-login">
			<img src="../assets/hr-dashboard/img/Mednefits Logo V1.svg" class="center-block login-logo">
			</a>
			<h2 class="text-center text-below-image">for business</h2>

			@if($token)
			<!-- <p ng-if="!password_success" class="text-center" style="font-size: 20px;color: #222;margin: 40px 0;">We received your reset password request.<br>Please enter your new password!</p>
			<form ng-if="!password_success" class="med-form" ng-submit="changePassword( forgot_password_data )" id="form-forgot" style="margin-bottom: 100px;">
				<div class="form-group">
					<input type="hidden" id="hr-id" value="{{ $hr_id }}">	
					<input type="password" name="" class="form-control med-input" placeholder="New Password" ng-model="forgot_password_data.new_password" required>
				</div>
				<div class="form-group">
					<input type="password" name="" class="form-control med-input" placeholder="Confirm Password" ng-model="forgot_password_data.new_password2" required>
				</div>
				<p ng-if="new_password_error" class="text-center" style="color: #e61111">Password did not match!</p>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block med-button" id="login-btn">CHANGE PASSWORD</button>
				</div>
			</form> -->

			<!-- new account login -->

			<p ng-if="!password_success" class="text-center" style="font-size: 20px;color: #222;margin: 40px 0;">We received your reset password request.<br>Please enter your new password!</p>
			<img src="../assets/images/lock.png" class="lock" alt="lock">
			<form ng-if="!password_success" class="med-form" ng-submit="changePassword( forgot_password_data )" id="form-forgot" style="margin-bottom: 100px;">
				<div class="form-group" style="align-items: center; display: flex; position:relative;">
					<input type="hidden" id="hr-id" value="">
					<label for="password"  style="padding-right: 20px; margin-bottom: 22px;">Password</label>
					<input type="@{{inputType ? 'text' : 'password'}}" name="" class="form-control med-input" placeholder="Enter Your Password" ng-model="forgot_password_data.new_password" required>
					<img src="../assets/images/showhidepass.png" class="eye" alt="eye" style="cursor:pointer" ng-click="inputType = !inputType">
				</div>
				<div class="form-group" style="align-items: center; display: flex;">
					<label for="confirm" style="padding-right: 34px; margin-bottom: 22px;">Confirm</label>
					<input type="@{{inputType ? 'text' : 'password'}}" name="" class="form-control med-input" placeholder="Confirm Your Password" ng-model="forgot_password_data.new_password2" required>
				</div>
				<p ng-if="new_password_error" class="text-center" style="color: #e61111">Password did not match!</p>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block med-button" id="login-btn">Create</button>
				</div>
			</form>

			<!-- end new account login -->
			
			<div ng-if="password_success" class="success-content">
				<img src="../assets/hr-dashboard/img/verified.png" class="center-block login-logo" style="height: 80px;">
				<p class="text-center" style="font-size: 20px;color: #222;margin: 15px 0;">Your password has been successfully reset.<br> You may now <a href="/company-benefits-dashboard-login">sign in to Mednefits</a> with your new password.</p>
			</div>
			@endif
			@if(!$token)
			<div class="success-content">
				<p class="text-center" style="font-size: 20px;color: #222;margin: 15px 0;">Your Reset Token has expired.</p>
			</div>
			@endif
		</div>
	</div>

</body>


	{{ HTML::script('assets/hr-dashboard/js/jquery.min.js') }}
	{{ HTML::script('assets/hr-dashboard/js/bootstrap.min.js') }}
	{{ HTML::script('assets/hr-dashboard/js/angular.min.js') }}
	{{ HTML::script('assets/hr-dashboard/process/hr_login.js') }}
</html>