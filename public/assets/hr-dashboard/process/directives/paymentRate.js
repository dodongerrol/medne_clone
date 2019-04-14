app.directive('paymentRate', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("paymentRate Runnning !");
				
				scope.paymentRate = {};
				scope.isCheque = true;
				scope.isCredit = false;
				scope.choose_err = false;
				scope.paying = false;
				scope.customer_active_plan_id = null;
				scope.payBtn = function( ) {

					if( !scope.isCredit && !scope.isCheque ){
						scope.choose_err = true;
						return false;
					}else{
						scope.choose_err = false;
					}

					if( scope.isCredit == true ){
						$state.go('credit-card-form');
					}

					if( scope.isCheque == true ){
						
						var data = {
							// customer_active_plan_id : scope.customer_active_plan_id,
							cheque : scope.isCheque == true ? 'true' : 'false',
							credit_card : scope.isCredit == true ? 'true' : 'false',
						}

						scope.paying = true;

						hrSettings.payMethod( data )
							.then(function(response){
								scope.paying = false;

								if( response.data.status == true ){
									$state.go('cheque-payment-success');
									dashboardFactory.setHeadCountStatus(false);
									dashboardFactory.clearAll();
								}
							});
					}
				}

				scope.getRate = function( ){
					hrSettings.getPaymentRates( scope.customer_active_plan_id )
					.then(function(response){
						console.log(response);
						if( response.data.status == true ){
							scope.paymentRate = response.data;
						}else{
							$state.go('employee-overview');
						}
						
					});
					// console.log(dashboardFactory.getActivePlanID());
					// if(dashboardFactory.getActivePlanID()) {
					// 	scope.customer_active_plan_id = dashboardFactory.getActivePlanID();
					// 	hrSettings.getPaymentRates( scope.customer_active_plan_id )
					// 		.then(function(response){
					// 			if( response.data.status == true ){
					// 				scope.paymentRate = response.data.data;
					// 			}else{
					// 				$state.go('employee-overview');
					// 			}
								
					// 		});
					// } else {
					// 	hrSettings.getEnrollmentProgress()
					// 		.then(function(response){
					// 			if(response.data.data.added_purchase_status) {
					// 				hrSettings.getPaymentRates( response.data.data.active_plans[response.data.data.active_plans.length - 1].customer_active_plan_id )
					// 					.then(function(response){
					// 						if( response.data.status == true ){
					// 							scope.paymentRate = response.data.data;
					// 							scope.customer_active_plan_id = response.data.data.customer_active_plan_id;
					// 						}else{
					// 							$state.go('employee-overview');
					// 						}
					// 					});
					// 			} else {
					// 				$state.go('employee-overview');
					// 			}
					// 		});
					// }

				}

				scope.showGlobalModal = function( message ){
				    $( "#global_modal" ).modal('show');
				    $( "#global_message" ).text(message);
				 }

		        scope.onLoad = function( ){
		        	scope.getRate();
		        }

		        scope.onLoad();
			}
		}
	}
]);
