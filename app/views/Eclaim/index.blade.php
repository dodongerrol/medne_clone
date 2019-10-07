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
	<!-- <base href="/member-portal-login/"></base> -->
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>E-Claim</title>
	<link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/ico">

	<!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto" rel="stylesheet"> -->
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/bootstrap.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/bootstrap-slider.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/font-awesome.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/jquery.toast.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/sweetalert.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/intlTelInput.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/jquery-ui.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/bootstrap-datetimepicker.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/daterangepicker.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/css/offline-theme-default.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/css/offline-language-english.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/style.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/custom.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/responsive.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/fonts.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/pre-loader.css?_={{ $date->format('U') }}">
	<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async="async"></script>
</head>
<body style="position: relative;" ng-controller="checkController as home">
	<div ui-view="main" ></div>

	<div class="main-loader">
		<div class="preloader-container">
			<img src="../assets/e-claim/img/loading_logo.png" style="width: 65%;">
			<div class="white-space-20"></div>
			<div class="preloader-box">
				<div class="preloader-bar"></div>
			</div>
		</div>
	</div>

	<div class="circle-loader" hidden>
		<div class="preloader-container">
			<div class="white-space-50"></div>
			<div class="preloader-wrapper big active">
		    <div class="spinner-layer spinner-blue-only">
		      <div class="circle-clipper left">
		        <div class="circle"></div>
		      </div><div class="gap-patch">
		        <div class="circle"></div>
		      </div><div class="circle-clipper right">
		        <div class="circle"></div>
		      </div>
		    </div>
		  </div>
		</div>
	</div>

	<div style="padding-top: 160px;border-radius: 0;" class="modal fade" id="global_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	    <div class="modal-dialog" role="document" style="width: 450px;">
	      <div class="modal-content" style="">
	      	<div class="modal-header" style="border-bottom: none;padding: 10px 15px;">
		      </div>
	        <div class="modal-body" style="padding: 0 30px 30px 30px;">
	        	<p class="text-center">
	        		<span class="warning-icon">
	        			<i class="fa fa-exclamation"></i>
	        		</span>
	        	</p>
	          <p id="global_message" class="text-center weight-700" style="color: #666;margin-top: 20px;">Message goes here.</p>
	          <p class="text-center weight-700" id="login-status" hidden>
	          	<a href="/member-portal-login" class="btn btn-primary" style="background: #1667AC!important">Login Again</a>
	          </p>
	        </div>
	      </div>
	    </div>
	</div>
</body>

	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/calendar/moment/moment.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/calendar/moment/min/moment-with-locales.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/jquery.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/bootstrap.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/bootstrap-slider.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/intlTelInput.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/utils.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/jquery.toast.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/moment-timezone-with-data-2010-2020.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/moment-range.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/plotly.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/angular.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/angular-cache-buster.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/angular-ui-router.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/angular-local-storage.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/js/FileSaver.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/js/json-export-excel.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/sweetalert.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/ng-file-upload-shim.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/ng-file-upload.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/bootstrap-datetimepicker.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/daterangepicker.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/jquery-ui.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/jszip.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/js/offline.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/exif.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/angular-fix-image-orientation.js?_={{ $date->format('U') }}"></script>

	<!-- {{ HTML::script('assets/e-claim/process/app.js') }} -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/process/app.js?_={{ $date->format('U') }}"></script>

	<!-- Directives -->
	<!-- {{ HTML::script('assets/e-claim/process/directives/loginDirective.js') }} -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/process/directives/loginDirective.js?_={{ $date->format('U') }}"></script>
	<!-- {{ HTML::script('assets/e-claim/process/directives/empDetailsDirective.js') }} -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/process/directives/empDetailsDirective.js?_={{ $date->format('U') }}"></script>
	<!-- {{ HTML::script('assets/e-claim/process/directives/createEclaimDirective.js') }} -->
	<!-- {{ HTML::script('assets/e-claim/process/directives/eclaimSubmitDirective.js') }} -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/process/directives/eclaimSubmitDirective.js?_={{ $date->format('U') }}"></script>
	<!-- {{ HTML::script('assets/e-claim/process/directives/activityDirective.js') }} -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/process/directives/activityDirective.js?_={{ $date->format('U') }}"></script>
	<!-- Controllers -->
	<!-- {{ HTML::script('assets/e-claim/process/controllers/checkCtrl.js') }} -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/process/controllers/checkCtrl.js?_={{ $date->format('U') }}"></script>
	<!-- Services -->
	<!-- {{ HTML::script('assets/e-claim/process/services/eclaimService.js') }} -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/process/services/eclaimService.js?_={{ $date->format('U') }}"></script>
	<!-- {{ HTML::script('assets/e-claim/process/services/authService.js') }} -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/process/services/authService.js?_={{ $date->format('U') }}"></script>

	<!-- Factories -->
	<!-- {{ HTML::script('assets/e-claim/process/factories/storageFactory.js') }} -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/process/factories/storageFactory.js?_={{ $date->format('U') }}"></script>
</html>

