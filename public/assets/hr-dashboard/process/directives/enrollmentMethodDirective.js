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

				scope.excelMethod = false;
				scope.inputMethod = false;
				scope.no_select = false;

				scope.submitMethod = function( ){
					if( scope.excelMethod == true ){
						localStorage.setItem('method','excel');
						$state.go('download-template');
						scope.no_select = false;
					}else if( scope.inputMethod == true ){
						localStorage.setItem('method','input');
						$state.go('web-input');
						scope.no_select = false;
					}else{
						scope.no_select = true;
					}
				}

				var loading_trap = false;

        scope.toggleLoading = function( ){
					if ( loading_trap == false ) {
						$( ".circle-loader" ).fadeIn();	
						loading_trap = true;
					}else{
						setTimeout(function() {
							$( ".circle-loader" ).fadeOut();
							loading_trap = false;
						},100)
					}
				}

				scope.showLoading = function( ){
					$( ".circle-loader" ).fadeIn();	
					loading_trap = true;
				}

				scope.hideLoading = function( ){
					setTimeout(function() {
						$( ".circle-loader" ).fadeOut();
						loading_trap = false;
					},100)
				}

				scope.showGlobalModal = function( message ){
			    $( "#global_modal" ).modal('show');
			    $( "#global_message" ).text(message);
			  }

        scope.onLoad = function( ){
        	scope.toggleLoading();

        	setTimeout(function() {
        		scope.toggleLoading();
        	}, 100);
        	console.log( dashboardFactory.getHeadCountStatus() );
        }

        scope.onLoad();
			}
		}
	}
]);
