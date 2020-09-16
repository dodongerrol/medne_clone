(function (angular) {
    'use strict';
    class InformationController {
        constructor(billingContactAPI) {
            this.views = window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/information';
            this.billingContactAPI = billingContactAPI;
            this.element = 'information-form';
        }
        $onInit() {
            this.billingContactAPI.get().then((response) => console.log(response))
        }
    }

    angular.module('app')
        .component('information', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/information/index.html',
            controller: InformationController
        });
}(angular));