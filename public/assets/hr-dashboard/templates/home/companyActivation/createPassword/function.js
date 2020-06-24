app.directive('createCompanyPasswordDirective', [
	'$state',
	function directive($state) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("createCompanyPasswordDirective Runnning !");

				scope.inputType = 'password';

				var params = new URLSearchParams(window.location);
				console.log(params.get('token'));
				
				console.log(params);


				console.log(window.location.search);


        scope.togglePassword = function(){
          scope.inputType = scope.inputType == 'password' ? 'text' : 'password';
        }
				
			}
		}
	}
]);
