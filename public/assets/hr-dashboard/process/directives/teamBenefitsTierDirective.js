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
				scope.addTierIsShow = false;
				scope.tier_data = {
					gp_cap_status : false,
				};
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

				scope.toggleGPcapStatus = function(opt){
					scope.tier_data.gp_cap_status = opt;
				}

				scope.toggleAddTier = function( ){
					if( scope.addTierIsShow == false ){
						scope.addTierIsShow = true;
						scope.tier_data = {
							gp_cap_status : false,
						};
						$('.account-tier-container').hide();
						$('.benefits-tier-btn-content').hide();
						$('.account-tier-add-container').fadeIn();
					}else{
						scope.addTierIsShow = false;
						$('.account-tier-add-container').hide();
						if( scope.tier_arr.length > 0 ){
							$('.account-tier-container').fadeIn();
						}else{
							$('.benefits-tier-btn-content').fadeIn();
						}
					}
				}

				scope.saveTierData = function( data ){
					if( data.medical_annual_cap == 0 || data.wellness_annual_cap == 0 || data.gp_cap_per_visit == 0 || data.member_head_count == 0 || data.dependent_head_count == 0 ){
						swal( 'Error!', "Input values should be 1 or more", 'error' );
						return false;
					}
					if( data.gp_cap_status == true && (!data.gp_cap_per_visit || data.gp_cap_per_visit == 0) ){
						swal( 'Error!', "Input values should be 1 or more", 'error' );
						return false;
					}

					scope.showLoading();
					if( scope.tier_data.plan_tier_id ){
						data.plan_tier_id = scope.tier_data.plan_tier_id;
						dependentsSettings.updateTier( data )
							.then(function(response){
								console.log( response );
								scope.hideLoading();
								if( response.data.status ){
									swal( 'Success!', response.data.message, 'success' );
									$('.account-tier-edit-container').hide();
									scope.editTierIsShow = false;
									scope.getTiers();
								}else{
									swal( 'Error!', response.data.message, 'error' );
								}
							});
					}else{
						dependentsSettings.addBenefitsTier( data )
							.then(function(response){
								console.log( response );
								scope.hideLoading();
								if( response.data.status ){
									swal( 'Success!', response.data.message, 'success' );
									$('.account-tier-add-container').hide();
									scope.addTierIsShow = false;
									scope.getTiers();
								}else{
									swal( 'Error!', response.data.message, 'error' );
								}
							});
					}
				}

				scope.removeTier = function(){
					swal({
            title: "Confirm",
            text: "are you sure you want to delete this Tier?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ff6864",
            confirmButtonText: "Remove",
            cancelButtonText: "No",
            closeOnConfirm: true,
            customClass: "removeEmp"
          },
          function(isConfirm){
            if(isConfirm){
            	var data = {
            		plan_tier_id : scope.tier_data.plan_tier_id
            	}
              dependentsSettings.deleteTier( data )
								.then(function(response){
										$('.account-tier-edit-container').hide();
										scope.editTierIsShow = false;
										scope.getTiers();
									});
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
								if( scope.tier_arr.length > 0 ){
									$('.account-tier-container').fadeIn();
								}else{
									$('.benefits-tier-btn-content').fadeIn();
								}
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
