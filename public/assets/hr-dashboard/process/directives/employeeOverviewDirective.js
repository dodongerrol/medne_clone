app.directive("employeeOverviewDirective", [
  "$state",
  "hrSettings",
  "hrActivity",
  "$rootScope",
  "dashboardFactory",
  "dependentsSettings",
  "$timeout",
  "serverUrl",
  function directive($state, hrSettings, hrActivity, $rootScope, dashboardFactory, dependentsSettings, $timeout, serverUrl) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("employeeOverviewDirective Runnning !");

        scope.default_currency_type = localStorage.getItem('currency_item');
        scope.employees = {};
        scope.options = {};
        scope.page_ctr = 5;
        scope.page_active = 1;
        scope.emp_arr = [];
        scope.plan_status = {};
        scope.company_properties = {};
        scope.company_properties.total_allocation = 0.00;
        scope.company_properties.allocated = 0.00;
        scope.family_trap = false;
        scope.family_temp = null;
        scope.family_selected = null;

        scope.isAddDependentsShow = false;
        scope.isEmployeeShow = false;
        scope.job_list = [];
        scope.selectedEmployee_index = null;
        scope.selectedEmployee = {};
        scope.selectedDependent = {};
        scope.dependent_data = {};
        scope.isTierDetailsShow = false;
        scope.isMedicalUsageShow = false;
        scope.isWellnessUsageShow = false;
        scope.empTabSelected = 0;
        scope.nric_status = true;
        scope.fin_status = false;
        scope.addDependents_arr = [];
        scope.addActiveDependent_index = 0;
        scope.selected_customer_id = null;
        scope.nric_status_dependents = true;
        scope.fin_status_dependents = false;
        scope.refund_status = false;
        scope.remove_employee_data = {
          last_day_coverage: moment().add('days', 1).format('DD/MM/YYYY')
        }
        scope.replace_emp_data = {};
        scope.reserve_emp_date = moment().add('days', 1).format('DD/MM/YYYY');
        scope.update_member_wallet_status = null;

        scope.isRemoveEmployeeShow = false;
        scope.isRemoveEmployeeOptionsShow = false;
        scope.isHealthSpendingAccountSummaryShow = false;
        scope.isRefundSummaryShow = false;
        scope.isHealthSpendingAccountShow = false;
        scope.isReplaceEmpShow = false;
        scope.isReserveEmpShow = false;
        scope.isDeleteDependent = false;
        scope.dependents_ctr = 0;
        scope.cap_per_visit = 0;
        scope.isUpdateEmpInfoModalOpen = false;
        scope.statementHide = true;
        scope.empStatementShow = false;
        scope.arrowStatement = false;
        scope.litePlanCheckbox = false;
        scope.hideLitePlanCheckbox = true;
        scope.isBasicPlan = false;
        scope.showBlockHealthProviders = false;
        scope.entitlement_data = {};
        scope.dropdownEntitlement = {
          medical: false,
          wellness: false,
          med_alloc_formula: false,
          well_alloc_formula: false,
        };
        var iti = null;


        scope.$on("refresh", function (evt, data) {
          scope.onLoad();
          scope.emp_arr = [];
        });

        scope.emp_entitlement = {
          original_medical_entitlement: null,
          original_wellness_entitlement: null,
          medical_new_entitlement: null,
          wellness_new_entitlement: null
        };
        scope.entitlement_status = {};
        scope.calc_entitlement_med = {};
        scope.calc_entitlement_well = {};
        scope.proration = {};
        scope.medicalCalculatedInfo = false;
        scope.wellnessCalculatedInfo = false;
        scope.effectiveMedDateError = false;
        scope.effectiveWellDateError = false;
        scope.credit_status = {};

        scope.pagesToDisplay = 5;
        scope.startIndex = function () {
          if (scope.page_active > ((scope.pagesToDisplay / 2) + 1)) {
            if ((scope.page_active + Math.floor(scope.pagesToDisplay / 2)) > scope.employees.last_page) {
              return scope.employees.last_page - scope.pagesToDisplay + 1;
            }
            return scope.page_active - Math.floor(scope.pagesToDisplay / 2);
          }
          return 0;
        }

        scope.companyAccountType = function () {
          scope.account_type = localStorage.getItem('company_account_type');
          console.log(scope.account_type);

          if (scope.account_type === 'enterprise_plan') {
            $('.statement-hide').hide();
            scope.statementHide = false;
            scope.empStatementShow = true;
            scope.arrowStatement = true;
          }
        }


        scope.manageCap = function () {
          $("#manage-cap-modal").modal('show');
        }
        scope.settingsShow = function (data) {
          let x = data;

          if (x === 'open') {
            console.log('gawas ang open');
            scope.showBlockHealthProviders = true;
            scope.blockHealthPatnerLoad();
          } else if (x === 'close') {
            scope.showBlockHealthProviders = false;
          }
        }



        //---------- HEALTH PROVIDER BLOCK ------------//
        scope.clinic_blocked_search_trap = false;
        scope.clinic_opened_search_trap = false;
        scope.settings_active = 1;
        scope.isBlockSearch = false;
        scope.isOpenSearch = false;
        scope.transaction_ctr = 0;
        scope.search = {
          clinic_open_search_text: '',
          clinic_blocked_search_text: '',
        }
        scope.per_page_arr = [10, 20, 30, 40, 50, 100];
        //-- blocked --//
        scope.clinic_type_block_ids = [];
        scope.clinic_block_arr = [];
        scope.clinic_type_block_arr = [];
        scope.block_pagination = {};
        scope.block_page_active = 1;
        scope.block_per_page = 10;
        scope.filter_regionBlocked = 'all_region';
        scope.allBlockSelected = false;
        scope.list_opt_block = 'type';
        //-------------//

        //-- opened --//
        scope.clinic_type_open_ids = [];
        scope.clinic_open_arr = [];
        scope.clinic_type_open_arr = [];
        scope.open_pagination = {};
        scope.open_page_active = 1;
        scope.open_per_page = 10;
        scope.filter_regionOpened = 'all_region';
        scope.allOpenSelected = false;
        scope.list_opt_open = 'type';
        //-------------//
        scope.range = function (range) {
          var arr = [];
          for (var i = 0; i < range; i++) {
            arr.push(i + 1);
          }
          return arr;
        }
        scope.hideDropDowns = function () {
          $('.blocked-page-scroll').hide();
          $('.blocked-per-page-scroll').hide();
          $('.opened-page-scroll').hide();
          $('.opened-per-page-scroll').hide();
        }
        scope.searchClinics = function (search, opt) {
          if (search != "") {
            if (opt == 'block') {
              scope.block_page = 1;
              scope.block_per_page = 10;
              scope.getBlockedClinics();
            }
            if (opt == 'open') {
              scope.open_page = 1;
              scope.open_per_page = 10;
              scope.getOpenedClinics();
            }
          } else {
            scope.clinic_blocked_search_trap = false;
            scope.clinic_opened_search_trap = false;
            scope.getBlockedClinics();
            scope.getOpenedClinics();
          }
        }
        scope.changeFilterType = function (type) {
          if (type == 'open') {
            scope.resetOpenCheckBoxes();
          } else {
            scope.resetBlockCheckBoxes();
          }
        }
        scope.regionOpt = function (opt, source) {
          if (source == 'open') {
            scope.filter_regionOpened = opt;
            if (opt == 'all_region') {
              scope.filterByRegionOpened = undefined;
            } else if (opt == 'sgd') {
              scope.filterByRegionOpened = 'Singapore';
            } else if (opt == 'myr') {
              scope.filterByRegionOpened = 'Malaysia';
            }
          } else if (source == 'blocked') {
            scope.filter_regionBlocked = opt;
            if (opt == 'all_region') {
              scope.filterByRegionBlocked = undefined;
            } else if (opt == 'sgd') {
              scope.filterByRegionBlocked = 'Singapore';
            } else if (opt == 'myr') {
              scope.filterByRegionBlocked = 'Malaysia';
            }
          }
          scope.blockHealthPatnerLoad();
        }
        scope.showPageScroll = function (data) {
          let x = data;
          if (x === 'blocked_page') {
            $('.blocked-page-scroll').show();
          }
          if (x === 'blocked_per_page') {
            $('.blocked-per-page-scroll').show();
          }
          if (x === 'opened-page-scroll') {
            $('.opened-page-scroll').show();
          }
          if (x === 'opened-per-page-scroll') {
            $('.opened-per-page-scroll').show();
          }

          $("body").click(function (e) {
            if ($(e.target).parents(".page-blocked").length === 0) {
              $(".blocked-page-scroll").hide();
            }
            if ($(e.target).parents(".rows-per-page-blocked").length === 0) {
              $(".blocked-per-page-scroll").hide();
            }
            if ($(e.target).parents(".page-opened").length === 0) {
              $(".opened-page-scroll").hide();
            }
            if ($(e.target).parents(".rows-per-page-opened").length === 0) {
              $(".opened-per-page-scroll").hide();
            }
          });
        }
        scope.toggleBlockedClinicSearch = function () {
          if (scope.clinic_blocked_search_trap == false) {
            scope.clinic_blocked_search_trap = true;
          } else {
            scope.clinic_blocked_search_trap = false;
            scope.blockHealthPatnerLoad();
          }
        }
        scope.toggleOpenedClinicSearch = function () {
          if (scope.clinic_opened_search_trap == false) {
            scope.clinic_opened_search_trap = true;
          } else {
            scope.clinic_opened_search_trap = false;
            scope.blockHealthPatnerLoad();
          }
        }
        scope.toggleAllBlockedClinic = function (opt) {
          scope.allBlockSelected = opt;
          var arr = scope.list_opt_block == 'name' ? scope.clinic_block_arr : scope.clinic_type_block_arr;
          if (scope.allBlockSelected == true) {
            angular.forEach(arr, function (value, key) {
              value.selected = true;
            });
          } else {
            angular.forEach(arr, function (value, key) {
              value.selected = false;
            });
          }
        }
        scope.toggleAllOpenedClinic = function (opt) {
          scope.allOpenSelected = opt;
          var arr = scope.list_opt_open == 'name' ? scope.clinic_open_arr : scope.clinic_type_open_arr;
          if (scope.allOpenSelected == true) {
            scope.allOpenSelected = true;
            angular.forEach(arr, function (value, key) {
              value.selected = true;
            });
          } else {
            scope.allOpenSelected = false;
            angular.forEach(arr, function (value, key) {
              value.selected = false;
            });
          }
        }
        scope.resetOpenCheckBoxes = function () {
          scope.clinic_type_open_ids = [];
          scope.allOpenSelected = false;
          angular.forEach(scope.clinic_type_open_arr, function (value, key) {
            value.selected = false;
          });
          angular.forEach(scope.clinic_open_arr, function (value, key) {
            value.selected = false;
          });
        }
        scope.resetBlockCheckBoxes = function () {
          scope.clinic_type_block_ids = [];
          scope.allBlockSelected = false;
          angular.forEach(scope.clinic_type_block_arr, function (value, key) {
            value.selected = false;
          });
          angular.forEach(scope.clinic_block_arr, function (value, key) {
            value.selected = false;
          });
        }

        scope.blockHealthPatnerLoad = function () {
          scope.search = {
            clinic_open_search_text: '',
            clinic_blocked_search_text: '',
          }
          scope.resetOpenCheckBoxes();
          scope.resetBlockCheckBoxes();
          scope.getClinicTypes();
          scope.getBlockedClinics();
        }



        // -- PAGINATION FUNCTIONS -- //
        scope.nextPageBlock = function () {
          if (scope.block_page_active != scope.block_pagination.last_page) {
            scope.block_page_active++;
            scope.getBlockedClinics();
            scope.getOpenedClinics();
          }
        }
        scope.backPageBlock = function () {
          if (scope.block_page_active != 1) {
            scope.block_page_active--;
            scope.getBlockedClinics();
            scope.getOpenedClinics();
          }
        }
        scope.perPageBlock = function (page) {
          scope.hideDropDowns();
          scope.block_per_page = page;
          scope.block_page_active = 1;
          scope.getBlockedClinics();
          scope.getOpenedClinics();
        }
        scope.pageBlock = function (page) {
          scope.hideDropDowns();
          scope.block_page_active = page;
          scope.getBlockedClinics();
          scope.getOpenedClinics();
        }

        scope.nextPageOpen = function () {
          if (scope.open_page_active != scope.open_pagination.last_page) {
            scope.open_page_active++;
            scope.getBlockedClinics();
            scope.getOpenedClinics();
          }
        }
        scope.backPageOpen = function () {
          if (scope.open_page_active != 1) {
            scope.open_page_active--;
            scope.getBlockedClinics();
            scope.getOpenedClinics();
          }
        }
        scope.perPageOpen = function (page) {
          scope.hideDropDowns();
          scope.open_per_page = page;
          scope.open_page_active = 1;
          scope.getBlockedClinics();
          scope.getOpenedClinics();
        }
        scope.pageOpen = function (page) {
          scope.hideDropDowns();
          scope.open_page_active = page;
          scope.getBlockedClinics();
          scope.getOpenedClinics();
        }
        // --------------------------- // 
        scope.openToBlock = function (status, region, opt) {
          if (opt == 'name') {
            var ctr = 0;
            let toBlockArr = [];
            angular.forEach(scope.clinic_open_arr, function (value, key) {
              if (value.selected) {
                ctr += 1;
                scope.showLoading();
                toBlockArr.push(value.ClinicID)
                // Comment if ever got issue with this logic can revert back again to this logic
                // scope.updateClinics(value.ClinicID, status, region, opt);
              }
              if (ctr > 0 && scope.clinic_open_arr.length - 1 == key) {
                scope.updateClinics(toBlockArr, status, region, opt);
                // Comment if ever got issue with this logic can revert back again to this logic
                // scope.blockHealthPatnerLoad();
                // swal('Success!', 'Clinic Block Lists updated.', 'success');
                scope.hideLoading();
              } else if (ctr == 0 && scope.clinic_open_arr.length - 1 == key) {
                swal('Error!', 'Please Select a clinic first.', 'error');
              }
            });
            if (scope.clinic_open_arr.length == 0) {
              swal('Error!', 'Please Select a clinic first.', 'error');
            }
          }
          if (opt == 'type') {
            var ctr = 0;
            angular.forEach(scope.clinic_type_open_arr, function (value, key) {
              if (value.selected) {
                ctr += 1;
                scope.showLoading();
                scope.clinic_type_block_ids.push(value.ClinicTypeID);
              }
              if (ctr > 0 && scope.clinic_type_open_arr.length - 1 == key) {
                scope.updateClinics(scope.clinic_type_block_ids, status, region, opt);
              } else if (ctr == 0 && scope.clinic_type_open_arr.length - 1 == key) {
                swal('Error!', 'Please Select a clinic type first.', 'error');
              }
            });
            if (scope.clinic_type_open_arr.length == 0) {
              swal('Error!', 'Please Select a clinic type first.', 'error');
            }
          }
        }
        scope.blockToOpen = function (status, region, opt) {
          if (opt == 'name') {
            var ctr = 0;
            let toOpenArr = [];
            angular.forEach(scope.clinic_block_arr, function (value, key) {
              if (value.selected) {
                ctr += 1;
                scope.showLoading();
                toOpenArr.push(value.ClinicID);
                // Comment if ever got issue with this logic can revert back again to this logic
                // scope.updateClinics(value.ClinicID, status, region, opt);
              }
              if (ctr > 0 && scope.clinic_block_arr.length - 1 == key) {
                scope.updateClinics(toOpenArr, status, region, opt);
                // Comment if ever got issue with this logic can revert back again to this logic
                // scope.blockHealthPatnerLoad();
                // swal('Success!', 'Clinic Block Lists updated.', 'success');
                scope.hideLoading();
              } else if (ctr == 0 && scope.clinic_block_arr.length - 1 == key) {
                swal('Error!', 'Please Select a clinic first.', 'error');
              }
            });
            if (scope.clinic_block_arr.length == 0) {
              swal('Error!', 'Please Select a clinic first.', 'error');
            }
          }
          if (opt == 'type') {
            var ctr = 0;
            angular.forEach(scope.clinic_type_block_arr, function (value, key) {
              if (value.selected) {
                ctr += 1;
                scope.showLoading();
                scope.clinic_type_open_ids.push(value.ClinicTypeID);
              }
              if (ctr > 0 && scope.clinic_type_block_arr.length - 1 == key) {
                scope.updateClinics(scope.clinic_type_open_ids, status, region, opt);
              } else if (ctr == 0 && scope.clinic_type_block_arr.length - 1 == key) {
                swal('Error!', 'Please Select a clinic type first.', 'error');
              }
            });
            if (scope.clinic_type_block_arr.length == 0) {
              swal('Error!', 'Please Select a clinic type first.', 'error');
            }
          }
        }
        scope.updateClinics = function (id, status, region, type) {
          var data = {
            user_id: scope.selectedEmployee.user_id,
            access_status: status == 0 ? 'open' : 'block',
            region: region,
            clinic_id: id,
            clinic_type_id: id,
            status: status,
            type: type == 'name' ? 'clinic_name' : 'clinic_type',
          }
          hrActivity.OpenBlockClinicsEmp(data)
            .then(function (response) {
              console.log(response);
              if (response.data.status) {
                if (type == 'type') {
                  swal('Success!', response.data.message, 'success');
                  scope.blockHealthPatnerLoad();
                  scope.hideLoading();
                }
              } else {
                swal('Error!', response.data.message, 'error');
              }
            });
        }
        scope.getClinicTypes = function () {
          hrActivity.fetchClinicTypesEmp('open', scope.filter_regionOpened, scope.selectedEmployee.user_id)
            .then(function (response) {
              console.log(response);
              scope.clinic_type_open_arr = response.data;
            });
          hrActivity.fetchClinicTypesEmp('block', scope.filter_regionBlocked, scope.selectedEmployee.user_id)
            .then(function (response) {
              console.log(response);
              scope.clinic_type_block_arr = response.data;
            });
        }
        scope.getBlockedClinics = function () {
          scope.showLoading();
          hrActivity.fetchBlockedClinicsEmp(scope.block_per_page, scope.block_page_active, scope.filter_regionBlocked, scope.search.clinic_blocked_search_text, scope.selectedEmployee.user_id)
            .then(function (response) {
              // console.log(response);
              if (scope.search.clinic_blocked_search_text == null || scope.search.clinic_blocked_search_text == '') {
                scope.clinic_block_arr = response.data.data;
                scope.block_pagination = response.data;
                scope.isBlockSearch = false;
              } else {
                scope.clinic_block_arr = response.data;
                scope.isBlockSearch = true;
              }
              scope.getOpenedClinics();
            });
        }
        scope.getOpenedClinics = function () {
          hrActivity.fetchOpenedClinicsEmp(scope.open_per_page, scope.open_page_active, scope.filter_regionOpened, scope.search.clinic_open_search_text, scope.selectedEmployee.user_id)
            .then(function (response) {
              // console.log(response);
              if (scope.search.clinic_open_search_text == null || scope.search.clinic_open_search_text == '') {
                scope.clinic_open_arr = response.data.data;
                scope.open_pagination = response.data;
                scope.isOpenSearch = false;
              } else {
                scope.clinic_open_arr = response.data;
                scope.isOpenSearch = true;
              }
              scope.hideLoading();
            });
        }

        // --------------------------------------------- //





        scope.submitCapPerVisit = function (cap) {
          scope.showLoading();
          var data = {
            employee_id: scope.selectedEmployee.user_id,
            cap_amount: cap,
          }
          hrSettings.updateCapPerVisit(data)
            .then(function (response) {
              scope.hideLoading();
              if (response.data.status) {
                scope.cap_per_visit = 0;
                swal('Success!', response.data.message, 'success');
                $("#manage-cap-modal").modal('hide');
              } else {
                swal('Error!', response.data.message, 'error');
              }
            });
        }

        scope.gotToOverview = function () {
          scope.isAddDependentsShow = false;
          scope.isEmployeeShow = false;
          $('.prev-next-buttons-container').hide();
          $('.remove-employee-wrapper').hide();
          $('.add-dependent-wrapper').hide();
          $(".hrdb-body-container").fadeIn();
          $(".employee-information-wrapper").hide();
          $('body').scrollTop(0);
          scope.reset();
          $timeout(function () {
            $('body').css('overflow', 'hidden');
          }, 200);
        }

        scope.resendResetAccount = function () {
          swal({
            title: "Confirm!",
            text: "Are you sure you want to resend and reset the password for this account?",
            type: "info",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            confirmButtonColor: "#0392CF",
            closeOnConfirm: true,
            customClass: "updateEmp",
          },
            function (isConfirm) {
              if (isConfirm) {
                $('#input-pass').modal('show');
              }
            });
        }

        scope.confirmPassword = function (pass) {
          if (pass) {
            var data = {
              password: pass
            }
            hrSettings.sendPassword(data)
              .then(function (response) {
                if (response.data.status) {
                  $('#input-pass').modal('hide');
                  scope.sendResetAccount();
                  scope.passCredit = null;
                } else {
                  swal('Error!', response.data.message, 'error');
                }
              });
          } else {
            swal('Error!', 'Please input password.', 'error');
          }
        }

        scope.sendResetAccount = function () {
          var data = {
            employee_id: scope.selectedEmployee.user_id
          }
          scope.showLoading();
          hrSettings.resetAccount(data)
            .then(function (response) {
              // console.log( response );
              if (response.data.status) {
                swal('Success!', response.data.message, 'success');
              } else {
                swal('Error!', response.data.message, 'error');
              }
              scope.hideLoading();
            });
        }

        scope.goToBulkAllocation = function () {
          $state.go('cred-allocation');
        }

        scope.spending_account_status = {};
        scope.getSpendingAcctStatus = function () {
          // hrSettings.getSpendingAccountStatus()
          hrSettings.getPrePostStatus()
						.then(function (response) {
							console.log(response);
              scope.spending_account_status = response.data;
              scope.checkSpendingValuesStatus();
						});
        }

        scope.enrollMoreEmployees = function () {
          // localStorage.setItem('fromEmpOverview', false);
          // $state.go('create-team-benefits-tiers');
          // $state.go('enrollment-options');
          localStorage.setItem('fromEmpOverview', true);

          hrSettings.getSpendingAccountStatus()
						.then(function (response) {
							console.log(response);
              var spending_account_status = response.data;

              // if(spending_account_status.medical == true || spending_account_status.wellness == true) {
              //   $state.go('enrollment-options');
              //   $('body').css('overflow', 'auto');
              // } else {
              //   $state.go( 'create-team-benefits-tiers' );
              //   $('body').css('overflow', 'auto');
              // }
              // $state.go('enrollment-options');
              // $('body').css('overflow', 'auto');
              
              $state.go( 'create-team-benefits-tiers' );
              $('body').css('overflow', 'auto');
						});
        }

        scope.resetRemoveBtn  = function(){
          scope.reset();
          scope.isEmployeeShow = true;
          scope.getSession();
        }

        scope.removeBtn = function () {
          scope.showLoading();
          $('.employee-information-wrapper').hide();
          // $('.prev-next-buttons-container').fadeIn();
          // $('.remove-employee-wrapper').fadeIn();
          scope.selectedEmployee.last_day_coverage = moment().add('days', 1).format('DD/MM/YYYY');
          scope.reset();
          scope.isRemoveEmployeeShow = true;
          scope.isDeleteDependent = false;
          $state.go('employee-overview.remove-emp-inputs');
        }

        scope.removeDependentBtn = function (data) {
          // console.log( data );
          $('.employee-information-wrapper').hide();
          $('.prev-next-buttons-container').fadeIn();
          $('.remove-employee-wrapper').fadeIn();
          scope.reset();
          scope.isRemoveEmployeeShow = true;
          scope.isDeleteDependent = true;
          scope.selectedDependent = data;
        }

        scope.getUsage = function (x, y) {
          if( x && y ){
            var a = x.toString().replace(',','');
            var b = y.toString().replace(',','');
            return (parseFloat(a) + parseFloat(b));
          }else{
            return x + y;
          }
          
        }

        scope.range = function (range) {
          var arr = [];
          for (var i = 0; i < range; i++) {
            arr.push(i);
          }
          return arr;
        }

        scope.showFamily = function (emp, evt) {
          scope.family_selected = emp.user_id;

          if (emp.family_coverage.dependents.length > 0 || emp.family_coverage.spouse.spouse) {
            if (scope.family_trap == false) {
              if (emp.family_coverage.spouse.spouse) {
                var duration = emp.family_coverage.spouse.spouse.duration.split(" ");
                emp.family_coverage.spouse.spouse.plan_start = moment(emp.family_coverage.spouse.spouse.plan_start).format("MM/DD/YYYY");
                emp.family_coverage.spouse.spouse.plan_end = moment(emp.family_coverage.spouse.spouse.plan_start).add(duration[0], duration[1]).format("MM/DD/YYYY");
              }

              for (var i = emp.family_coverage.dependents.length - 1; i >= 0; i--) {
                var duration2 = emp.family_coverage.dependents[i].plan.duration.split(" ");
                emp.family_coverage.dependents[i].plan.plan_start = moment(emp.family_coverage.dependents[i].plan.plan_start).format("MM/DD/YYYY");
                emp.family_coverage.dependents[i].plan.plan_end = moment(emp.family_coverage.dependents[i].plan.plan_start).add(duration2[0], duration2[1]).format("MM/DD/YYYY");

                $(`<tr class="family-tr family-tr-` +
                  emp.family_coverage.dependents[i].dependent.UserID + `" style="background:#eee;">
                    <td class="for-checkbox-container"></td>
                    <td>
                      <p><a>` + emp.family_coverage.dependents[i].dependent.Name + `</a></p>
                    </td>
                    <td>
                      <p>---</p>
                    </td>
                    <td>
                      <p>Start Date: <span>` + emp.family_coverage.dependents[i].plan.plan_start + `</span></p>
                      <p>End Date: <span>` + emp.family_coverage.dependents[i].plan.plan_end + `</span></p>
                    </td>
                    <td>
                      <p>Dependent</p>
                    </td>
                    <td>
                      <p class="text-center">
                        S$<span style="width: 100px;text-align: right;margin-right: 10px;">` + emp.family_coverage.dependents[i].spent + `</span>
                        <span>Usage</span>
                      </p>
                    </td>
                  </tr>`).insertAfter($(evt.target).closest("tr"));
              }

              $(`<tr class="family-tr family-tr-` + emp.user_id + `" style="background:#eee;">
                  <td class="for-checkbox-container"></td>
                  <td>
                    <p><a>` + emp.family_coverage.spouse.spouse.Name + `</a></p>
                  </td>
                  <td>
                    <p>---</p>
                  </td>
                  <td>
                    <p>Start Date: <span>` + emp.family_coverage.spouse.spouse.plan_start + `</span></p>
                    <p>End Date: <span>` + emp.family_coverage.spouse.spouse.plan_end + `</span></p>
                  </td>
                  <td>
                    <p>Spouse</p>
                  </td>
                  <td>
                    <p class="text-center">
                      S$<span style="width: 100px;text-align: right;margin-right: 10px;">` + emp.family_coverage.spouse.spent + `</span>
                      <span>Usage</span>
                    </p>
                  </td>
                </tr>`).insertAfter($(evt.target).closest("tr"));

              scope.family_temp = emp.user_id;
              scope.family_trap = true;
            } else {
              $(".family-tr").remove();
              scope.family_trap = false;

              if (scope.family_temp != scope.family_selected) {
                scope.showFamily(emp, evt);
              }
            }
          } else {
            $(".family-tr").remove();
            scope.family_trap = false;
          }
        };

        scope.checkNRIC = function (theNric) {
          var nric_pattern = new RegExp('^[stfgSTFG]{1}[0-9]{7}[a-zA-z]{1}$');
          return nric_pattern.test(theNric);
        };

        scope.checkEmail = function (email) {
          var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
          return regex.test(email);
        }

        scope.checkDependentForm = function (data) {
          if (!data.fullname) {
            swal('Error!', 'Full Name is required.', 'error');
            return false;
          }
          // if( !data.last_name ){
          //   swal( 'Error!', 'Last Name is required.', 'error' );
          //   return false;
          // }
          // if( !data.nric ){
          //   swal( 'Error!', 'NRIC is required.', 'error' );
          //   return false;
          // }else{
          //   if( scope.nric_status_dependents == true ){
          //     var checkNRIC = scope.checkNRIC(data.nric);
          //     if( checkNRIC != true ){
          //       swal( 'Error!', 'Invalid NRIC.', 'error' );
          //       return false;
          //     }
          //   } 
          // }
          if (!data.dob) {
            swal('Error!', 'Date of Birth is required.', 'error');
            return false;
          }
          if (!data.relationship) {
            data.relationship = null;
            // swal( 'Error!', 'Relationship is required.', 'error' );
            // return false;
          }
          if (!data.start_date) {
            swal('Error!', 'Start Date is required.', 'error');
            return false;
          }

          return true;
        }

        scope.checkUpdateEmployeeForm = function (data) {
          if (!data.name) {
            swal('Error!', 'Full Name is required.', 'error');
            return false;
          }
          // if( !data.last_name ){
          //   swal( 'Error!', 'Last Name is required.', 'error' );
          //   return false;
          // }
          // if( !data.nric ){
          //   swal( 'Error!', 'NRIC is required.', 'error' );
          //   return false;
          // }else{
          //   if( scope.nric_status == true ){
          //     var checkNRIC = scope.checkNRIC(data.nric);
          //     if( checkNRIC != true ){
          //       swal( 'Error!', 'Invalid NRIC.', 'error' );
          //       return false;
          //     }
          //   } 
          // }
          if ( (data.email == "" && data.phone_no == "") || (data.email == null && data.phone_no == null) ) {
            swal( 'Error!', 'Phone number or Email Address is required.', 'error' );
            return false;
          }
          console.log( data );
          if (data.email) {
            console.log( data.email );
            console.log( scope.checkEmail(data.email) );
            if( scope.checkEmail(data.email) == false ){
              swal( 'Error!', 'Email is invalid.', 'error' );
              return false;
            }
          }
          // if( !data.phone_no ){
          //   swal( 'Error!', 'Mobile Number is required.', 'error' );
          //   return false;
          // }else{
          if( data.phone_no ){
            // console.log( iti.getSelectedCountryData().iso2 );
            if (iti.getSelectedCountryData().iso2 == 'sg' && data.phone_no.length < 8) {
              swal('Error!', 'Mobile Number for your country code should be 8 digits.', 'error');
              return false;
            }
            if (iti.getSelectedCountryData().iso2 == 'my' && data.phone_no.length < 10) {
              swal('Error!', 'Mobile Number for your country code should be 10 digits.', 'error');
              return false;
            }
            if (iti.getSelectedCountryData().iso2 == 'ph' && data.phone_no.length < 9) {
              swal('Error!', 'Mobile Number for your country code should be 9 digits.', 'error');
              return false;
            }
          }

          if( !data.dob ){
            swal( 'Error!', 'Date of Birth is required.', 'error' );
            return false;
          }
          // if( !data.email ){
          //   swal( 'Error!', 'Email is required.', 'error' );
          //   return false;
          // }else{
          //   if( scope.checkEmail(data.email) == false ){
          //     swal( 'Error!', 'Email is invalid.', 'error' );
          //     return false;
          //   }
          // }
          // if( !data.postal_code ){
          //   swal( 'Error!', 'Postal Code is required.', 'error' );
          //   return false;
          // }

          if(data.nric) {
            if (data.nric.includes("-")) {
              sweetAlert("Oops...", "Invalid NRIC format. Please enter NRIC in the format of 12 digit number only.", "error");
              return false;
            } else if (!scope.checkNRIC(data.nric)) {
              sweetAlert("Oops...", "Invalid NRIC format. Please enter NRIC in the format of 12 digit number only.", "error");
              return false;
            }
					}
					
					if(data.passport) {
            if (!scope.checkPassport(data.passport)) {
              sweetAlert("Oops...", "Invalid passport format. Please enter passport in the format of a letter followed by an 8 digit number.", "error");
              return false;
            }
          }
          return true;
        }

        scope.checkReplaceEmployeeForm = function (data) {
          if (!data.fullname) {
            swal('Error!', 'Full Name is required.', 'error');
            return false;
          }
          // if( !data.last_name ){
          //   swal( 'Error!', 'Last Name is required.', 'error' );
          //   return false;
          // }
          // if( !data.nric ){
          //   swal( 'Error!', 'NRIC is required.', 'error' );
          //   return false;
          // }else{
          //   if( scope.nric_status == true ){
          //     var checkNRIC = scope.checkNRIC(data.nric);
          //     if( checkNRIC != true ){
          //       swal( 'Error!', 'Invalid NRIC.', 'error' );
          //       return false;
          //     }
          //   } 
          // }
          if (!data.dob) {
            swal('Error!', 'Date of Birth is required.', 'error');
            return false;
          }
          if (!data.email) {
            // swal( 'Error!', 'Email is required.', 'error' );
            // return false;
          } else {
            if (scope.checkEmail(data.email) == false) {
              swal('Error!', 'Email is invalid.', 'error');
              return false;
            }
          }
          if (!data.mobile) {
            swal('Error!', 'Mobile Number is required.', 'error');
            return false;
          } else {
            // console.log( iti.getSelectedCountryData().iso2 );
            if (iti2.getSelectedCountryData().iso2 == 'sg' && data.mobile.length < 8) {
              swal('Error!', 'Mobile Number for your country code should be 8 digits.', 'error');
              return false;
            }
            if (iti2.getSelectedCountryData().iso2 == 'my' && data.mobile.length < 10) {
              swal('Error!', 'Mobile Number for your country code should be 10 digits.', 'error');
              return false;
            }
            if (iti2.getSelectedCountryData().iso2 == 'ph' && data.mobile.length < 9) {
              swal('Error!', 'Mobile Number for your country code should be 9 digits.', 'error');
              return false;
            }
          }
          // if( !data.postal_code ){
          //   swal( 'Error!', 'Postal Code is required.', 'error' );
          //   return false;
          // }
          if (!data.plan_start) {
            swal('Error!', 'Start Date is required.', 'error');
            return false;
          }
          // if (data.medical_credits > scope.credit_status.total_medical_employee_balance_number) {
          //   swal('Error!', 'We realised your Company Medical Spending Account has insufficient credits. Please contact our support team to increase the credit limit.', 'error');
          //   return false;
          // }
          // if (data.wellness_credits > scope.credit_status.total_wellness_employee_balance_number) {
          //   swal('Error!', 'We realised your Company Wellness Spending Account has insufficient credits. Please contact our support team to increase the credit limit.', 'error');
          //   return false;
          // }

          return true;
        }

        scope.pushActiveDependent = function (data) {
          if (scope.checkDependentForm(data) == true) {
            scope.showLoading();
            scope.hideLoading();
            data.done = true;
            data.dob = moment(data.dob, 'DD/MM/YYYY').format('YYYY-MM-DD');
            data.start_date = moment(data.start_date, 'DD/MM/YYYY').format('YYYY-MM-DD');
            scope.addDependents_arr.push(data);
            scope.dependents_ctr += 1;
            scope.addActiveDependent_index += 1;
            scope.dependent_data = {};
          }
        };

        scope.prevActiveDependent = function () {
          if (scope.dependents_ctr != 0) {
            scope.dependents_ctr -= 1;
            scope.addActiveDependent_index -= 1;
            scope.dependent_data = scope.addDependents_arr[scope.dependents_ctr];
          }
        }

        scope.nextActiveDependent = function () {
          scope.dependents_ctr += 1;
          scope.addActiveDependent_index += 1;
          if (scope.addDependents_arr[scope.dependent_ctr]) {
            scope.dependent_data = scope.addDependents_arr[scope.dependent_ctr];
          } else {
            scope.dependent_data = {};
          }
          console.log(scope.addDependents_arr);
        }

        scope.perPage = function (num) {
          scope.page_ctr = num;
          scope.page_active = 1;
          scope.getEmployeeList(scope.page_active);
        };

        scope.toggleAddDependents = function () {
          if (scope.isAddDependentsShow == false) {
            if (scope.selectedEmployee.plan_tier) {
              if (scope.selectedEmployee.plan_tier.dependent_enrolled_count == scope.selectedEmployee.plan_tier.dependent_head_count) {
                swal({
                  title: "Info",
                  text: "Number of dependents head count is already zero. Please contact mednefits for assistance.",
                  type: "info",
                  showCancelButton: false,
                  confirmButtonColor: "#0392CF",
                  closeOnConfirm: true,
                  customClass: "updateEmp"
                },
                  function (isConfirm) {
                    if (isConfirm) {

                    }
                  });
              } else {
                $('.employee-information-wrapper').hide();
                $('.add-dependent-wrapper').fadeIn();
                scope.isAddDependentsShow = true;
              }
            } else {
              // $('.employee-information-wrapper').fadeIn();
              // $('.add-dependent-wrapper').hide();
              // scope.isAddDependentsShow = false; 
              if (scope.dependents.total_number_of_seats == scope.dependents.occupied_seats) {
                swal({
                  title: "Info",
                  text: "Number of dependents head count is already zero. Please contact mednefits for assistance.",
                  type: "info",
                  showCancelButton: false,
                  confirmButtonColor: "#0392CF",
                  closeOnConfirm: true,
                  customClass: "updateEmp"
                },
                  function (isConfirm) {
                    if (isConfirm) {

                    }
                  });
              } else {
                $('.employee-information-wrapper').hide();
                $('.add-dependent-wrapper').fadeIn();
                scope.isAddDependentsShow = true;
              }
            }
          } else {
            $('.employee-information-wrapper').fadeIn();
            $('.add-dependent-wrapper').hide();
            scope.isAddDependentsShow = false;
          }
        };

        scope.med_effective_date = moment().format('DD/MM/YYYY');
        scope.well_effective_date = moment().format('DD/MM/YYYY');

        scope.toggleEmpTab = function (opt) {
      
          scope.emp_entitlement.medical_new_entitlement = '';
          scope.emp_entitlement.wellness_new_entitlement = '';
          scope.new_allocation_med = 0;
          scope.new_allocation_well = 0;
          // scope.effective_date = {
          //   med_date : new Date( $('.medical-entitlement-date').val() ),
          //   well_date : new Date( $('.medical-entitlement-date').val() ),
          // }
          scope.med_effective_date = moment().format('DD/MM/YYYY');
          scope.well_effective_date = moment().format('DD/MM/YYYY');
          scope.cal_one = false;
          scope.cal_two = false;
          
         
        
          scope.medicalCalculatedInfo = false;
          scope.wellnessCalculatedInfo = false;

          $('.dropdown-entitlement').toggle();
          
          $("body").click(function(e){ 
            if ($(e.target).parents(".entitlementWithIcon").length === 0) {
              $(".dropdown-entitlement").hide();
            }
          });
          
          setTimeout(() => {
            var dt = new Date();
            // dt.setFullYear(new Date().getFullYear()-18);
            $('.datepicker').datepicker({
              format: 'dd/mm/yyyy',
              endDate: dt
            });

            $('.datepicker-medical').datepicker({
              format: 'dd/mm/yyyy',
            });

            $('.datepicker-wellness').datepicker({
              format: 'dd/mm/yyyy',
            });

            $('.datepicker').datepicker().on('hide', function (evt) {
              var val = $(this).val();
              if (val != "") {
                $(this).datepicker('setDate', val);
              }
            })
          }, 1000);

          scope.empTabSelected = opt;
          scope.healthSpendingAccountTabIsShow = false;
        };

        scope.togglePage = function () {

          $(".per_page").toggle();
        };

        scope.toggleTierDetails = function () {
          if (scope.isTierDetailsShow == false) {
            scope.isTierDetailsShow = true;
          } else {
            scope.isTierDetailsShow = false;
          }
        }

        scope.toggleMedicalUsage = function () {
          if (scope.isMedicalUsageShow == false) {
            scope.isMedicalUsageShow = true;
          } else {
            scope.isMedicalUsageShow = false;
          }
        }

        scope.toggleWellnessUsage = function () {
          if (scope.isWellnessUsageShow == false) {
            scope.isWellnessUsageShow = true;
          } else {
            scope.isWellnessUsageShow = false;
          }
        }

        scope.toggleEditEmployeeNRIC = function (data, opt) {
          if (opt == "nric") {
            scope.nric_status = true;
            scope.fin_status = false;
          } else {
            scope.nric_status = false;
            scope.fin_status = true;
          }
          // scope.selectedEmployee.nric = "";
        };

        scope.openUpdateEmployeeModal = function () {
          scope.isUpdateEmpInfoModalOpen = true;
          $("#update-employee-modal").modal('show');
          // scope.selectedEmployee.dob = moment( scope.selectedEmployee.dob ).format('DD/MM/YYYY');
          if(scope.selectedEmployee.country_code.indexOf('+') < 0){
            scope.selectedEmployee.country_code = '+' + scope.selectedEmployee.country_code;
          }
          scope.editEmpCountryCode = scope.selectedEmployee.country_code;
          console.log(scope.selectedEmployee.dob);
          $('.datepicker').datepicker('setDate', scope.selectedEmployee.dob);
          scope.inititalizeGeoCode();
          console.log(scope.selectedEmployee);
          console.log(scope.editEmpCountryCode);
        }

        scope.openUpdateDependentModal = function (data) {
          // console.log( data );
          scope.selectedDependent = data;
          scope.selectedDependent.dob = data.dob;
          $("#update-dependent-modal").modal('show');
          $('.datepicker').datepicker('setDate', scope.selectedDependent.dob);
        }

        scope.toggleEmployee = function (emp, index) {
          // console.log(emp);
          if( scope.isRemoveEmployeeShow ){
            scope.isEmployeeShow = true;
            scope.isRemoveEmployeeShow = false;
            $('.employee-information-wrapper').show();
          }
          
          console.log(scope.isEmployeeShow);
          if (scope.isEmployeeShow == false) {
            scope.isRemoveEmployeeShow = false;
            $('.employee-information-wrapper').fadeIn();
            $('.prev-next-buttons-container').hide();
            $('.remove-employee-wrapper').hide();

            
            scope.isEmployeeShow = true;
            scope.empTabSelected = 0;
            scope.healthSpendingAccountTabIsShow = false;
            scope.selectedEmployee_index = index;
            scope.selectedEmployee = emp;
            scope.plan_name = emp.plan_name;
            // console.log(scope.plan_name);

            // scope.medical_wallet = emp.medical_wallet;
            // scope.wellness_wallet = emp.wellness_wallet;
            // console.log(scope.medical_wallet);
            // console.log(scope.wellness_wallet);
            if (emp.account_type === 'lite_plan'){
              scope.isBasicPlan = true;
            }else{
              scope.isBasicPlan = false;
            }
            if (emp.account_type === 'lite_plan' && emp.plan_method_type == 'pre_paid') {
              scope.hideLitePlanCheckbox = false;
              scope.litePlanCheckbox = true;
            }else{
              scope.hideLitePlanCheckbox = true;
              scope.litePlanCheckbox = false;
            }

            if (scope.selectedEmployee.plan_tier != null || scope.selectedEmployee.plan_tier) {
              scope.addActiveDependent_index = scope.selectedEmployee.plan_tier.dependent_enrolled_count + 1;
            } else {
              scope.addActiveDependent_index = scope.dependents.occupied_seats + 1;
            }

            scope.selectedEmployee.start_date_dmy = moment(scope.selectedEmployee.start_date,['YYYY-MM-DD', 'DD/MM/YYYY']).format('DD/MM/YYYY');
            scope.selectedEmployee.end_date_dmy = moment(scope.selectedEmployee.expiry_date).format('DD/MM/YYYY');
            console.log( emp );
            console.log( scope.selectedEmployee )

            scope.selectedEmployee.dob = moment(scope.selectedEmployee.dob, ['YYYY-MM-DD', 'DD/MM/YYYY']).format('DD/MM/YYYY');

            // console.log(scope.selectedEmployee.dob);
            scope.showLoading();
            scope.hideLoading();
            scope.fetchRefundStatus(emp.user_id);
            scope.getEmpDependents(emp.user_id);
            scope.getEmpPlans(emp.user_id);
            scope.getMemberEntitlement(emp.user_id);
            scope.getMemberNewEntitlementStatus(emp.user_id);
            scope.entitlementCalc(emp.user_id);
            $('body').css('overflow', 'auto');
            $(".hrdb-body-container").hide();
            $(".employee-information-wrapper").fadeIn();
          } else {
            scope.selectedEmployee_index = null;
            scope.isEmployeeShow = false;
            $(".hrdb-body-container").fadeIn();
            $(".employee-information-wrapper").hide();
            $('body').scrollTop(0);
            $timeout(function () {
              $('body').css('overflow', 'hidden');
            }, 200);
          }
        }

        scope.getEmpPlans = function (id) {
          dependentsSettings.fetchEmpPlans(id)
            .then(function (response) {
              // console.log(response);
              scope.selectedEmployee.plan_list = response.data;
            });
        }

        scope.medical_date = null;

        scope.getMemberEntitlement = function ( emp ) {

 
          scope.emp_member_id = emp;
          hrActivity.fetchMemberEntitlement( scope.emp_member_id ) 
              .then(function(response) {
                console.log('member Entitlement',response);
                scope.emp_entitlement = response.data;

                // Gicomment nako kay issue is after update gaka invalid date ang medical and wellness dates - Jeamar
                
                // scope.med_effective_date = scope.emp_entitlement.medical_entitlement_date;
                // scope.well_effective_date = scope.emp_entitlement.wellness_entitlement_date;

                console.log(scope.emp_entitlement.updated_medical_entitlement);
                console.log(scope.emp_entitlement.updated_wellness_entitlement);

                scope.emp_entitlement.medical_entitlement_date = moment( scope.emp_entitlement.medical_entitlement_date, 'YYYY-MM-DD' ).format('DD/MM/YYYY');
                scope.emp_entitlement.wellness_entitlement_date = moment( scope.emp_entitlement.wellness_entitlement_date, 'YYYY-MM-DD').format('DD/MM/YYYY');

                scope.emp_entitlement.medical_new_entitlement = '';
                scope.emp_entitlement.wellness_new_entitlement = '';
                // scope.med_effective_date = '';
                // scope.well_effective_date = '';

                scope.med_effective_date = moment().format('DD/MM/YYYY');
                scope.well_effective_date = moment().format('DD/MM/YYYY');
          });
        }
        
        scope.getMemberNewEntitlementStatus = function ( emp ) {
          hrActivity.fetchMemberNewEntitlementStatus( scope.emp_member_id ) 
              .then(function(response) {
                // console.log(response);
                
                scope.entitlement_status = response.data;
                console.log(scope.entitlement_status);

                if ( scope.entitlement_status.medical_entitlement != null && scope.entitlement_status.wellness_entitlement != null ) {
                  scope.entitlement_status.medical_entitlement.effective_date = moment( scope.entitlement_status.medical_entitlement.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
                  scope.entitlement_status.wellness_entitlement.effective_date = moment( scope.entitlement_status.wellness_entitlement.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
                }
          });
        }

        scope.cal_one = false;
        scope.cal_two = false;
        scope.calc_update_med = false;
        scope.calc_update_well = false;

        scope.entitlementCalc = function ( type, cal ) {

          if ( cal == 1 ) {
             scope.cal_one = true;
          }

          if ( cal == 2) {
            scope.cal_two  = true;
          } 

          scope.entitlement_credits = {
            med_credits : scope.emp_entitlement.medical_new_entitlement,
            well_credits : scope.emp_entitlement.wellness_new_entitlement
          }
          scope.effective_date = {
            med_date : moment( $('.medical-entitlement-date').val(), 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            well_date : moment( $('.wellness-entitlement-date').val(), 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
          }
          scope.proration = {
            med_proration : scope.emp_entitlement.medical_proration,
            well_proration : scope.emp_entitlement.wellness_proration
          }
          scope.entitlement_spending_type = type;

          if ( type == 'medical' ) {
            scope.showLoading();
            hrActivity.openEntitlementCalc( scope.emp_member_id, scope.entitlement_credits.med_credits, scope.effective_date.med_date, scope.proration.med_proration, scope.entitlement_spending_type) 
                .then(function(response) {
                  console.log(response);
                  console.log( scope.entitlement_credits.med_credits );
                  scope.hideLoading();
                  scope.calc_entitlement_med = response.data;
                  scope.new_allocation_med = scope.calc_entitlement_med.new_allocation;
                  scope.medicalCalculatedInfo = true;

                  if ( response.data.status == false ) {
                    console.log('New Medical Entitlement Usage Date exceeded the Spending End Date.');
                    scope.medicalCalculatedInfo = false;
                    scope.effectiveMedDateError = true;
                    scope.calc_update_med = false;
                  } else {
                    scope.effectiveMedDateError = false;
                    scope.calc_update_med = true;
                  }

            });
          
          } 
          if (type == 'wellness') {
            console.log('wellnesss');
            scope.showLoading();
            hrActivity.openEntitlementCalc( scope.emp_member_id, scope.entitlement_credits.well_credits, scope.effective_date.well_date, scope.proration.well_proration, scope.entitlement_spending_type) 
                .then(function(response) {
                  console.log(response);
                  console.log( scope.entitlement_credits.well_credits );
                  scope.hideLoading();
                  scope.calc_entitlement_well = response.data;
                  scope.new_allocation_well = scope.calc_entitlement_well.new_allocation;
                  scope.wellnessCalculatedInfo = true;

                  if ( response.data.status == false ) {
                    console.log('New Medical Entitlement Usage Date exceeded the Spending End Date.');
                    scope.wellnessCalculatedInfo = false;
                    scope.effectiveWellDateError = true;
                    scope.calc_update_well = false;
                  } else {
                    scope.effectiveWellDateError = false;
                    scope.calc_update_well = true;
                  }

            });
  
          }
        }  

        scope.updateDisable = true;
        scope.checkNewAllocation =  function (data) {
          // if  (data.medical_new_entitlement == null && data.wellness_new_entitlement == null || data.medical_new_entitlement == '' && data.wellness_new_entitlement == '' || data.medical_new_entitlement == null && data.wellness_new_entitlement == '' || data.medical_new_entitlement == '' && data.wellness_new_entitlement == null)  {
          //   scope.updateDisable = true;
          // } else {
          //   scope.updateDisable = false;
          // }

          if ( (data.medical_new_entitlement === '' || data.wellness_new_entitlement === '') &&
          (data.medical_new_entitlement === null || data.wellness_new_entitlement === null) ||
          (data.medical_new_entitlement === null && data.wellness_new_entitlement === null) )  {
            scope.updateDisable = true;
          } else {
            scope.updateDisable = false;
          }

          console.log(data);
        }

        scope.entitlement_credits = {
          med_credits : scope.emp_entitlement.medical_new_entitlement,
          well_credits : scope.emp_entitlement.wellness_new_entitlement
        }
        

        console.log(scope.effective_date);

        scope.updateEntitlement = function () {
          scope.effective_date = {
            med_date : moment( $('.medical-entitlement-date').val(), 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            well_date : moment( $('.wellness-entitlement-date').val(), 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            // med_date : moment( $('.medical-entitlement-date').val(), 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            // well_date : moment( $('.wellness-entitlement-date').val(), 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
          }

          console.log(scope.emp_entitlement.medical_new_entitlement, scope.emp_entitlement.wellness_new_entitlement);

          console.log(scope.effective_date);
          var text;

          if ((scope.emp_entitlement.medical_new_entitlement > 0 && scope.emp_entitlement.wellness_new_entitlement > 0)|| (scope.emp_entitlement.medical_new_entitlement === 0 && scope.emp_entitlement.wellness_new_entitlement === 0)) {
            console.log('1 if');
            text = `<span>Please note that</span> <br><br> <span>_ The new Medical Allocation of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.medical_new_entitlement}</span> will override the current amount of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.original_medical_entitlement}</span>.</span><br><span>_ The new Wellness Allocation of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.wellness_new_entitlement}</span> will override the current amount of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.original_wellness_entitlement}</span>.</span> <br><br> <span>Please confirm to proceed.</span>`;
          } else if ((scope.emp_entitlement.medical_new_entitlement > 0) || (scope.emp_entitlement.medical_new_entitlement === 0)) {
            console.log('2 if');
            text = `<span> Please note that the new Medical Allocation of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.medical_new_entitlement}</span> will override the current amount of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.original_medical_entitlement}</span>.</span> <br><br> <span>Please confirm to proceed.</span>`;
          } else if ((scope.emp_entitlement.wellness_new_entitlement > 0) || (scope.emp_entitlement.wellness_new_entitlement === 0)) {
            console.log('3 if');
            text = `<span>Please note that the new Wellness Allocation of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.wellness_new_entitlement}</span> will override the current amount of <span style="text-transform: uppercase; font-weight:bold;">${scope.emp_entitlement.currency_type} ${scope.emp_entitlement.original_wellness_entitlement}</span>.</span> <br><br> <span>Please confirm to proceed.</span>`;
          }

          swal({
            title: '',
            text: text,
            html: true,
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            reverseButtons: true,
            customClass : 'allocationEntitlementModal'
          }, function(result) {
            console.log(result);
            setTimeout(function(){
              if(result) {
                
                if ((scope.emp_entitlement.medical_new_entitlement > 0 && scope.emp_entitlement.wellness_new_entitlement > 0) || (scope.emp_entitlement.medical_new_entitlement === 0 && scope.emp_entitlement.wellness_new_entitlement === 0)) {
                  console.log('both');
                  scope.updateAllEntitlement();
                } else if ((scope.emp_entitlement.medical_new_entitlement > 0) || (scope.emp_entitlement.medical_new_entitlement === 0)) {
                  console.log('medical');
                  scope.updateMedicalEntitlement();
                } else if ((scope.emp_entitlement.wellness_new_entitlement > 0) || (scope.emp_entitlement.wellness_new_entitlement === 0)) {
                  console.log('wellness');
                  scope.updateWellnessEntitlement();
                }


                // swal({
                //   title: '',
                //   text: '<span>The allocation amount has been successfully updated.</span>',
                //   html: true,
                //   showCancelButton: false,
                //   confirmButtonText: 'Close',
                //   customClass : 'allocationEntitlementSuccessModal'
                // });
              }
            }, 500)
          })
        }

        scope.updateMedicalEntitlement = function () {
          scope.showLoading();
          var medical_data = {
            member_id:  scope.emp_member_id,
            new_allocation_credits: scope.emp_entitlement.medical_new_entitlement,
            effective_date: scope.effective_date.med_date,
            spending_type:  'medical',
          }
          // var medical_data = {
          //   member_id : scope.emp_member_id,
          //   new_entitlement_credits : scope.entitlement_credits.med_credits,
          //   entitlement_usage_date : scope.effective_date.med_date,
          //   proration_type : scope.proration.med_proration,
          //   entitlement_spending_type : 'medical',
          // }

          console.log('medical ni update');
          hrActivity.updateEntitlement( medical_data ) 
            .then(function(response) {
              console.log(response);
              console.log(medical_data);
              console.log(response.data.status);

              if (response.data.status) {
                scope.hideLoading();
                let text;
                if (medical_data.effective_date > moment().format('YYYY-MM-DD')) {
                  text  =  `<span>The allocation amount will be updated on ${moment(medical_data.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY')}.</span>`;
                  console.log(text);
                } else {
                  text  =  '<span>The allocation amount has been successfully updated.</span>';
                  console.log(text);
                }
                swal({
                  title: '',
                  text: text,
                  html: true,
                  showCancelButton: false,
                  confirmButtonText: 'Close',
                  customClass : 'allocationEntitlementSuccessModal'
                });
                scope.updateDisable = true;
                scope.getMemberEntitlement( scope.emp_member_id );
                scope.getMemberNewEntitlementStatus();
                scope.memberCredits();
              } else {
                scope.hideLoading();
                swal('Error!', response.data.message,'error');
              }
              
          });
        }

        scope.updateWellnessEntitlement = function () {
          scope.showLoading();
          var wellness_data = {
            member_id:  scope.emp_member_id,
            new_allocation_credits: scope.emp_entitlement.wellness_new_entitlement,
            effective_date: scope.effective_date.well_date,
            spending_type : 'wellness',
          }

          // var wellness_data = {
          //   member_id : scope.emp_member_id,
          //   new_entitlement_credits : scope.entitlement_credits.well_credits,
          //   entitlement_usage_date : scope.effective_date.well_date,
          //   proration_type : scope.proration.well_proration,
          //   entitlement_spending_type : 'wellness',
          // }

          console.log('wellness ni update');
          hrActivity.updateEntitlement( wellness_data ) 
            .then(function(response) {
              console.log(response);
              // console.log(data);
              console.log(response.data.status);
              console.log( wellness_data );
              if (response.data.status) {
                scope.hideLoading();
                let text;
                if (wellness_data.effective_date > moment().format('YYYY-MM-DD')) {
                  text  =  `<span>The allocation amount will be updated on ${moment(wellness_data.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY')}.</span>`;
                  console.log(text);
                } else {
                  text  =  '<span>The allocation amount has been successfully updated.</span>';
                  console.log(text);
                }
                swal({
                  title: '',
                  text: text,
                  html: true,
                  showCancelButton: false,
                  confirmButtonText: 'Close',
                  customClass : 'allocationEntitlementSuccessModal'
                });
                scope.updateDisable = true;
                scope.getMemberEntitlement( scope.emp_member_id );
                scope.getMemberNewEntitlementStatus();
                scope.memberCredits();
              } else {
                scope.hideLoading();
                swal('Error!', response.data.message,'error');
                console.log( response.data.message );
              }
              
          });
          
        }


        scope.updateAllEntitlement = function () {
          scope.showLoading();
          console.log(scope.effective_date.med_date);
          console.log(scope.effective_date.well_date);

          var medical_data = {
            member_id:  scope.emp_member_id,
            new_allocation_credits: scope.emp_entitlement.medical_new_entitlement,
            effective_date: scope.effective_date.med_date,
            spending_type:  'medical',
          }

          var wellness_data = {
            member_id:  scope.emp_member_id,
            new_allocation_credits: scope.emp_entitlement.wellness_new_entitlement,
            effective_date: scope.effective_date.well_date,
            spending_type : 'wellness',
          }

          hrActivity.updateEntitlement( medical_data )
            .then(function(response1){
              console.log(response1);
              // scope.hideLoading();
              console.log(response1.data.status);
              if (response1.data.status) {
                // scope.hideLoading();
                hrActivity.updateEntitlement( wellness_data )
                  .then(function(response2){
                    console.log(response2);
                    scope.hideLoading();
                    console.log(response2.data.status);
                    if (response2.data.status) {
                      scope.hideLoading();
                      let text;
                      if (medical_data.effective_date > moment().format('YYYY-MM-DD') && wellness_data.effective_date > moment().format('YYYY-MM-DD')) {
                        text  =  `<span>The allocation amount will be updated on ${moment(medical_data.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY')} for medical and ${moment(wellness_data.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY')} for wellness.</span>`;
                        console.log(text);
                      } else if (medical_data.effective_date > moment().format('YYYY-MM-DD') && wellness_data.effective_date <= moment().format('YYYY-MM-DD')) {
                        text  =  `<span>The allocation amount has been successfully updated and for wellness will be updated on ${moment(wellness_data.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY')}.</span>`;
                        console.log(text);
                      } else if (wellness_data.effective_date > moment().format('YYYY-MM-DD') && medical_data.effective_date <= moment().format('YYYY-MM-DD')) {
                        text  =  `<span>The allocation amount has been successfully updated and for medical will be updated on ${moment(medical_data.effective_date, 'YYYY-MM-DD').format('DD/MM/YYYY')}.</span>`;
                        console.log(text);
                      } else {
                        text = '<span>The allocation amount has been successfully updated.</span>';
                      }
                      swal({
                        title: '',
                        text: text,
                        html: true,
                        showCancelButton: false,
                        confirmButtonText: 'Close',
                        customClass : 'allocationEntitlementSuccessModal'
                      });
                      scope.updateDisable = true;
                      scope.getMemberEntitlement( scope.emp_member_id );
                      scope.getMemberNewEntitlementStatus();
                      scope.memberCredits();
                    } else {
                      scope.hideLoading();
                      swal('Error!', response2.data.message,'error');
                    }
                });  
              } else {
                scope.hideLoading();
                swal('Error!', response1.data.message,'error');
              }
          });
        }

        scope.memberCredits = function () {
          hrActivity.memberCredits( scope.emp_member_id )
            .then(function(response) {
              console.log(response.data);
              scope.selectedEmployee.spending_account = response.data;
            })
        }

        scope.prevSelectedEmployee = function () {
          scope.empTabSelected = 0;
          if (scope.selectedEmployee_index != 0) {
            scope.showLoading();
            scope.hideLoading();
            scope.selectedEmployee_index--;
            scope.selectedEmployee = scope.employees.data[scope.selectedEmployee_index];
            scope.fetchRefundStatus(scope.selectedEmployee.user_id);
            scope.getEmpDependents(scope.selectedEmployee.user_id);
            scope.getEmpPlans(scope.selectedEmployee.user_id);
            scope.getMemberEntitlement(scope.selectedEmployee.user_id);
            scope.getMemberNewEntitlementStatus(scope.selectedEmployee.user_id);
            scope.entitlementCalc(scope.selectedEmployee.user_id);
            scope.blockHealthPatnerLoad();
          }
        };

        scope.nextSelectedEmployee = function () {
          scope.empTabSelected = 0;
          if (scope.selectedEmployee_index != (scope.employees.data.length - 1)) {
            scope.showLoading();
            scope.hideLoading();
            scope.selectedEmployee_index++;
            scope.selectedEmployee = scope.employees.data[scope.selectedEmployee_index];
            scope.fetchRefundStatus(scope.selectedEmployee.user_id);
            scope.getEmpDependents(scope.selectedEmployee.user_id);
            scope.getEmpPlans(scope.selectedEmployee.user_id);
            scope.getMemberEntitlement(scope.selectedEmployee.user_id);
            scope.getMemberNewEntitlementStatus(scope.selectedEmployee.user_id);
            scope.entitlementCalc(scope.selectedEmployee.user_id);
            scope.blockHealthPatnerLoad();
          }
        };

        scope.nextPage = function () {
          if (scope.page_active < scope.employees.last_page) {
            scope.page_active++;
            scope.getEmployeeList(scope.page_active);
          }
        };

        scope.goToPage = function (page) {
          scope.page_active = page;
          scope.getEmployeeList(scope.page_active);
        };

        scope.prevPage = function () {
          if (scope.page_active > 1) {
            scope.page_active--;
            scope.getEmployeeList(scope.page_active);
          }
        };

        scope.removeSearchEmp = function () {
          scope.inputSearch = "";
          scope.page_active = 1;
          scope.getEmployeeList(1);
        }

        scope.searchEmployee = function (input) {
          // console.log(input);
          if (input) {
            scope.showLoading();
            // var data = {
            //   search: input
            // };
            let data = input;

            // hrSettings.findEmployee(data)
            hrSettings.searchMemberList(scope.page_active,scope.global_empLimitList,data)
              .then(function (response) {
                scope.employees = response.data;
                angular.forEach(scope.employees.data, function (value, key) {
                  value.fname = scope.employees.data[key].name.substring(0, value.name.lastIndexOf(" "));
                  value.lname = scope.employees.data[key].name.substring(value.name.lastIndexOf(" ") + 1);
                  value.start_date = moment(value.start_date).format("DD/MM/YYYY");
                  value.start_date_format = moment(value.start_date, 'DD/MM/YYYY').format("DD MMMM YYYY");
                  value.end_date_format = moment(value.expiry_date).format("DD MMMM YYYY");
                  value.expiry_date = moment(value.expiry_date).format("DD/MM/YYYY");
                });
                $(".employee-overview-pagination").hide();
                scope.hideLoading();
                scope.isSearchEmp = true;

                console.log(scope.selectedEmployee);
                if (scope.selectedEmployee_index != null) {
                  scope.selectedEmployee = scope.employees.data[scope.selectedEmployee_index];
                  console.log(scope.selectedEmployee);
                  scope.selectedEmployee.dob = moment(scope.selectedEmployee.dob, ['YYYY-MM-DD', 'DD/MM/YYYY']).format('DD/MM/YYYY');
                  if (scope.selectedEmployee.plan_tier != null || scope.selectedEmployee.plan_tier) {
                    scope.addActiveDependent_index = scope.selectedEmployee.plan_tier.dependent_enrolled_count + 1;
                  } else {
                    scope.addActiveDependent_index = scope.progress.completed + 1;
                  }
                  scope.fetchRefundStatus(scope.selectedEmployee.user_id);
                  scope.getEmpPlans(scope.selectedEmployee.user_id);
                  scope.getEmpDependents(scope.selectedEmployee.user_id);
                }
              });
          } else {
            scope.isSearchEmp = false;
            scope.removeSearchEmp();
          }
        };

        scope.changeRemoveOption = function (opt) {
          scope.remove_employee_data.replace = false;
          scope.remove_employee_data.reserve = false;
          scope.remove_employee_data.remove = false;

          if (opt == 1) {
            scope.remove_employee_data.replace = true;
          }
          if (opt == 2) {
            scope.remove_employee_data.reserve = true;
          }
          if (opt == 3) {
            scope.remove_employee_data.remove = true;
          }
        }

        scope.changeMemberWalletUpdateStatus = function (opt) {

          scope.update_member_wallet_status = opt;
        }

        scope.showLoading = function () {
          $(".circle-loader").fadeIn();
          loading_trap = true;
        };

        scope.hideLoading = function () {
          setTimeout(function () {
            $(".circle-loader").fadeOut();
            loading_trap = false;
          }, 10);
        };

        scope.isCalculateBtnActive = false;

        scope.calculateHealthSpending = function () {
          var dates = {
            start: moment(scope.health_spending_summary.date.pro_rated_start, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            end: moment(scope.health_spending_summary.date.pro_rated_end, 'DD/MM/YYYY').format('YYYY-MM-DD'),
          }
          scope.isCalculateBtnActive = true;
          scope.getSpendingAccountSummary(moment(scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY').format('MM/DD/YYYY'), dates);
        }

        scope.initializeNewCustomDatePicker = function () {
          setTimeout(function () {
            $('.btn-custom-start').daterangepicker({
              autoUpdateInput: true,
              autoApply: true,
              singleDatePicker: true,
              startDate: moment(scope.health_spending_summary.date.pro_rated_start, 'DD/MM/YYYY').format('MM/DD/YYYY'),
            }, function (start, end, label) {
              scope.health_spending_summary.date.pro_rated_start = moment(start).format('DD/MM/YYYY');
              $("#rangePicker_start").text(scope.health_spending_summary.date.pro_rated_start);
              $('.btn-custom-end').data('daterangepicker').setMinDate(start);

              // if( scope.rangePicker_end && ( moment(scope.rangePicker_end,'DD/MM/YYYY') < moment(scope.rangePicker_start,'DD/MM/YYYY') ) ){
              //   scope.rangePicker_end = moment( start ).format( 'DD/MM/YYYY' );
              //   $("#rangePicker_end").text( scope.rangePicker_end );
              // }
            });

            $('.btn-custom-end').daterangepicker({
              autoUpdateInput: true,
              autoApply: true,
              singleDatePicker: true,
              startDate: moment(scope.health_spending_summary.date.pro_rated_end, 'DD/MM/YYYY').format('MM/DD/YYYY'),
            }, function (start, end, label) {
              scope.health_spending_summary.date.pro_rated_end = moment(end).format('DD/MM/YYYY');
              $("#rangePicker_end").text(scope.health_spending_summary.date.pro_rated_end);
            });

            var start = moment(scope.health_spending_summary.date.pro_rated_start, 'DD/MM/YYYY').format('DD/MM/YYYY');
            var end = moment(scope.health_spending_summary.date.pro_rated_end, 'DD/MM/YYYY').format('DD/MM/YYYY');
            $("#rangePicker_start").text(start);
            $("#rangePicker_end").text(end);
            $('.btn-custom-end').data('daterangepicker').setMinDate(start);
          }, 100);
        }

        scope.removeBackBtn = function () {
          if (scope.isRemoveEmployeeShow == true) {
            $('.employee-information-wrapper').fadeIn();
            $('.prev-next-buttons-container').hide();
            $('.remove-employee-wrapper').hide();
            scope.reset();
            scope.isEmployeeShow = true;
          } else if (scope.isRemoveEmployeeOptionsShow == true) {
            $('.remove-employee-wrapper').fadeIn();
            $('.employee-standalone-pro-wrapper').hide();
            scope.reset();
            scope.isRemoveEmployeeShow = true;
          } else if (scope.isReplaceEmpShow == true || scope.isReserveEmpShow == true) {
            $('.employee-standalone-pro-wrapper').fadeIn();
            $('.employee-replacement-wrapper').hide();
            $('.dependent-replacement-wrapper').hide();
            $('.hold-seat-wrapper').hide();
            scope.reset();
            scope.isRemoveEmployeeOptionsShow = true;
            iti2.destroy();
          } else if (scope.isHealthSpendingAccountSummaryShow == true) {
            $('.account-summary-wrapper').hide();
            $('.prev-next-buttons-container').hide();
            $(".employee-information-wrapper").fadeIn();
            scope.reset();
            scope.isCalculateBtnActive = false;
            scope.isEmployeeShow = true;
          } else if (scope.isHealthSpendingAccountShow == true) {
            $('.health-spending-account-wrapper').hide();
            $('.prev-next-buttons-container').hide();
            $(".employee-information-wrapper").fadeIn();
            scope.reset();
            scope.isCalculateBtnActive = false;
            scope.isEmployeeShow = true;
          } else if (scope.isRefundSummaryShow == true ) {
						scope.isRefundSummaryShow = false;
						$('.employee-information-wrapper').fadeIn();
            $('.prev-next-buttons-container').hide();
						$('.remove-employee-wrapper').hide();
            scope.reset();
            scope.isEmployeeShow = true;
					}
        }

        scope.showCalculation = false;
        scope.removeNextBtn = function () {
          if (scope.isRemoveEmployeeShow == true) {
            $('.employee-standalone-pro-wrapper').fadeIn();
            $('.remove-employee-wrapper').hide();
            scope.reset();
            scope.isRemoveEmployeeOptionsShow = true;
          } else if (scope.isRemoveEmployeeOptionsShow == true) {
            if (scope.remove_employee_data.remove != true) {
              $('.employee-standalone-pro-wrapper').hide();
              scope.reset();
              if (scope.remove_employee_data.replace == true) {
                if (scope.isDeleteDependent == true) {
                  $('.dependent-replacement-wrapper').fadeIn();
                } else {
                  $('.employee-replacement-wrapper').fadeIn();
                  scope.inititalizeGeoCode();
                }
                scope.isReplaceEmpShow = true;
                scope.replace_emp_data.plan_start = moment(scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY').add(1, 'days').format('DD/MM/YYYY');
              }
              if (scope.remove_employee_data.reserve == true) {
                // $('.hold-seat-wrapper').fadeIn();
                scope.isReserveEmpShow = true;
                if (scope.isDeleteDependent == true) {
									console.log('1');
                  scope.reserveDependent();
                } else {
                  if(scope.litePlanCheckbox){
										console.log('2');
                    scope.removeEmployeeRequests();
                  }else{
										console.log('3');
										let account_type = scope.account_plan_status.account_type;

										if ( account_type == 'enterprise_plan' && scope.selectedEmployee.wellness_wallet == false) {
											scope.removeEmployeeRequests();
										} else {
											scope.getSpendingAccountSummary(moment(scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY').format('MM/DD/YYYY'));
											$('.employee-standalone-pro-wrapper').hide();
											$(".account-summary-wrapper").fadeIn();
										}
                    
                    scope.reset();
                    scope.isHealthSpendingAccountSummaryShow = true;
                    scope.getSession();
                  }
                  
                }
              }
            } else {
              if (scope.isDeleteDependent == true) {
                scope.deleteDependent();
              } else {
                swal({
                  title: "Confirm",
                  text: "Are you sure you want to remove this employee completely?",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#ff6864",
                  confirmButtonText: "Remove",
                  cancelButtonText: "No",
                  closeOnConfirm: true,
                  customClass: "removeEmp"
                },
                  function (isConfirm) {
                    if (isConfirm) {

                      if(scope.litePlanCheckbox){
                        scope.removeEmployeeRequests();
                      }else{
                        scope.getSpendingAccountSummary(moment(scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY').format('MM/DD/YYYY'));
                        $('.employee-standalone-pro-wrapper').hide();
                        
                        let account_type = scope.account_plan_status.account_type;
                        // let account_type = 'enterprise_plan';
                        console.log('account_type', account_type);
                        
                        if (account_type =='lite_plan') {

                          $(".account-summary-wrapper").fadeIn(); // Show Health Spending Acct Summary
                          scope.reset();
                          // scope.isHealthSpendingAccountSummaryShow = true;  // Show Health Spending Acct Summary
                          scope.getSession();

                        } else if (account_type == 'enterprise_plan') {
													// Enterprise only ------------
													scope.removeEmployeeRequests();
													var data = {
														member_id : scope.selectedEmployee.user_id,
														refund_date: moment(scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY').format('YYYY/MM/DD'),
													}
													scope.get_member_refund(data);
                          scope.reset();
                          scope.isRefundSummaryShow = true; // show refund summary 
                          scope.getSession();
                          // end of Enterprise only -----
                          
                        } else {
                          $(".account-summary-wrapper").fadeIn(); // Show Health Spending Acct Summary
                          scope.reset();
                          // scope.isHealthSpendingAccountSummaryShow = true;  // Show Health Spending Acct Summary
                          scope.getSession();
                        }
                        
                      }
                    }
                  });
              }

            }
          } else if (scope.isReplaceEmpShow == true) {
            if (scope.isDeleteDependent == true) {
              scope.replaceDependent(scope.replace_emp_data);
            } else {
              // scope.replaceEmployee( scope.replace_emp_data );
              if (scope.checkReplaceEmployeeForm(scope.replace_emp_data) == true) {
                // swal({
                //   title: "Confirm",
                //   text: "Are you sure you want to replace existing employee?",
                //   type: "warning",
                //   showCancelButton: true,
                //   confirmButtonColor: "#0392CF",
                //   confirmButtonText: "Replace",
                //   cancelButtonText: "No",
                //   closeOnConfirm: true,
                //   customClass: "updateEmp"
                // },
                // function(isConfirm){
                //   if(isConfirm){
                  if(scope.litePlanCheckbox){
                    scope.removeEmployeeRequests();
                  }else{
                    scope.getSpendingAccountSummary(moment(scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY').format('MM/DD/YYYY'));
                    $('.employee-replacement-wrapper').hide();
                    $(".account-summary-wrapper").fadeIn();

                    scope.reset();
                    scope.isHealthSpendingAccountSummaryShow = true;
                    scope.getSession();
                  }
                //   }
                // });
              }
            }
          } else if (scope.isReserveEmpShow == true) {
            if (scope.isDeleteDependent == true) {
              scope.reserveDependent();
            } else {
              // scope.reserveEmployee( );
              if(scope.litePlanCheckbox){
                scope.removeEmployeeRequests();
              }else{
                scope.getSpendingAccountSummary();
                $('.hold-seat-wrapper').hide();
                $(".account-summary-wrapper").fadeIn();

                scope.reset();
                scope.isHealthSpendingAccountSummaryShow = true;
                scope.getSession();
              }
            }
          } else if (scope.isHealthSpendingAccountSummaryShow == true) {
            if (scope.isCalculateBtnActive == false) {
              swal('Error!', 'Please click the calcultate button first.', 'error');
              return false;
            } else {
              $('.health-spending-account-wrapper').fadeIn();
              $('.account-summary-wrapper').hide();
              scope.reset();
              scope.isHealthSpendingAccountShow = true;
            }
          }
        }



        //----- HTTP REQUESTS -----//

        scope.confirmWalletUpdateBtn = function () {
          if (scope.update_member_wallet_status) {
            var dates = {
              start: moment(scope.health_spending_summary.date.pro_rated_start, 'DD/MM/YYYY').format('YYYY-MM-DD'),
              end: moment(scope.health_spending_summary.date.pro_rated_end, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            }
            dependentsSettings.updateWalletMember(scope.selectedEmployee.user_id, scope.selected_customer_id, scope.health_spending_summary.medical.exceed, scope.health_spending_summary.wellness.exceed, moment(scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'), dates)
              .then(function (response) {
                // console.log( response );
                if (response.data.status) {
                  // swal('Success!', response.data.message, 'success');
                  swal('Success!', "Member has successfully scheduled for remove and credits updated according.", 'success');
                  $('.health-spending-account-wrapper').hide();
                  $('.prev-next-buttons-container').hide();
                  $('.employee-information-wrapper').fadeIn();
                  scope.reset();
                  scope.isEmployeeShow = true;
                } else {
                  swal('Error!', response.data.message, 'error');
                }
              });
          } else {
            swal('Success!', "Member has successfully scheduled for remove.", 'success');
            $('.health-spending-account-wrapper').hide();
            $('.prev-next-buttons-container').hide();
            $('.employee-information-wrapper').fadeIn();
            scope.reset();
            scope.isEmployeeShow = true;
          }

          scope.removeEmployeeRequests();
        }

        scope.removeEmployeeRequests  = function(){
          console.log(scope.remove_employee_data);
          if (scope.remove_employee_data.remove == true) {
            scope.confirmWalletUpdateBtn();
          }
          if (scope.remove_employee_data.reserve == true) {
            scope.reserveEmployee();
          }
          if (scope.remove_employee_data.replace == true) {
            scope.replaceEmployee(scope.replace_emp_data);
          }
        }

        scope.getSpendingAccountSummary = function (last_date_of_coverage, dates) {
          scope.showLoading();
          dependentsSettings.fetchEmpAccountSummary(scope.selectedEmployee.user_id, scope.selected_customer_id, moment(last_date_of_coverage, 'MM/DD/YYYY').format('YYYY-MM-DD'), dates)
            .then(function (response) {
              console.log(response);
              scope.health_spending_summary = response.data;
              scope.getTotalMembers();
              scope.initializeNewCustomDatePicker();
              scope.hideLoading();
              // if( scope.health_spending_summary.medical != false || scope.health_spending_summary.wellness != false ){
              //   if( scope.health_spending_summary.medical.exceed == true || scope.health_spending_summary.wellness.exceed == true ){
              //     $(".prev-next-buttons-container").fadeIn();
              //   }else{
              //     $(".prev-next-buttons-container").hide();
              //   }
              // }else{
              // scope.reset();
              // $('.account-summary-wrapper').hide();
              // $('.health-spending-account-wrapper').hide();
              // $('.prev-next-buttons-container').hide();
              // $(".employee-information-wrapper").hide();
              // $(".hrdb-body-container").fadeIn();
              // $('body').scrollTop(0);
              // $timeout(function() {
              //   $('body').css('overflow','hidden');
              // }, 200);
              // }
            });
        }

        scope.deleteDependent = function () {
          // console.log( data );
          swal({
            title: "Confirm",
            text: "Are you sure you want to remove this dependent?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ff6864",
            confirmButtonText: "Remove",
            cancelButtonText: "No",
            closeOnConfirm: true,
            customClass: "removeEmp"
          },
            function (isConfirm) {
              if (isConfirm) {
                scope.showLoading();
                var data = {
                  expiry_date: moment(scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'),
                  user_id: scope.selectedDependent.user_id
                }
                dependentsSettings.removeDependent(data)
                  .then(function (response) {
                    scope.hideLoading();
                    // console.log(response);
                    if (response.data.status) {
                      swal('Success!',
                        response.data.message, 'success');
                      $('.employee-standalone-pro-wrapper').hide();
                      $('.prev-next-buttons-container').hide();
                      $('.employee-information-wrapper').fadeIn();
                      scope.reset();
                      scope.isEmployeeShow = true;
                      scope.getSession();
                    } else {
                      $('.employee-standalone-pro-wrapper').fadeIn();
                      swal('Error!', response.data.message, 'error');
                    }
                  });
              }
            });
        }

        scope.reserveDependent = function () {
          var data = {
            user_id: scope.selectedDependent.user_id,
            date_enrollment: moment(scope.reserve_emp_date, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            last_date_of_coverage: moment(scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            customer_id: scope.selected_customer_id,
          }
          scope.showLoading();
          dependentsSettings.reserveDependentService(data)
            .then(function (response) {
              // console.log( response );
              scope.hideLoading();
              if (response.data.status) {
                swal('Success!', response.data.message, 'success');
                $('.hold-seat-wrapper').hide();
                $('.prev-next-buttons-container').hide();
                $('.employee-information-wrapper').fadeIn();
                scope.reset();
                scope.isEmployeeShow = true;
                scope.getSession();
              } else {
                $('.employee-standalone-pro-wrapper').fadeIn();
                swal('Error!', response.data.message, 'error');
              }
            });
        }

        scope.replaceDependent = function (data) {
          // console.log( data );
          if (scope.checkDependentForm(data) == true) {
            // swal({
            //   title: "Confirm",
            //   text: "Are you sure you want to replace existing employee?",
            //   type: "warning",
            //   showCancelButton: true,
            //   confirmButtonColor: "#0392CF",
            //   confirmButtonText: "Replace",
            //   cancelButtonText: "No",
            //   closeOnConfirm: true,
            //   customClass: "updateEmp"
            // },
            // function(isConfirm){
            //   if(isConfirm){
            scope.showLoading();
            data.last_day_coverage = moment(scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD');
            data.plan_start = moment(data.start_date, 'DD/MM/YYYY').format('YYYY-MM-DD');
            data.replace_id = scope.selectedDependent.user_id;
            data.customer_id = scope.selected_customer_id;
            dependentsSettings.replaceDependentService(data)
              .then(function (response) {
                scope.hideLoading();
                // console.log(response);
                if (response.data.status) {
                  swal('Success!', response.data.message, 'success');
                  $('.dependent-replacement-wrapper').hide();
                  $('.prev-next-buttons-container').hide();
                  $('.employee-information-wrapper').fadeIn();
                  scope.reset();
                  scope.isEmployeeShow = true;
                  scope.getSession();
                } else {
                  $('.employee-standalone-pro-wrapper').fadeIn();
                  swal('Error!', response.data.message, 'error');
                }
              });
            //   }
            // });
          }
        }

        scope.reserveEmployee = function () {
          var data = {
            employee_id: scope.selectedEmployee.user_id,
            // date_enrollment : moment( scope.reserve_emp_date, 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            last_date_of_coverage: moment(scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            customer_id: scope.selected_customer_id
          }
          scope.showLoading();
          dependentsSettings.reserveEmployee(data)
            .then(function (response) {
              // console.log( response );
              scope.hideLoading();
              if (response.data.status) {
                // swal( 'Success!', response.data.message, 'success' );
                // scope.getSpendingAccountSummary( scope.remove_employee_data.last_day_coverage );
                // $('.hold-seat-wrapper').hide();
                // $(".account-summary-wrapper").fadeIn();
                // scope.reset();
                // scope.isHealthSpendingAccountSummaryShow = true;
                scope.getSession();
              } else {
                // swal('Error!', response.data.message, 'error');
              }
            });
        }
        scope.global_empLimitList = 5;
        scope.getEmployeeList = function (page) {
          $(".employee-overview-pagination").show();

          scope.showLoading();
          hrSettings.getEmployees(page,scope.page_ctr)
            .then(function (response) {
              console.log(response);
              scope.employees = response.data;
              scope.employees.total_allocation = response.data.total_allocation;
              scope.employees.allocated = response.data.allocated;
          
              angular.forEach(scope.employees.data, function (value, key) {
                value.fname = scope.employees.data[key].name.substring(0, value.name.lastIndexOf(" "));
                value.lname = scope.employees.data[key].name.substring(value.name.lastIndexOf(" ") + 1);
                value.start_date = moment(value.start_date).format("DD/MM/YYYY");
                value.start_date_format = moment(value.start_date, 'DD/MM/YYYY').format("DD MMMM YYYY");
                value.end_date_format = moment(value.expiry_date).format("DD MMMM YYYY");
                value.expiry_date = moment(value.expiry_date).format("MM/DD/YYYY");
                value.dob = moment(value.dob).format('DD/MM/YYYY');
              });
              $(".loader-table").hide();
              $(".main-table").fadeIn();
              scope.hideLoading();

              if (scope.selectedEmployee_index != null) {
                scope.selectedEmployee = scope.employees.data[scope.selectedEmployee_index];
                // scope.selectedEmployee.dob = moment(scope.selectedEmployee.dob, ['YYYY-MM-DD', 'DD/MM/YYYY']).format('DD/MM/YYYY');
                // console.log(scope.selectedEmployee.dob);
                console.log(scope.selectedEmployee);
                if (scope.selectedEmployee.plan_tier != null || scope.selectedEmployee.plan_tier) {
                  scope.addActiveDependent_index = scope.selectedEmployee.plan_tier.dependent_enrolled_count + 1;
                } else {
                  scope.addActiveDependent_index = scope.progress.completed + 1;
                }
                scope.fetchRefundStatus(scope.selectedEmployee.user_id);
                scope.getEmpPlans(scope.selectedEmployee.user_id);
                scope.getEmpDependents(scope.selectedEmployee.user_id);
              }

            });
        };

        scope.replaceEmployee = function (data) {
          scope.showLoading();
          data.dob = moment(scope.remove_employee_data.dob).format('YYYY-MM-DD');
          data.last_day_coverage = moment(scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD');
          data.replace_id = scope.selectedEmployee.user_id;
          data.plan_start = moment(data.plan_start, 'DD/MM/YYYY').format('YYYY-MM-DD');
          if (!data.medical_credits) {
            data.medical_credits = 0;
          }

          if (!data.wellness_credits) {
            data.wellness_credits = 0;
          }
          dependentsSettings.replaceEmployee(data)
            .then(function (response) {
              scope.hideLoading();
              // console.log(response);
              if (response.data.status) {
                // swal( 'Success!', response.data.message, 'success' );
                // scope.getSpendingAccountSummary( scope.remove_employee_data.last_day_coverage );
                // $('.employee-replacement-wrapper').hide();
                // $(".account-summary-wrapper").fadeIn();
                // scope.reset();
                // scope.isHealthSpendingAccountSummaryShow = true;
                scope.getSession();
              } else {
                // swal('Error!', response.data.message, 'error');
              }
            });

        }

        scope.saveActiveDependents = function () {
          // console.log( scope.addDependents_arr );
          console.log(scope.dependent_data);
          if ((scope.dependent_data.fullname && scope.dependent_data.dob) || scope.addDependents_arr.length == 0) {
            if (scope.checkDependentForm(scope.dependent_data) == true) {
              if (!scope.addDependents_arr[scope.dependents_ctr]) {
                scope.addActiveDependent_index += 1;
                scope.dependent_data.dob = moment(scope.dependent_data.dob, 'DD/MM/YYYY').format('YYYY-MM-DD');
                scope.dependent_data.start_date = moment(scope.dependent_data.start_date, 'DD/MM/YYYY').format('YYYY-MM-DD');
                scope.addDependents_arr.push(scope.dependent_data);
              } else {

              }
            } else {
              return false;
            }
          }

          console.log(scope.addDependents_arr);

          scope.showLoading();
          var data = {
            customer_id: scope.selected_customer_id,
            employee_id: scope.selectedEmployee.user_id,
            dependents: scope.addDependents_arr
          }
          dependentsSettings.addDependentForEmployee(data)
            .then(function (response) {
              scope.hideLoading();
              // console.log(response);
              if (response.data.status) {
                swal('Success!', response.data.message, 'success');
                scope.addDependents_arr = [];
                scope.dependent_data = {};
                scope.dependents_ctr = 0;
                scope.getEmpDependents(scope.selectedEmployee.user_id);
                scope.toggleAddDependents();
                scope.getEmployeeList(scope.page_active);
              } else {
                swal('Error!', response.data.message, 'error');
              }
            });
        }

        scope.saveEmployee = function (data) {
          if (scope.checkUpdateEmployeeForm(data) == false) {
            return false;
          }
          console.log(data);
          swal({
            title: "Confirm",
            text: "Are you sure you want to update this employee?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0392CF",
            confirmButtonText: "Update",
            cancelButtonText: "No",
            closeOnConfirm: true,
            customClass: "updateEmp"
          },
            function (isConfirm) {
              if (isConfirm) {
                scope.showLoading();
                console.log(data);
                var update_data = {
                  name: data.name,
                  dob: moment(data.dob, 'DD/MM/YYYY').format('YYYY-MM-DD'),
                  nric: data.nric == '' || data.nric == null ? '' : data.nric,
                  email: data.email,
                  phone_no: data.phone_no,
                  // country_code: data.country_code.replace('+', ''),
                  country_code: scope.editEmpCountryCode.toString().replace('+', ''),
                  job_title: data.job_title,
                  postal_code: data.postal_code,
                  bank_account: data.bank_account,
                  bank_code: data.bank_code,
                  bank_branch: data.bank_branch,
                  user_id: data.user_id,
                  bank_name: data.bank_name,
                  emp_id: data.employee_id,
                  passport: data.passport,
                  // phone_code: scope.editEmpCountryCode,
                };
                console.log(update_data);
                dependentsSettings.updateEmployee(update_data)
                  .then(function (response) {
                    scope.hideLoading();
                    // console.log(response);
                    if (response.data.status) {
                      swal('Success!', response.data.message, 'success');
                      $("#update-employee-modal").modal('hide');
                      scope.getSession();
                    } else {
                      swal('Error!', response.data.message, 'error');
                    }
                  });
              }
            });
        }

        scope.saveDependent = function (data) {
          var dob = moment(data.dob, 'DD/MM/YYYY');
          var today = moment();
          console.log(dob.diff(today, 'days'));
          if (dob.diff(today, 'days') <= 0) {

          } else {
            swal('Error!', 'Date of Birth is Invalid.', 'error');
            return false;
          }
          swal({
            title: "Confirm",
            text: "Are you sure you want to update this dependent?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0392CF",
            confirmButtonText: "Update",
            cancelButtonText: "No",
            closeOnConfirm: true,
            customClass: "updateEmp"
          },
            function (isConfirm) {
              if (isConfirm) {
                scope.showLoading();
                dependentsSettings.updateDependent(data)
                  .then(function (response) {
                    scope.hideLoading();
                    // console.log(response);
                    if (response.data.status) {
                      swal('Success!', response.data.message, 'success');
                      $("#update-dependent-modal").modal('hide');
                      scope.getSession();
                    } else {
                      swal('Error!', response.data.message, 'error');
                    }
                  });
              }
            });
        }

        scope.confirmWalletUpdateBtn = function () {
          scope.showLoading();
          var users = [{
            expiry_date: moment(scope.remove_employee_data.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            user_id: scope.selectedEmployee.user_id
          }];
          dependentsSettings.removeEmployee(users)
            .then(function (response) {
              scope.hideLoading();
              // console.log(response);
              if (response.data.status) {
                // swal( 'Success!', response.data.message, 'success' );
                // scope.getSpendingAccountSummary( scope.remove_employee_data.last_day_coverage );
                // $('.employee-standalone-pro-wrapper').hide();
                // $(".account-summary-wrapper").fadeIn();
                // scope.reset();
                // scope.isHealthSpendingAccountSummaryShow = true;
                scope.getSession();
              } else {
                // swal( 'Error!', response.data.message, 'error');
              }
            });
        }

        scope.getProgress = function () {
          hrSettings.getEnrollmentProgress()
            .then(function (response) {
              scope.hideLoading();
              // console.log( response );
              scope.progress = response.data.data;
            });
        };

        scope.companyDependents = function () {
          hrSettings.companyDependents()
            .then(function (response) {
              scope.dependents = response.data;
              scope.overall_dep_count = scope.dependents.occupied_seats + 1;
              // console.log(scope.dependents);
            });
        }

        scope.last_term_credits = false;
        scope.empDetailsLoadingState = function(){
          window.open(serverUrl.url + '/hr/get_company_employee_lists_credits?token=' + window.localStorage.getItem('token'));
          // scope.showLoading();
          // $(".export-emp-details-message").show();
          // hrSettings.getEployeeDetails()
          //   .then(function(response) {
          //     scope.last_term_credits = response.data.last_term_credits;
          //     scope.allEmpData = response.data.data;
          //     scope.export_member_details = response.data;
          //     scope.hideLoading();
          //     setTimeout(function() {
          //       $( ".export-emp-details-message" ).hide();
          //       $( "#empDetailsBtn" ).click();
          //     }, 10);
          //   });
        }

        scope.checkCompanyBalance = function () {
          hrSettings.getCheckCredits()
            .then(function (response) {
              console.log(response);
              scope.credit_status = response.data;
            });
        };

        scope.userCompanyCreditsAllocated = function () {
          hrSettings.userCompanyCreditsAllocated()
            .then(function (response) {
              scope.company_properties = response.data;
            });
        }

        scope.getPlanStatus = function () {
          hrSettings.getPlanStatus()
            .then(function (response) {
              console.log(response);
              scope.plan_status = response.data;
            });
        }

        scope.getTotalMembers = function () {
          hrSettings.getCountMembers()
            .then(function (response) {
              // console.log(response);
              scope.member_count = response.data.total_members;
            });
        }

        scope.getEmpDependents = function (id) {
          hrSettings.getDependents(id)
            .then(function (response) {
              // console.log(response);
              scope.selected_emp_dependents = response.data.dependents;
              angular.forEach(scope.selected_emp_dependents, function (value, key) {
                value.dob = moment(value.dob).format('DD/MM/YYYY');
              });
            });
        }

        scope.checkDependentsStatus = function () {
          hrSettings.getMethodType()
            .then(function (response) {
              console.log(response);
              scope.dependents_status = response.data.data;

              scope.account_plan_status = {
                plan_method: response.data.data.plan.plan_method,
                account_type: response.data.data.plan.account_type
              }
            });
        }

        scope.getJobs = function () {
          hrSettings.getJobTitle()
            .then(function (response) {
              // console.log( response );
              scope.job_list = response.data;
            });
        };

        scope.fetchRefundStatus = function (id) {
          hrSettings.getRefundStatus(id)
            .then(function (response) {
              // console.log( response );
              scope.refund_status = response.data.refund_status;
            });
        };

        scope.reset = function () {
          scope.isEmployeeShow = false;
          scope.isAddDependentsShow = false;
          scope.isRemoveEmployeeShow = false;
          scope.isRemoveEmployeeOptionsShow = false;
          scope.isHealthSpendingAccountSummaryShow = false;
          scope.isHealthSpendingAccountShow = false;
          scope.isReplaceEmpShow = false;
          scope.isReserveEmpShow = false;
          scope.isCalculateBtnActive = false;
        }

        scope.getSession = function () {
          hrSettings.getSession()
            .then(function (response) {
              // console.log( response );
              scope.selected_customer_id = response.data.customer_buy_start_id;
              scope.options.accessibility = response.data.accessibility;
              if (scope.isSearchEmp) {
                scope.searchEmployee(scope.inputSearch);
              } else {
                scope.getEmployeeList(scope.page_active);
              }
              scope.getTotalMembers();
              scope.getProgress();
            });
        }


        scope.healthSpendingAccountTabIsShow = false;
        scope.viewEmployeeSpendingSummary = function () {
          if (scope.healthSpendingAccountTabIsShow == false) {
            // scope.getSpendingAccountSummary(scope.selectedEmployee.expiry_date);
            scope.empTabSelected = 99;
            scope.healthSpendingAccountTabIsShow = true;
            $('body').scrollTop(0);
            $state.go('employee-overview.health-spending-account-summary');
          } else {
            scope.empTabSelected = 0;
            scope.healthSpendingAccountTabIsShow = false;
          }
        }
        
        

        scope.inititalizeGeoCode = function () {
          $timeout(function () {
            var input = document.querySelector("#area_code");
            var settings = {
              separateDialCode: true,
              initialCountry: "SG",
              autoPlaceholder: "off",
              utilsScript: "../assets/hr-dashboard/js/utils.js",
              onlyCountries: ['SG','MY','PH'],
            };
            iti = intlTelInput(input, settings);
            iti.setNumber(scope.selectedEmployee.mobile_no);
            console.log(scope.selectedEmployee);
            if (scope.selectedEmployee.country_code == null) {
              scope.selectedEmployee.country_code = '+65';
            }
            scope.selectedEmployee.phone_no = scope.selectedEmployee.phone_no;
            $("#area_code").val(scope.selectedEmployee.phone_no);
            input.addEventListener("countrychange", function () {
              console.log(iti.getSelectedCountryData());
              scope.selectedEmployee.country_code = iti.getSelectedCountryData().dialCode;
              scope.selectedEmployee.mobile_area_code = iti.getSelectedCountryData().dialCode;
              scope.selectedEmployee.mobile_area_code_country = iti.getSelectedCountryData().iso2;
            });

            var input2 = document.querySelector("#area_code2");
            iti2 = intlTelInput(input2, settings);
            iti2.setCountry("SG");
            scope.replace_emp_data.country_code = '+65';
            input2.addEventListener("countrychange", function () {
              console.log(iti2.getSelectedCountryData());
              scope.replace_emp_data.country_code = iti2.getSelectedCountryData().dialCode;
              scope.replace_emp_data.mobile_area_code = iti2.getSelectedCountryData().dialCode;
              scope.replace_emp_data.mobile_area_code_country = iti2.getSelectedCountryData().iso2;
            });

            var settings_emp_details = {
              preferredCountries: [],
              separateDialCode: true,
              initialCountry: "SG",
              autoPlaceholder: "off",
              utilsScript: "../assets/hr-dashboard/js/utils.js",
              onlyCountries: ["sg","my", "ph"],
            }
            
            var input3 = document.querySelector("#phoneNum");
            iti3 = intlTelInput(input3, settings_emp_details);

            input3.addEventListener("countrychange", function () {
              console.log(iti3.getSelectedCountryData());
              scope.editEmpCountryCode = iti3.getSelectedCountryData().dialCode;
              console.log(scope.editEmpCountryCode);
            });

          }, 300);
				}
				
				scope.member_refund_details = {};
				scope.get_member_refund = function (data) {
					hrSettings.get_member_refund(data)
					.then(function (response) {
						console.log('refund ni',response);
						scope.member_refund_details = response.data.data;
						scope.member_refund_details.unutilised_start_date = moment(scope.member_refund_details.unutilised_start_date).format('DD/MM/YYYY');
						scope.member_refund_details.unutilised_end_date = moment(scope.member_refund_details.unutilised_end_date).format('DD/MM/YYYY');

					});
				}

        scope._showAddFilterModal_ = function() {
          $("#add-filter-modal").modal('show');

          scope.global_statusData = {
            pending: false,
            logged_in: false,
            active: false,
            removed: false,
          }
        }

        scope._resetActivation_ = function () {
          let params = {
            id: scope.selectedEmployee.member_id,
          }
          scope.showLoading();
          hrSettings.employeeResetActivation ( params  )
            .then(function( response ) {
              console.log(response);

              if ( response.data.status == true ) {
                scope.hideLoading();
                swal('Success!', response.data.message, 'success');
              }
            });
        }

        scope._resetPassword_ = function() {
          let params = {
            id: scope.selectedEmployee.member_id,
          }
          scope.showLoading();
          hrSettings.employeeResetPassword ( params  )
            .then(function( response ) {
              console.log(response);

              if ( response.data.status == true ) {
                scope.hideLoading();
                swal('Success!', response.data.message, 'success');
              }
            });
        }

        scope._statusClear_ = function ( data ) {
          data.pending = false;
          data.logged_in = false;
          data.active = false;
          data.removed = false;
        }
        
        
        scope._empApplyFilter_ = function ( data ) {
          
          console.log( data );
          scope.showLoading();
          hrSettings.getFilterEmployees ( scope.page_active,scope.global_empLimitList,data.pending,data.logged_in,data.active,data.removed  )
          .then(function( response ) {
            console.log(response);
            scope.employees = response.data;
            // scope.getEmployeeList( scope.page_active );
            scope.hideLoading();
            $('#add-filter-modal').modal('hide');            
          });
        }

        scope._cancelModal_ = function () {
          $('#add-filter-modal').modal('hide');
        }

        scope.listBank = function () {
          $('.bank-list-wrapper').toggle();
        }
        
        scope.selectBank = function ( data ) {
          scope.selectedEmployee.bank_name = data;
          $('.bank-list-wrapper').hide();
        }

        scope.checkNRIC = function (theNric) {
          var nric_pattern = null;
          if (theNric.length == 9) {
            nric_pattern = new RegExp("^[stfgSTFG]{1}[0-9]{7}[a-zA-z]{1}$");
          } else if (theNric.length == 12) {
            // nric_pattern = new RegExp("^[0-9]{2}(?:0[1-9]|1[-2])(?:[0-1]|[1-2][0-9]|[3][0-1])[0-9]{6}$");
            return true;
          } else {
            return false;
          }
          return nric_pattern.test(theNric);
				};
				
				scope.checkPassport = function (value) {
          let passport_pattern = null;
          if (value) {
            passport_pattern = new RegExp("^[a-zA-Z][a-zA-Z0-9.,$;]+$");
          } else {
            return false;
          }
          return passport_pattern.test(value);
        };

        scope.checkSpendingValuesStatus = function(){
          if( scope.spending_account_status.account_type == 'lite_plan' ){
            if(scope.spending_account_status.medical_enabled == true || scope.spending_account_status.wellness_enabled == true){
              scope.showBulkEntitlement = true;
            }
          }
          if( scope.spending_account_status.account_type == 'enterprise_plan' ){
            if(scope.spending_account_status.wellness_enabled == true){
              scope.showBulkEntitlement = true;
            }
          }
          if( scope.spending_account_status.account_type == 'out_of_pocket' ){
            if( scope.spending_account_status.wellness_enabled){
              scope.showBulkEntitlement = true;
            }
          }

          if(
            (scope.spending_account_status.account_type == 'lite_plan' && (scope.spending_account_status.medical_enabled)) ||
            (scope.spending_account_status.account_type == 'enterprise_plan' && scope.spending_account_status.currency_type == 'sgd' && (scope.spending_account_status.medical_enabled)) || 
            (scope.spending_account_status.account_type != 'lite_plan' && scope.spending_account_status.account_type != 'enterprise_plan' && (scope.spending_account_status.medical_enabled))
          ){
            scope.isMedicalShow = true;
          }
          if(
            (scope.spending_account_status.account_type == 'lite_plan' && (scope.spending_account_status.wellness_enabled)) ||
            (scope.spending_account_status.account_type == 'enterprise_plan' && scope.spending_account_status.currency_type == 'myr' && scope.spending_account_status.wellness_enabled) || 
            (scope.spending_account_status.account_type == 'enterprise_plan' && scope.spending_account_status.currency_type == 'sgd' && (scope.spending_account_status.wellness_enabled)) ||
            (scope.spending_account_status.account_type == 'out_of_pocket' && (scope.spending_account_status.wellness_enabled)) || 
            (scope.spending_account_status.account_type != 'lite_plan' && scope.spending_account_status.account_type != 'enterprise_plan' && scope.spending_account_status.account_type != 'out_of_pocket' && (scope.spending_account_status.wellness_enabled))
          ){
            scope.isWellnessShow = true;
          }
        }

        scope.onLoad = function () {
          console.log($state.current);
          if( $state.current.name != 'employee-overview' && $state.current.name.indexOf('employee-overview') > -1 ){
            $state.go('employee-overview');
          }
          // scope.checkCompanyBalance();
          scope.getPlanStatus();
          // scope.userCompanyCreditsAllocated();
          scope.getTotalMembers();
          scope.checkDependentsStatus();
          scope.companyDependents();
          scope.getJobs();
          scope.showLoading();
          scope.getSession();
          scope.companyAccountType();
          scope.getSpendingAcctStatus();
          console.log(scope.emp_entitlement.medical_new_entitlement);
        };

        scope.onLoad();

        $('body').css('overflow', 'hidden');

        // ----------------

        $("body").click(function (e) {
          if ($(e.target).parents(".per-page-pagination").length === 0) {
            $(".per_page").hide();
          }
        });

        $("body").delegate('.per_page li', 'click', function (e) {

          $(".per_page").hide();
        });

        var dt = new Date();
        // dt.setFullYear(new Date().getFullYear()-18);
        $('.datepicker').datepicker({
          format: 'dd/mm/yyyy',
          endDate: dt
        });

        $('.datepicker').datepicker().on('hide', function (evt) {
          var val = $(this).val();
          if (val != "") {
            $(this).datepicker('setDate', val);
          }
        })

        $('.start-date-datepicker-dependent').datepicker({
          format: 'dd/mm/yyyy',
        });

        $('.start-date-datepicker-dependent').datepicker().on('hide', function (evt) {
          var val = $(this).val();
          if (val == "") {
            $('.start-date-datepicker-dependent').datepicker('setDate', scope.selectedEmployee.start_date);
          }
        })

        $('.last-day-coverage-datepicker').datepicker({
          format: 'dd/mm/yyyy',

        });

        $('.last-day-coverage-datepicker').datepicker().on('hide', function (evt) {
          var val = $(this).val();
          if (val == "") {
            $('.last-day-coverage-datepicker').datepicker('setDate', moment(scope.remove_employee_data.last_day_coverage).format('DD/MM/YYYY'));
          }
        })

        $('.start-date-datepicker-replace').datepicker({
          format: 'dd/mm/yyyy',

        });

        $('.start-date-datepicker-replace').datepicker().on('hide', function (evt) {
          var val = $(this).val();
          if (val == "") {
            $('.start-date-datepicker-replace').datepicker('setDate', scope.selectedEmployee.start_date);
          }
        })

        $('.future-datepicker').datepicker({
          format: 'dd/mm/yyyy',
          startDate: moment().format('DD/MM/YYYY')
        });

        $('.future-datepicker').datepicker().on('hide', function (evt) {
          var val = $(this).val();
          if (val == "") {
            $('.future-datepicker').datepicker('setDate', moment().format('DD/MM/YYYY'));
          }
        })

        $('.modal').on('hidden.bs.modal', function () {
          if (scope.isUpdateEmpInfoModalOpen == true) {
            iti.destroy();
          }
          scope.isUpdateEmpInfoModalOpen = false;
          // iti2.destroy();
          // console.log(iti);
          // console.log(iti2);
        })

        // -------------- //

      }
    }
  }
]);
