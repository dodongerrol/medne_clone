app.directive('appointmentsDirective', [
	"$http",
	"appointmentsModule",
	"serverUrl",
	"favouritesModule",
	function directive( $http, appointmentsModule, serverUrl ,favouritesModule) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				// console.log("appointments Directive Runnning !");
				scope.appointment_list = {};
				scope.appointment_details = {};
				$( ".sidebar ul li" ).removeClass('active');
				$( ".sidebar ul li#appointments_li" ).addClass('active');
				$('#select-clinic').modal('hide');
				$('body').removeClass('modal-open');
				$('.modal-backdrop').remove();
				
				scope.userInfo = function userInfo( ) {
					$http.get(serverUrl.url + 'auth/userprofile')
					.success(function(response){
						scope.user = response;
					})
					.error(function(err){
						
					});
				};

				scope.getAppointmentList = function( ) {
					appointmentsModule.appointmentList( )
					.success(function(response){
						// console.log(response);
						scope.appointment_list = response.data;
						$('#loading-container').fadeOut(500);
						$('#appointment-options').fadeIn(500);
						$('.upcoming').fadeIn(500);
						$('.past').fadeIn(500);
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.formatDate = function(date) {
					return moment(date).format("MMMM D, YYYY");  
				};

				scope.formatDateAppointmentDisplay = function(date) {
					return moment(date).format('Do MMMM YYYY');
				};

				scope.showAppointment = function(id) {
					// console.log(id);
					appointmentsModule.getAppointmentDetails(id)
					.success(function(response){
						// console.log(response);
						scope.appointment_details = response.data;
						$('#trackModal').modal('show');
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.cancelAppointment = function( booking_id, clinic_id ) {
					var btn = $('#cancel-appointment').text();
					$('#cancel-appointment').text('CANCELLING...')
					$('#cancel-appointment').attr('disabled', true);

					appointmentsModule.cancelAppointment( booking_id, clinic_id )
					.success(function(response){
						$('#cancel-appointment').attr('disabled', false);
						$('#cancel-appointment').text(btn);
						scope.getAppointmentList( );
						$('#trackModal').modal('hide');
						scope.appointment_details = {};
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.searchClinic = function( ) {
					scope.search_list = [];
					// console.log(scope.search);
					favouritesModule.searchClinic(scope.search.clinic_name)
					.success(function(response){
						// console.log(response);
						angular.forEach(response.data.clinics, function(value, key){
							var index = $.inArray( value.clinic_id, scope.fav_list );
							if( index >= 0 ){
								scope.search_list.push({ clinic_id: value.clinic_id, address: value.address, country: value.country, district: value.district, name: value.name, open_status: value.open_status, telephone: value.telephone, favourite: 1 });
							} else {
								scope.search_list.push({ clinic_id: value.clinic_id, address: value.address, country: value.country, district: value.district, name: value.name, open_status: value.open_status, telephone: value.telephone, favourite: 0 });
							}
						});
						angular.forEach(response.data.doctors, function(value, key){
							scope.search_list.push({ clinic_id: value.clinic_id, address: value.address, country: "", district: "", name: value.name, open_status: value.open_status, telephone: value.phone, favourite: 2 });
						});
						// console.log(scope.search_list);
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.goToClinic = function(data){
					// console.log(data);
					$('#search-clinic').modal('hide');

					setTimeout(function(){
						window.location.href = "#/appointment-create/" + data.clinic_id + "/favourites";
					},400);
					
				}

				scope.addToFavourite = function(data) {
					var favourite = data.favourite;
					var status = 0;
					if(favourite == 0) {
						status = 1;
						$('#fav_icon_' + data.clinic_id).removeClass('fa-heart-o');
						$('#fav_icon_' + data.clinic_id).addClass('fa-heart');
					} else {
						status = 0;
						$('#fav_icon_' + data.clinic_id).removeClass('fa-heart');
						$('#fav_icon_' + data.clinic_id).addClass('fa-heart-o');
					}
					favouritesModule.removeFavourite(data.clinic_id, status)
					.success(function(response){
						scope.getFavouriteList( );
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
				scope.userInfo( );
				scope.getAppointmentList( );
			}
		}
	}
]);
