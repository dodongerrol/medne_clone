app.directive('paymentDirective', [
	'$state',
	'carePlanFactory',
	'CarePlanSettings',
	'$stateParams',
	function directive($state, carePlanFactory, CarePlanSettings, $stateParams) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("payment Directive Runnning !");
				scope.amount = 0;
				$(".steps-nav .step-li a").removeClass('active');
				$(".steps-nav .step-li a#plan-step").addClass('done');
				$(".steps-nav .step-li a#comp-step").addClass('done');
				$(".steps-nav .step-li a#payment-step").addClass('active');

				scope.payment_selected = 0;
					
				console.log($stateParams);

				scope.getToken = function( ) {
					
				};

				scope.selectPayment = function(num){
					scope.payment_selected = num;

					$(".payment-type-wrapper .plan-type").removeClass('active');
					$(".payment-type-wrapper .plan-type").attr('disabled',false);

					$(".payment-type-wrapper .plan-type:nth-child(" + num + ") .icon-container").fadeIn();
					if(num == 1){
						scope.userDetails_data.paymentType = 'cheque';
					}else{
						scope.userDetails_data.paymentType = 'credit_card';
					}

					setTimeout(function(){
						$(".payment-type-wrapper .plan-type").removeClass('active');
						$(".payment-type-wrapper .plan-type").attr('disabled',false);
						$(".payment-type-wrapper .plan-type:nth-child(" + num + ")").addClass('active');
						$(".payment-type-wrapper .plan-type:nth-child(" + num + ")").attr('disabled',true);

						$(".payment-type-wrapper .plan-type:nth-child(" + num + ") .icon-container").hide();
						
						
					},1000);
					
				}

				scope.payButtonClicked = function(){
					

					if( scope.userDetails_data.cover_type == 'individual' ){
						scope.userDetails_data.paymentType = 'credit_card';
						scope.payment_selected = 2;
					}else{
						scope.payBtnClicked = true;
					}

					if( scope.payment_selected != 0 ){
						$('#pay-loader').fadeIn();
						$('#payPaymentButton').attr('disabled', true);
						var data = {
							customer_buy_start_id : scope.userDetails_data.customer_buy_start_id,
							cheque : ((scope.userDetails_data.paymentType == 'cheque') ? true : false),
							credit_card : ((scope.userDetails_data.paymentType == 'credit_card') ? true : false),
							start_date : moment(scope.userDetails_data.plan_start).format('YYYY-MM-DD')
						}

						console.log(data);

						CarePlanSettings.choosePayment( data )
							.then(function(response){
								console.log(response);
								if( response.data.status == true ){

									if( scope.userDetails_data.paymentType == 'cheque' ){
										$state.go('steps.payment_success2');
									}else{
										$('#form-one').hide();
										$('#form-two').fadeIn();
										scope.scrollTo('form-two');
									}
									
								}

								$('#pay-loader').fadeOut();
								$('#payPaymentButton').attr('disabled', false);
								
							});
					}
					
				}


				scope.payStripe = function(){
					console.log(scope.userDetails_data);

					$( '#submitStripe' ).click();
					// $state.go('steps.employee-details');
				}

				scope.getSession = function(){
					CarePlanSettings.getSessionSurvey()
						.then(function(response){
							console.log(response);

							if( response.data.status ){
								CarePlanSettings.getSessionSurveyData( response.data.data.customer_buy_start_id )
									.then(function(response){
										console.log(response);

										if( response.data.status ){
											scope.userDetails_data.customer_buy_start_id = response.data.data.customer_buy_start.customer_buy_start_id;

											if( response.data.data.corporate_business_contact ){
												if( response.data.data.corporate_business_contact.billing_contact == false  ){
													scope.userDetails_data.cardholder_name = response.data.data.billing_contact.first_name + " " + response.data.data.billing_contact.last_name ;
													scope.userDetails_data.card_email = response.data.data.billing_contact.work_email;
												}else{
													scope.userDetails_data.cardholder_name = scope.userDetails_data.contact_name;
													scope.userDetails_data.card_email = scope.userDetails_data.contact_email;
												}
											}
										}
									});
							}
						});
				}

				scope.datePickerClosed = function(){
					var month = $(".picker__select--month option:selected").text();
					var year = $(".picker__select--year option:selected").text();

					month = moment(month,'MMMM').format('MM');
					year = moment(year,'YYYY').format('YYYY');

					console.log(month);
					console.log(year);

					scope.userDetails_data.card_expiry = month + "/" + year;
					scope.userDetails_data.card_exp_month = month;
					scope.userDetails_data.card_exp_year = (year.toString()).substr(0, 2);

					$(".datepicker").val(month + "/" + year);
					$("#card_month").val(month);
					$("#card_year").val((year.toString()).substr(0, 2));

				}

				scope.datePickerClicked = function(){
					$(".picker").fadeIn();
				}

				scope.onLoad = function(){
					$("html, body").animate({
					        scrollTop: 0
					    }, 500);

					scope.getSession();
					carePlanFactory.setLastRoute('steps.company-details');

					scope.userDetails_data = carePlanFactory.getCarePlan();
					// $("#payPaymentButton span").text(scope.userDetails_data.plan_amount);
					$("#plan_amount").text(scope.userDetails_data.plan_amount);
					scope.amount = scope.userDetails_data.plan_amount;
					console.log(scope.userDetails_data);
				}
				
				scope.onLoad();

				scope.scrollTo = function( div ){
					$("html, body").animate({
					        scrollTop: $('#'+div).offset().top - 150
					    }, 500);
				}

				var $input = $('.datepicker').pickadate({
				    min : true,
				    format: 'mm/yyyy',
				    onClose : function(date){
				    	$(".picker").css({'display':'none'});
				    	
				    	scope.datePickerClosed();
						picker.close();
				    },
				    closeOnSelect: true,
					closeOnClear: true,
					selectYears: true,
					selectMonths: true,
				  });

			   	var picker = $input.pickadate('picker');


				// ---------- STRIPE ------------------- 	//



				
			}
		}
	}
]);
