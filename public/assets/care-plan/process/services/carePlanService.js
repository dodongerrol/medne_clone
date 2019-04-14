var service = angular.module('CarePlanService', []);

service.factory('CarePlanSettings', function($http, serverUrl){
	var carePlanFactory = {};

	carePlanFactory.getSessionSurvey = function( ) {
		return $http.get(serverUrl.url + '/get/resume/purchase');
	};	

	carePlanFactory.getSessionSurveyData = function( id ) {
		return $http.get(serverUrl.url + '/get/purchase/data/' + id);
	};	

	carePlanFactory.insertSessionSurvey = function( data ) {
		return $http.post(serverUrl.url + '/insert/purchase' , data);
	};	

	carePlanFactory.insertCorporatePlan = function( data ) {
		return $http.post(serverUrl.url + '/insert/corporate_plan' , data);
	};	

	carePlanFactory.insertCorporateInfo = function( data ) {
		return $http.post(serverUrl.url + '/insert/corporate_business_information' , data);
	};

	carePlanFactory.insertCorporateContact = function( data ) {
		return $http.post(serverUrl.url + '/insert/corporate_business_contact' , data);
	};	

	carePlanFactory.insertPersonalDetails = function( data ) {
		return $http.post(serverUrl.url + '/insert/customer/personal_details' , data);
	};	

	carePlanFactory.insertCorporateAccount = function( data ) {
		return $http.post(serverUrl.url + '/insert/hr_dashboard_account' , data);
	};	

	carePlanFactory.updateCorporateStart = function( data ) {
		return $http.post(serverUrl.url + '/update/purchase_corporate_start' , data);
	};	

	carePlanFactory.updateCorporatePlan = function( data ) {
		return $http.post(serverUrl.url + '/update/purchase_corporate_plan' , data);
	};	

	carePlanFactory.updateCorporateBusinessInfo = function( data ) {
		return $http.post(serverUrl.url + '/update/purchase_corporate_business_information' , data);
	};	

	carePlanFactory.insertPromoCode = function( data ) {
		return $http.post(serverUrl.url + '/insert/corporate_promo_code' , data);
	};

	carePlanFactory.paymentCredit = function( data ) {
		return $http.post(serverUrl.url + '/payment/insert/corporate_credit_payment' , data);
	};	

	carePlanFactory.choosePayment = function( data ) {
		return $http.post(serverUrl.url + '/insert/corporate_choose_payment' , data);
	};	

	carePlanFactory.getNetworkList = function( ) {
		return $http.get(serverUrl.url + '/list/local_network');
	};	

	carePlanFactory.getNetworkListPartners = function( id ) {
		return $http.get(serverUrl.url + '/list/local_network_partners/' + id);
	};	

	return carePlanFactory;
});