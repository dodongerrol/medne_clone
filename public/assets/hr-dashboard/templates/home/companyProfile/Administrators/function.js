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

            await scope.getLocationData();
          }
          if ( type == 'departments' ) {
            scope.permission_data = 'Departments';
            scope.showDepartmentSelector = true;
            
            scope.showLocationSelector = false;

            await scope.getDepartmentData();
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
            scope.addAdminData.phone_code = primaryAdminCountry.getSelectedCountryData().dialCode;
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

        scope.addAdministrator = function () {
          scope.is_mednefits_emp = '1';
          console.log(scope.is_mednefits_emp);
          scope.resetData();
          
          scope.addAdminData = {
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
          await scope.getEmployeeName();
        }

        scope.getPrimaryAdmin = async function () {
          await hrSettings.fetchPrimaryAdministrator()
          .then( function (response) {
            console.log(response);
            scope.primary_admin_status = response.data.status;   
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
          });
        }
        scope.getEmployeeName = function () {
          hrSettings.fetchEmployeeName().then(function (response) {
            console.log(response);
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
        }
        scope.onLoad();
      }
    }
  }
]);