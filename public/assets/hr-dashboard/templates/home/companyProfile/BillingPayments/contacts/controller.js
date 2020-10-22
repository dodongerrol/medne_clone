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
                    work_email: null
                },
                form: {
                    customer_billing_contact_id: null,
                    first_name: '',
                    phone: '',
                    work_email: null
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
                $(".circle-loader").fadeOut();
                presentModal('contact-form', false);
                this.hydrate();
            });
        }
    }

    angular.module('app')
        .component('contacts', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/contacts/index.html',
            controller: BillingContactsController
        });
}(angular));