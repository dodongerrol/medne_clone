<!DOCTYPE html>
<html ng-app="app">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='-1'>
	<meta http-equiv='pragma' content='no-cache'>
	<title>Care Plan</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
	<link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/ico">
	<!-- <link rel="shortcut icon" href="{{ asset('assets/images/Medicloud-Favicon_16x16px.ico') }}" type="image/ico"> -->


	{{ HTML::style('assets/care-plan/css/intlTelInput.css') }}
	{{ HTML::style('assets/care-plan/css/date-picker.css') }}
	{{ HTML::style('assets/care-plan/css/jquery-ui.css') }}
	{{ HTML::style('assets/care-plan/css/jquery.timepicker.css') }}
	{{ HTML::style('assets/care-plan/css/fullcalendar.css') }}
	{{ HTML::style('assets/care-plan/css/font-awesome.min.css') }}
	{{ HTML::style('assets/care-plan/css/animations.css') }}
	{{ HTML::style('assets/care-plan/css/loading-bar.min.css') }}
	{{ HTML::style('assets/care-plan/css/jquery-confirm.min.css') }}
	{{ HTML::style('assets/care-plan/css/materialize.min.css') }}
	{{ HTML::style('assets/care-plan/css/sweetalert.css') }}
	{{ HTML::style('assets/care-plan/css/materialize.clockpicker.css') }}

	{{ HTML::style('assets/care-plan/css/style.css') }}
	{{ HTML::style('assets/care-plan/css/responsive.css') }}
</head>
<body>
	<div id="page">
		<div ui-view="header"></div>
		<div ui-view="main"></div>
	</div>

	
</body>


	{{ HTML::script('assets/care-plan/js/calendar/moment/moment.js') }}
	{{ HTML::script('assets/care-plan/js/calendar/moment/min/moment-with-locales.min.js') }}
	{{ HTML::script('assets/care-plan/js/jquery.min.js') }}
	{{ HTML::script('assets/care-plan/js/moment-timezone-with-data-2010-2020.min.js') }}
	{{ HTML::script('assets/care-plan/js/calendar/jquery-ui/jquery-ui.min.js') }}
	{{ HTML::script('assets/care-plan/js/lodash.min.js') }}
	{{ HTML::script('assets/care-plan/js/jquery.timepicker.min.js') }}
	{{ HTML::script('assets/care-plan/js/jquery.blockUI.min.js') }}
	{{ HTML::script('assets/care-plan/js/datepicker.js') }}
	{{ HTML::script('assets/care-plan/js/moment-range.min.js') }}
	{{ HTML::script('assets/care-plan/js/fullcalendar.js') }}
	{{ HTML::script('assets/care-plan/js/materialize.clockpicker.js') }}
	{{ HTML::script('assets/care-plan/js/angular.min.js') }}
	{{ HTML::script('assets/care-plan/js/angular-cache-buster.js') }}
	{{ HTML::script('assets/care-plan/js/unsavedChanges.js') }}
	{{ HTML::script('assets/care-plan/js/angular-animate.min.js') }}
	{{ HTML::script('assets/care-plan/js/angular-materialize.min.js') }}
	{{ HTML::script('assets/care-plan/js/ng-file-upload-shim.js') }}
	{{ HTML::script('assets/care-plan/js/ng-file-upload.min.js') }}
	{{ HTML::script('assets/care-plan/js/angular-ui-router.min.js') }}
	{{ HTML::script('assets/care-plan/js/angular-local-storage.min.js') }}
	{{ HTML::script('assets/care-plan/js/main.js') }}
	{{ HTML::script('assets/care-plan/js/ng-image-appear.js') }}
	{{ HTML::script('assets/care-plan/js/loading-bar.min.js') }}
	{{ HTML::script('assets/care-plan/js/jquery-confirm.min.js') }}
	{{ HTML::script('assets/care-plan/js/intlTelInput.min.js') }}
	{{ HTML::script('assets/care-plan/js/utils.js') }}
	{{ HTML::script('assets/care-plan/js/materialize.min.js') }}
	{{ HTML::script('assets/care-plan/js/sweetalert.min.js') }}

	<!-- {{ HTML::script('assets/care-plan/process/app.js') }} -->

	<!-- App -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/care-plan/process/app.js?_={{ $date->format('U') }}"></script>

	<!-- SERVICES -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/care-plan/process/services/carePlanService.js?_={{ $date->format('U') }}"></script>
	<!-- {{ HTML::script('assets/care-plan/process/services/carePlanService.js') }} -->

	<!-- FACTORIES -->
	<!-- {{ HTML::script('assets/care-plan/process/factories/carePlanFactory.js') }} -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/care-plan/process/factories/carePlanFactory.js?_={{ $date->format('U') }}"></script>


	<!-- DIRECTIVES -->
		
	<!-- {{ HTML::script('assets/care-plan/process/directives/survey.js') }} -->
	<!-- {{ HTML::script('assets/care-plan/process/directives/stepsDirective.js') }} -->
	<!-- {{ HTML::script('assets/care-plan/process/directives/planDirective.js') }} -->
	<!-- {{ HTML::script('assets/care-plan/process/directives/compDetailsDirective.js') }} -->
	<!-- {{ HTML::script('assets/care-plan/process/directives/paymentDirective.js') }} -->
	<!-- {{ HTML::script('assets/care-plan/process/directives/employeeDetailsDirective.js') }} -->
	<!-- {{ HTML::script('assets/care-plan/process/directives/callback.js') }} -->

	<script type="text/javascript" src="<?php echo $server; ?>/assets/care-plan/process/directives/survey.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/care-plan/process/directives/stepsDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/care-plan/process/directives/planDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/care-plan/process/directives/compDetailsDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/care-plan/process/directives/paymentDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/care-plan/process/directives/employeeDetailsDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/care-plan/process/directives/callback.js?_={{ $date->format('U') }}"></script>

	<!--  -->

	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmjDjs6EkwoyJKZCn1pMZ5dYvcHsRPXPk"></script>
	<script src="https://js.stripe.com/v3/"></script>
	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
	
	<script type="text/javascript">
		// console.log(window.location.origin);
	</script>
</html>

