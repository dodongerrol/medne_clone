app.directive("mobileExerciseDirective", [
  "$http",
  "$state",
  "$timeout",
  function directive($http, $state, $timeout) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("mobileExerciseDirective running!");
        
        
        scope.onLoad = function (){
          
        }

        scope.onLoad();
      }
    };
  }
]);
