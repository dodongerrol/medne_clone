app.directive('capPerVisitDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("capPerVisitDirective Runnning !");
				scope.showDataText = true;
				scope.showInputText = false;

				scope.fileUploadModal = function( emp ){
					scope.selected_emp = emp;
				}
				
				scope.closePass = function( ) {
					$('#file_upload').modal('hide');
				}

				scope.editTableCell = function () {
					if ( scope.showDataText == true ) {
						scope.showDataText = false;
						scope.showInputText = true;
						 $("button").removeClass("save-continue-disabled");
					} else {
						scope.showDatatText = false;
					}
				}

				scope.showPageScroll = function() {
					$(".opened-per-page-scroll").show();

					$("body").click(function(e){ 
            if ($(e.target).parents(".rows-page-wrapper").length === 0) {
              $(".opened-per-page-scroll").hide();
            }
          });  
				}
       
        scope.onLoad = function( ){
        		        
        }

        scope.onLoad();
			}
		}
	}
]);
