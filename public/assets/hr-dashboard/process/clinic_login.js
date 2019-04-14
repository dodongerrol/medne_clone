var login = angular.module('login', []);

login.factory('serverUrl',[
    function factory(){
      return {
        url: window.location.origin + '/app/',
      }
    }
]);

login.directive('loginSection', [
	"$http",
	"serverUrl",
	function directive($http, serverUrl) {
		return {
			restrict: "A",
			scope: true,
			link: function link(scope, element, attributeSet) {
				console.log('running loginSection');
				scope.login_details = {};
				scope.callback = {};
				scope.ng_fail = false;

				scope.loginClinic = function( ) {
					$('#login-btn').attr('disabled', true);
					$('#login-btn').text('Logging in...');
					$http.post(serverUrl.url + 'auth/loginnow', scope.login_details)
					.success(function(response){
						$('#login-btn').attr('disabled', false);
						$('#login-btn').text('Log in');
						console.log(response);
						if(response == 2){
              window.location = base_url+"doctor/home";
              // window.location = serverUrl.url + "login";
            }else if(response == 3){
              window.location.href = serverUrl.url + "setting/claim-report";
              // window.location = serverUrl.url + "login";
            }else if(response == 4){
               window.location.href = serverUrl.url + "clinic/appointment-home-view";
               // window.location = serverUrl.url + "login";
            } else {
            	scope.ng_fail = true;
            }
					});
				};

				scope.showForgotPassword = function( ) {
					$('#login-container').hide();
					$('#forgot-password').show();
				};

				scope.showLogin = function( ) {
					$('#forgot-password').hide();
					$('#login-container').show();
				};

				scope.resetPassword = function( ) {
					scope.callback.text = "";
					$('#reset-password').attr('disabled', true);
					$('#reset-password').text('Resetting...');

					$http.post(serverUrl.url + 'auth/forgot-password', { email: scope.login_details.email })
					.then(function(response){
						if(response.data == "0" || response.data == 0) {
							scope.callback.text = "Sorry ! We could not find any information";
						} else if(response.data == "1" || response.data == 1){
							scope.callback.text = "Check your mail, we have sent instructions to reset your password";
						}
						$('#reset-password').attr('disabled', false);
						$('#reset-password').text('Reset Password');
					});
				};
			}
		}
	}
]);