app.directive('companyDetailsDirective', [
	'$state',
	'carePlanFactory',
	'CarePlanSettings',
	function directive($state, carePlanFactory, CarePlanSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("companyDetails Directive Runnning !");

				$(".steps-nav .step-li a").removeClass('active');
				$(".steps-nav .step-li a#plan-step").addClass('done');
				$(".steps-nav .step-li a#comp-step").addClass('active');

				scope.no_company_error = false;
				scope.no_address_error = false;
				scope.no_postal_code_error = false;

				scope.no_fname_error = false;
				scope.no_lname_error = false;
				scope.no_job_error = false;
				scope.no_email_error = false;
				scope.notSame_email_error = false;
				scope.no_phone_error = false;

				scope.no_fname_error2 = false;
				scope.no_lname_error2 = false;
				scope.no_email_error2 = false;
				scope.notSame_email_error2 = false;

				scope.no_address_error2 = false;
				scope.no_postal_code_error2 = false;

				scope.no_person_selection = false;
				scope.no_address_selection = false;

				scope.email_blank_error = false;
				scope.email_mismatch_error = false;
				scope.email_confirm_mismatch_error = false;
				scope.job_error = false;

				scope.years = new Array(99);

				scope.nature_of_business_list = {};
				scope.job_list = {};

				scope.invalid_nric = false;

				scope.stepOneDone = function(){
					scope.stepOneClicked = true;

					if( !scope.userDetails_data.nature_of_business ){
						$("#business-select .select-wrapper").addClass('invalid');
						$("#business-select .select-wrapper input").removeClass('valid');
					}else{
						$("#business-select .select-wrapper").removeClass('invalid');
						$("#business-select .select-wrapper input").addClass('valid');
					}

					if( !scope.userDetails_data.establishment ){
						$("#establish-select .select-wrapper").addClass('invalid');
						$("#establish-select .select-wrapper input").removeClass('valid');
					}else{
						$("#establish-select .select-wrapper").removeClass('invalid');
						$("#establish-select .select-wrapper input").addClass('valid');
					}

					if( scope.userDetails_data.company_name && scope.userDetails_data.nature_of_business &&
						scope.userDetails_data.company_address && scope.userDetails_data.company_postal_code &&
						scope.userDetails_data.establishment){

						var data = {
							customer_buy_start_id : scope.userDetails_data.customer_buy_start_id,
							company_name : scope.userDetails_data.company_name,
							nature_of_business : scope.userDetails_data.nature_of_business,
							company_address : scope.userDetails_data.company_address ,
							postal_code : scope.userDetails_data.company_postal_code,
							establishment : scope.userDetails_data.establishment
						};

						CarePlanSettings.insertCorporateInfo( data )
						.then(function(response){


							if( response.data.status == true ){
								if( scope.editSummary == true ){
									$("#form-one").hide();
									$("#form-two").hide();
									$(".personal-container").hide();
									$("#form-five").fadeIn();

									scope.editSummary = false;
								}else{
									$("#form-one").hide();
									$("#form-two").fadeIn();
									scope.scrollTo('form-two');
									$('select').material_select();
								}
							}
						});
						
					}

				}

				scope.stepTwoDone = function(){
					scope.stepTwoClicked = true;

					if( scope.userDetails_data.email != scope.userDetails_data.email_confirm || scope.userDetails_data.email == undefined || scope.userDetails_data.email_confirm == undefined ){
						scope.email_same_error = true;
					}else{
						scope.email_same_error = false;
						
						var data = {
							customer_buy_start_id : scope.userDetails_data.customer_buy_start_id,
							first_name : scope.userDetails_data.firstname,
							last_name : scope.userDetails_data.lastname,
							job_title : scope.userDetails_data.job_title ,
							work_email : scope.userDetails_data.email,
							phone : scope.userDetails_data.phone,
							billing_contact : scope.person_isBilling,
							billing_address : scope.address_isBilling,
							billing_address_data : (scope.userDetails_data.billing_person ? scope.userDetails_data.billing_person.address : null) ,
							postal_code : (scope.userDetails_data.billing_person ? scope.userDetails_data.billing_person.postal_code : null),
						};

						if( scope.person_isBilling == false ){
							data.billing_contact = false;
							data.billing_first_name = scope.userDetails_data.billing_person.firstname;
							data.billing_last_name = scope.userDetails_data.billing_person.firstname;
							data.billing_work_email = scope.userDetails_data.billing_person.email;
						}

						if( scope.address_isBilling == true ){
							scope.billing_address = true;
							data.billing_address_data = scope.userDetails_data.billing_person.address;
							data.postal_code = scope.userDetails_data.billing_person.postal_code;
						}


						CarePlanSettings.insertCorporateContact( data )
						.then(function(response){

							if( response.data.status == true ){
								if( scope.editSummary == true ){
									$("#form-one").hide();
									$("#form-two").hide();
									$(".personal-container").hide();
									$("#form-five").fadeIn();

									scope.editSummary = false;
								}else{
									$("#form-two").hide();
									$("#form-three").fadeIn();
									scope.scrollTo('form-three');
									scope.email_taken_error = false;

								}
							}else{
								scope.email_taken_error = true;
								console.log(response);
							}
						});
					}
				}

				scope.checkNRIC = function(theNric) {
				      var nric_pattern = new RegExp('^[stfgSTFG]{1}[0-9]{7}[a-zA-z]{1}$');
				      if (nric_pattern.test(theNric) == false) {
				       return "invalid_format";
				      } else {
				       var icArray = new Array(9);
				       for (i = 0; i < 9; i++) {
				        icArray[i] = theNric.charAt(i);
				      }
				      icArray[1] *= 2;
				      icArray[2] *= 7;
				      icArray[3] *= 6;
				      icArray[4] *= 5;
				      icArray[5] *= 4;
				      icArray[6] *= 3;
				      icArray[7] *= 2;
				      var weight = 0;
				      for (i = 1; i < 8; i++) {
				       weight += parseInt(icArray[i]);
				      }
				      var offset = (icArray[0] == "T" || icArray[0] == "G") ? 4 : 0;
				      var temp = (offset + weight) % 11;
				      var st = Array("J", "Z", "I", "H", "G", "F", "E", "D", "C", "B", "A");
				      var fg = Array("X", "W", "U", "T", "R", "Q", "P", "N", "M", "L", "K");
				      var theAlpha;
				      var nric_fin;
				      if (icArray[0] == "S" || icArray[0] == "T") {
				       theAlpha = st[temp];
				       nric_fin = 'NRIC';
				      } else if (icArray[0] == "F" || icArray[0] == "G") {
				       theAlpha = fg[temp];
				       nric_fin = 'FIN';
				      }
				      if (theAlpha != icArray[8]){
				       return false;
				      } else {
				       return true;
				      }
				     }
			     };

				scope.stepTwoIndividalDone = function(){
					console.log(scope.userDetails_data);

					var checkNRIC = scope.checkNRIC(scope.userDetails_data.nric);

					console.log(checkNRIC);

					if(scope.userDetails_data.email != scope.userDetails_data.email_confirm) {
						scope.email_mismatch_error = true;
						scope.email_blank_error = false;
						scope.invalid_nric = false;
						scope.job_error = false;
						scope.email_confirm_mismatch_error = false;
						return false;
					}

					if(!scope.userDetails_data.email) {
						scope.email_mismatch_error = false;
						scope.email_blank_error = true;
						scope.invalid_nric = false;
						scope.job_error = false;
						scope.email_confirm_mismatch_error = false;
						return false;
					}

					if(!scope.userDetails_data.email_confirm) {
						scope.email_confirm_mismatch_error = true;
						scope.email_mismatch_error = false;
						scope.email_blank_error = true;
						scope.invalid_nric = false;
						scope.job_error = false;
						return false;
					}

					if(!scope.userDetails_data.phone) {
						var mobile = null;
					} else {
						var mobile = scope.userDetails_data.phone;
					}

					if(!scope.userDetails_data.home_address) {
						var address = null;
					} else {
						var address = scope.userDetails_data.home_address;
					}

					if(!scope.userDetails_data.job_title) {
						scope.job_error = true;
						scope.email_mismatch_error = false;
						scope.email_blank_error = false;
						scope.invalid_nric = false;
						scope.email_confirm_mismatch_error = false;
						return false;
					}

					if( checkNRIC == true ){
						scope.invalid_nric = false;

						var data = {
							customer_buy_start_id : scope.userDetails_data.customer_buy_start_id,
							first_name : scope.userDetails_data.firstname,
							last_name : scope.userDetails_data.lastname,
							nric : scope.userDetails_data.nric ,
							age : scope.userDetails_data.age,
							contact_dob : "1995-10-12" ,
							contact_gender : scope.userDetails_data.contact_gender ,
							job_title : scope.userDetails_data.job_title,
							email : scope.userDetails_data.email,
							mobile : mobile,
							address : address,
							postal_code : scope.userDetails_data.company_postal_code,
						};

						CarePlanSettings.insertPersonalDetails( data )
						.then(function(response){
							console.log(response);
							if( response.data.status == true ){
								if( scope.editSummary == true ){
									$("#form-one").hide();
									$("#form-two").hide();
									$(".personal-container").hide();
									$("#form-five").fadeIn();

									scope.editSummary = false;
								}else{
									$(".personal-container").hide();
									$("#form-three").fadeIn();
									scope.scrollTo('form-three');
								}
							}else{
								scope.email_taken_error = true;
								console.log(response.data.message);
							}
						});
					}else{
						scope.email_mismatch_error = false;
						scope.email_blank_error = false;
						scope.invalid_nric = true;
						scope.job_error = false;
						scope.email_confirm_mismatch_error = false;
					}

				} 

				scope.stepThreeDone = function(){
					scope.stepThreeClicked = true;
					scope.stepTwoClicked = true;

					var data = {
						customer_buy_start_id : scope.userDetails_data.customer_buy_start_id,
						email : scope.userDetails_data.email,
						password : scope.userDetails_data.password,
					};

					CarePlanSettings.insertCorporateAccount( data )
					.then(function(response){
						console.log(response);
						if( response.data.status == true ){
							scope.email_taken_error = false;
							scope.email_mismatch_error = false;
							scope.email_blank_error = false;
							scope.invalid_nric = false;
							scope.job_error = false;
							scope.email_confirm_mismatch_error = false;
							$("#form-three").hide();
							$("#form-four").fadeIn();
							scope.scrollTo('form-four');
						} else {
							scope.email_taken_error = true;
							scope.email_mismatch_error = false;
							scope.email_blank_error = false;
							scope.invalid_nric = false;
							scope.job_error = false;
							scope.email_confirm_mismatch_error = false;
						}
					});
				}

				scope.stepThreeIndividualDone = function(){
					$("#form-three").hide();
					$("#form-four").fadeIn();
					scope.scrollTo('form-four');
				}

				scope.proceedButton = function(){
					if( scope.terms_agree == true ){
						scope.terms_agree = true;

						$("#form-four").hide();
						$("#form-five").fadeIn();
						scope.scrollTo('form-five');
					}else{
						scope.terms_agree = 'not';
					}
					
				}

				scope.proceedPay = function(){
					carePlanFactory.setCarePlan( scope.userDetails_data );
					$state.go('steps.payment');
				}

				scope.editForm = function(form){
					console.log(form);

					scope.editSummary = true;

					if( form == 'business-info' ){
						$("#form-five").hide();
						$("#form-one").fadeIn();
					}else if( form == 'business-contact' || form == 'business-contact-add'){
						$("#form-five").hide();
						$("#form-two").fadeIn();
					}else if( form == 'personal-details' ){
						$("#form-five").hide();
						$(".personal-container").fadeIn();
					}

					$(".btn-con").hide();
					$(".btn-done").fadeIn();
				}

				scope.datePickerClicked = function(){
					$(".picker").fadeIn();
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
											scope.userDetails_data.customer_buy_start_id = response.data.data.customer_buy_start.customer_buy_start_id;

											if( response.data.data.customer_business_info ){
												// $("#establish-select .select-wrapper input").val( response.data.data.customer_business_info.establishment );
												// $("#form-two").fadeIn();
												// $("#form-three").fadeIn();
												// $("#form-four").fadeIn();
											}

											if( response.data.data.customerbusiness_contact ){
												if( response.data.data.customerbusiness_contact.billing_address == true ){
													scope.address_isBilling = true;
												}else{
													scope.address_isBilling = false;
												}

												if( response.data.data.customerbusiness_contact.billing_contact == true ){
													scope.person_isBilling = false;
												}else{
													scope.person_isBilling = true;
												}
											}
										}
									});
							}
						});
				}

				scope.getBusinesses = function(){
					carePlanFactory.getNatureOfBusiness( )
						.then(function(response){
							scope.nature_of_business_list = response.data;
							$('select').material_select();
						});
				}

				scope.getJobs = function(){
					$('select').material_select();
					carePlanFactory.getJobTitle( )
						.then(function(response){
							scope.job_list = response.data;
							$('select').material_select();
						});
				}


				scope.onLoad = function(){
					$("#payTop-btn").hide();

					scope.getSession();
					scope.getBusinesses();
					scope.getJobs();
					carePlanFactory.setLastRoute('steps.plan');

					$("html, body").animate({
					        scrollTop: 0
					    }, 500);

					scope.userDetails_data = carePlanFactory.getCarePlan();
					console.log(scope.userDetails_data);

					$('select').material_select();
				}

				scope.scrollTo = function( div ){
					$("html, body").animate({
					        scrollTop: $('#'+div).offset().top - 150
					    }, 500);
				};
				
				scope.onLoad();

				var $input = $('.datepicker').pickadate({
				    min : true,
				    format: 'd mmmm yyyy',
				    onSet : function(date){
				    	// scope.grayItem();
				    	$(".picker").css({'display':'none'});
				    	picker.close();
				    },
				    closeOnSelect: true,
					closeOnClear: true,
				  });

			   	var picker = $input.pickadate('picker');
			}
		}
	}
]);
