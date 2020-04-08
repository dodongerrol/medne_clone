app.directive('webPreviewDirective', [
	'$state',
	'hrSettings',
	'$rootScope',
	'$timeout',
	'$rootScope',
	'dashboardFactory',
	'dependentsSettings',
	function directive($state,hrSettings,$rootScope,$timeout,$rootScope,dashboardFactory,dependentsSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("webPreviewDirective Runnning !");

				scope.spending_account_status = null;
				scope.hasMedicalBalance = localStorage.getItem('hasMedicalEntitlementBalance') == 'true' ? true : false;
				scope.hasWellnessBalance = localStorage.getItem('hasWellnessEntitlementBalance') == 'true' ? true : false;
				scope.temp_employees = [];
				scope.hasError = false;
				scope.table_dependents_ctr = 0;
				scope.customer_data = {};
				scope.selected_edit_details_data = {};
				scope.editEmpAreaCode = null;
				scope.selectedCheckBox	=	[];
				scope.selectAllEmp = false;
				scope.isDeleteActive = false;
				scope.current_enrolled_count = {
					total_dependents_enrolled: 0,
					total_employee_enrolled: 0,
				};
				scope.isSuccessfulEnroll = false;


				scope.enrollAgain	=	function(){
					$state.go('enrollment-options');
				}

				scope.saveTempUser = function () {
					scope.showLoading();
					scope.current_enrolled_count = {
						total_dependents_enrolled: 0,
						total_employee_enrolled: 0,
					};
					angular.forEach(scope.temp_employees, function (value, key) {
						value.loading = true;
						var data = {
							temp_enrollment_id: value.employee.temp_enrollment_id
						}
						dependentsSettings.saveTempEnrollees(data)
							.then(function (response) {
								value.loading = false
								if (response.data.result) {
									if (response.data.result.status == true) {
										value.success = true;
										value.fail = false;
										scope.current_enrolled_count.total_employee_enrolled += response.data.result.total_employee_enrolled;
										scope.current_enrolled_count.total_dependents_enrolled += response.data.result.total_dependents_enrolled;
									}
								} else {
									value.success = false;
									value.fail = true;
								}
								if (key == scope.temp_employees.length - 1) {
									scope.hideLoading();
									// scope.getEnrollTempEmployees();
									scope.isSuccessfulEnroll = true;
								}

							});
					});
				}

				scope.empCheckBoxAll = function(opt){
					if(opt == true){
						scope.isDeleteActive = true;
					}else{
						scope.isDeleteActive = false;
					}
					angular.forEach(scope.temp_employees, function (value, key) {
						value.checkboxSelected = opt;
					});
				}

				scope.empCheckBox =	function(){
					scope.isDeleteActive = false;
					angular.forEach(scope.temp_employees, function (value, key) {
						console.log(value.checkboxSelected)
						if(value.checkboxSelected){
							scope.isDeleteActive = true;
						}
					});
				}

				scope.removeManyEmp = function () {
					swal({
						title: "Cornfirm",
						text: "are you sure you want to delete these employees?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#ff6864",
						confirmButtonText: "Remove",
						cancelButtonText: "No",
						closeOnConfirm: true,
						customClass: "removeEmp"
					},
						function (isConfirm) {
							if (isConfirm) {
								scope.showLoading();
								angular.forEach(scope.temp_employees, function (value, key) {
									if(value.checkboxSelected){
										dependentsSettings.deleteTempEmployees(value.employee.temp_enrollment_id)
										.then(function (response) {
											// console.log(response);
											if ((scope.temp_employees.length - 1) == key) {
												scope.hideLoading();
												scope.selectAllEmp = false;
												scope.isDeleteActive = false;
												scope.getEnrollTempEmployees();
												$(".modal").modal('hide');
											}
										});
									}else{
										if ((scope.temp_employees.length - 1) == key) {
											scope.hideLoading();
											scope.selectAllEmp = false;
											scope.isDeleteActive = false;
											scope.getEnrollTempEmployees();
											$(".modal").modal('hide');
										}
									}
								});
							}
						});
				}
				
				scope.removeTempEmp = function (data) {
					swal({
						title: "Cornfirm",
						text: "are you sure you want to delete this employee?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#ff6864",
						confirmButtonText: "Remove",
						cancelButtonText: "No",
						closeOnConfirm: true,
						customClass: "removeEmp"
					},
						function (isConfirm) {
							if (isConfirm) {
								dependentsSettings.deleteTempEmployees(data.employee.temp_enrollment_id)
									.then(function (response) {
										scope.getEnrollTempEmployees();
										$(".modal").modal('hide');
									});
							}
						});
				}

				scope.checkEmail	=	function(email){
					var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					return regex.test(email);
				}

				scope.updateEnrolleEmp = function (emp) {
					if (!emp.employee.email && !emp.employee.mobile) {
						swal("Error!", "Email Address or Mobile Number is required.", 'error');
						return false;
					}
					if(emp.employee.email){
						if(scope.checkEmail(emp.employee.email) == false){
							swal("Error!", "Email Address is Invalid.", 'error');
							return false;
						}
					}
					if (emp.employee.mobile) {
						if (scope.editEmpAreaCode.getSelectedCountryData().iso2 == 'sg' && emp.employee.mobile.length < 8) {
							swal('Error!', 'Mobile Number for your country code should be 8 digits.', 'error');
							return false;
						}
						if (scope.editEmpAreaCode.getSelectedCountryData().iso2 == 'my' && emp.employee.mobile.length < 10) {
							swal('Error!', 'Mobile Number for your country code should be 10 digits.', 'error');
							return false;
						}
						if (scope.editEmpAreaCode.getSelectedCountryData().iso2 == 'ph' && emp.employee.mobile.length < 9) {
							swal('Error!', 'Mobile Number for your country code should be 9 digits.', 'error');
							return false;
						}
					}

					swal({
						title: "Confirm",
						text: "Are you sure you want to update this employee?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#0392CF",
						confirmButtonText: "Update",
						cancelButtonText: "No",
						closeOnConfirm: true,
						customClass: "updateEmp"
					},
						function (isConfirm) {
							if (isConfirm) {
								var data = {
									temp_enrollment_id: emp.employee.temp_enrollment_id,
									fullname: emp.employee.fullname,
									dob: emp.employee.dob ? moment(emp.employee.dob, 'DD/MM/YYYY').format('DD/MM/YYYY') : null,
									email: emp.employee.email,
									mobile: emp.employee.mobile,
									job_title: emp.employee.job_title,
									medical_credits: parseFloat(emp.employee.credits),
									wellness_credits: parseFloat(emp.employee.wellness_credits),
									plan_start: emp.employee.start_date ? moment(emp.employee.start_date, 'DD/MM/YYYY').format('DD/MM/YYYY') : null,
									postal_code: emp.employee.postal_code,
									mobile_area_code: emp.employee.mobile_area_code
								}
								scope.showLoading();
								dependentsSettings.updateTempEnrollee(data)
									.then(function (response) {
										// console.log(response);
										if (emp.dependents.length > 0) {
											angular.forEach(emp.dependents, function (value, key) {
												var dep_data = {
													dependent_temp_id: value.enrollee.dependent_temp_id,
													fullname: value.enrollee.fullname,
													dob: moment(value.enrollee.dob, 'DD/MM/YYYY').format('YYYY-MM-DD'),
													plan_start: moment(value.enrollee.plan_start, 'DD/MM/YYYY').format('YYYY-MM-DD'),
													relationship: value.enrollee.relationship,
												}
												dependentsSettings.updateTempDependent(dep_data)
													.then(function (response) {
														// console.log(response);
														if (key == (emp.dependents.length - 1)) {
															scope.hideLoading();
															scope.getEnrollTempEmployees();
															$(".modal").modal('hide');
														}
													});
											});
										} else {
											scope.getEnrollTempEmployees();
											$(".modal").modal('hide');
											scope.hideLoading();
										}
									});
							}
						})
				}

				scope.openEditDetailsModal = function (data) {
					scope.selected_edit_details_data = data;
					$("#edit-employee-details").modal('show');
					scope.inititalizeDatepickers();
					if( scope.editEmpAreaCode == null ){
						scope.inititalizeGeoCode();
					}
				}

				scope.inititalizeGeoCode = function () {
					$timeout(function () {
						var settings = {
							separateDialCode: true,
							initialCountry: "SG",
							autoPlaceholder: "off",
							utilsScript: "../assets/hr-dashboard/js/utils.js",
						};

						var input2 = document.querySelector("#area_code2");
						scope.editEmpAreaCode = intlTelInput(input2, settings);
						scope.editEmpAreaCode.setNumber(scope.selected_edit_details_data.employee.format_mobile);
						scope.selected_edit_details_data.employee.mobile = scope.selected_edit_details_data.employee.mobile;
						$("#area_code2").val(scope.selected_edit_details_data.employee.mobile);
						input2.addEventListener("countrychange", function () {
							scope.selected_edit_details_data.employee.mobile_area_code = scope.editEmpAreaCode.getSelectedCountryData().dialCode;
							scope.selected_edit_details_data.employee.mobile_area_code_country = scope.editEmpAreaCode.getSelectedCountryData().iso2;
						});
					}, 300);
				}

				scope.inititalizeDatepickers	=	function(){
					$timeout(function () {
						var dt = new Date();
						dt.setFullYear(new Date().getFullYear() - 18);
						$('.datepicker').datepicker({
							format: 'dd/mm/yyyy',
						});
						$('.start-date-datepicker').datepicker({
							format: 'dd/mm/yyyy',
						});
						$('.start-date-datepicker').datepicker().on('hide', function (evt) {
							var val = $('.start-date-datepicker').val();
							if (val == "") {
								$('.start-date-datepicker').datepicker('setDate', scope.customer_data.plan.plan_start);
							}
						})
						$('.start-date-datepicker-dependent').datepicker({
							format: 'dd/mm/yyyy',
						});
						$('.start-date-datepicker-dependent').datepicker().on('hide', function (evt) {
							var val = $('.start-date-datepicker-dependent').val();
							if (val == "") {
								$('.start-date-datepicker-dependent').datepicker('setDate', scope.customer_data.plan.plan_start);
							}
						})
					}, 300);
				}

				scope.range = function (range) {
					var arr = [];
					for (var i = 0; i < range; i++) {
						arr.push(i + 1);
					}
					return arr;
				}

				scope.backBtn = function(){
					if($state.current.name == "excel-enrollment.web-preview"){
						$state.go('excel-enrollment.upload');
					}else{
						$state.go('web-input');
					}
				}

				scope.getEnrollTempEmployees = function () {
					scope.hasError = false;
					scope.showLoading();
					dependentsSettings.getTempEmployees()
						.then(function (response) {
							console.log(response);
							scope.temp_employees = response.data.data;
							angular.forEach(scope.temp_employees, function (value, key) {
								if (value.dependents.length > scope.table_dependents_ctr) {
									scope.table_dependents_ctr = value.dependents.length;
								}
								if (value.error_logs.error == true) {
									scope.hasError = true;
									value.hasError = true;
								}
								value.success = false;
								value.fail = false;
								value.loading = false;

								angular.forEach(value.dependents, function(dep_value, dep_key){
									if (dep_value.error_logs.error == true) {
										scope.hasError = true;
										value.hasError = true;
									}
								});
							})
							scope.hideLoading();
						});
				}

				scope.getSpendingAccountStatus = function () {
					hrSettings.getSpendingAccountStatus()
						.then(function (response) {
							console.log(response);
							scope.spending_account_status = response.data;
						});
				}

				scope.getMethod = function () {
					hrSettings.getMethodType()
						.then(function (response) {
							// console.log(response);
							scope.customer_data = response.data.data;
							scope.customer_data.plan.plan_start = moment(scope.customer_data.plan.plan_start).format('DD/MM/YYYY');
						});
				}

				scope.getProgress = function () {
					hrSettings.getEnrollmentProgress()
						.then(function (response) {
							scope.progress = response.data.data;
						});
				};
				
				scope.showLoading = function () {
					$(".circle-loader").fadeIn();
				}

				scope.hideLoading = function () {
					setTimeout(function () {
						$(".circle-loader").fadeOut();
					},100)
				}

        scope.onLoad = function( ) {
					scope.getMethod();
					scope.getProgress();
					scope.getSpendingAccountStatus();
        	scope.getEnrollTempEmployees();
        };

        scope.onLoad();
			}
		}
	}
]);
