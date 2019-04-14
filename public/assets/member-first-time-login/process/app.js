var app = angular.module('app', ['ui.router', 'authService', 'memberService', 'ngCacheBuster']);

app.run(['$rootScope', '$state', '$stateParams', '$templateCache',
  function ($rootScope, $state, $stateParams, $templateCache, $window) {
    $rootScope.$state = $state;
    $rootScope.$stateParams = $stateParams;
    $rootScope.$on('$viewContentLoaded', function () {
      $templateCache.removeAll();
    });
    $rootScope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) {
      window.ga('create', 'UA-78188906-2', 'auto');
      window.ga('set', 'page', toState.url);
      window.ga('send', 'pageview');
    });
  }
]);

app.factory('serverUrl', [
  function factory() {
    return {
      url: window.location.origin,
    }
  }
]);

// plugins
// creating the passwordCount filter
app.filter('passwordCount', [function () {
	return function (value, peak) {
		var value = angular.isString(value) ? value : '',
			peak = isFinite(peak) ? peak : 8;

		return value && (value.length > peak ? peak + '+' : value.length);
	};
}]);

// creating a service to provide zxcvbn() functionality
app.factory('zxcvbn', [function () {
	return {
		score: function () {
			var compute = zxcvbn.apply(null, arguments);
			return compute && compute.score;
		}
	};
}]);

// creating the okPassword directive with zxcvbn as dependency
app.directive('okPassword', ['zxcvbn', function (zxcvbn) {
	return {
		// restrict to only attribute and class
		restrict: 'AC',

		// use the NgModelController
		require: 'ngModel',

		// add the NgModelController as a dependency to your link function
		link: function ($scope, $element, $attrs, ngModelCtrl) {
			$element.on('blur change keydown', function (evt) {
				$scope.$evalAsync(function ($scope) {
					// update the $scope.password with the element's value
					var pwd = $scope.password = $element.val();

					// resolve password strength score using zxcvbn service
					$scope.passwordStrength = pwd ? (pwd.length > 7 && zxcvbn.score(pwd) || 0) :
						null;

					// define the validity criterion for okPassword constraint
					ngModelCtrl.$setValidity('okPassword', $scope.passwordStrength >= 2);
				});
			});
		}
	};
}]);
// end plugins

app.config(function ($stateProvider, $urlRouterProvider, $locationProvider, $httpProvider, $compileProvider) {
  // $httpProvider.interceptors.push('AuthInterceptor');
  $stateProvider
    .state('/', {
      url: '/',
      views: {
        'main': {
          templateUrl: window.location.origin + '/assets/member-first-time-login/templates/first-time-login.html'
        },
      },
    });

  $urlRouterProvider.otherwise('/');
  // $locationProvider.html5Mode(true);
});