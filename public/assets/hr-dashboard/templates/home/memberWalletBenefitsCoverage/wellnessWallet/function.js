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
				scope.isWellnessWalletShow = false;
				scope.isSaveEnable = false;
				scope.wellnessActivated = false;
				scope.applyTerm = false;
				// scope.medicalWalletData = {
				// 	non_panel_reimbursement : false,
				// }

				scope.termSelector = function () {
          scope.showLastTermSelector = scope.showLastTermSelector ? false : true;
        }
				
				scope.getDateTerms = async function () {
          await hrSettings.fetchDateTerms('wellness')
          .then(async function(response){
						scope.account_details = response.data;
            scope.dateTerm = response.data.data;
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
          scope.currentTermEndDate = moment(data.end ).format('YYYY-MM-DD');
          await hrSettings.fetchMemberWallet( scope.currentTermStartDate, scope.currentTermEndDate, 'wellness' )
            .then(function(response){
							// console.log(response);
							scope.wellnessActivated = response.data.data.status;
							scope.wellnessWalletData = response.data.data;
							scope.wellnessWalletData.roll_over = scope.wellnessWalletData.roll_over.toString();
							if(scope.account_details.account_type == 'out_of_pocket'){
								scope.wellnessWalletData.benefits_start = '';
								scope.wellnessWalletData.benefits_end = '';
							}else{
								scope.wellnessWalletData.benefits_start = moment(scope.wellnessWalletData.benefits_start).format('DD/MM/YYYY');
								scope.wellnessWalletData.benefits_end = moment(scope.wellnessWalletData.benefits_end).format('DD/MM/YYYY');
							}
            })
				}
				// start and end date for activating wellness
				scope.getMedicalWalletData = async function ( data ) {
					scope.currentTermStartDate = moment(data.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment(data.end ).format('YYYY-MM-DD');
          await hrSettings.fetchMemberWallet( scope.currentTermStartDate, scope.currentTermEndDate, 'medical' )
            .then(function(response){
							scope.medicalWalletData = response.data.data;
							scope.medicalWalletData.benefits_start = moment(scope.medicalWalletData.benefits_start).format('DD/MM/YYYY');
							scope.medicalWalletData.benefits_end = moment(scope.medicalWalletData.benefits_end).format('DD/MM/YYYY');
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
						scope.wellnessWalletData.non_panel_reimbursement = opt;
						console.log(scope.wellnessWalletData.non_panel_reimbursement);
					}
					if ( type == 'non-panel-submission' ) {
						scope.wellnessWalletData.non_panel_submission = opt;
					}

					scope._saveWallet_();
				}

				scope._nonPanelPaymentMethod_ = function ( opt ) {
					if (scope.wellnessWalletData.non_panel_payment_method != opt) {
						scope._saveButtonTrigger_();
					}
        	scope.wellnessWalletData.non_panel_payment_method = opt;
				}

				scope.getMemberActivity = async function ( data ) {
					data.type = 'wellness';
					await hrSettings.fetchMemberWalletActivitiesData( data.customer_id, data.type, scope.page, scope.per_page )
            .then(function(response){
							// console.log(response);
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

				scope._saveWallet_ = function () {

					let data = {
						id: scope.wellnessWalletData.id,
						customer_id: scope.wellnessWalletData.customer_id,
						type:'wellness',
						start: scope.wellnessWalletData.benefits_start,
						end: scope.wellnessWalletData.benefits_end,
						active_non_panel_claim:scope.wellnessWalletData.non_panel_submission,
						reimbursement:scope.wellnessWalletData.non_panel_reimbursement,
						payment_method_panel:scope.wellnessWalletData.panel_payment_method,
						payment_method_non_panel:scope.wellnessWalletData.non_panel_payment_method,
					}

					hrSettings.updateMemberWallet( data )
					.then(function(response){
						console.log(response);
						scope.isSaveEnable = false;
						scope.hideLoading();
					})
				}

				scope.activeWellnessWallet = function ( type ) {
					if ( type == 'active-wellness-wallet' ) {
						scope.isWellnessWalletShow = true;
					}
					if ( type == 'cancel' ) {
						scope.isWellnessWalletShow = false;
					}

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

					scope.getMedicalWalletData(scope.defaultDateTerms);
				}

				scope.activeWellnessMethod = function ( opt ) {
					if (scope.reim_payment_method != opt) {
						scope._saveButtonTrigger_();
						// console.log('save button trigger');
					}
					scope.reim_payment_method = opt;
				}

				scope.confirmWellnessWallet = function () {
					let data = { 
						benefits_start: moment(scope.medicalWalletData.benefits_start).format('YYYY-MM-DD'),
						benefits_end: moment(scope.medicalWalletData.benefits_end).format('YYYY-MM-DD'),
						non_panel_payment_method: scope.reim_payment_method,
						non_panel_reimbursement: scope.reimbursementStatus,
					}

					console.log(data);
					scope.showLoading();
					hrSettings.updateWellnessWallet( data )
					.then(function(response){
						console.log(response);
						$('#activate-member-modal').modal('hide');
						scope.hideLoading();
					})
				}

				scope._saveButtonTrigger_ = function () {
					scope.isSaveEnable = true;
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
				}

				scope.hideLoading = function( ){
					setTimeout(function() {
						$(".circle-loader").hide();
						loading_trap = false;
					},10)
				}

				scope.onLoad = async function () {
					scope.showLoading();
					await scope.getDateTerms();
					await scope.getMemberWalletData(scope.defaultDateTerms);
					await scope.getMemberActivity(scope.defaultDateTerms);
					await scope.getMedicalWalletData(scope.defaultDateTerms);
					await scope.getStatus();
					scope.hideLoading();
				}

				scope.onLoad();
				
			}
		}
	}
]);
