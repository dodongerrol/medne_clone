app.directive('inputTableEnrollmentDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("inputTableEnrollmentDirective Runnning !");

				scope.backPage	=	function(){
					$state.go('enrollment.select-account-type');
				}

				scope.nextPage	=	function(){
					$state.go('enrollment.preview');
				}

				var lastScrollTop = 0;
				var lastScrollLeft = 0;
				$(".head-h-scroll-wrapper").scroll(function() {
					$(".body-h-scroll-wrapper").prop("scrollLeft", this.scrollLeft);

					var documentScrollLeft = $(this).scrollLeft();
					if (lastScrollLeft != documentScrollLeft) {
						console.log('leftright');
						$('.body-h-scroll-wrapper::-webkit-scrollbar').css(
							'background', '#000'
						);
					}
					lastScrollLeft = documentScrollLeft;
				});

				$(".body-h-scroll-wrapper").scroll(function() {
					$(".head-h-scroll-wrapper").prop("scrollLeft", this.scrollLeft);
					$(".body-h-fixed").prop("scrollTop", this.scrollTop);
				});

				$(".body-h-fixed").scroll(function() {
					$(".body-h-scroll-wrapper").prop("scrollTop", this.scrollTop);

					var st = $(this).scrollTop();
					if (st != lastScrollTop){
						console.log('updown');
					}
					lastScrollTop = st;
				});


        scope.onLoad = function( ){
        		        
        }

        scope.onLoad();
			}
		}
	}
]);
