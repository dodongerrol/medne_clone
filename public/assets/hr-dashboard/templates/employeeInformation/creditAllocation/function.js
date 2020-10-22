app.directive('creditAllocationDirective', [
  '$http',
  'serverUrl',
  '$timeout',
  '$state',
  'employeeFactory',
  '$rootScope',
  'hrActivity',
  function directive($http, serverUrl, $timeout, $state, employeeFactory, $rootScope, hrActivity ) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log('creditAllocationDirective running!');
        scope.selected_member_id = localStorage.getItem('selected_member_id');
        scope.updateDisable = true;
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
        scope.medical_date = null;
        scope.updateDisable = true;
        
        

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
        
        scope.checkNewAllocation =  function (data) {
          if ( (data.medical_new_entitlement === '' || data.wellness_new_entitlement === '') &&
          (data.medical_new_entitlement === null || data.wellness_new_entitlement === null) ||
          (data.medical_new_entitlement === null && data.wellness_new_entitlement === null) )  {
            scope.updateDisable = true;
          } else {
            scope.updateDisable = false;
          }
        }

        scope.entitlement_credits = {
          med_credits : scope.emp_entitlement.medical_new_entitlement,
          well_credits : scope.emp_entitlement.wellness_new_entitlement
        }
    
        scope.updateEntitlement = function () {
          scope.effective_date = {
            med_date : moment( $('.medical-entitlement-date').val(), 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            well_date : moment( $('.wellness-entitlement-date').val(), 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
          }
          var text;
          if ((scope.emp_entitlement.medical_new_entitlement > 0 && scope.emp_entitlement.wellness_new_entitlement > 0)|| (scope.emp_entitlement.medical_new_entitlement === 0 && scope.emp_entitlement.wellness_new_entitlement === 0)) {
            console.log('1 if');
            text = `<span>Please note that</span> <br><br> <span>_ The new Medical Allocation of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.medical_new_entitlement}</span> will override the current amount of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.original_medical_entitlement}</span>.</span><br><span>_ The new Wellness Allocation of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.wellness_new_entitlement}</span> will override the current amount of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.original_wellness_entitlement}</span>.</span> <br><br> <span>Please confirm to proceed.</span>`;
          } else if ((scope.emp_entitlement.medical_new_entitlement > 0) || (scope.emp_entitlement.medical_new_entitlement === 0)) {
            console.log('2 if');
            text = `<span> Please note that the new Medical Allocation of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.medical_new_entitlement}</span> will override the current amount of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.original_medical_entitlement}</span>.</span> <br><br> <span>Please confirm to proceed.</span>`;
          } else if ((scope.emp_entitlement.wellness_new_entitlement > 0) || (scope.emp_entitlement.wellness_new_entitlement === 0)) {
            console.log('3 if');
            text = `<span>Please note that the new Wellness Allocation of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.wellness_new_entitlement}</span> will override the current amount of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.original_wellness_entitlement}</span>.</span> <br><br> <span>Please confirm to proceed.</span>`;
          }
          swal({
            title: '',
            text: text,
            html: true,
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            reverseButtons: true,
            customClass : 'allocationEntitlementModal'
          }, function(result) {
            console.log(result);
            setTimeout(function(){
              if(result) {
                if ((scope.emp_entitlement.medical_new_entitlement > 0 && scope.emp_entitlement.wellness_new_entitlement > 0) || (scope.emp_entitlement.medical_new_entitlement === 0 && scope.emp_entitlement.wellness_new_entitlement === 0)) {
                  console.log('both');
                  scope.updateAllEntitlement();
                } else if ((scope.emp_entitlement.medical_new_entitlement > 0) || (scope.emp_entitlement.medical_new_entitlement === 0)) {
                  console.log('medical');
                  scope.updateMedicalEntitlement();
                } else if ((scope.emp_entitlement.wellness_new_entitlement > 0) || (scope.emp_entitlement.wellness_new_entitlement === 0)) {
                  console.log('wellness');
                  scope.updateWellnessEntitlement();
                }
              }
            }, 500)
          })
        }

        scope.updateMedicalEntitlement = function () {
          scope.showLoading();
          var medical_data = {
            member_id:  scope.emp_member_id,
            new_allocation_credits: scope.emp_entitlement.medical_new_entitlement,
            effective_date: scope.effective_date.med_date,
            spending_type:  'medical',
          }
          hrActivity.updateEntitlement( medical_data ) 
            .then(function(response) {
              if (response.data.status) {
                scope.hideLoading();
                let text;
                if (medical_data.effective_date > moment().format('YYYY-MM-DD')) {
                  text  =  `<span>The allocation amount will be updated on ${moment(medical_data.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY')}.</span>`;
                  console.log(text);
                } else {
                  text  =  '<span>The allocation amount has been successfully updated.</span>';
                  console.log(text);
                }
                swal({
                  title: '',
                  text: text,
                  html: true,
                  showCancelButton: false,
                  confirmButtonText: 'Close',
                  customClass : 'allocationEntitlementSuccessModal'
                });
                scope.updateDisable = true;
                scope.getMemberEntitlement( scope.emp_member_id );
                scope.getMemberNewEntitlementStatus();
                $rootScope.$broadcast('updateEmployeeDetails');
              } else {
                scope.hideLoading();
                swal('Error!', response.data.message,'error');
              }
          });
        }

        scope.updateWellnessEntitlement = function () {
          scope.showLoading();
          var wellness_data = {
            member_id:  scope.emp_member_id,
            new_allocation_credits: scope.emp_entitlement.wellness_new_entitlement,
            effective_date: scope.effective_date.well_date,
            spending_type : 'wellness',
          }
          hrActivity.updateEntitlement( wellness_data ) 
            .then(function(response) {
              if (response.data.status) {
                scope.hideLoading();
                let text;
                if (wellness_data.effective_date > moment().format('YYYY-MM-DD')) {
                  text  =  `<span>The allocation amount will be updated on ${moment(wellness_data.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY')}.</span>`;
                  console.log(text);
                } else {
                  text  =  '<span>The allocation amount has been successfully updated.</span>';
                  console.log(text);
                }
                swal({
                  title: '',
                  text: text,
                  html: true,
                  showCancelButton: false,
                  confirmButtonText: 'Close',
                  customClass : 'allocationEntitlementSuccessModal'
                });
                scope.updateDisable = true;
                scope.getMemberEntitlement( scope.emp_member_id );
                scope.getMemberNewEntitlementStatus();
                $rootScope.$broadcast('updateEmployeeDetails');
              } else {
                scope.hideLoading();
                swal('Error!', response.data.message,'error');
                console.log( response.data.message );
              }
          });
        }

        scope.updateAllEntitlement = function () {
          var medical_data = {
            member_id:  scope.emp_member_id,
            new_allocation_credits: scope.emp_entitlement.medical_new_entitlement,
            effective_date: scope.effective_date.med_date,
            spending_type:  'medical',
          }
          var wellness_data = {
            member_id:  scope.emp_member_id,
            new_allocation_credits: scope.emp_entitlement.wellness_new_entitlement,
            effective_date: scope.effective_date.well_date,
            spending_type : 'wellness',
          }
          scope.showLoading();
          hrActivity.updateEntitlement( medical_data )
            .then(function(response1){
              if (response1.data.status) {
                hrActivity.updateEntitlement( wellness_data )
                  .then(function(response2){
                    scope.hideLoading();
                    if (response2.data.status) {
                      scope.hideLoading();
                      let text;
                      if (medical_data.effective_date > moment().format('YYYY-MM-DD') && wellness_data.effective_date > moment().format('YYYY-MM-DD')) {
                        text  =  `<span>The allocation amount will be updated on ${moment(medical_data.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY')} for medical and ${moment(wellness_data.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY')} for wellness.</span>`;
                        console.log(text);
                      } else if (medical_data.effective_date > moment().format('YYYY-MM-DD') && wellness_data.effective_date <= moment().format('YYYY-MM-DD')) {
                        text  =  `<span>The allocation amount has been successfully updated and for wellness will be updated on ${moment(wellness_data.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY')}.</span>`;
                        console.log(text);
                      } else if (wellness_data.effective_date > moment().format('YYYY-MM-DD') && medical_data.effective_date <= moment().format('YYYY-MM-DD')) {
                        text  =  `<span>The allocation amount has been successfully updated and for medical will be updated on ${moment(medical_data.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY')}.</span>`;
                        console.log(text);
                      } else {
                        text = '<span>The allocation amount has been successfully updated.</span>';
                      }
                      swal({
                        title: '',
                        text: text,
                        html: true,
                        showCancelButton: false,
                        confirmButtonText: 'Close',
                        customClass : 'allocationEntitlementSuccessModal'
                      });
                      scope.updateDisable = true;
                      scope.getMemberEntitlement( scope.emp_member_id );
                      scope.getMemberNewEntitlementStatus();
                      $rootScope.$broadcast('updateEmployeeDetails');
                    } else {
                      scope.hideLoading();
                      swal('Error!', response2.data.message,'error');
                    }
                });  
              } else {
                scope.hideLoading();
                swal('Error!', response1.data.message,'error');
              }
          });
        }

        scope.initializeDatepickers  = function(){
          $('.datepicker-medical').datepicker({
            format: 'dd/mm/yyyy',
          });

          $('.datepicker-wellness').datepicker({
            format: 'dd/mm/yyyy',
          });
        }

        scope.getMemberEntitlement = function ( emp ) {
          scope.emp_member_id = emp;
          hrActivity.fetchMemberEntitlement( scope.emp_member_id ) 
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
        
        scope.getMemberNewEntitlementStatus = function ( emp ) {
          hrActivity.fetchMemberNewEntitlementStatus( scope.emp_member_id ) 
            .then(function(response) {
              scope.entitlement_status = response.data;
              if ( scope.entitlement_status.medical_entitlement != null && scope.entitlement_status.wellness_entitlement != null ) {
                scope.entitlement_status.medical_entitlement.effective_date = moment( scope.entitlement_status.medical_entitlement.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
                scope.entitlement_status.wellness_entitlement.effective_date = moment( scope.entitlement_status.wellness_entitlement.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
              }
            });
        }

        scope.entitlementCalc = function ( type, cal ) {
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
            hrActivity.openEntitlementCalc( scope.emp_member_id, scope.entitlement_credits.med_credits, scope.effective_date.med_date, scope.proration.med_proration, scope.entitlement_spending_type) 
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
            hrActivity.openEntitlementCalc( scope.emp_member_id, scope.entitlement_credits.well_credits, scope.effective_date.well_date, scope.proration.well_proration, scope.entitlement_spending_type) 
              .then(function(response) {
                console.log(response);
                console.log( scope.entitlement_credits.well_credits );
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
          await scope.getEmployeeDetails();
          await scope.getMemberEntitlement(scope.selected_member_id);
          await scope.getMemberNewEntitlementStatus(scope.selected_member_id);
          await scope.entitlementCalc(scope.selected_member_id);
        }
        scope.onLoad();
      }
    }
  }
]);