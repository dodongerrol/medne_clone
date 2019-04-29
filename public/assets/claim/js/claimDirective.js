app.directive("claimDirective", [
  "$http",
  "$state",
  function directive($http, $state) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("claimDirective running!");
        
        scope.verifyNRIC = function(){
          $('#modalNRIC').modal('show');
          console.log('clicked');
        }

        scope.manualClaim = function(){
          $('#modalManual').modal('show');
          console.log('clicked');
        }
      }
    };
  }
]);
