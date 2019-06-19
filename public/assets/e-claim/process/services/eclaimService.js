var service = angular.module("eclaimService", []);

service.factory("eclaimSettings", function($http, serverUrl, Upload) {
  var eclaimFactory = {};

  eclaimFactory.loginEmp = function(data) {
    return $http.post(serverUrl.url + "/app/e_claim/login", data);
  };

  eclaimFactory.logoutEmp = function() {
    return $http.get(serverUrl.url + "/app/e_claim/logout");
  };

  eclaimFactory.empDetails = function() {
    return $http.get(serverUrl.url + "/employee/get/user_details");
  };

  eclaimFactory.employeeCurrentActivity = function(spending_type) {
    return $http.get(serverUrl.url + "/employee/get_current_spending?spending_type=" + spending_type);
  };

  eclaimFactory.employeeCurrentActivityWellness = function() {
    return $http.get(serverUrl.url + "/employee/get_current_wellness_spending");
  };

  eclaimFactory.employeeSearchActivity = function(data) {
    return $http.post(serverUrl.url + "/employee/search_eclaim_activity", data);
  };

  eclaimFactory.uploadInNetworkReceipt = function(data) {
    return Upload.upload({
      url: serverUrl.url + "/employee/create/transaction_receipt",
      data: data
    });
  };

  eclaimFactory.uploadOutNetworkReceipt = function(data) {
    return Upload.upload({
      url: serverUrl.url + "/employee/create/e_claim_receipt",
      data: data
    });
  };

  eclaimFactory.uploadEclaimReceipt = function(data) {
    return Upload.upload({
      url: serverUrl.url + "/employee/save/e_claim_receipt",
      data: data
    });
  };

  eclaimFactory.getEclaimMember = function() {
    return $http.get(serverUrl.url + "/employee/get_members");
  };

  eclaimFactory.saveEclaimMedical = function(data) {
    return $http.post(serverUrl.url + "/employee/create/e_claim", data);
  };

  eclaimFactory.saveEclaimWellness = function(data) {
    return $http.post(serverUrl.url + "/employee/create/e_claim_wellness", data);
  };

  eclaimFactory.resetPassword = function(data) {
    return $http.post(serverUrl.url + "/v2/auth/forgotpassword", data);
  };

  eclaimFactory.updatePassword = function(data) {
    return $http.post(serverUrl.url + "/employee/change_password", data);
  };

  eclaimFactory.getClaimTypes = function(opt) {
    return $http.get(serverUrl.url + "/employee/get_health_partner_lists?type=" + opt);
  };

  eclaimFactory.notification = function(id) {
    $http.get(window.location.origin + '/config/notification')
    .then(function(response){
      // console.log(response);
      OneSignal.push(["init", {
          appId: response.data,
          autoRegister: true, // Set to true to automatically prompt visitors 
          httpPermissionRequest: {
            enable: true
          },
          notifyButton: {
            enable: false /* Set to false to hide */
          }
        }]);
      OneSignal.push(["sendTag", "employee_id", id]);
    });
  };

  eclaimFactory.getPackages = function( ) {
    return $http.get(serverUrl.url + '/employee_care_package');
  };

  eclaimFactory.getEclaimPresignedUrl = function(data) {
    return $http.get(serverUrl.url + "/employee_care_package/get_e_claim_doc?id=" + data);
  };

  return eclaimFactory;
});
