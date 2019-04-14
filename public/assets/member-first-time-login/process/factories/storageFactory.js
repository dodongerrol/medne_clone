app.factory('storageFactory', function(localStorageService, $http) {
  return {
      getEclaim: getEclaim,
      setEclaim: setEclaim,
      clearAll : clearAll,
  }

  function getEclaim(){
      return localStorageService.get('eclaim');
  }

  function setEclaim(data){
      localStorageService.set('eclaim',data);
  }

  function clearAll() {
   return localStorageService.clearAll();
  }

})