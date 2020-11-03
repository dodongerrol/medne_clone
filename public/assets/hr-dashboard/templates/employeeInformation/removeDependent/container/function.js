app.directive('removeEmpContainerDirective', [
	'$state',
	'removeEmployeeFactory',
	'hrSettings',
	'dependentsSettings',
	function directive( $state, removeEmployeeFactory, hrSettings, dependentsSettings ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log( 'removeEmpContainerDirective running!' );
				scope.emp_details = removeEmployeeFactory.getEmployeeDetails();
				
				
				scope.onLoad	=	function(){
					if($state.current.name != 'member-remove.remove-emp-inputs' || $state.current.name != 'member-remove.health-spending-account-summary'){
						$state.go('member-remove.remove-emp-inputs');
					}
				}
				scope.onLoad();
			}
		}
	}
]);