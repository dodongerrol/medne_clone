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
            console.log( type );
            this.globalData = type;
            this.sample = angular.copy(type);
            console.log(this.sample);
            // this.state = {
            //     id: this.globalData.id.currentValue,
            //     type: this.globalData.type.currentValue,
            // }
            // console.log(this.state);
            // if ( this.state.type.currentValue == 'location' ) {
            //     console.log('loc');
            // } else {
            //     console.log('dep');
            // }
        }
        get() {
            this.api.getEmployeesLocation().then(response => {
                this.location = response;
                // presentModal(this.modal.id);
                console.log(this.location);
            });
        }
        getDepartment() {
            this.api.getEmployeesDepartment().then(response => {
                this.department = response;
                // presentModal(this.modal.id);
                console.log(this.location);
            });
        }
        open() {
            console.log(this.sample);
            console.log(this.id);
            console.log(this.type);
            this.get_employee_names.map((res) => {
                res.selected = false;
                console.log(res.selected);
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
                console.log(response)
                this.get_permission_data = response.data;
                console.log(this.get_permission_data);
            });
        }
        getEnrolledEmployee() {
            this.api.enrolledEmployee().then(response => {
                console.log(response)
                this.get_employee_names = response.data;
                console.log(this.get_employee_names);
            });
        }
        selectProperty(prop, opt){
            // console.log(scope.selectedEmpArr);
            console.log(prop);
            prop.selected = opt;
            if(opt){
                this.selectedEnrolledEmpArr.push(prop);
                this.selectEmployeeId.push(prop.user_id);
                console.log(this.selectEmployeeId);
                console.log(this.selectedEnrolledEmpArr);
            }else{
                var index = $.inArray(prop, this.selectedEnrolledEmpArr);
                this.selectedEnrolledEmpArr.splice(index, 1);
                
                var index_id = $.inArray(prop.user_id, this.selectEmployeeId);
                this.selectEmployeeId.splice(index_id, 1);
                console.log( this.selectEmployeeId );
            }
        }
        // saveAllocation() {
            
            
        // }
        saveAllocation() {
            let data = {
                employee_ids: this.selectEmployeeId,
                location_id: this.id,
            }

            const request = this.api.saveAllocateLocation(data);
            
            $(".circle-loader").fadeIn();
            console.log(data);

            request.then((response) => {
                $(".circle-loader").fadeOut();
                presentModal(this.modal.id, 'hide');
                this.get(); 
            })    
        }
        
    }
    angular.module('app')
        .component('allocation', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/employee-allocation/index.html',
            bindings: {
                id: '<',
                type: '@'
            },
            controller: AllocationController
        });
}(angular));