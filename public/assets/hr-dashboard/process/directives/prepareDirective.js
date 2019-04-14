app.directive('prepareDirective', [
	'$state',
	'hrSettings',
	function directive($state,hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("prepareDirective Runnning !");

				scope.isFormat = false;
				scope.isNameGood = false;
				scope.isDateFormat = false;
				scope.isMobileGood = false;
				scope.isDropDown = false;

				var loading_trap = false;

        scope.toggleLoading = function( ){
					if ( loading_trap == false ) {
						$( ".circle-loader" ).fadeIn();	
						loading_trap = true;
					}else{
						setTimeout(function() {
							$( ".circle-loader" ).fadeOut();
							loading_trap = false;
						},1000)
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
					},1000)
				}

				scope.showGlobalModal = function( message ){
			    $( "#global_modal" ).modal('show');
			    $( "#global_message" ).text(message);
			  }

        scope.onLoad = function( ) {
        	scope.toggleLoading();

        	setTimeout(function() {
        		scope.toggleLoading();
        	}, 500);

        	$('body').scrollTop(0);
        }

        scope.onLoad();
			}
		}
	}
]);
