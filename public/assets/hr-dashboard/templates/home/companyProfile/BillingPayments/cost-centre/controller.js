(function (angular) {
    'use strict';
    class CostCentreController {
        constructor() {
            this.views = window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/cost-centre';
            this.element = 'cost-centre-form'
        }
        $onInit() {
        }
        open() {
            presentModal('contact-form')
        }
        dismiss() {
            presentModal('contact-form', 'hide')
        }
        submit() {
        }
    }

    angular.module('app')
        .component('costcentre', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/cost-centre/index.html',
            controller: CostCentreController
        });
}(angular));