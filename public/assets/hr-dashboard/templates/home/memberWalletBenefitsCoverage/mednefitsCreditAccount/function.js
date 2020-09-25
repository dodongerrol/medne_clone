app.directive('mednefitsCreditAccountDirective', [
	'$state',
  '$location',
  'hrSettings',
	function directive($state,$location,hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("mednefits credit account directive Runnning !");
				// console.log($location);

        scope.showLastTermSelector = false;
        scope.defaultDateTerms = {};
        scope.isCalculationShow = false;
        scope.isCreditsBonusCreditShow = false;
        scope.isCreditsInputFormShow = true;
        scope.isCreditsConfirmShow = false;
        scope.isTopUpSuccess = false;
        scope.applyTerm = false;

        scope.creditsTopUpData  = {
          total_credits: '0.00',
          purchased_credits: '0.00',
          bonus_credits: '0.00',
          bonus_credits_percentage: 20,
          invoice_date : new Date(),
        }

        scope.isPrepaidCreditsActivated = true;
        scope.isPrepaidCreditsFormShow = false;
        scope.isCreditsCalculationShow = false;

        scope.activateCreditsData  = {
          total_credits: '0.00',
          purchased_credits: '0.00',
          bonus_credits: '0.00',
          bonus_credits_percentage: 20,
          invoice_date: new Date(),
        }

        scope.isMednefitsCreditsSuccessShow = false;

        scope.termSelector = function () {
          scope.showLastTermSelector = scope.showLastTermSelector ? false : true;
          console.log(scope.showLastTermSelector);
        }

        scope.formatDate = function (date) {
          return moment(new Date(date)).format("DD/MM/YYYY");
        };

        scope.formatTableDate = function (date) {
          return moment(new Date(date)).format("DD MMMM YYYY");
        };

        scope.getDateTerms = async function () {
          await hrSettings.fetchDateTerms()
          .then(function(response){
            // console.log(response);
            scope.dateTerm = response.data.data;
            console.log(scope.dateTerm);
            let termLength = scope.dateTerm.length;
            scope.dateTerm.map(function(value,index) {
              if (index == 0) {
                value.term = `Current term (${moment(value.start).format('DD/MM/YYYY')} - ${moment(value.end).format('DD/MM/YYYY')})`;
                value.index = index;
                scope.defaultDateTerms = value;
                scope.selectedTerm = value;
                scope.dateTermIndex = value.index;
              } else {
                value.term = `Last term (${moment(value.start).format('DD/MM/YYYY')} - ${moment(value.end).format('DD/MM/YYYY')})`;
              }
            });
          })
        }

        scope.termSelection = async function (data,src) {
          // data is ang value kai ang index g select sa date terms
          // src if sa select ba or sa apply na button
          if( src == 'select') {
            // scope.dateTermIndex = parseInt(data);
            scope.termSelector();
            scope.selectedTerm = data;
            scope.applyTerm = true;
          } else if (src == 'applyBtn') {
            // let termData = _.filter(scope.dateTerms, index => index.index == scope.dateTermIndex);  //{ 'index': scope.dateTermIndex }
            scope.getMednefitsCreditAccount(data);
          }
        }

        scope.getMednefitsCreditAccount = async function (data,status_data) {
          scope.currentTermStartDate = moment(data.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment( data.end ).format('YYYY-MM-DD');  
          
          scope.showLoading();
          await hrSettings.fetchMednefitsCreditsAccountData( scope.currentTermStartDate, scope.currentTermEndDate )
            .then(function(response){
              // console.log(response);
              if ( response.data.status  ) {
                scope.mednefitsCreditsData = response.data.data;
                scope.isPrepaidCreditsActivated = response.data.status
                scope.hideLoading();
              } else {
                scope.isPrepaidCreditsActivated = response.data.status;
                scope.hideLoading();
              }
             
            })
        }

        scope.getMednefitsCreditActivities = async function ( data ) {
          scope.showLoading();
          await hrSettings.fetchMednefitsActivitiesData( scope.currentTermStartDate, scope.currentTermEndDate, scope.page, scope.per_page )
            .then(function(response){
              // console.log(response);
              scope.hideLoading();
              scope.mednefitsActivitiesData = response.data.data.data;
              scope.spending_activity = response.data.data
            })
        }

        // pagination activity table
        scope.pagination_dropdown = false;
        scope.pagesToDisplay = 5;
        scope.page_active = 1;
        scope.per_page = 10;
        scope.page = 1;

        scope._toggleOpenPerPage_ = function (type) {
          scope.pagination_dropdown = !scope.pagination_dropdown;
        }

        scope._selectNumList_ = function (type, num) {
          scope.page = num;
          scope.getMednefitsCreditActivities(scope.selectedTerm);
          // scope.getEnrollmentHistory(scope.customer_active_plan_id);
        }
        scope._prevPageList_ = function (type) {
          scope.page -= 1;
          scope.getMednefitsCreditActivities(scope.selectedTerm);
          // scope.getEnrollmentHistory(scope.customer_active_plan_id);
        }

        scope._nextPageList_ = function (type) {
          scope.page += 1;
          scope.getMednefitsCreditActivities(scope.selectedTerm);
          // scope.getEnrollmentHistory(scope.customer_active_plan_id);
        }

        scope._setPageLimit_ = function (type, num) {
          scope.per_page = num;
          scope.page = 1;
          scope.getMednefitsCreditActivities(scope.selectedTerm);
          // scope.getEnrollmentHistory(scope.customer_active_plan_id);
        }

        scope.toggleCalculation = function(){
          scope.isCalculationShow = scope.isCalculationShow ? false : true;
        }

        scope.toggleCreditsBonusCredit = function(){
          scope.isCreditsBonusCreditShow = scope.isCreditsBonusCreditShow ? false : true;
        }
        scope.calculateCredits =  function(type){
          scope.creditsTopUpData.total_credits = scope.validateCreditsValue(scope.creditsTopUpData.total_credits);
          scope.creditsTopUpData.purchased_credits = scope.validateCreditsValue(scope.creditsTopUpData.purchased_credits);
          scope.creditsTopUpData.bonus_credits = scope.validateCreditsValue(scope.creditsTopUpData.bonus_credits);

          scope.creditsTopUpData.total_credits = scope.creditsTopUpData.total_credits.replace(/\,/g, '');
          scope.creditsTopUpData.purchased_credits = scope.creditsTopUpData.purchased_credits.replace(/\,/g, '');
          if(type == 'total_credits'){
            scope.creditsTopUpData.purchased_credits = scope.creditsTopUpData.total_credits / ((scope.creditsTopUpData.bonus_credits_percentage / 100) + 1); 
          }
          if(type == 'purchased_credits'){
            scope.creditsTopUpData.total_credits = scope.creditsTopUpData.purchased_credits * ((scope.creditsTopUpData.bonus_credits_percentage / 100) + 1);
          }
          if(type == 'bonus_credits_percentage'){
            scope.creditsTopUpData.purchased_credits = scope.creditsTopUpData.total_credits / ((scope.creditsTopUpData.bonus_credits_percentage / 100) + 1);
            scope.creditsTopUpData.total_credits = scope.creditsTopUpData.purchased_credits * ((scope.creditsTopUpData.bonus_credits_percentage / 100) + 1);
          }
          scope.creditsTopUpData.bonus_credits = parseFloat( scope.creditsTopUpData.total_credits - scope.creditsTopUpData.purchased_credits ).toFixed(2);
          scope.creditsTopUpData.bonus_credits = scope.numberWithCommas( parseFloat( scope.creditsTopUpData.bonus_credits ).toFixed(2) );
          scope.creditsTopUpData.total_credits = scope.numberWithCommas( parseFloat( scope.creditsTopUpData.total_credits ).toFixed(2) );
          scope.creditsTopUpData.purchased_credits = scope.numberWithCommas( parseFloat( scope.creditsTopUpData.purchased_credits ).toFixed(2) );
        }

        scope.onCreditFocus = function(type){
          if(type == 'total_credits' && scope.creditsTopUpData.total_credits == '0.00'){
            scope.creditsTopUpData.total_credits = '';
          }
          if(type == 'purchased_credits' && scope.creditsTopUpData.purchased_credits == '0.00'){
            scope.creditsTopUpData.purchased_credits = '';
          }
        }
        scope.numberWithCommas = function(number) {
          var parts = number.toString().split(".");
          parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          return parts.join(".");
        }

        scope.toggleTopUpCreditsConfirm  = function(opt){
          scope.isCreditsInputFormShow = opt ? false : true;
          scope.isCreditsConfirmShow = opt;
          scope.isTopUpSuccess = false;
        }

        scope.submitTopUpCredits = function ( formData ) {
          var data = {
            // customer_id: Number( scope.selected_customer_id ),
            total_credits: Number( formData.total_credits.replace(/,/g, "") ),
            purchase_credits: Number( formData.purchased_credits.replace(/,/g, "") ),
            bonus_percentage: Number( formData.bonus_credits_percentage / 100 ),
            bonus_credits: Number( formData.bonus_credits.replace(/,/g, "") ),
            invoice_date: moment( formData.invoice_date ).format('YYYY-MM-DD')
          }
          scope.showLoading();
          hrSettings.updateTopUp( data )
            .then(function(response){
              // console.log(response);
              scope.hideLoading();
              if(response.data.status){
                scope.isTopUpSuccess = true;
                scope.getMednefitsCreditAccount(scope.defaultDateTerms,scope.planStatusData)
              }else{
                swal('Error!', response.data.message, 'error');
              }
            })
        }

        scope.topUpCredits = function () {
          scope.isCreditsInputFormShow = true;
          scope.isCreditsConfirmShow = false;
          scope.isTopUpSuccess = false;
          scope.creditsTopUpData.total_credits = '0.00';
          scope.creditsTopUpData.purchased_credits = '0.00';
          scope.creditsTopUpData.bonus_credits = '0.00';
          scope.creditsTopUpData.invoice_date = moment(scope.creditsTopUpData.invoice_date).format('DD/MM/YYYY');
        }

        scope.getStatus = async function () {
					await hrSettings.getPlanStatus( )
            .then(function(response){
							scope.planStatusData = response.data;
						})
						
				}
 

        scope.showLoading = function () {
          $(".circle-loader").fadeIn();
          loading_trap = true;
        };

        scope.hideLoading = function () {
          setTimeout(function () {
            $(".circle-loader").fadeOut();
            loading_trap = false;
          }, 10);
        };

        scope.toggleCreditsActivation = function(){
          scope.isPrepaidCreditsFormShow = scope.isPrepaidCreditsFormShow ? false : true;
          scope.activateCreditsData.invoice_date = moment().format('DD/MM/YYYY');
          setTimeout(() => {
						var dt = new Date();
						// dt.setFullYear(new Date().getFullYear()-18);
						$('.datepicker').datepicker({
							format: 'dd/mm/yyyy',
							endDate: dt
						});
	
						$('.datepicker').datepicker().on('hide', function (evt) {
							var val = $(this).val();
							if (val != "") {
								$(this).datepicker('setDate', val);
							}
						})
					}, 300); 
        }

        scope.toggleCreditsCalculation = function(){
          scope.isCreditsCalculationShow = scope.isCreditsCalculationShow ? false : true;
        }

        scope.calculateActivateCredits =  function(type){
          scope.activateCreditsData.total_credits = scope.validateCreditsValue(scope.activateCreditsData.total_credits);
          scope.activateCreditsData.purchased_credits = scope.validateCreditsValue(scope.activateCreditsData.purchased_credits);
          scope.activateCreditsData.bonus_credits = scope.validateCreditsValue(scope.activateCreditsData.bonus_credits);

          scope.activateCreditsData.total_credits = scope.activateCreditsData.total_credits.replace(/\,/g, '');
          scope.activateCreditsData.purchased_credits = scope.activateCreditsData.purchased_credits.replace(/\,/g, '');
          if(type == 'total_credits'){
            scope.activateCreditsData.purchased_credits = scope.activateCreditsData.total_credits / ((scope.activateCreditsData.bonus_credits_percentage / 100) + 1); 
          }
          if(type == 'purchased_credits'){
            scope.activateCreditsData.total_credits = scope.activateCreditsData.purchased_credits * ((scope.activateCreditsData.bonus_credits_percentage / 100) + 1);
          }
          if(type == 'bonus_credits_percentage'){
            scope.activateCreditsData.purchased_credits = scope.activateCreditsData.total_credits / ((scope.activateCreditsData.bonus_credits_percentage / 100) + 1);
            scope.activateCreditsData.total_credits = scope.activateCreditsData.purchased_credits * ((scope.activateCreditsData.bonus_credits_percentage / 100) + 1);
          }
          scope.activateCreditsData.bonus_credits = parseFloat( scope.activateCreditsData.total_credits - scope.activateCreditsData.purchased_credits ).toFixed(2);
          scope.activateCreditsData.bonus_credits = scope.numberWithCommas( parseFloat( scope.activateCreditsData.bonus_credits ).toFixed(2) );
          scope.activateCreditsData.total_credits = scope.numberWithCommas( parseFloat( scope.activateCreditsData.total_credits ).toFixed(2) );
          scope.activateCreditsData.purchased_credits = scope.numberWithCommas( parseFloat( scope.activateCreditsData.purchased_credits ).toFixed(2) );
        }

        scope.submitActivateMednefitsCredits = function (formData) {
          var data = {
            // customer_id: Number( scope.selected_customer_id ),
            total_credits: Number( formData.total_credits.replace(/,/g, "") ),
            purchase_credits: Number( formData.purchased_credits.replace(/,/g, "") ),
            bonus_percentage: Number( formData.bonus_credits_percentage / 100 ),
            bonus_credits: Number( formData.bonus_credits.replace(/,/g, "") ),
            invoice_date: moment( formData.invoice_date,'DD/MM/YYYY' ).format('YYYY-MM-DD')
          }
          scope.showLoading();
          hrSettings.updatePrepaidCredits( data )
            .then(function(response){
              // console.log(response);
              if (response.status) {
                scope.isMednefitsCreditsSuccessShow = true;
                scope.isPrepaidCreditsFormShow = false;
                scope.isPrepaidCreditsActivated = true;
                scope.getMednefitsCreditAccount(scope.defaultDateTerms,scope.planStatusData);
              } else {
                swal('Error!', response.message, 'error');
              }
            })
        }

        scope.validateCreditsValue = function(value){
          if(value == ''){
            return '0.00';
          }
          return value.replace(/[a-zA-Z\s]/gi, '');
        }

        scope.range = function (num) {
          var arr = [];
          for (var i = 0; i < num; i++) {
            arr.push(i);
          }
          return arr;
        };

        scope.getMemberWalletData = async function ( data, type ) {
					scope.currentTermStartDate = moment(data.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment(data.end).format('YYYY-MM-DD');
          await hrSettings.fetchMemberWallet( scope.currentTermStartDate, scope.currentTermEndDate, type )
            .then(function(response){
              if(type == 'medical'){
                scope.medicalWalletData = response.data.data;
                scope.medicalWalletData.roll_over = scope.medicalWalletData.roll_over.toString();
                scope.medicalWalletData.benefits_start = moment(scope.medicalWalletData.benefits_start).format('DD/MM/YYYY');
                scope.medicalWalletData.benefits_end = moment(scope.medicalWalletData.benefits_end).format('DD/MM/YYYY');
              }
              if(type == 'wellness'){
                scope.wellnessActivated = response.data.status;
                scope.wellnessWalletData = response.data.data;
                scope.wellnessWalletData.roll_over = scope.wellnessWalletData.roll_over.toString();
                scope.wellnessWalletData.benefits_start = moment(scope.wellnessWalletData.benefits_start).format('DD/MM/YYYY');
                scope.wellnessWalletData.benefits_end = moment(scope.wellnessWalletData.benefits_end).format('DD/MM/YYYY');
              }
            })
        }
        
        scope.closeModal  = function(){
          $(".modal").modal('close');
        }

       
        scope.onLoad = async function () {
          scope.showLoading();
          await scope.getStatus();
          await scope.getDateTerms();
          await scope.getMednefitsCreditAccount(scope.defaultDateTerms,scope.planStatusData);
          await scope.getMednefitsCreditActivities();
          await scope.getMemberWalletData(scope.defaultDateTerms, 'medical');
          await scope.getMemberWalletData(scope.defaultDateTerms, 'wellness');

        }

        scope.onLoad();
				
			}
		}
	}
]);
