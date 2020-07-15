var service = angular.module("activationService", []);

service.factory("activationSettings", function($http, serverUrl) {
  var activationFactory = {};

  activationFactory.updateAgreeStatus = function($id) {
    return $http.get(serverUrl.url + "update/agree_status?hr_id=" + $id);
  };
  
  activationFactory.createActivationPassword = function(data) {
    return $http.post(serverUrl.url + "hr/create-company-password", data);
  };

  activationFactory.validateToken = function( token ) {
    return $http.get(serverUrl.url + "/hr/validate_token?token=" + token);
  };
  
  activationFactory.requestNewLink = function( data ) {
    return $http.post(serverUrl.url + "hr/resend_hr_activation_link", data);
  };

  return activationFactory;
});
