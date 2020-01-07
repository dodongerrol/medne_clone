app.directive('webPreviewDirective', [
	'$state',
	'hrSettings',
	'$rootScope',
	'$timeout',
	'$rootScope',
	'dashboardFactory',
	function directive($state,hrSettings,$rootScope,$timeout,$rootScope,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("webPreviewDirective Runnning !");

				scope.temp_employees = [];
				scope.active_plan = [];
				scope.hasError = false;
				scope.enrolling_active = false;
				scope.temp_employees_ids = [];

				var allow = false;

				scope.$on( 'tempEmployeesRefresh', function( evt, data ){
					scope.onLoad();
		    });

		    scope.enrollButton = function( ){
		    	if( scope.hasError == false ){
		    		if( scope.progress.added_purchase_status == true ){
		    			localStorage.setItem('method','excel');
			    		$state.go('payment-method');
			    	}else{
			    		scope.submitEmployees();
			    	}
		    	}
		    }

				scope.submitEmployees = function( ){

					if(scope.temp_employees.length > scope.progress.in_progress) {
						swal("Oooops!", "Please enroll " + scope.progress.in_progress + " employee(s) only. Your are trying to enroll a total of " + scope.temp_employees.length + " employees.", "error");
						return false;
					}

					scope.enrolling_active = true;
					angular.forEach( scope.temp_employees , function( value, key ){

						scope.temp_employees[key].sending = true;
						scope.temp_employees[key].done = false;

						hrSettings.insertFinalEmployee( { temp_enrollment_id : value.enrollee.temp_enrollment_id } )
							.then(function(response){
								// console.log(response);
								scope.temp_employees[key].sending = false;
								scope.temp_employees[key].done = true;

								if( key == (scope.temp_employees.length-1) ){
									dashboardFactory.setEnrolledEmp( scope.temp_employees.length );
									scope.enrolling_active = false;
									$.toast({ 
									  text : "Employees are successfully enrolled !", 
									  bgColor : '#1487b3',              
									  textColor : '#fff',            
									  allowToastClose : true,       
									  hideAfter : 5000,              
									  stack : 5,                     
									  textAlign : 'left',            
									  position : 'bottom-right'       
									})

									$timeout(function() {
										localStorage.setItem('method','input');
										$state.go('web-successful');
									}, 2000);
								}
							});
					});
				};

				scope.editEmployee = function( emp ) {
					$rootScope.$broadcast('editDetailsInitialized', { modal : 'edit-employee-details' , data : emp });
				};

		    scope.getTempEmployees = function( ) {
			    	console.log('calling');
			    	scope.temp_employees_ids = [];
						hrSettings.getTempEmployees()
		          	.then(function(response){
		          		console.log(response);
		          		if( response.data.length > 0 ){
			          		// scope.active_plan = response.data[0].active_plan;
			          		scope.temp_employees = response.data;
			          		angular.forEach( scope.temp_employees , function(value,key){

			          			scope.temp_employees_ids.push( value.enrollee.temp_enrollment_id );

			          			if( value.error_logs.error == true ){
			          				scope.hasError = true;
			          			}

			          			// if( scope.temp_employees[key].active_plan != null ){
			          				scope.temp_employees[key].start_date = scope.temp_employees[key].enrollee.start_date;
			          				scope.temp_employees[key].dob = moment(scope.temp_employees[key].enrollee.dob).format('MM/DD/YYYY');
			          			// }
			          		})
		          		} else {
		          			scope.temp_employees = {};
		          		}
		          		// else{
		          		// 	$state.go("enrollment-method");
		          		// }
		          	});
				};

				scope.removeTempEmps = function( ) {
						swal({
						  title: "Confirm",
						  text: "Enrollees will be deleted. Proceed?",
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
								allow = true;

								scope.toggleLoading();

								var data = {
									ids : scope.temp_employees_ids
								}

								hrSettings.deleteTempEmployees( data )
									.then(function(response){
										// console.log(response);
										scope.toggleLoading();
										if( response.data.status == true ){
											$.toast({ 
											  text : "Successfully removed enrollees.", 
											  bgColor : '#1487b3',              
											  textColor : '#fff',            
											  allowToastClose : true,       
											  hideAfter : 5000,              
											  stack : 5,                     
											  textAlign : 'left',            
											  position : 'bottom-right'       
											})

											$state.go( 'benefits-dashboard' );
										}else{
											$.toast({ 
											  text : "Something went wrong.", 
											  bgColor : '#1487b3',              
											  textColor : '#fff',            
											  allowToastClose : true,       
											  hideAfter : 5000,              
											  stack : 5,                     
											  textAlign : 'left',            
											  position : 'bottom-right'       
											})
										}
									});
							}
						});
						
				}

				scope.getProgress = function( ) {
						$('.loader-container').fadeIn();
						hrSettings.getEnrollmentProgress()
							.then(function(response){
								scope.progress = response.data.data;
								// console.log(scope.progress);
								$('.loader-table').hide();
							});
        };

      	var loading_trap = false;

	    	scope.toggleLoading = function( ){
					if ( loading_trap == false ) {
						$( ".circle-loader" ).fadeIn();	
						loading_trap = true;
					}else{
						setTimeout(function() {
							$( ".circle-loader" ).fadeOut();
							loading_trap = false;
						},100)
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
					},100)
				}

				scope.showGlobalModal = function( message ){
			    $( "#global_modal" ).modal('show');
			    $( "#global_message" ).text(message);
			  }

        scope.onLoad = function( ) {
        	scope.hasError = false;
        	scope.getTempEmployees();
        	scope.getProgress();
        	$('body').scrollTop(0);

        	scope.enrolled_count = dashboardFactory.getEnrolledEmp( );
        };

        scope.onLoad();

        

        $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams) {
			    // console.log(event);
			    

			    if( fromState.name == 'web-preview' && toState.name != 'web-successful' && toState.name != 'payment-method' ){
			    	if( scope.temp_employees_ids.length > 0 ){
				    	if( allow == false ){
				    		event.preventDefault();

				    		scope.removeTempEmps();
				    	}
			    	}
			    }
			    
			  });

			}
		}
	}
]);
