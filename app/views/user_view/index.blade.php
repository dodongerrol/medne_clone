<!DOCTYPE html>
<html ng-app="app">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='-1'>
	<meta http-equiv='pragma' content='no-cache'>
	<title ng-bind="$state.$current.data.pageTitle">Login</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<!-- <link rel="shortcut icon" href="{{ asset('images/mednefits.ico') }}" type="image/ico">
	<link href="{{ asset('user/css/intlTelInput.css') }}" rel="stylesheet">
	<link href="{{ asset('user/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ asset('user/css/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
	<link href="{{ asset('user/css/font-awesome.min.css') }}" rel="stylesheet">
	<link href="{{ asset('user/css/animations.css') }}" rel="stylesheet">
	<link href="{{ asset('user/css/fullcalendar.css') }}" rel="stylesheet">
	<link href="{{ asset('user/css/loading-bar.min.css') }}" rel="stylesheet">
	<link href="{{ asset('user/css/jquery-confirm.min.css') }}" rel="stylesheet"> -->
	{{ HTML::style('user_platform/css/intlTelInput.css') }}
	{{ HTML::style('user_platform/css/bootstrap.min.css') }}
	{{ HTML::style('user_platform/css/bootstrap-material-datetimepicker.css') }}
	{{ HTML::style('user_platform/css/font-awesome.min.css') }}
	{{ HTML::style('user_platform/css/animations.css') }}
	{{ HTML::style('user_platform/css/fullcalendar.css') }}
	{{ HTML::style('user_platform/css/loading-bar.min.css') }}
	{{ HTML::style('user_platform/css/jquery-confirm.min.css') }}
	<link rel="stylesheet" href="<?php echo $server; ?>/user_platform/css/style.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/user_platform/css/responsive.css?_={{ $date->format('U') }}">
</head>
<body>
	<div id="page" ng-controller="HeaderCtrl as user">
		<div ui-view="fixed-menu-custom"></div>
		<div ui-view="header"></div>
		<div ui-view="side-menu"></div>
		<div ui-view="main"></div>
	</div>

	<div class="modal fade" id="loading" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	  <div class="modal-dialog modal-dialog-center modal-sm" role="document">
	    <div class="modal-content">
	    	<div class="trackPopup-container" style="background: #EDEDEE;">
	    		<img src="../user_platform/img/loading.gif" style="width: 50%;">
	    		<span>Please wait.</span>
	    	</div>
	    </div>
	  </div>
	</div>

	
</body>
	<!-- production -->
	<script type="text/javascript" src="<?php echo $server; ?>/production/vendor.min.js?_={{ $date->format('U') }}"></script>
	<!-- <script type="text/javascript" src="{{ asset('js/angular-cache-buster.js')}}"></script> -->
	{{ HTML::script('user_platform/js/angular-cache-buster.js') }}
	<script type="text/javascript" src="<?php echo $server; ?>/production/app.min.js?_={{ $date->format('U') }}"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBvgNNMxcgAQ5UMFn29PCB9oeJUhKeM7XI"></script>

	<script type="text/javascript">
		var $document = $(document),
			$element = $('header');

		$document.scroll(function() {
			if ($document.scrollTop() >= 52) {
				// $element.addClass(className);
				$( '.fixed-menu' ).css({
					'top' : '0px',
					'position': 'fixed'
				});
			} else {
				// $element.removeClass(className);
				$( '.fixed-menu' ).css({
					'top' : '52px',
					'position': 'absolute'
				});
			}
		});
	</script>
</html>

