app.directive('uploadExcelDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("uploadExcelDirective Runnning !");

				scope.disableBtn = true;
				scope.isInvalid = false;
				scope.isValid = false;
				scope.message = "";


				scope.runUpload = function( file ) {
					// console.log(file);
					var data = {
						file : file,
						plan_start : moment().format('YYYY-MM-DD'),
						// duration : scope.progress.active_plans[0].duration
					}
					$('.upload-load').show();
					$('.upload-box').hide();

					if( dashboardFactory.getHeadCountStatus() == true ){

						hrSettings.newPurchaseUploadExcel( data )
		        		.then(function(response){
			        		if( response.data.status == true ){
			        			// dashboardFactory.setActivePlanID( response.data.customer_active_plan_id );
			        			scope.isInvalid = false;
								scope.isValid = true;
			        			scope.disableBtn = false;
			        			$("#disableBtn").attr('disabled',false);
			        		}

			        		if( response.data.status == false ){
			        			scope.isInvalid = true;
								scope.isValid = false;
			        			scope.disableBtn = true;
			        			$("#disableBtn").attr('disabled',true);
			        		}
		        			
		        			$('.upload-load').hide();
							$('.upload-box').show();
			        		scope.message = response.data.message;
			        	});
		        	}else{
			        	hrSettings.uploadExcel( data )
			        	.then(function(response){
			        		if( response.data.status == true ){
			        			scope.isInvalid = false;
										scope.isValid = true;
			        			scope.disableBtn = false;
			        			$("#disableBtn").attr('disabled',false);
			        		}

			        		if( response.data.status == false ){
			        			scope.isInvalid = true;
										scope.isValid = false;
			        			scope.disableBtn = true;
			        			$("#disableBtn").attr('disabled',true);
			        		}
			        		
		        			$('.upload-load').hide();
									$('.upload-box').show();
			        		scope.message = response.data.message;
			        	});
		        	}

				}

				scope.goToPreview = function( ) {
					$state.go('web-preview');
				}

				scope.getProgress = function( ) {
					hrSettings.getEnrollmentProgress()
						.then(function(response){
							scope.progress = response.data.data;
						});
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

        scope.onLoad = function( ){
        	scope.getProgress();
        	$('body').scrollTop(0);

        	scope.toggleLoading();

        	setTimeout(function() {
        		scope.toggleLoading();
        	}, 500);

        	console.log( $("#upload-here").val() );
        }

        scope.onLoad();
			}
		}
	}
]);
