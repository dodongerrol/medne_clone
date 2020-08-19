var storage = {
  employeeListData: null,
}

app.factory('enrollmentFactory', function() {
  return {
    getEmployeeData: getEmployeeData,
    setEmployeeData: setEmployeeData,
    clearAll : clearAll,
  }

  function getEmployeeData(){
    return storage.employeeData;
  }

  function setEmployeeData(data){
    storage.employeeData = data;
    return true;
  }

  function clearAll() {
    storage = {
      employeeData: null,
    }
    return true;
  }

});