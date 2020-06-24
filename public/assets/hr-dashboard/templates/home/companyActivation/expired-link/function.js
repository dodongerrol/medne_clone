app.directive('expiredLinkDirective', [
	'$state',
	'$location',
	'activationSettings',
	function directive($state,$location,activationSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("expired link directive Runnning !");
				console.log($location);


				scope.validateToken = function () {
					let token = $location.search().activation_token;
					// console.log(token);

					scope.showLoading();
					activationSettings.validateToken( token )
		      	.then(function(response){
		      		scope.getTokenData = token;
		      		scope.validateToken = response.data.data;
		      		console.log(scope.validateToken);
		      		console.log(scope.getTokenData);
							scope.hideLoading();

		      		if ( scope.validateToken.activated == false && scope.validateToken.expired_token == false && scope.validateToken.t_c == false || scope.validateToken.activated == true && scope.validateToken.t_c == false ) {
		      			scope.hideLoading();
		      			$state.go('T&C');
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
