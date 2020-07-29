var service = angular.module("memberWalletService", []);

service.factory("memberWalletSettings", function($http, serverUrl) {
  var memberWalletFactory = {};

  // Sample

  memberWalletFactory.updateAgreeStatus = function($id) {
    return $http.get(serverUrl.url + "update/agree_status?hr_id=" + $id);
  };
  

  return memberWalletFactory;
});
