var login = angular.module('eclaim', []);

login.run(function($http) {
  $http.defaults.headers.common.Authorization = window.localStorage.getItem('token');
});

login.factory('serverUrl',[
    function factory(){
      return {
        url: window.location.origin + '/',
      }
    }
]);

login.factory("eclaimSettings", function($http, serverUrl) {
  var eclaimFactory = {};

  eclaimFactory.resetPassword = function(data) {
    return $http.post(serverUrl.url + "v2/auth/forgotpassword", data);
  };

  return eclaimFactory;
});

login.directive('eclaimLogin', [
	"$http",
	"serverUrl",
  "eclaimSettings",
	function directive($http, serverUrl, eclaimSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link(scope, element, attributeSet) {
				console.log('running loginSection');
				scope.invalid_credentials = false;
        scope.login_details = {};
        var introLoader_trap;
        var loading_trap;
        scope.email = '';
        scope.password = '';
        scope.forgot_password_data = {};
        scope.new_password_error = false;
        scope.password_success = false;
        scope.showPasswordInput = false;
        scope.disabledContinue = true;
        scope.disabledSignIn = true;
        scope.disabledVerify = true;
        scope.disableCreate = true;
        scope.passwordNotMatch = false;

        scope.deviceOs = null;
        scope.getOs = function(){
          var userAgent = window.navigator.userAgent,
              platform = window.navigator.platform,
              macosPlatforms = ['Macintosh', 'MacIntel', 'MacPPC', 'Mac68K'],
              windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
              iosPlatforms = ['iPhone', 'iPad', 'iPod'],
              os = null;

          if (macosPlatforms.indexOf(platform) !== -1) {
            os = 'Mac OS';
          } else if (iosPlatforms.indexOf(platform) !== -1) {
            os = 'iOS';
          } else if (windowsPlatforms.indexOf(platform) !== -1) {
            os = 'Windows';
          } else if (/Android/.test(userAgent)) {
            os = 'Android';
          } else if (!os && /Linux/.test(platform)) {
            os = 'Linux';
          }

          // return os;
          scope.deviceOs = os;
        }

        scope.getOs();

        scope.goToPassword = function () {
          scope.showPasswordInput = !scope.showPasswordInput;
        }

        scope.removeDisabledBtn = function (email,password) {

          scope.email = email;
          scope.password = password;

          console.log(scope.email, scope.password);

          if (email) {
            scope.disabledContinue = false;
          } else {
            scope.disabledContinue = true;
          }

          if (password) {
            scope.disabledSignIn = false;
          } else {
            scope.disabledSignIn = true;
          }

          
        }

        scope.goToUpdateDetails = function(){
          // if( scope.deviceOs == 'iOS' ){
          //   window.location.assign( serverUrl.url + 'app/update_user_id_web?platform=web&os=' + ( scope.deviceOs ).toLowerCase() );
          // }else{
          //   window.open( serverUrl.url + 'app/update_user_id_web?platform=web&os=' + ( scope.deviceOs ).toLowerCase() );
          // }
          // window.location.href = serverUrl.url + 'app/update_user_id_web?platform=web&os=' + ( scope.deviceOs ).toLowerCase();
          window.location.href = serverUrl.url + 'app/update_user_id_web?platform=web';
          // localStorage.setItem('isFromWeb', true);
        }

        scope.changePassword = function( data ){
          if( data.new_password == data.new_password2 ){
            scope.new_password_error = false;

            var data = {
              new_password : data.new_password
            }

            // $http.post(serverUrl.url + 'hr/forgot/company-benefits-dashboard', data)
            //  .success(function(response){
            //    console.log(response);
            //  });

            scope.password_success = true;
          }else{
            scope.new_password_error = true;
          }
        }

        scope.showForgotPassword = function() {
          $("#login-container").hide();
          $("#forgot-password").show();
        };

        scope.showLogin = function() {
          $("#forgot-password").hide();
          $("#login-container").show();
          scope.showPasswordInput = false;
          
        };

        scope.login = function(){

          console.log(scope.email, scope.password);
          
          if( !scope.email || !scope.password ){
            swal('Ooops!', 'Mobile Number and Password is required', 'error');
            return false;
          }
          scope.showLoading();
          var data = {
            email: scope.email,
            password: scope.password
          };

          $http.post(serverUrl.url + 'app/e_claim/login', data)
	          .then(function(response) {
	            scope.hideLoading();
	            if (response.data.status == true) {
	              scope.invalid_credentials = false;
	              scope.email = null;
	              scope.password = null;
	              // $state.go("home");
	              window.location.href = window.location.origin + '/member-portal#/home';
                window.localStorage.setItem('token_member', response.data.token);
	            } else {
                scope.invalid_credentials = true;
                scope.email = null;
                scope.password = null;
                scope.showPasswordInput = false;
                scope.removeDisabledBtn();
                // if(  ){
                  // swal('Ooops!', response.data.message, 'error');
                // }else{
                  swal({ 
                    html: true, 
                    title: '<span style="font-size: 22px;">Your User ID or Password is Incorrect.</span>', 
                    text: "<p style='text-align:left;margin:30px'><span>1. Make sure you have updated your User ID to your Mobile Number.</span><br><br>" +
                    "<span>2. If you still can't login, reset your password.</span></p>" 
                  });
                // }
	            }
	          })
	          .catch(function(error) {
	            scope.hideLoading();
	            swal('Ooops!', 'Connection Losts! Please check your internet connection.', 'error');
	          });
        };

        scope.resetPassword = function() {
          var data = {
            email: scope.login_details.email
          };
          scope.showLoading();
          eclaimSettings.resetPassword(data).then(function(response) {
            console.log(response);
						
            if(response.data.status) {
              swal('Success!', response.data.message, 'success');
              scope.showLogin();
            }else{
              swal('Error!', response.data.message, 'error');
            }
            scope.hideLoading();
          })
          .catch(function(err){
            swal('Ooops!', 'Connection Losts! Please check your internet connection.', 'error');
          });
        };

        scope.country_code_value = 65;
        scope.country_active = false;
        scope.toggleSelectCountry = false;
        scope.showContinueInput = false;
        scope.showPasswordInputInOtp = false;
        scope.showPostalCodeInput = false;
        scope.mobileValidation = false;
        scope.otpValidation = false;
        scope.disableCreateText = false;
        scope.disabledSignIn = true;
        scope.disabledDone = true;
        // testing for flag 
        // scope.countryData = [
        //   {
        //     name: 'Singapore',
        //     image: 'singapore-flag.png',
        //   },
        //   {
        //     name: 'Malaysia',
        //     image: 'singapore-flag.png',
        //   },
        // ];

        scope.countrySelector = function ( code ) {
          scope.country_code_value = code;
          scope.country_active = false;
          scope.toggleSelectCountry = false;
          if ( code == 65 ) {
            console.log('singapore');
            scope.country_active = true;
          } else {
            scope.country_active = false;
          }
        }

        scope.selectCountry = function () {
          scope.toggleSelectCountry = scope.toggleSelectCountry ? false : true;
        }

        scope.getOtpStatus = function () {
          console.log(scope.checkMemberData.user_id);
          $http.get(serverUrl.url + 'employee/check_user_otp_status?user_id=' + scope.checkMemberData.user_id)
	          .then(function(response) {
              console.log(response);
              scope.otpStatus = response.data.status;
            })
        }
        
        scope.continueButton = function (num) {
          scope.showContinueInput = true;
        }

        scope.checkMobileNum = async function (num) {
          scope.mobile_number = num
          // scope.showContinueInput = true;
          scope.showLoading();
          await $http.get(serverUrl.url + 'employee/check_member?mobile=' + scope.mobile_number)
	          .then(async function(response) {
              console.log(response);
              scope.checkMobileData = response.data;
              scope.checkMemberData = response.data.data;
              console.log(scope.checkMemberData);
              // scope.checkMemberData.Password = 0;
              if (response.data.status == true ) {
                scope.disabledContinue = false;
                scope.mobileValidation = false;
                scope.hideLoading();
                if ( scope.checkMemberData.member_activated == 0 ) {
                  await scope.getOtpStatus();
                }
              } else {
                scope.disabledContinue = true;
                scope.mobileValidation = true;
                scope.hideLoading();
              }
            })
        }

        scope.verifyOTP = function (  ) {
          scope.showPasswordInputInOtp = true;
        }

        scope.checkOTP = function ( opt_num ) {
          let data = {
            otp_code: opt_num,
            user_id: scope.checkMemberData.user_id,
          }
          scope.showLoading();
          // $http.post(serverUrl.url + 'employee/validate_otp_web', data)
          $http.post(serverUrl.url + 'v2/auth/validate-otp-mobile', data)
	          .then(function(response) {
              console.log(response);
              scope.otpData = response.data;
              if (response.data.status == true) {
                scope.hideLoading();
                scope.disabledVerify = false;
                scope.otpValidation = false;
              } else {
                scope.hideLoading();
                scope.disabledVerify = true; 
                scope.otpValidation = true;
              }
            })
        }

        scope.resendOtp = function () {
          let data = {
            mobile: scope.mobile_number,
            mobile_country_code: scope.country_code_value,
          }
          console.log(data);
          scope.showLoading();
          $http.post(serverUrl.url + 'v2/auth/send-otp-mobile',data)
	          .then(function(response) {
              console.log(response);
              scope.hideLoading();
              swal('Success!', response.data.message, 'success');
            })
        }

        scope.createPassword = function () {
          // scope.showPostalCodeInput = true;
          console.log(scope.new_password);
          console.log(scope.confirm_new_password);

          let data = {
            password: scope.new_password,
            password_confirm: scope.confirm_new_password,
            user_id: scope.checkMemberData.user_id,
          }

          console.log(data);
          scope.showLoading();
          $http.post(serverUrl.url + 'employee/create_new_password_member', data)
	          .then(function(response) {
              console.log(response);
              scope.createNewPasswordData = response.data;              
              if (response.data.status) {
                scope.showPostalCodeInput = true;
                scope.hideLoading();
                
              } else {
                // scope.showPostalCodeInput = true;
                scope.disableCreateText = true;
                scope.hideLoading();
              }
            })
        }

        scope.removeDisable = function ( type,data ) {
          // console.log(data);
          
          if ( type == 'new_password' ) {
            scope.new_password = data;
            console.log(scope.new_password);
            console.log(scope.confirm_new_password);
          }
          if ( type == 'confirm_new_password' ) {
            scope.confirm_new_password = data;
            console.log(scope.new_password);
            console.log(scope.confirm_new_password);
          }
          
          if ( (scope.new_password != undefined && scope.confirm_new_password != undefined) && (scope.new_password != "" && scope.confirm_new_password != "") ) {
           
            // if ( scope.new_password == scope.confirm_new_password ) {
            //   // console.log('naa pa ang disale ug mugawas ang trigger');
            //   scope.disableCreate = false;
            //   scope.passwordNotMatch = false;
            // } else {
            //   // console.log('wala ang trigger tas wala ang disable sa button')
              
            //   scope.disableCreate = true;
            //   scope.passwordNotMatch = true;
            //   scope.disableCreateText = false;
            // }

            if ( scope.new_password != scope.confirm_new_password ) {
            // console.log('naa pa ang disale ug mugawas ang trigger');
              scope.disableCreate = true;
              scope.passwordNotMatch = true;
              scope.disableCreateText = false;
            } else if (scope.new_password == null && scope.confirm_new_password == null ) {
              scope.disableCreate = true;
              scope.passwordNotMatch = false;
              scope.disableCreateText = false;
            } else {
              // console.log('wala ang trigger tas wala ang disable sa button')
              scope.disableCreate = false;
              scope.passwordNotMatch = false;
            }
          }
        }
        scope.postal_code_value = "";

        scope.postalCode = function ( postal_code ) {
          if ( postal_code != undefined ) {
            scope.postal_code_value = postal_code;
            scope.disabledDone = false;
            console.log(scope.postal_code_value);
          } else {
            scope.disabledDone = true;
          }
        }

        scope.completeSignIn = async function ( type ) {
          
            if ( type == 'postal' ) {
              console.log(scope.postal_code_value);
              let data = {
                user_id: scope.checkMemberData.user_id,
                postal_code: scope.postal_code_value,
              }
              scope.showLoading();
              await $http.post(serverUrl.url + 'employee/add_postal_code_member', data)
              .then(async function(response) {
                console.log(response);
                if (response.data.status) {

                  scope.hideLoading();
                  await scope.signIn();
                }
            })
          }
        }

        scope.signIn = function () {
          let data = {
            email: scope.mobile_number,
            password: scope.new_password,
          }
          $http.post(serverUrl.url + 'app/e_claim/login', data)
	          .then(function(response) {
              console.log(response);
              if ( response.data.status ) {
                
                console.log('trueeeeeeee');
                window.location.href = window.location.origin + '/member-portal#/home';
                window.localStorage.setItem('token_member', response.data.token);
              } else {
                swal('Ooops!', response.data.message, 'error');
              }
            })
        }

        scope.checkPassword = function ( new_password ) {
          scope.disabledSignIn = false;
          scope.new_password = new_password;
          console.log(scope.new_password);
        }
        scope.passwordSignInNotMatch = false;
        scope.signInPassword = function () {
          // scope.showPostalCodeInput = true;
          let data = {
            user_id: scope.checkMemberData.user_id,
            password: scope.new_password,
          }
          console.log(data);
          scope.showLoading();
          $http.post(serverUrl.url + 'employee/check_member_password', data)
	          .then(function(response) {
              console.log(response);
              scope.checkMemberPassword = response.data;
              if ( response.data.status ) {
                scope.hideLoading();
                if(scope.checkMemberData.postal_code == 1){
                  scope.signIn();
                }else{
                  scope.showPostalCodeInput = true;
                }
              } else {
                scope.hideLoading();
                scope.passwordSignInNotMatch = true;
              }
            })
        }

        scope.showLoading = function(){
          $(".circle-loader").fadeIn();
          loading_trap = true;
        };

        scope.hideLoading = function(){
          setTimeout(function() {
            $(".circle-loader").fadeOut();
            loading_trap = false;
          }, 800);
        };

        scope.hideIntroLoader = function(){
          setTimeout(function() {
            $(".main-loader").fadeOut();
            introLoader_trap = false;
          }, 1000);
        };

        scope.onLoad = function(){
          scope.hideIntroLoader();
          scope.hideLoading();
          // localStorage.setItem('isFromWeb', false);
          // console.log( window.location );
          // scope.getOtpStatus();
        };

        scope.onLoad();
			}
		}
	}
]);

// login.directive('forgotSection', [
// 	"$http",
// 	"serverUrl",
// 	function directive($http, serverUrl) {
// 		return {
// 			restrict: "A",
// 			scope: true,
// 			link: function link(scope, element, attributeSet) {
// 				console.log('running forgotSection');
// 				scope.forgot_password_data = {};
// 				scope.login_details = {};
// 				scope.ng_fail = false;
// 				scope.new_password_error = false;
// 				scope.password_success = false;

// 				scope.loginHr = function( ) {
// 					console.log(scope.login_details);
// 					$('#login-btn').attr('disabled', true);
// 					$('#login-btn').text('Submitting...');
// 					$http.post(serverUrl.url + 'hr/forgot/company-benefits-dashboard', scope.login_details)
// 					.success(function(response){
// 						// console.log(response);
// 						$('#login-btn').attr('disabled', false);
// 						$('#login-btn').text('Log in');
// 						$('#form-forgot').slideUp();
// 						$('#success-message').fadeIn();
// 					});
// 				};

// 				scope.changePassword = function( data ){
// 					if( data.new_password == data.new_password2 ){
// 						scope.new_password_error = false;
// 						var hr_id = $('#hr-id').val();
// 						var data = {
// 							new_password : data.new_password,
// 							hr_id		 : hr_id
// 						}

// 						console.log(data);

// 						$http.post(serverUrl.url + 'hr/reset-password-data', data)
// 						.success(function(response){
// 							console.log(response);
// 							scope.password_success = true;
// 						});

// 					}else{
// 						scope.new_password_error = true;
// 					}
// 				}
// 			}
// 		}
// 	}
// ]);