app.directive('activityPage', [
	"hrActivity",
	"hrSettings",
	"$timeout",
	"serverUrl",
	function directive(hrActivity, hrSettings, $timeout, serverUrl) {
		return {
			restrict: "A",
			scope: true,
			link: function link(scope, element, attributeSet) {
				console.log('running activityPage');
				scope.inNetWorkTable = true;
				scope.outNetWorkTable = false;
				scope.options = {};
				scope.temp_no_search_activity = {};

				scope.activity = {};
				scope.search = {};
				scope.search.close = false;
				scope.employee_lists = {};
				scope.activity_title = "Team Benefits Cost";
				scope.activity_dates = [];
				scope.eclaim_dates = [];
				scope.selected_list = {};

				scope.inNetwork_pagination = {};
				scope.outNetwork_pagination = {};

				scope.rangePicker_start = moment().startOf('month').format( 'DD/MM/YYYY' );
				scope.rangePicker_end = moment().format( 'DD/MM/YYYY' );
				$("#rangePicker_start").text( scope.rangePicker_start );
				$("#rangePicker_end").text( scope.rangePicker_end );

				scope.showCustomPicker = false;
				scope.year_active = 1;

				scope.currentPage = 1;
				scope.activitySpendingTypeSelected = 'medical';
				scope.activitySpendingType = 0;

				scope.inNetwork_activePage = 1;
				scope.inNetwork_perPage = 10;

				scope.outNetwork_activePage = 1;
				scope.outNetwork_perPage = 10;

				scope.perPage_arr = [10,20,30,50,70,100];

				scope.fetching_data = {
					from : 0,
					to: 0
				}

				scope.statementHide = true;
				scope.empStatementShow = false;

				var monthToday = moment().format('MM');
				var monthToday2 = moment().format('MM');

				var yearToday = moment().format('YYYY');
				var introLoader_trap = false;
				var loading_trap = false;
				var temp_list = null;
				var slide_trap = null;

				var date_slider = null;


				scope.pagesToDisplay = 5;

				scope.isDownloadDropShow = false;
				scope.toggleDownloadDrop = function(){
					scope.isDownloadDropShow = scope.isDownloadDropShow ? false : true;
				}

				scope.selectDownloadOpt = function( opt ){
					if( opt == 0 ){
						$(".inNetwork-dl").click();
					}
					if( opt == 1 ){
						scope.downloadCSV();
					}
					if( opt == 2 ){
						scope.downloadCSVBoth();
					}
					scope.isDownloadDropShow = false;
				}

				scope.downloadCSVBoth = function(){
					var data = {
						token : window.localStorage.getItem('token'),
						start : moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end : moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
						spending_type : scope.activitySpendingTypeSelected,
						status : 3,
					}
					if( scope.search.user_id ){
						data.user_id = scope.search.user_id;
					}
					scope.toggleLoading();
					var api_url = serverUrl.url + "/hr/download_out_of_network_csv?type=both&token=" + data.token + "&start=" + data.start + "&end=" + data.end + "&spending_type=" + data.spending_type + "&status=" + data.status;
			    if( data.user_id ){
			      api_url += ("&user_id=" + data.user_id);
			    }
			    // console.log( api_url );
			    window.open( api_url );
			    scope.toggleLoading();
				}

				scope.companyAccountType = function () {
					scope.account_type = localStorage.getItem('company_account_type');
					console.log(scope.account_type);

					if(scope.account_type === 'enterprise_plan') {
						$('.statement-hide').hide();
						scope.statementHide = false;
						scope.empStatementShow = true;
					}
				}

				scope.downloadCSV = function(){
					var data = {
						token : window.localStorage.getItem('token'),
						start : moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end : moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
						spending_type : scope.activitySpendingTypeSelected,
						status : 3,
					}
					if( scope.search.user_id ){
						data.user_id = scope.search.user_id;
					}
					scope.toggleLoading();
					var api_url = serverUrl.url + "/hr/download_out_of_network_csv?token=" + data.token + "&start=" + data.start + "&end=" + data.end + "&spending_type=" + data.spending_type + "&status=" + data.status;
			    if( data.user_id ){
			      api_url += ("&user_id=" + data.user_id);
			    }
			    // console.log( api_url );
			    window.open( api_url );
			    scope.toggleLoading();
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
							// scope.toggleLoading();
							// var url = "https://docs.google.com/viewer?url=" + img.file + "&embedded=true&chrome=true";
							// console.log('url', url);
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

        scope.startIndex = function(active_page, last_page){
          if( active_page > ((scope.pagesToDisplay / 2) + 1 )) {
            if ((active_page + Math.floor(scope.pagesToDisplay / 2)) > last_page) {
              return last_page - scope.pagesToDisplay + 1;
            }
            return active_page - Math.floor(scope.pagesToDisplay / 2);
          }    
          return 0;
        }

				scope.range = function (range) {
				    var arr = []; 
				    for (var i = 0; i < range; i++) {
				        arr.push(i+1);
				    }
				    return arr;
				}

				scope.downloadReceipt = function( res, all_data ){
					scope.toggleLoading();

					if( res.length > 1 ){
						var zip = new JSZip();

						angular.forEach( res, function(value,key){
							var filename = $.trim( value.file.split('/').pop() );
							filename = filename.substring(0, filename.indexOf('?'));
							var img = zip.folder("images");
							var promise = $.ajax({
				        url: value.file,
				        method: 'GET',
				        xhrFields: {
				          responseType: 'blob'
				        }
					    });

							if( value.file_type == 'pdf' ){
								zip.file(filename, promise);
							}
							if( value.file_type == 'image' ){
								img.file(filename,promise);
							}
							
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
							filename = filename.substring(0, filename.indexOf('?'));
							console.log(filename);
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

				scope.setSpendType = function( opt ){
					scope.activitySpendingType = opt;
					scope.activitySpendingTypeSelected = opt == 0 ? 'medical' : 'wellness';

					var activity_search = {
						start : moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end : moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
					}
					scope.currentPage = 1;
					if(scope.search.user_id) {
		    			scope.searchEmployeeActivity(scope.search.user_id);
			    	}else{
							scope.searchActivity( activity_search );
							scope.searchActivityPagination( );
			    	}
				}

				scope.showDetails = function( e, list ){
					scope.selected_list = list;
					// var height = $( e.currentTarget ).offset().top - $( '.transactions-container' ).offset().top - 120;
					var height = 70;
					$( '.transaction-tr' ).removeClass('active');

					if( temp_list == null || temp_list != list){
						temp_list = list;
						$( e.currentTarget ).addClass('active');
						$( ".main-transac-container" ).animate({'left':'-13%'}, 'slow');
						$( ".trans-pagination-shadow" ).css({'margin-right':'75px'});
						$( ".hidden-details-container" ).css({'top': height+'px'});
						$( ".hidden-details-container" ).animate({'right':'1%'}, 'slow');
					}else{
						temp_list = null;
						$( ".main-transac-container" ).animate({'left':'0'}, 'slow');
						$( ".trans-pagination-shadow" ).css({'margin-right':'0'});
						$( ".hidden-details-container" ).animate({'right':'-100%'}, 'slow');
					}
					console.log( $( ".hidden-details-container" ).height() );
					console.log( $( ".transaction-rows" ).height() );

					$timeout(function() {
						if( $( ".hidden-details-container" ).height() > $( ".transaction-rows" ).height() - 100 ){
							$( ".hidden-details-container" ).css('height', $( ".transaction-rows" ).height());
							$( ".hidden-details-container" ).css('overflow-y', 'auto');
						}else{
							$( ".hidden-details-container" ).css('height', 'auto');
							$( ".hidden-details-container" ).css('overflow', 'inherit');
						}
					}, 500);
					
				}

				scope.filterActivityByDateInNetwork = function( data ){
					scope.activity_dates = [];
					var temp_date = null;
					var ctr = 0;

					data.sort(function (left, right) {
						return moment.utc(right.date_of_transaction, 'DD MMMM YYYY, hh:mma').diff(moment.utc(left.date_of_transaction, 'DD MMMM YYYY, hh:mma'))
					});

					angular.forEach( data ,function(value,key){
						if( temp_date == null ){
							temp_date = value.month;
							scope.activity_dates.push({
								month: temp_date,
								transactions : [value]
							});
						}else{
							if( temp_date == value.month ){
								scope.activity_dates[ctr].transactions.push(value);
								
							}else{
								temp_date = value.month;
								scope.activity_dates.push({
									month: temp_date,
									transactions : [value]
								});

								ctr++;
							}
						}
					});

				}

				scope.filterActivityByDateEclaim = function( data ){
					scope.eclaim_dates = [];
					var temp_date = null;
					var ctr = 0;

					data.sort(function (left, right) {
				    return moment.utc(right.approved_date).diff(moment.utc(left.approved_date))
					});

					angular.forEach( data ,function(value,key){
						// console.log(value.month);
						if( temp_date == null ){
							temp_date = value.month;
							scope.eclaim_dates.push({
								month: temp_date,
								transactions : [value]
							});
						}else{
							if( temp_date == value.month ){
								scope.eclaim_dates[ctr].transactions.push(value);
								
							}else{
								temp_date = value.month;
								scope.eclaim_dates.push({
									month: temp_date,
									transactions : [value]
								});

								ctr++;
							}
						}
					});
				}

				scope.fetchNextPage = function( data ){
					scope.currentPage = scope.currentPage + 1;
					data.page = scope.currentPage;
					hrActivity.getHrActivity(data)
					.then(function(response){
						if(response.status == 200) {

							scope.fetching_data = {
								from : response.data.from,
								to: response.data.total
							}

							angular.forEach( response.data.data.in_network_transactions, function(value,key){
								scope.in_network_transactions.push( value );
							});

							angular.forEach( response.data.data.e_claim_transactions, function(value,key){
								scope.e_claim_transactions.push( value );
							});

							scope.activity.e_claim_spending_format_number += response.data.data.e_claim_spending_format_number;
							scope.activity.in_network_spending_format_number += response.data.data.in_network_spending_format_number;
							scope.activity.total_in_network_spent_format_number += response.data.data.total_in_network_spent_format_number;
							scope.activity.total_lite_plan_consultation += response.data.data.total_lite_plan_consultation;
							scope.activity.total_in_network_transactions += response.data.data.total_in_network_transactions;
							scope.activity.total_spent_format_number += response.data.data.total_spent_format_number;
							
							// if( response.data.data.balance.indexOf(',') > -1 ){
							// 	response.data.data.balance = response.data.data.balance.replace(",", "");
							// }
							// if( response.data.data.allocation.indexOf(',') > -1 ){
							// 	response.data.data.allocation = response.data.data.allocation.replace(",", "");
							// }
							// if( response.data.data.pending_e_claim_amount.indexOf(',') > -1 ){
							// 	response.data.data.pending_e_claim_amount = response.data.data.pending_e_claim_amount.replace(",", "");
							// }

							// response.data.data.balance = parseFloat( response.data.data.balance );
							// response.data.data.allocation = parseFloat( response.data.data.allocation );
							// response.data.data.pending_e_claim_amount = parseFloat( response.data.data.pending_e_claim_amount );

							// scope.activity.balance += response.data.data.balance;
							// scope.activity.allocation += response.data.data.allocation;
							// scope.activity.pending_e_claim_amount += response.data.data.pending_e_claim_amount;

							// scope.activity.in_network_breakdown.dental_care_breakdown += response.data.data.in_network_breakdown.dental_care_breakdown;
							// scope.activity.in_network_breakdown.general_practitioner_breakdown += response.data.data.in_network_breakdown.general_practitioner_breakdown;
							// scope.activity.in_network_breakdown.health_screening_breakdown += response.data.data.in_network_breakdown.health_screening_breakdown;
							// scope.activity.in_network_breakdown.health_specialist_breakdown += response.data.data.in_network_breakdown.health_specialist_breakdown;
							// scope.activity.in_network_breakdown.tcm_breakdown += response.data.data.in_network_breakdown.tcm_breakdown;
							// scope.activity.in_network_breakdown.wellness_breakdown += response.data.data.in_network_breakdown.wellness_breakdown;

							// console.log('scope.activity.in_network_breakdown.general_practitioner_breakdown / scope.activity.in_network_spending_format_number * 100: ' + scope.activity.in_network_breakdown.general_practitioner_breakdown / scope.activity.in_network_spending_format_number * 100);
							// console.log('general_practitioner_breakdown: ' + scope.activity.in_network_breakdown.general_practitioner_breakdown);
							// console.log('in_network_spending_format_number: ' + scope.activity.in_network_spending_format_number);

							if( scope.activity.total_spent_format_number > 0 ){
								scope.spent_progress_percentage = ( scope.activity.in_network_spending_format_number / scope.activity.total_spent_format_number ) * 100;
								$( ".spent-box .progress-wrapper .progress-bar" ).css({ 'width': scope.spent_progress_percentage + '%' });
							}

							if( scope.currentPage != response.data.last_page ){
								while( scope.fetch_ctr < response.data.to ){
									scope.fetching_data.from = scope.fetch_ctr;
									scope.fetch_ctr = scope.fetch_ctr + 1;
									if( scope.fetch_ctr == response.data.to ){
										scope.fetchNextPage( data );
									}
								}
							}else{
								// if( scope.activity.in_network_spending_format_number != 0 ){
								// 	scope.activity.in_network_breakdown.dental_care_breakdown = (scope.activity.in_network_breakdown.dental_care_breakdown / scope.activity.in_network_spending_format_number * 100).toFixed(2);
								// 	scope.activity.in_network_breakdown.general_practitioner_breakdown = (scope.activity.in_network_breakdown.general_practitioner_breakdown / scope.activity.in_network_spending_format_number * 100).toFixed(2);
								// 	scope.activity.in_network_breakdown.health_screening_breakdown = (scope.activity.in_network_breakdown.health_screening_breakdown / scope.activity.in_network_spending_format_number * 100).toFixed(2);
								// 	scope.activity.in_network_breakdown.health_specialist_breakdown = (scope.activity.in_network_breakdown.health_specialist_breakdown / scope.activity.in_network_spending_format_number * 100).toFixed(2);
								// 	scope.activity.in_network_breakdown.tcm_breakdown = (scope.activity.in_network_breakdown.tcm_breakdown / scope.activity.in_network_spending_format_number * 100).toFixed(2);
								// 	scope.activity.in_network_breakdown.wellness_breakdown = (scope.activity.in_network_breakdown.wellness_breakdown / scope.activity.in_network_spending_format_number * 100).toFixed(2);
								// }

								// scope.filterActivityByDateInNetwork( scope.activity.in_network_transactions );
								// scope.filterActivityByDateEclaim( scope.activity.e_claim_transactions );

								scope.fetching_data.from = response.data.total;
								console.log( scope.in_network_transactions );
								scope.in_network_transactions.sort(function (left, right) {
									return moment.utc(right.date_of_transaction, 'DD MMMM YYYY, hh:mma').diff(moment.utc(left.date_of_transaction, 'DD MMMM YYYY, hh:mma'))
								});

								$("#fetching_text").hide();
								$("#done_fetching_text").show();

								setTimeout(function() {
									$("#fetching_users").fadeOut('slow');
								}, 2000);

								$(".searchActivityLoader").hide();
								$(".searchActivityLoader2").hide();
								scope.hideLoading();

								scope.togglePointerEvents();
								scope.currentPage = 1;
								// scope.stockACtivityData();
							}
						}
					});
				}

				scope.downloadInNetwork = function( ) {
					window.open(window.location.origin + '/hr/download_in_network_transactions?start=' + moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD') + '&end=' + moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD') + '&token=' + window.localStorage.getItem('token'));
				}

				scope.searchActivity = function( data ) {
					scope.toggleLoading();
					scope.togglePointerEvents();
					$("#fetching_text").show();
					$("#done_fetching_text").hide();
					$("#fetching_users").show();
					$(".searchActivityLoader").show();
					$(".searchActivityLoader2").show();
					temp_list = null;
					$( ".main-transac-container" ).animate({'left':'0'}, 'slow');
					$( ".trans-pagination-shadow" ).css({'margin-right':'0'});
					$( ".hidden-details-container" ).animate({'right':'-100%'}, 'slow');
					data.page = 1;
					data.spending_type = scope.activitySpendingTypeSelected;
					scope.fetch_ctr = 1;
					scope.fetching_data = {
						from : 0,
						to: 0
					}
					hrActivity.getHrActivity(data)
					.then(function(response){
						// console.log(response);
						// scope.toggleLoading();
						if(response.status == 200) {
							scope.activity = {};
							scope.activity.total_lite_plan_consultation = 0;
							// scope.activity_dates = [];
							// scope.eclaim_dates = [];
							scope.activity = response.data.data;
							
							scope.fetching_data = {
								from : response.data.from,
								to: response.data.total
							}

							if(scope.activity.spending_type == "medical") {
								scope.activity.total_allocation = scope.total_allocation.total_allocation;
							} else {
								scope.activity.total_allocation = scope.total_allocation.total_wellness_allocation;
							}
							scope.in_network_transactions = response.data.data.in_network_transactions;
							scope.e_claim_transactions = response.data.data.e_claim_transactions;
							scope.activity.total_lite_plan_consultation = response.data.data.total_lite_plan_consultation;


							// if( scope.activity.balance.indexOf(',') > -1 ){
							// 	scope.activity.balance = scope.activity.balance.replace(",", "");
							// }
							// if( scope.activity.allocation.indexOf(',') > -1 ){
							// 	scope.activity.allocation = scope.activity.allocation.replace(",", "");
							// }
							// if( scope.activity.pending_e_claim_amount.indexOf(',') > -1 ){
							// 	scope.activity.pending_e_claim_amount = scope.activity.pending_e_claim_amount.replace(",", "");
							// }

							// scope.activity.balance = parseFloat( scope.activity.balance );
							// scope.activity.allocation = parseFloat( scope.activity.allocation );
							// scope.activity.pending_e_claim_amount = parseFloat( scope.activity.pending_e_claim_amount );

							if( scope.activity.total_spent_format_number > 0 ){
								scope.spent_progress_percentage = ( scope.activity.in_network_spending_format_number / scope.activity.total_spent_format_number ) * 100;
							}else{
								scope.spent_progress_percentage = 0;
							}
							$( ".spent-box .progress-wrapper .progress-bar" ).css({ 'width': scope.spent_progress_percentage + '%' });

							if( response.data.last_page > 0 && scope.currentPage != response.data.last_page ){
								scope.fetchNextPage( data );
							}else{
								// if( scope.activity.in_network_spending_format_number != 0 ){
								// 	scope.activity.in_network_breakdown.dental_care_breakdown = (scope.activity.in_network_breakdown.dental_care_breakdown / scope.activity.in_network_spending_format_number * 100).toFixed(2);
								// 	scope.activity.in_network_breakdown.general_practitioner_breakdown = (scope.activity.in_network_breakdown.general_practitioner_breakdown / scope.activity.in_network_spending_format_number * 100).toFixed(2);
								// 	scope.activity.in_network_breakdown.health_screening_breakdown = (scope.activity.in_network_breakdown.health_screening_breakdown / scope.activity.in_network_spending_format_number * 100).toFixed(2);
								// 	scope.activity.in_network_breakdown.health_specialist_breakdown = (scope.activity.in_network_breakdown.health_specialist_breakdown / scope.activity.in_network_spending_format_number * 100).toFixed(2);
								// 	scope.activity.in_network_breakdown.tcm_breakdown = (scope.activity.in_network_breakdown.tcm_breakdown / scope.activity.in_network_spending_format_number * 100).toFixed(2);
								// 	scope.activity.in_network_breakdown.wellness_breakdown = (scope.activity.in_network_breakdown.wellness_breakdown / scope.activity.in_network_spending_format_number * 100).toFixed(2);
								// }

								// scope.filterActivityByDateInNetwork( scope.activity.in_network_transactions );
								// scope.filterActivityByDateEclaim( scope.activity.e_claim_transactions );

								scope.fetching_data.from = response.data.total;
								console.log( scope.in_network_transactions );
								scope.in_network_transactions.sort(function (left, right) {
									return moment.utc(right.date_of_transaction, 'DD MMMM YYYY, hh:mma').diff(moment.utc(left.date_of_transaction, 'DD MMMM YYYY, hh:mma'))
								});

								$("#fetching_text").hide();
								$("#done_fetching_text").show();

								setTimeout(function() {
									$("#fetching_users").fadeOut('slow');
								}, 2000);
								
								$(".searchActivityLoader").hide();
								$(".searchActivityLoader2").hide();

								scope.hideLoading();
								scope.togglePointerEvents();
								scope.stockACtivityData();
							}
							
						}
					});
				}

				scope.searchActivityPagination = function( ){
					scope.getInNetworkPagination( );
					scope.getOutNetworkPagination( );
				}

				scope.getInNetworkPagination = function( ){
					scope.activity_dates = [];
					// scope.toggleLoading();
					var data = {
						start : moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end : moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
						page : scope.inNetwork_activePage,
						per_page : scope.inNetwork_perPage,
						spending_type : scope.activitySpendingTypeSelected,
						customer_id : scope.selected_customer_id
					}
					if( scope.search.user_id ){
						data.user_id = scope.search.user_id;
					}
					hrActivity.getHrActivityInNetworkWithPagination(data)
					.then(function(response){
						// console.log(response);
						// scope.toggleLoading();
						scope.inNetwork_pagination = response.data;

						scope.filterActivityByDateInNetwork( response.data.data );

						$('.transaction-rows').css('height', $(window).innerHeight() );
					});
				}
				scope.getOutNetworkPagination = function(){
					scope.eclaim_dates = [];
					// scope.toggleLoading();
					var data = {
						start : moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end : moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
						page : scope.outNetwork_activePage,
						per_page : scope.outNetwork_perPage,
						spending_type : scope.activitySpendingTypeSelected,
						customer_id : scope.selected_customer_id
					}
					if( scope.search.user_id ){
						data.user_id = scope.search.user_id;
					}
					hrActivity.getHrActivityOutNetworkWithPagination(data)
					.then(function(response){
						// console.log(response);
						// scope.toggleLoading();
						scope.outNetwork_pagination = response.data;

						scope.filterActivityByDateEclaim( response.data.data );
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
				};

				scope.getEmployeeLists = function( ) {
					hrActivity.getEmployeeLists( )
					.then(function(response){
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

				scope.stockACtivityData = function( ){
					scope.temp_no_search_activity.activity_dates = scope.activity_dates;
					scope.temp_no_search_activity.eclaim_dates = scope.eclaim_dates;
					scope.temp_no_search_activity.activity = scope.activity;
					scope.temp_no_search_activity.in_network_transactions = scope.in_network_transactions;
					scope.temp_no_search_activity.e_claim_transactions = scope.e_claim_transactions;
					scope.temp_no_search_activity.total_alloc = scope.total_alloc;
					scope.temp_no_search_activity.total_spent = scope.total_spent;
					scope.temp_no_search_activity.spent_progress_percentage = scope.spent_progress_percentage;
					scope.temp_no_search_activity.total_lite_plan_consultation = scope.activity.total_lite_plan_consultation;
					scope.temp_no_search_activity.lite_plan = scope.activity.lite_plan;
				}

				scope.searchEmployeeActivity = function(user_id) {
					
					scope.toggleLoading();
					temp_list = null;
					$( ".main-transac-container" ).animate({'left':'0'}, 'slow');
					$( ".trans-pagination-shadow" ).css({'margin-right':'0'});
					$( ".hidden-details-container" ).animate({'right':'-100%'}, 'slow');
					var activity_search = null;
					scope.currentPage = 1;
					activity_search = {
						start: moment( scope.rangePicker_start,'DD/MM/YYYY' ).format('YYYY-MM-DD'),
						end: moment( scope.rangePicker_end ,'DD/MM/YYYY').format('YYYY-MM-DD'),
					}
					scope.search.user_id = user_id;
					activity_search.user_id = user_id;
					activity_search.spending_type = scope.activitySpendingTypeSelected;
					scope.search.close = true;
					hrActivity.searchEmployeeActivity(activity_search)
					.then(function(response){
						scope.toggleLoading();
						if(response.status == 200) {
							scope.activity_title = response.data.employee + ' Benefits Cost';
							scope.activity = {};
							scope.activity.total_lite_plan_consultation = 0;
							scope.activity_dates = [];
							scope.eclaim_dates = [];
							scope.activity = response.data;
							scope.activity.total_lite_plan_consultation = response.data.total_lite_plan_consultation;

							if(scope.activity.spending_type == "medical") {
								scope.activity.total_allocation = scope.total_allocation.total_allocation;
							} else {
								scope.activity.total_allocation = scope.total_allocation.total_wellness_allocation;
							}

							if( scope.activity.balance.indexOf(',') > -1 ){
								scope.activity.balance = scope.activity.balance.replace(",", "");
							}
							if( scope.activity.allocation.indexOf(',') > -1 ){
								scope.activity.allocation = scope.activity.allocation.replace(",", "");
							}
							if( scope.activity.pending_e_claim_amount.indexOf(',') > -1 ){
								scope.activity.pending_e_claim_amount = scope.activity.pending_e_claim_amount.replace(",", "");
							}

							scope.activity.balance = parseFloat( scope.activity.balance );
							scope.activity.allocation = parseFloat( scope.activity.allocation );
							scope.activity.pending_e_claim_amount = parseFloat( scope.activity.pending_e_claim_amount );

							if( scope.activity.total_spent_format_number > 0 ){
								scope.spent_progress_percentage = ( scope.activity.in_network_spending_format_number / scope.activity.total_spent_format_number ) * 100;
							}else{
								scope.spent_progress_percentage = 0;
							}
							$( ".spent-box .progress-wrapper .progress-bar" ).css({ 'width': scope.spent_progress_percentage + '%' });
							
							scope.filterActivityByDateInNetwork( scope.activity.in_network_transactions );
							scope.filterActivityByDateEclaim( scope.activity.e_claim_transactions );
							scope.searchActivityPagination();
						}
					});
				};

				scope.closeSeach = function( ) {
					scope.search = {};
					scope.search.close = false;
					scope.activity_title = "Benefits Cost";
					$('.typeahead').val("");
					// scope.toggleLoading();
					// scope.activity = scope.temp_no_search_activity.activity;
					scope.activity_dates = scope.temp_no_search_activity.activity_dates;
					scope.eclaim_dates = scope.temp_no_search_activity.eclaim_dates;
					scope.in_network_transactions = scope.temp_no_search_activity.in_network_transactions;
					scope.e_claim_transactions = scope.temp_no_search_activity.e_claim_transactions;
					scope.total_alloc = scope.temp_no_search_activity.total_alloc;
					scope.total_spent = scope.temp_no_search_activity.total_spent;
					scope.spent_progress_percentage = scope.temp_no_search_activity.spent_progress_percentage;
					$( ".spent-box .progress-wrapper .progress-bar" ).css({ 'width': scope.spent_progress_percentage + '%' });

					// scope.temp_no_search_activity = {};

					var activity_search = {
						start : moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end : moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
					}
					scope.getAllocation( activity_search );

					// setTimeout(function() {
					// 	scope.toggleLoading();
					// }, 1000);
				};

				scope.displayText = function(item) {

				  return item.Name
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
					}else{
						$( ".disable-cursor-off" ).removeClass( "disable-cursor-on" );
						pointer_trap = false;
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

				scope.checkSession = function( ){
					hrSettings.getSession( )
		        	.then(function(response){
		        		// console.log(response);
		        		scope.selected_customer_id = response.data.customer_buy_start_id;
								scope.options.accessibility = response.data.accessibility;
								// scope.getEmployeeLists( );
		        	});
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
							scope.currentPage = 1;
						  scope.rangePicker_start = moment( start ).format( 'DD/MM/YYYY' );
							$("#rangePicker_start").text( scope.rangePicker_start );
							$('.btn-custom-end').data('daterangepicker').setMinDate( start );
							if( scope.rangePicker_end && ( scope.rangePicker_end > scope.rangePicker_start ) ){
								var activity_search = {
							  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
									end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
							  };
								if(scope.search.user_id) {
					    		scope.searchEmployeeActivity(scope.search.user_id);
					    	} else {
									scope.searchActivity( activity_search );
									scope.searchActivityPagination( );
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
						  scope.currentPage = 1;
						  scope.rangePicker_end = moment( end ).format( 'DD/MM/YYYY' );
							$("#rangePicker_end").text( scope.rangePicker_end );
							var activity_search = {
						  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
								end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
						  };
							if(scope.search.user_id) {
				    		scope.searchEmployeeActivity(scope.search.user_id);
				    	} else {
								scope.searchActivity( activity_search );
								scope.searchActivityPagination( );
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
		    	var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
		    	scope.currentPage = 1;
		    	if(scope.search.user_id) {
		    		scope.searchEmployeeActivity(scope.search.user_id);
		    	} else {
						scope.searchActivity( activity_search );
						scope.searchActivityPagination( );
		    	}
				}

				scope.initializeRangeSlider = function( ){
					date_slider = new Slider("#date-slider", { 
						id: "date-slider", 
						min: 1, 
						max: 12, 
						range: true, 
						value: [1, parseInt(monthToday2)],
						ticks: [1,2,3,4,5,6,7,8,9,10,11,12],
						ticks_labels: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
						tooltip : 'hide',
						ticks_tooltip : false,
					});

					$( '#date-slider' ).on('slideStop', function(ev){
						// clearTimeout(slide_trap);
						scope.currentPage = 1;
				    // slide_trap = setTimeout(function() {
			    	var range_data = date_slider.getValue();

			    	monthToday = range_data[0];
			    	monthToday2 = range_data[1];

			    	var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
			    	if(scope.search.user_id) {
			    		scope.searchEmployeeActivity(scope.search.user_id);
			    	} else {
							scope.searchActivity( activity_search );
							scope.searchActivityPagination( );
			    	}
				    // }, 800);
					});
				}

				scope.showGlobalModal = function( message ){
			    $( "#global_modal" ).modal('show');
			    $( "#global_message" ).text(message);
			  }

			  scope.getAllocation = function( dates ){
			  	var data = {
			  		start: dates.start,
			  		end: dates.end
			  	}
			  	hrActivity.getTotalAlloc( data )
			  		.then(function(response){
			  			// console.log(response);
			  			scope.total_allocation = response.data;
			  			var activity_search = {
								start : moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
								end : moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
							}
						if(scope.search.user_id) {
							scope.searchEmployeeActivity(scope.search.user_id);
						}else{
							scope.searchActivity( activity_search );
							scope.searchActivityPagination( );
						}
			  			
			  		});
			  }

			  scope.checkCompanyBalance = function(){

					hrSettings.getCheckCredits();
				}

				scope.applyDates = function(){
					var activity_search = {
				  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
						end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
				  };
					if(scope.search.user_id) {
		    		scope.searchEmployeeActivity(scope.search.user_id);
		    	} else {
						// scope.searchActivity( activity_search );
						scope.getAllocation( activity_search );
						scope.searchActivityPagination( );
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

			// PAGINATION
				scope.innetworkPrev = function() {
					if( scope.inNetwork_activePage != 1 ){
						scope.inNetwork_activePage--;
						scope.getInNetworkPagination();
					}
				}
				scope.innetworkNext = function() {
					if( scope.inNetwork_activePage != scope.inNetwork_pagination.last_page ){
						scope.inNetwork_activePage++;
						scope.getInNetworkPagination();
					}
				}
				scope.innetworkPageTo = function(page) {
					scope.inNetwork_activePage = page;
					scope.getInNetworkPagination();
				}
				scope.innetworkPerPage = function(perpage) {
					scope.inNetwork_activePage = 1;
					scope.inNetwork_perPage = perpage;
					scope.getInNetworkPagination();
					$(".per-page-drop").hide();
				}
				

				scope.outnetworkPrev = function() {
					if( scope.outNetwork_activePage != 1 ){
						scope.outNetwork_activePage--;
						scope.getOutNetworkPagination();
					}
				}
				scope.outnetworkNext = function() {
					if( scope.outNetwork_activePage != scope.outNetwork_pagination.last_page ){
						scope.outNetwork_activePage++;
						scope.getOutNetworkPagination();
					}
				}
				scope.outnetworkPageTo = function(page) {
					scope.outNetwork_activePage = page;
					scope.getOutNetworkPagination();
				}
				scope.outnetworkPerPage = function(perpage) {
					scope.outNetwork_activePage = 1;
					scope.outNetwork_perPage = perpage;
					$(".per-page-drop").hide();
					scope.getOutNetworkPagination();
				}

				scope.onLoad = function( ){
					scope.companyAccountType( );
					scope.checkSession( );
					scope.getEmployeeLists( );
					// scope.initializeRangeSlider( );
					scope.initializeNewCustomDatePicker();

					setTimeout(function() {
						var activity_search = {
							start : moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
							end : moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
						}
						
						$('.btn-custom-end').data('daterangepicker').setMinDate( activity_search.start );
						scope.getAllocation( activity_search );
					}, 500);
				};

				scope.credits = {};

				scope.dashCredits = function( ) {
		        	hrSettings.getCheckCredits()
					.then(function(response){
						console.log(response);
	      				scope.credits = response.data;
					});
		        }

				scope.onLoad( );
				scope.dashCredits( );

				$(document).on('click', ".per-page", function(ev) {
					$(".per-page-drop").fadeIn();
				});

				$("body").click(function(e){
			    if ( $(e.target).parents(".per-page-container").length === 0) {
			      $(".per-page-drop").hide();
			    }
			    if ( $(e.target).parents(".right-download-block").length === 0) {
			      scope.isDownloadDropShow = false;
			      scope.$apply();
			    }
				});

			}
		}
	}
]);	