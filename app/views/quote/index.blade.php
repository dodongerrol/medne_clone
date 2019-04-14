<!DOCTYPE html>
<html ng-app="app">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='-1'>
	<meta http-equiv='pragma' content='no-cache'>
	<title>
		Get Quote
	</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="shortcut icon" href="{{ asset('assets/images/Medicloud-Favicon_16x16px.ico') }}" type="image/ico">
	{{ HTML::style('assets/quote/css/bootstrap.min.css') }}
	{{ HTML::style('assets/quote/css/bootstrap-material-datetimepicker.css') }}
	{{ HTML::style('assets/quote/css/font-awesome.min.css') }}
	{{ HTML::style('assets/quote/css/animations.css') }}
	{{ HTML::style('assets/quote/css/fullcalendar.css') }}
	{{ HTML::style('assets/quote/css/loading-bar.min.css') }}
	{{ HTML::style('assets/css/animate.css') }}
	{{ HTML::style('assets/quote/css/style.css') }}
</head>
<body>
	<div ui-view="header"></div>
	<div ui-view="main"></div>
	
</body>
	{{ HTML::script('assets/quote/js/jquery.min.js') }}
	{{ HTML::script('assets/quote/js/moment.min.js') }}
	{{ HTML::script('assets/quote/js/moment-range.min.js') }}
	{{ HTML::script('assets/quote/js/fullcalendar.js') }}
	{{ HTML::script('assets/quote/js/bootstrap.min.js') }}
	{{ HTML::script('assets/quote/js/angular.min.js') }}
	{{ HTML::script('assets/quote/js/unsavedChanges.js') }}
	{{ HTML::script('assets/quote/js/bootstrap-material-datetimepicker.js') }}
	{{ HTML::script('assets/quote/js/angular-animate.min.js') }}
	{{ HTML::script('assets/quote/js/ng-file-upload-shim.js') }}
	{{ HTML::script('assets/quote/js/ng-file-upload.min.js') }}
	{{ HTML::script('assets/quote/js/angular-ui-router.min.js') }}
	{{ HTML::script('assets/quote/js/main.js') }}
	{{ HTML::script('assets/quote/js/ng-image-appear.js') }}
	{{ HTML::script('assets/quote/js/loading-bar.min.js') }}
	{{ HTML::script('assets/js/wow.min.js') }}
	{{ HTML::script('assets/quote/process/controllers/mainCtrl.js') }}
	{{ HTML::script('assets/quote/process/app.js') }}
	{{ HTML::script('assets/quote/process/directives/home.js') }}
	<script type="text/javascript">
		new WOW().init();
	</script>
</html>