var service = angular.module("dependentsService", []);

service.factory("dependentsSettings", function($http, serverUrl, Upload) {
  var dependentsFactory = {};

  dependentsFactory.fetchBenefitsTier = function(  ) {
    return $http.get(serverUrl.url + "/hr/get_plan_tiers");
  };

  dependentsFactory.addBenefitsTier = function( data ) {
    return $http.post(serverUrl.url + "/hr/create/plan_tier", data);
  };

  dependentsFactory.addEnrollEmployees = function( data ) {
    return $http.post(serverUrl.url + "/hr/create/employee_enrollment", data);
  };

  dependentsFactory.getTempEmployees = function() {
    return $http.get(serverUrl.url + "/hr/get/plan_tier_enrolless");
  };

  dependentsFactory.updateTier = function( data ) {
    return $http.post(serverUrl.url + "/hr/update_plan_tier", data);
  };
  dependentsFactory.deleteTier = function( data ) {
    return $http.post(serverUrl.url + "/hr/remove_plan_tier", data);
  };

  dependentsFactory.updateTempEnrollee = function( data ) {
    return $http.post(serverUrl.url + "/hr/update/tier_employee_enrollee_details", data);
  };

  dependentsFactory.updateTempDependent = function( data ) {
    return $http.post(serverUrl.url + "/hr/update_tier_dependent_enrollee_details", data);
  };

  dependentsFactory.saveTempEnrollees = function( data ) {
    return $http.post(serverUrl.url + "/hr/create/employee_user", data);
  };

  dependentsFactory.deleteTempEmployees = function(id) {
    return $http.get(serverUrl.url + "/remove/temp_enrollee/" + id);
  };

  dependentsFactory.removeEmployee = function(data) {
    // return $http.get(serverUrl.url + "/hr/remove_employee/" + id);
    return $http.post(serverUrl.url + "/hr/employees/withdraw", { users: data });
  };

  dependentsFactory.removeDependent = function(data) {
    return $http.post(serverUrl.url + "/hr/with_draw_dependent", data);
  };
  dependentsFactory.reserveDependentService = function(data) {
    return $http.post(serverUrl.url + "/hr/create_dependent_replace_seat", data);
  };
  dependentsFactory.replaceDependentService = function(data) {
    return $http.post(serverUrl.url + "/hr/replace_new_dependent", data);
  };

  dependentsFactory.updateEmployee = function(data) {
    return $http.post(serverUrl.url + "/hr/employee/update", data);
  };

  dependentsFactory.updateDependent = function(data) {
    return $http.post(serverUrl.url + "/hr/update_dependent_details", data);
  };

  dependentsFactory.addDependentForEmployee = function(data) {
    return $http.post(serverUrl.url + "/hr/create_dependent_accounts", data);
  };

  dependentsFactory.fetchEmpAccountSummary = function( emp_id, customer_id, last_day, dates) {
    var url = serverUrl.url + "/hr/get_employee_spending_account_summary?employee_id=" + emp_id + "&customer_id=" + customer_id + "&last_date_of_coverage=" + last_day;
    if( dates ){
      url += "&pro_allocation_start_date=" + dates.start + "&pro_allocation_end_date=" + dates.end;
    }
    return $http.get( url );
  };

  dependentsFactory.replaceEmployee = function(data) {
    return $http.post(serverUrl.url + "/hr/employee/replace", data);
  };

  dependentsFactory.reserveEmployee = function(data) {
    return $http.post(serverUrl.url + "/hr/create_employee_replace_seat", data);
  };

  dependentsFactory.updateWalletMember = function(emp_id, customer_id, medical, wellness, last_day, dates, calibrate_status) {
    var api_url = serverUrl.url + "/hr/get_employee_spending_account_summary?employee_id=" + emp_id + "&customer_id=" + customer_id + "&last_date_of_coverage=" + last_day;
    // if( wellness == true && medical == true ){
      api_url += "&calibrate_wellness=" + calibrate_status + "&calibrate_medical=" + calibrate_status;
      api_url += "&pro_allocation_start_date=" + dates.start + "&pro_allocation_end_date=" + dates.end;;
    // }else if( wellness == true ){
    //   api_url += "&calibrate_wellness=true";
    // }else{
    //   api_url += "&calibrate_medical=true";
    // }
    console.log( api_url );
    return $http.get(api_url);
  };

  dependentsFactory.enrollReplaceEmployee = function(data) {
    return $http.post(serverUrl.url + "/hr/enroll_employee", data);
  };

  dependentsFactory.enrollVacantDependent = function(data) {
    return $http.post(serverUrl.url + "/hr/enroll_dependent_vacant", data);
  };

  dependentsFactory.getDependentVacantStatus = function(id) {
    return $http.get(serverUrl.url + "/hr/check_dependent_vacant_seat?dependent_replacement_seat_id=" + id);
  };

  dependentsFactory.fetchEmpPlans = function(id) {
    return $http.get(serverUrl.url + "/hr/get_employee_plan_covers?employee_id=" + id);
  };

  return dependentsFactory;
});
