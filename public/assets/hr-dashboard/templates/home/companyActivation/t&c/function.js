app.directive('t-and-c-directive', [
	'$state',
	function directive($state) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("t-and-c-directive Runnning !");

        scope.inputType = 'password';

        scope.togglePassword = function(){
          scope.inputType = scope.inputType == 'password' ? 'text' : 'password';
        }
				
			}
		}
	}
]);
