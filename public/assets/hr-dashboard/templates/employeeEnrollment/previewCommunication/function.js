app.directive('enrollmentCommunicationDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("enrollmentCommunicationDirective Runnning !");

				scope._toggleInfo_ = function() {
					console.log('gana');
					$('.employee-activation-email-tooltip').toggle();
				}

        scope.onLoad = function( ){
        		        
        }

        scope.onLoad();
			}
		}
	}
]);
