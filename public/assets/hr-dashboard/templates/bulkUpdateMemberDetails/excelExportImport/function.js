app.directive("updateExcelImportDirective", [
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
        console.log("updateExcelImportDirective Runnning !");

        scope.backBtn = function(){
          $state.go('bulk-update-member-details.update-options');
        }
        scope.nextBtn = function(){
          $state.go('bulk-update-member-details.summary-preview');
        }

        scope.onLoad  = function(){

        }
        scope.onLoad()
        
      }
    }
  }
]);