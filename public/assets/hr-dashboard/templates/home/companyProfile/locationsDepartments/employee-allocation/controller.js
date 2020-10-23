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
            this.employeeAllocationApi = employeeAllocationApi;
            this.get_permission_data = {};
            
        }
        $onInit() {
            this.permission();
        }
        get() {
            this.api.getEmployees().then(response => {
                this.employees = response;
                presentModal(this.modal.id);
            });
        }
        open() {
            // this.get();
            // this.permission();
            // console.log('test test');
            if ( this.get_permission_data.add_location_departments == 1 ) {
                console.log('test allocate employees');
            } else {
                this.presentModal('permission-modal', true);
            }
        }
        dismiss() {
            presentModal(this.modal.id, 'hide');
        }
        presentModal(id, show = true) {
            $(`#${id}`).modal(show ? "show" : "hide");
        }
        permission() {
            this.employeeAllocationApi.permission().then(response => {
                console.log(response)
                this.get_permission_data = response.data;
                console.log(this.get_permission_data);
            });
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