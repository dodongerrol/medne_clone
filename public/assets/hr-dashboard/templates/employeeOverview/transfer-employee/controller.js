(function (angular) {
    'use strict';
    class TransferEmployeeController {
        constructor() {
            this.states = {
                company_selector: [
                    'Within this Company',
                    'Another linked company'
                ],
                location_department_selector: [
                    'Location',
                    'Department'
                ],
                form: {}
            }
        }
        $onInit() {
            
        }
        attemptCheck($event) {
            console.log($event)
        }
        setField(field, value) {
            this.states.form[field] = value;
        }
        open() {
            presentModal('transfer-employee-modal');
            console.log(this.employee);
            this.states.form = this.employee;
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
            },
            controller: TransferEmployeeController
        });
}(angular));