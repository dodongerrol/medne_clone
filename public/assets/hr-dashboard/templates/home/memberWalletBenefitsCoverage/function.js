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
				}
				
				scope.formatDate = function (date) {
          return moment(new Date(date)).format("DD/MM/YYYY");
        };

        scope.formatTableDate = function (date) {
          return moment(new Date(date)).format("DD MMMM YYYY");
				};

			
			}
		}
	}
]);
