app.directive("locationsDepartmentsDirective", [
    "$state",
    "serverUrl",
    "$timeout",
    function directive($state, serverUrl, $timeout) {
      return {
        restrict: "A",
        scope: true,
        link: function link(scope, element, attributeSet) {
            scope.views = `${serverUrl.url}/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments`;
            scope.action = null;
            scope.workLocation = null;
            scope.department = null;
            scope.filters = {
                search: ''
            }
            scope.countries = countries()
            scope.modals = {
                location: 'location-modal',
                department: 'department-modal',
                allocation: 'allocate-emloyees-modal',
                confirmation: 'confirmation-modal'
            }
            scope.confirmProperties = {
                title: '',
                message: '',
                type: DEPARTMENTS
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
            scope.departmentFields =  {
                name: '',
                employees: 0
            },
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
            scope.departments = [
                {
                    id: 'sxfjt1614n9xr8v7dtosab',
                    name: 'Sales Team',
                    employees: 0
                },
                {
                    id: '119j3lfov0oprtwqbywiaj',
                    name: 'Product',
                    employees: 0
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
            scope.isEditing = () => {
                return scope.action === EDIT ? true : false;
            }
            scope.resetFields = () => {
                scope.workLocationFields = _.mapValues(
                    scope.workLocationFields,
                    () => null
                );
                scope.workLocationFields.country = 'Singapore';
                scope.action = CREATE;
                scope.departmentFields = _.mapValues(
                    scope.departmentFields,
                    () => null
                );
            }
            scope.setField = (field, value) => {
                scope.workLocationFields[field] = value;
            }
            scope.presentModal = (id, show = true) => {
                $(`#${id}`).modal(show ? "show" : "hide");
            };
            scope.addLocation = () => {
                scope.resetFields();
                scope.presentModal(scope.modals.location, true);
            }
            scope.editLocation = (location) => {
                scope.action = EDIT;
                scope.workLocationFields = { ...location };
                scope.presentModal(scope.modals.location, true)
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
            scope.allocateEmployees = (data, type) => {
                if (type === LOCATIONS) {
                    scope.workLocation = { ...data };
                }

                if (type === DEPARTMENTS) {
                    scope.department = { ...data };
                }

                scope.presentModal(scope.modals.allocation, true);
            }
            scope.saveAllocation = () => {
                // Silence is golden
            }
            scope.addDepartment = () => {
                scope.resetFields();
                scope.presentModal(scope.modals.department, true);
            }
            scope.editDepartment = (department) => {
                scope.action = EDIT;
                scope.departmentFields = { ...department };
                scope.presentModal(scope.modals.department, true)
            }
            scope.saveDepartment = () => {
                scope.departmentFields.id = scope.generateRandomString();
                scope.departmentFields.employees = 0;

                scope.departments.push(scope.departmentFields);

                scope.resetFields();
                scope.presentModal(scope.modals.department, false);
            },
            scope.removeLocation = () => {
                // Silence is golden
            }
            scope.attemptDestroy = (type) => {
                if (type === LOCATIONS) {
                    scope.workLocation = { ...data };
                }

                if (type === DEPARTMENTS) {
                    scope.confirmProperties.title = 'Remove Department';
                    scope.confirmProperties.message = "Are you sure you want to remove your Product department? If you do, the 5 current employees in Product won't be assigned to a department.";
                    scope.confirmProperties.type = type;
                    scope.presentModal(scope.modals.department, false);
                }

                scope.presentModal(scope.modals.confirmation, true);
            }
            scope.destroy = () => {

            }
            scope.generateRandomString = () => {
                return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
            }
        },
      };
    },
]);
