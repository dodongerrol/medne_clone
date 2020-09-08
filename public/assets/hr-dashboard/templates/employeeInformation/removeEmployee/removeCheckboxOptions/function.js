app.directive('removeCheckboxOptionsDirective', [
	'$state',
	'$timeout',
	'removeEmployeeFactory',
	function directive( $state, $timeout, removeEmployeeFactory ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log( 'removeCheckboxOptionsDirective running!' );

				scope.emp_details = removeEmployeeFactory.getEmployeeDetails();
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
					$state.go('member-remove.remove-emp-inputs');
				}
				scope.nextBtn	=	function(){
					console.log( scope.checkboxes_options );
					if( !scope.checkboxes_options.replace && !scope.checkboxes_options.reserve && !scope.checkboxes_options.remove ){
						swal('Error!', 'Please select an option.', 'error');
					}
					
					if( scope.checkboxes_options.replace == true ){
						scope.showLoading();
						scope.emp_details.remove_option = 'replace';
						removeEmployeeFactory.setEmployeeDetails( scope.emp_details );
						$state.go('member-remove.remove-replace-emp');
					}
					if( scope.checkboxes_options.reserve == true ){
						scope.showLoading();
						scope.emp_details.remove_option = 'reserve';
						removeEmployeeFactory.setEmployeeDetails( scope.emp_details );
						$state.go('member-remove.health-spending-account-summary');
					}
					if( scope.checkboxes_options.remove == true ){
						scope.emp_details.remove_option = 'remove';
						removeEmployeeFactory.setEmployeeDetails( scope.emp_details );

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
						// 			$state.go('member-remove.refund-summary');
						// 		}
						// 	});
						// }else{
							scope.showLoading();
							$state.go('member-remove.health-spending-account-summary');
						// }
					}
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