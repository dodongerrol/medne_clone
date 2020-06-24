var storage = {
  activationDetails: null,
}

app.factory('activationFactory', function() {
  return {
    getActivationDetails: getActivationDetails,
    setActivationDetails: setActivationDetails,
    clearAll : clearAll,
  }

  function getActivationDetails(){
    return storage.activationDetails;
  }

  function setActivationDetails(data){
    storage.activationDetails = data;
    return true;
  }

  function clearAll() {
    storage = {
      activationDetails: null,
    }
    return true;
  }

})