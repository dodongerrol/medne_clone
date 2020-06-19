var login = angular.module('hr', []);

login.run(function($http) {
  $http.defaults.headers.common.Authorization = window.localStorage.getItem('token');
});

login.factory('serverUrl',[
    function factory(){
      return {
        url: window.location.origin,
        // url: 'http://ec2-13-251-63-109.ap-southeast-1.compute.amazonaws.com',
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
				scope.ng_fail = false;

				scope.checkUserLogin = function( ) {
					var token = window.localStorage.getItem('token');

					if(token) {
						$http.get(serverUrl.url + "/get-hr-session")
						.then(function(response){
							console.log(response);
							if(response) {
								window.location.href = window.location.origin + '/company-benefits-dashboard/';
							}
						});
					}
				};

				scope.loginHr = function( ) {
					console.log(scope.login_details);
					$('#login-btn').attr('disabled', true);
					$('#login-btn').text('Logging in...');
					$http.post(serverUrl.url + '/company-benefits-dashboard-login', scope.login_details)
					.success(function(response){
						// console.log(response);
						$('#login-btn').attr('disabled', false);
						$('#login-btn').text('Log in');
						if(response.status == true){
							window.localStorage.setItem('token', response.token)
			              window.location.href = window.location.origin + "/company-benefits-dashboard/";
			              scope.ng_fail = false;
			            }else{
			              scope.ng_fail = true;
			            }
					});
				};

				scope.checkUserLogin( );
			}
		}
	}
]);

login.directive('forgotSection', [
	"$http",
	"serverUrl",
	function directive($http, serverUrl) {
		return {
			restrict: "A",
			scope: true,
			link: function link(scope, element, attributeSet) {
				console.log('running forgotSection');
				scope.forgot_password_data = {};
				scope.login_details = {};
				scope.ng_fail = false;
				scope.new_password_error = false;
				scope.password_success = false;

				scope.loginHr = function( ) {
					console.log(scope.login_details);
					$('#login-btn').attr('disabled', true);
					$('#login-btn').text('Submitting...');
					$http.post(serverUrl.url + '/hr/forgot/company-benefits-dashboard', scope.login_details)
					.success(function(response){
						// console.log(response);
						$('#login-btn').attr('disabled', false);
						$('#login-btn').text('Log in');
						$('#form-forgot').slideUp();
						$('#success-message').fadeIn();
					});
				};

				scope.changePassword = function( data ){
					if( data.new_password == data.new_password2 ){
						scope.new_password_error = false;
						var hr_id = $('#hr-id').val();
						var data = {
							new_password : data.new_password,
							hr_id		 : hr_id
						}

						console.log(data);

						$http.post(serverUrl.url + '/hr/reset-password-data', data)
						.success(function(response){
							console.log(response);
							scope.password_success = true;
						});

					}else{
						scope.new_password_error = true;
					}
				}
			}
		}
	}
]);