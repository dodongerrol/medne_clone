var app = angular.module('member-wallet-benefits-coverage', ['ui.router','memberWalletService']);

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
    .state('member-wallet-benefits-coverage', {
      url: '/member-wallet-benefits-coverage',
      views: {
        'main': {
          templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/memberWalletBenefitsCoverage/index.html'
        }
      }
    })

    // .state('member-wallet-benefits-coverage.member-wallet', {
    //   url: '/member-wallet',
    //   views: {
    //     'main': {
    //       templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/memberWalletBenefitsCoverage/memberWallet/index.html'
    //     }
    //   }
    // })

    
    
    

    $urlRouterProvider.otherwise('/member-wallet-benefits-coverage/mednefits-credits-account');
  
});