<!DOCTYPE html>
<html >
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
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='-1'>
	<meta http-equiv='pragma' content='no-cache'>
	<meta name="google-site-verification" content="WPMtMxZexNrXxam6mJ0ZnfXmyk5JRJ8z6nwNVHe7JK8" />
	<title>Mednefits: Modern Employees Digital Benefits</title>
	<link rel="shortcut icon" href="../assets/hr-dashboard/img/icons/favicon.ico" type="image/ico">

	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/bootstrap.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/font-awesome.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/fonts.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/style.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/custom.css?_={{ $date->format('U') }}">
	<!-- {{ HTML::style('assets/hr-dashboard/css/responsive.css') }} -->

</head>
<body>
	<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<img src="../assets/hr-dashboard/img/mednefits_logo_v3_(blue)_LARGE.png" class="center-block intro-login-logo">
			<div class="intro-login-container">
				<div class="white-space-10"></div>
				<p class="text-center weight-300 font-24 color-gray">Login as</p>
				<div class="white-space-20"></div>
				<div class="white-space-10"></div>
				<div class="platforms-box">
					<a href="../business-portal-login" class="link-box">
						<div class="intro-login-content-box">
							<div class="img-wrapper" >
								<img src="/images/login icons/Business.png" class="center-block" style="width: 35px;">
							</div>
							<div class="login-desc">
								<h4>Business</h4>
								<p>Manage your Mednefits business account here.</p>
							</div>
						</div>
					</a>
					<a href="../provider-portal-login" class="link-box">
						<div class="intro-login-content-box">
							<div class="img-wrapper" style="padding-right: 18px;">
								<img src="/images/login icons/Health_Provider.png" class="center-block" style="width: 45px;">
							</div>	
							<div class="login-desc">
								<h4>Health Provider</h4>
								<p>View claims or check member eligibility here.</p>
							</div>
						</div>
					</a>
					<a href="../member-portal-login" class="link-box">
						<div class="intro-login-content-box">
							<div class="img-wrapper" style="padding-right: 18px;">
								<img src="/images/login icons/Member2.png" class="center-block" style="width: 48px;">
							</div>
							<div class="login-desc">
								<h4>Member</h4>
								<p>Manage your personal health coverage here.</p>
							</div>
						</div>
					</a>
				</div>
				<!-- <div class="intro-text-below-box">
					<h4 class="text-center">The company benefits dashboard<br>allows HR to better manage<br>company's benefits.</h4>
				</div>
				<div class="intro-text-below-box">
					<h4 class="text-center">The health partner platform allows<br>health professional to better<br>manage healthcare.</h4>
				</div> -->
			</div>
		</div>
	</div>
</body>


	{{ HTML::script('assets/hr-dashboard/js/jquery.min.js') }}
	{{ HTML::script('assets/hr-dashboard/js/bootstrap.min.js') }}
	{{ HTML::script('assets/hr-dashboard/js/parallax.min.js') }}
	<!-- {{ HTML::script('assets/hr-dashboard/js/main.js') }} -->
</html>