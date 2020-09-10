app.directive('memberWalletBenefitsCoverageDirective', [
	'$state',
	'$location',
	'$rootScope',
	'$state',
	'hrSettings',
	function directive($state,$location,$rootScope,$state,hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("member wallet benefits coverage directive Runnning !");
				console.log($location);

				scope.isBasicPlan = true;
        scope.isEnterprisePlan = true;
        scope.isOutofPlan = true;

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

							scope._disabledStatus_();
						})
						
				}

				scope._disabledStatus_ = async function () {
					// basic plan
					console.log(scope.planStatusData.account_type);
					if ( scope.planStatusData.account_type == 'lite_plan' || scope.planStatusData.account_type == 'out_of_pocket' || scope.planStatusData.account_type == 'out_pocket' ) {
						scope.isBasicPlan = await false;
						console.log('basic plan ni')
					}
					if ( scope.planStatusData.account_type == 'enterprise_plan' ) {
						scope.isEnterprisePlan = await false;
						console.log('enterprise plan ni')
					}
					if ( scope.planStatusData.account_type == 'out_of_pocket' || scope.planStatusData.account_type == 'out_pocket' ) {
						scope.isOutofPlan = await false;
						console.log('out of pocket ni')
					}
				}

				scope.onLoad = async function () {
					await scope.getStatus();
				}

				scope.onLoad();

				

			}
		}
	}
]);
