(function (angular) {
    'use strict';
    class LocationsController {
        constructor(locationAPI) {
            this.views = window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/locations',
                this.loading = false;
            this.locations = [];
            this.countries = countries();
            this.locationAPI = locationAPI;
            this.state = {
                form: {
                    location: null,
                    street_address: null,
                    unit: null,
                    building: null,
                    country: 'Singapore',
                    postal_code: null
                },
            }
        }
        $onInit() {
            this.get();
        }
        buttonState () {
            return this.locations.length > 0 ? 'h-10' : 'h-40';
        }
        get() {
            this.locationAPI.get().then(response => {
                console.log(response)
                this.locations = response;
            });
        }
        add() {
            this.reset();
            this.state.form.country = 'Singapore';
            this.presentModal('create-location-modal', true);
        }
        store() {
            const location = this.getFormData();

            $(".circle-loader").fadeIn();
            const request = this.locationAPI.store(location);

            request.then(() => {
                $(".circle-loader").fadeOut();
                this.presentModal('create-location-modal', false);
                swal('Success', 'Work Location successfully added!', 'success');
                this.get();
                this.reset();
            });
        }
        edit(location) {
            this.state.form = { ...location };
            presentModal('edit-location-modal');
        }
        update() {
            const location = {
                LocationID: this.state.form.LocationID,
                ...this.getFormData()
            }

            $(".circle-loader").fadeIn();
            const request = this.locationAPI.update(location);

            request.then((response) => {
                $(".circle-loader").fadeOut();
                this.presentModal('edit-location-modal', false);
                swal('Success', 'Changes saved!', 'success');
                this.get();
            });
        }
        getFormData() {
            const address = `${this.state.form.street_address},${this.state.form.unit},${this.state.form.building}`;

            return {
                location: this.state.form.location,
                business_address: address,
                country: this.state.form.country,
                postal_code: this.state.form.postal_code
            }
        }
        attemptDelete() {
            this.presentModal('edit-location-modal', false);
            this.presentModal('remove-location-confirm-modal', true);
        }
        delete() {
            this.presentModal('remove-location-confirm-modal', false);
            $(".circle-loader").fadeIn();
            const request = this.locationAPI.delete(this.state.form.LocationID);

            request.then(() => {
                $(".circle-loader").fadeOut();
                this.reset();
                this.presentModal('success-department-confirm-modal', true);
                this.get();
            });
        }
        reset() {
            this.state.form = _.mapValues(
                this.state.form,
                () => null
            );
        }
        presentModal(id, show = true) {
            $(`#${id}`).modal(show ? "show" : "hide");
        }
        setField(field, value) {
            this.state.form[field] = value;
        }
    }

    angular.module('app')
        .component('locations', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/locations/index.html',
            controller: LocationsController
        });

}(angular));