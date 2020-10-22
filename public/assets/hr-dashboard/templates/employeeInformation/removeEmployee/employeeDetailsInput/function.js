app.directive('employeeDetailsInputDirective', [
	'$http',
	'serverUrl',
	'$state',
	'removeEmployeeFactory',
	'dependentsSettings',
	'employeeFactory',
	'$timeout',
	function directive( $http, serverUrl, $state, removeEmployeeFactory, dependentsSettings, employeeFactory, $timeout ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log( 'employeeDetailsInputDirective running!' );
				scope.selected_member_id = localStorage.getItem('selected_member_id');



				scope.getEmployeeDetails  = async function(isRefresh){
          scope.selectedEmployee = await employeeFactory.getEmployeeDetails();
          if( scope.selectedEmployee == null || scope.selectedEmployee.user_id != Number(scope.selected_member_id) || isRefresh ){
            await scope.fetchEmployeeDetails();
          }else{
						// scope.hideLoading();
						scope.selectedEmployee.last_day_coverage = moment().format('DD/MM/YYYY');
						scope.emp_details = scope.selectedEmployee;
          }
        }
        scope.fetchEmployeeDetails  = async function(){
          scope.showLoading();
          await $http.get(serverUrl.url + "/hr/employee/" + scope.selected_member_id)
            .then(function(response){
              console.log(response);
							scope.selectedEmployee = response.data.data;
							scope.selectedEmployee.last_day_coverage = moment().format('DD/MM/YYYY');
							scope.emp_details = scope.selectedEmployee;
              employeeFactory.setEmployeeDetails(scope.selectedEmployee);
              // scope.hideLoading();
            });
        }
				
				scope.backBtn	=	function(){
					$state.go('member.emp-details', { member_id : scope.selected_member_id });
				}
				scope.nextBtn	=	function(){
					scope.emp_details = scope.selectedEmployee;
					removeEmployeeFactory.setEmployeeDetails( scope.selectedEmployee );
					if( scope.selectedEmployee.account_type == 'enterprise_plan' ){
						if( scope.selectedEmployee.wellness_wallet == true ){
							scope.showLoading();
							$state.go('member-remove.health-spending-account-summary');
						}else{
							scope.showLoading();
							$state.go('member-remove.refund-summary');
						}
					}else if( scope.selectedEmployee.account_type == 'basic_plan' || scope.selectedEmployee.account_type == 'lite_plan' ){
						scope.showLoading();
						$state.go('member-remove.health-spending-account-summary');
					}else if( scope.selectedEmployee.account_type == 'out_of_pocket' || scope.selectedEmployee.account_type == 'out_pocket' ){
						$("#remove-employee-confirm-modal").modal('show');
					}else{
						scope.showLoading();
						$state.go('member-remove.remove-emp-checkboxes');
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
				scope.showLoading = function () {
          $(".circle-loader").fadeIn();
        };
        scope.hideLoading = function () {
          $timeout(function () {
            $(".circle-loader").fadeOut();
          }, 10);
        };

				scope.onLoad	= async	function(){
					scope.showLoading();
          await scope.getEmployeeDetails();
					scope.hideLoading();
				}
				scope.onLoad();
			}
		}
	}
]);

