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
        scope.selected_department_data = [];
        scope.showLocationSelector = false;
        scope.showEmployeeList = false;
        scope.isShowChangeAdmin = false;
        scope.add_admin_data = {};
      
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

        scope.editSelectedPermission = function ( type, data, opt ) {
          if ( type == 'locations' ) {
            // scope.showLocationSelector = false;
            scope.chooseSelectorLocation = false;
            if ( opt ) {
              scope.edit_administrator_data.locations.push(data);
            } else {
              console.log('close pud siyaaa sulod');
              let index = $.inArray(data, scope.edit_administrator_data.locations);
              scope.edit_administrator_data.locations.splice(index, 1);
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

        scope.initializeEditAdminCountryCode = function(){
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
            scope.edit_administrator_data.phone_code = primaryAdminCountry.getSelectedCountryData().dialCode;
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
        scope.noMednefitsEmpAddAdmin = false;
        scope.mednefitsEmployee = function ( opt ) {
          scope.mednefits_selector_emp = opt;
          scope.permission_data = 'All Employees & Dependents';
          scope.showLocationSelector = false;
          scope.showDepartmentSelector = false;
          scope.activeAdminBtn = false;
          scope.isEmployeePending = false;
          scope.isEmployeeActive = false;
          

          scope.selected_locaction_data = [];
          scope.locations_data.map((res) => {
            console.log(res);
            res.status = false;
          });
          
          scope.selected_department_data = [];
          scope.departments_data.map((res) => {
            console.log(res);
            res.status = false;
          });

          if ( opt == 'yes' ) {
            scope.add_admin_data.employee_name = '';
            scope.showEmployeeList = false;
            scope.add_admin_data.edit_employee_dependent = false;
            scope.add_admin_data.enroll_terminate_employee = false;
            scope.add_admin_data.approve_reject_edit_non_panel_claims = false;
            scope.add_admin_data.create_remove_edit_admin_unlink_account = false;
            scope.add_admin_data.manage_billing_and_payments = false;
            scope.add_admin_data.add_location_departments = false;
            scope.noMednefitsEmpAddAdmin = false;
            scope.activeAdminBtn = false;

          } else {
            $timeout( async function(){
              await scope.initializeAddAdminCountryCode();
            },400);

            scope.add_admin_data.fullname = '';
            scope.add_admin_data.email = '';
            scope.add_admin_data.mobile_number = '';
            scope.add_admin_data.edit_employee_dependent = false;
            scope.add_admin_data.enroll_terminate_employee = false;
            scope.add_admin_data.approve_reject_edit_non_panel_claims = false;
            scope.add_admin_data.create_remove_edit_admin_unlink_account = false;
            scope.add_admin_data.manage_billing_and_payments = false;
            scope.add_admin_data.add_location_departments = false;
            scope.noMednefitsEmpAddAdmin = true;
            scope.activeAdminBtn = false;
          }
        }

        scope.addAdministratorBtn = async function ( data ) {
          console.log(data);
          scope.isAddAdministratorConfirm = false;
          scope.isAddAdministratorSuccess = false;
          scope.showEmployeeList = false;
          scope.chooseSelectorLocation = false;
          scope.activeAdminBtn = false;
          scope.isEmployeePending = false;
          scope.isEmployeeActive = false;
          scope.mednefits_selector_emp = 'yes';
          
          scope.selected_location_data = [];
          scope.selected_department_data = [];
          

          if ( scope.get_permissions_data.create_remove_edit_admin_unlink_account == 1 ) {
            $('#add-administrator-modal').modal('show');
            scope.add_admin_data = {
              is_mednefits_emp : 1,
              view_employee_dependent: true,
              phone_code: '65',
              employee_name: '',
            }
            console.log(scope.add_admin_data.is_mednefits_emp);
            scope.resetData();
          } else {
            $('#permission-modal').modal('show');
            $('#add-administrator-modal').modal('hide');
          }

          console.log( scope.add_admin_data.employee_name );
          console.log( scope.activeAdminBtn );
          console.log( scope.isEmployeePending );
        }
        

        scope.editAdministrator = function ( data ) {

          if ( scope.get_permissions_data.create_remove_edit_admin_unlink_account == 1 ) {
            $('#edit-administrator-modal').modal('show');
            scope.resetData();
            scope.isShowUpdateAdmin = false;
            scope.isShowSuccessAdmin = false;
            scope.add_admin_data = {
              view_employee_dependent: true,
            }

            scope.edit_administrator_data = data;
            // console.log(scope.edit_administrator_data);
            // if ( data ) {

            // }
            // scope.edit_administrator_data.fullname = data.fullname;
            scope.edit_administrator_data.edit_employee_dependent = scope.edit_administrator_data.edit_employee_dependent == 1 ? true: false;
            scope.edit_administrator_data.enroll_terminate_employee = scope.edit_administrator_data.enroll_terminate_employee == 1 ? true: false;
            scope.edit_administrator_data.approve_reject_edit_non_panel_claims = scope.edit_administrator_data.approve_reject_edit_non_panel_claims == 1 ? true: false;
            scope.edit_administrator_data.create_remove_edit_admin_unlink_account = scope.edit_administrator_data.create_remove_edit_admin_unlink_account == 1 ? true: false;
            scope.edit_administrator_data.manage_billing_and_payments = scope.edit_administrator_data.manage_billing_and_payments == 1 ? true: false;
            scope.edit_administrator_data.add_location_departments = scope.edit_administrator_data.add_location_departments == 1 ? true: false;

            if ( data.is_mednefits_employee == 0 ) {
              $timeout( async function(){
                await scope.initializeEditAdminCountryCode();
              },400);
            }
           
          } else {
            $('#permission-modal').modal('show');
            $('#edit-administrator-modal').modal('hide');
          }          
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
        
        scope.updateHrAdmin = function () {
          if( scope.checkEmail(scope.changePrimaryData.email) == false ){
            return swal('Error!', 'Invalid Email', 'error');
          }
          let data = {
            id: scope.global_hrData.id,
            fullname: scope.changePrimaryData.fullname,
            email: scope.changePrimaryData.email,
            phone_code: scope.changePrimaryData.phone_code,
            phone_number: scope.changePrimaryData.phone_no,
          }
          scope.showLoading();
          $http.post(serverUrl.url + "/hr/update_hr_details", data)
            .then(function(response){
              console.log(response);
              if ( response.data.status ) {
                scope.isShowChangeAdmin = true;
                scope.getPrimaryAdmin();
                scope.hideLoading();
              }
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
        scope.employee_list_arr = [];
        scope.getEmployeeName = function () {
          scope.showLoading();
          hrSettings.fetchEmployeeList().then(function (response) {
            console.log(response);
            scope.employee_data = response.data.data;
            console.log(scope.employee_data);
            scope.hideLoading();
          });
        }
        scope.employeeNameSelector = async function ( data ) {
          console.log( data );
          scope.add_admin_data.employee_id = data.user_id;
          scope.add_admin_data.employee_name = data.Name;
          scope.showEmployeeList = false;
          console.log(data);
          scope.employee_user_id = data.user_id;

          await scope.checkEmployeeName();
        }

        scope.get_dept_id = [];
        scope.get_loc_id = [];
        scope.isAddAdministratorConfirm = false;
        scope.isAddAdministratorSuccess = false;
        scope.addAdministrator = function () {
          scope.isAddAdministratorConfirm = true;
        }
        scope.confirmAdmininistrator = function ( ) {
          console.log(scope.add_admin_data.is_mednefits_emp);
          scope.selected_location_data.map((value, key) => {
            scope.get_loc_id.push( value.LocationID )
            console.log(value)
          });
          scope.selected_department_data.map((value, key) => {
            scope.get_dept_id.push( value.id )
            console.log(value)
          });
          // console.log( scope.get_loc_id );
          // console.log( scope.get_dept_id );
          scope.add_admin_data.is_mednefits_emp = parseInt(scope.add_admin_data.is_mednefits_emp);
          let data = {
            fullname: scope.add_admin_data.is_mednefits_emp == 1 ? scope.add_admin_data.employee_name : scope.add_admin_data.fullname,
            email: scope.add_admin_data.email,
            phone_code: scope.add_admin_data.phone_code,
            phone_no: scope.add_admin_data.mobile_number,
            is_mednefits_employee: scope.add_admin_data.is_mednefits_emp == true ? 1 : 0,
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
            scope.additional_admin_data = response.data;

            if ( response.data.status ) {
              scope.isAddAdministratorSuccess = true;

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
          
          if ( scope.get_permissions_data.create_remove_edit_admin_unlink_account == 1 ) {
            $('#remove-administrator-modal').modal('show');
            scope.isShowSuccessfullRemoveAdmin = false;
            scope.additional_add_admin_data = data;
            console.log(scope.additional_add_admin_data);
          } else {
            $('#permission-modal').modal('show');
            $('#remove-administrator-modal').modal('hide');
          }
        }

        scope.isShowSuccessfullRemoveAdmin = false;
        scope.confirmRemoveAdmin = function (  ) { 
          console.log( scope.additional_add_admin_data.id );

          scope.showLoading();
          hrSettings.removeAdditionalAdmin( scope.additional_add_admin_data.id ).then( async function (response) {
            console.log(response);
            if ( response.data.status ) {
              scope.isShowSuccessfullRemoveAdmin = true;
              await scope.getAdditionalAdmin();
              
              scope.hideLoading();
            }
            
            
          });
        }
        scope.changePrimAdmin = function () {
          scope.isShowNoteAdmin = false;
          scope.isShowChangeAdmin = false;

          if ( scope.get_permissions_data.create_remove_edit_admin_unlink_account == 1 ) {
            $('#change-primary-admin-modal').modal('show');
          } else {
            $('#permission-modal').modal('show');
            $('#change-primary-admin-modal').modal('hide');
          }
        }

        scope.isShowUpdateAdmin = false;
        scope.isShowSuccessAdmin = false;
        scope.updateAdmin = async function ( ) {
          scope.isShowUpdateAdmin = true;
        }
        scope.confirmUpdateAdmin = async function ( edit_data ) {
          let data = {
            id: edit_data.id,
            edit_employee_dependent: scope.edit_administrator_data.edit_employee_dependent == true ? 1: 0,
            enroll_terminate_employee: scope.edit_administrator_data.enroll_terminate_employee == true ? 1: 0,
            approve_reject_edit_non_panel_claims: scope.edit_administrator_data.approve_reject_edit_non_panel_claims == true ? 1: 0,
            create_remove_edit_admin_unlink_account: scope.edit_administrator_data.create_remove_edit_admin_unlink_account == true ? 1: 0,
            manage_billing_and_payments: scope.edit_administrator_data.manage_billing_and_payments == true ? 1: 0,
            add_location_departments: scope.edit_administrator_data.add_location_departments == true ? 1: 0,
          }

          scope.showLoading();
          await hrSettings.updateAddAdministrator( data ).then( async function (response) {
            console.log(response);
            if ( response.data.status ) {
              scope.isShowSuccessAdmin = true;
              await scope.getAdditionalAdmin();
              
              scope.hideLoading();
            }
            
            
          });
        }

        scope.getPermissionsData = async function () {
          await hrSettings.getPermissions()
            .then( function (response) {
              console.log(response);
              scope.get_permissions_data = response.data.data;
          });
        }
        scope.activeAdminBtn = false;
        scope.editActiveAdminBtn = false;
        scope.activeHrBtn = false;
        scope.changeBtnToActive = function () {
          if ( scope.add_admin_data.employee_name != '' && scope.isEmployeePending == false ) {
            scope.activeAdminBtn = true;
            console.log( scope.activeAdminBtn );
          }
        }
        scope.changeHrBtnActive = function() {
          scope.activeHrBtn = true;
        }
        scope.editChangeBtnActive = function () {
          scope.editActiveAdminBtn = true;
        }
        scope.isEmployeePending = false;
        scope.isEmployeeActive = false;
        scope.checkEmployeeName = async function () {
          scope.showLoading();
          await hrSettings.validateEmployeeName( scope.employee_user_id )
            .then( function (response) {
              console.log(response);
              scope.validate_data = response.data;
              if ( response.data.status == false ) {
                scope.isEmployeePending = true;
                scope.isEmployeeActive = false;
                console.log(scope.isEmployeePending,'pending');
                console.log( scope.isEmployeeActive );
                console.log( scope.activeAdminBtn )
                scope.hideLoading();
              } else {
                scope.isEmployeePending = false;
                scope.isEmployeeActive = true;
                console.log( scope.isEmployeeActive );
                console.log( scope.activeAdminBtn )
                scope.hideLoading();
              }
          });
        }
        scope.checkSearchInput = function ( data ) {
          // scope.activeAdminBtn = false;
          if ( data == '' ) {
            scope.activeAdminBtn = false;
            scope.isEmployeeActive = false;
          } 
        }
        // scope.isSearchEmpty = false;
        scope.searchEmployeeName = async function ( input ) {
          console.log(scope.activeAdminBtn);
          console.log(scope.isEmployeePending)
          if ( input ) {
            console.log(input);
            // let data = {
            //   search: input,
            // }
            let data = input;
            scope.showLoading();
            await hrSettings.searchEmployee( data )
              .then( function (response) {
                console.log(response);
                scope.employee_data = response.data.data;
                scope.showEmployeeList = true;
                scope.activeAdminBtn = false;
                scope.isEmployeeActive = false;
                scope.hideLoading();
            });
          } else {
            scope.removeEmployeeSearch();
            scope.showEmployeeList = false;
            scope.isEmployeePending = false;      
          }
        }
        scope.removeEmployeeSearch = function () {
          scope.getEmployeeName();
        }
        scope.checkEmail = function (email) {
					var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					return regex.test(email);
				}
        scope.fetchCompanyDetails = async function () {
          await hrSettings.fetchBusinessInformation()
            .then( function (response) {
              console.log(response);
              scope.get_company_details = response.data.data;
          });
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
          await scope.getPermissionsData();
          await scope.fetchCompanyDetails();
        }
        scope.onLoad();
      }
    }
  }
]);