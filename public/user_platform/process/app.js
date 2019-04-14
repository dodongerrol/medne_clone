var app = angular.module('app', ['ui.router', 'ngAnimate', 'unsavedChanges', 'angular-loading-bar', 'walletService', 'authService', 'mainCtrl','ngImageAppear', 'benefitService', 'clinicService', 'favouriteService', 'appointmentService', 'profileService', 'ngFileUpload']);

app.run([ '$rootScope', '$state', '$stateParams',
function ($rootScope, $state, $stateParams) {
  $rootScope.$state = $state;
  $rootScope.$stateParams = $stateParams;
}]);

app.factory('serverUrl',[
    function factory(){
      return {
        // url: "http://ec2-52-221-188-147.ap-southeast-1.compute.amazonaws.com/v1/",
        // url: "http://medicloud.dev/v1/",
        // url: "https://medicloud.sg/v1/",
        url: window.location.origin + '/v1/',
      }
    }
]);

app.config(function($stateProvider, $urlRouterProvider, $locationProvider,  $httpProvider, cfpLoadingBarProvider, $compileProvider){
  $httpProvider.interceptors.push('AuthInterceptor');
  // $compileProvider.debugInfoEnabled(false);
  // $httpProvider.useApplyAsync(true);
  cfpLoadingBarProvider.parentSelector = '#loading-selected';
  cfpLoadingBarProvider.spinnerTemplate = '<div id="loading-bar-spinner"><div class="spinner-icon"></div></div>';

	$stateProvider
    .state('home', {
      url: '/home',
      data : { pageTitle: 'Home' },
      views: {
        'header': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/benefits.html'
        },
        'side-menu': {
          templateUrl: window.location.origin + '/user_platform/templates/side-menu.html'
        },
        'fixed-menu-custom': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/fixed-menu.html'
        }
      },
    })
    .state('benefit-dashboard', {
      url: '/benefit-dashboard',
      data : { pageTitle: 'Benefits Dashboard' },
      views: {
        'header': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/benefit-dashboard.html'
        },
        'side-menu': {
          templateUrl: window.location.origin + '/user_platform/templates/side-menu.html'
        },
        'fixed-menu-custom': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/fixed-menu.html'
        }
      },
    })
    .state('favourites', {
      url: '/favourites',
      data : { pageTitle: 'Favourites' },
      views: {
        'header': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/favourites.html'
        },
        'side-menu': {
          templateUrl: window.location.origin + '/user_platform/templates/side-menu.html'
        },
        'fixed-menu-custom': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/fixed-menu.html'
        }
      },
    })
    .state('calendar', {
      url: '/calendar',
      data : { pageTitle: 'Calendar' },
      views: {
        'header': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/calendar.html'
        },
        'side-menu': {
          templateUrl: window.location.origin + '/user_platform/templates/side-menu.html'
        },
        'fixed-menu-custom': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/fixed-menu.html'
        }
      },
    })
    .state('appointments', {
      url: '/appointments',
      data : { pageTitle: 'Appointments' },
      views: {
        'header': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/appointments.html'
        },
        'side-menu': {
          templateUrl: window.location.origin + '/user_platform/templates/side-menu.html'
        },
        'fixed-menu-custom': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/fixed-menu.html'
        }
      },
    })
    .state('profile', {
      url: '/profile',
      data : { pageTitle: 'Profile' },
      views: {
        'header': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/profile.html'
        },
        'side-menu': {
          templateUrl: window.location.origin + '/user_platform/templates/side-menu.html'
        },
        'fixed-menu-custom': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/fixed-menu.html'
        }
      }
    })
    .state('wallet', {
      url: '/wallet',
      data : { pageTitle: 'Wallet' },
      views: {
        'header': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/wallet.html'
        },
        'side-menu': {
          templateUrl: window.location.origin + '/user_platform/templates/side-menu.html'
        },
        'fixed-menu-custom': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/fixed-menu.html'
        }
      }
    })
    .state('ecommerce', {
      url: '/ecommerce',
      data : { pageTitle: 'Ecommerce' },
      views: {
        'header': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/ecommerce.html'
        },
        'side-menu': {
          templateUrl: window.location.origin + '/user_platform/templates/side-menu.html'
        },
        'fixed-menu-custom': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/fixed-menu.html'
        }
      }
    })
    .state('appointment-create', {
      url: '/appointment-create/:id/:state',
      data : { pageTitle: 'Create Appointment' },
      views: {
        'header': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/appointment-create.html'
        },
        'side-menu': {
          templateUrl: window.location.origin + '/user_platform/templates/side-menu.html'
        },
        'fixed-menu-custom': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/fixed-menu.html'
        }
      }
    })
    .state('clinic-map', {
      url: '/clinic-map/:type',
      data : { pageTitle: 'Clinic Location' },
      views: {
        'header': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/clinic-map.html'
        },
        'side-menu': {
          templateUrl: window.location.origin + '/user_platform/templates/side-menu.html'
        },
        'fixed-menu-custom': {
          templateUrl: window.location.origin + '/user_platform/templates/menus/fixed-menu.html'
        }
      }
    })
    .state('login', {
      url: '/login',
      data : { pageTitle: 'Login' },
      views: {
        'header': {
          
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/auth/login.html'
        },
        'side-menu': {
         
        }
      }
    })
    .state('signup', {
      url: '/signup',
      data : { pageTitle: 'Signup' },
      views: {
        'header': {
         
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/auth/signup.html'
        },
        'side-menu': {
         
        }
      }
    })
    .state('forgot', {
      url: '/forgot',
      data : { pageTitle: 'Forgot Password' },
      views: {
        'header': {
         
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/auth/forgot.html'
        },
        'side-menu': {
         
        }
      }
    })
    .state('password-reset', {
      url: '/password-reset/:token',
      data : { pageTitle: 'Password Reset' },
      views: {
        'header': {
         
        },
        'main': {
          templateUrl: window.location.origin + '/user_platform/templates/auth/password-reset.html'
        },
        'side-menu': {
         
        }
      }
    });

    $urlRouterProvider.otherwise('/login');
});