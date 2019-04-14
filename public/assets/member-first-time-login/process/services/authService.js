var service = angular.module('authService', ['ui.router'])

service.factory('AuthInterceptor', function($q, $window, $injector, $rootScope){
	var interceptorFactory = {};
	interceptorFactory.request = function( config ) {
		// console.log(config);
		return config;
	};
	interceptorFactory.response = function( response ) {
		// console.log(response);
		return response;
	};
	interceptorFactory.requestError = function( response ) {
		// console.log(response);
		return $q.reject(response);
	};
	interceptorFactory.responseError = function( response ) {
		console.log(response);
		if(response.status == 403) {
			window.location.href = window.location.origin + '/member-portal-login';
		}
		return $q.reject(response);
	};
	return interceptorFactory;
});