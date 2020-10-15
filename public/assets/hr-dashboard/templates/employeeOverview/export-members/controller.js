(function (angular) {
    'use strict';
    class ExportMembersController {
        constructor() {
            this.exportMembersAPI = null;
            this.state = {
                properties: [
                    {
                        name: 'ID',
                        selected: true,
                    },
                    {
                        name: 'Full Name',
                        selected: true,
                    },
                    {
                        name: 'Status',
                        selected: true,
                    },
                    {
                        name: 'Mobile Number',
                        selected: true,
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
                        selected: true,
                    },
                    {
                        name: 'Benefits End Date',
                        selected: false,
                    },
                    {
                        name: 'Family Coverage',
                        selected: true,
                    },
                    {
                        name: 'Medical Entitlement',
                        selected: true,
                    },
                    {
                        name: 'Medical Usage',
                        selected: true,
                    },
                    {
                        name: 'Medical Balance',
                        selected: true,
                    },
                    {
                        name: 'Wellness Entitlement',
                        selected: true,
                    },
                    {
                        name: 'Wellness Usage',
                        selected: true,
                    },
                    {
                        name: 'Wellness Balance',
                        selected: true,
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
                        selected: true,
                    },
                    {
                        name: 'Departments',
                        selected: true,
                    },
                ],
                selectedPropertiesArr: [],
                filterProps: '',
            }
        }
        $onInit() {
        }
        open() {
            this.state.selectedPropertiesArr = [];
            angular.forEach(this.state.properties, (value, key) => {
                if(value.selected){
                    this.state.selectedPropertiesArr.push(value);
                }
            });
            presentModal('export-members-modal');
            console.log(this.selectedEmployeeData);
        }
        dismiss() {
            presentModal('export-members-modal', 'hide')
        }
        selectProperty(prop, opt){
            console.log(scope.selectedEmpArr);
            prop.selected = opt;
            if(opt){
                this.state.selectedPropertiesArr.push(prop);
            }else{
                var index = $.inArray(prop, this.state.selectedPropertiesArr);
                this.state.selectedPropertiesArr.splice(index, 1);
            }
        }
    }

    angular.module('app')
        .component('exportmembers', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/employeeOverview/export-members/index.html',
            bindings: {
                selectedEmployeeData: '<',
            },
            controller: ExportMembersController
        });
}(angular));