app.directive('employeeDetailsDirective', [
  '$http',
  'serverUrl',
  '$timeout',
  '$state',
  'employeeFactory',
  '$rootScope',
  'hrActivity',
  'dependentsSettings',
  'hrSettings',
  function directive($http, serverUrl, $timeout, $state, employeeFactory, $rootScope, hrActivity, dependentsSettings, hrSettings) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log('employeeDetailsDirective running!');
        scope.selected_member_id = localStorage.getItem('selected_member_id');
        var phoneNumTel = null;
        scope.dropdownEntitlement = {
          medical: false,
          wellness: false,
          med_alloc_formula: false,
          well_alloc_formula: false,
        };
        scope.emp_entitlement = {
          original_medical_entitlement: null,
          original_wellness_entitlement: null,
          medical_new_entitlement: null,
          wellness_new_entitlement: null
        };
        scope.cal_one = false;
        scope.cal_two = false;
        scope.calc_update_med = false;
        scope.calc_update_well = false;
        scope.isAddDependentsShow = false;
        scope.addDependents_arr = [];
        scope.addActiveDependent_index = 0;
        scope.dependents_ctr = 0;
        scope.dependent_data = {};

        scope.getEmployeeDetails  = async function(isRefresh){
          scope.selectedEmployee = await employeeFactory.getEmployeeDetails();
          if( scope.selectedEmployee == null || scope.selectedEmployee.user_id != Number(scope.selected_member_id) || isRefresh ){
            await scope.fetchEmployeeDetails();
          }else{
            scope.setEmployeeValues();
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
              scope.setEmployeeValues();
              // scope.hideLoading();
            });
        }

        scope.setEmployeeValues = function(){
          scope.selectedEmployee.start_date_dmy = moment(scope.selectedEmployee.start_date,['YYYY-MM-DD', 'DD/MM/YYYY']).format('DD/MM/YYYY');
          scope.selectedEmployee.end_date_dmy = moment(scope.selectedEmployee.expiry_date).format('DD/MM/YYYY');
          scope.selectedEmployee.fname = scope.selectedEmployee.name.substring(0, scope.selectedEmployee.name.lastIndexOf(" "));
          scope.selectedEmployee.lname = scope.selectedEmployee.name.substring(scope.selectedEmployee.name.lastIndexOf(" ") + 1);
          scope.selectedEmployee.start_date = moment(scope.selectedEmployee.start_date).format("DD/MM/YYYY");
          scope.selectedEmployee.start_date_format = moment(scope.selectedEmployee.start_date, 'DD/MM/YYYY').format("DD MMMM YYYY");
          scope.selectedEmployee.end_date_format = moment(scope.selectedEmployee.expiry_date).format("DD MMMM YYYY");
          scope.selectedEmployee.expiry_date = moment(scope.selectedEmployee.expiry_date).format("MM/DD/YYYY");
          scope.selectedEmployee.dob = moment(scope.selectedEmployee.dob, ['YYYY-MM-DD', 'DD/MM/YYYY']).format('DD/MM/YYYY');
        }

        scope.openUpdateEmployeeModal = function () {
          scope.isUpdateEmpInfoModalOpen = true;
          $('.datepicker').datepicker('setDate', scope.selectedEmployee.dob);
          scope.inititalizeGeoCode();
          scope.initializeDatepickers();
          $("#update-employee-modal").modal('show');
        }

        scope.inititalizeGeoCode = function () {
          $timeout(function () {
            var settings_emp_details = {
              preferredCountries: [],
              separateDialCode: true,
              initialCountry: "SG",
              autoPlaceholder: "off",
              utilsScript: "../assets/hr-dashboard/js/utils.js",
              onlyCountries: ["sg","my"],
            }
            var input3 = document.querySelector("#phoneNum");
            phoneNumTel = intlTelInput(input3, settings_emp_details);
            input3.addEventListener("countrychange", function () {
              scope.selectedEmployee.country_code = phoneNumTel.getSelectedCountryData().dialCode;
            });
          }, 300);
        }
        
        scope.initializeDatepickers = function(){
          var dt = new Date();
          // dt.setFullYear(new Date().getFullYear()-18);
          $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            endDate: dt
          });

          $('.start-date-datepicker-dependent').datepicker({
            format: 'dd/mm/yyyy',
          });
  
          $('.start-date-datepicker-dependent').datepicker().on('hide', function (evt) {
            var val = $(this).val();
            if (val == "") {
              $('.start-date-datepicker-dependent').datepicker('setDate', scope.selectedEmployee.start_date);
            }
          })
        }

        scope.checkEmail = function (email) {
          var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
          return regex.test(email);
        }

        scope.checkUpdateEmployeeForm = function (data) {
          if (!data.name) {
            swal('Error!', 'Full Name is required.', 'error');
            return false;
          }
          if ( (data.email == "" && data.phone_no == "") || (data.email == null && data.phone_no == null) ) {
            swal( 'Error!', 'Phone number or Email Address is required.', 'error' );
            return false;
          }
          if (data.email) {
            if( scope.checkEmail(data.email) == false ){
              swal( 'Error!', 'Email is invalid.', 'error' );
              return false;
            }
          }
          if( data.phone_no ){
            if (phoneNumTel.getSelectedCountryData().iso2 == 'sg' && data.phone_no.length < 8) {
              swal('Error!', 'Mobile Number for your country code should be 8 digits.', 'error');
              return false;
            }
            if (phoneNumTel.getSelectedCountryData().iso2 == 'my' && data.phone_no.length < 10) {
              swal('Error!', 'Mobile Number for your country code should be 10 digits.', 'error');
              return false;
            }
            if (phoneNumTel.getSelectedCountryData().iso2 == 'ph' && data.phone_no.length < 9) {
              swal('Error!', 'Mobile Number for your country code should be 9 digits.', 'error');
              return false;
            }
          }
          if( !data.dob ){
            swal( 'Error!', 'Date of Birth is required.', 'error' );
            return false;
          }
          return true;
        }

        scope.saveEmployee = function (data) {
          if (scope.checkUpdateEmployeeForm(data) == false) {
            return false;
          }
          swal({
            title: "Confirm",
            text: "Are you sure you want to update this employee?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0392CF",
            confirmButtonText: "Update",
            cancelButtonText: "No",
            closeOnConfirm: true,
            customClass: "updateEmp"
          },
          function (isConfirm) {
            if (isConfirm) {
              scope.showLoading();
              // console.log(data);
              var update_data = {
                name: data.name,
                dob: moment(data.dob, 'DD/MM/YYYY').format('YYYY-MM-DD'),
                nric: (data.nric == '' || data.nric == null) ? '' : data.nric,
                email: data.email,
                phone_no: data.phone_no,
                country_code: data.country_code.replace('+', ''),
                job_title: data.job_title,
                postal_code: data.postal_code,
                bank_account: data.bank_account,
                bank_code: data.bank_code,
                bank_branch: data.bank_branch,
                user_id: data.user_id,
                bank_name: data.bank_name,
                emp_id: data.employee_id,
              };
              // console.log(update_data);
              dependentsSettings.updateEmployee(update_data)
                .then(function (response) {
                  scope.hideLoading();
                  // console.log(response);
                  if (response.data.status) {
                    swal('Success!', response.data.message, 'success');
                    $("#update-employee-modal").modal('hide');
                    scope.getEmployeeDetails(true);
                    $rootScope.$broadcast('updateEmployeeDetails');
                  } else {
                    swal('Error!', response.data.message, 'error');
                  }
                });
            }
          });
        }

        scope.getMemberEntitlement = async function ( emp ) {
          scope.emp_member_id = emp;
          await hrActivity.fetchMemberEntitlement( scope.emp_member_id ) 
            .then(function(response) {
              console.log('member Entitlement',response);
              scope.emp_entitlement = response.data;
              scope.emp_entitlement.medical_entitlement_date = moment( scope.emp_entitlement.medical_entitlement_date, 'YYYY-MM-DD' ).format('DD/MM/YYYY');
              scope.emp_entitlement.wellness_entitlement_date = moment( scope.emp_entitlement.wellness_entitlement_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
              scope.emp_entitlement.medical_new_entitlement = '';
              scope.emp_entitlement.wellness_new_entitlement = '';
              scope.med_effective_date = moment().format('DD/MM/YYYY');
              scope.well_effective_date = moment().format('DD/MM/YYYY');
            });
        }
        
        scope.getMemberNewEntitlementStatus = async function ( emp ) {
          await hrActivity.fetchMemberNewEntitlementStatus( scope.emp_member_id ) 
            .then(function(response) {
              // console.log(response);
              scope.entitlement_status = response.data;
              if ( scope.entitlement_status.medical_entitlement != null && scope.entitlement_status.wellness_entitlement != null ) {
                scope.entitlement_status.medical_entitlement.effective_date = moment( scope.entitlement_status.medical_entitlement.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
                scope.entitlement_status.wellness_entitlement.effective_date = moment( scope.entitlement_status.wellness_entitlement.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
              }
          });
        }

        scope.entitlementCalc = async function ( type, cal ) {
          if ( cal == 1 ) {
            scope.cal_one = true;
          }
          if ( cal == 2) {
            scope.cal_two  = true;
          } 
          scope.entitlement_credits = {
            med_credits : scope.emp_entitlement.medical_new_entitlement,
            well_credits : scope.emp_entitlement.wellness_new_entitlement
          }
          scope.effective_date = {
            med_date : moment( $('.medical-entitlement-date').val(), 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            well_date : moment( $('.wellness-entitlement-date').val(), 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
          }
          scope.proration = {
            med_proration : scope.emp_entitlement.medical_proration,
            well_proration : scope.emp_entitlement.wellness_proration
          }
          scope.entitlement_spending_type = type;

          if ( type == 'medical' ) {
            scope.showLoading();
            await hrActivity.openEntitlementCalc( scope.emp_member_id, scope.entitlement_credits.med_credits, scope.effective_date.med_date, scope.proration.med_proration, scope.entitlement_spending_type) 
              .then(function(response) {
                scope.hideLoading();
                scope.calc_entitlement_med = response.data;
                scope.new_allocation_med = scope.calc_entitlement_med.new_allocation;
                scope.medicalCalculatedInfo = true;
                if ( response.data.status == false ) {
                  console.log('New Medical Entitlement Usage Date exceeded the Spending End Date.');
                  scope.medicalCalculatedInfo = false;
                  scope.effectiveMedDateError = true;
                  scope.calc_update_med = false;
                } else {
                  scope.effectiveMedDateError = false;
                  scope.calc_update_med = true;
                }
              });
          } 
          if (type == 'wellness') {
            scope.showLoading();
            await hrActivity.openEntitlementCalc( scope.emp_member_id, scope.entitlement_credits.well_credits, scope.effective_date.well_date, scope.proration.well_proration, scope.entitlement_spending_type) 
              .then(function(response) {
                scope.hideLoading();
                scope.calc_entitlement_well = response.data;
                scope.new_allocation_well = scope.calc_entitlement_well.new_allocation;
                scope.wellnessCalculatedInfo = true;

                if ( response.data.status == false ) {
                  console.log('New Medical Entitlement Usage Date exceeded the Spending End Date.');
                  scope.wellnessCalculatedInfo = false;
                  scope.effectiveWellDateError = true;
                  scope.calc_update_well = false;
                } else {
                  scope.effectiveWellDateError = false;
                  scope.calc_update_well = true;
                }
              });
          }
        }  

        scope.selectBank = function ( data ) {
          scope.selectedEmployee.bank_name = data;
        }

        scope.toggleAddDependents = function () {
          if (scope.isAddDependentsShow == false) {
            if (scope.dependents.total_number_of_seats == scope.dependents.occupied_seats) {
              swal({
                title: "Info",
                text: "Number of dependents head count is already zero. Please contact mednefits for assistance.",
                type: "info",
                showCancelButton: false,
                confirmButtonColor: "#0392CF",
                closeOnConfirm: true,
                customClass: "updateEmp"
              });
            } else {
              scope.initializeDatepickers();
              $('#employee-dependent-info-container').hide();
              $('.add-dependent-wrapper').fadeIn();
              scope.isAddDependentsShow = true;
            }
          } else {
            $('#employee-dependent-info-container').fadeIn();
            $('.add-dependent-wrapper').hide();
            scope.isAddDependentsShow = false;
          }
        };

        scope.checkDependentsStatus = async function () {
          await hrSettings.getMethodType()
            .then(function (response) {
              console.log(response);
              scope.dependents_status = response.data.data;
              scope.account_plan_status = {
                plan_method: response.data.data.plan.plan_method,
                account_type: response.data.data.plan.account_type
              }
            });
        }

        scope.companyDependents = async function () {
          await hrSettings.companyDependents()
            .then(function (response) {
              scope.dependents = response.data;
              scope.overall_dep_count = scope.dependents.occupied_seats + 1;
            });
        }

        scope.pushActiveDependent = function (data) {
          if (scope.checkDependentForm(data) == true) {
            scope.showLoading();
            scope.hideLoading();
            data.done = true;
            data.dob = moment(data.dob, 'DD/MM/YYYY').format('YYYY-MM-DD');
            data.start_date = moment(data.start_date, 'DD/MM/YYYY').format('YYYY-MM-DD');
            scope.addDependents_arr.push(data);
            scope.dependents_ctr += 1;
            scope.addActiveDependent_index += 1;
            scope.dependent_data = {};
          }
        };

        scope.prevActiveDependent = function () {
          if (scope.dependents_ctr != 0) {
            scope.dependents_ctr -= 1;
            scope.addActiveDependent_index -= 1;
            scope.addDependents_arr[scope.dependents_ctr].dob = moment(scope.addDependents_arr[scope.dependents_ctr].dob, 'YYYY-MM-DD').format('DD/MM/YYYY');
            scope.addDependents_arr[scope.dependents_ctr].start_date = moment(scope.addDependents_arr[scope.dependents_ctr].start_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
            scope.dependent_data = scope.addDependents_arr[scope.dependents_ctr];
          }
        }

        scope.nextActiveDependent = function () {
          scope.dependents_ctr += 1;
          scope.addActiveDependent_index += 1;
          if (scope.addDependents_arr[scope.dependent_ctr]) {
            scope.addDependents_arr[scope.dependents_ctr].dob = moment(scope.addDependents_arr[scope.dependents_ctr].dob, 'YYYY-MM-DD').format('DD/MM/YYYY');
            scope.addDependents_arr[scope.dependents_ctr].start_date = moment(scope.addDependents_arr[scope.dependents_ctr].start_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
            scope.dependent_data = scope.addDependents_arr[scope.dependent_ctr];
          } else {
            scope.dependent_data = {};
          }
        }

        scope.checkDependentForm = function (data) {
          if (!data.fullname) {
            swal('Error!', 'Full Name is required.', 'error');
            return false;
          }
          if (!data.dob) {
            swal('Error!', 'Date of Birth is required.', 'error');
            return false;
          }
          if (!data.relationship) {
            data.relationship = null;
          }
          if (!data.start_date) {
            swal('Error!', 'Start Date is required.', 'error');
            return false;
          }
          return true;
        }

        scope.saveActiveDependents = function () {
          if ((scope.dependent_data.fullname && scope.dependent_data.dob) || scope.addDependents_arr.length == 0) {
            if (scope.checkDependentForm(scope.dependent_data) == true) {
              if (!scope.addDependents_arr[scope.dependents_ctr]) {
                scope.addActiveDependent_index += 1;
                scope.dependent_data.dob = moment(scope.dependent_data.dob, 'DD/MM/YYYY').format('YYYY-MM-DD');
                scope.dependent_data.start_date = moment(scope.dependent_data.start_date, 'DD/MM/YYYY').format('YYYY-MM-DD');
                scope.addDependents_arr.push(scope.dependent_data);
              }
            } else {
              return false;
            }
          }
          var data = {
            customer_id: scope.selected_customer_id,
            employee_id: scope.selectedEmployee.user_id,
            dependents: scope.addDependents_arr
          }
          scope.showLoading();
          dependentsSettings.addDependentForEmployee(data)
            .then(function (response) {
              scope.hideLoading();
              // console.log(response);
              if (response.data.status) {
                swal('Success!', response.data.message, 'success');
                scope.addDependents_arr = [];
                scope.dependent_data = {};
                scope.dependents_ctr = 0;
                scope.toggleAddDependents();
              } else {
                swal('Error!', response.data.message, 'error');
              }
            });
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
          await scope.getMemberEntitlement(scope.selected_member_id);
          await scope.getMemberNewEntitlementStatus(scope.selected_member_id);
          await scope.entitlementCalc(scope.selected_member_id);
          await scope.checkDependentsStatus();
          await scope.companyDependents();
          scope.hideLoading();
        }
        scope.onLoad();
      }
    }
  }
]);