var service = angular.module("memberService", []);

service.factory("memberSettings", function($http, serverUrl) {
  var memberFactory = {};

  // memberFactory.getCompanyBenefitsDashboard = function() {
  //   return $http.get(serverUrl.url + "/company-benefits-dashboard");
  // };
  
  return memberFactory;
});


