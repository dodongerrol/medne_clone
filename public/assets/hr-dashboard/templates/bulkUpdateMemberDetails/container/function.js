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

        scope.account_types = [
          {
            name: 'Employee',
            selected: true,
          },
          {
            name: 'Dependent',
            selected: true,
          },
        ];
        // both emp and dep
        scope.details = [
          {
            name: 'Full Name',
            selected: true,
          },
          {
            name: 'Mobile Number',
            selected: false,
          },
          {
            name: 'Postal Code',
            selected: false,
          },
          {
            name: 'Locations & Department',
            selected: false,
          },
          {
            name: 'Work Email',
            selected: false,
          },
          {
            name: 'Date of Birth',
            selected: false,
          },
          {
            name: 'For Communication',
            selected: false,
          },
          {
            name: 'Bank Name & Bank Account Number',
            selected: false,
          },
          {
            name: 'Employee ID',
            selected: false,
          },
          {
            name: 'Benefits Start Date',
            selected: false,
          },
          {
            name: 'Relationship',
            selected: false,
          },
        ];
        // emp only
        scope.details_emp = [
          {
            name: 'Full Name',
            selected: true,
          },
          {
            name: 'Mobile Number',
            selected: false,
          },
          {
            name: 'Postal Code',
            selected: false,
          },
          {
            name: 'Locations & Department',
            selected: false,
          },
          {
            name: 'Work Email',
            selected: false,
          },
          {
            name: 'Date of Birth',
            selected: false,
          },
          {
            name: 'For Communication',
            selected: false,
          },
          {
            name: 'Bank Name & Bank Account Number',
            selected: false,
          },
          {
            name: 'Employee ID',
            selected: false,
          },
          {
            name: 'Benefits Start Date',
            selected: false,
          },
        ];
        // dep only
        scope.details_dep = [
          {
            name: 'Full Name',
            selected: true,
          },
          {
            name: 'Date of Birth',
            selected: false,
          },
          {
            name: 'Relationship',
            selected: false,
          },
        ];

        scope.onLoad  = function(){

        }
        scope.onLoad()
        
      }
    }
  }
]);