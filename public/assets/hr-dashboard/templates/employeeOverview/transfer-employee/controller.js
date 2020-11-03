(function (angular) {
'use strict';
	class TransferEmployeeController {
		constructor(hrSettings) {
			this.hrSettings = hrSettings;
			this.states = {
				company_selector: [ 
					{
						selected: true,
						transfer_option : 0,
						item: 'Within this Company', 
					},
					{
						selected: false,
						transfer_option : 1,
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
				this.states.form.transfer_option = this.states.company_selector[0].transfer_option;
				this.states.location_list = this.locationsdata;
				this.states.department_list = this.departmentsdata;

				

		}
		dismiss() {
			this.states.form = {};
			presentModal('transfer-employee-modal', 'hide');
		}
		transferEmployee (item) {
			let data;
			if (item.transfer_option == 0) { // Within this Company
				if (item.selected_location_department.transfer_option == 1) {
					data = {
						"employee_id": item.user_id,
						"transfer_option": item.transfer_option,
						"asssign": item.selected_location_department.transfer_option,
						"location_id": item.selected_location_department_data.LocationID,
						// "department_id": 3,
						// "except_current": "enable"
					}
				} else {
					data = {
						"employee_id": item.user_id,
						"transfer_option": item.transfer_option,
						"asssign": item.selected_location_department.transfer_option,
						// "location_id": item.selected_location_department_data.LocationID,
						"department_id": item.selected_location_department_data.LocationID,
						// "except_current": "enable"
					}
				}
			} else if (item.transfer_option == 1) { // Another Linked Company
				console.log('Another Linked Company');
			}
			this.hrSettings.updateUnlinkAccount(data)
			.then(function(response){
				$ctrl.dismiss();
			});
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