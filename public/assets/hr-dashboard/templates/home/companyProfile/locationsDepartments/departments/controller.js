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
      this.selectedDepartmentEmpArr =[];
      this.selectEmployeeId = [];
    }
    $onInit() {
      this.get();
      this.permission();
      this.getEnrolledEmployee();
    }
    get() {
      this.departmentAPI.get().then((response) => {
        this.departments = response;
        // console.log(this.departments);
        this.departments.map((res) => {
          // res.selected = false;
          this.department_id = res.id;
          console.log(res);
        });
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
    selectProperty(prop, opt){
      // console.log(scope.selectedEmpArr);
      prop.selected = opt;
      if(opt){
          console.log(prop);
          this.selectedDepartmentEmpArr.push(prop);
          this.selectEmployeeId.push(prop.user_id);
          console.log(this.selectEmployeeId);
          console.log(this.selectedDepartmentEmpArr);
      }else{
          var index = $.inArray(prop, this.selectedDepartmentEmpArr);
          this.selectedDepartmentEmpArr.splice(index, 1);
          
          var index_id = $.inArray(prop.user_id, this.selectEmployeeId);
          this.selectEmployeeId.splice(index_id, 1);
          console.log( this.selectEmployeeId );
      }
    }
    saveDepartment() {
      let data = {
          employee_ids: this.selectEmployeeId,
          location_id: this.department_id,
      }

      const request = this.departmentAPI.saveAllocateDepartment(data);
      
      $(".circle-loader").fadeIn();
      console.log(data);

      request.then((response) => {
          console.log(response)
          if (response.data.status) {
            $(".circle-loader").fadeOut();
            this.presentModal("allocate-employees-department-modal", false); 
            this.get(); 
          } else {
            $(".circle-loader").fadeOut();
            return swal('Error!', response.data.message, 'error');
          }
          
      })    
    }
    employeeAllocate() {
      this.get_employee_names.map((res) => {
        res.selected = false;
        console.log(res.selected);
      });
      this.selectedDepartmentEmpArr =[];
      this.presentModal("allocate-employees-department-modal", true); 
    }
    getEnrolledEmployee() {
      this.departmentAPI.enrolledEmployee().then(response => {
          console.log(response)
          this.get_employee_names = response.data;
          console.log(this.get_employee_names);
      });
    }
    dismiss() {
      this.presentModal("allocate-employees-department-modal", false); 
    }
  }

  angular.module("app").component("departments", {
    templateUrl: window.location.origin + "/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/departments/index.html",
    controller: DepartmentsController,
  });
})(angular);
