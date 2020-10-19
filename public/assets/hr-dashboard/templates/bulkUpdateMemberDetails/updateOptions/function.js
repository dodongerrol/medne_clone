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