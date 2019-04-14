var appointmentService = angular.module('appointmentService', [])

appointmentService.factory('appointmentsModule', function( serverUrl, $http ){
	var appointmentFactory = {};

	appointmentFactory.appointmentList = function( ) {
		return $http.get(serverUrl.url + 'clinic/booking-history');
	};

	appointmentFactory.getAppointmentDetails = function( id ) {
		return $http.get(serverUrl.url + 'clinic/booking-detail/' + id);
	};

	appointmentFactory.cancelAppointment = function( booking_id, clinic_id ) {
		return $http.post(serverUrl.url + 'doctor/booking-delete', { appointmentid: booking_id, clinicid: clinic_id });
	};

	appointmentFactory.getClinicDetails = function( id ) {
		return $http.get(serverUrl.url + 'clinic/clinicdetails/' + id);
	};

	appointmentFactory.getProcedureDetails = function( id ) {
		return $http.get(serverUrl.url + 'clinic/procedure_details/' +id);
	};

	appointmentFactory.getDoctorProcedure = function( id ) {
		return $http.get(serverUrl.url + 'clinic/doctor_procedure/' + id);
	};

	appointmentFactory.getDoctorAvailability = function( date, id ) {
		return $http.get(serverUrl.url + 'doctor/availability?date=' + date + '&doctorid=' + id);
	};

	appointmentFactory.getSlotsRefresh = function( date, doctorid, procedureid, clinicid ) {
		return $http.post(serverUrl.url + 'doctor/slots-refresh', { bookingdate: date, procedureid: procedureid, doctorid: doctorid, clinicid: clinicid });
	}

	appointmentFactory.getDoctorMoreSlots = function( date, doctorid, clinicid ) {
		return $http.post(serverUrl.url + 'doctor/moreslots', { date: date, doctorid: doctorid, clinicid: clinicid });
	}

	appointmentFactory.BookingQueue = function( ) {
		return $http.post(serverUrl.url + 'doctor/booking-queue');
	};

	appointmentFactory.BookingSlot = function( ) {
		return $http.post(serverUrl.url + 'doctor/booking-slot');
	};

	appointmentFactory.getDoctorSlot = function( date, doctorid, clinicid ) {
		return $http.post(serverUrl.url + 'doctor/moreslots', { doctorid: doctorid, date: date, clinicid: clinicid });
	};

	appointmentFactory.getDoctorMoreQueues = function( date, doctorid, clinicid ) {
		return $http.post(serverUrl.url + 'doctor/morequeues', { doctorid: doctorid, date: date, clinicid: clinicid });
	};

	appointmentFactory.confirmSlot = function( data ) {
		return $http.post(serverUrl.url + 'doctor/confirm-slot', data);
	};

	appointmentFactory.resendSMS = function( number ) {
		return $http.post(serverUrl.url + 'auth/otpresend');
	}

	appointmentFactory.validateOTP = function( otp ) {
		return $http.post(serverUrl.url + 'auth/otpvalidation', { otp_code: otp });
	};

	appointmentFactory.otpUpdate = function( phone, promo, nric ) {
		return $http.post(serverUrl.url + 'auth/otpupdate', { mobile_phone: phone, promo_code: promo, nric: nric });
	}

	return appointmentFactory;
});