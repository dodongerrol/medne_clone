var service = angular.module('authService', ['ui.router'])

service.factory('AuthToken', function($window){
	var authTokenFactory = {};
	authTokenFactory.getToken = function( ) {
		return $window.localStorage.getItem('token');
	}
	authTokenFactory.setToken = function( token ) {
		console.log( token );
		if(token) {
			return $window.localStorage.setItem('token', token);
		} else {
			$window.localStorage.removeItem('token');
		}
	}
	return authTokenFactory;
});

service.factory('AuthInterceptor', function($q, $window, $injector, $rootScope, AuthToken){
	var interceptorFactory = {};
	interceptorFactory.request = function( config ) {
		var token = AuthToken.getToken( );

		if(token) {
			config.headers['Authorization'] = token;
		}
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