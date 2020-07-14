app.directive("companyContactsDirective", [
  "$state",
  "hrSettings",
  "$rootScope",
  "serverUrl",
  function directive($state, hrSettings, $rootScope, serverUrl) {
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

          console.log( scope.global_hrData );
          console.log( scope.global_hrData.phone_code );
  
          if ( scope.global_hrData.phone_code == '65' ) {
            var input = document.querySelector("#phone_number");
            iti1 = intlTelInput(input, settings);

            input.addEventListener("countrychange", function () {
              console.log(iti1.getSelectedCountryData());
              scope.global_hrData.phone_code = iti1.getSelectedCountryData().dialCode;
            })
          } else if ( scope.global_hrData.phone_code == '60' ) {
            var input2 = document.querySelector("#phone_number");
            iti1 = intlTelInput(input2, settings2);

            input2.addEventListener("countrychange", function () {
              console.log(iti1.getSelectedCountryData());
              scope.global_hrData.phone_code = iti1.getSelectedCountryData().dialCode;
            })
          } else if ( scope.global_hrData.phone_code == '63' || scope.global_hrData.phone_code == "" ) {
            console.log('pag blank');
            $('.iti__selected-dial-code').addClass('empty');
            var input3 = document.querySelector("#phone_number");
            iti1 = intlTelInput(input3, settings3);

            input3.addEventListener("countrychange", function () {
              console.log(iti1.getSelectedCountryData());
              scope.global_hrData.phone_code = iti1.getSelectedCountryData().dialCode;
            })
          }
        }

        scope.selectSpendingTab = function(opt){
          scope.selectedSpendingTab = opt;
        }

        scope.companyAccountType = function () {
          scope.account_type = localStorage.getItem('company_account_type');
          console.log(scope.account_type);

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
              // console.log(response);
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
              // console.log(response);
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
            console.log(response);
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
            console.log('scope.transactions.current_total', scope.transactions.current_total)
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
            console.log(response);
            scope.transactions = response.data;
            scope.transactions.current_total = curr_total - temp_total ;
            scope.transactions.temp_total = parseInt(response.data.to);
            console.log('scope.transactions.current_total', scope.transactions.current_total)
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
            console.log(response);
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

            console.log('benefits spending', scope.benefits_spending.data);
            scope.toggleOff();

          });
        };

        scope.getBenefitsSpendingTransacPrev = function(page){
          scope.toggleLoading();
          var curr_total = scope.benefits_spending.current_total != 0 ? scope.benefits_spending.current_total : 0;
          var temp_total = scope.benefits_spending.temp_total != 0 ? scope.benefits_spending.temp_total : 0;
          hrSettings.getBenefitsSpendingTransac( page ).then(function(response) {
            console.log(response);
            scope.benefits_spending = response.data;
            scope.benefits_spending.current_total = curr_total - temp_total ;
            scope.benefits_spending.temp_total = parseInt(response.data.to);
            console.log('scope.transactions.current_total', scope.benefits_spending.current_total)
            setTimeout(function() {
              $(".info-container").fadeIn();
              $(".loader-container").hide();
            }, 200);
            scope.toggleOff();
          });
        };

        scope.downloadSpendingInvoice = function(data) {
          // console.log(data);
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
          // console.log(data);
          if(scope.download_token.live == true) {
            window.open(scope.download_token.download_link + "/spending_receipt_download?id=" + data.statement_id + '&token=' + scope.download_token.token);
          } else {
            window.open(serverUrl.url + '/hr/download_spending_receipt?statement_id=' + data.statement_id + '&token=' + window.localStorage.getItem('token'));
          }
        }

        scope.downloadRefund = function(customer_active_plan_id) {
          window.open(serverUrl.url + '/hr/get_cancellation_details?id=' + customer_active_plan_id + '&token=' + window.localStorage.getItem('token'));
        }

        scope.getRefundList = function() {
          hrSettings.getRefunds().then(function(response) {
            // console.log(response);
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
            // console.log(response);
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
            // console.log(response);
            setTimeout(function() {
              $(".info-container").fadeIn();
              $(".loader-container").hide();
            }, 200);
          });
        };

        scope.getPlanSubscriptions = function(){
          hrSettings.getPlanSubs().then(function(response) {
            scope.plan_subs = response.data;
            console.log(response);
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
            console.log(response);
            console.log(scope.sample);
            scope.comp_active_plans = response.data.data;
            console.log(response.data.data[0].customer_active_plan_id);
            scope.getCustomerPlanId = response.data.data[0].customer_active_plan_id;

            
            angular.forEach( scope.comp_active_plans, function( value, key ){
              value.plan_start = moment( value.plan_start ).format( 'DD MMMM YYYY' );
            });
            
            scope.getEnrollmentHistoryData();
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
          console.log(data);
          window.open(serverUrl.url + '/hr/download_dependent_invoice?dependent_plan_id=' + data.dependent_plan_id + '&token=' + window.localStorage.getItem('token'));
        }

        scope.getPlanInfo = function(){
          hrSettings.getMethodType()
          .then(function(response) {
            console.log(response);
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
							console.log(response);
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
                console.log(response);

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
        scope.getInvoiceHistoryData = function ( page,per_page ) {
          page = scope.page_active;
          per_page = scope.per_page;
          
          scope.toggleLoading();
          hrSettings.getPlanInvoiceHistory( page,per_page )
            .then(function (response) {
              console.log(response);
              scope.getPlanInvoiceData = response.data.data.data;
              scope.invoicePlanPagination = response.data.data;
              console.log(scope.invoicePlanPagination);

              angular.forEach(scope.getPlanInvoiceData, function(value, key) {
                console.log(value);
                value.invoice_date = moment( value.invoice_date ).format('DD MMMM YYYY');
                value.invoice_due = moment( value.invoice_due ).format('DD MMMM YYYY');
                value.payment_date = moment( value.payment_date ).format('DD MMMM YYYY');
                value.total = value.total.toFixed(2);
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
              console.log(response);

              scope.global_hrData = response.data.hr_account_details;
              console.log(scope.global_hrData);
              console.log(scope.global_hrData.phone_code);
            });
        } 

        scope._editDetailsBtn_ = function ( data ) {
          
          scope.initializeGeoCode();
        }

        scope._updateHrDetails_ = function ( data ) {
          console.log(data);
          let params = {
            email: data.email,
            phone_number: data.phone,
            fullname: data.full_name, 
            phone_code: data.phone_code,
          }

          scope.toggleLoading();
          hrSettings.updateHrDetails( params )
            .then(function (response) {
              console.log(response);
              
              if ( response.data.status == true ) {
                $('#edit_details').modal('hide');

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
          customer_active_plan_id = scope.getCustomerPlanId;

          scope.toggleLoading();
          hrSettings.fetchEnrollmentHistoryData( page,per_page,customer_active_plan_id )
            .then(function (response) {
              console.log(response);
              scope.global_enrollmentHistoryData = response.data.data.data;
              scope.global_enrollmentHistoryPagination = response.data.data;

              angular.forEach(scope.global_enrollmentHistoryData, function(value, key) {
                console.log(value);
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
              console.log('true');
              value.isActionShow = value.isActionShow == true ? false : true;
            } else {
              console.log('false');
              value.isActionShow = false;
            }
          })
        }

        scope._confirmActivationEmail_ = function () {
          let data = {
            id: scope.global_enrollCustomerId
          }
          console.log( data );

          hrSettings.sendImmediateActivation( data )
            .then(function (response) {
              console.log(response);

              if ( response.data.status == true ) {
                $('#send_immediately_modal').modal('hide');

                swal('Success!', response.data.message, 'success');
              } else {
                swal('Error!', response.data.message, 'error');
              }
              
            });
        }

        scope.onLoad = function(){
          scope.initializeGeoCode();
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
            scope.getCompanyContacts();
            scope.getBillingList();
            scope.getPlanSubscriptions();
            scope.getActiveCompPlans();
            scope.companyAccountType();
            scope.getInvoiceHistoryData();
            scope._getHrDetails_();
          }
          
        };

        scope.onLoad();
      }
    };
  }
]);
