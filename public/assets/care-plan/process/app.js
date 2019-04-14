var app = angular.module('app', ['ui.materialize', 'ngCacheBuster', 'ui.router','LocalStorageModule','CarePlanService']);

app.run([ '$rootScope', '$state', '$stateParams', '$templateCache', 
function ($rootScope, $state, $stateParams, $templateCache) {
  
  $rootScope.$state = $state;
  $rootScope.$stateParams = $stateParams;

  $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });

  $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams) {
    // console.log(toState);
    // console.log(toState.name);
    
  });

}]);

app.factory('serverUrl',[
    function factory(){
      return {
        url: window.location.origin,
        external_url: 'https://dev.geckorest.com/mednefits/',
        mednefits_url: 'http://app.mednefits.com/api/'
      }
    }
]);

app.config(function($stateProvider, $urlRouterProvider, $locationProvider,  $httpProvider, $compileProvider){
  // $httpProvider.interceptors.push('AuthInterceptor');
  // $compileProvider.debugInfoEnabled(false);
  // $httpProvider.useApplyAsync(true);
  // cfpLoadingBarProvider.parentSelector = '#loading-selected';
  // cfpLoadingBarProvider.spinnerTemplate = '<div id="loading-bar-spinner"><div class="spinner-icon"></div></div>';

	$stateProvider
    .state('introduction', {
      url: '/introduction',
      views: {
        'header': {
          // templateUrl: window.location.origin + '/assets/care-plan/templates/menus/header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/care-plan/templates/introduction.html'
        }
      },
    })
    .state('steps', {
      url: '/steps',
      views: {
        'header': {
          // templateUrl: window.location.origin + '/assets/care-plan/templates/menus/header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/care-plan/templates/steps.html'
        }
      },
    })
    .state('steps.plan', {
      url: '/plan',
      views: {
        'header': {
          // templateUrl: window.location.origin + '/assets/care-plan/templates/menus/header.html'
        },
        'step@steps': {
          templateUrl: window.location.origin + '/assets/care-plan/templates/plan.html'
        }
      },
    })
    .state('steps.company-details', {
      url: '/company-details',
      views: {
        'header': {
          // templateUrl: window.location.origin + '/assets/care-plan/templates/menus/header.html'
        },
        'step@steps': {
          templateUrl: window.location.origin + '/assets/care-plan/templates/company-details.html'
        }
      },
    })
    .state('steps.payment', {
      url: '/payment',
      views: {
        'header': {
          // templateUrl: window.location.origin + '/assets/care-plan/templates/menus/header.html'
        },
        'step@steps': {
          templateUrl: window.location.origin + '/assets/care-plan/templates/payment.html'
        }
      },
    })

    .state('steps.payment_success', {
      url: '/payment_success',
      views: {
        'header': {
          // templateUrl: window.location.origin + '/assets/care-plan/templates/menus/header.html'
        },
        'step@steps': {
          templateUrl: window.location.origin + '/assets/care-plan/templates/payment_success.html'
        }
      },
    })

    .state('steps.payment_success2', {
      url: '/payment_success2',
      views: {
        'header': {
          // templateUrl: window.location.origin + '/assets/care-plan/templates/menus/header.html'
        },
        'step@steps': {
          templateUrl: window.location.origin + '/assets/care-plan/templates/payment_success2.html'
        }
      },
    })

    .state('steps.payment_failed', {
      url: '/payment_failed',
      views: {
        'header': {
          // templateUrl: window.location.origin + '/assets/care-plan/templates/menus/header.html'
        },
        'step@steps': {
          templateUrl: window.location.origin + '/assets/care-plan/templates/payment_failed.blade.php'
        }
      },
    })
    .state('steps.employee-details', {
      url: '/employee-details',
      views: {
        'header': {
          // templateUrl: window.location.origin + '/assets/care-plan/templates/menus/header.html'
        },
        'step@steps': {
          templateUrl: window.location.origin + '/assets/care-plan/templates/employee-details.html'
        }
      },
    });

    $urlRouterProvider.otherwise('/introduction');
});