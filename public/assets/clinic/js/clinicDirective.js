var clinic = angular.module("clinic", [

]);
window.base_url = window.location.origin + "/app/";


clinic.directive("clinicDirective", [
  "$http",
  function directive($http) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("clinicDirective");
        
        scope.forgot_password_data = {};
        scope.new_password_error = false;
        scope.password_success = false;

        scope.changePassword = function( data ){
          if( data.new_password == data.new_password2 ){
            scope.new_password_error = false;
            var id = $('#clinic-id').val();
            var data = {
              newpass : data.new_password,
              oldpass : data.new_password,
              userid  : id
            }

            console.log(data);

            $http.post(window.base_url + 'auth/resetpassword', data)
             .then(function(response){
               console.log(response);
               if(response.data.status) {
                scope.password_success = true;
               } else {
                scope.password_success = false;
               }
             });

          }else{
            scope.new_password_error = true;
          }
        }
        
      }
    };
  }
])



