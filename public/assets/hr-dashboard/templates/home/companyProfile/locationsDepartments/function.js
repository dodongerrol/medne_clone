/**
 * Contains all Locations and Departments variables and method calls
 *
 * @jquery for extra dom manipulations
 * @axios for API calls with auth token injected out of the box
 * @return {object} location & departments directive object
 */
function locationDepartments()  {
    return {
        showAddLocationModal: false, // Show or Unshow Add Location Modal
        showEditLocationModal: false,  // Show or Unshow Edit Location Modal
        workLocationFields: {
            location_name: null,
            business_address: null,
            country: null,
            postal_code: null,
            employees: 0
        },
        workLocations: [
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
        /**
         * Populate form fields on edit button click
         */
        editLocation: function (location) {
            this.workLocationFields = { ...location };
            this.showEditLocationModal = true;
        },
        /**
         * Do a post request using axios instead of $http
         */
        removeLocation: async function (location)  {
            // Do an axios POST method
            // $http.post(url, params)
            await axios.post(``);
        },
         /**
         * Do a post request using axios instead of $http
         */
        updateLocation: async function () {
            // Do an axios POST method
            // $http.post(url, params)
            await axios.post(``);
        }
    }
}