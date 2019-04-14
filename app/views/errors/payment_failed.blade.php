

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
	<!-- <link rel="shortcut icon" href="{{ asset('images/mednefits.ico') }}" type="image/ico"> -->
	<link rel="shortcut icon" href="{{ asset('assets/images/Medicloud-Favicon_16x16px.ico') }}" type="image/ico">

	<!-- <link href="{{ asset('care-plan/css/intlTelInput.css') }}" rel="stylesheet">
	<link href="{{ asset('care-plan/css/date-picker.css') }}" rel="stylesheet">
	<link href="{{ asset('care-plan/css/jquery-ui.css') }}" rel="stylesheet">
	<link href="{{ asset('care-plan/css/jquery.timepicker.css') }}" rel="stylesheet">
	<link href="{{ asset('care-plan/css/fullcalendar.css') }}" rel="stylesheet">
	<link href="{{ asset('care-plan/css/font-awesome.min.css') }}" rel="stylesheet">
	<link href="{{ asset('care-plan/css/animations.css') }}" rel="stylesheet">
	<link href="{{ asset('care-plan/css/loading-bar.min.css') }}" rel="stylesheet">
	<link href="{{ asset('care-plan/css/jquery-confirm.min.css') }}" rel="stylesheet">
	<link href="{{ asset('care-plan/css/materialize.min.css') }}" rel="stylesheet">
	<link href="{{ asset('care-plan/css/sweetalert.css') }}" rel="stylesheet">
	<link href="{{ asset('css/materialize.clockpicker.css') }}" rel="stylesheet"> -->


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
		<div>
			
		</div>
			<div>

				<div id="steps-container" class="main-container" steps-directive>
					<div class="header-wrapper ">
						<div class="row no-margin-bottom">
							<div class="col s5">
								<img src="{{ URL::asset('/assets/care-plan/img/mednefits logo v3 (blue) LARGE.png') }}" class="responsive-img" style="width: 150px;">
							</div>
							<div class="col s7 right-align header-opt" >
								<!-- <div class="white-space-10"></div> -->
								<button id="payTop-btn" class="btn btn-large blue white-text font-20 radius-8" hidden>
									PAY S$<span>0</span> / YR 
									<div class="icon-wrapper" hidden>
										<div class="preloader-wrapper big active">
										    <div class="spinner-layer spinner-white-only">
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
								</button>
								<a href="/uat/care-plan#/steps/payment"><i class="material-icons font-25" style="position: relative;top: 15px">arrow_back</i></a>
								
								<div class="drop" style="position: relative;">
									<a href="javascript:void(0)"><i class="material-icons font-25" style="position: relative;top: 15px">help_outline</i></a>

									<div class="help">
										<div class="arrow-drop"></div>
										<div class="help-container">
											<h4 class="color-blue">We're here to help.</h4>
											<p class="color-dark-grey no-margin-bottom">You may ring us</p>
											<p class="color-gray no-margin">+65 6254 7889</p>
											<p class="color-gray no-margin">Mon - Fri 10:00 to 19:00</p>
											<p class="color-dark-grey no-margin-bottom">Drop us a note, anytime</p>
											<p class="color-gray no-margin">happiness@mednefits.com</p>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row no-margin-bottom">
							<div class="col s12">
								<nav class="steps-nav center-align">
								    <div class="nav-wrapper inline-block">
								      <div class="col s12 step-li">
								        <a id="intro-step" class="breadcrumb done">Introduction</a>
								        <a id="plan-step" class="breadcrumb ">Plan</a>
								        <a id="comp-step" class="breadcrumb">Company Details</a>
								        <a id="payment-step" class="breadcrumb active">Payment</a>
								        <a id="emp-step" class="breadcrumb">Employee Details</a>
								      </div>
								    </div>
								  </nav>

							</div>
						</div>
					</div>

					<div class="step-wrapper">
						<div class="payment-wrapper center-align">

							<div id="fail-form">
								<div class="row">
									<div class="col s12">
										<div class="white-space-50"></div>
										<h1 class="font-40 weight-500 color-dark-grey">Unsuccessful</h1>
									</div>
								</div>

								<div class="row">
									<div class="col s12">
										<div class="white-space-20"></div>
										<i class="material-icons font-80 red-text text-lighten-1">cancel</i>
										<div class="white-space-20"></div>
									</div>
								</div>

								<div class="row margin-bottom-30">
									<div class="col s12">
										<h4 class="color-dark-grey weight-500">Sorry we are unable to process your payment.<br>Kindly please click the back button to try again</h4>
										<h6 class="color-dark-grey weight-500">{{$error}}</h6>
									</div>
								</div>

								<div class="row margin-bottom-30">
							        <div class="input-field col s12 center-align">
							          <button id="btn-four" class="btn btn-large blue white-text font-20 radius-8" onclick="window.history.back();">BACK</button>
							        </div>
								</div>	
							</div>
						</div>
					</div>
				</div>

			<!-- <div class="step-wrapper">
				<div class="payment-wrapper center-align">

					<div id="fail-form">
						<div class="row">
							<div class="col s12">
								<div class="white-space-50"></div>
								<h1 class="font-40 weight-500 color-dark-grey">Unsuccessful</h1>
							</div>
						</div>

						<div class="row">
							<div class="col s12">
								<div class="white-space-20"></div>
								<i class="material-icons font-80 red-text text-lighten-1">cancel</i>
								<div class="white-space-20"></div>
							</div>
						</div>

						<div class="row margin-bottom-30">
							<div class="col s12">
								<h4 class="color-dark-grey weight-500">Sorry we are unable to process your payment.<br>Kindly please click the back button to try again</h4>
								<h5 class="color-dark-grey weight-500">{{$error}}</h5>
							</div>
						</div>

						<div class="row margin-bottom-30">
					        <div class="input-field col s12 center-align">
					          <button id="btn-four" class="btn btn-large blue white-text font-20 radius-8" onclick="window.history.back();">BACK</button>
					        </div>
						</div>	
					</div>
				</div>
			</div> -->

			
		</div>
	</div>

	
</body>

</html>




