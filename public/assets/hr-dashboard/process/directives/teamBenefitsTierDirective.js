app.directive('teamBenefitsTierDirective', [
	'$http',
	'serverUrl',
	'hrSettings',
	'dependentsSettings',
	function directive($http, serverUrl, hrSettings, dependentsSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("teamBenefitsTierDirective Runnning !");
				
				scope.editTierIsShow = false;
				scope.tier_data = {};
				scope.tier_arr = [];

				scope.toggleEditTier = function( data, index ){
					if( scope.editTierIsShow == false ){
						scope.editTierIsShow = true;
						scope.tier_data = data;
						scope.tier_data.index = index;
						$('.account-tier-container').hide();
						$('.account-tier-edit-container').fadeIn();
					}else{
						scope.editTierIsShow = false;
						$('.account-tier-container').fadeIn();
						$('.account-tier-edit-container').hide();
					}
				}

				scope.saveTierData = function( data ){
					if( data.medical_annual_cap == 0 || data.wellness_annual_cap == 0 || data.gp_cap_per_visit == 0 || data.member_head_count == 0 || data.dependent_head_count == 0 ){
						swal( 'Error!', "Input values should be 1 or more", 'error' );
						return false;
					}

					scope.showLoading();
					data.plan_tier_id = scope.tier_data.plan_tier_id;
					dependentsSettings.updateTier( data )
						.then(function(response){
							console.log( response );
							scope.hideLoading();
							if( response.data.status ){
								swal( 'Success!', response.data.message, 'success' );
								scope.getTiers();
								scope.toggleEditTier();
							}else{
								swal( 'Error!', response.data.message, 'error' );
							}
						});
				}

				scope.getTiers = function( ){
					scope.tier_arr = [];
					dependentsSettings.fetchBenefitsTier( )
						.then(function(response){
							console.log( response );
							if( response.data.status ){
								scope.tier_arr = response.data.data;
							}else{
								swal( 'Error!', response.data.message, 'error' );
							}
						});
				}

				scope.showLoading = function( ){
					$( ".circle-loader" ).fadeIn();	
					loading_trap = true;
				}

				scope.hideLoading = function( ){
					setTimeout(function() {
						$( ".circle-loader" ).fadeOut();
						loading_trap = false;
					},2000)
				}

        scope.onLoad = function( ){
        	scope.getTiers();
        }

    		scope.onLoad();
			}
		}
	}
]);
