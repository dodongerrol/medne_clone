var app = angular.module('app', []);

app.factory('serverUrl',[
    function factory(){
      return {
        url: window.location.origin + '/'
      }
    }
]);

app.directive('payment', [
	"$http",
	"serverUrl",
	function directive($http, serverUrl) {
		return {
			restrict: "A",
			scope: true,
			link: function link(scope, element, attributeSet) {
				console.log('running');
				scope.payment_data = {}
				scope.products = {};

				scope.getProducts = function( ) {
					$http.get(serverUrl.url + 'get/products')
					.success(function(response){
						scope.products = response;
						console.log(response);
					});
				}

				scope.change = function( ) {
					// $("#product_id").val(scope.payment_data.prod)
					angular.forEach(scope.products, function(value, key){
						if(scope.payment_data.id == value.product_id) {
							$("#amount").val(value.product_price);
							scope.payment_data.amount = value.product_price;
						}
					})
				}

				scope.getProducts();
			}
		}
	}
]);