app.directive('memberMedicalWalletDirective', [
	'$state',
	'$location',
	'hrSettings',
	function directive($state,$location,hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("member medical wallet directive Runnning !");
				console.log($location);

        scope.giroStatus = false;

        scope._accountSelector_ = function ( opt ) {
        	scope.giroStatus = opt;
				}
				
				scope.getDateTerms = function () {
          hrSettings.fetchDateTerms()
          .then(function(response){
            scope.dateTerm = response.data.data;
            console.log(scope.dateTerm);

            scope.currentTerm = scope.dateTerm.slice(-1).pop();
            console.log(scope.currentTerm );

            scope.getMemberWalletData();
          })
        }

				scope.getMemberWalletData = function (  ) {
					scope.currentTermStartDate = moment(scope.currentTerm.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment( scope.currentTerm.end ).format('YYYY-MM-DD');
          
          hrSettings.fetchMemberWallet( scope.currentTermStartDate, scope.currentTermEndDate, 'medical' )
            .then(function(response){
							scope.medicalWalletData = response.data.data;
							scope.medicalWalletData.roll_over = scope.medicalWalletData.roll_over.toString();
							scope.medicalWalletData.benefits_start = moment(scope.medicalWalletData.benefits_start).format('DD/MM/YYYY');
							scope.medicalWalletData.benefits_end = moment(scope.medicalWalletData.benefits_end).format('DD/MM/YYYY');
							console.log(scope.medicalWalletData);
							
							scope.hideLoading();
            })
				}

				scope.toggleFunds = function ( type ) {
					if ( type == 'medical-balance' ) {
						$('.credits-tooltip-container.medical-balance').toggle();
						$('.credits-tooltip-container.medical-funds').hide();
						$('.credits-tooltip-container.company-funds').hide();
					}
					if ( type == 'medical-funds' ) {
						$('.credits-tooltip-container.medical-funds').toggle();
						$('.credits-tooltip-container.medical-balance').hide();
						$('.credits-tooltip-container.company-funds').hide();
					}
					if ( type == 'company-funds' ) {
						$('.credits-tooltip-container.company-funds').toggle();
						$('.credits-tooltip-container.medical-balance').hide();
						$('.credits-tooltip-container.medical-funds').hide();
					}
				}

				scope.showLoading = function () {
          $(".circle-loader").fadeIn();
          loading_trap = true;
        };

        scope.hideLoading = function () {
          setTimeout(function () {
            $(".circle-loader").fadeOut();
            loading_trap = false;
          }, 10);
        };
			

				scope.onLoad = function () {
					scope.showLoading();
					scope.getDateTerms();
				}

				scope.onLoad();
				
			}
		}
	}
]);
