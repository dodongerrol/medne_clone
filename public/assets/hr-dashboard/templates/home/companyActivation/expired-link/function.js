app.directive('expiredLinkDirective', [
	'$state',
	'$location',
	'activationSettings',
	'activationFactory',
	'$http',
	'serverUrl',
	function directive($state,$location,activationSettings,activationFactory,$http,serverUrl) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("expired link directive Runnning !");
				console.log($location);
				console.log($location.search());


				scope.validateToken = function () {
					let token = $location.search().activation_token;
					localStorage.setItem('activation_token', token);
					// console.log(token);

					scope.showLoading();
					activationSettings.validateToken( token )
		      	.then(function(response){
		      		scope.getTokenData = token;
		      		scope.validateToken = response.data.data;
		      		console.log(scope.validateToken);
		      		console.log(scope.getTokenData);
							scope.hideLoading();
							scope.validateToken.token = scope.getTokenData;
							activationFactory.setActivationDetails(scope.validateToken);
		      		if ( scope.validateToken.activated == false && scope.validateToken.expired_token == false && scope.validateToken.t_c == false || scope.validateToken.activated == true && scope.validateToken.t_c == false || scope.validateToken.activated == false && scope.validateToken.expired_token == false && scope.validateToken.t_c == true) {
		      			scope.hideLoading();
		      			$state.go('T&C');
		      		}
		      		if ( scope.validateToken.activated == false && scope.validateToken.expired_token == false && scope.validateToken.t_c == true ) {
		      			$state.go('company-create-password');
		      		}
		      	});
				}

				scope.validateTokenAdmin	=	function(){
					let token = $location.search().activation_token;
					localStorage.setItem('activation_token', token);
					// console.log(token);

					scope.showLoading();
					$http.get( serverUrl.url + 'hr/validate_external_admin_token?token=' + token )
		      	.then(function(response){
		      		scope.getTokenData = token;
		      		scope.validateToken = response.data.data;
		      		console.log(scope.validateToken);
		      		console.log(scope.getTokenData);
							scope.hideLoading();
							scope.validateToken.token = scope.getTokenData;
							scope.validateToken.isAdmin = true;
							activationFactory.setActivationDetails(scope.validateToken);
		      		if ( scope.validateToken.activated == false && scope.validateToken.expired_token == false) {
		      			$state.go('company-create-password');
		      		}
		      	});
				}

				scope.requestLink	=	function(){
					let token = $location.search().activation_token;
					let data =	{
						token : token,
					}
					scope.showLoading();
					activationSettings.requestNewLink(data)
						.then(function(response){
							scope.hideLoading();
							console.log(response);
							if(response.data.status){
								swal('Success!', response.data.message, 'success');
							}else{
								swal('Error!', response.data.message, 'error');
							}
						});
				}

				scope.goToLogin	=	function(){
					// console.log(scope.validateToken);
					// window.localStorage.setItem('token', scope.validateToken.token);
					window.location.href = '/business-portal-login';
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

				scope.onLoad	=	function(){
					var url_params = $location.search();
					if(url_params.user_type && url_params.user_type == 'external_admin'){
						scope.validateTokenAdmin();
					}else{
						scope.validateToken();
					}
				}
				
				scope.onLoad();
			}
		}
	}
]);
