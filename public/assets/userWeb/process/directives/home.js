app.directive('homeDirective', [
	"$http",
	"serverUrl",
	function directive( $http, serverUrl ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				// console.log("home Directive Runnning !");

				scope.userInfo = function userInfo( ) {
					$http.get(serverUrl.url + 'auth/userprofile')
					.success(function(response){
						scope.user = response;
					})
					.error(function(err){
						
					});
				};

				scope.userInfo( );
			}
		}
	}
]);
