app.directive('stepsDirective', [
	'$state',
	'$rootScope',
	'carePlanFactory',
	function directive($state,$rootScope,carePlanFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("steps Directive Runnning !");

				scope.backButton = function(){
					var route = carePlanFactory.getLastRoute();
					$state.go( route );
				}

				scope.onLoad = function(){
					// console.log($state);

					scope.userDetails_data = carePlanFactory.getCarePlan();

					if( scope.userDetails_data.cover_type == 'individual' ){
						$('.steps-nav a#comp-step').text('Personal Details');
						$('.steps-nav a#emp-step').hide();
					}
				}
				
				scope.onLoad();
			}
		}
	}
]);
