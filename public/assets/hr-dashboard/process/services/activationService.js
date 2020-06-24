var service = angular.module("activationService", []);

service.factory("activationSettings", function($http, serverUrl) {
  var activationFactory = {};

  activationFactory.updateAgreeStatus = function() {
    return $http.get(serverUrl.url + "/update/agree_status");
  };
  
  activationFactory.createActivationPassword = function(data) {
    return $http.post(serverUrl.url + "hr/create-password-activated", data);
  };

  activationFactory.validateToken = function( token ) {
    return $http.get(serverUrl.url + "/hr/validate_token?token=" + token);
  };

  return activationFactory;
});
