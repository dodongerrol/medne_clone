scope.getEnrollTempEmployees = function () {
  scope.temp_employees = [];
  scope.hasError = false;
  // var hasMedicalBalance = false;
  // var hasWellnessBalance = false;
  scope.employee_data.hasMedicalBalance = localStorage.getItem('hasMedicalEntitlementBalance') == 'true' ? true : false;
  scope.employee_data.hasWellnessBalance = localStorage.getItem('hasWellnessEntitlementBalance') == 'true' ? true : false;
  var option = {
    minimumFractionDigits: 2, 
    maximumFractionDigits: 2
  }
  console.log(scope.employee_data.hasWellnessBalance, scope.employee_data.hasWellnessBalance);
  
  $timeout(function () {
    scope.employee_data.hasMedicalBalance = localStorage.getItem('hasMedicalEntitlementBalance') == 'true' ? true : false;
    scope.employee_data.hasWellnessBalance = localStorage.getItem('hasWellnessEntitlementBalance') == 'true' ? true : false;
    console.log(scope.employee_data.hasWellnessBalance, scope.employee_data.hasWellnessBalance);
    $("#enrollee-details-tbl tbody").html('');
    $("#enrollee-details-tbl thead tr").html($compile(`<th><input type="checkbox" ng-click="empCheckBoxAll()"></th><th>Full Name</th><th>Date of Birth</th><th>Work Email</th><th>Country Code</th><th>Mobile</th ><th ng-if="(spendingPlan_status.account_type != 'enterprise_plan' && spendingPlan_status.account_type == 'lite_plan' && spendingPlan_status.medical_enabled && spendingPlan_status.paid_status) || (spendingPlan_status.account_type != 'enterprise_plan' && spendingPlan_status.medical_method == 'post_paid' && spendingPlan_status.medical_enabled)">Medical Allocation</th><th ng-if="(spendingPlan_status.account_type == 'lite_plan' && spendingPlan_status.wellness_enabled && spendingPlan_status.paid_status) || (spendingPlan_status.wellness_method == 'post_paid' && spendingPlan_status.wellness_enabled)">Wellness Allocation</th>`)(scope));
    // <th ng-if="employee_data.hasMedicalBalance">Medical Entitlement Balance</th>	<th ng-if="employee_data.hasWellnessBalance">Wellness Entitlement Balance</th>
    dependentsSettings.getTempEmployees()
      .then(function (response) {
        // console.log( response );
        scope.temp_employees = response.data.data;
        angular.forEach(scope.temp_employees, function (ctr_value, ctr_key) {
          if (ctr_value.dependents.length > scope.table_dependents_ctr) {
            scope.table_dependents_ctr = ctr_value.dependents.length;
          }
          if ((scope.temp_employees.length - 1) == ctr_key) {
            angular.forEach(scope.temp_employees, function (value, key) {
              console.log(value);
              if (value.error_logs.error == true) {
                scope.hasError = true;
              }
              value.success = false;
              value.fail = false;
              scope.isTrError = (value.error_logs.error == true) ? 'has-error' : '';
              var html_tr = '<tr class="dependent-hover-container ' + scope.isTrError + ' "><td><input type="checkbox" ng-model="temp_employees[' + key + '].checkboxSelected" ng-click="empCheckBoxClicked(' + key + ')"></td><td><span class="icon"><i class="fa fa-check" style="display: none;"></i><i class="fa fa-times" style="display: none;"></i><i class="fa fa-circle-o-notch fa-spin" style="display: none;"></i></span><span class="fname">' + value.employee.fullname + '</span><button class="dependent-hover-btn" ng-click="openEditDetailsModal(' + key + ')">Edit</button></td><td>' + value.employee.dob + '</td><td>' + value.employee.email + '</td><td>+' + value.employee.mobile_area_code + '</td><td>' + value.employee.mobile + `</td><td ng-if="(spendingPlan_status.account_type != 'enterprise_plan' && spendingPlan_status.account_type == 'lite_plan' && spendingPlan_status.medical_enabled && spendingPlan_status.paid_status) || (spendingPlan_status.account_type != 'enterprise_plan' &&  spendingPlan_status.medical_method == 'post_paid' && spendingPlan_status.medical_enabled)">` + parseFloat(value.employee.credits).toLocaleString('en', option) + `</td><td ng-if="(spendingPlan_status.account_type == 'lite_plan' && spendingPlan_status.wellness_enabled && spendingPlan_status.paid_status) || (spendingPlan_status.wellness_method == 'post_paid' && spendingPlan_status.wellness_enabled)">` + parseFloat(value.employee.wellness_credits).toLocaleString('en', option) + '</td>';
              // <td ng-if="employee_data.hasMedicalBalance">' + value.employee.medical_balance_entitlement.toLocaleString('en', option) + '</td>
              // <td ng-if="employee_data.hasWellnessBalance">' + value.employee.wellness_balance_entitlement.toLocaleString('en', option) + '</td>
              var emp_ctr = 0;
              while (emp_ctr != value.dependents.length) {
                scope.isTrError = (value.dependents[emp_ctr].error_logs.error == true) ? 'has-error' : '';
                html_tr += '<td>' + value.dependents[emp_ctr].enrollee.fullname + '</td><td>' + value.dependents[emp_ctr].enrollee.dob + '</td><td>' + value.dependents[emp_ctr].enrollee.relationship + '</td>';
                emp_ctr++;
              }
              while (emp_ctr != scope.table_dependents_ctr) {
                html_tr += '<td></td><td></td><td></td>';
                emp_ctr++;
              }
              html_tr += '<td>' + value.employee.start_date + '</td></tr>';

              $("#enrollee-details-tbl tbody").append($compile(html_tr)(scope));

              if ((scope.temp_employees.length - 1) == key) {
                var while_ctr = 0;
                while (while_ctr != scope.table_dependents_ctr) {
                  while_ctr++;
                  $("#enrollee-details-tbl thead tr").append(
                    '<th>Dependent ' + while_ctr + '<br>Full Name</th>' +
                    '<th>Dependent ' + while_ctr + '<br>Date of Birth</th>' +
                    '<th>Dependent ' + while_ctr + '<br>Relationship</th>'
                  );
                }
                $("#enrollee-details-tbl thead tr").append('<th class="start-date-header">Start Date</th>');
                scope.hideLoading();
              }
            });
          }
        })
      });
  }, 200);
}