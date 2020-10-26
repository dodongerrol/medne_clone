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
		return response;
	};
	interceptorFactory.requestError = function( response ) {
		return $q.reject(response);
	};
	interceptorFactory.responseError = function( response ) {
			console.log(response);
		if(response.status == 403) {
			// if(!response.config.headers.Authorization) {
			// 	window.location.href = window.location.origin + '/company-benefits-dashboard-login';
					// $('#global_modal').modal('show');
					// $('#global_message').text(response.data);
					// $('#login-status').show();
			// }
			// window.location.href = window.location.origin + '/business-portal-login';
			$('#global_modal').modal('show');
			$('#global_message').text(response.data);
			$('#login-status').show();
			$('.circle-loader').hide();
		} else if(response.status == 401) {
			// window.location.href = window.location.origin + '/company-benefits-dashboard-login';
			$('#global_modal').modal('show');
			if(response.data.type && response.data.type == "hr_not_activated"){
				$('#global_message').text(response.data.message);
				$('#login-status').hide();
			}else{
				$('#global_message').text(response.data);
				$('#login-status').show();
			}
			$('.circle-loader').hide();
		} else if(response.status == 500 || response.status == 408) {
			// window.location.href = window.location.origin + '/company-benefits-dashboard-login';
			$('#global_modal').modal('show');
			$('#global_message').text('Ooops! Something went wrong. Please check you internet connection or reload the page.');
			$('#login-status').hide();
			$('.circle-loader').hide();
		} else {
			$('#global_modal').modal('show');
			$('#global_message').text('Ooops! Something went wrong. Please check you internet connection or reload the page.');
			$('#login-status').hide();
			$('.circle-loader').hide();
		}
		
		return $q.reject(response);
	};
	return interceptorFactory;
});