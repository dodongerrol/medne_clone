(function (angular) {
    'use strict';
    class ExportMembersController {
        constructor() {
            this.exportMembersAPI = null;
            this.state = {
                properties: [
                    'ID',
                    'Full Name',
                    'Status',
                    'Mobile Number',
                    'Email',
                    '(Medical) Benefits  Coverage',
                    'Benefits Start Date',
                    'Benefits End Date'
                ],
                selected_properties: []
            }
        }
        $onInit() {
        }
        open() {
            presentModal('export-members-modal')
        }
        dismiss() {
            presentModal('export-members-modal', 'hide')
        }
    }

    angular.module('app')
        .component('exportmembers', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeOverview/export-members/index.html',
            controller: ExportMembersController
        });
}(angular));