(function (angular) {
    'use strict';
    class AllocationController {
        constructor(employeeAllocationApi) {
            this.employees = [];
            this.api = employeeAllocationApi;
            this.modal = {
                id: 'employee-allocation-modal',
                form: 'employeeAllocationModal'
            }
        }
        $onInit() {
        }
        get() {
            this.api.getEmployees().then(response => {
                this.employees = response;
                presentModal(this.modal.id);
            });
        }
        open() {
            this.get();
        }
        dismiss() {
            presentModal(this.modal.id, 'hide');
        }
    }
    angular.module('app')
        .component('allocation', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/employee-allocation/index.html',
            bindings: {
                id: '<'
            },
            controller: AllocationController
        });
}(angular));