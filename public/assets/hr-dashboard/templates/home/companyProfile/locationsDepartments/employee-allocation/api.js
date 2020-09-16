(function (angular) {
    'use strict';

    class EmployeeAllocationApi {
        constructor($http, serverUrl) {
            this.$http = $http;
            this.serverUrl = serverUrl.url;
        }
        getEmployees(id) {
            // return this.$http.get('http://localhost:3000/employees', {
            //     id
            // }).then(response => response.data)
        }
    }

    angular.module('app')
        .service('employeeAllocationApi', EmployeeAllocationApi);
}(angular));
