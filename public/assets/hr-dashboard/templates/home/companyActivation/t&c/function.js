app.directive('tAndCDirective', [
	'$state',
	'activationSettings',
	function directive($state,activationSettings) {
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

        scope.scrollBottom = function () {
        	let myDiv = document.getElementById("privacy-policy-container");
        	// let clientHeight = document.getElementById("privacy-policy-container").clientHeight;

        	myDiv.scrollTop = myDiv.scrollHeight;        	
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
					activationSettings.updateAgreeStatus()
          	.then(function(response){
          		console.log(response);
          		$state.go('company-create-password');
          	});
				}

				scope.onLoad = function () {
					scope.scrollBottom();
					document.getElementById('privacy-policy-container').scrollTop -= 1000;
				}

				scope.onLoad();
       
				
			}
		}
	}
]);
