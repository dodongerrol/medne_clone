	app.directive('editDetailsDirective', [
	'$state',
	'hrSettings',
	'$rootScope',
	function directive($state,hrSettings,$rootScope) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("editDetailsDirective Runnning !");

				scope.selected_employee = [];
				scope.job_list = {};

				scope.$on( 'editDetailsInitialized', function( evt, data )  {
			      if( data.modal == 'edit-employee-details' ){
			      	scope.selected_employee = data.data;
			      }

			      if( data.modal == 'account-billing-edit-business-info' ){
			      	scope.business_info = data.data;
			      }

			      if( data.modal == 'account-billing-edit-business-contact' ){
			      	scope.business_contact = data.data;
			      }

			      if( data.modal == 'account-billing-edit-billing-contact-and-address' ){
			      	scope.billing_contact = data.data;
			      }

			      if( data.modal == 'account-billing-edit-billing-contact-and-address2' ){
			      	scope.billing_address = data.data;
			      	console.log(scope.billing_address);
			      }

			      if( data.modal == 'account-billing-edit-payment-information' ){
			      	scope.payment = data.data;
			      	if( scope.payment.payment_method.cheque == true || scope.payment.payment_method.cheque == 'true' ){
			      		scope.payment.payment_method.method = 'Cheque';
			      	}
			      	if( scope.payment.payment_method.credit_card == true || scope.payment.payment_method.credit_card == 'true' ){
			      		scope.payment.payment_method.method = 'Credit Card';
			      	}
			      }

			      if( data.modal == 'account-billing-edit-payment-information-details' ){
			      	scope.payment = data.data;
			      }

			      if( data.modal == 'account-billing-edit-password' ){
			      	scope.billing_contact = data.data;
			      }

			    });

					scope.updatePasswordSubmit = function( pass ) {
						console.log( pass );
						if( pass.new_password == pass.re_password ){

				    	swal({
							  title: "Confirm",
							  text: "Are you sure you want to UPDATE your account password?",
							  type: "warning",
							  showCancelButton: true,
							  confirmButtonColor: "#DD6B55",
							  confirmButtonText: "Update",
							  cancelButtonText: "No",
							  closeOnConfirm: true,
							  customClass: "updateEmp"
							},
							function(isConfirm){
								if(isConfirm){
									$('#update-btn-pass').attr('disabled', true);
									$('#update-btn-pass').text('UPDATING...');
									var data = {
											current_password : pass.current_password,
											new_password : pass.new_password
										}
									// }

									console.log(data);

									hrSettings.updateHrPassword( data )
									.then(function(response){
										console.log(response);
										$('#update-btn-pass').attr('disabled', false);
										$('#update-btn-pass').text('UPDATE');
										if(response.data.status) {
											swal('Success!', response.data.message, 'success');
											$('#account-billing-edit-password-modal').modal('hide');
											$('body').css({'overflow-y': 'auto'});
											$rootScope.$broadcast('informationRefresh');
										} else {
											swal('Ooops!', response.data.message, 'error');
										}
									});

								}
							});

						}else{
							swal({
							  title: "Alert",
							  text: "Passwords do not match.",
							  type: "warning",
							  showCancelButton: false,
							  confirmButtonColor: "#DD6B55",
							},
							function(isConfirm){

							});
						}
			    }

			    scope.updatePaymentAddress = function( ) {

			    	swal({
						  title: "Confirm",
						  text: "Are you sure you want to UPDATE your billing address?",
						  type: "warning",
						  showCancelButton: true,
						  confirmButtonColor: "#DD6B55",
						  confirmButtonText: "Update",
						  cancelButtonText: "No",
						  closeOnConfirm: true,
						  customClass: "updateEmp"
						},
						function(isConfirm){
							if(isConfirm){
								var data;

								if( scope.payment.contacts.billing_contact_status == true ){
									data = {
										billing_contact_status  : true ,
										company_name : scope.payment.contacts.billing_address.company_name,
										billing_address	: scope.payment.contacts.billing_address.billing_address,
										postal	: scope.payment.contacts.billing_address.postal,
									}
								}else{

									data = {
										billing_contact_status  : false ,
										company_name : scope.payment.contacts.billing_address.company_name,
										billing_address	: scope.payment.contacts.billing_address.billing_address,
										postal	: scope.payment.contacts.billing_address.postal,
									}
								}


								hrSettings.updateBillingAddress( data )
									.then(function(response){
										if( response.data.status ){
											swal('Success!', response.data.message, 'success');
											$('#account-billing-edit-payment-information-details-modal').modal('hide');
											$rootScope.$broadcast('informationRefresh');
										}else{
											swal('Error!', response.data.message, 'error');
										}
									});

							}
						});
			    }

			    scope.updatePaymentMethod = function( ) {
			    	swal({
						  title: "Confirm",
						  text: "Are you sure you want to UPDATE your payment method?",
						  type: "warning",
						  showCancelButton: true,
						  confirmButtonColor: "#DD6B55",
						  confirmButtonText: "Update",
						  cancelButtonText: "No",
						  closeOnConfirm: true,
						  customClass: "updateEmp"
						},
						function(isConfirm){
							if(isConfirm){
								var data = {
									payment_method : (scope.payment.payment_method.method).toLowerCase()
								}

								console.log(data);
								hrSettings.updatePaymentMethod( data )
									.then(function(response){
										if( response.data.status ){
											swal('Success!', response.data.message, 'success');
										}else{
											$('#account-billing-edit-payment-information-modal').modal('hide');
											$rootScope.$broadcast('informationRefresh');
										}
									});

							}
						});
			    }

			    scope.updateBillingAddress = function( ) {
			    	if( !scope.billing_address.business_information.company_name ){
			    		swal('Error!', 'Company Name is required.', 'error');
			    		return false;
			    	}
			    	if( !scope.billing_address.billing_contact.billing_address ){
			    		swal('Error!', 'Billing Address is required.', 'error');
			    		return false;
			    	}
			    	if( !scope.billing_address.billing_contact.postal ){
			    		swal('Error!', 'Postal Code is required.', 'error');
			    		return false;
			    	}

			    	swal({
						  title: "Confirm",
						  text: "Are you sure you want to UPDATE your billing address?",
						  type: "warning",
						  showCancelButton: true,
						  confirmButtonColor: "#DD6B55",
						  confirmButtonText: "Update",
						  cancelButtonText: "No",
						  closeOnConfirm: true,
						  customClass: "updateEmp"
						},
						function(isConfirm){
							if(isConfirm){
								var data;

								// if( scope.billing_address.billing_contact_status == true ){
									data = {
										// billing_contact_status  : true ,
										company_name : scope.billing_address.business_information.company_name,
										billing_address	: scope.billing_address.billing_contact.billing_address,
										postal	: scope.billing_address.billing_contact.postal,
									}
								// }else{

								// 	data = {
								// 		billing_contact_status  : false ,
								// 		company_name : scope.billing_address.billing_address.company_name,
								// 		billing_address	: scope.billing_address.billing_address.billing_address,
								// 		postal	: scope.billing_address.billing_address.postal,
								// 	}
								// }


								hrSettings.updateBillingAddress( data )
									.then(function(response){
										if( response.data.status ){
											swal('Success!', response.data.message, 'success');
											$('#account-billing-edit-billing-contact-and-address-modal-2').modal('hide');
											$rootScope.$broadcast('informationRefresh');
										}else{
											swal('Error!', response.data.message, 'error');
										}
									});

							}
						});
			    }

			    scope.updateBillingContact = function( ) {
			    	if( !scope.billing_contact.billing_contact.first_name ){
			    		swal('Error!', 'First Name is required.', 'error');
			    		return false;
			    	}
			    	if( !scope.billing_contact.billing_contact.last_name ){
			    		swal('Error!', 'Last Name is required.', 'error');
			    		return false;
			    	}
			    	if( !scope.billing_contact.billing_contact.work_email ){
			    		swal('Error!', 'Email is required.', 'error');
			    		return false;
			    	}
			    	swal({
						  title: "Confirm",
						  text: "Are you sure you want to UPDATE your billing contact?",
						  type: "warning",
						  showCancelButton: true,
						  confirmButtonColor: "#DD6B55",
						  confirmButtonText: "Update",
						  cancelButtonText: "No",
						  closeOnConfirm: true,
						  customClass: "updateEmp"
						},
						function(isConfirm){
							if(isConfirm){
								var data;

								// if( scope.billing_contact.billing_contact_status == true ){
									data = {
										// billing_contact_status  : true ,
										customer_billing_contact_id: scope.billing_contact.billing_contact.customer_billing_contact_id,
										first_name : scope.billing_contact.billing_contact.first_name,
										last_name	: scope.billing_contact.billing_contact.last_name,
										work_email	: scope.billing_contact.billing_contact.work_email,
									}
								// }else{

								// 	data = {
								// 		billing_contact_status  : false ,
								// 		first_name : scope.billing_contact.business_contact.first_name,
								// 		last_name	: scope.billing_contact.business_contact.last_name,
								// 		job_title	: scope.billing_contact.business_contact.job_title,
								// 		work_email	: scope.billing_contact.business_contact.work_email,
								// 		phone	: scope.billing_contact.business_contact.phone,
								// 	}
								// }


								hrSettings.updateBillingContact( data )
									.then(function(response){
										if( response.data.status ){
											swal('Success!', response.data.message, 'success');
											$('#account-billing-edit-billing-contact-and-address-modal').modal('hide');
											$rootScope.$broadcast('informationRefresh');
										}else{
											swal('Error!', response.data.message, 'error');
										}
									});

							}
						});
			    }

			    scope.updateContacts = function( ) {
			    	if( !scope.business_contact.first_name ){
			    		swal('Error!', 'First Name is required.', 'error');
			    		return false;
			    	}
			    	if( !scope.business_contact.last_name ){
			    		swal('Error!', 'Last Name is required.', 'error');
			    		return false;
			    	}
			    	if( !scope.business_contact.work_email ){
			    		swal('Error!', 'Email is required.', 'error');
			    		return false;
			    	}
			    	if( !scope.business_contact.phone ){
			    		swal('Error!', 'Phone is required.', 'error');
			    		return false;
			    	}

			    	swal({
						  title: "Confirm",
						  text: "Are you sure you want to UPDATE your business contact?",
						  type: "warning",
						  showCancelButton: true,
						  confirmButtonColor: "#DD6B55",
						  confirmButtonText: "Update",
						  cancelButtonText: "No",
						  closeOnConfirm: true,
						  customClass: "updateEmp"
						},
						function(isConfirm){
							if(isConfirm){
								var data = {
									customer_business_contact_id  : scope.business_contact.customer_business_contact_id ,
									first_name : scope.business_contact.first_name,
									last_name	: scope.business_contact.last_name,
									job_title	: scope.business_contact.job_title,
									work_email	: scope.business_contact.work_email,
									phone	: scope.business_contact.phone,
								}

								hrSettings.updateBusinessContact( data )
									.then(function(response){
										if( response.data.status ){
											swal('Success!', response.data.message, 'success');
											$('#account-billing-edit-business-contact-modal').modal('hide');
											$rootScope.$broadcast('informationRefresh');
										}else{
											swal('Error!', response.data.message, 'error');
										}
									});
							}
						});
			    }

			    scope.updateInfos = function( ) {
			    	if( !scope.business_info.company_address ){
			    		swal('Error!', 'Address is required.', 'error');
			    		return false;
			    	}
			    	if( !scope.business_info.postal_code ){
			    		swal('Error!', 'Postal Code is required.', 'error');
			    		return false;
			    	}

			    	swal({
						  title: "Confirm",
						  text: "Are you sure you want to UPDATE your business Information?",
						  type: "warning",
						  showCancelButton: true,
						  confirmButtonColor: "#DD6B55",
						  confirmButtonText: "Update",
						  cancelButtonText: "No",
						  closeOnConfirm: true,
						  customClass: "updateEmp"
						},
						function(isConfirm){
							if(isConfirm){
								var data = {
									customer_business_information_id : scope.business_info.customer_business_information_id,
									address : scope.business_info.company_address,
									postal	: scope.business_info.postal_code,
								}

								hrSettings.updateBusinessInfo( data )
									.then(function(response){
										if( response.data.status ){
											swal('Success!', response.data.message, 'success');
											$('#account-billing-edit-business-info-modal').modal('hide');
											$rootScope.$broadcast('informationRefresh');
										}else{
											swal('Error!', response.data.message, 'error');
										}
									});

							}
						});
			    }

			    scope.updateEmployee = function( emp ) {
			    	console.log(emp);
			    	swal({
						  title: "Confirm",
						  text: "Are you sure you want to update this employee?",
						  type: "warning",
						  showCancelButton: true,
						  confirmButtonColor: "#DD6B55",
						  confirmButtonText: "Update",
						  cancelButtonText: "No",
						  closeOnConfirm: true,
						  customClass: "updateEmp"
						},
						function(isConfirm){
							if(isConfirm){
								var data = {
									customer_id : emp.enrollee.customer_buy_start_id,
									first_name : emp.enrollee.first_name,
									last_name	: emp.enrollee.last_name,
									nric : emp.enrollee.nric,
									dob : emp.enrollee.dob,
									email : emp.enrollee.email,
									mobile : emp.enrollee.mobile,
									job_title : emp.enrollee.job_title,
									temp_enrollment_id : emp.enrollee.temp_enrollment_id,
									credits : parseFloat(emp.enrollee.credits),
									wellness_credits: parseFloat(emp.enrollee.wellness_credits),
									start_date : moment(emp.start_date, "DD/MM/YYYY").format('YYYY-MM-DD')
								}
								console.log(data);
								hrSettings.updateTempEmployee( data )
									.then(function(response){
										console.log(response);
										if( response.data.status ){
											swal('Success!', response.data.message, 'success');
											$('#edit-employee-details').modal('hide');
											$rootScope.$broadcast('tempEmployeesRefresh');
										}else{
											swal('Error!', response.data.message, 'error');
										}
									});

							}
						});

			    };

			    scope.removeEmployee = function( ) {
			    	swal({
						  title: "Hold on a sec",
						  text: "Are you sure you want to remove this employee?",
						  type: "warning",
						  showCancelButton: true,
						  confirmButtonColor: "#DD6B55",
						  confirmButtonText: "Delete",
						  cancelButtonText: "No",
						  closeOnConfirm: true,
						  customClass: "removeEmp"
						},
						function(isConfirm){
							if(isConfirm){

								hrSettings.deleteTempEmployee( scope.selected_employee.enrollee.temp_enrollment_id )
									.then(function(response){
										if( response.data.status ){
											swal('Success!', response.data.message, 'success');
											$('#edit-employee-details').modal('hide');
											$rootScope.$broadcast('tempEmployeesRefresh');
										}else{
											swal('Error!', response.data.message, 'error');
										}
									});
							}
						});
			    };

			    scope.getJobs = function( ) {
						hrSettings.getJobTitle()
							.then(function(response){
								scope.job_list = response.data;
							});
					}

					scope.showGlobalModal = function( message ){
				    $( "#global_modal" ).modal('show');
				    $( "#global_message" ).text(message);
				  }

	        scope.onLoad = function( ) {
	        	scope.getJobs();
	        };

	        scope.onLoad();

	        $('.start-date-datepicker').datepicker({
				    format: 'dd/mm/yyyy',
				    // startDate: '-18y'
					});

					$('.start-date-datepicker').datepicker().on('hide',function(evt){
						console.log(scope.selected_employee);
						// if( scope.selected_employee.start_date == null || scope.selected_employee.start_date == "" ){
							$('.start-date-datepicker').datepicker('setDate', scope.selected_employee.start_date);
						// }
					})
			}
		}
	}
]);
