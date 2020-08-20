app.directive('memberMedicalWalletDirective', [
	'$state',
	'$location',
	'hrSettings',
	function directive($state,$location,hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("member medical wallet directive Runnning !");
				console.log($location);

				scope.panelGiroStatus = false;
				scope.panelBankTransfer = false;
				scope.panelMednefitsCredits = false;
				scope.nonPanelGiroStatus = false;
				scope.nonPanelBankTransfer = false;
				scope.nonPanelMednefitsCredits = false;

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
          scope.currentTermEndDate = moment(data.end).format('YYYY-MM-DD');
          scope.showLoading();
          hrSettings.fetchMemberWallet( scope.currentTermStartDate, scope.currentTermEndDate, 'medical' )
            .then(function(response){
							scope.medicalWalletData = response.data.data;
							scope.medicalWalletData.roll_over = scope.medicalWalletData.roll_over.toString();
							scope.medicalWalletData.benefits_start = moment(scope.medicalWalletData.benefits_start).format('DD/MM/YYYY');
							scope.medicalWalletData.benefits_end = moment(scope.medicalWalletData.benefits_end).format('DD/MM/YYYY');
							console.log(scope.medicalWalletData);
							
							scope.hideLoading();
            })
				}

				scope.toggleFunds = function ( type ) {
					if ( type == 'medical-balance' ) {
						$('.credits-tooltip-container.medical-balance').toggle();
						$('.credits-tooltip-container.medical-funds').hide();
						$('.credits-tooltip-container.company-funds').hide();
					}
					if ( type == 'medical-funds' ) {
						$('.credits-tooltip-container.medical-funds').toggle();
						$('.credits-tooltip-container.medical-balance').hide();
						$('.credits-tooltip-container.company-funds').hide();
					}
					if ( type == 'company-funds' ) {
						$('.credits-tooltip-container.company-funds').toggle();
						$('.credits-tooltip-container.medical-balance').hide();
						$('.credits-tooltip-container.medical-funds').hide();
					}
				}

				scope.togglePaymentMethods = function ( type, opt ) {
					if ( type == 'panel_giro' ) {
						scope.panelGiroStatus = opt;
					}
					if ( type == 'panel_bank_transfer' ) {
						scope.panelBankTransfer = opt;
					}
					if ( type == 'panel_mednefits_credits' ) {
						scope.panelMednefitsCredits = opt;
					}
					if ( type == 'non_panel_giro' ) {
						scope.nonPanelGiroStatus = opt;
					}
					if ( type == 'non_panel_bank_transfer' ) {
						scope.nonPanelBankTransfer = opt;
					}
					if ( type == 'non_panel_mednefits_credits' ) {
						scope.nonPanelMednefitsCredits = opt;
					}
				}

				scope.toggleMednefitsCredits = function ( type ) {
					if (  type == 'mednefits-basic-plan' ) {
						$('.credits-tooltip-container.mednefits-basic-plan').toggle();
						$('.credits-tooltip-container.panel-mednefits-credits').hide();
						$('.credits-tooltip-container.non-panel-mednefits-credits').hide();
					}
					if (  type == 'panel-mednefits-credits' ) {
						$('.credits-tooltip-container.mednefits-basic-plan').hide();
						$('.credits-tooltip-container.panel-mednefits-credits').toggle();
						$('.credits-tooltip-container.non-panel-mednefits-credits').hide();
					}
					if (  type == 'non-panel-mednefits-credits' ) {
						$('.credits-tooltip-container.mednefits-basic-plan').hide();
						$('.credits-tooltip-container.panel-mednefits-credits').hide();
						$('.credits-tooltip-container.non-panel-mednefits-credits').toggle();
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
