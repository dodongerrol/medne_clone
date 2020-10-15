(function (angular) {
    'use strict';
    class ExportMembersController {
        constructor() {
            this.exportMembersAPI = null;
            this.state = {
                properties: [
                    {
                        name: 'ID',
                        selected: false,
                    },
                    {
                        name: 'Full Name',
                        selected: false,
                    },
                    {
                        name: 'Status',
                        selected: false,
                    },
                    {
                        name: 'Mobile Number',
                        selected: false,
                    },
                    {
                        name: 'Email',
                        selected: false,
                    },
                    {
                        name: '(Medical) Benefits  Coverage',
                        selected: false,
                    },
                    {
                        name: 'Benefits Start Date',
                        selected: false,
                    },
                    {
                        name: 'Benefits End Date',
                        selected: false,
                    },
                    {
                        name: 'Family Coverage',
                        selected: false,
                    },
                    {
                        name: 'Medical Entitlement',
                        selected: false,
                    },
                    {
                        name: 'Medical Usage',
                        selected: false,
                    },
                    {
                        name: 'Medical Balance',
                        selected: false,
                    },
                    {
                        name: 'Wellness Entitlement',
                        selected: false,
                    },
                    {
                        name: 'Wellness Usage',
                        selected: false,
                    },
                    {
                        name: 'Wellness Balance',
                        selected: false,
                    },
                    {
                        name: 'Medical Entitlement Last Term',
                        selected: false,
                    },
                    {
                        name: 'Medical Usage Last Term',
                        selected: false,
                    },
                    {
                        name: 'Medical Balance Last Term',
                        selected: false,
                    },
                    {
                        name: 'Wellness Entitlement Last Term',
                        selected: false,
                    },
                    {
                        name: 'Wellness Usage Last Term',
                        selected: false,
                    },
                    {
                        name: 'Wellness Balance Last Term',
                        selected: false,
                    },
                    {
                        name: 'Locations',
                        selected: false,
                    },
                    {
                        name: 'Departments',
                        selected: false,
                    },
                ],
                selectedPropertiesArr: [],
                filterProps: '',
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
        removeselectedProps(prop){
            console.log(prop);
        }
        selectProperty(prop, opt){
            if(opt){
                this.state.selectedPropertiesArr.push(prop);
            }else{
                var index = $.inArray(prop, this.state.selectedPropertiesArr);
                this.state.selectedPropertiesArr.splice(index, 1);
            }
            console.log(this.state.selectedPropertiesArr);
        }
    }

    angular.module('app')
        .component('exportmembers', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeOverview/export-members/index.html',
            controller: ExportMembersController
        });
}(angular));