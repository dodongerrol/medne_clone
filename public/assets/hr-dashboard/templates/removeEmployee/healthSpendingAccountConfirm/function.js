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
        scope.emp_details.return_credits_date = moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').add(1,'days').format('DD/MM/YYYY');
        scope.remove_emp_details = removeEmployeeFactory.getReplaceEmployeeDetails();
        scope.emp_details.wallet_opt = null;
        scope.isRemoveSuccess = false;
        console.log(scope.emp_details);
        console.log(scope.remove_emp_details);

        scope.closeConfirm = function(){
          $("#remove-employee-confirm-modal").modal('hide');
          $('.modal-backdrop').hide();
        }
        scope.selectOption  = function(opt){
          scope.emp_details.wallet_opt = opt;
        }
				scope.backBtn	=	function(){
					$state.go('employee-overview.health-spending-account-summary');
				}
				scope.nextBtn	=	function(evt){
					if(scope.emp_details.wallet_opt == null){
            swal('Error!', 'Please select an option first.', 'error');
          }else{
            // if(scope.emp_details.account_type == 'lite_plan' && (scope.emp_details.summary.medical.plan_method == 'pre_paid' || scope.emp_details.summary.wellness.plan_method == 'pre_paid') ){
            if(scope.emp_details.account_type == 'lite_plan' ){
              $("#remove-employee-confirm-modal").modal('show');
            }else if(scope.emp_details.account_type == 'enterprise_plan'){
              $state.go('employee-overview.refund-summary');
            }else{
              // scope.confirmRemoveEmployee();
              scope.removeEmployeeRequests();
            }
          }
        }
        scope.confirmRemoveEmployee = function(){
          var dates = {
            start: moment(scope.emp_details.summary.date.pro_rated_start, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            end: moment(scope.emp_details.summary.date.pro_rated_end, 'DD/MM/YYYY').format('YYYY-MM-DD'),
          }
          scope.showLoading();
          dependentsSettings.updateWalletMember(scope.emp_details.user_id, scope.selected_customer_id, scope.emp_details.summary.medical.exceed, scope.emp_details.summary.wellness.exceed, moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'), dates, scope.emp_details.wallet_opt)
            .then(function (response) {
              console.log(response);
              scope.hideLoading();
              if (response.data.status) {
                // scope.removeEmployeeRequests();
                if(scope.emp_details.account_type == 'lite_plan' && (scope.emp_details.summary.medical.plan_method == 'pre_paid' || scope.emp_details.summary.wellness.plan_method == 'pre_paid') ){
                  scope.isRemoveSuccess = true;
                }else{
                  swal({
                    title: "Success!",
                    // text: response.data.message,
                    text: "The employee has been successfully removed",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#0392CF",
                    closeOnConfirm: true,
                  },
                  function (isConfirm) {
                    if (isConfirm) {
                      scope.resetEmpData();
                    }
                  });
                }
              } else {
                swal('Error!', response.data.message, 'error');
              }
            });
        }
				scope.removeEmployeeRequests  = function(){
          if (scope.emp_details.remove_option == 'remove' || !scope.emp_details.remove_option) {
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
            customer_id: scope.selected_customer_id,
            calibrate_medical: scope.emp_details.wallet_opt,
            calibrate_wellness: scope.emp_details.wallet_opt,
          }
          if( scope.emp_details.account_type == 'enterprise_plan' ){
            data.calibrate_medical = false;
          }
          scope.showLoading();
          dependentsSettings.reserveEmployee(data)
            .then(function (response) {
              // console.log( response );
              // scope.hideLoading();
              if (response.data.status) {
                scope.hideLoading();
                if(scope.emp_details.account_type == 'lite_plan' && (scope.emp_details.summary.medical.plan_method == 'pre_paid' || scope.emp_details.summary.wellness.plan_method == 'pre_paid') ){
                  scope.isRemoveSuccess = true;
                }else{
                  swal({
                    title: "Success!",
                    // text: response.data.message,
                    text: "The employee has been successfully removed",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#0392CF",
                    closeOnConfirm: true,
                  },
                  function (isConfirm) {
                    if (isConfirm) {
                      scope.resetEmpData();
                    }
                  });
                }
              } else {
                scope.hideLoading();
                swal('Error!', response.data.message, 'error');
              }
            });
        }
        scope.submitReplaceEmployee = function (data) {
          scope.showLoading();
          scope.remove_emp_details.dob = moment(scope.remove_emp_details.dob, 'DD/MM/YYYY').format('YYYY-MM-DD');
          scope.remove_emp_details.employee_id = scope.emp_details.user_id;
          scope.remove_emp_details.customer_id = scope.selected_customer_id;
          scope.remove_emp_details.last_day_coverage = moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD');
          scope.remove_emp_details.last_date_of_coverage = moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD');
          scope.remove_emp_details.calibrate_medical = scope.emp_details.wallet_opt;
          scope.remove_emp_details.calibrate_wellness = scope.emp_details.wallet_opt;
          scope.remove_emp_details.replace_id = scope.emp_details.user_id;
          scope.remove_emp_details.plan_start = moment(scope.remove_emp_details.plan_start, 'DD/MM/YYYY').format('YYYY-MM-DD');
          if (!scope.remove_emp_details.medical_credits) {
            scope.remove_emp_details.medical_credits = 0;
          }
          if (!scope.remove_emp_details.wellness_credits) {
            scope.remove_emp_details.wellness_credits = 0;
          }
          if( scope.emp_details.account_type == 'enterprise_plan' ){
            scope.remove_emp_details.calibrate_medical = false;
          }
          dependentsSettings.replaceEmployee(scope.remove_emp_details)
            .then(function (response) {
              // scope.hideLoading();
              // console.log(response);
              if (response.data.status) {
                scope.hideLoading();
                if(scope.emp_details.account_type == 'lite_plan' && (scope.emp_details.summary.medical.plan_method == 'pre_paid' || scope.emp_details.summary.wellness.plan_method == 'pre_paid') ){
                  scope.isRemoveSuccess = true;
                }else{
                  swal({
                    title: "Success!",
                    // text: response.data.message,
                    text: "The employee has been successfully removed",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#0392CF",
                    closeOnConfirm: true,
                  },
                  function (isConfirm) {
                    if (isConfirm) {
                      scope.resetEmpData();
                    }
                  });
                }
              } else {
                scope.hideLoading();
                swal('Error!', response.data.message, 'error');
              }
            });

        }
        scope.submitRemoveEmployee = function () {
          scope.showLoading();
          var users = [{
            expiry_date: moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            user_id: scope.emp_details.user_id,
            employee_id: scope.emp_details.user_id,
            last_date_of_coverage: moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            customer_id: scope.selected_customer_id,
            calibrate_medical: scope.emp_details.account_type == 'enterprise_plan' ? false : scope.emp_details.wallet_opt,
            calibrate_wellness: scope.emp_details.wallet_opt,
          }];
          dependentsSettings.removeEmployee(users)
            .then(function (response) {
              // scope.hideLoading();
              // console.log(response);
              if (response.data.status) {
                scope.hideLoading();
                if(scope.emp_details.account_type == 'lite_plan' && (scope.emp_details.summary.medical.plan_method == 'pre_paid' || scope.emp_details.summary.wellness.plan_method == 'pre_paid') ){
                  scope.isRemoveSuccess = true;
                }else{
                  swal({
                    title: "Success!",
                    // text: response.data.message,
                    text: "The employee has been successfully removed",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#0392CF",
                    closeOnConfirm: true,
                  },
                  function (isConfirm) {
                    if (isConfirm) {
                      scope.resetEmpData();
                    }
                  });
                }
              } else {
                scope.hideLoading();
                swal('Error!', response.data.message, 'error');
              }
            });
        }

        scope.resetEmpData  = function(){
          $state.go('employee-overview');
          scope.resetRemoveBtn();
          scope.closeConfirm();
          $('.employee-information-wrapper').fadeIn();
        }
        scope.doneConfirmModal  = function(){
          
          scope.resetEmpData();
        }
        scope.onLoad	=	function(){
					scope.hideLoading();
				}
				scope.onLoad();
			}
		}
	}
]);