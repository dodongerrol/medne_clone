var app = angular.module('app', ['ui.router', 'ngCacheBuster', 'LocalStorageModule','authService', 'hrService', 'dependentsService', 'checkCtrl', 'bootstrap3-typeahead', 'ngJsonExportExcel', 'ngFileUpload','cp.ng.fix-image-orientation']);

app.run([ '$rootScope', '$state', '$stateParams', '$templateCache', 
function ($rootScope, $state, $stateParams, $templateCache) {
  
  $rootScope.$state = $state;
  $rootScope.$stateParams = $stateParams;

  if( location.hash != '#/employee-overview' ){
    $('body').css('overflow','auto');
  }


  $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });

  $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
    window.ga('create', 'UA-78188906-2', 'auto');
    window.ga('set', 'page', toState.url);
    window.ga('send', 'pageview');
    console.log( toState.url );
    if( toState.url != '/e-claim' ){
      $('.download-receipt-message').hide();
    }
    if( toState.url != '/employee-overview' ){
      $('body').css('overflow','auto');
    }
    if( toState.url == '/benefits-dashboard' ){
      $('body').addClass('bg-color-home');
    }else{
      $('body').removeClass('bg-color-home');
    }
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

app.directive('disabledSpecificCharacters', function() {
  function link(scope, elem, attrs, ngModel) {
    ngModel.$parsers.push(function(viewValue) {
      var reg = /^[^`~!@#$%\^&*+={}|[\]\\:';"<>?,/1-9/-]*$/;
      // var reg = /^[^`~!@#$%\^&*()_+={}|[\]\\:';"<>?,./1-9]*$/;
      // if view values matches regexp, update model value
      if (viewValue.match(reg)) {
        return viewValue;
      }
      // keep the model value as it is
      var transformedValue = ngModel.$modelValue;
      ngModel.$setViewValue(transformedValue);
      ngModel.$render();
      return transformedValue;
    });
  }
  return {
    restrict: 'A',
    require: 'ngModel',
    link: link
  };      
});

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
app.config(function($stateProvider, $urlRouterProvider, $locationProvider,  $httpProvider, $compileProvider){
  $httpProvider.interceptors.push('AuthInterceptor');
  // $compileProvider.debugInfoEnabled(false);
  // $httpProvider.useApplyAsync(true);
  // cfpLoadingBarProvider.parentSelector = '#loading-selected';
  // cfpLoadingBarProvider.spinnerTemplate = '<div id="loading-bar-spinner"><div class="spinner-icon"></div></div>';

  $stateProvider
    .state('introduction', {
      url: '/introduction',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/navigation.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/intro.html'
        }
      },
    })
    .state('privacy-policy', {
      url: '/privacy-policy',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/navigation.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/privacy.html'
        }
      },
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
    })
    .state('enrollment-options', {
      url: '/enrollment-options',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/enrollment-options.html'
        }
      },
    })
    .state('create-team-benefits-tiers', {
      url: '/create-team-benefits-tiers',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/create-team-benefits-tiers.html'
        }
      },
    })
    .state('enrollment-method', {
      url: '/enrollment-method',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/enrollment-method-dashboard.html'
        }
      },
    })
    .state('download-template', {
      url: '/download-template',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/emn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/download-template.html'
        }
      },
    })
    .state('prepare', {
      url: '/prepare',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/emn-2.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/prepare.html'
        }
      },
    })
    .state('upload', {
      url: '/upload',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/emn-3.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/upload.html'
        }
      },
    })
    .state('preview', {
      url: '/preview',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/emn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/preview.html'
        },
        'modal': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/edit-employee-details.html'
        }
      },
    })
    .state('successful', {
      url: '/successful',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/emn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/successful.html'
        }
      },
    })
    .state('web-input', {
      url: '/web-input',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/web.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/web-input.html'
        }
      },
    })
    .state('vacant-seat-enrollment', {
      url: '/vacant-seat-enrollment?vacant_id&type',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/web.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/external-replace-employee-enrollment.html'
        }
      },
    })
    .state('web-preview', {
      url: '/web-preview',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/web.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/web-preview.html'
        },
        'modal': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/edit-employee-details.html'
        }
      },
    })
    .state('web-successful', {
      url: '/web-successful',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/web.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/web-successful.html'
        }
      },
    })
    .state('employee-overview', {
      url: '/employee-overview',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn-emp-overview.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/employee-overview.html'
        },
        // 'modal': {
        //   templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/edit-employee-modal.html'
        // },
        // 'modal_2': {
        //   templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/delete-employee-confirmation-modal.html'
        // },
        // 'modal_3': {
        //   templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/delete-employee-withdraw-modal.html'
        // },
        // 'modal_4': {
        //   templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/replace-employee.html'
        // },
        // 'modal_5': {
        //   templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/under-development-modal.html'
        // }
      },
    })

    .state('cred-allocation', {
      url: '/cred-allocation',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/cred-allocation.html'
        },
        'modal': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/edit-employee-modal.html'
        },
        'modal_2': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/delete-employee-confirmation-modal.html'
        },
        'modal_3': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/replace-employee.html'
        }
      },
    })

    .state('activity', {
      url: '/activity',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/activity.html'
        },
        'modal': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/edit-employee-modal.html'
        },
        'modal_2': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/delete-employee-confirmation-modal.html'
        },
        'modal_3': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/replace-employee.html'
        }
      },
    })

    .state('e-claim', {
      url: '/e-claim',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/e-claim.html'
        },
        'modal': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/edit-employee-modal.html'
        },
        'modal_2': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/delete-employee-confirmation-modal.html'
        },
        'modal_3': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/replace-employee.html'
        }
      },
    })

    .state('statement', {
      url: '/statement',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/statement.html'
        },
        'modal': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/edit-employee-modal.html'
        },
        'modal_2': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/delete-employee-confirmation-modal.html'
        },
        'modal_3': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/replace-employee.html'
        }
      },
    })

    .state('congratulations', {
      url: '/congratulations',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/growing-team.html'
        }
      },
    })
    .state('payment-method', {
      url: '/payment-method',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/emn-4.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/payment-method.html'
        }
      },
    })
    .state('credit-card-form', {
      url: '/credit-card-form',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/emn-4.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/credit-card-form.html'
        }
      },
    })
    .state('payment-success', {
      url: '/payment-success',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/emn-4.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/payment-success.html'
        }
      },
    })
    .state('cheque-payment-success', {
      url: '/cheque-payment-success',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/emn-4.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/payment-success2.html'
        }
      },
    })
    .state('company-and-contacts', {
      url: '/account-and-billing/company-and-contacts',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/company-and-contacts.html'
        },
        'modal': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/account-billing-edit-business-info-modal.html'
        },
        'modal_2': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/account-billing-edit-business-contact-modal.html'
        },
        'modal_3': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/account-billing-edit-billing-contact-and-address-modal.html'
        },
        'modal_4': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/account-billing-edit-billing-contact-and-address-modal-2.html'
        }
      },
    })
    .state('transactions', {
      url: '/account-and-billing/transactions',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/transactions.html'
        },
        'modal': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/view-withdrawn-employee-details-modal.html'
        }
      },
    })
    .state('document-center', {
      url: '/account-and-billing/document-center',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/document-center.html'
        }
      },
    })
    .state('benefits-tier', {
      url: '/account-and-billing/benefits-tier',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/benefits-tier.html'
        }
      },
    })
    .state('account-and-payment', {
      url: '/account-and-billing/account-and-payment',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/account-and-payment.html'
        },
        'modal': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/account-billing-edit-payment-information-modal.html'
        },
        'modal_2': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/account-billing-edit-payment-information-details-modal.html'
        },
        'modal_3': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/credit-card-details-modal.html'
        },
        'modal_4': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/account-billing-edit-password-modal.html'
        }
      },
    })
    .state('plan-coverage', {
      url: '/plan-coverage',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/plan-coverage.html'
        }
      },
    })
    .state('local-network-partners', {
      url: '/local-network-partners',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/local-network-partners.html'
        }
      },
    })
    .state('reset-password', {
      url: '/reset-password/:token',
      views: {
        // 'navigation': {
        //   templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        // },
        'reset': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/reset.html'
        }
      }
    })
    .state('first-time-login', {
      url: '/first-time-login',
      views: {
        // 'navigation': {
        //   templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
        // },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/first-time-login.html'
        }
      }
    });

    $urlRouterProvider.otherwise('/introduction');
    // $locationProvider.html5Mode(true);
});