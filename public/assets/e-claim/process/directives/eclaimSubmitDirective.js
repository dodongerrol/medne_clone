app.directive('eclaimSubmitDirective', [
	'$state',
	'Upload',
	'serverUrl',
	'storageFactory',
	'eclaimSettings',
	function directive($state,Upload,serverUrl,storageFactory,eclaimSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("eclaimSubmitDirective Runnning !");

				scope.step_active = 1;
				scope.eclaim = {};
				scope.eclaim.selectedDayTime = 'AM';
				scope.eclaim.selectedCurrencyType = 'SGD';
				scope.receipts = [];
				scope.uploading_files = [];
				scope.submitting = false;
				scope.upload_ctr = 0;
				scope.upload_active = 0;
				var loading_trap = false;
				var introLoader_trap = false;

				scope.selected_hour = parseInt(moment().format('hh'));
				scope.selected_minute = parseInt(moment().format('mm'));

				scope.eclaim.visit_date = "";
				scope.eclaim.spending_type = "medical";
				scope.spendingTypeOpt = 0;

				scope.claim_type_arr = [];
				scope.summ_reminder = false;

				scope.setSpendingType = function( opt ){
					scope.spendingTypeOpt = opt;
					scope.eclaim.spending_type = (opt == 0) ? 'medical' : 'wellness';
					scope.eclaim.service_selected = null;

					scope.getClaims( scope.eclaim.spending_type );
				}

				scope.selectClaimType = function( service, type ){
					scope.eclaim.service_selected = type;
					scope.eclaim.service = service;
				}

				scope.showVisitTime = function(){
					if( scope.eclaim.visit_date ){
						$( ".time-select-container" ).show();
						// scope.setVisitTime();
						$('.daytime-drop .dropdown-menu').hide();
					}
				}

				scope.hideVisitTime = function() {
					$( ".time-select-container" ).hide();
				};

				scope.visitTimeChanged = function( value ){
					console.log( value );
					if( value ){
						var temp_val = value.split(':');
						scope.selected_hour = parseInt(temp_val[0]);
						if( temp_val[1] ){
							scope.selected_minute = parseInt(temp_val[1]);
						}else{
							scope.selected_minute = 0;
						}
						scope.setVisitTime();
					}
				}

				scope.addHour = function(){
					var check = scope.checkVisitTime( "hours" );
					console.log(check);
					if( check ){
						$( ".time-select-container" ).show();
						if( scope.selected_hour < 12 ){
							scope.selected_hour++;
						}else{
							scope.selected_hour = 1;
						}	
						scope.setVisitTime();
					}
					
				}

				scope.deductHour = function(){
					if( scope.selected_hour > 1 ){
						scope.selected_hour--;
					}else{
						scope.selected_hour = 12;
					}	
					scope.setVisitTime();
				}

				scope.addMinute = function(){
					var check = scope.checkVisitTime( "minutes" );
					console.log(check);

					if( check ){
						if( scope.selected_minute < 59 ){
							scope.selected_minute++;
						}else{
							scope.selected_minute = 0;
						}	

						scope.setVisitTime();
					}
				}

				scope.deductMinute = function() {
					if( scope.selected_minute > 0 ){
						scope.selected_minute--;
					}else{
						scope.selected_minute = 59;
					}	

					scope.setVisitTime();
				}

				scope.setVisitTime = function( ) {
					var hour = "" + (( scope.selected_hour < 10 ) ? 0 : "") + scope.selected_hour + ":" + (( scope.selected_minute < 10 ) ? 0 : "") + scope.selected_minute;
					scope.eclaim.visit_time = hour;
					// console.log(hour);
				}

				scope.checkVisitTime = function( opt_type ) {
					var selected_time = moment( scope.eclaim.visit_date + " " + scope.selected_hour + ":" + scope.selected_minute + " " + scope.eclaim.selectedDayTime, 'MMM DD, YYYY hh:mm A' ).format( 'MM/DD/YYYY hh:mm A' );
					var curr_time;

					if( opt_type == "" ){
						curr_time = moment().format( 'MM/DD/YYYY hh:mm A' );
					}else{
						curr_time = moment().subtract(1,opt_type).format( 'MM/DD/YYYY hh:mm A' );
					}

					if( moment(selected_time).isSameOrBefore( moment(curr_time) ) ){
						return true;
					}else{
						scope.showToast( 'Selected visit time exceeds' );
					}
					
					return false;
				}

				scope.nextStep = function( ) {
					scope.step_active++;

					scope.checkEclaimVisit_data = {};
					scope.eclaim = storageFactory.getEclaim();
					console.log(scope.eclaim);
					if(scope.step_active == 3) {
						var data = {
							visit_date: moment(scope.eclaim.visit_date).format('YYYY-MM-DD'),
							spending_type: scope.eclaim.spending_type,
							currency_type: localStorage.getItem('currency_type'),
						}
						scope.showLoading();
						eclaimSettings.getCheckEclaimVisit(data)
						.then(function(response){
							scope.hideLoading();
							scope.checkEclaimVisit_data = response.data;
							// scope.checkEclaimVisit_data.balance = Number( parseFloat( scope.checkEclaimVisit_data.balance.replace(',','') ).toFixed(2) );

							if(jQuery.type( scope.checkEclaimVisit_data.balance ) == 'string'){
								scope.checkEclaimVisit_data.balance = Number( parseFloat( scope.checkEclaimVisit_data.balance.replace(',','') ).toFixed(2) );
							  }else{
								scope.checkEclaimVisit_data.balance = Number( parseFloat( scope.checkEclaimVisit_data.balance ).toFixed(2) );
							  }
							  if(jQuery.type( scope.eclaim.service.cap_amount ) == 'string'){
								scope.eclaim.service.cap_amount = Number( parseFloat( scope.eclaim.service.cap_amount.replace(',','') ).toFixed(2) );
							  }else{
								scope.eclaim.service.cap_amount = Number( parseFloat( scope.eclaim.service.cap_amount ).toFixed(2) );
							  }
							scope.eclaim.claim_amount = Number( parseFloat( scope.eclaim.claim_amount ).toFixed(2) );
							console.log('scope.checkEclaimVisit_data.balance', scope.checkEclaimVisit_data.balance);
							console.log('scope.eclaim.claim_amount', scope.eclaim.claim_amount);
							// claim_amount = receipt & new_claim_amount = claim_amount
							if (scope.checkEclaimVisit_data.balance < scope.eclaim.claim_amount ) {
								if(scope.eclaim.service.cap_amount > 0 && scope.eclaim.service.cap_amount < scope.eclaim.claim_amount){
									scope.eclaim.new_claim_amount = scope.eclaim.service.cap_amount;
								}else{
									scope.eclaim.new_claim_amount = scope.checkEclaimVisit_data.balance;
								}
							} else {
								if(scope.eclaim.service.cap_amount > 0 && scope.eclaim.service.cap_amount < scope.eclaim.claim_amount){
									scope.eclaim.new_claim_amount = scope.eclaim.service.cap_amount;
								}else{
									scope.eclaim.new_claim_amount = scope.eclaim.claim_amount;
								}
							}

							if( scope.checkEclaimVisit_data.last_term == true) {
								scope.summ_reminder = true;
							}
							console.log('new api 8-9', scope.checkEclaimVisit_data);
							console.log(scope.eclaim);
						});
					}
				}

				scope.backStep = function( ) {
					scope.step_active--;
					if(scope.step_active == 1){
						scope.initializeDatepickers();
					}
				}

				scope.close_new_popup = function() {
					scope.summ_reminder = false;

				}

				scope.selectDayTime = function( daytime ) {
					var temp = scope.eclaim.selectedDayTime;
					scope.eclaim.selectedDayTime = daytime;

					var check = scope.checkVisitTime( "" );

					if( !check ){
						scope.eclaim.selectedDayTime = temp;
					}
				}

				scope.showCurrencyDropdown = function() {
					
					console.log('sadasdsa');

					// if ( scope.currency_myr === 'sgd' ) {
						$('.currency-type-selector').toggle();
					// }
					
				}

				scope.selectCurrencyType = function ( currencyTime ) {
					console.log('currency type');
					var temp = currencyTime;

					scope.eclaim.selectedCurrencyType = currencyTime;
					$('.currency-type-selector').hide();
					console.log(temp);

				}

				scope.selectMember = function( member ) {
					scope.eclaim.member_selected = member;
				}

				scope.setVisitDate = function( date ) {
					scope.eclaim.visit_date = moment(date).format('DD MMMM, YYYY');
				}

				scope.showToast = function( text ) {
					$.toast({ 
						  text : text, 
						  showHideTransition : 'slide',  
						  bgColor : 'rgba(0, 134, 211, 0.86)',           
						  textColor : '#FFF',            
						  allowToastClose : true,      
						  hideAfter : 5000,              
						  // hideAfter : false,              
						  stack : 1,                     
						  textAlign : 'center',            
						  position : 'bottom-center'       
						})
				}

				scope.saveEclaimInfo = function(  ) {
					scope.hideVisitTime(); 

					var info = scope.eclaim;

					if( !info.service_selected ){
						scope.showToast( "Please select a Claim type" );
						return false;
					}

					if( !info.merchant ){
						scope.showToast( "Please input a provider" );
						return false;
					}

					if( !info.visit_date ){
						scope.showToast( "Please select a visit date" );
						return false;
					}else{
						if( moment(info.visit_date).isBefore( moment( scope.user_status.valid_start_claim ).subtract( 1, 'days' ) ) 
							|| moment(info.visit_date).isAfter( moment( ).add( 1, 'days' ) ) ){
							scope.showToast( "Visit Date should be between " + moment( scope.user_status.valid_start_claim ).format("MM/DD/YYYY") + " and " + moment( ).format("MM/DD/YYYY") );
							// scope.showToast( "Visit Date should be between " + moment( scope.user_status.valid_start_claim ).format("MM/DD/YYYY") + " and " + moment( scope.user_status.valid_end_claim ).format("MM/DD/YYYY") );
							return false;
						}
					}

					if( !info.visit_time ){
						scope.showToast( "Please select a visit time" );
						return false;
					}

					if( !info.claim_amount || info.claim_amount == 0){
						scope.showToast( "Claim Amount should be more than 0" );
						return false;
					}

					if( !info.member_selected ){
						scope.showToast( "Please select a member" );
						return false;
					}

					console.log(info);

					storageFactory.setEclaim(info);
					scope.nextStep();
				}

				scope.saveUploads = function( ) {
					var info = storageFactory.getEclaim();
					info.receipts = scope.receipts;

					storageFactory.setEclaim(info);
					scope.nextStep();
				}

				scope.uploadReceipts = function( data ) {
					console.log( data );
					if( data ){
						scope.uploading_files.push(data);
						data.uploading = 0;
						scope.upload_ctr++;

						eclaimSettings.uploadEclaimReceipt( { file : data } )
							.then(function(response){
								console.log(response);

								scope.upload_ctr--;

								if( response.data.receipt != null && response.data.status == true){
									scope.receipts.push(response.data.receipt);
									data.uploading = 100;
									scope.upload_active++;
								}else{
									data.uploading = 10;
									data.error = true;
									data.error_text = response.data.message;
								}

							}, function(response){
								console.log(response);

							},function (evt) {
		            var progressPercentage = parseInt(100.0 * evt.loaded / evt.total) - 20;
		            data.uploading = progressPercentage;
			        });
		      }

		      console.log(scope.receipts);
				}

				scope.removeReceipt = function( rec ) {
					scope.uploading_files.splice( $.inArray( scope.uploading_files , rec ), 1 );
					scope.receipts.splice( $.inArray( scope.uploading_files , rec ), 1 );
				}

				scope.submitEclaim = function(  ) {
					scope.toggleLoading( );

					var data = {
						user_id: scope.eclaim.member_selected.user_id,
						service: scope.eclaim.service_selected,
						merchant: scope.eclaim.merchant,
						amount: scope.eclaim.claim_amount,
						date: moment(scope.eclaim.visit_date).format('YYYY-MM-DD'),
						time: scope.eclaim.visit_time + '' + scope.eclaim.selectedDayTime,
						receipts: scope.receipts,
						currency_type: scope.eclaim.selectedCurrencyType,
						claim_amount: scope.eclaim.new_claim_amount
					}

					console.log(data);

					if( scope.spendingTypeOpt == 0 ){
						eclaimSettings.saveEclaimMedical(data)
							.then(function(response){
								console.log(response);
								scope.toggleLoading( );
								
								if( response.data.status == true ){
									scope.nextStep();
								}else{
									scope.submitFailed = true;
									scope.showToast( response.data.message );
								}
							});
					}else{
						eclaimSettings.saveEclaimWellness(data)
							.then(function(response){
								console.log(response);
								scope.toggleLoading( );
								
								if( response.data.status == true ){
									scope.nextStep();
								}else{
									scope.submitFailed = true;
									scope.showToast( response.data.message );
								}
							});
					}
					

				}

				//---- LOADERS -----

				scope.hideIntroLoader = function( ) {
					setTimeout(function() {
						$( ".main-loader" ).fadeOut();
						introLoader_trap = false;
					}, 1000);
				}

				scope.toggleLoading = function( ) {
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

				scope.showLoading = function( ) {
					$( ".circle-loader" ).fadeIn();	
					loading_trap = true;
				}

				scope.hideLoading = function( ) {
					setTimeout(function() {
						$( ".circle-loader" ).fadeOut();
						loading_trap = false;
					},1000)
				}

				// --------------

				scope.getClaims = function( opt ){
					eclaimSettings.getClaimTypes( opt )
						.then(function(response){
							console.log(response);
							scope.claim_type_arr = response.data;
						});
				}
				
				scope.getEnterpriseClaims = function( opt ){
					eclaimSettings.getClaimTypes( opt )
						.then(function(response){
							console.log(response);
							scope.claim_type_arr = response.data;
						});
				}

				scope.getDetails = async function( ) {
					await eclaimSettings.empDetails( )
						.then(function( response ) {
							console.log(response);
							scope.user_details = response.data.data;
							scope.hideIntroLoader();
							if ( scope.user_details.wellness == true && scope.user_details.currency_type == 'myr' && scope.user_details.plan_type == 'enterprise_plan' ) {
								scope.spendingTypeOpt = 1;
								scope.setSpendingType(1);
							}else{
								scope.getClaims( scope.eclaim.spending_type );
							}

							scope.eClaimDisabled();
						});
				}

				scope.fetchMembers = async function( ) {
					await eclaimSettings.getEclaimMember()
						.then(function(response){
							// console.log(response);
							scope.elcaim_members = response.data;
						});
				}

				scope.getCurrentActivity = function( ) {
					eclaimSettings.employeeCurrentActivity( )
						.then(function(response){
							console.log(response);
							if(response.status == 200) {
								scope.current_spending = response.data;
								scope.total_balance = (response.data.total_allocation.indexOf(",") >= 0) ? response.data.total_allocation.replace(",", "") : response.data.total_allocation;
								// scope.total_balance = parseInt(scope.total_balance);
								// console.log(scope.total_balance);
								scope.currency_myr = response.data.currency_type;
								console.log(scope.currency_myr);

								if (scope.currency_myr === 'myr') {
									scope.eclaim.selectedCurrencyType = scope.currency_myr;
								}
							}

							scope.hideIntroLoader();
						});
				};

				scope.getEclaimPackages = async function( ) {
					await eclaimSettings.getPackages( )
					.then(function(response){
						console.log( response );
						scope.user_status = response.data;
						scope.currency_myr = response.data.currency_type;
						if (scope.currency_myr === 'myr') {
							scope.eclaim.selectedCurrencyType = scope.currency_myr;
						}
						scope.initializeDatepickers();
					})
				}

				scope.initializeDatepickers = function(){
					setTimeout(function() {
	        	var visit_date_dp =  $('#visitDateInput').datetimepicker({
				    	format : 'DD MMMM, YYYY',
				    	minDate : new Date( moment( scope.user_status.valid_start_claim ) ),
				    	maxDate : new Date( ),
				    	// maxDate : new Date( moment( scope.user_status.valid_end_claim ) ),
				    	useCurrent : false,
				    });

	        	$('#visitDateInput').on('dp.show', function(e){ 
	        		if( scope.eclaim.visit_date == "" ){
				    		scope.setVisitDate( moment() ); 
				    		// scope.setVisitDate( moment().subtract( 1, 'days' ) ); 
	        		}
				    });

				    $('#visitDateInput').on('dp.change', function(e){ 
				    	scope.setVisitDate( e.date ); 
				    });

				    // var visit_time_dp =  $('#visitTimeInput').datetimepicker({
				    // 	format : 'hh:mm',
				    // });

				    // $('#visitTimeInput').on('dp.change', function(e){ 
				    // 	scope.setVisitTime( e.date ); 
				    // });

				    $('.daytime-drop').click(function(){
				    	$('.daytime-drop .dropdown-menu').toggle();
				    	scope.hideVisitTime();
				    });

				  //   $("#claimAmountInput").keypress(function (e) {
						//     if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
						// });
	        }, 100);
				}
				scope.eClaimDisabledState = false;
				scope.eClaimDisabled = function () {
					if ( scope.user_details.plan_type == 'enterprise_plan' && scope.user_details.wellness == false && scope.user_details.currency_type == 'myr') {
						scope.eClaimDisabledState = true;
					}
				}

				scope.eClaimDisabledClosed = function () {
					scope.eClaimDisabledState = false;
				}

				scope.onLoad = async function( ) {

					await scope.getDetails();
					await scope.getEclaimPackages();
					await scope.fetchMembers();
					
					
					scope.local_eclaim = storageFactory.getEclaim();
					// console.log( scope.local_eclaim );
					console.log(scope.eclaim);

        }

        scope.onLoad();

        
        


    //     $('body').on('keypress', '.provider-input', function (event) {
    //     	console.log('sdfds');
				//     var regex = new RegExp("^[a-zA-Z0-9]+$");
				//     var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				//     if (!regex.test(key)) {
				//     	scope.showToast( 'Special characters are not allowed for provider name.' );
				//        event.preventDefault();
				//        return false;
				//     }
				// });

			}
		}
	}
]);
