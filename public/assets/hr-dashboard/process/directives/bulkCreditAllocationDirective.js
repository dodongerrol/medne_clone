app.directive('bulkCreditAllocationDirective', [ //creditAllocationDirective
	'$state',
	'hrSettings',
	'$rootScope',
	'$timeout',
	'dashboardFactory',
	'Upload',
	function directive($state,hrSettings,$rootScope, $timeout,dashboardFactory,Upload) {
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
				scope.page_scroll = false;
				scope.page_ctr = 10;
				scope.page_active = 1;
        scope.employees_pagi = {};
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

				scope.spendingTypeTabSelected = 'medical';
				
				scope.isTotalCreditsShow = false;
				scope.isPurchasedCreditsShow = false;
				scope.isBonusCreditsShow = false;
				scope.isTotalAllocatedCreditsShow = false;
				scope.isAvailableCreditsShow = false;

				scope.selectSpendingTab	=	function(opt){
					if( scope.spendingTypeTabSelected != opt ){
						scope.spendingTypeTabSelected = opt;
						// scope.page_ctr = 10;
						scope.page_active = 1;
						scope.employees = {};
						scope.employees_pagi = {};
						scope.getEmployeeBulkCredit();
					}
				}

				scope.toggleTooltipDropdown	=	function(opt){
					if( opt == 'total_credits' ){
						scope.isTotalCreditsShow = scope.isTotalCreditsShow ? false : true;
					}
					if( opt == 'purchased_credits' ){
						scope.isPurchasedCreditsShow = scope.isPurchasedCreditsShow ? false : true;;
					}
					if( opt == 'bonus_credits' ){
						scope.isBonusCreditsShow = scope.isBonusCreditsShow ? false : true;;
					}
					if( opt == 'total_allocated_credits' ){
						scope.isTotalAllocatedCreditsShow = scope.isTotalAllocatedCreditsShow ? false : true;;
					}
					if( opt == 'available_credits' ){
						scope.isAvailableCreditsShow = scope.isAvailableCreditsShow ? false : true;;
					}
				}
        
				scope.$on( 'refresh', function( evt, data ){
					scope.onLoad();
				});
				
				scope.formatDate	=	function(date, from, to){
					return moment(date, from).format(to);
				}

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


				scope.prevPageBulkCred = function () {
          scope.page_active -= 1;
          scope.getEmployeeBulkCredit();

          if (scope.page_active == 0) {
            $('.prev-page-gp-cap').addClass('prev-disabled');
          }
        }

        scope.nextPageBulkCred = function () {
          scope.page_active += 1;
          scope.getEmployeeBulkCredit();
          $('.prev-page-gp-cap').removeClass('prev-disabled');
        }

        scope.goToBulkCred = function (num) {
          scope.page_active = num;
          scope.getEmployeeBulkCredit();
        }

        scope.changeBulkCred = function (num) {
          scope.page_ctr = num;
          scope.page_active = 1;
					// $('.opened-per-page-scroll').toggle();
					scope.page_scroll = false;
          scope.getEmployeeBulkCredit();
        }

				scope.getEmployeeBulkCredit = function() {

					scope.showLoading();
					hrSettings.getEmployeeBulkAllocation(scope.page_ctr, scope.page_active, scope.spendingTypeTabSelected)
						.then(function(response){
							console.log(response);
							scope.employees = response.data.members.data;
							scope.employees_pagi = response.data.members;
							scope.bulk_credit_values = response.data;

							scope.employees.map((value, index) => {
								if( scope.spendingTypeTabSelected != 'all' ){
									if (value.allocation.new_allocation == 0) {
										value.allocation.new_allocation = null;
									}
								}else{
									if (value.medical_allocation.new_allocation == 0) {
										value.medical_allocation.new_allocation = null;
									}
									if (value.wellness_allocation.new_allocation == 0) {
										value.wellness_allocation.new_allocation = null;
									}
								}
								
							})
							scope.hideLoading();
							scope.inititalizeDatepicker();
							console.log(scope.employees_pagi);
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

				scope.uploadFile = function (file) {
					// scope.showUploadModal = false;
					console.log(file);
					hrSettings.uploadAllocation(file)
            .then(function (response) {
              console.log(response);
              if (response.data.status == true) {
								file.uploading = 100;
								scope.showUploadModal = false;
                setTimeout(function () {
									// $mdDialog.hide();
									swal({
										title: '',
										text: `${response.data.message}`,
										html: true,
										showCancelButton: false,
										confirmButtonText: 'Close',
										customClass : 'allocationEntitlementSuccessModal'
									}, function(response) {
										if (response) {
											scope.getEmployeeBulkCredit();
										}
									});
                }, 2000);
                // $("button").removeClass("save-continue-disabled");
								// scope.getGpCapPerVisit();
              } else {
                file.uploading = 10;
                file.error = true;
                file.error_text = response.data.message;
              }
              // scope.hideLoading();
            }, function (response) {
              // console.log(response);
            }, function (evt) {
              console.log(evt);
              var progressPercentage = parseInt(100.0 * evt.loaded / evt.total) - 20;
              file.uploading = progressPercentage;
            });

				};

				scope.downloadFile = function () {

					var token = localStorage.getItem("token");
					hrSettings.downloadBulkAllocation( token );
				};
				
				scope.getSpendingAcctStatus = function () {
          // hrSettings.getSpendingAccountStatus()
          hrSettings.getPrePostStatus()
						.then(function (response) {
							console.log(response);
							scope.spending_account_status = response.data;
							// scope.spending_account_status.medical_method = 'pre_paid';
							// scope.spending_account_status.medical_enabled = false;
							// scope.spending_account_status.wellness_enabled = false;
							// scope.spending_account_status.wellness_method = 'pre_paid';
							// if( scope.account_plan_status.plan_method == 'pre_paid' ){
							if( scope.spending_account_status.account_type == 'lite_plan' ){
								if( scope.spending_account_status.medical_enabled == false){
									scope.spendingTypeTabSelected = 'wellness';
								}
							}else if( scope.spending_account_status.account_type == 'enterprise_plan'){
								scope.spendingTypeTabSelected = 'wellness';
							}else{
								scope.spendingTypeTabSelected = 'all';
							}
							console.log(scope.spending_account_status);
							console.log(scope.spendingTypeTabSelected);
							scope.getEmployeeBulkCredit();
						});
				}

				var bulkUpdateCtr = 0;
				var success_ctr = 0;
				scope.updateEachBulkAllocation	=	function(){
						var value = scope.toUpdateAllocation[bulkUpdateCtr];
						var index = bulkUpdateCtr;
						console.log(value, index);
						value.effective_date = moment(value.effective_date, 'DD/MM/YYYY').format('YYYY-MM-DD');
						
						hrSettings.updateAllocation(value)
						.then(function (response) {
							console.log(response);
							if(response.data.credit_balance_exceed == true) {
								setTimeout(function(){
									swal({
										title: '',
										text: `<span style="color:red;">Error:</span><br>You have reached your limit of <span style="color:#000; font-weight:bold;">Available Credits</span>.<br>Please contact us if you wish to allocate more credits.`,
										html: true,
										showCancelButton: false,
										confirmButtonText: 'Close',
										customClass : 'allocationEntitlementErrorModal'
									}, function(result) {
										if	(result)	{
											bulkUpdateCtr = 0;
											success_ctr = 0;
											scope.toUpdateAllocation = [];
											scope.apiErrorResponse = [];
											scope.getEmployeeBulkCredit();
										}
									});
								},600);
								console.log(scope.apiErrorResponse);
							} else {
								success_ctr++;
								console.log('success ctr', success_ctr)
								if (index == scope.toUpdateAllocation.length-1) {
									console.log('index check equal');
									bulkUpdateCtr = 0;
									scope.hideLoading();
									scope.successModal(success_ctr, value);
								}else{
									bulkUpdateCtr += 1;
									scope.updateEachBulkAllocation();
								}
							}
						});
				}

				scope.apiErrorResponse = [];
				scope.updateBulkAllocation = function () {
					console.log(scope.toUpdateAllocation);

					swal({
            title: '',
            text: `<span>Please note that the new allocation(s) set will override the previous amount.</span> <br><br> <span>Please confirm to proceed.</span>`,
            html: true,
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            reverseButtons: true,
            customClass : 'allocationEntitlementModal'
          }, function(result) {
            console.log(result);
            setTimeout(function(){
              if(result) {
								
								scope.showLoading();
                scope.updateEachBulkAllocation();
								// scope.toUpdateAllocation.map((value,index) => {
								// 	console.log(value, index);
								// 	value.effective_date = moment(value.effective_date, 'DD/MM/YYYY').format('YYYY-MM-DD');
									
								// 	hrSettings.updateAllocation(value)
								// 	.then(function (response) {
								// 		console.log(response);
								// 		if(response.data.credit_balance_exceed == true) {
								// 			scope.apiErrorResponse.push({
								// 				member_id: value.member_id,
								// 				message:response.data.message,
								// 				credit_balance_exceed: response.data.credit_balance_exceed
								// 			});
								// 			console.log('fail');
								// 			if (index == scope.toUpdateAllocation.length-1) {
								// 				console.log('index check equal');
								// 				scope.successModal(success_ctr, value);
								// 			}
								// 			console.log(scope.apiErrorResponse);
								// 		} else {
								// 			success_ctr++;
								// 			console.log('success ctr', success_ctr)
								// 			if (index == scope.toUpdateAllocation.length-1) {
								// 				console.log('index check equal');
								// 				scope.successModal(success_ctr, value);
								// 			}
								// 		}
										
								// 	});
								// });
              }
            }, 500)
					});
				}

				

				scope.successModal = function(success_ctr, value) { //success and error modal
					if(success_ctr > 0) {
						console.log('if success > 0');
						var text;
						var today = new Date();
						var effective_date = moment(value.effective_date,'YYYY/MM/DD').format('DD/MM/YYYY');

						var dateToday= scope.toUpdateAllocation.every( thing => new Date(thing.effective_date) <= today );
						var dateFuture = scope.toUpdateAllocation.every( thing => new Date(thing.effective_date) > today );
						var dateAllEqual = scope.toUpdateAllocation.every( thing => thing.effective_date === scope.toUpdateAllocation[0].effective_date);

						console.log('Every()',today,new Date(scope.toUpdateAllocation[0].effective_date ),dateToday,dateFuture,dateAllEqual);
					
						if (dateToday) {
							text = `<span>The allocation amount has been successfully updated.</span>`;
						} else if (dateFuture && dateAllEqual) {
							text = `<span>The allocation amount will be updated on ${effective_date}.</span>`;
						} else if (dateFuture && !dateAllEqual) {
							text = `<span>The allocation amount will be updated on scheduled dates.</span>`;
						} else {
							text = `<span>The allocation amount will be updated on scheduled dates.</span>`;
						}
						setTimeout(function() {
							swal({
								title: '',
								text: text,
								html: true,
								showCancelButton: false,
								confirmButtonText: 'Close',
								customClass : 'allocationEntitlementSuccessModal'
							}, function(result)	{
								
								if(result) {
									setTimeout(function(){
								
										var errorLength = scope.apiErrorResponse.length;
										var list_id = [];
										scope.apiErrorResponse.map(value => {
											list_id.push(value.member_id);
										});
										console.log(list_id);
									
										if (errorLength > 0) {
											console.log(errorLength);
											setTimeout(function(){
												swal({
													title: '',
													text: `<span style="color:red;">Error:</span><br>You have reached your limit of <span style="color:#000; font-weight:bold;">Available Credits</span>.<br>Please contact us if you wish to allocate more credits.`,
													html: true,
													showCancelButton: false,
													confirmButtonText: 'Close',
													customClass : 'allocationEntitlementErrorModal'
												}, function(result) {
													if	(result)	{
														scope.toUpdateAllocation = [];
														scope.apiErrorResponse = [];
														scope.getEmployeeBulkCredit();
													}
												});
											},600);
										} else {
											scope.toUpdateAllocation = [];
											scope.getEmployeeBulkCredit();
										}
									},600);
								}
							});
						},600);
				} else {
						console.log('else error drtso');
						setTimeout(function(){
							
							var errorLength = scope.apiErrorResponse.length;
							var list_id = [];
							scope.apiErrorResponse.map(value => {
								list_id.push(value.member_id);
							});
							console.log(list_id);
						
							if (errorLength > 0) {
								console.log(errorLength);
								setTimeout(function(){
									swal({
										title: '',
										text: `<span style="color:red;">Error:</span><br>You have reached your limit of <span style="color:#000; font-weight:bold;">Available Credits</span>.<br>Please contact us if you wish to allocate more credits.`,
										html: true,
										showCancelButton: false,
										confirmButtonText: 'Close',
										customClass : 'allocationEntitlementErrorModal'
									}, function(result) {
										if	(result)	{
											scope.toUpdateAllocation = [];
											scope.apiErrorResponse = [];
											scope.getEmployeeBulkCredit();
										}
									});
								},600);
							} else {
								scope.toUpdateAllocation = [];
								scope.getEmployeeBulkCredit();
							}
						},1500);
					}
				}

				scope.toUpdateAllocation = [];
				scope.pushToUpdateAllocation = function ( member_id, new_allocation , effective_date, spending_type ) {

					if (new_allocation != null && effective_date != null) {

						// var index = scope.toUpdateAllocation.findIndex(x => x.member_id === member_id);
						var index2 = scope.toUpdateAllocation.findIndex(x => x.member_id === member_id && x.spending_type === spending_type);
						console.log('index ni',index2);
						if (index2 < 0 || index2 >= 0 && scope.toUpdateAllocation[index2].spending_type != spending_type){
							scope.toUpdateAllocation.push({
								member_id:	member_id,
								new_allocation_credits:	new_allocation,
								effective_date:	effective_date,
								spending_type:	spending_type,
							});
							console.log('push',scope.toUpdateAllocation);
						} else {
							scope.toUpdateAllocation[index2].new_allocation_credits = new_allocation;
							scope.toUpdateAllocation[index2].effective_date = effective_date;
							
							console.log('replace',scope.toUpdateAllocation);
						}
						
					} else {
            var index2 = scope.toUpdateAllocation.findIndex(x => x.member_id === member_id && x.spending_type === spending_type);

            scope.toUpdateAllocation.splice(index2, 1);
            console.log('splice',scope.toUpdateAllocation);

          }
					
				}
				scope.account_plan_status = {}
				scope.checkAccountStatus = function () {
          hrSettings.getMethodType()
            .then(function (response) {
              console.log(response);
              scope.account_plan_status = {
                plan_method: response.data.data.plan.plan_method,
                account_type: response.data.data.plan.account_type
							}
							// scope.getEmployeeBulkCredit();
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

				scope.range = function (num) {
          var arr = [];
          for (var i = 0; i < num; i++) {
            arr.push(i);
          }
          return arr;
        };

		scope.dashCredits = function () {
			hrSettings.getCheckCredits('current_term');
		}

        scope.onLoad = function( ) {
			scope.checkSession( );
			scope.dashCredits( );
					// scope.getEmployeeList( );
					// scope.userCompanyCreditsAllocated();
					scope.getSpendingAcctStatus();
					// scope.getEmployeeBulkCredit();
					scope.inititalizeDatepicker();
					scope.checkAccountStatus();
					// scope.companyAccountType ();
        }

				// scope.checkCompanyBalance();
				
	      scope.onLoad();
				}

		}
	}
]);
