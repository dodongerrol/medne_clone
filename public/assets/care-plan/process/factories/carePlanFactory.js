app.factory('carePlanFactory', function(localStorageService, $http) {
  return {
      getCarePlan: getCarePlan,
      setCarePlan: setCarePlan,
      clearAll : clearAll,
      setLastRoute : setLastRoute,
      getLastRoute : getLastRoute,
      getNatureOfBusiness : getNatureOfBusiness,
      getJobTitle: getJobTitle,
  }

  function getCarePlan(){
      return localStorageService.get('care_plan');
  }

  function setCarePlan(data){
      localStorageService.set('care_plan',data);
  }

  function clearAll() {
   return localStorageService.clearAll();
  }

  function getLastRoute(){
      return localStorageService.get('last_route');
  }

  function setLastRoute(data){
      localStorageService.set('last_route',data);
  }

  function getNatureOfBusiness( ) {
    return $http.get(window.location.origin + '/care_plan_json/nature.json');
  }

  function getJobTitle( ) {
    return $http.get(window.location.origin + '/care_plan_json/job.json');
  }
  
})