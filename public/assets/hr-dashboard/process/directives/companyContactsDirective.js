app.directive("companyContactsDirective", [
  "$state",
  "hrSettings",
  "$rootScope",
  "serverUrl",
  "$http",
  function directive($state, hrSettings, $rootScope, serverUrl, $http) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("companyContactsDirective Runnning !");
        scope.local_currency_type = localStorage.getItem('currency_type');
        scope.company_contacts = {};
        scope.transactions = {};
        scope.transactions.current_total = 0;
        scope.transactions.temp_total = 0;
        scope.refunds = {};
        scope.billings = {};
        scope.benefits_spending = {};
        scope.benefits_spending.current_total = 0;
        scope.benefits_spending.temp_total = 0;
        scope.spending_deposits = {};
        scope.options = {};
        scope.download_token = {};
        scope.token = window.localStorage.getItem('token');
        scope.wdraw_dl = false;

        scope.plan_transactions_page = 1;
        scope.benefits_spending_page = 1;
        scope.spending_deposit_page = 1;
        scope.statementHide = true;
        // scope.empStatementShow = false;
        scope.selectedSpendingTab = 0;


         // Active Plan Pagination and Old Plan List
         scope.selectedOldPlan = null;
         scope.activePlan_active_page = 1;
         scope.isActivePlanDropShow = false;
         scope.oldPlan_list = [];
         scope.oldPlan_active_page = null;
         scope.isOldPlanListDropShow = false;

         scope.invoiceHistoryType = 'plan';


        scope.$on("informationRefresh", function(evt, data){
          scope.onLoad();
        });

        scope.global_hrData = {
          phone_code: '',
        }

        scope.initializeGeoCode = function () { 
          var settings = {
            preferredCountries: [],
            separateDialCode: true,
            initialCountry: "SG",
            autoPlaceholder: "off",
            utilsScript: "../assets/hr-dashboard/js/utils.js",
            onlyCountries: ["sg","my"],
          }

          var settings2 = {
            preferredCountries: [],
            separateDialCode: true,
            initialCountry: "MY",
            autoPlaceholder: "off",
            utilsScript: "../assets/hr-dashboard/js/utils.js",
            onlyCountries: ["sg","my"],
          }

          var settings3 = {
            preferredCountries: [],
            separateDialCode: true,
            initialCountry: false,
            autoPlaceholder: "off",
            utilsScript: "../assets/hr-dashboard/js/utils.js",
            onlyCountries: ["sg","my"],
          }
  
          if ( scope.global_hrData.phone_code == '+65' ) {
            var input = document.querySelector("#phone_number");
            iti1 = intlTelInput(input, settings);

            input.addEventListener("countrychange", function () {
              scope.global_hrData.phone_code = iti1.getSelectedCountryData().dialCode;
            })
          } else if ( scope.global_hrData.phone_code == '+60' ) {
            var input2 = document.querySelector("#phone_number");
            iti1 = intlTelInput(input2, settings2);

            input2.addEventListener("countrychange", function () {
              scope.global_hrData.phone_code = iti1.getSelectedCountryData().dialCode;
            })
          } else if ( scope.global_hrData.phone_code == '+63' || scope.global_hrData.phone_code == "" || scope.global_hrData.phone_code == null ) {
            $('.iti__selected-dial-code').addClass('empty');
            var input3 = document.querySelector("#phone_number");
            iti1 = intlTelInput(input3, settings3);

            input3.addEventListener("countrychange", function () {
              scope.global_hrData.phone_code = iti1.getSelectedCountryData().dialCode;
            })
          }
        }

        

        scope.selectSpendingTab = function(opt){
          scope.selectedSpendingTab = opt;
        }

        scope.companyAccountType = function () {
          scope.account_type = localStorage.getItem('company_account_type');

          if(scope.account_type === 'enterprise_plan') {
            $('.statement-hide').hide();
            scope.statementHide = false;
            scope.empStatementShow = true;
          }
        }

        scope.downDepedentInvoice = function(id) {
          window.open(serverUrl.url + '/hr/download_dependent_invoice?dependent_plan_id=' + id + '&token=' + window.localStorage.getItem('token'));
        }

        scope.downloadSpendingDeposit = function(data) {
          window.open(serverUrl.url + '/hr/spending_desposit?id=' + data + '&token=' + window.localStorage.getItem('token'));
        }

        scope.goToEmpOverview = function(){
          setTimeout(function() {
            $state.go('employee-overview');
          }, 100);
        }

        scope.openEditInfoModal = function(data) {
          $rootScope.$broadcast("editDetailsInitialized", {
            modal: "account-billing-edit-business-info",
            data: data
          });
        };

        scope.openEditContactModal = function(data){
          $rootScope.$broadcast("editDetailsInitialized", {
            modal: "account-billing-edit-business-contact",
            data: data
          });
        };

        scope.openEditBillingModal = function(data){
          $rootScope.$broadcast("editDetailsInitialized", {
            modal: "account-billing-edit-billing-contact-and-address",
            data: data
          });
        };

        scope.openEditBillingAddressModal = function(data){
          $rootScope.$broadcast("editDetailsInitialized", {
            modal: "account-billing-edit-billing-contact-and-address2",
            data: data
          });
        };

        scope.openEditPaymentModal = function(data){
          $rootScope.$broadcast("editDetailsInitialized", {
            modal: "account-billing-edit-payment-information",
            data: data
          });
        };

        scope.openEditPaymentModal2 = function(data){
          data.contacts = scope.company_contacts;
          $rootScope.$broadcast("editDetailsInitialized", {
            modal: "account-billing-edit-payment-information-details",
            data: data
          });
        };

        scope.openEditPasswordModal = function(data){
          $rootScope.$broadcast("editPasswordInitialized", {
            modal: "account-billing-edit-password",
            data: data
          });
        };

        scope.calculateTotalCredits = function(medical, wellness) {
          var total = parseFloat(medical) + parseFloat(wellness);
          return total.toFixed(2);
        }

        scope.calculateDepositData = function(medical, wellness, percent) {
          var total = parseFloat(medical) + parseFloat(wellness);
          var deposit = total * parseFloat(percent);
          return deposit.toFixed(2);
        }

        scope.cancellation_details = {};

        scope.selectActivePlan = function( list ){
          scope.selected_active_plan = list;

          hrSettings.getCompActivePlanDetails( list.customer_active_plan_id )
            .then(function(response){
              scope.selected_active_plan_details = response.data.data;
            })
        }

        scope.activePlanDownloadInvoice = function(){
          window.open(serverUrl.url + '/benefits/invoice?invoice_id=' + scope.selected_active_plan_details.invoice.corporate_invoice_id + '&token='+window.localStorage.getItem('token'));
        }

        scope.activePlanDownloadReceipt = function(){
          window.open(serverUrl.url + '/benefits/receipt?invoice_id=' + scope.selected_active_plan_details.invoice.corporate_invoice_id);
        }

        scope.downloadHeadCountPDF = function(invoice_data) {
          $(".show-dl").show();
          $(".hide-dl").hide();
          var file = document.getElementById("head-count-print");
          (form = $("#head-count-print")),
            (cache_width = form.width()),
            (pdf_name =
              $(".invoice_number").text() +
              " (" +
              $(".invoice_first_day").text() +
              " - " +
              $(".invoice_last_day").text() +
              " )"),
            (a4 = [640, 841.89]); // for a4 size paper width and height
          getCanvas().then(function(canvas) {
            var img = canvas.toDataURL("image/png");
            var doc = new jsPDF({
              unit: "px",
              format: "a4"
            });
            doc.addImage(img, "PNG", 0, 0);
            // doc.save(pdf_name + '.pdf');
            form.width(cache_width);
            window.open(doc.output("bloburl"), "_blank");
          });
        };

        scope.downloadRefundPDF = function(invoice_data) {
          console.log(invoice_data);
          $(".show-dl").show();
          $(".hide-dl").hide();
          var file = document.getElementById("pdf-print");
          (form = $("#pdf-print")),
            (cache_width = form.width()),
            (pdf_name =
              $(".invoice_number").text() +
              " (" +
              $(".invoice_first_day").text() +
              " - " +
              $(".invoice_last_day").text() +
              " )"),
            (a4 = [640, 841.89]); // for a4 size paper width and height
          getCanvas().then(function(canvas) {
            var img = canvas.toDataURL("image/png");
            var doc = new jsPDF({
              unit: "px",
              format: "a4"
            });
            doc.addImage(img, "PNG", 0, 0);
            // doc.save(pdf_name + '.pdf');
            form.width(cache_width);
            window.open(doc.output("bloburl"), "_blank");
          });
        };

        scope.downloadWdraw = function(id, index){
          scope.setHeadCount(false);
          window.location.href = window.location.origin + "/hr/get_cancellation_details/" + id;
        };

        function getCanvas() {
          form.width(a4[0] * 1.33333 - 80).css("max-width", "none");
          return html2canvas(form, {
            imageTimeout: 2000,
            removeContainer: true,
            allowTaint: false,
            useCORS: true
          });
        }

        scope.downloadWdrawHide = function(){
          scope.wdraw_dl = false;
        };

        scope.setHeadCount = function(opt){
          scope.dl_head_count = opt;
        };

        scope.toggleInvoiceNewHeadcount = function(trans, index){
          scope.setHeadCount(true);
          $(".transaction-table tbody tr:nth-child(" + (index + 1) + ") .edit-button-in-table").text("Downloading..");

          hrSettings
            .getDownloadHeadCountPlan(trans.customer_active_plan_id)
            .then(function(response) {
              scope.head_count_data = response.data;
              $(".transaction-table tbody tr .edit-button-in-table").text(
                "Download"
              );
              scope.wdraw_dl = true;
              setTimeout(function() {
                $("#pdf-print").hide();
                $("#head-count-print").show();
              }, 300);
            });
        };

        scope.getTransac = function(page){
          scope.toggleLoading();
          var curr_total = scope.transactions.current_total != 0 ? scope.transactions.current_total : 0;
          hrSettings.getTransactions( page ).then(function(response) {
            scope.transactions = response.data;
            angular.forEach(scope.transactions.data, function(value, key) {
              if(scope.transactions.data[ key ].amount.includes('S$')) {
                value.new_amount = scope.transactions.data[ key ].amount.replace('S$','');
              } else if (scope.transactions.data[ key ].amount.includes('RM')) {
                value.new_amount = scope.transactions.data[ key ].amount.replace('RM','');
              } else if (scope.transactions.data[ key ].amount.includes('SGD')) {
                value.new_amount = scope.transactions.data[ key ].amount.replace('SGD','');
              } else if (scope.transactions.data[ key ].amount.includes('MYR')) {
                value.new_amount = scope.transactions.data[ key ].amount.replace('MYR','');
              }
            });

            scope.transactions.current_total = curr_total + parseInt(response.data.to);
            scope.transactions.temp_total = parseInt(response.data.to);
            setTimeout(function() {
              $(".info-container").fadeIn();
              $(".loader-container").hide();
            }, 200);
            scope.toggleOff();
          });
        };

        scope.getTransacPrev = function(page){
          scope.toggleLoading();
          var curr_total = scope.transactions.current_total != 0 ? scope.transactions.current_total : 0;
          var temp_total = scope.transactions.temp_total != 0 ? scope.transactions.temp_total : 0;
          hrSettings.getTransactions( page ).then(function(response) {
            scope.transactions = response.data;
            scope.transactions.current_total = curr_total - temp_total ;
            scope.transactions.temp_total = parseInt(response.data.to);
            setTimeout(function() {
              $(".info-container").fadeIn();
              $(".loader-container").hide();
            }, 200);
            scope.toggleOff();
          });
        };

        scope.getBenefitsSpendingTransac = function(page){
          scope.toggleLoading();
          var request = null;
          var curr_total = scope.benefits_spending.current_total != 0 ? scope.benefits_spending.current_total : 0;
          if(scope.account_plan.plan_method == 'pre_paid'){
            request = hrSettings.getPrePaidSpendingPurchaseTransac(page);
          }else{
            request = hrSettings.getBenefitsSpendingTransac(page);
          }
          
          request.then(function(response) {
            scope.benefits_spending = response.data;
            // angular.forEach(scope.benefits_spending.data, function(value, key) {
            //   if(scope.benefits_spending.data[ key ].amount.includes('S$')) {
            //     value.new_amount = scope.benefits_spending.data[ key ].amount.replace('S$','');
            //   } else if (scope.benefits_spending.data[ key ].amount.includes('RM')) {
            //     value.new_amount = scope.benefits_spending.data[ key ].amount.replace('RM','');
            //   } else if (scope.benefits_spending.data[ key ].amount.includes('SGD')) {
            //     value.new_amount = scope.benefits_spending.data[ key ].amount.replace('SGD','');
            //   } else if (scope.benefits_spending.data[ key ].amount.includes('MYR')) {
            //     value.new_amount = scope.benefits_spending.data[ key ].amount.replace('MYR','');
            //   }
            // });

            scope.benefits_spending.current_total = curr_total + parseInt(response.data.to);
            scope.benefits_spending.temp_total = parseInt(response.data.to);
            scope.toggleOff();

          });
        };

        scope.getBenefitsSpendingTransacPrev = function(page){
          scope.toggleLoading();
          var curr_total = scope.benefits_spending.current_total != 0 ? scope.benefits_spending.current_total : 0;
          var temp_total = scope.benefits_spending.temp_total != 0 ? scope.benefits_spending.temp_total : 0;
          hrSettings.getBenefitsSpendingTransac( page ).then(function(response) {
            scope.benefits_spending = response.data;
            scope.benefits_spending.current_total = curr_total - temp_total ;
            scope.benefits_spending.temp_total = parseInt(response.data.to);
            setTimeout(function() {
              $(".info-container").fadeIn();
              $(".loader-container").hide();
            }, 200);
            scope.toggleOff();
          });
        };

        scope.downloadSpendingInvoice = function(data) {
          if(scope.account_plan.plan_method == 'pre_paid'){
            if(data.spending_type == "purchase")  {
              window.open(serverUrl.url + "/hr/download_spending_purchase_invoice?id=" + data.spending_purchase_invoice_id + "&token=" + window.localStorage.getItem('token'));
            } else {
              if(scope.download_token.live == true) {
                window.open(scope.download_token.download_link + "/spending_invoice_download?id=" + data.statement_id + '&token=' + scope.download_token.token);
              } else {
                window.open(serverUrl.url + '/hr/statement_download?id=' + data.statement_id + '&token=' + window.localStorage.getItem('token'));
              }
            }
          }else{
            if(scope.download_token.live == true) {
              window.open(scope.download_token.download_link + "/spending_invoice_download?id=" + data.statement_id + '&token=' + scope.download_token.token);
            } else {
              window.open(serverUrl.url + '/hr/statement_download?id=' + data.statement_id + '&token=' + window.localStorage.getItem('token'));
            }
          }
          
        }

        scope.downloadSpendingReceipt = function(data) {
          if(scope.download_token.live == true) {
            window.open(scope.download_token.download_link + "/spending_receipt_download?id=" + data.statement_id + '&token=' + scope.download_token.token);
          } else {
            window.open(serverUrl.url + '/hr/download_spending_receipt?statement_id=' + data.statement_id + '&token=' + window.localStorage.getItem('token'));
          }
        }

        scope.downloadRefund = function(customer_active_plan_id) {
          window.open(`${serverUrl.url}/hr/get_cancellation_details?id=${customer_active_plan_id}&token=${window.localStorage.getItem('token')}`)
        }

        scope.getRefundList = function() {
          hrSettings.getRefunds().then(function(response) {
            scope.refunds = response.data;
          });
        };

        scope.getUsersRefund = function(data) {
          hrSettings
            .getRefundUsers(data.payment_refund_id)
            .then(function(response) {
              response.data.data.comp = response.data.company;
              $rootScope.$broadcast("refundList", { data: response.data.data });
              $("#view-withdrawn-employee-details-modal").modal("show");
            });
        };

        scope.getCompanyContacts = function() {
          hrSettings.getContacts().then(function(response) {
            console.log(response);
            scope.company_contacts = response.data.data;
            setTimeout(function() {
              $(".info-container").fadeIn();
              $(".loader-container").hide();
            }, 200);
          });
        };

        scope.getBillingList = function() {
          hrSettings.getBillings().then(function(response) {
            scope.billings = response.data;
            setTimeout(function() {
              $(".info-container").fadeIn();
              $(".loader-container").hide();
            }, 200);
          });
        };

        scope.getPlanSubscriptions = function(){
          hrSettings.getPlanSubs().then(function(response) {
            scope.plan_subs = response.data;
            scope.plan_subs.start_date = moment( scope.plan_subs.start_date, 'DD/MM/YYYY' ).format( 'DD MMMM YYYY' );
            scope.plan_subs.end_date = moment( scope.plan_subs.end_date, 'DD/MM/YYYY' ).format( 'DD MMMM YYYY' );
            setTimeout(function() {
              $(".info-container").fadeIn();
              $(".loader-container").hide();
            }, 200);
          });
        }
        scope.comp_active_plans = [];
        
        scope.getActiveCompPlans = function(){
          hrSettings.getCompActivePlans().then(function(response) {
            scope.comp_active_plans = response.data.data;
            scope.getCustomerPlanId = response.data.data[0].customer_active_plan_id;
            
            angular.forEach( scope.comp_active_plans, function( value, key ){
              value.plan_start = moment( value.plan_start ).format( 'DD MMMM YYYY' );
            });
            
            // scope.getEnrollmentHistoryData();
            setTimeout(function() {
              $(".info-container").fadeIn();
              $(".loader-container").hide();
            }, 200);
          });
        }


       

        scope.getSpendingDeposits = function( page ) {
          scope.toggleLoading();
          hrSettings.getSpendingDeposits( page )
          .then(function(response) {
            scope.spending_deposits = response.data;
            angular.forEach(scope.spending_deposits.data, function(value, key) {
              if(scope.spending_deposits.data[ key ].amount.includes('S$')) {
                value.new_amount = scope.spending_deposits.data[ key ].amount.replace('S$','');
              } else if (scope.spending_deposits.data[ key ].amount.includes('RM')) {
                value.new_amount = scope.spending_deposits.data[ key ].amount.replace('RM','');
              } else if (scope.spending_deposits.data[ key ].amount.includes('SGD')) {
                value.new_amount = scope.spending_deposits.data[ key ].amount.replace('SGD','');
              } else if (scope.spending_deposits.data[ key ].amount.includes('MYR')) {
                value.new_amount = scope.spending_deposits.data[ key ].amount.replace('MYR','');
              }
            });
            scope.toggleOff();
          });
        }

        scope.showGlobalModal = function( message ){
          $( "#global_modal" ).modal('show');
          $( "#global_message" ).text(message);
        }
        scope.getDownloadToken = function( ) {
          hrSettings.getDownloadToken( )
          .then(function(response){
            scope.download_token = response.data;
          });
        }
        scope.nextPrevPlanTransac = function(opt){
          if( opt == true ){
            scope.plan_transactions_page++;
            scope.getTransac(scope.plan_transactions_page, opt);
          }else{
            scope.plan_transactions_page--;
            scope.getTransacPrev( scope.plan_transactions_page );
          }
          
        }

        scope.nextPrevBenefitsSpendingTransac = function(opt){
          if( opt == true ){
            scope.benefits_spending_page++;
            scope.getBenefitsSpendingTransac(scope.benefits_spending_page, opt);
          }else{
            scope.benefits_spending_page--;
            scope.getBenefitsSpendingTransacPrev(scope.benefits_spending_page, opt);
          }
          
        }

        scope.nextPrevSpendingDeposits = function(opt){
          if( opt == true ){
            scope.spending_deposit_page++;
          }else{
            scope.spending_deposit_page--;
          }
          scope.getSpendingDeposits(scope.spending_deposit_page);
        }

        var loading_trap = false;

        scope.toggleOff = function( ) {
          $( ".circle-loader" ).fadeOut();
          loading_trap = false;
        }

        scope.toggleLoading = function( ){
          if ( loading_trap == false ) {
            $( ".circle-loader" ).fadeIn(); 
            loading_trap = true;
          }else{
            setTimeout(function() {
              $( ".circle-loader" ).fadeOut();
              loading_trap = false;
            }, 100)
          }
        }

        scope.dependentPlanDownloadInvoice = function(data) {
          window.open(serverUrl.url + '/hr/download_dependent_invoice?dependent_plan_id=' + data.dependent_plan_id + '&token=' + window.localStorage.getItem('token'));
        }

        scope.getPlanInfo = function(){
          hrSettings.getMethodType()
          .then(function(response) {
            scope.plan_info_status = response.data.data;
            scope.account_plan = {
              plan_method : response.data.data.plan.plan_method,
              account_type : response.data.data.plan.account_type,
            }
            scope.getBenefitsSpendingTransac(scope.benefits_spending_page);
          });
        }

        scope.spending_account_status = {};
        scope.getSpendingAcctStatus = function () {
          // hrSettings.getSpendingAccountStatus()
          hrSettings.getPrePostStatus()
						.then(function (response) {
              scope.spending_account_status = response.data;
						});
        }

        scope.passwordData = {
          newPassword: '',
          confirmPassword: '',
        };
        scope.global_passwordSuccess = false;

        scope._updatePasswordBtn_ = function ( ) {
          scope.global_passwordSuccess = false;
          scope.passwordData.newPassword = "";
          scope.passwordData.confirmPassword = "";
          scope.passwordCheck = false;
        }

        scope.passwordCheck = false;
        scope._updatePassword_ = function ( data ) {
          let params = {
            new_password: data.newPassword,
            confirm_password: data.confirmPassword,
          }

          if ( data.newPassword == data.confirmPassword ) {
            scope.toggleLoading();
            hrSettings.updateHrPassword( params )
              .then(function (response) {
                if ( response.status ) {
                  scope.global_passwordSuccess = true;
                }
                scope.toggleOff();
              });
          } else {
            scope.passwordCheck = true;
          }
          
        }
        scope.page_active = 1;
        scope.per_page = 3;
        scope.getInvoiceHistoryData = function ( page,per_page,customer_active_plan_id ) {
          page = scope.page_active;
          per_page = scope.per_page;
          customer_active_plan_id = scope.activePlanDetails_pagination.data.customer_active_plan_id;
          
          scope.toggleLoading();
          hrSettings.getPlanInvoiceHistory( page,per_page,customer_active_plan_id )
            .then(function (response) {
              scope.getPlanInvoiceData = response.data.data.data;
              scope.invoicePlanPagination = response.data.data;
              console.log(scope.getPlanInvoiceData);
              angular.forEach(scope.getPlanInvoiceData, function(value, key) {
                value.invoice_date = moment( value.invoice_date ).format('DD MMMM YYYY');
                value.invoice_due = moment( value.invoice_due ).format('DD MMMM YYYY');
                value.payment_date = moment( value.payment_date ).format('DD MMMM YYYY');
                // value.total = value.total.toFixed(2);
              });
              scope.toggleOff();
            });
        }

        scope.range = function(num) {
          var arr = [];
          for (var i = 0; i < num; i++) {
            arr.push(i);
          }
          return arr;
        };

        scope._selectNumList_ = function ( type,num ) {
          if ( type == 'invoice-history' ) {
            scope.page_active = num;
            scope.getInvoiceHistoryData();
          }
          if ( type == 'enrollment-history' ) {
            scope.enroll_page_active = num;
            scope.getEnrollmentHistoryData();
          }
        }

        scope._prevPageList_ = function ( type ) {
          if ( type == 'invoice-history' ) {
            scope.page_active -= 1;
            scope.getInvoiceHistoryData();
          }
          if ( type == 'enrollment-history' ) {
            scope.enroll_page_active -= 1;
            scope.getEnrollmentHistoryData();
          }
        }

        scope._nextPageList_ = function ( type ) {
          if ( type == 'invoice-history' ) {
            scope.page_active += 1;
            scope.getInvoiceHistoryData();
          }
          if ( type == 'enrollment-history' ) {
            scope.enroll_page_active += 1;
            scope.getEnrollmentHistoryData();
          }
        }

        scope._toggleInvoicePerPage_ = function () {
          $(".invoice-per-page-container").toggle();
        }

        scope._toggleEnrollmentPerPage_ = function () {
          $(".enrollment-per-page-container").toggle();
        }

        scope._setPageLimit_ = function ( type,num ) {
          if ( type == 'invoice-history' ) {
            scope.per_page = num;
            scope.page_active = 1;
            scope.getInvoiceHistoryData();
          }
          if ( type == 'enrollment-history' ) {
            scope.enroll_per_page = num;
            scope.enroll_page_active = 1;
            scope.getEnrollmentHistoryData();
          }
        }

        scope._getHrDetails_ = function () {
          hrSettings.fecthHrDetails( )
            .then(function (response) {
              scope.global_hrData = response.data.hr_account_details;
            });
        } 

        scope._editDetailsBtn_ = function ( data ) {
          console.log(data);
          scope.editHrSuccessfullyUpdated = false;
          scope.initializeGeoCode();
        }
        scope.editHrSuccessfullyUpdated = false;
        scope._updateHrDetails_ = function ( data ) {
          let params = {
            email: data.email,
            phone_number: data.phone,
            fullname: data.full_name, 
            phone_code: data.phone_code,
          }

          scope.toggleLoading();
          hrSettings.updateHrDetails( params )
            .then(function (response) {
              // console.log(response);
              if ( response.data.status == true ) {
                // $('#edit_details').modal('hide');
                scope.editHrSuccessfullyUpdated = true;

                scope._getHrDetails_();
                scope.toggleOff();
              }
            });
        }

        scope.enroll_page_active = 1;
        scope.enroll_per_page = 3;
        scope.getEnrollmentHistoryData = function ( page,per_page,customer_active_plan_id ) {
          page = scope.enroll_page_active;
          per_page = scope.enroll_per_page;
          // customer_active_plan_id = scope.getCustomerPlanId;
          customer_active_plan_id = scope.activePlanDetails_pagination.data.customer_active_plan_id;


          scope.toggleLoading();
          hrSettings.fetchEnrollmentHistoryData( page,per_page,customer_active_plan_id )
            .then(function (response) {
              scope.global_enrollmentHistoryData = response.data.data.data;
              scope.global_enrollmentHistoryPagination = response.data.data;
              console.log(scope.global_enrollmentHistoryData)
              angular.forEach(scope.global_enrollmentHistoryData, function(value, key) {
                value.date_of_edit = moment( value.date_of_edit ).format('DD MMMM YYYY');
                value.plan_start = moment( value.plan_start ).format('DD MMMM YYYY');
              });

              scope.toggleOff();
            });
        }
        
        scope.enrollAction = function ( data,index ) {
          scope.global_enrollCustomerId = data.id;
          
          scope.global_enrollmentHistoryData.map((value,key)  => {
            if ( index == key ) {
              value.isActionShow = value.isActionShow == true ? false : true;
            } 
            // else {
            //   value.isActionShow = false;
            // }
          })
        }
        scope.closeAllEnrollAction  = function(){
          if(scope.global_enrollmentHistoryData){
            scope.global_enrollmentHistoryData.map((value,key)  => {
              value.isActionShow = false;
            })
          }
        }

        $("body").click(function(e){
          if ($(e.target).parents(".enrollment-actions-dp-wrapper").length === 0) {
            scope.closeAllEnrollAction();
            // scope.$apply();
          }
        });

        scope._confirmActivationEmail_ = function () {
          let data = {
            id: scope.global_enrollCustomerId
          }

          hrSettings.sendImmediateActivation( data )
            .then(function (response) {

              if ( response.data.status == true ) {
                $('#send_immediately_modal').modal('hide');

                swal('Success!', response.data.message, 'success');
              } else {
                swal('Error!', response.data.message, 'error');
              }
              
            });
        }

        scope.getPlanDetails = function () {
          var data  = {
            page: scope.activePlan_active_page,
          }
          if(scope.selectedOldPlan != null){
            data.oldPlanCustomerPlanID = scope.selectedOldPlan.customer_plan_id;
          }
          scope.toggleLoading();
          hrSettings.getActivePlanDetails(data)
            .success(function (response) {
              scope.activePlanDetails_pagination = response;
              scope.employee_acount_details = response.data.employee_acount_details;
              scope.dependent_acount_details = response.data.dependent_acount_details;

              scope.getEnrollmentHistoryData();
              scope.getInvoiceHistoryData();
            })
        }

        scope.customFormatDate  = function(date, from, to){
          return moment(date , from).format(to);
        }

        scope.getOldPlansList = function () {
          // scope.toggleLoading();
          hrSettings.getOldPlanList()
            .success(function (response) {
              scope.oldPlan_list = response.data;
            })
        }

        // Active Plan pagination and Old Plan list //
          scope._toggleActivePlanDrop_ = function(){
            scope.isActivePlanDropShow = scope.isActivePlanDropShow ? false : true;
          }
          scope._toggleOldActivePlanDrop_ = function(){
            scope.isOldPlanListDropShow = scope.isOldPlanListDropShow ? false : true;
          }
          $("body").click(function(e){
            if ($(e.target).parents(".active-plan-dot").length === 0) {
              scope.isActivePlanDropShow = false;
              scope.$apply();
            }
            if ($(e.target).parents(".old-active-plans-wrapper").length === 0) {
              scope.isOldPlanListDropShow = false;
              scope.$apply();
            }
          });
          scope.selectActivePlanPage  = function(page){
            if(scope.activePlan_active_page != page){
              scope.page_active = 1;
              scope.activePlan_active_page = page;
              scope.isActivePlanDropShow = false;
              scope.getPlanDetails();
            }
          }
          scope.selectOldPlanPage = function(page, plan){
            scope.selectedOldPlan = plan;
            scope.activePlan_active_page = 1;
            scope.isOldPlanListDropShow = false;
            scope.oldPlan_active_page = page;
            scope.getPlanDetails();
          }
          scope._selectCurrentPlan_ = function(){
            scope.activePlan_active_page = 1;
            scope.selectedOldPlan = null;
            scope.oldPlan_active_page = null;
            scope.getPlanDetails();
          }
        // ---------------------------------------- //
        scope._downloadInvoiceHistoryPDF_ = function(){
          window.open(serverUrl.url + `/hr/plan_all_download?token=` + window.localStorage.getItem('token') + `&customer_active_plan_id=` + scope.activePlanDetails_pagination.data.customer_active_plan_id, '_blank' );
        }

        scope.global_planData = {
          start_date: new Date(),
          plan_duration: '12',
          invoice_start_date: new Date(),
          // invoice_date: new Date(),
        }

        scope._editDetails_ = function ( type, planData ) {
          console.log(planData);
          scope.global_planData = planData;
          scope.global_planData.type = type;
          // scope.global_planData.invoice_date = scope.global_planData.plan_start;

          // scope.global_planData.invoice_date = moment(scope.global_planData.invoice_date).add(scope.global_planData.duration, 'months').subtract(1, 'days');

          scope.global_planData.plan_start = moment(scope.global_planData.plan_start, ['YYYY-MM-DD', 'DD/MM/YYYY']).format('DD/MM/YYYY');
          scope.global_planData.invoice_date = moment(scope.global_planData.invoice_date, ['YYYY-MM-DD', 'DD/MM/YYYY']).format('DD/MM/YYYY');
          scope.global_planData.invoice_due = moment(scope.global_planData.invoice_due, ['YYYY-MM-DD', 'DD/MM/YYYY']).format('DD/MM/YYYY');

          setTimeout(() => {
            var dt = new Date();
            // dt.setFullYear(new Date().getFullYear()-18);
            $('.datepicker').datepicker({
              format: 'dd/mm/yyyy',
              endDate: dt
            });

            $('.datepicker').datepicker().on('hide', function (evt) {
              var val = $(this).val();
              if (val != "") {
                $(this).datepicker('setDate', val);
              }
            })
          }, 300); 
        }

        scope._changePlanDuration_ = function ( duration, start) {
          
          let year = moment(start, 'DD/MM/YYYY').year();
          let month = moment(start, 'DD/MM/YYYY').month();
          let day = moment(start, 'DD/MM/YYYY').date();
          let new_invoice_start = start;
          let new_invoice_due = moment([year,month,day]).add(parseInt(duration),'months').subtract(1, 'days').format('DD/MM/YYYY');
          // scope.edit_employee_acount_details.invoice_start = new_invoice_start;
          scope.global_planData.invoice_date = new_invoice_due;
        }

        scope._updatePlanDetails_ = function(formData){
          console.log(formData);
          formData.plan_start = moment(formData.plan_start, ['YYYY-MM-DD', 'DD/MM/YYYY']).format('YYYY-MM-DD');
          let data = {};
          if (formData.account_type == 'enterprise_plan') {
            formData.invoice_date = moment(formData.invoice_date, ['YYYY-MM-DD', 'DD/MM/YYYY']).format('YYYY-MM-DD');
            formData.invoice_due = moment(formData.invoice_due, ['YYYY-MM-DD', 'DD/MM/YYYY']).format('YYYY-MM-DD');
            data = {
              start_date: formData.plan_start,
              plan_duration: formData.duration,
              invoice_start: formData.invoice_date,
              invoice_due: formData.invoice_due,
              individual_price: formData.individual_price
            }
          } else {
            data = {
              start_date: formData.plan_start,
              plan_duration: formData.duration,
            }
          }
          scope.toggleLoading();
          if(formData.type == 'employee'){
            data.customer_active_plan_id = scope.activePlanDetails_pagination.data.customer_active_plan_id;
            hrSettings.updateEmployeePlan(data)
              .then(function(response){
                console.log(response);
                if(response.data.status){
                  $(".modal").modal('hide');
                  scope.getPlanDetails();
                }else{
                  swal('Error!', response.data.message, 'error');
                }
                
              });
          }
          if(formData.type == 'dependent'){
            data.dependent_plan_id = formData.dependent_plan_id;
            hrSettings.updateDependentPlan(data)
              .then(function(response){
                console.log(response);
                if(response.data.status){
                  $(".modal").modal('hide');
                  scope.getPlanDetails();
                }else{
                  swal('Error!', response.data.message, 'error');
                }
              });
          }
        }
        scope._planTypeChanged_ = function(plan_type){
          if(plan_type == 'enterprise_plan'){
            setTimeout(() => {
              var dt = new Date();
              // dt.setFullYear(new Date().getFullYear()-18);
              $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                endDate: dt
              });
  
              $('.datepicker').datepicker().on('hide', function (evt) {
                var val = $(this).val();
                if (val != "") {
                  $(this).datepicker('setDate', val);
                }
              })
            }, 300); 
          }
        }


        // view member modal functions //
        scope.isViewMemberPerPageShow = false;
        scope.mem_per_page = 10;
        scope.mem_active_page = 1;
        scope.selectedEnrollHistoryList = {};
        scope.memberSearch = null;
        scope.isViewMemberModalShow = false;
        scope.viewMemberModalpagesToDisplay = 10;
        scope.memberSearch = null;
        scope.viewMemberList  = [];
        
        scope._getEnrolledMemberList_  = function(list, search){       
          scope.selectedEnrollHistoryList = list;      
          var data  = {
            page: scope.mem_active_page,
            customer_active_plan_id : list.customer_active_plan_id,
            per_page : scope.mem_per_page,
          }
          if(search){
            data.search = search;
          }
          scope.toggleLoading();
          hrSettings.getViewMemberModalList(data)
            .then(function(response){
              scope.toggleLoading();
              console.log(response);
              if (response.data.status) {
                scope.viewMemberList = response.data.data.data;
                scope.viewMemberModalPagination = response.data.data;
                if(!scope.isViewMemberModalShow){
                  $('#viewMemberModal').modal('show');
                  scope.isViewMemberModalShow = true;
                }
              } else {
                swal('Error!', response.data.message, 'error');
              }
            });
        }
        scope.closeViewMemberModal  = function(){
          scope.isViewMemberModalShow = false;
          $('#viewMemberModal').modal('hide');
        }
        scope.showDependentList = function(list){
          if(list.dependents.length > 0){
            list.showDependents = list.showDependents == true ? false : true;
          }
        }
        scope._toggleViewMemberPopUp  = function(){
          scope.isViewMemberPerPageShow = scope.isViewMemberPerPageShow == false ? true : false;
        }
        scope._setViewMembersPageLimit_  = function(page){
          scope.mem_per_page = page;
          scope.mem_active_page = 1;
          scope._getEnrolledMemberList_(scope.selectedEnrollHistoryList, scope.memberSearch);
        }
        scope._viewMembersSetPage_  = function(page){
          scope.mem_active_page = page;
          scope._getEnrolledMemberList_(scope.selectedEnrollHistoryList, scope.memberSearch);
        }
        scope._viewMembersPrevPage_  = function(page){
          if( scope.mem_active_page != 1 ){
            scope.mem_active_page -= 1;
            scope._getEnrolledMemberList_(scope.selectedEnrollHistoryList, scope.memberSearch);
          }
        }
        scope._viewMembersNextPage_  = function(page){
          // pagination.length + 1
          if( scope.mem_active_page != 10 ){
            scope.mem_active_page += 1;
            scope._getEnrolledMemberList_(scope.selectedEnrollHistoryList, scope.memberSearch);
          }
        }
        
        scope.startViewMembersModalIndex = function () {
          if (scope.mem_active_page > ((scope.viewMemberModalpagesToDisplay / 2) + 1)) {
            if ((scope.mem_active_page + Math.floor(scope.viewMemberModalpagesToDisplay / 2)) > scope.viewMemberModalPagination.last_page) {
              return scope.viewMemberModalPagination.last_page - scope.viewMemberModalpagesToDisplay + 1;
            }
            return scope.mem_active_page - Math.floor(scope.viewMemberModalpagesToDisplay / 2);
          }
          return 0;
        }

        $("body").click(function(e){
          if ($(e.target).parents("#viewMemberModal .custom-list-per-page").length === 0) {
            scope.isViewMemberPerPageShow = false;
            scope.$apply();
          }
          if ($(e.target).parents(".enrollment-history-drop-wrapper").length === 0) {
            scope.hideEnrolActionDrops();
          }
        });
        $('#viewMemberModal').on('hidden.bs.modal', function (e) {
          scope.isViewMemberModalShow = false;
          scope.memberSearch = null;
        })

        scope.hideEnrolActionDrops  = function(){
          if(scope.enrollment_history){
            angular.forEach(scope.enrollment_history.data, function(value, key){
              // console.log(value);
              if( value.isEnrolActionsShow == true ){
                value.isEnrolActionsShow = false;
                scope.$apply();
              }
            });
          }
          
        }
        // scope.newScheduleDate = new Date();
        scope._editScheduleDate_ = function ( data ) {
          console.log(data);
          scope.scheduleData = data;
          scope.scheduleData.schedule_date = moment( scope.scheduleData.schedule_date,['YYYY-MM-DD', 'DD/MM/YYYY'] ).format('DD/MM/YYYY');
          document.getElementById('new-scheduled-date').value = '';
        
          setTimeout(() => {
            // var dt = new Date();
            // dt.setFullYear(new Date().getFullYear()-18);
            $('.datepicker').datepicker({
              format: 'dd/mm/yyyy',
              // endDate: dt
            });

            $('.datepicker').datepicker().on('hide', function (evt) {
              var val = $(this).val();
              if (val != "") {
                $(this).datepicker('setDate', val);
              }
            })
          }, 300); 
        }

        scope._changeDate_ = function ( date ) {
          console.log(date);
          scope.new_scheduled_date = date.split("/").reverse().join("-");;
          console.log(scope.new_scheduled_date);
        }

        scope.setScheduleDate = function (  ) {
          let data = {
            id: scope.scheduleData.id,
            schedule_date: scope.new_scheduled_date,
          } 
          scope.toggleLoading();
          hrSettings.updateScheduleDate( data )
            .then(function(response){
              console.log(response);
              if ( response.data.status) {
                // scope.toggleOff();
                $('#edit_scheduled_modal').modal('hide');
                document.getElementById('new-scheduled-date').value = '';
                scope.getEnrollmentHistoryData();
              } else {
                scope.toggleOff();
                swal('Error!', response.data.message, 'error');
              }
              
            });
        }

      // ---------------------------------------- //

      scope.toggleInvoiceHistoryDrop = function ( data,index ) {
        scope.getPlanInvoiceData[index].isShowDrop = scope.getPlanInvoiceData[index].isShowDrop == true ? false : true;
      }
      scope.downloadInvoiceHistoryDataPDF  = function(list){
        window.open(serverUrl.url + '/benefits/invoice?invoice_id=' + list.invoice_id + '&token='+window.localStorage.getItem('token'));
      }
      $("body").click(function(e){
        if ($(e.target).parents(".invoice-history-drop-click").length === 0) {
          console.log(scope.getPlanInvoiceData);
          scope.getPlanInvoiceData.map((value,key)  => {
            value.isShowDrop = false;
          })
          scope.$apply();
        }
      });

      scope.changeInvoiceHistoryType  = function(type){
        scope.page_active = 1;
        if(type == 'plan'){
          scope.getInvoiceHistoryData();
        }
        if(type == 'refund'){
          scope.getRefundInvoiceHistory();
        }
      }

      scope.getRefundInvoiceHistory = function(){
        scope.toggleLoading();
        $http.get(serverUrl.url + `/hr/get_refund_invoices?customer_active_plan_id=${scope.activePlanDetails_pagination.data.customer_active_plan_id}&limit=${scope.per_page}&page=${scope.page_active}`)
          .success(function (response) {
            console.log(response);
            scope.toggleLoading();
            scope.getPlanInvoiceData = response.data;
            scope.invoicePlanPagination = response;
            console.log(scope.getPlanInvoiceData);
            angular.forEach(scope.getPlanInvoiceData, function(value, key) {
              value.cancellation_date = moment( value.cancellation_date ).format('DD MMMM YYYY');
              value.payment_due = moment( value.payment_due ).format('DD MMMM YYYY');
              value.payment_date = value.payment_date ? moment( value.payment_date ).format('DD MMMM YYYY') : null;
              // value.total = value.total.toFixed(2);
            });
            scope.toggleOff();
          })
      }





        scope.onLoad = function(){
          // scope.initializeGeoCode();
          scope.getDownloadToken();
            
          hrSettings.getSession( )
            .then(function(response){
            scope.options.accessibility = response.data.accessibility;
          });
          
          if( $state.current.name == "company-and-contacts" ){
            scope.getCompanyContacts();
          }
          if( $state.current.name == "transactions" ){
            scope.getTransac(scope.plan_transactions_page);
            scope.getPlanInfo();
            scope.getSpendingDeposits(scope.spending_deposit_page);
            scope.getRefundList();
          }
          if( $state.current.name == "account-and-payment" ){
            scope.getSpendingAcctStatus();
            // scope.getCompanyContacts();
            // scope.getBillingList();
            // scope.getPlanSubscriptions();
            scope.getActiveCompPlans();
            scope.companyAccountType();
            // scope.getInvoiceHistoryData();
            scope._getHrDetails_();
            scope.getOldPlansList();
            scope.getPlanDetails();
          }
          
        };

        scope.onLoad();
      }
    };
  }
]);
