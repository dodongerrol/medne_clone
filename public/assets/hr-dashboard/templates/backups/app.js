var app = angular.module('app', ['ui.router', 'ngCacheBuster', 'LocalStorageModule','authService', 'hrService', 'dependentsService', 'checkCtrl', 'bootstrap3-typeahead', 'ngJsonExportExcel', 'ngFileUpload','cp.ng.fix-image-orientation']);

app.run([ '$rootScope', '$state', '$stateParams', '$templateCache', '$window',
function ($rootScope, $state, $stateParams, $templateCache, $window) {

  $rootScope.$state = $state;
  $rootScope.$stateParams = $stateParams;

  if( location.hash != '#/employee-overview' ){
    $('body').css('overflow','auto');
  }

  $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });

  $rootScope.$on('$locationChangeSuccess', function() {
     if ($window.Appcues) {
        $window.Appcues.page();
      }
   });


  $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
    // console.log( toState );
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

    window.ga('create', 'UA-78188906-2', 'auto');
    window.ga('set', 'page', toState.url);
    window.ga('send', 'pageview');
  });

}]);

app.factory('serverUrl',[
    function factory(){
      return {
        url: window.location.origin,
        // url: "https://hrapi.medicloud.sg",
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
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/benefits-home-dashboard.html'
        },
        // 'modal': {
        //   templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/faqs.html'
        // },
        // 'modal_2': {
        //   templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/faqs-new-emp.html'
        // },
        // 'modal_3': {
        //   templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/faqs-remove-emp.html'
        // },
        // 'modal_4': {
        //   templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/faqs-replace-emp.html'
        // },
        // 'modal_5': {
        //   templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/faqs-cancel-plan.html'
        // },
        // 'modal_6': {
        //   templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/modals/faqs-data-privacy.html'
        // },
      },
    })
    .state('enrollment-options', {
      url: '/enrollment-options',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
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
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
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
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/enrollment-method.html'
        }
      },
    })
    .state('web-input', {
      url: '/web-input',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/web-input.html'
        }
      },
    })
    .state('excel-enrollment', {
      url: '/excel-enrollment',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/excel-enrollment.html'
        }
      },
    })
      .state('excel-enrollment.download-template', {
        url: '/download-template',
        views: {
          'child-content@excel-enrollment': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/download-template.html'
          }
        }
      })
      .state('excel-enrollment.prepare', {
        url: '/prepare',
        views: {
          'child-content@excel-enrollment': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/prepare.html'
          }
        }
      })
      .state('excel-enrollment.upload', {
        url: '/upload',
        views: {
          'child-content@excel-enrollment': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/upload.html'
          }
        }
      })
      .state('excel-enrollment.web-preview', {
        url: '/web-preview',
        views: {
          'child-content@excel-enrollment': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/web-preview.html'
          },
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

    // EMPLOYEE OVERVIEW STATE
    // .state('employee-overview', {
    //   url: '/employee-overview',
    //   views: {
    //     'navigation': {
    //       templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
    //     },
    //     'main': {
    //       templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/employee-overview.html'
    //     },
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
    //   },
    // })
    .state('employee-overview', {
      url: '/employee-overview',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeOverview/index.html'
        },
      },
    })

    // ------------ MEMBER INFORMATION STATES --------------- //
      .state('member', {
        url: '/member/:member_id',
        params: {
          member_id: localStorage.getItem('selected_member_id')
        },
        views: {
          'navigation': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
          },
          'main': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/employeeInfoContainer/index.html'
          },
        },
      })
      .state('member.emp-details', {
        url: '/emp-details',
        views: {
          'right-content@member': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/employeeDetails/index.html'
          },
        },
      })
      .state('member.dep-details', {
        url: '/dep-details',
        views: {
          'right-content@member': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/dependentDetails/index.html'
          },
        },
      })
      .state('member.credit-allocation', {
        url: '/credit-allocation',
        views: {
          'right-content@member': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/creditAllocation/index.html'
          },
        },
      })
      .state('member.emp-settings', {
        url: '/emp-settings',
        views: {
          'right-content@member': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/employeeSettings/index.html'
          },
        },
      })
      .state('member.health-spending-account-summary', {
        url: '/health-spending-account-summary',
        views: {
          'right-content@member': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/removeEmployee/healthSpendingAccountSummary/index.blade.php'
          },
        },
      })
      .state('member.health-partner-access', {
        url: '/health-partner-access',
        views: {
          'right-content@member': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/employeeHealthProviderAccess/index.html'
          },
        },
      })

      
      .state('member-remove', {
        url: '/member-opt/:member_id',
        params: {
          member_id: localStorage.getItem('selected_member_id')
        },
        views: {
          'navigation': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
          },
          'main': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/removeEmployee/container/index.blade.php'
          },
        },
      })
      .state('member-remove.remove-emp-inputs', {
        url: '/remove/details',
        views: {
          'remove-content@member-remove': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/removeEmployee/employeeDetailsInput/index.blade.php'
          },
        },
      })
      .state('member-remove.remove-emp-checkboxes', {
        url: '/remove/option',
        views: {
          'remove-content@member-remove': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/removeEmployee/removeCheckboxOptions/index.blade.php'
          },
        },
      })
      .state('member-remove.remove-replace-emp', {
        url: '/remove/replace',
        views: {
          'remove-content@member-remove': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/removeEmployee/replaceEmployeeInput/index.blade.php'
          },
        },
      })
      .state('member-remove.health-spending-account-summary', {
        url: '/remove/health-spending-account-summary',
        views: {
          'remove-content@member-remove': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/removeEmployee/healthSpendingAccountSummary/index.blade.php'
          },
        },
      })
      .state('member-remove.health-spending-account-confirm', {
        url: '/remove/health-spending-account-confirm',
        views: {
          'remove-content@member-remove': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/removeEmployee/healthSpendingAccountConfirm/index.blade.php'
          },
        },
      })
      .state('member-remove.refund-summary', {
        url: '/remove/refund-summary',
        views: {
          'remove-content@member-remove': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeInformation/removeEmployee/refundSummary/index.blade.php'
          },
        },
      })
    // ------------ END OF MEMBER INFORMATION STATES --------------- //

    // ----------------------------------------- //

    .state('bulk-cred-allocation', {
      url: '/bulk-cred-allocation',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/bulk-cred-allocation.html'
        },
      },
    })

    .state('cred-allocation', {
      url: '/cred-allocation',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/bulk-cred-allocation.html'
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
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/activity.html'
        },
      },
    })

    .state('e-claim', {
      url: '/e-claim',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/e-claim.html'
        },
      },
    })

    .state('statement', {
      url: '/statement',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/statement.html'
        },
      },
    })

    .state('company-and-contacts', {
      url: '/account-and-billing/company-and-contacts',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
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
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
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
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
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
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
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
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/account-and-payment.html'
        },
      },
    })
    .state('plan-coverage', {
      url: '/plan-coverage',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
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
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/local-network-partners.html'
        }
      },
    })
    .state('reset-password', {
      url: '/reset-password/:token',
      views: {
        'reset': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/reset.html'
        }
      }
    })
    .state('first-time-login', {
      url: '/first-time-login',
      views: {
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/first-time-login.html'
        }
      }
    })
    .state('settings', {
      url: '/settings',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/settings.html'
        }
      }
    })
    .state('settings.cap-per-visit', {
      url: '/cap-per-visit',
      views: {
        'child-content@settings': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/cap-per-visit.html'
        }
      }
    })
    .state('settings.block-health-partners', {
      url: '/block-health-partners',
      views: {
        'child-content@settings': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/block-health-partners.html'
        }
      }
    })
    .state('expired-link', {
      url: '/expired-link',
      views: {
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/expired-link/expired-link.html'
        }
      }
    })
    .state('T&C', {
      url: '/T&C',
      views: {
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/t&c/t&c.html'
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

    // ------------------------  NEW EMPLOYEE ENROLLMENT STATES  --------------------------- //

      .state('enrollment', {
        url: '/enrollment',
        views: {
          'navigation': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/bdn.html'
          },
          'main': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeEnrollment/container/index.html'
          }
        }
      })
      .state('enrollment.select-account-type', {
        url: '/select-account-type',
        views: {
          'enrollment-content@enrollment': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeEnrollment/selectAccountType/index.html'
          }
        }
      })
      .state('enrollment.input-table', {
        url: '/input-table',
        views: {
          'enrollment-content@enrollment': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeEnrollment/inputTableEnrollment/index.html'
          }
        }
      })
      .state('enrollment.excel-upload', {
        url: '/excel-upload',
        views: {
          'enrollment-content@enrollment': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeEnrollment/excelEnrollment/index.html'
          }
        }
      })
      .state('enrollment.preview-table', {
        url: '/preview-table',
        views: {
          'enrollment-content@enrollment': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeEnrollment/previewTable/index.html'
          }
        }
      })
      .state('enrollment.send-employee-activation', {
        url: '/send-employee-activation',
        views: {
          'enrollment-content@enrollment': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeEnrollment/sendEmployeeActivation/index.html'
          }
        }
      })
      .state('enrollment.enterprise-summary', {
        url: '/enterprise-summary',
        views: {
          'enrollment-content@enrollment': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeEnrollment/enterpriseSummary/index.html'
          }
        }
      })
      .state('enrollment.preview-communication', {
        url: '/preview-communication',
        views: {
          'enrollment-content@enrollment': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeEnrollment/previewCommunication/index.html'
          }
        }
      })


    // -----------------------  END OF NEW EMPLOYEE ENROLLMENT STATES  -------------------- //


    // ============== REMOVE EMPLOYEE STATES ================== //

      .state('employee-overview.remove-emp-inputs', {
        url: '/remove-emp-inputs',
        views: {
          'remove-emp-content@employee-overview': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/removeEmployee/employeeDetailsInput/index.blade.php'
          },
        },
      })
      .state('employee-overview.remove-emp-checkboxes', {
        url: '/remove-emp-checkboxes',
        views: {
          'remove-emp-content@employee-overview': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/removeEmployee/removeCheckboxOptions/index.blade.php'
          },
        },
      })
      .state('employee-overview.refund-summary', {
        url: '/refund-summary',
        views: {
          'remove-emp-content@employee-overview': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/removeEmployee/refundSummary/index.blade.php'
          },
        },
      })
      .state('employee-overview.remove-replace-emp', {
        url: '/remove-replace-emp',
        views: {
          'remove-emp-content@employee-overview': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/removeEmployee/replaceEmployeeInput/index.blade.php'
          },
        },
      })
      .state('employee-overview.health-spending-account-summary', {
        url: '/health-spending-account-summary',
        views: {
          'remove-emp-content@employee-overview': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/removeEmployee/healthSpendingAccountSummary/index.blade.php'
          },
        },
      })
      .state('employee-overview.health-spending-account-confirm', {
        url: '/health-spending-account-confirm',
        views: {
          'remove-emp-content@employee-overview': {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/removeEmployee/healthSpendingAccountConfirm/index.blade.php'
          },
        },
      })

    // ======================================================== //

    .state('member-wallet-benefits-coverage', {
      url: '/member-wallet-benefits-coverage',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/memberWalletBenefitsCoverage/index.html'
        }
      }
    })

    .state('member-wallet-benefits-coverage.medical-wallet', {
      url: '/medical-wallet',
      views: {
        'child-content@member-wallet-benefits-coverage': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/memberWalletBenefitsCoverage/medicalWallet/index.html'
        }
      }
    })

    .state('member-wallet-benefits-coverage.wellness-wallet', {
      url: '/wellness-wallet',
      views: {
        'child-content@member-wallet-benefits-coverage': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/memberWalletBenefitsCoverage/wellnessWallet/index.html'
        }
      }
    })

    .state('member-wallet-benefits-coverage.mednefits-basic-plan', {
      url: '/mednefits-basic-plan',
      views: {
        'child-content@member-wallet-benefits-coverage': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/memberWalletBenefitsCoverage/mednefitsBasicPlan/index.html'
        }
      }
    })

    .state('member-wallet-benefits-coverage.mednefits-enterprise-plan', {
      url: '/mednefits-enterprise-plan',
      views: {
        'child-content@member-wallet-benefits-coverage': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/memberWalletBenefitsCoverage/mednefitsEnterprisePlan/index.html'
        }
      }
    })

    .state('member-wallet-benefits-coverage.out-of-pocket', {
      url: '/out-of-pocket',
      views: {
        'child-content@member-wallet-benefits-coverage': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/memberWalletBenefitsCoverage/outOfPocket/index.html'
        }
      }
    })

    .state('member-wallet-benefits-coverage.mednefits-credits-account', {
      url: '/mednefits-credits-account',
      views: {
        'child-content@member-wallet-benefits-coverage': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/memberWalletBenefitsCoverage/mednefitsCreditAccount/index.html'
        }
      }
    })

    .state('spending-billing', {
      url: '/spending-billing',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/billing/index.html'
        }
      }
    })

    // Account Settings

    .state('account-settings', {
      url: '/account-settings',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/accountSettings/index.html'
        }
      }
    })

    // Company Profile

    .state('company-profile', {
      url: '/company',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/container/index.html'
        }
      }
    })
    .state('company-profile.company-overview', {
      url: '/overview',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'company-profile-content@company-profile': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/companyOverview/index.html'
        }
      }
    })
    .state('company-profile.locations-departments', {
      url: '/locations-departments',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'company-profile-content@company-profile': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/index.html'
        }
      }
    })
    .state('company-profile.billings-payments', {
      url: '/billings-payments',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'company-profile-content@company-profile': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/billingPayments/index.html'
        }
      }
    })
    .state('company-profile.administrators', {
      url: '/administrators',
      views: {
        'navigation': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/navs/global-header.html'
        },
        'company-profile-content@company-profile': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/administrators/index.html'
        }
      }
    })


    $urlRouterProvider.otherwise('/benefits-dashboard');
    // $urlRouterProvider.otherwise('/introduction');
    // $locationProvider.html5Mode(true);
});