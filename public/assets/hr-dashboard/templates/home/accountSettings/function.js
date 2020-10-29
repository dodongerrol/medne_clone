app.directive("accountSettingsDirective", [
  "$state",
  "hrSettings",
  "$rootScope",
  "$state",
  "serverUrl",
  "$timeout",
  "$http",
  "serverUrl",
  function directive($state, hrSettings, $rootScope, $state, serverUrl, $timeout, $http, serverUrl) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("accountSettingsDirective Runnning !");
        scope.token = window.localStorage.getItem("token");
        scope.global_hrData = {
          phone_code: "",
        };
        scope.editHrSuccessfullyUpdated = false;
        scope.unlinkSuccessfullyUpdated = false;
        // Active Plan Pagination and Old Plan List
        scope.selectedOldPlan = null;
        scope.activePlan_active_page = 1;
        scope.isActivePlanDropShow = false;
        scope.oldPlan_list = [];
        scope.oldPlan_active_page = null;
        scope.isOldPlanListDropShow = false;

        scope.isViewMemberPerPageShow = false;
        scope.mem_per_page = 10;
        scope.mem_active_page = 1;
        scope.selectedEnrollHistoryList = {};
        scope.memberSearch = null;
        scope.isViewMemberModalShow = false;
        scope.viewMemberModalpagesToDisplay = 10;
        scope.memberSearch = null;
        scope.viewMemberList = [];

        scope.passwordData = {
          newPassword: "",
          confirmPassword: "",
          currentPassword: "",
        };
        scope.global_passwordSuccess = false;
        scope.passwordCheck = false;
        scope.page_active = 1;
        scope.per_page = 3;
        scope.editHrSuccessfullyUpdated = false;

        scope.enroll_page_active = 1;
        scope.enroll_per_page = 3;

        scope.global_planData = {
          start_date: new Date(),
          plan_duration: "12",
          invoice_start_date: new Date(),
          // invoice_date: new Date(),
        };

        scope.changePrimaryData = {
          phone_code: ''
        }
        scope.unlinkData = {
          phone_code: ''
        }
        scope.isChangedInput = false;
        scope.isPasswordInputChanged = false;
        scope.updateHrData  = {};

        scope.setPhoneCode = () => {
          alert('asdasds');
          // scope.global_hrData.phone_code = code;
        }

        scope.linkedAccountHeaders = ["Account Name", "Company ID", "Link Date", "Plan Type", "Primary Admin's email", "Action"];

        scope.changePrimaryAdminModals = {
          warn: 'change-primary-admin-warn-modal',
          form: 'change-primary-admin-form-modal',
          success: 'change-primary-admin-success'
        }

        scope.presentModal = (id, show = true) => {

          if ( scope.get_permissions_data.create_remove_edit_admin_unlink_account == 1 ) {
            $(`#${id}`).modal(show ? "show" : "hide");

            if(id == scope.changePrimaryAdminModals.form && show == true){
              scope.changePrimaryData = {
                phone_code: '65'
              }
              $timeout(function(){
                scope.initializeChangePrimaryAdminCountryCode();
              },400);
            }
          } else {
            $('#permission-modal').modal('show');
            $(`#${id}`).modal("hide");
          }
          
        };

        scope.isChanged = function(){
          scope.isChangedInput = true;
        }
        scope.isPasswordChanged = function(){
          scope.isPasswordInputChanged = true;
        }

        scope.changeAdmin = () => {
          scope.presentModal(
            scope.changePrimaryAdminModals.form,
            false
          );
          scope.presentModal(
            scope.changePrimaryAdminModals.success,
            true
          );
        }

        scope._updatePasswordBtn_ = function () {
          scope.global_passwordSuccess = false;
          scope.passwordData.newPassword = "";
          scope.passwordData.confirmPassword = "";
          scope.passwordCheck = false;
        };

        scope._updatePassword_ = function (data) {
          let params = {
            new_password: data.newPassword,
            confirm_password: data.confirmPassword,
            current_password: data.currentPassword,
          };

          if (data.newPassword == data.confirmPassword) {
            scope.showLoading();
            hrSettings.updateHrPassword(params).then(function (response) {
              if (response.status) {
                scope.global_passwordSuccess = true;
                scope.passwordData = {
                  newPassword: "",
                  confirmPassword: "",
                  currentPassword: "",
                };
              }
              scope.hideLoading();
            });
          } else {
            scope.passwordCheck = true;
          }
        };

        scope.getInvoiceHistoryData = function (page, per_page, customer_active_plan_id) {
          page = scope.page_active;
          per_page = scope.per_page;
          customer_active_plan_id = scope.activePlanDetails_pagination.data.customer_active_plan_id;

          scope.showLoading();
          hrSettings.getPlanInvoiceHistory(page, per_page, customer_active_plan_id).then(function (response) {
            scope.getPlanInvoiceData = response.data.data.data;
            scope.invoicePlanPagination = response.data.data;
            console.log(scope.getPlanInvoiceData);
            angular.forEach(scope.getPlanInvoiceData, function (value, key) {
              value.invoice_date = moment(value.invoice_date).format("DD MMMM YYYY");
              value.invoice_due = moment(value.invoice_due).format("DD MMMM YYYY");
              value.payment_date = moment(value.payment_date).format("DD MMMM YYYY");
              // value.total = value.total.toFixed(2);
            });
            scope.hideLoading();
          });
        };

        scope._selectNumList_ = function (type, num) {
          if (type == "invoice-history") {
            scope.page_active = num;
            scope.getInvoiceHistoryData();
          }
          if (type == "enrollment-history") {
            scope.enroll_page_active = num;
            scope.getEnrollmentHistoryData();
          }
        };

        scope._prevPageList_ = function (type) {
          if (type == "invoice-history") {
            scope.page_active -= 1;
            scope.getInvoiceHistoryData();
          }
          if (type == "enrollment-history") {
            scope.enroll_page_active -= 1;
            scope.getEnrollmentHistoryData();
          }
        };

        scope._nextPageList_ = function (type) {
          if (type == "invoice-history") {
            scope.page_active += 1;
            scope.getInvoiceHistoryData();
          }
          if (type == "enrollment-history") {
            scope.enroll_page_active += 1;
            scope.getEnrollmentHistoryData();
          }
        };

        scope._toggleInvoicePerPage_ = function () {
          $(".invoice-per-page-container").toggle();
        };

        scope._toggleEnrollmentPerPage_ = function () {
          $(".enrollment-per-page-container").toggle();
        };

        scope._setPageLimit_ = function (type, num) {
          if (type == "invoice-history") {
            scope.per_page = num;
            scope.page_active = 1;
            scope.getInvoiceHistoryData();
          }
          if (type == "enrollment-history") {
            scope.enroll_per_page = num;
            scope.enroll_page_active = 1;
            scope.getEnrollmentHistoryData();
          }
        };

        scope._getHrDetails_ = async function () {
          await hrSettings.fecthHrDetails().then(function (response) {
            scope.global_hrData = response.data.hr_account_details;
          });
        };

        scope._editDetailsBtn_ = function (data) {
          console.log(data);
          scope.editHrSuccessfullyUpdated = false;
          scope.updateHrData  = {
            email: data.email,
            phone: data.phone,
            full_name: data.full_name,
            phone_code: data.phone_code,
          };
          
          $("#edit_details").modal('show');

          $timeout(function(){
            scope.initializeGeoCode();
          }, 300);
        };

        scope.checkHrForm = function(data){
          console.log(data);
          if (data.phone_code == "+65") {
            console.log(data.phone);
            console.log(typeof data.phone[0]);
            data.phone = data.phone.toString();
            if((data.phone[0] != "8" && data.phone[0] != "9") || data.phone.length != 8) {
							swal('Error!', 'Invalid mobile format. Please enter mobile in the format 8 digit number and starts with 8 or 9.', 'error');
							return false;
						}
					}
          if (data.phone_code == "+60") {
            if(data.phone.length < 9 || data.phone.length > 10) {
							swal('Error!', 'Invalid mobile format. Please enter mobile in the format of 9-10 digit number without the prefix “0”.', 'error');
							return false;
						}
					}
        }

        scope._updateHrDetails_ = function (data) {
          if(scope.checkHrForm(data) == false){
            return false;
          }
          let params = {
            email: data.email,
            phone_number: data.phone,
            fullname: data.full_name,
            phone_code: data.phone_code,
          };

          scope.showLoading();
          hrSettings.updateHrDetails(params).then(function (response) {
            // console.log(response);
            if (response.data.status == true) {
              // $('#edit_details').modal('hide');
              scope.editHrSuccessfullyUpdated = true;

              scope._getHrDetails_();
              scope.hideLoading();
            }
          });
        };

        scope.getEnrollmentHistoryData = function (page, per_page, customer_active_plan_id) {
          page = scope.enroll_page_active;
          per_page = scope.enroll_per_page;
          // customer_active_plan_id = scope.getCustomerPlanId;
          customer_active_plan_id = scope.activePlanDetails_pagination.data.customer_active_plan_id;

          scope.showLoading();
          hrSettings.fetchEnrollmentHistoryData(page, per_page, customer_active_plan_id).then(function (response) {
            scope.global_enrollmentHistoryData = response.data.data.data;
            scope.global_enrollmentHistoryPagination = response.data.data;
            console.log(scope.global_enrollmentHistoryData);
            angular.forEach(scope.global_enrollmentHistoryData, function (value, key) {
              value.date_of_edit = moment(value.date_of_edit).format("DD MMMM YYYY");
              value.plan_start = moment(value.plan_start).format("DD MMMM YYYY");
            });

            scope.hideLoading();
          });
        };

        scope.enrollAction = function (data, index) {
          scope.global_enrollCustomerId = data.id;

          scope.global_enrollmentHistoryData.map((value, key) => {
            if (index == key) {
              value.isActionShow = value.isActionShow == true ? false : true;
            }
            // else {
            //   value.isActionShow = false;
            // }
          });
        };
        scope.closeAllEnrollAction = function () {
          if (scope.global_enrollmentHistoryData) {
            scope.global_enrollmentHistoryData.map((value, key) => {
              value.isActionShow = false;
            });
          }
        };

        $("body").click(function (e) {
          if ($(e.target).parents(".enrollment-actions-dp-wrapper").length === 0) {
            scope.closeAllEnrollAction();
            // scope.$apply();
          }
        });

        scope._confirmActivationEmail_ = function () {
          let data = {
            id: scope.global_enrollCustomerId,
          };

          hrSettings.sendImmediateActivation(data).then(function (response) {
            if (response.data.status == true) {
              $("#send_immediately_modal").modal("hide");

              swal("Success!", response.data.message, "success");
            } else {
              swal("Error!", response.data.message, "error");
            }
          });
        };

        scope.getPlanDetails = async function () {
          var data = {
            page: scope.activePlan_active_page,
          };
          if (scope.selectedOldPlan != null) {
            data.oldPlanCustomerPlanID = scope.selectedOldPlan.customer_plan_id;
          }
          scope.showLoading();
          await hrSettings.getActivePlanDetails(data).success(function (response) {
            scope.activePlanDetails_pagination = response;
            scope.employee_acount_details = response.data.employee_acount_details;
            scope.dependent_acount_details = response.data.dependent_acount_details;

            scope.getEnrollmentHistoryData();
            scope.getInvoiceHistoryData();
          });
        };

        scope.customFormatDate = function (date, from, to) {
          return moment(date, from).format(to);
        };

        scope.getOldPlansList = async function () {
          // scope.showLoading();
          await hrSettings.getOldPlanList().success(function (response) {
            scope.oldPlan_list = response.data;
          });
        };

        // Active Plan pagination and Old Plan list //
        scope._toggleActivePlanDrop_ = function () {
          scope.isActivePlanDropShow = scope.isActivePlanDropShow ? false : true;
        };
        scope._toggleOldActivePlanDrop_ = function () {
          scope.isOldPlanListDropShow = scope.isOldPlanListDropShow ? false : true;
        };
        $("body").click(function (e) {
          if ($(e.target).parents(".active-plan-dot").length === 0) {
            scope.isActivePlanDropShow = false;
            scope.$apply();
          }
          if ($(e.target).parents(".old-active-plans-wrapper").length === 0) {
            scope.isOldPlanListDropShow = false;
            scope.$apply();
          }
        });
        scope.selectActivePlanPage = function (page) {
          if (scope.activePlan_active_page != page) {
            scope.activePlan_active_page = page;
            scope.isActivePlanDropShow = false;
            scope.getPlanDetails();
          }
        };
        scope.selectOldPlanPage = function (page, plan) {
          scope.selectedOldPlan = plan;
          scope.activePlan_active_page = 1;
          scope.isOldPlanListDropShow = false;
          scope.oldPlan_active_page = page;
          scope.getPlanDetails();
        };
        scope._selectCurrentPlan_ = function () {
          scope.activePlan_active_page = 1;
          scope.selectedOldPlan = null;
          scope.oldPlan_active_page = null;
          scope.getPlanDetails();
        };
        // ---------------------------------------- //
        scope._downloadInvoiceHistoryPDF_ = function () {
          window.open(
            serverUrl.url +
              `/hr/plan_all_download?token=` +
              window.localStorage.getItem("token") +
              `&customer_active_plan_id=` +
              scope.activePlanDetails_pagination.data.customer_active_plan_id,
            "_blank"
          );
        };

        scope._editDetails_ = function (type, planData) {
          console.log(planData);
          scope.global_planData = planData;
          scope.global_planData.type = type;
          // scope.global_planData.invoice_date = scope.global_planData.plan_start;

          // scope.global_planData.invoice_date = moment(scope.global_planData.invoice_date).add(scope.global_planData.duration, 'months').subtract(1, 'days');

          scope.global_planData.plan_start = moment(scope.global_planData.plan_start, ["YYYY-MM-DD", "DD/MM/YYYY"]).format("DD/MM/YYYY");
          scope.global_planData.invoice_date = moment(scope.global_planData.invoice_date, ["YYYY-MM-DD", "DD/MM/YYYY"]).format("DD/MM/YYYY");
          scope.global_planData.invoice_due = moment(scope.global_planData.invoice_due, ["YYYY-MM-DD", "DD/MM/YYYY"]).format("DD/MM/YYYY");

          setTimeout(() => {
            var dt = new Date();
            // dt.setFullYear(new Date().getFullYear()-18);
            $(".datepicker").datepicker({
              format: "dd/mm/yyyy",
              endDate: dt,
            });

            $(".datepicker")
              .datepicker()
              .on("hide", function (evt) {
                var val = $(this).val();
                if (val != "") {
                  $(this).datepicker("setDate", val);
                }
              });
          }, 300);
        };

        scope._changePlanDuration_ = function (duration, start) {
          let year = moment(start, "DD/MM/YYYY").year();
          let month = moment(start, "DD/MM/YYYY").month();
          let day = moment(start, "DD/MM/YYYY").date();
          let new_invoice_start = start;
          let new_invoice_due = moment([year, month, day]).add(parseInt(duration), "months").subtract(1, "days").format("DD/MM/YYYY");
          // scope.edit_employee_acount_details.invoice_start = new_invoice_start;
          scope.global_planData.invoice_date = new_invoice_due;
        };

        scope._updatePlanDetails_ = function (formData) {
          console.log(formData);
          formData.plan_start = moment(formData.plan_start, ["YYYY-MM-DD", "DD/MM/YYYY"]).format("YYYY-MM-DD");
          let data = {};
          if (formData.account_type == "enterprise_plan") {
            formData.invoice_date = moment(formData.invoice_date, ["YYYY-MM-DD", "DD/MM/YYYY"]).format("YYYY-MM-DD");
            formData.invoice_due = moment(formData.invoice_due, ["YYYY-MM-DD", "DD/MM/YYYY"]).format("YYYY-MM-DD");
            data = {
              start_date: formData.plan_start,
              plan_duration: formData.duration,
              invoice_start: formData.invoice_date,
              invoice_due: formData.invoice_due,
              individual_price: formData.individual_price,
            };
          } else {
            data = {
              start_date: formData.plan_start,
              plan_duration: formData.duration,
            };
          }
          scope.showLoading();
          if (formData.type == "employee") {
            data.customer_active_plan_id = scope.activePlanDetails_pagination.data.customer_active_plan_id;
            hrSettings.updateEmployeePlan(data).then(function (response) {
              console.log(response);
              if (response.data.status) {
                $(".modal").modal("hide");
                scope.getPlanDetails();
              } else {
                swal("Error!", response.data.message, "error");
              }
            });
          }
          if (formData.type == "dependent") {
            data.dependent_plan_id = formData.dependent_plan_id;
            hrSettings.updateDependentPlan(data).then(function (response) {
              console.log(response);
              if (response.data.status) {
                $(".modal").modal("hide");
                scope.getPlanDetails();
              } else {
                swal("Error!", response.data.message, "error");
              }
            });
          }
        };
        scope._planTypeChanged_ = function (plan_type) {
          if (plan_type == "enterprise_plan") {
            setTimeout(() => {
              var dt = new Date();
              // dt.setFullYear(new Date().getFullYear()-18);
              $(".datepicker").datepicker({
                format: "dd/mm/yyyy",
                endDate: dt,
              });

              $(".datepicker")
                .datepicker()
                .on("hide", function (evt) {
                  var val = $(this).val();
                  if (val != "") {
                    $(this).datepicker("setDate", val);
                  }
                });
            }, 300);
          }
        };

        // view member modal functions //

        scope._getEnrolledMemberList_ = function (list, search) {
          scope.selectedEnrollHistoryList = list;
          var data = {
            page: scope.mem_active_page,
            customer_active_plan_id: list.customer_active_plan_id,
            per_page: scope.mem_per_page,
          };
          if (search) {
            data.search = search;
          }
          scope.showLoading();
          hrSettings.getViewMemberModalList(data).then(function (response) {
            scope.hideLoading();
            console.log(response);
            if (response.data.status) {
              scope.viewMemberList = response.data.data.data;
              scope.viewMemberModalPagination = response.data.data;
              if (!scope.isViewMemberModalShow) {
                $("#viewMemberModal").modal("show");
                scope.isViewMemberModalShow = true;
              }
            } else {
              swal("Error!", response.data.message, "error");
            }
          });
        };
        scope.closeViewMemberModal = function () {
          scope.isViewMemberModalShow = false;
          $("#viewMemberModal").modal("hide");
        };
        scope.showDependentList = function (list) {
          if (list.dependents.length > 0) {
            list.showDependents = list.showDependents == true ? false : true;
          }
        };
        scope._toggleViewMemberPopUp = function () {
          scope.isViewMemberPerPageShow = scope.isViewMemberPerPageShow == false ? true : false;
        };
        scope._setViewMembersPageLimit_ = function (page) {
          scope.mem_per_page = page;
          scope.mem_active_page = 1;
          scope._getEnrolledMemberList_(scope.selectedEnrollHistoryList, scope.memberSearch);
        };
        scope._viewMembersSetPage_ = function (page) {
          scope.mem_active_page = page;
          scope._getEnrolledMemberList_(scope.selectedEnrollHistoryList, scope.memberSearch);
        };
        scope._viewMembersPrevPage_ = function (page) {
          if (scope.mem_active_page != 1) {
            scope.mem_active_page -= 1;
            scope._getEnrolledMemberList_(scope.selectedEnrollHistoryList, scope.memberSearch);
          }
        };
        scope._viewMembersNextPage_ = function (page) {
          // pagination.length + 1
          if (scope.mem_active_page != 10) {
            scope.mem_active_page += 1;
            scope._getEnrolledMemberList_(scope.selectedEnrollHistoryList, scope.memberSearch);
          }
        };

        scope.startViewMembersModalIndex = function () {
          if (scope.mem_active_page > scope.viewMemberModalpagesToDisplay / 2 + 1) {
            if (scope.mem_active_page + Math.floor(scope.viewMemberModalpagesToDisplay / 2) > scope.viewMemberModalPagination.last_page) {
              return scope.viewMemberModalPagination.last_page - scope.viewMemberModalpagesToDisplay + 1;
            }
            return scope.mem_active_page - Math.floor(scope.viewMemberModalpagesToDisplay / 2);
          }
          return 0;
        };

        $("body").click(function (e) {
          if ($(e.target).parents("#viewMemberModal .custom-list-per-page").length === 0) {
            scope.isViewMemberPerPageShow = false;
            scope.$apply();
          }
          if ($(e.target).parents(".enrollment-history-drop-wrapper").length === 0) {
            scope.hideEnrolActionDrops();
          }
        });
        $("#viewMemberModal").on("hidden.bs.modal", function (e) {
          scope.isViewMemberModalShow = false;
          scope.memberSearch = null;
        });

        scope.hideEnrolActionDrops = function () {
          if (scope.enrollment_history) {
            angular.forEach(scope.enrollment_history.data, function (value, key) {
              // console.log(value);
              if (value.isEnrolActionsShow == true) {
                value.isEnrolActionsShow = false;
                scope.$apply();
              }
            });
          }
        };
        // scope.newScheduleDate = new Date();
        scope._editScheduleDate_ = function (data) {
          console.log(data);
          scope.scheduleData = data;
          scope.scheduleData.schedule_date = moment(scope.scheduleData.schedule_date, ["YYYY-MM-DD", "DD/MM/YYYY"]).format("DD/MM/YYYY");
          document.getElementById("new-scheduled-date").value = "";

          setTimeout(() => {
            // var dt = new Date();
            // dt.setFullYear(new Date().getFullYear()-18);
            $(".datepicker").datepicker({
              format: "dd/mm/yyyy",
              // endDate: dt
            });

            $(".datepicker")
              .datepicker()
              .on("hide", function (evt) {
                var val = $(this).val();
                if (val != "") {
                  $(this).datepicker("setDate", val);
                }
              });
          }, 300);
        };

        scope._changeDate_ = function (date) {
          console.log(date);
          scope.new_scheduled_date = date.split("/").reverse().join("-");
          console.log(scope.new_scheduled_date);
        };

        scope.setScheduleDate = function () {
          let data = {
            id: scope.scheduleData.id,
            schedule_date: scope.new_scheduled_date,
          };
          scope.showLoading();
          hrSettings.updateScheduleDate(data).then(function (response) {
            console.log(response);
            if (response.data.status) {
              // scope.hideLoading();
              $("#edit_scheduled_modal").modal("hide");
              document.getElementById("new-scheduled-date").value = "";
              scope.getEnrollmentHistoryData();
            } else {
              scope.hideLoading();
              swal("Error!", response.data.message, "error");
            }
          });
        };

        // ---------------------------------------- //

        scope.initializeGeoCode = function () {
          var settings = {
            preferredCountries: [],
            separateDialCode: true,
            initialCountry: scope.updateHrData.phone_code == "+60" ? "SG" : "MY",
            autoPlaceholder: "off",
            utilsScript: "../assets/hr-dashboard/js/utils.js",
            onlyCountries: ["sg", "my"],
          };

          var input = document.querySelector("#phone_number");
          iti1 = intlTelInput(input, settings);

          input.addEventListener("countrychange", function () {
            console.log(iti1.getSelectedCountryData());
            scope.updateHrData.phone_code = "+" + iti1.getSelectedCountryData().dialCode;
            scope.isChangedInput = true;
          });

        };

        scope.spending_account_status = {};
        scope.getSpendingAcctStatus = function () {
          hrSettings.getPrePostStatus().then(function (response) {
            scope.spending_account_status = response.data;
          });
        };

        scope.initializeChangePrimaryAdminCountryCode = function(){
          var settings = {
            preferredCountries: [],
            separateDialCode: true,
            initialCountry: false,
            autoPlaceholder: "off",
            utilsScript: "../assets/hr-dashboard/js/utils.js",
            onlyCountries: ["sg", "my"],
          };

          var input = document.querySelector("#phone_number_primary_admin");
          primaryAdminCountry = intlTelInput(input, settings);
          primaryAdminCountry.setCountry("SG");
          input.addEventListener("countrychange", function () {
            scope.changePrimaryData.phone_code = primaryAdminCountry.getSelectedCountryData().dialCode;
          });
        }
        scope.checkEmail = function (email) {
					var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					return regex.test(email);
				}
        scope.submitChangePrimaryAdmin  = function(){
          if( scope.checkEmail(scope.changePrimaryData.email) == false ){
            return swal('Error!', 'Invalid Email', 'error');
          }
          var data  = {
            id: scope.global_hrData.id,
            hr_id: scope.global_hrData.id,
            fullname: scope.changePrimaryData.fullname,
            email: scope.changePrimaryData.email,
            phone_code: scope.changePrimaryData.phone_code,
            phone_no: scope.changePrimaryData.phone_no,
            customer_id: scope.spending_account_status.customer_id,
            action_type: 'change_primary',
          }
          scope.showLoading();
          $http.post(serverUrl.url + "/hr/unlink/company_account", data)
            .then(function(response){
              console.log(response);
              scope.hideLoading();
              if(response.data.status){
                scope.changeAdmin();
              }else{
                swal("Error!", response.data.message, 'error');
              }
            });
        }
        scope.limit_link = 5;
        scope.page_link = 1;
        
        scope.getLinkedAccount = async function () {
          await hrSettings.fetchLinkAccount( scope.limit_link,scope.page_link,'enable' ).then(function (response) {
            console.log(response);
            scope.link_account_data = response.data;
          });
        }

        scope.linkAction = function (data,index) {
          scope.link_account_data.data.map((value,key)  => {
            if ( index == key ) {
              value.isUnlinkShow = value.isUnlinkShow == true ? false : true;
            } 
          })
        }

        scope.unlinkAccount = async function ( data ) {
          if ( scope.get_permissions_data.create_remove_edit_admin_unlink_account == 1 ) {
            $('#unlink_account').modal('show');
            console.log(data);
            scope.link_account_id = data.id;
            scope.link_hr_id = data.hr_id;
            scope.unlinkData = {
              phone_code: '65',
              hr_id: data.hr_id,
              customer_id: data.company_id,
              account_name: data.account_name
            }
            await scope.initializeUnlinkCountryCode();
          } else {
            $('#permission-modal').modal('show');
            $('#unlink_account').modal('hide');
          } 
        }

        scope.initializeUnlinkCountryCode = function(){
          var settings = {
            preferredCountries: [],
            separateDialCode: true,
            initialCountry: false,
            autoPlaceholder: "off",
            utilsScript: "../assets/hr-dashboard/js/utils.js",
            onlyCountries: ["sg", "my"],
          };

          var input = document.querySelector("#unlink_mobile_number");
          primaryAdminCountry = intlTelInput(input, settings);
          primaryAdminCountry.setCountry("SG");
          input.addEventListener("countrychange", function () {
            scope.unlinkData.phone_code = primaryAdminCountry.getSelectedCountryData().dialCode;
          });
        }

        scope._switchAccount_	=	function(data){
          console.log(data);
          $http.get(window.location.origin + '/hr/login_company_linked?id=' + data.id + '&token=' + localStorage.getItem('token'))
            .success(function(response){
              console.log(response);
              if(response.status){
                window.localStorage.setItem('token', response.token);
                window.location.href = window.location.origin + "/company-benefits-dashboard/";
              }else{
                swal('Error!', response.message, 'error');
              }
            });
        }

        scope._updateLink_ = async function ( ) {
          let data = {
            id: scope.link_account_id,
            hr_id: scope.unlinkData.hr_id,
            customer_id: scope.unlinkData.customer_id,
            fullname: scope.unlinkData.full_name,
            email: scope.unlinkData.email,
            phone_code: scope.unlinkData.phone_code,
            phone_no: scope.unlinkData.mobile_number,
            action_type: 'unlink',
          }
          if( scope.checkEmail(data.email) == false ){
            return swal('Error!', 'Invalid Email', 'error');
          }
          scope.showLoading();
          await hrSettings.updateUnlinkAccount( data ).then(async function (response) {
            console.log(response);
            scope.updatedLinkData = response.data;
            if (response.data.status) {
              scope.unlinkSuccessfullyUpdated = true;
              scope.hideLoading();
              await scope.getLinkedAccount();
            } else {
              return swal('Error!', response.data.message, 'error');
            }
          });
        }

        scope.getPermissionsData = async function () {
          await hrSettings.getPermissions()
            .then( function (response) {
              console.log(response);
              scope.get_permissions_data = response.data.data;
          });
        }

        scope.range = function (num) {
          var arr = [];
          for (var i = 0; i < num; i++) {
            arr.push(i);
          }
          return arr;
        };

        scope.showLoading = function () {
          $(".circle-loader").show();
        };

        scope.hideLoading = function () {
          $timeout(function () {
            $(".circle-loader").fadeOut();
          }, 100);
        };

        $('.modal').on('hidden.bs.modal', function (e) {
          scope.isChangedInput = false;
          scope.isPasswordInputChanged = false;
        })

        scope.onLoad = async function () {
          scope.getSpendingAcctStatus();
          await scope._getHrDetails_();
          await scope.getOldPlansList();
          await scope.getPlanDetails();
          await scope.getLinkedAccount();
          await scope.getPermissionsData();
        };
        scope.onLoad();
      },
    };
  },
]);
