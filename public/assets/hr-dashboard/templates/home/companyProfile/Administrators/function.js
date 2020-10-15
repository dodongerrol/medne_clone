app.directive("administratorsDirective", [
  "$state",
  "hrSettings",
  "hrActivity",
  "$rootScope",
  "dependentsSettings",
  "$timeout",
  "serverUrl",
  function directive($state, hrSettings, hrActivity, $rootScope, dependentsSettings, $timeout, serverUrl) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("administratorsssDirective Runnning !");
        scope.permissionSelector = false;
        scope.chooseSelectorLocation = false;
        scope.chooseSelectorDepartment = false;
        scope.permission_data = 'All Employees & Dependents';
        scope.locations_data = [
          {
            location: 'Location '
          },
          {
            location: 'Location 1'
          },
          {
            location: 'Location 2'
          },
          {
            location: 'Location 3'
          },
          {
            location: 'Location 4'
          },
          {
            location: 'Location 5'
          },
          {
            location: 'Location 6'
          },
          {
            location: 'Location 7'
          },
          {
            location: 'Location 8'
          },
          {
            location: 'Location 9'
          },
        ];
        scope.departments_data = [
          {
            department: 'Department '
          },
          {
            department: 'Department 1'
          },
          {
            department: 'Department 2'
          },
          {
            department: 'Department 3'
          },
          {
            department: 'Department 4'
          },
          {
            department: 'Department 5'
          },
          {
            department: 'Department 6'
          },
          {
            department: 'Department 7'
          },
          {
            department: 'Department 8'
          },
          {
            department: 'Department 9'
          },
        ];
        scope.selected_location_data = [];
        scope.selected_deparment_data = [];
        scope.showLocationSelector = false;
        scope.showEmployeeList = false;

        scope.adminPermission = function () {
          console.log('test');
          scope.permissionSelector = scope.permissionSelector == true ? false : true;
          console.log(scope.locations_data);
        }

        scope.permissionSelectorData = function ( type ) {
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

        scope.addAdministrator = async function () {
          scope.resetData();
          scope.is_mednefits_emp = 1;

          scope.addAdminData = {
            phone_code: '65'
          }
          $timeout(function(){
            scope.initializeAddAdminCountryCode();
          },400);
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

        scope.seachEmployeeName = function () {
          scope.showEmployeeList = scope.showEmployeeList == true ? false : true;
        }

        scope.getPrimaryAdmin = async function () {
          await hrSettings.fetchPrimaryAdministrator()
          .then( function (response) {
            console.log(response);
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
        }
        scope.onLoad();
      }
    }
  }
]);