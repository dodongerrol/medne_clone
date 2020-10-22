app.directive("billingPaymentsDirective", [
    "$state",
    "serverUrl",
    "$timeout",
    function directive($state, serverUrl, $timeout) {
        return {
            restrict: "A",
            scope: true,
            link: function link(scope, element, attributeSet) {
                scope.sectionsView = `${serverUrl.url}/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/sections/`;
                scope.modalsView = `${serverUrl.url}/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/modals/`;
                scope.modals ={
                    info: 'information',
                    contact: 'contact',
                    centre: 'centre'
                },
                scope.editingCostCentre = false;
                scope.countries = countries();
                scope.informationLabels = {
                    billing_name: 'Medicloud Pte Ltd',
                    billing_address: '7 Temasek Boulevard #18-02 Suntec Tower One, Singapore 038987',
                    unit: '#18-02',
                    building: 'Suntec Tower One',
                    country: 'Singapore',
                    postal_code: '038987',
                }
                scope.contactLabels = {
                    full_name: 'Filbert Tan',
                    email_address: 'filbert@mednefits.com',
                    phone_number: '(+65) 8856 4736'
                }
                scope.informationFields = {
                    billing_name: '',
                    billing_address: '',
                    unit: '',
                    building: '',
                    country: 'Singapore',
                    postal_code: '',
                }
                scope.contactFields = {
                    full_name: null,
                    email_address: null,
                    phone_code: null,
                    phone_number: null
                },
                scope.costCentre = 'Location'
                scope.setCostCentre = (costcentre) => {
                    scope.costCentre = costcentre;
                }
                scope.setField = (field, value) => {
                    scope.informationFields[field] = value;
                },
                scope.resetFields = () => {
                    scope.informationFields = _.mapValues(
                        scope.informationFields,
                        () => null
                    );
                    scope.contactFields = _.mapValues(
                        scope.contactFields,
                        () => null
                    );
                }
                scope.presentModal = (id, show = true) => {
                    $(`#${id}`).modal(show ? "show" : "hide");
                };
                scope.editBillingInfo = () => {
                    scope.informationFields = { ...scope.informationFields }
                    scope.presentModal(scope.modals.info, true);
                }
                scope.editBillingContact = () => {
                    scope.contactFields = { ...scope.contactLabels }
                    scope.presentModal(scope.modals.contact, true);
                }
                scope.editCostcentre = () => {
                    scope.editingCostCentre = true;
                    scope.presentModal(scope.modals.centre, true);
                }
                scope.saveBillingInfo = () => {
                    scope.informationLabels = { ...scope.informationFields }
                    scope.resetFields();
                    scope.presentModal(scope.modals.info, false);
                }
                scope.saveBillingContact = () => {
                    scope.contactLabels = { ...scope.contactFields }
                    scope.resetFields();
                    scope.presentModal(scope.modals.contact, false);
                }
            }
        }
    }
]);