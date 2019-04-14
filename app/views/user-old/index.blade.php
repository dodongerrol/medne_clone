<!DOCTYPE html>
<html ng-app="app">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='-1'>
	<meta http-equiv='pragma' content='no-cache'>
	<title ng-bind="$state.current.data.pageTitle">Login</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<link rel="shortcut icon" href="{{ asset('assets/images/Medicloud-Favicon_16x16px.ico') }}" type="image/ico">
	{{ HTML::style('assets/userWeb/css/intlTelInput.css') }}
	{{ HTML::style('assets/userWeb/css/bootstrap.min.css') }}
	{{ HTML::style('assets/userWeb/css/bootstrap-material-datetimepicker.css') }}
	{{ HTML::style('assets/userWeb/css/font-awesome.min.css') }}
	{{ HTML::style('assets/userWeb/css/animations.css') }}
	{{ HTML::style('assets/userWeb/css/fullcalendar.css') }}
	{{ HTML::style('assets/userWeb/css/loading-bar.min.css') }}
	{{ HTML::style('assets/userWeb/css/jquery-confirm.min.css') }}
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/userWeb/css/style.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/userWeb/css/responsive.css?_={{ $date->format('U') }}">
</head>
<body>
	
	<div id="page">
		<div ui-view="header"></div>
		<div ui-view="side-menu"></div>
		<div ui-view="main"></div>
	</div>

	<div class="modal fade" id="loading" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	  <div class="modal-dialog modal-dialog-center modal-sm" role="document">
	    <div class="modal-content">
	    	<div class="trackPopup-container" style="background: #EDEDEE;">
	    		<img src="/assets/userWeb/img/loading.gif" style="width: 50%;">
	    		<span>Please wait.</span>
	    	</div>
	    </div>
	  </div>
	</div>
</body>
	<!-- production -->
	<!-- <script type="text/javascript" src="<?php echo $server; ?>/production/vendor.min.js?_={{ $date->format('U') }}"></script> -->
	<!-- <script type="text/javascript" src="<?php echo $server; ?>/production/app.min.js?_={{ $date->format('U') }}"></script> -->
	
	<!-- dev -->
	{{ HTML::script('assets/userWeb/js/jquery.min.js') }}
	{{ HTML::script('assets/userWeb/js/moment.min.js') }}
	{{ HTML::script('assets/userWeb/js/moment-range.min.js') }}
	{{ HTML::script('assets/userWeb/js/fullcalendar.js') }}
	{{ HTML::script('assets/userWeb/js/bootstrap.min.js') }}
	{{ HTML::script('assets/userWeb/js/angular.min.js') }}
	{{ HTML::script('assets/userWeb/js/unsavedChanges.js') }}
	{{ HTML::script('assets/userWeb/js/bootstrap-material-datetimepicker.js') }}
	{{ HTML::script('assets/userWeb/js/angular-animate.min.js') }}
	{{ HTML::script('assets/userWeb/js/ng-file-upload-shim.js') }}
	{{ HTML::script('assets/userWeb/js/ng-file-upload.min.js') }}
	{{ HTML::script('assets/userWeb/js/angular-ui-router.min.js') }}
	{{ HTML::script('assets/userWeb/js/main.js') }}
	{{ HTML::script('assets/userWeb/js/ng-image-appear.js') }}
	{{ HTML::script('assets/userWeb/js/loading-bar.min.js') }}
	{{ HTML::script('assets/userWeb/js/jquery-confirm.min.js') }}
	{{ HTML::script('assets/userWeb/js/intlTelInput.min.js') }}
	{{ HTML::script('assets/userWeb/js/utils.js') }}
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/controllers/mainCtrl.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/authService.js?_={{ $date->format('U') }}">	
	</script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/benefitService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/favouriteService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/appointmentService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/walletService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/profileService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/clinicService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/app.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/calendar.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/home.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/benefits.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/appointments.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/favourites.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/profile.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/wallet.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/clinic-maps.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/appointment-create.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/ecommerce.js?_={{ $date->format('U') }}"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmjDjs6EkwoyJKZCn1pMZ5dYvcHsRPXPk"></script>
</html>