app.directive('statementPage', [
	"hrActivity",
	"hrSettings",
	"serverUrl",
	function directive(hrActivity, hrSettings, serverUrl) {
		return {
			restrict: "A",
			scope: true,
			link: function link(scope, element, attributeSet) {
				console.log('running statementPage');
				scope.overview = {};
				scope.options = {};
				scope.full = {};
				scope.search = {};
				scope.search.close = false;
				scope.employee_lists = {};
				scope.selected_list = {};
				scope.filter_text = 'All';
				scope.filter_num = 1;
				scope.net_active = 'inNetWorkTable';
				scope.overview_active = true;
				scope.full_active = false;
				scope.monthStart = moment().startOf('month').format('D MMMM');
				scope.monthEnd = moment().endOf('month').format('D MMMM');
				scope.year = moment().format('YYYY');
				scope.download_token = {};
				scope.rangePicker_start = moment().startOf('month').format( 'DD/MM/YYYY' );
				scope.rangePicker_end = moment().endOf('month').format( 'DD/MM/YYYY' );

				scope.showCustomPicker = false;
				scope.year_active = 1;

				var monthToday = moment().format('MM');
				var monthToday2 = moment().format('MM');
				var yearToday = moment().format('YYYY');
				var introLoader_trap = false;
				var loading_trap = false;
				var temp_list = null;
				var slide_trap = null;

				var date_slider = null;

				scope.spendingTypeOpt = 2;
				scope.spendingTypeFilter = undefined;

				scope.setSpendingType = function( opt ){
					scope.toggleLoading();
					scope.spendingTypeOpt = opt;

					if( opt == 0 ){
						scope.spendingTypeFilter = 'medical';
					}else if( opt == 1 ){
						scope.spendingTypeFilter = 'wellness';
					}else{
						scope.spendingTypeFilter = undefined;
					}

					scope.toggleLoading();
				}

				scope.downloadCSV = function(){
					var data = {
						token : window.localStorage.getItem('token'),
						start : moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end : moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
						spending_type : scope.spendingTypeFilter == undefined ? "both" : scope.spendingTypeFilter,
						status : 3,
					}
					if( scope.search.user_id ){
						data.user_id = scope.search.user_id;
					}
					scope.toggleLoading();
					console.log( data );
					var api_url = serverUrl.url + "/hr/download_out_of_network_csv?token=" + data.token + "&start=" + data.start + "&end=" + data.end + "&spending_type=" + data.spending_type + "&status=" + data.status;
			    if( data.user_id ){
			      api_url += ("&user_id=" + data.user_id);
			    }
			    console.log( api_url );
			    window.open( api_url );
			    scope.toggleLoading();
				}

				scope.toggleNetwork = function( net ) {
					scope.net_active = net;
				}

				scope.downloadReceipt = function( res, all_data ){
					scope.toggleLoading();
					var zip = new JSZip();
					var main_folder = zip.folder( all_data.member + "_" + all_data.transaction_id );
					console.log( res );
					angular.forEach( res, function(value,key){
						var filename = $.trim( value.file.split('/').pop().replace(/\.*/,'') );
						filename = ( filename.indexOf("?") >= 0 ) ? filename.substring(0, filename.indexOf('?')) : filename;
						console.log( filename );
						var promise = $.ajax({
			        url: value.file,
			        method: 'GET',
			        xhrFields: {
			          responseType: 'blob'
			        }
				    });

						main_folder.file(filename, promise);
						
						if( key == (res.length-1) ){
							console.log('asdfsa');
							zip.generateAsync({type:"blob"}).then(function(content) {
						    saveAs(content, all_data.member + "_" + all_data.transaction_id + ".zip");
							});
							scope.toggleLoading();
						}
					})
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
					hrActivity.uploadOutNetworkReceipt( data )
						.then(function(response){
							// console.log(response);
							list.uploading = false;
							if( response.data.status == true ){
								response.data.receipt = response.data.file_link;
								list.files.push( response.data.receipt );
							}
						})
						.catch(function(response){
							// console.log(response);
							list.uploading = false;
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
					scope.filter_num = num;

					if( num == 1 ){
						scope.filter_text = 'All';
					}else if( num == 2 ){
						scope.filter_text = 'Pending';
					}else if( num == 3 ){
						scope.filter_text = 'Approved';
					}else{
						scope.filter_text = 'Rejected';
					}
				}

				scope.updateStatus = function( list, num ){
					scope.toggleLoading();
					var data = {
						e_claim_id: list.transaction_id,
						status: num
					}

					hrActivity.updateEclaimStatus( data )
					.then(function(response){
						scope.toggleLoading();
						if( response.data.status == true ){
							list.status = num;

							if( list.status == 1 ){
								list.status_text = 'Approved';
							}
							if( list.status == 2 ){
								list.status_text = 'Rejected';
							}

						}
					});
				}

			scope.downloadPDF = function(invoice_data) {
				if(scope.download_token.live == true) {
					window.open(scope.download_token.download_link + "/spending_invoice_download?id=" + invoice_data.statement_id + '&token=' + scope.download_token.token);
				} else {
					window.open(serverUrl.url + '/hr/statement_download?id=' + invoice_data.statement_id + '&token=' + window.localStorage.getItem('token'));
				}
		  	};

		  	scope.downloadFullINPDF = function(invoice_data, type) {
		  		if(type == "pdf") {
			  		if(scope.download_token.live == true) {
			  			window.open(scope.download_token.download_link + "/spending_transactions_download?id=" + invoice_data.statement_id + '&token=' + scope.download_token.token);
			  		} else {
			  			window.open(serverUrl.url + '/hr/statement_in_network_download?id=' + invoice_data.statement_id + '&token=' + window.localStorage.getItem('token') + '&type=' + type);
			  		}
		  		} else {
		  			window.open(serverUrl.url + '/hr/statement_in_network_download?id=' + invoice_data.statement_id + '&token=' + window.localStorage.getItem('token') + '&type=' + type);
		  		}
		  	};

		  	scope.downloadFullOUTPDF = function(invoice_data) {
		  		console.log(invoice_data);
		  		window.open(serverUrl.url + '/hr/statement_eclaim_download?id=' + invoice_data.statement_id + '&token=' + window.localStorage.getItem('token'));
		  	};

		  	function getCanvas(){
			    form.width((a4[0]*1.33333) -80).css('max-width','none');
			    return html2canvas(form,{
			        imageTimeout:2000,
			        removeContainer:true,
			        allowTaint: false,
			        useCORS: true
			      });
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

				scope.isStatusShow = true;

				scope.searchActivity = function( data ) {
					scope.toggleLoading();
					hrActivity.getOverviewStatement(data)
						.then(function(response){
							scope.toggleLoading();
							console.log(response);

							if( response.data.status == false ){
								scope.overview = {};
								scope.dl_in_network = {};
								scope.dl_eclaim = {};
								scope.full = {};
								scope.full.total_e_claim_spent = "0.00";
								scope.full.total_transaction_spent = "0.00";
								scope.hideIfNoTransaction = true;
								scope.err_msg = response.data.message;
							}else{
								scope.overview = {};
								scope.overview = response.data.data;
								scope.dl_in_network = response.data.data.in_network_transactions;
								scope.dl_eclaim = response.data.data.e_claim_transactions;
								scope.full = {};
								scope.full = response.data.data;
								console.log( scope.dl_eclaim );
								scope.hideIfNoTransaction = false;
								scope.isStatusShow = response.data.data.show_status;
							}
						});

					// hrActivity.getFullStatement(data)
					// 	.then(function(response){
					// 		console.log(response);
					// 		scope.full = {};
					// 		scope.full = response.data;
					// 		scope.dl_in_network = response.data.in_network_transactions;
					// 		scope.dl_eclaim = response.data.e_claim_transactions;

					// 	});
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
					   		scope.searchEmployeeActivity(item.user_id);
					   	}, 100);
					   }
					  });
					});
				};

				scope.initializeSearch = function( ) {
					scope.getEmployeeLists( );
				}

				scope.searchEmployeeActivity = function(user_id) {
					var range_data = date_slider.getValue();
					var activity_search = null;
					scope.toggleLoading();
					if( scope.showCustomPicker ){
						activity_search = {
							start: moment( scope.rangePicker_start,'DD/MM/YYYY' ).format('YYYY-MM-DD'),
							end: moment( scope.rangePicker_end ,'DD/MM/YYYY').format('YYYY-MM-DD'),
						}
					}else{
						activity_search = scope.getFirstEndDate( range_data, range_data );
					}
					scope.search.user_id = user_id;
					activity_search.user_id = user_id;
					scope.search.close = true;
					hrActivity.searchEmployeeStatement(activity_search)
					.then(function(response){
						scope.toggleLoading();
						if(response.status == 200) {
							scope.full = {};
							scope.full = response.data;
						}
					});
				};

				scope.closeSeach = function( ) {
					scope.search.close = false;
					scope.activity_title = "Benefits Cost";
					$('.typeahead').val("");
					var range_data = date_slider.getValue();
					var activity_search = scope.getFirstEndDate( range_data, range_data );
					scope.searchActivity( activity_search );
				};

				scope.displayText = function(item) {
				  return item.Name
				};

				scope.afterSelect = function(item) {
				  console.log(item);
				};

				scope.getFirstEndDate = function( firstMonth, lastMonth ){
					firstMonth = moment( firstMonth + " " + yearToday,'MM YYYY').format('YYYY-MM-DD');
					lastMonth = moment( lastMonth + " " + yearToday,'MM YYYY').format('YYYY-MM-DD');

					var date1 = new Date(firstMonth);
					var date2 = new Date(lastMonth);
					var y1 = date1.getFullYear();
					var m1 = date1.getMonth();
					var y2 = date2.getFullYear();
					var m2 = date2.getMonth();
					var firstDay = new Date(y1, m1, 1);
					var lastDay = new Date(y2, m2 + 1, 0);

					firstDay = moment(firstDay).format('YYYY-MM-DD');
					lastDay = moment(lastDay).format('YYYY-MM-DD');

					scope.monthStart = moment(firstDay).startOf('month').format('D MMMM');
					scope.monthEnd = moment(lastDay).endOf('month').format('D MMMM');
					scope.year = yearToday;

					scope.rangePicker_start = moment( firstDay ).startOf('month').format( 'DD/MM/YYYY' );
					scope.rangePicker_end = moment( lastDay ).endOf('month').format( 'DD/MM/YYYY' );
					// console.log(scope.monthStart);
					// console.log(scope.monthEnd);

					return {
						start: firstDay,
						end: lastDay,
						// customer_id: scope.user_details.UserID
					}
				};

				scope.hideIntroLoader = function( ){
					setTimeout(function() {
						$( ".main-loader" ).fadeOut();
						introLoader_trap = false;
					}, 100);
				}

				scope.toggleLoading = function( ){
					if ( loading_trap == false ) {
						$( ".circle-loader" ).fadeIn();
						loading_trap = true;
					}else{
						setTimeout(function() {
							$( ".circle-loader" ).fadeOut();
							loading_trap = false;
						},100)
					}
				}

				scope.showLoading = function( ){
					$( ".circle-loader" ).fadeIn();
					loading_trap = true;
				}

				scope.hideLoading = function( ){
					setTimeout(function() {
						$(".circle-loader").hide();
						loading_trap = false;
					},10)
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
							scope.monthStart = moment( start ).format('D MMMM');

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
							scope.monthEnd = moment( end ).format('D MMMM');
							scope.year = moment( end ).format( 'YYYY' );

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

					var range_data = date_slider.getValue();


		    	var activity_search = scope.getFirstEndDate( range_data, range_data );
		    	// var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
		    	if(scope.search.user_id) {
		    		scope.searchEmployeeActivity(scope.search.user_id);
		    	} else {
						scope.searchActivity( activity_search );
		    	}
				}

				scope.initializeRangeSlider = function( ){

					date_slider = new Slider("#date-slider", {
						id: "date-slider",
						min: 1,
						max: 12,
						range: false,
						value: parseInt(monthToday2),
						// value: parseInt(monthToday),
						// value: [parseInt(monthToday), parseInt(monthToday2)],
						ticks: [1,2,3,4,5,6,7,8,9,10,11,12],
						ticks_labels: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
						tooltip : 'hide',
						ticks_tooltip : false,
					});

					$( '#date-slider' ).on('slideStop', function(ev){
						clearTimeout(slide_trap);

					    slide_trap = setTimeout(function() {
					    	var range_data = date_slider.getValue();
					    	monthToday = range_data;
				    		// monthToday2 = range_data[1];
					    	var activity_search = scope.getFirstEndDate( range_data, range_data );
					    	console.log(activity_search);
					    	if(scope.search.user_id) {
					    		scope.searchEmployeeActivity(scope.search.user_id);
					    	}else{
									scope.searchActivity( activity_search );
					    	}
					    }, 800);
					});

				}

				scope.showPreview = function( img , ev){
					$(ev.target).closest(".click_box_wrapper").find(".preview-box").fadeIn();

					if( img.file_type == 'image' ){
						$(".preview-box img").show();
						$(".preview-box .img-container").css({'width': '500px'});
						$(".preview-box iframe").hide();
						$(".preview-box img").attr('img-fix-orientation', img.file);

						$(".preview-box img").attr('src', img.file);
					}else{
						// scope.toggleLoading();
						// hrSettings.getEclaimPresignedUrl(img.e_claim_doc_id)
						// .then(function(response){
						// 	scope.toggleLoading();
							// var url = "https://docs.google.com/viewer?url=" + img.file + "&embedded=true&chrome=true";
							$(".preview-box iframe").show();
							$(".preview-box .img-container").css({'width': '80%'});
							$(".preview-box img").hide();
							$(".preview-box #src-view-data").attr('src', img.file);
						// });
					}
				}

				scope.hidePreview = function( img ){
					$(".preview-box").fadeOut();
				}

				scope.showGlobalModal = function( message ){
			    $( "#global_modal" ).modal('show');
			    $( "#global_message" ).text(message);
			  }

				scope.onLoad = function( ){
					hrSettings.getSession( )
	        	.then(function(response){
							scope.options.accessibility = response.data.accessibility;
	        	});
					scope.getEmployeeLists( );
					scope.initializeRangeSlider( );
					setTimeout(function() {
						// var activity_search = scope.getFirstEndDate( 4 , 12 );
						var range_data = date_slider.getValue();
						var activity_search = scope.getFirstEndDate( range_data , range_data );
						scope.searchActivity( activity_search );
					}, 100);
				}

				scope.checkCompanyBalance = function(){
					hrSettings.getCheckCredits();
				}

				scope.getDownloadToken = function( ) {
		          hrSettings.getDownloadToken( )
		          .then(function(response){
		            console.log(response);
		            scope.download_token = response.data;
		          });
		        }


				// scope.checkCompanyBalance();
				scope.getDownloadToken( );
				scope.onLoad( );

			}
		}
	}
]);
