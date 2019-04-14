var app = angular.module('app', ['ui.router', 'ngCacheBuster', 'LocalStorageModule','eclaimService','ngFileUpload', 'authService', 'ngJsonExportExcel', 'checkCtrl','cp.ng.fix-image-orientation']);

app.run([ '$rootScope', '$state', '$stateParams', '$templateCache', 
function ($rootScope, $state, $stateParams, $templateCache, $window) {
  
  $rootScope.$state = $state;
  $rootScope.$stateParams = $stateParams;

  $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });

  $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
    window.ga('create', 'UA-78188906-2', 'auto');
    window.ga('set', 'page', toState.url);
    window.ga('send', 'pageview');
  });

}]);

app.factory('serverUrl',[
    function factory(){
      return {
        url: window.location.origin,
      }
    }
]);

app.filter('cmdate', [
    '$filter', function($filter) {
        return function(input, format) {
          if( input != undefined ){
            var t = input.split(/[- :]/);
              input = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));
              return $filter('date')(new Date(input), format);
          }
        };
    }
])

app.directive('validNumber', function() {
  return {
    require: '?ngModel',
    link: function(scope, element, attrs, ngModelCtrl) {
      if(!ngModelCtrl) {
        return; 
      }

      ngModelCtrl.$parsers.push(function(val) {
        if (angular.isUndefined(val)) {
            var val = '';
        }
        var clean = val.replace(/[^0-9\.]/g, '');
        var decimalCheck = clean.split('.');

        if(!angular.isUndefined(decimalCheck[1])) {
            decimalCheck[1] = decimalCheck[1].slice(0,2);
            clean =decimalCheck[0] + '.' + decimalCheck[1];
        }

        if (val !== clean) {
          ngModelCtrl.$setViewValue(clean);
          ngModelCtrl.$render();
        }
        return clean;
      });

      element.bind('keypress', function(event) {
        if(event.keyCode === 32) {
          event.preventDefault();
        }
      });
    }
  };
})

app.directive('validTime', function() {
  return {
    require: '?ngModel',
    link: function(scope, element, attrs, ngModelCtrl) {
      if(!ngModelCtrl) {
        return; 
      }

      ngModelCtrl.$parsers.push(function(val) {
        if (angular.isUndefined(val)) {
            var val = '';
        }

        var clean = val.replace(/[^0-9:]/g, '');
        var colonCheck = clean.split('');
        var colonSymbolCheck = clean.split(':');

        if(!angular.isUndefined(colonSymbolCheck[1])) {
            colonSymbolCheck[1] = colonSymbolCheck[1].slice(0,2);
            clean =colonSymbolCheck[0] + ':' + colonSymbolCheck[1];
        }

        if( colonSymbolCheck[0] > 12 ){
          if( !angular.isUndefined(colonSymbolCheck[1]) ){
            clean = 12 + ':' + colonSymbolCheck[1];
          }else{
            clean = 12 + ':' + '00';
          }
        }

        if( colonSymbolCheck[1] > 59 ){
          clean = colonSymbolCheck[0] + ':' + 59;
        }

        if( colonSymbolCheck[0] > 12 || colonSymbolCheck[1] > 59 ){
          $.toast({ 
            text : 'Time invalid. It should be 12 hour format.', 
            showHideTransition : 'slide',  
            bgColor : 'rgba(0, 134, 211, 0.86)',           
            textColor : '#FFF',            
            allowToastClose : true,      
            hideAfter : 3000,              
            // hideAfter : false,              
            stack : 1,                     
            textAlign : 'center',            
            position : 'bottom-center'       
          })
        }
        
        if (val !== clean) {
          ngModelCtrl.$setViewValue(clean);
          ngModelCtrl.$render();
        }
        
        return clean;
        
      });

      element.bind('keypress', function(event) {
        if(event.keyCode === 32) {
          event.preventDefault();
        }
        if (this.value.length > 4) {
        //   event.preventDefault();
          $.toast({ 
            text : 'Time invalid. It should be 12 hour format.', 
            showHideTransition : 'slide',  
            bgColor : 'rgba(0, 134, 211, 0.86)',           
            textColor : '#FFF',            
            allowToastClose : true,      
            hideAfter : 3000,              
            // hideAfter : false,              
            stack : 1,                     
            textAlign : 'center',            
            position : 'bottom-center'       
          })
        }
      });
    }
  };
})

app.config(function($stateProvider, $urlRouterProvider, $locationProvider,  $httpProvider, $compileProvider){
  $httpProvider.interceptors.push('AuthInterceptor');
  $stateProvider
    // .state('login', {
    //   url: '/',
    //   views: {
    //     'main': {
    //       templateUrl: window.location.origin + '/assets/e-claim/templates/login.html'
    //     },
    //   },
    // })

    .state('home', {
      url: '/home',
      views: {
        'navigation@home': {
          templateUrl: window.location.origin + '/assets/e-claim/templates/navigation.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/e-claim/templates/home.html'
        },
      },
    })

    .state('activity', {
      url: '/activity',
      views: {
        'navigation@activity': {
          templateUrl: window.location.origin + '/assets/e-claim/templates/navigation.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/e-claim/templates/activity.html'
        },
      },
    })

    .state('e-claim', {
      url: '/e-claim',
      views: {
        'navigation@e-claim': {
          templateUrl: window.location.origin + '/assets/e-claim/templates/navigation.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/e-claim/templates/e-claim.html'
        },
      },
    })

    $urlRouterProvider.otherwise('/home');
    // $locationProvider.html5Mode(true);
});