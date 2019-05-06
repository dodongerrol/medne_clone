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
	<title>Mednefits: Claim</title>
	<link rel="shortcut icon" href="<?php echo $server; ?>/assets/new_landing/images/favicon.ico" type="image/ico">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/bootstrap.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/bootstrap-datepicker.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/daterangepicker.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/css/font-awesome.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/claim/css/style.css?_={{ $date->format('U') }}">

</head>
<body>
	@include('common.home_header')
	<div ui-view="main"></div>
</body>

	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/calendar/moment/moment.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/calendar/moment/min/moment-with-locales.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/jquery.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/bootstrap.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/bootstrap-datepicker.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/daterangepicker.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/moment-timezone-with-data-2010-2020.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/moment-range.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/angular.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/angular-ui-router.min.js?_={{ $date->format('U') }}"></script>

	<script type="text/javascript" src="<?php echo $server; ?>/assets/claim/js/app.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/claim/js/claimDirective.js?_={{ $date->format('U') }}"></script>
</html>
