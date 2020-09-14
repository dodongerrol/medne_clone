(function (angular) {
    'use strict';

    class DepartmentAPI {
        constructor($http, serverUrl) {
            this.$http = $http;
            this.serverUrl = serverUrl.url;
        }
        getDepartments() {
            return this.$http.get('http://localhost:3000/departments')
                .then(response => response.data)
        }
    }

    angular.module('app')
        .service('departmentAPI', DepartmentAPI);
}(angular));
