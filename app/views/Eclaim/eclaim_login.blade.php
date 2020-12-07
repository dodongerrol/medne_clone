<!DOCTYPE html>
<html ng-app="eclaim">
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-78188906-2"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-78188906-2');
	</script>
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- <meta charset="UTF-8" /> -->
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="-1" />
	<title>E-Claim</title>
	<link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/ico">

	<!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto" rel="stylesheet"> -->
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/bootstrap.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/font-awesome.min.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/style.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/custom.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/responsive.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/fonts.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/e-claim/css/pre-loader.css?_={{ $date->format('U') }}">
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/sweetalert.css?_={{ $date->format('U') }}">

	<style type="text/css">
		/* For Firefox */
		input.mobile-num-input {
		    -moz-appearance:textfield;
		}
		/* Webkit browsers like Safari and Chrome */
		input.mobile-num-input::-webkit-inner-spin-button,
		input.mobile-num-input::-webkit-outer-spin-button {
		    -webkit-appearance: none;
		    margin: 0;
		}
	</style>
</head>
<body eclaim-login>
	
	<div class="login-wrapper">
		<div class="login-need-help-container">
			<a href="#" class="pull-right need-help-text tooltips">
				<h3>Need help?</h3>
				<div class="tooltip-container">
					<h3 class="tooltip-title">We're here to help.</h3>
					<h3 class="tooltip-phone-email-title">You may ring us</h3>
					<h3 class="tooltip-phone-email-details">+65 3163 5403</h3>
					<h3 class="tooltip-phone-email-details">+60 330 995 774</h3>
					<h3 class="tooltip-phone-email-details">Mon - Fri 9:00am to 6:00pm</h3>
					<br>
					<h3 class="tooltip-phone-email-title">Drop us a note, anytime</h3>
					<h3 class="tooltip-phone-email-details">support@mednefits.com</h3>
				</div>
			</a>
		</div>
		<!-- <div class="col-sm-12 col-md-12 col-lg-12" id="login-container">
			<div class="login-container-header">
				<img src="../assets/hr-dashboard/img/Mednefits Logo V1.svg" class="center-block login-logo">
				<h2 class="text-center text-below-image">for member</h2>
			</div>
			<div class="notification-wrapper">
				<div class="notification-container">
					<div>
						<img src="./assets/images/danger.png">
					</div>
					<div>
						<div class="notification-text">Notification: User ID Change</div>
						<p>NRIC/FIN and email address will no longer be valid as your user ID. <br>
						Please click <a class="here-text" ng-click="goToUpdateDetails()">here</a> to change your user ID to your mobile number.</p>
					</div>
				</div>
			</div>
			<form class="med-form" ng-submit="login()">
				<div class="form-group">
					valid-number pattern="[0-9]*" type="tel" comment ni
					<input type="text" name="text" class="form-control med-input mobile-num-input" placeholder="Mobile Number" ng-model="email" />
				</div>
				<div class="form-group">
					<input type="password" class="form-control med-input" placeholder="Enter password" ng-model="password"  style="margin-bottom: 15px">
				</div>
				<div class="checkbox">
			    <label>
			      <input type="checkbox"> Stay signed in
			    </label>
			  </div>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block med-button" id="login-btn">Log in</button>
				</div>
				<span ng-if="invalid_credentials">*Please check your login credentials</span>
				<a href="javascript:void(0)" class="forgot-password pull-right" ng-click="showForgotPassword()">Forgot password?</a>
			</form>
		</div> -->

		<!-- new account login feature -->
		<div class="col-sm-12 col-md-12 col-lg-12 new-account" id="login-container">
			<div class="login-container-header">
				<img src="../assets/hr-dashboard/img/Mednefits Logo V1.svg" class="center-block login-logo">
				<h2 class="text-center text-below-image">for member</h2>
			</div>
			<!-- <div class="notification-wrapper">
				<div class="notification-container">
					<div>
						<img src="./assets/images/danger.png">
					</div>
					<div>
						<div class="notification-text">Notification: User ID Change</div>
						<p>NRIC/FIN and email address will no longer be valid as your user ID. <br>
						Please click <a class="here-text" ng-click="goToUpdateDetails()">here</a> to change your user ID to your mobile number.</p>
					</div>
				</div>
			</div> -->
			<form ng-if="false" class="med-form">
				<div ng-if="!showPasswordInput" class="form-group">
					<!-- valid-number pattern="[0-9]*" type="tel" -->
					<label for="mobile">Mobile</label>
					<input type="text" name="text" class="form-control med-input mobile-num-input" placeholder="Mobile Number" ng-model="email" ng-model-options="{debounce: 1000}" ng-change="removeDisabledBtn(email,password)" />
				</div>
				<div ng-if="showPasswordInput" class="form-group">
					<label for="mobile">Password</label>
					<input type="password" class="form-control med-input" placeholder="Enter password" ng-model="password" ng-model-options="{debounce: 1000}" ng-change="removeDisabledBtn(email,password)"  style="margin-bottom: 15px">
				</div>
				<div ng-if="showPasswordInput" class="checkbox">
			    <label>
			      <input type="checkbox"> Stay signed in
			    </label>
			  </div>
				<div  class="form-group">
					<button ng-if="!showPasswordInput" type="none" class="btn btn-info btn-block med-button" ng-class="{'disabled': disabledContinue}" id="login-btn" ng-click="goToPassword()" ng-disabled="disabledContinue">Continue</button>
					<button ng-if="showPasswordInput" type="submit" class="btn btn-info btn-block med-button" id="login-btn" ng-click="login()" ng-class="{'disabled': disabledSignIn}" ng-disabled="disabledSignIn">Sign in</button>
				</div>
				<span ng-if="invalid_credentials">*Please check your login credentials</span>
				<a ng-if="true" href="javascript:void(0)" class="forgot-password pull-right" ng-click="showForgotPassword()">Forgot password?</a>
			</form>
			
			<div class="new-login-container med-form">
				<div ng-if="true">
					<div ng-if="!showContinueInput" class="form-group mobile-country-code-wrapper">
						<label for="mobile">Mobile</label>
						<div class="mobile-input-wrapper display-flex">
							<div ng-click="selectCountry()" class="form-control med-input">
								<img ng-if="country_code_value == 65" src="../assets/images/flag/singapore-flag.png">
								<img ng-if="country_code_value == 60" src="../assets/images/flag/malaysia-flag.png">
								<span class="dp-flex-ai">
									<!-- <span>+</span> -->
									+<input ng-model="country_code_value">
								</span>
							</div>
							<input type="number" name="text" class="form-control med-input mobile-num-input" ng-class="{'error' : mobileValidation == true }" placeholder="Enter Mobile Number" ng-model="mobile_number" ng-model-options="{debounce: 1000}" ng-change="checkMobileNum(mobile_number)" />
							

							<div ng-if="toggleSelectCountry" class="country-code-wrapper">
								<span>Select Country</span>
								<div ng-click="countrySelector(65)" class="country-row-details display-flex">
									<span class="country-info display-flex">
										<img src="../assets/images/flag/singapore-flag.png">
										<span class="country-name">Singapore</span>
										<span class="country-code">+65</span>
									</span>
									<span ng-if="country_code_value == 65" class="blue-check-active">
										<img src="../assets/images/blue-check.svg">
									</span>	
								</div>
								<div ng-click="countrySelector(60)" class="country-row-details display-flex">
									<span class="country-info display-flex">
										<img src="../assets/images/flag/malaysia-flag.png">
										<span class="country-name">Malaysia</span>
										<span class="country-code">+60</span>
									</span>
									<span ng-if="country_code_value == 60" class="blue-check-active">
										<img src="../assets/images/blue-check.svg">
									</span>
								</div>
								<!-- for future flag ui -->
								<!-- <div ng-repeat="list in countryData">
									<img src="../assets/images/flag/@{{list.image}}" style="height: 20px; width: 30px;">
									<div ng-bind="list.name"></div>
								</div> -->
							</div>
						</div>
					</div>
					<div ng-if="showContinueInput && !showPostalCodeInput">
						<div ng-if="checkMemberData.member_activated == 0 && !showPasswordInputInOtp" class="otp-container form-group">
							<label for="otp">Please enter the OTP we’ve sent to your phone number.</label>
							<div>
								<input type="number" name="text" class="form-control med-input mobile-num-input" ng-class="{'error' : otpValidation == true }" placeholder="Enter Your OTP" ng-model="otp_number" ng-model-options="{debounce: 1000}" ng-change="checkOTP(otp_number)" />
							</div>
						</div>
						<div ng-if="checkMemberData.member_activated == 0 && showPasswordInputInOtp == true" class="form-group">
							<label for="password">Please create your password</label>
							<div>
								<input type="password" name="text" class="form-control med-input mobile-num-input" ng-class="{'error' : passwordNotMatch }" placeholder="Enter Your Password" ng-model="new_password" ng-model-options="{debounce: 1000}" ng-change="removeDisable('new_password',new_password)" />
							</div>
							<div>
								<input type="password" name="text" class="form-control med-input mobile-num-input" ng-class="{'error' : passwordNotMatch }" placeholder="Confirm Your Password" ng-model="confirm_new_password" ng-model-options="{debounce: 1000}" ng-change="removeDisable('confirm_new_password',confirm_new_password)" />
							</div>
						</div>
						<div ng-if="checkMemberData.member_activated == 1 && showContinueInput">
							<div class="form-group">
								<label for="mobile">Password</label>
								<input type="password" class="form-control med-input mobile-num-input" placeholder="Enter password" ng-class="{'error' : passwordSignInNotMatch }" ng-model="new_password" ng-model-options="{debounce: 1000}" ng-change="checkPassword(new_password)"  style="margin-bottom: 15px">
							</div>
							<div ng-if="true" class="checkbox stay-signed-container">
								<label>
									<input type="checkbox"> Stay signed in
								</label>
							</div>
						</div>
					</div>
					<div ng-if="showPostalCodeInput" class="form-group">
						<label class="pass-created-text" for="password">Your password has been created.</label>
						<label for="password">Next, register your Postal Code.</label>
						<p class="postal-code-text">*Postal Code is required to determine health providers in your proximity.</p>
						<div>
							<input type="number" name="text" class="form-control med-input mobile-num-input" placeholder="Enter your Postal Code" ng-model="postal_code" ng-model-options="{debounce: 1000}" ng-change="postalCode(postal_code)" />
						</div>
					</div>
				</div>

				<div ng-if="false">
					<div ng-if="false" class="form-group">
						<label for="mobile">Mobile</label>
						<div>
							<input type="text" name="text" class="form-control med-input mobile-num-input" placeholder="Enter Mobile Number" ng-model="email" ng-model-options="{debounce: 1000}" ng-change="removeDisabledBtn(email,password)" />
						</div>
					</div>
					<div ng-if="false">
						<div class="form-group">
							<label for="mobile">Password</label>
							<input type="password" class="form-control med-input" placeholder="Enter password" ng-model="password" ng-model-options="{debounce: 1000}" ng-change="removeDisabledBtn(email,password)"  style="margin-bottom: 15px">
						</div>
						<div ng-if="true" class="checkbox stay-signed-container">
							<label>
								<input type="checkbox"> Stay signed in
							</label>
						</div>
					</div>
					<div ng-if="true" class="form-group">
						<label for="postal-code">Before accessing your account, please register your Postal Code.	</label>
						<p class="postal-code-text">*Postal Code is required to determine health providers in your proximity.</p>
						<div>
							<input type="text" name="text" class="form-control med-input mobile-num-input" placeholder="Enter your Postal Code" ng-model="email" ng-model-options="{debounce: 1000}" ng-change="removeDisabledBtn(email,password)" />
						</div>
					</div>
				</div>
				<div  class="footer-btn form-group">
					<button ng-if="!showContinueInput" ng-click="continueButton(mobile_number)" ng-class="{'disabled': disabledContinue}" type="none" class="btn btn-info btn-block med-button">Continue</button>
					<button ng-if="checkMemberData.member_activated == 0 && showContinueInput == true && !showPasswordInputInOtp" ng-class="{'disabled': disabledVerify}" ng-click="verifyOTP(otp_number)" type="submit" class="btn btn-info btn-block med-button">Verify</button>
					<button ng-if="checkMemberData.member_activated == 0 && showPasswordInputInOtp == true && !showPostalCodeInput" ng-class="{'disabled': disableCreate }" ng-click="createPassword()" type="none" class="btn btn-info btn-block med-button">Create</button>
					<!-- <button ng-if="showPasswordInputInOtp && !showPostalCodeInput" ng-click="createPassword()" type="none" class="btn btn-info btn-block med-button">Create</button> -->
					<!-- <button ng-if="otpStatus == 0 && showContinueInput == true && !showPostalCodeInput" ng-click="createPassword()" type="none" class="btn btn-info btn-block med-button">Create</button> -->
					<!-- <button ng-if="otpStatus == 1 || showContinueInput == true && !showPostalCodeInput && showPasswordInputInOtp && !showPostalCodeInput && checkMemberData.Password == 1" ng-class="{'disabled': disabledSignIn }" ng-click="signInPassword()" type="submit" class="btn btn-info btn-block med-button">Sign in</button> -->
					<div ng-if="checkMemberData.member_activated == 1 && showContinueInput && !showPostalCodeInput">
						<button ng-class="{'disabled': disabledSignIn }" ng-click="signInPassword()" type="submit" class="btn btn-info btn-block med-button">Sign in</button>
						<a href="javascript:void(0)" class="forgot-password pull-right" ng-click="showForgotPassword()">Forgot password?</a>
					</div>
					<button ng-if="checkMemberData.member_activated == 0 && showPostalCodeInput" type="submit" ng-click="completeSignIn('postal')" ng-class="{'disabled' : disabledDone}" class="btn btn-info btn-block med-button">Complete and Sign in</button>
					<button ng-if="checkMemberData.member_activated == 1 && showPostalCodeInput" type="submit" ng-click="completeSignIn('postal')" ng-class="{'disabled' : disabledDone}" class="btn btn-info btn-block med-button">Done</button>
					<div ng-if="checkMemberData.member_activated == 0 && showContinueInput == true && !showPasswordInputInOtp" class="resend-otp-container">
						<span>Don't receive OPT? <a ng-click="resendOtp()" class="resend-otp-text">Resend OTP</a>.</span>
					</div>
					<div class="mobile-message-container" ng-if="checkMobileData.status == false">
						<span ng-bind="checkMobileData.message"></span>
					</div>
					<div class="mobile-message-container" ng-if="otpData.status == false">
						<span ng-bind="otpData.message"></span>
					</div>
					<div class="mobile-message-container" ng-if="passwordNotMatch">
						<span>Sorry, your password and confirmation password do not match.</span>
					</div>
					<div class="mobile-message-container" ng-if="disableCreateText">
						<span ng-bind="createNewPasswordData.message"></span>
					</div>
					<div class="mobile-message-container" ng-if="checkMemberPassword.status == false">
						<span ng-bind="checkMemberPassword.message"></span>
					</div>
				</div>
			</div>
		</div>
		<!-- end new account login -->

		<div class="col-sm-12 col-md-12 col-lg-12 new-account" id="forgot-password" hidden>
			<div class="login-container-header">
				<img src="../assets/hr-dashboard/img/Mednefits Logo V1.svg" class="center-block login-logo">
				<h2 class="text-center text-below-image">for member</h2>
			</div>
			<form class="med-form" ng-submit="resetPassword()">
				<div class="form-group">
					<label for="email">Mobile or Email</label>
					<input type="text" name="email" class="form-control med-input" placeholder="Enter Mobile Number or Email Address" ng-model="login_details.email" required/>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block med-button" id="reset-password">Reset Password</button>
				</div>
				<span ng-bind="callback.text"></span>
				<a href="javascript:void(0)" class="forgot-password pull-right" ng-click="showLogin()">Login</a>
			</form>
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

</body>

	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/jquery.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/bootstrap.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/js/angular.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/sweetalert.min.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/e-claim/process/eclaim_login.js?_={{ $date->format('U') }}"></script>

</html>
