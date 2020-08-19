app.directive('employeeEnrollmentContainerDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("employeeEnrollmentContainerDirective Runnning !");

        scope.onLoad = function( ){
        		        
        }

        scope.onLoad();
			}
		}
	}
]);
