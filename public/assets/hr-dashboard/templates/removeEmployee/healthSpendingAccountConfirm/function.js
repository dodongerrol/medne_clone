app.directive('healthSpendingAccountConfirmDirective', [
	'$state',
	'removeEmployeeFactory',
	'dependentsSettings',
	function directive( $state, removeEmployeeFactory, dependentsSettings ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log( 'healthSpendingAccountConfirmDirective running!' );
        scope.emp_details = removeEmployeeFactory.getEmployeeDetails();
        scope.remove_emp_details = removeEmployeeFactory.getReplaceEmployeeDetails();
        scope.emp_details.wallet_opt = null;
        scope.isRemoveSuccess = false;
        console.log(scope.emp_details);
        console.log(scope.spendingPlan_status);

        scope.closeConfirm = function(){
          $("#remove-employee-confirm-modal").modal('hide');
        }
        scope.selectOption  = function(opt){
          scope.emp_details.wallet_opt = opt;
        }
				scope.backBtn	=	function(){
					$state.go('corporates.health-spending-account-summary');
				}
				scope.nextBtn	=	function(evt){
					if(scope.emp_details.wallet_opt == null){
            swal('Error!', 'Please select an option first.', 'error');
          }else{
            if(scope.emp_details.account_type == 'lite_plan' && scope.emp_details.plan_method_type == 'pre_paid'){
              $("#remove-employee-confirm-modal").modal('show');
            }else{
              scope.confirmRemoveEmployee();
            }
          }
        }
        scope.confirmRemoveEmployee = function(){
          var dates = {
            start: moment(scope.emp_details.summary.date.pro_rated_start, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            end: moment(scope.emp_details.summary.date.pro_rated_end, 'DD/MM/YYYY').format('YYYY-MM-DD'),
          }
          dependentsSettings.updateWalletMember(scope.emp_details.user_id, scope.selected_customer_id, scope.emp_details.summary.medical.exceed, scope.emp_details.summary.wellness.exceed, moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'), dates, scope.emp_details.wallet_opt)
            .then(function (response) {
              if (response.data.status) {
                scope.removeEmployeeRequests();
              } else {
                swal('Error!', response.data.message, 'error');
              }
            });
        }
				scope.removeEmployeeRequests  = function(){
          if (scope.emp_details.remove_option == 'remove') {
            scope.submitRemoveEmployee();
          }
          if (scope.emp_details.remove_option == 'reserve') {
            scope.submitReserveEmployee();
          }
          if (scope.emp_details.remove_option == 'replace') {
            scope.submitReplaceEmployee(scope.replace_employee_data);
          }
        }
        scope.submitReserveEmployee = function () {
          var data = {
            employee_id: scope.emp_details.user_id,
            last_date_of_coverage: moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            customer_id: scope.selected_customer_id
          }
          scope.showLoading();
          dependentsSettings.reserveEmployee(data)
            .then(function (response) {
              // console.log( response );
              scope.hideLoading();
              if (response.data.status) {
                scope.isRemoveSuccess = true;
                swal('Success!', response.data.message, 'success');
              } else {
                swal('Error!', response.data.message, 'error');
              }
            });
        }
        scope.submitReplaceEmployee = function (data) {
          scope.showLoading();
          scope.remove_emp_details.dob = moment(scope.remove_emp_details.dob).format('YYYY-MM-DD');
          scope.emp_details.last_day_coverage = moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD');
          scope.remove_emp_details.replace_id = scope.emp_details.user_id;
          scope.remove_emp_details.plan_start = moment(scope.remove_emp_details.plan_start, 'DD/MM/YYYY').format('YYYY-MM-DD');
          if (!scope.remove_emp_details.medical_credits) {
            scope.remove_emp_details.medical_credits = 0;
          }
          if (!scope.remove_emp_details.wellness_credits) {
            scope.remove_emp_details.wellness_credits = 0;
          }
          dependentsSettings.replaceEmployee(scope.remove_emp_details)
            .then(function (response) {
              scope.hideLoading();
              // console.log(response);
              if (response.data.status) {
                scope.isRemoveSuccess = true;
                swal('Success!', response.data.message, 'success');
              } else {
                swal('Error!', response.data.message, 'error');
              }
            });

        }
        scope.submitRemoveEmployee = function () {
          scope.showLoading();
          var users = [{
            expiry_date: moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            user_id: scope.emp_details.user_id
          }];
          dependentsSettings.removeEmployee(users)
            .then(function (response) {
              scope.hideLoading();
              // console.log(response);
              if (response.data.status) {
                scope.isRemoveSuccess = true;
                swal('Success!', response.data.message, 'success');
              } else {
                swal('Error!', response.data.message, 'error');
              }
            });
        }

        scope.resetEmpData  = function(){
          scope.getSession();
        }
        scope.doneConfirmModal  = function(){
          scope.closeConfirm();
          scope.resetEmpData();
        }
			}
		}
	}
]);