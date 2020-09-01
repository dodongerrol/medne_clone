app.directive("billingPaymentsDirective", [
    "$state",
    "serverUrl",
    "$timeout",
    function directive($state, serverUrl, $timeout) {
        return {
            restrict: "A",
            scope: true,
            link: function link(scope, element, attributeSet) {
                scope.sections = `${serverUrl.url}/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/sections/`;
            }
        }
    }
]);