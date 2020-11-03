app.directive('removeDependentCheckboxOptionsDirective', [
	'$state',
	'$timeout',
	'removeDependentFactory',
	'dependentsSettings',
	function directive( $state, $timeout, removeDependentFactory, dependentsSettings ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log( 'removeDependentCheckboxOptionsDirective running!' );

				scope.emp_details = removeDependentFactory.getEmployeeDetails();
				console.log(scope.emp_details);
				scope.checkboxes_options = {};
				console.log(scope.emp_details);

				scope.checkboxOption = function (opt) {
          scope.checkboxes_options = {
						replace : false,
						reserve : false,
						remove : false,
					}
          if (opt == 1) {
            scope.checkboxes_options.replace = true;
          }
          if (opt == 2) {
            scope.checkboxes_options.reserve = true;
          }
          if (opt == 3) {
            scope.checkboxes_options.remove = true;
          }
        }
				scope.backBtn	=	function(){
					$state.go('dependent-remove.remove-emp-inputs');
				}
				scope.nextBtn	=	function(){
					console.log( scope.checkboxes_options );
					if( !scope.checkboxes_options.replace && !scope.checkboxes_options.reserve && !scope.checkboxes_options.remove ){
						swal('Error!', 'Please select an option.', 'error');
					}
					
					if( scope.checkboxes_options.replace == true ){
						scope.showLoading();
						scope.emp_details.remove_option = 'replace';
						removeDependentFactory.setEmployeeDetails( scope.emp_details );
						$state.go('dependent-remove.remove-replace-emp');
					}
					if( scope.checkboxes_options.reserve == true ){
						// scope.showLoading();
						scope.emp_details.remove_option = 'reserve';
						removeDependentFactory.setEmployeeDetails( scope.emp_details );
						// $state.go('dependent-remove.health-spending-account-summary');
						// $("#remove-employee-confirm-modal").modal('show');
						scope.reserveDependent();
					}
					if( scope.checkboxes_options.remove == true ){
						scope.emp_details.remove_option = 'remove';
						removeDependentFactory.setEmployeeDetails( scope.emp_details );

						$("#remove-employee-confirm-modal").modal('show');

						// if (scope.emp_details.account_type == 'enterprise_plan') {
						// 	swal({
						// 		title: "Confirm",
						// 		text: "Are you sure you want to remove this employee completely?",
						// 		type: "warning",
						// 		showCancelButton: true,
						// 		confirmButtonColor: "#ff6864",
						// 		confirmButtonText: "Remove",
						// 		cancelButtonText: "No",
						// 		closeOnConfirm: true,
						// 		customClass: "removeEmp"
						// 	},
						// 	function (isConfirm) {
						// 		if(isConfirm){
						// 			scope.showLoading();
						// 			$state.go('dependent-remove.refund-summary');
						// 		}
						// 	});
						// }else{
							// scope.showLoading();
							// $state.go('dependent-remove.health-spending-account-summary');
						// }
					}
				}

				scope.reserveDependent = function () {
          var data = {
            user_id: scope.emp_details.user_id,
            // date_enrollment: moment(scope.reserve_emp_date, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            last_date_of_coverage: moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            customer_id: scope.selected_customer_id,
          }
          scope.showLoading();
          dependentsSettings.reserveDependentService(data)
            .then(function (response) {
              // console.log( response );
              if (response.data.status) {
                scope.hideLoading();
								scope.isRemoveSuccess = true;
              } else {
                scope.hideLoading();
                swal('Error!', response.data.message, 'error');
              }
            });
        }

				scope.submitRemoveEmployee = function () {
					var data = {
						expiry_date: moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'),
						user_id: scope.emp_details.user_id
					}
					scope.showLoading();
					dependentsSettings.removeDependent(data)
						.then(function (response) {
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
          // scope.resetRemoveBtn();
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
				scope.onLoad	=	function(){
					scope.hideLoading();
				}
				scope.onLoad();
			}
		}
	}
]);