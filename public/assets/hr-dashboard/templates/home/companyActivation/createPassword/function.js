app.directive('createCompanyPasswordDirective', [
	'$state',
	'activationSettings',
	'activationFactory',
	'serverUrl',
	function directive($state, activationSettings, activationFactory, serverUrl) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("createCompanyPasswordDirective Runnning !");
				// scope.activationDetails = activationFactory.getActivationDetails();
				console.log(scope.activationDetails);
				let token = localStorage.getItem('activation_token');

				scope.inputType = 'password';
				scope.formData = {};

				scope.togglePassword = function(){
					scope.inputType = scope.inputType == 'password' ? 'text' : 'password';
				}
				
				scope.validateToken = function () {
					
					activationSettings.validateToken( token )
					.then(function(response){
						console.log('response', response);
						scope.activationDetails = response.data.data;
					});
				}

				scope.createPassword	=	function(formData){
					if(formData.password != formData.confirm_password){
						swal('Error!', 'Passwords do not match.', 'error');
						return false;
					}
					console.log('ASDFASDFA');
					scope.showLoading();
					var data	=	{
						hr_dashboard_id: scope.activationDetails.hr_dashboard_id,
						new_password: formData.password,
						token: token,
					}
					activationSettings.createActivationPassword(data)
						.then(function(response){
							console.log(response);
							if(response.data.status){
								window.localStorage.setItem('token', response.data.token);
								localStorage.removeItem('activation_token');
								window.location.href = window.location.origin + "/company-benefits-dashboard/";
							}else{
								swal('Error!', response.data.message, 'error');
							}
							scope.hideLoading();
						});

				}

				scope.showLoading = function () {
					$(".circle-loader").fadeIn();
					loading_trap = true;
				}

				scope.hideLoading = function( ){
					setTimeout(function() {
						$(".circle-loader").hide();
						loading_trap = false;
					},10)
				}
				
				scope.validateToken( );
			}
		}
	}
]);
