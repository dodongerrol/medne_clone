var enquiry = angular.module('enquiry', []);

enquiry.run(function($http) {
  $http.defaults.headers.common.Authorization = window.localStorage.getItem('token');
});

enquiry.factory('serverUrl',[
    function factory(){
      return {
        url: window.location.origin,
				// url: 'http://ec2-13-251-63-109.ap-southeast-1.compute.amazonaws.com',
				external_url: 'https://dev.geckorest.com/mednefits/',
        mednefits_url: 'http://app.mednefits.com/api/'
      }
    }
]);

enquiry.directive('enquiryDirective', [
	"$http",
	"serverUrl",
	function directive($http, serverUrl) {
		return {
			restrict: "A",
			scope: true,
			link: function link(scope, element, attributeSet) {
				console.log('running enquiryDirective!');

				scope.companyDetails	=	{};

				scope.submitEnquiry	=	function(formData){
					var data	=	{
						content: formData.message
					}
					scope.showLoading();
					$http.post(serverUrl.url + '/hr/send_spending_activation_inquiry', data)
						.success(function(response){
							console.log(response);
							scope.hideLoading();
							if(response.status){
								scope.companyDetails.message = null;
								swal('Success!', response.message, 'success');
							}else{
								swal('Error!', response.message, 'error');
							}
						});
				}
        
        scope.getCompanyInfo	=	function(){
					scope.showLoading();
					$http.get(serverUrl.url + '/hr/get_intro_overview')
						.success(function(response){
							console.log(response);
							scope.hideLoading();
							if(response.status){
								scope.companyDetails = response.data;
							}else{
								swal('Error!', response.data.message, 'error');
							}
						});
				}
				scope.showLoading = function () {
					$(".circle-loader").fadeIn();
				}
				scope.hideLoading = function () {
					setTimeout(function () {
						$(".circle-loader").fadeOut();
					}, 100)
				}



				scope.onLoad	=	function(){
					scope.getCompanyInfo();
				}
				
				scope.onLoad();
			}
		}
	}
]);
