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
        scope.selectIsDisabled = true;
        scope.selectAll = ()=>{
          scope.details.forEach( item => {
            if (item.selected == false) {
              console.log(scope.selected_account_types)
              scope.selected_account_types.push(item);
              item.selected = true;
              scope.isDisabled = false;
            }
          });
        }
        scope.deselectAll = ()=>{
          scope.details.forEach( item => {
            if (item.selected == true && item.name != 'Full Name') {
                item.selected = false;
                scope.isDisabled = true;
                scope.selected_account_types = [];
            }
          });
        }

        scope.check = (list)=>{
        if (list.selected == true) {
          scope.isDisabled = false;
          scope.selected_account_types.push(list.name);
        }else if(list.selected == false){
          scope.selected_account_types.splice(list.name, 1);
        }
        if(scope.selected_account_types.length == 0){
          scope.selected_account_types = [];
          scope.isDisabled = true;
        }
        }

        scope.checkCheckbox = () =>{
         if (scope.account_types.employee == false && scope.account_types.dependent == false) {
          scope.isDisabled = true;
          scope.selected_account_types = [];
          scope.details.forEach(item => {
            item.selected = false;
          });
         }
         else{
          scope.details.forEach(item => {
           if (item.name == "Full Name") {
              item.selected = true;
           }
          });
          // scope.isDisabled = true;
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