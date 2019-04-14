app.directive('favouritesDirective', [
	"$http",
	"serverUrl",
	"favouritesModule",
	"appointmentsModule",
	"$state",
	function directive( $http, serverUrl, favouritesModule, appointmentsModule, $state ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				// console.log("favourites Directive Runnning !");
				scope.favourite_list = {};
				scope.search = {};
				scope.fav_list = [];
				scope.search_list = {};
				scope.clinic_details = {};
				scope.clinic_procedure = {};
				scope.date = {};
				scope.date_temp_booking = {};
				scope.schedule_list = {};
				scope.booking = {};
				$( ".sidebar ul li" ).removeClass('active');
				$( ".sidebar ul li#favourites_li" ).addClass('active');

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

				scope.getFavouriteList = function( ) {
					scope.fav_list = [];
					favouritesModule.favouriteList( )
					.success(function(response){
						// console.log(response);
						scope.favourite_list = response.data;
						angular.forEach(response.data, function(value, key ) {
							scope.fav_list.push( value.clinic_id );
						});

						// console.log(scope.fav_list);
						$('#loading-container').fadeOut(500);
						$('#add-favourite').fadeIn(500);
						$('#fav-container').fadeIn(500);
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.removeFavourite = function(data) {
					// console.log(data);
					$.confirm({
					    title: '',
					    content: 'Are you sure you want to remove ' + data.name.toUpperCase() + ' ?',
					    buttons: {
					        confirm: function () {
								favouritesModule.removeFavourite(data.clinic_id, 0)
								.success(function(response){
									// console.log( response );
									$('#fav_' + data.clinic_id).fadeOut(500);
									scope.getFavouriteList( );
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
						// console.log(scope.search_list);
					})
					.error(function(err){
						// console.log(err);
					});
				};

				
				
				scope.onLoad = function(){
					$('#select-clinic').on('hidden.bs.modal', function (e) {
					  	procedure_trap = 0;
						doctor_trap = 0;
						scope.clinic_details = null;

						$('#procedures .tab-list .list .left a').removeClass('active');

						$('.one a').tab('show');
						$('#main-body').fadeIn(500);
						$('#schedule-list').hide();
					})

					$('#select-clinic').modal({
						show : false
					});
				}

				// scope.findClinicDetails = function(id) {
					// $state.go('appointment-creat')
					// scope.clinic_details = null;

					// appointmentsModule.getClinicDetails(id)
					// .success(function(response){
					// 	console.log(response);
					// 	// scope.booking.clinic = response.data;
					// 	scope.clinic_details = response.data;
					// 	scope.clinic_procedures = response.data.clinic_procedures;
					// 	scope.clinic_doctors = response.data.doctors;

					// 	$('#select-clinic').modal({
					// 		show : true,
					// 		keyboard : false,
					// 		backdrop : 'static'
					// 	});
					// })
					// .error(function(err){

					// });
				// };

				var procedure_trap = 0;
				var doctor_trap = 0;

				scope.selectProcedure = function(list , num){
					console.log(list);
					scope.booking.list = list;

					$('#procedures .tab-list .list .left a').removeClass('active');
					$('#procedures .tab-list .list:nth-child(' + num + ') .left a').addClass('active');

					if( procedure_trap == 0 ){
						appointmentsModule.getProcedureDetails(list.procedureid)
						.success(function(response){
							console.log(response)
							scope.clinic_doctors = response.data.doctors;
							scope.selected_procedure_id = list.procedureid;
						})
						.error(function(err){
							console.log(err);
						});

						$('.two a').tab('show');
						doctor_trap = 1;
					}else{
						scope.selectScheduleList(list, scope.date_list[0].date, scope.selected_doctor_id, list.procedureid, scope.clinic_details.clinic_id,0)
					}
				}

				scope.selectDoctor = function( list ){
					console.log(list);
					scope.booking.list = list;
					if( doctor_trap == 0 ){

						appointmentsModule.getDoctorProcedure(list.doctor_id)
						.success(function(response){
							console.log(response);
							scope.clinic_procedures = response.data.clinic_procedures;
							scope.selected_doctor_id = list.doctor_id
						})
						.error(function(err){
							console.log(err);
						});

						$('.one a').tab('show');
						procedure_trap = 1;
					}else{
						scope.selectScheduleList(list, scope.date_list[0].date, list.doctor_id, scope.selected_procedure_id, scope.clinic_details.clinic_id,0);
					}

				}

				// scope.tab.step = 0;
				scope.selectScheduleList = function(list, date, doctor_id, procedure_id, clinic_id,num) {
					scope.schedule_list = null;

					scope.booking.list.doctor = list;

					scope.date_temp_booking.date = date;
					scope.date_temp_booking.doctor_id = doctor_id;
					scope.date_temp_booking.procedure_id = procedure_id;
					scope.date_temp_booking.clinic_id = clinic_id;

					$('.line-list').removeClass('line-list-active');
					$('#line_indicator_' + num).addClass('line-list-active');
					appointmentsModule.getSlotsRefresh(date, doctor_id, procedure_id, clinic_id)
					.success(function(response){
						console.log(response);
						scope.schedule_list = response.data.booking.timeslot;

						$('#main-body').hide();
						$('#schedule-list').fadeIn(500);
						// console.log(scope.schedule_list);
						// $('#appointments-avaible-list').show();
						// $('#doctors .tab-list .list .left a').removeClass('active');
						// $('#appointments-avaible-list .tab-list .list:nth-child(' + num + ') .left a').addClass('active');
					})
					.error(function(err){
						console.log(err);
					});
				};

				scope.confirmBooking = function( start_time, end_time, date, doctor_id, procedure_id, clinic_id ) {
					scope.booking.bookingdate = date;
					scope.booking.starttime = start_time;
					scope.booking.endtime = end_time;
					scope.booking.procedureid = procedure_id;
					scope.booking.doctorid = doctor_id;
					scope.booking.remarks = '';
					scope.booking.clinicid = clinic_id;

					console.log(scope.booking);

					$('#schedule-list').hide();
					$('#confirm-details').fadeIn(500);
				};

				scope.submitBooking = function( ) {
					var btn = $('#confirm-booking').text();
					$('#confirm-booking').text('SUBMITTING...');
					$('#confirm-booking').attr('disabled', true);
					appointmentsModule.confirmSlot(scope.booking)
					.success(function(response){
						console.log(response);
						if(response.status == true) {
							// alert(response.data.message);
							$.alert({
							    title: '',
							    content: response.data.message,
							    columnClass: 'small'
							});
						} else {
							// alert(response.data.message);
							$.alert({
							    title: '',
							    content: response.data.message,
							    columnClass: 'small'
							});
						}
						$('#confirm-booking').text(btn);
						$('#confirm-booking').attr('disabled', false);
						$('')
						scope.booking = {};
						scope.date_temp_booking = {};
						scope.clinic_details = {};
						scope.clinic_procedures = {};
						scope.clinic_doctors = {};
						procedure_trap = 0;
						doctor_trap = 0;
					})
					.error(function(err){
						console.log(err);
					});
				};
				var response_status_booking, response_reconciliation_booking;
				scope.BookingSlot = function( start_time, end_time, date, doctor_id, procedure_id, clinic_id ) {
					scope.booking.bookingdate = date;
					scope.booking.starttime = start_time;
					scope.booking.endtime = end_time;
					scope.booking.procedureid = procedure_id;
					scope.booking.doctorid = doctor_id;
					scope.booking.remarks = '';
					scope.booking.clinicid = clinic_id;
					console.log(scope.booking);
					appointmentsModule.BookingSlot( )
					.success(function(response){
						console.log(response);
						scope.booking.nric = response.data.nric;
						scope.booking.phone = response.data.phone;
						response_status_booking = response.status;
						response_reconciliation_booking = response.reconciliation;
						$('#schedule-list').hide();
						$('#confirm-details').fadeIn(500);
					})
					.error(function(err){
						console.log(err);
					});
				};

				scope.getDoctorAvailability = function( date, doctor_id, procedure_id, clinic_id ) {
					appointmentsModule.getDoctorAvailability(date, doctor_id)
					.success(function(response){
						console.log(response);
					})
					.error(function(err){
						console.log(err);
					});
				};

				scope.getDoctorMoreQueues = function( date, doctor_id, procedure_id, clinic_id ) {
					appointmentsModule.getDoctorMoreQueues(date, doctor_id, clinic_id)
					.success(function(response){
						console.log(response);
					})
					.error(function(err){
						console.log(err);
					});
				};

				scope.getDateList = function( ) {
					scope.date_list = [];
					var date = [];
					var start = moment(new Date()).format('YYYY-MM-DD'),
				        end = moment(start, 'YYYY-MM-DD').add(3, 'days');
				    var range = moment.range(start, end);
				    range.by('days', function(moment) {
				   		date.push({ date: moment._d });
					});
					angular.forEach(date, function(value, key){
						scope.date_list.push({date: moment(value.date).format('DD-MM-YYYY')});
					});
					console.log(scope.date_list);
				};

				scope.formatDateAppointmentDisplay = function(date) {
					return moment(date).format('Do MMMM YYYY');
				};

				scope.showMain = function(){
					$('#main-body').fadeIn(500);
					$('#schedule-list').hide();
				}

				scope.showAppiontments = function(){
					$('#schedule-list').fadeIn(500);
					$('#confirm-details').hide();
				}

				scope.confirmBookingModal = function(){
					if(response.status == false && response.reconciliation == 1) {
						
					} else {
						$('#confirmBookingModal').fadeIn(500);
						$('#mainModal').hide();
						
					}
				}

				scope.cancelBookingModal = function(){
					$('#confirmBookingModal').hide();
					$('#mainModal').fadeIn(500);
				}

				scope.backToNric = function( ) {
					$('#confirmBookingModal').hide(500);
					$('#mainModal').fadeIn(500);
				};


				scope.getDateList();
				scope.userInfo( );
				scope.getFavouriteList( );
				scope.onLoad( );
			}
		}
	}
]);
