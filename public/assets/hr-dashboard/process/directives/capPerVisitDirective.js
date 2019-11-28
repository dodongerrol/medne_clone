app.directive('capPerVisitDirective', [
	'$http',
	'serverUrl',
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($http,serverUrl,$state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("capPerVisitDirective Runnning !");

				// scope.showDataText = true;
				// scope.showInputText = false;

				scope.showDataText = [];
				scope.showInputText = [];
				scope.capPerVisitNoValue = [];
				scope.not_applicable = [];
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
				//http://medicloud.local/hr/employee_cap_per_visit_list?per_page=5
				// scope.gpCapPerVisitInfo = [
				// 	{ id : 4, name : 'Filbert Tan', cap : 30.00 },
				// 	{ id : 1, name : 'Sarah Lim', cap : 40.00 },
				// 	{ id : 5, name : 'Calvin Lee', cap : 50.00 },
				// 	{ id : 3, name : 'Kryss Kynn', cap : 20.00 },
				// 	{ id : 9, name : 'Jeamar Libres', cap : 10.00 },
				// 	{ id : 9, name : 'Kintoy Salado', cap : 0 },
				// ];
				scope.indexInput = [];
				scope.per_page_arr = [10,20,30,40,50,100];
				// scope.capPerVisitNoValue[index] = false;

				// Count total numbers, init
				

				scope.getGpCapPerVisit = function () {
					$http.get(serverUrl.url + "/hr/employee_cap_per_visit_list?per_page=5&page=1")
            .success(function(response) {
              console.log(response);
              scope.gpCapPerVisitInfo = response.data;
              
              for (let i = 0; i < scope.gpCapPerVisitInfo.length; i++) {
								scope.showDataText[i] = true;
								scope.showInputText[i] = false;
								console.log(scope.gpCapPerVisitInfo[i].cap);
								scope.capPerVisitNoValue[i] = false;

								if (scope.gpCapPerVisitInfo[i].cap == 0) {
									scope.capPerVisitNoValue[i] = true;
									scope.showDataText[i] = false;
									scope.showInputText[i] = false;
								} 
							}
            });
     				console.log('get cap per visit list');
				}

				scope.gpCapAddFile = function ( file ) {
					console.log('add file');
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

				scope.getTableCell = function ( index ) {
					data = scope.gpCapPerVisitInfo;
					// scope.not_applicable[index] = data[5].cap;
					
				}


				scope.editTableCell = function ( index, data ) {
					console.log('row index: ' + index);
					$("button").removeClass("save-continue-disabled");
					scope.showDataText[index] = false;
					scope.showInputText[index] = true;
					// scope.capPerVisitNoValue[index] = true;
					scope.indexInput[index] = data.cap;

					if ( scope.indexInput[index] == 0 ) {
						console.log('no value');
						scope.capPerVisitNoValue[index] = false;
						scope.showDataText[index] = false;
						scope.showInputText[index] = true;
					} 
					// console.log('showDataText', scope.showDataText)
					// console.log('showInputText', scope.showInputText)
					console.log(scope.indexInput[index]);
				}

				scope.saveBtn = function () {
					angular.forEach( scope.gpCapPerVisitInfo , function(value,key) {
						console.log( value );
						var cap = {
							employee_id : value.user_id,
		          cap_amount : value.cap_amount,
		        }
		        console.log(cap);
						scope.showDataText[key] = true;
						scope.showInputText[key] = false;
						$("button").addClass("save-continue-disabled");

						if (value.cap === 0) {
							scope.showDataText[key] = false;
							scope.showInputText[key] = false;
						} 
		        
						hrSettings.updateCapPerVisit( cap )
            .then(function(response){
            	console.log(response);
              if( response.data.status ){
                swal( 'Success!', response.data.message, 'success' );
              }else{
                swal( 'Error!', response.data.message, 'error' );
              }
            });
					});
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
        	scope.getTableCell();
        	scope.getGpCapPerVisit();
        	data = scope.gpCapPerVisitInfo;
        	console.log(data);        
        }

        scope.onLoad();
			}
		}
	}
]);
