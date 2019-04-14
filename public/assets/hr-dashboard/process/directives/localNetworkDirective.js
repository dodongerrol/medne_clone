app.directive('localNetworkDirective', [
	'$state',
	'hrSettings',
	function directive($state,hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("localNetworkDirective Runnning !");

				scope.active_localNetworkFilter = 'all';
				scope.active_District = 'CENTRAL';
				scope.options = {};

				scope.central_length = 1;
				scope.east_length = 1;
				scope.west_length = 1;
				scope.north_length = 1;
				scope.south_length = 1;

				scope.getLocalNetPart = function( id , index, net){
					$('.btn-local-network').removeClass('active');
					scope.active_District = net.local_network_name;

					hrSettings.getLocalNetworkPartners( id )
						.then(function(response){
							$(".btn-local-network:nth-child("+(index+1)+")").addClass('active');
							scope.local_partners = response.data;
						});
				}

				scope.getLocalNet = function( ){
					hrSettings.getLocalNetworks()
						.then(function(response){
							scope.local_networks = response.data;
							scope.getLocalNetPart(scope.local_networks[0].local_network_id, 0 , scope.local_networks[0]);
						});
				}
				scope.showGlobalModal = function( message ){
			    $( "#global_modal" ).modal('show');
			    $( "#global_message" ).text(message);
			  }

		        scope.onLoad = function( ){
		        	hrSettings.getSession( )
		        	.then(function(response){
						scope.options.accessibility = response.data.accessibility;
		        	});
		        	scope.getLocalNet();
		        }

		        scope.onLoad();
			}
		}
	}
]);
