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
          if ( code == 65 ) {
            console.log('singapore');
            scope.country_active = true;
          } else {
            scope.country_active = false;
          }
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