app.directive('createCompanyPasswordDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("createCompanyPasswordDirective Runnning !");

        scope.inputType = 'password';

        scope.togglePassword = function(){
          scope.inputType = scope.inputType == 'password' ? 'text' : 'password';
        }
				
			}
		}
	}
]);
