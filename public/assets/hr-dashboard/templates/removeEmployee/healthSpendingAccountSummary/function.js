app.directive('healthSpendingAccountSummaryDirective', [
	'$state',
	'removeEmployeeFactory',
	'dependentsSettings',
	function directive( $state, removeEmployeeFactory, dependentsSettings ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log( 'healthSpendingAccountSummaryDirective running!' );

				scope.emp_details = removeEmployeeFactory.getEmployeeDetails();
				if(scope.emp_details == null || scope.healthSpendingAccountTabIsShow){
					scope.emp_details = scope.selectedEmployee;
					scope.emp_details.last_day_coverage = moment( scope.emp_details.expiry_date, 'MM/DD/YYYY' ).format('DD/MM/YYYY');
				}
				scope.isCalculated = false;
				scope.isMedicalDropShow = false;
				scope.isWellnessDropShow = false;
				scope.isMedicalBalanceDropShow = false;
				scope.isWellnessBalanceDropShow = false;
				scope.isSoloDropShow = false;
				scope.isSoloBalanceDropShow = false;
				console.log(scope.emp_details);
				console.log(scope.spending_account_status);

				scope.calculate	=	function(){
					var dates = {
            start: moment(scope.health_spending_summary.date.pro_rated_start, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            end: moment(scope.health_spending_summary.date.pro_rated_end, 'DD/MM/YYYY').format('YYYY-MM-DD'),
					}
					scope.emp_details.pro_rated_dates = dates;
					scope.isCalculated = true;
					scope.getHealthSpendingSummary(dates);
				}
				scope.getHealthSpendingSummary = function (dates) {
					scope.showLoading();
					dependentsSettings.fetchEmpAccountSummary(scope.emp_details.user_id, scope.selected_customer_id, moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'), dates)
            .then(function (response) {
              console.log(response);
							scope.health_spending_summary = response.data;
							
							scope.emp_details.summary = response.data;
							scope.emp_details.summary.date.pro_rated_start = new Date(moment(scope.emp_details.summary.date.pro_rated_start, 'DD/MM/YYYY'));
							scope.emp_details.summary.date.pro_rated_end = new Date(moment(scope.emp_details.summary.date.pro_rated_end, 'DD/MM/YYYY'));
							if( scope.emp_details.account_type == 'enterprise_plan' ){
								scope.health_spending_summary.medical = {};
								scope.emp_details.summary.medical = {};
							}
							removeEmployeeFactory.setEmployeeDetails( scope.emp_details );
							// scope.health_spending_summary.medical = false;
              scope.initializeNewCustomDatePicker();
              scope.hideLoading();
            });
        }
				scope.backBtn	=	function(){
					if( scope.emp_details.account_type == 'basic_plan' || scope.emp_details.account_type == 'lite_plan' ){
						$state.go('employee-overview.remove-emp-inputs');
					}else{
						$state.go('employee-overview.remove-emp-checkboxes');
					}
					
				}
				scope.nextBtn	=	function(){
					scope.showLoading();
					$state.go('employee-overview.health-spending-account-confirm');
				}
				scope.toggleSumamryTooltipDrop	=	function(opt){
					if(opt == 'medical'){
						scope.isMedicalDropShow = scope.isMedicalDropShow ? false : true;
					}
					if(opt == 'wellness'){
						scope.isWellnessDropShow = scope.isWellnessDropShow ? false : true;
					}
					if(opt == 'medical-balance'){
						scope.isMedicalBalanceDropShow = scope.isMedicalBalanceDropShow ? false : true;
					}
					if(opt == 'wellness-balance'){
						scope.isWellnessBalanceDropShow = scope.isWellnessBalanceDropShow ? false : true;
					}
					if(opt == 'solo'){
						scope.isSoloDropShow = scope.isSoloDropShow ? false : true;
					}
					if(opt == 'solo-balance'){
						scope.isSoloBalanceDropShow = scope.isSoloBalanceDropShow ? false : true;
					}
				}
				scope.formatDate	=	function(date, from, to){
					return moment(date, from).format(to);
				}

				scope.initializeNewCustomDatePicker = function () {
          setTimeout(function () {
            $('.btn-custom-start').daterangepicker({
              autoUpdateInput: true,
              autoApply: true,
              singleDatePicker: true,
              startDate: moment(scope.health_spending_summary.date.pro_rated_start, 'DD/MM/YYYY').format('MM/DD/YYYY'),
            }, function (start, end, label) {
              scope.health_spending_summary.date.pro_rated_start = moment(start).format('DD/MM/YYYY');
              $("#rangePicker_start").text(scope.health_spending_summary.date.pro_rated_start);
              $('.btn-custom-end').data('daterangepicker').setMinDate(start);
            });

            $('.btn-custom-end').daterangepicker({
              autoUpdateInput: true,
              autoApply: true,
              singleDatePicker: true,
              startDate: moment(scope.health_spending_summary.date.pro_rated_end, 'DD/MM/YYYY').format('MM/DD/YYYY'),
            }, function (start, end, label) {
              scope.health_spending_summary.date.pro_rated_end = moment(end).format('DD/MM/YYYY');
              $("#rangePicker_end").text(scope.health_spending_summary.date.pro_rated_end);
            });

            var start = moment(scope.health_spending_summary.date.pro_rated_start, 'DD/MM/YYYY').format('DD/MM/YYYY');
            var end = moment(scope.health_spending_summary.date.pro_rated_end, 'DD/MM/YYYY').format('DD/MM/YYYY');
            $("#rangePicker_start").text(start);
            $("#rangePicker_end").text(end);
            $('.btn-custom-end').data('daterangepicker').setMinDate(start);
          }, 100);
        }

				scope.onLoad	=	function(){
					scope.getHealthSpendingSummary();
				}
				scope.onLoad();
			}
		}
	}
]);