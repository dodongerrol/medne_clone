app.directive('outOfPocketDirective', [
	'$state',
  '$location',
  'hrSettings',
	function directive($state,$location,hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("out of pocket directive Runnning !");
				console.log($location);

        scope.showLastTermSelector = false;
        scope.applyTerm = false;
        

        scope.termSelector = function () {
          scope.showLastTermSelector = scope.showLastTermSelector ? false : true;
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

            scope.getBenefitsCoverageData(scope.defaultDateTerms);
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
            scope.applyTerm = true;
          } else if (src == 'applyBtn') {
            // let termData = _.filter(scope.dateTerms, index => index.index == scope.dateTermIndex);  //{ 'index': scope.dateTermIndex }
            console.log(data);
            scope.getBenefitsCoverageData(data);
          }
          console.log(scope.selectedTerm)
        }

        scope.getBenefitsCoverageData = async function ( data ) {
					scope.currentTermStartDate = moment(data.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment(data.end).format('YYYY-MM-DD');
          scope.showLoading();
          await hrSettings.fetchBenefitsCoverageData( scope.currentTermStartDate, scope.currentTermEndDate, 'out_of_pocket' )
            .then(function(response){
              console.log(response);
							scope.benefitsCoverageData = response.data;
							// scope.medicalWalletData.roll_over = scope.medicalWalletData.roll_over.toString();
							// scope.medicalWalletData.benefits_start = moment(scope.medicalWalletData.benefits_start).format('DD/MM/YYYY');
							// scope.medicalWalletData.benefits_end = moment(scope.medicalWalletData.benefits_end).format('DD/MM/YYYY');
							console.log(scope.benefitsCoverageData);
							
							scope.hideLoading();
            })
        }
        
        scope._getPaymentDetails_ = async function ( data ) {
            // console.log(data);
            await hrSettings.fetchMemberWallet( data.start, data.end, 'medical' )
            .then(function(response){
              // console.log(response);
              scope.medical_wallet_details = response.data.data;
              console.log(scope.medical_wallet_details);
							scope.hideLoading();
            })

            await hrSettings.fetchMemberWallet( data.start, data.end, 'wellness' )
            .then(function(response){
              // console.log(response);
							scope.wellness_wallet_details = response.data.data;
              console.log(scope.wellness_wallet_details);
						
							scope.hideLoading();
            })
        }

        scope.getActivitiesDataTable = async function ( data ) {
          // console.log(data);
          await hrSettings.fetchMednefitsActivitiesData( data.start,data.end,scope.page,scope.per_page, 'out_of_pocket' )
          .then(function(response){
            // console.log(response);
            scope.spending_activity = response.data.data;
            console.log(scope.spending_activity);
          
            scope.hideLoading();
          })
        }

        scope.toggleTransaction = function () {
          $('.credits-tooltip-container.total-member-transaction').toggle();
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
          scope.getActivitiesDataTable(scope.selectedTerm);
          // scope.getEnrollmentHistory(scope.customer_active_plan_id);
        }
        scope._prevPageList_ = function (type) {
          scope.page -= 1;
          scope.getActivitiesDataTable(scope.selectedTerm);
          // scope.getEnrollmentHistory(scope.customer_active_plan_id);
        }

        scope._nextPageList_ = function (type) {
          scope.page += 1;
          scope.getActivitiesDataTable(scope.selectedTerm);
          // scope.getEnrollmentHistory(scope.customer_active_plan_id);
        }

        scope._setPageLimit_ = function (type, num) {
          scope.per_page = num;
          scope.page = 1;
          scope.getActivitiesDataTable(scope.selectedTerm);
          // scope.getEnrollmentHistory(scope.customer_active_plan_id);
        }


        scope.range = function (num) {
          var arr = [];
          for (var i = 0; i < num; i++) {
            arr.push(i);
          }
          return arr;
        };
        

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

       
        scope.onLoad = async function () {
          scope.showLoading();
          await scope.getDateTerms();
          await scope._getPaymentDetails_(scope.defaultDateTerms);
          await scope.getActivitiesDataTable(scope.defaultDateTerms);
        }

        scope.onLoad();
				
			}
		}
	}
]);
