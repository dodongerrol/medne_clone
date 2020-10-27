(function (angular) {
  "use strict";

  class DepartmentAPI {
    constructor($http, serverUrl) {
      this.$http = $http;
      this.serverUrl = serverUrl.url;
    }
    enrolledEmployee () {
      return this.$http.get(`${this.serverUrl}/hr/employee_lists`)
          .then(response => response.data)
    }
    get() {
      return this.$http.get(`${this.serverUrl}/hr/get_department_list`).then((response) => response.data);
    }
    store(department_name) {
      return this.$http.post(`${this.serverUrl}/hr/create_department`, {
        department_name: department_name,
      });
    }
    update(department) {
      return this.$http.post(`${this.serverUrl}/hr/update_department`, {
        id: department.id,
        department_name: department.department_name,
      });
    }
    remove(id) {
      return this.$http.post(`${this.serverUrl}/hr/remove_department`, {
        id,
      });
    }
    permission () {
      return this.$http.get(`${this.serverUrl}/hr/get_account_permissions`)
          .then(response => response.data)
    }
    saveAllocateDepartment( data ) {
      return this.$http.post(`${this.serverUrl}/hr/allocate_employee_department`,{data})
  }
  }

  angular.module("app").service("departmentAPI", DepartmentAPI);
})(angular);
