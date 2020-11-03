app.directive("billingPaymentsDirective", [
    "$state",
    "serverUrl",
    "$timeout",
    "hrSettings",
    function directive($state, serverUrl, $timeout, hrSettings) {
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
                scope.costCentre = 'Location';
                scope.invoiceSelectorValue = 'spending';
                scope.page_active = 1;
                scope.per_page = 5;
                scope.totalOutstanding = 0;
                
                scope.currencyType = localStorage.getItem('currency_type');

                scope.getBillingInvoiceHistory = type =>{
                  console.log(scope.page_active);
                    hrSettings.fetchCompanyInvoiceHistory(type, scope.page_active, scope.per_page)
                    .then( response => {
                        console.log(response);
                        scope.billingData = response.data.data;
                        scope.totalOutstanding = response.data.total_due;
                        scope.billingPagination = response.data;
                        scope.billingData.forEach( item => {
                            if (moment(item.invoice_date, 'D MMM YYYY') <= moment()) {
                                item.isEnableInvoices = true;
                            }else{
                                item.isEnableInvoices = false;
                            }
                        });  
                    })
                };

                scope.invoiceSelector = selectorValue => {
                    scope.invoiceSelectorValue = selectorValue;
                    scope.getBillingInvoiceHistory (scope.invoiceSelectorValue);
                };

                scope.downloadViewInvoice = function ( id ) {
                    if(scope.invoiceSelectorValue == 'spending'){
                      if(scope.download_token.live == true) {
                        window.open(scope.download_token.download_link + "/spending_invoice_download?id=" + id + '&token=' + scope.download_token.token);
                      } else {
                        window.open(serverUrl.url + '/hr/statement_download?id=' + id + '&token=' + window.localStorage.getItem('token'));
                      }
                    }
                    if(scope.invoiceSelectorValue == 'spending_purchase'){
                      window.open(serverUrl.url + '/hr/download_spending_purchase_invoice?id=' + id + '&token=' + window.localStorage.getItem('token'));
                    }
                    if(scope.invoiceSelectorValue == 'plan'){
                      window.open(serverUrl.url + '/benefits/invoice?invoice_id=' + id + '&token=' + window.localStorage.getItem('token'));
                    }
                    if(scope.invoiceSelectorValue == 'plan_withdrawal'){
                      window.open(serverUrl.url + '/hr/get_cancellation_details/' + id + '&token=' + window.localStorage.getItem('token'));
                    }
                    if(scope.invoiceSelectorValue == 'deposit'){
                      window.open(serverUrl.url + '/hr/spending_desposit?id=' + id + '&token=' + window.localStorage.getItem('token'));
                    }
                  }
                  scope.downloadViewTransactions = function ( id, type ) {
                    if(type == "panel") {
                      window.open(serverUrl.url + '/hr/statement_in_network_download?id=' + id + '&token=' + window.localStorage.getItem('token'));
                    } else {
                      window.open(serverUrl.url + '/hr/download_non_panel_invoice?id=' + id + '&token=' + window.localStorage.getItem('token'));
                    }
                  }
                  scope.getDownloadToken = async function( ) {
                    await hrSettings.getDownloadToken( )
                    .then(function(response){
                      scope.download_token = response.data;
                    });
                  }
                  
                  scope._prevPageList_ = function () {
                    scope.page_active -= 1;
                    scope.getBillingInvoiceHistory( scope.invoiceSelectorValue );
                  }
          
                  scope._nextPageList_ = function () {
                    scope.page_active += 1;
                    scope.getBillingInvoiceHistory( scope.invoiceSelectorValue );
                  }

                  scope._selectNumList_ = function ( num ) {
                    scope.page_active = num;
                    scope.getBillingInvoiceHistory( scope.invoiceSelectorValue );
                  }
          
                  scope._setPageLimit_ = function ( num ) {
                    scope.per_page = num;
                    scope.page_active = 1;
                    scope.getBillingInvoiceHistory( scope.invoiceSelectorValue );
                  }

                  scope.range = function (num) {
                    var arr = [];
                    for (var i = 0; i < num; i++) {
                      arr.push(i);
                    }
                    return arr;
                  };

                  scope.showLoading = function () {
                    $(".circle-loader").fadeIn();
                    loading_trap = true;
                  };
          
                  scope.hideLoading = function () {
                    setTimeout(function () {
                      $(".circle-loader").fadeOut();
                      loading_trap = false;
                    }, 10);
                  };

                scope.onload = async () =>{
                    await scope.getDownloadToken();
                    await scope.getBillingInvoiceHistory(scope.invoiceSelectorValue);
                };
                scope.onload();



            }
        }
    }
]);
