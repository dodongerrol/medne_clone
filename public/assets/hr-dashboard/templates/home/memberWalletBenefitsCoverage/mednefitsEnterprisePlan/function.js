app.directive('mednefitsEnterprisePlanDirective', [
	'$state',
  '$location',
  'hrSettings',
	function directive($state,$location,hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("mednefits basic plan directive Runnning !");
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

        scope.getDateTerms = function () {
          hrSettings.fetchDateTerms()
          .then(function(response){
            scope.dateTerm = response.data.data;
            console.log(scope.dateTerm);

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

        scope.getBenefitsCoverageData = function ( data ) {
					scope.currentTermStartDate = moment(data.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment(data.end).format('YYYY-MM-DD');
          scope.showLoading();
          hrSettings.fetchBenefitsCoverageData( scope.currentTermStartDate, scope.currentTermEndDate, 'enterprise_plan' )
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

        scope.toggleVisits = function ( type ) {
          if ( type == 'total-utilised-visits' ) {
            $('.credits-tooltip-container.total-utilised-visits').toggle();
            $('.credits-tooltip-container.utilised-panel-visits').hide(); 
            $('.credits-tooltip-container.utilised-non-panel-visits').hide();  
            $('.credits-tooltip-container.visits-per-member').hide(); 
          }
          if ( type == 'utilised-panel-visits' ) {
            $('.credits-tooltip-container.total-utilised-visits').hide();
            $('.credits-tooltip-container.utilised-panel-visits').toggle(); 
            $('.credits-tooltip-container.utilised-non-panel-visits').hide();  
            $('.credits-tooltip-container.visits-per-member').hide(); 
          }
          if ( type == 'utilised-non-panel-visits' ) {
            $('.credits-tooltip-container.total-utilised-visits').hide();
            $('.credits-tooltip-container.utilised-panel-visits').hide(); 
            $('.credits-tooltip-container.utilised-non-panel-visits').toggle();  
            $('.credits-tooltip-container.visits-per-member').hide(); 
          }
          if ( type == 'visits-per-member' ) {
            $('.credits-tooltip-container.total-utilised-visits').hide();
            $('.credits-tooltip-container.utilised-panel-visits').hide(); 
            $('.credits-tooltip-container.utilised-non-panel-visits').hide();  
            $('.credits-tooltip-container.visits-per-member').toggle(); 
          }
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

       
        scope.onLoad = function () {
          scope.showLoading();
          scope.getDateTerms();
        }

        scope.onLoad();
				
			}
		}
	}
]);
