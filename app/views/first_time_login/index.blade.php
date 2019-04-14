<!DOCTYPE html>
<html ng-app="app">
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-78188906-2"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-78188906-2');
	</script>
	<!-- <base href="/company-benefits-dashboard/"></base> -->
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Member First Time Login</title>
	<link rel="shortcut icon" href="<?php echo $server; ?>/assets/new_landing/images/favicon.ico" type="image/ico">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

	<link rel="stylesheet" href="<?php echo $server; ?>/assets/member-first-time-login/css/font-awesome.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/member-first-time-login/css/jquery.toast.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/member-first-time-login/css/bootstrap.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/member-first-time-login/css/bootstrap-datetimepicker.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/member-first-time-login/css/sweetalert.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/member-first-time-login/css/pre-loader.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/member-first-time-login/css/style.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/member-first-time-login/css/custom.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/member-first-time-login/css/fonts.css?_={{ $date->format('U') }}">

</head>
<body>
	<div id="main-section-container">
		<div ui-view="main"></div>
	</div>
</body>

	<script type="text/javascript" src="<?php echo $server; ?>/assets/member-first-time-login/js/jquery.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/member-first-time-login/js/moment.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/member-first-time-login/js/moment-with-locales.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/member-first-time-login/js/jquery.toast.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/member-first-time-login/js/bootstrap.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/member-first-time-login/js/bootstrap-datetimepicker.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/member-first-time-login/js/angular.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/member-first-time-login/js/angular-ui-router.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/member-first-time-login/js/angular-cache-buster.js?_={{ $date->format('U') }}"></script>



	<!-- ROUTES -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/member-first-time-login/process/app.js?_={{ $date->format('U') }}"></script>

	<!-- Directives -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/member-first-time-login/process/directives/firstTimeLoginDirective.js?_={{ $date->format('U') }}"></script>


	<!-- Controllers -->



	<!-- Services -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/member-first-time-login/process/services/authService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/member-first-time-login/process/services/memberService.js?_={{ $date->format('U') }}"></script>

	<!-- Factories -->
</html>
