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
					scope.showLoading();
					removeEmployeeFactory.setEmployeeDetails( scope.selectedEmployee );
					$state.go('employee-overview.remove-emp-checkboxes');
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

				scope.onLoad	=	function(){
					scope.hideLoading();
				}
				scope.onLoad();
			}
		}
	}
]);

