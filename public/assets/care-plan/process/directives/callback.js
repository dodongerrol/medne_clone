app.directive('callbackDirective', [
	'$state',
	'$stateParams',
	function directive($state, $stateParams) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				$(".steps-nav .step-li a").removeClass('active');
				$(".steps-nav .step-li a#plan-step").addClass('done');
				$(".steps-nav .step-li a#comp-step").addClass('done');
				$(".steps-nav .step-li a#payment-step").addClass('active');

				scope.payment_selected = 0;
					
				// console.log($stateParams);

				
			}
		}
	}
]);
