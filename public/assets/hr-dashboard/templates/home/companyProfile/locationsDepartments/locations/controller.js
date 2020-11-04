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
            this.get_permission_data = {};
            this.isShowRemove = true;
        }
        $onInit() {
            this.get();
            this.permission();
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
            if ( this.get_permission_data.add_location_departments == 1 ) {
                this.reset();
                this.state.form.country = 'Singapore';
                this.presentModal('create-location-modal', true);
            } else {
                this.presentModal('permission-modal', true);
            }
        }
        store() {
            const location = this.getFormData();

            $(".circle-loader").fadeIn();
            const request = this.locationAPI.store(location);

            request.then((response) => {
                if(response.data.status){
                    this.presentModal('create-location-modal', false);
                    swal('Success', response.data.message, 'success');
                    this.get();
                    this.reset();
                }else{
                    swal('Error', response.data.message, 'error');
                }
                $(".circle-loader").fadeOut();
            });
        }
        edit(location,index) {
            console.log(location);
            console.log(index);
            if ( this.get_permission_data.add_location_departments == 1 ) { 
                this.state.form = { ...location };
                presentModal('edit-location-modal');
                if ( index == 0 ) {
                    this.isShowRemove = false;
                    console.log(this.isShowRemove, 'isShowRemove');
                } else {
                    this.isShowRemove = true;
                }
            } else {
                this.presentModal('permission-modal', true);
            }
        }
        update() {
            const location = {
                LocationID: this.state.form.LocationID,
                location_id: this.state.form.LocationID,
                ...this.getFormData()
            }

            $(".circle-loader").fadeIn();
            const request = this.locationAPI.update(location);

            request.then((response) => {
                console.log(response);
                if(response.data.status){
                    this.presentModal('edit-location-modal', false);
                    swal('Success', response.data.message, 'success');
                    this.get();
                }else{
                    swal('Error', response.data.message, 'error');
                }
                $(".circle-loader").fadeOut();
            });
        }
        getFormData() {

            return {
                location: this.state.form.location,
                business_address: this.state.form.business_address,
                country: this.state.form.country,
                postal_code: this.state.form.postal_code,
                unit_number: this.state.form.unit_number,
                building_name: this.state.form.building_name
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

            request.then((response) => {
                if(response.data.status){
                    // this.presentModal('create-location-modal', false);
                    this.presentModal('success-department-confirm-modal', true);
                    // swal('Success', response.data.message, 'success');
                    this.get();
                    this.reset();
                }else{
                    swal('Error', response.data.message, 'error');
                }
                $(".circle-loader").fadeOut();
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
        permission() {
            this.locationAPI.permission().then(response => {
                console.log(response)
                this.get_permission_data = response.data;
                console.log(this.get_permission_data);
            });
        }
    }

    angular.module('app')
        .component('locations', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments/locations/index.html',
            controller: LocationsController
        });

}(angular));