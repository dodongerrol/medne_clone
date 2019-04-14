app.directive('employeeDetailsDirective', [
	function directive() {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("employeeDetails Directive Runnning !");

				$(".steps-nav .step-li a").removeClass('active');
				$(".steps-nav .step-li a#plan-step").addClass('done');
				$(".steps-nav .step-li a#comp-step").addClass('done');
				$(".steps-nav .step-li a#payment-step").addClass('done');
				$(".steps-nav .step-li a#emp-step").addClass('active');
				
				scope.onLoad = function(){

				}
				
				scope.onLoad();
			}
		}
	}
]);
