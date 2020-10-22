var selected_member_id = localStorage.getItem('selected_member_id');
var storage = {
  employeeDetails: null,
}

app.factory('employeeFactory', function($http, serverUrl) {
  return {
    getEmployeeDetails: getEmployeeDetails,
    setEmployeeDetails: setEmployeeDetails,
    clearAll : clearAll,
  }

  function getEmployeeDetails(){
    return storage.employeeDetails;
  }

  function setEmployeeDetails(data){
    storage.employeeDetails = data;
    return true;
  }

  function clearAll() {
    storage = {
      employeeDetails: null,
    }
    return true;
  }
})