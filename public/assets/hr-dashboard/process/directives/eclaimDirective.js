	app.directive('eclaimPage', [
	"hrActivity",
	"hrSettings",
	"$timeout",
	"$compile",
	function directive(hrActivity, hrSettings, $timeout, $compile) {
		return {
			restrict: "A",
			scope: true,
			link: function link(scope, element, attributeSet) {
				console.log('running eclaimPage');
				scope.temp_no_search_activity = {};
				scope.options = {};
				scope.activity = {};
				scope.search = {};
				scope.search.close = false;
				scope.employee_lists = {};
				scope.selected_list = {};
				scope.filter_text = 'All';
				scope.filter_num = 1;

				scope.rangePicker_start = moment().startOf('month').format( 'DD/MM/YYYY' );
				scope.rangePicker_end = moment().format( 'DD/MM/YYYY' );
				$("#rangePicker_start").text( scope.rangePicker_start );
				$("#rangePicker_end").text( scope.rangePicker_end );

				scope.showCustomPicker = false;
				scope.year_active = 1;

				scope.current_page = 1;

				scope.eclaimSpendingTypeSelected = 'medical';
				scope.eclaimSpendingType = 0;

				scope.csv_e_claim_transactions = [];
				scope.csv_e_claim_transactions_pending = [];
				scope.csv_e_claim_transactions_approved = [];
				scope.csv_e_claim_transactions_rejected = [];
				scope.csv_dl = [];

				scope.fetching_data = {
					from : 1,
					to: 1
				}

				scope.selected_transaction = {};
				scope.selected_duplicate_transactions = [];

				scope.receipts_all = [];
				scope.receipts_pending = [];
				scope.receipts_approved = [];
				scope.receipts_rejected = [];
				scope.receipts_arr = [];

				var monthToday = moment().format('MM');
				var monthToday2 = moment().format('MM');
				var yearToday = moment().format('YYYY');
				var introLoader_trap = false;
				var loading_trap = false;
				var temp_list = null;
				var slide_trap = null;

				var date_slider = null;

				scope.checkTransStatus = function( data ){
					console.log( data );
					scope.selected_transaction = data;
					scope.toggleLoading();
					hrSettings.checkTransactionDuplicates( data.trans_id )
					.then(function(response){
						console.log(response);
						scope.toggleLoading();
						if( response.data.status ){
							scope.selected_duplicate_transactions = response.data.data;
							$('#check-duplicate-modal').modal('show');
						}else{
							swal( 'Info!', response.data.message, 'info' );
						}
					});
				}

				scope.setSpendType = function( opt ){
					scope.eclaimSpendingType = opt;
					scope.eclaimSpendingTypeSelected = opt == 0 ? 'medical' : 'wellness';
					scope.current_page = 1;
					// var range_data = date_slider.getValue();
			  //   var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
			  	var activity_search = {
				  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
				  };
					if(scope.search.user_id) {
		    		scope.searchEmployeeActivity(scope.search.user_id);
		    	}else{
						scope.searchActivity( activity_search );
		    	}
				}

				scope.downloadReceipt = function( res, all_data ) {
					scope.toggleLoading();

						var zip = new JSZip();

						angular.forEach( res, function(value,key){
							var filename = $.trim( value.file.split('/').pop() );
							var img = zip.folder("images");
							var pdf = zip.folder("pdf");
							var xls = zip.folder("xls");
							var promise = $.ajax({
				        url: value.file,
				        method: 'GET',
				        xhrFields: {
				          responseType: 'blob'
				        }
					    });
							if( value.file_type == 'pdf' ){
								pdf.file(filename, promise);
							}
							if( value.file_type == 'image' ){
								img.file(filename,promise);
							}
							if( value.file_type == 'xls' ){
								xls.file(filename,promise);
							}
							
							if( key == (res.length-1) ){
								zip.generateAsync({type:"blob"}).then(function(content) {
							    saveAs(content, all_data.transaction_id + " - " + all_data.member + ".zip");
								});
								scope.toggleLoading();
							}
						})

				}

				scope.download_receipts_ctr = 0;
				var zip = new JSZip();

				scope.downloadAllReceipts = function(  ){
					if( scope.download_receipts_ctr == 0 ){
						scope.toggleLoading();
						$('.download-receipt-message').show();
						$('.download-receipt-message .total').text( scope.receipts_arr.length );
						zip = new JSZip();
					}
					$('.download-receipt-message .ctr').text( scope.download_receipts_ctr + 1 );
					var zipfilename = moment( scope.rangePicker_start, 'DD/MM/YYYY' ).format('DD MMM') + ' to ' + moment( scope.rangePicker_end, 'DD/MM/YYYY' ).format('DD MMM YYYY') + ' ' + scope.company_details;
					var transaction = scope.receipts_arr[scope.download_receipts_ctr];
					var main_folder = zip.folder( transaction.filename );
					angular.forEach( transaction.files , function( value, key ){
						var filename = $.trim( value.file.split('/').pop() );
						// var img = main_folder.folder("images");
						// var pdf = main_folder.folder("pdf");
						// var xls = main_folder.folder("xls");
						var promise = $.ajax({
			        url: value.file,
			        method: 'GET',
			        xhrFields: {
			          responseType: 'blob'
			        }
			    	});
				    promise.then(function(a,b,c){
				    	console.log( scope.download_receipts_ctr, b );
				   //  	if( value.file_type == 'pdf' ){
							// 	pdf.file(filename, promise);
							// }
							// if( value.file_type == 'image' ){
							// 	img.file(filename,promise);
							// }
							// if( value.file_type == 'xls' ){
							// 	xls.file(filename,promise);
							// }
							main_folder.file(filename, promise);
							if( key == transaction.files.length-1 ){
								if( scope.download_receipts_ctr == (scope.receipts_arr.length-1) ){
									$timeout(function() {
										zip.generateAsync({type:"blob"})
											.then(function(content) {
										    saveAs(content, zipfilename + ".zip");
											});
										scope.download_receipts_ctr = 0;
										scope.toggleLoading();
									}, 1000);
									
								}else{
									$timeout(function() {
										scope.download_receipts_ctr+=1;
										scope.downloadAllReceipts();
									}, 300);
								}
							}
				    }).catch(function( a, b, c ){
				    	console.log( scope.download_receipts_ctr, b );
				    	$timeout(function() {
								scope.downloadAllReceipts();
							}, 300);
							if( key == transaction.files.length-1 ){
								if( scope.download_receipts_ctr == (scope.receipts_arr.length-1) ){
									$timeout(function() {
										zip.generateAsync({type:"blob"})
											.then(function(content) {
										    saveAs(content, zipfilename + ".zip");
											});
										scope.download_receipts_ctr = 0;
										scope.toggleLoading();
									}, 1000);
									
								}else{
									$timeout(function() {
										scope.download_receipts_ctr+=1;
										scope.downloadAllReceipts();
									}, 300);
								}
							}
				    });

					});
				}

				scope.openDetails = function( list ) {
					if( list.showTransacDetails == true ){
						list.showTransacDetails = false;
					}else{
						list.showTransacDetails = true;
					}
				}

				scope.filterTransactions = function( num ){
					scope.showLoading();
					scope.filter_num = num;
					if( num == 1 ){
						scope.filter_text = 'All';
						scope.receipts_arr = scope.receipts_all;
						scope.csv_dl = scope.csv_e_claim_transactions;
					}else if( num == 2 ){
						scope.filter_text = 'Pending';
						scope.receipts_arr = scope.receipts_pending;
						scope.csv_dl = scope.csv_e_claim_transactions_pending;
					}else if( num == 3 ){
						scope.filter_text = 'Approved';
						scope.receipts_arr = scope.receipts_approved;
						scope.csv_dl = scope.csv_e_claim_transactions_approved;
					}else{
						scope.filter_text = 'Rejected';
						scope.receipts_arr = scope.receipts_rejected;
						scope.csv_dl = scope.csv_e_claim_transactions_rejected;
					}
					// console.log( scope.receipts_arr );
					scope.hideLoading();
				}

				scope.hideReasonInput = function( list ){
					list.showReasonInput = false;
					list.showRemarksInput = false;
				}

				scope.updateStatus = function( list, num ){
					if( num == 1 ){
						list.showReasonInput = false;
						list.showRemarksInput = true;
					}
					if( num == 2 ){
						list.showReasonInput = true;
						list.showRemarksInput = false;
					}
					if( num == 3 ){
						// list.showReasonInput = true;
						// list.showRemarksInput = false;
						scope.toggleLoading();
						var data = {
							e_claim_id : list.trans_id
						}
						hrActivity.revertEclaim( data )
							.then(function(response){
								console.log(response);
								if( response.data.status == true ){
									list.status = 0;
									list.status_text = 'Pending';
									list.res = true;
									list.message = response.data.message;
								}else{
									swal( 'Oops!', response.data.message, 'error' );
								}
								scope.toggleLoading();
							});
					}
				}

				scope.updateStatusToApprove = function( list, num ){
					scope.toggleLoading();
					var data = {
						e_claim_id: list.transaction_id,
						status: num,
						rejected_reason : list.reason
					}

					hrActivity.updateEclaimStatus( data )
					.then(function(response){
						console.log(response);
						scope.toggleLoading();
						if( response.data.status == true ){
							console.log(list);
							list.status = num;
							list.showRemarksInput = false;
							if( list.status == 1 ){
								list.status_text = 'Approved';
							}
							
							if( response.data.status == true ){
								list.res = true;
								list.message = response.data.message;
							}else{
								list.res = false;
								list.message = response.data.message;
							}
						} else {
							alert(response.data.message);
						}
					});
				}

				scope.updateStatusToReject = function( list, num ){
					if( list.reason != "" ){
						scope.toggleLoading();
						var data = {
							e_claim_id: list.transaction_id,
							status: num,
							rejected_reason : list.reason
						}

						hrActivity.updateEclaimStatus( data )
						.then(function(response){
							scope.toggleLoading();
							if( response.data.status == true ){
								console.log(list);
								list.status = num;
								list.showReasonInput = false;
								if( list.status == 2 ){
									list.status_text = 'Rejected';
									list.rejected_reason = list.reason;
									list.rejected_date = moment().format("DD MMMM YYYY hh:mm A");
								}
								
								if( response.data.status == true ){
									list.res = true;
									list.message = response.data.message;
								}else{
									list.res = false;
									list.message = response.data.message;
								}
							}
						});
					}
				}

				scope.showDetails = function( e, list ){
					// console.log(e);
					scope.selected_list = list;
					var height = $( e.currentTarget ).offset().top - $( '.transactions-container' ).offset().top - 20;
					$( '.transaction-tr' ).removeClass('active');

					if( temp_list == null || temp_list != list){
						temp_list = list;
						$( e.currentTarget ).addClass('active');
						$( ".main-transac-container" ).animate({'left':'-18%'}, 'slow');
						$( ".hidden-details-container" ).css({'top': height+'px'});
						$( ".hidden-details-container" ).animate({'right':'3%'}, 'slow');
					}else{
						temp_list = null;
						$( ".main-transac-container" ).animate({'left':'0'}, 'slow');
						$( ".hidden-details-container" ).animate({'right':'-100%'}, 'slow');
					}

				}

				scope.fetchNextPage = function( data ) {
					scope.current_page = scope.current_page + 1;
					data.page = scope.current_page;
					hrActivity.getEclaimActivity(data)
					.then(function(response){
						console.log(response);
						
						scope.fetching_data = {
							from : response.data.from,
							to: response.data.total
						}

						angular.forEach( response.data.data.e_claim_transactions, function(value,key){
							scope.activity.e_claim_transactions.push( value );
						});

						scope.activity.all_transaction_total_formatted += response.data.data.all_transaction_total_formatted;
						scope.activity.pending_transaction_total_formatted += response.data.data.pending_transaction_total_formatted;
						scope.activity.rejected_transaction_total_formatted += response.data.data.rejected_transaction_total_formatted;
						scope.activity.total_e_claim_approved_formatted += response.data.data.total_e_claim_approved_formatted;
						scope.activity.total_e_claim_pending_formatted += response.data.data.total_e_claim_pending_formatted;
						scope.activity.total_e_claim_rejected_formatted += response.data.data.total_e_claim_rejected_formatted;
						scope.activity.total_e_claim_submitted_formatted += response.data.data.total_e_claim_submitted_formatted;
						

						if( scope.current_page != response.data.last_page ){
							scope.fetchNextPage(data);
						}else{
							scope.fetching_data.from = response.data.total;

							scope.activity.e_claim_transactions.sort(function (left, right) {
								return moment.utc(right.claim_date, 'DD MMMM YYYY, hh:mma').diff(moment.utc(left.claim_date, 'DD MMMM YYYY, hh:mma'))
							});

							scope.csv_e_claim_transactions = scope.activity.e_claim_transactions;

							if( scope.activity.e_claim_transactions.length > 0 ){
								$('.btn-receipts').attr( 'disabled', false );
								angular.forEach( scope.activity.e_claim_transactions, function( value, key ){
									var temp_arr = [];
									angular.forEach( value.files, function( value2, key2 ){
										temp_arr.push(value2);
										if( key2 == ( value.files.length-1 ) ){
											scope.receipts_arr.push( { filename: value.transaction_id + ' - ' + value.member, files : temp_arr } );
											scope.receipts_all.push( { filename: value.transaction_id + ' - ' + value.member, files : temp_arr } );
											if( value.status == 0 ){
												scope.receipts_pending.push( { filename: value.transaction_id + ' - ' + value.member, files : temp_arr } );
												scope.csv_e_claim_transactions_pending.push( value );
											}
											if( value.status == 1 ){
												scope.receipts_approved.push( { filename: value.transaction_id + ' - ' + value.member, files : temp_arr } );
												scope.csv_e_claim_transactions_approved.push( value );
											}
											if( value.status == 2 ){
												scope.receipts_rejected.push( { filename: value.transaction_id + ' - ' + value.member, files : temp_arr } );
												scope.csv_e_claim_transactions_rejected.push( value );
											}
										}
									})
								});
							}else{
								$('.btn-receipts').attr( 'disabled', true );
							}

							scope.csv_dl = scope.csv_e_claim_transactions;

							$("#fetching_text").hide();
							$("#done_fetching_text").show();
							setTimeout(function() {
								$("#fetching_users").fadeOut('slow');
							}, 2000);
							$(".searchEclaimLoader").hide();
							$(".searchEclaimLoader2").hide();
							scope.togglePointerEvents();
						}
					});
				}

				scope.searchActivity = function( data ) {
					scope.toggleLoading();
					scope.togglePointerEvents();
					$(".searchEclaimLoader").show();
					$(".searchEclaimLoader2").show();
					$("#fetching_text").show();
					$("#done_fetching_text").hide();
					$("#fetching_users").show();
					data.page = 1;
					data.spending_type = scope.eclaimSpendingTypeSelected;
					scope.fetching_data = {
						from : 0,
						to: 0
					}
					scope.receipts_all = [];
					scope.receipts_pending = [];
					scope.receipts_approved = [];
					scope.receipts_rejected = [];
					scope.receipts_arr = [];
					scope.csv_e_claim_transactions = [];
					scope.csv_e_claim_transactions_pending = [];
					scope.csv_e_claim_transactions_approved = [];
					scope.csv_e_claim_transactions_rejected = [];
					hrActivity.getEclaimActivity(data)
					.then(function(response){
						console.log(response);
						scope.toggleLoading();
						scope.activity = {};
						scope.activity = response.data.data;
						
						scope.fetching_data = {
							from : response.data.from,
							to: response.data.total
						}

						if( response.data.last_page > 0 && scope.current_page != response.data.last_page ){
							scope.fetchNextPage(data);
						}else{
							scope.fetching_data.from = response.data.total;

							scope.activity.e_claim_transactions.sort(function (left, right) {
								return moment.utc(right.claim_date, 'DD MMMM YYYY, hh:mma').diff(moment.utc(left.claim_date, 'DD MMMM YYYY, hh:mma'))
							});

							scope.csv_e_claim_transactions = scope.activity.e_claim_transactions;

							if( scope.activity.e_claim_transactions.length > 0 ){
								$('.btn-receipts').attr( 'disabled', false );
								angular.forEach( scope.activity.e_claim_transactions, function( value, key ){
									var temp_arr = [];
									angular.forEach( value.files, function( value2, key2 ){
										temp_arr.push(value2);
										if( key2 == ( value.files.length-1 ) ){
											scope.receipts_arr.push( { filename: value.transaction_id + ' - ' + value.member, files : temp_arr } );
											scope.receipts_all.push( { filename: value.transaction_id + ' - ' + value.member, files : temp_arr } );
											if( value.status == 0 ){
												scope.receipts_pending.push( { filename: value.transaction_id + ' - ' + value.member, files : temp_arr } );
												scope.csv_e_claim_transactions_pending.push( value );
											}
											if( value.status == 1 ){
												scope.receipts_approved.push( { filename: value.transaction_id + ' - ' + value.member, files : temp_arr } );
												scope.csv_e_claim_transactions_approved.push( value );
											}
											if( value.status == 2 ){
												scope.receipts_rejected.push( { filename: value.transaction_id + ' - ' + value.member, files : temp_arr } );
												scope.csv_e_claim_transactions_rejected.push( value );
											}
										}
									})
								});
							}else{
								$('.btn-receipts').attr( 'disabled', true );
							}
							
							scope.csv_dl = scope.csv_e_claim_transactions;

							$("#fetching_text").hide();
							$("#done_fetching_text").show();
							setTimeout(function() {
								$("#fetching_users").fadeOut('slow');
							}, 2000);
							$(".searchEclaimLoader").hide();
							$(".searchEclaimLoader2").hide();
							scope.togglePointerEvents();
						}
					});
				}

				scope.getEmployeeLists = function( ) {
					hrActivity.getEmployeeLists( )
					.then(function(response){
						scope.hideIntroLoader();
						// scope.items = response.data.data;
						// console.log(scope.employee_lists);
						$('.typeahead').typeahead({
					   showHintOnFocus: true,
					   source: response.data.data,
					   displayText: function(item) {
					     return item.Name
					   },
					   items: 15,
					   afterSelect: function(item) {
					   	setTimeout(function() {
					   		scope.selected_search_user = item;
					   		console.log( item );
					   		scope.searchEmployeeActivity(item.user_id);
					   	}, 100);
					   }
					  });
					});
				};

				scope.searchEmployeeActivity = function(user_id) {
					scope.toggleLoading();
					scope.temp_no_search_activity = scope.activity;
					scope.receipts_arr = [];
					// var range_data = date_slider.getValue();
					var activity_search = {
						start: moment( scope.rangePicker_start,'DD/MM/YYYY' ).format('YYYY-MM-DD'),
						end: moment( scope.rangePicker_end ,'DD/MM/YYYY').format('YYYY-MM-DD'),
					}
					scope.search.user_id = user_id;
					activity_search.user_id = user_id;
					activity_search.spending_type = scope.eclaimSpendingTypeSelected;
					scope.search.close = true;
					hrActivity.searchEmployeeEclaimActivity(activity_search)
					.then(function(response){
						scope.toggleLoading();
						if(response.status == 200) {
							console.log( response);
							scope.activity_title = response.data.employee + ' Benefits Cost';
							scope.activity = {};
							scope.activity_dates = [];
							scope.eclaim_dates = [];
							scope.activity = response.data;
							if( scope.activity.e_claim_transactions.length > 0 ){
								$('.btn-receipts').attr( 'disabled', false );
								angular.forEach( scope.activity.e_claim_transactions, function( value, key ){
									var temp_arr = [];
									angular.forEach( value.files, function( value2, key2 ){
										temp_arr.push(value2);
										if( key2 == ( value.files.length-1 ) ){
											scope.receipts_arr.push( { filename: value.transaction_id + ' - ' + value.member, files : temp_arr } );
										}
									})
								});
							}else{
								$('.btn-receipts').attr( 'disabled', true );
							}
						}
					});
				};

				scope.closeSeach = function( ) {
					scope.search = {};
					scope.search.close = false;
					scope.activity_title = "Benefits Cost";
					$('.typeahead').val("");
					// var range_data = date_slider.getValue();
					// var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
					// scope.searchActivity( activity_search );

					// scope.toggleLoading();
					// scope.activity = scope.temp_no_search_activity;
					// scope.temp_no_search_activity = {};
					// console.log(scope.activity);

					// var range_data = date_slider.getValue();
			    // var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
			    var activity_search = {
				  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
				  };
					scope.searchActivity( activity_search );

					// setTimeout(function() {
					// 	scope.toggleLoading();
					// }, 1000);
				};

				scope.displayText = function(item) {
				  return item.Name
				};

				scope.afterSelect = function(item) {
				  console.log(item);
				};

				scope.getFirstEndDate = function( firstMonth, lastMonth ){
					var startOfMonth = moment( firstMonth + " " + yearToday,'MM YYYY' ).startOf('month').format('YYYY-MM-DD');
					var endOfMonth   = moment( lastMonth + " " + yearToday,'MM YYYY' ).endOf('month').format('YYYY-MM-DD');
					return {
						start: startOfMonth,
						end: endOfMonth,
					}
				};

				scope.hideIntroLoader = function( ){
					setTimeout(function() {
						$( ".main-loader" ).fadeOut();
						introLoader_trap = false;
					}, 1000);
				}

				scope.toggleLoading = function( ){
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

				var pointer_trap = false;
				scope.togglePointerEvents = function( ){
					if ( pointer_trap == false ) {
						$( ".disable-cursor-off" ).addClass( "disable-cursor-on" );
						pointer_trap = true;
						scope.isLoading = true;
					}else{
						$( ".disable-cursor-off" ).removeClass( "disable-cursor-on" );
						pointer_trap = false;
						scope.isLoading = false;
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
					},2000)
				}

				scope.showCustomDate = function( num ){
					scope.year_active = num;

					scope.showCustomPicker = true;
					$( '.showCustomPickerTrue' ).hide();

					setTimeout(function() {
						$('.btn-custom-start').daterangepicker({
							autoUpdateInput : true,
							autoApply : true,
							singleDatePicker: true,
							startDate : moment( scope.rangePicker_start, 'DD/MM/YYYY' ).format( 'MM/DD/YYYY' ),
						}, function(start, end, label) {

						  scope.rangePicker_start = moment( start ).format( 'DD/MM/YYYY' );
							$("#rangePicker_start").text( scope.rangePicker_start );

							$('.btn-custom-end').data('daterangepicker').setMinDate( start );

							if( scope.rangePicker_end && ( scope.rangePicker_end > scope.rangePicker_start ) ){
								var activity_search = {
							  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
									end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
							  };
							  // console.log(activity_search);
								if(scope.search.user_id) {
					    		scope.searchEmployeeActivity(scope.search.user_id);
					    	} else {
									scope.searchActivity( activity_search );
					    	}
							}else{
								scope.rangePicker_end = moment( start ).format( 'DD/MM/YYYY' );
								$("#rangePicker_end").text( scope.rangePicker_end );
							}
						});

						$('.btn-custom-end').daterangepicker({
							autoUpdateInput : true,
							autoApply : true,
							singleDatePicker: true,
							startDate : moment( scope.rangePicker_end, 'DD/MM/YYYY' ).format( 'MM/DD/YYYY' ),
						}, function(start, end, label) {
						  
						  scope.rangePicker_end = moment( end ).format( 'DD/MM/YYYY' );
							$("#rangePicker_end").text( scope.rangePicker_end );

							var activity_search = {
						  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
								end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
						  };
						  // console.log(activity_search);
							if(scope.search.user_id) {
				    		scope.searchEmployeeActivity(scope.search.user_id);
				    	} else {
								scope.searchActivity( activity_search );
				    	}
						});

						$("#rangePicker_start").text( scope.rangePicker_start );
						$("#rangePicker_end").text( scope.rangePicker_end );

					}, 100);
				}

				scope.setYear = function( num ){
					$( '.showCustomPickerTrue' ).fadeIn();
					scope.showCustomPicker = false;
					scope.initializeRangeSlider( );

					scope.year_active = num;
					if( num == 1 ){
						yearToday = moment().format('YYYY');
					}else{
						yearToday = moment().subtract(1,'years').format('YYYY');
					}

					// var range_data = date_slider.getValue();

		   //  	var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
		   		var activity_search = {
				  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
				  };
		    	if(scope.search.user_id) {
		    		scope.searchEmployeeActivity(scope.search.user_id);
		    	} else {
						scope.searchActivity( activity_search );
		    	}
				}

				scope.applyDates = function(){
					var activity_search = {
				  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
				  };
					if(scope.search.user_id) {
		    		scope.searchEmployeeActivity(scope.search.user_id);
		    	} else {
						scope.searchActivity( activity_search );
		    	}
				}

				scope.initializeNewCustomDatePicker = function(){
					setTimeout(function() {
						$('.btn-custom-start').daterangepicker({
							autoUpdateInput : true,
							autoApply : true,
							singleDatePicker: true,
							startDate : moment( scope.rangePicker_start, 'DD/MM/YYYY' ).format( 'MM/DD/YYYY' ),
						}, function(start, end, label) {
							scope.currentPage = 1;
						  scope.rangePicker_start = moment( start ).format( 'DD/MM/YYYY' );
							$("#rangePicker_start").text( scope.rangePicker_start );
							$('.btn-custom-end').data('daterangepicker').setMinDate( start );

							if( scope.rangePicker_end && ( moment(scope.rangePicker_end,'DD/MM/YYYY') < moment(scope.rangePicker_start,'DD/MM/YYYY') ) ){
								scope.rangePicker_end = moment( start ).format( 'DD/MM/YYYY' );
								$("#rangePicker_end").text( scope.rangePicker_end );
							}
						});

						$('.btn-custom-end').daterangepicker({
							autoUpdateInput : true,
							autoApply : true,
							singleDatePicker: true,
							startDate : moment( scope.rangePicker_end, 'DD/MM/YYYY' ).format( 'MM/DD/YYYY' ),
						}, function(start, end, label) {
						  scope.currentPage = 1;
						  scope.rangePicker_end = moment( end ).format( 'DD/MM/YYYY' );
							$("#rangePicker_end").text( scope.rangePicker_end );
						});

						// $("#rangePicker_start").text( scope.rangePicker_start );
						// $("#rangePicker_end").text( scope.rangePicker_end );
					}, 100);
				}

				scope.initializeRangeSlider = function( ){

					date_slider = new Slider("#date-slider", { 
						id: "date-slider", 
						min: 1, 
						max: 12, 
						range: true, 
						// value: [parseInt(monthToday), parseInt(monthToday2)],
						value: [1, parseInt(monthToday2)],
						ticks: [1,2,3,4,5,6,7,8,9,10,11,12],
						ticks_labels: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
						tooltip : 'hide',
						ticks_tooltip : false,
					});

					$( '#date-slider' ).on('slideStop', function(ev){
						// clearTimeout(slide_trap);

				    // slide_trap = setTimeout(function() {
				    	var range_data = date_slider.getValue();

				    	monthToday = range_data[0];
				    	monthToday2 = range_data[1];

				    	var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
				    	scope.current_page = 1;
				    	if(scope.search.user_id) {
				    		scope.searchEmployeeActivity(scope.search.user_id);
				    	} else {
								scope.searchActivity( activity_search );
				    	}
				    // }, 800);
					});

				}

				scope.showGlobalModal = function( message ){
			    $( "#global_modal" ).modal('show');
			    $( "#global_message" ).text(message);
			  }

			  scope.getCompanyDetails = function( ) {
        	hrSettings.getCompanyDetails( )
        	.then(function(response){
        		console.log(response);
        		scope.company_details = response.data.data;
        	});
        }

				scope.onLoad = function( ){
					hrSettings.getSession( )
						.then(function(response){
							scope.options.accessibility = response.data.accessibility;
	        	});

					scope.getEmployeeLists( );
					// scope.initializeRangeSlider( );
					scope.initializeNewCustomDatePicker();
					scope.getCompanyDetails();

					setTimeout(function() {
						// var activity_search = scope.getFirstEndDate( 4 , 12 );	
						// var range_data = date_slider.getValue();
				  //   var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
				  	var activity_search = {
					  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
							end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
					  };
						scope.searchActivity( activity_search );
					}, 500);
				}

				scope.checkCompanyBalance = function(){
					hrSettings.getCheckCredits();
				}

				// scope.checkCompanyBalance();
				scope.onLoad( );

				scope.showPreview = function( img , ev){
					var url = "http://docs.google.com/viewer?url=" + img.file + "&embedded=true&chrome=true";

					$(ev.target).closest(".click_box_wrapper").find(".preview-box").fadeIn();

					if( img.file_type == 'image' ){
						$(".preview-box img").show();
						$(".preview-box .img-container").css({'width': '500px'});
						$(".preview-box iframe").hide();
						$(".preview-box img").attr('img-fix-orientation', img.file);

						$(".preview-box img").attr('src', img.file);
					}else{
						$(".preview-box iframe").show();
						$(".preview-box .img-container").css({'width': '80%'});
						$(".preview-box img").hide();
						$(".preview-box #src-view-data").attr('src', img.file);
					}
				}

				scope.hidePreview = function( img ){
					$(".preview-box").fadeOut();
				}

			}
		}
	}
]);