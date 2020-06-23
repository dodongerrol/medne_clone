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
				scope.login_details = {
					status : false, // activated , not activated, false
				};
				scope.ng_fail = false;
				scope.showPassword = false;

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
						  // window.location.href = serverUrl.url + "company-benefits-dashboard/";
			              window.location.href = window.location.origin + "/company-benefits-dashboard/";
			              scope.ng_fail = false;
			            }else{
			              scope.ng_fail = true;
			            }
					});
				};

				scope.showPasswordToggle = function () {
					scope.showPassword = !scope.showPassword;
					console.log(scope.showPassword);
				}

				scope.enableContinue = function (email) {

					// let emailFromDb = 'example@email.com';
					let account_status;

					$http.post(serverUrl.url + `/employee/check_email_validation?email=${email}`)
					.success(function(response) {
						console.log(response);
						account_status = response.status;
						console.log(account_status);
						if( account_status == 1) {
							// check if email exist in db.
							scope.login_details.status = 'activated';
						} else if ((account_status == 0)) {
							scope.login_details.status = 'not activated';
						}	else if (account_status == 2) {
							scope.login_details.status = 'not-exist';
						} else {
							scope.login_details.status = false;
						}
	
						console.log(scope.login_details.status);
					});
				}

				scope.checkUserLogin();
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