app.directive('downloadTemplateDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("downloadTemplateDirective Runnning !");

        scope.downloadTemplateOption = null;
        scope.spending_account_status = {};



        scope.backBtn = function(){
          $state.go('enrollment-method');
        }

        scope.downloadExcelTemplate = function(opt){
          scope.downloadTemplateOption = opt;
          localStorage.setItem('enrollmentIsWithDependents', opt == 1 ? false : true );
        }

        scope.downloadTemplate = function(){
          scope.showLoading();
          var med_spending_acct = scope.spending_account_status.medical;
					var well_spending_acct = scope.spending_account_status.wellness;
					// var med_spending_acct = false;
					// var well_spending_acct = true;
					var medical_entitlement = localStorage.getItem('hasMedicalEntitlementBalance');
					var wellness_entitlement = localStorage.getItem('hasWellnessEntitlementBalance');

					if (scope.downloadTemplateOption != null) {
						if (scope.downloadTemplateOption == 1) {
							// window.location.href = '/excel/Employee Enrollment Listing.xlsx';
							console.log('w/out dependents', med_spending_acct, well_spending_acct, medical_entitlement, wellness_entitlement);

							if (med_spending_acct == true && medical_entitlement == 'false' && well_spending_acct == false && wellness_entitlement == 'false') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employee+Enrollment+Listing+-+With+Medical+Spending+Account+-+No+Medical+Entitlement+Balance+-+No+Wellness+Spending+Account.xlsx';
								console.log('scenario 2');
							} else if (med_spending_acct == true && medical_entitlement == 'true' && well_spending_acct == false && wellness_entitlement == 'false') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employee+Enrollment+Listing+-+With+Medical+Spending+Account+-+With+Medical+Entitlement+Balance+-+No+Wellness+Spending+Account.xlsx';
								console.log('scenario 3');
							} else if (med_spending_acct == true && medical_entitlement == 'false' && well_spending_acct == true && wellness_entitlement == 'false') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employee+Enrollment+Listing+-+With+Medical+Spending+Account+-+No+Medical+Entitlement+Balance+-+With+Wellness+Spending+Account+-+No+Wellness+Entitlement+Balance.xlsx';
								console.log('scenario 4');
							} else if (med_spending_acct == true && medical_entitlement == 'true' && well_spending_acct == true && wellness_entitlement == 'true') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employee+Enrollment+Listing+-+With+Medical+Spending+Account+-+With+Medical+Entitlement+Balance+-+With+Wellness+Spending+Account+-+With+Wellness+Entitlement+Balance.xlsx';
								console.log('scenario 5');
							} else if (med_spending_acct == false && medical_entitlement == 'false' && well_spending_acct == true && wellness_entitlement == 'false') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employee+Enrollment+Listing+-+With+Wellness+Spending+Account+-+No+Wellness+Entitlement+Balance+-+No+Medical+Spending+Account.xlsx';
								console.log('scenario 6');
							} else if (med_spending_acct == false && medical_entitlement == 'false' && well_spending_acct == true && wellness_entitlement == 'true') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employee+Enrollment+Listing+-+With+Wellness+Spending+Account+-+With+Wellness+Entitlement+Balance+-+No+Medical+Spending+Account.xlsx';
								console.log('scenario 7');
							} else if (med_spending_acct == true && medical_entitlement == 'false' && well_spending_acct == true && wellness_entitlement == 'true') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employee+Enrollment+Listing+-+With+Medical+Spending+Account+-+No+Medical+Entitlement+Balance+-+With+Wellness+Spending+Account+-+With+Wellness+Entitlement+Balance.xlsx';
								console.log('scenario 8 feedback 20');
							} else if (med_spending_acct == true && medical_entitlement == 'true' && well_spending_acct == true && wellness_entitlement == 'false') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employee+Enrollment+Listing+-+With+Medical+Spending+Account+-+With+Medical+Entitlement+Balance+-+With+Wellness+Spending+Account+-+No+Wellness+Entitlement+Balance.xlsx';
								console.log('scenario 9');
							} else if (med_spending_acct == false || well_spending_acct == false) {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employee+Enrollment+Listing+-+No+Spending.xlsx';
								console.log('scenario 1');
							}
						} else {
							console.log('w/dependents');
							// window.location.href = '/excel/Employees and Dependents.xlsx';
							// window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employees+and+Dependents.xlsx';

							if (med_spending_acct == true && medical_entitlement == 'false' && well_spending_acct == false && wellness_entitlement == 'false') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employees+and+Dependents+-+With+Medical+Spending+Account+-+No+Medical+Entitlement+Balance+-+No+Wellness+Spending+Account.xlsx';
								console.log('scenario 2');
							} else if (med_spending_acct == true && medical_entitlement == 'true' && well_spending_acct == false && wellness_entitlement == 'false') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employees+and+Dependents+-+With+Medical+Spending+Account+-+With+Medical+Entitlement+Balance+-+No+Wellness+Spending+Account.xlsx';
								console.log('scenario 3');
							} else if (med_spending_acct == true && medical_entitlement == 'false' && well_spending_acct == true && wellness_entitlement == 'false') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employees+and+Dependents+-+With+Medical+Spending+Account+-+No+Medical+Entitlement+Balance+-+With+Wellness+Spending+Account+-+No+Wellness+Entitlement+Balancexlsx.xlsx';
								console.log('scenario 4');
							} else if (med_spending_acct == true && medical_entitlement == 'true' && well_spending_acct == true && wellness_entitlement == 'true') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employees+and+Dependents+-+With+Medical+Spending+Account+-+With+Medical+Entitlement+Balance+-+With+Wellness+Spending+Account+-+With+Wellness+Entitlement+Balance.xlsx';
								console.log('scenario 5');
							} else if (med_spending_acct == false && medical_entitlement == 'false' && well_spending_acct == true && wellness_entitlement == 'false') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employees+and+Dependents+-+With+Wellness+Spending+Account+-+No+Wellness+Entitlement+Balance+-+No+Medical+Spending+Account.xlsx';
								console.log('scenario 6');
							} else if (med_spending_acct == false && medical_entitlement == 'false' && well_spending_acct == true && wellness_entitlement == 'true') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employees+and+Dependents+-+With+Wellness+Spending+Account+-+With+Wellness+Entitlement+Balance+-+No+Medical+Spending+Account.xlsx';
								console.log('scenario 7');
							} else if (med_spending_acct == true && medical_entitlement == 'false' && well_spending_acct == true && wellness_entitlement == 'true') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employees+and+Dependents+-+With+Medical+Spending+Account+-+No+Medical+Entitlement+Balance+-+With+Wellness+Spending+Account+-+With+Wellness+Entitlement+Balance.xlsx';
								console.log('scenario 8 feedback 20');
							} else if (med_spending_acct == true && medical_entitlement == 'true' && well_spending_acct == true && wellness_entitlement == 'false') {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employees+and+Dependents+-+With+Medical+Spending+Account+-+With+Medical+Entitlement+Balance+-+With+Wellness+Spending+Account+-+No+Wellness+Entitlement+Balance.xlsx';
								console.log('scenario 9');
							} else if (med_spending_acct == false || well_spending_acct == false) {
								window.location.href = 'https://mednefits.s3-ap-southeast-1.amazonaws.com/excel/Employees+and+Dependents+-+No+Spending.xlsx';
								console.log('scenario 1');
							}
            }
            scope.hideLoading();
					} else {
						swal('Error!', 'Please select an option for you template.', 'error');
					}
        }
        
        scope.nextBtn = function(){
          if (scope.downloadTemplateOption == null) {
            swal('Error!', 'Please select an option for you template.', 'error');
          }else{
            $state.go('excel-enrollment.prepare');
          }
        }

        scope.getSpendingAccountStatus = function () {
          scope.showLoading();
					hrSettings.getSpendingAccountStatus()
						.then(function (response) {
              console.log(response);
              scope.hideLoading();
							scope.spending_account_status = response.data;
						});
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
          scope.getSpendingAccountStatus();
        }

        scope.onLoad();
			}
		}
	}
]);
