app.directive('enterpriseSummaryDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("enterpriseSummaryDirective Runnning !");

        scope.onLoad = function( ){
        		        
        }

        scope.onLoad();
			}
		}
	}
]);
