app.directive('memberWalletBenefitsCoverageDirective', [
	'$state',
	'$location',
	'$rootScope',
	'$state',
	'hrSettings',
	'$http',
	'serverUrl',
	function directive($state,$location,$rootScope,$state,hrSettings, $http, serverUrl) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("member wallet benefits coverage directive Runnning !");
				console.log($location);

				scope.isBasicPlan = false;
        scope.isEnterprisePlan = false;
        scope.isOutofPlan = false;

        scope.memberWalletSelector = function ( opt ) {
					scope.memberWalletBeneftsSelectorValue = opt;
					
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
				
				scope.formatDate = function (date) {
          return moment(new Date(date)).format("DD/MM/YYYY");
        };

        scope.formatTableDate = function (date) {
          return moment(new Date(date)).format("DD MMMM YYYY");
				};

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

				scope.getStatus = async function () {
					await hrSettings.getPlanStatus( )
            .then(function(response){
							scope.planStatusData = response.data;
							console.log(scope.planStatusData);

							// scope._disabledStatus_();
						})
						
				}

				scope._disabledStatus_ = async function () {
					// basic plan
					console.log(scope.planStatusData.account_type);
					if ( scope.planStatusData.account_type == 'lite_plan' ) {
						scope.isBasicPlan = true;
					}
					if ( scope.planStatusData.account_type == 'enterprise_plan' ) {
						scope.isEnterprisePlan = true;
						if (scope.wellnessWalletData.non_panel_reimbursement) {
              scope.isBasicPlan = true;
            }
					}
					if ( scope.planStatusData.account_type == 'out_of_pocket' || scope.planStatusData.account_type == 'out_pocket' ) {
						scope.isBasicPlan = true;
						scope.isOutofPlan = true;
					}
				}

				scope.companyDateTerms = async function () {
          await $http.get(serverUrl.url+ `/hr/get_company_date_terms`)
            .success(async function (response) {
              // console.log(response.data);
              if(response.status){
                scope.account_details = response;
                scope.dateTerms = response.data;
                let termLength = scope.dateTerms.length;
                await scope.dateTerms.map( function(value,index) {
                  if (index == 0) {
                    value.end = value.end ? value.end : moment().format('YYYY-MM-DD');
                    value.term = `Current term (${moment(value.start).format('DD/MM/YYYY')} - ${moment(value.end).format('DD/MM/YYYY')})`;
                    value.index = index;
                    scope.defaultDateTerms = value;
                    scope.selectedTerm = value;
                    scope.dateTermIndex = value.index;
                    
                  } else {
                    value.term = `Last term (${moment(value.start).format('DD/MM/YYYY')} - ${moment(value.end).format('DD/MM/YYYY')})`;
                  }
                });
              }
            });
        }

				scope.getMemberWalletData = async function ( data, type ) {
					scope.currentTermStartDate = moment(data.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment(data.end).format('YYYY-MM-DD');
          await hrSettings.fetchMemberWallet( scope.currentTermStartDate, scope.currentTermEndDate, type )
            .then(async function(response){
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
							await scope._disabledStatus_();
            })
				}

				scope.onLoad = async function () {
					await scope.getStatus();
					await scope.companyDateTerms();
					await scope.getMemberWalletData(scope.defaultDateTerms, 'medical');
					await scope.getMemberWalletData(scope.defaultDateTerms, 'wellness');
				}

				scope.onLoad();

				

			}
		}
	}
]);
