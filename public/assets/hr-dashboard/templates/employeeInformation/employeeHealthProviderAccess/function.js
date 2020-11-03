app.directive('employeeHealthProviderAccessDirective', [
  '$http',
  'serverUrl',
  '$timeout',
  '$state',
  'employeeFactory',
  '$rootScope',
  'hrActivity',
  '$stateParams',
  function directive($http, serverUrl, $timeout, $state, employeeFactory, $rootScope, hrActivity, $stateParams) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log('employeeHealthProviderAccessDirective running!');
        scope.selected_member_id = $stateParams.member_id;
        scope.showBlockHealthProviders = false;
        scope.search = {
          clinic_open_search_text: '',
          clinic_blocked_search_text: '',
        }


        
        scope.getEmployeeDetails  = async function(isRefresh){
          scope.selectedEmployee = await employeeFactory.getEmployeeDetails();
          if( scope.selectedEmployee == null || scope.selectedEmployee.user_id != Number(scope.selected_member_id) || isRefresh ){
            await scope.fetchEmployeeDetails();
          }else{
            // scope.hideLoading();
          }
        }
        scope.fetchEmployeeDetails  = async function(){
          scope.showLoading();
          await $http.get(serverUrl.url + "/hr/employee/" + scope.selected_member_id)
            .then(function(response){
              console.log(response);
              scope.selectedEmployee = response.data.data;
              employeeFactory.setEmployeeDetails(scope.selectedEmployee);
              // scope.hideLoading();
            });
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
        scope.resetOpenCheckBoxes = async function () {
          scope.clinic_type_open_ids = [];
          scope.allOpenSelected = false;
          await angular.forEach(scope.clinic_type_open_arr, function (value, key) {
            value.selected = false;
          });
          await angular.forEach(scope.clinic_open_arr, function (value, key) {
            value.selected = false;
          });
        }
        scope.resetBlockCheckBoxes = async function () {
          scope.clinic_type_block_ids = [];
          scope.allBlockSelected = false;
          await angular.forEach(scope.clinic_type_block_arr, function (value, key) {
            value.selected = false;
          });
          await angular.forEach(scope.clinic_block_arr, function (value, key) {
            value.selected = false;
          });
        }

        scope.blockHealthPatnerLoad = function () {
          
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
            angular.forEach(scope.clinic_open_arr, function (value, key) {
              if (value.selected) {
                ctr += 1;
                scope.showLoading();
                scope.updateClinics(value.ClinicID, status, region, opt);
              }
              if (ctr > 0 && scope.clinic_open_arr.length - 1 == key) {
                scope.blockHealthPatnerLoad();
                swal('Success!', 'Clinic Block Lists updated.', 'success');
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
            angular.forEach(scope.clinic_block_arr, function (value, key) {
              if (value.selected) {
                ctr += 1;
                scope.showLoading();
                scope.updateClinics(value.ClinicID, status, region, opt);
              }
              if (ctr > 0 && scope.clinic_block_arr.length - 1 == key) {
                scope.blockHealthPatnerLoad();
                swal('Success!', 'Clinic Block Lists updated.', 'success');
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

        scope.blockToOpen = function (status, region, opt) {
          if (opt == 'name') {
            var ctr = 0;
            angular.forEach(scope.clinic_block_arr, function (value, key) {
              if (value.selected) {
                ctr += 1;
                scope.showLoading();
                scope.updateClinics(value.ClinicID, status, region, opt);
              }
              if (ctr > 0 && scope.clinic_block_arr.length - 1 == key) {
                scope.blockHealthPatnerLoad();
                swal('Success!', 'Clinic Block Lists updated.', 'success');
                scope.hideLoading();
              }
            });
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
              }
            });
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
        scope.getClinicTypes = async function () {
          await hrActivity.fetchClinicTypesEmp('open', scope.filter_regionOpened, scope.selectedEmployee.user_id)
            .then(function (response) {
              console.log(response);
              scope.clinic_type_open_arr = response.data;
            });
          await hrActivity.fetchClinicTypesEmp('block', scope.filter_regionBlocked, scope.selectedEmployee.user_id)
            .then(function (response) {
              console.log(response);
              scope.clinic_type_block_arr = response.data;
            });
        }
        scope.getBlockedClinics = async function () {
          scope.showLoading();
          await hrActivity.fetchBlockedClinicsEmp(scope.block_per_page, scope.block_page_active, scope.filter_regionBlocked, scope.search.clinic_blocked_search_text, scope.selectedEmployee.user_id)
            .then(async function (response) {
              // console.log(response);
              if (scope.search.clinic_blocked_search_text == null || scope.search.clinic_blocked_search_text == '') {
                scope.clinic_block_arr = response.data.data;
                scope.block_pagination = response.data;
                scope.isBlockSearch = false;
              } else {
                scope.clinic_block_arr = response.data;
                scope.isBlockSearch = true;
              }
              await scope.getOpenedClinics();
            });
        }
        scope.getOpenedClinics = async function () {
          await hrActivity.fetchOpenedClinicsEmp(scope.open_per_page, scope.open_page_active, scope.filter_regionOpened, scope.search.clinic_open_search_text, scope.selectedEmployee.user_id)
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


        // CUSTOM REUSABLE FUNCTIONS
        
        scope.formatMomentDate  = function(date, from, to){
          return moment(date, from).format(to);
        }
        scope.closeModal  = function(){
          $('.modal').modal('hide');
        }
        scope.range = function (range) {
          var arr = [];
          for (var i = 0; i < range; i++) {
            arr.push(i);
          }
          return arr;
        }
        scope.showLoading = function () {
          $(".circle-loader").fadeIn();
        };
        scope.hideLoading = function () {
          $timeout(function () {
            $(".circle-loader").fadeOut();
          }, 10);
        };

        scope.onLoad  = async function(){
          scope.showLoading();
          await scope.getEmployeeDetails();
          await scope.resetOpenCheckBoxes();
          await scope.resetBlockCheckBoxes();
          await scope.getClinicTypes();
          await scope.getBlockedClinics();
          scope.hideLoading();
        }
        scope.onLoad();
      }
    }
  }
]);