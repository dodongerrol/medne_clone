app.directive('employeeSettingsDirective', [
  '$http',
  'serverUrl',
  '$timeout',
  '$state',
  'employeeFactory',
  'hrSettings',
  function directive($http, serverUrl, $timeout, $state, employeeFactory, hrSettings) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log('employeeSettingsDirective running!');
        scope.selected_member_id = localStorage.getItem('selected_member_id');
        scope.add_admin_data = {
          is_mednefits_emp : 1,
          view_employee_dependent: true,
          phone_code: '65',
          employee_name: '',
        }
        // scope.permission_data = 'All Employees & Dependents';
        scope.showEmployeeList = false;
        scope.isEmployeePending = false;

        scope.getEmployeeDetails  = async function(isRefresh){
          scope.selectedEmployee = await employeeFactory.getEmployeeDetails();
          if( scope.selectedEmployee == null || scope.selectedEmployee.user_id != Number(scope.selected_member_id) || isRefresh ){
            await scope.fetchEmployeeDetails();
          }else{
            // scope.hideLoading();
          }
        }
        scope.fetchEmployeeDetails  = async function(){
          scope.showLoading();
          await $http.get(serverUrl.url + "/hr/employee/" + scope.selected_member_id)
            .then(function(response){
              console.log(response);
              scope.selectedEmployee = response.data.data;
              employeeFactory.setEmployeeDetails(scope.selectedEmployee);
              // scope.hideLoading();
            });
        }

        scope._resetActivation_ = function () {
          let params = {
            id: scope.selectedEmployee.member_id,
          }
          scope.showLoading();
          hrSettings.employeeResetActivation ( params  )
            .then(function( response ) {
              console.log(response);
              if ( response.data.status == true ) {
                scope.hideLoading();
                swal('Success!', response.data.message, 'success');
              }
            });
        }

        scope._resetPassword_ = function() {
          let params = {
            id: scope.selectedEmployee.member_id,
          }
          scope.showLoading();
          hrSettings.employeeResetPassword ( params  )
            .then(function( response ) {
              console.log(response);
              if ( response.data.status == true ) {
                scope.hideLoading();
                swal('Success!', response.data.message, 'success');
              }
            });
        }

        scope.manageCap = function () {
          $("#manage-cap-modal").modal('show');
        }

        scope.submitCapPerVisit = function (cap) {
          scope.showLoading();
          var data = {
            employee_id: scope.selectedEmployee.user_id,
            cap_amount: cap,
          }
          hrSettings.updateCapPerVisit(data)
            .then(function (response) {
              scope.hideLoading();
              if (response.data.status) {
                scope.cap_per_visit = 0;
                swal('Success!', response.data.message, 'success');
                $("#manage-cap-modal").modal('hide');
              } else {
                swal('Error!', response.data.message, 'error');
              }
            });
        }
        scope.permissionSelector = false;
        scope.activeAdminBtn = false;
        scope.showLocationSelector = false;
        scope.showDepartmentSelector = false;
        scope.changeBtnToActive = function () {
          scope.activeAdminBtn = true;
          console.log( scope.activeAdminBtn );
        }
        scope.adminPermission = function () {
          console.log('test');
          scope.permissionSelector = scope.permissionSelector == true ? false : true;
          // console.log(scope.locations_data);
        }

        scope.permissionSelectorData = async function ( type ) {
          scope.permissionSelector = false;

          if ( type == 'locations' ) {
            scope.permission_data = 'Locations';
            scope.showLocationSelector = true;

            scope.showDepartmentSelector = false;

            // this is for edit
            // scope.locations_data.map(function(value,key){
            //   if( _.findIndex(scope.edit_administrator_data.locations, {'LocationID' : value.LocationID}) > -1 ){
            //     value.status = true;
            //   }
            // });
            scope.selected_location_data = [];
            scope.locations_data.map((res) => {
              console.log(res);
              res.status = false;
            });

          }
          if ( type == 'departments' ) {
            scope.permission_data = 'Departments';
            scope.showDepartmentSelector = true;
            
            scope.showLocationSelector = false;

            scope.selected_department_data = [];
            scope.departments_data.map((res) => {
              console.log(res);
              res.status = false;
            });
            
          }
          console.log(type);
        }
        scope.chooseSelector = function ( type ) {
          if ( type == 'locations' ) {
            scope.chooseSelectorLocation = scope.chooseSelectorLocation == true ? false : true;
          }
          if ( type == 'departments' ) {
            scope.chooseSelectorDepartment = scope.chooseSelectorDepartment == true ? false : true;
          }
        }
        scope.selectedPermission = function ( type, data, opt ) {
          if ( type == 'locations' ) {
            // scope.showLocationSelector = false;
            scope.chooseSelectorLocation = false;
            if ( opt ) {
              scope.selected_location_data.push(data);
              console.log(scope.selected_location_data);
            } else {
              console.log('close pud siyaaa sulod');
              let index = $.inArray(data, scope.selected_location_data);
              scope.selected_location_data.splice(index, 1);
              data.status = false;             
            }
          }
          if ( type == 'departments' ) {
            // scope.showDepartmentSelector = false;
            scope.chooseSelectorDepartment = false;
            if ( opt ) {
              scope.selected_department_data.push(data);
            } else {
              console.log('close pud siyaaa sulod');
              let index = $.inArray(data, scope.selected_department_data);
              scope.selected_department_data.splice(index, 1);
              data.status = false;             
            }
          }
        }
        scope.resetData = function () {
          scope.permission_data = 'All Employees & Dependents';
          scope.showDepartmentSelector = false;
          scope.showLocationSelector =  false;
          scope.permissionSelector = false;
        }
        scope.getLocationData = async function () {
          scope.showLoading();
          await hrSettings.fetchLocationData()
          .then( function (response) {
            console.log(response);
            scope.locations_data = response.data;
            scope.hideLoading();
          });
        }

        scope.getDepartmentData = async function () {
          scope.showLoading();
          await hrSettings.fetchDepartmentData()
          .then( function (response) {
            console.log(response);
            scope.departments_data = response.data;
            scope.hideLoading();
          });
        }
        scope.assignAdmin = function () {
          scope.isAddAdministratorConfirm = false;
          scope.isAddAdministratorSuccess = false;
          scope.chooseSelectorLocation = false;
          scope.chooseSelectorDepartment = false;
          scope.showLocationSelector = false;
          scope.showDepartmentSelector = false;
          scope.activeAdminBtn = false;
          scope.permission_data = 'All Employees & Dependents';
          scope.selected_location_data = [];
          scope.selected_department_data = [];

          scope.add_admin_data = {
            is_mednefits_emp : 1,
            view_employee_dependent: true,
            phone_code: '65',
            employee_name: '',
          }
        }

        

        // CUSTOM REUSABLE FUNCTIONS
        
        scope.formatMomentDate  = function(date, from, to){
          return moment(date, from).format(to);
        }
        scope.closeModal  = function(){
          $('.modal').modal('hide');
        }
        scope.range = function (range) {
          var arr = [];
          for (var i = 0; i < range; i++) {
            arr.push(i);
          }
          return arr;
        }
        scope.showLoading = function () {
          $(".circle-loader").fadeIn();
        };
        scope.hideLoading = function () {
          $timeout(function () {
            $(".circle-loader").fadeOut();
          }, 10);
        };

        scope.onLoad  = async function(){
          scope.showLoading();
          await scope.getEmployeeDetails();
          scope.hideLoading();
        }
        scope.onLoad();
      }
    }
  }
]);