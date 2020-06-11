app.directive('refundSummaryDirective', [
	'$state',
	'removeEmployeeFactory',
	'hrSettings',
	function directive( $state, removeEmployeeFactory, hrSettings ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log( 'refundSummaryDirective running!' );
				scope.emp_details = removeEmployeeFactory.getEmployeeDetails();
				scope.member_refund_details = {};
				console.log(scope.emp_details);

				scope.get_member_refund = function () {
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
				
				scope.onLoad	=	function(){
					scope.get_member_refund();
				}
				scope.onLoad();
			}
		}
	}
]);