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
				scope.defaultDateTerms = {};
				scope.applyTerm = false;



				scope.termSelector = function () {
          scope.showLastTermSelector = scope.showLastTermSelector ? false : true;
        }
				
				scope.getDateTerms = async function () {
          await hrSettings.fetchDateTerms()
          .then(async function(response){
						scope.account_details = response.data;
						scope.dateTerm = response.data.data;
            // console.log(scope.dateTerm);
						let termLength = scope.dateTerm.length;
            await scope.dateTerm.map(function(value,index) {
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
            console.log(data);
						scope.selectedTerm = data;
						scope.applyTerm = true;
          } else if (src == 'applyBtn') {
            // let termData = _.filter(scope.dateTerms, index => index.index == scope.dateTermIndex);  //{ 'index': scope.dateTermIndex }
            console.log(data);
            scope.getMemberWalletData(data);
          }
          console.log(scope.selectedTerm)
        }

				scope.getMemberWalletData = async function ( data ) {
					scope.currentTermStartDate = moment(data.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment(data.end).format('YYYY-MM-DD');
          await hrSettings.fetchMemberWallet( scope.currentTermStartDate, scope.currentTermEndDate, 'medical' )
            .then(function(response){
							scope.medicalWalletData = response.data.data;
							scope.medicalWalletData.roll_over = scope.medicalWalletData.roll_over.toString();
							if(scope.medicalWalletData.benefits_coverage == 'out_of_pocket'){
								scope.medicalWalletData.benefits_start = '';
								scope.medicalWalletData.benefits_end = '';
							}else{
								scope.medicalWalletData.benefits_start = moment(scope.medicalWalletData.benefits_start).format('DD/MM/YYYY');
								scope.medicalWalletData.benefits_end = moment(scope.medicalWalletData.benefits_end).format('DD/MM/YYYY');
							}
							// console.log(scope.medicalWalletData);
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

				scope.panelPaymentMethods = function ( opt ) {
					if (scope.medicalWalletData.panel_payment_method != opt) {
						scope._saveButtonTrigger_();
						console.log('save button trigger');
					}
					scope.medicalWalletData.panel_payment_method = opt;
				}
				scope.nonPanelPaymentMethod = function ( opt ) {
					if (scope.medicalWalletData.non_panel_payment_method != opt) {
						scope._saveButtonTrigger_();
						// console.log('save button trigger');
					}
					scope.medicalWalletData.non_panel_payment_method = opt;
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

					scope._saveWallet_();
				}

				scope.getMemberActivity = async function ( data ) {
					data.type = 'medical';
					await hrSettings.fetchMemberWalletActivitiesData( data.customer_id, data.type , scope.page, scope.per_page )
            .then(function(response){
							console.log(response);
							scope.activity_pagination = response.data;
							scope.activity_data = response.data.data;
            })
				}

				scope.range = function (num) {
          var arr = [];
          for (var i = 0; i < num; i++) {
            arr.push(i);
          }
          return arr;
        };

				// pagination activity table
        scope.pagination_dropdown = false;
        scope.pagesToDisplay = 5;
        scope.page_active = 1;
        scope.per_page = 10;
        scope.page = 1;

        scope._toggleInvoicePerPage_ = function () {
          scope.pagination_dropdown = !scope.pagination_dropdown;
        }

        scope._selectNumList_ = function (num) {
          scope.page = num;
          scope.getMemberActivity(scope.selectedTerm);
        }
        scope._prevPageList_ = function () {
					if(scope.page != 1){
						scope.page -= 1;
          	scope.getMemberActivity(scope.selectedTerm);
					}
        }

        scope._nextPageList_ = function () {
          scope.page += 1;
          scope.getMemberActivity(scope.selectedTerm);
        }

        scope._setPageLimit_ = function (num) {
          scope.per_page = num;
          scope.page = 1;
          scope.getMemberActivity(scope.selectedTerm);
        }

				scope._saveWallet_ = async function () {

					let data = {
						id: scope.medicalWalletData.id,
						customer_id: scope.medicalWalletData.customer_id,
						type:'medical',
						start: scope.medicalWalletData.benefits_start,
						end: scope.medicalWalletData.benefits_end,
						active_non_panel_claim:scope.medicalWalletData.non_panel_submission,
						reimbursement:scope.medicalWalletData.non_panel_reimbursement,
						payment_method_panel:scope.medicalWalletData.panel_payment_method,
						payment_method_non_panel:scope.medicalWalletData.non_panel_payment_method,
					}
					scope.showLoading();
					await hrSettings.updateMemberWallet( data )
					.then(async function(response){
						console.log(response);
						await scope.getMemberWalletData( scope.selectedTerm );
						scope.hideLoading();
					})
				}
				
				scope.getStatus = async function () {
					await hrSettings.getPlanStatus( )
            .then(function(response){
							scope.planStatusData = response.data;
							console.log(scope.planStatusData);
						})		
				}

				scope._saveButtonTrigger_ = function () {
					scope.isSaveEnable = true;
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
			

				scope.onLoad = async function () {
					scope.showLoading();
					await scope.getDateTerms();
					await scope.getMemberWalletData(scope.defaultDateTerms);
					await scope.getMemberActivity(scope.defaultDateTerms);
					await scope.getStatus();
					scope.hideLoading();
				}

				scope.onLoad();
				
			}
		}
	}
]);
