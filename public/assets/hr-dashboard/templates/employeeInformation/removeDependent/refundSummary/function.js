app.directive('refundSummaryDirective', [
	'$state',
	'removeEmployeeFactory',
	'hrSettings',
	'dependentsSettings',
	'$timeout',
	function directive( $state, removeEmployeeFactory, hrSettings, dependentsSettings, $timeout ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log( 'refundSummaryDirective running!' );
				scope.emp_details = removeEmployeeFactory.getEmployeeDetails();
				scope.member_refund_details = {};
				console.log(scope.emp_details);
				scope.isRemoveSuccess = false;


				scope.closeConfirm = function(){
          $("#remove-employee-confirm-modal").modal('hide');
          $('.modal-backdrop').hide();
        }
				scope.getRefundSummary = function () {
					var data = {
						member_id : scope.emp_details.user_id,
						refund_date: moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY/MM/DD'),
					}
					scope.showLoading();
					hrSettings.get_member_refund(data)
					.then(function (response) {
						console.log('refund ni',response);
						scope.member_refund_details = response.data.data;
						scope.member_refund_details.unutilised_start_date = moment(scope.member_refund_details.unutilised_start_date).format('DD/MM/YYYY');
						scope.member_refund_details.unutilised_end_date = moment(scope.member_refund_details.unutilised_end_date).format('DD/MM/YYYY');
						scope.hideLoading();
					});
				}

				scope.backBtn	=	function(){
					if( scope.emp_details.wellness_wallet == true ){
						$state.go('employee-overview.health-spending-account-confirm');
					}else{
						$state.go('employee-overview.remove-emp-inputs');
					}
					
				}
				scope.nextBtn	=	function(){
					$('#remove-employee-confirm-modal').modal('show');
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
								scope.isRemoveSuccess = true;
              } else {
                scope.hideLoading();
                swal('Error!', response.data.message, 'error');
              }
            });
        }

				scope.resetEmpData  = function(){
          // scope.resetRemoveBtn();
          scope.closeConfirm();
					// $('.employee-information-wrapper').fadeIn();
					$state.go('employee-overview');
        }
        scope.doneConfirmModal  = function(){
          
          scope.resetEmpData();
				}
				
				scope.showLoading = function () {
          $(".circle-loader").fadeIn();
        };
        scope.hideLoading = function () {
          $timeout(function () {
            $(".circle-loader").fadeOut();
          }, 10);
        };
				
				scope.onLoad	=	function(){
					scope.getRefundSummary();
				}
				scope.onLoad();
			}
		}
	}
]);