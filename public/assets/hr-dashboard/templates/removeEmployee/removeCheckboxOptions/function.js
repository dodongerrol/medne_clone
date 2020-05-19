app.directive('removeCheckboxOptionsDirective', [
	'$state',
	'removeEmployeeFactory',
	function directive( $state, removeEmployeeFactory ) {
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
					$state.go('employee-overview.remove-emp-inputs');
				}
				scope.nextBtn	=	function(){
					console.log( scope.checkboxes_options );
					if( !scope.checkboxes_options.replace && !scope.checkboxes_options.reserve && !scope.checkboxes_options.remove ){
						swal('Error!', 'Please select an option.', 'error');
					}
					scope.showLoading();
					if( scope.checkboxes_options.replace == true ){
						scope.emp_details.remove_option = 'replace';
						removeEmployeeFactory.setEmployeeDetails( scope.emp_details );
						$state.go('employee-overview.remove-replace-emp');
					}
					if( scope.checkboxes_options.reserve == true ){
						scope.emp_details.remove_option = 'reserve';
						removeEmployeeFactory.setEmployeeDetails( scope.emp_details );
						$state.go('employee-overview.health-spending-account-summary');
					}
					if( scope.checkboxes_options.remove == true ){
						scope.emp_details.remove_option = 'remove';
						removeEmployeeFactory.setEmployeeDetails( scope.emp_details );
						$state.go('employee-overview.health-spending-account-summary');
					}
				}
				scope.onLoad	=	function(){
					scope.hideLoading();
				}
				scope.onLoad();
			}
		}
	}
]);