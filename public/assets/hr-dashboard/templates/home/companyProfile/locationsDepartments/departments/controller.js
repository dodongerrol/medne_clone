(function (angular) {
    'use strict';
    class DepartmentsController {
        constructor(departmentAPI) {
            this.views = window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/departments',
                this.loading = false;
            this.departments = [];
            this.countries = countries();
            this.formFields = {
                name: '',
                employees: 0
            };
            this.departmentAPI = departmentAPI;
        }
        $onInit() {
            this.get();
        }
        get() {
            this.loading = true;
            this.departmentAPI.getDepartments().then(response => {
                this.loading = false;
                this.departments = response;
            });
        }
        add() {
            this.presentModal('create-department-modal', true);
        }
        store() {
            //
        }
        edit(department) {
            this.presentModal('edit-department-modal', true);
            this.formFields = { ...department };
        }
        update() {
            //
        }
        delete(department) {
            //
        }
        presentModal(id, show = true) {
            $(`#${id}`).modal(show ? "show" : "hide");
        }
        setField(field, value) {
            this.formFields[field] = value;
        }
        resetFormFields() {
            this.formFields = _.mapValues(
                scope.formFields,
                () => null
            );
        }
    }

    angular.module('app')
        .component('departments', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/departments/index.html',
            controller: DepartmentsController
        });

}(angular));