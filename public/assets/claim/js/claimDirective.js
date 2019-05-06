app.directive("claimDirective", [
  "$http",
  "$state",
  function directive($http, $state) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("claimDirective running!");
        
        scope.clinic = {};
        scope.backdate_list = {};
        scope.add_claim_data = {
          amount : 0,
          selected_service_ids : [],
          selected_service : [],
          daytime : 'AM',
          visit_time : moment().format('hh:mm'),
          visit_date : moment().format('DD MMM, YYYY'),
        };
        scope.claim_list = [];
        scope.service_list = [];
        scope.services = [];
        scope.users_arr = [];
        scope.placeholder = "";
        scope.search_member = "";
        scope.selected_start_date = moment().startOf('month').format('MM/DD/YYYY');
        scope.selected_end_date = moment().format('MM/DD/YYYY');
        scope.isSearchNRIC = false;
        scope.selected_hour = parseInt(moment().format("hh"));
        scope.selected_minute = parseInt(moment().format("mm"));

        scope.verifyNRIC = function(){
          $('#modalNRIC').modal('show');
        }
        scope.manualClaim = function(){
          $('#modalManual').modal('show');
          scope.add_claim_data = {
            amount : 0,
            selected_service_ids : [],
            selected_service : [],
            daytime : 'AM',
            visit_time : moment().format('hh:mm'),
            visit_date : moment().format('DD MMM, YYYY'),
          };
        }
        scope.showServiceDrop = function(){
          $(".service-drop").fadeIn();
        }
        scope.selectNRIC = function( data ){
          scope.add_claim_data.selected_nric_data = data;
          scope.add_claim_data.nric = data.nric;
          scope.isSearchNRIC = false;
          console.log( scope.add_claim_data );
        }
        scope.selectService = function( data ){
          $(".service-drop").hide();
          if( $.inArray( data.name, scope.add_claim_data.selected_service ) < 0 ){
            data.selected = true;
            scope.add_claim_data.selected_service_ids.push( data.id );
            scope.add_claim_data.selected_service.push( data.name );
          }
        }
        scope.removeService = function( data ){
          var index = $.inArray( data.name, scope.add_claim_data.selected_service );
          scope.add_claim_data.selected_service_ids.splice( index, 1 );
          scope.add_claim_data.selected_service.splice( index, 1 );
          data.selected = false;
        }
        scope.showTimePicker = function(){
          $(".timepicker-container").fadeIn();
          $(".daytime-container").hide();
          scope.setVisitTime();
        }
        scope.showTimeDayDrop = function(){
          $(".daytime-container").fadeIn();
          $(".timepicker-container").hide();
        }
        scope.pickDayTime = function( opt ){
          scope.add_claim_data.daytime = opt;
          $(".daytime-container").hide();
        }
        scope.addHour = function( opt ) {
          if (scope.selected_hour < 12) {
            scope.selected_hour++;
          } else {
            scope.selected_hour = 1;
          }
          scope.setVisitTime( opt );
        };
        scope.deductHour = function( opt ){
          if (scope.selected_hour > 1) {
            scope.selected_hour--;
          } else {
            scope.selected_hour = 12;
          }
          scope.setVisitTime( opt );
        };
        scope.addMinute = function( opt ){
          if (scope.selected_minute < 59) {
            scope.selected_minute++;
          } else {
            scope.selected_minute = 0;
          }
          scope.setVisitTime(opt);
        };
        scope.deductMinute = function( opt ){
          if (scope.selected_minute > 0) {
            scope.selected_minute--;
          } else {
            scope.selected_minute = 59;
          }
          scope.setVisitTime(opt);
        };
        scope.setVisitTime = function( opt ){
          var hour = "" + (scope.selected_hour < 10 ? 0 : "") + scope.selected_hour + ":" + (scope.selected_minute < 10 ? 0 : "") + scope.selected_minute;
          scope.add_claim_data.visit_time = hour;
        };
        scope.checkClaimForm = function( data ) {
          if (!data.selected_nric_data) {
            swal("Ooops!", "Please select Name from the NRIC drop down result.", "error");
            return false;
          }
          if (!data.visit_date) {
            swal("Ooops!", "Please select the visit date.", "error");
            return false;
          }
          if (data.selected_service_ids.length == 0) {
            swal("Ooops!","Please select a procedure/service.","error");
            return false;
          }
          if(!data.visit_time) {
            swal("Ooops!", "Please enter time of transaction.", "error");
            return false;
          }
          if (data.amount < 0) {
            swal("Ooops!", "Negative values are not allowed.", "error");
            return false;
          }

          return true;
        }



        // === REQUESTS === //
          scope.addClaim = function( ) {
            console.log( scope.add_claim_data );
            if( scope.checkClaimForm( scope.add_claim_data ) == true ){
              data.currency_type = scope.clinic.currency_type;
              swal({
                  title: "Are you sure?",
                  text: "This transaction data will be save.",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "Yes!",
                  cancelButtonText: "Cancel",
                  closeOnConfirm: true,
                  closeOnCancel: true
                },
                function(isConfirm) {
                  if (isConfirm) {
                    $http.post(base_url + "clinic/save/claim/transaction", data)
                      .success(function(response) {
                        if(!response.status) {
                          swal("Oooops!", response.message, "error");
                        } else {
                          swal("Success!","The Transaction Successfully Saved.","success");
                          $('#modalManual').modal('hide');
                        }
                      });
                  }
                }
              );
            }
          }
          scope.checkClaim = function(){
            $( '#check-claim-modal' ).modal( 'show' );
            $('.isNotDoneChecking').show();
            $('.isDoneChecking').hide();
            var data = {
              user_id: scope.add_claim_data.selected_nric_data.id,
              date_transaction : moment( scope.add_claim_data.visit_date ).format('YYYY-MM-DD'),
              amount : scope.add_claim_data.amount,
            }
            console.log( data );
            $http.post(base_url + "check_duplicate_transaction", data)
            .then(function(response) {
              // console.log(response);
              if( response.data.status == true && response.data.error == 0){
                scope.your_transaction = response.data.new_transaction;
                scope.other_transaction = response.data.duplicates;
                $('.isNotDoneChecking').hide();
                $('.isDoneChecking').show();
              }else{
                $( '#check-claim-modal' ).modal( 'hide' );
                scope.addClaim();
              }
            })
            .catch(function(err){
              $('#check-claim-modal').modal('hide');
            });
          }
          scope.getServices = function() {
            $http.get(base_url + "clinic/get/services")
              .success(function(response) {
                scope.service_list = response;
                scope.services = response;
                // console.log(response);
              });
          };
          scope.getClinicDetails = function() {
            $http.get(base_url + "clinic/details")
              .success(function(response) {
                scope.clinic = response.clinic;
                if(scope.clinic.currency_type == "myr") {
                  scope.placeholder = "Enter Amount in MYR";
                } else {
                  scope.placeholder = "Enter Amount in SGD";
                }
                // var stored_list = localStorageService.get("trans_table_" + scope.clinic.ClinicID);
                // if ( stored_list != null ) {
                //   angular.forEach(stored_list, function(value, key) {
                //     scope.claim_list.push( value );
                //   })
                // }
              });
          };
          scope.searchByNric = function() {
            if (scope.search.length > 5) {
              var data = {
                nric: scope.searchNRIC,
                start_date: scope.selected_start_date,
                end_date: scope.selected_end_date,
              }
              $http.post(base_url + "clinic/search_by_nric_transactions", data)
                .success(function(response) {
                  scope.backdate_list = response;
                  scope.toggleBackLoading();
                });
            }
          };
          scope.getHeathProvider = function() {
            $http.get(base_url + "clinic/get/health_provider/transaction")
              .success(function(response) {
                angular.forEach(response, function(value, key) {
                  // console.log( value );
                  var procedures = [];
                  angular.forEach( value.procedure_ids, function(value2, key){ 
                    $http.get( base_url + "clinic/get/service/details/" + value2 )
                      .then(function(response){
                        procedures.push( response.data );
                      });
                  });
                  $http.get( base_url + "clinic/get/user/details/" + value.user_id )
                    .then(function(response){
                      var data = {
                        name: response.data[0].Name,
                        nric: value.nric,
                        procedures: procedures,
                        procedure: value.procedure,
                        display_book_date: value.date,
                        book_date: moment(value.date).format("YYYY-MM-DD"),
                        id: value.user_id,
                        amount: value.amount,
                        user_type: value.user_type,
                        access_type: value.access_type,
                        back_date: 0,
                        health_provider: 1,
                        multiple_procedures: value.multiple_procedures,
                        procedure_ids: value.procedure_ids,
                        transaction_id: value.transaction_id
                      };
                      var check_exist = 0;
                      if( scope.claim_list.length > 0 ){
                        angular.forEach(scope.claim_list, function(value2, key2) {
                          if( value2.transaction_id == data.transaction_id ){
                            check_exist = 1;
                          }
                          if( key2 == (scope.claim_list.length-1) && check_exist == 0 ){
                            scope.claim_list.push(data);
                          }
                        });
                      }else{
                        scope.claim_list.push(data);
                      }
                    });
                  
                });
              });
          };
          scope.getPusherConfig = function(connection) {
            // console.log('connection', connection);
            // socket.on(connection, function (data) {
            //   console.log(data);
            //   if (parseInt(data.clinic_id) == parseInt(scope.clinic.ClinicID)) {
            //         // check if transaction is already push to the array of claim_list
            //       if (!scope.checkDataClaimLists(data.transaction_id)) {
            //         $http.get(base_url + "clinic/transaction_specific?transaction_id=" + data.transaction_id)
            //           .success(function(response) {
            //             setTimeout(function() {
            //               scope.load_status = true;
            //             }, 100);
            //             var procedures = [];
            //             angular.forEach( response.procedure_ids, function(value, key){ 
            //               $http.get( base_url + "clinic/get/service/details/" + value )
            //                 .then(function(response){
            //                   procedures.push( response.data );
            //                 });
            //             });
            //             var data = {
            //               nric: response.NRIC,
            //               procedure: response.ProcedureID,
            //               procedures: procedures,
            //               display_book_date: response.date_of_transaction,
            //               book_date: moment( response.date_of_transaction ).format("YYYY-MM-DD"),
            //               id: response.UserID,
            //               amount: response.procedure_cost,
            //               back_date: 0,
            //               health_provider: response.health_provider,
            //               multiple_procedures: response.multiple_procedures,
            //               procedure_ids: response.procedure_ids,
            //               transaction_id: response.transaction_id
            //             };
            //             $http.get( base_url + "clinic/get/user/details/" + response.UserID )
            //               .then(function(response2){
            //                 data.name = response2.data[0].Name;
            //                 data.user_type = response2.data[0].UserType;
            //                 data.access_type = response2.data[0].access_type;
            //                 scope.claim_list.push(data);
            //               });
            //           });
            //       }
            //     }
            // });
          };
          scope.getClinicSocketConnection = function( ) {
            $http.get(base_url + 'clinic_socket_connection')
            .then(function(response){
              if(response.data.status) {
                scope.getPusherConfig(response.data.socket_connection);
              }
            });
          };
          scope.getAllUsers = function( data ) {
            if( data.length >= 2 ){
              $http.get(base_url + "clinic/get/all/users?q=" + data)
                .success(function(response) {
                  console.log( response );
                  scope.users_arr = response.items;
                  scope.isSearchNRIC = true;
                });
            }else{
              scope.isSearchNRIC = false;
            }
          };
          scope.getSuccessfullTransactions = function() {
            $http.get(base_url + "clinic/all_transactions")
              .success(function(response) {
                console.log( response );
                scope.backdate_list = response;
              });
          };
        // ================ //

        scope.initializeDatePickers = function(){
          setTimeout(function() {
            $('.datepicker').datepicker({
              format: "dd MM, yyyy",
              maxDate: new Date()
            });

            $('.start-datepicker').datepicker({
              format: "mm/dd/yyyy",
              maxDate: new Date()
            });

            $('.end-datepicker').datepicker({
              format: "mm/dd/yyyy",
              maxDate: new Date()
            });
          }, 500);
        }

        scope.onLoad = function (){
          scope.getClinicDetails();
          // scope.getClinicSocketConnection();
          // scope.getHeathProvider();
          scope.getSuccessfullTransactions();
          scope.getServices();
          scope.initializeDatePickers();
        }

        scope.onLoad();


        // ===== JQUERY ===== //
          $('.modal').on('hide.bs.modal', function () {
            scope.users_arr = [];
            scope.search_member = "";
          })
          $("body").click(function(e){
            if ( $(e.target).parents(".service-td").length === 0) {
              $(".service-drop").hide();
            }
            if ( $(e.target).parents(".datepicker-td").length === 0) {
              $(".timepicker-container").hide();
              $(".daytime-container").hide();
            }
          });
        // ================== //


        
      }
    };
  }
]);
