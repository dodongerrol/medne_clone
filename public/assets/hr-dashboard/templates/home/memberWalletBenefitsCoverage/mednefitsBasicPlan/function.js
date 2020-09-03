app.directive('mednefitsBasicPlanDirective', [
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
        scope.isConfirmPaymentShow = false;
        scope.medical_wallet_details = {
          panel_payment_method: 'mednefits_credits',
          non_panel_payment_method: 'mednefits_credits',
        }
        scope.wellness_wallet_details = {
          non_panel_payment_method: 'mednefits_credits',
        }
        scope.isConfirmModal = false;

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
            // console.log(scope.dateTerm);

            // scope.currentTerm = scope.dateTerm.slice(-1).pop();
            // console.log(scope.currentTerm );

            // // tempo lang ni dire
            // scope.hideLoading();

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
            scope._getPaymentDetails_(scope.defaultDateTerms);
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
            scope.getBenefitsCoverageData(data);
          }
          console.log(scope.selectedTerm)
        }

        scope.getBenefitsCoverageData = function ( data ) {
					scope.currentTermStartDate = moment(data.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment(data.end).format('YYYY-MM-DD');
          scope.showLoading();
          hrSettings.fetchBenefitsCoverageData( scope.currentTermStartDate, scope.currentTermEndDate, 'basic_plan' )
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

        scope.toggleFunds = function ( ) {
          $('.credits-tooltip-container.total-spent').toggle();
        }
        
        scope.medicalPanel = function ( opt ) {
          scope.medicalPanelValue = opt;
          console.log(scope.medicalPanelValue);
        }

        scope.editMednefitsCredits = function () {
          scope.isConfirmPaymentShow = false;
        }

        scope.updatePaymentMethods = function ( type ) {
          if ( type == 'update' ) {
            scope.isConfirmPaymentShow = true;
          }
          if ( type == 'confirm' ) {
            let data = {
              id: 1,
              customer_id: 334,
              medical_panel_payment_method: scope.medical_wallet_details.panel_payment_method,
              medical_non_panel_payment_method: scope.medical_wallet_details.non_panel_payment_method,
              wellness_non_panel_payment_method: scope.wellness_wallet_details.non_panel_payment_method,
            }
            console.log(data);
            scope.showLoading();
            hrSettings.updatePaymentMethods( data )
            .then(function(response){
              console.log(response);
              if ( response.data.status == true ) {
                $('#edit-payment-methods-modal').modal('hide');
                scope.hideLoading();
              }							
            })
          }
        }

        scope._getPaymentDetails_ = function ( data ) {
          // console.log(data);
          hrSettings.fetchMemberWallet( data.start, data.end, 'medical' )
            .then(function(response){
              // console.log(response);
              scope.medical_wallet_details = response.data.data;
              console.log(scope.medical_wallet_details);
							scope.hideLoading();
            })

            hrSettings.fetchMemberWallet( data.start, data.end, 'wellness' )
            .then(function(response){
              // console.log(response);
							scope.wellness_wallet_details = response.data.data;
              console.log(scope.wellness_wallet_details);
						
							scope.hideLoading();
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

        scope.showConfirmationModal = function () {
          scope.isConfirmModal = false;
        }

        scope._isConfirmModalBtn_ = async function ( type, src ) {
          if ( type == 'confirm' && src == 'activate' ) {
            await hrSettings.fetchBasicPlan( )
            .then(function(response){
              console.log(response);	
              scope.hideLoading();
              
              scope.getBenefitsCoverageData(scope.selectedTerm);
              scope.isConfirmModal = true;
            })
          }
        }

       
        scope.onLoad = function () {
          scope.showLoading();
          scope.getDateTerms();
        }

        scope.onLoad();
				
			}
		}
	}
]);
