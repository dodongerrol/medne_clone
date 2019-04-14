app.directive("loginDirective", [
  "$state",
  "eclaimSettings",
  function directive($state, eclaimSettings) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        scope.invalid_credentials = false;
        scope.login_details = {};
        var introLoader_trap;
        var loading_trap;
        scope.forgot_password_data = {};
        scope.new_password_error = false;
        scope.password_success = false;

        scope.changePassword = function( data ){
          if( data.new_password == data.new_password2 ){
            scope.new_password_error = false;

            var data = {
              new_password : data.new_password
            }

            // $http.post(serverUrl.url + 'hr/forgot/company-benefits-dashboard', data)
            //  .success(function(response){
            //    console.log(response);
            //  });

            scope.password_success = true;
          }else{
            scope.new_password_error = true;
          }
        }

        scope.showForgotPassword = function() {
          $("#login-container").hide();
          $("#forgot-password").show();
        };

        scope.showLogin = function() {
          $("#forgot-password").hide();
          $("#login-container").show();
        };

        scope.login = () => {
          scope.showLoading();

          var data = {
            email: scope.email,
            password: scope.password
          };

          eclaimSettings.loginEmp(data).then(function(response) {
            scope.hideLoading();
            if (response.data.status == true) {
              scope.invalid_credentials = false;
              scope.email = null;
              scope.password = null;
              // $state.go("home");
              window.location.href = window.location.origin + '/member-portal#/home';
            } else {
              scope.invalid_credentials = true;
            }
          })
          .catch(function(error) {
            scope.hideLoading();
            swal('Ooops!', 'Connection Losts! Please check your internet connection.', 'error');
          });
        };

        scope.resetPassword = function() {
          var data = {
            email: scope.login_details.email
          };
          scope.showLoading();
          eclaimSettings.resetPassword(data).then(function(response) {
            console.log(response);
            if(response.data.status) {
						  alert(response.data.message);
            }
            scope.hideLoading();
          })
          .catch(function(err){
            swal('Ooops!', 'Connection Losts! Please check your internet connection.', 'error');
          });
        };

        scope.showLoading = () => {
          $(".circle-loader").fadeIn();
          loading_trap = true;
        };

        scope.hideLoading = () => {
          setTimeout(function() {
            $(".circle-loader").fadeOut();
            loading_trap = false;
          }, 800);
        };

        scope.hideIntroLoader = () => {
          setTimeout(function() {
            $(".main-loader").fadeOut();
            introLoader_trap = false;
          }, 1000);
        };

        scope.onLoad = () => {
          scope.hideIntroLoader();
          scope.hideLoading();
          console.log( window.location );
        };

        scope.onLoad();
      }
    };
  }
]);
