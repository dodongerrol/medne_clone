app.directive('bulkCreditAllocationDirective', [ //creditAllocationDirective
	'$state',
	'hrSettings',
	'$rootScope',
	'$timeout',
	'dashboardFactory',
	function directive($state,hrSettings,$rootScope, $timeout,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("bulkCreditAllocationDirective Runnning !");

        // Employee List ----
				scope.employees = {};
				scope.totalAllocation = {};
        // ------------------

        // Check Session --
				scope.options = {};
				scope.spending_account_status = {};
        // ----------------
        
        // pagination -----
				scope.page_ctr = 5;
				scope.page_active = 1;
        scope.employees_pagi;
        scope.emp_last_page;
        scope.no_result_err;
				// -----------------
				
				// Modal trigger ---
				scope.showUploadModal = false; // close
				scope.bulkCreditFile = {};
				// -----------------
        
        scope.company_properties = {};
        scope.company_properties.total_allocation = 0.00;
        scope.company_properties.allocated = 0.00;
        
				scope.$on( 'refresh', function( evt, data ){
					scope.onLoad();
	    	});

	    	scope.passwordCredit = function( pass ){
					if( !pass || pass == '' ){
						scope.show_error = true;
						scope.err_mess = "Please input your password"
					}else{
						scope.show_error = false;
						var data = {
							password : pass
						}
						$('#password-submit').attr('disabled', true);
						$('#password-submit').text('Checking...');
						hrSettings.sendPassword( data )
						.then(function(response){
							if( response.data.status == false ){
								scope.show_error = true;
								scope.err_mess = response.data.message;
							}else{
								scope.show_error = false;
								scope.closePass();
								scope.passCredit = "";
								scope.updateCredit(scope.selected_emp);
							}
							$('#password-submit').attr('disabled', false);
							$('#password-submit').text('Submit');
						});
					}
				}

				scope.closePass = function( ) {
					$('#input-pass').modal('hide');
				}

				scope.getEmployeeList = function( ){
					scope.showLoading();
					$('.employee-overview-pagination').show();
					hrSettings.getEmployees(scope.page_ctr, scope.page_active)
						.then(function(response){
							console.log(response);
							scope.employees = response.data.data;
							scope.employees_pagi = response.data;
							scope.emp_last_page = response.data.last_page;

							angular.forEach( scope.employees, function( value, key ){ 

								scope.employees[key].creditAllocSpendingTypeText = 'medical';
								scope.employees[key].creditAllocSpendingType = 0;
								scope.employees[key].creditAllocTransactionType = 0;
								scope.employees[key].add_credit = 0;
								scope.employees[key].deduct_credit = 0;
								scope.employees[key].loading = false;
								scope.employees[key].success = false;
								scope.employees[key].failed = false;

								if( key == (scope.employees.length-1) ){
									scope.hideLoading();
								}
							});

							if( scope.employees.length == 0 ){
								scope.hideLoading();
								scope.no_result_err = true;
								$('.employee-overview-pagination').hide();
							}else{
								scope.no_result_err = false;
								$('.employee-overview-pagination').show();
							}
						});
				}

				scope.getEmployeeBulkCredit = function() {

					scope.showLoading();
					hrSettings.getEmployeeBulkAllocation(scope.page_ctr, scope.page_active)
						.then(function(response){
							console.log(response);
							scope.employees = response.data.members.data;
							scope.employees_pagi = response.data.members;
							scope.totalAllocation = response.data;
							scope.hideLoading();
							scope.inititalizeDatepicker();
						});
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

		  	scope.checkSession = function( ){
					hrSettings.getSession( )
	        	.then(function(response){
							scope.options.accessibility = response.data.accessibility;
	        	});
				}

        scope.checkCompanyBalance = function(){
					hrSettings.getCheckCredits();
				}
				
        scope.userCompanyCreditsAllocated = function(){
					hrSettings.userCompanyCreditsAllocated( )
					.then(function(response){
						console.log(response);
						scope.company_properties = response.data;
					});
        }

        $("body").click(function(e){
          if ( $(e.target).parents(".per-page-pagination").length === 0) {
            $(".per_page").hide();
          }
				});
				
				scope.fileUploadModal = function() {
					console.log('click');
					scope.showUploadModal = !scope.showUploadModal;
				}

				scope.uploadFile = function () {
					scope.showUploadModal = false;
					swal({
						title: '',
						text: `The allocation amount has been successfully updated.`,
						html: true,
						showCancelButton: false,
						confirmButtonText: 'Close',
						customClass : 'allocationEntitlementSuccessModal'
					});

				};

				scope.downloadFile = function () {

					var token = localStorage.getItem("token");
					hrSettings.downloadBulkAllocation( token );
				};

				scope.getSpendingAcctStatus = function () {
          hrSettings.getSpendingAccountStatus()
						.then(function (response) {
							console.log(response);
              scope.spending_account_status = response.data;
						});
				}
				scope.testDate = '';
				scope.inititalizeDatepicker = function () {
					$timeout(function () {
						console.log('initialize date');
					
						var dt = new Date();
						dt.setFullYear(new Date().getFullYear() - 18);
						$('.datepicker').datepicker({
							format: 'dd/mm/yyyy',
							// endDate : dt
						});
					}, 300);
				}

        scope.onLoad = function( ) {
        	scope.checkSession( );
					// scope.getEmployeeList( );
					scope.getSpendingAcctStatus();
					scope.getEmployeeBulkCredit();
					scope.inititalizeDatepicker();
					// scope.companyAccountType ();
        }

				// scope.checkCompanyBalance();
				scope.userCompanyCreditsAllocated();
	      scope.onLoad();
				}

		}
	}
]);
