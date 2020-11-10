app.directive("claimDirective", [
  "$http",
  "$state",
  "socket",
  "$timeout",
  function directive($http, $state, socket, $timeout) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("claimDirective running!");

        scope.clinic = {};
        scope.backdate_list = {};
        scope.add_claim_data = {
          amount: 0,
          selected_service_ids: [],
          selected_service: [],
          daytime: "AM",
          visit_time: moment().format("hh:mm"),
          visit_date: moment().format("DD MMM, YYYY"),
        };
        scope.claim_list = [];
        scope.service_list = [];
        scope.services = [];
        scope.users_arr = [];
        scope.users_nric_arr = [];
        scope.placeholder = "";
        scope.search_member = "";
        scope.selected_start_date = moment()
          .startOf("month")
          .format("MM/DD/YYYY");
        scope.selected_end_date = moment().endOf("month").format("MM/DD/YYYY");
        scope.isSearchNRIC = false;
        scope.selected_hour = parseInt(moment().format("hh"));
        scope.selected_minute = parseInt(moment().format("mm"));
        scope.searchTrans_text = "";
        scope.selected_submit_data = {};
        scope.e_card_data = {};
        scope.registration_arr = [];
        scope.isLoading = false;
        scope.currencyType = "";

        scope.verifyNRIC = function () {
          $("#modalNRIC").modal("show");
        };
        scope.manualClaim = function () {
          $("#modalManual").modal("show");
          scope.e_card_data = {};
          scope.add_claim_data = {
            amount: 0,
            selected_service_ids: [],
            selected_service: [],
            daytime: "AM",
            visit_time: moment().format("hh:mm"),
            visit_date: moment().format("DD MMM, YYYY"),
          };
        };
        scope.showServiceDrop = function () {
          $(".service-drop").fadeIn();
        };
        scope.selectNRIC = function (data) {
          scope.add_claim_data.selected_nric_data = data;
          scope.add_claim_data.nric = data.nric;
          scope.isSearchNRIC = false;
          $http
            .get(base_url + "clinic/get/special_user/details/" + data.id)
            .success(function (response) {
              console.log(response);
              if (response.public_user && response.no_data) {
              } else {
                scope.e_card_data = response;
                $(".datepicker").datepicker(
                  "setStartDate",
                  moment(scope.e_card_data.valid_start_claim).format(
                    "MM/DD/YYYY"
                  )
                );
                // $('.datepicker').datepicker('setEndDate', moment(scope.e_card_data.valid_end_claim).format('MM/DD/YYYY') );
                $("#e-card-modal").modal("show");
              }
            });
        };
        scope.selectService = function (data) {
          $(".service-drop").hide();
          if ($.inArray(data.name, scope.add_claim_data.selected_service) < 0) {
            data.selected = true;
            scope.add_claim_data.selected_service_ids.push(data.id);
            scope.add_claim_data.selected_service.push(data.name);
          }
        };
        scope.removeService = function (data) {
          var index = $.inArray(
            data.name,
            scope.add_claim_data.selected_service
          );
          scope.add_claim_data.selected_service_ids.splice(index, 1);
          scope.add_claim_data.selected_service.splice(index, 1);
          data.selected = false;
        };
        scope.showTimePicker = function () {
          $(".timepicker-container").fadeIn();
          $(".daytime-container").hide();
          scope.setVisitTime();
        };
        scope.showTimeDayDrop = function () {
          $(".daytime-container").fadeIn();
          $(".timepicker-container").hide();
        };
        scope.pickDayTime = function (opt) {
          scope.add_claim_data.daytime = opt;
          $(".daytime-container").hide();
        };
        scope.addHour = function (opt) {
          if (scope.selected_hour < 12) {
            scope.selected_hour++;
          } else {
            scope.selected_hour = 1;
          }
          scope.setVisitTime(opt);
        };
        scope.deductHour = function (opt) {
          if (scope.selected_hour > 1) {
            scope.selected_hour--;
          } else {
            scope.selected_hour = 12;
          }
          scope.setVisitTime(opt);
        };
        scope.addMinute = function (opt) {
          if (scope.selected_minute < 59) {
            scope.selected_minute++;
          } else {
            scope.selected_minute = 0;
          }
          scope.setVisitTime(opt);
        };
        scope.deductMinute = function (opt) {
          if (scope.selected_minute > 0) {
            scope.selected_minute--;
          } else {
            scope.selected_minute = 59;
          }
          scope.setVisitTime(opt);
        };
        scope.setVisitTime = function (opt) {
          var hour =
            "" +
            (scope.selected_hour < 10 ? 0 : "") +
            scope.selected_hour +
            ":" +
            (scope.selected_minute < 10 ? 0 : "") +
            scope.selected_minute;
          scope.add_claim_data.visit_time = hour;
        };
        scope.checkClaimForm = function (data) {
          if (!data.selected_nric_data) {
            swal(
              "Ooops!",
              "Please select Name from the NRIC drop down result.",
              "error"
            );
            return false;
          }
          if (!data.visit_date) {
            swal("Ooops!", "Please select the visit date.", "error");
            return false;
          }
          if (data.selected_service_ids.length == 0) {
            swal("Ooops!", "Please select a procedure/service.", "error");
            return false;
          }
          if (!data.visit_time) {
            swal("Ooops!", "Please enter time of transaction.", "error");
            return false;
          }
          if (data.amount < 0) {
            swal("Ooops!", "Negative values are not allowed.", "error");
            return false;
          }

          return true;
        };
        scope.toggleClaimSummaryModal = function (data, index) {
          // console.log( data );
          scope.selected_submit_data = data;
          scope.selected_submit_data.index = index;
          $("#summary-claim-modal").modal("show");
        };

        // === REQUESTS === //
        scope.cancelRegistrationData = function (data) {
          swal(
            {
              title: "Are you sure?",
              text: "This will be removed.",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes!",
              cancelButtonText: "Cancel",
              closeOnConfirm: true,
              closeOnCancel: true,
            },
            function (isConfirm) {
              if (isConfirm) {
                scope.showLoading();
                $http
                  .post(base_url + "clinic/remove_specific_check_in", data)
                  .success(function (response) {
                    scope.hideLoading();
                    if (response.status) {
                      swal("Success!", response.message, "success");
                      scope.getClinicCheckIns();
                    } else {
                      swal("Ooops!", response.message, "error");
                    }
                  });
              }
            }
          );
        };
        scope.cancelBackDateTransaction = function (data) {
          swal(
            {
              title: "Are you sure?",
              text: "This transaction data will be removed.",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes!",
              cancelButtonText: "Cancel",
              closeOnConfirm: true,
              closeOnCancel: true,
            },
            function (isConfirm) {
              if (isConfirm) {
                scope.showLoading();
                $http
                  .get(
                    base_url + "clinic/remove/transaction?id=" + data.trans_id
                  )
                  .success(function (response) {
                    scope.hideLoading();
                    if (response.status) {
                      swal("Success!", response.message, "success");
                      scope.getSuccessfullTransactions();
                    } else {
                      swal("Ooops!", response.message, "error");
                    }
                  });
              }
            }
          );
        };
        scope.removeTransPreview = function (data) {
          swal(
            {
              title: "Confirm",
              text: "Are you sure you want to delete this transaction?",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes",
              cancelButtonText: "Cancel",
              closeOnConfirm: true,
              closeOnCancel: true,
            },
            function (isConfirm) {
              if (isConfirm) {
                // delete transaction
                $http
                  .post(base_url + "clinic/delete_transaction", {
                    transaction_id: data.transaction_id,
                  })
                  .success(function (response) {
                    swal("Success!", response.message, "success");
                    scope.claim_list.splice(scope.claim_list.indexOf(data), 1);
                  });
              }
            }
          );
        };
        scope.submitSummaryClaimData = function (data) {
          console.log(data);
          swal(
            {
              title: "Are you sure?",
              text: "This transaction data will be saved.",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes!",
              cancelButtonText: "Cancel",
              closeOnConfirm: true,
              closeOnCancel: true,
            },
            function (isConfirm) {
              if (isConfirm) {
                data.currency_type = scope.clinic.currency_type;
                // data.currency_amount = (data.currency_type == 'sgd') ? data.amount : data.amount * 3;
                data.currency_amount = scope.clinic.currency_amount;
                scope.showLoading();
                $http
                  .post(base_url + "clinic/save/claim/transaction", data)
                  .success(function (response) {
                    scope.hideLoading();
                    if (!response.status) {
                      swal("Oooops!", response.message, "error");
                    } else {
                      scope.getSuccessfullTransactions();
                      scope.claim_list.splice(
                        scope.claim_list.indexOf(data),
                        1
                      );
                      swal(
                        "Success!",
                        "The Transaction Successfully Saved.",
                        "success"
                      );
                      $("#modalManual").modal("hide");
                      $("#summary-claim-modal").modal("hide");
                    }
                  });
              }
            }
          );
        };
        scope.addClaim = function () {
          console.log(scope.add_claim_data);
          $("#check-claim-modal").modal("hide");
          if (scope.checkClaimForm(scope.add_claim_data) == true) {
            scope.add_claim_data.id =
              scope.add_claim_data.selected_nric_data.id;
            scope.add_claim_data.back_date = 1;
            console.log(
              scope.add_claim_data.visit_date +
                " " +
                scope.add_claim_data.visit_time +
                " " +
                scope.add_claim_data.daytime
            );
            scope.add_claim_data.transaction_date = moment(
              scope.add_claim_data.visit_date +
                " " +
                scope.add_claim_data.visit_time +
                " " +
                scope.add_claim_data.daytime,
              "DD MMM, YYYY hh:mm A"
            ).format("YYYY-MM-DD hh:mm A");
            scope.add_claim_data.procedure_ids =
              scope.add_claim_data.selected_service_ids;
            scope.add_claim_data.currency_type = scope.clinic.currency_type;
            // scope.add_claim_data.currency_amount = (scope.add_claim_data.currency_type == 'sgd') ? scope.add_claim_data.amount : scope.add_claim_data.amount * 3;
            scope.add_claim_data.currency_amount = scope.clinic.currency_amount;
            scope.add_claim_data.health_provider = 0;
            // scope.add_claim_data.transaction_id = 0;

            swal(
              {
                title: "Are you sure?",
                text: "This transaction data will be saved.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!",
                cancelButtonText: "Cancel",
                closeOnConfirm: true,
                closeOnCancel: true,
              },
              function (isConfirm) {
                if (isConfirm) {
                  scope.showLoading();
                  $http
                    .post(
                      base_url + "clinic/save/claim/transaction",
                      scope.add_claim_data
                    )
                    .success(function (response) {
                      scope.hideLoading();
                      if (!response.status) {
                        swal("Oooops!", response.message, "error");
                      } else {
                        scope.getSuccessfullTransactions();
                        swal(
                          "Success!",
                          "The Transaction Successfully Saved.",
                          "success"
                        );
                        $("#modalManual").modal("hide");
                        $("#summary-claim-modal").modal("hide");
                      }
                    });
                }
              }
            );
          }
        };
        scope.checkClaim = function () {
          $("#check-claim-modal").modal("show");
          $(".isNotDoneChecking").show();
          $(".isDoneChecking").hide();
          var data = {
            user_id: scope.add_claim_data.selected_nric_data.id,
            date_transaction: moment(scope.add_claim_data.visit_date).format(
              "YYYY-MM-DD"
            ),
            amount: scope.add_claim_data.amount,
            currency_type: scope.currencyType,
          };
          console.log(data);
          $http
            .post(base_url + "check_duplicate_transaction", data)
            .then(function (response) {
              // console.log(response);
              if (response.data.status == true && response.data.error == 0) {
                scope.your_transaction = response.data.new_transaction;
                scope.other_transaction = response.data.duplicates;

                console.log(scope.your_transaction);
                $(".isNotDoneChecking").hide();
                $(".isDoneChecking").show();
              } else {
                $("#check-claim-modal").modal("hide");
                scope.addClaim();
              }
            })
            .catch(function (err) {
              $("#check-claim-modal").modal("hide");
            });
        };
        scope.getServices = function () {
          $http
            .get(base_url + "clinic/get/services")
            .success(function (response) {
              scope.service_list = response;
              scope.services = response;
              // console.log(response);
            });
        };
        scope.getClinicDetails = function () {
          scope.showLoading();
          $http.get(base_url + "clinic/details").success(function (response) {
            scope.hideLoading();
            scope.clinic = response.clinic;
            scope.currencyType = response.clinic.currency_type;

            if (scope.clinic.currency_type == "myr") {
              scope.placeholder = "Enter Amount in MYR";
            } else {
              scope.placeholder = "Enter Amount in SGD";
            }
          });
        };
        scope.searchByNric = function (data) {
          var data = {
            nric: data,
            mobile: data,
            start_date: scope.selected_start_date,
            end_date: scope.selected_end_date,
          };
          scope.showLoading();
          $http
            .post(base_url + "clinic/search_by_nric_transactions", data)
            .success(function (response) {
              scope.backdate_list = response.data;
              scope.hideLoading();
            });
        };
        scope.searchNRICchanged = function (data) {
          if (data == "") {
            scope.getSuccessfullTransactions();
          }
        };
        scope.getHealthProvider = function () {
          $http
            .get(base_url + "clinic/get/health_provider/transaction")
            .success(function (response) {
              angular.forEach(response, function (value, key) {
                // console.log( value );
                var procedures = [];
                angular.forEach(value.procedure_ids, function (value2, key) {
                  $http
                    .get(base_url + "clinic/get/service/details/" + value2)
                    .then(function (response) {
                      procedures.push(response.data);
                    });
                });
                $http
                  .get(base_url + "clinic/get/user/details/" + value.user_id)
                  .then(function (response) {
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
                      transaction_id: value.transaction_id,
                    };
                    var check_exist = 0;
                    if (scope.claim_list.length > 0) {
                      angular.forEach(scope.claim_list, function (
                        value2,
                        key2
                      ) {
                        if (value2.transaction_id == data.transaction_id) {
                          check_exist = 1;
                        }
                        if (
                          key2 == scope.claim_list.length - 1 &&
                          check_exist == 0
                        ) {
                          scope.claim_list.push(data);
                        }
                      });
                    } else {
                      scope.claim_list.push(data);
                    }
                  });
              });
            });
        };
        scope.checkDataClaimLists = function (id) {
          var status = false;
          angular.forEach(scope.claim_list, function (value, key) {
            if (value.transaction_id == id) {
              status = true;
            }
          });
          return status;
        };
        scope.getPusherConfig = function (connection) {
          console.log("connection", connection);
          socket.on(connection, function (data) {
            console.log(data);
            if (parseInt(data.clinic_id) == parseInt(scope.clinic.ClinicID)) {
              // check if transaction is already push to the array of claim_list
              if (!scope.checkDataClaimLists(data.transaction_id)) {
                $http
                  .get(
                    base_url +
                      "clinic/transaction_specific?transaction_id=" +
                      data.transaction_id
                  )
                  .success(function (response) {
                    var procedures = [];
                    angular.forEach(response.procedure_ids, function (
                      value,
                      key
                    ) {
                      $http
                        .get(base_url + "clinic/get/service/details/" + value)
                        .then(function (response) {
                          procedures.push(response.data);
                        });
                    });
                    var data = {
                      nric: response.NRIC,
                      procedure: response.ProcedureID,
                      procedures: procedures,
                      display_book_date: response.date_of_transaction,
                      book_date: moment(response.date_of_transaction).format(
                        "YYYY-MM-DD"
                      ),
                      id: response.UserID,
                      amount: response.procedure_cost,
                      back_date: 0,
                      health_provider: response.health_provider,
                      multiple_procedures: response.multiple_procedures,
                      procedure_ids: response.procedure_ids,
                      transaction_id: response.transaction_id,
                    };
                    $http
                      .get(
                        base_url + "clinic/get/user/details/" + response.UserID
                      )
                      .then(function (response2) {
                        data.name = response2.data[0].Name;
                        data.user_type = response2.data[0].UserType;
                        data.access_type = response2.data[0].access_type;
                        scope.claim_list.push(data);
                      });
                  });
              }
            }
          });
        };
        scope.autoRemoveRegData = function (id, key) {
          $http
            .get(base_url + "clinic/auto_remove_check_in?check_in_id=" + id)
            .then(function (response) {
              console.log(response);
              if (response.data.status) {
                // scope.registration_arr.splice( key, 1 );
                scope.getClinicCheckIns();
              }
            });
        };
        var reg_timeout = null;
        scope.checkExpiredRegistrations = function () {
          angular.forEach(scope.registration_arr, function (value, key) {
            var expiry_date = moment(value.expiry);
            var date_now = moment();
            var duration = moment.duration(
              moment(date_now).diff(moment(expiry_date))
            );
            var hours = duration.asMinutes();
            console.log(hours);
            if (hours >= 0) {
              scope.autoRemoveRegData(value.check_in_id, key);
            }
            // if( key == scope.registration_arr.length-1 ){
            //   $timeout.cancel( reg_timeout );

            // }
          });
          reg_timeout = $timeout(function () {
            scope.checkExpiredRegistrations();
          }, 30000);
        };
        scope.getClinicCheckIns = function () {
          $http
            .get(base_url + "clinic/get_check_in_lists")
            .then(function (response) {
              console.log(response);
              scope.registration_arr = response.data.data;
              scope.checkExpiredRegistrations();
            });
        };
        scope.getCheckInConfig = function (connection) {
          console.log("connection check in", connection);
          socket.on(connection, function (data) {
            console.log(data);
            if (parseInt(data.clinic_id) == parseInt(scope.clinic.ClinicID)) {
              // scope.showLoading();
              scope.isLoading = true;
              $http
                .get(
                  base_url +
                    "clinic/get_specific_check_in?check_in_id=" +
                    data.check_in_id
                )
                .then(function (response) {
                  console.log(response);
                  // scope.hideLoading();
                  scope.isLoading = false;
                  scope.registration_arr.push(response.data.data);
                });
            }
          });
        };
        scope.getCheckInConfigRemove = function (connection) {
          console.log("connection check in remove", connection);
          socket.on(connection, function (data) {
            console.log(data);
            if (parseInt(data.clinic_id) == parseInt(scope.clinic.ClinicID)) {
              // scope.showLoading();
              scope.isLoading = true;
              scope.getClinicCheckIns();
              scope.getSuccessfullTransactions();

              $timeout(function () {
                scope.isLoading = false;
              }, 1000);
            }
          });
        };
        scope.getClinicSocketConnection = function () {
          console.log(base_url + "clinic_socket_connection");
          $http
            .get(base_url + "clinic_socket_connection")
            .then(function (response) {
              console.log(response);
              if (response.data.status) {
                scope.getPusherConfig(
                  response.data.socket_connection_pay_direct
                );
                scope.getCheckInConfig(
                  response.data.socket_connection_check_in
                );
                scope.getCheckInConfigRemove(
                  response.data.connection_check_in_remove
                );
              }
            });
        };
        scope.searchUserByNRIC = function (search) {
          if (search) {
            scope.showLoading();
            $http
              .get(base_url + "clinic/search_all_users?q=" + search)
              .success(function (response) {
                scope.hideLoading();
                scope.users_arr = response.results;

                if (scope.users_arr.length == 0) {
                  swal("Error!", "No Users found.", "error");
                }
              });
          }
        };
        scope.getAllUsers = function (search) {
          scope.users_nric_arr = [];
          console.log("search.length", search.length);
          if (search && search.length > 7) {
            $http
              .get(base_url + "clinic/get/all/users?q=" + search)
              .success(function (response) {
                scope.users_nric_arr = response.items;
                scope.isSearchNRIC = true;
              });
          } else {
            scope.isSearchNRIC = false;
          }
        };
        scope.getSuccessfullTransactions = function () {
          // scope.showLoading();
          $http
            .get(base_url + "clinic/all_transactions")
            .success(function (response) {
              console.log(response);
              // scope.hideLoading();
              scope.backdate_list = response;
              // localStorage.setItem("currency_type",scope.backdate_list.data.data.currency_type);
            });
        };
        // ================ //

        scope.showStartdate = function () {
          $(".start-datepicker").datepicker("show");
        };
        scope.showEnddate = function () {
          $(".end-datepicker").datepicker("show");
        };
        scope.initializeDatePickers = function () {
          setTimeout(function () {
            $(".datepicker")
              .datepicker({
                format: "dd MM, yyyy",
                endDate: new Date(),
              })
              .on("changeDate", function (e) {
                console.log(e);
                scope.add_claim_data.visit_date = moment(e.date).format(
                  "DD MMMM, YYYY"
                );
              });

            $(".start-datepicker")
              .datepicker({
                format: "mm/dd/yyyy",
                viewMode: "months",
                minViewMode: "months",
                maxDate: new Date(),
              })
              .on("changeDate", function (e) {
                // console.log( e );
                $(".start-datepicker").datepicker("hide");
                scope.selected_start_date = moment(e.date)
                  .startOf("month")
                  .format("MM/DD/YYYY");
                var date = moment(e.date).startOf("month").format("MM/DD/YYYY");
                if (date > scope.selected_end_date) {
                  scope.selected_end_date = moment(e.date)
                    .endOf("month")
                    .format("MM/DD/YYYY");
                  $(".end-datepicker").datepicker(
                    "update",
                    scope.selected_end_date
                  );
                }
                $(".end-datepicker").datepicker(
                  "setStartDate",
                  scope.selected_start_date
                );
                scope.searchByNric(scope.searchTrans_text);
              });

            $(".end-datepicker")
              .datepicker({
                format: "mm/dd/yyyy",
                startDate: new Date(),
                viewMode: "months",
                minViewMode: "months",
                maxDate: new Date(),
              })
              .on("changeDate", function (e) {
                // console.log( e );
                $(".end-datepicker").datepicker("hide");
                scope.selected_end_date = moment(e.date)
                  .endOf("month")
                  .format("MM/DD/YYYY");
                scope.searchByNric(scope.searchTrans_text);
              });
          }, 500);
        };
        scope.showLoading = function () {
          $(".main-loader").fadeIn();
        };
        scope.hideLoading = function () {
          setTimeout(function () {
            $(".main-loader").fadeOut();
          }, 1000);
        };

        scope.onLoad = function () {
          scope.getClinicDetails();
          scope.getClinicSocketConnection();
          scope.getHealthProvider();
          scope.getSuccessfullTransactions();
          scope.getServices();
          scope.initializeDatePickers();
          scope.getClinicCheckIns();
          console.log(scope.currencyType);
        };

        scope.onLoad();

        // ===== JQUERY ===== //
        $(".modal").on("hide.bs.modal", function () {
          scope.users_arr = [];
          scope.search_member = "";
        });
        $("body").click(function (e) {
          if ($(e.target).parents(".service-td").length === 0) {
            $(".service-drop").hide();
          }
          if ($(e.target).parents(".datepicker-td").length === 0) {
            $(".timepicker-container").hide();
            $(".daytime-container").hide();
          }
        });
        // ================== //
      },
    };
  },
]);
