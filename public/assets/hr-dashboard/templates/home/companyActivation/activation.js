var app = angular.module('activation', ['ui.router','activationService']);

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
    })
    .state('benefits-dashboard', {
      url: '/benefits-dashboard',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/benefits-home-dashboard.html'
        },
        'modal': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/faqs.html'
        },
        'modal_2': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/faqs-new-emp.html'
        },
        'modal_3': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/faqs-remove-emp.html'
        },
        'modal_4': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/faqs-replace-emp.html'
        },
        'modal_5': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/faqs-cancel-plan.html'
        },
        'modal_6': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/faqs-data-privacy.html'
        },
      },
    });

    $urlRouterProvider.otherwise('/activation-link');
  
});