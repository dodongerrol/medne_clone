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
        getPermission() {
            return this.$http.get(`${this.serverUrl}/hr/get_account_permissions`).then((response) => response.data);
        }
    }

    angular.module('app')
        .service('billingContactAPI', BillingContactAPI);
}(angular));
