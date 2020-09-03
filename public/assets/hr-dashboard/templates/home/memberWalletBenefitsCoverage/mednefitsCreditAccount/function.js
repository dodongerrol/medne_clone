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
				console.log($location);

        scope.showLastTermSelector = false;
        scope.defaultDateTerms = {};
        scope.isCalculationShow = false;
        scope.isCreditsBonusCreditShow = false;
        scope.isCreditsInputFormShow = true;
        scope.isCreditsConfirmShow = false;
        scope.isTopUpSuccess = false;

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
            console.log(response);
            scope.dateTerm = response.data.data;
            // console.log(scope.dateTerm);

            // scope.currentTerm = scope.dateTerm.slice(-1).pop();
            // console.log(scope.currentTerm );

            let termLength = scope.dateTerm.length;
            // console.log(termLength);

            scope.dateTerm.map(function(value,index) {
              if (index == termLength-1) {
                value.term = `Current term (${moment(value.start).format('DD/MM/YYYY')} - ${moment(value.end).format('DD/MM/YYYY')})`;
                value.index = index;
                scope.defaultDateTerms = value;
                scope.selectedTerm = value;
                scope.dateTermIndex = value.index;
              } else {
                value.term = `Last term (${moment(value.start).format('DD/MM/YYYY')} - ${moment(value.end).format('DD/MM/YYYY')})`;
              }
            });

            // scope.getMedicalMemberWallet(scope.defaultDateTerms);
            // scope.getWellnessMemberWallet(scope.defaultDateTerms);
          })
        }

        scope.termSelection = async function (data,src) {
          // data is ang value kai ang index g select sa date terms
          // src if sa select ba or sa apply na button
          if( src == 'select') {
            // scope.dateTermIndex = parseInt(data);
            scope.termSelector();
            console.log(data);
            scope.selectedTerm = data;
          } else if (src == 'applyBtn') {
            // let termData = _.filter(scope.dateTerms, index => index.index == scope.dateTermIndex);  //{ 'index': scope.dateTermIndex }
            console.log(data);
            scope.getMednefitsCreditAccount(data);
          }
          console.log(scope.selectedTerm)
        }

        scope.getMednefitsCreditAccount = async function (data,status_data) {
          scope.currentTermStartDate = moment(data.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment( data.end ).format('YYYY-MM-DD');  
          
          scope.showLoading();
          await hrSettings.fetchMednefitsCreditsAccountData( scope.currentTermStartDate, scope.currentTermEndDate )
            .then(function(response){
              console.log(response);
              if ( response.data.status  ) {
                scope.mednefitsCreditsData = response.data.data;
                scope.isPrepaidCreditsActivated = response.data.status
                scope.hideLoading();
                console.log(scope.mednefitsCreditsData);
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
              console.log(scope.spending_activity);
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
          console.log(num);
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
        scope.numberWithCommas = function(number) {
          var parts = number.toString().split(".");
          parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          return parts.join(".");
        }

        scope.toggleTopUpCreditsConfirm  = function(opt){
          // console.log(opt);
          // if ( opt == false ) {
          //   scope.isCreditsConfirmShow = true;
          //   scope.isCreditsInputFormShow = false;
          //   scope.isTopUpSuccess = false;
          // }
          scope.isCreditsInputFormShow = opt ? false : true;
          scope.isCreditsConfirmShow = opt;
          scope.isTopUpSuccess = false;
          console.log( scope.isCreditsInputFormShow );
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
          console.log(data);
          hrSettings.updateTopUp( data )
            .then(function(response){
              console.log(response);
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
							console.log(scope.planStatusData);
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

        // get the keys for member wallet 
        scope.getMedicalMemberWallet = function ( data ) {
          scope.currentTermStartDate = moment(data.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment( data.end ).format('YYYY-MM-DD');

          console.log('gkan sa member ug wellness',data);
          hrSettings.fetchMemberWallet( scope.currentTermStartDate, scope.currentTermEndDate, 'medical')
            .then(function(response){
              console.log('medical',response);
             
            })
        }

        scope.getWellnessMemberWallet = function ( data ) {
          scope.currentTermStartDate = moment(data.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment( data.end ).format('YYYY-MM-DD');

          console.log('gkan sa member ug wellness',data);
          hrSettings.fetchMemberWallet( scope.currentTermStartDate, scope.currentTermEndDate, 'wellness' )
            .then(function(response){
              console.log('wellness',response);
             
            })
        }

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
          console.log(formData);
          var data = {
            // customer_id: Number( scope.selected_customer_id ),
            total_credits: Number( formData.total_credits.replace(/,/g, "") ),
            purchase_credits: Number( formData.purchased_credits.replace(/,/g, "") ),
            bonus_percentage: Number( formData.bonus_credits_percentage / 100 ),
            bonus_credits: Number( formData.bonus_credits.replace(/,/g, "") ),
            invoice_date: moment( formData.invoice_date,'DD/MM/YYYY' ).format('YYYY-MM-DD')
          }
          console.log(data);
          scope.showLoading();
          hrSettings.updatePrepaidCredits( data )
            .then(function(response){
              console.log(response);
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
          console.log(value);
        }

        scope.range = function (num) {
          var arr = [];
          for (var i = 0; i < num; i++) {
            arr.push(i);
          }
          return arr;
        };

       
        scope.onLoad = async function () {
          scope.showLoading();
          await scope.getStatus();
          await scope.getDateTerms();
          await scope.getMednefitsCreditAccount(scope.defaultDateTerms,scope.planStatusData);
          await scope.getMednefitsCreditActivities();
        }

        scope.onLoad();
				
			}
		}
	}
]);
