app.directive('enrollmentOptionsDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	'$rootScope',
	function directive($state,hrSettings,dashboardFactory,$rootScope) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("enrollmentOptionsDirective Runnning !");

				scope.isOptionSelected = false;
				scope.isRequiredTiering = null;

				scope.selected_option = {
					medical_opt : null,
					wellness_opt : null,
				}


				scope.selectMedicalOpt = function( opt ){
					scope.selected_option.medical_opt = opt;
					$(".select-drop-box").hide();
				}
				scope.selectWellnessOpt = function( opt ){
					scope.selected_option.wellness_opt = opt;
					$(".select-drop-box").hide();
				}

				scope.backButton = function(){
					if( localStorage.getItem('fromEmpOverview') == true || localStorage.getItem('fromEmpOverview') == 'true' ){
						$state.go( 'employee-overview' );
					}else{
						$state.go( 'benefits-dashboard' );
					}
				}
				

				scope.toggleEnrollmentOptions = function( opt ){
					localStorage.setItem('enrollmentOptionTiering', opt);
					scope.isRequiredTiering = opt;
					scope.isOptionSelected = true;
				}

				scope.enrollmentNextBtn = function(){
					localStorage.setItem('hasMedicalEntitlementBalance', scope.selected_option.medical_opt);
					localStorage.setItem('hasWellnessEntitlementBalance', scope.selected_option.wellness_opt);
					// if( scope.isRequiredTiering == true ){
						$state.go( 'create-team-benefits-tiers' );
					// }else{
					// 	$state.go( 'enrollment-method' );
					// }
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

			  $(".select-value").click(function(e){
			  	$(".select-drop-box").hide();
			  	$(this).closest('.select-div').find(".select-drop-box").show();
			  });

			  $("body").click(function(e){
			    if ( $(e.target).parents(".select-div").length === 0) {
			      $(".select-drop-box").hide();
			    }
				});

        scope.onLoad = function( ){
        	scope.toggleLoading();

        	setTimeout(function() {
        		scope.toggleLoading();
        	}, 500);

        // 	scope.toggleEnrollmentOptions( false );
      		// $state.go( 'create-team-benefits-tiers' );
        }

        scope.onLoad();


			}
		}
	}
]);
