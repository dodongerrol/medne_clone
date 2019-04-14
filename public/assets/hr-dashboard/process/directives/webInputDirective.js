app.directive('webInputDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("webInputDirective Runnning !");

				scope.customer_data;
				scope.input_data = {
					nric_status : true,
					medical_credit : 0,
					wellness_credit : 0,
				};
				scope.employee_arr = [];
				scope.active_employeeCtr = 0;
				scope.disable_btn = false;
				scope.number_emp_error = false;
				scope.job_list = {};

				scope.range = function(n) {
	        return new Array(n);
		    };

				scope.openSummary = function( ){
					$('.summary-right-button').toggleClass('list-active');
			  	$('.list-of-employee').toggleClass('list-employee-active', 2000);
				}

				scope.toggleNRIC = function( data, opt ){
					if( opt == 'nric' ){
						scope.input_data.nric_status = true;
						scope.input_data.fin_status = false;
					}else{
						scope.input_data.nric_status = false;
						scope.input_data.fin_status = true;
					}

					scope.input_data.nric = "";
				}

				scope.checkEmail = function(email){
				  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				  return regex.test(email);
				}

				scope.checkInputs = function( ){
					if( !scope.input_data.first_name || scope.input_data.first_name == '' ){
						return false;
					}
					if( !scope.input_data.last_name || scope.input_data.last_name == ''){
						return false;
					}
					if( !scope.input_data.nric || scope.input_data.nric == ''){
						return false;
					}
					if( !scope.input_data.dob || scope.input_data.dob == ''){
						return false;
					}
					if( !scope.input_data.email || scope.input_data.email == ''){
						return false;
					}
					if( !scope.input_data.mobile || scope.input_data.mobile == ''){
						return false;
					}
					if( !scope.input_data.job_title || scope.input_data.job_title == ''){
						return false;
					}
					// if( !scope.input_data.medical_credit || scope.input_data.medical_credit < 0 ){
					// 	return false;
					// }
					// if( !scope.input_data.wellness_credit || scope.input_data.wellness_credit < 0 ){
					// 	return false;
					// }
					if( !scope.input_data.plan_start || scope.input_data.plan_start == ''){
						return false;
					}

					return true;
				}

				scope.nextEmployee = function( ){
					console.log( scope.input_data );
					if( scope.checkInputs() == false ){
						scope.inputs_error = true;
						return false;
					}else{
						scope.inputs_error = false;
					}
					console.log(scope.checkEmail(scope.input_data.email));
					if( scope.checkEmail(scope.input_data.email) == false ){
						scope.email_error = true;
						return false;
					}else{
						scope.email_error = false;
					}

					if( scope.input_data.nric_status == true ){
						var checkNRIC = scope.checkNRIC(scope.input_data.nric);
						if( checkNRIC != true ){
							scope.nric_error = true;
							return false;
						}else{
							scope.nric_error = false;
						}
					}

					if( scope.employee_arr[ scope.active_employeeCtr+1 ] ){

						scope.employee_arr[ scope.active_employeeCtr ] = scope.input_data;
						scope.active_employeeCtr++;
						scope.input_data = scope.employee_arr[ scope.active_employeeCtr ];

						if( scope.active_employeeCtr+1 == scope.total_emps ){
							scope.disable_btn = true;
						}

					}else if( scope.active_employeeCtr == scope.employee_arr.length ){

						if( dashboardFactory.getHeadCountStatus() == true ){
							scope.total_emps++;
						}

						if( scope.number_emp == scope.total_emps ){
							scope.disable_btn = true;
							scope.employee_arr.push(scope.input_data);
							swal( 'Success!', 'Maximum count of employees reached. Enroll now or Edit Employee Details.', 'success');
						}else{
							scope.employee_arr.push(scope.input_data);
							scope.number_emp++;
							scope.active_employeeCtr++;
							scope.input_data = [];
							scope.input_data.plan_start = scope.customer_data.plan.plan_start;
							scope.input_data.medical_credit = 0;
							scope.input_data.wellness_credit = 0;
							scope.input_data.nric_status = true;
							scope.nric_error = false;
						}

					}else{
						scope.employee_arr[ scope.active_employeeCtr ] = scope.input_data;
						scope.active_employeeCtr++;
						scope.input_data = [];
						scope.input_data.plan_start = scope.customer_data.plan.plan_start;
						scope.input_data.medical_credit = 0;
						scope.input_data.wellness_credit = 0;
						scope.input_data.nric_status = true;
						scope.nric_error = false;
					}
				}

				scope.prevEmployee = function( ){
					scope.active_employeeCtr--;
					scope.input_data = scope.employee_arr[ scope.active_employeeCtr ];
					scope.disable_btn = false;
				}

				scope.removeEmpArr = function( index ){
					if( scope.active_employeeCtr != 0 ){
						scope.active_employeeCtr--;
					}
					scope.employee_arr.splice( index , 1 );
					scope.input_data = scope.employee_arr[scope.active_employeeCtr];
				}

				scope.editEmpArr = function( index ){
					scope.active_employeeCtr = index;
					scope.input_data = scope.employee_arr[ index ];
				}

				scope.createBtn = function( ){
					swal({
						  title: "Confirm",
						  text: "Please double check all inputs. Proceed?",
						  type: "warning",
						  showCancelButton: true,
						  confirmButtonColor: "#DD6B55",
						  confirmButtonText: "Yes",
						  cancelButtonText: "No",
						  closeOnConfirm: true,
						  customClass: "removeEmp"
						},
						function(isConfirm){
							if(isConfirm){
								if( scope.employee_arr.length == 0 ){
									scope.nextEmployee();
								}
								console.log( scope.checkInputs() );

								if( dashboardFactory.getHeadCountStatus() == true ){
									scope.createNewHeadCountWebInput();
								}else{
									scope.createTempEmployee();
								}
							}
						});
				}

				scope.createTempEmployee = function( ){
					var data = [];
					console.log(scope.employee_arr.length);
					if(scope.employee_arr.length == 0) {
						swal('Ooops!', 'Please input a employee details.', 'error');
						return false;
					}

					$('#create-btn').attr('disabled', true);
					angular.forEach( scope.employee_arr , function(value,key){
						console.log(value);
						data.push({
							// customer_id : scope.customer_data.customer.customer_buy_start_id,
							first_name : value.first_name,
							last_name	: value.last_name,
							nric : value.nric,
							date_of_birth : value.dob,
							work_email : value.email,
							mobile : value.mobile,
							job_title : value.job_title,
							medical_credits : value.medical_credit ? value.medical_credit : 0,
							wellness_credits : value.wellness_credit ? value.wellness_credit : 0,
							plan_start : value.plan_start 
						});
						if( key == (scope.employee_arr.length-1) ){
							hrSettings.insertTempEmployee( { users: data } )
							.then(function(response){
								$('#create-btn').attr('disabled', false);
								if( key == (scope.employee_arr.length-1) ){
									if(response.data.status) {
										localStorage.setItem('method','input');
										$state.go('web-preview');
									} else {
										swal('Ooops!', response.data.message, 'error');
									}
								}
							});
						}
					});

					console.log(data);
				}

				scope.createNewHeadCountWebInput = function( ){
					var temp_arr = [];
					$('#create-btn').attr('disabled', true);
					angular.forEach( scope.employee_arr , function(value,key){
						var arr = {
							// customer_id : scope.customer_data.customer.customer_buy_start_id,
							first_name : value.first_name,
							last_name	: value.last_name,
							nric : value.nric,
							date_of_birth : value.dob,
							work_email : value.email,
							mobile : value.mobile,
							job_title : value.job_title,
							medical_credits : value.medical_credit ? value.medical_credit : 0,
							wellness_credits : value.wellness_credit ? value.wellness_credit : 0,
							start_date : value.plan_start 
						}

						temp_arr.push( arr );

						if( (scope.employee_arr.length-1) == key ){
							var data = {
								plan_start : moment().format('YYYY-MM-DD'),
								duration : scope.progress.active_plans[0].duration,
								data : temp_arr
							}

							hrSettings.newPurchaseInsertFinalEmployee( data )
								.then(function(response){
									$('#create-btn').attr('disabled', false);
									if( response.data.status == true ){
										// dashboardFactory.setActivePlanID( response.data.customer_active_plan_id );
										localStorage.setItem('method','input');
										$state.go('web-preview');
									} else {
										swal('Ooops!', response.data.message, 'error');
									}
								});
						}
					});
				}

				scope.removeTempEmployee = function( id ){
					hrSettings.deleteTempEmployee( id )
						.then(function(response){

						});
				}

				scope.checkNRIC = function(theNric){
		      var nric_pattern = new RegExp('^[stfgSTFG]{1}[0-9]{7}[a-zA-z]{1}$');
		      return nric_pattern.test(theNric);
	      };

		    scope.getTempEmployees = function( ){
					hrSettings.getTempEmployees()
          	.then(function(response){
          		if( response.data.length > 0 ){
	          		scope.active_plan = response.data[0].active_plan;
	          		scope.temp_employees = response.data;
	          		angular.forEach( scope.temp_employees , function(value,key){

	          			if( value.error_logs.error == true ){
	          				scope.hasError = true;
	          			}
	          			scope.active_plan.plan_start = moment(scope.active_plan.plan_start).format('DD/MM/YYYY');
	          		})
          		}
          	});
				}

				scope.getMethod = function( ){
					hrSettings.getMethodType()
          	.then(function(response){
          		// console.log(response);
          		scope.customer_data = response.data.data;
          		scope.customer_data.plan.plan_start = moment(scope.customer_data.plan.plan_start).format('DD/MM/YYYY');
          		scope.input_data.plan_start = scope.customer_data.plan.plan_start;
          	});
				}

				scope.getProgress = function( ){
					hrSettings.getEnrollmentProgress()
						.then(function(response){
							console.log(response);
							scope.progress = response.data.data;
							if( dashboardFactory.getHeadCountStatus() == true ){
								scope.number_emp = 1;
								scope.total_emps = scope.progress.in_progress;
							}else{
								// scope.number_emp = scope.progress.completed + 1;
								scope.number_emp = 1;
								scope.total_emps = scope.progress.in_progress;
							}
						});
				}

				scope.getJobs = function( ){
					hrSettings.getJobTitle()
						.then(function(response){
							scope.job_list = response.data;
						});
				}

				var loading_trap = false;

        scope.toggleLoading = function( ){
					if ( loading_trap == false ) {
						$( ".circle-loader" ).fadeIn();	
						loading_trap = true;
					}else{
						setTimeout(function() {
							$( ".circle-loader" ).fadeOut();
							loading_trap = false;
						},1000)
					}
				}

				scope.showLoading = function( ){
					$( ".circle-loader" ).fadeIn();	
					loading_trap = true;
				}

				scope.hideLoading = function( ){
					setTimeout(function() {
						$( ".circle-loader" ).fadeOut();
						loading_trap = false;
					},1000)
				}

				scope.showGlobalModal = function( message ){
			    $( "#global_modal" ).modal('show');
			    $( "#global_message" ).text(message);
			  }

        scope.onLoad = function( ) {
        	scope.getMethod();
        	scope.getTempEmployees();
        	scope.getProgress();
        	scope.getJobs();

        	$('body').scrollTop(0);

        	scope.toggleLoading();

        	setTimeout(function() {
        		scope.toggleLoading();
        	}, 500);
      		
      		window.onbeforeunload = function(e) {
	      		console.log(e);
		    		return false;
					};
        }

        scope.onLoad();

        var dt = new Date();
				dt.setFullYear(new Date().getFullYear()-18);
        $('.datepicker').datepicker({
			    format: 'mm/dd/yyyy',
			    endDate : dt
			    // startDate: '-18y'
				});

				$('.start-date-datepicker').datepicker({
			    format: 'mm/dd/yyyy',
			    // startDate: '-18y'
				});

				$('.start-date-datepicker').datepicker().on('hide',function(evt){
					// console.log(evt);
					var val = $('.start-date-datepicker').val();
					if( val == "" ){
						$('.start-date-datepicker').datepicker('setDate', scope.customer_data.plan.plan_start);
					}
				})


			}
		}
	}
]);
