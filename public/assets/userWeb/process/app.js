var app = angular.module('app', ['ui.router', 'ngAnimate', 'unsavedChanges', 'angular-loading-bar', 'walletService', 'authService', 'mainCtrl','ngImageAppear', 'benefitService', 'clinicService', 'favouriteService', 'appointmentService', 'profileService', 'ngFileUpload']);

app.run([ '$rootScope', '$state', '$stateParams', '$templateCache',
function ($rootScope, $state, $stateParams, $templateCache) {
  $rootScope.$state = $state;
  $rootScope.$stateParams = $stateParams;
  $rootScope.$on('$routeChangeStart', function(event, next, current) {
    if (typeof(current) !== 'undefined'){
        $templateCache.remove(current.templateUrl);
    }
  });
}]);

app.factory('serverUrl',[
    function factory(){
      return {
        url: window.location.origin + "/v1/",
      }
    }
]);

app.config(function($stateProvider, $urlRouterProvider, $locationProvider,  $httpProvider, cfpLoadingBarProvider, $compileProvider){
  $httpProvider.interceptors.push('AuthInterceptor');
  // $compileProvider.debugInfoEnabled(false);
  $httpProvider.useApplyAsync(true);
  cfpLoadingBarProvider.parentSelector = '#loading-selected';
  cfpLoadingBarProvider.spinnerTemplate = '<div id="loading-bar-spinner"><div class="spinner-icon"></div></div>';

	$stateProvider
    .state('home', {
      url: '/home',
      data : { pageTitle: 'Home' },
      views: {
        'header': {
          templateUrl: '../assets/userWeb/templates/menus/header.html'
        },
        'main': {
          templateUrl: '../assets/userWeb/templates/benefits.html'
        },
        'side-menu': {
          templateUrl: '../assets/userWeb/templates/side-menu.html'
        }
      },
    })
    .state('benefit-dashboard', {
      url: '/benefit-dashboard',
      data : { pageTitle: 'Benefits Dashboard' },
      views: {
        'header': {
          templateUrl: '../assets/userWeb/templates/menus/header.html'
        },
        'main': {
          templateUrl: '../assets/userWeb/templates/benefit-dashboard.html'
        },
        'side-menu': {
          templateUrl: '../assets/userWeb/templates/side-menu.html'
        }
      },
    })
    .state('favourites', {
      url: '/favourites',
      data : { pageTitle: 'Favourites' },
      views: {
        'header': {
          templateUrl: '../assets/userWeb/templates/menus/header.html'
        },
        'main': {
          templateUrl: '../assets/userWeb/templates/favourites.html'
        },
        'side-menu': {
          templateUrl: '../assets/userWeb/templates/side-menu.html'
        }
      },
    })
    .state('calendar', {
      url: '/calendar',
      data : { pageTitle: 'Calendar' },
      views: {
        'header': {
          templateUrl: '../assets/userWeb/templates/menus/header.html'
        },
        'main': {
          templateUrl: '../assets/userWeb/templates/calendar.html'
        },
        'side-menu': {
          templateUrl: '../assets/userWeb/templates/side-menu.html'
        }
      },
    })
    .state('appointments', {
      url: '/appointments',
      data : { pageTitle: 'Appointments' },
      views: {
        'header': {
          templateUrl: '../assets/userWeb/templates/menus/header.html'
        },
        'main': {
          templateUrl: '../assets/userWeb/templates/appointments.html'
        },
        'side-menu': {
          templateUrl: '../assets/userWeb/templates/side-menu.html'
        }
      },
    })
    .state('profile', {
      url: '/profile',
      data : { pageTitle: 'Profile' },
      views: {
        'header': {
          templateUrl: '../assets/userWeb/templates/menus/header.html'
        },
        'main': {
          templateUrl: '../assets/userWeb/templates/profile.html'
        },
        'side-menu': {
          templateUrl: '../assets/userWeb/templates/side-menu.html'
        }
      }
    })
    .state('wallet', {
      url: '/wallet',
      data : { pageTitle: 'Wallet' },
      views: {
        'header': {
          templateUrl: '../assets/userWeb/templates/menus/header.html'
        },
        'main': {
          templateUrl: '../assets/userWeb/templates/wallet.html'
        },
        'side-menu': {
          templateUrl: '../assets/userWeb/templates/side-menu.html'
        }
      }
    })
    .state('ecommerce', {
      url: '/ecommerce',
      data : { pageTitle: 'Ecommerce' },
      views: {
        'header': {
          templateUrl: '../assets/userWeb/templates/menus/header.html'
        },
        'main': {
          templateUrl: '../assets/userWeb/templates/ecommerce.html'
        },
        'side-menu': {
          templateUrl: '../assets/userWeb/templates/side-menu.html'
        }
      }
    })
    .state('appointment-create', {
      url: '/appointment-create/:id/:state',
      data : { pageTitle: 'Create Appointment' },
      views: {
        'header': {
          templateUrl: '../assets/userWeb/templates/menus/header.html'
        },
        'main': {
          templateUrl: '../assets/userWeb/templates/appointment-create.html'
        },
        'side-menu': {
          templateUrl: '../assets/userWeb/templates/side-menu.html'
        }
      }
    })
    .state('clinic-map', {
      url: '/clinic-map/:type',
      data : { pageTitle: 'Clinic Location' },
      views: {
        'header': {
          templateUrl: '../assets/userWeb/templates/menus/header.html'
        },
        'main': {
          templateUrl: '../assets/userWeb/templates/clinic-map.html'
        },
        'side-menu': {
          templateUrl: '../assets/userWeb/templates/side-menu.html'
        }
      }
    })
    .state('login', {
      url: '/',
      data : { pageTitle: 'Login' },
      views: {
        'header': {
          templateUrl: ''
        },
        'main': {
          templateUrl: '../assets/userWeb/templates/auth/login.html'
        },
        'side-menu': {
          templateUrl: ''
        }
      }
    })
    .state('signup', {
      url: '/',
      data : { pageTitle: 'Signup' },
      views: {
        'header': {
          templateUrl: ''
        },
        'main': {
          templateUrl: '../assets/userWeb/templates/auth/signup.html'
        },
        'side-menu': {
          templateUrl: ''
        }
      }
    })

    $urlRouterProvider.otherwise('/home');
});