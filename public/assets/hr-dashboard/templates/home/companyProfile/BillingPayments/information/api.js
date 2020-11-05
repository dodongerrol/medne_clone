(function (angular) {
    'use strict';

    class BillingInformationAPI {
        constructor($http, serverUrl) {
            this.$http = $http;
            this.serverUrl = serverUrl.url;
        }
        get() {
            return this.$http.get(`${this.serverUrl}/hr/get_billing_information`).then((response) => response.data);
        }
        update ( data ) {
            return this.$http.post(`${this.serverUrl}/hr/update/billing_information`, data).then((response) => response.data);
        }
        getPermission() {
            return this.$http.get(`${this.serverUrl}/hr/get_account_permissions`).then((response) => response.data);
        }
    }

    angular.module('app')
        .service('billingInformationAPI', BillingInformationAPI);
}(angular));
