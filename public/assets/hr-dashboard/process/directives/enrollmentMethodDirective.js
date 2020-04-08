app.directive('enrollmentMethodDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("enrollmentMethodDirective Runnning !");

				scope.optionSelected = null;
				scope.no_select = false;

				scope.submitMethod = function( ){
					if( scope.optionSelected == 'excel' ){
						localStorage.setItem('method','excel');
						$state.go('excel-enrollment.download-template');
						scope.no_select = false;
					}else if( scope.optionSelected == 'web_input' ){
						localStorage.setItem('method','input');
						$state.go('web-input');
						scope.no_select = false;
					}else{
						scope.no_select = true;
					}
				}

				scope.selectEnrollmentMethod	=	function(opt){
					scope.optionSelected = opt;
				}

        scope.onLoad = function( ){

        }

        scope.onLoad();
			}
		}
	}
]);
