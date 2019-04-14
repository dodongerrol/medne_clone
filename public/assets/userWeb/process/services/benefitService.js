var benefitService = angular.module('benefitService', [])

benefitService.factory('benefitsModule', function( serverUrl, $http ){
	var benefitFactory = {};

	benefitFactory.categoryList = function( ) {
		return $http.get(serverUrl.url + 'clinic/clinic_type');
	};

	return benefitFactory;
});