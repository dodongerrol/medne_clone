(function (angular) {
    'use strict';

    class BillingContactAPI {
        constructor($http, serverUrl) {
            this.$http = $http;
            this.serverUrl = serverUrl.url;
        }
        get() {
            return this.$http.get(`${this.serverUrl}/hr/account_billing`)
                .then((response) => response);
        }
        update () {
        }
    }

    angular.module('app')
        .service('billingContactAPI', BillingContactAPI);
}(angular));
