app.directive('removeDepContainerDirective', [
	'$state',
	'removeDependentFactory',
	'$stateParams',
	'hrSettings',
	function directive( $state, removeDependentFactory, $stateParams, hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log( 'removeDepContainerDirective running!' );
				scope.selected_member_id = $stateParams.member_id;
				scope.emp_details = removeDependentFactory.getEmployeeDetails();
				console.log(scope.emp_details);
				
				scope.getSession = async function () {
          await hrSettings.getSession()
            .then(async function (response) {
              // console.log( response );
              scope.selected_customer_id = response.data.customer_buy_start_id;
            });
        }

				scope.onLoad	=	function(){
					scope.getSession();
					if(scope.emp_details != null){
						if($state.current.name != 'dependent-remove.remove-emp-inputs'){
							$state.go('dependent-remove.remove-emp-inputs');
						}
					}else{
						$state.go('member.dep-details', { member_id : scope.selected_member_id });
					}
				}
				scope.onLoad();
			}
		}
	}
]);