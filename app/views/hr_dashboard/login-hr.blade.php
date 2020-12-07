<!DOCTYPE html>
<html ng-app="hr">

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
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Mednefits: Modern Employees Digital Benefits</title>
	<link rel="shortcut icon" href="assets/hr-dashboard/img/icons/favicon.ico" type="image/ico">

	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
	<!-- <script>
      (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: "ca-pub-8344843655918366",
        enable_page_level_ads: true
      });
	</script> -->

	<!-- Facebook Pixel Code -->
	<script>!function (f, b, e, v, n, t, s) { if (f.fbq) return; n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments) }; if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = []; t = b.createElement(e); t.async = !0; t.src = v; s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s) }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js'); fbq('init', '165152804138364'); fbq('track', 'PageView');</script>
	<noscript><img height="1" width="1" src="https://www.facebook.com/tr?id=165152804138364&ev=PageView(44 B)https://www.facebook.com/tr?id=165152804138364&ev=PageView&noscript=1" /></noscript>
	<!-- End Facebook Pixel Code -->
	{{ HTML::style('assets/hr-dashboard/css/bootstrap.min.css') }}
	{{ HTML::style('assets/hr-dashboard/css/font-awesome.min.css') }}
	{{ HTML::style('assets/hr-dashboard/css/style.css') }}
	<!-- {{ HTML::style('assets/hr-dashboard/css/responsive.css') }} -->

</head>

<body ng-cloak login-section>
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
		<!-- <div class="col-sm-12 col-md-12 col-lg-12">
			<img src="assets/hr-dashboard/img/Mednefits Logo V1.svg" class="center-block login-logo">
			<h2 class="text-center text-below-image">for business</h2>
			<form class="med-form" ng-submit="loginHr()">
				<div class="form-group">
					<input type="email" name="email" class="form-control med-input" placeholder="Email Address"
						ng-model="login_details.email" required>
				</div>
				<div class="form-group">
					<input type="password" class="form-control med-input" placeholder="Enter password"
						ng-model="login_details.password" required style="margin-bottom: 15px">
				</div>
				<div class="checkbox">
					<label style="color: #777;font-size: 15px;">
						<input type="checkbox" ng-model="login_details.signed_in" style="margin-top: 5px;"> Stay signed
						in
					</label>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block med-button" id="login-btn">Log in</button>
				</div>
				<span ng-if="ng_fail">*Please check your login credentials</span>
				<a href="/company-benefits-dashboard-forgot-password" class="forgot-password pull-right">Forgot
					password?</a>
			</form>
		</div> -->
		<!-- New Account Feature -->
		<div class="col-sm-12 col-md-12 col-lg-12 new-account">
			<img src="assets/hr-dashboard/img/Mednefits Logo V1.svg" class="center-block login-logo">
			<h2 class="text-center text-below-image">for business</h2>
			<!-- <span ng-if="!showPassword" class="no-account">Don't have an account? <a href="#">Sign up</a>.</span> -->
			<form class="med-form">
				<div ng-if="!showPassword" class="form-group">
					<label for="email">Email</label>
					<input type="email" name="email" class="form-control med-input" ng-class="{'not-activated': login_details.status == 'not activated' || login_details.status == 'not-exist'}" placeholder="Enter Email Address"
						ng-model="login_details.email" ng-model-options="{debounce: 1000}" ng-change="enableContinue(login_details.email)" required>
				</div>
				<div ng-if="showPassword" class="form-group">
					<label for="password">Password</label>
					<input type="password" class="form-control med-input" placeholder="Enter password"
						ng-model="login_details.password" required style="margin-bottom: 15px">
				</div>
				<div ng-if="showPassword" class="checkbox">
					<label style="color: #000;font-size: 15px;">
						<input type="checkbox" ng-model="login_details.signed_in" style="margin-top: 5px;"> Stay signed
						in
					</label>
				</div>
				<div class="form-group">
					<button ng-if="!showPassword" type="submit" class="btn btn-info btn-block med-button" ng-class="{'disabled': login_details.status == false, 'not-activated': login_details.status == 'not activated' || login_details.status == 'not-exist' }" id="login-btn" ng-disabled="login_details.status == false || login_details.status == 'not activated' || login_details.status == 'not-exist'" ng-click="showPasswordToggle()">Continue</button>
					<button ng-if='showPassword' type="submit" class="btn btn-info btn-block med-button" id="login-btn" ng-click="loginHr()" ng-disabled="!login_details.password">Sign in</button>
				</div>
				<span ng-if="ng_fail">*Please check your login credentials</span>
				<a ng-if="showPassword" href="/company-benefits-dashboard-forgot-password" class="forgot-password pull-right">Forgot
					password?</a>
				
				<div class="not-activated" ng-if="login_details.status === 'not activated'">
				Oops! An email to activate your account has been sent on <span ng-bind="login_details.date_created"></span>. Please click the link inside to activate your account. 
				<br> <br>
				Or <a ng-click="resend_hr_activation()">resend</a> the email now.
				</div>

				<div class="not-activated" ng-if="login_details.status === 'not-exist'">
				Your email has not been signed up with Mednefits. 

				</div>
			</form>
		</div>
		<!-- End New Account Feature -->
	</div>

</body>


{{ HTML::script('assets/hr-dashboard/js/calendar/moment/moment.js') }}
{{ HTML::script('assets/hr-dashboard/js/calendar/moment/min/moment-with-locales.min.js') }}
{{ HTML::script('assets/hr-dashboard/js/moment-timezone-with-data-2010-2020.min.js') }}
{{ HTML::script('assets/hr-dashboard/js/calendar/moment/min/moment-with-locales.min.js') }}
{{ HTML::script('assets/hr-dashboard/js/jquery.min.js') }}
{{ HTML::script('assets/hr-dashboard/js/bootstrap.min.js') }}
{{ HTML::script('assets/hr-dashboard/js/parallax.min.js') }}
{{ HTML::script('assets/hr-dashboard/js/angular.min.js') }}
{{ HTML::script('assets/hr-dashboard/process/hr_login.js') }}
</html>