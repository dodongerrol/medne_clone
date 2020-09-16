(function (angular) {
    'use strict';

    class DepartmentAPI {
        constructor($http, serverUrl) {
            this.$http = $http;
            this.serverUrl = serverUrl.url;
        }
        get() {
            return this.$http.get(`${this.serverUrl}/hr/get_department_list`)
                .then((response) => response.data.data.department_name);
        }
        store (department_name) {
            return this.$http.post(`${this.serverUrl}/hr/create_department`, {
                department_name: department_name
            });
        }
        update (department) {
            return this.$http.post(`${this.serverUrl}/hr/update_department`, {
                id: department.id,
                department_name: department.department_name
            });
        }
        delete (id) {
            return this.$http.get(`${this.serverUrl}/hr/remove_department?id=${id}`)
        }
    }

    angular.module('app')
        .service('departmentAPI', DepartmentAPI);
}(angular));
