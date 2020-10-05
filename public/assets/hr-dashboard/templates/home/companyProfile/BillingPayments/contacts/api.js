(function (angular) {
    'use strict';

    class BillingContactAPI {
        constructor($http, serverUrl) {
            this.$http = $http;
            this.serverUrl = serverUrl.url;
        }
        get() {
            return this.$http.get(`${this.serverUrl}/hr/get_billing_contact`)
                .then((response) => response.data.data);
        }
        update (billingContact) {
            return this.$http.post(`${this.serverUrl}/hr/update/billing_contact`, {
                ...billingContact
            });
        }
    }

    angular.module('app')
        .service('billingContactAPI', BillingContactAPI);
}(angular));
