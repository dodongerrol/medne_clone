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

        scope.onLoad  = function(){

        }
        scope.onLoad()
        
      }
    }
  }
]);