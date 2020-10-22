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
        update () {
        }
    }

    angular.module('app')
        .service('billingInformationAPI', BillingInformationAPI);
}(angular));
