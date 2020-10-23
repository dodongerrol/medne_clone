app.directive("updateOptionDirective", [
  "$state",
  "hrSettings",
  "hrActivity",
  "$rootScope",
  "$timeout",
  "serverUrl",
  function directive($state, hrSettings, hrActivity, $rootScope, $timeout, serverUrl) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("updateOptionDirective Runnning !");

        scope.selected_account_types = ['Employee','Dependent']
        scope.selected_account_details = [
          'Full Name',
        ];

        scope.state = {
          isEmployee: true,
          isDependent: true
        }

        scope._selectedAccountType_ = async function (data) {

          // if (data.selected) {
          //   scope.selected_account_types.push(data.name);
          // } else {
          //   const index = scope.selected_account_types.indexOf(data.name);
          //   scope.selected_account_types.splice(index, 1);
          // }

          await scope.details.map((value, index) => {
            if (value.name == 'Full Name') {
              value.selected = true;
            } else {
              value.selected = false;
            }
          });
          await scope.details_emp.map((value, index) => {
            if (value.name == 'Full Name') {
              value.selected = true;
            } else {
              value.selected = false;
            }
          });
          await scope.details_dep.map((value, index) => {
            if (value.name == 'Full Name') {
              value.selected = true;
            } else {
              value.selected = false;
            }
          });

          scope.selected_account_details = ['Full Name'];
          
          if( data.name == 'Employee' && data.selected) {
            scope.state.isEmployee = data.selected;
            console.log(scope.state);
          } else if (data.name == 'Employee' && !data.selected) {
            scope.state.isEmployee = data.selected;
            console.log(scope.state);
          }
          if (data.name == 'Dependent' && data.selected) {
            scope.state.isDependent = data.selected;
            console.log(scope.state);
          } else if (data.name == 'Dependent' && !data.selected) {
            scope.state.isDependent = data.selected;
            console.log(scope.state);
          }
        }

        scope._selectedAccountDetails_ = function (data) {

          if (data.selected) {
            scope.selected_account_details.push(data.name);
          } else {
            const index = scope.selected_account_details.indexOf(data.name);
            scope.selected_account_details.splice(index, 1);
          }
          console.log(scope.selected_account_details);
        }

        scope._selectDeselectAll_ = function (type) {
          if (type == 1) {
            if (scope.state.isEmployee && scope.state.isDependent) {
              scope.details.map((value, index) => {
                if (value.name == 'Full Name') {
                  value.selected = true;
                } else if (value.selected == false) {
                  value.selected = true;
                  scope.selected_account_details.push(value.name);
                }
              });
            } else if (scope.state.isEmployee && !scope.state.isDependent) {
              scope.details_emp.map((value, index) => {
                if (value.name == 'Full Name') {
                  value.selected = true;
                } else if (value.selected == false){
                  value.selected = true;
                  scope.selected_account_details.push(value.name);
                }
              });
            } else if (!scope.state.isEmployee && scope.state.isDependent) {
              scope.details_dep.map((value, index) => {
                if (value.name == 'Full Name') {
                  value.selected = true;
                } else if (value.selected == false){
                  value.selected = true;
                  scope.selected_account_details.push(value.name);
                }
              });
            }

            console.log(scope.selected_account_details);
          } else {
            if (scope.state.isEmployee && scope.state.isDependent) {
              scope.details.map((value, index) => {
                if (value.name == 'Full Name') {
                  value.selected = true;
                } else {
                  value.selected = false;
                  scope.selected_account_details = ['Full Name'];
                }
              });
            } else if (scope.state.isEmployee && !scope.state.isDependent) {
              scope.details_emp.map((value, index) => {
                if (value.name == 'Full Name') {
                  value.selected = true;
                } else {
                  value.selected = false;
                  scope.selected_account_details = ['Full Name'];
                }
              });
            } else if (!scope.state.isEmployee && scope.state.isDependent) {
              scope.details_dep.map((value, index) => {
                if (value.name == 'Full Name') {
                  value.selected = true;
                } else {
                  value.selected = false;
                  scope.selected_account_details = ['Full Name'];
                }
              });
            }

            console.log(scope.selected_account_details);
          }
        }

        scope.nextBtn = function(){
          $state.go('bulk-update-member-details.excel-import');
        }

        scope.onLoad  = function(){

        }
        scope.onLoad()
        
      }
    }
  }
]);