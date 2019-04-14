app.directive('ecommerceDirective', [
	"$http",
	"serverUrl",
	function directive( $http, serverUrl ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				// console.log("ecommerce Directive Runnning !");

				scope.oneHover = function(){
					$("#one").fadeIn();
					$("#two").hide();
					$("#three").hide();

					$(".subcat-wrapper .subcat a").css({'background': '#CDD6DA' , 'color': '#000'});
					$("#oneHover a").css({'background': '#F36F36', 'color': '#FFF'});
				}

				scope.twoHover = function(){
					$("#one").hide();
					$("#two").fadeIn();
					$("#three").hide();

					$(".subcat-wrapper .subcat a").css({'background': '#CDD6DA' , 'color': '#000'});
					$("#twoHover a").css({'background': '#596D78', 'color': '#FFF'});
				}

				scope.threeHover = function(){
					$("#one").hide();
					$("#two").hide();
					$("#three").fadeIn();

					$(".subcat-wrapper .subcat a").css({'background': '#CDD6DA' , 'color': '#000'});
					$("#threeHover a").css({'background': '#596D78', 'color': '#FFF'});
				}

				scope.onLoad = function(){
					$("#one").fadeIn();
					$("#two").hide();
					$("#three").hide();

					$(".subcat-wrapper .subcat a").css({'background': '#CDD6DA'});
					$("#oneHover a").css({'background': '#F36F36', 'color': '#FFF'});

					$('.modal').modal('hide');
				}

				scope.onLoad();
			}
		}
	}
]);
