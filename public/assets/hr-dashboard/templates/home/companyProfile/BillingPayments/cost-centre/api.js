(function (angular) {
  'use strict';

  class CostCentreAPI {
      constructor($http, serverUrl) {
          this.$http = $http;
          this.serverUrl = serverUrl.url;
      }
      getPermission() {
          return this.$http.get(`${this.serverUrl}/hr/get_account_permissions`).then((response) => response.data);
      }
  }

  angular.module('app')
      .service('costCentreAPI', CostCentreAPI);
}(angular));
