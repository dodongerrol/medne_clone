var service = angular.module("activationService", []);

service.factory("activationSettings", function($http, serverUrl) {
  var activationFactory = {};

  activationFactory.updateAgreeStatus = function() {
    return $http.get(serverUrl.url + "/update/agree_status");
  };


  return activationFactory;
});
