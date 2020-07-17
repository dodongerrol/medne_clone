app.directive('expiredLinkDirective', [
	'$state',
	'$location',
	'activationSettings',
	'activationFactory',
	function directive($state,$location,activationSettings,activationFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("expired link directive Runnning !");
				console.log($location);


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

				scope.validateToken();
				
			}
		}
	}
]);
