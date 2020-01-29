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
				scope.download_token = {};
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
				// scope.capPerVisitNoValue[index] = false;

				// Count total numbers, init

				scope.range = function(num) {
          var arr = [];
          for (var i = 0; i < num; i++) {
            arr.push(i);
          }
          return arr;
        };

        scope.showLoading = function(){
          $(".circle-loader").fadeIn();
        };

        scope.hideLoading = function(){
          setTimeout(function() {
            $(".circle-loader").fadeOut();
          }, 100);
        };

        // scope.showLoading = function( ){
        //   $( ".main-loader" ).fadeIn(); 
        // }
				
				scope.selectCapPage = 1;
        scope.selectCapPerPage = 10;
        scope.gpCapPerVisitInfo_pagination = {};
				scope.getGpCapPerVisit = function () {
					// scope.showLoading();
					$http.get(serverUrl.url + "/hr/employee_cap_per_visit_list?&page=" + scope.selectCapPage + '&per_page=' + scope.selectCapPerPage)
            .success(function(response) {
            	// scope.hideLoading();
              console.log(response);
              scope.gpCapPerVisitInfo = response.data;
              scope.gpCapPerVisitInfo_pagination = response;
              console.log(scope.gpCapPerVisitInfo_pagination);
              
              for (let i = 0; i < scope.gpCapPerVisitInfo.length; i++) {
              	scope.gpCapPerVisitInfo[i].index = i;
								scope.showDataText[i] = true;
								scope.showInputText[i] = false;
								scope.capPerVisitNoValue[i] = false;

								if (scope.gpCapPerVisitInfo[i].cap_amount == 0 || scope.gpCapPerVisitInfo[i].cap_amount == "0.00" || scope.gpCapPerVisitInfo[i].cap_amount == "" || scope.gpCapPerVisitInfo[i].cap_amount == null) {
									scope.capPerVisitNoValue[i] = true;
									scope.showDataText[i] = false;
									scope.showInputText[i] = false;
								} 
							}
            });
     				console.log('get cap per visit list');
				}

				scope.prevPageGpCap = function () {
          scope.selectCapPage -= 1;
          scope.getGpCapPerVisit();

          if (scope.selectCapPage == 0 ) {
            $('.prev-page-gp-cap').addClass('prev-disabled');
          }
        }

        scope.nextPageGpCap = function () {
          scope.selectCapPage += 1;
          scope.getGpCapPerVisit();
          $('.prev-page-gp-cap').removeClass('prev-disabled');
        }

        scope.goToGpCap = function (num) {
          scope.selectCapPage = num;
          scope.getGpCapPerVisit();
        }

        scope.changeGpPerPage = function( num ) {
          scope.selectCapPerPage = num;
          scope.selectCapPage = 1;
          $('.opened-per-page-scroll').toggle();
          scope.getGpCapPerVisit();  
        }

				scope.fileUploadModal = function( emp ){
					scope.selected_emp = emp;
				}
				
				scope.closePass = function( ) {
					$('#file_upload').modal('hide');
					scope.gpCapFile = {};
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

				scope.inputActiveSaveBtn = function () {
					$("button").removeClass("save-continue-disabled");
				}


				scope.editTableCell = function ( index, data ) {
					// console.log('row index: ' + index);
					data.cap_amount = data.cap_amount == "" ? "0.00" : parseFloat(data.cap_amount).toFixed(2);
          scope.showDataText[index] = false;
          scope.showInputText[index] = true;
           let hideMe = document.getElementById('hideMe');

					if ( scope.gpCapPerVisitInfo[index].cap_amount == 0 || scope.gpCapPerVisitInfo[index].cap_amount == "") {
						console.log( index + " " + scope.gpCapPerVisitInfo[index].cap_amount );
            scope.capPerVisitNoValue[index] = false;
            scope.showDataText[index] = false;
            scope.showInputText[index] = true;

            document.onclick = function(e) {
	          	console.log( 'hideMe' + index );

	          	if(e.target.id != 'hideMe' + index && e.target.id != 'hideMe') {
	          		var value = $( "#hideMe" + index ).val();
	          		console.log( value );
	          		if( parseInt( value ) == 0 || value == "" ){
	          			// console.log('click ni siya');
	          			$( "#hideMe" + index ).val("0.00");
	          			scope.gpCapPerVisitInfo[index].cap_amount = "0.00";
	          			scope.capPerVisitNoValue[index] = true;
		          		scope.showInputText[index] = false;
		          		scope.showDataText[index] = false;
		          		scope.$apply();
	          		}

	          		angular.forEach( scope.gpCapPerVisitInfo,function(value,key){
			          	if( value.cap_amount == 0 || value.cap_amount == "" ){
			          		value.cap_amount = "0.00";
			          		scope.capPerVisitNoValue[key] = true;
			          		scope.showInputText[key] = false;
			          		scope.showDataText[key] = false;
			          	}
			        	});
	          	}

	          	
	          	angular.forEach( scope.gpCapPerVisitInfo,function(value,key){
		          	if( value.cap_amount == "" ){
		          		value.cap_amount = "0.00";
		          	}
		        	});

	          	
	          }
          } 



					// console.log('showDataText', scope.showDataText)
					// console.log('showInputText', scope.showInputText)      
				}

				scope.getDownloadToken = function( ) {
          hrSettings.getDownloadToken( )
          .then(function(response){
            console.log(response);
            scope.download_token = response.data;
            console.log(scope.download_token);
          });
        }

        scope.downloadGpCapExcelTemplate = function () {
        	window.open(serverUrl.url + '/hr/download_employee_cap_per_visit?&token=' + window.localStorage.getItem('token'));
        }

        scope.gpCapFile = {
        	uploading : 0,
        };
        scope.uploadGpCapChanged = function( file ){
          file.uploading = 0;
          scope.gpCapFile = file; 
        }
        
        scope.uploadGpCapPerVisit = function ( file ) {
        	hrSettings.uploadCapExcel( { file : file } )
        		.then(function(response){
							console.log(response); 
							if( response.data.status == true){
                file.uploading = 100;
                setTimeout(function(){
                  $("#file_upload").modal("hide");
                  scope.gpCapFile = {};
                }, 1000);
                $("button").removeClass("save-continue-disabled");
                scope.getGpCapPerVisit();
              }else{
                file.uploading = 10;
                file.error = true;
                file.error_text = response.data.message;
              }
						},function (evt) {
              console.log( evt );
              var progressPercentage = parseInt(100.0 * evt.loaded / evt.total) - 20;
              file.uploading = progressPercentage;
            });
        }

				scope.saveBtn = function () {
					var err_ctr = 0;
					angular.forEach( scope.gpCapPerVisitInfo , function(value,key) {
						// console.log( value );
						var cap = {
							employee_id : value.user_id,
		          cap_amount : parseFloat(value.cap_amount).toFixed(2),
		        }
						$("button").addClass("save-continue-disabled");

						hrSettings.updateCapPerVisit( cap )
            .then(function(response){
              if( response.data.status ){
              	
              }else{
              	err_ctr += 1;
                swal( 'Error!', response.data.message, 'error' );
              }
            });

            if( scope.gpCapPerVisitInfo.length - 1 == key && err_ctr == 0 ){
            	$("button").addClass("save-continue-disabled");
            	scope.getGpCapPerVisit();
              swal( 'Success!', 'Cap updated', 'success' );
            }
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
        	scope.getDownloadToken();
        	scope.getTableCell();
        	scope.getGpCapPerVisit(); 
        }

        scope.onLoad();
			}
		}
	}
]);
