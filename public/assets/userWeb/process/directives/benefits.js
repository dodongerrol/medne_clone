app.directive('benefitsDirective', [
	"$http",
	"benefitsModule",
	"serverUrl",
	function directive( $http, benefitsModule, serverUrl ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				// console.log("benefits Directive Runnning !");
				scope.category_list = {};
				scope.user = {};
				$( ".sidebar ul li" ).removeClass('active');
				$( ".sidebar ul li#home_li" ).addClass('active');

				scope.userInfo = function userInfo( ) {
					$http.get(serverUrl.url + 'auth/userprofile')
					.success(function(response){
						scope.user = response;
						scope.getUsersLocation( );
						scope.getCategoryList( );
					})
					.error(function(err){
						
					});
				};

				scope.getCategoryList = function( ) {
					benefitsModule.categoryList( )
					.success(function(response){
						console.log(response);
						scope.category_list = response.data.clinic_types;
						$('#loading-container').fadeOut(500);
						$('.benefit-container').fadeIn(500);
					})
					.error(function(err){
						// console.log(err);
					});
				};

				function showPosition(position) {
					scope.user.lat = position.coords.latitude;
					scope.user.lng = position.coords.longitude; 
				}

				scope.getUsersLocation = function( ) {
					if (navigator.geolocation) {
				        navigator.geolocation.getCurrentPosition(function(position){
				        	scope.user.lat = position.coords.latitude;
							scope.user.lng = position.coords.longitude; 
				        }, function(error){
				        	console.log(error);
				        }, {maximumAge:600000, timeout:5000, enableHighAccuracy: true});
				    } else {
        				$.alert({
						    title: '',
						    content: 'Geolocation is not supported by this browser.',
						    columnClass: 'small'
						});
        			}
				};

				scope.onLoad = function(){
					$('.modal').modal('hide');
				}

				scope.onLoad();
				scope.userInfo( );
			}
		}
	}
]);
