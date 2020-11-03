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
            this.get_employee_names = [];
            this.selectEmployeeId = [];
            this.state = {
                id: null,
                type: null,
            }
            this.selectedEnrolledEmpArr = [];
        }
        $onInit() {
            this.get();
            this.permission();
            this.getEnrolledEmployee();
        }
        $onChanges( type ) {
            // console.log( type );
        }
        get() {
            this.api.getEmployeesLocation().then(response => {
                this.location = response;
                console.log(this.location);
            });
        }
        getDepartment() {
            this.api.getEmployeesDepartment().then(response => {
                this.department = response;
            });
        }
        open() {
            this.get_employee_names.map((res) => {
                this.selectedEnrolledEmpArr = [];
                this.selectEmployeeId = [];
                res.selected = false;
            });
            if ( this.get_permission_data.add_location_departments == 1 ) { 
                presentModal(this.modal.id);
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
                // console.log(response)
                this.get_permission_data = response.data;
            });
        }
        getEnrolledEmployee() {
            this.api.enrolledEmployee().then(response => {
                // console.log(response)
                this.get_employee_names = response.data;
            });
        }
        selectProperty(prop, opt){
            prop.selected = opt;
            if(opt){
                this.selectedEnrolledEmpArr.push(prop);
                this.selectEmployeeId.push(prop.user_id);
            }else{
                var index = $.inArray(prop, this.selectedEnrolledEmpArr);
                this.selectedEnrolledEmpArr.splice(index, 1);
                
                var index_id = $.inArray(prop.user_id, this.selectEmployeeId);
                this.selectEmployeeId.splice(index_id, 1);
            }
        }
        saveAllocation() {
            // let data = {
            //     employee_ids: this.selectEmployeeId,
            //     location_id: this.id,
            // }

            const request = this.api.saveAllocateLocation({ employee_ids: this.selectEmployeeId,location_id: this.id });
            console.log(request);
            $(".circle-loader").fadeIn();

            request.then((response) => {
                $(".circle-loader").fadeOut();
                presentModal(this.modal.id, 'hide');
                this.onSave();
                this.get(); 
                return swal('Success!', response.data.message, 'success');
            })    
        }
        
    }
    angular.module('app')
        .component('allocation', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/employee-allocation/index.html',
            bindings: {
                id: '<',
                type: '@',
                onSave: '&'
            },
            controller: AllocationController
        });
}(angular));