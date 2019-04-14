app.directive('replaceExternalDirective', [
	'$state',
	'$stateParams',
	'hrSettings',
	'dashboardFactory',
	'dependentsSettings',
	function directive($state,$stateParams,hrSettings,dashboardFactory,dependentsSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("replaceExternalDirective Runnning !");

				scope.vacant = {};
				scope.employee_name = null;
				scope.employee_data = {};
				scope.dependent_data = {};
				scope.nric_status = true;
				scope.fin_status = false;
				scope.number_emp = 0;
				scope.type = null;

				scope.vacant = $stateParams;
				console.log( scope.vacant );

				scope.checkNRIC = function(theNric){
					var nric_pattern = new RegExp('^[stfgSTFG]{1}[0-9]{7}[a-zA-z]{1}$');
					return nric_pattern.test(theNric);
				};

				scope.checkEmail = function(email){
					var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					return regex.test(email);
				}

				scope.checkEmployeeForm = function( ){
					if( !scope.employee_data.first_name ){
						swal( 'Error!', 'First Name is required.', 'error' );
						return false;
					}
					if( !scope.employee_data.last_name ){
						swal( 'Error!', 'Last Name is required.', 'error' );
						return false;
					}
					if( !scope.employee_data.nric ){
						swal( 'Error!', 'NRIC is required.', 'error' );
						return false;
					}else{
						if( scope.nric_status == true ){
							var checkNRIC = scope.checkNRIC(scope.employee_data.nric);
							if( checkNRIC != true ){
								swal( 'Error!', 'Invalid NRIC.', 'error' );
								return false;
							}
						}	
					}
					if( !scope.employee_data.dob ){
						swal( 'Error!', 'Date of Birth is required.', 'error' );
						return false;
					}
					if( !scope.employee_data.email ){
						swal( 'Error!', 'Email is required.', 'error' );
						return false;
					}else{
						if( scope.checkEmail(scope.employee_data.email) == false ){
							swal( 'Error!', 'Email is invalid.', 'error' );
							return false;
						}
					}
					if( !scope.employee_data.mobile ){
						swal( 'Error!', 'Phone is required.', 'error' );
						return false;
					}
					if( !scope.employee_data.postal_code ){
						swal( 'Error!', 'Postal Code is required.', 'error' );
						return false;
					}
					if( !scope.employee_data.plan_start ){
						swal( 'Error!', 'Start Date is required.', 'error' );
						return false;
					}

					return true;
				}

				scope.checkDependentForm = function( ){
					if( !scope.dependent_data.first_name ){
						swal( 'Error!', 'First Name is required.', 'error' );
						return false;
					}

					if( !scope.dependent_data.last_name ){
						swal( 'Error!', 'Last Name is required.', 'error' );
						return false;
					}

					if( !scope.dependent_data.nric ){
						swal( 'Error!', 'NRIC is required.', 'error' );
						return false;
					}else{
						if( scope.nric_status == true ){
							var checkNRIC = scope.checkNRIC(scope.dependent_data.nric);
							if( checkNRIC != true ){
								swal( 'Error!', 'Invalid NRIC.', 'error' );
								return false;
							}
						}	
					}
					if( !scope.dependent_data.dob ){
						swal( 'Error!', 'Date of Birth is required.', 'error' );
						return false;
					}

					if( !scope.dependent_data.relationship ){
						swal( 'Error!', 'Relationship Type is required.', 'error' );
						return false;
					}

					if( !scope.dependent_data.start_date ){
						swal( 'Error!', 'Start Date is required.', 'error' );
						return false;
					}

					return true;
				}

				scope.enrollEmployee = function(){
					console.log( scope.employee_data );
					console.log( $stateParams.empID );
					if( scope.checkEmployeeForm() == true ){
						scope.employee_data.employee_replacement_seat_id = $stateParams.vacant_id;
						dependentsSettings.enrollReplaceEmployee( scope.employee_data )
						.then(function(response){
							console.log(response);
							if( response.data.status ){
								// swal('Success!', response.data.message, 'success');
								swal({
								  title: "Success!",
								  text: response.data.message,
								  type: "success",
								  showCancelButton: false,
								  confirmButtonClass: "btn-primary",
								  confirmButtonText: "Go To Benefits Dashboard!",
								  closeOnConfirm: true
								},
								function(){
								  $state.go('benefits-dashboard');
								});
								$
							}else{
								swal('Error!', response.data.message, 'error');
							}
						});
					}
				}

				scope.enrollDependent = function(){
					console.log( scope.employee_data );
					console.log( $stateParams.empID );
					if( scope.checkDependentForm() == true ){
						scope.dependent_data.dependent_replacement_seat_id = $stateParams.vacant_id;
						dependentsSettings.enrollVacantDependent( scope.dependent_data )
						.then(function(response){
							console.log(response);
							if( response.data.status ){
								swal({
								  title: "Success!",
								  text: response.data.message,
								  type: "success",
								  showCancelButton: false,
								  confirmButtonClass: "btn-primary",
								  confirmButtonText: "Go To Benefits Dashboard!",
								  closeOnConfirm: true
								},
								function(){
								  $state.go('benefits-dashboard');
								});
								$
							}else{
								swal('Error!', response.data.message, 'error');
							}
						});
					}
				}

				scope.checkVacantSeat = function( ) {
					// console.log($stateParams);
					if($stateParams.type == "employee") {
						hrSettings.getEmployeeVacantStatus($stateParams.vacant_id)
						.then(function(response){
							// console.log(response);
							if(response.data.status) {
								scope.type = "employee";
								console.log(scope.type);
							} else {
								// swal('Oooops!', response.data.message, 'error');
								swal({
								  title: "Oooops!",
								  text: response.data.message,
								  type: "error",
								  showCancelButton: false,
								  confirmButtonClass: "btn-danger",
								  confirmButtonText: "Go Back!",
								  closeOnConfirm: true
								},
								function(){
								  $state.go('benefits-dashboard');
								});
							}
						});
					} else if($stateParams.type == "dependent") {
						dependentsSettings.getDependentVacantStatus($stateParams.vacant_id)
						.then(function(response){
							// console.log(response);
							if(response.data.status) {
								scope.type = "dependent";
								scope.employee_name = response.data.employee;
								console.log(scope.type);
							} else {
								// swal('Oooops!', response.data.message, 'error');
								swal({
								  title: "Oooops!",
								  text: response.data.message,
								  type: "error",
								  showCancelButton: false,
								  confirmButtonClass: "btn-danger",
								  confirmButtonText: "Go Back!",
								  closeOnConfirm: true
								},
								function(){
								  $state.go('benefits-dashboard');
								});
							}
						});
					}
				}

				scope.onLoad = function( ){
					scope.checkVacantSeat();
					// scope.getMethod();
					// scope.getProgress();
				}

				scope.onLoad();


				var dt = new Date();
				dt.setFullYear(new Date().getFullYear()-18);
				$('.datepicker').datepicker({
					format: 'mm/dd/yyyy',
					endDate : dt
				});

				$('.start-date-datepicker').datepicker({
					format: 'mm/dd/yyyy',
				});

				$('.start-date-datepicker').datepicker().on('hide',function(evt){
					// console.log(evt);
					var val = $('.start-date-datepicker').val();
					if( val == "" ){
						// $('.start-date-datepicker').datepicker('setDate', scope.customer_data.plan.plan_start);
					}
				})


			}
		}
	}
	]);
