app.directive('appointmentCreateDirective', [
	"$http",
	"appointmentsModule",
	"serverUrl",
	"$state",
	"$stateParams",
	"favouritesModule",
	"profilesModule",
	function directive( $http, appointmentsModule, serverUrl, $state, $stateParams ,favouritesModule, profilesModule) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				var procedure_trap = 0;
				var doctor_trap = 0;
				scope.clinic_details = {};
				scope.clinic_procedure = {};
				scope.date = {};
				scope.date_temp_booking = {};
				scope.schedule_list = {};
				scope.booking = {};



				scope.toggleFavs = function(list,num){
					// console.log(num);

					if(num == 1){	
						scope.removeFavourite(list);
					}else{
						scope.addToFavourite(list);
					}
					
				}

				scope.removeFavourite = function(data) {

					$.confirm({
					    title: '',
					    content: 'Are you sure you want to remove ' + data.name.toUpperCase() + ' from favourites ?',
					    buttons: {
					        confirm: function () {
					           // console.log('remove');
								favouritesModule.removeFavourite(data.clinic_id, 0)
								.success(function(response){
									// console.log( response );
									// $('#fav_' + data.clinic_id).fadeOut(500);
									// scope.getFavouriteList( );
									$.alert({
									    title: '',
									    content: 'Clinic removed from favourites',
									    columnClass: 'small'
									});

									$("#activeFavs").hide();
									$(".activeFavs").hide();
									$("#notActiveFavs").show();
									// $(".notActiveFavs").show();

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
					var status = 1;
					favouritesModule.removeFavourite(data.clinic_id, status)
					.success(function(response){
						// scope.getFavouriteList( );
						// console.log(response);
						$.alert({
						    title: '',
						    content: 'Clinic added to favourites',
						    columnClass: 'small'
						});

						$("#activeFavs").show();
						$(".activeFavs").show();
						$("#notActiveFavs").hide();
						$(".notActiveFavs").hide();
					})
					.error(function(response){
						$.alert({
							    title: '',
						    content: 'Oooops! Something went wrong.',
						    columnClass: 'small'
						});
					});
				};
				
				scope.userInfo = function userInfo( ) {
					$http.get(serverUrl.url + 'auth/userprofile')
					.success(function(response){
						scope.user = response;
					})
					.error(function(err){
						
					});
				};

				scope.findClinicDetails = function(id) {
					scope.clinic_details = null;

					appointmentsModule.getClinicDetails($stateParams.id)
					.success(function(response){
						// console.log(response);
						scope.clinic_details = response.data;
						scope.clinic_procedures = response.data.clinic_procedures;
						scope.clinic_doctors = response.data.doctors;

						// setTimeout(function(){
							$('#select-clinic').modal({
								show : true,
								keyboard : false,
								backdrop : 'static'
							});
						// },1500);
						
						// $('#select-clinic').modal('show');
					})
					.error(function(err){

					});
				};

				scope.selectProcedure = function(list , num){
					// console.log(list);
					// scope.booking.list = list;

					$('#procedures .tab-list .list .left a').removeClass('active');
					$('#procedures .tab-list .list:nth-child(' + num + ') .left a').addClass('active');

					if( procedure_trap == 0 ){
						appointmentsModule.getProcedureDetails(list.procedureid)
						.success(function(response){
							// console.log(response)
							scope.clinic_doctors = response.data.doctors;
							scope.selected_procedure_id = list.procedureid;
							scope.selected_procedure = response.data.name;
						})
						.error(function(err){
							// console.log(err);
						});

						$('.two a').tab('show');
						doctor_trap = 1;
					}else{
						scope.selectScheduleList(list, scope.date_list[0].date, scope.selected_doctor_id, list.procedureid, scope.clinic_details.clinic_id,0)
					}
				}

				scope.selectDoctor = function( list ){
					// console.log(list);
					// scope.booking.list = list;
					if( doctor_trap == 0 ){

						appointmentsModule.getDoctorProcedure(list.doctor_id)
						.success(function(response){
							// console.log(response);
							scope.clinic_procedures = response.data.clinic_procedures;
							scope.selected_doctor_id = list.doctor_id
						})
						.error(function(err){
							// console.log(err);
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

					scope.booking.list = list;

					scope.date_temp_booking.date = date;
					scope.date_temp_booking.doctor_id = doctor_id;
					scope.date_temp_booking.procedure_id = procedure_id;
					scope.date_temp_booking.clinic_id = clinic_id;

					$('.line-list').removeClass('line-list-active');
					$('#line_indicator_' + num).addClass('line-list-active');
					$('#loading').modal('show');
					appointmentsModule.getSlotsRefresh(moment(date).format('DD-MM-YYYY'), doctor_id, procedure_id, clinic_id)
					.success(function(response){
						// console.log(response);
						if(response.status == true) {
							scope.schedule_list = response.data.booking.timeslot;
							scope.booking.list.price = response.data.booking.price;
							scope.booking.list.duration = response.data.booking.duration;
						} else {
							// alert(response.message);

							$.alert({
							    title: '',
							    content: response.message,
							    columnClass: 'small'
							});
						}
						$('#main-body').hide();
						$('#schedule-list').fadeIn(500);
						$('#loading').modal('hide');
					})
					.error(function(err){
						// console.log(err);
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

					// console.log(scope.booking);

					$('#schedule-list').hide();
					$('#confirm-details').fadeIn(500);
				};

				scope.submitBooking = function( ) {
					var btn = $('#confirm-booking').text();
					$('#confirm-booking').text('SUBMITTING...');
					$('#confirm-booking').attr('disabled', true);
					appointmentsModule.confirmSlot(scope.booking)
					.success(function(response){
						// console.log(response);
						if(response.status == true) {
							scope.track_response = response.data.message
							$('#trackPopupModal').modal({
								show : true,
								keyboard : false,
								backdrop : 'static'
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
						
					})
					.error(function(err){
						// console.log(err);
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
					// console.log(scope.booking);
					appointmentsModule.BookingSlot( )
					.success(function(response){
						// console.log(response);
						scope.booking.nric = response.data.nric;
						scope.booking.phone = response.data.phone;
						response_status_booking = response.status;
						response_reconciliation_booking = response.reconciliation;
						$('#schedule-list').hide();
						$('#confirm-details').fadeIn(500);
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.getDoctorAvailability = function( date, doctor_id, procedure_id, clinic_id ) {
					appointmentsModule.getDoctorAvailability(date, doctor_id)
					.success(function(response){
						// console.log(response);
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.getDoctorMoreQueues = function( date, doctor_id, procedure_id, clinic_id ) {
					appointmentsModule.getDoctorMoreQueues(date, doctor_id, clinic_id)
					.success(function(response){
						// console.log(response);
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.formatDateAppointmentDisplay = function(date) {
					return moment(date).format('Do MMMM YYYY');
				};

				scope.showMain = function(){
					$('#main-body').fadeIn(500);
					$('#schedule-list').hide();
				};

				scope.showAppiontments = function(){
					$('#schedule-list').fadeIn(500);
					$('#confirm-details').hide();
				};

				scope.confirmBookingModal = function(){
					// console.log(scope.booking);
					if(scope.booking.nric.length < 9) {
						// alert('NRIC digit must be 6.');
						$.alert({
						    title: '',
						    content: 'NRIC digit must be 6.',
						    columnClass: 'small'
						});
						return false;
					}
					if(!scope.booking.promo) {
						scope.booking.promo = '';
					}
						console.log(response_status_booking, response_reconciliation_booking);
					if(response_status_booking == false && response_reconciliation_booking == 1) {
						appointmentsModule.otpUpdate(scope.booking.phone, scope.booking.promo, scope.booking.nric)
						.success(function(response){
							$('#confirm-opt').fadeIn(500);
							$('#confirm-details').hide();
						})
						.error(function(err){
							$.alert({
							    title: '',
							    content: err,
							    columnClass: 'small'
							});
						});
					} else {
						var profile = {
							mobile_phone: scope.booking.phone,
							nric: scope.booking.nric
						};
						profilesModule.profileUpdate(profile)
						.success(function(response){
							$('#confirmBookingModal').fadeIn(500);
							$('#mainModal').hide();
						})
						.error(function(err){
							$.alert({
							    title: '',
							    content: err,
							    columnClass: 'small'
							});
						})
					}
				};

				var validate_opt = false;
				scope.optValidate = function( ) {
					// console.log(scope.booking.otp);
					if(validate_opt == true) {
						$('#confirmBookingModal').fadeIn(500);
						$('#mainModal').hide();
					} else {
						appointmentsModule.validateOTP(scope.booking.otp)
						.success(function(response){
							// console.log(response);
							if(response.status == true) {
								validate_opt = true;
								$('#confirmBookingModal').fadeIn(500);
								$('#mainModal').hide();
							} else {
								// alert(response.message);
								$.alert({
								    title: '',
								    content: response.message,
								    columnClass: 'small'
								});
							}
						})
						.error(function(err){
							// alert(err);
							$.alert({
							    title: '',
							    content: err,
							    columnClass: 'small'
							});
						})
					}
				};

				scope.resendSms = function( ) {
					appointmentsModule.resendSMS();
				};

				scope.cancelBookingModal = function(){
					$('#confirmBookingModal').hide();
					$('#mainModal').fadeIn(500);
				};

				scope.backToNric = function( ) {
					$('#confirmBookingModal').hide(500);
					$('#mainModal').fadeIn(500);
					$('#confirm-opt').hide(500);
				};
				scope.getDateList = function( ) {
					scope.date_list = [];
					var date = [];
					var start = moment(new Date()).format('YYYY-MM-DD'),
				        end = moment(start, 'YYYY-MM-DD').add(4, 'days');
				    var range = moment.range(start, end);
				    range.by('days', function(moment) {
				   		date.push({ date: moment._d });
					});
					angular.forEach(date, function(value, key){
						scope.date_list.push({date: moment(value.date).format('YYYY-MM-DD')});
					});
					// console.log(scope.date_list);
				};

				var date_trap = 0;
				var temp_date;
				scope.nextSetDate = function(){
					date_trap++;
					scope.date_list = [];
					var date = [];
					var start = moment(new Date()).add(date_trap, 'days').format('YYYY-MM-DD'),
				        end = moment(start, 'YYYY-MM-DD').add(4, 'days');
				    temp_date = start;
				    var range = moment.range(start, end);
				    range.by('days', function(moment) {
				   		date.push({ date: moment._d });
					});
					angular.forEach(date, function(value, key){
						scope.date_list.push({date: moment(value.date).format('YYYY-MM-DD')});
					});
				}

				scope.backSetDate = function(){
					date_trap--;
					scope.date_list = [];
					var date = [];
					var start = moment(temp_date).subtract(1, 'days').format('YYYY-MM-DD'),
				        end = moment(start, 'YYYY-MM-DD').add(4, 'days');
				    temp_date = start;
				    var range = moment.range(start, end);
				    range.by('days', function(moment) {
				   		date.push({ date: moment._d });
					});
					angular.forEach(date, function(value, key){
						scope.date_list.push({date: moment(value.date).format('YYYY-MM-DD')});
					});
				}

				scope.onLoad = function(){
					$('#select-clinic').on('hidden.bs.modal', function (e) {
					  	procedure_trap = 0;
						doctor_trap = 0;
						scope.clinic_details = null;

						$('#procedures .tab-list .list .left a').removeClass('active');

						$('.one a').tab('show');
						$('#main-body').fadeIn(500);
						$('#schedule-list').hide();
						$('body').css({'padding-right':'0px'});
					})
				};

				scope.back = function( ) {

					$.confirm({
					    title: '',
					    content: 'Are you sure you want to cancel?',
					    buttons: {
					        confirm: function () {
					           $state.go($stateParams.state);
					        },
					        cancel: function () {
					            
					        }
					    }
					});
				};

				scope.getDirections = function( lat, lng) {
					 window.open('http://maps.google.com/maps?q=' + lat + ',' + lng, '_blank');
				};

				scope.userInfo( );
				scope.onLoad( );
				scope.findClinicDetails();
				scope.getDateList( );
			}
		}
	}
]);