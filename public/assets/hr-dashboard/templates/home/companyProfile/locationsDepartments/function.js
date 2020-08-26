app.directive("locationsDepartmentsDirective", [
    "$state",
    "serverUrl",
    "$timeout",
    function directive($state, serverUrl, $timeout) {
      return {
        restrict: "A",
        scope: true,
        link: function link(scope, element, attributeSet) {
            scope.workLocation = null;
            scope.filters = {
                search: ''
            }
            scope.countries = countries()
            scope.modals = {
                add: 'add-location-modal',
                edit: 'edit-location-modal',
                allocation: 'allocate-emloyees-modal'
            }
            scope.workLocationFields = {
                location_name: null,
                business_address: null,
                unit: null,
                building: null,
                country: 'Singapore',
                postal_code: null,
                employees: 0
            }
            scope.workLocations = [
                {
                    id: 1,
                    location_name: '7 Temasek Boulevard',
                    business_address: '7 Temasek Boulevard ',
                    unit: '#18-02',
                    building: 'Suntec Tower One',
                    country: 'Singapore',
                    postal_code: '038987',
                    employees: 40
                },
                {
                    id: 2,
                    location_name: 'One North Office',
                    business_address: 'Ayer Rajah Crescent',
                    unit: '#01-1112',
                    building: 'JTC Launchpad',
                    country: 'Singapore',
                    postal_code: '695049',
                    employees: 5
                }
            ],
            scope.employees = [
                {
                    id: 1,
                    name: 'Mednefits Member 1'
                },
                {
                    id: 2,
                    name: 'Mednefits Member 2'
                },
                {
                    id: 3,
                    name: 'Mednefits Member 3'
                },
                {
                    id: 4,
                    name: 'Mednefits Member 4'
                },
                {
                    id: 5,
                    name: 'Mednefits Member 5'
                },
                {
                    id: 6,
                    name: 'Mednefits Member 6'
                }
            ]
            scope.selectedEmployees = [],
            scope.resetFields = () => {
                scope.workLocationFields = _.mapValues(
                    scope.workLocationFields,
                    () => null
                );
                scope.workLocationFields.country = 'Singapore';
            }
            scope.setField = (field, value) => {
                scope.workLocationFields[field] = value;
            }
            scope.presentModal = (id, show = true) => {
                $(`#${id}`).modal(show ? "show" : "hide");
            };
            scope.addLocation = () => {
                scope.resetFields();
                scope.presentModal(scope.modals.add, true);
            }
            scope.editLocation = (location) => {
                scope.workLocationFields = { ...location };
                scope.presentModal(scope.modals.edit, true)
            }
            scope.onCheck = (employee) => {
                const existed = scope.employeeExisted(employee);

                if (existed) {
                    scope.selectedEmployees =  _.filter(scope.selectedEmployees, function (selectedEmployee) {
                        return  selectedEmployee.id !== existed.id
                    });

                    return;
                }

                scope.selectedEmployees.push(employee);
            }
            scope.employeeChecked = (employee) => {
                return  _.find(
                    scope.selectedEmployees,
                    function (selectedEmployee) {
                        return selectedEmployee.id === employee.id
                }) ? true : false;
            }
            scope.removeSelectedEmployee = (employee) => {
                scope.selectedEmployees =  _.filter(scope.selectedEmployees, function (selectedEmployee) {
                    return  selectedEmployee.id !== employee.id
                });
            }
            scope.employeeExisted = (employee) => {
               return  _.find(
                    scope.selectedEmployees,
                    function (selectedEmployee) {
                        return selectedEmployee.id === employee.id
                });
            }
            scope.saveLocation = () => {
                // Silence is golden
            }
            scope.removeLocation = () => {
                // Silence is golden
            }
            scope.allocateEmployees = (location) => {
                scope.workLocation = location;
                scope.presentModal(scope.modals.allocation, true);
            }
            scope.saveAllocation = () => {
                // Silence is golden
            }
        },
      };
    },
]);
