app.directive("locationsDepartmentsDirective", [
    "$state",
    "serverUrl",
    "$timeout",
    function directive($state, serverUrl, $timeout) {
      return {
        restrict: "A",
        scope: true,
        link: function link(scope, element, attributeSet) {
            scope.states = {
                showAddLocationModal: false,
                showEditLocationModal: false
            }
            scope.workLocationFields = {
                location_name: null,
                business_address: null,
                country: null,
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
            ]
            scope.addLocation = () => {
                scope.showAddLocationModal = !scope.showAddLocationModal;
            }
            scope.editLocation = (location) => {
                scope.workLocationFields = { ...location };
                scope.showEditLocationModal = true;
            }
            scope.saveLocation = () => {

            }
            scope.onLoad = async function () {
                console.log('Loaded!')
            };
            scope.onLoad();
        },
      };
    },
]);
