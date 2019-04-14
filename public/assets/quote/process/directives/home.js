app.directive('homeDirective', [
	"$http",
	function directive( $http) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				// console.log("home Directive Runnning !");

				scope.firstValidation = function(input){
					console.log(input);

					if( input.length == 5 ){
						scope.showTwo = true;
						scope.blurOne = true;
					}else{
						scope.showTwo = false;
					}

				}
			}
		}
	}
]);
