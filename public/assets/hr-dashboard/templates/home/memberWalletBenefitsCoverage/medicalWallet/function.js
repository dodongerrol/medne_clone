app.directive('memberMedicalWalletDirective', [
	'$state',
	'$location',
	function directive($state,$location) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("member medical wallet directive Runnning !");
				console.log($location);

        scope.giroStatus = false;

        scope._accountSelector_ = function ( opt ) {
        	scope.giroStatus = opt;
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
				
			}
		}
	}
]);
