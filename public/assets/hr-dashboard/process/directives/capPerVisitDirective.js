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

				// scope.showDataText = true;
				// scope.showInputText = false;

				scope.showDataText = [];
				scope.showInputText = [];

				// Notes:
				// - hide/show based on index
				// - ex.
				/*[
					{
						boolLabel: true,
						boolInput: true
					},
					{
						boolLabel: true,
						boolInput: true
					}
				]*/

				scope.gpCapPerVisitInfo = [
					{ id : 4, name : 'Filbert Tan', cap : 30.00 },
					{ id : 1, name : 'Sarah Lim', cap : 40.00 },
					{ id : 5, name : 'Calvin Lee', cap : 50.00 },
					{ id : 3, name : 'Kryss Kynn', cap : 20.00 },
					{ id : 9, name : 'Jeamar Libres', cap : 10.00 }
				];
				scope.indexInput = [];


				// Count total numbers, init
				for (let i = 0; i < scope.gpCapPerVisitInfo.length; i++) {
					scope.showDataText[i] = true;
					scope.showInputText[i] = false;
				}


				scope.fileUploadModal = function( emp ){
					scope.selected_emp = emp;
				}
				
				scope.closePass = function( ) {
					$('#file_upload').modal('hide');
				}

				/**
				 * Edit data based on its index
				 * 
				 * @params  int index
				 * @params  obj data
				 */
				scope.editTableCell = function ( index, data ) {
					console.log('row index: ' + index);
					$("button").removeClass("save-continue-disabled");
					scope.showDataText[index] = false
					scope.showInputText[index] = true
					scope.indexInput[index] = data.cap

					console.log('showDataText', scope.showDataText)
					console.log('showInputText', scope.showInputText)

					// if ( scope.showDataText[index] == true ) {
					// 	scope.showDataText[index] = false;
					// 	scope.showInputText[index] = true;
					// 	console.log('asdgjhasdasgdasgdjasgj');
						
					// } else {
					// 	scope.showDatatText = false;
					// }
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
