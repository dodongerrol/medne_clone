(function (angular) {
    'use strict';

    class WorkLocationService {
        constructor($http, serverUrl) {
            this.$http = $http;
            this.serverUrl = serverUrl.url;
        }
        getLocations() {
            // return this.$http.get('http://localhost:3000/locations')
            //     .then(response => response.data)
        }
        storeLocation() {
            //
        }
        updateLocation() {
            //
        }
        removeLocation() {
            //
        }
    }

    angular.module('app')
        .service('workLocationService', WorkLocationService);
}(angular));
