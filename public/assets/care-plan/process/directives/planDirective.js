app.directive('planDirective', [
	'$state',
	'carePlanFactory',
	'CarePlanSettings',
	function directive($state, carePlanFactory, CarePlanSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("plan Directive Runnning !");
				$(".steps-nav .step-li a").removeClass('active');
				$(".steps-nav .step-li a#plan-step").addClass('active');

				scope.package = 0;
				scope.total_required_error = false;
				scope.total_minimum_error = false;
				scope.package_none_error = false;
				scope.promo_code_error = false;
				scope.promo_code_success = false;

				scope.map_selected = '';
				scope.list_clinics = [];
				// scope.list_clinics = [
				// 	{
				// 		area : 'Ang Mio Ko',
				// 		clinic : [
				// 			{
				// 				name : 'Sin Chong TCM',
				// 				address : '#01-4250 Blk 727 Ang Mo Kio Ave 6 S560727',
				// 			},
				// 			{
				// 				name : 'North East Medical Group',
				// 				address : '2450 AMK Ave 8, #01-02 S569811',
				// 			},
				// 		]
				// 	},
				// 	{
				// 		area : 'Ang Mio Ko',
				// 		clinic : [
				// 			{
				// 				name : 'Sin Chong TCM',
				// 				address : '#01-4250 Blk 727 Ang Mo Kio Ave 6 S560727',
				// 			},
				// 			{
				// 				name : 'North East Medical Group',
				// 				address : '2450 AMK Ave 8, #01-02 S569811',
				// 			},
				// 		]
				// 	},
				// 	{
				// 		area : 'Ang Mio Ko',
				// 		clinic : [
				// 			{
				// 				name : 'Sin Chong TCM',
				// 				address : '#01-4250 Blk 727 Ang Mo Kio Ave 6 S560727',
				// 			},
				// 			{
				// 				name : 'North East Medical Group',
				// 				address : '2450 AMK Ave 8, #01-02 S569811',
				// 			},
				// 		]
				// 	},
				// ];
				
				scope.selectPlan = function(num){
					scope.package = num;

					$(".plan-items-wrapper .plan").removeClass('active');
					$(".plan-items-wrapper .plan").attr('disabled',false);

					$(".plan-items-wrapper .plan:nth-child(" + num + ") .icon-container").fadeIn();

					setTimeout(function(){
						$(".plan-items-wrapper .plan").removeClass('active');
						$(".plan-items-wrapper .plan").attr('disabled',false);
						$(".plan-items-wrapper .plan:nth-child(" + num + ")").addClass('active');
						$(".plan-items-wrapper .plan:nth-child(" + num + ")").attr('disabled',true);

						$(".plan-items-wrapper .plan:nth-child(" + num + ") .icon-container").hide();
						if( num == 1 ){
							scope.userDetails_data.package_selected = {
								price_per_year : 0,
								duration : 'free',
								total : '0'
							};
							$("#payTop-btn span").text(scope.userDetails_data.package_selected.total);
							$("#payPlanButton span").text(scope.userDetails_data.package_selected.total);

						}else{
							scope.userDetails_data.package_selected = {
								price_per_year : 99,
								duration : 'per_year',
								total : 99 * scope.userDetails_data.employees
							};
							$("#payTop-btn span").text(scope.userDetails_data.package_selected.total);
							$("#payPlanButton span").text(scope.userDetails_data.package_selected.total);
							
						}

						scope.userDetails_data.choose_plan = scope.userDetails_data.package_selected.duration;
						scope.userDetails_data.plan_amount = scope.userDetails_data.package_selected.total;

						console.log(scope.userDetails_data);
						
					},1000);
				}

				scope.inputTotal = function(data){
					
					if( !data || data == "" ){
						scope.total_required_error = true;
						return false;
					}else{
						scope.total_required_error = false;
					}

					if( data < 3 ){
						scope.total_minimum_error = true;
						return false;
					}else{
						scope.total_minimum_error = false;
					}

					scope.userDetails_data.total = data;

					scope.userDetails_data.package_selected = {
						price_per_year : 99,
						duration : 'per_year',
						total : 99 * scope.userDetails_data.employees
					};

					$("#payTop-btn span").text(scope.userDetails_data.package_selected.total);
					$("#payPlanButton span").text(scope.userDetails_data.package_selected.total);
					$(".plan-package .package h1 span").text(scope.userDetails_data.package_selected.total);
				}

				scope.selectAge = function( ){
					var year = $('#plan-age-wrapper .picker__year-display div').text();
		    		var month = $('#plan-age-wrapper .picker__month-display div').text();
		    		var day = $('#plan-age-wrapper .picker__day--selected').text();
		    		var today;

			    	if( day == "" ){
			    		day = $('#plan-age-wrapper .picker__day-display div').text();

			    		today = moment( month + " " + day + ", " + year , 'MMM DD YYYY' );
			    	}else{
			    		today = moment( month + " " + day + ", " + year , 'MMM DD YYYY');
			    	}

			    	var age = moment().diff(moment(today), 'years');

					if( age < 18 || isNaN(age) ){
			    		$("#age_error").fadeIn();
			    		scope.age_error = true;
			    	}else{
			    		scope.age_error = false;
			    		$("#age_error").hide();
			    	}

			    	if( isNaN(age) ){
			    		$('#age-input').val(1);
		    			
			    	}else{
			    		$('#age-input').val(age);
			    		scope.userDetails_data.age = age;
			    	}			    	
				}

				scope.detailsDone = function(){

					if( scope.userDetails_data.cover_type == 'individual' ){
						scope.userDetails_data.package_selected = {
							price_per_year : 125,
							duration : 'per_year',
							total : 125
						};
					}else{
						scope.userDetails_data.package_selected = {
							price_per_year : 99,
							duration : 'per_year',
							total : 99 * scope.userDetails_data.employees
						};
					}

					scope.userDetails_data.choose_plan = scope.userDetails_data.package_selected.duration;
					scope.userDetails_data.plan_amount = scope.userDetails_data.package_selected.total;

					var data = {
						customer_buy_start_id : scope.userDetails_data.customer_buy_start_id,
						choose_plan: scope.userDetails_data.choose_plan,
						plan_amount: scope.userDetails_data.plan_amount,
						cover_type : scope.userDetails_data.cover_type,
						company_postal_code : scope.userDetails_data.company_postal_code,
						age : scope.userDetails_data.age,
						gender : scope.userDetails_data.gender,
						gender : scope.userDetails_data.gender,
						plan_start : scope.userDetails_data.plan_start,
						employees : scope.userDetails_data.employees
					};

					CarePlanSettings.insertCorporatePlan( data )
					.then(function(response){
							console.log(response);

							if( response.data.status ){

								carePlanFactory.setCarePlan( scope.userDetails_data );

								$("#form-two").fadeIn();
			   					scope.scrollTo('form-two');

			   					$("#payTop-btn").fadeIn();
			   					$("#payTop-btn span").text(scope.userDetails_data.package_selected.total);
			   					$("#payPlanButton span").text(scope.userDetails_data.package_selected.total);

								
							}
						});

   					
					
				}

				scope.skipPromoCode = function(){

					scope.userDetails_data.promo_code = null;
   					$("#form-three").fadeIn();
					$("#form-four").fadeIn();
	   				scope.scrollTo('form-three');
	   				
				}

				scope.submitPromoCode = function(){
					if( scope.promo_code != undefined && scope.promo_code != "" ){

		   				var data = {
							customer_buy_start_id : scope.userDetails_data.customer_buy_start_id,
							code: scope.promo_code,
						};


						CarePlanSettings.insertPromoCode( data )
						.then(function(response){

								if( response.data.status == false){
									scope.promo_code_error = true;
									scope.promo_code_success = false;

									swal({
									  title: "",
									  text: response.data.message,
									  type: "warning",
									  showCancelButton: false,
									  closeOnConfirm: true
									},
									function(){
									});


								}else{
									
					   				CarePlanSettings.getSessionSurvey()
										.then(function(response){

											if( response.data.status ){
												CarePlanSettings.getSessionSurveyData( response.data.data.customer_buy_start_id )
													.then(function(response){
														console.log(response);


														scope.promo_code_success = true;
										   				scope.promo_code_error = false;

										   				scope.userDetails_data.promo_code = scope.promo_code;
										   				if( response.data.data.customer_plan.plan_amount - response.data.data.customer_plan.discount >= 0 ){
										   					scope.userDetails_data.package_selected.total = response.data.data.customer_plan.plan_amount - response.data.data.customer_plan.discount;
										   				}else{
										   					scope.userDetails_data.package_selected.total = 0;
										   				}


										   				$("#payTop-btn span").text(scope.userDetails_data.package_selected.total);
		   												$("#payPlanButton span").text(scope.userDetails_data.package_selected.total);

										   				$("#payTop-btn .icon-wrapper").fadeIn();

										   				setTimeout(function(){
										   					$("#payTop-btn .icon-wrapper").hide();

										   					$("#form-three").fadeIn();
															$("#form-four").fadeIn();
											   				scope.scrollTo('form-three');
										   				},1000);
													});
											}
										});
								}
							});

					}else{
						scope.promo_code_error = true;
						scope.promo_code_success = false;
					}
				}

				$("#payTop-btn").click(function(){
	   				scope.scrollTo('activate-benefits');
				});


				scope.datePickerClicked = function(){
					$(".picker").fadeIn();
				}

				scope.mapClicked = function(map){
					console.log(map);
				}

				scope.mapOption = function(area){
					scope.map_selected = area.local_network_name;

					CarePlanSettings.getNetworkListPartners( area.local_network_id )
						.then(function(response){
							console.log(response);
							scope.list_clinics = response.data;
						});
				}

				scope.payPlanButton = function(){

					$(".plan-bottom .icon-wrapper").fadeIn();
					$(".plan-bottom #payPlanButton").hide();

					setTimeout(function(){
						$(".plan-bottom .icon-wrapper").hide();
						$(".plan-bottom #payPlanButton").fadeIn();

						$state.go('steps.company-details');
					},1000);
					
				}

				scope.getNetworks = function(){
					CarePlanSettings.getNetworkList()
						.then(function(response){
							scope.networks = response.data;

							scope.mapOption(scope.networks[0]);
						});
				}

				scope.getSession = function(){
					CarePlanSettings.getSessionSurvey()
						.then(function(response){

							if( response.data.status ){
								CarePlanSettings.getSessionSurveyData( response.data.data.customer_buy_start_id )
									.then(function(response){

										if( response.data.status == true){
											scope.userDetails_data.customer_buy_start_id = response.data.data.customer_buy_start.customer_buy_start_id;

											if( response.data.data.customer_plan != null){
												$("#form-two").fadeIn();
												$("#form-three").fadeIn();
												$("#form-four").fadeIn();

												$("#payTop-btn").fadeIn();
							   					

							   					if( response.data.data.customer_plan.discount != 0 ){
							   						scope.promo_code_inserted = true;

							   						var total = response.data.data.customer_plan.plan_amount - response.data.data.customer_plan.discount;

							   						if( total >= 0 ){
							   							$("#payTop-btn span").text(response.data.data.customer_plan.plan_amount - response.data.data.customer_plan.discount);
							   							$("#payPlanButton span").text(response.data.data.customer_plan.plan_amount - response.data.data.customer_plan.discount);
							   						}else{
							   							$("#payTop-btn span").text(0);
							   							$("#payPlanButton span").text(0);
							   						}
							   						
							   					}else{
							   						$("#payTop-btn span").text(scope.userDetails_data.package_selected.total);
							   						$("#payPlanButton span").text(scope.userDetails_data.package_selected.total);
							   					}
											}else{
												$("#payPlanButton span").text(scope.userDetails_data.employees * 99);
												$("#payTop-btn span").text(scope.userDetails_data.employees * 99);
											}

											$(".plan-package .package h1 span").text(scope.userDetails_data.employees * 99);
										}
									});
							}
						});
				}


				scope.onLoad = function(){
					scope.getSession();
					scope.getNetworks();
					carePlanFactory.setLastRoute('introduction');

					$("html, body").animate({
					        scrollTop: 0
					    }, 500);

					scope.userDetails_data = carePlanFactory.getCarePlan();

					console.log(scope.userDetails_data);

				}
				
				scope.onLoad();

				scope.scrollTo = function( div ){
					$("html, body").animate({
					        scrollTop: $('#'+div).offset().top - 150
					    }, 500);
				}

			   	var $input = $('#datepick').pickadate({
				    min : true,
				    format: 'd mmmm yyyy',
				    close: 'Set',
				    onOpen : function(){
				    	var date = scope.userDetails_data.plan_start;

				    	var year = moment( date ).format('YYYY');
			    		var month = moment( date ).format('MMMM');
			    		var day = moment( date ).format('D');

						$('#plan-start-wrapper .picker__year-display div').text( year );
						$('#plan-start-wrapper  .picker__month-display div').text( month );
						$('#plan-start-wrapper  .picker__day-display div').text( day );
				    },
				    onClose : function(){
				    	var year = $('#plan-start-wrapper  .picker__year-display div').text();
			    		var month = $('#plan-start-wrapper  .picker__month-display div').text();
			    		var day = $('#plan-start-wrapper  .picker__day--selected').text();
			    		var today;

				    	if( day == "" ){
				    		day = $('#plan-start-wrapper  .picker__day-display div').text();

				    		today = moment( month + " " + day + ", " + year ).format('D MMMM YYYY');
				    	}else{
				    		today = moment( month + " " + day + ", " + year ).format('D MMMM YYYY');
				    	}
				    	console.log(today);
				    	$('#plan-start-wrapper .picker__input').val(today);

				    	scope.userDetails_data.plan_start = today;

				    	$("#plan-start-wrapper  .picker").css({'display':'none'});
				    	picker.close();

				    },
				    closeOnSelect: false,
					closeOnClear: false,
				  });

				var picker = $input.pickadate('picker');
				
			}
		}
	}
]);
