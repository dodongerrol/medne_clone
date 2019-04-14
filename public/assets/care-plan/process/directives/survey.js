app.directive('surveyDirective', [
	'$state',
	'carePlanFactory',
	'CarePlanSettings',
	function directive($state, carePlanFactory, CarePlanSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("survey Directive Runnning !");

				scope.carePlan_data = {};

				var step_ctr = 1;

				scope.post_digit_error = false;
				scope.post_required_error = false;
				scope.post_none_error = false;
				scope.total_minimum_error = false;
				scope.total_required_error = false;
				scope.name_email_error = false;
				scope.cover_error = false;
				scope.show_next = false;
				scope.age_error = false;
				scope.email_error = false;

				var localData = carePlanFactory.getCarePlan();

				console.log( localData );

				scope.selectData = function(data){
					
					if( data == ""){
						scope.cover_error = true;
						return false;
					}else{
						scope.cover_error = false;
					}					

					if( data == 'individual' ){
						scope.carePlan_data.cover_type = 'individual';
						$("#one.survey-item:nth-child(2) .select-wrapper").css({'width':'100px'});
						$("#one.survey-item:nth-child(2) .select-wrapper input").css({'width':'100px'});

						setTimeout(function(){
							$('#select-gender').material_select();
						}, 100);
						
					}else{
						scope.carePlan_data.cover_type = 'team/corporate';
						$("#one.survey-item:nth-child(2) .select-wrapper").css({'width':'340px'});
						$("#one.survey-item:nth-child(2) .select-wrapper input").css({'width':'340px'});
					}

					if(step_ctr == 1){
						scope.grayItem();
					}
				}			

				scope.inputPostal = function(data){
					if( data != undefined ){
						if( data == "" ){
							scope.post_required_error = true;
							return false;
						}else{
							scope.post_required_error = false;
						}

						if( data.length < 6 ){
							scope.post_digit_error = true;
							return false;
						}else{
							scope.post_digit_error = false;
						}

						if( data.length > 6 ){
							scope.post_digit_error = true;
							return false;
						}else{
							scope.post_digit_error = false;
						}

						if( data == 0 ){
							scope.post_none_error = true;
							return false;
						}else{
							scope.post_none_error = false;
						}

						if(step_ctr == 3){

							$("input").blur();
							
							scope.grayItem();

							if( scope.carePlan_data.cover_type == 'individual' ){
								$('#select-gender').material_select();

								$('#age-input').focus();
							}else{
								$("#three input").focus();
							}
						}
					}
				}

				scope.inputNumPostal = function( input ){
					scope.carePlan_data.company_postal_code = input.replace(/[^0-9]/g, '');
				}

				scope.inputTotal = function(data){
					
					if( step_ctr >= 4 ){
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
					}

					if(step_ctr == 4){
						scope.grayItem();
						$("#plan-datepicker").focus();
					}
				}

				scope.selectAge = function( ageInput ){
					if( ageInput != undefined ){
						var age = ageInput;

						if( age < 18 || isNaN(age) ){
				    		$("#age_error").fadeIn();
				    		scope.age_error = true;
				    	}else{

				    		// scope.carePlan_data.contact_dob = moment(today).format('YYYY-MM-DD');
				    		scope.age_error = false;
				    		$("#age_error").hide();

				    	}

			    		scope.carePlan_data.age = age;
					}
				}

				scope.selectGender = function(data){
					scope.carePlan_data.gender = data;

					if( scope.age_error == false ){
						scope.grayItem();
						$("#plan-datepicker").focus();
					}
				}

				scope.datePickerClicked = function(){
					$(".picker").fadeIn();
				}

				scope.inputInfo = function(data){
					
					if( scope.carePlan_data.contact_name ){
						$(".survey-wrapper #five.survey-item input.first").attr('size', $("input.first").val().length);
						$(".survey-wrapper #five.survey-item input.first").css({'width':'auto'});

						scope.carePlan_data.firstname = scope.carePlan_data.contact_name.substring(0, scope.carePlan_data.contact_name.lastIndexOf(" "));
						scope.carePlan_data.lastname = scope.carePlan_data.contact_name.substring(scope.carePlan_data.contact_name.lastIndexOf(" ") + 1);
						
						if( scope.carePlan_data.firstname == '' ){
							scope.no_lastname2_error = true;
							return false;
	  					}else{
	  						scope.no_lastname2_error = false;
	  					}
					}else{
						$(".survey-wrapper #five.survey-item input.first").css({'width':'500px'});
					}

					if( step_ctr >= 6 ){

						if( !scope.carePlan_data.contact_name ){
							scope.name_error = true;
							return false;
						}else{
							scope.name_error = false;
						}

						if( name.substring(0, name.lastIndexOf(" ")) == ""){
							scope.no_lastname_error = false;
							
						}else{
							scope.no_lastname_error = true;
							return false;
						}

						$("input.second").focus();
					}

					if(step_ctr == 6 && scope.carePlan_data.contact_name && scope.carePlan_data.contact_email){
						scope.grayItem();
					}
				}

				scope.inputEmail = function(data){
					console.log(data);
					if( scope.carePlan_data.contact_email ){
						$(".survey-wrapper #five.survey-item input.second").attr('size', $("input.second").val().length);
						$(".survey-wrapper #five.survey-item input.second").css({'width':'auto'});
						
					}else{
						$(".survey-wrapper #five.survey-item input.second").css({'width':'320px'});
					}

					if( step_ctr >= 6 ){

						if( !scope.carePlan_data.contact_email ){
							scope.email_error2 = true;
							return false;
						}else{
							scope.email_error2 = false;
							var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

	  						if( !emailReg.test( scope.carePlan_data.contact_email ) ){
	  							scope.email_error = true;
	  							return false;
	  						}else{
	  							scope.email_error = false;
								scope.show_next = true;
								scope.carePlan_data.email = scope.carePlan_data.contact_email;
	  						}

						}
					}

					if(step_ctr == 6 && scope.carePlan_data.contact_name && scope.carePlan_data.contact_email){
						scope.grayItem();
					}
				}

				scope.typingName = function(name){

					if( $("input.first").val().length > $("input.first").attr('size') ){
						$(".survey-wrapper #five.survey-item input.first").attr('size', $("input.first").val().length + 1);
						$(".survey-wrapper #five.survey-item input.first").css({'width':'auto'});
					}
				}

				scope.typingEmail = function(email){

					if( $("input.second").val().length > $("input.second").attr('size') ){
						$(".survey-wrapper #five.survey-item input.second").attr('size', $("input.second").val().length + 1);
						$(".survey-wrapper #five.survey-item input.second").css({'width':'auto'});
					}
				}

				scope.grayItem = function(){
					if( step_ctr == 1 ){
						$(".survey-item:nth-child(1)").animate({'height':'0'},100);
						step_ctr ++;
					}

					$(".survey-item:nth-child(" +  step_ctr + ")").removeClass('all-white').addClass('all-gray');
					step_ctr ++;
					$(".survey-item:nth-child(" +  step_ctr + ")").fadeIn();

					if( step_ctr == 5 ){
						$(".survey-wrapper").animate({'margin-top':'0','padding' : '0px 130px'},100);
					}
				}

				scope.nextButton = function(){
					$(".loading-survey-wrapper").fadeIn();
					$(".survey-wrapper").hide();

					scope.carePlan_data.firstname = scope.carePlan_data.contact_name.substring(0, scope.carePlan_data.contact_name.lastIndexOf(" "));
					scope.carePlan_data.lastname = scope.carePlan_data.contact_name.substring(scope.carePlan_data.contact_name.lastIndexOf(" ") + 1);
					scope.carePlan_data.email = scope.carePlan_data.contact_email;
					var data = scope.carePlan_data;
					console.log(data);
					data.plan_start = moment(data.plan_start).format('YYYY-MM-DD');

					CarePlanSettings.insertSessionSurvey( data )
						.then(function(response){

							scope.carePlan_data.plan_start = moment(scope.carePlan_data.plan_start).format('D MMMM YYYY');

							if( response.data.status == false ){
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
								// $(".loading-survey-wrapper").fadeIn();
								// $(".survey-wrapper").hide();

								scope.carePlan_data.customer_buy_start_id = response.data.customer_buy_start_id;
								carePlanFactory.setCarePlan( scope.carePlan_data );

								setTimeout(function(){
									$state.go('steps.plan');
									// $(".loading-survey-wrapper").hide();
									// $(".survey-wrapper").fadeIn();
									
								},3000);
							}
						});

				}

				scope.getSession = function(){
					CarePlanSettings.getSessionSurvey()
						.then(function(response){
							console.log(response);

							if( response.data.status ){
								CarePlanSettings.getSessionSurveyData( response.data.data.customer_buy_start_id )
									.then(function(response){
										console.log(response);

										if( response.data.status == true){
											step_ctr = 99;
											scope.carePlan_data =  localData;
											console.log(scope.carePlan_data);


											$('body').css({'transition':'none !important'});
											$(".survey-wrapper").css({'margin-top':'0','padding' : '0px 130px'});
											$(".survey-item").show().addClass('all-gray');
											$(".survey-item:nth-child(1)").hide();

											setTimeout(function(){
												$('#select-gender').material_select();
												// var $input = $('#age-input').pickadate({
												//     // min : true,
												//     format: 'd mmmm yyyy',
												//     close: 'Set',
												//     onStart : function(){
												//     	var year = moment( scope.carePlan_data.contact_dob ).format('YYYY');
												//     	var day = moment( scope.carePlan_data.contact_dob ).format('DD');
												//     	var month = moment( scope.carePlan_data.contact_dob ).format('MMMM');
												//     	var month_num = moment( scope.carePlan_data.contact_dob ).format('M');

												//     	$('#if-individual .picker__year-display div').text( year );
											 //    		$('#if-individual .picker__month-display div').text( month );
											 //    		$('#if-individual .picker__day-display div').text( day );
											 //    		$('#if-individual .picker__day--selected').text( day );

											 //    		$('#if-individual .picker__select--month').val( month_num-1 );
											 //    		$('#if-individual .picker__day--year').val( year );
												//     },
												//     onSet:function(){
												//     	scope.selectAge();
												//     },
												//     onClose : function(){
												//     	scope.selectAge();

												//     	$("#if-individual .picker").css({'display':'none'});
												//     	picker.close();
												//     },
												//     closeOnSelect: false,
												// 	closeOnClear: false,
												// 	selectYears: 4,
												// 	selectMonths: true,
												//   });

												// var picker = $input.pickadate('picker');

												if( scope.carePlan_data.cover_type == 'team/corporate' ){
													$("#cover-select .select-wrapper input").val( 'me and my team' );

													$("#one .select-wrapper").css({'width':'340px'});
													$("#one .select-wrapper input").css({'width':'340px'});
													
												}else{
													$("#cover-select .select-wrapper input").val( 'me' );

													$("#one .select-wrapper").css({'width':'100px'});
													$("#one .select-wrapper input").css({'width':'100px'});
												}

												$(".survey-wrapper #five.survey-item input.first").attr('size', scope.carePlan_data.contact_name.length);
												$(".survey-wrapper #five.survey-item input.first").css({'width':'auto'});
												$(".survey-wrapper #five.survey-item input.second").attr('size', scope.carePlan_data.contact_email.length);
												$(".survey-wrapper #five.survey-item input.second").css({'width':'auto'});

											},300);

											scope.show_next = true;
										}
									});
							}
						});
				}

				scope.onLoad = function(){

					scope.getSession();

					$(".loading-survey-wrapper").hide();
					$(".survey-wrapper").fadeIn();

					// carePlanFactory.clearAll();
				}
				
				scope.onLoad();

				var $input = $('#plan-datepicker').pickadate({
				    min : true,
				    format: 'd mmmm yyyy',
				    close: 'Set',
				    onClose : function(){
				    	var year = $('#four .picker__year-display div').text();
			    		var month = $('#four .picker__month-display div').text();
			    		var day = $('#four .picker__day--selected').text();
			    		var today;

				    	if( day == "" ){
				    		day = $('#four .picker__day-display div').text();

				    		today = moment( month + " " + day + ", " + year ).format('D MMMM YYYY');
				    	}else{
				    		today = moment( month + " " + day + ", " + year ).format('D MMMM YYYY');
				    	}

				    	if( step_ctr == 5 ){
				    		scope.grayItem();

				    		$("#input-name").focus();
				    	}

				    	$("#four .picker").css({'display':'none'});
				    	picker.close();
				    	// YYYY-MM-DD

				    	$('#four .picker__input').val(today);

				    	scope.carePlan_data.start = today;
				    },
				    closeOnSelect: false,
					closeOnClear: false,
				  });

				var picker = $input.pickadate('picker');
			}
		}
	}
]);
