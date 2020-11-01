app.directive('employeeInfoContainerDirective', [
	"$http",
	"serverUrl",
  '$timeout',
  '$state',
  '$stateParams',
  'employeeFactory',
  '$http',
  'dependentsSettings',
  'hrSettings',
  '$rootScope',
	function directive( $http, serverUrl, $timeout, $state, $stateParams, employeeFactory, $http, dependentsSettings, hrSettings, $rootScope) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
        console.log('running employeeInfoContainerDirective!');
        scope.selected_member_id = localStorage.getItem('selected_member_id');
        scope.selected_member_index = Number(localStorage.getItem('selected_member_index'));
        scope.empOverviewData = JSON.parse( localStorage.getItem('empOverviewData') );
        scope.selectedEmployee = {};
        scope.refund_status = null;
        scope.isTierDetailsShow = false;
        scope.isMedicalUsageShow = false;
        scope.isWellnessUsageShow = false;



        scope.getEmployeeDetails  = async function(isRefresh){
          scope.selectedEmployee = await employeeFactory.getEmployeeDetails();
          if( scope.selectedEmployee == null || scope.selectedEmployee.user_id != Number(scope.selected_member_id) || isRefresh ){
            await scope.fetchEmployeeDetails();
          }else{
            scope.setEmployeeValues();
          }
        }
        scope.fetchEmployeeDetails  = async function(){
          scope.showLoading();
          await $http.get(serverUrl.url + "/hr/employee/" + scope.selected_member_id)
            .then(function(response){
              console.log(response);
              scope.selectedEmployee = response.data.data;
              employeeFactory.setEmployeeDetails(scope.selectedEmployee);
              scope.setEmployeeValues();
            });
        }
        scope.setEmployeeValues = function(){
          scope.selectedEmployee.start_date_dmy = moment(scope.selectedEmployee.start_date,['YYYY-MM-DD', 'DD/MM/YYYY']).format('DD/MM/YYYY');
          scope.selectedEmployee.end_date_dmy = moment(scope.selectedEmployee.expiry_date).format('DD/MM/YYYY');
          scope.selectedEmployee.fname = scope.selectedEmployee.name.substring(0, scope.selectedEmployee.name.lastIndexOf(" "));
          scope.selectedEmployee.lname = scope.selectedEmployee.name.substring(scope.selectedEmployee.name.lastIndexOf(" ") + 1);
          scope.selectedEmployee.start_date = moment(scope.selectedEmployee.start_date).format("DD/MM/YYYY");
          scope.selectedEmployee.start_date_format = moment(scope.selectedEmployee.start_date, 'DD/MM/YYYY').format("DD MMMM YYYY");
          scope.selectedEmployee.end_date_format = moment(scope.selectedEmployee.expiry_date).format("DD MMMM YYYY");
          scope.selectedEmployee.expiry_date = moment(scope.selectedEmployee.expiry_date).format("MM/DD/YYYY");
          scope.selectedEmployee.dob = moment(scope.selectedEmployee.dob, ['YYYY-MM-DD', 'DD/MM/YYYY']).format('DD/MM/YYYY');
        }

        scope.fetchRefundStatus = async function (id) {
          await hrSettings.getRefundStatus(id)
            .then(function (response) {
              scope.refund_status = response.data.refund_status;
            });
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
        scope.getEmpDependents = async function (id) {
          await hrSettings.getDependents(id)
            .then(function (response) {
              scope.selected_emp_dependents = response.data.dependents;
            });
        }
        scope.getEmpPlans = async function (id) {
          await dependentsSettings.fetchEmpPlans(id)
            .then(function (response) {
              scope.selectedEmployee.plan_list = response.data;
            });
        }

        scope.prevSelectedEmployee = function () {
          if(scope.selected_member_index != 0){
            var id = scope.employees.data[scope.selected_member_index-1].user_id;
            localStorage.setItem('selected_member_id', id);
            scope.selected_member_index -= 1;
            localStorage.setItem('selected_member_index', scope.selected_member_index);
            $state.go('member.emp-details', { member_id : id });
          }
        };

        scope.nextSelectedEmployee = function () {
          if(scope.selected_member_index != scope.employees.data.length - 1){
            var id = scope.employees.data[scope.selected_member_index+1].user_id;
            localStorage.setItem('selected_member_id', id);
            scope.selected_member_index += 1;
            localStorage.setItem('selected_member_index', scope.selected_member_index);
            $state.go('member.emp-details', { member_id : id });
          }
        };

        scope.getEmployeeList = async function (page,per_page) {
          await hrSettings.getEmployees(page,per_page)
            .then(function (response) {
              console.log(response);
              scope.employees = response.data;
            });
        };

        scope.searchEmployee = async function (input) {
          await hrSettings.searchMemberList(1,5,input)
            .then(function (response) {
              console.log(response);
              scope.employees = response.data;
            });
        };

        scope.backToEmpOverview = function(){
          localStorage.setItem('selected_member_id', null);
          localStorage.setItem('selected_member_index', null);
          localStorage.setItem('empOverviewData', null);
          $state.go("employee-overview");
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

        scope.goToRemoveEmployee  = function(){
          if ( scope.get_permissions_data.enroll_terminate_employee == 1 ) {
            $state.go('member-remove.remove-emp-inputs', { member_id : scope.selected_member_id });
          } else {
            $('#permission-modal').modal('show');
          }
          
        }
        scope.goToHealthAccountSummary  = function(){
          $state.go('member.health-spending-account-summary');
        }
        scope.getPermissionsData = async function () {
          await hrSettings.getPermissions()
            .then( function (response) {
              console.log(response);
              scope.get_permissions_data = response.data.data;
          });
        }

				
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
          await scope.getEmpPlans(scope.selected_member_id);
          await scope.getEmpDependents(scope.selected_member_id);
          await scope.getPermissionsData();
          // await scope.entitlementCalc(scope.selected_member_id);

          if (scope.empOverviewData.isSearchEmp != null && scope.empOverviewData.isSearchEmp != 'null') {
            await scope.searchEmployee(scope.empOverviewData.isSearchEmp);
          } else {
            await scope.getEmployeeList(scope.empOverviewData.pageActive, scope.empOverviewData.perPage);
          }
          scope.hideLoading();
        }
        scope.onLoad();




        scope.$on('updateEmployeeDetails', async function(ev, params){
          console.log(params);
          await scope.getEmployeeDetails(true);
        });
			}
		}
	}
]);