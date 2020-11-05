(function (angular) {
    'use strict';
    class BillingInformationController {
        constructor(billingInformationAPI) {
            this.views = window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/information';
            this.billingInformationAPI = billingInformationAPI;
            this.countries = countries();
            this.element = 'information-form';
            this.states =   {
                billingInformationData: {},
            };
        }
        $onInit() {
            this.hydrate();
            this.getPermission();
        }
        hydrate() {
            this.billingInformationAPI.get()
                .then((response) => {
                    console.log(response);
                    this.states.billingInformationData = response.data;
                    this.states.billingInformationData.country = this.states.billingInformationData.currency_type == 'sgd' ? 'Singapore' : 'Malaysia';
                });
        }
        open() {
            if ( this.get_permissions_data.manage_billing_and_payments == 1 ) {
                presentModal('edit-information-modal');
            } else {
                $('#permission-modal').modal('show');
            }
        }
        dismiss() {
            presentModal('edit-information-modal', 'hide');
        }
        submit() {
            let data = {
                billing_name: this.states.billingInformationData.billing_name,
                billing_address: this.states.billingInformationData.billing_address,
                company_address: this.states.billingInformationData.street_address,
                unit_number: this.states.billingInformationData.unit_number,
                building_name: this.states.billingInformationData.building_name,
                customer_billing_contact_id: this.states.billingInformationData.customer_billing_contact_id,
                currency_type: this.states.billingInformationData.country == 'Singapore' ? 'sgd' : 'myr',
                postal_code: this.states.billingInformationData.postal_code
            }
            $(".circle-loader").fadeIn();
            this.billingInformationAPI.update(data)
                .then((response) => {
                    console.log(response);
                    if(response.status){
                        
                        swal('Success', response.message, 'success');
                        this.hydrate();
                        this.dismiss();
                    }else{
                        swal('Error', response.message, 'error');
                    }
                    $(".circle-loader").fadeOut();
                });
        }
        setField(field, value) {
            this.states.billingInformationData[field] = value;
        }
        getPermission() {
            this.billingInformationAPI.getPermission()
                .then((response) => {
                    this.get_permissions_data = response.data;
                    console.log(this.get_permissions_data);
                });
        }
    }

    angular.module('app')
        .component('information', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/information/index.html',
            controller: BillingInformationController
        });
}(angular));