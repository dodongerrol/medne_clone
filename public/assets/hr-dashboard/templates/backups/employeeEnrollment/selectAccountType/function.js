app.directive('selectAccountTypeDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("selectAccountTypeDirective Runnning !");

				scope.selectedAccountType	=	null;

				scope.setAccountType	=	function(opt){
					console.log(opt);
					scope.selectedAccountType = opt;
				}

				scope.backPage	=	function(){
					$state.go('benefits-dashboard');
				}

				scope.nextPage	=	function(){
					$state.go('enrollment.input-table');
				}

        scope.onLoad = function( ){
        		        
        }

        scope.onLoad();
			}
		}
	}
]);
