app.directive("employeeOverviewDirective", [
  "$state",
  "hrSettings",
  "hrActivity",
  "$rootScope",
  "dependentsSettings",
  "$timeout",
  "serverUrl",
  function directive($state, hrSettings, hrActivity, $rootScope, dependentsSettings, $timeout, serverUrl) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("employeeOverviewDirective Runnning !");
        scope.inputSearch = localStorage.getItem('isSearchEmp', scope.isSearchEmp ? scope.inputSearch : null);
        scope.isSearchEmp = (scope.inputSearch != null && scope.inputSearch != 'null');
        scope.employees = {};
        scope.options = {};
        scope.page_ctr = 5;
        scope.page_active = 1;
        scope.global_empLimitList = 5;
        scope.inputSearch = '';
        scope.pagesToDisplay = 10;
        scope.global_statusData = {
          pending: false,
          logged_in: false,
          active: false,
          removed: false,
        }
        scope.empNumber = 5;
        scope.locNumber = 10;
        scope.spending_account_status = {};
        scope.isAllEmpCheckboxSelected = false;
        scope.selectedEmpArr  = [];
        scope.isSelectOverallEmployees  = false;
        scope.isClickExportAll = false;

        scope.isTotalMembersShow = true;
        scope.isFiltersShow = false;
        scope.isStatusFiltersShow = false;
        scope.isLocationFiltersShow = false;
        scope.isDepartmentFiltersShow = false;

        scope.locationList  = [];
        scope.departmentList  = [];
        scope.isApplyFilter = false;

        scope.add_admin_data = {
          is_mednefits_emp : 1,
          view_employee_dependent: true,
          phone_code: '65',
          employee_name: '',
        }
        scope.permission_data = 'All Employees & Dependents';
        scope.showEmployeeList = false;
        scope.isEmployeePending = false;
        
        scope.empFiltersObj = {
          status : [
            {
              name: 'Pending',
              active: false,
            },
            {
              name: 'Activated',
              active: false,
            },
            {
              name: 'Active',
              active: false,
            },
          ],
          location: [],
          department: [],
          selectedLocations: [],
          selectedDepartments: [],
        }








        scope.empGetNumber = function (num) {
          return new Array(num);
        }
        scope.locGetNumber = function (num) {
          return new Array(num);
        }
        scope.getEmployeeList = async function () {
          // $(".employee-overview-pagination").show();
          scope.showLoading();
          await hrSettings.getEmployees(scope.page_active,scope.page_ctr,scope.empFiltersObj.status,scope.empFiltersObj.location,scope.empFiltersObj.department)
            .then(async function (response) {
              console.log(response);
              scope.employees = response.data;
              scope.employees.total_allocation = response.data.total_allocation;
              scope.employees.allocated = response.data.allocated;
          
              await angular.forEach(scope.employees.data, function (value, key) {
                value.fname = scope.employees.data[key].name.substring(0, value.name.lastIndexOf(" "));
                value.lname = scope.employees.data[key].name.substring(value.name.lastIndexOf(" ") + 1);
                value.start_date = moment(value.start_date).format("DD/MM/YYYY");
                value.start_date_format = moment(value.start_date, 'DD/MM/YYYY').format("DD MMMM YYYY");
                value.end_date_format = moment(value.expiry_date).format("DD MMMM YYYY");
                value.expiry_date = moment(value.expiry_date).format("MM/DD/YYYY");
                value.dob = moment(value.dob).format('DD/MM/YYYY');
              });
              scope.hideLoading();
            });
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
        scope.perPage = function (num) {
          scope.page_ctr = num;
          scope.page_active = 1;
          scope.getEmployeeList(scope.page_active);
        };
        scope.removeSearchEmp = function () {
          scope.inputSearch = "";
          scope.page_active = 1;
          scope.getEmployeeList(1);
        }
        scope.searchEmployee = async function (input) {
          if (input) {
            scope.showLoading();
            let data = input;
            await hrSettings.searchMemberList(scope.page_active,scope.global_empLimitList,data)
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
              });
          } else {
            scope.isSearchEmp = false;
            scope.removeSearchEmp();
          }
        };
        scope.getSession = async function () {
          await hrSettings.getSession()
            .then(async function (response) {
              // console.log( response );
              scope.selected_customer_id = response.data.customer_buy_start_id;
              scope.options.accessibility = response.data.accessibility;
              if (scope.isSearchEmp) {
                await scope.searchEmployee(scope.inputSearch);
              } else {
                await scope.getEmployeeList(scope.page_active);
              }
            });
        }
        scope.getTotalMembers = function () {
          hrSettings.getCountMembers()
            .then(function (response) {
              scope.companyEmployeeValues = response.data;
            });
        }
        scope.getProgress = function () {
          hrSettings.getEnrollmentProgress()
            .then(function (response) {
              scope.progress = response.data.data;
            });
        };
        scope._showAddFilterModal_ = function() {
          $("#add-filter-modal").modal('show');

          scope.global_statusData = {
            pending: false,
            logged_in: false,
            active: false,
            removed: false,
          }
        }
        scope._statusClear_ = function ( data ) {
          data.pending = false;
          data.logged_in = false;
          data.active = false;
          data.removed = false;
        }
        scope._empApplyFilter_ = function ( data ) {
          scope.showLoading();
          hrSettings.getFilterEmployees ( scope.page_active,scope.global_empLimitList,data.pending,data.logged_in,data.active,data.removed  )
          .then(function( response ) {
            // console.log(response);
            scope.employees = response.data;
            scope.hideLoading();
            $('#add-filter-modal').modal('hide');            
          });
        }
        scope._cancelModal_ = function () {
          $('#add-filter-modal').modal('hide');
        }
        scope.empDetailsLoadingState = function(){
          window.open(serverUrl.url + '/hr/get_company_employee_lists_credits?token=' + window.localStorage.getItem('token'));
        }
        scope.enrollMoreEmployees = function () {
          if ( scope.get_permissions_data.enroll_terminate_employee == 1 ) {
            localStorage.setItem('fromEmpOverview', true);
            $state.go( 'create-team-benefits-tiers' );
            $('body').css('overflow', 'auto');
          } else {
            $('#permission-modal').modal('show');
          }
          
        }
        scope.goToMemberInfo  = function(data, index){
          console.log(data);
          localStorage.setItem('selected_member_id', data.user_id);
          localStorage.setItem('selected_member_index', index);
          var empOverviewData = {
            isSearchEmp : scope.isSearchEmp ? scope.inputSearch : null,
            pageActive : scope.page_active,
            perPage: scope.page_ctr,
          }
          localStorage.setItem('empOverviewData', JSON.stringify(empOverviewData));
          $state.go('member.emp-details', { member_id : data.user_id });
        }
        scope.getSpendingAcctStatus = async function () {
          // hrSettings.getSpendingAccountStatus()
          await hrSettings.getPrePostStatus()
						.then(function (response) {
							console.log(response);
              scope.spending_account_status = response.data;
						});
        }
        scope._selectAllEmpCheckbox_  = async function(opt){
          scope.isAllEmpCheckboxSelected = opt;
          scope.selectedEmpArr  = [];
          if(opt == false){
            scope.isSelectOverallEmployees  = false;
          }
          await angular.forEach(scope.employees.data, async function(value, key){
            value.selected = opt;
            if(opt){
              scope.selectedEmpArr.push(value.user_id);
            }
          });
          console.log(scope.selectedEmpArr);
        }
        scope._selectOneEmpCheckbox_  = function(user_id, opt){
          if(opt){
            scope.selectedEmpArr.push(user_id);
          }else{
            var index = $.inArray(user_id, scope.selectedEmpArr);
            scope.selectedEmpArr.splice(index, 1);
          }
          console.log(scope.selectedEmpArr);
        }
        scope.selectOverallEmployees  = function(opt){
          if(opt){
            scope.isSelectOverallEmployees  = true;
          }else{
            scope.isSelectOverallEmployees  = false;
            scope.isAllEmpCheckboxSelected = false;
            scope._selectAllEmpCheckbox_(false);
          }
        }
        scope.exportSelectedMember  = function(){
          // scope.isClickExportAll = false;
          $timeout(function(){
            $("#export-member-btn-modal").trigger('click');
          },200);
        }

        scope._filterBackBtn_ = function(opt){
          if(opt == 'filterList'){
            scope.isTotalMembersShow = true;
            scope.isFiltersShow = false;
          }
          if(opt == 'status' || opt == 'location' || opt == 'department'){
            if(scope.isApplyFilter == false){
              scope._removeFilterType_(opt);
            }
            scope.isStatusFiltersShow = false;
            scope.isLocationFiltersShow = false;
            scope.isDepartmentFiltersShow = false;
            scope.isFiltersShow = true;
          }
        }
        scope._showAddFilters_  = function(){
          scope.isTotalMembersShow = false;
          scope.isFiltersShow = true;
        }
        scope._showTypeItemsFilters_  = function(opt){
          scope.isFiltersShow = false;
          if(opt == 'status'){
            scope.isStatusFiltersShow = true;
          }
          if(opt == 'location'){
            scope.isLocationFiltersShow = true;
          }
          if(opt == 'department'){
            scope.isDepartmentFiltersShow = true;
          }
        }
        scope._applyFilterTypes_ = function(){
          scope.isTotalMembersShow = true;
          scope.isFiltersShow = false;
          scope.isStatusFiltersShow = false;
          scope.isLocationFiltersShow = false;
          scope.isDepartmentFiltersShow = false;

          scope.isApplyFilter = true;
          scope.getEmployeeList();
        }
        scope._removeFilterType_  = function(opt){
          if(opt == 'status'){
            scope.empFiltersObj.status = [
              {
                name: 'Pending',
                active: false,
              },
              {
                name: 'Activated',
                active: false,
              },
              {
                name: 'Active',
                active: false,
              },
            ];
          }
          if(opt == 'location'){
            scope.empFiltersObj.location = [];
            scope.empFiltersObj.selectedLocations = [];
            scope.locationList.map((res) => {
              res.selected = false;
            });
          }
          if(opt == 'department'){
            scope.empFiltersObj.department = [];
            scope.empFiltersObj.selectedDepartments = [];
            scope.departmentList.map((res) => {
              res.selected = false;
            });
          }
        }
        scope._selectLocationFilterData_  = function(value, opt){
          if(opt){
            scope.empFiltersObj.location.push(value.LocationID);
            scope.empFiltersObj.selectedLocations.push(value);
          }else{
            var index = $.inArray(value.LocationID, scope.empFiltersObj.location);
            scope.empFiltersObj.location.splice(index, 1);
            scope.empFiltersObj.selectedLocations.splice(index, 1);
            var indexLoc = $.inArray(value, scope.locationList);
            scope.locationList[indexLoc].selected = false;
          }
        }
        scope._selectDepartmentFilterData_  = function(value, opt){
          if(opt){
            scope.empFiltersObj.department.push(value.id);
            scope.empFiltersObj.selectedDepartments.push(value);
          }else{
            var index = $.inArray(value.id, scope.empFiltersObj.department);
            scope.empFiltersObj.department.splice(index, 1);
            scope.empFiltersObj.selectedDepartments.splice(index, 1);
            var indexDep = $.inArray(value, scope.departmentList);
            scope.departmentList[indexDep].selected = false;
          }
        }
        scope.selectTransferBtn = function(data){
          console.log(data);
          scope.selected_employee = data;
          scope.locationList = scope.locationList;
          scope.departmentList = scope.departmentList;

          $timeout(function(){
            $("#transfer-employee-btn").trigger('click');
          },200);
        }
        scope._goToRemoveEmployee_  = function(data){
          console.log(data.user_id);
          localStorage.setItem('selected_member_id', data.user_id);
          $state.go('member-remove', { member_id : data.user_id });
        }
        scope._getLocationListing_  = async function(){
          await hrSettings.fetchLocationData()
            .then(function(response){
              console.log(response);
              scope.locationList  = response.data;
            });
        }
        scope._getDepartmentListing_  = async function(){
          await hrSettings.fetchDepartmentData()
            .then(function(response){
              console.log(response);
              scope.departmentList  = response.data;
            });
        }

        scope.getPermissionsData = async function () {
          await hrSettings.getPermissions()
            .then( function (response) {
              console.log(response);
              scope.get_permissions_data = response.data.data;
          });
        }
        scope.permissionSelector = false;
        scope.activeAdminBtn = false;
        scope.showLocationSelector = false;
        scope.showDepartmentSelector = false;
        scope.changeBtnToActive = function () {
          scope.activeAdminBtn = true;
          console.log( scope.activeAdminBtn );
        }
        scope.adminPermission = function () {
          console.log('test');
          scope.permissionSelector = scope.permissionSelector == true ? false : true;
          // console.log(scope.locations_data);
        }

        scope.permissionSelectorData = async function ( type ) {
          scope.permissionSelector = false;

          if ( type == 'locations' ) {
            scope.permission_data = 'Locations';
            scope.showLocationSelector = true;

            scope.showDepartmentSelector = false;

            // this is for edit
            // scope.locations_data.map(function(value,key){
            //   if( _.findIndex(scope.edit_administrator_data.locations, {'LocationID' : value.LocationID}) > -1 ){
            //     value.status = true;
            //   }
            // });
            scope.selected_location_data = [];
            scope.locations_data.map((res) => {
              console.log(res);
              res.status = false;
            });

          }
          if ( type == 'departments' ) {
            scope.permission_data = 'Departments';
            scope.showDepartmentSelector = true;
            
            scope.showLocationSelector = false;

            scope.selected_department_data = [];
            scope.departments_data.map((res) => {
              console.log(res);
              res.status = false;
            });
            
          }
          console.log(type);
        }
        scope.chooseSelector = function ( type ) {
          if ( type == 'locations' ) {
            scope.chooseSelectorLocation = scope.chooseSelectorLocation == true ? false : true;
          }
          if ( type == 'departments' ) {
            scope.chooseSelectorDepartment = scope.chooseSelectorDepartment == true ? false : true;
          }
        }
        scope.selectedPermission = function ( type, data, opt ) {
          if ( type == 'locations' ) {
            // scope.showLocationSelector = false;
            scope.chooseSelectorLocation = false;
            if ( opt ) {
              scope.selected_location_data.push(data);
              console.log(scope.selected_location_data);
            } else {
              console.log('close pud siyaaa sulod');
              let index = $.inArray(data, scope.selected_location_data);
              scope.selected_location_data.splice(index, 1);
              data.status = false;             
            }
          }
          if ( type == 'departments' ) {
            // scope.showDepartmentSelector = false;
            scope.chooseSelectorDepartment = false;
            if ( opt ) {
              scope.selected_department_data.push(data);
            } else {
              console.log('close pud siyaaa sulod');
              let index = $.inArray(data, scope.selected_department_data);
              scope.selected_department_data.splice(index, 1);
              data.status = false;             
            }
          }
        }
        scope.resetData = function () {
          scope.permission_data = 'All Employees & Dependents';
          scope.showDepartmentSelector = false;
          scope.showLocationSelector =  false;
          scope.permissionSelector = false;
        }
        scope.getLocationData = async function () {
          scope.showLoading();
          await hrSettings.fetchLocationData()
          .then( function (response) {
            console.log(response);
            scope.locations_data = response.data;
            scope.hideLoading();
          });
        }

        scope.getDepartmentData = async function () {
          scope.showLoading();
          await hrSettings.fetchDepartmentData()
          .then( function (response) {
            console.log(response);
            scope.departments_data = response.data;
            scope.hideLoading();
          });
        }
        scope.assignAdmin = function () {
          scope.isAddAdministratorConfirm = false;
          scope.isAddAdministratorSuccess = false;
          scope.chooseSelectorLocation = false;
          scope.chooseSelectorDepartment = false;
          scope.showLocationSelector = false;
          scope.showDepartmentSelector = false;
          scope.activeAdminBtn = false;
          scope.permission_data = 'All Employees & Dependents'
          scope.selected_location_data = [];
          scope.selected_department_data = [];
        }


        $('.modal').on('hidden.bs.modal', function (e) {
          scope.isSelectOverallEmployees = false;
        })


        // CUSTOM REUSABLE FUNCTIONS
        scope.startIndex = function () {
          if (scope.page_active > ((scope.pagesToDisplay / 2) + 1)) {
            if ((scope.page_active + Math.floor(scope.pagesToDisplay / 2)) > scope.employees.last_page) {
              return scope.employees.last_page - scope.pagesToDisplay + 1;
            }
            return scope.page_active - Math.floor(scope.pagesToDisplay / 2);
          }
          return 0;
        }
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

        scope.testClick = function () {
          console.log('test click');
        }
        

        scope.onLoad  = async function(){
          await scope.getSession();
          // await scope.getSpendingAcctStatus();
          await scope.getTotalMembers();
          await scope._getLocationListing_();
          await scope._getDepartmentListing_();
          // await scope.getProgress();

          await scope.getPermissionsData();
          // await scope.getLocationData();
          // await scope.getDepartmentData()

          localStorage.setItem('selected_member_id', null);
          localStorage.setItem('selected_member_index', null);
          localStorage.setItem('empOverviewData', null);
        }
        scope.onLoad();
      }
    }
  }
]);