(function (angular) {
  "use strict";
  class DepartmentsController {
    constructor(departmentAPI) {
      (this.views = window.location.origin + "/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/departments"),
        (this.loading = false);
      this.departments = [];
      this.countries = countries();
      this.state = {
        department: {
          id: null,
          department_name: null,
        },
      };
      this.departmentAPI = departmentAPI;
      this.get_permission_data = {};
    }
    $onInit() {
      this.get();
      this.permission();
    }
    get() {
      this.departmentAPI.get().then((response) => {
        this.departments = response;
      });
    }
    add() {
      if ( this.get_permission_data.add_location_departments == 1 ) {
        this.reset();
        this.presentModal("create-department-modal", true);
      } else {
        this.presentModal('permission-modal', true);
      }
      
    }
    store() {
      $(".circle-loader").fadeIn();
      const request = this.departmentAPI.store(this.state.department.department_name);

      request.then((response) => {
        $(".circle-loader").fadeOut();
        this.presentModal("create-department-modal", false);
        this.get();
      });
    }
    edit(department) {
      if ( this.get_permission_data.add_location_departments == 1 ) {
        this.state.department = department;
        this.presentModal("edit-department-modal", true);
      } else {
        this.presentModal('permission-modal', true);
      }
      
    }
    update() {
      $(".circle-loader").fadeIn();
      const request = this.departmentAPI.update(this.state.department);

      request.then((response) => {
        $(".circle-loader").fadeOut();
        this.presentModal("edit-department-modal", false);
        this.get();
      });
    }
    attemptDelete() {
      this.presentModal("edit-department-modal", false);
      this.presentModal("remove-department-confirm-modal", true);
    }
    delete() {
      this.presentModal("remove-department-confirm-modal", false);
      $(".circle-loader").fadeIn();

      const request = this.departmentAPI.remove(this.state.department.id);

      request.then(() => {
        $(".circle-loader").fadeOut();
        this.reset();
        this.presentModal("success-department-confirm-modal", true);
        this.get();
      });
    }
    buttonState() {
      return this.departments.length > 0 ? "h-10" : "h-40";
    }
    presentModal(id, show = true) {
      $(`#${id}`).modal(show ? "show" : "hide");
    }
    setField(field, value) {
      this.formFields[field] = value;
    }
    reset() {
      this.state.department = _.mapValues(this.state.department, () => null);
    }
    permission() {
      this.departmentAPI.permission().then(response => {
          console.log(response)
          this.get_permission_data = response.data;
          console.log(this.get_permission_data);
      });
    }
  }

  angular.module("app").component("departments", {
    templateUrl: window.location.origin + "/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/departments/index.html",
    controller: DepartmentsController,
  });
})(angular);
