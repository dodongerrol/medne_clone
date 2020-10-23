(function (angular) {
    'use strict';

    class LocationAPI {
        constructor($http, serverUrl) {
            this.$http = $http;
            this.serverUrl = serverUrl.url;
        }
        get() {
            return this.$http.get(`${this.serverUrl}/hr/get_location_list`)
                .then(response => response.data)
        }
        store(location) {
            return this.$http.post(`${this.serverUrl}/hr/create_locations`, { ...location });
        }
        update (location) {
            return this.$http.post(`${this.serverUrl}/hr/update_location`, { ...location });
        }
        delete (id) {
            return this.$http.get(`${this.serverUrl}/hr/remove_location?id=${id}`)
        }
        permission () {
            return this.$http.get(`${this.serverUrl}/hr/get_account_permissions`)
                .then(response => response.data)
        }
    }

    angular.module('app')
        .service('locationAPI', LocationAPI);
}(angular));
