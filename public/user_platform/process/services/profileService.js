var profileService = angular.module('profileService', [])

profileService.factory('profilesModule', function( serverUrl, $http, Upload ){
	var profileFactory = {};

	profileFactory.updateProfile = function( profile ) {
		return Upload.upload({
	    	url: serverUrl.url + 'auth/update',
	    	arrayKey: '',
	    	data: profile,
	    	method: 'POST'
	    });
	};
	profileFactory.getProfile = function( ) {
		return $http.get(serverUrl.url + 'auth/userprofile');
	};
	profileFactory.profileUpdate = function( data ) {
		return $http.post(serverUrl.url + 'auth/update', data);
	};
	profileFactory.updateUserCredentials = function( user ) {
		return $http.post(serverUrl.url + 'auth/change-password', { oldpassword: user.oldpassword, password: user.password });
	};
	profileFactory.addMedication = function( medication ) {
		return $http.post(serverUrl.url + 'auth/newmedication', { medication: medication.medication, userid: medication.userid, dosage: medication.dosage });
	};
	profileFactory.addMedicalHistory = function( medical ) {
		return $http.post(serverUrl.url + 'auth/newhistory', medical);
	};
	profileFactory.addAllergy = function( allergy ) {
		return $http.post(serverUrl.url + 'auth/newallergy', allergy);
	};
	profileFactory.addMedicalCondition = function( medical_condition ) {
		return $http.post(serverUrl.url + 'auth/newcondition', medical_condition);
	};
	profileFactory.deleteMedication = function( id ) {
		return $http.get(serverUrl.url + 'auth/deletemedication?value=' + id);
	};
	profileFactory.deleteMedicalHistory = function( id ) {
		return $http.get(serverUrl.url + 'auth/deletehistory?value=' + id);
	};
	profileFactory.deleteAllergy = function( id ) {
		return $http.get(serverUrl.url + 'auth/deleteallergy?value=' + id);
	};
	profileFactory.deleteMedicalCondition = function( id ) {
		return $http.get(serverUrl.url + 'auth/deletecondition?value=' + id);
	};
	return profileFactory;
});