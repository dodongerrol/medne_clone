app.directive('tAndCDirective', [
	'$state',
	'activationSettings',
	'$location',
	function directive($state,activationSettings, $location) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("t-and-c-directive Runnning !");
// <<<<<<< HEAD

//         scope.inputType = 'password';

//         scope.togglePassword = function(){
//           scope.inputType = scope.inputType == 'password' ? 'text' : 'password';
//         }
// =======
				scope.global_agreementSelector = false;
				scope.getToken = {};
				scope.scrollBottom = function () {
					let myDiv = document.getElementById("privacy-policy-container");
					// let clientHeight = document.getElementById("privacy-policy-container").clientHeight;

					myDiv.scrollTop = myDiv.scrollHeight;        	
				}
				scope.validateToken = function () {
					let token = localStorage.getItem('activation_token');
					activationSettings.validateToken( token )
					.then(function(response){
						console.log('response', response);
						scope.getToken = response.data.data;
					});
				}

				jQuery( function($) {
							$('#privacy-policy-container').bind('scroll', function() {
						if($(this).scrollTop() + $(this).innerHeight()>=$(this)[0].scrollHeight) {
						$('.btn-scroll').addClass('disable');
						} else {
							$('.btn-scroll').removeClass('disable');
						}
					})
	  			}
				);

				scope.enableAgreement = function ( opt ) {
					// opt = (opt == 'true');
					console.log(opt);

					scope.global_agreementSelector = opt;
				}
				scope.proceedPrivacy = function () {
					console.log('sulod');
					activationSettings.updateAgreeStatus(scope.getToken.hr_dashboard_id)
					.then(function(response){
						console.log(response);
						if(response.status) {
							$state.go('company-create-password');
						} else {
							alert(response.data.message);
						}
					});
				}

				scope.onLoad = function () {
					scope.validateToken();
					scope.scrollBottom();
					document.getElementById('privacy-policy-container').scrollTop -= 1000;
				}

				scope.onLoad();
       
				
			}
		}
	}
]);
