app.directive('dependentDetailsDirective', [
  '$timeout',
  'dependentsSettings',
  'hrSettings',
  '$stateParams',
  '$state',
  'removeDependentFactory',
  function directive($timeout, dependentsSettings, hrSettings, $stateParams, $state, removeDependentFactory) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log('dependentDetailsDirective as running!');
        scope.selected_member_id = $stateParams.member_id;
        scope.selected_emp_dependents = [];
        scope.selectedDependent = {};

        scope.getEmpDependents = function (id) {
          scope.showLoading();
          hrSettings.getDependents(id)
            .then(function (response) {
              // console.log(response);
              scope.hideLoading();
              scope.selected_emp_dependents = response.data.dependents;
            });
        }

        scope.openUpdateDependentModal = function (data) {
          if ( scope.get_permissions_data.edit_employee_dependent == 1 ) { 
            scope.initializeDatepickers();
            scope.selectedDependent = data;
            scope.selectedDependent.dob = scope.formatMomentDate(data.dob, null, 'DD/MM/YYYY');
            $("#update-dependent-modal").modal('show');
            $('.datepicker').datepicker('setDate', scope.selectedDependent.dob);
          } else {
            scope.edit_emp_text = true;
            scope.remove_text = false;
            $("#update-dependent-modal").modal('hide');
            $("#permission-modal").modal('show');
          }
          
        }

        scope.initializeDatepickers = function(){
          var dt = new Date();
          // dt.setFullYear(new Date().getFullYear()-18);
          $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            endDate: dt
          });
        }

        scope.saveDependent = function (data) {
          var dob = moment(data.dob, 'DD/MM/YYYY');
          var today = moment();
          if (dob.diff(today, 'days') <= 0) {

          } else {
            swal('Error!', 'Date of Birth is Invalid.', 'error');
            return false;
          }
          swal({
            title: "Confirm",
            text: "Are you sure you want to update this dependent?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0392CF",
            confirmButtonText: "Update",
            cancelButtonText: "No",
            closeOnConfirm: true,
            customClass: "updateEmp"
          },
            function (isConfirm) {
              if (isConfirm) {
                scope.showLoading();
                dependentsSettings.updateDependent(data)
                  .then(function (response) {
                    scope.hideLoading();
                    // console.log(response);
                    if (response.data.status) {
                      swal('Success!', response.data.message, 'success');
                      $("#update-dependent-modal").modal('hide');
                      scope.getEmpDependents(scope.selected_member_id);
                    } else {
                      swal('Error!', response.data.message, 'error');
                    }
                  });
              }
            });
        }

        scope.removeDependentBtn = function (data) {
          console.log(data);
          if ( scope.get_permissions_data.enroll_terminate_employee == 1 ) {
            removeDependentFactory.setEmployeeDetails(data);
            $state.go('dependent-remove.remove-emp-inputs', { member_id : scope.selected_member_id });
          } else {
            scope.remove_text = true;
            scope.edit_emp_text = false;
            $('#permission-modal').modal('show');
          }
        }

        scope.getPermissionsData = async function () {
          await hrSettings.getPermissions()
            .then( function (response) {
              console.log(response);
              scope.get_permissions_data = response.data.data;
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
          scope.getEmpDependents(scope.selected_member_id);
          await scope.getPermissionsData();
        }
        scope.onLoad();
      }
    }
  }
]);