app.directive("administratorsDirective", [
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
        console.log("administratorsssDirective Runnning !");
        scope.permissionSelector = false;
        scope.chooseSelectorLocation = false;
        scope.chooseSelectorDepartment = false;
        scope.permission_data = 'All Employees & Dependents';
        scope.locations_data = [
          {
            location: 'Location '
          },
          {
            location: 'Location 1'
          },
          {
            location: 'Location 2'
          },
          {
            location: 'Location 3'
          },
          {
            location: 'Location 4'
          },
          {
            location: 'Location 5'
          },
          {
            location: 'Location 6'
          },
          {
            location: 'Location 7'
          },
          {
            location: 'Location 8'
          },
          {
            location: 'Location 9'
          },
        ];

        scope.adminPermission = function () {
          console.log('test');
          scope.permissionSelector = scope.permissionSelector == true ? false : true;
          console.log(scope.locations_data);
        }

        scope.permissionSelectorData = function ( type ) {
          scope.permissionSelector = false;

          if ( type == 'locations' ) {
            scope.permission_data = 'Locations';
          }
          if ( type == 'departments' ) {
            scope.permission_data = 'Departments';
          }
          console.log(type);
        }

        scope.chooseSelector = function ( type ) {
          if ( type == 'locations' ) {
            scope.chooseSelectorLocation = scope.chooseSelectorLocation == true ? false : true;
          }
          if ( type == 'departments' ) {
            scope.chooseSelectorDepartment = scope.chooseSelectorDepartment == true ? false : true;
          }
        }

        scope.formatMomentDate  = function(date, from, to){
          return moment(date, from).format(to);
        }
        scope.closeModal  = function(){
          $('.modal').modal('hide');
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
          
        }
        scope.onLoad();
      }
    }
  }
]);