var storage = {
  employeeDetails: null,
  replaceEmployeDetails: null,
}

app.factory('removeDependentFactory', function() {
  return {
    getEmployeeDetails: getEmployeeDetails,
    setEmployeeDetails: setEmployeeDetails,
    getReplaceEmployeeDetails: getReplaceEmployeeDetails,
    setReplaceEmployeeDetails: setReplaceEmployeeDetails,
    clearAll : clearAll,
  }

  function getEmployeeDetails(){
    return storage.employeeDetails;
  }

  function setEmployeeDetails(data){
    storage.employeeDetails = data;
    return true;
  }

  function getReplaceEmployeeDetails(){
    return storage.replaceEmployeDetails;
  }

  function setReplaceEmployeeDetails(data){
    storage.replaceEmployeDetails = data;
    return true;
  }

  function clearAll() {
    storage = {
      employeeDetails: null,
      replaceEmployeDetails: null,
    }
    return true;
  }

})