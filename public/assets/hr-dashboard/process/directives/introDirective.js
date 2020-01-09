app.directive('introDirective', [
	'$state',
	'hrSettings',
	function directive($state,hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("introDirective Runnning !");

				scope.payment = {};
				scope.plan = {};

				scope.isPrivacyChecked = false;
				scope.privacyError = false;
				scope.intro_data = {};

				scope.updateAgree = function(  ){
					hrSettings.updateAgreeStatus()
          	.then(function(response){
          		$state.go('benefits-dashboard');
          	});
				}

				scope.proceedPrivacy = function( ){

					if( scope.isPrivacyChecked == true ){
						scope.privacyError = false;
						scope.updateAgree();
					}else{
						scope.privacyError = true;
					}
				}

				scope.navigateChoice = function( ) {
					hrSettings.updateShowDone();
					if(scope.intro_data.agree_status == 'true') {
						$state.go('benefits-dashboard');
					} else {
						$state.go('privacy-policy');
					}
				}
				
				scope.getMethod = function( ) {
					hrSettings.getMethodType()
          	.then(function(response){
          		scope.intro_data = response.data.data;
          		console.log(scope.intro_data);
          		scope.hideIntroLoader();
          		// scope.payment = response.data.payment_method;
          		// scope.plan = response.data.plan;
          		// scope.customer = response.data.customer;

          		if( response.data.data.checks == true){
          			$state.go('benefits-dashboard');
          		}

          		setTimeout(function() {
          			$('.main-container').fadeIn();
          			$('.loader-container').hide();
          		}, 200);
          	});
				}

				scope.hideIntroLoader = function( ){
					setTimeout(function() {
						$( ".main-loader" ).fadeOut();
						introLoader_trap = false;
					}, 1000);
				}

				scope.checkCompanyBalance = function() {
					hrSettings.getCheckCredits();
				}

				scope.showGlobalModal = function( message ){
			    $( "#global_modal" ).modal('show');
			    $( "#global_message" ).text(message);
			  }

				scope.onLoad = function( ) {
          scope.getMethod();
        }

        // scope.checkCompanyBalance();
      	// scope.onLoad();
			}
		}
	}
]);
