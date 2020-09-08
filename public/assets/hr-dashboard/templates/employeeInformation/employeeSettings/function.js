app.directive('employeeSettingsDirective', [
  '$http',
  'serverUrl',
  '$timeout',
  '$state',
  'employeeFactory',
  'hrSettings',
  function directive($http, serverUrl, $timeout, $state, employeeFactory, hrSettings) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log('employeeSettingsDirective running!');
        scope.selected_member_id = localStorage.getItem('selected_member_id');
        
        scope.getEmployeeDetails  = async function(isRefresh){
          scope.selectedEmployee = await employeeFactory.getEmployeeDetails();
          if( scope.selectedEmployee == null || scope.selectedEmployee.user_id != Number(scope.selected_member_id) || isRefresh ){
            await scope.fetchEmployeeDetails();
          }else{
            // scope.hideLoading();
          }
        }
        scope.fetchEmployeeDetails  = async function(){
          scope.showLoading();
          await $http.get(serverUrl.url + "/hr/employee/" + scope.selected_member_id)
            .then(function(response){
              console.log(response);
              scope.selectedEmployee = response.data.data;
              employeeFactory.setEmployeeDetails(scope.selectedEmployee);
              // scope.hideLoading();
            });
        }

        scope._resetActivation_ = function () {
          let params = {
            id: scope.selectedEmployee.member_id,
          }
          scope.showLoading();
          hrSettings.employeeResetActivation ( params  )
            .then(function( response ) {
              console.log(response);
              if ( response.data.status == true ) {
                scope.hideLoading();
                swal('Success!', response.data.message, 'success');
              }
            });
        }

        scope._resetPassword_ = function() {
          let params = {
            id: scope.selectedEmployee.member_id,
          }
          scope.showLoading();
          hrSettings.employeeResetPassword ( params  )
            .then(function( response ) {
              console.log(response);
              if ( response.data.status == true ) {
                scope.hideLoading();
                swal('Success!', response.data.message, 'success');
              }
            });
        }

        scope.manageCap = function () {
          $("#manage-cap-modal").modal('show');
        }

        scope.submitCapPerVisit = function (cap) {
          scope.showLoading();
          var data = {
            employee_id: scope.selectedEmployee.user_id,
            cap_amount: cap,
          }
          hrSettings.updateCapPerVisit(data)
            .then(function (response) {
              scope.hideLoading();
              if (response.data.status) {
                scope.cap_per_visit = 0;
                swal('Success!', response.data.message, 'success');
                $("#manage-cap-modal").modal('hide');
              } else {
                swal('Error!', response.data.message, 'error');
              }
            });
        }

        

        // CUSTOM REUSABLE FUNCTIONS
        
        scope.formatMomentDate  = function(date, from, to){
          return moment(date, from).format(to);
        }
        scope.closeModal  = function(){
          $('.modal').modal('hide');
        }
        scope.range = function (range) {
          var arr = [];
          for (var i = 0; i < range; i++) {
            arr.push(i);
          }
          return arr;
        }
        scope.showLoading = function () {
          $(".circle-loader").fadeIn();
        };
        scope.hideLoading = function () {
          $timeout(function () {
            $(".circle-loader").fadeOut();
          }, 10);
        };

        scope.onLoad  = async function(){
          scope.showLoading();
          await scope.getEmployeeDetails();
          scope.hideLoading();
        }
        scope.onLoad();
      }
    }
  }
]);