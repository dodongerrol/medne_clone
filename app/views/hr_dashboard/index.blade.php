<!DOCTYPE html>
<html ng-app="app">
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<!-- <script async src="https://www.googletagmanager.com/gtag/js?id=UA-78188906-2"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-78188906-2');
	</script> -->
	<!-- Facebook Pixel Code -->
	<script>!function (f, b, e, v, n, t, s) { if (f.fbq) return; n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments) }; if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = []; t = b.createElement(e); t.async = !0; t.src = v; s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s) }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js'); fbq('init', '165152804138364'); fbq('track', 'PageView');</script>
	<noscript><img height="1" width="1" src="https://www.facebook.com/tr?id=165152804138364&ev=PageView(44 B)https://www.facebook.com/tr?id=165152804138364&ev=PageView&noscript=1" /></noscript>
	<!-- End Facebook Pixel Code -->
	<!-- <base href="/company-benefits-dashboard/"></base> -->
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Mednefits: Modern Employees Digital Benefits</title>
	<link rel="shortcut icon" href="<?php echo $server; ?>/assets/new_landing/images/favicon.ico" type="image/ico">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/bootstrap.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/materialize.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/bootstrap-slider.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/font-awesome.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/jquery.toast.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/bootstrap-datepicker.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/daterangepicker.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/sweetalert.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/intlTelInput.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/css/offline-theme-default.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/css/offline-language-english.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/pre-loader.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/style.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/custom.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/fonts.css?_={{ $date->format('U') }}">
	<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async="async"></script>



	<!-- <script src="//fast.appcues.com/57952.js"></script> -->

</head>
<body>
	<div id="main-section-container">
		<div ui-view="navigation" ng-controller="checkCtrls"></div>
		<div class="main-ui-view" ui-view="main"></div>
		<div ui-view="modal"></div>
		<div ui-view="modal_2"></div>
		<div ui-view="modal_3"></div>
		<div ui-view="modal_4"></div>
		<div ui-view="modal_5"></div>
		<div ui-view="modal_6"></div>
	</div>
	<!-- <div ng-controller="resetCtrl as reset">
		<div ui-view="reset"></div>
	</div> -->

<!-- 	<div style="padding-top: 160px;border-radius: 0;" class="modal fade" id="global_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 450px;">
      <div class="modal-content" style="">
      	<div class="modal-header" style="border-bottom: none;padding: 10px 15px;">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      </div>
        <div class="modal-body" style="padding: 0 30px 30px 30px;">
        	<p class="text-center">
        		<span class="warning-icon">
        			<i class="fa fa-exclamation"></i>
        		</span>
        	</p>
          <p id="global_message" class="text-center weight-700" style="color: #666;margin-top: 20px;">Message goes here.</p>
        </div>
      </div>
    </div>
	</div> -->

	<div style="padding-top: 160px;border-radius: 0;" class="modal fade" id="global_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 450px;">
      <div class="modal-content" style="">
      	<div class="modal-header" style="border-bottom: none;padding: 10px 15px;">
	        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
	      </div>
        <div class="modal-body" style="padding: 0 30px 30px 30px;">
        	<p class="text-center">
        		<span class="warning-icon">
        			<i class="fa fa-exclamation"></i>
        		</span>
        	</p>
          <p id="global_message" class="text-center weight-700" style="color: #666;margin-top: 20px;">Message goes here.</p>
          <p class="text-center weight-700" id="login-status" hidden>
          	<a href="/company-benefits-dashboard-login" class="btn btn-primary" style="background: #1667AC!important">Login Again</a>
          </p>
        </div>
      </div>
    </div>
	</div>

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
		<div class="preloader-container" style="width: auto !important;">
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
		  <p class="text-center export-emp-details-message color-black2 weight-700" style="margin-top: 30px;" hidden>
		  	Fetching employee details. 
		  	May take a while depending on the number of employees.
		  </p>
		  <p class="text-center download-receipt-message color-black2 weight-700" style="margin-top: 30px;" hidden>
		  	Downloading <span class="ctr">0</span> of <span class="total">0</span>
		  </p>
		</div>
	</div>
</body>

	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/calendar/moment/moment.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/calendar/moment/min/moment-with-locales.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/jquery.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/jquery.toast.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/bootstrap.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/bootstrap3-typeahead.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/bootstrap-datepicker.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/daterangepicker.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/moment-timezone-with-data-2010-2020.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/moment-range.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/plotly.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/angular.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/angular-cache-buster.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/angular-ui-router.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/angular-local-storage.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/ng-file-upload-shim.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/ng-file-upload.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/angular-bootstrap3-typeahead.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/js/FileSaver.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/js/json-export-excel.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/jspdf.debug.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/html2canvas.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/jquery.printElement.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/sweetalert.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/parallax.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/bootstrap-slider.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/jszip.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/js/offline.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/exif.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/angular-fix-image-orientation.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/intlTelInput.js?_={{ $date->format('U') }}"></script>

	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/app.js?_={{ $date->format('U') }}"></script>
	<!-- Directives -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/introDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/dashboardDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/enrollmentMethodDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/enrollmentOptionsDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/benefitsTiersDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/webInputDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/webPreviewDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/editDetailsDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/companyContactsDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/employeeOverviewDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/employeeListDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/replaceExternalDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/refundListDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/prepareDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/uploadExcelDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/paymentRate.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/payCredit.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/document.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/localNetworkDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/creditAllocationDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/activityDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/eclaimDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/statementDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/teamBenefitsTierDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/firstTimeLoginDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/settingsDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/blockHealthPartnersDirective.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/directives/capPerVisitDirective.js?_={{ $date->format('U') }}"></script>
	<!-- Controllers -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/controllers/checkCtrl.js?_={{ $date->format('U') }}"></script>
	<!-- Services -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/services/hrServices.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/services/authService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/services/dependentsService.js?_={{ $date->format('U') }}"></script>
	<!-- Factories -->
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/factories/dashboardFactory.js?_={{ $date->format('U') }}"></script>

</html>
