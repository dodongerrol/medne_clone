app.directive('memberWalletBenefitsCoverageDirective', [
	'$state',
	'$location',
	'$rootScope',
	'$state',
	function directive($state,$location,$rootScope,$state) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("member wallet benefits coverage directive Runnning !");
				console.log($location);

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

			}
		}
	}
]);
