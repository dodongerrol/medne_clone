app.directive('benefitsTiersDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	'$timeout',
	'dependentsSettings',
	'$compile',
	'$window',
	function directive($state, hrSettings, dashboardFactory, $timeout, dependentsSettings, $compile, $window) {
		return {
			restrict: "A",
			scope: true,
			link: function link(scope, element, attributeSet) {
				console.log("benefitsTiersDirective Runnning !");

				scope.alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
				scope.tier_arr = [];
				scope.tier_arr_currency_type;
				scope.dependent_arr = [];
				scope.employee_arr = [];
				scope.temp_employees = [];
				scope.previewTable_arr = [];
				scope.tier_data = {
					gp_cap_status: false,
				};
				scope.reviewExcelData = {
					format: false,
					name: false,
					dob: false,
					email: false,
					postcode: false,
					relationship: false,
					plan_start: false,
				}
				scope.dependent_data = {};
				scope.added_dependent_data = {};
				scope.showCurrencyType = localStorage.getItem("currency_type");
				scope.employee_data = {
					medical_credits: 0,
					wellness_credits: 0,
					dependents: [],
					mobile_area_code: scope.showCurrencyType == 'myr' ? '60' : '65' ,
					mobile_area_code_country: scope.showCurrencyType
				};
				scope.upload_file_dependent = null;
				scope.customer_data = null;
				scope.selected_edit_tier_index = null;
				scope.tierSelected = null;
				scope.downloadWithDependents = null;
				scope.active_employee_index = 0;
				scope.lastContentShown = 0;
				scope.table_dependents_ctr = 0;
				scope.employee_enroll_count = 0;
				scope.dependents_enroll_count = 0;
				scope.download_step = 1;
				scope.employee_ctr = 0;
				scope.dependent_ctr = 0;
				scope.added_dependent_ctr = 0;
				scope.selected_emp_dep_tab = 1;
				scope.isTiering = false;
				scope.nric_status = true;
				scope.fin_status = false;
				scope.isNextBtnDisabled = true;
				scope.isBackBtnDisabled = false;
				scope.isEditActive = false;
				scope.nric_status_dependents = true;
				scope.fin_status_dependents = false;
				scope.isTierLoaderShow = true;
				scope.isTierBtn = false;
				scope.isTierInput = false;
				scope.isTierSummary = false;
				scope.isExcelSelected = false;
				scope.isWebInputSelected = false;
				scope.isEnrollmentOptions = false;
				scope.isWebInput = false;
				scope.isAddDependentsShow = false;
				scope.isDependentListShow = false;
				scope.isExcel = false;
				scope.isDownloadTemplate = false;
				scope.downloadWithDependentsCheckbox = false;
				scope.isUploadFile = false;
				scope.isReviewEnroll = false;
				scope.isSuccessfulEnroll = false;
				scope.isFromUpload = false;
				scope.isAllPreviewEmpChecked = false;
				scope.showDependentsAdded = false;
				scope.isEditDetailModalOpen = false;
				
				scope.spending_account_status = {}

				scope.isCommunicationShow = false;

				scope.communication_send = {
					type: 'immediate',
					date: null,
				};
				scope.isActivationInfoDropShow = false;

				console.log(scope.showCurrencyType);

				var iti = null;
				var iti2 = null;


				scope.getLetter = function (index) {

					return scope.alphabet[index];
				};

				scope.selectEmpDepTab = function (opt) {
					scope.selected_emp_dep_tab = opt;
					if (opt == 1) {
						scope.isWebInput = true;
						scope.inititalizeGeoCode();
						scope.showDependentsAdded = false;
						scope.isDependentListShow = false;
						scope.inititalizeDatepicker();
					} else {
						scope.isWebInput = false;
						scope.showDependentsAdded = true;
						scope.isDependentListShow = true;
						scope.added_dependent_ctr = 0;
						scope.added_dependent_data = scope.employee_data.dependents[scope.added_dependent_ctr];
					}
				}

				scope.toggleGPcapStatus = function (opt) {

					scope.tier_data.gp_cap_status = opt;
				}

				scope.nextBtn = function () {
					// if (scope.isTierBtn == true) {
					// 	scope.isTierBtn = false;
					// 	scope.isTierInput = true;
					// } else if (scope.isTierInput == true) {

					// } else if (scope.isTierSummary == true) {
					// 	scope.isTierSummary = false;
					// 	scope.isNextBtnDisabled = true;
					// 	scope.isEnrollmentOptions = true;
					// 	scope.isExcelSelected = false;
					// 	scope.isWebInputSelected = false;
					// } 
					if (scope.isEnrollmentOptions == true) {
						scope.isEnrollmentOptions = false;
						if (scope.isExcelSelected == true) {
							//excel input
							scope.isExcel = true;
						} else {
							//web input
							scope.employee_data.hasMedicalBalance = localStorage.getItem('hasMedicalEntitlementBalance') == 'true' ? true : false;
							scope.employee_data.hasWellnessBalance = localStorage.getItem('hasWellnessEntitlementBalance') == 'true' ? true : false;
							console.log(scope.employee_data);
							scope.isWebInput = true;
							scope.isFromUpload = false;
							scope.inititalizeDatepicker();
							$('.summary-right-container').show();
							scope.employee_data.dependents = [];
							scope.inititalizeGeoCode();
						}
					} else if (scope.isExcel == true) {
						if (scope.downloadWithDependents != null) {
							scope.isExcel = false;
							scope.downloadWithDependentsCheckbox = true;
							scope.download_step = 2;
						} else {
							swal('Error!', 'Please select an option for you template.', 'error');
						}
					} else if (scope.downloadWithDependentsCheckbox == true) { 
						//scope.reviewExcelData.name slide 21
						// scope.reviewExcelData.email &&
						//  && scope.reviewExcelData.postcode
						if (scope.reviewExcelData.format && scope.reviewExcelData.dob && scope.reviewExcelData.plan_start) {
							if (scope.downloadWithDependents == true) {
								if (scope.reviewExcelData.relationship) {
									scope.downloadWithDependentsCheckbox = false;
									scope.isUploadFile = true;
									scope.download_step = 3;
								} else {
									swal('Error!', 'please review your downloaded file and check the boxes.', 'error');
								}
							} else {
								scope.downloadWithDependentsCheckbox = false;
								scope.isUploadFile = true;
								scope.download_step = 3;
							}
						} else {
							swal('Error!', 'please review your downloaded file and check the boxes.', 'error');
						}

					} else if (scope.isUploadFile == true) {
						console.log(scope.upload_file_dependent);
						if (scope.uploadedFile == false) {
							swal('Error!', 'please upload a file first.', 'error');
						} else {
							scope.message = '';
							scope.uploadedFile = false;
							scope.isUploadFile = false;
							scope.getEnrollTempEmployees();
							scope.isReviewEnroll = true;
							scope.isFromUpload = true;
							scope.download_step = 4;
						}
					}
				}

				scope.backBtn = function () {
					scope.isEditActive = false;
					// if (scope.isTierBtn == true) {
					// 	$state.go('enrollment-options');
					// 	// $state.go('benefits-dashboard');
					// } else if (scope.isTierInput == true) {
					// 	scope.isTierInput = false;
					// 	if (scope.tier_arr.length > 0) {
					// 		scope.isTierSummary = false;
					// 	} else {
					// 		scope.isTierBtn = false;
					// 	}
					// } else if (scope.isTierSummary == true) {
					// 	$state.go('enrollment-options');
					// 	// $state.go('benefits-dashboard');
					// } 
					if (scope.isEnrollmentOptions == true) {
						// if (scope.spending_account_status.medical == false || scope.spending_account_status.wellness == false) {
						// 	// scope.isTierSummary = false;
						// 	scope.isEnrollmentOptions = false;
						// 	$state.go('benefits-dashboard');
						// } else {
						// 	$state.go('enrollment-options');
						// }
						if( localStorage.getItem('fromEmpOverview') == true || localStorage.getItem('fromEmpOverview') == 'true' ){
							$state.go( 'employee-overview' );
						}else{
							$state.go( 'benefits-dashboard' );
						}
						// $state.go('benefits-dashboard');
					} else if (scope.isExcel == true || scope.isWebInput == true) {
						scope.isEnrollmentOptions = true;
						scope.isExcel = false;
						scope.isWebInput = false;
						$('.summary-right-container').hide();
					} else if (scope.isReviewEnroll == true) {
						swal({
							title: "Confirm",
							text: "Temporary employee data will be deleted, Proceed?",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: "#0392CF",
							confirmButtonText: "Confirm",
							cancelButtonText: "No",
							closeOnConfirm: true,
							customClass: "updateEmp"
						},
							function (isConfirm) {
								if (isConfirm) {
									if (scope.temp_employees.length > 0) {
										scope.showLoading();
										angular.forEach(scope.temp_employees, function (value, key) {
											dependentsSettings.deleteTempEmployees(value.employee.temp_enrollment_id)
												.then(function (response) {
													// console.log(response);
													if (key == scope.temp_employees.length - 1) {
														scope.hideLoading();
														// $state.go('enrollment-options');
														localStorage.setItem('fromEmpOverview', false);
														// $state.go('create-team-benefits-tiers');
														scope.isAllPreviewEmpChecked = false;
														scope.isReviewEnroll = false;
														scope.isEnrollmentOptions = true;
													}
												});
										});
									} else {
										// $state.go('enrollment-options');
										localStorage.setItem('fromEmpOverview', false);
										// $state.go('create-team-benefits-tiers');
										scope.isAllPreviewEmpChecked = false;
										scope.isReviewEnroll = false;
										scope.isEnrollmentOptions = true;
										scope.$apply();
									}
								}
							});
					} else if (scope.downloadWithDependentsCheckbox == true) {
						scope.downloadWithDependentsCheckbox = false;
						scope.isExcel = true;
						scope.download_step = 1;
					} else if (scope.isUploadFile == true) {
						scope.upload_file_dependent = null;
						scope.isUploadFile = false;
						scope.isExcel = true;
						scope.download_step = 1;
					}
					if( scope.isCommunicationShow == true ){
						scope.isCommunicationShow = false;
						scope.getEnrollTempEmployees();
						scope.isReviewEnroll = true;
					}
				}

				scope.addTierBtn = function () {
					scope.tier_data = {
						gp_cap_status: false,
					};

					scope.isTierBtn = false;
					scope.isTierSummary = false;
					scope.toggleTierLoader();
					$timeout(function () {
						scope.toggleTierLoader();
						scope.isTierInput = true;
					}, 500);
				}

				scope.toggleTierLoader = function () {
					if (scope.isTierLoaderShow == false) {
						scope.isTierLoaderShow = true;
					} else {
						scope.isTierLoaderShow = false;
					}
				}

				scope.saveTierData = function (data) {
					if (data.medical_annual_cap == 0 || data.wellness_annual_cap == 0 || data.gp_cap_per_visit == 0 || data.member_head_count == 0) {
						swal('Error!', "Input values should be 1 or more", 'error');
						return false;
					}
					if (data.gp_cap_status == true && (!data.gp_cap_per_visit || data.gp_cap_per_visit == 0)) {
						swal('Error!', "Input values should be 1 or more", 'error');
						return false;
					}
					scope.lastContentShown = 1;
					if (scope.isEditActive == true) {
						scope.updateBenefitsTier(data);
					} else {
						scope.saveBenefitsTier(data);
					}
				}

				scope.editTierData = function (data, index) {
					scope.lastContentShown = 2;
					scope.selected_edit_tier_index = index;
					if (data.gp_cap_per_visit > 0) {
						data.gp_cap_status = true;
					}
					scope.tier_data = data;
					scope.isEditActive = true;
					scope.toggleTierLoader();
					scope.isTierSummary = false;
					$timeout(function () {
						scope.toggleTierLoader();
						scope.isTierInput = true;
					}, 500);
				}

				scope.tierSummaryClicked = function (data, index) {
					if (data.member_enrolled_count == data.member_head_count) {
						swal('Error!', 'Employee head count is full', 'error');
					} else {
						angular.forEach(scope.tier_arr, function (value, key) {
							value.isSelected = false;
						});
						data.isSelected = true;
						scope.tierSelected = data;
						scope.tierSelected.index = index - 1;
						scope.isNextBtnDisabled = false;
						scope.employee_enroll_count = data.member_enrolled_count + 1;
						scope.dependents_enroll_count = data.dependent_enrolled_count + 1;
						scope.employee_data = {
							medical_credits: scope.tierSelected.medical_annual_cap,
							wellness_credits: scope.tierSelected.wellness_annual_cap,
							dependents: [],
							plan_start: scope.customer_data.plan.plan_start,
							mobile_area_code: scope.showCurrencyType == 'myr' ? '60' : '65' ,
							mobile_area_code_country: scope.showCurrencyType
						};
					}
				}

				scope.toggleAddDependents = function () {
					if (scope.isAddDependentsShow == false) {
						if (scope.isTiering) {
							if (scope.dependents_enroll_count > scope.tierSelected.dependent_head_count) {
								swal('Error!', 'Maximum Tier dependent head count already reached.', 'error');
								return false;
							}
						} else {
							if (scope.overall_dep_count > scope.dependents.total_number_of_seats) {
								swal('Error!', 'Maximum dependent head count already reached.', 'error');
								return false;
							}
						}
						scope.isWebInput = false;
						scope.isAddDependentsShow = true;
						scope.isBackBtnDisabled = true;
						scope.isNextBtnDisabled = true;
						scope.showDependentsAdded = false;
						scope.isDependentListShow = false;
					} else {
						swal({
							title: "Confirm",
							text: "Unsaved data will be deleted, Proceed?",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: "#0392CF",
							confirmButtonText: "Confirm",
							cancelButtonText: "No",
							closeOnConfirm: true,
							customClass: "updateEmp"
						},
							function (isConfirm) {
								if (isConfirm) {
									scope.$apply(function () {
										scope.isAddDependentsShow = false;
										scope.isBackBtnDisabled = false;
										scope.isNextBtnDisabled = false;
										scope.isWebInput = true;
										scope.inititalizeGeoCode();
										scope.inititalizeDatepicker();
										scope.showDependentsAdded = false;
										scope.dependent_arr = [];
										scope.dependent_data = {};
										scope.selected_emp_dep_tab = 1;
									});

								}
							});
					}
					scope.inititalizeDatepicker();
				}

				scope.showSummary = function () {
					scope.toggleTierLoader();
					$('.tier-feature-item').hide();
					scope.lastContentShown = '.benefits-tier-summary-container-wrapper';
					$timeout(function () {
						scope.toggleTierLoader();
						$('.benefits-tier-summary-container-wrapper').fadeIn();
					}, 1000);
				}

				scope.openEditDetailsModal = function (index) {
					scope.isEditDetailModalOpen = true;
					scope.selected_edit_details_data = scope.temp_employees[index];
					$("#edit-employee-details").modal('show');
					$('.edit-employee-details-form .datepicker').datepicker('setDate', scope.selected_edit_details_data.employee.dob);
					$('.edit-employee-details-form .start-date-datepicker').datepicker('setDate', scope.selected_edit_details_data.employee.start_date);
					$timeout(function () {
						var dt = new Date();
						dt.setFullYear(new Date().getFullYear() - 18);
						$('.datepicker').datepicker({
							format: 'dd/mm/yyyy',
							// endDate : dt
						});
						$('.start-date-datepicker').datepicker({
							format: 'dd/mm/yyyy',
						});
						$('.start-date-datepicker').datepicker().on('hide', function (evt) {
							// console.log(evt);
							var val = $('.start-date-datepicker').val();
							if (val == "") {
								$('.start-date-datepicker').datepicker('setDate', scope.customer_data.plan.plan_start);
							}
						})
						$('.start-date-datepicker-dependent').datepicker({
							format: 'dd/mm/yyyy',
						});
						$('.start-date-datepicker-dependent').datepicker().on('hide', function (evt) {
							// console.log(evt);
							var val = $('.start-date-datepicker-dependent').val();
							if (val == "") {
								$('.start-date-datepicker-dependent').datepicker('setDate', scope.customer_data.plan.plan_start);
							}
						})
					}, 300);
					scope.inititalizeGeoCode();
				}

				scope.removeDependent = function (index) {
					swal({
						title: "Confirm",
						text: "are you sure you want to remove this dependent?",
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
								scope.toggleTierLoader();
								scope.employee_data.dependents.splice(index, 1);
								scope.toggleTierLoader();
								scope.$apply(function () {
									if (scope.isTiering) {
										scope.dependents_enroll_count -= 1;
									} else {
										scope.overall_dep_count -= 1;
									}
									if (scope.employee_data.dependents.length > 0) {
										scope.isWebInput = false;
										scope.added_dependent_ctr = 0;
										scope.added_dependent_data = scope.employee_data.dependents[0];
									} else {
										scope.isWebInput = true;
										scope.inititalizeGeoCode();
										scope.inititalizeDatepicker();
										scope.showDependentsAdded = false;
										scope.isDependentListShow = false;
									}
								});

							}
						});
				}

				scope.prevDependentActive = function () {
					if (scope.dependent_ctr != 0) {
						if (scope.dependent_arr[scope.dependent_ctr]) {
							scope.dependent_arr[scope.dependent_ctr] = scope.dependent_data;
						}
						if (scope.isTiering) {
							scope.dependents_enroll_count -= 1;
						} else {
							scope.overall_dep_count -= 1;
						}
						scope.dependent_ctr -= 1;
						if (scope.dependent_arr[scope.dependent_ctr]) {
							scope.dependent_data = scope.dependent_arr[scope.dependent_ctr];
						} else {
							scope.dependent_data = {}
						}
					}
				}

				scope.nextDependentActive = function () {
					if (scope.dependents_enroll_count < scope.dependents.total_number_of_seats) {
						if (scope.dependent_arr[scope.dependent_ctr]) {
							scope.dependent_arr[scope.dependent_ctr] = scope.dependent_data;
						}
						if (scope.isTiering) {
							scope.dependents_enroll_count += 1;
						} else {
							scope.overall_dep_count += 1;
						}
						scope.dependent_ctr += 1;
						if (scope.dependent_arr[scope.dependent_ctr]) {
							scope.dependent_data = scope.dependent_arr[scope.dependent_ctr];
						} else {
							scope.dependent_data = {}
						}
					}
				}

				scope.prevAddedDependent = function () {
					if (scope.added_dependent_ctr != 0) {
						scope.added_dependent_ctr -= 1;
						scope.added_dependent_data = scope.employee_data.dependents[scope.added_dependent_ctr];
					}
				}

				scope.nextAddedDependent = function () {
					if (scope.added_dependent_ctr < (scope.employee_data.dependents.length - 1)) {
						scope.added_dependent_ctr += 1;
						scope.added_dependent_data = scope.employee_data.dependents[scope.added_dependent_ctr];
					}
				}

				scope.pushActiveDependent = function (data) {
					console.log(data);
					if (scope.isTiering) {
						if (scope.dependents_enroll_count > scope.tierSelected.dependent_head_count) {
							swal('Error!', 'Maximum Tier dependent head count already reached.', 'error');
							return false;
						}
					} else {
						if (scope.overall_dep_count > scope.dependents.total_number_of_seats) {
							swal('Error!', 'Maximum dependent head count already reached.', 'error');
							return false;
						}
					}
					if (scope.checkDependentForm() == true) {
						data.done = true;
						scope.dependent_arr.push(data);
						if (scope.isTiering) {
							if (scope.dependents_enroll_count <= scope.tierSelected.dependent_head_count) {
								scope.dependents_enroll_count += 1;
							}
						} else {
							if (scope.overall_dep_count <= scope.dependents.total_number_of_seats) {
								scope.overall_dep_count += 1;
							}
						}
						scope.dependent_ctr += 1;
						scope.dependent_data = {};
						scope.showLoading();
						scope.hideLoading();

						if ((scope.dependents_enroll_count > scope.tierSelected.dependent_head_count) || (scope.overall_dep_count > scope.dependents.total_number_of_seats)) {
							if (scope.employee_data.dependents.length > 0) {
								angular.forEach(scope.dependent_arr, function (value, key) {
									scope.employee_data.dependents.push(value);
								});
							} else {
								scope.employee_data.dependents = scope.dependent_arr;
							}
							swal('Success', 'Dependents successfully added under this employee.', 'success');
							scope.isAddDependentsShow = false;
							scope.isBackBtnDisabled = false;
							scope.isNextBtnDisabled = false;
							scope.isWebInput = true;
							scope.inititalizeGeoCode();
							scope.dependent_arr = [];
							scope.dependent_data = {};
							scope.dependent_data.plan_start = scope.customer_data.plan.plan_start;
							scope.dependent_ctr = 0;
							scope.selected_emp_dep_tab = 1;
							scope.inititalizeDatepicker();
						}

					}
				}

				scope.saveActiveDependents = function () {
					if (!scope.dependent_data.fullname && !scope.dependent_data.dob && !scope.dependent_data.relationship && !scope.dependent_data.plan_start) {
					} else {
						if (scope.checkDependentForm() == true) {
							scope.dependent_arr.push(scope.dependent_data);
							if (scope.isTiering) {
								scope.dependents_enroll_count += 1;
							} else {
								scope.overall_dep_count += 1;
							}
							scope.dependent_ctr += 1;
							scope.dependent_data = {};
						} else {
							return false;
						}
					}
					if (scope.employee_data.dependents.length > 0) {
						angular.forEach(scope.dependent_arr, function (value, key) {
							console.log(value);
							value.dob = moment(value.dob).format('YYYY-MM-DD');
							scope.employee_data.dependents.push(value);
						});
					} else {
						scope.employee_data.dependents = scope.dependent_arr;
					}
					scope.showLoading();
					scope.hideLoading();
					swal('Success', 'Dependents successfully added under this employee.', 'success');
					scope.isAddDependentsShow = false;
					scope.isBackBtnDisabled = false;
					scope.isNextBtnDisabled = false;
					scope.isWebInput = true;
					scope.inititalizeGeoCode();
					scope.dependent_arr = [];
					scope.dependent_data = {};
					scope.dependent_data.plan_start = scope.customer_data.plan.plan_start;
					scope.dependent_ctr = 0;
					scope.selected_emp_dep_tab = 1;
					scope.inititalizeDatepicker();
					console.log(scope.employee_data);
				}

				scope.deleteActiveDependents = function () {
					scope.employee_data = {
						medical_credits: 0,
						wellness_credits: 0,
						dependents: [],
						plan_start: scope.customer_data.plan.plan_start,
						mobile_area_code: scope.showCurrencyType == 'myr' ? '60' : '65' ,
						mobile_area_code_country: scope.showCurrencyType
					};

					if (scope.employee_arr[scope.employee_ctr]) {
						scope.employee_arr[scope.employee_ctr] = scope.employee_data;
					}

					console.log(scope.employee_arr);
				}

				scope.isEmpDataNotEmpty = function () {
					if (scope.employee_data.fullname || scope.employee_data.dob ||
						scope.employee_data.email || scope.employee_data.mobile) {
						return true;
					}
					return false;
				}

				scope.pushActiveEmployee = function (data) {
					console.log(data);
					if (scope.checkEmployeeForm() == true) {
						data.job_title = 'Others';
						data.postcode = '12345';
						scope.employee_arr.push(data);
						if (scope.isTiering) {
							if (scope.employee_enroll_count != scope.tierSelected.member_head_count) {
								if (scope.isTiering) {
									scope.employee_data.medical_credits = scope.tierSelected.medical_annual_cap;
									scope.employee_data.wellness_credits = scope.tierSelected.wellness_credits;
								}
								scope.employee_enroll_count += 1;
							}
						} else {
							if (scope.overall_emp_count != scope.progress.total_employees) {
								scope.overall_emp_count += 1;
							}
						}
						scope.employee_ctr += 1;
						if ( scope.showCurrencyType == 'myr' ) {
							scope.employee_data = {
								medical_credits: 0,
								wellness_credits: 0,
								dependents: [],
								plan_start: scope.customer_data.plan.plan_start,
								mobile_area_code: '60',
								mobile_area_code_country: 'my'
							};
						} else {
							scope.employee_data = {
								medical_credits: 0,
								wellness_credits: 0,
								dependents: [],
								plan_start: scope.customer_data.plan.plan_start,
								mobile_area_code: '65',
								mobile_area_code_country: 'sg'
							};
						}
						
						iti.setCountry(scope.showCurrencyType.toUpperCase());
						scope.dependent_data = {};
						scope.dependent_data.plan_start = scope.customer_data.plan.plan_start;
						scope.employee_data.plan_start = scope.customer_data.plan.plan_start;
						scope.dependent_arr = [];
						scope.showDependentsAdded = false;
						scope.isDependentListShow = false;
						scope.added_dependent_ctr = 0;
						scope.added_dependent_data = {};
						scope.showLoading();
						scope.hideLoading();
					}
				}

				scope.prevActiveEmployee = function () {
					if (scope.employee_arr[scope.employee_ctr] == undefined) {
						scope.employee_arr[scope.employee_ctr] = scope.employee_data;
						// swal({
						//        title: "Confirm",
						//        text: "Unsaved changes will be deleted. Proceed?",
						//        type: "warning",
						//        showCancelButton: true,
						//        confirmButtonColor: "#0392CF",
						//        confirmButtonText: "Yes",
						//        cancelButtonText: "No",
						//        closeOnConfirm: true,
						//        customClass: "updateEmp"
						//      },
						//      function(isConfirm){
						//      	if( isConfirm ){
						//      		scope.$apply(function(){

						if (scope.isTiering) {
							scope.employee_enroll_count -= 1;
						} else {
							scope.overall_emp_count -= 1;
						}
						scope.employee_ctr -= 1;
						scope.employee_data = scope.employee_arr[scope.employee_ctr];
						iti.setCountry(scope.employee_data.mobile_area_code_country);
						if (scope.employee_data.dependents.length > 0) {
							scope.added_dependent_data = scope.employee_data.dependents[0];
						}
						//      		});
						//      	}
						//      });
					} else {
						if (scope.isTiering) {
							scope.employee_enroll_count -= 1;
						} else {
							scope.overall_emp_count -= 1;
						}
						scope.employee_ctr -= 1;
						scope.employee_data = scope.employee_arr[scope.employee_ctr];
						iti.setCountry(scope.employee_data.mobile_area_code_country);
						if (scope.employee_data.dependents.length > 0) {
							scope.added_dependent_data = scope.employee_data.dependents[0];
						}
					}

				}

				scope.nextActiveEmployee = function () {
					if (scope.checkEmployeeForm() == true) {
						if (scope.isTiering) {
							if (scope.employee_arr[scope.employee_ctr]) {
								scope.employee_enroll_count++;
								scope.employee_ctr += 1;
								scope.employee_data = scope.employee_arr[scope.employee_ctr];
								iti.setCountry(scope.employee_data.mobile_area_code_country);
								if (!scope.employee_data) {
									scope.employee_data = {
										medical_credits: 0,
										wellness_credits: 0,
										dependents: [],
										plan_start: scope.customer_data.plan.plan_start,
										mobile_area_code: scope.showCurrencyType == 'myr' ? '60' : '65' ,
										mobile_area_code_country: scope.showCurrencyType,
										medical_entitlement: null,
										wellness_entitlement: null,
										bank_name: null,
										bank_account: null,
										cap_per_visit: null,
									};
									iti.setCountry(scope.showCurrencyType.toUpperCase());
								}
								if (scope.employee_data.dependents.length > 0) {
									scope.added_dependent_data = scope.employee_data.dependents[0];
								}
							} else {
								scope.pushActiveEmployee(scope.employee_data);
							}
						} else {
							if (scope.employee_arr[scope.employee_ctr]) {
								scope.overall_emp_count++;
								scope.employee_ctr += 1;
								scope.employee_data = scope.employee_arr[scope.employee_ctr];
								iti.setCountry(scope.employee_data.mobile_area_code_country);
								if (!scope.employee_data) {
									scope.employee_data = {
										medical_credits: 0,
										wellness_credits: 0,
										dependents: [],
										plan_start: scope.customer_data.plan.plan_start,
										mobile_area_code: scope.showCurrencyType == 'myr' ? '60' : '65',
										mobile_area_code_country: scope.showCurrencyType,
										medical_entitlement: null,
										wellness_entitlement: null,
										bank_name: null,
										bank_account: null,
										cap_per_visit: null,
									};
									iti.setCountry( scope.showCurrencyType.toUpperCase() );
								}
								if (scope.employee_data.dependents.length > 0) {
									scope.added_dependent_data = scope.employee_data.dependents[0];
								}
							} else {
								scope.pushActiveEmployee(scope.employee_data);
							}
						}
					}
				}

				scope.selectEnrollmentMethod = function (opt) {
					if (opt == 0) {
						scope.isExcelSelected = true;
						scope.isWebInputSelected = false;
					} else {
						scope.isExcelSelected = false;
						scope.isWebInputSelected = true;
					}
					scope.isNextBtnDisabled = false;
				}

				scope.checkNRIC = function (theNric) {
					var nric_pattern = null;
					if (theNric.length == 9) {
						nric_pattern = new RegExp("^[stfgSTFG]{1}[0-9]{7}[a-zA-z]{1}$");
					} else if (theNric.length == 12) {
						// nric_pattern = new RegExp("^[0-9]{2}(?:0[1-9]|1[-2])(?:[0-1]|[1-2][0-9]|[3][0-1])[0-9]{6}$");
						return true;
					} else {
						return false;
					}
					return nric_pattern.test(theNric);
				};

				scope.checkEmail = function (email) {
					var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					return regex.test(email);
				}

				scope.getPrePostStatus = function () {
					hrSettings.getPrePostStatus()
						.then(function (response) {
							console.log(response);
							scope.spendingPlan_status = response.data;

							scope.checkSpendingValuesStatus();
						});
				}

				scope.excelTemplate = {};
				scope.getExcelLink = function () {
					var company_id = localStorage.getItem('apc_user_id');
					console.log('company ID',company_id);
					hrSettings.get_excel_link(company_id)
						.then(function(response) {
							console.log(response);
							scope.excelTemplate = response.data;
						})
				}

				scope.downloadTemplate = function () {
					var med_spending_acct = scope.spending_account_status.medical;
					var well_spending_acct = scope.spending_account_status.wellness;
					// var med_spending_acct = false;
					// var well_spending_acct = true;
					// var medical_entitlement = localStorage.getItem('hasMedicalEntitlementBalance');
					// var wellness_entitlement = localStorage.getItem('hasWellnessEntitlementBalance');


					if ( scope.excelTemplate.status == true) {
						if (scope.downloadWithDependents != null) {

							if (scope.downloadWithDependents == false) {
								console.log('w/out dependents','prepaid');
								
								window.location.href = `${scope.excelTemplate.employee}`;
							} else {

								console.log('w/dependents','prepaid');
								window.location.href = `${scope.excelTemplate.dependent}`;
							}
							
						} else {
							swal('Error!', 'Please select an option for you template.', 'error');
						}
					} else {
						if (scope.downloadWithDependents != null) {
							if (scope.downloadWithDependents == false) {
								// window.location.href = '/excel/Employee Enrollment Listing.xlsx';
								console.log('w/out dependents', med_spending_acct, well_spending_acct);
	
								if (med_spending_acct == true && well_spending_acct == true ) {
									window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v2/employees/Employee-Enrollment-Listing-With-Medical-With-Wellness.xlsx';
									console.log('scenario 1');
								} else if (med_spending_acct == true && well_spending_acct == false) {
									window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v2/employees/Employee-Enrollment-Listing-With-Medical.xlsx';
									console.log('scenario 2');
								} else if (med_spending_acct == false && well_spending_acct == true) {
									window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v2/employees/Employee-Enrollment-Listing-With-Wellness.xlsx';
									console.log('scenario 3');
								} else if (med_spending_acct == false || well_spending_acct == false) {
									window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v2/employees/Employee-Enrollment-Listing.xlsx';
									console.log('scenario 4');
								}
							} else {
								console.log('w/dependents');
								// window.location.href = '/excel/Employees and Dependents.xlsx';
								// window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employees+and+Dependents.xlsx';
	
								if (med_spending_acct == true && well_spending_acct == true ) {
									window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v2/dependents/Employees-and-Dependents-With-Medical-With-Wellness.xlsx';
									console.log('scenario 1');
								} else if (med_spending_acct == true && well_spending_acct == false) {
									window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v2/dependents/Employees-and-Dependents-With-Medical.xlsx';
									console.log('scenario 2');
								} else if (med_spending_acct == false && well_spending_acct == true) {
									window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v2/dependents/Employees-and-Dependents-With-Wellness.xlsx';
									console.log('scenario 3');
								} else if (med_spending_acct == false || well_spending_acct == false) {
									window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/v2/dependents/Employees-and-Dependents.xlsx';
									console.log('scenario 4');
								}
							}
						} else {
							swal('Error!', 'Please select an option for you template.', 'error');
						}
					}
					
				}

				scope.downloadExcelTemplate = function (opt) {
					if (opt == 0) {
						scope.downloadWithDependents = false;
					}
					if (opt == 1) {
						scope.downloadWithDependents = true;
					}
				}

				scope.uploadedFile = false;

				scope.runUpload = function (file) {
					// console.log(file);
					var data = {
						file: file,
						plan_start: moment().format('YYYY-MM-DD'),
					}
					// if (scope.isTiering == true || scope.isTiering == 'true') {
					// 	data.plan_tier_id = scope.tierSelected.plan_tier_id;
					// }
					scope.showLoading();
					hrSettings.uploadExcel(data)
						.then(function (response) {
							// console.log( response );
							scope.hideLoading();
							if (response.data.status == true) {
								scope.uploadedFile = true;
								scope.isInvalid = false;
								scope.isValid = true;
								scope.isNextBtnDisabled = false;
								scope.message = 'Successfully Uploaded.';
								swal('Success!', 'uploaded.', 'success');
							} else {
								scope.uploadedFile = false;
								scope.isInvalid = true;
								scope.isValid = false;
								scope.isNextBtnDisabled = true;
								console.log(response);
								console.log(response.data);
								swal('Error!', response.data.message, 'error');
							}

						});
				}

				scope.checkEmployeeForm = function () {
					if ( scope.showCurrencyType == 'myr' ) {
						if ( !scope.employee_data.nric && !scope.employee_data.mobile && !scope.employee_data.passport ) {
							sweetAlert("Error!", "Mobile Number,NRIC or Passport Number is required", "error");
							return false;
						}
					} else {
						if (!scope.employee_data.email && !scope.employee_data.mobile) {
							swal('Error!', 'Email or Mobile is required.', 'error');
							return false;
						}
					}

					if (!scope.employee_data.fullname) {
						swal('Error!', 'Full Name is required.', 'error');
						return false;
					}
					if (!scope.employee_data.dob) {
						swal('Error!', 'Date of Birth is required.', 'error');
						return false;
					}
					if (scope.employee_data.email) {
						if (scope.checkEmail(scope.employee_data.email) == false) {
							swal('Error!', 'Email is invalid.', 'error');
							return false;
						}
					}
					if (scope.employee_data.mobile) {
						// console.log( iti.getSelectedCountryData().iso2 );
						if (iti.getSelectedCountryData().iso2 == 'sg' && scope.employee_data.mobile.length < 8) {
							swal('Error!', 'Mobile Number for your country code should be 8 digits.', 'error');
							return false;
						}
						if (iti.getSelectedCountryData().iso2 == 'my' && scope.employee_data.mobile.length < 9 || scope.employee_data.mobile.length > 10) {
							// swal('Error!', 'Mobile Number for your country code should be 10 digits.', 'error');
							swal('Error!', 'Invalid mobile format. Please enter mobile in the format of 9-10 digit number without the prefix “0”.', 'error');
							return false;
						}
						if (iti.getSelectedCountryData().iso2 == 'ph' && scope.employee_data.mobile.length < 9) {
							swal('Error!', 'Mobile Number for your country code should be 9 digits.', 'error');
							return false;
						}
					}
					if ( scope.showCurrencyType == 'myr' ) {
						if ( scope.employee_data.nric ) {
							if (scope.employee_data.nric.includes("-")) {
								sweetAlert("Oops...", "Invalid NRIC format. Please enter NRIC in the format of 12 digit number only.", "error");
								return false;
							} else if (!scope.checkNRIC(scope.employee_data.nric)) {
								sweetAlert("Oops...", "Invalid NRIC format. Please enter NRIC in the format of 12 digit number only.", "error");
								return false;
							}
						}
						if ( scope.employee_data.passport ) {
							if (!scope.checkPassport(scope.employee_data.passport)) {
								sweetAlert("Oops...", "Invalid passport format. Please enter passport in the format of a letter followed by an 8 digit number.", "error");
									return false;
							}
						}
					}
					
					// if( !scope.employee_data.postal_code ){
					// 	swal( 'Error!', 'Postal Code is required.', 'error' );
					// 	return false;
					// }
					if (!scope.employee_data.plan_start) {
						swal('Error!', 'Start Date is required.', 'error');
						return false;
					}

					return true;
				}

				scope.checkDependentForm = function () {
					if (!scope.dependent_data.fullname) {
						swal('Error!', 'Full Name is required.', 'error');
						return false;
					}
					// if( !scope.dependent_data.last_name ){
					// 	swal( 'Error!', 'Last Name is required.', 'error' );
					// 	return false;
					// }
					// if( !scope.dependent_data.nric ){
					// 	swal( 'Error!', 'NRIC is required.', 'error' );
					// 	return false;
					// }else{
					// 	if( scope.nric_status_dependents == true ){
					// 		var checkNRIC = scope.checkNRIC(scope.dependent_data.nric);
					// 		if( checkNRIC != true ){
					// 			swal( 'Error!', 'Invalid NRIC.', 'error' );
					// 			return false;
					// 		}
					// 	}	
					// }
					if (!scope.dependent_data.dob) {
						swal('Error!', 'Date of Birth is required.', 'error');
						return false;
					}
					if (!scope.dependent_data.relationship) {
						scope.dependent_data.relationship = null;
						// swal( 'Error!', 'Relationship is required.', 'error' );
						// return false;
					}
					if (!scope.dependent_data.plan_start) {
						swal('Error!', 'Start Date is required.', 'error');
						return false;
					}

					return true;
				}

				scope.reloadPage = function () {

					window.location.reload();
				}

				scope.empCheckBoxClicked = function (index) {
					var check = $.inArray(scope.temp_employees[index].employee.temp_enrollment_id, scope.previewTable_arr);
					if (check < 0) {
						scope.previewTable_arr.push(scope.temp_employees[index].employee.temp_enrollment_id);
					} else {
						scope.previewTable_arr.splice(check, 1);
					}
					if (scope.previewTable_arr.length > 0) {
						$('.preview-trash-icon').show();
					} else {
						$('.preview-trash-icon').hide();
					}
				}

				scope.empCheckBoxAll = function () {
					scope.previewTable_arr = [];
					if (scope.isAllPreviewEmpChecked == false) {
						scope.isAllPreviewEmpChecked = true;
						angular.forEach(scope.temp_employees, function (value, key) {
							value.checkboxSelected = true;
							scope.previewTable_arr.push(value.employee.temp_enrollment_id);
						});
					} else {
						angular.forEach(scope.temp_employees, function (value, key) {
							value.checkboxSelected = false;
						});
						scope.isAllPreviewEmpChecked = false;
					}
				}



				// HTTP REQUEST

				scope.enrollEmployees = function () {
					console.log(scope.employee_data);
					var emp_arr = [];
					if (!scope.employee_data.fullname && !scope.employee_data.dob
						&& !scope.employee_data.mobile && !scope.employee_data.email) {

					} else {
						if (scope.checkEmployeeForm() == true) {
							if (!scope.employee_arr[scope.employee_ctr]) {
								scope.employee_data.job_title = 'Others';
								console.log(scope.employee_data);
								scope.employee_arr.push(scope.employee_data);
							}
						} else {
							return false;
						}
					}
					angular.forEach(scope.employee_arr, function (value, key) {
						if (value.fullname && value.dob && value.plan_start) {
							emp_arr.push(value);
						}

						if (key == scope.employee_arr.length - 1) {

							angular.forEach(emp_arr, function (value, key) {
								console.log(value.dob);
								// value.dob = moment( value.dob ).format('YYYY-MM-DD');
								// value.plan_start = moment( value.plan_start, 'DD/MM/YYYY').format('YYYY-MM-DD');
								// value.plan_start = moment( value.plan_start, 'DD/MM/YYYY').format('YYYY-MM-DD');

								angular.forEach(value.dependents, function (value2, key2) {
									console.log(value2);
									value2.dob = moment(value2.dob, 'DD/MM/YYYY').format('YYYY-MM-DD');
									value2.plan_start = moment(value2.plan_start, 'DD/MM/YYYY').format('YYYY-MM-DD');
								})
							});
							$('.tier-feature-item').hide();
							scope.toggleTierLoader();
							var data = {
								employees: emp_arr,
								plan_tier_id: null
							}
							// if (scope.isTiering == true || scope.isTiering == 'true') {
							// 	data.plan_tier_id = scope.tierSelected.plan_tier_id;
							// }
							console.log(data);
							dependentsSettings.addEnrollEmployees(data)
								.then(function (response) {
									// console.log( response );
									$timeout(function () {
										scope.toggleTierLoader();
										if (response.data.status) {
											scope.getBenefitsTier();
											scope.getEnrollTempEmployees();
											scope.isWebInput = false;
											$('.summary-right-container').hide();
											scope.isReviewEnroll = true;
											scope.employee_data = {
												medical_credits: 0,
												wellness_credits: 0,
												dependents: [],
												plan_start: scope.customer_data.plan.plan_start,
												mobile_area_code: '65',
												mobile_area_code_country: 'sg'
											};
										} else {
											swal('Error!', response.data.message, 'error');
										}
									}, 1000);
								});
						}
					});
				}

				scope.getBenefitsTier = function () {
					scope.tier_arr = [];
					dependentsSettings.fetchBenefitsTier()
						.then(function (response) {
							// console.log( response );
							if (response.data.status) {
								scope.tier_arr = response.data.data;
								console.log('currency', scope.tier_arr);
								scope.selected_edit_tier_index = scope.tier_arr.length + 1;
								angular.forEach(scope.tier_arr, function (value, key) {
									value.dependents = [];
									value.employees = [];
								});
							} else {
								swal('Error!', response.data.message, 'error');
							}
						});
				}

				scope.getEnrollTempEmployees = function () {
					scope.temp_employees = [];
					scope.hasError = false;
					scope.hasEmailOrMobile = false;
					scope.table_dependents_ctr = 0;
					
					scope.showLoading();
					dependentsSettings.getTempEmployees()
						.then(function (response) {
							console.log( response );
							scope.temp_employees = response.data.data;
							angular.forEach(scope.temp_employees, function (value, key) {
								console.log(value);
								if ( (value.employee.email != '' && value.employee.email != null) || (value.employee.mobile != '' && value.employee.mobile != null) ) {
									scope.hasEmailOrMobile = true;
									console.log('proceed enroll');
								}
								if (value.dependents.length > scope.table_dependents_ctr) {
									scope.table_dependents_ctr = value.dependents.length;
								}
								if (value.error_logs.error == true) {
									scope.hasError = true;
								}
								if ((scope.temp_employees.length - 1) == key) {
									scope.hideLoading();
								}
							})
						});
				}

				scope.range = function (range) {
          var arr = [];
          for (var i = 0; i < range; i++) {
            arr.push(i);
          }
          return arr;
        }

				scope.parseValueCommaFloat = function(value){
          return parseFloat(value).toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
				}
				
				scope.formatMomentDate = function (date, from, to) {
          return date ? moment(date, from).format(to) : date;
        };

				scope.range = function (range) {
          var arr = [];
          for (var i = 0; i < range; i++) {
            arr.push(i);
          }
          return arr;
        }

				scope.parseValueCommaFloat = function(value){
          return parseFloat(value).toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

				scope.updateEnrolleEmp = function (emp) {
					console.log(emp);

					// if ( scope.showCurrencyType == 'myr' ) {
					// 	if ( emp.employee.nric == '' && emp.employee.mobile == '' && emp.employee.passport == '' ) {
					// 		sweetAlert("Error!", "Please key in Mobile No., NRIC, or Passport Number.", "error");
					// 		return false;
					// 	}
					// } else {
					// 	if (emp.employee.email == "" && emp.employee.mobile == "") {
					// 		swal("Error!", "Email Address or Mobile Number is required.", 'error');
					// 		return false;
					// 	}
					// }

					// if( !emp.employee.mobile_area_code ) {
					// 	swal("Error!", "Please prvoide a Mobile Area Code is required.", 'error');
					// 	return false;
					// }

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
								scope.showLoading();
								var data = {
									temp_enrollment_id: emp.employee.temp_enrollment_id,
									fullname: emp.employee.fullname,
									// first_name: emp.employee.first_name,
									// last_name: emp.employee.last_name,
									// nric: emp.employee.nric,
									dob: moment(emp.employee.dob, 'DD/MM/YYYY').format('DD/MM/YYYY'),
									email: emp.employee.email,
									mobile: emp.employee.mobile,
									nric: emp.employee.nric,
									job_title: emp.employee.job_title,
									medical_credits: parseFloat(emp.employee.credits),
									wellness_credits: parseFloat(emp.employee.wellness_credits),
									plan_start: moment(emp.employee.start_date, 'DD/MM/YYYY').format('DD/MM/YYYY'),
									postal_code: emp.employee.postal_code,
									passport: emp.employee.passport,
									mobile_area_code: emp.employee.mobile_area_code,
								}
								dependentsSettings.updateTempEnrollee(data)
									.then(function (response) {
										// console.log(response);
										if (emp.dependents.length > 0) {
											angular.forEach(emp.dependents, function (value, key) {
												var dep_data = {
													dependent_temp_id: value.enrollee.dependent_temp_id,
													fullname: value.enrollee.fullname,
													// first_name : value.enrollee.first_name,
													// last_name : value.enrollee.last_name,
													// nric : value.enrollee.nric,
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
								angular.forEach(scope.previewTable_arr, function (value, key) {
									dependentsSettings.deleteTempEmployees(value)
										.then(function (response) {
											// console.log(response);
											if ((scope.previewTable_arr.length - 1) == key) {
												scope.hideLoading();
												scope.getEnrollTempEmployees();
												scope.previewTable_arr = [];
												// if( scope.temp_employees.length == 0 ){
												// 	$state.go('enrollment-options');
												// }
											}
										});
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
										// console.log(response);
										scope.getEnrollTempEmployees();
										$(".modal").modal('hide');
									});
							}
						});
				}
				scope.goToCommunication	=	function(){
					console.log(scope.hasEmailOrMobile);
					if(scope.hasEmailOrMobile == false){
						scope.saveTempUser();
						scope.isReviewEnroll = false;
						scope.isCommunicationShow = false;
					}else{
						scope.isReviewEnroll = false;
						scope.isCommunicationShow = true;
						
						$timeout(function(){
							$('.comm-schedule-datepicker').datepicker({
								format: 'dd/mm/yyyy',
								startDate : new Date( moment().add(1, 'days') )
							});
						},400);
					}
				}

				scope.saveTempUser = function () {
					console.log(scope.communication_send);
					console.log($('.comm-schedule-datepicker').val());
					if( scope.communication_send.type == 'schedule' && ($('.comm-schedule-datepicker').val() == null || $('.comm-schedule-datepicker').val() == '' )){
						swal('Error!', 'Please select schedule date.', 'error');
						return false;
					}
					scope.showLoading();
					var err = 0;
					scope.current_enrolled_count = {
						total_dependents_enrolled: 0,
						total_employee_enrolled: 0,
					};
					angular.forEach(scope.temp_employees, function (value, key) {
						$('#enrollee-details-tbl tbody tr:nth-child(' + (key + 1) + ') .fa-circle-o-notch').fadeIn();
						var data = {
							temp_enrollment_id: value.employee.temp_enrollment_id,
							communication_send: scope.communication_send.type,
						}
						if(scope.communication_send.type == 'schedule'){
							data.schedule_date = moment( $('.comm-schedule-datepicker').val(), ['DD/MM/YYYY', 'YYYY-MM-DD'] ).format('YYYY-MM-DD');
						}
						dependentsSettings.saveTempEnrollees(data)
							.then(function (response) {
								console.log( response );
								scope.current_enrolled_count.total_employee_enrolled += response.data.result.total_employee_enrolled;
								scope.current_enrolled_count.total_dependents_enrolled += response.data.result.total_dependents_enrolled;
								// $('#enrollee-details-tbl tbody tr:nth-child(' + (key + 1) + ') .fa-circle-o-notch').hide();
								if (response.data.result) {
									if (response.data.result.status == true) {
										value.success = true;
										value.fail = false;
										// $('#enrollee-details-tbl tbody tr:nth-child(' + (key + 1) + ') .fa-check').fadeIn();
									}
								} else {
									value.success = false;
									value.fail = true;
									err++;
									// $('#enrollee-details-tbl tbody tr:nth-child(' + (key + 1) + ') .fa-times').fadeIn();
								}
								if (key == scope.temp_employees.length - 1) {
									$timeout(function () {
										scope.hideLoading();
										scope.getEnrollTempEmployees();
										if (err == 0) {
											scope.isCommunicationShow = false;
											scope.isSuccessfulEnroll = true;
											scope.isFromUpload = false;
											scope.employee_data = {
												medical_credits: 0,
												wellness_credits: 0,
												dependents: [],
												plan_start: scope.customer_data.plan.plan_start,
												mobile_area_code: '65',
												mobile_area_code_country: 'sg'
											};
										}
									}, 1000);
								}

							});
					});
				}

				scope.saveBenefitsTier = function (data) {
					$('.tier-feature-item').hide();
					scope.toggleTierLoader();
					dependentsSettings.addBenefitsTier(data)
						.then(function (response) {
							// console.log( response );
							$timeout(function () {
								scope.toggleTierLoader();
								if (response.data.status) {
									scope.getBenefitsTier();
									scope.tier_data = {
										gp_cap_status: false,
									};
									scope.isTierInput = false;
									scope.isTierSummary = false;
								} else {
									swal('Error!', response.data.message, 'error');
									$('.tier-item-container').fadeIn();
								}
							}, 1000);
						});
				}

				scope.updateBenefitsTier = function (data) {
					$('.tier-feature-item').hide();
					scope.toggleTierLoader();
					data.plan_tier_id = scope.tier_data.plan_tier_id;
					dependentsSettings.updateTier(data)
						.then(function (response) {
							// console.log( response );
							scope.toggleTierLoader();
							if (response.data.status) {
								scope.isTierInput = false;
								scope.isTierSummary = true;
								scope.isEditActive = false;
								swal('Success!', response.data.message, 'success');
							} else {
								swal('Error!', response.data.message, 'error');
								$('.tier-item-container').fadeIn();
							}
							scope.getBenefitsTier();
						});
				}

				scope.removeTier = function () {
					swal({
						title: "Confirm",
						text: "are you sure you want to delete this Tier?",
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
								scope.toggleTierLoader();
								var data = {
									plan_tier_id: scope.tier_data.plan_tier_id
								}
								dependentsSettings.deleteTier(data)
									.then(function (response) {
										// console.log(response);
										scope.isTierInput = false;
										scope.isEditActive = false;
										scope.onLoad();
									});
							}
						});
				}

				scope.getMethod = function () {
					hrSettings.getMethodType()
						.then(function (response) {
							// console.log(response);
							scope.customer_data = response.data.data;
							scope.customer_data.plan.plan_start = moment(scope.customer_data.plan.plan_start).format('DD/MM/YYYY');
							scope.dependent_data.plan_start = scope.customer_data.plan.plan_start;
							scope.employee_data.plan_start = scope.customer_data.plan.plan_start;
						});
				}

				scope.getProgress = function () {
					hrSettings.getEnrollmentProgress()
						.then(function (response) {
							scope.progress = response.data.data;
							scope.overall_emp_count = scope.progress.completed + 1;
							// console.log(scope.progress);
						});
				};

				scope.companyDependents = function () {
					hrSettings.companyDependents()
						.then(function (response) {
							scope.dependents = response.data;
							scope.overall_dep_count = scope.dependents.occupied_seats + 1;
							// console.log(scope.dependents);
						});
				}

				scope.inititalizeDatepicker = function () {
					$timeout(function () {
						var dt = new Date();
						dt.setFullYear(new Date().getFullYear() - 18);
						$('.datepicker').datepicker({
							format: 'dd/mm/yyyy',
							// endDate : dt
						});
						$('.start-date-datepicker').datepicker({
							format: 'dd/mm/yyyy',
						});
						$('.start-date-datepicker-dependent').datepicker({
							format: 'dd/mm/yyyy',
						});
						$('.start-date-datepicker').datepicker().on('hide', function (evt) {
							var val = $('.start-date-datepicker').val();
							if (val == "") {
								$('.start-date-datepicker').datepicker('setDate', scope.customer_data.plan.plan_start);
							}
						})
						$('.start-date-datepicker-dependent').datepicker().on('hide', function (evt) {
							var val = $('.start-date-datepicker-dependent').val();
							if (val == "") {
								$('.start-date-datepicker-dependent').datepicker('setDate', scope.customer_data.plan.plan_start);
							}
						})
					}, 300);
				}

				scope.inititalizeGeoCode = function () {
					$timeout(function () {
						var settings = {
							separateDialCode: true,
							initialCountry: "SG",
							autoPlaceholder: "off",
							utilsScript: "../assets/hr-dashboard/js/utils.js",
						};

						let my_settings = {
							separateDialCode: true,
							initialCountry: "MY",
							autoPlaceholder: "off",
							utilsScript: "../assets/hr-dashboard/js/utils.js",
						};

						if (scope.isEditDetailModalOpen == false) {
							if ( scope.showCurrencyType == 'myr' ) {
								var input = document.querySelector("#area_code");
								iti = intlTelInput(input, my_settings);
								input.addEventListener("countrychange", function () {
									console.log(iti.getSelectedCountryData());
									scope.employee_data.mobile_area_code = iti.getSelectedCountryData().dialCode;
									scope.employee_data.mobile_area_code_country = iti.getSelectedCountryData().iso2;
								});
							} else {
								var input = document.querySelector("#area_code");
								iti = intlTelInput(input, settings);
								input.addEventListener("countrychange", function () {
									console.log(iti.getSelectedCountryData());
									scope.employee_data.mobile_area_code = iti.getSelectedCountryData().dialCode;
									scope.employee_data.mobile_area_code_country = iti.getSelectedCountryData().iso2;
								});
							}
						}
						if (scope.isEditDetailModalOpen == true) {
							var input2 = document.querySelector("#area_code2");
							iti2 = intlTelInput(input2, settings);
							console.log(scope.selected_edit_details_data.employee.format_mobile);
							iti2.setNumber(scope.selected_edit_details_data.employee.format_mobile);
							scope.selected_edit_details_data.employee.mobile = scope.selected_edit_details_data.employee.mobile;
							$("#area_code2").val(scope.selected_edit_details_data.employee.mobile);
							input2.addEventListener("countrychange", function () {
								console.log(iti2.getSelectedCountryData());
								scope.selected_edit_details_data.employee.mobile_area_code = iti2.getSelectedCountryData().dialCode;
								scope.selected_edit_details_data.employee.mobile_area_code_country = iti2.getSelectedCountryData().iso2;
							});
						}
					}, 300);
				}

				scope.getSpendingAccountStatus = function () {
					hrSettings.getSpendingAccountStatus()
						.then(function (response) {
							console.log(response);
							scope.spending_account_status = response.data;
						});
				}

				scope.toggleActivationInfo	=	function(){
					scope.isActivationInfoDropShow = scope.isActivationInfoDropShow == true ? false : true;
				}
				$("body").click(function (e) {
					if ($(e.target).parents(".sub-header-text span").length === 0) {
						scope.isActivationInfoDropShow = false;
						scope.$apply();
					}
				});

				scope.isMedicalAllocColShow = false;
        scope.isWellnessAllocColShow = false;
        scope.isCapVisitColShow = false;
        scope.isBankNameColShow = false;
        scope.isBankNumColShow = false;

        scope.checkSpendingValuesStatus = function(){
          scope.isMedicalAllocColShow = false;
          scope.isWellnessAllocColShow = false;
          scope.isCapVisitColShow = false;
          scope.isBankNameColShow = false;
          scope.isBankNumColShow = false;

          if(
						(scope.spendingPlan_status.account_type == 'lite_plan' && scope.spendingPlan_status.medical_enabled && scope.spendingPlan_status.medical_method == 'post_paid') ||
						(scope.spendingPlan_status.account_type == 'lite_plan' && scope.spendingPlan_status.medical_enabled && scope.spendingPlan_status.medical_method == 'pre_paid' && scope.spendingPlan_status.paid_status) ||
						(scope.spendingPlan_status.account_type != 'lite_plan' && scope.spendingPlan_status.account_type != 'enterprise_plan' && scope.spendingPlan_status.medical_enabled)
						){
            scope.isMedicalAllocColShow = true;
          }
          if(
              (scope.spendingPlan_status.account_type == 'lite_plan' && scope.spendingPlan_status.wellness_enabled && scope.spendingPlan_status.wellness_method == 'post_paid') ||
              (scope.spendingPlan_status.account_type == 'lite_plan' && scope.spendingPlan_status.wellness_enabled && scope.spendingPlan_status.wellness_method == 'pre_paid' && scope.spendingPlan_status.paid_status) ||
              (scope.spendingPlan_status.account_type == 'enterprise_plan' && scope.spendingPlan_status.wellness_enabled && scope.spendingPlan_status.paid_status) ||
              (scope.spendingPlan_status.account_type != 'enterprise_plan' && scope.spendingPlan_status.account_type != 'lite_plan' && scope.spendingPlan_status.wellness_enabled)
            ){
            scope.isWellnessAllocColShow = true;
          }
          if(
							(scope.spendingPlan_status.account_type == 'lite_plan' && 
								(scope.spendingPlan_status.medical_enabled || scope.spendingPlan_status.wellness_enabled) &&
								(scope.spendingPlan_status.medical_method == 'post_paid' && scope.spendingPlan_status.wellness_method == 'post_paid')
							) ||
							(scope.spendingPlan_status.account_type == 'lite_plan' && 
								(scope.spendingPlan_status.medical_enabled || scope.spendingPlan_status.wellness_enabled) &&
								(scope.spendingPlan_status.medical_method == 'pre_paid' || scope.spendingPlan_status.wellness_method == 'pre_paid') &&
								scope.spendingPlan_status.paid_status
							) ||
							(scope.spendingPlan_status.account_type != 'lite_plan' && 
								(scope.spendingPlan_status.medical_enabled || scope.spendingPlan_status.wellness_enabled) &&
								scope.spendingPlan_status.paid_status
							) 
						){
            scope.isCapVisitColShow = true;
					}
					if(scope.spendingPlan_status.account_type == 'enterprise_plan'){
						scope.isCapVisitColShow = false;
					}
          if(scope.spendingPlan_status.medical_reimbursement || scope.spendingPlan_status.wellness_reimbursement){
            scope.isBankNameColShow = true;
            scope.isBankNumColShow = true;
          }
				}
				
				scope.removeAllTempEmployeeOnClose	=	async function(){
					// swal({
					// 	title: "Confirm",
					// 	text: "Temporary employee data will be deleted, Proceed?",
					// 	type: "warning",
					// 	showCancelButton: true,
					// 	confirmButtonColor: "#0392CF",
					// 	confirmButtonText: "Confirm",
					// 	cancelButtonText: "No",
					// 	closeOnConfirm: true,
					// 	customClass: "updateEmp"
					// },
					// 	async function (isConfirm) {
					// 		if (isConfirm) {
								if (scope.temp_employees.length > 0) {
									await angular.forEach(scope.temp_employees, async function (value, key) {
										await dependentsSettings.deleteTempEmployees(value.employee.temp_enrollment_id)
											.then(function (response) {
												if (key == scope.temp_employees.length - 1) {
													return true;
												}
											});
									});
								}else{
									return true;
								}
						// 	}
						// });
				}

				scope.checkNRIC = function (theNric) {
          var nric_pattern = null;
          if (theNric.length == 9) {
            nric_pattern = new RegExp("^[stfgSTFG]{1}[0-9]{7}[a-zA-z]{1}$");
          } else if (theNric.length == 12) {
            // nric_pattern = new RegExp("^[0-9]{2}(?:0[1-9]|1[-2])(?:[0-1]|[1-2][0-9]|[3][0-1])[0-9]{6}$");
            return true;
          } else {
            return false;
          }
          return nric_pattern.test(theNric);
				};
				
				scope.checkPassport = function (value) {
          let passport_pattern = null;
          if (value) {
            passport_pattern = new RegExp("^[a-zA-Z][a-zA-Z0-9.,$;]+$");
          } else {
            return false;
          }
          return passport_pattern.test(value);
        };

				scope.showLoading = function () {
					$(".circle-loader").fadeIn();
					loading_trap = true;
				}

				scope.hideLoading = function () {
					setTimeout(function () {
						$(".circle-loader").fadeOut();
						loading_trap = false;
					},100)
				}

				scope.onLoad = function () {
					// if (localStorage.getItem('enrollmentOptionTiering') == 'true' || localStorage.getItem('enrollmentOptionTiering') == true) {
					// 	scope.isTiering = true;
					// } else {
					// 	scope.isTiering = false;
					// }
					scope.getProgress();
					scope.companyDependents();
					scope.getMethod();
					scope.getBenefitsTier();
					scope.getSpendingAccountStatus();
					scope.getExcelLink();
					scope.getPrePostStatus();


					$timeout(function () {
						loading_trap = false;
						$(".circle-loader").fadeOut();

						scope.toggleTierLoader();

						scope.isTierSummary = false;
						scope.isTierBtn = false;
						scope.isEnrollmentOptions = true;


						// if (scope.isTiering == true || scope.isTiering == 'true') {
						// 	if (scope.tier_arr.length > 0) {
						// 		scope.isTierSummary = true;
						// 	} else {
						// 		scope.isTierBtn = true;
						// 	}
						// } else {
						// 	scope.isTierSummary = false;
						// 	scope.isTierBtn = false;
						// 	scope.isEnrollmentOptions = true;
						// }

						// scope.getEnrollTempEmployees();
						// scope.isReviewEnroll = true;

						scope.hideLoading();
					}, 500);
				}

				// localStorage.getItem("currency_type");
				//   	console.log(localStorage.getItem("currency_type"));


				scope.onLoad();




				$("body").delegate('.summary-right-button', 'click', function (e) {
					$(".summary-right-container").toggleClass('show');
				});

				$("body").click(function (e) {
					if ($(e.target).parents(".summary-right-container").length === 0) {
						$(".summary-right-container").removeClass('show');
					}
				});


				var dt = new Date();
				dt.setFullYear(new Date().getFullYear() - 18);
				$('.datepicker').datepicker({
					format: 'dd/mm/yyyy',
					// endDate : dt
				});

				$('.start-date-datepicker').datepicker({
					format: 'dd/mm/yyyy',
				});

				$('.start-date-datepicker-dependent').datepicker({
					format: 'dd/mm/yyyy',
				});

				$('.start-date-datepicker').datepicker().on('hide', function (evt) {
					// console.log(evt);
					var val = $('.start-date-datepicker').val();
					if (val == "") {
						$('.start-date-datepicker').datepicker('setDate', scope.customer_data.plan.plan_start);
					}
				})

				$('.start-date-datepicker-dependent').datepicker().on('hide', function (evt) {
					// console.log(evt);
					var val = $('.start-date-datepicker-dependent').val();
					if (val == "") {
						$('.start-date-datepicker-dependent').datepicker('setDate', scope.customer_data.plan.plan_start);
					}
				})

				$('.modal').on('hidden.bs.modal', function () {
					scope.isEditDetailModalOpen = false;
					// iti.destroy();
					iti2.destroy();
					console.log(iti);
					console.log(iti2);
				})


				window.addEventListener('beforeunload', async function (e) {
					console.log(e);
					if(scope.isReviewEnroll){
						e.preventDefault(); 
						e.returnValue = ''; 
						await scope.removeAllTempEmployeeOnClose();
					}
				});

				

			}
		}
	}
]);
