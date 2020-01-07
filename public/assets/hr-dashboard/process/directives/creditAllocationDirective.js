app.directive('creditAllocationDirective', [
	'$state',
	'hrSettings',
	'$rootScope',
	'dashboardFactory',
	function directive($state,hrSettings,$rootScope,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("creditAllocationDirective Runnning !");

				scope.employees = {};
				scope.search = "";
				scope.selected_emp = null;
				scope.options = {};
				scope.page_ctr = 6;
    		scope.page_active = 1;
    		scope.isSearch = false;

				scope.$on( 'refresh', function( evt, data ){
					scope.onLoad();
	    	});

	    	scope.companyAccountType = function () {
					scope.account_type = localStorage.getItem('company_account_type');
					console.log(scope.account_type);

					if(scope.account_type === 'enterprise_plan') {
						$('.statement-hide').hide();
					}
				}

				scope.setSpendType = function(list,opt){
					list.creditAllocSpendingType = opt;
					list.creditAllocSpendingTypeText = opt == 0 ? 'medical' : 'wellness';
				}

				scope.setTransType = function(list,opt){
					list.creditAllocTransactionType = opt;
				}

				scope.range = function (range) {
					console.log( range );
			    var arr = []; 
			    for (var i = 0; i < range; i++) {
			        arr.push(i+1);
			    }
			    return arr;
				}

				scope.perPage = function(num){
          scope.page_ctr = num;
          scope.page_active = 1;
          scope.getEmployeeList();
        };

        scope.pagesToDisplay = 5;
        scope.startIndex = function(){
          if( scope.page_active > ((scope.pagesToDisplay / 2) + 1 )) {
            if ((scope.page_active + Math.floor(scope.pagesToDisplay / 2)) > scope.employees.last_page) {
              return scope.employees.last_page - scope.pagesToDisplay + 1;
            }
            return scope.page_active - Math.floor(scope.pagesToDisplay / 2);
          }    
          return 0;
        }

        scope.goToPage = function(page){
          scope.page_active = page;
          scope.getEmployeeList();
        };

        scope.togglePage = function(){
          $(".per_page").toggle();
        };

        scope.nextPage = function(){
          scope.page_active++;
          scope.getEmployeeList();
        };

        scope.prevPage = function(){
          scope.page_active--;
          scope.getEmployeeList();
        };

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

				scope.setSelectedEmp = function( emp ){
					scope.selected_emp = emp;
				}

				scope.closePass = function( ) {
					$('#input-pass').modal('hide');
				}

				scope.updateCredit = function( emp ) {
					if(emp.add_credit > 0 || emp.deduct_credit > 0){
						emp.loading = true;
						emp.empty = false;
						if( emp.creditAllocTransactionType == 0 ){
							var data = {
								user_id : emp.user_id,
								credits : emp.add_credit,
								company_id : scope.company_properties.company_id,
								spending_type : emp.creditAllocSpendingTypeText,
								allocation_type: 'add'
							}
							console.log(data);
							hrSettings.addEmployeeCredits( data )
								.then(function(response){
									console.log(response);
									emp.loading = false;

									if( !response.data.status ){
										emp.failed = true;
										emp.success = false;
										swal('Ooops!', response.data.message, 'error');
									}else{
										emp.success = true;
										emp.failed = false;
										swal('Success!', response.data.message, 'success');
										setTimeout(function() {
											if( scope.isSearch == true ){
												scope.searchEmployee();
											}else{
												$rootScope.$broadcast('refresh');
											}
										}, 1000);
									}
								})
								.catch(function(error) {
									emp.failed = true;
									emp.success = false;
									
								});
						}else{
							var data = {
								user_id : emp.user_id,
								credits : emp.deduct_credit,
								company_id : scope.company_properties.company_id,
								spending_type : emp.creditAllocSpendingTypeText,
								allocation_type: 'deduct'
							}
							console.log(data);
							hrSettings.deductEmployeeCredits( data )
								.then(function(response){
									emp.loading = false;

									if( !response.data.status ){
										emp.failed = true;
										emp.success = false;
										swal('Ooops!', response.data.message, 'error');
									}else{
										emp.success = true;
										emp.failed = false;
										swal('Success!', response.data.message, 'success');
										setTimeout(function() {
											$rootScope.$broadcast('refresh');
										}, 1000);
									}
								})
								.catch(function(error) {
									emp.failed = true;
									emp.success = false;
								});
						}
					}else{
						emp.empty = true;
					}
				}

				scope.searchChanged = function( data ) {
					if( data == "" ){
						scope.isSearch = false;
						scope.getEmployeeList();
					}
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

				scope.removeSearchEmp = function(){
          scope.search = "";
          scope.isSearch = false;
          scope.page_active = 1;
          scope.getEmployeeList();
        }

				scope.searchEmployee = function() {
					$('.employee-overview-pagination').hide();
					scope.showLoading();
					var data = {
            search: scope.search
          };
          scope.isSearch = true;
					hrSettings.findEmployee(data)
					// hrSettings.searchCompanyEmployeeCredits(scope.search)
					.then(function(response){
						console.log(response);
						scope.employees = response.data.data;

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
							// $('.employee-overview-pagination').show();
						}
					});
				}

				scope.toggleAddCredit = function( emp ){
					emp.add_credit = 0;
					emp.remove_circle = false;

					if( emp.add_circle == false ){
						emp.add_circle = true;
					}else{
						emp.add_circle = false;
					}
				}

				scope.toggleRemoveCredit = function( emp ){
					emp.deduct_credit = 0;
					emp.add_circle = false;

					if( emp.remove_circle == false ){
						emp.remove_circle = true;
					}else{
						emp.remove_circle = false;
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

		  	scope.checkSession = function( ){
					hrSettings.getSession( )
	        	.then(function(response){
							scope.options.accessibility = response.data.accessibility;
	        	});
				}

        scope.onLoad = function( ) {
        	scope.checkSession( );
        	scope.getEmployeeList( );
        	scope.companyAccountType ( );
        }

        scope.checkCompanyBalance = function(){
					hrSettings.getCheckCredits();
				}

				scope.company_properties = {};
        scope.company_properties.total_allocation = 0.00;
        scope.company_properties.allocated = 0.00;
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

				// scope.checkCompanyBalance();
				scope.userCompanyCreditsAllocated();
	        scope.onLoad();
				}

		}
	}
]);
