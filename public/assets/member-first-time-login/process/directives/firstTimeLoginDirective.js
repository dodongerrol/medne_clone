app.directive('firstTimeLoginDirective', [
	'$http',
	'serverUrl',
	'memberSettings',
	function directive($http, serverUrl, memberSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link(scope, element, attributeSet) {
				console.log("firstTimeLoginDirective Runnning !");

				// variables
				scope.formData = {};

				scope.togglePassword = function () {
					scope.typePassword = !scope.typePassword;
					scope.toggleEye = !scope.toggleEye;

				};

				scope.checkPassMatch = function () {
					if (scope.formData.password2 != scope.formData.password) {
						scope.passMatch = 'form-cotrol-danger'
						console.log('password did not match');
					} else {
						scope.passMatch = 'form-cotrol-sucess'
						console.log('password match');
					};

				}

				scope.submit = function (data) {
					console.log(data);
				}

				scope.onLoad = function () {

				}

				scope.onLoad();
				// add plain JS or jquery here

				//date picker bootstrap
				$(function () {
					$('#datetimepicker1').datetimepicker({
						format: 'LL'
					});
				});

				// strong password plugin
				(function () {
					var ZXCVBN_SRC = '/assets/member-first-time-login/js/zxcvbn/zxcvbn.js';

					var async_load = function () {
						var first, s;
						// create a <script> element using the DOM API
						s = document.createElement('script');

						// set attributes on the script element
						s.src = ZXCVBN_SRC;
						s.type = 'text/javascript';
						s.async = true; // HTML5 async attribute

						// Get the first script element in the document
						first = document.getElementsByTagName('script')[0];

						// insert the <script> element before the first in the document
						return first.parentNode.insertBefore(s, first);
					};

					// attach async_load as callback to the window load event
					if (window.attachEvent != null) {
						window.attachEvent('onload', async_load);
					} else {
						window.addEventListener('load', async_load, false);
					}
				}).call(this);

			}
		}
	}
]);