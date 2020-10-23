var service = angular.module("hrService", []);

service.factory("hrSettings", function($http, serverUrl, Upload) {
  var hrFactory = {};

  hrFactory.getProRatedCalculation = function( data ) {
    return $http.post(serverUrl.url + "/company-benefits-dashboard", data);
  };

  hrFactory.getCompanyBenefitsDashboard = function() {
    return $http.get(serverUrl.url + "/company-benefits-dashboard");
  };

  hrFactory.getSession = function() {
    return $http.get(serverUrl.url + "/get-hr-session");
  };

  hrFactory.updateAgreeStatus = function() {
    return $http.get(serverUrl.url + "/update/agree_status");
  };

  hrFactory.checkTransactionDuplicates = function( id ) {
    return $http.get(serverUrl.url + "/hr/check_duplicate_transaction?id=" + id);
  };

  hrFactory.getMethodType = function() {
    return $http.get(serverUrl.url + "/hr/check_plan");
  };

  hrFactory.getEnrollmentProgress = function() {
    return $http.get(serverUrl.url + "/hr/enrollment_progress");
  };

  hrFactory.getTempEmployees = function() {
    return $http.get(serverUrl.url + "/hr/get/temp_enrollment");
  };

  hrFactory.deleteTempEmployees = function(data) {
    return $http.post(serverUrl.url + "/hr/remove_enrollees", data);
  };

  hrFactory.insertTempEmployee = function(data) {
    return $http.post(serverUrl.url + "/insert/enrollee_web_input", data);
  };

  hrFactory.deleteTempEmployee = function(id) {
    return $http.get(serverUrl.url + "/remove/temp_enrollee/" + id);
  };

  hrFactory.updateTempEmployee = function(data) {
    return $http.post(serverUrl.url + "/update/enrollee_details", data);
  };

  hrFactory.insertFinalEmployee = function(data) {
    return $http.post(serverUrl.url + "/hr/finish/enroll", data);
  };

  hrFactory.newPurchaseInsertFinalEmployee = function(data) {
    return $http.post(
      serverUrl.url + "/hr/save/web_input/new_active_plan",
      data
    );
  };

  hrFactory.getContacts = function() {
    return $http.get(serverUrl.url + "/hr/company_contacts");
  };

  hrFactory.getTransactions = function(page) {
    return $http.get(serverUrl.url + "/hr/transactions?page=" + page);
  };

  hrFactory.getBenefitsSpendingTransac = function(page) {
    return $http.get(
      serverUrl.url + "/hr/benefits_spending_invoice_transactions?page=" + page
    );
  };
  
  hrFactory.getPrePaidSpendingPurchaseTransac = function(page) {
    return $http.get(
      serverUrl.url + "/hr/get_spending_invoice_purchase_lists?page=" + page
    );
  };

  hrFactory.getRefunds = function() {
    return $http.get(serverUrl.url + "/hr/get_refunds");
  };

  hrFactory.getInvoice = function(id) {
    return $http.get(serverUrl.url + "/benefits/invoice/" + id);
  };

  hrFactory.getReceipt = function(id) {
    return $http.get(serverUrl.url + "/benefits/receipt/" + id);
  };

  hrFactory.getBillings = function() {
    return $http.get(serverUrl.url + "/hr/account_billing");
  };

  hrFactory.getPlanSubs = function() {
    return $http.get(serverUrl.url + "/hr/get_company_plan_status");
  };

  hrFactory.getCompActivePlans = function() {
    return $http.get(serverUrl.url + "/hr/company_active_plans");
  };
  
  hrFactory.getSpendingDeposits = function(page) {
    return $http.get(serverUrl.url + "/hr/get_spending_deposits?page=" + page);
  };

  hrFactory.getCompActivePlanDetails = function(id) {
    return $http.get(serverUrl.url + "/hr/active_plan_details/" + id);
  };

  hrFactory.deleteEmployee = function(id) {
    return $http.get(serverUrl.url + "/hr/remove_employee/" + id);
  };

  hrFactory.getEmployees = function(page,limit,status,location,department) {
    var url = serverUrl.url + "/hr/employee/list"+ "?page=" + page +  "&limit=" + limit;
    status.map((res,index) => {
      if(res.active){
        url += ("&status[]=" + res.name.toLowerCase());
      }
    });
    if(location.length > 0){
      url += ("&location_id=[" + location + "]");
    }
    if(department.length > 0){
      url += ("&department_id[" + department + "]");
    }
    return $http.get(url);
  };

  hrFactory.getEmployeeCredits = function(num, page) {
    return $http.get(serverUrl.url + "/hr/all_employee_credits/"+ num + "?page=" + page);
  };

  hrFactory.addEmployeeCredits = function(data) {
    return $http.post(serverUrl.url + "/hr/employee/allocate_credits", data);
  };

  hrFactory.deductEmployeeCredits = function(data) {
    return $http.post(serverUrl.url + "/hr/employee/deduct_credits", data);
  };

  hrFactory.findEmployee = function(data) {
    return $http.post(serverUrl.url + "/hr/search/employee", data);
  };

  hrFactory.updateBusinessInfo = function(data) {
    return $http.post(serverUrl.url + "/hr/update/business_information", data);
  };

  hrFactory.updateBusinessContact = function(data) {
    return $http.post(serverUrl.url + "/hr/update/business_contact", data);
  };

  hrFactory.updateBillingContact = function(data) {
    return $http.post(serverUrl.url + "/hr/update/billing_contact", data);
  };

  hrFactory.updateBillingAddress = function(data) {
    return $http.post(serverUrl.url + "/hr/update/billing_address", data);
  };

  hrFactory.updatePaymentMethod = function(data) {
    return $http.post(serverUrl.url + "/hr/update/payment_method", data);
  };

  hrFactory.getTaskList = function() {
    return $http.get(serverUrl.url + "/hr/task_list");
  };

  hrFactory.deleteEmployee = function(id) {
    return $http.get(serverUrl.url + "/hr/remove_employee/" + id);
  };

  hrFactory.updateEmployee = function(data) {
    return $http.post(serverUrl.url + "/hr/employee/update", data);
  };

  hrFactory.replaceEmployee = function(data) {
    return $http.post(serverUrl.url + "/hr/employee/replace", data);
  };

  hrFactory.getRefundUsers = function(id) {
    return $http.get(serverUrl.url + "/hr/get_runded_lists/" + id);
  };

  hrFactory.getJobTitle = function() {
    return $http.get(window.location.origin + "/care_plan_json/job.json");
  };

  hrFactory.updateShowDone = function() {
    return $http.get(serverUrl.url + "/hr/update_show_done");
  };

  hrFactory.uploadExcel = function(data) {
    return Upload.upload({
      url: serverUrl.url + "/upload/excel_enrollment",
      data: data
    });
  };

  hrFactory.uploadCapExcel = function(data) {
    return Upload.upload({
      url: serverUrl.url + "/hr/upload_employee_cap_per_visit",
      data: data
    });
  };

  hrFactory.newPurchaseUploadExcel = function(data) {
    return Upload.upload({
      url: serverUrl.url + "/hr/new_purchase_active_plan/excel",
      data: data
    });
  };

  hrFactory.getCredits = function(id) {
    return $http.get(serverUrl.url + "/hr/employee/credits/" + id);
  };

  hrFactory.getDashCredits = function() {
    return $http.get(serverUrl.url + "/hr/credits");
  };

  hrFactory.getCheckCredits = function(data) {
    return $http.get(serverUrl.url + "/hr/check_balance?filter="+ data);
  };

  hrFactory.assignCredits = function(data) {
    return $http.post(serverUrl.url + "/hr/employee/assign_credits", data);
  };

  hrFactory.sendPassword = function(pass) {
    return $http.post(serverUrl.url + "/hr/password", pass);
  };

  hrFactory.getPaymentRates = function( ) {
    return $http.get(serverUrl.url + "/hr/calculate_added_headcount");
  };

  hrFactory.payMethod = function(data) {
    return $http.post(
      serverUrl.url + "/hr/save/payment/method/new_active_plan",
      data
    );
  };

  hrFactory.getLocalNetworkPartners = function(id) {
    return $http.get(serverUrl.url + "/list/local_network_partners/" + id);
  };

  hrFactory.getLocalNetworks = function() {
    return $http.get(serverUrl.url + "/list/local_network");
  };

  hrFactory.withDraw = function(data) {
    return $http.post(serverUrl.url + "/hr/employees/withdraw", {
      users: data
    });
  };

  hrFactory.updateHrPassword = function(data) {
    return $http.post(serverUrl.url + "/hr/update_password", data);
  };

  hrFactory.getCancellationDetails = function(id) {
    return $http.get(serverUrl.url + "/hr/get_cancellation_details/" + id);
  };

  hrFactory.getDownloadHeadCountPlan = function(id) {
    return $http.get(serverUrl.url + "/hr/get_head_count_plan/" + id);
  };

  hrFactory.userCompanyCreditsAllocated = function( ) {
    return $http.get(serverUrl.url + "/hr/company_allocation");
  };

  hrFactory.searchCompanyEmployeeCredits = function(data) {
    return $http.post(serverUrl.url + "/hr/search_company_employee_credits", { search: data });
  };

  hrFactory.getEployeeDetails = function( ) {
    return $http.get(serverUrl.url + "/hr/get_company_employee_lists_credits");
  };

  hrFactory.getCompanyDetails = function( ) {
    return $http.get(serverUrl.url + '/hr/details');
  };

  hrFactory.getDownloadToken = function( ) {
    return $http.get(serverUrl.url + '/hr/get_download_token');
  };

  hrFactory.getPlanStatus = function( ) {
    return $http.get(serverUrl.url + '/hr/get_plan_status');
  };

  hrFactory.getIntroMessage = function( ) {
    return $http.get(serverUrl.url + '/hr/get_intro_overview');
  };

  hrFactory.companyPlanTotalDue = function( ) {
    return $http.get(serverUrl.url + '/hr/get_current_plan_total_due');
  };

  hrFactory.companySpendingTotalDue = function( ) {
    return $http.get(serverUrl.url + '/hr/get_current_spending_total_due');
  };

  hrFactory.companyDependents = function( ) {
    return $http.get(serverUrl.url + '/hr/get_dependent_status');
  };

  hrFactory.getCountMembers = function( ) {
    return $http.get(serverUrl.url + '/hr/get_total_members');
  };

  hrFactory.getDependents = function( id ) {
    return $http.get(serverUrl.url + '/hr/get_employee_dependents?employee_id=' + id);
  };

  hrFactory.getRefundStatus = function(id) {
    return $http.get(serverUrl.url + "/hr/get_employee_refund_status_type?employee_id=" + id);
  };

  hrFactory.getEmployeeVacantStatus = function(id) {
    return $http.get(serverUrl.url + "/hr/check_employee_vacant_seat?employee_replacement_seat_id=" + id);
  };

  hrFactory.resetAccount = function(data) {
    return $http.post(serverUrl.url + "/hr/employee_reset_account", data);
  };

  hrFactory.updateCapPerVisit = function(data) {
    return $http.post(serverUrl.url + "/hr/update_employee_cap", data);
  };
  
  hrFactory.getEclaimPresignedUrl = function(data) {
    return $http.get(serverUrl.url + "/hr/get_e_claim_doc?id=" + data);
  };

  hrFactory.getSpendingAccountStatus = function() {
    return $http.get( serverUrl.url + "/hr/get_spending_account_status");
  };
  hrFactory.getPrePostStatus = function() {
    return $http.get( serverUrl.url + "/hr/spending_account_status");
  };

  hrFactory.getEmployeeBulkAllocation = function( per_page, page, type  ) {
    return $http.get( serverUrl.url + "/hr/get_employee_lists_bulk_allocation?per_page="+ per_page +"&page=" + page + "&spending_type=" + type );
  };

  hrFactory.downloadBulkAllocation = function( token  ) {
    return window.open( serverUrl.url + "/hr/download_bulk_allocation_employee_lists?token=" + token );
  };

  hrFactory.updateAllocation = function( data  ) {
    return $http.post( serverUrl.url + "/hr/create_member_credits_allocation", data  );
    // return $http.post( serverUrl.url + "/hr/create_member_new_entitlement", data  );
  };

  hrFactory.uploadAllocation = function(file) {
    return Upload.upload({
      url: serverUrl.url + '/hr/upload_employee_bulk_allocation',
      data: {file: file}
    });
  };

  hrFactory.get_excel_link = function (id) {
    return $http.get( serverUrl.url + "/hr/get_excel_link?customer_id=" + id );
  }

  // Enterprise Plan

  hrFactory.get_member_refund = function (data) {
    return $http.post( serverUrl.url + "/hr/get_member_refund_calculation", data  );
  }
  
  hrFactory.checkReplaceEmpForm = function (data) {
    return $http.post( serverUrl.url + "/hr/check_user_field_replacement", data);
  }
  
  hrFactory.get_member_refund = function (data) {
    return $http.post( serverUrl.url + "/hr/get_member_refund_calculation", data  );
  }

  hrFactory.getPlanInvoiceHistory = function ( page,per_page,id ) {
    return $http.get( serverUrl.url + "/hr/get_plan_invoice_histories?page=" + page + '&per_page=' + per_page + '&customer_active_plan_id=' + id);
  };

  hrFactory.fecthHrDetails = function ( ) {
    return $http.get( serverUrl.url + "/hr/get_hr_details" );
  };

  hrFactory.updateHrDetails = function (data) {
    return $http.post( serverUrl.url + "/hr/update_hr_details", data  );
  };

  hrFactory.fetchEnrollmentHistoryData = function ( page,per_page,id ) {
    return $http.get( serverUrl.url + "/hr/get_plan_enrollment_histories?page=" + page + '&per_page=' + per_page + '&customer_active_plan_id=' + id );
  };

  hrFactory.sendImmediateActivation = function ( data ) {
    return $http.post( serverUrl.url + "/hr/send_immediate_activation", data  );
  }

  hrFactory.employeeResetPassword = function ( id ) {
    return $http.post( serverUrl.url + "/hr/employee_reset_password", id  );
  }

  hrFactory.employeeResetActivation = function ( id ) {
    return $http.post( serverUrl.url + "/hr/resend_activation_email", id  );
  }

  hrFactory.getFilterEmployees = function(page,limit,status_pending,status_activated,status_active,status_removed) {

    let url = serverUrl.url + "/hr/employee/list/"+ "?page=" + page +  "&limit=" + limit;
    if ( status_pending == true ) {
      url += ("&status[]=" + 'pending');
    }
    if ( status_activated == true ) {
      url += ("&status[]=" + 'activated');
    }
    if ( status_active == true ) {
      url += ("&status[]=" + 'active');
    }
    if ( status_removed == true ) {
      url += ("&status[]=" + 'removed');
    }
    return $http.get( url );
  };
  // hrFactory.get_member_refund = function (data) {
  //   return $http.post( serverUrl.url + "/employee/check_email_validation", data  );
  // }
  hrFactory.getEmployeeStatus = function( ) {
    return $http.get( serverUrl.url + "/hr/get_employee_enrollment_status");
  };


  hrFactory.getActivePlanDetails = function ( data ) {
    var url = serverUrl.url + "/hr/get_plan_details?page=" + data.page;
    if(data.oldPlanCustomerPlanID){
      url += ("&type=old&customer_plan_id=" + data.oldPlanCustomerPlanID);
    }else{
      url += "&type=new";
    }
    return $http.get( url );
  }
  hrFactory.getOldPlanList = function ( ) {
    return $http.get( serverUrl.url + "/hr/get_old_list_plans");
  }
  
  hrFactory.updateEmployeePlan = function ( data ) {
    return $http.post( serverUrl.url + "/hr/update_employee_active_plan_details", data);
  }
  hrFactory.updateDependentPlan = function ( data ) {
    return $http.post( serverUrl.url + "/hr/update_dependent_active_plan_details", data);
  }
  hrFactory.getViewMemberModalList = function ( data ) {
    var url = serverUrl.url + "/hr/get_users_by_active_plan?page=" + data.page + "&customer_active_plan_id=" + data.customer_active_plan_id + "&per_page=" + data.per_page;
    if(data.search){
      url += '&search=' + data.search;
    }
    return $http.get( url );
  }
  hrFactory.searchMemberList = function ( page,limit,search ) {
    return $http.get( serverUrl.url + "/hr/employee/list?page=" + page + "&limit=" + limit + "&search=" + search ); 
  }
  hrFactory.updateScheduleDate = function ( data ) {
    return $http.post( serverUrl.url + "/hr/update_enrollment_schedule", data);
  }
  hrFactory.fetchMednefitsCreditsAccountData = function ( start,end ) {
    return $http.get( serverUrl.url + "/hr/get_mednefits_credits_account/?start=" + start + "&end=" + end  ); 
  }
  hrFactory.fetchDateTerms = function (  ) {
    return $http.get( serverUrl.url + "/hr/get_company_date_terms" );
  }

  // Member Wallet
  hrFactory.fetchMemberWallet = function ( start,end,type ) {
    return $http.get( serverUrl.url + "/hr/get_member_wallet_details/?start=" + start + "&end=" + end + "&type=" + type );
  }

  // Mednefits Credits Account Activity Table
  hrFactory.fetchMednefitsActivitiesData = function ( start,end,page,per_page, account_type ) {
    var url = serverUrl.url + "/hr/spending_account_activity/?start=" + start + "&end=" + end + "&page=" + page + "&per_page=" + per_page;
    if(account_type){
      url += '&coverage_type=' + account_type;
    }
    return $http.get(url);
  }

  // Medical and Wellness Wallent Activity Table
  hrFactory.fetchMemberWalletActivitiesData = function ( id,type, page, per_page ) {
    return $http.get( serverUrl.url + "/hr/get_member_allocation_activity/?customer_id=" + id + "&spending_type=" + type + "&page=" + page + "&per_page=" + per_page );
  }

  // Member save wallet
  hrFactory.updateMemberWallet = function ( data ) {
    return $http.post( serverUrl.url + "/hr/update_member_wallet_details", data);
  }

  // Active wellness wallet
  hrFactory.updateWellnessWallet = function ( data ) {
    return $http.post( serverUrl.url + "/hr/activate_wellness_wallet_details", data);
  }

  // confirm payment methods
  hrFactory.updatePaymentMethods = function ( data ) {
    return $http.post( serverUrl.url + "/hr/update_spending_payment_method", data);
  }

  // Benefits Coverage
  hrFactory.fetchBenefitsCoverageData = function ( start,end,type ) {
    return $http.get( serverUrl.url + "/hr/get_benefits_coverage_details/?start=" + start + "&end=" + end + "&type=" + type );
  }

  // confirm top up mednefits credits 
  hrFactory.updateTopUp = function ( data ) {
    return $http.post( serverUrl.url + "/hr/create_top_up_mednefits_credits", data);
  }

  hrFactory.updatePrepaidCredits = function ( data ) {
    return $http.post( serverUrl.url + "/hr/activate_company_mednefits_credits", data);
  }
  
  // Billing 
  hrFactory.fetchCompanyInvoiceHistory = function ( type ) {
    return $http.get( serverUrl.url + "/hr/company_invoice_history/?type=" + type  );
  }

  // Download SOA
  hrFactory.downloadSoaData = function ( type,download ) {
    return window.open( serverUrl.url + "/hr/company_invoice_history/?type=" + type + "&download=" + download + '&token=' + window.localStorage.getItem('token'));
    // return window.open( serverUrl.url + "/hr/download_bulk_allocation_employee_lists?token=" + token );
  }

  // Activate mednefits basic plan
  hrFactory.fetchBasicPlan = function ( ) {
    return $http.post( serverUrl.url + "/hr/activate_mednefits_basic_plan"  );
  }


  hrFactory.fetchBusinessInformation = function () {
    return $http.get( serverUrl.url + "/hr/get_business_information" );
  }

  hrFactory.fetchBusinessContact = function () {
    return $http.get( serverUrl.url + "/hr/get_business_contact" );
  }

  hrFactory.updateBusinessContact = function ( data ) {
    return $http.post( serverUrl.url + "/hr/update_business_contact",data );
  }

  hrFactory.updateBusinessInformation = function ( data ) {
    return $http.post( serverUrl.url + "/hr/update/business_information",data );
  }
  
  hrFactory.fetchCompanyContacts = function () {
    return $http.get( serverUrl.url + "/hr/get_company_contacts" );
  }

  hrFactory.updateMoreBusinessContact = function ( data ) {
    return $http.post( serverUrl.url + "/hr/add_more_business_contact",data );
  }

  hrFactory.fetchLinkAccount = function ( per_page, page, exception ) {
    return $http.get( serverUrl.url + "/hr/get/corporate_linked_account?limit="+per_page+"&page="+page+"&total_enrolled_employee_status=true&total_enrolled_dependent_status=true"+"&except_current="+exception );
  }
  
  hrFactory.fetchPrimaryAdministrator = function ( ) {
    return $http.get( serverUrl.url + "/hr/get_primary_admin_details" );
  }

  hrFactory.fetchLocationData = function ( ) {
    return $http.get( serverUrl.url + "/hr/get_location_list" );
  }

  hrFactory.fetchDepartmentData = function ( ) {
    return $http.get( serverUrl.url + "/hr/get_department_list" );
  }

  hrFactory.fetchDepartmentData = function ( ) {
    return $http.get( serverUrl.url + "/hr/get_department_list" );
  }

  hrFactory.fecthAdditionalAdminDetails = function () {
    return $http.get( serverUrl.url + "/hr/get_additional_admin_details" );
  }

  hrFactory.fetchEmployeeName = function () {
    return $http.get( serverUrl.url + "/hr/employee/list?status[]=active&status[]=pending" );
  }

  hrFactory.updateAdditionalAdmin = function ( data ) {
    return $http.post( serverUrl.url + "/hr/add_employee_admin",data );
  }

  hrFactory.removeAdditionalAdmin = function ( id ) {
    return $http.get( serverUrl.url + "/hr/remove_additional_administrator?admin_id="+id );
  }

  hrFactory.getPermissions = function ( ) {
    return $http.get( serverUrl.url + "/hr/get_account_permissions" );
  }

  hrFactory.updateAddAdministrator = function ( data ) {
    return $http.post( serverUrl.url + "/hr/update_administrator",data );
  }

  hrFactory.fetchEmployeeList = function ( ) {
    return $http.get( serverUrl.url + "/hr/employee_lists" );
  }

  return hrFactory;
});





service.factory("hrActivity", function($http, serverUrl, Upload) {
  var hrFactory = {};

  hrFactory.getDateTerms = function(data) {
    return $http.get(serverUrl.url + "/hr/get_date_terms");
  };

  hrFactory.getHrActivity = function(data,location,department) {
    var url = serverUrl.url + "/hr/get_activity?page=" + data.page + "&start=" + data.start + "&end=" + data.end + "&spending_type=" + data.spending_type + "&filter=" + data.filter;
    if(location.length > 0){
      url += ("&location_id=[" + location + "]");
    }
    if(department.length > 0){
      url += ("&department_id[" + department + "]");
    }
    return $http.get(url);
  };

  hrFactory.getHrActivityInNetworkWithPagination = function(data,location,department) {
    var url = serverUrl.url + "/hr/get_activity_in_network_transactions?page=" + data.page + "&per_page=" + data.per_page + "&start=" + data.start + "&end=" + data.end + "&spending_type=" + data.spending_type + "&customer_id=" + data.customer_id;
    if( data.user_id ){
      url += ("&user_id=" + data.user_id);
    }
    var url = serverUrl.url + "/hr/get_activity?page=" + data.page + "&start=" + data.start + "&end=" + data.end + "&spending_type=" + data.spending_type + "&filter=" + data.filter;
    if(location.length > 0){
      url += ("&location_id=[" + location + "]");
    }
    if(department.length > 0){
      url += ("&department_id[" + department + "]");
    }
    return $http.get( url );
  };
  hrFactory.getHrActivityOutNetworkWithPagination = function(data,location,department) {
    var url = serverUrl.url + "/hr/get_activity_out_network_transactions?page=" + data.page + "&per_page=" + data.per_page + "&start=" + data.start + "&end=" + data.end + "&spending_type=" + data.spending_type + "&customer_id=" + data.customer_id;
    if( data.user_id ){
      url += ("&user_id=" + data.user_id);
    }
    var url = serverUrl.url + "/hr/get_activity?page=" + data.page + "&start=" + data.start + "&end=" + data.end + "&spending_type=" + data.spending_type + "&filter=" + data.filter;
    if(location.length > 0){
      url += ("&location_id=[" + location + "]");
    }
    if(department.length > 0){
      url += ("&department_id[" + department + "]");
    }
    return $http.get( url );
  };

  hrFactory.getEmployeeLists = function() {
    return $http.get(serverUrl.url + "/hr/employee_lists");
  };

  hrFactory.searchEmployeeActivity = function(data) {
    return $http.post(serverUrl.url + "/hr/search_employee_activity", data);
  };

  hrFactory.getEclaimActivity = function(data) {
    return $http.get(serverUrl.url + "/hr/e_claim_activity?page="+data.page+"&start="+data.start+"&end="+data.end + "&spending_type=" + data.spending_type);
  };

  hrFactory.updateEclaimStatus = function(data) {
    return $http.post(serverUrl.url + "/hr/e_claim_update_status", data);
  };

  hrFactory.searchEmployeeEclaimActivity = function(data) {
    return $http.post(
      serverUrl.url + "/hr/search_employee_e_claim_activity",
      data
    );
  };

  hrFactory.searchEmployeeStatement = function(data) {
    return $http.post(serverUrl.url + "/hr/search_employee_statement", data);
  };

  hrFactory.getOverviewStatement = function(data) {
    return $http.post(serverUrl.url + "/hr/get_statement", data);
  };

  hrFactory.getFullStatement = function(data) {
    return $http.post(serverUrl.url + "/hr/get_full_statement", data);
  };

  hrFactory.getTotalAlloc = function(data) {
    return $http.get(serverUrl.url + "/hr/total_credits_allocation?start="+data.start+"&end="+data.end+"&spending_type="+data.spending_type+"&filter="+data.filter);
  };

  hrFactory.downloadStatment = function(id) {
    // return $http.get(serverUrl.url + '/hr/statement_download/' + id);
    return serverUrl.url + "/hr/statement_download/" + id;
  };

  hrFactory.revertEclaim = function( data ) {
    return $http.post(serverUrl.url + "/hr/revert_pending_e_claim", data);
  };

  hrFactory.uploadOutNetworkReceipt = function(data) {
    return Upload.upload({
      url: serverUrl.url + "/hr/upload_e_claim_receipt",
      data: data
    });
  };

  hrFactory.fetchBlockedClinics = function( per, page, opt, search) {
    var url = serverUrl.url + "/hr/get_company_block_lists?per_page=" + per + "&page=" + page + "&region=" + opt;
    if( search != null && search != '' ){
      url += "&search=" + search;
    }
    return $http.get( url );
  };

  hrFactory.fetchOpenedClinics = function( per, page, opt, search ) {
    var url = serverUrl.url + "/hr/get_clinic_lists_block_company?per_page=" + per + "&page=" + page + "&region=" + opt;
    if( search != null && search != '' ){
      url += "&search=" + search;
    }
    return $http.get( url );
  };

  hrFactory.fetchClinicTypes = function( status, region ) {
    return $http.get( serverUrl.url + "/hr/get_block_clinic_type_lists_status?status=" + status + "&region=" + region );
  };

  hrFactory.OpenBlockClinics = function( data ) {
    return $http.post( serverUrl.url + "/hr/create_company_block_lists", data );
  };





  hrFactory.fetchBlockedClinicsEmp = function( per, page, opt, search, id) {
    var url = serverUrl.url + "/hr/get_employee_company_block_lists?per_page=" + per + "&page=" + page + "&region=" + opt + "&user_id=" + id;
    if( search != null && search != '' ){
      url += "&search=" + search;
    }
    return $http.get( url );
  };

  hrFactory.fetchOpenedClinicsEmp = function( per, page, opt, search, id ) {
    var url = serverUrl.url + "/hr/get_employee_clinic_lists_block_company?per_page=" + per + "&page=" + page + "&region=" + opt + "&user_id=" + id;
    if( search != null && search != '' ){
      url += "&search=" + search;
    }
    return $http.get( url );
  };

  hrFactory.fetchClinicTypesEmp = function( status, region, id ) {
    return $http.get( serverUrl.url + "/hr/get_employee_block_clinic_type_lists_status?status=" + status + "&region=" + region + "&user_id=" + id );
  };

  hrFactory.OpenBlockClinicsEmp = function( data ) {
    return $http.post( serverUrl.url + "/hr/create_employee_company_block_lists", data );
  };

  hrFactory.fetchMemberEntitlement = function( id ) {
    return $http.get( serverUrl.url + "/hr/get_member_entitlement?member_id=" + id );
  };

  hrFactory.fetchMemberNewEntitlementStatus = function( id ) {
    return $http.get( serverUrl.url + "/hr/get_member_new_entitlement_status?member_id=" + id );
  };

  hrFactory.openEntitlementCalc = function( id, entitlement_credits, entitlement_date, proration, entitlement_type ) {
    return $http.post( serverUrl.url + "/hr/get_member_entitlement_calculation?member_id=" + id + "&new_entitlement_credits=" + entitlement_credits + "&entitlement_usage_date=" + entitlement_date + "&proration_type=" + proration + "&entitlement_spending_type=" + entitlement_type );
  };

  hrFactory.updateEntitlement = function( data  ) {
    return $http.post( serverUrl.url + "/hr/create_member_credits_allocation", data  );
    // return $http.post( serverUrl.url + "/hr/create_member_new_entitlement", data  );
  };

  hrFactory.memberCredits = function( id  ) {
    return $http.get( serverUrl.url + "/hr/member_credits?member_id=" + id );
  };
  return hrFactory;
});
