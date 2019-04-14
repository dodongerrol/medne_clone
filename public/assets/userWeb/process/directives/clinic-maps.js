app.directive('clinicMapsDirective', [
	"$http",
	"clinicsModule",
	"serverUrl",
	"$state",
	"$stateParams",
	"favouritesModule",
	function directive( $http, clinicsModule, serverUrl, $state, $stateParams ,favouritesModule) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				scope.clinic = {};
				
				scope.getClinicLocation = function( ) {
					if (navigator.geolocation) {
			            navigator.geolocation.getCurrentPosition(function(position) {
							clinicsModule.searchClinicLocation(position.coords.latitude, position.coords.longitude, $stateParams.type )
							.success(function(response){
								console.log(response);
								scope.clinic_list = response.data.clinics;
							})
							.error(function(err){
								console.log(err);
							});

						}, function() {
				            console.log("error getting current location");
				          });
			        }else{
			        	console.log("geolocation not supported");
			        }
				};

				var toggleview = 0;
				scope.toggleView = function(){
					if( toggleview == 0 ){
						$('#fav-container').hide();
						$('#map-container').fadeIn();
						$('#map-btn').hide();
						$('#list-btn').fadeIn();

						setTimeout(function(){
							scope.initializeMap();
						},500);
						toggleview = 1;
					}else{
						$('#fav-container').fadeIn();
						$('#map-container').hide();
						$('#map-btn').fadeIn();
						$('#list-btn').hide();
						toggleview = 0;
					}
				}

				scope.initializeMap = function () {
					var locations = scope.clinic_list;
					console.log(locations);
					var infowindow = new google.maps.InfoWindow();
					var marker, i;

			        var map = new google.maps.Map(document.getElementById('map'), {
			          // center: {lat: 1.295194, lng: 103.854468},
			          zoom: 12
			        });

			        if (navigator.geolocation) {
			          navigator.geolocation.getCurrentPosition(function(position) {

			        	// This is current location 
			            var pos = {
			              lat: position.coords.latitude,
          				  lng: position.coords.longitude
			            };
			            marker = new google.maps.Marker({
						  position: new google.maps.LatLng(pos.lat, pos.lng),
						  map: map,
						  animation:google.maps.Animation.DROP
						});


						$('.gm-style-iw').css({'width':'100px !important'});		              
			            infowindow.setContent('<div id="iw-container" style="width: 294px;margin: 0 2px;text-align: center;">'+
						  						'<div class="iw-title">'+
						  							' <div class="info">' +
						  								' <h3>You Are Here</h3>' +
						  							' </div>' +
						  						'</div>' +
								              '</div>');
			            infowindow.open(map, marker);
			            google.maps.event.addListener(marker, 'click', (function (marker) {
						      return function () {
						          infowindow.setContent('<div id="iw-container" style="width: 294px;margin: 0 2px;text-align: center;">'+
						  						'<div class="iw-title">'+
						  							' <div class="info">' +
						  								' <h3>You Are Here</h3>' +
						  							' </div>' +
						  						'</div>' +
								              '</div>');
						          infowindow.open(map, marker);
						      }
						  })(marker));
			            map.setCenter(pos);
			            //  End of Current Location

			            google.maps.event.addListener(infowindow, 'domready', function() {

						   var iwOuter = $('.gm-style-iw');

						   var iwBackground = iwOuter.prev();

						   // Remove the background shadow DIV
						   iwBackground.children(':nth-child(2)').css({'display' : 'none'});

						   // Remove the white background DIV
						   iwBackground.children(':nth-child(4)').css({'display' : 'none'});

							iwBackground.children(':nth-child(3)').find('div').children().css({'box-shadow': 'rgba(72, 181, 233, 0.6) 0px 1px 6px','z-index' : '1'});
							
							var iwCloseBtn = iwOuter.next();

							iwCloseBtn.css({'display': 'none'});
						});

			            // This is the nearby Clinic Markers
						for (i = 0; i < locations.length; i++) {
						  var image = {
						    url: locations[i].annotation_url,
						    scaledSize: new google.maps.Size(40, 50), // scaled size
						    origin: new google.maps.Point(0,0), // origin
						    anchor: new google.maps.Point(0, 0) // anchor
						  };

						  var stat;

						  if( locations[i].open_status== 1 ){
						  	stat = '<i class="fa fa-circle green"></i> Open';
						  }else{
						  	stat = '<i class="fa fa-circle red"></i> Closed';
						  }

						  marker = new google.maps.Marker({
						      position: new google.maps.LatLng(locations[i].lattitude, locations[i].longitude),
						      map: map,
						      icon: image,
						  	  animation:google.maps.Animation.DROP
						  });

						  google.maps.event.addListener(marker, 'click', (function (marker, i) {
						      return function () {
						          infowindow.setContent('<div id="iw-container">'+
						  						'<div class="iw-title">'+
						  							' <img src="'+locations[i].image_url+'" />' +
						  							' <div class="info">' +
						  								' <h3>' +locations[i].name+ '</h3>' +
						  								' <p>' +locations[i].address+ '</p>' +
						  							' </div>' +
						  						'</div>' +
						  						'<div class="iw-status">'+
						  							' <p>' +stat+ ' <a href="#/appointment-create/'+locations[i].clinic_id+'/favourites" class="btn btn-book pull-right">BOOK NOW</a> </p>' +
						  						'</div>' +
								              '</div>');
						          // infowindow.setContent(locations[i].name);
						          infowindow.open(map, marker);
						      }
						  })(marker, i));
						}
						// END


			          }, function() {
			            console.log("error getting current location");
			          });
			        }else{
			        	console.log("geolocation not supported");
			        }

			    }

			    scope.toggleFavs = function(list,num){
					console.log(num);

					if(num == 1){	
						scope.removeFavourite(list);
					}else{
						scope.addToFavourite(list);
					}
					
				}

			    scope.removeFavourite = function(data) {
					// console.log(data);
					$.confirm({
					    title: '',
					    content: 'Are you sure you want to remove ' + data.name.toUpperCase() + ' from favourites?',
					    buttons: {
					        confirm: function () {
								favouritesModule.removeFavourite(data.clinic_id, 0)
								.success(function(response){
									// console.log( response );
									scope.getClinicLocation();
								})
								.error(function(response){
									$.alert({
									    title: '',
									    content: 'Oooops! Something went wrong.',
									    columnClass: 'small'
									});
								});
					        },
					        cancel: function () {
					            
					        }
					    }
					});
				};

				scope.addToFavourite = function(data) {
					var favourite = data.favourite;
					var status =1;

					favouritesModule.removeFavourite(data.clinic_id, status)
					.success(function(response){
						scope.getClinicLocation();
					})
					.error(function(response){
						$.alert({
							    title: '',
						    content: 'Oooops! Something went wrong.',
						    columnClass: 'small'
						});
					});
				};

			    scope.onLoad = function(){
			    	$('.modal').modal('hide');
			    }

			    scope.onLoad();
				scope.getClinicLocation( );
			}
		}
	}
])