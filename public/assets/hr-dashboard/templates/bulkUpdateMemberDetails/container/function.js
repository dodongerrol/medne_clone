app.directive("bulkUpdateMemberDetailsDirective", [
  "$state",
  "hrSettings",
  "hrActivity",
  "$rootScope",
  "dependentsSettings",
  "$timeout",
  "serverUrl",
  function directive($state, hrSettings, hrActivity, $rootScope, dependentsSettings, $timeout, serverUrl) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("bulkUpdateMemberDetailsDirective Runnning !");

        scope.selected_account_types = [];
        scope.account_types = [
          'Employee',
          'Dependent'
        ];
        scope.details = [
          'Full Name',
          'Mobile Number',
          'Postal Code',
          'Locations & Department',
          'Work Email',
          'Date of Birth',
          'For Communication',
          'Bank Name & Bank Account Number',
          'Employee ID',
          'Benefits Start Date',
          'Relationship'
        ];


        scope.onLoad  = function(){

        }
        scope.onLoad()
        
      }
    }
  }
]);