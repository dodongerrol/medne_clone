app.directive('benefitsTiersDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	'$timeout',
	'dependentsSettings',
	'$compile',
	function directive($state,hrSettings,dashboardFactory,$timeout,dependentsSettings,$compile) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("benefitsTiersDirective Runnning !");

				scope.alphabet = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
				scope.tier_arr = [];
				scope.dependent_arr = [];
				scope.employee_arr = [];
				scope.temp_employees = [];
				scope.previewTable_arr = [];
				scope.tier_data = {
					gp_cap_status : false,
				};
				scope.reviewExcelData = {
					format : false,
					name : false,
					dob : false,
					email : false,
					postcode : false,
					relationship : false,
				}
				scope.dependent_data = {};
				scope.added_dependent_data = {};
				scope.employee_data = {
					medical_credits : 0,
					wellness_credits : 0,
					dependents : [],
				};
				scope.upload_file_dependent = {};
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
				


				scope.getLetter = function(index) {

				  return scope.alphabet[index];
				};

				scope.selectEmpDepTab = function(opt){
					scope.selected_emp_dep_tab = opt;
					if( opt == 1 ){
						scope.isWebInput = true;
						scope.showDependentsAdded = false;
						scope.isDependentListShow = false;
						scope.inititalizeDatepicker();
					}else{
						scope.isWebInput = false;
						scope.showDependentsAdded = true;
						scope.isDependentListShow = true;
						scope.added_dependent_ctr = 0;
						scope.added_dependent_data = scope.employee_data.dependents[scope.added_dependent_ctr];
					}
				}

				scope.toggleGPcapStatus = function(opt){

					scope.tier_data.gp_cap_status = opt;
				}

				scope.nextBtn = function(){
					if( scope.isTierBtn == true ){
						scope.isTierBtn = false;
						scope.isTierInput = true;	
					}else if( scope.isTierInput == true ){

					}else if( scope.isTierSummary == true ){
						scope.isTierSummary = false;
						scope.isNextBtnDisabled = true;
						scope.isEnrollmentOptions = true;
						scope.isExcelSelected = false;
						scope.isWebInputSelected = false;
					}else if( scope.isEnrollmentOptions == true ){
						scope.isEnrollmentOptions = false;
						if( scope.isExcelSelected == true ){
							scope.isExcel = true;
						}else{
							scope.isWebInput = true;
							scope.isFromUpload = false;
							scope.inititalizeDatepicker();
							$('.summary-right-container').show();
							scope.employee_data.dependents = [];
						}
					}else if( scope.isExcel == true ){
						if( scope.downloadWithDependents != null ){
							scope.isExcel = false;
							scope.downloadWithDependentsCheckbox = true;
							scope.download_step = 2;
						}else{
							swal( 'Error!', 'Please select an option for you template.', 'error' );
						}
					}else if( scope.downloadWithDependentsCheckbox == true ){
						if( scope.reviewExcelData.format && scope.reviewExcelData.name && 
								scope.reviewExcelData.dob && scope.reviewExcelData.email && 
								scope.reviewExcelData.postcode ){
							if( scope.downloadWithDependents == true ){
								if( scope.reviewExcelData.relationship ){
									scope.downloadWithDependentsCheckbox = false;
									scope.isUploadFile = true;
									scope.download_step = 3;
								}else{
									swal('Error!','please review your downloaded file and check the boxes.');
								}
							}else{
								scope.downloadWithDependentsCheckbox = false;
								scope.isUploadFile = true;
								scope.download_step = 3;
							}
						}else{
							swal('Error!','please review your downloaded file and check the boxes.');
						}
						
					}else if( scope.isUploadFile == true ){
						scope.isUploadFile = false;
						scope.getEnrollTempEmployees();
						scope.isReviewEnroll = true;
						scope.isFromUpload = true;
						scope.download_step = 4;
					}
				}

				scope.backBtn = function(){
					scope.isEditActive = false;
					if( scope.isTierBtn == true ){
						$state.go('enrollment-options');
						// $state.go('benefits-dashboard');
					}else if( scope.isTierInput == true ){
						scope.isTierInput = false;
						if( scope.tier_arr.length > 0 ){
							scope.isTierSummary = true;
						}else{
							scope.isTierBtn = true;
						}
					}else if( scope.isTierSummary == true ){
						$state.go('enrollment-options');
						// $state.go('benefits-dashboard');
					}else if( scope.isEnrollmentOptions == true ){
						if( scope.isTiering == true || scope.isTiering == 'true' ){
							scope.isTierSummary = true;
							scope.isEnrollmentOptions = false;
						}else{
							$state.go('enrollment-options');
						}
						// $state.go('benefits-dashboard');
					}else if( scope.isExcel == true || scope.isWebInput == true ){
						scope.isEnrollmentOptions = true;
						scope.isExcel = false;
						scope.isWebInput = false;
						$('.summary-right-container').hide();
					}else if( scope.isReviewEnroll == true ){
						$state.go('enrollment-options');
						localStorage.setItem('fromEmpOverview', false);
          				// $state.go('create-team-benefits-tiers');
          				scope.isAllPreviewEmpChecked = false;
          				scope.isReviewEnroll = false;
          				scope.isEnrollmentOptions = true;
					}else if( scope.downloadWithDependentsCheckbox == true ){
						scope.downloadWithDependentsCheckbox = false;
						scope.isExcel = true;
						scope.download_step = 1;
					}else if( scope.isUploadFile == true ){
						scope.isUploadFile = false;
						scope.isExcel = true;
						scope.download_step = 1;
					}
				}

				scope.addTierBtn = function(){
					scope.tier_data = {
						gp_cap_status : false,
					};

					scope.isTierBtn = false;
					scope.isTierSummary = false;
					scope.toggleTierLoader();
					$timeout(function() {
						scope.toggleTierLoader();
						scope.isTierInput = true;
					}, 500);
				}

				scope.toggleTierLoader = function(){
					if( scope.isTierLoaderShow == false ){
						scope.isTierLoaderShow = true;
					}else{
						scope.isTierLoaderShow = false;
					}
				}

				scope.saveTierData = function( data ){
					if( data.medical_annual_cap == 0 || data.wellness_annual_cap == 0 || data.gp_cap_per_visit == 0 || data.member_head_count == 0){
						swal( 'Error!', "Input values should be 1 or more", 'error' );
						return false;
					}
					if( data.gp_cap_status == true && (!data.gp_cap_per_visit || data.gp_cap_per_visit == 0) ){
						swal( 'Error!', "Input values should be 1 or more", 'error' );
						return false;
					}
					scope.lastContentShown = 1;
					if( scope.isEditActive == true ){
						scope.updateBenefitsTier( data );
					}else{
						scope.saveBenefitsTier( data );
					}
				}

				scope.editTierData = function( data, index ){
					scope.lastContentShown = 2;
					scope.selected_edit_tier_index = index;
					if( data.gp_cap_per_visit > 0 ){
						data.gp_cap_status = true;
					}
					scope.tier_data = data;
					scope.isEditActive = true;
					scope.toggleTierLoader();
					scope.isTierSummary = false;
					$timeout(function() {
						scope.toggleTierLoader();
						scope.isTierInput = true;
					}, 500);
				}

				scope.tierSummaryClicked = function( data, index ){
					if( data.member_enrolled_count == data.member_head_count ){
						swal('Error!', 'Employee head count is full', 'error');
					}else{
						angular.forEach( scope.tier_arr, function(value,key){
							value.isSelected = false;
						});
						data.isSelected = true;
						scope.tierSelected = data;
						scope.tierSelected.index = index - 1;
						scope.isNextBtnDisabled = false;
						scope.employee_enroll_count = data.member_enrolled_count + 1;
						scope.dependents_enroll_count = data.dependent_enrolled_count + 1;
						scope.employee_data = {
							medical_credits : scope.tierSelected.medical_annual_cap,
							wellness_credits : scope.tierSelected.wellness_annual_cap,
							dependents : [],
							plan_start : scope.customer_data.plan.plan_start
						};
					}
				}

				scope.toggleAddDependents = function( ){
					if( scope.isAddDependentsShow == false ){
						if( scope.isTiering ){
							if( scope.dependents_enroll_count > scope.tierSelected.dependent_head_count ){
								swal('Error!', 'Maximum Tier dependent head count already reached.', 'error');
								return false;
							}
						}else{
							if( scope.overall_dep_count > scope.dependents.total_number_of_seats ){
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
					}else{
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
	          function(isConfirm){
	          	if(isConfirm){
	          		scope.$apply(function(){
	          			scope.isAddDependentsShow = false;
									scope.isBackBtnDisabled = false;
									scope.isNextBtnDisabled = false;
									scope.isWebInput = true;
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

				scope.showSummary = function(){
					scope.toggleTierLoader();
					$('.tier-feature-item').hide();
					scope.lastContentShown = '.benefits-tier-summary-container-wrapper';
					$timeout(function() {
						scope.toggleTierLoader();
						$('.benefits-tier-summary-container-wrapper').fadeIn();
					}, 1000);
				}

				scope.openEditDetailsModal = function( index ){
					scope.selected_edit_details_data = scope.temp_employees[index];
					$("#edit-employee-details").modal('show');
					$('.edit-employee-details-form .datepicker').datepicker('setDate', scope.selected_edit_details_data.employee.dob);
					$('.edit-employee-details-form .start-date-datepicker').datepicker('setDate', scope.selected_edit_details_data.employee.start_date);
					$timeout(function() {
						var dt = new Date();
						dt.setFullYear(new Date().getFullYear()-18);
						$('.datepicker').datepicker({
							format: 'dd/mm/yyyy',
							// endDate : dt
						});
						$('.start-date-datepicker').datepicker({
							format: 'dd/mm/yyyy',
						});
						$('.start-date-datepicker').datepicker().on('hide',function(evt){
							// console.log(evt);
							var val = $('.start-date-datepicker').val();
							if( val == "" ){
								$('.start-date-datepicker').datepicker('setDate', scope.customer_data.plan.plan_start);
							}
						})
						$('.start-date-datepicker-dependent').datepicker({
							format: 'dd/mm/yyyy',
						});
						$('.start-date-datepicker-dependent').datepicker().on('hide',function(evt){
							// console.log(evt);
							var val = $('.start-date-datepicker-dependent').val();
							if( val == "" ){
								$('.start-date-datepicker-dependent').datepicker('setDate', scope.customer_data.plan.plan_start);
							}
						})
					}, 300);
				}

				scope.removeDependent = function( index ){
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
          function(isConfirm){
            if(isConfirm){
            	scope.toggleTierLoader();
							scope.employee_data.dependents.splice( index , 1 );
							scope.toggleTierLoader();
							scope.$apply(function(){
								if( scope.isTiering ){
									scope.dependents_enroll_count -= 1;
								}else{
									scope.overall_dep_count -= 1;
								}
								if( scope.employee_data.dependents.length > 0 ){
									scope.isWebInput = false;
									scope.added_dependent_ctr = 0;
									scope.added_dependent_data = scope.employee_data.dependents[0];
								}else{
									scope.isWebInput = true;
									scope.inititalizeDatepicker();
									scope.showDependentsAdded = false;
									scope.isDependentListShow = false;
								}
							});
							
            }
          });
				}

				scope.prevDependentActive = function(){
					if( scope.dependent_ctr != 0 ){
						if( scope.dependent_arr[ scope.dependent_ctr ] ){
							scope.dependent_arr[ scope.dependent_ctr ] = scope.dependent_data;
						}
						if( scope.isTiering ){
							scope.dependents_enroll_count -= 1;
						}else{
							scope.overall_dep_count -= 1;
						}
						scope.dependent_ctr -= 1;
						if( scope.dependent_arr[ scope.dependent_ctr ] ){
							scope.dependent_data = scope.dependent_arr[ scope.dependent_ctr ];
						}else{
							scope.dependent_data = {}
						}
					}
				}

				scope.nextDependentActive = function(){
					if( scope.dependents_enroll_count < scope.dependents.total_number_of_seats ){
						if( scope.dependent_arr[ scope.dependent_ctr ] ){
							scope.dependent_arr[ scope.dependent_ctr ] = scope.dependent_data;
						}
						if( scope.isTiering ){
							scope.dependents_enroll_count += 1;
						}else{
							scope.overall_dep_count += 1;
						}
						scope.dependent_ctr += 1;
						if( scope.dependent_arr[ scope.dependent_ctr ] ){
							scope.dependent_data = scope.dependent_arr[ scope.dependent_ctr ];
						}else{
							scope.dependent_data = {}
						}
					}
				}

				scope.prevAddedDependent = function(){
					if( scope.added_dependent_ctr != 0 ){
						scope.added_dependent_ctr -= 1;
						scope.added_dependent_data = scope.employee_data.dependents[ scope.added_dependent_ctr ];
					}
				}

				scope.nextAddedDependent = function(){
					if( scope.added_dependent_ctr < (scope.employee_data.dependents.length - 1) ){
						scope.added_dependent_ctr += 1;
						scope.added_dependent_data = scope.employee_data.dependents[ scope.added_dependent_ctr ];
					}
				}

				scope.pushActiveDependent = function( data ){
					console.log(data);
					if( scope.isTiering ){
						if( scope.dependents_enroll_count > scope.tierSelected.dependent_head_count ){
							swal('Error!', 'Maximum Tier dependent head count already reached.', 'error');
							return false;
						}
					}else{
						if( scope.overall_dep_count > scope.dependents.total_number_of_seats ){
							swal('Error!', 'Maximum dependent head count already reached.', 'error');
							return false;
						}
					}
					if( scope.checkDependentForm() == true ){
						data.done = true;
						scope.dependent_arr.push(data);
						if( scope.isTiering ){
							if( scope.dependents_enroll_count <= scope.tierSelected.dependent_head_count ){
								scope.dependents_enroll_count += 1;
							}
						}else{
							if( scope.overall_dep_count <= scope.dependents.total_number_of_seats ){
								scope.overall_dep_count += 1;
							}
						}
						scope.dependent_ctr+=1;
						scope.dependent_data = {};
						scope.showLoading();
						scope.hideLoading();

						if( (scope.dependents_enroll_count > scope.tierSelected.dependent_head_count) || (scope.overall_dep_count > scope.dependents.total_number_of_seats) ){
							if( scope.employee_data.dependents.length > 0 ){
								angular.forEach( scope.dependent_arr, function(value, key){
									scope.employee_data.dependents.push( value );
								});
							}else{
								scope.employee_data.dependents = scope.dependent_arr;
							}
							swal('Success', 'Dependents successfully added under this employee.', 'success');
							scope.isAddDependentsShow = false;
							scope.isBackBtnDisabled = false;
							scope.isNextBtnDisabled = false;
							scope.isWebInput = true;
							scope.dependent_arr = [];
							scope.dependent_data = {};
							scope.dependent_data.plan_start = scope.customer_data.plan.plan_start;
							scope.dependent_ctr = 0;
							scope.selected_emp_dep_tab = 1;
							scope.inititalizeDatepicker();
						}
						
					}
				}

				scope.saveActiveDependents = function( ){
					if( !scope.dependent_data.first_name && !scope.dependent_data.last_name && !scope.dependent_data.nric && 
								!scope.dependent_data.dob && !scope.dependent_data.relationship && !scope.dependent_data.plan_start ){
					}else{
						if( scope.checkDependentForm() == true ){scope.dependent_arr.push( scope.dependent_data );
							if( scope.isTiering ){
								scope.dependents_enroll_count += 1;
							}else{
								scope.overall_dep_count += 1;
							}
							scope.dependent_ctr+=1;
							scope.dependent_data = {};
						}else{
							return false;
						}
					}
					if( scope.employee_data.dependents.length > 0 ){
						angular.forEach( scope.dependent_arr, function(value, key){
							scope.employee_data.dependents.push( value );
						});
					}else{
						scope.employee_data.dependents = scope.dependent_arr;
					}
					scope.showLoading();
					scope.hideLoading();
					swal('Success', 'Dependents successfully added under this employee.', 'success');
					scope.isAddDependentsShow = false;
					scope.isBackBtnDisabled = false;
					scope.isNextBtnDisabled = false;
					scope.isWebInput = true;
					scope.dependent_arr = [];
					scope.dependent_data = {};
					scope.dependent_data.plan_start = scope.customer_data.plan.plan_start;
					scope.dependent_ctr = 0;
					scope.selected_emp_dep_tab = 1;
					scope.inititalizeDatepicker();
				}

				scope.deleteActiveDependents = function(){
					scope.employee_data = {
						medical_credits : 0,
						wellness_credits : 0,
						dependents : [],
						plan_start : scope.customer_data.plan.plan_start
					};

					if( scope.employee_arr[ scope.employee_ctr ] ){
						scope.employee_arr[ scope.employee_ctr ] = scope.employee_data;
					}

					console.log( scope.employee_arr );
				}

				scope.isEmpDataNotEmpty = function(){
					if( scope.employee_data.first_name || scope.employee_data.last_name || scope.employee_data.nric || scope.employee_data.dob ||
							scope.employee_data.email || scope.employee_data.mobile || scope.employee_data.postal_code ){
						return true;
					}
					return false;
				}

				scope.pushActiveEmployee = function( data ){
					if( scope.checkEmployeeForm() == true ){
						data.job_title = 'Others';
						data.postcode = '12345';
						scope.employee_arr.push(data);
						if( scope.isTiering ){
							if( scope.employee_enroll_count != scope.tierSelected.member_head_count ){
								if( scope.isTiering ){
									scope.employee_data.medical_credits = scope.tierSelected.medical_annual_cap;
									scope.employee_data.wellness_credits = scope.tierSelected.wellness_credits;
								}
								scope.employee_enroll_count += 1;
							}
						}else{
							if( scope.overall_emp_count != scope.progress.total_employees ){
								scope.overall_emp_count+=1;
							}
						}
						scope.employee_ctr+=1;	
						scope.employee_data = {
							medical_credits : 0,
							wellness_credits : 0,
							dependents : [],
							plan_start : scope.customer_data.plan.plan_start
						};
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

				scope.prevActiveEmployee = function() {
					if( scope.employee_arr[ scope.employee_ctr ] == undefined ){
						scope.employee_arr[ scope.employee_ctr ] = scope.employee_data;
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
	          			if( scope.isTiering ){
										scope.employee_enroll_count-=1;
									}else{
										scope.overall_emp_count-=1;
									}
									scope.employee_ctr-=1;
									scope.employee_data = scope.employee_arr[ scope.employee_ctr ];
									if( scope.employee_data.dependents.length > 0 ){
										scope.added_dependent_data = scope.employee_data.dependents[0];
									}
	     //      		});
	     //      	}
	     //      });
					}else{
						if( scope.isTiering ){
							scope.employee_enroll_count-=1;
						}else{
							scope.overall_emp_count-=1;
						}
						scope.employee_ctr-=1;
						scope.employee_data = scope.employee_arr[ scope.employee_ctr ];
						if( scope.employee_data.dependents.length > 0 ){
							scope.added_dependent_data = scope.employee_data.dependents[0];
						}
					}
					
				}

				scope.nextActiveEmployee = function() {
					if( scope.checkEmployeeForm() == true ){
						if( scope.isTiering ){
							if( scope.employee_arr[ scope.employee_ctr ] ){
								scope.employee_enroll_count++;
								scope.employee_ctr+=1;
								scope.employee_data = scope.employee_arr[ scope.employee_ctr];
								if( !scope.employee_data ){
									scope.employee_data = {
										medical_credits : 0,
										wellness_credits : 0,
										dependents : [],
										plan_start : scope.customer_data.plan.plan_start
									};
								}
								if( scope.employee_data.dependents.length > 0 ){
									scope.added_dependent_data = scope.employee_data.dependents[0];
								}
							}else{
								scope.pushActiveEmployee( scope.employee_data );
							}
						}else{
							if( scope.employee_arr[ scope.employee_ctr ] ){
								scope.overall_emp_count++;
								scope.employee_ctr+=1;
								scope.employee_data = scope.employee_arr[ scope.employee_ctr];
								if( !scope.employee_data ){
									scope.employee_data = {
										medical_credits : 0,
										wellness_credits : 0,
										dependents : [],
										plan_start : scope.customer_data.plan.plan_start
									};
								}
								if( scope.employee_data.dependents.length > 0 ){
									scope.added_dependent_data = scope.employee_data.dependents[0];
								}
							}else{
								scope.pushActiveEmployee( scope.employee_data );
							}
						}
					}
				}

				scope.selectEnrollmentMethod = function( opt ){
					if( opt == 0 ){
						scope.isExcelSelected = true;
						scope.isWebInputSelected = false;
					}else{
						scope.isExcelSelected = false;
						scope.isWebInputSelected = true;
					}
					scope.isNextBtnDisabled = false;
				}

				scope.checkNRIC = function(theNric){
					var nric_pattern = new RegExp('^[stfgSTFG]{1}[0-9]{7}[a-zA-z]{1}$');
					return nric_pattern.test(theNric);
				};

				scope.checkEmail = function(email){
					var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					return regex.test(email);
				}

				scope.downloadTemplate = function(){
					if( scope.downloadWithDependents != null ){
						if( scope.downloadWithDependents == false ){
							window.location.href = '/excel/Employee Enrollment Listing.xlsx';
						}else{
							window.location.href = '/excel/Employees and Dependents.xlsx';
						}
					}else{
						swal( 'Error!', 'Please select an option for you template.', 'error' );
					}
				}

				scope.downloadExcelTemplate = function(opt){
					if( opt == 0 ){
						scope.downloadWithDependents = false;
					}
					if( opt == 1 ){
						scope.downloadWithDependents = true;
					}
				}

				scope.runUpload = function( file ) {
					// console.log(file);
					var data = {
						file : file,
						plan_start : moment().format('YYYY-MM-DD'),
					}
					if( scope.isTiering == true || scope.isTiering == 'true' ){
						data.plan_tier_id = scope.tierSelected.plan_tier_id;
					}
					scope.showLoading();
        	hrSettings.uploadExcel( data )
	        	.then(function(response){
	        		// console.log( response );
	        		scope.hideLoading();
	        		if( response.data.status == true ){
	        			scope.isInvalid = false;
								scope.isValid = true;
	        			scope.isNextBtnDisabled = false;
	        			scope.message = 'Successfully Uploaded.';
	        			swal( 'Success!', 'uploaded.', 'success' );
	        		}else{
	        			scope.isInvalid = true;
								scope.isValid = false;
	        			scope.isNextBtnDisabled = true;
	        			console.log(response);
	        			console.log(response.data);
	        			swal( 'Error!', response.data.message, 'error' );
	        		}
	        		
	        	});
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
					if( !scope.employee_data.email && !scope.employee_data.mobile ){
						swal( 'Error!', 'Email or Mobile is required.', 'error' );
						return false;
					}
					if( scope.employee_data.email ){
						if( scope.checkEmail(scope.employee_data.email) == false ){
							swal( 'Error!', 'Email is invalid.', 'error' );
							return false;
						}
					}
					// if( !scope.employee_data.mobile ){
					// 	swal( 'Error!', 'Phone is required.', 'error' );
					// 	return false;
					// }
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
						if( scope.nric_status_dependents == true ){
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
						scope.dependent_data.relationship = null;
						// swal( 'Error!', 'Relationship is required.', 'error' );
						// return false;
					}
					if( !scope.dependent_data.plan_start ){
						swal( 'Error!', 'Start Date is required.', 'error' );
						return false;
					}

					return true;
				}

				scope.reloadPage = function(){

					window.location.reload();
				}

				scope.empCheckBoxClicked = function( index ){
					var check = $.inArray( scope.temp_employees[index].employee.temp_enrollment_id, scope.previewTable_arr );
					if( check < 0 ){
						scope.previewTable_arr.push( scope.temp_employees[index].employee.temp_enrollment_id );
					}else{
						scope.previewTable_arr.splice( check, 1 );
					}
					if( scope.previewTable_arr.length > 0 ){
						$('.preview-trash-icon').show();
					}else{
						$('.preview-trash-icon').hide();
					}
				}

				scope.empCheckBoxAll = function(){
					scope.previewTable_arr = [];
					if( scope.isAllPreviewEmpChecked == false ){
						scope.isAllPreviewEmpChecked = true;
						angular.forEach( scope.temp_employees,function(value, key){
							value.checkboxSelected = true;
							scope.previewTable_arr.push( value.employee.temp_enrollment_id );
						});
					}else{
						angular.forEach( scope.temp_employees,function(value, key){
							value.checkboxSelected = false;
						});
						scope.isAllPreviewEmpChecked = false;
					}
				}



				// HTTP REQUEST

				scope.enrollEmployees = function(){
					var emp_arr = [];
					if( !scope.employee_data.first_name && !scope.employee_data.last_name && !scope.employee_data.nric && !scope.employee_data.dob 
							&& !scope.employee_data.mobile && !scope.employee_data.email && !scope.employee_data.postal_code ){

					}else{
						if( scope.checkEmployeeForm() == true ){
							if( !scope.employee_arr[scope.employee_ctr] ){
								scope.employee_data.job_title = 'Others';
								scope.employee_arr.push( scope.employee_data );
							}
						}else{
							return false;
						}
					}
					angular.forEach( scope.employee_arr,function(value,key){
						if( value.first_name && value.last_name && value.nric && value.dob && value.postal_code && value.plan_start ){
							emp_arr.push( value );
						}

						if( key == scope.employee_arr.length-1 ){

							angular.forEach( emp_arr,function(value,key){
								console.log( value.dob );
								// value.dob = moment( value.dob ).format('YYYY-MM-DD');
								// value.plan_start = moment( value.plan_start, 'DD/MM/YYYY').format('YYYY-MM-DD');
								// value.plan_start = moment( value.plan_start, 'DD/MM/YYYY').format('YYYY-MM-DD');

								angular.forEach( value.dependents,function(value2,key2){
									value2.dob = moment( value2.dob ).format('YYYY-MM-DD');
									value2.plan_start = moment( value2.plan_start, 'DD/MM/YYYY').format('YYYY-MM-DD');
								})
							});
							$('.tier-feature-item').hide();
							scope.toggleTierLoader();
							var data = {
								employees : emp_arr,
								plan_tier_id : null
							}
							if( scope.isTiering == true || scope.isTiering == 'true' ){
								data.plan_tier_id = scope.tierSelected.plan_tier_id;
							}
							dependentsSettings.addEnrollEmployees( data )
							.then(function(response){
									// console.log( response );
									$timeout(function() {
										scope.toggleTierLoader();
										if( response.data.status ){
											scope.getBenefitsTier();
											scope.getEnrollTempEmployees();
											scope.isWebInput = false;
											$('.summary-right-container').hide();
											scope.isReviewEnroll = true;
											scope.employee_data = {
												medical_credits : 0,
												wellness_credits : 0,
												dependents : [],
												plan_start : scope.customer_data.plan.plan_start
											};
										}else{
											swal( 'Error!', response.data.message, 'error' );
										}
									}, 1000);
								});
						}
					});
				}

				scope.getBenefitsTier = function( ){
					scope.tier_arr = [];
					dependentsSettings.fetchBenefitsTier( )
					.then(function(response){
							// console.log( response );
							if( response.data.status ){
								scope.tier_arr = response.data.data;
								scope.selected_edit_tier_index = scope.tier_arr.length + 1;
								angular.forEach( scope.tier_arr, function(value,key){
									value.dependents = [];
									value.employees = [];
								});
							}else{
								swal( 'Error!', response.data.message, 'error' );
							}
						});
				}

				scope.getEnrollTempEmployees = function( ){
					scope.temp_employees = [];
					scope.hasError = false;
					$timeout(function() {
						$("#enrollee-details-tbl tbody").html('');
						$("#enrollee-details-tbl thead tr").html( $compile('<th><input type="checkbox" ng-click="empCheckBoxAll()"></th><th>First Name</th><th>Last Name</th><th>NRIC/FIN</th><th>Date of Birth</th><th>Work Email</th><th>Mobile</th><th>Medical Credits</th><th>Wellness Credits</th>')(scope) );
						dependentsSettings.getTempEmployees( )
							.then(function(response){
								// console.log( response );
								scope.temp_employees = response.data.data;
								angular.forEach( scope.temp_employees, function(ctr_value, ctr_key){
									if( ctr_value.dependents.length > scope.table_dependents_ctr ){
										scope.table_dependents_ctr = ctr_value.dependents.length;
									}
									if( (scope.temp_employees.length-1) == ctr_key ){
										angular.forEach( scope.temp_employees, function(value, key){
											// console.log(value);
											if( value.error_logs.error == true ){
												scope.hasError = true;
											}
											value.success = false;
											value.fail = false;
											scope.isTrError = ( value.error_logs.error == true ) ? 'has-error' : '';
											var html_tr = '<tr class="dependent-hover-container '+ scope.isTrError +' "><td><input type="checkbox" ng-model="temp_employees[' + key + '].checkboxSelected" ng-click="empCheckBoxClicked(' + key + ')"></td><td><span class="icon"><i class="fa fa-check" style="display: none;"></i><i class="fa fa-times" style="display: none;"></i><i class="fa fa-circle-o-notch fa-spin" style="display: none;"></i></span><span class="fname">' + value.employee.first_name + '</span><button class="dependent-hover-btn" ng-click="openEditDetailsModal('+ key +')">Edit</button></td><td>' + value.employee.last_name + '</td><td>' + value.employee.nric + '</td><td>' + value.employee.dob + '</td><td>' + value.employee.email + '</td><td>' + value.employee.format_mobile + '</td><td>' + value.employee.credits + '</td><td>' + value.employee.wellness_credits + '</td>';
											var emp_ctr = 0;
											while( emp_ctr != value.dependents.length ){
												scope.isTrError = ( value.dependents[emp_ctr].error_logs.error == true ) ? 'has-error' : '';
												html_tr += '<td>' + value.dependents[emp_ctr].enrollee.first_name + '</td><td>' + value.dependents[emp_ctr].enrollee.last_name + '</td><td>' + value.dependents[emp_ctr].enrollee.nric + '</td><td>' + value.dependents[emp_ctr].enrollee.dob + '</td><td>' + value.dependents[emp_ctr].enrollee.relationship + '</td>';
												emp_ctr++;
											}	
											while( emp_ctr != scope.table_dependents_ctr ){
												html_tr += '<td></td><td></td><td></td><td></td><td></td>';
												emp_ctr++;
											}
											html_tr += '<td>' + value.employee.start_date + '</td></tr>';

											$("#enrollee-details-tbl tbody").append( $compile(html_tr)(scope) );

											if( (scope.temp_employees.length-1) == key ){
												var while_ctr = 0;
												while( while_ctr != scope.table_dependents_ctr ){
													while_ctr++;
													$("#enrollee-details-tbl thead tr").append( 
														'<th>Dependent ' + while_ctr + '<br>First Name</th>' + 
														'<th>Dependent ' + while_ctr + '<br>Last Name</th>' + 
														'<th>Dependent ' + while_ctr + '<br>NRIC/FIN</th>' + 
														'<th>Dependent ' + while_ctr + '<br>Date of Birth</th>' + 
														'<th>Dependent ' + while_ctr + '<br>Relationship</th>' 
													);
												}	
												$("#enrollee-details-tbl thead tr").append( '<th class="start-date-header">Start Date</th>' );
												scope.hideLoading();
											}
										});
									}
								})
							});
					}, 200);
				}

				scope.updateEnrolleEmp = function( emp ){
					if( emp.employee.email == "" && emp.employee.mobile == "" ){
      			swal("Error!", "Email Address or Mobile Number is required.", 'error');
      			return false;
      		}

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
          function(isConfirm){
          	if(isConfirm){
          		scope.showLoading();
							var data = {
								temp_enrollment_id : emp.employee.temp_enrollment_id,
								first_name: emp.employee.first_name,
								last_name: emp.employee.last_name,
								nric: emp.employee.nric,
								dob: moment(emp.employee.dob, 'DD/MM/YYYY').format('DD/MM/YYYY'),
								email: emp.employee.email,
								mobile: emp.employee.mobile,
								job_title: emp.employee.job_title,
								medical_credits: parseInt(emp.employee.credits),
								wellness_credits: parseInt(emp.employee.wellness_credits),
								plan_start: moment(emp.employee.start_date, 'DD/MM/YYYY').format('DD/MM/YYYY'),
								postal_code: emp.employee.postal_code,
								mobile_area_code: emp.employee.mobile_area_code
							}
							dependentsSettings.updateTempEnrollee( data )
							.then(function(response){
								// console.log(response);
								if( emp.dependents.length > 0 ){
									angular.forEach( emp.dependents, function(value,key){
										var dep_data = {
											dependent_temp_id : value.enrollee.dependent_temp_id,
											first_name : value.enrollee.first_name,
											last_name : value.enrollee.last_name,
											nric : value.enrollee.nric,
											dob : moment(value.enrollee.dob, 'DD/MM/YYYY').format('YYYY-MM-DD'),
											plan_start : moment(value.enrollee.plan_start, 'DD/MM/YYYY').format('YYYY-MM-DD'),
											relationship : value.enrollee.relationship,
										}
										dependentsSettings.updateTempDependent( dep_data )
										.then(function(response){
											// console.log(response);
											if( key == (emp.dependents.length-1) ){
												scope.hideLoading();
												scope.getEnrollTempEmployees();
												$(".modal").modal('hide');
											}
										});
									});
								}else{
									scope.getEnrollTempEmployees();
									$(".modal").modal('hide');
									scope.hideLoading();
								}
							});
          	}
          })
				}

				scope.removeManyEmp = function( ){
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
          function(isConfirm){
            if(isConfirm){
							angular.forEach( scope.previewTable_arr, function(value,key){
								dependentsSettings.deleteTempEmployees( value )
									.then(function(response){
											// console.log(response);
											if( (scope.previewTable_arr.length-1) == key ){
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

				scope.removeTempEmp = function( data ){
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
          function(isConfirm){
            if(isConfirm){
              dependentsSettings.deleteTempEmployees( data.employee.temp_enrollment_id )
								.then(function(response){
										// console.log(response);
										scope.getEnrollTempEmployees();
										$(".modal").modal('hide');
									});
            }
          });
				}

				scope.saveTempUser = function(){
					scope.showLoading();
					var err = 0;
					scope.current_enrolled_count = {
						total_dependents_enrolled : 0,
						total_employee_enrolled : 0,
					};
					angular.forEach( scope.temp_employees, function( value, key ){
						$('#enrollee-details-tbl tbody tr:nth-child(' + (key + 1) + ') .fa-circle-o-notch').fadeIn();
						var data = {
							temp_enrollment_id : value.employee.temp_enrollment_id
						}
						dependentsSettings.saveTempEnrollees( data )
							.then(function(response){
								// console.log( response );
								scope.current_enrolled_count.total_employee_enrolled += response.data.result.total_employee_enrolled;
								scope.current_enrolled_count.total_dependents_enrolled += response.data.result.total_dependents_enrolled;
								$('#enrollee-details-tbl tbody tr:nth-child(' + (key + 1) + ') .fa-circle-o-notch').hide();
								if( response.data.result){
									if( response.data.result.status == true ){
										value.success = true;
										value.fail = false;
										$('#enrollee-details-tbl tbody tr:nth-child(' + (key + 1) + ') .fa-check').fadeIn();
									}
								}else{
									value.success = false;
									value.fail = true;
									err++;
									$('#enrollee-details-tbl tbody tr:nth-child(' + (key + 1) + ') .fa-times').fadeIn();
								}
								if( key == scope.temp_employees.length-1 ){
									$timeout(function() {
										scope.hideLoading();
										scope.getEnrollTempEmployees();
										if( err == 0 ){
											scope.isReviewEnroll = false;
											scope.isSuccessfulEnroll = true;
											scope.isFromUpload = false;
											scope.employee_data = {
												medical_credits : 0,
												wellness_credits : 0,
												dependents : [],
												plan_start : scope.customer_data.plan.plan_start
											};
										}
									}, 1000);	
								}
							
						});
					});
				}

				scope.saveBenefitsTier = function( data ){
					$('.tier-feature-item').hide();
					scope.toggleTierLoader();
					dependentsSettings.addBenefitsTier( data )
					.then(function(response){
							// console.log( response );
							$timeout(function() {
								scope.toggleTierLoader();
								if( response.data.status ){
									scope.getBenefitsTier();
									scope.tier_data = {
										gp_cap_status : false,
									};
									scope.isTierInput = false;
									scope.isTierSummary = true;
								}else{
									swal( 'Error!', response.data.message, 'error' );
									$('.tier-item-container').fadeIn();
								}
							}, 1000);
						});
				}

				scope.updateBenefitsTier = function( data ){
					$('.tier-feature-item').hide();
					scope.toggleTierLoader();
					data.plan_tier_id = scope.tier_data.plan_tier_id;
					dependentsSettings.updateTier( data )
						.then(function(response){
							// console.log( response );
							scope.toggleTierLoader();
							if( response.data.status ){
								scope.isTierInput = false;
								scope.isTierSummary = true;
								scope.isEditActive = false;
								swal( 'Success!', response.data.message, 'success' );
							}else{
								swal( 'Error!', response.data.message, 'error' );
								$('.tier-item-container').fadeIn();
							}
							scope.getBenefitsTier();
						});
				}

				scope.removeTier = function(){
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
          function(isConfirm){
            if(isConfirm){
            	scope.toggleTierLoader();
            	var data = {
            		plan_tier_id : scope.tier_data.plan_tier_id
            	}
              dependentsSettings.deleteTier( data )
								.then(function(response){
										// console.log(response);
										scope.isTierInput = false;
										scope.isEditActive = false;
										scope.onLoad();
									});
            }
          });
				}

				scope.getMethod = function( ){
					hrSettings.getMethodType()
					.then(function(response){
						// console.log(response);
						scope.customer_data = response.data.data;
						scope.customer_data.plan.plan_start = moment(scope.customer_data.plan.plan_start).format('DD/MM/YYYY');
						scope.dependent_data.plan_start = scope.customer_data.plan.plan_start;
						scope.employee_data.plan_start = scope.customer_data.plan.plan_start;
					});
				}

				scope.getProgress = function( ) {
					hrSettings.getEnrollmentProgress()
					.then(function(response){
						scope.progress = response.data.data;
						scope.overall_emp_count = scope.progress.completed + 1;
						// console.log(scope.progress);
					});
				};

				scope.companyDependents = function( ) {
					hrSettings.companyDependents( )
					.then(function(response){
						scope.dependents = response.data;
						scope.overall_dep_count = scope.dependents.occupied_seats + 1;
						// console.log(scope.dependents);
					});
				}

				scope.inititalizeDatepicker = function( ){
					$timeout(function() {
						var dt = new Date();
						dt.setFullYear(new Date().getFullYear()-18);
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
						$('.start-date-datepicker').datepicker().on('hide',function(evt){
							var val = $('.start-date-datepicker').val();
							if( val == "" ){
								$('.start-date-datepicker').datepicker('setDate', scope.customer_data.plan.plan_start);
							}
						})
						$('.start-date-datepicker-dependent').datepicker().on('hide',function(evt){
							var val = $('.start-date-datepicker-dependent').val();
							if( val == "" ){
								$('.start-date-datepicker-dependent').datepicker('setDate', scope.customer_data.plan.plan_start);
							}
						})
					}, 300);
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

				scope.onLoad = function( ){
					if( localStorage.getItem('enrollmentOptionTiering') == 'true' || localStorage.getItem('enrollmentOptionTiering') == true ){
						scope.isTiering = true;
					}else{
						scope.isTiering = false;
					}

					scope.getProgress();
					scope.companyDependents();
					scope.getMethod();
					scope.getBenefitsTier();

					$timeout(function() {
						loading_trap = false;
						$( ".circle-loader" ).fadeOut();

						scope.toggleTierLoader();

						if( scope.isTiering == true || scope.isTiering == 'true' ){
							if( scope.tier_arr.length > 0 ){
								scope.isTierSummary = true;
							}else{
								scope.isTierBtn = true;
							}
						}else{
							scope.isTierSummary = false;
							scope.isTierBtn = false;
							scope.isEnrollmentOptions = true;
						}

						// scope.getEnrollTempEmployees();
						// scope.isReviewEnroll = true;
						
						scope.hideLoading();
					},500);
				}

				scope.onLoad();




				$("body").delegate( '.summary-right-button', 'click', function(e){
					$(".summary-right-container").toggleClass('show');
				});

				$("body").click(function(e){
			    if ( $(e.target).parents(".summary-right-container").length === 0) {
			      $(".summary-right-container").removeClass('show');
			    }
				});


				var dt = new Date();
				dt.setFullYear(new Date().getFullYear()-18);
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

				$('.start-date-datepicker').datepicker().on('hide',function(evt){
					// console.log(evt);
					var val = $('.start-date-datepicker').val();
					if( val == "" ){
						$('.start-date-datepicker').datepicker('setDate', scope.customer_data.plan.plan_start);
					}
				})

				$('.start-date-datepicker-dependent').datepicker().on('hide',function(evt){
					// console.log(evt);
					var val = $('.start-date-datepicker-dependent').val();
					if( val == "" ){
						$('.start-date-datepicker-dependent').datepicker('setDate', scope.customer_data.plan.plan_start);
					}
				})
			}
		}
	}
]);
