app.directive('docsData', [
	'$http',
	'serverUrl',
	'hrSettings',
	function directive($http, serverUrl, hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("docsData Runnning !");
				scope.customer = "";
				scope.options = {};
				scope.statementHide = true;

				scope.companyAccountType = function () {
					scope.account_type = localStorage.getItem('company_account_type');
					console.log(scope.account_type);

					if(scope.account_type === 'enterprise_plan') {
						$('.statement-hide').hide();
						scope.statementHide = false;
					}
				}

        scope.onLoad = function( ){
        	scope.companyAccountType( );

        	hrSettings.getSession( )
        	.then(function(response){
				scope.options.accessibility = response.data.accessibility;
        	});
      		$http.get(serverUrl.url + '/get/active_plan_hr')
      		.success(function(response){
      			console.log(response);
      			scope.customer = window.location.origin + '/get/certificate/' + response;
      			console.log(scope.customer);
      		});
        }
    		scope.onLoad();
			}
		}
	}
]);
