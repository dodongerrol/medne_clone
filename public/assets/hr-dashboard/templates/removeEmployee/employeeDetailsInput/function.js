app.directive('employeeDetailsInputDirective', [
	'$state',
	'removeEmployeeFactory',
	function directive( $state, removeEmployeeFactory ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log( 'employeeDetailsInputDirective running!' );

				
				scope.backBtn	=	function(){
					// scope.isEmployeeShow = true;
					$state.go('employee-overview');
					// $('.employee-information-wrapper').fadeIn();
					scope.removeBackBtn();
				}
				scope.nextBtn	=	function(){
					scope.emp_details = scope.selectedEmployee;
					scope.showLoading();
					removeEmployeeFactory.setEmployeeDetails( scope.selectedEmployee );
					if( scope.selectedEmployee.account_type == 'enterprise_plan' ){
						if( scope.selectedEmployee.wellness_wallet == true ){
							$state.go('employee-overview.health-spending-account-summary');
						}else{
							$state.go('employee-overview.refund-summary');
						}
					}else if( scope.selectedEmployee.account_type == 'basic_plan' || scope.selectedEmployee.account_type == 'lite_plan' ){
						$state.go('employee-overview.health-spending-account-summary');
					}else if( scope.selectedEmployee.account_type == 'out_of_pocket' || scope.selectedEmployee.account_type == 'out_pocket' ){
						$("#remove-employee-confirm-modal").modal('show');
					}else{
						$state.go('employee-overview.remove-emp-checkboxes');
					}
				}

				setTimeout(() => {
					$('.last-day-coverage-datepicker').datepicker({
						format: 'dd/mm/yyyy',
					});
	
					$('.last-day-coverage-datepicker').datepicker().on('hide', function (evt) {
						var val = $(this).val();
						if (val == "") {
							$('.last-day-coverage-datepicker').datepicker('setDate', moment(scope.selectedEmployee.last_day_coverage).format('DD/MM/YYYY'));
						}
					})
				}, 500);

				scope.submitRemoveEmployee = function () {
          scope.showLoading();
          var users = [{
            expiry_date: moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            user_id: scope.emp_details.user_id,
            employee_id: scope.emp_details.user_id,
            last_date_of_coverage: moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            customer_id: scope.selected_customer_id,
            calibrate_medical: false,
            calibrate_wellness: false,
          }];
          dependentsSettings.removeEmployee(users)
            .then(function (response) {
              // scope.hideLoading();
              // console.log(response);
              if (response.data.status) {
                scope.hideLoading();
								scope.isRemoveSuccess = true;
              } else {
                scope.hideLoading();
                swal('Error!', response.data.message, 'error');
              }
            });
        }

				scope.closeConfirm = function(){
          $("#remove-employee-confirm-modal").modal('hide');
          $('.modal-backdrop').hide();
				}
				
				scope.doneConfirmModal	=	function(){
					$state.go('employee-overview');
          scope.resetRemoveBtn();
          scope.closeConfirm();
          $('.employee-information-wrapper').fadeIn();
				}

				scope.onLoad	=	function(){
					scope.hideLoading();
				}
				scope.onLoad();
			}
		}
	}
]);

