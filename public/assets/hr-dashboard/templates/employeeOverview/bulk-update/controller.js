(function (angular) {
    'use strict';
    class BulkUpdateController {
        constructor() {
            this.states = {
                selected_account_types: [],
                account_types: [
                    'Employee',
                    'Dependent'
                ],
                details: [
                    'Full Name',
                    'Mobile Number',
                    'Postal Code',
                    'Locations & Department',
                    'Work Email',
                    'Date of Birth',
                    'For Communication',
                    'Bank Name & Bank Account Number',
                    'Employee ID',
                    'Benefits Start Date',
                    'Relationship'
                ]
            }
        }
        $onInit() {

        }
        open() {
            presentModal('bulk-update-modal')
        }
        dismiss() {
            presentModal('bulk-update-modal', 'hide')
        }
    }

    angular.module('app')
        .component('bulkupdate', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeOverview/bulk-update/index.html',
            controller: BulkUpdateController
        });
}(angular));