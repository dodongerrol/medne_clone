app.factory('dashboardFactory', function(localStorageService, $http) {
  return {
      getEmployees: getEmployees,
      setEmployees: setEmployees,
      getActivePlanID: getActivePlanID,
      setActivePlanID: setActivePlanID,
      getHeadCountStatus: getHeadCountStatus,
      setHeadCountStatus: setHeadCountStatus,
      getEnrolledEmp: getEnrolledEmp,
      setEnrolledEmp: setEnrolledEmp,
      clearAll : clearAll,
  }

  function getEmployees(){
      return localStorageService.get('employees');
  }

  function setEmployees(data){
      localStorageService.set('employees',data);
  }

  function getActivePlanID(){
      return localStorageService.get('active_plan_id');
  }

  function setActivePlanID(data){
      localStorageService.set('active_plan_id',data);
  }

  function getHeadCountStatus(){
      return localStorageService.get('head_count_status');
  }

  function setHeadCountStatus(data){
      localStorageService.set('head_count_status',data);
  }

  function getEnrolledEmp(){
      return localStorageService.get('emp_count_enrolled');
  }

  function setEnrolledEmp(data){
      localStorageService.set('emp_count_enrolled',data);
  }

  function clearAll() {
   return localStorageService.clearAll();
  }

})