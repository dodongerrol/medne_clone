var service = angular.module('authService', ['ui.router'])

service.factory('Auth', function( $http, $q, AuthToken, serverUrl, $window ){
	var authFactory = {};
	authFactory.login = function(data) {
		data.grant_type = "password";
		data.client_secret = "b6589fc6ab0dc82cf12099d1c2d40ab994e8410c";
		data.client_id = "cfcd208495d565ef66e7dff9f98764da";
		return $http({
		  method  : 'POST',
		  url     : serverUrl.url + 'auth/login',
		  data    : $.param(data),
		  headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
		 })
		.success(function(data) {
			AuthToken.setToken(data.data.access_token);
	    	return data;
	  	});
	}
	authFactory.resetPassword = function(email) {
		return $http.post(serverUrl.url + 'auth/forgotpassword', { email: email });
	}
	authFactory.resetDetails = function(token) {
		return $http.post(serverUrl.url + 'auth/reset-details', { resetcode: token });
	}
	authFactory.resetProcess = function(user_id, oldpass, newpass) {
		return $http.post(serverUrl.url + 'auth/reset-process', { userid: user_id, oldpass: oldpass, newpass: newpass });
	}
	authFactory.logout = function( ) {
		$window.localStorage.removeItem('token');
	}

	authFactory.isLoggedIn = function( ) {
		if(AuthToken.getToken()) {
			return true;
		} else {
			return false;
		}
	}
	authFactory.signUp = function( userData ) {
		return $http.post(serverUrl.url + 'auth/signup', { full_name: userData.full_name, email: userData.email, password: userData.password, phone: userData.phone, latitude: 0, longitude: 0 });
	}
	return authFactory;
});

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


service.factory('AuthInterceptor', function($q, $window, AuthToken, $injector, $rootScope){
	var interceptorFactory = {};
	interceptorFactory.request = function( config ) {
		var token = AuthToken.getToken( );

		if(token) {
			config.headers['Authorization'] = token;
		}

		return config;
	};
	interceptorFactory.response = function( response ) {
		console.log($injector.get('$state'));
		if(response.data.login_status == false) {
			if($injector.get('$state').$current.self.name == 'password-reset') {
				// $injector.get('$state').transitionTo('password-reset');
				window.location.href = window.location.origin + '/user#password-reset/'+$injector.get('$state').params.token;
			} else {
				$injector.get('$state').transitionTo('login');
			}
		} else {
			// $injector.get('$state').transitionTo('login');
		}
		return response;
	};
	interceptorFactory.requestError = function( response ) {
		console.log(response);
		if(response.status == false) {
			$injector.get('$state').transitionTo('login');
			// window.location.href = window.location.origin + '/app/user_web';
		}

		return $q.reject(response);
	};
	return interceptorFactory;
});