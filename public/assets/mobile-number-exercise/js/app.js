var app = angular.module("app", ['ui.router',]);
window.base_url = window.location.origin + "/app/";


app.config(function($stateProvider, $urlRouterProvider, $locationProvider,  $httpProvider, $compileProvider){
  // $httpProvider.interceptors.push('AuthInterceptor');
  $stateProvider
    .state('claim', {
      url: '/',
      views: {
        'main': {
          templateUrl: window.location.origin + '/assets/mobile-number-exercise/index.html'
        },
      },
    })

    $urlRouterProvider.otherwise('/');
});



app.filter("cmdate", [
  "$filter",
  function($filter) {
    return function(input, format) {
      if (input && format) {
        return $filter("date")(new Date(input), format);
      }
    };
  }
]);
// app.factory('AuthToken', function($window){
//   var authTokenFactory = {};
//   authTokenFactory.getToken = function( ) {
//     return $window.localStorage.getItem('token');
//   }
//   authTokenFactory.setToken = function( token ) {
//     if(token) {
//       return $window.localStorage.setItem('token', token);
//     } else {
//       $window.localStorage.removeItem('token');
//     }
//   }
//   return authTokenFactory;
// });
// app.factory('AuthInterceptor', function($q, $window, $injector, $rootScope, AuthToken){
//   var interceptorFactory = {};
//   interceptorFactory.request = function( config ) {
//     var token = AuthToken.getToken( );

//     if(token) {
//       config.headers['Authorization'] = token;
//     }
//     return config;
//   };
//   interceptorFactory.response = function( response ) {
//     // console.log(response);
//     return response;
//   };
//   interceptorFactory.requestError = function( response ) {
//     return $q.reject(response);
//   };
//   interceptorFactory.responseError = function( response ) {
//     // if(response.status == 403) {
//     //   if(!response.config.headers.Authorization) {
//     //     // window.location.href = window.location.origin + '/company-benefits-dashboard-login';
//     //     $('#claim_modal').modal('show');
//     //     $('#claim_message').text(response.data);
//     //     $('#claim_modal #login-status').show();
//     //   }
//     // } else if(response.status == 401) {
//     //   $('#claim_modal').modal('show');
//     //   $('#claim_message').text(response.data);
//     //   $('#claim_modal #login-status').show();
//     // } else 

//     if(response.status == 500 || response.status == 408) {
//       $('#claim_modal').modal('show');
//       $('#claim_message').text('Ooops! Something went wrong. Please check you internet connection or reload the page.');
//       $('#claim_modal #login-status').show();
//     } else {
//       $('#claim_modal').modal('show');
//       $('#claim_message').text('Ooops! Something went wrong. Please check you internet connection or reload the page.');
//       $('#claim_modal #login-status').show();
//     }
//     return $q.reject(response);
//   };
//   return interceptorFactory;
// });
// app.factory('socket', function ($rootScope) {
//   var socket = io.connect('https://sockets.medicloud.sg');
//   return {
//     on: function (eventName, callback) {
//       socket.on(eventName, function () {  
//         var args = arguments;
//         $rootScope.$apply(function () {
//           callback.apply(socket, args);
//         });
//       });
//     },
//     emit: function (eventName, data, callback) {
//       socket.emit(eventName, data, function () {
//         var args = arguments;
//         $rootScope.$apply(function () {
//           if (callback) {
//             callback.apply(socket, args);
//           }
//         });
//       })
//     }
//   };
// });
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
});