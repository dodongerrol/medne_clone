app.directive('memberWellnessWalletDirective', [
	'$state',
	'$location',
	'hrSettings',
	function directive($state,$location,hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("member wellness wallet directive Runnning !");
				console.log($location);
        
				scope.giroStatus = false;
				scope.reimbursementStatus = false;
				scope.paymentGiroStatus = false;
				scope.paymentBankTransferStatus = false;
				scope.paymentMednefitsCreditsStatus = false;
				// scope.medicalWalletData = {
				// 	non_panel_reimbursement : false,
				// }

				scope.termSelector = function () {
          scope.showLastTermSelector = scope.showLastTermSelector ? false : true;
        }
				
				scope.getDateTerms = function () {
          hrSettings.fetchDateTerms()
          .then(function(response){
            scope.dateTerm = response.data.data;
            // console.log(scope.dateTerm);

            // scope.currentTerm = scope.dateTerm.slice(-1).pop();
            // console.log(scope.currentTerm );

						// tempo lang ni
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
						
						scope.getMemberWalletData(scope.defaultDateTerms);
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
            scope.getMemberWalletData(data);
          }
          console.log(scope.selectedTerm)
        }
				
				scope.getMemberWalletData = function ( data ) {
					scope.currentTermStartDate = moment(data.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment(data.end ).format('YYYY-MM-DD');
					
					scope.showLoading();
          hrSettings.fetchMemberWallet( scope.currentTermStartDate, scope.currentTermEndDate, 'wellness' )
            .then(function(response){
							scope.wellnessWalletData = response.data.data;
							scope.wellnessWalletData.roll_over = scope.wellnessWalletData.roll_over.toString();
							scope.wellnessWalletData.benefits_start = moment(scope.wellnessWalletData.benefits_start).format('DD/MM/YYYY');
							scope.wellnessWalletData.benefits_end = moment(scope.wellnessWalletData.benefits_end).format('DD/MM/YYYY');
							console.log(scope.wellnessWalletData);
							
							scope.hideLoading();
            })
				}

        scope._accountSelector_ = function ( opt ) {
        	scope.giroStatus = opt;
				}
				
				scope.toggleReimbursement = function ( opt ) {
					scope.reimbursementStatus = opt;
				}

				scope.toggleWellnessPaymentMethod = function ( type, opt ) {
					if ( type == 'paymentGiro' ) {
						scope.paymentGiroStatus = opt;
					}
					if ( type == 'paymentBankTransfer' ) {
						scope.paymentBankTransferStatus = opt;
					}
					if ( type == 'paymentMednefitsCredits' ) {
						scope.paymentMednefitsCreditsStatus = opt;
					}
				}

				scope.toggleMednefitsCredits = function () {
					$('.credits-tooltip-container.mednefits-credits').toggle();
				}
				
				scope.toggleWellnessBasicPlan = function () {
					$('.credits-tooltip-container.mednefits-basic-plan').toggle();
				}

				scope.toggleWellnessTooltip = function ( type ) {
					if ( type == 'wellness-balance' ) {
						$('.credits-tooltip-container.wellness-balance').toggle();
						$('.credits-tooltip-container.wellness-budget').hide();
						$('.credits-tooltip-container.company-budget').hide();
					}
					if ( type == 'wellness-budget' ) {
						$('.credits-tooltip-container.wellness-balance').hide();
						$('.credits-tooltip-container.wellness-budget').toggle();
						$('.credits-tooltip-container.company-budget').hide();
					}
					if ( type == 'company-budget' ) {
						$('.credits-tooltip-container.wellness-balance').hide();
						$('.credits-tooltip-container.wellness-budget').hide();
						$('.credits-tooltip-container.company-budget').toggle();
					}
				}

				scope.toggleNonPanelClaim = function ( type,opt ) {
					if ( type == 'non-panel-reimbursement' ) {
						scope.medicalWalletData.non_panel_reimbursement = opt;
					}
					if ( type == 'non-panel-submission' ) {
						scope.medicalWalletData.non_panel_submission = opt;
					}
				}

				scope.showLoading = function () {
					$(".circle-loader").fadeIn();
					loading_trap = true;
				}

				scope.hideLoading = function( ){
					setTimeout(function() {
						$(".circle-loader").hide();
						loading_trap = false;
					},10)
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
