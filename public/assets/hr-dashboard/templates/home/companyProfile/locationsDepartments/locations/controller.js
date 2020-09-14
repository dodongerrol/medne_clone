(function (angular) {
    'use strict';
    class LocationsController {
        constructor(workLocationService) {
            this.views = window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/locations',
                this.loading = false;
            this.locations = [];
            this.countries = countries();
            this.formFields = {
                location_name: null,
                business_address: null,
                unit: null,
                building: null,
                country: 'Singapore',
                postal_code: null,
                employees: 0
            };
            this.workLocationService = workLocationService;
        }
        $onInit() {
            this.get();
        }
        get() {
            this.loading = true;
            this.workLocationService.getLocations().then(response => {
                this.loading = false;
                this.locations = response;
            });
        }
        add() {
            this.presentModal('create-location-modal', true);
        }
        store() {
            //
        }
        edit(location) {
            presentModal('edit-location-modal');
            this.formFields = { ...location };
        }
        update() {
            //
        }
        delete(location) {
            //
        }
        presentModal(id, show = true) {
            $(`#${id}`).modal(show ? "show" : "hide");
        }
        setField(field, value) {
            this.formFields[field] = value;
        }
    }

    angular.module('app')
        .component('locations', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/locations/index.html',
            controller: LocationsController
        });

}(angular));