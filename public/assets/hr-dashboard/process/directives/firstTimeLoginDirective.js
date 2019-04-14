app.directive('firstTimeLoginDirective', [
	'$http',
	'serverUrl',
	'hrSettings',
	function directive($http, serverUrl, hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("firstTimeLoginDirective Runnning !");




        scope.onLoad = function( ){

        }

    		scope.onLoad();
			}
		}
	}
]);
