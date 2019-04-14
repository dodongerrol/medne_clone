app.directive('empDetailsDirective', [
	'$state',
	'$stateParams',
	'eclaimSettings',
	function directive($state,$stateParams,eclaimSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("empDetailsDirective Runnning !");

				scope.user_details = {};
				scope.current_spending = {};
				scope.spendingTypeOpt = 0;
				scope.employee_packages = {};

				var introLoader_trap = false;
				var loading_trap = false;

				scope.spendingType = function ( opt ) {
					scope.spendingTypeOpt = opt;

					var spending_type = opt == 0 ? 'medical' : 'wellness';
					scope.getCurrentActivity(spending_type);
					// opt == 0 ? scope.getCurrentActivityMedical() : scope.getCurrentActivityWellness();
				}

				// scope.getDetails = ( ) => {
				// 	scope.showLoading( );
				// 	eclaimSettings.empDetails( )
				// 		.then(function( response ) {
				// 			scope.user_details = response.data.data;
				// 			// scope.hideLoading( );
				// 			eclaimSettings.notification(parseInt(response.data.data.UserID));
				// 			scope.getCurrentActivityMedical( );
				// 		});
				// }

				scope.populatePie = function(total, current , color) {
					var balance = total - current;
					var pieColor;
					if( color ){
						pieColor = ['#b6e2e4','#b6e2e4'];
					}else{
						pieColor = [ '00B2E1', '8EA4AA' ];
					}
					var data = [{
					  values: [current, balance],
					  type: 'pie',
					  hole: .8,
					  showlegend : false,
					  hoverinfo : 'none',
					  marker : {
					  	colors : pieColor
					  },
					  textinfo: 'none',
					  sort:false
					}];
					var layout = {
					  height: 330,
					  width: 330
					};
					Plotly.newPlot('statusPieGraph', data, layout);
				};

				scope.getCurrentActivity = function(speding_type) {
					scope.showLoading();
					eclaimSettings.employeeCurrentActivity(speding_type)
						.then(function(response){
							scope.hideLoading();
							if(response.status == 200) {
								scope.current_spending = response.data;
								if( response.data.current_spending_format_number != 0 && response.data.in_network_spent_format_number != 0 ){
									scope.populatePie(response.data.current_spending_format_number, response.data.in_network_spent_format_number);
								}else{
									scope.populatePie(1,0,true);
								}
							}
						});
				};

				// scope.getCurrentActivityMedical = function( ) {
				// 	scope.showLoading();
				// 	eclaimSettings.employeeCurrentActivity( )
				// 		.then(function(response){
				// 			scope.hideLoading();
				// 			if(response.status == 200) {
				// 				scope.current_spending = response.data;
				// 				if( response.data.current_spending_format_number != 0 && response.data.in_network_spent_format_number != 0 ){
				// 					scope.populatePie(response.data.current_spending_format_number, response.data.in_network_spent_format_number);
				// 				}else{
				// 					scope.populatePie(1,0,true);
				// 				}
				// 			}
				// 		});
				// };

				// scope.getCurrentActivityWellness = function( ) {
				// 	scope.showLoading();
				// 	eclaimSettings.employeeCurrentActivityWellness( )
				// 		.then(function(response){
				// 			scope.hideLoading();
				// 			if(response.status == 200) {
				// 				scope.current_spending = response.data;
				// 				if( response.data.current_spending_format_number != 0 && response.data.in_network_spent_format_number != 0 ){
				// 					scope.populatePie(response.data.current_spending_format_number, response.data.in_network_spent_format_number);
				// 				}else{
				// 					scope.populatePie(1,0,true);
				// 				}
				// 			}
				// 		});
				// };

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

				scope.getPackages = function( ) {
					eclaimSettings.getPackages( )
					.then(function(response){
						if(response.data) {
							scope.user_details = response.data;
							// console.log(scope.user_details);
							scope.employee_packages = response.data.packages;
							eclaimSettings.notification(parseInt(scope.user_details.member_id));
						}
					})
				}

				scope.onLoad = function( ){
					scope.hideIntroLoader();
					// scope.getDetails( );
					scope.getCurrentActivity('medical');
					scope.getPackages( );
					
				}


				scope.onLoad();

			}
		}
	}
]);
