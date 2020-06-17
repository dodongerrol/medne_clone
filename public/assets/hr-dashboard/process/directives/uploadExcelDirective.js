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

				scope.uploadFile = {};
				scope.uploadedFile = false;
				scope.isInvalid = false;
				scope.isValid = false;
				scope.isNextBtnDisabled = false;
				scope.message = 'Successfully Uploaded.';

				scope.runUpload = function (file) {
					var data = {
						file: file,
						plan_start: moment().format('YYYY-MM-DD'),
					}
					scope.showLoading();
					hrSettings.uploadExcel(data)
						.then(function (response) {
							// console.log( response );
							scope.hideLoading();
							if (response.data.status == true) {
								scope.uploadedFile = true;
								scope.isInvalid = false;
								scope.isValid = true;
								scope.isNextBtnDisabled = false;
								scope.message = 'Successfully Uploaded.';
								swal('Success!', 'uploaded.', 'success');
							} else {
								scope.uploadedFile = false;
								scope.isInvalid = true;
								scope.isValid = false;
								scope.isNextBtnDisabled = true;
								swal('Error!', response.data.message, 'error');
							}

						});
				}

				scope.backBtn = function(){
					$state.go('excel-enrollment.prepare');
				}

				scope.nextBtn =	function(){
					if(scope.uploadedFile == false){
						swal('Error!', 'please upload a file first.', 'error');
					}else{
						$state.go('excel-enrollment.web-preview');
					}
				}

				scope.showLoading = function () {
					$(".circle-loader").fadeIn();
				}

				scope.hideLoading = function () {
					setTimeout(function () {
						$(".circle-loader").fadeOut();
					},100)
				}
				
        scope.onLoad = function( ){
        	
        }

        scope.onLoad();
			}
		}
	}
]);
