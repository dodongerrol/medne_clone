app.directive('walletDirective', [
	"$http",
	"serverUrl",
	"walletsModule",
	function directive( $http, serverUrl, walletsModule ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				// console.log("wallet Directive Runnning !");
				$( ".sidebar ul li" ).removeClass('active');
				$( ".sidebar ul li#wallet_li" ).addClass('active');
				scope.wallet_list = {};
				scope.wallet_code = {};

				scope.userInfo = function userInfo( ) {
					$http.get(serverUrl.url + 'auth/userprofile')
					.success(function(response){
						scope.user = response;
					})
					.error(function(err){
						
					});
				};

				scope.getWallet = function( ) {
					walletsModule.categoryList( )
					.success(function(response){
						// console.log(response);
						scope.wallet_list = response.data;
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.submitCode = function( ) {
					// console.log(scope.wallet_code);
					walletsModule.submitCode(scope.wallet_code)
					.success(function(response){
						// console.log(response);
						if(response.status == 1) {
							scope.getWallet( );
							$.alert({
							    title: '',
							    content: response.data.message,
							    columnClass: 'small'
							});
						} else {
							$.alert({
							    title: '',
							    content: response.data.message,
							    columnClass: 'small'
							});
						}
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.userInfo( );
				scope.getWallet( );
			}
		}
	}
]);
