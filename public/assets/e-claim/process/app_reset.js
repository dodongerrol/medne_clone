var app = angular.module('app', ['ui.router', 'ngCacheBuster']);

app.factory('serverUrl',[
    function factory(){
      return {
        url: window.location.origin,
      }
    }
]);

app.directive("forgotDirective", [
  "$state",
  "$http",
  function directive($state, $http) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        var url = window.location.origin + '/';
        scope.forgot_password_data = {};
        scope.password_success = false;
        scope.expire_token = false;

        scope.devicePlatform = null;
        scope.deviceOs = null;

        scope.changePassword = function( ) {
          if(scope.forgot_password_data.new_password !== scope.forgot_password_data.confirm_password) {
            return alert('New Password and Confirm Password does not match.');
          }
          $('#login-btn').attr('disabled', true);
          $('#login-btn').text('PROCCESSING...');
          $http.post(url + 'v2/auth/reset-process', { userid: scope.forgot_password_data.user_id, oldpass: scope.forgot_password_data.new_password, newpass: scope.forgot_password_data.new_password })
          .then(function(response){
            if(response.data.status) {
              scope.password_success = true;
            } else {
              swal('Oooops!','Something went wrong with the connection. Please try again. If problem still exist, Please contact Mednefits Team.', 'error');
            }
            $('#login-btn').attr('disabled', false);
            $('#login-btn').text('CHANGE PASSWORD');
          })
          .catch(function(err){
            $('#login-btn').attr('disabled', false);
            $('#login-btn').text('CHANGE PASSWORD');
            swal('Oooops!','Something went wrong with the connection. Please try again.', 'error');
          });
        }

        scope.getOs = function(){
          var userAgent = window.navigator.userAgent,
              platform = window.navigator.platform,
              macosPlatforms = ['Macintosh', 'MacIntel', 'MacPPC', 'Mac68K'],
              windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
              iosPlatforms = ['iPhone', 'iPad', 'iPod'],
              os = null;

          if (macosPlatforms.indexOf(platform) !== -1) {
            os = 'Mac OS';
          } else if (iosPlatforms.indexOf(platform) !== -1) {
            os = 'iOS';
          } else if (windowsPlatforms.indexOf(platform) !== -1) {
            os = 'Windows';
          } else if (/Android/.test(userAgent)) {
            os = 'Android';
          } else if (!os && /Linux/.test(platform)) {
            os = 'Linux';
          }

          scope.deviceOs = os;
          return os;
        }

        scope.goToLogin = function(){
          if( scope.devicePlatform == 'web' ){
            window.location = '/member-portal-login';
          }else{
            window.location = 'mednefitsapp://';
          }
        }
        
        scope.onLoad = function( ) {
          var fetchOs = scope.getOs();
          if( fetchOs == 'Mac OS' || fetchOs == 'Windows' ){
            scope.devicePlatform = 'web';
          }else{
            scope.devicePlatform = 'mobile';
          }

          var urlParams = new URLSearchParams(window.location.search);
          $http.post(url + 'v1/auth/reset-details', { resetcode: urlParams.get('token') })
          .then(function(response){
            if(response.data.status) {
              scope.forgot_password_data.user_id = response.data.user_id;
            } else {
              scope.expire_token = true;
            }
          })
          .catch(function(err){
            swal('Ooops!', 'Connection Losts! Please check your internet connection.', 'error');
          });
        };

        scope.onLoad();
      }
    };
  }
]);