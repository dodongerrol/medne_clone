app.directive('refundListDirective', [
	'$state',
	'hrSettings',
	'$rootScope',
	function directive($state,hrSettings,$rootScope) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("refundListDirective Runnning !");

				scope.refundUsers = {};
				scope.job_list = {};
				scope.emp_active = 0;

				scope.$on( 'refundList', function( evt, data ){
			      scope.refundUsers = data.data;
			      angular.forEach( scope.refundUsers ,function(value,key){
			      	scope.refundUsers[key].user.fname = scope.refundUsers[key].user.Name.substring(0, scope.refundUsers[key].user.Name.lastIndexOf(" "));
							scope.refundUsers[key].user.lname = scope.refundUsers[key].user.Name.substring(scope.refundUsers[key].user.Name.lastIndexOf(" ") + 1);
							scope.refundUsers[key].user.Email = scope.refundUsers[key].user.Email.substring(scope.refundUsers[key].user.Email.lastIndexOf(" ") + 1);
			      });
			    });

				scope.nextEmp = function( ){
		    	scope.emp_active++;
		    }

		    scope.prevEmp = function( ){
		    	scope.emp_active--;
		    }

		    scope.getMethod = function( ){
					hrSettings.getMethodType()
          	.then(function(response){
          		scope.payment = response.data.payment_method;
          		scope.plan = response.data.plan;
          		scope.customer = response.data.customer;

          	});
				}

				scope.getJobs = function( ){
					hrSettings.getJobTitle()
						.then(function(response){
							scope.job_list = response.data;
						});
				}

				scope.showGlobalModal = function( message ){
			    $( "#global_modal" ).modal('show');
			    $( "#global_message" ).text(message);
			  }

        scope.onLoad = function( ){
        	scope.getMethod();
        	scope.getJobs();
        };

        scope.onLoad();
			}
		}
	}
]);
