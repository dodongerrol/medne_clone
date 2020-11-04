(function (angular) {
    'use strict';
    class BillingContactsController {
        constructor(billingContactAPI) {
            this.views = window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/contacts';
            this.billingContactAPI = billingContactAPI;
            this.states = {
                readonly: {
                    customer_billing_contact_id: null,
                    first_name: '',
                    phone: '',
                    billing_email: null
                },
                form: {
                    customer_billing_contact_id: null,
                    first_name: '',
                    phone: '',
                    billing_email: null
                }
            }
        }
        $onInit() {
            this.hydrate();
        }
        hydrate() {
            this.billingContactAPI.get()
                .then((response) => {
                    this.states.readonly = response;
                    this.states.form = response;
                } );
        }
        open() {
            presentModal('contact-form')
        }
        dismiss() {
            presentModal('contact-form', 'hide')
        }
        submit() {
            $(".circle-loader").fadeIn();
            const request = this.billingContactAPI.update(this.states.form);

            request.then((response) => {
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
    }

    angular.module('app')
        .component('contacts', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/contacts/index.html',
            controller: BillingContactsController
        });
}(angular));