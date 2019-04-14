var clinicService = angular.module('clinicService', [])

clinicService.factory('clinicsModule', function( serverUrl, $http ){
	var clinicFactory = {};

	clinicFactory.searchClinicLocation = function(lat, lng, type) {
		return $http.get(serverUrl.url + 'clinic/nearby?lat=' + lat + '&lng=' + lng + '&type=' + type);
	}

	return clinicFactory;
});