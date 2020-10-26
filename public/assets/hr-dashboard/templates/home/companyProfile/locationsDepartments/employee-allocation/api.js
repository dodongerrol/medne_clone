(function (angular) {
    'use strict';

    class EmployeeAllocationApi {
        constructor($http, serverUrl) {
            this.$http = $http;
            this.serverUrl = serverUrl.url;
        }
        getEmployeesLocation(id) {
            return this.$http.get(`${this.serverUrl}/hr/get_location_list`)
                .then(response => response.data)
        }
        getEmployeesDepartment() {
            return this.$http.get(`${this.serverUrl}/hr/get_department_list`)
                .then(response => response.data)
        }
        permission () {
            return this.$http.get(`${this.serverUrl}/hr/get_account_permissions`)
                .then(response => response.data)
        }
        enrolledEmployee () {
            return this.$http.get(`${this.serverUrl}/hr/employee_lists`)
                .then(response => response.data)
        }
        saveAllocateLocation( data ) {
            return this.$http.post(`${this.serverUrl}/hr/allocate_employee_location`,{data})
        }
        saveAllocateDepartment( data ) {
            return this.$http.post(`${this.serverUrl}/hr/allocate_employee_department`,{data})
        }
    }

    angular.module('app')
        .service('employeeAllocationApi', EmployeeAllocationApi);
}(angular));
