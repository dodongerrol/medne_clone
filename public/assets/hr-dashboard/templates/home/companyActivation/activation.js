var app = angular.module('activation', ['ui.router']);

app.run(function($http) {
  $http.defaults.headers.common.Authorization = window.localStorage.getItem('token');
});

app.factory('serverUrl',[
    function factory(){
      return {
        url: window.location.origin + '/',
      }
    }
]);

app.config(function($stateProvider, $urlRouterProvider){

  $stateProvider
    .state('expired-link', {
      url: '/activation-link',
      views: {
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyActivation/expired-link/expired-link.html'
        }
      }
    })
    .state('T&C', {
      url: '/T&C',
      views: {
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyActivation/t&c/t&c.html'
        }
      }
    })
    .state('company-create-password', {
      url: '/company-create-password',
      views: {
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyActivation/createPassword/index.html'
        }
      }
    });

    $urlRouterProvider.otherwise('/activation-link');
  
});