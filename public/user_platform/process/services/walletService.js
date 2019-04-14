var walletService = angular.module('walletService', [])

walletService.factory('walletsModule', function( serverUrl, $http ){
	var walletFactory = {};

	walletFactory.categoryList = function( ) {
		return $http.get(serverUrl.url + 'user/credits');
	};
	walletFactory.submitCode = function( code ) {
		return $http.post(serverUrl.url + 'user/match/promo', { code: code });
	};
	return walletFactory;
});