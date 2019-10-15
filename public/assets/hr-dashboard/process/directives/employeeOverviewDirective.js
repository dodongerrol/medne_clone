app.directive("employeeOverviewDirective", [
  "$state",
  "hrSettings",
  "$rootScope",
  "dashboardFactory",
  "dependentsSettings",
  "$timeout",
  function directive($state, hrSettings, $rootScope, dashboardFactory, dependentsSettings, $timeout) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("employeeOverviewDirective Runnning !");

        scope.employees = {};
        scope.options = {};
        scope.page_ctr = 5;
        scope.page_active = 1;
        scope.emp_arr = [];
        scope.plan_status = {};
        scope.company_properties = {};
        scope.company_properties.total_allocation = 0.00;
        scope.company_properties.allocated = 0.00;
        scope.family_trap = false;
        scope.family_temp = null;
        scope.family_selected = null;

        scope.isAddDependentsShow = false;
        scope.isEmployeeShow = false;
        scope.job_list = [];
        scope.selectedEmployee_index = null;
        scope.selectedEmployee = {};
        scope.selectedDependent = {};
        scope.dependent_data = {};
        scope.isTierDetailsShow = false;
        scope.isMedicalUsageShow = false;
        scope.isWellnessUsageShow = false;
        scope.empTabSelected = 0;
        scope.nric_status = true;
        scope.fin_status = false;
        scope.addDependents_arr = [];
        scope.addActiveDependent_index = 0;
        scope.selected_customer_id = null;
        scope.nric_status_dependents = true;
        scope.fin_status_dependents = false;
        scope.refund_status = false;
        scope.remove_employee_data = {
          last_day_coverage : moment().add('days',1).format('DD/MM/YYYY')
        }
        scope.replace_emp_data = {};
        scope.reserve_emp_date = moment().add('days',1).format('DD/MM/YYYY');
        scope.update_member_wallet_status = null;

        scope.isRemoveEmployeeShow = false;
        scope.isRemoveEmployeeOptionsShow = false;
        scope.isHealthSpendingAccountSummaryShow = false;
        scope.isHealthSpendingAccountShow = false;
        scope.isReplaceEmpShow = false;
        scope.isReserveEmpShow = false;
        scope.isDeleteDependent = false;
        scope.dependents_ctr = 0;
        scope.cap_per_visit = 0;
        scope.isUpdateEmpInfoModalOpen = false;

        var iti = null;


        scope.$on("refresh", function(evt, data){
          scope.onLoad();
          scope.emp_arr = [];
        });

        scope.pagesToDisplay = 5;
        scope.startIndex = function(){
          if( scope.page_active > ((scope.pagesToDisplay / 2) + 1 )) {
            if ((scope.page_active + Math.floor(scope.pagesToDisplay / 2)) > scope.employees.last_page) {
              return scope.employees.last_page - scope.pagesToDisplay + 1;
            }
            return scope.page_active - Math.floor(scope.pagesToDisplay / 2);
          }    
          return 0;
        }


        scope.manageCap = function(){
          $("#manage-cap-modal").modal('show');
        }
        scope.submitCapPerVisit = function( cap ){
          scope.showLoading();
          var data = {
            employee_id : scope.selectedEmployee.user_id,
            cap_amount : cap,
          }
          hrSettings.updateCapPerVisit( data )
            .then(function(response){
              scope.hideLoading();
              if( response.data.status ){
                scope.cap_per_visit = 0;
                swal( 'Success!', response.data.message, 'success' );
                $("#manage-cap-modal").modal('hide');
              }else{
                swal( 'Error!', response.data.message, 'error' );
              }
            });
        }

        scope.gotToOverview = function(){
          scope.isAddDependentsShow = false;
          scope.isEmployeeShow = false;
          $('.prev-next-buttons-container').hide();
          $('.remove-employee-wrapper').hide();
          $('.add-dependent-wrapper').hide();
          $(".hrdb-body-container").fadeIn();
          $(".employee-information-wrapper").hide();
          $('body').scrollTop(0);
          scope.reset();
          $timeout(function() {
            $('body').css('overflow','hidden');
          }, 200);
        }

        scope.resendResetAccount = function(){
          swal({
            title: "Confirm!",
            text: "Are you sure you want to resend and reset the password for this account?",
            type: "info",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            confirmButtonColor: "#0392CF",
            closeOnConfirm: true,
            customClass: "updateEmp",
          },
          function(isConfirm){
            if(isConfirm){
              $('#input-pass').modal('show');
            }
          });
        }

        scope.confirmPassword = function( pass ){
          if( pass ){
            var data = {
              password : pass
            }
            hrSettings.sendPassword( data )
              .then(function(response){
                if( response.data.status ){
                  $('#input-pass').modal('hide');
                  scope.sendResetAccount( );
                  scope.passCredit = null;
                }else{
                  swal( 'Error!', response.data.message, 'error' );
                }
              });
          }else{
            swal( 'Error!', 'Please input password.', 'error' );
          }
        }

        scope.sendResetAccount = function( ){
          var data = {
            employee_id : scope.selectedEmployee.user_id
          }
          scope.showLoading();
          hrSettings.resetAccount( data )
            .then(function(response) {
              // console.log( response );
              if( response.data.status ){
                swal( 'Success!', response.data.message, 'success' );
              }else{
                swal( 'Error!', response.data.message, 'error' );
              }
              scope.hideLoading();
            });
        }

        scope.enrollMoreEmployees = function(){
          // localStorage.setItem('fromEmpOverview', false);
          // $state.go('create-team-benefits-tiers');
          localStorage.setItem('fromEmpOverview', true);
          $state.go('enrollment-options');
          $('body').css('overflow','auto');
        }

        scope.removeBtn = function(){
          $('.employee-information-wrapper').hide();
          $('.prev-next-buttons-container').fadeIn();
          $('.remove-employee-wrapper').fadeIn();
          scope.reset();
          scope.isRemoveEmployeeShow = true;
          scope.isDeleteDependent = false;
        }

        scope.removeDependentBtn = function( data ){
          // console.log( data );
          $('.employee-information-wrapper').hide();
          $('.prev-next-buttons-container').fadeIn();
          $('.remove-employee-wrapper').fadeIn();
          scope.reset();
          scope.isRemoveEmployeeShow = true;
          scope.isDeleteDependent = true;
          scope.selectedDependent = data;
        }

        scope.getUsage = function(x,y){

          return ( parseFloat(x) + parseFloat(y) );
        }

        scope.range = function (range) {
          var arr = []; 
          for (var i = 0; i < range; i++) {
              arr.push(i);
          }
          return arr;
        }

        scope.showFamily = function(emp, evt){
          scope.family_selected = emp.user_id;

          if ( emp.family_coverage.dependents.length > 0 || emp.family_coverage.spouse.spouse ) {
            if (scope.family_trap == false) {
              if (emp.family_coverage.spouse.spouse) {
                var duration = emp.family_coverage.spouse.spouse.duration.split( " " );
                emp.family_coverage.spouse.spouse.plan_start = moment( emp.family_coverage.spouse.spouse.plan_start ).format("MM/DD/YYYY");
                emp.family_coverage.spouse.spouse.plan_end = moment( emp.family_coverage.spouse.spouse.plan_start ).add(duration[0], duration[1]).format("MM/DD/YYYY");
              }

              for ( var i = emp.family_coverage.dependents.length - 1; i >= 0; i-- ) {
                var duration2 = emp.family_coverage.dependents[ i ].plan.duration.split(" ");
                emp.family_coverage.dependents[i].plan.plan_start = moment( emp.family_coverage.dependents[i].plan.plan_start ).format("MM/DD/YYYY");
                emp.family_coverage.dependents[i].plan.plan_end = moment( emp.family_coverage.dependents[i].plan.plan_start ).add(duration2[0], duration2[1]).format("MM/DD/YYYY");

                $(`<tr class="family-tr family-tr-` +
                    emp.family_coverage.dependents[i].dependent.UserID + `" style="background:#eee;">
                    <td class="for-checkbox-container"></td>
                    <td>
                      <p><a>` + emp.family_coverage.dependents[i].dependent.Name + `</a></p>
                    </td>
                    <td>
                      <p>---</p>
                    </td>
                    <td>
                      <p>Start Date: <span>` + emp.family_coverage.dependents[i].plan.plan_start + `</span></p>
                      <p>End Date: <span>` + emp.family_coverage.dependents[i].plan.plan_end + `</span></p>
                    </td>
                    <td>
                      <p>Dependent</p>
                    </td>
                    <td>
                      <p class="text-center">
                        S$<span style="width: 100px;text-align: right;margin-right: 10px;">` + emp.family_coverage.dependents[i].spent + `</span>
                        <span>Usage</span>
                      </p>
                    </td>
                  </tr>`).insertAfter($(evt.target).closest("tr"));
              }

              $(`<tr class="family-tr family-tr-` + emp.user_id + `" style="background:#eee;">
                  <td class="for-checkbox-container"></td>
                  <td>
                    <p><a>` + emp.family_coverage.spouse.spouse.Name + `</a></p>
                  </td>
                  <td>
                    <p>---</p>
                  </td>
                  <td>
                    <p>Start Date: <span>` + emp.family_coverage.spouse.spouse.plan_start + `</span></p>
                    <p>End Date: <span>` + emp.family_coverage.spouse.spouse.plan_end + `</span></p>
                  </td>
                  <td>
                    <p>Spouse</p>
                  </td>
                  <td>
                    <p class="text-center">
                      S$<span style="width: 100px;text-align: right;margin-right: 10px;">` + emp.family_coverage.spouse.spent + `</span>
                      <span>Usage</span>
                    </p>
                  </td>
                </tr>`).insertAfter($(evt.target).closest("tr"));

              scope.family_temp = emp.user_id;
              scope.family_trap = true;
            } else {
              $(".family-tr").remove();
              scope.family_trap = false;

              if (scope.family_temp != scope.family_selected) {
                scope.showFamily(emp, evt);
              }
            }
          } else {
            $(".family-tr").remove();
            scope.family_trap = false;
          }
        };

        scope.checkNRIC = function(theNric){
          var nric_pattern = new RegExp('^[stfgSTFG]{1}[0-9]{7}[a-zA-z]{1}$');
          return nric_pattern.test(theNric);
        };

        scope.checkEmail = function(email){
          var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
          return regex.test(email);
        }

        scope.checkDependentForm = function( data ){
          if( !data.fullname ){
            swal( 'Error!', 'Full Name is required.', 'error' );
            return false;
          }
          // if( !data.last_name ){
          //   swal( 'Error!', 'Last Name is required.', 'error' );
          //   return false;
          // }
          // if( !data.nric ){
          //   swal( 'Error!', 'NRIC is required.', 'error' );
          //   return false;
          // }else{
          //   if( scope.nric_status_dependents == true ){
          //     var checkNRIC = scope.checkNRIC(data.nric);
          //     if( checkNRIC != true ){
          //       swal( 'Error!', 'Invalid NRIC.', 'error' );
          //       return false;
          //     }
          //   } 
          // }
          if( !data.dob ){
            swal( 'Error!', 'Date of Birth is required.', 'error' );
            return false;
          }
          if( !data.relationship ){
            data.relationship = null;
            // swal( 'Error!', 'Relationship is required.', 'error' );
            // return false;
          }
          if( !data.start_date ){
            swal( 'Error!', 'Start Date is required.', 'error' );
            return false;
          }

          return true;
        }

        scope.checkUpdateEmployeeForm = function( data ){
          if( !data.name ){
            swal( 'Error!', 'Full Name is required.', 'error' );
            return false;
          }
          // if( !data.last_name ){
          //   swal( 'Error!', 'Last Name is required.', 'error' );
          //   return false;
          // }
          // if( !data.nric ){
          //   swal( 'Error!', 'NRIC is required.', 'error' );
          //   return false;
          // }else{
          //   if( scope.nric_status == true ){
          //     var checkNRIC = scope.checkNRIC(data.nric);
          //     if( checkNRIC != true ){
          //       swal( 'Error!', 'Invalid NRIC.', 'error' );
          //       return false;
          //     }
          //   } 
          // }
          if( !data.dob ){
            swal( 'Error!', 'Date of Birth is required.', 'error' );
            return false;
          }
          if( !data.email ){
            swal( 'Error!', 'Email is required.', 'error' );
            return false;
          }else{
            if( scope.checkEmail(data.email) == false ){
              swal( 'Error!', 'Email is invalid.', 'error' );
              return false;
            }
          }
          if( !data.phone_no ){
            swal( 'Error!', 'Mobile Number is required.', 'error' );
            return false;
          }else{
            // console.log( iti.getSelectedCountryData().iso2 );
            if( iti.getSelectedCountryData().iso2 == 'sg' && data.phone_no.length < 8 ){
              swal( 'Error!', 'Mobile Number for your country code should be 8 digits.', 'error' );
              return false;
            }
            if( iti.getSelectedCountryData().iso2 == 'my' && data.phone_no.length < 10 ){
              swal( 'Error!', 'Mobile Number for your country code should be 10 digits.', 'error' );
              return false;
            }
            if( iti.getSelectedCountryData().iso2 == 'ph' && data.phone_no.length < 9 ){
              swal( 'Error!', 'Mobile Number for your country code should be 9 digits.', 'error' );
              return false;
            }
          }
          // if( !data.postal_code ){
          //   swal( 'Error!', 'Postal Code is required.', 'error' );
          //   return false;
          // }

          return true;
        }

        scope.checkReplaceEmployeeForm = function( data ){
          if( !data.fullname ){
            swal( 'Error!', 'Full Name is required.', 'error' );
            return false;
          }
          // if( !data.last_name ){
          //   swal( 'Error!', 'Last Name is required.', 'error' );
          //   return false;
          // }
          // if( !data.nric ){
          //   swal( 'Error!', 'NRIC is required.', 'error' );
          //   return false;
          // }else{
          //   if( scope.nric_status == true ){
          //     var checkNRIC = scope.checkNRIC(data.nric);
          //     if( checkNRIC != true ){
          //       swal( 'Error!', 'Invalid NRIC.', 'error' );
          //       return false;
          //     }
          //   } 
          // }
          if( !data.dob ){
            swal( 'Error!', 'Date of Birth is required.', 'error' );
            return false;
          }
          if( !data.email ){
            // swal( 'Error!', 'Email is required.', 'error' );
            // return false;
          }else{
            if( scope.checkEmail(data.email) == false ){
              swal( 'Error!', 'Email is invalid.', 'error' );
              return false;
            }
          }
          if( !data.mobile ){
            swal( 'Error!', 'Mobile Number is required.', 'error' );
            return false;
          }else{
            // console.log( iti.getSelectedCountryData().iso2 );
            if( iti2.getSelectedCountryData().iso2 == 'sg' && data.mobile.length < 8 ){
              swal( 'Error!', 'Mobile Number for your country code should be 8 digits.', 'error' );
              return false;
            }
            if( iti2.getSelectedCountryData().iso2 == 'my' && data.mobile.length < 10 ){
              swal( 'Error!', 'Mobile Number for your country code should be 10 digits.', 'error' );
              return false;
            }
            if( iti2.getSelectedCountryData().iso2 == 'ph' && data.mobile.length < 9 ){
              swal( 'Error!', 'Mobile Number for your country code should be 9 digits.', 'error' );
              return false;
            }
          }
          // if( !data.postal_code ){
          //   swal( 'Error!', 'Postal Code is required.', 'error' );
          //   return false;
          // }
          if( !data.plan_start ){
            swal( 'Error!', 'Start Date is required.', 'error' );
            return false;
          }
          if( data.medical_credits > scope.credit_status.total_medical_employee_balance_number ){
            swal( 'Error!', 'We realised your Company Medical Spending Account has insufficient credits. Please contact our support team to increase the credit limit.', 'error' );
            return false;
          }
          if( data.wellness_credits > scope.credit_status.total_wellness_employee_balance_number ){
            swal( 'Error!', 'We realised your Company Wellness Spending Account has insufficient credits. Please contact our support team to increase the credit limit.', 'error' );
            return false;
          }

          return true;
        }

        scope.pushActiveDependent = function( data ){
          if( scope.checkDependentForm( data ) == true ){
            scope.showLoading();
            scope.hideLoading();
            data.done = true;
            scope.addDependents_arr.push(data);
            scope.dependents_ctr += 1;
            scope.addActiveDependent_index += 1;
            scope.dependent_data = {};
          }
        };

        scope.prevActiveDependent = function(){
          if( scope.dependents_ctr != 0 ){
            scope.dependents_ctr -= 1;
            scope.addActiveDependent_index -= 1;
            scope.dependent_data = scope.addDependents_arr[scope.dependents_ctr];
          }
        }

        scope.nextActiveDependent = function(){
          scope.dependents_ctr += 1;
          scope.addActiveDependent_index += 1;
          if( scope.addDependents_arr[ scope.dependent_ctr ] ){
            scope.dependent_data = scope.addDependents_arr[ scope.dependent_ctr ];
          }else{
            scope.dependent_data = {};
          }
          console.log( scope.addDependents_arr );
        }

        scope.perPage = function(num){
          scope.page_ctr = num;
          scope.page_active = 1;
          scope.getEmployeeList(scope.page_active);
        };

        scope.toggleAddDependents = function(){
          if( scope.isAddDependentsShow == false ){
            if(scope.selectedEmployee.plan_tier) {
              if( scope.selectedEmployee.plan_tier.dependent_enrolled_count == scope.selectedEmployee.plan_tier.dependent_head_count ){
                swal({
                  title: "Info",
                  text: "Number of dependents head count is already zero. Please contact mednefits for assistance.",
                  type: "info",
                  showCancelButton: false,
                  confirmButtonColor: "#0392CF",
                  closeOnConfirm: true,
                  customClass: "updateEmp"
                },
                function(isConfirm){
                  if(isConfirm){
                    
                  }
                });
              }else{
                $('.employee-information-wrapper').hide();
                $('.add-dependent-wrapper').fadeIn();
                scope.isAddDependentsShow = true;
              }
            } else {
              // $('.employee-information-wrapper').fadeIn();
              // $('.add-dependent-wrapper').hide();
              // scope.isAddDependentsShow = false; 
              if( scope.dependents.total_number_of_seats == scope.dependents.occupied_seats ){
                swal({
                  title: "Info",
                  text: "Number of dependents head count is already zero. Please contact mednefits for assistance.",
                  type: "info",
                  showCancelButton: false,
                  confirmButtonColor: "#0392CF",
                  closeOnConfirm: true,
                  customClass: "updateEmp"
                },
                function(isConfirm){
                  if(isConfirm){
                    
                  }
                });
              }else{
                $('.employee-information-wrapper').hide();
                $('.add-dependent-wrapper').fadeIn();
                scope.isAddDependentsShow = true;
              }
            }
          }else{
            $('.employee-information-wrapper').fadeIn();
            $('.add-dependent-wrapper').hide();
            scope.isAddDependentsShow = false;
          }
        };

        scope.toggleEmpTab = function(opt){

          scope.empTabSelected = opt;
          scope.healthSpendingAccountTabIsShow = false;
        };

        scope.togglePage = function(){

          $(".per_page").toggle();
        };

        scope.toggleTierDetails = function(){
          if( scope.isTierDetailsShow == false ){
            scope.isTierDetailsShow = true;
          }else{
            scope.isTierDetailsShow = false;
          }
        }

        scope.toggleMedicalUsage = function(){
          if( scope.isMedicalUsageShow == false ){
            scope.isMedicalUsageShow = true;
          }else{
            scope.isMedicalUsageShow = false;
          }
        }

        scope.toggleWellnessUsage = function(){
          if( scope.isWellnessUsageShow == false ){
            scope.isWellnessUsageShow = true;
          }else{
            scope.isWellnessUsageShow = false;
          }
        }

        scope.toggleEditEmployeeNRIC = function(data, opt){
          if (opt == "nric") {
            scope.nric_status = true;
            scope.fin_status = false;
          } else {
            scope.nric_status = false;
            scope.fin_status = true;
          }
          // scope.selectedEmployee.nric = "";
        };

        scope.openUpdateEmployeeModal = function(){
          scope.isUpdateEmpInfoModalOpen = true;
          $("#update-employee-modal").modal('show');
          scope.selectedEmployee.dob = moment( scope.selectedEmployee.dob ).format('DD/MM/YYYY');
          // scope.selectedEmployee.country_code = scope.selectedEmployee.country_code;
          $('.datepicker').datepicker('setDate', scope.selectedEmployee.dob );
          scope.inititalizeGeoCode();
          console.log( scope.selectedEmployee );
        }

        scope.openUpdateDependentModal = function(data){
          // console.log( data );
          scope.selectedDependent = data;
          scope.selectedDependent.dob = data.dob;
          $("#update-dependent-modal").modal('show');
          $('.datepicker').datepicker('setDate', scope.selectedDependent.dob );
        }

        scope.toggleEmployee = function(emp, index){
          console.log(emp);
          if( scope.isEmployeeShow == false ){
            scope.isEmployeeShow = true;
            scope.empTabSelected = 0;
            scope.healthSpendingAccountTabIsShow = false;
            scope.selectedEmployee_index = index;
            scope.selectedEmployee = emp;
            if( scope.selectedEmployee.plan_tier != null || scope.selectedEmployee.plan_tier ){
              scope.addActiveDependent_index = scope.selectedEmployee.plan_tier.dependent_enrolled_count + 1;
            }else{
              scope.addActiveDependent_index = scope.dependents.occupied_seats + 1;
            }
            // console.log( emp );
            scope.showLoading();
            scope.hideLoading();
            scope.fetchRefundStatus( emp.user_id );
            scope.getEmpDependents( emp.user_id );
            scope.getEmpPlans( emp.user_id );
            $('body').css('overflow','auto');
            $(".hrdb-body-container").hide();
            $(".employee-information-wrapper").fadeIn();
          }else{
            scope.selectedEmployee_index = null;
            scope.isEmployeeShow = false;
            $(".hrdb-body-container").fadeIn();
            $(".employee-information-wrapper").hide();
            $('body').scrollTop(0);
            $timeout(function() {
              $('body').css('overflow','hidden');
            }, 200);
          }
        }

        scope.getEmpPlans = function( id ) {
          dependentsSettings.fetchEmpPlans( id )
              .then(function(response){
                console.log( response );
                scope.selectedEmployee.plan_list = response.data;
              });
        }

        scope.prevSelectedEmployee = function(){
          scope.empTabSelected = 0;
          if( scope.selectedEmployee_index != 0 ){
            scope.showLoading();
            scope.hideLoading();
            scope.selectedEmployee_index--;
            scope.selectedEmployee = scope.employees.data[ scope.selectedEmployee_index ];
            scope.getEmpDependents( scope.selectedEmployee.user_id );
          }
        };

        scope.nextSelectedEmployee = function(){
          scope.empTabSelected = 0;
          if( scope.selectedEmployee_index != (scope.employees.data.length-1) ){
            scope.showLoading();
            scope.hideLoading();
            scope.selectedEmployee_index++;
            scope.selectedEmployee = scope.employees.data[ scope.selectedEmployee_index ];
            scope.getEmpDependents( scope.selectedEmployee.user_id );
            scope.getEmpPlans( scope.selectedEmployee.user_id );
          }
        };

        scope.nextPage = function(){
          if( scope.page_active < scope.employees.last_page ){
            scope.page_active++;
            scope.getEmployeeList(scope.page_active);
          }
        };

        scope.goToPage = function(page){
          scope.page_active = page;
          scope.getEmployeeList(scope.page_active);
        };

        scope.prevPage = function(){
          if( scope.page_active > 1 ){
            scope.page_active--;
            scope.getEmployeeList(scope.page_active);
          }
        };

        scope.removeSearchEmp = function(){
          scope.inputSearch = "";
          scope.page_active = 1;
          scope.getEmployeeList(1);
        }

        scope.searchEmployee = function(input){
          // console.log(input);
          if( input ){
            scope.showLoading();
            var data = {
              search: input
            };

            hrSettings.findEmployee(data)
              .then(function(response) {
                scope.employees = response.data;
                  angular.forEach(scope.employees.data, function(value, key) {
                  value.fname = scope.employees.data[ key ].name.substring( 0, value.name.lastIndexOf(" ") );
                  value.lname = scope.employees.data[ key ].name.substring( value.name.lastIndexOf(" ") + 1 );
                  value.start_date = moment( value.start_date ).format("DD/MM/YYYY");
                  value.start_date_format = moment( value.start_date, 'DD/MM/YYYY' ).format("DD MMMM YYYY");
                  value.end_date_format = moment( value.expiry_date ).format("DD MMMM YYYY");
                  // value.expiry_date = moment( value.expiry_date ).format("MM/DD/YYYY");
                });
                $(".employee-overview-pagination").hide();
                scope.hideLoading();
                scope.isSearchEmp = true;

                console.log( scope.selectedEmployee );
                if( scope.selectedEmployee_index != null ){
                  scope.selectedEmployee = scope.employees.data[ scope.selectedEmployee_index ];
                  if( scope.selectedEmployee.plan_tier != null || scope.selectedEmployee.plan_tier ){
                    scope.addActiveDependent_index = scope.selectedEmployee.plan_tier.dependent_enrolled_count + 1;
                  }else{
                    scope.addActiveDependent_index = scope.progress.completed + 1;
                  }
                  scope.fetchRefundStatus( scope.selectedEmployee.user_id );
                  scope.getEmpPlans( scope.selectedEmployee.user_id );
                  scope.getEmpDependents( scope.selectedEmployee.user_id );
                }
              });
          }else{
            scope.isSearchEmp = false;
            scope.removeSearchEmp();
          }
        };

        scope.changeRemoveOption = function( opt ){
          scope.remove_employee_data.replace = false;
          scope.remove_employee_data.reserve = false;
          scope.remove_employee_data.remove = false;

          if( opt == 1 ){
            scope.remove_employee_data.replace = true;
          }
          if( opt == 2 ){
            scope.remove_employee_data.reserve = true;
          }
          if( opt == 3 ){
            scope.remove_employee_data.remove = true;
          }
        }

        scope.changeMemberWalletUpdateStatus = function(opt){

          scope.update_member_wallet_status = opt;
        }

        scope.showLoading = function(){
          $(".circle-loader").fadeIn();
          loading_trap = true;
        };

        scope.hideLoading = function(){
          setTimeout(function() {
            $(".circle-loader").fadeOut();
            loading_trap = false;
          }, 1000);
        };

        scope.isCalculateBtnActive = false;

        scope.calculateHealthSpending = function(){
          var dates = {
            start : moment( scope.health_spending_summary.date.pro_rated_start, 'DD/MM/YYYY' ).format( 'YYYY-MM-DD' ),
            end : moment( scope.health_spending_summary.date.pro_rated_end, 'DD/MM/YYYY' ).format( 'YYYY-MM-DD' ),
          }
          scope.isCalculateBtnActive = true;
          scope.getSpendingAccountSummary( moment( scope.remove_employee_data.last_day_coverage,'DD/MM/YYYY' ).format('MM/DD/YYYY'), dates );
        }

        scope.initializeNewCustomDatePicker = function(){
          setTimeout(function() {
            $('.btn-custom-start').daterangepicker({
              autoUpdateInput : true,
              autoApply : true,
              singleDatePicker: true,
              startDate : moment( scope.health_spending_summary.date.pro_rated_start, 'DD/MM/YYYY' ).format( 'MM/DD/YYYY' ),
            }, function(start, end, label) {
              scope.health_spending_summary.date.pro_rated_start = moment( start ).format( 'DD/MM/YYYY' );
              $("#rangePicker_start").text( scope.health_spending_summary.date.pro_rated_start );
              $('.btn-custom-end').data('daterangepicker').setMinDate( start );

              // if( scope.rangePicker_end && ( moment(scope.rangePicker_end,'DD/MM/YYYY') < moment(scope.rangePicker_start,'DD/MM/YYYY') ) ){
              //   scope.rangePicker_end = moment( start ).format( 'DD/MM/YYYY' );
              //   $("#rangePicker_end").text( scope.rangePicker_end );
              // }
            });

            $('.btn-custom-end').daterangepicker({
              autoUpdateInput : true,
              autoApply : true,
              singleDatePicker: true,
              startDate : moment( scope.health_spending_summary.date.pro_rated_end, 'DD/MM/YYYY' ).format( 'MM/DD/YYYY' ),
            }, function(start, end, label) {
              scope.health_spending_summary.date.pro_rated_end = moment( end ).format( 'DD/MM/YYYY' );
              $("#rangePicker_end").text( scope.health_spending_summary.date.pro_rated_end );
            });

            var start = moment( scope.health_spending_summary.date.pro_rated_start, 'DD/MM/YYYY' ).format( 'DD/MM/YYYY' );
            var end = moment( scope.health_spending_summary.date.pro_rated_end, 'DD/MM/YYYY' ).format( 'DD/MM/YYYY' );
            $("#rangePicker_start").text( start );
            $("#rangePicker_end").text( end );
            $('.btn-custom-end').data('daterangepicker').setMinDate( start );
          }, 100);
        }

        scope.removeBackBtn = function(){
          if( scope.isRemoveEmployeeShow == true ){
            $('.employee-information-wrapper').fadeIn();
            $('.prev-next-buttons-container').hide();
            $('.remove-employee-wrapper').hide();
            scope.reset();
            scope.isEmployeeShow = true;
          }else if( scope.isRemoveEmployeeOptionsShow == true ){
            $('.remove-employee-wrapper').fadeIn();
            $('.employee-standalone-pro-wrapper').hide();
            scope.reset();
            scope.isRemoveEmployeeShow = true;
          }else if( scope.isReplaceEmpShow == true || scope.isReserveEmpShow == true ){
            $('.employee-standalone-pro-wrapper').fadeIn();
            $('.employee-replacement-wrapper').hide();
            $('.dependent-replacement-wrapper').hide();
            $('.hold-seat-wrapper').hide();
            scope.reset();
            scope.isRemoveEmployeeOptionsShow = true;
            iti2.destroy();
          }else if( scope.isHealthSpendingAccountSummaryShow == true ){
            $('.account-summary-wrapper').hide();
            $('.prev-next-buttons-container').hide();
            $(".employee-information-wrapper").fadeIn();
            scope.reset();
            scope.isCalculateBtnActive = false;
            scope.isEmployeeShow = true;
          }else if( scope.isHealthSpendingAccountShow == true ){
            $('.health-spending-account-wrapper').hide();
            $('.prev-next-buttons-container').hide();
            $(".employee-information-wrapper").fadeIn();
            scope.reset();
            scope.isCalculateBtnActive = false;
            scope.isEmployeeShow = true;
          }
        }

        scope.removeNextBtn = function(){
          if( scope.isRemoveEmployeeShow == true ){
            $('.employee-standalone-pro-wrapper').fadeIn();
            $('.remove-employee-wrapper').hide();
            scope.reset();
            scope.isRemoveEmployeeOptionsShow = true;
          }else if( scope.isRemoveEmployeeOptionsShow == true ){
            if( scope.remove_employee_data.remove != true ){
              $('.employee-standalone-pro-wrapper').hide();
              scope.reset();
              if( scope.remove_employee_data.replace == true ){
                if( scope.isDeleteDependent == true ){
                  $('.dependent-replacement-wrapper').fadeIn();
                }else{
                  $('.employee-replacement-wrapper').fadeIn();
                  scope.inititalizeGeoCode();
                }
                scope.isReplaceEmpShow = true;
                scope.replace_emp_data.plan_start = moment( scope.remove_employee_data.last_day_coverage,'DD/MM/YYYY' ).add(1,'days').format('DD/MM/YYYY');
              }
              if( scope.remove_employee_data.reserve == true ){
                // $('.hold-seat-wrapper').fadeIn();
                scope.isReserveEmpShow = true;
                if( scope.isDeleteDependent == true ){
                  scope.reserveDependent( );
                }else{
                  scope.getSpendingAccountSummary( moment( scope.remove_employee_data.last_day_coverage,'DD/MM/YYYY' ).format('MM/DD/YYYY') );
                  $('.employee-standalone-pro-wrapper').hide();
                  $(".account-summary-wrapper").fadeIn();
                  
                  scope.reset();
                  scope.isHealthSpendingAccountSummaryShow = true;
                  scope.getSession();
                }
              }
            }else{
              if( scope.isDeleteDependent == true ){
                scope.deleteDependent();
              }else{
                swal({
                  title: "Confirm",
                  text: "Are you sure you want to remove this employee completely?",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#ff6864",
                  confirmButtonText: "Remove",
                  cancelButtonText: "No",
                  closeOnConfirm: true,
                  customClass: "removeEmp"
                },
                function(isConfirm){
                  if(isConfirm){
                    scope.getSpendingAccountSummary( moment( scope.remove_employee_data.last_day_coverage,'DD/MM/YYYY' ).format('MM/DD/YYYY') );
                    $('.employee-standalone-pro-wrapper').hide();
                    $(".account-summary-wrapper").fadeIn();
                    
                    scope.reset();
                    scope.isHealthSpendingAccountSummaryShow = true;
                    scope.getSession();
                  }
                });
              }
              
            }
          }else if( scope.isReplaceEmpShow == true ){
            if( scope.isDeleteDependent == true ){
              scope.replaceDependent( scope.replace_emp_data );
            }else{
              // scope.replaceEmployee( scope.replace_emp_data );
              if( scope.checkReplaceEmployeeForm( scope.replace_emp_data ) == true ){
                // swal({
                //   title: "Confirm",
                //   text: "Are you sure you want to replace existing employee?",
                //   type: "warning",
                //   showCancelButton: true,
                //   confirmButtonColor: "#0392CF",
                //   confirmButtonText: "Replace",
                //   cancelButtonText: "No",
                //   closeOnConfirm: true,
                //   customClass: "updateEmp"
                // },
                // function(isConfirm){
                //   if(isConfirm){
                    scope.getSpendingAccountSummary( moment( scope.remove_employee_data.last_day_coverage,'DD/MM/YYYY' ).format('MM/DD/YYYY') );
                    $('.employee-replacement-wrapper').hide();
                    $(".account-summary-wrapper").fadeIn();
                    
                    scope.reset();
                    scope.isHealthSpendingAccountSummaryShow = true;
                    scope.getSession();
                //   }
                // });
              }
            }
          }else if( scope.isReserveEmpShow == true ){
            if( scope.isDeleteDependent == true ){
              scope.reserveDependent( );
            }else{
              // scope.reserveEmployee( );
              scope.getSpendingAccountSummary();
              $('.hold-seat-wrapper').hide();
              $(".account-summary-wrapper").fadeIn();

              scope.reset();
              scope.isHealthSpendingAccountSummaryShow = true;
              scope.getSession();
            }
          }else if( scope.isHealthSpendingAccountSummaryShow == true ){
            if( scope.isCalculateBtnActive == false ){
              swal( 'Error!', 'Please click the calcultate button first.', 'error' );
              return false;
            }else{
              $('.health-spending-account-wrapper').fadeIn();
              $('.account-summary-wrapper').hide();
              scope.reset();
              scope.isHealthSpendingAccountShow = true;
            }
          }
        }



        //----- HTTP REQUESTS -----//

        scope.confirmWalletUpdateBtn = function(){
          if( scope.update_member_wallet_status ){
            var dates = {
              start : moment( scope.health_spending_summary.date.pro_rated_start, 'DD/MM/YYYY' ).format( 'YYYY-MM-DD' ),
              end : moment( scope.health_spending_summary.date.pro_rated_end, 'DD/MM/YYYY' ).format( 'YYYY-MM-DD' ),
            }
            dependentsSettings.updateWalletMember( scope.selectedEmployee.user_id, scope.selected_customer_id, scope.health_spending_summary.medical.exceed, scope.health_spending_summary.wellness.exceed, moment( scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY' ).format('YYYY-MM-DD'), dates )
              .then(function(response){
                // console.log( response );
                if( response.data.status ){
                  // swal('Success!', response.data.message, 'success');
                  swal('Success!', "Member has successfully scheduled for remove and credits updated according.", 'success');
                  $('.health-spending-account-wrapper').hide();
                  $('.prev-next-buttons-container').hide();
                  $('.employee-information-wrapper').fadeIn();
                  scope.reset();
                  scope.isEmployeeShow = true;
                }else{
                  swal('Error!', response.data.message, 'error');
                }
              });
          }else{
            swal('Success!', "Member has successfully scheduled for remove.", 'success');
            $('.health-spending-account-wrapper').hide();
            $('.prev-next-buttons-container').hide();
            $('.employee-information-wrapper').fadeIn();
            scope.reset();
            scope.isEmployeeShow = true;
          }

          console.log( scope.remove_employee_data );
          if( scope.remove_employee_data.remove == true ){
            scope.deleteEmployee();
          }
          if( scope.remove_employee_data.reserve == true ){
            scope.reserveEmployee( );
          }
          if( scope.remove_employee_data.replace == true ){
            scope.replaceEmployee( scope.replace_emp_data );
          }
        }

        scope.getSpendingAccountSummary = function( last_date_of_coverage, dates ){
          scope.showLoading();
          dependentsSettings.fetchEmpAccountSummary( scope.selectedEmployee.user_id, scope.selected_customer_id, moment(last_date_of_coverage, 'MM/DD/YYYY').format('YYYY-MM-DD'), dates)
            .then(function(response){
              console.log( response );
              scope.health_spending_summary = response.data;
              scope.getTotalMembers();
              scope.initializeNewCustomDatePicker();
              scope.hideLoading();
              // if( scope.health_spending_summary.medical != false || scope.health_spending_summary.wellness != false ){
              //   if( scope.health_spending_summary.medical.exceed == true || scope.health_spending_summary.wellness.exceed == true ){
              //     $(".prev-next-buttons-container").fadeIn();
              //   }else{
              //     $(".prev-next-buttons-container").hide();
              //   }
              // }else{
                // scope.reset();
                // $('.account-summary-wrapper').hide();
                // $('.health-spending-account-wrapper').hide();
                // $('.prev-next-buttons-container').hide();
                // $(".employee-information-wrapper").hide();
                // $(".hrdb-body-container").fadeIn();
                // $('body').scrollTop(0);
                // $timeout(function() {
                //   $('body').css('overflow','hidden');
                // }, 200);
              // }
            });
        }

        scope.deleteDependent = function(){
          // console.log( data );
          swal({
            title: "Confirm",
            text: "Are you sure you want to remove this dependent?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ff6864",
            confirmButtonText: "Remove",
            cancelButtonText: "No",
            closeOnConfirm: true,
            customClass: "removeEmp"
          },
          function(isConfirm){
            if(isConfirm){
              scope.showLoading();
              var data = {
                expiry_date : moment( scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
                user_id : scope.selectedDependent.user_id
              }
              dependentsSettings.removeDependent(data)
                .then(function(response){
                  scope.hideLoading();
                  // console.log(response);
                  if( response.data.status ){
                    swal( 'Success!', response.data.message, 'success' );
                    $('.employee-standalone-pro-wrapper').hide();
                    $('.prev-next-buttons-container').hide();
                    $('.employee-information-wrapper').fadeIn();
                    scope.reset();
                    scope.isEmployeeShow = true;
                    scope.getSession();
                  }else{
                    $('.employee-standalone-pro-wrapper').fadeIn();
                    swal( 'Error!', response.data.message, 'error');
                  }
                });
            }
          });
        }

        scope.reserveDependent = function( ){
          var data = {
            user_id : scope.selectedDependent.user_id,
            date_enrollment : moment( scope.reserve_emp_date, 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            last_date_of_coverage : moment( scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            customer_id : scope.selected_customer_id,
          }
          scope.showLoading();
          dependentsSettings.reserveDependentService( data )
            .then(function(response){
              // console.log( response );
              scope.hideLoading();
              if( response.data.status ){
                swal( 'Success!', response.data.message, 'success' );
                $('.hold-seat-wrapper').hide();
                $('.prev-next-buttons-container').hide();
                $('.employee-information-wrapper').fadeIn();
                scope.reset();
                scope.isEmployeeShow = true;
                scope.getSession();
              }else{
                $('.employee-standalone-pro-wrapper').fadeIn();
                swal('Error!', response.data.message, 'error');
              }
            });          
        }

        scope.replaceDependent = function( data ){
          // console.log( data );
          if( scope.checkDependentForm( data ) == true ){
            // swal({
            //   title: "Confirm",
            //   text: "Are you sure you want to replace existing employee?",
            //   type: "warning",
            //   showCancelButton: true,
            //   confirmButtonColor: "#0392CF",
            //   confirmButtonText: "Replace",
            //   cancelButtonText: "No",
            //   closeOnConfirm: true,
            //   customClass: "updateEmp"
            // },
            // function(isConfirm){
            //   if(isConfirm){
                scope.showLoading();
                data.last_day_coverage = moment( scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY' ).format('YYYY-MM-DD');
                data.plan_start = moment( data.start_date, 'DD/MM/YYYY' ).format('YYYY-MM-DD');
                data.replace_id = scope.selectedDependent.user_id;
                data.customer_id = scope.selected_customer_id;
                dependentsSettings.replaceDependentService( data )
                  .then(function(response){
                    scope.hideLoading();
                    // console.log(response);
                    if( response.data.status ){
                      swal( 'Success!', response.data.message, 'success' );
                      $('.dependent-replacement-wrapper').hide();
                      $('.prev-next-buttons-container').hide();
                      $('.employee-information-wrapper').fadeIn();
                      scope.reset();
                      scope.isEmployeeShow = true;
                      scope.getSession();
                    }else{
                      $('.employee-standalone-pro-wrapper').fadeIn();
                      swal('Error!', response.data.message, 'error');
                    }
                  });
            //   }
            // });
          }
        }

        scope.reserveEmployee = function( ){
          var data = {
            employee_id : scope.selectedEmployee.user_id,
            // date_enrollment : moment( scope.reserve_emp_date, 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            last_date_of_coverage : moment( scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            customer_id : scope.selected_customer_id
          }
          scope.showLoading();
          dependentsSettings.reserveEmployee( data )
            .then(function(response){
              // console.log( response );
              scope.hideLoading();
              if( response.data.status ){
                // swal( 'Success!', response.data.message, 'success' );
                // scope.getSpendingAccountSummary( scope.remove_employee_data.last_day_coverage );
                // $('.hold-seat-wrapper').hide();
                // $(".account-summary-wrapper").fadeIn();
                // scope.reset();
                // scope.isHealthSpendingAccountSummaryShow = true;
                scope.getSession();
              }else{
                // swal('Error!', response.data.message, 'error');
              }
            });          
        }

        scope.getEmployeeList = function(page){
          $(".employee-overview-pagination").show();
          scope.showLoading();
          hrSettings.getEmployees(scope.page_ctr, page)
            .then(function(response) {
              // console.log(response);
              scope.employees = response.data;
              scope.employees.total_allocation = response.data.total_allocation;
              scope.employees.allocated = response.data.allocated;
              angular.forEach(scope.employees.data, function(value, key) {
                value.fname = scope.employees.data[ key ].name.substring( 0, value.name.lastIndexOf(" ") );
                value.lname = scope.employees.data[ key ].name.substring( value.name.lastIndexOf(" ") + 1 );
                value.start_date = moment( value.start_date ).format("DD/MM/YYYY");
                value.start_date_format = moment( value.start_date, 'DD/MM/YYYY' ).format("DD MMMM YYYY");
                value.end_date_format = moment( value.expiry_date ).format("DD MMMM YYYY");
                // value.expiry_date = moment( value.expiry_date ).format("MM/DD/YYYY");
              });
              $(".loader-table").hide();
              $(".main-table").fadeIn();
              scope.hideLoading();

              if( scope.selectedEmployee_index != null ){
                scope.selectedEmployee = scope.employees.data[ scope.selectedEmployee_index ];
                if( scope.selectedEmployee.plan_tier != null || scope.selectedEmployee.plan_tier ){
                  scope.addActiveDependent_index = scope.selectedEmployee.plan_tier.dependent_enrolled_count + 1;
                }else{
                  scope.addActiveDependent_index = scope.progress.completed + 1;
                }
                scope.fetchRefundStatus( scope.selectedEmployee.user_id );
                scope.getEmpPlans( scope.selectedEmployee.user_id );
                scope.getEmpDependents( scope.selectedEmployee.user_id );
              }
              
            });
        };

        scope.replaceEmployee = function( data ){
          scope.showLoading();
          data.dob = moment( scope.remove_employee_data.dob ).format('YYYY-MM-DD');
          data.last_day_coverage = moment( scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY' ).format('YYYY-MM-DD');
          data.replace_id = scope.selectedEmployee.user_id;
          data.plan_start = moment( data.plan_start, 'DD/MM/YYYY' ).format('YYYY-MM-DD');
          if(!data.medical_credits) {
            data.medical_credits = 0;
          }

          if(!data.wellness_credits) {
            data.wellness_credits = 0;
          }
          dependentsSettings.replaceEmployee( data )
            .then(function(response){
              scope.hideLoading();
              // console.log(response);
              if( response.data.status ){
                // swal( 'Success!', response.data.message, 'success' );
                // scope.getSpendingAccountSummary( scope.remove_employee_data.last_day_coverage );
                // $('.employee-replacement-wrapper').hide();
                // $(".account-summary-wrapper").fadeIn();
                // scope.reset();
                // scope.isHealthSpendingAccountSummaryShow = true;
                scope.getSession();
              }else{
                // swal('Error!', response.data.message, 'error');
              }
            });
              
        }

        scope.saveActiveDependents = function( ){
          // console.log( scope.addDependents_arr );
          console.log( scope.dependent_data );
          if( ( scope.dependent_data.fullname && scope.dependent_data.dob ) || scope.addDependents_arr.length == 0 ){
            if( scope.checkDependentForm( scope.dependent_data ) == true ){
              if( !scope.addDependents_arr[ scope.dependents_ctr ] ){
                scope.addActiveDependent_index+=1;
                scope.addDependents_arr.push( scope.dependent_data );
              }else{

              }
            }else{
              return false;
            }
          }

          console.log( scope.addDependents_arr );

          scope.showLoading();
          var data = {
            customer_id: scope.selected_customer_id,
            employee_id: scope.selectedEmployee.user_id,
            dependents: scope.addDependents_arr
          }
          dependentsSettings.addDependentForEmployee( data )
            .then(function(response){
              scope.hideLoading();
              // console.log(response);
              if( response.data.status ){
                swal('Success!', response.data.message, 'success');
                scope.addDependents_arr = [];
                scope.dependent_data = {};
                scope.dependents_ctr = 0;
                scope.getEmpDependents( scope.selectedEmployee.user_id );
                scope.toggleAddDependents();
                scope.getEmployeeList( scope.page_active );
              }else{
                swal('Error!', response.data.message, 'error');
              }
            });
        }

        scope.saveEmployee = function( data ){
          if( scope.checkUpdateEmployeeForm( data ) == false ){
            return false;
          }
          console.log( data );
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
          function(isConfirm){
            if(isConfirm){
              scope.showLoading();
              console.log( data );
              var update_data = {
                name: data.name,
                dob: data.dob,
                nric: data.nric,
                email: data.email,
                phone_no: data.phone_no,
                country_code: data.country_code.replace('+', ''),
                job_title: data.job_title,
                postal_code: data.postal_code,
                bank_account: data.bank_account,
                bank_code: data.bank_code,
                bank_branch: data.bank_branch,
                user_id: data.user_id,
              };
              console.log( update_data );
              dependentsSettings.updateEmployee( update_data )
                .then(function(response){
                  scope.hideLoading();
                  // console.log(response);
                  if( response.data.status ){
                    swal('Success!', response.data.message, 'success');
                    $("#update-employee-modal").modal('hide');
                    scope.getSession();
                  }else{
                    swal('Error!', response.data.message, 'error');
                  }
                });
            }
          });
        }

        scope.saveDependent = function( data ){ 
          var dob = moment( data.dob, 'DD/MM/YYYY' );
          var today = moment();
          console.log( dob.diff( today, 'days' ) );
          if( dob.diff( today, 'days' ) <= 0  ){
            
          }else{
            swal('Error!', 'Date of Birth is Invalid.', 'error');
            return false;
          }
          swal({
            title: "Confirm",
            text: "Are you sure you want to update this dependent?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0392CF",
            confirmButtonText: "Update",
            cancelButtonText: "No",
            closeOnConfirm: true,
            customClass: "updateEmp"
          },
          function(isConfirm){
            if(isConfirm){
              scope.showLoading();
              dependentsSettings.updateDependent( data )
                .then(function(response){
                  scope.hideLoading();
                  // console.log(response);
                  if( response.data.status ){
                    swal('Success!', response.data.message, 'success');
                    $("#update-dependent-modal").modal('hide');
                    scope.getSession();
                  }else{
                    swal('Error!', response.data.message, 'error');
                  }
                });
            }
          });
        }

        scope.deleteEmployee = function(){
          scope.showLoading();
          var users = [{
            expiry_date : moment( scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            user_id : scope.selectedEmployee.user_id
          }];
          dependentsSettings.removeEmployee( users )
            .then(function(response){
              scope.hideLoading();
              // console.log(response);
              if( response.data.status ){
                // swal( 'Success!', response.data.message, 'success' );
                // scope.getSpendingAccountSummary( scope.remove_employee_data.last_day_coverage );
                // $('.employee-standalone-pro-wrapper').hide();
                // $(".account-summary-wrapper").fadeIn();
                // scope.reset();
                // scope.isHealthSpendingAccountSummaryShow = true;
                scope.getSession();
              }else{
                // swal( 'Error!', response.data.message, 'error');
              }
            });
        }

        scope.getProgress = function(){
          hrSettings.getEnrollmentProgress()
          .then(function(response) {
            scope.hideLoading();
            // console.log( response );
            scope.progress = response.data.data;
          });
        };

        scope.companyDependents = function( ) {
          hrSettings.companyDependents( )
          .then(function(response){
            scope.dependents = response.data;
            scope.overall_dep_count = scope.dependents.occupied_seats + 1;
            // console.log(scope.dependents);
          });
        }

        scope.empDetailsLoadingState = function(){
          scope.showLoading();
          $( ".export-emp-details-message" ).show();
          hrSettings.getEployeeDetails()
            .then(function(response) {
              scope.allEmpData = response.data.data;
              scope.hideLoading();
              setTimeout(function() {
                $( ".export-emp-details-message" ).hide();
                $( "#empDetailsBtn" ).click();
              }, 1000);
            });
        }

        scope.checkCompanyBalance = function(){
          hrSettings.getCheckCredits()
            .then(function(response){
              console.log( response );
              scope.credit_status = response.data;
            });
        };

        scope.userCompanyCreditsAllocated = function(){
          hrSettings.userCompanyCreditsAllocated( )
          .then(function(response){
            scope.company_properties = response.data;
          });
        }

        scope.getPlanStatus = function( ) {
          hrSettings.getPlanStatus( )
          .then(function(response){
            // console.log(response);
            scope.plan_status = response.data;
          });
        }

        scope.getTotalMembers = function( ) {
          hrSettings.getCountMembers( )
            .then(function(response){
              // console.log(response);
              scope.member_count = response.data.total_members;
            });
        }

        scope.getEmpDependents = function( id ) {
          hrSettings.getDependents( id )
            .then(function(response){
              console.log(response);
              scope.selected_emp_dependents = response.data.dependents;
              angular.forEach( scope.selected_emp_dependents, function(value, key) {
                value.dob = moment( value.dob ).format('DD/MM/YYYY');
              });
            });
        }

        scope.checkDependentsStatus = function(){
          hrSettings.getMethodType()
           .then(function(response){
              // console.log(response);
              scope.dependents_status = response.data.data;
            });
        }

        scope.getJobs = function() {
          hrSettings.getJobTitle()
          .then(function(response) {
            // console.log( response );
            scope.job_list = response.data;
          });
        };

        scope.fetchRefundStatus = function(id) {
          hrSettings.getRefundStatus(id)
            .then(function(response) {
              // console.log( response );
              scope.refund_status = response.data.refund_status;
            });
        };

        scope.reset = function(){
          scope.isEmployeeShow = false;
          scope.isAddDependentsShow = false;
          scope.isRemoveEmployeeShow = false;
          scope.isRemoveEmployeeOptionsShow = false;
          scope.isHealthSpendingAccountSummaryShow = false;
          scope.isHealthSpendingAccountShow = false;
          scope.isReplaceEmpShow = false;
          scope.isReserveEmpShow = false;
          scope.isCalculateBtnActive = false;
        }

        scope.getSession = function(){
          hrSettings.getSession()
            .then(function(response) {
              // console.log( response );
              scope.selected_customer_id = response.data.customer_buy_start_id;
              scope.options.accessibility = response.data.accessibility;
              if( scope.isSearchEmp ){
                scope.searchEmployee(scope.inputSearch);
              }else{
                scope.getEmployeeList(scope.page_active);
              }
              scope.getTotalMembers();
              scope.getProgress();
            });
        }


        scope.healthSpendingAccountTabIsShow = false;
        scope.viewEmployeeSpendingSummary = function( ) {
          if( scope.healthSpendingAccountTabIsShow == false ){
            scope.getSpendingAccountSummary( scope.selectedEmployee.expiry_date );
            scope.empTabSelected = 99;
            scope.healthSpendingAccountTabIsShow = true;
            $('body').scrollTop(0);
          }else{
            scope.empTabSelected = 0;
            scope.healthSpendingAccountTabIsShow = false;
          }
        }

        scope.inititalizeGeoCode = function(){
          $timeout(function() {
            var input = document.querySelector("#area_code");
            var settings = {
              separateDialCode : true,
              initialCountry : "SG",
              autoPlaceholder : "off",
              utilsScript : "../assets/hr-dashboard/js/utils.js",
            };
            iti = intlTelInput(input, settings);
            iti.setNumber( scope.selectedEmployee.mobile_no );
            console.log( scope.selectedEmployee );
            if( scope.selectedEmployee.country_code == null ){
              scope.selectedEmployee.country_code = '65';
            }
            scope.selectedEmployee.phone_no = scope.selectedEmployee.phone_no;
            $("#area_code").val( scope.selectedEmployee.phone_no );
            input.addEventListener("countrychange", function() {
              console.log( iti.getSelectedCountryData() );
              scope.selectedEmployee.country_code = iti.getSelectedCountryData().dialCode;
              scope.selectedEmployee.mobile_area_code = iti.getSelectedCountryData().dialCode;
              scope.selectedEmployee.mobile_area_code_country = iti.getSelectedCountryData().iso2;
            });

            var input2 = document.querySelector("#area_code2");
            iti2 = intlTelInput(input2, settings);
            iti2.setCountry( "SG" );
            scope.replace_emp_data.country_code = '65';
            input2.addEventListener("countrychange", function() {
              console.log( iti2.getSelectedCountryData() );
              scope.replace_emp_data.country_code = iti2.getSelectedCountryData().dialCode;
              scope.replace_emp_data.mobile_area_code = iti2.getSelectedCountryData().dialCode;
              scope.replace_emp_data.mobile_area_code_country = iti2.getSelectedCountryData().iso2;
            });
          }, 300);
        }

        scope.onLoad = function(){
          scope.checkCompanyBalance();
          scope.getPlanStatus( );
          scope.userCompanyCreditsAllocated();
          scope.getTotalMembers();
          scope.checkDependentsStatus();
          scope.companyDependents();
          scope.getJobs();
          scope.showLoading();
          scope.getSession();
        };
        
        scope.onLoad();

        $('body').css('overflow','hidden');

        // ----------------

          $("body").click(function(e){
            if ( $(e.target).parents(".per-page-pagination").length === 0) {
              $(".per_page").hide();
            }
          });

          $("body").delegate( '.per_page li', 'click', function(e){

            $(".per_page").hide();
          });

          var dt = new Date();
          // dt.setFullYear(new Date().getFullYear()-18);
          $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            endDate : dt
          });

          $('.datepicker').datepicker().on('hide',function(evt){
            var val = $(this).val();
            if( val != "" ){
              $(this).datepicker('setDate', val);
            }
          })

          $('.start-date-datepicker-dependent').datepicker({
            format: 'dd/mm/yyyy',
          });

          $('.start-date-datepicker-dependent').datepicker().on('hide',function(evt){
            var val = $(this).val();
            if( val == "" ){
              $('.start-date-datepicker-dependent').datepicker('setDate', scope.selectedEmployee.start_date);
            }
          })

          $('.last-day-coverage-datepicker').datepicker({
            format: 'dd/mm/yyyy',
            
          });

          $('.last-day-coverage-datepicker').datepicker().on('hide',function(evt){
            var val = $(this).val();
            if( val == "" ){
              $('.last-day-coverage-datepicker').datepicker('setDate', moment( scope.remove_employee_data.last_day_coverage ).format('DD/MM/YYYY') );
            }
          })

          $('.start-date-datepicker-replace').datepicker({
            format: 'dd/mm/yyyy',
            
          });

          $('.start-date-datepicker-replace').datepicker().on('hide',function(evt){
            var val = $(this).val();
            if( val == "" ){
              $('.start-date-datepicker-replace').datepicker('setDate', scope.selectedEmployee.start_date );
            }
          })

          $('.future-datepicker').datepicker({
            format: 'dd/mm/yyyy',
            startDate: moment().format('DD/MM/YYYY')
          });

          $('.future-datepicker').datepicker().on('hide',function(evt){
            var val = $(this).val();
            if( val == "" ){
              $('.future-datepicker').datepicker('setDate', moment().format('DD/MM/YYYY') );
            }
          })

          $('.modal').on('hidden.bs.modal', function () {
            if( scope.isUpdateEmpInfoModalOpen == true ){
              iti.destroy();
            }
            scope.isUpdateEmpInfoModalOpen = false;
            // iti2.destroy();
            console.log( iti );
            console.log( iti2 );
          })

        // -------------- //

      }
    };
  }
]);
