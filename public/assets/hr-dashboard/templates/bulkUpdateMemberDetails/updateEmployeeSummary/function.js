app.directive("updateExcelSummaryDirective", [
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
        console.log("updateExcelSummaryDirective Runnning !");

        scope.page_active = 1;
        scope.page_ctr = 5;

        scope.nextBtn = function(){
          console.log('asdfasdfa');
        }

        scope.onLoad  = function(){
          
        }
        scope.onLoad()
        
      }
    }
  }
]);