app.directive('excelEnrollmentDirective', [
	'$state',
	'$rootScope',
	'hrSettings',
	'dashboardFactory',
	function directive($state, $rootScope, hrSettings, dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("excelEnrollmentDirective Runnning !");
        scope.download_step = 1;
        console.log($state);

        $rootScope.$on('$stateChangeStart', 
          function(event, toState, toParams, fromState, fromParams){ 
            // console.log(toState);
            scope.setStep(toState.name);
          })

        scope.setStep = function(name){
          if( name == 'excel-enrollment.download-template' ){
            scope.download_step = 1;
          }
          if( name == 'excel-enrollment.prepare' ){
            scope.download_step = 2;
          }
          if( name == 'excel-enrollment.upload' ){
            scope.download_step = 3;
          }
          if( name == 'excel-enrollment.web-preview' ){
            scope.download_step = 4;
          }
        }

        scope.onLoad = function( ){
          scope.setStep($state.current.name);
        }

        scope.onLoad();
			}
		}
	}
]);
