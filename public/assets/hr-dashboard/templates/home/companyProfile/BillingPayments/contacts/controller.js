(function (angular) {
    'use strict';
    class ContactsController {
        constructor() {
            this.views = window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/contacts';
        }
        $onInit() {
        }
    }

    angular.module('app')
        .component('contacts', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/contacts/index.html',
            controller: ContactsController
        });
}(angular));