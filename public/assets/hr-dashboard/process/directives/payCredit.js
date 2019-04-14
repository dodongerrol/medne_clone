app.directive('payCredit', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("payCredit Runnning !");

        scope.onLoad = function( ){
        		        
        }

        scope.onLoad();
			}
		}
	}
]);
