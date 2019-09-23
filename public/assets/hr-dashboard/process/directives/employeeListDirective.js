app.directive("employeeListDirective", [
  "$state",
  "hrSettings",
  "$rootScope",
  function directive($state, hrSettings, $rootScope) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("employeeListDirective Runnning !");


        scope.empModal_arr = {};
        scope.emp_active = 0;
        scope.replace_input = {
          nric_status: true
        };
        scope.options = {};
        scope.invalid_email = false;
        scope.invalid_nric = false;
        scope.error_message = "";
        scope.job_list = {};
        scope.showAssign = false;
        scope.status_message = "";
        scope.success_message = "";
        scope.credits = 0;
        scope.editCreditSpendingType = 0;
        scope.editCreditTransactionType = 0;
        scope.err = false;
        scope.succ = false;

        scope.err_mess = "";
        scope.show_error = false;

        $("#edit-employee-modal").on("hidden.bs.modal", function(e) {
          scope.showAssign = false;
          $(".status-wrapper").fadeOut();
          scope.empModal_arr = {};
          // scope.credit = 0;
        });

        scope.$on("empInitialized", function(evt, data){
          console.log(data);
          scope.empModal_arr = data.data;
          scope.options = data.accessibility;
          scope.getData();

          angular.forEach( scope.empModal_arr, function(value,key){
            value.dob = scope.convertToDate( value.dob );
          });
        });

        scope.$on("refreshEmpList", function(evt, data){
          scope.getData();
        });

        scope.setSpendType = function( opt ){
          scope.editCreditSpendingType = opt;
        }

        scope.setTransType = function( opt ){
          scope.editCreditTransactionType = opt;
        }

        scope.toggleNRIC = function(data, opt){
          if (opt == "nric") {
            scope.replace_input.nric_status = true;
            scope.replace_input.fin_status = false;
          } else {
            scope.replace_input.nric_status = false;
            scope.replace_input.fin_status = true;
          }

          scope.replace_input.nric = "";
        };

        scope.getData = function() {
          // angular.forEach(scope.empModal_arr, function(value, key) {
          //   hrSettings.getCredits(value.user_id).then(function(response) {
          //     console.log(response);
          //     scope.empModal_arr[key].com_credits = response.data.data;
          //   });
          // });
        };

        scope.convertToDate = function( date ){
          return moment(date).format('MM/DD/YYYY');
        }

        scope.showForm = function() {
          scope.showAssign = true;
        };

        scope.hideForm = function() {
          scope.showAssign = false;
          scope.status_message = "";
        };

        scope.closePass = function() {
          $("#input-pass").modal("hide");
        };

        scope.editCreditSpendingType = 0;
        scope.editCreditTransactionType = 0;

        scope.removeCommas = function(str) {
            while (str.search(",") >= 0) {
                str = (str + "").replace(',', '');
            }
            return str;
        };

        scope.numberWithCommas = function (x) {
            // return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            // return x.toLocaleString(undefined, { maximumFractionDigits: 2 })
            return parseFloat(x).toFixed(2).toLocaleString();
        }

        scope.assignCred = function() {
          scope.credits = $("#ass_credits").val();

          var data = {
            user_id: scope.empModal_arr[scope.emp_active].user_id,
            credits: scope.credits,
            spending_type : scope.editCreditSpendingType == 0 ? 'medical' : 'wellness',
            allocation_type : scope.editCreditTransactionType == 0 ? 'add' : 'deduct'
          };

          hrSettings.addEmployeeCredits(data)
            .then(function(response) {
              console.log(response);
              if (response.status == false) {
                scope.status_message = response.data.message;
                scope.err = true;
                scope.succ = false;
                scope.credit = 0;
              } else {
                if(scope.editCreditSpendingType == 0) {
                  // medical
                  if(scope.editCreditTransactionType == 0) {
                    // add
                    var credits = scope.removeCommas(scope.empModal_arr[scope.emp_active].allocation.credits_allocation);
                    var format_credit = parseFloat(credits) + parseFloat(scope.credits);
                    scope.empModal_arr[scope.emp_active].allocation.credits_allocation = scope.numberWithCommas(format_credit);
                  } else {
                    var credits = scope.removeCommas(scope.empModal_arr[scope.emp_active].allocation.credits_allocation);
                    var format_credit = parseFloat(credits) - parseFloat(scope.credits);
                    scope.empModal_arr[scope.emp_active].allocation.credits_allocation = scope.numberWithCommas(format_credit);
                  }
                } else {
                  // wellness
                  if(scope.editCreditTransactionType == 0) {
                    // add
                    // scopeempModal_arr[scope.emp_active].allocation.credits_allocation_wellness = parseFloat(scope.empModal_arr[scope.emp_active].allocation.credits_allocation_wellness) + parseFloat(scope.credits);
                    var credits = scope.removeCommas(scope.empModal_arr[scope.emp_active].allocation.credits_allocation_wellness);
                    var format_credit = parseFloat(credits) + parseFloat(scope.credits);
                    scope.empModal_arr[scope.emp_active].allocation.credits_allocation_wellness = scope.numberWithCommas(format_credit);
                  } else {
                    // scopeempModal_arr[scope.emp_active].allocation.credits_allocation_wellness = parseFloat(scope.empModal_arr[scope.emp_active].allocation.credits_allocation_wellness) - parseFloat(scope.credits);
                    var credits = scope.removeCommas(scope.empModal_arr[scope.emp_active].allocation.credits_allocation_wellness);
                    var format_credit = parseFloat(credits) - parseFloat(scope.credits);
                    scope.empModal_arr[scope.emp_active].allocation.credits_allocation_wellness = scope.numberWithCommas(format_credit);
                  }
                }

                scope.err = false;
                scope.succ = true;
                scope.showAssign = false;
                scope.status_message = response.data.message;
                scope.credit = 0;
                $rootScope.$broadcast("refreshEmpList");
              }
            });
        };

        scope.passwordCredit = function(pass) {
          if (!pass || pass == "") {
            scope.show_error = true;
            scope.err_mess = "Please input your password";
          } else {
            scope.show_error = false;
            var data = {
              password: pass
            };

            hrSettings.sendPassword(data).then(function(response) {
              if (response.data.status === false) {
                scope.show_error = true;
                scope.err_mess = response.data.message;
              } else {
                scope.show_error = false;
                scope.closePass();
                scope.passCredit = "";
                scope.assignCred();
              }
            });
          }
        };

        scope.replaceEmp = function() {
          var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

          if (scope.replace_input.nric_status == true) {
            if (scope.checkNRIC(scope.replace_input.nric) != true) {
              scope.invalid_nric = true;
              return false;
            } else {
              scope.invalid_nric = false;
            }
          }

          if (emailReg.test(scope.replace_input.email) == true) {
            scope.invalid_email = false;

            $(".replace-employee-update-button").attr("disabled", true);
            $(".status-wrapper").hide();
            $(".preloader-wrapper").fadeIn();

            var data = {
              replace_id: scope.empModal_arr[scope.emp_active].user_id,
              first_name: scope.replace_input.fname,
              last_name: scope.replace_input.lname,
              nric: scope.replace_input.nric,
              email: scope.replace_input.email,
              mobile: scope.replace_input.phone_no,
              job_title: scope.replace_input.job_title,
              plan_start: moment(scope.replace_input.plan_start).format(
                "YYYY-MM-DD"
              ),
              dob: scope.replace_input.dob
            };

            console.log(data);

            hrSettings.replaceEmployee(data).then(function(response) {
              $(".replace-employee-update-button").attr("disabled", false);
              $(".preloader-wrapper").hide();

              if (response.data.status == true) {
                $(".update-replace").hide();

                if (scope.empModal_arr.length > 1) {
                  $(".next-replace").fadeIn();
                } else {
                  $(".status-wrapper").fadeIn();
                }

                $rootScope.$broadcast("refresh");
              } else {
                scope.error_message = response.data.message;
              }
            });
          } else {
            scope.invalid_email = true;
          }
        };

        scope.updateEmp = function() {
          $(".edit-employee-details-button").attr("disabled", true);
          $(".status-wrapper").hide();
          $(".preloader-wrapper").fadeIn();

          var data = {
            name:scope.empModal_arr[scope.emp_active].fname + " " + scope.empModal_arr[scope.emp_active].lname,
            dob: scope.empModal_arr[scope.emp_active].dob,
            nric: scope.empModal_arr[scope.emp_active].nric,
            email: scope.empModal_arr[scope.emp_active].email,
            phone_no: scope.empModal_arr[scope.emp_active].phone_no,
            job_title: scope.empModal_arr[scope.emp_active].job_title,
            user_id: scope.empModal_arr[scope.emp_active].user_id
          };
          hrSettings.updateEmployee(data).then(function(response) {
            $(".edit-employee-details-button").attr("disabled", false);
            $(".preloader-wrapper").hide();
            $(".status-wrapper").fadeIn();
            scope.empModal_arr[scope.emp_active].name = data.name;
            $rootScope.$broadcast("refresh");

            $.toast({
              text: "Employee(s) successfully updated!",
              bgColor: "#1487b3",
              textColor: "#fff",
              allowToastClose: true,
              hideAfter: 5000,
              stack: 5,
              textAlign: "left",
              position: "bottom-right"
            });
          });
        };

        scope.submitWithdraw = function(empModal_arr) {
          var temp = [];
          $("#submit-btn").attr("disabled", true);
          $("#submit-btn").text("SUBMITTING");
          angular.forEach(empModal_arr, function(value, key) {
            console.log(value);
            temp.push({
              user_id: value.user_id,
              expiry_date: moment(value.date_expire).format("YYYY/MM/DD")
            });
            if (empModal_arr.length == key + 1) {
              hrSettings.withDraw(temp).then(function(response) {
                console.log(response);
                $("#submit-btn").attr("disabled", false);
                $("#submit-btn").text("SUBMIT");
                if (response.data.status) {
                  swal("Success!", response.data.message, "success");
                  $("#delete-employee-withdraw-modal").modal("hide");
                  var temp = [];
                  $rootScope.$broadcast("refresh");
                } else {
                  swal("Ooops!", response.data.message, "error");
                }
              });
            }
          });
        };

        scope.deleteEmp = function() {
          $("#delete-employee-confirmation-modal").modal("hide");
          $("#delete-employee-withdraw-modal").modal("show");

          if (scope.empModal_arr.length > 0) {
            angular.forEach(scope.empModal_arr, function(value, key) {
              $(".wdraw-picker" + key).daterangepicker({
                autoUpdateInput: true,
                autoApply: true,
                singleDatePicker: true
              },
              function(start, end, label) {
                console.log(start);
                console.log(end);
                // $('.wdraw-picker' + key ).val( moment( start ).format( 'DD MMMM, YYYY' ) );
              });
              $(".wdraw-picker" + key).val(moment().format("DD MMMM, YYYY"));
              scope.empModal_arr[key].date_expire = moment().format("DD MMMM, YYYY");
              $(".wdraw-picker" + key).on("show.daterangepicker", function(ev,picker) {
                if (scope.empModal_arr[key].date_expire != moment(picker.startDate).format("DD MMMM, YYYY")) {
                  console.log(true);
                  $(".wdraw-picker" + key).val( moment(picker.startDate).add(1, "days").format("DD MMMM, YYYY"));
                  scope.empModal_arr[key].date_expire = moment(picker.startDate).add(1, "days").format("DD MMMM, YYYY");
                } else {
                  $(".wdraw-picker" + key).val(moment(picker.startDate).format("DD MMMM, YYYY"));
                  scope.empModal_arr[key].date_expire = moment(picker.startDate).format("DD MMMM, YYYY");
                }
              });

              $(".wdraw-picker" + key).on("hide.daterangepicker", function(ev,picker) {
                if (scope.empModal_arr[key].date_expire != moment(picker.startDate).format("DD MMMM, YYYY") ) {
                  console.log(true);
                  $(".wdraw-picker" + key).val(moment(picker.startDate).add(1, "days").format("DD MMMM, YYYY"));
                  scope.empModal_arr[key].date_expire = moment(picker.startDate).add(1, "days").format("DD MMMM, YYYY");
                } else {
                  $(".wdraw-picker" + key).val(moment(picker.startDate).format("DD MMMM, YYYY"));
                  scope.empModal_arr[key].date_expire = moment(picker.startDate).format("DD MMMM, YYYY");
                }
              });

              $(".wdraw-picker" + key).on("apply.daterangepicker", function(ev,picker) {
                $(".wdraw-picker" + key).val(moment(picker.startDate).format("DD MMMM, YYYY"));
                scope.empModal_arr[key].date_expire = moment(picker.startDate).format("DD MMMM, YYYY");
              });

            });
          }
        };

        scope.checkNRIC = function(theNric) {
          var nric_pattern = null;
          if( theNric.length == 9 ){
            nric_pattern = new RegExp("^[stfgSTFG]{1}[0-9]{7}[a-zA-z]{1}$");
          }else if( theNric.length == 12 ){
            // nric_pattern = new RegExp("^[0-9]{2}(?:0[1-9]|1[-2])(?:[0-1]|[1-2][0-9]|[3][0-1])[0-9]{6}$");
            return true;
          }else{
            return false;
          }
          return nric_pattern.test(theNric);
        };

        scope.nextEmp = function() {
          scope.emp_active++;
          $(".update-replace").hide();
          $(".next-replace").fadeIn();
        };

        scope.prevEmp = function() {
          scope.emp_active--;
        };

        scope.getJobs = function() {
          hrSettings.getJobTitle().then(function(response) {
            scope.job_list = response.data;
          });
        };

        scope.showGlobalModal = function( message ){
          $( "#global_modal" ).modal('show');
          $( "#global_message" ).text(message);
        }

        scope.onLoad = function() {
          scope.getJobs();
        };

        scope.onLoad();

        $('.dob-datepicker').datepicker({
          format: 'mm/dd/yyyy',
          // startDate: '-18y'
        });

        $('.dob-datepicker').datepicker().on('hide',function(evt){
          console.log(scope.empModal_arr[scope.emp_active]);
          // if( scope.selected_employee.start_date == null || scope.selected_employee.start_date == "" ){
            $('.dob-datepicker').datepicker('setDate', scope.empModal_arr[scope.emp_active].dob);
          // }
        })

        $('.replace-datepicker').datepicker({
          format: 'mm/dd/yyyy',
          // startDate: '-18y'
        });

        

        // $('.replace-datepicker').datepicker().on('hide',function(evt){
        //   console.log(scope.replace_input.dob);
        //   $('.replace-datepicker').datepicker('setDate', scope.replace_input.dob);
        // })
      }
    };
  }
]);
