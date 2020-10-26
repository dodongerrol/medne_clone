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
        scope.isDisabled = true;
        scope.selected_account_types = [];
        scope.account_types = {
          employee: true,
          dependent: true,
        }
        scope.details = [
          {
            name: 'Full Name',
            selected: true
          },
          {
            name: 'Mobile Number',
            selected: false
          },
          {
            name: 'Postal Code',
            selected: false
          },
          {
            name: 'Locations & Department',
            selected: false
          },
          {
            name: 'Work Email',
            selected: false
          },
          {
            name: 'Date of Birth',
            selected: false
          },
          {
            name: 'For Communication',
            selected: false
          },
          {
            name: 'Bank Name & Bank Account Number',
            selected: false
          },
          {
            name: 'Employee ID',
            selected: false
          },
          {
            name: 'Benefits Start Date',
            selected: false
          },
          {
            name: 'Relationship',
            selected: false
          },
        ];
        
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