app.directive('outOfPocketDirective', [
	'$state',
  '$location',
  'hrSettings',
	function directive($state,$location,hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("out of pocket directive Runnning !");
				console.log($location);

        scope.showLastTermSelector = false;
        

        scope.termSelector = function () {
          scope.showLastTermSelector = scope.showLastTermSelector ? false : true;
        }

        scope.formatDate = function (date) {
          return moment(new Date(date)).format("DD/MM/YYYY");
        };

        scope.getDateTerms = function () {
          hrSettings.fetchDateTerms()
          .then(function(response){
            scope.dateTerm = response.data.data;
            console.log(scope.dateTerm);

            scope.currentTerm = scope.dateTerm.slice(-1).pop();
            console.log(scope.currentTerm );

          })
        }
        


        var loading_trap = false;

        scope.toggleOff = function( ) {
          $( ".circle-loader" ).fadeOut();
          loading_trap = false;
        }

        scope.toggleLoading = function( ){
          if ( loading_trap == false ) {
            $( ".circle-loader" ).fadeIn(); 
            loading_trap = true;
          }else{
            setTimeout(function() {
              $( ".circle-loader" ).fadeOut();
              loading_trap = false;
            }, 100)
          }
        }

       
        scope.onLoad = function () {
          scope.getDateTerms();
        }

        scope.onLoad();
				
			}
		}
	}
]);
