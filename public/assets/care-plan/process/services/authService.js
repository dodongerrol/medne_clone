var service = angular.module('authService', ['ui.router'])

service.factory('Auth', function( $http, $q, AuthToken, serverUrl, calendarViewSettings, $injector, $window, $state ){
	var authFactory = {};
	authFactory.login = function(data) {
		return $http({
		  method  : 'POST',
		  url     : serverUrl.external_url + 'partners/signin',
		  data    : data,
		  headers : { 'Content-Type': 'application/json' }
		 })
		.success(function(data) {
			AuthToken.setToken(data.results.jwt, data.results.id);
	    	return true;
	  	});
	}
	authFactory.resetPassword = function(email) {
		return $http.post(serverUrl.url + 'auth/forgotpassword', { email: email });
	}
	authFactory.logout = function( ) {
		$window.localStorage.removeItem('token');
		$window.localStorage.removeItem('id');
	    $injector.get('$state').transitionTo('login');
	}

	authFactory.isLoggedIn = function( ) {

		var token = AuthToken.getToken();
		if(token) {
			calendarViewSettings.getCalendarSettings( )
		  	.success(function(response){
		  		$( "#page_loader" ).fadeOut('slow');
		  		$( "#settings_loader" ).fadeOut('slow');
		  		if(response.results.configure == false) {
		  			$injector.get('$state').transitionTo('update');
		  		}
		  	})
		  	.error(function(error){
	    		$window.localStorage.removeItem('token');
	    		$window.localStorage.removeItem('id');
	        	$state.go('login');
		  	});
		} else {
			$state.go('login');
		}

	}
	authFactory.signUp = function( data ) {
		data.business_type = 'clinic';
		return $http.post(serverUrl.external_url + 'partners/register', data);
	}
	return authFactory;
});

service.factory('AuthToken', function($window){
	var authTokenFactory = {};
	authTokenFactory.getToken = function( ) {
		return $window.localStorage.getItem('token');
	}
	authTokenFactory.setToken = function( token, id ) {
		console.log( token );
		if(token) {
			$window.localStorage.setItem('id', id);
			return $window.localStorage.setItem('token', token);
		} else {
			$window.localStorage.removeItem('token');
		}
	}
	return authTokenFactory;
});


service.factory('AuthInterceptor', function($q, $window, AuthToken, $injector){
	var interceptorFactory = {};
	interceptorFactory.request = function( config ) {
		var token = AuthToken.getToken( );
		if(token) {
			config.headers['Authorization'] = 'Bearer ' + token;
			config.headers['X-Secret'] = 'unsecure';
			config.headers['X-Key'] = 'mednefits ';
		}
		return config;
	};
	interceptorFactory.response = function( response ) {
		if(response.status == 403) {
			$injector.get('$state').transitionTo('login');
		}
		return response;
	};
	interceptorFactory.requestError = function( response ) {
		if(response.status == 403) {
			$injector.get('$state').transitionTo('login');
		}
		return $q.reject(response);
	};
	return interceptorFactory;
});