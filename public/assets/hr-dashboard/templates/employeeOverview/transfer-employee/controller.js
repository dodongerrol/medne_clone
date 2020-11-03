(function (angular) {
'use strict';
	class TransferEmployeeController {
		constructor(hrSettings) {
			this.hrSettings = hrSettings;
			this.states = {
				company_selector: [ 
					{
						selected: true,
						transfer_option : 1,
						item: 'Within this Company', 
					},
					{
						selected: false,
						transfer_option : 0,
						item: 'Another linked company', 
					},
				],
				location_department_selector: [
					{
						transfer_option: 1,
						name: 'Location'
					},
					{
						transfer_option: 0,
						name: 'Department'
					},
				],
				location_list : [],
				department_list : [],
				company_list: [],
				form: {}
			}
		}
		$onInit() {
				
		}
		attemptCheck($event) {
			console.log(this.states.company_selector);
		}
		setField(field, value) {
				this.states.form[field] = value;
		}
		open() {
				presentModal('transfer-employee-modal');
				// forms data
				this.states.form.user_id = this.employee.user_id;
				this.states.form.fullname = `${this.employee.fname} ${this.employee.lname}`;
				this.states.form.location = this.employee.location;
				this.states.form.selected_location_department = this.states.location_department_selector[0];
				this.states.form.transfer_option = this.states.company_selector[0].transfer_option;

				this.states.location_list = this.locationsdata;
				this.states.department_list = this.departmentsdata;

		}
		dismiss() {
			this.states.form = {};
			presentModal('transfer-employee-modal', 'hide');
		}
		async transferEmployee (item) {
			let data;
			if (item.transfer_option == 1) { // Within this Company
				if (item.selected_location_department.transfer_option == 1) {
					data = {
						"employee_id": item.user_id,
						"transfer_option": parseInt(item.transfer_option),
						"asssign": item.selected_location_department.transfer_option,
						"location_id": item.selected_location_department_data.LocationID,
						// "department_id": 3,
						// "except_current": "enable"
					}
				} else if (item.selected_location_department.transfer_option == 0) {
					data = {
						"employee_id": item.user_id,
						"transfer_option": parseInt(item.transfer_option),
						"asssign": item.selected_location_department.transfer_option,
						// "location_id": item.selected_location_department_data.LocationID,
						"department_id": item.selected_location_department_data.LocationID,
						// "except_current": "enable"
					}
				}
			} else if (item.transfer_option == 0) { // Another Linked Company
				console.log('Another Linked Company'); // waiting updated api for transfering another linked company
			}

			this.showloading();
			await this.hrSettings.updateUnlinkAccount(data)
			.then(function(response){
				
			});
			await this.hideloading();
			await this.dismiss();
		}
	}

	angular.module('app')
	.component('transferemployee', {
		templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeOverview/transfer-employee/index.html',
		bindings: {
			employee: '<',
			locationsdata: '<',
			departmentsdata: '<',
			showloading: '&',
			hideloading: '&'
		},
		controller: TransferEmployeeController
	});
}(angular));