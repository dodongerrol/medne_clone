app.directive('prepareDirective', [
	'$state',
	'hrSettings',
	function directive($state,hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("prepareDirective Runnning !");

				scope.isWithDependents = localStorage.getItem('enrollmentIsWithDependents') == 'true' ? true : false;
				console.log('scope.isWithDependents', scope.isWithDependents);
				scope.reviewExcelData = {
					format: false,
					name: false,
					dob: false,
					email: false,
					postcode: false,
					relationship: false,
					plan_start: false,
				}

				scope.backBtn = function(){
					$state.go('excel-enrollment.download-template');
				}

				scope.nextBtn	=	function(){
					if (scope.reviewExcelData.format && scope.reviewExcelData.dob && scope.reviewExcelData.email && 
						scope.reviewExcelData.postcode && scope.reviewExcelData.plan_start) {
						if (scope.isWithDependents == true) {
							if (scope.reviewExcelData.relationship) {
								$state.go('excel-enrollment.upload');
							} else {
								swal('Error!', 'please review your downloaded file and check the boxes.', 'error');
							}
						} else {
							$state.go('excel-enrollment.upload');
						}
					} else {
						swal('Error!', 'please review your downloaded file and check the boxes.', 'error');
					}
				}

        scope.onLoad = function( ) {
        	
        }

        scope.onLoad();
			}
		}
	}
]);
