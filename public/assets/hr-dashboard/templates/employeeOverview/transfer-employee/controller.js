(function (angular) {
'use strict';
	class TransferEmployeeController {
		constructor() {
			this.states = {
				company_selector: [ 
					{
						selected: true,
						item: 'Within this Company', 
					},
					{
						selected: false,
						item: 'Another linked company', 
					},
				],
				location_department_selector: [
					{
						transfer_option: 1,
						name: 'Location'
					},
					{
						transfer_option: 2,
						name: 'Department'
					},
				],
				location_list : [],
				department_list : [],
				form: {}
			}
		}
		$onInit() {
				
		}
		attemptCheck($event) {
		}
		setField(field, value) {
				this.states.form[field] = value;
		}
		open() {
				presentModal('transfer-employee-modal');
				this.states.form = this.employee;
				this.states.form.fullname = `${this.states.form.fname} ${this.states.form.lname}`;
				this.states.form.selected_location_department = this.states.location_department_selector[0];
				this.states.location_list = this.locationsdata;
				this.states.department_list = this.departmentsdata;

		}
		dismiss() {
				presentModal('transfer-employee-modal', 'hide')
		}
	}

	angular.module('app')
	.component('transferemployee', {
		templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeOverview/transfer-employee/index.html',
		bindings: {
			employee: '<',
			locationsdata: '<',
			departmentsdata: '<',
		},
		controller: TransferEmployeeController
	});
}(angular));