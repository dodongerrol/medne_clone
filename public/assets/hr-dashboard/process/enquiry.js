var enquiry = angular.module('enquiry', []);

enquiry.run(function($http) {
  $http.defaults.headers.common.Authorization = window.localStorage.getItem('token');
});

enquiry.factory('serverUrl',[
    function factory(){
      return {
        url: window.location.origin,
        // url: 'http://ec2-13-251-63-109.ap-southeast-1.compute.amazonaws.com',
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
        
        
        
			}
		}
	}
]);
