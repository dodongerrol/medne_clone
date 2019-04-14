var favouriteService = angular.module('favouriteService', [])

favouriteService.factory('favouritesModule', function( serverUrl, $http ){
	var favouriteFactory = {};

	favouriteFactory.favouriteList = function( ) {
		return $http.get(serverUrl.url + 'clinic/get_favourite_clinics');
	};

	favouriteFactory.removeFavourite = function( id, status ) {
		return $http.post(serverUrl.url + 'clinic/favourite', { clinicid: id, status: status });
	};

	favouriteFactory.searchClinic = function(name) {
		return $http.get(serverUrl.url + 'clinic/main_search?search=' + name);
	};
	
	return favouriteFactory;
});