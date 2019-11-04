app.directive('activityDirective', [
	'$state',
	'Upload',
	'serverUrl',
	'eclaimSettings',
	function directive($state,Upload,serverUrl,eclaimSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("activityDirective Runnning !");

				scope.inNetWorkTable = true;
				scope.outNetWorkTable = false;
				scope.in_network_transactions = {};
        scope.e_claim_transactions = {};

				scope.rangePicker_start = moment().startOf('year').format( 'DD/MM/YYYY' );
				scope.rangePicker_end = moment().format( 'DD/MM/YYYY' );

				scope.showCustomPicker = false;
				scope.year_active = 1;

				scope.spendingTypeOpt = 0;
				scope.spendingTypeSelected = 'medical';

				var monthToday = moment().format('MM');
				var monthToday2 = moment().format('MM');
				var yearToday = moment().format('YYYY');

				var introLoader_trap = false;
				var loading_trap = false;

				var slide_trap = null;
				scope.xs_start = parseInt(monthToday);
				scope.xs_start_text = moment( monthToday ,'MM').format('MMMM');
				scope.xs_end = parseInt(monthToday);
				scope.xs_end_text= moment( monthToday ,'MM').format('MMMM');

				scope.month_arr = [
					{
						value : 1,
						month : 'January'
					},
					{
						value : 2,
						month : 'February'
					},
					{
						value : 3,
						month : 'March'
					},
					{
						value : 4,
						month : 'April'
					},
					{
						value : 5,
						month : 'May'
					},
					{
						value : 6,
						month : 'June'
					},
					{
						value : 7,
						month : 'July'
					},
					{
						value : 8,
						month : 'August'
					},
					{
						value : 9,
						month : 'September'
					},
					{
						value : 10,
						month : 'October'
					},
					{
						value : 11,
						month : 'November'
					},
					{
						value : 12,
						month : 'December'
					},
				];

				scope.openDetails = function( list ){
					if( list.showTransacDetails == true ){
						list.showTransacDetails = false;
					}else{
						list.showTransacDetails = true;
					}
				}

				scope.downloadReceipt = function( res, all_data ){
					scope.toggleLoading();

					if( res.length > 1 ){
						var zip = new JSZip();

						angular.forEach( res, function(value,key){
							var filename = $.trim( value.file.split('/').pop() );
							var promise = $.ajax({
				        url: value.file,
				        method: 'GET',
				        xhrFields: {
				          responseType: 'blob'
				        }
					    });

							zip.file(filename, promise);
							
							if( key == (res.length-1) ){
								zip.generateAsync({type:"blob"}).then(function(content) {
							    saveAs(content, all_data.member + "_" + all_data.transaction_id + ".zip");
								});
								scope.toggleLoading();
							}
						})
					}else{

						angular.forEach( res, function(value,key){
							var filename = $.trim( value.file.split('/').pop() );
							$.ajax({
				        url: value.file,
				        method: 'GET',
				        xhrFields: {
				          responseType: 'blob'
				        },
				        success: function (data) {
			            var a = document.createElement('a');
			            var url = window.URL.createObjectURL(data);
			            a.href = url;
			            a.download = filename;
			            a.click();
			            window.URL.revokeObjectURL(url);

			            if( key == (res.length-1) ){
			            	scope.toggleLoading();
			            }
				        }
					    });
						});
					}
				}

				scope.spendingType = function( opt ){
					scope.spendingTypeOpt = opt;
					scope.spendingTypeSelected = opt == 0 ? 'medical' : 'wellness';
					var activity_search = {
				  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
				  };
					scope.searchActivity( activity_search );
				}

				scope.uploadReceipt = function( list ){
					if( !list.transaction_files ){
						list.transaction_files = [];
					}
					list.uploading = true;
					var data = {
						file : list.upload, 
						transaction_id : list.transaction_id
					}
					eclaimSettings.uploadInNetworkReceipt( data )
						.then(function(response){
							// console.log(response);
							list.uploading = false;
							if( response.data.status == true ){
								list.transaction_files.push( response.data.receipt );
								list.upload_err = false;
							}else{
								list.upload_err = true;
								list.upload_err_message = response.data.message;
							}
						})
						.catch(function(response){
							// console.log(response);
							list.uploading = false;
							list.upload_err = true;
							list.upload_err_message = 'Something went wrong. Please check your internet connection.'
						});
				}

				scope.uploadReceiptOut = function( list ){
					if( !list.files ){
						list.files = [];
					}
					list.uploading = true;
					var data = {
						file : list.upload, 
						e_claim_id : list.transaction_id
					}
					eclaimSettings.uploadOutNetworkReceipt( data )
						.then(function(response){
							// console.log(response);
							list.uploading = false;
							if( response.data.status == true ){
								response.data.receipt.file = response.data.receipt.doc_file;
								list.files.push( response.data.receipt );
							}
						})
						.catch(function(response){
							// console.log(response);
							list.uploading = false;
						});
				}

				scope.searchActivity = function( data ){
					scope.toggleLoading();
					data.spending_type = scope.spendingTypeSelected;
					eclaimSettings.employeeSearchActivity( data )
						.then(function(response){
							console.log(response.data)
							scope.activity_results = response.data;
							console.log(scope.activity_results);
							if( parseInt(scope.activity_results.total_allocation) > 0 ){
								if( scope.activity_results.total_allocation.indexOf(",") >= 0 ){
									scope.activity_results.total_allocation = scope.activity_results.total_allocation.replace(",", "");
								}
								scope.in_network_transactions = response.data.in_network_transactions;
								scope.e_claim_transactions = response.data.e_claim;

								scope.spent_total = scope.activity_results.total_spent;
								if( scope.spent_total.indexOf(",") >= 0 ){
									scope.spent_total = scope.spent_total.replace(",", "");
								}

								if( scope.spent_total > 0 ){
									scope.spent_in_network = scope.activity_results.in_network_spent;

									if( scope.spent_in_network.indexOf(",") >= 0 ){
										scope.spent_in_network = scope.spent_in_network.replace(",", "");
									}
									scope.spent_progress_percentage = ( scope.spent_in_network / scope.spent_total ) * 100;

								}else{
									scope.spent_progress_percentage = 0;
								}
							}else{
								scope.spent_progress_percentage = 0;
							}
							$( ".spent-box .progress-wrapper .progress-bar" ).css({ 'width': scope.spent_progress_percentage + '%' });
							scope.hideIntroLoader( );
							scope.toggleLoading();
						});
				}

				scope.getFirstEndDate = function( firstMonth, lastMonth ){
					var startOfMonth = moment( firstMonth + " " + yearToday,'MM YYYY' ).startOf('month').format('YYYY-MM-DD');
					var endOfMonth   = moment( lastMonth + " " + yearToday,'MM YYYY' ).endOf('month').format('YYYY-MM-DD');
					// console.log( startOfMonth );
					// console.log( endOfMonth );
					monthToday = moment( startOfMonth ).format( 'MM' );
					monthToday2 = moment( endOfMonth ).format( 'MM' );
					scope.rangePicker_start = moment( startOfMonth ).format( 'DD/MM/YYYY' );
					scope.rangePicker_end = moment( endOfMonth ).format( 'DD/MM/YYYY' );
					$("#rangePicker_start").text( scope.rangePicker_start );
					$("#rangePicker_end").text( scope.rangePicker_end );
					
					return {
						start: startOfMonth,
						end: endOfMonth,
					}
				}

				scope.getDetails = function( ){
					eclaimSettings.empDetails( )
						.then(function( response ) {
							scope.user_details = response.data.data;
							scope.hideIntroLoader();
						});
				}

				scope.changeStartDate = function( start ){
					console.log(start);
					scope.xs_start = start.value;
					scope.xs_start_text = start.month;
					if( scope.xs_end != null ){
						clearTimeout(slide_trap);
				    slide_trap = setTimeout(function() {
				    	var range_data = [ scope.xs_start, scope.xs_end ];
				    	var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
							scope.searchActivity( activity_search );
				    }, 800);
					}
				}

				scope.changeEndDate = function( end ){
					scope.xs_end = end.value;
					scope.xs_end_text = end.month;
					if( scope.xs_start != null ){
						clearTimeout(slide_trap);
				    slide_trap = setTimeout(function() {
				    	var range_data = [ scope.xs_start, scope.xs_end ];
				    	var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
							scope.searchActivity( activity_search );
				    }, 800);
					}
				}

				//---- LOADERS -----

				scope.hideIntroLoader = function( ){
					setTimeout(function() {
						$( ".main-loader" ).fadeOut();
						$( ".circle-loader" ).fadeOut();
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

				// --------------
				scope.applyDates = function(){
					var activity_search = {
				  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
				  };
					scope.searchActivity( activity_search );
				}

				scope.showCustomDate = function( num ){
					scope.year_active = num;

					scope.showCustomPicker = true;

					if( $(window).width() > 573 ){
						$( '.showCustomPickerTrue' ).hide();
					}else{
						$( '.xs-date-selector' ).hide();
					}

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
								scope.searchActivity( activity_search );
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
							scope.searchActivity( activity_search );
						});

						$("#rangePicker_start").text( scope.rangePicker_start );
						$("#rangePicker_end").text( scope.rangePicker_end );

					}, 100);
				}

				scope.setYear = function( num ){
					console.log( $(window).width() );

					if( $(window).width() > 573 ){
						$( '.showCustomPickerTrue' ).fadeIn();
					}else{
						$( '.xs-date-selector' ).fadeIn();
					}

					scope.showCustomPicker = false;
					scope.initializeRangeSlider( );

					scope.year_active = num;
					if( num == 1 ){
						yearToday = moment().format('YYYY');
					}else{
						yearToday = moment().subtract(1,'years').format('YYYY');
					}

					var activity_search = {
				  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
				  };
					scope.searchActivity( activity_search );
				}

				scope.initializeRangeSlider = function( ){

					date_slider = new Slider("#timeframe-range", { 
						id: "timeframe-range", 
						min: 1, 
						max: 12, 
						range: true, 
						value: [1, parseInt(monthToday2)],
						// value: [parseInt(monthToday), parseInt(monthToday2)],
						ticks: [1,2,3,4,5,6,7,8,9,10,11,12],
						ticks_labels: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
						tooltip : 'hide',
						ticks_tooltip : false,
					});

					$( '#timeframe-range' ).on('slideStop', function(ev){
						clearTimeout(slide_trap);

				    slide_trap = setTimeout(function() {
				    	var range_data = date_slider.getValue();

				    	monthToday = range_data[0];
				    	monthToday2 = range_data[1];

				    	var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );

							scope.searchActivity( activity_search );
				    }, 800);
					});
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

						$("#rangePicker_start").text( scope.rangePicker_start );
						$("#rangePicker_end").text( scope.rangePicker_end );
					}, 100);
				}

				scope.onLoad = function( ){
					scope.showLoading( );
					scope.getDetails( );
					// scope.initializeRangeSlider();
					scope.initializeNewCustomDatePicker();
					
					setTimeout(function() {
						var activity_search = {
							start : moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
							end : moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
						}
						
						$('.btn-custom-end').data('daterangepicker').setMinDate( moment( activity_search.start, 'YYYY-MM-DD' ).format( 'MM/DD/YYYY' ) );
						scope.searchActivity( activity_search );
					}, 500);
        }

        scope.downloadMednefitsReceipt = function(id) {
        	// window.location.href = serverUrl.url + '/download/transaction_receipt/' + id;
        	window.open(serverUrl.url + '/download/transaction_receipt/' + id);
        };

        scope.onLoad();

        scope.showPreview = function( img ){
					console.log(img);

					

					$(".preview-box").fadeIn();

					if( img.file_type == 'image' ){
						$(".preview-box img").show();
						$(".preview-box .img-container").css({'width': '500px'});
						$(".preview-box iframe").hide();
						$(".preview-box img").attr('src', img.file);
					}else{
						// scope.toggleLoading();
						// eclaimSettings.getEclaimPresignedUrl(img.e_claim_doc_id)
						// .then(function(response){
						// 	scope.toggleLoading();
							// var url = "https://docs.google.com/viewer?url=" + img.file + "&embedded=true&chrome=true";
							$(".preview-box iframe").show();
							$(".preview-box .img-container").css({'width': '80%'});
							$(".preview-box img").hide();
							$(".preview-box #src-view-data").attr('src', img.file);
						// });
						// $(".preview-box iframe").show();
						// $(".preview-box .img-container").css({'width': '80%'});
						// $(".preview-box img").hide();
						// $(".preview-box #src-view-data").attr('src', url);
					}
				}

			}
		}
	}
]);
