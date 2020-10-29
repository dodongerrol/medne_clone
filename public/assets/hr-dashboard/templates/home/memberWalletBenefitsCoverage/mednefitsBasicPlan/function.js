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
        scope.edit_medical_wallet_details = {};
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
            scope.applyTerm = true;
          } else if (src == 'applyBtn') {
            // let termData = _.filter(scope.dateTerms, index => index.index == scope.dateTermIndex);  //{ 'index': scope.dateTermIndex }
            console.log(data);
            scope.getBenefitsCoverageData(data);
            scope._getPaymentDetails_(data);
          }
          console.log(scope.selectedTerm)
        }

        scope.getBenefitsCoverageData = async function ( data ) {
          scope.showLoading();
          await hrSettings.fetchBenefitsCoverageData( data.start, data.end, 'basic_plan' )
            .then(function(response){
              console.log(response);
							scope.benefitsCoverageData = response.data;
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
          scope.edit_medical_wallet_details = {
            panel: scope.medical_wallet_details.panel_payment_method,
            non_panel: scope.medical_wallet_details.non_panel_payment_method
          };
          scope.edit_wellness_wallet_details = {
            // panel: scope.wellness_wallet_details.panel_payment_method,
            non_panel: scope.wellness_wallet_details.non_panel_payment_method
          };


          scope.isConfirmPaymentShow = false;
          scope.updateButtonStatus = false;
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
            console.log(response);
            scope.wellness_wallet_details = response.data.data;
            console.log(scope.wellness_wallet_details);
          
            scope.hideLoading();
          })
        }

        scope.updatePaymentMethods = async function ( type ) {
          if ( type == 'update' ) {
            scope.isConfirmPaymentShow = true;
          }
          if ( type == 'confirm' ) {
            let data = {
              id: 1,
              customer_id: 334,
              medical_panel_payment_method: scope.edit_medical_wallet_details.panel,
              medical_non_panel_payment_method: scope.edit_medical_wallet_details.non_panel,
              wellness_non_panel_payment_method: scope.edit_wellness_wallet_details.non_panel,
            }
            console.log(data);
            scope.showLoading();
            await hrSettings.updatePaymentMethods( data )
            .then(async function(response){
              console.log(response);
              if ( response.data.status == true ) {
                $('#edit-payment-methods-modal').modal('hide');
                await scope._getPaymentDetails_(scope.selectedTerm);
                await scope.getBenefitsCoverageData(scope.selectedTerm);
                scope.hideLoading();
              }							
            })
          }
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

        scope.updateButtonStatus = false;
        scope.panelSelector = function () {
          scope.updateButtonStatus = true;
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
            .then(async function(response){
              console.log(response);	
              scope.hideLoading();
              
              await scope._getPaymentDetails_(scope.selectedTerm);
              await scope.getBenefitsCoverageData(scope.selectedTerm);
              scope.isConfirmModal = true;
            })
          }
        }

       
        scope.onLoad = async function () {
          scope.showLoading();
          if (scope.defaultDateTerms == null) {
            await scope.getDateTerms();
          }
          await scope.getBenefitsCoverageData(scope.defaultDateTerms);
          await scope._getPaymentDetails_(scope.defaultDateTerms);
        }

        scope.onLoad();
				
			}
		}
	}
]);
