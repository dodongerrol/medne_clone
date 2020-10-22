(function (angular) {
    'use strict';
    class BillingInformationController {
        constructor(billingInformationAPI) {
            this.views = window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/information';
            this.billingInformationAPI = billingInformationAPI;
            this.element = 'information-form';
        }
        $onInit() {
            this.hydrate();
        }
        hydrate() {
            this.billingInformationAPI.get()
                .then((response) => {
                    console.log(response);
                });
        }
        open() {
            presentModal('edit-information-modal');
        }
        dismiss() {
            presentModal('edit-information-modal', false);
        }
    }

    angular.module('app')
        .component('information', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/information/index.html',
            controller: BillingInformationController
        });
}(angular));