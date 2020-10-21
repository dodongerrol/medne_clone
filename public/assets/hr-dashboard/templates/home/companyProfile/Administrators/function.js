app.directive("administratorsDirective", [
  "$state",
  "hrSettings",
  "hrActivity",
  "$rootScope",
  "dependentsSettings",
  "$timeout",
  "serverUrl",
  "$http",
  function directive($state, hrSettings, hrActivity, $rootScope, dependentsSettings, $timeout, serverUrl, $http) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("administratorsssDirective Runnning !");
        scope.permissionSelector = false;
        scope.chooseSelectorLocation = false;
        scope.chooseSelectorDepartment = false;
        scope.permission_data = 'All Employees & Dependents'
        scope.selected_location_data = [];
        scope.selected_deparment_data = [];
        scope.showLocationSelector = false;
        scope.showEmployeeList = false;
        scope.add_admin_data = {
          is_mednefits_emp: '1',
        };

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

            
          }
          if ( type == 'departments' ) {
            scope.permission_data = 'Departments';
            scope.showDepartmentSelector = true;
            
            scope.showLocationSelector = false;

            
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
              scope.selected_deparment_data.push(data);
            } else {
              console.log('close pud siyaaa sulod');
              let index = $.inArray(data, scope.selected_deparment_data);
              scope.selected_deparment_data.splice(index, 1);
              data.status = false;             
            }
          }
        }

        scope.editSelectedPermission = function ( type, data, opt ) {
          if ( type == 'locations' ) {
            // scope.showLocationSelector = false;
            scope.chooseSelectorLocation = false;
            if ( opt ) {
              scope.selected_location_data.push(data);
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
              scope.selected_deparment_data.push(data);
            } else {
              console.log('close pud siyaaa sulod');
              let index = $.inArray(data, scope.selected_deparment_data);
              scope.selected_deparment_data.splice(index, 1);
              data.status = false;             
            }
          }
        }

        scope.isShowNoteAdmin = false;
        // scope.is_mednefits_emp = 1;
        

        scope.initializeChangePrimaryAdminCountryCode = function(){
          var settings = {
            preferredCountries: [],
            separateDialCode: true,
            initialCountry: false,
            autoPlaceholder: "off",
            utilsScript: "../assets/hr-dashboard/js/utils.js",
            onlyCountries: ["sg", "my"],
          };

          var input = document.querySelector("#phone_number_primary_admin");
          primaryAdminCountry = intlTelInput(input, settings);
          primaryAdminCountry.setCountry("SG");
          input.addEventListener("countrychange", function () {
            scope.changePrimaryData.phone_code = primaryAdminCountry.getSelectedCountryData().dialCode;
          });
        }

        scope.initializeAddAdminCountryCode = function(){
          var settings = {
            preferredCountries: [],
            separateDialCode: true,
            initialCountry: false,
            autoPlaceholder: "off",
            utilsScript: "../assets/hr-dashboard/js/utils.js",
            onlyCountries: ["sg", "my"],
          };

          var input = document.querySelector("#phone_number_add_admin");
          primaryAdminCountry = intlTelInput(input, settings);
          primaryAdminCountry.setCountry("SG");
          input.addEventListener("countrychange", function () {
            scope.add_admin_data.phone_code = primaryAdminCountry.getSelectedCountryData().dialCode;
          });
        }

        scope.continuePrimAdmin = async function () {
          scope.isShowNoteAdmin = true;

          scope.changePrimaryData = {
            phone_code: '65'
          }
          $timeout(function(){
            scope.initializeChangePrimaryAdminCountryCode();
          },400);
        }

        scope.mednefitsEmployee = function ( val ) {
          console.log(val);
          scope.sample = val;
          if (val == '0') {
            $timeout( async function(){
              await scope.initializeAddAdminCountryCode();
            },400);
          }
        }

        scope.addAdministrator = async function () {
          scope.add_admin_data.is_mednefits_emp = '1';
          console.log(scope.add_admin_data.is_mednefits_emp);
          scope.resetData();
          
          scope.add_admin_data = {
            phone_code: '65'
          } 
        }
        

        scope.editAdministrator = function () {
          scope.resetData();
        }


        scope.resetData = function () {
          scope.permission_data = 'All Employees & Dependents';
          scope.showDepartmentSelector = false;
          scope.showLocationSelector =  false;
          scope.permissionSelector = false;
        }

        scope.seachEmployeeName = async function () {
          scope.showEmployeeList = scope.showEmployeeList == true ? false : true;
        }

        scope.getPrimaryAdmin = async function () {
          await hrSettings.fetchPrimaryAdministrator()
          .then( function (response) {
            console.log(response);
            scope.primary_admin_status = response.data.status;
            scope.primary_admin_data = response.data.admin_details;
          });
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
        scope.changePrimAdmin = function () {
          if ( scope.primary_admin_status == false ) {
            console.log('need pa i check');
            $('#permission-modal').modal('show');
            $('#change-primary-admin-modal').modal('hide');
          } else {
            console.log('sulod ditso');
            $('#permission-modal').modal('hide');
          }
        }
        scope.updateHrAdmin = function () {
          if( scope.checkEmail(scope.changePrimaryData.email) == false ){
            return swal('Error!', 'Invalid Email', 'error');
          }
          let data = {
            id: scope.global_hrData.id,
            fullname: scope.changePrimaryData.fullname,
            email: scope.changePrimaryData.email,
            phone_code: scope.changePrimaryData.phone_code,
            phone_no: scope.changePrimaryData.phone_no,
          }

          $http.post(serverUrl.url + "/hr/unlink/company_account", data)
            .then(function(response){
              console.log(response);
            });
        }

        scope._getHrDetails_ = async function () {
          await hrSettings.fecthHrDetails().then(function (response) {
            scope.global_hrData = response.data.hr_account_details;
            console.log(scope.global_hrData);
          });
        };
        scope.getAdditionalAdmin = function () {
          hrSettings.fecthAdditionalAdminDetails().then(function (response) {
            console.log(response);
            scope.additional_admin_data = response.data;
          });
        }
        scope.getEmployeeName = function () {
          scope.showLoading();
          hrSettings.fetchEmployeeName().then(function (response) {
            console.log(response);
            scope.employee_data = response.data.data;
            scope.hideLoading();
          });
        }
        scope.employeeNameSelector = function ( data ) {
          scope.add_admin_data.employee_id = data.user_id;
          scope.add_admin_data.employee_name = data.name;
          scope.showEmployeeList = false;
        }
        scope.get_dept_id = [];
        scope.get_loc_id = [];
        scope.addAdmininistrator = function ( ) {
          scope.selected_location_data.map((value, key) => {
            scope.get_loc_id.push( value.LocationID )
            console.log(value)
          });
          scope.selected_deparment_data.map((value, key) => {
            scope.get_dept_id.push( value.id )
            console.log(value)
          });
          // console.log( scope.get_loc_id );
          // console.log( scope.get_dept_id );

          let data = {
            fullname: scope.add_admin_data.fullname,
            email: scope.add_admin_data.email,
            is_mednefits_employee: scope.add_admin_data.is_mednefits_emp,
            employee_id: scope.add_admin_data.employee_id,
            locations: scope.get_loc_id,
            departments: scope.get_dept_id,
            edit_employee_dependent: scope.add_admin_data.edit_employee_dependent == true ? 1: 0,
            enroll_terminate_employee: scope.add_admin_data.enroll_terminate_employee == true ? 1: 0,
            approve_reject_edit_non_panel_claims: scope.add_admin_data.approve_reject_edit_non_panel_claims == true ? 1: 0,
            create_remove_edit_admin_unlink_account: scope.add_admin_data.create_remove_edit_admin_unlink_account == true ? 1: 0,
            manage_billing_and_payments: scope.add_admin_data.manage_billing_and_payments == true ? 1: 0,
            add_location_departments: scope.add_admin_data.add_location_departments == true ? 1: 0,
          }

          scope.showLoading();
          hrSettings.updateAdditionalAdmin( data ).then(async function (response) {
            console.log(response);

            if ( response.data.status ) {
              await scope.getAdditionalAdmin();
              scope.hideLoading();
            } else {
              swal("Error!", response.data.message, "error");
              scope.hideLoading();
            }
            
          });

          console.log(data);
        }

        scope.removeAdmin = function ( data ) {
          scope.additional_add_admin_data = data;
          console.log(scope.additional_add_admin_data);
        }

        scope.isShowSuccessfulRemoveAdmin = false;
        scope.confirmRemoveAdmin = function (  ) { 
          console.log( scope.additional_add_admin_data.id );

          scope.showLoading();
          hrSettings.removeAdditionalAdmin( additional_add_admin_data.id ).then( async function (response) {
            console.log(response);
            if ( response.data.status ) {
              scope.isShowSuccessfulRemoveAdmin = true;
              await scope.getAdditionalAdmin();
              $('#remove-administrator-modal').modal('hide');
              scope.hideLoading();
            }
            
            
          });
        }

        scope.checkEmail = function (email) {
					var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					return regex.test(email);
				}

        scope.formatMomentDate  = function(date, from, to){
          return moment(date, from).format(to);
        }
        scope.closeModal  = function(){
          $('.modal').modal('hide');
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
          await scope.getPrimaryAdmin();
          await scope._getHrDetails_();
          await scope.resetData();
          await scope.getAdditionalAdmin();

          await scope.getEmployeeName();
          await scope.getLocationData();
          await scope.getDepartmentData();
        }
        scope.onLoad();
      }
    }
  }
]);